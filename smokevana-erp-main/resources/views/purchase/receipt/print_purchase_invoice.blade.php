@php
	$pdf_generation_for = ['Original for Buyer'];
@endphp

@foreach($pdf_generation_for as $pdf_for)
	<link rel="stylesheet" href="css/app.css">
	<style type="text/css">
         body {
			font-family: Arial, sans-serif;
			margin: 0;
			/* padding: 20px; */
			font-size: 12px;
			line-height: 1.4;
		}
		
		.header {
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
			margin-bottom: 30px;
			border-bottom: 2px solid #333;
			padding-bottom: 20px;
		}
		
		.business-info {
			flex: 1;
		}
		
		.logo-section {
			text-align: center;
			flex: 1;
		}
		
		.invoice-title {
			text-align: right;
			flex: 1;
		}
		
		.invoice-title h1 {
			font-size: 24px;
			font-weight: bold;
			margin: 0 0 5px 0;
			color: #333;
		}
		
		.business-name {
			font-size: 18px;
			font-weight: bold;
			margin-bottom: 5px;
			color: #333;
		}
		
		.address {
			color: #666;
			margin-bottom: 5px;
		}
		
		.contact-info {
			color: #666;
			font-size: 11px;
		}
		
		.parties-section {
			display: flex;
			justify-content: space-between;
			margin-bottom: 30px;
		}
		
		.party-box {
			flex: 1;
			margin: 0 10px;
			padding: 15px;
			border: 1px solid #ddd;
			border-radius: 5px;
		}
		
		.party-box:first-child {
			margin-left: 0;
		}
		
		.party-box:last-child {
			margin-right: 0;
		}
		
		.party-title {
			font-weight: bold;
			font-size: 14px;
			margin-bottom: 10px;
			color: #333;
			border-bottom: 1px solid #eee;
			padding-bottom: 5px;
		}
		
		.party-content {
			color: #666;
			font-size: 11px;
		}
		
		.info-table {
			width: 100%;
			margin-bottom: 30px;
			border-collapse: collapse;
		}
		
		.info-table th {
			background-color: #f8f9fa;
			padding: 8px;
			text-align: left;
			font-weight: bold;
			border: 1px solid #ddd;
			font-size: 11px;
		}
		
		.info-table td {
			padding: 8px;
			border: 1px solid #ddd;
			font-size: 11px;
		}
		
		.products-table {
			width: 100%;
			margin-bottom: 30px;
			border-collapse: collapse;
		}
		
		.products-table th {
			background-color: #f8f9fa;
			padding: 10px 8px;
			text-align: left;
			font-weight: bold;
			border: 1px solid #ddd;
			font-size: 11px;
		}
		
		.products-table td {
			padding: 10px 8px;
			border: 1px solid #ddd;
			font-size: 11px;
		}
		
		.totals-section {
			text-align: right;
			margin-bottom: 30px;
		}
		
		.total-row {
			display: flex;
			justify-content: space-between;
			margin-bottom: 5px;
			max-width: 300px;
			margin-left: auto;
		}
		
		.total-label {
			font-weight: bold;
			color: #333;
		}
		
		.total-value {
			color: #666;
		}
		
		.total-due {
			font-size: 16px;
			font-weight: bold;
			color: #333;
		}
		
		.footer {
			text-align: center;
			color: #999;
			font-size: 10px;
			margin-top: 40px;
			padding-top: 20px;
			border-top: 1px solid #eee;
		}
		
		.date-time {
			color: #999;
			font-size: 10px;
		}
		
		.invoice-number {
			font-size: 14px;
			font-weight: bold;
			color: #333;
		}
		
		.footer-section {
			display: flex;
			justify-content: space-between;
			align-items: flex-end;
			margin-top: 40px;
		}
		
		.footer-text {
			flex: 1;
		}
		
		.barcode-section {
			text-align: center;
			flex: 1;
		}
		
		.barcode-section img {
			max-width: 100%;
			height: auto;
		}
	</style>
	
	<div class="header">
		<div class="business-info">
			<div class="business-name">{{ $purchase->business->name }}</div>
			<div class="address">
				@if(!empty($purchase->location->city) || !empty($purchase->location->state) || !empty($purchase->location->country))
					{{implode(', ', array_filter([$purchase->location->city, $purchase->location->state, $purchase->location->country]))}}
				@endif
				@if(!empty($purchase->location->zip_code))
					, {{$purchase->location->zip_code}}
				@endif
				@if(!empty($purchase->location->country))
					, {{$purchase->location->country}}
				@endif
			</div>
			<div class="contact-info">
				@if(!empty($location_details->website))
					Website: {{$location_details->website}}<br>
				@endif
				@if(!empty($location_details->email))
					Email: {{$location_details->email}}
				@endif
			</div>
		</div>
		 <!-- Logo (Centered) -->
		 <div style=" text-align: center;">
			@if (!empty($purchase->logo))
				<img src="{{ $purchase->logo }}" alt="Company Logo" style="max-height: 84px;">
			@endif
		</div>
		
		{{-- <div class="logo-section">
			@if (!empty($purchase->logo))
				<img src="{{ $purchase->business->logo }}" alt="Company Logo" style="max-height: 84px;">
			@endif
		</div> --}}
		
		<div class="invoice-title">
			<div class="date-time">{{ @format_date($purchase->transaction_date) }} {{ @format_time($purchase->transaction_date) }}</div>
            @if ($purchase->type == 'purchase_order')
                <h1>PURCHASE ORDER</h1>
            @else
                <h1>PURCHASE RECEIPT</h1>
            @endif
			<div class="invoice-number">{{ $purchase->ref_no }}</div>
		</div>
	</div>
	
	<div class="parties-section">
		<div class="party-box">
			<div class="party-title">ISSUED</div>
			<div class="party-content">
				<strong>Date:</strong> {{ @format_date($purchase->transaction_date) }} {{ @format_time($purchase->transaction_date) }}
			</div>
		</div>
		
		<div class="party-box">
			<div class="party-title">SUPPLIER</div>
			<div class="party-content">
				@if(!empty($purchase->contact->supplier_business_name))
					{{$purchase->contact->supplier_business_name}}<br>
				@endif
				@if(!empty($purchase->contact->name))
					{{$purchase->contact->name}}<br>
				@endif
				@if(!empty($purchase->contact->address_line_1))
					{{$purchase->contact->address_line_1}}<br>
				@endif
				@if(!empty($purchase->contact->address_line_2))
					{{$purchase->contact->address_line_2}}<br>
				@endif
				@if(!empty($purchase->contact->city) || !empty($purchase->contact->state) || !empty($purchase->contact->country))
					{{implode(', ', array_filter([$purchase->contact->city, $purchase->contact->state, $purchase->contact->country]))}}
				@endif
				@if(!empty($purchase->contact->zip_code))
					, {{$purchase->contact->zip_code}}
				@endif
				@if(!empty($purchase->contact->mobile))
					<br><strong>Mobile:</strong> {{$purchase->contact->mobile}}
				@endif
			</div>
		</div>
		
		<div class="party-box">
			<div class="party-title">DELIVER TO</div>
			<div class="party-content">
				{{ $purchase->location->name }}<br>
				@if(!empty($purchase->location->landmark))
					{{$purchase->location->landmark}}<br>
				@endif
				{!! $purchase->location->location_address !!}
			</div>
		</div>
	</div>
	
	<table class="info-table">
		<tr>
			<th>Terms</th>
			<th>Status</th>
			<th>Prepared By</th>
			<th>Representative</th>
			<th>Tax ID</th>
		</tr>
		<tr>
			<td>Payment In Advance</td>
			<td>{{ ucfirst($purchase->status) }}</td>
			<td>{{$purchase->sales_person->user_full_name ?? '-'}}</td>
			<td>-</td>
			<td>{{$purchase->business->tax_number_1 ?? '-'}}</td>
		</tr>
	</table>
	
	<table class="products-table">
		<thead>
			<tr>
				<th>SKU</th>
				<th>Product</th>
				<th>Quantity</th>
				<th>Unit Price</th>
				<th>Subtotal</th>
			</tr>
		</thead>
		<tbody>
			@php 
				$total = 0.00;
				$tax_array = [];
			@endphp
			@foreach($purchase->purchase_lines as $purchase_line)
				@php 
					$line_total = $purchase_line->quantity * $purchase_line->purchase_price;
					$total += $line_total;
					if (!empty($purchase_line->tax_id)) {
						$tax_array[$purchase_line->tax_id][] = ($purchase_line->item_tax * $purchase_line->quantity);
					}
				@endphp
				<tr>
					<td>{{$purchase_line->variations->sub_sku ?? '-'}}</td>
					<td>
						{{ $purchase_line->product->name }}
						@if( $purchase_line->product->type == 'variable')
							- {{ $purchase_line->variations->product_variation->name}}
							- {{ $purchase_line->variations->name}}
						@endif
					</td>
					<td>{{@format_quantity($purchase_line->quantity)}} @if(!empty($purchase_line->sub_unit)) {{$purchase_line->sub_unit->actual_name}} @else {{$purchase_line->product->unit->actual_name}} @endif</td>
					<td>@format_currency($purchase_line->purchase_price)</td>
					<td>@format_currency($line_total)</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	
    <div class="totals-section">
		<div class="total-row">
			<span class="total-label">Total Due</span>
			<span class="total-value total-due">@format_currency($purchase->final_total)</span>
		</div>
		<div class="total-row">
			<span class="total-label">Subtotal:</span>
			<span class="total-value">@format_currency($total)</span>
		</div>
		@if(!empty($tax_array))
			@foreach($tax_array as $key => $value)
				<div class="total-row">
					<span class="total-label">{{$taxes->where('id', $key)->first()->name}} ({{$taxes->where('id', $key)->first()->amount}}%):</span>
					<span class="total-value">@format_currency(array_sum($value))</span>
				</div>
			@endforeach
		@endif
		<div class="total-row">
			<span class="total-label">Total:</span>
			<span class="total-value">@format_currency($purchase->final_total)</span>
		</div>
	</div>
	
	@if(!empty($total_in_words))
	<div style="margin-bottom: 20px; font-style: italic; color: #666;">
		<strong>Amount in words:</strong> {!!ucfirst($total_in_words)!!}
	</div>
	@endif
	
	@if(!empty($purchase->additional_notes))
	<div style="margin-bottom: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
		<strong>Notes:</strong><br>
		{{ $purchase->additional_notes }}
	</div>
	@endif

	<div class="row" style="color: #000000 !important;">
		@if (!empty($purchase->footer_text))
			<div class="@if ($purchase->show_barcode || $purchase->show_qr_code) col-xs-8 @else col-xs-12 @endif">
				{!! $purchase->footer_text !!}
			</div>
		@endif
		@if ($purchase->show_barcode || $purchase->show_qr_code)
			<div class="@if (!empty($purchase->footer_text)) col-xs-4 @else col-xs-12 @endif text-center">
				@if ($purchase->show_barcode)
					<img class="center-block"
						src="data:image/png;base64,{{ DNS1D::getBarcodePNG($purchase->ref_no, 'C128', 2, 30, [39, 48, 54], true) }}">
				@endif

				@if ($purchase->show_qr_code && !empty($purchase->qr_code_text))
					<img class="center-block mt-5"
						src="data:image/png;base64,{{ DNS2D::getBarcodePNG($purchase->qr_code_text, 'QRCODE', 3, 3, [39, 48, 54]) }}">
				@endif
			</div>
		@endif
	</div>
	
	
@endforeach