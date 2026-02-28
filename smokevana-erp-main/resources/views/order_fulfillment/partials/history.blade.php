<div class="modal-dialog modal-xl no-print" id='history_modal' role="document">
    <div class="modal-content">
        <div class="modal-header" style="padding: 5px 10px;">
            <div class="tw-flex tw-justify-between" >
                <h4 class="modal-title tw-flex"  style="align-items: center" id="modalTitle">Sales Order History</h4>
                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" data-dismiss="modal"
                id='close_button'>@lang('messages.close')</button>
            </div>
        </div>
        <div class="modal-body">
            <div class="modal fade" id="filterHistoryModal" tabindex="-1" role="dialog" aria-labelledby="filterHistoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document"> {{-- Use modal-lg or modal-xl as needed --}}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('report.filters')</h4>
                    </div>
                    <div class="modal-body" style="padding: 0px; margin-top: 10px;">
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('history_sell_list_filter_date_range', __('report.date_range') . ':') !!}
                                {!! Form::text('history_sell_list_filter_date_range', null, ['id'=>'history_sell_list_filter_date_range' ,'placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                            </div>
                        </div>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white"
                            data-dismiss="modal">@lang('messages.close')</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- filter end --}}
        {{-- <div class="table-responsive"> --}}
        {{-- <table class="table table-bordered table-striped" style="width: 100% !important;" --}}
        <div class="tab-pane active" style="overflow-x: auto; width: 100%;"  >
            <table style="border-collapse: collapse; width: 100%; min-width: max-content;" class="table table-bordered table-striped " id="salesHistory" >
                            <thead style="white-space: nowrap;" >
                                <tr>
                                    <th>Sales Order</th>
                                    <th>Name</th>
                                    <th>Final Total</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th>Paid Amount</th>
                                    <th>Sale Date</th>
                                    <th>Picking Status</th>
                                    <th>Picker </th>
                                    <th>Verifier</th>
                                    <th>Picked Qty</th>
                                    <th>Picking Time</th>
                                    <th>Qty Amount</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody></tbody>
                    </table>
            </div>
        </div>
        <div id="invoice_modal_container"></div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var pickedOrdersTable = $('#salesHistory').DataTable({
                processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                serverSide: true,
                fixedHeader: false,
                scrollX: true,
                scrollY: '75vh',
                ajax: {
                    url: '/order-fulfillment-history-data',
                    data: function (d) {
                        if ($('#history_sell_list_filter_date_range').length > 0) {
                            d.history_sell_list_filter_date_range = $('#history_sell_list_filter_date_range').val();
                        }
                    }
                },
                order: [[1, 'desc']], // Default order by invoice_no descending
                columns: [
                    {
                        data: 'invoice_no',
                        name: 'transactions.invoice_no',
                        render: function (data) {
                            return data;
                        }
                    },
                    {
                        data: 'merged_column',
                        name: 'merged_column'
                    },
                    {
                        data: 'final_total',
                        name: 'transactions.final_total'
                    },
                    {
                        data: 'status',
                        name: 'transactions.status'
                    },
                    {
                        data: 'payment_status',
                        name: 'transactions.payment_status',
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            return data;
                        }
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sale_date',
                        name: 'transactions.transaction_date' 
                    },
                    {
                        data: 'picking_status',
                        name: 'transactions.picking_status',
                        render: function (data) {
                            return data;
                        }
                    },
                    {
                        data: 'picker_details',
                        name: 'picker_details',
                        render: function (data) {
                            return data;
                        }
                    },
                    {
                        data: 'verifier_details',
                        name: 'verifier_details',
                        render: function (data) {
                            return data;
                        }
                    },
                    {
                        data: 'total_picked_qty',
                        name: 'total_picked_qty',
                        render: function (data) {
                            return data;
                        }
                    },
                    {
                        data: 'picking_time',
                        name: 'picking_time',
                        render: function (data) {
                            return data;
                        }
                    },
                    {
                        data: 'picked_qty_amount',
                        name: 'picked_qty_amount',
                        render: function (data) {
                            return data;
                        }
                    },
                    // {
                    //     data: 'action',
                    //     name: 'action',
                    //     orderable: false,
                    //     searchable: false,
                    //     render: function (data) {
                    //         return data;
                    //     }
                    // }
                ],
                buttons: [
                    {
                        text: '<i class="fa fa-filter"></i> Filters',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        action: function () {
                            $('#filterHistoryModal').modal('show');
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> ' + LANG.export_to_csv,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible'
                        },
                        footer: true
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG.export_to_excel,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible'
                        },
                        footer: true
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                            stripHtml: true
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
                        }
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2'
                    }
                ]
            });
        $(document).on('click', '.view-invoice', function(e) {
            e.preventDefault();
            var invoiceId = $(this).data('id');
            var url = '/sells/' + invoiceId;
            var modalId = 'invoice_modal_' + invoiceId;
            $('#invoice_modal_container').empty();
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    var newModal = $('<div class="modal fade" id="' + modalId + '" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">' +
                        '<div class="modal-content">' + response + '</div>' +
                        '</div>');
                    $('#invoice_modal_container').append(newModal);
                    newModal.modal('show');
                    $('#invoice_modal_container #close_button').on('click', function(e) {
                        e.preventDefault();
                        $('#invoice_modal_container').empty();
                        $('.modal-backdrop').remove();
                    });
                    
                }
            });
        });
    });
</script>
