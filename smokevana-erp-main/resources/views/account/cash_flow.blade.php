@extends('layouts.app')
@section('title', __('lang_v1.cash_flow'))

@section('css')
<style>
/* Amazon Theme - Cash Flow */
.amazon-cash-flow {
    background: #EAEDED;
    min-height: calc(100vh - 60px);
    padding: 20px 24px;
}

/* Banner */
.amazon-cf-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(15, 17, 17, 0.3);
}

.amazon-cf-banner__stripe {
    height: 4px;
    background: linear-gradient(90deg, #ff9900, #ffad33, #ff9900);
}

.amazon-cf-banner__content {
    padding: 18px 24px;
}

.amazon-cf-banner__title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
}

.amazon-cf-banner__title i {
    color: #ff9900;
    font-size: 24px;
    filter: drop-shadow(0 2px 4px rgba(255, 153, 0, 0.4));
}

.amazon-cf-banner__subtitle {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
    margin: 4px 0 0 36px;
}

/* Filter Card */
.amazon-cf-filter-card {
    background: #ffffff;
    border: 1px solid #d5d9d9;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08);
    padding: 18px 22px;
    margin-bottom: 20px;
}

.amazon-cf-filter-card .filter-toggle {
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

.amazon-cf-filter-card .filter-toggle:hover {
    color: #e47911;
}

.amazon-cf-filter-card .filter-toggle i {
    font-size: 14px;
}

.amazon-cf-filter-card label {
    color: #0f1111;
    font-weight: 600;
    font-size: 13px;
}

.amazon-cf-filter-card .form-control {
    border: 1px solid #d5d9d9;
    border-radius: 6px;
    height: 38px;
    font-size: 13px;
}

.amazon-cf-filter-card .form-control:focus {
    border-color: #ff9900;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.15);
}

.amazon-cf-filter-card .input-group-addon {
    background: #232f3e;
    color: #ff9900;
    border: 1px solid #37475a;
    border-radius: 6px 0 0 6px;
}

/* Table Card */
.amazon-cf-card {
    background: #ffffff;
    border: 1px solid #d5d9d9;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08);
    overflow: hidden;
}

.amazon-cf-card .table-responsive {
    border-radius: 0;
}

/* DataTable Header */
.amazon-cf-card #cash_flow_table thead tr th {
    background: #232f3e !important;
    color: #ffffff !important;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 12px 14px;
    border: none !important;
    border-right: 1px solid rgba(255, 255, 255, 0.15) !important;
    position: relative;
}

.amazon-cf-card #cash_flow_table thead tr th:last-child {
    border-right: none !important;
}

.amazon-cf-card #cash_flow_table thead tr th::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: #ff9900;
}

/* DataTable Body */
.amazon-cf-card #cash_flow_table tbody tr td {
    font-size: 13px;
    color: #0f1111;
    padding: 10px 14px;
    border-bottom: 1px solid #e7e7e7 !important;
    border-left: none !important;
    border-right: none !important;
    vertical-align: middle;
}

.amazon-cf-card #cash_flow_table tbody tr:nth-child(even) td {
    background: #fafafa;
}

.amazon-cf-card #cash_flow_table tbody tr:hover td {
    background: #fef8f0 !important;
}

/* DataTable Footer */
.amazon-cf-card #cash_flow_table tfoot tr.bg-gray {
    background: #f7f8f8 !important;
}

.amazon-cf-card #cash_flow_table tfoot tr.bg-gray td {
    border-top: 2px solid #ff9900 !important;
    font-weight: 700;
    font-size: 14px;
    color: #0f1111;
    padding: 12px 14px;
}

/* DataTables Controls */
.amazon-cf-card .dataTables_wrapper .dataTables_length,
.amazon-cf-card .dataTables_wrapper .dataTables_filter {
    padding: 14px 18px;
    background: #fafafa;
    border-bottom: 1px solid #e7e7e7;
}

.amazon-cf-card .dataTables_wrapper .dataTables_length label,
.amazon-cf-card .dataTables_wrapper .dataTables_filter label {
    font-size: 13px;
    color: #0f1111;
    font-weight: 600;
}

.amazon-cf-card .dataTables_wrapper .dataTables_length select {
    border: 1px solid #d5d9d9;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 13px;
}

.amazon-cf-card .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #d5d9d9;
    border-radius: 4px;
    padding: 6px 10px;
    font-size: 13px;
}

.amazon-cf-card .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ff9900;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.15);
}

/* Export Buttons */
.amazon-cf-card .dt-buttons {
    padding: 14px 18px;
    background: #fafafa;
    border-bottom: 1px solid #e7e7e7;
}

.amazon-cf-card .dt-buttons .dt-button {
    background: #232f3e !important;
    color: #ffffff !important;
    border: 1px solid #37475a !important;
    border-radius: 6px !important;
    padding: 6px 14px !important;
    font-weight: 600;
    font-size: 12px;
    margin-right: 6px;
    transition: all 0.2s ease;
}

.amazon-cf-card .dt-buttons .dt-button:hover {
    background: #ff9900 !important;
    border-color: #e47911 !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(255, 153, 0, 0.3);
}

/* Pagination */
.amazon-cf-card .dataTables_wrapper .dataTables_paginate {
    padding: 14px 18px;
    background: #fafafa;
    border-top: 1px solid #e7e7e7;
}

.amazon-cf-card .dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 4px !important;
    font-size: 12px !important;
    margin: 0 2px !important;
    padding: 6px 10px !important;
}

.amazon-cf-card .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #ff9900 !important;
    color: #ffffff !important;
    border-color: #e47911 !important;
}

.amazon-cf-card .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #232f3e !important;
    color: #ffffff !important;
    border-color: #232f3e !important;
}

.amazon-cf-card .dataTables_wrapper .dataTables_info {
    font-size: 12px;
    color: #565959;
    padding: 14px 18px;
}
</style>
@endsection

@section('content')
<div class="amazon-cash-flow no-print">

    <!-- Banner -->
    <div class="amazon-cf-banner">
        <div class="amazon-cf-banner__stripe"></div>
        <div class="amazon-cf-banner__content">
            <h1 class="amazon-cf-banner__title">
                <i class="fas fa-money-bill-wave"></i>
                @lang('lang_v1.cash_flow')
            </h1>
            <p class="amazon-cf-banner__subtitle">Track cash inflows and outflows across your accounts</p>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="amazon-cf-filter-card">
        <div class="filter-toggle" data-toggle="collapse" data-target="#cf_filters">
            <i class="fas fa-filter"></i> @lang('report.filters')
        </div>
        <div id="cf_filters" class="collapse in">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group" style="margin-bottom: 0;">
                        {!! Form::label('account_id', __('account.account') . ':') !!}
                        {!! Form::select('account_id', $accounts, '', ['class' => 'form-control', 'placeholder' => __('messages.all')]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" style="margin-bottom: 0;">
                        {!! Form::label('cash_flow_location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('cash_flow_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group" style="margin-bottom: 0;">
                        {!! Form::label('transaction_date_range', __('report.date_range') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            {!! Form::text('transaction_date_range', null, ['class' => 'form-control', 'readonly', 'placeholder' => __('report.date_range')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group" style="margin-bottom: 0;">
                        {!! Form::label('transaction_type', __('account.transaction_type') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-exchange-alt"></i></span>
                            {!! Form::select('transaction_type', ['' => __('messages.all'),'debit' => __('account.debit'), 'credit' => __('account.credit')], '', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="amazon-cf-card">
        @can('account.access')
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="cash_flow_table">
                <thead>
                    <tr>
                        <th>@lang( 'messages.date' )</th>
                        <th>@lang( 'account.account' )</th>
                        <th>@lang( 'lang_v1.description' )</th>
                        <th>@lang( 'lang_v1.payment_method' )</th>
                        <th>@lang( 'lang_v1.payment_details' )</th>
                        <th>@lang('account.debit')</th>
                        <th>@lang('account.credit')</th>
                        <th>@lang( 'lang_v1.account_balance' ) @show_tooltip(__('lang_v1.account_balance_tooltip'))</th>
                        <th>@lang( 'lang_v1.total_balance' ) @show_tooltip(__('lang_v1.total_balance_tooltip'))</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                        <td class="footer_total_debit"></td>
                        <td class="footer_total_credit"></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endcan
    </div>

    <div class="modal fade account_model" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</div>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function(){

        // dateRangeSettings.autoUpdateInput = false
        $('#transaction_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#transaction_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                cash_flow_table.ajax.reload();
            }
        );
        
        // Cash Flow Table
        cash_flow_table = $('#cash_flow_table').DataTable({
            processing: true,
            language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            fixedHeader:false,
            "ajax": {
                    "url": "{{action([\App\Http\Controllers\AccountController::class, 'cashFlow'])}}",
                    "data": function ( d ) {
                        var start = '';
                        var end = '';
                        if($('#transaction_date_range').val() != ''){
                            start = $('#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            end = $('#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        }
                        
                        d.account_id = $('#account_id').val();
                        d.type = $('#transaction_type').val();
                        d.start_date = start,
                        d.end_date = end
                        d.location_id = $('#cash_flow_location_id').val();

                    }
                },
            "ordering": false,
            columns: [
                {data: 'operation_date', name: 'operation_date'},
                {data: 'account_name', name: 'A.name'},
                {data: 'sub_type', name: 'sub_type', searchable: false},
                {data: 'method', name: 'TP.method'},
                {data: 'payment_details', name: 'TP.payment_ref_no'},
                {data: 'debit', name: 'amount', searchable: false},
                {data: 'credit', name: 'amount', searchable: false},
                {data: 'balance', name: 'balance', searchable: false},
                {data: 'total_balance', name: 'total_balance', searchable: false},
            ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#cash_flow_table'));
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var footer_total_debit = 0;
                var footer_total_credit = 0;

                for (var r in data){
                    footer_total_debit += $(data[r].debit).data('orig-value') ? parseFloat($(data[r].debit).data('orig-value')) : 0;
                    footer_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;
                }

                $('.footer_total_debit').html(__currency_trans_from_en(footer_total_debit));
                $('.footer_total_credit').html(__currency_trans_from_en(footer_total_credit));
            }
        });
        $('#transaction_type, #account_id, #cash_flow_location_id').change( function(){
            cash_flow_table.ajax.reload();
        });
        $('#transaction_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#transaction_date_range').val('').change();
            cash_flow_table.ajax.reload();
        });

    });
</script>
@endsection