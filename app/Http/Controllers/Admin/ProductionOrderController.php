<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\InventoryMovement;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductionOrderController extends Controller
{
    public function index(Request $request)
    {
    $query = Production::with(['product','baker']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('prod_id')) {
            $query->where('prod_id', $request->prod_id);
        }
        if ($request->filled('baker_id')) {
            $query->where('baker_id', $request->baker_id);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s){
                $q->where('batch_number', 'like', "%$s%")
                  ->orWhere('notes','like', "%$s%");
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('production_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('production_date', '<=', $request->date_to);
        }

        $productions = $query->orderByDesc('production_date')->paginate(15);
        $products = Product::orderBy('name')->get(['prod_id','name','sku']);
        $employees = Employee::orderBy('last_name')->get(['emp_id','first_name','last_name']);

        return view('admin.productions.index', compact('productions','products','employees'));
    }

    public function create()
    {
    $products = Product::orderBy('name')->get();
        $employees = Employee::orderBy('last_name')->get();
        $nextBatchNumber = $this->generateNextBatchNumber();

        return view('admin.productions.create', compact('products','employees','nextBatchNumber'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prod_id' => 'required|exists:products,prod_id',
            'batch_number' => 'required|string|max:50|unique:production_batches,batch_number',
            'quantity_produced' => 'required|integer|min:1',
            'production_date' => 'required|date|after_or_equal:today',
            'expiration_date' => 'nullable|date|after_or_equal:production_date',
            'baker_id' => 'required|exists:employees,emp_id',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        DB::transaction(function() use ($request) {
            // Ensure we use a fresh batch number to prevent conflicts
            $batchNumber = $this->generateNextBatchNumber();
            
            $productionData = $request->only([
                'prod_id','quantity_produced','production_date','expiration_date','baker_id','notes','status'
            ]);
            $productionData['batch_number'] = $batchNumber;
            // Auto-calc expiration if not provided
            if (empty($productionData['expiration_date'])) {
                $product = Product::find($productionData['prod_id']);
                if ($product && $product->shelf_life && $productionData['production_date']) {
                    $productionData['expiration_date'] = \Carbon\Carbon::parse($productionData['production_date'])->addDays((int)$product->shelf_life)->toDateString();
                }
            }
            
            $production = Production::create($productionData);
            
            if ($production->status === 'completed') {
                $this->applyComplete($production);
            }
        });

        return redirect()->route('admin.productions.index')->with('success', 'Production order created successfully.');
    }

    public function show(Production $production)
    {
        $production->load(['product','baker']);
        return view('admin.productions.show', compact('production'));
    }

    public function edit(Production $production)
    {
    $products = Product::orderBy('name')->get();
    $employees = Employee::orderBy('last_name')->get();

        return view('admin.productions.edit', compact('production','products','employees'));
    }

    public function update(Request $request, Production $production)
    {
        $validator = Validator::make($request->all(), [
            'prod_id' => 'required|exists:products,prod_id',
            'batch_number' => 'required|string|max:50|unique:production_batches,batch_number,' . $production->batch_id . ',batch_id',
            'quantity_produced' => 'required|integer|min:1',
            'production_date' => 'required|date|after_or_equal:today',
            'expiration_date' => 'nullable|date|after_or_equal:production_date',
            'baker_id' => 'required|exists:employees,emp_id',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function() use ($request, $production) {
            $origStatus = $production->status;
            $production->update($request->only([
                'prod_id','batch_number','quantity_produced','production_date','expiration_date','baker_id','notes','status'
            ]));
            if ($origStatus !== 'completed' && $production->status === 'completed') {
                $this->applyComplete($production);
            }
        });
        return redirect()->route('admin.productions.index')->with('success', 'Production order updated successfully.');
    }

    public function destroy(Production $production)
    {
    $production->delete();

    return redirect()->route('admin.productions.index')->with('success', 'Production order deleted successfully.');
    }

    public function start(Production $production)
    {
        if ($production->status !== 'pending') {
            return back()->with('error', 'Only pending productions can be started.');
        }
        $production->update(['status' => 'in_progress']);
        return back()->with('success', 'Production batch set to in progress.');
    }

    public function complete(Production $production)
    {
        if ($production->status === 'completed') {
            return back()->with('info', 'Production already marked completed.');
        }
        DB::transaction(function() use ($production) {
            $production->update(['status' => 'completed']);
            $this->applyComplete($production);
        });
        return back()->with('success', 'Production completed and inventory updated.');
    }

    public function cancel(Production $production)
    {
        if ($production->status === 'completed') {
            return back()->with('error', 'Cannot cancel a completed production.');
        }
        $production->update(['status' => 'cancelled']);
        return back()->with('success', 'Production order cancelled.');
    }

    private function applyComplete(Production $production): void
    {
        $product = Product::lockForUpdate()->findOrFail($production->prod_id);
        // Get current snapshot
        $snapshot = InventoryStock::lockForUpdate()->where('prod_id', $product->prod_id)->first();
        $prev = $snapshot ? $snapshot->quantity : 0;
        $add = (int) $production->quantity_produced;
        $curr = $prev + $add;

        // Update snapshot
        if ($snapshot) {
            $snapshot->update(['quantity' => $curr, 'last_counted_at' => now()]);
        } else {
            InventoryStock::create([
                'prod_id' => $product->prod_id,
                'quantity' => $curr,
                'reorder_level' => 0,
                'last_counted_at' => now(),
            ]);
        }

        // Record movement
        InventoryMovement::create([
            'prod_id' => $product->prod_id,
            'transaction_type' => 'stock_in',
            'quantity' => $add,
            'previous_stock' => $prev,
            'current_stock' => $curr,
            'notes' => 'Production batch #' . $production->batch_number . ' completed',
        ]);
    }

    /**
     * Generate next incremental batch number
     */
    private function generateNextBatchNumber(): string
    {
        $today = now()->format('Ymd');
        $latestBatch = Production::where('batch_number', 'like', "BATCH-{$today}-%")
            ->orderByDesc('batch_number')
            ->first();

        if ($latestBatch) {
            // Extract the sequence number from the latest batch
            $parts = explode('-', $latestBatch->batch_number);
            $sequence = isset($parts[2]) ? (int)$parts[2] : 0;
            $nextSequence = $sequence + 1;
        } else {
            $nextSequence = 1;
        }

        return "BATCH-{$today}-" . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
    }
}
