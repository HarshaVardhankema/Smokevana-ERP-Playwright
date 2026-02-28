@extends('layouts.app')
@section('title', 'Edit Partner Transaction')

@section('css')
<style>
    /* ===== Page Container ===== */
    .partner-edit-wrapper {
        padding: 0;
        background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
        min-height: calc(100vh - 60px);
    }

    /* ===== Header Banner ===== */
    .partner-header-banner {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
        padding: 32px 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .partner-header-banner::before {
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
    }

    .header-title-area h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .header-title-area h1 i {
        font-size: 32px;
        opacity: 0.9;
    }

    .header-subtitle {
        font-size: 15px;
        opacity: 0.85;
        font-weight: 400;
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
    }

    .btn-header-secondary {
        background: rgba(255,255,255,0.15);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.25);
    }

    .btn-header-secondary:hover {
        background: rgba(255,255,255,0.25);
        color: #fff;
        transform: translateY(-2px);
    }

    /* ===== Content Area ===== */
    .content-area {
        padding: 30px 40px;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* ===== Form Container ===== */
    .form-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    /* ===== Form Cards ===== */
    .form-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid rgba(139, 92, 246, 0.1);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .form-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 14px;
        background: linear-gradient(135deg, #faf5ff 0%, #f5f3ff 100%);
    }

    .form-card-header-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .form-card-header-icon.purple {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: #fff;
    }

    .form-card-header-icon.blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
    }

    .form-card-header-icon.amber {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
    }

    .form-card-header h3 {
        font-size: 17px;
        font-weight: 700;
        color: #1e1b4b;
        margin: 0;
    }

    .form-card-header p {
        font-size: 13px;
        color: #6b7280;
        margin: 2px 0 0 0;
    }

    .form-card-body {
        padding: 24px;
    }

    /* ===== Transaction Type Selection ===== */
    .transaction-type-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .transaction-type-card {
        position: relative;
        padding: 16px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .transaction-type-card:hover {
        border-color: #8b5cf6;
        background: #faf5ff;
    }

    .transaction-type-card.selected {
        border-color: #8b5cf6;
        background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
    }

    .transaction-type-card input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .type-icon-wrapper {
        width: 40px;
        height: 40px;
        margin: 0 auto 10px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .transaction-type-card.capital .type-icon-wrapper {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #059669;
    }

    .transaction-type-card.drawing .type-icon-wrapper {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #dc2626;
    }

    .transaction-type-card.loan-in .type-icon-wrapper {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #2563eb;
    }

    .transaction-type-card.loan-out .type-icon-wrapper {
        background: linear-gradient(135deg, #fed7aa, #fdba74);
        color: #ea580c;
    }

    .transaction-type-card.repayment .type-icon-wrapper {
        background: linear-gradient(135deg, #cffafe, #a5f3fc);
        color: #0891b2;
    }

    .transaction-type-card.advance .type-icon-wrapper {
        background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
        color: #7c3aed;
    }

    .type-label {
        font-size: 13px;
        font-weight: 600;
        color: #1e1b4b;
        margin-bottom: 4px;
    }

    .type-description {
        font-size: 11px;
        color: #6b7280;
    }

    /* ===== Form Fields ===== */
    .field-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .field-row:last-child {
        margin-bottom: 0;
    }

    .form-group-custom {
        margin-bottom: 0;
    }

    .form-group-custom label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .form-group-custom label .required {
        color: #dc2626;
    }

    .form-group-custom .form-control {
        width: 100%;
        padding: 12px 14px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-group-custom .form-control:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        outline: none;
    }

    .form-group-custom .input-group {
        display: flex;
    }

    .form-group-custom .input-group-addon {
        padding: 12px 14px;
        background: #f3f4f6;
        border: 2px solid #e5e7eb;
        border-right: none;
        border-radius: 10px 0 0 10px;
        color: #6b7280;
    }

    .form-group-custom .input-group .form-control {
        border-radius: 0 10px 10px 0;
    }

    .form-group-custom textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .field-help {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 6px;
    }

    .field-help i {
        margin-right: 4px;
    }

    /* ===== Select2 Styling ===== */
    .select2-container--default .select2-selection--single {
        height: 46px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 8px 14px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        padding-left: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
    }

    /* ===== Sidebar Summary ===== */
    .sidebar-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid rgba(139, 92, 246, 0.1);
        overflow: hidden;
        position: sticky;
        top: 20px;
    }

    .sidebar-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: #fff;
    }

    .sidebar-header h4 {
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar-header p {
        font-size: 13px;
        opacity: 0.85;
        margin: 0;
    }

    .sidebar-body {
        padding: 24px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-item-label {
        font-size: 13px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .summary-item-label i {
        color: #8b5cf6;
        width: 16px;
    }

    .summary-item-value {
        font-size: 14px;
        font-weight: 600;
        color: #1e1b4b;
    }

    .summary-item-value.amount {
        font-size: 20px;
        color: #8b5cf6;
    }

    /* ===== Form Actions ===== */
    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 20px;
    }

    .btn-cancel {
        padding: 14px 28px;
        background: #f3f4f6;
        color: #6b7280;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-save {
        padding: 14px 28px;
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);
    }

    .btn-save:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* ===== Responsive ===== */
    @media (max-width: 1200px) {
        .form-container {
            grid-template-columns: 1fr;
        }
        .sidebar-card {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .partner-header-banner {
            padding: 24px 20px;
        }
        .header-content {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }
        .content-area {
            padding: 20px;
        }
        .transaction-type-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .field-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="partner-edit-wrapper">
    <!-- Header Banner -->
    <div class="partner-header-banner">
        <div class="header-content">
            <div class="header-title-area">
                <h1>
                    <i class="fas fa-edit"></i>
                    Edit Transaction #{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}
                </h1>
                <p class="header-subtitle">Modify partner transaction details</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('bookkeeping.partner.index') }}" class="btn-header btn-header-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Transactions
                </a>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="content-area">
        {!! Form::model($transaction, ['route' => ['bookkeeping.partner.update', $transaction->id], 'method' => 'PUT', 'id' => 'partner_transaction_form']) !!}

        <div class="form-container">
            <!-- Left Column - Form -->
            <div class="form-main-column">
                <!-- Transaction Type Card -->
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-header-icon purple">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div>
                            <h3>Transaction Type</h3>
                            <p>Select the type of partner transaction</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="transaction-type-grid">
                            <label class="transaction-type-card capital {{ $transaction->transaction_type == 'capital_contribution' ? 'selected' : '' }}">
                                <input type="radio" name="transaction_type" value="capital_contribution" {{ $transaction->transaction_type == 'capital_contribution' ? 'checked' : '' }}>
                                <div class="type-icon-wrapper">
                                    <i class="fas fa-plus-circle"></i>
                                </div>
                                <div class="type-label">Capital Contribution</div>
                                <div class="type-description">Investment into business</div>
                            </label>
                            <label class="transaction-type-card drawing {{ $transaction->transaction_type == 'owner_drawing' ? 'selected' : '' }}">
                                <input type="radio" name="transaction_type" value="owner_drawing" {{ $transaction->transaction_type == 'owner_drawing' ? 'checked' : '' }}>
                                <div class="type-icon-wrapper">
                                    <i class="fas fa-minus-circle"></i>
                                </div>
                                <div class="type-label">Owner Drawing</div>
                                <div class="type-description">Withdrawal for personal use</div>
                            </label>
                            <label class="transaction-type-card loan-in {{ $transaction->transaction_type == 'loan_from_partner' ? 'selected' : '' }}">
                                <input type="radio" name="transaction_type" value="loan_from_partner" {{ $transaction->transaction_type == 'loan_from_partner' ? 'checked' : '' }}>
                                <div class="type-icon-wrapper">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                                <div class="type-label">Loan from Partner</div>
                                <div class="type-description">Borrowed from partner</div>
                            </label>
                            <label class="transaction-type-card loan-out {{ $transaction->transaction_type == 'loan_to_partner' ? 'selected' : '' }}">
                                <input type="radio" name="transaction_type" value="loan_to_partner" {{ $transaction->transaction_type == 'loan_to_partner' ? 'checked' : '' }}>
                                <div class="type-icon-wrapper">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                                <div class="type-label">Loan to Partner</div>
                                <div class="type-description">Lent to partner</div>
                            </label>
                            <label class="transaction-type-card repayment {{ $transaction->transaction_type == 'loan_repayment' ? 'selected' : '' }}">
                                <input type="radio" name="transaction_type" value="loan_repayment" {{ $transaction->transaction_type == 'loan_repayment' ? 'checked' : '' }}>
                                <div class="type-icon-wrapper">
                                    <i class="fas fa-undo"></i>
                                </div>
                                <div class="type-label">Loan Repayment</div>
                                <div class="type-description">Repay partner loan</div>
                            </label>
                            <label class="transaction-type-card advance {{ $transaction->transaction_type == 'advance' ? 'selected' : '' }}">
                                <input type="radio" name="transaction_type" value="advance" {{ $transaction->transaction_type == 'advance' ? 'checked' : '' }}>
                                <div class="type-icon-wrapper">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div class="type-label">Partner Advance</div>
                                <div class="type-description">Advance payment</div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Partner & Details Card -->
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-header-icon blue">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <h3>Partner & Transaction Details</h3>
                            <p>Select the partner and enter transaction details</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="field-row">
                            <div class="form-group-custom">
                                <label>Partner <span class="required">*</span></label>
                                {!! Form::select('partner_id', $partners, $transaction->partner_id, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select partner', 'id' => 'partner_id']) !!}
                                <div class="field-help">
                                    <i class="fas fa-info-circle"></i> The business partner for this transaction
                                </div>
                            </div>
                            <div class="form-group-custom">
                                <label>Transaction Date <span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                                    {!! Form::date('transaction_date', $transaction->transaction_date->format('Y-m-d'), ['class' => 'form-control', 'required', 'id' => 'transaction_date']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="field-row">
                            <div class="form-group-custom">
                                <label>Amount <span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    {!! Form::number('amount', $transaction->amount, ['class' => 'form-control', 'required', 'step' => '0.01', 'min' => '0', 'placeholder' => '0.00', 'id' => 'amount']) !!}
                                </div>
                            </div>
                            <div class="form-group-custom">
                                <label>Corresponding Account <span class="required">*</span></label>
                                {!! Form::select('account_id', $accounts, $transaction->account_id, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select account', 'id' => 'account_id']) !!}
                                <div class="field-help">
                                    <i class="fas fa-info-circle"></i> The cash/bank account for this transaction
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Details Card -->
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-header-icon amber">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <h3>Additional Details</h3>
                            <p>Add reference number and notes</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="form-group-custom" style="margin-bottom: 20px;">
                            <label>Reference Number</label>
                            {!! Form::text('reference', $transaction->reference, ['class' => 'form-control', 'placeholder' => 'Check #, Receipt #, Wire Transfer #, etc.', 'id' => 'reference']) !!}
                            <div class="field-help">
                                <i class="fas fa-info-circle"></i> Optional reference for tracking
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label>Description / Notes</label>
                            {!! Form::textarea('description', $transaction->description, ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Enter any additional details, purpose of transaction, or notes...', 'id' => 'description']) !!}
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="{{ route('bookkeeping.partner.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn-save" id="submit_btn">
                        <i class="fas fa-check-circle"></i> Update Transaction
                    </button>
                </div>
            </div>

            <!-- Right Column - Summary Sidebar -->
            <div class="form-sidebar-column">
                <div class="sidebar-card">
                    <div class="sidebar-header">
                        <h4><i class="fas fa-receipt"></i> Transaction Summary</h4>
                        <p>Review before saving</p>
                    </div>
                    <div class="sidebar-body">
                        <div class="summary-item">
                            <span class="summary-item-label">
                                <i class="fas fa-hashtag"></i> ID
                            </span>
                            <span class="summary-item-value">#{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-item-label">
                                <i class="fas fa-tag"></i> Type
                            </span>
                            <span class="summary-item-value" id="summary_type">
                                @php
                                    $typeLabels = [
                                        'capital_contribution' => 'Capital Contribution',
                                        'owner_drawing' => 'Owner Drawing',
                                        'loan_from_partner' => 'Loan from Partner',
                                        'loan_to_partner' => 'Loan to Partner',
                                        'loan_repayment' => 'Loan Repayment',
                                        'advance' => 'Partner Advance',
                                    ];
                                @endphp
                                {{ $typeLabels[$transaction->transaction_type] ?? '-' }}
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-item-label">
                                <i class="fas fa-user"></i> Partner
                            </span>
                            <span class="summary-item-value" id="summary_partner">
                                {{ $transaction->partner ? $transaction->partner->first_name . ' ' . $transaction->partner->last_name : '-' }}
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-item-label">
                                <i class="fas fa-calendar"></i> Date
                            </span>
                            <span class="summary-item-value" id="summary_date">{{ $transaction->transaction_date->format('M d, Y') }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-item-label">
                                <i class="fas fa-dollar-sign"></i> Amount
                            </span>
                            <span class="summary-item-value amount" id="summary_amount">${{ number_format($transaction->amount, 2) }}</span>
                        </div>

                        @if($transaction->journal_entry_id)
                        <div class="summary-item">
                            <span class="summary-item-label">
                                <i class="fas fa-book"></i> Journal Entry
                            </span>
                            <span class="summary-item-value">
                                <a href="{{ route('bookkeeping.journal.show', $transaction->journal_entry_id) }}" target="_blank" style="color: #8b5cf6;">
                                    #{{ $transaction->journal_entry_id }}
                                </a>
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });

    // Transaction type card selection
    $('.transaction-type-card').on('click', function() {
        $('.transaction-type-card').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input[type="radio"]').prop('checked', true);
        updateSummary();
    });

    // Real-time summary updates
    $('#partner_id').on('change', updateSummary);
    $('#amount').on('input', updateSummary);
    $('#transaction_date').on('change', updateSummary);

    function updateSummary() {
        // Type
        var typeLabels = {
            'capital_contribution': 'Capital Contribution',
            'owner_drawing': 'Owner Drawing',
            'loan_from_partner': 'Loan from Partner',
            'loan_to_partner': 'Loan to Partner',
            'loan_repayment': 'Loan Repayment',
            'advance': 'Partner Advance'
        };
        var selectedType = $('input[name="transaction_type"]:checked').val();
        $('#summary_type').text(typeLabels[selectedType] || '-');

        // Partner
        var partnerText = $('#partner_id option:selected').text();
        $('#summary_partner').text(partnerText || '-');

        // Date
        var dateVal = $('#transaction_date').val();
        if (dateVal) {
            var date = new Date(dateVal);
            var options = { year: 'numeric', month: 'short', day: 'numeric' };
            $('#summary_date').text(date.toLocaleDateString('en-US', options));
        }

        // Amount
        var amount = parseFloat($('#amount').val()) || 0;
        var formattedAmount = '$' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        $('#summary_amount').text(formattedAmount);
    }

    // Form submission
    $('#partner_transaction_form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var submitBtn = $('#submit_btn');

        // Validation
        if (!$('input[name="transaction_type"]:checked').val()) {
            toastr.error('Please select a transaction type');
            return;
        }

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg || 'Transaction updated successfully');
                    window.location.href = '{{ route("bookkeeping.partner.index") }}';
                } else {
                    toastr.error(response.msg || 'An error occurred');
                    submitBtn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Update Transaction');
                }
            },
            error: function(xhr) {
                var errorMsg = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = xhr.responseJSON.msg;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    errorMsg = Object.values(errors).flat().join('<br>');
                }
                toastr.error(errorMsg);
                submitBtn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Update Transaction');
            }
        });
    });
});
</script>
@endsection





