<?php

namespace App\Http\Controllers\Baker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryStock;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['inventoryStock']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Stock level filter
        if ($request->filled('stock_level')) {
            switch ($request->stock_level) {
                case 'low':
                    $query->whereHas('inventoryStock', function($q) {
                        $q->whereRaw('quantity <= reorder_level');
                    });
                    break;
                case 'zero':
                    $query->whereHas('inventoryStock', function($q) {
                        $q->where('quantity', 0);
                    });
                    break;
                case 'normal':
                    $query->whereHas('inventoryStock', function($q) {
                        $q->whereRaw('quantity > reorder_level');
                    });
                    break;
            }
        }

        $products = $query->orderBy('name')->paginate(15);

        // Get categories for filter
        $categories = \App\Models\Category::orderBy('name')->get();

        // Get summary statistics
        $totalProducts = Product::count();
        $lowStockCount = Product::whereHas('inventoryStock', function($q) {
            $q->whereRaw('quantity <= reorder_level');
        })->count();
        $outOfStockCount = Product::whereHas('inventoryStock', function($q) {
            $q->where('quantity', 0);
        })->count();

        return view('baker.inventory.index', compact(
            'products', 
            'categories', 
            'totalProducts', 
            'lowStockCount', 
            'outOfStockCount'
        ));
    }

    public function show(Product $product)
    {
        $product->load(['inventoryStock', 'category']);
        
        // Get recent production history for this product
        $recentProductions = \App\Models\Production::with('baker')
            ->where('prod_id', $product->prod_id)
            ->orderBy('production_date', 'desc')
            ->take(10)
            ->get();

        return view('baker.inventory.show', compact('product', 'recentProductions'));
    }
}