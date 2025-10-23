@extends('layouts.cashier')

@section('title', 'Cashier Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="dashboard-title">
            <i class="fas fa-chart-line me-2"></i>
            Cashier Dashboard
        </h2>
    </div>
    </div>
    
    <!-- Today's Summary KPI Cards -->
    <div class="dashboard-cards mb-4">
    <a href="{{ route('cashier.sales.index') }}" class="text-decoration-none">
    <div class="cashier-kpi-card sales" style="cursor:pointer;" onclick="window.location='{{ route('cashier.sales.index', ['date_from'=>now()->toDateString(),'date_to'=>now()->toDateString()]) }}'">
        <div class="kpi-header d-flex align-items-center justify-content-between">
            <div class="kpi-value text-start" style="font-size:1.5rem; font-weight:600; min-width:90px;">
                ₱{{ number_format($todaysSales, 2) }}
            </div>
            <div class="flex-grow-1 text-end">
                <div class="kpi-label">Today's Sales</div>
                <small class="kpi-date">{{ now()->format('F j, Y') }}</small>
            </div>
            <i class="fas fa-money-bill-wave kpi-icon text-sea-primary ms-2"></i>
            </div>
        </div>
    </a>

    <a href="{{ route('cashier.sales.index') }}" class="text-decoration-none">
    <div class="cashier-kpi-card transactions" style="cursor:pointer;" onclick="window.location='{{ route('cashier.sales.index', ['date_from'=>now()->toDateString(),'date_to'=>now()->toDateString()]) }}'">
            <div class="kpi-header d-flex align-items-center justify-content-between">
                <div class="kpi-value text-start" style="font-size:1.5rem; font-weight:600; min-width:90px;">{{ $todaysTransactions }}</div>
                <div class="flex-grow-1 text-end">
                    <div class="kpi-label">Transactions</div>
                    <small class="kpi-date">Total orders today</small>
                </div>
                <i class="fas fa-shopping-cart kpi-icon text-sea-primary ms-2"></i>
            </div>
        </div>
        </a>

    <a href="{{ route('cashier.sales.index') }}" class="text-decoration-none">
    <div class="cashier-kpi-card average" style="cursor:pointer;" onclick="window.location='{{ route('cashier.sales.index', ['date_from'=>now()->toDateString(),'date_to'=>now()->toDateString()]) }}'">
        <div class="kpi-header d-flex align-items-center justify-content-between">
            <div class="kpi-value text-start" style="font-size:1.5rem; font-weight:600; min-width:90px;">{{ number_format($averageSale, 2) }}</div>
            <div class="flex-grow-1 text-end">
                <div class="kpi-label">Average Sale</div>
                <small class="kpi-date">Per transaction</small>
            </div>
            <i class="fas fa-chart-bar kpi-icon text-sea-primary ms-2"></i>
        </div>
    </div>
    </a>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-section mb-4">
        <div class="quick-actions-grid">
            <a href="{{ route('cashier.pos.index') }}" class="cashier-action-btn">
                <i class="fas fa-cash-register"></i>
                <div class="action-label">New Sale</div>
                <small>Start a new transaction</small>
            </a>
            <a href="{{ route('cashier.sales.index') }}" class="cashier-action-btn">
                <i class="fas fa-history"></i>
                <div class="action-label">Sales History</div>
                <small>View past transactions</small>
            </a>
        </div>
    </div>

<div class="row">
    <div class="col-12">
        <div class="content-card">
            <div class="content-card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 content-card-title">
                    <i class="fas fa-clock"></i>
                    Recent Sales
                </h5>
                <a href="{{ route('cashier.sales.index') }}" class="btn-admin-secondary">
                    <i class="fas fa-arrow-right"></i>
                    View All
                </a>
            </div>

            @if($recentSales->count() > 0)
                <div class="table-responsive w-100">
                    <table class="table-cashier w-100">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date & Time</th>
                                <th>Type</th>
                                <th>Items</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSales as $sale)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $sale->order_number }}</strong>
                                    </td>
                                    <td>
                                        <small>
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ \Carbon\Carbon::parse($sale->order_date)->format('M j, Y') }}
                                            <br>
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $sale->created_at->format('g:i A') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge-chip {{ strtolower($sale->order_type) == 'delivery' ? 'badge-sea-secondary' : (strtolower($sale->order_type) == 'dine_in' ? 'badge-sea-light' : 'badge-sea-primary') }}">
                                            @if($sale->order_type == 'dine_in')
                                                <i class="fas fa-utensils me-1"></i> Dine In
                                            @elseif($sale->order_type == 'takeout')
                                                <i class="fas fa-shopping-bag me-1"></i> Takeout
                                            @else
                                                <i class="fas fa-truck me-1"></i> Delivery
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-chip badge-sea-light">{{ $sale->items->count() }} items</span>
                                    </td>
                                    <td>
                                        <strong>₱{{ number_format($sale->total_amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        @php $status = strtolower($sale->status ?? 'pending'); @endphp
                                        <span class="badge-chip {{ $status === 'completed' ? 'badge-sea-light' : ($status === 'cancelled' ? 'badge-sea-primary' : 'badge-sea-secondary') }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('cashier.sales.show', $sale->so_id) }}" 
                                           class="btn-admin-light">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No sales recorded yet today. Start selling!</p>
                    <a href="{{ route('cashier.pos.index') }}" class="btn-admin-primary mt-3">
                        <i class="fas fa-plus-circle me-2"></i>
                        Create New Sale
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
// Inactivity timeout
const INACTIVITY_LIMIT_MS = 30000;
let inactivityTimer;

function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(triggerTimeout, INACTIVITY_LIMIT_MS);
}

function triggerTimeout() {
    // Show modal/message
    const modal = document.createElement('div');
    modal.id = 'timeout-modal';
    modal.style.position = 'fixed';
    modal.style.top = 0;
    modal.style.left = 0;
    modal.style.width = '100vw';
    modal.style.height = '100vh';
    modal.style.background = 'rgba(0,0,0,0.5)';
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    modal.style.zIndex = 9999;
    modal.innerHTML = `<div style=\"background:#fff;padding:2rem 3rem;border-radius:12px;box-shadow:0 2px 16px #0002;text-align:center;max-width:90vw;\">
        <h2 style='color:#c00;margin-bottom:1rem;'>Session Timeout</h2>
        <p style='margin-bottom:1.5rem;'>You have been inactive for a while.<br><strong>You have to log in due to inactivity.</strong></p>
        <button id='confirm-logout-btn' style='margin-top:1rem;padding:0.5rem 1.5rem;font-size:1.1rem;background:#c00;color:#fff;border:none;border-radius:6px;cursor:pointer;'>Log In Again</button>
        <div class='spinner-border text-danger' role='status' style='display:none;margin-top:1rem;' id='timeout-spinner'></div>
    </div>`;
    document.body.appendChild(modal);
    document.getElementById('confirm-logout-btn').onclick = function() {
        // Show spinner and redirect
        document.getElementById('confirm-logout-btn').disabled = true;
        document.getElementById('timeout-spinner').style.display = 'inline-block';
        setTimeout(() => {
            window.location.href = '/login';
        }, 1200);
    };
}

['mousemove','keydown','mousedown','touchstart'].forEach(evt => {
    window.addEventListener(evt, resetInactivityTimer, true);
});
window.addEventListener('DOMContentLoaded', resetInactivityTimer);
</script>
@endsection