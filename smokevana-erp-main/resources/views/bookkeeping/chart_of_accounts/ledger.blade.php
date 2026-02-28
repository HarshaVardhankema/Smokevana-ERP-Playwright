@extends('layouts.app')

@section('title', __('bookkeeping.account_ledger') . ' - ' . $account->name)

@section('css')
<style>
/* Account Ledger - Professional Purple Theme */
.ledger-page {
    background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
    min-height: 100vh;
    padding-bottom: 40px;
}

.ledger-header {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
    border-radius: 16px;
    padding: 28px 32px;
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

.ledger-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.ledger-header h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #fff !important;
}

.ledger-header h1 i {
    font-size: 28px;
    color: #fff !important;
}

.ledger-header .subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9) !important;
    margin: 0;
}

.ledger-header .account-code {
    background: rgba(255,255,255,0.2);
    padding: 4px 12px;
    border-radius: 6px;
    font-family: 'SF Mono', Monaco, monospace;
    font-size: 14px;
}

.ledger-header-actions {
    display: flex;
    gap: 12px;
    position: relative;
    z-index: 2;
}

.ledger-header-actions .btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 10px 20px;
    font-size: 14px;
    border: none;
}

.ledger-header-actions .btn-light {
    background: rgba(255,255,255,0.95);
    color: #7c3aed;
}

.ledger-header-actions .btn-light:hover {
    background: #fff;
}

/* Summary Cards */
.ledger-summary {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}

@media (max-width: 1200px) {
    .ledger-summary { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 576px) {
    .ledger-summary { grid-template-columns: 1fr; }
}

.ledger-summary-card {
    background: #fff;
    border-radius: 14px;
    padding: 20px 24px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    border-left: 4px solid #e5e7eb;
}

.ledger-summary-card.balance { border-left-color: #10b981; }
.ledger-summary-card.debits { border-left-color: #3b82f6; }
.ledger-summary-card.credits { border-left-color: #f59e0b; }
.ledger-summary-card.entries { border-left-color: #8b5cf6; }

.ledger-summary-label {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.ledger-summary-value {
    font-size: 24px;
    font-weight: 700;
    font-family: 'SF Mono', Monaco, monospace;
    margin-top: 4px;
}

.ledger-summary-card.balance .ledger-summary-value { color: #059669; }
.ledger-summary-card.debits .ledger-summary-value { color: #2563eb; }
.ledger-summary-card.credits .ledger-summary-value { color: #d97706; }
.ledger-summary-card.entries .ledger-summary-value { color: #7c3aed; }

/* Filters */
.ledger-filters {
    background: #fff;
    border-radius: 14px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    align-items: center;
}

.ledger-filters .form-control {
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    padding: 10px 16px;
    font-size: 14px;
}

.ledger-filters .form-control:focus {
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.ledger-filters .btn {
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
}

/* Ledger Table */
.ledger-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    overflow: hidden;
}

.ledger-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ledger-card-title {
    font-size: 16px;
    font-weight: 600;
    color: #1e1b4b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ledger-card-title i {
    color: #8b5cf6;
}

.ledger-table {
    width: 100%;
    border-collapse: collapse;
}

.ledger-table thead th {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 14px 16px;
    text-align: left;
}

.ledger-table thead th.text-right {
    text-align: right;
}

.ledger-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid #f5f5f5;
    font-size: 14px;
    color: #374151;
    vertical-align: middle;
}

.ledger-table tbody tr {
    transition: background 0.2s ease;
}

.ledger-table tbody tr:hover {
    background: #faf5ff;
}

.ledger-table tbody tr:last-child td {
    border-bottom: none;
}

.ledger-entry-number {
    font-weight: 600;
    color: #7c3aed;
}

.ledger-entry-number:hover {
    text-decoration: underline;
}

.ledger-date {
    color: #6b7280;
    font-size: 13px;
}

.ledger-memo {
    color: #374151;
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.ledger-amount {
    font-family: 'SF Mono', Monaco, monospace;
    font-weight: 600;
    font-size: 14px;
}

.ledger-amount.debit {
    color: #2563eb;
}

.ledger-amount.credit {
    color: #d97706;
}

.ledger-balance {
    font-family: 'SF Mono', Monaco, monospace;
    font-weight: 700;
    font-size: 14px;
}

.ledger-balance.positive { color: #059669; }
.ledger-balance.negative { color: #dc2626; }

.ledger-type-badge {
    font-size: 10px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    text-transform: uppercase;
}

.ledger-type-badge.standard { background: #ede9fe; color: #7c3aed; }
.ledger-type-badge.adjusting { background: #fef3c7; color: #92400e; }
.ledger-type-badge.closing { background: #fee2e2; color: #991b1b; }
.ledger-type-badge.deposit { background: #d1fae5; color: #065f46; }
.ledger-type-badge.expense { background: #ffedd5; color: #9a3412; }

/* Empty State */
.ledger-empty {
    text-align: center;
    padding: 60px 20px;
}

.ledger-empty i {
    font-size: 60px;
    color: #ddd6fe;
    margin-bottom: 20px;
}

.ledger-empty h4 {
    color: #1e1b4b;
    margin-bottom: 10px;
}

.ledger-empty p {
    color: #6b7280;
    margin-bottom: 24px;
}

/* Pagination */
.ledger-pagination {
    padding: 20px 24px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: center;
}

.ledger-pagination .pagination {
    margin: 0;
}

.ledger-pagination .pagination li a,
.ledger-pagination .pagination li span {
    border-radius: 8px;
    margin: 0 2px;
    border: 1px solid #e5e7eb;
    color: #374151;
}

.ledger-pagination .pagination li.active a {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    border-color: #8b5cf6;
}

/* Animation */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

.ledger-summary-card, .ledger-card {
    animation: fadeInUp 0.4s ease forwards;
}
</style>
@endsection

@section('content')
<section class="content ledger-page">
    
    <!-- Header -->
    <div class="ledger-header">
        <div>
            <h1>
                <i class="fas fa-book"></i> 
                @lang('bookkeeping.account_ledger')
            </h1>
            <p class="subtitle">
                <span class="account-code">{{ $account->account_code }}</span>
                {{ $account->name }}
            </p>
        </div>
        <div class="ledger-header-actions">
            <a href="{{ route('bookkeeping.accounts.edit', $account->id) }}" class="btn btn-light">
                <i class="fas fa-edit"></i> @lang('messages.edit')
            </a>
            <a href="{{ route('bookkeeping.accounts.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> @lang('messages.back')
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    @php
        $totalDebits = $entries->sum(function($entry) {
            return $entry->type === 'debit' ? $entry->amount : 0;
        });
        $totalCredits = $entries->sum(function($entry) {
            return $entry->type === 'credit' ? $entry->amount : 0;
        });
    @endphp
    <div class="ledger-summary">
        <div class="ledger-summary-card balance">
            <div class="ledger-summary-label">@lang('bookkeeping.current_balance')</div>
            <div class="ledger-summary-value">@format_currency($account->current_balance ?? 0)</div>
        </div>
        <div class="ledger-summary-card debits">
            <div class="ledger-summary-label">@lang('bookkeeping.total_debits')</div>
            <div class="ledger-summary-value">@format_currency($totalDebits)</div>
        </div>
        <div class="ledger-summary-card credits">
            <div class="ledger-summary-label">@lang('bookkeeping.total_credits')</div>
            <div class="ledger-summary-value">@format_currency($totalCredits)</div>
        </div>
        <div class="ledger-summary-card entries">
            <div class="ledger-summary-label">@lang('bookkeeping.total_entries')</div>
            <div class="ledger-summary-value">{{ $entries->total() }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="ledger-filters">
        <form method="GET" action="{{ route('bookkeeping.accounts.ledger', $account->id) }}" class="d-flex gap-3 flex-wrap align-items-center" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; width: 100%;">
            <div class="form-group mb-0">
                <input type="date" name="start_date" class="form-control" 
                       value="{{ request('start_date') }}" placeholder="Start Date">
            </div>
            <div class="form-group mb-0">
                <input type="date" name="end_date" class="form-control" 
                       value="{{ request('end_date') }}" placeholder="End Date">
            </div>
            <div class="form-group mb-0" style="flex: 1;">
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" placeholder="🔍 @lang('messages.search') @lang('bookkeeping.memo')...">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> @lang('report.filter')
            </button>
            <a href="{{ route('bookkeeping.accounts.ledger', $account->id) }}" class="btn btn-default">
                <i class="fas fa-redo"></i> @lang('report.reset')
            </a>
        </form>
    </div>

    <!-- Ledger Table -->
    <div class="ledger-card">
        <div class="ledger-card-header">
            <h3 class="ledger-card-title">
                <i class="fas fa-list"></i>
                @lang('bookkeeping.ledger_entries')
            </h3>
            <span class="text-muted">
                @lang('bookkeeping.showing') {{ $entries->firstItem() ?? 0 }} - {{ $entries->lastItem() ?? 0 }} 
                @lang('lang_v1.of') {{ $entries->total() }}
            </span>
        </div>

        @if($entries->count() > 0)
        <table class="ledger-table">
            <thead>
                <tr>
                    <th style="width: 10%;">@lang('bookkeeping.date')</th>
                    <th style="width: 12%;">@lang('bookkeeping.entry_number')</th>
                    <th style="width: 10%;">@lang('bookkeeping.type')</th>
                    <th>@lang('bookkeeping.description')</th>
                    <th class="text-right" style="width: 12%;">@lang('bookkeeping.debit')</th>
                    <th class="text-right" style="width: 12%;">@lang('bookkeeping.credit')</th>
                    <th class="text-right" style="width: 12%;">@lang('bookkeeping.balance')</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $runningBalance = $account->opening_balance ?? 0;
                    $normalBalance = \App\Models\ChartOfAccount::getNormalBalance($account->account_type);
                @endphp
                @foreach($entries as $entry)
                @php
                    // Calculate running balance
                    if ($entry->type === 'debit') {
                        $runningBalance = $normalBalance === 'debit' 
                            ? $runningBalance + $entry->amount 
                            : $runningBalance - $entry->amount;
                    } else {
                        $runningBalance = $normalBalance === 'credit' 
                            ? $runningBalance + $entry->amount 
                            : $runningBalance - $entry->amount;
                    }
                @endphp
                <tr>
                    <td>
                        <span class="ledger-date">
                            {{ $entry->journalEntry ? \Carbon\Carbon::parse($entry->journalEntry->entry_date)->format('M d, Y') : '-' }}
                        </span>
                    </td>
                    <td>
                        @if($entry->journalEntry)
                        <a href="{{ route('bookkeeping.journal.show', $entry->journalEntry->id) }}" class="ledger-entry-number">
                            {{ $entry->journalEntry->entry_number }}
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $entryType = $entry->journalEntry->entry_type ?? 'standard';
                            $typeClass = 'standard';
                            if (in_array($entryType, ['adjusting', 'closing'])) $typeClass = $entryType;
                            elseif ($entryType === 'bank_deposit') $typeClass = 'deposit';
                            elseif ($entryType === 'expense') $typeClass = 'expense';
                        @endphp
                        <span class="ledger-type-badge {{ $typeClass }}">
                            {{ ucfirst(str_replace('_', ' ', $entryType)) }}
                        </span>
                    </td>
                    <td>
                        <span class="ledger-memo" title="{{ $entry->description ?? ($entry->journalEntry->memo ?? '') }}">
                            {{ $entry->description ?? ($entry->journalEntry->memo ?? '-') }}
                        </span>
                    </td>
                    <td class="text-right">
                        @if($entry->type === 'debit')
                        <span class="ledger-amount debit">@format_currency($entry->amount)</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($entry->type === 'credit')
                        <span class="ledger-amount credit">@format_currency($entry->amount)</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <span class="ledger-balance {{ $runningBalance >= 0 ? 'positive' : 'negative' }}">
                            @format_currency($runningBalance)
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($entries->hasPages())
        <div class="ledger-pagination">
            {{ $entries->appends(request()->query())->links() }}
        </div>
        @endif

        @else
        <div class="ledger-empty">
            <i class="fas fa-book-open"></i>
            <h4>@lang('bookkeeping.no_ledger_entries')</h4>
            <p>@lang('bookkeeping.no_transactions_for_account')</p>
            <a href="{{ route('bookkeeping.journal.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> @lang('bookkeeping.create_journal_entry')
            </a>
        </div>
        @endif
    </div>

</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Auto-submit filters on date change
    $('input[type="date"]').on('change', function() {
        // Optional: auto-submit on date change
        // $(this).closest('form').submit();
    });
});
</script>
@endsection



