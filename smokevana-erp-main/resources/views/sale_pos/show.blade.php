<style>
    .btn-modal-cl {
        padding: 4px 8px;
        font-size: 12px;
        line-height: 1.2;
        border-radius: 4px;
    }


    
</style>
<div class="modal-dialog modal-xl no-print" role="document">
    <div class="hide modal_id" id={{$sell->id}} transaction_type="Transaction"></div>
    <div class="modal-content ">
        @php
            $is_edit_allowed = false;
            if($sell->status != 'void' && $sell->status != 'cancelled'){
                $is_edit_allowed = true;
            }
        @endphp
        <div class="modal-header tw-justify-between" style="display: flex; align-items: center; position: relative;">
            {{-- <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button> --}}
            <div style="flex: 1;">
                <p style="margin: 0; font-weight: bold;">
                    @if ($sell->type == 'sales_order')
                        @lang('restaurant.order_no')
                    @else
                        @lang('sale.invoice_no')
                    @endif: <span style="font-weight: normal;">{{ $sell->invoice_no }}</span>
                </p>
                <p style="margin: 5px 0 0 0;">@lang('messages.date'): {{ @format_date($sell->transaction_date) }}</p>
            </div>
            <div style="flex: 1; text-align: left;">
                <h4 class="modal-title" id="modalTitle" style="margin: 0; font-weight: bold; font-size: 24px;">@lang('sale.sell_details')</h4>
            </div>
            <div class="tw-flex tw-justify-end tw-gap-2" style="flex: 1;">
                <a href="#" class="tw-dw-btn tw-dw-btn-primary tw-text-white product_history" tabindex="2">
                    📜Item History
                </a>
                @if ($sell->type != 'sales_order')
                    <a href="#" class="print-invoice tw-dw-btn tw-dw-btn-success tw-text-white"
                        data-href="{{ route('sell.printInvoice', [$sell->id]) }}?package_slip=true">
                        <i class="fas fa-file-alt" aria-hidden="true"></i> @lang('lang_v1.packing_slip')
                    </a>
                @endif
                @if ($sell->type != 'sales_order')
                    <a  href="#" class="tw-dw-btn tw-dw-btn-primary tw-text-white sendNotification"
                        data-href="{{ route('notification.template', [$sell->id,'template_for' => 'new_sale']) }}">
                      <i class="fa fa-envelope" aria-hidden="true"></i> Send Notification
                    </a>
                @endif
                @can('print_invoice')
                    <a href="#" class="print-invoice tw-dw-btn tw-dw-btn-gray tw-text-white"
                        data-href="{{ route('sell.printInvoice', [$sell->id]) }}">
                        <i class="fa fa-print" aria-hidden="true"></i> @lang('lang_v1.print_invoice')
                    </a>
                    <a href="#" class="print-preview tw-dw-btn tw-dw-btn-info tw-text-white"
                        data-href="{{ route('sell.printInvoice', [$sell->id]) }}">
                        <i class="fas fa-eye" aria-hidden="true"></i> @lang('lang_v1.print_preview')
                    </a>
                @endcan
                @if (auth()->user()->can('so.update') && auth()->user()->can('direct_sell.update') && $is_edit_allowed)
                    <button id="save_button_invoice"
                        class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print @if ($sell->payment_status == 'due' && $sell->type != 'sales_order' && !$isLockModal || $sell->type == 'sales_order' && $sell->status == 'ordered' && !$isLockModal && $sell->picking_status != 'PICKING' && $customer_status != 'inactive') @else hide @endif">
                        Save
                    </button>
                @endif
                
                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" style="margin-left: 25px;" data-dismiss="modal"
                    id='close_button'>@lang('messages.close')</button>
            </div>

        </div>
        <div class="modal-body">
            @if (!empty($pos_settings['allow_overselling']))
                <input type="hidden" id="is_overselling_allowed">
            @endif
            {{-- <div class="row">
                <div class="col-xs-12">

                </div>
            </div> --}}
            {!! Form::hidden('location_id', $sell->location_id, ['id' => 'location_id']) !!}
            <div class="row">
                @php
                    $custom_labels = json_decode(session('business.custom_labels'), true);
                    $export_custom_fields = [];
                    if (!empty($sell->is_export) && !empty($sell->export_custom_fields_info)) {
                        $export_custom_fields = $sell->export_custom_fields_info;
                    }
                @endphp
                <div class="@if (!empty($export_custom_fields)) col-sm-2 @else col-sm-2 @endif">
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
                    @if ($sell->type == 'sales_order')
                        <b>Order Status:</b>
                        @php
                            $picking_status = $sell->picking_status ? strtoupper(trim($sell->picking_status)) : null;
                            $isVerified = $sell->isVerified ?? false;
                            
                            $status_label = '';
                            $status_class = 'bg-gray';
                            
                            // Check if order is voided or cancelled first
                            if ($sell->status == 'void' || $sell->status == 'cancelled') {
                                $status_label = 'Cancelled';
                                $status_class = 'bg-red';
                            } elseif (empty($picking_status) || $picking_status == 'NULL') {
                                $status_label = 'Pending';
                                $status_class = 'bg-gray';
                            } elseif ($picking_status == 'PICKING') {
                                $status_label = 'Picking';
                                $status_class = 'bg-blue';
                            } elseif ($picking_status == 'PICKED' && !$isVerified) {
                                $status_label = 'Verifying';
                                $status_class = 'bg-yellow';
                            } elseif ($picking_status == 'PICKED' && $isVerified) {
                                $status_label = 'Packing';
                                $status_class = 'bg-orange';
                            } elseif ($picking_status == 'PACKED' || $picking_status == 'INVOICED') {
                                $status_label = 'Completed';
                                $status_class = 'bg-green';
                            } else {
                                $status_label = ucfirst(strtolower($picking_status));
                                $status_class = 'bg-info';
                            }
                        @endphp
                        <span class="label {{ $status_class }}">{{ $status_label }}</span>
                        <br>
                    @endif
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
                    @if ($sell->document_path)
                        <br>
                        <br>
                        <a href="{{ $sell->document_path }}" download="{{ $sell->document_name }}"
                            class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-accent pull-left no-print">
                            <i class="fa fa-download"></i>
                            &nbsp;{{ __('purchase.download_document') }}
                        </a>
                    @endif
                </div>

                @if (!empty($sales_orders))
                    <div class="@if (!empty($export_custom_fields)) col-sm-2 @else col-sm-2 @endif tw-w-full">

                        <strong>@lang('lang_v1.sales_orders'):</strong>
                        <table class="table table-slim no-border">
                            <tr>
                                <th>@lang('lang_v1.sales_order')</th>
                                <th>@lang('lang_v1.date')</th>
                            </tr>
                            @foreach ($sales_orders as $so)
                                <tr>
                                    <td>{{ $so->invoice_no }}</td>
                                    <td>{{ @format_datetime($so->transaction_date) }}</td>
                                </tr>
                            @endforeach
                        </table>


                    </div>
                @endif


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
                        @if (!empty($sell->billing_phone))
                        <b>{{ __('contact.mobile') }}</b> : {{ $sell->billing_phone}}
                            <br>
                        @elseif ($sell->contact->mobile)
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
                        @if ($sell->billing_email)
                            <b>{{ __(key: 'business.email') }}</b> : {{ $sell->billing_email }}
                        @elseif ($sell->contact->email)
                            <b>{{ __(key: 'business.email') }}</b> : {{ $sell->contact->email }}
                        @endif
                    @endif

                </div>

                <div class="@if (!empty($export_custom_fields)) col-sm-2 @else col-sm-2 @endif tw-w-full">
                    <b>Billing Address:</b>
                    <br>
                    @php
                        // Check direct billing address columns first
                        $hasDirectBilling = !empty($sell->billing_first_name) || !empty($sell->billing_address1) || !empty($sell->billing_company);
                        // If no direct columns, check order_addresses JSON
                        $billing_address = $hasDirectBilling ? null : $sell->billing_address(true);
                    @endphp
                    @if ($hasDirectBilling)
                        @if (!empty($sell->billing_company))
                            {{ $sell->billing_company }}<br>
                        @endif
                        @if (!empty($sell->billing_first_name) || !empty($sell->billing_last_name))
                            {{ trim(($sell->billing_first_name ?? '') . ' ' . ($sell->billing_last_name ?? '')) }}<br>
                        @endif
                        @if (!empty($sell->billing_address1))
                            {{ $sell->billing_address1 }}<br>
                        @endif
                        @if (!empty($sell->billing_address2))
                            {{ $sell->billing_address2 }}<br>
                        @endif
                        @if (!empty($sell->billing_city))
                            {{ $sell->billing_city }},
                        @endif
                        @if (!empty($sell->billing_state))
                            {{ $sell->billing_state }}
                        @endif
                        @if (!empty($sell->billing_zip))
                            {{ $sell->billing_zip }}<br>
                        @endif
                        @if (!empty($sell->billing_country))
                            {{ $sell->billing_country }}
                        @endif
                    @elseif (!empty($billing_address) && count($billing_address) > 0)
                        @if (!empty($billing_address['company']))
                            {{ $billing_address['company'] }}<br>
                        @endif
                        @if (!empty($billing_address['name']))
                            {{ $billing_address['name'] }}<br>
                        @endif
                        @if (!empty($billing_address['address_line_1']))
                            {{ $billing_address['address_line_1'] }}<br>
                        @endif
                        @if (!empty($billing_address['address_line_2']))
                            {{ $billing_address['address_line_2'] }}<br>
                        @endif
                        @if (!empty($billing_address['city']))
                            {{ $billing_address['city'] }},
                        @endif
                        @if (!empty($billing_address['state']))
                            {{ $billing_address['state'] }}
                        @endif
                        @if (!empty($billing_address['zipcode']))
                            {{ $billing_address['zipcode'] }}<br>
                        @endif
                        @if (!empty($billing_address['country']))
                            {{ $billing_address['country'] }}
                        @endif
                    @else
                        {!! $sell->contact->contact_address !!}
                    @endif
                </div>

                <div class="@if (!empty($export_custom_fields)) col-sm-2 @else col-sm-2 @endif">
                    @if (in_array('tables', $enabled_modules))
                        <strong>@lang('restaurant.table'):</strong>
                        {{ $sell->table->name ?? '' }}<br>
                    @endif
                    @if (in_array('service_staff', $enabled_modules))
                        <strong>@lang('restaurant.service_staff'):</strong>
                        {{ $sell->service_staff->user_full_name ?? '' }}<br>
                    @endif

                    <strong>Shipping Address :</strong> 
                    @if ($sell->type == 'sales_order')
                    <span class="tw-cursor-pointer"><i class="fa fa-edit edit-icon" id="edit_shipping_address" data-href="{{ $sell->id }}"></i></span>
                    @endif
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
                <input type="hidden" name="customer_state" id="customer_state" value="{{ $sell->shipping_state??'IL' }}" >

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
                <div class="col-sm-1">
                    <div class="tw-flex tw-justify-between" style="width: 90%;">
                    @if ($sell->type == 'sales_order')
                        @if ($sell->status == 'completed')
                            <img src='/img/completed.svg' alt="" style="width: 250px !important; height: 120px !important; max-width: 120px !important; max-height: 250px !important; object-fit: contain;">
                        @endif
                        @if ($sell->status == 'cancelled')
                            <img src='/img/cancelled.svg' alt="" style="width: 250px !important; height: 120px !important; max-width: 120px !important; max-height: 250px !important; object-fit: contain;">
                        @endif
                        @if ($sell->status == 'ordered')
                            <img src='/img/ordered.svg' alt="" style="width: 250px !important; height: 120px !important; max-width: 120px !important; max-height: 250px !important; object-fit: contain;">
                        @endif
                        @if ($sell->status == 'partial')
                            <img src='/img/partial.svg' alt="" style="width: 250px !important; height: 120px !important; max-width: 120px !important; max-height: 250px !important; object-fit: contain;">
                        @endif
                    @else
                        @if ($sell->payment_status == 'paid')
                            <img src='/img/Paid.svg' alt="" style="width: 250px !important; height: 120px !important; max-width: 120px !important; max-height: 250px !important; object-fit: contain;">
                        @endif
                        @if ($sell->payment_status == 'due')
                        <img src='/img/due.svg' alt="" style="width: 250px !important; height: 120px !important; max-width: 120px !important; max-height: 250px !important; object-fit: contain;">

                        @endif
                        @if ($sell->payment_status == 'partial')
                            <img src='/img/partial.svg' alt="">
                        @endif
                    @endif
                    </div>
                </div>
                @if (!empty($export_custom_fields))
                    <div class="col-sm-3">
                        @foreach ($export_custom_fields as $label => $value)
                            <strong>
                                @php
                                    $export_label = __('lang_v1.export_custom_field1');
                                    if ($label == 'export_custom_field_1') {
                                        $export_label = __('lang_v1.export_custom_field1');
                                    } elseif ($label == 'export_custom_field_2') {
                                        $export_label = __('lang_v1.export_custom_field2');
                                    } elseif ($label == 'export_custom_field_3') {
                                        $export_label = __('lang_v1.export_custom_field3');
                                    } elseif ($label == 'export_custom_field_4') {
                                        $export_label = __('lang_v1.export_custom_field4');
                                    } elseif ($label == 'export_custom_field_5') {
                                        $export_label = __('lang_v1.export_custom_field5');
                                    } elseif ($label == 'export_custom_field_6') {
                                        $export_label = __('lang_v1.export_custom_field6');
                                    }
                                @endphp

                                {{ $export_label }}
                                :
                            </strong> {{ $value ?? '' }} <br>
                        @endforeach
                    </div>
                @endif

            </div>
            <div class="row">
                <div class="col-sm-12 col-xs-12 tw-flex tw-justify-between">
                    <h4>{{ __('sale.products') }}:</h4>
                    <div class="tw-relative tw-inline-block tw-mb-1 tw-gap-5">
                        @if ($sell->payment_status == 'due' || $sell->type == 'sales_order')
                            <button id="openLock" data-href={{ $sell->id }}
                                class="btn-modal-cl @if(!$isLockModal) hide @endif" @if (!$isLockModal) disabled @endif>
                                @if ($isLockModal)
                                    🔒
                                @else
                                    🔓
                                @endif

                            </button>
                        @endif
                        
                        @if ($sell->payment_status == 'due' || $sell->payment_status == 'partial')
                        <button type="button" onclick="var searchFoot = document.getElementById('search_foot'); if(searchFoot) { searchFoot.classList.remove('hide'); } var searchInput = document.getElementById('search_product'); if(searchInput) { setTimeout(function() { searchInput.focus(); searchInput.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 100); }"
                                class="btn-modal-cl btn-add-product" style="white-space: nowrap; min-width: 120px;">
                            ➕ Add Product
                        </button>
                        @endif

                        <button id="openSellsNoteModal" data-href={{ $sell->id }} class="btn-modal-cl">
                            📝 Sales Note
                        </button>


                        <button id="openActivityModal" data-href={{ $sell->id }} class="btn-modal-cl">
                            ⚽ Activity
                        </button>

                        <div class="tw-relative tw-inline-block tw-text-left">
                            <button onclick="toggleMenu('table1')" id="toggleDropdown" class="btn-modal-cl">
                                ☰ Quickbooks
                            </button>
                            <div id="columnMenu-table1"
                                style="position: absolute; right: 110%; top: -150px; width: 200px; background-color: white; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); border-radius: 6px; display: none; z-index: 10; padding: 10px;">
                                <div id="dynamic-options" style="display: flex; flex-direction: column; gap: 5px;">
                                    <!-- Options will be dynamically added here -->
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        @include('sale_pos.partials.sale_line_details')
                    </div>
                </div>
            </div>
            <div class="row">
                @php
                    $total_paid = 0;
                @endphp
                @foreach ($sell->payment_lines as $payment_line)
                    @php
                        if ($payment_line->is_return == 1) {
                            $total_paid -= $payment_line->amount;
                        } else {
                            $total_paid += $payment_line->amount;
                        }
                    @endphp
                @endforeach


                @if ($sell->type != 'sales_order')
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="tw-flex tw-justify-between">
                            <h4>{{ __('sale.payment_info') }}:</h4>
                            {{-- <div class="tw-flex">
                                <h4>{{ __('sale.total_payable') }}: </h4>

                                <h4 class="display_currency pull-right" data-currency_symbol="true" id="final_total_p">
                                    {{ $sell->final_total }}
                                </h4>
                            </div> --}}
                            <div class="tw-flex">
                                <h4>{{ __('sale.total_paid') }}:</h4>
                                <h4 class="display_currency pull-right" data-currency_symbol="true" id="total_paid_value"
                                    data-value={{$total_paid}}>
                                    {{ $total_paid }}</h4>
                            </div>

                            <div class="tw-flex">
                                <h4>{{ __('sale.total_remaining') }}:</h4>
                                @php
                                    $total_paid = (string) $total_paid;
                                @endphp
                                <h4 class="display_currency pull-right" data-currency_symbol="true" id="final_total">
                                    {{ $sell->final_total - $total_paid}}
                                </h4>
                            </div>

                        </div>
                        <div class="table-responsive" style="max-height: 120px; overflow-y: auto;">
                            <table class="table table-striped"
                                style="background:@if($sell->type != 'sales_order') rgb(239 254 238) @else rgb(255 252 217)@endif; margin-bottom: 0;">
                                <thead style="position: sticky; top: 0;z-index: 1;">
                                    <tr>
                                        <th style="background: inherit;">#</th>
                                        <th style="background: inherit;">{{ __('messages.date') }}</th>
                                        <th style="background: inherit;">{{ __('purchase.ref_no') }}</th>
                                        <th style="background: inherit;">{{ __('sale.amount') }}</th>
                                        <th style="background: inherit;">{{ __('sale.payment_mode') }}</th>
                                        <th style="background: inherit;">{{ __('sale.payment_note') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sell->payment_lines as $payment_line)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ @format_date($payment_line->paid_on) }}</td>
                                            <td>{{ $payment_line->payment_ref_no }}</td>
                                            <td><span class="display_currency"
                                                    data-currency_symbol="true">{{ $payment_line->amount }}</span></td>
                                            <td>
                                                {{ $payment_types[$payment_line->method] ?? $payment_line->method }}
                                                @if ($payment_line->is_return == 1)
                                                    <br />
                                                    ({{ __('lang_v1.change_return') }})
                                                @endif
                                            </td>
                                            <td>
                                                @if ($payment_line->note)
                                                    {{ ucfirst($payment_line->note) }}
                                                @else
                                                    --
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                <div
                    class="col-md-6 col-sm-12 col-xs-12 @if ($sell->type == 'sales_order') col-md-offset-6 @endif tw-mt-1">
                    <div class="table-responsive"
                        style="border: 1px solid rgb(228, 226, 226); background:{{ $sell->type != 'sales_order' ? 'rgb(239 254 238)' : 'rgb(255 252 217)' }}; border-radius: 5px;">
                        <table>
                            <tbody>
                                @if (in_array('types_of_service', $enabled_modules) && !empty($sell->packing_charge))
                                    <tr>
                                        <th class="tw-px-3 tw-py-0"
                                            style="background: {{ $sell->type != 'sales_order' ? '#e1f1df' : '#f8f4c2' }};">
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

                                @if (session('business.enable_rp') == 1)
                                    <tr>
                                        <th class="tw-px-3 tw-py-0"
                                            style="background: {{ $sell->type != 'sales_order' ? '#e1f1df' : '#f8f4c2' }};">
                                            {{ (session('business.rp_name') ?? __('lang_v1.reward_points')) }} {{ __('lang_v1.redeemed') }}</th>
                                        <td class="tw-px-3 tw-py-0">
                                            <span class="display_currency" data-currency_symbol="true">{{ $sell->rp_redeemed_amount ?? 0 }}</span>
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <th class="tw-px-3 tw-py-0"
                                        style="background: {{ $sell->type != 'sales_order' ? '#e1f1df' : '#f8f4c2' }};">
                                        {{ __('sale.order_tax') }}</th>
                                    <td class="tw-px-3 tw-py-0">
                                        @foreach ($order_taxes ?? [] as $k => $v)
                                            <strong><small>{{ $k }}</small></strong> -
                                            <span class="display_currency" data-currency_symbol="true">{{ $v }}</span><br>
                                        @endforeach
                                        @if (!empty($customTotalTax))
                                            {{-- <strong><small>ML Tax</small></strong> - --}}
                                            <span class="display_currency" data-currency_symbol="true"
                                                id="total_tax">{{ $customTotalTax['tax'] }}</span>
                                        @endif
                                    </td>
                                </tr>

                                @if (!empty($line_taxes))
                                    <tr>
                                        <th class="tw-px-3 tw-py-0"
                                            style="background: {{ $sell->type != 'sales_order' ? '#e1f1df' : '#f8f4c2' }};">
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
                                        style="background: {{ $sell->type != 'sales_order' ? '#e1f1df' : '#f8f4c2' }};">
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
                                                style="background: {{ $sell->type != 'sales_order' ? '#e1f1df' : '#f8f4c2' }};">
                                                {{ $sell->$key }}</th>
                                            <td class="tw-px-3 tw-py-0">
                                                <span class="display_currency">{{ $sell->$value }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                @endfor

                                <tr>
                                    <th class="tw-px-3 tw-py-0"
                                        style="background: {{ $sell->type != 'sales_order' ? '#e1f1df' : '#f8f4c2' }};">
                                        {{ __('lang_v1.round_off') }}</th>
                                    <td class="tw-px-3 tw-py-0">
                                        <span class="display_currency"
                                            data-currency_symbol="true">{{ $sell->round_off_amount }}</span>
                                    </td>
                                </tr>

                                <tr class="">
                                    <th class="tw-px-3 tw-py-0"
                                        style="background: {{ $sell->type != 'sales_order' ? '#e1f1df' : '#f8f4c2' }};">
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
                                        style="background: {{ $sell->type != 'sales_order' ? '#e1f1df' : '#f8f4c2' }};">
                                        Subtotal</th>
                                    <td class="tw-px-3 tw-py-0">
                                        <span class="display_currency" data-currency_symbol="true"
                                            id="total_before_tax">{{ $sell->total_before_tax }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="tw-px-3 tw-py-0"
                                        style="background: {{ $sell->type != 'sales_order' ? '#e1f1df' : '#f8f4c2' }};">
                                        Final Total</th>
                                    <td class="tw-px-3 tw-py-0">
                                        <span class="display_currency" data-currency_symbol="true"
                                            id="main_final_total">{{ $sell->final_total }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id='payloads_for_api' class="hide">
    <div class="location_id_data">{{$sell->location_id}}</div>
    <div class="contact_id_data">{{$sell->contact_id}}</div>
    <div class="deliverd_to_data">{{$sell->delivered_to}}</div>
    <div class="delivery_person_data">{{$sell->delivery_person}}</div>
    <div class="discount_amount_data">{{$sell->discount_amount}}</div>
    <div class="discount_type_data">{{$sell->discount_type}}</div>
    <div class="final_total_data" id="final_total_p">{{$sell->final_total}}</div>
    <div class="invoice_no_data">{{$sell->invoice_no}}</div>
    <div class="is_direct_sale_data">{{$sell->is_direct_sale}}</div>
    <div class="pay_term_number_data">{{$sell->pay_term_number}}</div>
    <div class="pay_term_type_data">{{$sell->pay_term_type}}</div>
    <div class="rp_redeemed_data">{{$sell->rp_redeemed}}</div>
    <div class="rp_redeemed_amount_no_data">{{$sell->rp_redeemed_amount}}</div>
    <div class="sale_note_data">{{$sell->additional_notes}}</div>
    <div class="sell_price_tax_data">{{ $business_details->sell_price_tax }}</div>
    <div class="shipping_address_data">{{$sell->shipping_address}}</div>
    <div class="shipping_charges_data">{{$sell->shipping_charges}}</div>
    <div class="shipping_details_data">{{$sell->shipping_details}}</div>
    <div class="shipping_status_data">{{$sell->shipping_status}}</div>
    <div class="status_data">{{$sell->status}}</div>
    <div class="tax_calculation_amount_data">0</div>
    <div class="tax_rate_id_data">{{$business_details->default_sales_tax}}</div>
    <div class="transaction_date_data">{{@format_datetime($sell->transaction_date)}}</div>

    <div class="update_url_data">
        {{action([\App\Http\Controllers\SellPosController::class, 'update'], ['po' => $sell->id])}}</div>
    <div class="paymet_row">
    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <input type="text" class="hide picking_status" value={{$sell->picking_status}}>
</div>

<script>
    $(document).ready(function () {
        let sellLines = @json($sell->sell_lines);
        let maxLength = 0;
        let maxGroupPrices = [];

        // Find max group prices
        sellLines.forEach(sellLine => {
            let groupPricesLength = sellLine.variations.group_prices.length;
            if (groupPricesLength > maxLength) {
                maxLength = groupPricesLength;
                maxGroupPrices = sellLine.variations.group_prices;
            }
        });

        // Populate the dropdown dynamically
        let optionsContainer = $("#dynamic-options");
        optionsContainer.empty(); // Clear existing options

        maxGroupPrices.forEach(groupPrice => {
            let priceName = groupPrice.group_info.name.replace("SellingPrice", "").toLowerCase();
            let label = `
            <label style="display: flex; align-items: center;">
                <input type="radio" name="column-toggle" class="column-toggle"
                    data-table="table1" value="${priceName}" style="margin-right: 5px;">
                ${groupPrice.group_info.name}
            </label>
        `;
            optionsContainer.append(label);
        });

        // Add 'None' option
        optionsContainer.append(`
        <label style="display: flex; align-items: center;">
            <input type="radio" name="column-toggle" class="column-toggle"
                data-table="table1" value="no" style="margin-right: 5px;" checked>
            None
        </label>
    `);

        // Hide all columns initially
        $(".column-toggle").each(function () {
            let columnClass = "." + $(this).val() + "-price";
            $(columnClass).hide();
        });

        $(".silver-price, .gold-price, .platinum-price, .lowest-price, .diamond-price, .no-price").hide();

        // Toggle visibility when a radio button is selected
        $(".column-toggle").on("change", function () {
            let selectedValue = $(this).val();

            // Hide all prices first
            $(".silver-price, .gold-price, .platinum-price, .lowest-price, .diamond-price, .no-price")
                .hide();

            // Show only the selected price column
            $("." + selectedValue + "-price").show();
        });

        var element = $('div.modal-xl');
        __currency_convert_recursively(element);

        // Hide search field after product is added
        function hideSearchField() {
            var searchFoot = document.getElementById('search_foot');
            if(searchFoot) {
                searchFoot.classList.add('hide');
            }
        }

        // Watch for new rows added to the table
        var tableObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    setTimeout(hideSearchField, 200);
                }
            });
        });

        var tableBody = document.querySelector('#sellsModalTable tbody');
        if (tableBody) {
            tableObserver.observe(tableBody, { childList: true, subtree: true });
        }

        // Hide when autocomplete selects a product
        $('#search_product').on('autocompleteselect', function() {
            setTimeout(hideSearchField, 500);
        });
    });

</script>
<script src="{{ asset('js/invoice.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/session_lock.js?v=' . $asset_v) }}"></script>