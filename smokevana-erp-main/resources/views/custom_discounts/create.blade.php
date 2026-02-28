@extends('layouts.app')

@section('title', __('lang_v1.offer.create_new_offer'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
    <style>
        /* ========== Create Offer Page – Amazon theme ========== */
        .offer-create-page {
            background: #EAEDED;
            min-height: 100%;
            padding-bottom: 2rem;
        }
        .offer-create-page .content-header {
            background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
            border: 1px solid #4a5d6e;
            border-radius: 10px;
            padding: 24px 32px !important;
            margin-bottom: 20px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }
        .offer-create-page .content-header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff9900, #e47911);
            opacity: 0.9;
        }
        .offer-create-page .content-header h1 {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: #fff !important;
            margin: 0 !important;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .offer-create-page .content-header h1 small {
            display: block;
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #b8c4ce !important;
            margin-top: 4px;
        }

        /* Cards – Amazon style */
        .offer-create-page .box-primary,
        .offer-create-page .tw-mb-4 {
            background: #fff !important;
            border: 1px solid #D5D9D9 !important;
            border-radius: 10px !important;
            box-shadow: 0 2px 5px rgba(15, 17, 17, 0.08) !important;
            overflow: hidden;
        }
        .offer-create-page .box-header {
            background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
            color: #fff !important;
            padding: 14px 20px !important;
            border-bottom: 2px solid #ff9900 !important;
        }
        .offer-create-page .box-title {
            color: #fff !important;
            font-weight: 600 !important;
        }
        .offer-create-page .box-header .fa,
        .offer-create-page .box-header i {
            color: #FF9900 !important;
            margin-right: 8px;
        }

        /* Form controls */
        .offer-create-page .form-control {
            border: 1px solid #D5D9D9;
        }
        .offer-create-page .form-control:focus {
            border-color: #FF9900;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
        }
        .offer-create-page .select2-container--default .select2-selection {
            border-color: #D5D9D9;
        }
        .offer-create-page .select2-container--default.select2-container--focus .select2-selection {
            border-color: #FF9900;
            box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
        }
        .offer-create-page input[type="checkbox"] { accent-color: #FF9900; }

        /* Toggle switches – Amazon orange */
        .offer-create-page .switch .slider { transition: background-color 0.3s ease, transform 0.3s ease; }
        .offer-create-page .switch .slider span { transition: right 0.3s ease, transform 0.3s ease; }
        .offer-create-page .switch input:checked+.slider {
            background-color: #FF9900 !important;
            transform: scale(1.05);
        }
        .offer-create-page .switch input:not(:checked)+.slider {
            background-color: #ccc !important;
            transform: scale(1);
        }
        .offer-create-page .switch input:checked+.slider span { right: 2px !important; transform: scale(1.1); }
        .offer-create-page .switch input:not(:checked)+.slider span { right: 22px !important; transform: scale(1); }
        .offer-create-page .switch:hover .slider { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2); }
        .offer-create-page .switch input:checked+.slider:hover { background-color: #E47911 !important; }
        .offer-create-page .switch input:not(:checked)+.slider:hover { background-color: #bbb !important; }

        /* Create button */
        .offer-create-page .content-header .btn-primary {
            background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
            border: 1px solid #a88734 !important;
            color: #0f1111 !important;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 18px;
        }

        /* Alert styles */
        .offer-create-page .alert { margin-bottom: 1rem; }
        .offer-create-page .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .offer-create-page .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .offer-create-page .loading { opacity: 0.7; cursor: not-allowed !important; }
        .offer-create-page button[type="submit"]:disabled { cursor: not-allowed; opacity: 0.7; }
        .offer-create-page .input-error { color: #d9534f; font-size: 0.95em; margin-top: 2px; }
        .offer-create-page .input-invalid,
        .offer-create-page .select2-container--default .select2-selection--single.input-invalid,
        .offer-create-page .select2-container--default .select2-selection--multiple.input-invalid {
            border: 1.5px solid #d9534f !important;
            box-shadow: 0 0 2px #d9534f;
        }

        /* Action buttons */
        .offer-create-page .add-filter-row,
        .offer-create-page .add-bogo-row { color: #0066c0 !important; }
        .offer-create-page .add-filter-row:hover,
        .offer-create-page .add-bogo-row:hover { color: #C45500 !important; }
        .offer-create-page .btn-success { background: #067d62 !important; border-color: #056952 !important; color: #fff !important; }
    </style>
@endsection

@section('content')
<div class="admin-amazon-page offer-create-page">
    {{-- Offer Creation Form --}}
    {!! Form::open([
    'url' => '/offers/store',
    'method' => 'post',
    'id' => 'create_offer_form',
]) !!}
    <!-- Content Header (Page header) -->
    <section class="content-header tw-flex tw-justify-between tw-flex-wrap tw-gap-4 tw-mt-2 tw-mb-2">
        <div>
            <h1><i class="fa fa-tags" aria-hidden="true"></i> Create New Offer</h1>
            <small>Configure your promotional offers with flexible settings</small>
        </div>
        <div class="text-right">
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </section>

    <!-- ================= Basic Information Section ================= -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Basic Information', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
        <div class="row">
            <div class="col-md-3">
                {{-- Rule Name Input --}}
                {!! Form::label('rule_name', 'Web Discount Rule') !!}
                {!! Form::text('rule_name', null, ['class' => 'form-control', 'placeholder' => 'eg. Winter Sale', 'required', 'minlength' => '3', 'maxlength' => '100']) !!}
            </div>
            <div class="col-md-3">
                {{-- Rule Name Input --}}
                {!! Form::label('lable_name', 'Lable Name ') !!}
                @show_tooltip('This discount label name visibility to Ecom') 
                {!! Form::text('lable_name', null, ['class' => 'form-control', 'placeholder' => 'eg. Winter Sale',  'minlength' => '3', 'maxlength' => '100']) !!}
            </div>
            <div class="col-md-3">
                {{-- Rule Type Dropdown --}}
                {!! Form::label('rule_type', 'Rule Type *') !!}
                {!! Form::select('rule_type', ['productAdjustment' => 'Product Adjustment', 'buyXgetY' => 'Buy X Get Y', 'cartAdjustment' => 'Cart Adjustment', 'freeShipping' => 'Free Shipping'], null, ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="col-md-3">
                {{-- Coupon Code Based Toggle and Input --}}
                <div class="d-flex align-items-center">
                    {!! Form::checkbox('coupon_code_based', 1, false, ['id' => 'coupon_code_based']) !!}
                    {!! Form::label('coupon_code_based', 'Coupon Code Based', ['style' => 'margin-left:5px;']) !!}
                </div>
                {!! Form::text('coupon_code', null, ['class' => 'form-control', 'placeholder' => 'eg : ADNEW100', 'disabled' => true, 'pattern' => '[A-Za-z0-9_-]+', 'minlength' => '3', 'maxlength' => '50']) !!}
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <div>
                    {{-- Rule Enable Toggle --}}
                    {!! Form::label('is_active', 'Rule is Enable') !!}<br>
                    <label class="switch">
                        {!! Form::checkbox('is_active', 1, true, ['class' => 'form-control', 'style' => 'display:none']) !!}
                        <span class="slider round"
                            style="display:inline-block;width:40px;height:20px;background:#FF9900;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                            <span
                                style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                        </span>
                    </label>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div>
                    {{-- Apply for all Products Toggle --}}
                    {!! Form::label('is_apply_for_all', 'Apply for all Products') !!}<br>
                    <label class="switch">
                        {!! Form::checkbox('is_apply_for_all', 1, true, ['class' => 'form-control', 'style' => 'display:none']) !!}
                        <span class="slider round"
                            style="display:inline-block;width:40px;height:20px;background:#FF9900;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                            <span
                                style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                        </span>
                    </label>
                </div>
            </div>

            @if( session('business')->enable_referal_program)
            <div class="col-md-2 d-flex align-items-end">
                <div>
                    {!! Form::label('is_referal_program_discount', 'Is Referal Program Discount') !!}<br>
                    <label class="switch">
                        {!! Form::checkbox('is_referal_program_discount', 1, false, ['class' => 'form-control', 'style' => 'display:none']) !!}
                    </label>
                </div>
            </div>
            @endif
        </div>
        @endcomponent

        <!-- ================= Filter Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Filter', 'title_svg' => '<i class="fa fa-filter"></i>'])
        <div id="filter-rows">
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
                                style="display:inline-block;width:40px;height:20px;background:#FF9900;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
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
        </div>
        @endcomponent

        <!-- ================= Discount Configuration Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => ' Discount Configuration', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
        <div class="row">
            <div class="col-md-4">
                {{-- Discount Type Dropdown --}}
                {!! Form::label('discount_type', 'Discount Type') !!}
                {!! Form::select('discount_type', ['Fixed Discount' => 'Fixed Discount','Percentage Discount' => 'Percentage Discount'], null, ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="col-md-4">
                {{-- Discount Value Input --}}
                {!! Form::label('discount_value', 'Discount Value') !!}
                {!! Form::number('discount_value', null, ['class' => 'form-control', 'placeholder' => 'e.g.10', 'min' => '1', 'step' => '1']) !!}
            </div>
            <div class="col-md-2">
                {{-- Minimum Quantity Input --}}
                {!! Form::label('minimum_quantity', 'Minimum Quantity') !!}
                {!! Form::number('minimum_quantity', null, ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '1', 'step' => '1']) !!}
            </div>
            <div class="col-md-2">
                {{-- Maximum Quantity Input --}}
                {!! Form::label('maximum_quantity', 'Maximum Quantity') !!}
                {!! Form::number('maximum_quantity', null, ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '1', 'step' => '1']) !!}
            </div>
        </div>
        @endcomponent

        <!-- ================= Buy X Get Y Configuration Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Buy X Get Y Configuration', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
        <div class="row">
            <div class="col-md-3">
                {{-- Buy Quantity Input --}}
                {!! Form::label('buy_quantity', 'Minimum Quantity') !!}
                {!! Form::number('buy_quantity', null, ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '1', 'step' => '1']) !!}
            </div>
            <div class="col-md-1 d-flex ">
                <div>
                    {{-- Is Recursive Toggle --}}
                    {!! Form::label('is_recursive', 'Is Recursive') !!}<br>
                    <label class="switch">
                        {!! Form::checkbox('is_recursive', 1, true, ['class' => 'form-control', 'style' => 'display:none']) !!}
                        <span class="slider round"
                            style="display:inline-block;width:40px;height:20px;background:#FF9900;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                            <span
                                style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
        <div class="bogo_products">
            <div class="row bogo-product-row">
                <div class="col-md-6 bogo-products-group">
                    {{-- BOGO Product & Variation Select --}}
                    {!! Form::label('bogo_products[]', 'Select Product & Variation') !!}
                    {!! Form::select('bogo_products[]', [], null, ['class' => 'form-control select2 bogo-single-select', 'data-type' => 'product_variations']) !!}
                </div>
                <div class="col-md-2">
                    {{-- BOGO Quantity Input --}}
                    {!! Form::label('bogo_quantity[]', 'Quantity') !!}
                    {!! Form::number('bogo_quantity[]', null, ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '1', 'step' => '1']) !!}
                </div>
                <div class="col-md-1" style="display:flex; flex-direction:column">
                    <label>&nbsp;</label>
                    {{-- Remove BOGO Row Icon --}}
                    <i class="fa fa-trash remove-bogo-row" style="display:none; color: red;"></i>
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    {{-- Add BOGO Row Button --}}
                    <button type="button" class="btn btn-success btn-sm add-bogo-row"><i class="fa fa-plus"></i> Add
                        Product</button>
                </div>
            </div>
        </div>
        @endcomponent

        <!-- ================= Cart Adjustment Configuration Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Cart Adjustment Configuration', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
        <div class="row">
            <div class="col-md-2">
                {{-- Minimum Order Value Input --}}
                {!! Form::label('min_order_value_cart_adjustment', 'Minimum Order Value') !!}
                {!! Form::number('min_order_value_cart_adjustment', null, ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '.01', 'step' => '.01']) !!}
            </div>
            <div class="col-md-2">
                {{-- Maximum Discount Amount Input --}}
                {!! Form::label('max_discount_amount_cart_adjustment', 'Maximum discount Amount') !!}
                {!! Form::number('max_discount_amonut_cart_adjustment', null, ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '.01', 'step' => '.01']) !!}
            </div>
            <div class="col-md-4">
                {{-- Discount Type Dropdown --}}
                {!! Form::label('discount_type_cart_adjustment', 'Discount Type') !!}
                {!! Form::select('discount_type_cart_adjustment', ['Fixed Discount' => 'Fixed Discount','Percentage Discount' => 'Percentage Discount'], null, ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="col-md-2">
                {{-- Discount Value Input --}}
                {!! Form::label('discount_value_cart_adjustment', 'Discount Value') !!}
                {!! Form::number('discount_value_cart_adjustment', null, ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '.01', 'step' => '.01']) !!}
            </div>
        </div>
        @endcomponent

        <!-- ================= Free Shipping Configuration Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Free Shipping Configuration', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
        <div class="row">
            <div class="col-md-2">
                {{-- Minimum Order Value Input --}}
                {!! Form::label('min_order_value_shipping', 'Minimum Order Value') !!}
                {!! Form::number('min_order_value_shipping', null, ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '.01', 'step' => '.01']) !!}
            </div>
            <div class="col-md-2" hidden>
                {{-- Maximum Discount Amount Input --}}
                {!! Form::label('max_discount_amount_shipping', 'Maximum discount Amount') !!}
                {!! Form::number('max_discount_amonut_shipping', null, ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '.01', 'step' => '.01']) !!}
            </div>
            <div class="col-md-4">
                {{-- Discount Type Dropdown (includes FREE) --}}
                {!! Form::label('discount_type_shipping', 'Discount Type') !!}
                {{-- {!! Form::select('discount_type_shipping', ['free' => "FREE", 'Percentage Discount' => 'Percentage Discount', 'Fixed Discount' => 'Fixed Discount'], null, ['class' => 'form-control', 'required']) !!} --}}
                {!! Form::select('discount_type_shipping', ['free' => "FREE"], null, ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="col-md-4" hidden>
                {{-- Discount Value Input --}}
                {!! Form::label('discount_value_shipping', 'Discount Value') !!}
                {!! Form::number('discount_value_shipping', null, ['class' => 'form-control', 'placeholder' => 'e.g.10', 'min' => '1', 'step' => '1']) !!}
            </div>
        </div>
        @endcomponent

        <!-- ================= Validity & Usage Limits Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Validity & Usage limits', 'style' => 'z-index:999;position:relative;', 'title_svg' => '<i class="fa fa-clock"></i>'])
        <div class="row">
            <div class="col-md-4">
                {{-- Valid From Input --}}
                {!! Form::label('valid_from', 'Rule Valid From') !!}
                {!! Form::text('valid_from', null, ['class' => 'form-control', 'placeholder' => 'dd-mm-yyy --:-- --', 'pattern' => '\\d{2}-\\d{2}-\\d{4} \\d{2}:\\d{2}']) !!}
            </div>
            <div class="col-md-4">
                {{-- Valid To Input --}}
                {!! Form::label('valid_to', 'Rule Valid To') !!}
                {!! Form::text('valid_to', null, ['class' => 'form-control', 'placeholder' => 'dd-mm-yyy --:-- --', 'pattern' => '\\d{2}-\\d{2}-\\d{4} \\d{2}:\\d{2}']) !!}
            </div>
            <div class="col-md-2" hidden>
                {{-- Per Customer Limit Input --}}
                {!! Form::label('per_customer_limit', 'Per Customer Limit') !!}
                {!! Form::number('per_customer_limit', null, ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '1', 'step' => '1']) !!}
            </div>
            <div class="col-md-2" hidden>
                {{-- Maximum Usage Limit Input --}}
                {!! Form::label('max_usage_limit', 'Maximum Usage Limit') !!}
                {!! Form::number('max_usage_limit', null, ['class' => 'form-control', 'placeholder' => 'e.g. 1000', 'min' => '1', 'step' => '1']) !!}
            </div>
        </div>
        @endcomponent

        <!-- ================= Description & Customer Groups Section ================= -->
        <div class="row">
            <div class="col-md-6">
                @component('components.widget', ['class' => 'box-primary', 'title' => 'Description', 'title_svg' => '<i class="fa fa-file"></i>'])
                <div class="form-group">
                    {{-- Description Textarea --}}
                    {!! Form::textarea('description', null, ['class' => 'form-control description']) !!}
                </div>
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.widget', ['class' => 'box-primary', 'title' => 'Customer Groups', 'title_svg' => '<i class="fa fa-users"></i>'])
                @if (!empty($is_b2c))
                <div class="form-group">
                    {{-- Customer Groups Type Dropdown --}}
                    {!! Form::select('customer_groups_type', ['all_customers' => 'All Customers', 'customers_list' => 'Customers List'], null, ['class' => 'form-control', 'id' => 'customer_groups_type']) !!}
                </div>
                @else
                <div class="form-group">
                    {{-- Customer Groups Type Dropdown --}}
                    {!! Form::select('customer_groups_type', ['all_customers' => 'All Customers', 'customers_list' => 'Customers List', 'customers_group_list' => 'Customers Groups List'], null, ['class' => 'form-control', 'id' => 'customer_groups_type']) !!}
                </div>
                @endif
                <div id="customers_list_box">
                    <div class="row">
                        <div class="col-md-11">
                            {{-- Customers List Multi-select --}}
                            {!! Form::label('customers_list[]', 'Customers list') !!}
                            {!! Form::select('customers_list[]', [], null, ['class' => 'form-control select2 multi-search', 'multiple' => 'multiple', 'data-type' => 'customer']) !!}
                        </div>
                        <div class="col-md-1 d-flex ">
                            <div>
                                {{-- Is In Toggle for Customers List --}}
                                {!! Form::label('is_filter_in', 'Is In') !!}<br>
                                <label class="switch">
                                    {!! Form::checkbox('is_filter_in', 1, true, ['class' => 'form-control', 'style' => 'display:none']) !!}
                                    <span class="slider round"
                                        style="display:inline-block;width:40px;height:20px;background:#FF9900;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                                        <span
                                            style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="customers_group_list_box">
                    <div class="row">
                        <div class="col-md-11">
                            {{-- Customers Groups Multi-select --}}
                            {!! Form::label('customers_group_list[]', 'Customers Groups') !!}
                            {!! Form::select('customers_group_list[]', [], null, ['class' => 'form-control select2 multi-search', 'multiple' => 'multiple', 'data-type' => 'customers_group']) !!}
                        </div>
                        <div class="col-md-1 d-flex ">
                            <div>
                                {{-- Is In Toggle for Customers Groups --}}
                                {!! Form::label('is_filter_in', 'Is In') !!}<br>
                                <label class="switch">
                                    {!! Form::checkbox('is_filter_in', 1, true, ['class' => 'form-control', 'style' => 'display:none']) !!}
                                    <span class="slider round"
                                        style="display:inline-block;width:40px;height:20px;background:#FF9900;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                                        <span
                                            style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group tw-mt-3">
                    {{-- Customer Order Type Dropdown --}}
                    {!! Form::label('customer_order_type', 'Order Value Type') !!}
                    {!! Form::select('customer_order_type', ['all_orders' => 'All Orders', 'first_order' => 'On First Order', 'on_last_order' => 'On Last Order Value'], null, ['class' => 'form-control', 'id' => 'customer_order_type']) !!}
                    <div class="last_order_value_div tw-mt-3" style="display:none;">
                        {{-- Last Order Value Input (shown conditionally) --}}
                        {!! Form::label('last_order_value', 'Last Order Value') !!}
                        {!! Form::number('last_order_value', null, ['class' => 'form-control', 'min' => '1', 'step' => '1']) !!}
                    </div>
                </div>
                @endcomponent
                @if(auth()->user()->can('access_all_locations') || auth()->user()->can('admin') || !empty($is_b2c))
                @component('components.widget', ['class' => 'box-primary', 'title' => 'Business Locations', 'title_svg' => '<i class="fa fa-file"></i>'])
                @if(auth()->user()->can('access_all_locations') || auth()->user()->can('admin'))
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! Form::select('business_location', $business_locations, null, ['class' => 'form-control select2 select_location_id', 'placeholder' => __('messages.please_select') ,'required']) !!}
                        </div>
                    </div>
                @endif
                @if(!empty($is_b2c))
                    @if(!auth()->user()->can('access_all_locations') || !auth()->user()->can('admin'))
                        <div class="brand_select_div_for_non customer_fields">
                            <div class="form-group">
                                {!! Form::label('brand_id', 'Select Brand' . ':') !!}
                                {!! Form::select('brand_id', $brands, ['all'], ['class' => 'form-control select2', "required", "multiple" ,'style' => 'width: 100%;']) !!}
                            </div>
                        </div>
                    @endif
                @endif
                
                {{-- Brand Selection for Admin Users (Hidden by default) --}}
                @if(auth()->user()->can('access_all_locations') || auth()->user()->can('admin'))
                    <div class="brand_select_form_admin hide customer_fields">
                        <div class="form-group">
                            {!! Form::label('brand_id', 'Select Brand' . ':') !!}
                            {!! Form::select('brand_id', $brands, ['all'], ['class' => 'form-control select2 brand_id_select_form_admin', "multiple" ,'style' => 'width: 100%;']) !!}
                        </div>
                    </div>
                @endif
                @endcomponent
                @endif
            </div>
        </div>
    </section>
    {!! Form::close() !!}
</div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
        // --- Toggle Switch Functionality (Amazon orange) ---
        $('input[name="is_active"]').on('change', function () {
            const toggle = $(this).next('span');
            const thumb = toggle.find('span');
            if ($(this).is(':checked')) {
                toggle.css('background-color', '#FF9900');
                thumb.css('right', '2px');
            } else {
                toggle.css('background-color', '#ccc');
                thumb.css('right', '22px');
            }
        });

        // Toggle for filter inclusion/exclusion
        $(document).on('change', 'input[name="is_filter_in"]', function () {
            const toggle = $(this).next('span');
            const thumb = toggle.find('span');
            if ($(this).is(':checked')) {
                toggle.css('background-color', '#FF9900');
                thumb.css('right', '2px');
            } else {
                toggle.css('background-color', '#ccc');
                thumb.css('right', '22px');
            }
        });

        // --- Initialize TinyMCE for Description Field ---
        if ($('textarea.description').length > 0) {
            tinymce.init({
                selector: 'textarea.description',
                height: 250
            });
        }

        // --- Filter Row Visibility Logic ---
        function updateFilterRowVisibility($row) {
            var type = $row.find('.filter-type-select').val();
            $row.find('.filter-categories-group').toggleClass('hide', type !== 'categories');
            $row.find('.filter-brand-group').toggleClass('hide', type !== 'brand');
            $row.find('.filter-products-group').toggleClass('hide', type !== 'products');
        }

        // Initial update for all filter rows
        $('#filter-rows .filter-row').each(function () {
            updateFilterRowVisibility($(this));
        });

        // Update filter row on filter type change
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
                        <label>{{ $is_b2c ? 'Choose (Categories/Product)' : 'Choose (Categories/Brand/Product)' }}</label>
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
                                <span class="slider round" style="display:inline-block;width:40px;height:20px;background:#FF9900;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
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

            // Initialize select2 for new selects
            $newRow.find('.multi-search').select2({
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
                            type: $(this).data('type')
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data.result, function (item) {
                                if (item.name && item.id) {
                                    return { id: item.id, text: item.name };
                                } else if (item.sku && item.name) {
                                    return { id: item.id, text: item.name + ' (' + item.sku + ')' };
                                }
                                return { id: item.id, text: item.name || item.sku || item.id };
                            })
                        };
                    },
                    cache: true
                }
            });

            // Append the new row
            $('#filter-rows').append($newRow);

            // Hide add button in all but the last row
            $('#filter-rows .add-filter-row').hide();
            $('#filter-rows .add-filter-row:last').show();

            // Show only the categories group by default
            $newRow.find('.filter-categories-group').removeClass('hide');
            $newRow.find('.filter-brand-group, .filter-products-group').addClass('hide');
        });

        // --- Delete Filter Row ---
        $(document).on('click', '.delete-filter-row', function () {
            $(this).closest('.filter-row').remove();
            // Always show add button in the last row
            $('#filter-rows .add-filter-row').hide();
            $('#filter-rows .add-filter-row:last').show();
        });

        // --- Datepicker Initialization ---
        $('input[name="valid_from"], input[name="valid_to"]').datetimepicker({
            format: 'DD-MM-YYYY HH:mm',
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
        updateCustomerGroupSelect(); // Initial call

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
                    return {
                        results: $.map(data.result, function (item) {
                            if (item.name && item.id) {
                                return { id: item.id, text: item.name };
                            } else if (item.sku && item.name) {
                                return { id: item.id, text: item.name + ' (' + item.sku + ')' };
                            }
                            return { id: item.id, text: item.name || item.sku || item.id };
                        })
                    };
                },
                cache: true
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

            // if (ruleType === 'buyXgetY' || ruleType === 'freeShipping') {
            //     $('input[name="per_customer_limit"]').closest('.col-md-2').hide();
            //     $('input[name="max_usage_limit"]').closest('.col-md-2').hide();
            // } else {
            //     $('input[name="per_customer_limit"]').closest('.col-md-2').show();
            //     $('input[name="max_usage_limit"]').closest('.col-md-2').show();
            // }

            if (ruleType === 'buyXgetY') {
                // Hide Discount Configuration and Validity & Usage limits for buyXgetY
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Discount Configuration') {
                        $(this).hide();
                    }
                });

                // Show Buy X Get Y Configuration
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Buy X Get Y Configuration') {
                        $(this).show();
                    }
                });

                // Hide other configuration sections
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Cart Adjustment Configuration' || title === 'Free Shipping Configuration') {
                        $(this).hide();
                    }
                });
            } else if (ruleType === 'cartAdjustment') {
                // Hide Discount Configuration and Validity & Usage limits for cartAdjustment
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Discount Configuration') {
                        $(this).hide();
                    }
                });

                // Show Cart Adjustment Configuration
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Cart Adjustment Configuration') {
                        $(this).show();
                    }
                });

                // Hide other configuration sections
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Buy X Get Y Configuration' || title === 'Free Shipping Configuration') {
                        $(this).hide();
                    }
                });
            } else if (ruleType === 'freeShipping') {
                // Hide Discount Configuration and Validity & Usage limits for freeShipping
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Discount Configuration') {
                        $(this).hide();
                    }
                });

                // Show Free Shipping Configuration
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Free Shipping Configuration') {
                        $(this).show();
                    }
                });

                // Hide other configuration sections
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Buy X Get Y Configuration' || title === 'Cart Adjustment Configuration') {
                        $(this).hide();
                    }
                });
            } else {
                // Show Discount Configuration and Validity & Usage limits for other rule types (productAdjustment)
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Discount Configuration') {
                        $(this).show();
                    }
                });

                // Hide all other configuration sections
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Buy X Get Y Configuration' || title === 'Cart Adjustment Configuration' || title === 'Free Shipping Configuration') {
                        $(this).hide();
                    }
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
                // Hide filter section when "Apply for all Products" is enabled
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Filter') {
                        $(this).hide();
                    }
                });
            } else {
                // Show filter section when "Apply for all Products" is disabled
                $('.box-primary').each(function () {
                    var title = $(this).find('.box-title').text().trim();
                    if (title === 'Filter') {
                        $(this).show();
                    }
                });
            }
        }
        toggleFilterSection();
        $('input[name="is_apply_for_all"]').on('change', function () {
            toggleFilterSection();
            // Update toggle switch visual state
            const toggle = $(this).next('span');
            const thumb = toggle.find('span');
            if ($(this).is(':checked')) {
                toggle.css('background-color', '#FF9900');
                thumb.css('right', '2px');
            } else {
                toggle.css('background-color', '#ccc');
                thumb.css('right', '22px');
            }
        });

        // --- BOGO Products Functionality ---
        function updateBogoRowButtons() {
            var $rows = $('.bogo-product-row');
            if ($rows.length > 1) {
                $rows.find('.remove-bogo-row').show();
            } else {
                $rows.find('.remove-bogo-row').hide();
            }
            $rows.find('.add-bogo-row').hide();
            $rows.last().find('.add-bogo-row').show();
        }

        // Add BOGO product row
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

            // Initialize select2 for new select (BOGO single select)
            $newRow.find('.bogo-single-select').select2({
                placeholder: 'Search product & variation...',
                minimumInputLength: 1,
                width: '100%',
                ajax: {
                    url: '/multi-select/search',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            s: params.term,
                            type: 'product_variations'
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data.result, function (item) {
                                if (item.product_id) {
                                    return {
                                        id: item.variation_id ? (item.product_id + '-' + item.variation_id) : (item.product_id + ''),
                                        text: (item.product_name || '') + (item.variation_name ? ' - ' + item.variation_name : '')
                                    };
                                }
                                return null;
                            }).filter(Boolean)
                        };
                    },
                    cache: true
                }
            });

            // Append the new row
            $('.bogo_products').append($newRow);

            // Update button visibility
            updateBogoRowButtons();
        });

        // Remove BOGO product row
        $(document).on('click', '.remove-bogo-row', function () {
            $(this).closest('.bogo-product-row').remove();
            updateBogoRowButtons();
        });
        updateBogoRowButtons();

        // Initialize Select2 for BOGO single selects
        $('.bogo-single-select').select2({
            placeholder: 'Search product & variation...',
            minimumInputLength: 1,
            width: '100%',
            ajax: {
                url: '/multi-select/search',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        s: params.term,
                        type: 'product_variations'
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.result, function (item) {
                            if (item.product_id) {
                                return {
                                    id: item.variation_id ? (item.product_id + '-' + item.variation_id) : (item.product_id + ''),
                                    text: (item.product_name || '') + (item.variation_name ? ' - ' + item.variation_name : '')
                                };
                            }
                            return null;
                        }).filter(Boolean)
                    };
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
                //if (!maxDiscountShip || isNaN(maxDiscountShip) || Number(maxDiscountShip) < 1) {
                if ($maxDiscountShip.is(':visible') && (!maxDiscountShip || isNaN(maxDiscountShip) || Number(maxDiscountShip) < 1)) {
                    showFieldError($maxDiscountShip, 'Maximum Discount Amount for Free Shipping is required and must be at least 1.');
                    return false;
                }
              // Only validate max discount amount if field is visible
                //if (!maxDiscountShip || isNaN(maxDiscountShip) || Number(maxDiscountShip) < 1) {
                if ($maxDiscountShip.is(':visible') && (!maxDiscountShip || isNaN(maxDiscountShip) || Number(maxDiscountShip) < 1)) {
                    showFieldError($maxDiscountShip, 'Maximum Discount Amount for Free Shipping is required and must be at least 1.');
                    return false;
                }
                // if (!discountTypeShip) {
                //     showFieldError($discountTypeShip, 'Discount Type for Free Shipping is required.');
                //     return false;
                // }
                
                // Only validate discount value if field is visible and type is not 'free'
                //if (discountTypeShip !== 'free' && (!discountValueShip || isNaN(discountValueShip) || Number(discountValueShip) < 1)) {
                if ($discountValueShip.is(':visible') && discountTypeShip && discountTypeShip !== 'free' && (!discountValueShip || isNaN(discountValueShip) || Number(discountValueShip) < 1)) {
                    showFieldError($discountValueShip, 'Discount Value for Free Shipping is required and must be at least 1 unless type is FREE.');
                    return false;
                }
            }
            // Validity & Usage
            var $validFrom = $('input[name="valid_from"]');
            var $validTo = $('input[name="valid_to"]');
            var validFrom = $validFrom.val();
            var validTo = $validTo.val();
            if (validFrom && !/^\d{2}-\d{2}-\d{4} \d{2}:\d{2}$/.test(validFrom)) {
                showFieldError($validFrom, 'Valid From date must be in format dd-mm-yyyy hh:mm.');
                return false;
            }
            if (validTo && !/^\d{2}-\d{2}-\d{4} \d{2}:\d{2}$/.test(validTo)) {
                showFieldError($validTo, 'Valid To date must be in format dd-mm-yyyy hh:mm.');
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

        // --- Form Submission Handler ---
        $('#create_offer_form').on('submit', function (e) {
            e.preventDefault();
            if (!validateForm()) {
                return false;
            }
            let payload = createPayload();
            // Disable the create button and show loading state
            
            $('button[type="submit"]').prop('disabled', true).text('Creating...').addClass('loading');
            $.ajax({
                url: '/custom-discounts',
                method: 'POST',
                data: JSON.stringify(payload),
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('button[type="submit"]').prop('disabled', false).text('Create').removeClass('loading');
                    swal({
                        title: "Success!",
                        text: "Offer created successfully!",
                        icon: "success",
                        button: "OK",
                    }).then((value) => {
                        window.location.href = '/custom-discounts';
                    });
                },
                error: function (xhr) {
                    var errorMessage = 'Error creating offer. Please try again.';
                    // Log the full response for debugging
                    console.log('Status:', xhr.status);
                    console.log('Response Text:', xhr.responseText);
                    console.log('Response Headers:', xhr.getAllResponseHeaders());

                    // Handle 422 validation errors
                    if (xhr.status === 422) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            console.log('Parsed Response:', response);

                            if (response.errors) {
                                // Build error message from validation errors
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
                            console.error('Error parsing response:', e);
                            errorMessage = 'Server validation error. Please check the console for details.';
                        }
                    }
                    // Re-enable the create button after error
                    $('button[type="submit"]').prop('disabled', false).text('Create');
                    // Clear any loading states
                    $('button[type="submit"]').removeClass('loading');
                    swal({
                        title: "Error!",
                        text: errorMessage,
                        icon: "error",
                        button: "OK",
                    });
                    console.error('Error:', xhr);
                }
            });
        });

        // --- Payload Construction ---
        function createPayload() {
            // filter paload
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
                                // Add only unique values
                                values.forEach(function (value) {
                                    var intValue = parseInt(value);
                                    if (filter.categories.ids.indexOf(intValue) === -1) {
                                        filter.categories.ids.push(intValue);
                                    }
                                });
                            } else {
                                if (!filter.not_categories) filter.not_categories = { opration: 'in', ids: [] };
                                // Add only unique values
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
                                // Add only unique values
                                values.forEach(function (value) {
                                    var intValue = parseInt(value);
                                    if (filter.brand.ids.indexOf(intValue) === -1) {
                                        filter.brand.ids.push(intValue);
                                    }
                                });
                            } else {
                                if (!filter.not_brand) filter.not_brand = { opration: 'in', ids: [] };
                                // Add only unique values
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
                                // Add only unique values
                                values.forEach(function (value) {
                                    var intValue = parseInt(value);
                                    if (filter.product_ids.ids.indexOf(intValue) === -1) {
                                        filter.product_ids.ids.push(intValue);
                                    }
                                });
                            } else {
                                if (!filter.not_product_ids) filter.not_product_ids = { opration: 'in', ids: [] };
                                // Add only unique values
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

            // Build rulesOnCustomer object as per requirements
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

            // custom_meta Payload
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

            // Add rule type specific payload data
            if (ruleType === 'productAdjustment') {
                payload.discountValue = Number($('input[name="discount_value"]').val());
                payload.discount = $('select[name="discount_type"]').val() === 'Percentage Discount' ? 'percentageDiscount' : 'fixedDiscount';
                payload.minBuyQty = $('input[name="minimum_quantity"]').val() || null;
                payload.maxBuyQty = $('input[name="maximum_quantity"]').val() || null;
            }

            if (ruleType === 'buyXgetY') {
                // BOGO specific data
                custom_meta_data.buy_quantity = Number($('input[name="buy_quantity"]').val()) || 0;
                custom_meta_data.is_recursive = $('input[name="is_recursive"]').is(':checked');

                // Collect BOGO products from all rows
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
            }
            if (ruleType === 'cartAdjustment') {
                //Add Cart Adjustment specific data
                rulesOnCartData.minOrderValue = Number($('input[name="min_order_value_cart_adjustment"]').val());
                rulesOnCartData.maxDiscountAmount = Number($('input[name="max_discount_amonut_cart_adjustment"]').val());
                payload.discountValue = Number($('input[name="discount_value_cart_adjustment"]').val());
                payload.discount = $('select[name="discount_type_cart_adjustment"]').val()=== 'Percentage Discount' ? 'percentageDiscount' : 'fixedDiscount';

                payload.rulesOnCart = rulesOnCartData;
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


            return payload
        }
            $('.select_location_id').on('change', function () {
                $.ajax({
                    url: '/business-location/' + $(this).val(),
                    type: 'GET',
                    data: { location_id: $(this).val() },
                    success: function (response) {
                        console.log(response);
                        if (response.is_b2c == 1) {
                            $('.brand_select_form_admin').removeClass('hide');
                            $('.brand_id_select_form_admin').empty();
                            $('.brand_id_select_form_admin').append('<option value="all">All Brands</option>');
                            response.brands.forEach(function (brand) {
                                $('.brand_id_select_form_admin').append('<option value="' + brand.id + '">' + brand.name + '</option>');
                            });
                            // Select "All Brands" by default
                            $('.brand_id_select_form_admin').val('all').trigger('change');
                        } else {
                            $('.brand_select_form_admin').addClass('hide');
                            $('.brand_id_select_form_admin').empty();
                            $('.brand_id_select_form_admin').append('<option value="all">All Brands</option>');
                        }
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