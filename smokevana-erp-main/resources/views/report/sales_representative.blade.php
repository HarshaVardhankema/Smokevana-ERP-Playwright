@extends('layouts.app')
@section('title', __('report.sales_representative'))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="report-amazon-page">
<!-- Amazon banner -->
<div class="sr-banner amazon-theme-banner">
    <div class="banner-inner">
        <div class="banner-icon"><i class="fas fa-chart-line" aria-hidden="true"></i></div>
        <div class="banner-text">
            <h1 class="banner-title">{{ __('report.sales_representative') }}</h1>
            <p class="banner-subtitle">@lang('report.sales_representative_expenses')</p>
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
                            'getStockReport']), 'method' => 'get', 'id' => 'sales_representative_filter_form' ]) !!}
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('sr_id', __('report.user') . ':') !!}
                                    {!! Form::select('sr_id', $users, null, ['class' => 'form-control select2', 'style'
                                    => 'width:100%', 'placeholder' => __('report.all_users')]); !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('sr_business_id', __('business.business_location') . ':') !!}
                                    {!! Form::select('sr_business_id', $business_locations, null, ['class' =>
                                    'form-control select2', 'style' => 'width:100%']); !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">

                                    {!! Form::label('sr_date_filter', __('report.date_range') . ':') !!}
                                    {!! Form::text('date_range', null, ['placeholder' =>
                                    __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' =>
                                    'sr_date_filter', 'readonly']); !!}
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

    <!-- Sales Representative Graph Section -->
    <div class="row no-print" id="sales_representative_chart_container" style="margin-bottom: 20px;">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('Sales Representative Trend')])
                <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                    <canvas id="sales_representative_chart"></canvas>
                </div>
            @endcomponent
        </div>
    </div>

    <!-- Summary -->
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['title' => __('report.summary')])
            <h3 class="text-muted">
                {{ __('report.total_sell') }} - {{ __('lang_v1.total_sales_return') }}:
                <span id="sr_total_sales">
                    <i class="fas fa-sync fa-spin fa-fw"></i>
                </span>
                -
                <span id="sr_total_sales_return">
                    <i class="fas fa-sync fa-spin fa-fw"></i>
                </span>
                =
                <span id="sr_total_sales_final">
                    <i class="fas fa-sync fa-spin fa-fw"></i>
                </span>
            </h3>
            <div class="hide" id="total_payment_with_commsn_div">
                <h3 class="text-muted">
                    {{ __('lang_v1.total_payment_with_commsn') }}:
                    <span id="total_payment_with_commsn">
                        <i class="fas fa-sync fa-spin fa-fw"></i>
                    </span>
                </h3>
            </div>
            <div class="hide" id="total_commission_div">
                <h3 class="text-muted">
                    {{ __('lang_v1.total_sale_commission') }}:
                    <span id="sr_total_commission">
                        <i class="fas fa-sync fa-spin fa-fw"></i>
                    </span>
                </h3>
            </div>
            <h3 class="text-muted">
                {{ __('report.total_expense') }}:
                <span id="sr_total_expenses">
                    <i class="fas fa-sync fa-spin fa-fw"></i>
                </span>
            </h3>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#sr_sales_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-cog"
                                aria-hidden="true"></i> @lang('lang_v1.sales_added')</a>
                    </li>

                    <li>
                        <a href="#sr_commission_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-cog"
                                aria-hidden="true"></i> @lang('lang_v1.sales_with_commission')</a>
                    </li>

                    <li>
                        <a href="#sr_expenses_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-cog"
                                aria-hidden="true"></i> @lang('expense.expenses')</a>
                    </li>

                    @if(!empty($pos_settings['cmmsn_calculation_type']) && $pos_settings['cmmsn_calculation_type'] ==
                    'payment_received')
                    <li>
                        <a href="#sr_payments_with_cmmsn_tab" data-toggle="tab" aria-expanded="true"><i
                                class="fa fa-cog" aria-hidden="true"></i> @lang('lang_v1.payments_with_cmmsn')</a>
                    </li>
                    @endif
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="sr_sales_tab">
                        @include('report.partials.sales_representative_sales')
                    </div>

                    <div class="tab-pane" id="sr_commission_tab">
                        @include('report.partials.sales_representative_commission')
                    </div>

                    <div class="tab-pane" id="sr_expenses_tab">
                        @include('report.partials.sales_representative_expenses')
                    </div>

                    @if(!empty($pos_settings['cmmsn_calculation_type']) && $pos_settings['cmmsn_calculation_type'] ==
                    'payment_received')
                    <div class="tab-pane" id="sr_payments_with_cmmsn_tab">
                        @include('report.partials.sales_representative_payments_with_cmmsn')
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

</section>
<!-- /.content -->
</div>
<div class="modal fade view_register" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@endsection

@section('javascript')
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>

<script type="text/javascript">
    var salesRepresentativeChart = null;
    
    function loadSalesRepresentativeChart() {
        var start_date = $('#sr_date_filter').data('daterangepicker') 
            ? $('#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD')
            : null;
        var end_date = $('#sr_date_filter').data('daterangepicker')
            ? $('#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD')
            : null;
        var location_id = $('#sr_business_id').val();
        var created_by = $('#sr_id').val();
        
        // Build data object, only include non-null values
        var requestData = {};
        if (start_date) requestData.start_date = start_date;
        if (end_date) requestData.end_date = end_date;
        if (location_id) requestData.location_id = location_id;
        if (created_by) requestData.created_by = created_by;
        
        $.ajax({
            url: '/reports/sales-representative-chart-data',
            method: 'GET',
            data: requestData,
            success: function(response) {
                if (response.success && response.labels && response.sales) {
                    var ctx = document.getElementById('sales_representative_chart');
                    if (!ctx) {
                        console.error('Chart canvas element not found');
                        return;
                    }
                    ctx = ctx.getContext('2d');
                    
                    if (salesRepresentativeChart) {
                        salesRepresentativeChart.destroy();
                    }
                    
                    salesRepresentativeChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: response.labels,
                            datasets: [
                                {
                                    label: '@lang("report.total_sell")',
                                    data: response.sales,
                                    borderColor: 'rgb(75, 192, 192)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: '@lang("lang_v1.total_sales_return")',
                                    data: response.sales_return,
                                    borderColor: 'rgb(255, 99, 132)',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: '@lang("report.total_expense")',
                                    data: response.expenses,
                                    borderColor: 'rgb(255, 206, 86)',
                                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: '@lang("Net Sales")',
                                    data: response.net_sales,
                                    borderColor: 'rgb(153, 102, 255)',
                                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
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
                                    text: '@lang("Sales Representative Trend")',
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
                console.error('Error loading sales representative chart data:', {xhr, status, error, response: xhr.responseJSON});
            }
        });
    }
    
    $(document).ready(function() {
        // Load chart on page load
        setTimeout(function() {
            loadSalesRepresentativeChart();
        }, 1000);
        
        // Reload chart when filters change
        $('#sr_business_id, #sr_id').on('change', function() {
            loadSalesRepresentativeChart();
        });
        
        $('#sr_date_filter').on('apply.daterangepicker', function() {
            loadSalesRepresentativeChart();
        });
    });
</script>
@endsection