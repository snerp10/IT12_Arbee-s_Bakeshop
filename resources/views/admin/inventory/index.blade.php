@extends('layouts.admin')

@section('title', 'Inventory Management')
@section('page-title', 'Inventory Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-muted-sea">
            <i class="fas fa-warehouse"></i> Inventory Management
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.inventory.create') }}" class="btn-admin-secondary me-2">
                <i class="fas fa-plus me-2"></i> Add Movement
            </a>
            <a href="{{ route('admin.inventory.low-stock-alerts') }}" class="btn-admin-light">
                <i class="fas fa-exclamation-triangle me-2"></i> Low Stock Alerts
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card admin-filter-card shadow mb-4">
        <div class="card-body admin-card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Product name or SKU">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Product</label>
                    <select name="product_id" class="form-select">
                        <option value="">All</option>
                        @foreach($products as $p)
                            <option value="{{ $p->prod_id }}" {{ request('product_id')==$p->prod_id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->sku }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All</option>
                        <option value="stock_in" {{ request('type')==='stock_in' ? 'selected' : '' }}>Stock In</option>
                        <option value="stock_out" {{ request('type')==='stock_out' ? 'selected' : '' }}>Stock Out</option>
                        <option value="adjustment" {{ request('type')==='adjustment' ? 'selected' : '' }}>Adjustment</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn-admin-secondary me-2">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                    <button type="button" onclick="window.location='{{ route('admin.inventory.index') }}'" class="btn-admin-light">
                        <i class="fas fa-undo me-2"></i>Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Movements Table -->
    <div class="card kpi-card shadow">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-sea-primary"><i class="fas fa-list me-2"></i> Stock Movements ({{ $movements->total() }})</h6>
        </div>
        <div class="card-body admin-card-body">
            @if($movements->count() === 0)
                <div class="text-center text-muted py-5">
                    <i class="fas fa-warehouse fa-3x mb-3 d-block"></i>
                    No movements found.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th class="text-end">Qty</th>
                                <th class="text-center">Prev → Curr</th>
                                <th>Notes</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movements as $mv)
                                <tr>
                                    <td>{{ $mv->created_at->format('Y-m-d H:i') }}</td>
                                    <td><a href="{{ route('admin.products.show', $mv->product) }}" class="text-sea-dark text-decoration-none"><i class="fas fa-box text-sea-primary me-2"></i>{{ $mv->product->name }}</a></td>
                                    <td>
                                        @switch($mv->transaction_type)
                                            @case('stock_in')<span class="badge-chip bg-sea-light"><i class="fas fa-arrow-down me-1"></i> In</span>@break
                                            @case('stock_out')<span class="badge-chip bg-sea-light"><i class="fas fa-arrow-up me-1"></i> Out</span>@break
                                            @default <span class="badge-chip bg-sea-light"><i class="fas fa-balance-scale me-1"></i> Adj</span>
                                        @endswitch
                                    </td>
                                    <td class="text-end">{{ $mv->quantity }}</td>
                                    <td class="text-center">{{ $mv->previous_stock }} → {{ $mv->current_stock }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($mv->notes, 80) }}</td>
                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            <button type="button" class="btn-admin-light btn-sm border border-1 d-flex align-items-center gap-1" title="View" onclick="window.location='{{ route('admin.inventory.show', $mv->movement_id) }}'">
                                                <i class="fas fa-eye" aria-label="View"></i>
                                                <span class="small text-muted">View</span>
                                            </button>
                                            <button type="button" class="btn-admin-secondary btn-sm border border-1 d-flex align-items-center gap-1" title="Edit" onclick="window.location='{{ route('admin.inventory.edit', $mv->movement_id) }}'">
                                                <i class="fas fa-edit" aria-label="Edit"></i>
                                                <span class="small text-muted">Edit</span>
                                            </button>
                                            <button type="button" class="btn-admin-delete btn-sm border border-1 d-flex align-items-center gap-1" title="Delete"
                                            data-action="{{ route('admin.inventory.destroy', $mv->movement_id) }}"
                                                    onclick="openDeleteInvModal(this)">
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
                <div class="d-flex justify-content-end">
                    {{ $movements->appends(request()->query())->links('vendor.pagination.admin') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteInvModal" class="sea-modal" aria-hidden="true">
        <div class="sea-modal-dialog">
            <div class="sea-modal-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 flex-grow-1" style="flex-basis:80%;max-width:80%;">
                    <i class="fas fa-trash me-2"></i> Confirm Deletion - <span id="deleteMovementProduct"></span>
                </h5>
                <div style="flex-basis:10%;max-width:10%;" class="text-end">
                    <button type="button" class="btn-admin-light btn-sm" onclick="closeModal('deleteInvModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="sea-modal-body">
                <form id="deleteInvForm" method="POST" action="#">
                    @csrf
                    @method('DELETE')
                    <p>
                        Are you sure you want to delete <strong id="deleteMovementProductName"></strong>? Deleting a movement will revert the product stock to its previous value. This action cannot be undone.
                    </p>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn-admin-light me-2" onclick="closeModal('deleteInvModal')">Cancel</button>
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
function openDeleteInvModal(btn){
    const modal = document.getElementById('deleteInvModal');
    const form = document.getElementById('deleteInvForm');
    form.action = btn.dataset.action;
    modal.classList.add('show');
}
function closeModal(id){ document.getElementById(id).classList.remove('show'); }
document.querySelectorAll('.sea-modal').forEach(m => {
    m.addEventListener('click', e => { if(e.target === m){ m.classList.remove('show'); } });
});
</script>
@endpush