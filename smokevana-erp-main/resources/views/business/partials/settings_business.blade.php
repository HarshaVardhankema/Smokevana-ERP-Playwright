<div class="pos-tab-content active">
    <div class="settings-cards-grid">
        {{-- Card 1: Basic info --}}
        <div class="modern-settings-card">
            <div class="modern-settings-card__title">
                <i class="fa fa-building"></i> @lang('business.business')
            </div>
            <div class="form-group">
                {!! Form::label('name',__('business.business_name') . ':*') !!}
                {!! Form::text('name', $business->name, ['class' => 'form-control', 'required', 'placeholder' => __('business.business_name')]); !!}
            </div>
            <div class="form-group">
                {!! Form::label('start_date', __('business.start_date') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    {!! Form::text('start_date', @format_date($business->start_date), ['class' => 'form-control start-date-picker','placeholder' => __('business.start_date'), 'readonly']); !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('default_profit_percent', __('business.default_profit_percent') . ':*') !!} @show_tooltip(__('tooltip.default_profit_percent'))
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-plus-circle"></i></span>
                    {!! Form::text('default_profit_percent', @num_format($business->default_profit_percent), ['class' => 'form-control input_number']); !!}
                </div>
            </div>
        </div>

        {{-- Card 2: Currency & Timezone --}}
        <div class="modern-settings-card">
            <div class="modern-settings-card__title">
                <i class="fas fa-money-bill-alt"></i> @lang('business.currency') &amp; @lang('business.time_zone')
            </div>
            <div class="form-group">
                {!! Form::label('currency_id', __('business.currency') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i></span>
                    {!! Form::select('currency_id', $currencies, $business->currency_id, ['class' => 'form-control select2','placeholder' => __('business.currency'), 'required']); !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('currency_symbol_placement', __('lang_v1.currency_symbol_placement') . ':') !!}
                {!! Form::select('currency_symbol_placement', ['before' => __('lang_v1.before_amount'), 'after' => __('lang_v1.after_amount')], $business->currency_symbol_placement, ['class' => 'form-control select2', 'required']); !!}
            </div>
            <div class="form-group">
                {!! Form::label('time_zone', __('business.time_zone') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-clock"></i></span>
                    {!! Form::select('time_zone', $timezone_list, $business->time_zone, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>

        {{-- Card 3: Logos --}}
        <div class="modern-settings-card">
            <div class="modern-settings-card__title">
                <i class="fa fa-image"></i> Business Logos
            </div>
            <div class="form-group">
                {!! Form::label('business_logo', __('business.upload_logo') . ':') !!}
                {!! Form::file('business_logo', ['accept' => 'image/*']); !!}
                <p class="help-block"><i> @lang('business.logo_help')</i></p>
            </div>
            <div class="form-group">
                {!! Form::label('fevicon_logo', 'Favicon Icon:') !!}
                {!! Form::file('fevicon_logo', ['accept' => 'image/*']); !!}
                <p class="help-block"><i> @lang('business.logo_help')</i></p>
            </div>
        </div>

        {{-- Card 4: Accounting --}}
        <div class="modern-settings-card">
            <div class="modern-settings-card__title">
                <i class="fa fa-calculator"></i> Accounting Settings
            </div>
            <div class="form-group">
                {!! Form::label('fy_start_month', __('business.fy_start_month') . ':') !!} @show_tooltip(__('tooltip.fy_start_month'))
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    {!! Form::select('fy_start_month', $months, $business->fy_start_month, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('accounting_method', __('business.accounting_method') . ':*') !!} @show_tooltip(__('tooltip.accounting_method'))
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calculator"></i></span>
                    {!! Form::select('accounting_method', $accounting_methods, $business->accounting_method, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('transaction_edit_days', __('business.transaction_edit_days') . ':*') !!} @show_tooltip(__('tooltip.transaction_edit_days'))
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                    {!! Form::number('transaction_edit_days', $business->transaction_edit_days, ['class' => 'form-control','placeholder' => __('business.transaction_edit_days'), 'required']); !!}
                </div>
            </div>
        </div>

        {{-- Card 5: Formats & Precision --}}
        <div class="modern-settings-card">
            <div class="modern-settings-card__title">
                <i class="fa fa-clock"></i> Formats & Precision
            </div>
            <div class="form-group">
                {!! Form::label('date_format', __('lang_v1.date_format') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    {!! Form::select('date_format', $date_formats, $business->date_format, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('time_format', __('lang_v1.time_format') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-clock"></i></span>
                    {!! Form::select('time_format', [12 => __('lang_v1.12_hour'), 24 => __('lang_v1.24_hour')], $business->time_format, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('currency_precision', __('lang_v1.currency_precision') . ':*') !!}
                        {!! Form::select('currency_precision', [0 =>0, 1=>1, 2=>2, 3=>3,4=>4], $business->currency_precision, ['class' => 'form-control select2', 'required']); !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('quantity_precision', __('lang_v1.quantity_precision') . ':*') !!}
                        {!! Form::select('quantity_precision', [0 =>0, 1=>1, 2=>2, 3=>3,4=>4], $business->quantity_precision, ['class' => 'form-control select2', 'required']); !!}
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 6: Order Module --}}
        <div class="modern-settings-card">
            <div class="modern-settings-card__title">
                <i class="fa fa-sitemap"></i> Order Module
            </div>
            <div class="form-group">
                {!! Form::label('manage_order_module',  'Manage Order Module:*') !!}
                <br>
                <div class="tw-flex tw-gap-4">
                    <label class="radio-inline" style="accent-color: #ff9900;">
                        <input type="radio" name="manage_order_module" @if($business->manage_order_module == 'manual') checked @endif value="manual"> ERP
                    </label>
                    <label class="radio-inline" style="accent-color: #ff9900;">
                        <input type="radio" name="manage_order_module" @if($business->manage_order_module == 'pickerApp') checked @endif value="pickerApp"> Picker App
                    </label>
                    <label class="radio-inline" style="accent-color: #ff9900;">
                        <input type="radio" name="manage_order_module" @if($business->manage_order_module == 'both') checked @endif value="both"> Both
                    </label>
                </div>
            </div>

            <div class="modern-switch-container">
                <span class="modern-switch-label">{{ __( 'lang_v1.enable_export' ) }}</span>
                <label class="modern-switch">
                    {!! Form::checkbox('common_settings[is_enabled_export]', true, !empty($common_settings['is_enabled_export']) ? true : false); !!}
                    <span class="modern-slider"></span>
                </label>
            </div>
        </div>
    </div>

    {{-- Price Group Sequence table - full width card below the grid --}}
    <div class="modern-settings-card">
        <div class="modern-settings-card__title">
            <i class="fa fa-list-ol"></i> @lang('lang_v1.price_group_sequence')
        </div>
        <p class="help-block">@lang('lang_v1.price_group_sequence_help')</p>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="price_group_sequence_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang('lang_v1.price_group_name')</th>
                        <th>@lang('lang_v1.sequence')</th>
                        <th>@lang('lang_v1.percentage')</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($price_groups) && count($price_groups) > 0)
                        @php
                            $price_group_sequence = !empty($common_settings['price_group_sequence']) ? $common_settings['price_group_sequence'] : [];
                            $price_group_percentage = !empty($common_settings['price_group_percentage']) ? $common_settings['price_group_percentage'] : [];
                        @endphp
                        @foreach($price_groups as $price_group)
                            <tr>
                                <td>{{ $price_group->name }}</td>
                                <td>
                                    {!! Form::number('price_group_sequence[' . $price_group->id . ']', !empty($price_group_sequence[$price_group->id]) ? $price_group_sequence[$price_group->id] : null, ['class' => 'form-control', 'min' => '1', 'placeholder' => __('lang_v1.sequence')]) !!}
                                </td>
                                <td>
                                    {!! Form::number('price_group_percentage[' . $price_group->id . ']', !empty($price_group_percentage[$price_group->id]) ? $price_group_percentage[$price_group->id] : null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'max' => '100', 'placeholder' => __('lang_v1.percentage')]) !!}
                                    <small class="help-block">@lang('lang_v1.percentage_help_text')</small>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="text-center">@lang('lang_v1.no_price_groups_found')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
