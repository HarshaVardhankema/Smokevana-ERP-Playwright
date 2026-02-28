<br>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table bg-gray">
                <tr class="bg-green">
                    <th>@lang('product.sku')/@lang('product.barcode_no')</th>
                    @can('view_purchase_price')
                        <th class="hidden">@lang('product.default_purchase_price') (@lang('product.exc_of_tax'))</th>
                        <th>@lang('product.default_purchase_price') (@lang('product.inc_of_tax'))</th>
                    @endcan
                    @can('access_default_selling_price')
                        @can('view_purchase_price')
                            <th>@lang('product.profit_percent')</th>
                        @endcan
                        <th class="hidden">@lang('product.default_selling_price') (@lang('product.exc_of_tax'))</th>
                        <th>@lang('product.default_selling_price') (@lang('product.inc_of_tax'))</th>
                    @endcan
                    @if (!empty($allowed_group_prices))
                        {{-- <th>@lang('lang_v1.group_prices')</th> --}}
                        @foreach ($allowed_group_prices as $key => $value)
                            <th>{{ str_replace('Selling', ' ', $value) }}</th>
                        @endforeach

                    @endif
                    <th>@lang('lang_v1.variation_images')</th>
                </tr>
                @foreach ($product->variations as $variation)
                    <tr>
                        <td>
                            <strong>@lang('product.sku')</strong> - {{ $variation->sub_sku }}
                            <br>
                            <strong>@lang('product.barcode_no')</strong> - {{ $variation->var_barcode_no }}
                        </td>
                        @can('view_purchase_price')
                            <td class="hidden">
                                <span class="display_currency"
                                    data-currency_symbol="true">{{ $variation->default_purchase_price }}</span>
                            </td>
                            <td>
                                <span class="display_currency"
                                    data-currency_symbol="true">{{ $variation->dpp_inc_tax }}</span>
                            </td>
                        @endcan
                        @can('access_default_selling_price')
                            @can('view_purchase_price')
                                <td>
                                    {{ @num_format($variation->profit_percent) }}
                                </td>
                            @endcan
                            <td class="hidden">
                                <span class="display_currency"
                                    data-currency_symbol="true">{{ $variation->default_sell_price }}</span>
                            </td>
                            <td>
                                <span class="display_currency"
                                    data-currency_symbol="true">{{ $variation->sell_price_inc_tax }}</span>
                            </td>
                        @endcan
                        @if (!empty($allowed_group_prices))
                            {{-- <td class="td-full-width">
                                @foreach ($allowed_group_prices as $key => $value)
                                    <strong>{{ $value }}</strong> - @if (!empty($group_price_details[$variation->id][$key]))
                                        <span class="display_currency"
                                            data-currency_symbol="true">{{ $group_price_details[$variation->id][$key]['calculated_price'] }}</span>
                                    @else
                                        0
                                    @endif
                                    <br>
                                @endforeach
                            </td> --}}
                            @foreach ($allowed_group_prices as $key => $value)
                                <td class="td-full-width">
                                    @if (!empty($group_price_details[$variation->id][$key]))
                                        @if ($group_price_details[$variation->id][$key]['price_type'] == 'fixed')
                                            <span class="display_currency"
                                                data-currency_symbol="true">{{ $group_price_details[$variation->id][$key]['price'] }}</span>
                                        @elseif($group_price_details[$variation->id][$key]['price_type'] == 'percentage')
                                            {{ $group_price_details[$variation->id][$key]['price'] }} %
                                        @endif
                                    @else
                                        0
                                    @endif
                                    <br>
                                </td>
                            @endforeach
                        @endif
                        <td>
                            @foreach ($variation->media as $media)
                                {!! $media->thumbnail([60, 60], 'img-thumbnail') !!}
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
