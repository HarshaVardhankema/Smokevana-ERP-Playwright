@extends('layouts.app')

@section('title', 'Create Credit Application')

@section('content')

@if(session('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-check"></i> Success!</h4>
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Validation Errors!</h4>
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Amazon-style banner -->
<section class="content-header no-print">
    <div class="credit-line-create-banner amazon-theme-banner">
        <div>
            <h1 class="banner-title"><i class="fa fa-credit-card"></i> Create Credit Application</h1>
            <p class="banner-subtitle">Submit a new credit application for customer approval</p>
        </div>
        <div class="banner-actions">
            <button type="submit" form="creditApplicationCreateForm" class="btn btn-success">
                <i class="fa fa-check"></i> Submit Credit Application
            </button>
            <a href="{{ route('credit-lines.index') }}" class="btn btn-back">
                <i class="fa fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content credit-line-create-page">
    {!! Form::open(['url' => route('credit-lines.store'), 'method' => 'post', 'id' => 'creditApplicationCreateForm', 'files' => true]) !!}
    
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Customer Selection'])
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> <strong>Important Information:</strong>
                    <ul style="margin-bottom: 0; margin-top: 10px;">
                        <li>Customers can create multiple credit applications over time</li>
                        <li>If you have a <strong>pending</strong> application, you must wait for approval or rejection before submitting a new request</li>
                        <li>If you have an <strong>approved</strong> credit limit, new requests must be for an amount <strong>greater than</strong> your current approved limit</li>
                        <li>After approval or rejection, you can submit a new credit application</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group @error('contact_id') has-error @enderror">
                    {!! Form::label('contact_id', 'Select Customer *') !!}
                    {!! Form::select('contact_id', 
                        ['' => 'Select a customer...'] + $contacts->pluck('name', 'id')->toArray(), 
                        old('contact_id'), 
                        ['class' => 'form-control select2 ' . ($errors->has('contact_id') ? 'parsley-error' : ''), 'required', 'id' => 'contact_select']) !!}
                    @error('contact_id')
                        <span class="help-block text-red">{{ $message }}</span>
                    @enderror
                </div>
                <div id="customer_credit_info" style="display: none;" class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> <strong>Current Credit Limit:</strong> <span id="current_credit_limit">0.00</span>
                    <br><small>Your new request must be greater than this amount.</small>
                </div>
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-info', 'title' => 'Credit Application Details'])
        <div class="row">
            <div class="col-md-6">
                <div class="form-group @error('requested_credit_amount') has-error @enderror">
                    {!! Form::label('requested_credit_amount', 'Requested Credit Amount *') !!}
                    {!! Form::number('requested_credit_amount', old('requested_credit_amount'), ['class' => 'form-control ' . ($errors->has('requested_credit_amount') ? 'parsley-error' : ''), 'placeholder' => 'Enter requested credit amount', 'required', 'step' => '0.01', 'min' => '0']) !!}
                    @error('requested_credit_amount')
                        <span class="help-block text-red">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group @error('average_revenue_per_month') has-error @enderror">
                    {!! Form::label('average_revenue_per_month', 'Average Revenue Per Month *') !!}
                    {!! Form::number('average_revenue_per_month', old('average_revenue_per_month'), ['class' => 'form-control ' . ($errors->has('average_revenue_per_month') ? 'parsley-error' : ''), 'placeholder' => 'Enter average monthly revenue', 'required', 'step' => '0.01', 'min' => '0']) !!}
                    @error('average_revenue_per_month')
                        <span class="help-block text-red">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-success', 'title' => 'Authorized Signatory Information'])
        <div class="row">
            <div class="col-md-4">
                <div class="form-group @error('authorized_signatory_name') has-error @enderror">
                    {!! Form::label('authorized_signatory_name', 'Signatory Name *') !!}
                    {!! Form::text('authorized_signatory_name', old('authorized_signatory_name'), ['class' => 'form-control ' . ($errors->has('authorized_signatory_name') ? 'parsley-error' : ''), 'placeholder' => 'Enter authorized signatory name', 'required']) !!}
                    @error('authorized_signatory_name')
                        <span class="help-block text-red">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group @error('authorized_signatory_email') has-error @enderror">
                    {!! Form::label('authorized_signatory_email', 'Signatory Email *') !!}
                    {!! Form::email('authorized_signatory_email', old('authorized_signatory_email'), ['class' => 'form-control ' . ($errors->has('authorized_signatory_email') ? 'parsley-error' : ''), 'placeholder' => 'Enter signatory email', 'required']) !!}
                    @error('authorized_signatory_email')
                        <span class="help-block text-red">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group @error('authorized_signatory_phone') has-error @enderror">
                    {!! Form::label('authorized_signatory_phone', 'Signatory Phone *') !!}
                    {!! Form::text('authorized_signatory_phone', old('authorized_signatory_phone'), ['class' => 'form-control ' . ($errors->has('authorized_signatory_phone') ? 'parsley-error' : ''), 'placeholder' => 'Enter signatory phone', 'required']) !!}
                    @error('authorized_signatory_phone')
                        <span class="help-block text-red">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-warning', 'title' => 'Supporting Documents'])
        <div class="row">
            <div class="col-md-12">
                <div class="form-group @error('supporting_documents') has-error @enderror">
                    {!! Form::label('supporting_documents', 'Supporting Documents *') !!}
                    {!! Form::file('supporting_documents[]', ['class' => 'form-control-file', 'multiple' => true, 'accept' => '.pdf,.jpg,.jpeg,.png,.doc,.docx', 'required']) !!}
                    @error('supporting_documents')
                        <span class="help-block text-red">{{ $message }}</span>
                    @enderror
                    <small class="help-block">
                        <strong>Supported formats:</strong> PDF, JPG, JPEG, PNG, DOC, DOCX<br>
                        <strong>Maximum file size:</strong> 10MB per file<br>
                        <strong>Maximum files:</strong> 10 documents
                    </small>
                </div>
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-danger', 'title' => 'Digital Signatures'])
        <div class="row">
            <div class="col-md-12">
                <div class="form-group @error('digital_signatures') has-error @enderror">
                    {!! Form::label('digital_signatures', 'Digital Signatures *') !!}
                    {!! Form::file('digital_signatures[]', ['class' => 'form-control-file', 'multiple' => true, 'accept' => '.jpg,.jpeg,.png,.pdf', 'required']) !!}
                    @error('digital_signatures')
                        <span class="help-block text-red">{{ $message }}</span>
                    @enderror
                    <small class="help-block">
                        <strong>Supported formats:</strong> JPG, JPEG, PNG, PDF<br>
                        <strong>Maximum file size:</strong> 10MB per file<br>
                        <strong>Maximum files:</strong> 10 signatures
                    </small>
                </div>
            </div>
        </div>
    @endcomponent
    
    {!! Form::close() !!}
</section>

@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Store customer credit limits
    var customerCreditLimits = {!! json_encode($contacts->pluck('credit_limit', 'id')->toArray()) !!};
    
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select a customer...',
        allowClear: true,
        width: '100%'
    });

    // Handle customer selection change
    $('#contact_select').on('change', function() {
        var contactId = $(this).val();
        
        if (contactId && customerCreditLimits[contactId]) {
            var creditLimit = parseFloat(customerCreditLimits[contactId]) || 0;
            
            if (creditLimit > 0) {
                $('#current_credit_limit').text(creditLimit.toFixed(2));
                $('#customer_credit_info').slideDown();
                
                // Update the minimum value hint for requested credit amount
                var $requestedAmount = $('input[name="requested_credit_amount"]');
                $requestedAmount.attr('min', creditLimit);
                $requestedAmount.attr('placeholder', 'Must be greater than ' + creditLimit.toFixed(2));
            } else {
                $('#customer_credit_info').slideUp();
                $('input[name="requested_credit_amount"]').attr('min', '0');
                $('input[name="requested_credit_amount"]').attr('placeholder', 'Enter requested credit amount');
            }
        } else {
            $('#customer_credit_info').slideUp();
            $('input[name="requested_credit_amount"]').attr('min', '0');
            $('input[name="requested_credit_amount"]').attr('placeholder', 'Enter requested credit amount');
        }
    });

    // Form validation
    $('#creditApplicationCreateForm').on('submit', function(e) {
        var validated = true;
        
        // Clear previous errors
        $('.parsley-error').removeClass('parsley-error');
        $('.help-block').remove();
        
        // Validate required fields
        var contactId = $('select[name="contact_id"]').val();
        if (!contactId) {
            $('select[name="contact_id"]').addClass('parsley-error');
            toastr.error('Please select a customer.');
            validated = false;
        }

        var requestedAmount = parseFloat($('input[name="requested_credit_amount"]').val());
        var contactId = $('select[name="contact_id"]').val();
        var currentCreditLimit = contactId && customerCreditLimits[contactId] ? parseFloat(customerCreditLimits[contactId]) : 0;
        
        if (isNaN(requestedAmount) || requestedAmount <= 0) {
            $('input[name="requested_credit_amount"]').addClass('parsley-error');
            toastr.error('Please enter a valid credit amount greater than 0.');
            validated = false;
        } else if (currentCreditLimit > 0 && requestedAmount <= currentCreditLimit) {
            $('input[name="requested_credit_amount"]').addClass('parsley-error');
            toastr.error('Requested credit amount must be greater than your current credit limit of ' + currentCreditLimit.toFixed(2));
            validated = false;
        }

        var avgRevenue = parseFloat($('input[name="average_revenue_per_month"]').val());
        if (isNaN(avgRevenue) || avgRevenue <= 0) {
            $('input[name="average_revenue_per_month"]').addClass('parsley-error');
            toastr.error('Please enter a valid average monthly revenue greater than 0.');
            validated = false;
        }

        // Validate files
        var supportingDocs = $('input[name="supporting_documents[]"]')[0].files.length;
        var digitalSigs = $('input[name="digital_signatures[]"]')[0].files.length;

        if (supportingDocs === 0) {
            $('input[name="supporting_documents[]"]').addClass('parsley-error');
            toastr.error('Please upload at least one supporting document.');
            validated = false;
        }

        if (digitalSigs === 0) {
            $('input[name="digital_signatures[]"]').addClass('parsley-error');
            toastr.error('Please upload at least one digital signature.');
            validated = false;
        }

        if (!validated) {
            e.preventDefault();
            toastr.error('Please fix the validation errors.');
            return false;
        }
        
        // Show SweetAlert confirmation
        e.preventDefault();
        
        swal({
            title: 'Submit Credit Application?',
            text: 'Are you sure you want to submit this credit application? It will be pending for review.',
            icon: 'warning',
            buttons: {
                cancel: 'Cancel',
                confirm: {
                    text: 'Submit Application',
                    className: 'btn-success'
                }
            },
            dangerMode: true,
        }).then((willSubmit) => {
            if (willSubmit) {
                // Show loading state
                swal({
                    title: 'Submitting...',
                    text: 'Please wait while we submit your credit application.',
                    icon: 'info',
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                });
                
                // Submit the form
                document.getElementById('creditApplicationCreateForm').submit();
            }
        });
    });

    // File validation on change
    $('input[type="file"]').on('change', function() {
        var allowedTypes = {
            'supporting_documents': ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'],
            'digital_signatures': ['jpg', 'jpeg', 'png', 'pdf']
        };
        
        var inputName = $(this).attr('name');
        var allowedExts = allowedTypes[inputName.split('[')[0]];
        var files = this.files;
        
        for (var i = 0; i < files.length; i++) {
            var fileExtension = files[i].name.split('.').pop().toLowerCase();
            
            if (!allowedExts.includes(fileExtension)) {
                alert('File ' + files[i].name + ' has invalid format. Only ' + allowedExts.join(', ').toUpperCase() + ' files are allowed.');
                this.value = '';
                return;
            }
            
            if (files[i].size > 10 * 1024 * 1024) { // 10MB
                alert('File ' + files[i].name + ' is too large. Please select files smaller than 10MB.');
                this.value = '';
                return;
            }
        }
        
        if (files.length > 10) {
            alert('Maximum 10 files allowed.');
            this.value = '';
            return;
        }
    });
});
</script>
@endsection

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
.credit-line-create-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
.credit-line-create-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    border-radius: 0 0 10px 10px;
    padding: 22px 28px;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.credit-line-create-banner.amazon-theme-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: #ff9900;
    z-index: 1;
}
.credit-line-create-banner .banner-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: #fff !important;
}
.credit-line-create-banner .banner-title i { color: #fff !important; }
.credit-line-create-banner .banner-subtitle {
    font-size: 13px;
    color: rgba(255,255,255,0.9) !important;
    margin: 4px 0 0 0;
}
.credit-line-create-banner .banner-actions { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
.credit-line-create-banner .banner-actions .btn-success {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border: 2px solid #C7511F !important;
    color: #fff !important;
    font-weight: 600;
}
.credit-line-create-banner .banner-actions .btn-success:hover {
    color: #fff !important;
    opacity: 0.95;
    border-color: #E47911 !important;
}
.credit-line-create-banner .banner-actions .btn-back {
    background: #f0f0f0 !important;
    border: 1px solid #888 !important;
    color: #333 !important;
    font-weight: 500;
}
.credit-line-create-banner .banner-actions .btn-back:hover {
    background: #e5e5e5 !important;
    border-color: #666 !important;
    color: #333 !important;
}
.credit-line-create-page .box-primary,
.credit-line-create-page .box-info,
.credit-line-create-page .box-success,
.credit-line-create-page .box-warning,
.credit-line-create-page .box-danger {
    border-radius: 10px; overflow: hidden; border: 1px solid #D5D9D9; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;
}
.credit-line-create-page .box-primary .box-header,
.credit-line-create-page .box-info .box-header,
.credit-line-create-page .box-success .box-header,
.credit-line-create-page .box-warning .box-header,
.credit-line-create-page .box-danger .box-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #fff !important;
    border: none !important;
    padding: 14px 20px !important;
    position: relative;
}
.credit-line-create-page .box-primary .box-header::before,
.credit-line-create-page .box-info .box-header::before,
.credit-line-create-page .box-success .box-header::before,
.credit-line-create-page .box-warning .box-header::before,
.credit-line-create-page .box-danger .box-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900;
}
.credit-line-create-page .box-title { color: #fff !important; font-weight: 600; }

.select2-container .select2-selection--single {
    height: 34px;
    line-height: 34px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 34px;
    padding-left: 12px;
}

.form-group.has-error .form-control,
.form-group.has-error .select2-container,
.form-group.has-error .form-control-file {
    border-color: #dc3545;
}

.help-block.text-red {
    color: #dc3545;
}

.info-box {
    margin-top: 15px;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 16px;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

@media (max-width: 768px) {
    .btn-lg {
        display: block;
        width: 100%;
        margin-bottom: 10px;
    }
}
</style>
@endsection
