@extends('layouts.app')

@section('title', __('subscription::lang.subscription_settings'))

@section('content')
<style>
    .settings-page {
        background: #f3f3f3;
        min-height: calc(100vh - 120px);
        padding: 20px 0;
    }
    
    .page-header-card {
        background: linear-gradient(90deg, #232f3e 0%, #37475a 100%);
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }
    
    .page-header-card h1 {
        color: #fff;
        font-size: 26px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    
    .page-header-card h1 .icon-box {
        background: rgba(0,0,0,0.25);
        border-radius: 12px;
        padding: 10px;
        display: flex;
    }
    
    .page-header-card .subtitle {
        color: rgba(255,255,255,0.8);
        margin-top: 8px;
        font-size: 14px;
    }
    
    .settings-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .settings-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
        border-bottom: 2px solid #f0f0f0;
        padding: 18px 24px;
    }
    
    .settings-card .card-header h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .settings-card .card-header h4 i {
        color: #00a8e1;
        font-size: 18px;
    }
    
    .settings-card .card-body {
        padding: 24px;
    }
    
    .setting-group {
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .setting-group:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .setting-group h5 {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .setting-group h5 i {
        color: #00a8e1;
    }
    
    .form-group {
        margin-bottom: 16px;
    }
    
    .form-group label {
        font-size: 13px;
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
        display: block;
    }
    
    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid #e8e8e8;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.2s ease;
        background-color: #fff !important;
        color: #131921 !important;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #00a8e1;
        box-shadow: 0 0 0 4px rgba(0, 168, 225, 0.15);
    }
    
    /* Settings dropdowns - Amazon-style light appearance */
    .settings-page .form-select,
    .settings-page .form-select option {
        background-color: #fff !important;
        color: #131921 !important;
    }
    
    .settings-status-dropdown.select2-dropdown,
    .settings-status-dropdown {
        background-color: #fff !important;
        border: 1px solid #e8e8e8 !important;
        border-radius: 10px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1) !important;
    }
    .settings-status-dropdown .select2-results__option {
        background-color: #fff !important;
        color: #131921 !important;
        padding: 10px 16px;
    }
    .settings-status-dropdown .select2-results__option--highlighted[aria-selected] {
        background-color: #FFF3E0 !important;
        color: #131921 !important;
    }
    .settings-status-dropdown .select2-results__option[aria-selected=true] {
        background-color: #FFF8F0 !important;
        color: #e88b00 !important;
    }
    .settings-page .select2-container--default .select2-selection--single {
        background-color: #fff !important;
        border: 2px solid #e8e8e8;
        border-radius: 10px;
    }
    .settings-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #131921 !important;
    }
    
    .form-text {
        font-size: 12px;
        color: #888;
        margin-top: 6px;
    }
    
    /* Toggle Switch */
    .toggle-setting {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 12px;
    }
    
    .toggle-setting .toggle-info {
        flex: 1;
    }
    
    .toggle-setting .toggle-info label {
        font-size: 14px;
        font-weight: 500;
        color: #333;
        margin: 0;
        display: block;
    }
    
    .toggle-setting .toggle-info small {
        font-size: 12px;
        color: #888;
    }
    
    .switch {
        position: relative;
        width: 50px;
        height: 26px;
        flex-shrink: 0;
    }
    
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 26px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background: linear-gradient(135deg, #00a8e1, #0077a3);
    }
    
    input:checked + .slider:before {
        transform: translateX(24px);
    }
    
    /* Submit Button */
    .btn-submit {
        background: linear-gradient(90deg, #00a8e1 0%, #0077a3 100%);
        border: none;
        color: #fff;
        padding: 14px 32px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 168, 225, 0.45);
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 168, 225, 0.55);
        color: #fff;
    }
    
    /* Alert Info */
    .info-alert {
        background: linear-gradient(135deg, #e3f2fd, #f5f5f5);
        border-left: 4px solid #2196f3;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 20px;
    }
    
    .info-alert i {
        color: #2196f3;
        margin-right: 10px;
    }
</style>

<div class="settings-page">
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="page-header-card">
            <h1>
                <div class="icon-box">
                    <i class="fas fa-cog"></i>
                </div>
                Subscription Settings
            </h1>
            <div class="subtitle">Configure subscription module behavior and defaults</div>
        </div>

        <form action="{{ route('subscription.settings.update') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-lg-8">
                    {{-- Customer Group Settings --}}
                    <div class="settings-card">
                        <div class="card-header">
                            <h4><i class="fas fa-users"></i> Customer Group Settings</h4>
                        </div>
                        <div class="card-body">
                            <div class="info-alert">
                                <i class="fas fa-info-circle"></i>
                                These settings control how customer groups are managed when subscriptions are activated or cancelled.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Default Customer Group (On Expiry)</label>
                                        <select name="default_customer_group_id" id="default_customer_group_id" class="form-select select2">
                                            @foreach($customer_groups as $id => $name)
                                                <option value="{{ $id }}" {{ ($settings['default_customer_group_id'] ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Customer will be assigned to this group when their subscription expires or is cancelled.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Default Selling Price Group (On Expiry)</label>
                                        <select name="default_selling_price_group_id" id="default_selling_price_group_id" class="form-select select2">
                                            @foreach($selling_price_groups as $id => $name)
                                                <option value="{{ $id }}" {{ ($settings['default_selling_price_group_id'] ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Default selling price group when subscription expires.</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="toggle-setting">
                                <div class="toggle-info">
                                    <label>Auto-Update Customer Group on Activation</label>
                                    <small>Automatically update customer's group based on the subscription plan</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="auto_update_customer_group" value="1" {{ ($settings['auto_update_customer_group'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="toggle-setting">
                                <div class="toggle-info">
                                    <label>Auto-Revert Customer Group on Expiry/Cancel</label>
                                    <small>Automatically revert customer's group when subscription ends</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="auto_revert_customer_group" value="1" {{ ($settings['auto_revert_customer_group'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Plan Change Settings --}}
                    <div class="settings-card">
                        <div class="card-header">
                            <h4><i class="fas fa-exchange-alt"></i> Plan Change Settings</h4>
                        </div>
                        <div class="card-body">
                            <div class="toggle-setting">
                                <div class="toggle-info">
                                    <label>Allow Plan Upgrades</label>
                                    <small>Allow customers to upgrade to higher-tier plans</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="allow_plan_upgrades" value="1" {{ ($settings['allow_plan_upgrades'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="toggle-setting">
                                <div class="toggle-info">
                                    <label>Allow Plan Downgrades</label>
                                    <small>Allow customers to downgrade to lower-tier plans</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="allow_plan_downgrades" value="1" {{ ($settings['allow_plan_downgrades'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="toggle-setting">
                                <div class="toggle-info">
                                    <label>Prorate Plan Changes</label>
                                    <small>Calculate prorated amounts when changing plans mid-cycle</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="prorate_plan_changes" value="1" {{ ($settings['prorate_plan_changes'] ?? false) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Renewal & Expiry Settings --}}
                    <div class="settings-card">
                        <div class="card-header">
                            <h4><i class="fas fa-calendar-alt"></i> Renewal & Expiry Settings</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Renewal Reminder Days</label>
                                        <input type="number" name="renewal_reminder_days" class="form-control" 
                                               value="{{ $settings['renewal_reminder_days'] ?? 7 }}" min="1" max="30">
                                        <div class="form-text">Days before expiry to send renewal reminders</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Grace Period Days</label>
                                        <input type="number" name="grace_period_days" class="form-control" 
                                               value="{{ $settings['grace_period_days'] ?? 3 }}" min="0" max="30">
                                        <div class="form-text">Days after expiry before benefits are revoked</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="toggle-setting">
                                <div class="toggle-info">
                                    <label>Auto-Expire Subscriptions</label>
                                    <small>Automatically mark subscriptions as expired when they reach their end date</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="auto_expire_subscriptions" value="1" {{ ($settings['auto_expire_subscriptions'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    {{-- Payment Gateway Settings --}}
                    <div class="settings-card">
                        <div class="card-header">
                            <h4><i class="fas fa-credit-card"></i> Payment Gateway Settings</h4>
                        </div>
                        <div class="card-body">
                            <div class="toggle-setting" style="background: {{ ($settings['payment_demo_mode'] ?? false) ? 'linear-gradient(135deg, #fff3e0, #ffe0b2)' : '#f8f9fa' }}; border: {{ ($settings['payment_demo_mode'] ?? false) ? '2px solid #ff9800' : 'none' }};">
                                <div class="toggle-info">
                                    <label style="{{ ($settings['payment_demo_mode'] ?? false) ? 'color: #e65100;' : '' }}">
                                        <i class="fas fa-flask" style="margin-right: 5px;"></i> Demo Mode
                                    </label>
                                    <small>Skip actual payment gateway charges for testing</small>
                                    @if($settings['payment_demo_mode'] ?? false)
                                        <div style="margin-top: 6px; padding: 4px 8px; background: #fff; border-radius: 4px; display: inline-block;">
                                            <span style="color: #e65100; font-weight: 600; font-size: 11px;">
                                                <i class="fas fa-exclamation-triangle"></i> DEMO MODE ACTIVE
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="payment_demo_mode" value="1" {{ ($settings['payment_demo_mode'] ?? false) ? 'checked' : '' }}>
                                    <span class="slider" style="{{ ($settings['payment_demo_mode'] ?? false) ? 'background: linear-gradient(135deg, #ff9800, #f57c00);' : '' }}"></span>
                                </label>
                            </div>
                            
                            <div class="info-alert" style="background: linear-gradient(135deg, #fff8e1, #fff3e0); border-left-color: #ff9800; margin-top: 16px;">
                                <i class="fas fa-info-circle" style="color: #ff9800;"></i>
                                <strong>Demo Mode:</strong> When enabled, the payment form will still appear and tokenize cards via NMI, but no actual charges will be processed. Perfect for testing the subscription flow.
                            </div>
                            
                            <div class="toggle-setting">
                                <div class="toggle-info">
                                    <label>Auto-Retry Failed Payments</label>
                                    <small>Automatically retry failed recurring payments</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="auto_retry_payments" value="1" {{ ($settings['auto_retry_payments'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="form-group" style="margin-top: 12px;">
                                <label>Payment Retry Attempts</label>
                                <input type="number" name="payment_retry_attempts" class="form-control" 
                                       value="{{ $settings['payment_retry_attempts'] ?? 3 }}" min="1" max="5">
                                <div class="form-text">Number of times to retry failed payments</div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Email Notifications --}}
                    <div class="settings-card">
                        <div class="card-header">
                            <h4><i class="fas fa-envelope"></i> Email Notifications</h4>
                        </div>
                        <div class="card-body">
                            <div class="toggle-setting">
                                <div class="toggle-info">
                                    <label>Activation Email</label>
                                    <small>Send email when subscription is activated</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="send_activation_email" value="1" {{ ($settings['send_activation_email'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="toggle-setting">
                                <div class="toggle-info">
                                    <label>Renewal Reminder Email</label>
                                    <small>Send email before subscription expires</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="send_renewal_email" value="1" {{ ($settings['send_renewal_email'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="toggle-setting">
                                <div class="toggle-info">
                                    <label>Expiry Email</label>
                                    <small>Send email when subscription expires</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="send_expiry_email" value="1" {{ ($settings['send_expiry_email'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="toggle-setting">
                                <div class="toggle-info">
                                    <label>Cancellation Email</label>
                                    <small>Send email when subscription is cancelled</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="send_cancellation_email" value="1" {{ ($settings['send_cancellation_email'] ?? true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <div class="settings-card">
                        <div class="card-body text-center">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                            <div class="form-text mt-3">Settings will apply to all new and existing subscriptions.</div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Settings dropdowns - light Amazon-style
    if ($.fn.select2) {
        ['#default_customer_group_id', '#default_selling_price_group_id'].forEach(function(id) {
            var $el = $(id);
            if ($el.length) {
                try {
                    if ($el.data('select2')) $el.select2('destroy');
                    $el.select2({ dropdownCssClass: 'settings-status-dropdown' });
                } catch (e) {
                    $el.select2({ dropdownCssClass: 'settings-status-dropdown' });
                }
            }
        });
    }
});
</script>
@endsection
