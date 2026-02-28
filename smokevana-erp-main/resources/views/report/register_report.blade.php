@extends('layouts.app')
@section('title', __('report.register_report'))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="report-amazon-page">
<!-- Amazon banner -->
<div class="sr-banner amazon-theme-banner">
    <div class="banner-inner">
        <div class="banner-icon"><i class="fas fa-cash-register" aria-hidden="true"></i></div>
        <div class="banner-text">
            <h1 class="banner-title">{{ __('report.register_report') }}</h1>
            <p class="banner-subtitle">@lang('report.payment_methods_trend')</p>
        </div>
    </div>
    <div class="banner-actions">
        <button type="button" class="btn btn-sr-filters" data-toggle="modal" data-target="#filterModal">
            <i class="fa fa-filter"></i> @lang('report.filters')
        </button>
    </div>
</div>

<!-- Main content -->
<section class="content">
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
                            {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class,
                            'getStockReport']), 'method' => 'get', 'id' => 'register_report_filter_form' ]) !!}
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('register_user_id', __('report.user') . ':') !!}
                                    {!! Form::select('register_user_id', $users, null, ['class' => 'form-control
                                    select2', 'style' => 'width:100%', 'placeholder' => __('report.all_users')]); !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('register_status', __('sale.status') . ':') !!}
                                    {!! Form::select('register_status', ['open' => __('cash_register.open'), 'close' =>
                                    __('cash_register.close')], null, ['class' => 'form-control select2', 'style' =>
                                    'width:100%', 'placeholder' => __('report.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('register_report_date_range', __('report.date_range') . ':') !!}
                                    {!! Form::text('register_report_date_range', null , ['placeholder' =>
                                    __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' =>
                                    'register_report_date_range', 'readonly']); !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                            {{-- @endcomponent --}}
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

    <!-- Register Report Graph Section -->
    <div class="row no-print" id="register_report_chart_container" style="margin-bottom: 20px;">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('Payment Methods Trend')])
                <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                    <canvas id="register_report_chart"></canvas>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('report.register_report') . ' - ' . __('report.summary')])
            <table class="table   table-bordered table-striped ajax_view hide-footer" id="register_report_table" style="min-width: max-content;">
                <thead>
                    <tr>
                        <th>@lang('report.open_time')</th>
                        <th>@lang('report.close_time')</th>
                        <th>@lang('sale.location')</th>
                        <th>@lang('report.user')</th>
                        <th>@lang('cash_register.total_card_slips')</th>
                        <th>@lang('cash_register.total_cheques')</th>
                        <th>@lang('cash_register.total_cash')</th>
                        <th>@lang('lang_v1.total_bank_transfer')</th>
                        <th>@lang('lang_v1.total_advance_payment')</th>
                        <th>{{$payment_types['custom_pay_1']}}</th>
                        <th>{{$payment_types['custom_pay_2']}}</th>
                        <th>{{$payment_types['custom_pay_3']}}</th>
                        <th>{{$payment_types['custom_pay_4']}}</th>
                        <th>{{$payment_types['custom_pay_5']}}</th>
                        <th>{{$payment_types['custom_pay_6']}}</th>
                        <th>{{$payment_types['custom_pay_7']}}</th>
                        <th>@lang('cash_register.other_payments')</th>
                        <th>@lang('sale.total')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="bg-gray font-17 text-center footer-total">
                        <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                        <td class="footer_total_card_payment"></td>
                        <td class="footer_total_cheque_payment"></td>
                        <td class="footer_total_cash_payment"></td>
                        <td class="footer_total_bank_transfer_payment"></td>
                        <td class="footer_total_advance_payment"></td>
                        <td class="footer_total_custom_pay_1"></td>
                        <td class="footer_total_custom_pay_2"></td>
                        <td class="footer_total_custom_pay_3"></td>
                        <td class="footer_total_custom_pay_4"></td>
                        <td class="footer_total_custom_pay_5"></td>
                        <td class="footer_total_custom_pay_6"></td>
                        <td class="footer_total_custom_pay_7"></td>
                        <td class="footer_total_other_payments"></td>
                        <td class="footer_total"></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
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

<script type="text/javascript">
    var registerReportChart = null;
    
    function loadRegisterReportChart() {
        var start_date = $('#register_report_date_range').data('daterangepicker') 
            ? $('#register_report_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD')
            : null;
        var end_date = $('#register_report_date_range').data('daterangepicker')
            ? $('#register_report_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD')
            : null;
        var user_id = $('#register_user_id').val();
        
        // Build data object, only include non-null values
        var requestData = {};
        if (start_date) requestData.start_date = start_date;
        if (end_date) requestData.end_date = end_date;
        if (user_id) requestData.user_id = user_id;
        
        $.ajax({
            url: '/reports/register-report-chart-data',
            method: 'GET',
            data: requestData,
            success: function(response) {
                if (response.success && response.labels && response.total) {
                    var ctx = document.getElementById('register_report_chart');
                    if (!ctx) {
                        console.error('Chart canvas element not found');
                        return;
                    }
                    ctx = ctx.getContext('2d');
                    
                    if (registerReportChart) {
                        registerReportChart.destroy();
                    }
                    
                    registerReportChart = new Chart(ctx, {
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
                                    text: '@lang("Payment Methods Trend")',
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
                                    beginAtZero: true,
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
                console.error('Error loading register report chart data:', {xhr, status, error, response: xhr.responseJSON});
            }
        });
    }
    
    $(document).ready(function() {
        // Load chart on page load
        setTimeout(function() {
            loadRegisterReportChart();
        }, 1000);
        
        // Reload chart when filters change
        $('#register_user_id').on('change', function() {
            loadRegisterReportChart();
        });
        
        $('#register_report_date_range').on('apply.daterangepicker', function() {
            loadRegisterReportChart();
        });
    });
</script>
@endsection