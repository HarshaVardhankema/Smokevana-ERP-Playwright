@extends('layouts.vendor_portal')
@section('title', 'Payments')

@section('css')
<style>
/* Earnings Grid - Amazon Style */
.earnings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.earning-card {
    background: #fff;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    padding: 20px;
    position: relative;
    transition: all 0.2s ease;
}

.earning-card:hover {
    border-color: var(--amazon-orange);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.earning-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin-bottom: 12px;
}

.earning-card-icon.teal { background: #d1ecf1; color: var(--amazon-teal); }
.earning-card-icon.success { background: #d4edda; color: var(--amazon-success); }
.earning-card-icon.warning { background: #fff3cd; color: #856404; }
.earning-card-icon.navy { background: #e2e6ea; color: var(--amazon-navy); }

.earning-card-label {
    font-size: 12px;
    color: var(--gray-600);
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 6px;
}

.earning-card-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1;
}

.earning-card-desc {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 8px;
}

/* Data Table */
.earnings-table {
    width: 100%;
    border-collapse: collapse;
}

.earnings-table thead th {
    background: var(--gray-100);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--gray-700);
    padding: 12px 14px;
    text-align: left;
    border-bottom: 1px solid var(--gray-200);
}

.earnings-table tbody td {
    padding: 14px;
    border-bottom: 1px solid var(--gray-200);
    font-size: 13px;
    color: var(--gray-700);
}

.earnings-table tbody tr:hover {
    background: #fffbf3;
}

.earnings-table tbody tr:last-child td {
    border-bottom: none;
}

.amount-positive {
    color: var(--amazon-success);
    font-weight: 700;
}

/* Chart Container */
.chart-container {
    height: 220px;
    display: flex;
    align-items: flex-end;
    gap: 12px;
    padding: 24px 16px 16px;
    background: var(--gray-50);
    border-radius: 8px;
    margin-bottom: 20px;
}

.chart-bar-wrap {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.chart-bar {
    width: 100%;
    max-width: 50px;
    background: linear-gradient(180deg, var(--amazon-orange), var(--amazon-orange-light));
    border-radius: 4px 4px 0 0;
    min-height: 8px;
    transition: all 0.3s ease;
}

.chart-bar:hover {
    background: linear-gradient(180deg, var(--amazon-orange-hover), var(--amazon-orange));
}

.chart-label {
    font-size: 11px;
    color: var(--gray-600);
    font-weight: 600;
}

.chart-value {
    font-size: 11px;
    color: var(--gray-800);
    font-weight: 700;
}

/* Info Box */
.info-box {
    background: #e7f3fe;
    border: 1px solid #b6d4fe;
    border-radius: 6px;
    padding: 14px 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-top: 16px;
}

.info-box i {
    color: #0d6efd;
    font-size: 16px;
    margin-top: 2px;
}

.info-box p {
    margin: 0;
    color: #084298;
    font-size: 13px;
    line-height: 1.5;
}

/* Payment Info Items */
.payment-info-item {
    padding: 14px 0;
    border-bottom: 1px solid var(--gray-200);
}

.payment-info-item:last-child {
    border-bottom: none;
}

.payment-info-label {
    font-size: 12px;
    color: var(--gray-500);
    margin-bottom: 4px;
}

.payment-info-value {
    font-size: 15px;
    font-weight: 600;
    color: var(--gray-800);
}

@media (max-width: 768px) {
    .earnings-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 480px) {
    .earnings-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="sc-page-header">
    <h1 class="sc-page-title"><strong>Payments</strong> & Earnings</h1>
</div>

<!-- Earnings Summary Cards -->
<div class="earnings-grid">
    <div class="earning-card">
        <div class="earning-card-icon teal">
            <i class="bi bi-calendar"></i>
        </div>
        <div class="earning-card-label">This Month</div>
        <div class="earning-card-value">${{ number_format($earnings['this_month'] ?? 0, 2) }}</div>
        <div class="earning-card-desc">{{ now()->format('F Y') }}</div>
    </div>
    <div class="earning-card">
        <div class="earning-card-icon success">
            <i class="bi bi-clock-history"></i>
        </div>
        <div class="earning-card-label">Last Month</div>
        <div class="earning-card-value">${{ number_format($earnings['last_month'] ?? 0, 2) }}</div>
        <div class="earning-card-desc">{{ now()->subMonth()->format('F Y') }}</div>
    </div>
    <div class="earning-card">
        <div class="earning-card-icon warning">
            <i class="bi bi-clock"></i>
        </div>
        <div class="earning-card-label">Pending Payout</div>
        <div class="earning-card-value">${{ number_format($earnings['pending_payout'] ?? 0, 2) }}</div>
        <div class="earning-card-desc">Awaiting processing</div>
    </div>
    <div class="earning-card">
        <div class="earning-card-icon navy">
            <i class="bi bi-wallet"></i>
        </div>
        <div class="earning-card-label">Total Earned</div>
        <div class="earning-card-value">${{ number_format($earnings['total_earned'] ?? 0, 2) }}</div>
        <div class="earning-card-desc">Lifetime earnings</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Monthly Breakdown -->
        <div class="sc-card">
            <div class="sc-card-header">
                <h3 class="sc-card-title">
                    <i class="bi bi-bar-chart"></i>
                    Monthly Breakdown
                </h3>
            </div>
            <div class="sc-card-body">
                @if($monthlyEarnings->count() > 0)
                <!-- Chart -->
                <div class="chart-container">
                    @php
                        $maxAmount = $monthlyEarnings->max('total') ?: 1;
                    @endphp
                    @foreach($monthlyEarnings->reverse() as $month)
                    @php
                        $height = ($month->total / $maxAmount) * 160;
                    @endphp
                    <div class="chart-bar-wrap">
                        <div class="chart-value">${{ number_format($month->total, 0) }}</div>
                        <div class="chart-bar" style="height: {{ max($height, 8) }}px;"></div>
                        <div class="chart-label">{{ date('M', mktime(0,0,0,$month->month,1)) }}</div>
                    </div>
                    @endforeach
                </div>

                <!-- Table -->
                <table class="earnings-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Orders</th>
                            <th style="text-align: right;">Earnings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyEarnings as $month)
                        <tr>
                            <td><strong>{{ date('F Y', mktime(0,0,0,$month->month,1,$month->year)) }}</strong></td>
                            <td>{{ $month->orders }} order(s)</td>
                            <td style="text-align: right;" class="amount-positive">
                                ${{ number_format($month->total, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="sc-empty">
                    <i class="bi bi-bar-chart"></i>
                    <h4>No earnings data yet</h4>
                    <p>When you complete orders, your earnings will appear here.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Recent Payouts -->
        <div class="sc-card">
            <div class="sc-card-header">
                <h3 class="sc-card-title">
                    <i class="bi bi-receipt"></i>
                    Recent Payouts
                </h3>
            </div>
            <div class="sc-card-body" style="padding: 0;">
                @if($recentPayouts->count() > 0)
                <table class="earnings-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPayouts as $payout)
                        <tr>
                            <td>
                                <strong>{{ $payout->transaction->invoice_no ?? 'N/A' }}</strong>
                                <br><small class="text-muted">
                                    {{ $payout->completed_at ? $payout->completed_at->format('M d, Y') : '-' }}
                                </small>
                            </td>
                            <td style="text-align: right;" class="amount-positive">
                                ${{ number_format($payout->vendor_payout_amount ?? 0, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="sc-empty" style="padding: 30px;">
                    <i class="bi bi-receipt"></i>
                    <p class="text-muted">No payouts yet</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Info -->
        <div class="sc-card">
            <div class="sc-card-header">
                <h3 class="sc-card-title">
                    <i class="bi bi-info-circle"></i>
                    Payment Settings
                </h3>
            </div>
            <div class="sc-card-body">
                <div class="payment-info-item">
                    <div class="payment-info-label">Payment Schedule</div>
                    <div class="payment-info-value">{{ ucfirst($vendor->payment_terms ?? 'Monthly') }}</div>
                </div>
                <div class="payment-info-item">
                    <div class="payment-info-label">Commission Type</div>
                    <div class="payment-info-value">
                        {{ ucfirst($vendor->commission_type ?? 'Percentage') }}
                        @if($vendor->commission_value)
                        - {{ $vendor->commission_value }}{{ $vendor->commission_type === 'percentage' ? '%' : '' }}
                        @endif
                    </div>
                </div>
                
                <div class="info-box">
                    <i class="bi bi-info-circle"></i>
                    <p>Payouts are processed according to your payment schedule. Contact support for any questions.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
