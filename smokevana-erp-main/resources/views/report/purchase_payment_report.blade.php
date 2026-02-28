@extends('layouts.app')
@section('title', __('lang_v1.purchase_payment_report'))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="admin-amazon-page report-amazon-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('lang_v1.purchase_payment_report')}}</h1>
</section>

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
                            {{-- <div class="row">
                                <div class="col-md-12">
                                    @component('components.filters', ['title' => __('report.filters')]) --}}
                                    {!! Form::open(['url' => '#', 'method' => 'get', 'id' =>
                                    'purchase_payment_report_form' ]) !!}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('supplier_id', __('purchase.supplier') . ':') !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-user"></i>
                                                </span>
                                                {!! Form::select('supplier_id', $suppliers, null, ['class' =>
                                                'form-control select2', 'style' => 'width:100%', 'placeholder' =>
                                                __('messages.please_select'), 'required']); !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('location_id', __('purchase.business_location').':') !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-map-marker"></i>
                                                </span>
                                                {!! Form::select('location_id', $business_locations, null, ['class' =>
                                                'form-control select2', 'style' => 'width:100%', 'placeholder' =>
                                                __('messages.please_select'), 'required']); !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">

                                            {!! Form::label('ppr_date_filter', __('report.date_range') . ':') !!}
                                            {!! Form::text('date_range', null, ['placeholder' =>
                                            __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' =>
                                            'ppr_date_filter', 'readonly']); !!}
                                        </div>
                                    </div>
                                    {!! Form::close() !!}

                                    {{-- {{-- @endcomponent --}}
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

            <!-- Purchase Payment Report Graph Section -->
            <div class="row no-print" id="purchase_payment_report_chart_container" style="margin-bottom: 20px;">
                <div class="col-md-12">
                    @component('components.widget', ['class' => 'box-primary', 'title' => __('Purchase Payment Trend'), 'title_svg' => '<i class="fa fa-chart-line"></i>'])
                        <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                            <canvas id="purchase_payment_report_chart"></canvas>
                        </div>
                    @endcomponent
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.purchase_payment_report'), 'title_svg' => '<i class="fa fa-table"></i>'])
                    <div >
                        <table class="table  table-bordered table-striped" id="purchase_payment_report_table" style="min-width: max-content;">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>@lang('purchase.ref_no')</th>
                                    <th>@lang('lang_v1.paid_on')</th>
                                    <th>@lang('sale.amount')</th>
                                    <th>@lang('purchase.supplier')</th>
                                    <th>@lang('lang_v1.payment_method')</th>
                                    <th>@lang('lang_v1.purchase')</th>
                                    <th>@lang('messages.action')</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-17 footer-total text-left">
                                    <td colspan="3"><strong>@lang('sale.total'):</strong></td>
                                    <td  class="footer_total_amount text-left"><span class="display_currency" id="footer_total_amount"
                                            data-currency_symbol="true"></span></td>
                                    <td colspan="4"></td>
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
    var purchasePaymentReportChart = null;
    
    function loadPurchasePaymentReportChart() {
        var start_date = $('#ppr_date_filter').data('daterangepicker') 
            ? $('#ppr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD')
            : null;
        var end_date = $('#ppr_date_filter').data('daterangepicker')
            ? $('#ppr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD')
            : null;
        var location_id = $('#location_id').val();
        var supplier_id = $('#supplier_id').val();
        
        // Build data object, only include non-null values
        var requestData = {};
        if (start_date) requestData.start_date = start_date;
        if (end_date) requestData.end_date = end_date;
        if (location_id) requestData.location_id = location_id;
        if (supplier_id) requestData.supplier_id = supplier_id;
        
        $.ajax({
            url: '/reports/purchase-payment-report-chart-data',
            method: 'GET',
            data: requestData,
            success: function(response) {
                if (response.success && response.labels && response.total) {
                    var ctx = document.getElementById('purchase_payment_report_chart');
                    if (!ctx) {
                        console.error('Chart canvas element not found');
                        return;
                    }
                    ctx = ctx.getContext('2d');
                    
                    if (purchasePaymentReportChart) {
                        purchasePaymentReportChart.destroy();
                    }
                    
                    purchasePaymentReportChart = new Chart(ctx, {
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
                                    text: '@lang("Purchase Payment Trend")',
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
                console.error('Error loading purchase payment report chart data:', {xhr, status, error, response: xhr.responseJSON});
            }
        });
    }
    
    $(document).ready(function() {
        // Load chart on page load
        setTimeout(function() {
            loadPurchasePaymentReportChart();
        }, 1000);
        
        // Reload chart when filters change
        $('#location_id, #supplier_id').on('change', function() {
            loadPurchasePaymentReportChart();
        });
        
        $('#ppr_date_filter').on('apply.daterangepicker', function() {
            loadPurchasePaymentReportChart();
        });
    });
</script>
@endsection