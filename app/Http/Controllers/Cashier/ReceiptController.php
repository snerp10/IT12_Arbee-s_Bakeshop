<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function pdf($saleId)
    {
        $cashier = Auth::user();
        $sale = Sale::with('items.product', 'cashier')
            ->where('so_id', $saleId)
            ->where('cashier_id', $cashier->employee->emp_id ?? null)
            ->firstOrFail();

        $pdf = Pdf::loadView('cashier.sales.receipt_pdf', compact('sale'));
        $filename = 'receipt_' . $sale->order_number . '.pdf';
        return $pdf->download($filename);
    }
}
