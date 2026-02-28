@extends('layouts.app')
@section('title', __('report.stock_report'))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="report-amazon-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('report.stock_report')}}</h1>
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
                            {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class,
                            'getStockReport']), 'method' => 'get', 'id' => 'stock_report_filter_form' ]) !!}
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control
                                    select2', 'style' => 'width:100%']);!!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('category_id', __('category.category') . ':') !!}
                                    {!! Form::select('category', $categories, null, ['placeholder' =>
                                    __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id'
                                    => 'category_id']); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                                    {!! Form::select('sub_category', array(), null, ['placeholder' =>
                                    __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id'
                                    => 'sub_category_id']); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('brand', __('product.brand') . ':') !!}
                                    {!! Form::select('brand', $brands, null, ['placeholder' => __('messages.all'),
                                    'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('unit',__('product.unit') . ':') !!}
                                    {!! Form::select('unit', $units, null, ['placeholder' => __('messages.all'), 'class'
                                    => 'form-control select2', 'style' => 'width:100%']); !!}
                                </div>
                            </div>
                            @if($show_manufacturing_data)
                            <div class="col-md-3">
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

    <!-- Stock Report Graph Section -->
    <div class="row no-print" id="stock_report_chart_container" style="margin-bottom: 20px;">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('Stock Value Trend')])
                <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                    <canvas id="stock_report_chart"></canvas>
                </div>
            @endcomponent
        </div>
    </div>

    @can('view_product_stock_value')
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid'])
            <table class="table no-border">
                <tr>
                    <td>@lang('report.closing_stock') (@lang('lang_v1.by_purchase_price'))</td>
                    <td>@lang('report.closing_stock') (@lang('lang_v1.by_sale_price'))</td>
                    <td>@lang('lang_v1.potential_profit')</td>
                    <td>@lang('lang_v1.profit_margin')</td>
                </tr>
                <tr>
                    <td>
                        <h3 id="closing_stock_by_pp" class="mb-0 mt-0"></h3>
                    </td>
                    <td>
                        <h3 id="closing_stock_by_sp" class="mb-0 mt-0"></h3>
                    </td>
                    <td>
                        <h3 id="potential_profit" class="mb-0 mt-0"></h3>
                    </td>
                    <td>
                        <h3 id="profit_margin" class="mb-0 mt-0"></h3>
                    </td>
                </tr>
            </table>
            @endcomponent
        </div>
    </div>
    @endcan
    <div class="row">
        <div class="col-md-12">
            <div class="tw-flex tw-justify-end tw-mb-2 no-print">
                <button id="stockReportPrintBtn" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs">
                    <i class="fa fa-print" aria-hidden="true"></i> @lang('messages.print')
                </button>
            </div>
            @component('components.widget', ['class' => 'box-solid'])
            @include('report.partials.stock_report_table')
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
    var stockReportChart = null;
    
    function loadStockReportChart() {
        var location_id = $('#location_id').val();
        
        // Get financial year for default dates
        var fy_start = moment().startOf('month').format('YYYY-MM-DD');
        var fy_end = moment().endOf('month').format('YYYY-MM-DD');
        
        $.ajax({
            url: '/reports/stock-report-chart-data',
            method: 'GET',
            data: {
                start_date: fy_start,
                end_date: fy_end,
                location_id: location_id
            },
            success: function(response) {
                if (response.success && response.labels && response.stock_value) {
                    var ctx = document.getElementById('stock_report_chart');
                    if (!ctx) {
                        console.error('Chart canvas element not found');
                        return;
                    }
                    ctx = ctx.getContext('2d');
                    
                    if (stockReportChart) {
                        stockReportChart.destroy();
                    }
                    
                    stockReportChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: response.labels,
                            datasets: [{
                                label: '@lang("Stock Value")',
                                data: response.stock_value,
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderWidth: 2,
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: '@lang("Stock Value Trend")',
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
                console.error('Error loading stock chart data:', {xhr, status, error, response: xhr.responseJSON});
            }
        });
    }
    
    $(document).ready(function() {
        // Load chart on page load
        setTimeout(function() {
            loadStockReportChart();
        }, 500);
        
        // Reload chart when location filter changes
        $('#location_id').on('change', function() {
            loadStockReportChart();
        });

        // Print button for stock report table (exclude Action column)
        $('#stockReportPrintBtn').on('click', function() {
            // Collect headers, skipping first column (Action). Prefer DataTables scroll header.
            var headers = [];
            var $headerCells = $('#stock_report_table_wrapper .dataTables_scrollHead th:visible');
            if ($headerCells.length === 0) {
                $headerCells = $('#stock_report_table thead th:visible');
            }
            $headerCells.each(function(index) {
                if (index === 0) { return; }
                headers.push($(this).text().trim());
            });

            // Collect body rows, skipping first cell (Action). Prefer DataTables scroll body.
            var rows = [];
            var $rows = $('#stock_report_table_wrapper .dataTables_scrollBody tbody tr:visible');
            if ($rows.length === 0) {
                $rows = $('#stock_report_table tbody tr:visible');
            }
            $rows.each(function() {
                var row = [];
                $(this).find('td:visible').each(function(index) {
                    if (index === 0) { return; }
                    row.push($(this).text().trim());
                });
                if (row.length) {
                    rows.push(row);
                }
            });

            var win = window.open('', '', 'height=800,width=1200');
            if (!win) {
                alert('Please allow popups for this site to print the stock report.');
                return;
            }

            var doc = win.document;
            doc.write('<html><head><title>@lang('report.stock_report') - Smokevana</title>');
            doc.write('<style>');
            doc.write('body{font-family:Arial,sans-serif;font-size:10px;margin:16px;}');
            doc.write('h2{margin-bottom:12px;}');
            doc.write('table{border-collapse:collapse;width:100%;}');
            doc.write('th,td{border:1px solid #000;padding:4px;text-align:left;white-space:nowrap;}');
            doc.write('thead th{background:#f0f0f0;font-weight:bold;}');
            doc.write('</style></head><body>');
            doc.write('<h2>@lang('report.stock_report')</h2>');
            doc.write('<table><thead><tr>');

            headers.forEach(function(header) {
                doc.write('<th>' + header + '</th>');
            });
            doc.write('</tr></thead><tbody>');

            rows.forEach(function(row) {
                doc.write('<tr>');
                row.forEach(function(cell) {
                    doc.write('<td>' + cell + '</td>');
                });
                doc.write('</tr>');
            });

            doc.write('</tbody></table></body></html>');
            doc.close();
            win.focus();
            win.print();
        });
    });
</script>
@endsection