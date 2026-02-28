<style>
	.invoice-container {
		font-family: Arial, sans-serif;
		max-width: 800px;
		margin: 0 auto;
		padding: 10px;
		font-size: 12px;
	}

	.invoice-header {
		text-align: right;
		margin-bottom: 10px;
	}

	.invoice-title {
		font-size: 18px;
		font-weight: bold;
		color: #333;
	}

	.invoice-number {
		font-size: 14px;
		color: #666;
	}

	.business-info {
		display: flex;
		justify-content: space-between;
		margin-bottom: 15px;
	}

	.business-details {
		flex: 1;
	}

	.invoice-details {
		flex: 1;
		text-align: right;
	}

	.business-logo {
		max-width: 150px;
		margin-bottom: 5px;
	}

	.customer-info {
		margin-bottom: 10px;
	}

	.invoice-table {
		width: 100%;
		border-collapse: collapse;
		margin-bottom: 10px;
		font-size: 11px;
	}

	.invoice-table th {
		background-color: #357ca5;
		color: white;
		padding: 6px;
		text-align: left;
		white-space: nowrap;
		min-width: 80px;
		font-size: 11px;
	}

	.invoice-table td {
		padding: 5px;
		border: 1px solid #ddd;
	}

	.invoice-table th:first-child {
		min-width: 40px;
	}

	.invoice-table th:nth-child(2) {
		min-width: 150px;
	}

	.invoice-totals {
		width: 100%;
		margin-top: 10px;
		font-size: 11px;
	}

	.invoice-totals td {
		padding: 4px;
	}

	.total-row {
		background-color: #357ca5;
		color: white;
		font-weight: bold;
	}

	.footer {
		margin-top: 15px;
		border-top: 1px solid #ddd;
		padding-top: 10px;
	}

	.barcode {
		text-align: center;
		margin-top: 10px;
	}

	.text-right {
		text-align: right;
	}

	.text-center {
		text-align: center;
	}

	.color-555 {
		color: #555;
	}

	.font-23 {
		font-size: 16px;
	}

	.padding-10 {
		padding: 5px;
	}

	.header-text {
		margin-bottom: 10px;
	}

	.business-details .color-555 {
		line-height: 1.3;
	}
</style>

<div class="invoice-container">
	<div class="invoice-header">
		@if(!empty($receipt_details->invoice_heading))
			<div class="invoice-title">{!! $receipt_details->invoice_heading !!}</div>
		@endif
		<div class="invoice-number">
			@if(!empty($receipt_details->invoice_no_prefix))
				{!! $receipt_details->invoice_no_prefix !!}
			@endif
			{{$receipt_details->invoice_no}}
		</div>
	</div>

	@if(!empty($receipt_details->header_text))
		<div class="header-text">
			{!! $receipt_details->header_text !!}
		</div>
	@endif

	<div class="business-info">
		<div class="business-details">
			@if(!empty($receipt_details->display_name))
				<div class="color-555">
					<strong>{{$receipt_details->display_name}}</strong><br>
					{!! $receipt_details->address !!}

					@if(!empty($receipt_details->contact))
						<br>{!! $receipt_details->contact !!}
					@endif

					@if(!empty($receipt_details->website))
						<br>{{ $receipt_details->website }}
					@endif

					@if(!empty($receipt_details->tax_info1))
						<br>{{ $receipt_details->tax_label1 }} {{ $receipt_details->tax_info1 }}
					@endif

					@if(!empty($receipt_details->tax_info2))
						<br>{{ $receipt_details->tax_label2 }} {{ $receipt_details->tax_info2 }}
					@endif

					@if(!empty($receipt_details->location_custom_fields))
						<br>{{ $receipt_details->location_custom_fields }}
					@endif
				</div>
			@endif
		</div>
		<div>
			@if(!empty($receipt_details->logo))
				<img src="{{$receipt_details->logo}}" class="business-logo">
			@endif
		</div>
		<div class="invoice-details">
			<div class="customer-info">
				<strong>{{ $receipt_details->customer_label ?? '' }}</strong><br>
				@if(!empty($receipt_details->customer_name))
					{!! $receipt_details->customer_name !!}<br>
				@endif
				@if(!empty($receipt_details->customer_info))
					{!! $receipt_details->customer_info !!}
				@endif
			</div>
			@if(!empty($receipt_details->date_label))
				<div class="color-555">
					<strong>{{$receipt_details->date_label}}:</strong>
					{{$receipt_details->invoice_date}}
				</div>
			@endif

			@if(!empty($receipt_details->total_paid))
				<div class="color-555">
					<strong>{!! $receipt_details->total_paid_label !!}:</strong>
					{{$receipt_details->total_paid}}
				</div>
			@endif

			@if(!empty($receipt_details->total_due))
				<div class="color-555">
					<strong>{!! $receipt_details->total_due_label !!}:</strong>
					{{$receipt_details->total_due}}
				</div>
			@endif
		</div>
	</div>



	<table class="invoice-table">
		<thead>
			<tr>
				<th>No</th>
				<th>{{$receipt_details->table_product_label}}</th>
				<th>{{$receipt_details->table_qty_label}}</th>
				<th>{{$receipt_details->table_unit_price_label}}</th>
				<th>{{$receipt_details->table_subtotal_label}}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($receipt_details->lines as $line)
				<tr>
					<td class="text-center">{{$loop->iteration}}</td>
					<td>
						{{$line['name']}} {{$line['variation']}}
						@if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif
						@if(!empty($line['brand'])), {{$line['brand']}} @endif
						@if(!empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif
					</td>
					<td class="text-right">{{$line['quantity']}} {{$line['units']}}</td>
					<td class="text-right">{{$line['unit_price_exc_tax']}}</td>
					<td class="text-right">{{$line['line_total']}}</td>
				</tr>
			@endforeach
		</tbody>
	</table>

	<table class="invoice-totals">
		<tr>
			<td>{!! $receipt_details->subtotal_label !!}</td>
			<td class="text-right">{{$receipt_details->subtotal}}</td>
		</tr>

		@if(!empty($receipt_details->taxes))
			@foreach($receipt_details->taxes as $k => $v)
				<tr>
					<td>{{$k}}</td>
					<td class="text-right">{{$v}}</td>
				</tr>
			@endforeach
		@endif

		@if(!empty($receipt_details->discount))
			<tr>
				<td>{!! $receipt_details->discount_label !!}</td>
				<td class="text-right">(-) {{$receipt_details->discount}}</td>
			</tr>
		@endif

		@if(!empty($receipt_details->group_tax_details))
			@foreach($receipt_details->group_tax_details as $key => $value)
				<tr>
					<td>{!! $key !!}</td>
					<td class="text-right">(+) {{$value}}</td>
				</tr>
			@endforeach
		@elseif(!empty($receipt_details->tax))
			<tr>
				<td>{!! $receipt_details->tax_label !!}</td>
				<td class="text-right">(+) {{$receipt_details->tax}}</td>
			</tr>
		@endif

		<tr class="total-row">
			<td class="padding-10">{!! $receipt_details->total_label !!}</td>
			<td class="text-right padding-10">{{$receipt_details->total}}</td>
		</tr>
	</table>

	<div class="footer">
		<div class="row">
			<div class="col-xs-6">
				<strong>Authorized Signatory</strong>
			</div>
			@if($receipt_details->show_barcode)
				<div class="col-xs-6 barcode">
					<img
						src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2, 30, array(39, 48, 54), true)}}">
				</div>
			@endif
		</div>

		@if(!empty($receipt_details->footer_text))
			<div class="footer-text">
				{!! $receipt_details->footer_text !!}
			</div>
		@endif
	</div>
</div>

<script>
    window.onload = function() {
        setTimeout(function() {
            window.print();
        }, 500);
    }
</script>

<style>
    @media print {
        body {
            margin: 0;
            padding: 0;
        }
        .invoice-container {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0;
        }
        .invoice-table {
            page-break-inside: avoid;
        }
        .invoice-totals {
            page-break-inside: avoid;
        }
        .footer {
            page-break-inside: avoid;
        }
        @page {
            margin: 0.5cm;
        }
    }
</style>