<div class="modal-dialog tax-rate-modal" role="document">
  <div class="modal-content">
  <style>
  .tax-rate-modal .modal-content { border-radius: 10px; overflow: hidden; border: none; box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
  .tax-rate-modal .modal-header { background: #37475A; color: #ffffff; padding: 1rem 1.25rem; border-bottom: 1px solid rgba(255, 255, 255, 0.15); }
  .tax-rate-modal .modal-header .modal-title { font-size: 1.25rem; font-weight: 600; margin: 0; }
  .tax-rate-modal .modal-header .close { color: #fff; opacity: 0.9; text-shadow: none; }
  .tax-rate-modal .modal-header .close:hover { color: #ff9900; opacity: 1; }
  .tax-rate-modal .modal-body { background: #fff; padding: 1.25rem 1.5rem; }
  .tax-rate-modal .modal-body .form-group { margin-bottom: 0.75rem; }
  .tax-rate-modal .modal-body label { color: #0F1111 !important; font-size: 0.8125rem; }
  .tax-rate-modal .modal-body .form-control { background: #fff; border: 1px solid #D5D9D9; color: #0F1111; font-size: 0.8125rem; padding: 0.375rem 0.5rem; min-height: 2rem; box-sizing: border-box; }
  .tax-rate-modal .modal-body .form-control:focus { border-color: #FF9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2); }
  .tax-rate-modal .modal-footer { background: #37475A; color: #ffffff; border-top: 1px solid rgba(255, 255, 255, 0.15); padding: 0.75rem 1.25rem; }
  .tax-rate-modal .modal-footer .tw-dw-btn-primary { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border-color: #C7511F !important; color: #fff !important; font-weight: 600; padding: 8px 20px; border-radius: 6px; }
  .tax-rate-modal .modal-footer .tw-dw-btn-neutral { background: #fff !important; border: 1px solid #D5D9D9 !important; color: #0f1111 !important; font-weight: 500; padding: 8px 20px; border-radius: 6px; }
  .tax-rate-modal .modal-footer .tw-dw-btn-neutral:hover { background: #f7f8f8 !important; border-color: #a2a6a6 !important; }
  </style>

    {!! Form::open(['url' => action([\App\Http\Controllers\TaxRateController::class, 'update'], [$tax_rate->id]), 'method' => 'PUT', 'id' => 'tax_rate_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('tax_rate.edit_tax_rate')</h4>
    </div>

    <div class="modal-body">
      @if(auth()->user()->can('access_all_locations') || auth()->user()->can('admin'))
                
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                        <div class="input-group">
                            
                            {!! Form::select('location_id', $business_locations,$tax_rate->web_location_id, ['class' => 'form-control select2 select_location_id', 'placeholder' => __('messages.please_select'),'required']); !!}
                        </div>
                    </div>
                
                @endif
                <div class="form-group brand_select_div customer_fields @if(!$is_b2c) hide @endif">
                    
                        {{ Form::label('brand_id', 'Select Brand' . ':') }}
                        <div class="brand_select">
                        {{ Form::select('brand_id', $brands, $tax_rate->brand_id??null, [
                        'class' => 'form-control select',
                        'placeholder' => __('messages.please_select')
                        ]) }}
                        </div>
                    
                </div>
                <div class="clearfix"></div>
      <!-- Location Tax Type (Pre-filled with the current selection) -->
      <div class="form-group">
        {!! Form::label('location_tax_type', __('tax_rate.location_tax_type') . ':*') !!}
        {!! Form::select('location_tax_type', $locationTaxTypes, $tax_rate->location_id, ['class' => 'form-control', 'required', 'placeholder' => __('tax_rate.select_tax_type'), 'id' => 'location_tax_type']) !!}
      </div>

      <!-- Tax Type (Dependent on Location Tax Type) -->
      <div class="form-group" id="tax_type_group" style="display: {{ $tax_rate->location_id ? 'block' : 'none' }};">
        {!! Form::label('tax_type', __('tax_rate.tax_type') . ':*') !!}
        {!! Form::select('tax_type', [
            'UNIT_COUNT' => 'Unit Count',
            'UNIT_BASIS_ML' => 'Unit Basis ML',
            'PERCENTAGE_ON_SALE' => 'Percentage on Sale',
            'PERCENTAGE_ON_COST' => 'Percentage on Cost'
        ], $tax_rate->tax_type, ['class' => 'form-control', 'id' => 'tax_type']) !!}
      </div>

      <!-- State Name -->
      {{-- <div class="form-group">
        {!! Form::label('state_name', __('tax_rate.state_name') . ':*') !!}
        {!! Form::text('state_name', $tax_rate->state_name, ['class' => 'form-control', 'required', 'placeholder' => __('tax_rate.state_name')]) !!}
      </div> --}}

      <!-- State Code -->
      <div class="form-group">
        {!! Form::label('state_code', __('tax_rate.state_code') . ':*') !!}
        {!! Form::text('state_code', $tax_rate->state_code, ['class' => 'form-control', 'required', 'placeholder' => __('tax_rate.state_code')]) !!}
      </div>

      <!-- Value -->
      <div class="form-group">
        {!! Form::label('value', __('tax_rate.value') . ':*') !!}
        {!! Form::number('value', $tax_rate->value, ['class' => 'form-control', 'required', 'step' => 'any', 'min' => '0.01']) !!}
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.update')</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  // Handle dynamic show/hide of tax type field based on selected location_tax_type
  $(document).ready(function() {
    // Show or hide the tax type group based on selected location tax type
    $('#location_tax_type').change(function () {
      var selectedType = $(this).val();
      if (selectedType) {
        $('#tax_type_group').show();
      } else {
        $('#tax_type_group').hide();
      }
    });

    // Ensure tax type visibility is correct when editing
    var selectedLocationTaxType = $('#location_tax_type').val();
    if (selectedLocationTaxType) {
      $('#tax_type_group').show();
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
                    $('.brand_select_div').removeClass('hide');
                    $('.brand_select').empty();
                    $('.brand_select').append('<select name="brand_id" class="form-control select brand_select_form_admin">');
                    $('.brand_select_form_admin').append('<option value="">Select Brand</option>');
                    response.brands.forEach(function (brand) {
                        $('.brand_select_form_admin').append('<option value="' + brand.id + '">' + brand.name + '</option>');
                    });
                    $('.brand_select_form_admin').append('</select>');
                    }else{
                    $('.brand_select_div').addClass('hide');
                    $('.brand_select').empty();
                    }
                }
            });
        });

  // Prevent negative values in tax rate value field
  $('input[name="value"]').on('input', function() {
    var value = parseFloat($(this).val());
    if (value < 0) {
      $(this).val(0);
    }
  });

  // Validate on form submit
  $('#tax_rate_edit_form').on('submit', function(e) {
    var value = parseFloat($('input[name="value"]').val());
    if (value < 0) {
      e.preventDefault();
      alert('Tax rate value cannot be negative.');
      $('input[name="value"]').val(0);
      return false;
    }
  });

</script>
