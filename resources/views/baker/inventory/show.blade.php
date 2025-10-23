@extends('layouts.baker')

@section('title', 'Product Inventory Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('baker.inventory.index') }}" class="btn-admin-light">
            <i class="fas fa-arrow-left me-2"></i> Back to Inventory
        </a>
    </div>
    <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
        <i class="fas fa-box me-2"></i> {{ $product->name }}
    </h1>
    <div>
        <a href="{{ route('baker.production.create') }}?product={{ $product->prod_id }}" class="btn-admin-secondary">
            <i class="fas fa-industry me-2"></i> Create Production
        </a>
    </div>
</div>

<div class="row">
    <!-- Product Information -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i> Product Information
                </h5>
            </div>
            <div class="card-body admin-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Product Name:</strong></td>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Category:</strong></td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Price:</strong></td>
                                <td>₱{{ number_format($product->price, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Product ID:</strong></td>
                                <td>{{ $product->prod_id }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if($product->description)
                        <div>
                            <strong>Description:</strong>
                            <p class="mt-2">{{ $product->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Production History -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i> Recent Production History
                </h5>
                <a href="{{ route('baker.production.create') }}?product={{ $product->prod_id }}" class="btn-admin-secondary btn-sm">
                    <i class="fas fa-plus me-1"></i> New Batch
                </a>
            </div>
            <div class="card-body admin-card-body">
                @if($recentProductions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Batch Number</th>
                                    <th>Quantity</th>
                                    <th>Production Date</th>
                                    <th>Baker</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentProductions as $production)
                                <tr>
                                    <td>
                                        <a href="{{ route('baker.production.show', $production) }}" class="text-decoration-none">
                                            <strong>{{ $production->batch_number }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ number_format($production->quantity_produced) }} pcs</td>
                                    <td>{{ $production->production_date->format('M d, Y') }}</td>
                                    <td>{{ $production->baker->first_name ?? 'N/A' }} {{ $production->baker->last_name ?? '' }}</td>
                                    <td>
                                        <span class="badge-status {{ $production->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $production->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-industry fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No production history for this product yet.</p>
                        <a href="{{ route('baker.production.create') }}?product={{ $product->prod_id }}" class="btn-admin-secondary">
                            <i class="fas fa-plus me-2"></i> Create First Batch
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Stock Information Sidebar -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-warehouse me-2"></i> Stock Information
                </h5>
            </div>
            <div class="card-body admin-card-body">
                @php
                    $stock = $product->inventoryStock;
                    $currentStock = $stock->quantity ?? 0;
                    $reorderLevel = $stock->reorder_level ?? 10;
                    $lastUpdated = $stock->last_counted_at ?? null;
                @endphp
                
                <div class="text-center mb-4">
                    <div class="kpi-card">
                        <div class="kpi-icon {{ $currentStock <= $reorderLevel ? 'text-danger' : 'text-success' }}">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="kpi-value">{{ number_format($currentStock) }}</div>
                        <div class="kpi-label">Current Stock</div>
                    </div>
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Reorder Level:</strong></td>
                        <td>{{ number_format($reorderLevel) }} units</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            @if($currentStock == 0)
                                <span class="badge-status cancelled">Out of Stock</span>
                            @elseif($currentStock <= $reorderLevel)
                                <span class="badge-status pending">Low Stock</span>
                            @else
                                <span class="badge-status completed">In Stock</span>
                            @endif
                        </td>
                    </tr>
                    @if($lastUpdated)
                    <tr>
                        <td><strong>Last Updated:</strong></td>
                        <td>{{ $lastUpdated->format('M d, Y') }}</td>
                    </tr>
                    @endif
                </table>
                
                @if($currentStock <= $reorderLevel)
                <div class="alert-admin-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Reorder Alert!</strong><br>
                    This product is {{ $currentStock == 0 ? 'out of stock' : 'running low' }}. Consider creating a new production batch.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection