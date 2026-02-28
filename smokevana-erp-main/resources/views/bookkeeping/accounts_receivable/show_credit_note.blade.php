@extends('layouts.app')
@section('title', 'Credit Note - ' . $creditNote->credit_note_number)

@section('css')
<style>
/* Credit Note View - Professional UI */
.cn-page {
    background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
    min-height: 100vh;
    padding: 20px;
    padding-bottom: 60px;
}

/* Header Banner */
.cn-header-banner {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #f87171 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 8px 30px rgba(220, 38, 38, 0.25);
    position: relative;
    overflow: hidden;
}

.cn-header-banner h1,
.cn-header-banner .subtitle,
.cn-header-banner i { color: #fff !important; }

.cn-header-banner h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.cn-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }

.cn-header-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.cn-btn {
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.cn-btn-light {
    background: rgba(255,255,255,0.95);
    color: #dc2626;
}

.cn-btn-light:hover {
    background: #fff;
    transform: translateY(-2px);
    color: #dc2626;
    text-decoration: none;
}

.cn-btn-outline {
    background: rgba(255,255,255,0.15);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.3);
}

.cn-btn-outline:hover {
    background: rgba(255,255,255,0.25);
    color: #fff;
    text-decoration: none;
}

/* Main Layout */
.cn-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 24px;
}

@media (max-width: 1200px) {
    .cn-grid { grid-template-columns: 1fr; }
}

/* Card Styling */
.cn-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    overflow: hidden;
    border: 1px solid rgba(220, 38, 38, 0.08);
    margin-bottom: 24px;
}

.cn-card-header {
    padding: 18px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cn-card-header.red {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
}

.cn-card-header.green {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
}

.cn-card-header h3 {
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.cn-card-body { padding: 24px; }

/* Credit Note Header Card */
.cn-main-info {
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 2px solid #f1f5f9;
}

.cn-main-icon {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 32px;
    flex-shrink: 0;
}

.cn-main-details { flex: 1; }

.cn-main-number {
    font-size: 28px;
    font-weight: 700;
    color: #dc2626;
    font-family: 'SF Mono', Monaco, monospace;
}

.cn-main-date {
    font-size: 14px;
    color: #6b7280;
    margin-top: 4px;
}

.cn-main-status {
    text-align: right;
}

.cn-status-badge {
    display: inline-flex;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.cn-status-badge.draft { background: #f3f4f6; color: #6b7280; }
.cn-status-badge.approved { background: #dbeafe; color: #2563eb; }
.cn-status-badge.applied { background: #d1fae5; color: #059669; }
.cn-status-badge.partially_applied { background: #fef3c7; color: #d97706; }
.cn-status-badge.voided { background: #fee2e2; color: #dc2626; text-decoration: line-through; }
.cn-status-badge.cancelled { background: #f3f4f6; color: #9ca3af; }

/* Amount Display */
.cn-amount-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

@media (max-width: 768px) {
    .cn-amount-grid { grid-template-columns: 1fr; }
}

.cn-amount-box {
    background: #f8f9fe;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    border: 2px solid #e5e7eb;
}

.cn-amount-box.total { border-color: #dc2626; background: #fef2f2; }
.cn-amount-box.applied { border-color: #10b981; background: #f0fdf4; }
.cn-amount-box.balance { border-color: #3b82f6; background: #eff6ff; }

.cn-amount-label {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.cn-amount-value {
    font-size: 28px;
    font-weight: 700;
    font-family: 'SF Mono', Monaco, monospace;
}

.cn-amount-box.total .cn-amount-value { color: #dc2626; }
.cn-amount-box.applied .cn-amount-value { color: #059669; }
.cn-amount-box.balance .cn-amount-value { color: #2563eb; }

/* Detail Rows */
.cn-detail-row {
    display: flex;
    justify-content: space-between;
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
}

.cn-detail-row:last-child { border-bottom: none; }

.cn-detail-label {
    font-size: 14px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 8px;
}

.cn-detail-label i { color: #dc2626; font-size: 12px; }

.cn-detail-value {
    font-size: 14px;
    font-weight: 600;
    color: #1e1b4b;
    text-align: right;
    max-width: 60%;
}

/* Reason Section */
.cn-reason-section {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
}

.cn-reason-title {
    font-size: 14px;
    font-weight: 700;
    color: #dc2626;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.cn-reason-text {
    font-size: 14px;
    color: #374151;
    line-height: 1.6;
    background: #fff;
    padding: 16px;
    border-radius: 8px;
    border-left: 4px solid #dc2626;
}

/* Action Buttons */
.cn-action-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #f1f5f9;
}

.cn-action-btn {
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.cn-action-btn.approve {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: #fff;
}

.cn-action-btn.approve:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

.cn-action-btn.void {
    background: #fee2e2;
    color: #dc2626;
    border: 2px solid #dc2626;
}

.cn-action-btn.void:hover {
    background: #dc2626;
    color: #fff;
}

.cn-action-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none !important;
}

/* Apply Credit Section */
.cn-apply-section {
    margin-top: 24px;
}

.cn-invoice-select-wrapper {
    margin-bottom: 16px;
}

.cn-invoice-item {
    background: #f8f9fe;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.cn-invoice-item:hover {
    border-color: #dc2626;
    background: #fef2f2;
}

.cn-invoice-item.selected {
    border-color: #dc2626;
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
}

.cn-invoice-info {
    display: flex;
    flex-direction: column;
}

.cn-invoice-number {
    font-weight: 700;
    color: #1e1b4b;
    font-family: 'SF Mono', Monaco, monospace;
}

.cn-invoice-date {
    font-size: 12px;
    color: #6b7280;
}

.cn-invoice-balance {
    font-weight: 700;
    color: #dc2626;
    font-family: 'SF Mono', Monaco, monospace;
    font-size: 16px;
}

.cn-apply-form {
    background: #f8f9fe;
    border-radius: 12px;
    padding: 20px;
    display: none;
}

.cn-apply-form.active { display: block; }

.cn-apply-row {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 16px;
    align-items: end;
}

@media (max-width: 768px) {
    .cn-apply-row { grid-template-columns: 1fr; }
}

.cn-apply-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
}

.cn-apply-input:focus {
    border-color: #dc2626;
    outline: none;
}

.cn-apply-btn {
    padding: 12px 24px;
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
}

.cn-apply-btn:hover {
    transform: translateY(-2px);
}

/* Applications History */
.cn-applications-list {
    margin-top: 16px;
}

.cn-application-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px;
    background: #f0fdf4;
    border-radius: 10px;
    margin-bottom: 8px;
    border-left: 4px solid #10b981;
}

.cn-application-info {
    display: flex;
    flex-direction: column;
}

.cn-application-invoice {
    font-weight: 600;
    color: #1e1b4b;
}

.cn-application-date {
    font-size: 12px;
    color: #6b7280;
}

.cn-application-amount {
    font-weight: 700;
    color: #059669;
    font-family: 'SF Mono', Monaco, monospace;
}

/* Journal Entry Link */
.cn-journal-link {
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    transition: all 0.3s ease;
}

.cn-journal-link:hover {
    transform: translateX(4px);
}

.cn-journal-link-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.cn-journal-link-icon {
    width: 40px;
    height: 40px;
    background: #7c3aed;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
}

.cn-journal-link-text {
    font-weight: 600;
    color: #1e1b4b;
}

.cn-journal-link-number {
    font-size: 12px;
    color: #6b7280;
    font-family: 'SF Mono', Monaco, monospace;
}

.cn-journal-link a {
    color: #7c3aed;
    font-weight: 600;
    text-decoration: none;
}

.cn-journal-link a:hover {
    text-decoration: underline;
}

/* Customer Sidebar Card */
.cn-sidebar-card {
    position: sticky;
    top: 20px;
}

.cn-customer-card {
    text-align: center;
    padding: 24px;
}

.cn-customer-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 32px;
    margin: 0 auto 16px;
}

.cn-customer-name {
    font-size: 20px;
    font-weight: 700;
    color: #1e1b4b;
    margin-bottom: 8px;
}

.cn-customer-code {
    font-size: 13px;
    color: #6b7280;
    font-family: 'SF Mono', Monaco, monospace;
    margin-bottom: 20px;
}

.cn-customer-stat {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-top: 1px solid #f1f5f9;
}

.cn-customer-stat-label {
    color: #6b7280;
    font-size: 13px;
}

.cn-customer-stat-value {
    font-weight: 600;
    font-family: 'SF Mono', Monaco, monospace;
}

/* Audit Info */
.cn-audit-info {
    font-size: 12px;
    color: #9ca3af;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
    margin-top: 16px;
}

.cn-audit-item {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
}
</style>
@endsection

@section('content')
<section class="content cn-page">
    <!-- Header Banner -->
    <div class="cn-header-banner">
        <div>
            <h1><i class="fas fa-file-invoice-dollar"></i> {{ $creditNote->credit_note_number }}</h1>
            <p class="subtitle">Credit Note Details</p>
        </div>
        <div class="cn-header-actions">
            <a href="{{ route('bookkeeping.credit-notes.index') }}" class="cn-btn cn-btn-outline">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @if($creditNote->status == 'draft')
            <button type="button" class="cn-btn cn-btn-light" onclick="approveCreditNote()">
                <i class="fas fa-check"></i> Approve
            </button>
            @endif
        </div>
    </div>

    <!-- Main Layout -->
    <div class="cn-grid">
        <!-- Left Column - Details -->
        <div>
            <!-- Main Info Card -->
            <div class="cn-card">
                <div class="cn-card-header red">
                    <h3><i class="fas fa-info-circle"></i> Credit Note Information</h3>
                </div>
                <div class="cn-card-body">
                    <!-- Header Info -->
                    <div class="cn-main-info">
                        <div class="cn-main-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="cn-main-details">
                            <div class="cn-main-number">{{ $creditNote->credit_note_number }}</div>
                            <div class="cn-main-date">
                                <i class="fas fa-calendar-alt"></i> 
                                {{ $creditNote->credit_date->format('F d, Y') }}
                            </div>
                        </div>
                        <div class="cn-main-status">
                            <span class="cn-status-badge {{ $creditNote->status }}">
                                {{ $creditNote->getStatusLabel() }}
                            </span>
                        </div>
                    </div>

                    <!-- Amount Grid -->
                    <div class="cn-amount-grid">
                        <div class="cn-amount-box total">
                            <div class="cn-amount-label">Total Amount</div>
                            <div class="cn-amount-value">${{ number_format($creditNote->amount, 2) }}</div>
                        </div>
                        <div class="cn-amount-box applied">
                            <div class="cn-amount-label">Applied</div>
                            <div class="cn-amount-value">${{ number_format($creditNote->amount_applied, 2) }}</div>
                        </div>
                        <div class="cn-amount-box balance">
                            <div class="cn-amount-label">Balance</div>
                            <div class="cn-amount-value">${{ number_format($creditNote->balance, 2) }}</div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="cn-detail-row">
                        <span class="cn-detail-label"><i class="fas fa-tag"></i> Reason Category</span>
                        <span class="cn-detail-value">{{ $creditNote->getReasonCategoryLabel() }}</span>
                    </div>

                    @if($creditNote->reference_type)
                    <div class="cn-detail-row">
                        <span class="cn-detail-label"><i class="fas fa-link"></i> Reference Type</span>
                        <span class="cn-detail-value">{{ ucfirst($creditNote->reference_type) }}</span>
                    </div>
                    @endif

                    @if($creditNote->reference_number)
                    <div class="cn-detail-row">
                        <span class="cn-detail-label"><i class="fas fa-hashtag"></i> Reference Number</span>
                        <span class="cn-detail-value" style="font-family: 'SF Mono', Monaco, monospace;">{{ $creditNote->reference_number }}</span>
                    </div>
                    @endif

                    <!-- Reason Description -->
                    <div class="cn-reason-section">
                        <div class="cn-reason-title">
                            <i class="fas fa-align-left"></i> Reason for Credit
                        </div>
                        <div class="cn-reason-text">
                            {{ $creditNote->reason_description }}
                        </div>
                    </div>

                    @if($creditNote->internal_notes)
                    <div class="cn-reason-section" style="background: #f8f9fe; margin-top: 16px;">
                        <div class="cn-reason-title" style="color: #6b7280;">
                            <i class="fas fa-sticky-note"></i> Internal Notes
                        </div>
                        <div class="cn-reason-text" style="border-left-color: #6b7280;">
                            {{ $creditNote->internal_notes }}
                        </div>
                    </div>
                    @endif

                    <!-- Journal Entry Link -->
                    @if($creditNote->journalEntry)
                    <div class="cn-journal-link">
                        <div class="cn-journal-link-info">
                            <div class="cn-journal-link-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div>
                                <div class="cn-journal-link-text">Journal Entry</div>
                                <div class="cn-journal-link-number">{{ $creditNote->journalEntry->entry_number }}</div>
                            </div>
                        </div>
                        <a href="{{ route('bookkeeping.journal.show', $creditNote->journalEntry->id) }}">
                            View Entry <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="cn-action-buttons">
                        @if($creditNote->status == 'draft')
                        <button type="button" class="cn-action-btn approve" onclick="approveCreditNote()">
                            <i class="fas fa-check-circle"></i> Approve Credit Note
                        </button>
                        <button type="button" class="cn-action-btn void" onclick="voidCreditNote()">
                            <i class="fas fa-ban"></i> Void
                        </button>
                        @endif
                        
                        @if($creditNote->status == 'approved' || $creditNote->status == 'partially_applied')
                        <button type="button" class="cn-action-btn void" onclick="voidCreditNote()" {{ $creditNote->amount_applied > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-ban"></i> Void
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Apply Credit Section -->
            @if(in_array($creditNote->status, ['approved', 'partially_applied']) && $creditNote->balance > 0)
            <div class="cn-card cn-apply-section">
                <div class="cn-card-header green">
                    <h3><i class="fas fa-hand-holding-usd"></i> Apply Credit to Invoices</h3>
                </div>
                <div class="cn-card-body">
                    @if(count($outstandingInvoices) > 0)
                    <p style="color: #6b7280; margin-bottom: 16px;">Select an invoice to apply this credit:</p>
                    
                    <div class="cn-invoice-select-wrapper">
                        @foreach($outstandingInvoices as $invoice)
                        <div class="cn-invoice-item" 
                             data-id="{{ $invoice->id }}"
                             data-invoice="{{ $invoice->invoice_no }}"
                             data-balance="{{ $invoice->balance }}"
                             onclick="selectInvoice(this)">
                            <div class="cn-invoice-info">
                                <span class="cn-invoice-number">{{ $invoice->invoice_no }}</span>
                                <span class="cn-invoice-date">{{ \Carbon\Carbon::parse($invoice->transaction_date)->format('M d, Y') }}</span>
                            </div>
                            <span class="cn-invoice-balance">${{ number_format($invoice->balance, 2) }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="cn-apply-form" id="applyForm">
                        <form id="applyCreditsForm">
                            @csrf
                            <input type="hidden" name="transaction_id" id="apply_transaction_id">
                            <div class="cn-apply-row">
                                <div>
                                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                                        Selected Invoice
                                    </label>
                                    <input type="text" class="cn-apply-input" id="apply_invoice_display" readonly>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                                        Amount to Apply (Max: ${{ number_format($creditNote->balance, 2) }})
                                    </label>
                                    <input type="number" class="cn-apply-input" name="amount" id="apply_amount" step="0.01" min="0.01" max="{{ $creditNote->balance }}" required>
                                </div>
                                <button type="submit" class="cn-apply-btn">
                                    <i class="fas fa-check"></i> Apply
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div style="text-align: center; padding: 40px; color: #6b7280;">
                        <i class="fas fa-check-circle" style="font-size: 48px; color: #10b981; margin-bottom: 16px;"></i>
                        <p>No outstanding invoices for this customer.</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Applications History -->
            @if($creditNote->applications->count() > 0)
            <div class="cn-card">
                <div class="cn-card-header">
                    <h3><i class="fas fa-history"></i> Application History</h3>
                </div>
                <div class="cn-card-body">
                    <div class="cn-applications-list">
                        @foreach($creditNote->applications as $application)
                        <div class="cn-application-item">
                            <div class="cn-application-info">
                                <span class="cn-application-invoice">
                                    {{ $application->transaction->invoice_no ?? 'N/A' }}
                                </span>
                                <span class="cn-application-date">
                                    Applied on {{ $application->application_date->format('M d, Y') }}
                                    by {{ $application->appliedBy->first_name ?? '' }} {{ $application->appliedBy->last_name ?? '' }}
                                </span>
                            </div>
                            <span class="cn-application-amount">${{ number_format($application->amount_applied, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Customer Info -->
        <div class="cn-sidebar-card">
            <div class="cn-card">
                <div class="cn-card-header red">
                    <h3><i class="fas fa-user"></i> Customer</h3>
                </div>
                <div class="cn-card-body cn-customer-card">
                    <div class="cn-customer-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="cn-customer-name">{{ $creditNote->getCustomerDisplayName() }}</div>
                    @if($creditNote->contact && $creditNote->contact->contact_id)
                    <div class="cn-customer-code">{{ $creditNote->contact->contact_id }}</div>
                    @endif

                    @if($creditNote->contact)
                    @if($creditNote->contact->email)
                    <div class="cn-customer-stat">
                        <span class="cn-customer-stat-label">Email</span>
                        <span class="cn-customer-stat-value" style="font-family: inherit;">{{ $creditNote->contact->email }}</span>
                    </div>
                    @endif
                    @if($creditNote->contact->mobile)
                    <div class="cn-customer-stat">
                        <span class="cn-customer-stat-label">Phone</span>
                        <span class="cn-customer-stat-value">{{ $creditNote->contact->mobile }}</span>
                    </div>
                    @endif
                    @endif

                    <a href="{{ action([\App\Http\Controllers\ContactController::class, 'show'], [$creditNote->contact_id]) }}?type=customer" 
                       style="display: inline-block; margin-top: 16px; color: #dc2626; font-weight: 600; text-decoration: none;">
                        View Customer Profile <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Audit Info Card -->
            <div class="cn-card" style="margin-top: 20px;">
                <div class="cn-card-header">
                    <h3><i class="fas fa-clock"></i> Audit Trail</h3>
                </div>
                <div class="cn-card-body">
                    <div class="cn-audit-info" style="border: none; padding: 0; margin: 0;">
                        <div class="cn-audit-item">
                            <span>Created By</span>
                            <span>{{ $creditNote->createdBy ? $creditNote->createdBy->first_name . ' ' . $creditNote->createdBy->last_name : 'N/A' }}</span>
                        </div>
                        <div class="cn-audit-item">
                            <span>Created At</span>
                            <span>{{ $creditNote->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        @if($creditNote->approved_at)
                        <div class="cn-audit-item">
                            <span>Approved By</span>
                            <span>{{ $creditNote->approvedBy ? $creditNote->approvedBy->first_name . ' ' . $creditNote->approvedBy->last_name : 'N/A' }}</span>
                        </div>
                        <div class="cn-audit-item">
                            <span>Approved At</span>
                            <span>{{ $creditNote->approved_at->format('M d, Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
function approveCreditNote() {
    if (!confirm('Are you sure you want to approve this credit note? This will create a journal entry and reduce the customer\'s balance.')) {
        return;
    }
    
    $.ajax({
        url: "{{ route('bookkeeping.credit-notes.approve', $creditNote->id) }}",
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                toastr.success(response.msg);
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                toastr.error(response.msg);
            }
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.msg || 'An error occurred.');
        }
    });
}

function voidCreditNote() {
    if (!confirm('Are you sure you want to void this credit note? This action cannot be undone.')) {
        return;
    }
    
    $.ajax({
        url: "{{ route('bookkeeping.credit-notes.void', $creditNote->id) }}",
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                toastr.success(response.msg);
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                toastr.error(response.msg);
            }
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.msg || 'An error occurred.');
        }
    });
}

function selectInvoice(element) {
    // Remove selection from all
    $('.cn-invoice-item').removeClass('selected');
    
    // Select this one
    $(element).addClass('selected');
    
    // Show apply form
    $('#applyForm').addClass('active');
    
    // Populate form
    var id = $(element).data('id');
    var invoice = $(element).data('invoice');
    var balance = parseFloat($(element).data('balance'));
    var maxApply = Math.min(balance, {{ $creditNote->balance }});
    
    $('#apply_transaction_id').val(id);
    $('#apply_invoice_display').val(invoice);
    $('#apply_amount').attr('max', maxApply.toFixed(2));
    $('#apply_amount').val(maxApply.toFixed(2));
}

$(document).ready(function() {
    $('#applyCreditsForm').on('submit', function(e) {
        e.preventDefault();
        
        var btn = $(this).find('button[type="submit"]');
        var originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ route('bookkeeping.credit-notes.apply', $creditNote->id) }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    toastr.error(response.msg);
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.msg || 'An error occurred.');
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endsection
