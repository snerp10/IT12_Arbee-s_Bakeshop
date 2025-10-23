@extends('layouts.cashier')

@section('title', 'Point of Sale')

@section('extra-css')
<style>
    .pos-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        height: calc(100vh - 250px);
    }

    .products-section {
        overflow-y: auto;
    }

    .cart-section {
        position: sticky;
        top: 20px;
    }

    @media (max-width: 992px) {
        .pos-container {
            grid-template-columns: 1fr;
            height: auto;
        }
        
        .cart-section {
            position: relative;
            top: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-cash-register"></i>
        Point of Sale
    </h1>
    <p class="page-subtitle">Select products and process sales transactions</p>
</div>

<div class="pos-container">
    <!-- Products Grid -->
    <div class="products-section">
        <div class="content-card">
            <div class="content-card-header d-flex justify-content-between align-items-center">
                <h3 class="content-card-title mb-0">
                    <i class="fas fa-th-large"></i>
                    Available Products
                </h3>
                <input type="text" id="product-search" class="form-control-cashier" style="max-width: 420px; width: 100%;" placeholder="Search products...">
            </div>

            <div class="cashier-product-grid" id="products-grid">
                @forelse($products as $product)
                    <div class="product-item" data-name="{{ strtolower($product->name) }}">
                    <div class="cashier-product-item"
                        onclick='addToCart({{ $product->prod_id }}, @json($product->name), {{ $product->price }}, {!! ($product->inventoryStock && $product->inventoryStock->quantity !== null) ? $product->inventoryStock->quantity : 'null' !!})'>
                            <div class="product-header">
                                <h6 class="mb-1">{{ $product->name }}</h6>
                            </div>
                            <div class="product-details">
                                <div class="price text-primary">₱{{ number_format($product->price, 2) }}</div>
                                <div class="stock-info mt-1">
                                    @if((optional($product->inventoryStock)->quantity ?? 0) > 0)
                                        <span class="badge-chip badge-sea-light">
                                            <i class="fas fa-check-circle me-1"></i>
                                            {{ optional($product->inventoryStock)->quantity }} in stock
                                        </span>
                                    @else
                                        <span class="badge-chip badge-sea-info">
                                            <i class="fas fa-times-circle me-1"></i>
                                            Out of stock
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No products available</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Cart Section -->
    <div class="cart-section">
        <div class="content-card">
            <div class="content-card-header">
                <h3 class="content-card-title">
                    <i class="fas fa-shopping-cart"></i>
                    Current Order
                </h3>
            </div>

            <div id="cart-items" class="mb-3" style="max-height: 300px; overflow-y: auto;">
                <div class="text-center text-muted py-4" id="empty-cart">
                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                    <p class="mb-0">Cart is empty</p>
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <strong id="subtotal">₱0.00</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">VAT ({{ config('vat.vat_rate', 12) }}%):</span>
                    <strong id="vat-amount">₱0.00</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <h5 class="mb-0">Total:</h5>
                    <h5 class="mb-0 text-primary" id="total">₱0.00</h5>
                </div>
            </div>

            <hr>

            <!-- Payment Section -->
            <div class="mb-3">
                <label class="form-label-cashier">Cash Received</label>
                <div class="input-group">
                    <span class="input-group-text">₱</span>
                    <input type="number" 
                           id="cash-received" 
                           class="form-control-cashier" 
                           placeholder="0.00" 
                           step="0.01" 
                           min="0"
                           oninput="calculateChange()">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label-cashier">Change</label>
                <div class="p-3 text-center bg-light border rounded">
                    <h3 class="mb-0 fw-bold" id="change-amount">₱0.00</h3>
                </div>
                <small id="change-warning" class="text-danger d-none mt-1 d-block">
                    <i class="fas fa-exclamation-triangle me-1"></i> Insufficient cash
                </small>
            </div>

            <hr>

            <form id="checkout-form" action="{{ route('cashier.pos.checkout') }}" method="POST">
                @csrf
                <input type="hidden" name="cart" id="cart-data">
                <input type="hidden" name="cash_given" id="cash-given-hidden">
                <input type="hidden" name="change" id="change-hidden">
                
                <div class="mb-3">
                    <label class="form-label-cashier">Order Type</label>
                    <select name="order_type" class="form-control-cashier" required>
                        <option value="dine_in">Dine In</option>
                        <option value="takeout" selected>Takeout</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label-cashier">Notes (Optional)</label>
                    <textarea name="notes" class="form-control-cashier" rows="2" placeholder="Order notes..."></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn-admin-primary" id="checkout-btn" disabled>
                        <i class="fas fa-credit-card me-1"></i>
                        Complete Sale
                    </button>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn-admin-light d-flex flex-row align-items-center justify-content-center" onclick="clearCart()">
                            <i class="fas fa-trash me-2"></i>
                            <span>Clear Cart</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
let cart = [];
const VAT_RATE = {{ config('vat.vat_rate', 12) }};

// Ensure hidden fields are updated before form submit
document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            // Always update totals and hidden fields before submit
            updateTotals();
        });
    }
});

function addToCart(prodId, prodName, price, stock) {
    console.log('✅ Adding to cart:', {prodId, prodName, price, stock});
    
    // Parse values properly
    const productId = parseInt(prodId);
    const productPrice = parseFloat(price);
    const productStock = stock === null || stock === undefined ? 999999 : parseInt(stock);
    
    // Block if out of stock
    if (productStock <= 0) {
        alert('Product is out of stock!');
        return;
    }

    // Find existing item
    const existingItem = cart.find(item => parseInt(item.prod_id) === productId);
    
    if (existingItem) {
        // Item exists - increase quantity
        if (productStock !== 999999 && existingItem.quantity >= productStock) {
            alert('Cannot add more than available stock!');
            return;
        }
        existingItem.quantity++;
        console.log('✅ Increased quantity for existing item');
    } else {
        // New item - add to cart
        cart.push({
            prod_id: productId,
            prod_name: prodName,
            price: productPrice,
            quantity: 1,
            stock: productStock
        });
        console.log('✅ Added new item to cart');
    }

    updateCartDisplay();
    updateTotals();
}

function removeFromCart(prodId) {
    console.log('🗑️ Removing from cart:', prodId);
    const productId = parseInt(prodId);
    const initialLength = cart.length;
    cart = cart.filter(item => parseInt(item.prod_id) !== productId);
    console.log(`✅ Cart length: ${initialLength} → ${cart.length}`);
    updateCartDisplay();
    updateTotals();
}

function updateQuantity(prodId, change) {
    console.log('🔄 Updating quantity:', {prodId, change});
    const productId = parseInt(prodId);
    const item = cart.find(item => parseInt(item.prod_id) === productId);
    
    if (item) {
        const newQuantity = item.quantity + change;
        console.log(`Quantity will change: ${item.quantity} → ${newQuantity}`);
        
        if (newQuantity <= 0) {
            // Remove item if quantity becomes 0 or negative
            removeFromCart(prodId);
        } else if (item.stock === 999999 || newQuantity <= item.stock) {
            // Update quantity if within stock limits
            item.quantity = newQuantity;
            console.log('✅ Quantity updated successfully');
            updateCartDisplay();
            updateTotals();
        } else {
            alert(`Cannot exceed available stock of ${item.stock}!`);
        }
    } else {
        console.log('❌ Item not found in cart');
    }
}

function setQuantity(prodId, value) {
    console.log('📝 Setting quantity manually:', {prodId, value});
    const productId = parseInt(prodId);
    const newQuantity = parseInt(value);
    const item = cart.find(item => parseInt(item.prod_id) === productId);
    
    if (!item) {
        console.log('❌ Item not found in cart');
        return;
    }
    
    // Validate quantity
    if (isNaN(newQuantity) || newQuantity < 1) {
        alert('Please enter a valid quantity (minimum 1)');
        updateCartDisplay(); // Reset to previous value
        return;
    }
    
    if (item.stock !== 999999 && newQuantity > item.stock) {
        alert(`Cannot exceed available stock of ${item.stock}!`);
        updateCartDisplay(); // Reset to previous value
        return;
    }
    
    // Update quantity
    item.quantity = newQuantity;
    console.log('✅ Quantity set to:', newQuantity);
    updateCartDisplay();
    updateTotals();
}

function updateCartDisplay() {
    const cartItemsDiv = document.getElementById('cart-items');
    const emptyCart = document.getElementById('empty-cart');
    
    if (cart.length === 0) {
        // Show empty cart message
        if (emptyCart) {
            emptyCart.style.display = 'block';
            cartItemsDiv.innerHTML = '';
            cartItemsDiv.appendChild(emptyCart);
        } else {
            cartItemsDiv.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-shopping-cart fa-2x mb-2"></i><p class="mb-0">Cart is empty</p></div>';
        }
        document.getElementById('checkout-btn').disabled = true;
    } else {
        // Hide empty cart message
        if (emptyCart) {
            emptyCart.style.display = 'none';
        }
        cartItemsDiv.innerHTML = '';
        
        // Render cart items
        cart.forEach(item => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'cart-item mb-3 p-3 border rounded bg-light';
            itemDiv.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-7">
                        <h5 class="mb-1 fw-bold text-dark">${item.prod_name}</h5>
                        <small class="text-muted">₱${item.price.toFixed(2)} each</small>
                    </div>
                    <div class="col-5 d-flex justify-content-end align-items-center gap-2">
                        <div class="btn-group align-items-center" role="group" style="gap: 8px;">
                            <button type="button" class="btn-admin-light btn-sm d-flex align-items-center justify-content-center"
                                onclick="updateQuantity(${item.prod_id}, -1)"
                                style="width: 36px; height: 36px; padding: 0;">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" 
                                class="form-control form-control-sm text-center" 
                                value="${item.quantity}" 
                                min="1" 
                                max="${item.stock === 999999 ? 9999 : item.stock}"
                                onchange="setQuantity(${item.prod_id}, this.value)"
                                onclick="this.select()"
                                style="width: 60px; height: 36px; padding: 0; margin: 0 5px; font-weight: bold;">
                            <button type="button" class="btn-admin-light btn-sm d-flex align-items-center justify-content-center"
                                onclick="updateQuantity(${item.prod_id}, 1)"
                                style="width: 36px; height: 36px; padding: 0;">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button type="button" class="btn-admin-warning btn-sm d-flex align-items-center justify-content-center"
                                onclick="removeFromCart(${item.prod_id})" title="Remove"
                                style="width: 36px; height: 36px; padding: 0; margin-left: 5px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div style="width: 70%;"></div>
                    <div style="width: 30%;" class="text-end">
                        <strong class="text-success fs-5">₱${(item.price * item.quantity).toFixed(2)}</strong>
                    </div>
                </div>
            `;
            cartItemsDiv.appendChild(itemDiv);
        });
        
        document.getElementById('checkout-btn').disabled = false;
    }
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const vatAmount = +(subtotal * (VAT_RATE / 100)).toFixed(2);
    const total = +(subtotal + vatAmount).toFixed(2);

    document.getElementById('subtotal').textContent = '₱' + subtotal.toFixed(2);
    document.getElementById('vat-amount').textContent = '₱' + vatAmount.toFixed(2);
    document.getElementById('total').textContent = '₱' + total.toFixed(2);

    // Update hidden form data
    document.getElementById('cart-data').value = JSON.stringify(cart);
    // Also update cash_given and change hidden fields
    const cashInput = document.getElementById('cash-received');
    const cashGiven = parseFloat(cashInput.value) || 0;
    const change = +(cashGiven - total).toFixed(2);
    document.getElementById('cash-given-hidden').value = cashGiven.toFixed(2);
    document.getElementById('change-hidden').value = change >= 0 ? change.toFixed(2) : '0.00';

    // Recalculate change when total changes
    calculateChange();

    console.log('💰 Totals updated:', {subtotal: subtotal.toFixed(2), vat: vatAmount.toFixed(2), total: total.toFixed(2), cash_given: cashGiven.toFixed(2), change: (change >= 0 ? change.toFixed(2) : '0.00'), items: cart.length});
}

function calculateChange() {
    const totalElement = document.getElementById('total');
    const cashInput = document.getElementById('cash-received');
    const changeDisplay = document.getElementById('change-amount');
    const checkoutBtn = document.getElementById('checkout-btn');
    const warningText = document.getElementById('change-warning');
    
    if (!totalElement || !cashInput || !changeDisplay || !checkoutBtn) return;
    
    // Get total amount (remove ₱ and parse)
    const total = parseFloat(totalElement.textContent.replace('₱', '').replace(',', ''));
    const cashReceived = parseFloat(cashInput.value) || 0;
    
    // Calculate change
    const change = cashReceived - total;
    
    if (cashReceived === 0) {
        // No cash entered yet
        changeDisplay.textContent = '₱0.00';
        changeDisplay.style.color = '#6c757d';
        warningText.classList.add('d-none');
        checkoutBtn.disabled = cart.length === 0;
    } else if (change < 0) {
        // Insufficient cash
        changeDisplay.textContent = '₱' + Math.abs(change).toFixed(2);
        changeDisplay.style.color = '#dc3545';
        warningText.classList.remove('d-none');
        checkoutBtn.disabled = true;
    } else {
        // Sufficient cash
        changeDisplay.textContent = '₱' + change.toFixed(2);
        changeDisplay.style.color = '#28a745';
        warningText.classList.add('d-none');
        checkoutBtn.disabled = cart.length === 0;
    }
}

function clearCart() {
    if (confirm('Are you sure you want to clear the cart?')) {
        cart = [];
        console.log('🧹 Cart cleared');
        
        // Reset cash input
        const cashInput = document.getElementById('cash-received');
        if (cashInput) cashInput.value = '';
        
        updateCartDisplay();
        updateTotals();
    }
}

// Product search functionality
document.getElementById('product-search').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const productItems = document.querySelectorAll('.product-item');
    
    productItems.forEach(item => {
        const name = item.getAttribute('data-name') || '';
        if (name.includes(searchTerm)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});

// Initialize cart when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 POS System initialized');
    updateCartDisplay();
    updateTotals();
});

// Initialize immediately
updateCartDisplay();
updateTotals();
</script>
@endsection
