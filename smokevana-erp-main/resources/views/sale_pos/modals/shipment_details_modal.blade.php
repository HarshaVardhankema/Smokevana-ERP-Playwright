<div class="modal-dialog no-print" role="document" @if ($shipment['shipment_details']['service_code']=='Manual Shipment')
    style="width: 70%; max-height: 90vh; overflow: hidden;"
@else
   style="width: 70%; max-height: 90vh; overflow: hidden;" 
@endif  id='shipment_details_modal'>
    <div class="modal-content" style="max-height: 90vh; overflow: hidden; display: flex; flex-direction: column;">
        <div class="tw-flex tw-justify-between gradiantDiv tw-p-2"
            style="border-top-left-radius: 10px; border-top-right-radius: 10px">
            <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-2">

                <h3 class="tw-text-base sm:tw-text-lg tw-text-white">
                    <b>Shipment #{{ $sale->invoice_no ?? '_ _ _ _ _' }}</b>
                </h3>
                <div class="tw-flex tw-justify-center">
                    <h5 class="bg-green tw-text-white tw-rounded-full tw-px-4 tw-py-2 tw-text-sm sm:tw-text-base">
                        Shipped
                    </h5>
                </div>
            </div>

            <div class="tw-flex tw-flex-wrap tw-justify-end tw-gap-2 ">
                @can('print_invoice')
                    <a href="#"
                        class="print-invoice tw-dw-btn tw-dw-btn-primary tw-text-white tw-px-2 tw-py-1 tw-text-sm"
                        data-href="{{ route('sell.printInvoice', [$sale->id]) }}">
                        <i class="fa fa-print" aria-hidden="true"></i> @lang('lang_v1.print_invoice')
                    </a>
                @endcan
                <div class="dropdown">
                    <button class="tw-dw-btn tw-text-white tw-px-2 tw-py-1 tw-text-sm tw-w-full sm:tw-w-auto"
                        type="button" id="dropdownButton" data-bs-toggle="dropdown" aria-expanded="false"
                        style="background: #4822BB; border: none;">
                        Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownButton" id="head_dropdown"
                        style="position: absolute; z-index: 9999; max-height: 200px; overflow-y: auto;">
                        <li>
                            <a class="dropdown-item send-notification" href="#">
                                Send Notification
                            </a>
                        </li>
                        <li>
                            <a href="#" class="dropdown-item" id="void_label_button">
                                Void Label
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" id="copyTracking">
                                <p id="copyText" class="cursor-pointer">Copy Tracking Number</p>
                            </a>
                        </li>
                    </ul>
                </div>

                <button id="Create_return_button"
                    class="tw-dw-btn tw-text-white tw-px-2 tw-py-1 tw-text-sm tw-w-full sm:tw-w-auto no-print"
                    style="background: #4822BB; border: none;">
                    Create Return
                </button>
                <button type="button"
                    class="tw-dw-btn tw-dw-btn-neutral tw-text-white tw-px-2 tw-py-1 tw-text-sm tw-w-full sm:tw-w-auto no-print"
                    data-dismiss="modal" id="close_button">
                    @lang('messages.close')
                </button>
            </div>


        </div>
        <div class="modal-body" style="flex: 1; overflow-y: auto;">
            <div class="row tw-gap-2">
                <!-- Packing Slip -->
                <div class="col-lg-2 tw-flex tw-justify-between tw-items-center"
                    style="border-right: 2px solid #E9EBEC; padding: 10px;">
                    @if ($sale->type != 'sales_order')
                        <div style="font-size: 16px;">
                            <h4 style="margin: 0;">Packing Slip</h4>
                            <a href="#" class="print-invoice"
                                data-href="{{ route('sell.printInvoice', [$sale->id]) }}?package_slip=true">
                                <h5 style="margin: 0; text-decoration: underline;">
                                    @foreach ($shipment['packages'] as $pack)
                                        {{ $pack['package_id'] }}
                                    @endforeach
                                </h5>
                            </a>
                        </div>
                    @endif
                    <div style="width: 50px; height: 50px; background: #EAF5FB; border-radius: 50%;"></div>
                </div>

                <!-- Label -->
                <div class="col-lg-2 tw-flex tw-justify-between tw-items-center"
                    style="border-right: 2px solid #E9EBEC; padding: 10px;">
                    <div style="font-size: 16px;">
                        <h4 style="margin: 0;">Label</h4>
                        <h5 style="margin: 0; color: #00AC76;">Purchased</h5>
                    </div>
                    <div
                        style="width: 50px; height: 50px; background: #00AC76; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="glyphicon glyphicon-usd" style="font-size: 32px; color: #fff;"></i>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="col-lg-2 tw-flex tw-justify-between tw-items-center"
                    style="border-right: 2px solid #E9EBEC; padding: 10px;">
                    <div style="font-size: 16px;">
                        <h4 style="margin: 0;">Notifications</h4>
                        <a href="#" class="send-notification" data-id={{ $sale->id }}>
                            <h5 style="margin: 0; text-decoration: underline;">Send</h5>
                        </a>
                    </div>
                    <div
                        style="width: 50px; height: 50px; background: #F7B84B; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-bell-slash" aria-hidden="true" style="font-size: 32px; color: #fff;"></i>
                    </div>
                </div>

                <!-- Order No. -->
                <div class="col-lg-2 tw-flex tw-gap-1 tw-items-center"
                    style="border-right: 2px solid #E9EBEC; padding: 10px; font-size: 16px;">
                    <h4 style="color: rgb(5, 5, 122); margin: 0;"><b>Order No.</b></h4>
                    <h4 style="margin: 0;"># {{ $sale->invoice_no ?? '_ _ _ _ _' }}</h4>
                </div>

                <!-- Ship By -->
                <div class="col-lg-2 tw-flex tw-gap-1 tw-items-center" style="padding: 10px; font-size: 16px;">
                    <h4 style="color: rgb(5, 5, 122); margin: 0;"><b>Ship By :</b></h4>
                    <h4 style="margin: 0;">{{ $wareHouse->name ?? '_ _ _ _ _' }}</h4>
                </div>
            </div>

            <hr style="background-color:  #E9EBEC; height: 1px; border: none;">
            <div class="row tw-p-2">
                @php
                    $profileName = '_____';
                    $profilebusinessName = '_____';
                    if ($sale->shipping_first_name || $sale->shipping_last_name) {
                        $profileName = $sale->shipping_first_name . ' ' . $sale->shipping_last_name;
                        $profilebusinessName = $sale->shipping_company;
                    }
                @endphp
                <div class="col-lg-2">
                    <h4 class="tw-align-center" style="color: rgb(5, 5, 122)"><b>Ship To</b> </h4>
                </div>
                <div class="col-lg-10 row "
                    style="border: 2px solid #17A7E4; background: #eef9ff;  border-radius: 9.11px; margin: auto;">
                    <div class=" col-lg-4 tw-flex tw-gap-2">
                        <h4><span
                                style="background:#1F30B7; color: white; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%;"><i
                                    class="glyphicon glyphicon-map-marker"></i></span></h4>

                        <div>
                            <h4>
                                {{ $profilebusinessName ?? '  _ _ _ _' }} </h4>
                            <h4>Ac.No : {{ $user->contact_id ?? ' _ _ _ _' }}</h4>
                        </div>

                    </div>
                    <div class="col-lg-4 tw-flex tw-gap-2">
                        <h4><span
                                style="background:#1F30B7; color: white; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%;"><i
                                    class="glyphicon glyphicon-map-marker"></i></span>
                        </h4>
                        <div>
                            <h4> {{ $profileName }}</h4>
                            @if ($sale->shipping_address1)
                                <h4>{{ $sale->shipping_address1 }},{{ $sale->shipping_city }},
                                    {{ $sale->shipping_state }},{{ $sale->shipping_zip }},{{ $sale->shipping_country }}</h4>
                            @endif
                        </div>

                    </div>
                    <div class=" col-lg-4">
                        <h4><span
                                style="background:#1F30B7; color: white; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%;"><i
                                    class="fa fa-phone" aria-hidden="true"></i></span>
                            Ph :
                            @if ($user->mobile == '')
                                _ _ _ _
                            @else
                                {{ $user->mobile }}
                            @endif
                        </h4>
                        <h4><span
                                style="background:#1F30B7; color: white; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%;"><i
                                    class="fa fa-envelope" aria-hidden="true"></i></span>
                            Email:
                            {{ $user->email ?? ' _ _ _ _' }}</h4>
                    </div>
                </div>
            </div>
            <hr style="background-color:  #E9EBEC; height: 1px; border: none;">
            @if ($shipment['shipment_details']['service_code']=="Manual Shipment")

            <div class="tw-flex-1"
                    style="border: 2px solid #17A7E4; border-radius: 12px; box-shadow: 0px 16px 29.5px 0px #00000012;">
                    <div class="gradiantDiv tw-flex tw-justify-between tw-p-2"
                        style="border-top-left-radius: 10px; border-top-right-radius: 10px">
                        <h4 style="color:whitesmoke;"><b>Shipment</b></h4>
                    </div>
                    <div style="padding:20px">
                        <div class="row">
                            <div class="col-md-2" style="font-size: 16px;">Shipment Date</div>
                            <div class="col-md-4" style="font-size: 16px;">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        {!! Form::text('shipment_date', @format_date('now'), ['class' => 'form-control', 'required', 'readonly']) !!}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-2" style="font-size: 16px;">Ship From</div>
                            <div class="col-md-4" style="font-size: 16px;">
                                <div class="form-group">
                                    {{ $wareHouse->name }}
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-2" style="font-size: 16px;">Weight (lb)</div>
                            <div class="col-md-2" style="font-size: 16px;">
                                <div class="form-group">
                                    <input type="number" name="weight_lb" class="form-control" value="" placeholder="0" required readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-2" style="font-size: 16px;">Weight (oz)</div>
                            <div class="col-md-2" style="font-size: 16px;">
                                <div class="form-group">
                                    <input type="number" name="weight_OZ" class="form-control" value="" placeholder="0" required readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-2" style="font-size: 16px;">Length</div>
                            <div class="col-md-2" style="font-size: 16px;">
                                <div class="form-group">
                                    <input type="number" name="length" class="form-control" value="" placeholder="0" required readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-2" style="font-size: 16px;">Width</div>
                            <div class="col-md-2" style="font-size: 16px;">
                                <div class="form-group">
                                    <input type="number" name="width" class="form-control" value="" placeholder="0" required readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-2" style="font-size: 16px;">Height</div>
                            <div class="col-md-2" style="font-size: 16px;">
                                <div class="form-group">
                                    <input type="number" name="height" class="form-control" value="" placeholder="0" required readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            @else
                <div class="tw-flex tw-flex-col lg:tw-flex-row tw-gap-2"
                style="min-height: 40vh; width:100%; justify-content: space-between;">
                <!-- Rates Section -->
                <div class="tw-flex-1 "
                    style="border: 2px solid #17A7E4; border-radius: 12px; box-shadow: 0px 16px 29.5px 0px #00000012;">
                    <div class="gradiantDiv tw-flex tw-justify-between tw-p-3"
                        style="border-top-left-radius: 10px; border-top-right-radius: 10px">
                        <h4 style="color: whitesmoke "><b>Rates</b></h4>
                    </div>
                    <div id="rate-details" class="rate-details ">
                    </div>
                </div>
                <div class="tw-flex-1"
                    style="border: 2px solid #17A7E4; border-radius: 12px; box-shadow: 0px 16px 29.5px 0px #00000012;">
                    <div class="gradiantDiv tw-flex tw-justify-between tw-p-2"
                        style="border-top-left-radius: 10px; border-top-right-radius: 10px">
                        <h4 style="color:whitesmoke;"><b>Shipment</b></h4>

                        <button class="tw-dw-btn tw-text-white" id="apply_rates"
                            style="background:transparent; border: none;">Apply
                            Preset</button>

                    </div>
                    <div style="padding:20px">

                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-3" style="font-size: 16px;">Shipment Date</div>
                            <div class="col-lg-4" style="font-size: 16px;">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        {!! Form::text('shipment_date', @format_date('now'), ['class' => 'form-control', 'required', 'readonly']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-3" style="font-size: 16px;">Ship From</div>
                            <div class="col-lg-4" style="font-size: 16px;">
                                <div class="form-group">
                                    {{ $wareHouse->name }}
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-3" style="font-size: 16px;">Weight</div>
                            <div class="col-md-6" style="font-size: 16px;">
                                <div class="tw-flex tw-gap-2">
                                    <input type="number" name="weight_lb" class="form-control" value=""
                                        placeholder="0" required readonly>
                                    <p>(lb)</p>
                                    <input type="number" name="weight_OZ" class="form-control" value=""
                                        placeholder="0" required readonly>
                                    <p>(oz)</p>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-3" style="font-size: 16px;">Service</div>
                            <div class="col-md-8" style="font-size: 16px;">
                                <div class="form-group">
                                    <select id="service_select" class="form-control " readonly>
                                        <option value="usps_parcel_select_ground">UPSC Parcel Select Ground
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-3" style="font-size: 16px;">Package</div>
                            <div class="col-md-8" style="font-size: 16px;">
                                <div class="form-group">
                                    <select id="package_select" class="form-control" readonly>
                                        <option value="package">Package</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-3" style="font-size: 16px;">Size</div>
                            <div class="col-md-8" style="font-size: 16px;">
                                <div class="tw-flex tw-gap-2">
                                    <input type="number" name="length" class="form-control" value=""
                                        placeholder="0" required readonly>
                                    <p>L</p>
                                    <input type="number" name="width" class="form-control" value=""
                                        placeholder="0" required readonly>
                                    <P>W</P>
                                    <input type="number" name="height" class="form-control" value=""
                                        placeholder="0" required readonly>
                                    <p>H</p>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-3" style="font-size: 16px;">Rate</div>
                            <div class="col-md-3" style="font-size: 16px;" id="delivery_cost">_ _ _ _</div>
                            <div class="co-md-1 " style="font-size: 16px; color: green; cursor: pointer;"
                                id="cost_review">Cost Review
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-3" style="font-size: 16px;">Delivery</div>
                            <div class="col-md-6" style="font-size: 16px;" id="delivery_date">_ _ _ _</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="modal-footer ">
                <div class="tw-flex tw-justify-between">
                    <button id="void_label_button" class=" tw-px-4 tw-py-2 tw-dw-btn tw-text-white "
                        style="background: #4822BB; border:none" id="void_label_button">
                        Void Label
                    </button>
                    <button type="button" class="tw-dw-btn tw-px-4 tw-py-2 tw-text-white "
                        style="background: green ; border:none" id="printLabel">Print Label</button>
                </div>

            </div>

        </div>
        {{-- {{ $sale}} --}}
    </div>

    <script>
        $(document).ready(function() {
            var shipment = @json($shipment);
            var warehouse = @json($wareHouse);
            var sales = @json($sale);
            var user = @json($user);

            var labelPDF = shipment.shipment_details.label_download.pdf ? shipment.shipment_details.label_download
                .pdf : shipment.shipment_details.label_download;

            var weightInGrams = shipment.packages[0].weight.value;

            var weightInLbs = Math.floor(weightInGrams / 453.592);
            var weightInOz = ((weightInGrams % 453.592) / 28.35).toFixed(2);

            // Parse serviceList from warehouse if available
            let serviceList = [];
            if (warehouse && warehouse.serviceList) {
                try {
                    serviceList = typeof warehouse.serviceList === 'string' 
                        ? JSON.parse(warehouse.serviceList) 
                        : warehouse.serviceList;
                } catch (e) {
                    // serviceList might already be parsed or invalid
                }
            }

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

            let selectedRate = null;
            $("#rate-details").on("click", ".rate_row", function() {
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
            });

            $('input[name="weight_lb"]').val(weightInLbs)
            $('input[name="weight_OZ"]').val(weightInOz)
            $('input[name="length"]').val(shipment.packages[0].dimensions.length);
            $('input[name="width"]').val(shipment.packages[0].dimensions.width);
            $('input[name="height"]').val(shipment.packages[0].dimensions.length);
            $('#delivery_cost').text(shipment.shipment_details.shipment_cost.amount)

            $('#dropdownButton').dropdown();
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown').removeClass('open');
                }
            });

            $(document).on('click','#void_label_button', function(e) {
                e.preventDefault()
                swal({
                    icon: 'warning',
                    title: 'Confirm Void Label',
                    text: 'Confirm that you wish to void label for 1 shipment. Labels will be submitted to their shipping provider for a refund. Refunds are subject to the specific rules of each shipping provider.',
                    buttons: {
                        cancel: 'Cancel',
                        confirm: 'Yes, Void Label'
                    }
                }).then((willVoid) => {
                    if (willVoid) {
                        $.ajax({
                            url: `/void-shipment/${sales.id}`, // Replace with your endpoint
                            method: 'POST',
                            data: {
                                shipment_id: 123
                            },
                            success: function(response) {
                                swal({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Label successfully voided.',
                                    timer: 2000,
                                    buttons: false
                                });
                                sell_table.ajax.reload();
                                $('#close_button').trigger('click');
                            },
                            error: function() {
                                swal({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to void the label. Please try again.',
                                });
                            }
                        });
                    }
                });

            });
            $('#printLabel').on('click', function() {
                if (labelPDF != '') {
                    $('#printFrame').attr('src', labelPDF);
                    $('#printModal').modal('show');
                } else {
                    swal({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Label is not availeble for this Shipment',
                    });
                };
            });

            $('#print_modal_print').on('click', function() {
                var printUrl = labelPDF;

                var newTab = window.open(printUrl);

                if (newTab) {
                    newTab.onload = function() {
                        newTab.print();
                    };
                } else {
                    alert('Please allow pop-ups to print the label.');
                }
            });


            // Close Modal
            $('#close_modal_print').on('click', function() {
                $('#printModal').modal('hide');
            });

            $("#cost_review").on("click", function() {
                let isValid = true;

                // Select all required input fields
                let requiredFields = [
                    'input[name="shipment_date"]',
                    // '#warehouse_select',
                    'input[name="weight_lb"]',
                    'input[name="weight_OZ"]',
                    '#service_select',
                    '#package_select',
                    'input[name="length"]',
                    'input[name="width"]',
                    'input[name="height"]'
                ];

                // Loop through fields and validate
                $.each(requiredFields, function(index, selector) {
                    let field = $(selector);
                    if (field.val().trim() === "") {
                        isValid = false;
                        field.addClass("border border-danger").css("background-color",
                            "#f8d7da");
                    } else {
                        field.removeClass("border border-danger").css("background-color",
                            "");
                    }
                });

                if (!isValid) {
                    return;
                }
                let weightLb = $('input[name="weight_lb"]').val() || 0;
                let weightOz = $('input[name="weight_OZ"]').val() || 0;
                let weightInGrams = ((parseFloat(weightLb) * 16) + parseFloat(weightOz)) *
                    28.3495; // Convert to grams

                let length = $('input[name="length"]').val();
                let width = $('input[name="width"]').val();
                let height = $('input[name="height"]').val();

                // Get selected package type from dropdown
                let selectedPackageCode = $('#package_select').val();
                // For rate estimation, use package_code as package_type (ShipStation API accepts both)
                // Some carriers like UPS use package_code, while USPS uses package_type
                let packageTypeForRate = selectedPackageCode || "package";

                let data = {
                    "warehouse_id": `${warehouse.id}`,
                    "from_country_code": "US",
                    "from_postal_code": warehouse.postal_code,
                    "from_city_locality": warehouse.city_locality,
                    "from_state_province": warehouse.state_province,
                    "to_country_code": user.country,
                    "to_postal_code": user.zip_code,
                    "to_city_locality": user.city,
                    "to_state_province": user.state,
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
                        alert("Failed to fetch shipping rates. Please try again.");

                    }
                });
            });

            
            
            $("#copyText").click(function() {
                let text = shipment.shipment_details.tracking_number;

                navigator.clipboard.writeText(text).then(function() {
                    swal({
                        title: "Copied!",
                        text: "Text has been copied to clipboard.",
                        icon: "success",
                        timer: 1000, // Dismiss after 1 second
                        buttons: false
                    });
                }).catch(function(err) {
                    swal("Error!", "Failed to copy text.", "error");
                });
            });


            $('.send-notification').on('click', function() {
                let url = "/notification/get-template/" +sales.id + "/shipment";
                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(modalContent) {
                        $('.view_modal').html(
                                modalContent)
                            .modal({
                                backdrop: 'static',
                                keyboard: false
                            });
                    },
                    error: function() {
                        toastr.error(
                            "Failed to load the email template."
                        );
                    }
                });
            })
        });
    </script>
    <div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title tw-text-white" id="modalTitle">Print Preview</h3>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body">
                    <iframe id="printFrame" style="width: 100%; height: 500px; border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <div class="tw-flex tw-justify-between">
                        <button type="button" class="btn btn-primary" id="print_modal_print">Print</button>
                        <button type="button" class="btn btn-secondary tw-text-white" id="close_modal_print"
                            style="background: red">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
