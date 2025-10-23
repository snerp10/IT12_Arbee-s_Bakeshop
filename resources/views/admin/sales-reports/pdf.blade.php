<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        /* Minimal print styles - PDF only contains the report */
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111827; margin: 16px; }
        h1, h2, h3, h4, h5 { margin: 0; }
        .muted { color: #6b7280; }
        .report-header { margin-bottom: 14px; }
        .period { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .summary { display: table; width: 100%; margin: 10px 0 14px; }
        .summary .cell { display: table-cell; width: 33.33%; padding: 8px 10px; border: 1px solid #e5e7eb; }
        .label { font-size: 11px; color: #6b7280; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; }
        thead th { background: #f9fafb; font-weight: 600; }
        tfoot th { background: #f3f4f6; }
        .text-end { text-align: right; }
    </style>
    <!-- Optional: You can add a logo by placing an <img> here if needed for branding -->
<div style="text-align: center; margin-bottom: 18px;">
    <img src="{{ public_path('images/Arbee\'s_logo_round.png') }}" alt="Logo" style="height:72px; border: 2px solid black; border-radius: 50%;">
    <div style="font-size: 1.7em; font-weight: bold; margin-top: 8px; letter-spacing: 1px;">
        Arbee's Bakeshop
    </div>
</div>
    
</head>
<body>
    <div class="report-header">
        <h2>Sales Report</h2>
        <div class="period">Period: <b>{{ $dateFrom }} to {{ $dateTo }}<b></div>
    </div>

    <div class="summary">
        <div class="cell">
            <div class="label">Total Sales</div>
            <div><strong>₱{{ number_format($totalSales, 2) }}</strong></div>
        </div>
        <div class="cell">
            <div class="label">Total Transactions</div>
            <div><strong>{{ number_format($totalTransactions) }}</strong></div>
        </div>
        <div class="cell">
            <div class="label">Average Transaction</div>
            <div><strong>₱{{ number_format($averageTransaction, 2) }}</strong></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Cashier</th>
                <th class="text-end">Items</th>
                <th class="text-end">Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                <tr>
                    <td>{{ $sale->order_number }}</td>
                    <td>{{ $sale->order_date?->format('Y-m-d') }}</td>
                    <td>{{ $sale->cashier ? ($sale->cashier->first_name . ' ' . $sale->cashier->last_name) : '—' }}</td>
                    <td class="text-end">{{ $sale->items->sum('quantity') }}</td>
                    <td class="text-end">₱{{ number_format($sale->total_amount, 2) }}</td>
                    <td>{{ ucfirst($sale->status) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Totals</th>
                <th class="text-end">₱{{ number_format($totalSales, 2) }}</th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
