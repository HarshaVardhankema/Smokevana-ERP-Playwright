<div class="modal-dialog amazon-form-modal modal-xl" role="document" style="max-width: 1100px; width: 95%;">
  <div class="modal-content">
    @include('layouts.partials.amazon_form_styles')

    {!! Form::open(['url' => action([\App\Http\Controllers\ExpenseCategoryController::class, 'store']), 'method' => 'post', 'id' => 'expense_category_add_form' ]) !!}
    
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'expense.add_expense_category' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-folder"></i> @lang('expense.category_information')</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('name', __( 'expense.category_name' ) . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-tag"></i>
                    </span>
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'expense.category_name' )]); !!}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('code', __( 'expense.category_code' ) . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-hashtag"></i>
                    </span>
                    {!! Form::text('code', null, ['class' => 'form-control', 'placeholder' => __( 'expense.category_code' )]); !!}
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <div class="checkbox">
                    <label>
                      {!! Form::checkbox('add_as_sub_cat', 1, false,[ 'class' => 'toggler', 'data-toggle_id' => 'parent_cat_div' ]); !!} @lang( 'lang_v1.add_as_sub_cat' )
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group hide" id="parent_cat_div">
                  {!! Form::label('parent_id', __( 'category.select_parent_category' ) . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-sitemap"></i>
                    </span>
                    {!! Form::select('parent_id', $categories, null, ['class' => 'form-control', 'placeholder' => __('lang_v1.none')]); !!}
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
