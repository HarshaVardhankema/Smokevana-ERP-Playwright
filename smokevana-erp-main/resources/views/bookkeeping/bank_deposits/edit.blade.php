@extends('layouts.app')
@section('title', 'Edit Bank Deposit')

@section('css')
<style>
/* Bank Deposit Edit - Professional Purple Theme */
.bd-page { background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%); min-height: 100vh; padding-bottom: 40px; }

.bd-header-banner {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
    border-radius: 16px; padding: 28px 32px; margin-bottom: 24px;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;
    box-shadow: 0 8px 30px rgba(124, 58, 237, 0.25); position: relative; overflow: hidden;
}
.bd-header-banner::before { content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); border-radius: 50%; }
.bd-header-banner h1, .bd-header-banner .subtitle, .bd-header-banner i { color: #fff !important; }
.bd-header-banner h1 { font-size: 28px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 12px; }
.bd-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }
.bd-btn-back { background: #fff; color: #7c3aed; padding: 12px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
.bd-btn-back:hover { background: #f5f3ff; text-decoration: none; color: #6d28d9; transform: translateY(-2px); }

.bd-grid { display: grid; grid-template-columns: 1fr 340px; gap: 24px; }
@media (max-width: 1200px) { .bd-grid { grid-template-columns: 1fr; } }

.bd-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); margin-bottom: 24px; overflow: hidden; border: 1px solid rgba(139, 92, 246, 0.06); }
.bd-card-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 12px; }
.bd-card-header-icon { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); display: flex; align-items: center; justify-content: center; color: #7c3aed; font-size: 18px; }
.bd-card-header h3 { font-size: 16px; font-weight: 600; color: #1e1b4b; margin: 0; }
.bd-card-body { padding: 24px; }

.bd-form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px; }
@media (max-width: 768px) { .bd-form-row { grid-template-columns: 1fr; } }
.bd-form-group { margin-bottom: 20px; }
.bd-form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }
.bd-form-label .required { color: #dc2626; }
.bd-form-input { width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px; color: #1e1b4b; background: #fff; transition: all 0.2s ease; }
.bd-form-input:focus { border-color: #8b5cf6; outline: none; box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1); }
textarea.bd-form-input { min-height: 80px; resize: vertical; }

.bd-lines-table { width: 100%; border-collapse: collapse; }
.bd-lines-table thead th { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: #fff; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 16px; text-align: left; }
.bd-lines-table tbody td { padding: 12px 16px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
.bd-lines-table tbody tr:hover { background: #faf5ff; }
.bd-lines-table tfoot td { padding: 14px 16px; font-weight: 700; background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); }
.bd-line-input { width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; color: #1e1b4b; transition: all 0.2s ease; }
.bd-line-input:focus { border-color: #8b5cf6; outline: none; }

.bd-add-line { width: 100%; padding: 14px; border: 2px dashed #c4b5fd; border-radius: 10px; background: transparent; color: #7c3aed; font-weight: 600; cursor: pointer; margin-top: 16px; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 8px; }
.bd-add-line:hover { background: #faf5ff; border-color: #8b5cf6; }
.bd-remove-line { width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; border: none; cursor: pointer; transition: all 0.2s ease; }
.bd-remove-line:hover { background: #dc2626; color: #fff; }

.bd-amount-input { text-align: right; font-family: 'SF Mono', Monaco, monospace; background: #f0fdf4; border-color: #d1fae5 !important; }

/* Summary Card */
.bd-summary { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); position: sticky; top: 24px; border: 1px solid rgba(139, 92, 246, 0.06); }
.bd-summary-header { background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); padding: 20px 24px; border-radius: 16px 16px 0 0; }
.bd-summary-header h3 { font-size: 18px; font-weight: 600; color: #fff !important; margin: 0; display: flex; align-items: center; gap: 10px; }
.bd-summary-body { padding: 24px; }

.bd-total-display { text-align: center; padding: 24px; border-radius: 12px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); margin-bottom: 20px; }
.bd-total-icon { font-size: 36px; color: #d97706; margin-bottom: 8px; }
.bd-total-value { font-size: 32px; font-weight: 700; color: #d97706; font-family: 'SF Mono', Monaco, monospace; }
.bd-total-label { font-size: 12px; color: #92400e; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

.bd-info-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
.bd-info-label { font-size: 14px; color: #6b7280; }
.bd-info-value { font-weight: 600; color: #1e1b4b; }

.bd-actions { padding: 20px 24px; border-top: 1px solid #f1f5f9; }
.bd-btn-save { width: 100%; padding: 16px; border: none; border-radius: 10px; background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #fff; font-weight: 600; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 12px; transition: all 0.2s ease; box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3); }
.bd-btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4); }
.bd-btn-save:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
.bd-btn-cancel { width: 100%; padding: 14px; border: 2px solid #fecaca; border-radius: 10px; background: #fff; color: #dc2626; font-weight: 600; text-align: center; display: block; text-decoration: none; transition: all 0.2s ease; }
.bd-btn-cancel:hover { background: #fef2f2; text-decoration: none; color: #dc2626; }

@keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
.bd-card, .bd-summary { animation: fadeInUp 0.4s ease forwards; }
</style>
@endsection

@section('content')
<section class="content bd-page">
    <div class="bd-header-banner">
        <div>
            <h1><i class="fas fa-edit"></i> Edit Deposit #{{ $deposit->deposit_number ?? 'N/A' }}</h1>
            <p class="subtitle">Modify pending deposit details</p>
        </div>
        <a href="{{ route('bookkeeping.deposits.show', $deposit->id) }}" class="bd-btn-back"><i class="fas fa-arrow-left"></i> Back to Deposit</a>
    </div>

    <form action="{{ route('bookkeeping.deposits.store') }}" method="POST" id="deposit_form">
        @csrf
        <input type="hidden" name="deposit_id" value="{{ $deposit->id }}">
        
        <div class="bd-grid">
            <div class="bd-main">
                <!-- Deposit Details -->
                <div class="bd-card">
                    <div class="bd-card-header">
                        <div class="bd-card-header-icon"><i class="fas fa-university"></i></div>
                        <h3>Deposit Details</h3>
                    </div>
                    <div class="bd-card-body">
                        <div class="bd-form-row">
                            <div class="bd-form-group">
                                <label class="bd-form-label">Deposit To <span class="required">*</span></label>
                                <select name="deposit_to_account_id" class="bd-form-input select2" required>
                                    <option value="">Select bank account...</option>
                                    @foreach($bankAccounts ?? [] as $account)
                                    <option value="{{ $account->id }}" {{ $deposit->deposit_to_account_id == $account->id ? 'selected' : '' }}>{{ $account->name }} ({{ number_format($account->current_balance, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="bd-form-group">
                                <label class="bd-form-label">Deposit Date <span class="required">*</span></label>
                                <input type="text" name="deposit_date" class="bd-form-input bd-datepicker" value="{{ \Carbon\Carbon::parse($deposit->deposit_date)->format('m/d/Y') }}" required placeholder="MM/DD/YYYY">
                            </div>
                        </div>
                        <div class="bd-form-group">
                            <label class="bd-form-label">Memo</label>
                            <textarea name="memo" class="bd-form-input" placeholder="Deposit description or notes...">{{ $deposit->memo }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Deposit Lines -->
                <div class="bd-card">
                    <div class="bd-card-header">
                        <div class="bd-card-header-icon"><i class="fas fa-list"></i></div>
                        <h3>Deposit Items</h3>
                    </div>
                    <div class="bd-card-body">
                        <table class="bd-lines-table">
                            <thead>
                                <tr>
                                    <th>Received From</th>
                                    <th>Account</th>
                                    <th>Payment Method</th>
                                    <th>Ref #</th>
                                    <th style="width: 130px;">Amount</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="deposit_lines">
                                @foreach($deposit->lines as $index => $line)
                                <tr class="deposit-line" data-index="{{ $index }}">
                                    <td>
                                        <select name="lines[{{ $index }}][contact_id]" class="bd-line-input select2-contact">
                                            <option value="">Select contact...</option>
                                            @foreach($contacts ?? [] as $id => $name)
                                            <option value="{{ $id }}" {{ $line->contact_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="lines[{{ $index }}][account_id]" class="bd-line-input select2-account" required>
                                            <option value="">Select account...</option>
                                            @foreach($accounts ?? [] as $id => $name)
                                            <option value="{{ $id }}" {{ $line->account_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="lines[{{ $index }}][payment_method]" class="bd-line-input">
                                            <option value="">Select...</option>
                                            <option value="cash" {{ $line->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="check" {{ $line->payment_method == 'check' ? 'selected' : '' }}>Check</option>
                                            <option value="card" {{ $line->payment_method == 'card' ? 'selected' : '' }}>Card</option>
                                            <option value="wire" {{ $line->payment_method == 'wire' ? 'selected' : '' }}>Wire Transfer</option>
                                            <option value="ach" {{ $line->payment_method == 'ach' ? 'selected' : '' }}>ACH</option>
                                            <option value="other" {{ $line->payment_method == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="lines[{{ $index }}][ref_no]" class="bd-line-input" placeholder="Check #, Ref..." value="{{ $line->ref_no }}">
                                    </td>
                                    <td>
                                        <input type="number" name="lines[{{ $index }}][amount]" class="bd-line-input bd-amount-input amount-input" step="0.01" min="0.01" placeholder="0.00" value="{{ $line->amount }}" required>
                                    </td>
                                    <td>
                                        <button type="button" class="bd-remove-line" title="Remove line"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="text-align: right; font-weight: 600; color: #1e1b4b;">
                                        <i class="fas fa-calculator" style="color: #7c3aed; margin-right: 8px;"></i> Total Deposit:
                                    </td>
                                    <td class="deposit-total" style="color: #059669; font-family: 'SF Mono', Monaco, monospace; font-size: 18px;">${{ number_format($deposit->lines->sum('amount'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        <button type="button" class="bd-add-line" id="add_line_btn">
                            <i class="fas fa-plus-circle"></i> Add Another Item
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="bd-sidebar">
                <div class="bd-summary">
                    <div class="bd-summary-header">
                        <h3><i class="fas fa-receipt"></i> Deposit Summary</h3>
                    </div>
                    <div class="bd-summary-body">
                        <div class="bd-total-display">
                            <div class="bd-total-icon"><i class="fas fa-edit"></i></div>
                            <div class="bd-total-value" id="summary_total">${{ number_format($deposit->lines->sum('amount'), 2) }}</div>
                            <div class="bd-total-label">Editing Deposit</div>
                        </div>

                        <div class="bd-info-row">
                            <span class="bd-info-label">Items</span>
                            <span class="bd-info-value" id="summary_items">{{ $deposit->lines->count() }}</span>
                        </div>
                        <div class="bd-info-row">
                            <span class="bd-info-label">Deposit #</span>
                            <span class="bd-info-value">{{ $deposit->deposit_number }}</span>
                        </div>
                        <div class="bd-info-row">
                            <span class="bd-info-label">Status</span>
                            <span class="bd-info-value" style="color: #f59e0b;"><i class="fas fa-clock"></i> Pending</span>
                        </div>
                    </div>
                    <div class="bd-actions">
                        <button type="submit" class="bd-btn-save" id="save_btn">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="{{ route('bookkeeping.deposits.show', $deposit->id) }}" class="bd-btn-cancel">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var lineIndex = {{ $deposit->lines->count() }};
    
    // Initialize datepicker
    if ($.fn.datepicker) {
        $('.bd-datepicker').datepicker({ dateFormat: 'mm/dd/yy', autoclose: true });
    }
    
    // Initialize Select2
    function initSelect2() {
        if ($.fn.select2) {
            $('.select2').select2({ width: '100%', placeholder: 'Select...' });
            $('.select2-contact').select2({ width: '100%', placeholder: 'Select contact...' });
            $('.select2-account').select2({ width: '100%', placeholder: 'Select account...' });
        }
    }
    initSelect2();
    
    // Calculate totals
    function calculateTotals() {
        var total = 0;
        var itemCount = 0;
        
        $('.amount-input').each(function() {
            var amount = parseFloat($(this).val()) || 0;
            if (amount > 0) {
                total += amount;
                itemCount++;
            }
        });
        
        $('.deposit-total').text('$' + total.toFixed(2));
        $('#summary_total').text('$' + total.toFixed(2));
        $('#summary_items').text(itemCount);
    }
    
    // Add new line
    $('#add_line_btn').click(function() {
        var newLine = `
            <tr class="deposit-line" data-index="${lineIndex}">
                <td>
                    <select name="lines[${lineIndex}][contact_id]" class="bd-line-input select2-contact">
                        <option value="">Select contact...</option>
                        @foreach($contacts ?? [] as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="lines[${lineIndex}][account_id]" class="bd-line-input select2-account" required>
                        <option value="">Select account...</option>
                        @foreach($accounts ?? [] as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="lines[${lineIndex}][payment_method]" class="bd-line-input">
                        <option value="">Select...</option>
                        <option value="cash">Cash</option>
                        <option value="check">Check</option>
                        <option value="card">Card</option>
                        <option value="wire">Wire Transfer</option>
                        <option value="ach">ACH</option>
                        <option value="other">Other</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="lines[${lineIndex}][ref_no]" class="bd-line-input" placeholder="Check #, Ref...">
                </td>
                <td>
                    <input type="number" name="lines[${lineIndex}][amount]" class="bd-line-input bd-amount-input amount-input" step="0.01" min="0.01" placeholder="0.00" required>
                </td>
                <td>
                    <button type="button" class="bd-remove-line" title="Remove line"><i class="fas fa-times"></i></button>
                </td>
            </tr>
        `;
        
        $('#deposit_lines').append(newLine);
        lineIndex++;
        initSelect2();
    });
    
    // Remove line
    $(document).on('click', '.bd-remove-line', function() {
        var rows = $('#deposit_lines tr').length;
        if (rows > 1) {
            $(this).closest('tr').remove();
            calculateTotals();
        } else {
            toastr.warning('At least one deposit line is required.');
        }
    });
    
    // Calculate on amount change
    $(document).on('input change', '.amount-input', function() {
        calculateTotals();
    });
    
    // Form submission
    $('#deposit_form').on('submit', function(e) {
        e.preventDefault();
        
        var total = 0;
        $('.amount-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        
        if (total <= 0) {
            toastr.error('Please add at least one deposit item with an amount.');
            return false;
        }
        
        var $btn = $('#save_btn');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg || 'Deposit updated successfully!');
                    setTimeout(function() {
                        window.location.href = '{{ route("bookkeeping.deposits.show", $deposit->id) }}';
                    }, 1000);
                } else {
                    toastr.error(response.msg || 'Failed to update deposit.');
                    $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred while updating the deposit.';
                if (xhr.responseJSON) {
                    msg = xhr.responseJSON.msg || xhr.responseJSON.message || msg;
                }
                toastr.error(msg);
                $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
            }
        });
    });
    
    // Initial calculation
    calculateTotals();
});
</script>
@endsection
