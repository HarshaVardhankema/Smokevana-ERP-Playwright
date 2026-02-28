@extends('layouts.app')
@section('title', __('lang_v1.all_sales'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .sell-list-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
    .sell-header-banner {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 0 0 10px 10px;
        padding: 22px 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .sell-header-banner.amazon-theme-banner::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #ff9900; z-index: 1; }
    .sell-header-content { display: flex; flex-direction: column; gap: 4px; }
    .sell-header-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; margin: 0; color: #fff !important; }
    .sell-header-title i { font-size: 22px; color: #fff !important; }
    .sell-header-subtitle { font-size: 13px; color: rgba(255,255,255,0.9) !important; margin: 0; }
    .sell-list-page #dynamic_button,
    .sell-list-page .box-tools .tw-dw-btn,
    .sell-list-page .box-tools a[href*="create"] { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 2px solid #C7511F !important; color: #fff !important; }
    .sell-list-page #dynamic_button:hover,
    .sell-list-page .box-tools .tw-dw-btn:hover,
    .sell-list-page .box-tools a[href*="create"]:hover { color: #fff !important; opacity: 0.95; border-color: #E47911 !important; }
    .sell-list-page .box-primary { border-radius: 10px; overflow: hidden; border: 1px solid #D5D9D9; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .sell-list-page .box-primary .box-header { background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important; color: #fff !important; border: none !important; padding: 14px 20px !important; position: relative; }
    .sell-list-page .box-primary .box-header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900; }
    .sell-list-page .box-primary .box-title { color: #fff !important; font-weight: 600; }
    .sell-list-page .box-primary .tw-flow-root,
    .sell-list-page .box-primary .table-responsive { background: #f7f8f8 !important; padding: 1rem 1.25rem !important; }
    /* Allow the table to scroll horizontally within the card */
    .sell-list-page .box-primary > .tw-p-2,
    .sell-list-page .box-primary .tw-flow-root,
    .sell-list-page .box-primary .tw-flow-root > div,
    .sell-list-page .box-primary .tw-flow-root > div > div {
        overflow-x: auto !important;
        max-width: 100% !important;
    }
    .sell-list-page .dt-buttons .btn,
    .sell-list-page .dt-buttons button,
    .sell-list-page .dt-buttons .dt-button { background: #232f3e !important; border: 1px solid #37475a !important; color: #fff !important; }
    .sell-list-page .dt-buttons .btn:hover,
    .sell-list-page .dt-buttons button:hover,
    .sell-list-page .dt-buttons .dt-button:hover { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border-color: #C7511F !important; color: #fff !important; }
    .sell-list-page .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #ff9900 !important; border-color: #ff9900 !important; color: #fff !important; }
    .sell-list-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover { border-color: #ff9900; color: #232f3e; }
</style>
@endsection

@section('content')

<!-- Content Header (Page header) – banner -->
<section class="content-header no-print">
    <div class="sell-header-banner amazon-theme-banner">
        <div class="sell-header-content">
            <h1 class="sell-header-title">
                <i class="fas fa-file-invoice-dollar"></i>
                @lang('sale.sells') Invoice
            </h1>
            <p class="sell-header-subtitle">
                View and manage all sales invoices. Track payment status, shipping, and totals.
            </p>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content no-print sell-list-page">
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
                            @include('sell.partials.sell_list_filters')
                            @if ($payment_types)
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('payment_method', __('lang_v1.payment_method') . ':') !!}
                                    {!! Form::select('payment_method', $payment_types, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'placeholder' => __('lang_v1.all'),
                                    ]) !!}
                                </div>
                            </div>
                            @endif

                            @if (!empty($sources))
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('sell_list_filter_source', __('lang_v1.sources') . ':') !!}

                                    {!! Form::select('sell_list_filter_source', $sources, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'placeholder' => __('lang_v1.all'),
                                    ]) !!}
                                </div>
                            </div>
                            @endif
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
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_sales')])
    @can('direct_sell.access')
    @slot('tool')
    <div class="box-tools">
        <a id="dynamic_button" class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white tw-rounded-full pull-right amazon-orange-add"
            href="{{ action([\App\Http\Controllers\SellController::class, 'create']) }}">
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
    @if (auth()->user()->can('direct_sell.view') ||
    auth()->user()->can('view_own_sell_only') ||
    auth()->user()->can('view_commission_agent_sell'))
    @php
    $custom_labels = json_decode(session('business.custom_labels'), true);
    @endphp
        <table  class="table  table-striped ajax_view hide-footer" id="sell_table" style="min-width:max-content;">
            <thead>
                <tr>
                    <th>@lang('messages.date')</th>
                    <th>@lang('sale.invoice_no')</th>
                    <th>@lang('sale.customer_name')</th>
                    <th>@lang('lang_v1.contact_no')</th>
                    <th>@lang('sale.location')</th>
                    <th>Payment</th>
                    <th>Method</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Due</th>
                    <th>@lang('lang_v1.sell_return_due')</th>
                    <th>@lang('lang_v1.shipping_status')</th>
                    <th>Items</th>
                    <th>@lang('lang_v1.types_of_service')</th>
                    <th>{{ $custom_labels['types_of_service']['custom_field_1'] ??
                        __('lang_v1.service_custom_field_1') }}
                    </th>
                    <th>{{ $custom_labels['sell']['custom_field_1'] ?? '' }}</th>
                    <th>{{ $custom_labels['sell']['custom_field_2'] ?? '' }}</th>
                    <th>{{ $custom_labels['sell']['custom_field_3'] ?? '' }}</th>
                    <th>{{ $custom_labels['sell']['custom_field_4'] ?? '' }}</th>
                    <th>@lang('lang_v1.added_by')</th>
                    <th>@lang('sale.sell_note')</th>
                    <th>@lang('sale.staff_note')</th>
                    <th>@lang('sale.shipping_details')</th>
                    <th>@lang('restaurant.table')</th>
                    <th>@lang('restaurant.service_staff')</th>
                    <th>@lang('messages.action')</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr class="bg-gray font-17 footer-total text-left">
                    <td colspan="2"></td>
                    <td><strong>@lang('sale.total'):</strong></td>
                    <td colspan="2"></td>
                    <td class="footer_payment_status_count"></td>
                    <td class="payment_method_count"></td>
                    <td class="footer_sale_total"></td>
                    <td class="footer_total_paid"></td>
                    <td class="footer_total_remaining"></td>
                    <td class="footer_total_sell_return_due"></td>
                    <td colspan="2"></td>
                    <td class="service_type_count"></td>
                    <td colspan="12"></td>
                </tr>
            </tfoot>
        </table>
    @endif
    @endcomponent
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<!-- This will be printed -->
<section class="invoice print_section" id="receipt_section">
</section>

@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
            //Date range as a button
            $('#sell_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                        moment_date_format));
                    sell_table.ajax.reload();
                }
            );
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#sell_list_filter_date_range').val('');
                sell_table.ajax.reload();
            });

            sell_table = $('#sell_table').DataTable({
                processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                serverSide: true,
                fixedHeader:false,
                aaSorting: [
                    [1, 'desc']
                ],
                "ajax": {
                    "url": "/sells",
                    "data": function(d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate
                                .format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                        d.is_direct_sale = 1;

                        d.location_id = $('#sell_list_filter_location_id').val();
                        d.customer_id = $('#sell_list_filter_customer_id').val();
                        d.payment_status = $('#sell_list_filter_payment_status').val();
                        d.created_by = $('#created_by').val();
                        d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
                        d.service_staffs = $('#service_staffs').val();

                        if ($('#shipping_status').length) {
                            d.shipping_status = $('#shipping_status').val();
                        }

                        if ($('#sell_list_filter_source').length) {
                            d.source = $('#sell_list_filter_source').val();
                        }

                        if ($('#only_subscriptions').is(':checked')) {
                            d.only_subscriptions = 1;
                        }

                        if ($('#payment_method').length) {
                            d.payment_method = $('#payment_method').val();
                        }

                        d = __datatable_ajax_callback(d);
                    }
                },
                scrollY: "73vh",
                scrollX: true,
                scrollCollapse: false,
                columns: [
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'conatct_name',
                        name: 'conatct_name'
                    },
                    {
                        data: 'mobile',
                        name: 'contacts.mobile',
                        visible: false
                    },
                    {
                        data: 'business_location',
                        name: 'bl.name',
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'payment_methods',
                        orderable: false,
                        "searchable": false
                    },
                    {
                        data: 'final_total',
                        name: 'final_total'
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        "searchable": false
                    },
                    {
                        data: 'total_remaining',
                        name: 'total_remaining'
                    },
                    {
                        data: 'return_due',
                        orderable: false,
                        "searchable": false,
                    },
                    {
                        data: 'shipping_status',
                        name: 'shipping_status'
                    },
                    {
                        data: 'total_items',
                        name: 'total_items',
                        "searchable": false
                    },
                    {
                        data: 'types_of_service_name',
                        name: 'tos.name',
                        @if (empty($is_types_service_enabled))
                            visible: false
                        @endif
                    },
                    {
                        data: 'service_custom_field_1',
                        name: 'service_custom_field_1',
                        @if (empty($is_types_service_enabled))
                            visible: false
                        @endif
                    },
                    {
                        data: 'custom_field_1',
                        name: 'transactions.custom_field_1',
                        @if (empty($custom_labels['sell']['custom_field_1']))
                            visible: false
                        @endif
                    },
                    {
                        data: 'custom_field_2',
                        name: 'transactions.custom_field_2',
                        @if (empty($custom_labels['sell']['custom_field_2']))
                            visible: false
                        @endif
                    },
                    {
                        data: 'custom_field_3',
                        name: 'transactions.custom_field_3',
                        @if (empty($custom_labels['sell']['custom_field_3']))
                            visible: false
                        @endif
                    },
                    {
                        data: 'custom_field_4',
                        name: 'transactions.custom_field_4',
                        @if (empty($custom_labels['sell']['custom_field_4']))
                            visible: false
                        @endif
                    },
                    {
                        data: 'added_by',
                        name: 'u.first_name'
                    },
                    {
                        data: 'additional_notes',
                        name: 'additional_notes',
                        visible: false
                    },
                    {
                        data: 'staff_note',
                        name: 'staff_note'
                    },
                    {
                        data: 'shipping_details',
                        name: 'shipping_details',
                        visible: false
                    },
                    {
                        data: 'table_name',
                        name: 'tables.name',
                        @if (empty($is_tables_enabled))
                            visible: false
                        @endif
                    },
                    {
                        data: 'waiter',
                        name: 'ss.first_name',
                        @if (empty($is_service_staff_enabled))
                            visible: false
                        @endif
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        "searchable": false
                    },
                ],
                "fnDrawCallback": function(oSettings) {
                    __currency_convert_recursively($('#sell_table'));
                },
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

                "footerCallback": function(row, data, start, end, display) {
                    var footer_sale_total = 0;
                    var footer_total_paid = 0;
                    var footer_total_remaining = 0;
                    var footer_total_sell_return_due = 0;
                    for (var r in data) {
                        footer_sale_total += $(data[r].final_total).data('orig-value') ? parseFloat($(
                            data[r].final_total).data('orig-value')) : 0;
                        footer_total_paid += $(data[r].total_paid).data('orig-value') ? parseFloat($(
                            data[r].total_paid).data('orig-value')) : 0;
                        footer_total_remaining += $(data[r].total_remaining).data('orig-value') ?
                            parseFloat($(data[r].total_remaining).data('orig-value')) : 0;
                        footer_total_sell_return_due += $(data[r].return_due).find('.sell_return_due')
                            .data('orig-value') ? parseFloat($(data[r].return_due).find(
                                '.sell_return_due').data('orig-value')) : 0;
                    }

                    $('.footer_total_sell_return_due').html(__currency_trans_from_en(
                        footer_total_sell_return_due));
                    $('.footer_total_remaining').html(__currency_trans_from_en(footer_total_remaining));
                    $('.footer_total_paid').html(__currency_trans_from_en(footer_total_paid));
                    $('.footer_sale_total').html(__currency_trans_from_en(footer_sale_total));

                    $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
                    $('.service_type_count').html(__count_status(data, 'types_of_service_name'));
                    $('.payment_method_count').html(__count_status(data, 'payment_methods'));
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).find('td:eq(6)').attr('class', 'clickable_td');
                }
            });

            $(document).on('change',
                '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status, #sell_list_filter_source, #payment_method',
                function() {
                    sell_table.ajax.reload();
                });

            $('#only_subscriptions').on('ifChanged', function(event) {
                sell_table.ajax.reload();
            });
            // Track the last focused row for better navigation
    var lastFocusedRow = null;
    
    // Update lastFocusedRow when any input in a table row gets focus
    $(document).on("focus", "tr.sell-line-row input", function() {
        lastFocusedRow = $(this).closest('tr.sell-line-row');
    });
    $(document).on("keydown", function(e) {
        if (e.key === 'H' || e.key === 'h') {
            var focusedElement = document.activeElement;
            if(focusedElement.tagName!='INPUT' && focusedElement.tagName!='SELECT' || focusedElement.type =='checkbox'){
                e.preventDefault();
                $('.product_history').trigger('click');
            }else{
                console.log('focusedElement', focusedElement);
            }
        }
    });
    
    $(document).on("keydown", function (e) {
        // Check if Shift + Arrow Up or Down is pressed
        if (e.shiftKey && (e.key === 'ArrowUp' || e.key === 'ArrowDown')) {
            e.preventDefault();
            
            // Check if the table exists first
            var table = $('#sellsModalTable');
            if (!table.length) {
                return false;
            }
            
            // Get the currently focused element
            var focusedElement = document.activeElement;
            var currentRow = $(focusedElement).closest('tr.sell-line-row');
            
            // If no row is currently focused, try to use the last focused row
            if (!currentRow || currentRow.length === 0) {
                currentRow = lastFocusedRow;
            }
            
            // If still no row, check if any checkbox is already checked
            if (!currentRow || currentRow.length === 0) {
                var checkedCheckbox = table.find('tr.sell-line-row input[type="checkbox"]:checked');
                if (!checkedCheckbox || checkedCheckbox.length === 0) {
                    // No checkbox is checked, check the first row
                    var firstRow = table.find('tr.sell-line-row').first();
                    if (firstRow && firstRow.length > 0) {
                        // Uncheck all checkboxes first
                        table.find('tr.sell-line-row').each(function(){
                            var checkbox = $(this).find('input[type="checkbox"]');
                            if (checkbox.length > 0) {
                                checkbox.prop('checked', false);
                            }
                        });
                        // Check the first row
                        var firstCheckbox = firstRow.find('input[type="checkbox"]');
                        if (firstCheckbox.length > 0) {
                            firstCheckbox.prop('checked', true);
                        }
                        // Update lastFocusedRow before focusing
                        lastFocusedRow = firstRow;
                        // Focus on the first row's first input field
                        var firstInput = firstRow.find('input').first();
                        if (firstInput.length > 0) {
                            firstInput.focus();
                        }
                    }
                } else {
                    // Use the row with the checked checkbox as current row
                    currentRow = checkedCheckbox.closest('tr.sell-line-row');
                    if (currentRow && currentRow.length > 0) {
                        lastFocusedRow = currentRow;
                    }
                }
                return false;
            }
            
            // If a row is focused, handle navigation
            if (currentRow && currentRow.length > 0) {
                var targetRow;
                
                if (e.key === 'ArrowUp') {
                    // Move to previous row
                    targetRow = currentRow.prev('tr.sell-line-row');
                    if (!targetRow || targetRow.length === 0) {
                        // If no previous row, wrap to the last row
                        targetRow = table.find('tr.sell-line-row').last();
                    }
                } else {
                    // Move to next row
                    targetRow = currentRow.next('tr.sell-line-row');
                    if (!targetRow || targetRow.length === 0) {
                        // If no next row, wrap to the first row
                        targetRow = table.find('tr.sell-line-row').first();
                    }
                }
                
                if (targetRow && targetRow.length > 0) {
                    // Uncheck all checkboxes first
                    table.find('tr.sell-line-row').each(function(){
                        var checkbox = $(this).find('input[type="checkbox"]');
                        if (checkbox.length > 0) {
                            checkbox.prop('checked', false);
                        }
                    });
                    
                    // Check the target row's checkbox
                    var targetCheckbox = targetRow.find('input[type="checkbox"]');
                    if (targetCheckbox.length > 0) {
                        targetCheckbox.prop('checked', true);
                    }
                    
                    // Update lastFocusedRow immediately
                    lastFocusedRow = targetRow;
                    
                    // Use setTimeout to ensure focus is set properly
                    setTimeout(function() {
                        var targetInput = targetRow.find('input').first();
                        if (targetInput && targetInput.length > 0) {
                            targetInput.focus();
                        }
                    }, 10);
                }
            }
            
            return false;
        }
    });
        });
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection