@extends('layouts.app')
@section('title', 'Add Dropship Vendor')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .dropship-vendor-create-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
    .adv-header-banner {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 0 0 10px 10px;
        padding: 22px 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }
    .adv-header-banner.amazon-theme-banner::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #ff9900; z-index: 1; }
    .adv-header-banner .banner-content { display: flex; flex-direction: column; gap: 4px; }
    .adv-header-banner .banner-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; margin: 0; color: #fff !important; }
    .adv-header-banner .banner-title i { color: #fff !important; }
    .adv-header-banner .banner-subtitle { font-size: 13px; color: rgba(255,255,255,0.9) !important; margin: 4px 0 0 0; }
    .amazon-orange-btn { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 2px solid #C7511F !important; color: #fff !important; font-weight: 600; padding: 10px 24px; border-radius: 6px; }
    .amazon-orange-btn:hover { color: #fff !important; opacity: 0.95; }
    /* Amazon-style section cards */
    .dropship-vendor-create-page .box-primary,
    .dropship-vendor-create-page .box-info,
    .dropship-vendor-create-page .box-warning,
    .dropship-vendor-create-page .box-success,
    .dropship-vendor-create-page .box-default {
        border-radius: 10px; overflow: hidden; border: 1px solid #D5D9D9; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;
    }
    .dropship-vendor-create-page .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important; color: #fff !important; border: none !important; padding: 14px 20px !important; position: relative;
    }
    .dropship-vendor-create-page .box-header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900; }
    .dropship-vendor-create-page .box-title { color: #fff !important; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .dropship-vendor-create-page .box-title i.fas,
    .dropship-vendor-create-page .box-title .card-icon { color: #fff; font-size: 1rem; }
    #vendor-info-card .box-title::before { content: '\f007'; font-family: 'Font Awesome 5 Free'; font-weight: 900; margin-right: 6px; }
    #pricing-commission-card .box-title::before { content: '\f295'; font-family: 'Font Awesome 5 Free'; font-weight: 900; margin-right: 6px; }
    #payment-settings-card .box-title::before { content: '\f09d'; font-family: 'Font Awesome 5 Free'; font-weight: 900; margin-right: 6px; }
    #woocommerce-card .box-title::before { content: '\f19a'; font-family: 'Font Awesome 5 Brands'; margin-right: 6px; }
    #portal-access-card .box-title::before { content: '\f084'; font-family: 'Font Awesome 5 Free'; font-weight: 900; margin-right: 6px; }
    #notes-card .box-title::before { content: '\f249'; font-family: 'Font Awesome 5 Free'; font-weight: 900; margin-right: 6px; }
    .dropship-vendor-create-page .tw-flow-root { background: #f7f8f8 !important; padding: 1.25rem 1.5rem !important; }
    .dropship-vendor-create-page .tw-flow-root .form-group { margin-bottom: 0.75rem; }
    .dropship-vendor-create-page .tw-flow-root label { color: #0F1111 !important; }
    .dropship-vendor-create-page .tw-flow-root .form-control { background: #fff; border: 1px solid #D5D9D9; }
    .dropship-vendor-create-page .tw-flow-root .form-control:focus { border-color: #FF9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2); }
    .dropship-vendor-create-page .tw-flow-root .help-block { color: #565959; font-size: 0.8125rem; }
</style>
@endsection

@section('content')
{!! Form::open(['route' => 'dropship.vendors.store', 'method' => 'POST', 'id' => 'vendor-form']) !!}
<section class="content-header">
    <div class="adv-header-banner amazon-theme-banner">
        <div class="banner-content">
            <h1 class="banner-title"><i class="fas fa-user-plus"></i> Add Dropship Vendor</h1>
            <p class="banner-subtitle">Create a new dropship vendor with portal access and product mapping.</p>
        </div>
        <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-sm tw-text-white amazon-orange-btn">
            <i class="fas fa-save"></i> @lang('messages.save')
        </button>
    </div>
</section>

<section class="content dropship-vendor-create-page">
    <div class="row">
        <div class="col-lg-8">
            @component('components.widget', ['class' => 'box-primary', 'title' => 'Vendor Information', 'id' => 'vendor-info-card'])
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('name', 'Vendor Name *') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => 'Enter vendor name']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('company_name', 'Company Name') !!}
                            {!! Form::text('company_name', null, ['class' => 'form-control', 'placeholder' => 'Enter company name']) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('vendor_type', 'Vendor Type *') !!}
                            {!! Form::select('vendor_type', $vendorTypes, 'erp_dropship', ['class' => 'form-control', 'required', 'id' => 'vendor_type']) !!}
                            <p class="help-block">
                                <strong>ERP Vendor:</strong> Normal internal vendor for accounting/supplier purposes.<br>
                                <strong>ERP Dropship Vendor:</strong> Third-party vendors who will use the Vendor Portal to manage and fulfill dropship orders.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('email', 'Email') !!}
                            {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'vendor@example.com']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('phone', 'Phone') !!}
                            {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => '+1 234 567 890']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('address', 'Address') !!}
                    {!! Form::textarea('address', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Enter full address']) !!}
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('contact_id', 'Link to Supplier Contact') !!}
                            {!! Form::select('contact_id', $suppliers, null, ['class' => 'form-control select2', 'placeholder' => 'Select supplier (optional)']) !!}
                            <p class="help-block">Link this vendor to an existing supplier for accounting purposes</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('status', 'Status *') !!}
                            {!! Form::select('status', [
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'pending' => 'Pending'
                            ], 'active', ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-info', 'title' => 'Pricing & Commission', 'id' => 'pricing-commission-card'])
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('margin_percentage', 'Margin Percentage (%)') !!}
                            {!! Form::number('margin_percentage', 0, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'max' => '100']) !!}
                            <p class="help-block">Go Hunter's margin on vendor products</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('default_markup_percentage', 'Default Markup (%)') !!}
                            {!! Form::number('default_markup_percentage', 0, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) !!}
                            <p class="help-block">Default markup for new product mappings</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('commission_type', 'Commission Type *') !!}
                            {!! Form::select('commission_type', [
                                'percentage' => 'Percentage',
                                'fixed' => 'Fixed Amount'
                            ], 'percentage', ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('commission_value', 'Commission Value') !!}
                            {!! Form::number('commission_value', 0, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) !!}
                        </div>
                    </div>
                </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-warning', 'title' => 'Payment Settings', 'id' => 'payment-settings-card'])
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('payment_terms', 'Payment Terms *') !!}
                            {!! Form::select('payment_terms', [
                                'immediate' => 'Immediate',
                                'weekly' => 'Weekly',
                                'biweekly' => 'Bi-Weekly',
                                'monthly' => 'Monthly'
                            ], 'monthly', ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('payment_method', 'Preferred Payment Method') !!}
                            {!! Form::text('payment_method', null, ['class' => 'form-control', 'placeholder' => 'e.g., Bank Transfer, PayPal']) !!}
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>

        <div class="col-lg-4">
            <div id="woocommerce-section" style="display: none;">
                @component('components.widget', ['class' => 'box-success', 'title' => 'WooCommerce Integration', 'id' => 'woocommerce-card'])
                    <div class="form-group">
                        {!! Form::label('wp_term_id', 'WooCommerce Vendor Term ID') !!}
                        {!! Form::number('wp_term_id', null, ['class' => 'form-control', 'placeholder' => 'e.g., 123']) !!}
                        <p class="help-block">The vendor taxonomy term ID from WooCommerce (only for testing/debugging)</p>
                    </div>
                @endcomponent
            </div>

            <div id="portal-access-section">
                @component('components.widget', ['class' => 'box-info', 'title' => 'Portal Access', 'id' => 'portal-access-card'])
                    <div class="alert alert-info" id="dropship-vendor-info" style="display: none;">
                        <i class="fas fa-info-circle"></i> <strong>ERP Dropship Vendors</strong> will automatically get a portal user account created so they can manage and fulfill orders through the Vendor Portal.
                    </div>
                    <div class="form-group" id="manual-portal-checkbox">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('create_portal_user', 1, false, ['class' => 'input-icheck', 'id' => 'create_portal_user']) !!}
                                Create vendor portal user
                            </label>
                        </div>
                        <p class="help-block">If checked, a user account will be created for the vendor to access the portal. Email is required.</p>
                    </div>
                    
                    {{-- Password Fields for Portal Access --}}
                    <div id="password-fields" style="display: none;">
                        <hr>
                        <div class="form-group">
                            {!! Form::label('password', 'Portal Password *') !!}
                            {!! Form::password('password', ['class' => 'form-control', 'id' => 'vendor_password', 'placeholder' => 'Enter password', 'minlength' => '6']) !!}
                            <p class="help-block">Minimum 6 characters. This will be the vendor's login password.</p>
                        </div>
                        <div class="form-group">
                            {!! Form::label('password_confirmation', 'Confirm Password *') !!}
                            {!! Form::password('password_confirmation', ['class' => 'form-control', 'id' => 'vendor_password_confirmation', 'placeholder' => 'Re-enter password', 'minlength' => '6']) !!}
                            <p class="help-block text-danger" id="password-match-error" style="display: none;">
                                <i class="fas fa-exclamation-circle"></i> Passwords do not match!
                            </p>
                            <p class="help-block text-success" id="password-match-success" style="display: none;">
                                <i class="fas fa-check-circle"></i> Passwords match!
                            </p>
                        </div>
                    </div>
                @endcomponent
            </div>

            @component('components.widget', ['class' => 'box-default', 'title' => 'Notes', 'id' => 'notes-card'])
                <div class="form-group">
                    {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Internal notes about this vendor...']) !!}
                </div>
            @endcomponent
        </div>
    </div>

    {!! Form::close() !!}
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('.select2').select2();
    
    $('input.input-icheck').iCheck({
        checkboxClass: 'icheckbox_square-blue'
    });

    // Handle vendor type change
    function updateVendorTypeUI() {
        var vendorType = $('#vendor_type').val();
        
        if (vendorType === 'erp_dropship') {
            $('#dropship-vendor-info').show();
            $('#manual-portal-checkbox').hide();
            $('#woocommerce-section').hide();
            $('#password-fields').show();
            // Make password required for erp_dropship
            $('#vendor_password').attr('required', true);
            $('#vendor_password_confirmation').attr('required', true);
        } else if (vendorType === 'erp') {
            $('#dropship-vendor-info').hide();
            $('#manual-portal-checkbox').show();
            $('#woocommerce-section').hide();
            updatePasswordFieldsVisibility();
        }
    }

    // Show/hide password fields based on checkbox
    function updatePasswordFieldsVisibility() {
        var vendorType = $('#vendor_type').val();
        if (vendorType === 'erp_dropship') {
            $('#password-fields').show();
            $('#vendor_password').attr('required', true);
            $('#vendor_password_confirmation').attr('required', true);
        } else if ($('#create_portal_user').is(':checked')) {
            $('#password-fields').show();
            $('#vendor_password').attr('required', true);
            $('#vendor_password_confirmation').attr('required', true);
        } else {
            $('#password-fields').hide();
            $('#vendor_password').attr('required', false);
            $('#vendor_password_confirmation').attr('required', false);
        }
    }

    // Password match validation
    function validatePasswords() {
        var password = $('#vendor_password').val();
        var confirmPassword = $('#vendor_password_confirmation').val();
        
        if (confirmPassword.length > 0) {
            if (password === confirmPassword) {
                $('#password-match-error').hide();
                $('#password-match-success').show();
                return true;
            } else {
                $('#password-match-error').show();
                $('#password-match-success').hide();
                return false;
            }
        } else {
            $('#password-match-error').hide();
            $('#password-match-success').hide();
            return true;
        }
    }

    // Initial state
    updateVendorTypeUI();

    // On vendor type change
    $('#vendor_type').on('change', updateVendorTypeUI);

    // On checkbox change
    $('#create_portal_user').on('ifChanged', updatePasswordFieldsVisibility);

    // On password input
    $('#vendor_password, #vendor_password_confirmation').on('input', validatePasswords);

    // Form submission validation
    $('#vendor-form').on('submit', function(e) {
        var vendorType = $('#vendor_type').val();
        var createUser = $('#create_portal_user').is(':checked');
        
        if (vendorType === 'erp_dropship' || createUser) {
            if (!validatePasswords()) {
                e.preventDefault();
                toastr.error('Passwords do not match!');
                return false;
            }
            
            var password = $('#vendor_password').val();
            if (password.length < 6) {
                e.preventDefault();
                toastr.error('Password must be at least 6 characters!');
                return false;
            }
        }
        return true;
    });
});
</script>
@endsection












