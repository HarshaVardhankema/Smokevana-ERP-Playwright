@extends('layouts.app')
@section('title', __('account.balance_sheet'))

@section('css')
<style>
    /* ===== BALANCE SHEET MODERN DESIGN ===== */
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
    
    :root {
        --bs-primary: #7c3aed;
        --bs-primary-light: #a78bfa;
        --bs-primary-dark: #5b21b6;
        --bs-success: #10b981;
        --bs-danger: #ef4444;
        --bs-warning: #f59e0b;
        --bs-info: #06b6d4;
        --bs-gradient-start: #7c3aed;
        --bs-gradient-end: #ec4899;
        --bs-card-shadow: 0 10px 40px rgba(124, 58, 237, 0.15);
        --bs-border-radius: 16px;
    }
    
    .bs-container {
        font-family: 'Outfit', sans-serif;
        padding: 0;
        background: linear-gradient(135deg, #faf5ff 0%, #fdf2f8 50%, #f0fdf4 100%);
        min-height: 100vh;
    }
    
    /* Header */
    .bs-header {
        background: linear-gradient(135deg, var(--bs-gradient-start) 0%, var(--bs-gradient-end) 100%);
        padding: 2rem 2.5rem;
        border-radius: 0 0 30px 30px;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .bs-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 60%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse-slow 8s ease-in-out infinite;
    }
    
    @keyframes pulse-slow {
        0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.5; }
        50% { transform: scale(1.2) rotate(10deg); opacity: 0.8; }
    }
    
    .bs-header-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
    }
    
    .bs-title-section h1 {
        color: white;
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .bs-title-section h1 i {
        font-size: 1.5rem;
        background: rgba(255,255,255,0.2);
        padding: 0.5rem;
        border-radius: 12px;
    }
    
    .bs-subtitle {
        color: rgba(255,255,255,0.85);
        font-size: 1rem;
        margin: 0;
    }
    
    .bs-date-badge {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        padding: 0.75rem 1.25rem;
        border-radius: 50px;
        color: white;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid rgba(255,255,255,0.3);
    }
    
    /* Filters Section */
    .bs-filters {
        background: white;
        border-radius: var(--bs-border-radius);
        padding: 1.5rem;
        margin: 0 1.5rem 2rem;
        box-shadow: var(--bs-card-shadow);
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        align-items: flex-end;
    }
    
    .bs-filter-group {
        flex: 1;
        min-width: 200px;
    }
    
    .bs-filter-group label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .bs-filter-group label i {
        margin-right: 0.5rem;
        color: var(--bs-primary);
    }
    
    .bs-filter-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }
    
    .bs-filter-input:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
        outline: none;
        background: white;
    }
    
    .bs-refresh-btn {
        background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-dark) 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .bs-refresh-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(124, 58, 237, 0.35);
    }
    
    .bs-refresh-btn.loading i {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Summary Cards */
    .bs-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        padding: 0 1.5rem;
        margin-bottom: 2rem;
    }
    
    .bs-summary-card {
        background: white;
        border-radius: var(--bs-border-radius);
        padding: 1.5rem;
        box-shadow: var(--bs-card-shadow);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .bs-summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 50px rgba(124, 58, 237, 0.2);
    }
    
    .bs-summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    
    .bs-summary-card.assets::before {
        background: linear-gradient(90deg, var(--bs-success), #34d399);
    }
    
    .bs-summary-card.liabilities::before {
        background: linear-gradient(90deg, var(--bs-danger), #f87171);
    }
    
    .bs-summary-card.equity::before {
        background: linear-gradient(90deg, var(--bs-primary), var(--bs-primary-light));
    }
    
    .bs-summary-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .bs-summary-card.assets .bs-summary-icon {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(52, 211, 153, 0.15));
        color: var(--bs-success);
    }
    
    .bs-summary-card.liabilities .bs-summary-icon {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(248, 113, 113, 0.15));
        color: var(--bs-danger);
    }
    
    .bs-summary-card.equity .bs-summary-icon {
        background: linear-gradient(135deg, rgba(124, 58, 237, 0.15), rgba(167, 139, 250, 0.15));
        color: var(--bs-primary);
    }
    
    .bs-summary-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .bs-summary-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
    }
    
    .bs-summary-card.assets .bs-summary-value { color: var(--bs-success); }
    .bs-summary-card.liabilities .bs-summary-value { color: var(--bs-danger); }
    .bs-summary-card.equity .bs-summary-value { color: var(--bs-primary); }
    
    /* Balance Indicator */
    .bs-balance-indicator {
        background: white;
        border-radius: var(--bs-border-radius);
        padding: 1.5rem;
        margin: 0 1.5rem 2rem;
        box-shadow: var(--bs-card-shadow);
        text-align: center;
    }
    
    .bs-balance-status {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .bs-balance-status.balanced {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(52, 211, 153, 0.15));
        color: var(--bs-success);
        border: 2px solid rgba(16, 185, 129, 0.3);
    }
    
    .bs-balance-status.unbalanced {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(248, 113, 113, 0.15));
        color: var(--bs-danger);
        border: 2px solid rgba(239, 68, 68, 0.3);
    }
    
    .bs-balance-status i {
        font-size: 1.25rem;
    }
    
    /* Visual Chart */
    .bs-visual-section {
        background: white;
        border-radius: var(--bs-border-radius);
        padding: 2rem;
        margin: 0 1.5rem 2rem;
        box-shadow: var(--bs-card-shadow);
    }
    
    .bs-visual-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .bs-visual-title i {
        color: var(--bs-primary);
    }
    
    .bs-bar-chart {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .bs-bar-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .bs-bar-label {
        width: 120px;
        font-weight: 500;
        color: #6b7280;
        font-size: 0.9rem;
    }
    
    .bs-bar-track {
        flex: 1;
        height: 32px;
        background: #f3f4f6;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
    }
    
    .bs-bar-fill {
        height: 100%;
        border-radius: 8px;
        transition: width 1s ease-out;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 1rem;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        min-width: fit-content;
    }
    
    .bs-bar-fill.assets-bar {
        background: linear-gradient(90deg, var(--bs-success), #34d399);
    }
    
    .bs-bar-fill.liabilities-bar {
        background: linear-gradient(90deg, var(--bs-danger), #f87171);
    }
    
    /* Detail Cards */
    .bs-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
        padding: 0 1.5rem;
        margin-bottom: 2rem;
    }
    
    .bs-detail-card {
        background: white;
        border-radius: var(--bs-border-radius);
        box-shadow: var(--bs-card-shadow);
        overflow: hidden;
    }
    
    .bs-detail-header {
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .bs-detail-header:hover {
        background: #f9fafb;
    }
    
    .bs-detail-card.liabilities-card .bs-detail-header {
        border-bottom: 3px solid var(--bs-danger);
    }
    
    .bs-detail-card.assets-card .bs-detail-header {
        border-bottom: 3px solid var(--bs-success);
    }
    
    .bs-detail-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
    }
    
    .bs-detail-title i {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }
    
    .bs-detail-card.liabilities-card .bs-detail-title i {
        background: rgba(239, 68, 68, 0.15);
        color: var(--bs-danger);
    }
    
    .bs-detail-card.assets-card .bs-detail-title i {
        background: rgba(16, 185, 129, 0.15);
        color: var(--bs-success);
    }
    
    .bs-detail-toggle {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #f3f4f6;
        color: #6b7280;
        transition: all 0.3s ease;
    }
    
    .bs-detail-card.expanded .bs-detail-toggle {
        transform: rotate(180deg);
        background: var(--bs-primary);
        color: white;
    }
    
    .bs-detail-body {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-out;
    }
    
    .bs-detail-card.expanded .bs-detail-body {
        max-height: 1000px;
    }
    
    .bs-detail-content {
        padding: 1.5rem;
    }
    
    .bs-line-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.875rem 1rem;
        border-radius: 10px;
        margin-bottom: 0.5rem;
        background: #f9fafb;
        transition: all 0.2s ease;
    }
    
    .bs-line-item:hover {
        background: #f3f4f6;
        transform: translateX(5px);
    }
    
    .bs-line-item:last-child {
        margin-bottom: 0;
    }
    
    .bs-line-label {
        font-weight: 500;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .bs-line-label i {
        font-size: 0.75rem;
        color: #9ca3af;
    }
    
    .bs-line-value {
        font-weight: 600;
        color: #1f2937;
    }
    
    .bs-detail-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        margin-top: 1rem;
        border-radius: 12px;
        font-weight: 700;
    }
    
    .bs-detail-card.liabilities-card .bs-detail-total {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(248, 113, 113, 0.1));
        color: var(--bs-danger);
    }
    
    .bs-detail-card.assets-card .bs-detail-total {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(52, 211, 153, 0.1));
        color: var(--bs-success);
    }
    
    /* Action Buttons */
    .bs-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
        padding: 0 1.5rem 2rem;
        flex-wrap: wrap;
    }
    
    .bs-action-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.95rem;
    }
    
    .bs-action-btn.primary {
        background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-dark) 100%);
        color: white;
    }
    
    .bs-action-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(124, 58, 237, 0.35);
    }
    
    .bs-action-btn.secondary {
        background: white;
        color: #374151;
        border: 2px solid #e5e7eb;
    }
    
    .bs-action-btn.secondary:hover {
        border-color: var(--bs-primary);
        color: var(--bs-primary);
        background: rgba(124, 58, 237, 0.05);
    }
    
    /* Loading State */
    .bs-loading {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #9ca3af;
    }
    
    .bs-loading i {
        animation: spin 1s linear infinite;
    }
    
    /* Print Styles */
    @media print {
        .bs-header { border-radius: 0; }
        .bs-filters, .bs-actions, .no-print { display: none !important; }
        .bs-detail-card { break-inside: avoid; }
        .bs-detail-body { max-height: none !important; }
        .bs-container { background: white; }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .bs-header { padding: 1.5rem; border-radius: 0 0 20px 20px; }
        .bs-title-section h1 { font-size: 1.5rem; }
        .bs-filters { flex-direction: column; margin: 0 1rem 1.5rem; }
        .bs-summary-grid { padding: 0 1rem; }
        .bs-details-grid { grid-template-columns: 1fr; padding: 0 1rem; }
        .bs-visual-section { margin: 0 1rem 1.5rem; }
        .bs-balance-indicator { margin: 0 1rem 1.5rem; }
        .bs-bar-label { width: 80px; font-size: 0.8rem; }
    }
    
    /* Account sub-items */
    .bs-account-section {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px dashed #e5e7eb;
    }
    
    .bs-account-section-title {
        font-size: 0.8rem;
        font-weight: 600;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
        padding-left: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="bs-container">
    <!-- Header -->
    <div class="bs-header no-print">
        <div class="bs-header-content">
            <div class="bs-title-section">
                <h1>
                    <i class="fas fa-balance-scale"></i>
                    @lang('account.balance_sheet')
                </h1>
                <p class="bs-subtitle">{{session()->get('business.name')}} - Financial Position Statement</p>
            </div>
            <div class="bs-date-badge">
                <i class="fas fa-calendar-alt"></i>
                <span id="display_date">{{@format_date('now')}}</span>
            </div>
        </div>
    </div>
    
    <!-- Print Header -->
    <div class="hidden print_section" style="text-align: center; padding: 1rem; border-bottom: 2px solid #7c3aed;">
        <h2 style="margin: 0; color: #1f2937;">{{session()->get('business.name')}}</h2>
        <h3 style="margin: 0.5rem 0 0; color: #6b7280;">@lang('account.balance_sheet') - <span id="print_date">{{@format_date('now')}}</span></h3>
    </div>
    
    <!-- Filters -->
    <div class="bs-filters no-print">
        <div class="bs-filter-group">
            <label><i class="fas fa-map-marker-alt"></i> @lang('purchase.business_location')</label>
            {!! Form::select('bal_sheet_location_id', $business_locations, null, ['class' => 'bs-filter-input', 'id' => 'bal_sheet_location_id']); !!}
        </div>
        <div class="bs-filter-group">
            <label><i class="fas fa-calendar"></i> @lang('messages.filter_by_date')</label>
            <input type="text" id="end_date" value="{{@format_date('now')}}" class="bs-filter-input" readonly>
        </div>
        <button type="button" class="bs-refresh-btn" id="refresh_btn" onclick="update_balance_sheet()">
            <i class="fas fa-sync-alt"></i>
            Refresh Data
        </button>
    </div>
    
    <!-- Summary Cards -->
    <div class="bs-summary-grid">
        <div class="bs-summary-card assets">
            <div class="bs-summary-icon">
                <i class="fas fa-arrow-trend-up"></i>
            </div>
            <div class="bs-summary-label">@lang('account.total_assets')</div>
            <div class="bs-summary-value" id="summary_total_assets">
                <span class="bs-loading"><i class="fas fa-spinner"></i> Loading...</span>
            </div>
        </div>
        <div class="bs-summary-card liabilities">
            <div class="bs-summary-icon">
                <i class="fas fa-arrow-trend-down"></i>
            </div>
            <div class="bs-summary-label">@lang('account.total_liability')</div>
            <div class="bs-summary-value" id="summary_total_liabilities">
                <span class="bs-loading"><i class="fas fa-spinner"></i> Loading...</span>
            </div>
        </div>
        <div class="bs-summary-card equity">
            <div class="bs-summary-icon">
                <i class="fas fa-coins"></i>
            </div>
            <div class="bs-summary-label">Net Worth (Assets - Liabilities)</div>
            <div class="bs-summary-value" id="summary_net_worth">
                <span class="bs-loading"><i class="fas fa-spinner"></i> Loading...</span>
            </div>
        </div>
    </div>
    
    <!-- Balance Indicator -->
    <div class="bs-balance-indicator">
        <div class="bs-balance-status balanced" id="balance_status">
            <i class="fas fa-check-circle"></i>
            <span>Calculating balance...</span>
        </div>
    </div>
    
    <!-- Visual Chart -->
    <div class="bs-visual-section">
        <div class="bs-visual-title">
            <i class="fas fa-chart-bar"></i>
            Assets vs Liabilities Comparison
        </div>
        <div class="bs-bar-chart">
            <div class="bs-bar-item">
                <div class="bs-bar-label">Assets</div>
                <div class="bs-bar-track">
                    <div class="bs-bar-fill assets-bar" id="assets_bar" style="width: 0%;">
                        <span id="assets_bar_value">$0</span>
                    </div>
                </div>
            </div>
            <div class="bs-bar-item">
                <div class="bs-bar-label">Liabilities</div>
                <div class="bs-bar-track">
                    <div class="bs-bar-fill liabilities-bar" id="liabilities_bar" style="width: 0%;">
                        <span id="liabilities_bar_value">$0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detail Cards -->
    <div class="bs-details-grid">
        <!-- Liabilities Card -->
        <div class="bs-detail-card liabilities-card expanded">
            <div class="bs-detail-header" onclick="toggleCard(this)">
                <div class="bs-detail-title">
                    <i class="fas fa-file-invoice-dollar"></i>
                    @lang('account.liability')
                </div>
                <div class="bs-detail-toggle">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            <div class="bs-detail-body">
                <div class="bs-detail-content">
                    <div class="bs-line-item">
                        <span class="bs-line-label">
                            <i class="fas fa-circle"></i>
                            @lang('account.supplier_due')
                        </span>
                        <span class="bs-line-value remote-data" id="supplier_due">
                            <span class="bs-loading"><i class="fas fa-spinner"></i></span>
                        </span>
                        <input type="hidden" id="hidden_supplier_due" class="liability">
                    </div>
                    
                    <div class="bs-detail-total">
                        <span>@lang('account.total_liability')</span>
                        <span id="total_liabilty">
                            <span class="bs-loading"><i class="fas fa-spinner"></i></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Assets Card -->
        <div class="bs-detail-card assets-card expanded">
            <div class="bs-detail-header" onclick="toggleCard(this)">
                <div class="bs-detail-title">
                    <i class="fas fa-wallet"></i>
                    @lang('account.assets')
                </div>
                <div class="bs-detail-toggle">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            <div class="bs-detail-body">
                <div class="bs-detail-content" id="assets_content">
                    <div class="bs-line-item">
                        <span class="bs-line-label">
                            <i class="fas fa-circle"></i>
                            @lang('account.customer_due')
                        </span>
                        <span class="bs-line-value remote-data" id="customer_due">
                            <span class="bs-loading"><i class="fas fa-spinner"></i></span>
                        </span>
                        <input type="hidden" id="hidden_customer_due" class="asset">
                    </div>
                    <div class="bs-line-item">
                        <span class="bs-line-label">
                            <i class="fas fa-circle"></i>
                            @lang('report.closing_stock')
                        </span>
                        <span class="bs-line-value remote-data" id="closing_stock">
                            <span class="bs-loading"><i class="fas fa-spinner"></i></span>
                        </span>
                        <input type="hidden" id="hidden_closing_stock" class="asset">
                    </div>
                    
                    <!-- Account Balances Section -->
                    <div class="bs-account-section">
                        <div class="bs-account-section-title">
                            <i class="fas fa-university"></i> @lang('account.account_balances')
                        </div>
                        <div id="account_balances_container">
                            <div class="bs-line-item">
                                <span class="bs-loading"><i class="fas fa-spinner"></i> Loading accounts...</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bs-detail-total">
                        <span>@lang('account.total_assets')</span>
                        <span id="total_assets">
                            <span class="bs-loading"><i class="fas fa-spinner"></i></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="bs-actions no-print">
        <button type="button" class="bs-action-btn primary" onclick="window.print()">
            <i class="fas fa-print"></i>
            Print Report
        </button>
        <button type="button" class="bs-action-btn secondary" onclick="exportToExcel()">
            <i class="fas fa-file-excel"></i>
            Export Excel
        </button>
        <button type="button" class="bs-action-btn secondary" onclick="exportToPDF()">
            <i class="fas fa-file-pdf"></i>
            Export PDF
        </button>
    </div>
</div>
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        // Date picker
        $('#end_date').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });
        
        // Initial load
        update_balance_sheet();
        
        // Event listeners
        $('#end_date').change(function() {
            update_balance_sheet();
            var newDate = $(this).val();
            $('#display_date').text(newDate);
            $('#print_date').text(newDate);
        });
        
        $('#bal_sheet_location_id').change(function() {
            update_balance_sheet();
        });
    });
    
    function toggleCard(header) {
        var card = $(header).closest('.bs-detail-card');
        card.toggleClass('expanded');
    }
    
    function update_balance_sheet() {
        var loader = '<span class="bs-loading"><i class="fas fa-spinner"></i></span>';
        
        // Show loading state
        $('#refresh_btn').addClass('loading');
        $('span.remote-data').each(function() {
            $(this).html(loader);
        });
        $('#summary_total_assets, #summary_total_liabilities, #summary_net_worth').html(loader);
        $('#total_liabilty, #total_assets').html(loader);
        $('#account_balances_container').html('<div class="bs-line-item"><span class="bs-loading"><i class="fas fa-spinner"></i> Loading accounts...</span></div>');
        
        // Reset bars
        $('#assets_bar').css('width', '0%');
        $('#liabilities_bar').css('width', '0%');
        
        var end_date = $('input#end_date').val();
        var location_id = $('#bal_sheet_location_id').val();
        
        $.ajax({
            url: "{{action([\App\Http\Controllers\AccountReportsController::class, 'balanceSheet'])}}?end_date=" + end_date + '&location_id=' + location_id,
            dataType: "json",
            success: function(result) {
                // Update supplier due
                $('span#supplier_due').text(__currency_trans_from_en(result.supplier_due, true));
                __write_number($('input#hidden_supplier_due'), result.supplier_due);
                
                // Update customer due
                $('span#customer_due').text(__currency_trans_from_en(result.customer_due, true));
                __write_number($('input#hidden_customer_due'), result.customer_due);
                
                // Update closing stock
                $('span#closing_stock').text(__currency_trans_from_en(result.closing_stock, true));
                __write_number($('input#hidden_closing_stock'), result.closing_stock);
                
                // Update account balances
                var account_balances = result.account_balances;
                $('#account_balances_container').html('');
                
                for (var key in account_balances) {
                    var accnt_bal = __currency_trans_from_en(result.account_balances[key]);
                    var accnt_bal_with_sym = __currency_trans_from_en(result.account_balances[key], true);
                    var account_item = '<div class="bs-line-item">' +
                        '<span class="bs-line-label"><i class="fas fa-circle"></i>' + key + '</span>' +
                        '<span class="bs-line-value">' + accnt_bal_with_sym + '</span>' +
                        '<input type="hidden" class="asset" value="' + accnt_bal + '">' +
                        '</div>';
                    $('#account_balances_container').append(account_item);
                }
                
                // Calculate totals
                var total_liability = 0;
                var total_assets = 0;
                
                $('input.liability').each(function() {
                    total_liability += __read_number($(this));
                });
                
                $('input.asset').each(function() {
                    total_assets += __read_number($(this));
                });
                
                var net_worth = total_assets - total_liability;
                
                // Update totals
                $('span#total_liabilty').text(__currency_trans_from_en(total_liability, true));
                $('span#total_assets').text(__currency_trans_from_en(total_assets, true));
                
                // Update summary cards
                $('#summary_total_assets').text(__currency_trans_from_en(total_assets, true));
                $('#summary_total_liabilities').text(__currency_trans_from_en(total_liability, true));
                $('#summary_net_worth').text(__currency_trans_from_en(net_worth, true));
                
                // Update balance indicator
                var balanceStatus = $('#balance_status');
                if (Math.abs(net_worth) < 0.01) {
                    balanceStatus.removeClass('unbalanced').addClass('balanced');
                    balanceStatus.html('<i class="fas fa-check-circle"></i><span>Balance Sheet is Balanced</span>');
                } else if (net_worth > 0) {
                    balanceStatus.removeClass('unbalanced').addClass('balanced');
                    balanceStatus.html('<i class="fas fa-arrow-up"></i><span>Net Positive: ' + __currency_trans_from_en(net_worth, true) + '</span>');
                } else {
                    balanceStatus.removeClass('balanced').addClass('unbalanced');
                    balanceStatus.html('<i class="fas fa-exclamation-triangle"></i><span>Net Negative: ' + __currency_trans_from_en(net_worth, true) + '</span>');
                }
                
                // Update visual bars
                var maxValue = Math.max(total_assets, total_liability, 1);
                var assetsPercent = (total_assets / maxValue) * 100;
                var liabilitiesPercent = (total_liability / maxValue) * 100;
                
                setTimeout(function() {
                    $('#assets_bar').css('width', assetsPercent + '%');
                    $('#assets_bar_value').text(__currency_trans_from_en(total_assets, true));
                    $('#liabilities_bar').css('width', liabilitiesPercent + '%');
                    $('#liabilities_bar_value').text(__currency_trans_from_en(total_liability, true));
                }, 100);
                
                // Remove loading state
                $('#refresh_btn').removeClass('loading');
            },
            error: function() {
                $('#refresh_btn').removeClass('loading');
                toastr.error('Failed to load balance sheet data');
            }
        });
    }
    
    function exportToExcel() {
        var end_date = $('input#end_date').val();
        var location_id = $('#bal_sheet_location_id').val();
        
        // Create table data for export
        var csvContent = "Balance Sheet Report\n";
        csvContent += "Date: " + end_date + "\n\n";
        csvContent += "LIABILITIES\n";
        csvContent += "Supplier Due," + $('#supplier_due').text() + "\n";
        csvContent += "Total Liabilities," + $('#total_liabilty').text() + "\n\n";
        csvContent += "ASSETS\n";
        csvContent += "Customer Due," + $('#customer_due').text() + "\n";
        csvContent += "Closing Stock," + $('#closing_stock').text() + "\n";
        
        $('#account_balances_container .bs-line-item').each(function() {
            var label = $(this).find('.bs-line-label').text().trim();
            var value = $(this).find('.bs-line-value').text().trim();
            csvContent += label + "," + value + "\n";
        });
        
        csvContent += "Total Assets," + $('#total_assets').text() + "\n";
        
        // Download
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement("a");
        var url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", "balance_sheet_" + end_date.replace(/\//g, '-') + ".csv");
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        toastr.success('Excel file downloaded successfully');
    }
    
    function exportToPDF() {
        window.print();
        toastr.info('Use your browser\'s print dialog to save as PDF');
    }
</script>
@endsection
