<script type="text/javascript">
    $(document).ready(function () {
        let user = @json($user);
        let packing_order = @json($packingOrder);
        var warehouses = @json($shipstation);
        let warehouse = warehouses[0];
        let selectedRate = null;
        let choose_shipment;
        warehouse = (warehouses.length > 0 && warehouses[0]?.serviceList) ? warehouses[0] : null;

        let serviceList = [];
        if (warehouse) {
            try {
                serviceList = JSON.parse(warehouse.serviceList);
            } catch (e) {
                console.error('Failed to parse serviceList:', e);
            }
        } else {
            console.warn('No valid warehouse found or serviceList is missing');
        }

        let user_name;
        let user_company;
        let user_email;
        let user_phone;
        let user_address1;
        let user_address2;
        let user_zipCode;
        let user_state;
        let user_country;
        let user_city;

        user_name = (packing_order.shipping_first_name && packing_order.shipping_last_name) ?
            `${packing_order.shipping_first_name} ${packing_order.shipping_last_name}` : user.name;
        user_company = packing_order.shipping_company ?? user.supplier_business_name;
        user_email = packing_order.shipping_email ?? user.email;
        user_phone = packing_order.shipping_mobile ?? user.mobile;
        user_address1 = packing_order.shipping_address1 ?? user.shipping_address1;
        user_zipCode = packing_order.shipping_zip ?? user.shipping_zip;
        user_state = packing_order.shipping_state ?? user.shipping_state;
        user_country = packing_order.shipping_country ?? user.shipping_country;
        user_city = packing_order.shipping_city ?? user.shipping_city;

        $('.user_email').text(user_email);
        $('.user_phone').text(user_phone);
        $('.profileName').text(user_name);
        $('.businessName').text(user_company);
        $('.fullAddress').text(user_address1 + ', ' + user_city + ', ' + user_state + ', ' + user_country +
            ' - ' + user_zipCode);


        // Function to populate package and service options
        function populateOptions() {
            // Clear previous options completely
            $('#package_select').empty()
            $('#service_select').empty()

            // Aggregate packages from all configured carriers (avoid duplicates)
            let allPackages = [];
            let packageCodesSeen = new Set();
            
            $.each(serviceList, function (carrierIndex, carrier) {
                $.each(carrier.packages || [], function (index, package) {
                    // Only add if not already seen (avoid duplicates across carriers)
                    if (!packageCodesSeen.has(package.package_code)) {
                        allPackages.push(package);
                        packageCodesSeen.add(package.package_code);
                    }
                });
            });

            // Populate packages from all carriers
            $.each(allPackages, function (index, package) {
                $('#package_select').append(
                    $('<option>', {
                        value: package.package_code,
                        text: package.name + (package.description ? ' - ' + package.description : '')
                    })
                );
            });
            $('#package_select option:eq(0)').prop('selected', true); // Select the first package by default

            // Aggregate services from all configured carriers (group by carrier for clarity)
            let allServices = [];
            $.each(serviceList, function (carrierIndex, carrier) {
                $.each(carrier.services || [], function (index, service) {
                    allServices.push({
                        service: service,
                        carrierIndex: carrierIndex,
                        serviceIndex: index,
                        carrierName: carrier.friendly_name || carrier.carrier_code
                    });
                });
            });

            // Populate services from all carriers
            $.each(allServices, function (index, item) {
                let service = item.service;
                let displayText = service.name;
                // Add carrier name if multiple carriers exist
                if (serviceList.length > 1) {
                    displayText += ' (' + item.carrierName + ')';
                }
                $('#service_select').append(
                    $('<option>', {
                        value: item.carrierIndex + '_' + item.serviceIndex, // Store both carrier and service index
                        text: displayText,
                        'data-carrier-index': item.carrierIndex,
                        'data-service-index': item.serviceIndex
                    })
                );
            });
            $('#service_select option:eq(0)').prop('selected', true); // Select the first service by default
        }

        // Initial population
        populateOptions();

        // Handle warehouse change
        $('.warehouse_select').change(function () {
            $(".rate_row").css({
                "background-color": "",
                "border": ""
            });
            let index = $(this).val();
            warehouse = warehouses[index];
            $('.ship_from_name_text').text(warehouse.name);
            serviceList = JSON.parse(warehouse.serviceList);
            populateOptions();
            selectedRate = null;
        });

        $('#apply_preset').on('click', function () {
            $(".rate_row").css({
                "background-color": "",
                "border": ""
            });
            warehouse = warehouses[0];
            $('.ship_from_name_text').text(warehouse.name);
            serviceList = JSON.parse(warehouse.serviceList);
            $('#submit_selection_button').prop('disabled', false);
            populateOptions();
            $('#warehouse_select').val(0)
        })




        $("#rate-details").on("click", ".rate_row", function () {
            $(".rate_row").css({
                "background-color": "",
                "border": ""
            });

            $(this).css({
                "background-color": "#F0FFE1",
                'border': '3px solid #299C368F'
            });

            // Parse the rate data
            selectedRate = $(this).data("rate");
            $('#delivery_cost').text("$" + selectedRate.shipping_amount.amount);
            let date = new Date(selectedRate.estimated_delivery_date);
            let formattedDate =
                `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;

            $('#delivery_date').text(formattedDate);

            // Clear existing options first
            $('#service_select').empty();
            $('#package_select').empty();

            // Find and set service
            let serviceFound = false;
            $.each(serviceList, function(carrierIndex, carrier) {
                $.each(carrier.services || [], function(serviceIndex, service) {
                    if (service.service_code === selectedRate.service_code) {
                        $('#service_select').append(
                            $('<option>', {
                                value: carrierIndex + '_' + serviceIndex,
                                text: service.name,
                                selected: true
                            })
                        );
                        serviceFound = true;
                        return false; // break inner loop
                    }
                });
                if (serviceFound) return false; // break outer loop
            });

            // If service not found, use service_code directly
            if (!serviceFound) {
                $('#service_select').append(
                    $('<option>', {
                        value: selectedRate.service_code,
                        text: selectedRate.service_type || selectedRate.service_code,
                        selected: true
                    })
                );
            }

            // Find package by package_code from serviceList
            let packageFound = false;
            let packageCode = selectedRate.package_code || selectedRate.package_type;
            
            $.each(serviceList, function(carrierIndex, carrier) {
                $.each(carrier.packages || [], function(packageIndex, package) {
                    if (package.package_code === packageCode) {
                        $('#package_select').append(
                            $('<option>', {
                                value: package.package_code,
                                text: package.name || package.package_code,
                                selected: true
                            })
                        );
                        packageFound = true;
                        return false; // break inner loop
                    }
                });
                if (packageFound) return false; // break outer loop
            });

            // If package not found, use what's available in rate
            if (!packageFound) {
                let packageText = selectedRate.package_type || selectedRate.package_code || 'Package';
                $('#package_select').append(
                    $('<option>', {
                        value: packageCode || 'package',
                        text: packageText,
                        selected: true
                    })
                );
            }

        });

        // Auto-select dropdown based on radio button selection
        $('input[name="shipment_type"]').on('change', function () {
            $('#shipment_type').val($(this).val());
            choose_shipment = $(this).val();
        });
        // Set initial value based on packing_order
        if (packing_order.shipping_charges === '0.0000') {
            choose_shipment = 'pickup';
            $('#pickup').prop('checked', true);
            $('#shipment_type').val('pickup');
        } else {
            choose_shipment = 'own';
            $('#own').prop('checked', true);
            $('#shipment_type').val('own');
        }
        $('.edit_shipping_address').on('click', function () {
            $('#AddressModal').modal('show');
            $('#AddressModalLabel').text('Edit Shipping Address');
            $('#AddressForm').data('type', 'shipping');
            $('input[name="company"]').val(user_company);
            $('input[name="first_name"]').val(user.shipping_first_name);
            $('input[name="last_name"]').val(user.shipping_last_name);
            $('input[name="address_1"]').val(user_address1);
            $('input[name="address_2"]').val(user_address2);
            $('input[name="city_locality"]').val(user_city);
            $('input[name="state_province"]').val(user_state);
            $('input[name="postal_code"]').val(user_zipCode);
            $('input[name="country_code"]').val(user_country);
        });

        $(document).on('click', '#AddressModal .close_address', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('#AddressModal').modal('hide');
        });

        $('#saveAddress').on('click', function () {
            let formArray = $('#AddressForm').serializeArray();
            let addressType = $('#AddressForm').data('type');


            if (!validateAddressForm('shipping')) {
                return;
            }
            console.log('clicked');

            let address = {};
              $.each(formArray, function () {
                address[this.name] = this.value;
            });

            swal({
                title: "Are you sure?",
                text: "Do you really want to change address?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then(function (willchange) {
                if (willchange) {
                    let payload = {
                        id: packing_order.id,
                        shipping_company: address.company,
                        shipping_first_name: address.first_name,
                        shipping_last_name: address.last_name,
                        shipping_address1: address.address_1,
                        shipping_address2: address.address_2,
                        shipping_city: address.city_locality,
                        shipping_state: address.state_province,
                        shipping_zip: address.postal_code,
                        shipping_country: address.country_code,
                    }
                    $.ajax({
                        url: '/sells/pos/update_shipping_address_transaction',
                        type: 'POST',
                        data: payload,
                        success: function (response) {
                            if (response.status) {
                                toastr.success(response.message);
                                user.shipping_first_name = address.first_name;
                                user.shipping_last_name = address.last_name;

                                user_name = address.first_name + " " + address.last_name
                                user_company = address.company
                                user_address1 = address.address_1
                                user_address2 = address.address_2
                                user_city = address.city_locality
                                user_state = address.state_province
                                user_zipCode = address.postal_code
                                user_country = address.country_code

                                $('.profileName').text(user_name);
                                $('.businessName').text(user_company);
                                $('.fullAddress').text(user_address1 + ', ' + user_city + ', ' + user_state + ', ' + user_country +
                                    ' - ' + user_zipCode);
                                
                                $('#AddressModal').modal('hide');
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    });
                }
            });

        });
        function validateAddressForm(type) {
            const requiredFields = {
                shipping: ['company', 'first_name', 'last_name', 'address_1', 'city_locality', 'state_province', 'postal_code', 'country_code']
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

        $('#continue_button').on('click', function () {
            if (packing_order.shipping_charges !== '0.0000' && choose_shipment === 'own') {
                $('#shipment-details').addClass('hide')
                $('#shipment_type').val('own');
                $("#submit_selection_button").removeClass("tw-hidden");
                $("#modal_shipment_packing").css("width", "70%");
                $('#shipment-size').show();
                $('#own-shipment').show();
                $('#ups-shipment').hide();
            } else if (packing_order.shipping_charges !== '0.0000' && choose_shipment !== 'own') {
                swal({
                    icon: 'warning',
                    title: 'Confirm Change',
                    text: 'Confirm that you wish to change Shipment Type to Local-Shipment',
                    dangerMode: true,
                    buttons: {
                        cancel: 'Cancel',
                        confirm: 'Yes, Change'
                    }
                }).then((change) => {
                    if (change) {
                        choose_shipment = 'pickup';
                        let data = {
                            sale_invoice_no: packing_order.id
                        }
                        $('#main_loader').removeClass("hidden");
                        $.ajax({
                            url: '/sells-invoice-return-store',
                            method: "POST",
                            contentType: "application/json",
                            data: JSON.stringify(data),
                            success: function (response) {
                                $('#main_loader').addClass("hidden");
                                if (response.status) {
                                    toastr.success(response.msg);
                                    sell_return_ecom_table_approved.ajax.reload();
                                    swal({
                                        title: "Do you want to send an email?",
                                        text: "This will open the email template modal.",
                                        icon: "warning",
                                        buttons: ["No", "Yes"],
                                        dangerMode: true,
                                    }).then((willSend) => {
                                        if (willSend) {
                                            let url =
                                                "/notification/get-template/" +
                                                response.transaction +
                                                "/sell_return_pickup";

                                            $.ajax({
                                                url: url,
                                                type: "GET",
                                                success: function (
                                                    modalContent
                                                ) {
                                                    $('.view_modal')
                                                        .html(
                                                            modalContent
                                                        )
                                                        .modal(
                                                            'show'
                                                        );
                                                        $('#modal_pickup_modal').modal('hide');
                                                },
                                                error: function () {
                                                    toastr
                                                        .error(
                                                            "Failed to load the email template."
                                                        );
                                                }
                                            });
                                        } else {
                                            $('#modal_pickup_modal').modal('hide');
                                            $('.view_modal').modal('hide');
                                        }
                                    });
                                } else {
                                    $('#main_loader').addClass("hidden");
                                    toastr.error(response.msg);
                                    toastr.error(response.message);
                                }
                            },
                            error: function () {
                                $('#main_loader').addClass("hidden");
                                toastr.error(
                                    'There was an error processing your request. Please try again.'
                                );
                            }
                        });
                    } else {
                        choose_shipment = 'own';
                        $('#own').prop('checked', true);
                        $('#shipment_type').val('own');
                    }
                });
            } else if (packing_order.shipping_charges === '0.0000' && choose_shipment === 'pickup') {
                let data = {
                    sale_invoice_no: packing_order.id
                }
                $('#main_loader').removeClass("hidden");
                $.ajax({
                    url: '/sells-invoice-return-store',
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(data),
                    success: function (response) {
                        $('#main_loader').addClass("hidden");
                        if (response.status) {
                            toastr.success(response.msg);
                            sell_return_ecom_table_approved.ajax.reload();
                            swal({
                                title: "Do you want to send an email?",
                                text: "This will open the email template modal.",
                                icon: "warning",
                                buttons: ["No", "Yes"],
                                dangerMode: true,
                            }).then((willSend) => {
                                if (willSend) {
                                    let url =
                                        "/notification/get-template/" +
                                        response.transaction +
                                        "/sell_return_pickup";

                                    $.ajax({
                                        url: url,
                                        type: "GET",
                                        success: function (
                                            modalContent
                                        ) {
                                            $('.view_modal')
                                                .html(
                                                    modalContent
                                                )
                                                .modal(
                                                    'show'
                                                );
                                            $('#modal_pickup_modal').modal('hide');
                                        },
                                        error: function () {
                                            toastr
                                                .error(
                                                    "Failed to load the email template."
                                                );
                                        }
                                    });
                                } else {
                                    $('#modal_pickup_modal').modal('hide');
                                    $('.view_modal').modal('hide');
                                }
                            });
                        } else {
                            $('#main_loader').addClass("hidden");
                            toastr.error(response.msg);
                            toastr.error(response.message);
                        }
                    },
                    error: function () {
                        $('#main_loader').addClass("hidden");
                        toastr.error(
                            'There was an error processing your request. Please try again.'
                        );
                    }
                });
            } else if (packing_order.shipping_charges === '0.0000' && choose_shipment !== 'pickup') {
                swal({
                    icon: 'warning',
                    title: 'Confirm Change',
                    text: 'Confirm that you wish to change Pickup To Shipment',
                    dangerMode: true,
                    buttons: {
                        cancel: 'Cancel',
                        confirm: 'Yes, Change'
                    }
                }).then((change) => {
                    if (change) {
                        $('#shipment-details').addClass('hide')
                        $('#shipment_type').val('own');
                        $("#submit_selection_button").removeClass("tw-hidden");
                        $("#modal_shipment_packing").css("width", "70%");
                        $('#shipment-size').show();
                        $('#own-shipment').show();
                        $('#ups-shipment').hide();
                        choose_shipment = 'own';
                        packing_order.shipping_charges = 'some';
                    } else {
                        choose_shipment = 'pickup';
                        $('#pickup').prop('checked', true);
                        $('#shipment_type').val('pickup');
                    }
                });
            }
        })

        $("#cost_review").on("click", function () {
            let isValid = true;

            // Select all required input fields
            let requiredFields = [
                'input[name="shipment_date"]',
                '#warehouse_select',
                '#service_select',
                '#package_select',
                'input[name="length"]',
                'input[name="width"]',
                'input[name="height"]'
            ];

            // Loop through fields and validate
            $.each(requiredFields, function (index, selector) {
                let field = $(selector);
                let value = field.val().trim();

                if (field.is('select')) {
                    if (value === "") {
                        isValid = false;
                        field.addClass("border border-danger").css("background-color",
                            "#f8d7da");
                    } else {
                        field.removeClass("border border-danger").css("background-color", "");
                    }
                } else {
                    let numericValue = parseFloat(value);
                    if (value === "" || isNaN(numericValue) || numericValue <= 0) {
                        isValid = false;
                        field.addClass("border border-danger").css("background-color",
                            "#f8d7da");
                    } else {
                        field.removeClass("border border-danger").css("background-color", "");
                    }
                }
            });

            // Validate weight_lb and weight_OZ together
            let weightLbField = $('input[name="weight_lb"]');
            let weightOzField = $('input[name="weight_OZ"]');

            let weightLb = parseFloat(weightLbField.val()) || 0;
            let weightOz = parseFloat(weightOzField.val()) || 0;

            let weightHasError = false;

            // Check for negative values
            if (weightLb < 0 || weightOz < 0) {
                weightHasError = true;
            }
            // Check if both are zero
            else if (weightLb === 0 && weightOz === 0) {
                weightHasError = true;
            }

            if (weightHasError) {
                isValid = false;
                weightLbField.addClass("border border-danger").css("background-color", "#f8d7da");
                weightOzField.addClass("border border-danger").css("background-color", "#f8d7da");
            } else {
                weightLbField.removeClass("border border-danger").css("background-color", "");
                weightOzField.removeClass("border border-danger").css("background-color", "");
            }

            // Final check and processing
            if (!isValid) {
                $('#submit_selection_button').prop('disabled', false);
                return;
            }


            // Calculate weight in grams
            let weightInGrams = ((weightLb * 16) + weightOz) * 28.3495;

            let length = $('input[name="length"]').val();
            let width = $('input[name="width"]').val();
            let height = $('input[name="height"]').val();
            let shipment_date = $('input[name="shipment_date"]').val();

            // Get selected package type from dropdown
            let selectedPackageCode = $('#package_select').val();
            // For rate estimation, use package_code as package_type (ShipStation API accepts both)
            // Some carriers like UPS use package_code, while USPS uses package_type
            let packageTypeForRate = selectedPackageCode || "package";

            let data = {
                "date": shipment_date,
                "warehouse_id": `${warehouse.id}`,
               "from_country_code": user_country,
                "from_postal_code": user_zipCode,
                "from_city_locality": user_city,
                "from_state_province": user_state,
                "to_country_code": "US",
                "to_postal_code": warehouse.postal_code,
                "to_city_locality": warehouse.city_locality,
                "to_state_province": warehouse.state_province,
                "weight": {
                    "value": weightInGrams,
                    "unit": 'gram'
                },
                "packages": [{
                    "package_type": packageTypeForRate,
                    "dimensions": {
                        "unit": "inch",
                        "length": length,
                        "width": width,
                        "height": height
                    },
                    "insuranceProviderId": 0,
                    "insuranceProvider": "None",
                    "insured_value": {
                        "currency": "usd",
                        "value": 0.00
                    },
                    "label_messages": {
                        "reference1": "test order 3"
                    },
                    "content_description": "Hand knitted wool socks",
                    "products": []
                }]
            };
            if (isValid) {
                $('#main_loader').removeClass("hidden");
                $.ajax({
                    url: "/get-est-rate",
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(data),
                    success: function (response) {
                        if (response.status && !response.data.errors) {
                            rates = response.data;
                            let rateDetailsHtml = `
        <div class='tw-overflow-y-auto' style="padding: 16px; max-height:40vh">
            <table class="table">
                <tbody>`;

                            // Helper function to get package display name
                            function getPackageDisplayName(rate) {
                                const packageCode = rate?.package_code || rate?.package_type;
                                if (!packageCode) return rate?.service_code || 'Package';
                                
                                // Try to find package name from serviceList
                                let packageName = null;
                                $.each(serviceList, function(carrierIndex, carrier) {
                                    $.each(carrier.packages || [], function(packageIndex, package) {
                                        if (package.package_code === packageCode) {
                                            packageName = package.name;
                                            return false; // break inner loop
                                        }
                                    });
                                    if (packageName) return false; // break outer loop
                                });
                                
                                // Return found name, or package_type, or package_code, or fallback
                                return packageName || rate?.package_type || packageCode || 'Package';
                            }
                            
                            $.each(rates, function (index, rate) {
                                // Comprehensive carrier logo mapping
                                // Check multiple fields: carrier_friendly_name, carrier_nickname, carrier_code
                                function getCarrierLogo(rate) {
                                    const carrierName = (rate?.carrier_friendly_name || rate?.carrier_nickname || rate?.carrier_code || '').toLowerCase();
                                    const carrierCode = (rate?.carrier_code || '').toLowerCase();
                                    
                                    // Use absolute path from root
                                    const imgBasePath = '/img/';
                                    
                                    // Comprehensive mapping for all possible carrier names
                                    const carrierLogos = {
                                        // USPS variations
                                        'usps': imgBasePath + 'usps.svg',
                                        'united states postal service': imgBasePath + 'usps.svg',
                                        'us postal service': imgBasePath + 'usps.svg',
                                        
                                        // UPS variations
                                        'ups': imgBasePath + 'ups.svg',
                                        'united parcel service': imgBasePath + 'ups.svg',
                                        'ups ground': imgBasePath + 'ups.svg',
                                        'ups next day': imgBasePath + 'ups.svg',
                                        'ups 2nd day': imgBasePath + 'ups.svg',
                                        
                                        // FedEx variations
                                        'fedex': imgBasePath + 'fedex.svg',
                                        'fedex one balance': imgBasePath + 'fedex.svg',
                                        'fedex express': imgBasePath + 'fedex.svg',
                                        'fedex ground': imgBasePath + 'fedex.svg',
                                        'fedex home delivery': imgBasePath + 'fedex.svg',
                                        'fedex smartpost': imgBasePath + 'fedex.svg',
                                        
                                        // DHL variations
                                        'dhl': imgBasePath + 'dhl-express.svg',
                                        'dhl express': imgBasePath + 'dhl-express.svg',
                                        'dhl global': imgBasePath + 'dhl-express.svg',
                                        
                                        // GlobalPost variations
                                        'globalpost': imgBasePath + 'globalpost.svg',
                                        'global post': imgBasePath + 'globalpost.svg',
                                        'globalpost international': imgBasePath + 'globalpost.svg',
                                    };
                                    
                                    // Try exact match first
                                    if (carrierLogos[carrierName]) {
                                        return carrierLogos[carrierName];
                                    }
                                    
                                    // Try partial matches
                                    for (const [key, value] of Object.entries(carrierLogos)) {
                                        if (carrierName.includes(key) || key.includes(carrierName)) {
                                            return value;
                                        }
                                    }
                                    
                                    // Check carrier_code for common codes
                                    if (carrierCode.includes('usps')) {
                                        return imgBasePath + 'usps.svg';
                                    }
                                    if (carrierCode.includes('ups')) {
                                        return imgBasePath + 'ups.svg';
                                    }
                                    if (carrierCode.includes('fedex') || carrierCode.includes('fed_ex')) {
                                        return imgBasePath + 'fedex.svg';
                                    }
                                    if (carrierCode.includes('dhl')) {
                                        return imgBasePath + 'dhl-express.svg';
                                    }
                                    if (carrierCode.includes('globalpost')) {
                                        return imgBasePath + 'globalpost.svg';
                                    }
                                    
                                    // Default fallback
                                    return imgBasePath + 'default.png';
                                }
                                
                                const logoSrc = getCarrierLogo(rate);
                                const carrierDisplayName = rate?.carrier_friendly_name || rate?.carrier_nickname || 'Unknown Carrier';
                                const defaultImgPath = '/img/default.png';
                                
                                if (rate.estimated_delivery_date === null) {
                                    rateDetailsHtml += `<tr class="rate_row" style="cursor: not-allowed; opacity: 0.5; pointer-events: none; background-color: #f5f5f5 !important;" data-rate='${JSON.stringify(rate)}'><td><img src="${logoSrc}" alt="${carrierDisplayName} logo" style="width: 48px; height: 48px;" onerror="this.src='${defaultImgPath}'"></td><td class="service-type">${carrierDisplayName}</td><td>Not Available</td><td></td></tr>`;
                                } else {
                                    rateDetailsHtml += `
            <tr class="rate_row" style="cursor: pointer;" data-rate='${JSON.stringify(rate)}'>
                <td>
                    
                    <img src="${logoSrc}" alt="${carrierDisplayName} logo" style="width: 48px; height: 48px;" onerror="this.src='${defaultImgPath}'">
                </td>
                <td class="service-type">
                    <p style="text-transform: capitalize; font-weight: bold; font-size: 18px; color: black;">${rate.service_type}</p>
                    <p style="font-size: 14px; text-transform: capitalize; ">${rate.carrier_nickname || rate.carrier_friendly_name || ''} | ${getPackageDisplayName(rate)}</p>
                </td>
                <td style="font-weight: bold; font-size: 18px; color: black;">${rate.delivery_days} days</td>
                <td class="service-code" data-service-code="${rate.service_code}" 
                    style="font-weight: bold; font-size: 18px; color: black;">
                    ${rate?.shipping_amount?.amount} ${rate?.shipping_amount?.currency}
                </td>
            </tr>`;
                                }

                            });
                            rateDetailsHtml += `</tbody></table></div>`;
                            $('#main_loader').addClass("hidden");
                            $("#rate-details").html(rateDetailsHtml);
                            $('#rate-details').removeClass('hidden');

                        } else if (response.data.errors) {
                            $('#main_loader').addClass("hidden");
                            toastr.error(response.data.errors[0].message);
                        } else {
                            $('#main_loader').addClass("hidden");
                            toastr.error(response.message);
                        }
                    },
                    error: function (error) {
                        $('#main_loader').addClass("hidden");
                        console.error("Error fetching rate data:", error);
                        toastr.error(error.responseJSON.message);

                    }
                });
            }

        });


        $('#submit_selection_button').on('click', function () {

            $('#submit_selection_button').prop('disabled', true);
            let isValid = true;

            // Select all required input fields
            let requiredFields = [
                'input[name="shipment_date"]',
                '#warehouse_select',
                '#service_select',
                '#package_select',
                'input[name="length"]',
                'input[name="width"]',
                'input[name="height"]'
            ];

            // Loop through fields and validate
            $.each(requiredFields, function (index, selector) {
                let field = $(selector);
                let value = field.val().trim();

                if (field.is('select')) {
                    if (value === "") {
                        isValid = false;
                        field.addClass("border border-danger").css("background-color",
                            "#f8d7da");
                    } else {
                        field.removeClass("border border-danger").css("background-color", "");
                    }
                } else {
                    let numericValue = parseFloat(value);
                    if (value === "" || isNaN(numericValue) || numericValue <= 0) {
                        isValid = false;
                        field.addClass("border border-danger").css("background-color",
                            "#f8d7da");
                    } else {
                        field.removeClass("border border-danger").css("background-color", "");
                    }
                }
            });

            // Validate weight_lb and weight_OZ together
            let weightLbField = $('input[name="weight_lb"]');
            let weightOzField = $('input[name="weight_OZ"]');

            let weightLb = parseFloat(weightLbField.val()) || 0;
            let weightOz = parseFloat(weightOzField.val()) || 0;

            let weightHasError = false;

            // Check for negative values
            if (weightLb < 0 || weightOz < 0) {
                weightHasError = true;
            }
            // Check if both are zero
            else if (weightLb === 0 && weightOz === 0) {
                weightHasError = true;
            }

            if (weightHasError) {
                isValid = false;
                weightLbField.addClass("border border-danger").css("background-color", "#f8d7da");
                weightOzField.addClass("border border-danger").css("background-color", "#f8d7da");
            } else {
                weightLbField.removeClass("border border-danger").css("background-color", "");
                weightOzField.removeClass("border border-danger").css("background-color", "");
            }

            // Final check and processing
            if (!isValid) {
                $('#submit_selection_button').prop('disabled', false);
                return;
            }


            // Calculate weight in grams
            let weightInGrams = ((weightLb * 16) + weightOz) * 28.3495;

            let length = $('input[name="length"]').val();
            let width = $('input[name="width"]').val();
            let height = $('input[name="height"]').val();

            let service_type;
            let carrier_code;
            let service_code;
            let carrier_id;
            let package_type;
            if (!selectedRate) {
                let serviceValue = $('#service_select').val();
                // Parse carrier and service indices from stored value
                let parts = serviceValue.split('_');
                let carrierIndex = parseInt(parts[0]);
                let serviceIndex = parseInt(parts[1]);
                
                if (serviceList[carrierIndex] && serviceList[carrierIndex].services[serviceIndex]) {
                    let service = serviceList[carrierIndex].services[serviceIndex];
                    let carrier = serviceList[carrierIndex];
                    service_code = service.service_code;
                    carrier_code = service.carrier_code || carrier.carrier_code;
                    service_type = service.name;
                    carrier_id = service.carrier_id || carrier.carrier_id;
                } else {
                    // Fallback to first carrier/service if parsing fails
                    let service = serviceList[0].services[0];
                    let carrier = serviceList[0];
                    service_code = service.service_code;
                    carrier_code = service.carrier_code || carrier.carrier_code;
                    service_type = service.name;
                    carrier_id = service.carrier_id || carrier.carrier_id;
                }
                // Get package_code from dropdown - this is what ShipStation API expects
                // For UPS, package_code is used directly, for USPS it may need mapping
                package_type = $('#package_select').val();
            } else {
                service_code = selectedRate?.service_code;
                carrier_code = selectedRate?.carrier_code;
                service_type = selectedRate?.service_type;
                carrier_id = selectedRate?.carrier_id;
                // Use package_code from rate (preferred) or package_type, or fallback to dropdown value
                // For UPS, package_code is what we need, for USPS it might be package_type
                package_type = selectedRate?.package_code || selectedRate?.package_type || $('#package_select').val();
            }
            let shipment_date = $('input[name="shipment_date"]').val();
            let data = {
                "date": shipment_date,
                "sale_invoice_no": packing_order.id,
                "warehouse_id": parseInt(warehouse.id, 10),
                "shipment": {
                    "shipment_type": 'own',
                    "service_type": service_type,
                    "service_code": service_code,
                    "carrier_code": carrier_code,
                    "carrier_id": carrier_id,
                    "validate_address": "validate_and_clean",
                    "ship_to": {
                        "name": warehouse.name,
                        "phone": warehouse.phone,
                        "company_name": warehouse.company_name,
                        "address_line1": warehouse.address_1,
                        "city_locality": warehouse.city_locality,
                        "state_province": warehouse.state_province,
                        "postal_code": warehouse.postal_code,
                        "country_code": warehouse.country_code,
                        "address_residential_indicator": "no"
                    },
                    "ship_from": {
                        "name": user_name,
                        "phone": user_phone,
                        "company_name": user_company ?? '',
                        "address_line1": user_address1,
                        "city_locality": user_city,
                        "state_province": user_state,
                        "postal_code": user_zipCode,
                        "country_code": user_country,
                        "address_residential_indicator": "no"

                    },
                    "packages": [{
                        "package_type": package_type,
                        "dimensions": {
                            "unit": "inch",
                            "length": length,
                            "width": width,
                            "height": height
                        },
                        "weight": {
                            "value": weightInGrams,
                            "unit": "gram"
                        },
                        "insuranceProviderId": 0,
                        "insuranceProvider": "None",
                        "insured_value": {
                            "currency": "usd",
                            "value": 0.00
                        },
                        "label_messages": {
                            "reference1": "test order 2"
                        },
                        "content_description": "General Merchant",
                        "products": [],

                    }]
                }
            };
            if (isValid) {
                $('#main_loader').removeClass("hidden");

                $.ajax({
                    url: '/sells-invoice-return-store',
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(data),
                    success: function (response) {
                        $('#main_loader').addClass("hidden");
                        if (response.status) {
                            toastr.success(response.msg);
                            window.open(response.shipping_label.href, '_blank');
                            sell_return_ecom_table_approved.ajax.reload();
                            swal({
                                title: "Do you want to send an email?",
                                text: "This will open the email template modal.",
                                icon: "warning",
                                buttons: ["No", "Yes"],
                                dangerMode: true,
                            }).then((willSend) => {
                                if (willSend) {
                                    let url = "/notification/get-template/" +
                                        response.transaction + "/sell_return_shipment";

                                    $.ajax({
                                        url: url,
                                        type: "GET",
                                        success: function (modalContent) {
                                            $('.view_modal').html(
                                                modalContent)
                                                .modal({
                                                    backdrop: 'static',
                                                    keyboard: false
                                                });
                                            $('#modal_pickup_modal').modal('hide');
                                        },
                                        error: function () {
                                            toastr.error(
                                                "Failed to load the email template."
                                            );
                                        }
                                    });
                                } else {
                                    $('#modal_pickup_modal').modal('hide');
                                    $('.view_modal').modal('hide');
                                }
                            });
                        } else {
                            toastr.error(response.message);
                            toastr.error(response.msg);
                            $('#submit_selection_button').prop('disabled', false);
                        }
                    },
                    error: function () {
                        toastr.error(
                            'There was an error processing your request. Please try again.'
                        );
                        $('#submit_selection_button').prop('disabled', false);
                        $('#main_loader').addClass("hidden");
                    }
                });
            }
        });
        const fieldSelectors = [
            'input[name="shipment_date"]',
            '#warehouse_select',
            'input[name="weight_lb"]',
            'input[name="weight_OZ"]',
            '#service_select',
            '#package_select',
            'input[name="length"]',
            'input[name="width"]',
            'input[name="height"]'
        ].join(', '); // Join selectors with a comma

        // Attach change event listener
        $(document).on('change', fieldSelectors, function () {
            onFieldChange(); // Call the desired function
        });

        // Your function to be triggered on change
        function onFieldChange() {
            selectedRate = null;
            $(".rate_row").css({
                "background-color": "",
                "border": ""
            });
        }
        $(document).ready(function () {
            const today = new Date().toISOString().split('T')[0];
            $('input[name="shipment_date"]').val(today);
        });

        $('#manual_ship').on('click', function () {

            $('#shipment-details').addClass('hide')
            $('#shipment_type').val('own');
            $("#submit_selection_button").removeClass("tw-hidden");
            $("#modal_shipment_packing").css("width", "50%");
            $('#shipment-size').show();
            $('#own-shipment').hide();
            $('#ups-shipment').hide();
            $('#manual-shipment').show();
            $('#user_email').text(user_email);
            $('#user_phone').text(user_phone);
            $('#profileName').text(user_name);
            $('#businessName').text(user_company);
            $('#fullAddress').text(user_address1 + ', ' + user_city + ', ' + user_state + ', ' + user_country +
                ' - ' + user_zipCode);

        })
        $('#auto_ship').on('click', function () {

            $('#shipment-details').addClass('hide')
            $('#shipment_type').val('own');
            $("#submit_selection_button").removeClass("tw-hidden");
            $("#modal_shipment_packing").css("width", "70%");
            $('#shipment-size').show();
            $('#own-shipment').show();
            $('#ups-shipment').hide();
            $('#manual-shipment').hide();

        })

        $('#submit_manual_shipment_button').on('click', function () {

            $('#submit_manual_shipment_button').prop('disabled', true);
            let isValid = true;

            // Select all required input fields
            let requiredFields = [
                'input[name="shipment_datem"]',
                '#warehouse_select',
                'input[name="lengthm"]',
                'input[name="widthm"]',
                'input[name="heightm"]',
                'input[name="shipping_chargesm"]'
            ];

            // Loop through fields and validate
            $.each(requiredFields, function (index, selector) {
                let field = $(selector);
                let value = field.val().trim();

                if (field.is('select')) {
                    if (value === "") {
                        isValid = false;
                        field.addClass("border border-danger").css("background-color",
                            "#f8d7da");
                    } else {
                        field.removeClass("border border-danger").css("background-color", "");
                    }
                } else {
                    let numericValue = parseFloat(value);
                    if (value === "" || isNaN(numericValue) || numericValue <= 0) {
                        isValid = false;
                        field.addClass("border border-danger").css("background-color",
                            "#f8d7da");
                    } else {
                        field.removeClass("border border-danger").css("background-color", "");
                    }
                }
            });

            // Validate weight_lb and weight_OZ together
            let weightLbField = $('input[name="weight_lbm"]');
            let weightOzField = $('input[name="weight_OZm"]');

            let weightLb = parseFloat(weightLbField.val()) || 0;
            let weightOz = parseFloat(weightOzField.val()) || 0;

            let weightHasError = false;

            // Check for negative values
            if (weightLb < 0 || weightOz < 0) {
                weightHasError = true;
            }
            // Check if both are zero
            else if (weightLb === 0 && weightOz === 0) {
                weightHasError = true;
            }

            if (weightHasError) {
                isValid = false;
                weightLbField.addClass("border border-danger").css("background-color", "#f8d7da");
                weightOzField.addClass("border border-danger").css("background-color", "#f8d7da");
            } else {
                weightLbField.removeClass("border border-danger").css("background-color", "");
                weightOzField.removeClass("border border-danger").css("background-color", "");
            }

            // Final check and processing
            if (!isValid) {
                $('#submit_manual_shipment_button').prop('disabled', false);
                return;
            }

            // Calculate weight in grams
            let weightInGrams = ((weightLb * 16) + weightOz) * 28.3495;

            let length = $('input[name="lengthm"]').val();
            let width = $('input[name="widthm"]').val();
            let height = $('input[name="heightm"]').val();
            let shipping_charges = $('input[name="shipping_chargesm"]').val()
            let shipment_date = $('input[name="shipment_datem"]').val();
            let data = {
                "date": shipment_date,
                "sale_invoice_no": packing_order.id,
                "warehouse_id": parseInt(warehouse.id, 10),
                "shipment": {
                    "shipment_type": 'manual',
                    "shipping_charges": shipping_charges,
                    "ship_to": {
                        "name": warehouse.name,
                        "phone": warehouse.phone,
                        "company_name": warehouse.company_name,
                        "address_line1": warehouse.address_1,
                        "city_locality": warehouse.city_locality,
                        "state_province": warehouse.state_province,
                        "postal_code": warehouse.postal_code,
                        "country_code": warehouse.country_code,
                        "address_residential_indicator": "no"
                    },
                    "ship_from": {
                        "name": user_name,
                        "phone": user_phone,
                        "company_name": user_company ?? '',
                        "address_line1": user_address1,
                        "city_locality": user_city,
                        "state_province": user_state,
                        "postal_code": user_zipCode,
                        "country_code": user_country,
                        "address_residential_indicator": "no"

                    },
                    "packages": [{
                        "dimensions": {
                            "unit": "inch",
                            "length": length,
                            "width": width,
                            "height": height
                        },
                        "weight": {
                            "value": weightInGrams,
                            "unit": "gram"
                        },
                        "insuranceProviderId": 0,
                        "insuranceProvider": "None",
                        "insured_value": {
                            "currency": "usd",
                            "value": 0.00
                        },
                        "label_messages": {
                            "reference1": "test order 2"
                        },
                        "content_description": "General Merchant",
                        "products": [],

                    }]
                }
            };
            if (isValid) {
                $('#main_loader').removeClass("hidden");

                $.ajax({
                    url: '/sells-invoice-return-store',
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(data),
                    success: function (response) {
                        $('#main_loader').addClass("hidden");
                        if (response.status) {
                            toastr.success(response.msg);
                            window.open(response.shipping_label, '_blank');
                            sell_return_ecom_table_approved.ajax.reload();
                            swal({
                                title: "Do you want to send an email?",
                                text: "This will open the email template modal.",
                                icon: "warning",
                                buttons: ["No", "Yes"],
                                dangerMode: true,
                            }).then((willSend) => {
                                if (willSend) {
                                    let url = "/notification/get-template/" +
                                        response.transaction + "/sell_return_shipment";

                                    $.ajax({
                                        url: url,
                                        type: "GET",
                                        success: function (modalContent) {
                                            $('.view_modal').html(
                                                modalContent)
                                                .modal({
                                                    backdrop: 'static',
                                                    keyboard: false
                                                });
                                            $('#modal_pickup_modal').modal('hide');
                                        },
                                        error: function () {
                                            toastr.error(
                                                "Failed to load the email template."
                                            );
                                        }
                                    });
                                } else {
                                    $('#modal_pickup_modal').modal('hide');
                                    $('.view_modal').modal('hide');
                                }
                            });
                        } else {
                            toastr.error(response.message);
                            toastr.error(response.msg);
                            $('#submit_manual_shipment_button').prop('disabled', false);
                        }
                    },
                    error: function () {
                        toastr.error(
                            'There was an error processing your request. Please try again.'
                        );
                        $('#submit_manual_shipment_button').prop('disabled', false);
                        $('#main_loader').addClass("hidden");
                    }
                });
            }
        });
    });
</script>