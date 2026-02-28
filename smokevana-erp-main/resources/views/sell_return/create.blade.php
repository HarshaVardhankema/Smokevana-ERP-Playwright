@extends('layouts.app')
@section('title', __('lang_v1.sell_return'))

@section('css')
<style>
    .sell-return-create-page {
        background: #EAEDED !important;
        min-height: 100vh;
        margin: 0 -1rem;
        padding: 1.5rem;
    }
    .sell-return-create-page .content-header { background: transparent !important; padding: 0 0 1rem !important; margin-bottom: 0 !important; }
    .sell-return-create-page .content-header h1 { color: #0F1111 !important; }
    .sell-return-create-page .content { background: transparent !important; }
    .sell-return-create-page .box {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #D5D9D9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .sell-return-create-page .box-header {
        background: linear-gradient(to bottom, #232f3e 0%, #37475a 100%) !important;
        color: #fff !important;
        border-bottom: 2px solid #FF9900;
    }
    .sell-return-create-page .box-body { background: #f7f8f8 !important; }
    .sell-return-create-page .form-group label { color: #0F1111 !important; }
    .sell-return-create-page #sell_return_table { background: #fff; }
    .sell-return-create-page #sell_return_table thead tr {
        background: linear-gradient(to bottom, #232f3e 0%, #37475a 100%) !important;
        color: #fff;
    }
    .sell-return-create-page #sell_return_table thead th { color: #fff !important; border-color: rgba(255,255,255,0.1); }
    .sell-return-create-page #sell_return_table tbody td { color: #0F1111; }
</style>
@endsection

@section('content')
<div class="sell-return-create-page">
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.sell_return')</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="box box-solid">
        <div class="box-body">
            <div class="form-group">
                {!! Form::label('sell_id', __('sale.invoice_no') . ':') !!}
				{!! Form::select('sell_id', $transactions, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]) !!}
            </div>
        </div>
    </div>

	{!! Form::open(['url' => action([\App\Http\Controllers\SellReturnController::class, 'store']), 'method' => 'post', 'id' => 'sell_return_form' ]) !!}
	{!! Form::hidden('location_id', null, ['id' => 'location_id', 'data-receipt_printer_type' => '' ]) !!}
	{!! Form::hidden('transaction_id', null, ['id' => 'transaction_id']) !!}

	<div class="box box-solid" id="parent_sale_box" style="display:none;">
		<div class="box-header">
			<h3 class="box-title">@lang('lang_v1.parent_sale')</h3>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<strong>@lang('sale.invoice_no'):</strong> <span id="parent_invoice_no">-</span> <br>
					<strong>@lang('messages.date'):</strong> <span id="parent_transaction_date">-</span>
				</div>
				<div class="col-sm-4">
					<strong>@lang('contact.customer'):</strong> <span id="parent_customer">-</span> <br>
					<strong>@lang('purchase.business_location'):</strong> <span id="parent_location">-</span>
				</div>
			</div>
		</div>
	</div>

	<div class="box box-solid" id="return_form_box" style="display:none;">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('invoice_no', __('sale.invoice_no').':') !!}
						{!! Form::text('invoice_no', null, ['class' => 'form-control', 'id' => 'invoice_no']) !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', null, ['class' => 'form-control', 'id' => 'transaction_date', 'readonly', 'required']) !!}
						</div>
					</div>
				</div>
				<div class="col-sm-12">
					<table class="table bg-gray" id="sell_return_table">
						<thead>
							<tr>
								<th>#</th>
								<th>@lang('product.product_name')</th>
								<th>@lang('sale.unit_price')</th>
								<th>@lang('lang_v1.sell_quantity')</th>
								<th>@lang('lang_v1.return_quantity')</th>
								<th>@lang('lang_v1.return_subtotal')</th>
							</tr>
						</thead>
						<tbody id="sell_return_tbody">
						</tbody>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('discount_type', __( 'purchase.discount_type' ) . ':') !!}
						{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed' => __( 'lang_v1.fixed' ), 'percentage' => __( 'lang_v1.percentage' )], null, ['class' => 'form-control', 'id' => 'discount_type']) !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('discount_amount', __( 'purchase.discount_amount' ) . ':') !!}
						{!! Form::text('discount_amount', null, ['class' => 'form-control input_number', 'id' => 'discount_amount']) !!}
					</div>
				</div>
			</div>
			{!! Form::hidden('tax_id', null, ['id' => 'tax_id']) !!}
			{!! Form::hidden('tax_amount', 0, ['id' => 'tax_amount']) !!}
			{!! Form::hidden('tax_percent', 0, ['id' => 'tax_percent']) !!}
			<div class="row">
				<div class="col-sm-12 text-right">
					<strong>@lang('lang_v1.total_return_discount'):</strong>
					&nbsp;(-) <span id="total_return_discount"></span>
				</div>
				<div class="col-sm-12 text-right">
					<strong>@lang('lang_v1.total_return_tax'):</strong>
					&nbsp;(+) <span id="total_return_tax"></span>
				</div>
				<div class="col-sm-12 text-right">
					<strong>@lang('lang_v1.return_total'):</strong>&nbsp;
					<span id="net_return">0</span>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-sm-12">
					<button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white pull-right">@lang('messages.save')</button>
				</div>
			</div>
		</div>
	</div>

	{!! Form::close() !!}
</section>
</div>
@stop
@section('javascript')
<script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/sell_return.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('form#sell_return_form').validate();

		$('#sell_id').on('change', function() {
            var sell_id = $(this).val();
			if(!sell_id){ 
				$('#parent_sale_box, #return_form_box').hide();
				return; 
			}
			$.ajax({
				url: "{{ url('sell-return/get-sell-data') }}/" + sell_id,
				type: 'GET',
				success: function(response){
					var sell = response;
					if(typeof response === 'string'){
						try { sell = JSON.parse(response); } catch(e){ sell = null; }
					}
					if(!sell || !sell.id){
						if(window.toastr){ toastr.error('{{ __('messages.something_went_wrong') }}'); }
						return;
					}
					populateSellReturnForm(sell);
					$('#parent_sale_box, #return_form_box').show();
					update_sell_return_total();
					if(window.toastr){ toastr.success('Sale data loaded successfully'); }
				},
				error: function(xhr){
					var errorMsg = '{{ __('messages.something_went_wrong') }}';
					if(xhr.responseJSON && xhr.responseJSON.error){
						errorMsg = xhr.responseJSON.error;
					}
					if(window.toastr){ toastr.error(errorMsg); }
					$('#parent_sale_box, #return_form_box').hide();
				}
			});
        });

		$(document).on('change', 'input.return_qty, #discount_amount, #discount_type', function() {
			update_sell_return_total()
		});
	});

	function populateSellReturnForm(sell){
		// Hidden fields
		$('#transaction_id').val(sell.id);
		$('#location_id').val(sell.location.id).data('receipt_printer_type', sell.location.receipt_printer_type || '');
		$('#tax_id').val(sell.tax_id || '');
		var tax_percent = 0;
		if(sell.tax && sell.tax.amount){ tax_percent = sell.tax.amount; }
		$('#tax_percent').val(tax_percent);

		// Parent sale info
		$('#parent_invoice_no').text(sell.invoice_no || '-');
		var tdate = sell.return_parent && sell.return_parent.transaction_date ? sell.return_parent.transaction_date : (sell.transaction_date || new Date());
		try {
			$('#parent_transaction_date').text(moment(tdate).format(moment_date_format + ' ' + moment_time_format));
			$('#transaction_date').val(moment(tdate).format(moment_date_format + ' ' + moment_time_format));
		} catch(e){
			$('#parent_transaction_date').text(tdate);
			$('#transaction_date').val(tdate);
		}
		$('#parent_customer').text(sell.contact ? (sell.contact.name || '-') : '-');
		$('#parent_location').text(sell.location ? (sell.location.name || '-') : '-');

		// Return header fields
		var invoice_no = (sell.return_parent && sell.return_parent.invoice_no) ? sell.return_parent.invoice_no : '';
		$('#invoice_no').val(invoice_no);

		// Discounts
		var discount_type = (sell.return_parent && sell.return_parent.discount_type) ? sell.return_parent.discount_type : (sell.discount_type || '');
		var discount_amount = (sell.return_parent && sell.return_parent.discount_amount) ? sell.return_parent.discount_amount : (sell.discount_amount || 0);
		$('#discount_type').val(discount_type);
		$('#discount_amount').val(discount_amount || 0);

		// Build table rows
		var tbody = $('#sell_return_tbody');
		tbody.empty();
		if(Array.isArray(sell.sell_lines)){
			sell.sell_lines.forEach(function(line, index){
				var allow_decimal = (line.product && line.product.unit && line.product.unit.allow_decimal == 0) ? 'true' : 'false';
				var unit_name = (line.product && line.product.unit) ? (line.product.unit.short_name || '') : '';
				if(line.sub_unit){
					unit_name = line.sub_unit.short_name || unit_name;
					allow_decimal = (line.sub_unit.allow_decimal == 0) ? 'true' : 'false';
				}
				var product_name = (line.product ? (line.product.name || '') : '');
				if(line.product && line.product.type == 'variable'){
					if(line.variations && line.variations.product_variation){
						product_name += ' - ' + (line.variations.product_variation.name || '');
					}
					if(line.variations){
						product_name += ' - ' + (line.variations.name || '');
					}
				}
				var sub_sku = (line.variations && line.variations.sub_sku) ? line.variations.sub_sku : '';
				var formatted_qty = line.formatted_qty || line.quantity;
				var remaining_qty = line.remaining_qty || line.quantity;
				var total_returned = line.total_returned || 0;
				var qty_val = 1; // Default return quantity
				var unit_price = line.unit_price_inc_tax || 0;
				
				// Show return history info
				var return_info = '';
				if(total_returned > 0){
					return_info = '<br><small class="text-info">Already returned: ' + __number_f(total_returned) + ' ' + unit_name + '</small>';
				}
				
				var row = ''+
					'<tr>'+
						'<td>'+(index+1)+'</td>'+
						'<td>'+product_name+'<br>'+sub_sku+return_info+'</td>'+
						'<td><span class="display_currency" data-currency_symbol="true">'+__currency_trans_from_en(unit_price, true)+'</span></td>'+
						'<td>'+formatted_qty+' '+unit_name+'<br><small class="text-muted">Remaining: '+__number_f(remaining_qty)+' '+unit_name+'</small></td>'+
						'<td>'+
							'<input type="text" name="products['+index+'][quantity]" value="'+qty_val+'" class="form-control input-sm input_number return_qty input_quantity" min="0" data-rule-abs_digit="'+allow_decimal+'" data-msg-abs_digit="{{ __('lang_v1.decimal_value_not_allowed') }}" data-rule-max-value="'+remaining_qty+'">'+
							'<input name="products['+index+'][unit_price_inc_tax]" type="hidden" class="unit_price" value="'+(unit_price || 0)+'">'+
							'<input name="products['+index+'][sell_line_id]" type="hidden" value="'+line.id+'">'+
						'</td>'+
						'<td><div class="return_subtotal"></div></td>'+
					'</tr>';
				tbody.append(row);
			});
		}
		__currency_convert_recursively($('#sell_return_table'));
	}

	function update_sell_return_total() {
		var net_return = 0;
		$('table#sell_return_table tbody tr').each(function() {
			var quantity = __read_number($(this).find('input.return_qty'));
			var unit_price = __read_number($(this).find('input.unit_price'));
			var subtotal = quantity * unit_price;
			$(this).find('.return_subtotal').text(__currency_trans_from_en(subtotal, true));
			net_return += subtotal;
		});
		var discount = 0;
		if ($('#discount_type').val() == 'fixed') {
			discount = __read_number($("#discount_amount"));
		} else if ($('#discount_type').val() == 'percentage') {
			var discount_percent = __read_number($("#discount_amount"));
			discount = __calculate_amount('percentage', discount_percent, net_return);
		}
		var discounted_net_return = net_return - discount;

		var tax_percent = $('input#tax_percent').val();
		var total_tax = __calculate_amount('percentage', tax_percent, discounted_net_return);
		var net_return_inc_tax = total_tax + discounted_net_return;

		$('input#tax_amount').val(total_tax);
		$('span#total_return_discount').text(__currency_trans_from_en(discount, true));
		$('span#total_return_tax').text(__currency_trans_from_en(total_tax, true));
		$('span#net_return').text(__currency_trans_from_en(net_return_inc_tax, true));
	}
</script>
@endsection