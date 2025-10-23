@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details - ' . $user->username)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.users.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-user-circle"></i> User Details: {{ $user->username }}
        </h1>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn-admin-secondary ms-3 position-absolute end-0">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
    </div>

    <div class="row">
        <!-- Main User Information -->
        <div class="col-md-8">
            <!-- Account Information -->
            <div class="card kpi-card primary shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-user me-2"></i> Account Information</h5>
                </div>
                <div class="card-body admin-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-sea-dark mb-3">Basic Details</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td>{{ $user->username }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Role:</strong></td>
                                    <td>
                                        <span class="badge-admin-role {{ $user->role }}">
                                            @switch($user->role)
                                                @case('admin')
                                                    <i class="fas fa-crown me-1"></i> Admin
                                                    @break
                                                @case('manager')
                                                    <i class="fas fa-user-tie me-1"></i> Manager
                                                    @break
                                                @case('baker')
                                                    <i class="fas fa-bread-slice me-1"></i> Baker
                                                    @break
                                                @case('cashier')
                                                    <i class="fas fa-cash-register me-1"></i> Cashier
                                                    @break
                                            @endswitch
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($user->status === 'active')
                                            <span class="badge-admin-role admin">
                                                <i class="fas fa-check-circle me-1"></i> Active
                                            </span>
                                        @else
                                            <span class="badge-admin-role baker">
                                                <i class="fas fa-times-circle me-1"></i> Inactive
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-sea-dark mb-3">Account Dates</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $user->created_at->format('F d, Y \a\t g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $user->updated_at->format('F d, Y \a\t g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Days Active:</strong></td>
                                    <td>{{ $user->created_at->diffInDays(now()) }} days</td>
                                </tr>
                                <tr>
                                    <td><strong>User ID:</strong></td>
                                    <td><code>#{{ $user->user_id }}</code></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Information -->
            @if($user->employee)
            <div class="card kpi-card primary shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-id-badge me-2"></i> Employee Information</h5>
                </div>
                <div class="card-body admin-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-sea-dark mb-3">Personal Details</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Full Name:</strong></td>
                                    <td>{{ $user->employee->full_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $user->employee->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $user->employee->address ?: 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Employee Status:</strong></td>
                                    <td>
                                        @if($user->employee->status == 'active')
                                            <span class="badge-admin-role admin">
                                                <i class="fas fa-check-circle me-1"></i> Active
                                            </span>
                                        @else
                                            <span class="badge-admin-role baker">
                                                <i class="fas fa-times-circle me-1"></i> {{ ucfirst($user->employee->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-sea-dark mb-3">Work Schedule</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Shift Start:</strong></td>
                                    <td>{{ $user->employee->shift_start ? $user->employee->shift_start->format('g:i A') : 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Shift End:</strong></td>
                                    <td>{{ $user->employee->shift_end ? $user->employee->shift_end->format('g:i A') : 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Employee ID:</strong></td>
                                    <td><code>#{{ $user->employee->employee_id }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Hired:</strong></td>
                                    <td>{{ $user->employee->created_at->format('F d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="card kpi-card warning shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-exclamation-triangle me-2"></i> Employee Information</h5>
                </div>
                <div class="card-body admin-card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> No employee record linked to this user account.
                    </div>
                </div>
            </div>
            @endif

            <!-- Role Permissions -->
            <div class="card kpi-card primary shadow">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-key me-2"></i> Role Permissions & Capabilities</h5>
                </div>
                <div class="card-body admin-card-body">
                    @switch($user->role)
                        @case('admin')
                            <div class="alert-admin-danger">
                                <h6><i class="fas fa-crown me-2"></i> Administrator Privileges</h6>
                                <ul class="mb-0">
                                    <li>Full system access and control</li>
                                    <li>Manage all users and roles</li>
                                    <li>Access all reports and analytics</li>
                                    <li>System configuration and settings</li>
                                    <li>Database management and backups</li>
                                </ul>
                            </div>
                            @break
                        @case('manager')
                            <div class="alert-admin-warning">
                                <h6><i class="fas fa-user-tie me-2"></i> Manager Capabilities</h6>
                                <ul class="mb-0">
                                    <li>Manage daily operations and staff</li>
                                    <li>Access sales and inventory reports</li>
                                    <li>Approve orders and deliveries</li>
                                    <li>Schedule employees and shifts</li>
                                    <li>Handle customer complaints and refunds</li>
                                </ul>
                            </div>
                            @break
                        @case('baker')
                            <div class="alert-admin-success">
                                <h6><i class="fas fa-bread-slice me-2"></i> Baker Responsibilities</h6>
                                <ul class="mb-0">
                                    <li>Manage production schedules and recipes</li>
                                    <li>Track ingredient usage and inventory</li>
                                    <li>Quality control and product standards</li>
                                    <li>Update production status and completion</li>
                                    <li>Coordinate with supply chain for ingredients</li>
                                </ul>
                            </div>
                            @break
                        @case('cashier')
                            <div class="alert-admin-info">
                                <h6><i class="fas fa-cash-register me-2"></i> Cashier Duties</h6>
                                <ul class="mb-0">
                                    <li>Process customer sales and payments</li>
                                    <li>Handle cash register and daily tallies</li>
                                    <li>Manage customer orders and pickups</li>
                                    <li>Basic inventory level monitoring</li>
                                    <li>Customer service and support</li>
                                </ul>
                            </div>
                            @break
                    @endswitch
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card kpi-card primary shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-bolt me-2"></i> Quick Actions</h5>
                </div>
                <div class="card-body admin-card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.users.edit', $user) }}" method="GET" class="d-inline">
                            <button type="submit" class="bg-transparent w-100 border-0 shadow-none px-0 text-start" style="color: #FFC107; font-weight: 600;">
                                <i class="fas fa-edit me-2" aria-label="Edit"></i> Edit User
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-transparent w-100 border-0 shadow-none px-0 text-start" style="color: #17a2b8; font-weight: 600;">
                                <i class="fas fa-{{ $user->status === 'active' ? 'pause' : 'play' }} me-2" aria-label="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}"></i>
                                {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }} User
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="bg-transparent w-100 border-0 shadow-none px-0 text-start" style="color: #5a5c69; font-weight: 600;">
                                <i class="fas fa-key me-2" aria-label="Reset Password"></i> Reset Password
                            </button>
                        </form>
                        
                        @if($user->user_id !== auth()->id())
                            <hr>
                            <button type="button" class="bg-transparent w-100 border-0 shadow-none px-0 text-start text-danger fw-bold" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-2" aria-label="Delete"></i> Delete User
                            </button>
                        @else
                            <div class="alert-admin-info p-2 mt-2">
                                <small><i class="fas fa-info-circle me-1"></i> You cannot delete your own account</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card kpi-card primary shadow">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-chart-bar me-2"></i> Account Statistics</h5>
                </div>
                <div class="card-body admin-card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h3 class="text-sea-dark">{{ $user->created_at->diffInDays(now()) }}</h3>
                                <small class="text-muted">Days Active</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h3 class="text-sea-dark">
                                {{ $user->status === 'active' ? 'ACTIVE' : 'INACTIVE' }}
                            </h3>
                            <small class="text-muted">Current Status</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <h6 class="text-muted mb-2">Account Created</h6>
                        <p class="mb-0">{{ $user->created_at->format('F d, Y') }}</p>
                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if($user->user_id !== auth()->id())
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm User Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="text-center mb-3">Delete User: {{ $user->username }}?</h5>
                <p class="text-center">This will permanently delete:</p>
                <ul class="list-unstyled text-center">
                    <li><i class="fas fa-user text-danger"></i> User account and login credentials</li>
                    <li><i class="fas fa-key text-danger"></i> All associated permissions</li>
                    <li><i class="fas fa-history text-danger"></i> User activity history</li>
                </ul>
                <div class="alert-admin-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i> <strong>This action cannot be undone!</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-admin-light" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancel
                </button>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i> Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection