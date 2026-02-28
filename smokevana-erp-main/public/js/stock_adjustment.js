$(document).ready(function() {
    //Add products
    if ($('#search_product_for_srock_adjustment').length > 0) {
        var $searchInput = $('#search_product_for_srock_adjustment');

        //Add Product
        $searchInput
            .autocomplete({
                source: function(request, response) {
                    var location_id = $('#location_id').val();
                    if (!location_id) {
                        toastr.warning(LANG.select_location);
                        return response([]);
                    }
                    $.getJSON('/products/list', { location_id: location_id, term: request.term }, response);
                },
                // show suggestions immediately on focus/click
                minLength: 0,
                response: function(event, ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];
                        if (ui.item.qty_available > 0 && ui.item.enable_stock == 1) {
                            $(this)
                                .data('ui-autocomplete')
                                ._trigger('select', 'autocompleteselect', ui);
                            $(this).autocomplete('close');
                        }
                    } else if (ui.content.length == 0) {
                        swal(LANG.no_products_found);
                    }
                },
                focus: function(event, ui) {
                    if (ui.item.qty_available <= 0) {
                        return false;
                    }
                },
                select: function(event, ui) {
                    if (ui.item.qty_available > 0) {
                        $(this).val(null);
                        stock_adjustment_product_row(ui.item.variation_id);
                    } else {
                        alert(LANG.out_of_stock);
                    }
                },
            })
            .autocomplete('instance')._renderItem = function(ul, item) {
            if (item.qty_available <= 0) {
                var string = '<li class="ui-state-disabled">' + item.name;
                if (item.type == 'variable') {
                    string += '-' + item.variation;
                }
                string += ' (' + item.sub_sku + ') (Out of stock) </li>';
                return $(string).appendTo(ul);
            } else if (item.enable_stock != 1) {
                return ul;
            } else {
                var string = '<div>' + item.name;
                if (item.type == 'variable') {
                    string += '-' + item.variation;
                }
                string += ' (' + item.sub_sku + ') </div>';
                return $('<li>')
                    .append(string)
                    .appendTo(ul);
            }
        };

        // trigger search when input gets focus so user sees products right away
        $searchInput.on('focus', function () {
            if (!$('#location_id').val()) {
                toastr.warning(LANG.select_location);
                return;
            }
            $(this).autocomplete('search', $(this).val() || '');
        });
    }

    $('select#location_id').change(function() {
        // when location changes, clear current rows and totals (keep search row)
        $('table#stock_adjustment_product_table tbody').find('tr.product_row').remove();
        $('#product_row_index').val(0);
        update_table_total();
        if ($('#search_product_for_srock_adjustment').length > 0) {
            $('#search_product_for_srock_adjustment').val('');
        }
    });

    $(document).on('change', 'input.product_quantity', function() {
        update_table_row($(this).closest('tr'));
    });
    $(document).on('change', 'input.product_unit_price', function() {
        update_table_row($(this).closest('tr'));
    });

    $(document).on('click', '.remove_product_row', function() {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                $(this)
                    .closest('tr')
                    .remove();
                update_table_total();
            }
        });
    });

    //Date picker
    $('#transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });

    // $('form#stock_adjustment_form').validate();

    stock_adjustment_table = $('#stock_adjustment_table').DataTable({
        processing: true,
        language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
        serverSide: true,
        fixedHeader:false,
        scrollY:'60vh',
        scrollX: true,

        ajax: '/stock-adjustments',
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false,
            },
        ],
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'action', name: 'action' },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'location_name', name: 'BL.name' },
            { data: 'adjustment_type', name: 'adjustment_type' },
            { data: 'final_total', name: 'final_total' },
            { data: 'total_amount_recovered', name: 'total_amount_recovered' },
            { data: 'additional_notes', name: 'additional_notes' },
            { data: 'added_by', name: 'u.first_name' },
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#stock_adjustment_table'));
        },
    });
    var detailRows = [];

    $(document).on('click', 'button.delete_stock_adjustment', function() {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            toastr.success(result.msg);
                            stock_adjustment_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
});

// function stock_adjustment_product_row(variation_id) {
//     var row_index = parseInt($('#product_row_index').val());
//     var location_id = $('select#location_id').val();
//     $.ajax({
//         method: 'POST',
//         url: '/stock-adjustments/get_product_row',
//         data: { row_index: row_index, variation_id: variation_id, location_id: location_id },
//         dataType: 'html',
//         success: function(result) {
//             $('table#stock_adjustment_product_table tbody').append(result);
//             update_table_total();
//             $('#product_row_index').val(row_index + 1);
//         },
//     });
// }
function stock_adjustment_product_row(variation_id) {
    var found = false;

    // Check if variation already exists in table
    $('table#stock_adjustment_product_table tbody tr.product_row').each(function () {
        var row = $(this);
        var existing_variation_id = row.find('input.variation_id').val();

        if (parseInt(existing_variation_id) === parseInt(variation_id)) {
            // Found matching variation, increase quantity
            var qty_input = row.find('input.product_quantity');
            var current_qty = parseFloat(__read_number(qty_input));
            var new_qty = current_qty + 1;
            __write_number(qty_input, new_qty);
            update_table_row(row);
            found = true;
            return false; // break loop
        }
    });

    if (!found) {
        var row_index = parseInt($('#product_row_index').val());
        var location_id = $('select#location_id').val();
        $.ajax({
            method: 'POST',
            url: '/stock-adjustments/get_product_row',
            data: { row_index: row_index, variation_id: variation_id, location_id: location_id },
            dataType: 'html',
            success: function (result) {
                $('table#stock_adjustment_product_table tbody').append(result);
                update_table_total();
                $('#product_row_index').val(row_index + 1);
            },
        });
    }
}

function update_table_total() {
    var table_total = 0;
    $('table#stock_adjustment_product_table tbody tr.product_row').each(function() {
        var this_total = parseFloat(__read_number($(this).find('input.product_line_total')));
        if (this_total) {
            table_total += this_total;
        }
    });
    $('input#total_amount').val(table_total);
    $('span#total_adjustment').text(__number_f(table_total));
}

function update_table_row(tr) {
    var quantity = parseFloat(__read_number(tr.find('input.product_quantity')));
    var unit_price = parseFloat(__read_number(tr.find('input.product_unit_price')));
    var row_total = 0;
    if (quantity && unit_price) {
        row_total = quantity * unit_price;
    }
    tr.find('input.product_line_total').val(__number_f(row_total));
    update_table_total();
}
$('#save_stock_adjustment').on('click',function(e) {
    e.preventDefault();

    if ($('table#stock_adjustment_product_table tbody').find('.product_row').length <= 0) {
        toastr.warning(LANG.no_products_added);
        return false;
    }
    if ($('form#stock_adjustment_form').valid()) {
        $('#stock_adjustment_form').submit();
    } else {
        return false;
    }
});
$(document).on('shown.bs.modal', '.view_modal', function() {
    __currency_convert_recursively($('.view_modal'));
});
