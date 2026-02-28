@extends('layouts.app')
@section('title', 'Income Statement')

@section('css')
<style>
    /* ===== Page Container ===== */
    .income-statement-wrapper {
        padding: 0;
        background: #f8f9fe;
        min-height: calc(100vh - 60px);
        width: 100%;
    }

    /* ===== Header Banner ===== */
    .report-header-banner {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
        padding: 20px 32px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .report-header-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .header-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .header-title-area h1 {
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-title-area h1 i {
        background: rgba(255,255,255,0.2);
        padding: 8px;
        border-radius: 8px;
        font-size: 16px;
    }

    .header-subtitle {
        font-size: 13px;
        opacity: 0.85;
    }

    .header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-header {
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-header-secondary {
        background: rgba(255,255,255,0.15);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.25);
    }

    .btn-header-secondary:hover {
        background: rgba(255,255,255,0.25);
        color: #fff;
    }

    .btn-header-primary {
        background: #fff;
        color: #7c3aed;
    }

    .btn-header-primary:hover {
        background: #f5f3ff;
        color: #6d28d9;
    }

    .btn-header-success {
        background: #10b981;
        color: #fff;
    }

    .btn-header-success:hover {
        background: #059669;
        color: #fff;
        text-decoration: none;
    }

    /* ===== Content Area ===== */
    .content-area {
        padding: 24px 32px;
        width: 100%;
    }

    /* ===== Main Layout Grid ===== */
    .report-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .report-left-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .report-right-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* ===== Summary Cards ===== */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: #fff;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #e5e7eb;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
    }

    .summary-card.revenue::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .summary-card.cogs::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .summary-card.expenses::before { background: linear-gradient(90deg, #ef4444, #f87171); }
    .summary-card.net::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

    .summary-value {
        font-size: 22px;
        font-weight: 700;
        color: #1e1b4b;
        margin-bottom: 2px;
        font-family: 'JetBrains Mono', monospace;
    }

    .summary-card.net .summary-value.profit { color: #059669; }
    .summary-card.net .summary-value.loss { color: #dc2626; }

    .summary-label {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
    }

    /* ===== Report Card ===== */
    .report-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        flex: 1;
    }

    .report-card.full-width {
        grid-column: 1 / -1;
    }

    .report-header {
        background: linear-gradient(135deg, #fafbff 0%, #f5f3ff 100%);
        padding: 16px 24px;
        border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .report-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .report-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
    }

    .report-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e1b4b;
        margin: 0;
    }

    .report-period {
        font-size: 12px;
        color: #6b7280;
        margin: 0;
    }

    .report-body {
        padding: 20px 24px;
    }

    /* ===== Section ===== */
    .report-section {
        margin-bottom: 20px;
    }

    .report-section:last-child {
        margin-bottom: 0;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .section-header.revenue { background: #d1fae5; }
    .section-header.cogs { background: #fef3c7; }
    .section-header.expenses { background: #fee2e2; }

    .section-title {
        font-weight: 600;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-header.revenue .section-title { color: #065f46; }
    .section-header.cogs .section-title { color: #92400e; }
    .section-header.expenses .section-title { color: #991b1b; }

    .section-total {
        font-weight: 700;
        font-size: 14px;
        font-family: 'JetBrains Mono', monospace;
    }

    .section-header.revenue .section-total { color: #059669; }
    .section-header.cogs .section-total { color: #d97706; }
    .section-header.expenses .section-total { color: #dc2626; }

    /* ===== Account List ===== */
    .account-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .account-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 14px;
        border-bottom: 1px solid #f1f5f9;
        margin-left: 16px;
    }

    .account-item:last-child {
        border-bottom: none;
    }

    .account-info {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        flex: 1;
    }

    .account-code {
        background: #ede9fe;
        color: #7c3aed;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        font-family: 'JetBrains Mono', monospace;
        flex-shrink: 0;
    }

    .account-name {
        font-size: 13px;
        color: #374151;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .account-balance {
        font-weight: 600;
        font-size: 13px;
        font-family: 'JetBrains Mono', monospace;
        color: #1e1b4b;
        flex-shrink: 0;
    }

    /* ===== Subtotal Row ===== */
    .subtotal-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 16px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 8px;
        margin: 16px 0;
        border: 1px solid #e2e8f0;
    }

    .subtotal-label {
        font-weight: 600;
        font-size: 14px;
        color: #374151;
    }

    .subtotal-value {
        font-weight: 700;
        font-size: 16px;
        color: #1e1b4b;
        font-family: 'JetBrains Mono', monospace;
    }

    .subtotal-value.profit { color: #059669; }
    .subtotal-value.loss { color: #dc2626; }

    /* ===== Net Income Row ===== */
    .net-income-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 20px;
        border-radius: 10px;
        color: #fff;
        margin-top: 20px;
    }

    .net-income-label {
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .net-income-label i {
        font-size: 20px;
    }

    .net-income-value {
        font-size: 24px;
        font-weight: 700;
        font-family: 'JetBrains Mono', monospace;
    }

    .net-income-value.profit { color: #34d399; }
    .net-income-value.loss { color: #f87171; }

    /* ===== Manual Entries Card ===== */
    .manual-entries-card {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-radius: 10px;
        padding: 16px 20px;
        border: 1px solid #93c5fd;
    }

    .manual-entries-header {
        font-size: 14px;
        font-weight: 700;
        color: #1e40af;
        margin: 0 0 14px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .manual-entries-header span {
        font-size: 11px;
        font-weight: normal;
        color: #3b82f6;
    }

    .manual-entries-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .manual-entry-item {
        background: #fff;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
    }

    .manual-entry-label {
        font-size: 10px;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }

    .manual-entry-value {
        font-size: 18px;
        font-weight: 700;
        font-family: 'JetBrains Mono', monospace;
    }

    .manual-entry-value.income { color: #059669; }
    .manual-entry-value.expense { color: #dc2626; }

    .manual-entry-count {
        font-size: 10px;
        color: #6b7280;
    }

    .manual-entries-link {
        margin-top: 12px;
        text-align: center;
    }

    .manual-entries-link a {
        font-size: 12px;
        color: #2563eb;
        text-decoration: none;
    }

    .manual-entries-link a:hover {
        text-decoration: underline;
    }

    /* ===== Animations ===== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .summary-card, .report-card {
        animation: fadeInUp 0.4s ease forwards;
    }

    /* ===== Responsive ===== */
    @media (max-width: 1200px) {
        .report-layout {
            grid-template-columns: 1fr;
        }

        .summary-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 992px) {
        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .report-header-banner {
            padding: 16px;
        }

        .content-area {
            padding: 16px;
        }

        .header-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .header-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .btn-header {
            padding: 8px 14px;
            font-size: 12px;
        }

        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .summary-card {
            padding: 12px 16px;
        }

        .summary-value {
            font-size: 18px;
        }

        .report-header {
            padding: 12px 16px;
            flex-direction: column;
            gap: 8px;
            text-align: center;
        }

        .report-body {
            padding: 16px;
        }

        .manual-entries-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .header-actions {
            flex-direction: column;
        }

        .btn-header {
            width: 100%;
            justify-content: center;
        }

        .account-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }

        .account-balance {
            align-self: flex-end;
        }
    }

    /* ===== Print ===== */
    @media print {
        .btn-header, .header-actions { display: none !important; }
        .report-header-banner { background: #1e1b4b !important; -webkit-print-color-adjust: exact; }
        .content-area { padding: 20px; }
        .report-layout { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="income-statement-wrapper">
    <!-- Header -->
    <div class="report-header-banner">
        <div class="header-content">
            <div class="header-title-area">
                <h1><i class="fas fa-chart-line"></i> Income Statement</h1>
                <p class="header-subtitle">Profit & Loss Report</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('bookkeeping.dashboard') }}" class="btn-header btn-header-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <a href="{{ route('bookkeeping.pl.index') }}" class="btn-header btn-header-success">
                    <i class="fas fa-exchange-alt"></i> P&L Transactions
                </a>
                <button onclick="window.print()" class="btn-header btn-header-primary">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>

    <div class="content-area">
        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card revenue">
                <div class="summary-value">${{ number_format(abs($totalIncome), 2) }}</div>
                <div class="summary-label">Total Revenue</div>
            </div>
            <div class="summary-card cogs">
                <div class="summary-value">${{ number_format(abs($totalCogs), 2) }}</div>
                <div class="summary-label">Cost of Goods</div>
            </div>
            <div class="summary-card expenses">
                <div class="summary-value">${{ number_format(abs($totalExpenses), 2) }}</div>
                <div class="summary-label">Expenses</div>
            </div>
            <div class="summary-card net">
                <div class="summary-value {{ $netIncome >= 0 ? 'profit' : 'loss' }}">
                    ${{ number_format(abs($netIncome), 2) }}
                </div>
                <div class="summary-label">Net {{ $netIncome >= 0 ? 'Profit' : 'Loss' }}</div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="report-layout">
            <!-- Left Column: Revenue & COGS -->
            <div class="report-left-column">
                <!-- Revenue Card -->
                <div class="report-card">
                    <div class="report-header">
                        <div class="report-header-left">
                            <div class="report-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <div>
                                <div class="report-title">Revenue / Income</div>
                                <div class="report-period">All income accounts</div>
                            </div>
                        </div>
                        <span class="section-total" style="color: #059669; font-size: 18px;">${{ number_format(abs($totalIncome), 2) }}</span>
                    </div>
                    <div class="report-body">
                        <ul class="account-list">
                            @forelse($income as $account)
                            @if(abs($account->current_balance) > 0)
                            <li class="account-item">
                                <div class="account-info">
                                    <span class="account-code">{{ $account->account_code }}</span>
                                    <span class="account-name">{{ $account->name }}</span>
                                </div>
                                <span class="account-balance">${{ number_format(abs($account->current_balance), 2) }}</span>
                            </li>
                            @endif
                            @empty
                            <li class="account-item"><span class="account-name" style="color: #9ca3af;">No revenue recorded</span></li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- COGS Card -->
                <div class="report-card">
                    <div class="report-header">
                        <div class="report-header-left">
                            <div class="report-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                <i class="fas fa-box"></i>
                            </div>
                            <div>
                                <div class="report-title">Cost of Goods Sold</div>
                                <div class="report-period">Direct costs</div>
                            </div>
                        </div>
                        <span class="section-total" style="color: #d97706; font-size: 18px;">${{ number_format(abs($totalCogs), 2) }}</span>
                    </div>
                    <div class="report-body">
                        <ul class="account-list">
                            @forelse($cogs as $account)
                            @if(abs($account->current_balance) > 0)
                            <li class="account-item">
                                <div class="account-info">
                                    <span class="account-code">{{ $account->account_code }}</span>
                                    <span class="account-name">{{ $account->name }}</span>
                                </div>
                                <span class="account-balance">${{ number_format(abs($account->current_balance), 2) }}</span>
                            </li>
                            @endif
                            @empty
                            <li class="account-item"><span class="account-name" style="color: #9ca3af;">No COGS recorded</span></li>
                            @endforelse
                        </ul>
                        
                        <!-- Gross Profit -->
                        <div class="subtotal-row">
                            <span class="subtotal-label"><i class="fas fa-equals"></i> Gross Profit</span>
                            <span class="subtotal-value {{ $grossProfit >= 0 ? 'profit' : 'loss' }}">
                                ${{ number_format(abs($grossProfit), 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Expenses & Net -->
            <div class="report-right-column">
                <!-- Expenses Card -->
                <div class="report-card">
                    <div class="report-header">
                        <div class="report-header-left">
                            <div class="report-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <div>
                                <div class="report-title">Operating Expenses</div>
                                <div class="report-period">All expense accounts</div>
                            </div>
                        </div>
                        <span class="section-total" style="color: #dc2626; font-size: 18px;">${{ number_format(abs($totalExpenses), 2) }}</span>
                    </div>
                    <div class="report-body">
                        <ul class="account-list">
                            @forelse($expenses as $account)
                            @if(abs($account->current_balance) > 0)
                            <li class="account-item">
                                <div class="account-info">
                                    <span class="account-code">{{ $account->account_code }}</span>
                                    <span class="account-name">{{ $account->name }}</span>
                                </div>
                                <span class="account-balance">${{ number_format(abs($account->current_balance), 2) }}</span>
                            </li>
                            @endif
                            @empty
                            <li class="account-item"><span class="account-name" style="color: #9ca3af;">No expenses recorded</span></li>
                            @endforelse
                        </ul>

                        <!-- Net Income -->
                        <div class="net-income-row">
                            <span class="net-income-label">
                                <i class="fas fa-{{ $netIncome >= 0 ? 'trophy' : 'exclamation-triangle' }}"></i>
                                Net {{ $netIncome >= 0 ? 'Income' : 'Loss' }}
                            </span>
                            <span class="net-income-value {{ $netIncome >= 0 ? 'profit' : 'loss' }}">
                                ${{ number_format(abs($netIncome), 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Manual P&L Transactions Summary (if any exist) -->
                @if(isset($manualIncomeTransactions) && ($manualIncomeTransactions->count() > 0 || $manualExpenseTransactions->count() > 0))
                <div class="report-card">
                    <div class="report-header">
                        <div class="report-header-left">
                            <div class="report-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div>
                                <div class="report-title">Manual P&L Entries</div>
                                <div class="report-period">{{ $startDate }} to {{ $endDate }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="report-body">
                        <div class="manual-entries-grid">
                            <div class="manual-entry-item">
                                <div class="manual-entry-label">Manual Income</div>
                                <div class="manual-entry-value income">${{ number_format($manualIncomeTotal ?? 0, 2) }}</div>
                                <div class="manual-entry-count">{{ $manualIncomeTransactions->count() }} entries</div>
                            </div>
                            <div class="manual-entry-item">
                                <div class="manual-entry-label">Manual Expenses</div>
                                <div class="manual-entry-value expense">${{ number_format($manualExpenseTotal ?? 0, 2) }}</div>
                                <div class="manual-entry-count">{{ $manualExpenseTransactions->count() }} entries</div>
                            </div>
                            <div class="manual-entry-item">
                                <div class="manual-entry-label">Net from Manual</div>
                                @php $manualNet = ($manualIncomeTotal ?? 0) - ($manualExpenseTotal ?? 0); @endphp
                                <div class="manual-entry-value {{ $manualNet >= 0 ? 'income' : 'expense' }}">
                                    ${{ number_format(abs($manualNet), 2) }}
                                </div>
                                <div class="manual-entry-count">{{ $manualNet >= 0 ? 'Profit' : 'Loss' }}</div>
                            </div>
                        </div>
                        <div class="manual-entries-link">
                            <a href="{{ route('bookkeeping.pl.index') }}">
                                <i class="fas fa-external-link-alt"></i> View All P&L Transactions
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Full Width Statement Card for Print -->
        <div class="report-card full-width" style="margin-top: 24px; display: none;" id="print-statement">
            <div class="report-header" style="text-align: center; display: block;">
                <div class="report-title" style="font-size: 20px;">Income Statement</div>
                <div class="report-period">For the period ending {{ now()->format('F d, Y') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

