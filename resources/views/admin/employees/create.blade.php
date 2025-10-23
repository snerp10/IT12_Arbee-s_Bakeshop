@extends('layouts.admin')

@section('title', 'Create Employee')
@section('page-title', 'Create New Employee')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.employees.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-user-plus"></i> Create New Employee
        </h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card kpi-card info shadow">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-user-plus me-2"></i> Employee Information</h5>
                </div>
                <div class="card-body admin-card-body">
                    <form action="{{ route('admin.employees.store') }}" method="POST">
                        @csrf
                        
                        <!-- Name Fields -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">
                                        <i class="fas fa-user text-sea-primary"></i> First Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="first_name" id="first_name" 
                                           class="form-control @error('first_name') is-invalid @enderror" 
                                           value="{{ old('first_name') }}" required>
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
                                           value="{{ old('middle_name') }}">
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
                                           value="{{ old('last_name') }}" required>
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
                                           value="{{ old('phone') }}" placeholder="+63 123 456 7890">
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
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                        <option value="on_leave" {{ old('status') == 'on_leave' ? 'selected' : '' }}>
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
                                      placeholder="Complete address">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Work Shift -->
                        <div class="alert-admin-info">
                            <i class="fas fa-info-circle me-2"></i> Work shift times are optional and can be set later
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="shift_start" class="form-label">
                                        <i class="fas fa-clock text-sea-primary"></i> Shift Start Time
                                    </label>
                                    <input type="time" name="shift_start" id="shift_start" 
                                           class="form-control @error('shift_start') is-invalid @enderror" 
                                           value="{{ old('shift_start') }}">
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
                                           value="{{ old('shift_end') }}">
                                    @error('shift_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Must be after shift start time</div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn-admin-light" onclick="window.location='{{ route('admin.employees.index') }}'">
                                <i class="fas fa-times me-2"></i> Cancel
                            </button>
                            <button type="submit" class="btn-admin-secondary">
                                <i class="fas fa-save me-2"></i> Create Employee
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Information Side Panel -->
        <div class="col-md-4">
            <div class="card kpi-card info shadow">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-info-circle me-2"></i> Employee Information</h5>
                </div>
                <div class="card-body admin-card-body">
                    <div class="info-section">
                        <h6 class="text-sea-primary mb-2">
                            <i class="fas fa-user me-2"></i> Personal Details
                        </h6>
                        <p class="small text-muted mb-3">
                            Fill in the employee's basic information including their full name, contact details, and current address.
                        </p>

                        <h6 class="text-sea-primary mb-2">
                            <i class="fas fa-toggle-on me-2"></i> Status Options
                        </h6>
                        <div class="status-info mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge-admin-role admin me-2">Active</span>
                                <small class="text-muted">Ready to work</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge-admin-role baker me-2">Inactive</span>
                                <small class="text-muted">Not currently working</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge-admin-role cashier me-2">On Leave</span>
                                <small class="text-muted">Temporarily away</small>
                            </div>
                        </div>

                        <h6 class="text-sea-primary mb-2">
                            <i class="fas fa-clock me-2"></i> Work Shifts
                        </h6>
                        <p class="small text-muted mb-3">
                            Set the employee's regular work hours. This is optional and can be updated later as needed.
                        </p>

                        <h6 class="text-sea-primary mb-2">
                            <i class="fas fa-user-plus me-2"></i> Next Steps
                        </h6>
                        <p class="small text-muted">
                            After creating the employee, you can create a user account for them to access the system.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="card kpi-card info shadow mt-3">
                <div class="card-header">
                    <h5 class="mb-0 text-sea-primary"><i class="fas fa-lightbulb me-2"></i> Quick Tips</h5>
                </div>
                <div class="card-body admin-card-body">
                    <div class="tip-item mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        <small>Phone numbers should be unique for each employee</small>
                    </div>
                    <div class="tip-item mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        <small>Middle name is optional but helps with identification</small>
                    </div>
                    <div class="tip-item mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        <small>Set status to "Active" for employees ready to work</small>
                    </div>
                    <div class="tip-item">
                        <i class="fas fa-check text-success me-2"></i>
                        <small>Work shifts can be set now or updated later</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection