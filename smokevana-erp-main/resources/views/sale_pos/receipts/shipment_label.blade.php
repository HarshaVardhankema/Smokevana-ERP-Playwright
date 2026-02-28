<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .label {
            width: 101.6mm;
            height: 152.4mm;
            box-sizing: border-box;
            padding: 8px;
            border: 2px solid black;
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .bold {
            font-weight: bold;
        }

        .large {
            font-size: 28pt;
            font-weight: bold;
        }

        .small {
            font-size: 8pt;
            line-height: 1.2;
        }

        .section {
            border-bottom: 2px solid black;
            padding: 4px 0;
        }

        .center {
            text-align: center;
        }

        .barcode,
        .tracking {
            text-align: center;
            margin-top: 4px;
            font-family: monospace;
            font-size: 10pt;
            letter-spacing: 1px;
        }

        .qr {
            width: 30px;
            height: 30px;
            background: #000;
            display: inline-block;
        }

        .address {
            font-size: 9pt;
            line-height: 1.2;
        }

        .tracking-barcode {
            height: 36px;
            background: repeating-linear-gradient(90deg,
                    #000,
                    #000 2px,
                    #fff 2px,
                    #fff 4px);
            margin: 2px 0;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10pt;
            padding-top: 4px;
        }
    </style>
</head>

<body>
    <div class="label">
        <!-- Title -->
        <div class="section center bold">{{$shipment['shipment_details']['service_code']}}</div>

        <!-- From Address -->
        <div class="section">
            <div class="row">
                <div class="bold">Warehouse-1</div>
                <div class="bold">{{$shipment['shipment_details']['carrier_name']}}</div>
            </div>
            <div class="address">
                {{ $wareHouse->address_1 }}
                <br>
                {{ $wareHouse->city_locality}}
                <br>
                {{ $wareHouse->postal_code}},{{ $wareHouse->country_code}}
            </div>
        </div>

        <!-- Ship To -->
        <div class="section">
            <div class="bold">SHIP TO:</div>
            <div class="row" style="margin-top: 4px;">
                <div class="address" style="margin-left: 6px;">
                    <b>{{$sale->shipping_first_name}} {{$sale->shipping_last_name}}</b><br>
                    {{ $sale->shipping_address1}}
                    <br>
                    {{ $sale->shipping_city}}
                    <br>
                    {{ $sale->shipping_state}} {{ $sale->shipping_zip}} {{ $sale->shipping_country}}
                </div>
            </div>
        </div>

        <!-- Tracking -->
        <div class="section">
            <div class="center bold"> {{$shipment['shipment_details']['carrier_id']}}</div>
            <div class="center">
                <img style="max-width:90% !important;height: {{20 * 0.24}}in !important; display: block;"
                    src="data:image/png;base64,{{ DNS1D::getBarcodePNG($shipment['shipment_details']['tracking_number'], 'C128', 1, 30, [0, 0, 0], false) }}"
                    alt="Tracking Barcode">
            </div>

            <div class="tracking"> {{$shipment['shipment_details']['tracking_number']}}</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="bold">{{ $sale->invoice_no }}</div>
        </div>
    </div>
</body>

</html>