
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

<tr class="variation_row">
    <td>
        <button type="button" class="btn-variation-delete delete_complete_row" title="Delete Variation"><i class="fa fa-trash"></i></button>
    </td>
    <td>
        {!! Form::select('product_variation[' . $row_index .'][variation_template_id]', $variation_templates, null, ['class' => 'form-control input-sm variation_template', 'required']); !!}
        <input type="hidden" class="row_index" value="{{$row_index}}">
        <div class="form-group variation_template_values_div mt-15 hide">
            <label>@lang('lang_v1.select_variation_values')</label>
            {!! Form::select('product_variation[' . $row_index .'][variation_template_values][]', [], null, ['class' => 'form-control input-sm variation_template_values', 'multiple', 'style' => 'width: 100%;']); !!}
        </div>
    </td>

    <td>
        <table class="table table-condensed table-bordered variation_value_table table-striped">
            <style>
                    .variation_value_table tbody tr {
                            border-bottom: 2px solid #ccc;
                            padding-bottom: 10px;
                        }
                </style>
            <thead>
            <tr>
                <th >@lang('product.sku') @show_tooltip(__('tooltip.sub_sku'))</th>
                <th >BarCode</th>
                <th >Sale Limit</th>
                <th>Name</th>
                <th class="{{$class}}">Cost
                    {{-- @lang('product.default_purchase_price') --}}
                    <br/>
                    <span class=" hide pull-left"><small><i>@lang('product.exc_of_tax')</i></small></span>

                    <span class=" hide pull-right"><small><i>@lang('product.inc_of_tax')</i></small></span>
                </th>
                <th class="hide" style="display: none !important;">@lang('product.profit_percent')</th>
                    <th class="{{$class}}">Selling Price</th>
                    <!-- &nbsp;&nbsp;<b><i class="fa fa-info-circle" aria-hidden="true" data-toggle="popover" data-html="true" data-trigger="hover" data-content="<p class='text-primary'>Drag the mouse over the table cells to copy input values</p>" data-placement="top"></i></b> -->
                </th>
                <th>@lang('lang_v1.variation_images')</th>
                <style>.add_variation_value_row {
                    display: none;
                }</style>
                <th><button type="button" class="btn-variation-add add_variation_value_row" title="Add Variation Value"><i class="fa fa-plus"></i></button></th>
                <script>
                    $(document).on('change', '.variation_template', function() {
                        var tr_obj = $(this).closest('tr');
                        var add_btn = tr_obj.find('.add_variation_value_row');
                        
                        if ($(this).val() !== '') {
                            add_btn.show(); // Show the + button when a template is selected
                        } else {
                            add_btn.hide(); 
                        }
                    });
                $(document).ready(function() {
                    $('.variation_template').each(function() {
                        var tr_obj = $(this).closest('tr');
                        var add_btn = tr_obj.find('.add_variation_value_row');
                        
                        if ($(this).val() === '') {
                            add_btn.hide();
                        }
                    });
                });
                </script>
            </tr>
            </thead>

            <tbody>
            <tr>
                
                <td>
                    {!! Form::text('product_variation[' . $row_index .'][variations][0][sub_sku]', null, ['class' => 'form-control input-sm']); !!}
                </td>
                <td >
                    {!! Form::text('product_variation[' . $row_index .'][variations][0][var_barcode_no]', null, ['class' => 'form-control input-sm']); !!}
                </td>
                <td >
                    {!! Form::text('product_variation[' . $row_index .'][variations][0][var_maxSaleLimit]', null, ['class' => 'form-control input-sm']); !!}
                </td>
                <td>
                    {!! Form::text('product_variation[' . $row_index .'][variations][0][value]', null, ['class' => 'form-control input-sm variation_value_name', 'required']); !!}
                </td>
                <td class="{{$class}}">
                    <div class="">
                        {!! Form::text('product_variation[' . $row_index .'][variations][0][default_purchase_price]', $default, ['class' => 'form-control input-sm variable_dpp input_number', 'placeholder' => __('product.exc_of_tax'), 'required']); !!}
                    </div>

                    <div class=" hide">
                        <div class="input-group">
                            {!! Form::text('product_variation[' . $row_index .'][variations][0][dpp_inc_tax]', $default, ['class' => 'form-control input-sm variable_dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'), 'required']); !!}
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default bg-white btn-flat apply-all btn-sm p-5-5" data-toggle="tooltip" title="@lang('lang_v1.apply_all')" data-target-class=".variable_dpp_inc_tax"><i class="fas fa-check-double"></i></button>
                            </span>
                        </div>
                    </div>
                </td>
                <td class="hide" style="display: none !important;">
                    <div class="input-group">
                        {!! Form::text('product_variation[' . $row_index .'][variations][0][profit_percent]', $profit_percent, ['class' => 'form-control input-sm variable_profit_percent input_number', 'required']); !!}
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default bg-white btn-flat apply-all btn-sm p-5-5" data-toggle="tooltip" title="@lang('lang_v1.apply_all')" data-target-class=".variable_profit_percent"><i class="fas fa-check-double"></i></button>
                        </span>
                    </div>
                </td>
                <td class="{{$class}}">
                    {!! Form::text('product_variation[' . $row_index .'][variations][0][default_sell_price]', $default, ['class' => 'form-control input-sm variable_dsp input_number', 'placeholder' => __('product.exc_of_tax'), 'required']); !!}

                     {!! Form::text('product_variation[' . $row_index .'][variations][0][sell_price_inc_tax]', $default, ['class' => 'form-control input-sm variable_dsp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'), 'required']); !!}
                </td>
                <td>{!! Form::file('variation_images_' . $row_index .'_0[]', ['class' => 'variation_images', 
                    'accept' => 'image/*', 'multiple']); !!}</td>
                <td>
                    <button type="button" class="btn-variation-remove remove_variation_value_row" title="Remove Variation Value"><i class="fa fa-trash"></i></button>
                    <input type="hidden" class="variation_row_index" value="0">
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>