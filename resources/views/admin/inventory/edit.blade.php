@extends('layouts.admin')

@section('title', 'Edit Movement')
@section('page-title', 'Edit Movement')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.inventory.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-edit"></i> Edit Movement #{{ $movement->movement_id }}
        </h1>
        <a href="{{ route('admin.inventory.show', $movement->movement_id) }}" class="btn-admin-secondary ms-3 position-absolute end-0">
            <i class="fas fa-eye me-1"></i> View
        </a>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body admin-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Product</label>
                    <input class="form-control" value="{{ $movement->product->name }} ({{ $movement->product->sku }})" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <input class="form-control" value="{{ ucfirst(str_replace('_',' ', $movement->transaction_type)) }}" disabled>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input class="form-control" value="{{ $movement->quantity }}" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Prev → Curr</label>
                    <input class="form-control" value="{{ $movement->previous_stock }} → {{ $movement->current_stock }}" disabled>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body admin-card-body">
            <form method="POST" action="{{ route('admin.inventory.update', $movement->movement_id) }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="4" class="form-control" placeholder="Update notes...">{{ old('notes', $movement->notes) }}</textarea>
                    @error('notes')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 mx-auto d-flex justify-content-end gap-2 mt-4">
                    <button type="reset" class="btn-admin-light w-25">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn-admin-secondary w-25">
                        <i class="fas fa-save me-2"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
