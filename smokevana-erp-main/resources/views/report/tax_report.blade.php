@extends('layouts.app')
@section('title', __( 'report.tax_report' ))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="report-amazon-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang( 'report.tax_report' )
        <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">@lang( 'report.tax_report_msg'
            )</small>
    </h1>
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
                                    {!! Form::label('tax_report_location_id', __('purchase.business_location') . ':')
                                    !!}
                                    {!! Form::select('tax_report_location_id', $business_locations, null, ['class' =>
                                    'form-control select2', 'style' => 'width:100%']); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('tax_report_contact_id', __( 'report.contact' ) . ':') !!}
                                    {!! Form::select('tax_report_contact_id', $contact_dropdown, null , ['class' =>
                                    'form-control select2', 'style' => 'width:100%', 'id' => 'tax_report_contact_id',
                                    'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('tax_report_date_range', __('report.date_range') . ':') !!}
                                    {!! Form::text('tax_report_date_range', null, ['placeholder' =>
                                    __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' =>
                                    'tax_report_date_range', 'readonly']); !!}
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

    {{--<div class="row">
        <div class="col-md-4 col-sm-12">
            @component('components.widget')
            @slot('title')
            {{ __('report.input_tax') }} @show_tooltip(__('tooltip.input_tax'))
            @endslot
            <div class="input_tax">
                <i class="fas fa-sync fa-spin fa-fw"></i>
            </div>
            @endcomponent
        </div>

        <div class="col-md-4 col-sm-12">
            @component('components.widget')
            @slot('title')
            {{ __('report.output_tax') }} @show_tooltip(__('tooltip.output_tax'))
            @endslot
            <div class="output_tax">
                <i class="fas fa-sync fa-spin fa-fw"></i>
            </div>
            @endcomponent
        </div>

        <div class="col-md-4 col-sm-12">
            @component('components.widget')
            @slot('title')
            {{ __('lang_v1.expense_tax') }} @show_tooltip(__('lang_v1.expense_tax_tooltip'))
            @endslot
            <div class="expense_tax">
                <i class="fa fa-refresh fa-spin fa-fw"></i>
            </div>
            @endcomponent
        </div>
    </div>--}}

    <!-- Tax Report Graph Section -->
    <div class="row no-print" id="tax_report_chart_container" style="margin-bottom: 20px;">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box box-primary', 'icon' => '<i class="fa fa-chart-line"></i> ', 'title' => __('Tax Trend')])
                <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                    <canvas id="tax_report_chart"></canvas>
                </div>
            @endcomponent
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12">
            @component('components.widget', ['class' => 'box box-primary', 'icon' => '<i class="fa fa-balance-scale"></i> '])
            @slot('title')
            {{ __('lang_v1.tax_overall') }} @show_tooltip(__('tooltip.tax_overall'))
            @endslot
            <h3 class="text-muted">
                {{ __('lang_v1.output_tax_minus_input_tax') }}:
                <span class="tax_diff">
                    <i class="fas fa-sync fa-spin fa-fw"></i>
                </span>
            </h3>
            @endcomponent
        </div>
    </div>
    <div class="row no-print">
        <div class="col-sm-12">
            <button class="tw-dw-btn tw-dw-btn-primary tw-text-white pull-right tw-mb-2" aria-label="Print"
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
    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#input_tax_tab" data-toggle="tab" aria-expanded="true"><i
                                class="fa fas fa-arrow-circle-down" aria-hidden="true"></i> @lang('report.input_tax') (
                            @lang('lang_v1.purchase') )</a>
                    </li>

                    <li>
                        <a href="#output_tax_tab" data-toggle="tab" aria-expanded="true"><i
                                class="fa fas fa-arrow-circle-up" aria-hidden="true"></i> @lang('report.output_tax') (
                            @lang('sale.sells') )</a>
                    </li>

                    <li>
                        <a href="#expense_tax_tab" data-toggle="tab" aria-expanded="true"><i
                                class="fa fas fa-minus-circle" aria-hidden="true"></i> @lang('lang_v1.expense_tax')</a>
                    </li>
                    @if(!empty($tax_report_tabs))
                    @foreach($tax_report_tabs as $key => $tabs)
                    @foreach ($tabs as $index => $value)
                    @if(!empty($value['tab_menu_path']))
                    @php
                    $tab_data = !empty($value['tab_data']) ? $value['tab_data'] : [];
                    @endphp
                    @include($value['tab_menu_path'], $tab_data)
                    @endif
                    @endforeach
                    @endforeach
                    @endif
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="input_tax_tab">
                        <table class="table nowrap table-bordered table-striped" id="input_tax_table">
                            <thead>
                                <tr>
                                    <th>@lang('messages.date')</th>
                                    <th>@lang('purchase.ref_no')</th>
                                    <th>@lang('purchase.supplier')</th>
                                    <th>@lang('contact.tax_no')</th>
                                    <th>@lang('sale.total_amount')</th>
                                    <th>@lang('lang_v1.payment_method')</th>
                                    <th>@lang('receipt.discount')</th>
                                    @foreach($taxes as $tax)
                                    <th>
                                        {{$tax['name']}}
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-17 text-center footer-total">
                                    <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                                    <td><span class="display_currency" id="sell_total"
                                            data-currency_symbol="true"></span></td>
                                    <td class="input_payment_method_count"></td>
                                    <td>&nbsp;</td>
                                    @foreach($taxes as $tax)
                                    <td>
                                        <span class="display_currency" id="total_input_{{$tax['id']}}"
                                            data-currency_symbol="true"></span>
                                    </td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="tab-pane" id="output_tax_tab">
                        <table class="table   table-bordered table-striped ajax_view hide-footer" id="output_tax_table" style="min-width: max-content;">
                            <thead>
                                <tr>
                                    <th>@lang('messages.date')</th>
                                    <th>@lang('sale.invoice_no')</th>
                                    <th>@lang('contact.customer')</th>
                                    <th>@lang('contact.tax_no')</th>
                                    <th>@lang('sale.total_amount')</th>
                                    <th>@lang('lang_v1.payment_method')</th>
                                    <th>@lang('receipt.discount')</th>
                                    @foreach($taxes as $tax)
                                    <th style="min-width: 100px">
                                        {{$tax['name']}}
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-17 text-center footer-total">
                                    <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                                    <td><span class="display_currency" id="purchase_total"
                                            data-currency_symbol="true"></span></td>
                                    <td class="output_payment_method_count"></td>
                                    <td>&nbsp;</td>
                                    @foreach($taxes as $tax)
                                    <td>
                                        <span class="display_currency" id="total_output_{{$tax['id']}}"
                                            data-currency_symbol="true"></span>
                                    </td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="tab-pane" id="expense_tax_tab">
                        <table class="table table-bordered table-striped" id="expense_tax_table" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('messages.date')</th>
                                    <th>@lang('purchase.ref_no')</th>
                                    <th>@lang('contact.tax_no')</th>
                                    <th>@lang('sale.total_amount')</th>
                                    <th>@lang('lang_v1.payment_method')</th>
                                    @foreach($taxes as $tax)
                                    <th>
                                        {{$tax['name']}}
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-17 text-center footer-total">
                                    <td colspan="3"><strong>@lang('sale.total'):</strong></td>
                                    <td>
                                        <span class="display_currency" id="expense_total"
                                            data-currency_symbol="true"></span>
                                    </td>
                                    <td class="expense_payment_method_count"></td>
                                    @foreach($taxes as $tax)
                                    <td>
                                        <span class="display_currency" id="total_expense_{{$tax['id']}}"
                                            data-currency_symbol="true"></span>
                                    </td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @if(!empty($tax_report_tabs))
                    @foreach($tax_report_tabs as $key => $tabs)
                    @foreach ($tabs as $index => $value)
                    @if(!empty($value['tab_content_path']))
                    @php
                    $tab_data = !empty($value['tab_data']) ? $value['tab_data'] : [];
                    @endphp
                    @include($value['tab_content_path'], $tab_data)
                    @endif
                    @endforeach
                    @endforeach
                    @endif
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
<script type="text/javascript">
    $(document).ready(function() {
        $('#tax_report_date_range').daterangepicker(
            dateRangeSettings, 
            function(start, end) {
                $('#tax_report_date_range').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
            }
        );

        input_tax_table = $('#input_tax_table').DataTable({
            scrollX:true,
            processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            fixedHeader:false,
            ajax: {
                url: '/reports/tax-details',
                data: function(d) {
                    d.type = 'purchase';
                    d.location_id = $('#tax_report_location_id').val();
                    d.contact_id = $('#tax_report_contact_id').val();
                    var start = $('input#tax_report_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end = $('input#tax_report_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'ref_no', name: 'ref_no' },
                { data: 'contact_name', name: 'c.name' },
                { data: 'tax_number', name: 'c.tax_number' },
                { data: 'total_before_tax', name: 'total_before_tax' },
                { data: 'payment_methods', orderable: false, "searchable": false},
                { data: 'discount_amount', name: 'discount_amount' },
                @foreach($taxes as $tax)
                { data: "tax_{{$tax['id']}}", searchable: false, orderable: false },
                @endforeach
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

            "footerCallback": function ( row, data, start, end, display ) {
                $('.input_payment_method_count').html(__count_status(data, 'payment_methods'));
            },
            fnDrawCallback: function(oSettings) {
                $('#sell_total').text(
                    sum_table_col($('#input_tax_table'), 'total_before_tax')
                );
                @foreach($taxes as $tax)
                    $("#total_input_{{$tax['id']}}").text(
                        sum_table_col($('#input_tax_table'), "tax_{{$tax['id']}}")
                    );
                @endforeach

                __currency_convert_recursively($('#input_tax_table'));
            },
        });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if ($(e.target).attr('href') == '#output_tax_tab') {
                if (typeof (output_tax_datatable) == 'undefined') {
                    output_tax_datatable = $('#output_tax_table').DataTable({
                        processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                        serverSide: true,
                        scrollX:true,
                        fixedHeader:false,
                        aaSorting: [[0, 'desc']],
                        ajax: {
                            url: '/reports/tax-details',
                            data: function(d) {
                                d.type = 'sell';
                                d.location_id = $('#tax_report_location_id').val();
                                d.contact_id = $('#tax_report_contact_id').val();
                                var start = $('input#tax_report_date_range')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                var end = $('input#tax_report_date_range')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                                d.start_date = start;
                                d.end_date = end;
                            }
                        },
                        columns: [
                            { data: 'transaction_date', name: 'transaction_date' },
                            { data: 'invoice_no', name: 'invoice_no' },
                            { data: 'contact_name', name: 'c.name' },
                            { data: 'tax_number', name: 'c.tax_number' },
                            { data: 'total_before_tax', name: 'total_before_tax' },
                            { data: 'payment_methods', orderable: false, "searchable": false},
                            { data: 'discount_amount', name: 'discount_amount' },
                            @foreach($taxes as $tax)
                            { data: "tax_{{$tax['id']}}", searchable: false, orderable: false },
                            @endforeach
                        ],
                        "footerCallback": function ( row, data, start, end, display ) {
                            $('.output_payment_method_count').html(__count_status(data, 'payment_methods'));
                        },
                        fnDrawCallback: function(oSettings) {
                            $('#purchase_total').text(
                                sum_table_col($('#output_tax_table'), 'total_before_tax')
                            );
                            @foreach($taxes as $tax)
                                $("#total_output_{{$tax['id']}}").text(
                                    sum_table_col($('#output_tax_table'), "tax_{{$tax['id']}}")
                                );
                            @endforeach
                            __currency_convert_recursively($('#output_tax_table'));
                        },
                    });
                }
            } else if ($(e.target).attr('href') == '#expense_tax_tab') {
                if (typeof (expense_tax_datatable) == 'undefined') {
                    expense_tax_datatable = $('#expense_tax_table').DataTable({
                        scrollX:true,
                        processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                        serverSide: true,
                        fixedHeader:false,
                        ajax: {
                            url: '/reports/tax-details',
                            data: function(d) {
                                d.type = 'expense';
                                d.location_id = $('#tax_report_location_id').val();
                                d.contact_id = $('#tax_report_contact_id').val();
                                var start = $('input#tax_report_date_range')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                var end = $('input#tax_report_date_range')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                                d.start_date = start;
                                d.end_date = end;
                            }
                        },
                        columns: [
                            { data: 'transaction_date', name: 'transaction_date' },
                            { data: 'ref_no', name: 'ref_no' },
                            { data: 'tax_number', name: 'c.tax_number' },
                            { data: 'total_before_tax', name: 'total_before_tax' },
                            { data: 'payment_methods', orderable: false, "searchable": false},
                            @foreach($taxes as $tax)
                            { data: "tax_{{$tax['id']}}", searchable: false, orderable: false },
                            @endforeach
                        ],
                        "footerCallback": function ( row, data, start, end, display ) {
                            $('.expense_payment_method_count').html(__count_status(data, 'payment_methods'));
                        },
                        fnDrawCallback: function(oSettings) {
                            $('#expense_total').text(
                                sum_table_col($('#expense_tax_table'), 'total_before_tax')
                            );
                            @foreach($taxes as $tax)
                                $("#total_expense_{{$tax['id']}}").text(
                                    sum_table_col($('#expense_tax_table'), "tax_{{$tax['id']}}")
                                );
                            @endforeach
                            __currency_convert_recursively($('#expense_tax_table'));
                        },
                    });
                }
            }

             // remove class from data table button
             $('.btn-default').removeClass('btn-default');
            $('.tw-dw-btn-outline').removeClass('btn');
        });
        
        $('#tax_report_date_range, #tax_report_location_id, #tax_report_contact_id').change( function(){
            if ($("#input_tax_tab").hasClass('active')) {
                input_tax_table.ajax.reload();
            }
            if ($("#output_tax_tab").hasClass('active')) {
                output_tax_datatable.ajax.reload();
            }
            if ($("#expense_tax_tab").hasClass('active')) {
                expense_tax_datatable.ajax.reload();
            }
            
        });
    });
    
    // Tax Report Chart
    var taxReportChart = null;
    
    function loadTaxReportChart() {
        var start_date = $('#tax_report_date_range').data('daterangepicker') 
            ? $('#tax_report_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD')
            : null;
        var end_date = $('#tax_report_date_range').data('daterangepicker')
            ? $('#tax_report_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD')
            : null;
        var location_id = $('#tax_report_location_id').val();
        var contact_id = $('#tax_report_contact_id').val();
        
        $.ajax({
            url: '/reports/tax-report-chart-data',
            method: 'GET',
            data: {
                start_date: start_date,
                end_date: end_date,
                location_id: location_id,
                contact_id: contact_id
            },
            success: function(response) {
                if (response.success && response.labels) {
                    var ctx = document.getElementById('tax_report_chart').getContext('2d');
                    
                    if (taxReportChart) {
                        taxReportChart.destroy();
                    }
                    
                    taxReportChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: response.labels,
                            datasets: [
                                {
                                    label: '@lang("report.input_tax")',
                                    data: response.input_tax,
                                    borderColor: 'rgb(255, 99, 132)',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: '@lang("report.output_tax")',
                                    data: response.output_tax,
                                    borderColor: 'rgb(75, 192, 192)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: '@lang("lang_v1.expense_tax")',
                                    data: response.expense_tax,
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
                                    text: '@lang("Tax Trend")',
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
                                    }
                                }
                            }
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading tax chart data:', error);
            }
        });
    }
    
    $(document).ready(function() {
        setTimeout(function() {
            loadTaxReportChart();
        }, 500);
        
        $('#tax_report_date_range, #tax_report_location_id, #tax_report_contact_id').on('change', function() {
            loadTaxReportChart();
        });
        
        $('#tax_report_date_range').on('apply.daterangepicker', function() {
            loadTaxReportChart();
        });
    });
</script>
@if(!empty($tax_report_tabs))
@foreach($tax_report_tabs as $key => $tabs)
@foreach ($tabs as $index => $value)
@if(!empty($value['module_js_path']))
@include($value['module_js_path'])
@endif
@endforeach
@endforeach
@endif
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
@endsection