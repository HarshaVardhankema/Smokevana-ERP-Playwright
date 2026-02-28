<div class="modal-dialog amazon-form-modal modal-xl" role="document" style="max-width: 1100px; width: 95%;">
  <div class="modal-content">
    @include('layouts.partials.amazon_form_styles')

    {!! Form::open(['url' => action([\App\Http\Controllers\WarrantyController::class, 'store']), 'method' => 'post', 'id' => 'warranty_form']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.add_warranty' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-shield-alt"></i> @lang('lang_v1.warranty_information')</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('name', __( 'lang_v1.name' ) . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-tag"></i>
                    </span>
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.name' ) ]); !!}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <strong>{!! Form::label('duration', __( 'lang_v1.duration' ) . ':') !!}*</strong>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-clock"></i>
                        </span>
                        {!! Form::number('duration', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.duration' ), 'required' ]); !!}
                      </div>
                    </div>
                    <div class="col-md-6">
                      {!! Form::select('duration_type', ['days' => __('lang_v1.days'), 'months' => __('lang_v1.months'), 'years' => __('lang_v1.years')], '', ['class' => 'form-control','placeholder' => __('messages.please_select'), 'required']); !!}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {!! Form::label('description', __( 'lang_v1.description' ) . ':') !!}
                  {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.description' ), 'rows' => 3 ]); !!}
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
