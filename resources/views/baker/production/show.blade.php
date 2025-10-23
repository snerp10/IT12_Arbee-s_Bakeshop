@extends('layouts.baker')

@section('title', 'Production Batch Details')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <!-- Back Button (left) -->
    <div class="flex-shrink-0">
        <a href="{{ route('baker.production.index') }}" class="btn-admin-light">
            <i class="fas fa-arrow-left me-2"></i> Back to Production
        </a>
    </div>
    <!-- Title (center) -->
    <div class="flex-grow-1 text-center">
        <h1 class="h3 mb-0 text-muted-sea text-center">
            <i class="fas fa-industry me-2"></i> Production Batch: {{ $production->batch_number }}
        </h1>
        @if($production->expiration_date)
            @php $daysUntilExpiry = (int)\Carbon\Carbon::now()->diffInDays($production->expiration_date, false); @endphp
            @if($daysUntilExpiry < 0)
                <span class="badge badge-admin-role admin">Expired {{ abs($daysUntilExpiry) }} days ago</span>
            @elseif($daysUntilExpiry <= 1)
                <span class="badge badge-admin-role manager">Expires in {{ $daysUntilExpiry }} days</span>
            @else
                <span class="badge badge-admin-role baker">Expires in {{ $daysUntilExpiry }} days</span>
            @endif
        @endif
    </div>
    <!-- Edit Button (right) -->
    <div class="flex-shrink-0">
        <a href="{{ route('baker.production.edit', $production) }}" class="btn-admin-light">
            <i class="fas fa-edit me-2"></i> Edit Batch
        </a>
    </div>
</div>

<div class="row">
    <!-- Batch Information -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i> Batch Information
                </h5>
            </div>
            <div class="card-body admin-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Batch Number:</strong></td>
                                <td>{{ $production->batch_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Product:</strong></td>
                                <td>{{ $production->product->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Quantity Produced:</strong></td>
                                <td>{{ number_format($production->quantity_produced) }} pieces</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @php
                                        $status = $production->status;
                                        $statusMap = [
                                            'pending' => ['label' => 'Pending', 'icon' => 'hourglass-half', 'class' => 'admin', 'time' => $production->pending_at],
                                            'in_progress' => ['label' => 'In Progress', 'icon' => 'cogs', 'class' => 'baker', 'time' => $production->in_progress_at],
                                            'completed' => ['label' => 'Completed', 'icon' => 'check-circle', 'class' => 'manager', 'time' => $production->completed_at],
                                            'cancelled' => ['label' => 'Cancelled', 'icon' => 'times-circle', 'class' => '', 'time' => $production->updated_at],
                                        ];
                                        $current = $statusMap[$status] ?? null;
                                    @endphp
                                    @if($current)
                                        <span class="badge-admin-role {{ $current['class'] }}">
                                            <i class="fas fa-{{ $current['icon'] }} me-1"></i> {{ $current['label'] }}
                                        </span>
                                        @if($current['time'])
                                            <span class="text-muted small ms-2">({{ $current['time']->format('M d, Y H:i') }})</span>
                                        @endif
                                    @endif
                                    <!-- Progress bar -->
                                    <div class="mt-2" style="max-width:320px;">
                                        <div class="progress" style="height: 8px;">
                                            @php
                                                $progress = 0;
                                                if($status==='pending') $progress=20;
                                                elseif($status==='in_progress') $progress=60;
                                                elseif($status==='completed') $progress=100;
                                                elseif($status==='cancelled') $progress=0;
                                            @endphp
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Production Date:</strong></td>
                                <td>{{ $production->production_date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Produced At:</strong></td>
                                <td>{{ $production->produced_at ? $production->produced_at->format('F d, Y g:i A') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Expiration Date:</strong></td>
                                <td>{{ $production->expiration_date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Baker:</strong></td>
                                <td>{{ $production->baker->first_name ?? 'N/A' }} {{ $production->baker->last_name ?? '' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($production->notes)
                <div class="mt-4">
                    <h6><strong>Production Notes:</strong></h6>
                    <div class="bg-light p-3 rounded">
                        {{ $production->notes }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Product Information -->
        @if($production->product)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-box me-2"></i> Product Information
                </h5>
            </div>
            <div class="card-body admin-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Product Name:</strong></td>
                                <td>{{ $production->product->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Category:</strong></td>
                                <td>{{ $production->product->category->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Price:</strong></td>
                                <td>₱{{ number_format($production->product->price, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if($production->product->description)
                        <div>
                            <strong>Description:</strong>
                            <p class="mt-2">{{ $production->product->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Action Panel -->
    <div class="col-md-4">
        
        <!-- Batch Statistics -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i> Batch Statistics
                </h5>
            </div>
            <div class="card-body admin-card-body">
                <div class="text-center">
                    <div class="kpi-card">
                        <div class="kpi-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="kpi-value">
                            {{ $production->created_at->diffForHumans() }}
                        </div>
                        <div class="kpi-label">Batch Age</div>
                    </div>
                </div>
                
                @if($production->expiration_date)
                <div class="mt-3">
                    <div class="text-center">
                        @php
                            $daysUntilExpiry = (int)now()->diffInDays($production->expiration_date, false);
                        @endphp
                        
                        @if($daysUntilExpiry < 0)
                            <span class="badge badge-admin-role admin">Expired {{ abs($daysUntilExpiry) }} days ago</span>
                        @elseif($daysUntilExpiry <= 2)
                            <span class="badge badge-admin-role manager">Expires in {{ $daysUntilExpiry }} days</span>
                        @else
                            <span class="badge badge-admin-role baker">Expires in {{ $daysUntilExpiry }} days</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection