<div class="modal-dialog amazon-form-modal modal-xl" role="document" style="max-width: 1100px; width: 95%;">
  <div class="modal-content">
    @include('layouts.partials.amazon_form_styles')

    {!! Form::open(['url' => action([\App\Http\Controllers\UnitController::class, 'store']), 'method' => 'post', 'id' => $quick_add ? 'quick_add_unit_form' : 'unit_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'unit.add_unit' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-ruler"></i> @lang('unit.unit_information')</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('actual_name', __( 'unit.name' ) . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-tag"></i>
                    </span>
                    {!! Form::text('actual_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'unit.name' )]); !!}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('short_name', __( 'unit.short_name' ) . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-text-width"></i>
                    </span>
                    {!! Form::text('short_name', null, ['class' => 'form-control', 'placeholder' => __( 'unit.short_name' ), 'required']); !!}
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('allow_decimal', __( 'unit.allow_decimal' ) . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-check-circle"></i>
                    </span>
                    {!! Form::select('allow_decimal', ['1' => __('messages.yes'), '0' => __('messages.no')], null, ['placeholder' => __( 'messages.please_select' ), 'required', 'class' => 'form-control']); !!}
                  </div>
                </div>
              </div>
            </div>
            @if(!$quick_add)
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="checkbox">
                      <label>
                        {!! Form::checkbox('define_base_unit', 1, false,[ 'class' => 'toggler', 'data-toggle_id' => 'base_unit_div' ]); !!} @lang( 'lang_v1.add_as_multiple_of_base_unit' )
                      </label> @show_tooltip(__('lang_v1.multi_unit_help'))
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group hide" id="base_unit_div">
                    <table class="table">
                      <tr>
                        <th style="vertical-align: middle;">1 <span id="unit_name">@lang('product.unit')</span></th>
                        <th style="vertical-align: middle;">=</th>
                        <td style="vertical-align: middle;">
                          {!! Form::text('base_unit_multiplier', null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.times_base_unit' )]); !!}
                        </td>
                        <td style="vertical-align: middle;">
                          {!! Form::select('base_unit_id', $units, null, ['placeholder' => __( 'lang_v1.select_base_unit' ), 'class' => 'form-control']); !!}
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            @endif
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
