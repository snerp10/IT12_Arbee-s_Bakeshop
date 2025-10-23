<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashierDashboardController extends Controller
{
    public function index()
    {
        $cashier = Auth::user();
        $today = now()->toDateString();
        
        // Today's stats for this cashier
        $todaysSales = Sale::where('cashier_id', $cashier->employee->emp_id ?? null)
            ->whereDate('order_date', $today)
            ->where('status', 'completed')
            ->sum('total_amount');
            
        $todaysTransactions = Sale::where('cashier_id', $cashier->employee->emp_id ?? null)
            ->whereDate('order_date', $today)
            ->where('status', 'completed')
            ->count();
            
        $averageSale = $todaysTransactions > 0 ? $todaysSales / $todaysTransactions : 0;
        
        // Recent sales
        $recentSales = Sale::with('items.product')
            ->where('cashier_id', $cashier->employee->emp_id ?? null)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        
        return view('cashier.dashboard.index', compact(
            'todaysSales',
            'todaysTransactions',
            'averageSale',
            'recentSales'
        ));
    }
}
