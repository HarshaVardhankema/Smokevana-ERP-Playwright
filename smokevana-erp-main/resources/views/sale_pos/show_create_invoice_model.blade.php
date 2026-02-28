<div class="modal-dialog modal-xl no-print" role="document">
    <div class="modal-content">
        {!! Form::open([
            'url' => action([\App\Http\Controllers\SellController::class, 'saleInvoiceStore'], [$sell->id]),
            'method' => 'post',
            'id' => 'edit_pos_sell_form',
            'class' => 'form-validation',
        ]) !!}
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle"> @lang('sale.sell_details') (<b>
                    @if ($sell->type == 'sales_order')
                        @lang('restaurant.order_no')
                    @else
                        @lang('sale.invoice_no')
                    @endif :
                </b> {{ $sell->invoice_no }})
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-xs-12">
                    <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($sell->transaction_date) }}</p>
                </div>
            </div>
            <div class="row">
                @php
                    $custom_labels = json_decode(session('business.custom_labels'), true);
                    $export_custom_fields = [];
                    if (!empty($sell->is_export) && !empty($sell->export_custom_fields_info)) {
                        $export_custom_fields = $sell->export_custom_fields_info;
                    }
                @endphp
                <div class="@if (!empty($export_custom_fields)) col-sm-3 @else col-sm-4 @endif">
                    <b>
                        @if ($sell->type == 'sales_order')
                            {{ __('restaurant.order_no') }}
                        @else
                            {{ __('sale.invoice_no') }}
                        @endif:
                        <input type="hidden" name="sale_invoice_no" value="{{ $sell->invoice_no }}" readonly />
                        <input type="hidden" name="sale_invoice_no" value="{{ $sell->id }}" readonly />
                    </b> #{{ $sell->invoice_no }}<br>
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

                    @if (!empty($sales_orders))
                        <br><br><strong>@lang('lang_v1.sales_orders'):</strong>
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
                    @endif
                      {{-- Child Orders Section for Parent Orders (Split Orders) --}}
                    @if (isset($isParentOrder) && $isParentOrder && isset($childOrders) && $childOrders->isNotEmpty())
                        <br><br>
                        <div class="alert alert-success" style="padding: 10px; border-radius: 8px; margin-bottom: 10px;">
                            <strong><i class="fas fa-sitemap"></i> Split Order - Parent</strong>
                            <p style="margin: 5px 0 0 0; font-size: 12px;">This is a parent order. Invoice will include items from all child orders below.</p>
                        </div>
                        <table class="table table-slim table-bordered" style="font-size: 12px;">
                            <tr class="bg-light-gray">
                                <th>Child Order</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Items</th>
                            </tr>
                            @foreach ($childOrders as $child)
                                <tr>
                                    <td>{{ $child->invoice_no }}</td>
                                    <td>
                                        @if($child->type === 'erp_sales_order')
                                            <span class="label" style="background: #10b981;">ERP Fulfilled</span>
                                        @elseif($child->type === 'wp_sales_order')
                                            <span class="label" style="background: #8b5cf6;">WC Vendor</span>
                                        @elseif($child->type === 'erp_dropship_order')
                                            <span class="label" style="background: #f59e0b;">ERP Vendor</span>
                                        @else
                                            <span class="label bg-gray">{{ $child->type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($child->status) }}</td>
                                    <td>{{ $child->sell_lines->count() }} item(s)</td>
                                </tr>
                            @endforeach
                        </table>
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
                <div class="@if (!empty($export_custom_fields)) col-sm-3 @else col-sm-4 @endif">
                    @if (!empty($sell->contact->supplier_business_name))
                        {{ $sell->contact->supplier_business_name }}<br>
                    @endif
                    <b>{{ __('sale.customer_name') }}:</b> {{ $sell->contact->name }}<br>
                    <b>{{ __('business.address') }}:</b><br>
                    @if (!empty($sell->billing_address()))
                        {{ $sell->billing_address() }}
                    @else
                        {!! $sell->contact->contact_address !!}
                        @if ($sell->contact->mobile)
                            <br>
                            {{ __('contact.mobile') }}: {{ $sell->contact->mobile }}
                        @endif
                        @if ($sell->contact->alternate_number)
                            <br>
                            {{ __('contact.alternate_contact_number') }}: {{ $sell->contact->alternate_number }}
                        @endif
                        @if ($sell->contact->landline)
                            <br>
                            {{ __('contact.landline') }}: {{ $sell->contact->landline }}
                        @endif
                        @if ($sell->contact->email)
                            <br>
                            {{ __('business.email') }}: {{ $sell->contact->email }}
                        @endif
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

                    <strong>@lang('sale.shipping'):</strong>
                    <span
                        class="label @if (!empty($shipping_status_colors[$sell->shipping_status])) {{ $shipping_status_colors[$sell->shipping_status] }} @else {{ 'bg-gray' }} @endif">{{ $shipping_statuses[$sell->shipping_status] ?? '' }}</span><br>
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
            <br>
            <div class="row">
                {{-- <div class="col-sm-12 col-xs-12">
                    <h4>{{ __('sale.products') }}:</h4>
                </div> --}}
                @php
                    $totalPicked = 0;
                    $totalOrdered = 0;
                    $fulfilledPercentage = 0;
                @endphp

                @foreach ($sell->sell_lines as $sell_line)
                    @php
                        $totalPicked += $sell_line->picked_quantity;
                        $totalOrdered += $sell_line->quantity;
                    @endphp
                @endforeach

                @php
                    // Calculate the fulfilled percentage after the loop
                    if ($totalOrdered > 0) {
                        $fulfilledPercentage = ($totalPicked / $totalOrdered) * 100;
                    } else {
                        $fulfilledPercentage = 0;
                    }
                @endphp

                <style>
                    .progress {
                        background-color: #f3f3f3;
                        border-radius: 20px;
                        height: 30px;
                        width: 100%;
                        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
                    }
                    .progress-bar {
                        height: 100%;
                        border-radius: 20px;
                        transition: width 0.5s ease-in-out, background-color 0.5s ease;
                        text-align: center;
                        line-height: 30px;
                        font-weight: bold;
                        color: #fff;
                    }
                </style>

                <!-- This is the structure inside the modal for each progress bar -->
                <div class="row">
                    <div class="col-xs-2 pl-20">
                        <div class="progress">
                            <div class="progress-bar bg-success" id="progress-bar-{{ $sell->id }}" role="progressbar"
                                style="width: {{ number_format($fulfilledPercentage, 2) }}%;"
                                aria-valuenow="{{ number_format($fulfilledPercentage, 2) }}" aria-valuemin="0"
                                aria-valuemax="100">
                                {{ number_format($fulfilledPercentage, 2) }}% 
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        @include('sale_pos.partials.create_sale_line_details')
                    </div>
                </div>
            </div>
            <div class="row">
                @php
                    $total_paid = 0;
                @endphp
                <div class="col-md-6 col-sm-12 col-xs-12 @if ($sell->type == 'sales_order') col-md-offset-6 @endif">
                    <div class="table-responsive">
                        <table class="table bg-gray">
                            <tr>
                                <th>{{ __('sale.total') }}: </th>
                                <td></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $sell->total_before_tax }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('sale.discount') }}:</th>
                                <td><b>(-)</b></td>
                                <td>
                                    <div class="pull-right"><span class="display_currency"
                                            @if ($sell->discount_type == 'fixed') data-currency_symbol="true" @endif>{{ $sell->discount_amount }}</span>
                                        @if ($sell->discount_type == 'percentage')
                                            {{ '%' }}
                                        @endif
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @if (in_array('types_of_service', $enabled_modules) && !empty($sell->packing_charge))
                                <tr>
                                    <th>{{ __('lang_v1.packing_charge') }}:</th>
                                    <td><b>(+)</b></td>
                                    <td>
                                        <div class="pull-right"><span class="display_currency"
                                                @if ($sell->packing_charge_type == 'fixed') data-currency_symbol="true" @endif>{{ $sell->packing_charge }}</span>
                                            @if ($sell->packing_charge_type == 'percent')
                                                {{ '%' }}
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @if (session('business.enable_rp') == 1 && !empty($sell->rp_redeemed))
                                <tr>
                                    <th>{{ session('business.rp_name') }} {{ __('lang_v1.redeemed') }}:</th>
                                    <td><b>(-)</b></td>
                                    <td> <span class="display_currency pull-right"
                                            data-currency_symbol="true">{{ $sell->rp_redeemed_amount }}</span> ({{ (int) $sell->rp_redeemed }} points)</td>
                                </tr>
                            @endif
                            <tr>
                                <th>{{ __('sale.order_tax') }}:</th>
                                <td><b>(+)</b></td>
                                <td class="text-right oooo">
                                    @if (!empty($order_taxes))
                                        @foreach ($order_taxes as $k => $v)
                                            <strong><small>{{ $k }}</small></strong> - <span
                                                class="display_currency pull-right"
                                                data-currency_symbol="true">{{ $v }}</span><br>
                                        @endforeach
                                    @else
                                        {{-- 0.00 --}}
                                    @endif

                                    {{-- custom tax  --}}
                                    @if (!empty($customTotalTax))
                                        <strong><small> ML Tax </small></strong> - <span
                                            class="display_currency pull-right"
                                            data-currency_symbol="true">{{ $customTotalTax['tax'] }}</span><br>
                                    @endif
                                </td>
                            </tr>
                            @if (!empty($line_taxes))
                                <tr>
                                    <th>{{ __('lang_v1.line_taxes') }}:</th>
                                    <td></td>
                                    <td class="text-right">
                                        @if (!empty($line_taxes))
                                            @foreach ($line_taxes as $k => $v)
                                                <strong><small>{{ $k }}</small></strong> - <span
                                                    class="display_currency pull-right"
                                                    data-currency_symbol="true">{{ $v }}</span><br>
                                            @endforeach
                                        @else
                                            0.00
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <th>{{ __('sale.shipping') }}: @if ($sell->shipping_details)
                                        ({{ $sell->shipping_details }})
                                    @endif
                                </th>
                                <td><b>(+)</b></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $sell->shipping_charges }}</span></td>
                            </tr>

                            @if (!empty($sell->additional_expense_value_1) && !empty($sell->additional_expense_key_1))
                                <tr>
                                    <th>{{ $sell->additional_expense_key_1 }}:</th>
                                    <td><b>(+)</b></td>
                                    <td><span
                                            class="display_currency pull-right">{{ $sell->additional_expense_value_1 }}</span>
                                    </td>
                                </tr>
                            @endif
                            @if (!empty($sell->additional_expense_value_2) && !empty($sell->additional_expense_key_2))
                                <tr>
                                    <th>{{ $sell->additional_expense_key_2 }}:</th>
                                    <td><b>(+)</b></td>
                                    <td><span
                                            class="display_currency pull-right">{{ $sell->additional_expense_value_2 }}</span>
                                    </td>
                                </tr>
                            @endif
                            @if (!empty($sell->additional_expense_value_3) && !empty($sell->additional_expense_key_3))
                                <tr>
                                    <th>{{ $sell->additional_expense_key_3 }}:</th>
                                    <td><b>(+)</b></td>
                                    <td><span
                                            class="display_currency pull-right">{{ $sell->additional_expense_value_3 }}</span>
                                    </td>
                                </tr>
                            @endif
                            @if (!empty($sell->additional_expense_value_4) && !empty($sell->additional_expense_key_4))
                                <tr>
                                    <th>{{ $sell->additional_expense_key_4 }}:</th>
                                    <td><b>(+)</b></td>
                                    <td><span
                                            class="display_currency pull-right">{{ $sell->additional_expense_value_4 }}</span>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <th>{{ __('lang_v1.round_off') }}: </th>
                                <td></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $sell->round_off_amount }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('sale.total_payable') }}: </th>
                                <td></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $sell->final_total }}</span></td>
                            </tr>
                            @if ($sell->type != 'sales_order')
                                <tr>
                                    <th>{{ __('sale.total_paid') }}:</th>
                                    <td></td>
                                    <td><span class="display_currency pull-right"
                                            data-currency_symbol="true">{{ $total_paid }}</span></td>
                                </tr>
                                <tr>
                                    <th>{{ __('sale.total_remaining') }}:</th>
                                    <td></td>
                                    <td>
                                        <!-- Converting total paid to string for floating point substraction issue -->
                                        @php
                                            $total_paid = (string) $total_paid;
                                        @endphp
                                        <span class="display_currency pull-right"
                                            data-currency_symbol="true">{{ $sell->final_total - $total_paid }}</span>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <strong>{{ __('sale.sell_note') }}:</strong><br>
                    <p class="well well-sm no-shadow bg-gray">
                        @if ($sell->additional_notes)
                            {!! nl2br($sell->additional_notes) !!}
                        @else
                            --
                        @endif
                    </p>
                </div>
                <div class="col-sm-6">
                    <strong>{{ __('sale.staff_note') }}:</strong><br>
                    <p class="well well-sm no-shadow bg-gray">
                        @if ($sell->staff_note)
                            {!! nl2br($sell->staff_note) !!}
                        @else
                            --
                        @endif
                    </p>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print">
                <i class="fas fa-file-invoice" aria-hidden="true"></i> Create Invoice
            </button>

            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print"
                data-dismiss="modal">@lang('messages.close')</button>
        </div>
        {{ Form::close() }}
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var element = $('div.modal-xl');
        __currency_convert_recursively(element);
    });
</script>
