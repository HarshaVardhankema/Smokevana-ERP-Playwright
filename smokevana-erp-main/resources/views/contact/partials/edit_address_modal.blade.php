<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\App\Http\Controllers\CustomerAddressController::class, 'update'], [$address->id]), 'method' => 'put', 'id' => 'edit_address_form']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('messages.edit') @lang('lang_v1.address')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('first_name', __('contact.first_name') . ':') !!}
                        {!! Form::text('first_name', $address->first_name, ['class' => 'form-control', 'placeholder' => __('contact.first_name')]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('last_name', __('contact.last_name') . ':') !!}
                        {!! Form::text('last_name', $address->last_name, ['class' => 'form-control', 'placeholder' => __('contact.last_name')]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('company', 'Company' . ':') !!}
                        {!! Form::text('company', $address->company, ['class' => 'form-control', 'placeholder' => 'Company']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('address_label', __('lang_v1.address_label') . ':') !!}
                        {!! Form::text('address_label', $address->address_label, ['class' => 'form-control', 'placeholder' => __('lang_v1.address_label')]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('address_type', __('lang_v1.address_type') . ':') !!}
                        {!! Form::select('address_type', ['billing' => 'Billing', 'shipping' => 'Shipping'], $address->address_type, ['class' => 'form-control', 'placeholder' =>"Select Address Type"]) !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('address_line_1', 'Address Line 1' . ':*') !!}
                        {!! Form::text('address_line_1', $address->address_line_1, ['class' => 'form-control', 'required', 'placeholder' => 'Address Line 1']) !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('address_line_2', 'Address Line 2' . ':') !!}
                        {!! Form::text('address_line_2', $address->address_line_2, ['class' => 'form-control', 'placeholder' => 'Address Line 2']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('city', 'City' . ':') !!}
                        {!! Form::text('city', $address->city, ['class' => 'form-control', 'placeholder' => 'City']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('state', 'State' . ':') !!}
                        {!! Form::text('state', $address->state, ['class' => 'form-control', 'placeholder' => 'State']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('zip_code', 'ZIP Code' . ':') !!}
                        {!! Form::text('zip_code', $address->zip_code, ['class' => 'form-control', 'placeholder' => 'ZIP Code']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('country', 'Country' . ':') !!}
                        {!! Form::text('country', $address->country, ['class' => 'form-control', 'placeholder' => 'Country']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">
                @lang('messages.update')
            </button>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

