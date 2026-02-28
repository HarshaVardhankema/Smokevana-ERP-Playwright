@extends('layouts.app')
@section('title', 'Edit Business Identification')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Edit Business Identification</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            {!! Form::model($identification, ['url' => action([\App\Http\Controllers\BusinessIdentificationController::class, 'update'], [$identification->id]), 'method' => 'PUT', 'id' => 'business_identification_form', 'files' => true, 'enctype' => 'multipart/form-data']) !!}
            
            <!-- Status Section (Admin) -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('status', 'Status:*') !!}
                        {!! Form::select('status', [
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected'
                        ], null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('admin_notes', 'Admin Notes:') !!}
                        {!! Form::textarea('admin_notes', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Internal notes about this submission']) !!}
                    </div>
                </div>
            </div>
            
            <hr>
            
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
                            @php
                                $selectedTypes = $identification->business_types ?? [];
                            @endphp
                            <label><input type="checkbox" name="business_types[]" value="retail" {{ in_array('retail', $selectedTypes) ? 'checked' : '' }}> Retail</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="business_types[]" value="distributor" {{ in_array('distributor', $selectedTypes) ? 'checked' : '' }}> Distributor</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="business_types[]" value="manufacturer" {{ in_array('manufacturer', $selectedTypes) ? 'checked' : '' }}> Manufacturer</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="business_types[]" value="delivery" {{ in_array('delivery', $selectedTypes) ? 'checked' : '' }}> Delivery</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="business_types[]" value="ecommerce" {{ in_array('ecommerce', $selectedTypes) ? 'checked' : '' }}> E-commerce</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="business_types[]" value="other" id="business_type_other_check" {{ in_array('other', $selectedTypes) ? 'checked' : '' }}> Other</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="business_type_other_input" style="{{ in_array('other', $identification->business_types ?? []) ? '' : 'display: none;' }}">
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
                            @if($identification->state_licenses && is_array($identification->state_licenses) && count($identification->state_licenses) > 0)
                                @foreach($identification->state_licenses as $index => $license)
                                    @if(isset($license['type']) || isset($license['number']) || isset($license['expiry']))
                                        <div class="license-entry row" style="margin-bottom: 10px;">
                                            <div class="col-md-4">
                                                <input type="text" name="state_licenses[{{ $index }}][type]" class="form-control" placeholder="License Type" value="{{ $license['type'] ?? '' }}">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="state_licenses[{{ $index }}][number]" class="form-control" placeholder="License Number" value="{{ $license['number'] ?? '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="date" name="state_licenses[{{ $index }}][expiry]" class="form-control" placeholder="Expiry Date" value="{{ $license['expiry'] ?? '' }}">
                                            </div>
                                            <div class="col-md-1">
                                                @if($loop->first)
                                                    <button type="button" class="btn btn-success btn-sm add-license"><i class="fa fa-plus"></i></button>
                                                @else
                                                    <button type="button" class="btn btn-danger btn-sm remove-license"><i class="fa fa-minus"></i></button>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
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
                            @endif
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
                            @php
                                $selectedMethods = $identification->age_gating_methods ?? [];
                            @endphp
                            <label><input type="checkbox" name="age_gating_methods[]" value="pos_id_scan" {{ in_array('pos_id_scan', $selectedMethods) ? 'checked' : '' }}> POS ID scan</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="age_gating_methods[]" value="third_party" {{ in_array('third_party', $selectedMethods) ? 'checked' : '' }}> Third-party age-verification</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="age_gating_methods[]" value="adult_signature" {{ in_array('adult_signature', $selectedMethods) ? 'checked' : '' }}> Adult signature on delivery</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="age_gating_methods[]" value="website_gate" {{ in_array('website_gate', $selectedMethods) ? 'checked' : '' }}> Website age-gate</label>
                            <label style="margin-left: 15px;"><input type="checkbox" name="age_gating_methods[]" value="other" id="age_gating_other_check" {{ in_array('other', $selectedMethods) ? 'checked' : '' }}> Other</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="age_gating_other_input" style="{{ in_array('other', $identification->age_gating_methods ?? []) ? '' : 'display: none;' }}">
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
                            {!! Form::checkbox('prohibited_jurisdictions_acknowledged', 1, null) !!}
                            <strong>We acknowledge we will not accept shipment to, or resell into, jurisdictions where the products are restricted/banned. We will notify Moonbuzz if our resale territory changes.</strong>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Existing Documents -->
            @if($identification->attachments && is_array($identification->attachments) && count($identification->attachments) > 0)
                <h4 class="tw-font-bold tw-mb-4 tw-mt-6"><i class="fa fa-paperclip"></i> Existing Attachments</h4>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            @foreach($identification->attachments as $attachment)
                                @php
                                    $ext = pathinfo($attachment, PATHINFO_EXTENSION);
                                    $filename = basename($attachment);
                                    $isPdf = strtolower($ext) === 'pdf';
                                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                                @endphp
                                <div class="col-md-3" style="margin-bottom: 15px;">
                                    <div class="thumbnail text-center">
                                        @if($isImage)
                                            <a href="{{ asset($attachment) }}" target="_blank">
                                                <img src="{{ asset($attachment) }}" style="max-height: 150px; object-fit: cover;">
                                            </a>
                                        @else
                                            <a href="{{ asset($attachment) }}" target="_blank">
                                                <i class="fa fa-file-{{ $isPdf ? 'pdf' : 'text' }}-o fa-4x" style="color: #3c8dbc; margin: 20px 0;"></i>
                                            </a>
                                        @endif
                                        <div class="caption">
                                            <small>{{ $filename }}</small><br>
                                            <div class="checkbox" style="margin: 5px 0;">
                                                <label>
                                                    <input type="checkbox" name="remove_documents[]" value="{{ $attachment }}">
                                                    Remove
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Upload New Documents -->
            <h4 class="tw-font-bold tw-mb-4 tw-mt-6"><i class="fa fa-paperclip"></i> Upload New Documents</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('documents', 'Add More Documents (Licenses, Certificates, etc.):') !!}
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
                        @lang('messages.update')
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
        var licenseIndex = {{ ($identification->state_licenses && is_array($identification->state_licenses)) ? count($identification->state_licenses) : 1 }};
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

