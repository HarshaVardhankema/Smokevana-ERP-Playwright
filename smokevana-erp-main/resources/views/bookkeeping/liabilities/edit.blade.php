@extends('layouts.app')
@section('title', 'Edit Liability')

@section('css')
<style>
/* Edit Liability - Compact Professional Design */
.al-page { 
    background: linear-gradient(180deg, #f8f9fe 0%, #f3f4f6 100%); 
    min-height: 100vh; 
    padding: 0 0 40px 0;
}

/* Header Banner */
.al-header-banner {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
    padding: 20px 28px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 4px 20px rgba(124, 58, 237, 0.2);
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
    background: #fff;
    color: #7c3aed;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.al-btn-back:hover { background: #f5f3ff; color: #6d28d9; text-decoration: none; }

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
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fafbfc;
}
.al-card-header-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7c3aed;
    font-size: 14px;
}
.al-card-header h3 { 
    font-size: 14px; 
    font-weight: 600; 
    color: #1e1b4b; 
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
    border-color: #c4b5fd; 
    background: #faf5ff; 
}
.al-type-card.selected { 
    border-color: #7c3aed; 
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); 
    box-shadow: 0 2px 10px rgba(124, 58, 237, 0.12); 
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
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); 
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
    border-color: #8b5cf6; 
    outline: none; 
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1); 
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
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
    color: #fff;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 2px 10px rgba(124, 58, 237, 0.25);
}
.al-btn-submit:hover { 
    transform: translateY(-1px); 
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.35); 
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
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

/* Status Badge */
.al-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    margin-left: 12px;
}
.al-status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
.al-status-badge.active { background: rgba(16, 185, 129, 0.15); color: #10b981; }
.al-status-badge.active::before { background: #10b981; }
.al-status-badge.paid_off { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
.al-status-badge.paid_off::before { background: #3b82f6; }
.al-status-badge.overdue { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
.al-status-badge.overdue::before { background: #ef4444; }
</style>
@endsection

@section('content')
@php
    $liabilityTypes = \App\Models\BusinessLiability::getLiabilityTypes();
    $paymentFrequencies = \App\Models\BusinessLiability::getPaymentFrequencies();
    $statuses = \App\Models\BusinessLiability::getStatuses();
    $currentStatus = $liability->isOverdue() && $liability->status === 'active' ? 'overdue' : $liability->status;
@endphp

<section class="content al-page">
    <div class="al-header-banner">
        <div>
            <h1>
                <i class="fas fa-edit"></i> Edit Liability
                <span class="al-status-badge {{ $currentStatus }}">{{ ucfirst(str_replace('_', ' ', $currentStatus)) }}</span>
            </h1>
            <p class="subtitle">Update liability details for: {{ $liability->name }}</p>
        </div>
        <a href="{{ route('bookkeeping.liabilities.index') }}" class="al-btn-back"><i class="fas fa-arrow-left"></i> Back to Liabilities</a>
    </div>

    <div class="al-content">
        <form action="{{ route('bookkeeping.liabilities.update', $liability->id) }}" method="POST" id="edit_liability_form">
            @csrf
            @method('PUT')
            
            <!-- Liability Type Selection -->
            <div class="al-card">
                <div class="al-card-header">
                    <div class="al-card-header-icon"><i class="fas fa-tags"></i></div>
                    <h3>Liability Type</h3>
                </div>
                <div class="al-card-body">
                    <div class="al-type-row">
                        @foreach($liabilityTypes as $value => $label)
                        <label class="al-type-card {{ $liability->liability_type === $value ? 'selected' : '' }}">
                            <input type="radio" name="liability_type" value="{{ $value }}" {{ $liability->liability_type === $value ? 'checked' : '' }}>
                            <div class="al-type-icon">
                                @switch($value)
                                    @case('vendors_unpaid')
                                        <i class="fas fa-file-invoice-dollar"></i>
                                        @break
                                    @case('owed_to_partner')
                                        <i class="fas fa-user-tie"></i>
                                        @break
                                    @case('credit_card')
                                        <i class="fas fa-credit-card"></i>
                                        @break
                                    @case('loan')
                                        <i class="fas fa-hand-holding-usd"></i>
                                        @break
                                    @case('advance_received')
                                        <i class="fas fa-user-tag"></i>
                                        @break
                                    @case('employee_payable')
                                        <i class="fas fa-users"></i>
                                        @break
                                    @case('tax_payable')
                                        <i class="fas fa-landmark"></i>
                                        @break
                                    @default
                                        <i class="fas fa-ellipsis-h"></i>
                                @endswitch
                            </div>
                            <div class="al-type-label">{{ $label }}</div>
                        </label>
                        @endforeach
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
                            <input type="text" name="name" class="al-form-input" required value="{{ $liability->name }}" placeholder="e.g., Office Rent - January">
                        </div>
                        <div class="al-form-group">
                            <label class="al-form-label">Liability Account</label>
                            <select name="liability_account_id" class="al-form-input select2">
                                <option value="">Select account...</option>
                                @foreach($liabilityAccounts ?? [] as $id => $name)
                                <option value="{{ $id }}" {{ $liability->liability_account_id == $id ? 'selected' : '' }}>{{ $name }}</option>
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
                                <option value="{{ $id }}" {{ $liability->contact_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="al-form-group">
                            <label class="al-form-label">Original Amount <span class="required">*</span></label>
                            <div class="al-amount-wrapper">
                                <span class="currency-symbol">$</span>
                                <input type="number" name="original_amount" class="al-form-input" step="0.01" required value="{{ $liability->original_amount }}" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="al-form-row">
                        <div class="al-form-group">
                            <label class="al-form-label">Current Balance</label>
                            <div class="al-amount-wrapper">
                                <span class="currency-symbol">$</span>
                                <input type="number" name="current_balance" class="al-form-input" step="0.01" value="{{ $liability->current_balance }}" placeholder="0.00">
                            </div>
                            <div class="al-form-hint"><i class="fas fa-info-circle"></i> Remaining amount to be paid</div>
                        </div>
                        <div class="al-form-group">
                            <label class="al-form-label">Status</label>
                            <select name="status" class="al-form-input">
                                @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ $liability->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="al-form-row">
                        <div class="al-form-group">
                            <label class="al-form-label">Start Date <span class="required">*</span></label>
                            <input type="text" name="start_date" class="al-form-input al-datepicker" value="{{ $liability->start_date ? $liability->start_date->format('m/d/Y') : '' }}" required placeholder="MM/DD/YYYY">
                        </div>
                        <div class="al-form-group">
                            <label class="al-form-label">Due Date</label>
                            <input type="text" name="due_date" class="al-form-input al-datepicker" value="{{ $liability->due_date ? $liability->due_date->format('m/d/Y') : '' }}" placeholder="MM/DD/YYYY">
                            <div class="al-form-hint"><i class="fas fa-info-circle"></i> Optional payment deadline</div>
                        </div>
                    </div>
                    <div class="al-form-row">
                        <div class="al-form-group">
                            <label class="al-form-label">Payment Frequency</label>
                            <select name="payment_frequency" class="al-form-input">
                                @foreach($paymentFrequencies as $value => $label)
                                <option value="{{ $value }}" {{ $liability->payment_frequency === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="al-form-group">
                            <label class="al-form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="al-form-input" value="{{ $liability->reference_number }}" placeholder="e.g., INV-12345">
                        </div>
                    </div>
                    <div class="al-form-row">
                        <div class="al-form-group full-width">
                            <label class="al-form-label">Description / Notes <span style="color: #9ca3af; font-weight: 400;">(Optional)</span></label>
                            <textarea name="description" class="al-form-input" placeholder="Additional details about this liability...">{{ $liability->description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="al-actions">
                    <a href="{{ route('bookkeeping.liabilities.index') }}" class="al-btn-cancel">Cancel</a>
                    <button type="submit" class="al-btn-submit"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({ 
        width: '100%',
        dropdownParent: $('.al-content')
    });
    
    // Initialize Datepickers
    $('.al-datepicker').datepicker({ 
        format: 'mm/dd/yyyy', 
        autoclose: true, 
        todayHighlight: true 
    });
    
    // Type card selection
    $('.al-type-card').click(function() { 
        $('.al-type-card').removeClass('selected'); 
        $(this).addClass('selected'); 
    });
    
    // Form submission with AJAX
    $('#edit_liability_form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var btn = form.find('.al-btn-submit');
        var originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg || 'Liability updated successfully!');
                    setTimeout(function() {
                        window.location.href = '{{ route("bookkeeping.liabilities.index") }}';
                    }, 1500);
                } else {
                    toastr.error(response.msg || 'Failed to update liability');
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred while updating the liability.';
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







