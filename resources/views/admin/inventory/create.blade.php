@extends('layouts.admin')

@section('title', 'Add Stock Movement')
@section('page-title', 'Add Stock Movement')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.inventory.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-plus-circle"></i> Add Stock Movement
        </h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body admin-card-body">
            <form method="POST" action="{{ route('admin.inventory.store') }}" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Product</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">Select product</option>
                        @foreach($products as $p)
                            <option value="{{ $p->prod_id }}" {{ (old('product_id', request('product_id'))==$p->prod_id) ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->sku }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Movement Type</label>
                    <select name="movement_kind" class="form-select" required>
                        <option value="stock_in" {{ old('movement_kind')==='stock_in' ? 'selected' : '' }}>Stock In</option>
                        <option value="stock_out" {{ old('movement_kind')==='stock_out' ? 'selected' : '' }}>Stock Out</option>
                        <option value="adjustment" {{ old('movement_kind')==='adjustment' ? 'selected' : '' }}>Adjustment</option>
                    </select>
                    @error('movement_kind')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" min="1" step="1" name="quantity" class="form-control" value="{{ old('quantity', 1) }}" required>
                    @error('quantity')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-control" placeholder="Optional details...">{{ old('notes') }}</textarea>
                    @error('notes')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 mx-auto d-flex justify-content-end gap-2">
                    <button type="reset" class="btn-admin-light w-25">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn-admin-secondary w-25">
                        <i class="fas fa-save me-2"></i> Save Movement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
