<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - Arbee's Bakeshop</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #222; margin: 0; padding: 0; }
        .receipt-container { width: 340px; margin: 0 auto; padding: 18px 12px; background: #fff; border: 1px solid #eee; border-radius: 8px; }
        .logo { text-align: center; margin-bottom: 10px; }
        .logo img { height: 60px; border-radius: 50%; border: 2px solid #222; }
        .shop-title { text-align: center; font-size: 1.3em; font-weight: bold; margin-bottom: 2px; letter-spacing: 1px; }
        .shop-address { text-align: center; font-size: 11px; color: #666; margin-bottom: 10px; }
        .receipt-title { text-align: center; font-size: 1.1em; font-weight: bold; margin: 10px 0 8px; }
        .info-table, .summary-table { width: 100%; margin-bottom: 10px; }
        .info-table td { font-size: 11px; padding: 2px 0; }
        .summary-table td { font-size: 12px; padding: 3px 0; }
        .summary-table .label { text-align: left; color: #555; }
        .summary-table .value { text-align: right; font-weight: bold; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .items-table th, .items-table td { font-size: 11px; padding: 3px 2px; border-bottom: 1px dashed #ccc; }
        .items-table th { text-align: left; font-weight: bold; background: #f7f7f7; }
        .items-table td.qty, .items-table td.price, .items-table td.total { text-align: right; }
        .footer { text-align: center; font-size: 10px; color: #888; margin-top: 12px; }
    </style>
</head>
<body>
<div class="receipt-container">
    <div class="logo">
        <img src="{{ public_path('images/Arbee\'s_logo_round.png') }}" alt="Arbee's Logo">
    </div>
    <div class="shop-title">Arbee's Bakeshop</div>
    <div class="shop-address">Brgy 76-A, New Matina, Bucana, Davao City<br>VAT Reg TIN: 123-456-789-000</div>
    <div class="receipt-title">OFFICIAL RECEIPT</div>
    <table class="info-table">
        <tr><td>Order #:</td><td>{{ $sale->order_number }}</td></tr>
        <tr><td>Date:</td><td>{{ $sale->order_date }}</td></tr>
        <tr><td>Time:</td><td>{{ $sale->created_at->format('g:i A') }}</td></tr>
        <tr><td>Cashier:</td><td>{{ $sale->cashier->first_name ?? 'N/A' }} {{ $sale->cashier->last_name ?? '' }}</td></tr>
        <tr><td>Order Type:</td><td>{{ ucfirst(str_replace('_',' ', $sale->order_type)) }}</td></tr>
    </table>
    <table class="items-table">
        <thead>
        <tr><th>Item</th><th class="qty">Qty</th><th class="price">Price</th><th class="total">Total</th></tr>
        </thead>
        <tbody>
        @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->product->name ?? 'N/A' }}</td>
                <td class="qty">{{ $item->quantity }}</td>
                <td class="price">₱{{ number_format($item->unit_price, 2) }}</td>
                <td class="total">₱{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table class="summary-table">
        <tr><td class="label">Subtotal:</td><td class="value">₱{{ number_format($sale->subtotal, 2) }}</td></tr>
        <tr><td class="label">VAT ({{ config('vat.vat_rate', 12) }}%):</td><td class="value">₱{{ number_format($sale->vat_amount, 2) }}</td></tr>
        <tr><td class="label">Total:</td><td class="value">₱{{ number_format($sale->total_amount, 2) }}</td></tr>
        <tr><td class="label">Amount Paid:</td><td class="value">₱{{ number_format($sale->cash_given, 2) }}</td></tr>
        <tr><td class="label">Change:</td><td class="value">₱{{ number_format($sale->change, 2) }}</td></tr>
        <tr><td class="label">Payment:</td><td class="value">{{ ucfirst($sale->payment_method ?? 'Cash') }}</td></tr>
    </table>
    <div class="footer">
        Thank you for your purchase!<br>
        This serves as your official receipt.<br>
        <b>Arbee's Bakeshop</b>
    </div>
</div>
</body>
</html>
