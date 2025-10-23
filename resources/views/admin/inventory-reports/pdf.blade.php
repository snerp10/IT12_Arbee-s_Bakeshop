<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report for {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border-bottom: 1px solid #ddd; }
        th { background: #EEF5DB; }
        .text-end { text-align: right; }
        .muted { color: #666; }
    </style>
    </head>
<body>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory Report</title>
        <style>
            * { box-sizing: border-box; }
            body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111827; margin: 16px; }
            h1, h2, h3, h4, h5 { margin: 0; }
            .muted { color: #6b7280; }
            .report-header { margin-bottom: 14px; }
            .period { font-size: 11px; color: #6b7280; margin-top: 4px; }
            .summary { display: table; width: 100%; margin: 10px 0 14px; }
            .summary .cell { display: table-cell; width: 25%; padding: 8px 10px; border: 1px solid #e5e7eb; }
            .label { font-size: 11px; color: #6b7280; margin-bottom: 4px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #e5e7eb; padding: 6px 8px; }
            thead th { background: #f9fafb; font-weight: 600; }
            tfoot th { background: #f3f4f6; }
            .text-end { text-align: right; }
        </style>
        <div style="text-align: center; margin-bottom: 18px;">
            <img src="{{ public_path('images/Arbee\'s_logo_round.png') }}" alt="Logo" style="height:72px; border: 2px solid black; border-radius: 50%;">
            <div style="font-size: 1.7em; font-weight: bold; margin-top: 8px; letter-spacing: 1px;">
                Arbee's Bakeshop
            </div>
        </div>
    </head>
    <body>
        <div class="report-header">
            <h2>Inventory Report</h2>
            <div class="period">Period: <b>{{ $dateFrom }} to {{ $dateTo }}<b></div>
        </div>

        <div class="summary">
            <div class="cell">
                <div class="label">Total Stock In</div>
                <div><strong>{{ number_format($totalIn ?? 0) }}</strong></div>
            </div>
            <div class="cell">
                <div class="label">Total Stock Out</div>
                <div><strong>{{ number_format($totalOut ?? 0) }}</strong></div>
            </div>
            <div class="cell">
                <div class="label">Adjustments</div>
                <div><strong>{{ number_format($totalAdjust ?? 0) }}</strong></div>
            </div>
            <div class="cell">
                <div class="label">Net Change</div>
                <div><strong>{{ number_format($netChange ?? 0) }}</strong></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Prev</th>
                    <th class="text-end">Current</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $m)
                    <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($m->created_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $m->product->sku ?? '-' }} - {{ $m->product->name ?? 'Unknown' }}</td>
                        <td>{{ ucfirst(str_replace('_',' ', $m->transaction_type)) }}</td>
                        <td class="text-end">{{ $m->quantity }}</td>
                        <td class="text-end">{{ $m->previous_stock }}</td>
                        <td class="text-end">{{ $m->current_stock }}</td>
                        <td>{{ $m->notes }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="muted">No movements in range.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Totals</th>
                    <th class="text-end">{{ number_format(($totalIn ?? 0) - ($totalOut ?? 0) + ($totalAdjust ?? 0)) }}</th>
                    <th colspan="3">&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </body>
    </html>
