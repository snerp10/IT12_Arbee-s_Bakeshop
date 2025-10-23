<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Production;
use App\Models\InventoryStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsDashboardController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        // KPI metrics
        $salesQuery = Sale::with('items')
            ->whereDate('order_date', '>=', $dateFrom)
            ->whereDate('order_date', '<=', $dateTo)
            ->where('status','completed');
        $totalSales = (clone $salesQuery)->sum('total_amount');
        $totalTransactions = (clone $salesQuery)->count();
        $averageTransaction = $totalTransactions ? $totalSales / $totalTransactions : 0;

        // Sales by day (Chart)
        $salesByDay = (clone $salesQuery)
            ->selectRaw('order_date as date, SUM(total_amount) as total')
            ->groupBy('order_date')
            ->orderBy('date')
            ->get();

        // Sales by hour (Chart) using created_at filtered by order_date
        $salesByHour = Sale::whereBetween(DB::raw('DATE(order_date)'), [$dateFrom, $dateTo])
            ->where('status','completed')
            ->selectRaw('HOUR(created_at) as hour, SUM(total_amount) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Top products (by quantity sold)
        $topProducts = SaleItem::join('sales_orders','order_items.so_id','=','sales_orders.so_id')
            ->join('products','order_items.prod_id','=','products.prod_id')
            ->whereBetween('sales_orders.order_date', [$dateFrom, $dateTo])
            ->where('sales_orders.status','completed')
            ->selectRaw('products.name, products.sku, SUM(order_items.quantity) as qty')
            ->groupBy('products.prod_id','products.name','products.sku')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        // Production status breakdown
        $productionStatuses = Production::whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count','status');

        // Low stock
        $lowStockCount = Product::lowStock()->count();

        // Inventory valuation (approximate using price * on-hand)
        $inventoryValuation = InventoryStock::join('products','inventory_stocks.prod_id','=','products.prod_id')
            ->selectRaw('SUM(inventory_stocks.quantity * products.price) as value')
            ->value('value');

        // Prepare datasets for charts
        $chart = [
            'salesByDay' => [
                'labels' => $salesByDay->pluck('date')->map(fn($d)=>\Illuminate\Support\Carbon::parse($d)->format('M d'))->toArray(),
                'data' => $salesByDay->pluck('total')->map(fn($v)=>round($v,2))->toArray(),
            ],
            'salesByHour' => [
                'labels' => $salesByHour->pluck('hour')->map(fn($h)=>str_pad($h,2,'0',STR_PAD_LEFT).':00')->toArray(),
                'data' => $salesByHour->pluck('total')->map(fn($v)=>round($v,2))->toArray(),
            ],
            'topProducts' => [
                'labels' => $topProducts->pluck('sku')->map(fn($sku,$i)=>$sku.' - '.$topProducts[$i]->name)->toArray(),
                'data' => $topProducts->pluck('qty')->toArray(),
            ],
            'productionStatuses' => [
                'labels' => $productionStatuses->keys()->toArray(),
                'data' => $productionStatuses->values()->toArray(),
            ],
        ];

        return view('admin.analytics.index', compact(
            'dateFrom','dateTo','totalSales','totalTransactions','averageTransaction',
            'lowStockCount','inventoryValuation','chart'
        ));
    }
}
