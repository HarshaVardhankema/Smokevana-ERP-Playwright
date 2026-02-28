@extends('layouts.app')
@section('title', __('lang_v1.product_purchase_report'))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="admin-amazon-page report-amazon-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-xl tw-font-bold tw-text-black">{{ __('lang_v1.product_purchase_report')}}</h1>
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
                                <div class="col-md-12"> --}}
                                    {{-- @component('components.filters', ['title' => __('report.filters')]) --}}
                                    {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class,
                                    'getStockReport']), 'method' => 'get', 'id' => 'product_purchase_report_form' ]) !!}
                                    {{-- <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('search_product', __('lang_v1.search_product') . ':') !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-search"></i>
                                                </span>
                                                <input type="hidden" value="" id="variation_id">
                                                {!! Form::text('search_product', null, ['class' => 'form-control', 'id'
                                                => 'search_product', 'placeholder' =>
                                                __('lang_v1.search_product_placeholder'), 'autofocus']);!!}
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            {!! Form::label('supplier_id', __('purchase.supplier') . ':') !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-user"></i>
                                                </span>
                                                {!! Form::select('supplier_id', $suppliers, null, ['class' =>
                                                'form-control select2', 'style' => 'width:100%;', 'required']);!!}
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
                                                'form-control select2', 'style' => 'width:100%;', 'placeholder' =>
                                                __('messages.please_select'), 'required']); !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">

                                            {!! Form::label('product_pr_date_filter', __('report.date_range') . ':') !!}
                                            {!! Form::text('date_range', null, ['placeholder' =>
                                            __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' =>
                                            'product_pr_date_filter', 'readonly']); !!}
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            {!! Form::label('ppr_brand_id', __('product.brand').':') !!}
                                            {!! Form::select('ppr_brand_id', $brands, null, ['class' => 'form-control
                                            select2', 'style' => 'width:100%;', 'placeholder' => __('lang_v1.all')]);
                                            !!}
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                    {{-- @endcomponent --}}
                                    {{--
                                </div>
                            </div> --}}
                            
                                {{-- <button type="button" class="btn btn-primary"
                                    id="applyFiltersBtn">@lang('messages.apply')</button>
                                --}}
                               
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white"
                                data-dismiss="modal">@lang('messages.close')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Purchase Report Pie Chart Section -->
    <div class="row no-print" id="product_purchase_report_chart_container" style="margin-bottom: 20px;">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('Product Purchase Distribution'), 'title_svg' => '<i class="fa fa-chart-pie"></i>'])
                <div class="chart-container" style="position: relative; height: 500px; width: 100%;">
                    <canvas id="product_purchase_report_chart"></canvas>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.product_purchase_report'), 'title_svg' => '<i class="fa fa-table"></i>'])
            <div>
                <table class="table   table-bordered table-striped ajax_view hide-footer" id="product_purchase_report_table" style="min-width: max-content;" >
                    <thead>
                        <tr>
                            <th style="min-width: 500px">@lang('sale.product')</th>
                            <th >@lang('product.sku')</th>
                            <th >@lang('purchase.supplier')</th>
                            <th >@lang('purchase.ref_no')</th>
                            <th >@lang('messages.date')</th>
                            <th >@lang('sale.qty')</th>
                            <th >@lang('lang_v1.total_unit_adjusted')</th>
                            <th >@lang('lang_v1.unit_perchase_price')</th>
                            <th >@lang('sale.subtotal')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-left">
                            <td colspan="4"></td>
                            <td><strong>@lang('sale.total'):</strong></td>
                            <td class="footer_total_purchase"></td>
                            <td class="footer_total_adjusted"></td>
                            <td></td>
                            <td class="footer_subtotal"><span class="display_currency" data-currency_symbol="true"></span>
                            </td>
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

<script type="text/javascript">
    var productPurchaseReportChart = null;
    
    // Color palette for pie chart slices
    var pieChartColors = [
        'rgb(54, 162, 235)',   // Blue
        'rgb(255, 99, 132)',    // Red
        'rgb(75, 192, 192)',    // Teal
        'rgb(153, 102, 255)',   // Purple
        'rgb(255, 159, 64)',    // Orange
        'rgb(255, 206, 86)',    // Yellow
        'rgb(201, 203, 207)',   // Grey
        'rgb(34, 139, 34)',     // Green
        'rgb(255, 20, 147)',    // Pink
        'rgb(0, 191, 255)'      // Sky Blue
    ];
    
    function loadProductPurchaseReportChart() {
        var start_date = $('#product_pr_date_filter').data('daterangepicker') 
            ? $('#product_pr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD')
            : null;
        var end_date = $('#product_pr_date_filter').data('daterangepicker')
            ? $('#product_pr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD')
            : null;
        var location_id = $('#location_id').val();
        var supplier_id = $('#supplier_id').val();
        var brand_id = $('#ppr_brand_id').val();
        
        // Build data object, only include non-null values
        var requestData = {};
        if (start_date) requestData.start_date = start_date;
        if (end_date) requestData.end_date = end_date;
        if (location_id) requestData.location_id = location_id;
        if (supplier_id) requestData.supplier_id = supplier_id;
        if (brand_id) requestData.brand_id = brand_id;
        
        $.ajax({
            url: '/reports/product-purchase-report-pie-chart-data',
            method: 'GET',
            data: requestData,
            success: function(response) {
                if (response.success && response.labels && response.data) {
                    var ctx = document.getElementById('product_purchase_report_chart');
                    if (!ctx) {
                        console.error('Chart canvas element not found');
                        return;
                    }
                    ctx = ctx.getContext('2d');
                    
                    if (productPurchaseReportChart) {
                        productPurchaseReportChart.destroy();
                    }
                    
                    // Prepare colors for each slice
                    var backgroundColors = [];
                    var borderColors = [];
                    for (var i = 0; i < response.labels.length; i++) {
                        backgroundColors.push(pieChartColors[i % pieChartColors.length]);
                        borderColors.push('rgb(255, 255, 255)');
                    }
                    
                    // Calculate total for percentage display
                    var total = response.data.reduce(function(a, b) { return a + b; }, 0);
                    
                    productPurchaseReportChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: response.labels,
                            datasets: [{
                                data: response.data,
                                backgroundColor: backgroundColors,
                                borderColor: borderColors,
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: '@lang("Product Purchase Distribution")',
                                    font: { size: 18, weight: 'bold' },
                                    padding: { top: 10, bottom: 20 }
                                },
                                legend: { 
                                    display: true, 
                                    position: 'right',
                                    labels: {
                                        padding: 15,
                                        usePointStyle: true,
                                        font: { size: 12 },
                                        generateLabels: function(chart) {
                                            var data = chart.data;
                                            if (data.labels.length && data.datasets.length) {
                                                var dataset = data.datasets[0];
                                                return data.labels.map(function(label, i) {
                                                    var value = dataset.data[i];
                                                    var percentage = total > 0 ? ((value / total) * 100).toFixed(2) : 0;
                                                    return {
                                                        text: label + ' (' + percentage + '%)',
                                                        fillStyle: dataset.backgroundColor[i],
                                                        strokeStyle: dataset.borderColor[i],
                                                        lineWidth: dataset.borderWidth,
                                                        hidden: false,
                                                        index: i
                                                    };
                                                });
                                            }
                                            return [];
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            var label = context.label || '';
                                            var value = context.parsed || 0;
                                            var percentage = total > 0 ? ((value / total) * 100).toFixed(2) : 0;
                                            
                                            label += ': ' + __currency_trans_from_en(value);
                                            label += ' (' + percentage + '%)';
                                            return label;
                                        }
                                    }
                                },
                            },
                            animation: {
                                animateRotate: true,
                                animateScale: true
                            }
                        }
                    });
                } else {
                    console.warn('No chart data available', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading product purchase report pie chart data:', {xhr, status, error, response: xhr.responseJSON});
            }
        });
    }
    
    $(document).ready(function() {
        // Load chart on page load
        setTimeout(function() {
            loadProductPurchaseReportChart();
        }, 1000);
        
        // Reload chart when filters change
        $('#location_id, #supplier_id, #ppr_brand_id').on('change', function() {
            loadProductPurchaseReportChart();
        });
        
        $('#product_pr_date_filter').on('apply.daterangepicker', function() {
            loadProductPurchaseReportChart();
        });
    });
</script>
@endsection