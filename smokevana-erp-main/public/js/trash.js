var US_STATES = {
    AL: {
        id: 'AL',
        name: 'Alabama',
    },
    AK: {
        id: 'AK',
        name: 'Alaska',
    },
    AS: {
        id: 'AS',
        name: 'American Samoa',
    },
    AZ: {
        id: 'AZ',
        name: 'Arizona',
    },
    AR: {
        id: 'AR',
        name: 'Arkansas',
    },
    CA: {
        id: 'CA',
        name: 'California',
    },
    CO: {
        id: 'CO',
        name: 'Colorado',
    },
    CT: {
        id: 'CT',
        name: 'Connecticut',
    },
    DE: {
        id: 'DE',
        name: 'Delaware',
    },
    DC: {
        id: 'DC',
        name: 'District Of Columbia',
    },
    FM: {
        id: 'FM',
        name: 'Federated States Of Micronesia',
    },
    FL: {
        id: 'FL',
        name: 'Florida',
    },
    GA: {
        id: 'GA',
        name: 'Georgia',
    },
    GU: {
        id: 'GU',
        name: 'Guam',
    },
    HI: {
        id: 'HI',
        name: 'Hawaii',
    },
    ID: {
        id: 'ID',
        name: 'Idaho',
    },
    IL: {
        id: 'IL',
        name: 'Illinois',
    },
    IN: {
        id: 'IN',
        name: 'Indiana',
    },
    IA: {
        id: 'IA',
        name: 'Iowa',
    },
    KS: {
        id: 'KS',
        name: 'Kansas',
    },
    KY: {
        id: 'KY',
        name: 'Kentucky',
    },
    LA: {
        id: 'LA',
        name: 'Louisiana',
    },
    ME: {
        id: 'ME',
        name: 'Maine',
    },
    MH: {
        id: 'MH',
        name: 'Marshall Islands',
    },
    MD: {
        id: 'MD',
        name: 'Maryland',
    },
    MA: {
        id: 'MA',
        name: 'Massachusetts',
    },
    MI: {
        id: 'MI',
        name: 'Michigan',
    },
    MN: {
        id: 'MN',
        name: 'Minnesota',
    },
    MS: {
        id: 'MS',
        name: 'Mississippi',
    },
    MO: {
        id: 'MO',
        name: 'Missouri',
    },
    MT: {
        id: 'MT',
        name: 'Montana',
    },
    NE: {
        id: 'NE',
        name: 'Nebraska',
    },
    NV: {
        id: 'NV',
        name: 'Nevada',
    },
    NH: {
        id: 'NH',
        name: 'New Hampshire',
    },
    NJ: {
        id: 'NJ',
        name: 'New Jersey',
    },
    NM: {
        id: 'NM',
        name: 'New Mexico',
    },
    NY: {
        id: 'NY',
        name: 'New York',
    },
    NC: {
        id: 'NC',
        name: 'North Carolina',
    },
    ND: {
        id: 'ND',
        name: 'North Dakota',
    },
    MP: {
        id: 'MP',
        name: 'Northern Mariana Islands',
    },
    OH: {
        id: 'OH',
        name: 'Ohio',
    },
    OK: {
        id: 'OK',
        name: 'Oklahoma',
    },
    OR: {
        id: 'OR',
        name: 'Oregon',
    },
    PW: {
        id: 'PW',
        name: 'Palau',
    },
    PA: {
        id: 'PA',
        name: 'Pennsylvania',
    },
    PR: {
        id: 'PR',
        name: 'Puerto Rico',
    },
    RI: {
        id: 'RI',
        name: 'Rhode Island',
    },
    SC: {
        id: 'SC',
        name: 'South Carolina',
    },
    SD: {
        id: 'SD',
        name: 'South Dakota',
    },
    TN: {
        id: 'TN',
        name: 'Tennessee',
    },
    TX: {
        id: 'TX',
        name: 'Texas',
    },
    UT: {
        id: 'UT',
        name: 'Utah',
    },
    VT: {
        id: 'VT',
        name: 'Vermont',
    },
    VI: {
        id: 'VI',
        name: 'Virgin Islands',
    },
    VA: {
        id: 'VA',
        name: 'Virginia',
    },
    WA: {
        id: 'WA',
        name: 'Washington',
    },
    WV: {
        id: 'WV',
        name: 'West Virginia',
    },
    WI: {
        id: 'WI',
        name: 'Wisconsin',
    },
    WY: {
        id: 'WY',
        name: 'Wyoming',
    },
};





var global_brand_id = null;
var global_p_category_id = null;
var userTaxState = 'IL';
var selling_price_group_id = 1;
var selectedUser = false;

var userTaxInc = false;


var locationTaxCharges = [];


$('#ex_taxes_checkbox').on('change', function () {
    const isChecked = $(this).is(':checked');

    $('#pos_table tbody .product_row').each(function () {
        const row = $(this);
        const recallPrice = row.attr('recallprice');
        const unitPrice = recallPrice ? parseFloat(recallPrice) : parseFloat(row.find('input.pos_unit_price').val()) || 0;
        const quantity = parseFloat(row.find('input.pos_quantity').val()) || 1;
        const ml = row.attr('ml');  // ml (or ct) for product
        const ct = row.attr('ct');
        const locationTaxType = row.attr('locationtaxtype'); // Assuming it's stored here

        if (isChecked) {
            // Remove tax
            row.find('input.pos_taxation_total').val(0);
            row.find('span.pos_taxation_total_text').text('$ 0.00');

            // Update unit price (excluding tax)
            // row.find('input.pos_unit_price_inc_tax').val(unitPrice.toFixed(2));
        } else {
            // Restore tax and update prices
            getProductTax(ml, ct, unitPrice, userTaxState, JSON.parse(locationTaxType || '[]'))
                .then(res => {
                    const { status, tax } = res;
                    const taxOnItem = status ? tax : 0;

                    row.find('input.pos_taxation_total').val(taxOnItem.toFixed(2));
                    row.find('span.pos_taxation_total_text').text(`$ ${taxOnItem.toFixed(2)}`);

                    const totalAmount = (unitPrice + taxOnItem) * quantity;
                    row.find('input.pos_unit_price_inc_tax').val((unitPrice + taxOnItem).toFixed(2));
                    row.find('input.pos_line_total').val(totalAmount.toFixed(2));
                    row.find('span.pos_line_total_text').text(`$ ${totalAmount.toFixed(2)}`);
                })
                .catch(error => console.error("Error restoring tax: ", error));
        }

        // Update line total after tax changes
        let lineTotal = isChecked ? unitPrice * quantity : (parseFloat(row.find('input.pos_unit_price_inc_tax').val()) || 0) * quantity;
        row.find('input.pos_line_total').val(lineTotal.toFixed(2));
        row.find('span.pos_line_total_text').text(`$ ${lineTotal.toFixed(2)}`);
    });

    pos_total_row(); // Refresh totals after tax changes
});



const getProductTax = async (ml = 0, ct = 0, price = 0, state = 'IL', locationTaxType = []) => {
    try {
        // console.log('Already have:', locationTaxCharges);
        if (locationTaxCharges.length === 0) {
            const response = await fetch('/list-tax-rates');
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



const getUnitPriceLevel = (selling_price_group_id, priceDetails) => {
    let unitPrice = 0;
    // let selling_price_group_id_new = $('#hidden_price_group').val();
    let selling_price_group_id2 = parseInt($('#hidden_price_group').val());
    var name = $('#price_group option[value="' + selling_price_group_id2 + '"]').text();

    switch (name) {

        case 'SilverSellingPrice':
            unitPrice = parseFloat(priceDetails.SilverSellingPrice || 0);
            break;

        case 'GoldSellingPrice':
            unitPrice = parseFloat(priceDetails.GoldSellingPrice || 0);
            break;

        case 'PlatinumSellingPrice':
            unitPrice = parseFloat(priceDetails.PlatinumSellingPrice || 0);
            break;

        case 'LowestSellingPrice':
            unitPrice = parseFloat(priceDetails.LowestSellingPrice || 0);
            break;

        case 'DiamondSellingPrice':
            unitPrice = parseFloat(priceDetails.DiamondSellingPrice || 0);
            break;
        default:
            unitPrice = 0;
    }

    return unitPrice;
};

$(document).ready(function () {
    var urlParams = new URLSearchParams(window.location.search);
    var cid = urlParams.get('cid');
    if (cid) {
        $('#cid_input').val(cid);
        // console.log('cid', cid);
        $.ajax({
            url: '/contacts/customers',
            dataType: 'json',
            data: { q: cid, isID: true },
            success: function (data) {
                if (data.length === 1) {
                    var customer = data[0];
                    var option = new Option(customer.text, customer.id, true, true);
                    
                    // Auto-check exempt tax checkbox if customer is tax exempt
                    if (customer.is_tax_exempt == 1 || customer.is_tax_exempt === true) {
                        $('#ex_taxes_checkbox').prop('checked', true).trigger('change');
                    } else {
                        $('#ex_taxes_checkbox').prop('checked', false).trigger('change');
                    }
                    
                    $('#customer_id').append(option).trigger('change');
                    $('#search_product').prop('disabled', false).focus();
                    $('.copyPasteAdd').prop('disabled', false);
                    setTimeout(function () {
                        update_shipping_address(customer);
                    }, 1000);
                }
            }
        });
    }

    customer_set = false;
    //Prevent enter key function except texarea
    $('form').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && e.target.tagName != 'TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    //For edit pos form
    if ($('form#edit_pos_sell_form').length > 0) {
        pos_total_row();
        pos_form_obj = $('form#edit_pos_sell_form');
    } else {
        pos_form_obj = $('form#add_pos_sell_form');
    }
    if ($('form#edit_pos_sell_form').length > 0 || $('form#add_pos_sell_form').length > 0) {
        initialize_printer();
    }

    $('select#select_location_id').change(function () {
        reset_pos_form();

        var default_price_group = $(this).find(':selected').data('default_price_group');
        if (default_price_group) {
            if ($("#price_group option[value='" + default_price_group + "']").length > 0) {
                $('#price_group').val(default_price_group);
                $('#price_group').change();
            }
        }

        //Set default invoice scheme for location
        if ($('#invoice_scheme_id').length) {
            if ($('input[name="is_direct_sale"]').length > 0) {
                //default scheme for sale screen
                var invoice_scheme_id = $(this)
                    .find(':selected')
                    .data('default_sale_invoice_scheme_id');
            } else {
                var invoice_scheme_id = $(this).find(':selected').data('default_invoice_scheme_id');
            }

            $('#invoice_scheme_id').val(invoice_scheme_id).change();
        }

        //Set default invoice layout for location
        if ($('#invoice_layout_id').length) {
            let invoice_layout_id = $(this).find(':selected').data('default_invoice_layout_id');
            $('#invoice_layout_id').val(invoice_layout_id).change();
        }

        //Set default price group
        if ($('#default_price_group').length) {
            var dpg = default_price_group ? default_price_group : 0;
            $('#default_price_group').val(dpg);
        }

        set_payment_type_dropdown();

        if ($('#types_of_service_id').length && $('#types_of_service_id').val()) {
            $('#types_of_service_id').change();
        }
    });

    //get customer
    $('select#customer_id').select2({
        ajax: {
            url: '/contacts/customers',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
        templateResult: function (data) {
            var template = '';
            if (data.supplier_business_name) {
                template += data.supplier_business_name + '<br>';
            }
            template += data.text + '<br>' + LANG.mobile + ': ' + data.mobile;

            if (typeof data.total_rp != 'undefined') {
                var rp = data.total_rp ? data.total_rp : 0;
                template += "<br><i class='fa fa-gift text-success'></i> " + rp;
            }

            return template;
        },
        minimumInputLength: 1,
        language: {
            inputTooShort: function (args) {
                return LANG.please_enter + args.minimum + LANG.or_more_characters;
            },
            noResults: function () {
                var name = $('#customer_id').data('select2').dropdown.$search.val();
                return (
                    '<button type="button" data-name="' +
                    name +
                    '" class="btn btn-link add_new_customer"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' +
                    __translate('add_name_as_new_customer', { name: name }) +
                    '</button>'
                );
            },
        },
        escapeMarkup: function (markup) {
            return markup;
        },
    });

    const changePriceTaxAsPeruser = () => {
        console.log('Calling change customer');
        try {
            const allTableData = document.querySelectorAll('#pos_table tbody .product_row');

            allTableData.forEach((row) => {
                let pricegroup_attr = row.getAttribute('pricegroup');
                if (!pricegroup_attr) {
                    console.warn('Missing pricegroup attribute for row:', row);
                    return;
                }

                let priceDetailsString;
                try {
                    priceDetailsString = JSON.parse(pricegroup_attr.replace(/&quot;/g, '"'));
                } catch (parseError) {
                    console.error('Error parsing pricegroup attribute:', parseError);
                    return;
                }

                const priceDetails = priceDetailsString.split(',').reduce((acc, item) => {
                    const [key, value] = item.split(':');
                    acc[key.trim()] = parseFloat(value);
                    return acc;
                }, {});
                const recallPrice = row.getAttribute('recallprice');
                const unitPrice = recallPrice ? parseFloat(recallPrice) : getUnitPriceLevel(selling_price_group_id, priceDetails);
                console.log('unitPrice', unitPrice, recallPrice);
                if (!unitPrice || isNaN(unitPrice)) {
                    console.warn('Invalid unit price for row:', row);
                    return;
                }

                const ml = row.getAttribute('ml');
                const ct = row.getAttribute('ct');
                const locationTaxType = row.getAttribute('locationtaxtype');
                const locationTaxTypeArray = JSON.parse(locationTaxType || '[]');

                const unitPriceInput = row.querySelector('input.pos_price_per_unit');
                unitPriceInput.value = unitPrice.toFixed(2);

                getProductTax(ml, ct, unitPrice, userTaxState, locationTaxTypeArray)
                    .then((res) => {
                        const { status, tax } = res;
                        const taxOnItem = status ? tax : 0;
                        console.log('eachunitPrice', selling_price_group_id, unitPrice, taxOnItem);

                        row.querySelector(
                            'span.pos_taxation_total_text'
                        ).textContent = `$ ${taxOnItem.toFixed(2)}`;

                        const totalAmt = unitPrice + taxOnItem;
                        var quantity = row.querySelector('input.pos_quantity').value;
                        // changes
                        // row.querySelector('input.pos_unit_price').value = totalAmt.toFixed(2);
                        // row.querySelector('input.pos_unit_price').defaultValue =
                        //     totalAmt.toFixed(2);
                        row.querySelector('input.pos_unit_price').value = parseFloat(unitPrice);
                        row.querySelector('input.pos_unit_price').defaultValue =
                            parseFloat(unitPrice);
                        row.querySelector('input.pos_unit_price_inc_tax').value =
                            totalAmt.toFixed(2);

                        row.querySelector('.old_pos_unit_price_tax').textContent =
                            taxOnItem.toFixed(2);
                        row.querySelector('input.pos_line_total').value = totalAmt * quantity;
                        let totalLineAmt = (quantity * totalAmt).toFixed(2);
                        row.querySelector('span.pos_line_total_text').textContent = `$ ${totalLineAmt}`;
                        console.log('tax aya', taxOnItem)
                        row.querySelector('input.pos_taxation_total').value = parseFloat(taxOnItem);
                        row.querySelector('input.pos_taxation_total').defaultValue =
                            parseFloat(taxOnItem);
                    })
                    .catch((err) => console.error('Error calculating tax:', err));

                // Call pos_total_row or equivalent function to update totals
                // pos_total_row();
            });
        } catch (error) {
            console.error('Error while changing price level and tax:', error);
        }
    };

    // Copy Button Functionality
    // document.getElementById('copyButton').addEventListener('click', function (event) {
    //     event.preventDefault();
    //     const tableBody = document.querySelector('#pos_table tbody');

    //     if (tableBody && tableBody.innerHTML.trim()) {
    //         localStorage.setItem('tableData', tableBody.innerHTML); // Save table rows in localStorage
    //         alert('Table data copied successfully!');
    //     } else {
    //         alert('No data found in the table to copy!');
    //     }
    // });

    // Paste Button Functionality
    // document.getElementById('pasteButton').addEventListener('click', function (event) {
    //     event.preventDefault();
    //     const tableBody = document.querySelector('#pos_table tbody');
    //     const storedData = localStorage.getItem('tableData'); // Retrieve data from localStorage

    //     console.log('storedData', storedData);

    //     console.log('selectedUser', selectedUser);
    //     if (storedData && tableBody) {
    //         tableBody.innerHTML = storedData; // Populate the table with stored rows

    //         // update the calculation
    //         if (sell_form_validator) {
    //             sell_form.valid();
    //         }
    //         if (pos_form_validator) {
    //             pos_form_validator.element($(this));
    //         }
    //         // var max_qty = parseFloat($(this).data('rule-max'));

    //         var entered_qty = __read_number($(this));
    //         var tr = $(this).parents('tr');

    //         var taxText = tr.find('span.pos_taxation_total_text').text();
    //         var taxPerUnit = parseFloat(taxText.split(' ')[1]);

    //         var unit_price_inc_tax = __read_number(tr.find('input.pos_unit_price_inc_tax'));
    //         var line_total = entered_qty * unit_price_inc_tax + entered_qty * taxPerUnit;

    //         __write_number(tr.find('input.pos_line_total'), line_total, false);
    //         tr.find('span.pos_line_total_text').text(__currency_trans_from_en(`$ ${line_total}`, true));

    //         //Change modifier quantity
    //         tr.find('.modifier_qty_text').each(function () {
    //             $(this).text(__currency_trans_from_en(entered_qty, false));
    //         });
    //         tr.find('.modifiers_quantity').each(function () {
    //             $(this).val(entered_qty);
    //         });

    //         pos_total_row();

    //         adjustComboQty(tr);

    //         // if (selectedUser) {
    //         //     setTimeout(()=>{
    //         //         changePriceTaxAsPeruser();
    //         //         alert('Table data pasted successfully!');
    //         //     },1000)
    //         // }else{
    //         //     alert('Table data pasted successfully!');
    //         // }
    //         alert('Table data pasted successfully!');
    //     } else {
    //         alert('No data available to paste!');
    //     }
    // });

    // Copy Button Functionality
    $('#copyButton').on('click', function (e) {
        e.preventDefault();

        let cartData = [];
        $('#pos_table tbody tr.product_row').each(function () {
            let row = $(this);
            let data = {
                variation_id: row.find('.row_variation_id').val(),
                qty: row.find('.input_quantity').val()
            };
            cartData.push(data);
        });

        // Copy JSON string to clipboard
        const jsonData = JSON.stringify(cartData);
        navigator.clipboard.writeText(jsonData).then(function () {
            toastr.success('Cart copied to clipboard successfully!');
        }).catch(function (err) {
            toastr.error('Failed to copy cart: ' + err);
        });
    });

    // Paste Button Functionality
    document.getElementById('pasteButton').addEventListener('click', function (event) {
        event.preventDefault();
        navigator.clipboard.readText()
            .then(text => {
                try {
                    const pastedData = JSON.parse(text);
                    let variationIds = [];
                    let quantities = [];

                    $(pastedData).each(function () {
                        var variationId = this.variation_id;
                        var quantity = this.qty ?? 1;

                        variationIds.push(variationId);
                        quantities.push(quantity);
                    });
                    if (variationIds.length > 0) {
                        pos_Matrix_row(variationIds.join(','), quantities.join(','));
                    }
                    toastr.success('Cart data pasted successfully!');
                } catch (e) {
                    toastr.error('Clipboard content is not valid JSON!');
                    console.error('Parsing error:', e);
                }
            })
            .catch(err => {
                toastr.error('Failed to read clipboard!');
            });


    });

    //     var urlParams = new URLSearchParams(window.location.search);
    //     var cid = urlParams.get('cid');
    //     if (cid) {
    //       $.ajax({
    //     url: '/contacts/customers',
    //     dataType: 'json',
    //     delay: 250,
    //     success: function (result) {
    //         // Find the customer with ID 17
    //         var selectedCustomer = result.find(obj => obj.id == cid);
    //         if (!selectedCustomer) {
    //             console.warn('Customer with ID not found.',cid);
    //             return;
    //         }

    //         // Set the value in the select2 dropdown (if not already selected)
    //         $('#select2-customer_id-container').text(selectedCustomer.text)

    //         // Manually trigger the select2:select event with custom data
    //         $('#customer_id').trigger({
    //             type: 'select2:select',
    //             params: {
    //                 data: selectedCustomer
    //             }
    //         });
    //     }
    // });


    //     }
    // original


    $('#customer_id').on('select2:select', function (e) {
        console.log('yes change customer this is function');

        // selling_price_group_id

        var data = e.params.data;
        userTaxState = data?.state ? data?.state : 'IL';
        selling_price_group_id = data?.selling_price_group_id;

        const isValidTaxState = US_STATES[userTaxState];
        console.log('isValidTaxState', isValidTaxState);
        if (!isValidTaxState) {
            alert('Selected tax state (State Code) is Invalid');
            return;
        }

        console.log('changeData', userTaxState, data);
        if (data.pay_term_number) {
            $('input#pay_term_number').val(data.pay_term_number);
        } else {
            $('input#pay_term_number').val('');
        }

        if (data.pay_term_type) {
            $('#add_sell_form select[name="pay_term_type"]').val(data.pay_term_type);
            $('#edit_sell_form select[name="pay_term_type"]').val(data.pay_term_type);
        } else {
            $('#add_sell_form select[name="pay_term_type"]').val('');
            $('#edit_sell_form select[name="pay_term_type"]').val('');
        }

        update_shipping_address(data);
        $('#advance_balance_text').text(__currency_trans_from_en(data.balance), true);
        $('#advance_balance').val(data.balance);

        if (data.price_calculation_type == 'selling_price_group') {
            $('#price_group').val(data.selling_price_group_id);
            $('#price_group').change();
        }
        //  else {
        //     $('#price_group').val(0);
        //     $('#price_group').change();
        // }
        if ($('.contact_due_text').length) {
            get_contact_due(data.id);
        }
        
        // Auto-check exempt tax checkbox if customer is tax exempt
        if (data.is_tax_exempt == 1 || data.is_tax_exempt === true) {
            $('#ex_taxes_checkbox').prop('checked', true).trigger('change');
        } else {
            $('#ex_taxes_checkbox').prop('checked', false).trigger('change');
        }
        
        $('#search_product').prop('disabled', false).focus();
        $('.copyPasteAdd').prop('disabled', false);
        changePriceTaxAsPeruser();
    });

    // // document query selector
    // $('#customer_id').on('select2:select', function (e) {
    //     console.log('yes change customer this is function');
    //     selectedUser = true;
    //     // selling_price_group_id

    //     var data = e.params.data;
    //     userTaxState = data?.state ? data?.state : 'IL';

    //     const isValidTaxState = US_STATES[userTaxState];
    //     console.log('isValidTaxState', isValidTaxState);
    //     if (!isValidTaxState) {
    //         alert('Selected tax state (State Code) is Invalid');
    //         return;
    //     }

    //     selling_price_group_id = data?.selling_price_group_id;
    //     console.log('changeData', userTaxState, data);
    //     if (data.pay_term_number) {
    //         $('input#pay_term_number').val(data.pay_term_number);
    //     } else {
    //         $('input#pay_term_number').val('');
    //     }

    //     if (data.pay_term_type) {
    //         $('#add_sell_form select[name="pay_term_type"]').val(data.pay_term_type);
    //         $('#edit_sell_form select[name="pay_term_type"]').val(data.pay_term_type);
    //     } else {
    //         $('#add_sell_form select[name="pay_term_type"]').val('');
    //         $('#edit_sell_form select[name="pay_term_type"]').val('');
    //     }

    //     update_shipping_address(data);
    //     $('#advance_balance_text').text(__currency_trans_from_en(data.balance), true);
    //     $('#advance_balance').val(data.balance);

    //     if (data.price_calculation_type == 'selling_price_group') {
    //         $('#price_group').val(data.selling_price_group_id);
    //         $('#price_group').change();
    //     }
    //     //  else {
    //     //     $('#price_group').val(0);
    //     //     $('#price_group').change();
    //     // }
    //     if ($('.contact_due_text').length) {
    //         get_contact_due(data.id);
    //     }

    //     const tableBody = document.querySelector('#pos_table tbody');

    //     if (tableBody && tableBody.innerHTML.trim()) {
    //         const newTbody = document.createElement('tbody');
    //         const rows = tableBody.querySelectorAll('tr');

    //         selling_price_group_id = data?.selling_price_group_id;
    //         console.log('allrows', rows, selling_price_group_id);

    //         for (let row of rows) {
    //             let pricegroup_attr = row.getAttribute('pricegroup');
    //             if (!pricegroup_attr) {
    //                 console.warn('Missing pricegroup attribute for row:', row);
    //                 continue;
    //             }

    //             const priceDetailsString = JSON.parse(pricegroup_attr.replace(/&quot;/g, '"'));
    //             const priceDetails = priceDetailsString?.split(',').reduce((acc, item) => {
    //                 const [key, value] = item?.split(':');
    //                 acc[key?.trim()] = parseFloat(value);
    //                 return acc;
    //             }, {});

    //             let unitPrice = getUnitPriceLevel(selling_price_group_id, priceDetails);
    //             if (unitPrice <= 0) {
    //                 console.warn('Invalid unit price for row:', row);
    //                 continue;
    //             }

    //             // Calculate tax
    //             const ml = row.getAttribute('ml');
    //             const ct = row.getAttribute('ct');
    //             const locationTaxType = row.getAttribute('locationtaxtype');
    //             const locationTaxTypeArray = JSON.parse(locationTaxType || '[]');

    //             console.log('update taxes', ml, ct, unitPrice, userTaxState, locationTaxTypeArray);

    //             getProductTax(ml, ct, unitPrice, userTaxState, locationTaxTypeArray)
    //                 .then((res) => {
    //                     const { status, tax } = res;
    //                     const taxOnItem = status ? tax : 0;
    //                     console.log('taxPerUnit', taxOnItem);

    //                     // Assuming 'row' is a DOM element
    //                     row.querySelector('span.pos_taxation_total_text').textContent =
    //                         taxOnItem.toFixed(2);

    //                     const totalAmt = parseFloat(unitPrice) + taxOnItem;
    //                     row.querySelector('input.pos_price_per_unit').value =
    //                         parseFloat(unitPrice).toFixed(2);
    //                     row.querySelector('input.pos_unit_price').value = totalAmt.toFixed(2);
    //                     row.querySelector('input.pos_unit_price').defaultValue =
    //                         totalAmt.toFixed(2);

    //                     row.querySelector('.old_pos_unit_price_tax').textContent =
    //                         taxOnItem.toFixed(2);

    //                     row.querySelector('input.pos_line_total').value = totalAmt;
    //                     row.querySelector('span.pos_line_total_text').textContent =
    //                         totalAmt.toFixed(2);

    //                     const rowHtml = row.outerHTML;
    //                     row.outerHTML = rowHtml;

    //                     newTbody.appendChild(row);

    //                     // Update totals
    //                     pos_total_row();
    //                 })
    //                 .catch((err) => console.error('Error calculating tax:', err));

    //             tableBody.parentNode.replaceChild(newTbody, tableBody);
    //         }
    //     }
    // });

    set_default_customer();


    // $('#search_product').on('paste', function (event) {
    //     // Use a timeout to ensure the paste content is available in the input
    //     let input = $(this);
    //     setTimeout(function () {
    //         let pastedData = input.val();
    //         console.log("Pasted content:", pastedData);
    //         handlePaste(pastedData);
    //     }, 0);
    // });

    // function handlePaste(data) {
    //     // Your custom logic
    //     alert("You pasted: " + data);
    // }


    if ($('#search_product').length && $('#toggle_switch').prop('checked')) {
        //Add Product
        $('#search_product')
            .autocomplete({
                delay: 1000,
                source: function (request, response) {
                    var is_metrix = $('#toggle_switch').prop('checked') ? true : false;
                    var price_group = '';
                    var search_fields = [];
                    $('.search_fields:checked').each(function (i) {
                        search_fields[i] = $(this).val();
                    });

                    if ($('#price_group').length > 0) {
                        price_group = $('#price_group').val();
                    }
                    $.getJSON(
                        '/products/list',
                        {
                            price_group: price_group,
                            location_id: $('input#location_id').val(),
                            term: request.term,
                            not_for_selling: 0,
                            search_fields: search_fields,
                            is_metrix: is_metrix,
                        },
                        response
                    );
                },
                minLength: 2,
                response: function (event, ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];

                        var is_overselling_allowed = false;
                        if ($('input#is_overselling_allowed').length) {
                            is_overselling_allowed = true;
                        }
                        var for_so = false;
                        if ($('#sale_type').length && $('#sale_type').val() == 'sales_order') {
                            for_so = true;
                        }

                        if (
                            (ui.item.enable_stock == 1 && ui.item.qty_available > 0) ||
                            ui.item.enable_stock == 0 ||
                            is_overselling_allowed ||
                            for_so
                        ) {
                            $(this)
                                .data('ui-autocomplete')
                                ._trigger('select', 'autocompleteselect', ui);
                            $(this).autocomplete('close');
                        }
                    } else if (ui.content.length == 0) {
                        toastr.error(LANG.no_products_found);
                        $('input#search_product').animate().focus();
                    }
                },
                focus: function (event, ui) {
                    if (ui.item.qty_available <= 0) {
                        return false;
                    }
                },
                select: function (event, ui) {
                    var searched_term = $(this).val();
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
                        ($('input#status').val() == 'quotation' ||
                            $('input#status').val() == 'draft')
                    ) {
                        var is_draft = true;
                    }

                    if (
                        ui.item.enable_stock != 1 ||
                        ui.item.qty_available > 0 ||
                        is_overselling_allowed ||
                        for_so ||
                        is_draft
                    ) {
                        $(this).val(null);

                        //Pre select lot number only if the searched term is same as the lot number
                        var purchase_line_id =
                            ui.item.purchase_line_id && searched_term == ui.item.lot_number
                                ? ui.item.purchase_line_id
                                : null;
                        // pos_product_row(ui.item.variation_id, purchase_line_id);
                    } else {
                        alert(LANG.out_of_stock);
                    }
                },
            })
            .autocomplete('instance')._renderItem = function (ul, item) {
                var is_metrix = $('#toggle_switch').prop('checked') ? true : false;
                let productId = item.id;
                let priceGroupId = $('#hidden_price_group').val();
                if (is_metrix) {
                    return $('<li>')
                        .append(`<a href="#" data-href="/sells/pos/getmatrixproduct/${productId}/${priceGroupId}" class="btn-modal" data-container=".view_modal">${item.name}  ${item.name}</a>`)
                        .appendTo(ul);
                }
            };

    }

    if ($('#search_product').length && !$('#toggle_switch').prop('checked')) {
        //Add Product
        let pasteQueue = [];
        let isProcessingPaste = false;
        let isPest = false;

        $('#search_product').on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Optional: prevents form submission
                console.log('Enter key pressed:', $(this).val());
                // Add your search or action logic here
                let input = $(this);
                isPest = true;

                // Get clipboard data
                // let clipboardData = (e.originalEvent || e).clipboardData || window.clipboardData;
                let pastedText = $(this).val();
                input.val('');

                // You can split pastedText by newline if multiple barcodes were pasted together
                let entries = pastedText.split(/\r?\n/).filter(Boolean);
                pasteQueue.push(...entries);

                // Clear the field (optional, so autocomplete can re-trigger same value)


                // Start processing queue
                if (!isProcessingPaste) {
                    processNextPaste();
                }

            }

        });

        function processNextPaste() {
            if (pasteQueue.length === 0) {
                isProcessingPaste = false;
                return;
            }

            isProcessingPaste = true;
            let nextValue = pasteQueue.shift();
            let input = $('#search_product');

            input.val(nextValue);
            var is_added = false;
            //Search for variation id in each row of pos table
            // $('#pos_table tbody')
            //     .find('tr')
            //     .each(function () {
            //         var row_v_id = $(this).find('.row_SKU').val();
            //         if (
            //             row_v_id == nextValue
            //         ) {
            //             is_added = true;

            //             //Increment product quantity
            //             qty_element = $(this).find('.pos_quantity');
            //             var qty = __read_number(qty_element);
            //             __write_number(qty_element, qty + 1);
            //             qty_element.change();

            //             round_row_to_iraqi_dinnar($(this));

            //             $('#search_product').focus().select();
            //             $('#search_product').val('');
            //             processNextPaste();
            //         }
            //     });
            if (!is_added) {
                input.autocomplete("search", nextValue);
            }
        }

        $('#search_product')
            .autocomplete({
                delay: isPest ? 0 : 1000,
                minLength: 1,
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
                    isPest = false;
                    if (ui.content.length === 0) {
                        toastr.warning(LANG.no_products_found);
                    } else if (ui.content.length === 1) {
                        ui.item = ui.content[0];
                        $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');
                    }

                    processNextPaste(); // Move to next item
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
                    let searched_term = $(this).val();
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
                        let purchase_line_id =
                            ui.item.purchase_line_id && searched_term == ui.item.lot_number
                                ? ui.item.purchase_line_id
                                : null;

                        pos_product_row(ui.item.variation_id, purchase_line_id);
                    } else {
                        toastr.warning(LANG.out_of_stock);
                    }

                    return false; // Prevent default selection behavior
                }
            })
            .autocomplete('instance')._renderItem = function (ul, item) {
                var is_metrix = $('#toggle_switch').prop('checked') ? true : false;
                let productId = item.id;
                let priceGroupId = $('#hidden_price_group').val();
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
                        // var string = '<div>' + item.name;
                        // if (item.type == 'variable') {
                        //     string += '-' + item.variation;
                        // }

                        // var selling_price = item.selling_price;
                        // if (item.variation_group_price) {
                        //     selling_price = item.variation_group_price;
                        // }

                        // string +=
                        //     ' (' +
                        //     item.sub_sku +
                        //     ')' +
                        //     '<br> Price: ' +
                        //     __currency_trans_from_en(
                        //         selling_price,
                        //         false,
                        //         false,
                        //         __currency_precision,
                        //         true
                        //     );
                        // if (item.enable_stock == 1) {
                        //     var qty_available = __currency_trans_from_en(
                        //         item.qty_available,
                        //         false,
                        //         false,
                        //         __currency_precision,
                        //         true
                        //     );
                        //     string += ' - ' + qty_available + item.unit;
                        // }
                        // string += '</div>';

                        var string = '<div>' + item.sub_sku + "   ";
                        string += item.name
                        if (item.type == 'variable') { string += '-' + item.variation; }
                        string += '</div>';

                        return $('<li>').append(string).appendTo(ul);
                    }
                }

            };

    }

    //Update line total and check for quantity not greater than max quantity
    // $('table#pos_table tbody').on('change', 'input.pos_quantity', function () {
    //     console.log('yes updateqty this is table function ');
    //     if (sell_form_validator) {
    //         sell_form.valid();
    //     }
    //     if (pos_form_validator) {
    //         pos_form_validator.element($(this));
    //     }
    //     // var max_qty = parseFloat($(this).data('rule-max'));

    //     var entered_qty = __read_number($(this));
    //     var tr = $(this).parents('tr');

    //     // var taxText = tr.find('span.pos_taxation_total_text').text();
    //     // var taxText = tr.find('span.item_tax').value;
    //     // console.log('taxText', taxText);
    //     // var taxPerUnit = parseFloat(taxText.split(' ')[1]);
    //     var taxPerUnit = parseFloat(__read_number(tr.find('input.item_tax')))

    //     __write_number(tr.find('input.pos_taxation_total'), taxPerUnit, false);

    //     // var unit_price_inc_tax = parseFloat(__read_number(tr.find('input.pos_unit_price_inc_tax')));
    //     var unit_price_inc_tax = parseFloat(__read_number(tr.find('input.pos_unit_price')));
    //     // var unit_price_inc_tax = tr.find('input.pos_unit_price').val();
    //     console.log('unit_price_inc_tax', unit_price_inc_tax);
    //     var line_total = entered_qty * unit_price_inc_tax + entered_qty * taxPerUnit;

    //     console.log('line_total', entered_qty, unit_price_inc_tax, taxPerUnit, line_total);

    //     __write_number(tr.find('input.pos_line_total'), line_total, false);
    //     tr.find('span.pos_line_total_text').text(__currency_trans_from_en(`$ ${line_total}`, true));

    //     //Change modifier quantity
    //     tr.find('.modifier_qty_text').each(function () {
    //         $(this).text(__currency_trans_from_en(entered_qty, false));
    //     });
    //     tr.find('.modifiers_quantity').each(function () {
    //         $(this).val(entered_qty);
    //     });

    //     pos_total_row();

    //     adjustComboQty(tr);
    // });

    // $('table#pos_table tbody').on('change', 'input.pos_quantity', function () {
    //     console.log('yes updateqty this is table function ');
    //     if (sell_form_validator) {
    //         sell_form.valid();
    //     }
    //     if (pos_form_validator) {
    //         pos_form_validator.element($(this));
    //     }

    //     var entered_qty = __read_number($(this));
    //     var tr = $(this).parents('tr');


    //     var taxPerUnit = parseFloat(__read_number(tr.find('input.item_tax')))

    //     __write_number(tr.find('input.pos_taxation_total'), taxPerUnit, false);

    //     var unit_price_inc_tax = parseFloat(__read_number(tr.find('input.pos_unit_price')));
    //     console.log('unit_price_inc_tax', unit_price_inc_tax);
    //     var line_total = entered_qty * unit_price_inc_tax + entered_qty * taxPerUnit;

    //     console.log('line_total', entered_qty, unit_price_inc_tax, taxPerUnit, line_total);

    //     __write_number(tr.find('input.pos_line_total'), line_total, false);
    //     tr.find('span.pos_line_total_text').text(__currency_trans_from_en(`$ ${line_total}`, true));

    //     tr.find('.modifier_qty_text').each(function () {
    //         $(this).text(__currency_trans_from_en(entered_qty, false));
    //     });
    //     tr.find('.modifiers_quantity').each(function () {
    //         $(this).val(entered_qty);
    //     });

    //     pos_total_row();

    //     adjustComboQty(tr);
    // });
    $('table#pos_table tbody').on('change', 'input.pos_quantity', function () {
        // console.log("Quantity changed...");

        let tr = $(this).closest('tr');
        let quantity = parseFloat($(this).val()) || 1;
        const recallPrice = tr.attr('recallprice');
        const unitPrice = recallPrice ? parseFloat(recallPrice) : parseFloat(tr.find('input.pos_unit_price').val()) || 0;
        let taxPerUnit = parseFloat(tr.find('input.pos_taxation_total').val()) || 0;

        // Check if tax checkbox is checked
        if ($('#ex_taxes_checkbox').is(':checked')) {
            taxPerUnit = 0; // Ensure tax stays 0 if checkbox is checked
            tr.find('input.pos_taxation_total').val(0);
            tr.find('span.pos_taxation_total_text').text('$ 0.00');
        }

        // Calculate new line total
        let lineTotal = (unitPrice + taxPerUnit) * quantity;
        tr.find('input.pos_line_total').val(lineTotal.toFixed(2));
        tr.find('span.pos_line_total_text').text(`$ ${lineTotal.toFixed(2)}`);

        pos_total_row(); // Refresh total after quantity change

    });
    $('table#pos_table tbody').on('change keyup', 'input.pos_quantity', function () {
        // console.log("Quantity changed...");

        let tr = $(this).closest('tr');
        let quantity = parseFloat($(this).val()) || 1;
        const recallPrice = tr.attr('recallprice');
        const unitPrice = recallPrice ? parseFloat(recallPrice) : parseFloat(tr.find('input.pos_unit_price').val()) || 0;
        let taxPerUnit = parseFloat(tr.find('input.pos_taxation_total').val()) || 0;

        // Check if tax checkbox is checked
        if ($('#ex_taxes_checkbox').is(':checked')) {
            taxPerUnit = 0; // Ensure tax stays 0 if checkbox is checked
            tr.find('input.pos_taxation_total').val(0);
            tr.find('span.pos_taxation_total_text').text('$ 0.00');
        }

        // Calculate new line total
        let lineTotal = (unitPrice + taxPerUnit) * quantity;
        tr.find('input.pos_line_total').val(lineTotal.toFixed(2));
        tr.find('span.pos_line_total_text').text(`$ ${lineTotal.toFixed(2)}`);

        pos_total_row(); // Refresh total after quantity change
    });




    //If change in unit price update price including tax and line total
    $('table#pos_table tbody').on('change', 'input.pos_unit_price', function () {
        console.log('my price change');
        var tr = $(this).parents('tr');
        const recallPrice = tr.attr('recallprice');
        const unitPrice = recallPrice ? parseFloat(recallPrice) : parseFloat($(this).val()) || 0;

        //calculate discounted unit price
        var discounted_unit_price = calculate_discounted_unit_price(tr);

        var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
        var quantity = __read_number(tr.find('input.pos_quantity'));

        var taxText = tr.find('span.pos_taxation_total_text').text();
        var taxPerUnit = parseFloat(taxText.split(' ')[1]);

        var unit_price_inc_tax = __add_percent(discounted_unit_price, tax_rate);
        var line_total = quantity * unit_price_inc_tax + quantity * taxPerUnit;

        __write_number(tr.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax);

        tr.find('input.pos_unit_price').val(unit_price_inc_tax);
        tr.find('input.pos_unit_price')[0].defaultValue = unit_price_inc_tax;

        __write_number(tr.find('input.pos_line_total'), line_total);

        tr.find('span.pos_line_total_text').text(__currency_trans_from_en(`$ ${line_total}`, true));
        pos_each_row(tr);
        pos_total_row();
        round_row_to_iraqi_dinnar(tr);
    });

    //If change in tax rate then update unit price according to it.
    $('table#pos_table tbody').on('change', 'select.tax_id', function () {
        var tr = $(this).parents('tr');

        var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
        var unit_price_inc_tax = __read_number(tr.find('input.pos_unit_price_inc_tax'));

        var discounted_unit_price = __get_principle(unit_price_inc_tax, tax_rate);
        var unit_price = get_unit_price_from_discounted_unit_price(tr, discounted_unit_price);
        __write_number(tr.find('input.pos_unit_price'), unit_price);
        pos_each_row(tr);
    });

    //If change in unit price including tax, update unit price
    $('table#pos_table tbody').on('change', 'input.pos_unit_price_inc_tax', function () {
        var unit_price_inc_tax = __read_number($(this));

        if (iraqi_selling_price_adjustment) {
            unit_price_inc_tax = round_to_iraqi_dinnar(unit_price_inc_tax);
            __write_number($(this), unit_price_inc_tax);
        }

        var tr = $(this).parents('tr');

        var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
        var quantity = __read_number(tr.find('input.pos_quantity'));

        var line_total = quantity * unit_price_inc_tax;
        var discounted_unit_price = __get_principle(unit_price_inc_tax, tax_rate);
        var unit_price = get_unit_price_from_discounted_unit_price(tr, discounted_unit_price);

        __write_number(tr.find('input.pos_unit_price'), unit_price);
        __write_number(tr.find('input.pos_line_total'), line_total, false);
        tr.find('span.pos_line_total_text').text(__currency_trans_from_en(`$ ${line_total}`, true));

        pos_each_row(tr);
        pos_total_row();
    });

    //Change max quantity rule if lot number changes
    $('table#pos_table tbody').on('change', 'select.lot_number', function () {
        var qty_element = $(this).closest('tr').find('input.pos_quantity');

        var tr = $(this).closest('tr');
        var multiplier = 1;
        var unit_name = '';
        var sub_unit_length = tr.find('select.sub_unit').length;
        if (sub_unit_length > 0) {
            var select = tr.find('select.sub_unit');
            multiplier = parseFloat(select.find(':selected').data('multiplier'));
            unit_name = select.find(':selected').data('unit_name');
        }
        var allow_overselling = qty_element.data('allow-overselling');
        if ($(this).val() && !allow_overselling) {
            var lot_qty = $('option:selected', $(this)).data('qty_available');
            var max_err_msg = $('option:selected', $(this)).data('msg-max');

            if (sub_unit_length > 0) {
                lot_qty = lot_qty / multiplier;
                var lot_qty_formated = __number_f(lot_qty, false);
                max_err_msg = __translate('lot_max_qty_error', {
                    max_val: lot_qty_formated,
                    unit_name: unit_name,
                });
            }

            qty_element.attr('data-rule-max-value', lot_qty);
            qty_element.attr('data-msg-max-value', max_err_msg);

            qty_element.rules('add', {
                'max-value': lot_qty,
                messages: {
                    'max-value': max_err_msg,
                },
            });
        } else {
            var default_qty = qty_element.data('qty_available');
            var default_err_msg = qty_element.data('msg_max_default');
            if (sub_unit_length > 0) {
                default_qty = default_qty / multiplier;
                var lot_qty_formated = __number_f(default_qty, false);
                default_err_msg = __translate('pos_max_qty_error', {
                    max_val: lot_qty_formated,
                    unit_name: unit_name,
                });
            }

            qty_element.attr('data-rule-max-value', default_qty);
            qty_element.attr('data-msg-max-value', default_err_msg);

            qty_element.rules('add', {
                'max-value': default_qty,
                messages: {
                    'max-value': default_err_msg,
                },
            });
        }
        qty_element.trigger('change');
    });

    //Change in row discount type or discount amount
    $('table#pos_table tbody').on(
        'change',
        'select.row_discount_type, input.row_discount_amount',
        function () {
            var tr = $(this).parents('tr');

            //calculate discounted unit price
            var discounted_unit_price = calculate_discounted_unit_price(tr);

            // var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
            var tax_rate = tr.find('.pos_taxation_total_text').text().replace('$', '').trim();
            var quantity = __read_number(tr.find('input.pos_quantity'));

            var unit_price_inc_tax = __add_percent(parseFloat(discounted_unit_price) + parseFloat(tax_rate));
            var line_total = quantity * unit_price_inc_tax;

            __write_number(tr.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax);
            __write_number(tr.find('input.pos_line_total'), line_total, false);
            tr.find('span.pos_line_total_text').text(__currency_trans_from_en(`$ ${line_total}`, true));
            // pos_each_row(tr);
            pos_total_row();
            round_row_to_iraqi_dinnar(tr);
        }
    );

    //Remove row on click on remove row
    $('table#pos_table tbody').on('click', 'i.pos_remove_row', function () {
        $(this).parents('tr').remove();
        pos_total_row();
    });

    //Cancel the invoice
    $('button#pos-cancel').click(function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((confirm) => {
            if (confirm) {
                reset_pos_form();
            }
        });
    });

    //Save invoice as draft
    $('button#pos-draft').click(function () {
        //Check if product is present or not.
        if ($('table#pos_table tbody').find('.product_row').length <= 0) {
            toastr.warning(LANG.no_products_added);
            return false;
        }

        var is_valid = isValidPosForm();
        if (is_valid != true) {
            return;
        }

        var data = pos_form_obj.serialize();
        data = data + '&status=draft';
        var url = pos_form_obj.attr('action');

        disable_pos_form_actions();
        $.ajax({
            method: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            success: function (result) {
                enable_pos_form_actions();
                if (result.success == 1) {
                    reset_pos_form();
                    toastr.success(result.msg);
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });

    //Save invoice as Quotation
    $('button#pos-quotation').click(function () {
        //Check if product is present or not.
        if ($('table#pos_table tbody').find('.product_row').length <= 0) {
            toastr.warning(LANG.no_products_added);
            return false;
        }

        var is_valid = isValidPosForm();
        if (is_valid != true) {
            return;
        }

        var data = pos_form_obj.serialize();
        data = data + '&status=quotation';
        var url = pos_form_obj.attr('action');

        disable_pos_form_actions();
        $.ajax({
            method: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            success: function (result) {
                enable_pos_form_actions();
                if (result.success == 1) {
                    reset_pos_form();
                    toastr.success(result.msg);

                    //Check if enabled or not
                    if (result.receipt.is_enabled) {
                        pos_print(result.receipt);
                    }
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });

    //Finalize invoice, open payment modal
    $('button#pos-finalize').click(function () {
        //Check if product is present or not.
        if ($('table#pos_table tbody').find('.product_row').length <= 0) {
            toastr.warning(LANG.no_products_added);
            return false;
        }

        if ($('#reward_point_enabled').length) {
            var validate_rp = isValidatRewardPoint();
            if (!validate_rp['is_valid']) {
                toastr.error(validate_rp['msg']);
                return false;
            }
        }

        $('#modal_payment').modal('show');
    });

    $('#modal_payment').one('shown.bs.modal', function () {
        $('#modal_payment').find('input').filter(':visible:first').focus().select();
        if ($('form#edit_pos_sell_form').length == 0) {
            $(this).find('#method_0').change();
        }
    });

    //Finalize without showing payment options
    $('button.pos-express-finalize').click(function () {
        //Check if product is present or not.
        if ($('table#pos_table tbody').find('.product_row').length <= 0) {
            toastr.warning(LANG.no_products_added);
            return false;
        }

        if ($('#reward_point_enabled').length) {
            var validate_rp = isValidatRewardPoint();
            if (!validate_rp['is_valid']) {
                toastr.error(validate_rp['msg']);
                return false;
            }
        }

        var pay_method = $(this).data('pay_method');

        //If pay method is credit sale submit form
        if (pay_method == 'credit_sale') {
            $('#is_credit_sale').val(1);
            pos_form_obj.submit();
            return true;
        } else {
            if ($('#is_credit_sale').length) {
                $('#is_credit_sale').val(0);
            }
        }

        //Check for remaining balance & add it in 1st payment row
        var total_payable = __read_number($('input#final_total_input'));
        var total_paying = __read_number($('input#total_paying_input'));
        if (total_payable > total_paying) {
            var bal_due = total_payable - total_paying;

            var first_row = $('#payment_rows_div').find('.payment-amount').first();
            var first_row_val = __read_number(first_row);
            first_row_val = first_row_val + bal_due;
            __write_number(first_row, first_row_val);
            first_row.trigger('change');
        }

        //Change payment method.
        var payment_method_dropdown = $('#payment_rows_div')
            .find('.payment_types_dropdown')
            .first();

        payment_method_dropdown.val(pay_method);
        payment_method_dropdown.change();
        if (pay_method == 'card') {
            $('div#card_details_modal').modal('show');
        } else if (pay_method == 'suspend') {
            $('div#confirmSuspendModal').modal('show');
        } else {
            pos_form_obj.submit();
        }
    });

    $('div#card_details_modal').on('shown.bs.modal', function (e) {
        $('input#card_number').focus();
    });

    $('div#confirmSuspendModal').on('shown.bs.modal', function (e) {
        $(this).find('textarea').focus();
    });

    //on save card details
    $('button#pos-save-card').click(function () {
        $('input#card_number_0').val($('#card_number').val());
        $('input#card_holder_name_0').val($('#card_holder_name').val());
        $('input#card_transaction_number_0').val($('#card_transaction_number').val());
        $('select#card_type_0').val($('#card_type').val());
        $('input#card_month_0').val($('#card_month').val());
        $('input#card_year_0').val($('#card_year').val());
        $('input#card_security_0').val($('#card_security').val());

        $('div#card_details_modal').modal('hide');
        pos_form_obj.submit();
    });

    $('button#pos-suspend').click(function () {
        $('input#is_suspend').val(1);
        $('div#confirmSuspendModal').modal('hide');
        pos_form_obj.submit();
        $('input#is_suspend').val(0);
    });

    //fix select2 input issue on modal
    $('#modal_payment')
        .find('.select2')
        .each(function () {
            $(this).select2({
                dropdownParent: $('#modal_payment'),
            });
        });

    $('button#add-payment-row').click(function () {
        var row_index = $('#payment_row_index').val();
        var location_id = $('input#location_id').val();
        $.ajax({
            method: 'POST',
            url: '/sells/pos/get_payment_row',
            data: { row_index: row_index, location_id: location_id },
            dataType: 'html',
            success: function (result) {
                if (result) {
                    var appended = $('#payment_rows_div').append(result);

                    var total_payable = __read_number($('input#final_total_input'));
                    var total_paying = __read_number($('input#total_paying_input'));
                    var b_due = total_payable - total_paying;
                    $(appended).find('input.payment-amount').focus();
                    $(appended)
                        .find('input.payment-amount')
                        .last()
                        .val(__currency_trans_from_en(b_due, false))
                        .change()
                        .select();
                    __select2($(appended).find('.select2'));
                    $(appended)
                        .find('#method_' + row_index)
                        .change();
                    $('#payment_row_index').val(parseInt(row_index) + 1);
                }
            },
        });
    });

    $(document).on('click', '.remove_payment_row', function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $(this).closest('.payment_row').remove();
                calculate_balance_due();
            }
        });
    });









    pos_form_validator = pos_form_obj.validate({
        submitHandler: function (form) {
            // var total_payble = __read_number($('input#final_total_input'));
            // var total_paying = __read_number($('input#total_paying_input'));
            var cnf = true;

            //Ignore if the difference is less than 0.5
            if ($('input#in_balance_due').val() >= 0.5) {
                cnf = confirm(LANG.paid_amount_is_less_than_payable);
                // if( total_payble > total_paying ){
                // 	cnf = confirm( LANG.paid_amount_is_less_than_payable );
                // } else if(total_payble < total_paying) {
                // 	alert( LANG.paid_amount_is_more_than_payable );
                // 	cnf = false;
                // }
            }

            var total_advance_payments = 0;
            $('#payment_rows_div')
                .find('select.payment_types_dropdown')
                .each(function () {
                    if ($(this).val() == 'advance') {
                        total_advance_payments++;
                    }
                });

            if (total_advance_payments > 1) {
                alert(LANG.advance_payment_cannot_be_more_than_once);
                return false;
            }

            var is_msp_valid = true;
            //Validate minimum selling price if hidden
            $('.pos_unit_price_inc_tax').each(function () {
                if (!$(this).is(':visible') && $(this).data('rule-min-value')) {
                    var val = __read_number($(this));
                    var error_msg_td = $(this)
                        .closest('tr')
                        .find('.pos_line_total_text')
                        .closest('td');
                    if (val > $(this).data('rule-min-value')) {
                        is_msp_valid = false;
                        error_msg_td.append(
                            '<label class="error">' + $(this).data('msg-min-value') + '</label>'
                        );
                    } else {
                        error_msg_td.find('label.error').remove();
                    }
                }
            });

            if (!is_msp_valid) {
                return false;
            }

            if (cnf) {
                disable_pos_form_actions();

                var data = $(form).serialize();
                data = data + '&status=final';
                var url = $(form).attr('action');
                $.ajax({
                    method: 'POST',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function (result) {
                        if (result.success == 1) {
                            if (result.whatsapp_link) {
                                window.open(result.whatsapp_link);
                            }
                            $('#modal_payment').modal('hide');
                            toastr.success(result.msg);

                            reset_pos_form();

                            //Check if enabled or not
                            if (result.receipt.is_enabled) {
                                pos_print(result.receipt);
                            }
                        } else {
                            toastr.error(result.msg);
                        }

                        enable_pos_form_actions();
                    },
                });
            }
            return false;
        },
    });

    $(document).on('change', '.payment-amount', function () {
        calculate_balance_due();
    });

    //Update discount
    $('button#posEditDiscountModalUpdate').click(function () {
        //if discount amount is not valid return false
        if (!$('#discount_amount_modal').valid()) {
            return false;
        }
        //Close modal
        $('div#posEditDiscountModal').modal('hide');

        //Update values
        $('input#discount_type').val($('select#discount_type_modal').val());
        __write_number($('input#discount_amount'), __read_number($('input#discount_amount_modal')));

        if ($('#reward_point_enabled').length) {
            var reward_validation = isValidatRewardPoint();
            if (!reward_validation['is_valid']) {
                toastr.error(reward_validation['msg']);
                $('#rp_redeemed_modal').val(0);
                $('#rp_redeemed_modal').change();
            }
            updateRedeemedAmount();
        }

        pos_total_row();
    });

    //Shipping
    $('button#posShippingModalUpdate').click(function () {
        //Close modal
        $('div#posShippingModal').modal('hide');

        //update shipping details
        $('input#shipping_details').val($('#shipping_details_modal').val());

        $('input#shipping_address').val($('#shipping_address_modal').val());
        $('input#shipping_status').val($('#shipping_status_modal').val());
        $('input#delivered_to').val($('#delivered_to_modal').val());
        $('input#delivery_person').val($('#delivery_person_modal').val());

        //Update shipping charges
        __write_number(
            $('input#shipping_charges'),
            __read_number($('input#shipping_charges_modal'))
        );

        //$('input#shipping_charges').val(__read_number($('input#shipping_charges_modal')));

        pos_total_row();
    });

    $('#posShippingModal').on('shown.bs.modal', function () {
        $('#posShippingModal')
            .find('#shipping_details_modal')
            .filter(':visible:first')
            .focus()
            .select();
        // $('.select2-selection__rendered').css('padding-right', '150px');
    });

    $(document).on('shown.bs.modal', '.row_edit_product_price_model', function () {
        var $modal = $(this);
        $modal.find('input').filter(':visible:first').focus().select();
        // Init select2 on dropdowns with dropdownParent to prevent clipping
        $modal.find('select.row_discount_type, select.tax_id, select[name*="warranty_id"]').each(function () {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({ dropdownParent: $('body'), width: '100%' });
            }
        });
    });

    //Update Order tax
    $('button#posEditOrderTaxModalUpdate').click(function () {
        //Close modal
        $('div#posEditOrderTaxModal').modal('hide');

        var tax_obj = $('select#order_tax_modal');
        var tax_id = tax_obj.val();
        var tax_rate = tax_obj.find(':selected').data('rate');

        $('input#tax_rate_id').val(tax_id);

        __write_number($('input#tax_calculation_amount'), tax_rate);
        pos_total_row();
    });

    $(document).on('click', '.add_new_customer', function () {
        $('#customer_id').select2('close');
        var name = $(this).data('name');
        $('.contact_modal').find('input#name').val(name);
        $('.contact_modal')
            .find('select#contact_type')
            .val('customer')
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
                            }).then((willContinue) => {
                                if (willContinue) {
                                    submitQuickContactForm(form);
                                } else {
                                    $('#mobile').select();
                                }
                            });
                        } else {
                            submitQuickContactForm(form);
                        }
                    },
                });
            },
        });
    $('.contact_modal').on('hidden.bs.modal', function () {
        $('form#quick_add_contact').find('button[type="submit"]').removeAttr('disabled');
        $('form#quick_add_contact')[0].reset();
    });

    //Updates for add sell
    $(
        'select#discount_type, input#discount_amount, input#shipping_charges, \
        input#rp_redeemed_amount'
    ).change(function () {
        pos_total_row();
    });
    $('select#tax_rate_id').change(function () {
        var tax_rate = $(this).find(':selected').data('rate');
        __write_number($('input#tax_calculation_amount'), tax_rate);
        pos_total_row();
    });
    //Datetime picker
    $('#transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });

    //Direct sell submit
    sell_form = $('form#add_sell_form');
    if ($('form#edit_sell_form').length) {
        sell_form = $('form#edit_sell_form');
        pos_total_row();
    }
    sell_form_validator = sell_form.validate();

    $('button#submit-sell, button#save-and-print').click(function (e) {
        e.preventDefault();
        combineRows();
        $('button#submit-sell').prop('disabled', true);
        $('button#save-and-print').prop('disabled', true);

        if ($('table#pos_table tbody').find('.product_row').length <= 0) {
            toastr.warning(LANG.no_products_added);
            $('button#submit-sell').prop('disabled', false);
            $('button#save-and-print').prop('disabled', false);
            return false;
        }

        var is_msp_valid = true;
        //Validate minimum selling price if hidden
        $('.pos_unit_price_inc_tax').each(function () {
            if (!$(this).is(':visible') && $(this).data('rule-min-value')) {
                var val = __read_number($(this));
                var error_msg_td = $(this).closest('tr').find('.pos_line_total_text').closest('td');
                if (val > $(this).data('rule-min-value')) {
                    is_msp_valid = false;
                    error_msg_td.append(
                        '<label class="error">' + $(this).data('msg-min-value') + '</label>'
                    );
                } else {
                    error_msg_td.find('label.error').remove();
                }
            }
        });

        if (!is_msp_valid) {
            // Re-enable both buttons if validation fails
            $('button#submit-sell').prop('disabled', false);
            $('button#save-and-print').prop('disabled', false);
            return false;
        }

        if ($(this).attr('id') == 'save-and-print') {
            $('#is_save_and_print').val(1);
        } else {
            $('#is_save_and_print').val(0);
        }

        if ($('#reward_point_enabled').length) {
            var validate_rp = isValidatRewardPoint();
            if (!validate_rp['is_valid']) {
                toastr.error(validate_rp['msg']);
                // Re-enable both buttons if validation fails
                $('button#submit-sell').prop('disabled', false);
                $('button#save-and-print').prop('disabled', false);
                return false;
            }
        }

        if ($('.enable_cash_denomination_for_payment_methods').length) {
            var payment_row = $('.enable_cash_denomination_for_payment_methods').closest(
                '.payment_row'
            );
            var is_valid = true;
            var payment_type = payment_row.find('.payment_types_dropdown').val();
            var denomination_for_payment_types = JSON.parse(
                $('.enable_cash_denomination_for_payment_methods').val()
            );
            if (
                denomination_for_payment_types.includes(payment_type) &&
                payment_row.find('.is_strict').length &&
                payment_row.find('.is_strict').val() === '1'
            ) {
                var payment_amount = __read_number(payment_row.find('.payment-amount'));
                var total_denomination = payment_row.find('input.denomination_total_amount').val();
                if (payment_amount != total_denomination) {
                    is_valid = false;
                }
            }

            if (!is_valid) {
                payment_row.find('.cash_denomination_error').removeClass('hide');
                toastr.error(payment_row.find('.cash_denomination_error').text());
                $('button#submit-sell').prop('disabled', false);
                $('button#save-and-print').prop('disabled', false);
                e.preventDefault();
                return false;
            } else {
                payment_row.find('.cash_denomination_error').addClass('hide');
            }
        }

        if (sell_form.valid()) {
            // Show invoice preview modal
            showInvoicePreview();
        } else {
            $('button#submit-sell').prop('disabled', false);
            $('button#save-and-print').prop('disabled', false);
        }
    });

    // Add the showInvoicePreview function
    function showInvoicePreview() {
        // Get all the necessary data from the form
        let shippingVal = __read_number($('input#shipping_charges, input[name="shipping_charges"]')) || 0;
        if (isNaN(shippingVal)) shippingVal = 0;
        let invoiceData = {
            products: [],
            subtotal: get_subtotal(),
            discount: $('#discount_amount').val() || 0,
            final_total: calculate_balance_due(),
            tax: 0,
            final_total: $('.price_total').text(),
            total_quantity: $('.total_quantity').text(),
            shipping_charges: shippingVal
        };
        // Get all product rows
        $('#pos_table tbody tr').each(function () {
            const row = $(this);
            let taxTextRaw = row.find('.pos_taxation_total_text').text();
            let taxText = taxTextRaw.replace(/[^0-9.\-]+/g, '').trim();
            let taxValue = parseFloat(taxText);
            invoiceData.tax += taxValue;

            let subtotal_textRaw=row.find('.pos_line_total_text').text();
            let subtotal_text=subtotal_textRaw.replace(/[^0-9.\-]+/g, '').trim();
            let subtotal_value=parseFloat(subtotal_text)

            const product = {
                name: row.find('.product_name_class').text(),
                quantity: row.find('.pos_quantity').val(),
                unit_price: row.find('.pos_unit_price').val(),
                tax: row.find('.pos_taxation_total_text').text(),
                total: subtotal_value,
            };
            invoiceData.products.push(product);
        });

        // Populate the preview table
        let previewHtml = '';
        invoiceData.products.forEach((product, index) => {
            previewHtml += `
        <tr>
            <td>${index + 1}</td>
            <td>${product.name}</td>
            <td>${product.quantity}</td>
            <td>$${product.unit_price}</td>
            <td>${product.tax}</td>
            <td>$${product.total.toFixed(2)}</td>
        </tr>
    `;
        });

        $('#preview_invoice_table_body').html(previewHtml);
        $('#preview_total_amount').text("$" + invoiceData.subtotal.toFixed(2));
        $('#preview_discount_amount').text(invoiceData.discount);
        $('#preview_tax_amount').text('$' + invoiceData.tax);
        $('#preview_final_total').text('$' + invoiceData.final_total);
        $('#preview_shipping_total').text('$ ' + (parseFloat(invoiceData.shipping_charges) || 0).toFixed(2));
        $('#preview_total_quantity').text(invoiceData.total_quantity);

        // Show the modal
        $('#invoicePreviewModal').modal('show');
    }

    // Handle the confirm button click in the preview modal
    $('#confirm_invoice_submit').click(function () {
        $('button#submit-sell').prop('disabled', true);
        $('button#save-and-print').prop('disabled', true);
        $('#invoicePreviewModal').modal('hide');
        window.onbeforeunload = null;

        const formData = new FormData(sell_form[0]);
        const formDataJson = {};

        for (let [key, value] of formData.entries()) {
            if (key.includes('[')) {
                const keys = key.split(/\[|\]/).filter(k => k !== '');
                let temp = formDataJson;

                keys.forEach((k, i) => {
                    if (i === keys.length - 1) {
                        temp[k] = value;
                    } else {
                        temp[k] = temp[k] || {};
                        temp = temp[k];
                    }
                });
            } else {
                formDataJson[key] = value;
            }
        }

        $.ajax({
            url: sell_form.attr('action'),
            method: sell_form.attr('method'),
            contentType: 'application/json',
            data: JSON.stringify(formDataJson),
            success: function (response) {
                if (response.redirect) {
                    $('button#submit-sell').prop('disabled', true);
                    $('button#save-and-print').prop('disabled', true);
                    toastr.success(response.msg);
                    window.location.href = response.redirect;
                } else {
                    toastr.success(response.msg);
                    console.log('Success:', response.status);
                }
                $('button#submit-sell').prop('disabled', true);
                $('button#save-and-print').prop('disabled', true);
            },
            error: function (error) {
                console.error('Form submission failed:', error);
                $('button#submit-sell').prop('disabled', false);
                $('button#save-and-print').prop('disabled', false);
            }
        });
    });

    //REPAIR MODULE:check if repair module field is present send data to filter product
    var is_enabled_stock = null;
    if ($('#is_enabled_stock').length) {
        is_enabled_stock = $('#is_enabled_stock').val();
    }

    var device_model_id = null;
    if ($('#repair_model_id').length) {
        device_model_id = $('#repair_model_id').val();
    }

    //Show product list.
    get_product_suggestion_list(
        global_p_category_id,
        global_brand_id,
        $('input#location_id').val(),
        null,
        is_enabled_stock,
        device_model_id
    );

    $('select#select_location_id').on('change', function (e) {
        $('input#suggestion_page').val(1);
        var location_id = $('input#location_id').val();
        if (location_id != '' || location_id != undefined) {
            get_product_suggestion_list(
                global_p_category_id,
                global_brand_id,
                $('input#location_id').val(),
                null
            );
        }
        get_featured_products();
    });

    // on click sub category in category drawer
    $('.product_category').on('click', function (e) {
        global_p_category_id = $(this).data('value');
        $('input#suggestion_page').val(1);
        var location_id = $('input#location_id').val();
        if (location_id != '' || location_id != undefined) {
            get_product_suggestion_list(
                global_p_category_id,
                global_brand_id,
                $('input#location_id').val(),
                null
            );
        }
        get_featured_products();
        $('.overlay-category').trigger('click');
    });

    //  function for show sub category
    $('.main-category').on('click', function () {
        global_p_category_id = $(this).data('value');
        parent = $(this).data('parent');

        if (parent == 0) {
            get_product_suggestion_list(
                global_p_category_id,
                global_brand_id,
                $('input#location_id').val(),
                null
            );
            get_featured_products();
            $('.overlay-category').trigger('click');
        } else {
            var main_category = $(this).data('value');

            $('.main-category-div').hide();
            $('.' + main_category).fadeIn();
            $('.category_heading').text('Sub Category ' + $(this).data('name'));
            $('.category-back').fadeIn();
        }
    });

    // function for back button in category
    $('.category-back').on('click', function () {
        $('.main-category-div').fadeIn();
        $('.main-category-all').fadeIn();
        $('.all-sub-category').hide();
        $('.category-back').hide();
        $('.category_heading').text('Category');
    });

    // on click brand in brand drawer
    $('.product_brand').on('click', function (e) {
        global_brand_id = $(this).data('value');
        $('input#suggestion_page').val(1);
        var location_id = $('input#location_id').val();

        if (location_id != '' || location_id != undefined) {
            get_product_suggestion_list(
                global_p_category_id,
                global_brand_id,
                $('input#location_id').val(),
                null
            );
        }
        get_featured_products();
        $('.overlay-brand').trigger('click');
    });

    // close side bar

    $('.close-side-bar-category').on('click', function () {
        $('.overlay-category').trigger('click');
    });

    $('.close-side-bar-brand').on('click', function () {
        $('.overlay-brand').trigger('click');
    });

    $(document).on('click', 'div.product_box', function () {
        //Check if location is not set then show error message.
        if ($('input#location_id').val() == '') {
            toastr.warning(LANG.select_location);
        } else {
            pos_product_row($(this).data('variation_id'));
        }
    });

    $(document).on('shown.bs.modal', '.row_description_modal', function () {
        $(this).find('textarea').first().focus();
    });

    //Press enter on search product to jump into last quantty and vice-versa
    $('#search_product').keydown(function (e) {
        var key = e.which;
        if (key == 9) {
            // the tab key code
            e.preventDefault();
            if ($('#pos_table tbody tr').length > 0) {
                $('#pos_table tbody tr:last').find('input.pos_quantity').focus().select();
            }
        }
    });
    $('#pos_table').on('keypress', 'input.pos_quantity', function (e) {
        var key = e.which;
        if (key == 13) {
            // the enter key code
            $('#search_product').focus();
        }
    });

    $('#exchange_rate').change(function () {
        var curr_exchange_rate = 1;
        if ($(this).val()) {
            curr_exchange_rate = __read_number($(this));
        }
        var total_payable = __read_number($('input#final_total_input'));
        var shown_total = total_payable * curr_exchange_rate;
        $('span#total_payable').text(__currency_trans_from_en(shown_total, false));
    });

    $('select#price_group').change(function () {
        $('input#hidden_price_group').val($(this).val());
    });

    //Quick add product
    $(document).on('click', 'button.pos_add_quick_product', function () {
        var url = $(this).data('href');
        var container = $(this).data('container');
        $.ajax({
            url: url + '?product_for=pos',
            dataType: 'html',
            success: function (result) {
                $(container).html(result).modal('show');
                $('.os_exp_date').datepicker({
                    autoclose: true,
                    format: 'dd-mm-yyyy',
                    clearBtn: true,
                });
            },
        });
    });

    $(document).on('change', 'form#quick_add_product_form input#single_dpp', function () {
        var unit_price = __read_number($(this));
        $('table#quick_product_opening_stock_table tbody tr').each(function () {
            var input = $(this).find('input.unit_price');
            __write_number(input, unit_price);
            input.change();
        });
    });

    $(document).on('quickProductAdded', function (e) {
        //Check if location is not set then show error message.
        if ($('input#location_id').val() == '') {
            toastr.warning(LANG.select_location);
        } else {
            pos_product_row(e.variation.id);
        }
    });

    $('div.view_modal').on('show.bs.modal', function () {
        __currency_convert_recursively($(this));
    });

    $('table#pos_table').on('change', 'select.sub_unit', function () {
        var tr = $(this).closest('tr');
        var base_unit_selling_price = tr.find('input.hidden_base_unit_sell_price').val();

        var selected_option = $(this).find(':selected');

        var multiplier = parseFloat(selected_option.data('multiplier'));

        var allow_decimal = parseInt(selected_option.data('allow_decimal'));

        tr.find('input.base_unit_multiplier').val(multiplier);

        var unit_sp = base_unit_selling_price * multiplier;

        var sp_element = tr.find('input.pos_unit_price');
        __write_number(sp_element, unit_sp);

        sp_element.change();

        var qty_element = tr.find('input.pos_quantity');
        var base_max_avlbl = qty_element.data('qty_available');
        var error_msg_line = 'pos_max_qty_error';

        if (tr.find('select.lot_number').length > 0) {
            var lot_select = tr.find('select.lot_number');
            if (lot_select.val()) {
                base_max_avlbl = lot_select.find(':selected').data('qty_available');
                error_msg_line = 'lot_max_qty_error';
            }
        }

        qty_element.attr('data-decimal', allow_decimal);
        var abs_digit = true;
        if (allow_decimal) {
            abs_digit = false;
        }
        qty_element.rules('add', {
            abs_digit: abs_digit,
        });

        if (base_max_avlbl) {
            var max_avlbl = parseFloat(base_max_avlbl) / multiplier;
            var formated_max_avlbl = __number_f(max_avlbl);
            var unit_name = selected_option.data('unit_name');
            var max_err_msg = __translate(error_msg_line, {
                max_val: formated_max_avlbl,
                unit_name: unit_name,
            });
            qty_element.attr('data-rule-max-value', max_avlbl);
            qty_element.attr('data-msg-max-value', max_err_msg);
            qty_element.rules('add', {
                'max-value': max_avlbl,
                messages: {
                    'max-value': max_err_msg,
                },
            });
            qty_element.trigger('change');
        }
        adjustComboQty(tr);
    });

    //Confirmation before page load.
    window.onbeforeunload = function () {
        if ($('form#edit_pos_sell_form').length == 0) {
            if ($('table#pos_table tbody tr').length > 0) {
                return LANG.sure;
            } else {
                return null;
            }
        }
    };
    $(window).resize(function () {
        var win_height = $(window).height();
        div_height = __calculate_amount('percentage', 63, win_height);
        // $('div.pos_product_div').css('min-height', div_height + 'px');
        // $('div.pos_product_div').css('max-height', div_height + 'px');
    });

    //Used for weighing scale barcode
    $('#weighing_scale_modal').on('shown.bs.modal', function (e) {
        //Attach the scan event
        onScan.attachTo(document, {
            suffixKeyCodes: [13], // enter-key expected at the end of a scan
            reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
            onScan: function (sCode, iQty) {
                console.log('Scanned: ' + iQty + 'x ' + sCode);
                $('input#weighing_scale_barcode').val(sCode);
                $('button#weighing_scale_submit').trigger('click');
            },
            onScanError: function (oDebug) {
                console.log(oDebug);
            },
            minLength: 2,
            // onKeyDetect: function(iKeyCode){ // output all potentially relevant key events - great for debugging!
            //     console.log('Pressed: ' + iKeyCode);
            // }
        });

        $('input#weighing_scale_barcode').focus();
    });

    $('#weighing_scale_modal').on('hide.bs.modal', function (e) {
        //Detach from the document once modal is closed.
        onScan.detachFrom(document);
    });

    $('button#weighing_scale_submit').click(function () {
        var price_group = '';
        if ($('#price_group').length > 0) {
            price_group = $('#price_group').val();
        }

        if ($('#weighing_scale_barcode').val().length > 0) {
            pos_product_row(null, null, $('#weighing_scale_barcode').val());
            $('#weighing_scale_modal').modal('hide');
            $('input#weighing_scale_barcode').val('');
        } else {
            $('input#weighing_scale_barcode').focus();
        }
    });

    $('#show_featured_products').click(function () {
        if (!$('#featured_products_box').is(':visible')) {
            $('#featured_products_box').fadeIn();
        } else {
            $('#featured_products_box').fadeOut();
        }
    });
    validate_discount_field();
    set_payment_type_dropdown();
    if ($('#__is_mobile').length) {
        $('.pos_form_totals').css('margin-bottom', $('.pos-form-actions').height() - 30);
    }

    setInterval(function () {
        if ($('span.curr_datetime').length) {
            $('span.curr_datetime').html(__current_datetime());
        }
    }, 60000);

    set_search_fields();
});

function set_payment_type_dropdown() {
    var payment_settings = $('#location_id').data('default_payment_accounts');
    payment_settings = payment_settings ? payment_settings : [];
    enabled_payment_types = [];
    for (var key in payment_settings) {
        if (payment_settings[key] && payment_settings[key]['is_enabled']) {
            enabled_payment_types.push(key);
        }
    }
    if (enabled_payment_types.length) {
        $('.payment_types_dropdown > option').each(function () {
            //skip if advance
            if ($(this).val() && $(this).val() != 'advance') {
                if (enabled_payment_types.indexOf($(this).val()) != -1) {
                    $(this).removeClass('hide');
                } else {
                    $(this).addClass('hide');
                }
            }
        });
    }
}

function get_featured_products() {
    var location_id = $('#location_id').val();
    if (location_id && $('#featured_products_box').length > 0) {
        $.ajax({
            method: 'GET',
            url: '/sells/pos/get-featured-products/' + location_id,
            dataType: 'html',
            success: function (result) {
                if (result) {
                    $('#feature_product_div').removeClass('hide');
                    $('#featured_products_box').html(result);
                } else {
                    $('#feature_product_div').addClass('hide');
                    $('#featured_products_box').html('');
                }
            },
        });
    } else {
        $('#feature_product_div').addClass('hide');
        $('#featured_products_box').html('');
    }
}

function get_product_suggestion_list(
    category_id,
    brand_id,
    location_id,
    url = null,
    is_enabled_stock = null,
    repair_model_id = null
) {
    if ($('div#product_list_body').length == 0) {
        return false;
    }

    if (url == null) {
        url = '/sells/pos/get-product-suggestion';
    }
    $('#suggestion_page_loader').fadeIn(700);
    var page = $('input#suggestion_page').val();
    if (page == 1) {
        $('div#product_list_body').html('');
    }
    if ($('div#product_list_body').find('input#no_products_found').length > 0) {
        $('#suggestion_page_loader').fadeOut(700);
        return false;
    }
    $.ajax({
        method: 'GET',
        url: url,
        data: {
            category_id: category_id,
            brand_id: brand_id,
            location_id: location_id,
            page: page,
            is_enabled_stock: is_enabled_stock,
            repair_model_id: repair_model_id,
        },
        dataType: 'html',
        success: function (result) {
            $('div#product_list_body').append(result);
            $('#suggestion_page_loader').fadeOut(700);
        },
    });
}

//Get recent transactions
function get_recent_transactions(status, element_obj) {
    if (element_obj.length == 0) {
        return false;
    }
    var transaction_sub_type = $('#transaction_sub_type').val();
    $.ajax({
        method: 'GET',
        url: '/sells/pos/get-recent-transactions',
        data: { status: status, transaction_sub_type: transaction_sub_type },
        dataType: 'html',
        success: function (result) {
            element_obj.html(result);
            __currency_convert_recursively(element_obj);
        },
    });
}

//variation_id is null when weighing_scale_barcode is used.
function pos_product_row(
    variation_id = null,
    purchase_line_id = null,
    weighing_scale_barcode = null,
    quantity = 1
) {
    //Get item addition method
    var item_addtn_method = 0;
    var add_via_ajax = true;
    var is_metrix = $('#toggle_switch').prop('checked') ? true : false;
    if (!is_metrix) {
        if (variation_id != null && $('#item_addition_method').length) {
            item_addtn_method = $('#item_addition_method').val();
        }

        if (item_addtn_method == 0) {
            add_via_ajax = true;
        } else {
            var is_added = false;

            //Search for variation id in each row of pos table
            // $('#pos_table tbody')
            //     .find('tr')
            //     .each(function () {
            //         var row_v_id = $(this).find('.row_variation_id').val();
            //         var enable_sr_no = $(this).find('.enable_sr_no').val();
            //         var modifiers_exist = false;
            //         if ($(this).find('input.modifiers_exist').length > 0) {
            //             modifiers_exist = true;
            //         }

            //         if (
            //             row_v_id == variation_id &&
            //             enable_sr_no !== '1' &&
            //             !modifiers_exist &&
            //             !is_added
            //         ) {
            //             add_via_ajax = false;
            //             is_added = true;

            //             //Increment product quantity
            //             qty_element = $(this).find('.pos_quantity');
            //             var qty = __read_number(qty_element);
            //             __write_number(qty_element, qty + 1);
            //             qty_element.change();

            //             round_row_to_iraqi_dinnar($(this));

            //             $('input#search_product').animate().focus().select();
            //         }
            //     });
        }

        if (add_via_ajax) {
            var product_row = $('input#product_row_count').val();
            var location_id = $('input#location_id').val();
            var customer_id = $('select#customer_id').val();
            var is_direct_sell = false;
            if (
                $('input[name="is_direct_sale"]').length > 0 &&
                $('input[name="is_direct_sale"]').val() == 1
            ) {
                is_direct_sell = true;
            }

            var disable_qty_alert = false;

            if ($('#disable_qty_alert').length) {
                disable_qty_alert = true;
            }

            var is_sales_order =
                $('#sale_type').length && $('#sale_type').val() == 'sales_order' ? true : false;

            var price_group = '';
            if ($('#price_group').length > 0) {
                price_group = parseInt($('#price_group').val());
            }

            //If default price group present
            if ($('#default_price_group').length > 0 && price_group === '') {
                price_group = $('#default_price_group').val();
            }

            //If types of service selected give more priority
            if (
                $('#types_of_service_price_group').length > 0 &&
                $('#types_of_service_price_group').val()
            ) {
                price_group = $('#types_of_service_price_group').val();
            }

            var is_draft = false;
            if (
                $('input#status') &&
                ($('input#status').val() == 'quotation' || $('input#status').val() == 'draft')
            ) {
                is_draft = true;
            }

            $.ajax({
                method: 'GET',
                url: '/sells/pos/get_product_row/' + variation_id + '/' + location_id,
                async: false,
                data: {
                    product_row: product_row,
                    customer_id: customer_id,
                    is_direct_sell: is_direct_sell,
                    price_group: price_group,
                    purchase_line_id: purchase_line_id,
                    weighing_scale_barcode: weighing_scale_barcode,
                    quantity: quantity,
                    is_sales_order: is_sales_order,
                    disable_qty_alert: disable_qty_alert,
                    is_draft: is_draft,
                },
                dataType: 'json',
                success: function (result) {
                    if (result.success) {
                        $('table#pos_table tbody')
                            .append(result.html_content)
                            .find('input.pos_quantity');
                        //increment row count
                        $('input#product_row_count').val(parseInt(product_row) + 1);
                        var this_row = $('table#pos_table tbody').find('tr').last();
                        pos_each_row(this_row);

                        var ml = this_row.attr('ml');
                        var ct = this_row.attr('ct');
                        var pricegroup = this_row.attr('pricegroup');
                        var recallPrice = this_row.attr('recallprice');
                        var locationTaxType = this_row.attr('locationTaxType');
                        var locationTaxTypeArray = JSON.parse(locationTaxType);

                        // update the tax value
                        var quantity = __read_number(this_row.find('input.pos_quantity'));
                        const unitPrice = recallPrice ? parseFloat(recallPrice) : this_row.find('input.pos_unit_price').val();
                        // console.log('clicked in this');
                        getProductTax(ml, ct, unitPrice, userTaxState, locationTaxTypeArray)
                            .then((res) => {
                                const { status, tax } = res;
                                const taxOnItem = status ? tax : 0;
                                // var taxOnItem = finalTax;
                                this_row.find('span.pos_taxation_total_text').text(taxOnItem);

                                pos_total_row();

                                const totalAmt = parseFloat(unitPrice) + taxOnItem;
                                this_row.find('input.pos_price_per_unit').val(parseFloat(unitPrice));
                                // changes
                                // this_row.find('input.pos_unit_price').val(totalAmt);
                                // this_row.find('input.pos_unit_price')[0].defaultValue = totalAmt;
                                this_row.find('input.pos_unit_price').val(parseFloat(unitPrice));
                                this_row.find('input.pos_unit_price')[0].defaultValue = parseFloat(unitPrice);

                                this_row.find('input.item_tax').val(parseFloat(taxOnItem));
                                this_row.find('input.item_tax')[0].defaultValue = parseFloat(taxOnItem);

                                this_row.find('input.pos_unit_price_inc_tax').val(totalAmt);
                                this_row.find('input.pos_unit_price_inc_tax')[0].defaultValue = totalAmt;

                                this_row.find('.old_pos_unit_price_tax').text(taxOnItem.toFixed(2));

                                var line_total = __read_number(this_row.find('input.pos_unit_price'));
                                __write_number(
                                    this_row.find('input.pos_line_total'),
                                    line_total + taxOnItem
                                );
                                this_row.find('span.pos_line_total_text').text(`$ ${line_total + taxOnItem}`);

                                this_row.find('input.pos_taxation_total').val(parseFloat(taxOnItem));
                                this_row.find('input.pos_taxation_total')[0].defaultValue = parseFloat(taxOnItem);

                                pos_total_row();

                                if (__getUnitMultiplier(this_row) > 1) {
                                    this_row.find('select.sub_unit').trigger('change');
                                }

                                if (result.enable_sr_no == '1') {
                                    var new_row = $('table#pos_table tbody').find('tr').last();
                                    new_row.find('.row_edit_product_price_model').modal('show');
                                }

                                round_row_to_iraqi_dinnar(this_row);
                                __currency_convert_recursively(this_row);

                                $('input#search_product').animate().focus();

                                //Used in restaurant module
                                if (result.html_modifier) {
                                    $('table#pos_table tbody')
                                        .find('tr')
                                        .last()
                                        .find('td:first')
                                        .append(result.html_modifier);
                                }

                                //scroll bottom of items list

                                $('.pos_product_div').animate(
                                    { scrollTop: $('.pos_product_div').prop('scrollHeight') },
                                    1000
                                );
                            })
                            .catch((err) => console.error(err));
                    } else {
                        toastr.error(result.msg);
                        $('input#search_product').animate().focus();
                    }
                },
            });
        }
    }
}


//metix product section


$(document).on('click', '#save_button_metrix', function () {
    var variationIds = [];
    var quantities = [];
    $('table.bg-gray tbody tr[data-variation-id]').each(function () {
        var variationId = $(this).data('variation-id');
        var quantity = parseInt($(this).find('.quantity-input').val()) || 0;
        if (quantity > 0) {
            variationIds.push(variationId);
            quantities.push(quantity);
        }
    });
    if (variationIds.length > 0) {
        pos_Matrix_row(variationIds.join(','), quantities.join(','));
    }
});
$('#combine_button').on('click', function () {
    var variationMap = {};

    $('#pos_table tbody tr').each(function () {
        var $row = $(this);
        var variation_id = $row.find('.row_variation_id').val();
        var qty_element = $row.find('.pos_quantity');
        var quantity = __read_number(qty_element);

        if (variationMap[variation_id]) {
            // Add to the existing quantity in the original row
            var existing_row = variationMap[variation_id];
            var existing_qty_element = existing_row.find('.pos_quantity');
            var existing_qty = __read_number(existing_qty_element);
            __write_number(existing_qty_element, existing_qty + quantity);
            existing_qty_element.change();

            // Remove the duplicate row
            $row.remove();
        } else {
            // Store the first occurrence of this variation_id
            variationMap[variation_id] = $row;
        }
        pos_total_row();
    });
    // Optionally apply rounding and refocus search
    $.each(variationMap, function (_, $row) {
        round_row_to_iraqi_dinnar($row);
    });

    $('#search_product').focus().select();
});
function combineRows() {
    var variationMap = {};
    $('#pos_table tbody tr').each(function () {
        var $row = $(this);
        var variation_id = $row.find('.row_variation_id').val();
        var qty_element = $row.find('.pos_quantity');
        var quantity = __read_number(qty_element);

        if (variationMap[variation_id]) {
            // Add to the existing quantity in the original row
            var existing_row = variationMap[variation_id];
            var existing_qty_element = existing_row.find('.pos_quantity');
            var existing_qty = __read_number(existing_qty_element);
            __write_number(existing_qty_element, existing_qty + quantity);
            existing_qty_element.change();

            // Remove the duplicate row
            $row.remove();
        } else {
            // Store the first occurrence of this variation_id
            variationMap[variation_id] = $row;
        }
        pos_total_row();
    })
    $.each(variationMap, function (_, $row) {
        round_row_to_iraqi_dinnar($row);
    });
}


function pos_Matrix_row(variation_ids, quantities) {
    if (typeof variation_ids === 'string') {
        variation_ids = variation_ids.split(',');
    }
    if (typeof quantities === 'string') {
        quantities = quantities.split(',').map(q => parseFloat(q));
    }

    var new_variation_ids = [];
    var new_quantities = [];

    $.each(variation_ids, function (index, variation_id) {
        var quantity = quantities[index];
        var is_added = false;

        // $('#pos_table tbody').find('tr').each(function () {
        //     var row_v_id = $(this).find('.row_variation_id').val();
        //     var enable_sr_no = $(this).find('.enable_sr_no').val();
        //     var modifiers_exist = $(this).find('input.modifiers_exist').length > 0;

        //     if (
        //         row_v_id == variation_id &&
        //         enable_sr_no !== '1' &&
        //         !modifiers_exist &&
        //         !is_added
        //     ) {
        //         is_added = true;
        //         var qty_element = $(this).find('.pos_quantity');
        //         var existing_qty = __read_number(qty_element);
        //         __write_number(qty_element, existing_qty + quantity);
        //         qty_element.change();

        //         round_row_to_iraqi_dinnar($(this));
        //         $('input#search_product').animate().focus().select();
        //     }
        // });

        if (!is_added) {
            new_variation_ids.push(variation_id);
            new_quantities.push(quantity);
        }
    });

    // Don't proceed if no new products to add
    if (new_variation_ids.length === 0) {
        $('.modal').modal('hide');
        return;
    }

    var product_row = $('input#product_row_count').val();
    var location_id = $('input#location_id').val();
    var customer_id = $('select#customer_id').val();
    var is_direct_sell = $('input[name="is_direct_sale"]').val() == 1;
    var price_group = $('#price_group').val() || $('#default_price_group').val();
    var is_draft = $('input#status').val() == 'quotation' || $('input#status').val() == 'draft';

    $.ajax({
        method: 'GET',
        url: '/sells/pos/get_matrix_row/' + new_variation_ids.join(',') + '/' + location_id,
        data: {
            product_row: product_row,
            customer_id: customer_id,
            is_direct_sell: is_direct_sell,
            price_group: price_group,
            quantities: new_quantities.join(','), // <--- FIXED
            is_sales_order: false,
            disable_qty_alert: false,
            is_draft: is_draft
        },

        dataType: 'json',
        success: function (result) {
            if (result.success) {
                // If the response is in the new format with multiple rows:
                if (result.rows && result.rows.length > 0) {
                    $.each(result.rows, function (index, rowData) {
                        if (rowData.success) {
                            // Append the HTML for the row
                            $('table#pos_table tbody').append(rowData.html_content);

                            // Increment row count for each new row appended
                            $('input#product_row_count').val(
                                parseInt($('input#product_row_count').val()) + 1
                            );

                            // Get the last row (just appended) and process it
                            var this_row = $('table#pos_table tbody').find('tr').last();
                            pos_each_row(this_row);

                            // Retrieve any required attributes from the row
                            var ml = this_row.attr('ml');
                            var ct = this_row.attr('ct');
                            var pricegroup = this_row.attr('pricegroup');
                            var locationTaxType = this_row.attr('locationTaxType');
                            // Parse the JSON string (for example: "[null]" or an array)
                            var locationTaxTypeArray = JSON.parse(locationTaxType);

                            // Get the quantity and unit price
                            var quantity = __read_number(this_row.find('input.pos_quantity'));
                            const recallPrice = this_row.attr('recallprice');
                            const unitPrice = recallPrice ? parseFloat(recallPrice) : parseFloat(this_row.find('input.pos_unit_price').val());

                            getProductTax(ml, ct, unitPrice, userTaxState, locationTaxTypeArray)
                                .then((res) => {
                                    const { status, tax } = res;
                                    const taxPerUnit = status ? tax : 0;

                                    // Multiply tax by quantity
                                    const totalTax = taxPerUnit * quantity;

                                    // Update tax display for the row
                                    this_row.find('span.pos_taxation_total_text').text(`$ ${taxPerUnit.toFixed(2)}`);
                                    pos_total_row();

                                    // Update pricing information
                                    this_row.find('input.pos_price_per_unit').val(unitPrice);
                                    this_row.find('input.pos_unit_price').val(unitPrice);
                                    this_row.find('input.pos_unit_price')[0].defaultValue = unitPrice;
                                    this_row.find('input.item_tax').val(totalTax);
                                    this_row.find('input.item_tax')[0].defaultValue = totalTax;
                                    this_row.find('input.pos_unit_price_inc_tax').val(unitPrice + totalTax);
                                    this_row.find('input.pos_unit_price_inc_tax')[0].defaultValue = unitPrice + totalTax;
                                    this_row.find('.old_pos_unit_price_tax').text(totalTax.toFixed(2));


                                    // Update the line total by adding total tax
                                    var line_total = __read_number(this_row.find('input.pos_line_total'));
                                    __write_number(this_row.find('input.pos_line_total'), line_total + totalTax);
                                    this_row.find('span.pos_line_total_text').text(`$ ${line_total + totalTax}`);
                                    this_row.find('input.pos_taxation_total').val(totalTax);
                                    this_row.find('input.pos_taxation_total')[0].defaultValue = totalTax;
                                    pos_total_row();
                                })
                                .catch((err) => console.error(err));

                        }
                    });
                    $('input#search_product').animate().focus();
                } else {
                    // Fallback for older response format (a single row in html_content)
                    $('table#pos_table tbody')
                        .append(result.html_content)
                        .find('input.pos_quantity');
                    $('input#product_row_count').val(
                        parseInt($('input#product_row_count').val()) + 1
                    );
                    var this_row = $('table#pos_table tbody').find('tr').last();
                    pos_each_row(this_row);

                    var ml = this_row.attr('ml');
                    var ct = this_row.attr('ct');
                    var pricegroup = this_row.attr('pricegroup');
                    var locationTaxType = this_row.attr('locationTaxType');
                    var locationTaxTypeArray = JSON.parse(locationTaxType);
                    var quantity = __read_number(this_row.find('input.pos_quantity'));
                    const recallPrice = this_row.attr('recallprice');
                    const unitPrice = recallPrice ? parseFloat(recallPrice) : parseFloat(this_row.find('input.pos_unit_price').val());

                    getProductTax(ml, ct, unitPrice, userTaxState, locationTaxTypeArray)
                        .then((res) => {
                            const { status, tax } = res;
                            const taxOnItem = status ? tax : 0;
                            this_row.find('span.pos_taxation_total_text').text(taxOnItem);
                            pos_total_row();
                            const totalAmt = unitPrice + taxOnItem;
                            this_row.find('input.pos_price_per_unit').val(unitPrice);
                            this_row.find('input.pos_unit_price').val(unitPrice);
                            this_row.find('input.pos_unit_price')[0].defaultValue = unitPrice;
                            this_row.find('input.item_tax').val(taxOnItem);
                            this_row.find('input.item_tax')[0].defaultValue = taxOnItem;
                            this_row.find('input.pos_unit_price_inc_tax').val(totalAmt);
                            this_row.find('input.pos_unit_price_inc_tax')[0].defaultValue = totalAmt;
                            this_row.find('.old_pos_unit_price_tax').text(taxOnItem.toFixed(2));
                            var line_total = __read_number(this_row.find('input.pos_line_total'));
                            __write_number(this_row.find('input.pos_line_total'), line_total + taxOnItem);
                            this_row.find('span.pos_line_total_text').text(`$ ${line_total + taxOnItem}`);
                            this_row.find('input.pos_taxation_total').val(taxOnItem);
                            this_row.find('input.pos_taxation_total')[0].defaultValue = taxOnItem;
                            pos_total_row();

                            if (__getUnitMultiplier(this_row) > 1) {
                                this_row.find('select.sub_unit').trigger('change');
                            }

                            if (result.enable_sr_no == '1') {
                                this_row.find('.row_edit_product_price_model').modal('show');
                            }

                            round_row_to_iraqi_dinnar(this_row);
                            __currency_convert_recursively(this_row);
                            $('input#search_product').animate().focus();

                            if (result.html_modifier) {
                                this_row.find('td:first').append(result.html_modifier);
                            }

                            $('.pos_product_div').animate(
                                { scrollTop: $('.pos_product_div').prop('scrollHeight') },
                                1000
                            );
                        })
                        .catch((err) => console.error(err));
                }
                $('.modal').modal('hide');
                $('input#search_product').animate().focus();
            } else {
                toastr.error(result.msg);
                $('input#search_product').animate().focus();
            }
        },
    });
}




//Update values for each row
function pos_each_row(row_obj) {
    var unit_price = __read_number(row_obj.find('input.pos_unit_price'));

    var discounted_unit_price = calculate_discounted_unit_price(row_obj);
    var tax_rate = row_obj.find('select.tax_id').find(':selected').data('rate');

    var unit_price_inc_tax =
        discounted_unit_price + __calculate_amount('percentage', tax_rate, discounted_unit_price);
    __write_number(row_obj.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax);

    var discount = __read_number(row_obj.find('input.row_discount_amount'));

    if (discount > 0) {
        var qty = __read_number(row_obj.find('input.pos_quantity'));
        var line_total = qty * unit_price_inc_tax;
        __write_number(row_obj.find('input.pos_line_total'), line_total);
    }

    //var unit_price_inc_tax = __read_number(row_obj.find('input.pos_unit_price_inc_tax'));

    __write_number(row_obj.find('input.item_tax'), unit_price_inc_tax - discounted_unit_price);
}

function pos_total_row() {
    var total_quantity = 0;
    var price_total = get_subtotal();
    $('table#pos_table tbody tr').each(function () {
        total_quantity = total_quantity + __read_number($(this).find('input.pos_quantity'));
    });

    //updating shipping charges
    $('span#shipping_charges_amount').text(
        __currency_trans_from_en(__read_number($('input#shipping_charges_modal')), false)
    );

    $('span.total_quantity').each(function () {
        $(this).html((total_quantity));
    });

    //$('span.unit_price_total').html(unit_price_total);
    $('span.price_total').html(__currency_trans_from_en(price_total, false));
    calculate_billing_details(price_total);
}

function get_subtotal() {
    var price_total = 0;

    $('table#pos_table tbody tr').each(function () {
        price_total = price_total + __read_number($(this).find('input.pos_line_total'));
    });

    //Go through the modifier prices.
    $('input.modifiers_price').each(function () {
        var modifier_price = __read_number($(this));
        var modifier_quantity = $(this)
            .closest('.product_modifier')
            .find('.modifiers_quantity')
            .val();
        var modifier_subtotal = modifier_price * modifier_quantity;
        price_total = price_total + modifier_subtotal;
    });

    return price_total;
}

function calculate_billing_details(price_total) {
    var discount = pos_discount(price_total);
    if ($('#reward_point_enabled').length) {
        total_customer_reward = $('#rp_redeemed_amount').val();
        discount = parseFloat(discount) + parseFloat(total_customer_reward);

        if ($('input[name="is_direct_sale"]').length <= 0) {
            $('span#total_discount').text(__currency_trans_from_en(discount, false));
        }
    }

    var order_tax = pos_order_tax(price_total, discount);

    //Add shipping charges.
    var shipping_charges = __read_number($('input#shipping_charges'));

    var additional_expense = 0;
    //calculate additional expenses
    if ($('input#additional_expense_value_1').length > 0) {
        additional_expense += __read_number($('input#additional_expense_value_1'));
    }
    if ($('input#additional_expense_value_2').length > 0) {
        additional_expense += __read_number($('input#additional_expense_value_2'));
    }
    if ($('input#additional_expense_value_3').length > 0) {
        additional_expense += __read_number($('input#additional_expense_value_3'));
    }
    if ($('input#additional_expense_value_4').length > 0) {
        additional_expense += __read_number($('input#additional_expense_value_4'));
    }

    //Add packaging charge
    var packing_charge = 0;
    if ($('#types_of_service_id').length > 0 && $('#types_of_service_id').val()) {
        packing_charge = __calculate_amount(
            $('#packing_charge_type').val(),
            __read_number($('input#packing_charge')),
            price_total
        );

        $('#packing_charge_text').text(__currency_trans_from_en(packing_charge, false));
    }

    var total_payable =
        price_total + order_tax - discount + shipping_charges + packing_charge + additional_expense;

    var rounding_multiple = $('#amount_rounding_method').val()
        ? parseFloat($('#amount_rounding_method').val())
        : 0;
    var round_off_data = __round(total_payable, rounding_multiple);
    var total_payable_rounded = round_off_data.number;

    var round_off_amount = round_off_data.diff;
    if (round_off_amount != 0) {
        $('span#round_off_text').text(__currency_trans_from_en(round_off_amount, false));
    } else {
        $('span#round_off_text').text(0);
    }
    $('input#round_off_amount').val(round_off_amount);

    __write_number($('input#final_total_input'), total_payable_rounded);
    var curr_exchange_rate = 1;
    if ($('#exchange_rate').length > 0 && $('#exchange_rate').val()) {
        curr_exchange_rate = __read_number($('#exchange_rate'));
    }
    var shown_total = total_payable_rounded * curr_exchange_rate;
    $('span#total_payable').text(__currency_trans_from_en(shown_total, false));

    $('span.total_payable_span').text(__currency_trans_from_en(total_payable_rounded, true));

    //Check if edit form then don't update price.
    if ($('form#edit_pos_sell_form').length == 0 && $('form#edit_sell_form').length == 0) {
        // __write_number($('.payment-amount').first(), total_payable_rounded);
    }

    $(document).trigger('invoice_total_calculated');

    calculate_balance_due();
}

function pos_discount(total_amount) {
    var calculation_type = $('#discount_type').val();
    var calculation_amount = __read_number($('#discount_amount'));

    var discount = __calculate_amount(calculation_type, calculation_amount, total_amount);

    $('span#total_discount').text(__currency_trans_from_en(discount, false));

    return discount;
}

function pos_order_tax(price_total, discount) {
    var tax_rate_id = $('#tax_rate_id').val();
    var calculation_type = 'percentage';
    var calculation_amount = __read_number($('#tax_calculation_amount'));
    var total_amount = price_total - discount;

    if (tax_rate_id) {
        var order_tax = __calculate_amount(calculation_type, calculation_amount, total_amount);
    } else {
        var order_tax = 0;
    }

    $('span#order_tax').text(__currency_trans_from_en(order_tax, false));

    return order_tax;
}

function calculate_balance_due() {
    var total_payable = __read_number($('#final_total_input'));
    var total_paying = 0;
    $('#payment_rows_div')
        .find('.payment-amount')
        .each(function () {
            if (parseFloat($(this).val())) {
                total_paying += __read_number($(this));
            }
        });
    var bal_due = total_payable - total_paying;
    var change_return = 0;

    //change_return
    if (bal_due < 0 || Math.abs(bal_due) < 0.05) {
        __write_number($('input#change_return'), bal_due * -1);
        $('span.change_return_span').text(__currency_trans_from_en(bal_due * -1, true));
        change_return = bal_due * -1;
        bal_due = 0;
    } else {
        __write_number($('input#change_return'), 0);
        $('span.change_return_span').text(__currency_trans_from_en(0, true));
        change_return = 0;
    }

    if (change_return !== 0) {
        $('#change_return_payment_data').removeClass('hide');
    } else {
        $('#change_return_payment_data').addClass('hide');
    }

    __write_number($('input#total_paying_input'), total_paying);
    $('span.total_paying').text(__currency_trans_from_en(total_paying, true));

    __write_number($('input#in_balance_due'), bal_due);
    $('span.balance_due').text(__currency_trans_from_en(bal_due, true));

    __highlight(bal_due * -1, $('span.balance_due'));
    __highlight(change_return * -1, $('span.change_return_span'));
}

function isValidPosForm() {
    flag = true;
    $('span.error').remove();

    if ($('select#customer_id').val() == null) {
        flag = false;
        error = '<span class="error">' + LANG.required + '</span>';
        $(error).insertAfter($('select#customer_id').parent('div'));
    }

    if ($('tr.product_row').length == 0) {
        flag = false;
        error = '<span class="error">' + LANG.no_products + '</span>';
        $(error).insertAfter($('input#search_product').parent('div'));
    }

    return flag;
}

function reset_pos_form() {
    //If on edit page then redirect to Add POS page
    if ($('form#edit_pos_sell_form').length > 0) {
        setTimeout(function () {
            window.location = $('input#pos_redirect_url').val();
        }, 4000);
        return true;
    }

    //reset all repair defects tags
    if ($('#repair_defects').length > 0) {
        tagify_repair_defects.removeAllTags();
    }

    if (pos_form_obj[0]) {
        pos_form_obj[0].reset();
    }
    if (sell_form[0]) {
        sell_form[0].reset();
    }
    set_default_customer();
    set_location();

    $('tr.product_row').remove();
    $(
        'span.total_quantity, span.price_total, span#total_discount, span#order_tax, span#total_payable, span#shipping_charges_amount'
    ).text(0);
    $('span.total_payable_span', 'span.total_paying', 'span.balance_due').text(0);

    $('#modal_payment')
        .find('.remove_payment_row')
        .each(function () {
            $(this).closest('.payment_row').remove();
        });

    if ($('#is_credit_sale').length) {
        $('#is_credit_sale').val(0);
    }

    //Reset discount
    __write_number($('input#discount_amount'), $('input#discount_amount').data('default'));
    $('input#discount_type').val($('input#discount_type').data('default'));

    //Reset tax rate
    $('input#tax_rate_id').val($('input#tax_rate_id').data('default'));
    __write_number(
        $('input#tax_calculation_amount'),
        $('input#tax_calculation_amount').data('default')
    );

    $('select.payment_types_dropdown').val('cash').trigger('change');
    $('#price_group').trigger('change');

    //Reset shipping
    __write_number($('input#shipping_charges'), $('input#shipping_charges').data('default'));
    $('input#shipping_details').val($('input#shipping_details').data('default'));
    $('input#shipping_address, input#shipping_status, input#delivered_to').val('');
    if ($('input#is_recurring').length > 0) {
        $('input#is_recurring').iCheck('update');
    }
    if ($('input#is_kitchen_order').length > 0) {
        $('input#is_kitchen_order').iCheck('update');
    }
    if ($('#invoice_layout_id').length > 0) {
        $('#invoice_layout_id').trigger('change');
    }
    $('span#round_off_text').text(0);

    //repair module extra  fields reset
    if ($('#repair_device_id').length > 0) {
        $('#repair_device_id').val('').trigger('change');
    }

    //Status is hidden in sales order
    if ($('#status').length > 0 && $('#status').is(':visible')) {
        $('#status').val('').trigger('change');
    }
    if ($('#transaction_date').length > 0) {
        $('#transaction_date').data('DateTimePicker').date(moment());
    }
    if ($('.paid_on').length > 0) {
        $('.paid_on').data('DateTimePicker').date(moment());
    }
    if ($('#commission_agent').length > 0) {
        $('#commission_agent').val('').trigger('change');
    }

    //reset contact due
    $('.contact_due_text').find('span').text('');
    $('.contact_due_text').addClass('hide');

    $(document).trigger('sell_form_reset');
}

function set_default_customer() {
    var default_customer_id = $('#default_customer_id').val();
    var default_customer_name = $('#default_customer_name').val();
    var default_customer_balance = $('#default_customer_balance').val();
    var default_customer_address = $('#default_customer_address').val();
    var exists = default_customer_id
        ? $('select#customer_id option[value=' + default_customer_id + ']').length
        : 0;
    if (exists == 0 && default_customer_id) {
        $('select#customer_id').append(
            $('<option>', { value: default_customer_id, text: default_customer_name })
        );
    }
    $('#advance_balance_text').text(__currency_trans_from_en(default_customer_balance), true);
    $('#advance_balance').val(default_customer_balance);
    $('#shipping_address_modal').val(default_customer_address);
    if (default_customer_address) {
        $('#shipping_address').val(default_customer_address);
    }
    $('select#customer_id').val(default_customer_id).trigger('change');

    if ($('#default_selling_price_group').length) {
        $('#price_group').val($('#default_selling_price_group').val());
        $('#price_group').change();
    }

    //initialize tags input (tagify)
    if ($('textarea#repair_defects').length > 0 && !customer_set) {
        let suggestions = [];
        if (
            $('input#pos_repair_defects_suggestion').length > 0 &&
            $('input#pos_repair_defects_suggestion').val().length > 2
        ) {
            suggestions = JSON.parse($('input#pos_repair_defects_suggestion').val());
        }
        let repair_defects = document.querySelector('textarea#repair_defects');
        tagify_repair_defects = new Tagify(repair_defects, {
            whitelist: suggestions,
            maxTags: 100,
            dropdown: {
                maxItems: 100, // <- mixumum allowed rendered suggestions
                classname: 'tags-look', // <- custom classname for this dropdown, so it could be targeted
                enabled: 0, // <- show suggestions on focus
                closeOnSelect: false, // <- do not hide the suggestions dropdown once an item has been selected
            },
        });
    }

    customer_set = true;
}

//Set the location and initialize printer
function set_location() {
    if ($('select#select_location_id').length == 1) {
        $('input#location_id').val($('select#select_location_id').val());
        $('input#location_id').data(
            'receipt_printer_type',
            $('select#select_location_id').find(':selected').data('receipt_printer_type')
        );
        $('input#location_id').data(
            'default_payment_accounts',
            $('select#select_location_id').find(':selected').data('default_payment_accounts')
        );

        $('input#location_id').attr(
            'data-default_price_group',
            $('select#select_location_id').find(':selected').data('default_price_group')
        );
    }

    if ($('input#location_id').val()) {
        $('input#search_product').prop('disabled', false).focus();
    } else {
        $('input#search_product').prop('disabled', true);
    }

    initialize_printer();
}

function initialize_printer() {
    if ($('input#location_id').data('receipt_printer_type') == 'printer') {
        initializeSocket();
    }
}

$('body').on('click', 'label', function (e) {
    var field_id = $(this).attr('for');
    if (field_id) {
        if ($('#' + field_id).hasClass('select2')) {
            $('#' + field_id).select2('open');
            return false;
        }
    }
});

$('body').on('focus', 'select', function (e) {
    var field_id = $(this).attr('id');
    if (field_id) {
        if ($('#' + field_id).hasClass('select2')) {
            $('#' + field_id).select2('open');
            return false;
        }
    }
});

function round_row_to_iraqi_dinnar(row) {
    if (iraqi_selling_price_adjustment) {
        var element = row.find('input.pos_unit_price_inc_tax');
        var unit_price = round_to_iraqi_dinnar(__read_number(element));
        __write_number(element, unit_price);
        element.change();
    }
}

function pos_print(receipt) {
    //If printer type then connect with websocket
    if (receipt.print_type == 'printer') {
        var content = receipt;
        content.type = 'print-receipt';

        //Check if ready or not, then print.
        if (socket != null && socket.readyState == 1) {
            socket.send(JSON.stringify(content));
        } else {
            initializeSocket();
            setTimeout(function () {
                socket.send(JSON.stringify(content));
            }, 700);
        }
    } else if (receipt.html_content != '') {
        var title = document.title;
        if (typeof receipt.print_title != 'undefined') {
            document.title = receipt.print_title;
        }

        //If printer type browser then print content
        $('#receipt_section').html(receipt.html_content);
        __currency_convert_recursively($('#receipt_section'));
        __print_receipt('receipt_section');

        setTimeout(function () {
            document.title = title;
        }, 1200);
    }
}

function calculate_discounted_unit_price(row) {
    var this_unit_price = __read_number(row.find('input.pos_unit_price'));
    var row_discounted_unit_price = this_unit_price;
    var row_discount_type = row.find('select.row_discount_type').val();
    var row_discount_amount = __read_number(row.find('input.row_discount_amount'));
    if (row_discount_amount) {
        if (row_discount_type == 'fixed') {
            row_discounted_unit_price = this_unit_price - row_discount_amount;
        } else {
            row_discounted_unit_price = __substract_percent(this_unit_price, row_discount_amount);
        }
    }

    return row_discounted_unit_price;
}

function get_unit_price_from_discounted_unit_price(row, discounted_unit_price) {
    var this_unit_price = discounted_unit_price;
    var row_discount_type = row.find('select.row_discount_type').val();
    var row_discount_amount = __read_number(row.find('input.row_discount_amount'));
    if (row_discount_amount) {
        if (row_discount_type == 'fixed') {
            this_unit_price = discounted_unit_price + row_discount_amount;
        } else {
            this_unit_price = __get_principle(discounted_unit_price, row_discount_amount, true);
        }
    }

    return this_unit_price;
}

//Update quantity if line subtotal changes
$('table#pos_table tbody').on('change', 'input.pos_line_total', function () {
    var subtotal = __read_number($(this));
    var tr = $(this).parents('tr');
    var quantity_element = tr.find('input.pos_quantity');
    var unit_price_inc_tax = __read_number(tr.find('input.pos_unit_price_inc_tax'));
    var quantity = subtotal / unit_price_inc_tax;
    __write_number(quantity_element, quantity);

    __write_number($(this), subtotal, false);

    if (sell_form_validator) {
        sell_form_validator.element(quantity_element);
    }
    if (pos_form_validator) {
        pos_form_validator.element(quantity_element);
    }
    tr.find('span.pos_line_total_text').text(__currency_trans_from_en(`$ ${subtotal}`, true));

    pos_total_row();
});

$('div#product_list_body').on('scroll', function () {
    if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
        var page = parseInt($('#suggestion_page').val());
        page += 1;
        $('#suggestion_page').val(page);
        var location_id = $('input#location_id').val();
        var category_id = global_p_category_id;
        var brand_id = global_brand_id;

        var is_enabled_stock = null;
        if ($('#is_enabled_stock').length) {
            is_enabled_stock = $('#is_enabled_stock').val();
        }

        var device_model_id = null;
        if ($('#repair_model_id').length) {
            device_model_id = $('#repair_model_id').val();
        }

        get_product_suggestion_list(
            category_id,
            brand_id,
            location_id,
            null,
            is_enabled_stock,
            device_model_id
        );
    }
});

$(document).on('ifChecked', '#is_recurring', function () {
    $('#recurringInvoiceModal').modal('show');
});

$(document).on('shown.bs.modal', '#recurringInvoiceModal', function () {
    $('input#recur_interval').focus();
});

$(document).on('click', '#select_all_service_staff', function () {
    var val = $('#res_waiter_id').val();
    $('#pos_table tbody')
        .find('select.order_line_service_staff')
        .each(function () {
            $(this).val(val).change();
        });
});

$(document).on('click', '.print-invoice-link', function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('href') + '?check_location=true',
        dataType: 'json',
        success: function (result) {
            if (result.success == 1) {
                //Check if enabled or not
                if (result.receipt.is_enabled) {
                    pos_print(result.receipt);
                }
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

function getCustomerRewardPoints() {
    if ($('#reward_point_enabled').length <= 0) {
        return false;
    }
    var is_edit =
        $('form#edit_sell_form').length || $('form#edit_pos_sell_form').length ? true : false;
    if (is_edit && !customer_set) {
        return false;
    }

    var customer_id = $('#customer_id').val();

    $.ajax({
        method: 'POST',
        url: '/sells/pos/get-reward-details',
        data: {
            customer_id: customer_id,
        },
        dataType: 'json',
        success: function (result) {
            $('#available_rp').text(result.points);
            $('#rp_redeemed_modal').data('max_points', result.points);
            updateRedeemedAmount();
            $('#rp_redeemed_amount').change();
        },
    });
}

function updateRedeemedAmount(argument) {
    var points = $('#rp_redeemed_modal').val().trim();
    points = points == '' ? 0 : parseInt(points);
    var amount_per_unit_point = parseFloat($('#rp_redeemed_modal').data('amount_per_unit_point'));
    var redeemed_amount = points * amount_per_unit_point;
    $('#rp_redeemed_amount_text').text(__currency_trans_from_en(redeemed_amount, true));
    $('#rp_redeemed').val(points);
    $('#rp_redeemed_amount').val(redeemed_amount);
}

$(document).on('change', 'select#customer_id', function () {
    var default_customer_id = $('#default_customer_id').val();
    if ($(this).val() == default_customer_id) {
        //Disable reward points for walkin customers
        if ($('#rp_redeemed_modal').length) {
            $('#rp_redeemed_modal').val('');
            $('#rp_redeemed_modal').change();
            $('#rp_redeemed_modal').attr('disabled', true);
            $('#available_rp').text('');
            updateRedeemedAmount();
            pos_total_row();
        }
    } else {
        if ($('#rp_redeemed_modal').length) {
            $('#rp_redeemed_modal').removeAttr('disabled');
        }
        getCustomerRewardPoints();
    }

    get_sales_orders();
});

$(document).on('change', '#rp_redeemed_modal', function () {
    var points = $(this).val().trim();
    points = points == '' ? 0 : parseInt(points);
    var amount_per_unit_point = parseFloat($(this).data('amount_per_unit_point'));
    var redeemed_amount = points * amount_per_unit_point;
    $('#rp_redeemed_amount_text').text(__currency_trans_from_en(redeemed_amount, true));
    var reward_validation = isValidatRewardPoint();
    if (!reward_validation['is_valid']) {
        toastr.error(reward_validation['msg']);
        $('#rp_redeemed_modal').select();
    }
});

$(document).on('change', '.direct_sell_rp_input', function () {
    updateRedeemedAmount();
    pos_total_row();
});

function isValidatRewardPoint() {
    var element = $('#rp_redeemed_modal');
    if (!element.length) {
        return { is_valid: true, msg: '' };
    }
    var val = element.val();
    var points = (val != null ? String(val) : '').trim();
    points = points == '' ? 0 : parseInt(points, 10);

    var max_points = parseInt(element.data('max_points'), 10) || 0;
    var is_valid = true;
    var msg = '';

    if (points == 0) {
        return {
            is_valid: is_valid,
            msg: msg,
        };
    }

    var rp_name = ($('input#rp_name').val() || '').toString();
    if (points > max_points) {
        is_valid = false;
        msg = __translate('max_rp_reached_error', { max_points: max_points, rp_name: rp_name });
    }

    var min_order_total_required = parseFloat(element.data('min_order_total'));

    var order_total = __read_number($('#final_total_input'));

    if (order_total < min_order_total_required) {
        is_valid = false;
        msg = __translate('min_order_total_error', {
            min_order: __currency_trans_from_en(min_order_total_required, true),
            rp_name: rp_name,
        });
    }

    var output = {
        is_valid: is_valid,
        msg: msg,
    };

    return output;
}

function adjustComboQty(tr) {
    if (tr.find('input.product_type').val() == 'combo') {
        var qty = __read_number(tr.find('input.pos_quantity'));
        var multiplier = __getUnitMultiplier(tr);

        tr.find('input.combo_product_qty').each(function () {
            $(this).val($(this).data('unit_quantity') * qty * multiplier);
        });
    }
}

$(document).on('change', '#types_of_service_id', function () {
    var types_of_service_id = $(this).val();
    var location_id = $('#location_id').val();

    if (types_of_service_id) {
        $.ajax({
            method: 'POST',
            url: '/sells/pos/get-types-of-service-details',
            data: {
                types_of_service_id: types_of_service_id,
                location_id: location_id,
            },
            dataType: 'json',
            success: function (result) {
                //reset form if price group is changed
                var prev_price_group = $('#types_of_service_price_group').val();
                if (result.price_group_id) {
                    $('#types_of_service_price_group').val(result.price_group_id);
                    $('#price_group_text').removeClass('hide');
                    $('#price_group_text span').text(result.price_group_name);
                } else {
                    $('#types_of_service_price_group').val('');
                    $('#price_group_text').addClass('hide');
                    $('#price_group_text span').text('');
                }
                $('#types_of_service_id').val(types_of_service_id);
                $('.types_of_service_modal').html(result.modal_html);

                if (prev_price_group != result.price_group_id) {
                    if ($('form#edit_pos_sell_form').length > 0) {
                        $('table#pos_table tbody').html('');
                        pos_total_row();
                    } else {
                        reset_pos_form();
                    }
                } else {
                    pos_total_row();
                }

                $('.types_of_service_modal').modal('show');
            },
        });
    } else {
        $('.types_of_service_modal').html('');
        $('#types_of_service_price_group').val('');
        $('#price_group_text').addClass('hide');
        $('#price_group_text span').text('');
        $('#packing_charge_text').text('');
        if ($('form#edit_pos_sell_form').length > 0) {
            $('table#pos_table tbody').html('');
            pos_total_row();
        } else {
            reset_pos_form();
        }
    }
});

$(document).on(
    'change',
    'input#packing_charge, #additional_expense_value_1, #additional_expense_value_2, \
        #additional_expense_value_3, #additional_expense_value_4',
    function () {
        pos_total_row();
    }
);

$(document).on('click', '.service_modal_btn', function (e) {
    if ($('#types_of_service_id').val()) {
        $('.types_of_service_modal').modal('show');
    }
});

$(document).on('change', '.payment_types_dropdown', function (e) {
    var default_accounts = $('select#select_location_id').length
        ? $('select#select_location_id').find(':selected').data('default_payment_accounts')
        : $('#location_id').data('default_payment_accounts');
    var payment_type = $(this).val();
    var payment_row = $(this).closest('.payment_row');
    if (payment_type && payment_type != 'advance') {
        var default_account =
            default_accounts && default_accounts[payment_type]['account']
                ? default_accounts[payment_type]['account']
                : '';
        var row_index = payment_row.find('.payment_row_index').val();

        var account_dropdown = payment_row.find('select#account_' + row_index);
        if (account_dropdown.length && default_accounts) {
            account_dropdown.val(default_account);
            account_dropdown.change();
        }
    }

    //Validate max amount and disable account if advance
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
            account_dropdown.closest('.form-group').addClass('hide');
        }
    } else {
        amount_element.rules('remove', 'max-value');
        if (account_dropdown) {
            account_dropdown.prop('disabled', false);
            account_dropdown.closest('.form-group').removeClass('hide');
        }
    }
});

$(document).on('show.bs.modal', '#recent_transactions_modal', function () {
    get_recent_transactions('final', $('div#tab_final'));
});
$(document).on('shown.bs.tab', 'a[href="#tab_quotation"]', function () {
    get_recent_transactions('quotation', $('div#tab_quotation'));
});
$(document).on('shown.bs.tab', 'a[href="#tab_draft"]', function () {
    get_recent_transactions('draft', $('div#tab_draft'));
});

function disable_pos_form_actions() {
    if (!window.navigator.onLine) {
        return false;
    }

    $('div.pos-processing').show();
    $('#pos-save').attr('disabled', 'true');
    $('div.pos-form-actions').find('button').attr('disabled', 'true');
}

function enable_pos_form_actions() {
    $('div.pos-processing').hide();
    $('#pos-save').removeAttr('disabled');
    $('div.pos-form-actions').find('button').removeAttr('disabled');
}

$(document).on('change', '#recur_interval_type', function () {
    if ($(this).val() == 'months') {
        $('.subscription_repeat_on_div').removeClass('hide');
    } else {
        $('.subscription_repeat_on_div').addClass('hide');
    }
});

function validate_discount_field() {
    discount_element = $('#discount_amount_modal');
    discount_type_element = $('#discount_type_modal');

    if ($('#add_sell_form').length || $('#edit_sell_form').length) {
        discount_element = $('#discount_amount');
        discount_type_element = $('#discount_type');
    }
    var max_value = parseFloat(discount_element.data('max-discount'));
    if (discount_element.val() != '' && !isNaN(max_value)) {
        if (discount_type_element.val() == 'fixed') {
            var subtotal = get_subtotal();
            //get max discount amount
            max_value = __calculate_amount('percentage', max_value, subtotal);
        }

        discount_element.rules('add', {
            'max-value': max_value,
            messages: {
                'max-value': discount_element.data('max-discount-error_msg'),
            },
        });
    } else {
        discount_element.rules('remove', 'max-value');
    }
    discount_element.trigger('change');
}

$(document).on('change', '#discount_type_modal, #discount_type', function () {
    validate_discount_field();
});

function update_shipping_address(data) {
    if ($('#shipping_address_div').length) {
        var shipping_address = '';
        if (data.supplier_business_name) {
            shipping_address += data.supplier_business_name;
        }
        if (data.name) {
            shipping_address += ',<br>' + data.name;
        }
        if (data.text) {
            shipping_address += ',<br>' + data.text;
        }
        shipping_address += ',<br>' + data.shipping_address;
        $('#shipping_address_div').html(shipping_address);
    }
    if ($('#billing_address_div').length) {
        var address = [];
        if (data.supplier_business_name) {
            address.push(data.supplier_business_name);
        }
        if (data.name) {
            address.push('<br>' + data.name);
        }
        if (data.text) {
            address.push('<br>' + data.text);
        }
        if (data.address_line_1) {
            address.push('<br>' + data.address_line_1);
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
        var billing_address = address.join(', ');
        $('#billing_address_div').html(billing_address);
    }

    if ($('#shipping_custom_field_1').length) {
        let shipping_custom_field_1 =
            data.shipping_custom_field_details != null
                ? data.shipping_custom_field_details.shipping_custom_field_1
                : '';
        $('#shipping_custom_field_1').val(shipping_custom_field_1);
    }

    if ($('#shipping_custom_field_2').length) {
        let shipping_custom_field_2 =
            data.shipping_custom_field_details != null
                ? data.shipping_custom_field_details.shipping_custom_field_2
                : '';
        $('#shipping_custom_field_2').val(shipping_custom_field_2);
    }

    if ($('#shipping_custom_field_3').length) {
        let shipping_custom_field_3 =
            data.shipping_custom_field_details != null
                ? data.shipping_custom_field_details.shipping_custom_field_3
                : '';
        $('#shipping_custom_field_3').val(shipping_custom_field_3);
    }

    if ($('#shipping_custom_field_4').length) {
        let shipping_custom_field_4 =
            data.shipping_custom_field_details != null
                ? data.shipping_custom_field_details.shipping_custom_field_4
                : '';
        $('#shipping_custom_field_4').val(shipping_custom_field_4);
    }

    if ($('#shipping_custom_field_5').length) {
        let shipping_custom_field_5 =
            data.shipping_custom_field_details != null
                ? data.shipping_custom_field_details.shipping_custom_field_5
                : '';
        $('#shipping_custom_field_5').val(shipping_custom_field_5);
    }

    //update export fields
    if (data.is_export) {
        $('#is_export').prop('checked', true);
        $('div.export_div').show();
        if ($('#export_custom_field_1').length) {
            $('#export_custom_field_1').val(data.export_custom_field_1);
        }
        if ($('#export_custom_field_2').length) {
            $('#export_custom_field_2').val(data.export_custom_field_2);
        }
        if ($('#export_custom_field_3').length) {
            $('#export_custom_field_3').val(data.export_custom_field_3);
        }
        if ($('#export_custom_field_4').length) {
            $('#export_custom_field_4').val(data.export_custom_field_4);
        }
        if ($('#export_custom_field_5').length) {
            $('#export_custom_field_5').val(data.export_custom_field_5);
        }
        if ($('#export_custom_field_6').length) {
            $('#export_custom_field_6').val(data.export_custom_field_6);
        }
    } else {
        $(
            '#export_custom_field_1, #export_custom_field_2, #export_custom_field_3, #export_custom_field_4, #export_custom_field_5, #export_custom_field_6'
        ).val('');
        $('#is_export').prop('checked', false);
        $('div.export_div').hide();
    }

    $('#shipping_address_modal').val(data.shipping_address);
    $('#shipping_address').val(data.shipping_address);
}

function get_sales_orders() {
    if ($('#sales_order_ids').length) {
        if ($('#sales_order_ids').hasClass('not_loaded')) {
            $('#sales_order_ids').removeClass('not_loaded');
            return false;
        }
        var customer_id = $('select#customer_id').val();
        var location_id = $('input#location_id').val();
        $.ajax({
            url: '/get-sales-orders/' + customer_id + '?location_id=' + location_id,
            dataType: 'json',
            success: function (data) {
                $('#sales_order_ids').select2('destroy').empty().select2({ data: data });
                $('table#pos_table tbody')
                    .find('tr')
                    .each(function () {
                        if (typeof $(this).data('so_id') !== 'undefined') {
                            $(this).remove();
                        }
                    });
                pos_total_row();
            },
        });
    }
}

$('#sales_order_ids').on('select2:select', function (e) {
    var sales_order_id = e.params.data.id;
    var product_row = $('input#product_row_count').val();
    var location_id = $('input#location_id').val();
    var location_id = $('input#location_id').val();
    var price_group = $('input#hidden_price_group').val();
    $.ajax({
        method: 'GET',
        url: '/get-sales-order-lines',
        async: false,
        data: {
            product_row: product_row,
            sales_order_id: sales_order_id,
            price_group: price_group,
        },
        dataType: 'json',
        success: function (result) {
            if (result.html) {
                var html = result.html;
                $(html)
                    .find('tr')
                    .each(function () {
                        $('table#pos_table tbody').append($(this)).find('input.pos_quantity');

                        var this_row = $('table#pos_table tbody').find('tr').last();
                        pos_each_row(this_row);

                        product_row = parseInt(product_row) + 1;

                        //For initial discount if present
                        var line_total = __read_number(this_row.find('input.pos_line_total'));
                        this_row.find('span.pos_line_total_text').text(`$ ${line_total}`);

                        //Check if multipler is present then multiply it when a new row is added.
                        if (__getUnitMultiplier(this_row) > 1) {
                            this_row.find('select.sub_unit').trigger('change');
                        }

                        round_row_to_iraqi_dinnar(this_row);
                        __currency_convert_recursively(this_row);
                    });

                set_so_values(result.sales_order);

                //increment row count
                $('input#product_row_count').val(product_row);

                pos_total_row();
            } else {
                toastr.error(result.msg);
                $('input#search_product').animate().focus().select();
            }
        },
    });
});

function set_so_values(so) {
    $('textarea[name="sale_note"]').val(so.additional_notes);
    if ($('#shipping_details').is(':visible')) {
        $('#shipping_details').val(so.shipping_details);
    }
    $('#shipping_address').val(so.shipping_address);
    $('#delivered_to').val(so.delivered_to);
    $('#shipping_charges').val(__number_f(so.shipping_charges));
    $('#shipping_status').val(so.shipping_status);
    if ($('#shipping_custom_field_1').length) {
        $('#shipping_custom_field_1').val(so.shipping_custom_field_1);
    }
    if ($('#shipping_custom_field_2').length) {
        $('#shipping_custom_field_2').val(so.shipping_custom_field_2);
    }
    if ($('#shipping_custom_field_3').length) {
        $('#shipping_custom_field_3').val(so.shipping_custom_field_3);
    }
    if ($('#shipping_custom_field_4').length) {
        $('#shipping_custom_field_4').val(so.shipping_custom_field_4);
    }
    if ($('#shipping_custom_field_5').length) {
        $('#shipping_custom_field_5').val(so.shipping_custom_field_5);
    }
}

$('#sales_order_ids').on('select2:unselect', function (e) {
    var sales_order_id = e.params.data.id;
    $('table#pos_table tbody')
        .find('tr')
        .each(function () {
            if (
                typeof $(this).data('so_id') !== 'undefined' &&
                $(this).data('so_id') == sales_order_id
            ) {
                $(this).remove();
                pos_total_row();
            }
        });
});

$(document).on('click', '#add_expense', function () {
    $.ajax({
        url: '/expenses/create',
        data: {
            location_id: $('#select_location_id').val(),
        },
        dataType: 'html',
        success: function (result) {
            $('#expense_modal').html(result);
            $('#expense_modal').modal('show');
        },
    });
});

$(document).on('shown.bs.modal', '#expense_modal', function () {
    $('#expense_transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });
    $('#expense_modal .paid_on').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });
    $(this).find('.select2').select2();
    $('#add_expense_modal_form').validate();
});

$(document).on('hidden.bs.modal', '#expense_modal', function () {
    $(this).html('');
});

$(document).on('submit', 'form#add_expense_modal_form', function (e) {
    e.preventDefault();
    var data = $(this).serialize();

    $.ajax({
        method: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        success: function (result) {
            if (result.success == true) {
                $('#expense_modal').modal('hide');
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

function get_contact_due(id) {
    $.ajax({
        method: 'get',
        url: /get-contact-due/ + id,
        dataType: 'text',
        success: function (result) {
            if (result != '') {
                $('.contact_due_text').find('span').text(result);
                $('.contact_due_text').removeClass('hide');
            } else {
                $('.contact_due_text').find('span').text('');
                $('.contact_due_text').addClass('hide');
            }
        },
    });
}

function submitQuickContactForm(form) {
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

                $('select#customer_id').append(
                    $('<option>', { value: result.data.id, text: name })
                );
                $('select#customer_id').val(result.data.id).trigger('change');
                $('div.contact_modal').modal('hide');
                update_shipping_address(result.data);
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
}

$(document).on('click', '#send_for_sell_return', function (e) {
    var invoice_no = $('#send_for_sell_return_invoice_no').val();

    if (invoice_no) {
        $.ajax({
            method: 'get',
            url: /validate-invoice-to-return/ + encodeURI(invoice_no),
            dataType: 'json',
            success: function (result) {
                if (result.success == true) {
                    window.location = result.redirect_url;
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    }
});

$(document).on('click', '#send_for_sercice_staff_replacement', function (e) {
    var invoice_no = $('#send_for_sell_service_staff_invoice_no').val();

    if (invoice_no) {
        $.ajax({
            method: 'get',
            url: /validate-invoice-to-service-staff-replacement/ + encodeURI(invoice_no),
            dataType: 'json',
            success: function (result) {
                if (result.success == true) {
                    $('#service_staff_replacement').popover('hide');
                    $('#service_staff_modal').html(result.msg);
                    $('#service_staff_modal').modal('show');
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    }
});

$(document).on('shown.bs.modal', '#service_staff_modal', function () {
    $('#change_service_staff').validate();
});

$(document).on('submit', 'form#change_service_staff', function (e) {
    e.preventDefault();
    var data = $(this).serialize();

    $.ajax({
        method: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        success: function (result) {
            if (result.success == true) {
                $('#service_staff_modal').modal('hide');
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

$(document).on('ifChanged', 'input[name="search_fields[]"]', function (event) {
    var search_fields = [];
    $('input[name="search_fields[]"]:checked').each(function () {
        search_fields.push($(this).val());
    });

    localStorage.setItem('pos_search_fields', search_fields);
});

function set_search_fields() {
    if ($('input[name="search_fields[]"]').length == 0) {
        return false;
    }

    var pos_search_fields = localStorage.getItem('pos_search_fields');

    if (pos_search_fields === null) {
        pos_search_fields = ['name', 'sku', 'lot'];
    }

    $('input[name="search_fields[]"]').each(function () {
        if (pos_search_fields.indexOf($(this).val()) >= 0) {
            $(this).iCheck('check');
        } else {
            $(this).iCheck('uncheck');
        }
    });
}

$(document).on('click', '#show_service_staff_availability', function () {
    loadServiceStaffAvailability();
});
$(document).on('click', '#refresh_service_staff_availability_status', function () {
    loadServiceStaffAvailability(false);
});
$(document).on('click', 'button.pause_resume_timer', function (e) {
    $('.view_modal').find('.overlay').removeClass('hide');
    $.ajax({
        method: 'get',
        url: $(this).attr('data-href'),
        dataType: 'json',
        success: function (result) {
            loadServiceStaffAvailability(false);
        },
    });
});

$(document).on('click', '.mark_as_available', function (e) {
    e.preventDefault();
    $('.view_modal').find('.overlay').removeClass('hide');
    $.ajax({
        method: 'get',
        url: $(this).attr('href'),
        dataType: 'json',
        success: function (result) {
            loadServiceStaffAvailability(false);
        },
    });
});
var service_staff_availability_interval = null;

function loadServiceStaffAvailability(show = true) {
    var location_id = $('[name="location_id"]').val();
    $.ajax({
        method: 'get',
        url: $('#show_service_staff_availability').attr('data-href'),
        dataType: 'html',
        data: { location_id: location_id },
        success: function (result) {
            $('.view_modal').html(result);
            if (show) {
                $('.view_modal').modal('show');

                //auto refresh service staff availabilty if modal is open
                service_staff_availability_interval = setInterval(function () {
                    loadServiceStaffAvailability(false);
                }, 60000);
            }
        },
    });
}

$(document).on('hidden.bs.modal', '.view_modal', function () {
    if (service_staff_availability_interval !== null) {
        clearInterval(service_staff_availability_interval);
    }
    service_staff_availability_interval = null;
});

$(document).on('change', '#res_waiter_id', function (e) {
    var is_enable = $(this).find('option:selected').data('is_enable');

    if (is_enable) {
        swal({
            text: LANG.enter_pin_here,
            buttons: true,
            dangerMode: true,
            content: {
                element: 'input',
                attributes: {
                    placeholder: LANG.enter_pin_here,
                    type: 'password',
                },
            },
        }).then((inputValue) => {
            if (inputValue !== null) {
                $.ajax({
                    method: 'get',
                    url: '/modules/data/check-staff-pin',
                    dataType: 'json',
                    data: {
                        service_staff_pin: inputValue,
                        user_id: $('#res_waiter_id').val(),
                    },
                    success: (result) => {
                        if (result == false) {
                            toastr.error(LANG.authentication_failed);
                            $('#res_waiter_id').val('');
                        } else {
                            // AJAX request succeeded, resolve
                            toastr.success(LANG.authentication_successfull);
                        }
                    },
                });
            } else {
                // Handle the "Cancel" action
                $('#res_waiter_id').val('');
            }
        });
    }
});
