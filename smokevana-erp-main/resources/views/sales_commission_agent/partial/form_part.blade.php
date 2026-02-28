<div class="col-md-12">
    <div class="col-md-12">
        <hr>
        <h4>Personal Details:</h4>
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('user_dob', __( 'lang_v1.dob' ) . ':') !!}
        {!! Form::text(
            'dob',
            !empty($user->dob) ? @format_date($user->dob) : null,
            [
                'class' => 'form-control',
                'id' => 'agent_dob',
                'placeholder' => __( 'lang_v1.dob'),
                'readonly',
            ]
        ) !!}
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('gender', __( 'lang_v1.gender' ) . ':') !!}
        {!! Form::select('gender', ['male' => __('lang_v1.male'), 'female' => __('lang_v1.female'), 'others' => __('lang_v1.others')], !empty($user->gender) ? $user->gender : null, ['class' => 'form-control', 'id' => 'gender', 'placeholder' => __( 'messages.please_select') ]); !!}
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('marital_status', __( 'lang_v1.marital_status' ) . ':') !!}
        {!! Form::select('marital_status', ['married' => __( 'lang_v1.married'), 'unmarried' => __( 'lang_v1.unmarried' ), 'divorced' => __( 'lang_v1.divorced' )], !empty($user->marital_status) ? $user->marital_status : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.marital_status') ]); !!}
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('blood_group', __( 'lang_v1.blood_group' ) . ':') !!}
        {!! Form::text('blood_group', !empty($user->blood_group) ? $user->blood_group : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.blood_group') ]); !!}
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-3">
        {!! Form::label('contact_number', __( 'lang_v1.mobile_number' ) . ':') !!}
        {!! Form::text('contact_number', !empty($user->contact_number) ? $user->contact_number : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.mobile_number') ]); !!}
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('alt_number', __( 'business.alternate_number' ) . ':') !!}
        {!! Form::text('alt_number', !empty($user->alt_number) ? $user->alt_number : null, ['class' => 'form-control', 'placeholder' => __( 'business.alternate_number') ]); !!}
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('family_number', __( 'lang_v1.family_contact_number' ) . ':') !!}
        {!! Form::text('family_number', !empty($user->family_number) ? $user->family_number : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.family_contact_number') ]); !!}
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('guardian_name', __( 'lang_v1.guardian_name') . ':') !!}
        {!! Form::text('guardian_name', !empty($user->guardian_name) ? $user->guardian_name : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.guardian_name' ) ]); !!}
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('id_proof_name', __( 'lang_v1.id_proof_name') . ':') !!}
        {!! Form::text('id_proof_name', !empty($user->id_proof_name) ? $user->id_proof_name : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.id_proof_name' ) ]); !!}
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('id_proof_number', __( 'lang_v1.id_proof_number') . ':') !!}
        {!! Form::text('id_proof_number', !empty($user->id_proof_number) ? $user->id_proof_number : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.id_proof_number' ) ]); !!}
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-6">
        {!! Form::label('permanent_address', __( 'lang_v1.permanent_address') . ':') !!}
        {!! Form::textarea('permanent_address', !empty($user->permanent_address) ? $user->permanent_address : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.permanent_address'), 'rows' => 3 ]); !!}
    </div>
    <div class="form-group col-md-6">
        {!! Form::label('current_address', __( 'lang_v1.current_address') . ':') !!}
        {!! Form::textarea('current_address', !empty($user->current_address) ? $user->current_address : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.current_address'), 'rows' => 3 ]); !!}
    </div>
    <div class="col-md-12">
        <hr>
        <h4>@lang('lang_v1.bank_details'):</h4>
    </div>
    @php
        $bank_details = [];
        if (isset($user) && !empty($user->bank_details)) {
            $bank_details = json_decode($user->bank_details, true);
        }
    @endphp
    <div class="form-group col-md-3">
        {!! Form::label('account_holder_name', __( 'lang_v1.account_holder_name') . ':') !!}
        {!! Form::text('bank_details[account_holder_name]', !empty($bank_details['account_holder_name']) ? $bank_details['account_holder_name'] : null , ['class' => 'form-control', 'id' => 'account_holder_name', 'placeholder' => __( 'lang_v1.account_holder_name') ]); !!}
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('account_number', __( 'lang_v1.account_number') . ':') !!}
        {!! Form::text('bank_details[account_number]', !empty($bank_details['account_number']) ? $bank_details['account_number'] : null, ['class' => 'form-control', 'id' => 'account_number', 'placeholder' => __( 'lang_v1.account_number') ]); !!}
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('bank_name', __( 'lang_v1.bank_name') . ':') !!}
        {!! Form::text('bank_details[bank_name]', !empty($bank_details['bank_name']) ? $bank_details['bank_name'] : null, ['class' => 'form-control', 'id' => 'bank_name', 'placeholder' => __( 'lang_v1.bank_name') ]); !!}
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('bank_code', __( 'lang_v1.bank_code') . ':') !!} @show_tooltip(__('lang_v1.bank_code_help'))
        {!! Form::text('bank_details[bank_code]', !empty($bank_details['bank_code']) ? $bank_details['bank_code'] : null, ['class' => 'form-control', 'id' => 'bank_code', 'placeholder' => __( 'lang_v1.bank_code') ]); !!}
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('branch', __( 'lang_v1.branch') . ':') !!}
        {!! Form::text('bank_details[branch]', !empty($bank_details['branch']) ? $bank_details['branch'] : null, ['class' => 'form-control', 'id' => 'branch', 'placeholder' => __( 'lang_v1.branch') ]); !!}
    </div>
    <div class="form-group col-md-3">
        {!! Form::label('tax_payer_id', __( 'lang_v1.tax_payer_id') . ':') !!}
        @show_tooltip(__('lang_v1.tax_payer_id_help'))
        {!! Form::text('bank_details[tax_payer_id]', !empty($bank_details['tax_payer_id']) ? $bank_details['tax_payer_id'] : null, ['class' => 'form-control', 'id' => 'tax_payer_id', 'placeholder' => __( 'lang_v1.tax_payer_id') ]); !!}
    </div>
  </div>