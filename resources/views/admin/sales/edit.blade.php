@extends('layouts.admin')
@section('title', 'Edit Sale')
@section('page-title', 'Edit Sale')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.sales.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-edit"></i> Edit Sale: {{ $sale->order_number }}
        </h1>
    </div>

    @if($sale->status !== 'completed')
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Note:</strong> Only completed sales can be edited for security reasons.
        </div>
    @else
        <form method="POST" action="{{ route('admin.sales.update', $sale) }}">
            @csrf
            @method('PUT')
            
            <!-- Sale Details (Read-only) -->
            <div class="card admin-card mb-3">
                <div class="card-header">
                    <h6 class="m-0"><i class="fas fa-info-circle me-2"></i>Sale Information</h6>
                </div>
                <div class="card-body admin-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr><td><strong>Order Number:</strong></td><td>{{ $sale->order_number }}</td></tr>
                                <tr><td><strong>Date:</strong></td><td>{{ $sale->order_date->format('M d, Y') }}</td></tr>
                                <tr><td><strong>Status:</strong></td><td>
                                    <span class="badge-admin-role admin">
                                        <i class="fas fa-check-circle me-1"></i> {{ ucfirst($sale->status) }}
                                    </span>
                                </td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr><td><strong>Total Amount:</strong></td><td>₱{{ number_format($sale->total_amount, 2) }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items (Read-only) -->
            <div class="card admin-card mb-3">
                <div class="card-header">
                    <h6 class="m-0"><i class="fas fa-list me-2"></i>Items Sold</h6>
                </div>
                <div class="card-body admin-card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }} <small class="text-muted">({{ $item->product->sku }})</small></td>
                                        <td class="text-end">{{ $item->quantity }}</td>
                                        <td class="text-end">₱{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end">₱{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Editable Fields -->
            <div class="card admin-card mb-3">
                <div class="card-header">
                    <h6 class="m-0"><i class="fas fa-edit me-2"></i>Editable Information</h6>
                </div>
                <div class="card-body admin-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Cashier</label>
                            <select name="cashier_id" class="form-select" required>
                                <option value="">Select cashier</option>
                                @foreach($cashiers as $emp)
                                    <option value="{{ $emp->emp_id }}" @selected(old('cashier_id', $sale->cashier_id) == $emp->emp_id)>
                                        {{ $emp->first_name }} {{ $emp->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this sale...">{{ old('notes', $sale->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>     
                <div class="row mt-2">
                    <div class="col-md-12 d-flex justify-content-end gap-2">
                        <button type="button" onclick="window.location='{{ route('admin.sales.index') }}'" class="btn-admin-light btn-sm">
                            <i class="fas fa-times me-2"></i> Cancel
                        </button>
                        <button class="btn-admin-secondary btn-sm" type="submit">
                            <i class="fas fa-save me-2"></i> Update Sale
                        </button>
                    </div>
                </div>
           
        </form>
    @endif
</div>
@endsection