@extends('layouts.app')

@section('title', 'Edit Credit Limit')

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

<!-- Content Header (Page header) -->
<section class="content-header tw-flex tw-items-center tw-justify-between">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Edit Credit Limit</h1>
    <div class="tw-flex tw-gap-3">
        <button type="submit" form="creditLimitEditForm" class="btn btn-success">
            <i class="fa fa-save"></i> Update Credit Limit
        </button>
        <a href="{{ route('credit-lines.index') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
    </div>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => route('credit-lines.update', $contact->id), 'method' => 'put', 'id' => 'creditLimitEditForm']) !!}
    
    @component('components.widget', ['class' => 'box-info', 'title' => 'Customer Information'])
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <td><strong>Customer Name:</strong></td>
                        <td>{{ $contact->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Business Name:</strong></td>
                        <td>{{ $contact->supplier_business_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $contact->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td>{{ $contact->mobile ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Application Status:</strong></td>
                        <td><span class="label label-success">{{ ucfirst($contact->credit_application_status ?? 'approved') }}</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <td><strong>Requested Credit Amount:</strong></td>
                        <td><span class="display_currency">{{ number_format($contact->requested_credit_amount ?? 0, 2) }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Average Monthly Revenue:</strong></td>
                        <td><span class="display_currency">{{ number_format($contact->average_revenue_per_month ?? 0, 2) }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Applied Date:</strong></td>
                        <td>{{ $contact->updated_at ? $contact->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-success', 'title' => 'Current Credit Information'])
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <td><strong>Authorized Signatory:</strong></td>
                        <td>{{ $contact->authorized_signatory_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Signatory Email:</strong></td>
                        <td>{{ $contact->authorized_signatory_email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Signatory Phone:</strong></td>
                        <td>{{ $contact->authorized_signatory_phone ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="label label-success">{{ ucfirst($contact->credit_application_status) }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Last Updated:</strong></td>
                        <td>{{ $contact->updated_at ? $contact->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    @endcomponent

     @component('components.widget', ['class' => 'box-warning', 'title' => 'Edit Credit Limit'])
         <div class="row">
             <div class="col-md-6">
                 <div class="form-group @error('credit_limit') has-error @enderror">
                     {!! Form::label('credit_limit', 'Current Credit Limit *') !!}
                     {!! Form::number('credit_limit', old('credit_limit', $contact->credit_limit), ['class' => 'form-control ' . ($errors->has('credit_limit') ? 'parsley-error' : ''), 'placeholder' => 'Enter new credit limit', 'required', 'step' => '0.01', 'min' => '0']) !!}
                     @error('credit_limit')
                         <span class="help-block text-red">{{ $message }}</span>
                     @enderror
                     <small class="help-block">
                         Current limit: <span class="display_currency">{{ number_format($contact->credit_limit ?? 0, 2) }}</span>
                     </small>
                 </div>
             </div>
         </div>
         
         <input type="hidden" name="old_credit_limit" value="{{ $contact->credit_limit }}">
     @endcomponent
    
    {!! Form::close() !!}
</section>

@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Form validation and submission
    $('#creditLimitEditForm').on('submit', function(e) {
        var newLimit = parseFloat($('input[name="credit_limit"]').val());
        var validated = true;
        
        // Clear previous errors
        $('.parsley-error').removeClass('parsley-error');
        $('.help-block').remove();
        
        // Validate credit limit
        if (isNaN(newLimit) || newLimit <= 0) {
            $('input[name="credit_limit"]').addClass('parsley-error');
            toastr.error('Please enter a valid credit limit amount greater than 0.');
            validated = false;
        }
        
        if (!validated) {
            e.preventDefault();
            toastr.error('Please fix the validation errors.');
            return false;
        }
        
        // Show loading state
        var submitBtn = $('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        
        // Re-enable button after a short delay in case of errors
        setTimeout(function() {
            submitBtn.prop('disabled', false).html(originalText);
        }, 5000);
    });
});
</script>
@endsection

@section('css')
<style>
.info-box {
    margin-top: 15px;
}

.change-positive {
    color: #28a745;
}

.change-negative {
    color: #dc3545;
}

.change-neutral {
    color: #6c757d;
}

.form-group.has-error .form-control {
    border-color: #dc3545;
}

.help-block.text-red {
    color: #dc3545;
}
</style>
@endsection
