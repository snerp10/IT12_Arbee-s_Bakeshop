@extends('layouts.admin')
@section('title', 'Sales Reports')
@section('page-title', 'Sales Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-muted-sea">
            <i class="fas fa-chart-line"></i> Sales Reports
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.sales-reports.show', request()->query()) }}" class="btn-admin-secondary">
                <i class="fas fa-chart-bar me-2"></i> Detailed Report
            </a>
            <a href="{{ route('admin.sales-reports.export', request()->query()) }}" class="btn-admin-primary">
                <i class="fas fa-file-pdf me-2"></i> Export PDF
            </a>
        </div>
    </div>
        <!-- Filters -->
    <div class="card admin-filter-card shadow mb-4">
        <div class="card-body admin-card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(['completed','cancelled','refunded'] as $s)
                            <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cashier</label>
                    <select name="cashier_id" class="form-select">
                        <option value="">All cashiers</option>
                        @foreach($cashiers as $emp)
                            <option value="{{ $emp->emp_id }}" {{ request('cashier_id')==$emp->emp_id ? 'selected' : '' }}>
                                {{ $emp->first_name }} {{ $emp->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Sale # or notes">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn-admin-secondary me-2" type="submit"><i class="fas fa-filter me-1"></i> Apply</button>
                    <button type="button" onclick="window.location='{{ route('admin.sales-reports.index') }}'" class="btn-admin-light">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card kpi-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="text-sea-primary">
                        <i class="fas fa-peso-sign fa-2x"></i>
                    </div>
                    <div class="flex-grow-1 text-end">
                        <h3 class="mb-0">{{ '₱' . number_format($totalSales, 2) }}</h3>
                        <p class="text-muted mb-0">Total Sales</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="text-sea-primary">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <div class="flex-grow-1 text-end">
                        <h3 class="mb-0">{{ number_format($totalTransactions) }}</h3>
                        <p class="text-muted mb-0">Total Transactions</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="text-sea-primary">
                        <i class="fas fa-calculator fa-2x"></i>
                    </div>
                    <div class="flex-grow-1 text-end">
                        <h3 class="mb-0">{{ '₱' . number_format($averageTransaction, 2) }}</h3>
                        <p class="text-muted mb-0">Average Transaction</p>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Sales Table -->
    <div class="card kpi-card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-sea-primary">Sales Transactions ({{ $sales->total() }})</h6>
            <small class="text-muted">{{ $dateFrom }} to {{ $dateTo }}</small>
        </div>
        <div class="card-body admin-card-body">
            @if($sales->count())
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Cashier</th>
                                <th class="text-end">Items</th>
                                <th class="text-end">Total</th>
                                <th>Status</th>
                                <th width="120" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                                <tr>
                                    <td><a class="text-decoration-none" href="{{ route('admin.sales.show', $sale) }}">{{ $sale->order_number }}</a></td>
                                    <td>{{ $sale->order_date?->format('M d, Y') }}</td>
                                    <td>{{ $sale->cashier ? ($sale->cashier->first_name . ' ' . $sale->cashier->last_name) : '—' }}</td>
                                    <td class="text-end">{{ $sale->items->sum('quantity') }}</td>
                                    <td class="text-end">₱{{ number_format($sale->total_amount, 2) }}</td>
                                    <td>
                                        @switch($sale->status)
                                            @case('completed')
                                                <span class="badge-admin-role" style="background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%) !important; color: #fff !important;">
                                                    <i class="fas fa-check-circle me-1" aria-label="Completed"></i> Completed
                                                </span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge-admin-role baker"><i class="fas fa-times-circle me-1" aria-label="Cancelled"></i> Cancelled</span>
                                                @break
                                            @case('refunded')
                                                <span class="badge-admin-role manager"><i class="fas fa-undo me-1" aria-label="Refunded"></i> Refunded</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            <button type="button"
                                                class="btn-admin-light btn-sm border border-1 d-flex align-items-center gap-1"
                                                onclick="window.location='{{ route('admin.sales.show', $sale) }}'"
                                                title="View"
                                                aria-label="View Sale">
                                                <i class="fas fa-eye" aria-label="View"></i>
                                                <span class="small text-muted">View</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>Showing {{ $sales->firstItem() }} to {{ $sales->lastItem() }} of {{ $sales->total() }}</div>
                    {{ $sales->appends(request()->query())->links('vendor.pagination.admin') }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i>
                    <h5>No sales found for selected period</h5>
                    <p class="text-muted">Try adjusting your date range or filters</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection