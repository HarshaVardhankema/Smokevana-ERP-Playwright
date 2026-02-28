<div class="modal-dialog amazon-form-modal modal-xl" role="document" style="max-width: 1100px; width: 95%;">
  <div class="modal-content">
    @include('layouts.partials.amazon_form_styles')

    {!! Form::open(['url' => action([\App\Http\Controllers\VariationTemplateController::class, 'store']), 'method' => 'post', 'id' => 'variation_add_form', 'class' => 'form-horizontal' ]) !!}
    
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('lang_v1.add_variation')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-list-alt"></i> @lang('lang_v1.variation_information')</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('name',__('lang_v1.variation_name') . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-tag"></i>
                    </span>
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.variation_name')]); !!}
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>@lang('lang_v1.add_variation_values'):*</label>
                  <div class="row">
                    <div class="col-md-10">
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-list"></i>
                        </span>
                        {!! Form::text('variation_values[]', null, ['class' => 'form-control', 'required']); !!}
                      </div>
                    </div>
                    <div class="col-md-2">
                      <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="add_variation_values" style="width: 100%;">+</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="variation_values"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.save')</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
