@extends('layouts.app')
@section('title', 'Bookkeeping Dashboard')

@section('css')
<style>
/* Bookkeeping Dashboard - Professional Purple Theme */
.bk-page {
    background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
    min-height: 100vh;
    padding: 40px 50px 40px;
}

/* Header Banner */
.bk-header-banner {
    background: linear-gradient(135deg, #37475a 0%, #37475a  50%, #37475a  100%);
    border-radius: 20px;
    padding: 40px 32px;
    /* small gap so it doesn't stick to the app header */
    margin-top: 12px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 8px 30px rgba(124, 58, 237, 0.25);
    position: relative;
    overflow: hidden;
}

.bk-header-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.bk-header-content {
    position: relative;
    z-index: 2;
}

.bk-header-banner h1 {
    font-size: 26px;
    font-weight: 700;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #fff !important;
}

.bk-header-banner h1 i {
    font-size: 26px;
    color: #ecfdf5 !important;
}

.bk-header-banner .subtitle {
    font-size: 13px;
    color: rgba(236, 253, 245, 0.95) !important;
    margin: 0;
}

/* Net Worth Banner */
.bk-networth-banner {
    background: linear-gradient(135deg, #37475a 0%, #37475a  100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 8px 30px rgba(5, 150, 105, 0.25);
}

.bk-networth-main {
    display: flex;
    align-items: center;
    gap: 20px;
}

.bk-networth-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #fff;
}

.bk-networth-info {
    color: #fff;
}

.bk-networth-label {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 4px;
}

.bk-networth-value {
    font-size: 36px;
    font-weight: 700;
    font-family: 'SF Mono', Monaco, monospace;
}

.bk-networth-stats {
    display: flex;
    gap: 32px;
}

.bk-networth-stat {
    text-align: center;
    color: #fff;
    padding: 0 20px;
    border-left: 1px solid rgba(255,255,255,0.2);
}

.bk-networth-stat:first-child {
    border-left: none;
}

.bk-networth-stat-value {
    font-size: 20px;
    font-weight: 700;
    font-family: 'SF Mono', Monaco, monospace;
}

.bk-networth-stat-label {
    font-size: 12px;
    opacity: 0.9;
    margin-top: 4px;
}

/* Stats Cards */
.bk-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}

@media (max-width: 1200px) {
    .bk-stats { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 576px) {
    .bk-stats { grid-template-columns: 1fr; }
}

.bk-stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 20px 24px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    border-left: 4px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.3s ease;
}

.bk-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(139, 92, 246, 0.12);
}

.bk-stat-card.assets { border-left-color: #10b981; }
.bk-stat-card.liabilities { border-left-color: #ef4444; }
.bk-stat-card.equity { border-left-color: #3b82f6; }
.bk-stat-card.revenue { border-left-color: #8b5cf6; }

.bk-stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}

.bk-stat-card.assets .bk-stat-icon { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669; }
.bk-stat-card.liabilities .bk-stat-icon { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }
.bk-stat-card.equity .bk-stat-icon { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2563eb; }
.bk-stat-card.revenue .bk-stat-icon { background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); color: #7c3aed; }

.bk-stat-info {
    flex: 1;
    min-width: 0;
}

.bk-stat-value {
    font-size: 24px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 4px;
    font-family: 'SF Mono', Monaco, monospace;
}

.bk-stat-card.assets .bk-stat-value { color: #059669; }
.bk-stat-card.liabilities .bk-stat-value { color: #dc2626; }
.bk-stat-card.equity .bk-stat-value { color: #2563eb; }
.bk-stat-card.revenue .bk-stat-value { color: #7c3aed; }

.bk-stat-label {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

/* Quick Actions */
.bk-quick-actions {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

@media (max-width: 1400px) {
    .bk-quick-actions { grid-template-columns: repeat(4, 1fr) !important; }
}

@media (max-width: 992px) {
    .bk-quick-actions { grid-template-columns: repeat(3, 1fr) !important; }
}

@media (max-width: 768px) {
    .bk-quick-actions { grid-template-columns: repeat(2, 1fr) !important; }
}

.bk-quick-action {
    background: #fff;
    border-radius: 14px;
    padding: 20px;
    text-align: center;
    text-decoration: none;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.bk-quick-action:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(139, 92, 246, 0.15);
    text-decoration: none;
    border-color: #c4b5fd;
}

.bk-quick-action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    color: #7c3aed;
}

.bk-quick-action-label {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    text-align: center;
}

/* Section Cards */
.bk-section {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    margin-bottom: 24px;
    overflow: hidden;
}

.bk-section-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.bk-section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 16px;
    font-weight: 600;
    color: #1e1b4b;
    margin: 0;
}

.bk-section-title i {
    color: #8b5cf6;
    font-size: 18px;
}

.bk-section-action {
    color: #7c3aed;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
}

.bk-section-action:hover {
    color: #6d28d9;
    text-decoration: underline;
}

.bk-section-body {
    padding: 24px;
}

/* Cash/Bank List */
.bk-account-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.bk-account-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: #f8f9fe;
    border-radius: 12px;
    transition: all 0.2s ease;
}

.bk-account-item:hover {
    background: #f5f3ff;
}

.bk-account-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7c3aed;
    font-size: 18px;
    flex-shrink: 0;
}

.bk-account-info {
    flex: 1;
    min-width: 0;
}

.bk-account-name {
    font-weight: 600;
    color: #1e1b4b;
    margin-bottom: 2px;
    font-size: 14px;
}

.bk-account-type {
    font-size: 12px;
    color: #6b7280;
}

.bk-account-balance {
    font-size: 18px;
    font-weight: 700;
    font-family: 'SF Mono', Monaco, monospace;
    color: #059669;
    white-space: nowrap;
}

/* Recent Entries Table */
.bk-table {
    width: 100%;
    border-collapse: collapse;
}

.bk-table thead th {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 16px;
    text-align: left;
}

.bk-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid #f5f5f5;
    font-size: 14px;
    color: #374151;
}

.bk-table tbody tr:hover {
    background: #faf5ff;
}

.bk-table tbody tr:last-child td {
    border-bottom: none;
}

/* Empty State */
.bk-empty {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}

.bk-empty i {
    font-size: 40px;
    margin-bottom: 12px;
    opacity: 0.5;
}

.bk-empty p {
    margin: 0;
    font-size: 14px;
}

/* Grid Layout */
.bk-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

@media (max-width: 1200px) {
    .bk-grid { grid-template-columns: 1fr 1fr; }
}

@media (max-width: 768px) {
    .bk-grid { grid-template-columns: 1fr; }
}

/* Animation */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

.bk-stat-card, .bk-quick-action, .bk-section {
    animation: fadeInUp 0.4s ease forwards;
}

/* Responsive */
@media (max-width: 768px) {
    .bk-page {
        padding: 15px;
    }
    
    .bk-header-banner {
        flex-direction: column;
        text-align: center;
        padding: 24px;
    }
    
    .bk-networth-banner {
        flex-direction: column;
        text-align: center;
    }
    
    .bk-networth-stats {
        flex-direction: column;
        gap: 16px;
    }
    
    .bk-networth-stat {
        border-left: none;
        border-top: 1px solid rgba(255,255,255,0.2);
        padding: 16px 0 0;
    }
    
    .bk-networth-stat:first-child {
        border-top: none;
        padding-top: 0;
    }
}
</style>
@endsection

@section('content')
<section class="content bk-page">
    
    <!-- Header Banner -->
    <div class="bk-header-banner">
        <div class="bk-header-content">
            <h1><i class="fas fa-calculator"></i> Bookkeeping Dashboard</h1>
            <p class="subtitle">Financial overview and quick actions</p>
        </div>
    </div>

    <!-- Net Worth Banner -->
    <div class="bk-networth-banner">
        <div class="bk-networth-main">
            <div class="bk-networth-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="bk-networth-info">
                <div class="bk-networth-label">Net Worth (Assets - Liabilities)</div>
                <div class="bk-networth-value">${{ number_format(($summary['total_assets'] ?? 0) - ($summary['total_liabilities'] ?? 0), 2) }}</div>
            </div>
        </div>
        <div class="bk-networth-stats">
            <div class="bk-networth-stat">
                <div class="bk-networth-stat-value">${{ number_format($summary['total_assets'] ?? 0, 2) }}</div>
                <div class="bk-networth-stat-label">Total Assets</div>
            </div>
            <div class="bk-networth-stat">
                <div class="bk-networth-stat-value">${{ number_format($summary['total_liabilities'] ?? 0, 2) }}</div>
                <div class="bk-networth-stat-label">Total Liabilities</div>
            </div>
            <div class="bk-networth-stat">
                <div class="bk-networth-stat-value">${{ number_format($summary['total_equity'] ?? 0, 2) }}</div>
                <div class="bk-networth-stat-label">Total Equity</div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="bk-stats">
        <div class="bk-stat-card assets">
            <div class="bk-stat-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="bk-stat-info">
                <div class="bk-stat-value">${{ number_format($summary['total_assets'] ?? 0, 2) }}</div>
                <div class="bk-stat-label">Total Assets</div>
            </div>
        </div>
        <div class="bk-stat-card liabilities">
            <div class="bk-stat-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="bk-stat-info">
                <div class="bk-stat-value">${{ number_format($summary['total_liabilities'] ?? 0, 2) }}</div>
                <div class="bk-stat-label">Total Liabilities</div>
            </div>
        </div>
        <div class="bk-stat-card equity">
            <div class="bk-stat-icon">
                <i class="fas fa-landmark"></i>
            </div>
            <div class="bk-stat-info">
                <div class="bk-stat-value">${{ number_format($summary['total_equity'] ?? 0, 2) }}</div>
                <div class="bk-stat-label">Total Equity</div>
            </div>
        </div>
        <div class="bk-stat-card revenue">
            <div class="bk-stat-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="bk-stat-info">
                <div class="bk-stat-value">${{ number_format($summary['total_income'] ?? 0, 2) }}</div>
                <div class="bk-stat-label">Total Revenue</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bk-quick-actions">
        <a href="{{ route('bookkeeping.pl.income.create') }}" class="bk-quick-action" style="border-left: 3px solid #10b981;">
            <div class="bk-quick-action-icon" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669;"><i class="fas fa-plus"></i></div>
            <div class="bk-quick-action-label">Add Income</div>
        </a>
        <a href="{{ route('bookkeeping.pl.expense.create') }}" class="bk-quick-action" style="border-left: 3px solid #ef4444;">
            <div class="bk-quick-action-icon" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626;"><i class="fas fa-minus"></i></div>
            <div class="bk-quick-action-label">Add Expense</div>
        </a>
        <a href="{{ route('bookkeeping.deposits.create') }}" class="bk-quick-action">
            <div class="bk-quick-action-icon"><i class="fas fa-piggy-bank"></i></div>
            <div class="bk-quick-action-label">Record Deposit</div>
        </a>
        <a href="{{ route('bookkeeping.journal.create') }}" class="bk-quick-action">
            <div class="bk-quick-action-icon"><i class="fas fa-book"></i></div>
            <div class="bk-quick-action-label">Journal Entry</div>
        </a>
        <a href="{{ route('bookkeeping.liabilities.create') }}" class="bk-quick-action">
            <div class="bk-quick-action-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="bk-quick-action-label">Add Liability</div>
        </a>
        <a href="{{ route('bookkeeping.accounts.create') }}" class="bk-quick-action">
            <div class="bk-quick-action-icon"><i class="fas fa-folder-plus"></i></div>
            <div class="bk-quick-action-label">New Account</div>
        </a>
        <a href="{{ route('bookkeeping.pl.index') }}" class="bk-quick-action" style="border-left: 3px solid #3b82f6;">
            <div class="bk-quick-action-icon" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2563eb;"><i class="fas fa-exchange-alt"></i></div>
            <div class="bk-quick-action-label">P&L Transactions</div>
        </a>
        <a href="{{ route('bookkeeping.reports.income-statement') }}" class="bk-quick-action">
            <div class="bk-quick-action-icon"><i class="fas fa-chart-line"></i></div>
            <div class="bk-quick-action-label">P&L Report</div>
        </a>
    </div>

    <!-- Content Grid -->
    <div class="bk-grid">
        <!-- Cash & Bank Accounts -->
        <div class="bk-section">
            <div class="bk-section-header">
                <h3 class="bk-section-title"><i class="fas fa-university"></i> Cash & Bank</h3>
                <a href="{{ route('bookkeeping.accounts.index') }}" class="bk-section-action">View All</a>
            </div>
            <div class="bk-section-body">
                <div class="bk-account-list">
                    @forelse($cashBalances ?? [] as $account)
                    <div class="bk-account-item">
                        <div class="bk-account-icon">
                            <i class="fas fa-{{ in_array($account->detail_type, ['checking', 'savings', 'money_market']) ? 'university' : 'money-bill-wave' }}"></i>
                        </div>
                        <div class="bk-account-info">
                            <div class="bk-account-name">{{ $account->name }}</div>
                            <div class="bk-account-type">{{ ucfirst(str_replace('_', ' ', $account->detail_type ?? 'Cash')) }}</div>
                        </div>
                        <div class="bk-account-balance">${{ number_format($account->current_balance ?? 0, 2) }}</div>
                    </div>
                    @empty
                    <div class="bk-empty">
                        <i class="fas fa-university"></i>
                        <p>No cash or bank accounts found</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tracked Liabilities -->
        <div class="bk-section">
            <div class="bk-section-header">
                <h3 class="bk-section-title"><i class="fas fa-file-invoice"></i> Tracked Liabilities</h3>
                <a href="{{ route('bookkeeping.liabilities.index') }}" class="bk-section-action">View All</a>
            </div>
            <div class="bk-section-body">
                <!-- Liabilities Summary -->
                <div style="display: flex; gap: 16px; margin-bottom: 16px; padding: 16px; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-radius: 12px; border: 1px solid #fecaca;">
                    <div style="flex: 1; text-align: center; padding: 8px; border-right: 1px solid #fecaca;">
                        <div style="font-size: 24px; font-weight: 700; color: #dc2626; font-family: 'SF Mono', Monaco, monospace;">${{ number_format($totalTrackedLiabilities ?? 0, 2) }}</div>
                        <div style="font-size: 11px; color: #991b1b; text-transform: uppercase; font-weight: 600;">Total Outstanding</div>
                    </div>
                    <div style="flex: 1; text-align: center; padding: 8px;">
                        <div style="font-size: 24px; font-weight: 700; color: #dc2626; font-family: 'SF Mono', Monaco, monospace;">{{ $trackedLiabilitiesCount ?? 0 }}</div>
                        <div style="font-size: 11px; color: #991b1b; text-transform: uppercase; font-weight: 600;">Active Items</div>
                    </div>
                </div>
                
                <div class="bk-account-list">
                    @forelse($activeLiabilities ?? [] as $liability)
                    <div class="bk-account-item">
                        <div class="bk-account-icon" style="background: linear-gradient(135deg, {{ $liability->isOverdue() ? '#fee2e2' : '#fef3c7' }} 0%, {{ $liability->isOverdue() ? '#fecaca' : '#fde68a' }} 100%); color: {{ $liability->isOverdue() ? '#dc2626' : '#d97706' }};">
                            <i class="fas fa-{{ $liability->isOverdue() ? 'exclamation-circle' : 'file-invoice' }}"></i>
                        </div>
                        <div class="bk-account-info">
                            <div class="bk-account-name">{{ $liability->name }}</div>
                            <div class="bk-account-type">
                                @if($liability->contact)
                                    {{ $liability->contact->name }} • 
                                @endif
                                Due: {{ $liability->due_date ? \Carbon\Carbon::parse($liability->due_date)->format('M d, Y') : 'No due date' }}
                                @if($liability->isOverdue())
                                    <span style="color: #dc2626; font-weight: 600;"> (Overdue)</span>
                                @endif
                            </div>
                        </div>
                        <div class="bk-account-balance" style="color: {{ $liability->isOverdue() ? '#dc2626' : '#d97706' }};">${{ number_format($liability->current_balance ?? 0, 2) }}</div>
                    </div>
                    @empty
                    <div class="bk-empty">
                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                        <p>No active liabilities</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Inventory Valuation -->
        <div class="bk-section">
            <div class="bk-section-header">
                <h3 class="bk-section-title"><i class="fas fa-boxes"></i> Inventory</h3>
                <a href="{{ route('bookkeeping.inventory.index') }}" class="bk-section-action">View Details</a>
            </div>
            <div class="bk-section-body">
                @if($latestValuation ?? null)
                <div class="bk-account-list">
                    <div class="bk-account-item">
                        <div class="bk-account-icon" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2563eb;">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="bk-account-info">
                            <div class="bk-account-name">Cost Value</div>
                            <div class="bk-account-type">Total inventory at cost</div>
                        </div>
                        <div class="bk-account-balance" style="color: #2563eb;">${{ number_format($latestValuation->total_cost_value ?? 0, 2) }}</div>
                    </div>
                    <div class="bk-account-item">
                        <div class="bk-account-icon" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669;">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="bk-account-info">
                            <div class="bk-account-name">Retail Value</div>
                            <div class="bk-account-type">Total inventory at retail</div>
                        </div>
                        <div class="bk-account-balance">${{ number_format($latestValuation->total_retail_value ?? 0, 2) }}</div>
                    </div>
                </div>
                @else
                <div class="bk-empty">
                    <i class="fas fa-boxes"></i>
                    <p>No inventory valuation data</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Journal Entries -->
    <div class="bk-section">
        <div class="bk-section-header">
            <h3 class="bk-section-title"><i class="fas fa-history"></i> Recent Journal Entries</h3>
            <a href="{{ route('bookkeeping.journal.index') }}" class="bk-section-action">View All</a>
        </div>
        <div class="bk-section-body" style="padding: 0;">
            @if(isset($recentEntries) && $recentEntries->count() > 0)
            <table class="bk-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Entry #</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentEntries->take(5) as $entry)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($entry->entry_date)->format('M d, Y') }}</td>
                        <td><strong>{{ $entry->entry_number }}</strong></td>
                        <td>{{ Str::limit($entry->memo ?? 'No description', 40) }}</td>
                        <td><span style="background: #ede9fe; color: #7c3aed; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ ucfirst($entry->entry_type ?? 'General') }}</span></td>
                        <td style="text-align: right; font-weight: 700; font-family: 'SF Mono', Monaco, monospace; color: #059669;">
                            ${{ number_format($entry->total_debit ?? 0, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="bk-empty" style="padding: 40px;">
                <i class="fas fa-book"></i>
                <p>No journal entries found</p>
            </div>
            @endif
        </div>
    </div>

</section>
@endsection
