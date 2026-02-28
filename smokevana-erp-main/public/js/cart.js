$(document).ready(function () {
    var shipment_type = 'PICKUP'
    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;
        let cart_shipping_chargrs = 0;
        const currentShipping = $('input[name="shipping"]:checked').val();
        if (currentShipping == 'flat_rate') {
            cart_shipping_chargrs = 15;
            shipment_type = 'FLAT_RATE'
        }

        $('#cartTable tbody tr').each(function () {
            let row = $(this);
            let priceText = row.find('.price_inc_tax').text().replace('$', '').trim();
            let price = parseFloat(priceText) || 0;

            // Get quantity
            let qty = parseFloat(row.find('.quantity-input').val()) || 0;

            // Get tax (from hidden input)
            let tax = parseFloat(row.data('item-tax')) || 0;

            subtotal += price * qty;
            totalTax += tax * qty;
        });

        let finalTotal = subtotal + totalTax + cart_shipping_chargrs;
        // Update UI
        $('.cart_subtotal').text(`$ ${subtotal.toFixed(2)}`);
        $('.cart_total_tax').text(`$ ${totalTax.toFixed(2)}`);
        $('.cart_final_total').text(`$ ${finalTotal.toFixed(2)}`);
        $('.cart_shipping_chargrs').text(`$ ${cart_shipping_chargrs.toFixed(2)}`);
    }

    let cartData = [];
    let shippingData;
    let billingData;
    let isCartAvailable
    let isOverSelling;
    let overSellingQuantity;

    let isOverSellingHolder = document.getElementById('isOverSelling-data-holder');
    if (isOverSellingHolder) {
        let rawData = isOverSellingHolder.getAttribute('data-isOverSelling');
        isOverSelling = JSON.parse(rawData);

    }

    let overSellingQuantityHolder = document.getElementById('overSellingQuantity-data-holder');
    if (overSellingQuantityHolder) {
        let rawData = overSellingQuantityHolder.getAttribute('data-overSellingQuantity');
        overSellingQuantity = JSON.parse(rawData);

    }

    let cartHolder = document.getElementById('cart-data-holder');
    if (cartHolder) {
        let rawData = cartHolder.getAttribute('data-cart');
        cartData = JSON.parse(rawData);

    }

    let shippingHolder = document.getElementById('shipping-data-holder');
    if (shippingHolder) {
        let rawData = shippingHolder.getAttribute('data-shipping');
        shippingData = JSON.parse(rawData);

    }

    let billingHolder = document.getElementById('billing-data-holder');
    if (billingHolder) {
        let rawData = billingHolder.getAttribute('data-billing');
        billingData = JSON.parse(rawData);

    }

    let isCartAvailableHolder = document.getElementById('isCartAvailable-data-holder');
    if (billingHolder) {
        let rawData = isCartAvailableHolder.getAttribute('data-isCartAvailable');
        isCartAvailable = JSON.parse(rawData);
    }


    let contact_id = document.getElementById('contact_id')?.value;
    $('#copyCartButton').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (cartData && cartData.length > 0) {
            navigator.clipboard.writeText(JSON.stringify(cartData, null, 2))
                .then(() => toastr.success('Cart copied to clipboard!'))
                .catch(err => toastr.error('Failed to copy cart!'));
        } else {
            toastr.error('No data found in the table to copy!');
        }
    });

    if ($('#search_product').length) {
        $('#search_product')
            .autocomplete({
                delay:1000,
                minLength: 4,
                source: function (request, response) {
                    $.getJSON('/purchases/get_products?check_enable_stock=false', { term: request.term }, function (data) {
                        response($.map(data, function (v) {
                            return v.variation_id ? { label: v.text, value: v.variation_id } : null;
                        }));
                    });
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
                    event.preventDefault();
                    $(this).val(ui.item.label);
                    $('#search_product').val('');
                    fetchProductRow(ui.item.value);
                },
                focus: function (event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                },
            })

        // Barcode scanner support for cart
        ;(function initCartBarcodeScanner() {
            if (!$('#search_product').length) { return; }

            function scanAndAddToCart(scannedCode) {
                const code = String(scannedCode).trim();
                if (!code) { return; }

                $.getJSON('/purchases/get_products?check_enable_stock=false', { term: code }, function (data) {
                    if (!data || !data.length) {
                        toastr.warning(LANG.no_products_found);
                        $('#search_product').val('').focus();
                        return;
                    }

                    let exact = [];
                    try {
                        exact = data.filter(function (it) {
                            return (
                                String(it.sub_sku || '') === code ||
                                String(it.sku || '') === code ||
                                String(it.var_barcode_no || '') === code ||
                                String(it.barcode || '') === code
                            );
                        });
                    } catch (_) { exact = []; }

                    const chosen = exact.length === 1 ? exact[0] : (data.length === 1 ? data[0] : null);

                    if (chosen) {
                        const variationId = chosen.variation_id || chosen.value;
                        if (variationId) {
                            fetchProductRow(variationId);
                            $('#search_product').val('').focus();
                            return;
                        }
                    }

                    // Multiple matches or no exact; trigger suggestions
                    $('#search_product').val(code).focus();
                    if ($('#search_product').autocomplete) {
                        $('#search_product').autocomplete('search', code);
                    }
                });
            }

            try {
                onScan.attachTo(document, {
                    suffixKeyCodes: [13],
                    reactToPaste: true,
                    minLength: 3,
                    onScan: function (sCode) {
                        if ($('.modal.show').length) { return; }
                        const active = document.activeElement;
                        if ($(active).is('input,textarea,[contenteditable=true]') && !$(active).is('#search_product')) { return; }
                        scanAndAddToCart(sCode);
                    },
                    onScanError: function () { }
                });
            } catch (e) {
                // Fallback: Enter on search triggers add
                $('#search_product').on('keypress', function (e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        const val = $(this).val().trim();
                        if (val) { scanAndAddToCart(val); }
                    }
                });
            }
        })();
    }

    $('#edit_cart_display').on('click', function () {
        $('.edit_hide').addClass('hide');
        $('.edit_show').removeClass('hide');
        $('.edit_enable').prop('readonly', false);
        $('.edit_enable').prop('disabled', false);
    })

    $('#cancel_edit').on('click', function () {
        $('.edit_enable').prop('disabled', true);
        $('.edit_enable').prop('readonly', true);
        $('.edit_show').addClass('hide');
        $('.edit_hide').removeClass('hide');
    })

    // Add click handlers for address edit icons
    $('#edit_shipping_address').on('click', function () {
        $('#AddressModal').modal('show');
        $('#AddressModalLabel').text('Edit Shipping Address');
        $('#AddressForm').data('type', 'shipping');

        $('input[name="company"]').val(shippingData.business_name);
        $('input[name="first_name"]').val(shippingData.first_name);
        $('input[name="last_name"]').val(shippingData.last_name);
        $('.hideinput').addClass('hide')
        $('input[name="address_1"]').val(shippingData.address1);
        $('input[name="address_2"]').val(shippingData.address2);
        $('input[name="city_locality"]').val(shippingData.city);
        $('input[name="state_province"]').val(shippingData.state);
        $('input[name="postal_code"]').val(shippingData.zip_code);
        $('input[name="country_code"]').val(shippingData.country);

    });

    $('#edit_billing_address').on('click', function () {
        $('#AddressModal').modal('show');
        $('#AddressModalLabel').text('Edit Billing Address');
        $('#AddressForm').data('type', 'billing');
        $('input[name="company"]').val(billingData.business_name);
        $('input[name="first_name"]').val(billingData.first_name);
        $('input[name="last_name"]').val(billingData.last_name);
        $('.hideinput').removeClass('hide')
        $('input[name="phone"]').val(billingData.mobile);
        $('input[name="email"]').val(billingData.email);
        $('input[name="address_1"]').val(billingData.address1);
        $('input[name="address_2"]').val(billingData.address2);
        $('input[name="city_locality"]').val(billingData.city);
        $('input[name="state_province"]').val(billingData.state);
        $('input[name="postal_code"]').val(billingData.zip_code);
        $('input[name="country_code"]').val(billingData.country);
    });

    // Handle modal close buttons
    $(document).on('click', '#AddressModal .close_address', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('#AddressModal').modal('hide');
    });
    // Handle address save
    $('#saveAddress').on('click', function () {
        let formArray = $('#AddressForm').serializeArray();
        let addressType = $('#AddressForm').data('type');

        if (!validateAddressForm(addressType)) {
            return;
        }

        let address = {};
        $.each(formArray, function () {
            address[this.name] = this.value;
        });
        let payload = {};
        if (addressType === 'shipping') {
            payload = {
                user_id: contact_id,
                billing_first_name: billingData.first_name,
                billing_last_name: billingData.last_name,
                billing_company: billingData.business_name,
                billing_address1: billingData.address1,
                billing_address2: billingData.address2,
                billing_city: billingData.city,
                billing_state: billingData.state,
                billing_zip: billingData.zip_code,
                billing_country: billingData.country,
                billing_phone: billingData.mobile,
                billing_email: billingData.email,
                shipping_first_name: address.first_name,
                shipping_last_name: address.last_name,
                shipping_company: address.company,
                shipping_address1: address.address_1,
                shipping_address2: address.address_2,
                shipping_city: address.city_locality,
                shipping_state: address.state_province,
                shipping_zip: address.postal_code,
                shipping_country: address.country_code
            }
        } else {
            payload = {
                user_id: contact_id,
                billing_first_name: address.first_name,
                billing_last_name: address.last_name,
                billing_company: address.company,
                billing_address1: address.address_1,
                billing_address2: address.address_2,
                billing_city: address.city_locality,
                billing_state: address.state_province,
                billing_zip: address.postal_code,
                billing_country: address.country_code,
                billing_phone: address.phone,
                billing_email: address.email,
                shipping_first_name: shippingData.first_name,
                shipping_last_name: shippingData.last_name,
                shipping_company: shippingData.business_name,
                shipping_address1: shippingData.address1,
                shipping_address2: shippingData.address2,
                shipping_city: shippingData.city,
                shipping_state: shippingData.state,
                shipping_zip: shippingData.zip_code,
                shipping_country: shippingData.country,
            }
        }
        swal({
            title: "Are you sure?",
            text: "Do you really want to change address?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then(function (willchange) {
            if (willchange) {
                $.ajax({
                    url: '/customer-cart/checkout-address' + (contact_id ? `?cid=${contact_id}` : ''),
                    method: 'POST',
                    data: payload,
                    success: function (response) {
                        if (response.status) {
                            toastr.success(response.message);
                            changeAddressData(payload);
                            $('#AddressModal').modal('hide');
                        } else {
                            swal("Error!", "Something went wrong", "error");
                        }
                    },
                    error: function (xhr) {
                        swal("Failed!", xhr.responseJSON?.message || "Something went wrong", "error");
                    }
                });
            }
        });

    });

    // Add click handler for delete button
    $('#delete_row button').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let row = $(this).closest('tr');
        delete_cart_row(row)
    });

    // price recall change button
    $('.cart_price_update_btn').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const container = $(this).closest('.price-update-container');
        const btn = $(this);
        price_recall_function(container, btn)

    });

    // quantity change
    $('.quantity-input').on('change', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let row = $(this).closest('tr');
        let newQty = parseFloat($(this).val()) || 0;
        quantity_change_function(row, newQty);

    });

    $('input[name="shipping"]').on('change', function () {
        calculateTotals()
    });

    function changeAddressData(payload) {
        shippingData.first_name = payload.shipping_first_name;
        shippingData.last_name = payload.shipping_last_name;
        shippingData.business_name = payload.shipping_company;
        shippingData.address1 = payload.shipping_address1;
        shippingData.address2 = payload.shipping_address2;
        shippingData.city = payload.shipping_city;
        shippingData.state = payload.shipping_state;
        shippingData.zip_code = payload.shipping_zip;
        shippingData.country = payload.shipping_country
        billingData.first_name = payload.billing_first_name;
        billingData.last_name = payload.billing_last_name;
        billingData.business_name = payload.billing_company;
        billingData.address1 = payload.billing_address1;
        billingData.address2 = payload.billing_address2;
        billingData.city = payload.billing_city;
        billingData.state = payload.billing_state;
        billingData.zip_code = payload.billing_zip;
        billingData.country = payload.billing_country;
        billingData.mobile = payload.billing_phone;
        billingData.email = payload.billing_email
        let shipping_fullname = (shippingData.first_name ?? "") + " " + (shippingData.last_name ?? "");
        let shipping_address =
            (shippingData.address1 ?? '') + " " +
            (shippingData.address2 ?? '') + " " +
            (shippingData.city ?? '') + " " +
            (shippingData.state ?? '') + " " +
            (shippingData.zip_code ?? '') + " " +
            (shippingData.country ?? '');

        let billing_fullname = (billingData.first_name ?? "") + " " + (billingData.last_name ?? "");
        let billing_address =
            (billingData.address1 ?? '') + " " +
            (billingData.address2 ?? '') + " " +
            (billingData.city ?? '') + " " +
            (billingData.state ?? '') + " " +
            (billingData.zip_code ?? '') + " " +
            (billingData.country ?? '');

        $('.shipping_business_name').text(shippingData.business_name);
        $('.shipping_full_name').text(shipping_fullname);
        $('.shipping_full_address').text(shipping_address);
        $('.billing_business_name').text(billingData.business_name);
        $('.billing_full_name').text(billing_fullname);
        $('.billing_full_address').text(billing_address);

        syncCartTable()
    }

    function syncCartTable() {
        $.ajax({
            url: `/contacts/sync-customer-cart/${contact_id}`,
            dataType: 'json',
            success: function (result) {
                if (Array.isArray(result)) {
                    $('#cartTable tbody').empty();

                    result.forEach(function (cart) {
                        let recalled = cart.recalled_price ? 'Yes' : 'No';

                        let row = `
                    <tr data-item-id="${cart.item_id}" data-product-id="${cart.product_id}"
                        data-variation-id="${cart.variation_id}" data-item-tax="${cart.product_tax}">
                        <td><img src="${cart.product_image}" alt="Product Image"
                            style="width: 40px; height: 40px; object-fit: cover;"></td>
                        <td>
                            <div class="product-info">
                                <div class="product-details">
                                    <span class="product-name">${cart.product_name}</span>
                                    <div>
                                        <span class="variation-name">${cart.variation_name}</span>
                                        <span class="sku">${cart.sku}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="price_inc_tax">$ ${parseFloat(cart.product_price_with_tax).toFixed(2)}</td>
                        <td>${cart.stock}
                        <input type="number" class="quantity_available hide" name="quantity_available" value=${cart.stock}></td>
                        <td>
                            <input type="number" value="${cart.qty}" class="form-control quantity-input edit_enable">
                            <input type="number" value="${cart.qty}" class="form-control hide quantity-last">
                        </td>
                        <td>
                            <div class="input-group" style="width: 140px;">
                                <input type="text" class="form-control input_number row_discount_amount edit_enable" value="0">
                                <select class="form-control row_discount_type edit_enable" >
                                    <option value="fixed">$</option>
                                    <option value="percentage">%</option>
                                </select>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="edit_hide hide"><strong>${recalled}</strong></div>
                            <div class="price-update-container edit_show">
                                <input type="text" class="form-control price-input"
                                    value="${cart.recalled_price ?? ''}"
                                    data-product-id="${cart.product_id}"
                                    data-variation-id="${cart.variation_id}"
                                    data-contact-id="${contact_id}">
                                <button class="btn btn-sm btn-primary cart_price_update_btn">
                                    <i class="fas fa-save"></i>
                                </button>
                            </div>
                        </td>
                        <td class="edit_show text-center" id="delete_row">
                            <button><i class="fa fa-trash" style="color: red"></i></button>
                        </td>
                    </tr>`;
                        $('#cartTable tbody').append(row);
                    });

                    // ✅ Show success toast
                    toastr.success('Cart table synced successfully');
                    $('#delete_row button').on('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        let row = $(this).closest('tr');
                        delete_cart_row(row)
                    });
                    // price recall change button
                    $('.cart_price_update_btn').on('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const container = $(this).closest('.price-update-container');
                        const btn = $(this);
                        price_recall_function(container, btn)

                    });
                    // quantity change
                    $('.quantity-input').on('change', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        let row = $(this).closest('tr');
                        let newQty = parseFloat($(this).val()) || 0;
                        quantity_change_function(row, newQty);

                    });
                    calculateTotals()
                } else {
                    toastr.error('Failed to sync cart: Invalid data');
                }
            },
            error: function (xhr) {
                toastr.error('AJAX Error while syncing cart');
                console.error('AJAX error:', xhr.responseText);
            }
        });
    }

    function fetchProductRow(variationId) {
        $.getJSON(`/sells/price-recall/get_product_row/${variationId}/${contact_id}`, function (response) {
            let product_id = response.product_id;
            let recalled_price = response.recalled_price;
            let unit_price_inc_tax = response.price_with_tax;
            let tax = response.tax_amount;
            let stockQty = parseInt(response?.variation_location_details[0]?.in_stock_qty ?? 0);

            if(stockQty<1){
                toastr.warning("Product Is Out Of Stock");
                return;
            }

            // Step 1: Prepare item for backend validation
            let cartItem = {
                product_id: product_id,
                variation_id: variationId,
                qty: 1
            };

            // Step 2: Send POST request to cart update API
            $.ajax({
                url: '/contacts/customer-cart/cart?cid=' + contact_id, // admin mode
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ items: [cartItem] }),
                success: function (cartResponse) {
                    if (cartResponse.status) {
                        let existingRow = $(`#cartTable tbody tr[data-item-id="${cartResponse.item_id}"]`);

                        if (existingRow.length > 0) {
                            // Item already exists, increase quantity
                            let qtyInput = existingRow.find('.quantity-input');
                            let currentQty = parseInt(qtyInput.val()) || 0;
                            qtyInput.val(currentQty + 1);
                            toastr.success('Product quantity updated successfully!');
                        } else {
                            let newRow = `
                        <tr data-item-id=${cartResponse.item_id} data-product-id=${product_id} data-variation-id=${variationId} data-item-tax=${tax}>
                            <td>
                                <img src="${response.product.image_url}" alt="Product Image" style="width: 40px; height: 40px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="product-info">
                                    <div class="product-details">
                                        <span class="product-name">${response.product.name}</span>
                                        <div>
                                            <span class="variation-name">${response.name}</span>
                                            <span class="sku">${response.sub_sku}</span>
                                        </div>                                    
                                    </div>
                                </div>
                            </td>
                            <td class='price_inc_tax'>$ ${parseFloat(unit_price_inc_tax).toFixed(2)}</td>
                            <td>${stockQty}
                            <input type="number" class="quantity_available hide" name="quantity_available" value=${stockQty}>
                            </td>
                            <td>
                                <input type="number" value="1" class="form-control quantity-input edit_enable">
                                <input type="number" value="1" class="form-control hide quantity-last">
                            </td>
                            <td>
                                <div class="input-group" style="width: 140px;">
                                    <input type="text" name="discount_amount" value="0" class="form-control input_number row_discount_amount edit_enable">
                                    <select name="discount_type" class="form-control row_discount_type edit_enable">
                                        <option value="fixed">$</option>
                                        <option value="percentage">%</option>
                                    </select>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="edit_hide hide">
                                  ${recalled_price ? "<strong>Yes</strong>" : "<strong>No</strong>"}
                                  </div>
                                <div class="price-update-container edit_show">
                                    <input type="text" class="form-control price-input"
                                        value="${recalled_price ?? ''}"
                                        data-product-id="${product_id}"
                                        data-variation-id="${variationId}"
                                        data-contact-id="${contact_id}">
                                    <button class="btn btn-sm btn-primary cart_price_update_btn">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="edit_show text-center" id="delete_row"><button><i class="fa fa-trash" style="color: red"></i></button></td>
                        </tr>`;

                            $('#cartTable tbody').append(newRow);
                            $('[data-toggle="tooltip"]').tooltip();
                            $('#search_product').val('');
                            calculateTotals();
                            toastr.success('Product added to cart successfully!');
                            $('#delete_row button').on('click', function (e) {
                                e.preventDefault();
                                e.stopPropagation();
                                let row = $(this).closest('tr');
                                delete_cart_row(row)
                            });
                            // price recall change button
                            $('.cart_price_update_btn').on('click', function (e) {
                                e.preventDefault();
                                e.stopPropagation();

                                const container = $(this).closest('.price-update-container');
                                const btn = $(this);
                                price_recall_function(container, btn)

                            });
                            // quantity change
                            $('.quantity-input').on('change', function (e) {
                                e.preventDefault();
                                e.stopPropagation();
                                let row = $(this).closest('tr');
                                let newQty = parseFloat($(this).val()) || 0;
                                quantity_change_function(row, newQty);

                            });
                        }

                    } else {
                        let msg = Array.isArray(cartResponse.message)
                            ? cartResponse.message.map(e => e.messages.join(', ')).join('\n')
                            : cartResponse.message;
                        alert("Cannot add product: " + msg);
                    }
                },
                error: function (xhr) {
                    alert("Cart validation failed. Please try again.");
                }
            });
        });
        calculateTotals()
    }

    function price_recall_function(container, btn) {
        const input = container.find('.price-input');
        const productId = input.data('product-id');
        const variationId = input.data('variation-id');
        const contactId = input.data('contact-id');
        const newPrice = input.val();

        if (!newPrice || isNaN(newPrice) || newPrice <= 0) {
            toastr.error('Please enter a valid price greater than 0');
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '/contacts/customer-cart/update-recall-price',
            method: 'POST',
            data: {
                product_id: productId,
                variation_id: variationId,
                contact_id: contactId,
                new_price: newPrice
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    syncCartTable();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                toastr.error('Something went wrong. Please try again.');
                console.error(xhr);
            },
            complete: function () {
                // Re-enable button after request
                btn.prop('disabled', false).html('<i class="fas fa-save"></i>');
            }
        });
    }

    function delete_cart_row(row) {
        let item_id = row.data('item-id');
        let cid = contact_id; // Make sure this is globally defined

        swal({
            title: "Are you sure?",
            text: "Do you really want to remove this item from the cart?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then(function (willDelete) {
            if (willDelete) {
                $.ajax({
                    url: '/contacts/customer-cart/cart/delete' + (cid ? `?cid=${cid}` : ''),
                    method: 'POST', // Using POST with _method spoofing
                    data: {
                        item_id: item_id,
                    },
                    success: function (response) {
                        if (response.status) {
                            row.remove();
                            calculateTotals()
                            toastr.success('Item removed from cart successfully!');
                        } else {
                            swal("Error!", response.message, "error");
                        }
                        calculateTotals()
                    },
                    error: function (xhr) {
                        swal("Failed!", xhr.responseJSON?.message || "Something went wrong", "error");
                    }
                });
            }
        });
        calculateTotals()
    }

    function quantity_change_function(row, newQty) {
        let quantity_Available = parseFloat(row.find('input[name="quantity_available"]').val())
        if (!validateQuantity(newQty, quantity_Available)) {
            row.find('.quantity-input').val(row.find('.quantity-last').val());
            return;
        }

        let last_qty = parseFloat(row.find('.quantity-last').val());
        let itemId = row.data('item-id');
        let product_id = row.data('product-id');
        let variation_id = row.data('variation-id');

        if (newQty > last_qty) {
            let qty = newQty - last_qty;
            let cartItem = {
                product_id: product_id,
                variation_id: variation_id,
                qty: qty
            };

            // Step 2: Send POST request to cart update API
            $.ajax({
                url: '/contacts/customer-cart/cart?cid=' + contact_id, // admin mode
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ items: [cartItem] }),
                success: function (cartResponse) {
                    if (cartResponse.status) {
                        row.find('.quantity-last').val(newQty);
                        toastr.success('Cart quantity updated successfully!');
                    } else {
                        $(this).val(last_qty)
                        let msg = Array.isArray(cartResponse.message)
                            ? cartResponse.message.map(e => e.messages.join(', ')).join('\n')
                            : cartResponse.message;
                        toastr.warning("Cannot add product: " + msg);
                    }
                },
                error: function (xhr) {
                    $(this).val(last_qty)
                    toastr.error("Cart validation failed. Please try again.");
                }
            });

        } else if (newQty < last_qty) {
            let qty = last_qty - newQty;

            $.ajax({
                url: '/contacts/customer-cart/cart/reduce?cid=' + contact_id, // admin mode
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    "item_id": itemId,
                    "qty": qty
                }),
                success: function (cartResponse) {
                    if (cartResponse.status) {
                        row.find('.quantity-last').val(newQty);
                        calculateTotals()
                        toastr.success('Cart quantity reduced successfully!');
                    } else {
                        $(this).val(last_qty)
                        calculateTotals()
                        let msg = Array.isArray(cartResponse.message)
                            ? cartResponse.message.map(e => e.messages.join(', ')).join('\n')
                            : cartResponse.message;
                        toastr.warning("Cannot add product: " + msg);
                    }
                },
                error: function (xhr) {
                    $(this).val(last_qty)
                    toastr.error("Cart validation failed. Please try again.");
                }
            });
        }
        calculateTotals()
    }

    calculateTotals();

    $('#place_cart_order').on('click', function (e) {
        e.preventDefault();

        // Check if cart has items
        if ($('#cartTable tbody tr').length === 0) {
            swal({
                title: "Empty Cart",
                text: "Please add at least one item to the cart before placing an order",
                icon: "warning",
                buttons: true,
            });
            return;
        }

        // Validate address
        if (!validateAddressFormPlaceorder()) {
            return;
        }

        let cid = contact_id;
        if (isCartAvailable) {
            swal({
                title: "Confirm Order",
                text: "Are you sure you want to place this order?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willPlace) => {
                if (willPlace) {
                    $.ajax({
                        url: '/customer-cart-process-order' + (cid ? `?cid=${cid}` : ''),
                        method: 'POST',
                        data: {
                            paymentType: "onaccount",
                            shippingType: shipment_type,
                        },
                        success: function (response) {
                            if (response.status) {
                                toastr.success(response.message);
                                $('.view_modal').modal('hide');
                            } else {
                                toastr.error(response.message || 'Failed to place order');
                            }
                        },
                        error: function (xhr) {
                            toastr.error(xhr.responseJSON?.message || 'An error occurred while processing your order');
                        }
                    });
                }
            });
        } else {
            payload = {
                user_id: contact_id,
                billing_first_name: billingData.first_name,
                billing_last_name: billingData.last_name,
                billing_company: billingData.business_name,
                billing_address1: billingData.address1,
                billing_address2: billingData.address2,
                billing_city: billingData.city,
                billing_state: billingData.state,
                billing_zip: billingData.zip_code,
                billing_country: billingData.country,
                billing_phone: billingData.mobile,
                billing_email: billingData.email,
                shipping_first_name: shippingData.first_name,
                shipping_last_name: shippingData.last_name,
                shipping_company: shippingData.business_name,
                shipping_address1: shippingData.address1,
                shipping_address2: shippingData.address2,
                shipping_city: shippingData.city,
                shipping_state: shippingData.state,
                shipping_zip: shippingData.zip_code,
                shipping_country: shippingData.country,
            }
            $.ajax({
                url: '/customer-cart/checkout-address' + (contact_id ? `?cid=${contact_id}` : ''),
                method: 'POST',
                data: payload,
                success: function (response) {
                    if (response.status) {
                        $.ajax({
                            url: '/customer-cart-process-order' + (cid ? `?cid=${cid}` : ''),
                            method: 'POST',
                            data: {
                                paymentType: "onaccount",
                                shippingType: shipment_type,
                            },
                            success: function (response) {
                                if (response.status) {
                                    toastr.success(response.message);
                                    $('.view_modal').modal('hide');
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function (xhr) {
                                swal("Failed!", xhr.responseJSON?.message || "Something went wrong", "error");
                            }
                        });
                    } else {
                        swal("Error!", "Something went wrong", "error");
                    }
                },
                error: function (xhr) {
                    swal("Failed!", xhr.responseJSON?.message || "Something went wrong", "error");
                }
            });

        }
    })

    // Add validation functions at the top after document.ready
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function validatePhone(phone) {
        const re = /^\+?[\d\s-]{10,}$/;
        return re.test(phone);
    }

    function validateAddressForm(type) {
        const requiredFields = {
            shipping: ['company', 'first_name', 'last_name', 'address_1', 'city_locality', 'state_province', 'postal_code', 'country_code'],
            billing: ['company', 'first_name', 'last_name', 'address_1', 'city_locality', 'state_province', 'postal_code', 'country_code', 'phone', 'email']
        };

        let isValid = true;
        let errorMessages = [];

        // Check required fields
        requiredFields[type].forEach(field => {
            const value = $(`input[name="${field}"]`).val().trim();
            if (!value) {
                isValid = false;
                errorMessages.push(`${field.replace('_', ' ')} is required`);
            }
        });

        // Additional validation for billing address
        if (type === 'billing') {
            const email = $('input[name="email"]').val().trim();
            const phone = $('input[name="phone"]').val().trim();

            if (!validateEmail(email)) {
                isValid = false;
                errorMessages.push('Please enter a valid email address');
            }

            if (!validatePhone(phone)) {
                isValid = false;
                errorMessages.push('Please enter a valid phone number');
            }
        }

        if (!isValid) {
            swal({
                title: "Validation Error",
                text: errorMessages.join('\n'),
                icon: "error",
                buttons: true,
            });
        }

        return isValid;
    }

    function validateAddressFormPlaceorder() {
        let isValid = true;
        let errorMessages = [];

        // Validate shipping address fields
        if (!shippingData.first_name || shippingData.first_name.trim() === '') {
            isValid = false;
            errorMessages.push('Shipping first name is required');
        }
        if (!shippingData.last_name || shippingData.last_name.trim() === '') {
            isValid = false;
            errorMessages.push('Shipping last name is required');
        }
        if (!shippingData.business_name || shippingData.business_name.trim() === '') {
            isValid = false;
            errorMessages.push('Shipping business name is required');
        }
        if (!shippingData.address1 || shippingData.address1.trim() === '') {
            isValid = false;
            errorMessages.push('Shipping address line 1 is required');
        }
        if (!shippingData.city || shippingData.city.trim() === '') {
            isValid = false;
            errorMessages.push('Shipping city is required');
        }
        if (!shippingData.state || shippingData.state.trim() === '') {
            isValid = false;
            errorMessages.push('Shipping state is required');
        }
        if (!shippingData.zip_code || shippingData.zip_code.trim() === '') {
            isValid = false;
            errorMessages.push('Shipping zip code is required');
        }
        if (!shippingData.country || shippingData.country.trim() === '') {
            isValid = false;
            errorMessages.push('Shipping country is required');
        }

        // Validate billing address fields
        if (!billingData.first_name || billingData.first_name.trim() === '') {
            isValid = false;
            errorMessages.push('Billing first name is required');
        }
        if (!billingData.last_name || billingData.last_name.trim() === '') {
            isValid = false;
            errorMessages.push('Billing last name is required');
        }
        if (!billingData.business_name || billingData.business_name.trim() === '') {
            isValid = false;
            errorMessages.push('Billing business name is required');
        }
        if (!billingData.address1 || billingData.address1.trim() === '') {
            isValid = false;
            errorMessages.push('Billing address line 1 is required');
        }
        if (!billingData.city || billingData.city.trim() === '') {
            isValid = false;
            errorMessages.push('Billing city is required');
        }
        if (!billingData.state || billingData.state.trim() === '') {
            isValid = false;
            errorMessages.push('Billing state is required');
        }
        if (!billingData.zip_code || billingData.zip_code.trim() === '') {
            isValid = false;
            errorMessages.push('Billing zip code is required');
        }
        if (!billingData.country || billingData.country.trim() === '') {
            isValid = false;
            errorMessages.push('Billing country is required');
        }

        // Validate billing contact details
        if (!billingData.mobile || billingData.mobile.trim() === '') {
            isValid = false;
            errorMessages.push('Billing phone number is required');
        } else if (!validatePhone(billingData.mobile)) {
            isValid = false;
            errorMessages.push('Please enter a valid billing phone number');
        }

        if (!billingData.email || billingData.email.trim() === '') {
            isValid = false;
            errorMessages.push('Billing email is required');
        } else if (!validateEmail(billingData.email)) {
            isValid = false;
            errorMessages.push('Please enter a valid billing email address');
        }

        if (!isValid) {
            swal({
                title: "Validation Error",
                text: errorMessages.join('\n'),
                icon: "error",
                buttons: true,
            });
        }

        return isValid;
    }

    // Update quantity validation
    function validateQuantity(quantity, quantity_Available) {
        let qty = parseFloat(quantity)
        let maxQuantity = quantity_Available;

        // if (isOverSelling) {
        //     if (overSellingQuantity) {
        //         maxQuantity = quantity_Available + overSellingQuantity
        //     } else {
        //         maxQuantity = quantity_Available + 5000;
        //     }
        // } else {
        //     maxQuantity = quantity_Available;
        // }
        if (qty > maxQuantity) {
            toastr.warning(`Max Quantity For This Product Is ${maxQuantity}`);
            return false;
        }

        if (!qty || qty < 1) {
            toastr.warning('Quantity must be at least 1');
            return false;
        }
        return true;
    }
});

