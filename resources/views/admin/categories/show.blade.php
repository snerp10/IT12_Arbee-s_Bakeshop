@extends('layouts.admin')

@section('title', 'Category Details')
@section('page-title', 'Category Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.categories.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-tag"></i> {{ $category->name }}
        </h1>
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn-admin-secondary ms-3 position-absolute end-0">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card kpi-card info shadow-sm">
                <div class="card-body admin-card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td><strong>Status:</strong></td><td>
                            @if($category->status==='active')
                                <span class="badge-admin-role"><i class="fas fa-check-circle me-1"></i> Active</span>
                            @else
                                <span class="badge-admin-role baker"><i class="fas fa-times-circle me-1"></i> Inactive</span>
                            @endif
                        </td></tr>
                        <tr><td><strong>Description:</strong></td><td>{{ $category->description ?: '—' }}</td></tr>
                        <tr><td><strong>Products:</strong></td><td>{{ $category->products->count() }}</td></tr>
                        <tr><td><strong>Created:</strong></td><td>{{ $category->created_at->format('Y-m-d') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card kpi-card info shadow-sm">
                <div class="card-header p-3 card-header-sea-secondary"><h2 class="h6 mb-0"><i class="fas fa-box me-2"></i> Products in this Category</h2></div>
                <div class="card-body admin-card-body">
                    @if($category->products->count())
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light"><tr><th>SKU</th><th>Name</th><th class="text-end">Price</th><th class="text-end">Stock</th></tr></thead>
                                <tbody>
                                    @foreach($category->products as $p)
                                        <tr>
                                            <td><code>{{ $p->sku }}</code></td>
                                            <td><a href="{{ route('admin.products.show', $p) }}" class="text-sea-dark text-decoration-none">{{ $p->name }}</a></td>
                                            <td class="text-end">{{ number_format($p->price, 2) }}</td>
                                            <td class="text-end">{{ $p->stock_quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">No products in this category.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection