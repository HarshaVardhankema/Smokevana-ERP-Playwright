@extends('layouts.app')
@section('title', 'Edit Dropship Vendor')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
        <i class="fas fa-user-edit"></i> Edit Vendor: {{ $vendor->display_name }}
    </h1>
</section>

<section class="content">
    {!! Form::open(['route' => ['dropship.vendors.update', $vendor->id], 'method' => 'PUT', 'id' => 'vendor-form']) !!}
    
    <div class="row">
        <div class="col-lg-8">
            @component('components.widget', ['class' => 'box-primary', 'title' => 'Vendor Information'])
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('name', 'Vendor Name *') !!}
                            {!! Form::text('name', $vendor->name, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('company_name', 'Company Name') !!}
                            {!! Form::text('company_name', $vendor->company_name, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('vendor_type', 'Vendor Type *') !!}
                            {!! Form::select('vendor_type', $vendorTypes, $vendor->vendor_type, ['class' => 'form-control', 'required', 'id' => 'vendor_type']) !!}
                            <p class="help-block">
                                <strong>ERP Vendor:</strong> Normal internal vendor for accounting/supplier purposes.<br>
                                <strong>ERP Dropship Vendor:</strong> Third-party vendors who use the Vendor Portal to manage and fulfill orders.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('email', 'Email') !!}
                            {!! Form::email('email', $vendor->email, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('phone', 'Phone') !!}
                            {!! Form::text('phone', $vendor->phone, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('address', 'Address') !!}
                    {!! Form::textarea('address', $vendor->address, ['class' => 'form-control', 'rows' => 3]) !!}
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('contact_id', 'Link to Supplier Contact') !!}
                            {!! Form::select('contact_id', $suppliers, $vendor->contact_id, ['class' => 'form-control select2', 'placeholder' => 'Select supplier (optional)']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('status', 'Status *') !!}
                            {!! Form::select('status', [
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'pending' => 'Pending'
                            ], $vendor->status, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-info', 'title' => 'Pricing & Commission'])
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('margin_percentage', 'Margin Percentage (%)') !!}
                            {!! Form::number('margin_percentage', $vendor->margin_percentage, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'max' => '100']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('default_markup_percentage', 'Default Markup (%)') !!}
                            {!! Form::number('default_markup_percentage', $vendor->default_markup_percentage, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) !!}
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
                            ], $vendor->commission_type, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('commission_value', 'Commission Value') !!}
                            {!! Form::number('commission_value', $vendor->commission_value, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) !!}
                        </div>
                    </div>
                </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-warning', 'title' => 'Payment Settings'])
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('payment_terms', 'Payment Terms *') !!}
                            {!! Form::select('payment_terms', [
                                'immediate' => 'Immediate',
                                'weekly' => 'Weekly',
                                'biweekly' => 'Bi-Weekly',
                                'monthly' => 'Monthly'
                            ], $vendor->payment_terms, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('payment_method', 'Preferred Payment Method') !!}
                            {!! Form::text('payment_method', $vendor->payment_method, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            @endcomponent

           
        </div>

        <div class="col-lg-4">
            @component('components.widget', ['class' => 'box-success', 'title' => 'WooCommerce Integration'])
                <div class="form-group">
                    {!! Form::label('wp_term_id', 'WooCommerce Vendor Term ID') !!}
                    {!! Form::number('wp_term_id', $vendor->wp_term_id, ['class' => 'form-control']) !!}
                </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-default', 'title' => 'Statistics'])
                <div class="tw-space-y-2">
                    <div class="tw-flex tw-justify-between">
                        <span>Products Mapped:</span>
                        <span class="tw-font-semibold">{{ $vendor->products()->count() }}</span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <span>Pending Orders:</span>
                        <span class="tw-font-semibold text-warning">{{ $vendor->pendingOrders()->count() }}</span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <span>Completed Orders:</span>
                        <span class="tw-font-semibold text-success">{{ $vendor->completedOrders()->count() }}</span>
                    </div>
                </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-default', 'title' => 'Notes'])
                <div class="form-group">
                    {!! Form::textarea('notes', $vendor->notes, ['class' => 'form-control', 'rows' => 4]) !!}
                </div>
            @endcomponent

             @component('components.widget', ['class' => 'box-warning', 'title' => 'Vendor Portal Access'])
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('password', 'Password') !!}
                            <div class="input-group">
                                {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Leave empty to keep current password', 'id' => 'password-field', 'autocomplete' => 'off']) !!}
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="toggle-password">
                                        <i class="fas fa-eye" id="password-icon"></i>
                                    </button>
                                </span>
                            </div>
                            <p class="help-block">Enter a new password to change the vendor's portal access password. Leave empty to keep the current password.</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('password_confirmation', 'Confirm Password') !!}
                            <div class="input-group">
                                {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirm new password', 'id' => 'password-confirmation-field']) !!}
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="toggle-password-confirmation">
                                        <i class="fas fa-eye" id="password-confirmation-icon"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endcomponent

            <div class="form-group">
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-w-full">
                    <i class="fas fa-save"></i> @lang('messages.update')
                </button>
            </div>
            <a href="{{ route('dropship.vendors.index') }}" class="tw-dw-btn tw-dw-btn-ghost tw-w-full">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    
    {!! Form::close() !!}
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('.select2').select2();

    $('#toggle-password').on('click', function(e) {
        e.preventDefault();
        var $field = $('#password-field');
        var $icon = $('#password-icon');

        if ($field.attr('type') === 'password') {
            $field.attr('type', 'text');
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            $field.attr('type', 'password');
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('#toggle-password-confirmation').on('click', function(e) {
        e.preventDefault();
        var $field = $('#password-confirmation-field');
        var $icon = $('#password-confirmation-icon');

        if ($field.attr('type') === 'password') {
            $field.attr('type', 'text');
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            $field.attr('type', 'password');
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>
@endsection












