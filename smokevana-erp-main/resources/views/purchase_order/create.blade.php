@extends('layouts.app')
@section('title', __('lang_v1.add_purchase_order'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
#purchase_entry_table.po-compact .po-compact-hide {
    display: none;
}

#purchase_entry_table.po-compact {
    width: 100%;
}

.purchase-order-table {
    overflow-x: auto;
    overflow-y: visible;
}

/* Amazon theme for Purchase Order Create page */
.purchase-order-create-page {
    background: #EAEDED;
    padding: 0;
    min-height: 100vh;
}

/* Amazon-style banner */
.po-create-header-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    border-radius: 0 0 10px 10px;
    padding: 22px 28px;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative;
    overflow: hidden;
}

.po-create-header-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: #ff9900;
    z-index: 1;
}

.po-create-header-banner .banner-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.po-create-header-banner .banner-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 24px;
    font-weight: 700;
    margin: 0;
    color: #fff !important;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.po-create-header-banner .banner-title i {
    color: #ff9900;
    font-size: 26px;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
}

.po-create-header-banner .banner-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

/* Header buttons - Square shape */
#submit_purchase_form {
    background: #FF9900 !important;
    border-color: #e47911 !important;
    color: #FFFFFF !important;
    border-radius: 6px !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 4px rgba(255, 153, 0, 0.3) !important;
    padding: 8px 20px !important;
}

#submit_purchase_form:hover {
    background: #ffac33 !important;
    border-color: #ff9900 !important;
    box-shadow: 0 3px 8px rgba(255, 153, 0, 0.4) !important;
    transform: translateY(-1px) !important;
}

#submit_purchase_draft {
    background: #28a745 !important;
    border-color: #218838 !important;
    color: #FFFFFF !important;
    border-radius: 6px !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3) !important;
    padding: 8px 20px !important;
}

#submit_purchase_draft:hover {
    background: #34ce57 !important;
    border-color: #28a745 !important;
    box-shadow: 0 3px 8px rgba(40, 167, 69, 0.4) !important;
    transform: translateY(-1px) !important;
}

#closeBtn {
    background: #dc3545 !important;
    border-color: #c82333 !important;
    color: #FFFFFF !important;
    border-radius: 6px !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3) !important;
    padding: 8px 20px !important;
}

#closeBtn:hover {
    background: #e4606d !important;
    border-color: #dc3545 !important;
    box-shadow: 0 3px 8px rgba(220, 53, 69, 0.4) !important;
    transform: translateY(-1px) !important;
}

/* Card styling - Amazon white cards */
.purchase-order-create-page .box-solid,
.purchase-order-create-page .box-primary {
    background: #FFFFFF !important;
    border: 1px solid #D5D9D9 !important;
    border-radius: 12px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
    margin-bottom: 20px;
    padding: 20px;
}

.purchase-order-create-page .box-solid .box-header,
.purchase-order-create-page .box-primary .box-header {
    background: transparent !important;
    border-bottom: 1px solid #E7E7E7 !important;
    padding: 12px 0 16px 0 !important;
    margin-bottom: 16px;
}

.purchase-order-create-page .box-solid .box-title,
.purchase-order-create-page .box-primary .box-title {
    color: #232f3e !important;
    font-weight: 600 !important;
    font-size: 16px !important;
}

/* Product Search Section - Amazon styled */
.purchase-order-create-page .product-search-section {
    background: #F7F8F8 !important;
    border: 1px solid #D5D9D9 !important;
    border-radius: 8px !important;
    padding: 16px !important;
    margin-bottom: 20px;
}

.purchase-order-create-page .product-search-section label {
    color: #232f3e !important;
    font-weight: 600 !important;
}

.purchase-order-create-page .product-search-section label i {
    color: #ff9900 !important;
    margin-right: 6px;
}

/* Add new product button - Amazon Orange, Square */
.btn-primary[href*="product"][href*="create"],
a.btn-primary[href*="product"][href*="create"],
.btn[href*="product"][href*="create"] {
    background: #FF9900 !important;
    border-color: #e47911 !important;
    color: #FFFFFF !important;
    border-radius: 6px !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 4px rgba(255, 153, 0, 0.3) !important;
}

.btn-primary[href*="product"][href*="create"]:hover,
a.btn-primary[href*="product"][href*="create"]:hover,
.btn[href*="product"][href*="create"]:hover {
    background: #ffac33 !important;
    border-color: #ff9900 !important;
    color: #FFFFFF !important;
    box-shadow: 0 3px 8px rgba(255, 153, 0, 0.4) !important;
    transform: translateY(-1px) !important;
}

/* Table styling - Amazon theme */
.purchase-order-create-page #purchase_entry_table thead th {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #FFFFFF !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    font-size: 12px !important;
    padding: 12px 8px !important;
    border: none !important;
    text-align: center !important;
}

.purchase-order-create-page #purchase_entry_table thead th:first-child {
    border-top-left-radius: 8px;
}

.purchase-order-create-page #purchase_entry_table thead th:last-child {
    border-top-right-radius: 8px;
}

.purchase-order-create-page #purchase_entry_table tbody td {
    background: #FFFFFF !important;
    border-color: #E7E7E7 !important;
    padding: 10px 8px !important;
}

.purchase-order-create-page #purchase_entry_table tbody tr:hover {
    background: #F7F8F8 !important;
}

/* Input fields styling */
.purchase-order-create-page .form-control {
    border-color: #D5D9D9 !important;
    border-radius: 4px !important;
}

.purchase-order-create-page .form-control:focus {
    border-color: #ff9900 !important;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.2) !important;
}

/* Dropdown items use the global amazon-theme.css base styles for consistency */

/* Content wrapper */
.purchase-order-create-content {
    padding: 0 28px 40px;
}

/* Table wrapper styling */
.purchase-order-create-page .table-responsive {
    background: #FFFFFF !important;
    border-radius: 8px !important;
    overflow: hidden;
    border: 1px solid #D5D9D9 !important;
}

/* Summary section styling */
.purchase-order-create-page .pull-right table {
    background: #F7F8F8 !important;
    padding: 16px !important;
    border-radius: 8px !important;
    border: 1px solid #D5D9D9 !important;
}

.purchase-order-create-page .pull-right table th {
    color: #232f3e !important;
    font-weight: 600 !important;
    padding: 8px 12px !important;
}

.purchase-order-create-page .pull-right table td {
    color: #232f3e !important;
    padding: 8px 12px !important;
    font-weight: 600 !important;
}

/* Input group addon styling */
.purchase-order-create-page .input-group-addon {
    background: #F7F8F8 !important;
    border-color: #D5D9D9 !important;
    color: #232f3e !important;
}

/* Label styling */
.purchase-order-create-page label {
    color: #232f3e !important;
    font-weight: 500 !important;
}

/* Warning text styling */
.purchase-order-create-page .text-warning {
    color: #ff9900 !important;
    font-weight: 500 !important;
}

/* Select2 dropdown styling */
.purchase-order-create-page .select2-container--default .select2-selection--single {
    border-color: #D5D9D9 !important;
    border-radius: 4px !important;
}

.purchase-order-create-page .select2-container--default .select2-selection--single:focus,
.purchase-order-create-page .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #ff9900 !important;
}

/* Button group styling */
.purchase-order-create-page .input-group-btn .btn {
    border-radius: 4px !important;
}

.purchase-order-create-page .input-group-btn .btn:hover {
    background: #F7F8F8 !important;
    border-color: #ff9900 !important;
    color: #ff9900 !important;
}
</style>
@endsection

@section('content')

<div class="purchase-order-create-page">
<!-- Amazon-style banner -->
<section class="content-header no-print">
    <div class="po-create-header-banner">
        <div class="banner-content">
            <h1 class="banner-title">
                <i class="fas fa-file-invoice"></i> @lang('lang_v1.add_purchase_order')
            </h1>
            <div class="banner-actions">
                <button type="button" id="submit_purchase_form" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-md tw-text-white">@lang('messages.save')</button>
                <button type="button" id="submit_purchase_draft" class="tw-dw-btn tw-dw-btn-md tw-text-white tw-dw-btn-success">Save As Draft</button>
                <a href="/purchase-order"><button type="button" id="closeBtn" class="tw-dw-btn tw-dw-btn-danger tw-dw-btn-md tw-text-white">Close</button></a>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content purchase-order-create-content">

	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

	@include('layouts.partials.error')

	{!! Form::open(['url' => action([\App\Http\Controllers\PurchaseOrderController::class, 'store']), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
	@include('sale_pos.partials.configure_search_modal')

	@component('components.widget', ['class' => 'box-solid', 'style' => 'z-index:999; position:relative;'])
		<input type="hidden" id="is_purchase_order">
		<div class="row">
			<div class="@if(!empty($default_purchase_status)) col-sm-2 @else col-sm-2 @endif">
				<div class="form-group">
					{!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-user"></i>
						</span>
						{!! Form::select('contact_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier_id']); !!}
						<span class="input-group-btn">
							<a class="btn btn-default bg-white btn-flat btn-modal"
                              data-href="{{ action([\App\Http\Controllers\ContactController::class, 'create'], ['type' => 'supplier']) }}"
                             data-container=".contact_modal">
							 <i class="fa fa-plus-circle text-primary fa-lg"></i>
                            </a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-2">
				<strong>
				@lang('business.address'):
			</strong>
			<div id="supplier_address_div"></div>
		</div>
			<div class="@if(!empty($default_purchase_status)) col-sm-2 @else col-sm-2 @endif">
				<div class="form-group">
					{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
					{!! Form::text('ref_no', null, ['class' => 'form-control']); !!}
				</div>
			</div>
			<div class="@if(!empty($default_purchase_status)) col-sm-2 @else col-sm-2 @endif ">
				<div class="form-group">
					{!! Form::label('transaction_date', __('lang_v1.order_date') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'required']); !!}
					</div>
				</div>
				
			</div>

			<div class="@if(!empty($default_purchase_status)) col-md-2 @else col-md-2 @endif">
				<div class="form-group ">
					{!! Form::label('delivery_date', __('lang_v1.delivery_date') . ':') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! Form::text('delivery_date', null, ['class' => 'form-control']); !!}
					</div>
				</div>
			</div>
			<div class="col-md-1-5 ">
		          <div class="form-group">
		            <div class="multi-input">
		              {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
		              <br/>
		              {!! Form::number('pay_term_number', null, ['class' => 'form-control width-40 pull-left', 'min' => '0', 'placeholder' => __('contact.pay_term')]); !!}

		              {!! Form::select('pay_term_type', 
		              	['months' => __('lang_v1.months'), 
		              		'days' => __('lang_v1.days')], 
		              		null, 
		              	['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'), 'id' => 'pay_term_type']); !!}
		            </div>
		        </div>
		    </div>
			<div class="col-md-0-5 tw-mt-6">
				<button type="button" class="btn btn-default bg-white btn-flat"
                id="combine_button" title="Combine Rows" data-toggle="tooltip"
                style="padding: 0.15rem 0.35rem; border-radius: 0; border: 1px solid #ddd;">
                <i class="fa fa-object-group text-primary " aria-hidden="true"></i>
               </button>
			</div>
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
			<div class="col-md-2 @if(count($business_locations) == 1) hide @endif">
				<div class="form-group">
					{!! Form::label('location_id', __('purchase.business_location').':*') !!}
					@show_tooltip(__('tooltip.purchase_location'))
					{!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
				</div>
			</div>

			<!-- Currency Exchange Rate -->
			<div class="col-sm-2 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
				<div class="form-group">
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
			</div>

			<div class="col-sm-2 hide">
                <div class="form-group">
                    {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                    {!! Form::file('document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
					<label id="upload_document-error" class="error" for="upload_document"></label>
                    {{-- <p class="help-block">
                    	@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                    	@includeIf('components.document_help_text')
                    </p> --}}
                </div>
            </div>
		</div>
		@if(!empty($common_settings['enable_purchase_requisition']))
		<div class="row">
			<div class="col-sm-1">
				<div class="form-group">
					{!! Form::label('purchase_requisition_ids', __('lang_v1.purchase_requisition').':') !!}
					{!! Form::select('purchase_requisition_ids[]', [], null, ['class' => 'form-control select2', 'multiple', 'id' => 'purchase_requisition_ids']); !!}
				</div>
			</div>
		</div>
		@endif
	@endcomponent

	@component('components.widget', ['class' => 'box-solid'])
		{{-- <div class="row">
			
			
		</div> --}}
		@php
			$hide_tax = '';
			if( session()->get('business.enable_inline_tax') == 0){
				$hide_tax = 'hide';
			}
		@endphp
		
		<!-- Product Search Section - PROMINENT -->
		<div class="row product-search-section">
			<div class="col-md-8">
				<div class="form-group" style="margin-bottom: 0;">
					<label style="font-weight: 600; color: #232f3e; margin-bottom: 8px; display: block;">
						<i class="fas fa-search"></i> Search & Add Products
					</label>
					<div class="input-group">
						<span class="input-group-btn">
							<button type="button" class="btn btn-default bg-white btn-flat"
								data-toggle="modal" data-target="#configure_search_modal"
								title="{{ __('lang_v1.configure_product_search') }}">
								<i class="fas fa-search-plus"></i>
							</button>
						</span>
						{!! Form::text('search_product', null, ['class' => 'form-control mousetrap ui-autocomplete-input', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'disabled' => $search_disable, 'style' => 'font-size: 14px; padding: 10px;']); !!}
						<span class="input-group-btn">
							<a href="{{action([\App\Http\Controllers\ProductController::class, 'create'])}}" target="_blank" class="btn btn-primary" style="border-radius: 6px;">
								<i class="fa fa-plus"></i> @lang('product.add_new_product')
							</a>
						</span>
					</div>
					@if($search_disable)
						<small class="text-warning" style="display: block; margin-top: 5px;"><i class="fas fa-exclamation-triangle"></i> Please select a Business Location above to enable product search</small>
					@endif
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group" style="margin-bottom: 0;">
					<label style="font-weight: 600; color: #333; margin-bottom: 8px; display: block;">&nbsp;</label>
					<label style="display: flex; align-items: center; cursor: pointer; margin-top: 5px;">
						<input type="checkbox" style="display: none;" id="toggle_switch"
							onchange="this.nextElementSibling.style.backgroundColor = this.checked ? '#4CAF50' : '#ccc'; 
							  this.nextElementSibling.firstElementChild.style.transform = this.checked ? 'translateX(20px)' : 'translateX(0)';">
						<div style="width: 40px; height: 20px; background-color: #ccc; border-radius: 20px; position: relative; transition: background-color 0.3s; margin-right: 10px;">
							<div style="width: 18px; height: 18px; background-color: white; border-radius: 50%; position: absolute; top: 1px; left: 1px; transition: transform 0.3s;"></div>
						</div>
						<span style="font-size: 12px; font-weight: 500; color: #333;">Enable Metrix</span>
					</label>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="table-responsive purchase-order-table">
					<table class="table table-condensed table-bordered text-center table-striped po-compact" id="purchase_entry_table" data-hide-tax="{{ $hide_tax }}">
						<thead>
							<tr>
								<th>#</th>
								<th>@lang( 'product.product_name' )</th>
								<th>@lang('report.current_stock')</th>
								<th>Order Qty</th>
								<th>@lang( 'lang_v1.unit_cost' )</th>
								<th>Discount</th>
								<th >Final Cost</th>
								<th class="{{$hide_tax}} po-compact-hide">@lang( 'purchase.subtotal_before_tax' )</th>
								<th class="{{$hide_tax}} po-compact-hide">@lang( 'purchase.product_tax' )</th>
								<th class="{{$hide_tax}} po-compact-hide">@lang( 'purchase.net_cost' )</th>
								<th>@lang( 'purchase.line_total' )</th>
								<th class="hide po-compact-hide">
									@lang( 'lang_v1.profit_margin' )
								</th>
								<th style="width: 2%"><i class="fa fa-trash" aria-hidden="true"></i></th>
							</tr>
						</thead>
						<tbody ></tbody>
					</table>
				</div>
				
				<hr/>
				<div class='tw-flex tw-justify-between'>
					<div class='tw-flex tw-gap-6'>
						<div class="form-group ">
							{!!Form::label('Total Discount')!!}
							<div class="input-group" style="display: flex">
							{!! Form::text('discount_amount', 0, ['class' => 'form-control input_number', 'required','min'=>0,'id'=>'discount_amount']); !!}
							{!! Form::select('discount_type', [ 'fixed' => '$', 'percentage' => '%'], '', ['class' => 'form-control select2',"style"=>"max-width: 70px; border-top-left-radius: 0; border-bottom-left-radius: 0;",'id'=>'discount_type']); !!}
							</div>
						</div>
						
						<div class="form-group ">
							{!!Form::label('shipping_charges', __('sale.shipping_charges'))!!}
							<div class="input-group">
							<span class="input-group-addon">
							<i class="fa fa-dollar-sign"></i>
							</span>
							{!!Form::text('shipping_charges',@num_format(0.00),['class'=>'form-control input_number','placeholder'=> __('sale.shipping_charges') ,'min'=>0,'required']);!!}
							</div>
						</div>
						<div class="form-group">
							{!!Form::label('Memo')!!}
							<div class="input-group">
							</span>
							{!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 1]); !!}
							</div>
						</div>
					</div>
					<div>
						<div class="pull-right ">
							<table class="pull-right col-md-12">
								<tr>
									<th class=" text-right">@lang( 'lang_v1.total_items' ):</th>
									<td class=" text-right">
										<span id="total_quantity" class="display_currency" data-currency_symbol="false" ></span>
									</td>
								</tr>
								<tr class="hide">
									<th class="text-right">@lang( 'purchase.total_before_tax' ):</th>
									<td class=" text-right">
										<span id="total_st_before_tax" class="display_currency"></span>
										<input type="hidden" id="st_before_tax_input" value=0>
									</td>
								</tr>
								<tr>
									<th class="text-right">@lang( 'purchase.net_total_amount' ):</th>
									<td class=" text-left">
										<span id="total_subtotal" class="display_currency"></span>
										<!-- This is total before purchase tax-->
										<input type="hidden" id="total_subtotal_input" value=0  name="total_before_tax">
									</td>
								</tr>
							</table>
						</div>
		
						<input type="hidden" id="row_count" value="0">
					</div>
				</div>
				
			</div>
		</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-solid hide'])
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
	            {!! Form::label('shipping_details', __('sale.shipping_details')) !!}
	            {!! Form::textarea('shipping_details',null, ['class' => 'form-control','placeholder' => __('sale.shipping_details') ,'rows' => '3', 'cols'=>'30']); !!}
	        </div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
	            {!! Form::label('shipping_address', __('lang_v1.shipping_address')) !!}
	            {!! Form::textarea('shipping_address',null, ['class' => 'form-control','placeholder' => __('lang_v1.shipping_address') ,'rows' => '3', 'cols'=>'30']); !!}
	        </div>
		</div>
		<div class="col-md-4">
			{{-- <div class="form-group">
				{!!Form::label('shipping_charges', __('sale.shipping_charges'))!!}
				<div class="input-group">
				<span class="input-group-addon">
				<i class="fa fa-info"></i>
				</span>
				{!!Form::text('shipping_charges',@num_format(0.00),['class'=>'form-control input_number','placeholder'=> __('sale.shipping_charges')]);!!}
				</div> --}}
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="col-md-4">
			<div class="form-group">
	            {!! Form::label('shipping_status', __('lang_v1.shipping_status')) !!}
	            {!! Form::select('shipping_status',$shipping_statuses, null, ['class' => 'form-control','placeholder' => __('messages.please_select')]); !!}
	        </div>
		</div>
		<div class="col-md-4">
	        <div class="form-group">
	            {!! Form::label('delivered_to', __('lang_v1.delivered_to') . ':' ) !!}
	            {!! Form::text('delivered_to', null, ['class' => 'form-control','placeholder' => __('lang_v1.delivered_to')]); !!}
	        </div>
	    </div>
	    @php
	    	$custom_labels = json_decode(session('business.custom_labels'), true);
	        $shipping_custom_label_1 = !empty($custom_labels['shipping']['custom_field_1']) ? $custom_labels['shipping']['custom_field_1'] : '';

	        $is_shipping_custom_field_1_required = !empty($custom_labels['shipping']['is_custom_field_1_required']) && $custom_labels['shipping']['is_custom_field_1_required'] == 1 ? true : false;

	        $shipping_custom_label_2 = !empty($custom_labels['shipping']['custom_field_2']) ? $custom_labels['shipping']['custom_field_2'] : '';

	        $is_shipping_custom_field_2_required = !empty($custom_labels['shipping']['is_custom_field_2_required']) && $custom_labels['shipping']['is_custom_field_2_required'] == 1 ? true : false;

	        $shipping_custom_label_3 = !empty($custom_labels['shipping']['custom_field_3']) ? $custom_labels['shipping']['custom_field_3'] : '';
	        
	        $is_shipping_custom_field_3_required = !empty($custom_labels['shipping']['is_custom_field_3_required']) && $custom_labels['shipping']['is_custom_field_3_required'] == 1 ? true : false;

	        $shipping_custom_label_4 = !empty($custom_labels['shipping']['custom_field_4']) ? $custom_labels['shipping']['custom_field_4'] : '';
	        
	        $is_shipping_custom_field_4_required = !empty($custom_labels['shipping']['is_custom_field_4_required']) && $custom_labels['shipping']['is_custom_field_4_required'] == 1 ? true : false;

	        $shipping_custom_label_5 = !empty($custom_labels['shipping']['custom_field_5']) ? $custom_labels['shipping']['custom_field_5'] : '';
	        
	        $is_shipping_custom_field_5_required = !empty($custom_labels['shipping']['is_custom_field_5_required']) && $custom_labels['shipping']['is_custom_field_5_required'] == 1 ? true : false;
        @endphp

        @if(!empty($shipping_custom_label_1))
        	@php
        		$label_1 = $shipping_custom_label_1 . ':';
        		if($is_shipping_custom_field_1_required) {
        			$label_1 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('shipping_custom_field_1', $label_1 ) !!}
		            {!! Form::text('shipping_custom_field_1', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_1, 'required' => $is_shipping_custom_field_1_required]); !!}
		        </div>
		    </div>
        @endif
        @if(!empty($shipping_custom_label_2))
        	@php
        		$label_2 = $shipping_custom_label_2 . ':';
        		if($is_shipping_custom_field_2_required) {
        			$label_2 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('shipping_custom_field_2', $label_2 ) !!}
		            {!! Form::text('shipping_custom_field_2', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_2, 'required' => $is_shipping_custom_field_2_required]); !!}
		        </div>
		    </div>
        @endif
        @if(!empty($shipping_custom_label_3))
        	@php
        		$label_3 = $shipping_custom_label_3 . ':';
        		if($is_shipping_custom_field_3_required) {
        			$label_3 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('shipping_custom_field_3', $label_3 ) !!}
		            {!! Form::text('shipping_custom_field_3', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_3, 'required' => $is_shipping_custom_field_3_required]); !!}
		        </div>
		    </div>
        @endif
        @if(!empty($shipping_custom_label_4))
        	@php
        		$label_4 = $shipping_custom_label_4 . ':';
        		if($is_shipping_custom_field_4_required) {
        			$label_4 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('shipping_custom_field_4', $label_4 ) !!}
		            {!! Form::text('shipping_custom_field_4', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_4, 'required' => $is_shipping_custom_field_4_required]); !!}
		        </div>
		    </div>
        @endif
        @if(!empty($shipping_custom_label_5))
        	@php
        		$label_5 = $shipping_custom_label_5 . ':';
        		if($is_shipping_custom_field_5_required) {
        			$label_5 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('shipping_custom_field_5', $label_5 ) !!}
		            {!! Form::text('shipping_custom_field_5', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_5, 'required' => $is_shipping_custom_field_5_required]); !!}
		        </div>
		    </div>
        @endif
        <div class="col-md-4 ">
            <div class="form-group">
                {!! Form::label('shipping_documents', __('lang_v1.shipping_documents') . ':') !!}
                {!! Form::file('shipping_documents[]', ['id' => 'shipping_documents', 'multiple', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                {{-- <p class="help-block">
                	@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                	@includeIf('components.document_help_text')
                </p> --}}
            </div>
        </div>        
	</div>
	<div class="row">
			<div class="col-md-12 text-center">
				<button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" id="toggle_additional_expense"> <i class="fas fa-plus"></i> @lang('lang_v1.add_additional_expenses') <i class="fas fa-chevron-down"></i></button>
			</div>
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
								{!! Form::text('additional_expense_key_1', null, ['class' => 'form-control']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_1', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_1']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_2', null, ['class' => 'form-control']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_2', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_2']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_3', null, ['class' => 'form-control']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_3', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_3']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_4', null, ['class' => 'form-control']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_4', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_4']); !!}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-8">
	    {!! Form::hidden('final_total', 0 , ['id' => 'grand_total_hidden']); !!}
		<b>@lang('lang_v1.order_total'): </b><span id="grand_total" class="display_currency" data-currency_symbol='true'>0</span>
		</div>
	</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-solid hide'])
		<div class="row">
			<div class="col-sm-12">
			<table class="table">
				<tr class="hide">
					<td class="col-md-3">
						{{-- <div class="form-group">
							{!! Form::label('discount_type', __( 'purchase.discount_type' ) . ':') !!}
							{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed' => __( 'lang_v1.fixed' ), 'percentage' => __( 'lang_v1.percentage' )], '', ['class' => 'form-control select2']); !!}
						</div> --}}
					</td>
					{{-- <td class="col-md-3">
						<div class="form-group">
						{!! Form::label('discount_amount', __( 'purchase.discount_amount' ) . ':') !!}
						{!! Form::text('discount_amount', 0, ['class' => 'form-control input_number', 'required']); !!}
						</div>
					</td> --}}
					<td class="col-md-3">
						&nbsp;
					</td>
					<td class="col-md-3">
						<b>@lang( 'purchase.discount' ):</b>(-) 
						<span id="discount_calculated_amount" class="display_currency">0</span>
					</td>
				</tr>
				<tr class="hide">
					<td>
						<div class="form-group">
						{!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}
						<select name="tax_id" id="tax_id" class="form-control select2" placeholder="'Please Select'">
							<option value="" data-tax_amount="0" data-tax_type="fixed" selected>@lang('lang_v1.none')</option>
							@foreach($taxes as $tax)
								<option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" data-tax_type="{{ $tax->calculation_type }}">{{ $tax->name }}</option>
							@endforeach
						</select>
						{!! Form::hidden('tax_amount', 0, ['id' => 'tax_amount']); !!}
						</div>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
						<b>@lang( 'purchase.purchase_tax' ):</b>(+) 
						<span id="tax_calculated_amount" class="display_currency">0</span>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						{{-- <div class="form-group">
							{!! Form::label('additional_notes',__('purchase.additional_notes')) !!}
							{!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 3]); !!}
						</div> --}}
					</td>
				</tr>

			</table>
			</div>
		</div>
	@endcomponent
	<div class="row">
			{{-- <div class="col-sm-12 text-center">
				<button type="button" id="submit_purchase_form" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">@lang('messages.save')</button>
			</div> --}}
		</div>

{!! Form::close() !!}
</section>
</div>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
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

            $('#shipping_documents').fileinput({
		        showUpload: false,
		        showPreview: false,
		        browseLabel: LANG.file_browse_label,
		        removeLabel: LANG.remove,
		    });

			if($('#location_id').length){
				$('#location_id').change();
			}
    	});
	</script>
	@include('purchase.partials.keyboard_shortcuts')
@endsection
