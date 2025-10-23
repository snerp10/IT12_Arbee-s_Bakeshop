@extends('layouts.admin')

@section('title', 'Product Details')
@section('page-title', 'Product Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.products.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-box text-sea-primary"></i> {{ $product->name }}
        </h1>
        <a href="{{ route('admin.products.edit', $product) }}" class="btn-admin-secondary ms-3 position-absolute end-0">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card kpi-card info shadow-sm">
                <div class="card-body admin-card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td><strong>SKU:</strong></td><td><code>{{ $product->sku }}</code></td></tr>
                        <tr><td><strong>Name:</strong></td><td>{{ $product->name }}</td></tr>
                        <tr><td><strong>Category:</strong></td><td>{{ $product->category?->name ?? '-' }}</td></tr>
                        <tr><td><strong>Price:</strong></td><td>{{ number_format($product->price, 2) }} / {{ $product->unit }}</td></tr>
                        <tr><td><strong>Status:</strong></td><td>
                            @if($product->status==='active')
                                <span class="badge-admin-role"><i class="fas fa-check-circle me-1"></i> Active</span>
                            @else
                                <span class="badge-admin-role baker"><i class="fas fa-times-circle me-1"></i> Inactive</span>
                            @endif
                        </td></tr>
                        <tr><td><strong>Description:</strong></td><td>{{ $product->description ?: '—' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card kpi-card primary shadow-sm">
                <div class="card-header p-3 card-header-sea-primary"><h2 class="h6 mb-0"><i class="fas fa-warehouse me-2"></i> Inventory</h2></div>
                <div class="card-body admin-card-body">
                    <div class="row g-3 mb-2">
                        <div class="col-md-4">
                            <div class="bg-sea-light p-3 rounded">
                                <div class="text-muted">On Hand</div>
                                <div class="h4 mb-0">{{ $product->inventoryStock->quantity ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-sea-light p-3 rounded">
                                <div class="text-muted">Reorder Level</div>
                                <div class="h4 mb-0">{{ $product->inventoryStock->reorder_level ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-sea-light p-3 rounded">
                                <div class="text-muted">Status</div>
                                <div class="h4 mb-0">{{ $product->isLowStock() ? 'Low' : 'OK' }}</div>
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-3 mb-2 text-muted">Recent Stock Movements</h6>
                    @if(isset($stockMovements) && $stockMovements->count())
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light"><tr><th>Date</th><th>Type</th><th class="text-end">Qty</th><th>Prev → Curr</th><th>Notes</th></tr></thead>
                                <tbody>
                                    @foreach($stockMovements as $mv)
                                        <tr>
                                            <td>{{ $mv->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ ucfirst($mv->transaction_type) }}</td>
                                            <td class="text-end">{{ $mv->quantity }}</td>
                                            <td>{{ $mv->previous_stock }} → {{ $mv->current_stock }}</td>
                                            <td>{{ $mv->notes }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">No movements yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
