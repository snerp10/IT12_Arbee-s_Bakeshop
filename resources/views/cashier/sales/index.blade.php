@extends('layouts.cashier')

@section('title', 'Sales History')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="fas fa-receipt"></i>
                Sales History
            </h1>
            <p class="page-subtitle">View and manage your sales transactions</p>
        </div>
        <a href="{{ route('cashier.pos.index') }}" class="btn-admin-primary">
            <i class="fas fa-plus-circle"></i>
            New Sale
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <form action="{{ route('cashier.sales.index') }}" method="GET">
        <div class="filter-row">
            <div class="filter-group">
                <label class="form-label-cashier">Start Date</label>
                <input type="date" name="start_date" class="form-control-cashier" value="{{ request('start_date') }}">
            </div>
            <div class="filter-group">
                <label class="form-label-cashier">End Date</label>
                <input type="date" name="end_date" class="form-control-cashier" value="{{ request('end_date') }}">
            </div>
            <div class="filter-group">
                <label class="form-label-cashier">Search</label>
                <input type="text" name="search" class="form-control-cashier" placeholder="Order number..." value="{{ request('search') }}">
            </div>
            <div class="filter-group" style="flex: 0 0 auto;">
                <button type="submit" class="btn-admin-secondary">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Sales Table -->
<div class="content-card w-100">
    <div class="content-card-header">
        <h3 class="content-card-title">
            <i class="fas fa-list"></i>
            Your Sales ({{ $sales->total() }} records)
        </h3>
    </div>

    @if($sales->count() > 0)
        <div class="table-responsive w-100">
            <table class="table-cashier w-100">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date & Time</th>
                        <th>Order Type</th>
                        <th>Items</th>
                        <th>Total Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ $sale->order_number }}</strong>
                            </td>
                            <td>
                                <small>
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($sale->order_date)->format('M j, Y') }}
                                    <br>
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $sale->created_at->format('g:i A') }}
                                </small>
                            </td>
                            <td>
                                <span class="badge-chip {{ strtolower($sale->order_type) == 'dine_in' ? 'badge-sea-light' : 'badge-sea-primary' }}">
                                    @if($sale->order_type == 'dine_in')
                                        <i class="fas fa-utensils me-1"></i> Dine In
                                    @elseif($sale->order_type == 'takeout')
                                        <i class="fas fa-shopping-bag me-1"></i> Takeout
                                    @else
                                        <i class="fas fa-question me-1"></i> {{ ucfirst($sale->order_type) }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $sale->items->count() }} items</span>
                            </td>
                            <td>
                                <strong class="text-primary">₱{{ number_format($sale->total_amount, 2) }}</strong>
                            </td>
                            <td>
                                <a href="{{ route('cashier.sales.show', $sale->so_id) }}" 
                                   class="btn-admin-light">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
            <div class="mt-3">
                {{ $sales->links('vendor.pagination.admin') }}
            </div>
        @endif
    @else
        <div class="text-center py-5">
            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-3">No sales records found</p>
            <a href="{{ route('cashier.pos.index') }}" class="btn-admin-primary">
                <i class="fas fa-plus-circle"></i>
                Create Your First Sale
            </a>
        </div>
    @endif
</div>

@endsection
