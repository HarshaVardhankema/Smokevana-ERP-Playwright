@if(!session('business.enable_price_tax')) 
  @php
    $default = 0;
    $class = 'hide';
  @endphp
@else
  @php
    $default = null;
    $class = '';
  @endphp
@endif

<div class="table-responsive">
    <table class="table add-product-price-table table-condensed {{$class}}">
        <thead>
          <th>@lang('product.default_purchase_price')</th>
          <th class="hide" style="display: none !important;">@lang('product.profit_percent') @show_tooltip(__('tooltip.profit_percent'))</th>   
          <th>BarCode</th>
          <th>Selling Price</th>
          @if(empty($quick_add))
            <th>@lang('lang_v1.product_image')</th>
          @endif
        </thead>
        <tbody>
        <tr>
          <td>
            <div class="col-sm-6">
              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}

              {!! Form::text('single_dpp', $default, ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'), 'required']) !!}
            </div>
            <div class=" hide col-sm-6">
              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
            
              {!! Form::text('single_dpp_inc_tax', $default, ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'), 'required']); !!}
            </div>
          </td>

          <td class="hide" style="display: none !important;">
            <br/>
            {!! Form::text('profit_percent', @num_format($profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent', 'required']); !!}
          </td>
          <td>
            <div class="">
              {!! Form::label('barcode_no', trans('product.barcode_no') . ':*') !!}
              
              {!! Form::text('barcode_no', null, ['class' => 'form-control input-sm barcode','id'=>'barcode_no', 'placeholder' => __('product.barcode_no')]) !!}
            </div>
          </td>
          <td class=" ">
            <label><span class="dsp_label">@lang('product.exc_of_tax')</span></label>
            {!! Form::text('single_dsp', $default, ['class' => 'form-control input-sm dsp input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp', 'required']); !!}

            {!! Form::text('single_dsp_inc_tax', $default, ['class' => 'form-control input-sm hide input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax', 'required']); !!}
          </td>
          @if(empty($quick_add))
          <td>
              <div class="form-group">
                {!! Form::label('variation_images', __('lang_v1.product_image') . ':') !!}
                {!! Form::file('variation_images[]', ['class' => 'variation_images', 
                    'accept' => 'image/*', 'multiple']); !!}
                <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
              </div>
          </td>
          @endif
        </tbody>
    </table>
</div>