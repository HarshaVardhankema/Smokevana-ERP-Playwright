@extends('layouts.app')
@section('title', __('lang_v1.my_profile'))

@section('content')

<style>
    .profile-field .input-group-btn .btn {
        min-width: 40px;
    }

    .profile-field .profile-edit-btn {
        background: #FFB703 !important;
        border-color: #FFB703 !important;
    }

    .profile-field .profile-edit-btn,
    .profile-field .profile-edit-btn i {
        color: #111 !important;
    }

    .profile-field .profile-edit-btn i {
        font-size: 14px;
    }

    .profile-field .profile-edit-btn:hover,
    .profile-field .profile-edit-btn:focus {
        background: #F4A900 !important;
        border-color: #F4A900 !important;
    }

    .profile-field .profile-save-btn,
    .profile-field .profile-cancel-btn {
        border-left: 0;
    }

    .profile-field .profile-edit-btn {
        border-left: 0;
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.my_profile')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
<div class="row">
    <div class="col-sm-12">
        <div class="box box-solid"> <!--business info box start-->
            <div class="box-header">
                <div class="box-header">
                    <h3 class="box-title"> @lang('user.change_password')</h3>
                </div>
            </div>
            <div class="box-body">
                <div class="row" style="margin-left: -5px; margin-right: -5px;">
                    <!-- Left Column: Profile Photo -->
                    <div class="col-md-4" style="padding-right: 10px;">
                        <div class="form-group">
                            <label><strong>@lang('lang_v1.profile_photo')</strong></label>
                            @if(!empty($user->media))
                                <div class="text-center" style="margin-bottom: 15px;">
                                    {!! $user->media->thumbnail([150, 150], 'img-circle') !!}
                                </div>
                            @endif
                            <div class="form-group">
                                {!! Form::label('profile_photo', __('lang_v1.upload_image') . ':') !!}
                                {!! Form::file('profile_photo', ['id' => 'profile_photo', 'accept' => 'image/*', 'form' => 'edit_user_profile_form_main']); !!}
                                <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])</p></small>
                            </div>
                        </div>
                    </div>
                    <!-- Right Column: Password Fields -->
                    <div class="col-md-8" style="padding-left: 10px;">
                        {!! Form::open(['url' => action([\App\Http\Controllers\UserController::class, 'updatePassword']), 'method' => 'post', 'id' => 'edit_password_form',
                                    'class' => 'form-horizontal' ]) !!}
                        <div class="form-group">
                            {!! Form::label('current_password', __('user.current_password') . ':', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    {!! Form::password('current_password', ['class' => 'form-control','placeholder' => __('user.current_password'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('new_password', __('user.new_password') . ':', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    {!! Form::password('new_password', ['class' => 'form-control','placeholder' => __('user.new_password'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('confirm_password', __('user.confirm_new_password') . ':', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    {!! Form::password('confirm_password', ['class' => 'form-control','placeholder' =>  __('user.confirm_new_password'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white pull-right">@lang('messages.update')</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Profile Section - Full width for proper alignment -->
{!! Form::open(['url' => action([\App\Http\Controllers\UserController::class, 'updateProfile']), 'method' => 'post', 'id' => 'edit_user_profile_form_main', 'files' => true ]) !!}
<div class="row">
    <div class="col-sm-12">
        <div class="box box-solid"> <!--business info box start-->
            <div class="box-header">
            <h3 class="box-title"> @lang('user.edit_profile')</h3>
            </div>
            <div class="box-body">
                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group profile-field" data-field="name">
                                    <label>@lang('user.name'):</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input type="text" class="form-control profile-field-input" value="{{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) }}" disabled>
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                                            <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                                            <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group profile-field" data-field="email">
                                    <label>@lang('business.email'):</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                        <input type="email" class="form-control profile-field-input" value="{{ $user->email }}" disabled>
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                                            <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                                            <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group profile-field" data-field="language">
                                    <label>@lang('business.language'):</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-language"></i></span>
                                        {!! Form::select('language',$languages, $user->language, ['class' => 'form-control select2 profile-field-input', 'disabled' => true]); !!}
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                                            <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                                            <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group profile-field" data-field="contact_number">
                                    <label>@lang('lang_v1.mobile_number'):</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                        <input type="text" class="form-control profile-field-input" value="{{ $user->contact_number }}" disabled>
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default profile-edit-btn">Edit</button>
                                            <button type="button" class="btn btn-success profile-save-btn" style="display:none;"><i class="fa fa-check"></i></button>
                                            <button type="button" class="btn btn-danger profile-cancel-btn" style="display:none;"><i class="fa fa-times"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('user.edit_profile_form_part', ['bank_details' => !empty($user->bank_details) ? json_decode($user->bank_details, true) : null])
<div class="row">
    <div class="col-md-12 text-center">
        <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-lg">@lang('messages.update')</button>
    </div>
</div>
{!! Form::close() !!}

</section>
<!-- /.content -->
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    function disableAllProfileFields() {
        $('.profile-field').each(function() {
            
            var $field = $(this);
            var $input = $field.find('.profile-field-input');
            $input.prop('disabled', true);
            $field.find('.profile-save-btn, .profile-cancel-btn').hide();
            $field.find('.profile-edit-btn').show();
        });
    }

    $(document).on('click', '.profile-edit-btn', function() {
        disableAllProfileFields();
        var $field = $(this).closest('.profile-field');
        var $input = $field.find('.profile-field-input');
        $field.data('original', $input.val());
        $input.prop('disabled', false);

        if ($input.hasClass('select2')) {
            __select2($input);
        }

        $field.find('.profile-edit-btn').hide();
        $field.find('.profile-save-btn, .profile-cancel-btn').show();
        $input.focus();
    });

    $(document).on('click', '.profile-cancel-btn', function() {
        var $field = $(this).closest('.profile-field');
        var $input = $field.find('.profile-field-input');
        var original = $field.data('original');
        if (typeof original !== 'undefined') {
            $input.val(original).trigger('change');
        }
        disableAllProfileFields();
    });

    $(document).on('click', '.profile-save-btn', function() {
        var $field = $(this).closest('.profile-field');
        var fieldName = $field.data('field');
        var $input = $field.find('.profile-field-input');
        var value = $input.val();

        $.ajax({
            method: 'POST',
            url: "{{ route('user.updateProfileField') }}",
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                field: fieldName,
                value: value
            },
            success: function(result) {
                if (result.success) {
                    toastr.success(result.msg);
                    disableAllProfileFields();
                } else {
                    toastr.error(result.msg);
                }
            },
            error: function(xhr) {
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error(LANG.something_went_wrong);
                }
            }
        });
    });
});
</script>
@endsection