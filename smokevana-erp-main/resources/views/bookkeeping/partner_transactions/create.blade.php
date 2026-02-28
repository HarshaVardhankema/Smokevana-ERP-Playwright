@extends('layouts.app')
@section('title', 'Create Partner Transaction')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    /* ===== Page Container - Amazon Theme ===== */
    .partner-create-wrapper {
        padding: 0;
        background: #EAEDED;
        min-height: calc(100vh - 60px);
    }

    /* ===== Header Banner ===== */
    .partner-header-banner {
        background: linear-gradient(180deg, #37475a 0%, #232f3e 100%);
        padding: 32px 40px;
        color: #fff !important;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    }

    .partner-header-banner .header-title-area h1,
    .partner-header-banner .header-title-area h1 *,
    .partner-header-banner .header-subtitle {
        color: #fff !important;
    }

    .partner-header-banner .header-title-area h1 i {
        color: #fff !important;
    }

    .partner-header-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
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
        font-size: 32px;
        opacity: 0.95;
        color: #fff !important;
    }

    .header-subtitle {
        font-size: 15px;
        opacity: 0.95;
        font-weight: 400;
        color: #fff !important;
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
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: 1px solid #D5D9D9;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .form-card-header {
        padding: 20px 24px;
        border-bottom: 3px solid #ff9900;
        display: flex;
        align-items: center;
        gap: 14px;
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    }

    .form-card-header-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        background: rgba(255,153,0,0.2);
        color: #ff9900;
    }

    .form-card-header-icon.purple,
    .form-card-header-icon.blue,
    .form-card-header-icon.amber {
        background: rgba(255,153,0,0.2);
        color: #ff9900;
    }

    .form-card-header h3 {
        font-size: 18px;
        font-weight: 700;
        color: #fff;
        margin: 0;
    }

    .form-card-body {
        padding: 24px;
    }

    /* ===== Transaction Type Selection ===== */
    .transaction-type-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .transaction-type-card {
        background: #fff;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .transaction-type-card:hover {
        border-color: #ff9900;
        background: #fff8e7;
    }

    .transaction-type-card input[type="radio"] {
        display: none;
    }

    .transaction-type-card:has(input[type="radio"]:checked) {
        border-color: #ff9900;
        background: #fff8e7;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.25);
    }

    .type-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
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

    .transaction-type-card.advance .type-icon-wrapper {
        background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
        color: #7c3aed;
    }

    .transaction-type-card.reimbursement .type-icon-wrapper {
        background: linear-gradient(135deg, #cffafe, #a5f3fc);
        color: #0891b2;
    }

    .transaction-type-card.personal-asset .type-icon-wrapper {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #d97706;
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

    .field-row.full-width {
        grid-template-columns: 1fr;
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
        border: 1px solid #D5D9D9;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        color: #111827 !important;
        background-color: #ffffff !important;
        transition: all 0.3s ease;
        line-height: 1.5;
    }

    .form-group-custom .form-control::placeholder {
        color: #9ca3af !important;
        font-weight: 400;
        opacity: 1;
    }

    .form-group-custom .form-control:hover {
        border-color: #ffb84d;
    }

    .form-group-custom .form-control:focus {
        border-color: #FF9900 !important;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.25) !important;
        outline: none !important;
        background-color: #ffffff !important;
        color: #111827 !important;
    }

    /* Ensure select elements don't inherit unwanted styles */
    .form-group-custom select.form-control {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
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

    /* ===== Select2 Styling - Enhanced ===== */
    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 46px !important;
        border: 1px solid #D5D9D9 !important;
        border-radius: 10px !important;
        background-color: #ffffff !important;
        transition: all 0.3s ease;
    }

    .select2-container--default .select2-selection--single:hover {
        border-color: #ffb84d !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #FF9900 !important;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.25) !important;
        outline: none !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 14px !important;
        padding-right: 30px !important;
        color: #111827 !important;
        font-size: 14px !important;
        font-weight: 500 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6b7280 !important;
        font-weight: 400 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
        right: 12px !important;
        width: 20px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #6b7280 transparent transparent transparent !important;
        border-width: 6px 5px 0 5px !important;
        margin-top: -3px !important;
    }

    /* Select2 Dropdown Styling */
    .select2-dropdown {
        border: 2px solid #e5e7eb !important;
        border-radius: 10px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
        margin-top: 4px !important;
    }

    .select2-container--default .select2-results__option {
        padding: 12px 14px !important;
        font-size: 14px !important;
        color: #111827 !important;
        transition: all 0.2s ease;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #FF9900 !important;
        color: #fff !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #f3f4f6 !important;
        color: #111827 !important;
    }

    .select2-search--dropdown .select2-search__field {
        border: 2px solid #e5e7eb !important;
        border-radius: 8px !important;
        padding: 10px 12px !important;
        font-size: 14px !important;
        color: #111827 !important;
    }

    .select2-search--dropdown .select2-search__field:focus {
        border-color: #FF9900 !important;
        outline: none !important;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.25) !important;
    }

    /* ===== Sidebar Summary ===== */
    .sidebar-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: 1px solid #D5D9D9;
        overflow: hidden;
        position: sticky;
        top: 20px;
    }

    .sidebar-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
        color: #fff;
        border-bottom: 3px solid #ff9900;
    }

    .sidebar-header h4 {
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar-header h4 i {
        color: #ff9900;
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

    .summary-item-value {
        font-size: 14px;
        font-weight: 600;
        color: #1e1b4b;
    }

    /* ===== Action Buttons ===== */
    .form-actions {
        padding: 24px;
        background: #f9fafb;
        border-top: 1px solid #f3f4f6;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-action {
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-cancel {
        background: #fff;
        color: #6b7280;
        border: 2px solid #e5e7eb;
    }

    .btn-cancel:hover {
        background: #f9fafb;
        color: #374151;
    }

    .btn-submit {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
        color: #fff;
        border: 1px solid #C7511F;
    }

    .btn-submit:hover {
        opacity: 0.95;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255,153,0,0.4);
    }

    /* Responsive */
    @media (max-width: 968px) {
        .form-container {
            grid-template-columns: 1fr;
        }
        
        .sidebar-card {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .content-area {
            padding: 20px;
        }

        .partner-header-banner {
            padding: 24px 20px;
        }

        .header-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
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
<section class="content partner-create-wrapper">
    <!-- Header Banner -->
    <div class="partner-header-banner">
        <div class="header-content">
            <div class="header-title-area">
                <h1><i class="fas fa-handshake"></i> Create Partner Transaction</h1>
                <p class="header-subtitle">Record a new transaction with a business partner</p>
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
        <form id="create_partner_transaction_form" method="POST" action="{{ route('bookkeeping.partner.store') }}">
            @csrf
            
            <div class="form-container">
                <!-- Main Form -->
                <div>
                    <!-- Transaction Type Selection -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-card-header-icon purple">
                                <i class="fas fa-tags"></i>
                            </div>
                            <h3>Transaction Type</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="transaction-type-grid">
                                @foreach($transactionTypes as $type => $label)
                                <label class="transaction-type-card {{ str_replace('_', '-', $type) }}">
                                    <input type="radio" name="transaction_type" value="{{ $type }}" required>
                                    <div class="type-content">
                                        <div class="type-icon-wrapper">
                                            @if($type == 'capital_contribution')
                                                <i class="fas fa-arrow-up"></i>
                                            @elseif($type == 'owner_drawing')
                                                <i class="fas fa-arrow-down"></i>
                                            @elseif($type == 'loan_from_partner')
                                                <i class="fas fa-hand-holding-usd"></i>
                                            @elseif($type == 'loan_to_partner')
                                                <i class="fas fa-hand-holding"></i>
                                            @elseif($type == 'advance')
                                                <i class="fas fa-forward"></i>
                                            @elseif($type == 'reimbursement')
                                                <i class="fas fa-undo"></i>
                                            @else
                                                <i class="fas fa-box"></i>
                                            @endif
                                        </div>
                                        <div class="type-label">{{ $label }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Details -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-card-header-icon blue">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h3>Transaction Details</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="field-row">
                                <div class="form-group-custom">
                                    <label>Partner <span class="required">*</span></label>
                                    <select name="partner_id" class="form-control select2" required data-placeholder="Select Partner...">
                                        <option value="">Select Partner...</option>
                                        @foreach($partners as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group-custom">
                                    <label>Account <span class="required">*</span></label>
                                    <select name="account_id" class="form-control select2" required data-placeholder="Select Account...">
                                        <option value="">Select Account...</option>
                                        @foreach($accounts as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="field-row">
                                <div class="form-group-custom">
                                    <label>Amount <span class="required">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required placeholder="0.00" id="transaction_amount">
                                    </div>
                                </div>
                                <div class="form-group-custom">
                                    <label>Transaction Date <span class="required">*</span></label>
                                    <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="field-row full-width">
                                <div class="form-group-custom">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" placeholder="Enter transaction description (optional)"></textarea>
                                </div>
                            </div>

                            <div class="field-row full-width">
                                <div class="form-group-custom">
                                    <label>Reference Number</label>
                                    <input type="text" name="reference" class="form-control" placeholder="Enter reference number (optional)">
                                    <div class="field-help">
                                        <i class="fas fa-info-circle"></i> Optional reference for tracking
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Summary -->
                <div>
                    <div class="sidebar-card">
                        <div class="sidebar-header">
                            <h4><i class="fas fa-info-circle"></i> Transaction Summary</h4>
                            <p>Review your transaction details</p>
                        </div>
                        <div class="sidebar-body">
                            <div class="summary-item">
                                <div class="summary-item-label">
                                    <i class="fas fa-user"></i> Partner
                                </div>
                                <div class="summary-item-value" id="summary_partner">-</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-item-label">
                                    <i class="fas fa-tag"></i> Type
                                </div>
                                <div class="summary-item-value" id="summary_type">-</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-item-label">
                                    <i class="fas fa-dollar-sign"></i> Amount
                                </div>
                                <div class="summary-item-value" id="summary_amount">$0.00</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-item-label">
                                    <i class="fas fa-calendar"></i> Date
                                </div>
                                <div class="summary-item-value" id="summary_date">{{ date('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('bookkeeping.partner.index') }}" class="btn-action btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-action btn-submit">
                    <i class="fas fa-check"></i> Create Transaction
                </button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize Select2 with enhanced styling
    $('.select2').select2({
        width: '100%',
        theme: 'bootstrap4',
        placeholder: function() {
            return $(this).data('placeholder') || 'Select an option...';
        },
        allowClear: false,
        minimumResultsForSearch: 0
    });

    // Ensure text is visible after selection
    $('.select2').on('select2:select', function() {
        $(this).next('.select2-container').find('.select2-selection__rendered').css({
            'color': '#111827',
            'font-weight': '500'
        });
    });

    // Style placeholder text
    $('.select2').on('select2:open', function() {
        var $container = $(this).next('.select2-container');
        if ($(this).val() === '' || $(this).val() === null) {
            $container.find('.select2-selection__rendered').css('color', '#6b7280');
        }
    });

    // Update summary on form changes
    function updateSummary() {
        var partner = $('select[name="partner_id"] option:selected').text();
        var type = $('input[name="transaction_type"]:checked').closest('.transaction-type-card').find('.type-label').text();
        var amount = parseFloat($('#transaction_amount').val()) || 0;
        var date = $('input[name="transaction_date"]').val();
        
        $('#summary_partner').text(partner || '-');
        $('#summary_type').text(type || '-');
        $('#summary_amount').text('$' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        
        if (date) {
            var dateObj = new Date(date);
            $('#summary_date').text(dateObj.toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'}));
        }
    }

    // Bind events
    $('select[name="partner_id"], input[name="transaction_type"], #transaction_amount, input[name="transaction_date"]').on('change keyup', updateSummary);

    // Form submission
    $('#create_partner_transaction_form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        // Disable submit button
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    setTimeout(function() {
                        window.location.href = "{{ route('bookkeeping.partner.index') }}";
                    }, 1500);
                } else {
                    toastr.error(response.msg || 'An error occurred');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                var errorMsg = 'An error occurred while creating the transaction';
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = xhr.responseJSON.msg;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('<br>');
                }
                toastr.error(errorMsg);
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endsection
