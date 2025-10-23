@extends('layouts.baker')

@section('title', 'Baker Dashboard')

@section('content')
@if(Auth::check() && Auth::user()->employee && Auth::user()->employee->status === 'inactive')
<!-- Profile Completion Section -->
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header card-header-sea-secondary">
                <h4 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i> Complete Your Baker Profile
                </h4>
            </div>
            <div class="card-body admin-card-body">
                <p class="text-muted mb-4">Please complete your profile information to access all baker features and start managing production.</p>
                
                <form method="POST" action="{{ route('baker.profile.complete') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="first_name"
                                       name="first_name" 
                                       value="{{ old('first_name', Auth::user()->employee->first_name ?? '') }}" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" 
                                       class="form-control"
                                       id="middle_name"
                                       name="middle_name" 
                                       value="{{ old('middle_name', Auth::user()->employee->middle_name ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" 
                                       class="form-control"
                                       id="last_name"
                                       name="last_name" 
                                       value="{{ old('last_name', Auth::user()->employee->last_name ?? '') }}" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" 
                                       class="form-control"
                                       id="phone"
                                       name="phone" 
                                       value="{{ old('phone', Auth::user()->employee->phone ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" 
                                  id="address" 
                                  name="address" 
                                  rows="3">{{ old('address', Auth::user()->employee->address ?? '') }}</textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn-admin-secondary">
                            <i class="fas fa-check me-2"></i> Complete Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@else
<!-- Baker Dashboard Content -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="flex-grow-1" style="flex-basis:80%;">
        <h1 class="h3 mb-0 text-muted-sea">
            <i class="fas fa-bread-slice me-2"></i> Baker Dashboard
        </h1>
    </div>
    <div class="text-end" style="flex-basis:20%;">
        <button type="button" class="btn-admin-secondary" onclick="window.location='{{ route('baker.production.create') }}'">
            <i class="fas fa-plus me-2"></i> New Production
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div class="row mb-4">
    <div class="col-md-3">
    <a href="{{ route('baker.production.index', ['date_from'=>$today,'date_to'=>$today]) }}" class="text-decoration-none">
    <div class="kpi-card" style="cursor:pointer;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="kpi-value text-start" style="font-size:1.5rem; font-weight:600; min-width:90px;">{{ $todaysProduction ?? 0 }}</div>
                <div class="flex-grow-1 text-end">
                    <div class="kpi-label">Today's Production</div>
                </div>
                <div class="kpi-icon ms-2"><i class="fas fa-calendar-day"></i></div>
            </div>
        </div>
        </a>
    </div>
    
    <div class="col-md-3">
    <a href="{{ route('baker.production.index', ['date_from'=>$weekStart,'date_to'=>$weekEnd]) }}" class="text-decoration-none">
    <div class="kpi-card" style="cursor:pointer;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="kpi-value text-start" style="font-size:1.5rem; font-weight:600; min-width:90px;">{{ $weeklyProduction ?? 0 }}</div>
                <div class="flex-grow-1 text-end">
                    <div class="kpi-label">This Week</div>
                </div>
                <div class="kpi-icon ms-2"><i class="fas fa-calendar-week"></i></div>
            </div>
        </div>
        </a>
    </div>
    
    <div class="col-md-3">
    <a href="{{ route('baker.production.index', ['date_from'=>$monthStart,'date_to'=>$monthEnd]) }}" class="text-decoration-none">
    <div class="kpi-card" style="cursor:pointer;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="kpi-value text-start" style="font-size:1.5rem; font-weight:600; min-width:90px;">{{ $monthlyProduction ?? 0 }}</div>
                <div class="flex-grow-1 text-end">
                    <div class="kpi-label">This Month</div>
                </div>
                <div class="kpi-icon ms-2"><i class="fas fa-calendar-alt"></i></div>
            </div>
        </div>
        </a>
    </div>
    
    <div class="col-md-3">
    <a href="{{ route('baker.inventory.index') }}" class="text-decoration-none">
    <div class="kpi-card" style="cursor:pointer;" onclick="window.location='{{ route('baker.inventory.index', ['stock_level'=>'low']) }}'">
            <div class="d-flex align-items-center justify-content-between">
                <div class="kpi-value text-start" style="font-size:1.5rem; font-weight:600; min-width:90px;">{{ $lowStockProducts->count() ?? 0 }}</div>
                <div class="flex-grow-1 text-end">
                    <div class="kpi-label">Low Stock Alerts</div>
                </div>
                <div class="kpi-icon ms-2"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
        </a>
    </div>
</div>

<div class="row">
    <!-- Recent Production Batches -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-industry me-2"></i> Recent Production Batches
                </h5>
                <a href="{{ route('baker.production.index') }}" class="btn-admin-secondary btn-sm">
                    <i class="fas fa-eye me-1"></i> View All
                </a>
            </div>
            <div class="card-body admin-card-body">
                @if(isset($recentBatches) && $recentBatches->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Batch #</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBatches as $batch)
                                <tr>
                                    <td><strong>{{ $batch->batch_number }}</strong></td>
                                    <td>{{ $batch->product->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($batch->quantity_produced) }} pcs</td>
                                    <td>
                                        @php $dateVal = $batch->production_date; @endphp
                                        @if(is_string($dateVal) && strtotime($dateVal))
                                            {{ date('M d, Y', strtotime($dateVal)) }}
                                        @else
                                            {{ $dateVal }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($batch->status === 'pending')
                                            <span class="badge-admin-role admin">
                                                <i class="fas fa-clock me-1"></i> Pending
                                            </span>
                                        @elseif($batch->status === 'in_progress')
                                            <span class="badge-admin-role manager">
                                                <i class="fas fa-spinner me-1"></i> In Progress
                                            </span>
                                        @elseif($batch->status === 'completed')
                                            <span class="badge-admin-role">
                                                <i class="fas fa-check-circle me-1"></i> Completed
                                            </span>
                                        @elseif($batch->status === 'cancelled')
                                            <span class="badge bg-danger text-white">
                                                <i class="fas fa-times-circle me-1"></i> Cancelled
                                            </span>
                                        @else
                                            <span class="badge-admin-role baker">
                                                {{ ucfirst(str_replace('_', ' ', $batch->status)) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-industry fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No production batches yet. <a href="{{ route('baker.production.create') }}" class="text-decoration-none">Create your first batch</a>!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i> Low Stock Alerts
                </h5>
                <a href="{{ route('baker.inventory.index') }}" class="btn-admin-secondary btn-sm">
                    <i class="fas fa-boxes me-1"></i> View Inventory
                </a>
            </div>
            <div class="card-body admin-card-body">
                @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
                    @foreach($lowStockProducts as $product)
                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-start border-warning border-3">
                        <div>
                            <strong>{{ $product->name }}</strong><br>
                            <small class="text-muted">Stock: {{ $product->inventoryStock->quantity ?? 0 }} units</small>
                        </div>
                        <div>
                            <span class="badge badge-admin-role manager">Low Stock</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0">All products have adequate stock levels.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('extra-js')
<script>
// Inactivity timeout in milliseconds (edit this value to change the timeout)
const INACTIVITY_LIMIT_MS = 30000; // 30 seconds for demo; set to 300000 for 5 minutes
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