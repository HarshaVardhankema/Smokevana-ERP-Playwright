@extends('layouts.app')
@section('title', 'Dropship Dashboard')

@section('css')
<style>
    /* Amazon-themed Dropship Dashboard Styles */
    .dropship-dashboard {
        padding: 20px;
        background: #EAEDED;
        min-height: calc(100vh - 60px);
    }
    
    /* Dashboard Header - Amazon style */
    .dashboard-header {
        background: #37475a;
        border-radius: 6px;
        padding: 22px 28px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
    }
    
    .dashboard-header-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .dashboard-header h1 {
        font-size: 22px;
        font-weight: 700;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
        line-height: 1;
    }
    
    .dashboard-header h1 svg {
        color: #ffffff !important;
        display: block;
    }
    
    .dashboard-header-subtitle {
        font-size: 13px;
        color: rgba(249, 250, 251, 0.88);
        margin: 4px 0 0 0;
    }
    
    .header-actions {
        display: flex;
        gap: 12px;
    }
    
    .header-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
    }
    
    .header-btn-primary {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border: 1px solid #C7511F !important;
        color: #ffffff !important;
    }
    
    .header-btn-primary:hover {
        color: #ffffff !important;
        opacity: 0.95;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 153, 0, 0.3);
    }
    
    .header-btn-secondary {
        background: rgba(255, 255, 255, 0.2) !important;
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        color: #FFFFFF !important;
    }
    
    .header-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.3) !important;
        color: #FFFFFF !important;
        text-decoration: none;
        transform: translateY(-1px);
    }
    
    /* KPI Cards Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    
    @media (max-width: 1200px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .kpi-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* KPI Card */
    .kpi-card {
        background: #FFFFFF;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #E8E8E8;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }
    
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    
    .kpi-card.orders::before { background: linear-gradient(90deg, #232F3E, #37475A); }
    .kpi-card.pending::before { background: linear-gradient(90deg, #FF9900, #FFBA57); }
    .kpi-card.completed::before { background: linear-gradient(90deg, #067D62, #00A88A); }
    .kpi-card.vendors::before { background: linear-gradient(90deg, #7B68EE, #9683EC); }
    
    .kpi-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .kpi-card.orders .kpi-icon { background: rgba(35, 47, 62, 0.1); color: #232F3E; }
    .kpi-card.pending .kpi-icon { background: rgba(255, 153, 0, 0.1); color: #FF9900; }
    .kpi-card.completed .kpi-icon { background: rgba(6, 125, 98, 0.1); color: #067D62; }
    .kpi-card.vendors .kpi-icon { background: rgba(123, 104, 238, 0.1); color: #7B68EE; }
    
    .kpi-trend {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 20px;
    }
    
    .kpi-trend.up {
        background: rgba(6, 125, 98, 0.1);
        color: #067D62;
    }
    
    .kpi-trend.neutral {
        background: rgba(107, 114, 128, 0.1);
        color: #6B7280;
    }
    
    .kpi-label {
        font-size: 13px;
        color: #6B7280;
        font-weight: 500;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .kpi-value {
        font-size: 32px;
        font-weight: 700;
        color: #232F3E;
        line-height: 1.2;
    }
    
    .kpi-subtitle {
        font-size: 12px;
        color: #9CA3AF;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .kpi-subtitle span {
        font-weight: 600;
        color: #6B7280;
    }
    
    /* Alert Banner */
    .alert-banner {
        background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
        border: 1px solid #F59E0B;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .alert-banner-icon {
        width: 40px;
        height: 40px;
        background: #F59E0B;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        flex-shrink: 0;
    }
    
    .alert-banner-content {
        flex: 1;
    }
    
    .alert-banner-title {
        font-weight: 600;
        color: #92400E;
        margin-bottom: 2px;
    }
    
    .alert-banner-text {
        font-size: 14px;
        color: #B45309;
    }
    
    .alert-banner-link {
        color: #92400E;
        font-weight: 600;
        text-decoration: underline;
    }
    
    .alert-banner-link:hover {
        color: #78350F;
    }
    
    /* Main Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    
    @media (max-width: 1200px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Dashboard Card */
    .dash-card {
        background: #FFFFFF;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #E8E8E8;
        overflow: hidden;
    }
    
    .dash-card-header {
        background: #232F3E;
        color: #FFFFFF;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .dash-card-title {
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        color: #FFFFFF;
    }
    
    .dash-card-title svg {
        color: #FF9900;
    }
    
    .dash-card-body {
        padding: 20px;
    }
    
    /* Chart Container */
    .chart-container {
        position: relative;
        height: 280px;
        padding: 10px;
    }
    
    /* Quick Actions */
    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        border: 2px solid transparent;
        background: none;
        width: 100%;
        text-align: left;
    }
    
    .action-btn:hover {
        transform: translateX(4px);
        text-decoration: none;
    }
    
    .action-btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    
    .action-btn.add-vendor {
        background: #FFFBEB;
        border-color: #FCD34D;
        color: #92400E;
    }
    
    .action-btn.add-vendor .action-btn-icon {
        background: #FF9900;
        color: #FFFFFF;
    }
    
    .action-btn.add-vendor:hover {
        background: #FEF3C7;
        color: #92400E;
    }
    
    .action-btn.view-orders {
        background: #EFF6FF;
        border-color: #93C5FD;
        color: #1E40AF;
    }
    
    .action-btn.view-orders .action-btn-icon {
        background: #3B82F6;
        color: #FFFFFF;
    }
    
    .action-btn.view-orders:hover {
        background: #DBEAFE;
        color: #1E40AF;
    }
    
    .action-btn.sync {
        background: #ECFDF5;
        border-color: #6EE7B7;
        color: #065F46;
    }
    
    .action-btn.sync .action-btn-icon {
        background: #10B981;
        color: #FFFFFF;
    }
    
    .action-btn.sync:hover {
        background: #D1FAE5;
        color: #065F46;
    }
    
    .action-btn.manage-vendors {
        background: #F5F3FF;
        border-color: #C4B5FD;
        color: #5B21B6;
    }
    
    .action-btn.manage-vendors .action-btn-icon {
        background: #8B5CF6;
        color: #FFFFFF;
    }
    
    .action-btn.manage-vendors:hover {
        background: #EDE9FE;
        color: #5B21B6;
    }
    
    /* Metrics Section */
    .metrics-section {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #E5E7EB;
    }
    
    .metrics-title {
        font-size: 14px;
        font-weight: 600;
        color: #232F3E;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .metrics-title svg {
        color: #FF9900;
    }
    
    .metric-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed #E5E7EB;
    }
    
    .metric-row:last-child {
        border-bottom: none;
    }
    
    .metric-label {
        font-size: 14px;
        color: #6B7280;
    }
    
    .metric-value {
        font-size: 16px;
        font-weight: 700;
        color: #232F3E;
    }
    
    .metric-value.success {
        color: #067D62;
    }
    
    /* Bottom Grid */
    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }
    
    @media (max-width: 992px) {
        .bottom-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Table Styles */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .modern-table thead th {
        background: #F9FAFB;
        padding: 14px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #E5E7EB;
    }
    
    .modern-table thead th:first-child {
        border-radius: 8px 0 0 0;
    }
    
    .modern-table thead th:last-child {
        border-radius: 0 8px 0 0;
        text-align: right;
    }
    
    .modern-table tbody tr {
        transition: background 0.15s ease;
    }
    
    .modern-table tbody tr:hover {
        background: #F9FAFB;
    }
    
    .modern-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #F3F4F6;
        font-size: 14px;
        color: #374151;
    }
    
    .modern-table tbody td:last-child {
        text-align: right;
    }
    
    .vendor-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .vendor-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #FF9900 0%, #FFBA57 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }
    
    .vendor-name {
        font-weight: 600;
        color: #232F3E;
    }
    
    .vendor-products {
        font-size: 12px;
        color: #9CA3AF;
    }
    
    .order-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        background: #ECFDF5;
        color: #065F46;
    }
    
    .view-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #F3F4F6;
        color: #6B7280;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    
    .view-btn:hover {
        background: #232F3E;
        color: #FFFFFF;
        text-decoration: none;
    }
    
    /* Activity Feed */
    .activity-feed {
        max-height: 340px;
        overflow-y: auto;
    }
    
    .activity-feed::-webkit-scrollbar {
        width: 6px;
    }
    
    .activity-feed::-webkit-scrollbar-track {
        background: #F3F4F6;
        border-radius: 3px;
    }
    
    .activity-feed::-webkit-scrollbar-thumb {
        background: #D1D5DB;
        border-radius: 3px;
    }
    
    .activity-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px;
        border-radius: 10px;
        margin-bottom: 10px;
        background: #F9FAFB;
        transition: all 0.2s ease;
    }
    
    .activity-item:hover {
        background: #F3F4F6;
        transform: translateX(4px);
    }
    
    .activity-item:last-child {
        margin-bottom: 0;
    }
    
    .activity-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .activity-icon.completed {
        background: #ECFDF5;
        color: #059669;
    }
    
    .activity-icon.shipped {
        background: #EFF6FF;
        color: #3B82F6;
    }
    
    .activity-icon.pending {
        background: #FEF3C7;
        color: #D97706;
    }
    
    .activity-icon.default {
        background: #F3F4F6;
        color: #6B7280;
    }
    
    .activity-content {
        flex: 1;
        min-width: 0;
    }
    
    .activity-title {
        font-weight: 600;
        color: #232F3E;
        font-size: 14px;
        margin-bottom: 2px;
    }
    
    .activity-vendor {
        font-size: 12px;
        color: #9CA3AF;
    }
    
    .activity-meta {
        text-align: right;
        flex-shrink: 0;
    }
    
    .activity-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    
    .activity-status.completed {
        background: #ECFDF5;
        color: #065F46;
    }
    
    .activity-status.shipped {
        background: #EFF6FF;
        color: #1E40AF;
    }
    
    .activity-status.pending {
        background: #FEF3C7;
        color: #92400E;
    }
    
    .activity-time {
        font-size: 11px;
        color: #9CA3AF;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #9CA3AF;
    }
    
    .empty-state svg {
        width: 48px;
        height: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }
    
    .empty-state p {
        font-size: 14px;
        margin: 0;
    }
    
    /* Loading spinner */
    .btn-loading .action-btn-icon svg {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Responsive header */
    @media (max-width: 768px) {
        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
        
        .header-actions {
            width: 100%;
        }
        
        .header-btn {
            flex: 1;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="dropship-dashboard">
    <!-- Dashboard Header - Amazon style -->
    <div class="dashboard-header">
        <div class="dashboard-header-content">
            <h1>
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
                Dropship Dashboard
            </h1>
            <p class="dashboard-header-subtitle">Orders, vendors, and performance metrics at a glance.</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('dropship.orders.index') }}" class="header-btn header-btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
                View Orders
            </a>
            <a href="{{ route('dropship.vendors.create') }}" class="header-btn header-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Vendor
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <!-- Orders Today -->
        <div class="kpi-card orders">
            <div class="kpi-header">
                <div class="kpi-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                </div>
                <div class="kpi-trend neutral">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Today
                </div>
            </div>
            <div class="kpi-label">Orders Today</div>
            <div class="kpi-value">{{ $stats['orders_today'] ?? 0 }}</div>
            <div class="kpi-subtitle">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                </svg>
                This week: <span>{{ $stats['orders_this_week'] ?? 0 }}</span>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="kpi-card pending">
            <div class="kpi-header">
                <div class="kpi-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                @if(($stats['pending_orders'] ?? 0) > 0)
                <div class="kpi-trend up">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    </svg>
                    Active
                </div>
                @endif
            </div>
            <div class="kpi-label">Pending Orders</div>
            <div class="kpi-value">{{ $stats['pending_orders'] ?? 0 }}</div>
            <div class="kpi-subtitle">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                </svg>
                Shipped: <span>{{ $stats['shipped_orders'] ?? 0 }}</span>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="kpi-card completed">
            <div class="kpi-header">
                <div class="kpi-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div class="kpi-trend up">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Success
                </div>
            </div>
            <div class="kpi-label">Completed</div>
            <div class="kpi-value">{{ $stats['completed_orders'] ?? 0 }}</div>
            <div class="kpi-subtitle">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                </svg>
                This month: <span>{{ $stats['orders_this_month'] ?? 0 }}</span>
            </div>
        </div>

        <!-- Active Vendors -->
        <div class="kpi-card vendors">
            <div class="kpi-header">
                <div class="kpi-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
            </div>
            <div class="kpi-label">Active Vendors</div>
            <div class="kpi-value">{{ $stats['active_vendors'] ?? 0 }}</div>
            <div class="kpi-subtitle">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                </svg>
                Products: <span>{{ $stats['total_dropship_products'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Alert Banner -->
    @if(($stats['failed_syncs'] ?? 0) > 0)
    <div class="alert-banner">
        <div class="alert-banner-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>
        <div class="alert-banner-content">
            <div class="alert-banner-title">Sync Issues Detected</div>
            <div class="alert-banner-text">
                <strong>{{ $stats['failed_syncs'] }}</strong> orders have failed to sync with WooCommerce. 
                <a href="{{ route('dropship.orders.index', ['sync_status' => 'failed']) }}" class="alert-banner-link">View failed syncs →</a>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <div class="content-grid">
        <!-- Chart Section -->
        <div class="dash-card">
            <div class="dash-card-header">
                <h3 class="dash-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    Order Trends (Last 30 Days)
                </h3>
            </div>
            <div class="dash-card-body">
                <div class="chart-container">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dash-card">
            <div class="dash-card-header">
                <h3 class="dash-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                    </svg>
                    Quick Actions
                </h3>
            </div>
            <div class="dash-card-body">
                <div class="quick-actions">
                    <a href="{{ route('dropship.vendors.create') }}" class="action-btn add-vendor">
                        <div class="action-btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </div>
                        <span>Add New Vendor</span>
                    </a>
                    
                    <a href="{{ route('dropship.orders.index') }}" class="action-btn view-orders">
                        <div class="action-btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                <line x1="3" y1="18" x2="3.01" y2="18"></line>
                            </svg>
                        </div>
                        <span>View All Orders</span>
                    </a>
                    
                    <button type="button" class="action-btn sync" id="sync-products-btn">
                        <div class="action-btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <polyline points="1 20 1 14 7 14"></polyline>
                                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                            </svg>
                        </div>
                        <span>Sync from WooCommerce</span>
                    </button>
                    
                    <a href="{{ route('dropship.vendors.index') }}" class="action-btn manage-vendors">
                        <div class="action-btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                        </div>
                        <span>Manage Vendors</span>
                    </a>
                </div>

                <!-- Performance Metrics -->
                <div class="metrics-section">
                    <h4 class="metrics-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                        </svg>
                        Performance Metrics
                    </h4>
                    <div class="metric-row">
                        <span class="metric-label">Avg. Fulfillment Time</span>
                        <span class="metric-value">{{ $stats['avg_fulfillment_time'] ?? 0 }}h</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Monthly Revenue</span>
                        <span class="metric-value success">@format_currency($stats['revenue_this_month'] ?? 0)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="bottom-grid">
        <!-- Top Vendors -->
        <div class="dash-card">
            <div class="dash-card-header">
                <h3 class="dash-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    Top Performing Vendors
                </h3>
            </div>
            <div class="dash-card-body" style="padding: 0;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th style="text-align: center;">Completed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topVendors ?? [] as $vendor)
                        <tr>
                            <td>
                                <div class="vendor-info">
                                    <div class="vendor-avatar">
                                        {{ strtoupper(substr($vendor->display_name ?? 'V', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="vendor-name">{{ $vendor->display_name ?? 'Unknown' }}</div>
                                        <div class="vendor-products">{{ $vendor->products_count ?? 0 }} products</div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="order-badge">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    {{ $vendor->completed_orders_count ?? 0 }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('dropship.vendors.show', $vendor->id) }}" class="view-btn" title="View Details">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    <p>No vendors yet. Add your first vendor to get started!</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="dash-card">
            <div class="dash-card-header">
                <h3 class="dash-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    Recent Activity
                </h3>
            </div>
            <div class="dash-card-body">
                <div class="activity-feed">
                    @forelse($recentActivity ?? [] as $activity)
                    <div class="activity-item">
                        <div class="activity-icon {{ $activity->fulfillment_status ?? 'default' }}">
                            @switch($activity->fulfillment_status ?? '')
                                @case('completed')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    @break
                                @case('shipped')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="1" y="3" width="15" height="13"></rect>
                                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                    </svg>
                                    @break
                                @case('pending')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    @break
                                @default
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    </svg>
                            @endswitch
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">{{ $activity->transaction->invoice_no ?? 'N/A' }}</div>
                            <div class="activity-vendor">{{ $activity->vendor->display_name ?? 'Unknown Vendor' }}</div>
                        </div>
                        <div class="activity-meta">
                            <div class="activity-status {{ $activity->fulfillment_status ?? 'default' }}">
                                {{ ucfirst($activity->fulfillment_status ?? 'Processing') }}
                            </div>
                            <div class="activity-time">{{ $activity->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        <p>No recent activity to display</p>
                    </div>
                    @endforelse
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
    // Orders Chart with Amazon theme colors
    var ctx = document.getElementById('ordersChart');
    if (ctx) {
        ctx = ctx.getContext('2d');
        var chartData = @json($chartData ?? ['labels' => [], 'datasets' => []]);
        
        // Update chart colors to match Amazon theme
        if (chartData.datasets) {
            chartData.datasets.forEach(function(dataset, index) {
                if (index === 0) { // Total Orders
                    dataset.borderColor = '#232F3E';
                    dataset.backgroundColor = 'rgba(35, 47, 62, 0.1)';
                    dataset.pointBackgroundColor = '#232F3E';
                } else if (index === 1) { // Completed
                    dataset.borderColor = '#067D62';
                    dataset.backgroundColor = 'rgba(6, 125, 98, 0.1)';
                    dataset.pointBackgroundColor = '#067D62';
                } else if (index === 2) { // Pending
                    dataset.borderColor = '#FF9900';
                    dataset.backgroundColor = 'rgba(255, 153, 0, 0.1)';
                    dataset.pointBackgroundColor = '#FF9900';
                }
                dataset.fill = true;
                dataset.tension = 0.4;
                dataset.borderWidth = 2;
                dataset.pointRadius = 4;
                dataset.pointHoverRadius = 6;
            });
        }
        
        new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#232F3E',
                        titleFont: {
                            size: 13,
                            weight: '600'
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 11
                            },
                            color: '#6B7280'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#6B7280',
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    }

    // Sync Products Button with loading state
    $('#sync-products-btn').on('click', function() {
        var btn = $(this);
        var originalHtml = btn.html();
        
        btn.addClass('btn-loading').prop('disabled', true);
        btn.find('span').text('Syncing...');
        
        $.ajax({
            url: '{{ route("dropship.sync-products") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function() {
                toastr.error('Failed to initiate sync');
            },
            complete: function() {
                btn.removeClass('btn-loading').prop('disabled', false);
                btn.html(originalHtml);
            }
        });
    });
});
</script>
@endsection
