@php($editing = isset($production))
@php($isCompleted = $editing && $production->status === 'completed')
<div class="card kpi-card info shadow">
    <div class="card-header">
        <h5 class="mb-0 text-sea-primary">
            <i class="fas fa-industry me-2"></i> Production Details
        </h5>
    </div>
    <div class="card-body admin-card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Product</label>
        @if($isCompleted)
          <input type="text" class="form-control" value="{{ $production->product->sku }} - {{ $production->product->name }}" readonly style="background-color: #f8f9fa;">
          <input type="hidden" name="prod_id" value="{{ $production->prod_id }}">
          <small class="form-text text-muted">Cannot change product for completed production</small>
        @else
          <select name="prod_id" class="form-select" id="prod_id" required>
            <option value="">Select product</option>
            @foreach($products as $p)
              <option value="{{ $p->prod_id }}" data-shelf-life="{{ $p->shelf_life ?? '' }}" @selected(old('prod_id', $production->prod_id ?? '')==$p->prod_id)>{{ $p->sku }} - {{ $p->name }}</option>
            @endforeach
          </select>
        @endif
      </div>
      <div class="col-md-3">
        <label class="form-label">Batch #</label>
        @if($editing)
          <input type="text" name="batch_number" class="form-control" value="{{ old('batch_number', $production->batch_number ?? '') }}" readonly style="background-color: #f8f9fa;">
          <small class="form-text text-muted">Batch number cannot be changed</small>
        @else
          <input type="text" name="batch_number" class="form-control" value="{{ old('batch_number', $nextBatchNumber ?? '') }}" readonly style="background-color: #f8f9fa;">
          <small class="form-text text-muted">Auto-generated batch number</small>
        @endif
      </div>
      <div class="col-md-3">
        <label class="form-label">Quantity Produced</label>
        @if($isCompleted)
          <input type="text" class="form-control" value="{{ number_format($production->quantity_produced) }}" readonly style="background-color: #f8f9fa;">
          <input type="hidden" name="quantity_produced" value="{{ $production->quantity_produced }}">
          <small class="form-text text-muted">Cannot change quantity for completed production</small>
        @else
          <input type="number" name="quantity_produced" class="form-control" value="{{ old('quantity_produced', $production->quantity_produced ?? 1) }}" min="1" required>
        @endif
      </div>
      <div class="col-md-4">
        <label class="form-label">Production Date</label>
   <input type="date" name="production_date" id="production_date" class="form-control" 
     value="{{ old('production_date', optional($production->production_date ?? now())->format('Y-m-d')) }}" 
               min="{{ now()->format('Y-m-d') }}" 
               required>
        <small class="form-text text-muted">Cannot select past dates</small>
      </div>
      <div class="col-md-4">
        <label class="form-label d-block">Produce at</label>
        <div class="form-control" style="background-color:#f8f9fa" readonly>
            Barangay 76-A, New Matina, Bucana, Davao City, Branch
        </div>
        <small class="form-text text-muted">Production location</small>
      </div>
      <div class="col-md-4">
   <label class="form-label">Expiration Date</label>
   <input type="date" name="expiration_date" id="expiration_date" class="form-control"
     value="{{ old('expiration_date', optional($production->expiration_date ?? null)?->format('Y-m-d')) }}">
   <small class="form-text text-muted">Defaults based on product shelf life</small>
      </div>
      <div class="col-md-4">
        <label class="form-label">Responsible Baker</label>
  <select name="baker_id" class="form-select" required>
          <option value="">Select baker</option>
          @foreach($employees as $employee)
            <option value="{{ $employee->emp_id }}" @selected(old('baker_id', $production->baker_id ?? '')==$employee->emp_id)>{{ $employee->full_name ?? ($employee->first_name.' '.$employee->last_name) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Status</label>
        @if($isCompleted)
          <input type="text" class="form-control" value="Completed" readonly style="background-color: #f8f9fa;">
          <input type="hidden" name="status" value="completed">
          <small class="form-text text-muted">Cannot change status from completed</small>
        @else
          <select name="status" class="form-select" required>
            @foreach(['pending','in_progress','completed','cancelled'] as $s)
              <option value="{{ $s }}" @selected(old('status', $production->status ?? 'pending')===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
          </select>
        @endif
      </div>
      <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $production->notes ?? '') }}</textarea>
      </div>
        </div>
        <div class="col-12 mx-auto d-flex justify-content-end gap-2 mt-4">
          <button type="reset" class="btn-admin-light w-25">
            <i class="fas fa-times me-2"></i> Cancel
          </button>
          <button type="submit" class="btn-admin-secondary w-25">
            <i class="fas fa-save me-2"></i> {{ $editing ? 'Update Production' : 'Create Production' }}
          </button>
        </div>
    </div>
</div>

@if(!$editing)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default production date to today if not set
    const dateInput = document.querySelector('input[name="production_date"]');
    if (dateInput && !dateInput.value) {
        dateInput.value = new Date().toISOString().split('T')[0];
    }
    
    // Show batch number info tooltip
    const batchInput = document.querySelector('input[name="batch_number"]');
    if (batchInput && batchInput.readOnly) {
        batchInput.title = 'Batch numbers are automatically generated based on today\'s date and sequence';
    }
  // Auto-calc expiration based on shelf life
  function calcExpiration() {
    const prodSelect = document.getElementById('prod_id');
    const shelf = parseInt(prodSelect?.selectedOptions?.[0]?.getAttribute('data-shelf-life') || '');
    const prodDateEl = document.getElementById('production_date');
    const expEl = document.getElementById('expiration_date');
    if (!prodDateEl || !expEl || !prodDateEl.value) return;
    if (!isNaN(shelf) && shelf > 0) {
      const base = new Date(prodDateEl.value + 'T00:00:00');
      base.setDate(base.getDate() + shelf);
      const yyyy = base.getFullYear();
      const mm = String(base.getMonth() + 1).padStart(2, '0');
      const dd = String(base.getDate()).padStart(2, '0');
      expEl.value = `${yyyy}-${mm}-${dd}`;
    }
  }
  document.getElementById('prod_id')?.addEventListener('change', calcExpiration);
  document.getElementById('production_date')?.addEventListener('change', calcExpiration);
  // Initial calc on load if empty
  const expElInit = document.getElementById('expiration_date');
  if (expElInit && !expElInit.value) {
    calcExpiration();
  }
});
</script>
@endif
