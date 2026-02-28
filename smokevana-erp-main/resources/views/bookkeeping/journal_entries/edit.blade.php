@extends('layouts.app')
@section('title', 'Edit Journal Entry - ' . $entry->entry_number)

@section('css')
<style>
.je-page { background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%); min-height: 100vh; padding-bottom: 40px; }

.je-header-banner {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
    border-radius: 16px; padding: 28px 32px; margin-bottom: 24px;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;
    box-shadow: 0 8px 30px rgba(124, 58, 237, 0.25);
}
.je-header-banner h1, .je-header-banner .subtitle, .je-header-banner i { color: #fff !important; }
.je-header-banner h1 { font-size: 28px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 12px; }
.je-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }
.je-entry-badge {
    background: rgba(255,255,255,0.2);
    padding: 6px 14px;
    border-radius: 8px;
    font-family: 'SF Mono', Monaco, monospace;
    font-size: 14px;
    color: #fff;
}
.je-btn-back { background: #fff; color: #7c3aed; padding: 12px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
.je-btn-back:hover { background: #f5f3ff; text-decoration: none; color: #7c3aed; }

.je-grid { display: grid; grid-template-columns: 1fr 340px; gap: 24px; }
@media (max-width: 1200px) { .je-grid { grid-template-columns: 1fr; } }

.je-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); margin-bottom: 24px; overflow: hidden; }
.je-card-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 12px; }
.je-card-header-icon { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); display: flex; align-items: center; justify-content: center; color: #7c3aed; font-size: 18px; }
.je-card-header h3 { font-size: 16px; font-weight: 600; color: #1e1b4b; margin: 0; }
.je-card-body { padding: 24px; }

.je-form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px; }
@media (max-width: 768px) { .je-form-row { grid-template-columns: 1fr; } }
.je-form-group { margin-bottom: 20px; }
.je-form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }
.je-form-label .required { color: #dc2626; }
.je-form-input { width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px; color: #1e1b4b; background: #fff; }
.je-form-input:focus { border-color: #8b5cf6; outline: none; }
textarea.je-form-input { min-height: 80px; resize: vertical; }

.je-lines-table { width: 100%; border-collapse: collapse; }
.je-lines-table thead th { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: #fff; font-size: 11px; font-weight: 600; text-transform: uppercase; padding: 14px 16px; text-align: left; }
.je-lines-table tbody td { padding: 12px 16px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
.je-lines-table tfoot td { padding: 14px 16px; font-weight: 700; background: #f8f9fe; }
.je-line-input { width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; color: #1e1b4b; }
.je-line-input:focus { border-color: #8b5cf6; outline: none; }

.je-add-line { width: 100%; padding: 14px; border: 2px dashed #c4b5fd; border-radius: 10px; background: transparent; color: #7c3aed; font-weight: 600; cursor: pointer; margin-top: 16px; }
.je-add-line:hover { background: #faf5ff; border-color: #8b5cf6; }
.je-remove-line { width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; border: none; cursor: pointer; }
.je-remove-line:hover { background: #dc2626; color: #fff; }

.je-debit-input { border-color: #d1fae5 !important; background: #f0fdf4; }
.je-credit-input { border-color: #fee2e2 !important; background: #fef2f2; }

/* Summary Card */
.je-summary { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); position: sticky; top: 24px; }
.je-summary-header { background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); padding: 20px 24px; border-radius: 16px 16px 0 0; }
.je-summary-header h3 { font-size: 18px; font-weight: 600; color: #fff !important; margin: 0; display: flex; align-items: center; gap: 10px; }
.je-summary-body { padding: 24px; }

.je-balance-status { text-align: center; padding: 20px; border-radius: 12px; margin-bottom: 20px; }
.je-balance-status.balanced { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); }
.je-balance-status.unbalanced { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); }
.je-balance-icon { font-size: 32px; margin-bottom: 8px; }
.je-balance-status.balanced .je-balance-icon { color: #059669; }
.je-balance-status.unbalanced .je-balance-icon { color: #dc2626; }
.je-balance-text { font-weight: 600; }
.je-balance-status.balanced .je-balance-text { color: #059669; }
.je-balance-status.unbalanced .je-balance-text { color: #dc2626; }

.je-total-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
.je-total-label { font-size: 14px; color: #6b7280; }
.je-total-value { font-weight: 700; font-family: 'SF Mono', Monaco, monospace; }
.je-total-value.debit { color: #059669; }
.je-total-value.credit { color: #dc2626; }

.je-actions { padding: 20px 24px; border-top: 1px solid #f1f5f9; }
.je-btn-save { width: 100%; padding: 16px; border: none; border-radius: 10px; background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); color: #fff; font-weight: 600; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 12px; }
.je-btn-save:hover { transform: translateY(-2px); }
.je-btn-save:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
.je-btn-draft { width: 100%; padding: 14px; border: 2px solid #e5e7eb; border-radius: 10px; background: #fff; color: #6b7280; font-weight: 600; cursor: pointer; margin-bottom: 12px; }
.je-btn-draft:hover { background: #f9fafb; }
.je-btn-cancel { width: 100%; padding: 14px; border: 2px solid #fecaca; border-radius: 10px; background: #fff; color: #dc2626; font-weight: 600; text-align: center; display: block; text-decoration: none; }
.je-btn-cancel:hover { background: #fef2f2; text-decoration: none; }

/* Status Info */
.je-status-info { background: #fef3c7; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
.je-status-info i { color: #f59e0b; font-size: 20px; }
.je-status-info span { color: #92400e; font-size: 13px; font-weight: 500; }
</style>
@endsection

@section('content')
<section class="content je-page">
    <div class="je-header-banner">
        <div>
            <h1>
                <i class="fas fa-edit"></i> 
                Edit Journal Entry
                <span class="je-entry-badge">{{ $entry->entry_number }}</span>
            </h1>
            <p class="subtitle">Modify journal entry details</p>
        </div>
        <a href="{{ route('bookkeeping.journal.show', $entry->id) }}" class="je-btn-back"><i class="fas fa-arrow-left"></i> Back to Entry</a>
    </div>

    <div class="je-status-info">
        <i class="fas fa-info-circle"></i>
        <span>This entry is currently in <strong>Draft</strong> status. You can edit and save changes, or save and post directly.</span>
    </div>

    <form action="{{ route('bookkeeping.journal.update', $entry->id) }}" method="POST" id="journal_form">
        @csrf
        @method('PUT')
        <div class="je-grid">
            <div class="je-main">
                <!-- Entry Details -->
                <div class="je-card">
                    <div class="je-card-header">
                        <div class="je-card-header-icon"><i class="fas fa-info-circle"></i></div>
                        <h3>Entry Details</h3>
                    </div>
                    <div class="je-card-body">
                        <div class="je-form-row">
                            <div class="je-form-group">
                                <label class="je-form-label">Entry Date <span class="required">*</span></label>
                                <input type="text" name="entry_date" class="je-form-input je-datepicker" value="{{ $entry->entry_date->format('m/d/Y') }}" required placeholder="MM/DD/YYYY">
                            </div>
                            <div class="je-form-group">
                                <label class="je-form-label">Entry Type</label>
                                <select name="entry_type" class="je-form-input">
                                    @foreach($entryTypes ?? [] as $key => $value)
                                    <option value="{{ $key }}" {{ $entry->entry_type === $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="je-form-row">
                            <div class="je-form-group">
                                <label class="je-form-label">Reference #</label>
                                <input type="text" name="reference_number" class="je-form-input" placeholder="Optional reference" value="{{ $entry->source_document }}">
                            </div>
                            <div class="je-form-group">
                                <label class="je-form-label">Contact</label>
                                <select name="contact_id" class="je-form-input select2">
                                    <option value="">Select contact (optional)</option>
                                    @foreach($contacts ?? [] as $id => $name)
                                    <option value="{{ $id }}" {{ $entry->contact_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="je-form-group">
                            <label class="je-form-label">Memo</label>
                            <textarea name="memo" class="je-form-input" placeholder="Entry description...">{{ $entry->memo }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Entry Lines -->
                <div class="je-card">
                    <div class="je-card-header">
                        <div class="je-card-header-icon"><i class="fas fa-list"></i></div>
                        <h3>Entry Lines</h3>
                    </div>
                    <div class="je-card-body">
                        <table class="je-lines-table">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th style="width: 140px;">Debit</th>
                                    <th style="width: 140px;">Credit</th>
                                    <th>Memo</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="entry_lines">
                                @php $lineIndex = 0; @endphp
                                @foreach($entry->lines as $line)
                                <tr class="entry-line">
                                    <td>
                                        <select name="lines[{{ $lineIndex }}][account_id]" class="je-line-input select2" required>
                                            <option value="">Select account...</option>
                                            @foreach($accounts as $id => $name)
                                            <option value="{{ $id }}" {{ $line->account_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="lines[{{ $lineIndex }}][debit]" class="je-line-input je-debit-input debit-input" step="0.01" placeholder="0.00" value="{{ $line->type === 'debit' ? number_format($line->amount, 2, '.', '') : '' }}"></td>
                                    <td><input type="number" name="lines[{{ $lineIndex }}][credit]" class="je-line-input je-credit-input credit-input" step="0.01" placeholder="0.00" value="{{ $line->type === 'credit' ? number_format($line->amount, 2, '.', '') : '' }}"></td>
                                    <td><input type="text" name="lines[{{ $lineIndex }}][memo]" class="je-line-input" placeholder="Line memo" value="{{ $line->description }}"></td>
                                    <td><button type="button" class="je-remove-line"><i class="fas fa-times"></i></button></td>
                                </tr>
                                @php $lineIndex++; @endphp
                                @endforeach
                                @if($entry->lines->count() < 2)
                                @for($i = $entry->lines->count(); $i < 2; $i++)
                                <tr class="entry-line">
                                    <td>
                                        <select name="lines[{{ $lineIndex }}][account_id]" class="je-line-input select2" required>
                                            <option value="">Select account...</option>
                                            @foreach($accounts as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="lines[{{ $lineIndex }}][debit]" class="je-line-input je-debit-input debit-input" step="0.01" placeholder="0.00"></td>
                                    <td><input type="number" name="lines[{{ $lineIndex }}][credit]" class="je-line-input je-credit-input credit-input" step="0.01" placeholder="0.00"></td>
                                    <td><input type="text" name="lines[{{ $lineIndex }}][memo]" class="je-line-input" placeholder="Line memo"></td>
                                    <td><button type="button" class="je-remove-line"><i class="fas fa-times"></i></button></td>
                                </tr>
                                @php $lineIndex++; @endphp
                                @endfor
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="text-align: right;">Totals:</td>
                                    <td class="debit-total" style="color: #059669; font-family: 'SF Mono', Monaco, monospace;">$0.00</td>
                                    <td class="credit-total" style="color: #dc2626; font-family: 'SF Mono', Monaco, monospace;">$0.00</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                        <button type="button" class="je-add-line" id="add_line"><i class="fas fa-plus"></i> Add Another Line</button>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="je-sidebar">
                <div class="je-summary">
                    <div class="je-summary-header">
                        <h3><i class="fas fa-balance-scale"></i> Balance Check</h3>
                    </div>
                    <div class="je-summary-body">
                        <div class="je-balance-status unbalanced" id="balance_status">
                            <div class="je-balance-icon"><i class="fas fa-exclamation-circle"></i></div>
                            <div class="je-balance-text">Entry Not Balanced</div>
                        </div>
                        <div class="je-total-row">
                            <span class="je-total-label">Total Debits</span>
                            <span class="je-total-value debit" id="total_debits">$0.00</span>
                        </div>
                        <div class="je-total-row">
                            <span class="je-total-label">Total Credits</span>
                            <span class="je-total-value credit" id="total_credits">$0.00</span>
                        </div>
                        <div class="je-total-row">
                            <span class="je-total-label">Difference</span>
                            <span class="je-total-value" id="difference">$0.00</span>
                        </div>
                    </div>
                    <div class="je-actions">
                        <button type="submit" name="status" value="posted" class="je-btn-save" id="btn_post" disabled><i class="fas fa-check"></i> Save & Post</button>
                        <button type="submit" name="status" value="draft" class="je-btn-draft"><i class="fas fa-save"></i> Save as Draft</button>
                        <a href="{{ route('bookkeeping.journal.show', $entry->id) }}" class="je-btn-cancel">Cancel</a>
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
    $('.select2').select2({ width: '100%' });
    $('.je-datepicker').datepicker({ format: 'mm/dd/yyyy', autoclose: true, todayHighlight: true });
    
    var lineIndex = {{ $lineIndex }};
    
    // Build account options string for dynamic rows
    var accountOptions = '<option value="">Select account...</option>';
    @foreach($accounts as $id => $name)
    accountOptions += '<option value="{{ $id }}">{{ addslashes($name) }}</option>';
    @endforeach
    
    $('#add_line').click(function() {
        var newRow = `
        <tr class="entry-line">
            <td>
                <select name="lines[${lineIndex}][account_id]" class="je-line-input select2" required>
                    ${accountOptions}
                </select>
            </td>
            <td><input type="number" name="lines[${lineIndex}][debit]" class="je-line-input je-debit-input debit-input" step="0.01" placeholder="0.00"></td>
            <td><input type="number" name="lines[${lineIndex}][credit]" class="je-line-input je-credit-input credit-input" step="0.01" placeholder="0.00"></td>
            <td><input type="text" name="lines[${lineIndex}][memo]" class="je-line-input" placeholder="Line memo"></td>
            <td><button type="button" class="je-remove-line"><i class="fas fa-times"></i></button></td>
        </tr>`;
        $('#entry_lines').append(newRow);
        $('#entry_lines tr:last .select2').select2({ width: '100%' });
        lineIndex++;
    });
    
    $(document).on('click', '.je-remove-line', function() {
        if ($('.entry-line').length > 2) {
            $(this).closest('tr').remove();
            updateTotals();
        } else {
            toastr.warning('You must have at least 2 entry lines.');
        }
    });
    
    $(document).on('input', '.debit-input, .credit-input', updateTotals);
    
    function updateTotals() {
        var totalDebits = 0, totalCredits = 0;
        $('.debit-input').each(function() { totalDebits += parseFloat($(this).val()) || 0; });
        $('.credit-input').each(function() { totalCredits += parseFloat($(this).val()) || 0; });
        
        $('.debit-total').text('$' + totalDebits.toFixed(2));
        $('.credit-total').text('$' + totalCredits.toFixed(2));
        $('#total_debits').text('$' + totalDebits.toFixed(2));
        $('#total_credits').text('$' + totalCredits.toFixed(2));
        
        var diff = Math.abs(totalDebits - totalCredits);
        $('#difference').text('$' + diff.toFixed(2));
        
        if (totalDebits > 0 && totalCredits > 0 && Math.abs(totalDebits - totalCredits) < 0.01) {
            $('#balance_status').removeClass('unbalanced').addClass('balanced');
            $('#balance_status .je-balance-icon').html('<i class="fas fa-check-circle"></i>');
            $('#balance_status .je-balance-text').text('Entry Balanced');
            $('#btn_post').prop('disabled', false);
        } else {
            $('#balance_status').removeClass('balanced').addClass('unbalanced');
            $('#balance_status .je-balance-icon').html('<i class="fas fa-exclamation-circle"></i>');
            $('#balance_status .je-balance-text').text('Entry Not Balanced');
            $('#btn_post').prop('disabled', true);
        }
    }
    
    // Initial totals calculation
    updateTotals();
    
    // Handle form submission via AJAX
    $('#journal_form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = form.find('button[type="submit"].clicked').length ? form.find('button[type="submit"].clicked') : form.find('.je-btn-draft');
        var originalText = submitBtn.html();
        var status = submitBtn.val() || 'draft';
        
        // Collect lines data
        var lines = [];
        
        $('.entry-line').each(function(index) {
            var accountId = $(this).find('select[name*="account_id"]').val();
            var debit = parseFloat($(this).find('.debit-input').val()) || 0;
            var credit = parseFloat($(this).find('.credit-input').val()) || 0;
            var memo = $(this).find('input[name*="memo"]').val() || '';
            
            if (accountId && (debit > 0 || credit > 0)) {
                if (debit > 0) {
                    lines.push({
                        account_id: accountId,
                        type: 'debit',
                        amount: debit,
                        description: memo
                    });
                }
                if (credit > 0) {
                    lines.push({
                        account_id: accountId,
                        type: 'credit',
                        amount: credit,
                        description: memo
                    });
                }
            }
        });
        
        if (lines.length < 2) {
            toastr.error('Please add at least 2 journal entry lines (one debit and one credit)');
            return;
        }
        
        // Prepare form data
        var formData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT',
            entry_date: $('input[name="entry_date"]').val(),
            entry_type: $('select[name="entry_type"]').val(),
            contact_id: $('select[name="contact_id"]').val(),
            reference_number: $('input[name="reference_number"]').val(),
            memo: $('textarea[name="memo"]').val(),
            status: status,
            lines: lines
        };
        
        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg || 'Journal entry updated successfully!');
                    setTimeout(function() {
                        window.location.href = '{{ route("bookkeeping.journal.show", $entry->id) }}';
                    }, 1500);
                } else {
                    toastr.error(response.msg || 'Failed to update journal entry');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred while updating the journal entry.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.msg) {
                        msg = xhr.responseJSON.msg;
                    } else if (xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        msg = Object.values(errors).flat().join('<br>');
                    }
                }
                toastr.error(msg);
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Track which button was clicked
    $('#journal_form button[type="submit"]').on('click', function() {
        $('#journal_form button[type="submit"]').removeClass('clicked');
        $(this).addClass('clicked');
    });
});
</script>
@endsection








