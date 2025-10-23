@extends('layouts.admin')
@section('title', 'Inventory Reports')
@section('page-title', 'Inventory Reports')

@section('content')
<div class="container-fluid">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-muted-sea">
        <i class="fas fa-warehouse me-2"></i> Inventory Reports
    </h1>
    <a href="{{ route('admin.inventory-reports.export', request()->all()) }}" class="btn-admin-primary">
        <i class="fas fa-file-export me-2"></i> Export PDF
    </a>
    </div>

    <div class="card admin-filter-card shadow mb-4">
        <div class="card-body admin-card-body">
            <form method="GET" action="{{ route('admin.inventory-reports.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Product</label>
                    <select name="prod_id" class="form-select">
                        <option value="">All Products</option>
                        @foreach($products as $p)
                            <option value="{{ $p->prod_id }}" {{ request('prod_id') == $p->prod_id ? 'selected' : '' }}>
                                {{ $p->sku }} - {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="transaction_type" class="form-select">
                        <option value="">All</option>
                        <option value="stock_in" {{ request('transaction_type')=='stock_in' ? 'selected' : '' }}>Stock In</option>
                        <option value="stock_out" {{ request('transaction_type')=='stock_out' ? 'selected' : '' }}>Stock Out</option>
                        <option value="adjustment" {{ request('transaction_type')=='adjustment' ? 'selected' : '' }}>Adjustment</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn-admin-secondary">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <button type="reset" class="btn-admin-light" onclick="window.location='{{ route('admin.inventory-reports.index') }}'">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card kpi-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="text-sea-primary">
                        <i class="fas fa-arrow-down fa-2x"></i>
                    </div>
                    <div class="flex-grow-1 text-end">
                        <h3 class="mb-0">{{ number_format($totals['stock_in'] ?? 0) }}</h3>
                        <p class="text-muted mb-0">Total Stock In</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="text-sea-primary">
                        <i class="fas fa-arrow-up fa-2x"></i>
                    </div>
                    <div class="flex-grow-1 text-end">
                        <h3 class="mb-0">{{ number_format($totals['stock_out'] ?? 0) }}</h3>
                        <p class="text-muted mb-0">Total Stock Out</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="text-sea-primary">
                        <i class="fas fa-sliders-h fa-2x"></i>
                    </div>
                    <div class="flex-grow-1 text-end">
                        <h3 class="mb-0">{{ number_format($totals['adjustment'] ?? 0) }}</h3>
                        <p class="text-muted mb-0">Adjustments</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card kpi-card shadow">
        <div class="card-body admin-card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Movement Ledger</h5>
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn-admin-light btn-sm border border-1 d-flex align-items-center gap-1" onclick="window.location='{{ route('admin.inventory-reports.show', request()->all()) }}'" title="Summary View">
                        <i class="fas fa-chart-line" aria-label="Summary View"></i>
                        <span class="small text-muted">Summary</span>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless align-middle">
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
                                <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $m->product->sku ?? '-' }} - {{ $m->product->name ?? 'Unknown' }}</td>
                                <td>
                                    @if($m->transaction_type == 'stock_in')
                                        <span class="badge-admin-role" style="background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%) !important; color: #fff !important;">
                                            <i class="fas fa-arrow-down me-1" aria-label="Stock In"></i> Stock In
                                        </span>
                                    @elseif($m->transaction_type == 'stock_out')
                                        <span class="badge-admin-role admin">
                                            <i class="fas fa-arrow-up me-1" aria-label="Stock Out"></i> Stock Out
                                        </span>
                                    @else
                                        <span class="badge-admin-role manager">
                                            <i class="fas fa-sliders-h me-1" aria-label="Adjustment"></i> Adjustment
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">{{ $m->quantity }}</td>
                                <td class="text-end">{{ $m->previous_stock }}</td>
                                <td class="text-end">{{ $m->current_stock }}</td>
                                <td>{{ $m->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No movements for selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                {{ $movements->appends(request()->all())->links('vendor.pagination.admin') }}
            </div>
        </div>
    </div>
    </div>
@endsection
