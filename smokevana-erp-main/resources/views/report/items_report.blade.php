@extends('layouts.app')
@section('title', __('lang_v1.items_report'))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="report-amazon-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('lang_v1.items_report')}}</h1>
</section>

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
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('ir_supplier_id', __('purchase.supplier') . ':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                        </span>
                                        {!! Form::select('ir_supplier_id', $suppliers, null, ['class' => 'form-control
                                        select2', 'placeholder' => __('lang_v1.all')]);!!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('ir_customer_id', __('contact.customer') . ':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                        </span>
                                        {!! Form::select('ir_customer_id', $customers, null, ['class' => 'form-control
                                        select2', 'placeholder' => __('lang_v1.all')]); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('ir_purchase_date_filter', __('purchase.purchase_date') . ':') !!}
                                    {!! Form::text('ir_purchase_date_filter', null, ['placeholder' =>
                                    __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('ir_sale_date_filter', __('lang_v1.sell_date') . ':') !!}
                                    {!! Form::text('ir_sale_date_filter', null, ['placeholder' =>
                                    __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('ir_location_id', __('purchase.business_location').':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-map-marker"></i>
                                        </span>
                                        {!! Form::select('ir_location_id', $business_locations, null, ['class' =>
                                        'form-control select2', 'placeholder' =>
                                        __('messages.please_select'), 'required']); !!}
                                    </div>
                                </div>
                            </div>
                            @if(Module::has('Manufacturing'))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <br>
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('only_mfg', 1, false,
                                            [ 'class' => 'input-icheck', 'id' => 'only_mfg_products']); !!} {{
                                            __('manufacturing::lang.only_mfg_products') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endif
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

    <!-- Items Report Graph Section -->
    <div class="row no-print" id="items_report_chart_container" style="margin-bottom: 20px;">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('Items Report Trend')])
                <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                    <canvas id="items_report_chart"></canvas>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row no-print">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="no-print">
                <table style="min-width: max-content;" class="table   table-bordered table-striped ajax_view hide-footer no-print" id="items_report_table">
                    <thead>
                        <tr>
                            <th style="min-width: 400px">@lang('sale.product')</th>
                            <th >@lang('product.sku')</th>
                            <th >@lang('lang_v1.description')</th>
                            <th >@lang('purchase.purchase_date')</th>
                            <th >@lang('lang_v1.purchase')</th>
                            <th >@lang('lang_v1.lot_number')</th>
                            <th >@lang('purchase.supplier')</th>
                            <th >@lang('lang_v1.purchase_price')</th>
                            <th >@lang('lang_v1.sell_date')</th>
                            <th >@lang('business.sale')</th>
                            <th >@lang('contact.customer')</th>
                            <th >@lang('sale.location')</th>
                            <th >@lang('lang_v1.sell_quantity')</th>
                            <th >@lang('lang_v1.selling_price')</th>
                            <th >@lang('sale.subtotal')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 text-left footer-total">
                            <td colspan="6"></td>
                            <td ><strong>@lang('sale.total'):</strong></td>
                            <td class="footer_total_pp" class="display_currency" data-currency_symbol="true"></td>
                            <td colspan="4"></td>
                            <td class="footer_total_qty"></td>
                            <td class="footer_total_sp" class="display_currency" data-currency_symbol="true"></td>
                            <td class="footer_total_subtotal" class="display_currency" data-currency_symbol="true"></td>
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
    var itemsReportChart = null;
    
    function loadItemsReportChart() {
        var purchase_start = $('#ir_purchase_date_filter').data('daterangepicker') 
            ? $('#ir_purchase_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD')
            : null;
        var purchase_end = $('#ir_purchase_date_filter').data('daterangepicker')
            ? $('#ir_purchase_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD')
            : null;
        var sale_start = $('#ir_sale_date_filter').data('daterangepicker') 
            ? $('#ir_sale_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD')
            : null;
        var sale_end = $('#ir_sale_date_filter').data('daterangepicker')
            ? $('#ir_sale_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD')
            : null;
        var location_id = $('#ir_location_id').val();
        var supplier_id = $('#ir_supplier_id').val();
        var customer_id = $('#ir_customer_id').val();
        
        // Build data object, only include non-null values
        var requestData = {};
        if (purchase_start) requestData.purchase_start = purchase_start;
        if (purchase_end) requestData.purchase_end = purchase_end;
        if (sale_start) requestData.sale_start = sale_start;
        if (sale_end) requestData.sale_end = sale_end;
        if (location_id) requestData.location_id = location_id;
        if (supplier_id) requestData.supplier_id = supplier_id;
        if (customer_id) requestData.customer_id = customer_id;
        
        $.ajax({
            url: '/reports/items-report-chart-data',
            method: 'GET',
            data: requestData,
            success: function(response) {
                if (response.success && response.labels && response.purchase_value) {
                    var ctx = document.getElementById('items_report_chart');
                    if (!ctx) {
                        console.error('Chart canvas element not found');
                        return;
                    }
                    ctx = ctx.getContext('2d');
                    
                    if (itemsReportChart) {
                        itemsReportChart.destroy();
                    }
                    
                    itemsReportChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: response.labels,
                            datasets: [
                                {
                                    label: '@lang("lang_v1.purchase_value")',
                                    data: response.purchase_value,
                                    borderColor: 'rgb(255, 99, 132)',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: '@lang("lang_v1.sale_value")',
                                    data: response.sale_value,
                                    borderColor: 'rgb(75, 192, 192)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: '@lang("report.profit")',
                                    data: response.profit,
                                    borderColor: 'rgb(255, 206, 86)',
                                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
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
                                    text: '@lang("Items Report Trend")',
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
                console.error('Error loading items report chart data:', {xhr, status, error, response: xhr.responseJSON});
            }
        });
    }
    
    $(document).ready(function() {
        // Load chart on page load
        setTimeout(function() {
            loadItemsReportChart();
        }, 1000);
        
        // Reload chart when filters change
        $('#ir_location_id, #ir_supplier_id, #ir_customer_id').on('change', function() {
            loadItemsReportChart();
        });
        
        $('#ir_purchase_date_filter, #ir_sale_date_filter').on('apply.daterangepicker', function() {
            loadItemsReportChart();
        });
    });
</script>
@endsection