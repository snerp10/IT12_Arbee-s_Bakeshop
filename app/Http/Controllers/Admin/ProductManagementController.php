<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductManagementController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by stock level (now using inventory_stocks table)
        if ($request->filled('stock_filter')) {
            switch ($request->stock_filter) {
                case 'low_stock':
                    $query->whereHas('inventoryStock', function($q) {
                        $q->whereColumn('quantity', '<=', 'reorder_level');
                    });
                    break;
                case 'out_of_stock':
                    $query->whereHas('inventoryStock', function($q) {
                        $q->where('quantity', 0);
                    });
                    break;
                case 'in_stock':
                    $query->whereHas('inventoryStock', function($q) {
                        $q->where('quantity', '>', 0);
                    });
                    break;
            }
        }

        // Search by name or SKU
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->orderBy('name')->paginate(15);
        $categories = Category::active()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:product_categories,category_id',
            'sku' => 'required|unique:products,sku|max:255',
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|max:50',
            'preparation_time' => 'nullable|integer|min:0',
            'is_available' => 'boolean',
            'shelf_life' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
            'initial_quantity' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::create([
                'category_id' => $request->category_id,
                'sku' => strtoupper($request->sku),
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'unit' => $request->unit,
                'preparation_time' => $request->preparation_time,
                'is_available' => $request->has('is_available'),
                'shelf_life' => $request->shelf_life,
                'status' => $request->status,
            ]);

            // Create initial inventory stock record if needed
            if ($request->filled('initial_quantity') && (int)$request->initial_quantity >= 0) {
                $product->inventoryStocks()->create([
                    'quantity' => (int)$request->initial_quantity,
                    'reorder_level' => (int)($request->reorder_level ?? 10),
                    'last_counted_at' => now(),
                ]);
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load('category', 'inventoryStock');
        
        // Get recent stock movements from InventoryMovement model
        $stockMovements = \App\Models\InventoryMovement::where('prod_id', $product->prod_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.products.show', compact('product', 'stockMovements'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        $product->load('inventoryStock');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:product_categories,category_id',
            'sku' => [
                'required',
                'max:255',
                Rule::unique('products', 'sku')->ignore($product->prod_id, 'prod_id'),
            ],
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|max:50',
            'preparation_time' => 'nullable|integer|min:0',
            'is_available' => 'boolean',
            'shelf_life' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $product->update([
            'category_id' => $request->category_id,
            'sku' => strtoupper($request->sku),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'unit' => $request->unit,
            'preparation_time' => $request->preparation_time,
            'is_available' => $request->has('is_available'),
            'shelf_life' => $request->shelf_life,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        // Check if product has sales records
        if ($product->orderItems()->exists()) {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot delete product that has sales records. You can deactivate it instead.']);
        }

        // Check if product has inventory stock with quantity > 0
        if ($product->inventoryStock()->exists() && $product->inventoryStock->quantity > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot delete product that has inventory stock. Please adjust stock to zero first or deactivate the product instead.']);
        }

        DB::beginTransaction();
        try {
            // Delete inventory stock record if exists (with zero quantity)
            if ($product->inventoryStock()->exists()) {
                $product->inventoryStock()->delete();
            }

            $product->delete();
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Error deleting product: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Adjust product stock manually
     */
    public function adjustStock(Request $request, Product $product)
    {
        $request->validate([
            'adjustment_type' => 'required|in:increase,decrease',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $product) {
            // Get current inventory stock or create if doesn't exist
            $inventoryStock = $product->inventoryStock ?? $product->inventoryStocks()->create([
                'quantity' => 0,
                'reorder_level' => 10,
                'last_counted_at' => now(),
            ]);

            $oldQuantity = $inventoryStock->quantity;
            $adjustmentQuantity = (int) $request->quantity;

            if ($request->adjustment_type === 'decrease') {
                if ($adjustmentQuantity > $oldQuantity) {
                    throw new \Exception('Cannot decrease stock below zero.');
                }
                $newQuantity = $oldQuantity - $adjustmentQuantity;
            } else {
                $newQuantity = $oldQuantity + $adjustmentQuantity;
            }

            // Update inventory stock
            $inventoryStock->update([
                'quantity' => $newQuantity,
                'last_counted_at' => now(),
            ]);

            // Create inventory movement record
            \App\Models\InventoryMovement::create([
                'prod_id' => $product->prod_id,
                'transaction_type' => 'adjustment',
                'quantity' => $adjustmentQuantity,
                'previous_stock' => $oldQuantity,
                'current_stock' => $newQuantity,
                'notes' => "Manual {$request->adjustment_type}: {$request->reason}" . ($request->notes ? " - {$request->notes}" : ''),
            ]);

            AuditLog::logAction(
                'stock_adjustment',
                'inventory_stocks',
                $inventoryStock->inventory_id,
                ['quantity' => $oldQuantity],
                ['quantity' => $newQuantity],
                "Stock adjustment for {$product->name}: {$request->adjustment_type} {$adjustmentQuantity} units. Reason: {$request->reason}"
            );
        });

        return redirect()->back()
            ->with('success', 'Stock adjusted successfully.');
    }

    /**
     * Bulk update products
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,prod_id',
            'action' => 'required|in:activate,deactivate,delete',
        ]);

        DB::transaction(function () use ($request) {
            $products = Product::whereIn('prod_id', $request->product_ids)->get();

            foreach ($products as $product) {
                switch ($request->action) {
                    case 'activate':
                        $product->update(['status' => 'active']);
                        AuditLog::logAction('bulk_activate', 'products', $product->prod_id, null, null, "Bulk activated product: {$product->name}");
                        break;
                    case 'deactivate':
                        $product->update(['status' => 'inactive']);
                        AuditLog::logAction('bulk_deactivate', 'products', $product->prod_id, null, null, "Bulk deactivated product: {$product->name}");
                        break;
                    case 'delete':
                        // Allow deletion if no sales records and (no inventory OR inventory quantity is 0)
                        $hasInventory = $product->inventoryStock()->exists();
                        $hasStock = $hasInventory && $product->inventoryStock->quantity > 0;
                        
                        if (!$product->orderItems()->exists() && !$hasStock) {
                            // Delete inventory record if exists (with zero quantity)
                            if ($hasInventory) {
                                $product->inventoryStock()->delete();
                            }
                            AuditLog::logAction('bulk_delete', 'products', $product->prod_id, $product->toArray(), null, "Bulk deleted product: {$product->name}");
                            $product->delete();
                        }
                        break;
                }
            }
        });

        return redirect()->back()
            ->with('success', 'Bulk operation completed successfully.');
    }

    /**
     * Export products to PDF
     */
    public function export()
    {
        $products = Product::with('category')->orderBy('name')->get();

        $pdf = Pdf::loadView('admin.products.pdf', [
            'products' => $products,
        ])->setPaper('a4', 'portrait');

        $filename = 'products_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        AuditLog::logAction('export', 'products', null, null, null, "Exported products to PDF: {$filename}");

        return $pdf->download($filename);
    }
}
