<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111827; margin: 16px; }
        h1, h2, h3, h4, h5 { margin: 0; }
        .muted { color: #6b7280; }
        .report-header { margin-bottom: 14px; }
        .period { font-size: 11px; color: #6b7280; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; }
        thead th { background: #f9fafb; font-weight: 600; }
        .text-end { text-align: right; }
        .nowrap { white-space: nowrap; }
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
        <h2>Audit Logs</h2>
        @if($dateFrom || $dateTo)
          <div class="period">Period: <b>{{ $dateFrom ?? '...' }} to {{ $dateTo ?? '...' }}<b></div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="nowrap">Date</th>
                <th>User</th>
                <th>Action</th>
                <th>Table</th>
                <th class="nowrap">Record ID</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="nowrap">{{ \Illuminate\Support\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                    <td>{{ optional($log->user)->username }}</td>
                    <td>{{ ucfirst($log->action) }}</td>
                    <td>{{ $log->table_name }}</td>
                    <td class="nowrap">{{ $log->record_id }}</td>
                    <td>{{ $log->description }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="muted">No audit logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
