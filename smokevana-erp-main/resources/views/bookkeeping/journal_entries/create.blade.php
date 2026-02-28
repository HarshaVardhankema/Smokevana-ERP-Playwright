@extends('layouts.app')
@section('title', 'Create Journal Entry')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
.je-page { background: #EAEDED; min-height: 100vh; padding-bottom: 40px; }
.je-header-banner { background: linear-gradient(180deg, #37475a 0%, #232f3e 100%); border-radius: 10px; padding: 28px 32px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; box-shadow: 0 4px 14px rgba(0,0,0,0.2); position: relative; overflow: hidden; }
.je-header-banner::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #ff9900, #e47911); z-index: 1; }
.je-header-banner h1, .je-header-banner .subtitle { color: #fff !important; }
.je-header-banner h1 { font-size: 28px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 12px; }
.je-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }
.je-btn-back { background: rgba(255,255,255,0.2); color: #fff; border: 1px solid rgba(255,255,255,0.35); padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
.je-btn-back:hover { background: rgba(255,255,255,0.3); color: #fff; text-decoration: none; }
.je-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 24px; border: 1px solid #D5D9D9; }
.je-card-header { padding: 20px 24px; border-bottom: 3px solid #ff9900; background: linear-gradient(135deg, #232f3e 0%, #37475a 100%); }
.je-card-title { font-size: 16px; font-weight: 600; color: #fff; margin: 0; display: flex; align-items: center; gap: 10px; }
.je-card-title i { color: #ff9900; }
.je-card-body { padding: 24px; }
.form-group { margin-bottom: 20px; }
.form-label { font-weight: 600; font-size: 13px; color: #374151; margin-bottom: 8px; display: block; }
.form-control { padding: 12px 16px; border: 1px solid #D5D9D9; border-radius: 10px; font-size: 14px; transition: all 0.2s ease; width: 100%; }
.form-control:focus { border-color: #FF9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.25); }
.lines-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
.lines-table th { background: linear-gradient(135deg, #232f3e 0%, #37475a 100%); color: #fff; font-size: 12px; font-weight: 600; padding: 14px 16px; text-align: left; border-bottom: 2px solid #ff9900; }
.lines-table td { padding: 12px 16px; border-bottom: 1px solid #e5e7eb; }
.lines-table .form-control { padding: 10px 12px; font-size: 13px; }
.btn-add-line { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%); color: #fff; border: 1px solid #C7511F; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
.btn-add-line:hover { opacity: 0.95; }
.btn-remove-line { background: #fee2e2; color: #dc2626; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.btn-remove-line:hover { background: #dc2626; color: #fff; }
.balance-info { background: #f7f8f8; border-radius: 10px; padding: 16px 20px; margin-top: 20px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #D5D9D9; }
.balance-info.balanced { background: #d1fae5; border-color: #10b981; }
.balance-info.unbalanced { background: #fee2e2; border-color: #dc2626; }
.balance-label { font-weight: 600; color: #6b7280; }
.balance-value { font-size: 20px; font-weight: 700; font-family: 'SF Mono', Monaco, monospace; }
.balance-info.balanced .balance-value { color: #059669; }
.balance-info.unbalanced .balance-value { color: #dc2626; }
.btn-submit { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%); color: #fff; border: 1px solid #C7511F; padding: 14px 32px; border-radius: 10px; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.2s ease; }
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255,153,0,0.4); opacity: 0.95; }
.btn-submit:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
</style>
@endsection

@section('content')
<section class="content je-page">
    <div class="je-header-banner">
        <div>
            <h1><i class="fas fa-plus-circle"></i> Create Journal Entry</h1>
            <p class="subtitle">Record a new double-entry transaction</p>
        </div>
        <a href="{{ route('bookkeeping.journal.index') }}" class="je-btn-back"><i class="fas fa-arrow-left"></i> Back to List</a>
    </div>

    <form id="journal-entry-form" action="{{ route('bookkeeping.journal.store') }}" method="POST">
        @csrf
        
        <div class="je-card">
            <div class="je-card-header">
                <h3 class="je-card-title"><i class="fas fa-info-circle"></i> Entry Details</h3>
            </div>
            <div class="je-card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Entry Date <span class="text-danger">*</span></label>
                            <input type="date" name="entry_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Entry Type</label>
                            <select name="entry_type" class="form-control">
                                @foreach($entryTypes as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Reference</label>
                            <input type="text" name="reference" class="form-control" placeholder="Optional reference">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Contact</label>
                            <select name="contact_id" class="form-control select2">
                                <option value="">-- Select Contact --</option>
                                @foreach($contacts as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Memo / Description</label>
                    <textarea name="memo" class="form-control" rows="2" placeholder="Description of this journal entry"></textarea>
                </div>
            </div>
        </div>

        <div class="je-card">
            <div class="je-card-header">
                <h3 class="je-card-title"><i class="fas fa-list-alt"></i> Journal Lines</h3>
            </div>
            <div class="je-card-body">
                <table class="lines-table" id="lines-table">
                    <thead>
                        <tr>
                            <th style="width: 35%;">Account</th>
                            <th style="width: 25%;">Description</th>
                            <th style="width: 15%; text-align: right;">Debit</th>
                            <th style="width: 15%; text-align: right;">Credit</th>
                            <th style="width: 10%; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="lines-body">
                        <tr class="line-row">
                            <td>
                                <select name="lines[0][account_id]" class="form-control account-select" required>
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="lines[0][description]" class="form-control" placeholder="Line description"></td>
                            <td><input type="number" name="lines[0][debit]" class="form-control debit-input" step="0.01" min="0" value="0" onchange="updateTotals()"></td>
                            <td><input type="number" name="lines[0][credit]" class="form-control credit-input" step="0.01" min="0" value="0" onchange="updateTotals()"></td>
                            <td style="text-align: center;"><button type="button" class="btn-remove-line" onclick="removeLine(this)"><i class="fas fa-times"></i></button></td>
                        </tr>
                        <tr class="line-row">
                            <td>
                                <select name="lines[1][account_id]" class="form-control account-select" required>
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="lines[1][description]" class="form-control" placeholder="Line description"></td>
                            <td><input type="number" name="lines[1][debit]" class="form-control debit-input" step="0.01" min="0" value="0" onchange="updateTotals()"></td>
                            <td><input type="number" name="lines[1][credit]" class="form-control credit-input" step="0.01" min="0" value="0" onchange="updateTotals()"></td>
                            <td style="text-align: center;"><button type="button" class="btn-remove-line" onclick="removeLine(this)"><i class="fas fa-times"></i></button></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8f9fe;">
                            <td colspan="2" style="text-align: right; font-weight: 700; padding-right: 20px;">TOTALS:</td>
                            <td style="text-align: right; font-weight: 700; color: #059669;" id="total-debit">$0.00</td>
                            <td style="text-align: right; font-weight: 700; color: #dc2626;" id="total-credit">$0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                
                <div style="margin-top: 16px;">
                    <button type="button" class="btn-add-line" onclick="addLine()"><i class="fas fa-plus"></i> Add Line</button>
                </div>

                <div class="balance-info" id="balance-info">
                    <span class="balance-label">Difference (Debit - Credit):</span>
                    <span class="balance-value" id="balance-value">$0.00</span>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 16px; justify-content: flex-end;">
            <a href="{{ route('bookkeeping.journal.index') }}" class="je-btn-back" style="background: #f3f4f6; color: #374151; border-color: #d1d5db;">Cancel</a>
            <button type="button" class="btn-submit" id="submit-btn" disabled onclick="submitForm('draft')"><i class="fas fa-save"></i> Save as Draft</button>
            <button type="button" class="btn-submit" id="post-btn" disabled onclick="submitForm('posted')"><i class="fas fa-check-circle"></i> Save & Post</button>
        </div>
    </form>
</section>
@endsection

@section('javascript')
<script>
var lineIndex = 2;
var accountOptions = `<option value="">Select Account</option>@foreach($accounts as $account)<option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->name }}</option>@endforeach`;

function addLine() {
    var row = `<tr class="line-row">
        <td><select name="lines[${lineIndex}][account_id]" class="form-control account-select" required>${accountOptions}</select></td>
        <td><input type="text" name="lines[${lineIndex}][description]" class="form-control" placeholder="Line description"></td>
        <td><input type="number" name="lines[${lineIndex}][debit]" class="form-control debit-input" step="0.01" min="0" value="0" onchange="updateTotals()"></td>
        <td><input type="number" name="lines[${lineIndex}][credit]" class="form-control credit-input" step="0.01" min="0" value="0" onchange="updateTotals()"></td>
        <td style="text-align: center;"><button type="button" class="btn-remove-line" onclick="removeLine(this)"><i class="fas fa-times"></i></button></td>
    </tr>`;
    $('#lines-body').append(row);
    lineIndex++;
}

function removeLine(btn) {
    if ($('.line-row').length > 2) {
        $(btn).closest('tr').remove();
        updateTotals();
    } else {
        alert('Minimum 2 lines required for a journal entry.');
    }
}

function updateTotals() {
    var totalDebit = 0;
    var totalCredit = 0;
    
    $('.debit-input').each(function() { totalDebit += parseFloat($(this).val()) || 0; });
    $('.credit-input').each(function() { totalCredit += parseFloat($(this).val()) || 0; });
    
    $('#total-debit').text('$' + totalDebit.toFixed(2));
    $('#total-credit').text('$' + totalCredit.toFixed(2));
    
    var diff = totalDebit - totalCredit;
    $('#balance-value').text('$' + Math.abs(diff).toFixed(2));
    
    var balanceInfo = $('#balance-info');
    if (Math.abs(diff) < 0.01 && totalDebit > 0) {
        balanceInfo.removeClass('unbalanced').addClass('balanced');
        $('#balance-value').text('$0.00 (Balanced)');
        $('#submit-btn, #post-btn').prop('disabled', false);
    } else {
        balanceInfo.removeClass('balanced').addClass('unbalanced');
        $('#submit-btn, #post-btn').prop('disabled', true);
    }
}

function submitForm(status) {
    // Disable buttons during submission
    $('#submit-btn, #post-btn').prop('disabled', true);
    
    var $clickedBtn = status === 'posted' ? $('#post-btn') : $('#submit-btn');
    var originalHtml = $clickedBtn.html();
    $clickedBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
    
    // Build form data with transformed lines
    var formData = {
        _token: $('input[name="_token"]').val(),
        entry_date: $('input[name="entry_date"]').val(),
        entry_type: $('select[name="entry_type"]').val(),
        reference: $('input[name="reference"]').val(),
        contact_id: $('select[name="contact_id"]').val(),
        memo: $('textarea[name="memo"]').val(),
        status: status,
        lines: []
    };
    
    // Collect line data and transform debit/credit to type/amount format
    $('.line-row').each(function() {
        var $row = $(this);
        var accountId = $row.find('.account-select').val();
        var description = $row.find('input[name$="[description]"]').val();
        var debit = parseFloat($row.find('.debit-input').val()) || 0;
        var credit = parseFloat($row.find('.credit-input').val()) || 0;
        
        // Add debit line if amount > 0
        if (debit > 0) {
            formData.lines.push({
                account_id: accountId,
                description: description,
                type: 'debit',
                amount: debit
            });
        }
        // Add credit line if amount > 0
        if (credit > 0) {
            formData.lines.push({
                account_id: accountId,
                description: description,
                type: 'credit',
                amount: credit
            });
        }
    });
    
    $.ajax({
        url: $('#journal-entry-form').attr('action'),
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(response.msg || 'Journal entry saved successfully.');
                }
                // Redirect to journal entries list
                window.location.href = "{{ route('bookkeeping.journal.index') }}";
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(response.msg || 'Error saving journal entry.');
                } else {
                    alert(response.msg || 'Error saving journal entry.');
                }
                resetButtons(originalHtml, $clickedBtn);
            }
        },
        error: function(xhr) {
            var errorMsg = 'Error saving journal entry.';
            if (xhr.responseJSON && xhr.responseJSON.msg) {
                errorMsg = xhr.responseJSON.msg;
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                var errors = xhr.responseJSON.errors;
                errorMsg = Object.values(errors).flat().join('\n');
            }
            if (typeof toastr !== 'undefined') {
                toastr.error(errorMsg);
            } else {
                alert(errorMsg);
            }
            resetButtons(originalHtml, $clickedBtn);
        }
    });
    
    function resetButtons(originalHtml, $btn) {
        $btn.html(originalHtml);
        updateTotals(); // Re-check if buttons should be disabled
    }
}

$(document).ready(function() {
    updateTotals();
    if ($.fn.select2) { $('.select2').select2(); }
});
</script>
@endsection
