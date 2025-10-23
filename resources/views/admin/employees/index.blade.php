@extends('layouts.admin')

@section('title', 'Employee Management')
@section('page-title', 'Employee Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-muted-sea">
            <i class="fas fa-users"></i> Employee Management
        </h1>
        <a href="{{ route('admin.employees.create') }}" class="btn-admin-secondary">
            <i class="fas fa-user-plus me-2"></i> Add New Employee
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card info h-100" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
                <div class="card-body admin-card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="ms-2 me-2">
                                <i class="fas fa-users kpi-icon" style="font-size: 1.25rem; opacity: 0.7;"></i>
                            </div>
                            <div class="kpi-label text-sea-primary mb-1 me-2" style="font-size: 0.875rem;">Total Employees</div>
                        </div>
                        <div class="text-end">
                            <div class="kpi-number mb-1" style="font-size: 1.5rem; font-weight: 600; line-height: 1.2; min-width: 90px;">
                                {{ $stats['total'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.employees.index', ['status' => 'active']) }}" class="text-decoration-none">
            <div class="card kpi-card info h-100" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
                <div class="card-body admin-card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                    <div class="ms-2 me-2">
                        <i class="fas fa-user-check kpi-icon" style="font-size: 1.25rem; opacity: 0.7;"></i>
                    </div>
                    <div class="kpi-label text-sea-primary mb-1 me-2" style="font-size: 0.875rem;">Active Employees</div>
                    </div>
                    <div class="text-end">
                    <div class="kpi-number mb-1" style="font-size: 1.5rem; font-weight: 600; line-height: 1.2; min-width: 90px;">
                        {{ $stats['active'] }}
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.employees.index', ['status' => 'on_leave']) }}" class="text-decoration-none">
            <div class="card kpi-card info h-100" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
                <div class="card-body admin-card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                    <div class="ms-2 me-2">
                        <i class="fas fa-user-clock kpi-icon" style="font-size: 1.25rem; opacity: 0.7;"></i>
                    </div>
                    <div class="kpi-label text-sea-primary mb-1 me-2" style="font-size: 0.875rem;">On Leave</div>
                    </div>
                    <div class="text-end">
                    <div class="kpi-number mb-1" style="font-size: 1.5rem; font-weight: 600; line-height: 1.2; min-width: 90px;">
                        {{ $stats['on_leave'] }}
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.employees.index', ['with_accounts' => 1]) }}" class="text-decoration-none">
            <div class="card kpi-card info h-100" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
                <div class="card-body admin-card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                    <div class="ms-2 me-2">
                        <i class="fas fa-user-tie kpi-icon" style="font-size: 1.25rem; opacity: 0.7;"></i>
                    </div>
                    <div class="kpi-label text-sea-primary mb-1 me-2" style="font-size: 0.875rem;">With Accounts</div>
                    </div>
                    <div class="text-end">
                    <div class="kpi-number mb-1" style="font-size: 1.5rem; font-weight: 600; line-height: 1.2; min-width: 90px;">
                        {{ $stats['with_accounts'] }}
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </a>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="card kpi-card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0 text-sea-primary">
                <i class="fas fa-list me-2"></i> All Employees
            </h5>
        </div>
        <div class="card-body admin-card-body">
            @if($employees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th style="width: 200px;">Full Name</th>
                                <th style="width: 150px;" class="d-none d-md-table-cell">Phone</th>
                                <th style="width: 120px;" class="d-none d-lg-table-cell">Shift</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 120px;" class="d-none d-sm-table-cell">User Account</th>
                                <th style="width: 100px;" class="d-none d-lg-table-cell">Created</th>
                                <th style="width: 120px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-sea-primary">#{{ $employee->employee_id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <i class="fas fa-user-circle text-muted fa-2x"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $employee->full_name }}</div>
                                                @if($employee->phone)
                                                    <small class="text-muted">{{ $employee->phone }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        @if($employee->phone)
                                            <span class="text-info">
                                                <i class="fas fa-phone me-1"></i> {{ $employee->phone }}
                                            </span>
                                        @else
                                            <span class="text-muted small">Not provided</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        @if($employee->shift_start && $employee->shift_end)
                                            <div class="badge-admin-role manager d-inline-block text-nowrap">
                                                <i class="fas fa-clock me-1"></i>
                                                <span>{{ $employee->shift_start->format('H:i') }} - {{ $employee->shift_end->format('H:i') }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted small">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($employee->status)
                                            @case('active')
                                                <div class="badge-admin-role d-inline-block" style="background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%) !important; color: #fff !important;">
                                                    <i class="fas fa-check-circle me-1" aria-label="Active"></i>
                                                    <span>Active</span>
                                                </div>
                                                @break
                                            @case('on_leave')
                                                <div class="badge-admin-role cashier d-inline-block">
                                                    <i class="fas fa-user-clock me-1" aria-label="On Leave"></i> 
                                                    <span class="d-none d-sm-inline">On Leave</span>
                                                    <span class="d-inline d-sm-none">Leave</span>
                                                </div>
                                                @break
                                            @case('inactive')
                                                <div class="badge-admin-role baker d-inline-block">
                                                    <i class="fas fa-user-times me-1" aria-label="Inactive"></i> 
                                                    <span>Inactive</span>
                                                </div>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        @if($employee->user)
                                            <div class="badge-admin-role {{ $employee->user->role }} d-inline-block text-nowrap">
                                                <i class="fas fa-user-check me-1"></i> 
                                                <span>{{ ucfirst($employee->user->role) }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted small">No account</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="text-muted small">{{ $employee->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                        <button type="button" class="btn-admin-light btn-sm border border-1 d-flex align-items-center gap-1"
                            onclick="window.location='{{ route('admin.employees.show', $employee) }}'"
                            title="View">
                                                <i class="fas fa-eye" aria-label="View"></i>
                                                <span class="small text-muted">View</span>
                                            </button>
                        <button type="button" class="btn-admin-secondary btn-sm border border-1 d-flex align-items-center gap-1"
                            onclick="window.location='{{ route('admin.employees.edit', $employee) }}'"
                            title="Edit">
                                                <i class="fas fa-edit" aria-label="Edit"></i>
                                                <span class="small text-muted">Edit</span>
                                            </button>
                                            <button type="button" class="btn-admin-info btn-sm border border-1 d-flex align-items-center gap-1"
                                                    onclick="toggleStatus({{ $employee->employee_id }})" title="Toggle Status">
                                                <i class="fas fa-{{ $employee->status === 'active' ? 'ban' : 'check' }}" aria-label="{{ $employee->status === 'active' ? 'Deactivate' : 'Activate' }}"></i>
                                                <span class="small text-muted">{{ $employee->status === 'active' ? 'Deactivate' : 'Activate' }}</span>
                                            </button>
                                            @if(!$employee->user)
                                                <button type="button" class="btn-admin-primary btn-sm border border-1 d-flex align-items-center gap-1"
                                                        onclick="openDeleteModal(this)"
                                                        data-name="{{ $employee->full_name }}"
                                                        data-id="{{ $employee->employee_id }}"
                                                        data-action="{{ route('admin.employees.destroy', $employee->employee_id) }}"
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

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <div class="pagination-info">
                        @if($employees->count() > 0)
                            <i class="fas fa-info-circle me-1"></i>
                            Showing <strong>{{ $employees->firstItem() }}</strong> to <strong>{{ $employees->lastItem() }}</strong> 
                            of <strong>{{ $employees->total() }}</strong> employees
                        @else
                            <i class="fas fa-exclamation-circle me-1"></i>
                            No employees found
                        @endif
                    </div>
                    <div>
                        {{ $employees->appends(request()->query())->links('vendor.pagination.admin') }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No employees found</h5>
                    <p class="text-muted">Get started by adding your first employee.</p>
                    <a href="{{ route('admin.employees.create') }}" class="btn-admin-secondary">
                        <i class="fas fa-user-plus me-2"></i> Add First Employee
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Employee Modal -->
<div id="deleteModal" class="sea-modal" aria-hidden="true">
    <div class="sea-modal-dialog">
        <div class="sea-modal-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 flex-grow-1" style="flex-basis:80%;max-width:80%;">
                <i class="fas fa-trash me-2"></i> Confirm Deletion - <span id="deleteEmployeeName"></span>
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
                <p>Are you sure you want to delete employee <strong id="deleteEmployeeNameStrong"></strong>? This action cannot be undone.</p>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn-admin-light me-2" onclick="closeModal('deleteModal')">Cancel</button>
                    <button type="submit" class="btn-admin-primary">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openDeleteModal(btn) {
    const name = btn.getAttribute('data-name');
    const action = btn.getAttribute('data-action');
    
    document.getElementById('deleteEmployeeName').textContent = name;
    document.getElementById('deleteEmployeeNameStrong').textContent = name;
    document.getElementById('deleteForm').setAttribute('action', action);
    
    document.getElementById('deleteModal').classList.add('show');
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