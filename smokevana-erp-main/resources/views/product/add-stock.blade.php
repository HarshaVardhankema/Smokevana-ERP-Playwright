@extends('layouts.app')
@section('title', __('lang_v1.add_stock'))

@section('css')
<style>
/* Amazon Theme - Stock Management Page */
.amazon-stock-container {
    background: #EAEDED;
    min-height: 100vh;
    padding: 16px 20px;
}

/* Amazon style banner */
.amazon-banner {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%);
    border-radius: 10px;
    padding: 24px 28px;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative;
    overflow: hidden;
}

.amazon-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #ff9900, #e47911);
    z-index: 1;
}

.amazon-banner-inner {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.amazon-page-title {
    font-size: 24px;
    font-weight: 700;
    color: #fff !important;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.amazon-page-title i {
    color: #ff9900 !important;
}

.amazon-banner-subtitle {
    font-size: 14px;
    color: rgba(255,255,255,0.88);
    margin: 4px 0 0 0;
}

/* Main Card */
.amazon-card {
    background: #FFF;
    border-radius: 8px;
    border: 1px solid #D5D9D9;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    overflow: hidden;
}

.amazon-card-header {
    background: linear-gradient(180deg, #232F3E 0%, #1A252F 100%);
    padding: 16px 20px;
    border-bottom: 1px solid #232F3E;
}

.amazon-card-title {
    color: #FFF;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.amazon-card-title i {
    color: #FF9900;
}

.amazon-card-body {
    padding: 20px;
}

/* Filter Section */
.filter-section {
    background: #F7F8F8;
    border: 1px solid #E7E9EB;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
}

.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: flex-end;
}

.filter-group {
    flex: 1;
    min-width: 180px;
}

.filter-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #565959;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.filter-select {
    width: 100%;
    padding: 8px 12px;
    font-size: 13px;
    border: 1px solid #D5D9D9;
    border-radius: 6px;
    background: #FFF;
    color: #0F1111;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.filter-select:focus {
    outline: none;
    border-color: #FF9900;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.15);
}

/* Amazon Buttons */
.amazon-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    white-space: nowrap;
    border: 1px solid;
}

.amazon-btn-primary {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    border-color: #C7511F;
    color: #0F1111;
}

.amazon-btn-primary:hover {
    background: linear-gradient(to bottom, #FFB84D 0%, #FF9900 100%);
    box-shadow: 0 2px 5px rgba(255, 153, 0, 0.3);
    text-decoration: none;
    color: #0F1111;
}

.amazon-btn-secondary {
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border-color: #D5D9D9;
    color: #0F1111;
}

.amazon-btn-secondary:hover {
    background: linear-gradient(to bottom, #F7FAFA 0%, #E3E6E6 100%);
    border-color: #BBBFBF;
    text-decoration: none;
}

/* Small Filter/Clear buttons */
.amazon-btn-sm {
    padding: 5px 10px;
    font-size: 12px;
    gap: 4px;
}
.amazon-btn-sm i {
    font-size: 10px;
}

/* Stock Table */
.stock-table-wrapper {
    border: 1px solid #E7E9EB;
    border-radius: 8px;
    overflow: hidden;
}

.stock-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.stock-table thead {
    background: linear-gradient(180deg, #232F3E 0%, #1A252F 100%);
}

.stock-table thead th {
    color: #FFFFFF;
    font-weight: 500;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 12px 14px;
    text-align: left;
    border: none;
    white-space: nowrap;
}

.stock-table tbody tr {
    transition: background 0.15s ease;
    border-bottom: 1px solid #F3F4F6;
}

.stock-table tbody tr:last-child {
    border-bottom: none;
}

.stock-table tbody tr:hover {
    background: #FFF8E7;
}

.stock-table tbody td {
    padding: 12px 14px;
    font-size: 13px;
    color: #0F1111;
    vertical-align: middle;
}

/* Stock Input */
.stock-input {
    width: 80px;
    padding: 6px 10px;
    font-size: 13px;
    border: 1px solid #D5D9D9;
    border-radius: 4px;
    text-align: center;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.stock-input:focus {
    outline: none;
    border-color: #FF9900;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.15);
}

/* Product Info */
.product-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-image {
    width: 40px;
    height: 40px;
    border-radius: 4px;
    object-fit: cover;
    border: 1px solid #E5E7EB;
    background: #FFF;
}

.product-name {
    font-weight: 500;
    color: #0F1111;
}

.product-sku {
    font-size: 11px;
    color: #565959;
}

/* Stock Badge */
.stock-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 12px;
}

.stock-badge.positive {
    background: #E7F5E8;
    color: #067D17;
}

.stock-badge.warning {
    background: #FFF8E7;
    color: #B45309;
}

.stock-badge.negative {
    background: #FEF2F2;
    color: #B91C1C;
}

/* Loading State */
.loading-overlay {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.loading-overlay.active {
    display: flex;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #E7E9EB;
    border-top-color: #FF9900;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #565959;
}

.empty-state i {
    font-size: 48px;
    color: #D5D9D9;
    margin-bottom: 16px;
}

.empty-state h4 {
    font-size: 18px;
    color: #0F1111;
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 14px;
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .amazon-stock-container {
        padding: 12px;
    }
    
    .filter-group {
        min-width: 100%;
    }
    
    .amazon-banner .amazon-page-title {
        font-size: 20px;
    }
}
</style>
@endsection

@section('content')
<div class="amazon-stock-container">
    <!-- Amazon style banner -->
    <div class="amazon-banner">
        <div class="amazon-banner-inner">
            <div>
                <h1 class="amazon-page-title">
                    <i class="fas fa-boxes"></i> {{ __('lang_v1.add_stock') }}
                </h1>
                <p class="amazon-banner-subtitle">Add quantity to your product inventory</p>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="amazon-card">
        <div class="amazon-card-header">
            <h2 class="amazon-card-title">
                <i class="fas fa-filter"></i> Filter Products
            </h2>
        </div>
        <div class="amazon-card-body">
            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-row">
                    @if($has_multiple_locations)
                    <div class="filter-group">
                        <label class="filter-label">@lang('purchase.business_location')</label>
                        <select class="filter-select" id="location_id" name="location_id">
                            <option value="">@lang('lang_v1.all_locations')</option>
                            @foreach($business_locations as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="filter-group">
                        <label class="filter-label">@lang('product.product_type')</label>
                        <select class="filter-select" id="product_type" name="product_type">
                            <option value="">@lang('lang_v1.all')</option>
                            <option value="single">@lang('lang_v1.single')</option>
                            <option value="variable">@lang('lang_v1.variable')</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">@lang('product.category')</label>
                        <select class="filter-select" id="category_id" name="category_id">
                            <option value="">@lang('lang_v1.all')</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">@lang('product.brand')</label>
                        <select class="filter-select" id="brand_id" name="brand_id">
                            <option value="">@lang('lang_v1.all')</option>
                            @foreach($brands as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">&nbsp;</label>
                        <div style="display: flex; gap: 6px;">
                            <button type="button" class="amazon-btn amazon-btn-primary amazon-btn-sm" id="filter_btn">
                                <i class="fas fa-search"></i> @lang('lang_v1.filter')
                            </button>
                            <button type="button" class="amazon-btn amazon-btn-secondary amazon-btn-sm" id="clear_filter_btn">
                                <i class="fas fa-times"></i> @lang('lang_v1.clear')
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Table -->
            <div class="stock-table-wrapper" style="position: relative;">
                <div class="loading-overlay" id="loading_overlay">
                    <div class="loading-spinner"></div>
                </div>
                
                <div id="products_container">
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h4>@lang('lang_v1.select_filters')</h4>
                        <p>@lang('lang_v1.use_filters_above_to_load_products')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Filter button click
    $('#filter_btn').on('click', function() {
        loadProducts();
    });

    // Clear filter button click
    $('#clear_filter_btn').on('click', function() {
        $('#location_id').val('');
        $('#product_type').val('');
        $('#category_id').val('');
        $('#brand_id').val('');
        $('#products_container').html(`
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h4>@lang('lang_v1.select_filters')</h4>
                <p>@lang('lang_v1.use_filters_above_to_load_products')</p>
            </div>
        `);
    });

    function loadProducts() {
        $('#loading_overlay').addClass('active');
        
        $.ajax({
            url: '/products/get-filtered-products-for-stock',
            type: 'GET',
            data: {
                location_id: $('#location_id').val(),
                product_type: $('#product_type').val(),
                category_id: $('#category_id').val(),
                brand_id: $('#brand_id').val()
            },
            success: function(response) {
                $('#loading_overlay').removeClass('active');
                
                if (response.success && response.products && response.products.length > 0) {
                    renderProductsTable(response.products);
                } else {
                    $('#products_container').html(`
                        <div class="empty-state">
                            <i class="fas fa-search"></i>
                            <h4>@lang('lang_v1.no_products_found')</h4>
                            <p>@lang('lang_v1.try_different_filters')</p>
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                $('#loading_overlay').removeClass('active');
                toastr.error('@lang('messages.something_went_wrong')');
            }
        });
    }

    function renderProductsTable(products) {
        let html = `
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>@lang('sale.product')</th>
                        <th>@lang('product.sku')</th>
                        <th>@lang('lang_v1.variation')</th>
                        <th>@lang('report.current_stock')</th>
                        <th>@lang('lang_v1.add_stock')</th>
                    </tr>
                </thead>
                <tbody>
        `;

        products.forEach(function(product) {
            let stockClass = 'positive';
            if (product.current_stock <= 0) {
                stockClass = 'negative';
            } else if (product.current_stock < 10) {
                stockClass = 'warning';
            }

            html += `
                <tr>
                    <td>
                        <div class="product-info">
                            <div>
                                <div class="product-name">${product.product_name}</div>
                            </div>
                        </div>
                    </td>
                    <td>${product.sku || '-'}</td>
                    <td>${product.variation_name && product.variation_name !== 'DUMMY' ? product.variation_name : '-'}</td>
                    <td><span class="stock-badge ${stockClass}">${parseFloat(product.current_stock || 0).toFixed(0)}</span></td>
                    <td>
                        <input type="number" class="stock-input" 
                               data-product-id="${product.product_id}" 
                               data-variation-id="${product.variation_id}"
                               placeholder="0" min="0" step="1">
                    </td>
                </tr>
            `;
        });

        html += `
                </tbody>
            </table>
        `;

        $('#products_container').html(html);
    }
});
</script>
@endsection
