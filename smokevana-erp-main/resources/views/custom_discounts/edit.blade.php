@extends('layouts.app')

@section('title', __('lang_v1.offer.edit_offer'))

@section('css')
    <style>
        /* (Copy all CSS from create.blade.php here for consistency) */
        .alert { margin-bottom: 1rem; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .loading { opacity: 0.7; cursor: not-allowed !important; }
        button[type="submit"]:disabled { cursor: not-allowed; opacity: 0.7; }
        .switch .slider { transition: background-color 0.3s ease, transform 0.3s ease; }
        .switch .slider span { transition: right 0.3s ease, transform 0.3s ease; }
        .switch input:checked+.slider { background-color: #4a90e2 !important; transform: scale(1.05); }
        .switch input:not(:checked)+.slider { background-color: #ccc !important; transform: scale(1); }
        .switch input:checked+.slider span { right: 2px !important; transform: scale(1.1); }
        .switch input:not(:checked)+.slider span { right: 22px !important; transform: scale(1); }
        .switch:hover .slider { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2); }
        .switch input:checked+.slider:hover { background-color: #357abd !important; }
        .switch input:not(:checked)+.slider:hover { background-color: #bbb !important; }
        .input-error { color: #d9534f; font-size: 0.95em; margin-top: 2px; }
        .input-invalid, .select2-container--default .select2-selection--single.input-invalid, .select2-container--default .select2-selection--multiple.input-invalid { border: 1.5px solid #d9534f !important; box-shadow: 0 0 2px #d9534f; }
    </style>
@endsection

@section('content')
    {{-- Offer Edit Form --}}
    <!-- Content Header (Page header) -->
    <section class="content-header tw-flex tw-justify-between tw-mt-2 tw-mb-2" style="width: 100%">
        <div>
            <h1 style="font-size: 24px; font-weight: bold; color: #333; padding: 0; margin: 0;">Edit Offer</h1>
            <p style="color: #666;">Update your promotional offer settings</p>
        </div>
        <div class="text-right">
            <button type="submit" class="btn btn-primary" id = 'edit_offer_form' >Update</button>
        </div>
    </section>

    <section class="content">
        <input type="text" class="hide discount_id" value={{$custom_discount->id}}>
        @php
// Helper to get old value or from model
function field($name, $default = null)
{
    return old($name, $default);
}
$filter = $custom_discount->filter ? json_decode($custom_discount->filter, true) : null;
$rulesOnCustomer = $custom_discount->rulesOnCustomer ? json_decode($custom_discount->rulesOnCustomer, true) : null;
$custom_meta = $custom_discount->custom_meta ? json_decode($custom_discount->custom_meta, true) : null;
$rulesOnCart = $custom_discount->rulesOnCart ? json_decode($custom_discount->rulesOnCart, true) : null;
        @endphp
        <!-- ================= Basic Information Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Basic Information', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
        <div class="row">
            <div class="col-md-3">
                {!! Form::label('rule_name', 'Rule Name *') !!}
                {!! Form::text('rule_name', old('rule_name', $custom_discount->couponName), ['class' => 'form-control', 'placeholder' => 'eg. Winter Sale', 'required', 'minlength' => '3', 'maxlength' => '100']) !!}
            </div>
            <div class="col-md-3">
                {!! Form::label('lable_name', 'Lable Name ') !!}
                @show_tooltip('This discount label name visibility to Ecom') 
                {!! Form::text('lable_name', old('lable_name', $custom_discount->discount_lable), ['class' => 'form-control', 'placeholder' => 'eg. Winter Sale' , 'minlength' => '3', 'maxlength' => '100']) !!}
            </div>
            <div class="col-md-3">
                {!! Form::label('rule_type', 'Rule Type *') !!}
                {!! Form::select('rule_type', ['productAdjustment' => 'Product Adjustment', 'buyXgetY' => 'Buy X Get Y', 'cartAdjustment' => 'Cart Adjustment', 'freeShipping' => 'Free Shipping'], old('rule_type', $custom_discount->discountType), ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    {!! Form::checkbox('coupon_code_based', 1, !empty($custom_discount->couponCode), ['id' => 'coupon_code_based']) !!}
                    {!! Form::label('coupon_code_based', 'Coupon Code Based', ['style' => 'margin-left:5px;']) !!}
                </div>
                {!! Form::text('coupon_code', old('coupon_code', $custom_discount->couponCode), ['class' => 'form-control', 'placeholder' => 'eg : ADNEW100', 'disabled' => empty($custom_discount->couponCode), 'pattern' => '[A-Za-z0-9_-]+', 'minlength' => '3', 'maxlength' => '50']) !!}
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <div>
                    {!! Form::label('is_active', 'Rule is Enable') !!}<br>
                    <label class="switch">
                        {!! Form::checkbox('is_active', 1, !$custom_discount->isDisabled, ['class' => 'form-control', 'style' => 'display:none']) !!}
                        <span class="slider round" style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                            <span style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                        </span>
                    </label>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div>
                    {!! Form::label('is_apply_for_all', 'Apply for all Products') !!}<br>
                    <label class="switch">
                        {!! Form::checkbox('is_apply_for_all', 1, empty($custom_discount->filter), ['class' => 'form-control', 'style' => 'display:none']) !!}
                        <span class="slider round" style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                            <span style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                        </span>
                    </label>
                </div>
            </div>

            @if( session('business')->enable_referal_program)
            <div class="col-md-2 d-flex align-items-end">
                <div>
                    {!! Form::label('is_referal_program_discount', 'Is Referal Program Discount') !!}<br>
                    <label class="switch">
                        {!! Form::checkbox('is_referal_program_discount', 1, $custom_discount->is_referal_program_discount, ['class' => 'form-control', 'style' => 'display:none']) !!}
                        <span class="slider round" style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                            <span style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                        </span>
                    </label>
                </div>
            </div>
            @endif
        </div>
        @endcomponent
        <!-- ================= Filter Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Filter', 'title_svg' => '<i class=\'fa fa-filter\'></i>'])
        <div id="filter-rows">
            @if($filter)
                @foreach($filter as $key => $value)
                    @php
        $type = $key;
        $isIn = strpos($key, 'not_') === false;
        $typeName = str_replace('not_', '', $key);
        $selectType = $typeName === 'categories' ? 'category' : ($typeName === 'brand' ? 'brand' : 'product_ids');
        $ids = $value['ids'] ?? [];
                    @endphp
                    <div class="row align-items-end filter-row">
                        <div class="col-md-3">
                            <label>{{ $is_b2c ? 'Choose (Categories/Product)' : 'Choose (Categories/Brand/Product)' }}</label>
                            <select class="form-control filter-type-select" name="filter_type">
                                <option value="categories" {{ $typeName == 'categories' ? 'selected' : '' }}>Categories</option>
                                @if(!$is_b2c)
                                <option value="brand" {{ $typeName == 'brand' ? 'selected' : '' }}>Brand</option>
                                @endif
                                <option value="products" {{ $typeName == 'product_ids' ? 'selected' : '' }}>Products</option>
                            </select>
                        </div>
                        <div class="col-md-6 filter-categories-group {{ $typeName != 'categories' ? 'hide' : '' }}">
                            <label>Select Categories</label>
                            <select class="form-control select2 multi-search" name="categories" multiple data-type="category">
                                @php
        $categoryOptions = $type === 'not_categories' ? ($notCategories ?? []) : ($selectedCategories ?? []);
                                @endphp
                                @foreach($categoryOptions as $id => $name)
                                    <option value="{{ $id }}" selected>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 filter-brand-group {{ $typeName != 'brand' ? 'hide' : '' }}">
                            <label>Select Brand</label>
                            <select class="form-control select2 multi-search" name="brand" multiple data-type="brand">
                                @php
        $brandOptions = $type === 'not_brand' ? ($notBrands ?? []) : ($selectedBrands ?? []);
                                @endphp
                                @foreach($brandOptions as $id => $name)
                                    <option value="{{ $id }}" selected>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 filter-products-group {{ $typeName != 'product_ids' ? 'hide' : '' }}">
                            <label>Select Products</label>
                            <select class="form-control select2 multi-search" name="products" multiple data-type="product">
                                @php
        $productOptions = $type === 'not_product_ids' ? ($notProducts ?? []) : ($selectedProducts ?? []);
                                @endphp
                                @foreach($productOptions as $id => $name)
                                    <option value="{{ $id }}" selected>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 d-flex">
                            <div>
                                <label>Is In</label><br>
                                <label class="switch">
                                    <input type="checkbox" class="form-control" name="is_filter_in" style="display:none" {{ $isIn ? 'checked' : '' }}>
                                    <span class="slider round" style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                                        <span style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex flex-column">
                            <label>&nbsp;</label>
                            <div class="mt-auto d-flex">
                                <button type="button" class="btn btn-danger btn-sm ml-2 delete-filter-row"><i class="fa fa-trash"></i></button>
                                <a href="#" class="text-primary add-filter-row"><i class="fa fa-plus-circle"></i> Add another filter</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
             <div class="row align-items-end filter-row">
                <div class="col-md-3">
                    {{-- Filter Type Dropdown --}}
                    {!! Form::label('filter_type', $is_b2c ? 'Choose (Categories/Product)' : 'Choose (Categories/Brand/Product)') !!}
                    {!! Form::select('filter_type', array_filter(['categories' => 'Categories', 'brand' => $is_b2c ? null : "Brand", 'products' => 'Products']), null, ['class' => 'form-control filter-type-select']) !!}
                </div>
                <div class="col-md-6 filter-categories-group ">
                    {{-- Categories Multi-select --}}
                    {!! Form::label('categories', 'Select Categories') !!}
                    {!! Form::select('categories', [], null, ['class' => 'form-control select2 multi-search', 'multiple' => 'multiple', 'data-type' => 'category']) !!}
                </div>
                <div class="col-md-6 filter-brand-group">
                    {{-- Brand Multi-select --}}
                    {!! Form::label('brand', 'Select Brand') !!}
                    {!! Form::select('brand', [], null, ['class' => 'form-control select2 multi-search', 'multiple' => 'multiple', 'data-type' => 'brand']) !!}
                </div>
                <div class="col-md-6 filter-products-group">
                    {{-- Products Multi-select --}}
                    {!! Form::label('products', 'Select Products') !!}
                    {!! Form::select('products', [], null, ['class' => 'form-control select2 multi-search', 'multiple' => 'multiple', 'data-type' => 'product']) !!}
                </div>
                <div class="col-md-1 d-flex ">
                    <div>
                        {{-- Is In Toggle --}}
                        {!! Form::label('is_filter_in', 'Is In') !!}<br>
                        <label class="switch">
                            {!! Form::checkbox('is_filter_in', 1, true, ['class' => 'form-control', 'style' => 'display:none']) !!}
                            <span class="slider round"
                                style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                                <span
                                    style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="col-md-2 d-flex flex-column">
                    <label>&nbsp;</label>
                    <div class="mt-auto d-flex">
                        {{-- Delete and Add Filter Row Buttons --}}
                        <button type="button" class="btn btn-danger btn-sm ml-2 delete-filter-row" style="display:none;"><i class="fa fa-trash"></i></button>
                        <a href="#" class="text-primary add-filter-row"><i class="fa fa-plus-circle"></i> Add another
                            filter</a>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endcomponent
        <!-- ================= Discount Configuration Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => ' Discount Configuration', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
        <div class="row">
            <div class="col-md-4">
                {!! Form::label('discount_type', 'Discount Type') !!}
                {!! Form::select('discount_type', ['Percentage Discount' => 'Percentage Discount', 'Fixed Discount' => 'Fixed Discount'], old('discount_type', $custom_discount->discount == 'percentageDiscount' ? 'Percentage Discount' : 'Fixed Discount'), ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="col-md-4">
                {!! Form::label('discount_value', 'Discount Value') !!}
                {!! Form::number('discount_value', old('discount_value', $custom_discount->discountValue), ['class' => 'form-control', 'placeholder' => 'e.g.10', 'min' => '1', 'step' => '1']) !!}
            </div>
            <div class="col-md-2">
                {!! Form::label('minimum_quantity', 'Minimum Quantity') !!}
                {!! Form::number('minimum_quantity', old('minimum_quantity', $custom_discount->minBuyQty), ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '1', 'step' => '1']) !!}
            </div>
            <div class="col-md-2">
                {!! Form::label('maximum_quantity', 'Maximum Quantity') !!}
                {!! Form::number('maximum_quantity', old('maximum_quantity', $custom_discount->maxBuyQty), ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '1', 'step' => '1']) !!}
            </div>
        </div>
        @endcomponent
        <!-- ================= Buy X Get Y Configuration Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Buy X Get Y Configuration', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
        <div class="row">
            <div class="col-md-3">
                {!! Form::label('buy_quantity', 'Minimum Quantity') !!}
                {!! Form::number('buy_quantity', old('buy_quantity', $custom_meta['buy_quantity'] ?? null), ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '1', 'step' => '1']) !!}
            </div>
            <div class="col-md-1 d-flex ">
                <div>
                    {!! Form::label('is_recursive', 'Is Recursive') !!}<br>
                    <label class="switch">
                        {!! Form::checkbox('is_recursive', 1, $custom_meta['is_recursive'] ?? false, ['class' => 'form-control', 'style' => 'display:none']) !!}
                        <span class="slider round" style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                            <span style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
        <div class="bogo_products">
            @if(isset($selectedGetYProducts) && is_array($selectedGetYProducts) && count($selectedGetYProducts))
                @foreach($selectedGetYProducts as $bogo)
                    <div class="row bogo-product-row">
                        <div class="col-md-6 bogo-products-group">
                            <label>Select Product & Variation</label>
                            <select class="form-control select2 bogo-single-select" name="bogo_products[]" data-type="product_variations">
                                <option value="{{ $bogo['id'] }}" selected>{{ $bogo['text'] }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Quantity</label>
                            <input type="number" name="bogo_quantity[]" class="form-control" placeholder="e.g. 2" min="1" step="1" value="{{ $bogo['quantity'] }}">
                        </div>
                        <div class="col-md-1" style="display:flex; flex-direction:column">
                            <label>&nbsp;</label>
                            <i class="fa fa-trash remove-bogo-row" style="color: red;"></i>
                        </div>
                        <div class="col-md-1">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-success btn-sm add-bogo-row"><i class="fa fa-plus"></i> Add Product</button>
                        </div>
                    </div>
                @endforeach
            @elseif(isset($custom_meta['get_y_products']) && is_array($custom_meta['get_y_products']))
                @foreach($custom_meta['get_y_products'] as $bogo)
                    <div class="row bogo-product-row">
                        <div class="col-md-6 bogo-products-group">
                            <label>Select Product & Variation</label>
                            <select class="form-control select2 bogo-single-select" name="bogo_products[]" data-type="product_variations">
                                <option value="{{ $bogo['product_id'] }}{{ $bogo['variation_id'] ? '-' . $bogo['variation_id'] : '' }}" selected></option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Quantity</label>
                            <input type="number" name="bogo_quantity[]" class="form-control" placeholder="e.g. 2" min="1" step="1" value="{{ $bogo['quantity'] }}">
                        </div>
                        <div class="col-md-1" style="display:flex; flex-direction:column">
                            <label>&nbsp;</label>
                            <i class="fa fa-trash remove-bogo-row" style="color: red;"></i>
                        </div>
                        <div class="col-md-1">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-success btn-sm add-bogo-row"><i class="fa fa-plus"></i> Add Product</button>
                        </div>
                    </div>
                @endforeach
                @else
                 <div class="row bogo-product-row">
                        <div class="col-md-6 bogo-products-group">
                            <label>Select Product & Variation</label>
                            <select class="form-control select2 bogo-single-select" name="bogo_products[]" data-type="product_variations">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Quantity</label>
                            <input type="number" name="bogo_quantity[]" class="form-control" placeholder="e.g. 2" min="1" step="1" value="">
                        </div>
                        <div class="col-md-1" style="display:flex; flex-direction:column">
                            <label>&nbsp;</label>
                            <i class="fa fa-trash remove-bogo-row" style="color: red;"></i>
                        </div>
                        <div class="col-md-1">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-success btn-sm add-bogo-row"><i class="fa fa-plus"></i> Add Product</button>
                        </div>
                    </div>
            @endif
        </div>
        @endcomponent
        <!-- ================= Cart Adjustment Configuration Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Cart Adjustment Configuration', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
        <div class="row">
            <div class="col-md-2">
                {!! Form::label('min_order_value_cart_adjustment', 'Minimum Order Value') !!}
                {!! Form::number('min_order_value_cart_adjustment', old('min_order_value_cart_adjustment', $rulesOnCart['minOrderValue'] ?? null), ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '.01', 'step' => '.01']) !!}
            </div>
            <div class="col-md-2">
                {!! Form::label('max_discount_amount_cart_adjustment', 'Maximum discount Amount') !!}
                {!! Form::number('max_discount_amonut_cart_adjustment', old('max_discount_amonut_cart_adjustment', $rulesOnCart['maxDiscountAmount'] ?? null), ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '.01', 'step' => '.01']) !!}
            </div>
            <div class="col-md-4">
                {!! Form::label('discount_type_cart_adjustment', 'Discount Type') !!}
                {!! Form::select('discount_type_cart_adjustment', ['Percentage Discount' => 'Percentage Discount', 'Fixed Discount' => 'Fixed Discount'], old('discount_type_cart_adjustment', $custom_discount->discount == 'percentageDiscount' ? 'Percentage Discount' : 'Fixed Discount'), ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="col-md-2">
                {!! Form::label('discount_value_cart_adjustment', 'Discount Value') !!}
                {!! Form::number('discount_value_cart_adjustment', old('discount_value_cart_adjustment', $custom_discount->discountValue), ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '.01', 'step' => '.01']) !!}
            </div>
        </div>
        @endcomponent
        <!-- ================= Free Shipping Configuration Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Free Shipping Configuration', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
        <div class="row">
            <div class="col-md-2">
                {!! Form::label('min_order_value_shipping', 'Minimum Order Value') !!}
                {!! Form::number('min_order_value_shipping', old('min_order_value_shipping', $rulesOnCart['minOrderValue'] ?? null), ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '.01', 'step' => '.01']) !!}
            </div>
            <div class="col-md-2" hidden>
                {!! Form::label('max_discount_amount_shipping', 'Maximum discount Amount') !!}
                {!! Form::number('max_discount_amonut_shipping', old('max_discount_amonut_shipping', $rulesOnCart['maxDiscountAmount'] ?? null), ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '.01', 'step' => '.01']) !!}
            </div>
            <div class="col-md-4">
                {!! Form::label('discount_type_shipping', 'Discount Type') !!}
                {!! Form::select('discount_type_shipping', ['free' => "FREE"], old('discount_type_shipping', $custom_discount->discount == 'free' ? 'free' : ($custom_discount->discount == 'percentageDiscount' ? 'Percentage Discount' : 'Fixed Discount')), ['class' => 'form-control', 'required']) !!}        
            </div>
            <div class="col-md-4" hidden>
                {!! Form::label('discount_value_shipping', 'Discount Value') !!}
                {!! Form::number('discount_value_shipping', old('discount_value_shipping', $custom_discount->discountValue), ['class' => 'form-control', 'placeholder' => 'e.g.10', 'min' => '1', 'step' => '1']) !!}
            </div>
        </div>
        @endcomponent
        <!-- ================= Validity & Usage Limits Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Validity & Usage limits', 'style' => 'z-index:999;position:relative;', 'title_svg' => '<i class="fa fa-clock"></i>'])
        <div class="row">
            <div class="col-md-4">
                {!! Form::label('valid_from', 'Rule Valid From') !!}
                {!! Form::text('valid_from', old('valid_from', $custom_discount->applyDate), [
    'class' => 'form-control',
    'placeholder' => 'yyyy-mm-dd HH:mm:ss',
    'pattern' => '\\d{4}-\\d{2}-\\d{2} \\d{2}:\\d{2}:\\d{2}'
]) !!}
            </div>
            <div class="col-md-4">
                {!! Form::label('valid_to', 'Rule Valid To') !!}
                {!! Form::text('valid_to', old('valid_to', $custom_discount->endDate), [
    'class' => 'form-control',
    'placeholder' => 'yyyy-mm-dd HH:mm:ss',
    'pattern' => '\\d{4}-\\d{2}-\\d{2} \\d{2}:\\d{2}:\\d{2}'
]) !!}
            </div>
            <div class="col-md-2" hidden>
                {!! Form::label('per_customer_limit', 'Per Customer Limit') !!}
                {!! Form::number('per_customer_limit', old('per_customer_limit', $custom_discount->per_customer_limit), ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '1', 'step' => '1']) !!}
            </div>
            <div class="col-md-2" hidden>
                {!! Form::label('max_usage_limit', 'Maximum Usage Limit') !!}
                {!! Form::number('max_usage_limit', old('max_usage_limit', $custom_discount->useLimit), ['class' => 'form-control', 'placeholder' => 'e.g. 1000', 'min' => '1', 'step' => '1']) !!}
            </div>
        </div>
        @endcomponent
        <!-- ================= Description & Customer Groups Section ================= -->
        <div class="row">
            <div class="col-md-6">
                @component('components.widget', ['class' => 'box-primary', 'title' => 'Description', 'title_svg' => '<i class="fa fa-file"></i>'])
                <div class="form-group">
                    {!! Form::textarea('description', old('description', $custom_discount->description), ['class' => 'form-control description']) !!}
                </div>
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.widget', ['class' => 'box-primary', 'title' => 'Customer Groups', 'title_svg' => '<i class="fa fa-users"></i>'])
                @if (!empty($is_b2c))
                <div class="form-group">
                    {!! Form::select('customer_groups_type', ['all_customers' => 'All Customers', 'customers_list' => 'Customers List'], old('customer_groups_type', isset($rulesOnCustomer['applyOn']) ? ($rulesOnCustomer['applyOn'] == 'customer-list' ? 'customers_list' : ($rulesOnCustomer['applyOn'] == 'customer-group' ? 'customers_group_list' : 'all_customers')) : 'all_customers'), ['class' => 'form-control', 'id' => 'customer_groups_type']) !!}
                </div>
                @else
                <div class="form-group">
                    {!! Form::select('customer_groups_type', ['all_customers' => 'All Customers', 'customers_list' => 'Customers List', 'customers_group_list' => 'Customers Groups List'], old('customer_groups_type', isset($rulesOnCustomer['applyOn']) ? ($rulesOnCustomer['applyOn'] == 'customer-list' ? 'customers_list' : ($rulesOnCustomer['applyOn'] == 'customer-group' ? 'customers_group_list' : 'all_customers')) : 'all_customers'), ['class' => 'form-control', 'id' => 'customer_groups_type']) !!}
                </div>
                @endif
                
                <div id="customers_list_box">
                    <div class="row">
                        <div class="col-md-11">
                            {!! Form::label('customers_list[]', 'Customers list') !!}
                            <select name="customers_list[]" class="form-control select2 multi-search" multiple data-type="customer">
                                @foreach($selectedCustomers ?? [] as $id => $name)
                                    <option value="{{ $id }}" selected>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 d-flex ">
                            <div>
                                {!! Form::label('is_filter_in', 'Is In') !!}<br>
                                <label class="switch">
                                    {!! Form::checkbox('is_filter_in', 1, true, ['class' => 'form-control', 'style' => 'display:none']) !!}
                                    <span class="slider round" style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                                        <span style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="customers_group_list_box">
                    <div class="row">
                        <div class="col-md-11">
                            {!! Form::label('customers_group_list[]', 'Customers Groups') !!}
                            <select name="customers_group_list[]" class="form-control select2 multi-search" multiple data-type="customers_group">
                                @foreach($selectedCustomerGroups ?? [] as $id => $name)
                                    <option value="{{ $id }}" selected>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 d-flex ">
                            <div>
                                {!! Form::label('is_filter_in', 'Is In') !!}<br>
                                <label class="switch">
                                    {!! Form::checkbox('is_filter_in', 1, true, ['class' => 'form-control', 'style' => 'display:none']) !!}
                                    <span class="slider round" style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                                        <span style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group tw-mt-3">
                    {!! Form::label('customer_order_type', 'Order Value Type') !!}
                    {!! Form::select('customer_order_type', ['all_orders' => 'All Orders', 'first_order' => 'On First Order', 'on_last_order' => 'On Last Order Value'], old('customer_order_type', ($rulesOnCustomer['on-last-order-value'] ?? false) ? 'on_last_order' : (($rulesOnCustomer['on-first-order'] ?? false) ? 'first_order' : 'all_orders')), ['class' => 'form-control', 'id' => 'customer_order_type']) !!}
                    <div class="last_order_value_div tw-mt-3" style="display:none;">
                        {!! Form::label('last_order_value', 'Last Order Value') !!}
                        {!! Form::number('last_order_value', old('last_order_value', $rulesOnCustomer['last-order-value'] ?? null), ['class' => 'form-control', 'min' => '1', 'step' => '1']) !!}
                    </div>
                </div>
                @endcomponent
                @if(auth()->user()->can('access_all_locations') || auth()->user()->can('admin') || !empty($is_b2c))
                @component('components.widget', ['class' => 'box-primary', 'title' => 'Business Locations', 'title_svg' => '<i class="fa fa-file"></i>'])
                @if(auth()->user()->can('access_all_locations') || auth()->user()->can('admin'))
                    <div class="form-group">
                        {!! Form::label('business_location', __('purchase.business_location') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! Form::select('business_location', $business_locations, $selectedLocation, ['class' => 'form-control select2 select_location_id business_location', 'placeholder' => __('messages.please_select'),'required']) !!}
                        </div>
                    </div>
                @endif
                <div class="brand_select_div customer_fields @if(!$is_b2c) hide @endif">
                    <div class="form-group">
                        {!! Form::label('brand_id', 'Select Brand' . ':') !!}
                        <div class="brand_select">
                            {!! Form::select('brand_id', $brands, $selectedBrandids, ['class' => 'form-control select2', "required", "multiple" ,'style' => 'width: 100%;']) !!}
                        </div>
                    </div>
                </div>
                @endcomponent
                @endif
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script>
    $(document).ready(function () {
        // --- Toggle Switch Functionality ---
        $('input[name="is_active"]').on('change', function () {
            const toggle = $(this).next('span');
            const thumb = toggle.find('span');
            if ($(this).is(':checked')) {
                toggle.css('background-color', '#4a90e2');
                thumb.css('right', '2px');
            } else {
                toggle.css('background-color', '#ccc');
                thumb.css('right', '22px');
            }
        });
        $(document).on('change', 'input[name="is_filter_in"]', function () {
            const toggle = $(this).next('span');
            const thumb = toggle.find('span');
            if ($(this).is(':checked')) {
                toggle.css('background-color', '#4a90e2');
                thumb.css('right', '2px');
            } else {
                toggle.css('background-color', '#ccc');
                thumb.css('right', '22px');
            }
        });
        // --- Initialize TinyMCE for Description Field ---
        if ($('textarea.description').length > 0) {
            tinymce.init({ selector: 'textarea.description', height: 250 });
        }
        // --- Filter Row Visibility Logic ---
        function updateFilterRowVisibility($row) {
            var type = $row.find('.filter-type-select').val();
            $row.find('.filter-categories-group').toggleClass('hide', type !== 'categories');
            $row.find('.filter-brand-group').toggleClass('hide', type !== 'brand');
            $row.find('.filter-products-group').toggleClass('hide', type !== 'products');
        }
        $('#filter-rows .filter-row').each(function () { updateFilterRowVisibility($(this)); });
        $(document).on('change', '.filter-type-select', function () {
            var $row = $(this).closest('.filter-row');
            updateFilterRowVisibility($row);
        });
        // --- Add Filter Row Functionality ---
        $(document).on('click', '.add-filter-row', function (e) {
            e.preventDefault();
            var newRowHtml = `
                <div class="row align-items-end filter-row">
                    <div class="col-md-3">
                        <label>Choose (Categories/Brand/Product)</label>
                        <select class="form-control filter-type-select" name="filter_type">
                            <option value="categories" selected>Categories</option>
                            @if(!$is_b2c)
                            <option value="brand">Brand</option>
                            @endif
                            <option value="products">Products</option>
                        </select>
                    </div>
                    <div class="col-md-6 filter-categories-group">
                        <label>Select Categories</label>
                        <select class="form-control select2 multi-search" name="categories" multiple data-type="category"></select>
                    </div>
                    <div class="col-md-6 filter-brand-group hide">
                        <label>Select Brand</label>
                        <select class="form-control select2 multi-search" name="brand" multiple data-type="brand"></select>
                    </div>
                    <div class="col-md-6 filter-products-group hide">
                        <label>Select Products</label>
                        <select class="form-control select2 multi-search" name="products" multiple data-type="product"></select>
                    </div>
                    <div class="col-md-1 d-flex">
                        <div>
                            <label>Is In</label><br>
                            <label class="switch">
                                <input type="checkbox" class="form-control" name="is_filter_in" style="display:none" checked>
                                <span class="slider round" style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                                    <span style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex flex-column">
                        <label>&nbsp;</label>
                        <div class="mt-auto d-flex">
                            <button type="button" class="btn btn-danger btn-sm ml-2 delete-filter-row"><i class="fa fa-trash"></i></button>
                            <a href="#" class="text-primary add-filter-row"><i class="fa fa-plus-circle"></i> Add another filter</a>
                        </div>
                    </div>
                </div>
            `;
            var $newRow = $(newRowHtml);
            $newRow.find('.multi-search').select2({
                placeholder: 'Search...',
                minimumInputLength: 1,
                width: '100%',
                ajax: {
                    url: '/multi-select/search',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { s: params.term, type: $(this).data('type') };
                    },
                    processResults: function (data) {
                        return { results: $.map(data.result, function (item) {
                            if (item.name && item.id) return { id: item.id, text: item.name };
                            else if (item.sku && item.name) return { id: item.id, text: item.name + ' (' + item.sku + ')' };
                            return { id: item.id, text: item.name || item.sku || item.id };
                        }) };
                    },
                    cache: true
                }
            });
            $('#filter-rows').append($newRow);
            $('#filter-rows .add-filter-row').hide();
            $('#filter-rows .add-filter-row:last').show();
            $newRow.find('.filter-categories-group').removeClass('hide');
            $newRow.find('.filter-brand-group, .filter-products-group').addClass('hide');
        });
        $(document).on('click', '.delete-filter-row', function () {
            $(this).closest('.filter-row').remove();
            $('#filter-rows .add-filter-row').hide();
            $('#filter-rows .add-filter-row:last').show();
        });
        // --- Datepicker Initialization ---
        $('input[name="valid_from"], input[name="valid_to"]').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            showTodayButton: true,
            showClear: true,
            showClose: true
        });
        // --- Customer Group Select Visibility ---
        function updateCustomerGroupSelect() {
            var type = $('#customer_groups_type').val();
            if (type === 'customers_list') {
                $('#customers_group_list_box select').val(null).trigger('change');
                $('#customers_list_box').show();
                $('#customers_group_list_box').hide();
            } else if (type === 'customers_group_list') {
                $('#customers_list_box select').val(null).trigger('change');
                $('#customers_list_box').hide();
                $('#customers_group_list_box').show();
            } else {
                $('#customers_list_box select').val(null).trigger('change');
                $('#customers_group_list_box select').val(null).trigger('change');
                $('#customers_list_box').hide();
                $('#customers_group_list_box').hide();
            }
        }
        $('#customer_groups_type').on('change', updateCustomerGroupSelect);
        updateCustomerGroupSelect();
        // --- Initialize Select2 for AJAX Multi-Search ---
        $('.multi-search').select2({
            placeholder: 'Search...',
            minimumInputLength: 1,
            width: '100%',
            ajax: {
                url: '/multi-select/search',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { 
                        s: params.term, 
                        type: $(this).data('type'),
                        location_id: $('select[name="business_location"]').val() || null
                    };
                },
                processResults: function (data) {
                    return { results: $.map(data.result, function (item) {
                        if (item.name && item.id) return { id: item.id, text: item.name };
                        else if (item.sku && item.name) return { id: item.id, text: item.name + ' (' + item.sku + ')' };
                        return { id: item.id, text: item.name || item.sku || item.id };
                    }) };
                },
                cache: true
            }
        });
        
        // --- Brand Selection Logic ---
        // Handle "All Brands" vs specific brand selection
        $('select[name="brand_id"]').on('change', function() {
            var selectedValues = $(this).val() || [];
            var allBrandsValue = 'all';
            
            // Only handle mutual exclusivity between "All Brands" and specific brands
            if (selectedValues.includes(allBrandsValue) && selectedValues.length > 1) {
                // If "All Brands" is selected along with other brands, remove "All Brands"
                var filteredValues = selectedValues.filter(function(value) {
                    return value !== allBrandsValue;
                });
                $(this).val(filteredValues).trigger('change');
            }
        });
        
        // --- Last Order Value Input Toggle ---
        function toggleLastOrderValueInput() {
            var val = $('#customer_order_type').val();
            if (val === 'on_last_order') {
                $('.last_order_value_div').show();
            } else {
                $('.last_order_value_div').hide();
                $('input[name="last_order_value"]').val('');
            }
        }
        $('#customer_order_type').on('change', toggleLastOrderValueInput);
        toggleLastOrderValueInput();
        // --- Coupon Code Input Toggle ---
        function toggleCouponCodeInput() {
            if ($('#coupon_code_based').is(':checked')) {
                $('input[name="coupon_code"]').prop('disabled', false);
                $('input[name="coupon_code"]').prop('required', true);
            } else {
                $('input[name="coupon_code"]').prop('disabled', true);
                $('input[name="coupon_code"]').prop('required', false);
                $('input[name="coupon_code"]').val('');
            }
        }
        $('#coupon_code_based').on('change', toggleCouponCodeInput);
        toggleCouponCodeInput();
        // --- Clear Discount Configuration Fields ---
        function clearDiscountConfigFields() {
            // Clear Discount Configuration (Product Adjustment) fields
            $('select[name="discount_type"]').val('').trigger('change');
            $('input[name="discount_value"]').val('');
            $('input[name="minimum_quantity"]').val('');
            $('input[name="maximum_quantity"]').val('');
            
            // Clear Buy X Get Y Configuration fields
            $('input[name="buy_quantity"]').val('');
            $('input[name="is_recursive"]').prop('checked', false);
            // Update toggle switch visual state
            var $isRecursiveToggle = $('input[name="is_recursive"]').next('span');
            if ($isRecursiveToggle.length) {
                var $thumb = $isRecursiveToggle.find('span');
                $isRecursiveToggle.css('background-color', '#ccc');
                $thumb.css('right', '22px');
            }
            // Clear all BOGO product rows except the first one, then clear the first one
            $('.bogo-product-row').not(':first').remove();
            $('.bogo-product-row:first select[name="bogo_products[]"]').val(null).trigger('change');
            $('.bogo-product-row:first input[name="bogo_quantity[]"]').val('');
            if (typeof updateBogoRowButtons === 'function') {
                updateBogoRowButtons();
            }
            
            // Clear Cart Adjustment Configuration fields
            $('input[name="min_order_value_cart_adjustment"]').val('');
            $('input[name="max_discount_amonut_cart_adjustment"]').val('');
            $('select[name="discount_type_cart_adjustment"]').val('').trigger('change');
            $('input[name="discount_value_cart_adjustment"]').val('');
            
            // Clear Free Shipping Configuration fields
            $('input[name="min_order_value_shipping"]').val('');
            $('input[name="max_discount_amonut_shipping"]').val('');
            $('select[name="discount_type_shipping"]').val('').trigger('change');
            $('input[name="discount_value_shipping"]').val('');
        }
        // --- Section Visibility by Rule Type ---
        function toggleSectionsByRuleType() {
            var ruleType = $('select[name="rule_type"]').val();

            // Hide/show specific fields in Validity & Usage limits section
            // if (ruleType === 'buyXgetY' || ruleType === 'freeShipping') {
            //     // Hide per_customer_limit and max_usage_limit fields
            //     $('input[name="per_customer_limit"]').closest('.col-md-2').hide();
            //     $('input[name="max_usage_limit"]').closest('.col-md-2').hide();
            // } else {
            //     // Show per_customer_limit and max_usage_limit fields
            //     $('input[name="per_customer_limit"]').closest('.col-md-2').show();
            //     $('input[name="max_usage_limit"]').closest('.col-md-2').show();
            // }

            if (ruleType === 'buyXgetY') {
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Discount Configuration') $(this).hide();
                });
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Buy X Get Y Configuration') $(this).show();
                });
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Cart Adjustment Configuration' || title === 'Free Shipping Configuration') $(this).hide();
                });
            } else if (ruleType === 'cartAdjustment') {
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Discount Configuration') $(this).hide();
                });
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Cart Adjustment Configuration') $(this).show();
                });
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Buy X Get Y Configuration' || title === 'Free Shipping Configuration') $(this).hide();
                });
            } else if (ruleType === 'freeShipping') {
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Discount Configuration') $(this).hide();
                });
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Free Shipping Configuration') $(this).show();
                });
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Buy X Get Y Configuration' || title === 'Cart Adjustment Configuration') $(this).hide();
                });
            } else {
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Discount Configuration') $(this).show();
                });
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Buy X Get Y Configuration' || title === 'Cart Adjustment Configuration' || title === 'Free Shipping Configuration') $(this).hide();
                });
            }
        }
        toggleSectionsByRuleType();
        $('select[name="rule_type"]').on('change', function() {
            clearDiscountConfigFields();
            toggleSectionsByRuleType();
        });
        // --- Filter Section Visibility by "Apply for all Products" ---
        function toggleFilterSection() {
            var isApplyForAll = $('input[name="is_apply_for_all"]').is(':checked');
            if (isApplyForAll) {
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Filter') $(this).hide();
                });
            } else {
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Filter') $(this).show();
                });
            }
        }
        toggleFilterSection();
        $('input[name="is_apply_for_all"]').on('change', function () {
            toggleFilterSection();
            const toggle = $(this).next('span');
            const thumb = toggle.find('span');
            if ($(this).is(':checked')) {
                toggle.css('background-color', '#4a90e2');
                thumb.css('right', '2px');
            } else {
                toggle.css('background-color', '#ccc');
                thumb.css('right', '22px');
            }
        });
        // --- BOGO Products Functionality ---
        function updateBogoRowButtons() {
            var $rows = $('.bogo-product-row');
            if ($rows.length > 1) $rows.find('.remove-bogo-row').show();
            else $rows.find('.remove-bogo-row').hide();
            $rows.find('.add-bogo-row').hide();
            $rows.last().find('.add-bogo-row').show();
        }
        $(document).on('click', '.add-bogo-row', function (e) {
            e.preventDefault();
            var newRowHtml = `
                <div class="row bogo-product-row">
                    <div class="col-md-6 bogo-products-group">
                        <label>Select Product & Variation</label>
                        <select class="form-control select2 bogo-single-select" name="bogo_products[]" data-type="product_variations"></select>
                    </div>
                    <div class="col-md-2">
                        <label>Quantity</label>
                        <input type="number" name="bogo_quantity[]" class="form-control" placeholder="e.g. 2" min="1" step="1">
                    </div>
                    <div class="col-md-1" style="display:flex; flex-direction:column">
                        <label>&nbsp;</label>
                        <i class="fa fa-trash remove-bogo-row" style="color: red;"></i>
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-success btn-sm add-bogo-row"><i class="fa fa-plus"></i> Add Product</button>
                    </div>
                </div>
            `;
            var $newRow = $(newRowHtml);
            $newRow.find('.bogo-single-select').select2({
                placeholder: 'Search product & variation...',
                minimumInputLength: 1,
                width: '100%',
                ajax: {
                    url: '/multi-select/search',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { s: params.term, type: 'product_variations' };
                    },
                    processResults: function (data) {
                        return { results: $.map(data.result, function (item) {
                            if (item.product_id) return { id: item.variation_id ? (item.product_id + '-' + item.variation_id) : (item.product_id + ''), text: (item.product_name || '') + (item.variation_name ? ' - ' + item.variation_name : '') };
                            return null;
                        }).filter(Boolean) };
                    },
                    cache: true
                }
            });
            $('.bogo_products').append($newRow);
            updateBogoRowButtons();
        });
        $(document).on('click', '.remove-bogo-row', function () {
            $(this).closest('.bogo-product-row').remove();
            updateBogoRowButtons();
        });
        updateBogoRowButtons();
        $('.bogo-single-select').select2({
            placeholder: 'Search product & variation...',
            minimumInputLength: 1,
            width: '100%',
            ajax: {
                url: '/multi-select/search',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { s: params.term, type: 'product_variations' };
                },
                processResults: function (data) {
                    return { results: $.map(data.result, function (item) {
                        if (item.product_id) return { id: item.variation_id ? (item.product_id + '-' + item.variation_id) : (item.product_id + ''), text: (item.product_name || '') + (item.variation_name ? ' - ' + item.variation_name : '') };
                        return null;
                    }).filter(Boolean) };
                },
                cache: true
            }
        });
        // --- VALIDATION LOGIC ---
        function showFieldError($input, message) {
            removeFieldError($input);
            $input.addClass('input-invalid');
            $input.focus();
            if ($input.hasClass('select2-hidden-accessible')) {
                $input.next('.select2-container').find('.select2-selection').addClass('input-invalid');
                $input.select2('open');
            }
            if ($input.next('.input-error').length === 0) {
                $input.after('<div class="input-error">' + message + '</div>');
            }
        }
        function removeFieldError($input) {
            $input.removeClass('input-invalid');
            $input.next('.input-error').remove();
            if ($input.hasClass('select2-hidden-accessible')) {
                $input.next('.select2-container').find('.select2-selection').removeClass('input-invalid');
            }
        }
        $(document).on('input change', '.input-invalid, .form-control, .select2-hidden-accessible', function () {
            removeFieldError($(this));
        });
        // --- Form Validation ---
        function validateForm() {
            // Remove all previous errors
            $('.input-error').remove();
            $('.input-invalid').removeClass('input-invalid');
            $('.select2-selection.input-invalid').removeClass('input-invalid');
            // Validate Rule Name
            var $ruleName = $('input[name="rule_name"]');
            var ruleName = $ruleName.val();
            if (!ruleName || ruleName.length < 3 || ruleName.length > 100) {
                showFieldError($ruleName, 'Rule Name is required (3-100 characters).');
                return false;
            }
            // Validate Rule Type
            var $ruleType = $('select[name="rule_type"]');
            var ruleType = $ruleType.val();
            if (!ruleType) {
                showFieldError($ruleType, 'Rule Type is required.');
                return false;
            }
            // Coupon code if enabled
            if ($('#coupon_code_based').is(':checked')) {
                var $couponCode = $('input[name="coupon_code"]');
                var couponCode = $couponCode.val();
                if (!couponCode || !/^[A-Za-z0-9_-]{3,50}$/.test(couponCode)) {
                    showFieldError($couponCode, 'Coupon Code is required and must be 3-50 characters (letters, numbers, _ or -).');
                    return false;
                }
            }
            // Discount Configuration (if visible)
            if ($('select[name="rule_type"]').val() === 'productAdjustment' && $("select[name='discount_type']").is(':visible')) {
                var $discountType = $('select[name="discount_type"]');
                var $discountValue = $('input[name="discount_value"]');
                var discountType = $discountType.val();
                var discountValue = $discountValue.val();
                if (!discountType) {
                    showFieldError($discountType, 'Discount Type is required.');
                    return false;
                }
                if (!discountValue || isNaN(discountValue) || Number(discountValue) < 1) {
                    showFieldError($discountValue, 'Discount Value is required and must be at least 1.');
                    return false;
                }
                if (discountType === 'Percentage Discount' && Number(discountValue) > 100) {
                    showFieldError($discountValue, 'Percentage discounts cannot exceed 100%.');
                    return false;
                }
            }
            // Min/Max Quantity
            var $minQty = $('input[name="minimum_quantity"]');
            if ($minQty.is(':visible')) {
                var minQty = $minQty.val();
                if (minQty && (isNaN(minQty) || Number(minQty) < 1)) {
                    showFieldError($minQty, 'Minimum Quantity must be at least 1.');
                    return false;
                }
            }
            var $maxQty = $('input[name="maximum_quantity"]');
            if ($maxQty.is(':visible')) {
                var maxQty = $maxQty.val();
                if (maxQty && (isNaN(maxQty) || Number(maxQty) < 1)) {
                    showFieldError($maxQty, 'Maximum Quantity must be at least 1.');
                    return false;
                }
            }
            // BOGO (Buy X Get Y)
            if (ruleType === 'buyXgetY') {
                var $buyQty = $('input[name="buy_quantity"]');
                var buyQty = $buyQty.val();
                if (!buyQty || isNaN(buyQty) || Number(buyQty) < 1) {
                    showFieldError($buyQty, 'Minimum Quantity for Buy X Get Y is required and must be at least 1.');
                    return false;
                }
                var bogoValid = true;
                var $firstInvalidBogo = null;
                $('.bogo-product-row').each(function () {
                    var $prod = $(this).find('select[name="bogo_products[]"]');
                    var $qty = $(this).find('input[name="bogo_quantity[]"]');
                    var prod = $prod.val();
                    var qty = $qty.val();
                    if (!prod) {
                        showFieldError($prod, 'Select a product/variation.');
                        if (!$firstInvalidBogo) $firstInvalidBogo = $prod;
                        bogoValid = false;
                    }
                    if (!qty || isNaN(qty) || Number(qty) < 1) {
                        showFieldError($qty, 'Quantity is required and must be at least 1.');
                        if (!$firstInvalidBogo) $firstInvalidBogo = $qty;
                        bogoValid = false;
                    }
                });
                if (!bogoValid) {
                    if ($firstInvalidBogo) $firstInvalidBogo.focus();
                    return false;
                }
            }
            // Cart Adjustment
            if (ruleType === 'cartAdjustment') {
                var $minOrder = $('input[name="min_order_value_cart_adjustment"]');
                var $maxDiscount = $('input[name="max_discount_amonut_cart_adjustment"]');
                var $discountTypeCart = $('select[name="discount_type_cart_adjustment"]');
                var $discountValueCart = $('input[name="discount_value_cart_adjustment"]');
                var minOrder = $minOrder.val();
                var maxDiscount = $maxDiscount.val();
                var discountTypeCart = $discountTypeCart.val();
                var discountValueCart = $discountValueCart.val();
                // if (!minOrder || isNaN(minOrder) || Number(minOrder) < 1) {
                //     showFieldError($minOrder, 'Minimum Order Value for Cart Adjustment is required and must be at least 1.');
                //     return false;
                // }
                // if (!maxDiscount || isNaN(maxDiscount) || Number(maxDiscount) < 1) {
                //     showFieldError($maxDiscount, 'Maximum Discount Amount for Cart Adjustment is required and must be at least 1.');
                //     return false;
                // }
                if (!discountTypeCart) {
                    showFieldError($discountTypeCart, 'Discount Type for Cart Adjustment is required.');
                    return false;
                }
                if (!discountValueCart || isNaN(discountValueCart) || Number(discountValueCart) < 1) {
                    showFieldError($discountValueCart, 'Discount Value for Cart Adjustment is required and must be at least 1.');
                    return false;
                }
                if (discountTypeCart === 'Percentage Discount' && Number(discountValueCart) > 100) {
                    showFieldError($discountValueCart, 'Percentage discounts cannot exceed 100%.');
                    return false;
                }
            }
            // Free Shipping
            if (ruleType === 'freeShipping') {
                var $minOrderShip = $('input[name="min_order_value_shipping"]');
                var $maxDiscountShip = $('input[name="max_discount_amonut_shipping"]');
                var $discountTypeShip = $('select[name="discount_type_shipping"]');
                var $discountValueShip = $('input[name="discount_value_shipping"]');
                var minOrderShip = $minOrderShip.val();
                var maxDiscountShip = $maxDiscountShip.val();
                var discountTypeShip = $discountTypeShip.val();
                var discountValueShip = $discountValueShip.val();
                if (!minOrderShip || isNaN(minOrderShip) || Number(minOrderShip) < 1) {
                    showFieldError($minOrderShip, 'Minimum Order Value for Free Shipping is required and must be at least 1.');
                    return false;
                }
                
                // Only validate max discount amount if the field is visible
                if ($maxDiscountShip.is(':visible') && (!maxDiscountShip || isNaN(maxDiscountShip) || Number(maxDiscountShip) < 1)) {
                    showFieldError($maxDiscountShip, 'Maximum Discount Amount for Free Shipping is required and must be at least 1.');
                    return false;
                }
                if (!discountTypeShip) {
                    showFieldError($discountTypeShip, 'Discount Type for Free Shipping is required.');
                    return false;
                }
                
                // Only validate discount value if the field is visible and not FREE
                if ($discountValueShip.is(':visible') && discountTypeShip !== 'free' && (!discountValueShip || isNaN(discountValueShip) || Number(discountValueShip) < 1)) {
                    showFieldError($discountValueShip, 'Discount Value for Free Shipping is required and must be at least 1 unless type is FREE.');
                    return false;
                }
            }
            // Validity & Usage
            var $validFrom = $('input[name="valid_from"]');
            var $validTo = $('input[name="valid_to"]');
            var validFrom = $validFrom.val();
            var validTo = $validTo.val();
            if (validFrom && !/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(validFrom)) {
                showFieldError($validFrom, 'Valid From date must be in format yyyy-mm-dd hh:mm:ss.');
                return false;
            }
            if (validTo && !/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(validTo)) {
                showFieldError($validTo, 'Valid To date must be in format yyyy-mm-dd hh:mm:ss.');
                return false;
            }
            var $perCustomerLimit = $('input[name="per_customer_limit"]');
            var perCustomerLimit = $perCustomerLimit.val();
            if (perCustomerLimit && (isNaN(perCustomerLimit) || Number(perCustomerLimit) < 1)) {
                showFieldError($perCustomerLimit, 'Per Customer Limit must be at least 1.');
                return false;
            }
            var $maxUsageLimit = $('input[name="max_usage_limit"]');
            var maxUsageLimit = $maxUsageLimit.val();
            if (maxUsageLimit && (isNaN(maxUsageLimit) || Number(maxUsageLimit) < 1)) {
                showFieldError($maxUsageLimit, 'Maximum Usage Limit must be at least 1.');
                return false;
            }
            // Customer Groups
            var groupType = $('#customer_groups_type').val();
            if (groupType === 'customers_list') {
                var $customersList = $('select[name="customers_list[]"]');
                var customersList = $customersList.val();
                if (!customersList || customersList.length === 0) {
                    showFieldError($customersList, 'At least one customer must be selected for Customers List.');
                    return false;
                }
            }
            if (groupType === 'customers_group_list') {
                var $customersGroupList = $('select[name="customers_group_list[]"]');
                var customersGroupList = $customersGroupList.val();
                if (!customersGroupList || customersGroupList.length === 0) {
                    showFieldError($customersGroupList, 'At least one customer group must be selected for Customers Groups List.');
                    return false;
                }
            }
            // Last Order Value
            var $lastOrderValue = $('input[name="last_order_value"]');
            if ($('#customer_order_type').val() === 'on_last_order') {
                var lastOrderValue = $lastOrderValue.val();
                if (!lastOrderValue || isNaN(lastOrderValue) || Number(lastOrderValue) < 1) {
                    showFieldError($lastOrderValue, 'Last Order Value is required and must be at least 1.');
                    return false;
                }
            }
            // Filter rows (if filter section is visible)
            if (!$('input[name="is_apply_for_all"]').is(':checked')) {
                var filterValid = true;
                var $firstInvalidFilter = null;
                $('#filter-rows .filter-row:visible').each(function () {
                    var $type = $(this).find('.filter-type-select');
                    var type = $type.val();
                    var $select = null;
                    var values = [];
                    if (type === 'categories') {
                        $select = $(this).find('select[name="categories"]');
                        values = $select.val() || [];
                    } else if (type === 'brand') {
                        $select = $(this).find('select[name="brand"]');
                        values = $select.val() || [];
                    } else if (type === 'products') {
                        $select = $(this).find('select[name="products"]');
                        values = $select.val() || [];
                    }
                    if ((!values || values.length === 0) && $select) {
                        showFieldError($select, 'At least one value must be selected.');
                        if (!$firstInvalidFilter) $firstInvalidFilter = $select;
                        filterValid = false;
                    }
                });
                if (!filterValid) {
                    if ($firstInvalidFilter) $firstInvalidFilter.focus();
                    return false;
                }
            }
            // All validations passed
            return true;
        }
        // --- Payload Construction ---
        function createPayload() {
            var filter = {};
            var isApplyForAll = $('input[name="is_apply_for_all"]').is(':checked');
            if (!isApplyForAll) {
                $('#filter-rows .filter-row').each(function () {
                    var type = $(this).find('.filter-type-select').val();
                    var isIn = $(this).find('input[name="is_filter_in"]').is(':checked');
                    var values = [];
                    if (type === 'categories') {
                        values = $(this).find('select[name="categories"]').val() || [];
                        if (values.length > 0) {
                            if (isIn) {
                                if (!filter.categories) filter.categories = { opration: 'in', ids: [] };
                                values.forEach(function (value) {
                                    var intValue = parseInt(value);
                                    if (filter.categories.ids.indexOf(intValue) === -1) {
                                        filter.categories.ids.push(intValue);
                                    }
                                });
                            } else {
                                if (!filter.not_categories) filter.not_categories = { opration: 'in', ids: [] };
                                values.forEach(function (value) {
                                    var intValue = parseInt(value);
                                    if (filter.not_categories.ids.indexOf(intValue) === -1) {
                                        filter.not_categories.ids.push(intValue);
                                    }
                                });
                            }
                        }
                    } else if (type === 'brand') {
                        values = $(this).find('select[name="brand"]').val() || [];
                        if (values.length > 0) {
                            if (isIn) {
                                if (!filter.brand) filter.brand = { opration: 'in', ids: [] };
                                values.forEach(function (value) {
                                    var intValue = parseInt(value);
                                    if (filter.brand.ids.indexOf(intValue) === -1) {
                                        filter.brand.ids.push(intValue);
                                    }
                                });
                            } else {
                                if (!filter.not_brand) filter.not_brand = { opration: 'in', ids: [] };
                                values.forEach(function (value) {
                                    var intValue = parseInt(value);
                                    if (filter.not_brand.ids.indexOf(intValue) === -1) {
                                        filter.not_brand.ids.push(intValue);
                                    }
                                });
                            }
                        }
                    } else if (type === 'products') {
                        values = $(this).find('select[name="products"]').val() || [];
                        if (values.length > 0) {
                            if (isIn) {
                                if (!filter.product_ids) filter.product_ids = { opration: 'in', ids: [] };
                                values.forEach(function (value) {
                                    var intValue = parseInt(value);
                                    if (filter.product_ids.ids.indexOf(intValue) === -1) {
                                        filter.product_ids.ids.push(intValue);
                                    }
                                });
                            } else {
                                if (!filter.not_product_ids) filter.not_product_ids = { opration: 'in', ids: [] };
                                values.forEach(function (value) {
                                    var intValue = parseInt(value);
                                    if (filter.not_product_ids.ids.indexOf(intValue) === -1) {
                                        filter.not_product_ids.ids.push(intValue);
                                    }
                                });
                            }
                        }
                    }
                });
            } else {
                filter = null;
            }
            var groupType = $('#customer_groups_type').val();
            var applyOn = 'all';
            var values = null;
            if (groupType === 'customers_group_list') {
                applyOn = 'customer-group';
                values = $('select[name="customers_group_list[]"]').val() || null;
            } else if (groupType === 'customers_list') {
                applyOn = 'customer-list';
                values = $('select[name="customers_list[]"]').val() || null;
            }
            var orderType = $('select[name="customer_order_type"]').val();
            var onFirstOrder = null, onLastOrderValue = null, lastOrderValue = null;
            if (orderType === 'all_orders') {
                onFirstOrder = false;
                onLastOrderValue = false;
                lastOrderValue = null;
            } else if (orderType === 'first_order') {
                onFirstOrder = true;
                onLastOrderValue = false;
                lastOrderValue = null;
            } else if (orderType === 'on_last_order') {
                onFirstOrder = null;
                onLastOrderValue = true;
                lastOrderValue = Number($('input[name="last_order_value"]').val());
            }
            var rulesOnCustomer = {
                applyOn: applyOn,
                opration: 'in',
                values: values,
                "on-first-order": onFirstOrder,
                "on-last-order-value": onLastOrderValue,
                "last-order-value": lastOrderValue
            };
            var custom_meta_data = {
                buy_quantity: 0,
                is_recursive: false,
                get_y_products: [],
            }
            var rulesOnCartData = {
                "minOrderValue": null,
                "maxDiscountAmount": null
            }
            var ruleType = $('select[name="rule_type"]').val();
            var payload = {
                couponName: $('input[name="rule_name"]').val(),
                discount_lable: $('input[name="lable_name"]').val(),
                couponCode: $('#coupon_code_based').is(':checked') ? $('input[name="coupon_code"]').val() : null,
                applyDate: $('input[name="valid_from"]').val(),
                endDate: $('input[name="valid_to"]').val(),
                isDisabled: !$('input[name="is_active"]').is(':checked'),
                rulesOnCustomer: rulesOnCustomer,
                filter: filter,
                discountType: ruleType,
                description: $('textarea[name="description"]').val(),
                per_customer_limit: $('input[name="per_customer_limit"]').val(),
                useLimit: $('input[name="max_usage_limit"]').val(),
                discountValue: 0,
                discount: "free",
                minBuyQty: null,
                maxBuyQty: null,
            };
            if (ruleType === 'productAdjustment') {
                payload.discountValue = Number($('input[name="discount_value"]').val());
                payload.discount = $('select[name="discount_type"]').val() === 'Percentage Discount' ? 'percentageDiscount' : 'fixedDiscount';
                payload.minBuyQty = $('input[name="minimum_quantity"]').val() || null;
                payload.maxBuyQty = $('input[name="maximum_quantity"]').val() || null;
                payload.custom_meta = null;
                payload.rulesOnCart = null;
            }
            if (ruleType === 'buyXgetY') {
                custom_meta_data.buy_quantity = Number($('input[name="buy_quantity"]').val()) || 0;
                custom_meta_data.is_recursive = $('input[name="is_recursive"]').is(':checked');
                var getYProducts = [];
                $('.bogo-product-row').each(function () {
                    var selected = $(this).find('select[name="bogo_products[]"]').val();
                    var quantity = Number($(this).find('input[name="bogo_quantity[]"]').val()) || 1;
                    if (selected) {
                        if (selected.includes('-')) {
                            var parts = selected.split('-');
                            getYProducts.push({
                                product_id: parseInt(parts[0]),
                                variation_id: parts[1] ? parseInt(parts[1]) : null,
                                quantity: quantity
                            });
                        } else {
                            getYProducts.push({
                                product_id: parseInt(selected),
                                variation_id: null,
                                quantity: quantity
                            });
                        }
                    }
                });
                custom_meta_data.get_y_products = getYProducts;
                payload.custom_meta = custom_meta_data;
                payload.rulesOnCart = null;
            }
            if (ruleType === 'cartAdjustment') {
                rulesOnCartData.minOrderValue = Number($('input[name="min_order_value_cart_adjustment"]').val());
                rulesOnCartData.maxDiscountAmount = Number($('input[name="max_discount_amonut_cart_adjustment"]').val());
                payload.discountValue = Number($('input[name="discount_value_cart_adjustment"]').val());
                payload.discount = $('select[name="discount_type_cart_adjustment"]').val()=== 'Percentage Discount' ? 'percentageDiscount' : 'fixedDiscount';
                payload.rulesOnCart = rulesOnCartData;
                payload.custom_meta = null;
            }
            if (ruleType === 'freeShipping') {
                rulesOnCartData.minOrderValue = Number($('input[name="min_order_value_shipping"]').val());         
                // Only include max discount amount if the field is visible and has a value
                var maxDiscountValue = $('input[name="max_discount_amonut_shipping"]').val();
                if (maxDiscountValue && !isNaN(maxDiscountValue)) {
                    rulesOnCartData.maxDiscountAmount = Number(maxDiscountValue);
                } else {
                    rulesOnCartData.maxDiscountAmount = null;
                }
                // Only include discount value if the field is visible and has a value
                var discountValue = $('input[name="discount_value_shipping"]').val();
                if (discountValue && !isNaN(discountValue)) {
                    payload.discountValue = Number(discountValue);
                } else {
                    payload.discountValue = 0;
                }
                
                var discountType = $('select[name="discount_type_shipping"]').val();
                payload.discount = discountType === 'Percentage Discount' ? 'percentageDiscount' : 'fixedDiscount';
                payload.rulesOnCart = rulesOnCartData;
                payload.custom_meta = null;
            }
            let brand_id = $('select[name="brand_id"]').val() || null;
            if(brand_id != null){   
            if(brand_id.includes('all')){
                payload.brand_id = ['all'];
            }else{
                payload.brand_id = brand_id;
            }
            }
            payload.location_id = $('select[name="business_location"]').val() || null;
            payload.is_referal_program_discount = $('input[name="is_referal_program_discount"]').is(':checked');

            return payload;
        }
        // --- Form Submission Handler ---
        $('#edit_offer_form').on('click', function (e) {
            e.preventDefault();
            if (!validateForm()) {
                return false;
            }
            if (typeof tinymce !== 'undefined') {
                tinymce.triggerSave();
            }
            let payload = createPayload();
            $('button[type="submit"]').prop('disabled', true).text('Updating...').addClass('loading');
            let discount_id=$('.discount_id').val();
            $.ajax({
                url: `/custom-discounts/${discount_id}`,
                method: 'PUT',
                data: JSON.stringify(payload),
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('button[type="submit"]').prop('disabled', false).text('Update').removeClass('loading');
                    swal({
                        title: "Success!",
                        text: "Offer updated successfully!",
                        icon: "success",
                        button: "OK",
                    }).then((value) => {
                        window.location.href = '/custom-discounts';
                    });
                },
                error: function (xhr) {
                    var errorMessage = 'Error updating offer. Please try again.';
                    if (xhr.status === 422) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.errors) {
                                var errorMessages = [];
                                for (var field in response.errors) {
                                    if (response.errors.hasOwnProperty(field)) {
                                        errorMessages.push(field + ': ' + response.errors[field].join(', '));
                                    }
                                }
                                errorMessage = 'Validation errors:\n' + errorMessages.join('\n');
                            } else if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            errorMessage = 'Server validation error. Please check the console for details.';
                        }
                    }
                    $('button[type="submit"]').prop('disabled', false).text('Update');
                    $('button[type="submit"]').removeClass('loading');
                    swal({
                        title: "Error!",
                        text: errorMessage,
                        icon: "error",
                        button: "OK",
                    });
                }
            });
        });
        $('.select_location_id').on('change', function () {
            $.ajax({
                url: '/business-location/' + $(this).val(),
                type: 'GET',
                data: { location_id: $(this).val() },
                success: function (response) {
                    console.log(response);
                    if (response.is_b2c == 1) {
                        $('.brand_select_div').removeClass('hide');
                        $('.brand_select').empty();
                        $('.brand_select').append('<select name="brand_id" class="form-control select brand_select_form_admin">');
                        $('.brand_select_form_admin').append('<option value="all">All Brands</option>');
                        response.brands.forEach(function (brand) {
                            $('.brand_select_form_admin').append('<option value="' + brand.id + '">' + brand.name + '</option>');
                        });
                        $('.brand_select_form_admin').append('</select>');
                        $('.brand_select_form_admin').select2({
                            width: '100%',
                            multiple: true,
                        });
                    } else {
                        $('.brand_select_div').addClass('hide');
                        $('.brand_select').empty();
                    }
                    
                    // Clear and refresh customer lists when location changes
                    $('select[name="customers_list[]"]').val(null).trigger('change');
                    $('select[name="customers_group_list[]"]').val(null).trigger('change');
                }
            });
        });
        const $discountTypeCart = $('select[name="discount_type_cart_adjustment"]');
        const $discountValueCart = $('input[name="discount_value_cart_adjustment"]');
        const $discountTypeProduct = $('select[name="discount_type"]');
        const $discountValueProduct = $('input[name="discount_value"]');

        function togglePercentMax($typeSelect, $valueInput) {
            if ($typeSelect.val() === 'Percentage Discount') {
                $valueInput.attr('max', 100);
            } else {
                $valueInput.removeAttr('max');
            }
        }

        $discountTypeCart.on('change', function () {
            togglePercentMax($discountTypeCart, $discountValueCart);
        });
        togglePercentMax($discountTypeCart, $discountValueCart);

        $discountTypeProduct.on('change', function () {
            togglePercentMax($discountTypeProduct, $discountValueProduct);
        });
        togglePercentMax($discountTypeProduct, $discountValueProduct);
    });
    </script>
@endsection
