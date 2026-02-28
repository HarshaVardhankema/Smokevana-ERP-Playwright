{{-- Minimal Packing Slip - Print Ready --}}
{{-- No branding, company details, or pricing - Only essential operational information --}}

<style>
    @media print {
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
    .packing-slip { font-family: Arial, sans-serif; color: #000; max-width: 800px; margin: 0 auto; padding: 20px; }
    .packing-slip * { box-sizing: border-box; }
    .slip-header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
    .slip-title { font-size: 28px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; margin: 0 0 15px 0; }
    .slip-meta { display: flex; justify-content: center; gap: 40px; font-size: 14px; }
    .slip-meta-item { text-align: center; }
    .slip-meta-label { font-weight: bold; color: #333; }
    .slip-meta-value { font-size: 16px; margin-top: 2px; }
    .customer-section { margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; }
    .section-title { font-size: 14px; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #999; padding-bottom: 5px; margin-bottom: 10px; color: #333; }
    .customer-grid { display: flex; gap: 30px; }
    .customer-col { flex: 1; }
    .customer-label { font-weight: bold; font-size: 12px; color: #555; margin-bottom: 3px; }
    .customer-value { font-size: 14px; line-height: 1.4; }
    .products-section { margin-bottom: 20px; }
    .products-table { width: 100%; border-collapse: collapse; }
    .products-table th { background-color: #333; color: #fff; padding: 10px 12px; text-align: left; font-size: 13px; font-weight: bold; text-transform: uppercase; }
    .products-table th:first-child { width: 8%; text-align: center; }
    .products-table th:last-child { width: 15%; text-align: center; }
    .products-table td { padding: 10px 12px; border-bottom: 1px solid #ddd; font-size: 13px; vertical-align: top; }
    .products-table td:first-child { text-align: center; font-weight: bold; }
    .products-table td:last-child { text-align: center; font-weight: bold; }
    .products-table tbody tr:nth-child(even) { background-color: #f9f9f9; }
    .products-table tbody tr:hover { background-color: #f0f0f0; }
    .product-name { font-weight: 500; }
    .product-sku { font-size: 11px; color: #666; margin-top: 2px; }
    .barcode-section { text-align: center; margin-top: 25px; padding-top: 15px; border-top: 1px dashed #ccc; }
    .total-items { font-size: 14px; font-weight: bold; text-align: right; margin-top: 10px; padding: 8px 12px; background: #f5f5f5; }
</style>

<div class="packing-slip">
    {{-- Header Section --}}
    <div class="slip-header">
        <h1 class="slip-title">Packing Slip</h1>
        <div class="slip-meta">
            <div class="slip-meta-item">
                <div class="slip-meta-label">Invoice No.</div>
                <div class="slip-meta-value">{{ $receipt_details->invoice_no }}</div>
            </div>
            <div class="slip-meta-item">
                <div class="slip-meta-label">Date</div>
                <div class="slip-meta-value">{{ $receipt_details->invoice_date }}</div>
            </div>
            @if(!empty($receipt_details->invoice_time))
            <div class="slip-meta-item">
                <div class="slip-meta-label">Time</div>
                <div class="slip-meta-value">{{ $receipt_details->invoice_time }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Customer Details Section --}}
    <div class="customer-section">
        <div class="section-title">Delivery Information</div>
        <div class="customer-grid">
            <div class="customer-col">
                <div class="customer-label">Customer Name</div>
                <div class="customer-value">
                    @if(!empty($receipt_details->customer_name))
                        {{ $receipt_details->customer_name }}
                    @elseif(!empty($receipt_details->customer_info))
                        {!! strip_tags($receipt_details->customer_info, '<br>') !!}
                    @else
                        -
                    @endif
                </div>
                @if(!empty($receipt_details->customer_mobile))
                <div style="margin-top: 8px;">
                    <div class="customer-label">Contact</div>
                    <div class="customer-value">{{ $receipt_details->customer_mobile }}</div>
                </div>
                @endif
            </div>
            <div class="customer-col">
                <div class="customer-label">Shipping Address</div>
                <div class="customer-value">
                    @if(!empty($receipt_details->shipping_address))
                        {!! $receipt_details->shipping_address !!}
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Products Section --}}
    <div class="products-section">
        <div class="section-title">Items to Pack</div>
        <table class="products-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                @php $totalQty = 0; @endphp
                @forelse($receipt_details->lines as $line)
                    @php $totalQty += floatval($line['quantity']); @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="product-name">
                                {{ $line['name'] }}
                                @if(!empty($line['product_variation']) && $line['product_variation'] != 'DUMMY')
                                    - {{ $line['product_variation'] }}
                                @endif
                                @if(!empty($line['variation']) && $line['variation'] != 'DUMMY')
                                    {{ $line['variation'] }}
                                @endif
                            </div>
                            @if(!empty($line['sub_sku']))
                                <div class="product-sku">SKU: {{ $line['sub_sku'] }}</div>
                            @endif
                        </td>
						   <td>{{ $line['quantity'] }} {{ $line['units'] ?? '' }}</td>
                    </tr>
                    @if(!empty($line['modifiers']))
                        @foreach($line['modifiers'] as $modifier)
                            <tr>
                                <td></td>
                                <td style="padding-left: 25px; font-style: italic; color: #666;">
                                    + {{ $modifier['name'] }} {{ $modifier['variation'] ?? '' }}
                                </td>
                                <td>{{ $modifier['quantity'] }} {{ $modifier['units'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    @endif
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 20px; color: #999;">No items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="total-items">
            Total Items: {{ count($receipt_details->lines) }} | Total Quantity: {{ $totalQty }}
        </div>
    </div>

    {{-- Barcode Section --}}
    @if($receipt_details->show_barcode)
    <div class="barcode-section">
        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2, 30, array(0, 0, 0), true) }}" alt="Barcode">
        <div style="font-size: 11px; margin-top: 3px;">{{ $receipt_details->invoice_no }}</div>
    </div>
    @endif

</div>