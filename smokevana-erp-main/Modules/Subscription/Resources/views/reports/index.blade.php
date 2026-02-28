@extends('layouts.app')

@section('title', __('subscription::lang.subscription_reports'))

@section('content')
<style>
    .reports-page {
        background: #f3f3f3;
        min-height: calc(100vh - 120px);
        padding: 20px 0;
    }
    
    .page-header-card {
        background: linear-gradient(90deg, #232f3e 0%, #37475a 100%);
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        box-shadow: 0 10px 40px rgba(240, 147, 251, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .page-header-card h1 {
        color: #fff;
        font-size: 26px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    
    .page-header-card h1 .icon-box {
        background: rgba(0,0,0,0.25);
        border-radius: 12px;
        padding: 10px;
        display: flex;
    }
    
    .date-range-selector {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .date-range-selector input {
        background: #ffffff;
        border: 1px solid #dde2e7;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 14px;
        width: 220px;
    }
    
    .btn-apply {
        background: linear-gradient(90deg, #FFD814 0%, #FCD200 100%);
        color: #131921;
        border: 1px solid #FCD200;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        box-shadow: 0 2px 5px rgba(213, 217, 217, 0.5);
    }
    
    .btn-apply:hover {
        background: linear-gradient(90deg, #F7CA00 0%, #F2C200 100%);
        color: #131921;
    }
    
    /* KPI Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .kpi-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .kpi-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    
    .kpi-card.revenue::after { background: linear-gradient(90deg, #11998e, #38ef7d); }
    .kpi-card.subscribers::after { background: linear-gradient(90deg, #667eea, #764ba2); }
    .kpi-card.churn::after { background: linear-gradient(90deg, #f5576c, #f093fb); }
    .kpi-card.mrr::after { background: linear-gradient(90deg, #4facfe, #00f2fe); }
    .kpi-card.ltv::after { background: linear-gradient(90deg, #ffd700, #ffb700); }
    
    .kpi-card .kpi-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin: 0 auto 16px;
    }
    
    .kpi-card.revenue .kpi-icon { background: linear-gradient(135deg, #11998e, #38ef7d); color: #fff; }
    .kpi-card.subscribers .kpi-icon { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; }
    .kpi-card.churn .kpi-icon { background: linear-gradient(135deg, #f5576c, #f093fb); color: #fff; }
    .kpi-card.mrr .kpi-icon { background: linear-gradient(135deg, #4facfe, #00f2fe); color: #fff; }
    .kpi-card.ltv .kpi-icon { background: linear-gradient(135deg, #00a8e1, #0077a3); color: #fff; }
    
    .kpi-card .kpi-value {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 4px;
    }
    
    .kpi-card .kpi-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    
    .kpi-card .kpi-change {
        font-size: 12px;
        font-weight: 600;
    }
    
    .kpi-card .kpi-change.positive { color: #28a745; }
    .kpi-card .kpi-change.negative { color: #dc3545; }
    
    /* Charts Row */
    .charts-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    
    .chart-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .chart-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .chart-card .card-header h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .chart-card .card-header h4 i {
        color: #00a8e1;
    }
    
    .chart-card .card-body {
        padding: 24px;
    }
    
    .chart-container {
        height: 300px;
        position: relative;
    }
    
    /* Plan Distribution */
    .plan-distribution {
        padding: 0;
    }
    
    .plan-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 24px;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .plan-item:last-child {
        border-bottom: none;
    }
    
    .plan-item .plan-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
    }
    
    .plan-item .plan-info {
        flex: 1;
    }
    
    .plan-item .plan-info h6 {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
    }
    
    .plan-item .plan-info span {
        font-size: 12px;
        color: #6c757d;
    }
    
    .plan-item .plan-count {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a2e;
    }
    
    /* Tables Row */
    .tables-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }
    
    .report-table-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .report-table-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 18px 24px;
    }
    
    .report-table-card .card-header h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .report-table-card .card-header h4 i {
        color: #667eea;
    }
    
    .report-table {
        margin: 0;
    }
    
    .report-table thead th {
        background: #f8f9fa;
        border: none;
        padding: 12px 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #6c757d;
    }
    
    .report-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid #f5f5f5;
        font-size: 13px;
    }
    
    .report-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    @media (max-width: 1400px) {
        .kpi-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 1200px) {
        .charts-row {
            grid-template-columns: 1fr;
        }
        
        .tables-row {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="reports-page">
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="page-header-card">
            <h1>
                <div class="icon-box">
                    <i class="fas fa-chart-bar"></i>
                </div>
                Subscription Analytics
            </h1>
            <div class="date-range-selector">
                <input type="text" id="report_date_range" placeholder="Select date range">
                <button type="button" class="btn btn-apply" id="apply_filter">
                    <i class="fas fa-filter"></i> Apply
                </button>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="kpi-grid">
            <div class="kpi-card revenue">
                <div class="kpi-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="kpi-value">${{ number_format($kpis['total_revenue'] ?? 0, 2) }}</div>
                <div class="kpi-label">Total Revenue</div>
                <div class="kpi-change {{ ($kpis['revenue_change'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-{{ ($kpis['revenue_change'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                    {{ abs($kpis['revenue_change'] ?? 0) }}% vs last period
                </div>
            </div>
            
            <div class="kpi-card subscribers">
                <div class="kpi-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="kpi-value">{{ number_format($kpis['total_subscribers'] ?? 0) }}</div>
                <div class="kpi-label">Active Subscribers</div>
                <div class="kpi-change {{ ($kpis['subscribers_change'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-{{ ($kpis['subscribers_change'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                    {{ abs($kpis['subscribers_change'] ?? 0) }}% vs last period
                </div>
            </div>
            
            <div class="kpi-card churn">
                <div class="kpi-icon">
                    <i class="fas fa-user-minus"></i>
                </div>
                <div class="kpi-value">{{ $kpis['churn_rate'] ?? 0 }}%</div>
                <div class="kpi-label">Churn Rate</div>
                <div class="kpi-change {{ ($kpis['churn_change'] ?? 0) <= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-{{ ($kpis['churn_change'] ?? 0) <= 0 ? 'arrow-down' : 'arrow-up' }}"></i>
                    {{ abs($kpis['churn_change'] ?? 0) }}% vs last period
                </div>
            </div>
            
            <div class="kpi-card mrr">
                <div class="kpi-icon">
                    <i class="fas fa-sync"></i>
                </div>
                <div class="kpi-value">${{ number_format($kpis['mrr'] ?? 0, 2) }}</div>
                <div class="kpi-label">Monthly Recurring Revenue</div>
                <div class="kpi-change {{ ($kpis['mrr_change'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-{{ ($kpis['mrr_change'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                    {{ abs($kpis['mrr_change'] ?? 0) }}% vs last period
                </div>
            </div>
            
            <div class="kpi-card ltv">
                <div class="kpi-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="kpi-value">${{ number_format($kpis['avg_ltv'] ?? 0, 2) }}</div>
                <div class="kpi-label">Avg Customer LTV</div>
                <div class="kpi-change {{ ($kpis['ltv_change'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-{{ ($kpis['ltv_change'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                    {{ abs($kpis['ltv_change'] ?? 0) }}% vs last period
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="charts-row">
            <div class="chart-card">
                <div class="card-header">
                    <h4><i class="fas fa-chart-line"></i> Revenue Trend</h4>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active" data-period="daily">Daily</button>
                        <button type="button" class="btn btn-outline-secondary" data-period="weekly">Weekly</button>
                        <button type="button" class="btn btn-outline-secondary" data-period="monthly">Monthly</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="card-header">
                    <h4><i class="fas fa-chart-pie"></i> Plan Distribution</h4>
                </div>
                <div class="card-body plan-distribution">
                    @php
                        $colors = ['#667eea', '#ffd700', '#11998e', '#f5576c', '#4facfe'];
                    @endphp
                    @foreach($plan_distribution ?? [] as $index => $plan)
                        <div class="plan-item">
                            <div class="plan-color" style="background: {{ $colors[$index % count($colors)] }};"></div>
                            <div class="plan-info">
                                <h6>{{ $plan['name'] }}</h6>
                                <span>{{ $plan['percentage'] }}% of total</span>
                            </div>
                            <div class="plan-count">{{ $plan['count'] }}</div>
                        </div>
                    @endforeach
                    @if(empty($plan_distribution))
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-pie fa-2x mb-2"></i>
                            <p>No subscription data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tables Row --}}
        <div class="tables-row">
            <div class="report-table-card">
                <div class="card-header">
                    <h4><i class="fas fa-trophy"></i> Top Performing Plans</h4>
                </div>
                <div class="card-body p-0">
                    <table class="table report-table">
                        <thead>
                            <tr>
                                <th>Plan</th>
                                <th>Subscribers</th>
                                <th>Revenue</th>
                                <th>Growth</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($top_plans ?? [] as $plan)
                                <tr>
                                    <td><strong>{{ $plan['name'] }}</strong></td>
                                    <td>{{ $plan['subscribers'] }}</td>
                                    <td>${{ number_format($plan['revenue'], 2) }}</td>
                                    <td>
                                        <span class="text-{{ $plan['growth'] >= 0 ? 'success' : 'danger' }}">
                                            <i class="fas fa-{{ $plan['growth'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                            {{ abs($plan['growth']) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="report-table-card">
                <div class="card-header">
                    <h4><i class="fas fa-user-clock"></i> Recent Cancellations</h4>
                </div>
                <div class="card-body p-0">
                    <table class="table report-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Plan</th>
                                <th>Duration</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_cancellations ?? [] as $cancel)
                                <tr>
                                    <td>{{ $cancel['customer_name'] }}</td>
                                    <td>{{ $cancel['plan_name'] }}</td>
                                    <td>{{ $cancel['duration'] }}</td>
                                    <td>{{ $cancel['date'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Date range picker
    if ($.fn.daterangepicker) {
        $('#report_date_range').daterangepicker({
            startDate: moment().subtract(30, 'days'),
            endDate: moment(),
            ranges: {
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last 3 Months': [moment().subtract(3, 'months'), moment()]
            },
            locale: { format: 'YYYY-MM-DD' }
        });
    }

    // Revenue Chart
    var ctx = document.getElementById('revenueChart');
    if (ctx) {
        var revenueChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chart_labels ?? []) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($chart_data ?? []) !!},
                    borderColor: 'rgb(0, 168, 225)',
                    backgroundColor: 'rgba(0, 168, 225, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgb(240, 147, 251)',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Apply filter
    $('#apply_filter').on('click', function() {
        var dates = $('#report_date_range').val();
        window.location.href = '{{ route("subscription.reports.index") }}?date_range=' + encodeURIComponent(dates);
    });
});
</script>
@endsection
