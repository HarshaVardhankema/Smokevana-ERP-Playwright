@extends('layouts.app')
@section('title', __('lang_v1.add_purchase_return'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .pr-create-page {
        background: #EAEDED;
        min-height: 100vh;
        padding-bottom: 24px;
    }

    .pr-create-banner {
        background: #37475a;
        border-radius: 6px;
        padding: 22px 28px;
        margin-bottom: 16px;
        box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .pr-create-banner-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        color: #ffffff;
    }

    .pr-create-banner-title i {
        color: #ffffff !important;
    }

    .pr-create-banner-subtitle {
        font-size: 13px;
        color: rgba(249, 250, 251, 0.88);
        margin: 0;
    }

    .pr-create-header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .pr-create-container .box {
        border-radius: 8px;
        border: 1px solid #D5D9D9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        /* Allow datepicker/calendar to overflow outside the card */
        overflow: visible;
    }

    .pr-create-container .box-body {
        background: #ffffff;
    }

    .pr-create-container .form-group label {
        font-weight: 600;
        color: #0F1111;
        font-size: 13px;
    }

    .pr-create-container .form-control {
        border-radius: 4px;
        border: 1px solid #D5D9D9;
        box-shadow: none;
    }

    .pr-create-container .form-control:focus {
        border-color: #FF9900;
        box-shadow: 0 0 0 3px rgba(255,153,0,0.25);
    }

    .pr-create-total {
        font-weight: 600;
        color: #0F1111;
    }
</style>
@endsection

@section('content')

<section class="content-header no-print">
    <div class="pr-create-banner">
        <div>
            <h1 class="pr-create-banner-title">
                <i class="fas fa-undo"></i>
                @lang('lang_v1.add_purchase_return')
            </h1>
            <p class="pr-create-banner-subtitle">
                Create a purchase return for your supplier.
            </p>
        </div>
        <div class="pr-create-header-actions">
            <button type="button" id="submit_purchase_return_form" class="amazon-btn-primary">
                @lang('messages.submit')
            </button>
        </div>
    </div>
</section>

{!! Form::open(['url' => action([\App\Http\Controllers\CombinedPurchaseReturnController::class, 'save']), 'method' => 'post', 'id' => 'purchase_return_form', 'files' => true ]) !!}

<!-- Main content -->
<section class="content no-print pr-create-page pr-create-container">
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-user"></i>
							</span>
							{!! Form::select('contact_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier_id']); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						{!! Form::label('location_id', __('purchase.business_location').':*') !!}
						{!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', null, ['class' => 'form-control']); !!}
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
				{{-- <div class="clearfix"></div> --}}
				<div class=" hide col-md-1-5">
	                <div class="form-group">
	                    {!! Form::label('document', __('purchase.attach_document') . ':') !!}
	                    {!! Form::file('document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
	                    {{-- <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
	                    @includeIf('components.document_help_text')</p> --}}
	                </div>
	            </div>
			</div>
		</div>
	</div> <!--box end-->
	<div class="box box-solid">
		{{-- <div class="box-header">
        	<h3 class="box-title">{{ __('stock_adjustment.search_products') }}</h3>
       	</div> --}}
		<div class="box-body">
			
			<div class="row">
				<div class="col-sm-12">
					<input type="hidden" id="product_row_index" value="0">
					<input type="hidden" id="total_amount" name="final_total" value="0">
					<div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
					<table class="table table-bordered table-striped table-condensed" 
					id="purchase_return_product_table">
						<thead style="position: sticky; top: 0; z-index: 4;">
							<tr>
								<th class="text-center">	
									@lang('sale.product')
								</th>
								@if(session('business.enable_lot_number'))
									<th>
										@lang('lang_v1.lot_number')
									</th>
								@endif
								@if(session('business.enable_product_expiry'))
									<th>
										@lang('product.exp_date')
									</th>
								@endif
								<th class="text-center">
									@lang('sale.qty')
								</th>
								<th class="text-center">
									@lang('sale.unit_price')
								</th>
								<th class="text-center">
									@lang('sale.subtotal')
								</th>
								<th class="text-center"><i class="fa fa-trash" aria-hidden="true"></i></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<div class="row">
					<div class="col-sm-8 col-sm-offset-2">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-search"></i>
								</span>
								{!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product_for_purchase_return', 'placeholder' => __('stock_adjustment.search_products'), 'disabled']); !!}
							</div>
						</div>
					</div>
				</div>
				</div>
				{{-- <div class="clearfix"></div> --}}
				<div class="col-md-4 hide">
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
				</div>
				<div class="col-md-12">
					<div class="pull-right"><b>@lang('stock_adjustment.total_amount'):</b> <span id="total_return">0.00</span></div>
				</div>
			</div>
		</div>
	</div> <!--box end-->
	
	{!! Form::close() !!}
</section>
@stop
@section('javascript')
	<script src="{{ asset('js/purchase_return.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		__page_leave_confirmation('#purchase_return_form');
	</script>
@endsection
