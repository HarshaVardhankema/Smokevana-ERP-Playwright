@php
    // Check if this is a parent order with child orders
    $isParentOrder = isset($isParentOrder) ? $isParentOrder : $sell->isParentOrder();
    $hasChildOrders = isset($childOrders) && $childOrders && $childOrders->isNotEmpty();
    
    // Determine which lines to display
    $displayLines = $sell->sell_lines;
    if ($isParentOrder && $hasChildOrders && isset($consolidatedLines) && $consolidatedLines->isNotEmpty()) {
        $displayLines = $consolidatedLines;
    }
@endphp

{{-- Color Legend for Split Orders --}}
@if($isParentOrder && $hasChildOrders)
<div class="alert alert-info" style="margin-bottom: 15px; padding: 10px 15px; border-radius: 8px;">
    <strong><i class="fas fa-info-circle"></i> Invoice Consolidation:</strong> 
    This invoice includes items from all split orders.
    <div style="margin-top: 8px; display: flex; gap: 20px; flex-wrap: wrap;">
        <span style="display: inline-flex; align-items: center; gap: 6px;">
            <span style="width: 16px; height: 16px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 4px; display: inline-block;"></span>
            <strong>ERP Fulfilled</strong> (In-house)
        </span>
        <span style="display: inline-flex; align-items: center; gap: 6px;">
            <span style="width: 16px; height: 16px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 4px; display: inline-block;"></span>
            <strong>Vendor Dropship</strong> (WooCommerce)
        </span>
        <span style="display: inline-flex; align-items: center; gap: 6px;">
            <span style="width: 16px; height: 16px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 4px; display: inline-block;"></span>
            <strong>Vendor Dropship</strong> (ERP Portal)
        </span>
    </div>
</div>
@endif

<style>
    .fulfillment-erp { 
        border-left: 4px solid #10b981 !important; 
        background-color: rgba(16, 185, 129, 0.05) !important;
    }
    .fulfillment-woocommerce { 
        border-left: 4px solid #8b5cf6 !important; 
        background-color: rgba(139, 92, 246, 0.05) !important;
    }
    .fulfillment-vendor { 
        border-left: 4px solid #f59e0b !important; 
        background-color: rgba(245, 158, 11, 0.05) !important;
    }
    .fulfillment-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-left: 8px;
    }
    .fulfillment-badge.erp {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }
    .fulfillment-badge.woocommerce {
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        color: #5b21b6;
    }
    .fulfillment-badge.vendor {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
    }
</style>

<table class="table @if(!empty($for_ledger)) table-slim mb-0 bg-light-gray @else bg-gray @endif" @if(!empty($for_pdf)) style="width: 100%;" @endif>
    <tr @if(empty($for_ledger)) class="bg-green" @endif>
    <th>#</th>
    <th>{{ __('sale.product') }}</th>
    @if( session()->get('business.enable_lot_number') == 1 && empty($for_ledger))
        <th>{{ __('lang_v1.lot_n_expiry') }}</th>
    @endif
    @if($sell->type == 'sales_order')
        <th>@lang('lang_v1.quantity_remaining')</th>
    @endif
    <th>{{ __('sale.qty') }}</th>
    @if(!empty($pos_settings['inline_service_staff']))
        <th>
            @lang('restaurant.service_staff')
        </th>
    @endif
    <th>{{ __('sale.unit_price') }}</th>
    <th>{{ __('sale.discount') }}</th>
    <th>{{ __('sale.tax') }}</th>
    <th>{{ __('sale.price_inc_tax') }}</th>
    <th>{{ __('sale.subtotal') }}</th>
</tr>
@foreach($displayLines as $sell_line)
    @php
        // Determine fulfillment type for color coding
        // Check both attribute and property access methods
        $fulfillmentType = null;
        $sourceInvoiceNo = null;
        
        // Try to get fulfillment_type from the model attribute
        if (isset($sell_line->fulfillment_type)) {
            $fulfillmentType = $sell_line->fulfillment_type;
        }
        
        // Try to get source_invoice_no
        if (isset($sell_line->source_invoice_no)) {
            $sourceInvoiceNo = $sell_line->source_invoice_no;
        }
        
        $rowClass = '';
        $badgeClass = '';
        $badgeText = '';
        
        if ($fulfillmentType === 'erp_sales_order' || $fulfillmentType === 'erp_fulfilled' || $fulfillmentType === 'sales_order') {
            $rowClass = 'fulfillment-erp';
            $badgeClass = 'erp';
            $badgeText = 'ERP';
        } elseif ($fulfillmentType === 'wp_sales_order') {
            $rowClass = 'fulfillment-woocommerce';
            $badgeClass = 'woocommerce';
            $badgeText = 'Vendor (WC)';
        } elseif ($fulfillmentType === 'erp_dropship_order') {
            $rowClass = 'fulfillment-vendor';
            $badgeClass = 'vendor';
            $badgeText = 'Vendor (ERP)';
        }
    @endphp
    <tr class="{{ $rowClass }}">
        <td>{{ $loop->iteration }}</td>
        <td>
            {{ $sell_line->product->name }}
            @if( $sell_line->product->type == 'variable')
            - {{ $sell_line->variations->product_variation->name ?? ''}}
            - {{ $sell_line->variations->name ?? ''}},
            @endif
            {{ $sell_line->variations->sub_sku ?? ''}}
            @php
            $brand = $sell_line->product->brand;
            @endphp
            @if(!empty($brand->name))
            , {{$brand->name}}
            @endif
            
            {{-- Fulfillment Type Badge --}}
            @if(!empty($badgeText) && $isParentOrder && $hasChildOrders)
            <span class="fulfillment-badge {{ $badgeClass }}">{{ $badgeText }}</span>
            @endif
            
            {{-- Source Order Info --}}
            @if(!empty($sourceInvoiceNo) && $sourceInvoiceNo !== $sell->invoice_no && $isParentOrder && $hasChildOrders)
            <br><small class="text-muted"><i class="fas fa-link"></i> From: {{ $sourceInvoiceNo }}</small>
            @endif

            @if(!empty($sell_line->sell_line_note) && !str_starts_with($sell_line->sell_line_note ?? '', '{"fulfillment_type"'))
            <br> {{$sell_line->sell_line_note}}
            @endif
            @if($is_warranty_enabled && !empty($sell_line->warranties->first()) )
                <br><small>{{$sell_line->warranties->first()->display_name ?? ''}} - {{ @format_date($sell_line->warranties->first()->getEndDate($sell->transaction_date))}}</small>
                @if(!empty($sell_line->warranties->first()->description))
                <br><small>{{$sell_line->warranties->first()->description ?? ''}}</small>
                @endif
            @endif

            @if(in_array('kitchen', $enabled_modules) && empty($for_ledger))
                <br><span class="label @if($sell_line->res_line_order_status == 'cooked' ) bg-red @elseif($sell_line->res_line_order_status == 'served') bg-green @else bg-light-blue @endif">@lang('restaurant.order_statuses.' . $sell_line->res_line_order_status) </span>
            @endif
        </td>
        @if( session()->get('business.enable_lot_number') == 1 && empty($for_ledger))
            <td>{{ $sell_line->lot_details->lot_number ?? '--' }}
                @if( session()->get('business.enable_product_expiry') == 1 && !empty($sell_line->lot_details->exp_date))
                ({{@format_date($sell_line->lot_details->exp_date)}})
                @endif
            </td>
        @endif
        @if($sell->type == 'sales_order')
            <td><span class="display_currency" data-currency_symbol="false" data-is_quantity="true">{{ $sell_line->quantity - $sell_line->so_quantity_invoiced }}</span> @if(!empty($sell_line->sub_unit)) {{$sell_line->sub_unit->short_name}} @else {{$sell_line->product->unit->short_name}} @endif</td>
        @endif
        <td>
            @if(!empty($for_ledger))
                {{@format_quantity($sell_line->quantity)}}
            @else
            O: <span class="display_currency" style="color: #f60; font-weight: 700;" data-currency_symbol="false" data-is_quantity="true">{{ $sell_line->ordered_quantity }}</span> 
            P: <span class="display_currency" style="color: rgb(0, 55, 255); font-weight: 700;" data-currency_symbol="false" data-is_quantity="true">{{ $sell_line->picked_quantity }}</span> 
            F: <span class="display_currency" style="color: green; font-weight: 700;" data-currency_symbol="false" data-is_quantity="true">{{ $sell_line->quantity }}</span>
            
            @endif
                {{-- @if(!empty($sell_line->sub_unit)) {{$sell_line->sub_unit->short_name}} @else {{$sell_line->product->unit->short_name}} @endif --}}

            @if(!empty($sell_line->product->second_unit) && $sell_line->secondary_unit_quantity != 0)
                <br>
                @if(!empty($for_ledger))
                    {{@format_quantity($sell_line->secondary_unit_quantity)}}
                @else
                    <span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $sell_line->secondary_unit_quantity }}</span> 
                @endif
                {{$sell_line->product->second_unit->short_name}}
            @endif
        </td>
        @if(!empty($pos_settings['inline_service_staff']))
            <td>
            {{ $sell_line->service_staff->user_full_name ?? '' }}
            </td>
        @endif
        <td>
            @if(!empty($for_ledger))
                @format_currency($sell_line->unit_price_before_discount)
            @else
                <span class="display_currency" data-currency_symbol="true">{{ $sell_line->unit_price_before_discount }}</span>
            @endif
        </td>
        <td>
            @if(!empty($for_ledger))
                @format_currency($sell_line->get_discount_amount())
            @else
                <span class="display_currency" data-currency_symbol="true">{{ $sell_line->get_discount_amount() }}</span>
            @endif
            @if($sell_line->line_discount_type == 'percentage') ({{$sell_line->line_discount_amount}}%) @endif
        </td>
        <td>
            @if(!empty($for_ledger))
                @format_currency($sell_line->item_tax)
            @else
                <span class="display_currency" data-currency_symbol="true">{{ $sell_line->item_tax }}</span> 
            @endif
            @if(!empty($taxes[$sell_line->tax_id]))
            ( {{ $taxes[$sell_line->tax_id]}} )
            @endif
        </td>
        <td>
            @if(!empty($for_ledger))
                @format_currency($sell_line->unit_price_inc_tax)
            @else
                <span class="display_currency" data-currency_symbol="true">{{ $sell_line->unit_price_inc_tax }}</span>
            @endif
        </td>
        <td>
            @if(!empty($for_ledger))
                @format_currency($sell_line->quantity * $sell_line->unit_price_inc_tax)
            @else
                <span class="display_currency" data-currency_symbol="true">{{ $sell_line->quantity * $sell_line->unit_price_inc_tax }}</span>
            @endif
        </td>
    </tr>
    @if(!empty($sell_line->modifiers))
    @foreach($sell_line->modifiers as $modifier)
        <tr>
            <td>&nbsp;</td>
            <td>
                {{ $modifier->product->name }} - {{ $modifier->variations->name ?? ''}},
                {{ $modifier->variations->sub_sku ?? ''}}
            </td>
            @if( session()->get('business.enable_lot_number') == 1)
                <td>&nbsp;</td>
            @endif
            <td>{{ $modifier->quantity }}</td>
            @if(!empty($pos_settings['inline_service_staff']))
                <td>
                    &nbsp;
                </td>
            @endif
            <td>
                @if(!empty($for_ledger))
                    @format_currency($modifier->unit_price)
                @else
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->unit_price }}</span>
                @endif
            </td>
            <td>
                &nbsp;
            </td>
            <td>
                @if(!empty($for_ledger))
                    @format_currency($modifier->item_tax)
                @else
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->item_tax }}</span> 
                @endif
                @if(!empty($taxes[$modifier->tax_id]))
                ( {{ $taxes[$modifier->tax_id]}} )
                @endif
            </td>
            <td>
                @if(!empty($for_ledger))
                    @format_currency($modifier->unit_price_inc_tax)
                @else
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->unit_price_inc_tax }}</span>
                @endif
            </td>
            <td>
                @if(!empty($for_ledger))
                    @format_currency($modifier->quantity * $modifier->unit_price_inc_tax)
                @else
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->quantity * $modifier->unit_price_inc_tax }}</span>
                @endif
            </td>
        </tr>
        @endforeach
    @endif
@endforeach
</table>