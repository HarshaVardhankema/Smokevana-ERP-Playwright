@php
  $custom_labels = json_decode(session('business.custom_labels'), true);
  $user_custom_field1 = !empty($custom_labels['user']['custom_field_1']) ? $custom_labels['user']['custom_field_1'] : __('lang_v1.user_custom_field1');
  $user_custom_field2 = !empty($custom_labels['user']['custom_field_2']) ? $custom_labels['user']['custom_field_2'] : __('lang_v1.user_custom_field2');
  $user_custom_field3 = !empty($custom_labels['user']['custom_field_3']) ? $custom_labels['user']['custom_field_3'] : __('lang_v1.user_custom_field3');
  $user_custom_field4 = !empty($custom_labels['user']['custom_field_4']) ? $custom_labels['user']['custom_field_4'] : __('lang_v1.user_custom_field4');
@endphp
<div class="row">
    <div class="form-group col-md-6 profile-field" data-field="dob">
        {!! Form::label('user_dob', __( 'lang_v1.dob' ) . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            {!! Form::text('dob', !empty($user->dob) ? @format_date($user->dob) : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'lang_v1.dob'), 'id' => 'user_dob', 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
    <div class="form-group col-md-6 profile-field" data-field="gender">
        {!! Form::label('gender', __( 'lang_v1.gender' ) . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-venus-mars"></i></span>
            {!! Form::select('gender', ['male' => __('lang_v1.male'), 'female' => __('lang_v1.female'), 'others' => __('lang_v1.others')], !empty($user->gender) ? $user->gender : null, ['class' => 'form-control profile-field-input', 'id' => 'gender', 'placeholder' => __( 'messages.please_select'), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-6 profile-field" data-field="marital_status">
        {!! Form::label('marital_status', __( 'lang_v1.marital_status' ) . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-info"></i></span>
            {!! Form::select('marital_status', ['married' => __( 'lang_v1.married'), 'unmarried' => __( 'lang_v1.unmarried' ), 'divorced' => __( 'lang_v1.divorced' )], !empty($user->marital_status) ? $user->marital_status : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'lang_v1.marital_status'), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
    <div class="form-group col-md-6 profile-field" data-field="blood_group">
        {!! Form::label('blood_group', __( 'lang_v1.blood_group' ) . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-tint"></i></span>
            {!! Form::text('blood_group', !empty($user->blood_group) ? $user->blood_group : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'lang_v1.blood_group'), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-6 profile-field" data-field="alt_number">
        {!! Form::label('alt_number', __( 'business.alternate_number' ) . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
            {!! Form::text('alt_number', !empty($user->alt_number) ? $user->alt_number : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'business.alternate_number'), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
    <div class="form-group col-md-6 profile-field" data-field="family_number">
        {!! Form::label('family_number', __( 'lang_v1.family_contact_number' ) . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
            {!! Form::text('family_number', !empty($user->family_number) ? $user->family_number : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'lang_v1.family_contact_number'), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
</div>

<div class="clearfix"></div>
<div class="col-md-12">
    <hr>
    {{-- <h4>@lang('lang_v1.more_info'):</h4> --}}
</div>
<div class="row">
    <div class="form-group col-md-6 profile-field" data-field="custom_field_1">
        {!! Form::label('custom_field_1', $user_custom_field1 . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-tag"></i></span>
            {!! Form::text('custom_field_1', !empty($user->custom_field_1) ? $user->custom_field_1 : null, ['class' => 'form-control profile-field-input', 'placeholder' => $user_custom_field1, 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
    <div class="form-group col-md-6 profile-field" data-field="custom_field_2">
        {!! Form::label('custom_field_2', $user_custom_field2 . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-tag"></i></span>
            {!! Form::text('custom_field_2', !empty($user->custom_field_2) ? $user->custom_field_2 : null, ['class' => 'form-control profile-field-input', 'placeholder' => $user_custom_field2, 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-6 profile-field" data-field="custom_field_3">
        {!! Form::label('custom_field_3', $user_custom_field3 . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-tag"></i></span>
            {!! Form::text('custom_field_3', !empty($user->custom_field_3) ? $user->custom_field_3 : null, ['class' => 'form-control profile-field-input', 'placeholder' => $user_custom_field3, 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
    <div class="form-group col-md-6 profile-field" data-field="custom_field_4">
        {!! Form::label('custom_field_4', $user_custom_field4 . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-tag"></i></span>
            {!! Form::text('custom_field_4', !empty($user->custom_field_4) ? $user->custom_field_4 : null, ['class' => 'form-control profile-field-input', 'placeholder' => $user_custom_field4, 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-6 profile-field" data-field="guardian_name">
        {!! Form::label('guardian_name', __( 'lang_v1.guardian_name') . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-user"></i></span>
            {!! Form::text('guardian_name', !empty($user->guardian_name) ? $user->guardian_name : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'lang_v1.guardian_name' ), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
    <div class="form-group col-md-6 profile-field" data-field="id_proof_name">
        {!! Form::label('id_proof_name', __( 'lang_v1.id_proof_name') . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
            {!! Form::text('id_proof_name', !empty($user->id_proof_name) ? $user->id_proof_name : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'lang_v1.id_proof_name' ), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-6 profile-field" data-field="id_proof_number">
        {!! Form::label('id_proof_number', __( 'lang_v1.id_proof_number') . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
            {!! Form::text('id_proof_number', !empty($user->id_proof_number) ? $user->id_proof_number : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'lang_v1.id_proof_number' ), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="form-group col-md-6 profile-field" data-field="permanent_address">
    {!! Form::label('permanent_address', __( 'lang_v1.permanent_address') . ':') !!}
    <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
        {!! Form::textarea('permanent_address', !empty($user->permanent_address) ? $user->permanent_address : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'lang_v1.permanent_address'), 'rows' => 3, 'disabled' => true ]); !!}
        <span class="input-group-btn">
            <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
            <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
            <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
        </span>
    </div>
</div>
<div class="form-group col-md-6 profile-field" data-field="current_address">
    {!! Form::label('current_address', __( 'lang_v1.current_address') . ':') !!}
    <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
        {!! Form::textarea('current_address', !empty($user->current_address) ? $user->current_address : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'lang_v1.current_address'), 'rows' => 3, 'disabled' => true ]); !!}
        <span class="input-group-btn">
            <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
            <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
            <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
        </span>
    </div>
</div>

<div class="clearfix"></div>
<div class="col-md-12">
    <hr>
    <h4>@lang('lang_v1.social_media_portfolios'):</h4>
</div>
<div class="row">
    <div class="form-group col-md-6 profile-field" data-field="fb_link">
        {!! Form::label('fb_link', __( 'lang_v1.fb_link' ) . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-link"></i></span>
            {!! Form::text('fb_link', !empty($user->fb_link) ? $user->fb_link : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'lang_v1.fb_link'), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
    <div class="form-group col-md-6 profile-field" data-field="twitter_link">
        {!! Form::label('twitter_link', __( 'lang_v1.twitter_link' ) . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-link"></i></span>
            {!! Form::text('twitter_link', !empty($user->twitter_link) ? $user->twitter_link : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'lang_v1.twitter_link'), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-6 profile-field" data-field="social_media_1">
        {!! Form::label('social_media_1', __( 'lang_v1.social_media', ['number' => 1] ) . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-link"></i></span>
            {!! Form::text('social_media_1', !empty($user->social_media_1) ? $user->social_media_1 : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'lang_v1.social_media', ['number' => 1] ), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
    <div class="form-group col-md-6 profile-field" data-field="social_media_2">
        {!! Form::label('social_media_2', __( 'lang_v1.social_media', ['number' => 2] ) . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-link"></i></span>
            {!! Form::text('social_media_2', !empty($user->social_media_2) ? $user->social_media_2 : null, ['class' => 'form-control profile-field-input', 'placeholder' => __( 'lang_v1.social_media', ['number' => 2] ), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
</div>

<div class="clearfix"></div>
<div class="col-md-12">
    <hr>
    <h4>@lang('lang_v1.bank_details'):</h4>
</div>
<div class="row">
    <div class="form-group col-md-6 profile-field" data-field="bank_details.account_holder_name">
        {!! Form::label('account_holder_name', __( 'lang_v1.account_holder_name') . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-university"></i></span>
            {!! Form::text('bank_details[account_holder_name]', !empty($bank_details['account_holder_name']) ? $bank_details['account_holder_name'] : null , ['class' => 'form-control profile-field-input', 'id' => 'account_holder_name', 'placeholder' => __( 'lang_v1.account_holder_name'), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
    <div class="form-group col-md-6 profile-field" data-field="bank_details.account_number">
        {!! Form::label('account_number', __( 'lang_v1.account_number') . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-university"></i></span>
            {!! Form::text('bank_details[account_number]', !empty($bank_details['account_number']) ? $bank_details['account_number'] : null, ['class' => 'form-control profile-field-input', 'id' => 'account_number', 'placeholder' => __( 'lang_v1.account_number'), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-6 profile-field" data-field="bank_details.bank_name">
        {!! Form::label('bank_name', __( 'lang_v1.bank_name') . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-university"></i></span>
            {!! Form::text('bank_details[bank_name]', !empty($bank_details['bank_name']) ? $bank_details['bank_name'] : null, ['class' => 'form-control profile-field-input', 'id' => 'bank_name', 'placeholder' => __( 'lang_v1.bank_name'), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
    <div class="form-group col-md-6 profile-field" data-field="bank_details.bank_code">
        {!! Form::label('bank_code', __( 'lang_v1.bank_code') . ':') !!} @show_tooltip(__('lang_v1.bank_code_help'))
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-university"></i></span>
            {!! Form::text('bank_details[bank_code]', !empty($bank_details['bank_code']) ? $bank_details['bank_code'] : null, ['class' => 'form-control profile-field-input', 'id' => 'bank_code', 'placeholder' => __( 'lang_v1.bank_code'), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-6 profile-field" data-field="bank_details.branch">
        {!! Form::label('branch', __( 'lang_v1.branch') . ':') !!}
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-university"></i></span>
            {!! Form::text('bank_details[branch]', !empty($bank_details['branch']) ? $bank_details['branch'] : null, ['class' => 'form-control profile-field-input', 'id' => 'branch', 'placeholder' => __( 'lang_v1.branch'), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
    <div class="form-group col-md-6 profile-field" data-field="bank_details.tax_payer_id">
        {!! Form::label('tax_payer_id', __( 'lang_v1.tax_payer_id') . ':') !!}
        @show_tooltip(__('lang_v1.tax_payer_id_help'))
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-university"></i></span>
            {!! Form::text('bank_details[tax_payer_id]', !empty($bank_details['tax_payer_id']) ? $bank_details['tax_payer_id'] : null, ['class' => 'form-control profile-field-input', 'id' => 'tax_payer_id', 'placeholder' => __( 'lang_v1.tax_payer_id'), 'disabled' => true ]); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
            </span>
        </div>
    </div>
</div>