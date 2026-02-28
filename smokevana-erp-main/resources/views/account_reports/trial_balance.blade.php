@extends('layouts.app')
@section('title', __( 'account.trial_balance' ))

@section('css')
<style>
/* Amazon Theme - Trial Balance */
.amazon-trial-balance {
    background: #EAEDED;
    min-height: calc(100vh - 60px);
    padding: 20px 24px;
}

/* Banner */
.amazon-tb-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(15, 17, 17, 0.3);
}

.amazon-tb-banner__stripe {
    height: 4px;
    background: linear-gradient(90deg, #ff9900, #ffad33, #ff9900);
}

.amazon-tb-banner__content {
    padding: 18px 24px;
}

.amazon-tb-banner__title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
}

.amazon-tb-banner__title i {
    color: #ff9900;
    font-size: 24px;
    filter: drop-shadow(0 2px 4px rgba(255, 153, 0, 0.4));
}

.amazon-tb-banner__subtitle {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
    margin: 4px 0 0 36px;
}

/* Filter Card */
.amazon-tb-filter-card {
    background: #ffffff;
    border: 1px solid #d5d9d9;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08);
    padding: 18px 22px;
    margin-bottom: 20px;
}

.amazon-tb-filter-card .filter-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #ff9900;
    cursor: pointer;
    margin-bottom: 14px;
    transition: color 0.2s;
}

.amazon-tb-filter-card .filter-toggle:hover {
    color: #e47911;
}

.amazon-tb-filter-card .filter-toggle i {
    font-size: 14px;
}

.amazon-tb-filter-card label {
    color: #0f1111;
    font-weight: 600;
    font-size: 13px;
}

.amazon-tb-filter-card .form-control {
    border: 1px solid #d5d9d9;
    border-radius: 6px;
    height: 38px;
    font-size: 13px;
}

.amazon-tb-filter-card .form-control:focus {
    border-color: #ff9900;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.15);
}

.amazon-tb-filter-card .input-group-addon {
    background: #232f3e;
    color: #ff9900;
    border: 1px solid #37475a;
    border-radius: 6px 0 0 6px;
}

/* Trial Balance Card */
.amazon-tb-card {
    background: #ffffff;
    border: 1px solid #d5d9d9;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08);
    overflow: hidden;
}

.amazon-tb-card__header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-bottom: 3px solid #ff9900;
    padding: 14px 22px;
}

.amazon-tb-card__header h3 {
    margin: 0;
    color: #ffffff;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.3px;
}

.amazon-tb-card__header h3 i {
    color: #ff9900;
    margin-right: 8px;
}

.amazon-tb-card__body {
    padding: 0;
}

/* Main Trial Balance Table */
.amazon-tb-card__body #trial_balance_table {
    margin: 0;
}

.amazon-tb-card__body #trial_balance_table thead tr.bg-gray th {
    background: #37475a !important;
    color: #ffffff !important;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 18px;
    border: none !important;
    border-right: 1px solid rgba(255, 255, 255, 0.15) !important;
}

.amazon-tb-card__body #trial_balance_table thead tr.bg-gray th:last-child {
    border-right: none !important;
}

.amazon-tb-card__body #trial_balance_table tbody tr th {
    color: #0f1111;
    font-weight: 700;
    font-size: 13px;
    padding: 10px 18px;
    border-top: none;
    border-bottom: 1px solid #f0f0f0;
}

.amazon-tb-card__body #trial_balance_table tbody tr td {
    color: #565959;
    font-size: 13px;
    font-weight: 500;
    padding: 10px 18px;
    border-top: none;
    border-bottom: 1px solid #f0f0f0;
}

.amazon-tb-card__body #trial_balance_table tfoot tr.bg-gray th,
.amazon-tb-card__body #trial_balance_table tfoot tr.bg-gray td {
    background: #f7f8f8 !important;
    border-top: 2px solid #ff9900 !important;
    padding: 12px 18px;
    font-size: 14px;
    font-weight: 700;
    color: #0f1111;
}

/* Card Footer */
.amazon-tb-card__footer {
    padding: 14px 22px;
    border-top: 1px solid #e7e7e7;
    background: #fafafa;
    display: flex;
    justify-content: flex-end;
}

.amazon-tb-card__footer .btn-print-amz {
    background: #ff9900 !important;
    color: #ffffff !important;
    border: 1px solid #e47911 !important;
    border-radius: 6px !important;
    padding: 8px 18px !important;
    font-weight: 600;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 6px rgba(255, 153, 0, 0.3);
    transition: all 0.2s ease;
    cursor: pointer;
}

.amazon-tb-card__footer .btn-print-amz:hover {
    background: #e47911 !important;
    box-shadow: 0 4px 10px rgba(255, 153, 0, 0.4);
    transform: translateY(-1px);
}
</style>
@endsection

@section('content')

<div class="amazon-trial-balance">

    <!-- Banner -->
    <div class="amazon-tb-banner">
        <div class="amazon-tb-banner__stripe"></div>
        <div class="amazon-tb-banner__content">
            <h1 class="amazon-tb-banner__title">
                <i class="fas fa-balance-scale"></i>
                @lang('account.trial_balance')
            </h1>
            <p class="amazon-tb-banner__subtitle">Compare total debits and credits for your accounts</p>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="amazon-tb-filter-card no-print">
        <div class="filter-toggle" data-toggle="collapse" data-target="#tb_filters">
            <i class="fas fa-filter"></i> @lang('report.filters')
        </div>
        <div id="tb_filters" class="collapse in">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group" style="margin-bottom: 0;">
                        {!! Form::label('trial_bal_location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('trial_bal_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-sm-3 col-xs-6">
                        <label for="end_date">@lang('messages.filter_by_date'):</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input type="text" id="end_date" value="{{@format_date('now')}}" class="form-control" readonly>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trial Balance Card -->
    <div class="amazon-tb-card">
        <div class="amazon-tb-card__header print_section">
            <h3><i class="fas fa-file-invoice-dollar"></i> {{session()->get('business.name')}} - @lang( 'account.trial_balance') - <span id="hidden_date">{{@format_date('now')}}</span></h3>
        </div>
        <div class="amazon-tb-card__body">
            <table class="table table-border-center-col no-border table-pl-12" id="trial_balance_table">
                <thead>
                    <tr class="bg-gray">
                        <th>@lang('account.trial_balance')</th>
                        <th>@lang('account.debit')</th>
                        <th>@lang('account.credit')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>@lang('account.supplier_due'):</th>
                        <td>&nbsp;</td>
                        <td>
                            <input type="hidden" id="hidden_supplier_due" class="debit">
                            <span class="remote-data" id="supplier_due">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('account.customer_due'):</th>
                        <td>
                            <input type="hidden" id="hidden_customer_due" class="credit">
                            <span class="remote-data" id="customer_due">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <th>@lang('account.account_balances'):</th>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
                <tbody id="account_balances_details">
                </tbody>
                {{--
                <tbody>
                    <tr>
                        <th>@lang('account.capital_accounts'):</th>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
                <tbody id="capital_account_balances_details"></tbody>
                --}}
                <tfoot>
                    <tr class="bg-gray">
                        <th>@lang('sale.total')</th>
                        <td>
                            <span class="remote-data" id="total_credit">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                        <td>
                            <span class="remote-data" id="total_debit">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="amazon-tb-card__footer no-print">
            <button type="button" class="btn-print-amz" onclick="window.print()">
                <i class="fa fa-print"></i> @lang('messages.print')
            </button>
        </div>
    </div>

</div>
<!-- /.content -->
@stop
@section('javascript')

<script type="text/javascript">
    $(document).ready( function(){
        //Date picker
        $('#end_date').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });
        update_trial_balance();

        $('#end_date').change( function() {
            update_trial_balance();
            $('#hidden_date').text($(this).val());
        });
        $('#trial_bal_location_id').change( function() {
            update_trial_balance();
        });
    });

    function update_trial_balance(){
        var loader = '<i class="fas fa-sync fa-spin fa-fw"></i>';
        $('span.remote-data').each( function() {
            $(this).html(loader);
        });

        $('table#trial_balance_table tbody#capital_account_balances_details').html('<tr><td colspan="3"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');
        $('table#trial_balance_table tbody#account_balances_details').html('<tr><td colspan="3"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');

        var end_date = $('input#end_date').val();
        var location_id = $('#trial_bal_location_id').val()
        $.ajax({
            url: "{{action([\App\Http\Controllers\AccountReportsController::class, 'trialBalance'])}}?end_date=" + end_date + '&location_id=' + location_id,
            dataType: "json",
            success: function(result){
                $('span#supplier_due').text(__currency_trans_from_en(result.supplier_due, true));
                __write_number($('input#hidden_supplier_due'), result.supplier_due);

                $('span#customer_due').text(__currency_trans_from_en(result.customer_due, true));
                __write_number($('input#hidden_customer_due'), result.customer_due);

                var account_balances = result.account_balances;
                $('table#trial_balance_table tbody#account_balances_details').html('');
                for (var key in account_balances) {
                    var accnt_bal = __currency_trans_from_en(result.account_balances[key]);
                    var accnt_bal_with_sym = __currency_trans_from_en(result.account_balances[key], true);
                    var account_tr = '<tr><td class="pl-20-td">' + key + ':</td><td><input type="hidden" class="credit" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td><td>&nbsp;</td></tr>';
                    $('table#trial_balance_table tbody#account_balances_details').append(account_tr);
                }

                var capital_account_details = result.capital_account_details;
                $('table#trial_balance_table tbody#capital_account_balances_details').html('');
                for (var key in capital_account_details) {
                    var accnt_bal = __currency_trans_from_en(result.capital_account_details[key]);
                    var accnt_bal_with_sym = __currency_trans_from_en(result.capital_account_details[key], true);
                    var account_tr = '<tr><td class="pl-20-td">' + key + ':</td><td><input type="hidden" class="credit" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td><td>&nbsp;</td></tr>';
                    $('table#trial_balance_table tbody#capital_account_balances_details').append(account_tr);
                }

                var total_debit = 0;
                var total_credit = 0;
                $('input.debit').each( function(){
                    total_debit += __read_number($(this));
                });
                $('input.credit').each( function(){
                    total_credit += __read_number($(this));
                });

                $('span#total_debit').text(__currency_trans_from_en(total_debit, true));
                $('span#total_credit').text(__currency_trans_from_en(total_credit, true));
            }
        });
    }
</script>

@endsection