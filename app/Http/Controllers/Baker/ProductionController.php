<?php

namespace App\Http\Controllers\Baker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Production;
use App\Models\Product;
use App\Models\InventoryStock;
use App\Models\InventoryMovement;
use App\Models\AuditLog;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $query = Production::with(['product', 'baker'])
            ->where('baker_id', Auth::user()->emp_id);

        // Date filter: if date_from/date_to present, use them; if not, do not filter
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        // Always use only the date part for filtering
        if ($dateFrom) {
            $dateFrom = substr($dateFrom, 0, 10);
        }
        if ($dateTo) {
            $dateTo = substr($dateTo, 0, 10);
        }
        // Log the final trimmed values for verification
        \Log::info('[Baker Production List] FINAL FILTER', [
            'baker_id' => Auth::user()->emp_id,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        if ($dateFrom && $dateTo && $dateFrom === $dateTo) {
            $query->whereDate('production_date', $dateFrom);
        } else {
            if ($dateFrom) {
                $query->whereDate('production_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('production_date', '<=', $dateTo);
            }
        }

        // Debug: Log the actual SQL query and bindings
        \Log::info('[Baker Production List] SQL', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhereHas('product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $productions = $query->orderBy('production_date', 'desc')->paginate(15);
        return view('baker.production.index', compact('productions'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('baker.production.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prod_id' => 'required|exists:products,prod_id',
            'quantity_produced' => 'required|integer|min:1',
            'production_date' => 'required|date',
            'expiration_date' => 'required|date|after:production_date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Generate batch number
            $batchNumber = 'BATCH-' . now()->format('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $production = Production::create([
                'prod_id' => $validated['prod_id'],
                'batch_number' => $batchNumber,
                'quantity_produced' => $validated['quantity_produced'],
                'production_date' => $validated['production_date'],
                'produced_at' => now(),
                'expiration_date' => $validated['expiration_date'],
                'baker_id' => Auth::user()->emp_id,
                'status' => $validated['status'],
                'notes' => $validated['notes'],
            ]);

            // Only update inventory if status is 'completed'
            if ($validated['status'] === 'completed') {
                // Update inventory stock and record movement
                $product = Product::lockForUpdate()->find($validated['prod_id']);
                $inventoryStock = InventoryStock::lockForUpdate()->where('prod_id', $validated['prod_id'])->first();
                
                $previousStock = $inventoryStock ? $inventoryStock->quantity : 0;
                $quantityAdded = $validated['quantity_produced'];
                $newStock = $previousStock + $quantityAdded;
                
                if ($inventoryStock) {
                    $inventoryStock->update([
                        'quantity' => $newStock,
                        'last_counted_at' => now()
                    ]);
                } else {
                    InventoryStock::create([
                        'prod_id' => $validated['prod_id'],
                        'quantity' => $newStock,
                        'reorder_level' => 10, // Default reorder level
                        'last_counted_at' => now(),
                    ]);
                }

                // Record the inventory movement
                InventoryMovement::create([
                    'prod_id' => $validated['prod_id'],
                    'transaction_type' => 'stock_in',
                    'quantity' => $quantityAdded,
                    'previous_stock' => $previousStock,
                    'current_stock' => $newStock,
                    'notes' => "Production batch {$batchNumber} completed by baker",
                ]);
            }

            DB::commit();

            $statusMessage = $validated['status'] === 'completed' 
                ? "Production batch {$batchNumber} created and completed! Added {$validated['quantity_produced']} units to inventory."
                : "Production batch {$batchNumber} created with status: " . ucfirst(str_replace('_', ' ', $validated['status']));

            return redirect()->route('baker.production.index')
                ->with('success', $statusMessage);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create production batch: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Production $production)
    {
        // Ensure baker can only view their own productions
        if ($production->baker_id !== Auth::user()->emp_id) {
            abort(403, 'Unauthorized access to production batch.');
        }

        $production->load(['product', 'baker']);
        return view('baker.production.show', compact('production'));
    }

    public function edit(Production $production)
    {
        // Ensure baker can only edit their own productions
        if ($production->baker_id !== Auth::user()->emp_id) {
            abort(403, 'Unauthorized access to production batch.');
        }

        $products = Product::orderBy('name')->get();
        return view('baker.production.edit', compact('production', 'products'));
    }

    public function update(Request $request, Production $production)
    {
        // Ensure baker can only update their own productions
        if ($production->baker_id !== Auth::user()->emp_id) {
            abort(403, 'Unauthorized access to production batch.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $production->status;
            $newStatus = $validated['status'];
            
            $production->update($validated);

            // Handle status change inventory logic
            if ($oldStatus !== 'completed' && $newStatus === 'completed') {
                // Moving to completed - add to inventory
                $this->updateInventoryForCompletedProduction($production);
            } elseif ($oldStatus === 'completed' && $newStatus !== 'completed') {
                // Moving from completed - remove from inventory
                $this->reverseInventoryForDeletedProduction($production);
            }

            // Log the update
            AuditLog::logAction(
                'update',
                'production_batches',
                $production->batch_id,
                ['status' => $oldStatus],
                ['status' => $newStatus],
                "Production batch {$production->batch_number} status changed from {$oldStatus} to {$newStatus} by baker"
            );

            DB::commit();

            $message = $oldStatus !== $newStatus 
                ? "Production batch {$production->batch_number} status updated from " . ucfirst(str_replace('_', ' ', $oldStatus)) . " to " . ucfirst(str_replace('_', ' ', $newStatus)) . "!"
                : "Production batch {$production->batch_number} updated successfully!";

            return redirect()->route('baker.production.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update production batch: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Production $production)
    {
        // Ensure baker can only delete their own productions
        if ($production->baker_id !== Auth::user()->emp_id) {
            abort(403, 'Unauthorized access to production batch.');
        }

        try {
            DB::beginTransaction();

            // Adjust inventory if needed (only if production was completed)
            if ($production->status === 'completed') {
                $inventoryStock = InventoryStock::lockForUpdate()->where('prod_id', $production->prod_id)->first();
                if ($inventoryStock) {
                    $previousStock = $inventoryStock->quantity;
                    $quantityRemoved = $production->quantity_produced;
                    $newStock = max(0, $previousStock - $quantityRemoved); // Prevent negative stock
                    
                    $inventoryStock->update([
                        'quantity' => $newStock,
                        'last_counted_at' => now()
                    ]);

                    // Record the inventory movement for deletion
                    InventoryMovement::create([
                        'prod_id' => $production->prod_id,
                        'transaction_type' => 'stock_out',
                        'quantity' => $quantityRemoved,
                        'previous_stock' => $previousStock,
                        'current_stock' => $newStock,
                        'notes' => "Production batch {$production->batch_number} deleted by baker",
                    ]);
                }
            }

            $batchNumber = $production->batch_number;
            $production->delete();

            DB::commit();

            return redirect()->route('baker.production.index')
                ->with('success', "Production batch {$batchNumber} deleted successfully!");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete production batch: ' . $e->getMessage()]);
        }
    }

    /**
     * Update inventory when production is completed
     */
    private function updateInventoryForCompletedProduction(Production $production)
    {
        $inventoryStock = InventoryStock::lockForUpdate()->where('prod_id', $production->prod_id)->first();
        
        if (!$inventoryStock) {
            $inventoryStock = InventoryStock::create([
                'prod_id' => $production->prod_id,
                'quantity' => 0,
                'reorder_level' => 10,
                'last_counted_at' => now()
            ]);
        }

        $previousStock = $inventoryStock->quantity;
        $quantityAdded = $production->quantity_produced;
        $newStock = $previousStock + $quantityAdded;

        $inventoryStock->update([
            'quantity' => $newStock,
            'last_counted_at' => now()
        ]);

        // Record the inventory movement
        InventoryMovement::create([
            'prod_id' => $production->prod_id,
            'transaction_type' => 'stock_in',
            'quantity' => $quantityAdded,
            'previous_stock' => $previousStock,
            'current_stock' => $newStock,
            'notes' => "Production batch {$production->batch_number} completed by baker",
        ]);
    }

    /**
     * Reverse inventory when production is cancelled/deleted
     */
    private function reverseInventoryForDeletedProduction(Production $production)
    {
        $inventoryStock = InventoryStock::lockForUpdate()->where('prod_id', $production->prod_id)->first();
        
        if ($inventoryStock) {
            $previousStock = $inventoryStock->quantity;
            $quantityRemoved = $production->quantity_produced;
            $newStock = max(0, $previousStock - $quantityRemoved);

            $inventoryStock->update([
                'quantity' => $newStock,
                'last_counted_at' => now()
            ]);

            // Record the movement
            InventoryMovement::create([
                'prod_id' => $production->prod_id,
                'transaction_type' => 'stock_out',
                'quantity' => $quantityRemoved,
                'previous_stock' => $previousStock,
                'current_stock' => $newStock,
                'notes' => "Production batch {$production->batch_number} status changed from completed by baker",
            ]);
        }
    }
}