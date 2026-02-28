@extends('layouts.app')
@section('title', 'Order Fulfillment')

@section('content')
    <section class="content-header">
        <div class="tw-flex tw-justify-between tw-items-center">
            <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Your Sales Orders
                <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold"></small>
            </h1>
            <button
                class="btn btn-sm px-2 py-1 rounded text-sm font-medium text-white transition-all duration-200 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
                id="logging-toggle">
                @if($activity && $activity->is_active)
                    <span class="btn-text">Active</span>
                    <span class="btn-icon ml-2">🟢</span>
                @else
                    <span class="btn-text">Inactive</span>
                    <span class="btn-icon ml-2">🔴</span>
                @endif
            </button>
        </div>
    </section>
    <section class="content">
        <div class="box-primary tw-mb-4 tw-transition-all lg:tw-col-span-2 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw-translate-y-0.5 tw-ring-gray-200">
            <div class="responsive-table tw-p-2 sm:tw-p-3">
                <table id="picking-orders-table" class="table table-bordered table-striped nowrap" style="width: 100% !important;">
                    <thead>
                        <tr>
                            <th style="min-width: 30px"><input type="checkbox" id="select-all-2"></th>
                            <th style="min-width: 100px">Sales Order</th>
                            <th style="min-width: 150px; max-width: 200px; white-space: normal; word-wrap: break-word;">Name</th>
                            <th style="min-width: 100px">Final Total</th>
                            <th style="min-width: 80px">Status</th>
                            <th style="min-width: 100px">Payment Status</th>
                            <th style="min-width: 100px">Paid Amount</th>
                            <th style="min-width: 100px">Sale Date</th>
                            <th style="min-width: 100px">Picking Status</th>
                            <th style="min-width: 100px">Picker Details</th>
                            <th style="min-width: 100px">Picked Qty</th>
                            <th style="min-width: 100px">Qty Amount</th>
                            <th style="min-width: 80px">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    @section('javascript')
        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            #logging-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 4px;
                cursor: pointer;
                border: none !important;
                outline: none;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                min-width: 120px;
                font-size: 13px;
                height: 32px;
                background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
                box-shadow: 0 2px 8px 0 rgba(16, 185, 129, 0.3) !important;
            }

            #logging-toggle:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
            }

            #logging-toggle:active {
                transform: translateY(0);
            }

            #logging-toggle:disabled {
                cursor: not-allowed;
                transform: none !important;
                opacity: 0.7;
            }

            .btn-text {
                font-weight: 600;
                letter-spacing: 0.5px;
            }

            .btn-icon {
                font-size: 12px;
                line-height: 1;
                vertical-align: middle;
                display: inline-flex;
                align-items: center;
            }
        </style>
        <script>
            // Fallback function for SweetAlert
            function showAlert(title, text, icon, confirmText, cancelText) {
                if (typeof Swal !== 'undefined') {
                    return Swal.fire({
                        title: title,
                        text: text,
                        icon: icon,
                        showCancelButton: !!cancelText,
                        confirmButtonText: confirmText,
                        cancelButtonText: cancelText
                    });
                } else {
                    // Fallback to regular confirm/alert
                    if (cancelText) {
                        return confirm(text) ? Promise.resolve({ isConfirmed: true }) : Promise.resolve({ isConfirmed: false });
                    } else {
                        alert(text);
                        return Promise.resolve({ isConfirmed: true });
                    }
                }
            }

            $(document).ready(function () {
                var pickingOrdersTable = $('#picking-orders-table').DataTable({
                    processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>` },
                    serverSide: true,
                    fixedHeader: false,
                    scrollX: true,
                    ajax: {
                        url: '/picker-man-order',
                        data: function (d) {
                            if ($('#sell_list_filter_date_range').length > 0) {
                                d.sell_list_filter_date_range = $('#sell_list_filter_date_range').val();
                            }
                        }
                    },
                    order: [[1, 'desc']], // Default order by invoice_no desc
                    columns: [
                        {
                            data: 'bulk_select',
                            name: 'bulk_select',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row) {
                                return '<input type="checkbox" class="order-checkbox" value="' + row.id + '">';
                            }
                        },
                        {
                            data: 'invoice_no',
                            name: 'transactions.invoice_no'
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
                            name: 'transactions.transaction_date' // ✅ use the real column, not alias
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
                            data: 'total_picked_qty',
                            name: 'total_picked_qty',
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
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function (data) {
                                return data;
                            }
                        }
                    ],
                    buttons: [{
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
                    initComplete: function () {
                        $('.picked-apply-operation').on('click', function () {
                            var selectedRows = [];
                            $('.order-checkbox:checked').each(function () {
                                selectedRows.push($(this).val());
                            });

                            var operation = $('#picked-order-action').val();
                            if (selectedRows.length > 0 && operation) {
                                $.ajax({
                                    url: '/mark-as-picked',
                                    type: 'POST',
                                    data: {
                                        ids: selectedRows,
                                        operation: operation,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function (response) {
                                        toastr.success(response.message);
                                        pickingOrdersTable.ajax.reload();
                                    }
                                });
                                $(this).closest(".modal").modal("hide");
                            } else {
                                toastr.error('Please select rows and Picker.');
                            }
                        });
                    }
                });
                function updateButtonAppearance(currentStatus) {
                    const button = $('#logging-toggle');
                    const btnText = button.find('.btn-text');
                    const btnIcon = button.find('.btn-icon');

                    if (currentStatus) {
                        btnText.text('Inactive');
                        btnIcon.text('🔴');
                        button.attr('style', 'background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important; box-shadow: 0 2px 8px 0 rgba(220, 38, 38, 0.3) !important; border: none !important; min-width: 120px !important; font-size: 13px !important; height: 32px !important;');

                    } else {
                        btnText.text('Active');
                        btnIcon.text('🟢');
                        button.attr('style', 'background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; box-shadow: 0 2px 8px 0 rgba(16, 185, 129, 0.3) !important; border: none !important; min-width: 120px !important; font-size: 13px !important; height: 32px !important;');
                    }

                }
                function getCurrentStatus() {
                    const btnText = $('#logging-toggle .btn-text').text();
                    return btnText.includes('Inactive');
                }

                $('#logging-toggle').on('click', function (e) {
                    e.preventDefault();
                    const button = $(this);
                    const isCurrentlyActive = getCurrentStatus();
                    const newStatus = !isCurrentlyActive;
                    const statusText = !newStatus ? 'active' : 'inactive';
                    const statusValue = newStatus ? 'false' : 'true';
                    showAlert(
                        'Confirm Action',
                        `Are you sure you want to mark yourself as ${statusText} for picking?`,
                        'question',
                        `Yes, mark as ${statusText}`,
                        'Cancel'
                    ).then((result) => {
                        if (result.isConfirmed) {
                            button.prop('disabled', true);
                            button.find('.btn-text').text('Processing...');
                            button.find('.btn-icon').text('⏳');
                            button.attr('style', 'background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important; box-shadow: 0 2px 8px 0 rgba(107, 114, 128, 0.3) !important; border: none !important; min-width: 120px !important; font-size: 13px !important; height: 32px !important;');

                            $.ajax({
                                url: `/logging-active/${statusValue}`,
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function (response) {

                                    if (response.status) {
                                        updateButtonAppearance(newStatus);

                                        showAlert(
                                            'Success!',
                                            response.message,
                                            'success',
                                            'OK'
                                        ).then(() => {
                                            updateButtonAppearance(newStatus);
                                        });
                                    } else {
                                        showAlert(
                                            'Error!',
                                            response.message || 'Something went wrong',
                                            'error',
                                            'OK'
                                        );
                                    }
                                },
                                error: function (xhr) {
                                    let errorMessage = 'Something went wrong. Please try again.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    showAlert(
                                        'Error!',
                                        errorMessage,
                                        'error',
                                        'OK'
                                    );
                                },
                                complete: function () {
                                    button.prop('disabled', false);
                                }
                            });
                        }
                    });
                });

                // Initialize button appearance on page load
                $(document).ready(function () {
                    const isActive = getCurrentStatus();
                    updateButtonAppearance(isActive);
                });
            });
        </script>
    @endsection
@endsection