@php
    $mode = $mode ?? 'create';
    $product = $product ?? null;
@endphp

@if ($errors->any())
    <div class="alert-admin-danger mb-3">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Please fix the errors below.
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card kpi-card info shadow-sm">
            <div class="card-header p-3 card-header-sea-light">
                <h2 class="h6 mb-0 text-sea-dark"><i class="fas fa-box me-2"></i> Product Details</h2>
            </div>
            <div class="card-body admin-card-body">
                <div class="mb-3">
                    <label class="form-label" for="sku">SKU</label>
                    <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku', $product->sku ?? '') }}" required>
                    @error('sku')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
                    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
                    @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="category_id">Category</label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            <option value="">Select category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->category_id }}" {{ old('category_id', $product->category_id ?? '') == $cat->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="active" {{ old('status', $product->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $product->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card kpi-card primary shadow-sm">
            <div class="card-header p-3 card-header-sea-primary">
                <h2 class="h6 mb-0"><i class="fas fa-cog me-2"></i> Pricing & Inventory</h2>
            </div>
            <div class="card-body admin-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="price">Price</label>
                        <input type="number" step="0.01" min="0" name="price" id="price" class="form-control" value="{{ old('price', $product->price ?? 0) }}" required>
                        @error('price')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="unit">Unit</label>
                        <input type="text" name="unit" id="unit" class="form-control" value="{{ old('unit', $product->unit ?? '') }}" placeholder="pcs, kg, box" required>
                        @error('unit')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label" for="shelf_life">Shelf Life (days)</label>
                        <input type="number" min="0" name="shelf_life" id="shelf_life" class="form-control" value="{{ old('shelf_life', $product->shelf_life ?? '') }}">
                        @error('shelf_life')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="reorder_level">Reorder Level</label>
                        <input type="number" min="0" name="reorder_level" id="reorder_level" class="form-control" value="{{ old('reorder_level', optional(optional($product)->inventoryStock)->reorder_level ?? 10) }}">
                        @error('reorder_level')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="form-check d-flex align-items-center mt-4">
                            <input class="form-check-input me-2" type="checkbox" name="is_available" id="is_available" value="1" {{ old('is_available', $product->is_available ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label mb-0" for="is_available">Available for Sale</label>
                        </div>
                    </div>
                </div>

                @if($mode === 'create')
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label" for="initial_quantity">Initial Stock</label>
                        <input type="number" min="0" name="initial_quantity" id="initial_quantity" class="form-control" value="{{ old('initial_quantity', 0) }}">
                        @error('initial_quantity')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="col-12 mx-auto d-flex justify-content-end gap-2 mt-4">
    <button type="reset" class="btn-admin-light w-25">
        <i class="fas fa-times me-2"></i> Cancel
    </button>
    <button type="submit" class="btn-admin-secondary w-25">
        <i class="fas fa-save me-2"></i> {{ $mode === 'edit' ? 'Update Product' : 'Create Product' }}
    </button>
</div>
