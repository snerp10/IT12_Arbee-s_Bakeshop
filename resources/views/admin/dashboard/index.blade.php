@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')

<!-- Top KPI Widgets -->
<div class="row mb-4">
    <!-- Today Sales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.sales.index') }}" class="text-decoration-none">
            <div class="card kpi-card secondary h-100" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
                <div class="card-body admin-card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="ms-2 me-2">
                                <i class="fas fa-peso-sign kpi-icon" style="font-size: 1.25rem; opacity: 0.7;"></i>
                            </div>
                            <div class="kpi-label text-sea-primary mb-1 me-2" style="font-size: 0.875rem;">Today <br>Sales</div>  
                        </div>
                        <div class="text-end">
                            <div class="kpi-number mb-1" style="font-size: 1.5rem; font-weight: 600; line-height: 1.2; min-width: 90px;">
                                ₱{{ number_format($stats['today_sales'] ?? 0, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- This Month Sales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.sales-reports.index') }}" class="text-decoration-none">
            <div class="card kpi-card secondary h-100" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
                <div class="card-body admin-card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="ms-2 me-2">
                                <i class="fas fa-calendar-alt kpi-icon" style="font-size: 1.25rem; opacity: 0.7;"></i>
                            </div>
                            <div class="kpi-label text-sea-primary mb-1 me-2" style="font-size: 0.875rem;">This Month Sales</div>
                        </div>
                        <div class="text-end">
                            <div class="kpi-number mb-1" style="font-size: 1.5rem; font-weight: 600; line-height: 1.2; min-width: 90px;">
                                ₱{{ number_format($stats['this_month_sales'] ?? 0, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Low Stock Alerts -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.inventory.index') }}" class="text-decoration-none">
            <div class="card kpi-card secondary h-100" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
                <div class="card-body admin-card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="ms-2 me-2">
                                <i class="fas fa-exclamation-triangle kpi-icon" style="font-size: 1.25rem; opacity: 0.7;"></i>
                            </div>
                            <div class="kpi-label text-sea-primary mb-1 me-2" style="font-size: 0.875rem;">Low Stock Alerts</div>
                        </div>
                        <div class="text-end">
                            <div class="kpi-number mb-1" style="font-size: 1.5rem; font-weight: 600; line-height: 1.2; min-width: 90px;">
                                {{ $stats['low_stock_products'] ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Pending Purchase Orders -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.productions.index') }}" class="text-decoration-none">
            <div class="card kpi-card secondary h-100" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
                <div class="card-body admin-card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="ms-2 me-2">
                                <i class="fas fa-shopping-cart kpi-icon" style="font-size: 1.25rem; opacity: 0.7;"></i>
                            </div>
                            <div class="kpi-label text-sea-primary mb-1 me-2" style="font-size: 0.875rem;">Pending POs</div>
                        </div>
                        <div class="text-end">
                            <div class="kpi-number mb-1" style="font-size: 1.5rem; font-weight: 600; line-height: 1.2; min-width: 90px;">
                                {{ $stats['pending_purchases'] ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="card-header border-0" style="background: linear-gradient(135deg, #EEF5DB 0%, #d4e7b8 100%); padding: 1.5rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 fw-bold" style="color: #2d6a4f;">
                            <i class="fas fa-chart-line me-2"></i>Sales Performance Analytics
                        </h5>
                        <small class="text-muted">Multi-dimensional revenue & transaction tracking</small>
                    </div>
                    <div class="btn-group btn-group-sm shadow-sm" role="group">
                        <button type="button" class="btn btn-light active fw-semibold" data-period="7" style="border: none; padding: 0.5rem 1rem;">7 Days</button>
                        <button type="button" class="btn btn-light fw-semibold" data-period="30" style="border: none; padding: 0.5rem 1rem;">30 Days</button>
                        <button type="button" class="btn btn-light fw-semibold" data-period="90" style="border: none; padding: 0.5rem 1rem;">90 Days</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-4" style="background: #fafafa;">
                <canvas id="advancedAnalyticsChart" style="height: 420px; max-height: 420px;"></canvas>
            </div>
            <div class="card-footer border-0" style="background: white; padding: 1.5rem;">
                <div class="row g-4">
                    <div class="col-6 col-lg-3">
                        <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, #d4f1dd 0%, #e8f8ed 100%);">
                            <div class="text-muted small mb-1">Total Revenue (7d)</div>
                            <div class="h4 mb-0 fw-bold" style="color: #27ae60;">
                                ₱{{ number_format(($last7DaysSales->sum('total') ?? 0), 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, #dbe9f4 0%, #e8f2fa 100%);">
                            <div class="text-muted small mb-1">Avg Daily Sales</div>
                            <div class="h4 mb-0 fw-bold" style="color: #3498db;">
                                ₱{{ number_format(($last7DaysSales->count() ? ($last7DaysSales->sum('total') / $last7DaysSales->count()) : 0), 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, #fff4d6 0%, #fff9e6 100%);">
                            <div class="text-muted small mb-1">Peak Day Revenue</div>
                            @php $peakDay = isset($last7DaysSales) && $last7DaysSales->count() ? $last7DaysSales->sortByDesc('total')->first() : null; @endphp
                            <div class="h4 mb-0 fw-bold" style="color: #f39c12;">
                                ₱{{ number_format(($peakDay->total ?? 0), 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, {{ $trend ?? 0 >= 0 ? '#d4f1dd' : '#ffd6d6' }} 0%, {{ $trend ?? 0 >= 0 ? '#e8f8ed' : '#ffe6e6' }} 100%);">
                            <div class="text-muted small mb-1">Growth Trend</div>
                            @php
                                $recent3 = isset($last7DaysSales) && $last7DaysSales->count() >= 3 ? $last7DaysSales->take(-3)->sum('total') : 0;
                                $previous3 = isset($last7DaysSales) && $last7DaysSales->count() >= 6 ? $last7DaysSales->slice(-6, 3)->sum('total') : 0;
                                $trend = $previous3 > 0 ? (($recent3 - $previous3) / $previous3) * 100 : 0;
                            @endphp
                            <div class="h4 mb-0 fw-bold {{ $trend >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-{{ $trend >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>{{ number_format(abs($trend), 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Top Products & Low Stock Row -->
<div class="row mb-4">
    <!-- Top Products (7 days) -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100 shadow-sm" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="card-header border-0" style="background: linear-gradient(135deg, #FE5F55 0%, #ff8a80 100%); padding: 1.25rem;">
                <h6 class="mb-0 text-white fw-bold">
                    <i class="fas fa-trophy me-2"></i>Top Performing Products
                </h6>
                <small class="text-white" style="opacity: 0.9;">Last 7 days performance</small>
            </div>
            <div class="card-body p-0">
                @if(isset($topProducts) && $topProducts->count())
                    <div class="list-group list-group-flush">
                        @foreach($topProducts as $index => $p)
                        <div class="list-group-item border-0 px-4 py-3" style="background: {{ $index % 2 == 0 ? '#ffffff' : '#fafafa' }};">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="me-3" style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #FFEAA5, #ffd54f); display: flex; align-items: center; justify-content: center; font-weight: bold; color: #333;">
                                        #{{ $index + 1 }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold" style="font-size: 0.95rem; color: #2c3e50;">{{ $p->name ?? '—' }}</div>
                                        <small class="text-muted" style="font-size: 0.8rem;">SKU: {{ $p->sku ?? 'N/A' }}</small>
                                    </div>
                                </div>
                                <div class="text-end ms-3">
                                    <div class="badge" style="background: #EEF5DB; color: #2d6a4f; font-size: 0.75rem; padding: 0.4rem 0.8rem; border-radius: 20px; font-weight: 600;">
                                        {{ number_format($p->qty_sold ?? 0) }} sold
                                    </div>
                                    <div class="mt-1" style="font-size: 0.9rem; font-weight: 600; color: #27ae60;">
                                        ₱{{ number_format($p->revenue ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-trophy mb-3" style="font-size: 3rem; color: #e0e0e0;"></i>
                        <p class="text-muted mb-0">No product sales data yet</p>
                        <small class="text-muted">Sales will appear here once recorded</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100 shadow-sm" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="card-header border-0" style="background: linear-gradient(135deg, #FFEAA5 0%, #ffe082 100%); padding: 1.25rem;">
                <h6 class="mb-0 fw-bold" style="color: #5a4a00;">
                    <i class="fas fa-exclamation-triangle me-2"></i>Stock Alerts
                </h6>
                <small style="color: #5a4a00; opacity: 0.8;">Items below reorder level</small>
            </div>
            <div class="card-body p-0">
                @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($lowStockProducts->take(5) as $product)
                        <div class="list-group-item border-0 px-4 py-3 hover-bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="width: 40px; height: 40px; border-radius: 10px; background: {{ ($product->stock_quantity ?? 0) == 0 ? 'linear-gradient(135deg, #FE5F55, #ff8a80)' : 'linear-gradient(135deg, #FFEAA5, #ffd54f)' }}; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-{{ ($product->stock_quantity ?? 0) == 0 ? 'times' : 'exclamation' }} text-white"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold" style="font-size: 0.95rem; color: #2c3e50;">{{ $product->name ?? '—' }}</div>
                                            <small class="text-muted" style="font-size: 0.8rem;">
                                                Current: <span class="fw-semibold {{ ($product->stock_quantity ?? 0) == 0 ? 'text-danger' : 'text-warning' }}">{{ $product->stock_quantity ?? 0 }}</span> | 
                                                Min: {{ $product->minimum_stock ?? 0 }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ms-3">
                                    <span class="fw-bold" style="font-size: 0.85rem; color: {{ ($product->stock_quantity ?? 0) == 0 ? '#b22424' : '#FFC107' }};">
                                        {{ ($product->stock_quantity ?? 0) == 0 ? 'OUT OF STOCK' : 'LOW' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle mb-3" style="font-size: 3rem; color: #27ae60;"></i>
                        <p class="text-success fw-semibold mb-0">All Products Well Stocked</p>
                        <small class="text-muted">No items below reorder level</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Initialization Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('advancedAnalyticsChart');
    if (!ctx) return;

    let currentChart = null;
    let currentPeriod = 7;

    // Function to fetch and update chart data
    async function updateChart(days) {
        try {
            // Show loading state
            const chartContainer = ctx.parentElement;
            chartContainer.style.opacity = '0.6';
            chartContainer.style.pointerEvents = 'none';

            // Fetch new data from server
            const response = await fetch(`{{ route('admin.dashboard') }}?period=${days}&ajax=1`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            
            // Update footer stats
            updateFooterStats(data.salesData);
            
            // Prepare chart data
            const labels = data.salesData.map(d => {
                const date = new Date(d.date);
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            });
            
            const salesRevenue = data.salesData.map(d => parseFloat(d.total || 0));
            const salesTransactions = data.salesData.map(d => parseInt(d.transactions || 0));
            
            const datasets = createDatasets(salesRevenue, salesTransactions, data.categoryData || []);
            
            // Destroy old chart if exists
            if (currentChart) {
                currentChart.destroy();
            }
            
            // Create new chart
            currentChart = new Chart(ctx, {
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: getChartOptions()
            });
            
            // Restore UI
            chartContainer.style.opacity = '1';
            chartContainer.style.pointerEvents = 'auto';
            
        } catch (error) {
            console.error('Error updating chart:', error);
            alert('Failed to load chart data. Please refresh the page.');
            chartContainer.style.opacity = '1';
            chartContainer.style.pointerEvents = 'auto';
        }
    }

    // Create datasets configuration
    function createDatasets(salesRevenue, salesTransactions, categoryData) {
        const categoryColors = [
            { bg: 'rgba(254, 95, 85, 0.6)', border: 'rgba(254, 95, 85, 1)' },
            { bg: 'rgba(255, 234, 165, 0.6)', border: 'rgba(255, 234, 165, 1)' },
            { bg: 'rgba(238, 245, 219, 0.6)', border: 'rgba(238, 245, 219, 1)' },
            { bg: 'rgba(100, 181, 246, 0.6)', border: 'rgba(100, 181, 246, 1)' },
            { bg: 'rgba(171, 71, 188, 0.6)', border: 'rgba(171, 71, 188, 1)' },
        ];

        const datasets = [
            {
                type: 'line',
                label: 'Total Sales (₱)',
                data: salesRevenue,
                borderColor: '#FE5F55',
                backgroundColor: 'rgba(254, 95, 85, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                yAxisID: 'y',
                order: 1,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#FE5F55',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            },
            {
                type: 'bar',
                label: 'Transactions',
                data: salesTransactions,
                backgroundColor: 'rgba(255, 234, 165, 0.7)',
                borderColor: '#FFEAA5',
                borderWidth: 1,
                yAxisID: 'y1',
                order: 2,
                barThickness: 20,
            }
        ];

        // Add category stacked bars if data exists
        if (categoryData && categoryData.length > 0) {
            const totalRevenue = salesRevenue.reduce((a, b) => a + b, 0) || 1;
            
            categoryData.slice(0, 3).forEach((cat, idx) => {
                const catRevenue = parseFloat(cat.revenue || 0);
                const dailyCatRevenue = salesRevenue.map(dayRev => (dayRev / totalRevenue) * catRevenue);
                
                const color = categoryColors[idx % categoryColors.length];
                datasets.push({
                    type: 'bar',
                    label: cat.category_name || cat.name || 'Category ' + (idx + 1),
                    data: dailyCatRevenue,
                    backgroundColor: color.bg,
                    borderColor: color.border,
                    borderWidth: 1,
                    yAxisID: 'y',
                    order: 3 + idx,
                    stack: 'categories',
                    barThickness: 15,
                });
            });
        }

        return datasets;
    }

    // Chart options
    function getChartOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: { size: 11, weight: '500' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    bodySpacing: 6,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.dataset.yAxisID === 'y1') {
                                label += context.parsed.y + ' txns';
                            } else {
                                label += '₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Revenue (₱)', font: { size: 12, weight: '600' } },
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                        },
                        font: { size: 10 }
                    },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Transactions', font: { size: 12, weight: '600' } },
                    ticks: { font: { size: 10 } },
                    grid: { drawOnChartArea: false }
                },
                x: {
                    ticks: { font: { size: 10 } },
                    grid: { display: false }
                }
            }
        };
    }

    // Update footer statistics
    function updateFooterStats(salesData) {
        const totalRevenue = salesData.reduce((sum, d) => sum + parseFloat(d.total || 0), 0);
        const avgDaily = salesData.length ? totalRevenue / salesData.length : 0;
        const peakDay = salesData.reduce((max, d) => parseFloat(d.total || 0) > max ? parseFloat(d.total || 0) : max, 0);
        
        // Calculate trend
        const dataCount = salesData.length;
        const half = Math.floor(dataCount / 2);
        const recent = salesData.slice(half).reduce((sum, d) => sum + parseFloat(d.total || 0), 0);
        const previous = salesData.slice(0, half).reduce((sum, d) => sum + parseFloat(d.total || 0), 0);
        const trend = previous > 0 ? ((recent - previous) / previous) * 100 : 0;
        
        // Update DOM
        const formatCurrency = (val) => '₱' + val.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        document.querySelectorAll('.card-footer .col-6').forEach((col, idx) => {
            const valueEl = col.querySelector('.h4');
            if (!valueEl) return;
            
            switch(idx) {
                case 0: // Total Revenue
                    valueEl.textContent = formatCurrency(totalRevenue);
                    break;
                case 1: // Avg Daily
                    valueEl.textContent = formatCurrency(avgDaily);
                    break;
                case 2: // Peak Day
                    valueEl.textContent = formatCurrency(peakDay);
                    break;
                case 3: // Growth Trend
                    const icon = trend >= 0 ? 'arrow-up' : 'arrow-down';
                    const colorClass = trend >= 0 ? 'text-success' : 'text-danger';
                    valueEl.className = 'h4 mb-0 fw-bold ' + colorClass;
                    valueEl.innerHTML = `<i class="fas fa-${icon} me-1"></i>${Math.abs(trend).toFixed(1)}%`;
                    
                    // Update gradient background
                    const gradientBg = trend >= 0 
                        ? 'linear-gradient(135deg, #d4f1dd 0%, #e8f8ed 100%)'
                        : 'linear-gradient(135deg, #ffd6d6 0%, #ffe6e6 100%)';
                    col.querySelector('.rounded').style.background = gradientBg;
                    break;
            }
        });
    }

    // Period toggle buttons
    document.querySelectorAll('[data-period]').forEach(btn => {
        btn.addEventListener('click', function() {
            const days = parseInt(this.dataset.period);
            if (days === currentPeriod) return;
            
            currentPeriod = days;
            document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            updateChart(days);
        });
    });

    // Initialize with default data (7 days)
    const initialSalesData = @json($last7DaysSales ?? collect());
    const initialCategoryData = @json($salesByCategory ?? collect());
    
    const labels = initialSalesData.map(d => {
        const date = new Date(d.date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    
    const salesRevenue = initialSalesData.map(d => parseFloat(d.total || 0));
    const salesTransactions = initialSalesData.map(d => parseInt(d.transactions || 0));
    
    currentChart = new Chart(ctx, {
        data: {
            labels: labels,
            datasets: createDatasets(salesRevenue, salesTransactions, initialCategoryData)
        },
        options: getChartOptions()
    });
});
</script>
@endsection