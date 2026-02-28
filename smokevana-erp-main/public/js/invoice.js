$(document).ready(function () {
    // activity modal
    $("#openActivityModal").on("click", function (e) {
        let url = `/sells/pos/activity_modal/${$(this).data('href')}`;;
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
    $(".sendNotification").on("click", function (e) {
        let url = $(this).data('href');;
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
        let url = `/sells/pos/sell_note_modal/${$(this).data('href')}`;
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
    $('.product_history').on("click", function (e) {
        e.preventDefault();
        // Get all checked checkboxes
        let checkedCheckboxes = $('.check_box_td_input:checked');

        // Check if multiple checkboxes are selected
        if (checkedCheckboxes.length > 1) {
            toastr.warning('Multiple items selected. Please select only one item.');
            return;
        }

        // Check if no checkbox is selected
        if (checkedCheckboxes.length === 0) {
            toastr.warning('Please select an item');
            return;
        }

        // Get the value of the single selected checkbox
        let selectedValue = checkedCheckboxes.data('variation-id');
        let customerId = checkedCheckboxes.data('customer-id');
        console.log('Selected value:', selectedValue);
        let modalId = 'modal-history-' + new Date().getTime();

        $.ajax({
            url: '/sells/pos/history_modal?variation_id=' + selectedValue + '&dateRange=90',
            method: 'GET',
            headers: {
                'Accept': 'text/html'
            },
            success: function (response) {
                let newModal = $('<div class="modal fade" id="' + modalId +
                    '" data-backdrop="static" data-keyboard="false">' +
                    +
                    '<div class="modal-content">' + response +
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

    $('#edit_shipping_address').on('click', function () {
        let url = `/sells/pos/edit_shipping_address/${$(this).data('href')}`;
        $.ajax({
            url: url,
            success: function (response) {
                let newModal = $('<div class="modal fade" id="' + 'AddressModal' +
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

    //edit Product price Modal
    $(".ancher_list").on("click", function () {
        let url = $(this).data('href');
        let modalId = 'modal-' + new Date().getTime(); // Generate a unique ID for the new modal

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

    // Toggle menu visibility
    $("#toggleDropdown").on("click", function (event) {
        event.stopPropagation(); // Prevent event bubbling
        $("#columnMenu-table1").toggle();
    });

    // Close dropdown if clicked outside
    $(document).on("click", function (event) {
        if (!$(event.target).closest("#toggleDropdown, #columnMenu-table1").length) {
            $("#columnMenu-table1").hide();
        }
    });

    $(document).on("keydown", ".quantity input, .discount input, .unit_price input, .price-column input", function (event) {
        let currentInput = $(this);
        let currentTd = currentInput.closest("td");
        let currentTr = currentTd.closest("tr");
        let columnIndex = currentTd.index();
        let nextInput;

        switch (event.key) {
            case "ArrowRight":
                nextInput = currentTd.next().find("input");
                break;
            case "ArrowLeft":
                nextInput = currentTd.prev().find("input");
                break;
            case "ArrowDown":
                nextInput = currentTr.next().find("td").eq(columnIndex).find("input");
                break;
            case "ArrowUp":
                nextInput = currentTr.prev().find("td").eq(columnIndex).find("input");
                break;
        }

        if (nextInput && nextInput.length) {
            nextInput.focus().select();
            event.preventDefault(); // Prevent default scrolling behavior
        }
    });

    $(document).on("focus", ".sell-line-row input", function () {
        $(this).closest("tr").addClass("tw-bg-yellow-100");
    });

    $(document).on("blur", ".sell-line-row input", function () {
        $(this).closest("tr").removeClass("tw-bg-yellow-100");
    });

    $('.delete_row').on('click', function () {
        let tr = $(this).closest('tr');
        tr.remove();
        updateTableTotals()
    });

    function showErrorInTd($input, message) {
        $input.css('border', '1px solid red');
        let $td = $input.closest('td');

        // Check if error already exists in this <td>
        let $error = $td.find('.error-message');
        if ($error.length === 0) {
            $error = $('<div class="error-message"></div>').css({
                'color': 'red',
                'font-size': '12px',
                'margin-top': '2px',
                'display': 'block'
            });
            $td.append($error);
        }

        $error.text(message).css('display', 'block');
        $input.focus();
    }

    function clearErrorInTd($input) {
        $input.css('border', '');
        $input.closest('td').find('.error-message').css('display', 'none');
    }

    // invoice update in form

    $("#save_button_invoice").on("click", function (e) {
        e.preventDefault();
        $("#save_button_invoice").prop('disabled', true);


        const location_id = $('.location_id_data').text()
        const contact_id = $('.contact_id_data').text()
        const delivered_to = $('.deliverd_to_data').text()
        const delivery_person = $('.delivery_person_data').text()
        const discount_amount = $('.discount_amount_data').text()
        const discount_type = $('.discount_type_data').text()
        const final_total = $('.final_total_data').text()
        const invoice_no = $('.invoice_no_data').text()
        const is_direct_sale = $('.is_direct_sale_data').text()
        const pay_term_number = $('.pay_term_number_data').text()
        const pay_term_type = $('.pay_term_type_data').text()
        const rp_redeemed = $('.rp_redeemed_data').text()
        const rp_redeemed_amount = $('.rp_redeemed_amount_no_data').text()
        const sale_note = $('.sale_note_data').text()
        const sell_price_tax = $('.sell_price_tax_data').text()
        const shipping_address = $('.shipping_address_data').text()
        const shipping_charges = $('.shipping_charges_data').text()
        const shipping_details = $('.shipping_details_data').text()
        const shipping_status = $('.shipping_status_data').text()
        const status = $('.status_data').text()
        const tax_calculation_amount = $('.tax_calculation_amount_data').text()
        const tax_rate_id = $('.tax_rate_id_data').text()
        const transaction_date = $('.transaction_date_data').text()

        let isValid = true;

        $(".sell-line-row").each(function (index) {
            let row = $(this);

            const quantity = parseFloat(row.find('input[name="quantity"]').val()) || 0;
            const unitPrice = parseFloat(row.find('input[name="unit_price"]').val()) || 0;
            const discount = parseFloat(row.find('input[name="discount"]').val()) || 0;
            const discountType = row.find('select[name="discount_type"]').val();
            const enable_stock = parseInt(row.find('.enable_stock_data').text()) || 0;
            const available_qty = parseFloat(row.find('.available_qty_data').text()) || 0;
            let allowed_qty = max_sell_qty + available_qty;

            let discount_rate = unitPrice;
            if (discountType === 'fixed') {
                discount_rate = unitPrice;
            } else {
                discount_rate = 100;
            }

            const $quantityInput = row.find('input[name="quantity"]');
            const $unitPriceInput = row.find('input[name="unit_price"]');
            const $discountInput = row.find('input[name="discount"]');

            // Unit price validation
            if (isNaN(unitPrice) || unitPrice <= 0) {
                showErrorInTd($unitPriceInput, 'Unit price must be a number > 0');
                isValid = false;
                $("#save_button_invoice").prop('disabled', false);
            } else {
                clearErrorInTd($unitPriceInput);
            }

            // Quantity validation
            let variation_id = row.attr('data-variation-id');
            let same_variation_quantity = 0;
            let is_same_variation = false;
            let currunt_row_index = row.index();
            $(".sell-line-row[data-variation-id='" + variation_id + "']").each(function () {
                allowed_qty = Math.max(allowed_qty, parseFloat($(this).find('.available_qty_data').text()) || 0);
                if (currunt_row_index == $(this).index()) {
                    return;
                }
                let same_variation_row = $(this);
                let other_quantity = parseFloat(same_variation_row.find('input[name="quantity"]').val()) || 0;
                same_variation_quantity += other_quantity;
                if (same_variation_row.attr('data-variation-id') == variation_id) {
                    is_same_variation = true;
                }
            });
            if (isNaN(quantity) || quantity <= 0) {
                showErrorInTd($quantityInput, 'Quantity must be a number greater than 0');
                isValid = false;
                $("#save_button_invoice").prop('disabled', true);
            } else if (enable_stock == 1 && quantity > allowed_qty) {
                showErrorInTd($quantityInput, `Quantity must be equal or below ${allowed_qty}`);
                isValid = false;
                $("#save_button_invoice").prop('disabled', false);
            } else if (enable_stock == 1 && is_same_variation && quantity + same_variation_quantity > allowed_qty) {
                showErrorInTd($quantityInput, `Same Product already have ${same_variation_quantity + quantity} quantity, Quantity must be equal or below ${allowed_qty}`);
                isValid = false;
                $("#save_button_invoice").prop('disabled', false);
            } else {
                clearErrorInTd($quantityInput);
            }

            // Discount validation
            if (isNaN(discount) || discount < 0) {
                showErrorInTd($discountInput, 'Discount must not be negative');
                isValid = false;
                $("#save_button_invoice").prop('disabled', false);
            } else if (discount > discount_rate) {
                showErrorInTd($discountInput, 'Discount cannot exceed unit price');
                isValid = false;
                $("#save_button_invoice").prop('disabled', false);
            } else {
                clearErrorInTd($discountInput);
            }


        })

        if (!isValid) return;


        let products = {};

        $(".sell-line-row").each(function (index) {
            let row = $(this);
            let sellLineData = {
                product_type: row.find('.product_type_data').text(),
                transaction_sell_lines_id: row.find('.transaction_sell_lines_id_data').text(),
                product_id: row.find('.product_id_data').text(),
                variation_id: row.find('.variation_id_data').text(),
                enable_stock: row.find('.enable_stock_data').text(),
                quantity: row.find('.quantity_data').text(),
                product_unit_id: row.find('.product_unit_id_data').text(),
                base_unit_multiplier: row.find('.base_unit_multiplier_data').text(),
                unit_price: row.find('.unit_price_data').text(),
                line_discount_amount: row.find('.line_discount_amount_data').text(),
                line_discount_type: row.find('.line_discount_type_data').text(),
                item_tax: row.find('.item_tax_data').text(),
                tax_id: row.find('.tax_id_data').text(),
                unit_price_inc_tax: row.find('.unit_price_inc_tax_data').text(),
                sell_line_note: row.find('.sell_line_note_data').text(),
                sub_sku: row.find('.sub_sku_data').text()
            };

            products[index] = sellLineData;
        });
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const data = {
            "_method": "PUT",
            "_token": csrfToken,
            "is_save_and_print": "0",
            "location_id": location_id,
            "contact_id": contact_id,
            "pay_term_number": pay_term_number,
            "pay_term_type": pay_term_type,
            "transaction_date": transaction_date,
            "status": status,
            "invoice_no": invoice_no,
            "sell_document": {},
            "sell_price_tax": sell_price_tax,
            "products": products,
            "search_product": "",
            "sale_note": sale_note,
            "shipping_charges": shipping_charges,
            "discount_type": discount_type,
            "discount_amount": discount_amount,
            "rp_redeemed": rp_redeemed,
            "rp_redeemed_amount": rp_redeemed_amount,
            "tax_rate_id": tax_rate_id,
            "tax_calculation_amount": tax_calculation_amount,
            "is_direct_sale": is_direct_sale,
            "shipping_details": shipping_details,
            "shipping_address": shipping_address,
            "shipping_status": shipping_status,
            "delivered_to": delivered_to,
            "delivery_person": delivery_person,
            "shipping_documents": {},
            "additional_expense_key_1": "",
            "additional_expense_value_1": "0.00",
            "additional_expense_key_2": "",
            "additional_expense_value_2": "0.00",
            "additional_expense_key_3": "",
            "additional_expense_value_3": "0.00",
            "additional_expense_key_4": "",
            "additional_expense_value_4": "0.00",
            "final_total": final_total,
            "payment": {
                "0": {
                    "amount": "0.00",
                    "paid_on": "05/30/2025 12:55 AM",
                    "method": "cash",
                    "card_number": "",
                    "card_holder_name": "",
                    "card_transaction_number": "",
                    "card_type": "credit",
                    "card_month": "",
                    "card_year": "",
                    "card_security": "",
                    "cheque_number": "",
                    "bank_account_number": "",
                    "transaction_no_1": "",
                    "transaction_no_2": "",
                    "transaction_no_3": "",
                    "transaction_no_4": "",
                    "transaction_no_5": "",
                    "transaction_no_6": "",
                    "transaction_no_7": "",
                    "note": ""
                },
                "change_return": {
                    "method": "cash",
                    "card_number": "",
                    "card_holder_name": "",
                    "card_transaction_number": "",
                    "card_type": "credit",
                    "card_month": "",
                    "card_year": "",
                    "card_security": "",
                    "cheque_number": "",
                    "bank_account_number": "",
                    "transaction_no_1": "",
                    "transaction_no_2": "",
                    "transaction_no_3": "",
                    "transaction_no_4": "",
                    "transaction_no_5": "",
                    "transaction_no_6": "",
                    "transaction_no_7": ""
                }
            },
            "change_return": "0.00"
        }
        const url = $('.update_url_data').text();
        $.ajax({
            url: url,
            method: "PUT",
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function (response) {
                if (response.success === 1) {
                    toastr.success(response.msg);
                    $("#save_button_invoice").prop('disabled', false);
                    if (typeof sell_table !== 'undefined' && sell_table.ajax) {
                        sell_table.ajax.reload();
                    }

                    $('.view_modal').modal('hide');
                    // updateTableTotals()
                } else {
                    toastr.error(response.msg);
                    $("#save_button_invoice").prop('disabled', false);
                    if (typeof sell_table !== 'undefined' && sell_table.ajax) {
                        sell_table.ajax.reload();
                    }

                    updateTableTotals()
                }

            },
            error: function (error) {
                console.error('Form submission failed:', error);
                $("#save_button_invoice").prop('disabled', false);
                updateTableTotals()
            }
        });
        updateTableTotals()
        if (typeof sell_table !== 'undefined' && sell_table.ajax) {
            sell_table.ajax.reload();
        }

    });

    $('input[name="quantity"]').on('input', function () {
        let quantity = parseFloat($(this).val()) || 0;
        if (quantity <= 0) {
            $(this).val(1); // Set to minimum of 1
        }
        let row = $(this).closest('tr');
        updateRowTotals(row);
    }).on('blur', function () {
        let quantity = parseFloat($(this).val()) || 0;
        if (quantity <= 0) {
            $(this).val(1); // Set to minimum of 1
            let row = $(this).closest('tr');
            updateRowTotals(row);
        }
    });

    $('input[name="unit_price"]').on('input', function () {
        let row = $(this).closest('tr');
        updateRowTotals(row);
    });

    $('input[name="discount"]').on('input', function () {
        let row = $(this).closest('tr');
        updateRowTotals(row);
    });

    $('select[name="discount_type"]').on('change', function () {
        let row = $(this).closest('tr');
        updateRowTotals(row);
    });

    $(document).on('change', "input[name='unit_price'], input[name='discount']", function () {
        let formattedValue = parseFloat($(this).val()).toFixed(0);
        $(this).val(formattedValue);
        $(this).data("original-value", formattedValue);
    });

    var userTaxState = $('#customer_state').val() ?? 'IL'
    var locationTaxCharges = [];

    if ($('#search_product').length && !$('#toggle_switch').prop('checked')) {
        $('#search_product')
            .autocomplete({
                delay: 1000,
                minLength: 4,
                source: function (request, response) {
                    let is_metrix = $('#toggle_switch').prop('checked');
                    let price_group = $('#price_group').val() || '';
                    let search_fields = $('.search_fields:checked').map(function () {
                        return $(this).val();
                    }).get();

                    $.getJSON('/products/list', {
                        price_group: price_group,
                        location_id: $('#location_id').val(),
                        term: request.term,
                        not_for_selling: 0,
                        search_fields: search_fields,
                        is_metrix: is_metrix,
                    }, response);
                },
                response: function (event, ui) {
                    if (ui.content.length === 0) {
                        toastr.warning(LANG.no_products_found);
                    } else if (ui.content.length === 1) {
                        ui.item = ui.content[0];
                        $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');
                    }
                },
                select: function (event, ui) {
                    let is_metrix = $('#toggle_switch').prop('checked');
                    if (is_metrix) {
                        event.preventDefault();
                        let productId = ui.item.id;
                        let priceGroupId = $('#hidden_price_group').val();
                        let modalUrl = `/sells/pos/getmatrixproduct/${productId}/${priceGroupId}`;

                        // Open the modal
                        $.ajax({
                            url: modalUrl,
                            dataType: 'html',
                            success: function (result) {
                                $('.view_modal').html(result).modal('show');
                            }
                        });
                        return false;
                    }
                    let is_overselling_allowed = $('#is_overselling_allowed').length > 0;
                    let for_so = ($('#sale_type').val() == 'sales_order');
                    let is_draft = ($('#status').val() == 'quotation' || $('#status').val() == 'draft');

                    if (
                        ui.item.enable_stock != 1 ||
                        ui.item.qty_available > 0 ||
                        is_overselling_allowed ||
                        for_so ||
                        is_draft
                    ) {
                        $(this).val('');
                        get_purchase_entry_row(ui.item.product_id, ui.item.variation_id);
                    } else {
                        toastr.warning(LANG.out_of_stock);
                    }

                    return false;
                }
            })
            .autocomplete('instance')._renderItem = function (ul, item) {
                var is_metrix = $('#toggle_switch').prop('checked') ? true : false;
                if (is_metrix) {
                    return $('<li>')
                        .append(`<button>${item.sku}  ${item.name}</button>`)
                        .appendTo(ul);
                }
                else {
                    var is_overselling_allowed = false;
                    if ($('input#is_overselling_allowed').length) {
                        is_overselling_allowed = true;
                    }

                    var for_so = false;
                    if ($('#sale_type').length && $('#sale_type').val() == 'sales_order') {
                        for_so = true;
                    }
                    var is_draft = false;
                    if (
                        $('input#status') &&
                        ($('input#status').val() == 'quotation' || $('input#status').val() == 'draft')
                    ) {
                        var is_draft = true;
                    }

                    if (
                        item.enable_stock == 1 &&
                        item.qty_available <= 0 &&
                        !is_overselling_allowed &&
                        !for_so &&
                        !is_draft
                    ) {
                        var string = '<li class="ui-state-disabled">' + item.name;
                        if (item.type == 'variable') {
                            string += '-' + item.variation;
                        }
                        var selling_price = item.selling_price;
                        if (item.variation_group_price) {
                            selling_price = item.variation_group_price;
                        }
                        string +=
                            ' (' +
                            item.sub_sku +
                            ')' +
                            '<br> Price: ' +
                            __currency_trans_from_en(
                                selling_price,
                                false,
                                false,
                                __currency_precision,
                                true
                            ) +
                            ' (Out of stock) </li>';
                        return $(string).appendTo(ul);
                    } else {
                        var string = '<div>' + item.sub_sku + "   ";
                        string += item.name
                        if (item.type == 'variable') { string += '-' + item.variation; }
                        string += '</div>';

                        return $('<li>').append(string).appendTo(ul);
                    }
                }

            };
    }
    // Barcode scanner support on Invoice search
    (function initInvoiceBarcodeScanner() {
        if (!$('#search_product').length) { return; }

        function scanAndAddToInvoice(scannedCode) {
            let is_metrix = $('#toggle_switch').prop('checked');
            let price_group = $('#price_group').val() || '';
            let search_fields = $('.search_fields:checked').map(function () {
                return $(this).val();
            }).get();

            $.getJSON('/products/list', {
                price_group: price_group,
                location_id: $('#location_id').val(),
                term: scannedCode,
                not_for_selling: 0,
                search_fields: search_fields,
                is_metrix: is_metrix,
            }, function (items) {
                if (!items || items.length === 0) {
                    toastr.warning(LANG.no_products_found);
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
                        const productId = chosen.id;
                        const priceGroupId = $('#hidden_price_group').val();
                        const modalUrl = `/sells/pos/getmatrixproduct/${productId}/${priceGroupId}`;
                        $.ajax({
                            url: modalUrl,
                            dataType: 'html',
                            success: function (result) {
                                $('.view_modal').html(result).modal('show');
                            }
                        });
                    } else {
                        get_purchase_entry_row(chosen.product_id, chosen.variation_id);
                    }
                    $('#search_product').val('').focus();
                } else {
                    // Multiple matches and no exact match; open suggestions
                    $('#search_product').val(scannedCode).focus().autocomplete && $('#search_product').autocomplete('search');
                }
            });
        }

        // Attach scanner if onScan is available
        try {
            onScan.attachTo(document, {
                suffixKeyCodes: [13],
                reactToPaste: true,
                minLength: 3,
                onScan: function (sCode) {
                    // Avoid when any modal is open to prevent conflicts
                    if ($('.modal.show').length) { return; }
                    const active = document.activeElement;
                    if ($(active).is('input,textarea,[contenteditable=true]') && !$(active).is('#search_product')) { return; }
                    scanAndAddToInvoice(sCode);
                },
                onScanError: function () { }
            });
        } catch (e) {
            // Fallback: pressing Enter in search triggers add
            $('#search_product').on('keypress', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    const val = $(this).val().trim();
                    if (val) { scanAndAddToInvoice(val); }
                }
            });
        }
    })();

    function get_purchase_entry_row(product_id, variation_id) {
        if (!product_id) {
            console.error('Product ID is required');
            return;
        }

        // Check if the variation already exists in the table
        var location_id = $('#location_id').val();
        var supplier_id = $('#supplier_id').val();

        var data = {
            product_id: product_id,
            row_count: 2,
            variation_id: variation_id,
            location_id: location_id,
            supplier_id: supplier_id
        };
        const customer_id = $('#customer_id_id').val();

        data.is_purchase_order = true;

        $.ajax({
            method: 'GET',
            url: `/sells/pos/get_erp_new_product_row?product_id=${product_id}&variation_id=${variation_id}&customer_id=${customer_id}`,
            success: function (data) {
                try {
                    let product = data;
                    if (!product) {
                        console.error('Invalid product data received');
                        return;
                    }

                    let recallprice = product.customer_price_recall ?? 0;

                    let ml = product.ml || 0;
                    let ct = product.ct || 0;
                    let price = recallprice ? recallprice : product.product_price || 0;
                    let locationTaxType = product.locationTaxType || [];

                    getProductTax(ml, ct, price, userTaxState, locationTaxType)
                        .then((res) => {
                            const { status, tax } = res;
                            const taxOnItem = status ? tax : 0;
                            console.log('Tax calculated:', taxOnItem);

                            // Get the table body
                            const total = parseFloat(price) + parseFloat(taxOnItem);
                            const tbody = $('#sellsModalTable tbody');
                            const customer_id = $('#customer_id_id').val();
                            const transaction_id = $('#transaction_id_id').val();
                            const picking_status = $('.picking_status').val();

                            // Create a new row
                            const newRow = $('<tr>').addClass('sell-line-row').attr('data-variation-id', product.variation_id).attr('data-sellline-id', 0).attr('data-transaction-id', transaction_id).attr('data-product-id', product.product_id).attr('data-ml', product.ml ?? 0).attr('data-ct', product.ct ?? 0).attr('data-locationTaxType', []);

                            // Get the current row count
                            const rowCount = tbody.find('tr').length + 1;

                            // Create the row content
                            const rowContent = `
                            <td class="check_box_td">
                            <input type="checkbox" class="check_box_td_input" name="check_box_td_input" data-variation-id="${product.variation_id}" data-customer-id="${customer_id}">  
                            <div id="Product_payload_row" class='hide'>
                                    <div class="base_unit_multiplier_data">1</div>
                                    <div class="enable_stock_data">${product.enable_stock}</div>
                                    <div class="item_tax_data">${taxOnItem}</div>
                                    <div class="line_discount_amount_data"></div>
                                    <div class="line_discount_type_data">fixed</div>
                                    <div class="product_id_data">${product.product_id}</div>
                                    <div class="product_type_data">${product.product_type}</div>
                                    <div class="product_unit_id_data">${product.unit_id}</div>
                                    <div class="quantity_data">1</div>
                                    <div class="sell_line_note_data"></div>
                                    <div class="tax_id_data"></div>
                                    <div class="unit_price_data">${price}</div>
                                    <div class="unit_price_inc_tax_data">${total}</div>
                                    <div class="variation_id_data">${product.variation_id}</div>
                                    <div class="available_qty_data">${product.stock}</div>
                                    <div class="sub_sku_data">${product.sub_sku}</div>
                                </div>
                                </td>
                         
                            <td>
                                <a href="#" data-href="/sells/pos/edit_price_product_modal/${product.product_id}/0" 
                                   data-container=".view_modal" tabindex="2" class="ancher_list tw-text-black">
                                    ${product.product_name + " - Flavor - " + product.variation_name + ", " + product.sku} 
                                </a>
                                <button type="button" class="btn btn-danger btn-xs remove-row" style="display: none; position: absolute; right: 10px;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                           
                            <td style="width: 100px;" class="text-white">
                                ${product.all_group_prices ? product.all_group_prices.map((price, index) => {
                                const priceTypes = ['silver', 'gold', 'platinum', 'lowest', 'diamond'];
                                return `
                                        <div class="${priceTypes[index]}-price" style="background-color: rgb(151, 103, 0);" data-variation-id="${product.variation_id}">
                                            ${price.price_inc_tax ?
                                        `<span class="display_currency" data-currency_symbol="true">$ ${parseFloat(price.price_inc_tax).toFixed(2)}</span>` :
                                        `<span class="display_currency" data-currency_symbol="true">$ ${parseFloat(product.product_price).toFixed(2)}</span>`
                                    }
                                        </div>
                                    `;
                            }).join('') : ''}
                            </td>
                            ${picking_status == 'PICKED' ? "<td class='text-center'>0</td><td class='text-center'>0</td>" : ""}
                            <td class="quantity" data-variation-id="${product.variation_id}" style="width: 100px">
                                <input style="width: 80px" type="text" name="quantity" 
                                       class="form-control display_currency" 
                                       value="1"
                                       min="0"
                                       data-currency_symbol="true" required>
                            </td>
                            <td class="unit_price" data-variation-id="${product.variation_id}" 
                                data-min-price="${product.all_group_prices?.[3]?.price_inc_tax || 1}" style="width: 100px">
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input style="width: 80px" type="text" name="unit_price" 
                                           class="form-control display_currency" 
                                           value="${parseFloat(price).toFixed(2)}"
                                           data-currency_symbol="true" required>
                                </div>
                            </td>
                            
                            <td class="discount" data-variation-id="${product.variation_id}" style="width: 170px">
                                <div class="input-group" style="display: flex">
                                    <input type="text" name="discount" class="form-control input-sm inline_discounts input_number" 
                                           style="width:100px" required min="0" step="any" max="100" value="0">
                                    <select name="discount_type" class="form-control input-sm inline_discounts" 
                                            style="max-width: 70px; border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                        <option value="fixed" selected>$</option>
                                        <option value="percentage">%</option>
                                    </select>
                                </div>
                            </td>
                            <td style="width: 100px">
                                <span class="display_currency" data-currency_symbol="true" id="tax_rate" id='td_tax'>$ ${taxOnItem.toFixed(2)}</span>
                                <input style="width: 80px" type="text" name="tax_rate_col" disabled
                                       class="form-control display_currency hide" value="${taxOnItem}"
                                       data-currency_symbol="true">
                            </td>
                            <td style="width: 100px">
                                <span class="display_currency" data-currency_symbol="true">$ 
                                    ${parseFloat(total).toFixed(2)}
                                </span>
                            </td>
                            <td style="width: 100px">
                                <span class="display_currency" data-currency_symbol="true" id='td_subtotal'>$ 
                                    ${parseFloat(total).toFixed(2)}
                                </span>
                            </td>
                            <td class='delete_new_row handle_lock'><button><i class="fa fa-trash" aria-hidden="true" style="color: red"></i></button></td>
                        `;

                            // Append the row to the table
                            newRow.html(rowContent);
                            tbody.append(newRow);
                            let last_show = $(".column-toggle").val();
                            $(".silver-price, .gold-price, .platinum-price, .lowest-price, .diamond-price, .no-price").hide();
                            // Initialize any necessary event handlers
                            initializeRowEventHandlers(newRow);
                        })
                        .catch((err) => {
                            console.error('Error calculating tax:', err);
                            toastr.error('Error calculating tax for the product');
                        });
                } catch (error) {
                    console.error('Error processing product data:', error);
                    toastr.error('Error processing product data');
                }
            },
            error: function (xhr, status, error) {
                console.error('Ajax error:', error);
                toastr.error('Error fetching product data');
            }
        });
    }

    // Helper function to initialize event handlers for the new row
    function initializeRowEventHandlers(row) {
        // Initialize quantity change handler
        row.find('input[name="quantity"]').on('input', function () {
            let quantity = parseFloat($(this).val()) || 0;
            if (quantity <= 0) {
                $(this).val(1); // Set to minimum of 1
            }
            updateRowTotals(row);
        }).on('blur', function () {
            let quantity = parseFloat($(this).val()) || 0;
            if (quantity <= 0) {
                $(this).val(1); // Set to minimum of 1
                updateRowTotals(row);
            }
        });

        // Initialize unit price input handler
        row.find('input[name="unit_price"]').on('input', function () {
            updateRowTotals(row);
        });

        // Initialize discount input handler
        row.find('input[name="discount"]').on('input', function () {
            updateRowTotals(row);
        });
        row.find('select[name="discount_type').on('change', function () {
            updateRowTotals(row);
        });

        $('.product_history').on("click", function (e) {
            e.preventDefault();
            let url = $(this).data('href');
            let modalId = 'modal-' + new Date().getTime();

            $.ajax({
                url: url,
                method: 'GET',
                headers: {
                    'Accept': 'text/html'
                },
                success: function (response) {
                    let newModal = $('<div class="modal fade" id="' + modalId +
                        '" data-backdrop="static" data-keyboard="false">' +
                        +
                        '<div class="modal-content">' + response +
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
        //edit Product price Modal for every row
        $(".ancher_list").on("click", function () {
            let url = $(this).data('href');
            let modalId = 'modal-' + new Date().getTime(); // Generate a unique ID for the new modal

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
        $('.delete_new_row').on('click', function () {
            $(this).closest('tr').remove();
            updateTableTotals()
        });
        updateTableTotals()
    }

    const over_selling_qty = parseFloat($('.over_selling_qty').val())
    const is_over_selling_allowd = parseFloat($('.is_over_selling_allowd').val())
    var max_sell_qty = 0;

    if (is_over_selling_allowd == '1') {
        if (over_selling_qty) {
            max_sell_qty = over_selling_qty;
        } else {
            max_sell_qty = 5000;
        }
    }
    // Helper function to update row totals for 
    function updateRowTotals(row) {
        const quantity = parseFloat(row.find('input[name="quantity"]').val()) || 0;
        const unitPrice = parseFloat(row.find('input[name="unit_price"]').val()) || 0;
        const discount = parseFloat(row.find('input[name="discount"]').val()) || 0;
        const discountType = row.find('select[name="discount_type"]').val();
        const enable_stock = parseInt(row.find('.enable_stock_data').text()) || 0;
        const available_qty = parseFloat(row.find('.available_qty_data').text()) || 0;
        let allowed_qty = max_sell_qty + available_qty;

        let discount_rate = unitPrice;
        if (discountType === 'fixed') {
            discount_rate = unitPrice;
        } else {
            discount_rate = 100;
        }

        const $quantityInput = row.find('input[name="quantity"]');
        const $unitPriceInput = row.find('input[name="unit_price"]');
        const $discountInput = row.find('input[name="discount"]');

        let isValid = true;

        // Unit price validation
        if (isNaN(unitPrice) || unitPrice <= 0) {
            showErrorInTd($unitPriceInput, 'Unit price must be a number > 0');
            isValid = false;
        } else {
            clearErrorInTd($unitPriceInput);
        }

        // Quantity validation
        let variation_id = row.attr('data-variation-id');
        let same_variation_quantity = 0;
        let is_same_variation = false;
        let currunt_row_index = row.index();
        $(".sell-line-row[data-variation-id='" + variation_id + "']").each(function () {
            allowed_qty = Math.max(allowed_qty, parseFloat($(this).find('.available_qty_data').text()) || 0);
            if (currunt_row_index == $(this).index()) {
                return;
            }
            let same_variation_row = $(this);
            let other_quantity = parseFloat(same_variation_row.find('input[name="quantity"]').val()) || 0;
            same_variation_quantity += other_quantity;
            if (same_variation_row.attr('data-variation-id') == variation_id) {
                is_same_variation = true;
            }
        });
        if (isNaN(quantity) || quantity <= 0) {
            showErrorInTd($quantityInput, 'Quantity must be a number greater than 0');
            isValid = false;
        } else if (enable_stock == 1 && quantity > allowed_qty) {
            showErrorInTd($quantityInput, `Quantity must be equal or below ${allowed_qty}`);
            isValid = false;
        } else if (enable_stock == 1 && is_same_variation && quantity + same_variation_quantity > allowed_qty) {
            showErrorInTd($quantityInput, `Same Product already have ${same_variation_quantity} quantity, Quantity must be equal or below ${allowed_qty}`);
            isValid = false;
        } else {
            clearErrorInTd($quantityInput);
        }

        // Discount validation
        if (isNaN(discount) || discount < 0) {
            showErrorInTd($discountInput, 'Discount must not be negative');
            isValid = false;
        } else if (discount > discount_rate) {
            showErrorInTd($discountInput, 'Discount cannot exceed unit price');
            isValid = false;
        } else {
            clearErrorInTd($discountInput);
        }

        if (!isValid) return;

        let unit_price_discount = unitPrice;
        if (discount > 0) {
            if (discountType === 'percentage') {
                unit_price_discount = unit_price_discount * (1 - discount / 100);
            } else {
                unit_price_discount = unit_price_discount - discount;
            }
        }

        const ml_data = row.attr('data-ml');
        const ct_data = row.attr('data-ct');
        const locationTaxType_data = row.attr('data-locationTaxType');
        getProductTax(ml_data, ct_data, unit_price_discount, userTaxState, locationTaxType_data).then((res) => {
            const { status, tax } = res;
            const taxOnItem = status ? tax : 0;
            const quantity = parseFloat(row.find('input[name="quantity"]').val()) || 0;
            const unitPrice = parseFloat(row.find('input[name="unit_price"]').val()) || 0;
            const discount = parseFloat(row.find('input[name="discount"]').val()) || 0;
            const discountType = row.find('select[name="discount_type"]').val();

            // Update tax display
            row.find('#tax_rate').text('$ ' + (taxOnItem).toFixed(2));
            row.find('input[name="tax_rate_col"]').val(taxOnItem);

            const unit_price_inc_tax = unit_price_discount + taxOnItem
            const subtotal = unit_price_inc_tax * quantity;

            // Update total display
            row.find('td:nth-last-child(2)').find('.display_currency').text('$ ' + subtotal.toFixed(2));

            // Update price inc tax
            row.find('td:nth-last-child(3)').find('.display_currency').text('$ ' + (unit_price_inc_tax).toFixed(2));

            row.find('.item_tax_data').text(taxOnItem);

            row.find('.line_discount_amount_data').text(discount);
            row.find('.line_discount_type_data').text(discountType);
            row.find('.quantity_data').text(quantity);
            row.find('.unit_price_data').text(unitPrice);
            row.find('.unit_price_inc_tax_data').text(unit_price_inc_tax);

            updateTableTotals()
        })
    }

    // Helper function to update table totals

    var US_STATES = {
        AL: { id: 'AL', name: 'Alabama' },
        AK: { id: 'AK', name: 'Alaska' },
        AS: { id: 'AS', name: 'American Samoa' },
        AZ: { id: 'AZ', name: 'Arizona' },
        AR: { id: 'AR', name: 'Arkansas' },
        CA: { id: 'CA', name: 'California' },
        CO: { id: 'CO', name: 'Colorado' },
        CT: { id: 'CT', name: 'Connecticut' },
        DE: { id: 'DE', name: 'Delaware' },
        DC: { id: 'DC', name: 'District Of Columbia' },
        FM: { id: 'FM', name: 'Federated States Of Micronesia' },
        FL: { id: 'FL', name: 'Florida' },
        GA: { id: 'GA', name: 'Georgia' },
        GU: { id: 'GU', name: 'Guam' },
        HI: { id: 'HI', name: 'Hawaii' },
        ID: { id: 'ID', name: 'Idaho' },
        IL: { id: 'IL', name: 'Illinois' },
        IN: { id: 'IN', name: 'Indiana' },
        IA: { id: 'IA', name: 'Iowa' },
        KS: { id: 'KS', name: 'Kansas' },
        KY: { id: 'KY', name: 'Kentucky' },
        LA: { id: 'LA', name: 'Louisiana' },
        ME: { id: 'ME', name: 'Maine' },
        MH: { id: 'MH', name: 'Marshall Islands' },
        MD: { id: 'MD', name: 'Maryland' },
        MA: { id: 'MA', name: 'Massachusetts' },
        MI: { id: 'MI', name: 'Michigan' },
        MN: { id: 'MN', name: 'Minnesota' },
        MS: { id: 'MS', name: 'Mississippi' },
        MO: { id: 'MO', name: 'Missouri' },
        MT: { id: 'MT', name: 'Montana' },
        NE: { id: 'NE', name: 'Nebraska' },
        NV: { id: 'NV', name: 'Nevada' },
        NH: { id: 'NH', name: 'New Hampshire' },
        NJ: { id: 'NJ', name: 'New Jersey' },
        NM: { id: 'NM', name: 'New Mexico' },
        NY: { id: 'NY', name: 'New York' },
        NC: { id: 'NC', name: 'North Carolina' },
        ND: { id: 'ND', name: 'North Dakota' },
        MP: { id: 'MP', name: 'Northern Mariana Islands' },
        OH: { id: 'OH', name: 'Ohio' },
        OK: { id: 'OK', name: 'Oklahoma' },
        OR: { id: 'OR', name: 'Oregon' },
        PW: { id: 'PW', name: 'Palau' },
        PA: { id: 'PA', name: 'Pennsylvania' },
        PR: { id: 'PR', name: 'Puerto Rico' },
        RI: { id: 'RI', name: 'Rhode Island' },
        SC: { id: 'SC', name: 'South Carolina' },
        SD: { id: 'SD', name: 'South Dakota' },
        TN: { id: 'TN', name: 'Tennessee' },
        TX: { id: 'TX', name: 'Texas' },
        UT: { id: 'UT', name: 'Utah' },
        VT: { id: 'VT', name: 'Vermont' },
        VI: { id: 'VI', name: 'Virgin Islands' },
        VA: { id: 'VA', name: 'Virginia' },
        WA: { id: 'WA', name: 'Washington' },
        WV: { id: 'WV', name: 'West Virginia' },
        WI: { id: 'WI', name: 'Wisconsin' },
        WY: { id: 'WY', name: 'Wyoming' }
    };

    const getProductTax = async (ml = 0, ct = 0, price = 0, state = 'IL', locationTaxType = []) => {
        try {
            // console.log('Already have:', locationTaxCharges);
            if (locationTaxCharges.length === 0) {
                const response = await fetch('/list-tax-rates?location_id=' + $('#location_id').val());
                const data = await response.json();
                locationTaxCharges = data;
                // console.log('Fetched locationTaxCharges:', locationTaxCharges);
            }

            if (!price || !state || !locationTaxType?.length) {
                return { state: false, tax: 0 };
            }
            const stateName = state;
            const isValidState = US_STATES[stateName];
            if (!isValidState) {
                alert('Selected tax state (State Code) is Invalid');
                return { state: false, tax: 0 };
            }
            const filterLocationCharge = locationTaxCharges?.filter((i) =>
                locationTaxType.includes(i?.location_tax_type_id?.toString())
            );
            // console.log('Filter Data: ',filterLocationCharge);
            const filterProductTaxes = filterLocationCharge.filter(
                (tax) => tax.state_code.toLowerCase() === stateName.toLowerCase()
            );
            let taxTotal = filterProductTaxes.reduce((total, val) => {
                if (val.tax_type == 'UNIT_BASIS_ML') {
                    if (ml && ml > 0) {
                        return total + ml * val.value;
                    }
                }
                if (val.tax_type == 'FLAT_RATE') {
                    return total + val.value;
                }
                if (val.tax_type == 'PERCENTAGE_ON_SALE') {
                    return total + (val.value / 100) * price;
                }
                if (val.tax_type == 'PERCENTAGE_ON_COST') {
                    return total + (val.value / 100) * price;
                }
                if (val.tax_type == 'UNIT_COUNT') {
                    if (ct && ct > 0) {
                        return total + ct * val.value;
                    }
                }
                return total;
            }, 0);
            return {
                status: true,
                tax: taxTotal,
            };
        } catch (error) {
            console.log('error', error);
        }
    };

    function updateTableTotals() {
        let grandTotal = 0;
        let totalTax = 0;

        let total_paid = $('#total_paid_value').data('value');
        let shipping_charge = parseFloat($('#td_shipping_charge').text().replace('$', ''));

        let total_discount_amount = parseFloat($('.discount_amount_data').text().replace('$', ''));
        let total_discount_type = $('.discount_type_data').text();
        let final_discount_amount = 0;

        $('#sellsModalTable tbody tr').each(function () {
            const total = parseFloat($(this).find('td:nth-last-child(2)').find('.display_currency').text().replace(/[^0-9.-]+/g, '')) || 0;
            const tax = parseFloat($(this).find('#tax_rate').text().replace('$', '')) * parseFloat($(this).find('input[name="quantity"]').val()) || 0;
            grandTotal += total;
            totalTax += tax;
        });

        if(total_discount_type == 'percentage'){
            final_discount_amount = total_discount_amount * parseFloat(grandTotal) / 100;
        }else{
            final_discount_amount = total_discount_amount;
        }

        $('#total_before_tax').text('$ ' + (grandTotal).toFixed(2))
        $('#total_tax').text('$ ' + (totalTax).toFixed(2))
        $('#final_total_p').text('$ ' + (grandTotal + shipping_charge - final_discount_amount).toFixed(2))
        $('#final_total').text('$ ' + (shipping_charge + grandTotal - total_paid??0).toFixed(2))
        $('.final_total_data').text(grandTotal + shipping_charge - final_discount_amount);
        
        let main_final_total = grandTotal + shipping_charge - final_discount_amount;
        $('#main_final_total').text('$ ' + (main_final_total).toFixed(2))
    }

    $(document).on('focus', 'input, textarea', function () {
        $(this).attr('autocomplete', 'off');
    });
    $('.delete_row_warning').on('click', function () {
        toastr.warning('This sells line is already Saved. You cannot delete it.You can edit the quantity or price.');
    });
});

