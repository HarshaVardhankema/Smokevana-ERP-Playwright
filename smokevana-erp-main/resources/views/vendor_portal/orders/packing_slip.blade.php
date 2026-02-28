<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing Slip - {{ $packingData->invoice_no }}</title>
    <style>
        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            body { margin: 0; padding: 10px; }
            .no-print { display: none !important; }
            .packing-slip { box-shadow: none !important; max-width: 100% !important; padding: 15px !important; }
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #000;
            font-size: 12px;
            line-height: 1.3;
        }
        
        .print-controls {
            position: fixed;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 8px;
            z-index: 1000;
        }
        
        .print-btn {
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .print-btn.primary { background: #4f46e5; color: #fff; }
        .print-btn.secondary { background: #fff; color: #374151; border: 1px solid #d1d5db; }
        
        .packing-slip {
            max-width: 600px;
            margin: 15px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        /* Header - Compact */
        .slip-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        
        .slip-title {
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        
        .slip-meta {
            display: flex;
            justify-content: center;
            gap: 30px;
            font-size: 11px;
        }
        
        .slip-meta-item { text-align: center; }
        .slip-meta-label { font-weight: 600; color: #666; text-transform: uppercase; }
        .slip-meta-value { font-size: 13px; font-weight: 700; margin-top: 2px; }
        
        /* Customer Info - Compact */
        .customer-section {
            display: flex;
            gap: 15px;
            margin-bottom: 12px;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
        }
        
        .customer-col { flex: 1; }
        .customer-label { font-size: 10px; font-weight: 600; color: #666; text-transform: uppercase; margin-bottom: 2px; }
        .customer-value { font-size: 12px; line-height: 1.4; }
        
        /* Tracking - Compact */
        .tracking-box {
            background: #f0f7ff;
            border: 1px dashed #3b82f6;
            padding: 8px 12px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
        }
        
        .tracking-label { font-weight: 600; color: #1e40af; text-transform: uppercase; }
        .tracking-value { font-size: 14px; font-weight: 700; font-family: monospace; }
        
        /* Products Table - Compact */
        .products-table { width: 100%; border-collapse: collapse; }
        
        .products-table th {
            background: #333;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .products-table th:first-child { width: 35px; text-align: center; }
        .products-table th:last-child { width: 60px; text-align: center; }
        
        .products-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e5e5;
            font-size: 12px;
            vertical-align: middle;
        }
        
        .products-table td:first-child { text-align: center; font-weight: 600; color: #666; }
        .products-table td:last-child { text-align: center; font-weight: 700; }
        
        .products-table tbody tr:nth-child(even) { background: #fafafa; }
        
        .product-name { font-weight: 600; }
        .product-sku { font-size: 10px; color: #888; }
        
        /* Summary - Compact */
        .items-summary {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
            padding: 8px 10px;
            background: #333;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
        }
        
        /* Barcode - Compact */
        .barcode-section {
            text-align: center;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }
        
        .barcode-section img { max-height: 35px; }
        .barcode-text { font-size: 10px; font-family: monospace; color: #666; margin-top: 3px; }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button class="print-btn primary" onclick="window.print()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print
        </button>
        <button class="print-btn secondary" onclick="window.close()">Close</button>
    </div>

    <div class="packing-slip">
        <!-- Header -->
        <div class="slip-header">
            <h1 class="slip-title">Packing Slip</h1>
            <div class="slip-meta">
                <div class="slip-meta-item">
                    <div class="slip-meta-label">Order</div>
                    <div class="slip-meta-value">{{ $packingData->invoice_no }}</div>
                </div>
                <div class="slip-meta-item">
                    <div class="slip-meta-label">Date</div>
                    <div class="slip-meta-value">{{ $packingData->invoice_date }}</div>
                </div>
            </div>
        </div>

        <!-- Customer & Shipping Info -->
        <div class="customer-section">
            <div class="customer-col">
                <div class="customer-label">Ship To</div>
                <div class="customer-value">
                    <strong>{{ $packingData->customer_name }}</strong>
                    @if($packingData->customer_mobile)
                        <br>{{ $packingData->customer_mobile }}
                    @endif
                </div>
            </div>
            <div class="customer-col">
                <div class="customer-label">Address</div>
                <div class="customer-value">{!! nl2br(e($packingData->shipping_address)) !!}</div>
            </div>
        </div>

        <!-- Tracking Info -->
        @if($packingData->tracking_number)
        <div class="tracking-box">
            <div>
                <span class="tracking-label">Tracking:</span>
                <span class="tracking-value">{{ $packingData->tracking_number }}</span>
            </div>
            @if($packingData->carrier)
            <div>
                <span class="tracking-label">Carrier:</span>
                <span class="tracking-value">{{ $packingData->carrier }}</span>
            </div>
            @endif
        </div>
        @endif

        <!-- Products -->
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
                @forelse($packingData->lines as $line)
                    @php $totalQty += floatval($line['quantity']); @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <span class="product-name">
                                {{ $line['name'] }}
                                @if(!empty($line['product_variation']) && $line['product_variation'] != 'DUMMY')
                                    - {{ $line['product_variation'] }}
                                @endif
                                @if(!empty($line['variation']) && $line['variation'] != 'DUMMY')
                                    {{ $line['variation'] }}
                                @endif
                            </span>
                            @if(!empty($line['sub_sku']))
                                <span class="product-sku">({{ $line['sub_sku'] }})</span>
                            @endif
                        </td>
                        <td>{{ $line['quantity'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 20px; color: #999;">No items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="items-summary">
            <span>Items: {{ count($packingData->lines) }}</span>
            <span>Total Qty: {{ $totalQty }}</span>
        </div>

        <!-- Barcode -->
        @if($packingData->show_barcode)
        <div class="barcode-section">
            @php
                $barcodeImg = '';
                try {
                    $barcodeImg = \DNS1D::getBarcodePNG($packingData->invoice_no, 'C128', 2, 30, [0, 0, 0], true);
                } catch (\Exception $e) {
                    $barcodeImg = '';
                }
            @endphp
            @if($barcodeImg)
                <img src="data:image/png;base64,{{ $barcodeImg }}" alt="Barcode">
            @endif
            <div class="barcode-text">{{ $packingData->invoice_no }}</div>
        </div>
        @endif
    </div>
</body>
</html>
