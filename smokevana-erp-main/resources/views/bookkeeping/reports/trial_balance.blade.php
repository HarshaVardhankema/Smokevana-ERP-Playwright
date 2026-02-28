@extends('layouts.app')
@section('title', 'Trial Balance')

@section('css')
<style>
    /* ===== Page Container ===== */
    .trial-balance-wrapper {
        padding: 0;
        background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
        min-height: calc(100vh - 60px);
    }

    /* ===== Header Banner - Amazon style ===== */
    .report-header-banner {
        background: #37475a;
        border-radius: 6px;
        padding: 22px 28px;
        color: #fff !important;
        box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
    }

    .report-header-banner::before { display: none; }

    .report-header-banner .header-title-area h1,
    .report-header-banner .header-title-area h1 *,
    .report-header-banner .header-subtitle {
        color: #fff !important;
    }

    .report-header-banner .header-title-area h1 i {
        color: #fff !important;
    }

    .header-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-title-area h1 {
        font-size: 32px;
        font-weight: 800;
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 16px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        letter-spacing: -0.5px;
        color: #fff !important;
    }

    .header-title-area h1 i {
        font-size: 36px;
        opacity: 1;
        background: rgba(255,255,255,0.2);
        padding: 12px;
        border-radius: 12px;
        color: #fff !important;
    }

    .header-subtitle {
        font-size: 16px;
        opacity: 0.95;
        font-weight: 500;
        letter-spacing: 0.2px;
        color: #fff !important;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .btn-header {
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-header-secondary {
        background: rgba(255,255,255,0.2);
        color: #fff;
        border: 2px solid rgba(255,255,255,0.35);
    }

    .btn-header-secondary:hover {
        background: rgba(255,255,255,0.3);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }

    .btn-header-primary {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border: 1px solid #C7511F !important;
        color: #fff !important;
    }

    .btn-header-primary:hover {
        opacity: 0.95;
        color: #fff !important;
        transform: translateY(-2px);
    }

    /* ===== Content Area ===== */
    .content-area {
        padding: 32px 40px;
    }

    /* ===== Report Info ===== */
    .report-info-card {
        background: #fff;
        border-radius: 16px;
        padding: 28px 32px;
        margin-bottom: 28px;
        box-shadow: 0 4px 24px rgba(139, 92, 246, 0.1);
        border: 1px solid rgba(139, 92, 246, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
    }

    .report-meta {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .meta-label {
        font-size: 11px;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }

    .meta-value {
        font-size: 16px;
        font-weight: 700;
        color: #1e1b4b;
    }

    .balance-status {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 24px;
        border-radius: 12px;
    }

    .balance-status.balanced {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #047857;
        border: 1px solid rgba(4, 120, 87, 0.2);
    }

    .balance-status.unbalanced {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #b91c1c;
        border: 1px solid rgba(185, 28, 28, 0.2);
    }

    .balance-status i {
        font-size: 22px;
    }

    .balance-status span {
        font-weight: 700;
        font-size: 14px;
        letter-spacing: 0.2px;
    }

    /* ===== Summary Cards ===== */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 28px;
    }

    @media (max-width: 992px) {
        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }

    .summary-card {
        background: #fff;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 4px 24px rgba(139, 92, 246, 0.1);
        border: 1px solid rgba(139, 92, 246, 0.08);
        position: relative;
        overflow: hidden;
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
    }

    .summary-card.debits::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .summary-card.credits::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .summary-card.difference::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

    .summary-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .summary-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .summary-card.debits .summary-icon { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1d4ed8; }
    .summary-card.credits .summary-icon { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857; }
    .summary-card.difference .summary-icon { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #6d28d9; }

    .summary-value {
        font-size: 32px;
        font-weight: 800;
        color: #1e1b4b;
        margin-bottom: 6px;
        letter-spacing: -0.5px;
    }

    .summary-label {
        font-size: 14px;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ===== Table Card ===== */
    .table-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(139, 92, 246, 0.1);
        border: 1px solid rgba(139, 92, 246, 0.08);
        overflow: hidden;
    }

    .table-header {
        background: linear-gradient(135deg, #fafbff 0%, #f5f3ff 100%);
        padding: 22px 28px;
        border-bottom: 2px solid rgba(139, 92, 246, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e1b4b;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .table-title i {
        color: #8b5cf6;
        font-size: 20px;
    }

    /* ===== Trial Balance Table ===== */
    .trial-balance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .trial-balance-table thead th {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: #fff;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 18px 24px;
        text-align: left;
        border: none;
    }

    .trial-balance-table thead th:nth-child(3),
    .trial-balance-table thead th:nth-child(4) {
        text-align: right;
    }

    .trial-balance-table tbody td {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 15px;
        color: #374151;
        vertical-align: middle;
    }

    .trial-balance-table tbody tr {
        transition: all 0.2s ease;
    }

    .trial-balance-table tbody tr:nth-child(even) {
        background: #fafbff;
    }

    .trial-balance-table tbody tr:hover {
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    }

    .trial-balance-table tbody tr:last-child td {
        border-bottom: none;
    }

    .trial-balance-table tbody td:nth-child(3),
    .trial-balance-table tbody td:nth-child(4) {
        text-align: right;
        font-family: 'SF Mono', 'Monaco', 'Consolas', monospace;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    /* Account Info Cell */
    .account-cell {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .account-code {
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        color: #6d28d9;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        font-family: 'SF Mono', 'Monaco', 'Consolas', monospace;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .account-name {
        font-weight: 600;
        color: #1e1b4b;
        font-size: 15px;
    }

    /* Type Badge */
    .type-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        white-space: nowrap;
    }

    .type-badge.asset { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1d4ed8; }
    .type-badge.liability { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #b45309; }
    .type-badge.equity { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857; }
    .type-badge.income { background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #15803d; }
    .type-badge.expense { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #b91c1c; }
    .type-badge.cost-of-goods-sold { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
    .type-badge.other-income { background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #15803d; }
    .type-badge.other-expense { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #b91c1c; }

    /* Amount Cells */
    .amount-debit {
        color: #1d4ed8;
        font-weight: 700;
    }

    .amount-credit {
        color: #047857;
        font-weight: 700;
    }

    .amount-zero {
        color: #d1d5db;
        font-weight: 500;
    }

    /* ===== Totals Row ===== */
    .trial-balance-table tfoot td {
        background-color: #ff9900;
        color: #000000ff;
        font-weight: 800;
        padding: 20px 24px;
        font-size: 16px;
        letter-spacing: 0.3px;
    }

    .trial-balance-table tfoot td:nth-child(3),
    .trial-balance-table tfoot td:nth-child(4) {
        text-align: right;
        font-family: 'SF Mono', 'Monaco', 'Consolas', monospace;
    }

    /* ===== Empty State ===== */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }

    .empty-state-icon {
        width: 90px;
        height: 90px;
        margin: 0 auto 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: #8b5cf6;
    }

    .empty-state h4 {
        font-size: 20px;
        font-weight: 700;
        color: #1e1b4b;
        margin-bottom: 10px;
    }

    .empty-state p {
        font-size: 15px;
        color: #6b7280;
    }

    /* ===== Print Styles ===== */
    @media print {
        .report-header-banner {
            background: #1e1b4b !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .btn-header, .header-actions {
            display: none !important;
        }

        .trial-balance-table thead th {
            background: #1e1b4b !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .table-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .trial-balance-table tbody td {
            padding: 14px 20px;
        }
    }

    /* ===== Animations ===== */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .summary-card, .table-card, .report-info-card {
        animation: fadeInUp 0.4s ease forwards;
    }

    .summary-card:nth-child(1) { animation-delay: 0.1s; }
    .summary-card:nth-child(2) { animation-delay: 0.15s; }
    .summary-card:nth-child(3) { animation-delay: 0.2s; }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .report-header-banner {
            padding: 28px 20px;
        }

        .header-content {
            flex-direction: column;
            text-align: center;
        }

        .header-title-area h1 {
            font-size: 24px;
            justify-content: center;
        }

        .content-area {
            padding: 20px;
        }

        .report-info-card {
            flex-direction: column;
            text-align: center;
        }

        .report-meta {
            justify-content: center;
        }

        .trial-balance-table tbody td {
            padding: 16px 18px;
        }
    }
</style>
@endsection

@section('content')
<div class="trial-balance-wrapper">
    <!-- Header Banner -->
    <div class="report-header-banner">
        <div class="header-content">
            <div class="header-title-area">
                <h1>
                    <i class="fas fa-balance-scale"></i>
                    Trial Balance
                </h1>
                <p class="header-subtitle">Summary of all account balances to verify debits equal credits</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('bookkeeping.dashboard') }}" class="btn-header btn-header-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <button onclick="window.print()" class="btn-header btn-header-primary">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="content-area">
        <!-- Report Info -->
        <div class="report-info-card">
            <div class="report-meta">
                <div class="meta-item">
                    <span class="meta-label">Report Date</span>
                    <span class="meta-value">{{ now()->format('F d, Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Business</span>
                    <span class="meta-value">{{ auth()->user()->business->name ?? 'Go Hunter Distro' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Accounts</span>
                    <span class="meta-value">{{ $accounts->count() }} Active Accounts</span>
                </div>
            </div>
            @php
                $isBalanced = abs($totalDebits - $totalCredits) < 0.01;
            @endphp
            <div class="balance-status {{ $isBalanced ? 'balanced' : 'unbalanced' }}">
                <i class="fas {{ $isBalanced ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i>
                <span>{{ $isBalanced ? 'Books are Balanced' : 'Books are Out of Balance' }}</span>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card debits">
                <div class="summary-card-header">
                    <div class="summary-icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
                <div class="summary-value">${{ number_format($totalDebits, 2) }}</div>
                <div class="summary-label">Total Debits</div>
            </div>
            <div class="summary-card credits">
                <div class="summary-card-header">
                    <div class="summary-icon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>
                <div class="summary-value">${{ number_format($totalCredits, 2) }}</div>
                <div class="summary-label">Total Credits</div>
            </div>
            <div class="summary-card difference">
                <div class="summary-card-header">
                    <div class="summary-icon">
                        <i class="fas fa-equals"></i>
                    </div>
                </div>
                <div class="summary-value">${{ number_format(abs($totalDebits - $totalCredits), 2) }}</div>
                <div class="summary-label">Difference</div>
            </div>
        </div>

        <!-- Trial Balance Table -->
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-list-alt"></i> Account Balances
                </div>
            </div>
            @if($accounts->count() > 0)
            <table class="trial-balance-table">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Type</th>
                        <th>Debit</th>
                        <th>Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                    @php
                        $normalBalance = \App\Models\ChartOfAccount::getNormalBalance($account->account_type);
                        $balance = abs($account->current_balance);
                        $isDebit = $normalBalance === 'debit';
                    @endphp
                    @if($balance > 0)
                    <tr>
                        <td>
                            <div class="account-cell">
                                <span class="account-code">{{ $account->account_code }}</span>
                                <span class="account-name">{{ $account->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="type-badge {{ str_replace('_', '-', $account->account_type) }}">
                                {{ ucwords(str_replace('_', ' ', $account->account_type)) }}
                            </span>
                        </td>
                        <td class="{{ $isDebit ? 'amount-debit' : 'amount-zero' }}">
                            {{ $isDebit ? '$' . number_format($balance, 2) : '-' }}
                        </td>
                        <td class="{{ !$isDebit ? 'amount-credit' : 'amount-zero' }}">
                            {{ !$isDebit ? '$' . number_format($balance, 2) : '-' }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"><strong>TOTALS</strong></td>
                        <td>${{ number_format($totalDebits, 2) }}</td>
                        <td>${{ number_format($totalCredits, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <h4>No Account Balances</h4>
                <p>Start by setting up your Chart of Accounts and recording transactions.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Any additional JavaScript functionality
});
</script>
@endsection

