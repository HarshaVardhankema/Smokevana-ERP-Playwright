@extends('layouts.app')
@section('title', 'Add Income')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .income-wrapper {
        padding: 0;
        background: #EAEDED;
        min-height: calc(100vh - 60px);
        width: 100%;
    }

    .page-header-banner {
        background: linear-gradient(180deg, #37475a 0%, #232f3e 100%);
        padding: 24px 32px;
        color: #fff !important;
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    }

    .page-header-banner .header-title-area h1,
    .page-header-banner .header-title-area h1 *,
    .page-header-banner .header-subtitle {
        color: #fff !important;
    }

    .page-header-banner .header-title-area h1 i {
        color: #fff !important;
    }

    .page-header-banner::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ff9900, #e47911);
        z-index: 1;
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
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 6px 0;
        display: flex;
        align-items: center;
        gap: 12px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        color: #fff !important;
    }

    .header-title-area h1 i {
        background: rgba(255,255,255,0.2);
        padding: 8px;
        border-radius: 10px;
        font-size: 18px;
        color: #fff !important;
    }

    .header-subtitle {
        font-size: 14px;
        opacity: 0.95;
        font-weight: 400;
        color: #fff !important;
    }

    .btn-header {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
        background: rgba(255,255,255,0.2);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(10px);
    }

    .btn-header:hover {
        background: rgba(255,255,255,0.3);
        color: #fff;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .content-area {
        padding: 0;
        width: 100%;
    }

    .form-card {
        background: #fff;
        border-radius: 0;
        box-shadow: none;
        border: none;
        overflow: hidden;
        min-height: calc(100vh - 140px);
    }

    .form-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
        padding: 20px 32px;
        border-bottom: 3px solid #ff9900;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .form-header-icon {
        width: 44px;
        height: 44px;
        background: rgba(255,153,0,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ff9900;
        font-size: 18px;
    }

    .form-header-text h3 {
        font-size: 17px;
        font-weight: 700;
        color: #fff;
        margin: 0;
    }

    .form-header-text p {
        font-size: 13px;
        color: rgba(255,255,255,0.85);
        margin: 0;
    }

    .form-body {
        padding: 28px 32px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 28px;
    }

    .form-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .form-section {
        margin-bottom: 0;
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        flex: 1;
    }

    .form-section-title {
        font-size: 13px;
        font-weight: 700;
        color: #232f3e;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 3px solid #ff9900;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-section-title i {
        font-size: 14px;
        color: #ff9900;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-row:last-child {
        margin-bottom: 0;
    }

    .form-row.single {
        grid-template-columns: 1fr;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .form-group label .required {
        color: #dc2626;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.2s;
        background-color: #fff;
        color: #1f2937;
    }

    .form-control:focus {
        border-color: #FF9900;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.25);
        outline: none;
    }

    /* Native Select Dropdown Styling */
    select.form-control {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 12px;
        padding-right: 40px;
        cursor: pointer;
        color: #1f2937 !important;
        font-weight: 500;
    }

    select.form-control option {
        color: #1f2937;
        background-color: #fff;
        padding: 12px 16px;
    }

    select.form-control option:checked,
    select.form-control option:hover {
        background-color: #f5f3ff;
        color: #5b21b6;
    }

    select.form-control:focus {
        border-color: #FF9900;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.25);
    }

    /* Placeholder style for select */
    select.form-control option[value=""] {
        color: #9ca3af;
    }

    /* Select2 Styling */
    .select2-container--default .select2-selection--single {
        height: 48px;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background-color: #fff;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        padding-left: 0;
        color: #1f2937 !important;
        font-weight: 500;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #9ca3af;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #FF9900;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.25);
    }

    .select2-dropdown {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #FF9900;
        color: #fff;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #f5f3ff;
        color: #5b21b6;
    }

    .select2-container--default .select2-results__option {
        padding: 10px 16px;
        color: #1f2937;
    }

    .amount-input-wrapper {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .amount-input-wrapper .currency-symbol {
        position: static;
        transform: none;
        font-size: 16px;
        font-weight: 600;
        color: #ff9900;
    }

    .amount-input-wrapper .form-control {
        padding-left: 12px;
        font-size: 18px;
        font-weight: 600;
        font-family: 'JetBrains Mono', monospace;
    }

    .form-footer {
        background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
        padding: 20px 32px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        position: sticky;
        bottom: 0;
    }

    .btn-cancel {
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        background: #fff;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cancel:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .btn-submit {
        padding: 12px 36px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
        color: #fff;
        border: 1px solid #C7511F;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(255,153,0,0.3);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255,153,0,0.4);
        opacity: 0.95;
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Customer Integration Highlight - Amazon theme */
    .customer-integration-note {
        background: #fff8e7;
        border: 1px solid #ffb84d;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 12px;
        color: #b45309;
    }
    .customer-integration-note i {
        font-size: 14px;
        color: #ff9900;
    }

    /* Responsive Design */
    @media (max-width: 1200px) { .form-body { grid-template-columns: 1fr; gap: 24px; } .form-column { gap: 20px; } }
    @media (max-width: 768px) {
        .form-body { padding: 16px; grid-template-columns: 1fr; gap: 16px; }
        .form-column { gap: 16px; }
        .form-section { padding: 16px; }
        .form-row { grid-template-columns: 1fr; gap: 12px; }
        .form-footer { padding: 16px; flex-direction: column-reverse; }
        .btn-cancel, .btn-submit { width: 100%; justify-content: center; }
    }
    @media (max-width: 480px) { .form-section-title { font-size: 12px; } .form-group label { font-size: 13px; } .amount-input-wrapper .form-control { font-size: 16px; } }
</style>
@endsection

@section('content')
<div class="income-wrapper">
    <!-- Header -->
    <div class="page-header-banner">
        <div class="header-content">
            <div class="header-title-area">
                <h1><i class="fas fa-plus-circle"></i> Add Income</h1>
                <p class="header-subtitle">Record income transaction for Profit & Loss</p>
            </div>
            <a href="{{ route('bookkeeping.pl.index') }}" class="btn-header">
                <i class="fas fa-arrow-left"></i> Back to Transactions
            </a>
        </div>
    </div>

    <div class="content-area">
        <div class="form-card">
            <div class="form-header">
                <div class="form-header-icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="form-header-text">
                    <h3>New Income Entry</h3>
                    <p>This entry will increase your revenue and affect the P&L report</p>
                </div>
            </div>

            <form id="income_form">
                @csrf
                <div class="form-body">
                    <!-- Left Column -->
                    <div class="form-column">
                        <!-- Basic Information -->
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-info-circle"></i> Basic Information</div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Transaction Date <span class="required">*</span></label>
                                    <input type="text" name="transaction_date" id="transaction_date" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Amount <span class="required">*</span></label>
                                    <div class="amount-input-wrapper">
                                        <span class="currency-symbol">$</span>
                                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" required placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="category" id="category" class="form-control select2">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Income Account <span class="required">*</span></label>
                                    <select name="account_id" id="account_id" class="form-control select2" required>
                                        <option value="">Select Account</option>
                                        @foreach($incomeAccounts as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Integration -->
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-user"></i> Customer Information</div>
                            
                            <div class="customer-integration-note">
                                <i class="fas fa-lightbulb"></i>
                                Link this income to a customer for better tracking and reporting
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select name="contact_id" id="contact_id" class="form-control select2">
                                        <option value="">Select Customer (Optional)</option>
                                        @foreach($customers as $id => $name)
                                            @if($id)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Invoice Number</label>
                                    <input type="text" name="invoice_number" class="form-control" placeholder="INV-001">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="form-column">
                        <!-- Payment Details -->
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-credit-card"></i> Payment Details</div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Deposit To Account <span class="required">*</span></label>
                                    <select name="payment_account_id" id="payment_account_id" class="form-control select2" required>
                                        <option value="">Select Bank/Cash Account</option>
                                        @foreach($paymentAccounts as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="">Select Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="check">Check</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="credit_card">Credit Card</option>
                                        <option value="online">Online Payment</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row single">
                                <div class="form-group">
                                    <label>Payment Reference</label>
                                    <input type="text" name="payment_reference" class="form-control" placeholder="Check number, transaction ID, etc.">
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-align-left"></i> Additional Details</div>
                            
                            <div class="form-row single">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="Enter description for this income entry..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button type="button" class="btn-cancel" onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="btn-submit" id="submit_btn">
                        <i class="fas fa-check"></i> Save Income
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%',
        placeholder: function() {
            return $(this).data('placeholder') || 'Select an option';
        }
    });

    // Initialize Datepicker
    flatpickr('#transaction_date', {
        dateFormat: 'Y-m-d',
        defaultDate: 'today',
        maxDate: 'today'
    });

    // Form submission
    $('#income_form').on('submit', function(e) {
        e.preventDefault();
        
        var $btn = $('#submit_btn');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: "{{ route('bookkeeping.pl.income.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.msg,
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        window.location.href = "{{ route('bookkeeping.pl.index') }}";
                    });
                } else {
                    Swal.fire('Error', response.msg, 'error');
                    $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Save Income');
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.msg) {
                        msg = xhr.responseJSON.msg;
                    } else if (xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        // Show first validation error message
                        var firstKey = Object.keys(xhr.responseJSON.errors)[0];
                        if (firstKey && xhr.responseJSON.errors[firstKey].length) {
                            msg = xhr.responseJSON.errors[firstKey][0];
                        }
                    }
                } else if (xhr.responseText) {
                    // Try to parse JSON from responseText if possible
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json.msg) {
                            msg = json.msg;
                        } else if (json.message) {
                            msg = json.message;
                        }
                    } catch (e) {
                        // keep default
                    }
                }
                Swal.fire('Error', msg, 'error');
                $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Save Income');
            }
        });
    });
});
</script>
@endsection


