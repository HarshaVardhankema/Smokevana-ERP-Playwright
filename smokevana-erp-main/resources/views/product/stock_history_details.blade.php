@php
	$common_settings = session()->get('business.common_settings');
@endphp
<style>
    .stock-details-summary {
        margin-bottom: 24px;
    }
    .stock-details-summary .col-md-4 {
        margin-bottom: 20px;
    }
    .stock-details-card {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        background-color: #37475a !important;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.15);
        border-left: 4px solid #FF9900;
        height: 100%;
    }
    .stock-details-summary .col-md-4 .stock-details-card,
    #product_stock_history .stock-details-card {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        background-color: #37475a !important;
    }
    .stock-details-card strong {
        color: #ffffff !important;
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 12px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stock-details-card .table {
        background: #ffffff !important;
        margin-bottom: 0;
        border-radius: 6px;
        overflow: hidden;
    }
    .stock-details-card .table th,
    .stock-details-card table th,
    .stock-details-card .table-condensed th {
        color: #0F1111 !important;
        font-weight: 600;
        font-size: 13px;
        padding: 8px 12px;
        border-bottom: 1px solid #E5E7EB !important;
        background: #F7F8F8 !important;
    }
    .stock-details-card .table td,
    .stock-details-card table td,
    .stock-details-card .table-condensed td {
        color: #0F1111 !important;
        font-size: 14px;
        padding: 8px 12px;
        border-bottom: 1px solid #E5E7EB !important;
        background: #ffffff !important;
    }
    .stock-details-card .table td .display_currency,
    .stock-details-card table td .display_currency,
    .stock-details-card .table-condensed td .display_currency,
    .stock-details-card .table td span,
    .stock-details-card table td span,
    .stock-details-card .table-condensed td span {
        color: #0F1111 !important;
    }
    .stock-details-card .table tr:last-child td {
        border-bottom: none;
    }
    .stock-history-table-wrapper {
        margin-top: 24px;
    }
    .stock-history-table-wrapper hr {
        border: none;
        border-top: 2px solid #D5D9D9;
        margin: 24px 0;
    }
    #stock_history_table {
        background: #ffffff;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #D5D9D9;
    }
    #stock_history_table thead th {
        background: #F7F8F8;
        color: #0F1111;
        font-weight: 600;
        font-size: 13px;
        padding: 12px 16px;
        border-bottom: 2px solid #D5D9D9;
        text-align: left;
    }
    #stock_history_table tbody td {
        padding: 12px 16px;
        color: #0F1111;
        border-bottom: 1px solid #E5E7EB;
        font-size: 13px;
    }
    #stock_history_table tbody tr:hover {
        background-color: #F7F8F8;
    }
    #stock_history_table tbody tr:last-child td {
        border-bottom: none;
    }
    #stock_history_table .text-success {
        color: #0F8644 !important;
        font-weight: 600;
    }
    #stock_history_table .text-danger {
        color: #C40000 !important;
        font-weight: 600;
    }
    #stock_history_table .text-center {
        text-align: center;
        padding: 24px;
        color: #64748b;
    }
</style>
<div class="row stock-details-summary">
	<div class="col-md-12">
		{{-- <h4>{{$stock_details['variation']}}</h4> --}}
	</div>
	<div class="col-md-4 col-xs-4">
		<div class="stock-details-card">
			<strong>@lang('lang_v1.quantities_in')</strong>
			<table class="table table-condensed">
				<tr>
					<th>@lang('report.total_purchase')</th>
					<td>
						<span class="display_currency" data-is_quantity="true">{{$stock_details['total_purchase']}}</span> {{$stock_details['unit']}}
					</td>
				</tr>
				<tr>
					<th>@lang('lang_v1.opening_stock')</th>
					<td>
						<span class="display_currency" data-is_quantity="true">{{$stock_details['total_opening_stock']}}</span> {{$stock_details['unit']}}
					</td>
				</tr>
				<tr>
					<th>@lang('lang_v1.total_sell_return')</th>
					<td>
						<span class="display_currency" data-is_quantity="true">{{$stock_details['total_sell_return']}}</span> {{$stock_details['unit']}}
					</td>
				</tr>
				<tr>
					<th>@lang('lang_v1.stock_transfers') (@lang('lang_v1.in'))</th>
					<td>
						<span class="display_currency" data-is_quantity="true">{{$stock_details['total_purchase_transfer']}}</span> {{$stock_details['unit']}}
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="col-md-4 col-xs-4">
		<div class="stock-details-card">
			<strong>@lang('lang_v1.quantities_out')</strong>
			<table class="table table-condensed">
				<tr>
					<th>@lang('lang_v1.total_sold')</th>
					<td>
						<span class="display_currency" data-is_quantity="true">{{$stock_details['total_sold']}}</span> {{$stock_details['unit']}}
					</td>
				</tr>
				<tr>
					<th>@lang('report.total_stock_adjustment')</th>
					<td>
						<span class="display_currency" data-is_quantity="true">{{$stock_details['total_adjusted']}}</span> {{$stock_details['unit']}}
					</td>
				</tr>
				<tr>
					<th>@lang('lang_v1.total_purchase_return')</th>
					<td>
						<span class="display_currency" data-is_quantity="true">{{$stock_details['total_purchase_return']}}</span> {{$stock_details['unit']}}
					</td>
				</tr>
				
				<tr>
					<th>@lang('lang_v1.stock_transfers') (@lang('lang_v1.out'))</th>
					<td>
						<span class="display_currency" data-is_quantity="true">{{$stock_details['total_sell_transfer']}}</span> {{$stock_details['unit']}}
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="col-md-4 col-xs-4">
		<div class="stock-details-card">
			<strong>@lang('lang_v1.totals')</strong>
			<table class="table table-condensed">
				<tr>
					<th>@lang('report.current_stock')</th>
					<td>
						<span class="display_currency" data-is_quantity="true" style="font-weight: 700; font-size: 16px;">{{$stock_details['current_stock']}}</span> {{$stock_details['unit']}}
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<div class="row stock-history-table-wrapper">
	<div class="col-md-12">
		<hr>
		<table class="table table-slim" id="stock_history_table">
			<thead>
			<tr>
				<th>@lang('lang_v1.type')</th>
				<th>@lang('lang_v1.quantity_change')</th>
				@if(!empty($common_settings['enable_secondary_unit']))
					<th>@lang('lang_v1.quantity_change') (@lang('lang_v1.secondary_unit'))</th>
				@endif
				<th>@lang('lang_v1.new_quantity')</th>
				@if(!empty($common_settings['enable_secondary_unit']))
					<th>@lang('lang_v1.new_quantity') (@lang('lang_v1.secondary_unit'))</th>
				@endif
				<th>@lang('lang_v1.date')</th>
				<th>@lang('purchase.ref_no')</th>
				<th>@lang('lang_v1.customer_supplier_info')</th>
			</tr>
			</thead>
			<tbody>
			@forelse($stock_history as $history)
				<tr>
					<td>{{$history['type_label']}}</td>
					@if($history['quantity_change'] > 0 )
						<td class="text-success"> +<span class="display_currency" data-is_quantity="true">{{$history['quantity_change']}}</span>
						</td>
					@else
						<td class="text-danger"><span class="display_currency text-danger" data-is_quantity="true">{{$history['quantity_change']}}</span>
						</td>
					@endif

					@if(!empty($common_settings['enable_secondary_unit']))
						@if($history['quantity_change'] > 0 )
							<td class="text-success">
								@if(!empty($history['purchase_secondary_unit_quantity']))
									+<span class="display_currency" data-is_quantity="true">{{$history['purchase_secondary_unit_quantity']}}</span> {{$stock_details['second_unit']}}
								@endif
							</td>
						@else
							<td class="text-danger">
								@if(!empty($history['sell_secondary_unit_quantity']))
									-<span class="display_currency" data-is_quantity="true">{{$history['sell_secondary_unit_quantity']}}</span> {{$stock_details['second_unit']}}
								@endif
							</td>
						@endif
					@endif
					<td>
						<span class="display_currency" data-is_quantity="true">{{$history['stock']}}</span>
					</td>
					@if(!empty($common_settings['enable_secondary_unit']))
						<td>
							@if(!empty($stock_details['second_unit']))
								<span class="display_currency" data-is_quantity="true">{{$history['stock_in_second_unit']}}</span> {{$stock_details['second_unit']}}
							@endif
						</td>
					@endif
					<td>{{@format_datetime($history['date'])}}</td>
					<td>
						{{$history['ref_no']}}

						@if(!empty($history['additional_notes']))
							@if(!empty($history['ref_no']))
							<br>
							@endif
							{{$history['additional_notes']}}
						
						@endif
					</td>
					<td>
						{{$history['contact_name'] ?? '--'}} 
						@if(!empty($history['supplier_business_name']))
						 - {{$history['supplier_business_name']}}
						@endif
					</td>
				</tr>
			@empty
				<tr><td colspan="5" class="text-center">
					@lang('lang_v1.no_stock_history_found')
				</td></tr>
			@endforelse
			</tbody>
		</table>
	</div>
</div>