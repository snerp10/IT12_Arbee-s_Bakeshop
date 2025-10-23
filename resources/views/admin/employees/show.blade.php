@extends('layouts.admin')

@section('title', 'Employee Details')
@section('page-title', 'Employee Details - ' . $employee->full_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.employees.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-user-circle"></i> Employee Details: {{ $employee->full_name }}
        </h1>
        <a href="{{ route('admin.employees.edit', $employee) }}" class="btn-admin-secondary ms-3 position-absolute end-0">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
    </div>

    <div class="row">
        <!-- Employee Information -->
        <div class="col-md-8">
            <!-- Employee Information -->
            <div class="card kpi-card info shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-user me-2"></i> Employee Information</h5>
                </div>
                <div class="card-body admin-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-sea-dark mb-3">Basic Details</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Full Name:</strong></td>
                                    <td>{{ $employee->full_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone Number:</strong></td>
                                    <td>
                                        @if($employee->phone)
                                            {{ $employee->phone }}
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @switch($employee->status)
                                            @case('active')
                                                <span class="badge-admin-role admin">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </span>
                                                @break
                                            @case('on_leave')
                                                <span class="badge-admin-role cashier">
                                                    <i class="fas fa-user-clock me-1"></i> On Leave
                                                </span>
                                                @break
                                            @case('inactive')
                                                <span class="badge-admin-role baker">
                                                    <i class="fas fa-user-times me-1"></i> Inactive
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Employee ID:</strong></td>
                                    <td><code>#{{ $employee->employee_id }}</code></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-sea-dark mb-3">Work Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>
                                        @if($employee->address)
                                            {{ $employee->address }}
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Work Shift:</strong></td>
                                    <td>
                                        @if($employee->shift_start && $employee->shift_end)
                                            <span class="badge-admin-role manager">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $employee->shift_start->format('H:i') }} - {{ $employee->shift_end->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Joined Date:</strong></td>
                                    <td>{{ $employee->created_at->format('F d, Y \a\t g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Days Active:</strong></td>
                                    <td>{{ $employee->created_at->diffInDays(now()) }} days</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Account Information -->
            @if($employee->user)
                <div class="card kpi-card info shadow">
                    <div class="card-header">
                        <h5 class="mb-0 text-sea-primary">
                            <i class="fas fa-user-circle me-2"></i> User Account Information
                        </h5>
                    </div>
                    <div class="card-body admin-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-sea-dark mb-3">Account Details</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Username:</strong></td>
                                        <td>{{ $employee->user->username }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $employee->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Role:</strong></td>
                                        <td>
                                            <span class="badge-admin-role {{ $employee->user->role }}">
                                            @switch($employee->user->role)
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
                                        <td><strong>Account Status:</strong></td>
                                        <td>
                                            @if($employee->user->status === 'active')
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
                                        <td><strong>Account Created:</strong></td>
                                        <td>{{ $employee->user->created_at->format('F d, Y \a\t g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Updated:</strong></td>
                                        <td>{{ $employee->user->updated_at->format('F d, Y \a\t g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>User ID:</strong></td>
                                        <td><code>#{{ $employee->user->user_id }}</code></td>
                                    </tr>
                                </table>
                            </div>
                        </div>                        
                        <div class="mt-3 pt-3 border-top">
                            <button type="button" class="btn-admin-secondary" onclick="window.location='{{ route('admin.users.show', $employee->user) }}'">
                                <i class="fas fa-external-link-alt me-2"></i> View User Account Details
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert-admin-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This employee doesn't have a user account yet. 
                    <a href="{{ route('admin.users.create') }}" class="fw-bold text-decoration-underline">Create one here</a>.
                </div>
            @endif
        </div>

        <!-- Action Panel -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card kpi-card info shadow">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary">
                        <i class="fas fa-bolt me-2"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body admin-card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.employees.edit', $employee) }}" method="GET" class="d-inline">
                            <button type="submit" class="btn-admin-secondary w-100">
                                <i class="fas fa-edit me-2"></i> Edit Employee
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.employees.toggle-status', $employee) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-admin-light w-100">
                                <i class="fas fa-{{ $employee->status === 'active' ? 'pause' : 'play' }} me-2"></i>
                                @if($employee->status === 'active')
                                    Mark as Inactive
                                @elseif($employee->status === 'inactive')
                                    Mark as Active
                                @else
                                    Mark as Active
                                @endif
                            </button>
                        </form>

                        @if(!$employee->user)
                            <a href="{{ route('admin.users.create') }}?emp_id={{ $employee->emp_id }}" 
                               class="btn-admin-secondary w-100">
                                <i class="fas fa-user-plus me-2"></i> Create User Account
                            </a>
                        @endif

                        @if(!$employee->user)
                            <button type="button" class="btn btn-danger w-100" 
                                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-2"></i> Delete Employee
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if(!$employee->user)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete employee <strong>{{ $employee->full_name }}</strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-admin-light" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i> Delete Employee
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection