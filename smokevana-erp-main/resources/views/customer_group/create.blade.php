<div class="modal-dialog amazon-form-modal modal-xl" role="document" style="max-width: 1100px; width: 95%;">
  <div class="modal-content">
    @include('layouts.partials.amazon_form_styles')

    {!! Form::open(['url' => action([\App\Http\Controllers\CustomerGroupController::class, 'store']), 'method' => 'post', 'id' => 'customer_group_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.add_customer_group' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-users"></i> @lang('lang_v1.customer_group_information')</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('name', __( 'lang_v1.customer_group_name' ) . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-tag"></i>
                    </span>
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.customer_group_name' ) ]); !!}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('price_calculation_type', __( 'lang_v1.price_calculation_type' ) . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-calculator"></i>
                    </span>
                    {!! Form::select(
                        'price_calculation_type',
                        ['percentage' => __('lang_v1.percentage'), 'selling_price_group' => __('lang_v1.selling_price_group')],
                        'percentage',
                        ['class' => 'form-control', 'id' => 'price_calculation_type']
                    ) !!}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-percent"></i> @lang('lang_v1.price_calculation')</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group percentage-field">
                  {!! Form::label('amount', __( 'lang_v1.calculation_percentage' ) . ':') !!}
                  @show_tooltip(__('lang_v1.tooltip_calculation_percentage'))
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-percent"></i>
                    </span>
                    {!! Form::text('amount', null, ['class' => 'form-control input_number','placeholder' => __( 'lang_v1.calculation_percentage')]); !!}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group selling_price_group-field" style="display: none;">
                  {!! Form::label('selling_price_group_id', __( 'lang_v1.selling_price_group' ) . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-list"></i>
                    </span>
                    {!! Form::select('selling_price_group_id', $price_groups, null, ['class' => 'form-control', 'id' => 'selling_price_group_id']); !!}
                  </div>
                </div>
                <div class="form-group price-percentage-field" style="display: none;">
                  {!! Form::label('price_percentage', __( 'lang_v1.calculation_percentage' ) . ':') !!}
                  @show_tooltip(__('lang_v1.tooltip_calculation_percentage'))
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-percent"></i>
                    </span>
                    {!! Form::text('price_percentage', null, ['class' => 'form-control input_number','placeholder' => __( 'lang_v1.calculation_percentage')]); !!}
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
      <button type="button" class="tw-dw-btn" data-dismiss="modal" style="background: #f0f0f0; border: 1px solid #888; color: #333;">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
    $(document).ready(function() {
        // Function to toggle fields based on price calculation type
        function togglePriceCalculationFields() {
            var calculationType = $('#price_calculation_type').val();
            
            if (calculationType === 'selling_price_group') {
                $('.selling_price_group-field').show();
                $('.price-percentage-field').show();
                $('.percentage-field').hide();
            } else {
                $('.selling_price_group-field').hide();
                $('.price-percentage-field').hide();
                $('.percentage-field').show();
            }
        }
        
        // Initial state - hide selling price group field
        togglePriceCalculationFields();
        
        // Listen for changes on the price calculation type dropdown
        $(document).on('change', '#price_calculation_type', function() {
            togglePriceCalculationFields();
        });
    });
</script>
