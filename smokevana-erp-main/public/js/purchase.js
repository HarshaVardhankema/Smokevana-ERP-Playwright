$(document).ready(function () {
    if ($('input#iraqi_selling_price_adjustment').length > 0) {
        iraqi_selling_price_adjustment = true;
    } else {
        iraqi_selling_price_adjustment = false;
    }

    //Date picker
    $('#transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });

    $('#delivery_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });

    var urlParams = new URLSearchParams(window.location.search);
    var cid = urlParams.get('cid');
    var poid = urlParams.get('po');
    if (cid) {
        // Fetch supplier details on page load when cid is present
        $.ajax({
            url: '/purchases/get_suppliers_auto',
            dataType: 'json',
            data: { q: cid },
            success: function (data) {
                if (data.length === 1) {
                    var supplier = data[0];
                    var option = new Option(supplier.text, supplier.id, true, true);
                    $('#supplier_id').append(option).trigger('change');
                    // add delay to setSupplierDetails
                    setTimeout(function () {
                        setSupplierDetails(supplier);
                    }, 1000);
                }
            }
        });
    }

    $('#supplier_id').select2({
        ajax: {
            url: cid ? '/purchases/get_suppliers_auto' : '/purchases/get_suppliers',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: cid ? cid : params.term, // Use cid if present, otherwise use input term
                    page: params.page,
                    location_id: $('#location_id').val(),
                };
            },
            processResults: function (data) {
                return { results: data };
            },
        },
        minimumInputLength: 1,
        escapeMarkup: function (m) {
            return m;
        },
        templateResult: function (data) {
            if (!data.id) {
                return data.text;
            }
            return data.text + ' - ' + data.business_name + ' (' + data.contact_id + ')';
        },
        language: {
            noResults: function () {
                var name = $('#supplier_id').data('select2').dropdown.$search.val();
                return (
                    '<button type="button" data-name="' +
                    name +
                    '" class="btn btn-link add_new_supplier"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' +
                    __translate('add_name_as_new_supplier', { name: name }) +
                    '</button>'
                );
            },
        },
    }).on('select2:select', function (e) {
        var data = e.params.data;
        // add delay to setSupplierDetails
        setTimeout(function () {
            $('#location_id').val(data.location_id).trigger('change').prop('readonly', true);
            setSupplierDetails(data);
        }, 1000);

    });
    function setSupplierDetails(data) {
        $('#pay_term_number').val(data.pay_term_number);
        $('#pay_term_type').val(data.pay_term_type);
        $('#advance_balance_text').text(__currency_trans_from_en(data.balance), true);
        $('#advance_balance').val(data.balance);
        set_supplier_address(data);
        if (poid) {
            $.ajax({
                url: '/get-purchase-order-lines/' + poid + '?row_count=0',
                dataType: 'json',
                success: function (data) {
                    set_purchase_order_discount(data);
                    var option = new Option(data.po.text, data.po.id, true, true);
                    $('#purchase_order_ids').val(data.po.id).trigger('change');
                    set_po_values(data.po);
                    append_purchase_lines(data.html, $('#row_count').val());
                    $('.additional_notes').val(data.po.additional_notes)
                }
            });
        }
    }
    //Quick add supplier
    $(document).on('click', '.add_new_supplier', function () {
        $('#supplier_id').select2('close');
        var name = $(this).data('name');
        $('.contact_modal')
            .find('input#name')
            .val(name);
        $('.contact_modal')
            .find('select#contact_type')
            .val('supplier')
            .closest('div.contact_type_div')
            .addClass('hide');
        $('.contact_modal').modal('show');
    });

    $('form#quick_add_contact')
        .submit(function (e) {
            e.preventDefault();
        })
        .validate({
            rules: {
                contact_id: {
                    remote: {
                        url: '/contacts/check-contacts-id',
                        type: 'post',
                        data: {
                            contact_id: function () {
                                return $('#contact_id').val();
                            },
                            hidden_id: function () {
                                if ($('#hidden_id').length) {
                                    return $('#hidden_id').val();
                                } else {
                                    return '';
                                }
                            },
                        },
                    },
                },
            },
            messages: {
                contact_id: {
                    remote: LANG.contact_id_already_exists,
                },
            },
            submitHandler: function (form) {
                $.ajax({
                    method: 'POST',
                    url: base_path + '/check-mobile',
                    dataType: 'json',
                    data: {
                        contact_id: function () {
                            return $('#hidden_id').val();
                        },
                        mobile_number: function () {
                            return $('#mobile').val();
                        },
                    },
                    beforeSend: function (xhr) {
                        __disable_submit_button($(form).find('button[type="submit"]'));
                    },
                    success: function (result) {
                        if (result.is_mobile_exists == true) {
                            swal({
                                title: LANG.sure,
                                text: result.msg,
                                icon: 'warning',
                                buttons: true,
                                dangerMode: true,
                            }).then(willContinue => {
                                if (willContinue) {
                                    submitQuickAddPurchaseContactForm(form);
                                } else {
                                    $('#mobile').select();
                                }
                            });

                        } else {
                            submitQuickAddPurchaseContactForm(form);
                        }
                    },
                });
            },
        });
    $('.contact_modal').on('hidden.bs.modal', function () {
        $('form#quick_add_contact')
            .find('button[type="submit"]')
            .removeAttr('disabled');
        $('form#quick_add_contact')[0].reset();
    });

    //Add products
    var search_fields = [];
    $('#configure_search_modal input[name="search_fields[]"]').on('ifChanged', function () {
        search_fields = [];
        $('#configure_search_modal input[name="search_fields[]"]:checked').each(function () {
            search_fields.push($(this).val());
        });
    });
    $('#configure_search_modal input[name="search_fields[]"]:checked').each(function () {
        search_fields.push($(this).val());
    });

    if ($('#search_product').length > 0) {
        $('#search_product')
            .autocomplete({
                source: function (request, response) {
                    var isParent = $('#toggle_switch').prop('checked') ? true : false;
                    var url = '/purchases/get_products';
                    $.getJSON(
                        url,
                        { location_id: $('#location_id').val(), term: request.term, search_fields: search_fields, isParent: isParent, check_enable_stock: false },
                        response
                    );
                },
                minLength: 4,
                delay: 1000,
                response: function (event, ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];
                        $(this)
                            .data('ui-autocomplete')
                            ._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');
                    } else if (ui.content.length == 0) {
                        var term = $(this).data('ui-autocomplete').term;
                        toastr.error('No products found');
                    }
                },
                select: function (event, ui) {
                    let is_metrix = $('#toggle_switch').prop('checked');
                    if (is_metrix) {
                        event.preventDefault();
                        let productId = ui.item.id;
                        let priceGroupId = $('#hidden_price_group').val();
                        let modalUrl = `/sells/pos/getmatrixproduct/${productId}/${priceGroupId}`;

                        $.ajax({
                            url: modalUrl,
                            data: {
                                is_purchase: true
                            },
                            dataType: 'html',
                            success: function (result1) {
                                let result = JSON.parse(result1)
                                if (result.status) {
                                    $('.view_modal').html(result.html).modal('show');
                                } else {
                                    toastr.warning(result.message);
                                }
                            }
                        });
                        return false;
                    }
                    $(this).val(null);
                    get_purchase_entry_row(ui.item.product_id, ui.item.variation_id);
                },
            })
            .autocomplete('instance')._renderItem = function (ul, item) {
                return $('<li>')
                    .append('<div>' + item.text + '</div>')
                    .appendTo(ul);
            };
    }

    // Barcode scanner support on Purchase search
    (function initPurchaseBarcodeScanner() {
        if (!$('#search_product').length) { return; }

        function scanAndAddToPurchase(scannedCode) {
            const is_metrix = $('#toggle_switch').prop('checked');
            const isParent = is_metrix ? true : false;
            const url = '/purchases/get_products';
            const search_fields = $('.search_fields:checked').map(function () { return $(this).val(); }).get();

            $.getJSON(url, {
                location_id: $('#location_id').val(),
                term: scannedCode,
                search_fields: search_fields,
                isParent: isParent,
                check_enable_stock: false
            }, function (items) {
                if (!items || items.length === 0) {
                    toastr.error('No products found');
                    $('#search_product').val('').focus();
                    return;
                }

                const code = String(scannedCode).trim();
                const exact = items.filter(function (it) {
                    return (
                        String(it.sub_sku || '') === code ||
                        String(it.sku || '') === code ||
                        String(it.var_barcode_no || '') === code ||
                        String(it.barcode || '') === code
                    );
                });

                const chosen = exact.length === 1 ? exact[0] : (items.length === 1 ? items[0] : null);

                if (chosen) {
                    if (is_metrix) {
                        const productId = chosen.id || chosen.product_id;
                        const priceGroupId = $('#hidden_price_group').val();
                        const modalUrl = `/sells/pos/getmatrixproduct/${productId}/${priceGroupId}`;
                        $.ajax({
                            url: modalUrl,
                            data: { is_purchase: true },
                            dataType: 'html',
                            success: function (result1) {
                                let result;
                                try { result = JSON.parse(result1); } catch (e) { result = { status: false, message: 'Invalid response' }; }
                                if (result.status) {
                                    $('.view_modal').html(result.html).modal('show');
                                } else {
                                    toastr.warning(result.message || 'Unable to open matrix modal');
                                }
                            }
                        });
                    } else {
                        get_purchase_entry_row(chosen.product_id, chosen.variation_id);
                    }
                    $('#search_product').val('').focus();
                } else {
                    // Multiple matches without exact match; trigger autocomplete for user selection
                    $('#search_product').val(scannedCode).focus();
                    if ($('#search_product').autocomplete) { $('#search_product').autocomplete('search'); }
                }
            });
        }

        try {
            onScan.attachTo(document, {
                suffixKeyCodes: [13],
                reactToPaste: true,
                minLength: 3,
                onScan: function (sCode) {
                    // Skip when any modal is open to prevent conflicts
                    if ($('.modal.show').length) { return; }
                    const active = document.activeElement;
                    if ($(active).is('input,textarea,[contenteditable=true]') && !$(active).is('#search_product')) { return; }
                    scanAndAddToPurchase(sCode);
                },
                onScanError: function () { }
            });
        } catch (e) {
            // Fallback: use Enter on search input
            $('#search_product').on('keypress', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    const val = $(this).val().trim();
                    if (val) { scanAndAddToPurchase(val); }
                }
            });
        }
    })();


    $(document).on('click', '#save_button_metrix', async function () {
        var variationIds = [];
        var quantities = [];
        var product_id = $('#matrix_product_id').val()
        $('table.bg-gray tbody tr[data-variation-id]').each(function () {
            var variationId = $(this).data('variation-id');
            var quantity = parseInt($(this).find('.quantity-input').val()) || 0;
            if (quantity > 0) {
                variationIds.push(variationId);
                quantities.push(quantity);
            }
        });
        if (variationIds.length > 0) {
            pos_Matrix_row(variationIds.join(','), quantities.join(','), product_id);
        }
    });

    function pos_Matrix_row(variation_ids, quantities, product_id) {
        // Ensure arrays
        if (typeof variation_ids === 'string') {
            variation_ids = variation_ids.split(',');
        }
        if (typeof quantities === 'string') {
            quantities = quantities.split(',').map(q => parseFloat(q));
        }

        // Filter valid variations and quantities
        let new_variation_ids = [];
        let new_quantities = [];

        $.each(variation_ids, function (index, variation_id) {
            let quantity = quantities[index];
            if (quantity && quantity > 0) {
                new_variation_ids.push(variation_id);
                new_quantities.push(quantity);
            }
        });

        if (new_variation_ids.length === 0) {
            $('.modal').modal('hide');
            return;
        }

        let product_row = $('#row_count').val();
        let location_id = $('#location_id').val();
        let supplier_id = $('#supplier_id').val();

        // Ensure location_id is available
        if (!location_id) {
            console.error('Location ID is required but not found');
            toastr.error('Location is required. Please select a location first.');
            $('.modal').modal('hide');
            return;
        }

        let data = {
            product_id: product_id,
            product_row: product_row,
            quantities: new_quantities.join(','),
            supplier_id: supplier_id,
            location_id: location_id
        };

        if ($('#is_purchase_order').length) {
            data.is_purchase_order = true;
        }

        // Additional validation before making AJAX call
        if (!new_variation_ids || new_variation_ids.length === 0) {
            console.error('No valid variation IDs found');
            toastr.error('No valid products selected.');
            $('.modal').modal('hide');
            return;
        }

        $.ajax({
            method: 'GET',
            url: '/purchases/get_purchase_entry_row/metrix/' + new_variation_ids.join(',') + '/' + location_id,
            data: data,
            dataType: 'json',
            success: function (result) {
                if (result.success) {
                    if (result.rows && result.rows.length > 0) {
                        $.each(result.rows, function (index, rowData) {
                            if (rowData.success) {
                                append_purchase_lines(rowData.html_content, index)
                            }
                        });
                    } else if (result.html_content) {
                        append_purchase_lines(result.html_content, 0)
                    }

                    $('.modal').modal('hide');
                    $('#search_product').focus();
                } else {
                    toastr.error(result.msg || 'Failed to add matrix product.');
                    $('#search_product').focus();
                }
            },
            error: function (xhr) {
                console.error('AJAX Error:', xhr);
                let errorMessage = 'Something went wrong. Please try again.';
                
                if (xhr.status === 404) {
                    errorMessage = 'Route not found. Please check if location is selected and try again.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        // If response is not JSON, use default message
                    }
                }
                
                toastr.error(errorMessage);
                $('.modal').modal('hide');
            }
        });
    }


    $(document).on('click', '.remove_purchase_entry_row', function () {
        $(this)
            .closest('tr')
            .remove();
        update_table_total();
        update_grand_total();
        update_table_sr_number();
        // }
        toastr.success('Row removed successfully');
        // });
    });

    //On Change of quantity
    $(document).on('input', '.purchase_quantity', function () {
        var row = $(this).closest('tr');
        var quantity = __read_number($(this), true);
        var purchase_before_tax = __read_number(row.find('input.purchase_unit_cost'), true);
        var purchase_after_tax = __read_number(
            row.find('input.purchase_unit_cost_after_tax'),
            true
        );

        //Calculate sub totals
        var sub_total_before_tax = quantity * purchase_before_tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.row_subtotal_before_tax').text(
            __currency_trans_from_en(sub_total_before_tax, false, true)
        );
        __write_number(
            row.find('input.row_subtotal_before_tax_hidden'),
            sub_total_before_tax,
            true
        );

        row.find('.row_subtotal_after_tax').text(
            __currency_trans_from_en(sub_total_after_tax, false, true)
        );
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);

        update_table_total();
        update_grand_total();
    });

    $(document).on('input', '.purchase_unit_cost_without_discount', function () {
        var purchase_before_discount = __read_number($(this), true);

        var row = $(this).closest('tr');
        var discount_percent = __read_number(row.find('input.inline_discounts'), true);
        var discount_type = row.find('select.inline_discounts').val();
        var quantity = __read_number(row.find('input.purchase_quantity'), true);

        if (discount_type == 'fixed') {
            row.find('input.inline_discounts').prop('max', purchase_before_discount);
        } else {
            row.find('input.inline_discounts').prop('max', 100);
        }

        //Calculations.
        var purchase_before_tax =
            parseFloat(purchase_before_discount) -
            __calculate_amount(discount_type, discount_percent, purchase_before_discount);

        __write_number(row.find('input.purchase_unit_cost'), purchase_before_tax, true);

        var sub_total_before_tax = quantity * purchase_before_tax;

        //Tax
        var tax_rate = parseFloat(
            row
                .find('select.purchase_line_tax_id')
                .find(':selected')
                .data('tax_amount')
        );
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        var purchase_after_tax = purchase_before_tax + tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.row_subtotal_before_tax').text(
            __currency_trans_from_en(sub_total_before_tax, false, true)
        );
        __write_number(
            row.find('input.row_subtotal_before_tax_hidden'),
            sub_total_before_tax,
            true
        );

        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, true);
        row.find('.row_subtotal_after_tax').text(
            __currency_trans_from_en(sub_total_after_tax, false, true)
        );
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);

        row.find('.purchase_product_unit_tax_text').text(
            __currency_trans_from_en(tax, false, true)
        );
        __write_number(row.find('input.purchase_product_unit_tax'), tax, true);

        update_inline_profit_percentage(row);
        update_table_total();
        update_grand_total();
    });

    function calculate_discount(row) {
        var discount_input = row.find('input.inline_discounts');
        var discount_select = row.find('select.inline_discounts');
        var discount_type = discount_select.val();
        var discount_value = __read_number(discount_input, true);
        var quantity = __read_number(row.find('input.purchase_quantity'), true);
        var purchase_before_discount = __read_number(
            row.find('input.purchase_unit_cost_without_discount'),
            true
        );
        if (discount_type == 'fixed') {
            discount_input.prop('max', purchase_before_discount);
        } else {
            discount_input.prop('max', 100);
        }

        //Calculations.
        var purchase_before_tax;
        if (discount_type === 'percent' || discount_type === 'percentage') {
            // For percentage discount
            purchase_before_tax = parseFloat(purchase_before_discount) -
                __calculate_amount('percentage', discount_value, purchase_before_discount);
        } else {
            // For fixed amount discount fixed
            purchase_before_tax = parseFloat(purchase_before_discount) - discount_value;
        }

        __write_number(row.find('input.purchase_unit_cost'), purchase_before_tax, true);

        var sub_total_before_tax = quantity * purchase_before_tax;

        //Tax
        var tax_rate = parseFloat(
            row
                .find('select.purchase_line_tax_id')
                .find(':selected')
                .data('tax_amount')
        );
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        var purchase_after_tax = purchase_before_tax + tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.row_subtotal_before_tax').text(
            __currency_trans_from_en(sub_total_before_tax, false, true)
        );
        __write_number(
            row.find('input.row_subtotal_before_tax_hidden'),
            sub_total_before_tax,
            true
        );

        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, true);
        row.find('.row_subtotal_after_tax').text(
            __currency_trans_from_en(sub_total_after_tax, false, true)
        );
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);
        row.find('.purchase_product_unit_tax_text').text(
            __currency_trans_from_en(tax, false, true)
        );
        __write_number(row.find('input.purchase_product_unit_tax'), tax, true);

        update_inline_profit_percentage(row);
        update_table_total();
        update_grand_total();
    }

    $(document).on('input', 'input.inline_discounts', function () {
        var row = $(this).closest('tr');
        calculate_discount(row);
    });

    $(document).on('change', 'select.inline_discounts', function () {
        var row = $(this).closest('tr');
        calculate_discount(row);
    });

    $(document).on('change', '.purchase_unit_cost', function () {
        var row = $(this).closest('tr');
        var quantity = __read_number(row.find('input.purchase_quantity'), true);
        var purchase_before_tax = __read_number($(this), true);

        var sub_total_before_tax = quantity * purchase_before_tax;

        //Update unit cost price before discount
        var discount_percent = __read_number(row.find('input.inline_discounts'), true);
        var purchase_before_discount = __get_principle(purchase_before_tax, discount_percent, true);
        __write_number(
            row.find('input.purchase_unit_cost_without_discount'),
            purchase_before_discount,
            true
        );

        //Tax
        var tax_rate = parseFloat(
            row
                .find('select.purchase_line_tax_id')
                .find(':selected')
                .data('tax_amount')
        );
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        var purchase_after_tax = purchase_before_tax + tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.row_subtotal_before_tax').text(
            __currency_trans_from_en(sub_total_before_tax, false, true)
        );
        __write_number(
            row.find('input.row_subtotal_before_tax_hidden'),
            sub_total_before_tax,
            true
        );

        row.find('.purchase_product_unit_tax_text').text(
            __currency_trans_from_en(tax, false, true)
        );
        __write_number(row.find('input.purchase_product_unit_tax'), tax, true);

        //row.find('.purchase_product_unit_tax_text').text( tax );
        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, true);
        row.find('.row_subtotal_after_tax').text(
            __currency_trans_from_en(sub_total_after_tax, false, true)
        );
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);

        update_inline_profit_percentage(row);
        update_table_total();
        update_grand_total();
    });

    $(document).on('change', 'select.purchase_line_tax_id', function () {
        var row = $(this).closest('tr');
        var purchase_before_tax = __read_number(row.find('.purchase_unit_cost'), true);
        var quantity = __read_number(row.find('input.purchase_quantity'), true);

        //Tax
        var tax_rate = parseFloat(
            $(this)
                .find(':selected')
                .data('tax_amount')
        );
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        //Purchase price
        var purchase_after_tax = purchase_before_tax + tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.purchase_product_unit_tax_text').text(
            __currency_trans_from_en(tax, false, true)
        );
        __write_number(row.find('input.purchase_product_unit_tax'), tax, true);

        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, true);

        row.find('.row_subtotal_after_tax').text(
            __currency_trans_from_en(sub_total_after_tax, false, true)
        );
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);

        update_table_total();
        update_grand_total();
    });

    $(document).on('change', '.purchase_unit_cost_after_tax', function () {
        var row = $(this).closest('tr');
        var purchase_after_tax = __read_number($(this), true);
        var quantity = __read_number(row.find('input.purchase_quantity'), true);

        var sub_total_after_tax = purchase_after_tax * quantity;

        //Tax
        var tax_rate = parseFloat(
            row
                .find('select.purchase_line_tax_id')
                .find(':selected')
                .data('tax_amount')
        );
        var purchase_before_tax = __get_principle(purchase_after_tax, tax_rate);
        var sub_total_before_tax = quantity * purchase_before_tax;
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        //Update unit cost price before discount
        var discount_percent = __read_number(row.find('input.inline_discounts'), true);
        var purchase_before_discount = __get_principle(purchase_before_tax, discount_percent, true);
        __write_number(
            row.find('input.purchase_unit_cost_without_discount'),
            purchase_before_discount,
            true
        );

        row.find('.row_subtotal_after_tax').text(
            __currency_trans_from_en(sub_total_after_tax, false, true)
        );
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);

        __write_number(row.find('.purchase_unit_cost'), purchase_before_tax, true);

        row.find('.row_subtotal_before_tax').text(
            __currency_trans_from_en(sub_total_before_tax, false, true)
        );
        __write_number(
            row.find('input.row_subtotal_before_tax_hidden'),
            sub_total_before_tax,
            true
        );

        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(tax, true, true));
        __write_number(row.find('input.purchase_product_unit_tax'), tax);

        update_table_total();
        update_grand_total();
    });

    $('#tax_id, #discount_type, #discount_amount, input#shipping_charges, \
        #additional_expense_value_1, #additional_expense_value_2, \
        #additional_expense_value_3, #additional_expense_value_4').change(function () {
        update_grand_total();
    });

    //Purchase table
    purchase_table = $('#purchase_table').DataTable({
        processing: true,
        language: { processing: `<div id="main_loader"><span class='loader'></span></div>` },
        serverSide: true,
        fixedHeader: false,
        scrollY: "75vh",
        scrollX: true,
        scrollCollapse: false,
        ajax: {
            url: '/purchases',
            data: function (d) {
                if ($('#purchase_list_filter_location_id').length) {
                    d.location_id = $('#purchase_list_filter_location_id').val();
                }
                if ($('#purchase_list_filter_supplier_id').length) {
                    d.supplier_id = $('#purchase_list_filter_supplier_id').val();
                }
                if ($('#purchase_list_filter_payment_status').length) {
                    d.payment_status = $('#purchase_list_filter_payment_status').val();
                }
                if ($('#purchase_list_filter_status').length) {
                    d.status = $('#purchase_list_filter_status').val();
                }

                var start = '';
                var end = '';
                if ($('#purchase_list_filter_date_range').val()) {
                    start = $('input#purchase_list_filter_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    end = $('input#purchase_list_filter_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;

                d = __datatable_ajax_callback(d);
            },
        },
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'location_name', name: 'BS.name' },
            { data: 'name', name: 'name', searchable: true },
            { data: 'status', name: 'status' },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'final_total', name: 'final_total' },
            { data: 'payment_due', name: 'payment_due', orderable: false, searchable: false },
            { data: 'added_by', name: 'u.first_name' },
        ],
        fnDrawCallback: function (oSettings) {
            __currency_convert_recursively($('#purchase_table'));
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

        "footerCallback": function (row, data, start, end, display) {
            var total_purchase = 0;
            var total_due = 0;
            var total_purchase_return_due = 0;
            for (var r in data) {
                total_purchase += $(data[r].final_total).data('orig-value') ?
                    parseFloat($(data[r].final_total).data('orig-value')) : 0;
                var payment_due_obj = $('<div>' + data[r].payment_due + '</div>');
                total_due += payment_due_obj.find('.payment_due').data('orig-value') ?
                    parseFloat(payment_due_obj.find('.payment_due').data('orig-value')) : 0;

                total_purchase_return_due += payment_due_obj.find('.purchase_return').data('orig-value') ?
                    parseFloat(payment_due_obj.find('.purchase_return').data('orig-value')) : 0;
            }

            $('.footer_purchase_total').html(__currency_trans_from_en(total_purchase));
            $('.footer_total_due').html(__currency_trans_from_en(total_due));
            $('.footer_total_purchase_return_due').html(__currency_trans_from_en(total_purchase_return_due));
            $('.footer_status_count').html(__count_status(data, 'status'));
            $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
        },
        createdRow: function (row, data, dataIndex) {
            $(row)
                .find('td:eq(5)')
                .attr('class', 'clickable_td');
        },
    });

    // Purchase Order Table
    purchase_order_table = $('#purchase_order_table').DataTable({
        processing: true,
        language: { processing: `<div id="main_loader"><span class='loader'></span></div>` },
        serverSide: true,
        fixedHeader: false,
        scrollY: "75vh",
        scrollX: true,
        scrollCollapse: false,
        ajax: {
            url: '/purchase-order',
            data: function (d) {
                d.location_id = $('#purchase_list_filter_location_id').val();
                d.supplier_id = $('#purchase_list_filter_supplier_id').val();
                d.payment_status = $('#purchase_list_filter_payment_status').val();
                d.status = $('#purchase_list_filter_status').val();

                var start = '';
                var end = '';
                if ($('#purchase_list_filter_date_range').val()) {
                    start = $('input#purchase_list_filter_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    end = $('input#purchase_list_filter_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;

                d = __datatable_ajax_callback(d);
            },
        },
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'location_name', name: 'BS.name' },
            { data: 'name', name: 'name', searchable: true },
            { data: 'status', name: 'status' },
            { data: 'final_total', name: 'final_total' },
            // { data: 'payment_due', name: 'payment_due', orderable: false, searchable: false },
            { data: 'added_by', name: 'u.first_name' },
        ],
        fnDrawCallback: function (oSettings) {
            __currency_convert_recursively($('#purchase_order_table'));
        },
        buttons: [

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
        // "footerCallback": function ( row, data, start, end, display ) {
        //     var total_purchase = 0;
        //     var total_due = 0;
        //     var total_purchase_return_due = 0;
        //     for (var r in data){
        //         total_purchase += $(data[r].final_total).data('orig-value') ?
        //         parseFloat($(data[r].final_total).data('orig-value')) : 0;
        //         var payment_due_obj = $('<div>' + data[r].payment_due + '</div>');
        //         total_due += payment_due_obj.find('.payment_due').data('orig-value') ? 
        //         parseFloat(payment_due_obj.find('.payment_due').data('orig-value')) : 0;

        //         total_purchase_return_due += payment_due_obj.find('.purchase_return').data('orig-value') ?
        //         parseFloat(payment_due_obj.find('.purchase_return').data('orig-value')) : 0;
        //     }

        //     $('.footer_purchase_total').html(__currency_trans_from_en(total_purchase));
        //     $('.footer_total_due').html(__currency_trans_from_en(total_due));
        //     $('.footer_total_purchase_return_due').html(__currency_trans_from_en(total_purchase_return_due));
        //     $('.footer_status_count').html(__count_status(data, 'status'));
        //     $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
        // },
        // createdRow: function(row, data, dataIndex) {
        //     $(row)
        //         .find('td:eq(5)')
        //         .attr('class', 'clickable_td');
        // },
    });

    // Purchase Return Table
    purchase_return_table = $('#purchase_return_table').DataTable({
        processing: true,
        language: { processing: `<div id="main_loader"><span class='loader'></span></div>` },
        serverSide: true,
        fixedHeader: false,
        scrollY: "75vh",
        scrollX: true,
        scrollCollapse: false,
        ajax: {
            url: '/purchase-return',
            data: function (d) {
                d.location_id = $('#purchase_list_filter_location_id').val();
                d.supplier_id = $('#purchase_list_filter_supplier_id').val();
                d.payment_status = $('#purchase_list_filter_payment_status').val();
                d.status = $('#purchase_list_filter_status').val();

                var start = '';
                var end = '';
                if ($('#purchase_list_filter_date_range').val()) {
                    start = $('input#purchase_list_filter_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    end = $('input#purchase_list_filter_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;

                d = __datatable_ajax_callback(d);
            },
        },
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'location_name', name: 'BS.name' },
            { data: 'name', name: 'name', searchable: true },
            { data: 'status', name: 'status' },
            { data: 'final_total', name: 'final_total' },
            { data: 'payment_due', name: 'payment_due', orderable: false, searchable: false },
            { data: 'added_by', name: 'u.first_name' },
        ],
        fnDrawCallback: function (oSettings) {
            __currency_convert_recursively($('#purchase_return_table'));
        },
        buttons: [
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
        // "footerCallback": function ( row, data, start, end, display ) {
        //     var total_purchase = 0;
        //     var total_due = 0;
        //     var total_purchase_return_due = 0;
        //     for (var r in data){
        //         total_purchase += $(data[r].final_total).data('orig-value') ?
        //         parseFloat($(data[r].final_total).data('orig-value')) : 0;
        //         var payment_due_obj = $('<div>' + data[r].payment_due + '</div>');
        //         total_due += payment_due_obj.find('.payment_due').data('orig-value') ? 
        //         parseFloat(payment_due_obj.find('.payment_due').data('orig-value')) : 0;

        //         total_purchase_return_due += payment_due_obj.find('.purchase_return').data('orig-value') ?
        //         parseFloat(payment_due_obj.find('.purchase_return').data('orig-value')) : 0;
        //     }

        //     $('.footer_purchase_total').html(__currency_trans_from_en(total_purchase));
        //     $('.footer_total_due').html(__currency_trans_from_en(total_due));
        //     $('.footer_total_purchase_return_due').html(__currency_trans_from_en(total_purchase_return_due));
        //     $('.footer_status_count').html(__count_status(data, 'status'));
        //     $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
        // },
        // createdRow: function(row, data, dataIndex) { 
        //     $(row)
        //         .find('td:eq(5)')
        //         .attr('class', 'clickable_td');
        // },
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr('href');
        if (target === '#purchases_tab') {
            if (typeof purchase_table !== 'undefined') purchase_table.columns.adjust().draw();
        } else if (target === '#purchase_return_tab') {
            if (typeof purchase_return_table !== 'undefined') purchase_return_table.columns.adjust().draw();
        } else if (target === '#purchase_order_tab') {
            if (typeof purchase_order_table !== 'undefined') purchase_order_table.columns.adjust().draw();
        } else if (target === '#sales_tab') {
            if (typeof sales_table !== 'undefined') sales_table.columns.adjust().draw();
        } else if (target === '#sales_order_tab') {
            if (typeof sales_order_table !== 'undefined') sales_order_table.columns.adjust().draw();
        }
    });

    $(document).on(
        'change',
        '#purchase_list_filter_location_id, \
                    #purchase_list_filter_supplier_id, #purchase_list_filter_payment_status,\
                     #purchase_list_filter_status',
        function () {
            purchase_table.ajax.reload();
        }
    );

    update_table_sr_number();

    $(document).on('change', '.mfg_date', function () {
        var this_date = $(this).val();
        var this_moment = moment(this_date, moment_date_format);
        var expiry_period = parseFloat(
            $(this)
                .closest('td')
                .find('.row_product_expiry')
                .val()
        );
        var expiry_period_type = $(this)
            .closest('td')
            .find('.row_product_expiry_type')
            .val();
        if (this_date) {
            if (expiry_period && expiry_period_type) {
                exp_date = this_moment
                    .add(expiry_period, expiry_period_type)
                    .format(moment_date_format);
                $(this)
                    .closest('td')
                    .find('.exp_date')
                    .datepicker('update', exp_date);
            } else {
                $(this)
                    .closest('td')
                    .find('.exp_date')
                    .datepicker('update', '');
            }
        } else {
            $(this)
                .closest('td')
                .find('.exp_date')
                .datepicker('update', '');
        }
    });

    $('#purchase_entry_table tbody')
        .find('.expiry_datepicker')
        .each(function () {
            $(this).datepicker({
                autoclose: true,
                format: datepicker_date_format,
            });
        });

    $(document).on('change', '.profit_percent', function () {
        var row = $(this).closest('tr');
        var profit_percent = __read_number($(this), true);

        var purchase_unit_cost = __read_number(row.find('input.purchase_unit_cost_after_tax'), true);
        var default_sell_price =
            parseFloat(purchase_unit_cost) +
            __calculate_amount('percentage', profit_percent, purchase_unit_cost);
        var exchange_rate = $('input#exchange_rate').val();
        __write_number(
            row.find('input.default_sell_price'),
            default_sell_price * exchange_rate,
            true
        );
    });

    $(document).on('change', '.default_sell_price', function () {
        var row = $(this).closest('tr');
        update_inline_profit_percentage(row);
    });

    $(document).on('click', 'a.delete-purchase', function (e) {
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
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            purchase_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

    $('table#purchase_entry_table').on('change', 'select.sub_unit', function () {
        var tr = $(this).closest('tr');
        var base_unit_cost = tr.find('input.base_unit_cost').val();
        var base_unit_selling_price = tr.find('input.base_unit_selling_price').val();

        var multiplier = parseFloat(
            $(this)
                .find(':selected')
                .data('multiplier')
        );

        var unit_sp = base_unit_selling_price * multiplier;
        var unit_cost = base_unit_cost * multiplier;

        var sp_element = tr.find('input.default_sell_price');
        __write_number(sp_element, unit_sp);

        var cp_element = tr.find('input.purchase_unit_cost_without_discount');
        __write_number(cp_element, unit_cost);
        cp_element.change();
    });
    toggle_search();
});

function get_purchase_entry_row(product_id, variation_id) {
    if (product_id) {
        // Check if the variation already exists in the table
        // var existing_row = $('#purchase_entry_table tbody tr').filter(function() {
        //     return $(this).find('input.hidden_variation_id').val() == variation_id;
        // });

        // if (existing_row.length > 0) {
        //     // If found, increase quantity instead of adding new row
        //     var qty_input = existing_row.find('input.purchase_quantity');
        //     var current_qty = __read_number(qty_input);
        //     __write_number(qty_input, current_qty + 1);
        //     qty_input.trigger('change'); // Trigger change to recalculate totals
        //     return;
        // }

        var row_count = $('#row_count').val();
        var location_id = $('#location_id').val();
        var supplier_id = $('#supplier_id').val();

        var data = {
            product_id: product_id,
            row_count: row_count,
            variation_id: variation_id,
            location_id: location_id,
            supplier_id: supplier_id
        };

        if ($('#is_purchase_order').length) {
            data.is_purchase_order = true;
        }

        $.ajax({
            method: 'POST',
            url: '/purchases/get_purchase_entry_row',
            dataType: 'html',
            data: data,
            success: function (result) {
                append_purchase_lines(result, row_count);
            },
        });
    }
}


function append_purchase_lines(data, row_count, trigger_change = false) {
    $(data)
        .find('.purchase_quantity')
        .each(function () {
            row = $(this).closest('tr');

            $('#purchase_entry_table tbody').append(
                update_purchase_entry_row_values(row)
            );
            update_row_price_for_exchange_rate(row);

            update_inline_profit_percentage(row);

            update_table_total();
            update_grand_total();
            update_table_sr_number();

            //Check if multipler is present then multiply it when a new row is added.
            if (__getUnitMultiplier(row) > 1) {
                row.find('select.sub_unit').trigger('change');
            }

            if (trigger_change && row.find('.purchase_unit_cost_without_discount').length) {
                row.find('.purchase_unit_cost_without_discount').trigger('change');
            }

            // Init select2 on tax dropdown with dropdownParent to prevent clipping in scrollable table
            row.find('select.purchase_line_tax_id').each(function () {
                if (!$(this).data('select2')) {
                    $(this).select2({ dropdownParent: $('body') });
                }
            });
        });
    if ($(data).find('.purchase_quantity').length) {
        $('#row_count').val(
            $(data).find('.purchase_quantity').length + parseInt(row_count)
        );
    }
}

function update_purchase_entry_row_values(row) {
    if (typeof row != 'undefined') {
        var quantity = __read_number(row.find('.purchase_quantity'), true);
        var unit_cost_price = __read_number(row.find('.purchase_unit_cost'), true);
        var row_subtotal_before_tax = quantity * unit_cost_price;

        var tax_rate = parseFloat(
            $('option:selected', row.find('.purchase_line_tax_id')).attr('data-tax_amount')
        );

        var unit_product_tax = __calculate_amount('percentage', tax_rate, unit_cost_price);

        var unit_cost_price_after_tax = unit_cost_price + unit_product_tax;
        var row_subtotal_after_tax = quantity * unit_cost_price_after_tax;

        row.find('.row_subtotal_before_tax').text(
            __currency_trans_from_en(row_subtotal_before_tax, false, true)
        );
        __write_number(row.find('.row_subtotal_before_tax_hidden'), row_subtotal_before_tax, true);
        __write_number(row.find('.purchase_product_unit_tax'), unit_product_tax, true);
        row.find('.purchase_product_unit_tax_text').text(
            __currency_trans_from_en(unit_product_tax, false, true)
        );
        row.find('.purchase_unit_cost_after_tax').text(
            __currency_trans_from_en(unit_cost_price_after_tax, true)
        );
        row.find('.row_subtotal_after_tax').text(
            __currency_trans_from_en(row_subtotal_after_tax, false, true)
        );
        __write_number(row.find('.row_subtotal_after_tax_hidden'), row_subtotal_after_tax, true);

        row.find('.expiry_datepicker').each(function () {
            $(this).datepicker({
                autoclose: true,
                format: datepicker_date_format,
            });
        });
        return row;
    }
}

function update_row_price_for_exchange_rate(row) {
    var exchange_rate = $('input#exchange_rate').val();

    if (exchange_rate == 1) {
        return true;
    }

    var purchase_unit_cost_without_discount =
        __read_number(row.find('.purchase_unit_cost_without_discount'), true) / exchange_rate;
    __write_number(
        row.find('.purchase_unit_cost_without_discount'),
        purchase_unit_cost_without_discount,
        true
    );

    var purchase_unit_cost = __read_number(row.find('.purchase_unit_cost'), true) / exchange_rate;
    __write_number(row.find('.purchase_unit_cost'), purchase_unit_cost, true);

    var row_subtotal_before_tax_hidden =
        __read_number(row.find('.row_subtotal_before_tax_hidden'), true) / exchange_rate;
    row.find('.row_subtotal_before_tax').text(
        __currency_trans_from_en(row_subtotal_before_tax_hidden, false, true)
    );
    __write_number(
        row.find('input.row_subtotal_before_tax_hidden'),
        row_subtotal_before_tax_hidden,
        true
    );

    var purchase_product_unit_tax =
        __read_number(row.find('.purchase_product_unit_tax'), true) / exchange_rate;
    __write_number(row.find('input.purchase_product_unit_tax'), purchase_product_unit_tax, true);
    row.find('.purchase_product_unit_tax_text').text(
        __currency_trans_from_en(purchase_product_unit_tax, false, true)
    );

    var purchase_unit_cost_after_tax =
        __read_number(row.find('.purchase_unit_cost_after_tax'), true) / exchange_rate;
    __write_number(
        row.find('input.purchase_unit_cost_after_tax'),
        purchase_unit_cost_after_tax,
        true
    );

    var row_subtotal_after_tax_hidden =
        __read_number(row.find('.row_subtotal_after_tax_hidden'), true) / exchange_rate;
    __write_number(
        row.find('input.row_subtotal_after_tax_hidden'),
        row_subtotal_after_tax_hidden,
        true
    );
    row.find('.row_subtotal_after_tax').text(
        __currency_trans_from_en(row_subtotal_after_tax_hidden, false, true)
    );
}

function iraqi_dinnar_selling_price_adjustment(row) {
    var default_sell_price = __read_number(row.find('input.default_sell_price'), true);

    //Adjsustment
    var remaining = default_sell_price % 250;
    if (remaining >= 125) {
        default_sell_price += 250 - remaining;
    } else {
        default_sell_price -= remaining;
    }

    __write_number(row.find('input.default_sell_price'), default_sell_price, true);

    update_inline_profit_percentage(row);
}

function update_inline_profit_percentage(row) {
    //Update Profit percentage
    var default_sell_price = __read_number(row.find('input.default_sell_price'), true);
    var exchange_rate = $('input#exchange_rate').val();
    default_sell_price_in_base_currency = default_sell_price / parseFloat(exchange_rate);

    var purchase_after_tax = __read_number(row.find('input.purchase_unit_cost_after_tax'), true);
    var profit_percent = __get_rate(purchase_after_tax, default_sell_price_in_base_currency);
    __write_number(row.find('input.profit_percent'), profit_percent, true);
}

function update_table_total() {
    var total_quantity = 0;
    var total_st_before_tax = 0;
    var total_subtotal = 0;

    $('#purchase_entry_table tbody')
        .find('tr')
        .each(function () {
            total_quantity += __read_number($(this).find('.purchase_quantity'), true);
            total_st_before_tax += __read_number(
                $(this).find('.row_subtotal_before_tax_hidden'),
                true
            );
            total_subtotal += __read_number($(this).find('.row_subtotal_after_tax_hidden'), true);
        });

    $('#total_quantity').text(__number_f(total_quantity, false));
    $('#total_st_before_tax').text(__currency_trans_from_en(total_st_before_tax, true, true));
    __write_number($('input#st_before_tax_input'), total_st_before_tax, true);

    $('#total_subtotal').text(__currency_trans_from_en(total_subtotal, true, true));
    __write_number($('input#total_subtotal_input'), total_subtotal, true);
}

function update_grand_total() {
    var st_before_tax = __read_number($('input#st_before_tax_input'), true);
    var total_subtotal = __read_number($('input#total_subtotal_input'), true);

    //Calculate Discount
    var discount_type = $('select#discount_type').val();
    var discount_amount = __read_number($('input#discount_amount'), true);
    var discount = __calculate_amount(discount_type, discount_amount, total_subtotal);
    $('#discount_calculated_amount').text(__currency_trans_from_en(discount, true, true));

    if (discount_type == "fixed") {
        $("input[name='discount_amount']").prop("max", total_subtotal);
    } else if (discount_type == "percentage") {
        $("input[name='discount_amount']").prop("max", 100);
    }

    //Calculate Tax
    var tax_rate = parseFloat($('option:selected', $('#tax_id')).data('tax_amount'));
    var tax = __calculate_amount('percentage', tax_rate, total_subtotal - discount);
    __write_number($('input#tax_amount'), tax);
    $('#tax_calculated_amount').text(__currency_trans_from_en(tax, true, true));

    //Calculate shipping
    var shipping_charges = __read_number($('input#shipping_charges'), true);

    //calculate additional expenses
    var additional_expense_1 = __read_number($('input#additional_expense_value_1'), true);
    var additional_expense_2 = __read_number($('input#additional_expense_value_2'), true);
    var additional_expense_3 = __read_number($('input#additional_expense_value_3'), true);
    var additional_expense_4 = __read_number($('input#additional_expense_value_4'), true);

    //Calculate Final total
    grand_total = total_subtotal - discount + tax + shipping_charges +
        additional_expense_1 + additional_expense_2 + additional_expense_3 + additional_expense_4;

    __write_number($('input#grand_total_hidden'), grand_total, true);

    var payment = __read_number($('input.payment-amount'), true);

    var due = grand_total - payment;
    // __write_number($('input.payment-amount'), grand_total, true);

    $('#grand_total').text(__currency_trans_from_en(grand_total, true, true));

    $('#payment_due').text(__currency_trans_from_en(due, true, true));

    //__currency_convert_recursively($(document));
    $('form#add_purchase_form').validate().resetForm();
}
$(document).on('change', 'input.payment-amount', function () {
    var payment = __read_number($(this), true);
    var grand_total = __read_number($('input#grand_total_hidden'), true);
    var bal = grand_total - payment;
    $('#payment_due').text(__currency_trans_from_en(bal, true, true));
});

function update_table_sr_number() {
    var sr_number = 1;
    $('table#purchase_entry_table tbody')
        .find('.sr_number')
        .each(function () {
            $(this).text(sr_number);
            sr_number++;
        });
}
$(document).on('click', 'button#submit_purchase_form, button#submit_purchase_draft', function (e) {
    e.preventDefault();

    //Check if product is present or not.

    if ($('table#purchase_entry_table tbody tr').length <= 0) {
        toastr.warning(LANG.no_products_added);
        $('input#search_product').select();
        return false;
    }
    var anyLineAvaileble = false;
    $('table#purchase_entry_table tbody tr').each(function () {
        if ($(this).find('.purchase_quantity').val() > 0) {
            anyLineAvaileble = true;
        }
    });
    if (anyLineAvaileble == false) {
        toastr.warning('Please add at least one line item');
        return false;
    }

    $('form#add_purchase_form').validate({
        rules: {
            ref_no: {
                remote: {
                    url: '/purchases/check_ref_number',
                    type: 'post',
                    data: {
                        ref_no: function () {
                            return $('#ref_no').val();
                        },
                        contact_id: function () {
                            return $('#supplier_id').val();
                        },
                        purchase_id: function () {
                            if ($('#purchase_id').length > 0) {
                                return $('#purchase_id').val();
                            } else {
                                return '';
                            }
                        },
                    },
                    complete: function (xhr, response) {
                        // If the response is the string "false", treat it as invalid
                        console.log(response)
                        console.log(xhr.responseText);
                        let enable = xhr.responseText === "false" ? false : true;
                        if (enable === false) {
                            $('#submit_purchase_form').prop('disabled', false);
                        }
                    }
                },
            },
        },
        messages: {
            ref_no: {
                remote: LANG.ref_no_already_exists,
            },
        },
        invalidHandler: function (event, validator) {
            $('#submit_purchase_form').prop('disabled', false);
            $('button#submit_purchase_draft').prop('disabled', false);

        }
    });

    // Disable the submit button initially
    if ($('form#add_purchase_form').valid()) {
        $('#submit_purchase_form').prop('disabled', true);
        $('button#submit_purchase_draft').prop('disabled', true);
    }

    var payment_types_dropdown = $('.payment_types_dropdown')
    var payment_type = payment_types_dropdown.val();
    var payment_row = payment_types_dropdown.closest('.payment_row');
    amount_element = payment_row.find('.payment-amount');
    account_dropdown = payment_row.find('.account-dropdown');
    if (payment_type == 'advance') {
        max_value = $('#advance_balance').val();
        msg = $('#advance_balance').data('error-msg');
        amount_element.rules('add', {
            'max-value': max_value,
            messages: {
                'max-value': msg,
            },
        });
        if (account_dropdown) {
            account_dropdown.prop('disabled', true);
        }

    } else {
        amount_element.rules("remove", "max-value");
        if (account_dropdown) {
            account_dropdown.prop('disabled', false);
        }
    }

    if ($('.enable_cash_denomination_for_payment_methods').length) {
        var payment_row = $('.enable_cash_denomination_for_payment_methods').closest('.payment_row');
        var is_valid = true;
        var payment_type = payment_row.find('.payment_types_dropdown').val();
        var denomination_for_payment_types = JSON.parse($('.enable_cash_denomination_for_payment_methods').val());
        if (denomination_for_payment_types.includes(payment_type) && payment_row.find('.is_strict').length && payment_row.find('.is_strict').val() === '1') {
            var payment_amount = __read_number(payment_row.find('.payment-amount'));
            var total_denomination = payment_row.find('input.denomination_total_amount').val();
            if (payment_amount != total_denomination) {
                is_valid = false;
            }
        }

        if (!is_valid) {
            payment_row.find('.cash_denomination_error').removeClass('hide');
            toastr.error(payment_row.find('.cash_denomination_error').text());
            e.preventDefault();
            return false;
        } else {
            payment_row.find('.cash_denomination_error').addClass('hide');
        }
    }

    if ($('form#add_purchase_form').valid()) {
        $(this).attr('disabled', true);

        // Check if the clicked button is submit_purchase_draft
        if ($(this).attr('id') === 'submit_purchase_draft') {
            // Add a hidden input field for save_mode
            $('<input>').attr({
                type: 'hidden',
                name: 'save_mode',
                value: 'quotation'
            }).appendTo('form#add_purchase_form');
        }

        $('form#add_purchase_form').submit();
    }
});

function toggle_search() {
    var hasLocation = $('#location_id').val();
    var hasVendor = $('#supplier_id').val();
    if (hasLocation || hasVendor) {
        $('#search_product').removeAttr('disabled');
        if (hasLocation) {
            $('#search_product').focus();
        }
    } else {
        $('#search_product').attr('disabled', true);
    }
}

$(document).on('change', '#location_id', function () {
    get_purchase_requisitions();
    toggle_search();
    $('#purchase_entry_table tbody').html('');
    update_table_total();
    update_grand_total();
    update_table_sr_number();
});

$(document).on('shown.bs.modal', '.quick_add_product_modal', function () {
    var selected_location = $('#location_id').val();
    if (selected_location) {
        $('.quick_add_product_modal').find('#product_locations').val([selected_location]).trigger("change");
    }
});

function set_supplier_address(data) {
    var address = [];
    // if (data.supplier_business_name) {
    //     address.push(data.supplier_business_name);
    // }
    // if (data.business_name) {
    //     address.push(data.business_name);
    // }
    // if (data.name) {
    //     address.push('<br>' + data.name);
    // }
    // if (data.text) {
    //     address.push('<br>' + data.text);
    // }
    if (data.address_line_1) {
        address.push(data.address_line_1);
    }
    if (data.address_line_2) {
        address.push('<br>' + data.address_line_2);
    }
    if (data.city) {
        address.push('<br>' + data.city);
    }
    if (data.state) {
        address.push(data.state);
    }
    if (data.country) {
        address.push(data.country);
    }
    if (data.zip_code) {
        address.push('<br>' + data.zip_code);
    }
    var supplier_address = address.join(', ');
    $('#supplier_address_div').html(supplier_address);
}

$(document).on('change', '#supplier_id', function () {
    toggle_search();
    if ($('#purchase_order_ids').length) {
        contact_id = $(this).val();
        $.ajax({
            url: '/get-purchase-orders/' + contact_id,
            dataType: 'json',
            success: function (data) {
                $('#purchase_order_ids').select2('destroy').empty().select2({ data: data });
                $('#purchase_entry_table tbody').find('tr').each(function () {
                    if (typeof ($(this).data('purchase_order_id')) !== 'undefined') {
                        $(this).remove();
                    }
                });
            },
        });
    }
});

$("#purchase_order_ids").on("select2:select", function (e) {
    var purchase_order_id = e.params.data.id;
    var row_count = $('#row_count').val();
    $.ajax({
        url: '/get-purchase-order-lines/' + purchase_order_id + '?row_count=' + row_count,
        dataType: 'json',
        success: function (data) {
            set_po_values(data.po);
            append_purchase_lines(data.html, row_count);
            set_purchase_order_discount(data);
            $('.additional_notes').val(function (index, currentValue) {
                let additional_notes = "";
                if (data.po.additional_notes != null) {
                    additional_notes = data.po.additional_notes;
                    return currentValue + data.po.ref_no + ":- " + additional_notes + " ,";
                } else {
                    return currentValue;
                }
            });
        },
    });

});

function set_purchase_order_discount(data) {
    let discount_amount = parseFloat(data.po.discount_amount);
    let discount_type = data.po.discount_type;
    let final_discount = 0;
    let total_discount = parseFloat($('#purchase_order_discount_calculated_amount').data('final-value'));
    let line_final_discount = 0;

    if (discount_type == 'fixed') {
        total_discount += discount_amount;
        line_final_discount = discount_amount;
        final_discount = __currency_trans_from_en(discount_amount);
        discount_amount = "$ " + discount_amount.toFixed(2);
    } else {
        total_discount += parseFloat(parseFloat(data.po.total_before_tax) - parseFloat(data.po.final_total));
        line_final_discount = parseFloat(data.po.total_before_tax) - parseFloat(data.po.final_total);
        final_discount = __currency_trans_from_en(parseFloat(data.po.total_before_tax) - parseFloat(data.po.final_total));
        discount_amount = discount_amount + " %";
    }

    $('#purchase_order_discount_calculated_amount').data('final-value', total_discount);
    $('#purchase_order_discount_calculated_amount').text(__currency_trans_from_en(total_discount));
    let purchase_order_id = data.po.ref_no;
    let tbody_html = `<tr id="purchase_order_discount_row_${data.po.id}">
                        <td>${purchase_order_id}</td>
                        <td>${discount_type}</td>
                        <td>${discount_amount}</td>
                        <td class="line_final_discount" data-line-final-value="${line_final_discount}">${final_discount}</td>
                    </tr>`;
    $('#purchase_order_discount_table tbody').append(tbody_html);

}

$("#purchase_order_ids").on("select2:unselect", function (e) {
    var purchase_order_id = e.params.data.id;

    let line_final_discount = $('#purchase_order_discount_row_' + purchase_order_id).find('.line_final_discount').data('line-final-value');
    let total_discount = parseFloat($('#purchase_order_discount_calculated_amount').data('final-value')) - parseFloat(line_final_discount);

    $('#purchase_order_discount_calculated_amount').data('final-value', total_discount);
    $('#purchase_order_discount_calculated_amount').text(__currency_trans_from_en(total_discount));

    $('#purchase_order_discount_row_' + purchase_order_id).remove();
    $('#purchase_entry_table tbody').find('tr').each(function () {
        if (typeof ($(this).data('purchase_order_id')) !== 'undefined'
            && $(this).data('purchase_order_id') == purchase_order_id) {
            $(this).remove();
        }
    });
});

function set_po_values(po) {
    $('#shipping_details').val(po.shipping_details);
    $('#shipping_charges').val(__number_f(po.shipping_charges));
    if ($('#shipping_custom_field_1').length) {
        $('#shipping_custom_field_1').val(po.shipping_custom_field_1);
    }
    if ($('#shipping_custom_field_2').length) {
        $('#shipping_custom_field_2').val(po.shipping_custom_field_2);
    }
    if ($('#shipping_custom_field_3').length) {
        $('#shipping_custom_field_3').val(po.shipping_custom_field_3);
    }
    if ($('#shipping_custom_field_4').length) {
        $('#shipping_custom_field_4').val(po.shipping_custom_field_4);
    }
    if ($('#shipping_custom_field_5').length) {
        $('#shipping_custom_field_5').val(po.shipping_custom_field_5);
    }

    $('#additional_expense_key_1').val(po.additional_expense_key_1);
    $('#additional_expense_key_2').val(po.additional_expense_key_2);
    $('#additional_expense_key_3').val(po.additional_expense_key_3);
    $('#additional_expense_key_4').val(po.additional_expense_key_4);

    $('#additional_expense_value_1').val(__number_f(po.additional_expense_value_1));
    $('#additional_expense_value_2').val(__number_f(po.additional_expense_value_2));
    $('#additional_expense_value_3').val(__number_f(po.additional_expense_value_3));
    $('#additional_expense_value_4').val(__number_f(po.additional_expense_value_4));
}

if ($("div#import_product_dz").length) {
    $("div#import_product_dz").dropzone({
        url: base_path + '/import-purchase-products',
        paramName: 'file',
        autoProcessQueue: false,
        addRemoveLinks: true,
        uploadMultiple: false,
        maxFiles: 1,
        init: function () {
            this.on("addedfile", function (file) {
                if ($('#location_id').val() == '') {
                    this.removeFile(file);
                    toastr.error('select location first');
                }
            });
            this.on("maxfilesexceeded", function (file) {
                this.removeAllFiles();
                this.addFile(file);
            });
            this.on("sending", function (file, xhr, formData) {
                formData.append("location_id", $('#location_id').val());
                formData.append("row_count", $('#row_count').val());
            });
        },
        acceptedFiles: '.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (file, response) {
            if (response.success) {
                toastr.success(response.msg);
                var row_count = $('#row_count').val();
                append_purchase_lines(response.html, row_count, true);

                this.removeAllFiles();

                $('#import_purchase_products_modal').modal('hide');
            } else {
                toastr.error(response.msg);
            }
        },
    });
}

$(document).on('click', '#import_purchase_products', function () {
    var productDz = Dropzone.forElement("#import_product_dz");
    productDz.processQueue();
})

function submitQuickAddPurchaseContactForm(form) {
    var data = $(form).serialize();
    $.ajax({
        method: 'POST',
        url: $(form).attr('action'),
        dataType: 'json',
        data: data,
        beforeSend: function (xhr) {
            __disable_submit_button($(form).find('button[type="submit"]'));
        },
        success: function (result) {
            if (result.success == true) {
                var name = result.data.name;

                if (result.data.supplier_business_name) {
                    name += result.data.supplier_business_name;
                }
                $('select#supplier_id').append(
                    $('<option>', { value: result.data.id, text: name })
                );
                $('select#supplier_id')
                    .val(result.data.id)
                    .trigger('change');
                $('div.contact_modal').modal('hide');
                set_supplier_address(result.data);
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
}

function get_purchase_requisitions() {
    var location_id = $('#location_id').val();
    if ($('#purchase_requisition_ids').length) {
        if (location_id !== '') {
            $.ajax({
                url: '/get-purchase-requisitions/' + location_id,
                dataType: 'json',
                success: function (data) {
                    $('#purchase_requisition_ids').select2('destroy').empty().select2({ data: data });
                    $('#purchase_entry_table tbody').find('tr').each(function () {
                        if (typeof ($(this).data('purchase_requisition_id')) !== 'undefined') {
                            $(this).remove();
                        }
                    });
                },
            });
        }
    }
}

$("#purchase_requisition_ids").on("select2:select", function (e) {
    var purchase_requisition_id = e.params.data.id;
    var row_count = $('#row_count').val();
    $.ajax({
        url: '/get-purchase-requisition-lines/' + purchase_requisition_id + '?row_count=' + row_count,
        dataType: 'json',
        success: function (data) {
            append_purchase_lines(data.html, row_count);
        },
    });

});

$("#purchase_requisition_ids").on("select2:unselect", function (e) {
    var purchase_requisition_id = e.params.data.id;
    $('#purchase_entry_table tbody').find('tr').each(function () {
        if (typeof ($(this).data('purchase_requisition_id')) !== 'undefined'
            && $(this).data('purchase_requisition_id') == purchase_requisition_id) {
            $(this).remove();
        }
    });
});

function scrollTableToBottom() {
    const $container = $('.table-responsive');
    $container.stop().animate({
        scrollTop: $container[0].scrollHeight
    }, 2000);
}
$("select[name='discount_type'], input[name='discount_amount']").on("change", function () {
    var discount_type = $("select[name='discount_type']").val();
    var total_amount = __read_number($('input#total_subtotal_input'), true);
    if (discount_type == "fixed") {
        $("input[name='discount_amount']").prop("max", total_amount);
    } else if (discount_type == "percentage") {
        $("input[name='discount_amount']").prop("max", 100);
    }
});
$("input[name='pay_term_number'],select[name='pay_term_type']").on("change", function () {
    var pay_term_number = $("input[name='pay_term_number']").val();
    var pay_term_type = $("select[name='pay_term_type']").val();

    // Check if pay_term_number has a value (not null, not empty, and greater than 0)
    var has_pay_term_number = pay_term_number && pay_term_number.trim() !== '' && parseFloat(pay_term_number) > 0;

    // Check if pay_term_type has a value
    var has_pay_term_type = pay_term_type && pay_term_type !== '';

    // If pay term number has value, make pay term type required
    if (has_pay_term_number) {
        $("select[name='pay_term_type']").prop("required", true);
    } else {
        $("select[name='pay_term_type']").prop("required", false);
    }

    // If pay term type has value, make pay term number required
    if (has_pay_term_type) {
        $("input[name='pay_term_number']").prop("required", true);
    } else {
        $("input[name='pay_term_number']").prop("required", false);
    }
});

function combineRows() {
    var variationMap = {};
    var rowsToRemove = [];

    // First pass: identify duplicates and prepare for combination
    $('#purchase_entry_table tbody tr').each(function () {
        var $row = $(this);
        var variation_id = $row.find('input.hidden_variation_id').val();

        // Skip rows with no variation_id or zero quantity
        if (!variation_id) {
            return;
        }

        var qty_element = $row.find('input.purchase_quantity');
        var quantity = __read_number(qty_element, true);

        // Skip rows with zero quantity
        if (quantity <= 0) {
            return;
        }

        if (variationMap[variation_id]) {
            // Add to the existing quantity in the original row
            var existing_row = variationMap[variation_id];
            var existing_qty_element = existing_row.find('input.purchase_quantity');
            var existing_qty = __read_number(existing_qty_element, true);
            __write_number(existing_qty_element, existing_qty + quantity, true);
            existing_qty_element.val(existing_qty + quantity);

            // Mark this row for removal
            rowsToRemove.push($row);
        } else {
            // Store the first occurrence of this variation_id
            variationMap[variation_id] = $row;
        }
    });

    // Second pass: remove duplicate rows
    $.each(rowsToRemove, function (index, $row) {
        $row.remove();
    });

    // Third pass: trigger change events on remaining rows to recalculate totals
    $.each(variationMap, function (variation_id, $row) {
        var qty_element = $row.find('input.purchase_quantity');
        qty_element.trigger('change');
        update_row_price_for_exchange_rate($row);
    });

    // Update totals once after all processing is complete
    update_table_total();
    update_grand_total();
    update_table_sr_number();

    // Show success message
    if (rowsToRemove.length > 0) {
        toastr.success('Successfully combined ' + rowsToRemove.length + ' duplicate rows');
    } else {
        toastr.info('No duplicate rows found to combine');
    }
}
$(document).on('click focus', '.input_number', function () {
    $(this).select();
});
$('#combine_button').on('click', function () {
    combineRows();
});
