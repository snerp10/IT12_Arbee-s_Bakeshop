<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Check if AJAX request for dynamic period data
        if (request()->ajax() || request()->has('ajax')) {
            $period = (int) request()->get('period', 7);
            $salesData = $this->getSalesByPeriod($period);
            $categoryData = $this->getSalesByCategoryByPeriod($period);
            
            return response()->json([
                'salesData' => $salesData,
                'categoryData' => $categoryData,
            ]);
        }

        // Dashboard statistics with safe data retrieval
        $stats = [
            // User & Employee Management
            'total_users' => $this->safeCount(User::class),
            'active_users' => $this->safeCount(User::class, ['status' => 'active']),
            'total_employees' => $this->safeCount(Employee::class),
            'active_employees' => $this->safeCount(Employee::class, ['status' => 'active']),
            
            // Product & Inventory Management
            'total_products' => $this->safeCount(Product::class),
            'active_products' => $this->safeCount(Product::class, ['status' => 'active']),
            'low_stock_products' => $this->getLowStockCount(),
            'total_categories' => $this->safeCount(Category::class),
            
            // Sales & Financial
            'today_sales' => $this->getTodaySales(),
            'today_transactions' => $this->getTodayTransactions(),
            'this_month_sales' => $this->getMonthSales(),
            
            // Purchase Management
            'pending_purchases' => $this->safeCount(Purchase::class, ['status' => 'pending']),
            
            // Production
            'production_batches' => $this->getProductionBatches(),
            'production_items' => $this->getProductionItems(),
        ];

        // Get supporting data for widgets
        $recentActivities = $this->getRecentActivities();
        $lowStockProducts = $this->getLowStockProducts();
        $productionBatches = $this->getTodayProduction();
        $last7DaysSales = $this->getLast7DaysSales();
        $topProducts = $this->getTopProductsLast7Days();
        $salesByCategory = $this->getSalesByCategoryLast7Days();
        $inventoryStats = $this->getInventoryStats();

        return view('admin.dashboard.index', compact(
            'stats', 
            'recentActivities', 
            'lowStockProducts', 
            'productionBatches',
            'last7DaysSales', 
            'topProducts',
            'salesByCategory',
            'inventoryStats'
        ));
    }

    /**
     * Lightweight API: System overview snapshot for async widgets
     */
    public function getSystemOverview()
    {
        $data = [
            'today_sales' => $this->getTodaySales(),
            'today_transactions' => $this->getTodayTransactions(),
            'this_month_sales' => $this->getMonthSales(),
            'low_stock_products' => $this->getLowStockCount(),
            'pending_purchases' => $this->safeCount(\App\Models\Purchase::class, ['status' => 'pending']),
        ];
        return response()->json($data);
    }

    /**
     * Safely count records with error handling
     */
    private function safeCount($model, $conditions = [])
    {
        try {
            $query = $model::query();
            foreach ($conditions as $field => $value) {
                $query->where($field, $value);
            }
            return $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get low stock product count
     */
    private function getLowStockCount()
    {
        try {
            return Product::query()
                ->join('inventory_stocks as i', 'i.prod_id', '=', 'products.prod_id')
                ->where('products.status', 'active')
                ->whereColumn('i.quantity', '<=', 'i.reorder_level')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get today's sales total
     */
    private function getTodaySales()
    {
        try {
            return Sale::where('status', 'completed')
                ->whereRaw('DATE(COALESCE(order_date, created_at)) = ?', [Carbon::today()->toDateString()])
                ->sum('total_amount') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get today's transaction count
     */
    private function getTodayTransactions()
    {
        try {
            return Sale::where('status', 'completed')
                ->whereRaw('DATE(COALESCE(order_date, created_at)) = ?', [Carbon::today()->toDateString()])
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get this month's sales
     */
    private function getMonthSales()
    {
        try {
            return Sale::where('status', 'completed')
                ->whereRaw('MONTH(COALESCE(order_date, created_at)) = ? AND YEAR(COALESCE(order_date, created_at)) = ?', [
                    Carbon::now()->month, Carbon::now()->year
                ])
                ->sum('total_amount') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get last 7 days sales summary (date, transactions, total)
     */
    private function getLast7DaysSales()
    {
        try {
            $to = Carbon::today();
            $from = $to->copy()->subDays(6);

            // Aggregate by coalesced sale date to support explicit sale_date
            $rows = Sale::selectRaw('DATE(COALESCE(order_date, created_at)) as date, COUNT(*) as transactions, SUM(total_amount) as total')
                ->where('status', 'completed')
                ->whereRaw('DATE(COALESCE(order_date, created_at)) >= ? AND DATE(COALESCE(order_date, created_at)) <= ?', [
                    $from->toDateString(), $to->toDateString()
                ])
                ->groupBy(DB::raw('DATE(COALESCE(order_date, created_at))'))
                ->orderBy('date', 'asc')
                ->get()
                ->keyBy('date');

            // Fill missing dates with zeros for a full 7-day window
            $series = collect();
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                $key = $d->toDateString();
                if (isset($rows[$key])) {
                    $series->push($rows[$key]);
                } else {
                    $series->push((object) [
                        'date' => $key,
                        'transactions' => 0,
                        'total' => 0,
                    ]);
                }
            }

            return $series;
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get top products by revenue in the last 7 days
     */
    private function getTopProductsLast7Days()
    {
        try {
            $to = Carbon::today();
            $from = $to->copy()->subDays(6);
            return SaleItem::query()
                ->join('sales_orders as s', 's.so_id', '=', 'order_items.so_id')
                ->join('products as p', 'p.prod_id', '=', 'order_items.prod_id')
                ->where('s.status', 'completed')
                ->whereRaw('DATE(COALESCE(s.order_date, s.created_at)) >= ? AND DATE(COALESCE(s.order_date, s.created_at)) <= ?', [
                    $from->toDateString(), $to->toDateString()
                ])
                ->groupBy('p.prod_id', 'p.name', 'p.sku')
                ->orderByDesc(DB::raw('SUM(order_items.total_price)'))
                ->limit(5)
                ->get([
                    'p.prod_id as prod_id',
                    'p.name',
                    'p.sku',
                    DB::raw('SUM(order_items.quantity) as qty_sold'),
                    DB::raw('SUM(order_items.total_price) as revenue'),
                ]);
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get production batches count for today
     */
    private function getProductionBatches()
    {
        try {
            if (class_exists(\App\Models\Production::class)) {
                return \App\Models\Production::whereDate('created_at', Carbon::today())->count();
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get production items count for today  
     */
    private function getProductionItems()
    {
        try {
            if (class_exists(\App\Models\Production::class)) {
                return \App\Models\Production::whereDate('created_at', Carbon::today())
                    ->sum('quantity_produced') ?? 0;
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get recent audit activities
     */
    private function getRecentActivities()
    {
        try {
            return AuditLog::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get low stock products details
     */
    private function getLowStockProducts()
    {
        try {
            return Product::query()
                ->join('inventory_stocks as i', 'i.prod_id', '=', 'products.prod_id')
                ->where('products.status', 'active')
                ->whereColumn('i.quantity', '<=', 'i.reorder_level')
                ->orderBy('i.quantity', 'asc')
                ->limit(10)
                ->get([
                    'products.prod_id',
                    'products.name',
                    'products.sku',
                    'i.quantity as stock_quantity',
                    'i.reorder_level as minimum_stock',
                ]);
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get today's production batches
     */
    private function getTodayProduction()
    {
        try {
            if (class_exists(\App\Models\Production::class)) {
                return \App\Models\Production::with('product')
                    ->whereDate('created_at', Carbon::today())
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            }
            return collect();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get sales by category for the last 7 days
     */
    private function getSalesByCategoryLast7Days()
    {
        try {
            $to = Carbon::today();
            $from = $to->copy()->subDays(6);

            return SaleItem::query()
                ->join('sales_orders as s', 's.so_id', '=', 'order_items.so_id')
                ->join('products as p', 'p.prod_id', '=', 'order_items.prod_id')
                ->join('categories as c', 'c.category_id', '=', 'p.category_id')
                ->where('s.status', 'completed')
                ->whereRaw('DATE(COALESCE(s.order_date, s.created_at)) >= ? AND DATE(COALESCE(s.order_date, s.created_at)) <= ?', [
                    $from->toDateString(), $to->toDateString()
                ])
                ->groupBy('c.category_id', 'c.name')
                ->orderByDesc(DB::raw('SUM(order_items.total_price)'))
                ->get([
                    'c.category_id',
                    'c.name as category_name',
                    DB::raw('SUM(order_items.quantity) as qty_sold'),
                    DB::raw('SUM(order_items.total_price) as revenue'),
                ]);
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get sales data for a specific period
     */
    private function getSalesByPeriod($days = 7)
    {
        try {
            $to = Carbon::today();
            $from = $to->copy()->subDays($days - 1);

            $rows = Sale::selectRaw('DATE(COALESCE(order_date, created_at)) as date, COUNT(*) as transactions, SUM(total_amount) as total')
                ->where('status', 'completed')
                ->whereRaw('DATE(COALESCE(order_date, created_at)) >= ? AND DATE(COALESCE(order_date, created_at)) <= ?', [
                    $from->toDateString(), $to->toDateString()
                ])
                ->groupBy(DB::raw('DATE(COALESCE(order_date, created_at))'))
                ->orderBy('date', 'asc')
                ->get()
                ->keyBy('date');

            // Fill missing dates with zeros
            $series = collect();
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                $key = $d->toDateString();
                if (isset($rows[$key])) {
                    $series->push($rows[$key]);
                } else {
                    $series->push((object) [
                        'date' => $key,
                        'transactions' => 0,
                        'total' => 0,
                    ]);
                }
            }

            return $series;
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get sales by category for a specific period
     */
    private function getSalesByCategoryByPeriod($days = 7)
    {
        try {
            $to = Carbon::today();
            $from = $to->copy()->subDays($days - 1);

            return SaleItem::query()
                ->join('sales_orders as s', 's.so_id', '=', 'order_items.so_id')
                ->join('products as p', 'p.prod_id', '=', 'order_items.prod_id')
                ->join('categories as c', 'c.category_id', '=', 'p.category_id')
                ->where('s.status', 'completed')
                ->whereRaw('DATE(COALESCE(s.order_date, s.created_at)) >= ? AND DATE(COALESCE(s.order_date, s.created_at)) <= ?', [
                    $from->toDateString(), $to->toDateString()
                ])
                ->groupBy('c.category_id', 'c.name')
                ->orderByDesc(DB::raw('SUM(order_items.total_price)'))
                ->get([
                    'c.category_id',
                    'c.name as category_name',
                    DB::raw('SUM(order_items.quantity) as qty_sold'),
                    DB::raw('SUM(order_items.total_price) as revenue'),
                ]);
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Inventory snapshot: SKU count and total valuation (qty * price)
     */
    private function getInventoryStats(): array
    {
        try {
            $skuCount = Product::count();
            $valuation = DB::table('inventory_stocks as i')
                ->join('products as p', 'p.prod_id', '=', 'i.prod_id')
                ->selectRaw('COALESCE(SUM(i.quantity * p.price), 0) as total')
                ->value('total');
            return [
                'sku_count' => $skuCount,
                'valuation' => $valuation ?? 0,
            ];
        } catch (\Exception $e) {
            return [
                'sku_count' => 0,
                'valuation' => 0,
            ];
        }
    }
}
