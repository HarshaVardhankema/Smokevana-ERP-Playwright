@extends('layouts.app')
@section('title', 'Product Order Limit Rules')

@section('content')

<!-- Amazon-style banner -->
<section class="content-header">
    <div style="background:#37475a;border-radius:6px;padding:22px 28px;margin-bottom:16px;box-shadow:0 3px 10px rgba(15,17,17,0.4);">
        <h1 style="display:flex;align-items:center;gap:10px;font-size:22px;font-weight:700;margin:0;color:#fff;"><i class="fas fa-list-ol" style="color:#fff!important;"></i> Product Order Limit Rules</h1>
        <p style="font-size:13px;color:rgba(249,250,251,0.88);margin:4px 0 0 0;">Set sale and order limits for products and variants.</p>
    </div>
</section>

<style>
    .calendar-icon {
    position: absolute;
    right: 25px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
    pointer-events: none;
    }
    .sale-limit-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
    }
    .sale-limit-card:hover {
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    #limitRulesTable thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #e5e7eb;
        font-weight: 600;
        color: #374151;
        padding: 12px 8px;
        font-size: 13px;
    }
    #limitRulesTable tbody td {
        padding: 10px 8px;
        vertical-align: middle;
    }
    #limitRulesTable tbody tr:hover {
        background-color: #f9fafb;
    }
    /* Amazon-style filter section card */
    .filter-section {
        background: #ffffff;
        border-radius: 10px;
        padding: 0;
        margin-bottom: 20px;
        border: 1px solid #D5D9D9;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    
    /* Filter section header */
    .filter-section-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
        padding: 14px 20px;
        border-bottom: 3px solid #FF9900;
        border-left: 4px solid #FF9900;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .filter-section-header h5 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .filter-section-header h5 i {
        color: #FF9900;
        font-size: 16px;
    }
    
    /* Filter section body */
    .filter-section-body {
        padding: 20px;
    }
    
    /* Amazon-style form inputs */
    .filter-section .form-control,
    .filter-section .select2-container--default .select2-selection--single {
        background-color: #F7F8F8;
        border: 1px solid #D5D9D9;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 14px;
        color: #0F1111;
        transition: all 0.2s ease;
        height: 38px;
    }
    
    .filter-section .form-control:focus,
    .filter-section .select2-container--default.select2-container--focus .select2-selection--single {
        background-color: #ffffff;
        border-color: #0066C0;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 102, 192, 0.15);
    }
    
    .filter-section .form-control:hover,
    .filter-section .select2-container--default .select2-selection--single:hover {
        background-color: #ffffff;
        border-color: #B8BDBD;
    }
    
    .filter-section label {
        font-weight: 600;
        font-size: 13px;
        color: #0F1111;
        margin-bottom: 6px;
        display: block;
    }
    
    /* Select2 dropdown styling */
    .filter-section .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #0F1111;
    }
    
    .filter-section .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 8px;
    }
    
    .filter-section .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #565959 transparent transparent transparent;
    }
    
    .filter-section .select2-container--default.select2-container--focus .select2-selection--single .select2-selection__arrow b {
        border-color: #0066C0 transparent transparent transparent;
    }
    
    /* Form group spacing */
    .filter-section .form-group {
        margin-bottom: 0;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .filter-section-body {
            padding: 16px;
        }
        
        .filter-section .col-md-3 {
            margin-bottom: 16px;
        }
        
        .filter-section .col-md-3:last-child {
            margin-bottom: 0;
        }
        
        .filter-section .tw-flex {
            justify-content: flex-start !important;
        }
    }
    
    /* Amazon-style filter buttons - smaller size */
    .amazon-filter-btn {
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 70px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        height: 32px;
    }
    
    /* Apply button - Amazon orange */
    .amazon-filter-btn-primary {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        color: #ffffff !important;
        border: 1px solid #E47911 !important;
    }
    
    .amazon-filter-btn-primary:hover {
        background: linear-gradient(to bottom, #E47911 0%, #D2691E 100%) !important;
        color: #ffffff !important;
        border-color: #D2691E !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(255, 153, 0, 0.4);
    }
    
    .amazon-filter-btn-primary:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(255, 153, 0, 0.3);
    }
    
    .amazon-filter-btn-primary:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.3);
    }
    
    /* Clear button - Amazon secondary style */
    .amazon-filter-btn-secondary {
        background: #ffffff !important;
        color: #232f3e !important;
        border: 2px solid #D5D9D9 !important;
    }
    
    .amazon-filter-btn-secondary:hover {
        background: #F7F8F8 !important;
        color: #0F1111 !important;
        border-color: #FF9900 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .amazon-filter-btn-secondary:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .amazon-filter-btn-secondary:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(35, 47, 62, 0.2);
    }
    
    /* Ensure icons are visible */
    .amazon-filter-btn i {
        color: inherit !important;
        opacity: 1 !important;
    }
    
    /* Amazon Modal Styles */
    .amazon-modal-content {
        border: none !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15) !important;
    }
    
    .amazon-modal-header {
        background: #37475A !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
        padding: 18px 24px !important;
    }
    
    .amazon-modal-header .modal-title {
        color: #ffffff !important;
        font-weight: 700 !important;
    }
    
    .amazon-modal-close {
        color: #ffffff !important;
        opacity: 0.9 !important;
        text-shadow: none !important;
    }
    
    .amazon-modal-close:hover {
        opacity: 1 !important;
        color: #FF9900 !important;
    }
    
    .amazon-modal-body {
        background: linear-gradient(180deg, #fff 0%, #F7F8F8 100%) !important;
    }
    
    /* Amazon Form Input Styles */
    .amazon-form-input {
        background-color: #F7F8F8 !important;
        border: 1px solid #D5D9D9 !important;
        border-radius: 8px !important;
        padding: 10px 14px !important;
        color: #0F1111 !important;
        transition: all 0.2s ease !important;
    }
    
    .amazon-form-input:hover {
        background-color: #ffffff !important;
        border-color: #B8BDBD !important;
    }
    
    .amazon-form-input:focus {
        background-color: #ffffff !important;
        border-color: #0066C0 !important;
        outline: 0 !important;
        box-shadow: 0 0 0 3px rgba(0, 102, 192, 0.15) !important;
    }
    
    /* Amazon Button Styles */
    .amazon-btn-primary {
        padding: 10px 20px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
        border: 1px solid #E47911 !important;
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        color: #ffffff !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        min-width: 120px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
    }
    
    .amazon-btn-primary:hover {
        background: linear-gradient(to bottom, #E47911 0%, #D2691E 100%) !important;
        border-color: #D2691E !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 8px rgba(255, 153, 0, 0.3) !important;
    }
    
    .amazon-btn-primary:active {
        transform: translateY(0) !important;
        box-shadow: 0 2px 4px rgba(255, 153, 0, 0.2) !important;
    }
    
    .amazon-btn-primary:focus {
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.3) !important;
    }
    
    .amazon-btn-primary i {
        color: #ffffff !important;
        opacity: 1 !important;
    }
    
    .amazon-btn-secondary {
        padding: 10px 20px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
        border: 2px solid #D5D9D9 !important;
        background: #ffffff !important;
        color: #0F1111 !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        min-width: 100px !important;
    }
    
    .amazon-btn-secondary:hover {
        background: #F7F8F8 !important;
        color: #0F1111 !important;
        border-color: #FF9900 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
    }
    
    .amazon-btn-secondary:active {
        transform: translateY(0) !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }
    
    .amazon-btn-secondary:focus {
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(35, 47, 62, 0.2) !important;
    }
    
    .amazon-modal-footer {
        background: #37475A !important;
        border-top: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
        padding: 16px 24px !important;
        display: flex !important;
        justify-content: flex-end !important;
        gap: 12px !important;
    }
    
    /* Amazon-style form inputs in modal */
    #addLimitModal .form-control,
    #editLimitModal .form-control {
        background-color: #ffffff !important;
        border: 1px solid #D5D9D9 !important;
        color: #0F1111 !important;
        height: 42px !important;
        line-height: 22px !important;
        padding: 10px 14px !important;
        font-size: 14px !important;
    }
    
    #addLimitModal .form-control:hover,
    #editLimitModal .form-control:hover {
        background-color: #ffffff !important;
        border-color: #B8BDBD !important;
    }
    
    #addLimitModal .form-control:focus,
    #editLimitModal .form-control:focus {
        background-color: #ffffff !important;
        border-color: #0066C0 !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(0, 102, 192, 0.15) !important;
    }
    
    /* Fix select dropdown text clipping */
    #addLimitModal select.form-control,
    #editLimitModal select.form-control {
        height: 42px !important;
        line-height: 22px !important;
        padding: 10px 14px !important;
        padding-right: 32px !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23565959' d='M6 9L1 4h10z'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        background-color: #ffffff !important;
    }
    
    #addLimitModal select.form-control option,
    #editLimitModal select.form-control option {
        padding: 8px 14px !important;
        line-height: 22px !important;
    }
    
    /* Select2 dropdowns in modal */
    #addLimitModal .select2-container--default .select2-selection--single,
    #editLimitModal .select2-container--default .select2-selection--single {
        background-color: #ffffff !important;
        border: 1px solid #D5D9D9 !important;
        height: 42px !important;
    }
    
    #addLimitModal .select2-container--default .select2-selection--single .select2-selection__rendered,
    #editLimitModal .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 14px !important;
        padding-right: 32px !important;
        color: #0F1111 !important;
        font-size: 14px !important;
    }
    
    #addLimitModal .select2-container--default .select2-selection--single .select2-selection__arrow,
    #editLimitModal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
        right: 8px !important;
    }
    
    #addLimitModal .select2-container--default .select2-selection--single:hover,
    #editLimitModal .select2-container--default .select2-selection--single:hover {
        border-color: #B8BDBD !important;
    }
    
    #addLimitModal .select2-container--default.select2-container--focus .select2-selection--single,
    #editLimitModal .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #0066C0 !important;
        box-shadow: 0 0 0 3px rgba(0, 102, 192, 0.15) !important;
    }
    
    /* Ensure modal body has proper Amazon styling */
    #addLimitModal .modal-body,
    #editLimitModal .modal-body {
        background: #ffffff !important;
        padding: 24px !important;
    }
    
    /* Amazon-style labels */
    #addLimitModal label,
    #editLimitModal label {
        font-weight: 600 !important;
        font-size: 14px !important;
        color: #0F1111 !important;
        margin-bottom: 8px !important;
    }
    
    /* Form group spacing */
    #addLimitModal .form-group,
    #editLimitModal .form-group {
        margin-bottom: 20px !important;
    }
    
    /* Date inputs */
    #addLimitModal input[type="datetime-local"],
    #editLimitModal input[type="datetime-local"] {
        height: 42px !important;
        line-height: 22px !important;
    }
</style>
    
<section class="content">
    <div class="row">
        <div class="col-md-12">
            
            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-section-header">
                    <h5>
                        <i class="fas fa-filter"></i>
                        Filter Options
                    </h5>
                </div>
                <div class="filter-section-body">
                    <div class="row tw-items-end">
                        <div class="col-md-3">
                            <div class="form-group tw-mb-0">
                                <label>Status</label>
                                <select class="form-control select2" id="statusFilter" style="width: 100%;">
                                    <option value="all">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group tw-mb-0">
                                <label>Search</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Search products...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group tw-mb-0">
                                <label>Product</label>
                                <select class="form-control select2" id="productFilter" style="width: 100%;">
                                    <option value="">All Products</option>
                                    @foreach($productsForDropdown ?? [] as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="tw-flex tw-gap-2" style="justify-content: flex-end;">
                                <button type="button" class="amazon-filter-btn amazon-filter-btn-primary" id="applyFilters">
                                    <i class="fa fa-filter" style="margin-right: 4px;"></i> Apply
                                </button>
                                <button type="button" class="amazon-filter-btn amazon-filter-btn-secondary" id="clearFilters">
                                    <i class="fa fa-times" style="margin-right: 4px;"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->

            <div class="box box-solid">
                <div class="box-body">
                    <h4 class="box-title">Order Limit Rules</h4>

                    <div class="tw-mt-3 tw-mb-3 tw-text-right">
                        <button type="button"
                            class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white tw-border-none tw-rounded-full btn-modal"
                            style="background:linear-gradient(to bottom,#FF9900 0%,#E47911 100%)!important;border:1px solid #C7511F!important;"
                            data-toggle="modal" data-target="#addLimitModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg> Add New Rule
                        </button>
                    </div>

                    <div class="box-body">

                    <table class="table   table-bordered table-striped" style="width: 100%;" id="limitRulesTable">
                        <thead>
                            <tr>
                                    <th title="Click to sort by creation date (Latest/Oldest)">Created</th>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>Sale Limit</th>
                                <th>Order Limit</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    </div>
</section>

<!-- Add/Edit Modal -->
<div class="modal fade" id="addLimitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content amazon-modal-content" style="border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
            <div class="modal-header amazon-modal-header" style="background: #37475A; border-radius: 12px 12px 0 0; border-bottom: 1px solid rgba(255, 255, 255, 0.15); padding: 18px 24px;">
                <h4 class="modal-title" style="color: #ffffff; font-weight: 700; font-size: 18px; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-plus-circle" style="color: #FF9900;"></i> Add Order Limit Rule
                </h4>
                <button type="button" class="close amazon-modal-close" data-dismiss="modal" style="color: #ffffff; opacity: 0.9; font-size: 28px; font-weight: 300; text-shadow: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="limitRuleForm">
                <div class="modal-body amazon-modal-body" style="padding: 24px; background: #ffffff;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; font-size: 14px; color: #0F1111; margin-bottom: 8px;">Product/Variant</label>
                                <input type="text" name="search_product"
                                    class="form-control amazon-form-input mousetrap ui-autocomplete-input" id="search_product"
                                    placeholder="Search product or variant..." autocomplete="off" style="background-color: #ffffff; border: 1px solid #D5D9D9; border-radius: 8px; padding: 10px 14px; transition: all 0.2s ease; height: 42px; line-height: 22px;">
                                <input type="hidden" name="product_id" id="product_id">
                                <input type="hidden" name="variant_id" id="variant_id">

                                <!-- Search Fields Checkboxes -->
                                <div style="margin-top: 16px; padding: 16px; background: #FFF4E5; border: 2px solid #FF9900; border-radius: 8px;">
                                    <label style="display: block; font-weight: 600; font-size: 14px; color: #0F1111; margin-bottom: 12px;">Search Fields:</label>
                                    <div style="display: flex; flex-wrap: wrap; gap: 16px;">
                                        <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #0F1111; cursor: pointer;">
                                            <input type="checkbox" name="search_fields[]" value="sku" checked style="width: 18px; height: 18px; cursor: pointer; accent-color: #FF9900;"> SKU
                                        </label>
                                        <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #0F1111; cursor: pointer;">
                                            <input type="checkbox" name="search_fields[]" value="name" checked style="width: 18px; height: 18px; cursor: pointer; accent-color: #FF9900;"> Name
                                        </label>
                                        <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #0F1111; cursor: pointer;">
                                            <input type="checkbox" name="search_fields[]" value="sub_sku" style="width: 18px; height: 18px; cursor: pointer; accent-color: #FF9900;"> Sub SKU
                                        </label>
                                        <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #0F1111; cursor: pointer;">
                                            <input type="checkbox" name="search_fields[]" value="var_barcode_no" style="width: 18px; height: 18px; cursor: pointer; accent-color: #FF9900;"> Barcode
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; font-size: 14px; color: #0F1111; margin-bottom: 8px;">Sale Limit</label>
                                <input type="number" class="form-control amazon-form-input" name="sale_limit" id="sale_limit" required
                                    placeholder="Max sale allowed" style="background-color: #ffffff; border: 1px solid #D5D9D9; border-radius: 8px; padding: 10px 14px; transition: all 0.2s ease; height: 42px; line-height: 22px;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; font-size: 14px; color: #0F1111; margin-bottom: 8px;">Order Limit</label>
                                <input type="number" class="form-control amazon-form-input" name="order_limit" id="order_limit"
                                    placeholder="Max orders allowed" style="background-color: #ffffff; border: 1px solid #D5D9D9; border-radius: 8px; padding: 10px 14px; transition: all 0.2s ease; height: 42px; line-height: 22px;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; font-size: 14px; color: #0F1111; margin-bottom: 8px;">Status</label>
                                <input type="hidden" name="product_id" id="edit-variant-product-id">
                                <select class="form-control amazon-form-input" name="is_active" id="is_active" style="background-color: #ffffff; border: 1px solid #D5D9D9; border-radius: 8px; padding: 10px 14px; padding-right: 32px; transition: all 0.2s ease; height: 42px; line-height: 22px; -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' viewBox=\'0 0 12 12\'%3E%3Cpath fill=\'%23565959\' d=\'M6 9L1 4h10z\'/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 12px center;">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 16px;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; font-size: 14px; color: #0F1111; margin-bottom: 8px;">Start Date</label>
                                <input type="datetime-local" class="form-control amazon-form-input" name="start_date" id="start_date" style="background-color: #ffffff; border: 1px solid #D5D9D9; border-radius: 8px; padding: 10px 14px; transition: all 0.2s ease; height: 42px; line-height: 22px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; font-size: 14px; color: #0F1111; margin-bottom: 8px;">End Date</label>
                                <input type="datetime-local" class="form-control amazon-form-input" name="end_date" id="end_date" style="background-color: #ffffff; border: 1px solid #D5D9D9; border-radius: 8px; padding: 10px 14px; transition: all 0.2s ease; height: 42px; line-height: 22px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer amazon-modal-footer" style="background: #37475A; color: #ffffff; border-radius: 0 0 12px 12px; border-top: 1px solid rgba(255, 255, 255, 0.15); padding: 16px 24px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn amazon-btn-secondary" data-dismiss="modal" style="padding: 6px 16px; font-size: 13px; font-weight: 600; border-radius: 6px; border: 1px solid #D5D9D9; background: #ffffff; color: #0F1111; transition: all 0.2s ease; cursor: pointer; min-width: 70px; height: 32px;">
                        Close
                    </button>
                    <button type="submit" class="btn amazon-btn-primary" style="padding: 6px 16px; font-size: 13px; font-weight: 600; border-radius: 6px; border: 1px solid #E47911; background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%); color: #ffffff; transition: all 0.2s ease; cursor: pointer; min-width: 90px; height: 32px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fa fa-save"></i> Save Rule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editLimitModal" tabindex="-1" role="dialog" style="z-index: 1070 !important;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content amazon-modal-content" style="border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
            <div class="modal-header amazon-modal-header" style="background: #37475A; border-radius: 12px 12px 0 0; border-bottom: 1px solid rgba(255, 255, 255, 0.15); padding: 18px 24px;">
                <h4 class="modal-title" style="color: #ffffff; font-weight: 700; font-size: 18px; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-edit" style="color: #FF9900;"></i> Edit Order Limit Rule
                </h4>
                <button type="button" class="close amazon-modal-close" data-dismiss="modal" style="color: #ffffff; opacity: 0.9; font-size: 28px; font-weight: 300; text-shadow: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editLimitRuleForm">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body amazon-modal-body" style="padding: 24px; background: #ffffff;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; font-size: 14px; color: #0F1111; margin-bottom: 8px;">Product/Variant</label>
                                <input type="text" name="search_product" class="form-control amazon-form-input" id="edit_search_product"
                                    readonly style="background: #F7F8F8; border: 1px solid #D5D9D9; border-radius: 8px; padding: 10px 14px; color: #6b7280; height: 42px; line-height: 22px;">
                                <input type="hidden" name="product_id" id="edit_product_id">
                                <input type="hidden" name="variant_id" id="edit_variant_id">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; font-size: 14px; color: #0F1111; margin-bottom: 8px;">Sale Limit</label>
                                <input type="number" class="form-control amazon-form-input" name="sale_limit" id="edit_sale_limit" min="0"
                                    placeholder="Max sales allowed" readonly style="background: #F7F8F8; border: 1px solid #D5D9D9; border-radius: 8px; padding: 10px 14px; color: #6b7280; height: 42px; line-height: 22px;">
                                <small style="font-size: 12px; color: #6b7280; margin-top: 4px; display: block;">Sale limit cannot be modified</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; font-size: 14px; color: #0F1111; margin-bottom: 8px;">Order Limit</label>
                                <input type="number" class="form-control amazon-form-input" name="order_limit" id="edit_order_limit"
                                    min="0" placeholder="Max orders allowed" style="background-color: #ffffff; border: 1px solid #D5D9D9; border-radius: 8px; padding: 10px 14px; transition: all 0.2s ease; height: 42px; line-height: 22px;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; font-size: 14px; color: #0F1111; margin-bottom: 8px;">Status</label>
                                <select class="form-control amazon-form-input" name="is_active" id="edit_is_active" style="background-color: #ffffff; border: 1px solid #D5D9D9; border-radius: 8px; padding: 10px 14px; padding-right: 32px; transition: all 0.2s ease; height: 42px; line-height: 22px; -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' viewBox=\'0 0 12 12\'%3E%3Cpath fill=\'%23565959\' d=\'M6 9L1 4h10z\'/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 12px center;">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 16px;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; font-size: 14px; color: #0F1111; margin-bottom: 8px;">Start Date</label>
                                <input type="datetime-local" class="form-control amazon-form-input" name="start_date"
                                    id="edit_start_date" style="background-color: #ffffff; border: 1px solid #D5D9D9; border-radius: 8px; padding: 10px 14px; transition: all 0.2s ease; height: 42px; line-height: 22px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; font-size: 14px; color: #0F1111; margin-bottom: 8px;">End Date</label>
                                <input type="datetime-local" class="form-control amazon-form-input" name="end_date" id="edit_end_date" style="background-color: #ffffff; border: 1px solid #D5D9D9; border-radius: 8px; padding: 10px 14px; transition: all 0.2s ease; height: 42px; line-height: 22px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer amazon-modal-footer" style="background: #37475A; color: #ffffff; border-radius: 0 0 12px 12px; border-top: 1px solid rgba(255, 255, 255, 0.15); padding: 16px 24px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn amazon-btn-secondary" data-dismiss="modal" style="padding: 6px 16px; font-size: 13px; font-weight: 600; border-radius: 6px; border: 1px solid #D5D9D9; background: #ffffff; color: #0F1111; transition: all 0.2s ease; cursor: pointer; min-width: 70px; height: 32px;">
                        Close
                    </button>
                    <button type="submit" class="btn amazon-btn-primary" style="padding: 6px 16px; font-size: 13px; font-weight: 600; border-radius: 6px; border: 1px solid #E47911; background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%); color: #ffffff; transition: all 0.2s ease; cursor: pointer; min-width: 90px; height: 32px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fa fa-save"></i> Update Rule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Consumer Details Modal -->
<div class="modal fade" id="consumerDetailsModal" tabindex="-1" role="dialog"
    aria-labelledby="consumerDetailsModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background: #37475A; color: #ffffff; border-radius: 12px 12px 0 0; border-bottom: 1px solid rgba(255, 255, 255, 0.15);">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title tw-font-semibold" id="consumerDetailsModalLabel">Consumer Details & Logs</h4>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="tw-text-base tw-font-semibold tw-text-gray-800 tw-mb-3">Product Information</h5>
                        <table class="table table-bordered" style="border-radius: 8px; overflow: hidden;">
                            <tr>
                                <td style="background: #f8f9fa; width: 30%;"><strong>Product:</strong></td>
                                <td id="modal-product-name">-</td>
                            </tr>
                            <tr>
                                <td style="background: #f8f9fa;"><strong>Variant:</strong></td>
                                <td id="modal-variant-name">-</td>
                            </tr>
                            {{-- <tr>
                                <td style="background: #f8f9fa;"><strong>Order Limit:</strong></td>
                                <td id="modal-order-limit">-</td>
                            </tr> --}}
                            <tr>
                                <td style="background: #f8f9fa;"><strong>Sale Limit:</strong></td>
                                <td id="modal-sale-limit">-</td>
                            </tr>
                            {{-- <tr>
                                <td style="background: #f8f9fa;"><strong>Status:</strong></td>
                                <td id="modal-status">-</td>
                            </tr> --}}
                        </table>
                    </div>

                    <!-- Variant Purchase Limits Section -->
                    <div class="col-md-12" id="variant-limits-section" style="display: none;">
                        <h5>Variant Purchase Limits</h5>
                        <div class="table-responsive" style="max-height:200px; overflow-y: auto;">
                            <table class="table table-bordered table-striped " id="variant-limits-table">
                                <thead>
                                    <tr>
                                        <th style="min-width: 120px;">Variant Name</th>
                                        <th style="min-width: 100px;">Purchase Limit</th>
                                        <th style="min-width: 100px;">Status</th>
                                        <th style="min-width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="variant-limits-table-body">
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <i class="fa fa-spinner fa-spin"></i> Loading variant information...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <h5>Rule Information</h5>
                        <div id="rules-container">
                            <div class="table-responsive" style="max-height:200px; overflow-y: auto;">
                                <table class="table table-bordered table-striped" id="rules-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 100px;">Order Limit</th>
                                            <th style="min-width: 120px;">Start Date</th>
                                            <th style="min-width: 120px;">End Date</th>
                                            <th style="min-width: 100px;">Status</th>
                                            <th style="min-width: 120px;">Created</th>
                                            <th style="min-width: 120px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rules-table-body">
                                        <!-- Rules will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h5>Consumer Logs</h5>
                        <div id="consumer-logs-container">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered table-striped " id="consumer-logs-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 140px;">Consumer Name</th>
                                            <th style="min-width: 120px;">Email</th>
                                            <th style="min-width: 120px;">Mobile</th>
                                            <th style="min-width: 100px;">Orders</th>
                                            <th style="min-width: 100px;">Quantity</th>
                                            <th style="min-width: 100px;">Blocked</th>
                                            <th style="min-width: 120px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="consumer-logs-table-body">
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <i class="fa fa-spinner fa-spin"></i> Loading consumer logs...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Rule for Existing Product Modal -->
<div class="modal fade" id="addRuleForProductModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Order Limit Rule</h4>
            </div>
            <form id="addRuleForProductForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Product/Variant</label>
                                <input type="text" name="search_product" class="form-control" id="fixed_search_product"
                                    readonly>
                                <input type="hidden" name="product_id" id="fixed_product_id">
                                <input type="hidden" name="variant_id" id="fixed_variant_id">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fixed_sale_limit">Sale Limit <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="sale_limit" id="fixed_sale_limit"
                                    min="1" required placeholder="Enter sale limit" autocomplete="off">
                                <small class="form-text text-muted">Maximum quantity per order</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Order Limit</label>
                                <input type="number" class="form-control" name="order_limit" id="fixed_order_limit"
                                    placeholder="Max orders allowed">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="is_active" id="fixed_is_active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="datetime-local" class="form-control" name="start_date"
                                    id="fixed_start_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="datetime-local" class="form-control" name="end_date" id="fixed_end_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Edit Sale Limit Modal -->
<div class="modal fade" id="editSaleLimitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Sale Limit</h4>
            </div>
            <form id="editSaleLimitForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Current Sale Limit</label>
                                <input type="text" class="form-control" id="edit-sale-limit-current" readonly
                                    style="background-color: #f8f9fa;">
                                <small class="form-text text-muted">This is the current maximum quantity that can be
                                    sold per order</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit-sale-limit-new">New Sale Limit <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit-sale-limit-new" name="sale_limit"
                                    min="1" required placeholder="Enter new sale limit" autocomplete="off">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="edit-sale-limit-product-id" name="product_id">
                    <input type="hidden" id="edit-sale-limit-variant-id" name="variant_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Update Sale Limit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Variant Purchase Limit Modal -->
<div class="modal fade" id="editVariantPurchaseLimitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Variant Purchase Limit</h4>
            </div>
            <form id="editVariantPurchaseLimitForm">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Variant</label>
                                <input type="text" class="form-control" id="edit-variant-name" readonly>
                                <input type="hidden" name="variant_id" id="edit-variant-id">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit-variant-purchase-limit">Purchase Limit <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="purchase_limit"
                                    id="edit-variant-purchase-limit" min="1" required placeholder="Enter purchase limit"
                                    autocomplete="off">
                                <small class="form-text text-muted">Maximum quantity that can be purchased per
                                    order</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Purchase Limit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('javascript')
    <!-- DataTables Buttons CSS and JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    
    {{-- <style>
    .form-control[readonly] {
        background-color: #f8f9fa;
        color: #6c757d;
        cursor: not-allowed;
    }

    .btn-xs {
        margin: 1px;
    }

    .modal-lg .form-group {
        margin-bottom: 15px;
    }

    /* Fix modal z-index stacking */
    #editLimitModal {
        z-index: 1070 !important;
    }

    #addLimitModal {
        z-index: 1070 !important;
    }

    #addRuleForProductModal {
        z-index: 1070 !important;
    }

    #editSaleLimitModal {
        z-index: 1070 !important;
    }



    #consumerDetailsModal {
        z-index: 1050 !important;
        background: transparent !important;
    }

    /* Make Consumer Details modal background transparent */
    #consumerDetailsModal .modal-content {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(5px);
    }

    /* Ensure proper backdrop handling */
    .modal-backdrop {
        z-index: 1040 !important;
    }

    /* Remove backdrop for Consumer Details modal */
    #consumerDetailsModal+.modal-backdrop {
        display: none !important;
    }

    /* Ensure background content is visible */
    body.modal-open {
        overflow: auto !important;
    }

    /* Make Consumer Details modal semi-transparent */
    #consumerDetailsModal .modal-dialog {
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    /* Ensure edit modals have proper backdrop */
    #editLimitModal .modal-content,
    #addLimitModal .modal-content,
    #addRuleForProductModal .modal-content,
    #editSaleLimitModal .modal-content {
        background: #fff !important;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
    }

    /* Make the page content visible behind Consumer Details modal */
    #consumerDetailsModal {
        background: rgba(0, 0, 0, 0.1) !important;
    }

    /* Ensure table rows are clickable even with modal open */
    #limitRulesTable tbody tr {
        cursor: pointer;
    }

    /* Add subtle border to Consumer Details modal */
    #consumerDetailsModal .modal-content {
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    /* Style readonly fields */
    .form-control[readonly] {
        background-color: #f8f9fa;
        color: #6c757d;
        cursor: not-allowed;
    }

    /* Special styling for read-only sale limit field */
    /* .form-control[readonly].sale-limit-readonly {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }
         */
    /* Enhanced table styling for rules and consumer logs */
    /* #rules-table, #consumer-logs-table {
            font-size: 12px;
            margin-bottom: 0;
            border: 1px solid #ddd;
        } */

    /* #rules-table thead, #consumer-logs-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
         */


    /* #rules-table td, #consumer-logs-table td {
            padding: 10px 8px;
            vertical-align: middle;
            border-color: #e9ecef;
            text-align: center;
        } */

    /* #rules-table tbody tr:hover, #consumer-logs-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        } */

    /* Action buttons styling */
    .btn-group-xs .btn {
        padding: 4px 8px;
        font-size: 10px;
        border-radius: 3px;
        margin: 0 1px;
        transition: all 0.2s ease;
    }

    .btn-group-xs .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* Individual button styling */
    .btn-xs.edit-rule {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-xs.delete-rule {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-xs.view-logs {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }

    .btn-xs:hover {
        color: white;
    }

    /* Table container styling */
    .table-responsive {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background: white;
    }

    /* Status label styling */
    .label {
        display: inline-block;
        padding: 2px 6px;
        font-size: 10px;
        font-weight: 600;
        border-radius: 3px;
        text-transform: uppercase;
    }

    .label-success {
        background-color: #28a745;
        color: white;
    }

    .label-danger {
        background-color: #dc3545;
        color: white;
    }

    .label-warning {
        background-color: #ffc107;
        color: #212529;
    }

            /* Variant limits table styling */
        
        /* Pagination and Show entries styling */
        .dataTables_wrapper .dataTables_length {
            float: left !important;
            margin-bottom: 15px !important;
        }
        
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            padding: 4px 8px !important;
            margin: 0 5px !important;
            background: white !important;
        }
        
        .dataTables_wrapper .dataTables_info {
            float: left !important;
            margin-top: 15px !important;
            margin-bottom: 15px !important;
            color: #666 !important;
            font-size: 13px !important;
        }
        
        .dataTables_wrapper .dataTables_paginate {
            float: right !important;
            margin-top: 15px !important;
            margin-bottom: 15px !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 6px 12px !important;
            margin-left: 2px !important;
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            background: white !important;
            color: #666 !important;
            text-decoration: none !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f8f9fa !important;
            border-color: #007bff !important;
            color: #007bff !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #007bff !important;
            border-color: #007bff !important;
            color: white !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            background: #f8f9fa !important;
            border-color: #ddd !important;
            color: #999 !important;
            cursor: not-allowed !important;
        }
        
        /* Search bar positioning */
        .dataTables_wrapper .dataTables_filter {
            float: right !important;
            margin-bottom: 15px !important;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            padding: 6px 12px !important;
            width: 200px !important;
        }
        
        /* Buttons positioning */
        .dataTables_wrapper .dt-buttons {
            float: right !important;
            margin-bottom: 15px !important;
            margin-right: 15px !important;
        }
        
        .dataTables_wrapper .dt-button {
            background: #6c757d !important;
            color: white !important;
            border: 1px solid #6c757d !important;
            border-radius: 4px !important;
            padding: 6px 12px !important;
            margin-left: 5px !important;
            font-size: 12px !important;
            transition: all 0.3s ease !important;
        }
        
        .dataTables_wrapper .dt-button:hover {
            background: #5a6268 !important;
            border-color: #545b62 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
        }
        
        /* Column-specific filtering for Created column */
        .dataTables_wrapper .dataTables_filter input[type="search"] {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>');
            background-repeat: no-repeat;
            background-position: 8px center;
            background-size: 16px;
            padding-left: 32px !important;
        }
        
        /* Ensure proper table header styling */
        .dataTables_wrapper table.dataTable thead th {
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #dee2e6 !important;
            font-weight: 600 !important;
            color: #495057 !important;
            padding: 12px 8px !important;
            text-align: left !important;
        }
        
        .dataTables_wrapper table.dataTable thead th.sorting {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7 15 5-5 5 5"/><path d="m7 9 5 5 5-5"/></svg>');
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 16px;
            cursor: pointer !important;
        }
        
        .dataTables_wrapper table.dataTable thead th.sorting_asc {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>');
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 16px;
            cursor: pointer !important;
        }
        
        .dataTables_wrapper table.dataTable thead th.sorting_desc {
            background-image: url('data:image/svg+xml;utf8,utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>');
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 16px;
            cursor: pointer !important;
        }
        
        /* Fix for search dropdown in modals */
        .ui-autocomplete {
            max-height: 200px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            z-index: 9999 !important;
            background: white !important;
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
            position: absolute !important;
        }
        
        .ui-autocomplete .ui-menu-item {
            padding: 8px 12px !important;
            cursor: pointer !important;
            border-bottom: 1px solid #f0f0f0 !important;
            font-size: 13px !important;
            line-height: 1.4 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        
        .ui-autocomplete .ui-menu-item:hover,
        .ui-autocomplete .ui-menu-item.ui-state-focus {
            background-color: #007bff !important;
            color: white !important;
            border-color: #007bff !important;
        }
        
        .ui-autocomplete .ui-menu-item:last-child {
            border-bottom: none !important;
        }
        
        /* Ensure modal has proper z-index */
        .modal {
            z-index: 1050 !important;
        }
        
        .modal-backdrop {
            z-index: 1040 !important;
        }
        
        /* Fix for select2 dropdowns in modals */
        .select2-container--open {
            z-index: 9999 !important;
        }
        
        .select2-dropdown {
            z-index: 9999 !important;
        }
        
        /* Ensure proper positioning for all dropdowns in modals */
        .modal .dropdown-menu,
        .modal .ui-autocomplete,
        .modal .select2-dropdown {
            position: absolute !important;
            z-index: 9999 !important;
            max-height: 200px !important;
            overflow-y: auto !important;
        }
        
        /* Additional fixes for autocomplete in modals */
        .modal .ui-autocomplete {
            position: fixed !important;
            max-width: 90% !important;
            width: auto !important;
            min-width: 200px !important;
        }
        
        /* Ensure autocomplete items are clickable */
        .ui-autocomplete .ui-menu-item {
            cursor: pointer !important;
            user-select: none !important;
        }
        
        .ui-autocomplete .ui-menu-item:hover,
        .ui-autocomplete .ui-menu-item.ui-state-focus {
            background-color: #007bff !important;
            color: white !important;
            border-color: #007bff !important;
        }
        
        /* Fix for autocomplete positioning */
        .ui-autocomplete {
            position: fixed !important;
            z-index: 99999 !important;
        }
        
        /* Ensure modal content doesn't overflow */
        .modal-body {
            overflow: visible !important;
        }
        
        /* Fix for search input in modal */
        .modal .form-control {
            position: relative !important;
        }
        
        /* Ensure proper spacing for autocomplete */
        .autocomplete-item {
            padding: 8px 12px !important;
            border-bottom: 1px solid #f0f0f0 !important;
        }
        
        .autocomplete-item:last-child {
            border-bottom: none !important;
        }
    </style> --}}

<script type="text/javascript">
    // Global flag to prevent multiple event bindings
        var ruleEventsBound = false;
        
        // Global variable for DataTable (like in taxonomy system)
        var limitRulesTable;
        
        // Function to restore consumer details modal if it was open
        function restoreConsumerDetailsModal() {
            if ($('.modal-backdrop').length > 1) {
                // Remove the extra backdrop
                $('.modal-backdrop').last().remove();
                // Restore consumer details modal
                setTimeout(function() {
                    $('#consumerDetailsModal').modal('show');
                }, 150);
            }
        }
        
        // Function to clean up modal backdrops
        function cleanupModalBackdrops() {
            // Simple cleanup - just remove extra backdrops
            var backdrops = $('.modal-backdrop');
            if (backdrops.length > 1) {
                backdrops.slice(1).remove();
            }
        }
        
        // Function to handle modal transitions smoothly
        function showModalWithTransition(modalId, hideConsumerModal = false) {
            // Simply show the modal - it will appear above due to z-index
            $(modalId).modal('show');
        }
        
        // Function to ensure proper modal state
        function ensureModalState() {
            // Simple cleanup - just remove extra backdrops
            cleanupModalBackdrops();
        }
        
        // Function to reset button state
        function resetSubmitButton() {
            var submitBtn = $('#addRuleForProductForm').find('button[type="submit"]');
            if (submitBtn.length) {
                var originalText = submitBtn.data('original-text') || 'Save Rule & Sale Limit';
                submitBtn.prop('disabled', false).text(originalText);
            }
        }
        
        // Function to remove backdrop for Consumer Details modal
        function removeConsumerModalBackdrop() {
            // Remove backdrop for Consumer Details modal to show background
            $('#consumerDetailsModal + .modal-backdrop').remove();
            // Also remove any backdrop that might be created
            $('.modal-backdrop').each(function() {
                if ($(this).prev('#consumerDetailsModal').length) {
                    $(this).remove();
                }
            });
        }
    
        $(document).ready(function() {
            console.log('Document ready - initializing components');
            
            // Global error handler to ensure button is never stuck disabled
            $(document).on('error', function() {
                resetSubmitButton();
            });
            
                        // Handle any AJAX errors globally
            $(document).ajaxError(function() {
                resetSubmitButton();
            });
            
            // Periodic check to ensure button is never stuck disabled
            setInterval(function() {
                var submitBtn = $('#addRuleForProductForm').find('button[type="submit"]');
                if (submitBtn.length && submitBtn.prop('disabled') && !$('#addRuleForProductModal').hasClass('processing')) {
                    resetSubmitButton();
                }
            }, 1000);
            
            // Prevent multiple event bindings
            if (!ruleEventsBound) {
                console.log('Binding rule events');
                $(document).off('click.ruleEvents');
    
                                // Edit rule button click
                $(document).on('click.ruleEvents', '.edit-rule', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var ruleId = $(this).data('rule-id');
                    console.log('EDIT CLICKED - Rule ID:', ruleId);
    
                    if ($(this).hasClass('processing')) {
                        console.log('Already processing edit, ignoring click');
                        return;
                    }
    
                    $(this).addClass('processing');
                    $.ajax({
                        url: '/products/sale-limit-control/get',
                        type: 'POST',
                        data: { id: ruleId, _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            if (response.status) {
                                var rule = response.data;
                                $('#editLimitRuleForm')[0].reset();
                                $('#edit_id').val(rule.id);
                                $('#edit_product_id').val(rule.product_id);
                                $('#edit_variant_id').val(rule.variant_id || '');
                                $('#edit_sale_limit').val(rule.sale_limit);
                                $('#edit_order_limit').val(rule.order_limit);
                                $('#edit_is_active').val(rule.is_active ? 1 : 0);

                                if (rule.start_date) {
                                    $('#edit_start_date').val(new Date(rule.start_date).toISOString().slice(0, 16));
                                }
                                if (rule.end_date) {
                                    $('#edit_end_date').val(new Date(rule.end_date).toISOString().slice(0, 16));
                                }

                                var productName = rule.product ? rule.product.name : 'Product';
                                var variantName = rule.variation ? rule.variation.name : '';
                                var displayText = variantName ? `${productName} - ${variantName}` : productName;
                                $('#edit_search_product').val(displayText);

                                // Check if consumer details modal is open and handle stacking
                                var consumerModalOpen = $('#consumerDetailsModal').hasClass('in');
                                showModalWithTransition('#editLimitModal', consumerModalOpen);
                            } else {
                                toastr.error(response.message || 'Failed to load rule data');
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to load rule data');
                        },
                        complete: function() {
                            $('.edit-rule[data-rule-id="' + ruleId + '"]').removeClass('processing');
                        }
                    });
                });
    
                // Delete rule button click
                $(document).on('click.ruleEvents', '.delete-rule', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var ruleId = $(this).data('rule-id');
                    var $deleteButton = $(this);
                    console.log('DELETE CLICKED - Rule ID:', ruleId);
    
                    if ($deleteButton.hasClass('processing')){
                        console.log('Already processing delete, ignoring click');
                        return;
                    }
    
                //     if (confirm('Are you sure you want to delete this rule?')) {
                //         $(this).addClass('processing');
                //         $.ajax({
                //             url: '/products/sale-limit-control/delete',
                //             type: 'POST',
                //             data: { id: ruleId, _token: $('meta[name="csrf-token"]').attr('content') },
                //             success: function(response) {
                //                 if (response.status) {
                //                     toastr.success(response.message);
                //                     limitRulesTable.ajax.reload();
                //                 } else {
                //                     var errorMessages = typeof response.message === 'object'
                //                         ? Object.values(response.message).flat().join('<br>')
                //                         : response.message;
                //                     toastr.error(errorMessages || 'Failed to delete rule');
                //                 }
                //             },
                //             error: function(xhr) {
                //                 var errorMessages = xhr.responseJSON?.message;
                //                 if (typeof errorMessages === 'object') {
                //                     errorMessages = Object.values(errorMessages).flat().join('<br>');
                //                 }
                //                 toastr.error(errorMessages || 'An error occurred while deleting the rule.');
                //             },
                //             complete: function() {
                //                 $('.delete-rule[data-rule-id="' + ruleId + '"]').removeClass('processing');
                //             }
                //         });
                //     }
                // });
    
            //     ruleEventsBound = true;
            //     console.log('Rule events bound successfully');
            // }
            
            swal({
                        title: "Are you sure?",
                        text: "Are you sure you want to delete this rule?",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $(this).addClass('processing');
                            $.ajax({
                                url: '/products/sale-limit-control/delete',
                                type: 'POST',
                                data: { id: ruleId, _token: $('meta[name="csrf-token"]').attr('content') },
                                success: function(response) {
                                    if (response.status) {
                                        toastr.success(response.message);
                                        // Reload the entire page after successful deletion
                                        setTimeout(function() {
                                            window.location.reload();
                                        }, 1000);
                                    } else {
                                        var errorMessages = typeof response.message === 'object'
                                            ? Object.values(response.message).flat().join('<br>')
                                            : response.message;
                                        toastr.error(errorMessages || 'Failed to delete rule');
                                    }
                                },
                                error: function(xhr) {
                                    var errorMessages = xhr.responseJSON?.message;
                                    if (typeof errorMessages === 'object') {
                                        errorMessages = Object.values(errorMessages).flat().join('<br>');
                                    }
                                    toastr.error(errorMessages || 'An error occurred while deleting the rule.');
                                },
                                complete: function() {
                                    $('.delete-rule[data-rule-id="' + ruleId + '"]').removeClass('processing');
                                }
                            });
                        }
                    });
                });
    
                ruleEventsBound = true;
                console.log('Rule events bound successfully');
            }
    
            // Initialize DataTable
                        limitRulesTable = $('#limitRulesTable').DataTable({
                processing: true,
                serverSide: false,
                paging: true,
         
                scrollX: true,
                scrollY: '400px',
                scrollCollapse: false,
                order: [[0, 'desc']], // Sort by Created column (index 0) in descending order (latest first)
               
               
                buttons: [
                    {
                text: '<i class="fa fa-filter"></i> Filters',
                className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                action: function () {
                    $('#filterModal').modal('show');
                }
            },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> ' + LANG.export_to_csv,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG.export_to_excel,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                            stripHtml: true,
                        },
                        footer: true,
                        customize: function (win) {
                            if ($('.print_table_part').length > 0) {
                                $($('.print_table_part').html()).insertBefore(
                                    $(win.document.body).find('table')
                                );
                            }
                            if ($(win.document.body).find('table.hide-footer').length) {
                                $(win.document.body).find('table.hide-footer tfoot').remove();
                            }
                            __currency_convert_recursively($(win.document.body).find('table'));
                        },
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                    },
                ],
                language: {
                    processing: '<div id="main_loader"><span class="loader"></span></div>'
                },
                ajax: {
                    url: '/products/sale-limit-control/get-all',
                    type: 'GET',
                    data: function(d) {
                        if ($('#statusFilter').val() && $('#statusFilter').val() !== 'all') {
                            d.status = $('#statusFilter').val();
                        }
                        if ($('#searchInput').val()) {
                            d.search = $('#searchInput').val();
                        }
                        if ($('#productFilter').val()) {
                            d.product_id = $('#productFilter').val();
                        }
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTable AJAX error:', error, thrown, xhr.responseText);
                        toastr.error('Failed to load table data');
                    }
                },
                columns: [
                    { 
                        data: 'created_at', 
                        name: 'created_at',
                        orderable: true, 
                        searchable: false,
                        
                    },
                    { data: 'product_name', name: 'product_name' },
                    { data: 'variant_name', name: 'variant_name' },
                    { data: 'sale_limit', name: 'sale_limit' },
                    { data: 'order_limit', name: 'order_limit' },
                    { data: 'start_date', name: 'start_date' },
                    { data: 'end_date', name: 'end_date' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                drawCallback: function() {
                    // Table draw completed
                    
                    // Add sorting indicator to Created column header
                    
                }
            });
    
            // Autocomplete for product/variant search
            $('#search_product').autocomplete({
                source: function(request, response) {
                    var searchFields = $('#addLimitModal input[name="search_fields[]"]:checked').map(function() {
                        return $(this).val();
                    }).get();
                    $.ajax({
                        url: '/purchases/get_products',
                        dataType: 'json',
                        data: {
                            term: request.term,
                            check_enable_stock: false,
                            search_fields: searchFields,
                            isParent: false
                        },
                        success: function(data) {
                            response(data.map(function(item) {
                                return {
                                    label: item.text,
                                    value: item.text,
                                    id: item.id,
                                    product_id: item.product_id,
                                    variation_id: item.variation_id,
                                    type: item.type
                                };
                            }));
                        },
                        error: function() {
                            toastr.error('Failed to load product search results');
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    $('#product_id').val(ui.item.product_id);
                    $('#variant_id').val(ui.item.type === 'single' ? null : ui.item.variation_id || null);
                    $('#search_product').val(ui.item.label);
                    return false;
                },
                open: function(event, ui) {
                    // Ensure dropdown is properly positioned within modal
                    var $autocomplete = $('.ui-autocomplete');
                    var $input = $(this);
                    var $modal = $input.closest('.modal');
                    
                    if ($modal.length) {
                        // Position dropdown relative to modal
                        $autocomplete.css({
                            'position': 'absolute',
                            'z-index': 9999,
                            'max-height': '200px',
                            'overflow-y': 'auto',
                            'width': $input.outerWidth()
                        });
                        
                        // Ensure dropdown doesn't go outside modal boundaries
                        var inputOffset = $input.offset();
                        var modalOffset = $modal.offset();
                        var dropdownHeight = Math.min(200, $autocomplete.height());
                        
                        if (inputOffset.top + $input.outerHeight() + dropdownHeight > modalOffset.top + $modal.height()) {
                            // Position above input if not enough space below
                            $autocomplete.css({
                                'top': inputOffset.top - dropdownHeight,
                                'left': inputOffset.left
                            });
                        } else {
                            // Position below input
                            $autocomplete.css({
                                'top': inputOffset.top + $input.outerHeight(),
                                'left': inputOffset.left
                            });
                        }
                    }
                },
                close: function(event, ui) {
                    // Clean up positioning
                    $('.ui-autocomplete').css({
                        'position': 'absolute',
                        'z-index': 9999
                    });
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>")
                    .append("<div class='autocomplete-item'>" + item.label + "</div>")
                    .appendTo(ul);
            };
    
            // Clear hidden fields when search input is cleared
            $('#search_product').on('input', function() {
                if ($(this).val() === '') {
                    $('#product_id').val('');
                    $('#variant_id').val('');
                }
            });
            
            // Fix modal positioning and z-index issues
            $('#addLimitModal').on('shown.bs.modal', function() {
                // Ensure proper z-index for modal
                $(this).css('z-index', '1050');
                
                // Fix any existing autocomplete dropdowns
                // $('.ui-autocomplete').css({
                //     'z-index': '9999',
                //     'position': 'absolute'
                // });
            });
            
            // Handle modal backdrop z-index
            // $('.modal-backdrop').css('z-index', '1040');
            
            // Ensure autocomplete works properly in modal
            $(document).on('click', '.ui-autocomplete .ui-menu-item', function(e) {
                e.preventDefault();
                var $item = $(this);
                var $input = $('#search_product');
                var $autocomplete = $input.autocomplete('instance');
                
                // Trigger selection manually
                var item = {
                    label: $item.text(),
                    value: $item.text(),
                    id: $item.data('id'),
                    product_id: $item.data('product_id'),
                    variation_id: $item.data('variation_id'),
                    type: $item.data('type')
                };
                
                $autocomplete._trigger('select', 'autocompleteselect', item);
                $input.autocomplete('close');
            });
    
            // Add rule form submission
            $('#limitRuleForm').submit(function(e) {
                e.preventDefault();
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
                    toastr.error('End date must be after start date');
                    return;
                }
                 // Validate that a product is selected
                //  var productId = $('#product_id').val();
                // if (!productId) {
                //     toastr.error('Please select a product/variant first');
                //     return;
                // }
    
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.text();
                submitBtn.prop('disabled', true).text('Saving...');
    
                $.ajax({
                    url: '/products/sale-limit-control/create',
                    type: 'POST',
                    data: $(this).serialize(),
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#addLimitModal').modal('hide');
                            limitRulesTable.ajax.reload();
                        } else {
                            var errorMessages = typeof response.message === 'object'
                                ? Object.values(response.message).flat().join('<br>')
                                : response.message;
                            toastr.error(errorMessages || 'Failed to create rule');
                        }
                    },
                    error: function(xhr) {
                        var errorMessages = xhr.responseJSON?.message;
                        if (typeof errorMessages === 'object') {
                            errorMessages = Object.values(errorMessages).flat().join('<br>');
                        }
                        toastr.error(errorMessages || 'An error occurred while creating the rule.');
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });
    
            // Edit rule form submission
            $('#editLimitRuleForm').submit(function(e) {
                e.preventDefault();
                var startDate = $('#edit_start_date').val();
                var endDate = $('#edit_end_date').val();
                if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
                    toastr.error('End date must be after start date');
                    return;
                }
    
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.text();
                submitBtn.prop('disabled', true).text('Updating...');
    
                $.ajax({
                    url: '/products/sale-limit-control/update',
                    type: 'POST',
                    data: $(this).serialize(),
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#editLimitModal').modal('hide');
                            limitRulesTable.ajax.reload();
                        } else {
                            var errorMessages = typeof response.message === 'object'
                                ? Object.values(response.message).flat().join('<br>')
                                : response.message;
                            toastr.error(errorMessages || 'Failed to update rule');
                        }
                    },
                    error: function(xhr) {
                        var errorMessages = xhr.responseJSON?.message;
                        if (typeof errorMessages === 'object') {
                            errorMessages = Object.values(errorMessages).flat().join('<br>');
                        }
                        toastr.error(errorMessages || 'An error occurred while updating the rule.');
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });
    
            // Add rule for existing product form submission
            $('#addRuleForProductForm').submit(function(e) {
                e.preventDefault();
                
                // Get submit button reference early
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.text();
                
                // Conditional validation for sale limit (only if field is editable)
                if (!$('#fixed_sale_limit').prop('readonly')) {
                    var saleLimit = $('#fixed_sale_limit').val();
                    if (!saleLimit || saleLimit < 1) {
                        toastr.error('Please enter a valid sale limit (minimum 1)');
                        $('#fixed_sale_limit').focus();
                        // Reset button state after validation error
                        resetSubmitButton();
                        return;
                    }
                }
                
                var startDate = $('#fixed_start_date').val();
                var endDate = $('#fixed_end_date').val();
                if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
                    toastr.error('End date must be after start date');
                    $('#fixed_end_date').focus();
                    // Reset button state after validation error
                    resetSubmitButton();
                    return;
                }
                
                // Only disable button AFTER all validation passes
                submitBtn.prop('disabled', true).text('Saving...');
                
                // Set processing flag
                $('#addRuleForProductModal').addClass('processing');
    
                $.ajax({
                    url: '/products/sale-limit-control/create',
                    type: 'POST',
                    data: $(this).serialize(),
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#addRuleForProductModal').modal('hide');
                            limitRulesTable.ajax.reload();
                        } else {
                            var errorMessages = typeof response.message === 'object'
                                ? Object.values(response.message).flat().join('<br>')
                                : response.message;
                            toastr.error(errorMessages || 'Failed to create rule');
                            // Re-enable submit button on error
                            submitBtn.prop('disabled', false).text(originalText);
                        }
                    },
                    error: function(xhr) {
                        var errorMessages = xhr.responseJSON?.message;
                        if (typeof errorMessages === 'object') {
                            errorMessages = Object.values(errorMessages).flat().join('<br>');
                        }
                        toastr.error(errorMessages || 'An error occurred while creating the rule.');
                        // Re-enable submit button on error
                        submitBtn.prop('disabled', false).text(originalText);
                    },
                    complete: function() {
                        // Remove processing flag
                        $('#addRuleForProductModal').removeClass('processing');
                        
                        // Only re-enable if not already handled in success/error
                        if (submitBtn.prop('disabled')) {
                            submitBtn.prop('disabled', false).text(originalText);
                        }
                    }
                });
            });
    
            // Handle add rule for existing product
            $(document).on('click', '.add-rule-for-product', function(e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                var variantId = $(this).data('variant-id') || '';
                
                // Check if this product already has a rule by looking at the row data
                var $row = $(this).closest('tr');
                var rowData = limitRulesTable.row($row).data();
                var hasExistingRule = rowData && rowData.rule_id;
                
                $.ajax({
                    url: '/products/sale-limit-control/get-product-details',
                    type: 'POST',
                    data: {
                        product_id: productId,
                        variant_id: variantId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            var product = response.data;
                            var displayText = product.product_name + (product.variant_name ? ' - ' + product.variant_name : '');
                            $('#fixed_search_product').val(displayText);
                            $('#fixed_product_id').val(product.product_id);
                            $('#fixed_variant_id').val(product.variant_id || '');
                            
                            // Conditional logic for sale limit field
                            if (hasExistingRule) {
                                // Product already has a rule - make sale limit read-only and show current value
                                $('#fixed_sale_limit').prop('readonly', true)
                                    .val(product.sale_limit || 'No Limit')
                                    .removeClass('is-invalid')
                                    .removeAttr('required')
                                    .attr('placeholder', 'Current sale limit (read-only)')
                                    .addClass('sale-limit-readonly');
                                $('#fixed_sale_limit').closest('.form-group').find('label span.text-danger').hide();
                                $('#fixed_sale_limit').closest('.form-group').find('small').text('Current sale limit (cannot be changed here)');
                            } else {
                                // No existing rule - make sale limit editable and required
                                $('#fixed_sale_limit').prop('readonly', false)
                                    .val('')
                                    .removeClass('is-invalid sale-limit-readonly')
                                    .attr('required', 'required')
                                    .attr('placeholder', 'Enter sale limit');
                                $('#fixed_sale_limit').closest('.form-group').find('label span.text-danger').show();
                                $('#fixed_sale_limit').closest('.form-group').find('small').text('Maximum quantity per order');
                            }
                            
                            $('#fixed_order_limit').val('');
                            $('#fixed_is_active').val('1');
                            $('#fixed_start_date').val('');
                            $('#fixed_end_date').val('');
                            
                            // Update modal title and button text based on whether we're adding or editing
                            if (hasExistingRule) {
                                $('#addRuleForProductModal .modal-title').text('Add Additional Rule');
                                $('#addRuleForProductModal .btn-primary').text('Save Additional Rule');
                            } else {
                                $('#addRuleForProductModal .modal-title').text('Add Rule & Set Sale Limit');
                                $('#addRuleForProductModal .btn-primary').text('Save Rule & Sale Limit');
                            }
                            
                            // Check if consumer details modal is open and handle stacking
                            var consumerModalOpen = $('#consumerDetailsModal').hasClass('in');
                            showModalWithTransition('#addRuleForProductModal', consumerModalOpen);
                        } else {
                            toastr.error(response.message || 'Failed to load product details');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Failed to load product details');
                    }
                });
            });
    
            // Reset forms when modals are closed
            $('#addLimitModal').on('hidden.bs.modal', function() {
                $('#limitRuleForm')[0].reset();
                $('#product_id').val('');
                $('#variant_id').val('');
                $('#search_product').val('');
            });
    
            $('#editLimitModal').on('hidden.bs.modal', function() {
                $('#editLimitRuleForm')[0].reset();
                $('#edit_id').val('');
                $('#edit_product_id').val('');
                $('#edit_variant_id').val('');
                $('#edit_search_product').val('');
                $(this).find('button[type="submit"]').prop('disabled', false).text('Update Rule');
            });
    
                    $('#addRuleForProductModal').on('shown.bs.modal', function() {
                        // Ensure submit button is enabled when modal is shown
                        var submitBtn = $('#addRuleForProductForm').find('button[type="submit"]');
                        submitBtn.prop('disabled', false);
                        
                        // Reset button text to original
                        var hasExistingRule = $('#fixed_sale_limit').prop('readonly');
                        if (hasExistingRule) {
                            submitBtn.text('Save Additional Rule');
                        } else {
                            submitBtn.text('Save Rule & Sale Limit');
                        }
                        
                        // Store original button state
                        submitBtn.data('original-text', submitBtn.text());
                        submitBtn.data('original-disabled', false);
                    });
                    
                    $('#addRuleForProductModal').on('hidden.bs.modal', function() {
            $('#addRuleForProductForm')[0].reset();
            $('#fixed_search_product').val('');
            $('#fixed_product_id').val('');
            $('#fixed_variant_id').val('');
            $('#fixed_sale_limit').val('');
            
            // Re-enable submit button when modal is closed
            var submitBtn = $('#addRuleForProductForm').find('button[type="submit"]');
            submitBtn.prop('disabled', false);
        });

                    // Reset edit sale limit modal when closed
            $('#editSaleLimitModal').on('hidden.bs.modal', function() {
            var $form = $('#editSaleLimitForm');
            var $submitBtn = $form.find('button[type="submit"]');
            
            // Reset form
            $form[0].reset();
            
            // Clear hidden fields
            $('#edit-sale-limit-current').val('');
            $('#edit-sale-limit-product-id').val('');
            $('#edit-sale-limit-variant-id').val('');
            
            // Re-enable submit button
            $submitBtn.prop('disabled', false).text('Update Sale Limit');
            
            // Remove any error states
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').remove();
        });

        // Ensure form is properly reset when modal is shown
        $('#editSaleLimitModal').on('shown.bs.modal', function() {
            var $form = $('#editSaleLimitForm');
            var $submitBtn = $form.find('button[type="submit"]');
            
            // Ensure submit button is enabled and has correct text
            $submitBtn.prop('disabled', false).text('Update Sale Limit');
            
            // Focus on the sale limit input
            $('#edit-sale-limit-new').val('').focus();
            
            // Remove any previous error states
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').remove();
        });
        
        // Reset consumer details modal when closed
        $('#consumerDetailsModal').on('hidden.bs.modal', function() {
            // Reset variant limits section
            $('#variant-limits-section').hide();
            $('#variant-limits-table-body').html('<tr><td colspan="3" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading variant information...</td></tr>');
        });
    
            // Consumer details on row click
            $(document).on('click', '#limitRulesTable tbody tr', function(e) {
                if ($(e.target).hasClass('btn') || $(e.target).closest('.btn').length) {
                    return;
                }
                var rowData = limitRulesTable.row(this).data();
                
                // Check if this row has a rule or not
                if (rowData) {
                    // Check if there's a rule_id (null means no rule exists)
                    if (!rowData.rule_id) {
                        // This is a product without rules - use product_id
                        $.ajax({
                            url: '/products/sale-limit-control/get-consumer-details',
                            type: 'POST',
                            data: { product_id: rowData.product_id, _token: $('meta[name="csrf-token"]').attr('content') },
                            success: function(response) {
                                if (response.status) {
                                    $('#modal-product-name').text(response.data.product_name || 'N/A');
                                    $('#modal-variant-name').text(response.data.variant_name || 'N/A');
                                    $('#modal-sale-limit').text(response.data.product_sale_limit || 'No Limit');
                                    
                                    // Populate variant purchase limits if variants exist
                                    if (response.data.variants && response.data.variants.length > 0) {
                                        $('#variant-limits-section').show();
                                        $('#variant-limits-table-body').empty();
                                        
                                        response.data.variants.forEach(variant => {
                                            var statusClass = variant.sale_limit && variant.sale_limit > 0 ? 'success' : 'warning';
                                            var statusText = variant.sale_limit && variant.sale_limit > 0 ? 'Active' : 'No Limit';
                                            
                                            var row = `
                                                <tr>
                                                    <td>${variant.name || 'N/A'}</td>
                                                    <td>${variant.sale_limit || 'No Limit'}</td>
                                                    <td><span class="label label-${statusClass}">${statusText}</span></td>
                                                    <td>${variant.edit_button || ''}</td>
                                                </tr>
                                            `;
                                            $('#variant-limits-table-body').append(row);
                                        });
                                    } else {
                                        $('#variant-limits-section').hide();
                                    }
                                    
                                    // No rules exist - show appropriate message
                                    $('#rules-table-body').html('<tr><td colspan="6" class="text-center"><div class="alert alert-info">No rules exist for this product. You can add a rule using the "Add New Rule" button.</div></td></tr>');
                                    $('#consumer-logs-table-body').html('<tr><td colspan="7" class="text-center"><div class="alert alert-info">No consumer logs available as no rules exist for this product.</div></td></tr>');
                                    
                                    $('#consumerDetailsModal').modal('show');
                                } else {
                                    toastr.error(response.message || 'Failed to load product information');
                                }
                            },
                            error: function() {
                                toastr.error('Failed to load product information');
                            }
                        });
                    } else {
                        // This is a product with rules - use rule_id
                        if (rowData.rule_id) {
                            $.ajax({
                                url: '/products/sale-limit-control/get-consumer-details',
                                type: 'POST',
                                data: { rule_id: rowData.rule_id, _token: $('meta[name="csrf-token"]').attr('content') },
                                success: function(response) {
                                    if (response.status) {
                                        $('#modal-product-name').text(response.data.product_name || 'N/A');
                                        $('#modal-variant-name').text(response.data.variant_name || 'N/A');
                                        $('#modal-sale-limit').text(response.data.product_sale_limit || 'No Limit');
                                        $('#modal-start-date').text(response.data.start_date || 'N/A');
                                        $('#modal-end-date').text(response.data.end_date || 'N/A');
                                        $('#modal-created-at').text(response.data.created_at || 'N/A');
                                        
                                        // Populate variant purchase limits if variants exist
                                        if (response.data.variants && response.data.variants.length > 0) {
                                            $('#variant-limits-section').show();
                                            $('#variant-limits-table-body').empty();
                                            
                                            response.data.variants.forEach(variant => {
                                                var statusClass = variant.sale_limit && variant.sale_limit > 0 ? 'success' : 'warning';
                                                var statusText = variant.sale_limit && variant.sale_limit > 0 ? 'Active' : 'No Limit';
                                                
                                                var row = `
                                                    <tr>
                                                        <td>${variant.name || 'N/A'}</td>
                                                        <td>${variant.sale_limit || 'No Limit'}</td>
                                                        <td><span class="label label-${statusClass}">${statusText}</span></td>
                                                        <td>${variant.edit_button || ''}</td>
                                                    </tr>
                                                `;
                                                $('#variant-limits-table-body').append(row);
                                            });
                                        } else {
                                            $('#variant-limits-section').hide();
                                        }

                                        // Populate rules table
                                        if (response.data.all_rules && response.data.all_rules.length) {
                                            // Clear the table body first
                                            $('#rules-table-body').empty();
                                            
                                            // Populate the table with rules
                                            response.data.all_rules.forEach((rule, index) => {
                                                var row = `
                                                    <tr>
                                                        <td>${rule.order_limit || 'N/A'}</td>
                                                        <td>${rule.start_date || 'N/A'}</td>
                                                        <td>${rule.end_date || 'N/A'}</td>
                                                        <td>${rule.status_html || 'N/A'}</td>
                                                        <td>${rule.created_at || 'N/A'}</td>
                                                        <td>
                                                            <div class="btn-group btn-group-xs">
                                                                <button type="button" class="btn btn-primary btn-xs edit-rule" data-rule-id="${rule.id}" title="Edit Rule">
                                                                    <i class="fa fa-edit"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger btn-xs delete-rule" data-rule-id="${rule.id}" title="Delete Rule">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                `;
                                                $('#rules-table-body').append(row);
                                            });
                                        } else {
                                            $('#rules-table-body').html('<tr><td colspan="6" class="text-center text-muted">No rules found for this product.</td></tr>');
                                        }

                                        // Populate consumer logs table
                                        if (response.data.consumers && response.data.consumers.length) {
                                            // Clear the table body first
                                            $('#consumer-logs-table-body').empty();
                                            
                                            // Populate the table with consumers
                                            response.data.consumers.forEach(consumer => {
                                                var row = `
                                                    <tr>
                                                        <td>${consumer.contact_name || 'N/A'}</td>
                                                        <td>${consumer.email || 'N/A'}</td>
                                                        <td>${consumer.mobile || 'N/A'}</td>
                                                        <td>${consumer.order_count || 0}</td>
                                                        <td>${consumer.qty_count || 0}</td>
                                                        <td>${consumer.blocked_attempts || 0}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-info btn-xs view-logs" data-consumer-id="${consumer.id}" title="View Logs">
                                                                <i class="fa fa-eye"></i> View
                                                            </button>
                                                        </td>
                                                    </tr>
                                                `;
                                                $('#consumer-logs-table-body').append(row);
                                            });
                                        } else {
                                            $('#consumer-logs-table-body').html('<tr><td colspan="7" class="text-center text-muted">No consumer logs found for this rule.</td></tr>');
                                        }

                                        $('#consumerDetailsModal').modal('show');
                                    } else {
                                        toastr.error(response.message || 'Failed to load consumer details');
                                    }
                                },
                                error: function() {
                                    toastr.error('Failed to load consumer details');
                                }
                            });
                        }
                    }
                }
            });
            
            // Remove backdrop when Consumer Details modal is shown
            $('#consumerDetailsModal').on('shown.bs.modal', function() {
                removeConsumerModalBackdrop();
            });
    
            // View consumer logs
            $(document).on('click', '.view-logs', function() {
                var consumerId = $(this).data('consumer-id');
                $.ajax({
                    url: '/products/sale-limit-control/get-consumer-logs',
                    type: 'POST',
                    data: { consumer_id: consumerId, _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.status) {
                            var logsHtml = response.data.logs && response.data.logs.length
                                ? `<div class="table-responsive"><table class="table table-bordered table-striped">
                                    <thead><tr><th>Timestamp</th><th>Qty Attempted</th><th>Will Exceed Qty</th><th>Will Exceed Orders</th><th>Remaining Qty</th><th>Remaining Orders</th></tr></thead>
                                    <tbody>${response.data.logs.map(log => `
                                        <tr>
                                            <td>${log.timestamp || 'N/A'}</td>
                                            <td>${log.qty_going_to_add || 0}</td>
                                            <td><span class="label label-${log.will_exceed_qty ? 'danger' : 'success'}">${log.will_exceed_qty ? 'Yes' : 'No'}</span></td>
                                            <td><span class="label label-${log.will_exceed_orders ? 'danger' : 'success'}">${log.will_exceed_orders ? 'Yes' : 'No'}</span></td>
                                            <td>${log.remaining_qty || 0}</td>
                                            <td>${log.remaining_orders || 0}</td>
                                        </tr>
                                    `).join('')}</tbody></table></div>`
                                : '<div class="alert alert-info">No detailed logs found</div>';
                            $('#consumer-logs-container').html(logsHtml);
                        } else {
                            toastr.error(response.message || 'Failed to load detailed logs');
                        }
                    },
                    error: function() {
                        toastr.error('Failed to load detailed logs');
                    }
                });
            });
    
            // Apply filters
            $('#applyFilters').click(function() {
                limitRulesTable.ajax.reload();
            });
    
            // Clear filters
            $('#clearFilters').click(function() {
                $('#statusFilter').val('all');
                $('#searchInput').val('');
                $('#productFilter').val('');
                limitRulesTable.ajax.reload();
            });
            

        });

        // Handle product info button click (for products with no rule)
        $(document).on('click', '.show-product-info', function() {
            var productId = $(this).data('product-id');
            
            // Show loading state in consumer details modal
                            $('#modal-product-name').text('Loading...');
                $('#modal-variant-name').text('Loading...');
                $('#modal-sale-limit').text('Loading...');
                $('#rules-table-body').html('<tr><td colspan="6" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');
                $('#consumer-logs-table-body').html('<tr><td colspan="7" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');
            
            // Show the consumer details modal
            $('#consumerDetailsModal').modal('show');
            
            // Fetch product information using the same endpoint
            $.ajax({
                url: '/products/sale-limit-control/get-consumer-details',
                type: 'POST',
                data: {
                    product_id: productId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === true) {
                        var data = response.data;
                        
                        // Update product information
                        $('#modal-product-name').text(data.product_name);
                        $('#modal-variant-name').text(data.variant_name);
                        $('#modal-sale-limit').text(data.product_sale_limit || 'No Limit');
                        
                        if (response.has_rules === false) {
                            // No rules exist - show appropriate message
                            $('#rules-table-body').html('<tr><td colspan="6" class="text-center"><div class="alert alert-info">No rules exist for this product. You can add a rule using the "Add New Rule" button.</div></td></tr>');
                            $('#consumer-logs-table-body').html('<tr><td colspan="7" class="text-center"><div class="alert alert-info">No consumer logs available as no rules exist for this product.</div></td></tr>');
                        } else {
                            // Rules exist - populate rules and consumer logs
                            // This will be handled by the existing logic
                        }
                    } else {
                        $('#rules-table-body').html('<tr><td colspan="6" class="text-center"><div class="alert alert-danger">' + (response.message || 'Failed to load product information') + '</div></td></tr>');
                        $('#consumer-logs-table-body').html('<tr><td colspan="7" class="text-center"><div class="alert alert-danger">Failed to load data</div></td></tr>');
                    }
                },
                error: function() {
                    $('#rules-table-body').html('<tr><td colspan="6" class="text-center"><div class="alert alert-danger">Failed to load product information. Please try again.</div></td></tr>');
                    $('#consumer-logs-table-body').html('<tr><td colspan="7" class="text-center"><div class="alert alert-danger">Failed to load data</div></tr></td></tr>');
                }
            });
        });

                    // Handle edit variant purchase limit button click
            $(document).on('click', '.edit-variant-purchase-limit', function() {
                var productId = $(this).data('product-id');
                var variantId = $(this).data('variant-id');
                var variantName = $(this).data('variant-name');
                var currentLimit = $(this).data('current-limit');
                
                // Reset form first
                var $form = $('#editVariantPurchaseLimitForm');
                $form[0].reset();
                
                // Clear any previous error states
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').remove();
                
                // Set values in the modal
                $('#edit-variant-product-id').val(productId);
                $('#edit-variant-id').val(variantId);
                $('#edit-variant-name').val(variantName);
                $('#edit-variant-purchase-limit').val(currentLimit === 'No Limit' ? '' : currentLimit);
                
                // Show the modal
                $('#editVariantPurchaseLimitModal').modal('show');
            });
            $('#editVariantPurchaseLimitForm').submit(function(e) {
                e.preventDefault();
                
                var $form = $(this);
                var $submitBtn = $form.find('button[type="submit"]');
                var originalText = $submitBtn.text();
                
                // Disable submit button and show loading state
                $submitBtn.prop('disabled', true).text('Updating...');
                
                // Get form data
                var formData = {
                    product_id: $('#edit-variant-product-id').val(),
                    variant_id: $('#edit-variant-id').val(),
                    purchase_limit: $('#edit-variant-purchase-limit').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                
                // Submit form via AJAX
                $.ajax({
                    url: '/products/sale-limit-control/update-variant-purchase-limit',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message || 'Variant purchase limit updated successfully');
                            $('#editVariantPurchaseLimitModal').modal('hide');
                            
                            // Refresh the consumer details modal if it's open
                            if ($('#consumerDetailsModal').hasClass('in')) {
                                var currentRow = limitRulesTable.row('.selected').node();
                                if (currentRow) {
                                    $(currentRow).click();
                                }
                            }
                        } else {
                            toastr.error(response.message || 'Failed to update variant purchase limit');
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Failed to update variant purchase limit';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });
            
            // Handle edit sale limit button click
            $(document).on('click', '.edit-sale-limit', function() {
            var productId = $(this).data('product-id');
            var variantId = $(this).data('variant-id');
            var currentLimit = $(this).data('current-limit');
            
            // Reset form first
            var $form = $('#editSaleLimitForm');
            $form[0].reset();
            
            // Clear any previous error states
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').remove();
            
            // Set values in the modal
            $('#edit-sale-limit-product-id').val(productId);
            $('#edit-sale-limit-variant-id').val(variantId);
            $('#edit-sale-limit-current').val(currentLimit);
            $('#edit-sale-limit-new').val('');
            
            // Ensure submit button is enabled and has correct text
            var $submitBtn = $('#editSaleLimitModal').find('button[type="submit"]');
            $submitBtn.prop('disabled', false).text('Update Sale Limit');
            
            // Show the modal
            $('#editSaleLimitModal').modal('show');
        });

        // Handle sale limit form submission
        $('#editSaleLimitForm').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"]');
            var originalText = $submitBtn.text();
            
            // Prevent double submission
            if ($submitBtn.prop('disabled')) {
                return false;
            }
            
            // Basic validation
            var productId = $('#edit-sale-limit-product-id').val();
            var saleLimit = $('#edit-sale-limit-new').val();
            
            if (!productId) {
                toastr.error('Product ID is missing');
                return false;
            }
            
            if (!saleLimit || saleLimit < 1) {
                toastr.error('Please enter a valid sale limit (minimum 1)');
                return false;
            }
            
            // Disable button and show loading state
            $submitBtn.prop('disabled', true).text('Updating...');
            
            // Prepare form data
            var formData = {
                product_id: productId,
                variant_id: $('#edit-sale-limit-variant-id').val() || null,
                sale_limit: saleLimit,
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            $('#filterModal').on('hidden.bs.modal', function() {
                $('.daterangepicker').hide();
            });

            // Submit form via AJAX
            $.ajax({
                url: '/products/sale-limit-control/update-sale-limit',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === true) {
                        // Success
                        toastr.success(response.message || 'Sale limit updated successfully');
                        $('#editSaleLimitModal').modal('hide');
                        
                        // Refresh the table (exactly like taxonomy system)
                        if (typeof limitRulesTable !== 'undefined') {
                            limitRulesTable.ajax.reload();
                        }
                        
                        // Reset form
                        $form[0].reset();
                    } else {
                        // Server returned error
                        toastr.error(response.message || 'Failed to update sale limit');
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = 'Failed to update sale limit';
                    
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            // Validation errors
                            var errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                        } else if (xhr.responseJSON.message) {
                            // Server error message
                            errorMessage = xhr.responseJSON.message;
                        }
                    } else if (xhr.status === 422) {
                        errorMessage = 'Validation failed. Please check your input.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error occurred. Please try again.';
                    }
                    
                    toastr.error(errorMessage);
                },
                complete: function() {
                    // Always re-enable button
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });

            // Handle edit variant purchase limit form submission
            $('#editVariantPurchaseLimitForm').submit(function(e) {
                e.preventDefault();
                
                var $form = $(this);
                var $submitBtn = $form.find('button[type="submit"]');
                var originalText = $submitBtn.text();
                
                // Disable submit button and show loading state
                $submitBtn.prop('disabled', true).text('Updating...');
                
                // Get form data
                var formData = {
                    product_id: $('#edit-variant-product-id').val(),
                    variant_id: $('#edit-variant-id').val(),
                    purchase_limit: $('#edit-variant-purchase-limit').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                
                // Submit form via AJAX
                $.ajax({
                    url: '/products/sale-limit-control/update-variant-purchase-limit',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message || 'Variant purchase limit updated successfully');
                            $('#editVariantPurchaseLimitModal').modal('hide');
                            
                            // Refresh the consumer details modal if it's open
                            if ($('#consumerDetailsModal').hasClass('in')) {
                                // Trigger a click on the current row to refresh data
                                var currentRow = limitRulesTable.row('.selected').node();
                                if (currentRow) {
                                    $(currentRow).click();
                                }
                            }
                        } else {
                            toastr.error(response.message || 'Failed to update variant purchase limit');
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Failed to update variant purchase limit';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        // Re-enable submit button
                        $submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });
        });
</script>
@endsection
