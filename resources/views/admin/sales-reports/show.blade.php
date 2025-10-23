@extends('layouts.admin')
@section('title', 'Detailed Sales Report')
@section('page-title', 'Detailed Sales Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-muted-sea">
            <i class="fas fa-chart-bar"></i> Detailed Sales Report
        </h1>
        <div class="btn-group gap-3">
            <a href="{{ route('admin.sales-reports.index', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn-admin-light">
                <i class="fas fa-arrow-left me-2"></i> Back to List
            </a>
            <a href="{{ route('admin.sales-reports.export', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn-admin-primary">
                <i class="fas fa-file-pdf me-2"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Period Info -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-calendar me-2"></i>
        <strong>Report Period:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
    </div>

    <!-- Daily Sales Summary -->
    <div class="card kpi-card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-sea-primary">
                <i class="fas fa-calendar-day me-2"></i> Daily Sales Summary
            </h6>
        </div>
        <div class="card-body admin-card-body">
            @if($dailySales->count())
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th class="text-end">Transactions</th>
                                <th class="text-end">Total Sales</th>
                                <th class="text-end">Average per Transaction</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalTrans = 0; $totalAmount = 0; @endphp
                            @foreach($dailySales as $day)
                                @php $totalTrans += $day->transactions; $totalAmount += $day->total; @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y (D)') }}</td>
                                    <td class="text-end">{{ number_format($day->transactions) }}</td>
                                    <td class="text-end">₱{{ number_format($day->total, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($day->transactions > 0 ? $day->total / $day->transactions : 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>Total</th>
                                <th class="text-end">{{ number_format($totalTrans) }}</th>
                                <th class="text-end">₱{{ number_format($totalAmount, 2) }}</th>
                                <th class="text-end">₱{{ number_format($totalTrans > 0 ? $totalAmount / $totalTrans : 0, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                    <h5>No sales data for this period</h5>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Top Products -->
        <div class="col-md-6">
            <div class="card kpi-card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-sea-primary">
                        <i class="fas fa-trophy me-2"></i> Top Products
                    </h6>
                </div>
                <div class="card-body admin-card-body">
                    @if($topProducts->count())
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Qty Sold</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topProducts as $product)
                                        <tr>
                                            <td>
                                                <strong>{{ $product->name }}</strong><br>
                                                <small class="text-muted">{{ $product->sku }}</small>
                                            </td>
                                            <td class="text-end">{{ number_format($product->total_qty) }}</td>
                                            <td class="text-end">₱{{ number_format($product->total_revenue, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box fa-2x text-gray-300 mb-3"></i>
                            <p>No product sales data</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Cashier Performance -->
        <div class="col-md-6">
            <div class="card kpi-card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-sea-primary">
                        <i class="fas fa-users me-2"></i> Cashier Performance
                    </h6>
                </div>
                <div class="card-body admin-card-body">
                    @if($cashierPerformance->count())
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Cashier</th>
                                        <th class="text-end">Transactions</th>
                                        <th class="text-end">Total Sales</th>
                                        <th class="text-end">Avg/Transaction</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cashierPerformance as $perf)
                                        <tr>
                                            <td>{{ $perf->cashier ? ($perf->cashier->first_name . ' ' . $perf->cashier->last_name) : 'Unknown' }}</td>
                                            <td class="text-end">{{ number_format($perf->transactions) }}</td>
                                            <td class="text-end">₱{{ number_format($perf->total_sales, 2) }}</td>
                                            <td class="text-end">₱{{ number_format($perf->transactions > 0 ? $perf->total_sales / $perf->transactions : 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-times fa-2x text-gray-300 mb-3"></i>
                            <p>No cashier performance data</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Hourly Sales Pattern -->
    <div class="card kpi-card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-sea-primary">
                <i class="fas fa-clock me-2"></i> Hourly Sales Pattern
            </h6>
        </div>
        <div class="card-body admin-card-body">
            @if($hourlySales->count())
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Hour</th>
                                <th class="text-end">Transactions</th>
                                <th class="text-end">Total Sales</th>
                                <th class="text-end">Average per Transaction</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hourlySales as $hour)
                                <tr>
                                    <td>{{ sprintf('%02d:00 - %02d:59', $hour->hour, $hour->hour) }}</td>
                                    <td class="text-end">{{ number_format($hour->transactions) }}</td>
                                    <td class="text-end">₱{{ number_format($hour->total, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($hour->transactions > 0 ? $hour->total / $hour->transactions : 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-clock fa-3x text-gray-300 mb-3"></i>
                    <h5>No hourly sales data</h5>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection