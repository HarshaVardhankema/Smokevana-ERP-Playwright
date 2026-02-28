<style>
/* ========================================
   EDIT PRICE MODAL - AMAZON THEME
   ======================================== */

/* Modal Container */
.edit-price-modal .modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

/* Modal Header */
.edit-price-modal .modal-header {
    background: linear-gradient(135deg, #232F3E 0%, #1a252f 100%);
    border-bottom: 3px solid #FF9900;
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.edit-price-modal .modal-title {
    color: #FFFFFF;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    line-height: 1.4;
}

.edit-price-modal .modal-title i {
    color: #FF9900;
}

.edit-price-modal .close {
    color: #FFFFFF;
    opacity: 0.8;
    font-size: 24px;
    text-shadow: none;
    transition: all 0.2s ease;
}

.edit-price-modal .close:hover {
    color: #FF9900;
    opacity: 1;
}

/* Modal Body */
.edit-price-modal .modal-body {
    padding: 24px;
    background: #FAFAFA;
}

/* Product Info Section */
.edit-price-product-info {
    display: grid;
    grid-template-columns: auto 1fr 1.5fr;
    gap: 20px;
    align-items: start;
    margin-bottom: 24px;
}

@media (max-width: 992px) {
    .edit-price-product-info {
        grid-template-columns: 1fr;
    }
}

/* Product Image */
.edit-price-image {
    width: 120px;
    height: 120px;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    overflow: hidden;
    background: #FFF;
    display: flex;
    align-items: center;
    justify-content: center;
}

.edit-price-image img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

/* Info Card */
.edit-price-info-card {
    background: #FFFFFF;
    border-radius: 8px;
    border: 1px solid #E5E7EB;
    overflow: hidden;
}

.edit-price-info-card.highlighted {
    border: 2px solid #FF9900;
}

.edit-price-card-header {
    background: linear-gradient(180deg, #F7F8F8 0%, #FAFAFA 100%);
    padding: 10px 16px;
    border-bottom: 1px solid #E5E7EB;
    font-weight: 600;
    font-size: 12px;
    color: #0F1111;
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.edit-price-card-header i {
    color: #FF9900;
}

.edit-price-card-header.highlight-header {
    background: linear-gradient(180deg, #FFF8E7 0%, #FFF5E0 100%);
    border-bottom: 1px solid #FFE4B5;
}

.edit-price-card-body {
    padding: 0;
}

.edit-price-detail-row {
    display: flex;
    padding: 8px 16px;
    border-bottom: 1px solid #F3F4F6;
    align-items: center;
}

.edit-price-detail-row:last-child {
    border-bottom: none;
}

.edit-price-detail-row:hover {
    background: #FAFAFA;
}

.edit-price-label {
    font-weight: 500;
    color: #565959;
    font-size: 12px;
    min-width: 120px;
    flex-shrink: 0;
}

.edit-price-value {
    color: #0F1111;
    font-size: 13px;
    word-break: break-word;
}

/* Two Column Layout */
.edit-price-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
}

@media (max-width: 768px) {
    .edit-price-columns {
        grid-template-columns: 1fr;
    }
}

.edit-price-column {
    padding: 0;
}

.edit-price-column:first-child {
    border-right: 1px solid #F3F4F6;
}

@media (max-width: 768px) {
    .edit-price-column:first-child {
        border-right: none;
        border-bottom: 1px solid #F3F4F6;
    }
}

/* Bulk Edit Section */
.bulk-edit-section {
    margin-bottom: 24px;
}

.bulk-edit-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}

.bulk-edit-title {
    font-size: 15px;
    font-weight: 600;
    color: #0F1111;
    margin: 0;
}

.bulk-edit-title i {
    color: #FF9900;
}

.bulk-edit-card {
    background: #FFFFFF;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    padding: 20px;
}

.bulk-edit-fields {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: flex-end;
}

.bulk-edit-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.bulk-edit-field label {
    font-size: 12px;
    font-weight: 500;
    color: #565959;
    margin: 0;
}

.bulk-edit-field .input-group {
    max-width: 120px;
}

.bulk-edit-field .input-group-addon {
    background: #F7F8F8;
    border: 1px solid #D5D9D9;
    border-right: none;
    color: #565959;
    font-weight: 500;
    padding: 6px 10px;
    border-radius: 4px 0 0 4px;
}

.bulk-edit-field .form-control {
    border: 1px solid #D5D9D9;
    border-radius: 0 4px 4px 0;
    padding: 6px 10px;
    font-size: 13px;
    height: auto;
    transition: all 0.15s ease;
}

.bulk-edit-field .form-control:focus {
    border-color: #FF9900;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.15);
    outline: none;
}

.bulk-edit-actions {
    margin-left: auto;
}

/* Amazon Button Styles */
.btn-amazon-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 18px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
    border: 1px solid;
    white-space: nowrap;
}

.btn-amazon-action:hover {
    transform: translateY(-1px);
}

.btn-amazon-action:active {
    transform: translateY(0);
}

.btn-amazon-primary {
    background: linear-gradient(180deg, #FF9900 0%, #E47911 100%);
    border-color: #C7511F;
    color: #0F1111;
}

.btn-amazon-primary:hover {
    background: linear-gradient(180deg, #FFB84D 0%, #FF9900 100%);
    box-shadow: 0 4px 12px rgba(255, 153, 0, 0.3);
    color: #0F1111;
}

.btn-amazon-secondary {
    background: linear-gradient(180deg, #FFFFFF 0%, #F7F8F8 100%);
    border-color: #D5D9D9;
    color: #0F1111;
}

.btn-amazon-secondary:hover {
    background: linear-gradient(180deg, #F7FAFA 0%, #E3E6E6 100%);
    border-color: #BBBFBF;
    box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    color: #0F1111;
}

.btn-amazon-success {
    background: linear-gradient(180deg, #067D17 0%, #056712 100%);
    border-color: #056712;
    color: #FFFFFF;
}

.btn-amazon-success:hover {
    background: linear-gradient(180deg, #078C1A 0%, #067D17 100%);
    box-shadow: 0 4px 12px rgba(6, 125, 23, 0.3);
    color: #FFFFFF;
}

/* Variations Table Section */
.variations-edit-section {
    margin-top: 0;
}

.variations-edit-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}

.variations-edit-title {
    font-size: 15px;
    font-weight: 600;
    color: #0F1111;
    margin: 0;
}

.variations-edit-title i {
    color: #FF9900;
}

/* Variations Edit Table */
.variations-edit-table-wrapper {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    overflow: hidden;
}

.variations-edit-table-scroll {
    max-height: 400px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #D5D9D9 transparent;
}

.variations-edit-table-scroll::-webkit-scrollbar {
    width: 6px;
}

.variations-edit-table-scroll::-webkit-scrollbar-thumb {
    background: #D5D9D9;
    border-radius: 3px;
}

.edit-price-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.edit-price-table thead {
    background: linear-gradient(180deg, #232F3E 0%, #1A252F 100%);
    position: sticky;
    top: 0;
    z-index: 10;
}

.edit-price-table thead th {
    color: #FFFFFF;
    font-weight: 500;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 12px 10px;
    text-align: left;
    border: none;
    white-space: nowrap;
}

.edit-price-table thead tr.discount-row {
    background: linear-gradient(180deg, #37475A 0%, #2D3A4A 100%);
}

.edit-price-table thead tr.discount-row th {
    font-size: 10px;
    font-weight: 400;
    padding: 6px 10px;
    color: #FFD580;
    text-transform: none;
    letter-spacing: 0;
}

.edit-price-table tbody tr {
    transition: background 0.15s ease;
    border-bottom: 1px solid #F3F4F6;
}

.edit-price-table tbody tr:last-child {
    border-bottom: none;
}

.edit-price-table tbody tr:hover {
    background: #FFF8E7;
}

.edit-price-table tbody td {
    padding: 10px;
    font-size: 13px;
    color: #0F1111;
    vertical-align: middle;
}

/* Price Input in Table */
.edit-price-table .input-group {
    max-width: 100px;
    min-width: 80px;
}

.edit-price-table .input-group-addon {
    background: #F7F8F8;
    border: 1px solid #D5D9D9;
    border-right: none;
    color: #565959;
    font-weight: 500;
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 4px 0 0 4px;
}

.edit-price-table .form-control {
    border: 1px solid #D5D9D9;
    border-radius: 0 4px 4px 0;
    padding: 4px 8px;
    font-size: 13px;
    height: auto;
    min-width: 50px;
    transition: all 0.15s ease;
}

.edit-price-table .form-control:focus {
    border-color: #FF9900;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.15);
    outline: none;
}

/* Modal Footer */
.edit-price-modal .modal-footer {
    background: #FAFAFA;
    border-top: 1px solid #E5E7EB;
    padding: 16px 24px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Download Button */
.download-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: linear-gradient(180deg, #17A7E4 0%, #1190C8 100%);
    border-radius: 4px;
    color: #FFF;
    font-size: 12px;
    text-decoration: none;
    transition: all 0.15s ease;
}

.download-badge:hover {
    background: linear-gradient(180deg, #1190C8 0%, #0D7FB5 100%);
    color: #FFF;
    text-decoration: none;
}

/* Badge */
.info-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.badge-success {
    background: #E7F5E8;
    color: #067D17;
}

.badge-neutral {
    background: #F3F4F6;
    color: #4B5563;
}
</style>

<div class="modal-dialog modal-xl no-print edit-price-modal" id="metrix_modal" role="document">
    <div class="modal-content">
        {{-- Modal Header --}}
        <div class="modal-header">
            <h4 class="modal-title">
                <i class="fas fa-dollar-sign"></i>
                {{ $product->name }}
            </h4>
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        {{-- Modal Body --}}
        <div class="modal-body">
            {{-- Product Info Grid --}}
            <div class="edit-price-product-info">
                {{-- Product Image --}}
                <div class="edit-price-image">
                    <img id="mainImage" src="{{ $product->image_url }}" alt="{{ $product->name }}" />
                </div>
                
                {{-- Basic Info Card --}}
                <div class="edit-price-info-card">
                    <div class="edit-price-card-header">
                        <i class="fas fa-info-circle"></i> Product Info
                    </div>
                    <div class="edit-price-card-body">
                        <div class="edit-price-detail-row">
                            <span class="edit-price-label">@lang('product.sku')</span>
                            <span class="edit-price-value"><strong>{{ $product->sku }}</strong></span>
                        </div>
                        
                        @if ($product->ml || $product->ct)
                            <div class="edit-price-detail-row">
                                <span class="edit-price-label">Tax Values</span>
                                <span class="edit-price-value">
                                    @if ($product->ml)ML: {{ $product->ml }}@endif
                                    @if ($product->ct) &amp; CT: {{ $product->ct }}@endif
                                </span>
                            </div>
                        @endif
                        
                        <div class="edit-price-detail-row">
                            <span class="edit-price-label">@lang('product.brand')</span>
                            <span class="edit-price-value">{{ $product->brand->name ?? '--' }}</span>
                        </div>
                        
                        <div class="edit-price-detail-row">
                            <span class="edit-price-label">@lang('product.unit')</span>
                            <span class="edit-price-value">{{ $product->unit->short_name ?? '--' }}</span>
                        </div>
                        
                        <div class="edit-price-detail-row">
                            <span class="edit-price-label">@lang('product.barcode_type')</span>
                            <span class="edit-price-value">{{ $product->barcode_type ?? '--' }}</span>
                        </div>
                        
                        @php
                            $custom_labels = json_decode(session('business.custom_labels'), true);
                        @endphp
                        
                        @for ($i = 1; $i <= 20; $i++)
                            @php
                                $db_field = 'product_custom_field' . $i;
                                $label = 'custom_field_' . $i;
                            @endphp
                            @if (!empty($product->$db_field))
                                <div class="edit-price-detail-row">
                                    <span class="edit-price-label">{{ $custom_labels['product'][$label] ?? '' }}</span>
                                    <span class="edit-price-value">{{ $product->$db_field }}</span>
                                </div>
                            @endif
                        @endfor
                        
                        <div class="edit-price-detail-row">
                            <span class="edit-price-label">@lang('lang_v1.available_in_locations')</span>
                            <span class="edit-price-value">
                                @if (count($product->product_locations) > 0)
                                    <span class="info-badge badge-success">
                                        {{ implode(', ', $product->product_locations->pluck('name')->toArray()) }}
                                    </span>
                                @else
                                    <span class="info-badge badge-neutral">@lang('lang_v1.none')</span>
                                @endif
                            </span>
                        </div>
                        
                        @if (!empty($product->media->first()))
                            <div class="edit-price-detail-row">
                                <span class="edit-price-label">@lang('lang_v1.product_brochure')</span>
                                <span class="edit-price-value">
                                    <a href="{{ $product->media->first()->display_url }}"
                                       download="{{ $product->media->first()->display_name }}"
                                       class="download-badge">
                                        <i class="fas fa-download"></i>
                                        {{ $product->media->first()->display_name }}
                                    </a>
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Category Info Card --}}
                <div class="edit-price-info-card highlighted">
                    <div class="edit-price-card-header highlight-header">
                        <i class="fas fa-tags"></i> Category &amp; Details
                    </div>
                    <div class="edit-price-columns">
                        <div class="edit-price-column">
                            <div class="edit-price-detail-row">
                                <span class="edit-price-label">@lang('product.brand')</span>
                                <span class="edit-price-value">{{ $product->brand->name ?? '--' }}</span>
                            </div>
                            <div class="edit-price-detail-row">
                                <span class="edit-price-label">@lang('product.category')</span>
                                <span class="edit-price-value">{{ $product->category->name ?? '--' }}</span>
                            </div>
                            <div class="edit-price-detail-row">
                                <span class="edit-price-label">@lang('product.sub_category')</span>
                                <span class="edit-price-value">{{ $product->sub_category->name ?? '--' }}</span>
                            </div>
                            <div class="edit-price-detail-row">
                                <span class="edit-price-label">@lang('product.web_category')</span>
                                <span class="edit-price-value">
                                    @if (!empty($product->webcategories))
                                        @foreach ($product->webcategories as $category)
                                            {{ $category['name'] }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    @else
                                        --
                                    @endif
                                </span>
                            </div>
                            <div class="edit-price-detail-row">
                                <span class="edit-price-label">@lang('product.manage_stock')</span>
                                <span class="edit-price-value">
                                    @if ($product->enable_stock)
                                        <span class="info-badge badge-success">@lang('messages.yes')</span>
                                    @else
                                        <span class="info-badge badge-neutral">@lang('messages.no')</span>
                                    @endif
                                </span>
                            </div>
                            @if ($product->enable_stock)
                                <div class="edit-price-detail-row">
                                    <span class="edit-price-label">@lang('product.alert_quantity')</span>
                                    <span class="edit-price-value">{{ $product->alert_quantity ?? '--' }}</span>
                                </div>
                            @endif
                            @if (!empty($product->warranty))
                                <div class="edit-price-detail-row">
                                    <span class="edit-price-label">@lang('lang_v1.warranty')</span>
                                    <span class="edit-price-value">{{ $product->warranty->display_name }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="edit-price-column">
                            @if ($product->weight)
                                <div class="edit-price-detail-row">
                                    <span class="edit-price-label">@lang('lang_v1.weight')</span>
                                    <span class="edit-price-value">{{ $product->weight }}</span>
                                </div>
                            @endif
                            <div class="edit-price-detail-row">
                                <span class="edit-price-label">@lang('product.applicable_tax')</span>
                                <span class="edit-price-value">{{ $product->product_tax->name ?? __('lang_v1.none') }}</span>
                            </div>
                            @php
                                $tax_type = [
                                    'inclusive' => __('product.inclusive'),
                                    'exclusive' => __('product.exclusive'),
                                ];
                            @endphp
                            <div class="edit-price-detail-row">
                                <span class="edit-price-label">@lang('product.selling_price_tax_type')</span>
                                <span class="edit-price-value">{{ $tax_type[$product->tax_type] ?? '--' }}</span>
                            </div>
                            <div class="edit-price-detail-row">
                                <span class="edit-price-label">@lang('product.product_type')</span>
                                <span class="edit-price-value">@lang('lang_v1.' . $product->type)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Bulk Edit Section --}}
            <div class="bulk-edit-section">
                <div class="bulk-edit-header">
                    <h4 class="bulk-edit-title">
                        <i class="fas fa-edit"></i> Bulk Edit
                    </h4>
                </div>
                <div class="bulk-edit-card">
                    <div class="bulk-edit-fields" id="bulk-edit-container">
                        {{-- Selling Price Input --}}
                        <div class="bulk-edit-field">
                            <label for="selling_price_bulk">Selling Price</label>
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="text" name="selling_price_bulk" 
                                       class="form-control input_number" 
                                       value="" id="selling_price_bulk"
                                       placeholder="0.00">
                            </div>
                        </div>
                        
                        @if(isset($price_groups) && count($price_groups) > 0)
                            @foreach ($price_groups->reverse() as $price_group)
                                <div class="bulk-edit-field">
                                    <label for="{{ $price_group->name }}">
                                        {{ \Illuminate\Support\Str::replaceFirst('SellingPrice', '', $price_group->name) }}
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="text"
                                               name="{{ \Illuminate\Support\Str::replaceFirst('SellingPrice', '', $price_group->name) }}_price"
                                               class="form-control input_number"
                                               value=""
                                               id="{{ $price_group->name }}"
                                               placeholder="0.00">
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        
                        <div class="bulk-edit-actions">
                            <button type="button" class="btn-amazon-action btn-amazon-primary" id="applytoall">
                                <i class="fas fa-check-double"></i> Apply to All
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Variations Table --}}
            <div class="variations-edit-section">
                <div class="variations-edit-header">
                    <h4 class="variations-edit-title">
                        <i class="fas fa-layer-group"></i> @lang('product.variations')
                    </h4>
                </div>
                
                {!! Form::open([
                    'url' => action([\App\Http\Controllers\ProductController::class, 'updatePricePopUP']),
                    'method' => 'post',
                    'id' => 'update_prices_invoice_popup',
                ]) !!}
                @csrf
                <input type="hidden" value="{{ $product->id }}" name="product_id">
                
                <div class="variations-edit-table-wrapper">
                    <div class="variations-edit-table-scroll">
                        <table class="edit-price-table">
                            <thead>
                                <tr>
                                    <th>@lang('product.sku')</th>
                                    <th>@lang('product.barcode_no')</th>
                                    <th>@lang('product.variations')</th>
                                    <th>Selling Price</th>
                                    @if(isset($price_groups) && !empty($price_groups))
                                        @foreach($price_groups->reverse() as $price_group)
                                            <th>
                                                {{ Str::before(preg_replace('/([a-z])([A-Z])/', '$1 $2', $price_group->name), ' ') }}
                                                @show_tooltip(__('lang_v1.price_group_price_type_tooltip'))
                                            </th>
                                        @endforeach
                                    @endif
                                </tr>
                                @if(isset($price_groups) && !empty($price_groups))
                                    <tr class="discount-row">
                                        <th colspan="3"></th>
                                        <th></th>
                                        @foreach($price_groups->reverse() as $price_group)
                                            @php
                                                $groupName = strtolower(Str::before(preg_replace('/([a-z])([A-Z])/', '$1 $2', $price_group->name), ' '));
                                                $discount = '';
                                                if (strpos($groupName, 'silver') !== false) {
                                                    $discount = 'Silver 15% off';
                                                } elseif (strpos($groupName, 'gold') !== false) {
                                                    $discount = 'Gold 20% off';
                                                } elseif (strpos($groupName, 'platinum') !== false) {
                                                    $discount = 'Platinum 25% off';
                                                } elseif (strpos($groupName, 'diamond') !== false) {
                                                    $discount = 'Diamond 30% off';
                                                } elseif (strpos($groupName, 'vip') !== false) {
                                                    $discount = 'VIP 35% off';
                                                }
                                            @endphp
                                            <th>{{ $discount }}</th>
                                        @endforeach
                                    </tr>
                                @endif
                            </thead>
                            <tbody>
                                @foreach ($product->variations as $variation)
                                    <tr data-variation-id="{{ $variation->id }}" class="sell-line-row">
                                        <td><strong>{{ $variation->sub_sku }}</strong></td>
                                        <td>{{ $variation->var_barcode_no }}</td>
                                        <td>{{ $variation->product_variation->name }} - {{ $variation->name }}</td>
                                        <td class="selling-price-column">
                                            <div class="input-group">
                                                <span class="input-group-addon">$</span>
                                                {!! Form::text('selling_prices[' . $variation->id . ']', 
                                                    !empty($variation->sell_price_inc_tax) ? @num_format($variation->sell_price_inc_tax) : (!empty($variation->default_sell_price) ? @num_format($variation->default_sell_price) : ''), 
                                                    ['class' => 'form-control input_number input-sm selling-price-input', 'id' => 'selling_price_' . $variation->id, 'placeholder' => '0.00']) !!}
                                            </div>
                                        </td>
                                        @if(isset($price_groups) && !empty($price_groups))
                                            @foreach($price_groups->reverse() as $price_group)
                                                <td class="price-column">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">$</span>
                                                        {!! Form::text('prices[' . $variation->id . '][' . $price_group->id . ']', 
                                                            !empty($variation_prices[$variation->id][$price_group->id]['price']) ? @num_format($variation_prices[$variation->id][$price_group->id]['price']) : '', 
                                                            ['class' => 'form-control input_number input-sm', 'id' => $price_group->name, 'placeholder' => '0.00']) !!}
                                                    </div>
                                                </td>
                                            @endforeach
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" id="save_button_metrix" class="btn-amazon-action btn-amazon-success no-print">
                        <i class="fas fa-save"></i> Save
                    </button>
                    <button type="button" class="btn-amazon-action btn-amazon-secondary no-print" id="close_button" data-dismiss="modal">
                        <i class="fas fa-times"></i> @lang('messages.close')
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize variables for auto-calculation
        var priceGroups = @json($price_groups ?? []);
        var priceGroupPercentages = @json($price_group_percentage ?? []);

        // Select all text on focus
        $(document).on('focus', '.price-column input, .selling-price-column input, input[type="text"]', function() {
            $(this).select();
        });

        // Format value to two decimal places on change
        $(document).on('change', '.price-column input, .selling-price-column input, input[type="text"]', function() {
            let inputValue = $(this).val().trim();
            if (inputValue === '' || inputValue === null) {
                $(this).val('');
                return;
            }
            let value = parseFloat(inputValue);
            if (!isNaN(value) && value >= 0) {
                $(this).val(value.toFixed(2));
            } else {
                $(this).val('');
            }
        });

        // Function to calculate group prices based on percentage when selling price changes
        function calculateGroupPricesFromPercentage(sellingPriceInput) {
            var $row = $(sellingPriceInput).closest('tr');
            var variationId = $row.data('variation-id');
            var sellingPrice = parseFloat($(sellingPriceInput).val()) || 0;
            
            if (sellingPrice <= 0 || isNaN(sellingPrice)) {
                return;
            }
            
            if (priceGroups && priceGroups.length > 0 && priceGroupPercentages) {
                priceGroups.forEach(function(pg) {
                    var percentage = priceGroupPercentages[pg.id];
                    
                    if (percentage !== undefined && percentage !== null && percentage !== '') {
                        var percentageValue = parseFloat(percentage);
                        var calculatedPrice = sellingPrice * (1 - percentageValue / 100);
                        var $groupPriceInput = $row.find('.price-column input[id="' + pg.name + '"]');
                        
                        if ($groupPriceInput.length > 0) {
                            var wasAutoCalculated = $groupPriceInput.data('auto-calculated') === true;
                            var currentValue = parseFloat($groupPriceInput.val()) || 0;
                            
                            if (currentValue === 0 || wasAutoCalculated) {
                                $groupPriceInput.val(calculatedPrice.toFixed(2));
                                $groupPriceInput.data('auto-calculated', true);
                            }
                        }
                    }
                });
            }
        }

        // Attach change handler to selling price inputs for auto-calculation
        $(document).on('change keyup blur', '.selling-price-input', function() {
            setTimeout(function() {
                calculateGroupPricesFromPercentage($(this));
            }.bind(this), 100);
        });

        // Mark group price inputs as manually edited when user changes them
        $(document).on('change keyup blur', '.price-column input', function() {
            var currentValue = parseFloat($(this).val()) || 0;
            if (currentValue > 0) {
                $(this).data('auto-calculated', false);
            }
        });

        $('#applytoall').on('click', function() {
            var sellingPriceValue = $('#selling_price_bulk').val();
            if (sellingPriceValue) {
                $('.selling-price-input').each(function() {
                    $(this).val(parseFloat(sellingPriceValue).toFixed(2));
                    setTimeout(function() {
                        calculateGroupPricesFromPercentage($(this));
                    }.bind(this), 100);
                });
            }
            
            $('#bulk-edit-container input').each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                if (inputId !== 'selling_price_bulk' && inputValue) {
                    $('tbody .price-column input[id="' + inputId + '"]').each(function() {
                        $(this).val(parseFloat(inputValue).toFixed(2));
                        $(this).data('auto-calculated', false);
                    });
                }
            });
            swal("Success", "Prices applied to all variations!", "success");
        });

        $('#update_prices_invoice_popup').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = form.serializeArray();
            let processedData = {};
            
            $.each(formData, function(i, field) {
                if (field.name.indexOf('prices[') === 0) {
                    var match = field.name.match(/prices\[(\d+)\]\[(\d+)\]/);
                    if (match) {
                        var variationId = match[1];
                        var priceGroupId = match[2];
                        var value = field.value.trim();
                        
                        if (!processedData.prices) {
                            processedData.prices = {};
                        }
                        if (!processedData.prices[variationId]) {
                            processedData.prices[variationId] = {};
                        }
                        processedData.prices[variationId][priceGroupId] = (value === '' || value === null) ? '' : value;
                    }
                } else if (field.name.indexOf('selling_prices[') === 0) {
                    var match = field.name.match(/selling_prices\[(\d+)\]/);
                    if (match) {
                        var variationId = match[1];
                        if (!processedData.selling_prices) {
                            processedData.selling_prices = {};
                        }
                        var value = field.value.trim();
                        processedData.selling_prices[variationId] = (value === '' || value === null) ? null : value;
                    }
                } else {
                    processedData[field.name] = field.value;
                }
            });

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: processedData,
                success: function(response) {
                    if (response.status) {
                        swal({
                            title: "Success",
                            text: response.message,
                            icon: "success",
                            timer: 2000,
                            buttons: false
                        });
                    } else {
                        swal({
                            title: "Error",
                            text: "Failed to update prices.",
                            icon: "error",
                            timer: 2000,
                            buttons: false
                        });
                    }
                },
                error: function() {
                    swal({
                        title: "Error",
                        text: "Something went wrong. Please try again.",
                        icon: "error",
                        timer: 2000,
                        buttons: false
                    });
                }
            });
        });
    });
</script>
