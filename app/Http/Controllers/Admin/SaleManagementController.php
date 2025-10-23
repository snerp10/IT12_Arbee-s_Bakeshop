<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\InventoryStock;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['items.product','cashier']);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s){
                $q->where('order_number','like',"%$s%")
                  ->orWhere('notes','like',"%$s%");
            });
        }
        if ($request->filled('date_from')) $query->whereDate('order_date','>=',$request->date_from);
        if ($request->filled('date_to')) $query->whereDate('order_date','<=',$request->date_to);

        $sales = $query->orderByDesc('order_date')->paginate(15);
        return view('admin.sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::active()->with('inventoryStock')->orderBy('name')->get();
        $cashiers = Employee::orderBy('last_name')->get(['emp_id','first_name','last_name']);
        return view('admin.sales.create', compact('products','cashiers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cashier_id' => 'required|exists:employees,emp_id',
            'items' => 'required|array|min:1',
            'items.*.prod_id' => 'required|exists:products,prod_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,gcash',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        // Calculate subtotal
        $subtotal = 0;
        foreach ($request->items as $it) {
            $subtotal += ((float)$it['unit_price']) * ((int)$it['quantity']);
        }

        // VAT calculation
        $vatRate = config('vat.vat_rate', 12);
        $vatAmount = round($subtotal * ($vatRate / 100), 2);
        $total = $subtotal + $vatAmount;

        // Verify the total matches
        if (abs($total - (float)$request->total_amount) > 0.01) {
            return back()->withErrors(['total_amount' => 'Total amount mismatch. Please refresh and try again.'])->withInput();
        }


        // Calculate change
        $cashGiven = (float)$request->amount_paid;
        $change = round($cashGiven - $total, 2);
        if ($cashGiven < $total) {
            return back()->withErrors(['amount_paid' => 'Amount paid must be at least equal to the total amount.'])->withInput();
        }

    DB::transaction(function() use ($request, $subtotal, $vatAmount, $total, $cashGiven, $change) {
            // Auto-generate order number
            $orderNumber = 'SO-' . date('Ymd') . '-' . str_pad(Sale::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'order_number' => $orderNumber,
                'order_type' => 'takeout',
                'cashier_id' => $request->cashier_id,
                'subtotal' => $subtotal,
                'vat_amount' => $vatAmount,
                'total_amount' => $total,
                'cash_given' => $cashGiven,
                'change' => $change,
                'order_date' => now()->toDateString(),
                'status' => 'completed',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $it) {
                $product = Product::lockForUpdate()->findOrFail($it['prod_id']);
                $qty = (int)$it['quantity'];
                $unit = (float)$it['unit_price'];
                $totalPrice = bcmul((string)$unit, (string)$qty, 2);

                SaleItem::create([
                    'so_id' => $sale->so_id,
                    'prod_id' => $product->prod_id,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'total_price' => $totalPrice,
                    'special_instructions' => null,
                ]);

                // Update inventory snapshot and record movement
                $snapshot = InventoryStock::lockForUpdate()->where('prod_id', $product->prod_id)->first();
                $prev = $snapshot ? $snapshot->quantity : 0;
                $newQty = max(0, $prev - $qty);
                if ($snapshot) {
                    $snapshot->update(['quantity' => $newQty, 'last_counted_at' => now()]);
                } else {
                    InventoryStock::create([
                        'prod_id' => $product->prod_id,
                        'quantity' => $newQty,
                        'reorder_level' => 0,
                        'last_counted_at' => now(),
                    ]);
                }
                InventoryMovement::create([
                    'prod_id' => $product->prod_id,
                    'transaction_type' => 'stock_out',
                    'quantity' => $qty,
                    'previous_stock' => $prev,
                    'current_stock' => $newQty,
                    'notes' => 'Sale #' . $orderNumber,
                ]);
            }
            // Log to audit
            if (class_exists('App\\Models\\AuditLog')) {
                $desc = "Sale completed.\n" .
                    "Order Number: {$sale->order_number}\n" .
                    "Total Amount: ₱" . number_format($sale->total_amount, 2) . "\n" .
                    "Cash Given: ₱" . number_format($sale->cash_given, 2) . "\n" .
                    "Change Returned: ₱" . number_format($sale->change, 2);
                \App\Models\AuditLog::logAction(
                    'create',
                    'sales_orders',
                    $sale->so_id,
                    null,
                    [
                        'total_amount' => $sale->total_amount,
                        'cash_given' => $sale->cash_given,
                        'change' => $sale->change,
                        'order_number' => $sale->order_number,
                    ],
                    $desc
                );
            }
        });

        return redirect()->route('admin.sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product','cashier']);
        return view('admin.sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
    $products = Product::active()->orderBy('name')->get();
    $cashiers = Employee::orderBy('last_name')->get(['emp_id','first_name','last_name']);
        $sale->load('items.product');
        
        return view('admin.sales.edit', compact('sale', 'products', 'cashiers'));
    }

    public function update(Request $request, Sale $sale)
    {
        // For security, only allow editing notes and cashier for completed sales
        if ($sale->status !== 'completed') {
            return back()->with('error', 'Only completed sales can be edited.');
        }

        $validator = Validator::make($request->all(), [
            'cashier_id' => 'required|exists:employees,emp_id',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $sale->update([
            'cashier_id' => $request->cashier_id,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.sales.index')->with('success', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale)
    {
        if ($sale->status === 'completed') {
            return back()->with('error', 'Cannot delete completed sales. Cancel them first.');
        }
        
        $sale->delete();
        return redirect()->route('admin.sales.index')->with('success', 'Sale deleted successfully.');
    }

    public function cancel(Sale $sale)
    {
        if ($sale->status === 'cancelled') return back()->with('info','Sale already cancelled.');
        DB::transaction(function() use ($sale) {
            $sale->load('items');
            $sale->update(['status' => 'cancelled']);
            foreach ($sale->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->prod_id);

                // Update inventory snapshot
                $snapshot = InventoryStock::lockForUpdate()->where('prod_id', $product->prod_id)->first();
                $prev = $snapshot ? $snapshot->quantity : 0;
                $newQty = $prev + (int)$item->quantity;
                if ($snapshot) {
                    $snapshot->update(['quantity' => $newQty, 'last_counted_at' => now()]);
                } else {
                    InventoryStock::create([
                        'prod_id' => $product->prod_id,
                        'quantity' => $newQty,
                        'reorder_level' => 0,
                        'last_counted_at' => now(),
                    ]);
                }

                // Record inventory movement (adjustment back in)
                InventoryMovement::create([
                    'prod_id' => $product->prod_id,
                    'transaction_type' => 'adjustment',
                    'quantity' => (int)$item->quantity,
                    'previous_stock' => $prev,
                    'current_stock' => $newQty,
                    'notes' => 'Cancel Sale #' . $sale->order_number,
                ]);
            }
        });
        return back()->with('success','Sale cancelled and stock restored.');
    }
}
