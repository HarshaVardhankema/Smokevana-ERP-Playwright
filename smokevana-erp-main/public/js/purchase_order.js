$(document).ready(function () {
    var purchase_id = document.querySelector('meta[name="purchase-id"]').getAttribute('content');
    // Handle supplier reference number update

    // open modal
    // activity modal

    $("#openActivityModal").on("click", function (e) {

        var modal_element = $('div.enable_session_lock');
        let url = `/sells/pos/activity_modal/${purchase_id}`;
        let modalId = 'modal-' + new Date().getTime();
        $.ajax({
            url: url,
            success: function (response) {
                let newModal = $('<div class="modal fade" id="' + modalId +
                    '" data-backdrop="static" data-keyboard="false">' +
                    +
                    '<div class="modal-content">' + response + +
                    '</div>' +
                    '</div>');
                $('body').append(newModal);
                newModal.modal('show');

                // Remove modal from DOM after closing
                newModal.on('hidden.bs.modal', function () {
                    $(this).remove();
                });
            }
        });
    });


    //open sell note modal

    $("#openSellsNoteModal").on("click", function (e) {
        var modal_element = $('div.enable_session_lock');
        let url = `/sells/pos/sell_note_modal/${purchase_id}`;
        let modalId = 'modal-' + new Date().getTime();
        $.ajax({
            url: url,
            success: function (response) {
                let newModal = $('<div class="modal fade" id="' + modalId +
                    '" data-backdrop="static" data-keyboard="false">' +
                    +
                    '<div class="modal-content">' + response + +
                    '</div>' +
                    '</div>');
                $('body').append(newModal);
                newModal.modal('show');

                // Remove modal from DOM after closing
                newModal.on('hidden.bs.modal', function () {
                    $(this).remove();
                });
            }
        });
    });

    //history Modal
    $(".product_history").on("click", function (e) {

        let url = $(this).data('href');
        let modalId = 'modal-' + new Date().getTime();

        $.ajax({
            url: url,
            success: function (response) {
                let newModal = $('<div class="modal fade" id="' + modalId +
                    '" data-backdrop="static" data-keyboard="false">' +
                    +
                    '<div class="modal-content">' + response + +
                    '</div>' +
                    '</div>');
                $('body').append(newModal);
                newModal.modal('show');
                newModal.on('hidden.bs.modal', function () {
                    $(this).remove();
                });
            }
        });
    });

    $(document).on('blur', '.supplier-ref-no-input', function () {
        let id = $(this).data('id');
        let value = $(this).text().replace('#', '').trim();

        if (!value) {
            toastr.error('Supplier reference number cannot be empty');
            $(this).text('#' + $(this).data('value')); // Restore previous value
            return;
        }

        swal({
            title: 'Are you sure?',
            text: "You are about to update the supplier's reference number.",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willUpdate) => {
            if (willUpdate) {
                $.ajax({
                    method: 'POST',
                    url: '/purchases/update-ref-no/' + id,
                    data: {
                        supplier_ref_no: value,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            $('.supplier-ref-no-input[data-id="' + id + '"]').data('value', value);
                        } else {
                            toastr.error(result.msg);
                            $('.supplier-ref-no-input[data-id="' + id + '"]').text('#' + $('.supplier-ref-no-input[data-id="' + id + '"]').data('value'));
                        }
                    },
                    error: function (xhr, status, error) {
                        toastr.error('Failed to update supplier reference number');
                        $('.supplier-ref-no-input[data-id="' + id + '"]').text('#' + $('.supplier-ref-no-input[data-id="' + id + '"]').data('value'));
                    }
                });
            } else {
                $(this).text('#' + $(this).data('value'));
            }
        });
    });

    var search_fields = ['sku'];
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
                    // var isParent = $('#toggle_switch').prop('checked') ? true : false;
                    var url = '/purchases/get_products';
                    $.getJSON(
                        url,
                        { location_id: $('#location_id').val(), term: request.term, search_fields: search_fields, isParent: false },
                        response
                    );
                },
                minLength: 2,
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

    function buildPurchaseRow(result) {
        const product = result.product;
        const variation = result.variations[0];
            const unit = product.unit ? product.unit.actual_name : '';
        const subSku = variation.sub_sku || '';
        let productName = product.name + (subSku ? ' (' + subSku + ')' : '');
        if (product.type === 'variable') {
            const pvName = variation.product_variation ? variation.product_variation.name : '';
            const varName = variation.name || '';
            if (pvName || varName) {
                productName += ' (' + pvName + (pvName && varName ? ' : ' : '') + varName + ')';
            }
        }
        const quantity = 1; // Default to 1, or set as needed
        const unitPrice = variation.default_purchase_price || 0;
        const discount = 0; // Default, or set as needed
        const discountType = 'fixed'; // Default, or set as needed
        const subtotal = unitPrice * quantity;
        const hideTaxClass = $('#purchase_entry_table').data('hide-tax') === 'hide' ? 'hide' : '';

        return `
            <tr class="purchase-line" data-purchase-line-id="">
                <td></td>
                <td>
                    <div class="hide puchase_line_data">
                        <input type="text" class="hide data_product_id" value="${product.id}">
                        <input type="text" class="hide data_variation_id" value="${variation.id}">
                        <input type="text" class="hide data_purchase_line_id" value="">
                        <input type="text" class="hide data_quantity" value="${quantity}">
                        <input type="text" class="hide data_product_unit_id" value="${product.unit_id}">
                        <input type="text" class="hide data_pp_without_discount" value="${unitPrice}">
                        <input type="text" class="hide data_row_discount_percent" value="${discount}">
                        <input type="text" class="hide data_row_discount_type" value="${discountType}">
                        <input type="text" class="hide data_purchase_price" value="${unitPrice}">
                        <input type="text" class="hide data_purchase_line_tax_id" value="">
                        <input type="text" class="hide data_item_tax" value="">
                        <input type="text" class="hide data_purchase_price_inc_tax" value="${unitPrice}">
                        <input type="text" class="hide data_profit_percent" value="${variation.profit_percent}">
                    </div>
                    ${productName}
                </td>
                <td>${result.stock_qty} ${unit}</td>
                <td class="quantity">
                    <input style="width: 80px" type="text" name="quantity" class="form-control display_currency" value="${quantity}" data-currency_symbol="false" required>
                </td>
                <td class="unit_price">
                   <div class="input-group">
                    <span class="input-group-addon">$</span>
                   <input style="width: 80px" type="text" name="unit_price" class="form-control display_currency" value="${parseFloat(unitPrice).toFixed(2)}" data-currency_symbol="true" required>
                </div>
                    
                </td>
                <td style="width:190px" class='discount'>
                    <div class="tw-flex input-group">
                        <input type="text" name="discount" class="form-control inline_discounts display_currency" value="${discount}" data-currency_symbol="true" style="width:100px">
                        <select name="row_discount_type" class="form-control inline_discounts" style="width:60px">
                            <option value="fixed" ${discountType === 'fixed' ? 'selected' : ''}>$</option>
                            <option value="percentage" ${discountType === 'percentage' ? 'selected' : ''}>%</option>
                        </select>
                    </div>
                </td>
                <td class="no-print">
    <span class="display_currency unit_price_after_discount" data-currency_symbol="true">
       $ ${parseFloat(unitPrice).toFixed(2)}
    </span>
</td>
                <td class="no-print ${hideTaxClass}"><span class="display_currency" data-currency_symbol="true">${subtotal}</span></td>
                <td class="${hideTaxClass}"></td>
                <td class="${hideTaxClass}"></td>
                <td class="text-right">
                    <input type="hidden" class="subtotal_hidden" value="${subtotal}">
                    <span class="display_currency subtotal" data-currency_symbol="true">
    $ ${parseFloat(subtotal).toFixed(2)}
</span>
                </td>
                <td class="hide"></td>
                <td class='delete_purchase_row handle_lock'><i class="fa fa-trash" aria-hidden="true" style='color:red'></i></td>
            </tr>
        `;
    }

    function updateRowNumbers() {
        $('#purchase_order_table_modal tbody tr').each(function (index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    function get_purchase_entry_row(product_id, variation_id) {
        if (product_id) {
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
                url: '/purchases/get_purchase_entry_row/popup',
                data: data,
                success: function (result) {
                    var newRow = buildPurchaseRow(result);
                    $('#purchase_order_table_modal tbody').append(newRow);
                    updateRowNumbers();
                },
            });
        }
    }

    // Listen for changes in quantity input fields
    $(document).on('input', '#purchase_order_table_modal input[name="quantity"]', function () {
        var $row = $(this).closest('tr');
        var quantity = parseFloat($(this).val()) || 0;
        var unit_price = parseFloat($row.find('input[name="unit_price"]').val()) || 0;
        var discount = parseFloat($row.find('input[name="discount"]').val()) || 0;
        var discount_type = $row.find('select[name="row_discount_type"]').val();

        // Calculate discount amount
        var discount_amount = 0;
        if (discount_type === 'percentage') {
            discount_amount = unit_price * (discount / 100);
        } else {
            discount_amount = discount;
        }

        // Calculate final unit price after discount
        var final_unit_price = unit_price - discount_amount;
        if (final_unit_price < 0) final_unit_price = 0;

        // Calculate subtotal for the row
        var subtotal = final_unit_price * quantity;

        // Update the hidden and visible subtotal fields
        $row.find('.subtotal_hidden').val(subtotal);
        $row.find('.subtotal').text(subtotal.toFixed(2));

        // Update the unit price after discount field
        $row.find('.unit_price_after_discount').text(final_unit_price.toFixed(2));

        // Update the display_currency class (if you have a function for formatting)
        if (typeof __currency_convert_recursively === "function") {
            __currency_convert_recursively($row);
        }

        $row.find('.data_quantity').val(quantity);
        $row.find('.data_pp_without_discount').val(unit_price);
        $row.find('.data_row_discount_percent').val(discount_amount);
        $row.find('.data_row_discount_type').val(discount_type);
        $row.find('.data_purchase_price').val(final_unit_price);
        $row.find('.data_purchase_price_inc_tax').val(final_unit_price);

        // Update totals
        updateTotals();
    });

    // Listen for changes in unit price, discount, or discount type as well
    $(document).on('input change', '#purchase_order_table_modal input[name="unit_price"], #purchase_order_table_modal input[name="discount"], #purchase_order_table_modal select[name="row_discount_type"]', function () {
        $(this).closest('tr').find('input[name="quantity"]').trigger('input');
    });

    // Function to update net total and purchase total
    function updateTotals() {
        var total_before_tax = 0;
        $('#purchase_order_table_modal .subtotal_hidden').each(function () {
            total_before_tax += parseFloat($(this).val()) || 0;
        });

        // Update net total amount
        $('.total_before_tax').text(total_before_tax.toFixed(2));
        $('.data_total_before_tax').val(total_before_tax);


        // Calculate discount
        var discount_type = $('.data_discount_type').val();
        var discount_amount = parseFloat($('.data_discount_amount').val()) || 0;
        var discount = 0;
        if (discount_type === 'percentage') {
            discount = total_before_tax * (discount_amount / 100);
        } else {
            discount = discount_amount;
        }

        // Calculate tax
        var tax_amount = parseFloat($('.data_tax_amount').val()) || 0;

        // Shipping charges
        var shipping_charges = parseFloat($('.shipping_charges').val()) || 0;

        // Additional expenses
        var additional_expense_1 = parseFloat($('input[name="additional_expense_value_1"]').val()) || 0;
        var additional_expense_2 = parseFloat($('input[name="additional_expense_value_2"]').val()) || 0;
        var additional_expense_3 = parseFloat($('input[name="additional_expense_value_3"]').val()) || 0;
        var additional_expense_4 = parseFloat($('input[name="additional_expense_value_4"]').val()) || 0;

        // Final total calculation
        var final_total = total_before_tax - discount + tax_amount + shipping_charges + additional_expense_1 + additional_expense_2 + additional_expense_3 + additional_expense_4;

        // Update purchase total
        $('.final_total').text(final_total.toFixed(2));
        $('.data_final_total').val(final_total);

        // Update display_currency formatting if needed
        if (typeof __currency_convert_recursively === "function") {
            __currency_convert_recursively($('.final_total').closest('table'));
        }
    }

    // Helper to show error below input
    function showInputError($input, message) {
        let $error = $input.siblings('.input-error');
        if ($error.length === 0) {
            $error = $('<span class="input-error" style="color:red;font-size:12px;display:block;"></span>');
            $input.after($error);
        }
        $error.text(message);
    }
    function clearInputError($input) {
        $input.siblings('.input-error').text('');
    }

    // Validate Quantity
    $(document).on('input', '#purchase_order_table_modal input[name="quantity"]', function () {
        var $input = $(this);
        var value = $input.val();
        if (!/^[0-9]+(\.[0-9]+)?$/.test(value) || parseFloat(value) <= 0) {
            showInputError($input, 'Quantity must be a positive number.');
        } else {
            clearInputError($input);
        }
    });

    // Validate Unit Price
    $(document).on('input', '#purchase_order_table_modal input[name="unit_price"]', function () {
        var $input = $(this);
        var value = $input.val();
        if (!/^[0-9]+(\.[0-9]+)?$/.test(value) || parseFloat(value) < 0) {
            showInputError($input, 'Unit price must be a non-negative number.');
        } else {
            clearInputError($input);
        }
    });

    // Validate Discount
    $(document).on('input', '#purchase_order_table_modal input[name="discount"]', function () {
        var $input = $(this);
        var value = $input.val();
        var $row = $input.closest('tr');
        var discountType = $row.find('select[name="row_discount_type"]').val();
        var unitPrice = parseFloat($row.find('input[name="unit_price"]').val()) || 0;
        if (!/^[0-9]+(\.[0-9]+)?$/.test(value) || parseFloat(value) < 0) {
            showInputError($input, 'Discount must be a non-negative number.');
        } else if (discountType === 'percentage' && parseFloat(value) > 100) {
            showInputError($input, 'Discount percentage cannot exceed 100%.');
        } else if (discountType === 'fixed' && parseFloat(value) > unitPrice) {
            showInputError($input, 'Discount amount cannot exceed unit price.');
        } else {
            clearInputError($input);
        }
    });

    // Validate Discount Type
    $(document).on('change', '#purchase_order_table_modal select[name="row_discount_type"]', function () {
        var $select = $(this);
        var value = $select.val();
        if (value !== 'fixed' && value !== 'percentage') {
            // For select, show error after select
            let $error = $select.siblings('.input-error');
            if ($error.length === 0) {
                $error = $('<span class="input-error" style="color:red;font-size:12px;display:block;"></span>');
                $select.after($error);
            }
            $error.text('Invalid discount type selected.');
            $select.val('fixed');
        } else {
            $select.siblings('.input-error').text('');
        }
    });

    // Save button click handler to gather payload and log it
    $('#save_button_purchase').on('click', function (e) {
        e.preventDefault();

        var formData = new FormData();
        formData.append('_method', 'POST');
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Main fields
        formData.append('contact_id', $('.data_contact_id').val());
        formData.append('ref_no', $('.data_ref_no').val());
        formData.append('transaction_date', $('.data_transaction_date').val());
        formData.append('delivery_date', $('.data_delivery_date').val());
        formData.append('exchange_rate', $('.data_exchange_rate').val());
        formData.append('pay_term_number', $('.data_pay_term_number').val());
        formData.append('pay_term_type', $('.data_pay_term_type').val());

        formData.append('shipping_charges', $('.data_shipping_charges').val());
        formData.append('total_before_tax', $('.data_total_before_tax').val());
        formData.append('shipping_details', $('.data_shipping_details').val());
        formData.append('shipping_address', $('.data_shipping_address').val());
        formData.append('shipping_status', $('.data_shipping_status').val());
        formData.append('delivered_to', $('.data_delivered_to').val());
        formData.append('final_total', $('.data_final_total').val());
        formData.append('discount_type', $('.data_discount_type').val());
        formData.append('discount_amount', $('.data_discount_amount').val());
        formData.append('tax_id', $('.data_tax_id').val());
        formData.append('tax_amount', $('.data_tax_amount').val());
        formData.append('additional_notes', $('.data_additional_notes').val());
        formData.append('search_product', $('#search_product').val() || '');

        // Additional Expenses
        for (let i = 1; i <= 4; i++) {
            formData.append('additional_expense_key_' + i, $('[name="additional_expense_key_' + i + '"]').val() || '');
            formData.append('additional_expense_value_' + i, $('[name="additional_expense_value_' + i + '"]').val() || '0.00');
        }

        // Handle purchase lines
        $('#purchase_order_table_modal tbody tr').each(function (index) {
            const $row = $(this);
            const prefix = `purchases[${index}]`;

            formData.append(`${prefix}[product_id]`, $row.find('.data_product_id').val());
            formData.append(`${prefix}[variation_id]`, $row.find('.data_variation_id').val());

            let lineId = $row.find('.data_purchase_line_id').val();
            if (lineId) formData.append(`${prefix}[purchase_line_id]`, lineId);

            formData.append(`${prefix}[quantity]`, $row.find('.data_quantity').val());
            formData.append(`${prefix}[product_unit_id]`, $row.find('.data_product_unit_id').val());
            formData.append(`${prefix}[pp_without_discount]`, $row.find('.data_pp_without_discount').val());
            formData.append(`${prefix}[row_discount_type]`, $row.find('.data_row_discount_type').val());
            formData.append(`${prefix}[discount_percent]`, $row.find('.data_row_discount_percent').val());
            formData.append(`${prefix}[purchase_price]`, $row.find('.data_purchase_price').val());
            formData.append(`${prefix}[purchase_line_tax_id]`, $row.find('.data_purchase_line_tax_id').val());
            formData.append(`${prefix}[item_tax]`, $row.find('.data_item_tax').val());
            formData.append(`${prefix}[purchase_price_inc_tax]`, $row.find('.data_purchase_price_inc_tax').val());
            formData.append(`${prefix}[profit_percent]`, $row.find('.data_profit_percent').val());
        });

        // Optional: Handle shipping_documents file if any
        const shippingFiles = $('[name="shipping_documents[]"]')[0];
        if (shippingFiles && shippingFiles.files.length > 0) {
            for (let i = 0; i < shippingFiles.files.length; i++) {
                formData.append('shipping_documents[]', shippingFiles.files[i]);
            }
        }

        // Submit via AJAX
        $.ajax({
            url: `/update-purchase-orders-modal/${purchase_id}`,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                // Reset button state
                $('#save_button_purchase').prop('disabled', false).html('Save');

                if (response.success) {
                    // Show success message
                    toastr.success(response.msg || 'Purchase order updated successfully!');

                    // Close the modal after a short delay
                    setTimeout(function () {
                        $('.modal').modal('hide');
                        // Optionally refresh the page or update the parent view
                        if (typeof window.parent !== 'undefined' && window.parent.location) {
                            window.parent.location.reload();
                        }
                    }, 1500);
                } else {
                    // Show error message
                    toastr.error(response.msg || 'Failed to update purchase order. Please try again.');
                }
            },
            error: function (xhr) {
                // Reset button state
                $('#save_button_purchase').prop('disabled', false).html('Save');

                let errorMessage = 'An error occurred while updating the purchase order.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
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
                console.error('Error:', xhr.responseText);
            }
        });

    });

    // Delete purchase row functionality
    $(document).on('click', '.delete_purchase_row', function (e) {
        e.preventDefault();

        var $row = $(this).closest('tr');
        var productName = $row.find('td:eq(1)').text().trim();

        // Show confirmation dialog
        swal({
            title: 'Are you sure?',
            text: `Do you want to remove "${productName}" from the purchase order?`,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                // Remove the row
                $row.remove();

                // Update row numbers
                updateRowNumbers();

                // Update totals
                updateTotals();

                // Show success message
                toastr.success('Product removed from purchase order successfully!');
            }
        });
    });

});
