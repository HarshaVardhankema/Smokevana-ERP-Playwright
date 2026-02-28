@extends('layouts.app')
@section('title', 'View Liability')

@section('css')
<style>
/* Liability Show - Professional Purple Theme */
.lis-page { background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%); min-height: 100vh; padding-bottom: 40px; }

.lis-header {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
    border-radius: 16px; padding: 28px 32px; margin-bottom: 24px;
    display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;
    box-shadow: 0 8px 30px rgba(124, 58, 237, 0.25); position: relative; overflow: hidden;
}
.lis-header::before { content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); border-radius: 50%; }
.lis-header-left { flex: 1; }
.lis-header h1 { font-size: 26px; font-weight: 700; margin: 0 0 8px 0; color: #fff; display: flex; align-items: center; gap: 12px; }
.lis-header h1 i { font-size: 22px; opacity: 0.9; }
.lis-header .subtitle { font-size: 14px; color: rgba(255,255,255,0.85); margin: 0; }

.lis-status-badge {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.5px; margin-top: 12px;
}
.lis-status-badge::before { content: ''; width: 8px; height: 8px; border-radius: 50%; }
.lis-status-badge.active { background: rgba(16, 185, 129, 0.2); color: #10b981; }
.lis-status-badge.active::before { background: #10b981; }
.lis-status-badge.paid_off { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
.lis-status-badge.paid_off::before { background: #3b82f6; }
.lis-status-badge.overdue { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
.lis-status-badge.overdue::before { background: #ef4444; }
.lis-status-badge.defaulted { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
.lis-status-badge.defaulted::before { background: #ef4444; }
.lis-status-badge.restructured { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.lis-status-badge.restructured::before { background: #f59e0b; }

.lis-header-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.lis-btn {
    padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 13px;
    text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
    transition: all 0.2s ease; border: none; cursor: pointer;
}
.lis-btn-back { background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.3); }
.lis-btn-back:hover { background: rgba(255,255,255,0.25); color: #fff; text-decoration: none; }
.lis-btn-edit { background: #fff; color: #3b82f6; }
.lis-btn-edit:hover { background: #dbeafe; color: #2563eb; text-decoration: none; }
.lis-btn-pay { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); }
.lis-btn-pay:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4); color: #fff; text-decoration: none; }
.lis-btn-print { background: #fff; color: #6b7280; }
.lis-btn-print:hover { background: #f3f4f6; color: #374151; text-decoration: none; }

/* Info Cards Grid */
.lis-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
@media (max-width: 992px) { .lis-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px) { .lis-grid { grid-template-columns: 1fr; } }

.lis-card {
    background: #fff; border-radius: 14px; padding: 24px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
}
.lis-card-header {
    display: flex; align-items: center; gap: 12px; margin-bottom: 20px;
    padding-bottom: 16px; border-bottom: 1px solid #f1f5f9;
}
.lis-card-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}
.lis-card-icon.purple { background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); color: #7c3aed; }
.lis-card-icon.blue { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #3b82f6; }
.lis-card-icon.green { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669; }
.lis-card-icon.orange { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706; }
.lis-card-title { font-size: 15px; font-weight: 600; color: #1e1b4b; margin: 0; }

.lis-info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #f1f5f9; }
.lis-info-row:last-child { border-bottom: none; }
.lis-info-label { font-size: 13px; color: #6b7280; }
.lis-info-value { font-size: 14px; font-weight: 600; color: #1e1b4b; text-align: right; }
.lis-info-value.amount { font-family: 'SF Mono', Monaco, monospace; color: #7c3aed; }
.lis-info-value.balance { font-family: 'SF Mono', Monaco, monospace; color: #dc2626; font-size: 18px; }

/* Full Width Card */
.lis-card-full { grid-column: 1 / -1; }

/* Payments Table */
.lis-payments-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.lis-payments-table thead th {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: #fff; font-size: 11px; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.5px; padding: 14px 16px; text-align: left;
}
.lis-payments-table thead th:first-child { border-radius: 10px 0 0 0; }
.lis-payments-table thead th:last-child { border-radius: 0 10px 0 0; }
.lis-payments-table tbody td {
    padding: 16px; font-size: 14px; color: #374151;
    border-bottom: 1px solid #f1f5f9; background: #fff;
}
.lis-payments-table tbody tr:hover td { background: #faf5ff; }
.lis-payments-table tbody tr:last-child td { border-bottom: none; }
.lis-payments-table tbody tr:last-child td:first-child { border-radius: 0 0 0 10px; }
.lis-payments-table tbody tr:last-child td:last-child { border-radius: 0 0 10px 0; }

.lis-payment-amount { font-family: 'SF Mono', Monaco, monospace; font-weight: 600; color: #10b981; }
.lis-payment-date { color: #6b7280; }
.lis-payment-method {
    display: inline-block; padding: 4px 10px; border-radius: 6px;
    font-size: 11px; font-weight: 600; text-transform: uppercase;
    background: #f1f5f9; color: #64748b;
}

/* Empty State */
.lis-empty {
    text-align: center; padding: 40px 20px;
}
.lis-empty-icon {
    width: 70px; height: 70px; border-radius: 50%; background: #f5f3ff;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; color: #8b5cf6; margin: 0 auto 16px;
}
.lis-empty h4 { font-size: 16px; font-weight: 600; color: #1e1b4b; margin: 0 0 8px 0; }
.lis-empty p { font-size: 13px; color: #6b7280; margin: 0; }

/* Notes Card */
.lis-notes { font-size: 14px; color: #374151; line-height: 1.7; }
.lis-notes:empty::after { content: 'No description provided.'; color: #9ca3af; font-style: italic; }

/* Progress Bar */
.lis-progress-container { margin-top: 20px; }
.lis-progress-header { display: flex; justify-content: space-between; margin-bottom: 8px; }
.lis-progress-label { font-size: 12px; color: #6b7280; }
.lis-progress-percent { font-size: 12px; font-weight: 600; color: #7c3aed; }
.lis-progress-bar { height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; }
.lis-progress-fill { height: 100%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 4px; transition: width 0.5s ease; }

@keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
.lis-card { animation: fadeInUp 0.4s ease forwards; }

/* ========================================
   PRINT STYLES - Compact View
   ======================================== */
@media print {
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    html, body {
        height: auto !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }
    
    .lis-page {
        min-height: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }
    
    /* Hide action buttons */
    .lis-header-actions {
        display: none !important;
    }
    
    /* Simplify header for print */
    .lis-header {
        background: #fff !important;
        box-shadow: none !important;
        border: 2px solid #7c3aed !important;
        border-radius: 8px !important;
        padding: 16px 20px !important;
        margin-bottom: 16px !important;
        page-break-inside: avoid !important;
    }
    .lis-header::before {
        display: none !important;
    }
    .lis-header h1 {
        color: #1e1b4b !important;
        font-size: 20px !important;
    }
    .lis-header h1 i {
        color: #7c3aed !important;
    }
    .lis-header .subtitle {
        color: #6b7280 !important;
    }
    .lis-status-badge {
        border: 1px solid currentColor !important;
        margin-top: 8px !important;
    }
    
    /* Compact grid for print */
    .lis-grid {
        display: block !important;
        gap: 0 !important;
    }
    
    /* Compact cards */
    .lis-card {
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 6px !important;
        padding: 12px 16px !important;
        margin-bottom: 12px !important;
        page-break-inside: avoid !important;
        animation: none !important;
    }
    
    .lis-card-header {
        margin-bottom: 10px !important;
        padding-bottom: 8px !important;
    }
    
    .lis-card-icon {
        width: 32px !important;
        height: 32px !important;
        font-size: 14px !important;
    }
    
    .lis-card-title {
        font-size: 13px !important;
    }
    
    .lis-info-row {
        padding: 6px 0 !important;
    }
    
    .lis-info-label {
        font-size: 11px !important;
    }
    
    .lis-info-value {
        font-size: 12px !important;
    }
    
    .lis-info-value.balance {
        font-size: 14px !important;
    }
    
    /* Progress bar */
    .lis-progress-container {
        margin-top: 10px !important;
    }
    
    /* Notes section */
    .lis-notes {
        font-size: 12px !important;
    }
    
    /* Compact table */
    .lis-payments-table thead th {
        background: #f3f4f6 !important;
        color: #374151 !important;
        font-size: 10px !important;
        padding: 8px 10px !important;
    }
    
    .lis-payments-table tbody td {
        font-size: 11px !important;
        padding: 8px 10px !important;
    }
    
    .lis-payment-method {
        font-size: 9px !important;
        padding: 2px 6px !important;
    }
    
    /* Empty state compact */
    .lis-empty {
        padding: 20px 10px !important;
    }
    .lis-empty-icon {
        width: 40px !important;
        height: 40px !important;
        font-size: 18px !important;
    }
    .lis-empty h4 {
        font-size: 13px !important;
    }
    .lis-empty p {
        font-size: 11px !important;
    }
    
    /* Full width cards in print */
    .lis-card-full {
        grid-column: auto !important;
    }
    
    /* Print footer with date */
    .lis-page::after {
        content: "Printed on: " attr(data-print-date);
        display: block;
        text-align: center;
        font-size: 10px;
        color: #9ca3af;
        margin-top: 20px;
        padding-top: 10px;
        border-top: 1px solid #e5e7eb;
    }
}
</style>
@endsection

@section('content')
@php
    $status = $liability->isOverdue() && $liability->status === 'active' ? 'overdue' : $liability->status;
    $statusLabel = ucfirst(str_replace('_', ' ', $status));
    $paidPercent = $liability->original_amount > 0 ? round(($liability->total_paid / $liability->original_amount) * 100) : 0;
    $liabilityTypes = \App\Models\BusinessLiability::getLiabilityTypes();
    $typeLabel = $liabilityTypes[$liability->liability_type] ?? ucfirst(str_replace('_', ' ', $liability->liability_type));
@endphp

<section class="content lis-page">
    <!-- Header -->
    <div class="lis-header">
        <div class="lis-header-left">
            <h1><i class="fas fa-file-invoice"></i> {{ $liability->name }}</h1>
            <p class="subtitle">{{ $typeLabel }}</p>
            <span class="lis-status-badge {{ $status }}">{{ $statusLabel }}</span>
        </div>
        <div class="lis-header-actions">
            <a href="{{ route('bookkeeping.liabilities.index') }}" class="lis-btn lis-btn-back">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <button onclick="window.print()" class="lis-btn lis-btn-print">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="{{ route('bookkeeping.liabilities.edit', $liability->id) }}" class="lis-btn lis-btn-edit">
                <i class="fas fa-edit"></i> Edit
            </a>
            @if($liability->status === 'active')
            <a href="{{ route('bookkeeping.liabilities.payment.create', $liability->id) }}" class="lis-btn lis-btn-pay">
                <i class="fas fa-dollar-sign"></i> Make Payment
            </a>
            @endif
        </div>
    </div>

    <!-- Info Cards -->
    <div class="lis-grid">
        <!-- Financial Summary -->
        <div class="lis-card">
            <div class="lis-card-header">
                <div class="lis-card-icon purple"><i class="fas fa-chart-pie"></i></div>
                <h3 class="lis-card-title">Financial Summary</h3>
            </div>
            <div class="lis-info-row">
                <span class="lis-info-label">Original Amount</span>
                <span class="lis-info-value amount">${{ number_format($liability->original_amount, 2) }}</span>
            </div>
            <div class="lis-info-row">
                <span class="lis-info-label">Total Paid</span>
                <span class="lis-info-value" style="color: #10b981;">${{ number_format($liability->total_paid, 2) }}</span>
            </div>
            <div class="lis-info-row">
                <span class="lis-info-label">Current Balance</span>
                <span class="lis-info-value balance">${{ number_format($liability->current_balance, 2) }}</span>
            </div>
            <div class="lis-progress-container">
                <div class="lis-progress-header">
                    <span class="lis-progress-label">Payment Progress</span>
                    <span class="lis-progress-percent">{{ $paidPercent }}%</span>
                </div>
                <div class="lis-progress-bar">
                    <div class="lis-progress-fill" style="width: {{ $paidPercent }}%"></div>
                </div>
            </div>
        </div>

        <!-- Liability Details -->
        <div class="lis-card">
            <div class="lis-card-header">
                <div class="lis-card-icon blue"><i class="fas fa-info-circle"></i></div>
                <h3 class="lis-card-title">Liability Details</h3>
            </div>
            <div class="lis-info-row">
                <span class="lis-info-label">Type</span>
                <span class="lis-info-value">{{ $typeLabel }}</span>
            </div>
            <div class="lis-info-row">
                <span class="lis-info-label">Linked Account</span>
                <span class="lis-info-value">{{ $liability->liabilityAccount->name ?? '—' }}</span>
            </div>
            <div class="lis-info-row">
                <span class="lis-info-label">Contact/Creditor</span>
                <span class="lis-info-value">{{ $liability->contact->name ?? '—' }}</span>
            </div>
            <div class="lis-info-row">
                <span class="lis-info-label">Reference #</span>
                <span class="lis-info-value">{{ $liability->reference_number ?? '—' }}</span>
            </div>
        </div>

        <!-- Dates & Schedule -->
        <div class="lis-card">
            <div class="lis-card-header">
                <div class="lis-card-icon orange"><i class="fas fa-calendar-alt"></i></div>
                <h3 class="lis-card-title">Dates & Schedule</h3>
            </div>
            <div class="lis-info-row">
                <span class="lis-info-label">Start Date</span>
                <span class="lis-info-value">{{ $liability->start_date ? $liability->start_date->format('m/d/Y') : '—' }}</span>
            </div>
            <div class="lis-info-row">
                <span class="lis-info-label">Due Date</span>
                <span class="lis-info-value" style="{{ $liability->isOverdue() ? 'color: #dc2626;' : '' }}">
                    {{ $liability->due_date ? $liability->due_date->format('m/d/Y') : '—' }}
                    @if($liability->isOverdue()) <i class="fas fa-exclamation-circle" style="color: #dc2626;"></i> @endif
                </span>
            </div>
            <div class="lis-info-row">
                <span class="lis-info-label">Payment Frequency</span>
                <span class="lis-info-value">{{ ucfirst(str_replace('_', ' ', $liability->payment_frequency ?? 'one_time')) }}</span>
            </div>
            <div class="lis-info-row">
                <span class="lis-info-label">Created</span>
                <span class="lis-info-value">{{ $liability->created_at->format('m/d/Y') }}</span>
            </div>
        </div>

        <!-- Description -->
        <div class="lis-card lis-card-full">
            <div class="lis-card-header">
                <div class="lis-card-icon green"><i class="fas fa-sticky-note"></i></div>
                <h3 class="lis-card-title">Description / Notes</h3>
            </div>
            <div class="lis-notes">{{ $liability->description ?? '' }}</div>
        </div>

        <!-- Payment History -->
        <div class="lis-card lis-card-full">
            <div class="lis-card-header">
                <div class="lis-card-icon purple"><i class="fas fa-history"></i></div>
                <h3 class="lis-card-title">Payment History</h3>
            </div>
            @if($liability->payments->count() > 0)
            <table class="lis-payments-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>From Account</th>
                        <th>Reference</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($liability->payments as $payment)
                    <tr>
                        <td class="lis-payment-date">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('m/d/Y') : '—' }}</td>
                        <td class="lis-payment-amount">${{ number_format($payment->total_amount, 2) }}</td>
                        <td><span class="lis-payment-method">{{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'cash')) }}</span></td>
                        <td>{{ $payment->fromAccount->name ?? '—' }}</td>
                        <td>{{ $payment->reference_number ?? '—' }}</td>
                        <td>{{ $payment->notes ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="lis-empty">
                <div class="lis-empty-icon"><i class="fas fa-receipt"></i></div>
                <h4>No Payments Yet</h4>
                <p>Record a payment to track your progress</p>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Any additional JavaScript can go here
});
</script>
@endsection







