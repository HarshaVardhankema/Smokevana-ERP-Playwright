//This file contains all functions used products tab

$(document).ready(function () {
    $('#state_check').on('change', function() {
        var value = $(this).val();
        if (value === 'in' || value === 'not_in') {
            $('#states_selection_div').slideDown();
            if (value === 'in') {
                $('#state_help_text').text('Product will only be available in selected states');
            } else {
                $('#state_help_text').text('Product will be excluded from selected states');
            }
        } else {
            $('#states_selection_div').slideUp();
            $('#states').val(null).trigger('change');
        }
    });
    // Initialize galleryFiles as a global variable
    window.galleryFiles = [];

    $(document).on('ifChecked', 'input#enable_stock', function () {
        $('div#alert_quantity_div').show();
        $('div#quick_product_opening_stock_div').show();

        //Enable expiry selection
        if ($('#expiry_period_type').length) {
            $('#expiry_period_type').removeAttr('disabled');
        }

        if ($('#opening_stock_button').length) {
            $('#opening_stock_button').removeAttr('disabled');
        }
    });
    $(document).on('ifUnchecked', 'input#enable_stock', function () {
        $('div#alert_quantity_div').hide();
        $('div#quick_product_opening_stock_div').hide();
        $('input#alert_quantity').val(0);

        //Disable expiry selection
        if ($('#expiry_period_type').length) {
            $('#expiry_period_type')
                .val('')
                .change();
            $('#expiry_period_type').attr('disabled', true);
        }
        if ($('#opening_stock_button').length) {
            $('#opening_stock_button').attr('disabled', true);
        }
    });

    //Start For product type single

    //If purchase price exc tax is changed
    $(document).on('change', 'input#single_dpp', function (e) {
        var purchase_exc_tax = __read_number($('input#single_dpp'));
        purchase_exc_tax = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;

        var tax_rate = $('select#tax')
            .find(':selected')
            .data('rate');
        tax_rate = tax_rate == undefined ? 0 : tax_rate;

        var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
        __write_number($('input#single_dpp_inc_tax'), purchase_inc_tax);

        var profit_percent = __read_number($('#profit_percent'));
        var selling_price = __add_percent(purchase_exc_tax, profit_percent);
        // __write_number($('input#single_dsp'), selling_price);

        var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
        // __write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax);
    });

    //If tax rate is changed
    $(document).on('change', 'select#tax', function () {
        if ($('select#type').val() == 'single') {
            var purchase_exc_tax = __read_number($('input#single_dpp'));
            purchase_exc_tax = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;

            var tax_rate = $('select#tax')
                .find(':selected')
                .data('rate');
            tax_rate = tax_rate == undefined ? 0 : tax_rate;

            var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
            __write_number($('input#single_dpp_inc_tax'), purchase_inc_tax);

            var selling_price = __read_number($('input#single_dsp'));
            var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
            __write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax);
        }
    });

    //If purchase price inc tax is changed
    $(document).on('change', 'input#single_dpp_inc_tax', function (e) {
        var purchase_inc_tax = __read_number($('input#single_dpp_inc_tax'));
        purchase_inc_tax = purchase_inc_tax == undefined ? 0 : purchase_inc_tax;

        var tax_rate = $('select#tax')
            .find(':selected')
            .data('rate');
        tax_rate = tax_rate == undefined ? 0 : tax_rate;

        var purchase_exc_tax = __get_principle(purchase_inc_tax, tax_rate);
        __write_number($('input#single_dpp'), purchase_exc_tax);
        $('input#single_dpp').change();

        var profit_percent = __read_number($('#profit_percent'));
        profit_percent = profit_percent == undefined ? 0 : profit_percent;
        var selling_price = __add_percent(purchase_exc_tax, profit_percent);
        __write_number($('input#single_dsp'), selling_price);

        var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
        __write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax);
    });

    $(document).on('change', 'input#profit_percent', function (e) {
        var tax_rate = $('select#tax')
            .find(':selected')
            .data('rate');
        tax_rate = tax_rate == undefined ? 0 : tax_rate;

        var purchase_inc_tax = __read_number($('input#single_dpp_inc_tax'));
        purchase_inc_tax = purchase_inc_tax == undefined ? 0 : purchase_inc_tax;

        var purchase_exc_tax = __read_number($('input#single_dpp'));
        purchase_exc_tax = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;

        var profit_percent = __read_number($('input#profit_percent'));
        var selling_price = __add_percent(purchase_exc_tax, profit_percent);
        __write_number($('input#single_dsp'), selling_price);

        var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
        __write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax);
    });

    $(document).on('change', 'input#single_dsp', function (e) {
        var tax_rate = $('select#tax')
            .find(':selected')
            .data('rate');
        tax_rate = tax_rate == undefined ? 0 : tax_rate;

        var selling_price = __read_number($('input#single_dsp'));
        var purchase_exc_tax = __read_number($('input#single_dpp'));
        var profit_percent = __read_number($('input#profit_percent'));

        //if purchase price not set
        if (purchase_exc_tax == 0) {
            profit_percent = 0;
        } else {
            profit_percent = __get_rate(purchase_exc_tax, selling_price);
        }

        __write_number($('input#profit_percent'), profit_percent);

        var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
        __write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax);
    });

    $(document).on('change', 'input#single_dsp_inc_tax', function (e) {
        var tax_rate = $('select#tax')
            .find(':selected')
            .data('rate');
        tax_rate = tax_rate == undefined ? 0 : tax_rate;
        var selling_price_inc_tax = __read_number($('input#single_dsp_inc_tax'));

        var selling_price = __get_principle(selling_price_inc_tax, tax_rate);
        __write_number($('input#single_dsp'), selling_price);
        var purchase_exc_tax = __read_number($('input#single_dpp'));
        var profit_percent = __read_number($('input#profit_percent'));

        //if purchase price not set
        if (purchase_exc_tax == 0) {
            profit_percent = 0;
        } else {
            profit_percent = __get_rate(purchase_exc_tax, selling_price);
        }

        __write_number($('input#profit_percent'), profit_percent);
    });

    if ($('#product_add_form').length) {
        $('form#product_add_form').validate({
            rules: {
                sku: {
                    remote: {
                        url: '/products/check_product_sku',
                        type: 'post',
                        data: {
                            sku: function () {
                                return $('#sku').val();
                            },
                            product_id: function () {
                                if ($('#product_id').length > 0) {
                                    return $('#product_id').val();
                                } else {
                                    return '';
                                }
                            },
                        },
                    },
                },
                barcode_no: {
                    remote: {
                        url: '/products/check_product_barcode_no',
                        type: 'post',
                        data: {
                            barcode_no: function () {
                                return $('#barcode_no').val();
                            },
                            product_id: function () {
                                if ($('#product_id').length > 0) {
                                    return $('#product_id').val();
                                } else {
                                    return '';
                                }
                            },
                        },
                    },
                },
                expiry_period: {
                    required: {
                        depends: function (element) {
                            return (
                                $('#expiry_period_type')
                                    .val()
                                    .trim() != ''
                            );
                        },
                    },
                },
            },
            messages: {
                sku: {
                    remote: LANG.sku_already_exists,
                },
                barcode_no: {
                    remote: LANG.barcode_no_already_exists,
                },
            },
            invalidHandler: function (form, validator) {
                var firstError = validator.errorList && validator.errorList[0];
                if (firstError && firstError.message) {
                    toastr.error(firstError.message);
                    var el = $(firstError.element);
                    if (el.length) {
                        $('html, body').animate({ scrollTop: el.offset().top - 100 }, 300);
                        el.focus();
                    }
                } else {
                    toastr.error(LANG.some_error_in_input_field);
                }
            },
        });
    }
    if ($('#product_add_form').length) {
        $('form#product_add_form').validate({
            rules: {
                barcode_no: {
                    remote: {
                        url: '/products/check_product_barcode_no',
                        type: 'post',
                        data: {
                            barcode_no: function () {
                                return $('#barcode_no').val();
                            },
                            product_id: function () {
                                if ($('#product_id').length > 0) {
                                    return $('#product_id').val();
                                } else {
                                    return '';
                                }
                            },
                        },
                    },
                },

                expiry_period: {
                    required: {
                        depends: function (element) {
                            return (
                                $('#expiry_period_type')
                                    .val()
                                    .trim() != ''
                            );
                        },
                    },
                },
            },
            messages: {

            },
            invalidHandler: function (form, validator) {
                var firstError = validator.errorList && validator.errorList[0];
                if (firstError && firstError.message) {
                    toastr.error(firstError.message);
                    var el = $(firstError.element);
                    if (el.length) {
                        $('html, body').animate({ scrollTop: el.offset().top - 100 }, 300);
                        el.focus();
                    }
                } else {
                    toastr.error(LANG.some_error_in_input_field);
                }
            },
        });
    }

    // Collect variable product variation data from DOM for JSON fallback (when normal POST misses them)
    function collectVariableProductVariationJson() {
        var $table = $('#product_form_part #product_variation_form_part');
        if (!$table.length) return null;
        var rows = $table.find('> tbody > tr');
        var out = [];
        rows.each(function () {
            var $row = $(this);
            var templateId = $row.find('select.variation_template').val();
            var templateValues = $row.find('select.variation_template_values').val();
            if (templateValues == null) templateValues = [];
            var variations = [];
            $row.find('table.variation_value_table tbody tr').each(function () {
                var $v = $(this);
                variations.push({
                    sub_sku: $v.find('input[name*="[sub_sku]"]').val() || '',
                    var_barcode_no: $v.find('input[name*="[var_barcode_no]"]').val() || '',
                    var_maxSaleLimit: $v.find('input[name*="[var_maxSaleLimit]"]').val() || '',
                    value: $v.find('input[name*="[value]"]').val() || '',
                    default_purchase_price: $v.find('input[name*="[default_purchase_price]"]').val() || '',
                    dpp_inc_tax: $v.find('input[name*="[dpp_inc_tax]"]').val() || '',
                    profit_percent: $v.find('input[name*="[profit_percent]"]').val() || '',
                    default_sell_price: $v.find('input[name*="[default_sell_price]"]').val() || '',
                    sell_price_inc_tax: $v.find('input[name*="[sell_price_inc_tax]"]').val() || ''
                });
            });
            if (variations.length > 0) {
                out.push({
                    variation_template_id: templateId || '',
                    variation_template_values: templateValues,
                    variations: variations
                });
            }
        });
        return out.length ? JSON.stringify(out) : null;
    }

    function ensureProductVariationJsonInForm() {
        var product_type = $('select#type').val();
        if (product_type !== 'variable') return;
        var json = collectVariableProductVariationJson();
        var $form = $('form#product_add_form');
        $form.find('input[name="product_variation_json"]').remove();
        if (json) {
            $('<input type="hidden" name="product_variation_json">').val(json).appendTo($form);
        }
    }

    $(document).on('click', '.submit_product_form', function (e) {
        e.preventDefault();
        var is_valid_product_form = true;

        var variation_skus = [];
        var variation_barcodes = [];

        var submit_type = $(this).attr('value');
        console.log('[ProductVariation] Submit clicked', { submit_type: submit_type });

        // Validate variable product variations before submission
        var product_type = $('select#type').val();
        console.log('[ProductVariation] Product type', { product_type: product_type });
        if (product_type === 'variable') {
            // Check if any variations are added
            var variation_rows = $('#product_variation_form_part tbody tr');
            if (variation_rows.length === 0) {
                variation_rows = $formPart.find('select.variation_template').closest('tr');
            }
            var hasVariationFields = $formPart.find('select.variation_template, input[name*="product_variation"]').length > 0;
            if (variation_rows.length === 0 && !hasVariationFields) {
                toastr.error(LANG.add_at_least_one_variation);
                return false;
            }

            // Check if each variation group has values
            var has_empty_variations = false;
            var has_empty_templates = false;
            
            variation_rows.each(function() {
                                
                var variation_table = $(this).find('table');
                if (variation_table.length > 0) {
                    var variation_value_rows = variation_table.find('tbody tr');
                    if (variation_value_rows.length === 0) {
                        has_empty_variations = true;
                        return false; // break the loop
                    }
                    
                    // Check if each variation value has a name
                    variation_value_rows.each(function() {
                        var variation_name = $(this).find('input[name*="[value]"]').val();
                        if (!variation_name || variation_name.trim() === '') {
                            has_empty_variations = true;
                            return false; // break the inner loop
                        }
                    });
                }
            });

            if (has_empty_templates) {
                toastr.error(LANG.select_variation_templates);
                return false;
            }
            
            if (has_empty_variations) {
                toastr.error(LANG.add_variations_for_all);
                return false;
            }
        }

        $('#product_form_part').find('.input_sub_sku').each(function () {
            var element = $(this);
            var row_variation_id = '';
            if ($(this).closest('tr').find('.row_variation_id')) {
                row_variation_id = $(this).closest('tr').find('.row_variation_id').val();
            }

            variation_skus.push({ sku: element.val(), variation_id: row_variation_id });
        });
        $('#product_form_part').find('.input_var_barcode_no').each(function () {
            var element = $(this);
            var row_variation_var_bar_validate = '';
            if ($(this).closest('tr').find('.row_variation_var_bar_validate')) {
                row_variation_var_bar_validate = $(this).closest('tr').find('.row_variation_var_bar_validate').val();
            }
            variation_barcodes.push({ barcode_no: element.val(), variation_id: row_variation_var_bar_validate });
        });

        if (variation_skus.length > 0 && variation_barcodes.length > 0) {
            // Make both API calls independently
            $.when(
                $.ajax({
                    method: 'post',
                    url: '/products/validate_variation_skus',
                    data: { skus: variation_skus }
                }),
                $.ajax({
                    method: 'post',
                    url: '/products/validate_variation_barcodes',
                    data: { barcodes: variation_barcodes }
                })
            ).then(function (skusResult, barcodesResult) {
                // Check both results
                if (skusResult[0].success && barcodesResult[0].success) {
                    $('#submit_type').val(submit_type);
                    if ($('form#product_add_form').valid()) {
                        $('form#product_add_form').submit();
                    }
                } else {
                    // Handle errors
                    if (!skusResult[0].success) {
                        toastr.error(__translate('skus_already_exists', { sku: skusResult[0].sku }));
                    }
                    if (!barcodesResult[0].success) {
                        toastr.error(__translate('barcodes_already_exists', { sku: barcodesResult[0].sku }));
                    }
                    return false;
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                toastr.error(LANG.something_went_wrong);
                return false;
            });
        } else if (variation_skus.length > 0) { //validate sku
            $.ajax({
                method: 'post',
                url: '/products/validate_variation_skus',
                data: { skus: variation_skus },
                success: function (result) {
                    if (result.success == true) {
                        $('#submit_type').val(submit_type);
                        if ($('form#product_add_form').valid()) {
                            $('form#product_add_form').submit();
                        }
                    } else {
                        toastr.error(__translate('skus_already_exists', { sku: result.sku }));
                        return false;
                    }
                },
            });
        } else if (variation_barcodes.length > 0) { // validate barcode
            $.ajax({
                method: 'post',
                url: '/products/validate_variation_barcodes',
                data: { barcodes: variation_barcodes },
                success: function (result) {
                    if (result.success == true) {
                        $('#submit_type').val(submit_type);
                        if ($('form#product_add_form').valid()) {
                            $('form#product_add_form').submit();
                        }
                    } else {
                        toastr.error(__translate('barcodes_already_exists', { sku: result.sku }));
                        return false;
                    }
                },
            });
        } else { //then only submit
            $('#submit_type').val(submit_type);
            if ($('form#product_add_form').valid()) {
                $('form#product_add_form').submit();
            }
        }

    });
    //End for product type single

    //Start for product type Variable
    //If purchase price exc tax is changed
    $(document).on('change', 'input.variable_dpp', function (e) {
        var tr_obj = $(this).closest('tr');

        var purchase_exc_tax = __read_number($(this));
        purchase_exc_tax = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;

        var tax_rate = $('select#tax')
            .find(':selected')
            .data('rate');
        tax_rate = tax_rate == undefined ? 0 : tax_rate;

        var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
        __write_number(tr_obj.find('input.variable_dpp_inc_tax'), purchase_inc_tax);

        var profit_percent = __read_number(tr_obj.find('input.variable_profit_percent'));
        // var selling_price = __add_percent(purchase_exc_tax, profit_percent);
        // __write_number(tr_obj.find('input.variable_dsp'), selling_price);

        // var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
        // __write_number(tr_obj.find('input.variable_dsp_inc_tax'), selling_price_inc_tax);
    });

    //If purchase price inc tax is changed
    $(document).on('change', 'input.variable_dpp_inc_tax', function (e) {
        var tr_obj = $(this).closest('tr');

        var purchase_inc_tax = __read_number($(this));
        purchase_inc_tax = purchase_inc_tax == undefined ? 0 : purchase_inc_tax;

        var tax_rate = $('select#tax')
            .find(':selected')
            .data('rate');
        tax_rate = tax_rate == undefined ? 0 : tax_rate;

        var purchase_exc_tax = __get_principle(purchase_inc_tax, tax_rate);
        __write_number(tr_obj.find('input.variable_dpp'), purchase_exc_tax);

        var profit_percent = __read_number(tr_obj.find('input.variable_profit_percent'));
        var selling_price = __add_percent(purchase_exc_tax, profit_percent);
        __write_number(tr_obj.find('input.variable_dsp'), selling_price);

        var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
        __write_number(tr_obj.find('input.variable_dsp_inc_tax'), selling_price_inc_tax);
    });

    $(document).on('change', 'input.variable_profit_percent', function (e) {
        var tax_rate = $('select#tax')
            .find(':selected')
            .data('rate');
        tax_rate = tax_rate == undefined ? 0 : tax_rate;

        var tr_obj = $(this).closest('tr');
        var profit_percent = __read_number($(this));

        var purchase_exc_tax = __read_number(tr_obj.find('input.variable_dpp'));
        purchase_exc_tax = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;

        var selling_price = __add_percent(purchase_exc_tax, profit_percent);
        __write_number(tr_obj.find('input.variable_dsp'), selling_price);

        var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
        __write_number(tr_obj.find('input.variable_dsp_inc_tax'), selling_price_inc_tax);
    });

    $(document).on('change', 'input.variable_dsp', function (e) {
        var tax_rate = $('select#tax')
            .find(':selected')
            .data('rate');
        tax_rate = tax_rate == undefined ? 0 : tax_rate;

        var tr_obj = $(this).closest('tr');
        var selling_price = __read_number($(this));
        var purchase_exc_tax = __read_number(tr_obj.find('input.variable_dpp'));

        var profit_percent = __read_number(tr_obj.find('input.variable_profit_percent'));

        //if purchase price not set
        if (purchase_exc_tax == 0) {
            profit_percent = 0;
        } else {
            profit_percent = __get_rate(purchase_exc_tax, selling_price);
        }

        __write_number(tr_obj.find('input.variable_profit_percent'), profit_percent);

        var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
        __write_number(tr_obj.find('input.variable_dsp_inc_tax'), selling_price_inc_tax);
    });
    $(document).on('change', 'input.variable_dsp_inc_tax', function (e) {
        var tr_obj = $(this).closest('tr');
        var selling_price_inc_tax = __read_number($(this));

        var tax_rate = $('select#tax')
            .find(':selected')
            .data('rate');
        tax_rate = tax_rate == undefined ? 0 : tax_rate;

        var selling_price = __get_principle(selling_price_inc_tax, tax_rate);
        __write_number(tr_obj.find('input.variable_dsp'), selling_price);

        var purchase_exc_tax = __read_number(tr_obj.find('input.variable_dpp'));
        var profit_percent = __read_number(tr_obj.find('input.variable_profit_percent'));
        //if purchase price not set
        if (purchase_exc_tax == 0) {
            profit_percent = 0;
        } else {
            profit_percent = __get_rate(purchase_exc_tax, selling_price);
        }

        __write_number(tr_obj.find('input.variable_profit_percent'), profit_percent);
    });

    $(document).on('click', '.add_variation_value_row', function () {
        var variation_row_index = $(this)
            .closest('.variation_row')
            .find('.row_index')
            .val();
        var variation_value_row_index = $(this)
            .closest('table')
            .find('tr:last .variation_row_index')
            .val();

        // Check if URL contains product/create
        var currentUrl = window.location.href;
        var isProductCreate = currentUrl.includes('products/create');
        
        if (isProductCreate) {
            var row_type = 'add';
        } else if (
            $(this)
                .closest('.variation_row')
                .find('.row_edit').length >= 1
        ) {
            var row_type = 'edit';
        } else {
            var row_type = 'add';
        }
        console.log(row_type);
        var table = $(this).closest('table');

        $.ajax({
            method: 'GET',
            url: '/products/get_variation_value_row',
            data: {
                variation_row_index: variation_row_index,
                value_index: variation_value_row_index,
                row_type: row_type,
            },
            dataType: 'html',
            success: function (result) {
                if (result) {
                    table.append(result);
                    toggle_dsp_input();
                }
            },
        });
    });
    // $(document).on('change', '.variation_template_values', function() {
    //     var tr_obj = $(this).closest('tr');
    //     var val = $(this).val();
    //     tr_obj.find('.variation_value_row').each(function(){
    //         if(val.includes($(this).attr('data-variation_value_id'))) {
    //             $(this).removeClass('hide');
    //             $(this).find('.is_variation_value_hidden').val(0);
    //         } else {
    //             $(this).addClass('hide');
    //             $(this).find('.is_variation_value_hidden').val(1);
    //         }
    //     })
    // });

    // Save the previous selection on focus
    // $(document).on('focus', '.variation_template_values', function () {
    //     const selected = $(this).val() || [];
    //     $(this).data('previous', selected);
    // });

    $(document).on('change', '.variation_template_values', function () {
        const $select = $(this);
        const tr_obj = $select.closest('tr');
        const variation_id = tr_obj.find('.variation_template').val();
        const table_body = tr_obj.find('table.variation_value_table tbody');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        const previous = $select.data('previous') || [];
        const current = $select.val() || [];

        // Store current for next change
        $select.data('previous', current);

        // Determine added and removed variation value IDs
        const added = current.filter(val => !previous.includes(val));
        const removed = previous.filter(val => !current.includes(val));

        // Remove rows for deselected variation values
        removed.forEach(value_id => {
            table_body.find(`tr[data-variation_value_id="${value_id}"]`).remove();
        });

        // After removal, reindex existing rows
        function updateExistingRowIndices() {
            table_body.find('tr.variation_value_row').each(function (idx) {
                $(this).find('input, select').each(function () {
                    let name = $(this).attr('name');
                    if (name) {
                        // Update variations index
                        name = name.replace(/product_variation\[0\]\[variations\]\[\d+\]/, `product_variation[0][variations][${idx}]`);
                        // Update image index if present
                        name = name.replace(/variation_images_\d+_\d+/, `variation_images_0_${idx}`);
                        $(this).attr('name', name);
                    }
                });
                // Update sub_sku for existing rows
                const $parentSku = $(document).find('.product_form input[name="sku"]').val();
                if ($parentSku) {
                    const sub_sku = $parentSku + (idx < 9 ? '0' : '') + (idx + 1);
                    $(this).find('input[name$="[sub_sku]"]').val(sub_sku);
                }
            });
        }

        updateExistingRowIndices();

        // Add new variation value rows
        added.forEach((value_id) => {
            // Get variation row index from the parent tr
            const variation_row_index = tr_obj.find('.row_index').val();
            // Get the next available index for the new row
            const next_index = table_body.find('tr.variation_value_row').length;

            $.ajax({
                method: 'POST',
                url: '/products/get_variation_value_row_by_id',
                data: {
                    variation_id: variation_id,
                    value_id: value_id,
                    row_index: variation_row_index,
                    value_index: next_index,
                    _token: csrfToken
                },
                success: function (res) {
                    if (res.status === 'success' && res.html) {
                        if (table_body.find('tr').length === 1 && !table_body.find('tr').hasClass('variation_value_row')) {
                            table_body.find('tr:first').replaceWith(res.html);
                        } else {
                            table_body.append(res.html);
                        }
                        const $parentSku = $(document).find('.product_form input[name="sku"]').val();
                        let sub_sku = '';
                        if ($parentSku) {
                            sub_sku = $parentSku + (next_index < 9 ? '0' : '') + (next_index + 1);
                        }

                        const $newRow = table_body.find('tr:last');
                        $newRow.find('input, select').each(function () {
                            let name = $(this).attr('name');
                            if (name) {
                                name = name.replace(/product_variation\[0\]\[variations\]\[\d+\]/, `product_variation[0][variations][${next_index}]`);
                                name = name.replace(/variation_images_\d+_\d+/, `variation_images_0_${next_index}`);
                                $(this).attr('name', name);
                                if (name.includes('[sub_sku]') && $parentSku) {
                                    $(this).val(sub_sku);
                                }
                            }
                        });
                        toggle_dsp_input();
                        // Reindex all rows after adding new one
                        updateExistingRowIndices();
                    } else {
                        console.warn('Unexpected response:', res);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching variation row:', error);
                }
            });
        });
    });

    $(document).on('change', '.variation_template', function () {
        tr_obj = $(this).closest('tr');

        if ($(this).val() !== '') {
            tr_obj.find('input.variation_name').val(
                $(this)
                    .find('option:selected')
                    .text()
            );

            var template_id = $(this).val();
            var row_index = $(this)
                .closest('tr')
                .find('.row_index')
                .val();
            $.ajax({
                method: 'POST',
                url: '/products/get_variation_template',
                dataType: 'json',
                data: { template_id: template_id, row_index: row_index },
                success: function (result) {
                    if (result) {
                        if (result.values.length > 0) {
                            tr_obj.find('.variation_template_values').select2();
                            tr_obj.find('.variation_template_values').empty();
                            tr_obj.find('.variation_template_values').select2({ data: result.values, closeOnSelect: true });
                            tr_obj.find('.variation_template_values_div').removeClass('hide');
                            tr_obj.find('.variation_template_values').select2('open');
                        } else {
                            tr_obj.find('.variation_template_values_div').addClass('hide');
                        }
                        tr_obj
                            .find('table.variation_value_table')
                            .find('tbody')
                            .html(result.html);

                        toggle_dsp_input();
                    }
                },
            });
        }
    });

    $(document).on('click', '.delete_complete_row', function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                $(this)
                    .closest('.variation_row')
                    .remove();
            }
        });
    });

    $(document).on('click', '.remove_variation_value_row', function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var count = $(this)
                    .closest('table')
                    .find('.remove_variation_value_row').length;
                if (count === 1) {
                    $(this)
                        .closest('.variation_row')
                        .remove();
                } else {
                    $(this)
                        .closest('tr')
                        .remove();
                }
            }
        });
    });

    // Handle discontinue variation value row
    $(document).on('click', '.discontinue_variation_value_row', function () {
        var $button = $(this);
        var variationId = $button.data('variation-id');
        var $row = $button.closest('tr');
        
        swal({
            title: 'Are you sure?',
            icon: 'warning',
            buttons: {
                cancel: 'Cancel',
                confirm: {
                    text: 'Ok',
                    value: true,
                }
            },
            dangerMode: false,
        }).then(willDiscontinue => {
            if (willDiscontinue) {
                // Add hidden input to mark as discontinued (before hiding)
                var $hiddenInput = $row.find('.discontinued_variation_id');
                if ($hiddenInput.length === 0) {
                    $row.append('<input type="hidden" name="discontinued_variations[]" class="discontinued_variation_id" value="' + variationId + '">');
                } else {
                    $hiddenInput.val(variationId);
                }
                
                // Hide the row with animation
                $row.slideUp(300, function() {
                    $(this).hide();
                });
                
                toastr.success('Variation discontinued successfully.');
            }
        });
    });

    // Handle discontinue complete variation row
    $(document).on('click', '.discontinue_variation_row', function () {
        var $button = $(this);
        var $row = $button.closest('.variation_row');
        var $variationRows = $row.find('tbody tr');
        var variationIds = [];
        
        $variationRows.each(function() {
            var $varRow = $(this);
            var variationId = $varRow.find('.row_variation_id').val();
            if (variationId) {
                variationIds.push(variationId);
            }
        });
        
        swal({
            title: 'Discontinue All Variations?',
            text: 'All variations in this group will be marked as discontinued and will not appear in new sales. Existing orders and invoices will not be affected.',
            icon: 'warning',
            buttons: {
                cancel: 'Cancel',
                confirm: {
                    text: 'Discontinue',
                    value: true,
                }
            },
            dangerMode: false,
        }).then(willDiscontinue => {
            if (willDiscontinue) {
                // Mark each variation row as discontinued and hide them
                var rowsToHide = [];
                $variationRows.each(function() {
                    var $varRow = $(this);
                    var variationId = $varRow.find('.row_variation_id').val();
                    
                    if (variationId) {
                        // Add hidden input before hiding
                        var $hiddenInput = $varRow.find('.discontinued_variation_id');
                        if ($hiddenInput.length === 0) {
                            $varRow.append('<input type="hidden" name="discontinued_variations[]" class="discontinued_variation_id" value="' + variationId + '">');
                        } else {
                            $hiddenInput.val(variationId);
                        }
                        
                        rowsToHide.push($varRow);
                    }
                });
                
                // Hide all variation rows with animation
                rowsToHide.forEach(function($varRow) {
                    $varRow.slideUp(300, function() {
                        $(this).hide();
                    });
                });
                
                // Hide the entire variation row if all variations are hidden
                if (rowsToHide.length === $variationRows.length) {
                    $row.slideUp(300, function() {
                        $(this).hide();
                    });
                }
                
                toastr.success('Variations will be marked as discontinued when you save the product.');
            }
        });
    });

    //If tax rate is changed
    $(document).on('change', 'select#tax', function () {
        if ($('select#type').val() == 'variable') {
            var tax_rate = $('select#tax')
                .find(':selected')
                .data('rate');
            tax_rate = tax_rate == undefined ? 0 : tax_rate;

            $('table.variation_value_table > tbody').each(function () {
                $(this)
                    .find('tr')
                    .each(function () {
                        var purchase_exc_tax = __read_number($(this).find('input.variable_dpp'));
                        purchase_exc_tax = purchase_exc_tax == undefined ? 0 : purchase_exc_tax;

                        var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
                        __write_number(
                            $(this).find('input.variable_dpp_inc_tax'),
                            purchase_inc_tax
                        );

                        var selling_price = __read_number($(this).find('input.variable_dsp'));
                        var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
                        __write_number(
                            $(this).find('input.variable_dsp_inc_tax'),
                            selling_price_inc_tax
                        );
                    });
            });
        }
    });
    //End for product type Variable
    $(document).on('change', '#tax_type', function (e) {
        toggle_dsp_input();
    });
    toggle_dsp_input();

    $(document).on('change', '#expiry_period_type', function (e) {
        if ($(this).val()) {
            $('input#expiry_period').prop('disabled', false);
        } else {
            $('input#expiry_period').val('');
            $('input#expiry_period').prop('disabled', true);
        }
    });

    $(document).on('click', 'a.view-product', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('href'),
            dataType: 'html',
            success: function (result) {
                $('.view_modal ')
                    .html(result)
                    .modal('show');
                __currency_convert_recursively($('#view_product_modal'));
            },
        });
    });
    var img_fileinput_setting = {
        showUpload: false,
        showPreview: true,
        browseLabel: LANG.file_browse_label,
        removeLabel: LANG.remove,
        maxFileSize: 5120,
        previewSettings: {
            image: { width: 'auto', height: 'auto', 'max-width': '100%', 'max-height': '100%' },
        },
    };
    $('#upload_image').fileinput(img_fileinput_setting);

    if ($('textarea#product_description').length > 0) {
        tinymce.init({
            selector: 'textarea#product_description',
            height: 250
        });
    }
    if ($('textarea#product_warranty').length > 0) {
        tinymce.init({
            selector: 'textarea#product_warranty',
            height: 250
        });
    }
    syncWebCategories();

    // Handle file selection and preview
    $('#gallery_images').on('change', function () {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const newFiles = Array.from(this.files);

        newFiles.forEach(file => {
            if (file.size > maxSize) {
                toastr.warning(`"${file.name}" exceeds the 5MB limit and was skipped.`);
                return;
            }
            // Skip duplicates
            if (galleryFiles.some(f => f.name === file.name && f.size === file.size)) {
                return;
            }
            if (!window.galleryFiles.some(f => f.name === file.name && f.size === file.size)) {
                window.galleryFiles.push(file);

                const reader = new FileReader();
                reader.onload = function (e) {
                    const thumb = $(`
                        <div class="product-gallery-thumb" data-type="new" data-name="${file.name}" data-size="${file.size}">
                            <img src="${e.target.result}" alt="${file.name}">
                            <div class="gallery-thumb-actions">
                                <button type="button" class="edit-gallery-thumb" title="Edit"><i class="fa fa-edit"></i></button>
                                <button type="button" class="remove-gallery-thumb" title="Remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    `);
                    $('#product-gallery-thumbs').append(thumb);
                };
                reader.readAsDataURL(file);
            }
        });

        // Update the input's FileList
        const dt = new DataTransfer();
        window.galleryFiles.forEach(f => dt.items.add(f));
        document.getElementById('gallery_images').files = dt.files;

        this.value = ''; // clear for reselecting same image
    });

    // Remove image from preview (and input)
    // $(document).on('click', '.remove-gallery-thumb', function (e) {
    //     e.preventDefault();
    //     e.stopPropagation();

    //     const $thumb = $(this).closest('.product-gallery-thumb');
    //     const imageId = $(this).data('id');
    //     const fileName = $thumb.data('name');
    //     const fileSize = $thumb.data('size');

    //     // If it's an existing image (has ID), make AJAX call to remove from server
    //     if (imageId) {
    //         swal({
    //             title: LANG.sure,
    //             icon: 'warning',
    //             buttons: true,
    //             dangerMode: true,
    //         }).then(willDelete => {
    //             if (willDelete) {
    //                 $.ajax({
    //                     method: 'POST',
    //                     url: '/products/remove-gallery-image',
    //                     data: {
    //                         image_id: imageId,
    //                         _token: $('meta[name="csrf-token"]').attr('content')
    //                     },
    //                     success: function (result) {
    //                         if (result.success) {
    //                             $thumb.fadeOut(300, function () {
    //                                 $(this).remove();
    //                             });
    //                             toastr.success(result.msg);
    //                         } else {
    //                             toastr.error(result.msg);
    //                         }
    //                     }
    //                 });
    //             }
    //         });
    //     } else {
    //         // If it's a newly added image (no ID), just remove from preview
    //         swal({
    //             title: LANG.sure,
    //             icon: 'warning',
    //             buttons: true,
    //             dangerMode: true,
    //         }).then(willDelete => {
    //             if (willDelete) {
    //                 // Remove from galleryFiles array
    //                 window.galleryFiles = window.galleryFiles.filter(f => !(f.name === fileName && f.size === fileSize));

    //                 // Update the input's FileList
    //                 const dt = new DataTransfer();
    //                 window.galleryFiles.forEach(f => dt.items.add(f));
    //                 document.getElementById('gallery_images').files = dt.files;

    //                 $thumb.fadeOut(300, function () {
    //                     $(this).remove();
    //                 });
    //             }
    //         });
    //     }
    // });

    // Handle form submission
    $(document).on('submit', '#product_add_form', function (e) {
        // Ensure gallery files are properly attached to the form
        if (window.galleryFiles && window.galleryFiles.length > 0) {
            const dt = new DataTransfer();
            window.galleryFiles.forEach(f => dt.items.add(f));
            document.getElementById('gallery_images').files = dt.files;
        }
    });
});

function toggle_dsp_input() {
    var tax_type = $('#tax_type').val();
    if (tax_type == 'inclusive') {
        $('.dsp_label').each(function () {
            $(this).text(LANG.inc_tax);
        });
        $('#single_dsp').addClass('hide');
        $('#single_dsp_inc_tax').removeClass('hide');

        $('.add-product-price-table')
            .find('.variable_dsp_inc_tax')
            .each(function () {
                $(this).removeClass('hide');
            });
        $('.add-product-price-table')
            .find('.variable_dsp')
            .each(function () {
                $(this).addClass('hide');
            });
    } else if (tax_type == 'exclusive') {
        $('.dsp_label').each(function () {
            $(this).text(LANG.exc_tax);
        });
        $('#single_dsp').removeClass('hide');
        $('#single_dsp_inc_tax').addClass('hide');

        $('.add-product-price-table')
            .find('.variable_dsp_inc_tax')
            .each(function () {
                $(this).addClass('hide');
            });
        $('.add-product-price-table')
            .find('.variable_dsp')
            .each(function () {
                $(this).removeClass('hide');
            });
    }
}

function get_product_details(rowData) {
    var div = $('<div/>')
        .addClass('loading')
        .text('Loading...');

    $.ajax({
        url: '/products/' + rowData.id,
        dataType: 'html',
        success: function (data) {
            div.html(data).removeClass('loading');
        },
    });

    return div;
}

//Quick add unit
$(document).on('submit', 'form#quick_add_unit_form', function (e) {
    e.preventDefault();
    var form = $(this);
    var data = form.serialize();

    $.ajax({
        method: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        beforeSend: function (xhr) {
            __disable_submit_button(form.find('button[type="submit"]'));
        },
        success: function (result) {
            if (result.success == true) {
                var newOption = new Option(result.data.short_name, result.data.id, true, true);
                // Append it to the select
                $('#unit_id')
                    .append(newOption)
                    .trigger('change');
                $('div.view_modal').modal('hide');
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

//Quick add brand
$(document).on('submit', 'form#quick_add_brand_form', function (e) {
    e.preventDefault();
    var form = $(this);
    var data = form.serialize();
    var category = form.find('select#category').val();
    // if(category == null || category == ''){
    //     toastr.error('Category is required');
    //     form.find('select#category').focus();
    //     form.find('select#category').parent().css('border', '2px solid #ef4444');
    //     form.find('button[type="submit"]').prop('disabled', false);
    //     return;
    // }
    $.ajax({
        method: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        beforeSend: function (xhr) {
            __disable_submit_button(form.find('button[type="submit"]'));
        },
        success: function (result) {
            if (result.success == true) {
                var newOption = new Option(result.data.name, result.data.id, true, true);
                // Append it to the select
                $('#brand_id')
                    .append(newOption)
                    .trigger('change');
                $('div.view_modal').modal('hide');
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

$(document).on('click', 'button.apply-all', function () {
    var val = $(this).closest('.input-group').find('input').val();
    var target_class = $(this).data('target-class');
    $(this).closest('tbody').find('tr').each(function () {
        element = $(this).find(target_class);
        element.val(val);
        element.change();
    });
});



// Function to sync category/subcategory with web categories
function syncWebCategories() {
    let selectedValues = $('#custom_sub_categories').val() || [];
    const categoryId = $('#category_id').val();
    const subCategoryId = $('#sub_category_id').val();

    // Add or remove category
    if (categoryId) {
        if (!selectedValues.includes(categoryId)) {
            selectedValues.push(categoryId);
        }
    } else {
        selectedValues = selectedValues.filter(value => value !== previousCategoryId);
    }

    // Add or remove subcategory
    if (subCategoryId) {
        if (!selectedValues.includes(subCategoryId)) {
            selectedValues.push(subCategoryId);
        }
    } else {
        selectedValues = selectedValues.filter(value => value !== previousSubCategoryId);
    }

    // Update the web categories select
    $('#custom_sub_categories').val(selectedValues);

    // Store current values for next comparison
    previousCategoryId = '';
    previousSubCategoryId = '';
}

// Store initial values
let previousCategoryId = $('#category_id').val();
let previousSubCategoryId = $('#sub_category_id').val();
let catid = previousCategoryId;
let subid = previousSubCategoryId;


// Bind to category change
$(document).on('change', '#category_id', function () {
    syncWebCategories();
});

// Bind to subcategory change
$(document).on('change', '#sub_category_id', function () {
    syncWebCategories();
});

// Handle manual deselection in web categories
$(document).on('change', '#custom_sub_categories', function (e) {
    const currentValues = $(this).val() || [];

    // If category was manually deselected, clear category select
    if (previousCategoryId && !currentValues.includes(previousCategoryId)) {
        $('#category_id').val('').trigger('change');
        previousCategoryId = null;
    }

    // If subcategory was manually deselected, clear subcategory select
    if (previousSubCategoryId && !currentValues.includes(previousSubCategoryId)) {
        $('#sub_category_id').val('').trigger('change');
        previousSubCategoryId = null;
    }
});

// Call initial sync when document is ready
$(document).ready(function () {
    syncWebCategories();

    // Rohit changes
    function setCategoryAndSubCategory(catid, subid) {
        new Promise(function (resolve) {
            $('#category_id').val(catid).trigger('change');
            resolve();
        }).then(function () {
            return new Promise(function (resolve) {
                setTimeout(resolve, 1500);
            });
        }).then(function () {
            $('#sub_category_id').val(subid).trigger('change');
        });
    }
    setTimeout(function() {setCategoryAndSubCategory(catid, subid)},2)



    $(document).on('click', '#add-gallery-images', function (e) {
        e.preventDefault();
        $('#gallery_images').click();
    });
    function renderGalleryPreviews() {
        const $thumbs = $('#product-gallery-thumbs');
        $thumbs.html('');
        galleryFiles.forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const thumb = $(`
                    <div class="product-gallery-thumb" data-idx="${idx}">
                        <img src="${e.target.result}" alt="Gallery Image">
                        <div class="gallery-thumb-actions">
                            <button type="button" class="edit-gallery-thumb" title="Edit"><i class="fa fa-edit"></i></button>
                            <button type="button" class="remove-gallery-thumb" title="Remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                `);
                $thumbs.append(thumb);
            };
            reader.readAsDataURL(file);
        });

        // Update the input's FileList
        const dt = new DataTransfer();
        galleryFiles.forEach(f => dt.items.add(f));
        document.getElementById('gallery_images').files = dt.files;
    }
    // let galleryFiles = [];
    // // Handle file selection and preview
    // $('#gallery_images').on('change', function () {
    //     const newFiles = Array.from(this.files);

    //     newFiles.forEach(file => {
    //         if (!galleryFiles.some(f => f.name === file.name && f.size === file.size)) {
    //             galleryFiles.push(file);

    //             const reader = new FileReader();
    //             reader.onload = function (e) {
    //                 const thumb = $(`
    //                     <div class="product-gallery-thumb" data-type="new" data-name="${file.name}" data-size="${file.size}">
    //                         <img src="${e.target.result}" alt="${file.name}">
    //                         <div class="gallery-thumb-actions">
    //                             <button type="button" class="edit-gallery-thumb" title="Edit"><i class="fa fa-edit"></i></button>
    //                             <button type="button" class="remove-gallery-thumb" title="Remove"><i class="fa fa-times"></i></button>
    //                         </div>
    //                     </div>
    //                 `);
    //                 $('#product-gallery-thumbs').append(thumb);
    //             };
    //             reader.readAsDataURL(file);
    //         }
    //     });

    //     // Update the input's FileList
    //     const dt = new DataTransfer();
    //     galleryFiles.forEach(f => dt.items.add(f));
    //     document.getElementById('gallery_images').files = dt.files;

    //     this.value = ''; // clear for reselecting same image
    // });


    $(document).on('change', '.variation_images', function () {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const input = this;
        const dt = new DataTransfer();
        let filesValid = false;

        Array.from(input.files).forEach(file => {
            if (file.size > maxSize) {
                toastr.warning(`"${file.name}" exceeds the 5MB limit and was skipped.`);
            } else {
                dt.items.add(file);
                filesValid = true;
            }
        });

        if (filesValid) {
            input.files = dt.files;
        } else {
            input.value = ''; // clear input if no valid files
        }
    });

    // Remove image from preview (and input)
    $(document).on('click', '.remove-gallery-thumb', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $thumb = $(this).closest('.product-gallery-thumb');
        const imageId = $(this).data('id');
        const fileName = $thumb.data('name');
        const fileSize = $thumb.data('size');

        // If it's an existing image (has ID), make AJAX call to remove from server
        if (imageId) {
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    $.ajax({
                        method: 'POST',
                        url: '/products/remove-gallery-image',
                        data: {
                            image_id: imageId,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (result) {
                            if (result.status) {
                                $thumb.fadeOut(300, function () {
                                    $(this).remove();
                                });
                                toastr.success(result.message);
                            } else {
                                toastr.error(result.message);
                            }
                        }
                    });
                }
            });
        } else {
            // If it's a newly added image (no ID), just remove from preview
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    // Remove from galleryFiles array
                    galleryFiles = galleryFiles.filter(f => !(f.name === fileName && f.size === fileSize));

                    // Update the input's FileList
                    const dt = new DataTransfer();
                    galleryFiles.forEach(f => dt.items.add(f));
                    document.getElementById('gallery_images').files = dt.files;

                    $thumb.fadeOut(300, function () {
                        $(this).remove();
                    });
                }
            });
        }
    });

    // Edit gallery image
    $(document).on('click', '.edit-gallery-thumb', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $thumb = $(this).closest('.product-gallery-thumb');
        const idx = $thumb.data('idx');
        const imgSrc = $thumb.find('img').attr('src');
        const imgAlt = $thumb.find('img').attr('alt') || '';

        // Populate edit modal
        $('#editGalleryImage').attr('src', imgSrc);
        $('#editGalleryAlt').val(imgAlt);
        $('#editGalleryIndex').val(idx);

        // Show edit modal
        $('#editGalleryModal').modal('show');
    });

    // Handle edit form submission
    $(document).on('submit', '#editGalleryForm', function (e) {
        e.preventDefault();

        const idx = $('#editGalleryIndex').val();
        const newAlt = $('#editGalleryAlt').val();

        // Update the image alt text
        $(`.product-gallery-thumb[data-idx="${idx}"] img`).attr('alt', newAlt);

        // Close modal
        $('#editGalleryModal').modal('hide');
    });

    // Gallery image preview modal
    $(document).on('click', '.product-gallery-thumb img', function () {
        // Get image src and name
        var src = $(this).attr('src');
        var name = '';
        // Try to get file name if possible
        if (this.src.startsWith('data:')) {
            // If using File API, get name from input
            var idx = $(this).closest('.product-gallery-thumb').data('idx');
            var input = document.getElementById('gallery_images');
            if (input && input.files && input.files[idx]) {
                name = input.files[idx].name + ' (' + Math.round(input.files[idx].size / 1024) + ' KB)';
            }
        } else {
            // If image is from server, get from alt or data attribute
            name = $(this).attr('alt') || '';
        }
        $('#galleryImagePreview').attr('src', src);
        $('#galleryImageName').text(name ? ' ' + name : '');
        $('#galleryImagePreviewModal').modal('show');
    });

});

// Handle form submission
$(document).on('submit', '#product_add_form', function (e) {
    // Ensure gallery files are properly attached to the form
    if (galleryFiles.length > 0) {
        const dt = new DataTransfer();
        galleryFiles.forEach(f => dt.items.add(f));
        document.getElementById('gallery_images').files = dt.files;
    }
});


 $(document).on('input', '#ct', function () {
        // Replace anything that's not a digit
        this.value = this.value.replace(/\D/g, '');
    });