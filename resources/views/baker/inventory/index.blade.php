@extends('layouts.baker')

@section('title', 'Inventory Overview')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-muted-sea">
        <i class="fas fa-boxes me-2"></i> Inventory Overview
    </h1>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="kpi-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="kpi-value">{{ $totalProducts }}</div>
            <div class="kpi-label">Total Products</div>
        </div>
    </div>
    
    <div class="col-md-4">
        <a href="{{ route('baker.inventory.index', ['stock_level'=>'low']) }}" class="text-decoration-none">
            <div class="kpi-card" style="cursor:pointer;">
                <div class="kpi-icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="kpi-value">{{ $lowStockCount }}</div>
                <div class="kpi-label">Low Stock Items</div>
            </div>
        </a>
    </div>
    
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="kpi-icon">
            <i class="fas fa-box-open"></i>
            </div>
            <div class="kpi-value">{{ $outOfStockCount }}</div>
            <div class="kpi-label">Out of Stock</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('baker.inventory.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search Products</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       placeholder="Product name or description"
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-md-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="stock_level" class="form-label">Stock Level</label>
                <select class="form-select" id="stock_level" name="stock_level">
                    <option value="">All Levels</option>
                    <option value="low" {{ request('stock_level') === 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="zero" {{ request('stock_level') === 'zero' ? 'selected' : '' }}>Out of Stock</option>
                    <option value="normal" {{ request('stock_level') === 'normal' ? 'selected' : '' }}>Normal Stock</option>
                </select>
            </div>
            
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn-admin-primary">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
                <button type="submit" name="clear" value="1" class="btn-admin-light">
                    <i class="fas fa-times me-1"></i> Clear
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Inventory Table -->
<div class="card">
    <div class="card-body admin-card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-border table-hover">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Reorder Level</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                @if($product->description)
                                    <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                @endif
                            </td>
                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $stock = $product->inventoryStock->quantity ?? 0;
                                    $reorderLevel = $product->inventoryStock->reorder_level ?? 10;
                                @endphp
                                <span class="fw-bold {{ $stock <= $reorderLevel ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($stock) }} units
                                </span>
                            </td>
                            <td>{{ number_format($reorderLevel) }} units</td>
                            <td>₱{{ number_format($product->price, 2) }}</td>
                            <td>
                                @php
                                    if ($stock == 0) {
                                        $statusText = 'Out of Stock';
                                        $statusClass = 'text-danger fw-bold';
                                    } elseif ($stock <= $reorderLevel) {
                                        $statusText = 'Low Stock';
                                        $statusClass = 'text-warning fw-bold';
                                    } else {
                                        $statusText = 'In Stock';
                                        $statusClass = 'text-dark fw-bold';
                                    }
                                @endphp
                                <span class="{{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="button"
                                        class="btn-admin-light btn-sm border border-1 d-flex align-items-center gap-1"
                                        title="View Product"
                                        onclick="window.location='{{ route('baker.inventory.show', $product) }}'">
                                        <i class="fas fa-eye" aria-label="View"></i>
                                        <span class="small text-muted">View</span>
                                    </button>
                                    @if($stock <= $reorderLevel)
                                        <button type="button"
                                            class="btn-admin-primary btn-sm border border-1 d-flex align-items-center gap-1"
                                            title="Create Production Batch"
                                            onclick="window.location='{{ route('baker.production.create') }}?product={{ $product->prod_id }}'">
                                            <i class="fas fa-bread-slice" aria-label="Produce"></i>
                                            <span class="small text-muted">Produce</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links('vendor.pagination.admin') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Products Found</h4>
                <p class="text-muted">No products match your current filter criteria.</p>
                <a href="{{ route('baker.inventory.index') }}" class="btn-admin-primary">
                    <i class="fas fa-refresh me-2"></i> Clear Filters
                </a>
            </div>
        @endif
    </div>
</div>
@endsection