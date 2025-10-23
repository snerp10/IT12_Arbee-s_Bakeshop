<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InventoryStock;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        $products = Product::active()
            ->with(['inventoryStock', 'category'])
            ->whereHas('inventoryStock', function($q) {
                $q->where('quantity', '>', 0);
            })
            ->orderBy('name')
            ->get();
            
        return view('cashier.pos.index', compact('products'));
    }
    
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'cart' => 'required|json',
            'order_type' => 'required|in:walk-in,takeout,dine_in,delivery',
            'customer_name' => 'nullable|string|max:255',
            'cash_given' => 'required|numeric|min:0',
            'change' => 'required|numeric|min:0',
        ]);
        
        $cart = json_decode($request->cart, true);
        
        if (empty($cart)) {
            return back()->withErrors(['cart' => 'Cart is empty'])->withInput();
        }
        
        $cashier = Auth::user();
        
        // Calculate subtotal from cart
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ((float)$item['price']) * ((int)$item['quantity']);
        }

        // VAT calculation (match admin logic)
        $vatRate = config('vat.vat_rate', 12); // percent
        $vatAmount = round($subtotal * ($vatRate / 100), 2);
        $totalAmount = round($subtotal + $vatAmount, 2);

    DB::transaction(function() use ($request, $cashier, $subtotal, $vatAmount, $totalAmount, $cart, &$orderNumber) {
            // Generate unique order number
            $orderNumber = 'SO-' . date('Ymd') . '-' . str_pad(Sale::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'order_number' => $orderNumber,
                'order_type' => $request->order_type,
                'cashier_id' => $cashier->employee->emp_id ?? null,
                'subtotal' => $subtotal,
                'vat_amount' => $vatAmount,
                'total_amount' => $totalAmount,
                'cash_given' => $request->cash_given,
                'change' => $request->change,
                'order_date' => now()->toDateString(),
                'status' => 'completed',
                'notes' => $request->customer_name ? 'Customer: ' . $request->customer_name : null,
            ]);
            
            foreach ($cart as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['prod_id']);
                $qty = (int)$item['quantity'];
                $unit = (float)$item['price'];
                $totalPrice = bcmul((string)$unit, (string)$qty, 2);
                
                SaleItem::create([
                    'so_id' => $sale->so_id,
                    'prod_id' => $product->prod_id,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'total_price' => $totalPrice,
                ]);
                
                // Update inventory snapshot
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
                
                // Create inventory movement
                InventoryMovement::create([
                    'prod_id' => $product->prod_id,
                    'transaction_type' => 'stock_out',
                    'quantity' => $qty,
                    'previous_stock' => $prev,
                    'current_stock' => $newQty,
                    'notes' => 'POS Sale #' . $orderNumber,
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
        
        return redirect()->route('cashier.dashboard')->with('success', 'Sale completed successfully! Order #' . ($orderNumber ?? ''));
    }
}
