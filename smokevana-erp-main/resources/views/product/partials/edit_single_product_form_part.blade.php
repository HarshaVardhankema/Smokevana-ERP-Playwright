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
@php
    $common_settings = session()->get('business.common_settings');
@endphp

<div class="col-sm-12"><br>
    <div class="table-responsive">
    <table class="table table-bordered add-product-price-table table-condensed {{$class}}">
        <thead>
          <th>@lang('product.default_purchase_price')</th>
          <th class="hide" style="display: none !important;">@lang('product.profit_percent') @show_tooltip(__('tooltip.profit_percent'))</th>
          <th>BarCode</th>
          <th>Selling Price</th>
          <th>@lang('lang_v1.product_image')</th>
        </thead>
        @foreach($product_deatails->variations as $variation )
            @php
                $is_image_required = !empty($common_settings['is_product_image_required']) && count($variation->media) == 0;
            @endphp
            @if($loop->first)
                <tbody>
                    <td>
                        <input type="hidden" name="single_variation_id" value="{{$variation->id}}">

                        <div class="col-sm-12">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}

                          {!! Form::text('single_dpp', @num_format($variation->default_purchase_price), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'), 'required']); !!}
                        </div>                      

                        <div class=" hide col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                        
                          {!! Form::text('single_dpp_inc_tax', @num_format($variation->dpp_inc_tax), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'), 'required']); !!}
                        </div>
                    </td>

                    <td class="hide" style="display: none !important;"> 
                        <br/>
                        {!! Form::text('profit_percent', @num_format($variation->profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent', 'required']); !!}
                    </td>
                    <td>
                        <div class="">
                            <label for="single_barcode_no">{{ __('product.barcode_no') }}:*</label>
                            <input type="text" id="single_barcode_no" name="var_barcode_no" 
                                class="form-control input-sm barcode_no" 
                                value="{{$variation->var_barcode_no }}" 
                                placeholder="{{ __('product.barcode_no') }}" 
                                >
                        </div>
                    </td>
                    <td>
                        <label><span class="dsp_label"></span></label>
                        {!! Form::text('single_dsp', @num_format($variation->default_sell_price), ['class' => 'form-control input-sm dsp input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp', 'required']); !!}

                        {!! Form::text('single_dsp_inc_tax', @num_format($variation->sell_price_inc_tax), ['class' => 'form-control input-sm hide input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax', 'required']); !!}
                    </td>
                    <td>
                        @php 
                            $action = !empty($action) ? $action : '';
                        @endphp
                        @if($action !== 'duplicate')
                            @foreach($variation->media as $media)
                                <div class="img-thumbnail">
                                    <span class="badge bg-red delete-media" data-href="{{ action([\App\Http\Controllers\ProductController::class, 'deleteMedia'], ['media_id' => $media->id])}}"><i class="fas fa-times"></i></span>
                                    {!! $media->thumbnail() !!}
                                </div>
                            @endforeach
                        @endif
                        <div class="form-group">
                            {!! Form::label('variation_images', __('lang_v1.product_image') . ':') !!}
                            {!! Form::file('variation_images[]', ['class' => 'variation_images', 
                                'accept' => 'image/*', 'multiple', 'required' => $is_image_required]); !!}
                            <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
                        </div>
                    </td>
                </tbody>
            @endif
        @endforeach
    </table>
    </div>
</div>