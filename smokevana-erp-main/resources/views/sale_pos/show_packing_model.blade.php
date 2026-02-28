<div class="modal-dialog no-print" role="document" id='modal_shipment_packing'>
    <div class="modal-content">
        <div class="tw-flex tw-justify-between gradiantDiv tw-p-2"
            style="border-top-left-radius: 10px; border-top-right-radius: 10px">
            <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-2">
                <h3 class="tw-text-base sm:tw-text-lg tw-text-white">
                    <b>Confirm Packing Completion</b>
                </h3>
            </div>
            <div class="tw-flex tw-flex-wrap tw-justify-end tw-gap-2 ">
                <button type="button"
                    class="tw-dw-btn tw-dw-btn-neutral tw-text-white tw-px-2 tw-py-1 tw-text-sm tw-w-full sm:tw-w-auto no-print"
                    data-dismiss="modal" id="close_button">
                    @lang('messages.close')
                </button>
            </div>
        </div>
        <!-- OWN Tracking ID Field -->
        <div class="modal-body">
            <input type="hidden" name="transaction_id" value="<?php echo $packingOrder->id; ?>" />
            <div id="own-shipment" class="shipment-fields" style="display: none;">
                <div class="row tw-gap-2">

                    <!-- Order No. -->
                    <div class="col-lg-3 tw-flex tw-gap-1 tw-items-center"
                        style="border-right: 2px solid #E9EBEC; padding: 10px; font-size: 16px;">
                        <h4 style="color: rgb(5, 5, 122); margin: 0;"><b>Order No.</b></h4>
                        <h4 style="margin: 0;">{{ $packingOrder->invoice_no ?? '_ _ _ _ _' }}</h4>
                    </div>

                    <!-- Ship By -->
                    <div class="col-lg-3 tw-flex tw-gap-1 tw-items-center" style="padding: 10px; font-size: 16px;">
                        <h4 style="color: rgb(5, 5, 122); margin: 0;"><b>Ship By :</b></h4>
                        <h4 style="margin: 0;" class="ship_from_name_text">{{ $shipstation[0]->name ?? '_ _ _ _ _' }}
                        </h4>
                    </div>
                </div>
                <style>
                </style>

                <hr style="background-color:  #E9EBEC; height: 1px; border: none;">
                <div class="row tw-p-2">
                    <div class="col-lg-2">
                        <h4 class="tw-align-center" style="color: rgb(5, 5, 122)"><b>Ship To</b> </h4>
                    </div>
                    <div class="col-lg-10 row "
                        style="border: 1px solid #17A7E4; background: #eef9ff;  border-radius: 9.11px; margin: auto;">
                         <i class="fa fa-edit edit-icon edit_show edit_shipping_address"
                                style="position: absolute; top: 1px; right: 1px; cursor: pointer; color: #1F30B7;"></i>
                        <div class=" col-lg-4 tw-flex tw-gap-2">
                            <h4><span
                                    style="background:#1F30B7; color: white; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%;"><i
                                        class="glyphicon glyphicon-map-marker"></i></span></h4>

                            <div>
                                <h4 id='businessName'></h4>
                                <h4>Ac.No : {{ $user->contact_id ?? ' _ _ _ _' }}</h4>
                            </div>

                        </div>
                        <div class="col-lg-4 tw-flex tw-gap-2">
                            <h4><span
                                    style="background:#1F30B7; color: white; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%;"><i
                                        class="glyphicon glyphicon-map-marker"></i></span>
                            </h4>
                            <div>
                                <h4 class='profileName'></h4>
                                <h4 class='fullAddress'></h4>
                            </div>

                        </div>
                        <div class=" col-lg-4">
                            <h4><span
                                    style="background:#1F30B7; color: white; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%;"><i
                                        class="fa fa-phone" aria-hidden="true"></i></span>
                                Ph :
                                <span class="user_phone">
                                </span>

                            </h4>
                            <h4><span
                                    style="background:#1F30B7; color: white; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%;"><i
                                        class="fa fa-envelope" aria-hidden="true"></i></span>
                                Email:
                                <span class='user_email'></span>
                            </h4>
                        </div>
                    </div>
                </div>

                <hr style="background-color:  #E9EBEC; height: 1px; border: none;">


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
                    <!-- Shipment Section -->
                    <div class="tw-flex-1"
                        style="border: 2px solid #17A7E4; border-radius: 12px; box-shadow: 0px 16px 29.5px 0px #00000012;">
                        <div class="gradiantDiv tw-flex tw-justify-between tw-p-2"
                            style="border-top-left-radius: 10px; border-top-right-radius: 10px">
                            <h4 style="color:whitesmoke;"><b>Shipment</b></h4>

                            <button class="tw-dw-btn tw-text-white" id="apply_preset"
                                style="background:transparent; border: none;">Apply
                                Preset</button>

                        </div>
                        <div style="padding: 20px;">

                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Shipment Date</b></div>
                                <div class="col-lg-4 " style="font-size: 16px;">
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

                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Ship From</b></div>
                                <div class="col-lg-4" style="font-size: 16px;">
                                    <div class="form-group">
                                        <select id="warehouse_select" class="form-control warehouse_select">
                                            @foreach ($shipstation as $index => $warehouse)
                                                <option value="{{ $index }}">{{ $warehouse->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Shipping Station</b></div>
                                <div class="col-lg-4" style="font-size: 16px;">
                                    <div class="form-group">
                                        <select id="shipping_station_select" class="form-control shipping_station_select">
                                            <option value="">@lang('messages.please_select')</option>
                                            @if(isset($shippingStations) && $shippingStations->count() > 0)
                                                @foreach ($shippingStations as $station)
                                                    <option value="{{ $station->id }}" {{ ($packingOrder->shipping_station_id == $station->id) ? 'selected' : '' }}>
                                                        {{ $station->name }} @if($station->station_code) ({{ $station->station_code }}) @endif
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Weight</b></div>
                                <div class="col-md-6" style="font-size: 16px;">
                                    <div class="tw-flex tw-gap-2">
                                        <input type="number" name="weight_lb" class="form-control" value="0"
                                            placeholder="0" required>
                                        <p style="margin: 0;">(lb)</p>
                                        <input type="number" name="weight_OZ" class="form-control" value="0"
                                            placeholder="0" required>
                                        <p style="margin: 0;">(oz)</p>
                                    </div>

                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Service</b></div>
                                <div class="col-md-8" style="font-size: 16px;">
                                    <div class="form-group">
                                        <select id="service_select" class="form-control">
                                            <option value=""><b>Select Service</b></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Package</b></div>
                                <div class="col-md-8 " style="font-size: 16px;">
                                    <div class="form-group">
                                        <select id="package_select" class="form-control">
                                            <option value=""><b>Select Package</b></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Size</b></div>
                                <div class="col-md-8 " style="font-size: 16px;">
                                    <div class="tw-flex tw-gap-2">
                                        <input type="number" name="length" class="form-control" placeholder="0"
                                            required>
                                        <p style="margin: 0;">L</p>
                                        <input type="number" name="width" class="form-control" placeholder="0" required>
                                        <p style="margin: 0;">W</p>
                                        <input type="number" name="height" class="form-control" placeholder="0"
                                            required>
                                        <p style="margin: 0;">H</p>
                                    </div>

                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Rate</b></div>
                                <div class="col-md-2" style="font-size: 16px;" id="delivery_cost">_ _ _ _</div>
                                <div class="col-md-3" style="font-size: 16px; color: green; cursor: pointer;"
                                    id="cost_review"><b>Cost Review</b></div>
                            </div>

                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Delivery</b></div>
                                <div class="col-md-6" style="font-size: 16px;" id="delivery_date">_ _ _ _</div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="modal-footer ">
                    <div class="tw-flex tw-justify-between">
                        <button id="manual_ship" class=" tw-px-4 tw-py-2 tw-dw-btn tw-text-white "
                            style="background: #4822BB; border:none">
                            Manual Ship
                        </button>
                        <button type="button" class="tw-dw-btn tw-px-4 tw-py-2 tw-text-white "
                            style="background: green ; border:none" id='submit_selection_button'>Create+Print
                            Label</button>
                    </div>

                </div>
            </div>
            <div id="manual-shipment" class="shipment-fields" style="display: none;">
                <div class="row tw-gap-2">

                    <!-- Order No. -->
                    <div class="col-lg-3 tw-flex tw-gap-1 tw-items-center"
                        style="border-right: 2px solid #E9EBEC; padding: 10px; font-size: 16px;">
                        <h4 style="color: rgb(5, 5, 122); margin: 0;"><b>Order No.</b></h4>
                        <h4 style="margin: 0;">{{ $packingOrder->invoice_no ?? '_ _ _ _ _' }}</h4>
                    </div>

                    <!-- Ship By -->
                    <div class="col-lg-3 tw-flex tw-gap-1 tw-items-center" style="padding: 10px; font-size: 16px;">
                        <h4 style="color: rgb(5, 5, 122); margin: 0;"><b>Ship By :</b></h4>
                        <h4 style="margin: 0;" class="ship_from_name_text">{{ $shipstation[0]->name ?? '_ _ _ _ _' }}
                        </h4>
                    </div>
                </div>

                <hr style="background-color:  #E9EBEC; height: 1px; border: none;">
                <div class="row tw-p-2">
                    <div class="col-lg-2">
                        <h4 class="tw-align-center" style="color: rgb(5, 5, 122)"><b>Ship To</b> </h4>
                    </div>
                    <div class="col-lg-10 row "
                        style="border: 2px solid #17A7E4; background: #eef9ff;  border-radius: 9.11px; margin: auto;">
                        <i class="fa fa-edit edit-icon edit_show edit_shipping_address" 
                                style="position: absolute; top: 1px; right: 1px; cursor: pointer; color: #1F30B7;"></i>
                        <div class=" col-lg-4 tw-flex tw-gap-2">
                            <h4><span
                                    style="background:#1F30B7; color: white; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%;"><i
                                        class="glyphicon glyphicon-map-marker"></i></span></h4>

                            <div>
                                <h4 id='businessName'></h4>
                                <h4>Ac.No : {{ $user->contact_id ?? ' _ _ _ _' }}</h4>
                            </div>

                        </div>
                        <div class="col-lg-4 tw-flex tw-gap-2">
                            <h4><span
                                    style="background:#1F30B7; color: white; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%;"><i
                                        class="glyphicon glyphicon-map-marker"></i></span>
                            </h4>
                            <div>
                                <h4 class='profileName'></h4>
                                <h4 class='fullAddress'></h4>
                            </div>

                        </div>
                        <div class=" col-lg-4">
                            <h4><span
                                    style="background:#1F30B7; color: white; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%;"><i
                                        class="fa fa-phone" aria-hidden="true"></i></span>
                                Ph :
                                <span class="user_phone">
                                </span>

                            </h4>
                            <h4><span
                                    style="background:#1F30B7; color: white; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%;"><i
                                        class="fa fa-envelope" aria-hidden="true"></i></span>
                                Email:
                                <span class='user_email'></span>
                            </h4>
                        </div>
                    </div>
                </div>

                <hr style="background-color:  #E9EBEC; height: 1px; border: none;">


                <div class="tw-flex tw-flex-col lg:tw-flex-row tw-gap-2"
                    style="min-height: 30vh; width:100%; justify-content: space-between;">
                    <!-- Rates Section -->
                    {{-- <div class="tw-flex-1 "
                        style="border: 2px solid #17A7E4; border-radius: 12px; box-shadow: 0px 16px 29.5px 0px #00000012;">
                        <div class="gradiantDiv tw-flex tw-justify-between tw-p-3"
                            style="border-top-left-radius: 10px; border-top-right-radius: 10px">
                            <h4 style="color: whitesmoke "><b>Rates</b></h4>
                        </div>
                        <div id="rate-details" class="rate-details ">
                        </div>
                    </div> --}}
                    <!-- Shipment Section -->
                    <div class="tw-flex-1"
                        style="border: 2px solid #17A7E4; border-radius: 12px; box-shadow: 0px 16px 29.5px 0px #00000012;">
                        <div class="gradiantDiv tw-flex tw-justify-between tw-p-2"
                            style="border-top-left-radius: 10px; border-top-right-radius: 10px">
                            <h4 style="color:whitesmoke;"><b>Shipment</b></h4>

                            <button class="tw-dw-btn tw-text-white" id="apply_preset"
                                style="background:transparent; border: none;">Apply
                                Preset</button>

                        </div>
                        <div style="padding: 20px;">

                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Shipment Date</b></div>
                                <div class="col-lg-4 " style="font-size: 16px;">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            {!! Form::text('shipment_datem', @format_date('now'), ['class' => 'form-control', 'required', 'readonly']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Ship From</b></div>
                                <div class="col-lg-4" style="font-size: 16px;">
                                    <div class="form-group">
                                        <select id="warehouse_selectm" class="form-control warehouse_select">
                                            @foreach ($shipstation as $index => $warehouse)
                                                <option value="{{ $index }}">{{ $warehouse->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Shipping Station</b></div>
                                <div class="col-lg-4" style="font-size: 16px;">
                                    <div class="form-group">
                                        <select id="shipping_station_select" class="form-control shipping_station_select">
                                            <option value="">@lang('messages.please_select')</option>
                                            @if(isset($shippingStations) && $shippingStations->count() > 0)
                                                @foreach ($shippingStations as $station)
                                                    <option value="{{ $station->id }}" {{ ($packingOrder->shipping_station_id == $station->id) ? 'selected' : '' }}>
                                                        {{ $station->name }} @if($station->station_code) ({{ $station->station_code }}) @endif
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Weight</b></div>
                                <div class="col-md-6" style="font-size: 16px;">
                                    <div class="tw-flex tw-gap-2">
                                        <input type="number" name="weight_lbm" class="form-control" value="0"
                                            placeholder="0" required>
                                        <p style="margin: 0;">(lb)</p>
                                        <input type="number" name="weight_OZm" class="form-control" value="0"
                                            placeholder="0" required>
                                        <p style="margin: 0;">(oz)</p>
                                    </div>

                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Size</b></div>
                                <div class="col-md-8 " style="font-size: 16px;">
                                    <div class="tw-flex tw-gap-2">
                                        <input type="number" name="lengthm" class="form-control" placeholder="0"
                                            required>
                                        <p style="margin: 0;">L</p>
                                        <input type="number" name="widthm" class="form-control" placeholder="0"
                                            required>
                                        <p style="margin: 0;">W</p>
                                        <input type="number" name="heightm" class="form-control" placeholder="0"
                                            required>
                                        <p style="margin: 0;">H</p>
                                    </div>

                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-md-3" style="font-size: 16px;"><b>Shipping Charges</b></div>
                                <div class="col-md-6" style="font-size: 16px;"><input type="number"
                                        name="shipping_chargesm" class="form-control" value="0" placeholder="0"
                                        required></div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer ">
                    <div class="tw-flex tw-justify-between">
                        <button id="auto_ship" class=" tw-px-4 tw-py-2 tw-dw-btn tw-text-white "
                            style="background: #4822BB; border:none">
                            Auto Ship
                        </button>
                        <button type="button" class="tw-dw-btn tw-px-4 tw-py-2 tw-text-white "
                            style="background: green ; border:none" id='submit_manual_shipment_button'>Create+Print
                            Label</button>
                    </div>

                </div>
            </div>
            <div class="clearfix"></div>

            <div id="shipment-details" class="tw-bg-white tw-p-6 tw-rounded-lg tw-shadow-lg tw-max-w-md tw-mx-auto">
                <label for="shipment_type" class="tw-block tw-text-xl tw-font-bold tw-text-center tw-mb-4">Shipment
                    Type</label>

                <div class="form-group tw-flex tw-justify-center tw-gap-6">
                    <div class="form-check tw-flex tw-items-center tw-gap-2">
                        <input class="form-check-input tw-w-5 tw-h-5" type="radio" name="shipment_type" id="pickup"
                            value="pickup" checked>
                        <label class="form-check-label tw-text-lg" for="pickup">Local Pickup</label>
                    </div>
                    <div class="form-check tw-flex tw-items-center tw-gap-2">
                        <input class="form-check-input tw-w-5 tw-h-5" type="radio" name="shipment_type" id="own"
                            value="own">
                        <label class="form-check-label tw-text-lg" for="own">Shipment</label>
                    </div>
                </div>

                <div class="tw-flex tw-justify-center tw-mt-6">
                    <button type="button" id="continue_button"
                        class="btn btn-success tw-w-full tw-py-2 tw-font-bold tw-text-lg">Continue</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="AddressModal" tabindex="-1" role="dialog" aria-labelledby="AddressModalLabel">
        <div class="modal-dialog" style="width:60%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="AddressModalLabel">Edit Address</h4>
                    <div class="tw-flex tw-justify-end tw-gap-2" style="margin-top: -35px">
                        <button type="button" class="btn btn-primary" id="saveAddress">Save changes</button>
                        <button type="button" class="btn btn-danger close_address">Close</button>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="AddressForm">
                        <div class="row">
                            <div class="form-group col-md-4">
                                {!! Form::label('company', 'Company Name' . ':') !!}
                                {!! Form::text('company', null, ['class' => 'form-control', 'required' => true]) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('first_name', 'First name' . ':') !!}
                                {!! Form::text('first_name', null, ['class' => 'form-control', 'required' => true]) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('last_name', 'Last Name' . ':') !!}
                                {!! Form::text('last_name', null, ['class' => 'form-control', 'required' => true]) !!}
                            </div>
                        </div>
                        <x-address-autocomplete addressInput="address_1" cityInput="city_locality"
                            stateInput="state_province" stateFormat="short_name" zipInput="postal_code"
                            countryInput="country_code" countryFormat="short_name" />
                        <div class="row">
                            <div class="form-group col-md-12">
                                {!! Form::label('address_1', 'Address 1' . ':') !!}
                                {!! Form::text('address_1', null, ['class' => 'form-control', 'required' => true]) !!}
                            </div>
                            <div class="form-group col-md-8">
                                {!! Form::label('address_2', 'Address 2' . ':') !!}
                                {!! Form::text('address_2', null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('city_locality', 'City/Locality' . ':') !!}
                                {!! Form::text('city_locality', null, ['class' => 'form-control', 'required' => true]) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('state_province', 'State/Province' . ':') !!}
                                {!! Form::text('state_province', null, ['class' => 'form-control', 'required' => true]) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('postal_code', 'Postal Code' . ':') !!}
                                {!! Form::text('postal_code', null, ['class' => 'form-control', 'required' => true]) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('country_code', 'Country Code' . ':') !!}
                                {!! Form::text('country_code', null, ['class' => 'form-control', 'required' => true]) !!}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('sale_pos.partials.packing_modal_javascript')