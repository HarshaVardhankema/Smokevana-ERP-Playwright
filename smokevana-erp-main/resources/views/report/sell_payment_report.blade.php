@extends('layouts.app')
@section('title', __('lang_v1.sell_payment_report'))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="report-amazon-page">
<!-- Amazon banner -->
<div class="sr-banner amazon-theme-banner">
    <div class="banner-inner">
        <div class="banner-icon"><i class="fas fa-credit-card" aria-hidden="true"></i></div>
        <div class="banner-text">
            <h1 class="banner-title">{{ __('lang_v1.sell_payment_report') }}</h1>
            <p class="banner-subtitle">@lang('lang_v1.sell_payment_trend')</p>
        </div>
    </div>
    <div class="banner-actions">
        <button type="button" class="btn btn-sr-filters" data-toggle="modal" data-target="#filterModal">
            <i class="fa fa-filter"></i> @lang('report.filters')
        </button>
    </div>
</div>

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-12">

            <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document"> {{-- Use modal-lg or modal-xl as needed --}}
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">@lang('report.filters')</h4>
                        </div>
                        <div class="modal-body" style="padding: 0px; margin-top: 10px;">

                            {{-- @component('components.filters', ['title' => __('report.filters')]) --}}
                            {!! Form::open(['url' => '#', 'method' => 'get', 'id' => 'sell_payment_report_form' ]) !!}
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('customer_id', __('contact.customer') . ':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                        </span>
                                        {!! Form::select('customer_id', $customers, null, ['class' => 'form-control
                                        select2', 'style' => 'width:100%', 'placeholder' => __('messages.all'),
                                        'required']); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('location_id', __('purchase.business_location').':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-map-marker"></i>
                                        </span>
                                        {!! Form::select('location_id', $business_locations, null, ['class' =>
                                        'form-control select2', 'style' => 'width:100%', 'placeholder' =>
                                        __('messages.all'), 'required']); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('payment_types', __('lang_v1.payment_method').':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fas fa-money-bill-alt"></i>
                                        </span>
                                        {!! Form::select('payment_types', $payment_types, null, ['class' =>
                                        'form-control select2', 'placeholder' => __('messages.all'), 'required']); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('customer_group_filter', __('lang_v1.customer_group').':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-users"></i>
                                        </span>
                                        {!! Form::select('customer_group_filter', $customer_groups, null, ['class' =>
                                        'form-control select2']); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">

                                    {!! Form::label('spr_date_filter', __('report.date_range') . ':') !!}
                                    {!! Form::text('date_range', null, ['placeholder' =>
                                    __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' =>
                                    'spr_date_filter', 'readonly']); !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                            <div class="modal-footer">
                                {{-- <button type="button" class="btn btn-primary"
                                    id="applyFiltersBtn">@lang('messages.apply')</button>
                                --}}
                                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white"
                                    data-dismiss="modal">@lang('messages.close')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- @endcomponent --}}

    <!-- Sell Payment Report Graph Section -->
    <div class="row no-print" id="sell_payment_report_chart_container" style="margin-bottom: 20px;">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('Sell Payment Trend')])
                <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                    <canvas id="sell_payment_report_chart"></canvas>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.sell_payment_report') . ' - ' . __('report.summary')])
            <div>
                <table class="table   table-bordered table-striped ajax_view " id="sell_payment_report_table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px">&nbsp;</th>
                            <th style="min-width: 100px">@lang('purchase.ref_no')</th>
                            <th style="min-width: 100px">@lang('lang_v1.paid_on')</th>
                            <th style="min-width: 100px">@lang('sale.amount')</th>
                            <th style="min-width: 100px">@lang('contact.customer')</th>
                            <th style="min-width: 100px">@lang('lang_v1.customer_group')</th>
                            <th style="min-width: 100px">@lang('lang_v1.payment_method')</th>
                            <th style="min-width: 100px">@lang('sale.sale')</th>
                            <th style="min-width: 100px">@lang('messages.action')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">
                            <td colspan="2"></td>
                            <td class="text-left"><strong>@lang('sale.total'):</strong></td>
                            <td class="footer_total_amount text-left"><span class="display_currency"
                                    data-currency_symbol="true"></span></td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
</div>
<div class="modal fade view_register" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@endsection

@section('javascript')
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>

<script type="text/javascript">
    var sellPaymentReportChart = null;
    
    function loadSellPaymentReportChart() {
        var start_date = $('#spr_date_filter').data('daterangepicker') 
            ? $('#spr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD')
            : null;
        var end_date = $('#spr_date_filter').data('daterangepicker')
            ? $('#spr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD')
            : null;
        var location_id = $('#location_id').val();
        var customer_id = $('#customer_id').val();
        var customer_group_id = $('#customer_group_filter').val();
        var payment_method = $('#payment_types').val();
        
        // Build data object, only include non-null values
        var requestData = {};
        if (start_date) requestData.start_date = start_date;
        if (end_date) requestData.end_date = end_date;
        if (location_id) requestData.location_id = location_id;
        if (customer_id) requestData.customer_id = customer_id;
        if (customer_group_id) requestData.customer_group_id = customer_group_id;
        if (payment_method) requestData.payment_types = payment_method;
        
        $.ajax({
            url: '/reports/sell-payment-report-chart-data',
            method: 'GET',
            data: requestData,
            success: function(response) {
                if (response.success && response.labels && response.total) {
                    var ctx = document.getElementById('sell_payment_report_chart');
                    if (!ctx) {
                        console.error('Chart canvas element not found');
                        return;
                    }
                    ctx = ctx.getContext('2d');
                    
                    if (sellPaymentReportChart) {
                        sellPaymentReportChart.destroy();
                    }
                    
                    sellPaymentReportChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: response.labels,
                            datasets: [
                                {
                                    label: '@lang("cash_register.total_cash")',
                                    data: response.cash,
                                    borderColor: 'rgb(75, 192, 192)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: '@lang("cash_register.total_card_slips")',
                                    data: response.card,
                                    borderColor: 'rgb(255, 99, 132)',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: '@lang("cash_register.total_cheques")',
                                    data: response.cheque,
                                    borderColor: 'rgb(255, 206, 86)',
                                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: '@lang("lang_v1.total_bank_transfer")',
                                    data: response.bank_transfer,
                                    borderColor: 'rgb(153, 102, 255)',
                                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: '@lang("sale.total")',
                                    data: response.total,
                                    borderColor: 'rgb(54, 162, 235)',
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: '@lang("Sell Payment Trend")',
                                    font: { size: 16, weight: 'bold' }
                                },
                                legend: { display: true, position: 'top' },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    callbacks: {
                                        label: function(context) {
                                            var label = context.dataset.label || '';
                                            if (label) label += ': ';
                                            label += __currency_trans_from_en(context.parsed.y);
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    ticks: {
                                        callback: function(value) {
                                            return __currency_trans_from_en(value);
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            interaction: {
                                mode: 'nearest',
                                axis: 'x',
                                intersect: false
                            }
                        }
                    });
                } else {
                    console.warn('No chart data available', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading sell payment report chart data:', {xhr, status, error, response: xhr.responseJSON});
            }
        });
    }
    
    $(document).ready(function() {
        // Load chart on page load
        setTimeout(function() {
            loadSellPaymentReportChart();
        }, 1000);
        
        // Reload chart when filters change
        $('#location_id, #customer_id, #customer_group_filter, #payment_types').on('change', function() {
            loadSellPaymentReportChart();
        });
        
        $('#spr_date_filter').on('apply.daterangepicker', function() {
            loadSellPaymentReportChart();
        });
    });
</script>
@endsection