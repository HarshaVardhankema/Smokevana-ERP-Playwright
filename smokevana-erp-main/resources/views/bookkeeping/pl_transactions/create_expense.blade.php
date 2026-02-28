@extends('layouts.app')
@section('title', 'Add Expense')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .expense-wrapper {
        padding: 0;
        background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
        min-height: calc(100vh - 60px);
    }

    .page-header-banner {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%);
        padding: 32px 40px;
        color: #fff !important;
        position: relative;
        overflow: hidden;
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
        color: #fff !important;
    }

    .header-title-area h1 i {
        color: #fff !important;
    }

    .header-subtitle {
        font-size: 15px;
        opacity: 0.95;
        color: #fff !important;
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
        max-width: 900px;
        margin: 0 auto;
    }

    .form-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.1);
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        padding: 20px 24px;
        border-bottom: 1px solid rgba(239, 68, 68, 0.1);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-header-icon {
        width: 48px;
        height: 48px;
        background: #ef4444;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
    }

    .form-header-text h3 {
        font-size: 18px;
        font-weight: 700;
        color: #991b1b;
        margin: 0;
    }

    .form-header-text p {
        font-size: 14px;
        color: #dc2626;
        margin: 0;
    }

    .form-body {
        padding: 24px;
    }

    .form-section {
        margin-bottom: 24px;
    }

    .form-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f1f5f9;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
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
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
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
        background-color: #fef2f2;
        color: #991b1b;
    }

    select.form-control:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
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
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .select2-dropdown {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #ef4444;
        color: #fff;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #fef2f2;
        color: #991b1b;
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
        color: #ef4444;
    }

    .amount-input-wrapper .form-control {
        padding-left: 12px;
        font-size: 18px;
        font-weight: 600;
        font-family: 'JetBrains Mono', monospace;
    }

    .form-footer {
        background: #f9fafb;
        padding: 20px 24px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-cancel {
        padding: 12px 24px;
        border-radius: 10px;
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
    }

    .btn-submit {
        padding: 12px 32px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #fff;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Vendor Integration Highlight */
    .vendor-integration-note {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #f59e0b;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #92400e;
    }

    .vendor-integration-note i {
        font-size: 16px;
    }

    /* Expense category icons */
    .category-hint {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        .content-area {
            padding: 20px;
        }
    }
</style>
@endsection

@section('content')
<div class="expense-wrapper">
    <!-- Header -->
    <div class="page-header-banner">
        <div class="header-content">
            <div class="header-title-area">
                <h1><i class="fas fa-minus-circle"></i> Add Expense</h1>
                <p class="header-subtitle">Record expense transaction for Profit & Loss</p>
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
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="form-header-text">
                    <h3>New Expense Entry</h3>
                    <p>This entry will increase your expenses and affect the P&L report</p>
                </div>
            </div>

            <form id="expense_form">
                @csrf
                <div class="form-body">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <div class="form-section-title">Basic Information</div>
                        
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
                                <p class="category-hint">Select the type of expense for better reporting</p>
                            </div>
                            <div class="form-group">
                                <label>Expense Account <span class="required">*</span></label>
                                <select name="account_id" id="account_id" class="form-control select2" required>
                                    <option value="">Select Account</option>
                                    @foreach($expenseAccounts as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Vendor Integration -->
                    <div class="form-section">
                        <div class="form-section-title">Vendor Information (Optional)</div>
                        
                        <div class="vendor-integration-note">
                            <i class="fas fa-info-circle"></i>
                            Link this expense to a vendor/supplier for better tracking and vendor reports
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Vendor/Supplier</label>
                                <select name="contact_id" id="contact_id" class="form-control select2">
                                    <option value="">Select Vendor (Optional)</option>
                                    @foreach($vendors as $id => $name)
                                        @if($id)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Bill/Invoice Number</label>
                                <input type="text" name="bill_number" class="form-control" placeholder="BILL-001">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="form-section">
                        <div class="form-section-title">Payment Details</div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Pay From Account <span class="required">*</span></label>
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
                                    <option value="debit_card">Debit Card</option>
                                    <option value="online">Online Payment</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row single">
                            <div class="form-group">
                                <label>Payment Reference</label>
                                <input type="text" name="payment_reference" class="form-control" placeholder="Check number, transaction ID, receipt number, etc.">
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-section">
                        <div class="form-section-title">Additional Details</div>
                        
                        <div class="form-row single">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Enter description for this expense entry..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button type="button" class="btn-cancel" onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="btn-submit" id="submit_btn">
                        <i class="fas fa-check"></i> Save Expense
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
    $('#expense_form').on('submit', function(e) {
        e.preventDefault();
        
        var $btn = $('#submit_btn');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: "{{ route('bookkeeping.pl.expense.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.msg,
                        confirmButtonColor: '#ef4444'
                    }).then(() => {
                        window.location.href = "{{ route('bookkeeping.pl.index') }}";
                    });
                } else {
                    Swal.fire('Error', response.msg, 'error');
                    $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Save Expense');
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
                        var firstKey = Object.keys(xhr.responseJSON.errors)[0];
                        if (firstKey && xhr.responseJSON.errors[firstKey].length) {
                            msg = xhr.responseJSON.errors[firstKey][0];
                        }
                    }
                } else if (xhr.responseText) {
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json.msg) {
                            msg = json.msg;
                        } else if (json.message) {
                            msg = json.message;
                        }
                    } catch (e) {
                        // ignore parse error
                    }
                }
                Swal.fire('Error', msg, 'error');
                $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Save Expense');
            }
        });
    });
});
</script>
@endsection




