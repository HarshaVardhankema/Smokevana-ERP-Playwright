@foreach( $variations as $variation)
    <tr @if(!empty($purchase_order_line)) data-purchase_order_id="{{$purchase_order_line->transaction_id}}" @endif @if(!empty($purchase_requisition_line)) data-purchase_requisition_id="{{$purchase_requisition_line->transaction_id}}" @endif >
        <input type="text" class="hidden_variation_id" value={{$variation->id }}>
        <td><span class="sr_number"></span></td>
        <td style="text-align: start;" class="tw-flex tw-justify-between">
            <a href="#" data-href="/sells/pos/edit_price_product_modal/{{ $product->id }}/0"
                            data-container=".view_modal" tabindex="2" class="ancher_list tw-text-black">
                            {{ $product->name  }}
                            @if ($product->type == 'variable')
                                - {{$variation->product_variation->name }}
                                - {{ $variation->name ?? '' }},
                            @endif
                            {{ $variation->sub_sku ?? '' }}
                            @php
                                $brand = $product->brand;
                            @endphp
                            @if (!empty($brand->name))
                                , {{ $brand->name }}
                            @endif


                        </a>
        </td>
        <td>
            @if($product->enable_stock == 1)
            <small class="text-muted" style="white-space: nowrap;">@if(!empty($variation->variation_location_details->first())) {{ (int) $variation->variation_location_details->first()->qty_available }}@else 0 @endif</small>
        @endif
        </td>
        <td></td>
        <td style="width: 120px">
                        <a href="#" data-href="/sells/pos/history_modal/{{ $product->id }}"
                            data-container=".view_modal" tabindex="2" class="product_history">
                            📜Item History
                        </a>
                    </td>


        @php
        $check_decimal = 'false';
        if($product->unit->allow_decimal == 0){
            $check_decimal = 'true';
        }
        $currency_precision = session('business.currency_precision', 2);
        $quantity_precision = session('business.quantity_precision', 2);

        $quantity_value = !empty($purchase_order_line) ? $purchase_order_line->quantity : 1;

        $quantity_value = !empty($purchase_requisition_line) ? $purchase_requisition_line->quantity - $purchase_requisition_line->po_quantity_purchased : $quantity_value;
        $max_quantity = !empty($purchase_order_line) ? $purchase_order_line->quantity - $purchase_order_line->po_quantity_purchased : 0;

        $max_quantity = !empty($purchase_requisition_line) ? $purchase_requisition_line->quantity - $purchase_requisition_line->po_quantity_purchased : $max_quantity;

        $quantity_value = !empty($imported_data) ? $imported_data['quantity'] : $quantity_value;
        $input_value = !empty($max_quantity) ? $max_quantity : $quantity_value;

    @endphp
        <td class="unit_price" data-variation-id="new" >
            @php
                $pp_without_discount = !empty($purchase_order_line) ? $purchase_order_line->pp_without_discount/$purchase_order->exchange_rate : $variation->default_purchase_price;

                $discount_percent = !empty($purchase_order_line) ? $purchase_order_line->discount_percent : 0;

                $purchase_price = !empty($purchase_order_line) ? $purchase_order_line->purchase_price/$purchase_order->exchange_rate : $variation->default_purchase_price;

                $tax_id = !empty($purchase_order_line) ? $purchase_order_line->tax_id : $product->tax;

                $tax_id = !empty($imported_data['tax_id']) ? $imported_data['tax_id'] : $tax_id;

                $pp_without_discount = !empty($imported_data['unit_cost_before_discount']) ? $imported_data['unit_cost_before_discount'] : $pp_without_discount;

                $discount_percent = !empty($imported_data['discount_percent']) ? $imported_data['discount_percent'] : $discount_percent;
            @endphp
            <div class="input-group">
                <span class="input-group-addon">$</span>

                {!! Form::number('unit_price',number_format($pp_without_discount, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
          [
              'class' => 'form-control input-sm input_number',
              'required',
              'min' => '0.01',
              'step' => 'any',
          ]); !!}
            </div>
          
      
            @if(!empty($last_purchase_line))
                <br class="hide">
                <small class="text-muted hide">@lang('lang_v1.prev_unit_price'): @format_currency($last_purchase_line->pp_without_discount)</small>
            @endif
        </td>
        <td>
            <div style="width:80px; margin: auto;">
                @if(!empty($purchase_order_line))
                {!! Form::hidden('purchases[' . $row_count . '][purchase_order_line_id]', $purchase_order_line->id ); !!}
            @endif

            @if(!empty($purchase_requisition_line))
                {!! Form::hidden('purchases[' . $row_count . '][purchase_requisition_line_id]', $purchase_requisition_line->id ); !!}
            @endif

            {!! Form::hidden('purchases[' . $row_count . '][product_id]', $product->id ); !!}
            {!! Form::hidden('purchases[' . $row_count . '][variation_id]', $variation->id , ['class' => 'hidden_variation_id']); !!}

           
             @if (!empty($purchase_order_line))
             <div>
                {{-- <small>Ordered Qty</small>
                 <strong> {{$quantity_value}}</strong>
             </div> --}}
              @endif
            <input type="text" 
                name="purchases[{{$row_count}}][quantity]" 
                value=""
                {{-- {{@format_quantity($input_value)}} --}}
                class="form-control input-sm purchase_quantity input_number mousetrap"
                {{-- required --}}
                min=1
                data-rule-abs_digit={{$check_decimal}}
                data-msg-abs_digit="{{__('lang_v1.decimal_value_not_allowed')}}"
                @if(!empty($max_quantity))
                    data-rule-max-value="{{$max_quantity}}"
                    data-msg-max-value="{{__('lang_v1.max_quantity_quantity_allowed', ['quantity' => $max_quantity])}}" 
                @endif
            >


            <input type="hidden" class="base_unit_cost" value="{{$variation->default_purchase_price}}">
            <input type="hidden" class="base_unit_selling_price" value="{{$variation->sell_price_inc_tax}}">

            <input type="hidden" name="purchases[{{$row_count}}][product_unit_id]" value="{{$product->unit->id}}">
            @if(!empty($sub_units))
                <select name="purchases[{{$row_count}}][sub_unit_id]" class="form-control input-sm sub_unit hide">
                    @foreach($sub_units as $key => $value)
                        <option value="{{$key}}" data-multiplier="{{$value['multiplier']}}">
                            {{$value['name']}}
                        </option>
                    @endforeach
                </select>
            @else 
                {{ $product->unit->short_name }}
            @endif

            @if(!empty($product->second_unit))
                @php
                    $secondary_unit_quantity = !empty($purchase_requisition_line) ? $purchase_requisition_line->secondary_unit_quantity : "";
                @endphp
                <br>
                <span style="white-space: nowrap;">
                @lang('lang_v1.quantity_in_second_unit', ['unit' => $product->second_unit->short_name])*:</span><br>
                <input type="text" 
                name="purchases[{{$row_count}}][secondary_unit_quantity]" 
                @if($secondary_unit_quantity !== '')value="{{@format_quantity($secondary_unit_quantity)}}" @endif
                class="form-control input-sm input_number"
                required>
            @endif
            </div>
        </td>

        {{-- <td class="unit_price" data-variation-id="new" --}}
                        {{-- data-min-price={{ $sell_line->variations->group_prices[3]->price_inc_tax ??1}}  --}}
                        {{-- style="width: 100px">
                       
                            <input style="width: 80px" type="text" name="unit_price"
                                @if ($isLockModal||$sell->payment_status != 'due'&&$sell->type!='sales_order') disabled @endif
                                class="form-control display_currency"value={{ $sell_line->unit_price_before_discount }}
                                data-currency_symbol="true" required>
            </td> --}}
        
        <td style="width: 170px">
            <div class="input-group" style="display: flex">
                {!! Form::text('purchases[' . $row_count . '][discount_percent]', number_format($discount_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm inline_discounts input_number', 'required', 'min' => 0,'style'=>'width:100px',
                'step' => 'any','max' => 100]);  !!}
                <select name="purchases[{{$row_count}}][row_discount_type]" class="form-control input-sm inline_discounts" style="width: 52px; min-width: 52px; flex: 0 0 52px; padding: 2px 4px; text-align: center; -webkit-appearance: menulist; appearance: menulist; border-top-left-radius: 0; border-bottom-left-radius: 0;">
                    <option value="fixed" selected>$</option><option value="percentage">%</option>
                </select>
            </div>
            @if(!empty($last_purchase_line))
                <br class="hide">
                <small class="text-muted hide">
                    @lang('lang_v1.prev_discount'): 
                    {{@num_format($last_purchase_line->discount_percent)}}%
                </small>
            @endif
        </td>
        <td 
        @if (empty($is_purchase_order))
            class="tw-hidden"
        @endif >
        <div class="input-group">
            <span class="input-group-addon">
                <i>$</i>
            </span>
            {!! Form::text('purchases[' . $row_count . '][purchase_price]',
            number_format($purchase_price, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost input_number', 'required', 'min' => '0.01',
            'step' => 'any',]); !!}
        </div>
            </td>
        <td class="{{$hide_tax}}">
            <span class="row_subtotal_before_tax display_currency">0</span>
            <input type="hidden" class="row_subtotal_before_tax_hidden" value=0>
        </td>
        <td class="{{$hide_tax}}">
            <div class="input-group">
                <select name="purchases[{{ $row_count }}][purchase_line_tax_id]" class="form-control select2 input-sm purchase_line_tax_id" placeholder="'Please Select'">
                    <option value="" data-tax_amount="0" @if( $hide_tax == 'hide' )
                    selected @endif >@lang('lang_v1.none')</option>
                    @foreach($taxes as $tax)
                        <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $tax_id == $tax->id && $hide_tax != 'hide') selected @endif >{{ $tax->name }}</option>
                    @endforeach
                </select>
                {!! Form::hidden('purchases[' . $row_count . '][item_tax]', 0, ['class' => 'purchase_product_unit_tax']); !!}
                <span class="input-group-addon purchase_product_unit_tax_text">
                    0.00</span>
            </div>
        </td>
        <td class="{{$hide_tax}}">
            @php
                $dpp_inc_tax = number_format($variation->dpp_inc_tax, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);
                if($hide_tax == 'hide'){
                    $dpp_inc_tax = number_format($variation->default_purchase_price, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);
                }

                $dpp_inc_tax = !empty($purchase_order_line) ? number_format($purchase_order_line->purchase_price_inc_tax/$purchase_order->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator) : $dpp_inc_tax;

            @endphp
            {!! Form::text('purchases[' . $row_count . '][purchase_price_inc_tax]', $dpp_inc_tax, ['class' => 'form-control input-sm purchase_unit_cost_after_tax input_number', 'required']); !!}
        </td>
        <td>
            <span>$</span>
            <span class="row_subtotal_after_tax display_currency">0</span>
            <input type="hidden" class="row_subtotal_after_tax_hidden" value=0>
        </td>
        @if (!empty($is_purchase_order))
        <td class="@if(!session('business.enable_editing_product_from_purchase') || !empty($is_purchase_order)) hide @endif">
            {!! Form::text('purchases[' . $row_count . '][profit_percent]', number_format($variation->profit_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm input_number profit_percent', 'required']); !!}
        </td> 
        @endif
        
        @if(empty($is_purchase_order))
        <td class="hide">
            @if(session('business.enable_editing_product_from_purchase'))
                {!! Form::text('purchases[' . $row_count . '][default_sell_price]', number_format($variation->sell_price_inc_tax, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm input_number default_sell_price', 'required']); !!}
            @else
                {{ number_format($variation->sell_price_inc_tax, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
            @endif
        </td>
        @if(session('business.enable_lot_number'))
            @php
                $lot_number = !empty($imported_data['lot_number']) ? $imported_data['lot_number'] : null;
            @endphp
            <td>
                {!! Form::text('purchases[' . $row_count . '][lot_number]', $lot_number, ['class' => 'form-control input-sm']); !!}
            </td>
        @endif
        @if(session('business.enable_product_expiry'))
            <td style="text-align: left;">

                {{-- Maybe this condition for checkin expiry date need to be removed --}}
                @php
                    $expiry_period_type = !empty($product->expiry_period_type) ? $product->expiry_period_type : 'month';
                @endphp
                @if(!empty($expiry_period_type))
                <input type="hidden" class="row_product_expiry" value="{{ $product->expiry_period }}">
                <input type="hidden" class="row_product_expiry_type" value="{{ $expiry_period_type }}">

                @if(session('business.expiry_type') == 'add_manufacturing')
                    @php
                        $hide_mfg = false;
                    @endphp
                @else
                    @php
                        $hide_mfg = true;
                    @endphp
                @endif

                @php
                    $mfg_date = !empty($imported_data['mfg_date']) ? $imported_data['mfg_date'] : null;
                    $exp_date = !empty($imported_data['exp_date']) ? $imported_data['exp_date'] : null;
                @endphp

                <b class="@if($hide_mfg) hide @endif"><small>@lang('product.mfg_date'):</small></b>
                <div class="input-group @if($hide_mfg) hide @endif">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::text('purchases[' . $row_count . '][mfg_date]', $mfg_date, ['class' => 'form-control input-sm expiry_datepicker mfg_date', 'readonly']); !!}
                </div>
                <b><small>@lang('product.exp_date'):</small></b>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::text('purchases[' . $row_count . '][exp_date]', $exp_date, ['class' => 'form-control input-sm expiry_datepicker exp_date', 'readonly']); !!}
                </div>
                @else
                <div class="text-center">
                    @lang('product.not_applicable')
                </div>
                @endif
            </td>
        @endif
        @endif
        <?php $row_count++ ;?>

        <td><i class="fa fa-times remove_purchase_entry_row text-danger" title="Remove" style="cursor:pointer;"></i></td>
    </tr>
@endforeach

<input type="hidden" id="row_count" value="{{ $row_count }}">