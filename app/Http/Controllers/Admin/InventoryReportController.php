<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\InventoryStock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $query = InventoryMovement::with('product')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if ($request->filled('prod_id')) {
            $query->where('prod_id', $request->prod_id);
        }
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        $movements = $query->orderByDesc('created_at')->paginate(15);

        // Snapshot summary per product for period (end quantities)
        $stocks = InventoryStock::with('product')->get();
        $products = Product::orderBy('name')->get(['prod_id','name','sku']);

        // Totals
        $totals = [
            'stock_in' => (clone $query)->where('transaction_type', 'stock_in')->sum('quantity'),
            'stock_out' => (clone $query)->where('transaction_type', 'stock_out')->sum('quantity'),
            'adjustment' => (clone $query)->where('transaction_type', 'adjustment')->sum('quantity'),
        ];

        return view('admin.inventory-reports.index', compact('movements','stocks','products','dateFrom','dateTo','totals'));
    }

    public function show(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        // Daily movement summary
        $daily = InventoryMovement::selectRaw('DATE(created_at) as date, 
                SUM(CASE WHEN transaction_type = "stock_in" THEN quantity ELSE 0 END) as total_in,
                SUM(CASE WHEN transaction_type = "stock_out" THEN quantity ELSE 0 END) as total_out,
                SUM(CASE WHEN transaction_type = "adjustment" THEN quantity ELSE 0 END) as total_adjust')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->groupBy('date')
            ->orderByDesc('date')
            ->get();

        // Top stock outs by product
        $topOut = InventoryMovement::join('products','inventory_movements.prod_id','=','products.prod_id')
            ->where('transaction_type','stock_out')
            ->whereDate('inventory_movements.created_at','>=',$dateFrom)
            ->whereDate('inventory_movements.created_at','<=',$dateTo)
            ->selectRaw('products.name, products.sku, SUM(inventory_movements.quantity) as total_out')
            ->groupBy('products.prod_id','products.name','products.sku')
            ->orderByDesc('total_out')
            ->limit(10)
            ->get();

        return view('admin.inventory-reports.show', compact('daily','topOut','dateFrom','dateTo'));
    }

    public function export(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $movements = InventoryMovement::with('product')
            ->whereDate('created_at','>=',$dateFrom)
            ->whereDate('created_at','<=',$dateTo)
            ->orderBy('created_at','desc')
            ->get();

        // Totals to mirror Sales PDF summary structure
        $totalIn = InventoryMovement::whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->where('transaction_type','stock_in')->sum('quantity');
        $totalOut = InventoryMovement::whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->where('transaction_type','stock_out')->sum('quantity');
        $totalAdjust = InventoryMovement::whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->where('transaction_type','adjustment')->sum('quantity');
        $netChange = ($totalIn ?? 0) - ($totalOut ?? 0) + ($totalAdjust ?? 0);

        $pdf = Pdf::loadView('admin.inventory-reports.pdf', [
            'movements' => $movements,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalIn' => $totalIn,
            'totalOut' => $totalOut,
            'totalAdjust' => $totalAdjust,
            'netChange' => $netChange,
        ])->setPaper('a4','portrait');

        $filename = 'inventory_report_' . $dateFrom . '_to_' . $dateTo . '.pdf';
        return $pdf->download($filename);
    }
}
