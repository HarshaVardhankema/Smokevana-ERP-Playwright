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
 $array_name = 'product_variation_edit';
 $variation_array_name = 'variations_edit';
 if($action == 'duplicate'){
    $array_name = 'product_variation';
    $variation_array_name = 'variations';
 }

    $common_settings = session()->get('business.common_settings');
@endphp

<tr class="variation_row">
    <td>
        @if($action == 'edit')
            <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-error discontinue_variation_row" 
                    title="Discontinue this variation (will not affect existing orders/invoices)">
                <i class="fa fa-trash"></i>
            </button>
        @else
            <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-error delete_complete_row"><i class="fa fa-trash"></i></button>
        @endif
    </td>
    <td>
        {!! Form::text($array_name . '[' . $row_index .'][name]', $product_variation->name, ['class' => 'form-control input-sm variation_name', 'required', 'readonly']); !!}

        {!! Form::hidden($array_name . '[' . $row_index .'][variation_template_id]', $product_variation->variation_template_id); !!}

        <input type="hidden" class="row_index" value="@if($action == 'edit'){{$row_index}}@else{{$loop->index}}@endif">
        <input type="hidden" class="row_edit" value="edit">
    </td>

    <td>
        <table class="table table-condensed table-bordered variation_value_table table-striped">
            <thead>
            <tr >
                <th>@lang('product.sku') @show_tooltip(__('tooltip.sub_sku'))</th>
                <th>@lang('product.var_barcode_no') @show_tooltip(__('tooltip.var_barcode_no'))</th>
                <th>@lang('product.var_maxSaleLimit') @show_tooltip(__('tooltip.var_maxSaleLimit'))</th>
                <th>Name</th>
                <th class="{{$class}}">Cost
                    <span class=" hide"><small><i>@lang('product.exc_of_tax')</i></small></span>
                    <span class=" hide"><small><i>@lang('product.inc_of_tax')</i></small></span>
                </th>
                <th class="{{$class}} hide" style="display: none !important;">@lang('product.profit_percent')</th>
                <th class="{{$class}}">Selling Price</th>
                @if(!empty($allowed_group_prices))
                    @foreach($allowed_group_prices as $pg_id => $pg_name)
                        <th class="vp-text-right">{{ $pg_name }}</th>
                    @endforeach
                @endif
                <th class="variation-images-th">@lang('lang_v1.variation_images')</th>
                <th><button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-accent add_variation_value_row">+</button></th>
            </tr>
            </thead>

            <tbody>

            @forelse ($product_variation->variations as $variation)
                @php
                    $variation_row_index = $variation->id;
                    $sub_sku_required = 'required';
                    if($action == 'duplicate'){
                        $variation_row_index = $loop->index;
                        $sub_sku_required = '';
                    }
                @endphp
                <style>
                    .variation_value_table tbody tr {
                            border-bottom: 2px solid #ccc;
                            padding-bottom: 10px;
                        }
                    .variation_value_table .variation-images-th,
                    .variation_value_table .variation-images-cell {
                        min-width: 140px;
                        vertical-align: top !important;
                    }
                    .variation_value_table .variation-images-cell input[type="file"].variation_images {
                        display: block;
                        margin-top: 6px;
                        font-size: 12px;
                        min-height: 28px;
                    }
                    .variation_value_table .variation-images-cell .img-thumbnail {
                        max-width: 50px;
                        max-height: 50px;
                        object-fit: cover;
                    }
                </style>

                <tr>
                    <td>
                        @if($action != 'duplicate')
                            <input type="hidden" class="row_variation_id" value="{{$variation->id}}">
                        @endif
                        {!! Form::text($array_name . '[' . $row_index .'][' . $variation_array_name . '][' . $variation_row_index . '][sub_sku]', $action == 'edit' ? $variation->sub_sku : null, ['class' => 'form-control input-sm input_sub_sku', $sub_sku_required]); !!}
                    </td>
                    <td>
                        @if($action != 'duplicate')
                            <input type="hidden" class="row_variation_id row_variation_var_bar_validate" value="{{$variation->id}}">
                        @endif
                        {!! Form::text($array_name . '[' . $row_index .'][' . $variation_array_name . '][' . $variation_row_index . '][var_barcode_no]', $action == 'edit' ? $variation->var_barcode_no : null, ['class' => 'form-control input-sm input_var_barcode_no input_sub_var_barcode_no', '']); !!}
                    </td>
                    <td>
                        {!! Form::text($array_name . '[' . $row_index .'][' . $variation_array_name . '][' . $variation_row_index . '][var_maxSaleLimit]', $variation->var_maxSaleLimit, ['class' => 'form-control input-sm variation_value_name']); !!}

                        {!! Form::hidden($array_name . '[' . $row_index .'][' . $variation_array_name . '][' . $variation_row_index . '][variation_value_id]', $variation->var_maxSaleLimit); !!}
                    </td>
                    <td>
                        {!! Form::text($array_name . '[' . $row_index .'][' . $variation_array_name . '][' . $variation_row_index . '][value]', $variation->name, ['class' => 'form-control input-sm variation_value_name', 'required']); !!}

                        {!! Form::hidden($array_name . '[' . $row_index .'][' . $variation_array_name . '][' . $variation_row_index . '][variation_value_id]', $variation->variation_value_id); !!}
                    </td>
                    <td class="{{$class}}">
                        <div class="">
                            {!! Form::text($array_name . '[' . $row_index .'][' . $variation_array_name . '][' . $variation_row_index . '][default_purchase_price]', @num_format($variation->default_purchase_price), ['class' => 'form-control input-sm variable_dpp input_number', 'placeholder' => __('product.exc_of_tax'), 'required']); !!}
                        </div>

                        <div class="col-sm-6 hide">
                            {!! Form::text($array_name . '[' . $row_index .'][' . $variation_array_name . '][' . $variation_row_index . '][dpp_inc_tax]', @num_format($variation->dpp_inc_tax), ['class' => 'form-control input-sm variable_dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'), 'required']); !!}
                        </div>
                    </td>
                    <td class="{{$class}} hide" style="display: none !important;">
                        {!! Form::hidden($array_name . '[' . $row_index .'][' . $variation_array_name . '][' . $variation_row_index . '][profit_percent]', @num_format($variation->profit_percent), ['class' => 'variable_profit_percent']); !!}
                    </td>
                    <td class="{{$class}}">
                        {!! Form::text($array_name . '[' . $row_index .'][' . $variation_array_name . '][' . $variation_row_index . '][default_sell_price]', @num_format($variation->default_sell_price), ['class' => 'form-control input-sm variable_dsp input_number', 'placeholder' => __('product.exc_of_tax'), 'required']); !!}

                        {!! Form::text($array_name . '[' . $row_index .'][' . $variation_array_name . '][' . $variation_row_index . '][sell_price_inc_tax]', @num_format($variation->sell_price_inc_tax), ['class' => 'form-control input-sm variable_dsp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'), 'required']); !!}
                    </td>
                    @if(!empty($allowed_group_prices))
                        @foreach($allowed_group_prices as $pg_id => $pg_name)
                            @php
                                $gp = $variation->group_prices->firstWhere('price_group_id', $pg_id);
                                $gp_value = $gp ? $gp->price_inc_tax : null;
                            @endphp
                            <td class="vp-text-right">
                                {!! Form::text($array_name . '[' . $row_index .'][' . $variation_array_name . '][' . $variation_row_index . '][group_prices][' . $pg_id . '][price]', $gp_value !== null ? @num_format($gp_value) : '', ['class' => 'form-control input-sm input_number', 'placeholder' => $pg_name]); !!}
                                {!! Form::hidden($array_name . '[' . $row_index .'][' . $variation_array_name . '][' . $variation_row_index . '][group_prices][' . $pg_id . '][price_type]', $gp && $gp->price_type ? $gp->price_type : 'fixed'); !!}
                            </td>
                        @endforeach
                    @endif
                    <td class="variation-images-cell">
                        @php 
                            $action = !empty($action) ? $action : '';
                        @endphp
                        @if($action !== 'duplicate')
                            @foreach($variation->media as $media)
                                <div class="img-thumbnail draggable-variant-image" 
                                     data-media-id="{{ $media->id }}" 
                                     data-media-url="{{ $media->display_url ?? asset('uploads/media/' . $media->file_name) }}"
                                     draggable="true"
                                     style="display: inline-block; margin: 5px; position: relative; cursor: move;">
                                    <span class="badge bg-red delete-media" data-href="{{ action([\App\Http\Controllers\ProductController::class, 'deleteMedia'], ['media_id' => $media->id])}}" style="cursor: pointer; position: absolute; top: 0; right: 0; z-index: 10; background-color: #dd4b39; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px;"><i class="fas fa-times"></i></span>
                                    {!! $media->thumbnail() !!}
                                </div>
                            @endforeach
                            {!! Form::file('edit_variation_images_' . $row_index . '_' . $variation_row_index . '[]',
                                 ['class' => 'variation_images', 'accept' => 'image/*', 'multiple']); !!}
                        @else
                            {!! Form::file('edit_variation_images_' . $row_index . '_' . $variation_row_index . '[]', 
                                ['class' => 'variation_images', 'accept' => 'image/*', 'multiple']); !!}
                        @endif
                    </td>
                    <td>
                        @if($action == 'edit' && $variation->id)
                            <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-error discontinue_variation_value_row" 
                                    data-variation-id="{{ $variation->id }}"
                                    title="Discontinue this variation (will not affect existing orders/invoices)">
                                <i class="fa fa-trash"></i>
                            </button>
                            <input type="hidden" name="discontinued_variations[]" class="discontinued_variation_id" value="" style="display:none;">
                        @else
                            <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-error remove_variation_value_row"><i class="fa fa-trash"></i></button>
                        @endif
                        <input type="hidden" class="variation_row_index" value="@if($action == 'duplicate'){{$loop->index}}@else{{0}}@endif">
                    </td>
                </tr>
            @empty
                &nbsp;
            @endforelse
            </tbody>
        </table>
    </td>
</tr>