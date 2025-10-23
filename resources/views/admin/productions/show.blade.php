@extends('layouts.admin')
@section('title', 'Production Details')
@section('page-title', 'Production Details')
@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.productions.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-industry"></i> Batch #{{ $production->batch_number }}
        </h1>
        <a href="{{ route('admin.productions.edit', $production) }}" class="btn-admin-secondary ms-3 position-absolute end-0">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
    </div>
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

    <div class="card kpi-card info shadow-sm">
        <div class="card-header">
            <h5 class="mb-0 text-sea-primary">
                <i class="fas fa-info-circle me-2"></i> Production Information
            </h5>
        </div>
        <div class="card-body admin-card-body">
            <table class="table table-borderless">
                <tr>
                    <td width="200"><strong>Batch Number:</strong></td>
                    <td><code>{{ $production->batch_number }}</code></td>
                </tr>
                <tr>
                    <td><strong>Product:</strong></td>
                    <td>
                        @if($production->product)
                            <a href="{{ route('admin.products.show', $production->product) }}" class="text-decoration-none">
                                {{ $production->product->name }} <span class="text-muted">({{ $production->product->sku }})</span>
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Quantity Produced:</strong></td>
                    <td><strong class="text-sea-primary">{{ number_format($production->quantity_produced) }}</strong> units</td>
                </tr>
                <tr>
                    <td><strong>Production Date:</strong></td>
                    <td>{{ $production->production_date?->format('M d, Y') ?? '—' }}</td>
                </tr>
                <tr>
                    <td><strong>Responsible Baker:</strong></td>
                    <td>{{ $production->baker?->full_name ?? ($production->baker?->first_name . ' ' . $production->baker?->last_name) ?? '—' }}</td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td>
                        @if($production->status === 'pending')
                            <span class="badge-admin-role admin"><i class="fas fa-hourglass-half me-1"></i> Pending</span>
                        @elseif($production->status === 'in_progress')
                            <span class="badge-admin-role baker"><i class="fas fa-spinner me-1"></i> In Progress</span>
                        @elseif($production->status === 'completed')
                            <span class="badge-admin-role"><i class="fas fa-check-circle me-1"></i> Completed</span>
                        @elseif($production->status === 'cancelled')
                            <span class="badge-admin-role baker"><i class="fas fa-times-circle me-1"></i> Cancelled</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Notes:</strong></td>
                    <td>{{ $production->notes ?? '—' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

@if($production->status !== 'completed')
<form id="delete-form" action="{{ route('admin.productions.destroy', $production) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endif
@endsection
