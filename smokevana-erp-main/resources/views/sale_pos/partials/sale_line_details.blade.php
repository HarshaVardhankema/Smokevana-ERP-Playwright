<div class="table-responsive" style="max-height:55vh; overflow-y: auto; min-height: 55vh;">
    @php
    $overSellingQuantity = session()->get('business.overselling_qty_limit');
    $pos_settings = session()->get('business.pos_settings');
    $isOverSelling=json_decode($pos_settings)->allow_overselling??'' ;
    @endphp
    <input type="" class="hide is_over_selling_allowd" value={{$isOverSelling}} >
    <input type="" class="hide over_selling_qty" value={{$overSellingQuantity}}>

    <input type="text" value={{$sell->contact->id}} id='customer_id_id' class="hide"> 
    <input type="text" value={{$sell->id}} id="transaction_id_id" class="hide">
    @php
                            $can_edit=false;
                            if (auth()->user()->can('so.update') || auth()->user()->can('direct_sell.update')) {
                                $can_edit=true;
                            }
                        @endphp
    <table class="table @if (!empty($for_ledger)) table-slim mb-0  @else  @endif"
        @if (!empty($for_pdf)) style="width: 100%;" @endif    style=" background:@if($sell->type!='sales_order') rgb(239 254 238) @else rgb(255 252 217)@endif ;" id="sellsModalTable">
        {{-- Table Header --}}
        <thead style="position: sticky; top: 0; z-index: 9;" class="tw-text-sm">
            <tr @if (empty($for_ledger))  @endif id="dy_th_tr">
                <th>#</th>
                <th>{{ __('sale.product') }}</th>
                <th style="width: 100px">Fulfillment</th>
                @if(session()->get('business.enable_lot_number') == 1 && empty($for_ledger))
                    <th>{{ __('lang_v1.lot_n_expiry') }}</th>
                @endif
                {{-- @if ($sell->type == 'sales_order') --}}
                    {{-- <th>@lang('lang_v1.quantity_remaining')</th> --}}
                {{-- @endif --}}
                <th style="width: 100px">
                    @foreach (['silver', 'gold', 'platinum', 'lowest', 'diamond'] as $priceType)
                        <div class="{{ $priceType }}-price">
                            @lang("product.{$priceType}_price")
                        </div>
                    @endforeach
                </th>
                {{-- <th style="width: 120px">Action</th> --}}
                @if ($sell->picking_status=='PICKED')
                    <th>Varified</th>
                    <th>Picked</th>
                @endif
                <th style="width: 100px">{{ __('sale.qty') }}</th>
                <th style="width: 100px">{{ __('sale.unit_price') }}</th>
                
                @if (!empty($pos_settings['inline_service_staff']))
                    <th>@lang('restaurant.service_staff')</th>
                @endif
                <th style="width: 100px">{{ __('sale.discount') }}</th>
                <th style="width: 100px">{{ __('sale.tax') }}</th>
                <th style="width: 100px">{{ __('sale.price_inc_tax') }}</th>
                <th style="width: 100px">{{ __('sale.subtotal') }}</th>
                <th class="@if ($is_edit_allowed && $sell->payment_status == 'due'&&$sell->type !='sales_order'&&!$isLockModal || $sell->type =='sales_order' && $sell->status=='ordered'&&!$isLockModal&&$sell->picking_status!='PICKING'&&$customer_status!='inactive' && $can_edit) @else hide @endif handle_lock" ><i class="fa fa-trash" aria-hidden="true"></i></th>
            </tr>
        </thead>

        {{-- Table Body --}}
        <tbody>
            @foreach ($sell->sell_lines as $sell_line)
              @php
                $qty = $sell_line->quantity ?? 0;
            @endphp

            @if($qty <= 0)
                @continue  {{-- Skip 0-quantity products --}}
            @endif

            {{-- Gift card lines: product_id is null, render in simplified row --}}
            @if (is_null($sell_line->product_id))
                <tr class="sell-line-row gift-card-row" data-sellline-id="{{ $sell_line->id }}" data-transaction-id="{{ $sell->id }}">
                    <td class="check_box_td"></td>
                    <td>
                        <strong>{{ $sell_line->sell_line_note ?? __('Gift Card') }}</strong>
                    </td>
                    <td style="width: 100px; text-align: center;">
                        <span class="label bg-green" style="font-size: 9px; margin-left: 5px;">
                            <i class="fas fa-gift"></i> Gift Card
                        </span>
                    </td>
                    @if (session()->get('business.enable_lot_number') == 1 && empty($for_ledger))
                        <td>--</td>
                    @endif
                    <td style="width: 100px;"></td>
                    @if ($sell->picking_status=='PICKED')
                      <td class="text-center">-</td>
                      <td class="text-center">-</td>
                    @endif
                    <td class="quantity" style="width: 100px">
                        {{ @format_quantity($sell_line->quantity) }}
                    </td>
                    <td class="unit_price" style="width: 100px">
                        @format_currency($sell_line->unit_price_before_discount ?? $sell_line->unit_price)
                    </td>
                    @if (!empty($pos_settings['inline_service_staff']))
                        <td style="width: 100px"></td>
                    @endif
                    <td style="width: 100px">
                        @format_currency($sell_line->get_discount_amount())
                    </td>
                    <td style="width: 100px">
                        @format_currency($sell_line->item_tax ?? 0)
                    </td>
                    <td style="width: 100px">
                        @format_currency(($sell_line->unit_price_inc_tax ?? $sell_line->unit_price) * $sell_line->quantity)
                    </td>
                    <td style="width: 100px">
                        @format_currency(($sell_line->unit_price_inc_tax ?? $sell_line->unit_price) * $sell_line->quantity)
                    </td>
                    <td class="handle_lock"></td>
                </tr>
                @continue
            @endif

                @php
                    // Check if product is dropshipped (outsourced) - if so, make row read-only
                    $productSourceType = $sell_line->product->product_source_type ?? 'in_house';
                    $isDropshipped = ($productSourceType === 'dropshipped');
                    $rowReadOnly = $isDropshipped;
                    
                    // Get vendor info for dropshipped products
                    $vendorBadge = '';
                    if ($isDropshipped) {
                        // Safely get vendors - check if relationship is loaded and not empty
                        $productVendors = $sell_line->product->relationLoaded('vendors') ? $sell_line->product->vendors : collect([]);
                        $productVendor = $productVendors->first();
                        if ($productVendor) {
                            if ($productVendor->vendor_type === 'woocommerce') {
                                $vendorBadge = '<span class="label bg-blue" style="font-size: 9px; margin-left: 5px;"><i class="fas fa-globe"></i> WC Dropship</span>';
                            } else {
                                $vendorBadge = '<span class="label bg-purple" style="font-size: 9px; margin-left: 5px;"><i class="fas fa-user-tie"></i> ERP Dropship</span>';
                            }
                        } else {
                            $vendorBadge = '<span class="label bg-orange" style="font-size: 9px; margin-left: 5px;"><i class="fas fa-truck"></i> Dropship</span>';
                        }
                    } else {
                        $vendorBadge = '<span class="label bg-green" style="font-size: 9px; margin-left: 5px;"><i class="fas fa-warehouse"></i> In-House</span>';
                    }
                @endphp
                <tr data-ml={{$sell_line->product->ml??0}} data-ct={{$sell_line->product->ct??0}} data-locationTaxType='@json($sell_line->product->locationTaxType ?? [])' data-variation-id="{{$sell_line->variations->id}}" data-product-id="{{$sell_line->product->id}}" data-sellline-id="{{$sell_line->id}}" data-transaction-id="{{$sell->id}}" class="sell-line-row" id="invoice-line-row" @if($rowReadOnly) style="background-color: #f9f9f9;" @endif>
                    <td class="check_box_td">
                        <input type="checkbox" class="check_box_td_input" name="check_box_td_input" data-variation-id="{{$sell_line->variations->id}}" data-customer-id="{{$sell->contact->id}}">
                        <div id="Product_payload_row" class="hide">
                            <div class="base_unit_multiplier_data">1</div>
                            <div class="enable_stock_data">{{$sell_line->product->enable_stock}}</div>
                            <div class="item_tax_data">{{$sell_line->item_tax}}</div>
                            <div class="line_discount_amount_data">{{$sell_line->line_discount_amount}}</div>
                            <div class="line_discount_type_data">{{$sell_line->line_discount_type}}</div>
                            <div class="product_id_data">{{$sell_line->product_id}}</div>
                            <div class="product_type_data">{{$sell_line->product->type}}</div>
                            <div class="product_unit_id_data">{{$sell_line->product->unit_id}}</div>
                            <div class="quantity_data">{{$sell_line->quantity}}</div>
                            <div class="sell_line_note_data">{{$sell_line->sell_line_note}}</div>
                            <div class="tax_id_data">{{$sell_line->tax_id}}</div>
                            <div class="transaction_sell_lines_id_data">{{$sell_line->id}}</div>
                            <div class="unit_price_data">{{$sell_line->unit_price}}</div>
                            <div class="unit_price_inc_tax_data">{{$sell_line->unit_price_inc_tax}}</div>
                            <div class="variation_id_data">{{$sell_line->variation_id}}</div>
                            <div class="available_qty_data">@if($sell->type != 'sales_order'){{$sell_line->variations->variation_location_details[0]->qty_available+$sell_line->quantity}}@else{{$sell_line->variations->variation_location_details[0]->qty_available}}@endif</div>
                            <div class="sub_sku_data">{{$sell_line->variations->sub_sku}}</div>
                        </div>
                    </td>
                    <td>                        
                        <a href="#" @can('product.edit') data-href="/sells/pos/edit_price_product_modal/{{ $sell_line->product->id }}/0"
                            data-container=".view_modal" tabindex="2" class="ancher_list tw-text-black" @endcan >
                            {{ $sell_line->product->name }}
                            @if ($sell_line->product->type == 'variable')
                                - {{ $sell_line->variations->product_variation->name ?? '' }}
                                - {{ $sell_line->variations->name ?? '' }},
                            @endif
                            {{ $sell_line->variations->sub_sku ?? '' }}
                            @php
                                $brand = $sell_line->product->brand;
                            @endphp
                            {{-- @if (!empty($brand->name))
                                , {{ $brand->name }}
                            @endif --}}

                            @if (!empty($sell_line->sell_line_note))
                                <br> {{ $sell_line->sell_line_note }}
                            @endif
                            @if ($is_warranty_enabled && !empty($sell_line->warranties->first()))
                                <br><small>{{ $sell_line->warranties->first()->display_name ?? '' }} -
                                    {{ @format_date($sell_line->warranties->first()->getEndDate($sell->transaction_date)) }}</small>
                                @if (!empty($sell_line->warranties->first()->description))
                                    <br><small>{{ $sell_line->warranties->first()->description ?? '' }}</small>
                                @endif
                            @endif

                            @if (in_array('kitchen', $enabled_modules) && empty($for_ledger))
                                <br><span
                                    class="label @if ($sell_line->res_line_order_status == 'cooked') bg-red @elseif($sell_line->res_line_order_status == 'served') bg-green @else bg-light-blue @endif">@lang('restaurant.order_statuses.' . $sell_line->res_line_order_status)
                                </span>
                            @endif
                        </a>
                    </td>
                    <td style="width: 100px; text-align: center;">
                        {!! $vendorBadge !!}
                        @if($rowReadOnly)
                            <br><small class="text-muted" style="font-size: 9px;"><i class="fas fa-lock"></i> Read Only</small>
                        @endif
                    </td>
                    @if (session()->get('business.enable_lot_number') == 1 && empty($for_ledger))
                        <td>{{ $sell_line->lot_details->lot_number ?? '--' }}
                            @if (session()->get('business.enable_product_expiry') == 1 && !empty($sell_line->lot_details->exp_date))
                                ({{ @format_date($sell_line->lot_details->exp_date) }})
                            @endif
                        </td>
                    @endif
                    {{-- @if ($sell->type == 'sales_order') --}}
                        {{-- <td class='text-center'><span class="display_currency  " data-currency_symbol="false"
                                data-is_quantity="true">{{ $sell_line->quantity - $sell_line->so_quantity_invoiced }}</span> --}}
                            {{-- @if (!empty($sell_line->sub_unit))
                                {{ $sell_line->sub_unit->short_name }}
                            @else
                                {{ $sell_line->product->unit->short_name }}
                            @endif --}}
                        {{-- </td> --}}
                    {{-- @endif --}}

                    <td style="width: 100px;" class="text-white">
                        @foreach ($sell_line->variations->group_prices as $index => $group_price)
                            @php
                                $priceType = ['silver', 'gold', 'platinum', 'lowest', 'diamond'][$index] ?? 'no';
                            @endphp
                            <div class="{{ $priceType }}-price" style="background-color: rgb(151, 103, 0);" data-variation-id="{{$sell_line->variations->id}}">
                                @if ($group_price->price_inc_tax)
                                    @format_currency($group_price->price_inc_tax)
                                    @if ($priceType === 'lowest')
                                        <input type="text" name="lowest_price_input" class="form-control display_currency hidden" value="{{ $group_price->price_inc_tax }}">
                                    @endif
                                @else
                                    <span class="display_currency" data-currency_symbol="true">
                                        {{ $sell_line->unit_price_before_discount }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </td>
                    {{-- <td style="width: 120px">
                        <a href="#" data-href="/sells/pos/history_modal?customer_id={{$sell->contact->id}}&variation_id={{$sell_line->variations->id}}&dateRange=90"
                            data-container=".view_modal" tabindex="2" class="product_history">
                            📜Item History
                            </a>
                        </td> --}}
                    @if ($sell->picking_status=='PICKED')
                      <td class="text-center">{{$sell_line->verified_qty}}</td>
                      <td class="text-center">{{$sell_line->picked_quantity}}</td>
                    @endif
                    <td class="quantity" data-variation-id="{{$sell_line->variations->id}}" style="width: 100px">
                        @if (!empty($for_ledger))
                            {{ @format_quantity($sell_line->quantity) }}
                        @else
                            <input style="width: 80px" type="number" name="quantity" min="1" step="0.01"
                            @if ($rowReadOnly) disabled title="Dropship product - Read Only" @endif
                            @if (!auth()->user()->can('so.update')||!auth()->user()->can('direct_sell.update')) disabled @endif
                                @if ($is_edit_allowed && $sell->payment_status == 'due'&&$sell->type !='sales_order'&&!$isLockModal || $sell->type =='sales_order' && $sell->status=='ordered'&&!$isLockModal&&$sell->picking_status!='PICKING'&&$customer_status!='inactive') @else disabled @endif
                                class="form-control display_currency @if($rowReadOnly) readonly-dropship @endif" value="{{ $sell_line->quantity }}"
                                data-currency_symbol="true" required>
                        @endif

                    </td>
                    <td class="unit_price" data-variation-id="{{$sell_line->variations->id}}"
                        data-min-price={{ $sell_line->variations->group_prices[3]->price_inc_tax ??1}} style="width: 100px">
                        @if (!empty($for_ledger))
                            @format_currency($sell_line->unit_price_before_discount)
                        @else
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input style="width: 80px" type="text" name="unit_price"
                            @if ($rowReadOnly) disabled title="Dropship product - Read Only" @endif
                            @if (!auth()->user()->can('so.update')||!auth()->user()->can('direct_sell.update')) disabled @endif
                                @if ($is_edit_allowed && $sell->payment_status == 'due'&&$sell->type !='sales_order'&&!$isLockModal || $sell->type =='sales_order' && $sell->status=='ordered'&&!$isLockModal&&$sell->picking_status!='PICKING' &&$customer_status!='inactive') @else disabled @endif
                                class="form-control display_currency @if($rowReadOnly) readonly-dropship @endif" value="{{ number_format($sell_line->unit_price_before_discount, 2) }}"
                                data-currency_symbol="true" required>
                        </div>
                        @endif
                    </td>

                    
                    @if (!empty($pos_settings['inline_service_staff']))
                        <td style="width: 100px">
                            {{ $sell_line->service_staff->user_full_name ?? '' }}
                        </td>
                    @endif

                    <td class="discount" data-variation-id="{{$sell_line->variations->id}}" style="width: 170px">
    @if (!empty($for_ledger))
        @format_currency($sell_line->get_discount_amount())
    @else
    <div class="input-group" style="display: flex">
        {!! Form::text(
            'discount',
            $sell_line->line_discount_type == 'percentage' 
                ? number_format($sell_line->line_discount_amount, 2)
                : number_format($sell_line->get_discount_amount(), 2),
            [
                'class' => 'form-control input-sm inline_discounts input_number' . ($rowReadOnly ? ' readonly-dropship' : ''),
                'required',
                'style' => 'width:100px',
                'step' => 'any',
                'max' => 100,
                'min' => 0,
                'disabled' => $rowReadOnly ? true : (($is_edit_allowed && $sell->payment_status == 'due'&&$sell->type !='sales_order'&&!$isLockModal || $sell->type =='sales_order' && $sell->status=='ordered'&&!$isLockModal && $sell->picking_status!='PICKING' &&$customer_status!='inactive'&&$can_edit) ? false : true),
                'title' => $rowReadOnly ? 'Dropship product - Read Only' : ''
            ]
        ) !!}
        <select
            name="discount_type"
            class="form-control input-sm inline_discounts @if($rowReadOnly) readonly-dropship @endif"
            style="max-width: 70px; border-top-left-radius: 0; border-bottom-left-radius: 0;"
            @if ($rowReadOnly) disabled title="Dropship product - Read Only" @endif
            @if ($is_edit_allowed && $sell->payment_status == 'due'&&$sell->type !='sales_order'&&!$isLockModal || $sell->type =='sales_order' && $sell->status=='ordered'&&!$isLockModal && $sell->picking_status!='PICKING'&&$customer_status!='inactive' && $can_edit) @else disabled @endif
        >
            <option value="fixed" {{ $sell_line->line_discount_type == 'fixed' ? 'selected' : '' }}>$</option>
            <option value="percentage" {{ $sell_line->line_discount_type == 'percentage' ? 'selected' : '' }}>%</option>
        </select>
    </div>
    @endif
</td>


                    <td style="width: 100px">
                        @if (!empty($for_ledger))
                            @format_currency($sell_line->item_tax)
                        @else
                            <span class="display_currency " data-currency_symbol="true"
                                id="tax_rate">{{ $sell_line->item_tax }}</span>
                            <input style="width: 80px" type="text" name="tax_rate_col" disabled
                                class="form-control display_currency hide"value={{ $sell_line->item_tax }}
                                data-currency_symbol="true">
                        @endif
                        @if (!empty($taxes[$sell_line->tax_id]))
                            ( {{ $taxes[$sell_line->tax_id] }} )
                        @endif
                    </td>
                    <td style="width: 100px">
                        @if (!empty($for_ledger))
                            @format_currency($sell_line->unit_price_inc_tax)
                        @else
                            <span class="display_currency"
                                data-currency_symbol="true">{{ $sell_line->unit_price_inc_tax }}</span>
                        @endif
                    </td>
                    <td style="width: 100px">
                        @php
                        // Calculate discounted unit price based on discount type
                        if ($sell_line->line_discount_type == 'percentage') {
                            $unit_price_inc_discount = $sell_line->unit_price_before_discount * (1 - ($sell_line->line_discount_amount / 100));
                        } else {
                            $unit_price_inc_discount = $sell_line->unit_price_before_discount - $sell_line->get_discount_amount();
                        }
                        $unit_price_inc_tax = $unit_price_inc_discount + $sell_line->item_tax;
                        $sub_total_final = $unit_price_inc_tax * $sell_line->quantity;
                        @endphp
                        @if (!empty($for_ledger))
                        @format_currency($sub_total_final)
                        @else
                        <span class="display_currency" data-currency_symbol="true">{{ $sub_total_final }}</span>
                        @endif
                    </td>
                    <td class="@if ($rowReadOnly) hide @elseif ($is_edit_allowed && $sell->payment_status == 'due'&&$sell->type !='sales_order'&&!$isLockModal || $sell->type =='sales_order' && $sell->status=='ordered'&&!$isLockModal&&$sell->picking_status!='PICKING'&&$can_edit&&$customer_status!='inactive') @else hide @endif handle_lock" ><button @if ($sell->type != 'sales_order') class='delete_row_warning' @else class='delete_row' @endif data-sellline-id="{{$sell_line->id}}" @if($rowReadOnly) disabled title="Dropship product - Cannot delete" @endif><i class="fa fa-trash" aria-hidden="true" style="color: @if($rowReadOnly) #ccc @else red @endif"></i></button></td>
                    
                </tr>
                @if (!empty($sell_line->modifiers))
                    @foreach ($sell_line->modifiers as $modifier)
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                {{ $modifier->product->name }} - {{ $modifier->variations->name ?? '' }},
                                {{ $modifier->variations->sub_sku ?? '' }}
                            </td>
                            @if (session()->get('business.enable_lot_number') == 1)
                                <td>&nbsp;</td>
                            @endif
                            <td>{{ $modifier->quantity }}</td>
                            @if (!empty($pos_settings['inline_service_staff']))
                                <td>
                                    &nbsp;
                                </td>
                            @endif
                            <td>
                                @if (!empty($for_ledger))
                                    @format_currency($modifier->unit_price)
                                @else
                                    <span class="display_currency"
                                        data-currency_symbol="true">{{ $modifier->unit_price }}</span>
                                @endif
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                @if (!empty($for_ledger))
                                    @format_currency($modifier->item_tax)
                                @else
                                    <span class="display_currency"
                                        data-currency_symbol="true">{{ $modifier->item_tax }}</span>
                                @endif
                                @if (!empty($taxes[$modifier->tax_id]))
                                    ({{ $taxes[$modifier->tax_id] }})
                                @endif
                            </td>
                            <td>
                                @if (!empty($for_ledger))
                                    @format_currency($modifier->unit_price_inc_tax)
                                @else
                                    <span class="display_currency"
                                        data-currency_symbol="true">{{ $modifier->unit_price_inc_tax }}</span>
                                @endif
                            </td>
                            <td >
                                @if (!empty($for_ledger))
                                    @format_currency($modifier->quantity * $modifier->unit_price_inc_tax)
                                @else
                                    <span class="display_currency"
                                        data-currency_symbol="true">{{ $modifier->quantity * $modifier->unit_price_inc_tax }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>

        {{-- Table Footer --}}
        @if (auth()->user()->can('so.update') && auth()->user()->can('direct_sell.update') && $is_edit_allowed) 
        <tfoot class="hide" id="search_foot">
            <tr style="vertical-align: middle;">
                <td style="border: none;">
                </td>
                <td style="border: none;">
                    <div class="form-group showhidebutton">
                        <div class="input-group">
                            <div class="input-group-btn" style="margin-right: 10px;">
                                <button type="button" 
                                        class="btn btn-default bg-white btn-flat"
                                        data-toggle="modal" 
                                        data-target="#configure_search_modal"
                                        title="{{ __('lang_v1.configure_product_search') }}">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                            </div>
                            <input type="text" class="hide" value="{{$sell->contact->state}}" id="customer_state">
                            {!! Form::text('search_product', null, [
                                'class' => 'form-control mousetrap',
                                'data-customerID'=>$sell->contact_id,
                                'id' => 'search_product',
                                'placeholder' => __('lang_v1.search_product_placeholder'),
                                'disabled' => false,
                                'autofocus' => false,
                            ]) !!}
                        </div>
                    </div>
                </td>
                <td class="text-center hide">
                    <label style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
                        <input type="checkbox" 
                               style="display: none;" 
                               id="toggle_switch"
                               onchange="this.nextElementSibling.style.backgroundColor = this.checked ? '#4CAF50' : '#ccc'; 
                                        this.nextElementSibling.firstElementChild.style.transform = this.checked ? 'translateX(20px)' : 'translateX(0)';">
                        <div style="width: 40px; height: 20px; background-color: #ccc; border-radius: 20px; position: relative; transition: background-color 0.3s; margin-bottom: 5px;">
                            <div style="width: 18px; height: 18px; background-color: white; border-radius: 50%; position: absolute; top: 1px; left: 1px; transition: transform 0.3s;">
                            </div>
                        </div>
                        <span style="font-size: 10px; font-weight: 500; color: #333;">Enable Metrix</span>
                    </label>
                </td>
            </tr>
        </tfoot> 
        @endif
        
    </table>
</div>