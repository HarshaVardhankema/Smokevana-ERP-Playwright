<div class="modal-dialog amazon-form-modal modal-xl" role="document" style="max-width: 1100px; width: 95%;">
  <div class="modal-content">
    @include('layouts.partials.amazon_form_styles')

    {!! Form::open(['url' => action([\App\Http\Controllers\DiscountController::class, 'store']), 'method' => 'post', 'id' => 'discount_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.add_discount' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <!-- Card: Basic Information -->
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-tag"></i> @lang('lang_v1.basic_information')</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('name', __( 'unit.name' ) . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-tag"></i>
                    </span>
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'unit.name' ) ]); !!}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('priority', __( 'lang_v1.priority' ) . ':') !!} @show_tooltip(__('lang_v1.discount_priority_help'))
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-sort-numeric-up"></i>
                    </span>
                    {!! Form::text('priority', null, ['class' => 'form-control input_number', 'required', 'placeholder' => __( 'lang_v1.priority' ) ]); !!}
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('discount_type', __('sale.discount_type') . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-percent"></i>
                    </span>
                    {!! Form::select('discount_type', ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'required']); !!}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('discount_amount', __( 'sale.discount_amount' ) . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-dollar-sign"></i>
                    </span>
                    {!! Form::text('discount_amount', null, ['class' => 'form-control input_number', 'required', 'placeholder' => __( 'sale.discount_amount' ) ]); !!}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card: Applicable To -->
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-filter"></i> @lang('lang_v1.applicable_to')</h5>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {!! Form::label('variation_ids', __('report.products') . ':') !!}
                  {!! Form::select('variation_ids[]', [], null, ['id' => "variation_ids", 'class' => 'form-control select2', 'multiple', 'style' => 'width: 100%;']); !!}
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6" id="brand_input">
                <div class="form-group">
                  {!! Form::label('brand_id', __('product.brand') . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-bookmark"></i>
                    </span>
                    {!! Form::select('brand_id', $brands, null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
                  </div>
                </div>
              </div>
              <div class="col-md-6" id="category_input">
                <div class="form-group">
                  {!! Form::label('category_id', __('product.category') . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-folder"></i>
                    </span>
                    {!! Form::select('category_id', $categories, null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('location_id', __('sale.location') . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-map-marker-alt"></i>
                    </span>
                    {!! Form::select('location_id', $locations, null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'required']); !!}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('spg', __('lang_v1.selling_price_group') . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-list"></i>
                    </span>
                    <select name="spg" class="form-control">
                      <option value="" >@lang('lang_v1.all')</option>
                      @foreach($price_groups as $k => $v)
                        <option value="{{$k}}">{{$v}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card: Date Range & Settings -->
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-calendar-alt"></i> @lang('lang_v1.date_range_settings')</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('starts_at', __( 'lang_v1.starts_at' ) . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::text('starts_at', null, ['class' => 'form-control discount_date', 'required', 'placeholder' => __( 'lang_v1.starts_at' ), 'readonly' ]); !!}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('ends_at', __( 'lang_v1.ends_at' ) . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-calendar-check"></i>
                    </span>
                    {!! Form::text('ends_at', null, ['class' => 'form-control discount_date', 'required', 'placeholder' => __( 'lang_v1.ends_at' ), 'readonly' ]); !!}
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    {!! Form::checkbox('applicable_in_cg', 1, false, ['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.applicable_in_cg')</strong>
                  </label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    {!! Form::checkbox('is_active', 1, true, ['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.is_active')</strong>
                  </label>
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
