@extends('layouts.app')
@section('title', __('lang_v1.sell_return'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .sell-return-list-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
    .sr-header-banner {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 0 0 10px 10px;
        padding: 22px 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .sr-header-banner.amazon-theme-banner::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #ff9900; z-index: 1; }
    .sr-header-content { display: flex; flex-direction: column; gap: 4px; }
    .sr-header-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; margin: 0; color: #fff !important; }
    .sr-header-title i { font-size: 22px; color: #fff !important; }
    .sr-header-subtitle { font-size: 13px; color: rgba(255,255,255,0.9) !important; margin: 0; }
    .sell-return-list-page #dynamic_button,
    .sell-return-list-page .box-tools .tw-dw-btn,
    .sell-return-list-page .box-tools a[href*="create"] { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 2px solid #C7511F !important; color: #fff !important; }
    .sell-return-list-page #dynamic_button:hover,
    .sell-return-list-page .box-tools .tw-dw-btn:hover,
    .sell-return-list-page .box-tools a[href*="create"]:hover { color: #fff !important; opacity: 0.95; border-color: #E47911 !important; }
    .sell-return-list-page .box-primary { border-radius: 10px; overflow: hidden; border: 1px solid #D5D9D9; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .sell-return-list-page .box-primary .box-header { background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important; color: #fff !important; border: none !important; padding: 14px 20px !important; position: relative; }
    .sell-return-list-page .box-primary .box-header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900; }
    .sell-return-list-page .box-primary .box-title { color: #fff !important; font-weight: 600; }
    .sell-return-list-page .box-primary .tw-flow-root,
    .sell-return-list-page .box-primary .table-responsive { background: #f7f8f8 !important; padding: 1rem 1.25rem !important; }
    .sell-return-list-page .dt-buttons .btn,
    .sell-return-list-page .dt-buttons button,
    .sell-return-list-page .dt-buttons .dt-button { background: #232f3e !important; border: 1px solid #37475a !important; color: #fff !important; }
    .sell-return-list-page .dt-buttons .btn:hover,
    .sell-return-list-page .dt-buttons button:hover,
    .sell-return-list-page .dt-buttons .dt-button:hover { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border-color: #C7511F !important; color: #fff !important; }
    .sell-return-list-page .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #ff9900 !important; border-color: #ff9900 !important; color: #fff !important; }
    .sell-return-list-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover { border-color: #ff9900; color: #232f3e; }
</style>
@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <div class="sr-header-banner amazon-theme-banner">
        <div class="sr-header-content">
            <h1 class="sr-header-title">
                <i class="fas fa-undo"></i>
                @lang('lang_v1.sell_return')
            </h1>
            <p class="sr-header-subtitle">
                View and manage credit notes and sales returns. Track payment status and refunds.
            </p>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content no-print sell-return-list-page">
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
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.sell_return')])
        @slot('tool')
            <div class="box-tools pull-right">
                <a id="dynamic_button" class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white tw-rounded-full amazon-orange-add"
                    href="{{ action([\App\Http\Controllers\SellReturnController::class, 'create'], ['cid' => 'sells_return']) }}">
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
    @include('sell_return.partials.sell_return_list')
    @endcomponent
    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>

<!-- /.content -->
@stop
@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>
    $(document).ready(function(){
        $('#sell_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                sell_return_table.ajax.reload();
            }
        );
        $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#sell_list_filter_date_range').val('');
            sell_return_table.ajax.reload();
        });

        sell_return_table = $('#sell_return_table').DataTable({
            processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            fixedHeader:true,
            scrollY:'60vh',
            scrollX: true,

            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sell-return",
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
                { data: 'payment_status', name: 'payment_status'},
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
                var total_sell = sum_table_col($('#sell_return_table'), 'final_total');
                $('.footer_sell_return_total').text(__currency_trans_from_en(total_sell));
                
                $('.footer_payment_status_count_sr').html(__sum_status_html($('#sell_return_table'), 'payment-status-label'));

                var total_due = sum_table_col($('#sell_return_table'), 'payment_due');
                $('.footer_total_due_sr').text(__currency_trans_from_en(total_due));

                __currency_convert_recursively($('#sell_return_table'));
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(2)').attr('class', 'clickable_td');
            }
        });
        $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #created_by',  function() {
            sell_return_table.ajax.reload();
        });
    })

    $(document).on('click', 'a.delete_sell_return', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            sell_return_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
</script>

@endsection