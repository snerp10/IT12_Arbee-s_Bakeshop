<?php

namespace App\Http\Controllers\Baker;

use Carbon\Carbon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Production;
use App\Models\Product;
use App\Models\InventoryStock;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Check if profile is complete
        if (!$user->employee || $user->employee->status === 'inactive') {
            return view('baker.dashboard.index');
        }

        // Get baker dashboard data
        $bakerId = $user->emp_id;

        // Use Carbon for exact date range matching
        $today = Carbon::today()->toDateString();
        $todaysProduction = Production::where('baker_id', $bakerId)
            ->whereDate('production_date', $today)
            ->count();

        $weekStart = Carbon::now()->startOfWeek()->toDateString();
        $weekEnd = Carbon::now()->endOfWeek()->toDateString();
        $weeklyProduction = Production::where('baker_id', $bakerId)
            ->whereDate('production_date', '>=', $weekStart)
            ->whereDate('production_date', '<=', $weekEnd)
            ->count();

        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $monthEnd = Carbon::now()->endOfMonth()->toDateString();
        $monthlyProduction = Production::where('baker_id', $bakerId)
            ->whereDate('production_date', '>=', $monthStart)
            ->whereDate('production_date', '<=', $monthEnd)
            ->count();

        // Debug: Log KPI query parameters
        \Log::info('[Baker Dashboard KPI] Params', [
            'baker_id' => $bakerId,
            'today' => $today,
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'month_start' => $monthStart,
            'month_end' => $monthEnd,
        ]);

        // Debug: Log KPI results
        \Log::info('[Baker Dashboard KPI] Results', [
            'productions_today' => $todaysProduction,
            'productions_this_week' => $weeklyProduction,
            'productions_this_month' => $monthlyProduction,
        ]);

        // Recent production batches
        $recentBatches = Production::with(['product'])
            ->where('baker_id', $bakerId)
            ->orderBy('production_date', 'desc')
            ->take(5)
            ->get();

        // Low stock alerts (products with stock below 10)
        $lowStockProducts = Product::whereHas('inventoryStock', function($query) {
            $query->where('quantity', '<', 10);
        })->with('inventoryStock')->take(5)->get();

        // Pending production tasks (can be implemented later)
        $pendingTasks = [];

        return view('baker.dashboard.index', compact(
            'todaysProduction',
            'weeklyProduction', 
            'monthlyProduction',
            'recentBatches',
            'lowStockProducts',
            'pendingTasks',
            // For KPI card links
            'today',
            'weekStart',
            'weekEnd',
            'monthStart',
            'monthEnd'
        ));
    }

    public function completeProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);
        
        $user = Auth::user();
        if ($user->employee) {
            $user->employee->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'middle_name' => $validated['middle_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'status' => 'active', // Mark as active when profile is complete
            ]);
        }
        
        return redirect()->route('baker.dashboard')->with('success', 'Profile completed successfully!');
    }
}