@extends('layouts.app')
@section('title', __('home.home'))

@section('css')
<style>
    /* Amazon Dashboard Styles */
    .amazon-dashboard {
        background: #EAEDED;
        min-height: 100vh;
    }
    
    /* Dashboard Header */
    .dashboard-header {
        background: #232F3E;
        padding: 20px 24px;
        border-bottom: 3px solid #FF9900;
    }
    
    .dashboard-header h1 {
        color: #FFFFFF;
        font-size: 24px;
        font-weight: 400;
        margin: 0;
    }
    
    .dashboard-header .welcome-text {
        color: #FF9900;
        font-weight: 600;
    }
    
    .header-controls {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    
    .header-control-btn {
        background: #37475A;
        color: #FFFFFF;
        border: 1px solid #485769;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    
    .header-control-btn:hover {
        background: #485769;
        color: #FF9900;
    }
    
    /* Section Containers */
    .dashboard-section {
        background: #FFFFFF;
        border: 1px solid #D5D9D9;
        border-radius: 8px;
        margin-bottom: 16px;
        overflow: hidden;
    }
    
    .section-header {
        background: #F7F8F8;
        border-bottom: 1px solid #D5D9D9;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #0F1111;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .section-title-icon {
        width: 20px;
        height: 20px;
        color: #FF9900;
    }
    
    .section-body {
        padding: 20px;
    }
    
    /* Performance Metrics Grid */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    
    @media (max-width: 1200px) {
        .metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .metrics-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Amazon-Style Metric Card */
    .metric-card {
        background: #FFFFFF;
        border: 1px solid #D5D9D9;
        border-radius: 8px;
        padding: 16px 20px;
        position: relative;
        transition: all 0.2s;
    }
    
    .metric-card:hover {
        border-color: #FF9900;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .metric-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }
    
    .metric-label {
        font-size: 13px;
        color: #565959;
        font-weight: 400;
        line-height: 1.3;
    }
    
    .metric-icon {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .metric-icon svg {
        width: 18px;
        height: 18px;
    }
    
    .metric-icon.sales { background: #E7F4FF; color: #0066C0; }
    .metric-icon.revenue { background: #E3F5E1; color: #067D62; }
    .metric-icon.warning { background: #FEF3CD; color: #B7791F; }
    .metric-icon.danger { background: #FFEBE5; color: #B12704; }
    .metric-icon.purchase { background: #E8F4FD; color: #007185; }
    .metric-icon.expense { background: #FCE8EC; color: #C7131A; }
    
    .metric-value {
        font-size: 28px;
        font-weight: 700;
        color: #0F1111;
        line-height: 1.2;
        font-family: 'Amazon Ember', Arial, sans-serif;
    }
    
    .metric-trend {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        margin-top: 6px;
    }
    
    .metric-trend.positive { color: #067D62; }
    .metric-trend.negative { color: #B12704; }
    .metric-trend.neutral { color: #565959; }
    
    /* Quick Stats Row */
    .quick-stats-row {
        display: flex;
        gap: 24px;
        padding: 16px 20px;
        background: linear-gradient(135deg, #232F3E 0%, #37475A 100%);
        border-radius: 8px;
        margin-bottom: 16px;
    }
    
    .quick-stat-item {
        flex: 1;
        text-align: center;
        padding: 12px;
        border-right: 1px solid rgba(255,255,255,0.1);
    }
    
    .quick-stat-item:last-child {
        border-right: none;
    }
    
    .quick-stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #FFFFFF;
    }
    
    .quick-stat-label {
        font-size: 12px;
        color: rgba(255,255,255,0.7);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }
    
    /* Chart Container */
    .chart-container {
        background: #FFFFFF;
        border: 1px solid #D5D9D9;
        border-radius: 8px;
        padding: 0;
        height: 100%;
    }
    
    .chart-header {
        padding: 16px 20px;
        border-bottom: 1px solid #E6E6E6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .chart-title {
        font-size: 15px;
        font-weight: 700;
        color: #0F1111;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .chart-body {
        padding: 16px;
    }
    
    /* Action Center */
    .action-center {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    
    @media (max-width: 992px) {
        .action-center {
            grid-template-columns: 1fr;
        }
    }
    
    .action-card {
        background: #FFFFFF;
        border: 1px solid #D5D9D9;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .action-card-header {
        background: linear-gradient(90deg, #232F3E 0%, #37475A 100%);
        color: #FFFFFF;
        padding: 14px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .action-card-title {
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .action-card-badge {
        background: #FF9900;
        color: #0F1111;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 10px;
    }
    
    .action-card-body {
        padding: 0;
        max-height: 350px;
        overflow-y: auto;
    }
    
    /* Amazon-Style Table */
    .amazon-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .amazon-table thead th {
        background: #F7F8F8;
        color: #0F1111;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 12px 16px;
        border-bottom: 1px solid #D5D9D9;
        text-align: left;
    }
    
    .amazon-table tbody td {
        padding: 12px 16px;
        font-size: 13px;
        color: #0F1111;
        border-bottom: 1px solid #E6E6E6;
    }
    
    .amazon-table tbody tr:hover {
        background: #F7FAFA;
    }
    
    .amazon-table .link {
        color: #007185;
        text-decoration: none;
    }
    
    .amazon-table .link:hover {
        color: #C7511F;
        text-decoration: underline;
    }
    
    /* Action Button */
    .action-btn {
        background: #FFD814;
        background: linear-gradient(180deg, #FFD814 0%, #FF9900 100%);
        border: 1px solid #FCD200;
        color: #0F1111;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s;
    }
    
    .action-btn:hover {
        background: linear-gradient(180deg, #F7CA00 0%, #F08804 100%);
    }
    
    /* Orders Section */
    .orders-section {
        background: #FFFFFF;
        border: 1px solid #D5D9D9;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .orders-header {
        background: #232F3E;
        color: #FFFFFF;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .orders-title {
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .orders-title svg {
        color: #FF9900;
    }
    
    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .status-badge.pending { background: #FEF3CD; color: #B7791F; }
    .status-badge.completed { background: #E3F5E1; color: #067D62; }
    .status-badge.shipped { background: #E7F4FF; color: #0066C0; }
    .status-badge.cancelled { background: #FFEBE5; color: #B12704; }
    
    /* Performance Summary Box */
    .performance-summary {
        background: linear-gradient(135deg, #232F3E 0%, #37475A 100%);
        border-radius: 8px;
        padding: 24px;
        color: #FFFFFF;
        margin-bottom: 16px;
    }
    
    .performance-summary-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .performance-summary-title {
        font-size: 18px;
        font-weight: 600;
    }
    
    .performance-summary-date {
        font-size: 13px;
        color: rgba(255,255,255,0.7);
    }
    
    .performance-metrics {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    
    @media (max-width: 992px) {
        .performance-metrics {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .perf-metric {
        text-align: center;
        padding: 16px;
        background: rgba(255,255,255,0.05);
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.1);
    }
    
    .perf-metric-icon {
        width: 40px;
        height: 40px;
        background: #FF9900;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }
    
    .perf-metric-icon svg {
        width: 20px;
        height: 20px;
        color: #232F3E;
    }
    
    .perf-metric-value {
        font-size: 26px;
        font-weight: 700;
        color: #FFFFFF;
        line-height: 1.2;
    }
    
    .perf-metric-label {
        font-size: 12px;
        color: rgba(255,255,255,0.7);
        margin-top: 4px;
    }
    
    /* Two Column Layout */
    .two-column-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    
    @media (max-width: 1200px) {
        .two-column-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Compact Table Styling */
    .compact-table-wrapper {
        max-height: 400px;
        overflow-y: auto;
    }
    
    /* Alert Badge */
    .alert-indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        background: #FF9900;
        color: #0F1111;
        font-size: 11px;
        font-weight: 700;
        border-radius: 50%;
        margin-left: 6px;
    }
    
    /* Info Tooltip */
    .info-tip {
        color: #007185;
        cursor: help;
    }
    
    /* Dropdown Override */
    .amazon-select {
        background: #FFFFFF;
        border: 1px solid #D5D9D9;
        border-radius: 4px;
        padding: 8px 12px;
        font-size: 13px;
        color: #0F1111;
        min-width: 180px;
    }
    
    .amazon-select:focus {
        border-color: #FF9900;
        outline: none;
        box-shadow: 0 0 0 3px rgba(255,153,0,0.2);
    }
    
    /* Full Width Section */
    .full-width-section {
        margin-top: 16px;
    }
    
    /* Section Controls */
    .section-controls {
        display: flex;
        gap: 8px;
        align-items: center;
    }
</style>
@endsection

@section('content')
<div class="amazon-dashboard">
    {{-- Dashboard Header --}}
    <div class="dashboard-header">
        <div class="tw-flex tw-justify-between tw-items-center tw-flex-wrap tw-gap-4">
            <div>
                <h1>
                    <span class="welcome-text">{{ __('home.welcome_message', ['name' => Session::get('user.first_name')]) }}</span>
                </h1>
                <p class="tw-text-gray-400 tw-text-sm tw-mt-1">{{ __('Here\'s your business overview for today') }}</p>
                        </div>
    
            @if (auth()->user()->can('dashboard.data') && $is_admin)
            <div class="header-controls">
                                    @if (count($all_locations) > 1)
                <div>
                                        {!! Form::select('dashboard_location', $all_locations, $default_location->id ?? null, [
                        'class' => 'amazon-select',
                                            'placeholder' => __('lang_v1.select_location'),
                                            'id' => 'dashboard_location',
                                        ]) !!}
                                </div>
                @endif
                
                <button type="button" id="dashboard_date_filter" class="header-control-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                                {{ __('messages.filter_by_date') }}
                                        </button>
                                </div>
                        @endif
                    </div>
    </div>

    <div class="tw-p-4 lg:tw-p-6">
        @if (auth()->user()->can('dashboard.data') && $is_admin)
        
        {{-- Performance Summary Section --}}
        <div class="performance-summary">
            <div class="performance-summary-header">
                <div class="performance-summary-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FF9900" stroke-width="2" style="display: inline; margin-right: 8px;">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                                </svg>
                    Business Performance Overview
                                            </div>
                <div class="performance-summary-date">
                    {{ @format_date('now') }}
                                            </div>
                                        </div>
            
            <div class="performance-metrics">
                <div class="perf-metric">
                    <div class="perf-metric-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                            <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                            <path d="M17 17h-11v-14h-2"></path>
                            <path d="M6 5l14 1l-1 7h-13"></path>
                        </svg>
                                    </div>
                    <div class="perf-metric-value total_sell">--</div>
                    <div class="perf-metric-label">{{ __('home.total_sell') }}</div>
                                </div>

                <div class="perf-metric">
                    <div class="perf-metric-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                                </svg>
                    </div>
                    <div class="perf-metric-value net">--</div>
                    <div class="perf-metric-label">{{ __('lang_v1.net') }} @show_tooltip(__('lang_v1.net_home_tooltip'))</div>
                                            </div>

                <div class="perf-metric">
                    <div class="perf-metric-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 3v12M16 11l-4 4l-4-4"></path>
                            <path d="M3 12a9 9 0 0 0 18 0"></path>
                        </svg>
                                            </div>
                    <div class="perf-metric-value total_purchase">--</div>
                    <div class="perf-metric-label">{{ __('home.total_purchase') }}</div>
                                </div>

                <div class="perf-metric">
                    <div class="perf-metric-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                                                </svg>
                                            </div>
                    <div class="perf-metric-value total_expense">--</div>
                    <div class="perf-metric-label">{{ __('lang_v1.expense') }}</div>
                                        </div>
                                    </div>
                                </div>

        {{-- Key Metrics Cards --}}
        <div class="metrics-grid tw-mb-4">
            {{-- Invoice Due --}}
            <div class="metric-card">
                <div class="metric-card-header">
                    <span class="metric-label">{{ __('home.invoice_due') }}</span>
                    <div class="metric-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="12" y1="18" x2="12" y2="12"></line>
                            <line x1="9" y1="15" x2="15" y2="15"></line>
                                                </svg>
                                            </div>
                                            </div>
                <div class="metric-value invoice_due">--</div>
                <div class="metric-trend neutral">
                    <span>Requires attention</span>
                                        </div>
                                    </div>

            {{-- Purchase Due --}}
            <div class="metric-card">
                <div class="metric-card-header">
                    <span class="metric-label">{{ __('home.purchase_due') }}</span>
                    <div class="metric-icon warning">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 9v4"></path>
                            <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                            <path d="M12 16h.01"></path>
                                            </svg>
                                        </div>
                                        </div>
                <div class="metric-value purchase_due">--</div>
                <div class="metric-trend neutral">
                    <span>Pending payments</span>
                                    </div>
                                </div>

            {{-- Total Sell Return --}}
            <div class="metric-card">
                <div class="metric-card-header">
                    <span class="metric-label">{{ __('lang_v1.total_sell_return') }} 
                        <i class="fa fa-info-circle text-info hover-q no-print info-tip" aria-hidden="true" data-container="body"
                        data-toggle="popover" data-placement="auto bottom" id="total_srp"
                        data-value="{{ __('lang_v1.total_sell_return') }}-{{ __('lang_v1.total_sell_return_paid') }}"
                        data-content="" data-html="true" data-trigger="hover"></i>
                    </span>
                    <div class="metric-icon danger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 7l-18 0"></path>
                            <path d="M18 10l3 -3l-3 -3"></path>
                            <path d="M6 20l-3 -3l3 -3"></path>
                            <path d="M3 17l18 0"></path>
                                            </svg>
                                        </div>
                                        </div>
                <div class="metric-value total_sell_return">--</div>
                <div class="metric-trend negative">
                    <span>Returns processed</span>
                                </div>
                            </div>

            {{-- Total Purchase Return --}}
            <div class="metric-card">
                <div class="metric-card-header">
                    <span class="metric-label">{{ __('lang_v1.total_purchase_return') }}
                        <i class="fa fa-info-circle text-info hover-q no-print info-tip" aria-hidden="true" data-container="body"
                                                data-toggle="popover" data-placement="auto bottom" id="total_prp"
                                                data-value="{{ __('lang_v1.total_purchase_return') }}-{{ __('lang_v1.total_purchase_return_paid') }}"
                                                data-content="" data-html="true" data-trigger="hover"></i>
                    </span>
                    <div class="metric-icon danger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2"></path>
                            <path d="M15 14v-2a2 2 0 0 0 -2 -2h-4l2 -2m0 4l-2 -2"></path>
                                            </svg>
                                        </div>
                                        </div>
                <div class="metric-value total_purchase_return">--</div>
                <div class="metric-trend negative">
                    <span>Returned to vendors</span>
                                    </div>
                                </div>
                            </div>

        {{-- Sales Charts Section --}}
                @if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view'))
                    @if (!empty($all_locations))
            <div class="two-column-grid tw-mb-4">
                {{-- Sales Last 30 Days Chart --}}
                <div class="chart-container">
                    <div class="chart-header">
                        <div class="chart-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FF9900" stroke-width="2">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                                        </svg>
                                        {{ __('home.sells_last_30_days') }}
                                </div>
                    </div>
                    <div class="chart-body">
                                            {!! $sells_chart_1->container() !!}
                                    </div>
                                </div>

                {{-- Sales Current FY Chart --}}
                <div class="chart-container">
                    <div class="chart-header">
                        <div class="chart-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FF9900" stroke-width="2">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                        </svg>
                                        {{ __('home.sells_current_fy') }}
                                </div>
                    </div>
                    <div class="chart-body">
                                            {!! $sells_chart_2->container() !!}
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

        {{-- Action Center - Payment Dues --}}
        <div class="action-center tw-mb-4">
            {{-- Sales Payment Due --}}
                @if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view'))
            <div class="action-card">
                <div class="action-card-header">
                    <div class="action-card-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 9v4"></path>
                            <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                            {{ __('lang_v1.sales_payment_dues') }}
                                            @show_tooltip(__('lang_v1.tooltip_sales_payment_dues'))
                                    </div>
                    <div>
                                        {!! Form::select('sales_payment_dues_location', $all_locations, $default_location->id ?? null, [
                            'class' => 'amazon-select',
                                            'placeholder' => __('lang_v1.select_location'),
                                            'id' => 'sales_payment_dues_location',
                            'style' => 'min-width: 150px; font-size: 12px;'
                                        ]) !!}
                                    </div>
                                </div>
                <div class="action-card-body">
                    <table class="table table-bordered table-striped amazon-table" id="sales_payment_dues_table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('contact.customer')</th>
                                                    <th>@lang('sale.invoice_no')</th>
                                                    <th>@lang('home.due_amount')</th>
                                                    <th>@lang('messages.action')</th>
                                                </tr>
                                            </thead>
                                        </table>
                        </div>
                    </div>
                @endif

            {{-- Purchase Payment Due --}}
                @can('purchase.view')
            <div class="action-card">
                <div class="action-card-header">
                    <div class="action-card-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 9v4"></path>
                            <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                            {{ __('lang_v1.purchase_payment_dues') }}
                                            @show_tooltip(__('tooltip.payment_dues'))
                                    </div>
                    <div>
                                        @if (count($all_locations) > 1)
                                            {!! Form::select('purchase_payment_dues_location', $all_locations, $default_location->id ?? null, [
                            'class' => 'amazon-select',
                                                'placeholder' => __('lang_v1.select_location'),
                                                'id' => 'purchase_payment_dues_location',
                            'style' => 'min-width: 150px; font-size: 12px;'
                                            ]) !!}
                                        @endif
                                    </div>
                                </div>
                <div class="action-card-body">
                    <table class="table table-bordered table-striped amazon-table" id="purchase_payment_dues_table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('purchase.supplier')</th>
                                                    <th>@lang('purchase.ref_no')</th>
                                                    <th>@lang('home.due_amount')</th>
                                                    <th>@lang('messages.action')</th>
                                                </tr>
                                            </thead>
                                        </table>
                        </div>
                    </div>
                @endcan
        </div>

        {{-- Stock Alerts Section --}}
                @can('stock_report.view')
        <div class="dashboard-section tw-mb-4">
            <div class="section-header">
                <h3 class="section-title">
                    <svg class="section-title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                        <path d="M12 8v4"></path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                            {{ __('home.product_stock_alert') }}
                                            @show_tooltip(__('tooltip.product_stock_alert'))
                                        </h3>
                <div class="section-controls">
                                        @if (count($all_locations) > 1)
                                            {!! Form::select('stock_alert_location', $all_locations, $default_location->id ?? null, [
                        'class' => 'amazon-select',
                                                'placeholder' => __('lang_v1.select_location'),
                                                'id' => 'stock_alert_location',
                                            ]) !!}
                                        @endif
                                    </div>
                                </div>
            <div class="section-body">
                <table class="table table-bordered table-striped amazon-table" id="stock_alert_table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('sale.product')</th>
                                                    <th>@lang('business.location')</th>
                                                    <th>@lang('report.current_stock')</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                    @if (session('business.enable_product_expiry') == 1)
        <div class="dashboard-section tw-mb-4">
            <div class="section-header">
                <h3 class="section-title">
                    <svg class="section-title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                                {{ __('home.stock_expiry_alert') }}
                    @show_tooltip(__('tooltip.stock_expiry_alert', ['days' => session('business.stock_expiry_alert_days', 30)]))
                                            </h3>
                                        </div>
            <div class="section-body">
                <input type="hidden" id="stock_expiry_alert_days" value="{{ \Carbon::now()->addDays(session('business.stock_expiry_alert_days', 30))->format('Y-m-d') }}">
                <table class="table table-bordered table-striped amazon-table" id="stock_expiry_alert_table">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('business.product')</th>
                                                        <th>@lang('business.location')</th>
                                                        <th>@lang('report.stock_left')</th>
                                                        <th>@lang('product.expires_in')</th>
                                                    </tr>
                                                </thead>
                                            </table>
                            </div>
                        </div>
                    @endif
                @endcan

        {{-- Sales Orders Section --}}
                @if (auth()->user()->can('so.view_all') || auth()->user()->can('so.view_own'))
        <div class="orders-section tw-mb-4">
            <div class="orders-header">
                <div class="orders-title">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                                    </svg>
                                            {{ __('lang_v1.sales_order') }}
                                    </div>
                <div>
                                        @if (count($all_locations) > 1)
                                            {!! Form::select('so_location',  $all_locations, $default_location->id ?? null, [
                        'class' => 'amazon-select',
                                                'placeholder' => __('lang_v1.select_location'),
                                                'id' => 'so_location',
                                            ]) !!}
                                        @endif
                                    </div>
                                </div>
            <div class="tw-p-0">
                <table class="table table-bordered table-striped ajax_view amazon-table" id="sales_order_table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.date')</th>
                                                    <th>@lang('restaurant.order_no')</th>
                                                    <th>@lang('sale.customer_name')</th>
                                                    <th>@lang('lang_v1.contact_no')</th>
                                                    <th>@lang('sale.location')</th>
                                                    <th>@lang('lang_v1.shipping_status')</th>
                                                    <th>@lang('lang_v1.quantity_remaining')</th>
                                                    <th>@lang('lang_v1.added_by')</th>
                                                    <th>@lang('messages.action')</th>
                                                </tr>
                                            </thead>
                                        </table>
                        </div>
                    </div>
                @endif

        {{-- Purchase Requisition Section --}}
        @if (!empty($common_settings['enable_purchase_requisition']) && (auth()->user()->can('purchase_requisition.view_all') || auth()->user()->can('purchase_requisition.view_own')))
        <div class="dashboard-section tw-mb-4">
            <div class="section-header">
                <h3 class="section-title">
                    <svg class="section-title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                        <rect x="9" y="3" width="6" height="4" rx="2"></rect>
                        <path d="M9 12h6"></path>
                        <path d="M9 16h6"></path>
                                    </svg>
                                            @lang('lang_v1.purchase_requisition')
                                        </h3>
                <div class="section-controls">
                                            @if (count($all_locations) > 1)
                                                {!! Form::select('pr_location',  $all_locations, $default_location->id ?? null, [
                        'class' => 'amazon-select',
                                                    'placeholder' => __('lang_v1.select_location'),
                                                    'id' => 'pr_location',
                                                ]) !!}
                                        @endif
                                    </div>
                                </div>
            <div class="section-body tw-p-0">
                <table class="table table-bordered table-striped ajax_view amazon-table" id="purchase_requisition_table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.action')</th>
                                                    <th>@lang('messages.date')</th>
                                                    <th>@lang('purchase.ref_no')</th>
                                                    <th>@lang('purchase.location')</th>
                                                    <th>@lang('sale.status')</th>
                                                    <th>@lang('lang_v1.required_by_date')</th>
                                                    <th>@lang('lang_v1.added_by')</th>
                                                </tr>
                                            </thead>
                                        </table>
                        </div>
                    </div>
                @endif

        {{-- Purchase Order Section --}}
        @if (!empty($common_settings['enable_purchase_order']) && (auth()->user()->can('purchase_order.view_all') || auth()->user()->can('purchase_order.view_own')))
        <div class="dashboard-section tw-mb-4">
            <div class="section-header">
                <h3 class="section-title">
                    <svg class="section-title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                        <line x1="4" y1="10" x2="20" y2="10"></line>
                        <line x1="12" y1="4" x2="12" y2="20"></line>
                                    </svg>
                                            @lang('lang_v1.purchase_order')
                                        </h3>
                <div class="section-controls">
                                        @if (count($all_locations) > 1)
                                            {!! Form::select('po_location',  $all_locations, $default_location->id ?? null, [
                        'class' => 'amazon-select',
                                                'placeholder' => __('lang_v1.select_location'),
                                                'id' => 'po_location',
                                            ]) !!}
                                        @endif
                                    </div>
                                </div>
            <div class="section-body tw-p-0">
                <table class="table table-bordered table-striped ajax_view amazon-table" id="purchase_order_table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.action')</th>
                                                    <th>@lang('messages.date')</th>
                                                    <th>@lang('purchase.ref_no')</th>
                                                    <th>@lang('purchase.location')</th>
                                                    <th>@lang('purchase.supplier')</th>
                                                    <th>@lang('sale.status')</th>
                                                    <th>@lang('lang_v1.quantity_remaining')</th>
                                                    <th>@lang('lang_v1.added_by')</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                @endif

        {{-- Pending Shipments Section --}}
        @if (auth()->user()->can('access_pending_shipments_only') || auth()->user()->can('access_shipping') || auth()->user()->can('access_own_shipping'))
        <div class="dashboard-section tw-mb-4">
            <div class="section-header">
                <h3 class="section-title">
                    <svg class="section-title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                        <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                        <path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"></path>
                                        <path d="M3 9l4 0"></path>
                                    </svg>
                                            @lang('lang_v1.pending_shipments')
                                        </h3>
                <div class="section-controls">
                                        @if (count($all_locations) > 1)
                                            {!! Form::select('pending_shipments_location',  $all_locations, $default_location->id ?? null, [
                        'class' => 'amazon-select',
                                                'placeholder' => __('lang_v1.select_location'),
                                                'id' => 'pending_shipments_location',
                                            ]) !!}
                                        @endif
                                    </div>
                                </div>
            <div class="section-body tw-p-0">
                <table class="table table-bordered table-striped ajax_view amazon-table" id="shipments_table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.date')</th>
                                                    <th>@lang('sale.invoice_no')</th>
                                                    <th>@lang('sale.customer_name')</th>
                                                    <th>@lang('lang_v1.contact_no')</th>
                                                    <th>@lang('sale.location')</th>
                                                    <th>@lang('lang_v1.shipping_status')</th>
                                                    @if (!empty($custom_labels['shipping']['custom_field_1']))
                                <th>{{ $custom_labels['shipping']['custom_field_1'] }}</th>
                                                    @endif
                                                    @if (!empty($custom_labels['shipping']['custom_field_2']))
                                <th>{{ $custom_labels['shipping']['custom_field_2'] }}</th>
                                                    @endif
                                                    @if (!empty($custom_labels['shipping']['custom_field_3']))
                                <th>{{ $custom_labels['shipping']['custom_field_3'] }}</th>
                                                    @endif
                                                    @if (!empty($custom_labels['shipping']['custom_field_4']))
                                <th>{{ $custom_labels['shipping']['custom_field_4'] }}</th>
                                                    @endif
                                                    @if (!empty($custom_labels['shipping']['custom_field_5']))
                                <th>{{ $custom_labels['shipping']['custom_field_5'] }}</th>
                                                    @endif
                                                    <th>@lang('sale.payment_status')</th>
                                                    <th>@lang('restaurant.service_staff')</th>
                                                    <th>@lang('messages.action')</th>
                                                </tr>
                                            </thead>
                                        </table>
                        </div>
                    </div>
                @endif

        {{-- Payment Recovered Today Section --}}
                @if (auth()->user()->can('account.access') && config('constants.show_payments_recovered_today') == true)
        <div class="dashboard-section tw-mb-4">
            <div class="section-header">
                <h3 class="section-title">
                    <svg class="section-title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                    </svg>
                                            @lang('lang_v1.payment_recovered_today')
                                        </h3>
                                    </div>
            <div class="section-body tw-p-0">
                <table class="table table-bordered table-striped amazon-table" id="cash_flow_table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.date')</th>
                                                    <th>@lang('account.account')</th>
                                                    <th>@lang('lang_v1.description')</th>
                                                    <th>@lang('lang_v1.payment_method')</th>
                                                    <th>@lang('lang_v1.payment_details')</th>
                                                    <th>@lang('account.credit')</th>
                            <th>@lang('lang_v1.account_balance') @show_tooltip(__('lang_v1.account_balance_tooltip'))</th>
                            <th>@lang('lang_v1.total_balance') @show_tooltip(__('lang_v1.total_balance_tooltip'))</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr class="bg-gray font-17 footer-total text-center">
                                                    <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                                                    <td class="footer_total_credit"></td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                        </div>
                    </div>
                @endif

        @endif {{-- End of dashboard.data permission check --}}
            </div>
        </div>

{{-- Modals --}}
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade edit_pso_status_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

@endsection

@section('javascript')
    <script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
    @includeIf('sales_order.common_js')
    @includeIf('purchase_order.common_js')
    @if (!empty($all_locations))
        {!! $sells_chart_1->script() !!}
        {!! $sells_chart_2->script() !!}
    @endif
    <script type="text/javascript">
        $(document).ready(function() {
        // Sales Order Table
            sales_order_table = $('#sales_order_table').DataTable({
                processing: true,
            language: { processing: `<div id="main_loader"><span class="loader"></span></div>`},
                serverSide: true,
            fixedHeader: false,
            scrollY: "400px",
                scrollX: true,
                scrollCollapse: false,
            aaSorting: [[0, 'desc']],
                "ajax": {
                    "url": '{{ action([\App\Http\Controllers\SellController::class, 'index']) }}?sale_type=sales_order',
                    "data": function(d) {
                        d.for_dashboard_sales_order = true;
                        if ($('#so_location').length > 0) {
                            d.location_id = $('#so_location').val();
                        }
                    }
                },
                columnDefs: [{
                "targets": 8,
                    "orderable": false,
                    "searchable": false
                }],
                columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'conatct_name', name: 'conatct_name' },
                { data: 'mobile', name: 'contacts.mobile' },
                { data: 'business_location', name: 'bl.name' },
                { data: 'shipping_status', name: 'shipping_status' },
                { data: 'so_qty_remaining', name: 'so_qty_remaining' },
                { data: 'added_by', name: 'u.first_name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            @if (auth()->user()->can('account.access') && config('constants.show_payments_recovered_today') == true)
                // Cash Flow Table
                cash_flow_table = $('#cash_flow_table').DataTable({
                    processing: true,
            language: { processing: `<div id="main_loader"><span class="loader"></span></div>`},
                    serverSide: true,
            fixedHeader: false,
                    "ajax": {
                        "url": "{{ action([\App\Http\Controllers\AccountController::class, 'cashFlow']) }}",
                        "data": function(d) {
                            d.type = 'credit';
                            d.only_payment_recovered = true;
                        }
                    },
                    "ordering": false,
                    "searching": false,
            columns: [
                { data: 'operation_date', name: 'operation_date' },
                { data: 'account_name', name: 'account_name' },
                { data: 'sub_type', name: 'sub_type' },
                { data: 'method', name: 'TP.method' },
                { data: 'payment_details', name: 'payment_details', searchable: false },
                { data: 'credit', name: 'amount' },
                { data: 'balance', name: 'balance' },
                { data: 'total_balance', name: 'total_balance' }
                    ],
                    "fnDrawCallback": function(oSettings) {
                        __currency_convert_recursively($('#cash_flow_table'));
                    },
                    "footerCallback": function(row, data, start, end, display) {
                        var footer_total_credit = 0;
                        for (var r in data) {
                    footer_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;
                        }
                        $('.footer_total_credit').html(__currency_trans_from_en(footer_total_credit));
                    }
                });
            @endif

            $('#so_location').change(function() {
                sales_order_table.ajax.reload();
            });

            @if (!empty($common_settings['enable_purchase_order']))
        // Purchase Order Table
                purchase_order_table = $('#purchase_order_table').DataTable({
                    processing: true,
            language: { processing: `<div id="main_loader"><span class="loader"></span></div>`},
                    serverSide: true,
            fixedHeader: false,
            aaSorting: [[1, 'desc']],
            scrollY: "400px",
                    scrollX: true,
                    scrollCollapse: false,
                    ajax: {
                        url: '{{ action([\App\Http\Controllers\PurchaseOrderController::class, 'index']) }}',
                        data: function(d) {
                            d.from_dashboard = true;
                            if ($('#po_location').length > 0) {
                                d.location_id = $('#po_location').val();
                            }
                }
                    },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'ref_no', name: 'ref_no' },
                { data: 'location_name', name: 'BS.name' },
                { data: 'name', name: 'contacts.name' },
                { data: 'status', name: 'transactions.status' },
                { data: 'po_qty_remaining', name: 'po_qty_remaining', searchable: false },
                { data: 'added_by', name: 'u.first_name' }
                    ]
        });

                $('#po_location').change(function() {
                    purchase_order_table.ajax.reload();
                });
            @endif

            @if (!empty($common_settings['enable_purchase_requisition']))
        // Purchase Requisition Table
                purchase_requisition_table = $('#purchase_requisition_table').DataTable({
                    processing: true,
            language: { processing: `<div id="main_loader"><span class="loader"></span></div>`},
                    serverSide: true,
            fixedHeader: false,
            aaSorting: [[1, 'desc']],
            scrollY: "400px",
                    scrollX: true,
                    scrollCollapse: false,
                    ajax: {
                        url: '{{ action([\App\Http\Controllers\PurchaseRequisitionController::class, 'index']) }}',
                        data: function(d) {
                            d.from_dashboard = true;
                            if ($('#pr_location').length > 0) {
                                d.location_id = $('#pr_location').val();
                            }
                }
                    },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'ref_no', name: 'ref_no' },
                { data: 'location_name', name: 'BS.name' },
                { data: 'status', name: 'status' },
                { data: 'delivery_date', name: 'delivery_date' },
                { data: 'added_by', name: 'u.first_name' }
                    ]
        });

                $('#pr_location').change(function() {
                    purchase_requisition_table.ajax.reload();
                });

                $(document).on('click', 'a.delete-purchase-requisition', function(e) {
                    e.preventDefault();
                    swal({
                        title: LANG.sure,
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                    }).then(willDelete => {
                        if (willDelete) {
                            var href = $(this).attr('href');
                            $.ajax({
                                method: 'DELETE',
                                url: href,
                                dataType: 'json',
                                success: function(result) {
                                    if (result.success == true) {
                                        toastr.success(result.msg);
                                        purchase_requisition_table.ajax.reload();
                                    } else {
                                        toastr.error(result.msg);
                                    }
                        }
                            });
                        }
                    });
                });
            @endif

        // Shipments Table
            sell_table = $('#shipments_table').DataTable({
                processing: true,
                    language: { processing: `<div id="main_loader"><span class="loader"></span></div>`},
                serverSide: true,
            fixedHeader: false,
            aaSorting: [[0, 'desc']],
            scrollY: "400px",
                scrollX: true,
                scrollCollapse: false,
                "ajax": {
                    "url": '{{ action([\App\Http\Controllers\SellController::class, 'index']) }}',
                    "data": function(d) {
                        d.only_pending_shipments = true;
                        if ($('#pending_shipments_location').length > 0) {
                            d.location_id = $('#pending_shipments_location').val();
                        }
                    }
                },
                columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'conatct_name', name: 'conatct_name' },
                { data: 'mobile', name: 'contacts.mobile' },
                { data: 'business_location', name: 'bl.name' },
                { data: 'shipping_status', name: 'shipping_status' },
                    @if (!empty($custom_labels['shipping']['custom_field_1']))
                    { data: 'shipping_custom_field_1', name: 'shipping_custom_field_1' },
                    @endif
                    @if (!empty($custom_labels['shipping']['custom_field_2']))
                    { data: 'shipping_custom_field_2', name: 'shipping_custom_field_2' },
                    @endif
                    @if (!empty($custom_labels['shipping']['custom_field_3']))
                    { data: 'shipping_custom_field_3', name: 'shipping_custom_field_3' },
                    @endif
                    @if (!empty($custom_labels['shipping']['custom_field_4']))
                    { data: 'shipping_custom_field_4', name: 'shipping_custom_field_4' },
                    @endif
                    @if (!empty($custom_labels['shipping']['custom_field_5']))
                    { data: 'shipping_custom_field_5', name: 'shipping_custom_field_5' },
                        @endif
                { data: 'payment_status', name: 'payment_status' },
                { data: 'waiter', name: 'ss.first_name', @if (empty($is_service_staff_enabled)) visible: false @endif },
                { data: 'action', name: 'action', searchable: false, orderable: false }
                ],
                "fnDrawCallback": function(oSettings) {
                    __currency_convert_recursively($('#sell_table'));
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).find('td:eq(4)').attr('class', 'clickable_td');
                }
            });

            $('#pending_shipments_location').change(function() {
                sell_table.ajax.reload();
            });
        });
    </script>
@endsection
