@extends('layouts.admin')
@section('title', 'Inventory Summary')
@section('page-title', 'Inventory Summary')

@section('content')
<div class="container-fluid">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-muted-sea">
        <i class="fas fa-chart-line me-2"></i> Inventory Summary
    </h1>
    <div>
        <a href="{{ route('admin.inventory-reports.index', request()->all()) }}" class="btn-admin-light me-2">
            <i class="fas fa-list me-1"></i> Ledger View
        </a>
        <a href="{{ route('admin.inventory-reports.export', request()->all()) }}" class="btn-admin-primary">
            <i class="fas fa-file-export me-2"></i> Export PDF
        </a>
    </div>
    </div>

<div class="card admin-filter-card shadow mb-4">
    <div class="card-body admin-card-body">
        <form method="GET" action="{{ route('admin.inventory-reports.show') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">From</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn-admin-secondary">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card kpi-card h-100 shadow">
            <div class="card-body admin-card-body">
                <h5 class="mb-3">Daily Movements</h5>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-end">Stock In</th>
                                <th class="text-end">Stock Out</th>
                                <th class="text-end">Adjustments</th>
                                <th class="text-end">Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($daily as $d)
                                <tr>
                                    <td>{{ $d->date }}</td>
                                    <td class="text-end">{{ number_format($d->total_in) }}</td>
                                    <td class="text-end">{{ number_format($d->total_out) }}</td>
                                    <td class="text-end">{{ number_format($d->total_adjust) }}</td>
                                    <td class="text-end">{{ number_format(($d->total_in ?? 0) - ($d->total_out ?? 0) + ($d->total_adjust ?? 0)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No data in selected range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card kpi-card h-100 shadow">
            <div class="card-body admin-card-body">
                <h5 class="mb-3">Top Stock Outs</h5>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-end">Total Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topOut as $row)
                                <tr>
                                    <td>{{ $row->sku }} - {{ $row->name }}</td>
                                    <td class="text-end">{{ number_format($row->total_out) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No stock-out data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection
