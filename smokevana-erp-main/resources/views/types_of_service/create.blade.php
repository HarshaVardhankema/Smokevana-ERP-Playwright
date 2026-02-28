<div class="modal-dialog amazon-form-modal modal-xl" role="document" style="max-width: 1100px; width: 95%;">
  <div class="modal-content">
    @include('layouts.partials.amazon_form_styles')

    {!! Form::open(['url' => action([\App\Http\Controllers\TypesOfServiceController::class, 'store']), 'method' => 'post', 'id' => 'types_of_service_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.add_type_of_service' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-concierge-bell"></i> @lang('lang_v1.service_information')</h5>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {!! Form::label('name', __( 'tax_rate.name' ) . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-tag"></i>
                    </span>
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'tax_rate.name' )]); !!}
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {!! Form::label('description', __( 'lang_v1.description' ) . ':') !!}
                  {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.description' ), 'rows' => 3]); !!}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-map-marker-alt"></i> @lang('lang_v1.location_price_groups')</h5>
            <div class="form-group">
              <table class="table table-slim">
                <thead>
                  <tr>
                    <th>@lang('sale.location')</th>
                    <th>@lang('lang_v1.price_group')</th> 
                  </tr>
                </thead>
                <tbody>
                  @foreach($locations as $key => $value)
                    <tr>
                      <td>{{$value}}</td>
                      <td>{!! Form::select('location_price_group[' . $key . ']', $price_groups, null, ['class' => 'form-control input-sm select2', 'style' => 'width: 100%;']); !!}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-box"></i> @lang('lang_v1.packing_charges')</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('packing_charge_type', __( 'lang_v1.packing_charge_type' ) . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-calculator"></i>
                    </span>
                    {!! Form::select('packing_charge_type', ['fixed' => __('lang_v1.fixed'), 'percent' => __('lang_v1.percentage')], 'fixed', ['class' => 'form-control']); !!}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('packing_charge', __( 'lang_v1.packing_charge' ) . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-dollar-sign"></i>
                    </span>
                    {!! Form::text('packing_charge', null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.packing_charge' )]); !!}
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="checkbox">
                    <label>
                      {!! Form::checkbox('enable_custom_fields', 1, false); !!} @lang( 'lang_v1.enable_custom_fields' )
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
