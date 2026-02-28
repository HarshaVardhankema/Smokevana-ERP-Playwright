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

$(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status',  function() {
    sell_table.ajax.reload();
});

sell_table = $('#sell_table').DataTable({
        processing: true,
        language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
        serverSide: true,
        fixedHeader:false,
        aaSorting: [[1, 'desc']],
        scrollY: "75vh",
        scrollX:        true,
        scrollCollapse: false,
        dom: '<"amazon-table-toolbar"<"toolbar-search"f><"toolbar-buttons"B>>rtip',
        buttons: [
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'amazon-export-btn',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                },
                footer: true
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'amazon-export-btn',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                },
                footer: true
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'amazon-export-btn',
                exportOptions: {
                    columns: ':visible:not(:last-child)',
                    stripHtml: true
                },
                footer: true
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Columns',
                className: 'amazon-export-btn column-visibility'
            }
        ],
        "ajax": {
            "url": "/sells", 
            "data": function ( d ) {
                if($('#sell_list_filter_date_range').val()) {
                    var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
                if ($('#is_direct_sale').length) {
                    d.is_direct_sale = $('#is_direct_sale').val();
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
                if($('#sales_cmsn_agnt').length) {
                    d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
                }
                if($('#service_staffs').length) {
                    d.service_staffs = $('#service_staffs').val();
                }

                if($('#shipping_status').length) {
                    d.shipping_status = $('#shipping_status').val();
                }

                if($('#only_subscriptions').length && $('#only_subscriptions').is(':checked')) {
                    d.only_subscriptions = 1;
                }

                d = __datatable_ajax_callback(d);
            }
        },
        columns: [
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'conatct_name', name: 'conatct_name'},
            { data: 'mobile', name: 'contacts.mobile',visible: false},
            { data: 'business_location', name: 'bl.name'},
            { data: 'payment_status', name: 'payment_status'},
            { data: 'payment_methods', orderable: false, "searchable": false},
            { data: 'final_total', name: 'final_total'},
            { data: 'total_paid', name: 'total_paid', "searchable": false},
            { data: 'total_remaining', name: 'total_remaining'},
            { data: 'return_due', orderable: false, "searchable": false,visible: false},
            { data: 'shipping_status', name: 'shipping_status'},
            { data: 'total_items', name: 'total_items', "searchable": false},
            { data: 'types_of_service_name', name: 'tos.name', @if(empty($is_types_service_enabled)) visible: false @endif},
            { data: 'service_custom_field_1', name: 'service_custom_field_1', @if(empty($is_types_service_enabled)) visible: false @endif},
            { data: 'added_by', name: 'u.first_name'},
            { data: 'additional_notes', name: 'additional_notes',visible: false},
            { data: 'staff_note', name: 'staff_note'},
            { data: 'shipping_details', name: 'shipping_details',visible: false},
            { data: 'table_name', name: 'tables.name', @if(empty($is_tables_enabled)) visible: false @endif },
            { data: 'waiter', name: 'ss.first_name', @if(empty($is_service_staff_enabled)) visible: false @endif },
            { data: 'action', name: 'action', orderable: false, "searchable": false}
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#sell_table'));
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var footer_sale_total = 0;
            var footer_total_paid = 0;
            var footer_total_remaining = 0;
            var footer_total_sell_return_due = 0;
            for (var r in data){
                footer_sale_total += $(data[r].final_total).data('orig-value') ? parseFloat($(data[r].final_total).data('orig-value')) : 0;
                footer_total_paid += $(data[r].total_paid).data('orig-value') ? parseFloat($(data[r].total_paid).data('orig-value')) : 0;
                footer_total_remaining += $(data[r].total_remaining).data('orig-value') ? parseFloat($(data[r].total_remaining).data('orig-value')) : 0;
                footer_total_sell_return_due += $(data[r].return_due).find('.sell_return_due').data('orig-value') ? parseFloat($(data[r].return_due).find('.sell_return_due').data('orig-value')) : 0;
            }

            $('.footer_total_sell_return_due').html(__currency_trans_from_en(footer_total_sell_return_due));
            $('.footer_total_remaining').html(__currency_trans_from_en(footer_total_remaining));
            $('.footer_total_paid').html(__currency_trans_from_en(footer_total_paid));
            $('.footer_sale_total').html(__currency_trans_from_en(footer_sale_total));

            $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
            $('.service_type_count').html(__count_status(data, 'types_of_service_name'));
            $('.payment_method_count').html(__count_status(data, 'payment_methods'));
        },
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(6)').attr('class', 'clickable_td');
        }
    });
    if ($('#contact_id').val()) {
        // Hide contact_name and mobile columns if contact_id exists
        sell_table.column(2).visible(false); // Index of 'contact_name' column
        sell_table.column(3).visible(false); // Index of 'mobile' column
        sell_table.column(4).visible(false); // Index of 'mobile' column
    } else {
        // Show contact_name and mobile columns if contact_id is empty
        sell_table.column(2).visible(true);
        sell_table.column(3).visible(true);
        sell_table.column(4).visible(true);
    }
    $('#only_subscriptions').on('ifChanged', function(event){
        sell_table.ajax.reload();
    });
    
    $('#only_subscriptions').on('ifChanged', function(event){
        sales_order_table.ajax.reload();
    });
    sales_order_table = $('#sales_order_table').DataTable({
        processing: true,
        language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
        serverSide: true,
        fixedHeader:false,
        aaSorting: [[1, 'desc']],
        scrollY: "75vh",
        scrollX:        true,
        scrollCollapse: false,
        dom: '<"amazon-table-toolbar"<"toolbar-search"f><"toolbar-buttons"B>>rtip',
        buttons: [
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'amazon-export-btn',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                },
                footer: true
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'amazon-export-btn',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                },
                footer: true
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'amazon-export-btn',
                exportOptions: {
                    columns: ':visible:not(:last-child)',
                    stripHtml: true
                },
                footer: true
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Columns',
                className: 'amazon-export-btn column-visibility'
            }
        ],
        "ajax": {
            "url": "/sells?sale_type=sales_order&customer_id="+$('#contact_id').val(), 
            "data": function ( d ) {
                if($('#sale-order #sell_list_filter_date_range').val()) {
                    var start = $('#sale-order #sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#sale-order #sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
                if ($('#sale-order #is_direct_sale').length) {
                    d.is_direct_sale = $('#sale-order #is_direct_sale').val();
                }

                if($('#sale-order #sell_list_filter_location_id').length) {
                    d.location_id = $('#sale-order #sell_list_filter_location_id').val();
                }
                d.customer_id = $('#sale-order #sell_list_filter_customer_id').val();

                if($('#sale-order #sell_list_filter_payment_status').length) {
                    d.payment_status = $('#sale-order #sell_list_filter_payment_status').val();
                }
                if($('#sale-order #created_by').length) {
                    d.created_by = $('#sale-order #created_by').val();
                }
                if($('#sale-order #sales_cmsn_agnt').length) {
                    d.sales_cmsn_agnt = $('#sale-order #sales_cmsn_agnt').val();
                }
                if($('#sale-order #service_staffs').length) {
                    d.service_staffs = $('#sale-order #service_staffs').val();
                }

                if($('#sale-order #shipping_status').length) {
                    d.shipping_status = $('#sale-order #shipping_status').val();
                }

                if($('#sale-order #only_subscriptions').length && $('#sale-order #only_subscriptions').is(':checked')) {
                    d.only_subscriptions = 1;
                }

                d = __datatable_ajax_callback(d);
            }
        },
        columns: [
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'conatct_name', name: 'conatct_name' },
            { data: 'mobile', name: 'contacts.mobile' },
            { data: 'business_location', name: 'bl.name',visible: false},
            { data: 'payment_status', name: 'payment_status',visible: false},
            { data: 'status', name: 'status'},
            { data: 'payment_methods', orderable: false, "searchable": false},
            { data: 'final_total', name: 'final_total'},
            { data: 'total_paid', name: 'total_paid', "searchable": false},
            { data: 'total_remaining', name: 'total_remaining'},
            { data: 'return_due',visible: false, orderable: false, "searchable": false},
            { data: 'shipping_status', name: 'shipping_status',visible: false},
            { data: 'total_items', name: 'total_items', "searchable": false},
            { data: 'types_of_service_name', name: 'tos.name', @if(empty($is_types_service_enabled)) visible: false @endif},
            { data: 'service_custom_field_1', name: 'service_custom_field_1', @if(empty($is_types_service_enabled)) visible: false @endif},
            { data: 'added_by', name: 'u.first_name'},
            { data: 'additional_notes',visible: false, name: 'additional_notes'},
            { data: 'staff_note',visible: false, name: 'staff_note'},
            { data: 'shipping_details', name: 'shipping_details',visible: false},
            { data: 'table_name', name: 'tables.name', @if(empty($is_tables_enabled)) visible: false @endif },
            { data: 'waiter', name: 'ss.first_name', @if(empty($is_service_staff_enabled)) visible: false @endif },
            { data: 'action', name: 'action', orderable: false, "searchable": false}
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#sales_order_table'));
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var footer_sale_total = 0;
            var footer_total_paid = 0;
            var footer_total_remaining = 0;
            var footer_total_sell_return_due = 0;
            for (var r in data){
                footer_sale_total += $(data[r].final_total).data('orig-value') ? parseFloat($(data[r].final_total).data('orig-value')) : 0;
                footer_total_paid += $(data[r].total_paid).data('orig-value') ? parseFloat($(data[r].total_paid).data('orig-value')) : 0;
                footer_total_remaining += $(data[r].total_remaining).data('orig-value') ? parseFloat($(data[r].total_remaining).data('orig-value')) : 0;
                footer_total_sell_return_due += $(data[r].return_due).find('.sell_return_due').data('orig-value') ? parseFloat($(data[r].return_due).find('.sell_return_due').data('orig-value')) : 0;
            }

            $('.footer_total_sell_return_due').html(__currency_trans_from_en(footer_total_sell_return_due));
            $('.footer_total_remaining').html(__currency_trans_from_en(footer_total_remaining));
            $('.footer_total_paid').html(__currency_trans_from_en(footer_total_paid));
            $('.footer_sale_total').html(__currency_trans_from_en(footer_sale_total));

            $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
            $('.service_type_count').html(__count_status(data, 'types_of_service_name'));
            $('.payment_method_count').html(__count_status(data, 'payment_methods'));
        },
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(6)').attr('class', 'clickable_td');
        },
    });
    if ($('#contact_id').val()) {
        // Hide contact_name and mobile columns if contact_id exists
        sales_order_table.column(2).visible(false); // Index of 'contact_name' column
        sales_order_table.column(3).visible(false); // Index of 'contact_name' column
        // sales_order_table.column(4).visible(false); // Index of 'mobile' column
        // sales_order_table.column(5).visible(false); 
    } else {
        // Show contact_name and mobile columns if contact_id is empty
        sales_order_table.column(2).visible(true);
        sales_order_table.column(3).visible(true);
    }
    $('#only_subscriptions').on('ifChanged', function(event){
        sales_order_table.ajax.reload();
    });


    $('#acount-add-sales-order').click(function(e) {
            e.preventDefault();
            var selectedContactId = $('#contact_id').val(); 
            $.ajax({
                url: '/contact/redirect-sale/' + selectedContactId+'/so', // URL with the contact ID
                type: 'GET',
                success: function(response) {
                    if (response.status == true){
                        toastr.success("Opening");
                        window.location.href = response.redirect_url;
                    } else {
                        toastr.error(response.message);
                        
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("Error occurred: " + error);
                }
            });
        });
    $('#account-add-sell').click(function(e) {
            e.preventDefault();
            var selectedContactId = $('#contact_id').val(); 
            $.ajax({
                url: '/contact/redirect-sale/' + selectedContactId+'/si', // URL with the contact ID
                type: 'GET',
                success: function(response) {
                    if (response.status == true){
                        toastr.success("Opening");
                        window.location.href = response.redirect_url;
                    } else {
                        toastr.error(response.message);
                        
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("Error occurred: " + error);
                }
            });
        });
    
    // Rearrange toolbar: Search LEFT, Buttons RIGHT
    setTimeout(function() {
        // For sell_table
        var sellWrapper = $('#sell_table_wrapper');
        if (sellWrapper.length) {
            var toolbar = sellWrapper.find('.amazon-table-toolbar');
            if (!toolbar.length) {
                // Create toolbar container if DOM option didn't work
                var dtButtons = sellWrapper.find('.dt-buttons');
                var dtFilter = sellWrapper.find('.dataTables_filter');
                if (dtButtons.length && dtFilter.length) {
                    var toolbarHtml = '<div class="amazon-table-toolbar"><div class="toolbar-search"></div><div class="toolbar-buttons"></div></div>';
                    dtButtons.before(toolbarHtml);
                    toolbar = sellWrapper.find('.amazon-table-toolbar');
                    toolbar.find('.toolbar-search').append(dtFilter);
                    toolbar.find('.toolbar-buttons').append(dtButtons);
                }
            }
        }
        
        // For sales_order_table
        var orderWrapper = $('#sales_order_table_wrapper');
        if (orderWrapper.length) {
            var toolbar2 = orderWrapper.find('.amazon-table-toolbar');
            if (!toolbar2.length) {
                var dtButtons2 = orderWrapper.find('.dt-buttons');
                var dtFilter2 = orderWrapper.find('.dataTables_filter');
                if (dtButtons2.length && dtFilter2.length) {
                    var toolbarHtml2 = '<div class="amazon-table-toolbar"><div class="toolbar-search"></div><div class="toolbar-buttons"></div></div>';
                    dtButtons2.before(toolbarHtml2);
                    toolbar2 = orderWrapper.find('.amazon-table-toolbar');
                    toolbar2.find('.toolbar-search').append(dtFilter2);
                    toolbar2.find('.toolbar-buttons').append(dtButtons2);
                }
            }
        }
    }, 500);
});



</script>