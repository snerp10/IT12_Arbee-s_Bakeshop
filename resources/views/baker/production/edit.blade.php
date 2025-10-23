@extends('layouts.baker')

@section('title', 'Edit Production Batch')

@section('content')
<div class="d-flex align-items-center mb-4">
    <div>
        <a href="{{ route('baker.production.index') }}" class="btn-admin-light me-3">
            <i class="fas fa-arrow-left me-2"></i> Back to Production
        </a>
    </div>
    <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
        <i class="fas fa-edit me-2"></i> Edit Production Batch: {{ $production->batch_number }}
    </h1>
    <div>
        <a href="{{ route('baker.production.show', $production) }}" class="btn-admin-secondary">
            <i class="fas fa-eye me-2"></i> View Details
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header card-header-sea-secondary">
                <h5 class="mb-0">
                    <i class="fas fa-industry me-2"></i> Edit Batch Information
                </h5>
            </div>
            <div class="card-body admin-card-body">
                <form action="{{ route('baker.production.update', $production) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Read-only batch information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Batch Number</label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $production->batch_number }}" 
                                       readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Product</label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $production->product->name ?? 'N/A' }}" 
                                       readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Quantity Produced</label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ number_format($production->quantity_produced) }} pieces" 
                                       readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Production Date</label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $production->production_date->format('F d, Y') }}" 
                                       readonly>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="mb-4">
                    
                    <!-- Editable fields -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                @if($production->status === 'completed')
                                    <input type="text" 
                                           class="form-control" 
                                           value="Completed" 
                                           readonly>
                                    <input type="hidden" name="status" value="completed">
                                    <small class="text-muted">
                                        <i class="fas fa-lock me-1"></i> Status cannot be changed for completed batches
                                    </small>
                                @else
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pending" {{ $production->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ $production->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $production->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $production->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                @endif
                                @error('status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Production Notes</label>
                        <textarea class="form-control" 
                                  id="notes" 
                                  name="notes" 
                                  rows="4" 
                                  placeholder="Enter any notes about this production batch...">{{ old('notes', $production->notes) }}</textarea>
                        @error('notes')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="alert-admin-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Note:</strong> Only the status and notes can be modified for existing production batches. Quantity and product changes are not allowed to maintain inventory accuracy.
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button type="button" class="btn-admin-light" onclick="window.location='{{ route('baker.production.index') }}'">
                            <i class="fas fa-times me-2"></i> Cancel
                        </button>
                        <button type="submit" class="btn-admin-primary">
                            <i class="fas fa-save me-2"></i> Update Production Batch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection