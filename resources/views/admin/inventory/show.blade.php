@extends('layouts.admin')

@section('title', 'Movement Details')
@section('page-title', 'Movement Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.inventory.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-exchange-alt"></i> Movement #{{ $movement->movement_id }}
        </h1>
        <a href="{{ route('admin.inventory.edit', $movement->movement_id) }}" class="btn-admin-secondary ms-3 position-absolute end-0">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
    </div>

    <div class="card kpi-card info shadow-sm">
        <div class="card-header">
            <h5 class="mb-0 text-sea-primary"><i class="fas fa-info-circle me-2"></i> Movement Information</h5>
        </div>
        <div class="card-body admin-card-body">
            <table class="table table-borderless">
                <tr>
                    <td width="200"><strong>Date:</strong></td>
                    <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                <tr>
                    <td><strong>Product:</strong></td>
                    <td>
                        <a href="{{ route('admin.products.show', $movement->product) }}" class="text-decoration-none">
                            {{ $movement->product->name }} <span class="text-muted">({{ $movement->product->sku }})</span>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><strong>Transaction Type:</strong></td>
                    <td>
                        @php
                            $typeClass = match($movement->transaction_type) {
                                'stock_in' => 'admin',
                                'stock_out' => 'baker',
                                'adjustment' => 'manager',
                                default => ''
                            };
                        @endphp
                        <span class="badge-admin-role {{ $typeClass }}">
                            @if($movement->transaction_type === 'stock_in')
                                <i class="fas fa-arrow-up me-1"></i>
                            @elseif($movement->transaction_type === 'stock_out')
                                <i class="fas fa-arrow-down me-1"></i>
                            @else
                                <i class="fas fa-balance-scale me-1"></i>
                            @endif
                            {{ ucfirst(str_replace('_', ' ', $movement->transaction_type)) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Quantity:</strong></td>
                    <td><strong class="text-sea-primary">{{ $movement->quantity }}</strong> units</td>
                </tr>
                <tr>
                    <td><strong>Stock Change:</strong></td>
                    <td>
                        <span class="text-muted">{{ $movement->previous_stock }}</span> 
                        <i class="fas fa-arrow-right mx-2"></i> 
                        <strong>{{ $movement->current_stock }}</strong>
                    </td>
                </tr>
                <tr>
                    <td><strong>Notes:</strong></td>
                    <td>{{ $movement->notes ?? '—' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
