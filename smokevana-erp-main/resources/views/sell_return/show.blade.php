<div class="modal-dialog modal-xl no-print" role="document">
  <div class="modal-content">
    <div class="modal-header">
    {{-- <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> --}}
    <h4 class="modal-title" id="modalTitle"> @lang('lang_v1.sell_return') (<b>@lang('sale.invoice_no'):</b> {{ $sell->return_parent->invoice_no }})</h4>
    {{-- Date Field --}}
       <p>@lang('messages.date'): {{ @format_date($sell->return_parent->transaction_date) }}</p>
  
    {{-- Buttons --}}   
    <div class="tw-flex tw-justify-end tw-gap-2" style="margin-top: -50px">
        {{-- <a href="#" class="tw-dw-btn tw-dw-btn-primary tw-text-white product_history" tabindex="2">📜Item History</a> --}}
        @can('print_invoice')
            <a href="#" class="print-invoice tw-dw-btn tw-dw-btn-primary tw-text-white" data-href="{{action([\App\Http\Controllers\SellReturnController::class, 'printInvoice'], [$sell->return_parent->id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a>
            @endcan
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" style="margin-left: 25px;" data-dismiss="modal"
                id='close_button'>@lang( 'messages.close')</button>
    </div> 
    </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-2 col-xs-2">
                      <b>
                        @if ($sell->type == 'sales_order')
                            {{ __('restaurant.order_no') }}
                        @else
                            {{ __('sale.invoice_no') }}
                        @endif:
                    </b> {{ $sell->invoice_no }}<br>
                    <b>{{ __('sale.status') }}:</b>
                     @if ($sell->status == 'draft' && $sell->is_quotation == 1)
                        {{ __('lang_v1.quotation') }}
                    @else
                        {{ $statuses[$sell->status] ?? __('sale.' . $sell->status) }}
                    @endif
                    <br>
                    @if ($sell->type != 'sales_order')
                        <b>{{ __('sale.payment_status') }}:</b>
                        @if (!empty($sell->payment_status))
                            {{ __('lang_v1.' . $sell->payment_status) }}
                        @endif
                    @endif

                     @if (!empty($custom_labels['sell']['custom_field_1']))
                        <br><strong>{{ $custom_labels['sell']['custom_field_1'] ?? '' }}: </strong>
                        {{ $sell->custom_field_1 }}
                    @endif
                    @if (!empty($custom_labels['sell']['custom_field_2']))
                        <br><strong>{{ $custom_labels['sell']['custom_field_2'] ?? '' }}: </strong>
                        {{ $sell->custom_field_2 }}
                    @endif
                    @if (!empty($custom_labels['sell']['custom_field_3']))
                        <br><strong>{{ $custom_labels['sell']['custom_field_3'] ?? '' }}: </strong>
                        {{ $sell->custom_field_3 }}
                    @endif
                    @if (!empty($custom_labels['sell']['custom_field_4']))
                        <br><strong>{{ $custom_labels['sell']['custom_field_4'] ?? '' }}: </strong>
                        {{ $sell->custom_field_4 }}
                    @endif
                </div>


        {{-- <h4>@lang('lang_v1.sell_return_details'):</h4> --}}
        {{-- <strong>@lang('lang_v1.return_date'):</strong> {{@format_date($sell->return_parent->transaction_date)}}<br>
        <strong>@lang('contact.customer'):</strong> {{ $sell->contact->name }} <br>
        <strong>@lang('purchase.business_location'):</strong> {{ $sell->location->name }} --}}
        
        <div class="@if (!empty($export_custom_fields)) col-sm-2 @else col-sm-2 @endif tw-w-full">
                    @php
                        $is_b2c_location = !empty($sell->location) && $sell->location->is_b2c == 1;
                    @endphp
                    
                    @if (!$is_b2c_location)
                        @if (!empty($sell->contact->contact_id))
                            <b>Account No: </b>{{ $sell->contact->contact_id }}</b>
                            <br>
                        @endif
                        
                        @if (!empty($sell->contact->supplier_business_name))
                            <b>Business Name: </b>{{ $sell->contact->supplier_business_name }}</b>
                            <br>
                        @endif
                    @endif
                    
                    @if (!empty($sell->contact->name))
                        <b>{{ __('sale.customer_name') }}:</b> {{ $sell->contact->name }}</b>
                        <br>
                    @endif
                    
                    @if (!$is_b2c_location)
                        @if ($sell->contact->mobile)
                           <b>{{ __('contact.mobile') }}</b> : {{ $sell->contact->mobile }}
                           <br>
                        @endif
                        @if ($sell->contact->alternate_number)
                            <b>{{ __('contact.alternate_contact_number') }}</b> : {{ $sell->contact->alternate_number }}
                            <br>
                        @endif
                        @if ($sell->contact->landline)
                            <b>{{ __('contact.landline') }}</b> : {{ $sell->contact->landline }}
                            <br>
                        @endif
                        @if ($sell->contact->email)
                            <b>{{ __(key: 'business.email') }}</b> : {{ $sell->contact->email }}
                        @endif
                    @endif

        </div>

                <div class="@if (!empty($export_custom_fields)) col-sm-2 @else col-sm-2 @endif tw-w-full">
                    <b>Billing Address:</b>
                    <br>
                    @if (!empty($sell->billing_address()))
                        {{ $sell->billing_address() }}
                    @else
                        {!! $sell->contact->contact_address !!}
                    @endif
                </div>

                <div class="@if (!empty($export_custom_fields)) col-sm-3 @else col-sm-4 @endif">
                    @if (in_array('tables', $enabled_modules))
                        <strong>@lang('restaurant.table'):</strong>
                        {{ $sell->table->name ?? '' }}<br>
                    @endif
                    @if (in_array('service_staff', $enabled_modules))
                        <strong>@lang('restaurant.service_staff'):</strong>
                        {{ $sell->service_staff->user_full_name ?? '' }}<br>
                    @endif
                    <strong>Shipping Address :</strong> 
                    <br>
                    <span class="shipping_company_name">
                    @if (!empty($sell->shipping_company))
                      {{ $sell->shipping_company }}
                    @endif
                    </span>
                    <br/>
                    <span class="shipping_first_name">
                    @if (!empty($sell->shipping_first_name))
                      {{ $sell->shipping_first_name }}
                    @endif
                    </span>
                    <span class="shipping_last_name">
                    @if (!empty($sell->shipping_last_name))
                       {{ $sell->shipping_last_name }}
                    @endif
                   </span>
                    <br>
                    <span class="shipping_address1_name">
                    @if (!empty($sell->shipping_address1))
                        {{ $sell->shipping_address1 }}
                    @endif
                    </span>
                    <span class="shipping_address2_name">
                    @if (!empty($sell->shipping_address2))
                    {{ $sell->shipping_address2 }}
                    @endif
                    </span>
                    <br/>
                    <span class="shipping_city_name">
                    @if (!empty($sell->shipping_city))
                    {{ $sell->shipping_city }}
                    @endif
                    </span>
                    <span class="shipping_state_name">
                    @if (!empty($sell->shipping_state))
                        {{ $sell->shipping_state }}
                    @endif
                    </span>
                    <span class="shipping_zip_name">
                    @if (!empty($sell->shipping_zip))
                        {{ $sell->shipping_zip }}
                    @endif
                    </span>
                    <span class="shipping_country_name">
                    @if (!empty($sell->shipping_zip))
                        {{ $sell->shipping_country }}
                    @endif
                   </span>
                    <span
                        class="label @if (!empty($shipping_status_colors[$sell->shipping_status])) {{ $shipping_status_colors[$sell->shipping_status] }} @else {{ 'bg-gray' }} @endif">{{ $shipping_statuses[$sell->shipping_status] ?? '' }}</span><br>
                        <div class="hide">
                            @if (!empty($sell->shipping_address()))
                        {{ $sell->shipping_address() }}
                    @else
                        {{ $sell->shipping_address ?? '--' }}
                    @endif
                    @if (!empty($sell->delivered_to))
                        <br><strong>@lang('lang_v1.delivered_to'): </strong> {{ $sell->delivered_to }}
                    @endif
                    @if (!empty($sell->delivery_person_user->first_name))
                        <br><strong>@lang('lang_v1.delivery_person'): </strong> {{ $sell->delivery_person_user->surname }}
                        {{ $sell->delivery_person_user->first_name }} {{ $sell->delivery_person_user->last_name }}
                    @endif
                    @if (!empty($sell->shipping_custom_field_1))
                        <br><strong>{{ $custom_labels['shipping']['custom_field_1'] ?? '' }}: </strong>
                        {{ $sell->shipping_custom_field_1 }}
                    @endif
                    @if (!empty($sell->shipping_custom_field_2))
                        <br><strong>{{ $custom_labels['shipping']['custom_field_2'] ?? '' }}: </strong>
                        {{ $sell->shipping_custom_field_2 }}
                    @endif
                    @if (!empty($sell->shipping_custom_field_3))
                        <br><strong>{{ $custom_labels['shipping']['custom_field_3'] ?? '' }}: </strong>
                        {{ $sell->shipping_custom_field_3 }}
                    @endif
                    @if (!empty($sell->shipping_custom_field_4))
                        <br><strong>{{ $custom_labels['shipping']['custom_field_4'] ?? '' }}: </strong>
                        {{ $sell->shipping_custom_field_4 }}
                    @endif
                    @if (!empty($sell->shipping_custom_field_5))
                        <br><strong>{{ $custom_labels['shipping']['custom_field_5'] ?? '' }}: </strong>
                        {{ $sell->shipping_custom_field_5 }}
                    @endif
                    @php
                        $medias = $sell->media->where('model_media_type', 'shipping_document')->all();
                    @endphp
                    @if (count($medias))
                        @include('sell.partials.media_table', ['medias' => $medias])
                    @endif
                    @if (in_array('types_of_service', $enabled_modules))
                        @if (!empty($sell->types_of_service))
                            <strong>@lang('lang_v1.types_of_service'):</strong>
                            {{ $sell->types_of_service->name }}<br>
                        @endif
                        @if (!empty($sell->types_of_service->enable_custom_fields))
                            <strong>{{ $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1') }}:</strong>
                            {{ $sell->service_custom_field_1 }}<br>
                            <strong>{{ $custom_labels['types_of_service']['custom_field_2'] ?? __('lang_v1.service_custom_field_2') }}:</strong>
                            {{ $sell->service_custom_field_2 }}<br>
                            <strong>{{ $custom_labels['types_of_service']['custom_field_3'] ?? __('lang_v1.service_custom_field_3') }}:</strong>
                            {{ $sell->service_custom_field_3 }}<br>
                            <strong>{{ $custom_labels['types_of_service']['custom_field_4'] ?? __('lang_v1.service_custom_field_4') }}:</strong>
                            {{ $sell->service_custom_field_4 }}<br>
                            <strong>{{ $custom_labels['types_of_service']['custom_field_5'] ?? __('lang_v1.custom_field', ['number' => 5]) }}:</strong>
                            {{ $sell->service_custom_field_5 }}<br>
                            <strong>{{ $custom_labels['types_of_service']['custom_field_6'] ?? __('lang_v1.custom_field', ['number' => 6]) }}:</strong>
                            {{ $sell->service_custom_field_6 }}
                        @endif
                    @endif
                        </div>
                </div>
      {{-- <div class="col-sm-6 col-xs-6">
        <h4>@lang('lang_v1.sell_details'):</h4>
        <strong>@lang('sale.invoice_no'):</strong> {{ $sell->invoice_no }} <br>
        <strong>@lang('messages.date'):</strong> {{@format_date($sell->transaction_date)}}
      </div> --}}
                </div>
            
        <br>
        <div class="row" style="max-height:55vh; overflow-y: auto; min-height: 55vh;">
            <div class="col-sm-12 col-xs-12 tw-flex tw-justify-between">
                <br>
        <table class="tw-w-full">
          <thead>
                    <tr>
                <th>#</th>
                <th>@lang('product.product_name')</th>
                <th>@lang('sale.unit_price')</th>
                <th>@lang('lang_v1.return_quantity')</th>
                <th>@lang('lang_v1.return_subtotal')</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $total_before_tax = 0;
                    @endphp
                    @foreach($sell->sell_lines as $sell_line)

                    @if($sell_line->quantity_returned == 0)
                        @continue
                    @endif

                    @php
                    $unit_name = $sell_line->product->unit->short_name;

                    if(!empty($sell_line->sub_unit)) {
                        $unit_name = $sell_line->sub_unit->short_name;
                    }
                    @endphp

                    <tr style="background: rgb(252, 230, 225);">
                        <td>{{ $loop->iteration }}</td>
                        <td>
                        {{ $sell_line->product->name }}
                        @if( $sell_line->product->type == 'variable')
                            - {{ $sell_line->variations->product_variation->name}}
                            - {{ $sell_line->variations->name}}
                        @endif
                        </td>
                        <td><span class="display_currency" data-currency_symbol="true">{{ $sell_line->unit_price_inc_tax }}</span></td>
                        <td>{{@format_quantity($sell_line->quantity_returned)}} {{$unit_name}}</td>
                        <td>
                        @php
                            $line_total = $sell_line->unit_price_inc_tax * $sell_line->quantity_returned;
                            $total_before_tax += $line_total ;
                        @endphp
                        <span class="display_currency" data-currency_symbol="true">{{$line_total}}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
        <div class="row">
        {{-- <div class="col-md-12">
            <strong>{{ __('Activities') }}:</strong><br>
            @includeIf('activity_log.activities', ['activity_type' => 'sell'])
        </div> --}}
        </div>
        
                    <div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12 @if ($sell->type == 'sales_order') col-md-offset-6 @endif tw-mt-1">
                        <div class="table-responsive col-md-12 col-md-offset-12 tw-mt-1"  
                        style="border: 1px solid rgb(228, 226, 226); background:{{ $sell->type != 'sales_order' ? 'rgb(252, 230, 225)' : 'rgb(255 252 217)' }}; border-radius: 1px;">
                        <table>
                            <tbody>
                                @if (in_array('types_of_service', $enabled_modules) && !empty($sell->packing_charge))
                                    <tr>
                                        <th class="tw-px-3 tw-py-0"
                                            style="background: {{ $sell->type != 'sales_order' ? '#fce6e1' : '#fce6e1' }};">
                                            {{ __('lang_v1.packing_charge') }}</th>
                                        <td class="tw-px-3 tw-py-0">
                                            <span class="display_currency" @if ($sell->packing_charge_type == 'fixed')
                                            data-currency_symbol="true" @endif>
                                                {{ $sell->packing_charge }}
                                            </span>
                                            @if ($sell->packing_charge_type == 'percent') % @endif
                                        </td>
                                    </tr>
                                @endif

                                @if (session('business.enable_rp') == 1 && !empty($sell->rp_redeemed))
                                    <tr>
                                        <th class="tw-px-3 tw-py-0"
                                            style="background: {{ $sell->type != 'sales_order' ? '#fce6e1' : '#fce6e1' }};">
                                            {{ session('business.rp_name') }}</th>
                                        <td class="tw-px-3 tw-py-0">
                                            <span class="display_currency"
                                                data-currency_symbol="true">{{ $sell->rp_redeemed_amount }}</span>
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <th class="tw-px-3 tw-py-0"
                                        style="background: {{ $sell->type != 'sales_order' ? '#fce6e1' : '#fce6e1' }};">
                                        {{ __('sale.order_tax') }}</th>
                                    <td class="tw-px-3 tw-py-0">
                                        @foreach ($order_taxes ?? [] as $k => $v)
                                            <strong><small>{{ $k }}</small></strong> -
                                            <span class="display_currency" data-currency_symbol="true">{{ $v }}</span><br>
                                        @endforeach
                                        @if (!empty($customTotalTax))
                                            <strong><small>ML Tax</small></strong> -
                                            <span class="display_currency" data-currency_symbol="true"
                                                id="total_tax">{{ $customTotalTax['tax'] }}</span>
                                        @endif
                                    </td>
                                </tr>

                                @if (!empty($line_taxes))
                                    <tr>
                                        <th class="tw-px-3 tw-py-0"
                                            style="background: {{ $sell->type != 'sales_order' ? '#fce6e1' : '#fce6e1' }};">
                                            {{ __('lang_v1.line_taxes') }}</th>
                                        <td class="tw-px-3 tw-py-0">
                                            @foreach ($line_taxes as $k => $v)
                                                <strong><small>{{ $k }}</small></strong> -
                                                <span class="display_currency" data-currency_symbol="true">{{ $v }}</span><br>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <th class="tw-px-3 tw-py-0"
                                        style="background: {{ $sell->type != 'sales_order' ? '#fce6e1' : '#fce6e1' }};">
                                        {{ __('sale.shipping') }}
                                        @if ($sell->shipping_details) ({{ $sell->shipping_details }}) @endif
                                    </th>
                                    <td class="tw-px-3 tw-py-0">
                                        <span class="display_currency" data-currency_symbol="true"
                                            id="td_shipping_charge">{{ $sell->shipping_charges }}</span>
                                    </td>
                                </tr>

                                @for ($i = 1; $i <= 4; $i++)
                                    @php
                                        $key = "additional_expense_key_{$i}";
                                        $value = "additional_expense_value_{$i}";
                                    @endphp
                                    @if (!empty($sell->$value) && !empty($sell->$key))
                                        <tr>
                                            <th class="tw-px-3 tw-py-0"
                                                style="background: {{ $sell->type != 'sales_order' ? '#fce6e1' : '#fce6e1' }};">
                                                {{ $sell->$key }}</th>
                                            <td class="tw-px-3 tw-py-0">
                                                <span class="display_currency">{{ $sell->$value }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                @endfor

                                <tr>
                                    <th class="tw-px-3 tw-py-0"
                                        style="background: {{ $sell->type != 'sales_order' ? '#fce6e1' : '#fce6e1' }};">
                                        {{ __('lang_v1.round_off') }}</th>
                                    <td class="tw-px-3 tw-py-0">
                                        <span class="display_currency"
                                            data-currency_symbol="true">{{ $sell->round_off_amount }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="tw-px-3 tw-py-0"
                                        style="background: {{ $sell->type != 'sales_order' ? '#fce6e1' : '#fce6e1' }};">
                                        {{ __('sale.discount') }}</th>
                                    <td class="tw-px-3 tw-py-0">
                                        <span class="display_currency" @if ($sell->discount_type == 'fixed')
                                        data-currency_symbol="true" @endif>
                                            {{ $sell->discount_amount }}
                                        </span>
                                        @if ($sell->discount_type == 'percentage') % @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th class="tw-px-3 tw-py-0"
                                        style="background: {{ $sell->type != 'sales_order' ? '#fce6e1' : '#fce6e1' }};">
                                        Subtotal Total</th>
                                    <td class="tw-px-3 tw-py-0">
                                        <span class="display_currency" data-currency_symbol="true"
                                            id="total_before_tax">{{ $sell->total_before_tax }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="tw-px-3 tw-py-0"
                                        style="background: {{ $sell->type != 'sales_order' ? '#fce6e1' : '#fce6e1' }};">
                                        Final Total</th>
                                    <td class="tw-px-3 tw-py-0">
                                        <span class="display_currency" data-currency_symbol="true"
                                            id="total_before_tax">{{ $sell->final_total }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>
                  </div>

 {{-- <div class="col-sm-6 col-sm-offset-6 col-xs-6 col-xs-offset-6"> 
      <table class="table">
        <tr>
          <th>@lang('purchase.net_total_amount'): </th>
          <td></td>
          <td><span class="display_currency pull-right" data-currency_symbol="true">{{ $total_before_tax }}</span></td>
        </tr>

        <tr>
          <th>@lang('lang_v1.return_discount'): </th>
          <td><b>(-)</b></td>
          <td class="text-right">@if($sell->return_parent->discount_type == 'percentage')
              @<strong><small>{{$sell->return_parent->discount_amount}}%</small></strong> -
              @endif
          <span class="display_currency pull-right" data-currency_symbol="true">{{ $total_discount }}</span></td>
        </tr>
        
        <tr>
          <th>@lang('lang_v1.total_return_tax'):</th>
          <td><b>(+)</b></td>
          <td class="text-right">
              @if(!empty($sell_taxes))
                @foreach($sell_taxes as $k => $v)
                  <strong><small>{{$k}}</small></strong> - <span class="display_currency pull-right" data-currency_symbol="true">{{ $v }}</span><br>
                @endforeach
              @else
              0.00
              @endif
            </td>
        </tr>
        <tr>
          <th>@lang('lang_v1.return_total'):</th>
          <td></td>
          <td><span class="display_currency pull-right" data-currency_symbol="true" >{{ $sell->return_parent->final_total }}</span></td>
        </tr>
      </table>
    </div> --}}


      {{-- <div class="modal-footer"> --}}
          {{-- <a href="#" class="print-invoice tw-dw-btn tw-dw-btn-primary tw-text-white" data-href="{{action([\App\Http\Controllers\SellReturnController::class, 'printInvoice'], [$sell->return_parent->id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" data-dismiss="modal">@lang( 'messages.close' )</button> --}}
          {{-- </div> --}}
        
            </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    var element = $('div.modal-xl');
    __currency_convert_recursively(element);
  });
</script>