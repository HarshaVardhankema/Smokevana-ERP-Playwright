<div class="pos-tab-content">
    <div class="settings-cards-grid">
        {{-- Card 1: Shortcuts --}}
        <div class="modern-settings-card">
            <div class="modern-settings-card__title">
                <i class="fa fa-keyboard"></i> @lang('business.add_keyboard_shortcuts')
            </div>
            <p class="help-block">@lang('lang_v1.shortcut_help'); @lang('lang_v1.example'): <b>ctrl+shift+b</b>, <b>ctrl+h</b></p>
            <div class="table-responsive">
                <table class="table table-striped table-condensed">
                    <tr>
                        <th>@lang('business.operations')</th>
                        <th>@lang('business.keyboard_shortcut')</th>
                    </tr>
                    @foreach([
                        'express_checkout' => __('sale.express_finalize'),
                        'pay_n_ckeckout' => __('sale.finalize'),
                        'draft' => __('sale.draft'),
                        'cancel' => __('messages.cancel'),
                        'recent_product_quantity' => __('lang_v1.recent_product_quantity'),
                        'weighing_scale' => __('lang_v1.weighing_scale'),
                        'edit_discount' => __('sale.edit_discount'),
                        'edit_order_tax' => __('sale.edit_order_tax'),
                        'add_payment_row' => __('sale.add_payment_row'),
                        'finalize_payment' => __('sale.finalize_payment'),
                        'add_new_product' => __('lang_v1.add_new_product')
                    ] as $key => $label)
                    <tr>
                        <td>{!! $label !!}:</td>
                        <td>
                            {!! Form::text('shortcuts[pos]['.$key.']', !empty($shortcuts["pos"][$key]) ? $shortcuts["pos"][$key] : null, ['class' => 'form-control input-sm']); !!}
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>

        {{-- Card 2: POS Behaviors --}}
        <div class="modern-settings-card">
            <div class="modern-settings-card__title">
                <i class="fa fa-cog"></i> @lang('lang_v1.pos_settings')
            </div>

            @php
                $pos_checkboxes = [
                    'disable_pay_checkout' => __('lang_v1.disable_pay_checkout'),
                    'disable_draft' => __('lang_v1.disable_draft'),
                    'disable_express_checkout' => __('lang_v1.disable_express_checkout'),
                    'hide_product_suggestion' => __('lang_v1.hide_product_suggestion'),
                    'hide_recent_trans' => __('lang_v1.hide_recent_trans'),
                    'disable_discount' => __('lang_v1.disable_discount'),
                    'disable_order_tax' => __('lang_v1.disable_order_tax'),
                    'is_pos_subtotal_editable' => __('lang_v1.subtotal_editable'),
                    'disable_suspend' => __('lang_v1.disable_suspend_sale'),
                    'enable_transaction_date' => __('lang_v1.enable_pos_transaction_date'),
                    'inline_service_staff' => __('lang_v1.enable_service_staff_in_product_line'),
                    'is_service_staff_required' => __('lang_v1.is_service_staff_required'),
                    'disable_credit_sale_button' => __('lang_v1.disable_credit_sale_button'),
                    'enable_weighing_scale' => __('lang_v1.enable_weighing_scale'),
                    'show_pricing_on_product_sugesstion' => __('lang_v1.show_pricing_on_product_sugesstion')
                ];
            @endphp

            @foreach($pos_checkboxes as $key => $label)
                <div class="modern-switch-container">
                    <span class="modern-switch-label">{{ $label }}</span>
                    <label class="modern-switch">
                        {!! Form::checkbox('pos_settings['.$key.']', 1, !empty($pos_settings[$key])); !!}
                        <span class="modern-slider"></span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>
    
    <hr>
    @include('business.partials.settings_weighing_scale')
</div>
