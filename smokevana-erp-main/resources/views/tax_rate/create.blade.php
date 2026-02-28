<div class="modal-dialog amazon-form-modal modal-xl" role="document" style="max-width: 1100px; width: 95%;">
    <div class="modal-content">
    @include('layouts.partials.amazon_form_styles')

        {!! Form::open(['url' => action([\App\Http\Controllers\TaxRateController::class, 'store']), 'method' => 'post',
        'id' => 'tax_rate_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('tax_rate.add_tax_rate')</h4>
        </div>

        <div class="modal-body">
          <div class="row">
            <!-- Card: Location & Brand Selection -->
            <div class="col-md-12">
              <div class="amazon-form-card">
                <h5 class="amazon-form-card-title"><i class="fa fa-map-marker-alt"></i> @lang('lang_v1.location_brand_selection')</h5>
                @if(auth()->user()->can('access_all_locations') || auth()->user()->can('admin'))
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-map-marker"></i>
                        </span>
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2 select_location_id', 'placeholder' => __('messages.please_select') ,'required']); !!}
                      </div>
                    </div>
                  </div>
                </div>
                @endif
                @if(!empty($is_b2c))
                @if(!auth()->user()->can('access_all_locations') || !auth()->user()->can('admin'))
                <div class="row">
                  <div class="col-md-6 brand_select_div_for_non customer_fields">
                    <div class="form-group">
                      {{ Form::label('brand_id', 'Select Brand' . ':') }}
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-bookmark"></i>
                        </span>
                        {{ Form::select('brand_id', $brands, null, [
                        'class' => 'form-control select',
                        'placeholder' => __('messages.please_select')
                        ]) }}
                      </div>
                    </div>
                  </div>
                </div>
                @endif
                @endif

                {{-- Brand Selection for Admin Users (Hidden by default) --}}
                @if(auth()->user()->can('access_all_locations') || auth()->user()->can('admin'))
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group brand_select_form_admin hide customer_fields">
                      {{ Form::label('brand_id', 'Select Brand' . ':') }}
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-bookmark"></i>
                        </span>
                        <select name="brand_id" class="form-control select brand_id_select_form_admin" placeholder="Select Brand">
                          <option value="">Select Brand</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                @endif
              </div>
            </div>

            <!-- Card: Tax Rate Information -->
            <div class="col-md-12">
              <div class="amazon-form-card">
                <h5 class="amazon-form-card-title"><i class="fa fa-percent"></i> @lang('tax_rate.tax_rate_information')</h5>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      {!! Form::label('location_tax_type', __('tax_rate.location_tax_type') . ':*') !!}
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-list"></i>
                        </span>
                        {!! Form::select('location_tax_type', $locationTaxTypes, null, ['class' => 'form-control', 'required',
                        'placeholder' => __('tax_rate.select_tax_type'), 'id' => 'location_tax_type']) !!}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group" id="tax_type_group" style="display: none;">
                      {!! Form::label('tax_type', __('tax_rate.tax_type') . ':*') !!}
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-cog"></i>
                        </span>
                        {!! Form::select('tax_type', [
                        'UNIT_COUNT' => 'Unit Count',
                        'UNIT_BASIS_ML' => 'Unit Basis ML',
                        'PERCENTAGE_ON_SALE' => 'Percentage on Sale',
                        'PERCENTAGE_ON_COST' => 'Percentage on Cost'
                        ], null, ['class' => 'form-control', 'id' => 'tax_type']) !!}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      {!! Form::label('state_code', __('tax_rate.state_code') . ':*') !!}
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-hashtag"></i>
                        </span>
                        {!! Form::text('state_code', null, ['class' => 'form-control', 'required', 'placeholder' =>
                        __('tax_rate.state_code')]) !!}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      {!! Form::label('value', __('tax_rate.value') . ':*') !!}
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-calculator"></i>
                        </span>
                        {!! Form::number('value', null, ['class' => 'form-control', 'required', 'step' => 'any' ,'min'=>'.001'])
                        !!}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.save')</button>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white"
                data-dismiss="modal">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    // Handle dynamic show/hide of tax type field based on selected location_tax_type
  $('#location_tax_type').change(function () {
      var selectedType = $(this).val();
      if (selectedType) {
          $('#tax_type_group').show();
      } else {
          $('#tax_type_group').hide();
      }
  });
  $('.select_location_id').on('change', function () {
    $.ajax({
        url: '/business-location/' + $(this).val(),
        type: 'GET',
        data: {location_id: $(this).val()},
        success: function (response) {
            console.log(response);
            if(response.is_b2c == 1){
            $('.brand_select_form_admin').removeClass('hide');
                response.brands.forEach(function (brand) {
                    $('.brand_id_select_form_admin').append('<option value="' + brand.id + '">' + brand.name + '</option>');
                });
            }else{
                $('.brand_select_form_admin').addClass('hide');
                $('.brand_id_select_form_admin').empty();
                $('.brand_id_select_form_admin').append('<option value="">Select Brand</option>');
            }
        }
    });
    // Prevent negative values in tax rate value field
  $('input[name="value"]').on('input', function() {
    var value = parseFloat($(this).val());
    if (value < 0) {
      $(this).val(0);
    }
  });

  // Validate on form submit
  $('#tax_rate_add_form').on('submit', function(e) {
    var value = parseFloat($('input[name="value"]').val());
    if (value < 0) {
      e.preventDefault();
      alert('Tax rate value cannot be negative.');
      $('input[name="value"]').val(0);
      return false;
    }
  });
});
</script>
