@extends('layouts.app')
@section('title', __('lang_v1.purchase_order'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .purchase-order-list-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
    .po-header-banner {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 0 0 10px 10px;
        padding: 22px 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .po-header-banner.amazon-theme-banner::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #ff9900; z-index: 1; }
    .po-header-banner .banner-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; margin: 0; color: #fff !important; }
    .po-header-banner .banner-title i { color: #fff !important; }
    .po-header-banner .banner-subtitle { font-size: 13px; color: rgba(255,255,255,0.9) !important; margin: 4px 0 0 0; }
    .purchase-order-list-page #dynamic_button,
    .purchase-order-list-page .amazon-orange-add,
    .purchase-order-list-page .box-tools .tw-dw-btn { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 2px solid #C7511F !important; color: #fff !important; }
    .purchase-order-list-page #dynamic_button:hover,
    .purchase-order-list-page .amazon-orange-add:hover,
    .purchase-order-list-page .box-tools .tw-dw-btn:hover { color: #fff !important; opacity: 0.95; border-color: #E47911 !important; }
    .purchase-order-list-page .box-primary { border-radius: 10px; overflow: hidden; border: 1px solid #D5D9D9; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .purchase-order-list-page .box-primary .box-header { background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important; color: #fff !important; border: none !important; padding: 14px 20px !important; position: relative; }
    .purchase-order-list-page .box-primary .box-header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900; }
    .purchase-order-list-page .box-primary .box-title { color: #fff !important; font-weight: 600; }
    .purchase-order-list-page .box-primary .tw-flow-root,
    .purchase-order-list-page .box-primary .table-responsive { background: #f7f8f8 !important; padding: 1rem 1.25rem !important; }
    .purchase-order-list-page .dt-buttons .btn,
    .purchase-order-list-page .dt-buttons button,
    .purchase-order-list-page .dt-buttons .dt-button { background: #232f3e !important; border: 1px solid #37475a !important; color: #fff !important; }
    .purchase-order-list-page .dt-buttons .btn:hover,
    .purchase-order-list-page .dt-buttons button:hover,
    .purchase-order-list-page .dt-buttons .dt-button:hover { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border-color: #C7511F !important; color: #fff !important; }
    .purchase-order-list-page .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #ff9900 !important; border-color: #ff9900 !important; color: #fff !important; }
    .purchase-order-list-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover { border-color: #ff9900; color: #232f3e; }
    
    /* Dropdown items use the global amazon-theme.css base styles for consistency */
</style>
@endsection

@section('content')

<!-- Amazon-style banner -->
<section class="content-header no-print">
    <div class="po-header-banner amazon-theme-banner">
        <h1 class="banner-title"><i class="fas fa-file-invoice"></i> @lang('lang_v1.purchase_order')</h1>
        <p class="banner-subtitle">@lang('lang_v1.all_purchase_orders')</p>
    </div>
</section>

<!-- Main content -->
<section class="content no-print purchase-order-list-page">
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
                                    {!! Form::label('po_list_filter_location_id', __('purchase.business_location') .
                                    ':') !!}
                                    {!! Form::select('po_list_filter_location_id', $business_locations, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'placeholder' => __('lang_v1.all'),
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('po_list_filter_supplier_id', __('purchase.supplier') . ':') !!}
                                    {!! Form::select('po_list_filter_supplier_id', $suppliers, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'placeholder' => __('lang_v1.all'),
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('po_list_filter_status', __('sale.status') . ':') !!}
                                    {!! Form::select('po_list_filter_status', $purchaseOrderStatuses, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'placeholder' => __('lang_v1.all'),
                                    ]) !!}
                                </div>
                            </div>
                            @if (!empty($shipping_statuses))
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('shipping_status', __('lang_v1.shipping_status') . ':') !!}
                                    {!! Form::select('shipping_status', $shipping_statuses, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'placeholder' => __('lang_v1.all'),
                                    ]) !!}
                                </div>
                            </div>
                            @endif
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('po_list_filter_date_range', __('report.date_range') . ':') !!}
                                    {!! Form::text('po_list_filter_date_range', null, [
                                    'placeholder' => __('lang_v1.select_a_date_range'),
                                    'class' => 'form-control',
                                    'readonly',
                                    ]) !!}
                                </div>
                            </div>
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
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_purchase_orders')])
    @can('purchase_order.create')
    @slot('tool')
    <div class="box-tools">
        <a id="dynamic_button" class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white tw-border-none pull-right amazon-orange-add"
            href="{{ action([\App\Http\Controllers\PurchaseOrderController::class, 'create']) }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 5l0 14" />
                <path d="M5 12l14 0" />
            </svg> @lang('messages.add')
        </a>
    </div>
    @endslot
    @endcan

    <style>
    /* Ensure Added By column is clearly visible with proper padding */
    #purchase_order_table th:last-child,
    #purchase_order_table td:last-child {
        min-width: 100px;
        padding-right: 16px !important;
    }
    .dataTables_scrollBody #purchase_order_table td:last-child {
        padding-right: 16px !important;
    }
    </style>
    <div class="table-responsive" style="overflow-x: auto; max-width: 100%; padding-right: 12px;">
    <table class="table nowrap table-bordered table-striped ajax_view" id="purchase_order_table" style="min-width: max-content;">
        <thead>
            <tr>
                <th>@lang('messages.action')</th>
                <th>@lang('messages.date')</th>
                <th>@lang('purchase.ref_no')</th>
                <th>@lang('purchase.location')</th>
                <th>@lang('purchase.supplier')</th>
                <th>@lang('sale.status')</th>
                <th>@lang('lang_v1.quantity_remaining')</th>
                <th>@lang('lang_v1.shipping_status')</th>
                <th>@lang('lang_v1.added_by')</th>
            </tr>
        </thead>
    </table>
    </div>
    @endcomponent
    <div class="modal fade edit_pso_status_modal" tabindex="-1" role="dialog"></div>
</section>
<!-- /.content -->
@stop
@section('javascript')
@includeIf('purchase_order.common_js')
<script type="text/javascript">
    $(document).ready(function() {
            //Purchase table
            purchase_order_table = $('#purchase_order_table').DataTable({
                processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                serverSide: true,
                aaSorting: [
                    [1, 'desc']
                ],
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: false,
                fixedHeader:false,
                ajax: {
                    url: '{{ action([\App\Http\Controllers\PurchaseOrderController::class, 'index']) }}',
                    data: function(d) {
                        if ($('#po_list_filter_location_id').length) {
                            d.location_id = $('#po_list_filter_location_id').val();
                        }
                        if ($('#po_list_filter_supplier_id').length) {
                            d.supplier_id = $('#po_list_filter_supplier_id').val();
                        }
                        if ($('#po_list_filter_status').length) {
                            d.status = $('#po_list_filter_status').val();
                        }
                        if ($('#shipping_status').length) {
                            d.shipping_status = $('#shipping_status').val();
                        }

                        var start = '';
                        var end = '';
                        if ($('#po_list_filter_date_range').val()) {
                            start = $('input#po_list_filter_date_range')
                                .data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            end = $('input#po_list_filter_date_range')
                                .data('daterangepicker')
                                .endDate.format('YYYY-MM-DD');
                        }
                        d.start_date = start;
                        d.end_date = end;

                        d = __datatable_ajax_callback(d);
                    },
                },
                columnDefs: [
                    { targets: -1, className: 'added-by-column', width: '120px' }
                ],
                columns: [{
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'ref_no',
                        name: 'ref_no'
                    },
                    {
                        data: 'location_name',
                        name: 'BS.name'
                    },
                    {
                        data: 'name',
                        name: 'contacts.name'
                    },
                    {
                        data: 'status',
                        name: 'transactions.status'
                    },
                    {
                        data: 'po_qty_remaining',
                        name: 'po_qty_remaining',
                        "searchable": false
                    },
                    {
                        data: 'shipping_status',
                        name: 'transactions.shipping_status'
                    },
                    {
                        data: 'added_by',
                        name: 'u.first_name'
                    }
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

            });

            $(document).on(
                'change',
                '#po_list_filter_location_id, #po_list_filter_supplier_id, #po_list_filter_status, #shipping_status',
                function() {
                    purchase_order_table.ajax.reload();
                }
            );

            $('#po_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#po_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                        moment_date_format));
                    purchase_order_table.ajax.reload();
                }
            );
            $('#po_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#po_list_filter_date_range').val('');
                purchase_order_table.ajax.reload();
            });

            $(document).on('click', 'a.delete-purchase-order', function(e) {
                e.preventDefault();
                swal({
                    title: LANG.sure,
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }).then(willDelete => {
                    if (willDelete) {
                        var href = $(this).attr('href');
                        $.ajax({
                            method: 'DELETE',
                            url: href,
                            dataType: 'json',
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    purchase_order_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            },
                        });
                    }
                });
            });
        });
</script>
@endsection