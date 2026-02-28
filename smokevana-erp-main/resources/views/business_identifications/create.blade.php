@extends('layouts.app')
@section('title', 'Create Business Identification')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Business Identification Form</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            {!! Form::open(['url' => action([\App\Http\Controllers\BusinessIdentificationController::class, 'store']), 'method' => 'post', 'id' => 'business_identification_form', 'files' => true, 'enctype' => 'multipart/form-data']) !!}
            
            <!-- Business Identification Section -->
            <h4 class="tw-font-bold tw-mb-4"><i class="fa fa-building"></i> Business Identification</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('legal_business_name', 'Legal Business Name:*') !!}
                        {!! Form::text('legal_business_name', null, ['class' => 'form-control', 'required', 'placeholder' => 'Enter legal business name']) !!}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('dba', 'DBA (if any):') !!}
                        {!! Form::text('dba', null, ['class' => 'form-control', 'placeholder' => 'Doing Business As']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('fein_tax_id', 'FEIN / Tax ID:') !!}
                        {!! Form::text('fein_tax_id', null, ['class' => 'form-control', 'placeholder' => 'Enter FEIN or Tax ID']) !!}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('contact_id', 'Customer/Contact:*') !!}
                        {!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select Customer/Contact', 'style' => 'width:100%']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('business_types', 'Business Type:') !!}
                        <div class="checkbox">
                            <label><input type="checkbox" name="business_types[]" value="retail"> Retail</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="business_types[]" value="distributor"> Distributor</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="business_types[]" value="manufacturer"> Manufacturer</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="business_types[]" value="delivery"> Delivery</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="business_types[]" value="ecommerce"> E-commerce</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="business_types[]" value="other" id="business_type_other_check"> Other</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="business_type_other_input" style="display: none;">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('business_type_other', 'Other Business Type:') !!}
                        {!! Form::text('business_type_other', null, ['class' => 'form-control', 'placeholder' => 'Specify other business type']) !!}
                    </div>
                </div>
            </div>

            <!-- Primary Contact Information -->
            <h4 class="tw-font-bold tw-mb-4 tw-mt-6"><i class="fa fa-user"></i> Primary Contact Information</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('primary_contact_name', 'Name & Title:') !!}
                        {!! Form::text('primary_contact_name', null, ['class' => 'form-control', 'placeholder' => 'Contact person name']) !!}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('primary_contact_title', 'Title:') !!}
                        {!! Form::text('primary_contact_title', null, ['class' => 'form-control', 'placeholder' => 'Job title']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('primary_contact_phone', 'Phone:') !!}
                        {!! Form::text('primary_contact_phone', null, ['class' => 'form-control', 'placeholder' => 'Phone number']) !!}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('primary_contact_email', 'Email:') !!}
                        {!! Form::email('primary_contact_email', null, ['class' => 'form-control', 'placeholder' => 'Email address']) !!}
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <h4 class="tw-font-bold tw-mb-4 tw-mt-6"><i class="fa fa-map-marker"></i> Address Information</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('business_address', 'Business Address:') !!}
                        {!! Form::textarea('business_address', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Full business address']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('ship_from_address', 'Ship-From Address (if different):') !!}
                        {!! Form::textarea('ship_from_address', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Shipping origin address']) !!}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('ship_to_address', 'Ship-To Address (if different):') !!}
                        {!! Form::textarea('ship_to_address', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Shipping destination address']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('website_marketplaces', 'Website / Marketplace Storefronts:') !!}
                        {!! Form::textarea('website_marketplaces', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Enter website URLs or marketplace store names']) !!}
                    </div>
                </div>
            </div>

            <!-- License and Permit Information -->
            <h4 class="tw-font-bold tw-mb-4 tw-mt-6"><i class="fa fa-certificate"></i> License and Permit Information</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('resale_certificate_number', 'Resale Certificate/Permit #:') !!}
                        {!! Form::text('resale_certificate_number', null, ['class' => 'form-control', 'placeholder' => 'Certificate number']) !!}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('resale_certificate_state', 'Issuing State:') !!}
                        {!! Form::text('resale_certificate_state', null, ['class' => 'form-control', 'placeholder' => 'State']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>State/Local Licenses (if required):</label>
                        <div id="licenses_container">
                            <div class="license-entry row" style="margin-bottom: 10px;">
                                <div class="col-md-4">
                                    <input type="text" name="state_licenses[0][type]" class="form-control" placeholder="License Type">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="state_licenses[0][number]" class="form-control" placeholder="License Number">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="state_licenses[0][expiry]" class="form-control" placeholder="Expiry Date">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-success btn-sm add-license"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Age-Gating Information -->
            <h4 class="tw-font-bold tw-mb-4 tw-mt-6"><i class="fa fa-shield"></i> Age-Gating Method</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('age_gating_methods', 'For Retail/E-commerce (check all that apply):') !!}
                        <div class="checkbox">
                            <label><input type="checkbox" name="age_gating_methods[]" value="pos_id_scan"> POS ID scan</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="age_gating_methods[]" value="third_party"> Third-party age-verification</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="age_gating_methods[]" value="adult_signature"> Adult signature on delivery</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="age_gating_methods[]" value="website_gate"> Website age-gate</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="age_gating_methods[]" value="other" id="age_gating_other_check"> Other</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="age_gating_other_input" style="display: none;">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('age_gating_other', 'Other Age-Gating Method:') !!}
                        {!! Form::text('age_gating_other', null, ['class' => 'form-control', 'placeholder' => 'Specify other method']) !!}
                    </div>
                </div>
            </div>

            <!-- Acknowledgments -->
            <h4 class="tw-font-bold tw-mb-4 tw-mt-6"><i class="fa fa-check-square"></i> Prohibited Jurisdictions Acknowledgment</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="prohibited_jurisdictions_acknowledged" value="1">
                            <strong>We acknowledge we will not accept shipment to, or resell into, jurisdictions where the products are restricted/banned. We will notify Moonbuzz if our resale territory changes.</strong>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Document Upload -->
            <h4 class="tw-font-bold tw-mb-4 tw-mt-6"><i class="fa fa-paperclip"></i> Attach Documents</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('documents', 'Upload Documents (Licenses, Certificates, etc.):') !!}
                        <input type="file" name="documents[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" multiple id="documents">
                        <small class="help-block">Upload multiple documents (PDF, Images, Word - Max 5MB each)</small>
                        
                        <!-- Document Preview Container -->
                        <div id="document_preview" class="row" style="margin-top: 15px;"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-text-white tw-border-none pull-right">
                        @lang('messages.save')
                    </button>
                    <a href="{{ action([\App\Http\Controllers\BusinessIdentificationController::class, 'index']) }}" class="tw-dw-btn tw-dw-btn-error pull-right" style="margin-right: 10px;">
                        @lang('messages.cancel')
                    </a>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</section>

@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2();
        
        // Show/hide "Other" business type input
        $('#business_type_other_check').on('change', function() {
            if ($(this).is(':checked')) {
                $('#business_type_other_input').show();
            } else {
                $('#business_type_other_input').hide();
                $('input[name="business_type_other"]').val('');
            }
        });
        
        // Show/hide "Other" age-gating input
        $('#age_gating_other_check').on('change', function() {
            if ($(this).is(':checked')) {
                $('#age_gating_other_input').show();
            } else {
                $('#age_gating_other_input').hide();
                $('input[name="age_gating_other"]').val('');
            }
        });
        
        // Add more license fields
        var licenseIndex = 1;
        $(document).on('click', '.add-license', function() {
            var newLicense = `
                <div class="license-entry row" style="margin-bottom: 10px;">
                    <div class="col-md-4">
                        <input type="text" name="state_licenses[${licenseIndex}][type]" class="form-control" placeholder="License Type">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="state_licenses[${licenseIndex}][number]" class="form-control" placeholder="License Number">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="state_licenses[${licenseIndex}][expiry]" class="form-control" placeholder="Expiry Date">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-license"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
            `;
            $('#licenses_container').append(newLicense);
            licenseIndex++;
        });
        
        // Remove license field
        $(document).on('click', '.remove-license', function() {
            $(this).closest('.license-entry').remove();
        });
        
        // Document preview functionality
        $('#documents').on('change', function(e) {
            var files = e.target.files;
            var previewContainer = $('#document_preview');
            previewContainer.empty();
            
            if (files.length > 0) {
                $.each(files, function(index, file) {
                    var iconClass = 'fa-file';
                    if (file.type.match('image.*')) {
                        iconClass = 'fa-file-image-o';
                    } else if (file.type.match('pdf')) {
                        iconClass = 'fa-file-pdf-o';
                    } else if (file.type.match('word') || file.name.endsWith('.doc') || file.name.endsWith('.docx')) {
                        iconClass = 'fa-file-word-o';
                    }
                    
                    var fileDiv = $('<div class="col-md-3" style="margin-bottom: 10px;">' +
                        '<div class="thumbnail text-center">' +
                        '<i class="fa ' + iconClass + ' fa-4x" style="color: #3c8dbc; margin: 20px 0;"></i>' +
                        '<div class="caption">' +
                        '<small>' + file.name + '</small><br>' +
                        '<small class="text-muted">' + (file.size / 1024).toFixed(2) + ' KB</small>' +
                        '</div>' +
                        '</div>' +
                        '</div>');
                    previewContainer.append(fileDiv);
                });
            }
        });
    });
</script>
@endsection

