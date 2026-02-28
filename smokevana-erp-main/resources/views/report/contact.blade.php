@extends('layouts.app')
@section('title', __('report.customer') . ' - ' . __('report.supplier') . ' ' . __('report.reports'))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="report-amazon-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.customer')}} & {{ __('report.supplier')}} {{ __('report.reports')}}</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
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


                            {{-- @component('components.filters', ['title' => __('report.filters')]) --}}

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('cg_customer_group_id', __( 'lang_v1.customer_group_name' ) . ':')
                                    !!}
                                    {!! Form::select('cnt_customer_group_id', $customer_group, null, ['class' =>
                                    'form-control select2', 'style' => 'width:100%', 'id' => 'cnt_customer_group_id']);
                                    !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('type', __( 'lang_v1.type' ) . ':') !!}
                                    {!! Form::select('contact_type', $types, null, ['class' => 'form-control select2',
                                    'style' => 'width:100%', 'id' => 'contact_type']); !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('cs_report_location_id', __( 'sale.location' ) . ':') !!}
                                    {!! Form::select('cs_report_location_id', $business_locations, null, ['class' =>
                                    'form-control select2', 'style' => 'width:100%', 'id' => 'cs_report_location_id']);
                                    !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('scr_contact_id', __( 'report.contact' ) . ':') !!}
                                    {!! Form::select('scr_contact_id', $contact_dropdown, null , ['class' =>
                                    'form-control select2', 'id' => 'scr_contact_id', 'placeholder' =>
                                    __('lang_v1.all')]); !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('scr_date_filter', __('report.date_range') . ':') !!}
                                    {!! Form::text('date_range', null, ['placeholder' =>
                                    __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' =>
                                    'scr_date_filter', 'readonly']); !!}
                                </div>
                            </div>

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

    <!-- Customer & Supplier Report Graph Section -->
    <div class="row no-print" id="customer_supplier_chart_container" style="margin-bottom: 20px;">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('Purchase & Sale Trend')])
                <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                    <canvas id="customer_supplier_chart"></canvas>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="supplier_report_tbl" style="min-width: max-content;">
                    <thead>
                        <tr>
                            <th>@lang('report.contact')</th>
                            <th>@lang('report.total_purchase')</th>
                            <th>@lang('lang_v1.total_purchase_return')</th>
                            <th>@lang('report.total_sell')</th>
                            <th>@lang('lang_v1.total_sell_return')</th>
                            <th>@lang('lang_v1.opening_balance_due')</th>
                            <th>@lang('report.total_due')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-left">
                            <td><strong>@lang('sale.total'):</strong></td>
                            <td><span class="display_currency footer_total_purchase"
                                    data-currency_symbol="true"></span></td>
                            <td><span class="display_currency footer_total_purchase_return"
                                    data-currency_symbol="true"></span></td>
                            <td><span class="display_currency footer_total_sell"
                                    data-currency_symbol="true"></span></td>
                            <td><span class="display_currency footer_total_sell_return"
                                    data-currency_symbol="true"></span></td>
                            <td><span class="display_currency footer_total_opening_bal_due"
                                    data-currency_symbol="true"></span></td>
                            <td><span class="display_currency footer_total_due" data-currency_symbol="true"></span>
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
@endsection

@section('javascript')
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>

<script type="text/javascript">
    var customerSupplierChart = null;
    
    function loadCustomerSupplierChart() {
        var start_date = null;
        var end_date = null;
        
        // Try to get dates from daterangepicker
        if ($('#scr_date_filter').length && $('#scr_date_filter').data('daterangepicker')) {
            start_date = $('#scr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
            end_date = $('#scr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
        }
        
        var location_id = $('#cs_report_location_id').val();
        var contact_type = $('#contact_type').val();
        var contact_id = $('#scr_contact_id').val();
        
        // Build data object, only include non-null values
        var requestData = {};
        if (start_date) requestData.start_date = start_date;
        if (end_date) requestData.end_date = end_date;
        if (location_id) requestData.location_id = location_id;
        if (contact_type) requestData.contact_type = contact_type;
        if (contact_id) requestData.contact_id = contact_id;
        
        $.ajax({
            url: '/reports/customer-supplier-chart-data',
            method: 'GET',
            data: requestData,
            success: function(response) {
                console.log('Chart data response:', response);
                
                var ctx = document.getElementById('customer_supplier_chart');
                if (!ctx) {
                    console.error('Chart canvas element not found');
                    return;
                }
                ctx = ctx.getContext('2d');
                
                if (customerSupplierChart) {
                    customerSupplierChart.destroy();
                }
                
                // Ensure we have data arrays, even if empty
                var labels = response.success && response.labels ? response.labels : [];
                var purchaseData = response.success && response.purchase ? response.purchase : [];
                var sellData = response.success && response.sell ? response.sell : [];
                
                // If no data, show empty chart with message
                if (labels.length === 0) {
                    labels = ['No Data'];
                    purchaseData = [0];
                    sellData = [0];
                }
                
                customerSupplierChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: '@lang("purchase.purchases")',
                                data: purchaseData,
                                borderColor: 'rgb(255, 99, 132)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            },
                            {
                                label: '@lang("sale.sells")',
                                data: sellData,
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: '@lang("Purchase & Sale Trend")',
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
            },
            error: function(xhr, status, error) {
                console.error('Error loading customer supplier chart data:', {xhr, status, error, response: xhr.responseJSON});
            }
        });
    }
    
    $(document).ready(function() {
        // Ensure Chart.js is loaded before trying to create chart
        if (typeof Chart === 'undefined') {
            console.error('Chart.js library not loaded');
            return;
        }
        
        // Wait a bit longer for daterangepicker to initialize if it exists
        setTimeout(function() {
            loadCustomerSupplierChart();
        }, 1500);
        
        // Reload chart when filters change
        $('#cs_report_location_id, #contact_type, #scr_contact_id').on('change', function() {
            setTimeout(function() {
                loadCustomerSupplierChart();
            }, 300);
        });
        
        $('#scr_date_filter').on('apply.daterangepicker', function() {
            setTimeout(function() {
                loadCustomerSupplierChart();
            }, 300);
        });
    });
</script>
@endsection