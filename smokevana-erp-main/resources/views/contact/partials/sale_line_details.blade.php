<div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
    <table
        class="table @if (!empty($for_ledger)) table-slim mb-0 bg-table-row-color @else bg-table-row-color @endif"
        @if (!empty($for_pdf)) style="width: 100%;" @endif>
        <thead style="position: sticky;" class="tw-text-sm">
            <tr @if (empty($for_ledger))  @endif id="dy_th_tr">
                <th>#</th>
                <th>{{ __('sale.product') }}</th>
                @if (session()->get('business.enable_lot_number') == 1 && empty($for_ledger))
                    <th>{{ __('lang_v1.lot_n_expiry') }}</th>
                @endif
                @if ($sell->type == 'sales_order')
                    <th>@lang('lang_v1.quantity_remaining')</th>
                @endif

                <th style="width: 100px">{{ __('sale.unit_price') }}</th>
                <th style="width: 100px">{{ __('sale.qty') }}</th>
                @if (!empty($pos_settings['inline_service_staff']))
                    <th>
                        @lang('restaurant.service_staff')
                    </th>
                @endif

                <th style="width: 100px">{{ __('sale.discount') }}</th>
                <th style="width: 100px">{{ __('sale.tax') }}</th>
                <th style="width: 100px">{{ __('sale.price_inc_tax') }}</th>
                <th style="width: 100px">{{ __('sale.subtotal') }}</th>
            </tr>
        </thead>

        @foreach ($sell->sell_lines as $sell_line)
            <tr data-variation-id="{{ $sell_line->id }}" class="sell-line-row" id="invoice-line-row">
                <td>{{ $loop->iteration }}</td>

                <td>
                       {{ $sell_line->product->name }}
                        @if ($sell_line->product->type == 'variable')
                            - {{ $sell_line->variations->product_variation->name ?? '' }}
                            - {{ $sell_line->variations->name ?? '' }},
                        @endif
                        {{ $sell_line->variations->sub_sku ?? '' }}
                        @php
                            $brand = $sell_line->product->brand;
                        @endphp
                        @if (!empty($brand->name))
                            , {{ $brand->name }}
                        @endif

                        @if (!empty($sell_line->sell_line_note))
                            <br>
                              {{-- {{ $sell_line->sell_line_note }}  --}}
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

                </td>
                @if (session()->get('business.enable_lot_number') == 1 && empty($for_ledger))
                    <td>{{ $sell_line->lot_details->lot_number ?? '--' }}
                        @if (session()->get('business.enable_product_expiry') == 1 && !empty($sell_line->lot_details->exp_date))
                            ({{ @format_date($sell_line->lot_details->exp_date) }})
                        @endif
                    </td>
                @endif
                @if ($sell->type == 'sales_order')
                    <td><span class="display_currency  " data-currency_symbol="false"
                            data-is_quantity="true">{{ $sell_line->quantity - $sell_line->so_quantity_invoiced }}</span>
                        @if (!empty($sell_line->sub_unit))
                            {{ $sell_line->sub_unit->short_name }}
                        @else
                            {{ $sell_line->product->unit->short_name }}
                        @endif
                    </td>
                @endif
                <td class="unit_price" data-variation-id="{{ $sell_line->id }}"
                    data-min-price={{ $sell_line->variations->group_prices[3]->price_inc_tax ??1}} style="width: 100px">
                    @if (!empty($for_ledger))
                        @format_currency($sell_line->unit_price_before_discount)
                    @else
                        <input style="width: 80px" type="text" name="unit_price"
                            @if ($isLockModal||$sell->payment_status != 'due') disabled @endif
                            class="form-control display_currency"value={{ $sell_line->unit_price_before_discount }}
                            data-currency_symbol="true" required>
                    @endif
                </td>

                <td class="quantity" data-variation-id="{{ $sell_line->id }}" style="width: 100px">
                    @if (!empty($for_ledger))
                        {{ @format_quantity($sell_line->quantity) }}
                    @else
                        <input style="width: 80px" type="text" name="quantity"
                            @if ($isLockModal||$sell->payment_status != 'due') disabled @endif
                            class="form-control display_currency"value={{ $sell_line->quantity }}
                            data-currency_symbol="true" required>
                    @endif

                </td>
                @if (!empty($pos_settings['inline_service_staff']))
                    <td style="width: 100px">
                        {{ $sell_line->service_staff->user_full_name ?? '' }}
                    </td>
                @endif

                <td class="discount" data-variation-id="{{ $sell_line->id }}" style="width: 100px">
                    @if (!empty($for_ledger))
                        @format_currency($sell_line->get_discount_amount())
                    @else
                        <input style="width: 80px" type="text" name="discount"
                            @if ($isLockModal ||$sell->payment_status != 'due') disabled @endif
                            class="form-control display_currency"value={{ $sell_line->get_discount_amount() }}
                            data-currency_symbol="true">
                    @endif
                    @if ($sell_line->line_discount_type == 'percentage')
                        ({{ $sell_line->line_discount_amount }}%)
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
                    @if (!empty($for_ledger))
                        @format_currency($sell_line->quantity * $sell_line->unit_price_inc_tax)
                    @else
                        <span class="display_currency"
                            data-currency_symbol="true">{{ $sell_line->quantity * $sell_line->unit_price_inc_tax }}</span>
                    @endif
                </td>
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
                        <td>
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
    </table>
</div>
<script>
</script>
