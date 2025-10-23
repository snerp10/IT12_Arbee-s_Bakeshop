@extends('layouts.admin')
@section('title', 'Analytics Dashboard')
@section('page-title', 'Analytics Dashboard')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-muted-sea">
      <i class="fas fa-chart-line me-2"></i> Analytics Dashboard
    </h1>
  </div>

  <div class="card admin-filter-card shadow mb-4">
    <div class="card-body admin-card-body">
      <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label">From</label>
          <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">To</label>
          <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn-admin-secondary"><i class="fas fa-filter me-1"></i> Apply</button>
        </div>
      </form>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card kpi-card">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div class="text-sea-primary">
            <i class="fas fa-peso-sign fa-2x"></i>
          </div>
          <div class="flex-grow-1 text-end">
            <h3 class="mb-0">{{ '₱' . number_format($totalSales, 2) }}</h3>
            <p class="text-muted mb-0">Total Sales</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card kpi-card">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div class="text-sea-primary">
            <i class="fas fa-shopping-cart fa-2x"></i>
          </div>
          <div class="flex-grow-1 text-end">
            <h3 class="mb-0">{{ number_format($totalTransactions) }}</h3>
            <p class="text-muted mb-0">Transactions</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card kpi-card">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div class="text-sea-primary">
            <i class="fas fa-calculator fa-2x"></i>
          </div>
          <div class="flex-grow-1 text-end">
            <h3 class="mb-0">{{ '₱' . number_format($averageTransaction, 2) }}</h3>
            <p class="text-muted mb-0">Avg. Transaction</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card kpi-card">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div class="text-sea-primary">
            <i class="fas fa-box-open fa-2x"></i>
          </div>
          <div class="flex-grow-1 text-end">
            <h3 class="mb-0">{{ number_format($lowStockCount) }}</h3>
            <p class="text-muted mb-0">Low Stock Items</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-lg-8">
      <div class="card kpi-card shadow h-100">
        <div class="card-body admin-card-body">
          <h5 class="mb-3"><i class="fas fa-chart-area me-2"></i> Sales by Day</h5>
          <canvas id="salesByDayChart" height="110"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card kpi-card shadow h-100">
        <div class="card-body admin-card-body">
          <h5 class="mb-3"><i class="fas fa-chart-pie me-2"></i> Production Status</h5>
          <canvas id="productionStatusChart" height="110"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-lg-6">
      <div class="card kpi-card shadow h-100">
        <div class="card-body admin-card-body">
          <h5 class="mb-3"><i class="fas fa-ranking-star me-2"></i> Top Products</h5>
          <canvas id="topProductsChart" height="120"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card kpi-card shadow h-100">
        <div class="card-body admin-card-body">
          <h5 class="mb-3"><i class="fas fa-clock me-2"></i> Sales by Hour</h5>
          <canvas id="salesByHourChart" height="120"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12">
      <div class="card kpi-card text-center">
        <div class="card-body">
          <div class="text-muted">Estimated Inventory Valuation (price × on-hand)</div>
          <h3 class="mt-2">₱{{ number_format($inventoryValuation, 2) }}</h3>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Colors following Sea-style palette
  const seaGreen = '#EEF5DB';
  const seaYellow = '#FFEAA5';
  const seaRed = '#FE5F55';
  const seaBlue = '#3B82F6';

  const salesByDay = @json($chart['salesByDay']);
  const salesByHour = @json($chart['salesByHour']);
  const topProducts = @json($chart['topProducts']);
  const productionStatuses = @json($chart['productionStatuses']);

  // Sales by Day (line)
  new Chart(document.getElementById('salesByDayChart'), {
    type: 'line',
    data: {
      labels: salesByDay.labels,
      datasets: [{
        label: 'Sales',
        data: salesByDay.data,
        fill: false,
        borderColor: seaRed,
        backgroundColor: seaRed,
        tension: 0.3,
      }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
  });

  // Top Products (bar)
  new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
      labels: topProducts.labels,
      datasets: [{
        label: 'Qty Sold',
        data: topProducts.data,
        backgroundColor: seaYellow,
        borderColor: '#D4AF37',
        borderWidth: 1
      }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });

  // Sales by Hour (bar)
  new Chart(document.getElementById('salesByHourChart'), {
    type: 'bar',
    data: {
      labels: salesByHour.labels,
      datasets: [{
        label: 'Sales',
        data: salesByHour.data,
        backgroundColor: seaGreen,
        borderColor: '#9CA3AF',
        borderWidth: 1
      }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });

  // Production Status (doughnut)
  new Chart(document.getElementById('productionStatusChart'), {
    type: 'doughnut',
    data: {
      labels: productionStatuses.labels,
      datasets: [{
        data: productionStatuses.data,
        backgroundColor: [seaRed, seaYellow, seaGreen, seaBlue],
        hoverOffset: 4
      }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
  });
</script>
@endpush
@endsection
