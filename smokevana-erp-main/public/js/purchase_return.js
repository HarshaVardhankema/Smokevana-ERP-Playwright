$(document).ready(function() {
    //get suppliers
    var urlParams = new URLSearchParams(window.location.search);
    var cid = urlParams.get('cid');

    if (cid) {
        $.ajax({
            url: '/purchases/get_suppliers_auto',
            dataType: 'json',
            data: { q: cid },
            success: function (data) {
                if (data.length === 1) {
                    var supplier = data[0];
                    var option = new Option(supplier.text, supplier.id, true, true);
                    $('#supplier_id').append(option).trigger('change');
                    setTimeout(function() {
                        setSupplierDetails(supplier);
                    }, 1000);
                }
            }
        });
    }
    $('#supplier_id').select2({
        ajax: {
            url: '/purchases/get_suppliers',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                };
            },
            processResults: function(data) {
                return {
                    results: data,
                };
            },
        },
        minimumInputLength: 1,
        escapeMarkup: function(m) {
            return m;
        },
        templateResult: function(data) {
            if (!data.id) {
                return data.text;
            }
            var html = data.text + ' - ' + data.business_name + ' (' + data.contact_id + ')';
            return html;
        }
    });
    //Add products
    if ($('#search_product_for_purchase_return').length > 0) {
        //Add Product
        $('#search_product_for_purchase_return')
            .autocomplete({
                source: function(request, response) {
                    $.getJSON(
                        '/products/list',
                        { location_id: $('#location_id').val(), term: request.term },
                        response
                    );
                },
                minLength: 2,
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
                        purchase_return_product_row(ui.item.variation_id);
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
    }

    $('select#location_id').change(function() {
        if ($(this).val()) {
            $('#search_product_for_purchase_return').removeAttr('disabled');
        } else {
            $('#search_product_for_purchase_return').attr('disabled', 'disabled');
        }
        $('table#stock_adjustment_product_table tbody').html('');
        $('#product_row_index').val(0);
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

    $('form#purchase_return_form').validate();

    // Prevent "Leave site?" warning if form was successfully submitted
    var isNavigating = false;
    var pendingNavigation = null;
    
    // Intercept link clicks to show SweetAlert
    $(document).on("click", "a", function(e) {
        var href = $(this).attr('href');
        if (href && href !== '#' && href !== 'javascript:void(0)' && !$(this).hasClass('no-confirm')) {
            if ($('form#purchase_return_form').data('modified') && !$('form#purchase_return_form').data('submitted')) {
                e.preventDefault();
                pendingNavigation = href;
                swal({
                    title: LANG.sure || "Are you sure?",
                    text: "Changes you made may not be saved.",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Cancel",
                            value: false,
                            visible: true
                        },
                        confirm: {
                            text: "Leave",
                            value: true,
                            visible: true
                        }
                    },
                    dangerMode: true
                }).then((willLeave) => {
                    if (willLeave) {
                        isNavigating = true;
                        $(window).off('beforeunload');
                        window.location.href = pendingNavigation;
                    }
                    pendingNavigation = null;
                });
                return false;
            }
        }
    });
    
    $(window).on('beforeunload', function(e) {
        if ($('form#purchase_return_form').data('submitted') || isNavigating) {
            return undefined; // Allow navigation without warning
        }
        // Only show warning if form has been modified and not submitted
        if ($('form#purchase_return_form').data('modified') && !$('form#purchase_return_form').data('submitted')) {
            // Modern browsers ignore custom messages, but we still return a string
            return 'Changes you made may not be saved.';
        }
    });

    // Track form modifications
    $('form#purchase_return_form').on('change input', function() {
        $(this).data('modified', true);
    });

    $(document).on('click', 'button#submit_purchase_return_form', function(e) {
        e.preventDefault();

        //Check if product is present or not.
        if ($('table#purchase_return_product_table tbody tr').length <= 0) {
            toastr.warning(LANG.no_products_added);
            $('input#search_product_for_purchase_return').select();
            return false;
        }

        // Debug: Log form data before submission
        console.log('Form validation result:', $('form#purchase_return_form').valid());
        console.log('Number of product rows:', $('table#purchase_return_product_table tbody tr').length);

        if ($('form#purchase_return_form').valid()) {
            console.log('Form is valid, submitting...');
            
            // Additional validation: Ensure products are present
            var productRows = $('table#purchase_return_product_table tbody tr');
            if (productRows.length === 0) {
                toastr.error('No products added to the purchase return');
                return false;
            }
            
            // Validate each product row has required fields
            var hasValidProducts = true;
            productRows.each(function(index) {
                var row = $(this);
                var productId = row.find('input[name="products[' + index + '][product_id]"]').val();
                var quantity = row.find('input[name="products[' + index + '][quantity]"]').val();
                var unitPrice = row.find('input[name="products[' + index + '][unit_price]"]').val();
                
                if (!productId || !quantity || !unitPrice) {
                    console.error('Invalid product row at index ' + index, {
                        productId: productId,
                        quantity: quantity,
                        unitPrice: unitPrice
                    });
                    hasValidProducts = false;
                }
            });
            
            if (!hasValidProducts) {
                toastr.error('Some product rows have missing required fields');
                return false;
            }
            
            // Build form data manually to ensure all fields are included
            var formData = new FormData($('form#purchase_return_form')[0]);
            
            // Manually add products array to ensure it's included
            productRows.each(function(index) {
                var row = $(this);
                
                // Add all product fields manually
                formData.append('products[' + index + '][product_id]', row.find('input[name="products[' + index + '][product_id]"]').val());
                formData.append('products[' + index + '][variation_id]', row.find('input[name="products[' + index + '][variation_id]"]').val());
                formData.append('products[' + index + '][enable_stock]', row.find('input[name="products[' + index + '][enable_stock]"]').val());
                formData.append('products[' + index + '][quantity]', row.find('input[name="products[' + index + '][quantity]"]').val());
                formData.append('products[' + index + '][unit_price]', row.find('input[name="products[' + index + '][unit_price]"]').val());
                formData.append('products[' + index + '][price]', row.find('input[name="products[' + index + '][price]"]').val());
                
                // Add optional fields if they exist
                var lotNumber = row.find('input[name="products[' + index + '][lot_number]"]').val();
                if (lotNumber) {
                    formData.append('products[' + index + '][lot_number]', lotNumber);
                }
                
                var expDate = row.find('input[name="products[' + index + '][exp_date]"]').val();
                if (expDate) {
                    formData.append('products[' + index + '][exp_date]', expDate);
                }
            });
            
            // Debug: Log the manually built form data
            console.log('Manually built form data:');
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Test: Verify products array is included
            var productsFound = false;
            for (var pair of formData.entries()) {
                if (pair[0].startsWith('products[')) {
                    productsFound = true;
                    break;
                }
            }
            
            if (!productsFound) {
                console.error('ERROR: Products array not found in form data!');
                toastr.error('Products not found in form data. Please try again.');
                return false;
            } else {
                console.log('SUCCESS: Products array found in form data');
            }
            
            // Use AJAX submission with FormData
            $.ajax({
                url: $('form#purchase_return_form').attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    // Disable submit button to prevent double submission
                    $('button#submit_purchase_return_form').prop('disabled', true).text('Submitting...');
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.msg);
                        
                        // Mark form as successfully submitted to prevent "Leave site?" warning
                        $('form#purchase_return_form').data('submitted', true);
                        
                        // Remove any beforeunload event handlers
                        $(window).off('beforeunload');
                        
                        // Use a small delay to ensure the success message is shown before redirect
                        // setTimeout(function() {
                            // Use replace instead of href to prevent back button issues
                            window.location.replace('/purchase-return');
                        // }, 1000);
                    } else {
                        toastr.error(response.msg || 'Something went wrong');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    console.log('Falling back to regular form submission...');
                    
                    // Fallback to regular form submission if AJAX fails
                    $('form#purchase_return_form').submit();
                },
                complete: function() {
                    // Re-enable submit button
                    $('button#submit_purchase_return_form').prop('disabled', false).text('Submit');
                }
            });
        } else {
            console.log('Form validation failed');
            console.log('Validation errors:', $('form#purchase_return_form').validate().errorList);
            toastr.error('Please fix the validation errors before submitting');
        }
    });

    $('#tax_id').change(function() {
        update_table_total();
    });

    $('#purchase_return_product_table tbody')
    .find('.expiry_datepicker')
    .each(function() {
        $(this).datepicker({
            autoclose: true,
            format: datepicker_date_format,
        });
    });


});

function purchase_return_product_row(variation_id) {
    var row_index = parseInt($('#product_row_index').val());
    var location_id = $('#location_id').val();
    $.ajax({
        method: 'POST',
        url: '/purchase-return/get_product_row',
        data: { row_index: row_index, variation_id: variation_id, location_id: location_id },
        dataType: 'html',
        success: function(result) {
            $('table#purchase_return_product_table tbody').append(result);
            
            $('table#purchase_return_product_table tbody tr:last').find('.expiry_datepicker').datepicker({
                autoclose: true,
                format: datepicker_date_format,
            });
            
            update_table_total();
            $('#product_row_index').val(row_index + 1);
        },
    });
}

function update_table_total() {
    var table_total = 0;
    $('table#purchase_return_product_table tbody tr').each(function() {
        var this_total = parseFloat(__read_number($(this).find('input.product_line_total')));
        if (this_total) {
            table_total += this_total;
        }
    });
    var tax_rate = parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
    var tax = __calculate_amount('percentage', tax_rate, table_total);
    __write_number($('input#tax_amount'), tax);
    var final_total = table_total + tax;
    $('input#total_amount').val(final_total);
    $('span#total_return').text(__number_f(final_total));
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

function get_stock_adjustment_details(rowData) {
    var div = $('<div/>')
        .addClass('loading')
        .text('Loading...');
    $.ajax({
        url: '/stock-adjustments/' + rowData.DT_RowId,
        dataType: 'html',
        success: function(data) {
            div.html(data).removeClass('loading');
        },
    });

    return div;
}
