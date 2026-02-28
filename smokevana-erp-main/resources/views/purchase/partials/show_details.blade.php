<div class="no-print">
  <div class="modal-header">
    @php
      $isLockModal = $isLockModal??null
    @endphp
        @php
  $title = $purchase->type == 'purchase_order' ? __('lang_v1.purchase_order_details') : __('purchase.purchase_details');
  $custom_labels = json_decode(session('business.custom_labels'), true);
      @endphp
        <style>
          .table-responsive {
            max-height: 35vh;
            overflow-y: auto;
          }
          @media print {
            .modal-content {
              border: none !important;
              box-shadow: none !important;
            }
          }
  
          .delete_purchase_row {
            cursor: pointer;
          }

          /* Purchase order details table alignment + styling */
          #purchase_order_table_modal {
            overflow-x: auto;
          }
          #purchase_order_table_modal .po-details-table {
            width: 100%;
            min-width: 1400px;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
          }
          #purchase_order_table_modal .po-details-table thead th {
            background: linear-gradient(180deg, #b35a00 0%, #d27700 100%);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
          }
          #purchase_order_table_modal .po-details-table th,
          #purchase_order_table_modal .po-details-table td {
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
          }
          #purchase_order_table_modal .po-details-table th.product-col,
          #purchase_order_table_modal .po-details-table td.product-col {
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.25;
            width: 520px;
            min-width: 520px;
            max-width: 520px;
            padding-right: 12px;
          }
          #purchase_order_table_modal .po-details-table th.col-idx,
          #purchase_order_table_modal .po-details-table td.col-idx {
            width: 40px;
          }
          #purchase_order_table_modal .po-details-table th.col-sku,
          #purchase_order_table_modal .po-details-table td.col-sku {
            width: 90px;
          }
          #purchase_order_table_modal .po-details-table th.col-qty-remaining,
          #purchase_order_table_modal .po-details-table td.col-qty-remaining {
            width: 120px;
          }
          #purchase_order_table_modal .po-details-table th.col-order-qty,
          #purchase_order_table_modal .po-details-table td.col-order-qty {
            width: 100px;
          }
          #purchase_order_table_modal .po-details-table th.col-unit-cost,
          #purchase_order_table_modal .po-details-table td.col-unit-cost {
            width: 110px;
          }
          #purchase_order_table_modal .po-details-table th.col-discount,
          #purchase_order_table_modal .po-details-table td.col-discount {
            width: 160px;
            min-width: 160px;
          }
          #purchase_order_table_modal .po-details-table th.col-final-cost,
          #purchase_order_table_modal .po-details-table td.col-final-cost {
            width: 110px;
          }
          #purchase_order_table_modal .po-details-table th.col-subtotal,
          #purchase_order_table_modal .po-details-table td.col-subtotal {
            width: 110px;
          }
          #purchase_order_table_modal .po-details-table th.col-action,
          #purchase_order_table_modal .po-details-table td.col-action {
            width: 40px;
          }
          #purchase_order_table_modal .po-details-table td.text-right,
          #purchase_order_table_modal .po-details-table th.text-right {
            text-align: right;
          }
          #purchase_order_table_modal .po-details-table tbody tr:nth-child(even) {
            background: #fff6ea;
          }
          #purchase_order_table_modal .po-details-table tbody tr:hover {
            background: #ffe6c6;
          }
          #purchase_order_table_modal .po-details-table .input-group {
            display: inline-flex;
            align-items: center;
            flex-wrap: nowrap;
          }
          #purchase_order_table_modal .po-details-table .input-group-addon {
            height: 28px;
            line-height: 26px;
            padding: 4px 8px;
          }
          #purchase_order_table_modal .po-details-table .form-control {
            height: 28px;
            line-height: 26px;
            padding: 4px 6px;
          }
          #purchase_order_table_modal .po-details-table .quantity input {
            width: 70px;
          }
          #purchase_order_table_modal .po-details-table .unit_price .form-control {
            width: 80px;
          }
          #purchase_order_table_modal .po-details-table td.discount .form-control {
            width: 90px;
          }
          #purchase_order_table_modal .po-details-table td.discount select {
            width: 80px;
            min-width: 80px;
            height: 32px;
            line-height: 30px;
            color: #000000;
            -webkit-text-fill-color: #000000;
            background-color: #ffffff;
            font-weight: 700;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            text-shadow: none;
            text-align: center;
            text-align-last: center;
            text-indent: 0;
            padding-left: 8px;
            padding-right: 22px;
            -webkit-appearance: menulist;
            -moz-appearance: menulist;
            appearance: menulist;
          }
          #purchase_order_table_modal .po-details-table td.discount select option {
            font-family: inherit;
            font-size: 14px;
            color: #000000;
          }
        </style>
      <h4 class="modal-title" id="modalTitle"> {{$title}} (<b>@lang('purchase.ref_no'):</b> #{{ $purchase->ref_no }})
      </h4>
      <p ><b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}</p>
        <div style="margin-top: -50px" class="pull-right" >
         
          <a href="#" class="print-invoice tw-dw-btn tw-dw-btn-gray tw-text-white"
          data-href="/purchases/print/{{$purchase->id}}">
          <i class="fa fa-print" aria-hidden="true"></i> @lang('lang_v1.print_invoice')
      </a>
          <button id="save_button_purchase" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print  @if($isLockModal) hide @endif">
            Save
          </button>
          <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" data-dismiss="modal" id='close_button'>@lang('messages.close')</button>
        </div>
  </div>
  <div class="modal-body">
    {!! Form::hidden('location_id', $purchase->location_id, ['id' => 'location_id']) !!}
    <div class="row ">
      <div class="col-sm-4 invoice-col">
        @lang('purchase.supplier'):
        <address>
          {!! $purchase->contact->contact_address !!}
          @if(!empty($purchase->contact->tax_number))
            <br>@lang('contact.tax_no'): {{$purchase->contact->tax_number}}
          @endif
          @if(!empty($purchase->contact->mobile))
            <br>@lang('contact.mobile'): {{$purchase->contact->mobile}}
          @endif
          @if(!empty($purchase->contact->email))
            <br>@lang('business.email'): {{$purchase->contact->email}}
          @endif
        </address>
        @if($purchase->document_path)
          
          <a href="{{$purchase->document_path}}" 
          download="{{$purchase->document_name}}" class="tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-sm pull-left no-print">
            <i class="fa fa-download"></i> 
              &nbsp;{{ __('purchase.download_document') }}
          </a>
        @endif
      </div>
  
      <div class="col-sm-4 invoice-col">
        @lang('business.business'):
        <address>
          <strong>{{ $purchase->business->name }}</strong>
          {{ $purchase->location->name }}
          @if(!empty($purchase->location->landmark))
            <br>{{$purchase->location->landmark}}
          @endif
          @if(!empty($purchase->location->city) || !empty($purchase->location->state) || !empty($purchase->location->country))
            <br>{{implode(',', array_filter([$purchase->location->city, $purchase->location->state, $purchase->location->country]))}}
          @endif
          
          @if(!empty($purchase->business->tax_number_1))
            <br>{{$purchase->business->tax_label_1}}: {{$purchase->business->tax_number_1}}
          @endif
  
          @if(!empty($purchase->business->tax_number_2))
            <br>{{$purchase->business->tax_label_2}}: {{$purchase->business->tax_number_2}}
          @endif
  
          @if(!empty($purchase->location->mobile))
            <br>@lang('contact.mobile'): {{$purchase->location->mobile}}
          @endif
          @if(!empty($purchase->location->email))
            <br>@lang('business.email'): {{$purchase->location->email}}
          @endif
        </address>
      </div>
  
      <div class="col-sm-4 invoice-col">
        <b>@lang('purchase.ref_no'):</b> #{{ $purchase->ref_no }}<br/>
        <b>@lang('purchase.supplier_ref_no'):</b> 
        <span class="supplier-ref-no-input" data-id="{{$purchase->id}}" data-value="{{$purchase->supplier_ref_no}}" contenteditable="true">#{{ $purchase->supplier_ref_no }}</span>
        <br/>
        <b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}<br/>
        @if(!empty($purchase->status))
          <b>@lang('purchase.purchase_status'):</b> @if($purchase->type == 'purchase_order'){{$po_statuses[$purchase->status]['label'] ?? ''}} @else {{ __('lang_v1.' . $purchase->status) }} @endif<br>
        @endif
        @if(!empty($purchase->payment_status))
        <b>@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $purchase->payment_status) }}
        @endif
  
        @if(!empty($custom_labels['purchase']['custom_field_1']))
          <br><strong>{{$custom_labels['purchase']['custom_field_1'] ?? ''}}: </strong> {{$purchase->custom_field_1}}
        @endif
        @if(!empty($custom_labels['purchase']['custom_field_2']))
          <br><strong>{{$custom_labels['purchase']['custom_field_2'] ?? ''}}: </strong> {{$purchase->custom_field_2}}
        @endif
        @if(!empty($custom_labels['purchase']['custom_field_3']))
          <br><strong>{{$custom_labels['purchase']['custom_field_3'] ?? ''}}: </strong> {{$purchase->custom_field_3}}
        @endif
        @if(!empty($custom_labels['purchase']['custom_field_4']))
          <br><strong>{{$custom_labels['purchase']['custom_field_4'] ?? ''}}: </strong> {{$purchase->custom_field_4}}
        @endif
        @if(!empty($purchase_order_nos))
              <strong>@lang('restaurant.order_no'):</strong>
              {{$purchase_order_nos}}
          @endif
  
          @if(!empty($purchase_order_dates))
              <br>
              <strong>@lang('lang_v1.order_dates'):</strong>
              {{$purchase_order_dates}}
          @endif
        @if($purchase->type == 'purchase_order')
          @php
    $custom_labels = json_decode(session('business.custom_labels'), true);
          @endphp
          <strong>@lang('sale.shipping'):</strong>
          <span class="label @if(!empty($shipping_status_colors[$purchase->shipping_status])) {{$shipping_status_colors[$purchase->shipping_status]}} @else {{'bg-gray'}} @endif">{{$shipping_statuses[$purchase->shipping_status] ?? '' }}</span><br>
          @if(!empty($purchase->shipping_address()))
            {{$purchase->shipping_address()}}
          @else
            {{$purchase->shipping_address ?? '--'}}
          @endif
          @if(!empty($purchase->delivered_to))
            <br><strong>@lang('lang_v1.delivered_to'): </strong> {{$purchase->delivered_to}}
          @endif
          @if(!empty($purchase->shipping_custom_field_1))
            <br><strong>{{$custom_labels['shipping']['custom_field_1'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_1}}
          @endif
          @if(!empty($purchase->shipping_custom_field_2))
            <br><strong>{{$custom_labels['shipping']['custom_field_2'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_2}}
          @endif
          @if(!empty($purchase->shipping_custom_field_3))
            <br><strong>{{$custom_labels['shipping']['custom_field_3'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_3}}
          @endif
          @if(!empty($purchase->shipping_custom_field_4))
            <br><strong>{{$custom_labels['shipping']['custom_field_4'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_4}}
          @endif
          @if(!empty($purchase->shipping_custom_field_5))
            <br><strong>{{$custom_labels['shipping']['custom_field_5'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_5}}
          @endif
          @php
    $medias = $purchase->media->where('model_media_type', 'shipping_document')->all();
          @endphp
          @if(count($medias))
            @include('sell.partials.media_table', ['medias' => $medias])
          @endif
        @endif
      </div>
    </div>
    
    <div class="row">
      <div class="col-sm-12 col-xs-12 tw-flex tw-justify-between">
        <h4>{{ __('sale.products') }}:</h4>
        
        <div class="tw-relative tw-inline-block tw-mb-1 tw-gap-5 no-print">
          @if ($purchase->type == 'purchase_order')
                              <button id="openLock" data-href={{ $purchase->id }} class="btn-modal-cl @if(!$isLockModal) hide @endif"
                                  @if (!$isLockModal) disabled @endif>
                                  @if ($isLockModal)
                                      🔒
                                  @else
                                      🔓
                                  @endif
  
                              </button>
                          @endif
            <button id="openSellsNoteModal" class="btn-modal-cl">
                📝 Memo
            </button>
            <button id="openActivityModal" class="btn-modal-cl">
                ⚽ Activity
            </button>
        </div>
  
      </div>
      <div class="hide">
              {!! Form::text('location_id', $purchase->location_id, ['class' => 'form-control select2 hide', 'placeholder' => __('messages.please_select'), 'disabled']);!!}
      </div>
      <div class="col-sm-12 col-xs-12">
        <div class="table-responsive" id="purchase_order_table_modal" style="max-height:45vh; overflow-y: auto; min-height: 45vh;">
          <table class="table table-condensed table-bordered po-details-table">
            <thead style="position: sticky; top: 0; z-index: 9;" class="tw-text-sm">
              <tr>
                <th class="col-idx">#</th>
                <th class="col-sku">@lang('product.sku')</th>
                <th class="product-col" style="min-width: 500px;">@lang('product.product_name')</th>
                @if($purchase->type == 'purchase_order')
                  <th class="col-qty-remaining">Qty Remaining</th>
                @endif
                <th class="col-order-qty">@if($purchase->type == 'purchase_order') Order Qty @else Purchase Qty @endif</th>
                <th class="col-unit-cost">@lang('lang_v1.unit_cost')</th>
                <th class="col-discount">Discount</th>
                @if($purchase->type == 'purchase_order')
                <th class="col-final-cost no-print">Final Cost</th>
                @endif
                @if($purchase->type != 'purchase_order')
                @if(session('business.enable_lot_number'))
                  <th>@lang('lang_v1.lot_number')</th>
                @endif
                @if(session('business.enable_product_expiry'))
                  <th>@lang('product.mfg_date')</th>
                  <th>@lang('product.exp_date')</th>
                @endif
                @endif
                <th class="col-subtotal text-right">@lang('sale.subtotal')</th>
                @if($purchase->type == 'purchase_order')
                    <th class="col-action handle_lock @if($isLockModal) hide @endif"><i class="fa fa-trash" aria-hidden="true"></i></th>
                @endif
              </tr>
            </thead>
            @php 
              $total_before_tax = 0.00;
            @endphp
            <tbody>
              @foreach($purchase->purchase_lines as $purchase_line)
              <tr class="purchase-line" data-purchase-line-id="{{ $purchase_line->id }}">
                <td class="col-idx">{{ $loop->iteration }}</td>
                <td class="col-sku">
                   @if($purchase_line->product->type == 'variable')
                    {{ $purchase_line->variations->sub_sku}}
                    @else
                    {{ $purchase_line->product->sku }}
                   @endif
                </td>
                <td class="product-col">
                  <div class="hide puchase_line_data">
                    <input type="text" class="hide data_product_id" value={{ $purchase_line->product_id }}>
                    <input type="text" class="hide data_variation_id" value={{ $purchase_line->variation_id }}>
                    <input type="text" class="hide data_purchase_line_id" value={{$purchase_line->id  }}>
                    <input type="text" class="hide data_quantity" value={{ $purchase_line->quantity }}>
                    <input type="text" class="hide data_product_unit_id" value={{ $purchase_line->product->unit_id }}>
                    <input type="text" class="hide data_pp_without_discount" value={{ $purchase_line->pp_without_discount }}>
                    <input type="text" class="hide data_row_discount_percent" value={{ $purchase_line->discount_percent }}>
                    <input type="text" class="hide data_row_discount_type" value={{ $purchase_line->row_discount_type}}>
                    <input type="text" class="hide data_purchase_price" value={{ $purchase_line->purchase_price }}>
                    <input type="text" class="hide data_purchase_line_tax_id" value={{ $purchase_line->tax_id}}>
                    <input type="text" class="hide data_item_tax" value={{ $purchase_line->item_tax }}>
                    <input type="text" class="hide data_purchase_price_inc_tax" value={{ $purchase_line->purchase_price_inc_tax }}>
                    <input type="text" class="hide data_profit_percent" value={{ $purchase_line->variations->profit_percent}}>
                  </div>
                  {{ $purchase_line->product->name }}
                   @if($purchase_line->product->type == 'variable')
                    - {{ $purchase_line->variations->product_variation->name}}
                    - {{ $purchase_line->variations->name}}
                   @endif
                </td>
                @if($purchase->type == 'purchase_order')
                  <td class="col-qty-remaining">
                    <span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $purchase_line->quantity - $purchase_line->po_quantity_purchased }}</span> @if(!empty($purchase_line->actual_name)) {{$purchase_line->sub_unit->actual_name}} @else {{$purchase_line->product->unit->actual_name}} @endif
                  </td>
                @endif
                <td class="col-order-qty quantity">
                  @if($purchase->type == 'purchase_order')
                    <input style="width: 80px" type="text" name="quantity" class="form-control display_currency" value="{{ $purchase_line->quantity }}" data-currency_symbol="false" required  @if($purchase->type != 'purchase_order'|| $isLockModal) disabled @endif>
                  @else
                    <span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $purchase_line->quantity }}</span>
                  @endif
  
                  @if($purchase_line->product->unit->sub_units)
                    @foreach($purchase_line->product->unit->sub_units as $sub_unit)
                      @if($sub_unit->id == $purchase_line->sub_unit_id)
                        ({{ (float) $sub_unit->base_unit_multiplier }}
                        {{ $purchase_line->product->unit->short_name }})
                      @endif
                    @endforeach
                  @endif
                  @if(!empty($purchase_line->product->second_unit) && $purchase_line->secondary_unit_quantity != 0)
                      <br>
                      <span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $purchase_line->secondary_unit_quantity }}</span> {{$purchase_line->product->second_unit->actual_name}}
                  @endif
  
                </td>
                <td class="col-unit-cost unit_price">
                  @if($purchase->type == 'purchase_order')
                  <div class="input-group">
                      <span class="input-group-addon">$</span>
                      <input style="width: 80px" type="text" name="unit_price" class="form-control display_currency" value="{{ number_format($purchase_line->pp_without_discount, 2) }}" data-currency_symbol="true" required @if($purchase->type != 'purchase_order'||$isLockModal) disabled @endif>
                  </div>
                  @else
                    <span class="display_currency" data-currency_symbol="true">{{ $purchase_line->pp_without_discount }}</span>
                  @endif
                </td>
                <td class="col-discount discount">
                  @if($purchase->type == 'purchase_order')
                    <div class="tw-flex input-group" >
                      <input
                             type="text" 
                             name="discount"
                             class="form-control inline_discounts display_currency" 
                             style="width:100px"
                             value="{{ $purchase_line->discount_percent }}"
                             data-currency_symbol="true" @if($purchase->type != 'purchase_order'||$isLockModal) disabled @endif>
                      <select name="row_discount_type" 
                      style="width:80px"
                              class="form-control inline_discounts "  @if($purchase->type != 'purchase_order'||$isLockModal) disabled @endif>
                          <option value="fixed" 
                                  @if($purchase_line->row_discount_type == 'fixed') selected @endif>
                              $
                          </option>
                          <option value="percentage"
                                  @if($purchase_line->row_discount_type == 'percentage') selected @endif>
                              %
                          </option>
                      </select>
                    </div>
                  @else
                    @if($purchase_line->row_discount_type == 'fixed')
                      $
                    @elseif($purchase_line->row_discount_type == 'percentage') 
                      %
                    @endif
                    <span class="display_currency">{{ $purchase_line->discount_percent }}</span>
                  @endif
                </td>
                @if($purchase->type == 'purchase_order')
                <td class="col-final-cost no-print"><span class="display_currency unit_price_after_discount" data-currency_symbol="true">{{ $purchase_line->purchase_price }}</span></td>
                @endif
                @if($purchase->type != 'purchase_order')
                @if(session('business.enable_lot_number'))
                  <td>{{$purchase_line->lot_number}}</td>
                @endif
  
                @if(session('business.enable_product_expiry'))
                <td>
                  @if(!empty($purchase_line->mfg_date))
                      {{ @format_date($purchase_line->mfg_date) }}
                  @endif
                </td>
                <td>
                  @if(!empty($purchase_line->exp_date))
                      {{ @format_date($purchase_line->exp_date) }}
                  @endif
                </td>
                @endif
                @endif
                <td class="col-subtotal text-right">
                  <input type="hidden" class="subtotal_hidden" value="{{ $purchase_line->purchase_price_inc_tax * $purchase_line->quantity }}">
                  <span class="display_currency subtotal" data-currency_symbol="true">{{ $purchase_line->purchase_price_inc_tax * $purchase_line->quantity }}</span>
                </td>
                @if($purchase->type == 'purchase_order')
                     <td class="col-action delete_purchase_row handle_lock @if($isLockModal) hide @endif"><i class="fa fa-trash" aria-hidden="true" style='color:red'></i></td>
                @endif
              </tr>
              @php 
                $total_before_tax += ($purchase_line->quantity * $purchase_line->purchase_price);
              @endphp
            @endforeach
            </tbody>
            @if($purchase->type == 'purchase_order')
            <tfoot class="handle_lock @if($isLockModal) hide @endif no-print">
              <tr>
                <td></td>
                <td colspan="2">
                  {!! Form::text('search_product', null, ['class' => 'form-control mousetrap ui-autocomplete-input', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'autofocus']); !!}
                </td>
              </tr>
            </tfoot>  
                @endif
          </table>
        </div>
      </div>
    </div>
    <br>
    <div class="row">
      @if(!empty($purchase->type == 'purchase'))
      <div class="col-sm-12 col-xs-12">
        <h4>{{ __('sale.payment_info') }}:</h4>
      </div>
      <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ __('messages.date') }}</th>
                <th>{{ __('purchase.ref_no') }}</th>
                <th>{{ __('sale.amount') }}</th>
                <th>{{ __('sale.payment_mode') }}</th>
                <th>{{ __('sale.payment_note') }}</th>
              </tr>
            </thead>
            
            @php
    $total_paid = 0;
            @endphp
            @forelse($purchase->payment_lines as $payment_line)
              @php
      $total_paid += $payment_line->amount;
              @endphp
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ @format_date($payment_line->paid_on) }}</td>
                <td>{{ $payment_line->payment_ref_no }}</td>
                <td><span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }}</span></td>
                <td>{{ $payment_methods[$payment_line->method] ?? '' }}</td>
                <td>@if($payment_line->note) 
                  {{ ucfirst($payment_line->note) }}
                  @else
                  --
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center">
                  @lang('purchase.no_payments')
                </td>
              </tr>
            @endforelse
          </table>
        </div>
      </div>
      @endif
      <div class="col-md-6 col-sm-12 col-xs-12 @if($purchase->type == 'purchase_order') col-md-offset-6 @endif">
        <div class="table-responsive" style="border: 1px solid rgb(209, 209, 209); border-radius: 5px;">
          <table class="">
           <tbody>
             <!-- <tr class="hide">
              <th>@lang('purchase.total_before_tax'): </th>
              <td></td>
              <td><span class="display_currency pull-right">{{ $total_before_tax }}</span></td>
            </tr> -->
            <tr>
              <td class="tw-px-3 tw-py-0" style="width: 50%; background:rgb(209, 209, 209)">@lang('purchase.net_total_amount'): </td>
              <td class="tw-px-3 tw-py-0" ><span class="display_currency total_before_tax" data-currency_symbol="true">{{ $total_before_tax }}</span></td>
            </tr>
            <tr>
              <td class="tw-px-3 tw-py-0" style="background:rgb(209, 209, 209)">@lang('purchase.discount'):</td>
              {{-- <td>
                <b>(-)</b>
                @if($purchase->discount_type == 'percentage')
                  ({{$purchase->discount_amount}} %)
                @endif
              </td> --}}
              <td class="tw-px-3 tw-py-0" >
               @php
               $discount_show=$purchase->discount_amount;
               if($purchase->discount_type == 'percentage'){
                $discount_show=$purchase->discount_amount * $total_before_tax / 100;
               }else{
                $discount_show=$purchase->discount_amount;
               }
                @endphp
                <span class="display_currency" data-currency_symbol="true" id="discount_show">
                  {{ $discount_show }}
                </span>
                @if($purchase->discount_type == 'percentage')
                <span class="tw-text-xs tw-text-right">(
                  <span class="display_currency" >
                    {{$purchase->discount_amount}} 
                    </span>
                    %
                </span>
                )
              
                @endif
              </td>
            </tr>
            <tr>
              <td class="tw-px-3 tw-py-0" style="background:rgb(209, 209, 209)">@lang('purchase.purchase_tax'):</td>
              {{-- <td><b>(+)</b></td> --}}
              <td class="tw-px-3 tw-py-0" >
                  <input type="hidden" id="tax_rate" value="{{ $purchase->tax_amount ? ($total_before_tax > 0 ? ($purchase->tax_amount / $total_before_tax) * 100 : 0) : 0 }}">
                  <span class="display_currency tax_amount" data-currency_symbol="true">{{ $purchase->tax_amount }}</span>
                </td>
            </tr>
            @if(!empty($purchase->shipping_charges))
              <tr>
                <td class="tw-px-3 tw-py-0" style="background:rgb(209, 209, 209)">@lang('purchase.additional_shipping_charges'):</td>
                {{-- <td><b>(+)</b></td> --}}
                <td class="tw-px-3 tw-py-0" >
                  <input type="hidden" class="shipping_charges" value="{{ $purchase->shipping_charges }}">
                  <span class="display_currency " data-currency_symbol="true">{{ $purchase->shipping_charges }}</span>
                </td>
              </tr>
            @endif
            @if(!empty($purchase->additional_expense_value_1) && !empty($purchase->additional_expense_key_1))
              <tr>
                <td class="tw-px-3 tw-py-0" style="background:rgb(209, 209, 209)">{{ $purchase->additional_expense_key_1 }}:</td>
                {{-- <td><b>(+)</b></td> --}}
                <td class="tw-px-3 tw-py-0" ><span class="display_currency " >{{ $purchase->additional_expense_value_1 }}</span></td>
              </tr>
            @endif
            @if(!empty($purchase->additional_expense_value_2) && !empty($purchase->additional_expense_key_2))
              <tr>
                <td class="tw-px-3 tw-py-0" style="background:rgb(209, 209, 209)">{{ $purchase->additional_expense_key_2 }}:</td>
                {{-- <td><b>(+)</b></td> --}}
                <td class="tw-px-3 tw-py-0" ><span class="display_currency" >{{ $purchase->additional_expense_value_2 }}</span></td>
              </tr>
            @endif
            @if(!empty($purchase->additional_expense_value_3) && !empty($purchase->additional_expense_key_3))
              <tr>
                <td class="tw-px-3 tw-py-0" style="background:rgb(209, 209, 209)">{{ $purchase->additional_expense_key_3 }}:</td>
                {{-- <td><b>(+)</b></td> --}}
                <td class="tw-px-3 tw-py-0" ><span class="display_currency " >{{ $purchase->additional_expense_value_3 }}</span></td>
              </tr>
            @endif
            @if(!empty($purchase->additional_expense_value_4) && !empty($purchase->additional_expense_key_4))
              <tr>
                <td class="tw-px-3 tw-py-0" style="background:rgb(209, 209, 209)">{{ $purchase->additional_expense_key_4 }}:</td>
                {{-- <td><b>(+)</b></td> --}}
                <td class="tw-px-3 tw-py-0" ><span class="display_currency" >{{ $purchase->additional_expense_value_4 }}</span></td>
              </tr>
            @endif
            <tr>
              <td class="tw-px-3 tw-py-0" style="background:rgb(209, 209, 209)">@lang('purchase.purchase_total'):</td>
              {{-- <td></td> --}}
              <td class="tw-px-3 tw-py-0" ><span class="display_currency final_total " data-currency_symbol="true">{{ $purchase->final_total }}</span></td>
            </tr>
           </tbody>
          </table>
        </div>
      </div>
    </div>
  
    <div class="po_values">
    <input type="text" class="hide data_contact_id" value={{$purchase->contact_id}}>
    <input type="text" class="hide data_ref_no" value={{ $purchase->ref_no }}>
    <input type="date" class="hide data_transaction_date" value={{ $purchase->transaction_date}}>
    <input type="date" class="hide data_delivery_date" value={{ $purchase->delivery_date}}>
    <input type="text" class="hide data_exchange_rate" value={{ $purchase->exchange_rate}}>
    <input type="text" class="hide data_pay_term_number" value={{ $purchase->pay_term_number}}>
    <input type="text" class="hide data_pay_term_type" value={{ $purchase->pay_term_type}}>
    <input type="text" class="hide data_shipping_charges" value={{$purchase->shipping_charges}}>
    <input type="text" class="hide data_total_before_tax" value={{$purchase->total_before_tax}}>
    <input type="text" class="hide data_shipping_details" value={{$purchase->shipping_details}}>
    <input type="text" class="hide data_shipping_address" value={{$purchase->shipping_address}}>
    <input type="text" class="hide data_shipping_status" value={{$purchase->shipping_status}}>
    <input type="text" class="hide data_delivered_to" value={{$purchase->delivered_to}}>
    <input type="text" class="hide data_final_total" value={{$purchase->final_total}}>
    <input type="text" class="hide data_discount_type" value={{$purchase->discount_type}}>
    <input type="text" class="hide data_discount_amount" value={{$purchase->discount_amount}}>
    <input type="text" class="hide data_tax_id" value={{$purchase->tax_id}}>
    <input type="text" class="hide data_tax_amount" value={{$purchase->tax_amount}}>
  
    {!! Form::textarea('additional_notes', $purchase->additional_notes, ['class' => 'hide data_additional_notes', 'rows' => 1]); !!}
    {{-- <input type="textarea" class="hide data_additional_notes" value={{$purchase->additional_notes}}> --}}
  </div>
  
  
    {{-- Barcode --}}
    <div class="row print_section">
      <div class="col-xs-12">
        <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($purchase->ref_no, 'C128', 2, 30, array(39, 48, 54), true)}}">
      </div>
    </div>
  </div>
  
  {{-- Sells Note Modal --}}
  <div id="SellsNoteModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button"  id="close_button"  class="close-modal pull-right tw-p-2" style="border-radius: 5px" >Close</button>
            <h4 class="modal-title" id="modalTitle">Memo</h4>
        </div>
            <div class="modal-body">
              <div class="row">
                {{-- <div class="col-sm-6">
                  <strong>@lang('purchase.shipping_details'):</strong><br>
                  <p class="well well-sm no-shadow bg-gray">
                    {{ $purchase->shipping_details ?? '' }}
            
                    @if(!empty($purchase->shipping_custom_field_1))
                      <br><strong>{{$custom_labels['purchase_shipping']['custom_field_1'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_1}}
                    @endif
                    @if(!empty($purchase->shipping_custom_field_2))
                      <br><strong>{{$custom_labels['purchase_shipping']['custom_field_2'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_2}}
                    @endif
                    @if(!empty($purchase->shipping_custom_field_3))
                      <br><strong>{{$custom_labels['purchase_shipping']['custom_field_3'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_3}}
                    @endif
                    @if(!empty($purchase->shipping_custom_field_4))
                      <br><strong>{{$custom_labels['purchase_shipping']['custom_field_4'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_4}}
                    @endif
                    @if(!empty($purchase->shipping_custom_field_5))
                      <br><strong>{{$custom_labels['purchase_shipping']['custom_field_5'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_5}}
                    @endif
                  </p>
                </div> --}}
                <div class="col-sm-12">
                  <strong>Memo:</strong><br>
                  <p class="well well-sm no-shadow bg-gray">
                    @if($purchase->additional_notes)
                      {{ $purchase->additional_notes }}
                    @else
                      --
                    @endif
                  </p>
                </div>
              </div>
            </div>
           
        </div>
    </div>
  </div>
  
  {{-- Activity Modal --}}
  <div id="ActivityModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close-modal pull-right tw-p-2"  id="close_button" style="border-radius: 5px">Close</button>
            <h4 class="modal-title" id="modalTitle">Activity</h4>
        </div>
            <div class="modal-body">
              @if(!empty($activities))
              <div class="row">
                <div class="col-md-12">
                      <strong>{{ __('lang_v1.activities') }}:</strong><br>
                      @includeIf('activity_log.activities', ['activity_type' => 'purchase'])
                  </div>
              </div>
              @endif
            </div>
        </div>
    </div>
  </div>  
</div>
@if($purchase->type == 'purchase_order')
<script src="{{ asset('js/session_lock.js?v=' . $asset_v) }}"></script>
@endif
<script type="text/javascript">
  $(document).ready(function () {
      var purchase_id = @json($purchase -> id)
      // Handle supplier reference number update
  
      // open modal
      // activity modal
  
      $("#openActivityModal").on("click", function (e) {
  
          var modal_element = $('div.enable_session_lock');
          let url = `/sells/pos/activity_modal/${purchase_id}`;
          let modalId = 'modal-' + new Date().getTime();
          $.ajax({
              url: url,
              success: function (response) {
                  let newModal = $('<div class="modal fade" id="' + modalId +
                      '" data-backdrop="static" data-keyboard="false">' +
                      +
                      '<div class="modal-content">' + response + +
                      '</div>' +
                      '</div>');
                  $('body').append(newModal);
                  newModal.modal('show');
  
                  // Remove modal from DOM after closing
                  newModal.on('hidden.bs.modal', function () {
                      $(this).remove();
                  });
              }
          });
      });
  
  
      //open sell note modal
  
      $("#openSellsNoteModal").on("click", function (e) {
          var modal_element = $('div.enable_session_lock');
          let url = `/sells/pos/sell_note_modal/${purchase_id}`;
          let modalId = 'modal-' + new Date().getTime();
          $.ajax({
              url: url,
              success: function (response) {
                  let newModal = $('<div class="modal fade" id="' + modalId +
                      '" data-backdrop="static" data-keyboard="false">' +
                      +
                      '<div class="modal-content">' + response + +
                      '</div>' +
                      '</div>');
                  $('body').append(newModal);
                  newModal.modal('show');
  
                  // Remove modal from DOM after closing
                  newModal.on('hidden.bs.modal', function () {
                      $(this).remove();
                  });
              }
          });
      });
  
      //history Modal
      $(".product_history").on("click", function (e) {
  
          let url = $(this).data('href');
          let modalId = 'modal-' + new Date().getTime();
  
          $.ajax({
              url: url,
              success: function (response) {
                  let newModal = $('<div class="modal fade" id="' + modalId +
                      '" data-backdrop="static" data-keyboard="false">' +
                      +
                      '<div class="modal-content">' + response + +
                      '</div>' +
                      '</div>');
                  $('body').append(newModal);
                  newModal.modal('show');
                  newModal.on('hidden.bs.modal', function () {
                      $(this).remove();
                  });
              }
          });
      });
  
      $(document).on('blur', '.supplier-ref-no-input', function () {
          let id = $(this).data('id');
          let value = $(this).text().replace('#', '').trim();
  
          if (!value) {
              toastr.error('Supplier reference number cannot be empty');
              $(this).text('#' + $(this).data('value')); // Restore previous value
              return;
          }
  
          swal({
              title: 'Are you sure?',
              text: "You are about to update the supplier's reference number.",
              icon: 'warning',
              buttons: true,
              dangerMode: true,
          }).then((willUpdate) => {
              if (willUpdate) {
                  $.ajax({
                      method: 'POST',
                      url: '/purchases/update-ref-no/' + id,
                      data: {
                          supplier_ref_no: value,
                          _token: $('meta[name="csrf-token"]').attr('content')
                      },
                      success: function (result) {
                          if (result.success == true) {
                              toastr.success(result.msg);
                              $('.supplier-ref-no-input[data-id="' + id + '"]').data('value', value);
                          } else {
                              toastr.error(result.msg);
                              $('.supplier-ref-no-input[data-id="' + id + '"]').text('#' + $('.supplier-ref-no-input[data-id="' + id + '"]').data('value'));
                          }
                      },
                      error: function (xhr, status, error) {
                          toastr.error('Failed to update supplier reference number');
                          $('.supplier-ref-no-input[data-id="' + id + '"]').text('#' + $('.supplier-ref-no-input[data-id="' + id + '"]').data('value'));
                      }
                  });
              } else {
                  $(this).text('#' + $(this).data('value'));
              }
          });
      });
  
      var search_fields = ['sku','name','var_barcode_no','sub_sku'];
      $('#configure_search_modal input[name="search_fields[]"]').on('ifChanged', function () {
          search_fields = [];
          $('#configure_search_modal input[name="search_fields[]"]:checked').each(function () {
              search_fields.push($(this).val());
          });
      });
      $('#configure_search_modal input[name="search_fields[]"]:checked').each(function () {
          search_fields.push($(this).val());
      });
      if ($('#search_product').length > 0) {
          $('#search_product')
              .autocomplete({
                  source: function (request, response) {
                      // var isParent = $('#toggle_switch').prop('checked') ? true : false;
                      var url = '/purchases/get_products';
                      $.getJSON(
                          url,
                          { location_id: $('#location_id').val(), term: request.term, search_fields: search_fields, isParent: false },
                          response
                      );
                  },
                  minLength: 2,
                  response: function (event, ui) {
                      if (ui.content.length == 1) {
                          ui.item = ui.content[0];
                          $(this)
                              .data('ui-autocomplete')
                              ._trigger('select', 'autocompleteselect', ui);
                          $(this).autocomplete('close');
                      } else if (ui.content.length == 0) {
                          var term = $(this).data('ui-autocomplete').term;
                          toastr.error('No products found');
                      }
                  },
                  select: function (event, ui) {
                      $(this).val(null);
                      get_purchase_entry_row(ui.item.product_id, ui.item.variation_id);
                  },
              })
              .autocomplete('instance')._renderItem = function (ul, item) {
                  return $('<li>')
                      .append('<div>' + item.text + '</div>')
                      .appendTo(ul);
              };
      }
  
      function buildPurchaseRow(result) {
          const product = result.product;
          const variation = result.variations[0];
          const unit = product.unit ? product.unit.actual_name : '';
          const subSku = variation.sub_sku || '';
          const productName = product.name + (product.type === 'variable' ? ' - ' + (variation.product_variation ? variation.product_variation.name : '') + ' - ' + variation.name : '');
          const quantity = 1; // Default to 1, or set as needed
          const unitPrice = variation.default_purchase_price || 0;
          const discount = 0; // Default, or set as needed
          const discountType = 'fixed'; // Default, or set as needed
          const subtotal = unitPrice * quantity;
  
          return `
              <tr class="purchase-line" data-purchase-line-id="">
                  <td class="col-idx"></td>
                  <td class="col-sku">${subSku}</td>
                  <td class="product-col">
                      <div class="hide puchase_line_data">
                          <input type="text" class="hide data_product_id" value="${product.id}">
                          <input type="text" class="hide data_variation_id" value="${variation.id}">
                          <input type="text" class="hide data_purchase_line_id" value="">
                          <input type="text" class="hide data_quantity" value="${quantity}">
                          <input type="text" class="hide data_product_unit_id" value="${product.unit_id}">
                          <input type="text" class="hide data_pp_without_discount" value="${unitPrice}">
                          <input type="text" class="hide data_row_discount_percent" value="${discount}">
                          <input type="text" class="hide data_row_discount_type" value="${discountType}">
                          <input type="text" class="hide data_purchase_price" value="${unitPrice}">
                          <input type="text" class="hide data_purchase_line_tax_id" value="">
                          <input type="text" class="hide data_item_tax" value="">
                          <input type="text" class="hide data_purchase_price_inc_tax" value="${unitPrice}">
                          <input type="text" class="hide data_profit_percent" value="${variation.profit_percent}">
                      </div>
                      ${productName}
                  </td>
                  <td class="col-qty-remaining">${result.stock_qty} ${unit}</td>
                  <td class="col-order-qty quantity">
                      <input style="width: 80px" type="text" name="quantity" class="form-control display_currency" value="${quantity}" data-currency_symbol="false" required>
                  </td>
                  <td class="col-unit-cost unit_price">
                     <div class="input-group">
                      <span class="input-group-addon">$</span>
                     <input style="width: 80px" type="text" name="unit_price" class="form-control display_currency" value="${parseFloat(unitPrice).toFixed(2)}" data-currency_symbol="true" required>
                  </div>
                      
                  </td>
                  <td class="col-discount discount">
                      <div class="tw-flex input-group">
                          <input type="text" name="discount" class="form-control inline_discounts display_currency" value="${discount}" data-currency_symbol="true" style="width:100px">
                          <select name="row_discount_type" class="form-control inline_discounts" style="width:80px">
                              <option value="fixed" ${discountType === 'fixed' ? 'selected' : ''}>$</option>
                              <option value="percentage" ${discountType === 'percentage' ? 'selected' : ''}>%</option>
                          </select>
                      </div>
                  </td>
                  <td class="col-final-cost no-print">
      <span class="display_currency unit_price_after_discount" data-currency_symbol="true">
         $ ${parseFloat(unitPrice).toFixed(2)}
      </span>
  </td>
                  <td class="col-subtotal text-right">
                      <input type="hidden" class="subtotal_hidden" value="${subtotal}">
                      <span class="display_currency subtotal" data-currency_symbol="true">
      $ ${parseFloat(subtotal).toFixed(2)}
  </span>
                  </td>
                  <td class="col-action delete_purchase_row handle_lock"><i class="fa fa-trash" aria-hidden="true" style='color:red'></i></td>
              </tr>
          `;
      }
  
      function updateRowNumbers() {
          $('#purchase_order_table_modal tbody tr').each(function (index) {
              $(this).find('td:first').text(index + 1);
          });
      }
  
      function get_purchase_entry_row(product_id, variation_id) {
          if (product_id) {
              var row_count = $('#row_count').val();
              var location_id = $('#location_id').val();
              var supplier_id = $('#supplier_id').val();
              var data = {
                  product_id: product_id,
                  row_count: row_count,
                  variation_id: variation_id,
                  location_id: location_id,
                  supplier_id: supplier_id
              };
              if ($('#is_purchase_order').length) {
                  data.is_purchase_order = true;
              }
              $.ajax({
                  method: 'POST',
                  url: '/purchases/get_purchase_entry_row/popup',
                  data: data,
                  success: function (result) {
                      var newRow = buildPurchaseRow(result);
                      $('#purchase_order_table_modal tbody').append(newRow);
                      updateRowNumbers();
                  },
              });
          }
      }
  
      // Listen for changes in quantity input fields
      $(document).on('input', '#purchase_order_table_modal input[name="quantity"]', function () {
          var $row = $(this).closest('tr');
          var quantity = parseFloat($(this).val()) || 0;
          var unit_price = parseFloat($row.find('input[name="unit_price"]').val()) || 0;
          var discount = parseFloat($row.find('input[name="discount"]').val()) || 0;
          var discount_type = $row.find('select[name="row_discount_type"]').val();
  
          // Calculate discount amount
          var discount_amount = 0;
          if (discount_type === 'percentage') {
              discount_amount = unit_price * (discount / 100);
          } else {
              discount_amount = discount;
          }
  
          // Calculate final unit price after discount
          var final_unit_price = unit_price - discount_amount;
          if (final_unit_price < 0) final_unit_price = 0;
  
          // Calculate subtotal for the row
          var subtotal = final_unit_price * quantity;
  
          // Update the hidden and visible subtotal fields
          $row.find('.subtotal_hidden').val(subtotal);
          $row.find('.subtotal').text(subtotal.toFixed(2));
  
          // Update the unit price after discount field
          $row.find('.unit_price_after_discount').text(final_unit_price.toFixed(2));
  
          // Update the display_currency class (if you have a function for formatting)
          if (typeof __currency_convert_recursively === "function") {
              __currency_convert_recursively($row);
          }
  
          $row.find('.data_quantity').val(quantity);
          $row.find('.data_pp_without_discount').val(unit_price);
          $row.find('.data_row_discount_percent').val(discount_amount);
          $row.find('.data_row_discount_type').val(discount_type);
          $row.find('.data_purchase_price').val(final_unit_price);
          $row.find('.data_purchase_price_inc_tax').val(final_unit_price);
  
          // Update totals
          updateTotals();
      });
  
      // Listen for changes in unit price, discount, or discount type as well
      $(document).on('input change', '#purchase_order_table_modal input[name="unit_price"], #purchase_order_table_modal input[name="discount"], #purchase_order_table_modal select[name="row_discount_type"]', function () {
          $(this).closest('tr').find('input[name="quantity"]').trigger('input');
      });
  
      // Function to update net total and purchase total
      function updateTotals() {
          var total_before_tax = 0;
          $('#purchase_order_table_modal .subtotal_hidden').each(function () {
              total_before_tax += parseFloat($(this).val()) || 0;
          });
  
          // Update net total amount
          $('.total_before_tax').text(total_before_tax.toFixed(2));
          $('.data_total_before_tax').val(total_before_tax);
  
  
          // Calculate discount
          var discount_type = $('.data_discount_type').val();
          var discount_amount = parseFloat($('.data_discount_amount').val()) || 0;
          var discount = 0;
          if (discount_type === 'percentage') {
              discount = total_before_tax * (discount_amount / 100);
          } else {
              discount = discount_amount;
          }
  
          // Calculate tax
          var tax_amount = parseFloat($('.data_tax_amount').val()) || 0;
  
          // Shipping charges
          var shipping_charges = parseFloat($('.shipping_charges').val()) || 0;
  
          // Additional expenses
          var additional_expense_1 = parseFloat($('input[name="additional_expense_value_1"]').val()) || 0;
          var additional_expense_2 = parseFloat($('input[name="additional_expense_value_2"]').val()) || 0;
          var additional_expense_3 = parseFloat($('input[name="additional_expense_value_3"]').val()) || 0;
          var additional_expense_4 = parseFloat($('input[name="additional_expense_value_4"]').val()) || 0;
  
          // Final total calculation
          var final_total = total_before_tax - discount + tax_amount + shipping_charges + additional_expense_1 + additional_expense_2 + additional_expense_3 + additional_expense_4;
  
          // Update purchase total
          $('.final_total').text(final_total.toFixed(2));
          $('#discount_show').text(discount.toFixed(2));
          $('.data_final_total').val(final_total);
  
          // Update display_currency formatting if needed
          if (typeof __currency_convert_recursively === "function") {
              __currency_convert_recursively($('.final_total').closest('table'));
          }
      }
  
      // Helper to show error below input
      function showInputError($input, message) {
          let $error = $input.siblings('.input-error');
          if ($error.length === 0) {
              $error = $('<span class="input-error" style="color:red;font-size:12px;display:block;"></span>');
              $input.after($error);
          }
          $error.text(message);
      }
      function clearInputError($input) {
          $input.siblings('.input-error').text('');
      }
  
      // Validate Quantity
      $(document).on('input', '#purchase_order_table_modal input[name="quantity"]', function () {
          var $input = $(this);
          var value = $input.val();
          if (!/^[0-9]+(\.[0-9]+)?$/.test(value) || parseFloat(value) <= 0) {
              showInputError($input, 'Quantity must be a positive number.');
          } else {
              clearInputError($input);
          }
      });
  
      // Validate Unit Price
      $(document).on('input', '#purchase_order_table_modal input[name="unit_price"]', function () {
          var $input = $(this);
          var value = $input.val();
          if (!/^[0-9]+(\.[0-9]+)?$/.test(value) || parseFloat(value) < 0) {
              showInputError($input, 'Unit price must be a non-negative number.');
          } else {
              clearInputError($input);
          }
      });
  
      // Validate Discount
      $(document).on('input', '#purchase_order_table_modal input[name="discount"]', function () {
          var $input = $(this);
          var value = $input.val();
          var $row = $input.closest('tr');
          var discountType = $row.find('select[name="row_discount_type"]').val();
          var unitPrice = parseFloat($row.find('input[name="unit_price"]').val()) || 0;
          if (!/^[0-9]+(\.[0-9]+)?$/.test(value) || parseFloat(value) < 0) {
              showInputError($input, 'Discount must be a non-negative number.');
          } else if (discountType === 'percentage' && parseFloat(value) > 100) {
              showInputError($input, 'Discount percentage cannot exceed 100%.');
          } else if (discountType === 'fixed' && parseFloat(value) > unitPrice) {
              showInputError($input, 'Discount amount cannot exceed unit price.');
          } else {
              clearInputError($input);
          }
      });
  
      // Validate Discount Type
      $(document).on('change', '#purchase_order_table_modal select[name="row_discount_type"]', function () {
          var $select = $(this);
          var value = $select.val();
          if (value !== 'fixed' && value !== 'percentage') {
              // For select, show error after select
              let $error = $select.siblings('.input-error');
              if ($error.length === 0) {
                  $error = $('<span class="input-error" style="color:red;font-size:12px;display:block;"></span>');
                  $select.after($error);
              }
              $error.text('Invalid discount type selected.');
              $select.val('fixed');
          } else {
              $select.siblings('.input-error').text('');
          }
      });
  
      // Save button click handler to gather payload and log it
      $('#save_button_purchase').on('click', function (e) {
          e.preventDefault();
  
          var formData = new FormData();
          formData.append('_method', 'POST');
          formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
  
          // Main fields
          formData.append('contact_id', $('.data_contact_id').val());
          formData.append('ref_no', $('.data_ref_no').val());
          formData.append('transaction_date', $('.data_transaction_date').val());
          formData.append('delivery_date', $('.data_delivery_date').val());
          formData.append('exchange_rate', $('.data_exchange_rate').val());
          formData.append('pay_term_number', $('.data_pay_term_number').val());
          formData.append('pay_term_type', $('.data_pay_term_type').val());
  
          formData.append('shipping_charges', $('.data_shipping_charges').val());
          formData.append('total_before_tax', $('.data_total_before_tax').val());
          formData.append('shipping_details', $('.data_shipping_details').val());
          formData.append('shipping_address', $('.data_shipping_address').val());
          formData.append('shipping_status', $('.data_shipping_status').val());
          formData.append('delivered_to', $('.data_delivered_to').val());
          formData.append('final_total', $('.data_final_total').val());
          formData.append('discount_type', $('.data_discount_type').val());
          formData.append('discount_amount', $('.data_discount_amount').val());
          formData.append('tax_id', $('.data_tax_id').val());
          formData.append('tax_amount', $('.data_tax_amount').val());
          formData.append('additional_notes', $('.data_additional_notes').val());
          formData.append('search_product', $('#search_product').val() || '');
  
          // Additional Expenses
          for (let i = 1; i <= 4; i++) {
              formData.append('additional_expense_key_' + i, $('[name="additional_expense_key_' + i + '"]').val() || '');
              formData.append('additional_expense_value_' + i, $('[name="additional_expense_value_' + i + '"]').val() || '0.00');
          }
  
          // Handle purchase lines
          $('#purchase_order_table_modal tbody tr').each(function (index) {
              const $row = $(this);
              const prefix = `purchases[${index}]`;
  
              formData.append(`${prefix}[product_id]`, $row.find('.data_product_id').val());
              formData.append(`${prefix}[variation_id]`, $row.find('.data_variation_id').val());
  
              let lineId = $row.find('.data_purchase_line_id').val();
              if (lineId) formData.append(`${prefix}[purchase_line_id]`, lineId);
  
              formData.append(`${prefix}[quantity]`, $row.find('.data_quantity').val());
              formData.append(`${prefix}[product_unit_id]`, $row.find('.data_product_unit_id').val());
              formData.append(`${prefix}[pp_without_discount]`, $row.find('.data_pp_without_discount').val());
              formData.append(`${prefix}[row_discount_type]`, $row.find('.data_row_discount_type').val());
              formData.append(`${prefix}[discount_percent]`, $row.find('.data_row_discount_percent').val());
              formData.append(`${prefix}[purchase_price]`, $row.find('.data_purchase_price').val());
              formData.append(`${prefix}[purchase_line_tax_id]`, $row.find('.data_purchase_line_tax_id').val());
              formData.append(`${prefix}[item_tax]`, $row.find('.data_item_tax').val());
              formData.append(`${prefix}[purchase_price_inc_tax]`, $row.find('.data_purchase_price_inc_tax').val());
              formData.append(`${prefix}[profit_percent]`, $row.find('.data_profit_percent').val());
          });
  
          // Optional: Handle shipping_documents file if any
          const shippingFiles = $('[name="shipping_documents[]"]')[0];
          if (shippingFiles && shippingFiles.files.length > 0) {
              for (let i = 0; i < shippingFiles.files.length; i++) {
                  formData.append('shipping_documents[]', shippingFiles.files[i]);
              }
          }
  
          // Submit via AJAX
          $.ajax({
              url: `/update-purchase-orders-modal/${purchase_id}`,
              method: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function (response) {
                  // Reset button state
                  $('#save_button_purchase').prop('disabled', false).html('Save');
  
                  if (response.success) {
                      // Show success message
                      toastr.success(response.msg || 'Purchase order updated successfully!');
  
                      // Close the modal after a short delay
                      setTimeout(function () {
                          $('.modal').modal('hide');
                          // Optionally refresh the page or update the parent view
                          if (typeof window.parent !== 'undefined' && window.parent.location) {
                              window.parent.location.reload();
                          }
                      }, 1500);
                  } else {
                      // Show error message
                      toastr.error(response.msg || 'Failed to update purchase order. Please try again.');
                  }
              },
              error: function (xhr) {
                  // Reset button state
                  $('#save_button_purchase').prop('disabled', false).html('Save');
  
                  let errorMessage = 'An error occurred while updating the purchase order.';
  
                  if (xhr.responseJSON && xhr.responseJSON.message) {
                      errorMessage = xhr.responseJSON.message;
                  } else if (xhr.responseText) {
                      try {
                          const response = JSON.parse(xhr.responseText);
                          if (response.message) {
                              errorMessage = response.message;
                          }
                      } catch (e) {
                          // If response is not JSON, use default message
                      }
                  }
  
                  toastr.error(errorMessage);
                  console.error('Error:', xhr.responseText);
              }
          });
  
      });
  
      // Delete purchase row functionality
      $(document).on('click', '.delete_purchase_row', function (e) {
          e.preventDefault();
  
          var $row = $(this).closest('tr');
          var productName = $row.find('td:eq(1)').text().trim();
  
          // Show confirmation dialog
          swal({
              title: 'Are you sure?',
              text: `Do you want to remove "${productName}" from the purchase order?`,
              icon: 'warning',
              buttons: true,
              dangerMode: true,
          }).then((willDelete) => {
              if (willDelete) {
                  // Remove the row
                  $row.remove();
  
                  // Update row numbers
                  updateRowNumbers();
  
                  // Update totals
                  updateTotals();
  
                  // Show success message
                  toastr.success('Product removed from purchase order successfully!');
              }
          });
      });
  
  });
  </script>
