<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesHistoryController extends Controller
{
    public function index(Request $request)
    {
        $cashier = Auth::user();
        $query = Sale::with('items.product')
            ->where('cashier_id', $cashier->employee->emp_id ?? null)
            ->where('status', 'completed');

        // Default to today if no date filter is set
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        if (!$dateFrom && !$dateTo) {
            $today = now()->toDateString();
            $query->whereDate('order_date', $today);
        } else {
            if ($dateFrom) {
                $query->whereDate('order_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('order_date', '<=', $dateTo);
            }
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('order_number', 'like', "%$s%")
                  ->orWhere('notes', 'like', "%$s%");
            });
        }

        $sales = $query->orderByDesc('created_at')->paginate(15);
        return view('cashier.sales.index', compact('sales'));
    }
    
    public function show(Sale $sale)
    {
        // Ensure cashier can only view their own sales
        $cashier = Auth::user();
        if ($sale->cashier_id !== ($cashier->employee->emp_id ?? null)) {
            abort(403, 'Unauthorized');
        }
        
        $sale->load('items.product');
        return view('cashier.sales.show', compact('sale'));
    }
}
