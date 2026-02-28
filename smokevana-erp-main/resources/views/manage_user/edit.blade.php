@extends('layouts.app')

@section('title', __( 'user.edit_user' ))

@section('css')
<style>
/* Amazon-style User Form - Compact Version */
.amazon-user-container {
    padding: 12px 20px;
    background: #EAEDED;
    min-height: 100vh;
}

.amazon-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 1px solid #DDD;
}

.amazon-page-title {
    font-size: 22px;
    font-weight: 400;
    color: #0F1111;
    margin: 0;
    font-family: "Amazon Ember", Arial, sans-serif;
}

.amazon-breadcrumb {
    font-size: 12px;
    color: #565959;
    margin-top: 2px;
}

.amazon-breadcrumb a {
    color: #007185;
    text-decoration: none;
}

.amazon-breadcrumb a:hover {
    color: #C7511F;
    text-decoration: underline;
}

/* Amazon Card Styling - Compact */
.amazon-card {
    background: #FFFFFF;
    border: 1px solid #D5D9D9;
    border-radius: 8px;
    margin-bottom: 12px;
    overflow: hidden;
}

.amazon-card-header {
    display: flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(to bottom, #F7F8F8, #F0F2F2);
    border-bottom: 1px solid #D5D9D9;
    padding: 10px 16px;
    font-size: 14px;
    font-weight: 700;
    color: #0F1111;
}

.amazon-card-header svg {
    flex-shrink: 0;
    color: #232F3E;
}

.amazon-card-body {
    padding: 14px 16px;
}

/* Form Grid Layout - Tighter */
.amazon-form-row {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 12px;
    align-items: flex-end;
}

.amazon-form-row:last-child {
    margin-bottom: 0;
}

.amazon-form-col {
    flex: 1;
    min-width: 180px;
}

.amazon-form-col-sm {
    flex: 0 0 90px;
    min-width: 90px;
}

.amazon-form-col-md {
    flex: 0 0 200px;
    min-width: 200px;
}

.amazon-form-col-email {
    flex: 0 0 280px;
    min-width: 250px;
}

.amazon-form-col-toggle {
    flex: 0 0 auto;
    min-width: auto;
}

@media (max-width: 768px) {
    .amazon-form-col,
    .amazon-form-col-sm,
    .amazon-form-col-md,
    .amazon-form-col-email,
    .amazon-form-col-toggle {
        flex: 1 1 100%;
    }
}

/* Form Group Styling - Compact */
.amazon-form-group {
    margin-bottom: 0;
}

.amazon-form-group label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #0F1111;
    margin-bottom: 3px;
    font-family: "Amazon Ember", Arial, sans-serif;
}

.amazon-form-group label .required {
    color: #C40000;
}

.amazon-form-control {
    width: 100%;
    height: 32px;
    padding: 4px 10px;
    font-size: 13px;
    color: #0F1111;
    background-color: #FFF;
    border: 1px solid #888C8C;
    border-radius: 4px;
    box-shadow: 0 1px 2px rgba(15,17,17,.15) inset;
    transition: all .1s linear;
}

.amazon-form-control:focus {
    outline: none;
    border-color: #007185;
    box-shadow: 0 0 0 3px #C8F3FA, 0 1px 2px rgba(15,17,17,.15) inset;
}

.amazon-form-control::placeholder {
    color: #888C8C;
}

select.amazon-form-control {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23555' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    padding-right: 30px;
}

.amazon-form-control.select2-hidden-accessible + .select2-container .select2-selection {
    height: 32px;
    border: 1px solid #888C8C;
    border-radius: 4px;
    box-shadow: 0 1px 2px rgba(15,17,17,.15) inset;
}

.amazon-form-control.select2-hidden-accessible + .select2-container .select2-selection__rendered {
    line-height: 30px;
    padding-left: 10px;
    color: #0F1111;
    font-size: 13px;
}

.amazon-form-control.select2-hidden-accessible + .select2-container .select2-selection__arrow {
    height: 30px;
}

textarea.amazon-form-control {
    height: auto;
    min-height: 60px;
    resize: vertical;
}

/* Amazon Toggle Switch - Inline */
.amazon-toggle-inline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 32px;
    padding-top: 0;
    white-space: nowrap;
}

.amazon-toggle {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 22px;
    flex-shrink: 0;
}

.amazon-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.amazon-toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #B7BABF;
    transition: .2s;
    border-radius: 22px;
}

.amazon-toggle-slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .2s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,.3);
}

.amazon-toggle input:checked + .amazon-toggle-slider {
    background-color: #FF9900;
}

.amazon-toggle input:checked + .amazon-toggle-slider:before {
    transform: translateX(18px);
}

.amazon-toggle-label {
    font-size: 13px;
    color: #0F1111;
    font-weight: 400;
    white-space: nowrap;
}

/* Amazon Checkbox - Fixed alignment */
.amazon-checkbox-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 4px 0;
    cursor: pointer;
    white-space: nowrap;
}

.amazon-checkbox {
    position: relative;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.amazon-checkbox input {
    opacity: 0;
    position: absolute;
    width: 100%;
    height: 100%;
    cursor: pointer;
    margin: 0;
}

.amazon-checkbox-mark {
    position: absolute;
    top: 0;
    left: 0;
    width: 16px;
    height: 16px;
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border: 1px solid #888C8C;
    border-radius: 3px;
    pointer-events: none;
    transition: all .1s linear;
}

.amazon-checkbox input:checked + .amazon-checkbox-mark {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    border-color: #C45500;
}

.amazon-checkbox input:checked + .amazon-checkbox-mark:after {
    content: '';
    position: absolute;
    left: 4px;
    top: 1px;
    width: 5px;
    height: 9px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.amazon-checkbox-label {
    font-size: 13px;
    color: #0F1111;
    display: inline;
    line-height: 16px;
}

/* Tooltip Icon - Improved */
.amazon-tooltip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 14px;
    height: 14px;
    background: #007185;
    color: white;
    border-radius: 50%;
    font-size: 10px;
    font-weight: 700;
    cursor: help;
    margin-left: 4px;
    vertical-align: middle;
    position: relative;
}

.amazon-tooltip:hover {
    background: #C7511F;
}

.amazon-tooltip:hover::after {
    content: attr(data-tip);
    position: absolute;
    bottom: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    background: #232F3E;
    color: #FFF;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 400;
    white-space: normal;
    width: max-content;
    max-width: 250px;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,.25);
    line-height: 1.4;
}

.amazon-tooltip:hover::before {
    content: '';
    position: absolute;
    bottom: calc(100% + 2px);
    left: 50%;
    transform: translateX(-50%);
    border: 6px solid transparent;
    border-top-color: #232F3E;
    z-index: 1001;
}

/* Help Text - Smaller */
.amazon-help-text {
    font-size: 11px;
    color: #565959;
    margin-top: 2px;
}

/* Section Divider - Minimal */
.amazon-section-divider {
    height: 1px;
    background: #E7E7E7;
    margin: 12px 0;
}

/* Location Checkboxes Grid - Fixed */
.amazon-locations-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 12px 20px;
    margin-top: 8px;
}

/* Submit Button with Hover Animation */
.amazon-btn-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 160px;
    height: 40px;
    padding: 0 20px;
    font-size: 14px;
    font-weight: 400;
    color: #0F1111;
    background: linear-gradient(to bottom, #FFD814 0%, #FF9900 100%);
    border: 1px solid #FCD200;
    border-radius: 8px;
    cursor: pointer;
    transition: all .2s ease;
    box-shadow: 0 2px 5px rgba(213,217,217,.5);
}

.amazon-btn-submit:hover {
    background: linear-gradient(to bottom, #F7CA00 0%, #E47911 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 153, 0, 0.4);
}

.amazon-btn-submit:active {
    transform: translateY(0) scale(0.98);
    box-shadow: 0 2px 5px rgba(213,217,217,.5);
}

/* Back/Cancel Button with Hover Animation */
.amazon-btn-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    font-size: 13px;
    color: #0F1111;
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border: 1px solid #D5D9D9;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s ease;
}

.amazon-btn-back:hover {
    background: linear-gradient(to bottom, #F7F8F8 0%, #E7E9EC 100%);
    text-decoration: none;
    color: #0F1111;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.amazon-btn-back:active {
    transform: translateY(0);
    box-shadow: none;
}

/* Input Group with Prefix */
.amazon-input-group {
    display: flex;
    align-items: stretch;
}

.amazon-input-group .amazon-form-control {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.amazon-input-group-prepend {
    display: flex;
    align-items: center;
    padding: 0 10px;
    font-size: 13px;
    font-weight: 600;
    color: #0F1111;
    background: #F0F2F2;
    border: 1px solid #888C8C;
    border-right: none;
    border-radius: 4px 0 0 4px;
    white-space: nowrap;
}

.amazon-input-group-addon {
    display: flex;
    align-items: center;
    padding: 0 10px;
    font-size: 13px;
    color: #0F1111;
    background: #F0F2F2;
    border: 1px solid #888C8C;
    border-left: none;
    border-radius: 0 4px 4px 0;
}

/* Conditional Fields */
.amazon-conditional-field.hide {
    display: none;
}

/* Form Actions */
.amazon-form-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    padding: 16px 0;
    margin-top: 4px;
}

/* Two Column Cards */
.amazon-cards-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

@media (max-width: 992px) {
    .amazon-cards-row {
        grid-template-columns: 1fr;
    }
}

/* Password field with toggle */
.amazon-password-wrapper {
    position: relative;
}

.amazon-password-toggle {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #007185;
    cursor: pointer;
    font-size: 11px;
    padding: 2px 4px;
}

.amazon-password-toggle:hover {
    color: #C7511F;
}

/* Scroll to Top Button - Enhanced */
.scroll-to-top,
#scroll-top,
.back-to-top,
a[href="#top"] {
    position: fixed !important;
    bottom: 30px !important;
    right: 30px !important;
    width: 44px !important;
    height: 44px !important;
    background: linear-gradient(135deg, #FF9900 0%, #E47911 100%) !important;
    border: none !important;
    border-radius: 50% !important;
    color: white !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 4px 12px rgba(255, 153, 0, 0.4) !important;
    transition: all 0.3s ease !important;
    z-index: 9999 !important;
}

.scroll-to-top:hover,
#scroll-top:hover,
.back-to-top:hover,
a[href="#top"]:hover {
    transform: translateY(-4px) !important;
    box-shadow: 0 6px 20px rgba(255, 153, 0, 0.5) !important;
    background: linear-gradient(135deg, #FFB84D 0%, #FF9900 100%) !important;
}

.scroll-to-top:active,
#scroll-top:active,
.back-to-top:active,
a[href="#top"]:active {
    transform: translateY(-2px) !important;
}

/* Staff PIN inline field */
.staff-pin-inline {
    display: flex;
    align-items: center;
    gap: 12px;
}

.staff-pin-input-wrapper {
    width: 140px;
}

/* Responsive */
@media (max-width: 768px) {
    .amazon-user-container {
        padding: 10px 12px;
    }
    
    .amazon-page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .amazon-form-actions {
        flex-direction: column;
    }
    
    .amazon-btn-submit,
    .amazon-btn-back {
        width: 100%;
        justify-content: center;
    }
    
    .staff-pin-inline {
        flex-wrap: wrap;
    }
}

/* Override iCheck */
.amazon-card .icheckbox_square-blue,
.amazon-card .iradio_square-blue {
    display: none !important;
}
</style>
@endsection

@section('content')
<div class="amazon-user-container">
    <!-- Page Header -->
    <div class="amazon-page-header">
        <div>
            <h1 class="amazon-page-title">@lang( 'user.edit_user' )</h1>
            <div class="amazon-breadcrumb">
                <a href="{{ action([\App\Http\Controllers\ManageUserController::class, 'index']) }}">Users</a>
                <span> › </span>
                <span>Edit User</span>
            </div>
        </div>
        <a href="{{ action([\App\Http\Controllers\ManageUserController::class, 'index']) }}" class="amazon-btn-back">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Users
        </a>
    </div>

    {!! Form::open(['url' => action([\App\Http\Controllers\ManageUserController::class, 'update'], [$user->id]), 'method' => 'PUT', 'id' => 'user_edit_form']) !!}

    <!-- Basic Information Card -->
    <div class="amazon-card">
        <div class="amazon-card-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4"></circle>
                <path d="M20 21a8 8 0 1 0-16 0"></path>
            </svg>
            Basic Information
                </div>
        <div class="amazon-card-body">
            <div class="amazon-form-row">
                <div class="amazon-form-col-sm">
                    <div class="amazon-form-group">
                        <label for="surname">{{ __('business.prefix') }}</label>
                        <select name="surname" id="surname" class="amazon-form-control">
                            <option value="">Select</option>
                            <option value="Mr" {{ $user->surname == 'Mr' ? 'selected' : '' }}>Mr</option>
                            <option value="Mrs" {{ $user->surname == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                            <option value="Ms" {{ $user->surname == 'Ms' ? 'selected' : '' }}>Ms</option>
                            <option value="Miss" {{ $user->surname == 'Miss' ? 'selected' : '' }}>Miss</option>
                            <option value="Dr" {{ $user->surname == 'Dr' ? 'selected' : '' }}>Dr</option>
                        </select>
            </div>
                </div>
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="first_name">{{ __('business.first_name') }} <span class="required">*</span></label>
                        {!! Form::text('first_name', $user->first_name, ['class' => 'amazon-form-control', 'id' => 'first_name', 'required', 'placeholder' => 'Enter first name']) !!}
            </div>
                </div>
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="last_name">{{ __('business.last_name') }}</label>
                        {!! Form::text('last_name', $user->last_name, ['class' => 'amazon-form-control', 'id' => 'last_name', 'placeholder' => 'Enter last name']) !!}
            </div>
                </div>
            </div>

            <div class="amazon-form-row">
                <div class="amazon-form-col-email">
                    <div class="amazon-form-group">
                        <label for="email">{{ __('business.email') }} <span class="required">*</span></label>
                        {!! Form::email('email', $user->email, ['class' => 'amazon-form-control', 'id' => 'email', 'required', 'placeholder' => 'email@example.com']) !!}
                  </div>
                </div>
                <div class="amazon-form-col-toggle">
                    <div class="amazon-form-group">
                        <label>&nbsp;</label>
                        <label class="amazon-toggle-inline">
                            <span class="amazon-toggle">
                                {!! Form::checkbox('is_active', $user->status, $is_checked_checkbox, ['id' => 'is_active']) !!}
                                <span class="amazon-toggle-slider"></span>
                            </span>
                            <span class="amazon-toggle-label">Is Active?</span>
                            <span class="amazon-tooltip" data-tip="{{ __('lang_v1.tooltip_enable_user_active') }}">i</span>
                        </label>
            </div>
                </div>
                <div class="amazon-form-col-toggle">
                    <div class="amazon-form-group">
                        <label>&nbsp;</label>
                        <div class="staff-pin-inline">
                            <label class="amazon-toggle-inline">
                                <span class="amazon-toggle">
                                    {!! Form::checkbox('is_enable_service_staff_pin', 1, $user->is_enable_service_staff_pin, ['id' => 'is_enable_service_staff_pin']) !!}
                                    <span class="amazon-toggle-slider"></span>
                                </span>
                                <span class="amazon-toggle-label">Staff PIN</span>
                                <span class="amazon-tooltip" data-tip="{{ __('lang_v1.tooltip_is_enable_service_staff_pin') }}">i</span>
                            </label>
                            <div class="staff-pin-input-wrapper service_staff_pin_div {{ $user->is_enable_service_staff_pin == 1 ? '' : 'hide' }}">
                                {!! Form::password('service_staff_pin', ['class' => 'amazon-form-control', 'id' => 'service_staff_pin', 'placeholder' => 'Enter PIN']) !!}
                </div>
              </div>
            </div>
              </div>
            </div>
        </div>
    </div>

    <!-- Roles and Permissions Card -->
    <div class="amazon-card">
        <div class="amazon-card-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <path d="M9 12l2 2 4-4"></path>
            </svg>
            @lang('lang_v1.roles_and_permissions')
        </div>
        <div class="amazon-card-body">
            <div class="amazon-form-row">
                <div class="amazon-form-col-md">
                    <label class="amazon-toggle-inline">
                        <span class="amazon-toggle">
                            {!! Form::checkbox('allow_login', 1, !empty($user->allow_login), ['id' => 'allow_login']) !!}
                            <span class="amazon-toggle-slider"></span>
                        </span>
                        <span class="amazon-toggle-label">{{ __('lang_v1.allow_login') }}</span>
                      </label>
                </div>
            </div>

            <div class="user_auth_fields @if(empty($user->allow_login)) hide @endif">
                <div class="amazon-section-divider"></div>
            @if(empty($user->allow_login))
                <div class="amazon-form-row" style="display: flex; flex-wrap: nowrap; gap: 12px; align-items: flex-start;">
                    <div style="flex: 1; min-width: 0;">
                        <div class="amazon-form-group">
                            <label for="username">{{ __('business.username') }}</label>
                      @if(!empty($username_ext))
                                <div class="amazon-input-group">
                                    {!! Form::text('username', null, ['class' => 'amazon-form-control', 'id' => 'username', 'placeholder' => 'Enter username']) !!}
                                    <span class="amazon-input-group-addon">{{ $username_ext }}</span>
                                </div>
                                <p class="amazon-help-text" id="show_username"></p>
                            @else
                                {!! Form::text('username', null, ['class' => 'amazon-form-control', 'id' => 'username', 'placeholder' => 'Enter username']) !!}
                            @endif
                            <p class="amazon-help-text">@lang('lang_v1.username_help')</p>
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="amazon-form-group">
                            <label for="password">{{ __('business.password') }}</label>
                            <div class="amazon-password-wrapper">
                                {!! Form::password('password', ['class' => 'amazon-form-control', 'id' => 'password', 'placeholder' => 'Min 5 characters']) !!}
                                <button type="button" class="amazon-password-toggle" onclick="togglePassword('password')">Show</button>
                </div>
                            <p class="amazon-help-text">@lang('user.leave_password_blank')</p>
                </div>
            </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="amazon-form-group">
                            <label for="confirm_password">{{ __('business.confirm_password') }}</label>
                            <div class="amazon-password-wrapper">
                                {!! Form::password('confirm_password', ['class' => 'amazon-form-control', 'id' => 'confirm_password', 'placeholder' => 'Confirm password']) !!}
                                <button type="button" class="amazon-password-toggle" onclick="togglePassword('confirm_password')">Show</button>
                </div>
            </div>
            </div>
                </div>
                @else
                <div class="amazon-form-row" style="display: flex; flex-wrap: nowrap; gap: 12px; align-items: flex-start;">
                    <div style="flex: 1; min-width: 0;">
                        <div class="amazon-form-group">
                            <label for="password">{{ __('business.password') }}</label>
                            <div class="amazon-password-wrapper">
                                {!! Form::password('password', ['class' => 'amazon-form-control', 'id' => 'password', 'placeholder' => 'Min 5 characters']) !!}
                                <button type="button" class="amazon-password-toggle" onclick="togglePassword('password')">Show</button>
            </div>
                            <p class="amazon-help-text">@lang('user.leave_password_blank')</p>
            </div>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="amazon-form-group">
                            <label for="confirm_password">{{ __('business.confirm_password') }}</label>
                            <div class="amazon-password-wrapper">
                                {!! Form::password('confirm_password', ['class' => 'amazon-form-control', 'id' => 'confirm_password', 'placeholder' => 'Confirm password']) !!}
                                <button type="button" class="amazon-password-toggle" onclick="togglePassword('confirm_password')">Show</button>
                    </div>
                  </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="amazon-section-divider"></div>

            <div class="amazon-form-row" style="display: flex; flex-wrap: nowrap; gap: 12px; align-items: flex-start;">
                <div style="flex: 0 0 40%; min-width: 250px;">
                    <div class="amazon-form-group">
                        <label for="role">{{ __('user.role') }} <span class="required">*</span>
                            <span class="amazon-tooltip" data-tip="{{ __('lang_v1.admin_role_location_permission_help') }}">i</span>
                      </label>
                        {!! Form::select('role', $roles, !empty($user->roles->first()->id) ? $user->roles->first()->id : null, ['class' => 'amazon-form-control select2', 'id' => 'role', 'style' => 'width: 100%;', 'placeholder' => 'Please select Role']) !!}
                    </div>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div class="amazon-form-group">
                        <label>@lang('role.access_locations')
                            <span class="amazon-tooltip" data-tip="{{ __('tooltip.access_locations_permission') }}">i</span>
                        </label>
                        <div style="margin-top: 6px; display: flex; flex-wrap: wrap; gap: 8px 16px;">
                            <label class="amazon-checkbox-wrapper" style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                                <input type="checkbox" name="access_all_locations" value="access_all_locations" id="access_all_locations" {{ !is_array($permitted_locations) && $permitted_locations == 'all' ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: #FF9900; cursor: pointer;">
                                <span style="font-size: 13px; color: #0F1111;">{{ __('role.all_locations') }}</span>
                            </label>
                            @foreach($locations as $location)
                            <label class="amazon-checkbox-wrapper" style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                                <input type="checkbox" name="location_permissions[]" value="location.{{ $location->id }}" {{ is_array($permitted_locations) && in_array($location->id, $permitted_locations) ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: #FF9900; cursor: pointer;">
                                <span style="font-size: 13px; color: #0F1111;">{{ $location->name }}@if(!empty($location->location_id)) <span style="color:#888;">({{ $location->location_id }})</span>@endif</span>
                            </label>
                            @endforeach
            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact & Personal Information -->
    @include('manage_user.partials.amazon_profile_form', ['bank_details' => !empty($user->bank_details) ? json_decode($user->bank_details, true) : null])

    @if(!empty($form_partials))
      @foreach($form_partials as $partial)
        {!! $partial !!}
      @endforeach
    @endif

    <!-- Form Actions -->
    <div class="amazon-form-actions">
        <a href="{{ action([\App\Http\Controllers\ManageUserController::class, 'index']) }}" class="amazon-btn-back" style="height: 40px; padding: 0 20px;">
            Cancel
        </a>
        <button type="submit" class="amazon-btn-submit" id="submit_user_button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            @lang('messages.update')
        </button>
    </div>

    {!! Form::close() !!}
</div>
  @stop

@section('javascript')
<script type="text/javascript">
  $(document).ready(function(){
    __page_leave_confirmation('#user_edit_form');
    
    // Toggle for allow login
    $('#allow_login').on('change', function(){
        if($(this).is(':checked')) {
            $('div.user_auth_fields').removeClass('hide').show();
        } else {
            $('div.user_auth_fields').addClass('hide').hide();
        }
    });

    // Toggle for service staff pin
    $('#is_enable_service_staff_pin').on('change', function(){
        if($(this).is(':checked')) {
            $('.service_staff_pin_div').removeClass('hide').show();
        } else {
            $('.service_staff_pin_div').addClass('hide').hide();
      $('#service_staff_pin').val('');
        }
    });

    // Select2 for user allowed contacts
    $('#user_allowed_contacts').select2({
        ajax: {
            url: '/contacts/customers',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page,
                    all_contact: true
                };
            },
            processResults: function(data) {
                return { results: data };
            },
        },
        templateResult: function (data) { 
            var template = '';
            if (data.supplier_business_name) {
                template += data.supplier_business_name + "<br>";
            }
            template += data.text + "<br>" + LANG.mobile + ": " + data.mobile;
            return template;
        },
        minimumInputLength: 1,
        escapeMarkup: function(markup) { return markup; },
    });

    // Mobile number - only allow numbers
    $('#contact_number, #alt_number, #family_number').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
  });

// Password toggle function
function togglePassword(fieldId) {
    var field = document.getElementById(fieldId);
    var btn = field.parentElement.querySelector('.amazon-password-toggle');
    if (field.type === 'password') {
        field.type = 'text';
        btn.textContent = 'Hide';
    } else {
        field.type = 'password';
        btn.textContent = 'Show';
    }
}

// Form validation
  $('form#user_edit_form').validate({
                rules: {
        first_name: { required: true },
                    email: {
                        email: true,
                        remote: {
                            url: "/business/register/check-email",
                            type: "post",
                            data: {
                    email: function() { return $("#email").val(); },
                                user_id: {{$user->id}}
                            }
                        }
                    },
        password: { minlength: 5 },
        confirm_password: { equalTo: "#password" },
                    username: {
                        minlength: 5,
                        remote: {
                            url: "/business/register/check-username",
                            type: "post",
                            data: {
                    username: function() { return $("#username").val(); },
                                @if(!empty($username_ext))
                                  username_ext: "{{$username_ext}}"
                                @endif
                            }
                        }
                    }
                },
                messages: {
        password: { minlength: 'Password should be minimum 5 characters' },
        confirm_password: { equalTo: 'Should be same as password' },
        username: { remote: 'Invalid username or User already exist' },
        email: { remote: '{{ __("validation.unique", ["attribute" => __("business.email")]) }}' }
    },
    errorClass: 'amazon-error',
    errorElement: 'span',
    highlight: function(element) { $(element).css('border-color', '#C40000'); },
    unhighlight: function(element) { $(element).css('border-color', '#888C8C'); }
            });
</script>
@endsection
