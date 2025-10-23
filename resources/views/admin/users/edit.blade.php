@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User - ' . $user->username)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.users.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-user-edit"></i> Edit User: {{ $user->username }}
        </h1>
        <a href="{{ route('admin.users.show', $user) }}" class="btn-admin-secondary ms-3 position-absolute end-0">
            <i class="fas fa-eye me-1"></i> View
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card kpi-card primary shadow">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-user-edit me-2"></i> Edit User Information</h5>
                </div>
                <div class="card-body admin-card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Employee Selection -->
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">
                                <i class="fas fa-id-badge text-sea-primary"></i> Employee <span class="text-danger">*</span>
                            </label>
                            <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                <option value="">Select Employee</option>
                                @foreach($availableEmployees as $employee)
                                    <option value="{{ $employee->employee_id }}" 
                                            {{ old('employee_id', $user->employee_id) == $employee->employee_id ? 'selected' : '' }}>
                                        {{ $employee->full_name }} - {{ $employee->phone }}
                                        @if($employee->employee_id == $user->employee_id)
                                            (Current)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user text-sea-primary"></i> Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="username" id="username" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   value="{{ old('username', $user->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope text-sea-primary"></i> Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label">
                                <i class="fas fa-user-tag text-sea-primary"></i> Role <span class="text-danger">*</span>
                            </label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="">Select Role</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>
                                <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>
                                    Manager
                                </option>
                                <option value="baker" {{ old('role', $user->role) == 'baker' ? 'selected' : '' }}>
                                    Baker
                                </option>
                                <option value="cashier" {{ old('role', $user->role) == 'cashier' ? 'selected' : '' }}>
                                    Cashier
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password (Optional for edit) -->
                        <div class="alert-admin-info">
                            <i class="fas fa-info-circle me-2"></i> Leave password fields empty to keep current password
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock text-sea-primary"></i> New Password
                            </label>
                            <input type="password" name="password" id="password" 
                                   class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 8 characters required (if changing)</div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock text-sea-primary"></i> Confirm New Password
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="form-control">
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">
                                <i class="fas fa-toggle-on text-success"></i> Status
                            </label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn-admin-light" onclick="window.location='{{ route('admin.users.index') }}'">
                                <i class="fas fa-times me-2"></i> Cancel
                            </button>
                            <button type="submit" class="btn-admin-secondary">
                                <i class="fas fa-save me-2"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- User Information Side Panel -->
        <div class="col-md-4">
            <div class="card kpi-card primary shadow">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-user-circle me-2"></i> Current User Info</h5>
                </div>
                <div class="card-body admin-card-body">
                    <!-- Current Role -->
                    <div class="mb-3">
                        <h6 class="text-sea-dark mb-2">
                            <i class="fas fa-user-tag me-2"></i> Current Role
                        </h6>
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
                    </div>

                    <!-- Employee Details -->
                    <div class="mb-3">
                        <h6 class="text-sea-dark mb-2">
                            <i class="fas fa-id-badge me-2"></i> Employee Details
                        </h6>
                        @if($user->employee)
                            <p class="mb-0 text-muted">
                                <strong>{{ $user->employee->full_name }}</strong>
                                
                                @if($user->employee->status == 'active')
                                    <span class="text-info">{{ $user->employee->phone }}</span>  
                                @else
                                    <span class="badge-admin-role baker">
                                        <i class="fas fa-times-circle me-1"></i> {{ ucfirst($user->employee->status) }}
                                    </span>
                                @endif
                            </p>
                        @else
                            <p class="mb-0 text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i> No employee linked
                            </p>
                        @endif
                    </div>

                    <!-- Account Details -->
                    <div class="mb-0">
                        <h6 class="text-sea-dark mb-2">
                            <i class="fas fa-user-circle me-2"></i> Account Details
                        </h6>
                        <p class="mb-0 text-muted">
                            <strong>Created:</strong> {{ $user->created_at->format('M d, Y') }} 
                            <strong>Updated:</strong> {{ $user->updated_at->format('M d, Y') }} 
                            <br>
                            <br>
                            <span class="badge-admin-role {{ $user->status === 'active' ? 'admin' : 'baker' }}">
                                <i class="fas fa-{{ $user->status === 'active' ? 'check-circle' : 'times-circle' }} me-1"></i>
                                {{ $user->status === 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card kpi-card info shadow mt-3">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-bolt me-2"></i> Quick Actions</h5>
                </div>
                <div class="card-body admin-card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-admin-light w-100">
                                <i class="fas fa-{{ $user->status === 'active' ? 'pause' : 'play' }} me-2"></i> 
                                {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }} User
                            </button>
                        </form>
                        
                        @if($user->user_id !== auth()->id())
                            <button type="button" class="btn btn-sm btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-2"></i> Delete User
                            </button>
                        @endif
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
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete user <strong>{{ $user->username }}</strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-admin-light" data-bs-dismiss="modal">Cancel</button>
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

