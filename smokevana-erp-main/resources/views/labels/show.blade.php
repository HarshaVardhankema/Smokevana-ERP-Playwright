@extends('layouts.app')
@section('title', __('barcode.print_labels'))

@section('css')
<style>
    .print-labels-header-banner {
        background: #37475a;
        border-radius: 6px;
        padding: 22px 28px;
        margin-bottom: 16px;
        box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
    }
    .print-labels-header-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .print-labels-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        color: #ffffff;
    }
    .print-labels-header-title i {
        font-size: 22px;
        color: #ffffff !important;
    }
    .print-labels-header-title .fa,
    .print-labels-header-title [data-toggle="tooltip"] {
        color: rgba(255, 255, 255, 0.9) !important;
    }
    .print-labels-header-subtitle {
        font-size: 13px;
        color: rgba(249, 250, 251, 0.88);
        margin: 0;
    }
</style>
@endsection

@section('content')

<!-- Amazon-style banner -->
<section class="content-header">
    <div class="print-labels-header-banner">
        <div class="print-labels-header-content">
            <h1 class="print-labels-header-title">
                <i class="fas fa-tags"></i>
                @lang('barcode.print_labels') @show_tooltip(__('tooltip.print_label'))
            </h1>
            <p class="print-labels-header-subtitle">
                @lang('product.add_product_for_labels')
            </p>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content no-print">
	{!! Form::open(['url' => '#', 'method' => 'post', 'id' => 'preview_setting_form', 'onsubmit' => 'return false']) !!}
	@component('components.widget', ['class' => 'box-primary', 'title' => __('product.add_product_for_labels')])
		<div class="row">
			<div class="tw-w-full">
				<table class="table table-bordered table-striped table-condensed" id="product_table">
					<thead>
						<tr>
							<th>@lang( 'barcode.products' )</th>
							<th>@lang( 'barcode.no_of_labels' )</th>
							@if(request()->session()->get('business.enable_lot_number') == 1)
								<th>@lang( 'lang_v1.lot_number' )</th>
							@endif
							@if(request()->session()->get('business.enable_product_expiry') == 1)
								<th>@lang( 'product.exp_date' )</th>
							@endif
							<th>@lang('lang_v1.packing_date')</th>
							<th>@lang('lang_v1.selling_price_group')</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@include('labels.partials.show_table_rows', ['index' => 0])
					</tbody>
				</table>
				{{-- Search bar below products - search symbol at start (left addon) --}}
				<div class="tw-mt-3 tw-p-3 tw-bg-gray-50 tw-rounded-lg tw-border tw-border-gray-200">
					<label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 tw-mb-2">Search & add products</label>
					<div class="input-group">
						<span class="input-group-addon" style="padding: 6px 12px;">
							<i class="fa fa-search" style="color: #6c757d;"></i>
						</span>
						<input type="text"
							id="search_product_for_label"
							name="search_product"
							class="form-control"
							placeholder="@lang('lang_v1.enter_product_name_to_print_labels')"
							autocomplete="off"
							autofocus>
					</div>
				</div>
			</div>
		</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-primary', 'title' => __( 'barcode.info_in_labels' )])
		<div class="row">
			<div class="col-md-12">
				<table class="table table-bordered">
					<tr>
						<td>
							<div class="checkbox">
							    <label>
							    	<input type="checkbox" checked name="print[name]" value="1"> <b>@lang( 'barcode.print_name' )</b>
							    </label>
							</div>

							<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
								<input type="text" class="form-control" 
									name="print[name_size]" 
									value="6">
							</div>
						</td>

						<td>
							<div class="checkbox">
							    <label>
							    	<input type="checkbox" checked name="print[variations]" value="1"> <b>@lang( 'barcode.print_variations' )</b>
							    </label>
							</div>

							<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
								<input type="text" class="form-control" 
									name="print[variations_size]" 
									value="5">
							</div>
						</td>

						<td>
							<div class="checkbox">
							    <label>
							    	<input type="checkbox" checked name="print[price]" value="1" id="is_show_price"> <b>@lang( 'barcode.print_price' )</b>
							    </label>
							</div>

							<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
								<input type="text" class="form-control" 
									name="print[price_size]" 
									value="6">
							</div>

						</td>

						<td class="hide">
							
							<div class="" id="price_type_div">
								<div class="form-group">
									{!! Form::label('print[price_type]', @trans( 'barcode.show_price' ) . ':') !!}
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-info"></i>
										</span>
										{!! Form::select('print[price_type]', ['inclusive' => __('product.inc_of_tax'), 'exclusive' => __('product.exc_of_tax')], 'inclusive', ['class' => 'form-control']); !!}
									</div>
								</div>
							</div>

						</td>
					</tr>

					<tr>
						<td>
							<div class="checkbox">
							    <label>
							    	<input type="checkbox" checked name="print[business_name]" value="1"> <b>@lang( 'barcode.print_business_name' )</b>
							    </label>
							</div>

							<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
								<input type="text" class="form-control" 
									name="print[business_name_size]" 
									value="8">
							</div>
						</td>

						<td>
							<div class="checkbox">
							    <label>
							    	<input type="checkbox" checked name="print[packing_date]" value="1"> <b>@lang( 'lang_v1.print_packing_date' )</b>
							    </label>
							</div>

							<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
								<input type="text" class="form-control" 
									name="print[packing_date_size]" 
									value="5">
							</div>
						</td>

						<td>
							@if(request()->session()->get('business.enable_lot_number') == 1)
							
								<div class="checkbox">
								    <label>
								    	<input type="checkbox" checked name="print[lot_number]" value="1"> <b>@lang( 'lang_v1.print_lot_number' )</b>
								    </label>
								</div>

								<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
									<input type="text" class="form-control" 
										name="print[lot_number_size]" 
										value="5">
								</div>
							@endif
						</td>

						<td>
							@if(request()->session()->get('business.enable_product_expiry') == 1)
								<div class="checkbox">
								    <label>
								    	<input type="checkbox" checked name="print[exp_date]" value="1"> <b>@lang( 'lang_v1.print_exp_date' )</b>
								    </label>
								</div>

								<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
									<input type="text" class="form-control" 
										name="print[exp_date_size]" 
										value="5">
								</div>
							@endif
						</td>						
					</tr>
					<tr>
						
						@php
							$c = 0;
							$custom_labels = json_decode(session('business.custom_labels'), true);
        					$product_custom_fields = !empty($custom_labels['product']) ? $custom_labels['product'] : [];
							 $product_cf_details = !empty($custom_labels['product_cf_details']) ? $custom_labels['product_cf_details'] : [];
						@endphp
						@foreach($product_custom_fields as $index => $cf)
							@if(!empty($cf))
								@php
									$field_name = 'product_custom_field' . $loop->iteration;
									$cf_type = !empty($product_cf_details[$loop->iteration]['type']) ? $product_cf_details[$loop->iteration]['type'] : 'text';
									$dropdown = !empty($product_cf_details[$loop->iteration]['dropdown_options']) ? explode(PHP_EOL, $product_cf_details[$loop->iteration]['dropdown_options']) : [];
									$c++;
								@endphp
								<td>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="print[{{ $field_name }}]" value="1"> <b>{{ $cf }}</b>
										</label>
									</div>

									<div class="input-group">
									<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
										<input type="text" class="form-control" 
											name="print[{{ $field_name }}_size]" 
											value="7">
									</div>
								</td>
								@if ($c % 4 == 0)
									</tr>
								@endif
							@endif
						@endforeach
					</tr>
				</table>
			</div>

			

			

			<div class="col-sm-12">
				<hr/>
			</div>

			<div class="col-sm-4">
				<div class="form-group">
					{!! Form::label('price_type', @trans( 'barcode.barcode_setting' ) . ':') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-cog"></i>
						</span>
						{!! Form::select('barcode_setting', $barcode_settings, !empty($default) ? $default->id : null, ['class' => 'form-control']); !!}
					</div>
				</div>
			</div>

			<div class="clearfix"></div>
			
			<div class="col-sm-12 text-center">
				<button type="button" id="labels_print" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">Print</button>
			</div>
		</div>
	@endcomponent
	{!! Form::close() !!}

	<div class="col-sm-8 hide display_label_div">
		<h3 class="box-title">@lang( 'barcode.preview' )</h3>
		<button type="button" class="col-sm-offset-2 btn btn-success btn-block" id="print_label">Print</button>
	</div>
	<div class="clearfix"></div>
</section>

<!-- Labels print section (hidden until print)-->
<div id="labels_print_container" style="display: none;">
</div>

@stop
@section('javascript')
	<script src="{{ asset('js/labels.js?v=' . $asset_v) }}"></script>
@endsection
