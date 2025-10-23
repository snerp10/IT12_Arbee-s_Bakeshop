@extends('layouts.baker')

@section('title', 'Create Production Batch')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('baker.production.index') }}" class="btn-admin-light">
            <i class="fas fa-arrow-left me-2"></i> Back to Production
        </a>
    </div>
    <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
        <i class="fas fa-industry me-2"></i> Create Production Batch
    </h1>
    <div></div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header card-header-sea-secondary">
                <h5 class="mb-0">
                    <i class="fas fa-industry me-2"></i> New Production Batch
                </h5>
            </div>
            <div class="card-body admin-card-body">
                <form action="{{ route('baker.production.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prod_id" class="form-label">Product *</label>
                                <select class="form-select" id="prod_id" name="prod_id" required>
                                    <option value="">Select a product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->prod_id }}" {{ old('prod_id') == $product->prod_id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('prod_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantity_produced" class="form-label">Quantity Produced *</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="quantity_produced" 
                                       name="quantity_produced" 
                                       value="{{ old('quantity_produced') }}" 
                                       min="1"
                                       required>
                                @error('quantity_produced')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="production_date" class="form-label">Production Date *</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="production_date" 
                                       name="production_date" 
                                       value="{{ old('production_date', date('Y-m-d')) }}" 
                                       required>
                                @error('production_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="expiration_date" class="form-label">Expiration Date *</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="expiration_date" 
                                       name="expiration_date" 
                                       value="{{ old('expiration_date') }}" 
                                       required>
                                <div id="shelf-life-info" class="text-info small mt-1"></div>
                                @error('expiration_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Production Status *</label>
                                <select class="form-select" id="status" name="status" required onchange="showStatusInfo()">
                                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div id="status-info" class="text-info small mt-1"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Production Notes</label>
                        <textarea class="form-control" 
                                  id="notes" 
                                  name="notes" 
                                  rows="4" 
                                  placeholder="Enter any notes about this production batch...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="alert-admin-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Smart Expiration:</strong> The system automatically calculates expiration dates based on product type. 
                                Bread products: 3 days, Pastries: 2 days, Cakes: 5 days, Cookies: 7 days, etc. 
                                You can adjust the date manually if needed.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="alert-admin-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Note:</strong> When you create this production batch, the quantity will be automatically added to the inventory. A unique batch number will be generated automatically.
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button type="button" class="btn-admin-light" onclick="window.location='{{ route('baker.production.index') }}'">
                            <i class="fas fa-times me-2"></i> Cancel
                        </button>
                        <button type="submit" class="btn-admin-secondary">
                            <i class="fas fa-save me-2"></i> Create Production Batch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('extra-js')
<script>
// Product shelf life mapping (in days)
const productShelfLife = {
    'bread': 3,
    'pastry': 2,
    'cake': 5,
    'cookie': 7,
    'pie': 4,
    'muffin': 3,
    'croissant': 2,
    'donut': 1,
    'default': 3
};

// Auto-calculate expiration date based on production date and product type
function updateExpirationDate() {
    const productionDate = document.getElementById('production_date').value;
    const productSelect = document.getElementById('prod_id');
    
    if (productionDate && productSelect.selectedOptions.length > 0) {
        const productName = productSelect.selectedOptions[0].text.toLowerCase();
        
        // Determine shelf life based on product name keywords
        let shelfLifeDays = productShelfLife.default;
        
        for (const [type, days] of Object.entries(productShelfLife)) {
            if (type !== 'default' && productName.includes(type)) {
                shelfLifeDays = days;
                break;
            }
        }
        
        // Calculate expiration date
        const productionDateObj = new Date(productionDate);
        const expirationDate = new Date(productionDateObj);
        expirationDate.setDate(expirationDate.getDate() + shelfLifeDays);
        
        document.getElementById('expiration_date').value = expirationDate.toISOString().split('T')[0];
        
        // Show shelf life info
        const shelfLifeInfo = document.getElementById('shelf-life-info');
        if (shelfLifeInfo) {
            shelfLifeInfo.textContent = `Shelf life: ${shelfLifeDays} days`;
            shelfLifeInfo.className = 'text-info small mt-1';
        }
    }
}

// Event listeners
document.getElementById('production_date').addEventListener('change', updateExpirationDate);
document.getElementById('prod_id').addEventListener('change', updateExpirationDate);

// Show status information
function showStatusInfo() {
    const statusSelect = document.getElementById('status');
    const statusInfo = document.getElementById('status-info');
    
    if (statusSelect && statusInfo) {
        const status = statusSelect.value;
        let info = '';
        
        switch(status) {
            case 'pending':
                info = 'Production planned but not started yet';
                break;
            case 'in_progress':
                info = 'Currently being produced';
                break;
            case 'completed':
                info = 'Production finished - inventory will be updated automatically';
                break;
            case 'cancelled':
                info = 'Production batch cancelled';
                break;
        }
        
        statusInfo.textContent = info;
    }
}

// Set default production date to today if not set
document.addEventListener('DOMContentLoaded', function() {
    const productionDateInput = document.getElementById('production_date');
    if (!productionDateInput.value) {
        productionDateInput.value = new Date().toISOString().split('T')[0];
    }
    updateExpirationDate();
    showStatusInfo();
});
</script>
@endsection
@endsection