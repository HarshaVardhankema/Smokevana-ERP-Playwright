@extends('layouts.app')

@section('title', __('subscription::lang.subscription_management'))

@section('content')
<style>
    .subscription-dashboard {
        background: #f3f3f3;
        min-height: calc(100vh - 120px);
        padding: 20px 0;
    }
    
    .page-header {
        background: #37475a;
        border-radius: 6px;
        padding: 22px 28px;
        margin-bottom: 24px;
        box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
    }
    
    .page-header h1 {
        color: #ffffff;
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .page-header h1 i {
        color: #ffffff;
        font-size: 22px;
    }
    
    .page-header-subtitle {
        font-size: 13px;
        color: rgba(249, 250, 251, 0.88);
        margin: 0;
    }
    
    /* Stats Row */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: none;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    
    .stat-card.purple::before { background: linear-gradient(90deg, #667eea, #764ba2); }
    .stat-card.green::before { background: linear-gradient(90deg, #11998e, #38ef7d); }
    .stat-card.blue::before { background: linear-gradient(90deg, #4facfe, #00f2fe); }
    .stat-card.orange::before { background: linear-gradient(90deg, #f093fb, #f5576c); }
    
    .stat-card .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
    }
    
    .stat-card.purple .stat-icon { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; }
    .stat-card.green .stat-icon { background: linear-gradient(135deg, #11998e, #38ef7d); color: #fff; }
    .stat-card.blue .stat-icon { background: linear-gradient(135deg, #4facfe, #00f2fe); color: #fff; }
    .stat-card.orange .stat-icon { background: linear-gradient(135deg, #f093fb, #f5576c); color: #fff; }
    
    .stat-card .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #1a1a2e;
        line-height: 1;
        margin-bottom: 8px;
    }
    
    .stat-card .stat-label {
        font-size: 13px;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Secondary Stats */
    .secondary-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .mini-stat {
        background: #fff;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        transition: all 0.2s ease;
    }
    
    .mini-stat:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    
    .mini-stat .mini-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    
    .mini-stat .mini-icon.trial { background: #e3f2fd; color: #1976d2; }
    .mini-stat .mini-icon.pending { background: #fff3e0; color: #f57c00; }
    .mini-stat .mini-icon.pastdue { background: #ffebee; color: #d32f2f; }
    .mini-stat .mini-icon.plans { background: #e8f5e9; color: #388e3c; }
    
    .mini-stat .mini-content h4 {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
        line-height: 1;
    }
    
    .mini-stat .mini-content span {
        font-size: 12px;
        color: #6c757d;
    }
    
    /* Main Content Area */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
    }
    
    .main-content .card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    
    .main-content .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .main-content .card-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .main-content .card-header h3 i {
        color: #00a8e1;
    }
    
    .btn-add-subscription {
        background: linear-gradient(180deg, #FFD814 0%, #FF9900 100%);
        border: 1px solid #FFA500;
        color: #0F1111;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(255, 153, 0, 0.3);
    }
    
    .btn-add-subscription:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(255, 153, 0, 0.4);
        color: #0F1111;
        background: linear-gradient(180deg, #FFE033 0%, #FFB020 100%);
    }
    
    /* Filters */
    .filters-row {
        display: flex;
        gap: 12px;
        padding: 16px 24px;
        background: #f8f9fa;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .filters-row .form-control,
    .filters-row .form-select {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        padding: 10px 14px;
        font-size: 13px;
        transition: all 0.2s ease;
    }
    
    .filters-row .form-control:focus,
    .filters-row .form-select:focus {
        border-color: #00a8e1;
        box-shadow: 0 0 0 3px rgba(0, 168, 225, 0.15);
    }

    /* Amazon-style dropdowns – light background */
    #status_filter,
    #plan_filter {
        background: #ffffff !important;
        color: #0f1111 !important;
        border-radius: 8px;
        border: 2px solid #d5d9d9 !important;
        font-weight: 500;
        padding-inline: 14px;
        box-shadow: 0 1px 2px rgba(15, 17, 17, 0.1);
        cursor: pointer;
    }

    #status_filter:focus,
    #status_filter:hover,
    #plan_filter:focus,
    #plan_filter:hover {
        border-color: #FF9900 !important;
        box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.2);
    }
    
    /* Select2 replacement for status/plan filters */
    .subscription-dashboard .filters-row .select2-container--default .select2-selection--single {
        background: #ffffff !important;
        border: 2px solid #d5d9d9 !important;
        color: #0f1111 !important;
    }
    
    /* Clear button – Amazon style, compact */
    .btn-clear-filters {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
        border: 1px solid #C7511F;
        color: #ffffff;
        padding: 6px 12px;
        border-radius: 4px;
        font-weight: 500;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
    }
    
    .btn-clear-filters i {
        font-size: 12px;
    }
    
    .btn-clear-filters:hover {
        background: linear-gradient(to bottom, #FFAC33 0%, #FF9900 100%);
        border-color: #FF9900;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(255, 153, 0, 0.3);
    }
    
    /* Table Styling */
    .table-container {
        padding: 0;
    }
    
    .subscription-table {
        margin: 0;
    }
    
    .subscription-table thead th {
        background: #f8f9fa;
        border: none;
        padding: 14px 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6c757d;
    }
    
    .subscription-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #f5f5f5;
        font-size: 14px;
        vertical-align: middle;
    }
    
    .subscription-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .subscription-table .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge-active { background: #d4edda; color: #155724; }
    .badge-trial { background: #cce5ff; color: #004085; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }
    .badge-expired { background: #e2e3e5; color: #383d41; }
    .badge-paused { background: #d6d8db; color: #1b1e21; }
    
    .action-btns .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 4px;
    }
    
    /* Sidebar Cards */
    .sidebar-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .sidebar-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 18px 20px;
    }
    
    .sidebar-card .card-header h5 {
        font-size: 15px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    /* Expiring Soon Card */
    .expiring-card {
        border-left: 4px solid #ffc107;
    }
    
    .expiring-card .card-header h5 i {
        color: #ffc107;
    }
    
    .expiring-item {
        padding: 14px 20px;
        border-bottom: 1px solid #f5f5f5;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s ease;
    }
    
    .expiring-item:last-child {
        border-bottom: none;
    }
    
    .expiring-item:hover {
        background: #f8f9fa;
    }
    
    .expiring-item .customer-info h6 {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0 0 4px 0;
    }
    
    .expiring-item .customer-info span {
        font-size: 12px;
        color: #6c757d;
    }
    
    .expiring-item .days-badge {
        background: linear-gradient(135deg, #fff3cd, #ffeeba);
        color: #856404;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .no-data-message {
        padding: 30px 20px;
        text-align: center;
    }
    
    .no-data-message i {
        font-size: 40px;
        color: #28a745;
        margin-bottom: 12px;
    }
    
    .no-data-message p {
        color: #6c757d;
        margin: 0;
        font-size: 13px;
    }
    
    /* Quick Actions */
    .quick-actions-card .card-header h5 i {
        color: #00a8e1;
    }
    
    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        font-size: 14px;
        font-weight: 500;
        color: #333;
        border-bottom: 1px solid #f5f5f5;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    
    .quick-action-btn:last-child {
        border-bottom: none;
    }
    
    .quick-action-btn:hover {
        background: linear-gradient(90deg, #fff8e6, #fff3e0);
        color: #c7511f;
        text-decoration: none;
        padding-left: 24px;
        border-left: 3px solid #FF9900;
    }
    
    .quick-action-btn i {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    
    .quick-action-btn.plans i { background: #fff8e6; color: #e47911; }
    .quick-action-btn.invoices i { background: #fff8e6; color: #e47911; }
    .quick-action-btn.prime i { background: #fff8e6; color: #FF9900; }
    .quick-action-btn.reports i { background: #fff8e6; color: #e47911; }
    
    /* Recent Activity */
    .recent-card .card-header h5 i {
        color: #17a2b8;
    }
    
    .activity-item {
        padding: 14px 20px;
        border-bottom: 1px solid #f5f5f5;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-item .activity-info h6 {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0 0 4px 0;
    }
    
    .activity-item .activity-info span {
        font-size: 12px;
        color: #6c757d;
    }
    
    .activity-item .activity-meta {
        text-align: right;
    }
    
    .activity-item .activity-meta .badge {
        display: block;
        margin-bottom: 4px;
    }
    
    .activity-item .activity-meta small {
        font-size: 11px;
        color: #999;
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .secondary-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .stats-row {
            grid-template-columns: 1fr;
        }
        
        .secondary-stats {
            grid-template-columns: 1fr;
        }
        
        .filters-row {
            flex-wrap: wrap;
        }
    }
</style>

<div class="subscription-dashboard">
    <div class="container-fluid">
        {{-- Page Header – Amazon-style banner --}}
        <div class="page-header">
            <h1>
                <i class="fas fa-crown"></i>
                Subscription Management
            </h1>
            <p class="page-header-subtitle">
                View and manage customer subscriptions, plans, and billing.
            </p>
        </div>

        {{-- Main Stats Row --}}
        <div class="stats-row">
            <div class="stat-card purple">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['active_subscriptions']) }}</div>
                <div class="stat-label">Active Subscriptions</div>
            </div>
            
            <div class="stat-card green">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-value">${{ number_format($stats['monthly_revenue'], 2) }}</div>
                <div class="stat-label">Monthly Revenue</div>
            </div>
            
            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['new_subscriptions']) }}</div>
                <div class="stat-label">New This Month</div>
            </div>
            
            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value">{{ $stats['churn_rate'] }}%</div>
                <div class="stat-label">Churn Rate</div>
            </div>
        </div>

        {{-- Secondary Stats --}}
        <div class="secondary-stats">
            <div class="mini-stat">
                <div class="mini-icon trial">
                    <i class="fas fa-flask"></i>
                </div>
                <div class="mini-content">
                    <h4>{{ $stats['trial_subscriptions'] }}</h4>
                    <span>In Trial</span>
                </div>
            </div>
            
            <div class="mini-stat">
                <div class="mini-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="mini-content">
                    <h4>{{ $stats['pending_renewals'] }}</h4>
                    <span>Pending Renewals</span>
                </div>
            </div>
            
            <div class="mini-stat">
                <div class="mini-icon pastdue">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="mini-content">
                    <h4>{{ $stats['past_due'] }}</h4>
                    <span>Past Due</span>
                </div>
            </div>
            
            <div class="mini-stat">
                <div class="mini-icon plans">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="mini-content">
                    <h4>{{ $stats['total_plans'] }}</h4>
                    <span>Active Plans</span>
                </div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="content-grid">
            {{-- Left: Subscriptions Table --}}
            <div class="main-content">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-list-alt"></i> Subscriptions</h3>
                        @can('subscription.create')
                        <a href="{{ route('subscription.subscriptions.create') }}" class="btn btn-add-subscription">
                            <i class="fas fa-plus"></i> Add Subscription
                        </a>
                        @endcan
                    </div>
                    
                    <div class="filters-row">
                        <select class="form-select" id="status_filter" style="width: 160px;">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="trial">Trial</option>
                            <option value="pending">Pending</option>
                            <option value="past_due">Past Due</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="expired">Expired</option>
                        </select>
                        <select class="form-select" id="plan_filter" style="width: 160px;">
                            <option value="">All Plans</option>
                            @foreach($plans as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control" id="date_range_filter" placeholder="Date Range" style="width: 200px;">
                        <button type="button" class="btn btn-clear-filters" id="clear_filters">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                    
                    <div class="table-container">
                        <table class="table subscription-table" id="subscriptions_table" width="100%">
                            <thead>
                                <tr>
                                    <th>Subscription #</th>
                                    <th>Customer</th>
                                    <th>Plan</th>
                                    <th>Status</th>
                                    <th>Expires</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Right: Sidebar --}}
            <div class="sidebar-content">
                {{-- Expiring Soon --}}
                <div class="sidebar-card expiring-card">
                    <div class="card-header">
                        <h5><i class="fas fa-hourglass-half"></i> Expiring Soon</h5>
                    </div>
                    <div class="card-body p-0">
                        @if($expiring_soon->count() > 0)
                            @foreach($expiring_soon as $sub)
                                <div class="expiring-item">
                                    <div class="customer-info">
                                        <h6>{{ $sub->contact->name ?? 'N/A' }}</h6>
                                        <span>{{ $sub->plan->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="days-badge">
                                        {{ $sub->days_remaining }} days
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="no-data-message">
                                <i class="fas fa-check-circle"></i>
                                <p>No subscriptions expiring soon</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="sidebar-card quick-actions-card">
                    <div class="card-header">
                        <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body p-0">
                        <a href="{{ route('subscription.plans.index') }}" class="quick-action-btn plans">
                            <i class="fas fa-tags"></i>
                            <span>Manage Plans</span>
                        </a>
                        <a href="{{ route('subscription.invoices.index') }}" class="quick-action-btn invoices">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>View Invoices</span>
                        </a>
                        <a href="{{ route('subscription.prime-products.index') }}" class="quick-action-btn prime">
                            <i class="fas fa-crown"></i>
                            <span>Prime Products</span>
                        </a>
                        <a href="{{ route('subscription.reports.index') }}" class="quick-action-btn reports">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports & Analytics</span>
                        </a>
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="sidebar-card recent-card">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Recent Subscriptions</h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($recent_subscriptions->take(5) as $sub)
                            <div class="activity-item">
                                <div class="activity-info">
                                    <h6>{{ $sub->contact->name ?? 'N/A' }}</h6>
                                    <span>{{ $sub->plan->name ?? 'N/A' }}</span>
                                </div>
                                <div class="activity-meta">
                                    <span class="badge badge-{{ $sub->status }}">{{ ucfirst($sub->status) }}</span>
                                    <small>{{ $sub->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var subscriptions_table = $('#subscriptions_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('subscription.subscriptions.data') }}",
            data: function(d) {
                d.status = $('#status_filter').val();
                d.plan_id = $('#plan_filter').val();
                d.date_range = $('#date_range_filter').val();
            }
        },
        columns: [
            { data: 'subscription_no', name: 'subscription_no' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'plan_name', name: 'plan_name' },
            { 
                data: 'status_badge', 
                name: 'status',
                render: function(data, type, row) {
                    var statusClass = 'badge-' + row.status;
                    return '<span class="badge ' + statusClass + '">' + row.status.charAt(0).toUpperCase() + row.status.slice(1) + '</span>';
                }
            },
            { data: 'expires_at_formatted', name: 'expires_at' },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                className: 'action-btns'
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        language: {
            emptyTable: "No subscriptions found",
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
        },
        dom: '<"top"lf>rt<"bottom"ip>',
    });

    // Filters
    $('#status_filter, #plan_filter').on('change', function() {
        subscriptions_table.ajax.reload();
    });

    // Date range picker
    if ($.fn.daterangepicker) {
        $('#date_range_filter').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD'
            }
        });

        $('#date_range_filter').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            subscriptions_table.ajax.reload();
        });

        $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            subscriptions_table.ajax.reload();
        });
    }

    // Clear filters
    $('#clear_filters').on('click', function() {
        $('#status_filter, #plan_filter').val('');
        $('#date_range_filter').val('');
        subscriptions_table.ajax.reload();
    });
});
</script>
@endsection
