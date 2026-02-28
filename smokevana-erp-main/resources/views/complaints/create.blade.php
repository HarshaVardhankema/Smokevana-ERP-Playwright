@extends('layouts.app')
@section('title', 'Create Complaint')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    /* Create Complaint Page - Amazon Theme */
    .complaint-create-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
    
    /* Header Banner */
    .complaint-create-page .content-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 0 0 10px 10px;
        padding: 22px 28px !important;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .complaint-create-page .content-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #ff9900;
        z-index: 1;
    }
    .complaint-create-page .content-header h1 {
        font-size: 24px !important;
        font-weight: 700 !important;
        color: #fff !important;
        margin: 0 !important;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    /* Form Container - Dark Background */
    .complaint-create-page .content {
        background: #37475a;
        padding: 1.5rem;
    }
    
    /* White Cards for Sections */
    .complaint-create-page .complaint-form-card {
        background: #fff;
        border-radius: 8px;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .complaint-create-page .complaint-form-card-title {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #232F3E;
        margin: 0 0 0.75rem 0;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #D5D9D9;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .complaint-create-page .complaint-form-card-title i {
        color: #FF9900;
    }
    
    /* Form Groups */
    .complaint-create-page .complaint-form-card .form-group {
        margin-bottom: 0.75rem;
    }
    .complaint-create-page .complaint-form-card .form-group:last-child {
        margin-bottom: 0;
    }
    .complaint-create-page .complaint-form-card label {
        color: #0F1111 !important;
        font-size: 0.8125rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .complaint-create-page .complaint-form-card .help-block {
        color: #565959 !important;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }
    
    /* Form Controls */
    .complaint-create-page .complaint-form-card .form-control {
        background: #fff;
        border: 1px solid #D5D9D9;
        color: #0F1111;
        font-size: 0.8125rem;
        padding: 0.375rem 0.5rem;
        min-height: 2rem;
        box-sizing: border-box;
        border-radius: 4px;
    }
    .complaint-create-page .complaint-form-card .form-control:focus {
        border-color: #FF9900;
        outline: none;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
    }
    .complaint-create-page .complaint-form-card textarea.form-control {
        min-height: 100px;
    }
    .complaint-create-page .complaint-form-card .input-group-addon {
        background: #F7F8F8;
        color: #232F3E;
        border-color: #D5D9D9;
        font-size: 0.8125rem;
        padding: 0.375rem 0.5rem;
        min-width: 2.25rem;
    }
    
    /* Select2 Styling */
    .complaint-create-page .select2-container--default .select2-selection--single,
    .complaint-create-page .select2-container--default .select2-selection--multiple {
        border: 1px solid #D5D9D9 !important;
        border-radius: 4px !important;
        min-height: 2rem;
    }
    .complaint-create-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2rem;
        padding-left: 0.5rem;
        font-size: 0.8125rem;
    }
    .complaint-create-page .select2-container--default.select2-container--focus .select2-selection--single,
    .complaint-create-page .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #FF9900 !important;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
    }
    .complaint-create-page .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #FF9900 !important;
        color: #fff !important;
    }
    
    /* File Upload Styling */
    .complaint-create-page .complaint-form-card input[type="file"] {
        padding: 0.5rem;
        border: 1px solid #D5D9D9;
        border-radius: 4px;
        background: #fff;
    }
    .complaint-create-page .complaint-form-card input[type="file"]:focus {
        border-color: #FF9900;
        outline: none;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
    }
    
    /* Image Preview */
    .complaint-create-page #image_preview .thumbnail {
        border: 1px solid #D5D9D9;
        border-radius: 4px;
        padding: 0.5rem;
        background: #fff;
    }
    .complaint-create-page #image_preview img {
        border-radius: 4px;
    }
    
    /* Row Gaps */
    .complaint-create-page .complaint-form-card .row {
        margin-left: -0.375rem;
        margin-right: -0.375rem;
    }
    .complaint-create-page .complaint-form-card .row > [class*="col-"] {
        padding-left: 0.375rem;
        padding-right: 0.375rem;
    }
    
    /* Buttons */
    .complaint-create-page .complaint-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 1rem;
    }
    .complaint-create-page .tw-dw-btn-primary {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 6px;
    }
    .complaint-create-page .tw-dw-btn-primary:hover {
        color: #fff !important;
        opacity: 0.95;
    }
    .complaint-create-page .tw-dw-btn-neutral {
        background: transparent !important;
        border: 1px solid rgba(255,255,255,0.6) !important;
        color: #fff !important;
        padding: 10px 24px;
        border-radius: 6px;
    }
    .complaint-create-page .tw-dw-btn-neutral:hover {
        background: rgba(255,255,255,0.1) !important;
        color: #fff !important;
    }
</style>
@endsection

@section('content')
<div class="complaint-create-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><i class="fa fa-exclamation-triangle"></i> Create Complaint</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action([\App\Http\Controllers\ComplaintController::class, 'store']), 'method' => 'post', 'id' => 'complaint_form', 'files' => true, 'enctype' => 'multipart/form-data']) !!}
    
    <div class="row">
        <!-- Card: Complaint Information -->
        <div class="col-md-12">
            <div class="complaint-form-card">
                <h5 class="complaint-form-card-title"><i class="fa fa-info-circle"></i> Complaint Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('request_type', 'Request Type:*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-tag"></i>
                                </span>
                                {!! Form::text('request_type', null, ['class' => 'form-control', 'required', 'placeholder' => 'e.g., Product Issue, Delivery Problem']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('contact_id', 'Customer/Contact:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                {!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2', 'placeholder' => 'Select Customer/Contact', 'style' => 'width:100%']) !!}
                            </div>
                            <small class="help-block">Optional - Select the customer related to this complaint</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Transaction & Products -->
        <div class="col-md-12">
            <div class="complaint-form-card">
                <h5 class="complaint-form-card-title"><i class="fa fa-shopping-cart"></i> Transaction & Products</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('transaction_id', 'Transaction:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-file-invoice"></i>
                                </span>
                                <select name="transaction_id" id="transaction_id" class="form-control select2" style="width:100%" disabled>
                                    <option value="">Select Contact First</option>
                                </select>
                            </div>
                            <small class="help-block">Select a contact first to load their transactions</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('variation_id', 'Products/Variations:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-box"></i>
                                </span>
                                <select name="variation_id[]" id="variation_id" class="form-control select2" style="width:100%" multiple disabled>
                                </select>
                            </div>
                            <small class="help-block">Select transaction first, then select one or more products</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Description -->
        <div class="col-md-12">
            <div class="complaint-form-card">
                <h5 class="complaint-form-card-title"><i class="fa fa-align-left"></i> Description</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('description', 'Description:') !!}
                            <div class="input-group">
                                <span class="input-group-addon" style="vertical-align: top; padding-top: 0.5rem;">
                                    <i class="fa fa-sticky-note"></i>
                                </span>
                                {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Describe the complaint in detail']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Attachments -->
        <div class="col-md-12">
            <div class="complaint-form-card">
                <h5 class="complaint-form-card-title"><i class="fa fa-paperclip"></i> Attachments</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('images', 'Upload Images:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-image"></i>
                                </span>
                                <input type="file" name="images[]" class="form-control" accept="image/*" multiple id="complaint_images">
                            </div>
                            <small class="help-block">Upload multiple images (JPEG, PNG, JPG, GIF - Max 2MB each)</small>
                            
                            <!-- Image Preview Container -->
                            <div id="image_preview" class="row" style="margin-top: 15px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="col-md-12">
            <div class="complaint-form-actions">
                <a href="{{ action([\App\Http\Controllers\ComplaintController::class, 'index']) }}" class="tw-dw-btn tw-dw-btn-neutral">
                    @lang('messages.cancel')
                </a>
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary">
                    <i class="fa fa-save"></i> @lang('messages.save')
                </button>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
</section>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            dropdownParent: $('.complaint-create-page')
        });
        
        // Load transactions when contact is selected
        $('#contact_id').on('change', function() {
            var contact_id = $(this).val();
            var transaction_select = $('#transaction_id');
            var variation_select = $('#variation_id');
            
            // Reset and disable dependent dropdowns
            transaction_select.html('<option value="">Loading...</option>').prop('disabled', true).trigger('change');
            variation_select.html('<option value="">Select Transaction First</option>').prop('disabled', true).trigger('change');
            
            if (contact_id) {
                $.ajax({
                    url: '/complaints/contact/' + contact_id + '/transactions',
                    type: 'GET',
                    success: function(data) {
                        transaction_select.html('<option value="">Select Transaction</option>');
                        $.each(data, function(index, transaction) {
                            transaction_select.append('<option value="' + transaction.id + '">' + transaction.text + '</option>');
                        });
                        transaction_select.prop('disabled', false).trigger('change');
                    },
                    error: function() {
                        transaction_select.html('<option value="">Error loading transactions</option>');
                        toastr.error('Failed to load transactions');
                    }
                });
            } else {
                transaction_select.html('<option value="">Select Contact First</option>');
            }
        });
        
        // Load variations when transaction is selected
        $('#transaction_id').on('change', function() {
            var transaction_id = $(this).val();
            var variation_select = $('#variation_id');
            
            // Reset variation dropdown
            variation_select.html('').prop('disabled', true).trigger('change');
            
            if (transaction_id) {
                $.ajax({
                    url: '/complaints/transaction/' + transaction_id + '/variations',
                    type: 'GET',
                    success: function(data) {
                        variation_select.html('');
                        $.each(data, function(index, variation) {
                            variation_select.append('<option value="' + variation.id + '">' + variation.text + '</option>');
                        });
                        variation_select.prop('disabled', false).trigger('change');
                    },
                    error: function() {
                        toastr.error('Failed to load products');
                    }
                });
            }
        });
        
        // Image preview functionality
        $('#complaint_images').on('change', function(e) {
            var files = e.target.files;
            var previewContainer = $('#image_preview');
            previewContainer.empty();
            
            if (files.length > 0) {
                $.each(files, function(index, file) {
                    if (file.type.match('image.*')) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var imageDiv = $('<div class="col-md-3" style="margin-bottom: 10px;">' +
                                '<div class="thumbnail">' +
                                '<img src="' + e.target.result + '" style="width: 100%; height: 150px; object-fit: cover;">' +
                                '<div class="caption text-center">' +
                                '<small>' + file.name + '</small>' +
                                '</div>' +
                                '</div>' +
                                '</div>');
                            previewContainer.append(imageDiv);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    });
</script>
@endsection
