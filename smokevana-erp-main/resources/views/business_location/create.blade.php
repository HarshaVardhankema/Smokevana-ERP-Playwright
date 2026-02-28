<div class="modal-dialog location-form-modal modal-lg" role="document">
    <div class="modal-content">
    <style>
    /* === Add Business Location – Amazon theme (card layout, ref: Add Customer modal) === */
    .location-form-modal { box-sizing: border-box; }
    .location-form-modal .modal-content { border-radius: 10px; overflow: hidden; border: none; box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
    .location-form-modal .modal-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
        color: #fff;
        padding: 1rem 1.25rem;
        border-bottom: 3px solid #ff9900;
    }
    .location-form-modal .modal-header .modal-title { font-size: 1.25rem; font-weight: 600; margin: 0; }
    .location-form-modal .modal-header .close { color: #fff; opacity: 0.9; text-shadow: none; }
    .location-form-modal .modal-header .close:hover { color: #ff9900; opacity: 1; }
    .location-form-modal .modal-body {
        background: #37475a;
        padding: 1rem 1.25rem;
        max-height: min(85vh, 720px);
        overflow-y: auto;
    }
    .location-form-modal .modal-footer {
        background: #f0f2f2;
        border-top: 1px solid #D5D9D9;
        padding: 0.75rem 1.25rem;
    }

    /* Cards – white on dark, orange icon + title */
    .location-form-modal .location-create-card {
        background: #fff;
        border-radius: 8px;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .location-form-modal .location-create-card-title {
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
    .location-form-modal .location-create-card-title i { color: #FF9900; }

    .location-form-modal .location-create-card .form-group { margin-bottom: 0.75rem; }
    .location-form-modal .location-create-card .form-group:last-child,
    .location-form-modal .location-create-card .row:last-child .form-group { margin-bottom: 0; }
    .location-form-modal .location-create-card label,
    .location-form-modal .location-create-card .control-label { color: #0F1111 !important; font-size: 0.8125rem; }
    .location-form-modal .location-create-card .form-control {
        background: #fff;
        border: 1px solid #D5D9D9;
        color: #0F1111;
        font-size: 0.8125rem;
        padding: 0.375rem 0.5rem;
        min-height: 2rem;
        box-sizing: border-box;
    }
    .location-form-modal .location-create-card .form-control:focus {
        border-color: #FF9900;
        outline: none;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
    }
    .location-form-modal .location-create-card input[type="checkbox"] { accent-color: #FF9900; }
    .location-form-modal .location-create-card .row { margin-left: -0.375rem; margin-right: -0.375rem; }
    .location-form-modal .location-create-card .row > [class*="col-"] { padding-left: 0.375rem; padding-right: 0.375rem; }

    /* Payment options table – dark header, row highlight */
    .location-form-modal .location-create-card .table {
        margin-bottom: 0;
        border-radius: 6px;
        overflow: hidden;
    }
    .location-form-modal .location-create-card .table thead th {
        background: linear-gradient(to bottom, #232f3e 0%, #1a252f 100%) !important;
        color: #fff !important;
        border-color: #37475a !important;
        padding: 10px 12px !important;
        font-weight: 600;
        font-size: 13px;
    }
    .location-form-modal .location-create-card .table tbody td {
        padding: 10px 12px;
        border-color: #e5e7eb;
        font-size: 13px;
    }
    .location-form-modal .location-create-card .table tbody tr:hover td {
        background: #fff8e7 !important;
    }
    .location-form-modal .location-create-card .table .form-control.input-sm {
        min-height: 1.75rem;
        padding: 0.25rem 0.5rem;
        font-size: 12px;
    }

    /* Footer buttons */
    .location-form-modal .modal-footer .btn-primary,
    .location-form-modal .modal-footer .tw-dw-btn-primary {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
        font-weight: 600;
        padding: 8px 20px;
        border-radius: 6px;
    }
    .location-form-modal .modal-footer .btn-default,
    .location-form-modal .modal-footer .tw-dw-btn-neutral {
        background: #fff !important;
        border: 1px solid #D5D9D9 !important;
        color: #0f1111 !important;
        font-weight: 500;
        padding: 8px 20px;
        border-radius: 6px;
    }
    .location-form-modal .modal-footer .btn-default:hover,
    .location-form-modal .modal-footer .tw-dw-btn-neutral:hover {
        background: #f7f8f8 !important;
        border-color: #a2a6a6 !important;
    }

    /* Tooltip / info icon */
    .location-form-modal .location-create-card .help-block { color: #565959 !important; font-size: 0.75rem; margin-top: 0.25rem; }
    </style>

        {!! Form::open(['url' => action([\App\Http\Controllers\BusinessLocationController::class, 'store']), 'method' => 'post', 'id' => 'business_location_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'business.add_business_location' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <!-- Card: Location basics -->
                <div class="col-sm-12">
                    <div class="location-create-card">
                        <h5 class="location-create-card-title"><i class="fa fa-map-marker-alt"></i> @lang('invoice.name') & @lang('lang_v1.location_id')</h5>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {!! Form::label('name', __( 'invoice.name' ) . ':*') !!}
                                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'invoice.name' ) ]); !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('location_id', __( 'lang_v1.location_id' ) . ':') !!}
                                    {!! Form::text('location_id', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.location_id' ) ]); !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('landmark', __( 'business.landmark' ) . ':') !!}
                                    {!! Form::text('landmark', null, ['class' => 'form-control', 'placeholder' => __( 'business.landmark' ) ]); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Address -->
                <div class="col-sm-12">
                    <div class="location-create-card">
                        <h5 class="location-create-card-title"><i class="fa fa-address-card"></i> @lang('business.address')</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('city', __( 'business.city' ) . ':*') !!}
                                    {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' => __( 'business.city'), 'required' ]); !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('zip_code', __( 'business.zip_code' ) . ':*') !!}
                                    {!! Form::text('zip_code', null, ['class' => 'form-control', 'placeholder' => __( 'business.zip_code'), 'required' ]); !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('state', __( 'business.state' ) . ':*') !!}
                                    {!! Form::text('state', null, ['class' => 'form-control', 'placeholder' => __( 'business.state'), 'required' ]); !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('country', __( 'business.country' ) . ':*') !!}
                                    {!! Form::text('country', null, ['class' => 'form-control', 'placeholder' => __( 'business.country'), 'required' ]); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Contact -->
                <div class="col-sm-12">
                    <div class="location-create-card">
                        <h5 class="location-create-card-title"><i class="fa fa-phone"></i> @lang('business.mobile') & @lang('business.email')</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('mobile', __( 'business.mobile' ) . ':') !!}
                                    {!! Form::text('mobile', null, ['class' => 'form-control', 'placeholder' => __( 'business.mobile')]); !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('alternate_number', __( 'business.alternate_number' ) . ':') !!}
                                    {!! Form::text('alternate_number', null, ['class' => 'form-control', 'placeholder' => __( 'business.alternate_number')]); !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('email', __( 'business.email' ) . ':') !!}
                                    {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => __( 'business.email')]); !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('website', __( 'lang_v1.website' ) . ':') !!}
                                    {!! Form::text('website', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.website')]); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Invoice & POS -->
                <div class="col-sm-12">
                    <div class="location-create-card">
                        <h5 class="location-create-card-title"><i class="fa fa-file-invoice"></i> @lang('invoice.invoice_scheme') & @lang('lang_v1.invoice_layout_for_pos')</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('invoice_scheme_id', __('invoice.invoice_scheme_for_pos') . ':*') !!} @show_tooltip(__('tooltip.invoice_scheme'))
                                    {!! Form::select('invoice_scheme_id', $invoice_schemes, null, ['class' => 'form-control', 'required', 'placeholder' => __('messages.please_select')]); !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('sale_invoice_scheme_id', __('invoice.invoice_scheme_for_sale') . ':*') !!}
                                    {!! Form::select('sale_invoice_scheme_id', $invoice_schemes, null, ['class' => 'form-control', 'required', 'placeholder' => __('messages.please_select')]); !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('invoice_layout_id', __('lang_v1.invoice_layout_for_pos') . ':*') !!} @show_tooltip(__('tooltip.invoice_layout'))
                                    {!! Form::select('invoice_layout_id', $invoice_layouts, null, ['class' => 'form-control', 'required', 'placeholder' => __('messages.please_select')]); !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('sale_invoice_layout_id', __('lang_v1.invoice_layout_for_sale') . ':*') !!} @show_tooltip(__('lang_v1.invoice_layout_for_sale_tooltip'))
                                    {!! Form::select('sale_invoice_layout_id', $invoice_layouts, null, ['class' => 'form-control', 'required', 'placeholder' => __('messages.please_select')]); !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('selling_price_group_id', __('lang_v1.default_selling_price_group') . ':') !!} @show_tooltip(__('lang_v1.location_price_group_help'))
                                    {!! Form::select('selling_price_group_id', $price_groups, null, ['class' => 'form-control', 'placeholder' => __('messages.please_select')]); !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('is_b2c', 'B2C' . ':') !!}
                                    {!! Form::checkbox('is_b2c', true, false) !!}
                                    @show_tooltip(__('B2C Help'))
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Custom fields -->
                @php
                $custom_labels = json_decode(session('business.custom_labels'), true);
                $location_custom_field1 = !empty($custom_labels['location']['custom_field_1']) ? $custom_labels['location']['custom_field_1'] : __('lang_v1.location_custom_field1');
                $location_custom_field2 = !empty($custom_labels['location']['custom_field_2']) ? $custom_labels['location']['custom_field_2'] : __('lang_v1.location_custom_field2');
                $location_custom_field3 = !empty($custom_labels['location']['custom_field_3']) ? $custom_labels['location']['custom_field_3'] : __('lang_v1.location_custom_field3');
                $location_custom_field4 = !empty($custom_labels['location']['custom_field_4']) ? $custom_labels['location']['custom_field_4'] : __('lang_v1.location_custom_field4');
                @endphp
                <div class="col-sm-12">
                    <div class="location-create-card">
                        <h5 class="location-create-card-title"><i class="fa fa-tags"></i> @lang('lang_v1.custom_field')</h5>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('custom_field1', $location_custom_field1 . ':') !!}
                                    {!! Form::text('custom_field1', null, ['class' => 'form-control', 'placeholder' => $location_custom_field1]); !!}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('custom_field2', $location_custom_field2 . ':') !!}
                                    {!! Form::text('custom_field2', null, ['class' => 'form-control', 'placeholder' => $location_custom_field2]); !!}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('custom_field3', $location_custom_field3 . ':') !!}
                                    {!! Form::text('custom_field3', null, ['class' => 'form-control', 'placeholder' => $location_custom_field3]); !!}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('custom_field4', $location_custom_field4 . ':') !!}
                                    {!! Form::text('custom_field4', null, ['class' => 'form-control', 'placeholder' => $location_custom_field4]); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: POS Featured products -->
                <div class="col-sm-12">
                    <div class="location-create-card">
                        <h5 class="location-create-card-title"><i class="fa fa-star"></i> @lang('lang_v1.pos_screen_featured_products')</h5>
                        <div class="form-group">
                            @show_tooltip(__('lang_v1.featured_products_help'))
                            {!! Form::select('featured_products[]', [], null, ['class' => 'form-control', 'id' => 'featured_products', 'multiple']); !!}
                        </div>
                    </div>
                </div>

                <!-- Card: Payment options -->
                <div class="col-sm-12">
                    <div class="location-create-card">
                        <h5 class="location-create-card-title"><i class="fa fa-credit-card"></i> @lang('lang_v1.payment_options') @show_tooltip(__('lang_v1.payment_option_help'))</h5>
                        <div class="form-group">
                            <table class="table table-condensed table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">@lang('lang_v1.payment_method')</th>
                                        <th class="text-center">@lang('lang_v1.enable')</th>
                                        <th class="text-center @if(empty($accounts)) hide @endif">@lang('lang_v1.default_accounts') @show_tooltip(__('lang_v1.default_account_help'))</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payment_types as $key => $value)
                                    <tr>
                                        <td class="text-center">{{ $value }}</td>
                                        <td class="text-center">{!! Form::checkbox('default_payment_accounts[' . $key . '][is_enabled]', 1, true); !!}</td>
                                        <td class="text-center @if(empty($accounts)) hide @endif">
                                            {!! Form::select('default_payment_accounts[' . $key . '][account]', $accounts, null, ['class' => 'form-control input-sm']); !!}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
