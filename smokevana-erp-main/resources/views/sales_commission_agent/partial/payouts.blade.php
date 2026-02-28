<div class="row">
    <div class="col-md-12">
        <!-- Payout History & Management -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-history"></i> Payout History & Management
                        </h3>

                        @if($pending_payout > 0)
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-success btn-sm" id="process_payout_btn">
                                <i class="fa fa-money"></i> Process Payout
                            </button>
                        </div>
                        @endif
                    </div>
                    <div class="box-body">
                        <!-- Payout Summary -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon"><i class="fa fa-check"></i></span>
                                    <div class="info-box-content">
                                        <p>Total Paid</p>
                                        <span class="info-box-number">{{ number_format($total_paid ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-yellow">
                                    <span class="info-box-icon"><i class="fa fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <p>Final Commission Dues</p>
                                        <span class="info-box-number">{{ number_format($pending_payout ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-blue">
                                    <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                                    <div class="info-box-content">
                                        <p>This Quarter</p>
                                        <span class="info-box-number">{{ number_format($current_quarter_commission ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon"><i class="fa fa-calendar-alt"></i></span>
                                    <div class="info-box-content">
                                        <p>This Year</p>
                                        <span class="info-box-number">{{ number_format($current_year_commission ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payout Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="payouts_table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Transaction No</th>
                                        <th> Commission Amount</th>
                                        <th>Transaction payment status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_date }}</td>
                                            <td>{{ $transaction->invoice_no }}</td>
                                            <td>$ {{ number_format($transaction->final_total * $user->percentage_value / 100, 2) }}</td>
                                            <td>{!! $transaction->payment_status == 'paid' ? "<span class=\"label label-success\">{$transaction->payment_status}</span>" : "<span class=\"label label-warning\">{$transaction->payment_status}</span>" !!}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Calculation Details -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-calculator"></i> Commission Calculation Details
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Current Period Calculation</h4>
                                <table class="table table-condensed">
                                    <tr>
                                        <td><strong>Total Sales:</strong></td>
                                        <td>${{ number_format($total_sells, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Commission Rate:</strong></td>
                                        <td>{{ $user->percentage_value }}%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Base Commission:</strong></td>
                                        <td>${{ number_format($final_commission, 2) }}</td>
                                    </tr>
                                    <tr class="warning">
                                        <td><strong>Payable Commission:</strong></td>
                                        <td><strong>${{ number_format($final_commission_payable, 2) }}</strong></td>
                                    </tr>
                                    <tr class="warning">
                                        <td><strong>Total Commission Paid:</strong></td>
                                        <td><strong>${{ number_format($total_paid, 2) }}</strong></td>
                                    </tr>
                                    <tr class="success">
                                        <td><strong>Final Commission Dues:</strong></td>
                                        <td><strong>${{ number_format($pending_payout, 2) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Bonus Eligibility</h4>
                                <table class="table table-condensed">
                                    <tr>
                                        <td><strong>Quarterly Target:</strong></td>
                                        <td>${{ number_format($user->quarterly_sales_target ?? 0, 2) }}</td>
                                        <td>
                                            @if(($current_quarter_commission ?? 0) >= ($user->quarterly_sales_target ?? 0))
                                                <span class="label label-success">Achieved</span>
                                            @else
                                                <span class="label label-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Quarterly Bonus:</strong></td>
                                        <td>${{ number_format($user->quarterly_bonus_amount ?? 0, 2) }}</td>
                                        <td>
                                            @if(($current_quarter_commission ?? 0) >= ($user->quarterly_sales_target ?? 0))
                                                <span class="label label-success">Eligible</span>
                                            @else
                                                <span class="label label-default">Not Eligible</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Yearly Target:</strong></td>
                                        <td>${{ number_format($user->yearly_sales_target ?? 0, 2) }}</td>
                                        <td>
                                            @if(($current_year_commission ?? 0) >= ($user->yearly_sales_target ?? 0))
                                                <span class="label label-success">Achieved</span>
                                            @else
                                                <span class="label label-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Yearly Bonus:</strong></td>
                                        <td>${{ number_format($user->yearly_bonus_amount ?? 0, 2) }}</td>
                                        <td>
                                            @if(($current_year_commission ?? 0) >= ($user->yearly_sales_target ?? 0))
                                                <span class="label label-success">Eligible</span>
                                            @else
                                                <span class="label label-default">Not Eligible</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Process Payout Modal -->
<div class="modal fade" id="process_payout_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Process Payout</h4>
            </div>
            <div class="modal-body">
                <form id="process_payout_form">
                    @csrf
                    <div class="form-group">
                        <label for="payout_amount">Payout Amount</label>
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="number" class="form-control" id="payout_amount" name="payout_amount" 
                                   value="{{ $pending_payout }}" 
                                   max="{{ $pending_payout }}"
                                   min="0.01"
                                   step="0.01">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                            <option value="cash">Cash</option>
                            {{-- <option value="paypal">PayPal</option> --}}
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_reference">Payment Reference</label>
                        <input type="text" class="form-control" id="payment_reference" name="payment_reference" 
                               placeholder="Transaction ID, Check Number, etc.">
                    </div>
                    
                    <div class="form-group">
                        <label for="payout_notes">Notes</label>
                        <textarea class="form-control" id="payout_notes" name="payout_notes" rows="3" 
                                  placeholder="Additional notes about this payout"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirm_payout_btn">Process Payout</button>
            </div>
        </div>
    </div>
</div>


<script>
    (function(){
        // Validate payout amount and toggle confirm button
        $(function(){
            var $amount = $('#payout_amount');
            var $confirm = $('#confirm_payout_btn');
            var maxVal = parseFloat($amount.attr('max')) || 0;

            function showError(msg){
                var $err = $('#payout_amount_error');
                if ($err.length === 0) {
                    $err = $('<p id="payout_amount_error" class="text-danger" style="margin-top:8px;"></p>');
                    $amount.closest('.form-group').append($err);
                }
                $err.text(msg).show();
            }

            function hideError(){
                $('#payout_amount_error').hide();
            }

            function validate(){
                var val = parseFloat($amount.val());
                if (isNaN(val) || val <= 0) {
                    $confirm.prop('disabled', true);
                    showError('Enter a valid amount greater than 0.');
                    return false;
                }
                if (maxVal && val > maxVal + 0.000001) {
                    // Clamp to maxVal and inform the user
                    var clamped = maxVal.toFixed(2);
                    $amount.val(clamped);
                    $confirm.prop('disabled', false);
                    showError('Only $' + clamped + ' is available to payout. Amount adjusted to the maximum available.');
                    return true;
                }

                $confirm.prop('disabled', false);
                hideError();
                return true;
            }

            // Initialize state
            $confirm.prop('disabled', true);

            // Ensure modal opens and initial validation runs
            $('#process_payout_btn').on('click', function(){
                $('#process_payout_modal').modal('show');
                // run validation after a tick in case value is set by server
                setTimeout(validate, 10);
            });

            $amount.on('input change', function(){
                var v = parseFloat($(this).val());
                if (!isNaN(v) && maxVal && v > maxVal + 0.000001) {
                    // Clamp immediately
                    var clamped = maxVal.toFixed(2);
                    $(this).val(clamped);
                    showError('Only $' + clamped + ' is available to payout. Amount adjusted.');
                } else {
                    hideError();
                }
                validate();
            });

            $('#process_payout_modal').on('shown.bs.modal', function(){
                // Re-evaluate when modal shown
                validate();
            });

            $confirm.on('click', function(e){
                // Prevent action if invalid
                if (!validate()) {
                    e.preventDefault();
                    return false;
                }
                // If valid, normal flow continues (server-side handling or existing handlers)
            });

        });
    })();
</script>

