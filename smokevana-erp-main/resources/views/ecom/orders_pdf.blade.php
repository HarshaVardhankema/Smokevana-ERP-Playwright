<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #111;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            border: 1px solid #ddd;
            padding: 20px;
            max-width: 100%;
        }
        .header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        .logo-section {
            text-align: center;
            margin-bottom: 10px;
        }
        .logo-section img {
            max-width: 60px;
            max-height: 20px;
            height: auto;
        }
        .header-info {
            text-align: center;
            width: 100%;
        }
        .header-title {
            color: #e47911;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 0;
            text-align: center;
        }
        .order-summary {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .order-summary div {
            margin-bottom: 3px;
        }
        .section-header {
            font-weight: bold;
            font-size: 13px;
            margin: 20px 0 10px 0;
            text-align: center;
            color: #111;
        }
        .shipped-box {
            border: 1px solid #000;
            padding: 10px;
            margin: 15px 0;
            text-align: center;
            font-weight: normal;
            font-size: 12px;
        }
        .items-section {
            margin: 20px 0;
        }
        .items-header {
            font-weight: bold;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
            margin-bottom: 10px;
        }
        .items-header-left {
            float: left;
            width: 80%;
        }
        .items-header-right {
            float: right;
            width: 15%;
            text-align: right;
        }
        .items-header:after {
            content: "";
            display: table;
            clear: both;
        }
        .item-row {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            overflow: hidden;
        }
        .item-details {
            float: left;
            width: 80%;
            padding-right: 20px;
        }
        .item-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .item-meta {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .item-price {
            float: right;
            width: 15%;
            text-align: right;
            font-weight: bold;
        }
        .item-row:after {
            content: "";
            display: table;
            clear: both;
        }
        .shipping-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .shipping-row {
            margin-bottom: 8px;
        }
        .shipping-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .pricing-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .pricing-row {
            padding: 5px 0;
            overflow: hidden;
        }
        .pricing-label {
            float: left;
            width: 70%;
        }
        .pricing-value {
            float: right;
            width: 25%;
            text-align: right;
        }
        .pricing-row:after {
            content: "";
            display: table;
            clear: both;
        }
        .total-row {
            border-top: 2px solid #ddd;
            border-bottom: 2px solid #ddd;
            padding: 10px 0;
            margin: 10px 0;
            font-weight: bold;
            font-size: 13px;
        }
        .payment-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    @foreach($orders as $orderIndex => $order)
        <div class="container {{ $orderIndex > 0 ? 'page-break' : '' }}">
            <!-- Header with Logo -->
            <div class="header">
                <div style="text-align: left;" float="left" width="10%" class="logo-section">
                    @if(!empty($business_logo))
                        <img src="{{ $business_logo }}" alt="{{ $business->name ?? 'Business Logo' }}" />
                    @elseif(!empty($business->name))
                        <div style="font-size: 12px; font-weight: bold; color: #111;">{{ $business->name }}</div>
                    @endif
                </div>
                <div class="header-info">
                    <div class="header-title">Final Details for Order #{{ $order['order_number'] ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <div><strong>Order Placed:</strong> 
                    @php
                        $orderDate = $order['transaction_date'] ?? 'N/A';
                        if ($orderDate != 'N/A' && $orderDate) {
                            try {
                                $date = \Carbon\Carbon::parse($orderDate);
                                echo $date->format('F j, Y');
                            } catch (\Exception $e) {
                                echo $orderDate;
                            }
                        } else {
                            echo 'N/A';
                        }
                    @endphp
                </div>
                <div><strong>{{ $business->name ?? 'Order' }} order number:</strong> {{ $order['order_number'] ?? 'N/A' }}</div>
                <div><strong>Order Total:</strong> ${{ number_format($order['pricing']['final_total'] ?? 0, 2) }}</div>
            </div>

            @if(!empty($order['shipping_status']) && $order['shipping_status'] != 'pending')
                <div class="shipped-box">Shipped on 
                    @php
                        $shippedDate = $order['transaction_date'] ?? 'N/A';
                        if ($shippedDate != 'N/A' && $shippedDate) {
                            try {
                                $date = \Carbon\Carbon::parse($shippedDate);
                                echo $date->format('F j, Y');
                            } catch (\Exception $e) {
                                echo $shippedDate;
                            }
                        } else {
                            echo 'N/A';
                        }
                    @endphp
                </div>
            @endif

            <!-- Items Ordered Section -->
            <div class="items-section">
                <div class="items-header">
                    <div class="items-header-left">Items Ordered</div>
                    <div class="items-header-right">Price</div>
                </div>
                
                @if(!empty($order['items']) && is_array($order['items']))
                    @foreach($order['items'] as $item)
                        <div class="item-row">
                            <div class="item-details">
                                <div class="item-title">
                                    {{ $item['quantity'] ?? 1 }} of: {{ $item['product_name'] ?? 'N/A' }}
                                    @if(!empty($item['variation_name']))
                                        - {{ $item['variation_name'] }}
                                    @endif
                                </div>
                                @if(!empty($item['variation_sku']))
                                    <div class="item-meta">SKU: {{ $item['variation_sku'] }}</div>
                                @endif
                                <div class="item-meta">
                                    Condition: New
                                    @if($item['received_quantity'] ?? 0 > 0)
                                        | Received: {{ $item['received_quantity'] }}/{{ $item['quantity'] }}
                                    @endif
                                </div>
                            </div>
                            <div class="item-price">
                                ${{ number_format($item['unit_price_inc_tax'] ?? 0, 2) }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="item-row">
                        <div class="item-details">No items found</div>
                    </div>
                @endif
            </div>

            <!-- Pricing Breakdown -->
            <div class="pricing-section">
                <div class="pricing-row">
                    <div class="pricing-label">Item(s) Subtotal:</div>
                    <div class="pricing-value">${{ number_format($order['pricing']['subtotal'] ?? 0, 2) }}</div>
                </div>
                <div class="pricing-row">
                    <div class="pricing-label">Shipping & Handling:</div>
                    <div class="pricing-value">${{ number_format($order['pricing']['shipping_charges'] ?? 0, 2) }}</div>
                </div>
                <div class="pricing-row">
                    <div class="pricing-label">Total before tax:</div>
                    <div class="pricing-value">${{ number_format(($order['pricing']['subtotal'] ?? 0) + ($order['pricing']['shipping_charges'] ?? 0), 2) }}</div>
                </div>
                <div class="pricing-row">
                    <div class="pricing-label">Sales Tax:</div>
                    <div class="pricing-value">${{ number_format($order['pricing']['tax_amount'] ?? 0, 2) }}</div>
                </div>
                @if(!empty($order['pricing']['discount_amount']) && $order['pricing']['discount_amount'] > 0)
                    <div class="pricing-row">
                        <div class="pricing-label">Discount:</div>
                        <div class="pricing-value">-${{ number_format($order['pricing']['discount_amount'] ?? 0, 2) }}</div>
                    </div>
                @endif
                <div class="pricing-row total-row">
                    <div class="pricing-label">Total for This Shipment:</div>
                    <div class="pricing-value">${{ number_format($order['pricing']['final_total'] ?? 0, 2) }}</div>
                </div>
            </div>

            <!-- Shipping Information -->
            @if(!empty($order['location']))
                <div class="shipping-section">
                    <div class="shipping-row">
                        <span class="shipping-label">Shipping Address:</span>
                        <span>{{ $order['location']['name'] ?? 'N/A' }}</span>
                    </div>
                    @if(!empty($order['shipping_status']))
                        <div class="shipping-row">
                            <span class="shipping-label">Shipping Status:</span>
                            <span>{{ ucfirst($order['shipping_status']) }}</span>
                        </div>
                    @endif
                    @if(!empty($order['picking_status']))
                        <div class="shipping-row">
                            <span class="shipping-label">Picking Status:</span>
                            <span>{{ ucfirst($order['picking_status']) }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Payment Information -->
            <div class="section-header">Payment Information</div>
            <div class="payment-section">
                @if(!empty($order['payment']['payment_methods']) && count($order['payment']['payment_methods']) > 0)
                    @foreach($order['payment']['payment_methods'] as $payment)
                        <div class="shipping-row">
                            <span class="shipping-label">Payment Method:</span>
                            <span>{{ ucfirst($payment['method'] ?? 'N/A') }} | Amount: ${{ number_format($payment['amount'] ?? 0, 2) }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="shipping-row">
                        <span class="shipping-label">Payment Status:</span>
                        <span>{{ ucfirst($order['payment_status'] ?? 'Due') }}</span>
                    </div>
                @endif
                
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                    <div class="pricing-row">
                        <div class="pricing-label">Item(s) Subtotal:</div>
                        <div class="pricing-value">${{ number_format($order['pricing']['subtotal'] ?? 0, 2) }}</div>
                    </div>
                    <div class="pricing-row">
                        <div class="pricing-label">Shipping & Handling:</div>
                        <div class="pricing-value">${{ number_format($order['pricing']['shipping_charges'] ?? 0, 2) }}</div>
                    </div>
                    <div class="pricing-row">
                        <div class="pricing-label">Total before tax:</div>
                        <div class="pricing-value">${{ number_format(($order['pricing']['subtotal'] ?? 0) + ($order['pricing']['shipping_charges'] ?? 0), 2) }}</div>
                    </div>
                    <div class="pricing-row">
                        <div class="pricing-label">Estimated Tax:</div>
                        <div class="pricing-value">${{ number_format($order['pricing']['tax_amount'] ?? 0, 2) }}</div>
                    </div>
                    <div class="pricing-row total-row">
                        <div class="pricing-label">Grand Total:</div>
                        <div class="pricing-value">${{ number_format($order['pricing']['final_total'] ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div>Generated on: {{ $generated_at }}</div>
                @if(!empty($business->name))
                    <div style="margin-top: 5px;">© {{ date('Y') }}, {{ $business->name }}</div>
                @endif
            </div>
        </div>
    @endforeach
</body>
</html>
