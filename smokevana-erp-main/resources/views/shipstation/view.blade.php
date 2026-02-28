<div class="modal-dialog" role="document" >
    <div class="modal-content">
        {!! Form::open([
            'url' => action([\App\Http\Controllers\ShipStationController::class, 'storeServices']),
            'method' => 'post',
            'id' => 'shipstation_service_add_form',
        ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Available Carrier Services: </h4>
        </div>
        <style>
            #carrier-container .carrier-packages .select2-container {
                width: 100% !important;
            }

            #carrier-container .carrier-services .select2-container {

                width: 100% !important;
            }
        </style>
        <div class="modal-body">
            <input type="hidden" name="id" value="{{ $shpstation->id }}">
            <div class="form-group">
                @if (!empty($carriers))
                    {!! Form::label('carrier', 'Select Default Carrier:*') !!}
                    <div id="carrier-container">
                        @foreach ($carriers as $carrier)
                            @php
                                $isSelectedCarrier = collect($shipmentList)->contains(
                                    'carrier_id',
                                    $carrier['carrier_id'],
                                );
                                $selectedServices = collect($shipmentList)
                                    ->where('carrier_id', $carrier['carrier_id'])
                                    ->pluck('services')
                                    ->flatten(1)
                                    ->pluck('service_code')
                                    ->toArray();
                                $selectedPackages = collect($shipmentList)
                                    ->where('carrier_id', $carrier['carrier_id'])
                                    ->pluck('packages')
                                    ->flatten(1)
                                    ->pluck('package_code')
                                    ->toArray();
                            @endphp

                            {{-- Carrier Checkbox --}}

                            <div class="form-check">
                                {!! Form::checkbox('carriers[]', $carrier['carrier_id'], $isSelectedCarrier, [
                                    'class' => 'form-check-input carrier-checkbox',
                                    'id' => 'carrier_' . $carrier['carrier_id'],
                                ]) !!}
                                {!! Form::label('carrier_' . $carrier['carrier_id'], $carrier['friendly_name'], ['class' => 'form-check-label']) !!}
                            </div>

                            {{-- Services & Packages (Initially Hidden) --}}
                            <div class="carrier-details w-100 mt-2" id="details_{{ $carrier['carrier_id'] }}"
                                style="{{ $isSelectedCarrier ? 'display: block;' : 'display: none;' }} margin-left: 20px;">

                                {{-- Carrier Services --}}
                                <strong>Services</strong>
                                <div class="carrier-services">
                                    {!! Form::select(
                                        'carrier_services[' . $carrier['carrier_id'] . '][]',
                                        collect($carrier['services'])->mapWithKeys(fn($service) => [$service['service_code'] => $service['name']]),
                                        $selectedServices, // Auto-fill selected services
                                        ['class' => 'form-control w-100 select2', 'multiple' => 'multiple'],
                                    ) !!}
                                </div>

                                {{-- Carrier Packages --}}
                                <strong>Packages</strong>
                                <div class="carrier-packages">
                                    {!! Form::select(
                                        'carrier_packages[' . $carrier['carrier_id'] . '][]',
                                        collect($carrier['packages'])->mapWithKeys(fn($package) => [$package['package_code'] => $package['name']]),
                                        $selectedPackages, // Auto-fill selected packages
                                        ['class' => 'form-control w-100 select2', 'multiple' => 'multiple'],
                                    ) !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>No carriers available.</p>
                @endif


            </div>
            <p style="font-size: 15px; padding: 7px 0px;"><strong>Warehouse Name:</strong> {{ $shpstation->name }}</p>
            <p style="font-size: 15px; padding: 7px 0px;"><strong>Warehouse Address:</strong>
                {{ $shpstation->address_1 }}, {{ $shpstation->city_locality }}, {{ $shpstation->state_province }}
                {{ $shpstation->postal_code }}, {{ $shpstation->country_code }}</p>
            <p style="font-size: 15px; padding: 7px 0px;"><strong>Contact Name:</strong> {{ $shpstation->contact_name }}
            </p>
            <p style="font-size: 15px; padding: 7px 0px;"><strong>Phone:</strong> {{ $shpstation->phone }}</p>
            <p style="font-size: 15px; padding: 7px 0px;"><strong>Company Name:</strong>
                {{ $shpstation->company_name }}</p>
            <p style="font-size: 15px; padding: 7px 0px;"><strong>API:</strong> {{ $shpstation->api }}</p>
            <p style="font-size: 15px; padding: 7px 0px;"><strong>Priority:</strong> {{ $shpstation->priority }}</p>
            <p style="font-size: 15px; padding: 7px 0px;"><strong>Business ID:</strong> {{ $shpstation->business_id }}
            </p>
            <p style="font-size: 15px; padding: 7px 0px;"><strong>Location ID:</strong> {{ $shpstation->location_id }}
            </p>
            <p style="font-size: 15px; padding: 7px 0px;"><strong>Usable:</strong>
                {{ $shpstation->usable ? 'Yes' : 'No' }}</p>
            <div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.save')</button>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white"
                data-dismiss="modal">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
