@extends('layouts.app')
@section('title', __('report.profit_loss'))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="report-amazon-page">
    <!-- Content Header (Page header) -->
    <section class="content-header amazon-theme-banner">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('report.profit_loss')
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="print_section">
            <h2>{{ session()->get('business.name') }} - @lang('report.profit_loss')</h2>
        </div>

        <div class="row no-print">
            <div class="col-md-3 col-md-offset-7 col-xs-6">
                <div class="input-group">
                    <span class="input-group-addon bg-light-blue"><i class="fa fa-map-marker"></i></span>
                    <select class="form-control select2" id="profit_loss_location_filter">
                        @foreach ($business_locations as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2 col-xs-6">
                <div class="form-group pull-right">
                    <div class="input-group">
                        <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" id="profit_loss_date_filter">
                            <span>
                                <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                            </span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profit/Loss Graph Section -->
        <div class="row no-print" id="profit_loss_chart_container" style="margin-bottom: 20px;">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-primary', 'title' => __('Monthly Profit and Loss Chart')])
                    <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                        <canvas id="profit_loss_chart"></canvas>
                    </div>
                @endcomponent
            </div>
        </div>
        
        <div class="row">
            <div id="pl_data_div">
            </div>
        </div>


        <div class="row no-print">
            <div class="col-sm-12 tw-mb-2">
                {{-- <button type="button" class="btn btn-primary pull-right" 
            aria-label="Print" onclick="window.print();"
            ><i class="fa fa-print"></i> @lang( 'messages.print' )</button> --}}

                <button class="tw-dw-btn tw-dw-btn-primary tw-text-white pull-right" aria-label="Print"
                    onclick="window.print();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-printer">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                        <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                    </svg> @lang('messages.print')
                </button>
            </div>
        </div>
        <div class="row no-print">
            <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#profit_by_products" data-toggle="tab" aria-expanded="true"><i class="fa fa-cubes"
                                    aria-hidden="true"></i> @lang('lang_v1.profit_by_products')</a>
                        </li>

                        <li>
                            <a href="#profit_by_categories" data-toggle="tab" aria-expanded="true"><i class="fa fa-tags"
                                    aria-hidden="true"></i> @lang('lang_v1.profit_by_categories')</a>
                        </li>

                        <li>
                            <a href="#profit_by_brands" data-toggle="tab" aria-expanded="true"><i class="fa fa-diamond"
                                    aria-hidden="true"></i> @lang('lang_v1.profit_by_brands')</a>
                        </li>

                        <li>
                            <a href="#profit_by_locations" data-toggle="tab" aria-expanded="true"><i
                                    class="fa fa-map-marker" aria-hidden="true"></i> @lang('lang_v1.profit_by_locations')</a>
                        </li>

                        <li>
                            <a href="#profit_by_invoice" data-toggle="tab" aria-expanded="true"><i class="fa fa-file-alt"
                                    aria-hidden="true"></i> @lang('lang_v1.profit_by_invoice')</a>
                        </li>

                        <li>
                            <a href="#profit_by_date" data-toggle="tab" aria-expanded="true"><i class="fa fa-calendar"
                                    aria-hidden="true"></i> @lang('lang_v1.profit_by_date')</a>
                        </li>
                        <li>
                            <a href="#profit_by_customer" data-toggle="tab" aria-expanded="true"><i class="fa fa-user"
                                    aria-hidden="true"></i> @lang('lang_v1.profit_by_customer')</a>
                        </li>
                        <li>
                            <a href="#profit_by_day" data-toggle="tab" aria-expanded="true"><i class="fa fa-calendar"
                                    aria-hidden="true"></i> @lang('lang_v1.profit_by_day')</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="profit_by_products">
                            @include('report.partials.profit_by_products')
                        </div>

                        <div class="tab-pane" id="profit_by_categories">
                            @include('report.partials.profit_by_categories')
                        </div>

                        <div class="tab-pane" id="profit_by_brands">
                            @include('report.partials.profit_by_brands')
                        </div>

                        <div class="tab-pane" id="profit_by_locations">
                            @include('report.partials.profit_by_locations')
                        </div>

                        <div class="tab-pane" id="profit_by_invoice">
                            @include('report.partials.profit_by_invoice')
                        </div>

                        <div class="tab-pane" id="profit_by_date">
                            @include('report.partials.profit_by_date')
                        </div>

                        <div class="tab-pane" id="profit_by_customer">
                            @include('report.partials.profit_by_customer')
                        </div>

                        <div class="tab-pane" id="profit_by_day">

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>
    <!-- /.content -->
</div>
@stop
@section('javascript')
    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>

    <script type="text/javascript">
        var profitLossChart = null;
        
        function loadProfitLossChart() {
            var start_date = $('#profit_loss_date_filter').data('daterangepicker') 
                ? $('#profit_loss_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD')
                : null;
            var end_date = $('#profit_loss_date_filter').data('daterangepicker')
                ? $('#profit_loss_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD')
                : null;
            var location_id = $('#profit_loss_location_filter').val();
            
            $.ajax({
                url: '/reports/profit-loss-chart-data',
                method: 'GET',
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    location_id: location_id
                },
                success: function(response) {
                    if (response.success && response.labels && response.data) {
                        var ctx = document.getElementById('profit_loss_chart').getContext('2d');
                        
                        // Destroy existing chart if it exists
                        if (profitLossChart) {
                            profitLossChart.destroy();
                        }
                        
                        // Prepare data with colors based on positive/negative values
                        var backgroundColors = response.data.map(function(value) {
                            return value >= 0 ? 'rgb(34, 139, 34)' : 'rgb(255, 99, 132)'; // Green for positive, Red for negative
                        });
                        
                        var borderColors = response.data.map(function(value) {
                            return value >= 0 ? 'rgb(34, 139, 34)' : 'rgb(255, 99, 132)';
                        });
                        
                        // Create new bar chart
                        profitLossChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: response.labels,
                                datasets: [{
                                    label: 'Profit / Loss',
                                    data: response.data,
                                    backgroundColor: backgroundColors,
                                    borderColor: borderColors,
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: '@lang("Monthly Profit and Loss Chart")',
                                        font: {
                                            size: 18,
                                            weight: 'bold'
                                        },
                                        padding: { top: 10, bottom: 20 }
                                    },
                                    legend: {
                                        display: true,
                                        position: 'bottom',
                                        labels: {
                                            generateLabels: function(chart) {
                                                return [
                                                    {
                                                        text: '@lang("Positive Liquidity")',
                                                        fillStyle: 'rgb(34, 139, 34)',
                                                        strokeStyle: 'rgb(34, 139, 34)',
                                                        lineWidth: 1,
                                                        hidden: false,
                                                        index: 0
                                                    },
                                                    {
                                                        text: '@lang("Negative Liquidity")',
                                                        fillStyle: 'rgb(255, 99, 132)',
                                                        strokeStyle: 'rgb(255, 99, 132)',
                                                        lineWidth: 1,
                                                        hidden: false,
                                                        index: 1
                                                    }
                                                ];
                                            }
                                        }
                                    },
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false,
                                        callbacks: {
                                            label: function(context) {
                                                var label = context.dataset.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
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
                                    mode: 'index',
                                    axis: 'x',
                                    intersect: false
                                }
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading chart data:', error);
                }
            });
        }
        
        $(document).ready(function() {
            // Load chart on page load
            setTimeout(function() {
                loadProfitLossChart();
            }, 500);
            
            // Reload chart when filters change
            $('#profit_loss_location_filter').on('change', function() {
                loadProfitLossChart();
            });
            
            // Hook into updateProfitLoss function to reload chart when date changes
            if (typeof window.updateProfitLoss === 'function') {
                var originalUpdateProfitLoss = window.updateProfitLoss;
                window.updateProfitLoss = function(start, end, location_id, selector) {
                    originalUpdateProfitLoss(start, end, location_id, selector);
                    setTimeout(function() {
                        loadProfitLossChart();
                    }, 300);
                };
            }
            
            profit_by_products_table = $('#profit_by_products_table').DataTable({
                processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                serverSide: true,
                fixedHeader:false,
                scrollX:true,
                "ajax": {
                    "url": "/reports/get-profit/product",
                    "data": function(d) {
                        d.start_date = $('#profit_loss_date_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#profit_loss_date_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.location_id = $('#profit_loss_location_filter').val();
                    }
                },
                columns: [{
                        data: 'product',
                        name: 'product'
                    },
                    {
                        data: 'gross_profit',
                        "searchable": false
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var total_profit = 0;
                    for (var r in data) {
                        total_profit += $(data[r].gross_profit).data('orig-value') ?
                            parseFloat($(data[r].gross_profit).data('orig-value')) : 0;
                    }

                    $('#profit_by_products_table .footer_total').html(__currency_trans_from_en(
                        total_profit));
                }
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr('href');
                if (target == '#profit_by_categories') {
                    if (typeof profit_by_categories_datatable == 'undefined') {
                    //     profit_by_categories_datatable = $('#profit_by_categories_table').DataTable({
                    //         processing: true,
                    // language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                    //         scrollX:true,
                    //         serverSide: true,
                    //         fixedHeader:false,
                    //         "ajax": {
                    //             "url": "/reports/get-profit/category",
                    //             "data": function(d) {
                    //                 d.start_date = $('#profit_loss_date_filter')
                    //                     .data('daterangepicker')
                    //                     .startDate.format('YYYY-MM-DD');
                    //                 d.end_date = $('#profit_loss_date_filter')
                    //                     .data('daterangepicker')
                    //                     .endDate.format('YYYY-MM-DD');
                    //                 d.location_id = $('#profit_loss_location_filter').val();
                    //             }
                    //         },
                    //         columns: [{
                    //                 data: 'category',
                    //                 name: 'C.name'
                    //             },
                    //             {
                    //                 data: 'gross_profit',
                    //                 "searchable": false
                    //             },
                    //         ],
                    //         footerCallback: function(row, data, start, end, display) {
                    //             var total_profit = 0;
                    //             for (var r in data) {
                    //                 total_profit += $(data[r].gross_profit).data('orig-value') ?
                    //                     parseFloat($(data[r].gross_profit).data('orig-value')) :
                    //                     0;
                    //             }

                    //             $('#profit_by_categories_table .footer_total').html(
                    //                 __currency_trans_from_en(total_profit));
                    //         },
                    //     });
                    profit_by_categories_datatable.ajax.reload();
                    } else {
                        profit_by_categories_datatable.ajax.reload();
                    }
                } else if (target == '#profit_by_brands') {
                    if (typeof profit_by_brands_datatable == 'undefined') {
                    //     profit_by_brands_datatable = $('#profit_by_brands_table').DataTable({
                    //         processing: true,
                    // language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                    //         scrollX:true,
                    //         serverSide: true,
                    //         fixedHeader:false,
                    //         "ajax": {
                    //             "url": "/reports/get-profit/brand",
                    //             "data": function(d) {
                    //                 d.start_date = $('#profit_loss_date_filter')
                    //                     .data('daterangepicker')
                    //                     .startDate.format('YYYY-MM-DD');
                    //                 d.end_date = $('#profit_loss_date_filter')
                    //                     .data('daterangepicker')
                    //                     .endDate.format('YYYY-MM-DD');
                    //                 d.location_id = $('#profit_loss_location_filter').val();
                    //             }
                    //         },
                    //         columns: [{
                    //                 data: 'brand',
                    //                 name: 'B.name'
                    //             },
                    //             {
                    //                 data: 'gross_profit',
                    //                 "searchable": false
                    //             },
                    //         ],
                    //         footerCallback: function(row, data, start, end, display) {
                    //             var total_profit = 0;
                    //             for (var r in data) {
                    //                 total_profit += $(data[r].gross_profit).data('orig-value') ?
                    //                     parseFloat($(data[r].gross_profit).data('orig-value')) :
                    //                     0;
                    //             }

                    //             $('#profit_by_brands_table .footer_total').html(
                    //                 __currency_trans_from_en(total_profit));
                    //         },
                    //     });
                    profit_by_brands_datatable.ajax.reload();
                    } else {
                        profit_by_brands_datatable.ajax.reload();
                    }
                } else if (target == '#profit_by_locations') {
                    if (typeof profit_by_locations_datatable == 'undefined') {
                    //     profit_by_locations_datatable = $('#profit_by_locations_table').DataTable({
                    //         processing: true,
                    // language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                    //         serverSide: true,
                    //         fixedHeader:false,
                    //         "ajax": {
                    //             "url": "/reports/get-profit/location",
                    //             "data": function(d) {
                    //                 d.start_date = $('#profit_loss_date_filter')
                    //                     .data('daterangepicker')
                    //                     .startDate.format('YYYY-MM-DD');
                    //                 d.end_date = $('#profit_loss_date_filter')
                    //                     .data('daterangepicker')
                    //                     .endDate.format('YYYY-MM-DD');
                    //                 d.location_id = $('#profit_loss_location_filter').val();
                    //             }
                    //         },
                    //         columns: [{
                    //                 data: 'location',
                    //                 name: 'L.name'
                    //             },
                    //             {
                    //                 data: 'gross_profit',
                    //                 "searchable": false
                    //             },
                    //         ],
                    //         footerCallback: function(row, data, start, end, display) {
                    //             var total_profit = 0;
                    //             for (var r in data) {
                    //                 total_profit += $(data[r].gross_profit).data('orig-value') ?
                    //                     parseFloat($(data[r].gross_profit).data('orig-value')) :
                    //                     0;
                    //             }

                    //             $('#profit_by_locations_table .footer_total').html(
                    //                 __currency_trans_from_en(total_profit));
                    //         },
                    //     });
                    profit_by_locations_datatable.ajax.reload();
                    } else {
                        profit_by_locations_datatable.ajax.reload();
                    }
                } else if (target == '#profit_by_invoice') {
                    if (typeof profit_by_invoice_datatable == 'undefined') {
                    //     profit_by_invoice_datatable = $('#profit_by_invoice_table').DataTable({
                    //         processing: true,
                    // language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                    //         serverSide: true,
                    //         fixedHeader:false,
                    //         scrollX:true,
                    //         "ajax": {
                    //             "url": "/reports/get-profit/invoice",
                    //             "data": function(d) {
                    //                 d.start_date = $('#profit_loss_date_filter')
                    //                     .data('daterangepicker')
                    //                     .startDate.format('YYYY-MM-DD');
                    //                 d.end_date = $('#profit_loss_date_filter')
                    //                     .data('daterangepicker')
                    //                     .endDate.format('YYYY-MM-DD');
                    //                 d.location_id = $('#profit_loss_location_filter').val();
                    //             }
                    //         },
                    //         columns: [{
                    //                 data: 'invoice_no',
                    //                 name: 'sale.invoice_no'
                    //             },
                    //             {
                    //                 data: 'gross_profit',
                    //                 "searchable": false
                    //             },
                    //         ],
                    //         footerCallback: function(row, data, start, end, display) {
                    //             var total_profit = 0;
                    //             for (var r in data) {
                    //                 total_profit += $(data[r].gross_profit).data('orig-value') ?
                    //                     parseFloat($(data[r].gross_profit).data('orig-value')) :
                    //                     0;
                    //             }

                    //             $('#profit_by_invoice_table .footer_total').html(
                    //                 __currency_trans_from_en(total_profit));
                    //         },
                    //     });
                    profit_by_invoice_datatable.ajax.reload();
                    } else {
                        profit_by_invoice_datatable.ajax.reload();
                    }
                } else if (target == '#profit_by_date') {
                    if (typeof profit_by_date_datatable == 'undefined') {
                    //     profit_by_date_datatable = $('#profit_by_date_table').DataTable({
                    //         processing: true,
                    // language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                    //         serverSide: true,
                    //         fixedHeader:false,
                    //         scrollX:true,
                    //         "ajax": {
                    //             "url": "/reports/get-profit/date",
                    //             "data": function(d) {
                    //                 d.start_date = $('#profit_loss_date_filter')
                    //                     .data('daterangepicker')
                    //                     .startDate.format('YYYY-MM-DD');
                    //                 d.end_date = $('#profit_loss_date_filter')
                    //                     .data('daterangepicker')
                    //                     .endDate.format('YYYY-MM-DD');
                    //                 d.location_id = $('#profit_loss_location_filter').val();
                    //             }
                    //         },
                    //         columns: [{
                    //                 data: 'transaction_date',
                    //                 name: 'sale.transaction_date'
                    //             },
                    //             {
                    //                 data: 'gross_profit',
                    //                 "searchable": false
                    //             },
                    //         ],
                    //         footerCallback: function(row, data, start, end, display) {
                    //             var total_profit = 0;
                    //             for (var r in data) {
                    //                 total_profit += $(data[r].gross_profit).data('orig-value') ?
                    //                     parseFloat($(data[r].gross_profit).data('orig-value')) :
                    //                     0;
                    //             }

                    //             $('#profit_by_date_table .footer_total').html(
                    //                 __currency_trans_from_en(total_profit));
                    //         },
                    //     });
                        profit_by_date_datatable.ajax.reload();
                    } else {
                        profit_by_date_datatable.ajax.reload();
                    }
                } else if (target == '#profit_by_customer') {
                    if (typeof profit_by_customers_table == 'undefined') {
                    //     profit_by_customers_table = $('#profit_by_customer_table').DataTable({
                    //         processing: true,
                    // language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                    //         scrollX:true,
                    //         serverSide: true,
                    //         fixedHeader:false,
                    //         "ajax": {
                    //             "url": "/reports/get-profit/customer",
                    //             "data": function(d) {
                    //                 d.start_date = $('#profit_loss_date_filter')
                    //                     .data('daterangepicker')
                    //                     .startDate.format('YYYY-MM-DD');
                    //                 d.end_date = $('#profit_loss_date_filter')
                    //                     .data('daterangepicker')
                    //                     .endDate.format('YYYY-MM-DD');
                    //                 d.location_id = $('#profit_loss_location_filter').val();
                    //             }
                    //         },
                    //         columns: [{
                    //                 data: 'customer',
                    //                 name: 'CU.name'
                    //             },
                    //             {
                    //                 data: 'gross_profit',
                    //                 "searchable": false
                    //             },
                    //         ],
                    //         footerCallback: function(row, data, start, end, display) {
                    //             var total_profit = 0;
                    //             for (var r in data) {
                    //                 total_profit += $(data[r].gross_profit).data('orig-value') ?
                    //                     parseFloat($(data[r].gross_profit).data('orig-value')) :
                    //                     0;
                    //             }

                    //             $('#profit_by_customer_table .footer_total').html(
                    //                 __currency_trans_from_en(total_profit));
                    //         },
                    //     });
                        profit_by_customers_table.ajax.reload();
                    } else {
                        profit_by_customers_table.ajax.reload();
                    }
                } else if (target == '#profit_by_day') {
                    var start_date = $('#profit_loss_date_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');

                    var end_date = $('#profit_loss_date_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    var location_id = $('#profit_loss_location_filter').val();

                    var url = '/reports/get-profit/day?start_date=' + start_date + '&end_date=' + end_date +
                        '&location_id=' + location_id;
                    $.ajax({
                        url: url,
                        dataType: 'html',
                        success: function(result) {
                            $('#profit_by_day').html(result);
                            profit_by_days_table = $('#profit_by_day_table').DataTable({
                                "searching": false,
                                'paging': false,
                                'ordering': false,
                                buttons: [],
                                scrollX:true,
                            });
                            var total_profit = sum_table_col($('#profit_by_day_table'),
                                'gross-profit');
                            $('#profit_by_day_table .footer_total').text(total_profit);
                            __currency_convert_recursively($('#profit_by_day_table'));
                        },
                    });
                } else if (target == '#profit_by_products') {
                    profit_by_products_table.ajax.reload();
                }
            });
            // erp_custom
profit_by_categories_datatable = $('#profit_by_categories_table').DataTable({
    processing: true,
    language: { processing: `<div id="main_loader"><span class='loader'></span></div>` },
    scrollX: true,
    serverSide: true,
    fixedHeader: false,
    "ajax": {
        "url": "/reports/get-profit/category",
        "data": function (d) {
            d.start_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            d.end_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            d.location_id = $('#profit_loss_location_filter').val();
        }
    },
    columns: [{
        data: 'category',
        name: 'C.name'
    },
    {
        data: 'gross_profit',
        "searchable": false
    },
    ],
    footerCallback: function (row, data, start, end, display) {
        var total_profit = 0;
        for (var r in data) {
            total_profit += $(data[r].gross_profit).data('orig-value') ?
                parseFloat($(data[r].gross_profit).data('orig-value')) :
                0;
        }

        $('#profit_by_categories_table .footer_total').html(
            __currency_trans_from_en(total_profit));
    },
});

profit_by_brands_datatable = $('#profit_by_brands_table').DataTable({
    processing: true,
    language: { processing: `<div id="main_loader"><span class='loader'></span></div>` },
    scrollX: true,
    serverSide: true,
    fixedHeader: false,
    "ajax": {
        "url": "/reports/get-profit/brand",
        "data": function (d) {
            d.start_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            d.end_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            d.location_id = $('#profit_loss_location_filter').val();
        }
    },
    columns: [{
        data: 'brand',
        name: 'B.name'
    },
    {
        data: 'gross_profit',
        "searchable": false
    },
    ],
    footerCallback: function (row, data, start, end, display) {
        var total_profit = 0;
        for (var r in data) {
            total_profit += $(data[r].gross_profit).data('orig-value') ?
                parseFloat($(data[r].gross_profit).data('orig-value')) :
                0;
        }

        $('#profit_by_brands_table .footer_total').html(
            __currency_trans_from_en(total_profit));
    },
});

profit_by_locations_datatable = $('#profit_by_locations_table').DataTable({
    processing: true,
    language: { processing: `<div id="main_loader"><span class='loader'></span></div>` },
    serverSide: true,
    fixedHeader: false,
    "ajax": {
        "url": "/reports/get-profit/location",
        "data": function (d) {
            d.start_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            d.end_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            d.location_id = $('#profit_loss_location_filter').val();
        }
    },
    columns: [{
        data: 'location',
        name: 'L.name'
    },
    {
        data: 'gross_profit',
        "searchable": false
    },
    ],
    footerCallback: function (row, data, start, end, display) {
        var total_profit = 0;
        for (var r in data) {
            total_profit += $(data[r].gross_profit).data('orig-value') ?
                parseFloat($(data[r].gross_profit).data('orig-value')) :
                0;
        }

        $('#profit_by_locations_table .footer_total').html(
            __currency_trans_from_en(total_profit));
    },
});

profit_by_invoice_datatable = $('#profit_by_invoice_table').DataTable({
    processing: true,
    language: { processing: `<div id="main_loader"><span class='loader'></span></div>` },
    serverSide: true,
    fixedHeader: false,
    scrollX: true,
    "ajax": {
        "url": "/reports/get-profit/invoice",
        "data": function (d) {
            d.start_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            d.end_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            d.location_id = $('#profit_loss_location_filter').val();
        }
    },
    columns: [{
        data: 'invoice_no',
        name: 'sale.invoice_no'
    },
    {
        data: 'gross_profit',
        "searchable": false
    },
    ],
    footerCallback: function (row, data, start, end, display) {
        var total_profit = 0;
        for (var r in data) {
            total_profit += $(data[r].gross_profit).data('orig-value') ?
                parseFloat($(data[r].gross_profit).data('orig-value')) :
                0;
        }

        $('#profit_by_invoice_table .footer_total').html(
            __currency_trans_from_en(total_profit));
    },
});
profit_by_date_datatable = $('#profit_by_date_table').DataTable({
    processing: true,
    language: { processing: `<div id="main_loader"><span class='loader'></span></div>` },
    serverSide: true,
    fixedHeader: false,
    scrollX: true,
    "ajax": {
        "url": "/reports/get-profit/date",
        "data": function (d) {
            d.start_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            d.end_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            d.location_id = $('#profit_loss_location_filter').val();
        }
    },
    columns: [{
        data: 'transaction_date',
        name: 'sale.transaction_date'
    },
    {
        data: 'gross_profit',
        "searchable": false
    },
    ],
    footerCallback: function (row, data, start, end, display) {
        var total_profit = 0;
        for (var r in data) {
            total_profit += $(data[r].gross_profit).data('orig-value') ?
                parseFloat($(data[r].gross_profit).data('orig-value')) :
                0;
        }

        $('#profit_by_date_table .footer_total').html(
            __currency_trans_from_en(total_profit));
    },
});

profit_by_customers_table = $('#profit_by_customer_table').DataTable({
    processing: true,
    language: { processing: `<div id="main_loader"><span class='loader'></span></div>` },
    scrollX: true,
    serverSide: true,
    fixedHeader: false,
    "ajax": {
        "url": "/reports/get-profit/customer",
        "data": function (d) {
            d.start_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            d.end_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            d.location_id = $('#profit_loss_location_filter').val();
        }
    },
    columns: [{
        data: 'customer',
        name: 'CU.name'
    },
    {
        data: 'gross_profit',
        "searchable": false
    },
    ],
    footerCallback: function (row, data, start, end, display) {
        var total_profit = 0;
        for (var r in data) {
            total_profit += $(data[r].gross_profit).data('orig-value') ?
                parseFloat($(data[r].gross_profit).data('orig-value')) :
                0;
        }

        $('#profit_by_customer_table .footer_total').html(
            __currency_trans_from_en(total_profit));
    },
});


        });
    </script>

@endsection
