@extends('layouts.admin')
@section('title', 'Production Orders')
@section('page-title', 'Production Orders')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-muted-sea">
            <i class="fas fa-industry"></i> Production Orders
        </h1>
        <a href="{{ route('admin.productions.create') }}" class="btn-admin-secondary">
            <i class="fas fa-plus me-2"></i> New Production
        </a>
    </div>

    <div class="card admin-filter-card shadow mb-4">
        <div class="card-body admin-card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(['pending','in_progress','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Product</label>
                    <select name="prod_id" class="form-select">
                        <option value="">All products</option>
                        @foreach($products as $p)
                            <option value="{{ $p->prod_id }}" {{ request('prod_id')==$p->prod_id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->sku }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Baker</label>
                    <select name="baker_id" class="form-select">
                        <option value="">All bakers</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->emp_id }}" {{ request('baker_id')==$emp->emp_id ? 'selected' : '' }}>{{ $emp->full_name ?? ($emp->first_name.' '.$emp->last_name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Batch # or notes">
                </div>
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn-admin-secondary me-2">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <button type="reset" class="btn-admin-light me-2" onclick="window.location='{{ route('admin.productions.index') }}'">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card kpi-card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-sea-primary">Production Batches ({{ $productions->total() }})</h6>
        </div>
        <div class="card-body admin-card-body">
            @if($productions->count())
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Batch #</th>
                                <th>Product</th>
                                <th class="text-end">Quantity</th>
                                <th>Baker</th>
                                <th>Produced</th>
                                <th>Expires</th>
                                <th>Status</th>
                                <th width="240" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productions as $production)
                                <tr>
                                    <td><a href="{{ route('admin.productions.show', $production) }}" class="text-decoration-none">{{ $production->batch_number }}</a></td>
                                    <td>{{ $production->product?->name }} <small class="text-muted">({{ $production->product?->sku }})</small></td>
                                    <td class="text-end">{{ number_format($production->quantity_produced) }}</td>
                                    <td>{{ $production->baker?->full_name ?? ($production->baker?->first_name.' '.$production->baker?->last_name) }}</td>
                                    <td>{{ $production->production_date?->format('Y-m-d') }}</td>
                                    <td>
                                        @if($production->expiration_date)
                                            {{ $production->expiration_date->format('Y-m-d') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $status = $production->status;
                                            $statusMap = [
                                                'pending' => ['label' => 'Pending', 'icon' => 'hourglass-half', 'class' => 'admin', 'time' => $production->pending_at],
                                                'in_progress' => ['label' => 'In Progress', 'icon' => 'cogs', 'class' => 'baker', 'time' => $production->in_progress_at],
                                                'completed' => ['label' => 'Completed', 'icon' => 'check-circle', 'class' => 'manager', 'time' => $production->completed_at],
                                                'cancelled' => ['label' => 'Cancelled', 'icon' => 'times-circle', 'class' => '', 'time' => $production->updated_at],
                                            ];
                                            $current = $statusMap[$status] ?? null;
                                        @endphp
                                        @if($current)
                                            <span class="badge-admin-role {{ $current['class'] }}">
                                                <i class="fas fa-{{ $current['icon'] }} me-1"></i> {{ $current['label'] }}
                                            </span>
                                            @if($current['time'])
                                                <span class="text-muted small ms-2">({{ $current['time']->format('M d, Y H:i') }})</span>
                                            @endif
                                        @endif
                                        <div class="mt-1" style="max-width:120px;">
                                            <div class="progress" style="height: 6px;">
                                                @php
                                                    $progress = 0;
                                                    if($status==='pending') $progress=20;
                                                    elseif($status==='in_progress') $progress=60;
                                                    elseif($status==='completed') $progress=100;
                                                    elseif($status==='cancelled') $progress=0;
                                                @endphp
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            <button type="button" class="btn-admin-light btn-sm border border-1 d-flex align-items-center gap-1" 
                                                    onclick="window.location='{{ route('admin.productions.show', $production) }}'" 
                                                    title="View">
                                                <i class="fas fa-eye" aria-label="View"></i>
                                                <span class="small text-muted">View</span>
                                            </button>
                                            <button type="button" class="btn-admin-secondary btn-sm border border-1 d-flex align-items-center gap-1" 
                                                    onclick="window.location='{{ route('admin.productions.edit', $production) }}'" 
                                                    title="Edit">
                                                <i class="fas fa-edit" aria-label="Edit"></i>
                                                <span class="small text-muted">Edit</span>
                                            </button>
                                            @if($production->status !== 'completed')
                                                <form action="{{ route('admin.productions.cancel', $production) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn-admin-info btn-sm border border-1 d-flex align-items-center gap-1" title="Cancel Production">
                                                        <i class="fas fa-ban" aria-label="Cancel"></i>
                                                        <span class="small text-muted">Cancel</span>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($production->status !== 'completed')
                                                <button type="button" class="btn-admin-delete btn-sm border border-1 d-flex align-items-center gap-1" 
                                                        onclick="openDeleteModal(this)"
                                                        data-batch="{{ $production->batch_number }}"
                                                        data-product="{{ $production->product->name ?? 'N/A' }}"
                                                        data-action="{{ route('admin.productions.destroy', $production) }}" 
                                                        title="Delete">
                                                    <i class="fas fa-trash" aria-label="Delete"></i>
                                                    <span class="small text-white">Delete</span>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>Showing {{ $productions->firstItem() }} to {{ $productions->lastItem() }} of {{ $productions->total() }}</div>
                    {{ $productions->appends(request()->query())->links('vendor.pagination.admin') }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-industry fa-3x text-gray-300 mb-3"></i>
                    <h5>No production orders found</h5>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Production Modal -->
<div id="deleteProductionModal" class="sea-modal" aria-hidden="true">
    <div class="sea-modal-dialog">
        <div class="sea-modal-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 flex-grow-1" style="flex-basis:80%;max-width:80%;">
                <i class="fas fa-trash me-2"></i> Confirm Deletion - <span id="deleteProductionBatch"></span>
            </h5>
            <div style="flex-basis:10%;max-width:10%;" class="text-end">
                <button type="button" class="btn-admin-light btn-sm" onclick="closeModal('deleteProductionModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="sea-modal-body">
            <form id="deleteProductionForm" method="POST" action="#">
                @csrf
                @method('DELETE')
                <p>
                    Are you sure you want to delete production batch <strong id="deleteProductionBatchName"></strong> 
                    for product <strong id="deleteProductionProduct"></strong>? 
                    This will revert the inventory stock changes. This action cannot be undone.
                </p>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn-admin-light me-2" onclick="closeModal('deleteProductionModal')">Cancel</button>
                    <button type="submit" class="btn-admin-primary">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openDeleteModal(button) {
    const batch = button.getAttribute('data-batch');
    const product = button.getAttribute('data-product');
    const action = button.getAttribute('data-action');
    
    document.getElementById('deleteProductionBatch').textContent = batch;
    document.getElementById('deleteProductionBatchName').textContent = batch;
    document.getElementById('deleteProductionProduct').textContent = product;
    document.getElementById('deleteProductionForm').setAttribute('action', action);
    
    document.getElementById('deleteProductionModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
    document.body.style.overflow = '';
}

// Close modal on backdrop click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('sea-modal')) {
        closeModal(e.target.id);
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const openModal = document.querySelector('.sea-modal.show');
        if (openModal) {
            closeModal(openModal.id);
        }
    }
});
</script>
@endpush
@endsection
