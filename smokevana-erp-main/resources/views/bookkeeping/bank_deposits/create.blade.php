@extends('layouts.app')
@section('title', 'Create Bank Deposit')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.bd-page { background: #EAEDED; min-height: 100vh; padding-bottom: 40px; }
.bd-header-banner { background: linear-gradient(180deg, #37475a 0%, #232f3e 100%); border-radius: 10px; padding: 28px 32px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; box-shadow: 0 4px 14px rgba(0,0,0,0.2); position: relative; overflow: hidden; }
.bd-header-banner::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #ff9900, #e47911); z-index: 1; }
.bd-header-banner h1, .bd-header-banner .subtitle { color: #fff !important; }
.bd-header-banner h1 { font-size: 28px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 12px; }
.bd-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }
.bd-btn-back { background: rgba(255,255,255,0.2); color: #fff; border: 1px solid rgba(255,255,255,0.35); padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
.bd-btn-back:hover { background: rgba(255,255,255,0.3); color: #fff; text-decoration: none; }
.bd-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 24px; border: 1px solid #D5D9D9; }
.bd-card-header { padding: 20px 24px; border-bottom: 3px solid #ff9900; background: linear-gradient(135deg, #232f3e 0%, #37475a 100%); }
.bd-card-title { font-size: 16px; font-weight: 600; color: #fff; margin: 0; display: flex; align-items: center; gap: 10px; }
.bd-card-title i { color: #ff9900; }
.bd-card-body { padding: 24px; }
.form-group { margin-bottom: 20px; }
.form-label { font-weight: 600; font-size: 13px; color: #374151; margin-bottom: 8px; display: block; }
.form-control { padding: 12px 16px; border: 1px solid #D5D9D9; border-radius: 10px; font-size: 14px; transition: all 0.2s ease; width: 100%; }
.form-control:focus { border-color: #FF9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.25); }
.bank-account-card { background: #f7f8f8; border-radius: 12px; padding: 16px 20px; margin-bottom: 12px; cursor: pointer; transition: all 0.2s ease; border: 2px solid #D5D9D9; }
.bank-account-card:hover { border-color: #ff9900; }
.bank-account-card.selected { border-color: #ff9900; box-shadow: 0 0 0 2px rgba(255,153,0,0.25); }
.bank-account-card input[type="radio"] { display: none; }
.bank-account-name { font-weight: 600; color: #232f3e; font-size: 15px; }
.bank-account-balance { font-family: 'SF Mono', Monaco, monospace; color: #ff9900; font-weight: 700; font-size: 18px; margin-top: 4px; }
.deposit-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
.deposit-table th { background: linear-gradient(135deg, #232f3e 0%, #37475a 100%); color: #fff; font-size: 12px; font-weight: 600; padding: 14px 16px; text-align: left; border-bottom: 2px solid #ff9900; }
.deposit-table td { padding: 12px 16px; border-bottom: 1px solid #e5e7eb; }
.payment-checkbox { width: 20px; height: 20px; cursor: pointer; }
.payment-amount { font-family: 'SF Mono', Monaco, monospace; font-weight: 600; color: #059669; }
.total-section { background: #fff8e7; border-radius: 10px; padding: 20px; margin-top: 20px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #ffb84d; }
.total-label { font-weight: 600; color: #b45309; font-size: 16px; }
.total-value { font-size: 28px; font-weight: 700; font-family: 'SF Mono', Monaco, monospace; color: #ff9900; }
.btn-submit { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%); color: #fff; border: 1px solid #C7511F; padding: 14px 32px; border-radius: 10px; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.2s ease; }
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255,153,0,0.4); opacity: 0.95; }
.empty-state { text-align: center; padding: 40px 20px; color: #9ca3af; }
.empty-state i { font-size: 48px; margin-bottom: 16px; opacity: 0.5; }
</style>
@endsection

@section('content')
<section class="content bd-page">
    <div class="bd-header-banner">
        <div>
            <h1><i class="fas fa-piggy-bank"></i> Create Bank Deposit</h1>
            <p class="subtitle">Record payments received into your bank account</p>
        </div>
        <a href="{{ route('bookkeeping.deposits.index') }}" class="bd-btn-back"><i class="fas fa-arrow-left"></i> Back to Deposits</a>
    </div>

    <form action="{{ route('bookkeeping.deposits.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <!-- Deposit Details -->
                <div class="bd-card">
                    <div class="bd-card-header">
                        <h3 class="bd-card-title"><i class="fas fa-info-circle"></i> Deposit Details</h3>
                    </div>
                    <div class="bd-card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Deposit Date <span class="text-danger">*</span></label>
                                    <input type="text" id="deposit_date" name="deposit_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Reference Number</label>
                                    <input type="text" name="reference_no" class="form-control" placeholder="e.g., DEP-001">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Deposit Method</label>
                                    <select name="deposit_method" class="form-control">
                                        <option value="cash">Cash</option>
                                        <option value="check">Check</option>
                                        <option value="wire_transfer">Wire Transfer</option>
                                        <option value="ach">ACH</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Memo</label>
                            <textarea name="memo" class="form-control" rows="2" placeholder="Optional notes about this deposit"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Select Payments -->
                <div class="bd-card">
                    <div class="bd-card-header">
                        <h3 class="bd-card-title"><i class="fas fa-money-check-alt"></i> Select Payments to Deposit</h3>
                    </div>
                    <div class="bd-card-body">
                        @if($undepositedPayments->count() > 0)
                        <table class="deposit-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"><input type="checkbox" id="select-all" class="payment-checkbox"></th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Reference</th>
                                    <th>Method</th>
                                    <th style="text-align: right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($undepositedPayments as $payment)
                                <tr>
                                    <td><input type="checkbox" name="payment_ids[]" value="{{ $payment->id }}" class="payment-checkbox payment-item" data-amount="{{ $payment->amount }}"></td>
                                    <td>{{ \Carbon\Carbon::parse($payment->paid_on)->format('M d, Y') }}</td>
                                    <td>{{ optional(optional($payment->transaction)->contact)->name ?? 'N/A' }}</td>
                                    <td>{{ $payment->payment_ref_no ?? '-' }}</td>
                                    <td>{{ ucfirst($payment->method) }}</td>
                                    <td style="text-align: right;"><span class="payment-amount">${{ number_format($payment->amount, 2) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <p>No undeposited payments available.</p>
                            <p class="text-muted">All received payments have been deposited.</p>
                        </div>
                        @endif

                        <div class="total-section">
                            <span class="total-label">Total Deposit Amount:</span>
                            <span class="total-value" id="total-deposit">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Bank Account Selection -->
                <div class="bd-card">
                    <div class="bd-card-header">
                        <h3 class="bd-card-title"><i class="fas fa-university"></i> Deposit To</h3>
                    </div>
                    <div class="bd-card-body">
                        @forelse($bankAccounts as $bank)
                        <label class="bank-account-card" onclick="selectBank(this)">
                            <input type="radio" name="bank_account_id" value="{{ $bank->id }}" {{ $loop->first ? 'checked' : '' }}>
                            <div class="bank-account-name">{{ $bank->name }}</div>
                            <div class="bank-account-balance">${{ number_format($bank->current_balance ?? 0, 2) }}</div>
                        </label>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-bank"></i>
                            <p>No bank accounts found.</p>
                            <a href="{{ route('bookkeeping.accounts.create') }}">Create Bank Account</a>
                        </div>
                        @endforelse

                        <div class="deposit-total-summary" style="margin-top: 16px; padding: 10px 12px; border-radius: 8px; background: #fff8e7; border: 1px solid #ffb84d;">
                            <span style="font-weight: 600; color: #b45309;">Deposit total:</span>
                            <span id="deposit-total-display" style="font-weight: 700; color: #ff9900;">$0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div style="margin-top: 20px;">
                    <button type="submit" class="btn-submit" style="width: 100%;"><i class="fas fa-save"></i> Create Deposit</button>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
function selectBank(label) {
    $('.bank-account-card').removeClass('selected');
    $(label).addClass('selected');
}

function updateTotal() {
    var total = 0;
    $('.payment-item:checked').each(function() {
        total += parseFloat($(this).data('amount')) || 0;
    });
    var formatted = '$' + total.toFixed(2);
    $('#total-deposit').text(formatted);
    $('#deposit-total-display').text(formatted);
}

$(document).ready(function() {
    // Initialize calendar datepicker for deposit date
    flatpickr('#deposit_date', {
        dateFormat: 'Y-m-d',
        defaultDate: '{{ date('Y-m-d') }}'
    });

    $('.bank-account-card:first').addClass('selected');

    // By default, select all undeposited payments so total shows immediately
    if ($('.payment-item').length) {
        $('.payment-item').prop('checked', true);
        $('#select-all').prop('checked', true);
        updateTotal();//update the total deposit amount
    }
    
    $('#select-all').on('change', function() {
        $('.payment-item').prop('checked', $(this).is(':checked'));
        updateTotal();
    });
    
    $('.payment-item').on('change', function() {
        updateTotal();
    });
});
</script>
@endsection
