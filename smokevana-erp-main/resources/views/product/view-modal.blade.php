<div class="modal-dialog modal-xl view-product-modal" role="document">
    <div class="modal-content">
        <div class="vp-header">
            @php
                $front_end_url = config('app.front-url') ?? '';
                $brand_url = $product->brand->brand_url ?? null;
                $product_url = $brand_url ? $brand_url.'/product/'.$product->slug : $front_end_url.'/product/'.$product->slug;
                $sourceType = $product->product_source_type ?? 'in_house';
                $fulfillmentBadge = '<span class="vp-badge vp-badge--green"><i class="fas fa-warehouse"></i> In-House</span>';
                if ($sourceType === 'dropshipped') {
                    $vendor = $product->vendors->first();
                    if ($vendor) {
                        if ($vendor->vendor_type === 'woocommerce') {
                            $fulfillmentBadge = '<span class="vp-badge vp-badge--blue"><i class="fas fa-globe"></i> Dropship - WooCommerce</span><small class="vp-badge-vendor">(' . e($vendor->name) . ')</small>';
                        } else {
                            $fulfillmentBadge = '<span class="vp-badge vp-badge--purple"><i class="fas fa-user-tie"></i> Dropship - ERP Portal</span><small class="vp-badge-vendor">(' . e($vendor->name) . ')</small>';
                        }
                    } else {
                        $fulfillmentBadge = '<span class="vp-badge vp-badge--orange"><i class="fas fa-truck"></i> Dropship</span>';
                    }
                }
            @endphp
            <h2 class="vp-title" id="modalTitle">
                {{ $product->name }} {!! $fulfillmentBadge !!}
                @if (!empty($product->slug) && $front_end_url != '')
                    <a href="{{ $product_url }}" target="_blank" class="vp-link" aria-label="@lang('messages.open_in_new_tab')"><i class="fa fa-external-link"></i></a>
                @endif
            </h2>
            <button type="button" class="vp-btn-close no-print" data-dismiss="modal" id="close_button" aria-label="@lang('messages.close')">
                @lang('messages.close')
            </button>
        </div>

        <div class="vp-body">
            {{-- Card: Product image + meta --}}
            <div class="vp-hero">
                <div class="vp-card vp-card--images">
                    <div class="vp-images">
                        @if($product->product_gallery_images->count() > 0)
                            <div class="vp-thumbnails" aria-label="@lang('product.gallery')">
                                @foreach ($product->product_gallery_images as $img)
                                    <button type="button" class="vp-thumb thumbnail-container" onclick="changeMainImage('{{ $img->image_url }}', this)" aria-pressed="false">
                                        <img src="{{ $img->image_url }}" alt="" />
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        <div class="vp-main-image-wrap">
                            @if(!empty($product->image_url))
                                <img id="mainImage" class="vp-main-image" src="{{ $product->image_url }}" alt="{{ $product->name }}" />
                            @else
                                <div class="vp-image-placeholder" id="mainImagePlaceholder">
                                    <i class="fas fa-image"></i>
                                    <span>No image</span>
                                </div>
                                <img id="mainImage" class="vp-main-image vp-main-image--hidden" src="" alt="{{ $product->name }}" />
                            @endif
                        </div>
                    </div>
                </div>

                <div class="vp-info-stack">
                    <div class="vp-card">
                        <h3 class="vp-card__title">Product Information</h3>
                        <div class="vp-info-grid">
                            <div class="vp-field">
                                <span class="vp-field__label">@lang('product.sku')</span>
                                <div class="vp-field__value vp-field__value--with-action">
                                    <span>{{ $product->sku }}</span>
                                    <button type="button" class="vp-icon-btn sku-barcode-copy-icon" data-clipboard-text="{{ $product->sku }}" aria-label="@lang('messages.copy')">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="vp-field">
                                <span class="vp-field__label">@lang('product.brand')</span>
                                <span class="vp-field__value">
                                    @if (!empty($brand_url) && !empty($product->brand->name))
                                        <a href="{{ $brand_url }}" target="_blank" class="vp-link">{{ $product->brand->name }}</a>
                                    @else
                                        {{ $product->brand->name ?? '--' }}
                                    @endif
                                </span>
                            </div>
                            <div class="vp-field">
                                <span class="vp-field__label">@lang('lang_v1.available_in_locations')</span>
                                <span class="vp-field__value">
                                    @if (count($product->product_locations) > 0)
                                        {{ implode(', ', $product->product_locations->pluck('name')->toArray()) }}
                                    @else
                                        @lang('lang_v1.none')
                                    @endif
                                </span>
                            </div>
                            @if (!empty($product->media->first()))
                                <div class="vp-field">
                                    <span class="vp-field__label">@lang('lang_v1.product_brochure')</span>
                                    <a href="{{ $product->media->first()->display_url }}" download="{{ $product->media->first()->display_name }}" class="vp-link vp-field__value">
                                        <i class="fas fa-download"></i> {{ $product->media->first()->display_name }}
                                    </a>
                                </div>
                            @endif
                            @php $custom_labels = json_decode(session('business.custom_labels'), true); @endphp
                            @for ($i = 1; $i <= 20; $i++)
                                @php
                                    $db_field = 'product_custom_field' . $i;
                                    $label = 'custom_field_' . $i;
                                @endphp
                                @if (!empty($product->$db_field))
                                    <div class="vp-field">
                                        <span class="vp-field__label">{{ $custom_labels['product'][$label] ?? '' }}</span>
                                        <span class="vp-field__value">{{ $product->$db_field }}</span>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    </div>

                    <div class="vp-card">
                        <h3 class="vp-card__title">Product Details</h3>
                        <div class="vp-info-grid">
                            <div class="vp-field">
                                <span class="vp-field__label">@lang('product.category')</span>
                                <span class="vp-field__value">{{ $product->category->name ?? '--' }}</span>
                            </div>
                            <div class="vp-field">
                                <span class="vp-field__label">@lang('product.sub_category')</span>
                                <span class="vp-field__value">{{ $product->sub_category->name ?? '--' }}</span>
                            </div>
                            <div class="vp-field">
                                <span class="vp-field__label">@lang('product.web_category')</span>
                                <span class="vp-field__value">
                                    @if (!empty($product->webcategories))
                                        @foreach ($product->webcategories as $category)
                                            {{ $category['name'] }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    @else
                                        --
                                    @endif
                                </span>
                            </div>
                            <div class="vp-field">
                                <span class="vp-field__label">@lang('product.product_type')</span>
                                <span class="vp-field__value">@lang('lang_v1.' . $product->type)</span>
                            </div>
                            @if (!empty($product->warranty))
                                <div class="vp-field">
                                    <span class="vp-field__label">@lang('lang_v1.warranty')</span>
                                    <span class="vp-field__value">{{ $product->warranty->display_name }}</span>
                                </div>
                            @endif
                            @if ($product->weight)
                                <div class="vp-field">
                                    <span class="vp-field__label">@lang('lang_v1.weight')</span>
                                    <span class="vp-field__value">{{ $product->weight }}</span>
                                </div>
                            @endif
                            <div class="vp-field">
                                <span class="vp-field__label">Tax Values</span>
                                <span class="vp-field__value">
                                    @if ($product->ml) ML: {{ $product->ml }} @endif
                                    @if ($product->ct) CT: {{ $product->ct }} @endif
                                    @if ($product->locationTaxType)
                                        Type:
                                        @php
                                            $locationTaxTypeName = 'none';
                                            if (!empty($product->locationTaxType) && is_array($product->locationTaxType) && count($product->locationTaxType) > 0) {
                                                $firstTaxTypeId = $product->locationTaxType[0];
                                                $locationTaxType = \App\LocationTaxType::find($firstTaxTypeId);
                                                $locationTaxTypeName = $locationTaxType ? $locationTaxType->name : 'none';
                                            }
                                        @endphp
                                        {{ $locationTaxTypeName }}
                                    @endif
                                    @if (!$product->ml && !$product->ct && empty($product->locationTaxType)) -- @endif
                                </span>
                            </div>
                            @if ($product->enable_stock)
                                <div class="vp-field">
                                    <span class="vp-field__label">@lang('product.alert_quantity')</span>
                                    <span class="vp-field__value">{{ isset($product->alert_quantity) ? number_format($product->alert_quantity, 0) : '--' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($rack_details->count() && (session('business.enable_racks') || session('business.enable_row') || session('business.enable_position')))
                <div class="vp-card vp-card--full">
                    <h3 class="vp-card__title">@lang('lang_v1.rack_details')</h3>
                    <div class="vp-table-wrap">
                        <table class="vp-table">
                            <thead>
                                <tr>
                                    <th>@lang('business.location')</th>
                                    @if (session('business.enable_racks')) <th>@lang('lang_v1.rack')</th> @endif
                                    @if (session('business.enable_row')) <th>@lang('lang_v1.row')</th> @endif
                                    @if (session('business.enable_position')) <th>@lang('lang_v1.position')</th> @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rack_details as $rd)
                                    <tr>
                                        <td>{{ $rd->name }}</td>
                                        @if (session('business.enable_racks')) <td>{{ $rd->rack }}</td> @endif
                                        @if (session('business.enable_row')) <td>{{ $rd->row }}</td> @endif
                                        @if (session('business.enable_position')) <td>{{ $rd->position }}</td> @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($product->type == 'combo')
                <div class="vp-card vp-card--full">
                    <h3 class="vp-card__title">@lang('product.combo_product_details')</h3>
                    <div class="vp-table-wrap">
                        <table class="vp-table">
                            <thead>
                                <tr>
                                    <th>@lang('product.product_name')</th>
                                    <th class="vp-text-right">@lang('sale.qty')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($combo_variations as $combo_variation)
                                    <tr>
                                        <td>
                                            {{ $combo_variation['variation']->product->name }}
                                            @if($combo_variation['variation']->product->type == 'variable')
                                                - {{ $combo_variation['variation']->name }}
                                            @endif
                                            <span class="vp-muted">({{ $combo_variation['variation']->sub_sku }})</span>
                                        </td>
                                        <td class="vp-text-right">{{ $combo_variation['quantity'] }} {{ $combo_variation['variation']->product->unit->short_name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($product->type == 'variable')
                @include('product.partials.variable_product_details', ['product' => $product, 'allowed_group_prices' => $allowed_group_prices ?? [], 'group_price_details' => $group_price_details ?? []])
            @endif

            <div class="vp-stock-slot" id="view_product_stock_details" data-product_id="{{ $product->id }}"></div>
        </div>

        <div class="modal-footer vp-footer"></div>
    </div>
</div>

<script>
(function() {
    function changeMainImage(imageSrc, clickedElement) {
        var mainImg = document.getElementById('mainImage');
        var placeholder = document.getElementById('mainImagePlaceholder');
        if (mainImg) {
            mainImg.src = imageSrc;
            mainImg.classList.remove('vp-main-image--hidden');
        }
        if (placeholder) placeholder.style.display = 'none';
        document.querySelectorAll('.view-product-modal .thumbnail-container').forEach(function(thumb) {
            thumb.setAttribute('aria-pressed', thumb === clickedElement ? 'true' : 'false');
            thumb.style.setProperty('border-color', thumb === clickedElement ? '#232f3e' : '#d5d9d9');
            thumb.style.setProperty('border-width', thumb === clickedElement ? '3px' : '2px');
        });
        if (clickedElement) {
            clickedElement.style.borderColor = '#232f3e';
            clickedElement.style.borderWidth = '3px';
        }
    }
    window.changeMainImage = changeMainImage;

    $(document).ready(function() {
        $('.modal').on('shown.bs.modal', function() {
            var $modal = $(this);
            if (!$modal.find('.view-product-modal').length) return;
            $modal.find('.variation-image-thumb, .img-thumbnail').each(function() {
                if (!$(this).hasClass('variation-clickable')) {
                    $(this).addClass('variation-clickable').css('cursor', 'pointer').on('click', function() {
                        var src = $(this).attr('src') || $(this).find('img').attr('src');
                        if (src) changeMainImage(src, null);
                    });
                }
            });
        });
    });
})();
</script>

<style>
/* View Product Modal – Amazon theme, zoom-safe, responsive */
.view-product-modal { font-size: 16px; box-sizing: border-box; }
.view-product-modal *, .view-product-modal *::before, .view-product-modal *::after { box-sizing: border-box; }
.view-product-modal .modal-content { border-radius: 0.5rem; overflow: hidden; border: 1px solid #d5d9d9; box-shadow: 0 4px 20px rgba(15,17,17,0.15); }
.view-product-modal .modal-body { padding: 0; }

.vp-header {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;
    background: #37475a; border-bottom: 1px solid #2c3948;
    padding: 1rem 1.25rem;
}
.vp-title { margin: 0; font-size: 1.25rem; font-weight: 600; color: #ffffff; line-height: 1.35; display: flex; align-items: center; flex-wrap: wrap; gap: 0.35rem; }
.vp-title .vp-badge { font-size: 0.6875rem; padding: 0.25rem 0.5rem; border-radius: 0.25rem; margin-left: 0.25rem; white-space: nowrap; }
.vp-badge--green { background: #067d62; color: #fff; }
.vp-badge--blue { background: #232f3e; color: #fff; }
.vp-badge--purple { background: #5c3d99; color: #fff; }
.vp-badge--orange { background: #c7511f; color: #fff; }
.vp-badge-vendor { margin-left: 0.25rem; color: rgba(255,255,255,0.9); font-size: 0.8125rem; }
.vp-header .vp-link { color: #fef3c7; text-decoration: none; }
.vp-header .vp-link:hover { text-decoration: underline; color: #fff; }
.vp-link { color: #007185; text-decoration: none; }
.vp-link:hover { text-decoration: underline; }
.vp-btn-close {
    background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.4); border-radius: 0.5rem; padding: 0.375rem 1rem;
    font-size: 0.875rem; color: #ffffff; cursor: pointer; white-space: nowrap;
}
.vp-btn-close:hover { background: rgba(255,255,255,0.25); border-color: rgba(255,255,255,0.6); color: #fff; }

.vp-body { background: #fff; padding: 1.25rem; }

.vp-hero {
    display: flex; gap: 1.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;
}
.vp-card {
    background: #fff; border: 1px solid #d5d9d9; border-radius: 0.5rem; overflow: hidden;
    box-shadow: 0 3px 10px rgba(15,17,17,0.1); min-width: 0;
}
.vp-card--images { flex: 0 0 min(20rem, 100%); padding: 1.25rem; }
.vp-card--full { width: 100%; }
.vp-card:not(.vp-card--images) { padding: 0; }
.vp-info-stack { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 1rem; }
/* Card header – Amazon-style dark bar, white title (Product Information, Product Details, Variations, etc.) */
.vp-card__title {
    font-size: 0.875rem; font-weight: 600; color: #ffffff; margin: 0; padding: 0.625rem 1.125rem;
    background-color: #37475a; border-bottom: 1px solid #2c3948;
}
.vp-card:not(.vp-card--images) .vp-info-grid,
.vp-card:not(.vp-card--images) .vp-table-wrap { padding: 1rem 1.25rem; }
.vp-card:not(.vp-card--images) .vp-info-grid { margin: 0; }

.vp-images { display: flex; gap: 0.75rem; align-items: flex-start; min-height: 12rem; }
.vp-thumbnails { display: flex; flex-direction: column; gap: 0.5rem; max-height: 28rem; overflow-y: auto; padding-right: 0.25rem; flex-shrink: 0; }
.vp-thumbnails::-webkit-scrollbar { width: 0.375rem; }
.vp-thumbnails::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 0.25rem; }
.vp-thumbnails::-webkit-scrollbar-thumb { background: #888; border-radius: 0.25rem; }
.vp-thumb { width: 3.125rem; height: 3.125rem; border: 2px solid #d5d9d9; border-radius: 0.25rem; padding: 0.2rem; background: #fff; cursor: pointer; overflow: hidden; flex-shrink: 0; transition: border-color 0.2s, box-shadow 0.2s; }
.vp-thumb img { width: 100%; height: 100%; object-fit: contain; display: block; }
.vp-thumb:hover { border-color: #ff9900; box-shadow: 0 2px 6px rgba(255,153,0,0.25); }
.vp-main-image-wrap { flex: 1; min-width: 0; border: 1px solid #d5d9d9; border-radius: 0.5rem; overflow: hidden; background: #fff; padding: 1rem; box-shadow: 0 2px 8px rgba(15,17,17,0.06); display: flex; align-items: center; justify-content: center; min-height: 12rem; }
.vp-main-image { max-width: 100%; height: auto; max-height: 28rem; object-fit: contain; display: block; }
.vp-main-image--hidden { display: none !important; }
.vp-image-placeholder { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; color: #a2a6a6; font-size: 0.875rem; padding: 2rem; }
.vp-image-placeholder i { font-size: 3rem; }

.vp-info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem 1.5rem; }
.vp-field__label { font-size: 0.8125rem; color: #565959; display: block; margin-bottom: 0.2rem; }
.vp-field__value { font-size: 0.875rem; color: #0f1111; font-weight: 500; }
.vp-field__value--with-action { display: flex; align-items: center; gap: 0.5rem; }
.vp-icon-btn { background: none; border: none; cursor: pointer; padding: 0.25rem; color: #007185; }
.vp-icon-btn:hover { color: #c7511f; }
.vp-muted { color: #565959; }

.vp-table-wrap { overflow-x: auto; border-radius: 0 0 0.5rem 0.5rem; }
.vp-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.vp-table th, .vp-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #e7e7e7; }
.vp-table th { background: #37475a; font-weight: 600; color: #ffffff; border-bottom-color: #2c3948; }
.vp-table td { color: #0f1111; }
.view-product-modal .amazon-variations-table th { background: #37475a; color: #ffffff; }
.vp-text-right { text-align: right; }
.vp-stock-slot { margin-top: 1rem; min-height: 2rem; }

.vp-footer { border-top: 1px solid #e7e7e7; padding: 0.5rem 1rem; }

/* Responsive: single column and stack image above info */
@media (max-width: 768px) {
    .view-product-modal { font-size: 14px; }
    .vp-hero { flex-direction: column; }
    .vp-card--images { flex: 1 1 100%; }
    .vp-info-grid { grid-template-columns: 1fr; }
    .vp-images { flex-wrap: wrap; }
    .vp-main-image-wrap { min-height: 10rem; }
}

/* Zoom-friendly: ensure taps and layout hold at 50%–100% */
@media (min-resolution: 1.5dppx) {
    .vp-btn-close { padding: 0.5rem 1.25rem; }
    .vp-thumb { width: 2.75rem; height: 2.75rem; }
}

.view-product-modal .variation-clickable:hover { opacity: 0.85; }
.view-product-modal .amazon-variations-table tbody tr:last-child { border-bottom: none; }
</style>
