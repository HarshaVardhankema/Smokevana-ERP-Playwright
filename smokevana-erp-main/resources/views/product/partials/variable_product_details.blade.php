<div class="vp-card vp-card--full amazon-variations-section">
    <h3 class="vp-card__title">@lang('product.variations')</h3>
    <div class="vp-table-wrap variations-table-scroll-wrapper">
        <table class="table amazon-variations-table vp-table">
            <thead>
                <tr>
                    <th>@lang('product.variations')</th>
                    <th>@lang('product.sku') / @lang('product.barcode_no')</th>
                    @can('view_purchase_price')
                        <th class="hidden">@lang('product.default_purchase_price') (@lang('product.exc_of_tax'))</th>
                        <th class="vp-text-right">@lang('product.default_purchase_price') (@lang('product.inc_of_tax'))</th>
                    @endcan
                    @can('access_default_selling_price')
                        @can('view_purchase_price')
                            <th class="vp-text-right">@lang('product.profit_percent')</th>
                        @endcan
                        <th class="hidden">@lang('product.default_selling_price') (@lang('product.exc_of_tax'))</th>
                        <th class="vp-text-right">@lang('product.default_selling_price') (@lang('product.inc_of_tax'))</th>
                    @endcan
                    @if (!empty($allowed_group_prices))
                        @foreach ($allowed_group_prices as $key => $value)
                            <th class="vp-text-right">{{ preg_replace('/([a-z])([A-Z])/', '$1 $2', $value) }}</th>
                        @endforeach
                    @endif
                    <th class="vp-text-center">@lang('lang_v1.variation_images')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($product->variations->filter(function($v) { return !$v->discontinued; }) as $variation)
                    <tr class="vp-table-row">
                        <td>
                            <strong class="vp-variation-name">{{ $variation->product_variation->name }}</strong>
                            <span class="vp-muted"> – {{ $variation->name }}</span>
                        </td>
                        <td>
                            <div class="vp-variation-meta">
                                <span class="vp-muted vp-variation-meta__label">@lang('product.sku'):</span>
                                <span>{{ $variation->sub_sku }}</span>
                            </div>
                            <div class="vp-variation-meta">
                                <span class="vp-muted vp-variation-meta__label">@lang('product.barcode_no'):</span>
                                <span>{{ $variation->var_barcode_no ?: '--' }}</span>
                            </div>
                        </td>
                        @can('view_purchase_price')
                            <td class="hidden">
                                <span class="display_currency" data-currency_symbol="true">{{ $variation->default_purchase_price }}</span>
                            </td>
                            <td class="vp-text-right">
                                <span class="display_currency" data-currency_symbol="true">{{ $variation->dpp_inc_tax }}</span>
                            </td>
                        @endcan
                        @can('access_default_selling_price')
                            @can('view_purchase_price')
                                <td class="vp-text-right">{{ @num_format($variation->profit_percent) }}%</td>
                            @endcan
                            <td class="hidden">
                                <span class="display_currency" data-currency_symbol="true">{{ $variation->default_sell_price }}</span>
                            </td>
                            <td class="vp-text-right vp-cell-price">
                                <span class="display_currency" data-currency_symbol="true">{{ $variation->sell_price_inc_tax }}</span>
                            </td>
                        @endcan
                        @if (!empty($allowed_group_prices))
                            @foreach ($allowed_group_prices as $key => $value)
                                <td class="vp-text-right">
                                    @if (!empty($group_price_details[$variation->id][$key]))
                                        @if ($group_price_details[$variation->id][$key]['price_type'] == 'fixed')
                                            <span class="display_currency" data-currency_symbol="true">{{ $group_price_details[$variation->id][$key]['price'] }}</span>
                                        @elseif($group_price_details[$variation->id][$key]['price_type'] == 'percentage')
                                            {{ $group_price_details[$variation->id][$key]['price'] }}%
                                        @endif
                                    @else
                                        <span class="vp-muted">0</span>
                                    @endif
                                </td>
                            @endforeach
                        @endif
                        <td class="vp-text-center vp-cell-images">
                            @foreach ($variation->media as $media)
                                <span class="variation-image-thumb vp-variation-thumb"
                                      onclick="if(typeof changeMainImage === 'function') { changeMainImage('{{ $media->display_url }}', this); }">
                                    {!! $media->thumbnail([60, 60], 'img-thumbnail') !!}
                                </span>
                            @endforeach
                            @if($variation->media->isEmpty())
                                <span class="vp-muted">--</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
.view-product-modal .variations-table-scroll-wrapper { max-height: 50vh; overflow-y: auto; }
.view-product-modal .vp-variation-name { color: #007185; }
.view-product-modal .vp-variation-meta { margin-bottom: 0.2rem; }
.view-product-modal .vp-variation-meta__label { font-size: 0.75rem; }
.view-product-modal .vp-cell-price { font-weight: 700; color: #b12704; font-size: 1rem; }
.view-product-modal .vp-variation-thumb { display: inline-block; margin: 0.15rem; cursor: pointer; border: 2px solid #d5d9d9; border-radius: 0.25rem; padding: 0.15rem; transition: border-color 0.2s, box-shadow 0.2s; vertical-align: middle; }
.view-product-modal .vp-variation-thumb:hover { border-color: #ff9900; box-shadow: 0 2px 6px rgba(255,153,0,0.25); }
.view-product-modal .vp-table-row:hover { background-color: #f7f8f8; }
.view-product-modal .vp-text-center { text-align: center; }
</style>
