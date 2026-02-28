<div class="pos-tab-content">
    <div class="settings-cards-grid">
        {{-- Card 1: SKU & Expiry --}}
        <div class="modern-settings-card">
            <div class="modern-settings-card__title">
                <i class="fa fa-tag"></i> SKU & Expiry Settings
            </div>
            <div class="form-group">
                {!! Form::label('sku_prefix', __('business.sku_prefix') . ':') !!}
                {!! Form::text('sku_prefix', $business->sku_prefix, ['class' => 'form-control text-uppercase', 'placeholder' => 'SKU Prefix']); !!}
            </div>

            <div class="form-group">
                <div class="modern-switch-container no-border" style="padding-bottom: 5px;">
                    <span class="modern-switch-label">
                        <strong>{{ __( 'product.enable_product_expiry' ) }}</strong>
                        @show_tooltip(__('lang_v1.tooltip_enable_expiry'))
                    </span>
                    <label class="modern-switch">
                        {!! Form::checkbox('enable_product_expiry', 1, $business->enable_product_expiry, ['id' => 'enable_product_expiry']); !!} 
                        <span class="modern-slider"></span>
                    </label>
                </div>
                <div class="input-group @if(!$business->enable_product_expiry) hide @endif" id="expiry_type_container">
                    <span class="input-group-addon"><i class="fa fa-calendar-check"></i></span>
                    <select class="form-control" id="expiry_type" name="expiry_type" @if(!$business->enable_product_expiry) disabled @endif>
                        <option value="add_expiry" @if($business->expiry_type == 'add_expiry') selected @endif>{{__('lang_v1.add_expiry')}}</option>
                        <option value="add_manufacturing" @if($business->expiry_type == 'add_manufacturing') selected @endif>{{__('lang_v1.add_manufacturing_auto_expiry')}}</option>
                    </select>
                </div>
            </div>

            <div class="@if(!$business->enable_product_expiry) hide @endif" id="on_expiry_div">
                <div class="form-group">
                    {!! Form::label('on_product_expiry', __('lang_v1.on_product_expiry') . ':') !!}
                    @show_tooltip(__('lang_v1.tooltip_on_product_expiry'))
                    <div class="row">
                        <div class="col-sm-7">
                            {!! Form::select('on_product_expiry', ['keep_selling'=>__('lang_v1.keep_selling'), 'stop_selling'=>__('lang_v1.stop_selling') ], $business->on_product_expiry, ['class' => 'form-control select2', 'style' => 'width:100%;']); !!}
                        </div>
                        <div class="col-sm-5">
                            @php $disabled = ($business->on_product_expiry == 'keep_selling') ? 'disabled' : ''; @endphp
                            {!! Form::number('stop_selling_before', $business->stop_selling_before, ['class' => 'form-control', 'placeholder' => 'Days', 'style' => 'width:100%;', $disabled, 'required', 'id' => 'stop_selling_before']); !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Features --}}
        <div class="modern-settings-card">
            <div class="modern-settings-card__title">
                <i class="fa fa-cubes"></i> Features & Attributes
            </div>
            
            <div class="modern-switch-container">
                <span class="modern-switch-label">{{ __( 'lang_v1.enable_brand' ) }}</span>
                <label class="modern-switch">
                    {!! Form::checkbox('enable_brand', 1, $business->enable_brand); !!}
                    <span class="modern-slider"></span>
                </label>
            </div>

            <div class="modern-switch-container">
                <span class="modern-switch-label">{{ __( 'lang_v1.enable_category' ) }}</span>
                <label class="modern-switch">
                    {!! Form::checkbox('enable_category', 1, $business->enable_category, ['id' => 'enable_category']); !!}
                    <span class="modern-slider"></span>
                </label>
            </div>

            <div class="modern-switch-container enable_sub_category @if($business->enable_category != 1) hide @endif">
                <span class="modern-switch-label">{{ __( 'lang_v1.enable_sub_category' ) }}</span>
                <label class="modern-switch">
                    {!! Form::checkbox('enable_sub_category', 1, $business->enable_sub_category, ['id' => 'enable_sub_category']); !!}
                    <span class="modern-slider"></span>
                </label>
            </div>

            <div class="modern-switch-container">
                <span class="modern-switch-label">{{ __( 'lang_v1.enable_price_tax' ) }}</span>
                <label class="modern-switch">
                    {!! Form::checkbox('enable_price_tax', 1, $business->enable_price_tax); !!}
                    <span class="modern-slider"></span>
                </label>
            </div>
        </div>

        {{-- Card 3: Units --}}
        <div class="modern-settings-card">
            <div class="modern-settings-card__title">
                <i class="fa fa-balance-scale"></i> Measurement Units
            </div>
            <div class="form-group">
                {!! Form::label('default_unit', __('lang_v1.default_unit') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-balance-scale"></i></span>
                    {!! Form::select('default_unit', $units_dropdown, $business->default_unit, ['class' => 'form-control select2', 'style' => 'width: 100%;' ]); !!}
                </div>
            </div>

            <div class="modern-switch-container">
                <span class="modern-switch-label">
                    {{ __( 'lang_v1.enable_sub_units' ) }}
                    @show_tooltip(__('lang_v1.sub_units_tooltip'))
                </span>
                <label class="modern-switch">
                    {!! Form::checkbox('enable_sub_units', 1, $business->enable_sub_units); !!}
                    <span class="modern-slider"></span>
                </label>
            </div>

            <div class="modern-switch-container @if(config('constants.enable_secondary_unit') == false) hide @endif">
                <span class="modern-switch-label">{{ __( 'lang_v1.enable_secondary_unit' ) }}</span>
                <label class="modern-switch">
                    {!! Form::checkbox('common_settings[enable_secondary_unit]', 1, !empty($common_settings['enable_secondary_unit']) ? true : false); !!}
                    <span class="modern-slider"></span>
                </label>
            </div>
        </div>

        {{-- Card 4: Organization --}}
        <div class="modern-settings-card">
            <div class="modern-settings-card__title">
                <i class="fa fa-sitemap"></i> Product Organization
            </div>
            
            <div class="modern-switch-container">
                <span class="modern-switch-label">
                    {{ __( 'lang_v1.enable_racks' ) }}
                    @show_tooltip(__('lang_v1.tooltip_enable_racks'))
                </span>
                <label class="modern-switch">
                    {!! Form::checkbox('enable_racks', 1, $business->enable_racks); !!}
                    <span class="modern-slider"></span>
                </label>
            </div>

            <div class="modern-switch-container">
                <span class="modern-switch-label">{{ __( 'lang_v1.enable_row' ) }}</span>
                <label class="modern-switch">
                    {!! Form::checkbox('enable_row', 1, $business->enable_row); !!}
                    <span class="modern-slider"></span>
                </label>
            </div>

            <div class="modern-switch-container">
                <span class="modern-switch-label">{{ __( 'lang_v1.enable_position' ) }}</span>
                <label class="modern-switch">
                    {!! Form::checkbox('enable_position', 1, $business->enable_position); !!}
                    <span class="modern-slider"></span>
                </label>
            </div>
        </div>

        {{-- Card 5: Additional Policies --}}
        <div class="modern-settings-card">
            <div class="modern-settings-card__title">
                <i class="fa fa-shield-alt"></i> Additional Policies
            </div>

            <div class="modern-switch-container">
                <span class="modern-switch-label">{{ __( 'lang_v1.enable_product_warranty' ) }}</span>
                <label class="modern-switch">
                    {!! Form::checkbox('common_settings[enable_product_warranty]', 1, !empty($common_settings['enable_product_warranty']) ? true : false); !!}
                    <span class="modern-slider"></span>
                </label>
            </div>

            <div class="modern-switch-container">
                <span class="modern-switch-label">{{ __( 'lang_v1.is_product_image_required' ) }}</span>
                <label class="modern-switch">
                    {!! Form::checkbox('common_settings[is_product_image_required]', 1, !empty($common_settings['is_product_image_required']) ? true : false); !!}
                    <span class="modern-slider"></span>
                </label>
            </div>
        </div>
    </div>
</div>
