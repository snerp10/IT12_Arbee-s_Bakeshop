@extends('layouts.cashier')

@section('title', 'Sale Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('cashier.sales.index') }}" class="btn-admin-light">
        <i class="fas fa-arrow-left me-2"></i>
        Back to Sales
    </a>
    <h1 class="h3 mb-0 text-muted-sea">
        <i class="fas fa-receipt"></i>
        Sale Details
    </h1>
    <div></div>
</div>

<div class="row">
    <!-- Order Information -->
    <div class="col-md-8">
        <div class="content-card mb-4">
            <div class="content-card-header">
                <h3 class="content-card-title">
                    <i class="fas fa-info-circle"></i>
                    Order Information
                </h3>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Order Number:</td>
                            <td><strong>{{ $sale->order_number }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Order Date:</td>
                            <td>{{ \Carbon\Carbon::parse($sale->order_date)->format('F j, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Order Time:</td>
                            <td>{{ $sale->created_at->format('g:i A') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Order Type:</td>
                            <td>
                                <span class="badge-chip {{ strtolower($sale->order_type) == 'delivery' ? 'badge-sea-secondary' : (strtolower($sale->order_type) == 'dine_in' ? 'badge-sea-light' : 'badge-sea-primary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $sale->order_type)) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Customer:</td>
                            <td>{{ $sale->customer_name ?? 'Walk-in Customer' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Cashier:</td>
                            <td>{{ $sale->cashier->first_name ?? 'N/A' }} {{ $sale->cashier->last_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status:</td>
                            <td>
                                @php $status = strtolower($sale->status ?? 'pending'); @endphp
                                <span class="badge-chip {{ $status === 'completed' ? 'badge-sea-light' : ($status === 'cancelled' ? 'badge-sea-primary' : 'badge-sea-secondary') }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Payment Method:</td>
                            <td>{{ ucfirst($sale->payment_method ?? 'Cash') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="cashier-card">
            <h5 class="mb-3">
                <i class="fas fa-box me-2"></i>
                Order Items
            </h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <span class="badge-chip badge-sea-light">
                                        {{ $item->product->category->cat_name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                <td>
                                    <span class="badge-chip badge-sea-primary">{{ $item->quantity }}</span>
                                </td>
                                <td>
                                    <strong>₱{{ number_format($item->unit_price * $item->quantity, 2) }}</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end"><strong>Total Items:</strong></td>
                            <td><strong>{{ $sale->items->sum('quantity') }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end"><h5 class="mb-0">Total Amount:</h5></td>
                            <td><h5 class="mb-0 text-primary">₱{{ number_format($sale->total_amount, 2) }}</h5></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="col-md-4">
        <div class="cashier-card mb-4">
            <h5 class="mb-3">
                <i class="fas fa-calculator me-2"></i>
                Order Summary
            </h5>

            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal:</span>
                <strong>₱{{ number_format($sale->subtotal, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">VAT ({{ config('vat.vat_rate', 12) }}%):</span>
                <strong>₱{{ number_format($sale->vat_amount, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Discount:</span>
                <strong>₱{{ number_format($sale->discount ?? 0, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Amount Paid:</span>
                <strong>₱{{ number_format($sale->cash_given, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Change:</span>
                <strong>₱{{ number_format($sale->change, 2) }}</strong>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <h5 class="mb-0">Total:</h5>
                <h5 class="mb-0 text-primary">₱{{ number_format($sale->total_amount, 2) }}</h5>
            </div>

            <hr>

            <div class="d-grid gap-2">
                <a href="{{ route('cashier.sales.receipt.pdf', $sale->so_id) }}" class="btn-admin-warning" target="_blank">
                    <i class="fas fa-file-pdf me-2"></i>
                    Download PDF Receipt
                </a>
                <button class="btn-admin-light" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>
                    Print (Browser)
                </button>
                <button type="button" class="btn-admin-light" onclick="window.location='{{ route('cashier.sales.index') }}'">
                    <i class="fas fa-list me-2"></i>
                    Back to List
                </button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="cashier-card">
            <h6 class="mb-3 text-muted">Quick Stats</h6>
            <div class="mb-3">
                <small class="text-muted d-block">Total Items</small>
                <h4 class="mb-0">{{ $sale->items->sum('quantity') }}</h4>
            </div>
            <div class="mb-3">
                <small class="text-muted d-block">Unique Products</small>
                <h4 class="mb-0">{{ $sale->items->count() }}</h4>
            </div>
            <div>
                <small class="text-muted d-block">Average Item Price</small>
                <h4 class="mb-0">₱{{ number_format($sale->items->avg('unit_price'), 2) }}</h4>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-css')
<style>
@media print {
    .cashier-header,
    .cashier-nav,
    .cashier-footer,
    .cashier-btn,
    .btn {
        display: none !important;
    }
    
    .cashier-card {
        box-shadow: none !important;
        border: 1px solid #ddd;
    }
}
</style>
@endsection
