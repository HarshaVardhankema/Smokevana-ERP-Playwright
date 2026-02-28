<div style="padding: 0; background: #FFF; width: 100%; overflow-x: auto;">
    <table class="table table-bordered table-hover" style="margin: 0; background: #FFF; width: 100%; border-collapse: collapse;">
        <thead style="background: #F7F8F8; border-bottom: 2px solid #D5D9D9;">
            <tr>
                <th style="padding: 10px; text-align: left; font-weight: 600; color: #0F1111; border: 1px solid #D5D9D9;">Image</th>
                <th style="padding: 10px; text-align: left; font-weight: 600; color: #0F1111; border: 1px solid #D5D9D9;">Variations</th>
                <th style="padding: 10px; text-align: left; font-weight: 600; color: #0F1111; border: 1px solid #D5D9D9;">SKU</th>
                <th style="padding: 10px; text-align: left; font-weight: 600; color: #0F1111; border: 1px solid #D5D9D9;">Cost</th>
                <th style="padding: 10px; text-align: left; font-weight: 600; color: #0F1111; border: 1px solid #D5D9D9;">Selling Price</th>
                @if(in_array('group_pricing', $enabled_modules) && $price_groups->isNotEmpty())
                    @foreach($price_groups->reverse() as $price_group)
                        @php
                            // Extract first word from camelCase name (e.g., "SilverPriceGroup" -> "Silver")
                            $formattedName = preg_replace('/([a-z])([A-Z])/', '$1 $2', $price_group->name);
                            $formattedName = explode(' ', $formattedName)[0];
                        @endphp
                        <th style="padding: 10px; text-align: left; font-weight: 600; color: #0F1111; border: 1px solid #D5D9D9;">{{ $formattedName }}</th>
                    @endforeach
                @endif
                <th style="padding: 10px; text-align: left; font-weight: 600; color: #0F1111; border: 1px solid #D5D9D9;">Current Stock</th>
            </tr>
        </thead>
        <tbody>
    @foreach ($variation as $variant)
        @php
            $group_price = $variant->group_prices;
            
            // Create a map of price_group_id => price_inc_tax for easy lookup
            $price_map = [];
            foreach ($group_price as $price) {
                $price_map[$price['price_group_id']] = $price['price_inc_tax'];
            }
        @endphp

            <tr style="background: #FFF; border-bottom: 1px solid #E7E7E7;">
                <td style="padding: 10px; border: 1px solid #E7E7E7;">
                    @if($variant->media->isNotEmpty())
                        <img src="{{ $variant->media->first()->display_url }}" alt="{{ $variant->name }}" width="50px" height="50px" style="border-radius: 4px; object-fit: cover; border: 1px solid #D5D9D9;">
                    @elseif($variant->product && $variant->product->image_url)
                        <img src="{{ $variant->product->image_url }}" alt="{{ $variant->name }}" width="50px" height="50px" style="border-radius: 4px; object-fit: cover; border: 1px solid #D5D9D9;">
                    @else
                        <div style="width: 50px; height: 50px; background: #F7F8F8; border: 1px solid #D5D9D9; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #888C8C;">
                            <i class="fa fa-image"></i>
                        </div>
                    @endif
                </td>
                <td style="padding: 10px; border: 1px solid #E7E7E7; color: #0F1111; font-weight: 500;">{{ $variant->name }}</td>
                <td style="padding: 10px; border: 1px solid #E7E7E7; color: #0F1111;">{{ $variant->sub_sku }}</td>
                <td style="padding: 10px; border: 1px solid #E7E7E7; white-space: nowrap;">
                    <button type="button" class="btn btn-sm btn-link toggle-cost-btn" data-target="cost-{{ $variant->id }}" style="padding: 0; border: none; background: none; cursor: pointer; color: #666; vertical-align: middle;">
                        <i class="fa fa-eye"></i>
                    </button>
                    <span id="cost-{{ $variant->id }}" class="variant-cost-value" style="display: none; margin-left: 8px; color: #0F1111; font-weight: 500;">{{ session('business.currency_symbol', '$') }}{{ number_format((float)($variant->default_purchase_price ?? 0), 2) }}</span>
                </td>
                <td style="padding: 10px; border: 1px solid #E7E7E7; color: #0F1111; font-weight: 500;">${{ number_format((float) $variant->sell_price_inc_tax,2 )}}</td>
                @if(in_array('group_pricing', $enabled_modules) && $price_groups->isNotEmpty())
                    @foreach($price_groups->reverse() as $price_group)
                        <td style="padding: 10px; border: 1px solid #E7E7E7; color: #0F1111;">
                            @if(isset($price_map[$price_group->id]) && $price_map[$price_group->id] != '')
                                ${{ number_format((float) $price_map[$price_group->id], 2) }}
                            @else
                                <span style="color: #888C8C;">N/A</span>
                            @endif
                        </td>
                    @endforeach
                @endif
                <td style="padding: 10px; border: 1px solid #E7E7E7; color: #0F1111;">
                    @if($variant->variation_location_details->isNotEmpty())
                        {{ $variant->variation_location_details->first()->in_stock_qty }}
                    @else
                        <span style="color: #888C8C;">N/A</span>
                    @endif
                </td>
            </tr>
    @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('.toggle-cost-btn').on('click', function() {
            var targetId = $(this).data('target');
            var $costSpan = $('#' + targetId);
            var $icon = $(this).find('i');
            
            if ($costSpan.is(':visible')) {
                $costSpan.hide();
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                $costSpan.show();
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });
    });
</script>