$(document).ready(function() {
    $('table#product_table tbody').find('.label-date-picker').each( function(){
        $(this).datepicker({
            autoclose: true
        });
    });
    // Add products - product search with autocomplete (search, click to add, search bar stays below products)
    if ($('#search_product_for_label').length > 0) {
        var url = (typeof base_path !== 'undefined' ? base_path : '') + '/purchases/get_products?check_enable_stock=false';
        $('#search_product_for_label')
            .autocomplete({
                source: function(request, response) {
                    $.getJSON(
                        url,
                        {
                            location_id: $('#location_id').length ? $('#location_id').val() : '',
                            term: request.term,
                            search_fields: ['sku', 'name', 'sub_sku', 'var_barcode_no'],
                            isParent: false
                        },
                        response
                    );
                },
                minLength: 2,
                delay: 300,
                appendTo: 'body',
                response: function(event, ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];
                        $(this)
                            .data('ui-autocomplete')
                            ._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');
                    } else if (ui.content.length == 0) {
                        if (typeof swal !== 'undefined' && typeof LANG !== 'undefined' && LANG.no_products_found) {
                            swal(LANG.no_products_found);
                        } else if (typeof toastr !== 'undefined') {
                            toastr.warning('No products found');
                        }
                    }
                },
                select: function(event, ui) {
                    $(this).val('');
                    get_label_product_row(ui.item.product_id, ui.item.variation_id);
                },
            })
            .autocomplete('instance')._renderItem = function(ul, item) {
                return $('<li>')
                    .append('<div>' + item.text + '</div>')
                    .appendTo(ul);
            };
    }

    $('input#is_show_price').change(function() {
        if ($(this).is(':checked')) {
            $('div#price_type_div').show();
        } else {
            $('div#price_type_div').hide();
        }
    });

    $('button#labels_print').click(function() {
        
        //validation
        $('.printQty').each(function() {
            var val = parseInt($(this).val(), 10);
            if (isNaN(val) || val < 1) {
                $(this).val(1); 
            }
        });
        
        if ($('form#preview_setting_form table#product_table tbody tr').length > 0) {
            // Load labels via AJAX and print directly (like invoice printing)
            $.ajax({
                method: 'GET',
                url: base_path + '/labels/preview',
                data: $('form#preview_setting_form').serialize(),
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        // Extract body content from full HTML document
                        var tempDiv = $('<div>').html(result.html);
                        var bodyContent = tempDiv.find('body').html() || result.html;
                        
                        // Put labels in receipt_section (same as invoice printing)
                        $('#receipt_section').html(bodyContent);
                        
                        // Store original title
                        var originalTitle = document.title;
                        document.title = 'Print Labels';
                        
                        // Print using the same method as invoices
                        __print_receipt('receipt_section');
                        
                        // Restore title after printing
                        setTimeout(function() {
                            document.title = originalTitle;
                            $('#receipt_section').html('');
                        }, 1200);
                    } else {
                        toastr.error(result.msg || LANG.label_no_product_error);
                    }
                },
                error: function() {
                    toastr.error(LANG.label_no_product_error);
                }
            });
        } else {
            swal(LANG.label_no_product_error).then(value => {
                $('#search_product_for_label').focus();
            });
        }
    });

    $(document).on('click', 'button#print_label', function() {
        window.print();
    });

    $(document).on('focus', '.label-date-picker', function() {
        $('.label-date-picker').not(this).datepicker('hide');
        $(this).datepicker('show');
    });
});

function get_label_product_row(product_id, variation_id) {
    if (product_id) {
        var row_count = $('table#product_table tbody tr').length;
        $.ajax({
            method: 'GET',
            url: (typeof base_path !== 'undefined' ? base_path : '') + '/labels/add-product-row',
            dataType: 'html',
            data: { product_id: product_id, row_count: row_count, variation_id: variation_id },
            success: function(result) {
                $('table#product_table tbody').append(result);

                $('table#product_table tbody').find('.label-date-picker').each( function(){
                    $(this).datepicker({
                        autoclose: true
                    });
                });
            },
        });
    }
}
