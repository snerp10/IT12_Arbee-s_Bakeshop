@extends('layouts.admin')
@section('title', 'Sale Details')
@section('page-title', 'Sale Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.sales.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-eye me-2"></i> Sale Details - {{ $sale->order_number }}
        </h1>
        @if($sale->status === 'completed')
            <a href="{{ route('admin.sales.edit', $sale) }}" class="btn-admin-secondary ms-3 position-absolute end-0">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
        @endif
    </div>

    <!-- Sale Information -->
    <div class="card kpi-card info shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0 text-sea-primary">
                <i class="fas fa-info-circle me-2"></i> Sale Information
            </h5>
        </div>
        <div class="card-body admin-card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr><td><strong>Order Number:</strong></td><td>{{ $sale->order_number }}</td></tr>
                        <tr><td><strong>Order Date:</strong></td><td>{{ $sale->order_date?->format('Y-m-d') }}</td></tr>
                        <tr><td><strong>Order Type:</strong></td><td>{{ ucfirst(str_replace('_',' ', $sale->order_type)) }}</td></tr>
                        <tr><td><strong>Cashier:</strong></td><td>{{ $sale->cashier?->full_name ?? ($sale->cashier?->first_name.' '.$sale->cashier?->last_name) ?? 'N/A' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr><td><strong>Subtotal:</strong></td><td>₱{{ number_format($sale->subtotal, 2) }}</td></tr>
                        <tr><td><strong>VAT ({{ config('vat.vat_rate', 12) }}%):</strong></td><td>₱{{ number_format($sale->vat_amount, 2) }}</td></tr>
                        <tr><td><strong>Total Amount:</strong></td><td class="text-success fw-bold">₱{{ number_format($sale->total_amount, 2) }}</td></tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @switch($sale->status)
                                    @case('completed')
                                        <span class="badge-admin-role baker"><i class="fas fa-check-circle me-1"></i> Completed</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge-admin-role baker"><i class="fas fa-times-circle me-1"></i> Cancelled</span>
                                        @break
                                    @case('refunded')
                                        <span class="badge-admin-role manager"><i class="fas fa-undo me-1"></i> Refunded</span>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                    </table>
                </div>
                @if($sale->notes)
                    <div class="col-12">
                        <strong>Notes:</strong>
                        <p class="mt-2 p-3 bg-light rounded">{{ $sale->notes }}</p>
                    </div>
                @endif
            </div>
            
            
        </div>
    </div>

    <!-- Sale Items -->
    <div class="card kpi-card info shadow">
        <div class="card-header">
            <h5 class="mb-0 text-sea-primary">
                <i class="fas fa-shopping-cart me-2"></i> Sale Items ({{ $sale->items->count() }})
            </h5>
        </div>
        <div class="card-body admin-card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                            <tr>
                                <td>{{ $item->product?->name ?? 'Unknown Product' }}</td>
                                <td>{{ $item->product?->sku ?? 'N/A' }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end fw-bold">₱{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">Total:</th>
                            <th class="text-end text-success">₱{{ number_format($sale->total_amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function handleCancelSale(url) {
    if(confirm('Are you sure you want to cancel this sale?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        var methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
