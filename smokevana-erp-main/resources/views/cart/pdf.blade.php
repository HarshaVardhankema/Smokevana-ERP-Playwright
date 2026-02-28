<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cart PDF</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            width: 100%;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }
        .header-table td {
            border: none;
            padding: 5px;
            vertical-align: middle;
            margin: 0;
        }
        .header-table tr {
            height: 60px;
        }
        .header-logo-cell {
            width: 25%;
            text-align: left;
        }
        .header-title-cell {
            text-align: center;
            width: 50%;
        }
        .header-spacer-cell {
            width: 25%;
        }
        .header-logo img {
            width: 60px !important;
            height: auto !important;
            max-width: 60px !important;
            max-height: 40px !important;
            object-fit: contain !important;
            display: block !important;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header-date {
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            vertical-align: middle;
        }
        .customer-info {
            margin-bottom: 20px;
        }
        .customer-info h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .customer-info p {
            margin: 2px 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            page-break-inside: auto;
        }
        table thead {
            background-color: #f5f5f5;
            display: table-header-group;
        }
        table tbody {
            display: table-row-group;
        }
        table th {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 11px;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        table tbody tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .summary {
            margin-top: 8px;
            width: 280px;
            margin-left: auto;
        }
        .summary table {
            margin-bottom: 0;
        }
        .summary-row {
            padding: 5px 8px;
        }
        .total-row {
            font-weight: bold;
            font-size: 13px;
            border-top: 2px solid #333;
            padding-top: 8px;
        }
        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-logo-cell" style="width: 25%; padding: 5px; vertical-align: middle;">
                    @if(isset($business_logo) && $business_logo)
                        <img src="{{ $business_logo }}" alt="Business Logo" style="width: 60px; height: auto; max-width: 100px; max-height: 80px; object-fit: contain; display: block;">
                    @endif
                </td>
                <td class="header-title-cell" style="width: 50%; text-align: center; vertical-align: middle;">
                    <h1 style="margin: 0; text-align: center;">Shopping Cart</h1>
                    <p class="header-date" style="margin: 5px 0 0 0; text-align: center;">Date: {{ $date ?? now()->format('F d, Y') }}</p>
                </td>
                <td class="header-spacer-cell" style="width: 25%;"></td>
            </tr>
        </table>
    </div>

    @if(isset($contact) && $contact)
    <div class="customer-info">
        <h3>Customer Information</h3>
        <p><strong>Name:</strong> {{ $contact->name ?? ($contact->first_name ?? '') . ' ' . ($contact->last_name ?? '') }}</p>
        @if(isset($contact->email) && $contact->email)
            <p><strong>Email:</strong> {{ $contact->email }}</p>
        @endif
        @if(isset($contact->mobile) && $contact->mobile)
            <p><strong>Phone:</strong> {{ $contact->mobile }}</p>
        @endif
        @if(isset($contact->company_name) && $contact->company_name)
            <p><strong>Company:</strong> {{ $contact->company_name }}</p>
        @endif
    </div>
    @endif

    @if(isset($cart_items) && is_array($cart_items) && count($cart_items) > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 10%;">Image</th>
                <th style="width: 30%;">Product Name</th>
                <th style="width: 10%;">SKU</th>
                <th style="width: 5%;" class="text-right">Qty</th>
                <th style="width: 10%;" class="text-right">Unit Price</th>
                <th style="width: 12%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cart_items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: center;">
                        @if(isset($item['product_image']) && $item['product_image'])
                            <img src="{{ $item['product_image'] }}" alt="{{ $item['product_name'] ?? 'Product' }}" class="product-image">
                        @else
                            <span style="color: #999;">-</span>
                        @endif
                    </td>
                    <td>
                        {{ $item['product_name'] ?? 'N/A' }}
                        @if(isset($item['variation_name']) && $item['variation_name'])
                            <br><small style="color: #666;">{{ $item['variation_name'] }}</small>
                        @endif
                    </td>
                    <td>{{ $item['sku'] ?? 'N/A' }}</td>
                    <td class="text-right">{{ $item['qty'] ?? 0 }}</td>
                    <td class="text-right">${{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                    <td class="text-right">${{ number_format($item['total_price'] ?? 0, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="padding: 20px; text-align: center; color: #999;">
        <p>No items in cart</p>
    </div>
    @endif

    <div class="summary">
        <table>
            <tr>
                <td class="summary-row">Subtotal:</td>
                <td class="summary-row text-right">${{ number_format($subtotal ?? 0, 2) }}</td>
            </tr>
            @if(isset($total_tax) && $total_tax > 0)
            <tr>
                <td class="summary-row">Tax:</td>
                <td class="summary-row text-right">${{ number_format($total_tax, 2) }}</td>
            </tr>
            @endif
            @if(isset($cart_discount_amount) && $cart_discount_amount > 0)
            <tr>
                <td class="summary-row">Discount:</td>
                <td class="summary-row text-right">-${{ number_format($cart_discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="summary-row total-row">Total:</td>
                <td class="summary-row total-row text-right">${{ number_format($subtotal_inc_tax ?? 0, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Total Items: {{ $item_count ?? 0 }}</p>
        <p>This is a printable copy of your shopping cart.</p>
    </div>
</body>
</html>
