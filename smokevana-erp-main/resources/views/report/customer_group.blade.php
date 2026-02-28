@extends('layouts.app')
@section('title', __('lang_v1.customer_groups_report'))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="report-amazon-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('lang_v1.customer_groups_report')}}</h1>
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
                            'getCustomerGroup']), 'method' => 'get', 'id' => 'cg_report_filter_form' ]) !!}
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('cg_customer_group_id', __( 'lang_v1.customer_group_name' ) . ':')
                                    !!}
                                    {!! Form::select('cg_customer_group_id', $customer_group, null, ['class' =>
                                    'form-control select2', 'style' => 'width:100%', 'id' => 'cg_customer_group_id']);
                                    !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('cg_location_id', __('purchase.business_location') . ':') !!}
                                    {!! Form::select('cg_location_id', $business_locations, null, ['class' =>
                                    'form-control select2', 'style' => 'width:100%']); !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('cg_date_range', __('report.date_range') . ':') !!}
                                    {!! Form::text('date_range', null, ['placeholder' =>
                                    __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' =>
                                    'cg_date_range', 'readonly']); !!}
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

    <!-- Customer Groups Graph Section -->
    <div class="row no-print" id="customer_group_chart_container" style="margin-bottom: 20px;">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('Sales by Customer Group')])
                <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                    <canvas id="customer_group_chart"></canvas>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="cg_report_table">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.customer_group')</th>
                            <th>@lang('report.total_sell')</th>
                        </tr>
                    </thead>
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

<script type="text/javascript">
    var customerGroupChart = null;
    
    function loadCustomerGroupChart() {
        var start_date = $('#cg_date_range').data('daterangepicker') 
            ? $('#cg_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD')
            : null;
        var end_date = $('#cg_date_range').data('daterangepicker')
            ? $('#cg_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD')
            : null;
        var location_id = $('#cg_location_id').val();
        var customer_group_id = $('#cg_customer_group_id').val();
        
        $.ajax({
            url: '/reports/customer-group-chart-data',
            method: 'GET',
            data: {
                start_date: start_date,
                end_date: end_date,
                location_id: location_id,
                customer_group_id: customer_group_id
            },
            success: function(response) {
                if (response.success && response.labels && response.data) {
                    var ctx = document.getElementById('customer_group_chart').getContext('2d');
                    
                    if (customerGroupChart) {
                        customerGroupChart.destroy();
                    }
                    
                    customerGroupChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: response.labels,
                            datasets: [{
                                label: '@lang("report.total_sell")',
                                data: response.data,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgb(75, 192, 192)',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: '@lang("Sales by Customer Group")',
                                    font: { size: 16, weight: 'bold' }
                                },
                                legend: { display: true, position: 'top' },
                                tooltip: {
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
                                    }
                                }
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
    
    $(document).ready(function(){
            if($('#cg_date_range').length == 1){
                $('#cg_date_range').daterangepicker(
                    dateRangeSettings,
                    function (start, end) {
                        $('#cg_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                        cg_report_table.ajax.reload();
                        loadCustomerGroupChart();
                    }
                );

                $('#cg_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    cg_report_table.ajax.reload();
                    loadCustomerGroupChart();
                });
            }

            cg_report_table = $('#cg_report_table').DataTable({
                            processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                            serverSide: true,
                            fixedHeader:false,
                            "ajax": {
                                "url": "/reports/customer-group",
                                "data": function ( d ) {
                                    d.location_id = $('#cg_location_id').val();
                                    d.customer_group_id = $('#cg_customer_group_id').val();
                                    d.start_date = $('#cg_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                    d.end_date = $('#cg_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                }
                            },
                            columns: [
                                {data: 'name', name: 'CG.name'},
                                {data: 'total_sell', name: 'total_sell', searchable: false}
                            ],
                            buttons: [
                    {
                        text: '<i class="fa fa-filter"></i> Filters',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        action: function () {
                            $('#filterModal').modal('show');
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> ' + LANG.export_to_csv,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG.export_to_excel,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                            stripHtml: true,
                        },
                        footer: true,
                        customize: function (win) {
                            if ($('.print_table_part').length > 0) {
                                $($('.print_table_part').html()).insertBefore(
                                    $(win.document.body).find('table')
                                );
                            }
                            if ($(win.document.body).find('table.hide-footer').length) {
                                $(win.document.body).find('table.hide-footer tfoot').remove();
                            }
                            __currency_convert_recursively($(win.document.body).find('table'));
                        },
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                    },
                ],

                            "fnDrawCallback": function (oSettings) {
                                __currency_convert_recursively($('#cg_report_table'));
                            }
                        });
            //Customer Group report filter
            $('select#cg_location_id, select#cg_customer_group_id, #cg_date_range').change( function(){
                cg_report_table.ajax.reload();
                loadCustomerGroupChart();
            });
            
            // Load chart on page load
            setTimeout(function() {
                loadCustomerGroupChart();
            }, 500);
        })
</script>
@endsection