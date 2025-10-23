@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-muted-sea">
            <i class="fas fa-users"></i> User Management
        </h1>
        <a href="{{ route('admin.users.create') }}" class="btn-admin-secondary">
            <i class="fas fa-plus me-2"></i> Add New User
        </a>
    </div>

    <!-- Filters -->
    <div class="card admin-filter-card shadow mb-4">
        <div class="card-body admin-card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="baker" {{ request('role') == 'baker' ? 'selected' : '' }}>Baker</option>
                        <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Username or email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn-admin-secondary me-2">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <button type="reset" class="btn-admin-light me-2" onclick="window.location='{{ route('admin.users.index') }}'">
                        <i class="fas fa-times me-1"></i> Clear
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card kpi-card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-sea-primary">
                Users List ({{ $users->total() }} total)
            </h6>
        </div>
        <div class="card-body admin-card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Employee</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="user-checkbox" value="{{ $user->user_id }}">
                                    </td>
                                    <td>
                                        <strong>{{ $user->username }}</strong>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge-admin-role {{ $user->role }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->employee)
                                            {{ $user->employee->full_name }}
                                            <small class="text-muted d-block">ID: {{ $user->employee->employee_id }}</small>
                                        @else
                                            <span class="text-muted">No employee linked</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->status === 'active')
                                            <span class="badge-admin-role" style="background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%) !important; color: #fff !important;">
                                                <i class="fas fa-check-circle me-1" aria-label="Active"></i> Active
                                            </span>
                                        @elseif($user->status === 'pending')
                                            <span class="badge-admin-role manager" style="background: linear-gradient(135deg, #FFEAA5 0%, #FFD700 100%) !important; color: #333 !important;">
                                                <i class="fas fa-user-clock me-1" aria-label="Pending"></i> Pending
                                            </span>
                                        @else
                                            <span class="badge-admin-role baker"><i class="fas fa-times-circle me-1" aria-label="Inactive"></i> Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $user->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            <button type="button" class="btn-admin-light btn-sm border border-1 d-flex align-items-center gap-1" 
                                                    onclick="window.location='{{ route('admin.users.show', $user->user_id) }}'" 
                                                    title="View">
                                                <i class="fas fa-eye" aria-label="View"></i>
                                                <span class="small text-muted">View</span>
                                            </button>
                                            <button type="button" class="btn-admin-secondary btn-sm border border-1 d-flex align-items-center gap-1" 
                                                    onclick="window.location='{{ route('admin.users.edit', $user->user_id) }}'" 
                                                    title="Edit">
                                                <i class="fas fa-edit" aria-label="Edit"></i>
                                                <span class="small text-muted">Edit</span>
                                            </button>
                                            @if($user->status === 'pending')
                                                <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn-admin-primary btn-sm"><i class="fas fa-check me-1"></i> Approve</button>
                                                </form>
                                                <form action="{{ route('admin.users.reject', $user) }}" method="POST" class="d-inline ms-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn-admin-light btn-sm"><i class="fas fa-times me-1"></i> Reject</button>
                                                </form>
                                            @else
                                                <button type="button" class="btn-admin-info btn-sm border border-1 d-flex align-items-center gap-1" 
                                                        onclick="toggleStatus({{ $user->user_id }})" title="Toggle Status">
                                                    <i class="fas fa-{{ $user->status === 'active' ? 'ban' : 'check' }}" aria-label="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}"></i>
                                                    <span class="small text-muted">{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}</span>
                                                </button>
                                                @if($user->role !== 'admin' || \App\Models\User::where('role', 'admin')->where('status', 'active')->count() > 1)
                                                    <button type="button" class="btn-admin-delete btn-sm border border-1 d-flex align-items-center gap-1" 
                                                            onclick="deleteUser({{ $user->user_id }})" title="Delete">
                                                        <i class="fas fa-trash" aria-label="Delete"></i>
                                                        <span class="small text-white">Delete</span>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                    </div>
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                    <h5>No users found</h5>
                    <p class="text-muted">Try adjusting your search criteria or create a new user.</p>
                    <a href="{{ route('admin.users.create') }}" class="btn-admin-primary">
                        <i class="fas fa-plus me-2"></i> Create First User
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-admin-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmAction">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
});

// Toggle user status
function toggleStatus(userId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/users/${userId}/toggle-status`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'PATCH';
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
}

// Delete user
function deleteUser(userId) {
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to delete this user? This action cannot be undone.';
    
    document.getElementById('confirmAction').onclick = function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    };
    
    new bootstrap.Modal(document.getElementById('confirmModal')).show();
}

// Bulk operations
function bulkActivate() {
    const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Please select users to activate.');
        return;
    }
    
    document.getElementById('confirmMessage').textContent = `Activate ${selected.length} selected user(s)?`;
    
    document.getElementById('confirmAction').onclick = function() {
        // Implement bulk activate logic
        console.log('Bulk activate:', selected);
    };
    
    new bootstrap.Modal(document.getElementById('confirmModal')).show();
}

function bulkDeactivate() {
    const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Please select users to deactivate.');
        return;
    }
    
    document.getElementById('confirmMessage').textContent = `Deactivate ${selected.length} selected user(s)?`;
    
    document.getElementById('confirmAction').onclick = function() {
        // Implement bulk deactivate logic
        console.log('Bulk deactivate:', selected);
    };
    
    new bootstrap.Modal(document.getElementById('confirmModal')).show();
}
</script>
@endsection