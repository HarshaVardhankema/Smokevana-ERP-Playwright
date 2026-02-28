<!-- business information here -->


<div class="heading">
    @php
    //   dd($receipt_details);  
    @endphp
    
    <div style="width: 100%; background: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <!-- Company Info -->
            <div style="width: 33%;">
                <h4 style="font-size: 18px; font-weight: bold;">@if(!empty($receipt_details->display_name))
						{{$receipt_details->display_name}}
					@endif
                </h4>
                <span>
                    @if(!empty($receipt_details->address))
						<small class="text-center">
						{{$receipt_details->address}}
						</small>
				@endif
                </span>
                <!-- <br>
                <span>website : <a href="https://www.phantasm.digital/" target="_blank" style="color: #0c27bd;">www.phantasm.digital</a></span>
                <span>Email:<a href="info@test.com" style="color: #0c27bd;">info@test.com</a>
                </span> -->
                @if(!empty($receipt_details->contact))
					<br/>{!! $receipt_details->contact !!}
				@endif
            </div>


            <!-- Logo (Centered) -->
            <div style=" text-align: center;">
                @if (!empty($receipt_details->logo))
                    <img src="{{ $receipt_details->logo }}" alt="Company Logo" style="max-height: 84px;">
                @endif
            </div>

            <!-- Invoice Info (Right-Aligned) -->
            <div style="width: 33%; text-align: right;">
                <h1 style="font-size: 30px; font-weight: bold; color: #0c27bd;">INVOICE</h1>
                <p style="font-size: 15px; font-weight: bold;  ">

                    {{ $receipt_details->invoice_no }}
                </p>
            </div>
        </div>
    </div>


</div>


{{-- @if (!empty($receipt_details->letter_head))
    <div class="col-xs-12 text-center">
        <img style="width: 100%;margin-bottom: 10px;" src="{{ $receipt_details->letter_head }}">
    </div>
@endif --}}


<div style="display: table; width: 100%; border-collapse: collapse; margin-top: 5px;">
    <div style="display: table-row;">
        <div style="display: table-cell; width: 33.33%; padding: 5px; border: 1px solid gray; ;">
            <p style="margin-top: 1px">Issued</p>
            <span>
                <b>{{ $receipt_details->date_label }}</b> {{ $receipt_details->invoice_date }}

                @if (!empty($receipt_details->due_date_label))
                    <br><b>{{ $receipt_details->due_date_label }}</b> {{ $receipt_details->due_date ?? '' }}
                @endif

                @if (!empty($receipt_details->brand_label) || !empty($receipt_details->repair_brand))
                    <br>
                    @if (!empty($receipt_details->brand_label))
                        <b>{!! $receipt_details->brand_label !!}</b>
                    @endif
                    {{ $receipt_details->repair_brand }}
                @endif


                @if (!empty($receipt_details->device_label) || !empty($receipt_details->repair_device))
                    <br>
                    @if (!empty($receipt_details->device_label))
                        <b>{!! $receipt_details->device_label !!}</b>
                    @endif
                    {{ $receipt_details->repair_device }}
                @endif

                @if (!empty($receipt_details->model_no_label) || !empty($receipt_details->repair_model_no))
                    <br>
                    @if (!empty($receipt_details->model_no_label))
                        <b>{!! $receipt_details->model_no_label !!}</b>
                    @endif
                    {{ $receipt_details->repair_model_no }}
                @endif

                @if (!empty($receipt_details->serial_no_label) || !empty($receipt_details->repair_serial_no))
                    <br>
                    @if (!empty($receipt_details->serial_no_label))
                        <b>{!! $receipt_details->serial_no_label !!}</b>
                    @endif
                    {{ $receipt_details->repair_serial_no }}<br>
                @endif
                @if (!empty($receipt_details->repair_status_label) || !empty($receipt_details->repair_status))
                    @if (!empty($receipt_details->repair_status_label))
                        <b>{!! $receipt_details->repair_status_label !!}</b>
                    @endif
                    {{ $receipt_details->repair_status }}<br>
                @endif

                @if (!empty($receipt_details->repair_warranty_label) || !empty($receipt_details->repair_warranty))
                    @if (!empty($receipt_details->repair_warranty_label))
                        <b>{!! $receipt_details->repair_warranty_label !!}</b>
                    @endif
                    {{ $receipt_details->repair_warranty }}
                    <br>
                @endif

                <!-- Waiter info -->
                @if (!empty($receipt_details->service_staff_label) || !empty($receipt_details->service_staff))
                    <br />
                    @if (!empty($receipt_details->service_staff_label))
                        <b>{!! $receipt_details->service_staff_label !!}</b>
                    @endif
                    {{ $receipt_details->service_staff }}
                @endif
                @if (!empty($receipt_details->shipping_custom_field_1_label))
                    <br><strong>{!! $receipt_details->shipping_custom_field_1_label !!} :</strong> {!! $receipt_details->shipping_custom_field_1_value ?? '' !!}
                @endif

                @if (!empty($receipt_details->shipping_custom_field_2_label))
                    <br><strong>{!! $receipt_details->shipping_custom_field_2_label !!}:</strong> {!! $receipt_details->shipping_custom_field_2_value ?? '' !!}
                @endif

                @if (!empty($receipt_details->shipping_custom_field_3_label))
                    <br><strong>{!! $receipt_details->shipping_custom_field_3_label !!}:</strong> {!! $receipt_details->shipping_custom_field_3_value ?? '' !!}
                @endif

                @if (!empty($receipt_details->shipping_custom_field_4_label))
                    <br><strong>{!! $receipt_details->shipping_custom_field_4_label !!}:</strong> {!! $receipt_details->shipping_custom_field_4_value ?? '' !!}
                @endif

                @if (!empty($receipt_details->shipping_custom_field_5_label))
                    <br><strong>{!! $receipt_details->shipping_custom_field_2_label !!}:</strong> {!! $receipt_details->shipping_custom_field_5_value ?? '' !!}
                @endif
                {{-- sale order --}}
                @if (!empty($receipt_details->sale_orders_invoice_no))
                    <br>
                    <strong>@lang('restaurant.order_no'):</strong> {!! $receipt_details->sale_orders_invoice_no ?? '' !!}
                @endif

                @if (!empty($receipt_details->sale_orders_invoice_date))
                    <br>
                    <strong>@lang('lang_v1.order_dates'):</strong> {!! $receipt_details->sale_orders_invoice_date ?? '' !!}
                @endif

                @if (!empty($receipt_details->sell_custom_field_1_value))
                    <br>
                    <strong>{{ $receipt_details->sell_custom_field_1_label }}:</strong> {!! $receipt_details->sell_custom_field_1_value ?? '' !!}
                @endif

                @if (!empty($receipt_details->sell_custom_field_2_value))
                    <br>
                    <strong>{{ $receipt_details->sell_custom_field_2_label }}:</strong> {!! $receipt_details->sell_custom_field_2_value ?? '' !!}
                @endif

                @if (!empty($receipt_details->sell_custom_field_3_value))
                    <br>
                    <strong>{{ $receipt_details->sell_custom_field_3_label }}:</strong> {!! $receipt_details->sell_custom_field_3_value ?? '' !!}
                @endif

                @if (!empty($receipt_details->sell_custom_field_4_value))
                    <br>
                    <strong>{{ $receipt_details->sell_custom_field_4_label }}:</strong> {!! $receipt_details->sell_custom_field_4_value ?? '' !!}
                @endif

            </span>
        </div>
        <div style="display: table-cell; width: 33.33%; padding: 5px; border: 1px solid gray;">
            <p style="margin:2px;">SOLD TO</p>

            <!-- customer info -->
            <span style="padding:2px;">
                @if (!empty($receipt_details->customer_info))
                    {!! $receipt_details->customer_info !!} <br>
                @endif
                {{-- @if (!empty($receipt_details->client_id_label))
                    <br />
                    <b>{{ $receipt_details->client_id_label }}</b> {{ $receipt_details->client_id }}
                @endif
                @if (!empty($receipt_details->customer_tax_label))
                    <br />
                    <b>{{ $receipt_details->customer_tax_label }}</b> {{ $receipt_details->customer_tax_number }}
                @endif
                @if (!empty($receipt_details->customer_custom_fields))
                    <br />{!! $receipt_details->customer_custom_fields !!}
                @endif
                @if (!empty($receipt_details->sales_person_label))
                    <br />
                    <b>{{ $receipt_details->sales_person_label }}</b> {{ $receipt_details->sales_person }}
                @endif
                @if (!empty($receipt_details->commission_agent_label))
                    <br />
                    <strong>{{ $receipt_details->commission_agent_label }}</strong>
                    {{ $receipt_details->commission_agent }}
                @endif
                @if (!empty($receipt_details->customer_rp_label))
                    <br />
                    <strong>{{ $receipt_details->customer_rp_label }}</strong>
                    {{ $receipt_details->customer_total_rp }}
                @endif --}}
            </span>


        </div>
        <div style="display: table-cell; width: 33.33%; padding: 5px; border: 1px solid gray; ">
            <p style="margin: 2px;">SHIP TO</p>
            <!-- customer info -->
            <span style="padding:2px;">
                @if (!empty($receipt_details->customer_info))
                    {!! $receipt_details->customer_info !!} <br>
                @endif
                {{-- @if (!empty($receipt_details->client_id_label))
                    <br />
                    <b>{{ $receipt_details->client_id_label }}</b> {{ $receipt_details->client_id }}
                @endif
                @if (!empty($receipt_details->customer_tax_label))
                    <br />
                    <b>{{ $receipt_details->customer_tax_label }}</b> {{ $receipt_details->customer_tax_number }}
                @endif
                @if (!empty($receipt_details->customer_custom_fields))
                    <br />{!! $receipt_details->customer_custom_fields !!}
                @endif
                @if (!empty($receipt_details->sales_person_label))
                    <br />
                    <b>{{ $receipt_details->sales_person_label }}</b> {{ $receipt_details->sales_person }}
                @endif
                @if (!empty($receipt_details->commission_agent_label))
                    <br />
                    <strong>{{ $receipt_details->commission_agent_label }}</strong>
                    {{ $receipt_details->commission_agent }}
                @endif
                @if (!empty($receipt_details->customer_rp_label))
                    <br />
                    <strong>{{ $receipt_details->customer_rp_label }}</strong>
                    {{ $receipt_details->customer_total_rp }}
                @endif --}}
            </span>
        </div>
    </div>
</div>

<div>
    <table style="width:100%; border-collapse: collapse; margin-top: 20px; border: 1px solid #0c27bd;">
        <thead style="background-color: #0c27bd; color: white;">
            <tr>
                <th style="border: 1px solid #0c27bd; text-align: center; color:">Terms</th>
                <th style="border: 1px solid #0c27bd; text-align: center; color:">Status</th>
                <th style="border: 1px solid #0c27bd;text-align: center; color:">Invoiced By</th>
                <th style="border: 1px solid #0c27bd; text-align: center; color:">Representative</th>
                <th style="border: 1px solid #0c27bd;text-align: center; color:">Sales Tax ID (STI)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #0c27bd; text-align: center; color: #0c27bd;">Payment In Advance</td>
                <td style="border: 1px solid #0c27bd; text-align: center; color: #0c27bd;">Pro Forma</td>
                <td style="border: 1px solid #0c27bd; text-align: center; color: #0c27bd;">{{$curruntUsername??$receipt_details->created_by}}</td>
                <td style="border: 1px solid #0c27bd; text-align: center; color: #0c27bd;">{{$receipt_details->sell_representative}}</td>
                <td style="border: 1px solid #0c27bd; text-align: center; color: #0c27bd;">34342344</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="row" style="color: #000000 !important;">
    @includeIf('sale_pos.receipts.partial.common_repair_invoice')
</div>

<div class="row" style="color: #000000 !important;">
    <div class="col-xs-12">
        <br />
        @php
            $p_width = 45;
        @endphp
        @if (!empty($receipt_details->item_discount_label))
            @php
                $p_width -= 10;
            @endphp
        @endif
        @if (!empty($receipt_details->discounted_unit_price_label))
            @php
                $p_width -= 10;
            @endphp
        @endif
        {{-- @php
            print_r($receipt_details);
        @endphp --}}
            @php
            // Check if there are multiple fulfillment types to show legend
            $hasErp = false;
            $hasDropship = false;
            foreach ($receipt_details->lines as $checkLine) {
                $ft = $checkLine['fulfillment_type'] ?? 'erp_inhouse';
                if (in_array($ft, ['erp_inhouse', 'erp_sales_order', 'erp_fulfilled', 'sales_order', 'in_house'])) {
                    $hasErp = true;
                }
                if (in_array($ft, ['vendor_dropship', 'woocommerce', 'wp_sales_order', 'erp_dropship_order', 'dropship', 'dropshipped', 'vendor'])) {
                    $hasDropship = true;
                }
            }
            $hasMixedFulfillment = $hasErp && $hasDropship;
        @endphp
        
        @if($hasMixedFulfillment)
        <div style="margin-bottom: 12px; padding: 10px 14px; background: linear-gradient(135deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 8px; font-size: 11px;">
            <strong style="color: #374151; font-size: 12px;">📦 Product Source Legend:</strong>
            <div style="display: flex; flex-wrap: wrap; gap: 25px; margin-top: 8px;">
                <span style="display: flex; align-items: center; gap: 6px;">
                    <span style="display: inline-block; width: 24px; height: 16px; background: #dbeafe; border: 1px solid #93c5fd; border-radius: 3px;"></span>
                    <span style="color: #1e40af; font-weight: 600;">ERP (In-house)</span>
                </span>
                <span style="display: flex; align-items: center; gap: 6px;">
                    <span style="display: inline-block; width: 24px; height: 16px; background: #fef9c3; border: 1px solid #fde047; border-radius: 3px;"></span>
                    <span style="color: #a16207; font-weight: 600;">Vendor Drop-shipped</span>
                </span>
            </div>
        </div>
        @endif


        <table style="width:100%; border-collapse: collapse; margin-top: 5px; border: 1px solid #0c27bd;">
            <thead style="background-color: #0c27bd; color: white;">
                <tr style="border: 1px solid #0c27bd;">
                    <th width="7%" style="padding:3px" class="text-left">SKU</th>
                    <th width="{{ $p_width }}%" class="text-center">{{ $receipt_details->table_product_label }}</th>
                    <th class="text-right" width="7%">{{ $receipt_details->table_qty_label }}</th>
                    <th class="text-right" width="7%">{{ $receipt_details->table_unit_price_label }}</th>
                    @if (!empty($receipt_details->discounted_unit_price_label))
                        <th class="text-right" width="7%">{{ $receipt_details->discounted_unit_price_label }}</th>
                    @endif
                    @if (!empty($receipt_details->item_discount_label))
                        <th class="text-right" width="7%">{{ $receipt_details->item_discount_label }}</th>
                    @endif
                    <th class="text-right" width="7%" style="padding:3px">{{ $receipt_details->table_subtotal_label }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipt_details->lines as $line)
     <tr style="border: 1px solid #0c27bd; padding:2px;">
                    @php
                        // Enhanced color coding based on fulfillment type - Full row background
                        $rowStyle = 'border: 1px solid #0c27bd; padding: 2px;';
                        $fulfillmentBadge = '';
                        $fulfillmentType = $line['fulfillment_type'] ?? null;
                        
                        // ERP / In-house Products - Light Blue Background
                        if ($fulfillmentType === 'erp_inhouse' || $fulfillmentType === 'erp_sales_order' || $fulfillmentType === 'erp_fulfilled' || $fulfillmentType === 'sales_order' || $fulfillmentType === 'in_house') {
                            $rowStyle .= ' background-color: #dbeafe;'; // Light blue - print-friendly
                            $fulfillmentBadge = '<span style="background: #1e40af; color: #ffffff; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; margin-left: 5px; white-space: nowrap;">ERP</span>';
                        } 
                        // WooCommerce Products - Light Green Background
                        elseif ($fulfillmentType === 'woocommerce' || $fulfillmentType === 'wp_sales_order') {
                            $rowStyle .= ' background-color: #dcfce7;'; // Light green - print-friendly
                            $fulfillmentBadge = '<span style="background: #166534; color: #ffffff; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; margin-left: 5px; white-space: nowrap;">WC</span>';
                        } 
                        // Vendor Drop-shipped Products - Light Yellow Background
                        elseif ($fulfillmentType === 'vendor_dropship' || $fulfillmentType === 'erp_dropship_order' || $fulfillmentType === 'dropship' || $fulfillmentType === 'dropshipped' || $fulfillmentType === 'vendor') {
                            $rowStyle .= ' background-color: #fef9c3;'; // Light yellow - print-friendly
                            $fulfillmentBadge = '<span style="background: #a16207; color: #ffffff; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; margin-left: 5px; white-space: nowrap;">DROPSHIP</span>';
                        }
                    @endphp
                    <tr style="{{ $rowStyle }}">
                        @if (!empty($line['sub_sku']))
                            <td style="padding: 5px; vertical-align: middle; font-size: 13px;">
                                {{ $line['sub_sku'] }}
                            </td>
                        @endif
                        <td class="text-left" style="padding: 3px; line-height: 1.1; word-spacing: -1px;">
                            <div style="margin: auto">
                            @if (!empty($line['image']))
                                <img src="{{ $line['image'] }}" alt="Image" width="40" height="40"
                                    style="float: left; margin: 1px; margin-right: 4px;">
                            @endif
                            </div>
                           
                            {{ $line['name'] }} {{ $line['product_variation'] }} {{ $line['variation'] }}
                                                        {!! $fulfillmentBadge !!}

                            {{-- @if (!empty($line['sub_sku']))
                                        , {{ $line['sub_sku'] }}
                        @endif --}}
                            @if (!empty($line['brand']))
                                , {{ $line['brand'] }}
                            @endif
                            @if (!empty($line['cat_code']))
                                , {{ $line['cat_code'] }}
                            @endif
                            @if (!empty($line['product_custom_fields']))
                                , {{ $line['product_custom_fields'] }}
                            @endif
                            @if (!empty($line['product_description']))
                                <small>
                                    {!! $line['product_description'] !!}
                                </small>
                            @endif
                            @if (!empty($line['sell_line_note']))
                                <br>
                                <small>
                                    {!! $line['sell_line_note'] !!}
                                </small>
                            @endif
                            @if (!empty($line['lot_number']))
                                <br> {{ $line['lot_number_label'] }}: {{ $line['lot_number'] }}
                            @endif
                            @if (!empty($line['product_expiry']))
                                , {{ $line['product_expiry_label'] }}: {{ $line['product_expiry'] }}
                            @endif

                            @if (!empty($line['warranty_name']))
                                <br><small>{{ $line['warranty_name'] }} </small>
                                @endif @if (!empty($line['warranty_exp_date']))
                                    <small>- {{ @format_date($line['warranty_exp_date']) }} </small>
                                @endif
                                @if (!empty($line['warranty_description']))
                                    <small> {{ $line['warranty_description'] ?? '' }}</small>
                                @endif

                                @if ($receipt_details->show_base_unit_details && $line['quantity'] && $line['base_unit_multiplier'] !== 1)
                                    <br><small>
                                        1 {{ $line['units'] }} = {{ $line['base_unit_multiplier'] }}
                                        {{ $line['base_unit_name'] }} <br>
                                        {{ $line['base_unit_price'] }} x {{ $line['orig_quantity'] }} =
                                        {{ $line['line_total'] }}
                                    </small>
                                @endif
                        </td>
                        <td class="text-right" width="7%">
                            {{ $line['quantity'] }} {{ $line['units'] }}

                            @if ($receipt_details->show_base_unit_details && $line['quantity'] && $line['base_unit_multiplier'] !== 1)
                                <br><small>
                                    {{ $line['quantity'] }} x {{ $line['base_unit_multiplier'] }} =
                                    {{ $line['orig_quantity'] }} {{ $line['base_unit_name'] }}
                                </small>
                            @endif
                        </td>
                        <td class="text-right" width="7%">{{ $line['unit_price_before_discount'] }}</td>
                        @if (!empty($receipt_details->discounted_unit_price_label))
                            <td class="text-right">
                                {{ $line['unit_price_inc_tax'] }}
                            </td>
                        @endif
                        @if (!empty($receipt_details->item_discount_label))
                            <td class="text-right" width="7%">
                                {{ $line['total_line_discount'] ?? '0.00' }}

                                @if (!empty($line['line_discount_percent']))
                                    ({{ $line['line_discount_percent'] }}%)
                                @endif
                            </td>
                        @endif
                        <td class="text-right" width="7%" style="padding: 5px; ">{{ $line['line_total'] }}</td>
                    </tr>
                    @if (!empty($line['modifiers']))
                        @foreach ($line['modifiers'] as $modifier)
                            <tr>
                                <td>
                                    {{ $modifier['name'] }} {{ $modifier['variation'] }}
                                    @if (!empty($modifier['sub_sku']))
                                        , {{ $modifier['sub_sku'] }}
                                        @endif @if (!empty($modifier['cat_code']))
                                            , {{ $modifier['cat_code'] }}
                                        @endif
                                        @if (!empty($modifier['sell_line_note']))
                                            ({!! $modifier['sell_line_note'] !!})
                                        @endif
                                </td>
                                <td class="text-right">{{ $modifier['quantity'] }} {{ $modifier['units'] }} </td>
                                <td class="text-right">{{ $modifier['unit_price_inc_tax'] }}</td>
                                @if (!empty($receipt_details->discounted_unit_price_label))
                                    <td class="text-right">{{ $modifier['unit_price_exc_tax'] }}</td>
                                @endif
                                @if (!empty($receipt_details->item_discount_label))
                                    <td class="text-right">0.00</td>
                                @endif
                                <td class="text-right">{{ $modifier['line_total'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                @empty
                    <tr>
                        <td colspan="4">&nbsp;</td>
                        @if (!empty($receipt_details->discounted_unit_price_label))
                            <td></td>
                        @endif
                        @if (!empty($receipt_details->item_discount_label))
                            <td></td>
                        @endif
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="row" style="color: #000000 !important;">
            <hr>
            <div class="col-xs-6">

                <table class="table table-slim">

                    @if (!empty($receipt_details->payments))
                        @foreach ($receipt_details->payments as $payment)
                            <tr>
                                <td>{{ $payment['method'] }}</td>
                                <td class="text-right">{{ $payment['amount'] }}</td>
                                <td class="text-right">{{ $payment['date'] }}</td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- Total Paid-->
                    @if (!empty($receipt_details->total_paid))
                        <tr>
                            <th>
                                {!! $receipt_details->total_paid_label !!}
                            </th>
                            <td class="text-right">
                                {{ $receipt_details->total_paid }}
                            </td>
                        </tr>
                    @endif

                    <!-- Total Due-->
                    @if (!empty($receipt_details->total_due) && !empty($receipt_details->total_due_label))
                        <tr>
                            <th>
                                {!! $receipt_details->total_due_label !!}
                            </th>
                            <td class="text-right">
                                {{ $receipt_details->total_due }}
                            </td>
                        </tr>
                    @endif

                    @if (!empty($receipt_details->all_due))
                        <tr>
                            <th>
                                {!! $receipt_details->all_bal_label !!}
                            </th>
                            <td class="text-right">
                                {{ $receipt_details->all_due }}
                            </td>
                        </tr>
                    @endif
                </table>
            </div>

            <div class="col-xs-6">
                <div class="table-responsive">
                    <table class="table table-slim">
                        <tbody>
                            @if (!empty($receipt_details->total_quantity_label))
                                <tr>
                                    <th style="width:70%">
                                        {!! $receipt_details->total_quantity_label !!}
                                    </th>
                                    <td class="text-right">
                                        {{ $receipt_details->total_quantity }}
                                    </td>
                                </tr>
                            @endif

                            @if (!empty($receipt_details->total_items_label))
                                <tr>
                                    <th style="width:70%">
                                        {!! $receipt_details->total_items_label !!}
                                    </th>
                                    <td class="text-right">
                                        {{ $receipt_details->total_items }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <th style="width:70%">
                                    {!! $receipt_details->subtotal_label !!}
                                </th>
                                <td class="text-right">
                                    {{ $receipt_details->subtotal }}
                                </td>
                            </tr>
                            @if (!empty($receipt_details->total_exempt_uf))
                                <tr>
                                    <th style="width:70%">
                                        @lang('lang_v1.exempt')
                                    </th>
                                    <td class="text-right">
                                        {{ $receipt_details->total_exempt }}
                                    </td>
                                </tr>
                            @endif
                            <!-- Shipping Charges -->
                            @if (!empty($receipt_details->shipping_charges))
                                <tr>
                                    <th style="width:70%">
                                        {!! $receipt_details->shipping_charges_label !!}
                                    </th>
                                    <td class="text-right">
                                        {{ $receipt_details->shipping_charges }}
                                    </td>
                                </tr>
                            @endif

                            @if (!empty($receipt_details->packing_charge))
                                <tr>
                                    <th style="width:70%">
                                        {!! $receipt_details->packing_charge_label !!}
                                    </th>
                                    <td class="text-right">
                                        {{ $receipt_details->packing_charge }}
                                    </td>
                                </tr>
                            @endif

                            <!-- Discount -->
                            @if (!empty($receipt_details->discount))
                                <tr>
                                    <th>
                                        {!! $receipt_details->discount_label !!}
                                    </th>

                                    <td class="text-right">
                                        (-) {{ $receipt_details->discount }}
                                    </td>
                                </tr>
                            @endif

                            @if (!empty($receipt_details->total_line_discount))
                                <tr>
                                    <th>
                                        {!! $receipt_details->line_discount_label !!}
                                    </th>

                                    <td class="text-right">
                                        (-) {{ $receipt_details->total_line_discount }}
                                    </td>
                                </tr>
                            @endif

                            @if (!empty($receipt_details->additional_expenses))
                                @foreach ($receipt_details->additional_expenses as $key => $val)
                                    <tr>
                                        <td>
                                            {{ $key }}:
                                        </td>

                                        <td class="text-right">
                                            (+)
                                            {{ $val }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                            @if (!empty($receipt_details->reward_point_label))
                                <tr>
                                    <th>
                                        {!! $receipt_details->reward_point_label !!} {{ __('lang_v1.redeemed') }}
                                    </th>

                                    <td class="text-right">
                                        {{ $receipt_details->reward_point_display ?? $receipt_details->reward_point_amount . ' (' . ($receipt_details->reward_point_points ?? 0) . ' points)' }}
                                    </td>
                                </tr>
                            @endif

                            <!-- Tax -->
                            @if (!empty($receipt_details->tax))
                                <tr>
                                    <th>
                                        {!! $receipt_details->tax_label !!}
                                    </th>
                                    <td class="text-right">
                                        (+) {{ $receipt_details->tax }}
                                    </td>
                                </tr>
                            @endif

                            @if ($receipt_details->round_off_amount > 0)
                                <tr>
                                    <th>
                                        {!! $receipt_details->round_off_label !!}
                                    </th>
                                    <td class="text-right">
                                        {{ $receipt_details->round_off }}
                                    </td>
                                </tr>
                            @endif

                            <!-- Total -->
                            <tr>
                                <th>
                                    {!! $receipt_details->total_label !!}
                                </th>
                                <td class="text-right">
                                    {{ $receipt_details->total }}
                                    @if (!empty($receipt_details->total_in_words))
                                        <br>
                                        <small>({{ $receipt_details->total_in_words }})</small>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="border-bottom col-md-12">
                @if (empty($receipt_details->hide_price) && !empty($receipt_details->tax_summary_label))
                    <!-- tax -->
                    @if (!empty($receipt_details->taxes))
                        <table class="table table-slim table-bordered">
                            <tr>
                                <th colspan="2" class="text-center">{{ $receipt_details->tax_summary_label }}
                                </th>
                            </tr>
                            @foreach ($receipt_details->taxes as $key => $val)
                                <tr>
                                    <td class="text-center"><b>{{ $key }}</b></td>
                                    <td class="text-center">{{ $val }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                @endif
            </div>

            @if (!empty($receipt_details->additional_notes))
                <div class="col-xs-12">
                    <p>{!! nl2br($receipt_details->additional_notes) !!}</p>
                </div>
            @endif

        </div>
        <div class="row" style="color: #000000 !important;">
            @php
                // Show QR code only for B2B locations (is_b2c == 0)
                $show_qr_for_b2b = ($receipt_details->show_qr_code && isset($receipt_details->location_is_b2c) && $receipt_details->location_is_b2c == 0);
            @endphp
            @if (!empty($receipt_details->footer_text))
                <div class="@if ($receipt_details->show_barcode || $show_qr_for_b2b) col-xs-8 @else col-xs-12 @endif">
                    {!! $receipt_details->footer_text !!}
                </div>
            @endif
            @if ($receipt_details->show_barcode || $show_qr_for_b2b)
                <div class="@if (!empty($receipt_details->footer_text)) col-xs-4 @else col-xs-12 @endif text-center">
                    @if ($receipt_details->show_barcode)
                        {{-- Barcode --}}
                        <img class="center-block"
                            src="data:image/png;base64,{{ DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2, 30, [39, 48, 54], true) }}">
                    @endif

                    @if ($show_qr_for_b2b && !empty($receipt_details->transaction_id))
                        @php
                            // Generate dynamic invoice URL with transaction ID using FRONT_URL
                            $front_url = config('app.front-url', url('/'));
                            $invoice_url = rtrim($front_url, '/') . '/profile/pay-invoice?orderIds=' . $receipt_details->transaction_id;
                        @endphp
                        <img class="center-block mt-5"
                            src="data:image/png;base64,{{ DNS2D::getBarcodePNG($invoice_url, 'QRCODE', 3, 3, [39, 48, 54]) }}">
                    @endif
                </div>
            @endif
        </div>
    </div>
