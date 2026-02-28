{{-- Shipping / Packing Slip PDF - Amazon-style with optional pricing (hidden for gift orders) --}}
<style>
    @media print { * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; } }
    body { font-family: Arial, sans-serif; color: #000; font-size: 12px; margin: 0; padding: 16px; }
    .slip { max-width: 800px; margin: 0 auto; }
    .slip * { box-sizing: border-box; }
    .header { display: flex; align-items: flex-start; justify-content: space-between; width: 100%; border-bottom: 1px solid #ddd; padding-bottom: 12px; margin-bottom: 16px; }
    .header-left { flex: 1; text-align: left; }
    .header-right { text-align: right; min-width: 160px; }
    .brand-logo { max-height: 40px; max-width: 150px; display: block; }
    .brand-name { font-size: 18px; font-weight: bold; color: #232f3e; margin-bottom: 4px; }
    .support-link { font-size: 11px; color: #666; margin-top: 4px; }
    .doc-title { font-size: 20px; font-weight: bold; color: #232f3e; }
    .meta-row { margin-bottom: 14px; }
    .meta-grid { display: table; width: 100%; font-size: 12px; }
    .meta-cell { display: table-cell; padding-right: 24px; }
    .meta-label { font-weight: bold; color: #333; margin-bottom: 2px; }
    .meta-value { color: #000; }
    .ship-to { margin-bottom: 16px; padding: 12px; border: 1px solid #ddd; background: #fafafa; }
    .ship-to-title { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #333; margin-bottom: 8px; }
    .ship-to-address { line-height: 1.5; }
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    table.items th { background: #232f3e; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; font-weight: bold; text-transform: uppercase; }
    table.items th.num { width: 6%; text-align: center; }
    table.items th.desc { }
    table.items th.qty { width: 10%; text-align: center; }
    table.items th.price { width: 14%; text-align: right; }
    table.items th.subtotal { width: 14%; text-align: right; }
    table.items td { padding: 8px 10px; border-bottom: 1px solid #eee; vertical-align: top; }
    table.items td.num { text-align: center; font-weight: bold; }
    table.items td.qty { text-align: center; }
    table.items td.price, table.items td.subtotal { text-align: right; }
    .product-name { font-weight: 500; }
    .product-sku { font-size: 10px; color: #666; margin-top: 2px; }
    .summary { margin-top: 16px; padding: 12px; border: 1px solid #ddd; background: #f9f9f9; max-width: 320px; margin-left: auto; }
    .summary-row { display: table; width: 100%; padding: 4px 0; font-size: 12px; }
    .summary-label { display: table-cell; }
    .summary-value { display: table-cell; text-align: right; font-weight: bold; }
    .summary-row.total .summary-value { font-size: 14px; }
</style>

<div class="slip">
    <div class="header">
        <div class="header-left">
            @if(!empty($receipt_details->logo))
                <img src="{{ $receipt_details->logo }}" alt="" class="brand-logo" />
            @else
                <div class="brand-name">{{ $receipt_details->business_name ?? 'Packing Slip' }}</div>
            @endif
            <div class="support-link">For customer support visit {{ config('app.url', request()->getSchemeAndHttpHost()) }}</div>
        </div>
        <div class="header-right">
            <div class="doc-title">Packing slip</div>
        </div>
    </div>

    <div class="meta-row">
        <div class="meta-grid">
            <div class="meta-cell">
                <div class="meta-label">Order date</div>
                <div class="meta-value">{{ $receipt_details->invoice_date }}</div>
            </div>
            <div class="meta-cell">
                <div class="meta-label">Purchase Order #</div>
                <div class="meta-value">{{ $receipt_details->purchase_order_no ?? '—' }}</div>
            </div>
            <div class="meta-cell">
                <div class="meta-label">Order #</div>
                <div class="meta-value">{{ $receipt_details->invoice_no }}</div>
            </div>
            <div class="meta-cell">
                <div class="meta-label">Date shipped</div>
                <div class="meta-value">{{ $receipt_details->invoice_date }}</div>
            </div>
        </div>
    </div>

    <div class="ship-to">
        <div class="ship-to-title">Ship to</div>
        <div class="ship-to-address">
            @if(!empty($receipt_details->customer_name))
                <strong>{{ $receipt_details->customer_name }}</strong><br/>
            @endif
            @if(!empty($receipt_details->shipping_address))
                {!! $receipt_details->shipping_address !!}
            @else
                —
            @endif
        </div>
    </div>

    @php $showPrices = empty($receipt_details->hide_prices_for_recipient); @endphp

    <table class="items">
        <thead>
            <tr>
                <th class="num">#</th>
                <th class="desc">Item description</th>
                <th class="qty">Qty</th>
                @if($showPrices)
                    <th class="price">Item price</th>
                    <th class="subtotal">Item subtotal</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($receipt_details->lines as $line)
                <tr>
                    <td class="num">{{ $loop->iteration }}</td>
                    <td class="desc">
                        <div class="product-name">
                            {{ $line['name'] ?? '' }}
                            @if(!empty($line['product_variation']) && ($line['product_variation'] ?? '') != 'DUMMY')
                                - {{ $line['product_variation'] }}
                            @endif
                            @if(!empty($line['variation']) && ($line['variation'] ?? '') != 'DUMMY')
                                {{ $line['variation'] }}
                            @endif
                        </div>
                        @if(!empty($line['sub_sku']))
                            <div class="product-sku">SKU: {{ $line['sub_sku'] }}</div>
                        @endif
                    </td>
                    <td class="qty">{{ $line['quantity'] ?? 0 }} {{ $line['units'] ?? '' }}</td>
                    @if($showPrices)
                        <td class="price">{{ $line['unit_price_inc_tax'] ?? '—' }}</td>
                        <td class="subtotal">{{ $line['line_total'] ?? '—' }}</td>
                    @endif
                </tr>
                @if(!empty($line['modifiers']))
                    @foreach($line['modifiers'] as $mod)
                        <tr>
                            <td class="num"></td>
                            <td class="desc" style="padding-left: 20px; font-style: italic;">+ {{ $mod['name'] ?? '' }} {{ $mod['variation'] ?? '' }}</td>
                            <td class="qty">{{ $mod['quantity'] ?? 0 }} {{ $mod['units'] ?? '' }}</td>
                            @if($showPrices)
                                <td class="price">{{ $mod['unit_price_inc_tax'] ?? '—' }}</td>
                                <td class="subtotal">{{ $mod['line_total'] ?? '—' }}</td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="{{ $showPrices ? 5 : 3 }}" style="text-align: center; padding: 16px;">No items</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($showPrices)
        <div class="summary">
            <div class="summary-row">
                <span class="summary-label">Item subtotal</span>
                <span class="summary-value">{{ $receipt_details->subtotal ?? '0' }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Shipping &amp; handling</span>
                <span class="summary-value">{{ $receipt_details->shipping_charges ?? '0.00' }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Sales tax</span>
                <span class="summary-value">{{ $receipt_details->tax ?? '0.00' }}</span>
            </div>
            <div class="summary-row total">
                <span class="summary-label">Total</span>
                <span class="summary-value">{{ $receipt_details->total ?? '0' }}</span>
            </div>
        </div>
    @endif
</div>
