@extends('layouts.admin')

@section('title', 'Low Stock Alerts')
@section('page-title', 'Low Stock Alerts')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <div>
            <a href="{{ route('admin.inventory.index') }}" class="btn-admin-light me-3">
                <i class="fas fa-arrow-left me-2"></i> Back to Movements
            </a>
        </div>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
        </h1>
        <div style="width:120px"></div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body admin-card-body">
            @if($products->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="fas fa-check-circle fa-3x mb-3 d-block"></i>
                    All products are above minimum stock.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-end">On Hand</th>
                                <th class="text-end">Minimum</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $p)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.products.show', $p) }}" class="text-decoration-none text-sea-dark">
                                            <i class="fas fa-box me-2 text-sea-primary"></i>{{ $p->name }} ({{ $p->sku }})
                                        </a>
                                    </td>
                                    <td class="text-end">{{ optional($p->inventoryStock)->quantity ?? 0 }}</td>
                                    <td class="text-end">{{ optional($p->inventoryStock)->reorder_level ?? 0 }}</td>
                                    <td>
                                        <button type="button" onclick="location.href='{{ route('admin.inventory.create', ['product_id' => $p->prod_id]) }}'" class="btn-admin-secondary btn-sm">
                                            <i class="fas fa-plus me-1"></i> Add Stock
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
