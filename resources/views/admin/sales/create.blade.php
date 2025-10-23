@extends('layouts.admin')
@section('title', 'New Sale')
@section('page-title', 'New Sale')

@section('content')
@if ($errors->any())
    <div class="alert alert-admin-danger alert-dismissible fade show mb-4" role="alert">
        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex align-items-center mb-4" style="position:relative;">
    <a href="{{ route('admin.sales.index') }}" class="btn-admin-light me-3 position-absolute start-0">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
    <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
        <i class="fas fa-plus me-2"></i> New Sale Order
    </h1>
</div>

<form method="POST" action="{{ route('admin.sales.store') }}" id="sale-form">
    @csrf
    
    <!-- Order Items Card -->
    <div class="card kpi-card primary shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0 text-sea-primary">
                <i class="fas fa-shopping-cart me-2"></i> Order Items
            </h5>
        </div>
        <div class="card-body admin-card-body">
            <div id="items-container">
                <div class="row g-3 item-row border rounded p-3 mb-3" data-index="0">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Product</label>
                        <select class="form-select product-select" name="items[0][prod_id]" data-index="0" required>
                            <option value="">Select product</option>
                            @foreach($products as $p)
                                <option value="{{ $p->prod_id }}" data-price="{{ $p->price }}" data-stock="{{ $p->inventoryStock->quantity ?? 0 }}">
                                    {{ $p->name }} ({{ $p->sku }}) - ₱{{ number_format($p->price, 2) }} — Stock: {{ $p->inventoryStock->quantity ?? 0 }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small text-muted mt-1">Stock: <span class="stock-label" data-index="0">—</span></div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control quantity-input" name="items[0][quantity]" value="1" min="1" data-index="0" required>
                        <div class="invalid-feedback qty-error" data-index="0">Quantity exceeds available stock.</div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label class="form-label">Unit Price</label>
                        <input type="number" step="0.01" class="form-control unit-price-input" name="items[0][unit_price]" value="0" min="0" data-index="0" readonly required>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6">
                        <label class="form-label">Subtotal</label>
                        <input type="text" class="form-control line-total" value="₱0.00" readonly>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <label class="form-label">Action</label>
                        <button type="button" class="btn-admin-light btn-sm w-100 remove-item" disabled>
                            <i class="fas fa-trash me-1"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center">
                <button type="button" class="btn-admin-secondary" id="add-item">
                    <i class="fas fa-plus me-2"></i> Add Another Item
                </button>
                <div class="text-end">
                    <h5 class="mb-0 text-sea-primary">Order Total: <span id="order-total">₱0.00</span></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Information Card -->
    <div class="card kpi-card primary shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0 text-sea-primary">
                <i class="fas fa-credit-card me-2"></i> Payment Information
            </h5>
        </div>
        <div class="card-body admin-card-body">
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">Cashier <span class="text-danger">*</span></label>
                    <select name="cashier_id" class="form-select" required>
                        <option value="">Select cashier</option>
                        @isset($cashiers)
                            @foreach($cashiers as $emp)
                                <option value="{{ $emp->emp_id }}" @selected(old('cashier_id')==$emp->emp_id)>
                                    {{ $emp->full_name ?? ($emp->first_name.' '.$emp->last_name) }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label">Subtotal</label>
                    <input type="text" class="form-control" id="subtotal-display" value="₱0.00" readonly style="background-color: #f8f9fa;">
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label">VAT ({{ config('vat.vat_rate', 12) }}%)</label>
                    <input type="text" class="form-control" id="vat-display" value="₱0.00" readonly style="background-color: #f8f9fa;">
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label">Total Amount <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="total-display" value="₱0.00" readonly style="background-color: #f8f9fa; font-weight: bold;">
                    <input type="hidden" name="total_amount" id="total-amount-hidden" value="0">
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label">Amount Paid <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" name="amount_paid" id="amount-paid" value="{{ old('amount_paid', 0) }}" min="0" required>
                    <div class="invalid-feedback" id="payment-error">
                        Amount paid must be at least equal to the total amount.
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label">Change</label>
                    <input type="text" class="form-control" id="change-display" value="₱0.00" readonly style="background-color: #f8f9fa;">
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label">Payment Method</label>
                    <input type="text" class="form-control" value="Cash" readonly disabled>
                    <input type="hidden" name="payment_method" value="cash">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" rows="2" placeholder="Optional notes about this sale...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Actions -->
    <div class="col-12 mx-auto d-flex justify-content-end gap-2">
        <button type="reset" class="btn-admin-light w-25">
            <i class="fas fa-times me-2"></i> Cancel
        </button>
        <button type="submit" class="btn-admin-primary w-25" id="submit-sale" disabled>
            <i class="fas fa-save me-2"></i> Complete Sale
        </button>
    </div>
</form>

@push('scripts')
<script>
(function(){
    let itemIndex = 1;
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');
    const orderTotalEl = document.getElementById('order-total');
    const totalDisplayEl = document.getElementById('total-display');
    const totalHiddenEl = document.getElementById('total-amount-hidden');
    const amountPaidEl = document.getElementById('amount-paid');
    const changeDisplayEl = document.getElementById('change-display');
    const submitButton = document.getElementById('submit-sale');
    const paymentError = document.getElementById('payment-error');

    function getRowStock(index) {
        const select = document.querySelector(`.product-select[data-index="${index}"]`);
        const opt = select ? select.selectedOptions[0] : null;
        return opt ? parseFloat(opt.dataset.stock || '0') : 0;
    }

    function setStockLabel(index) {
        const stock = getRowStock(index);
        const label = document.querySelector(`.stock-label[data-index="${index}"]`);
        if (label) label.textContent = (!isNaN(stock) && stock >= 0) ? stock : '—';
    }

    function validateQuantityAgainstStock(index) {
        const row = document.querySelector(`.item-row[data-index="${index}"]`);
        if (!row) return true;
        const qtyInput = row.querySelector('.quantity-input');
        const errorEl = row.querySelector('.qty-error');
        const stock = getRowStock(index);
        const qty = parseFloat(qtyInput.value || '0');
        const productSelected = !!document.querySelector(`.product-select[data-index="${index}"]`)?.value;
        const valid = !productSelected || (stock > 0 && qty <= stock);
        if (!valid) {
            qtyInput.classList.add('is-invalid');
            if (errorEl) errorEl.style.display = 'block';
        } else {
            qtyInput.classList.remove('is-invalid');
            if (errorEl) errorEl.style.display = 'none';
        }
        return valid;
    }

    function validateAllStocks() {
        let ok = true;
        document.querySelectorAll('.item-row').forEach(r => {
            const idx = r.getAttribute('data-index');
            if (!validateQuantityAgainstStock(idx)) ok = false;
        });
        return ok;
    }

    // Product options template
    const productOptions = `
        <option value="">Select product</option>
        @foreach($products as $p)
            <option value="{{ $p->prod_id }}" data-price="{{ $p->price }}" data-stock="{{ $p->inventoryStock->quantity ?? 0 }}">{{ $p->name }} ({{ $p->sku }}) - ₱{{ number_format($p->price, 2) }} — Stock: {{ $p->inventoryStock->quantity ?? 0 }}</option>
        @endforeach
    `;

    function createItemRow(index) {
        const div = document.createElement('div');
        div.className = 'row g-3 item-row border rounded p-3 mb-3';
        div.setAttribute('data-index', index);
        
        div.innerHTML = `
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Product</label>
                <select class="form-select product-select" name="items[${index}][prod_id]" data-index="${index}" required>
                    ${productOptions}
                </select>
                <div class="form-text small text-muted mt-1">Stock: <span class="stock-label" data-index="${index}">—</span></div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control quantity-input" name="items[${index}][quantity]" value="1" min="1" data-index="${index}" required>
                <div class="invalid-feedback qty-error" data-index="${index}">Quantity exceeds available stock.</div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label">Unit Price</label>
                <input type="number" step="0.01" class="form-control unit-price-input" name="items[${index}][unit_price]" value="0" min="0" data-index="${index}" readonly required>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6">
                <label class="form-label">Subtotal</label>
                <input type="text" class="form-control line-total" value="₱0.00" readonly>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <label class="form-label">Action</label>
                <button type="button" class="btn-admin-light btn-sm w-100 remove-item">
                    <i class="fas fa-trash me-1"></i> Remove
                </button>
            </div>
        `;
        
        return div;
    }

    function updateLineTotal(index) {
        const row = document.querySelector(`.item-row[data-index="${index}"]`);
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
        const lineTotal = quantity * unitPrice;
        
        row.querySelector('.line-total').value = '₱' + lineTotal.toFixed(2);
        updateOrderTotal();
    }

    function updateOrderTotal() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
            subtotal += quantity * unitPrice;
        });

        const vatRate = {{ config('vat.vat_rate', 12) }};
        const vatAmount = +(subtotal * (vatRate / 100)).toFixed(2);
        const total = +(subtotal + vatAmount).toFixed(2);

        document.getElementById('subtotal-display').value = '₱' + subtotal.toFixed(2);
        document.getElementById('vat-display').value = '₱' + vatAmount.toFixed(2);
        orderTotalEl.textContent = '₱' + total.toFixed(2);
        totalDisplayEl.value = '₱' + total.toFixed(2);
        totalHiddenEl.value = total.toFixed(2);

        updateChange();
        validatePayment();
    }

    function updateChange() {
        const total = parseFloat(totalHiddenEl.value) || 0;
        const paid = parseFloat(amountPaidEl.value) || 0;
        const change = Math.max(0, paid - total);
        
        changeDisplayEl.value = '₱' + change.toFixed(2);
    }

    function validatePayment() {
        const total = parseFloat(totalHiddenEl.value) || 0;
        const paid = parseFloat(amountPaidEl.value) || 0;
        const hasItems = document.querySelectorAll('.item-row').length > 0;
        
        const isValid = total > 0 && paid >= total && hasItems;
        
        submitButton.disabled = !isValid;
        
        if (paid < total && paid > 0) {
            amountPaidEl.classList.add('is-invalid');
            paymentError.style.display = 'block';
        } else {
            amountPaidEl.classList.remove('is-invalid');
            paymentError.style.display = 'none';
        }
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-item');
            removeBtn.disabled = rows.length === 1;
        });
    }

    // Event delegation for dynamic elements
    container.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const index = e.target.dataset.index;
            const selectedOption = e.target.selectedOptions[0];
            const price = selectedOption ? selectedOption.dataset.price : 0;
            const priceInput = document.querySelector(`.unit-price-input[data-index="${index}"]`);
            priceInput.value = price;
            setStockLabel(index);
            validateQuantityAgainstStock(index);
            updateLineTotal(index);
        }
        
        if (e.target.classList.contains('quantity-input') || e.target.classList.contains('unit-price-input')) {
            const index = e.target.dataset.index;
            // If unit-price input triggered change, re-sync it from selected product to avoid manual edits
            if (e.target.classList.contains('unit-price-input')) {
                const productSelect = document.querySelector(`.product-select[data-index="${index}"]`);
                const selectedOption = productSelect ? productSelect.selectedOptions[0] : null;
                const price = selectedOption ? selectedOption.dataset.price : 0;
                e.target.value = price;
            }
            if (e.target.classList.contains('quantity-input')) {
                validateQuantityAgainstStock(index);
            }
            updateLineTotal(index);
        }
    });

    // Also guard against typing into readonly via some browsers: block keypress
    container.addEventListener('keydown', function(e){
        if (e.target.classList.contains('unit-price-input')) {
            e.preventDefault();
        }
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const row = e.target.closest('.item-row');
            row.remove();
            updateOrderTotal();
            updateRemoveButtons();
        }
    });

    addButton.addEventListener('click', function() {
        const newRow = createItemRow(itemIndex);
        container.appendChild(newRow);
        setStockLabel(itemIndex);
        validateQuantityAgainstStock(itemIndex);
        itemIndex++;
        updateRemoveButtons();
    });

    amountPaidEl.addEventListener('input', function() {
        updateChange();
        validatePayment();
    });

    // Initialize
    updateRemoveButtons();
    document.querySelectorAll('.item-row').forEach(r => {
        const idx = r.getAttribute('data-index');
        setStockLabel(idx);
        validateQuantityAgainstStock(idx);
    });
    validatePayment();
})();
</script>
@endpush
@endsection