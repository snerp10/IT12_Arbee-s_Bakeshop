<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Export</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111827; margin: 16px; }
        h2 { margin: 0 0 8px; }
        .meta { font-size: 11px; color: #6b7280; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; }
        thead th { background: #f9fafb; font-weight: 600; }
        .text-end { text-align: right; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    <h2>Products</h2>
    <div class="meta">Exported at: {{ now()->format('Y-m-d H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Name</th>
                <th>Category</th>
                <th class="nowrap">Unit</th>
                <th class="text-end">Price</th>
                
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
                <tr>
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->category->name ?? '-' }}</td>
                    <td class="nowrap">{{ $p->unit }}</td>
                    <td class="text-end">₱{{ number_format($p->price, 2) }}</td>
                    
                    <td>{{ ucfirst($p->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>