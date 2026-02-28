@extends('layouts.app')
@section('title', 'Sell Return Ecom')

@section('css')
@include('report.partials.amazon_report_styles')
<style>
    /* Override content-header to prevent double banner */
    .report-amazon-page .content-header.no-print {
        background: transparent !important;
        border: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
        margin: 0 0 20px !important;
        box-shadow: none !important;
    }
    
    .report-amazon-page .content-header.no-print::before {
        display: none !important;
    }
    
    .report-amazon-page .sre-header-banner {
        background: linear-gradient(180deg, #37475a 0%, #232f3e 100%);
        border-radius: 10px;
        padding: 22px 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .report-amazon-page .sre-header-banner::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ff9900, #e47911);
        z-index: 1;
    }
    .sre-header-content { display: flex; flex-direction: column; gap: 4px; position: relative; z-index: 2; }
    .sre-header-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; margin: 0; color: #ffffff; }
    .sre-header-title i { font-size: 22px; color: #ff9900 !important; }
    .sre-header-subtitle { font-size: 13px; color: rgba(249, 250, 251, 0.88); margin: 0; }
    
    /* Hide widget title to prevent duplicate */
    .report-amazon-page .box-primary .box-header .box-title {
        display: none !important;
    }
</style>
@endsection

@section('content')
<div class="report-amazon-page">
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <div class="sre-header-banner">
        <div class="sre-header-content">
            <h1 class="sre-header-title">
                <i class="fas fa-undo"></i>
                Sell Return Ecom
            </h1>
            <p class="sre-header-subtitle">
                Manage ecommerce returns through Pending, Approved, In Transit, Verified, and Completed stages.
            </p>
        </div>
    </div>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('sell_list_filter_location_id', __('purchase.business_location') .
                                    ':') !!}

                                    {!! Form::select('sell_list_filter_location_id', $business_locations, null, ['class'
                                    => 'form-control select2', 'style' => 'width:100%', 'placeholder' =>
                                    __('lang_v1.all') ]); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('sell_list_filter_customer_id', __('contact.customer') . ':') !!}
                                    {!! Form::select('sell_list_filter_customer_id', $customers, null, ['class' =>
                                    'form-control select2', 'style' => 'width:100%', 'placeholder' =>
                                    __('lang_v1.all')]); !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
                                    {!! Form::text('sell_list_filter_date_range', null, ['placeholder' =>
                                    __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                                </div>
                            </div>
                            @can('access_sell_return')
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('created_by', __('report.user') . ':') !!}
                                    {!! Form::select('created_by', $sales_representative, null, ['class' =>
                                    'form-control select2', 'style' => 'width:100%']); !!}
                                </div>
                            </div>
                            @endcan
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
    @component('components.widget', ['class' => 'box-primary'])
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#pending_return" data-toggle="tab" id="tab-pending-return">
                    <i class="fa fa-spinner text-danger"></i> <strong> Pending </strong>
                </a>
            </li>
            <li>
                <a href="#approved_return" data-toggle="tab" id="tab-approved-return">
                    <i class="fa fa-check-circle text-success"></i> <strong>Approved</strong>
                </a>
            </li>
            <li>
                <a href="#in_transit_return" data-toggle="tab" id="tab-in-transit-return">
                    <i class="fa fa-truck text-primary"></i> <strong>In Transit</strong>
                </a>
            </li>
            <li>
                <a href="#varified_return" data-toggle="tab" id="tab-varified-return">
                    <i class="fa fa-check-circle text-warning"></i> <strong>Verified</strong>
                </a>
            </li>
            <li>
                <a href="#completed_return" data-toggle="tab" id="tab-completed-return">
                    <i class="fa fa-check-circle text-info"></i> <strong>Completed</strong>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="pending_return">
                <div class="table-responsive">
                        <table class="table nowrap table-bordered table-striped ajax_view" id="sell_return_ecom_table_pending" style="min-width: max-content;">
                            <thead>
                                <tr>
                                    <th>@lang('messages.date')</th>
                                    <th>@lang('sale.invoice_no')</th>
                                    <th>@lang('lang_v1.parent_sale')</th>
                                    <th>@lang('sale.customer_name')</th>
                                    <th>@lang('sale.location')</th>
                                    <th>@lang('sale.total_amount')</th>
                                    <th>@lang('purchase.payment_due')</th>
                                    <th>@lang('messages.action')</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-17 text-center footer-total">
                                    <td colspan="2"></td>
                                    <td><strong>@lang('sale.total'):</strong></td>
                                    <td colspan="2"></td>
                                    <td><span class="footer_pending_return_total text-right"></span></td>
                                    <td><span class="footer_pending_total_due_sr text-right"></span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                </div>
            </div>
            <div class="tab-pane" id="approved_return">
                <div class="table-responsive">
                    <table class="table nowrap table-bordered table-striped ajax_view" id="sell_return_ecom_table_approved" style="min-width: max-content;">
                        <thead>
                            <tr>
                                <th>@lang('messages.date')</th>
                                <th>@lang('sale.invoice_no')</th>
                                <th>@lang('lang_v1.parent_sale')</th>
                                <th>@lang('sale.customer_name')</th>
                                <th>@lang('sale.location')</th>
                                {{-- <th>@lang('sale.status')</th> --}}
                                <th>@lang('sale.total_amount')</th>
                                <th>@lang('purchase.payment_due')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-gray font-17 text-center footer-total">
                                <td colspan="2"></td>
                                <td><strong>@lang('sale.total'):</strong></td>
                                <td colspan="2"></td>
                                {{-- <td class="footer_payment_status_count_sr text-right"></td> --}}
                                <td><span class="footer_approved_return_total text-right"></span></td>
                                <td><span class="footer_approved_total_due_sr text-right"></span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="in_transit_return">
                <div class="table-responsive">
                <table class="table nowrap table-bordered table-striped ajax_view" id="sell_return_ecom_table_in_transit" style="min-width: max-content;">
                    <thead>
                        <tr>
                            <th>@lang('messages.date')</th>
                            <th>@lang('sale.invoice_no')</th>
                            <th>@lang('lang_v1.parent_sale')</th>
                            <th>@lang('sale.customer_name')</th>
                            <th>@lang('sale.location')</th>
                            {{-- <th>@lang('sale.status')</th> --}}
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('purchase.payment_due')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="2"></td>
                            <td><strong>@lang('sale.total'):</strong></td>
                            <td colspan="2"></td>
                            {{-- <td class="footer_payment_status_count_sr text-right"></td> --}}
                            <td><span class="footer_in_transit_return_total text-right"></span></td>
                            <td><span class="footer_in_transit_total_due_sr text-right"></span></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            </div>
            <div class="tab-pane" id="varified_return">
            <div class="table-responsive">
                <table class="table nowrap table-bordered table-striped ajax_view" id="sell_return_ecom_table_varified" style="min-width: max-content;">
                    <thead>
                        <tr>
                            <th>@lang('messages.date')</th>
                            <th>@lang('sale.invoice_no')</th>
                            <th>@lang('lang_v1.parent_sale')</th>
                            <th>@lang('sale.customer_name')</th>
                            <th>@lang('sale.location')</th>
                            <th>@lang('sale.status')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('purchase.payment_due')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="2"></td>
                            <td><strong>@lang('sale.total'):</strong></td>
                            <td colspan="2"></td>
                            <td class="footer_payment_status_count_sr text-right"></td>
                            <td><span class="footer_sell_return_total text-right"></span></td>
                            <td><span class="footer_total_due_sr text-right"></span></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            </div>
            <div class="tab-pane" id="completed_return">
            <div class="table-responsive">
                <table class="table nowrap table-bordered table-striped ajax_view" id="sell_return_ecom_table_completed" style="min-width: max-content;">
                    <thead>
                        <tr>
                            <th>@lang('messages.date')</th>
                            <th>@lang('sale.invoice_no')</th>
                            <th>@lang('lang_v1.parent_sale')</th>
                            <th>@lang('sale.customer_name')</th>
                            <th>@lang('sale.location')</th>
                            <th>@lang('sale.status')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('purchase.payment_due')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="2"></td>
                            <td><strong>@lang('sale.total'):</strong></td>
                            <td colspan="2"></td>
                            <td class="footer_payment_status_count_sr text-right"></td>
                            <td><span class="footer_sell_return_total text-right"></span></td>
                            <td><span class="footer_total_due_sr text-right"></span></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            </div>
    </div>
    </div>
    
    @endcomponent
    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div id="modal_pickup_modal" class="modal fade" tabindex="-1" role="dialog"></div>
</section>

</div>
<!-- /.content -->
@stop
@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>
    $(document).ready(function(){
        $(document).on('click', '.create_picking_btn', function(e){
            e.preventDefault();
            console.log('create_picking_btn');
            let url = $(this).data('href');
            $.ajax({
                url: url,
                method: 'GET',
                success: function (result) {
                    $('#modal_pickup_modal').html(result);
                    $('#modal_pickup_modal').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', {
                        xhr,
                        status,
                        error
                    });
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Failed to load modal. Please try again.');
                }
            });

        });

        $('#sell_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                sell_return_ecom_table.ajax.reload();
            }
        );
        $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#sell_list_filter_date_range').val('');
            sell_return_ecom_table.ajax.reload();
        });


        sell_return_ecom_table_pending = $('#sell_return_ecom_table_pending').DataTable({
            processing: true,
            language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            fixedHeader:true,
            scrollY:'60vh',
            scrollX: true,

            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sell-return-ecom-pending",
                "data": function ( d ) {
                    if($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }

                    if($('#sell_list_filter_location_id').length) {
                        d.location_id = $('#sell_list_filter_location_id').val();
                    }
                    d.customer_id = $('#sell_list_filter_customer_id').val();

                    if($('#created_by').length) {
                        d.created_by = $('#created_by').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": [7],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'parent_sale', name: 'T1.invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'final_total', name: 'final_total'},
                { data: 'payment_due', name: 'payment_due'},
                { data: 'action', name: 'action'}
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
                var total_sell = sum_table_col($('#sell_return_ecom_table_pending'), 'final_total');
                $('.footer_pending_return_total').text(__currency_trans_from_en(total_sell));
                
                var total_due = sum_table_col($('#sell_return_ecom_table_pending'), 'payment_due');
                $('.footer_pending_total_due_sr').text(__currency_trans_from_en(total_due));

                __currency_convert_recursively($('#sell_return_ecom_table_pending'));
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(2)').attr('class', 'clickable_td');
            }
        });
        
        sell_return_ecom_table_approved = $('#sell_return_ecom_table_approved').DataTable({
            processing: true,
            language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            fixedHeader:true,
            scrollY:'60vh',
            scrollX: true,

            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sell-return-ecom-approved",
                "data": function ( d ) {
                    if($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }

                    if($('#sell_list_filter_location_id').length) {
                        d.location_id = $('#sell_list_filter_location_id').val();
                    }
                    d.customer_id = $('#sell_list_filter_customer_id').val();

                    if($('#created_by').length) {
                        d.created_by = $('#created_by').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": [7],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'parent_sale', name: 'T1.invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                // { data: 'status', name: 'status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'payment_due', name: 'payment_due'},
                { data: 'action', name: 'action'}
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
                var total_sell = sum_table_col($('#sell_return_ecom_table_approved'), 'final_total');
                $('.footer_approved_return_total').text(__currency_trans_from_en(total_sell));
        
                var total_due = sum_table_col($('#sell_return_ecom_table_approved'), 'payment_due');
                $('.footer_approved_total_due_sr').text(__currency_trans_from_en(total_due));

                __currency_convert_recursively($('#sell_return_ecom_table_approved'));
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(2)').attr('class', 'clickable_td');
            }
        });
        
        sell_return_ecom_table_in_transit = $('#sell_return_ecom_table_in_transit').DataTable({
            processing: true,
            language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            fixedHeader:true,
            scrollY:'60vh',
            scrollX: true,

            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sell-return-ecom-in-transit",
                "data": function ( d ) {
                    if($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }

                    if($('#sell_list_filter_location_id').length) {
                        d.location_id = $('#sell_list_filter_location_id').val();
                    }
                    d.customer_id = $('#sell_list_filter_customer_id').val();

                    if($('#created_by').length) {
                        d.created_by = $('#created_by').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": [7],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'parent_sale', name: 'T1.invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                // { data: 'status', name: 'status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'payment_due', name: 'payment_due'},
                { data: 'action', name: 'action'}
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
                var total_sell = sum_table_col($('#sell_return_ecom_table_in_transit'), 'final_total');
                $('.footer_in_transit_return_total').text(__currency_trans_from_en(total_sell));
                
                var total_due = sum_table_col($('#sell_return_ecom_table_in_transit'), 'payment_due');
                $('.footer_in_transit_total_due_sr').text(__currency_trans_from_en(total_due));

                __currency_convert_recursively($('#sell_return_ecom_table_in_transit'));
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(2)').attr('class', 'clickable_td');
            }
        });
             
        sell_return_ecom_table_varified = $('#sell_return_ecom_table_varified').DataTable({
            processing: true,
            language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            fixedHeader:true,
            scrollY:'60vh',
            scrollX: true,

            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sell-return-ecom-varified",
                "data": function ( d ) {
                    if($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }

                    if($('#sell_list_filter_location_id').length) {
                        d.location_id = $('#sell_list_filter_location_id').val();
                    }
                    d.customer_id = $('#sell_list_filter_customer_id').val();

                    if($('#created_by').length) {
                        d.created_by = $('#created_by').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": [7, 8],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'parent_sale', name: 'T1.invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'status', name: 'status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'payment_due', name: 'payment_due'},
                { data: 'action', name: 'action'}
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
                var total_sell = sum_table_col($('#sell_return_ecom_table'), 'final_total');
                $('.footer_sell_return_total').text(__currency_trans_from_en(total_sell));
                
                $('.footer_payment_status_count_sr').html(__sum_status_html($('#sell_return_ecom_table'), 'payment-status-label'));

                var total_due = sum_table_col($('#sell_return_ecom_table'), 'payment_due');
                $('.footer_total_due_sr').text(__currency_trans_from_en(total_due));

                __currency_convert_recursively($('#sell_return_ecom_table'));
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(2)').attr('class', 'clickable_td');
            }
        });
          
        sell_return_ecom_table_completed = $('#sell_return_ecom_table_completed').DataTable({
            processing: true,
            language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            fixedHeader:true,
            scrollY:'60vh',
            scrollX: true,

            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sell-return-ecom-completed",
                "data": function ( d ) {
                    if($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }

                    if($('#sell_list_filter_location_id').length) {
                        d.location_id = $('#sell_list_filter_location_id').val();
                    }
                    d.customer_id = $('#sell_list_filter_customer_id').val();

                    if($('#created_by').length) {
                        d.created_by = $('#created_by').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": [7, 8],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'parent_sale', name: 'T1.invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'status', name: 'status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'payment_due', name: 'payment_due'},
                { data: 'action', name: 'action'}
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
                var total_sell = sum_table_col($('#sell_return_ecom_table'), 'final_total');
                $('.footer_sell_return_total').text(__currency_trans_from_en(total_sell));
                
                $('.footer_payment_status_count_sr').html(__sum_status_html($('#sell_return_ecom_table'), 'payment-status-label'));

                var total_due = sum_table_col($('#sell_return_ecom_table'), 'payment_due');
                $('.footer_total_due_sr').text(__currency_trans_from_en(total_due));

                __currency_convert_recursively($('#sell_return_ecom_table'));
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(2)').attr('class', 'clickable_td');
            }
        });
       
        // Store active tab in URL parameter when tab changes
        $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            var activeTab = $(e.target).attr('href');
            var tabId = $(e.target).attr('id');
            
            // Update URL parameter without page reload
            var url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.replaceState({}, '', url);
            
            $(e.target).parent().find('a[data-toggle="tab"]').removeClass('active');
            $(e.target).addClass('active');
            
            if(tabId == 'tab-pending-return') {
                sell_return_ecom_table_pending.ajax.reload();
            } else if(tabId == 'tab-approved-return') {
                sell_return_ecom_table_approved.ajax.reload();
            } else if(tabId == 'tab-in-transit-return') {
                sell_return_ecom_table_in_transit.ajax.reload();
            } else if(tabId == 'tab-varified-return') {
                sell_return_ecom_table_varified.ajax.reload();
            } else if(tabId == 'tab-completed-return') {
                sell_return_ecom_table_completed.ajax.reload();
            }
        });
        
        // Restore active tab from URL parameter on page load
        var urlParams = new URLSearchParams(window.location.search);
        var savedTabId = urlParams.get('tab');
        
        if (savedTabId) {
            // Find the tab element by ID
            var tabElement = $('#' + savedTabId);
            if (tabElement.length > 0) {
                // Remove active class from all tabs and panes
                $('.nav-tabs li').removeClass('active');
                $('.tab-pane').removeClass('active');
                
                // Add active class to saved tab and pane
                var tabHref = tabElement.attr('href');
                tabElement.parent().addClass('active');
                $(tabHref).addClass('active');
                
                // Trigger the tab change event to load the correct table
                if(savedTabId == 'tab-pending-return') {
                    sell_return_ecom_table_pending.ajax.reload();
                } else if(savedTabId == 'tab-approved-return') {
                    sell_return_ecom_table_approved.ajax.reload();
                } else if(savedTabId == 'tab-in-transit-return') {
                    sell_return_ecom_table_in_transit.ajax.reload();
                } else if(savedTabId == 'tab-varified-return') {
                    sell_return_ecom_table_varified.ajax.reload();
                } else if(savedTabId == 'tab-completed-return') {
                    sell_return_ecom_table_completed.ajax.reload();
                }
            }
        }
        

        $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #created_by',  function() {
            sell_return_ecom_table_pending.ajax.reload();
            sell_return_ecom_table_approved.ajax.reload();
            sell_return_ecom_table_in_transit.ajax.reload();
            sell_return_ecom_table_varified.ajax.reload();
            sell_return_ecom_table_completed.ajax.reload();
        });

        $(document).on('click', '.complete_return_btn', function () {
            $(this).prop('disabled', true);
            
            $(this).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            
            let id = $(this).data('id');
            $.ajax({
                url: '/sell-return-ecom-create-sell-return/' + id,
                method: 'POST',
                timeout: 30000, // 30 second timeout
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (result) {
                    if (result && result.status) {
                        toastr.success(result.message || 'Return completed successfully!');
                        $('#verify-return-btn')
                            .removeClass('tw-dw-btn-success')
                            .addClass('tw-dw-btn-secondary')
                            .prop('disabled', true)
                            .html('<i class="fa fa-check-circle"></i> Completed');
                            sell_return_ecom_table_varified.ajax.reload();  
                    } else {
                        toastr.error(result.message || 'Failed to complete return');
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error(error);
                    console.error('AJAX Error:', {status, error, xhr});
                },
                complete: function () {
                    $(this).prop('disabled', false);
                    $(this).html('Complete Return');
                }
            });
        });
    })
</script>

@endsection