<div class="modal-dialog shipstation-form-modal" role="document" style="max-width: 700px; width: 95%;">
    <div class="modal-content">
    <style>
        /* === ShipStation Add Modal - Amazon theme === */
        .shipstation-form-modal { box-sizing: border-box; }
        .shipstation-form-modal .modal-content { border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 24px rgba(0,0,0,0.2); }
        .shipstation-form-modal .modal-header {
            background: #37475a;
            color: #fff;
            padding: 1rem 1.25rem;
            border-bottom: 2px solid #FF9900;
            flex-shrink: 0;
        }
        .shipstation-form-modal .modal-header .modal-title { font-size: 1.25rem; font-weight: 600; margin: 0; }
        .shipstation-form-modal .modal-header .close { color: #fff; opacity: 0.9; text-shadow: none; margin-top: -0.25rem; }
        .shipstation-form-modal .modal-header .close:hover { color: #FF9900; }
        .shipstation-form-modal .modal-body {
            background: #EAEDED;
            padding: 1rem 1.25rem;
            max-height: min(85vh, 600px);
            overflow-y: auto;
        }
        .shipstation-form-modal .modal-footer {
            background: #37475a;
            border-top: 1px solid rgba(255,255,255,0.15);
            padding: 0.75rem 1.25rem;
        }

        /* Cards - white sections */
        .shipstation-form-modal .shipstation-card {
            background: #fff;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #D5D9D9;
        }
        .shipstation-form-modal .shipstation-card-title {
            font-size: 0.9375rem;
            font-weight: 600;
            color: #232F3E;
            margin: 0 0 0.75rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #D5D9D9;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .shipstation-form-modal .shipstation-card-title i { color: #FF9900; }

        /* Form controls */
        .shipstation-form-modal .shipstation-card .form-group { margin-bottom: 0.75rem; }
        .shipstation-form-modal .shipstation-card .form-group:last-child { margin-bottom: 0; }
        .shipstation-form-modal .shipstation-card label { color: #0F1111 !important; font-size: 0.8125rem; }
        .shipstation-form-modal .shipstation-card .form-control {
            background: #fff;
            border: 1px solid #D5D9D9;
            color: #0F1111;
            font-size: 0.8125rem;
            padding: 0.375rem 0.5rem;
            min-height: 2.25rem;
        }
        .shipstation-form-modal .shipstation-card .form-control:focus {
            border-color: #FF9900;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
        }
        .shipstation-form-modal .shipstation-card .input-group-addon {
            background: #F7F8F8;
            color: #232F3E;
            border-color: #D5D9D9;
        }
        .shipstation-form-modal .shipstation-card input[type="checkbox"] { accent-color: #FF9900; }

        /* Row layout for side-by-side fields */
        .shipstation-form-modal .shipstation-card .row { margin-left: -0.375rem; margin-right: -0.375rem; }
        .shipstation-form-modal .shipstation-card .row > [class*="col-"] { padding-left: 0.375rem; padding-right: 0.375rem; }

        /* Buttons - Amazon orange */
        .shipstation-form-modal .modal-footer .btn-primary,
        .shipstation-form-modal .modal-footer .tw-dw-btn-primary {
            background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
            border-color: #C7511F !important;
            color: #fff !important;
            font-weight: 500;
            padding: 0.375rem 1rem;
        }
        .shipstation-form-modal .modal-footer .btn-default,
        .shipstation-form-modal .modal-footer .tw-dw-btn-neutral {
            background: transparent !important;
            border: 1px solid rgba(255,255,255,0.6) !important;
            color: #fff !important;
        }
        .shipstation-form-modal .modal-footer .btn-default:hover,
        .shipstation-form-modal .modal-footer .tw-dw-btn-neutral:hover {
            background: rgba(255,255,255,0.1) !important;
            color: #fff !important;
        }
    </style>

        {!! Form::open([
            'url' => action([\App\Http\Controllers\ShipStationController::class, 'store']),
            'method' => 'post',
            'id' => 'shipstation_add_form',
        ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">ShipStation API (V2)</h4>
        </div>

        <div class="modal-body">
            <x-address-autocomplete addressInput="address_1" cityInput="city_locality" stateInput="state_province"
                stateFormat="short_name" zipInput="postal_code" countryInput="country_code"
                countryFormat="short_name" />

            <!-- Card: API & Warehouse -->
            <div class="shipstation-card">
                <h5 class="shipstation-card-title"><i class="fa fa-key"></i> API & Warehouse</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('warehouse', 'Warehouse' . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-warehouse"></i></span>
                                {!! Form::select(
                                    'warehouse',
                                    [
                                        'Warehouse-1' => 'Warehouse-1',
                                        'Warehouse-2' => 'Warehouse-2',
                                        'Warehouse-3' => 'Warehouse-3',
                                    ],
                                    null,
                                    ['class' => 'form-control', 'id' => 'tax_type'],
                                ) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('value', 'Your Api-Key (V2)' . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                {!! Form::text('value', null, ['class' => 'form-control', 'required', 'step' => 'any']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('priority', 'Priority' . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-sort-numeric-down"></i></span>
                                {!! Form::number('priority', null, ['class' => 'form-control', 'required', 'step' => 'any']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="margin-top: 1.75rem;">
                            <label>
                                {!! Form::checkbox('usable', 1, true); !!} Usable
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Contact Information -->
            <div class="shipstation-card">
                <h5 class="shipstation-card-title"><i class="fa fa-user"></i> Contact Information</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('contact_name', 'Contact Name' . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                {!! Form::text('contact_name', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('phone', 'Phone' . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                {!! Form::text('phone', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('company_name', 'Company Name' . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-briefcase"></i></span>
                                {!! Form::text('company_name', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Address -->
            <div class="shipstation-card">
                <h5 class="shipstation-card-title"><i class="fa fa-map-marker"></i> Address</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('address_1', 'Address 1' . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                                {!! Form::text('address_1', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('city_locality', 'City/Locality' . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                                {!! Form::text('city_locality', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('state_province', 'State/Province' . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                                {!! Form::text('state_province', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('postal_code', 'Postal Code' . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-map-pin"></i></span>
                                {!! Form::text('postal_code', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('country_code', 'Country Code' . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-globe"></i></span>
                                {!! Form::text('country_code', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.save')</button>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}
    </div>
</div>
