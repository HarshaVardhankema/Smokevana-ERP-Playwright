@extends('layouts.app')
    @php
        $title = $transaction->type == 'sales_order' ? __('lang_v1.edit_sales_order') : __('sale.edit_sale');
    @endphp
    @section('title', $title)

    @section('css')
    <style>
        /* Amazon Theme for Edit Sales Order Page */
        .amazon-sell-edit-page {
            background: linear-gradient(135deg, #f3f4f6 0%, #eaeded 35%, #f9fafb 100%);
            padding: 24px 28px 40px;
            min-height: 100vh;
        }
        @media (max-width: 768px) {
            .amazon-sell-edit-page {
                padding: 16px 12px 30px;
            }
        }

        /* Header Banner */
        .amazon-sell-header-banner {
            background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
            border-radius: 8px;
            padding: 22px 28px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: 0 4px 16px rgba(15, 17, 17, 0.25);
        }
        .amazon-sell-header-content h1 {
            font-size: clamp(20px, 2.5vw, 28px);
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .amazon-sell-header-content h1 i {
            color: #ff9900;
            font-size: 24px;
        }
        .amazon-sell-header-content small {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }
        .amazon-sell-header-content small .text-success {
            color: #ff9900 !important;
            font-weight: 600;
        }
        .amazon-sell-header-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .amazon-sell-header-actions .tw-dw-btn-primary {
            background: #ff9900;
            border-color: #ff9900;
            color: #fff;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 6px;
            transition: all 0.2s;
            box-shadow: 0 2px 6px rgba(255, 153, 0, 0.3);
        }
        .amazon-sell-header-actions .tw-dw-btn-primary:hover {
            background: #ffac33;
            border-color: #ffac33;
            box-shadow: 0 4px 12px rgba(255, 153, 0, 0.4);
            transform: translateY(-1px);
        }
        .amazon-sell-header-actions .tw-dw-btn-success {
            background: #067d62;
            border-color: #067d62;
            color: #fff;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 6px;
            transition: all 0.2s;
            box-shadow: 0 2px 6px rgba(6, 125, 98, 0.3);
        }
        .amazon-sell-header-actions .tw-dw-btn-success:hover {
            background: #0a9d7a;
            border-color: #0a9d7a;
            box-shadow: 0 4px 12px rgba(6, 125, 98, 0.4);
            transform: translateY(-1px);
        }

        /* Amazon Cards */
        .amazon-sell-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(15, 17, 17, 0.16);
            border: 1px solid #d5d9d9;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .amazon-sell-card .box-header {
            background: linear-gradient(135deg, #37475a 0%, #485769 100%);
            color: #fff;
            padding: 14px 20px;
            margin: 0;
            border-bottom: 2px solid #232f3e;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .amazon-sell-card .box-header .box-title {
            font-size: 15px;
            font-weight: 600;
            color: #ffffff;
            margin: 0;
        }
        .amazon-sell-card .box-body {
            padding: 20px 24px;
        }

        /* Form Elements */
        .amazon-sell-card .form-group {
            margin-bottom: 18px;
        }
        .amazon-sell-card label {
            font-weight: 600;
            font-size: 13px;
            color: #0f1111;
            margin-bottom: 6px;
            display: block;
        }
        .amazon-sell-card .form-control {
            border: 1px solid #d5d9d9;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
            transition: all 0.2s;
        }
        .amazon-sell-card .form-control:focus {
            border-color: #ff9900;
            outline: 0;
            box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.15);
        }
        .amazon-sell-card .input-group-addon {
            background: #f5f5f5;
            border-color: #d5d9d9;
            color: #565959;
        }

        /* Address Sections */
        .amazon-address-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .amazon-address-section strong {
            color: #232f3e;
            font-size: 13px;
            display: block;
            margin-bottom: 8px;
        }
        .amazon-address-section div {
            color: #565959;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Product Table */
        .amazon-product-table-wrapper {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(15, 17, 17, 0.1);
        }
        .amazon-product-table-wrapper table thead {
            background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
        }
        .amazon-product-table-wrapper table thead th {
            color: #fff;
            font-weight: 600;
            font-size: 13px;
            padding: 12px 10px;
            border-color: #485769;
        }
        .amazon-product-table-wrapper table tbody td {
            padding: 10px;
            border-color: #e2e8f0;
            font-size: 14px;
        }
        .amazon-product-table-wrapper table tbody tr:hover {
            background: #f8fafc;
        }

        /* Table Footer */
        #table_footer {
            background: #fff;
            border-top: 2px solid #ff9900;
            padding: 16px 20px;
            border-radius: 0 0 8px 8px;
        }
        #table_footer .form-group {
            margin-bottom: 0;
        }
        #table_footer .tw-text-right {
            font-size: 16px;
            font-weight: 600;
            color: #0f1111;
        }
        #table_footer .total_quantity,
        #table_footer .price_total {
            color: #ff9900;
            font-weight: 700;
        }

        /* Buttons */
        .amazon-sell-card .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .amazon-sell-card .btn-primary {
            background: #ff9900;
            border-color: #ff9900;
        }
        .amazon-sell-card .btn-primary:hover {
            background: #ffac33;
            border-color: #ffac33;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .amazon-sell-header-banner {
                flex-direction: column;
                align-items: flex-start;
            }
            .amazon-sell-header-actions {
                width: 100%;
            }
            .amazon-sell-header-actions .tw-dw-btn {
                flex: 1;
            }
        }

        /* Discount input + $ / % toggle */
        .discount-input-wrap {
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
            width: 140px !important;
            margin: 0 auto !important;
        }
        .discount-input-wrap .discount-amt {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            height: 30px !important;
            padding: 2px 6px !important;
            font-size: 12px !important;
            border: 1px solid #D5D9D9 !important;
            border-radius: 4px !important;
            box-sizing: border-box !important;
        }
        .discount-input-wrap .discount-type-sel {
            flex: 0 0 44px !important;
            width: 44px !important;
            min-width: 44px !important;
            max-width: 44px !important;
            height: 30px !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            line-height: 30px !important;
            text-align: center !important;
            text-align-last: center !important;
            color: #0F1111 !important;
            background: #F0F2F2 !important;
            border: 1px solid #D5D9D9 !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            box-shadow: none !important;
            outline: none !important;
            overflow: visible !important;
            box-sizing: border-box !important;
        }
        .discount-input-wrap .discount-type-sel:hover {
            border-color: #FF9900 !important;
            background: #FFF8E7 !important;
        }
        .discount-input-wrap .discount-type-sel:focus {
            border-color: #FF9900 !important;
            box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.25) !important;
        }
        .discount-input-wrap .discount-type-sel option {
            font-size: 15px;
            font-weight: 700;
            text-align: center;
            padding: 6px 10px;
        }
    </style>
    @endsection

    @section('content')
        <div class="amazon-sell-edit-page">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="amazon-sell-header-banner">
                <div class="amazon-sell-header-content">
                    <h1>
                        <i class="fas fa-edit"></i>
                        {{ $title }}
                        <small>
                            (@if ($transaction->type == 'sales_order')
                                @lang('restaurant.order_no')
                            @else
                                @lang('sale.invoice_no')
                            @endif: <span class="text-success">#{{ $transaction->invoice_no }})</span>
                        </small>
                    </h1>
                </div>
                <div class="amazon-sell-header-actions">
                    <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="submit-sell">
                        <i class="fas fa-save"></i> @lang('messages.update')
                    </button>
                    <button type="button" id="save-and-print" class="tw-dw-btn tw-dw-btn-success tw-text-white">
                        <i class="fas fa-print"></i> @lang('lang_v1.update_and_print')
                    </button>
                </div>
            </div>
        </section>
        <!-- Main content -->
        <section class="content">
            @php
            $user_firstname = session()->get('user.first_name');
            $user_lastname = session()->get('user.last_name');
            // $user_id=session()->get('user.id');
        @endphp
            <input type="hidden" id="amount_rounding_method" value="{{ $pos_settings['amount_rounding_method'] ?? '' }}">
            <input type="hidden" id="amount_rounding_method" value="{{ $pos_settings['amount_rounding_method'] ?? 'none' }}">
            @if (!empty($pos_settings['allow_overselling']))
                <input type="hidden" id="is_overselling_allowed">
            @endif
            @if (session('business.enable_rp') == 1)
                <input type="hidden" id="reward_point_enabled">
            @endif
            @php
                $custom_labels = json_decode(session('business.custom_labels'), true);
                $common_settings = session()->get('business.common_settings');
            @endphp
            <input type="hidden" id="item_addition_method" value="{{ $business_details->item_addition_method }}">
            {!! Form::open([
                'url' => action([\App\Http\Controllers\SellPosController::class, 'update'], ['po' => $transaction->id]),
                'method' => 'put',
                'id' => 'edit_sell_form',
                'files' => true,
            ]) !!}
            {!! Form::hidden('is_save_and_print', 0, ['id' => 'is_save_and_print']) !!}


            {!! Form::hidden('location_id', $transaction->location_id, [
                'id' => 'location_id',
                'data-receipt_printer_type' => !empty($location_printer_type) ? $location_printer_type : 'browser',
                'data-default_payment_accounts' => $transaction->location->default_payment_accounts,
            ]) !!}

            @if ($transaction->type == 'sales_order')
                <input type="hidden" id="sale_type" value="{{ $transaction->type }}">
            @endif
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    @component('components.widget', ['class' => 'box-solid amazon-sell-card', 'style' => 'z-index:999; position:relative;'])
                        @if (!empty($transaction->selling_price_group_id))
                            <div class="col-md-4 col-sm-6 hide">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fas fa-money-bill-alt"></i>
                                        </span>
                                        {!! Form::hidden('price_group', $transaction->selling_price_group_id, ['id' => 'price_group']) !!}
                                        {!! Form::text('price_group_text', $transaction->price_group->name, ['class' => 'form-control', 'readonly']) !!}
                                        <span class="input-group-addon">
                                            @show_tooltip(__('lang_v1.price_group_help_text'))
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (in_array('types_of_service', $enabled_modules) && !empty($transaction->types_of_service))
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fas fa-external-link-square-alt text-primary service_modal_btn"></i>
                                        </span>
                                        {!! Form::text('types_of_service_text', $transaction->types_of_service->name, [
                                            'class' => 'form-control',
                                            'readonly',
                                        ]) !!}

                                        {!! Form::hidden('types_of_service_id', $transaction->types_of_service_id, ['id' => 'types_of_service_id']) !!}

                                        <span class="input-group-addon">
                                            @show_tooltip(__('lang_v1.types_of_service_help'))
                                        </span>
                                    </div>
                                    <small>
                                        <p class="help-block @if (empty($transaction->selling_price_group_id)) hide @endif"
                                            id="price_group_text">
                                            @lang('lang_v1.price_group'): <span>
                                                @if (!empty($transaction->selling_price_group_id))
                                                    {{ $transaction->price_group->name }}
                                                @endif
                                            </span></p>
                                    </small>
                                </div>
                            </div>
                            <div class="modal fade types_of_service_modal" tabindex="-1" role="dialog"
                                aria-labelledby="gridSystemModalLabel">
                                @if (!empty($transaction->types_of_service))
                                    @include('types_of_service.pos_form_modal', [
                                        'types_of_service' => $transaction->types_of_service,
                                    ])
                                @endif
                            </div>
                        @endif

                        @if (in_array('subscription', $enabled_modules))
                            <div class="col-md-4 pull-right col-sm-6">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('is_recurring', 1, $transaction->is_recurring, [
                                            'class' => 'input-icheck',
                                            'id' => 'is_recurring',
                                        ]) !!} @lang('lang_v1.subscribe')?
                                    </label><button type="button" data-toggle="modal" data-target="#recurringInvoiceModal"
                                        class="btn btn-link"><i
                                            class="fa fa-external-link"></i></button>@show_tooltip(__('lang_v1.recurring_invoice_help'))
                                </div>
                            </div>
                        @endif
                        <div class="clearfix"></div>
                        <div class="@if (!empty($commission_agent)) col-sm-3 @else col-sm-3 @endif hide">
                            <div class="form-group">
                                <input type="text" id="cid_input" class="hide" name="cid_input">
                                {!! Form::label('contact_id', __('contact.customer') . ':*') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    <input type="hidden" id="default_customer_id" value="{{ $transaction->contact->id }}">
                                    <input type="hidden" id="default_customer_name" value="{{ $transaction->contact->name }}">
                                    {!! Form::select('contact_id', [], null, [
                                        'class' => 'form-control mousetrap',
                                        'id' => 'customer_id',
                                        'placeholder' => 'Enter Customer name / phone',
                                        'required',
                                    ]) !!}
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default bg-white btn-flat add_new_customer"
                                            data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                    </span>
                                </div>
                                <small
                                    class="text-danger @if (empty($customer_due)) hide @endif contact_due_text"><strong>@lang('account.customer_due'):</strong>
                                    <span>{{ $customer_due ?? '' }}</span></small>
                            </div>
                            <small class="tw-flex tw-gap-6">
                                <div>
                                    <strong>
                                        @lang('lang_v1.billing_address'):
                                    </strong>
                                    <div id="billing_address_div">
                                        {!! $transaction->contact->contact_address ?? '' !!}
                                    </div>
                                </div>
                                <div>
                                    <strong>
                                        @lang('lang_v1.shipping_address'):
                                    </strong>
                                    <div id="shipping_address_div">
                                        {!! $transaction->contact->supplier_business_name ?? '' !!} <br>
                                        {!! $transaction->contact->name ?? '' !!}, <br>
                                        {!! $transaction->contact->shipping_address ?? '' !!}
                                    </div>
                                </div>
                            </small>
                        </div>
                        <div class="col-md-3">
                            <div class="tw-flex" style="justify-content: space-around; flex-wrap: wrap; gap: 16px;">
                                <div class="amazon-address-section" style="flex: 1; min-width: 200px;">
                                    <strong>
                                        @lang('lang_v1.billing_address'):
                                    </strong>
                                    <div id="billing_address_div">
                                        {!! $transaction->contact->contact_address ?? '' !!}
                                    </div>
                                </div>
                                <div class="amazon-address-section" style="flex: 1; min-width: 200px;">
                                    <strong>
                                        @lang('lang_v1.shipping_address'):
                                    </strong>
                                    <div id="shipping_address_div" >
                                        <p>{{ $transaction->shipping_business_name ?? '' }}</p>
                                        <p>{{ $transaction->shipping_first_name ?? '' }} {{ $transaction->shipping_last_name ?? '' }},</p>
                                        <p>{{ $transaction->shipping_address1 ?? '' }} {{ $transaction->shipping_address2 ?? '' }},</p>
                                        <p>{{ $transaction->shipping_city ?? '' }}, {{ $transaction->shipping_state ?? '' }} {{ $transaction->shipping_zip ?? '' }}</p>
                                        <p>{{ $transaction->shipping_country ?? '' }}</p>
                                    <input type="hidden" id="shipping_first_name" value="{{ $transaction->shipping_first_name ?? '' }}" name="shipping_first_name">
                                    <input type="hidden" id="shipping_last_name" value="{{ $transaction->shipping_last_name ?? '' }}" name="shipping_last_name">
                                    <input type="hidden" id="shipping_company" value="{{ $transaction->shipping_company ?? '' }}" name="shipping_company">
                                    <input type="hidden" id="shipping_address1" value="{{ $transaction->shipping_address1 ?? '' }}" name="shipping_address1">
                                    <input type="hidden" id="shipping_address2" value="{{ $transaction->shipping_address2 ?? '' }}" name="shipping_address2">
                                    <input type="hidden" id="shipping_city" value="{{ $transaction->shipping_city ?? '' }}" name="shipping_city">
                                    <input type="hidden" id="shipping_state" value="{{ $transaction->shipping_state ?? '' }}" name="shipping_state">
                                    <input type="hidden" id="shipping_zip" value="{{ $transaction->shipping_zip ?? '' }}" name="shipping_zip">
                                    <input type="hidden" id="shipping_country" value="{{ $transaction->shipping_country ?? '' }}" name="shipping_country">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- @if ((!empty($pos_settings['enable_sales_order']) && $transaction->type != 'sales_order') || $is_order_request_enabled)
                            <div class="col-sm-2">
                                <div class="form-group">
                                    {!! Form::label('sales_order_ids', __('lang_v1.sales_order') . ':') !!}
                                    {!! Form::select('sales_order_ids[]', $sales_orders, $transaction->sales_order_ids, [
                                        'class' => 'form-control select2 not_loaded',
                                        'multiple',
                                        'id' => 'sales_order_ids',
                                    ]) !!}
                                </div>
                            </div>
                        @endif --}}
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="multi-input">
                                    @php
                                        $is_pay_term_required = !empty($pos_settings['is_pay_term_required']);
                                    @endphp
                                    {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
                                    <br />
                                    <div class="input-group">
                                    {!! Form::number('pay_term_number', $transaction->pay_term_number, [
                                        'class' => 'form-control width-40 pull-left',
                                        'min' => 0,
                                        'placeholder' => __('contact.pay_term'),
                                        'required' => $is_pay_term_required,
                                    ]) !!}

                                    {!! Form::select(
                                        'pay_term_type',
                                        ['months' => __('lang_v1.months'), 'days' => __('lang_v1.days')],
                                        $transaction->pay_term_type,
                                        [
                                            'class' => 'form-control width-60 pull-left',
                                            'placeholder' => __('messages.please_select'),
                                            'required' => $is_pay_term_required,
                                        ],
                                    ) !!}
                                    </div>
                                </div>
                            </div>
                        </div>


                        @if (!empty($commission_agent))
                            @php
                                $is_commission_agent_required = !empty($pos_settings['is_commission_agent_required']);
                            @endphp
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('commission_agent', __('lang_v1.commission_agent') . ':') !!}
                                    {!! Form::select('commission_agent', $commission_agent, $transaction->commission_agent, [
                                        'class' => 'form-control select2',
                                        'id' => 'commission_agent',
                                        'required' => $is_commission_agent_required,
                                    ]) !!}
                                </div>
                            </div>
                        @endif
                        <div class="@if (!empty($commission_agent)) col-sm-2 @else col-sm-2 @endif">
                            <div class="form-group">
                                {!! Form::label('transaction_date', __('sale.sale_date') . ':*') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::text('transaction_date', $transaction->transaction_date, [
                                        'class' => 'form-control',
                                        'readonly',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        @php
                            if ($transaction->status == 'draft' && $transaction->is_quotation == 1) {
                                $status = 'quotation';
                            } elseif ($transaction->status == 'draft' && $transaction->sub_status == 'proforma') {
                                $status = 'proforma';
                            } else {
                                $status = $transaction->status;
                            }
                        @endphp
                        @if ($transaction->type == 'sales_order')
                            <input type="hidden" name="status" id="status" value="{{ $transaction->status }}">
                        @else
                            <div class="@if (!empty($commission_agent)) col-sm-1 @else col-sm-1 @endif">
                                <div class="form-group">
                                    {!! Form::label('status', __('sale.status') . ':*') !!}
                                    {{-- {!! Form::select('status', $statuses, $status, [ --}}
                                    {!! Form::select('status', ['final'=>'Final'], $status, [
                                        'class' => 'form-control select2',
                                        // 'placeholder' => __('messages.please_select'),
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                        @endif
                        @if ($transaction->status == 'draft')
                            <div class="col-sm-1">
                                <div class="form-group">
                                    {!! Form::label('invoice_scheme_id', __('invoice.invoice_scheme') . ':') !!}
                                    {!! Form::select('invoice_scheme_id', $invoice_schemes, $default_invoice_schemes->id, [
                                        'class' => 'form-control select2',
                                        'placeholder' => __('messages.please_select'),
                                    ]) !!}
                                </div>
                            </div>
                        @endif
                        @can('edit_invoice_number')
                            <div class="col-sm-1">
                                <div class="form-group">
                                    {!! Form::label(
                                        'invoice_no',
                                        $transaction->type == 'sales_order' ? __('restaurant.order_no') : __('sale.invoice_no') . ':',
                                    ) !!}
                                    {!! Form::text('invoice_no', $transaction->invoice_no, [
                                        'class' => 'form-control',
                                        'placeholder' => $transaction->type == 'sales_order' ? __('restaurant.order_no') : __('sale.invoice_no'),
                                    ]) !!}
                                </div>
                            </div>
                        @endcan
                        
                        @php
                            $custom_field_1_label = !empty($custom_labels['sell']['custom_field_1'])
                                ? $custom_labels['sell']['custom_field_1']
                                : '';

                            $is_custom_field_1_required =
                                !empty($custom_labels['sell']['is_custom_field_1_required']) &&
                                $custom_labels['sell']['is_custom_field_1_required'] == 1
                                    ? true
                                    : false;

                            $custom_field_2_label = !empty($custom_labels['sell']['custom_field_2'])
                                ? $custom_labels['sell']['custom_field_2']
                                : '';

                            $is_custom_field_2_required =
                                !empty($custom_labels['sell']['is_custom_field_2_required']) &&
                                $custom_labels['sell']['is_custom_field_2_required'] == 1
                                    ? true
                                    : false;

                            $custom_field_3_label = !empty($custom_labels['sell']['custom_field_3'])
                                ? $custom_labels['sell']['custom_field_3']
                                : '';

                            $is_custom_field_3_required =
                                !empty($custom_labels['sell']['is_custom_field_3_required']) &&
                                $custom_labels['sell']['is_custom_field_3_required'] == 1
                                    ? true
                                    : false;

                            $custom_field_4_label = !empty($custom_labels['sell']['custom_field_4'])
                                ? $custom_labels['sell']['custom_field_4']
                                : '';

                            $is_custom_field_4_required =
                                !empty($custom_labels['sell']['is_custom_field_4_required']) &&
                                $custom_labels['sell']['is_custom_field_4_required'] == 1
                                    ? true
                                    : false;
                        @endphp
                        @if (!empty($custom_field_1_label))
                            @php
                                $label_1 = $custom_field_1_label . ':';
                                if ($is_custom_field_1_required) {
                                    $label_1 .= '*';
                                }
                            @endphp

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('custom_field_1', $label_1) !!}
                                    {!! Form::text('custom_field_1', $transaction->custom_field_1, [
                                        'class' => 'form-control',
                                        'placeholder' => $custom_field_1_label,
                                        'required' => $is_custom_field_1_required,
                                    ]) !!}
                                </div>
                            </div>
                        @endif
                        @if (!empty($custom_field_2_label))
                            @php
                                $label_2 = $custom_field_2_label . ':';
                                if ($is_custom_field_2_required) {
                                    $label_2 .= '*';
                                }
                            @endphp

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('custom_field_2', $label_2) !!}
                                    {!! Form::text('custom_field_2', $transaction->custom_field_2, [
                                        'class' => 'form-control',
                                        'placeholder' => $custom_field_2_label,
                                        'required' => $is_custom_field_2_required,
                                    ]) !!}
                                </div>
                            </div>
                        @endif
                        @if (!empty($custom_field_3_label))
                            @php
                                $label_3 = $custom_field_3_label . ':';
                                if ($is_custom_field_3_required) {
                                    $label_3 .= '*';
                                }
                            @endphp

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('custom_field_3', $label_3) !!}
                                    {!! Form::text('custom_field_3', $transaction->custom_field_3, [
                                        'class' => 'form-control',
                                        'placeholder' => $custom_field_3_label,
                                        'required' => $is_custom_field_3_required,
                                    ]) !!}
                                </div>
                            </div>
                        @endif
                        @if (!empty($custom_field_4_label))
                            @php
                                $label_4 = $custom_field_4_label . ':';
                                if ($is_custom_field_4_required) {
                                    $label_4 .= '*';
                                }
                            @endphp

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('custom_field_4', $label_4) !!}
                                    {!! Form::text('custom_field_4', $transaction->custom_field_4, [
                                        'class' => 'form-control',
                                        'placeholder' => $custom_field_4_label,
                                        'required' => $is_custom_field_4_required,
                                    ]) !!}
                                </div>
                            </div>
                        @endif
                        <div class="col-sm-2 hide">
                            <div class="form-group">
                                {!! Form::label('upload_document', __('purchase.attach_document') . ':') !!}
                                {!! Form::file('sell_document', [
                                    'id' => 'upload_document',
                                    'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
                                ]) !!}
                            </div>
                        </div>

                        <div class="col-sm-2">
                         {!! Form::label('Sells Rep', 'Sells Rep') !!}
                        <div class="tw-text-center "  style="border: 1px solid rgb(235, 235, 235); border-radius: 5px; background-color: rgb(235, 235, 235); width: 100%;">                        
                        <span><span class="tw-text-xl md:tw-text-lg tw-font-bold tw-text-black">{{ $user_firstname }} {{ $user_lastname }}</span></span>
                        </div>
                        
                        <div class="tw-flex tw-items-center tw-gap-2" style="margin-top:2px">
                            <div class=" tw-gap-2 tw-mb-[10px] tw-ml-[10px]">
                                {!! Form::checkbox('ex_taxes', 'wi', false, [
                                    'id' => 'ex_taxes_checkbox',
                                    'class' => 'tw-w-6 tw-h-6 tw-align-start ',
                                    'title' => 'Exempt tax',
                                ]) !!}
                            </div>
                            <span class="input-group-btn">
                            <button type="button"
                                title="Add New Products" data-toggle="tooltip"
                                class="btn btn-default bg-white btn-flat pos_add_quick_product copyPasteAdd"
                                data-href="{{ action([\App\Http\Controllers\ProductController::class, 'quickAdd']) }}"
                                data-container=".quick_add_product_modal" style="padding: 0.15rem 0.35rem; border-radius: 0; border: 1px solid #ddd;">
                                <i class="fa fa-plus-circle text-primary fa-lg"></i>
                            </button>

                            <button type="button" class="btn btn-default bg-white btn-flat copyPasteAdd"
                                id="copyButton" title="Copy" data-toggle="tooltip"
                                style="padding: 0.15rem 0.35rem; border-radius: 0; border: 1px solid #ddd;" >
                                <i class="fa fa-copy text-primary fa-lg"></i>
                            </button>
                            <button type="button" class="btn btn-default bg-white btn-flat copyPasteAdd"
                                id="pasteButton" title="Paste" data-toggle="tooltip"
                                style="padding: 0.15rem 0.35rem; border-radius: 0; border: 1px solid #ddd;" >
                                <i class="fa fa-paste text-primary fa-lg"></i>
                            </button>
                            <button type="button" class="btn btn-default bg-white btn-flat copyPasteAdd"
                                id="combine_button" title="Combine Rows" data-toggle="tooltip"
                                style="padding: 0.15rem 0.35rem; border-radius: 0; border: 1px solid #ddd;">
                                <i class="fa fa-object-group text-primary " aria-hidden="true"></i>
                            </button>
                            </span>
                            
                        </div>  
                    </div>
                        <div class="clearfix"></div>

                        <!-- Call restaurant module if defined -->
                        @if (in_array('tables', $enabled_modules) || in_array('service_staff', $enabled_modules))
                            <span id="restaurant_module_span" data-transaction_id="{{ $transaction->id }}">
                            </span>
                        @endif
                    @endcomponent

                    @component('components.widget', ['class' => 'box-solid amazon-sell-card'])
                        <div class="row col-sm-12 pos_product_div  tw-w-full tw-h-full pos_product_div " >

                            <input type="hidden" name="sell_price_tax" id="sell_price_tax"
                                value="{{ $business_details->sell_price_tax }}">

                            <!-- Keeps count of product rows -->
                            <input type="hidden" id="product_row_count" value="{{ count($sell_details) }}">
                            @php
                                $hide_tax = '';
                                if (session()->get('business.enable_inline_tax') == 0) {
                                    $hide_tax = 'hide';
                                }
                            @endphp
                           <div class="tw-flex-1 tw-overflow-auto">
                            <div class="table-responsive tw-h-full amazon-product-table-wrapper">
                                <table class="table table-condensed table-bordered table-striped"
                                    id="pos_table">
                                    <thead >
                                        <tr style="position: sticky; top: 0;">
                                            <th class="text-center"></th>
                                            <th class="text-center">@lang('sale.product')</th>
                                            <th class="text-center">Qty Available</th>
                                            <th class="text-center">@lang('sale.qty')</th>
                                            @if (!empty($pos_settings['inline_service_staff']))
                                                <th class="text-center">@lang('restaurant.service_staff')</th>
                                            @endif
                                            <th class="hide text-center">Price Per Unit</th>
                                            <th class=" text-center @if (!auth()->user()->can('edit_product_price_from_sale_screen')) hide @endif">
                                                @lang('sale.unit_price')
                                            </th>
                                            <th class="text-center @if (!auth()->user()->can('edit_product_discount_from_sale_screen')) hide @endif">
                                                @lang('receipt.discount')
                                            </th>
                                            <th class="text-center hide {{ $hide_tax }}">@lang('sale.tax')</th>
                                            <th class="text-center {{ $hide_tax }}">@lang('sale.price_inc_tax')</th>
                                            @if (!empty($common_settings['enable_product_warranty']))
                                                <th>@lang('lang_v1.warranty')</th>
                                            @endif
                                            <th class="text-center">Tax Per Unit</th>
                                            <th class="text-center">@lang('sale.subtotal')</th>
                                            <th class="hide text-center">Product IMEI Serial Number</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sell_details as $sell_line)
                                            @include('sale_pos.product_row', [
                                                'product' => $sell_line,
                                                'row_count' => $loop->index,
                                                'tax_dropdown' => $taxes,
                                                'sub_units' => !empty($sell_line->unit_details)
                                                    ? $sell_line->unit_details
                                                    : [],
                                                'action' => 'edit',
                                                'is_direct_sell' => true,
                                                'so_line' => $sell_line->so_line,
                                                'is_sales_order' => $transaction->type == 'sales_order',
                                                'is_delete_not_allowed' => true,
                                            ])
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td style="border: none;">
                                                {{-- <button type="button" class="btn" id="showButton" data-name="">
                                                    <i class="fa fa-plus-circle text-primary fa-lg"></i>
                                                </button> --}}
                                            </td>
                                            <td>
                                                <div class="col-sm-10 col-sm-offset-1">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-btn">
                                                                <button type="button"
                                                                    class="btn btn-default bg-white btn-flat"
                                                                    data-toggle="modal" data-target="#configure_search_modal"
                                                                    title="{{ __('lang_v1.configure_product_search') }}"><i
                                                                        class="fas fa-search-plus"></i></button>
                                                            </div>
                                                            {!! Form::text('search_product', null, [
                                                                'class' => 'form-control mousetrap',
                                                                'id' => 'search_product',
                                                                'placeholder' => __('lang_v1.search_product_placeholder'),
                                                                'autofocus' => true,
                                                            ]) !!}
                                                            {{-- <span class="input-group-btn">
                                                                <button type="button"
                                                                    class="btn btn-default bg-white btn-flat pos_add_quick_product"
                                                                    data-href="{{ action([\App\Http\Controllers\ProductController::class, 'quickAdd']) }}"
                                                                    data-container=".quick_add_product_modal"><i
                                                                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                                                <button type="button"
                                                                    class="btn btn-default bg-white btn-flat" id="copyButton"
                                                                    style="padding: 0.35rem 0.5rem; border-radius: 0; border: 1px solid #ddd;">
                                                                    <i class="fa fa-copy text-primary fa-lg"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-default bg-white btn-flat" id="pasteButton"
                                                                    style="padding: 0.35rem 0.5rem; border-radius: 0; border: 1px solid #ddd;">
                                                                    <i class="fa fa-paste text-primary fa-lg"></i>
                                                                </button>
                                                            </span> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <label
                                                    style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
                                                    <input type="checkbox" style="display: none;" id="toggle_switch"
                                                        onchange="this.nextElementSibling.style.backgroundColor = this.checked ? '#4CAF50' : '#ccc'; 
                                                          this.nextElementSibling.firstElementChild.style.transform = this.checked ? 'translateX(20px)' : 'translateX(0)';">
                                                    <div
                                                        style="width: 40px; height: 20px; background-color: #ccc; border-radius: 20px; position: relative; transition: background-color 0.3s; margin-bottom: 5px;">
                                                        <div
                                                            style="width: 18px; height: 18px; background-color: white; border-radius: 50%; position: absolute; top: 1px; left: 1px; transition: transform 0.3s;">
                                                        </div>
                                                    </div>
                                                    <span style="font-size: 10px; font-weight: 500; color: #333;">Enable
                                                        Metrix</span>
                                                </label>

                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                            {{-- <div class="table-responsive">
                                <table class="table table-condensed table-bordered table-striped table-responsive">
                                    <tr>
                                        <td>
                                            <div class="pull-right">
                                                <b>@lang('sale.item'):</b>
                                                <span class="total_quantity">0</span>
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>@lang('sale.total'): </b>
                                                <span class="price_total">0</span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div> --}}
                            
                        </div>
                        <div class="tw-sticky tw-w-full  tw-p-1" id='table_footer'>
                            <table class="table table-condensed ">
                                <tr>
                                    <td>
                                        <div class="row">
                                            <div class="form-group col-sm-3">
                                                <div class="form-group">
                                                    {!! Form::label('sell_note', __('sale.sell_note') . ':') !!}
                                                    {!! Form::textarea('sale_note', $transaction->additional_notes, ['class' => 'form-control', 'rows' => 1]) !!}
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-3" id="shipping_charges_section">
                                                {!! Form::label('shipping_charges', __('sale.shipping_charges')) !!}
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-dollar-sign"></i>
                                                    </span>
                                                    {!! Form::text('shipping_charges', @num_format($transaction->shipping_charges), [
                                                        'class' => 'form-control input_number',
                                                        'placeholder' => __('sale.shipping_charges'),
                                                        'min'=>0
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
    
                                    </td>
                                    <td>
                                        <div class="tw-text-right tw-text-xl">
                                            <b>@lang('sale.item'):</b>
                                            <span class="total_quantity">0</span>
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <b>@lang('sale.total'):</b>
                                            <span class="price_total">0</span>
                                        </div>
                                        <div class="tw-text-right tw-text-xl transaction_limit_text hide">
                                            <b>Transaction Limit:</b>
                                            <span class="transaction_limit"></span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    @endcomponent

                    @component('components.widget', ['class' => 'box-solid tw-hidden'])
                        <div class="col-md-4 @if ($transaction->type == 'sales_order') hide @endif">
                            <div class="form-group">
                                {!! Form::label('discount_type', __('sale.discount_type') . ':*') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    {!! Form::select(
                                        'discount_type',
                                        ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')],
                                        $transaction->discount_type,
                                        [
                                            'class' => 'form-control',
                                            'placeholder' => __('messages.please_select'),
                                            'required',
                                            'data-default' => 'percentage',
                                        ],
                                    ) !!}
                                </div>
                            </div>
                        </div>
                        @php
                            $max_discount = !is_null(auth()->user()->max_sales_discount_percent)
                                ? auth()->user()->max_sales_discount_percent
                                : '';
                        @endphp
                        <div class="col-md-4 @if ($transaction->type == 'sales_order') hide @endif">
                            <div class="form-group">
                                {!! Form::label('discount_amount', __('sale.discount_amount') . ':*') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    {!! Form::text('discount_amount', @num_format($transaction->discount_amount), [
                                        'class' => 'form-control input_number',
                                        'data-default' => $business_details->default_sales_discount,
                                        'data-max-discount' => $max_discount,
                                        'data-max-discount-error_msg' => __('lang_v1.max_discount_error_msg', [
                                            'discount' => $max_discount != '' ? @num_format($max_discount) : '',
                                        ]),
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 @if ($transaction->type == 'sales_order') hide @endif"><br>
                            <b>@lang('sale.discount_amount'):</b>(-)
                            <span class="display_currency" id="total_discount">0</span>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12 well well-sm bg-light-gray @if (session('business.enable_rp') != 1 || $transaction->type == 'sales_order') hide @endif">
                            <input type="hidden" name="rp_redeemed" id="rp_redeemed"
                                value="{{ $transaction->rp_redeemed }}">
                            <input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount"
                                value="{{ $transaction->rp_redeemed_amount }}">
                            <div class="col-md-12">
                                <h4>{{ session('business.rp_name') }}</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('rp_redeemed_modal', __('lang_v1.redeemed') . ':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-gift"></i>
                                        </span>
                                        {!! Form::number('rp_redeemed_modal', $transaction->rp_redeemed, [
                                            'class' => 'form-control direct_sell_rp_input',
                                            'data-amount_per_unit_point' => session('business.redeem_amount_per_unit_rp'),
                                            'min' => 0,
                                            'data-max_points' => !empty($redeem_details['points']) ? $redeem_details['points'] : 0,
                                            'data-min_order_total' => session('business.min_order_total_for_redeem'),
                                        ]) !!}
                                        <input type="hidden" id="rp_name" value="{{ session('business.rp_name') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p><strong>@lang('lang_v1.available'):</strong> <span
                                        id="available_rp">{{ $redeem_details['points'] ?? 0 }}</span></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>@lang('lang_v1.redeemed_amount'):</strong> (-)<span
                                        id="rp_redeemed_amount_text">{{ @num_format($transaction->rp_redeemed_amount) }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-4 @if ($transaction->type == 'sales_order') hide @endif">
                            <div class="form-group">
                                {!! Form::label('tax_rate_id', __('sale.order_tax') . ':*') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    {!! Form::select(
                                        'tax_rate_id',
                                        $taxes['tax_rates'],
                                        $transaction->tax_id,
                                        [
                                            'placeholder' => __('messages.please_select'),
                                            'class' => 'form-control',
                                            'data-default' => $business_details->default_sales_tax,
                                        ],
                                        $taxes['attributes'],
                                    ) !!}

                                    <input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount"
                                        value="{{ @num_format($transaction->tax?->amount) }}"
                                        data-default="{{ $business_details->tax_calculation_amount }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-md-offset-4 @if ($transaction->type == 'sales_order') hide @endif">
                            <b>@lang('sale.order_tax'):</b>(+)
                            <span class="display_currency" id="order_tax">{{ $transaction->tax_amount }}</span>
                        </div>
                        {{-- <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('sell_note', __('sale.sell_note') . ':') !!}
                                {!! Form::textarea('sale_note', $transaction->additional_notes, ['class' => 'form-control', 'rows' => 3]) !!}
                            </div>
                        </div> --}}
                        <input type="hidden" name="is_direct_sale" value="1">
                    @endcomponent

                    @component('components.widget', ['class' => 'box-solid tw-hidden'])
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_details', __('sale.shipping_details')) !!}
                                {!! Form::textarea('shipping_details', $transaction->shipping_details, [
                                    'class' => 'form-control',
                                    'placeholder' => __('sale.shipping_details'),
                                    'rows' => '3',
                                    'cols' => '30',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_address', __('lang_v1.shipping_address')) !!}
                                {!! Form::textarea('shipping_address', $transaction->shipping_address, [
                                    'class' => 'form-control',
                                    'placeholder' => __('lang_v1.shipping_address'),
                                    'rows' => '3',
                                    'cols' => '30',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-4 hide">
                            {{-- <div class="form-group">
                                {!! Form::label('shipping_charges', __('sale.shipping_charges')) !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    {!! Form::text('shipping_charges', @num_format($transaction->shipping_charges), [
                                        'class' => 'form-control input_number',
                                        'placeholder' => __('sale.shipping_charges'),
                                    ]) !!}
                                </div>
                            </div> --}}
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_status', __('lang_v1.shipping_status')) !!}
                                {!! Form::select('shipping_status', $shipping_statuses, $transaction->shipping_status, [
                                    'class' => 'form-control',
                                    'placeholder' => __('messages.please_select'),
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('delivered_to', __('lang_v1.delivered_to') . ':') !!}
                                {!! Form::text('delivered_to', $transaction->delivered_to, [
                                    'class' => 'form-control',
                                    'placeholder' => __('lang_v1.delivered_to'),
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('delivery_person', __('lang_v1.delivery_person') . ':') !!}
                                {!! Form::select('delivery_person', $users, $transaction->delivery_person, [
                                    'class' => 'form-control select2',
                                    'placeholder' => __('messages.please_select'),
                                ]) !!}
                            </div>
                        </div>
                        @php
                            $shipping_custom_label_1 = !empty($custom_labels['shipping']['custom_field_1'])
                                ? $custom_labels['shipping']['custom_field_1']
                                : '';

                            $is_shipping_custom_field_1_required =
                                !empty($custom_labels['shipping']['is_custom_field_1_required']) &&
                                $custom_labels['shipping']['is_custom_field_1_required'] == 1
                                    ? true
                                    : false;

                            $shipping_custom_label_2 = !empty($custom_labels['shipping']['custom_field_2'])
                                ? $custom_labels['shipping']['custom_field_2']
                                : '';

                            $is_shipping_custom_field_2_required =
                                !empty($custom_labels['shipping']['is_custom_field_2_required']) &&
                                $custom_labels['shipping']['is_custom_field_2_required'] == 1
                                    ? true
                                    : false;

                            $shipping_custom_label_3 = !empty($custom_labels['shipping']['custom_field_3'])
                                ? $custom_labels['shipping']['custom_field_3']
                                : '';

                            $is_shipping_custom_field_3_required =
                                !empty($custom_labels['shipping']['is_custom_field_3_required']) &&
                                $custom_labels['shipping']['is_custom_field_3_required'] == 1
                                    ? true
                                    : false;

                            $shipping_custom_label_4 = !empty($custom_labels['shipping']['custom_field_4'])
                                ? $custom_labels['shipping']['custom_field_4']
                                : '';

                            $is_shipping_custom_field_4_required =
                                !empty($custom_labels['shipping']['is_custom_field_4_required']) &&
                                $custom_labels['shipping']['is_custom_field_4_required'] == 1
                                    ? true
                                    : false;

                            $shipping_custom_label_5 = !empty($custom_labels['shipping']['custom_field_5'])
                                ? $custom_labels['shipping']['custom_field_5']
                                : '';

                            $is_shipping_custom_field_5_required =
                                !empty($custom_labels['shipping']['is_custom_field_5_required']) &&
                                $custom_labels['shipping']['is_custom_field_5_required'] == 1
                                    ? true
                                    : false;
                        @endphp

                        @if (!empty($shipping_custom_label_1))
                            @php
                                $label_1 = $shipping_custom_label_1 . ':';
                                if ($is_shipping_custom_field_1_required) {
                                    $label_1 .= '*';
                                }
                            @endphp

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('shipping_custom_field_1', $label_1) !!}
                                    {!! Form::text(
                                        'shipping_custom_field_1',
                                        !empty($transaction->shipping_custom_field_1) ? $transaction->shipping_custom_field_1 : null,
                                        [
                                            'class' => 'form-control',
                                            'placeholder' => $shipping_custom_label_1,
                                            'required' => $is_shipping_custom_field_1_required,
                                        ],
                                    ) !!}
                                </div>
                            </div>
                        @endif
                        @if (!empty($shipping_custom_label_2))
                            @php
                                $label_2 = $shipping_custom_label_2 . ':';
                                if ($is_shipping_custom_field_2_required) {
                                    $label_2 .= '*';
                                }
                            @endphp

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('shipping_custom_field_2', $label_2) !!}
                                    {!! Form::text(
                                        'shipping_custom_field_2',
                                        !empty($transaction->shipping_custom_field_2) ? $transaction->shipping_custom_field_2 : null,
                                        [
                                            'class' => 'form-control',
                                            'placeholder' => $shipping_custom_label_2,
                                            'required' => $is_shipping_custom_field_2_required,
                                        ],
                                    ) !!}
                                </div>
                            </div>
                        @endif
                        @if (!empty($shipping_custom_label_3))
                            @php
                                $label_3 = $shipping_custom_label_3 . ':';
                                if ($is_shipping_custom_field_3_required) {
                                    $label_3 .= '*';
                                }
                            @endphp

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('shipping_custom_field_3', $label_3) !!}
                                    {!! Form::text(
                                        'shipping_custom_field_3',
                                        !empty($transaction->shipping_custom_field_3) ? $transaction->shipping_custom_field_3 : null,
                                        [
                                            'class' => 'form-control',
                                            'placeholder' => $shipping_custom_label_3,
                                            'required' => $is_shipping_custom_field_3_required,
                                        ],
                                    ) !!}
                                </div>
                            </div>
                        @endif
                        @if (!empty($shipping_custom_label_4))
                            @php
                                $label_4 = $shipping_custom_label_4 . ':';
                                if ($is_shipping_custom_field_4_required) {
                                    $label_4 .= '*';
                                }
                            @endphp

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('shipping_custom_field_4', $label_4) !!}
                                    {!! Form::text(
                                        'shipping_custom_field_4',
                                        !empty($transaction->shipping_custom_field_4) ? $transaction->shipping_custom_field_4 : null,
                                        [
                                            'class' => 'form-control',
                                            'placeholder' => $shipping_custom_label_4,
                                            'required' => $is_shipping_custom_field_4_required,
                                        ],
                                    ) !!}
                                </div>
                            </div>
                        @endif
                        @if (!empty($shipping_custom_label_5))
                            @php
                                $label_5 = $shipping_custom_label_5 . ':';
                                if ($is_shipping_custom_field_5_required) {
                                    $label_5 .= '*';
                                }
                            @endphp

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('shipping_custom_field_5', $label_5) !!}
                                    {!! Form::text(
                                        'shipping_custom_field_5',
                                        !empty($transaction->shipping_custom_field_5) ? $transaction->shipping_custom_field_5 : null,
                                        [
                                            'class' => 'form-control',
                                            'placeholder' => $shipping_custom_label_5,
                                            'required' => $is_shipping_custom_field_5_required,
                                        ],
                                    ) !!}
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_documents', __('lang_v1.shipping_documents') . ':') !!}
                                {!! Form::file('shipping_documents[]', [
                                    'id' => 'shipping_documents',
                                    'multiple',
                                    'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
                                ]) !!}
                                <p class="help-block">
                                    @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])
                                    @includeIf('components.document_help_text')
                                </p>
                                @php
                                    $medias = $transaction->media
                                        ->where('model_media_type', 'shipping_document')
                                        ->all();
                                @endphp
                                @include('sell.partials.media_table', [
                                    'medias' => $medias,
                                    'delete' => true,
                                ])
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12 text-center">
                            <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm"
                                id="toggle_additional_expense"> <i class="fas fa-plus"></i> @lang('lang_v1.add_additional_expenses') <i
                                    class="fas fa-chevron-down"></i></button>
                        </div>
                        <div class="col-md-8 col-md-offset-4" id="additional_expenses_div">
                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th>@lang('lang_v1.additional_expense_name')</th>
                                        <th>@lang('sale.amount')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            {!! Form::text('additional_expense_key_1', $transaction->additional_expense_key_1, [
                                                'class' => 'form-control',
                                                'id' => 'additional_expense_key_1',
                                            ]) !!}
                                        </td>
                                        <td>
                                            {!! Form::text('additional_expense_value_1', @num_format($transaction->additional_expense_value_1), [
                                                'class' => 'form-control input_number',
                                                'id' => 'additional_expense_value_1',
                                            ]) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {!! Form::text('additional_expense_key_2', $transaction->additional_expense_key_2, [
                                                'class' => 'form-control',
                                                'id' => 'additional_expense_key_2',
                                            ]) !!}
                                        </td>
                                        <td>
                                            {!! Form::text('additional_expense_value_2', @num_format($transaction->additional_expense_value_2), [
                                                'class' => 'form-control input_number',
                                                'id' => 'additional_expense_value_2',
                                            ]) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {!! Form::text('additional_expense_key_3', $transaction->additional_expense_key_3, [
                                                'class' => 'form-control',
                                                'id' => 'additional_expense_key_3',
                                            ]) !!}
                                        </td>
                                        <td>
                                            {!! Form::text('additional_expense_value_3', @num_format($transaction->additional_expense_value_3), [
                                                'class' => 'form-control input_number',
                                                'id' => 'additional_expense_value_3',
                                            ]) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {!! Form::text('additional_expense_key_4', $transaction->additional_expense_key_4, [
                                                'class' => 'form-control',
                                                'id' => 'additional_expense_key_4',
                                            ]) !!}
                                        </td>
                                        <td>
                                            {!! Form::text('additional_expense_value_4', @num_format($transaction->additional_expense_value_4), [
                                                'class' => 'form-control input_number',
                                                'id' => 'additional_expense_value_4',
                                            ]) !!}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-4 col-md-offset-8">
                            @if (!empty($pos_settings['amount_rounding_method']) && $pos_settings['amount_rounding_method'] > 0)
                                <small id="round_off"><br>(@lang('lang_v1.round_off'): <span id="round_off_text">0</span>)</small>
                                <br />
                                <input type="hidden" name="round_off_amount" id="round_off_amount" value=0>
                            @endif
                            <div><b>@lang('sale.total_payable'): </b>
                                <input type="hidden" name="final_total" id="final_total_input">
                                <span id="total_payable">0</span>
                            </div>
                        </div>
                    @endcomponent
                    @if (!empty($common_settings['is_enabled_export']) && $transaction->type != 'sales_order')
                        @component('components.widget', ['class' => 'box-solid', 'title' => __('lang_v1.export')])
                            <div class="col-md-12 mb-12">
                                <div class="form-check">
                                    <input type="checkbox" name="is_export" class="form-check-input" id="is_export"
                                        @if (!empty($transaction->is_export)) checked @endif>
                                    <label class="form-check-label" for="is_export">@lang('lang_v1.is_export')</label>
                                </div>
                            </div>
                            @php
                                $i = 1;
                            @endphp
                            @for ($i; $i <= 6; $i++)
                                <div class="col-md-4 export_div"
                                    @if (empty($transaction->is_export)) style="display: none;" @endif>
                                    <div class="form-group">
                                        {!! Form::label('export_custom_field_' . $i, __('lang_v1.export_custom_field' . $i) . ':') !!}
                                        {!! Form::text(
                                            'export_custom_fields_info[' . 'export_custom_field_' . $i . ']',
                                            !empty($transaction->export_custom_fields_info['export_custom_field_' . $i])
                                                ? $transaction->export_custom_fields_info['export_custom_field_' . $i]
                                                : null,
                                            [
                                                'class' => 'form-control',
                                                'placeholder' => __('lang_v1.export_custom_field' . $i),
                                                'id' => 'export_custom_field_' . $i,
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                            @endfor
                        @endcomponent
                    @endif
                </div>
            </div>
            @php
                $is_enabled_download_pdf = config('constants.enable_download_pdf');
            @endphp
            @if ($is_enabled_download_pdf && $transaction->type != 'sales_order')
                @can('sell.payments')
                    @component('components.widget', ['class' => 'box-solid hide', 'title' => __('purchase.add_payment')])
                        <div class="well row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('prefer_payment_method', __('lang_v1.prefer_payment_method') . ':') !!}
                                    @show_tooltip(__('lang_v1.this_will_be_shown_in_pdf'))
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fas fa-money-bill-alt"></i>
                                        </span>
                                        {!! Form::select('prefer_payment_method', $payment_types, $transaction->prefer_payment_method, [
                                            'class' => 'form-control',
                                            'style' => 'width:100%;',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('prefer_payment_account', __('lang_v1.prefer_payment_account') . ':') !!}
                                    @show_tooltip(__('lang_v1.this_will_be_shown_in_pdf'))
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fas fa-money-bill-alt"></i>
                                        </span>
                                        {!! Form::select('prefer_payment_account', $accounts, $transaction->prefer_payment_account, [
                                            'class' => 'form-control',
                                            'style' => 'width:100%;',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcomponent
                @endcan
            @endif

            @if ($transaction->type != 'sales_order')
                @can('sell.payments')
                    @component('components.widget', ['class' => 'box-solid hide', 'title' => __('purchase.add_payment')])
                        <div class="row">
                            @foreach ($payment_lines as $payment_line)
                                @if ($payment_line['is_return'] == 1)
                                    @php
                                        $change_return = $payment_line;
                                    @endphp

                                    @continue
                                @endif

                                @if (!empty($payment_line['id']))
                                    {!! Form::hidden("payment[$loop->index][payment_id]", $payment_line['id']) !!}
                                @endif
                                <div class="payment_row" id="payment_rows_div">
                                    @include('sale_pos.partials.payment_row_form', [
                                        'row_index' => $loop->index,
                                        'show_date' => true,
                                        'payment_line' => $payment_line,
                                        'show_denomination' => true,
                                    ])
                                </div>
                            @endforeach


                            <div class="col-md-12">
                                <hr>
                                <strong>
                                    @lang('lang_v1.change_return'):
                                </strong>
                                <br />
                                <span class="lead text-bold change_return_span">0</span>
                                {!! Form::hidden('change_return', $change_return['amount'], [
                                    'class' => 'form-control change_return input_number',
                                    'required',
                                    'id' => 'change_return',
                                ]) !!}
                                <!-- <span class="lead text-bold total_quantity">0</span> -->
                                @if (!empty($change_return['id']))
                                    <input type="hidden" name="change_return_id" value="{{ $change_return['id'] }}">
                                @endif
                            </div>
                        </div>
                        <div class="row @if ($change_return['amount'] == 0) hide @endif payment_row"
                            id="change_return_payment_data">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('change_return_method', __('lang_v1.change_return_payment_method') . ':*') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fas fa-money-bill-alt"></i>
                                        </span>
                                        @php
                                            $_payment_method =
                                                empty($change_return['method']) &&
                                                array_key_exists('cash', $payment_types)
                                                    ? 'cash'
                                                    : $change_return['method'];

                                            $_payment_types = $payment_types;
                                            if (isset($_payment_types['advance'])) {
                                                unset($_payment_types['advance']);
                                            }
                                        @endphp
                                        {!! Form::select('payment[change_return][method]', $_payment_types, $_payment_method, [
                                            'class' => 'form-control col-md-12 payment_types_dropdown',
                                            'id' => 'change_return_method',
                                            'style' => 'width:100%;',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            @if (!empty($accounts))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('change_return_account', __('lang_v1.change_return_payment_account') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fas fa-money-bill-alt"></i>
                                            </span>
                                            {!! Form::select(
                                                'payment[change_return][account_id]',
                                                $accounts,
                                                !empty($change_return['account_id']) ? $change_return['account_id'] : '',
                                                ['class' => 'form-control select2', 'id' => 'change_return_account', 'style' => 'width:100%;'],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @include('sale_pos.partials.payment_type_details', [
                                'payment_line' => $change_return,
                                'row_index' => 'change_return',
                            ])
                        </div>
                    @endcomponent
                @endcan
            @endif
            {{-- <div class="row">
                <div class="col-md-12 text-center">
                    {!! Form::hidden('is_save_and_print', 0, ['id' => 'is_save_and_print']) !!}
                    <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-lg"
                        id="submit-sell">@lang('messages.update')</button>
                    <button type="button" id="save-and-print"
                        class="tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-lg">@lang('lang_v1.update_and_print')</button>
                </div>
            </div> --}}
            @if (in_array('subscription', $enabled_modules))
                @include('sale_pos.partials.recurring_invoice_modal')
            @endif
            {!! Form::close() !!}
        </section>
        </div>

        <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
            @include('contact.create', ['quick_add' => true])
        </div>
        <!-- /.content -->
        <div class="modal fade register_details_modal" tabindex="-1" role="dialog"
            aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade close_register_modal" tabindex="-1" role="dialog"
            aria-labelledby="gridSystemModalLabel">
        </div>
        <!-- quick product modal -->
        <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle">
        </div>
        <div class="hide edit_id" id={{$transaction->id}} transaction_type="Transaction"></div>
        @include('sale_pos.partials.configure_search_modal')
        <div class="modal fade" id="invoicePreviewModal" tabindex="-1" role="dialog" aria-labelledby="invoicePreviewModalLabel">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header" style="padding: 5px 10px;">
                        <div class="tw-flex tw-justify-between">
                            <h4 class="modal-title tw-flex" style="align-items: center" id="modalTitle">Invoice Preview</h4>
                            <div class="tw-flex tw-justify-end tw-gap-5">
                                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print" id="confirm_invoice_submit">Confirm</button>
                                <button type="button" class="tw-dw-btn tw-dw-btn-danger tw-text-white no-print" data-dismiss="modal"
                                    id='invoicePreviewModalLabel' style="background-color: #ff0019; border-color: #dc3545;">@lang('messages.close')</button>
                            </div>
            
                        </div>
                    </div>
                    <!-- Modal Body -->
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Scrollable Table -->
                                <div class="tw-overflow-x-auto" style="max-height:60vh; min-height:60vh; overflow-y: auto;">
                                    <table class="tw-w-full tw-border tw-text-center mb-0" id="preview_invoice_table">
                                        <thead style="position: sticky; top: 0; z-index: 9; background-color: #fff;">
                                            <tr>
                                                <th class="tw-p-2 tw-border ">S.No</th>
                                                <th class="tw-p-2 tw-border ">@lang('sale.product')</th>
                                                <th class="tw-p-2 tw-border ">@lang('sale.qty')</th>
                                                <th class="tw-p-2 tw-border ">Recalled Price</th>
                                                <th class="tw-p-2 tw-border ">@lang('sale.unit_price') Ex Tax</th>
                                                <th class="tw-p-2 tw-border customer-mode-hide">@lang('sale.discount')</th>
                                                <th class="tw-p-2 tw-border ">Discounted Price Ex Tax</th>
                                                <th class="tw-p-2 tw-border customer-mode-hide">Cost Price Ex Tax</th>
                                                <th class="tw-p-2 tw-border customer-mode-hide">Profit/Loss Per Unit @show_tooltip('Red Text indicates Loss, Green Text indicates Profit')</th>
                                                <th class="tw-p-2 tw-border customer-mode-hide">Tax Rate</th>
                                                <th class="tw-p-2 tw-border customer-mode-hide">Price Inc Tax</th>
                                                <th class="tw-p-2 tw-border customer-mode-hide">Cost Total Ex Tax</th>
                                                <th class="tw-p-2 tw-border customer-mode-hide">Total Price Ex Tax</th>
                                                <th class="tw-p-2 tw-border customer-mode-hide">Profit/Loss Total @show_tooltip('Red Text indicates Loss, Green Text indicates Profit')</th>
                                                <th class="tw-p-2 tw-border ">Subtotal Inc Tax</th>
        
                                            </tr>
                                        </thead>
                                        <tbody id="preview_invoice_table_body">
                                            <!-- Dynamic content -->
                                        </tbody>
                                    </table>
                                </div>
        
                                <!-- Totals Table aligned right -->
                                <div class="tw-mt-3 table-responsive" style="max-width: 400px; float: right;">
                                    <table class="table" style="border: none">
                                        <tbody >
                                            <tr>
                                                <td style="border:none;" class="text-right"><strong>Items:</strong></td>
                                                <td style="border:none;"><span> </span><span id="preview_total_quantity">0.00</span></td>
                                            </tr>
                                             <tr class="customer-mode-hide">
                                                <td style="border:none;" class="text-right"><strong>@lang('sale.discount'):</strong></td>
                                                <td style="border:none;"><span> </span><span id="preview_discount_amount">0.00</span></td>
                                            </tr>
                                            <tr class="customer-mode-hide">
                                                <td style="border:none;" class="text-right"><strong>@lang('sale.tax'):</strong></td>
                                                <td style="border:none;"><span> </span><span id="preview_tax_amount">0.00</span></td>
                                            </tr>
                                            <tr>
                                                <td style="border:none;" class="text-right"><strong>@lang('sale.total'):</strong></td>
                                                <td style="border:none;"><span> </span><span id="preview_total_amount">0.00</span></td>
                                            </tr>
                                            <tr class="customer-mode-hide">
                                                <td style="border:none;" class="text-right"><strong>Shipping Charges:</strong></td>
                                                <td style="border:none;"><span> </span><span id="preview_shipping_total">0.00</span></td>
                                            </tr>
                                            <tr class="customer-mode-hide">
                                                <td style="border:none;" class="text-right"><strong>@lang('sale.total_payable'):</strong></td>
                                                <td style="border:none;"><span> </span><span id="preview_final_total">0.00</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="clear: both;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</div>

    @stop
    

@section('javascript')
    <script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/session_lock.js?v=' . $asset_v) }}"></script>
    <!-- Call restaurant module if defined -->
    @if (in_array('tables', $enabled_modules) ||
            in_array('modifiers', $enabled_modules) ||
            in_array('service_staff', $enabled_modules))
        <script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <script type="text/javascript">
        $(document).ready(function() {


             $('#invoicePreviewModal').on('hidden.bs.modal', function () {
            $('button#submit-sell').prop('disabled', false);
            $('button#save-and-print').prop('disabled', false);
        });

            // let transaction_id=@json($transaction->id);
            // console.log(transaction_id);
            $('#shipping_documents').fileinput({
                showUpload: false,
                showPreview: false,
                browseLabel: LANG.file_browse_label,
                removeLabel: LANG.remove,
            });

            $('#is_export').on('change', function() {
                if ($(this).is(':checked')) {
                    $('div.export_div').show();
                } else {
                    $('div.export_div').hide();
                }
            });

            $('#status').change(function() {
                if ($(this).val() == 'final') {
                    $('#payment_rows_div').removeClass('hide');
                } else {
                    $('#payment_rows_div').addClass('hide');
                }
            });
            $('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            })
          
        });
    </script>
@endsection
