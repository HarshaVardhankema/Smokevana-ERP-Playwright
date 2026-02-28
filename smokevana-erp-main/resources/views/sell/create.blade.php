@extends('layouts.app')

@php
    if (!empty($status) && $status == 'quotation') {
        $title = __('lang_v1.add_quotation');
    } elseif (!empty($status) && $status == 'draft') {
        $title = __('lang_v1.add_draft');
    } else {
        $title = __('sale.add_sale');
    }

    if ($sale_type == 'sales_order') {
        $title = __('lang_v1.sales_order');
    }
    @endphp

    @section('title', $title)

@section('css')
<style>
/* ========================================
   AMAZON THEME - SALES ORDER CREATE
   ======================================== */

.so-container {
    padding: 16px 20px;
    margin: 0;
    background: #EAEDED;
    min-height: 100vh;
}

/* Header Bar – same style as Add new product / Manage Order / Sales Invoice */
.so-header {
    background: #37475a;
    border-radius: 6px;
    padding: 22px 28px;
    margin: 4px 0 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
}

.so-header-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.so-header-title {
    color: #ffffff;
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.so-header-title i {
    font-size: 22px;
    color: #ffffff !important;
}

.so-header-subtitle {
    font-size: 13px;
    color: rgba(249, 250, 251, 0.88);
    margin: 0;
}

.so-header-actions {
    display: flex;
    gap: 10px;
}

.so-btn {
    padding: 8px 20px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.so-btn-save {
    background: linear-gradient(180deg, #FFD814 0%, #FF9900 100%);
    color: #0F1111;
    border: 1px solid #FFA500;
}

.so-btn-save:hover {
    background: linear-gradient(180deg, #FFE033 0%, #FFB020 100%);
    box-shadow: 0 2px 5px rgba(255, 153, 0, 0.3);
}

.so-btn-print {
    background: linear-gradient(180deg, #36C478 0%, #2B9E5E 100%);
    color: #FFFFFF;
    border: 1px solid #2B9E5E;
}

.so-btn-print:hover {
    background: linear-gradient(180deg, #45D589 0%, #36B56A 100%);
}

.so-btn-secondary {
    background: linear-gradient(180deg, #F7FAFA 0%, #E3E6E6 100%);
    color: #0F1111;
    border: 1px solid #D5D9D9;
}

.so-btn-secondary:hover {
    background: linear-gradient(180deg, #EDEDED 0%, #D5D9D9 100%);
}

/* Customer Info Bar - Compact Single Row */
.so-customer-bar {
    background: #FFFFFF;
    padding: 10px 20px;
    border-bottom: 1px solid #D5D9D9;
    display: grid;
    grid-template-columns: 180px 1fr 1fr 150px 160px 170px 140px;
    gap: 15px;
    align-items: start;
}

.so-field-group {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.so-field-label {
    font-size: 11px;
    font-weight: 600;
    color: #565959;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.so-field-value {
    font-size: 12px;
    color: #0F1111;
    line-height: 1.3;
}

.so-customer-select {
    display: flex;
    align-items: center;
    gap: 8px;
}

.so-customer-select .form-control {
    height: 32px;
    font-size: 12px;
    border-radius: 4px;
    border: 1px solid #888C8C;
    padding: 4px 8px;
}

.so-customer-select .btn {
    height: 32px;
    width: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    background: #37475a;
    border: none;
    color: #fff;
}

.so-address-text {
    font-size: 11px;
    color: #565959;
    line-height: 1.4;
    max-height: 50px;
    overflow: hidden;
}

.so-sales-rep {
    background: #F0F2F2;
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    color: #0F1111;
    text-align: center;
}

/* Product Search Section - PROMINENT */
.so-search-section {
    background: linear-gradient(180deg, #232F3E 0%, #37475A 100%);
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.so-search-wrapper {
    flex: 1;
    max-width: 800px;
    position: relative;
}

.so-search-wrapper .input-group {
    position: relative;
}

.so-search-input {
    height: 44px !important;
    padding: 0 50px 0 40px !important;
    font-size: 14px !important;
    border: 1px solid #888C8C !important;
    border-radius: 8px !important;
    background: #FFFFFF !important;
    color: #0F1111 !important;
    outline: none !important;
    transition: all 0.2s ease !important;
}

.so-search-input:focus {
    border-color: #37475a !important;
    box-shadow: 0 0 0 2px rgba(55, 71, 90, 0.2) !important;
}

.so-search-input::placeholder {
    color: #888C8C !important;
}

.so-search-wrapper .input-group-btn:first-child {
    position: absolute;
    left: 0;
    z-index: 10;
    border: none;
    background: transparent;
}

.so-search-wrapper .input-group-btn:first-child .btn {
    border: none;
    background: transparent;
    padding: 12px;
    color: #888C8C;
}

.so-search-btn {
    position: absolute;
    right: 0;
    top: 0;
    height: 44px;
    width: 50px;
    background: #37475a;
    border: none;
    border-radius: 0 8px 8px 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.so-search-btn:hover {
    background: #232f3e;
}

.so-search-btn svg {
    width: 20px;
    height: 20px;
    color: #ffffff;
}

.so-search-options {
    display: flex;
    align-items: center;
    gap: 15px;
}

.so-matrix-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #FFFFFF;
    font-size: 12px;
    cursor: pointer;
}

.so-toggle-switch {
    width: 36px;
    height: 20px;
    background: #565959;
    border-radius: 10px;
    position: relative;
    transition: all 0.3s ease;
    cursor: pointer;
}

.so-toggle-switch.active {
    background: #37475a;
}

.so-toggle-switch::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    background: #FFFFFF;
    border-radius: 50%;
    top: 2px;
    left: 2px;
    transition: all 0.3s ease;
}

.so-toggle-switch.active::after {
    left: 18px;
}

.so-quick-actions {
    display: flex;
    gap: 8px;
}

.so-quick-btn {
    height: 36px;
    padding: 0 12px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    color: #FFFFFF;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.so-quick-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
}

/* Product Table - COMPACT */
.so-products-section {
    background: #FFFFFF;
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    /* Height tuned for ~10–15 visible product rows with sticky footer */
    min-height: 420px;
    max-height: calc(100vh - 260px);
}

.so-products-table-wrapper {
    flex: 1;
    overflow: auto;
}

.so-products-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.so-products-table thead {
    position: sticky;
    top: 0;
    z-index: 10;
}

.so-products-table thead th {
    background: #37475a;
    color: #FFFFFF;
    padding: 8px 6px;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    text-align: center;
    border-bottom: 2px solid #2c3948;
    white-space: nowrap;
}

.so-products-table tbody tr {
    border-bottom: 1px solid #E7E7E7;
    transition: background 0.15s ease;
}

.so-products-table tbody tr:hover {
    background: #F7FAFA;
}

.so-products-table tbody tr:nth-child(even) {
    background: #FAFAFA;
}

.so-products-table tbody tr:nth-child(even):hover {
    background: #F0F2F2;
}

.so-products-table tbody td {
    padding: 6px;
    text-align: center;
    vertical-align: middle;
    border-right: 1px solid #E7E7E7;
}

.so-products-table tbody td:last-child {
    border-right: none;
}

/* Product Name Column - Wider */
.so-products-table .product-col {
    text-align: left;
    min-width: 300px;
    max-width: 400px;
}

.so-product-name {
    font-weight: 500;
    color: #007185;
    font-size: 12px;
    line-height: 1.3;
    cursor: pointer;
}

.so-product-name:hover {
    color: #37475a;
    text-decoration: underline;
}

.so-product-sku {
    font-size: 10px;
    color: #565959;
}

/* Input Fields in Table */
.so-products-table input[type="text"],
.so-products-table input[type="number"] {
    width: 100%;
    height: 28px;
    padding: 2px 6px;
    font-size: 12px;
    border: 1px solid #D5D9D9;
    border-radius: 4px;
    text-align: center;
    background: #FFF;
}

.so-products-table input:focus {
    border-color: #37475a;
    outline: none;
    box-shadow: 0 0 0 2px rgba(55, 71, 90, 0.2);
}

.so-products-table select {
    height: 28px;
    padding: 2px 4px;
    font-size: 11px;
    border: 1px solid #D5D9D9;
    border-radius: 4px;
    background: #FFF;
}

/* Discount input + $ / % toggle */
.discount-input-wrap {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    width: 140px !important;
    margin: 0 auto !important;
}

.discount-input-wrap .discount-amt {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    height: 30px !important;
    padding: 2px 6px !important;
    font-size: 12px !important;
    border: 1px solid #D5D9D9 !important;
    border-radius: 4px !important;
    box-sizing: border-box !important;
}

.discount-input-wrap .discount-type-sel {
    flex: 0 0 44px !important;
    width: 44px !important;
    min-width: 44px !important;
    max-width: 44px !important;
    height: 30px !important;
    padding: 0 !important;
    margin: 0 !important;
    font-size: 16px !important;
    font-weight: 700 !important;
    line-height: 30px !important;
    text-align: center !important;
    text-align-last: center !important;
    color: #0F1111 !important;
    background: #F0F2F2 !important;
    border: 1px solid #D5D9D9 !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    box-shadow: none !important;
    outline: none !important;
    overflow: visible !important;
    box-sizing: border-box !important;
}

.discount-input-wrap .discount-type-sel:hover {
    border-color: #FF9900 !important;
    background: #FFF8E7 !important;
}

.discount-input-wrap .discount-type-sel:focus {
    border-color: #FF9900 !important;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.25) !important;
}

.discount-input-wrap .discount-type-sel option {
    font-size: 15px;
    font-weight: 700;
    text-align: center;
    padding: 6px 10px;
}

/* Quantity Available Badge */
.so-qty-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
}

.so-qty-badge.in-stock {
    background: #E7F4E4;
    color: #067D62;
}

.so-qty-badge.low-stock {
    background: #FFF4E5;
    color: #B24D00;
}

.so-qty-badge.out-stock {
    background: #FFE5E5;
    color: #CC0000;
}

/* Action Buttons */
.so-action-btn {
    width: 26px;
    height: 26px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
}

.so-action-btn.delete {
    background: #FFE5E5;
    color: #CC0000;
}

.so-action-btn.delete:hover {
    background: #CC0000;
    color: #FFFFFF;
}

/* Summary Footer - STICKY */
.so-summary-footer {
    background: linear-gradient(180deg, #FFFFFF 0%, #F7F8F8 100%);
    border-top: 2px solid #D5D9D9;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    bottom: 0;
    z-index: 50;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}

.so-summary-left {
    display: flex;
    gap: 20px;
    align-items: flex-end;
    flex: 1;
}

.so-summary-field {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.so-summary-field label {
    font-size: 11px;
    font-weight: 600;
    color: #565959;
    text-transform: uppercase;
    margin-bottom: 0;
    line-height: 1.2;
}

.so-summary-field textarea,
.so-summary-field input {
    height: 36px;
    padding: 6px 10px;
    font-size: 13px;
    border: 1px solid #D5D9D9;
    border-radius: 4px;
    resize: none;
    box-sizing: border-box;
}

.so-summary-field textarea {
    width: 250px;
    height: 36px;
}

.so-summary-field .input-group {
    width: 140px;
}

.so-summary-field .input-group-addon {
    height: 36px;
    line-height: 24px;
    vertical-align: middle;
}

.so-summary-right {
    display: flex;
    align-items: center;
    gap: 30px;
    flex-shrink: 0;
    margin-left: 20px;
}

.so-summary-stat {
    text-align: right;
}

.so-summary-stat-label {
    font-size: 11px;
    color: #565959;
    text-transform: uppercase;
    font-weight: 600;
}

.so-summary-stat-value {
    font-size: 22px;
    font-weight: 700;
    color: #0F1111;
}

.so-summary-stat-value.total {
    color: #B12704;
    font-size: 26px;
}

/* Summary toggle (up arrow) */
.so-summary-toggle {
    border: 1px solid #D5D9D9;
    background: #FFFFFF;
    border-radius: 999px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.15);
}

.so-summary-toggle:hover {
    background: #F0F2F2;
}

.so-summary-toggle svg {
    width: 14px;
    height: 14px;
    transition: transform 0.15s ease;
}

/* Collapsed footer - hides left side and rotates arrow down */
.so-summary-footer.collapsed .so-summary-left {
    display: none;
}

.so-summary-footer.collapsed {
    padding-top: 8px;
    padding-bottom: 8px;
}

.so-summary-footer.collapsed .so-summary-toggle svg {
    transform: rotate(180deg);
}

/* Responsive */
@media (max-width: 1200px) {
    .so-customer-bar {
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
}

@media (max-width: 768px) {
    .so-customer-bar {
        grid-template-columns: 1fr;
    }
    
    .so-search-section {
        flex-direction: column;
    }
    
    .so-search-wrapper {
        max-width: 100%;
    }
}

/* Hide default widget styling */
.so-container .box-solid {
    border: none;
    box-shadow: none;
    margin: 0;
    background: transparent;
}

.so-container .box-body {
    padding: 0;
}

/* Form Group Compact Override */
.so-container .form-group {
    margin-bottom: 0;
}

/* Hide elements for clean view */
.so-hide {
    display: none !important;
}

/* Hide specific columns in product table */
.so-products-table th.so-hide,
.so-products-table td.so-hide,
.so-products-table th.hide,
.so-products-table td.hide {
    display: none !important;
}

/* ========================================
   FIX: Modal z-index issue
   ======================================== */
/* Ensure modal backdrop appears behind modal dialog */
.modal-backdrop {
    z-index: 1040 !important;
}

/* Ensure modal dialog appears above backdrop */
#invoicePreviewModal {
    z-index: 1050 !important;
}

/* Ensure modal is visible when shown */
#invoicePreviewModal.in,
#invoicePreviewModal.show {
    display: block !important;
}
</style>
@endsection

    @section('content')
            @php
            $user_firstname = session()->get('user.first_name');
            $user_lastname = session()->get('user.last_name');
        @endphp
<div class="so-container">
    <!-- Header Bar – same style as Add new product / Manage Order -->
    <div class="so-header">
        <div class="so-header-content">
            <h1 class="so-header-title">
                <i class="fas fa-file-invoice"></i>
                {{ $title }}
            </h1>
            <p class="so-header-subtitle">
                Create a new sale invoice. Add customer, products, and payment details.
            </p>
        </div>
        <div class="so-header-actions">
            <button type="button" class="so-btn so-btn-secondary" id="toggle_customer_mode" title="Hide/Show Prices">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </button>
            <button type="button" id="submit-sell" class="so-btn so-btn-save">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Save
            </button>
            <button type="button" id="save-and-print" class="so-btn so-btn-print">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Save & Print
            </button>
        </div>
    </div>
    <!-- Main content -->
    <section class="content no-print">
            <input type="hidden" id="amount_rounding_method" value="{{ $pos_settings['amount_rounding_method'] ?? '' }}">
            @if (!empty($pos_settings['allow_overselling']))
                <input type="hidden" id="is_overselling_allowed">
            @endif
            @if (session('business.enable_rp') == 1)
                <input type="hidden" id="reward_point_enabled">
            @endif

            @php
                $custom_labels = json_decode(session('business.custom_labels'), true);
                $common_settings = session()->get('business.common_settings');
            $is_pay_term_required = !empty($pos_settings['is_pay_term_required']);
            @endphp
            <input type="hidden" id="item_addition_method" value="{{ $business_details->item_addition_method }}">
        
            {!! Form::open([
                'url' => action([\App\Http\Controllers\SellPosController::class, 'store']),
                'method' => 'post',
                'id' => 'add_sell_form',
                'files' => true,
            ]) !!}
            {!! Form::hidden('is_save_and_print', 0, ['id' => 'is_save_and_print']) !!}
        @if (!empty($sale_type))
            <input type="hidden" id="sale_type" name="type" value="{{ $sale_type }}">
        @endif

        <!-- Hidden fields -->
        {!! Form::hidden('location_id', !empty($default_location) ? $default_location->id : null, [
                'id' => 'location_id',
                'data-receipt_printer_type' => !empty($default_location->receipt_printer_type) ? $default_location->receipt_printer_type : 'browser',
            'data-default_payment_accounts' => !empty($default_location) ? $default_location->default_payment_accounts : '',
            ]) !!}

        @if (!empty($price_groups))
            @if (count($price_groups) > 1)
                @php reset($price_groups); $selected_price_group = !empty($default_price_group_id) && array_key_exists($default_price_group_id, $price_groups) ? $default_price_group_id : null; @endphp
                {!! Form::hidden('hidden_price_group', key($price_groups), ['id' => 'hidden_price_group']) !!}
                {!! Form::hidden('price_group', $selected_price_group, ['id' => 'price_group']) !!}
            @else
                @php reset($price_groups); @endphp
                {!! Form::hidden('price_group', key($price_groups), ['id' => 'price_group']) !!}
            @endif
                        @endif
        {!! Form::hidden('default_price_group', null, ['id' => 'default_price_group']) !!}
        
        <input type="hidden" id="default_customer_id" value="{{ $walk_in_customer['id'] }}">
        <input type="hidden" id="default_customer_name" value="{{ $walk_in_customer['name'] }}">
        <input type="hidden" id="default_customer_balance" value="{{ $walk_in_customer['balance'] ?? '' }}">
        <input type="hidden" id="default_customer_address" value="{{ $walk_in_customer['shipping_address'] ?? '' }}">
        @if (!empty($walk_in_customer['price_calculation_type']) && $walk_in_customer['price_calculation_type'] == 'selling_price_group')
            <input type="hidden" id="default_selling_price_group" value="{{ $walk_in_customer['selling_price_group_id'] ?? '' }}">
                        @endif
        <input type="hidden" id="shipping_first_name" value="{{ $walk_in_customer['shipping_first_name'] ?? '' }}" name="shipping_first_name">
        <input type="hidden" id="shipping_last_name" value="{{ $walk_in_customer['shipping_last_name'] ?? '' }}" name="shipping_last_name">
        <input type="hidden" id="shipping_company" value="{{ $walk_in_customer['shipping_company'] ?? '' }}" name="shipping_company">
        <input type="hidden" id="shipping_address1" value="{{ $walk_in_customer['shipping_address1'] ?? '' }}" name="shipping_address1">
        <input type="hidden" id="shipping_address2" value="{{ $walk_in_customer['shipping_address2'] ?? '' }}" name="shipping_address2">
        <input type="hidden" id="shipping_city" value="{{ $walk_in_customer['shipping_city'] ?? '' }}" name="shipping_city">
        <input type="hidden" id="shipping_state" value="{{ $walk_in_customer['shipping_state'] ?? '' }}" name="shipping_state">
        <input type="hidden" id="shipping_zip" value="{{ $walk_in_customer['shipping_zip'] ?? '' }}" name="shipping_zip">
        <input type="hidden" id="shipping_country" value="{{ $walk_in_customer['shipping_country'] ?? '' }}" name="shipping_country">
        <input type="text" id="cid_input" class="hide" name="cid_input">
        
        @if (!empty($status))
            <input type="hidden" name="status" id="status" value="{{ $status }}">
            @if (in_array($status, ['draft', 'quotation']))
                <input type="hidden" id="disable_qty_alert">
                        @endif
        @else
            {!! Form::hidden('status', 'final', ['id' => 'status']) !!}
        @endif
        <input type="hidden" name="final_total" id="final_total_input">
        <input type="hidden" name="discount_type" id="discount_type" value="fixed">
        <input type="hidden" name="discount_amount" id="discount_amount" value="0">
        <input type="hidden" name="tax_rate_id" id="tax_rate_id" value="">

        <!-- Customer Info Bar - Compact Single Row -->
        <div class="so-customer-bar">
            <!-- Customer Select -->
            <div class="so-field-group">
                <span class="so-field-label">Customer *</span>
                <div class="so-customer-select">
                                    {!! Form::select('contact_id', [], null, [
                                        'class' => 'form-control mousetrap',
                                        'id' => 'customer_id',
                        'placeholder' => 'Search customer...',
                                        'required',
                        'style' => 'width: 150px;'
                                    ]) !!}
                    <button type="button" class="btn add_new_customer" data-name="">
                        <i class="fa fa-plus"></i>
                    </button>
                                </div>
                <small class="text-danger hide contact_due_text"><strong>Due:</strong> <span></span></small>
                            </div>
            
            <!-- Billing Address -->
            <div class="so-field-group" style="margin-left: 20px;">
                <span class="so-field-label">Billing Address</span>
                <div class="so-address-text" id="billing_address_div" style="max-height: 50px;width: 150px; overflow: hidden;">
                    {!! $walk_in_customer['contact_address'] ?? 'Select customer' !!}
                            </div>
                        </div>

            <!-- Shipping Address -->
            <div class="so-field-group">
                <span class="so-field-label">Shipping Address</span>
                <div class="so-address-text" id="shipping_address_div">
                    {{ $walk_in_customer['supplier_business_name'] ?? '' }}
                    {{ $walk_in_customer['name'] ?? '' }}
                    {{ $walk_in_customer['shipping_address'] ?? 'Select customer' }}
                                </div>
                            </div>
            
            <!-- Pay Term -->
            <div class="so-field-group">
                <span class="so-field-label">Pay Term *</span>
                <div style="display: flex; gap: 4px;">
                    {!! Form::number('pay_term_number', $walk_in_customer['pay_term_number'], [
                                        'class' => 'form-control',
                        'min' => 0,
                        'style' => 'width: 50px; height: 32px; font-size: 12px;',
                        'required' => $is_pay_term_required,
                                    ]) !!}
                    {!! Form::select('pay_term_type', [
                        'due_on_receipt' => 'Due on Receipt',
                        'net_15' => 'Net 15',
                        'net_30' => 'Net 30',
                        'net_45' => 'Net 45',
                        'net_60' => 'Net 60',
                        'consignment' => 'Consignment',
                    ], $walk_in_customer['pay_term_type'], [
                                        'class' => 'form-control',
                        'style' => 'width: 90px; height: 32px; font-size: 11px;',
                        'required' => $is_pay_term_required,
                                    ]) !!}
                                </div>
                            </div>
            
            <!-- Sale Date -->
            <div class="so-field-group">
                <span class="so-field-label">Sale Date *</span>
                <div class="input-group" style="width: 150px;">
                    <span class="input-group-addon" style="padding: 4px 8px;">
                        <i class="fa fa-calendar" style="font-size: 12px;"></i>
                    </span>
                    {!! Form::text('transaction_date', $default_datetime, ['class' => 'form-control', 'readonly', 'required', 'style' => 'height: 32px; font-size: 12px;']) !!}
                                </div>
                            </div>
            
            <!-- Order Type (visible only for Sales Order) -->
            <div class="so-field-group @if(($sale_type ?? '') != 'sales_order') so-hide @endif">
                <span class="so-field-label">{{ __('sale.order_type') }} *</span>
                {!! Form::select(
                    'order_type',
                    [
                        'shipping' => __('sale.shipping'),
                        'pickup' => 'Pickup',
                    ],
                    (($sale_type ?? '') == 'sales_order') ? 'shipping' : 'pickup',
                    [
                        'class' => 'form-control',
                        'id' => 'order_type',
                        'style' => 'height: 32px; font-size: 12px; min-width: 95px; width: 100%;',
                    ]
                ) !!}
            </div>
            
            <!-- Sales Rep -->
            <div class="so-field-group">
                <span class="so-field-label">Sales Rep</span>
                <div class="so-sales-rep">{{ $user_firstname }} {{ $user_lastname }}</div>
                                </div>
                            </div>

        <!-- Product Search Section - PROMINENT -->
        <div class="so-search-section">
            <div class="so-search-wrapper">
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal" data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i class="fas fa-search-plus"></i></button>
                                </div>
                    {!! Form::text('search_product', null, [
                        'class' => 'form-control so-search-input',
                        'id' => 'search_product',
                        'placeholder' => __('lang_v1.search_product_placeholder'),
                        'disabled' => is_null($default_location) ? true : false,
                        'autofocus' => is_null($default_location) ? false : true,
                                    ]) !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat so-search-btn" style="background: #ffbb00; border: none; color: #ffffff;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 18px; height: 18px;">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </button>
                    </span>
                            </div>
                        </div>

            <div class="so-search-options">
                <label class="so-matrix-toggle" id="matrix_toggle_label">
                    <div class="so-toggle-switch" id="toggle_switch_display"></div>
                    <span>Enable Matrix</span>
                    <input type="checkbox" style="display: none;" id="toggle_switch">
                </label>
                
                <div class="so-quick-actions">
                    <button type="button" class="so-quick-btn pos_add_quick_product" 
                                data-href="{{ action([\App\Http\Controllers\ProductController::class, 'quickAdd']) }}"
                        data-container=".quick_add_product_modal"
                        title="Add New Product"
                        style="display: none;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Quick Add
                            </button>
                        </div>
                            <div class="so-quick-actions">
                    <button type="button" class="hidden" id="showButton" title="Toggle Search">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="3" y1="9" x2="21" y2="9"></line>
                        </svg>
                        More
                            </button>
                    @if (count($business_locations) > 1)
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        {!! Form::select('select_location_id', $business_locations, $default_location->id ?? null, [
                            'class' => 'form-control',
                            'id' => 'select_location_id',
                            'style' => 'height: 32px; font-size: 12px; width: 150px; border-radius: 4px;'
                        ], $bl_attributes) !!}
                        </div>  
                    @endif
                    </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="so-products-section">
            <div class="so-products-table-wrapper">
                @php
                    $hide_tax = session()->get('business.enable_inline_tax') == 0 ? 'so-hide' : '';
                            @endphp
                <input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{ $business_details->sell_price_tax }}">
                <input type="hidden" id="product_row_count" value="0">
                
                <table class="so-products-table" id="pos_table">
                    <thead>
                        <tr>
                            <th class="so-hide" style="width: 30px;">#</th>
                            <th class="product-col" style="text-align: left;">Product</th>
                            <th style="width: 100px;">Qty Available</th>
                            <th style="width: 100px;">Quantity</th>
                                            @if (!empty($pos_settings['inline_service_staff']))
                                <th class="so-hide" style="width: 100px;">Staff</th>
                                            @endif
                            <th style="width: 100px;">Unit Price</th>
                            <th style="width: 100px;">Discount</th>
                            <th class="so-hide {{ $hide_tax }}" style="width: 60px;">Tax</th>
                            <th class="so-hide {{ $hide_tax }}" style="width: 90px;">Price+Tax</th>
                                            @if (!empty($common_settings['enable_product_warranty']))
                                <th class="so-hide" style="width: 80px;">Warranty</th>
                                            @endif
                            <th style="width: 100px;">Tax Per Unit</th>
                            <th style="width: 100px;">Subtotal</th>
                            <th style="width: 60px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        <!-- Product rows will be added here dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

        <!-- Summary Footer - STICKY -->
        <div class="so-summary-footer" id="table_footer">
            <div class="so-summary-left">
                <div class="so-summary-field">
                    <label>Sales Notes</label>
                    {!! Form::textarea('sale_note', null, ['class' => 'form-control', 'rows' => 1, 'style' => 'width: 250px; height: 36px; resize: none;']) !!}
                        </div>
                <div class="so-summary-field" id="shipping_charges_section">
                    <label>Shipping Charges</label>
                    <div class="input-group" style="width: 140px; display: flex; align-items: center;">
                        <span class="input-group-addon" style="padding: 6px 10px; height: 36px; line-height: 36px; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ccc;"><i class="fa fa-dollar-sign"></i></span>
                        {!! Form::text('shipping_charges', null, [
                            'id' => 'shipping_charges',
                            'class' => 'form-control input_number',
                            'min' => 0,
                            'style' => 'height: 36px;',
                            'placeholder' => '0.00'
                        ]) !!}
                    </div>
                </div>
                <div class="so-summary-field" style="margin-left: 0;">
                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; margin-bottom: 0; padding-top: 28px;">
                        {!! Form::checkbox('ex_taxes', 'wi', false, ['id' => 'ex_taxes_checkbox', 'style' => 'width: 16px; height: 16px; margin: 0;']) !!}
                        <span style="font-size: 12px; color: #565959; font-weight: 600; text-transform: uppercase;">Exempt Tax</span>
                    </label>
                            </div>
                            </div>
            <div class="so-summary-right">
                <div class="so-summary-stat">
                    <div class="so-summary-stat-label">Items</div>
                    <div class="so-summary-stat-value"><span class="total_quantity">0</span></div>
                            </div>
                <div class="so-summary-stat">
                    <div class="so-summary-stat-label">Total</div>
                    <div class="so-summary-stat-value total">$<span class="price_total">0.00</span></div>
                        </div>
                                </div>
                            </div>
        
        <!-- Hidden section for other fields that may be needed -->
        <div style="display: none;">
            @if (in_array('types_of_service', $enabled_modules ?? []) && !empty($types_of_service))
                {!! Form::select('types_of_service_id', $types_of_service, null, ['id' => 'types_of_service_id']) !!}
                {!! Form::hidden('types_of_service_price_group', null, ['id' => 'types_of_service_price_group']) !!}
                        @endif
            
            @if (in_array('subscription', $enabled_modules ?? []))
                {!! Form::checkbox('is_recurring', 1, false, ['id' => 'is_recurring']) !!}
                        @endif
            
            @if (!empty($commission_agent))
                {!! Form::select('commission_agent', $commission_agent, null, ['id' => 'commission_agent']) !!}
                        @endif
            
            @can('edit_invoice_number')
                {!! Form::text('invoice_no', null, ['id' => 'invoice_no']) !!}
                @endcan

            @if ($sale_type != 'sales_order')
                {!! Form::select('invoice_scheme_id', $invoice_schemes, $default_invoice_schemes->id ?? null, ['id' => 'invoice_scheme_id']) !!}
            @endif
            
            <!-- Keep pos_add_quick_product button for JS functionality -->
            <button type="button" class="pos_add_quick_product"
                data-href="{{ action([\App\Http\Controllers\ProductController::class, 'quickAdd']) }}"
                data-container=".quick_add_product_modal">
            </button>
                            </div>
        
            {!! Form::close() !!}
        </section>
</div>

<!-- Modals -->
        <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
            @include('contact.create', ['quick_add' => true])
        </div>
<div class="modal fade register_details_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade" id="expense_modal" tabindex="-1" role="dialog"></div>

{{-- Invoice preview modal (used when clicking Save / Save & Print) --}}
<div class="modal fade" id="invoicePreviewModal" tabindex="-1" role="dialog"
    aria-labelledby="invoicePreviewModalLabel">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header" style="padding: 5px 10px;">
                        <div class="tw-flex tw-justify-between">
                            <h4 class="modal-title tw-flex" style="align-items: center" id="modalTitle">Invoice Preview</h4>
                            <div class="tw-flex tw-justify-end tw-gap-5">
                        <button type="button"
                            class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print"
                            id="confirm_invoice_submit">Confirm</button>
                        <button type="button"
                            class="tw-dw-btn tw-dw-btn-danger tw-text-white no-print"
                            data-dismiss="modal" id="invoicePreviewModalLabel"
                            style="background-color: #ff0019; border-color: #dc3545;">
                            @lang('messages.close')
                        </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Scrollable Table -->
                        <div class="tw-overflow-x-auto"
                            style="max-height:60vh; min-height:60vh; overflow-y: auto;">
                            <table class="tw-w-full tw-border tw-text-center mb-0"
                                id="preview_invoice_table">
                                        <thead style="position: sticky; top: 0; z-index: 9; background-color: #fff;">
                                            <tr>
                                        <th class="tw-p-2 tw-border">S.No</th>
                                        <th class="tw-p-2 tw-border">@lang('sale.product')</th>
                                        <th class="tw-p-2 tw-border">@lang('sale.qty')</th>
                                        <th class="tw-p-2 tw-border">Recalled Price</th>
                                        <th class="tw-p-2 tw-border">@lang('sale.unit_price') Ex Tax</th>
                                        <th class="tw-p-2 tw-border customer-mode-hide">
                                            @lang('sale.discount')</th>
                                        <th class="tw-p-2 tw-border">Discounted Price Ex Tax</th>
                                        <th class="tw-p-2 tw-border customer-mode-hide">Cost Price Ex Tax
                                        </th>
                                        <th class="tw-p-2 tw-border customer-mode-hide">
                                            Profit/Loss Per Unit
                                            @show_tooltip('Red Text indicates Loss, Green Text indicates Profit')
                                        </th>
                                                <th class="tw-p-2 tw-border customer-mode-hide">Tax Rate</th>
                                                <th class="tw-p-2 tw-border customer-mode-hide">Price Inc Tax</th>
                                        <th class="tw-p-2 tw-border customer-mode-hide">Cost Total Ex Tax
                                        </th>
                                        <th class="tw-p-2 tw-border customer-mode-hide">Total Price Ex Tax
                                        </th>
                                        <th class="tw-p-2 tw-border customer-mode-hide">
                                            Profit/Loss Total
                                            @show_tooltip('Red Text indicates Loss, Green Text indicates Profit')
                                        </th>
                                        <th class="tw-p-2 tw-border">Subtotal Inc Tax</th>
                                            </tr>
                                        </thead>
                                        <tbody id="preview_invoice_table_body">
                                            <!-- Dynamic content -->
                                        </tbody>
                                    </table>
                                </div>
        
                                <!-- Totals Table aligned right -->
                                <div class="tw-mt-3 table-responsive" style="max-width: 400px; float: right;">
                                    <table class="table" style="border: none">
                                <tbody>
                                    <tr>
                                        <td style="border:none;" class="text-right"><strong>Items:</strong>
                                        </td>
                                        <td style="border:none;"><span> </span><span
                                                id="preview_total_quantity">0.00</span></td>
                                            </tr>
                                             <tr class="customer-mode-hide">
                                        <td style="border:none;" class="text-right">
                                            <strong>@lang('sale.discount'):</strong>
                                        </td>
                                        <td style="border:none;"><span> </span><span
                                                id="preview_discount_amount">0.00</span></td>
                                            </tr>
                                            <tr class="customer-mode-hide">
                                        <td style="border:none;" class="text-right">
                                            <strong>@lang('sale.tax'):</strong>
                                        </td>
                                        <td style="border:none;"><span> </span><span
                                                id="preview_tax_amount">0.00</span></td>
                                            </tr>
                                            <tr>
                                        <td style="border:none;" class="text-right">
                                            <strong>@lang('sale.total'):</strong>
                                        </td>
                                        <td style="border:none;"><span> </span><span
                                                id="preview_total_amount">0.00</span></td>
                                            </tr>
                                            <tr class="customer-mode-hide">
                                        <td style="border:none;" class="text-right"><strong>Shipping
                                                Charges:</strong></td>
                                        <td style="border:none;"><span> </span><span
                                                id="preview_shipping_total">0.00</span></td>
                                            </tr>
                                            <tr class="customer-mode-hide">
                                        <td style="border:none;" class="text-right">
                                            <strong>@lang('sale.total_payable'):</strong>
                                        </td>
                                        <td style="border:none;"><span> </span><span
                                                id="preview_final_total">0.00</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="clear: both;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</div>

@include('sale_pos.partials.configure_search_modal')
@include('sale_pos.partials.recent_transactions_modal')
@if (in_array('subscription', $enabled_modules ?? []))
    @include('sale_pos.partials.recurring_invoice_modal')
@endif

    @stop

@section('javascript')
    <script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
@if (in_array('tables', $enabled_modules ?? []) || in_array('modifiers', $enabled_modules ?? []) || in_array('service_staff', $enabled_modules ?? []))
        <script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <script type="text/javascript">
        $(document).ready(function() {

            // Re-enable Save / Save & Print buttons when preview modal is closed
             $('#invoicePreviewModal').on('hidden.bs.modal', function () {
            $('button#submit-sell').prop('disabled', false);
            $('button#save-and-print').prop('disabled', false);
        });
    // Matrix toggle functionality
    $('#matrix_toggle_label').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var toggleSwitch = $('#toggle_switch_display');
        var checkbox = $('#toggle_switch');
        
        var isActive = toggleSwitch.hasClass('active');
        
        if (isActive) {
            toggleSwitch.removeClass('active');
            checkbox.prop('checked', false);
                } else {
            toggleSwitch.addClass('active');
            checkbox.prop('checked', true);
        }
        
        // Trigger change event so any listeners are notified
        checkbox.trigger('change');
    });
    
    // Also handle direct clicks on the toggle switch
    $('#toggle_switch_display').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#matrix_toggle_label').trigger('click');
    });

    // Status change handler
            $('#status').change(function() {
                if ($(this).val() == 'final') {
                    $('#payment_rows_div').removeClass('hide');
                } else {
                    $('#payment_rows_div').addClass('hide');
                }
            });

    // Shipping type handler
    $('#order_type').on('change', function() {
        if ($(this).val() === 'shipping') {
            $('#shipping_charges_section').show();
        } else {
            $('#shipping_charges_section').hide();
        }
    });

    // Initialize shipping section visibility based on default order type
    $('#order_type').trigger('change');

    // Summary footer toggle (up arrow button)
    $('#summary_toggle_btn').on('click', function() {
        $('#table_footer').toggleClass('collapsed');
    });

    // Ensure subtotal updates when quantity changes (handles both change and input events)
    $('table#pos_table tbody').on('input keyup change blur', 'input.pos_quantity', function() {
        let tr = $(this).closest('tr');
        let input = $(this);
        // Read quantity value - remove any formatting and convert to number
        let rawValue = input.val().toString().replace(/[^0-9.]/g, '');
        let quantity = parseFloat(rawValue) || 0;
        
        // Ensure quantity is at least 1 if it's a required field
        if (quantity <= 0) {
            quantity = 1;
            input.val(1);
        } else {
            // Ensure the input shows the clean numeric value
            input.val(quantity);
        }

        // If stock is tracked for this product, show a toast when entered
        // quantity exceeds available stock.
        // 1) Prefer explicit data-qty_available on the input.
        let qtyAvailable = parseFloat(input.data('qty_available'));
        // 2) Fallback: derive from the "Qty Available" table cell (3rd column)
        if (isNaN(qtyAvailable)) {
            let qtyCellText = tr.find('td').eq(2).text().toString().replace(/[^0-9.\-]/g, '');
            qtyAvailable = parseFloat(qtyCellText);
        }
        let allowOverselling = (input.data('allow-overselling') || '').toString() === 'true';
        let saleType = $('#sale_type').length ? $('#sale_type').val() : null;
        let isSalesOrder = saleType === 'sales_order';

        if (!isNaN(qtyAvailable) && quantity > qtyAvailable) {
            // Prefer the per-field validation message when available
            let msg =
                input.data('msg_max_default') ||
                (typeof LANG !== 'undefined' && LANG.out_of_stock) ||
                'Entered quantity exceeds available stock.';

            toastr.warning(msg);

            // For both regular sales and sales orders, enforce available stock
            // when overselling is not allowed.
            if (!allowOverselling) {
                quantity = qtyAvailable > 0 ? qtyAvailable : 0;
                input.val(quantity);
            }
        }
        
        // Manually calculate subtotal to ensure correct multiplication
        let unitPrice = 0;
        let unitPriceInput = tr.find('input.pos_unit_price');
        if (unitPriceInput.length) {
            let unitPriceVal = unitPriceInput.val().toString().replace(/[^0-9.]/g, '');
            unitPrice = parseFloat(unitPriceVal) || 0;
        }
        
        // Read discount
        let discountAmount = 0;
        let discountType = 'fixed';
        let discountInput = tr.find('input.row_discount_amount');
        let discountSelect = tr.find('select.row_discount_type');
        if (discountInput.length) {
            let discountVal = discountInput.val().toString().replace(/[^0-9.]/g, '');
            discountAmount = parseFloat(discountVal) || 0;
        }
        if (discountSelect.length) {
            discountType = discountSelect.val() || 'fixed';
        }
        
        // Calculate discounted unit price
        let discountedUnitPrice = unitPrice;
        if (discountAmount > 0) {
            if (discountType === 'fixed') {
                discountedUnitPrice = unitPrice - discountAmount;
            } else {
                discountedUnitPrice = unitPrice - (unitPrice * discountAmount / 100);
            }
        }
        
        // Read tax per unit
        let taxPerUnit = 0;
        let taxInput = tr.find('input.pos_taxation_total');
        if (taxInput.length) {
            let taxVal = taxInput.val().toString().replace(/[^0-9.]/g, '');
            taxPerUnit = parseFloat(taxVal) || 0;
        }
        
        // Calculate line total: (discounted_unit_price + tax_per_unit) * quantity
        let lineTotal = (discountedUnitPrice + taxPerUnit) * quantity;
        
        // Update the subtotal display immediately
        let lineTotalInput = tr.find('input.pos_line_total');
        let lineTotalSpan = tr.find('span.pos_line_total_text');
        if (lineTotalInput.length) {
            lineTotalInput.val(lineTotal.toFixed(2));
        }
        if (lineTotalSpan.length) {
            lineTotalSpan.text('$ ' + lineTotal.toFixed(2));
        }
        
        // Also call pos_each_row to ensure everything is synchronized
        setTimeout(function() {
            if (typeof pos_each_row === 'function') {
                pos_each_row(tr);
            }
            
            // Ensure total row is also updated after calculation
            if (typeof pos_total_row === 'function') {
                setTimeout(function() {
                    pos_total_row();
                }, 150);
            }
        }, 50);
    });

    // Handle quantity up/down buttons to ensure subtotal updates
    $(document).on('click', 'table#pos_table tbody .quantity-up, table#pos_table tbody .quantity-down', function(e) {
        e.stopPropagation();
        let tr = $(this).closest('tr');
        let input = tr.find('input.pos_quantity');
        
        // Wait for the common.js handler to update the value first, then recalculate
        setTimeout(function() {
            // Read the updated quantity value
            let rawValue = input.val().toString().replace(/[^0-9.]/g, '');
            let quantity = parseFloat(rawValue) || 1;
            
            // Force recalculate using pos_each_row which will read quantity and recalculate subtotal
            if (typeof pos_each_row === 'function') {
                pos_each_row(tr);
            }
            
            // Also update total row after calculation completes
            if (typeof pos_total_row === 'function') {
                setTimeout(function() {
                    pos_total_row();
                }, 200);
            }
        }, 150);
    });

    // Ensure total row is calculated on page load and when rows are added
    $(document).ready(function() {
        // Calculate totals after a short delay to ensure all rows are loaded
        setTimeout(function() {
            if (typeof pos_total_row === 'function') {
                pos_total_row();
            }
        }, 500);
    });

    // Ensure subtotal updates when discount changes
    $('table#pos_table tbody').on('input keyup change', 'input.row_discount_amount, select.row_discount_type', function() {
        let tr = $(this).closest('tr');
        if (typeof pos_each_row === 'function') {
            pos_each_row(tr);
        }
        // Also trigger total row update to ensure footer subtotal is updated
        if (typeof pos_total_row === 'function') {
            setTimeout(function() {
                pos_total_row();
            }, 100);
        }
    });

    // Ensure subtotal updates when unit price changes
    $('table#pos_table tbody').on('input keyup change', 'input.pos_unit_price', function() {
        let tr = $(this).closest('tr');
        if (typeof pos_each_row === 'function') {
            pos_each_row(tr);
        }
        // Also trigger total row update to ensure footer subtotal is updated
        if (typeof pos_total_row === 'function') {
            setTimeout(function() {
                pos_total_row();
            }, 100);
        }
    });

        // Observer to detect when new rows are added, validate stock, and recalculate totals
    if (typeof MutationObserver !== 'undefined') {
        const tableBody = document.querySelector('table#pos_table tbody');
        if (tableBody) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length > 0) {
                        // Row was added; validate its quantity and recalc totals after a short delay
                        setTimeout(function() {
                            let $newRows = $(mutation.addedNodes).filter('tr');
                            $newRows.each(function() {
                                let $qtyInput = $(this).find('input.pos_quantity');
                                if ($qtyInput.length) {
                                    // Trigger the existing handler so it shows the warning toast
                                    // when qty_available is 0 or less than the default quantity.
                                    $qtyInput.trigger('change');
                                }
                            });

                            if (typeof pos_total_row === 'function') {
                                pos_total_row();
                            }
                        }, 300);
                    }
                });
            });
            
            observer.observe(tableBody, {
                childList: true,
                subtree: false
            });
        }
    }
        });
    </script>
@endsection
