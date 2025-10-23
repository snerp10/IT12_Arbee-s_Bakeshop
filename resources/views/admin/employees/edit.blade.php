@extends('layouts.admin')

@section('title', 'Edit Employee')
@section('page-title', 'Edit Employee - ' . $employee->full_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.employees.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-user-edit"></i> Edit Employee: {{ $employee->full_name }}
        </h1>
        <a href="{{ route('admin.employees.show', $employee) }}" class="btn-admin-secondary ms-3 position-absolute end-0">
            <i class="fas fa-eye me-1"></i> View
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card kpi-card info shadow">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-user-edit me-2"></i> Edit Employee Information</h5>
                </div>
                <div class="card-body admin-card-body">
                    <form action="{{ route('admin.employees.update', $employee) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Name Fields -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">
                                        <i class="fas fa-user text-sea-primary"></i> First Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="first_name" id="first_name" 
                                           class="form-control @error('first_name') is-invalid @enderror" 
                                           value="{{ old('first_name', $employee->first_name) }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="middle_name" class="form-label">
                                        <i class="fas fa-user text-sea-primary"></i> Middle Name
                                    </label>
                                    <input type="text" name="middle_name" id="middle_name" 
                                           class="form-control @error('middle_name') is-invalid @enderror" 
                                           value="{{ old('middle_name', $employee->middle_name) }}">
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">
                                        <i class="fas fa-user text-sea-primary"></i> Last Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="last_name" id="last_name" 
                                           class="form-control @error('last_name') is-invalid @enderror" 
                                           value="{{ old('last_name', $employee->last_name) }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone text-sea-primary"></i> Phone Number
                                    </label>
                                    <input type="tel" name="phone" id="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $employee->phone) }}" placeholder="+63 123 456 7890">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">
                                        <i class="fas fa-toggle-on text-sea-primary"></i> Status <span class="text-danger">*</span>
                                    </label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="">Select Status</option>
                                        <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                        <option value="on_leave" {{ old('status', $employee->status) == 'on_leave' ? 'selected' : '' }}>
                                            On Leave
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">
                                <i class="fas fa-map-marker-alt text-sea-primary"></i> Address
                            </label>
                            <textarea name="address" id="address" rows="3" 
                                      class="form-control @error('address') is-invalid @enderror" 
                                      placeholder="Complete address">{{ old('address', $employee->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Work Shift -->
                        <div class="alert-admin-info">
                            <i class="fas fa-info-circle me-2"></i> Update work shift times as needed
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="shift_start" class="form-label">
                                        <i class="fas fa-clock text-sea-primary"></i> Shift Start Time
                                    </label>
                                    <input type="time" name="shift_start" id="shift_start" 
                                           class="form-control @error('shift_start') is-invalid @enderror" 
                                           value="{{ old('shift_start', $employee->shift_start ? $employee->shift_start->format('H:i') : '') }}">
                                    @error('shift_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="shift_end" class="form-label">
                                        <i class="fas fa-clock text-sea-primary"></i> Shift End Time
                                    </label>
                                    <input type="time" name="shift_end" id="shift_end" 
                                           class="form-control @error('shift_end') is-invalid @enderror" 
                                           value="{{ old('shift_end', $employee->shift_end ? $employee->shift_end->format('H:i') : '') }}">
                                    @error('shift_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Must be after shift start time</div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn-admin-light" onclick="window.location='{{ route('admin.employees.show', $employee) }}'">
                                <i class="fas fa-times me-2"></i> Cancel
                            </button>
                            <button type="submit" class="btn-admin-secondary">
                                <i class="fas fa-save me-2"></i> Update Employee
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Employee Information Side Panel -->
        <div class="col-md-4">
            <div class="card kpi-card info shadow">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-user-circle me-2"></i> Current Employee Info</h5>
                </div>
                <div class="card-body admin-card-body">
                    <!-- Employee ID & Created Date -->
                    <div class="mb-3">
                        <h6 class="text-sea-dark mb-2">
                            <i class="fas fa-id-badge me-2"></i> Employee Details
                        </h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td><code>#{{ $employee->employee_id }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Joined:</strong></td>
                                <td>{{ $employee->created_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Days:</strong></td>
                                <td>{{ $employee->created_at->diffInDays(now()) }} days</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Current Status -->
                    <div class="mb-3">
                        <h6 class="text-sea-dark mb-2">
                            <i class="fas fa-user-check me-2"></i> Current Status
                        </h6>
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
                    </div>

                    <!-- User Account Status -->
                    <div class="mb-0">
                        <h6 class="text-sea-dark mb-2">
                            <i class="fas fa-user-shield me-2"></i> User Account
                        </h6>
                        @if($employee->user)
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td>{{ $employee->user->username }}</td>
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
                            </table>
                        @else
                            <p class="mb-0 text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i> No user account
                            </p>
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