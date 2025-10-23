@extends('layouts.baker')

@section('title', 'Production Management')

@section('content')
@if(request('date_from') || request('date_to'))
<div class="alert alert-info mb-3">
    <strong>Filter:</strong>
    @if(request('date_from'))
        From <span class="badge bg-primary">{{ request('date_from') }}</span>
    @endif
    @if(request('date_to'))
        To <span class="badge bg-primary">{{ request('date_to') }}</span>
    @endif
</div>
@endif
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0 text-muted-sea">
        <i class="fas fa-industry me-1"></i> Production Management
    </h1>
    <a href="{{ route('baker.production.create') }}" class="btn-admin-secondary">
        <i class="fas fa-plus me-2"></i> New Production Batch
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body admin-card-body">
        <form method="GET" action="{{ route('baker.production.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       placeholder="Batch number or product name"
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" 
                       class="form-control" 
                       id="date_from" 
                       name="date_from" 
                       value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" 
                       class="form-control" 
                       id="date_to" 
                       name="date_to" 
                       value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn-admin-primary">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
                <button type="button" class="btn-admin-light" onclick="window.location='{{ route('baker.production.index') }}'">
                    <i class="fas fa-times me-1"></i> Clear
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Production Batches Table -->
<div class="card">
    <div class="card-body admin-card-body">
        @if($productions->count() > 0)
            <div class="table-responsive">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th>Batch Number</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Production Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productions as $production)
                        <tr>
                            <td>
                                <strong>{{ $production->batch_number }}</strong>
                            </td>
                            <td>{{ $production->product->name ?? 'N/A' }}</td>
                            <td>{{ number_format($production->quantity_produced) }} pcs</td>
                            <td>
                                @php $dateVal = $production->production_date; @endphp
                                @if(is_object($dateVal) && method_exists($dateVal, 'format'))
                                    {{ $dateVal->format('M d, Y') }}
                                @elseif(is_string($dateVal) && strtotime($dateVal))
                                    {{ date('M d, Y', strtotime($dateVal)) }}
                                @else
                                    {{ $dateVal }}
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
                                    <button type="button"
                                        class="btn-admin-light btn-sm border border-1 d-flex align-items-center gap-1"
                                        title="View Details"
                                        onclick="window.location='{{ route('baker.production.show', $production) }}'">
                                        <i class="fas fa-eye" aria-label="View"></i>
                                        <span class="small text-muted">View</span>
                                    </button>
                                    <button type="button"
                                        class="btn-admin-secondary btn-sm border border-1 d-flex align-items-center gap-1"
                                        title="Edit"
                                        onclick="window.location='{{ route('baker.production.edit', $production) }}'">
                                        <i class="fas fa-edit" aria-label="Edit"></i>
                                        <span class="small text-muted">Edit</span>
                                    </button>
                                    <button type="button"
                                        class="btn-admin-delete btn-sm border border-1 d-flex align-items-center gap-1"
                                        onclick="openDeleteModal(this)"
                                        data-name="Batch #{{ $production->batch_number }}"
                                        data-id="{{ $production->batch_id }}"
                                        data-action="{{ route('baker.production.destroy', $production) }}"
                                        title="Delete">
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
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $productions->links('vendor.pagination.admin') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-industry fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Production Batches Found</h4>
                <p class="text-muted">Start by creating your first production batch to track your baking activities.</p>
                <a href="{{ route('baker.production.create') }}" class="btn-admin-secondary">
                    <i class="fas fa-plus me-2"></i> Create First Batch
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Sea-style Delete Modal -->
<div id="deleteModal" class="sea-modal">
    <div class="sea-modal-content">
        <div class="sea-modal-header">
            <h5 class="sea-modal-title">
                <i class="fas fa-trash-alt me-2"></i> Delete Production Batch
            </h5>
        </div>
        <div class="sea-modal-body">
            <div class="alert-admin-danger mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Warning:</strong> This action cannot be undone!
            </div>
            <p>Are you sure you want to delete <strong id="deleteItemName"></strong>?</p>
            <p class="text-muted small mb-0">
                <i class="fas fa-info-circle me-1"></i> 
                This will permanently remove the production batch from the system.
            </p>
        </div>
        <div class="sea-modal-footer">
            <button type="button" class="btn-admin-light" onclick="closeModal('deleteModal')">
                <i class="fas fa-times me-2"></i> Cancel
            </button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-admin-primary">
                    <i class="fas fa-trash me-2"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openDeleteModal(button) {
    const modal = document.getElementById('deleteModal');
    const itemName = button.getAttribute('data-name');
    const actionUrl = button.getAttribute('data-action');
    
    document.getElementById('deleteItemName').textContent = itemName;
    document.getElementById('deleteForm').action = actionUrl;
    
    modal.style.display = 'flex';
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'none';
    
    // Restore body scroll
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        closeModal('deleteModal');
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal('deleteModal');
    }
});
</script>
@endsection

