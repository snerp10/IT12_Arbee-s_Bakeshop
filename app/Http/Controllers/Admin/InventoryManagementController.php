<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryStock;
use App\Models\InventoryMovement;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InventoryMovement::with(['product' => function($q){ $q->select('prod_id','sku','name'); }]);

        if ($request->filled('product_id')) {
            $query->where('prod_id', $request->product_id);
        }
        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search){
                $q->where('name','like',"%$search%")
                  ->orWhere('sku','like',"%$search%");
            });
        }

        $movements = $query->orderByDesc('created_at')->paginate(20);
    $products = Product::orderBy('name')->get(['prod_id','name','sku']);

        return view('admin.inventory.index', compact('movements','products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    $products = Product::orderBy('name')->get(['prod_id','name','sku']);
        return view('admin.inventory.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,prod_id',
            'movement_kind' => 'required|in:stock_in,stock_out,adjustment',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request) {
            $product = Product::lockForUpdate()->findOrFail($request->product_id);

            // Current snapshot
            $snapshot = InventoryStock::lockForUpdate()->where('prod_id', $product->prod_id)->first();
            $previous = $snapshot ? $snapshot->quantity : 0;
            $qty = (int) $request->quantity;

            if ($request->movement_kind === 'stock_out' && $qty > $previous) {
                throw new \Exception('Cannot stock out more than available.');
            }

            $current = match ($request->movement_kind) {
                'stock_in' => $previous + $qty,
                'stock_out' => $previous - $qty,
                default => $previous + $qty, // adjustment treated as delta (can be negative if needed later)
            };

            // Record movement
            $movement = InventoryMovement::create([
                'prod_id' => $product->prod_id,
                'transaction_type' => $request->movement_kind,
                'quantity' => $qty,
                'previous_stock' => $previous,
                'current_stock' => $current,
                'notes' => $request->notes,
            ]);

            // Update snapshot
            if (!$snapshot) {
                InventoryStock::create([
                    'prod_id' => $product->prod_id,
                    'quantity' => $current,
                    'reorder_level' => 10,
                    'last_counted_at' => now(),
                ]);
            } else {
                $snapshot->update([
                    'quantity' => $current,
                    'last_counted_at' => now(),
                ]);
            }

            AuditLog::logAction('inventory_'.$request->movement_kind, 'inventory_movements', $movement->movement_id, ['previous_stock' => $previous], ['current_stock' => $current], "Inventory {$request->movement_kind} of {$qty} for product {$product->name}");
        });

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory movement recorded.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    $movement = InventoryMovement::with('product')->findOrFail($id);
        return view('admin.inventory.show', compact('movement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    $movement = InventoryMovement::with('product')->findOrFail($id);
        return view('admin.inventory.edit', compact('movement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    $movement = InventoryMovement::findOrFail($id);
        $request->validate(['notes' => 'nullable|string|max:500']);
        $movement->update(['notes' => $request->notes]);
    return redirect()->route('admin.inventory.show', $movement->movement_id)->with('success', 'Movement updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $movement = InventoryMovement::lockForUpdate()->findOrFail($id);
            // Revert snapshot to previous
            $snapshot = InventoryStock::lockForUpdate()->where('prod_id', $movement->prod_id)->first();
            if ($snapshot) {
                $snapshot->update(['quantity' => $movement->previous_stock, 'last_counted_at' => now()]);
            }

            AuditLog::logAction('inventory_delete', 'inventory_movements', $movement->movement_id, $movement->toArray(), null, 'Deleted inventory movement and reverted stock.');
            $movement->delete();
        });

        return redirect()->route('admin.inventory.index')->with('success', 'Movement deleted and stock reverted.');
    }

    /**
     * Low stock alerts view
     */
    public function lowStockAlerts()
    {
        $products = Product::with('inventoryStock')
            ->lowStock()
            ->orderBy('name')
            ->get();
        return view('admin.inventory.low-stock-alerts', compact('products'));
    }

    /**
     * Bulk adjust stocks for multiple products
     */
    public function bulkAdjust(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,prod_id',
            'adjustment_type' => 'required|in:increase,decrease',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $qty = (int) $request->quantity;
            $isDecrease = $request->adjustment_type === 'decrease';

            $products = Product::lockForUpdate()->whereIn('prod_id', $request->product_ids)->get();
            foreach ($products as $product) {
                // Get current inventory stock or create if doesn't exist
                $inventoryStock = InventoryStock::where('prod_id', $product->prod_id)->first();
                $old = $inventoryStock ? $inventoryStock->quantity : 0;
                
                if ($isDecrease && $qty > $old) {
                    throw new \Exception("Cannot decrease {$product->name} below zero.");
                }
                $new = $isDecrease ? $old - $qty : $old + $qty;
                
                if (!$inventoryStock) {
                    InventoryStock::create([
                        'prod_id' => $product->prod_id,
                        'quantity' => $new,
                        'reorder_level' => 10,
                        'last_counted_at' => now(),
                    ]);
                } else {
                    $inventoryStock->update([
                        'quantity' => $new,
                        'last_counted_at' => now(),
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Bulk stock adjustment completed.');
    }
}
