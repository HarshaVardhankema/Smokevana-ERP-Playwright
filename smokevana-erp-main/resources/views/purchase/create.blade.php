@extends('layouts.app')
@section('title', __('purchase.add_purchase'))

@section('css')
<style>
/* ========================================
   AMAZON THEME - PURCHASE RECEIPT CREATE
   ======================================== */

.so-container {
    padding: 0;
    margin: 0;
    background: #EAEDED;
    min-height: 100vh;
}

/* Header Bar - Amazon style (matches other pages) */
.so-header {
    background: #37475a;
    border-radius: 10px;
    padding: 22px 28px;
    margin-bottom: 1px;
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
    color: #FFFFFF;
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.so-header-title svg,
.so-header-title i {
    color: #ffffff !important;
}

.so-header-subtitle {
    font-size: 13px;
    color: rgba(249, 250, 251, 0.88);
    margin: 4px 0 0 0;
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

.so-btn-secondary {
    background: linear-gradient(180deg, #F7FAFA 0%, #E3E6E6 100%);
    color: #0F1111;
    border: 1px solid #D5D9D9;
}

.so-btn-secondary:hover {
    background: linear-gradient(180deg, #EDEDED 0%, #D5D9D9 100%);
}

/* Supplier Info Bar - Compact Single Row */
.so-customer-bar {
    background: #FFFFFF;
    padding: 10px 20px;
    border-bottom: 1px solid #D5D9D9;
    display: grid;
    grid-template-columns: 180px minmax(200px, 1fr) minmax(200px, 1fr) 150px 160px minmax(220px, 1fr);
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
    background: #FF9900;
    border: none;
    color: #fff;
}

/* Ensure business location select shows full text */
#location_id {
    min-width: 220px;
    width: 100%;
}

.so-address-text {
    font-size: 11px;
    color: #565959;
    line-height: 1.4;
    max-height: 50px;
    overflow: hidden;
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
    border: 2px solid #FF9900 !important;
    border-radius: 8px !important;
    background: #FFFFFF !important;
    color: #0F1111 !important;
    outline: none !important;
    transition: all 0.2s ease !important;
}

.so-search-input:focus {
    border-color: #FF9900 !important;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.2) !important;
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
    background: linear-gradient(180deg, #FFD814 0%, #FF9900 100%);
    border: none;
    border-radius: 0 8px 8px 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.so-search-btn:hover {
    background: linear-gradient(180deg, #FFE033 0%, #FFB020 100%);
}

.so-search-btn svg {
    width: 20px;
    height: 20px;
    color: #0F1111;
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
    background: #FF9900;
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
    background: linear-gradient(180deg, #37475A 0%, #232F3E 100%);
    color: #FFFFFF;
    padding: 8px 6px;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    text-align: center;
    border-bottom: 2px solid #FF9900;
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

.so-products-table .product-col {
    text-align: left;
    min-width: 300px;
    max-width: 400px;
}

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
    border-color: #FF9900;
    outline: none;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.2);
}

.so-products-table select {
    height: 28px;
    padding: 2px 4px;
    font-size: 11px;
    border: 1px solid #D5D9D9;
    border-radius: 4px;
    background: #FFF;
}

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
    border-top: 2px solid #FF9900;
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

.so-container .form-group {
    margin-bottom: 0;
}

.so-hide {
    display: none !important;
}

.so-products-table th.so-hide,
.so-products-table td.so-hide,
.so-products-table th.hide,
.so-products-table td.hide {
    display: none !important;
}
</style>
@endsection

@section('content')

@php
	$custom_labels = json_decode(session('business.custom_labels'), true);
	$user_firstname = session()->get('user.first_name');
	$user_lastname = session()->get('user.last_name');
@endphp

<div class="so-container">
	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

	@include('layouts.partials.error')

	{!! Form::open(['url' => action([\App\Http\Controllers\PurchaseController::class, 'store']), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
	
	<!-- Header Bar - Amazon style -->
	<div class="so-header">
		<div class="so-header-content">
			<h1 class="so-header-title">
				<i class="fas fa-file-invoice"></i>
				@lang('purchase.add_purchase')
				<i 
					class="fa fa-keyboard-o"
					style="margin-left: 8px; cursor: pointer; opacity: 0.9;"
					aria-hidden="true"
					data-container="body"
					data-toggle="popover"
					data-placement="bottom"
					data-content="@include('purchase.partials.keyboard_shortcuts_details')"
					data-html="true"
					data-trigger="hover"
					title=""
				></i>
			</h1>
			<p class="so-header-subtitle">Add new purchase receipt. Select vendor, add products, and save.</p>
		</div>
		<div class="so-header-actions">
			<button type="button" id="submit_purchase_form" class="so-btn so-btn-save">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
					<polyline points="17 21 17 13 7 13 7 21"></polyline>
					<polyline points="7 3 7 8 15 8"></polyline>
				</svg>
				@lang('messages.save')
			</button>
		</div>
		@include('sale_pos.partials.configure_search_modal')
	</div>
	<!-- Supplier Info Bar - Compact Single Row -->
	<div class="so-customer-bar">
		<!-- Supplier Select -->
		<div class="so-field-group">
			<span class="so-field-label">@lang('purchase.supplier') *</span>
			<div class="so-customer-select">
				{!! Form::select('contact_id', [], null, [
					'class' => 'form-control mousetrap',
					'id' => 'supplier_id',
					'placeholder' => __('messages.please_select'),
					'required',
					'style' => 'width: 150px;'
				]) !!}
				<a class="btn btn-modal"
					data-href="{{ action([\App\Http\Controllers\ContactController::class, 'create'], ['type' => 'supplier']) }}"
					data-container=".contact_modal">
					<i class="fa fa-plus"></i>
				</a>
			</div>
		</div>
		
		<!-- Address -->
		<div class="so-field-group" style="margin-left: 20px;">
			<span class="so-field-label">@lang('business.address')</span>
			<div class="so-address-text" id="supplier_address_div" style="max-height: 50px;width: 150px; overflow: hidden;">
				Select supplier
			</div>
		</div>

		<!-- Reference No -->
		<div class="so-field-group">
			<span class="so-field-label">@lang('purchase.ref_no')</span>
			<div style="display: flex; align-items: center; gap: 4px;">
				{!! Form::text('ref_no', null, ['class' => 'form-control', 'style' => 'height: 32px; font-size: 12px;', 'placeholder' => __('lang_v1.leave_empty_to_autogenerate')]); !!}
				@show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
			</div>
		</div>
		
		<!-- Purchase Order -->
		@if(!empty($common_settings['enable_purchase_order']))
		<div class="so-field-group">
			<span class="so-field-label">@lang('lang_v1.purchase_order')</span>
			{!! Form::select('purchase_order_ids[]', [], null, ['class' => 'form-control select2', 'multiple', 'id' => 'purchase_order_ids', 'style' => 'height: 32px; font-size: 12px;']); !!}
		</div>
		@endif
		
		<!-- Purchase Date -->
		<div class="so-field-group">
			<span class="so-field-label">@lang('purchase.purchase_date') *</span>
			<div class="input-group" style="width: 150px;">
				<span class="input-group-addon" style="padding: 4px 8px;">
					<i class="fa fa-calendar" style="font-size: 12px;"></i>
				</span>
				{!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required', 'style' => 'height: 32px; font-size: 12px;']) !!}
			</div>
		</div>
		
		<!-- Business Location -->
		@if(count($business_locations) == 1)
			@php 
				$default_location = current(array_keys($business_locations->toArray()));
				$search_disable = false; 
			@endphp
		@else
			@php $default_location = null;
			$search_disable = true;
			@endphp
		@endif
		<div class="so-field-group @if(count($business_locations) == 1) hide @endif">
			<span class="so-field-label">@lang('purchase.business_location') *</span>
			<div style="display: flex; align-items: center; gap: 4px;">
				{!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'height: 32px; font-size: 12px;'], $bl_attributes); !!}
				@show_tooltip(__('tooltip.purchase_location'))
			</div>
		</div>
		
		<!-- Pay Term -->
		<div class="so-field-group">
			<span class="so-field-label">@lang('contact.pay_term')</span>
			<div style="display: flex; gap: 4px;">
				{!! Form::number('pay_term_number', null, [
					'class' => 'form-control',
					'min' => 0,
					'style' => 'width: 50px; height: 32px; font-size: 12px;',
					'placeholder' => __('contact.pay_term')
				]) !!}
				{!! Form::select('pay_term_type', 
					['months' => __('lang_v1.months'), 
						'days' => __('lang_v1.days')], 
						null, 
					['class' => 'form-control', 'style' => 'width: 90px; height: 32px; font-size: 11px;', 'id' => 'pay_term_type', 'placeholder' => __('messages.please_select')]); !!}
			</div>
		</div>
		
		<!-- Document Upload -->
		<div class="so-field-group">
			<span class="so-field-label">@lang('purchase.attach_document')</span>
			{!! Form::file('document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))), 'style' => 'font-size: 11px;']); !!}
			<label id="upload_document-error" class="error" for="upload_document"></label>
		</div>
		
		<!-- Combine Button -->
		<div class="so-field-group" style="display: flex; align-items: flex-end;">
			<button type="button" class="btn btn-default bg-white btn-flat" id="combine_button" title="Combine Rows" data-toggle="tooltip" style="height: 32px; width: 32px; padding: 0; border-radius: 4px; border: 1px solid #D5D9D9;">
				<i class="fa fa-object-group text-primary" aria-hidden="true"></i>
			</button>
		</div>
	</div>
	
	<!-- Hidden Status Field -->
	<div class="hide @if(!empty($default_purchase_status)) hide @endif">
		@php
			$orderStatuses =[
				'received'=>'Received'
			]
		@endphp
		{!! Form::select('status', $orderStatuses, 
		'received',
		 ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
	</div>
	
	<!-- Currency Exchange Rate (Hidden if not needed) -->
	<div class="hide @if(!$currency_details->purchase_in_diff_currency) hide @endif">
		{!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
		@show_tooltip(__('tooltip.currency_exchange_factor'))
		<div class="input-group">
			<span class="input-group-addon">
				<i class="fa fa-info"></i>
			</span>
			{!! Form::number('exchange_rate', $currency_details->p_exchange_rate, ['class' => 'form-control', 'required', 'step' => 0.001]); !!}
		</div>
		<span class="help-block text-danger">
			@lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
		</span>
	</div>
	<!-- Custom Fields (Hidden section, can be shown if needed) -->
	<div class="hide">
		@php
		    $custom_field_1_label = !empty($custom_labels['purchase']['custom_field_1']) ? $custom_labels['purchase']['custom_field_1'] : '';
		    $is_custom_field_1_required = !empty($custom_labels['purchase']['is_custom_field_1_required']) && $custom_labels['purchase']['is_custom_field_1_required'] == 1 ? true : false;
		    $custom_field_2_label = !empty($custom_labels['purchase']['custom_field_2']) ? $custom_labels['purchase']['custom_field_2'] : '';
		    $is_custom_field_2_required = !empty($custom_labels['purchase']['is_custom_field_2_required']) && $custom_labels['purchase']['is_custom_field_2_required'] == 1 ? true : false;
		    $custom_field_3_label = !empty($custom_labels['purchase']['custom_field_3']) ? $custom_labels['purchase']['custom_field_3'] : '';
		    $is_custom_field_3_required = !empty($custom_labels['purchase']['is_custom_field_3_required']) && $custom_labels['purchase']['is_custom_field_3_required'] == 1 ? true : false;
		    $custom_field_4_label = !empty($custom_labels['purchase']['custom_field_4']) ? $custom_labels['purchase']['custom_field_4'] : '';
		    $is_custom_field_4_required = !empty($custom_labels['purchase']['is_custom_field_4_required']) && $custom_labels['purchase']['is_custom_field_4_required'] == 1 ? true : false;
		@endphp
		@if(!empty($custom_field_1_label))
			@php
				$label_1 = $custom_field_1_label . ':';
				if($is_custom_field_1_required) {
					$label_1 .= '*';
				}
			@endphp
			{!! Form::label('custom_field_1', $label_1 ) !!}
			{!! Form::text('custom_field_1', null, ['class' => 'form-control','placeholder' => $custom_field_1_label, 'required' => $is_custom_field_1_required]); !!}
		@endif
		@if(!empty($custom_field_2_label))
			@php
				$label_2 = $custom_field_2_label . ':';
				if($is_custom_field_2_required) {
					$label_2 .= '*';
				}
			@endphp
			{!! Form::label('custom_field_2', $label_2 ) !!}
			{!! Form::text('custom_field_2', null, ['class' => 'form-control','placeholder' => $custom_field_2_label, 'required' => $is_custom_field_2_required]); !!}
		@endif
		@if(!empty($custom_field_3_label))
			@php
				$label_3 = $custom_field_3_label . ':';
				if($is_custom_field_3_required) {
					$label_3 .= '*';
				}
			@endphp
			{!! Form::label('custom_field_3', $label_3 ) !!}
			{!! Form::text('custom_field_3', null, ['class' => 'form-control','placeholder' => $custom_field_3_label, 'required' => $is_custom_field_3_required]); !!}
		@endif
		@if(!empty($custom_field_4_label))
			@php
				$label_4 = $custom_field_4_label . ':';
				if($is_custom_field_4_required) {
					$label_4 .= '*';
				}
			@endphp
			{!! Form::label('custom_field_4', $label_4 ) !!}
			{!! Form::text('custom_field_4', null, ['class' => 'form-control','placeholder' => $custom_field_4_label, 'required' => $is_custom_field_4_required]); !!}
		@endif
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
					'disabled' => $search_disable,
					'autofocus' => !$search_disable,
				]) !!}
				<span class="input-group-btn">
					<button type="button" class="btn btn-default bg-white btn-flat so-search-btn" style="background: linear-gradient(180deg, #FFD814 0%, #FF9900 100%); border: none; color: #0F1111;">
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
				<span>Enable Metrix</span>
				<input type="checkbox" style="display: none;" id="toggle_switch">
			</label>
			
			<div class="so-quick-actions">
				<a href="{{action([\App\Http\Controllers\ProductController::class, 'create'])}}" target="_blank" class="so-quick-btn">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<line x1="12" y1="5" x2="12" y2="19"></line>
						<line x1="5" y1="12" x2="19" y2="12"></line>
					</svg>
					Add Product
				</a>
			</div>
		</div>
	</div>

	<!-- Products Section -->
	<div class="so-products-section">
		<div class="so-products-table-wrapper">
			@php
				$hide_tax = session()->get('business.enable_inline_tax') == 0 ? 'so-hide' : '';
			@endphp
			<input type="hidden" id="row_count" value="0">
			
			<table class="so-products-table" id="purchase_entry_table">
				<thead>
					<tr>
						<th class="so-hide" style="width: 30px;">#</th>
						<th class="product-col" style="text-align: left;">@lang( 'product.product_name' )</th>
						<th style="width: 100px;">@lang( 'report.current_stock' )</th>
						<th style="width: 100px;">Ordered Qty</th>
						<th style="width: 100px;">@lang( 'purchase.purchase_quantity' )</th>
						<th style="width: 100px;">@lang( 'lang_v1.unit_cost' )</th>
						<th style="width: 100px;">@lang( 'lang_v1.discount_percent' )</th>
						<th class="so-hide {{ $hide_tax }}" style="width: 90px;">@lang( 'purchase.subtotal_before_tax' )</th>
						<th class="so-hide {{ $hide_tax }}" style="width: 90px;">@lang( 'purchase.product_tax' )</th>
						<th class="so-hide {{ $hide_tax }}" style="width: 90px;">@lang( 'purchase.net_cost' )</th>
						<th style="width: 100px;">@lang( 'purchase.line_total' )</th>
						@if(session('business.enable_lot_number'))
							<th style="width: 80px;">@lang('lang_v1.lot_number')</th>
						@endif
						@if(session('business.enable_product_expiry'))
							<th style="width: 120px;">@lang('product.mfg_date') / @lang('product.exp_date')</th>
						@endif
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
			<div class="so-summary-field hide">
				{!! Form::label('shipping_details', __( 'purchase.shipping_details' ) . ':') !!}
				{!! Form::text('shipping_details', null, ['class' => 'form-control', 'style' => 'width: 250px; height: 36px;']) !!}
			</div>
			<div class="so-summary-field">
				<label>@lang('purchase.additional_shipping_charges')</label>
				<div class="input-group" style="width: 140px; display: flex; align-items: center;">
					<span class="input-group-addon" style="padding: 6px 10px; height: 36px; line-height: 36px; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ccc;"><i class="fa fa-dollar-sign"></i></span>
					{!! Form::text('shipping_charges', 0, [
						'class' => 'form-control input_number',
						'min' => 0,
						'required',
						'style' => 'height: 36px;'
					]) !!}
				</div>
			</div>
			<div class="so-summary-field">
				<label>@lang('purchase.supplier_ref_no')</label>
				{!! Form::text('supplier_ref_no', null, ['class' => 'form-control', 'title' => __('If supplier Provide Ref No'), 'style' => 'width: 200px; height: 36px;']) !!}
			</div>
			<div class="so-summary-field">
				<label>Memo</label>
				{!! Form::textarea('additional_notes', null, ['class' => 'form-control additional_notes', 'rows' => 1, 'style' => 'width: 250px; height: 36px; resize: none;']) !!}
			</div>
		</div>
		<div class="so-summary-right">
			<div class="so-summary-stat">
				<div class="so-summary-stat-label">@lang( 'lang_v1.total_items' )</div>
				<div class="so-summary-stat-value"><span id="total_quantity" class="display_currency" data-currency_symbol="false">0</span></div>
			</div>
			<div class="so-summary-stat">
				<div class="so-summary-stat-label">@lang( 'purchase.net_total_amount' )</div>
				<div class="so-summary-stat-value total"><span id="total_subtotal" class="display_currency">0.00</span></div>
			</div>
		</div>
	</div>

	<!-- Discount Section (Hidden, can be shown if needed) -->
	<div class="hide" style="background: #FFFFFF; padding: 12px 20px; border-bottom: 1px solid #D5D9D9;">
		<div style="display: flex; gap: 20px; align-items: flex-end;">
			<div style="display: flex; flex-direction: column; gap: 4px;">
				<label style="font-size: 11px; font-weight: 600; color: #565959; text-transform: uppercase;">@lang( 'purchase.discount_type' )</label>
				{!! Form::select('discount_type', [ 'fixed' => __( 'lang_v1.fixed' ), 'percentage' => __( 'lang_v1.percentage' )], '', ['class' => 'form-control select2', 'style' => 'height: 36px; font-size: 13px;']); !!}
			</div>
			<div style="display: flex; flex-direction: column; gap: 4px;">
				<label style="font-size: 11px; font-weight: 600; color: #565959; text-transform: uppercase;">@lang( 'purchase.discount_amount' )</label>
				{!! Form::text('discount_amount', 0, ['class' => 'form-control input_number', 'required','min'=>0, 'style' => 'height: 36px; font-size: 13px;']); !!}
			</div>
			<div style="display: flex; flex-direction: column; gap: 4px;">
				<label style="font-size: 11px; font-weight: 600; color: #565959; text-transform: uppercase;">@lang( 'purchase.discount' )</label>
				<div style="font-size: 13px; font-weight: 600; color: #0F1111; padding-top: 8px;">
					(-) <span id="discount_calculated_amount" class="display_currency">0</span>
				</div>
			</div>
		</div>
		<div class="table-responsive" style="margin-top: 12px;">
			<table class="table table-condensed table-bordered table-striped" id="purchase_order_discount_table">
				<thead>
					<tr>
						<th>Purchase Order ID</th>
						<th>Discount Type</th>
						<th>Discount Value</th>
						<th>Final Discount</th>
					</tr>
				</thead>
				<tbody>	
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3"></td>
						<td>
							<b>@lang( 'purchase.discount' ):</b>(-) 
							<span id="purchase_order_discount_calculated_amount" class="display_currency" data-final-value="0">$ 0</span>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	
	<!-- Hidden Tax Fields -->
	<div class="hide">
		{!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}
		<select name="tax_id" id="tax_id" class="form-control select2" placeholder="'Please Select'">
			<option value="" data-tax_amount="0" data-tax_type="fixed" selected>@lang('lang_v1.none')</option>
			@foreach($taxes as $tax)
				<option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" data-tax_type="{{ $tax->calculation_type }}">{{ $tax->name }}</option>
			@endforeach
		</select>
		{!! Form::hidden('tax_amount', 0, ['id' => 'tax_amount']); !!}
		<b>@lang( 'purchase.purchase_tax' ):</b>(+) 
		<span id="tax_calculated_amount" class="display_currency">0</span>
	</div>
	
	<!-- Hidden totals for calculations -->
	<input type="hidden" id="total_subtotal_input" value="0" name="total_before_tax">
	<input type="hidden" id="st_before_tax_input" value="0">
	<!-- Hidden section for other fields that may be needed -->
	<div style="display: none;">
		@php
		    $shipping_custom_label_1 = !empty($custom_labels['purchase_shipping']['custom_field_1']) ? $custom_labels['purchase_shipping']['custom_field_1'] : '';
		    $is_shipping_custom_field_1_required = !empty($custom_labels['purchase_shipping']['is_custom_field_1_required']) && $custom_labels['purchase_shipping']['is_custom_field_1_required'] == 1 ? true : false;
		    $shipping_custom_label_2 = !empty($custom_labels['purchase_shipping']['custom_field_2']) ? $custom_labels['purchase_shipping']['custom_field_2'] : '';
		    $is_shipping_custom_field_2_required = !empty($custom_labels['purchase_shipping']['is_custom_field_2_required']) && $custom_labels['purchase_shipping']['is_custom_field_2_required'] == 1 ? true : false;
		    $shipping_custom_label_3 = !empty($custom_labels['purchase_shipping']['custom_field_3']) ? $custom_labels['purchase_shipping']['custom_field_3'] : '';
		    $is_shipping_custom_field_3_required = !empty($custom_labels['purchase_shipping']['is_custom_field_3_required']) && $custom_labels['purchase_shipping']['is_custom_field_3_required'] == 1 ? true : false;
		    $shipping_custom_label_4 = !empty($custom_labels['purchase_shipping']['custom_field_4']) ? $custom_labels['purchase_shipping']['custom_field_4'] : '';
		    $is_shipping_custom_field_4_required = !empty($custom_labels['purchase_shipping']['is_custom_field_4_required']) && $custom_labels['purchase_shipping']['is_custom_field_4_required'] == 1 ? true : false;
		    $shipping_custom_label_5 = !empty($custom_labels['purchase_shipping']['custom_field_5']) ? $custom_labels['purchase_shipping']['custom_field_5'] : '';
		    $is_shipping_custom_field_5_required = !empty($custom_labels['purchase_shipping']['is_custom_field_5_required']) && $custom_labels['purchase_shipping']['is_custom_field_5_required'] == 1 ? true : false;
		@endphp

		@if(!empty($shipping_custom_label_1))
			@php
				$label_1 = $shipping_custom_label_1 . ':';
				if($is_shipping_custom_field_1_required) {
					$label_1 .= '*';
				}
			@endphp
			{!! Form::label('shipping_custom_field_1', $label_1 ) !!}
			{!! Form::text('shipping_custom_field_1', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_1, 'required' => $is_shipping_custom_field_1_required]); !!}
		@endif
		@if(!empty($shipping_custom_label_2))
			@php
				$label_2 = $shipping_custom_label_2 . ':';
				if($is_shipping_custom_field_2_required) {
					$label_2 .= '*';
				}
			@endphp
			{!! Form::label('shipping_custom_field_2', $label_2 ) !!}
			{!! Form::text('shipping_custom_field_2', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_2, 'required' => $is_shipping_custom_field_2_required]); !!}
		@endif
		@if(!empty($shipping_custom_label_3))
			@php
				$label_3 = $shipping_custom_label_3 . ':';
				if($is_shipping_custom_field_3_required) {
					$label_3 .= '*';
				}
			@endphp
			{!! Form::label('shipping_custom_field_3', $label_3 ) !!}
			{!! Form::text('shipping_custom_field_3', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_3, 'required' => $is_shipping_custom_field_3_required]); !!}
		@endif
		@if(!empty($shipping_custom_label_4))
			@php
				$label_4 = $shipping_custom_label_4 . ':';
				if($is_shipping_custom_field_4_required) {
					$label_4 .= '*';
				}
			@endphp
			{!! Form::label('shipping_custom_field_4', $label_4 ) !!}
			{!! Form::text('shipping_custom_field_4', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_4, 'required' => $is_shipping_custom_field_4_required]); !!}
		@endif
		@if(!empty($shipping_custom_label_5))
			@php
				$label_5 = $shipping_custom_label_5 . ':';
				if($is_shipping_custom_field_5_required) {
					$label_5 .= '*';
				}
			@endphp
			{!! Form::label('shipping_custom_field_5', $label_5 ) !!}
			{!! Form::text('shipping_custom_field_5', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_5, 'required' => $is_shipping_custom_field_5_required]); !!}
		@endif
		
		<button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" id="toggle_additional_expense"> <i class="fas fa-plus"></i> @lang('lang_v1.add_additional_expenses') <i class="fas fa-chevron-down"></i></button>
		<div class="col-md-8 col-md-offset-4" id="additional_expenses_div" style="display: none;">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th>@lang('lang_v1.additional_expense_name')</th>
						<th>@lang('sale.amount')</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							{!! Form::text('additional_expense_key_1', null, ['class' => 'form-control', 'id' => 'additional_expense_key_1']); !!}
						</td>
						<td>
							{!! Form::text('additional_expense_value_1', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_1']); !!}
						</td>
					</tr>
					<tr>
						<td>
							{!! Form::text('additional_expense_key_2', null, ['class' => 'form-control', 'id' => 'additional_expense_key_2']); !!}
						</td>
						<td>
							{!! Form::text('additional_expense_value_2', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_2']); !!}
						</td>
					</tr>
					<tr>
						<td>
							{!! Form::text('additional_expense_key_3', null, ['class' => 'form-control', 'id' => 'additional_expense_key_3']); !!}
						</td>
						<td>
							{!! Form::text('additional_expense_value_3', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_3']); !!}
						</td>
					</tr>
					<tr>
						<td>
							{!! Form::text('additional_expense_key_4', null, ['class' => 'form-control', 'id' => 'additional_expense_key_4']); !!}
						</td>
						<td>
							{!! Form::text('additional_expense_value_4', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_4']); !!}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		{!! Form::hidden('final_total', 0 , ['id' => 'grand_total_hidden']); !!}
		<span id="grand_total" class="display_currency" data-currency_symbol='true'>0</span>
		
		<!-- Payment section (hidden) -->
		<div class="box-body payment_row hide">
			<div class="row">
				<div class="col-md-12">
					<strong>@lang('lang_v1.advance_balance'):</strong> <span id="advance_balance_text">0</span>
					{!! Form::hidden('advance_balance', null, ['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!}
				</div>
			</div>
			@include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'show_date' => true, 'show_denomination' => true])
			<hr>
			<div class="row">
				<div class="col-sm-12">
					<div class="pull-right"><strong>@lang('purchase.payment_due'):</strong> <span id="payment_due">0.00</span></div>
				</div>
			</div>
		</div>
	</div>

	{!! Form::close() !!}
</div>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>

@include('purchase.partials.import_purchase_products_modal')
<!-- /.content -->
@endsection

@section('javascript')
	<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$(document).ready( function(){
			__page_leave_confirmation('#add_purchase_form');
			$('.paid_on').datetimepicker({
				format: moment_date_format + ' ' + moment_time_format,
				ignoreReadonly: true,
			});

			if($('.payment_types_dropdown').length){
				$('.payment_types_dropdown').change();
			}
			set_payment_type_dropdown();
			$('select#location_id').change(function() {
				set_payment_type_dropdown();
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
			
		});
		
		$(document).on('change', '.payment_types_dropdown, #location_id', function(e) {
			var default_accounts = $('select#location_id').length ? 
				$('select#location_id')
				.find(':selected')
				.data('default_payment_accounts') : [];
			var payment_types_dropdown = $('.payment_types_dropdown');
			var payment_type = payment_types_dropdown.val();
			var payment_row = payment_types_dropdown.closest('.payment_row');
			var row_index = payment_row.find('.payment_row_index').val();

			var account_dropdown = payment_row.find('select#account_' + row_index);
			if (payment_type && payment_type != 'advance') {
				var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
					default_accounts[payment_type]['account'] : '';
				if (account_dropdown.length && default_accounts) {
					account_dropdown.val(default_account);
					account_dropdown.change();
				}
			}

			if (payment_type == 'advance') {
				if (account_dropdown) {
					account_dropdown.prop('disabled', true);
					account_dropdown.closest('.form-group').addClass('hide');
				}
			} else {
				if (account_dropdown) {
					account_dropdown.prop('disabled', false); 
					account_dropdown.closest('.form-group').removeClass('hide');
				}    
			}
		});

		function set_payment_type_dropdown() {
			var payment_settings = $('#location_id').find(':selected').data('default_payment_accounts');
			payment_settings = payment_settings ? payment_settings : [];
			enabled_payment_types = [];
			for (var key in payment_settings) {
				if (payment_settings[key] && payment_settings[key]['is_enabled']) {
					enabled_payment_types.push(key);
				}
			}
			if (enabled_payment_types.length) {
				$(".payment_types_dropdown > option").each(function() {
					//skip if advance
					if ($(this).val() && $(this).val() != 'advance') {
						if (enabled_payment_types.indexOf($(this).val()) != -1) {
							$(this).removeClass('hide');
						} else {
							$(this).addClass('hide');
						}
					}
				});
			}
		}
	</script>
	@include('purchase.partials.keyboard_shortcuts')
@endsection
