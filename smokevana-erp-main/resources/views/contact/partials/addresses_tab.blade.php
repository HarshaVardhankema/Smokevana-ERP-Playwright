<div class="row">
    <div class="col-md-12">
        <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm pull-right tw-m-2" id="add_address_btn">
            <i class="fa fa-plus"></i> @lang('messages.add') @lang('lang_v1.address')
        </button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        @include('contact.partials.addresses_table')
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="add_address_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {!! Form::open(['url' => action([\App\Http\Controllers\CustomerAddressController::class, 'store']), 'method' => 'post', 'id' => 'add_address_form']) !!}
            {!! Form::hidden('contact_id', $contact->id) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">@lang('messages.add') @lang('lang_v1.address')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('first_name', __('contact.first_name') . ':') !!}
                            {!! Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => __('contact.first_name')]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('last_name', __('contact.last_name') . ':') !!}
                            {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => __('contact.last_name')]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('company', 'Company' . ':') !!}
                            {!! Form::text('company', null, ['class' => 'form-control', 'placeholder' => 'Company']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('address_label', __('lang_v1.address_label') . ':') !!}
                            {!! Form::text('address_label', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.address_label')]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('address_type', __('lang_v1.address_type') . ':') !!}
                            {!! Form::select('address_type', ['billing' => 'Billing', 'shipping' => 'Shipping'], null, ['class' => 'form-control', 'placeholder' => "Select Address Type"]) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('address_line_1', 'Address Line 1' . ':*') !!}
                            {!! Form::text('address_line_1', null, ['class' => 'form-control', 'required', 'placeholder' => 'Address Line 1']) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('address_line_2', 'Address Line 2' . ':') !!}
                            {!! Form::text('address_line_2', null, ['class' => 'form-control', 'placeholder' => 'Address Line 2']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('city', 'City' . ':') !!}
                            {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' => 'City']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('state', 'State' . ':') !!}
                            {!! Form::text('state', null, ['class' => 'form-control', 'placeholder' => 'State']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('zip_code', 'ZIP Code' . ':') !!}
                            {!! Form::text('zip_code', null, ['class' => 'form-control', 'placeholder' => 'ZIP Code']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('country', 'Country' . ':') !!}
                            {!! Form::text('country', null, ['class' => 'form-control', 'placeholder' => 'Country']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">
                    @lang('messages.save')
                </button>
                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">
                    @lang('messages.close')
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- Edit Address Modal -->
<div class="modal fade" id="edit_address_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

