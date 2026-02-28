@extends('layouts.app')
@section('title', __('account.payment_account_report'))

@section('css')
<style>
/* Amazon Theme - Payment Account Report */
.amazon-payment-account-report {
    background: #EAEDED;
    min-height: calc(100vh - 60px);
    padding: 20px 24px;
}

/* Banner */
.amazon-par-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(15, 17, 17, 0.3);
}

.amazon-par-banner__stripe {
    height: 4px;
    background: linear-gradient(90deg, #ff9900, #ffad33, #ff9900);
}

.amazon-par-banner__content {
    padding: 18px 24px;
}

.amazon-par-banner__title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
}

.amazon-par-banner__title i {
    color: #ff9900;
    font-size: 24px;
    filter: drop-shadow(0 2px 4px rgba(255, 153, 0, 0.4));
}

.amazon-par-banner__subtitle {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
    margin: 4px 0 0 36px;
}

/* Filter Card */
.amazon-par-filter-card {
    background: #ffffff;
    border: 1px solid #d5d9d9;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08);
    padding: 18px 22px;
    margin-bottom: 20px;
}

.amazon-par-filter-card .filter-toggle {
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

.amazon-par-filter-card .filter-toggle:hover {
    color: #e47911;
}

.amazon-par-filter-card .filter-toggle i {
    font-size: 14px;
}

.amazon-par-filter-card label {
    color: #0f1111;
    font-weight: 600;
    font-size: 13px;
}

.amazon-par-filter-card .form-control {
    border: 1px solid #d5d9d9;
    border-radius: 6px;
    height: 38px;
    font-size: 13px;
}

.amazon-par-filter-card .form-control:focus {
    border-color: #ff9900;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.15);
}

/* Table Card */
.amazon-par-card {
    background: #ffffff;
    border: 1px solid #d5d9d9;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08);
    overflow: hidden;
}

.amazon-par-card .table-responsive {
    border-radius: 0;
}

/* DataTable Header */
.amazon-par-card #payment_account_report thead tr th {
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

.amazon-par-card #payment_account_report thead tr th:last-child {
    border-right: none !important;
}

.amazon-par-card #payment_account_report thead tr th::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: #ff9900;
}

/* DataTable Body */
.amazon-par-card #payment_account_report tbody tr td {
    font-size: 13px;
    color: #0f1111;
    padding: 10px 14px;
    border-bottom: 1px solid #e7e7e7 !important;
    border-left: none !important;
    border-right: none !important;
    vertical-align: middle;
}

.amazon-par-card #payment_account_report tbody tr:nth-child(even) td {
    background: #fafafa;
}

.amazon-par-card #payment_account_report tbody tr:hover td {
    background: #fef8f0 !important;
}

/* Link Account Button - Amazon Orange */
.amazon-par-card #payment_account_report tbody tr td:last-child button.tw-dw-btn-outline.tw-dw-btn-info,
.amazon-par-card #payment_account_report tbody tr td:last-child .tw-dw-btn-outline.tw-dw-btn-info,
.amazon-par-card #payment_account_report tbody tr td button[class*="tw-dw-btn-info"][class*="btn-modal"] {
    background: #ff9900 !important;
    color: #ffffff !important;
    border: 1px solid #e47911 !important;
    border-radius: 6px !important;
    padding: 6px 14px !important;
    font-weight: 600;
    font-size: 12px;
    transition: all 0.2s ease;
    display: inline-block;
    text-decoration: none;
}

.amazon-par-card #payment_account_report tbody tr td:last-child button.tw-dw-btn-outline.tw-dw-btn-info:hover,
.amazon-par-card #payment_account_report tbody tr td:last-child .tw-dw-btn-outline.tw-dw-btn-info:hover,
.amazon-par-card #payment_account_report tbody tr td button[class*="tw-dw-btn-info"][class*="btn-modal"]:hover {
    background: #e47911 !important;
    border-color: #c7511f !important;
    box-shadow: 0 2px 6px rgba(255, 153, 0, 0.3) !important;
    transform: translateY(-1px);
    color: #ffffff !important;
}

/* DataTables Controls */
.amazon-par-card .dataTables_wrapper .dataTables_length,
.amazon-par-card .dataTables_wrapper .dataTables_filter {
    padding: 14px 18px;
    background: #fafafa;
    border-bottom: 1px solid #e7e7e7;
}

.amazon-par-card .dataTables_wrapper .dataTables_length label,
.amazon-par-card .dataTables_wrapper .dataTables_filter label {
    font-size: 13px;
    color: #0f1111;
    font-weight: 600;
}

.amazon-par-card .dataTables_wrapper .dataTables_length select {
    border: 1px solid #d5d9d9;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 13px;
}

.amazon-par-card .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #d5d9d9;
    border-radius: 4px;
    padding: 6px 10px;
    font-size: 13px;
}

.amazon-par-card .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ff9900;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.15);
}

/* Export Buttons */
.amazon-par-card .dt-buttons {
    padding: 14px 18px;
    background: #fafafa;
    border-bottom: 1px solid #e7e7e7;
}

.amazon-par-card .dt-buttons .dt-button {
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

.amazon-par-card .dt-buttons .dt-button:hover {
    background: #ff9900 !important;
    border-color: #e47911 !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(255, 153, 0, 0.3);
}

/* Pagination */
.amazon-par-card .dataTables_wrapper .dataTables_paginate {
    padding: 14px 18px;
    background: #fafafa;
    border-top: 1px solid #e7e7e7;
}

.amazon-par-card .dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 4px !important;
    font-size: 12px !important;
    margin: 0 2px !important;
    padding: 6px 10px !important;
}

.amazon-par-card .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #ff9900 !important;
    color: #ffffff !important;
    border-color: #e47911 !important;
}

.amazon-par-card .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #232f3e !important;
    color: #ffffff !important;
    border-color: #232f3e !important;
}

.amazon-par-card .dataTables_wrapper .dataTables_info {
    font-size: 12px;
    color: #565959;
    padding: 14px 18px;
}
</style>
@endsection

@section('content')
<div class="amazon-payment-account-report no-print">

    <!-- Banner -->
    <div class="amazon-par-banner">
        <div class="amazon-par-banner__stripe"></div>
        <div class="amazon-par-banner__content">
            <h1 class="amazon-par-banner__title">
                <i class="fas fa-file-invoice-dollar"></i>
                {{ __('account.payment_account_report') }}
            </h1>
            <p class="amazon-par-banner__subtitle">View and manage payment account transactions</p>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="amazon-par-filter-card">
        <div class="filter-toggle" data-toggle="collapse" data-target="#par_filters">
            <i class="fas fa-filter"></i> @lang('report.filters')
        </div>
        <div id="par_filters" class="collapse in">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group" style="margin-bottom: 0;">
                        {!! Form::label('account_id', __('account.account') . ':') !!}
                        {!! Form::select('account_id', $accounts, null, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group" style="margin-bottom: 0;">
                        {!! Form::label('date_filter', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', null, [
                            'placeholder' => __('lang_v1.select_a_date_range'),
                            'class' => 'form-control',
                            'id' => 'date_filter',
                            'readonly',
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="amazon-par-card">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="payment_account_report">
                <thead>
                    <tr>
                        <th>@lang('messages.date')</th>
                        <th>@lang('account.payment_ref_no')</th>
                        <th>@lang('account.invoice_ref_no')</th>
                        <th>@lang('sale.amount')</th>
                        <th>@lang('lang_v1.payment_type')</th>
                        <th>@lang('account.account')</th>
                        <th>@lang('lang_v1.description')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>
<!-- /.content -->

@endsection

@section('javascript')

    <script type="text/javascript">
        $(document).ready(function() {

            if ($('#date_filter').length == 1) {

                $('#date_filter').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#date_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                        payment_account_report.ajax.reload();
                    }
                );

                $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    payment_account_report.ajax.reload();
                });
            }

            payment_account_report = $('#payment_account_report').DataTable({
                processing: true,
                language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                serverSide: true,
                fixedHeader:false,
                "ajax": {
                    "url": "{{ action([\App\Http\Controllers\AccountReportsController::class, 'paymentAccountReport']) }}",
                    "data": function(d) {
                        d.account_id = $('#account_id').val();
                        var start_date = '';
                        var endDate = '';
                        if ($('#date_filter').val()) {
                            var start_date = $('#date_filter').data('daterangepicker').startDate.format(
                                'YYYY-MM-DD');
                            var endDate = $('#date_filter').data('daterangepicker').endDate.format(
                                'YYYY-MM-DD');
                        }
                        d.start_date = start_date;
                        d.end_date = endDate;
                    }
                },
                columnDefs: [{
                    "targets": 7,
                    "orderable": false,
                    "searchable": false
                }],
                columns: [{
                        data: 'paid_on',
                        name: 'paid_on'
                    },
                    {
                        data: 'payment_ref_no',
                        name: 'payment_ref_no'
                    },
                    {
                        data: 'transaction_number',
                        name: 'transaction_number'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'type',
                        name: 'T.type'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'details',
                        name: 'details',
                        "searchable": false
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ],
                "fnDrawCallback": function(oSettings) {
                    __currency_convert_recursively($('#payment_account_report'));
                }
            });

            $('select#account_id, #date_filter').change(function() {
                payment_account_report.ajax.reload();
            });
        })

        $(document).on('submit', 'form#link_account_form', function(e) {
            e.preventDefault();
            var data = $(this).serialize();

            $.ajax({
                method: $(this).attr("method"),
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function(result) {
                    if (result.success === true) {
                        $('div.view_modal').modal('hide');
                        toastr.success(result.msg);
                        payment_account_report.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });
    </script>
@endsection
