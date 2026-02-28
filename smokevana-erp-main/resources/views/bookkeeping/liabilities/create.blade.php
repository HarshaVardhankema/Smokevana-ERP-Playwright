@extends('layouts.app')
@section('title', 'Add New Liability')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
/* Add Liability - Amazon Theme */
.al-page { 
    background: #EAEDED; 
    min-height: 100vh; 
    padding: 0 0 40px 0;
}

/* Header Banner */
.al-header-banner {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%);
    padding: 20px 28px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative;
    overflow: hidden;
}
.al-header-banner::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: linear-gradient(90deg, #ff9900, #e47911);
    z-index: 1;
}
.al-header-banner h1 {
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 4px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #fff !important;
}
.al-header-banner h1 i { font-size: 20px; color: #fff !important; }
.al-header-banner .subtitle { 
    font-size: 13px; 
    opacity: 0.9; 
    margin: 0; 
    color: #fff !important;
}
.al-btn-back {
    background: rgba(255,255,255,0.2);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.35);
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}
.al-btn-back:hover { background: rgba(255,255,255,0.3); color: #fff; text-decoration: none; }

/* Content Container */
.al-content { padding: 0 20px; max-width: 1200px; margin: 0 auto; }

/* Cards */
.al-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(139, 92, 246, 0.06);
    border: 1px solid rgba(139, 92, 246, 0.08);
    margin-bottom: 20px;
    overflow: hidden;
}
.al-card-header {
    padding: 16px 20px;
    border-bottom: 3px solid #ff9900;
    display: flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
}
.al-card-header-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255,153,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ff9900;
    font-size: 14px;
}
.al-card-header h3 { 
    font-size: 14px; 
    font-weight: 600; 
    color: #fff; 
    margin: 0; 
}
.al-card-body { padding: 20px; }

/* Type Selection - Horizontal Row */
.al-type-row {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.al-type-card {
    flex: 1;
    min-width: 120px;
    max-width: 180px;
    padding: 16px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    background: #fff;
}
.al-type-card:hover { 
    border-color: #ff9900; 
    background: #fff8e7; 
}
.al-type-card.selected { 
    border-color: #ff9900; 
    background: #fff8e7; 
    box-shadow: 0 0 0 2px rgba(255,153,0,0.25); 
}
.al-type-card.selected::after { 
    content: '\f00c'; 
    font-family: 'Font Awesome 5 Free'; 
    font-weight: 900; 
    position: absolute; 
    top: 6px; 
    right: 6px; 
    width: 18px; 
    height: 18px; 
    border-radius: 50%; 
    background: linear-gradient(135deg, #ff9900 0%, #e47911 100%); 
    color: #fff; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-size: 9px; 
}
.al-type-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin: 0 auto 10px;
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #dc2626;
}
.al-type-label { 
    font-size: 12px; 
    font-weight: 600; 
    color: #374151; 
    line-height: 1.3;
}
.al-type-card input[type="radio"] { display: none; }

/* Form Layout */
.al-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}
@media (max-width: 768px) { .al-form-row { grid-template-columns: 1fr; } }

.al-form-group { margin-bottom: 0; }
.al-form-group.full-width { grid-column: 1 / -1; }

.al-form-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}
.al-form-label .required { color: #dc2626; }

.al-form-input {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    color: #1e1b4b;
    background: #fff;
    transition: all 0.2s ease;
}
.al-form-input:focus { 
    border-color: #FF9900; 
    outline: none; 
    box-shadow: 0 0 0 2px rgba(255,153,0,0.25); 
}
.al-form-input::placeholder { color: #9ca3af; }
textarea.al-form-input { min-height: 70px; resize: vertical; }

.al-form-hint {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.al-form-hint i { font-size: 10px; }

/* Amount Input with Currency */
.al-amount-wrapper {
    position: relative;
}
.al-amount-wrapper .currency-symbol {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    font-weight: 600;
    font-size: 14px;
}
.al-amount-wrapper .al-form-input {
    padding-left: 28px;
}

/* Actions */
.al-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 16px 20px;
    background: #fafbfc;
    border-top: 1px solid #f1f5f9;
    border-radius: 0 0 12px 12px;
    margin-top: -1px;
}
.al-btn-cancel {
    padding: 10px 20px;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    color: #6b7280;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}
.al-btn-cancel:hover { 
    background: #f9fafb; 
    border-color: #d1d5db; 
    color: #4b5563; 
    text-decoration: none; 
}
.al-btn-submit {
    padding: 10px 24px;
    border: 1px solid #C7511F;
    border-radius: 8px;
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    color: #fff;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 2px 8px rgba(255,153,0,0.3);
}
.al-btn-submit:hover { 
    transform: translateY(-1px); 
    box-shadow: 0 4px 12px rgba(255,153,0,0.4);
    opacity: 0.95;
}

/* Select2 Customization */
.al-card .select2-container--default .select2-selection--single {
    height: 42px;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    padding: 6px 8px;
}
.al-card .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px;
    color: #1e1b4b;
    padding-left: 6px;
}
.al-card .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
}
.al-card .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #FF9900;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.25);
}
</style>
@endsection

@section('content')
<section class="content al-page">
    <div class="al-header-banner">
        <div>
            <h1><i class="fas fa-file-invoice"></i> Add New Liability</h1>
            <p class="subtitle">Record a new payable, loan, or obligation</p>
        </div>
        <a href="{{ route('bookkeeping.liabilities.index') }}" class="al-btn-back"><i class="fas fa-arrow-left"></i> Back to Liabilities</a>
    </div>

    <div class="al-content">
        <form action="{{ route('bookkeeping.liabilities.store') }}" method="POST" id="create_liability_form">
            @csrf
            
            <!-- Liability Type Selection -->
            <div class="al-card">
                <div class="al-card-header">
                    <div class="al-card-header-icon"><i class="fas fa-tags"></i></div>
                    <h3>Liability Type</h3>
                </div>
                <div class="al-card-body">
                    <div class="al-type-row">
                        <label class="al-type-card selected">
                            <input type="radio" name="liability_type" value="vendor_payable" checked>
                            <div class="al-type-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                            <div class="al-type-label">Vendor Payable</div>
                        </label>
                        <label class="al-type-card">
                            <input type="radio" name="liability_type" value="loan_payable">
                            <div class="al-type-icon"><i class="fas fa-hand-holding-usd"></i></div>
                            <div class="al-type-label">Loan Payable</div>
                        </label>
                        <label class="al-type-card">
                            <input type="radio" name="liability_type" value="credit_card">
                            <div class="al-type-icon"><i class="fas fa-credit-card"></i></div>
                            <div class="al-type-label">Credit Card</div>
                        </label>
                        <label class="al-type-card">
                            <input type="radio" name="liability_type" value="partner_loan">
                            <div class="al-type-icon"><i class="fas fa-user-tie"></i></div>
                            <div class="al-type-label">Partner Loan</div>
                        </label>
                        <label class="al-type-card">
                            <input type="radio" name="liability_type" value="customer_deposit">
                            <div class="al-type-icon"><i class="fas fa-user-tag"></i></div>
                            <div class="al-type-label">Customer Deposit</div>
                        </label>
                        <label class="al-type-card">
                            <input type="radio" name="liability_type" value="other">
                            <div class="al-type-icon"><i class="fas fa-ellipsis-h"></i></div>
                            <div class="al-type-label">Other</div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Liability Details -->
            <div class="al-card">
                <div class="al-card-header">
                    <div class="al-card-header-icon"><i class="fas fa-info-circle"></i></div>
                    <h3>Liability Details</h3>
                </div>
                <div class="al-card-body">
                    <div class="al-form-row">
                        <div class="al-form-group">
                            <label class="al-form-label">Name / Description <span class="required">*</span></label>
                            <input type="text" name="name" class="al-form-input" required placeholder="e.g., Office Rent - January">
                        </div>
                        <div class="al-form-group">
                            <label class="al-form-label">Liability Account <span class="required">*</span></label>
                            <select name="liability_account_id" class="al-form-input select2">
                                <option value="">Select account...</option>
                                @foreach($liabilityAccounts ?? [] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <div class="al-form-hint"><i class="fas fa-info-circle"></i> Link to a liability account</div>
                        </div>
                    </div>
                    <div class="al-form-row">
                        <div class="al-form-group">
                            <label class="al-form-label">Contact / Vendor</label>
                            <select name="contact_id" class="al-form-input select2">
                                <option value="">Select contact...</option>
                                @foreach($contacts ?? [] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="al-form-group">
                            <label class="al-form-label">Amount <span class="required">*</span></label>
                            <div class="al-amount-wrapper">
                                <span class="currency-symbol">$</span>
                                <input type="number" name="original_amount" class="al-form-input" step="0.01" required placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="al-form-row">
                        <div class="al-form-group">
                            <label class="al-form-label">Start Date <span class="required">*</span></label>
                            <input type="text" name="start_date" class="al-form-input al-datepicker" value="{{ date('m/d/Y') }}" required placeholder="MM/DD/YYYY">
                        </div>
                        <div class="al-form-group">
                            <label class="al-form-label">Due Date</label>
                            <input type="text" name="due_date" class="al-form-input al-datepicker" placeholder="MM/DD/YYYY">
                            <div class="al-form-hint"><i class="fas fa-info-circle"></i> Optional payment deadline</div>
                        </div>
                    </div>
                    <div class="al-form-row">
                        <div class="al-form-group full-width">
                            <label class="al-form-label">Description / Notes <span style="color: #9ca3af; font-weight: 400;">(Optional)</span></label>
                            <textarea name="description" class="al-form-input" placeholder="Additional details about this liability..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="al-actions">
                    <a href="{{ route('bookkeeping.liabilities.index') }}" class="al-btn-cancel">Cancel</a>
                    <button type="submit" class="al-btn-submit"><i class="fas fa-check"></i> Create Liability</button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({ 
        width: '100%',
        dropdownParent: $('.al-content')
    });
    
    // Initialize Datepickers (Flatpickr)
    flatpickr('.al-datepicker', {
        dateFormat: 'm/d/Y',
        allowInput: true
    });
    
    // Type card selection
    $('.al-type-card').click(function() { 
        $('.al-type-card').removeClass('selected'); 
        $(this).addClass('selected'); 
    });
    
    // Form submission with AJAX
    $('#create_liability_form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var btn = form.find('.al-btn-submit');
        var originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg || 'Liability created successfully!');
                    setTimeout(function() {
                        window.location.href = '{{ route("bookkeeping.liabilities.index") }}';
                    }, 1500);
                } else {
                    toastr.error(response.msg || 'Failed to create liability');
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred while creating the liability.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.msg) {
                        msg = xhr.responseJSON.msg;
                    } else if (xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                }
                toastr.error(msg);
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endsection
