@extends('layouts.admin')
@section('title', 'Sales Management')
@section('page-title', 'Sales Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-muted-sea">
            <i class="fas fa-cash-register"></i> Sales Management
        </h1>
        <a href="{{ route('admin.sales.create') }}" class="btn-admin-secondary">
            <i class="fas fa-plus me-2"></i> New Sale
        </a>
    </div>

    <!-- Filters -->
    <div class="card admin-filter-card shadow mb-4">
        <div class="card-body admin-card-body">
            <form method="GET" action="{{ route('admin.sales.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Status</option>
                        @foreach(['completed','cancelled','refunded'] as $s)
                            <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Sale number or notes..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn-admin-secondary">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <button type="reset" class="btn-admin-light" onclick="window.location='{{ route('admin.sales.index') }}'">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card kpi-card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-sea-primary">Sales ({{ $sales->total() }})</h6>
        </div>
        <div class="card-body admin-card-body">
            @if($sales->count())
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th class="text-end">Items</th>
                                <th class="text-end">Total</th>
                                <th>Status</th>
                                <th width="220" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $s)
                                <tr>
                                    <td><a class="text-decoration-none" href="{{ route('admin.sales.show', $s) }}">{{ $s->order_number }}</a></td>
                                    <td>{{ $s->order_date?->format('Y-m-d') }}</td>
                                    <td class="text-end">{{ $s->items->sum('quantity') }}</td>
                                    <td class="text-end">₱{{ number_format($s->total_amount, 2) }}</td>
                                    <td>
                                        @switch($s->status)
                                            @case('completed')
                                                <span class="badge-admin-role baker" style="background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%) !important; color: #fff !important;">
                                                    <i class="fas fa-check-circle me-1"></i> Completed
                                                </span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge-admin-role baker"><i class="fas fa-times-circle me-1"></i> Cancelled</span>
                                                @break
                                            @case('refunded')
                                                <span class="badge-admin-role manager"><i class="fas fa-undo me-1"></i> Refunded</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            <button type="button" class="btn-admin-light btn-sm border border-1 d-flex align-items-center gap-1" 
                                                    onclick="window.location='{{ route('admin.sales.show', $s) }}'" 
                                                    title="View">
                                                <i class="fas fa-eye" aria-label="View"></i>
                                                <span class="small text-muted">View</span>
                                            </button>
                                            @if($s->status === 'completed')
                                                <button type="button" class="btn-admin-secondary btn-sm border border-1 d-flex align-items-center gap-1" 
                                                        onclick="window.location='{{ route('admin.sales.edit', $s) }}'" 
                                                        title="Edit">
                                                    <i class="fas fa-edit" aria-label="Edit"></i>
                                                    <span class="small text-muted">Edit</span>
                                                </button>
                                            @endif
                                            @if($s->status !== 'cancelled')
                                                <button type="button" class="btn-admin-info btn-sm border border-1 d-flex align-items-center gap-1" 
                                                        title="Cancel"
                                                        onclick="openCancelModal(this)"
                                                        data-order="{{ $s->order_number }}"
                                                        data-total="{{ number_format($s->total_amount, 2) }}"
                                                        data-action="{{ route('admin.sales.cancel', $s) }}"> 
                                                    <i class="fas fa-ban" aria-label="Cancel"></i>
                                                    <span class="small text-muted">Cancel</span>
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
                    <div>Showing {{ $sales->firstItem() }} to {{ $sales->lastItem() }} of {{ $sales->total() }}</div>
                    {{ $sales->appends(request()->query())->links('vendor.pagination.admin') }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-cash-register fa-3x text-gray-300 mb-3"></i>
                    <h5>No sales found</h5>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Sale Modal -->
<div id="cancelSaleModal" class="sea-modal" aria-hidden="true">
    <div class="sea-modal-dialog">
        <div class="sea-modal-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 flex-grow-1" style="flex-basis:80%;max-width:80%;">
                <i class="fas fa-ban me-2"></i> Confirm Cancellation - <span id="cancelSaleOrder"></span>
            </h5>
            <div style="flex-basis:10%;max-width:10%;" class="text-end">
                <button type="button" class="btn-admin-light btn-sm" onclick="closeModal('cancelSaleModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="sea-modal-body">
            <form id="cancelSaleForm" method="POST" action="#">
                @csrf
                @method('PATCH')
                <p>
                    Are you sure you want to cancel sale <strong id="cancelSaleOrderNumber"></strong> 
                    with total amount of <strong>₱<span id="cancelSaleTotal"></span></strong>? 
                    This will restore the product stock. This action cannot be undone.
                </p>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn-admin-light me-2" onclick="closeModal('cancelSaleModal')">Cancel</button>
                    <button type="submit" class="btn-admin-primary">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openCancelModal(button) {
    const order = button.getAttribute('data-order');
    const total = button.getAttribute('data-total');
    const action = button.getAttribute('data-action');
    
    document.getElementById('cancelSaleOrder').textContent = order;
    document.getElementById('cancelSaleOrderNumber').textContent = order;
    document.getElementById('cancelSaleTotal').textContent = total;
    document.getElementById('cancelSaleForm').setAttribute('action', action);
    
    document.getElementById('cancelSaleModal').classList.add('show');
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
