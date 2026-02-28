@extends('layouts.app')
@section('title', 'Accounts Payable')

@section('css')
<style>
/* Accounts Payable - Professional Orange/Amber Theme (Liabilities) with Micro-interactions */
.ap-page { 
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); 
    min-height: 100vh; 
    padding-bottom: 40px; 
}

/* Header Banner */
.ap-header-banner {
    background: linear-gradient(135deg, #d97706 0%, #f59e0b 50%, #fbbf24 100%);
    border-radius: 16px; 
    padding: 28px 32px; 
    margin-bottom: 24px;
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    flex-wrap: wrap; 
    gap: 20px;
    box-shadow: 0 8px 30px rgba(217, 119, 6, 0.25); 
    position: relative; 
    overflow: hidden;
    animation: slideDown 0.5s ease-out;
}
.ap-header-banner::before { 
    content: ''; 
    position: absolute; 
    top: -50%; 
    right: -10%; 
    width: 300px; 
    height: 300px; 
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%); 
    border-radius: 50%; 
    animation: pulse 4s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.15; }
    50% { transform: scale(1.1); opacity: 0.2; }
}
.ap-header-banner h1, .ap-header-banner .subtitle, .ap-header-banner i { color: #fff !important; }
.ap-header-banner h1 { 
    font-size: 28px; 
    font-weight: 700; 
    margin: 0 0 6px 0; 
    display: flex; 
    align-items: center; 
    gap: 12px; 
}
.ap-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }
.ap-btn-back { 
    background: rgba(255,255,255,0.15); 
    color: #fff; 
    border: 1px solid rgba(255,255,255,0.3); 
    padding: 12px 24px; 
    border-radius: 10px; 
    font-weight: 600; 
    font-size: 14px; 
    text-decoration: none; 
    display: inline-flex; 
    align-items: center; 
    gap: 8px; 
    transition: all 0.3s ease; 
    cursor: pointer;
}
.ap-btn-back:hover { 
    background: rgba(255,255,255,0.25); 
    color: #fff; 
    text-decoration: none; 
    transform: translateX(-3px);
}

/* Total AP Banner */
.ap-total-banner {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    border-radius: 16px;
    padding: 24px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 8px 30px rgba(220, 38, 38, 0.25);
    animation: slideUp 0.5s ease-out 0.1s both;
}
.ap-total-main {
    display: flex;
    align-items: center;
    gap: 20px;
}
.ap-total-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 28px;
}
.ap-total-info {}
.ap-total-label { color: rgba(255,255,255,0.9); font-size: 14px; margin-bottom: 4px; }
.ap-total-value { color: #fff; font-size: 36px; font-weight: 700; font-family: 'SF Mono', Monaco, monospace; }
.ap-total-stats {
    display: flex;
    gap: 32px;
}
.ap-total-stat {
    text-align: center;
    padding-left: 32px;
    border-left: 1px solid rgba(255,255,255,0.2);
}
.ap-total-stat:first-child { border-left: none; padding-left: 0; }
.ap-total-stat-value { color: #fff; font-size: 24px; font-weight: 700; }
.ap-total-stat-label { color: rgba(255,255,255,0.8); font-size: 12px; margin-top: 4px; }

/* Aging Cards */
.ap-aging-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.ap-aging-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
    animation: slideUp 0.5s ease-out 0.2s both;
}
.ap-aging-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
}
.ap-aging-card.current { border-left: 4px solid #10b981; }
.ap-aging-card.warning { border-left: 4px solid #f59e0b; }
.ap-aging-card.danger { border-left: 4px solid #ef4444; }
.ap-aging-card.critical { border-left: 4px solid #7c3aed; }
.ap-aging-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: transform 0.3s ease;
}
.ap-aging-card.current .ap-aging-icon { background: #d1fae5; color: #059669; }
.ap-aging-card.warning .ap-aging-icon { background: #fef3c7; color: #d97706; }
.ap-aging-card.danger .ap-aging-icon { background: #fee2e2; color: #dc2626; }
.ap-aging-card.critical .ap-aging-icon { background: #ede9fe; color: #7c3aed; }
.ap-aging-info { flex: 1; }
.ap-aging-value { font-size: 20px; font-weight: 700; color: #1e1b4b; font-family: 'SF Mono', Monaco, monospace; }
.ap-aging-label { font-size: 12px; color: #6b7280; margin-top: 4px; }
.ap-aging-percent { 
    font-size: 11px; 
    padding: 2px 8px; 
    border-radius: 10px; 
    background: #f3f4f6; 
    color: #6b7280; 
    margin-top: 6px;
    display: inline-block;
}

/* Cards */
.ap-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    overflow: hidden;
    animation: slideUp 0.5s ease-out 0.3s both;
}
.ap-card-header {
    background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.ap-card-title {
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ap-card-badge {
    background: rgba(255,255,255,0.2);
    color: #fff;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.ap-card-body { padding: 0; }

/* Table */
.ap-table {
    width: 100%;
    border-collapse: collapse;
}
.ap-table th {
    background: #fffbeb;
    padding: 14px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #fef3c7;
}
.ap-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #fef3c7;
    font-size: 13px;
    color: #374151;
}
.ap-table tbody tr {
    transition: all 0.2s ease;
}
.ap-table tbody tr:hover {
    background: #fffbeb;
}
.ap-vendor-name {
    font-weight: 600;
    color: #1e1b4b;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ap-vendor-name i { color: #d97706; }
.ap-vendor-code { font-size: 11px; color: #9ca3af; margin-top: 2px; }
.ap-amount { font-family: 'SF Mono', Monaco, monospace; font-weight: 500; }
.ap-amount.negative { color: #dc2626; }
.ap-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    text-decoration: none;
}
.ap-action-btn.view {
    background: #fef3c7;
    color: #d97706;
}
.ap-action-btn.view:hover {
    background: #d97706;
    color: #fff;
}

/* Grid Layout */
.ap-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
}

/* Sidebar Cards */
.ap-sidebar-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 20px;
    animation: slideUp 0.5s ease-out 0.4s both;
}
.ap-sidebar-header {
    background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
    padding: 14px 20px;
}
.ap-sidebar-header h3 {
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ap-sidebar-body { padding: 16px 20px; }

/* Payment Item */
.ap-payment-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 10px;
    background: #fffbeb;
    transition: all 0.2s ease;
}
.ap-payment-item:hover { background: #fef3c7; }
.ap-payment-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #dc2626;
    font-size: 16px;
    flex-shrink: 0;
}
.ap-payment-icon.paid {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #059669;
}
.ap-payment-info { flex: 1; min-width: 0; }
.ap-payment-vendor { font-weight: 600; color: #1e1b4b; font-size: 13px; }
.ap-payment-details { font-size: 11px; color: #6b7280; margin-top: 2px; }
.ap-payment-amount { 
    font-weight: 700; 
    color: #dc2626; 
    font-family: 'SF Mono', Monaco, monospace; 
    font-size: 14px;
}
.ap-payment-amount.paid { color: #059669; }

/* Empty State */
.ap-empty {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}
.ap-empty i { font-size: 40px; margin-bottom: 12px; opacity: 0.5; }
.ap-empty p { margin: 0; }

/* Animations */
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* DataTables Custom Styling */
.ap-dt-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    flex-wrap: wrap;
    gap: 12px;
}
.ap-dt-header .dataTables_length select {
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
}
.ap-dt-header .dataTables_filter input {
    padding: 10px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    min-width: 250px;
}
.ap-dt-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    border-top: 1px solid #fef3c7;
}
.ap-dt-footer .dataTables_paginate .paginate_button {
    padding: 8px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin: 0 2px;
}
.ap-dt-footer .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
    border-color: #d97706;
    color: #fff !important;
}

@media (max-width: 1200px) {
    .ap-grid { grid-template-columns: 1fr; }
    .ap-aging-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .ap-aging-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')
<section class="content ap-page">
    <!-- Header Banner -->
    <div class="ap-header-banner">
        <div>
            <h1><i class="fas fa-file-invoice-dollar"></i> Accounts Payable</h1>
            <p class="subtitle">Vendor balances & aging analysis following US GAAP/IFRS standards</p>
        </div>
        <a href="{{ route('bookkeeping.dashboard') }}" class="ap-btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>

    <!-- Total AP Banner -->
    <div class="ap-total-banner">
        <div class="ap-total-main">
            <div class="ap-total-icon"><i class="fas fa-money-check-alt"></i></div>
            <div class="ap-total-info">
                <div class="ap-total-label">Total Accounts Payable (Liability)</div>
                <div class="ap-total-value">${{ number_format($summary['total_ap'] ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="ap-total-stats">
            <div class="ap-total-stat">
                <div class="ap-total-stat-value">{{ $summary['total_vendors'] ?? 0 }}</div>
                <div class="ap-total-stat-label">Vendors</div>
            </div>
            <div class="ap-total-stat">
                <div class="ap-total-stat-value">{{ $apAccount ? $apAccount->account_code : '0' }}</div>
                <div class="ap-total-stat-label">GL Account</div>
            </div>
        </div>
    </div>

    <!-- Aging Cards -->
    <div class="ap-aging-grid">
        <div class="ap-aging-card current">
            <div class="ap-aging-icon"><i class="fas fa-check-circle"></i></div>
            <div class="ap-aging-info">
                <div class="ap-aging-value">${{ number_format($summary['current_0_30'] ?? 0, 2) }}</div>
                <div class="ap-aging-label">Current (0-30 Days)</div>
                @if(($summary['total_ap'] ?? 0) > 0)
                <span class="ap-aging-percent">{{ number_format((($summary['current_0_30'] ?? 0) / $summary['total_ap']) * 100, 1) }}%</span>
                @endif
            </div>
        </div>
        <div class="ap-aging-card warning">
            <div class="ap-aging-icon"><i class="fas fa-clock"></i></div>
            <div class="ap-aging-info">
                <div class="ap-aging-value">${{ number_format($summary['days_31_60'] ?? 0, 2) }}</div>
                <div class="ap-aging-label">31-60 Days</div>
                @if(($summary['total_ap'] ?? 0) > 0)
                <span class="ap-aging-percent">{{ number_format((($summary['days_31_60'] ?? 0) / $summary['total_ap']) * 100, 1) }}%</span>
                @endif
            </div>
        </div>
        <div class="ap-aging-card danger">
            <div class="ap-aging-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="ap-aging-info">
                <div class="ap-aging-value">${{ number_format($summary['days_61_90'] ?? 0, 2) }}</div>
                <div class="ap-aging-label">61-90 Days</div>
                @if(($summary['total_ap'] ?? 0) > 0)
                <span class="ap-aging-percent">{{ number_format((($summary['days_61_90'] ?? 0) / $summary['total_ap']) * 100, 1) }}%</span>
                @endif
            </div>
        </div>
        <div class="ap-aging-card critical">
            <div class="ap-aging-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="ap-aging-info">
                <div class="ap-aging-value">${{ number_format($summary['over_90'] ?? 0, 2) }}</div>
                <div class="ap-aging-label">Over 90 Days</div>
                @if(($summary['total_ap'] ?? 0) > 0)
                <span class="ap-aging-percent">{{ number_format((($summary['over_90'] ?? 0) / $summary['total_ap']) * 100, 1) }}%</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Aging Progress Bar -->
    @if(($summary['total_ap'] ?? 0) > 0)
    <div class="ap-card" style="margin-bottom: 24px; padding: 20px 24px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-size: 13px; font-weight: 600; color: #374151;">Aging Distribution</span>
            <span style="font-size: 12px; color: #6b7280;">Total: ${{ number_format($summary['total_ap'], 2) }}</span>
        </div>
        <div style="height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden; display: flex;">
            <div style="height: 100%; background: #10b981; width: {{ (($summary['current_0_30'] ?? 0) / $summary['total_ap']) * 100 }}%;"></div>
            <div style="height: 100%; background: #f59e0b; width: {{ (($summary['days_31_60'] ?? 0) / $summary['total_ap']) * 100 }}%;"></div>
            <div style="height: 100%; background: #ef4444; width: {{ (($summary['days_61_90'] ?? 0) / $summary['total_ap']) * 100 }}%;"></div>
            <div style="height: 100%; background: #7c3aed; width: {{ (($summary['over_90'] ?? 0) / $summary['total_ap']) * 100 }}%;"></div>
        </div>
        <div style="display: flex; gap: 20px; margin-top: 12px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #6b7280;">
                <span style="width: 10px; height: 10px; border-radius: 2px; background: #10b981;"></span> Current
            </div>
            <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #6b7280;">
                <span style="width: 10px; height: 10px; border-radius: 2px; background: #f59e0b;"></span> 31-60 Days
            </div>
            <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #6b7280;">
                <span style="width: 10px; height: 10px; border-radius: 2px; background: #ef4444;"></span> 61-90 Days
            </div>
            <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #6b7280;">
                <span style="width: 10px; height: 10px; border-radius: 2px; background: #7c3aed;"></span> 90+ Days
            </div>
        </div>
    </div>
    @endif

    <!-- Vendor Balances Table -->
    <div class="ap-card" style="margin-bottom: 24px;">
        <div class="ap-card-header">
            <h3 class="ap-card-title"><i class="fas fa-truck"></i> Vendor Balances</h3>
            <span class="ap-card-badge">{{ $vendors->count() }} Vendors with Outstanding Balance</span>
        </div>
        <div class="ap-card-body" style="padding: 16px;">
            @if($vendors->count() > 0)
            <table class="ap-table" id="ap-vendors-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th style="text-align: right;">Total Purchased</th>
                        <th style="text-align: right;">Total Paid</th>
                        <th style="text-align: right;">Purchase Returns</th>
                        <th style="text-align: right;">Balance Due</th>
                        <th style="text-align: center;">Pay Terms</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendors as $vendor)
                    <tr>
                        <td>
                            <div class="ap-vendor-name"><i class="fas fa-building"></i> {{ $vendor->display_name }}</div>
                            @if($vendor->vendor_code)<div class="ap-vendor-code">{{ $vendor->vendor_code }}</div>@endif
                        </td>
                        <td style="text-align: right;" data-order="{{ $vendor->total_purchase }}">
                            <span class="ap-amount">${{ number_format($vendor->total_purchase, 2) }}</span>
                        </td>
                        <td style="text-align: right;" data-order="{{ $vendor->purchase_paid }}">
                            <span class="ap-amount" style="color: #059669;">${{ number_format($vendor->purchase_paid, 2) }}</span>
                        </td>
                        <td style="text-align: right;" data-order="{{ $vendor->total_purchase_return }}">
                            <span class="ap-amount">${{ number_format($vendor->total_purchase_return - $vendor->purchase_return_paid, 2) }}</span>
                        </td>
                        <td style="text-align: right;" data-order="{{ $vendor->balance_due }}">
                            <span class="ap-amount negative" style="font-weight: 700; font-size: 15px;">
                                ${{ number_format($vendor->balance_due, 2) }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span style="background: #f3f4f6; padding: 4px 10px; border-radius: 6px; font-size: 11px; color: #6b7280;">
                                {{ $vendor->pay_terms }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ action([\App\Http\Controllers\ContactController::class, 'show'], [$vendor->id]) }}?type=supplier" class="ap-action-btn view" title="View Vendor"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); font-weight: 700;">
                        <td><strong>TOTAL</strong></td>
                        <td style="text-align: right;"><strong>${{ number_format($vendors->sum('total_purchase'), 2) }}</strong></td>
                        <td style="text-align: right;"><strong>${{ number_format($vendors->sum('purchase_paid'), 2) }}</strong></td>
                        <td style="text-align: right;"><strong>${{ number_format($vendors->sum('total_purchase_return') - $vendors->sum('purchase_return_paid'), 2) }}</strong></td>
                        <td style="text-align: right; color: #dc2626;"><strong>${{ number_format($summary['total_ap'], 2) }}</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            @else
            <div class="ap-empty">
                <i class="fas fa-check-circle" style="color: #10b981;"></i>
                <p>No outstanding payables! All vendor bills are paid.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Main Grid: Journal Entries & Sidebar -->
    <div class="ap-grid">
        <!-- AP Journal Entries -->
        <div class="ap-card">
            <div class="ap-card-header">
                <h3 class="ap-card-title" style="color: #fff;"><i class="fas fa-book" style="color: #fbbf24;"></i> AP Journal Entries (Double-Entry Ledger)</h3>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <span style="color: #fbbf24; font-size: 12px;">
                        <i class="fas fa-arrow-up"></i> Debits: ${{ number_format($apJournalSummary['total_debits'] ?? 0, 2) }}
                    </span>
                    <span style="color: #fbbf24; font-size: 12px;">
                        <i class="fas fa-arrow-down"></i> Credits: ${{ number_format($apJournalSummary['total_credits'] ?? 0, 2) }}
                    </span>
                    <button type="button" id="sync-ap-btn" class="ap-btn-back" style="padding: 8px 16px; font-size: 12px; background: rgba(16, 185, 129, 0.2); border-color: rgba(16, 185, 129, 0.4);">
                        <i class="fas fa-sync-alt"></i> Sync Entries
                    </button>
                    <a href="{{ route('bookkeeping.journal.index') }}" class="ap-btn-back" style="padding: 8px 16px; font-size: 12px;">View All <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="ap-card-body" style="padding: 0;">
                @if(isset($apJournalEntries) && $apJournalEntries->count() > 0)
                <table class="ap-table" id="ap-journal-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Entry #</th>
                            <th>Date</th>
                            <th>Description / Memo</th>
                            <th>Vendor</th>
                            <th style="text-align: right;">Debit (↓ AP)</th>
                            <th style="text-align: right;">Credit (↑ AP)</th>
                            <th style="text-align: center;">Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($apJournalEntries as $entry)
                        <tr>
                            <td>
                                <a href="{{ route('bookkeeping.journal.show', $entry->id) }}" style="color: #d97706; font-weight: 600; text-decoration: none;">
                                    {{ $entry->entry_number }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($entry->entry_date)->format('M d, Y') }}</td>
                            <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $entry->memo ?? 'No description' }}
                            </td>
                            <td>{{ $entry->contact_name }}</td>
                            <td style="text-align: right;">
                                @if($entry->ap_debit > 0)
                                <span style="color: #059669; font-weight: 600; font-family: 'SF Mono', Monaco, monospace;">
                                    ${{ number_format($entry->ap_debit, 2) }}
                                </span>
                                @else
                                <span style="color: #d1d5db;">-</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if($entry->ap_credit > 0)
                                <span style="color: #dc2626; font-weight: 600; font-family: 'SF Mono', Monaco, monospace;">
                                    ${{ number_format($entry->ap_credit, 2) }}
                                </span>
                                @else
                                <span style="color: #d1d5db;">-</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($entry->source_type)
                                <span style="background: #fef3c7; color: #d97706; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;">
                                    {{ ucfirst(str_replace('_', ' ', $entry->source_type)) }}
                                </span>
                                @else
                                <span style="color: #9ca3af;">Manual</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="ap-empty" style="padding: 60px 20px;">
                    <i class="fas fa-book-open" style="color: #fbbf24; font-size: 48px;"></i>
                    <p style="margin-top: 16px; font-size: 15px; color: #6b7280;">No AP journal entries found.</p>
                    <p style="font-size: 13px; color: #9ca3af; margin-top: 8px;">
                        Click "Sync Entries" to create journal entries for your purchase transactions.
                    </p>
                    <div style="margin-top: 20px; padding: 16px; background: #fef3c7; border-radius: 10px; max-width: 500px; margin-left: auto; margin-right: auto;">
                        <p style="color: #92400e; font-size: 12px; margin: 0;">
                            <i class="fas fa-info-circle"></i> <strong>Double-Entry:</strong> 
                            Purchase → Debit Inventory, Credit AP<br>
                            Payment → Debit AP, Credit Cash
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Recent Payments Made -->
            <div class="ap-sidebar-card">
                <div class="ap-sidebar-header" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <h3><i class="fas fa-paper-plane"></i> Recent Payments Made</h3>
                </div>
                <div class="ap-sidebar-body">
                    @forelse($recentPayments as $payment)
                    <div class="ap-payment-item">
                        <div class="ap-payment-icon paid"><i class="fas fa-check"></i></div>
                        <div class="ap-payment-info">
                            <div class="ap-payment-vendor">{{ $payment->display_name }}</div>
                            <div class="ap-payment-details">
                                {{ $payment->purchase_ref }} &bull; {{ \Carbon\Carbon::parse($payment->paid_on)->format('M d') }} &bull; {{ ucfirst($payment->method) }}
                            </div>
                        </div>
                        <div class="ap-payment-amount paid">${{ number_format($payment->amount, 2) }}</div>
                    </div>
                    @empty
                    <div class="ap-empty" style="padding: 30px;">
                        <i class="fas fa-inbox"></i>
                        <p>No recent payments</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Top Outstanding Bills -->
            <div class="ap-sidebar-card">
                <div class="ap-sidebar-header" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
                    <h3><i class="fas fa-file-invoice"></i> Outstanding Bills</h3>
                </div>
                <div class="ap-sidebar-body">
                    @forelse($topOutstandingBills as $bill)
                    <div class="ap-payment-item" style="background: {{ $bill->days_outstanding > 60 ? '#fee2e2' : '#fffbeb' }};">
                        <div class="ap-payment-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="ap-payment-info">
                            <div class="ap-payment-vendor">{{ $bill->ref_no }}</div>
                            <div class="ap-payment-details">{{ $bill->display_name }} &bull; {{ $bill->days_outstanding }} days</div>
                        </div>
                        <div class="ap-payment-amount">${{ number_format($bill->balance, 2) }}</div>
                    </div>
                    @empty
                    <div class="ap-empty" style="padding: 30px;">
                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                        <p>All bills paid!</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- GL Account Info -->
            @if($apAccount)
            <div class="ap-sidebar-card">
                <div class="ap-sidebar-header" style="background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);">
                    <h3><i class="fas fa-landmark"></i> GL Account (Liability)</h3>
                </div>
                <div class="ap-sidebar-body">
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #6b7280; font-size: 13px;">Account Code</span>
                        <span style="font-weight: 600; color: #1e1b4b;">{{ $apAccount->account_code }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #6b7280; font-size: 13px;">Account Name</span>
                        <span style="font-weight: 600; color: #1e1b4b;">{{ $apAccount->name }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #6b7280; font-size: 13px;">Account Type</span>
                        <span style="font-weight: 600; color: #dc2626;">Liability</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                        <span style="color: #6b7280; font-size: 13px;">Balance</span>
                        <span style="font-weight: 700; color: #dc2626; font-family: 'SF Mono', Monaco, monospace; font-size: 16px;">
                            ${{ number_format($apAccount->current_balance ?? 0, 2) }}
                        </span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize DataTables for Vendor Balances
    if ($('#ap-vendors-table').length) {
        $('#ap-vendors-table').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[4, 'desc']], // Sort by Balance Due descending
            columnDefs: [
                { orderable: false, targets: [6] } // Disable sorting on Actions column
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search vendors...",
                lengthMenu: "Show _MENU_ vendors",
                info: "Showing _START_ to _END_ of _TOTAL_ vendors",
                infoEmpty: "No vendors found",
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>',
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>'
                }
            },
            dom: '<"ap-dt-header"lf>rt<"ap-dt-footer"ip>'
        });
    }

    // Initialize DataTables for Journal Entries
    if ($('#ap-journal-table').length) {
        $('#ap-journal-table').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            order: [[1, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search entries...",
                lengthMenu: "Show _MENU_",
                paginate: {
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>'
                }
            },
            dom: '<"ap-dt-header"lf>rt<"ap-dt-footer"ip>'
        });
    }

    // Animate numbers on load
    $('.ap-total-value, .ap-aging-value').each(function() {
        var $this = $(this);
        var text = $this.text();
        var number = parseFloat(text.replace(/[$,]/g, ''));
        
        if (!isNaN(number) && number > 0) {
            $({ count: 0 }).animate({ count: number }, {
                duration: 1000,
                easing: 'swing',
                step: function() {
                    $this.text('$' + this.count.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                },
                complete: function() {
                    $this.text('$' + number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                }
            });
        }
    });

    // Hover effects for aging cards
    $('.ap-aging-card').hover(
        function() {
            $(this).find('.ap-aging-icon').css('transform', 'rotate(-10deg) scale(1.1)');
        },
        function() {
            $(this).find('.ap-aging-icon').css('transform', '');
        }
    );

    // Sync AP Journal Entries
    $('#sync-ap-btn').on('click', function() {
        var $btn = $(this);
        var originalHtml = $btn.html();
        
        swal({
            title: 'Sync AP Journal Entries?',
            text: 'This will create missing journal entries for Purchase Receipts (Debit Inventory, Credit AP) and Vendor Payments (Debit AP, Credit Cash). This follows US GAAP/IFRS double-entry principles.',
            icon: 'info',
            buttons: {
                cancel: {
                    text: 'Cancel',
                    value: null,
                    visible: true,
                    closeModal: true,
                },
                confirm: {
                    text: 'Start Sync',
                    value: true,
                    visible: true,
                    closeModal: true,
                }
            }
        }).then((willSync) => {
            if (willSync) {
                $btn.html('<i class="fas fa-spinner fa-spin"></i> Syncing...').prop('disabled', true);
                
                $.ajax({
                    url: '{{ route("bookkeeping.accounts-payable.sync") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $btn.html(originalHtml).prop('disabled', false);
                        
                        if (response.success) {
                            swal({
                                title: 'Sync Completed!',
                                text: 'Created ' + response.synced + ' journal entries.' +
                                      (response.errors && response.errors.length > 0 ? 
                                       ' (' + response.errors.length + ' errors occurred)' : ''),
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            swal({
                                title: 'Sync Failed',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.html(originalHtml).prop('disabled', false);
                        swal({
                            title: 'Error',
                            text: 'Failed to sync entries. Please try again.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection
