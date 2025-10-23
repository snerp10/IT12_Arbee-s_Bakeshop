@extends('layouts.admin')

@section('title', 'Product Management')
@section('page-title', 'Product Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-muted-sea">
            <i class="fas fa-boxes"></i> Product Management
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.products.create') }}" class="btn-admin-secondary me-2">
                <i class="fas fa-plus me-2"></i> Add Product
            </a>
            <a href="{{ route('admin.products.export') }}" class="btn-admin-light">
                <i class="fas fa-file-pdf me-2"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card kpi-card info shadow-sm">
                <div class="card-body admin-card-body d-flex justify-content-between align-items-center">
                    <i class="fas fa-box text-sea-primary fa-2x"></i>
                    <div class="text-end ms-3 flex-grow-1">
                        <div class="h4 mb-0">{{ \App\Models\Product::count() }}</div>
                        <div class="text-muted">Total Products</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card info shadow-sm">
                <div class="card-body admin-card-body d-flex justify-content-between align-items-center">
                    <i class="fas fa-check-circle text-sea-primary fa-2x"></i>
                    <div class="text-end ms-3 flex-grow-1">
                        <div class="h4 mb-0">{{ \App\Models\Product::where('status','active')->count() }}</div>
                        <div class="text-muted">Active Products</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card info shadow-sm">
                <div class="card-body admin-card-body d-flex justify-content-between align-items-center">
                    <i class="fas fa-tags text-sea-primary fa-2x"></i>
                    <div class="text-end ms-3 flex-grow-1">
                        <div class="h4 mb-0">{{ \App\Models\Category::count() }}</div>
                        <div class="text-muted">Categories</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-3">
        <div class="card-body admin-card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name or SKU">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="active" {{ request('status')==='active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn-admin-secondary me-2"><i class="fas fa-search me-2"></i>Filter</button>
                    <button type="button" class="btn-admin-light" onclick="resetProductFilters(this.form)"><i class="fas fa-times me-2"></i>Reset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow-sm">
        <div class="card-header p-3 d-flex align-items-center justify-content-between card-header-sea-light">
            <h2 class="h6 mb-0 text-sea-dark"><i class="fas fa-box me-2"></i> Products Inventory</h2>
        </div>
        <div class="card-body admin-card-body">
            @if($products->count() === 0)
                <div class="text-center text-muted py-5">
                    <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                    No products found.
                </div>
            @else
                <form id="bulkForm" method="POST" action="{{ route('admin.products.bulk-update') }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:28px"><input type="checkbox" onclick="document.querySelectorAll('.select-product').forEach(cb=>cb.checked=this.checked)"></th>
                                    <th>SKU</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th class="text-end">Price</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td><input type="checkbox" name="product_ids[]" class="select-product" value="{{ $product->prod_id }}"></td>
                                        <td><code>{{ $product->sku }}</code></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-box text-sea-primary me-2"></i>
                                                <a href="{{ route('admin.products.show', $product) }}" class="text-sea-dark text-decoration-none">{{ $product->name }}</a>
                                                @if($product->isLowStock())
                                                    <span class="badge-chip bg-sea-light ms-2"><i class="fas fa-exclamation-triangle me-1"></i> Low</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $product->category?->name ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($product->price, 2) }}</td>
                                        
                                        <td>
                                            @if($product->status === 'active')
                                                <span class="badge-admin-role" style="background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%) !important; color: #fff !important;">
                                                    <i class="fas fa-check-circle me-1" aria-label="Active"></i> Active
                                                </span>
                                            @else
                                                <span class="badge-admin-role baker"><i class="fas fa-times-circle me-1" aria-label="Inactive"></i> Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 align-items-center">
                                                <button type="button" class="btn-admin-light btn-sm border border-1 d-flex align-items-center gap-1" title="View" onclick="window.location='{{ route('admin.products.show', $product) }}'">
                                                    <i class="fas fa-eye" aria-label="View"></i>
                                                    <span class="small text-muted">View</span>
                                                </button>
                                                <button type="button" class="btn-admin-secondary btn-sm border border-1 d-flex align-items-center gap-1" title="Edit" onclick="window.location='{{ route('admin.products.edit', $product) }}'">
                                                    <i class="fas fa-edit" aria-label="Edit"></i>
                                                    <span class="small text-muted">Edit</span>
                                                </button>
                                                <button type="button" class="btn-admin-warning btn-sm border border-1 d-flex align-items-center gap-1" title="Adjust Stock"
                                                        data-action="{{ route('admin.products.adjust-stock', $product) }}"
                                                        data-name="{{ $product->name }}"
                                                        onclick="openAdjustModal(this)">
                                                    <i class="fas fa-balance-scale" aria-label="Adjust Stock"></i>
                                                    <span class="small text-muted">Stock</span>
                                                </button>
                                                <button type="button" class="btn-admin-delete btn-sm border border-1 d-flex align-items-center gap-1" title="Delete"
                                                        data-action="{{ route('admin.products.destroy', $product) }}"
                                                        data-name="{{ $product->name }}"
                                                        onclick="openDeleteModal(this)">
                                                    <i class="fas fa-trash" aria-label="Delete"></i>
                                                    <span class="small text-white">Delete</span>
                                                </button>
                                            </div>
                                                
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="d-flex align-items-center">
                            <select name="action" class="form-select me-2" style="width:auto">
                                <option value="activate">Activate</option>
                                <option value="deactivate">Deactivate</option>
                                <option value="delete">Delete</option>
                            </select>
                            <button type="submit" class="btn-admin-secondary" id="applySelectedBtn">Apply to selected</button>
                        </div>
                        {{ $products->appends(request()->query())->links('vendor.pagination.admin') }}
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Global Modals -->
    <!-- Adjust Stock Modal -->
    <div id="adjustModal" class="sea-modal" aria-hidden="true">
        <div class="sea-modal-dialog">
            <div class="sea-modal-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 flex-grow-1" style="flex-basis:80%;max-width:80%;">
                    <i class="fas fa-balance-scale me-2"></i> Adjust Stock - <span id="adjustProductName"></span>
                </h5>
                <div style="flex-basis:10%;max-width:10%;" class="text-end">
                    <button type="button" class="btn-admin-light btn-sm" onclick="closeModal('adjustModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="sea-modal-body">
                <form id="adjustForm" method="POST" action="#">
                    @csrf
                    <div class="row g-2">
                        <div class="col-4">
                            <label class="form-label">Type</label>
                            <select name="adjustment_type" class="form-select">
                                <option value="increase">Increase</option>
                                <option value="decrease">Decrease</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" min="1" class="form-control" placeholder="Qty">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reason</label>
                            <input type="text" name="reason" class="form-control" placeholder="Reason" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Notes (optional)"></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn-admin-light me-2" onclick="closeModal('adjustModal')">Cancel</button>
                        <button type="submit" class="btn-admin-primary">Apply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="sea-modal" aria-hidden="true">
        <div class="sea-modal-dialog">
            <div class="sea-modal-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 flex-grow-1" style="flex-basis:80%;max-width:80%;">
                    <i class="fas fa-trash me-2"></i> Confirm Deletion - <span id="deleteProductName"></span>
                </h5>
                <div style="flex-basis:10%;max-width:10%;" class="text-end">
                    <button type="button" class="btn-admin-light btn-sm" onclick="closeModal('deleteModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="sea-modal-body">
                <form id="deleteForm" method="POST" action="#">
                    @csrf
                    @method('DELETE')
                    <p>Are you sure you want to delete <strong id="deleteProductName"></strong>? This action cannot be undone.</p>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn-admin-light me-2" onclick="closeModal('deleteModal')">Cancel</button>
                        <button type="submit" class="btn-admin-primary">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function resetProductFilters(form){
    Array.from(form.elements).forEach(el => {
        if (el.tagName === 'INPUT' && (el.type === 'text' || el.type === 'number')) el.value = '';
        if (el.tagName === 'SELECT') el.selectedIndex = 0;
    });
    form.submit();
}
function openAdjustModal(btn){
    const modal = document.getElementById('adjustModal');
    document.getElementById('adjustProductName').textContent = btn.dataset.name;
    const form = document.getElementById('adjustForm');
    form.action = btn.dataset.action;
    modal.classList.add('show');
}
function openDeleteModal(btn){
    const modal = document.getElementById('deleteModal');
    document.getElementById('deleteProductName').textContent = btn.dataset.name;
    const form = document.getElementById('deleteForm');
    form.action = btn.dataset.action;
    modal.classList.add('show');
}
function closeModal(id){
    document.getElementById(id).classList.remove('show');
}
// Close when clicking outside dialog
document.querySelectorAll('.sea-modal').forEach(m => {
    m.addEventListener('click', e => {
        if(e.target === m){ m.classList.remove('show'); }
    });
});

// Bulk apply guard
document.getElementById('bulkForm')?.addEventListener('submit', function(e){
    const checked = this.querySelectorAll('.select-product:checked');
    if(checked.length === 0){
        e.preventDefault();
        alert('Please select at least one product.');
        return false;
    }
});
</script>
@endpush