@extends('layouts.app')
@section('title', 'Transaction Details')

@section('css')
<style>
    .transaction-wrapper {
        padding: 0;
        background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
        min-height: calc(100vh - 60px);
    }

    .page-header-banner {
        background: linear-gradient(135deg, {{ $transaction->transaction_type === 'income' ? '#10b981 0%, #059669 50%, #047857 100%' : '#ef4444 0%, #dc2626 50%, #b91c1c 100%' }});
        padding: 32px 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .page-header-banner::before {
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
        gap: 20px;
    }

    .header-title-area h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .header-subtitle {
        font-size: 15px;
        opacity: 0.85;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .btn-header {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
        background: rgba(255,255,255,0.15);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.25);
    }

    .btn-header:hover {
        background: rgba(255,255,255,0.25);
        color: #fff;
        text-decoration: none;
    }

    .content-area {
        padding: 30px 40px;
        max-width: 1000px;
        margin: 0 auto;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    .detail-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
        border: 1px solid rgba(139, 92, 246, 0.1);
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #fafbff 0%, #f5f3ff 100%);
        padding: 16px 24px;
        border-bottom: 1px solid rgba(139, 92, 246, 0.1);
    }

    .card-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: #1e1b4b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-body {
        padding: 24px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-size: 14px;
        color: #6b7280;
    }

    .info-value {
        font-size: 14px;
        font-weight: 600;
        color: #1e1b4b;
        text-align: right;
    }

    .amount-highlight {
        font-size: 32px;
        font-weight: 700;
        font-family: 'JetBrains Mono', monospace;
        text-align: center;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .amount-highlight.income {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        color: #059669;
    }

    .amount-highlight.expense {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: #dc2626;
    }

    .badge-type {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-income {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-expense {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-status {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .badge-posted { background: #d1fae5; color: #065f46; }
    .badge-draft { background: #f3f4f6; color: #4b5563; }
    .badge-voided { background: #fee2e2; color: #991b1b; }

    .description-box {
        background: #f9fafb;
        border-radius: 10px;
        padding: 16px;
        font-size: 14px;
        color: #374151;
        line-height: 1.6;
    }

    /* Journal Entry Section */
    .journal-entry-card {
        margin-top: 24px;
    }

    .journal-table {
        width: 100%;
        border-collapse: collapse;
    }

    .journal-table th {
        background: #f3f4f6;
        padding: 12px 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .journal-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }

    .debit-amount {
        color: #059669;
        font-weight: 600;
        font-family: monospace;
    }

    .credit-amount {
        color: #dc2626;
        font-weight: 600;
        font-family: monospace;
    }

    .contact-card {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #93c5fd;
        border-radius: 12px;
        padding: 16px;
        margin-top: 16px;
    }

    .contact-card h4 {
        font-size: 14px;
        font-weight: 600;
        color: #1e40af;
        margin: 0 0 8px 0;
    }

    .contact-card p {
        font-size: 16px;
        font-weight: 700;
        color: #1e3a8a;
        margin: 0;
    }

    @media (max-width: 992px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .content-area {
            padding: 20px;
        }
    }
</style>
@endsection

@section('content')
<div class="transaction-wrapper">
    <!-- Header -->
    <div class="page-header-banner">
        <div class="header-content">
            <div class="header-title-area">
                <h1>
                    <i class="fas fa-{{ $transaction->transaction_type === 'income' ? 'arrow-up' : 'arrow-down' }}"></i>
                    {{ $transaction->transaction_type === 'income' ? 'Income' : 'Expense' }} Details
                </h1>
                <p class="header-subtitle">Reference: {{ $transaction->reference_number }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('bookkeeping.pl.index') }}" class="btn-header">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <button onclick="window.print()" class="btn-header">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>

    <div class="content-area">
        <div class="detail-grid">
            <!-- Main Details -->
            <div>
                <div class="detail-card">
                    <div class="card-header">
                        <h3><i class="fas fa-file-alt"></i> Transaction Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="amount-highlight {{ $transaction->transaction_type }}">
                            {{ $transaction->transaction_type === 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                        </div>

                        <div class="info-row">
                            <span class="info-label">Reference Number</span>
                            <span class="info-value">{{ $transaction->reference_number }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Type</span>
                            <span class="info-value">
                                <span class="badge-type badge-{{ $transaction->transaction_type }}">
                                    <i class="fas fa-{{ $transaction->transaction_type === 'income' ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ ucfirst($transaction->transaction_type) }}
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Category</span>
                            <span class="info-value">{{ $transaction->category_label }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date</span>
                            <span class="info-value">{{ $transaction->transaction_date->format('F d, Y') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                <span class="badge-status badge-{{ $transaction->status }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </span>
                        </div>

                        @if($transaction->description)
                        <div style="margin-top: 20px;">
                            <label class="info-label" style="display: block; margin-bottom: 8px;">Description</label>
                            <div class="description-box">{{ $transaction->description }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Journal Entry -->
                @if($transaction->journalEntry)
                <div class="detail-card journal-entry-card">
                    <div class="card-header">
                        <h3><i class="fas fa-book"></i> Journal Entry</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">Entry Number</span>
                            <span class="info-value">{{ $transaction->journalEntry->entry_number }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Entry Date</span>
                            <span class="info-value">{{ $transaction->journalEntry->entry_date->format('F d, Y') }}</span>
                        </div>

                        <table class="journal-table" style="margin-top: 16px;">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th style="text-align: right;">Debit</th>
                                    <th style="text-align: right;">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->journalEntry->lines as $line)
                                <tr>
                                    <td>{{ $line->account ? $line->account->name : '-' }}</td>
                                    <td style="text-align: right;">
                                        @if($line->type === 'debit')
                                        <span class="debit-amount">${{ number_format($line->amount, 2) }}</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        @if($line->type === 'credit')
                                        <span class="credit-amount">${{ number_format($line->amount, 2) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <!-- Side Panel -->
            <div>
                <!-- Account Info -->
                <div class="detail-card">
                    <div class="card-header">
                        <h3><i class="fas fa-wallet"></i> Account Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">{{ $transaction->transaction_type === 'income' ? 'Income' : 'Expense' }} Account</span>
                            <span class="info-value">{{ $transaction->account ? $transaction->account->name : '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Payment Account</span>
                            <span class="info-value">{{ $transaction->paymentAccount ? $transaction->paymentAccount->name : '-' }}</span>
                        </div>
                        @if($transaction->payment_method)
                        <div class="info-row">
                            <span class="info-label">Payment Method</span>
                            <span class="info-value">{{ ucwords(str_replace('_', ' ', $transaction->payment_method)) }}</span>
                        </div>
                        @endif
                        @if($transaction->payment_reference)
                        <div class="info-row">
                            <span class="info-label">Payment Reference</span>
                            <span class="info-value">{{ $transaction->payment_reference }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Customer/Vendor Info -->
                @if($transaction->contact)
                <div class="detail-card" style="margin-top: 24px;">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-{{ $transaction->transaction_type === 'income' ? 'user' : 'truck' }}"></i>
                            {{ $transaction->transaction_type === 'income' ? 'Customer' : 'Vendor' }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="contact-card">
                            <h4>{{ $transaction->transaction_type === 'income' ? 'Customer' : 'Vendor' }} Name</h4>
                            <p>{{ $transaction->contact->name }}</p>
                        </div>
                        @if($transaction->invoice_number)
                        <div class="info-row" style="margin-top: 16px;">
                            <span class="info-label">Invoice #</span>
                            <span class="info-value">{{ $transaction->invoice_number }}</span>
                        </div>
                        @endif
                        @if($transaction->bill_number)
                        <div class="info-row" style="margin-top: 16px;">
                            <span class="info-label">Bill #</span>
                            <span class="info-value">{{ $transaction->bill_number }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Audit Info -->
                <div class="detail-card" style="margin-top: 24px;">
                    <div class="card-header">
                        <h3><i class="fas fa-history"></i> Audit Trail</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">Created By</span>
                            <span class="info-value">
                                {{ $transaction->createdBy ? $transaction->createdBy->first_name . ' ' . $transaction->createdBy->last_name : '-' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Created At</span>
                            <span class="info-value">{{ $transaction->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        @if($transaction->updated_at && $transaction->updated_at != $transaction->created_at)
                        <div class="info-row">
                            <span class="info-label">Last Updated</span>
                            <span class="info-value">{{ $transaction->updated_at->format('M d, Y h:i A') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




