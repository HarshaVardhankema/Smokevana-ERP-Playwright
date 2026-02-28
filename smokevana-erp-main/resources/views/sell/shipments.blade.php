@extends('layouts.app')
@section('title', __( 'lang_v1.shipments'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang( 'lang_v1.shipments')
    </h1>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('created_by', __('report.user') . ':') !!}
                                    {!! Form::select('created_by', $sales_representative, null, ['class' =>
                                    'form-control select2', 'style' => 'width:100%']); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('sell_list_filter_payment_status', __('purchase.payment_status') .
                                    ':') !!}
                                    {!! Form::select('sell_list_filter_payment_status', ['paid' => __('lang_v1.paid'),
                                    'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' =>
                                    __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' =>
                                    'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('shipping_status', __('lang_v1.shipping_status') . ':') !!}

                                    {!! Form::select('shipping_status', $shipping_statuses, null, ['class' =>
                                    'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                    ]); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('delivery_person', __('lang_v1.delivery_person') . ':') !!}

                                    {!! Form::select('delivery_person', $delevery_person, null, ['class' =>
                                    'form-control select2', 'style' => 'width:100%']); !!}
                                </div>
                            </div>
                            @if(!empty($service_staffs))
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('service_staffs', __('restaurant.service_staff') . ':') !!}
                                    {!! Form::select('service_staffs', $service_staffs, null, ['class' => 'form-control
                                    select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
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
    @php
    $custom_labels = json_decode(session('business.custom_labels'), true);
    @endphp
    @component('components.widget', ['class' => 'box-primary'])
    @if(auth()->user()->can('access_shipping') ||
    auth()->user()->can('access_own_shipping') ||
    auth()->user()->can('access_commission_agent_shipping') )
    <div class="table-responsive">
        <table class="table table-bordered table-striped ajax_view" id="sell_table" style="min-width: max-content;">
            <thead>
                <tr>
                    <th >@lang('messages.date')</th>
                    <th >@lang('sale.invoice_no')</th>
                    <th >@lang('sale.customer_name')</th>
                    <th >@lang('lang_v1.contact_no')</th>
                    <th >@lang('sale.location')</th>
                    <th >@lang('lang_v1.delivery_person')</th>
                    <th >@lang('lang_v1.shipping_status')</th>
                    @if(!empty($custom_labels['shipping']['custom_field_1']))
                    <th >
                        {{$custom_labels['shipping']['custom_field_1']}}
                    </th>
                    @endif
                    @if(!empty($custom_labels['shipping']['custom_field_2']))
                    <th >
                        {{$custom_labels['shipping']['custom_field_2']}}
                    </th>
                    @endif
                    @if(!empty($custom_labels['shipping']['custom_field_3']))
                    <th >
                        {{$custom_labels['shipping']['custom_field_3']}}
                    </th>
                    @endif
                    @if(!empty($custom_labels['shipping']['custom_field_4']))
                    <th >
                        {{$custom_labels['shipping']['custom_field_4']}}
                    </th>
                    @endif
                    @if(!empty($custom_labels['shipping']['custom_field_5']))
                    <th >
                        {{$custom_labels['shipping']['custom_field_5']}}
                    </th>
                    @endif
                    <th >@lang('sale.payment_status')</th>
                    <th >@lang('restaurant.service_staff')</th>
                    <th >@lang('messages.action')</th>
                </tr>
            </thead>
        </table>
    </div>
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
    $(document).ready( function(){
    //Date range as a button
    $('#sell_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
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
        aaSorting: [[1, 'desc']],
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: false,
        "ajax": {
            "url": "/sells",
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

                if($('#sell_list_filter_payment_status').length) {
                    d.payment_status = $('#sell_list_filter_payment_status').val();
                }
                if($('#created_by').length) {
                    d.created_by = $('#created_by').val();
                }
                if($('#service_staffs').length) {
                    d.service_staffs = $('#service_staffs').val();
                }
                d.only_shipments = true;
                d.shipping_status = $('#shipping_status').val();
                d.delivery_person = $('#delivery_person').val();
            }
        },
        columns: [
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'conatct_name', name: 'conatct_name'},
            { data: 'mobile', name: 'contacts.mobile'},
            { data: 'business_location', name: 'bl.name'},
            { data: 'delivery_person', name: 'delivery_person'},
            { data: 'shipping_status', name: 'shipping_status'},
            @if(!empty($custom_labels['shipping']['custom_field_1']))
                { data: 'shipping_custom_field_1', name: 'shipping_custom_field_1'},
            @endif
            @if(!empty($custom_labels['shipping']['custom_field_2']))
                { data: 'shipping_custom_field_2', name: 'shipping_custom_field_2'},
            @endif
            @if(!empty($custom_labels['shipping']['custom_field_3']))
                { data: 'shipping_custom_field_3', name: 'shipping_custom_field_3'},
            @endif
            @if(!empty($custom_labels['shipping']['custom_field_4']))
                { data: 'shipping_custom_field_4', name: 'shipping_custom_field_4'},
            @endif
            @if(!empty($custom_labels['shipping']['custom_field_5']))
                { data: 'shipping_custom_field_5', name: 'shipping_custom_field_5'},
            @endif
            { data: 'payment_status', name: 'payment_status'},
            { data: 'waiter', name: 'ss.first_name', @if(empty($is_service_staff_enabled)) visible: false @endif },
            { data: 'action', name: 'action', searchable: false, orderable: false}
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
            __currency_convert_recursively($('#sell_table'));
        },
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(4)').attr('class', 'clickable_td');
        }
    });

    $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #shipping_status, #service_staffs, #delivery_person',  function() {
        sell_table.ajax.reload();
    });
});
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection