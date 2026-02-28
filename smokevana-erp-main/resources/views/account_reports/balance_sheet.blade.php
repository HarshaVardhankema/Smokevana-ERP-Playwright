@extends('layouts.app')
@section('title', __( 'account.balance_sheet' ))

@section('css')
<style>
/* Amazon Theme - Balance Sheet */
.amazon-balance-sheet {
    background: #EAEDED;
    min-height: calc(100vh - 60px);
    padding: 20px 24px;
}

/* Banner */
.amazon-bs-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(15, 17, 17, 0.3);
}

.amazon-bs-banner__stripe {
    height: 4px;
    background: linear-gradient(90deg, #ff9900, #ffad33, #ff9900);
}

.amazon-bs-banner__content {
    padding: 18px 24px;
}

.amazon-bs-banner__title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
}

.amazon-bs-banner__title i {
    color: #ff9900;
    font-size: 24px;
    filter: drop-shadow(0 2px 4px rgba(255, 153, 0, 0.4));
}

.amazon-bs-banner__subtitle {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
    margin: 4px 0 0 36px;
}

/* Filter Card */
.amazon-bs-filter-card {
    background: #ffffff;
    border: 1px solid #d5d9d9;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08);
    padding: 18px 22px;
    margin-bottom: 20px;
}

.amazon-bs-filter-card .filter-toggle {
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

.amazon-bs-filter-card .filter-toggle:hover {
    color: #e47911;
}

.amazon-bs-filter-card .filter-toggle i {
    font-size: 14px;
}

.amazon-bs-filter-card label {
    color: #0f1111;
    font-weight: 600;
    font-size: 13px;
}

.amazon-bs-filter-card .form-control {
    border: 1px solid #d5d9d9;
    border-radius: 6px;
    height: 38px;
    font-size: 13px;
}

.amazon-bs-filter-card .form-control:focus {
    border-color: #ff9900;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.15);
}

.amazon-bs-filter-card .input-group-addon {
    background: #232f3e;
    color: #ff9900;
    border: 1px solid #37475a;
    border-radius: 6px 0 0 6px;
}

/* Balance Sheet Card */
.amazon-bs-card {
    background: #ffffff;
    border: 1px solid #d5d9d9;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08);
    overflow: hidden;
}

.amazon-bs-card__header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-bottom: 3px solid #ff9900;
    padding: 14px 22px;
}

.amazon-bs-card__header h3 {
    margin: 0;
    color: #ffffff;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.3px;
}

.amazon-bs-card__header h3 i {
    color: #ff9900;
    margin-right: 8px;
}

.amazon-bs-card__body {
    padding: 0;
}

/* Main Table */
.amazon-bs-card__body .table-border-center {
    margin: 0;
}

.amazon-bs-card__body .table-border-center > thead > tr.bg-gray > th,
.amazon-bs-card__body .table-border-center > thead > tr > th {
    background: #37475a !important;
    color: #ffffff !important;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 18px;
    border: none !important;
    border-right: 1px solid rgba(255, 255, 255, 0.15) !important;
}

.amazon-bs-card__body .table-border-center > thead > tr > th:last-child {
    border-right: none !important;
}

.amazon-bs-card__body .table-border-center > tbody > tr > td {
    padding: 0;
    vertical-align: top;
    border: none !important;
    border-right: 1px solid #e7e7e7 !important;
}

.amazon-bs-card__body .table-border-center > tbody > tr > td:last-child {
    border-right: none !important;
}

/* Inner Tables */
.amazon-bs-card__body .table-border-center .table {
    margin: 0;
}

.amazon-bs-card__body .table-border-center .table th {
    color: #0f1111;
    font-weight: 700;
    font-size: 13px;
    padding: 10px 18px;
    border-bottom: 1px solid #f0f0f0;
    border-top: none;
}

.amazon-bs-card__body .table-border-center .table td {
    color: #565959;
    font-size: 13px;
    font-weight: 500;
    padding: 10px 18px;
    border-bottom: 1px solid #f0f0f0;
    border-top: none;
}

/* Footer Totals */
.amazon-bs-card__body .table-border-center > tfoot > tr.bg-gray > td,
.amazon-bs-card__body .table-border-center > tfoot > tr > td {
    background: #f7f8f8 !important;
    border-top: 2px solid #ff9900 !important;
    padding: 0;
}

.amazon-bs-card__body .table-border-center > tfoot .table.bg-gray {
    background: transparent !important;
}

.amazon-bs-card__body .table-border-center > tfoot .table th {
    color: #0f1111;
    font-weight: 800;
    font-size: 14px;
    padding: 14px 18px;
    border: none;
}

.amazon-bs-card__body .table-border-center > tfoot .table td {
    color: #0f1111;
    font-weight: 700;
    font-size: 14px;
    padding: 14px 18px;
    border: none;
}

/* Card Footer */
.amazon-bs-card__footer {
    padding: 14px 22px;
    border-top: 1px solid #e7e7e7;
    background: #fafafa;
    display: flex;
    justify-content: flex-end;
}

.amazon-bs-card__footer .btn-print-amz {
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

.amazon-bs-card__footer .btn-print-amz:hover {
    background: #e47911 !important;
    box-shadow: 0 4px 10px rgba(255, 153, 0, 0.4);
    transform: translateY(-1px);
}
</style>
@endsection

@section('content')
<div class="amazon-balance-sheet">

    <!-- Banner -->
    <div class="amazon-bs-banner">
        <div class="amazon-bs-banner__stripe"></div>
        <div class="amazon-bs-banner__content">
            <h1 class="amazon-bs-banner__title">
                <i class="fas fa-balance-scale"></i>
                @lang('account.balance_sheet')
            </h1>
            <p class="amazon-bs-banner__subtitle">View liabilities and assets overview for your business</p>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="amazon-bs-filter-card no-print">
        <div class="filter-toggle" data-toggle="collapse" data-target="#bs_filters">
            <i class="fas fa-filter"></i> @lang('report.filters')
        </div>
        <div id="bs_filters" class="collapse in">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group" style="margin-bottom: 0;">
                        {!! Form::label('bal_sheet_location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('bal_sheet_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
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

    <!-- Balance Sheet Card -->
    <div class="amazon-bs-card">
        <div class="amazon-bs-card__header print_section">
            <h3><i class="fas fa-file-invoice-dollar"></i> {{session()->get('business.name')}} - @lang('account.balance_sheet') - <span id="hidden_date">{{@format_date('now')}}</span></h3>
        </div>
        <div class="amazon-bs-card__body">
            <table class="table table-border-center no-border table-pl-12">
                <thead>
                    <tr class="bg-gray">
                        <th>@lang( 'account.liability')</th>
                        <th>@lang( 'account.assets')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <table class="table">
                                <tr>
                                    <th>@lang('account.supplier_due'):</th>
                                <td>
                                    <input type="hidden" id="hidden_supplier_due" class="liability">
                                    <span class="remote-data" id="supplier_due">
                                        <i class="fas fa-sync fa-spin fa-fw"></i>
                                    </span>
                                </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="table" id="assets_table">
                                <tbody>
                                    <tr>
                                        <th>@lang('account.customer_due'):</th>
                                        <td>
                                            <input type="hidden" id="hidden_customer_due" class="asset">
                                            <span class="remote-data" id="customer_due">
                                                <i class="fas fa-sync fa-spin fa-fw"></i>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>@lang('report.closing_stock'):</th>
                                        <td>
                                            <input type="hidden" id="hidden_closing_stock" class="asset">
                                            <span class="remote-data" id="closing_stock">
                                                <i class="fas fa-sync fa-spin fa-fw"></i>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">@lang('account.account_balances'):</th>
                                    </tr>
                                </tbody>
                                <tbody id="account_balances" class="pl-20-td">
                                    <tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>
                                </tbody>
                                {{--
                                <tbody>
                                    <tr>
                                        <th colspan="2">@lang('account.capital_accounts'):</th>
                                    </tr>
                                </tbody>
                                <tbody id="capital_account_balances" class="pl-20-td">
                                    <tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>
                                </tbody>
                                --}}
                            </table>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="bg-gray">
                        <td>
                            <table class="table bg-gray mb-0 no-border">
                                <tr>
                                    <th>
                                        @lang('account.total_liability'): 
                                    </th>
                                    <td>
                                        <span id="total_liabilty"><i class="fas fa-sync fa-spin fa-fw"></i></span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="table bg-gray mb-0 no-border">
                                <tr>
                                    <th>
                                        @lang('account.total_assets'): 
                                    </th>
                                    <td>
                                        <span id="total_assets"><i class="fas fa-sync fa-spin fa-fw"></i></span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="amazon-bs-card__footer no-print">
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
        update_balance_sheet();

        $('#end_date').change( function() {
            update_balance_sheet();
            $('#hidden_date').text($(this).val());
        });
        $('#bal_sheet_location_id').change( function() {
            update_balance_sheet();
        });
    });

    function update_balance_sheet(){
        var loader = '<i class="fas fa-sync fa-spin fa-fw"></i>';
        $('span.remote-data').each( function() {
            $(this).html(loader);
        });

        $('table#assets_table tbody#account_balances').html('<tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');
        $('table#assets_table tbody#capital_account_balances').html('<tr><td colspan="2"><i class="fas fa-sync fa-spin fa-fw"></i></td></tr>');

        var end_date = $('input#end_date').val();
        var location_id = $('#bal_sheet_location_id').val()
        $.ajax({
            url: "{{action([\App\Http\Controllers\AccountReportsController::class, 'balanceSheet'])}}?end_date=" + end_date + '&location_id=' + location_id, 
            dataType: "json",
            success: function(result){
                $('span#supplier_due').text(__currency_trans_from_en(result.supplier_due, true));
                __write_number($('input#hidden_supplier_due'), result.supplier_due);

                $('span#customer_due').text(__currency_trans_from_en(result.customer_due, true));
                __write_number($('input#hidden_customer_due'), result.customer_due);

                $('span#closing_stock').text(__currency_trans_from_en(result.closing_stock, true));
                __write_number($('input#hidden_closing_stock'), result.closing_stock);
                var account_balances = result.account_balances;
                $('table#assets_table tbody#account_balances').html('');
                for (var key in account_balances) {
                    var accnt_bal = __currency_trans_from_en(result.account_balances[key]);
                    var accnt_bal_with_sym = __currency_trans_from_en(result.account_balances[key], true);
                    var account_tr = '<tr><td class="pl-20-td">' + key + ':</td><td><input type="hidden" class="asset" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td></tr>';
                    $('table#assets_table tbody#account_balances').append(account_tr);
                }
                var capital_account_details = result.capital_account_details;
                $('table#assets_table tbody#capital_account_balances').html('');
                for (var key in capital_account_details) {
                    var accnt_bal = __currency_trans_from_en(result.capital_account_details[key]);
                    var accnt_bal_with_sym = __currency_trans_from_en(result.capital_account_details[key], true);
                    var account_tr = '<tr><td class="pl-20-td">' + key + ':</td><td><input type="hidden" class="asset" value="' + accnt_bal + '">' + accnt_bal_with_sym + '</td></tr>';
                    $('table#assets_table tbody#capital_account_balances').append(account_tr);
                }


                var total_liabilty = 0;
                var total_assets = 0;
                $('input.liability').each( function(){
                    total_liabilty += __read_number($(this));
                });
                $('input.asset').each( function(){
                    total_assets += __read_number($(this));
                });

                $('span#total_liabilty').text(__currency_trans_from_en(total_liabilty, true));
                $('span#total_assets').text(__currency_trans_from_en(total_assets, true));
                
            }
        });
    }
</script>

@endsection