@extends('layouts.admin')

@section('title', 'Create User')
@section('page-title', 'Create New User')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.users.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-user-plus"></i> Create New User
        </h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card kpi-card primary shadow">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-user-plus me-2"></i> User Information</h5>
                </div>
                <div class="card-body admin-card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        
                        <!-- Employee Selection -->
                        <div class="mb-3">
                            <label for="emp_id" class="form-label">
                                <i class="fas fa-id-badge text-sea-primary"></i> Employee <span class="text-danger">*</span>
                            </label>
                            <select name="emp_id" id="emp_id" class="form-select @error('emp_id') is-invalid @enderror" required>
                                <option value="">Select Employee</option>
                                @foreach($availableEmployees as $employee)
                                    <option value="{{ $employee->emp_id }}" {{ old('emp_id') == $employee->emp_id ? 'selected' : '' }}>
                                        {{ $employee->full_name }} - {{ $employee->phone }}
                                    </option>
                                @endforeach
                            </select>
                            @error('emp_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($availableEmployees->isEmpty())
                                <div class="text-warning mt-2">
                                    <i class="fas fa-exclamation-triangle"></i> No available employees. All employees already have user accounts.
                                </div>
                            @endif
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user text-sea-primary"></i> Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="username" id="username" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   value="{{ old('username') }}" required>
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
                                   value="{{ old('email') }}" 
                                   autocomplete="off"
                                   required>
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
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                    <i class="fas fa-crown"></i> Admin
                                </option>
                                <option value="baker" {{ old('role') == 'baker' ? 'selected' : '' }}>
                                    <i class="fas fa-bread-slice"></i> Baker
                                </option>
                                <option value="cashier" {{ old('role') == 'cashier' ? 'selected' : '' }}>
                                    <i class="fas fa-cash-register"></i> Cashier
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Info -->
                        <div class="alert-admin-info">
                            <i class="fas fa-info-circle me-2"></i> Password is required for new user accounts
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock text-sea-primary"></i> Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" id="password" 
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 8 characters required</div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock text-sea-primary"></i> Confirm Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="form-control" required>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">
                                <i class="fas fa-toggle-on text-success"></i> Status
                            </label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn-admin-light" onclick="window.location='{{ route('admin.users.index') }}'">
                                <i class="fas fa-times me-2"></i> Cancel
                            </button>
                            <button type="submit" class="btn-admin-secondary">
                                <i class="fas fa-save me-2"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Role Information Side Panel -->
        <div class="col-md-4">
            <div class="card kpi-card info shadow">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-info-circle me-2"></i> Role Descriptions</h5>
                </div>
                <div class="card-body admin-card-body">
                    <div class="role-info">
                        <div class="mb-3 p-3 rounded" style="border-left: 3px solid #FE5F55; background: rgba(238, 245, 219, 0.3);">
                            <span class="badge-admin-role admin mb-2">
                                <i class="fas fa-crown me-1"></i> Admin
                            </span>
                            <p class="mb-0 small text-muted">Full system access, manage all users and settings</p>
                        </div>
                        <div class="mb-3 p-3 rounded" style="border-left: 3px solid #FE5F55; background: rgba(238, 245, 219, 0.3);">
                            <span class="badge-admin-role baker mb-2">
                                <i class="fas fa-bread-slice me-1"></i> Baker
                            </span>
                            <p class="mb-0 small text-muted">Manage production, recipes, and baking operations</p>
                        </div>
                        <div class="mb-3 p-3 rounded" style="border-left: 3px solid #FE5F55; background: rgba(238, 245, 219, 0.3);">
                            <span class="badge-admin-role cashier mb-2">
                                <i class="fas fa-cash-register me-1"></i> Cashier
                            </span>
                            <p class="mb-0 small text-muted">Handle sales, payments, and customer service</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Employees Info -->
            <div class="card kpi-card primary shadow mt-3">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-users me-2"></i> Available Employees</h5>
                </div>
                <div class="card-body admin-card-body">
                    @if($availableEmployees->count() > 0)
                        <p class="text-success mb-2">
                            <i class="fas fa-check-circle"></i> {{ $availableEmployees->count() }} employees available for user creation
                        </p>
                        <small class="text-muted">
                            Select an employee from the dropdown to create their user account.
                        </small>
                    @else
                        <p class="text-warning mb-2">
                            <i class="fas fa-exclamation-triangle"></i> No employees available
                        </p>
                        <small class="text-muted">
                            All employees already have user accounts. Create new employees first to add more users.
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

