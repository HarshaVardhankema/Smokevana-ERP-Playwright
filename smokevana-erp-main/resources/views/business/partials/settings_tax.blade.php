<div class="pos-tab-content">
    <div class="modern-settings-card">
        <div class="modern-settings-card__title">
            <i class="fa fa-percent"></i> Tax Settings
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('tax_label_1', __('business.tax_1_name') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-info"></i></span>
                        {!! Form::text('tax_label_1', $business->tax_label_1, ['class' => 'form-control','placeholder' => __('business.tax_1_placeholder')]); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('tax_number_1', __('business.tax_1_no') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-info"></i></span>
                        {!! Form::text('tax_number_1', $business->tax_number_1, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('tax_label_2', __('business.tax_2_name') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-info"></i></span>
                        {!! Form::text('tax_label_2', $business->tax_label_2, ['class' => 'form-control','placeholder' => __('business.tax_1_placeholder')]); !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('tax_number_2', __('business.tax_2_no') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-info"></i></span>
                        {!! Form::text('tax_number_2', $business->tax_number_2, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="modern-switch-container">
            <span class="modern-switch-label">{{ __( 'lang_v1.enable_inline_tax' ) }}</span>
            <label class="modern-switch">
                {!! Form::checkbox('enable_inline_tax', 1, $business->enable_inline_tax); !!}
                <span class="modern-slider"></span>
            </label>
        </div>
    </div>
</div>
