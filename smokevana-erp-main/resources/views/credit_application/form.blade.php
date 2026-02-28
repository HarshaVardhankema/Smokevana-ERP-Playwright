@extends('layouts.app')

@section('title', 'Credit Application Form')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header tw-flex tw-items-center tw-justify-between">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Credit Application Form</h1>
    <div class="tw-flex tw-gap-3">
        <button type="submit" form="creditApplicationForm" class="btn btn-primary">
            <i class="fa fa-send"></i> Submit Application
        </button>
        <button type="button" class="btn btn-default" onclick="resetForm()">
            <i class="fa fa-refresh"></i> Reset Form
        </button>
    </div>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => '/api/credit-application', 'method' => 'post', 'id' => 'creditApplicationForm', 'files' => true]) !!}
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Credit Application Details'])
    <div class="row">
        <div class="col-md-6">
            <h4><i class="fa fa-user"></i> Authorized Signatory Information</h4>
            <div class="form-group">
                {!! Form::label('authorized_signatory_name', 'Full Name *') !!}
                {!! Form::text('authorized_signatory_name', null, ['class' => 'form-control', 'placeholder' => 'Enter full name', 'required']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('authorized_signatory_email', 'Email Address *') !!}
                {!! Form::email('authorized_signatory_email', null, ['class' => 'form-control', 'placeholder' => 'Enter email address', 'required']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('authorized_signatory_phone', 'Phone Number *') !!}
                {!! Form::text('authorized_signatory_phone', null, ['class' => 'form-control', 'placeholder' => 'Enter phone number', 'maxlength' => '20', 'required']) !!}
            </div>
        </div>
        <div class="col-md-6">
            <h4><i class="fa fa-building"></i> Business Information</h4>
            <div class="form-group">
                {!! Form::label('customer_group_id', 'Customer Group *') !!}
                {!! Form::select('customer_group_id', $customerGroups, null, ['class' => 'form-control select2', 'placeholder' => 'Select Customer Group', 'required']) !!}
            </div>
            <h4><i class="fa fa-dollar"></i> Credit Information</h4>
            <div class="form-group">
                {!! Form::label('requested_credit_amount', 'Requested Credit Amount ($) *') !!}
                {!! Form::number('requested_credit_amount', null, ['class' => 'form-control', 'placeholder' => '0.00', 'step' => '0.01', 'min' => '0', 'required']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('average_revenue_per_month', 'Average Monthly Revenue ($) *') !!}
                {!! Form::number('average_revenue_per_month', null, ['class' => 'form-control', 'placeholder' => '0.00', 'step' => '0.01', 'min' => '0', 'required']) !!}
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <h4><i class="fa fa-file-text"></i> Document Upload</h4>
            <div class="form-group">
                {!! Form::label('digital_signature', 'Digital Signature (Single)') !!}
                {!! Form::file('digital_signature', ['class' => 'form-control', 'accept' => 'image/jpeg,image/jpg,image/png']) !!}
                <small class="help-block">Accepted formats: JPG, JPEG, PNG. Max size: 2MB</small>
            </div>
            <div class="form-group">
                {!! Form::label('digital_signatures', 'Digital Signatures (Multiple)') !!}
                {!! Form::file('digital_signatures[]', ['class' => 'form-control', 'accept' => 'image/jpeg,image/jpg,image/png', 'multiple']) !!}
                <small class="help-block">You can upload up to 3 signature files</small>
            </div>
        </div>
        <div class="col-md-6">
            <h4><i class="fa fa-folder"></i> Supporting Documents</h4>
            <div class="form-group">
                {!! Form::label('supporting_documents', 'Supporting Documents') !!}
                {!! Form::file('supporting_documents[]', ['class' => 'form-control', 'accept' => '.pdf,.jpg,.jpeg,.png,.doc,.docx', 'multiple']) !!}
                <small class="help-block">
                    Accepted formats: PDF, JPG, JPEG, PNG, DOC, DOCX. Max size: 10MB per file. 
                    You can upload up to 10 documents.
                </small>
            </div>
        </div>

    </div>
    @endcomponent
    {!! Form::close() !!}
</section>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Application Submitted Successfully</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    Your credit application has been submitted successfully. You will receive a confirmation email shortly.
                </div>
                <div id="applicationDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Application Submission Failed</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    <div id="errorMessage"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize select2
    $('.select2').select2();
    
    // File upload validation
    $('input[name="digital_signature"]').on('change', function() {
        validateFile(this, ['jpg', 'jpeg', 'png'], 2);
    });

    $('input[name="digital_signatures[]"]').on('change', function() {
        validateMultipleFiles(this, ['jpg', 'jpeg', 'png'], 3, 2);
    });

    $('input[name="supporting_documents[]"]').on('change', function() {
        validateMultipleFiles(this, ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'], 10, 10);
    });

    // Form submission
    $('#creditApplicationForm').on('submit', function(e) {
        e.preventDefault();
        submitApplication();
    });

    // Real-time validation
    $('input[required], select[required]').on('blur', function() {
        validateField(this);
    });
});


function validateField(field) {
    var isValid = true;
    var value = $(field).val();
    
    if ($(field).attr('required') && !value) {
        $(field).addClass('input-invalid');
        $(field).siblings('.help-block').text('This field is required');
        isValid = false;
    } else {
        $(field).removeClass('input-invalid');
        $(field).siblings('.help-block').text('');
    }
    
    // Email validation
    if ($(field).attr('type') === 'email' && value) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            $(field).addClass('input-invalid');
            $(field).siblings('.help-block').text('Please enter a valid email address');
            isValid = false;
        }
    }
    
    return isValid;
}

function validateFile(input, allowedTypes, maxSizeMB) {
    if (input.files && input.files[0]) {
        var file = input.files[0];
        var fileType = file.name.split('.').pop().toLowerCase();
        var fileSize = file.size / (1024 * 1024);

        if (allowedTypes.indexOf(fileType) === -1) {
            alert('Invalid file type. Allowed types: ' + allowedTypes.join(', '));
            input.value = '';
            return false;
        }

        if (fileSize > maxSizeMB) {
            alert('File size too large. Maximum size: ' + maxSizeMB + 'MB');
            input.value = '';
            return false;
        }
    }
    return true;
}

function validateMultipleFiles(input, allowedTypes, maxFiles, maxSizeMB) {
    if (input.files && input.files.length > 0) {
        if (input.files.length > maxFiles) {
            alert('Too many files. Maximum: ' + maxFiles + ' files');
            input.value = '';
            return false;
        }

        for (var i = 0; i < input.files.length; i++) {
            var file = input.files[i];
            var fileType = file.name.split('.').pop().toLowerCase();
            var fileSize = file.size / (1024 * 1024);

            if (allowedTypes.indexOf(fileType) === -1) {
                alert('Invalid file type in file ' + (i + 1) + '. Allowed types: ' + allowedTypes.join(', '));
                input.value = '';
                return false;
            }

            if (fileSize > maxSizeMB) {
                alert('File size too large in file ' + (i + 1) + '. Maximum size: ' + maxSizeMB + 'MB');
                input.value = '';
                return false;
            }
        }
    }
    return true;
}

function submitApplication() {
    // Validate all required fields
    var isValid = true;
    $('input[required], select[required]').each(function() {
        if (!validateField(this)) {
            isValid = false;
        }
    });

    if (!isValid) {
        alert('Please fill in all required fields correctly.');
        return;
    }

    // Show loading state
    var submitBtn = $('button[type="submit"]');
    var originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

    // Prepare form data
    var formData = new FormData($('#creditApplicationForm')[0]);

    $.ajax({
        url: '/api/credit-application',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status) {
                showSuccessModal(response.data);
                resetForm();
            } else {
                showErrorModal(response.message || 'Application submission failed');
            }
        },
        error: function(xhr) {
            var response = xhr.responseJSON;
            var errorMessage = 'An error occurred while submitting the application.';
            
            if (response) {
                if (response.message) {
                    errorMessage = response.message;
                } else if (response.errors) {
                    var errors = [];
                    for (var field in response.errors) {
                        errors.push(response.errors[field][0]);
                    }
                    errorMessage = errors.join('<br>');
                }
            }
            
            showErrorModal(errorMessage);
        },
        complete: function() {
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
}

function showSuccessModal(data) {
    var details = '<h5>Application Details:</h5>';
    details += '<p><strong>Application ID:</strong> ' + data.application_id + '</p>';
    details += '<p><strong>Customer Name:</strong> ' + data.customer_name + '</p>';
    if (data.business_name) {
        details += '<p><strong>Business Name:</strong> ' + data.business_name + '</p>';
    }
    details += '<p><strong>Submitted At:</strong> ' + new Date(data.submitted_at).toLocaleString() + '</p>';
    
    if (data.upload_summary) {
        details += '<h6>Upload Summary:</h6>';
        details += '<ul>';
        details += '<li>Single Signature: ' + (data.upload_summary.single_signature_uploaded ? 'Yes' : 'No') + '</li>';
        details += '<li>Multiple Signatures: ' + data.upload_summary.multiple_signatures_count + '</li>';
        details += '<li>Supporting Documents: ' + data.upload_summary.supporting_documents_count + '</li>';
        details += '</ul>';
    }
    
    $('#applicationDetails').html(details);
    $('#successModal').modal('show');
}

function showErrorModal(message) {
    $('#errorMessage').html(message);
    $('#errorModal').modal('show');
}

function resetForm() {
    $('#creditApplicationForm')[0].reset();
    $('.input-invalid').removeClass('input-invalid');
    $('.help-block').text('');
    $('.select2').val(null).trigger('change');
}
</script>

<style>
.input-invalid {
    border-color: #dd4b39 !important;
    box-shadow: 0 0 0 0.2rem rgba(221, 75, 57, 0.25) !important;
}

.help-block {
    color: #dd4b39;
    font-size: 12px;
    margin-top: 5px;
}

.box h4 {
    color: #3c8dbc;
    border-bottom: 1px solid #eee;
    padding-bottom: 8px;
    margin-bottom: 15px;
    margin-top: 20px;
}

.box h4:first-child {
    margin-top: 0;
}

.form-group {
    margin-bottom: 15px;
}

.alert ul {
    margin-bottom: 0;
}

.row {
    margin-bottom: 10px;
}
</style>
@endsection
