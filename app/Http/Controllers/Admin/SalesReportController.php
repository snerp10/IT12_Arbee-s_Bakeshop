<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['cashier', 'items.product']);

        // Date range filters
        $dateFrom = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');
        
      $query->whereDate('order_date', '>=', $dateFrom)
          ->whereDate('order_date', '<=', $dateTo);

        // Additional filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('cashier_id')) {
            $query->where('cashier_id', $request->cashier_id);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                                $q->where('order_number', 'like', "%$s%")
                  ->orWhere('notes', 'like', "%$s%");
            });
        }

        // Calculate summary statistics before pagination
        $statsQuery = clone $query;
        $totalSales = $statsQuery->sum('total_amount');
        $totalTransactions = $statsQuery->count();
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

    $sales = $query->orderByDesc('order_date')->paginate(15);
    $cashiers = Employee::orderBy('last_name')->get(['emp_id', 'first_name', 'last_name']);

        return view('admin.sales-reports.index', compact(
            'sales', 'cashiers', 'dateFrom', 'dateTo', 
            'totalSales', 'totalTransactions', 'averageTransaction'
        ));
    }

    public function show(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');

        // Daily sales summary
        $dailySales = Sale::selectRaw('DATE(order_date) as date, COUNT(*) as transactions, SUM(total_amount) as total')
            ->whereDate('order_date', '>=', $dateFrom)
            ->whereDate('order_date', '<=', $dateTo)
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Top products
        $topProducts = DB::table('order_items')
            ->join('sales_orders', 'order_items.so_id', '=', 'sales_orders.so_id')
            ->join('products', 'order_items.prod_id', '=', 'products.prod_id')
            ->whereDate('sales_orders.order_date', '>=', $dateFrom)
            ->whereDate('sales_orders.order_date', '<=', $dateTo)
            ->where('sales_orders.status', 'completed')
            ->selectRaw('products.name, products.sku, SUM(order_items.quantity) as total_qty, SUM(order_items.total_price) as total_revenue')
            ->groupBy('products.prod_id', 'products.name', 'products.sku')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Cashier performance
        $cashierPerformance = Sale::with('cashier')
            ->selectRaw('cashier_id, COUNT(*) as transactions, SUM(total_amount) as total_sales')
            ->whereDate('order_date', '>=', $dateFrom)
            ->whereDate('order_date', '<=', $dateTo)
            ->where('status', 'completed')
            ->groupBy('cashier_id')
            ->orderByDesc('total_sales')
            ->get();

        // Hourly sales pattern
        $hourlySales = Sale::selectRaw('HOUR(created_at) as hour, COUNT(*) as transactions, SUM(total_amount) as total')
            ->whereDate('order_date', '>=', $dateFrom)
            ->whereDate('order_date', '<=', $dateTo)
            ->where('status', 'completed')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return view('admin.sales-reports.show', compact(
            'dailySales', 'topProducts', 'cashierPerformance', 'hourlySales', 
            'dateFrom', 'dateTo'
        ));
    }

    public function export(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');

        $sales = Sale::with(['cashier', 'items.product'])
            ->whereDate('order_date', '>=', $dateFrom)
            ->whereDate('order_date', '<=', $dateTo)
            ->orderBy('order_date', 'desc')
            ->get();

        // Summary values
        $totalSales = $sales->sum('total_amount');
        $totalTransactions = $sales->count();
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Generate PDF from blade view
        $pdf = Pdf::loadView('admin.sales-reports.pdf', [
            'sales' => $sales,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalSales' => $totalSales,
            'totalTransactions' => $totalTransactions,
            'averageTransaction' => $averageTransaction,
        ])->setPaper('a4', 'portrait');

        $filename = 'sales_report_' . $dateFrom . '_to_' . $dateTo . '.pdf';
        return $pdf->download($filename);
    }
}