@extends('layouts.app')

@section('title', 'Credit Line Approval')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header tw-flex tw-items-center tw-justify-between">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Credit Line Approval</h1>
    <div class="tw-flex tw-gap-3">
        <button type="submit" form="creditApprovalForm" class="btn btn-success">
            <i class="fa fa-check"></i> Approve Credit Line
        </button>
    </div>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => route('credit-lines.process-approval', $creditApplication->id), 'method' => 'post', 'id' => 'creditApprovalForm']) !!}
    
    @php
        // Get all applications for this customer
        $allApplications = \App\Models\CreditApplication::where('contact_id', $contact->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $hasMultipleApplications = $allApplications->count() > 1;
        $previousApprovedApp = $allApplications->where('credit_application_status', 'approved')->first();
        $currentCreditLimit = $contact->credit_limit ?? 0;
    @endphp

    @if($hasMultipleApplications)
    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="fa fa-info-circle"></i> Multiple Applications Notice</h4>
        <p><strong>This customer has {{ $allApplications->count() }} credit application(s) on record.</strong></p>
        @if($previousApprovedApp && $currentCreditLimit > 0)
        <p>
            <i class="fa fa-history"></i> Previous Approved Limit: <strong class="display_currency">{{ number_format($currentCreditLimit, 2) }}</strong><br>
            <small class="text-muted">This is a credit limit increase request. The new approved amount should be greater than the previous limit.</small>
        </p>
        @endif
    </div>
    @endif
    
    @component('components.widget', ['class' => 'box-success', 'title' => 'Credit Approval'])
        <div class="row">
            <div class="col-md-6">
                <div class="form-group @error('approve_credit_limit') has-error @enderror">
                    {!! Form::label('approve_credit_limit', 'Approve Credit Limit *') !!}
                    {!! Form::number('approve_credit_limit', old('approve_credit_limit', $contact->requested_credit_amount), ['class' => 'form-control ' . ($errors->has('approve_credit_limit') ? 'parsley-error' : ''), 'placeholder' => 'Enter approved credit limit', 'required', 'step' => '0.01', 'min' => '0', 'id' => 'approve_credit_limit_input']) !!}
                    @error('approve_credit_limit')
                        <span class="help-block text-red">{{ $message }}</span>
                    @enderror
                    @if($currentCreditLimit > 0)
                    <small class="help-block text-warning">
                        <i class="fa fa-info-circle"></i> <strong>Note:</strong> Customer's current approved limit is {{ number_format($currentCreditLimit, 2) }}. 
                        If this is a limit increase request, the new amount should typically be higher.
                    </small>
                    @endif
                    <small class="help-block text-muted">
                        <i class="fa fa-lightbulb-o"></i> Requested Amount: <strong class="display_currency">{{ number_format($contact->requested_credit_amount ?? 0, 2) }}</strong>
                    </small>
                </div>
            </div>
        </div>
    @endcomponent

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
                        <td><strong>Status:</strong></td>
                        <td><span class="label label-warning">{{ ucfirst($contact->credit_application_status ?? 'pending') }}</span></td>
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
                        <td><strong>Current Credit Limit:</strong></td>
                        <td><span class="display_currency">{{ number_format($contact->credit_limit ?? 0, 2) }}</span></td>
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

    @if($hasMultipleApplications)
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Credit Application History'])
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning">
                    <i class="fa fa-history"></i> <strong>Review Previous Applications:</strong> This section shows all credit applications for this customer to help you make an informed approval decision.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Application Date</th>
                                <th>Requested Amount</th>
                                <th>Avg Monthly Revenue</th>
                                <th>Status</th>
                                <th>Approved Limit</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allApplications as $index => $app)
                            <tr class="{{ $app->id == $creditApplication->id ? 'warning' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $app->created_at->format('Y-m-d H:i:s') }}</td>
                                <td><span class="display_currency">{{ number_format($app->requested_credit_amount, 2) }}</span></td>
                                <td><span class="display_currency">{{ number_format($app->average_revenue_per_month, 2) }}</span></td>
                                <td>
                                    @php
                                        $status = $app->credit_application_status ?? 'pending';
                                        $badgeColor = $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning');
                                    @endphp
                                    <span class="label label-{{ $badgeColor }}">{{ ucfirst($status) }}</span>
                                    @if($app->id == $creditApplication->id)
                                        <span class="label label-info">Current</span>
                                    @endif
                                </td>
                                <td>
                                    @if($app->credit_application_status === 'approved')
                                        <span class="display_currency text-success"><strong>{{ number_format($currentCreditLimit, 2) }}</strong></span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($app->id == $creditApplication->id)
                                        <span class="text-info"><i class="fa fa-arrow-right"></i> Reviewing Now</span>
                                    @elseif($app->credit_application_status === 'approved')
                                        <span class="text-success"><i class="fa fa-check"></i> Previously Approved</span>
                                    @elseif($app->credit_application_status === 'rejected')
                                        <span class="text-danger"><i class="fa fa-times"></i> Previously Rejected</span>
                                    @else
                                        <span class="text-muted">Old Application</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endcomponent
    @endif

    @component('components.widget', ['class' => 'box-info', 'title' => 'Supporting Documents'])
        @if($contact->supporting_documents_paths && is_array($contact->supporting_documents_paths))
            <div class="row">
                <div class="col-md-12">
                    <h5><strong>Uploaded Documents:</strong></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Document Name</th>
                                    <th>File Type</th>
                                    <th>File Size</th>
                                    <th>Uploaded Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contact->supporting_documents_paths as $doc)
                                <tr>
                                    <td>{{ $doc['original_name'] ?? 'Unknown' }}</td>
                                    <td>{{ $doc['file_type'] ?? 'Unknown' }}</td>
                                    <td>{{ isset($doc['file_size']) ? round($doc['file_size'] / 1024, 2) . ' KB' : 'Unknown' }}</td>
                                    <td>{{ isset($doc['uploaded_at']) ? \Carbon\Carbon::parse($doc['uploaded_at'])->format('Y-m-d H:i:s') : 'Unknown' }}</td>
                                    <td>
                                        @php
                                            $fileExtension = $doc['file_type'] ?? '';
                                            // If file_type is empty, try to get extension from file path
                                            if (empty($fileExtension) && isset($doc['file_path'])) {
                                                $fileExtension = pathinfo($doc['file_path'], PATHINFO_EXTENSION);
                                            }
                                            // If still empty, try from original name
                                            if (empty($fileExtension) && isset($doc['original_name'])) {
                                                $fileExtension = pathinfo($doc['original_name'], PATHINFO_EXTENSION);
                                            }
                                        @endphp
                                        <button type="button" class="btn btn-xs btn-info view-document" 
                                            data-file-path="{{ asset($doc['file_path']) }}" 
                                            data-file-name="{{ $doc['original_name'] ?? 'Document' }}"
                                            data-file-type="{{ strtolower($fileExtension) }}">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                        <a href="{{ asset($doc['file_path']) }}" download class="btn btn-xs btn-success">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> No supporting documents uploaded.
            </div>
        @endif
    @endcomponent

    @component('components.widget', ['class' => 'box-info', 'title' => 'Authorized Signatory Information'])
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('authorized_signatory_name', 'Signatory Name') !!}
                    {!! Form::text('authorized_signatory_name', $contact->authorized_signatory_name ?? 'Not Provided', ['class' => 'form-control', 'readonly' => true, 'style' => 'background-color: #f5f5f5;']) !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('authorized_signatory_email', 'Signatory Email') !!}
                    {!! Form::email('authorized_signatory_email', $contact->authorized_signatory_email ?? 'Not Provided', ['class' => 'form-control', 'readonly' => true, 'style' => 'background-color: #f5f5f5;']) !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('authorized_signatory_phone', 'Signatory Phone') !!}
                    {!! Form::text('authorized_signatory_phone', $contact->authorized_signatory_phone ?? 'Not Provided', ['class' => 'form-control', 'readonly' => true, 'style' => 'background-color: #f5f5f5;']) !!}
                </div>
            </div>
        </div>

        @if($contact->digital_signatures_paths && is_array($contact->digital_signatures_paths))
        <div class="row">
            <div class="col-md-12">
                <h4><strong>Digital Signatures</strong></h4>
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Signature File</th>
                                <th>File Type</th>
                                <th>File Size</th>
                                <th>Uploaded Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contact->digital_signatures_paths as $signature)
                            <tr>
                                <td>{{ $signature['original_name'] ?? 'Unknown' }}</td>
                                <td>{{ $signature['file_type'] ?? 'Unknown' }}</td>
                                <td>{{ isset($signature['file_size']) ? round($signature['file_size'] / 1024, 2) . ' KB' : 'Unknown' }}</td>
                                <td>{{ isset($signature['uploaded_at']) ? \Carbon\Carbon::parse($signature['uploaded_at'])->format('Y-m-d H:i:s') : 'Unknown' }}</td>
                                <td>
                                    @php
                                        $sigExtension = $signature['file_type'] ?? '';
                                        // If file_type is empty, try to get extension from file path
                                        if (empty($sigExtension) && isset($signature['file_path'])) {
                                            $sigExtension = pathinfo($signature['file_path'], PATHINFO_EXTENSION);
                                        }
                                        // If still empty, try from original name
                                        if (empty($sigExtension) && isset($signature['original_name'])) {
                                            $sigExtension = pathinfo($signature['original_name'], PATHINFO_EXTENSION);
                                        }
                                    @endphp
                                    <button type="button" class="btn btn-xs btn-info view-document" 
                                        data-file-path="{{ asset($signature['file_path']) }}" 
                                        data-file-name="{{ $signature['original_name'] ?? 'Signature' }}"
                                        data-file-type="{{ strtolower($sigExtension) }}">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                    <a href="{{ asset($signature['file_path']) }}" download class="btn btn-xs btn-success">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> No digital signatures uploaded.
                </div>
            </div>
        </div>
        @endif
    @endcomponent
    
    {!! Form::close() !!}

    <!-- Document Viewer Modal -->
    <div class="modal fade" id="documentViewerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1200px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="documentModalTitle">Document Viewer</h4>
                </div>
                <div class="modal-body" style="padding: 0; min-height: 500px;">
                    <div id="documentViewerContent" style="width: 100%; height: 600px; display: flex; align-items: center; justify-content: center;">
                        <div class="loader">
                            <i class="fa fa-spinner fa-spin fa-3x"></i>
                            <p>Loading document...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="downloadDocumentBtn" class="btn btn-success" download>
                        <i class="fa fa-download"></i> Download
                    </a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</section>

@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Handle view document button click
    $('.view-document').on('click', function() {
        var filePath = $(this).data('file-path');
        var fileName = $(this).data('file-name');
        var fileType = $(this).data('file-type');
        
        // Debug: Log the values
        console.log('File Path:', filePath);
        console.log('File Name:', fileName);
        console.log('File Type:', fileType);
        
        // Ensure fileType is a string and lowercase
        fileType = String(fileType || '').toLowerCase().trim();
        
        // If no file type, try to extract from file path
        if (!fileType && filePath) {
            var pathParts = filePath.split('.');
            fileType = pathParts[pathParts.length - 1].toLowerCase();
        }
        
        console.log('Final File Type:', fileType);
        
        // Set modal title and download button
        $('#documentModalTitle').text(fileName);
        $('#downloadDocumentBtn').attr('href', filePath);
        
        // Show modal
        $('#documentViewerModal').modal('show');
        
        // Get content container
        var contentDiv = $('#documentViewerContent');
        
        // Show loader
        contentDiv.html('<div class="loader"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading document...</p></div>');
        
        // Determine file type and display accordingly
        setTimeout(function() {
            var content = '';
            
            console.log('Rendering for file type:', fileType);
            
            // Image files
            if (['png', 'jpg', 'jpeg', 'gif', 'bmp', 'svg', 'webp', 'avif'].includes(fileType)) {
                content = '<div style="display: flex; justify-content: center; align-items: center; height: 600px; background: #f5f5f5;">' +
                         '<img src="' + filePath + '" alt="' + fileName + '" style="max-width: 100%; max-height: 600px; object-fit: contain;">' +
                         '</div>';
            }
            // PDF files
            else if (fileType === 'pdf') {
                content = '<iframe src="' + filePath + '#view=FitH" style="width: 100%; height: 600px; border: none;"></iframe>';
            }
            // Microsoft Office files (Word, Excel, PowerPoint)
            else if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(fileType)) {
                // Use Google Docs Viewer as fallback if Office viewer doesn't work
                content = '<iframe src="https://docs.google.com/gview?url=' + encodeURIComponent(filePath) + '&embedded=true" style="width: 100%; height: 600px; border: none;"></iframe>';
            }
            // Text files
            else if (['txt', 'csv', 'log', 'json', 'xml'].includes(fileType)) {
                content = '<iframe src="' + filePath + '" style="width: 100%; height: 600px; border: none;"></iframe>';
            }
            // Try generic iframe for any other file
            else {
                content = '<iframe src="' + filePath + '" style="width: 100%; height: 600px; border: none;"></iframe>' +
                         '<div style="padding: 20px; text-align: center; background: #fff;">' +
                         '<p class="text-warning"><i class="fa fa-exclamation-triangle"></i> If the document doesn\'t display properly, please download it to view. (File type: ' + fileType + ')</p>' +
                         '</div>';
            }
            
            contentDiv.html(content);
            
            // Handle iframe load errors
            contentDiv.find('iframe').on('error', function() {
                contentDiv.html('<div class="document-error">' +
                    '<i class="fa fa-exclamation-circle"></i>' +
                    '<h4>Unable to preview this file</h4>' +
                    '<p>This file type (' + fileType + ') may not be supported for preview. Please download the file to view it.</p>' +
                    '</div>');
            });
        }, 300);
    });
    
    // Clear content when modal is closed
    $('#documentViewerModal').on('hidden.bs.modal', function() {
        $('#documentViewerContent').html('<div class="loader"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading document...</p></div>');
    });

    // Form validation and submission
    $('#creditApprovalForm').on('submit', function(e) {
        e.preventDefault();
        
        var validated = true;
        var errorMessages = [];
        var warningMessages = [];
        
        // Clear previous error states
        $('.parsley-error').removeClass('parsley-error');
        $('.help-block').remove();
        
        // Get values
        var creditLimit = parseFloat($('input[name="approve_credit_limit"]').val());
        var currentCreditLimit = {{ $currentCreditLimit ?? 0 }};
        var requestedAmount = {{ $contact->requested_credit_amount ?? 0 }};
        
        // Validate credit limit
        if (isNaN(creditLimit) || creditLimit <= 0) {
            $('input[name="approve_credit_limit"]').addClass('parsley-error');
            errorMessages.push('Please enter a valid credit limit amount greater than 0.');
            validated = false;
        }
        
        // Warning if approved amount is less than current limit (for increase requests)
        if (validated && currentCreditLimit > 0 && creditLimit < currentCreditLimit) {
            warningMessages.push('The approved amount (' + creditLimit.toFixed(2) + ') is less than the current credit limit (' + currentCreditLimit.toFixed(2) + '). This will decrease the customer\'s credit limit.');
        }
        
        // Warning if approved amount is significantly different from requested amount
        if (validated && Math.abs(creditLimit - requestedAmount) > (requestedAmount * 0.5)) {
            warningMessages.push('The approved amount differs significantly from the requested amount (' + requestedAmount.toFixed(2) + ').');
        }
        
        if (!validated) {
            // Show error messages
            errorMessages.forEach(function(message) {
                toastr.error(message);
            });
            return false;
        }
        
        // If there are warnings, show confirmation dialog
        if (warningMessages.length > 0) {
            var warningText = warningMessages.join('\n\n');
            swal({
                title: 'Please Confirm',
                text: warningText + '\n\nDo you want to proceed with this approval?',
                icon: 'warning',
                buttons: {
                    cancel: 'Review Again',
                    confirm: {
                        text: 'Yes, Proceed',
                        className: 'btn-success'
                    }
                },
                dangerMode: true,
            }).then((willProceed) => {
                if (willProceed) {
                    submitApprovalForm();
                }
            });
            return false;
        }
        
        // No warnings, proceed with submission
        submitApprovalForm();
    });
    
    // Function to submit the approval form
    function submitApprovalForm() {
        var $form = $('#creditApprovalForm');
        var submitBtn = $('button[type="submit"]');
        var originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
        
        // Submit form via AJAX
        var formData = new FormData($form[0]);
        
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.msg || 'Credit application approved successfully!');
                setTimeout(function() {
                    if (response.redirect_url) {
                        window.location.href = response.redirect_url;
                    } else {
                        window.location.href = '{{ route("credit-lines.index") }}';
                    }
                }, 1000);
            },
            error: function(xhr) {
                var errorMessage = 'Something went wrong while processing the request.';
                
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMessage = xhr.responseJSON.msg;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    for (var field in errors) {
                        if (errors.hasOwnProperty(field)) {
                            errorMessage = errors[field][0];
                            break;
                        }
                    }
                }
                
                toastr.error(errorMessage);
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    }
});
</script>
@endsection

@section('css')
<style>
/* Credit Application History Table */
.table tbody tr.warning {
    background-color: #fcf8e3 !important;
    font-weight: 500;
}

.table tbody tr.warning td {
    border-color: #faebcc !important;
}

.help-block.text-warning {
    color: #f0ad4e;
    font-weight: 500;
    background-color: #fcf8e3;
    padding: 8px 12px;
    border-radius: 4px;
    border-left: 3px solid #f0ad4e;
    margin-top: 8px;
}

.signature-panel {
    border: 2px dashed #ddd;
    padding: 20px;
    border-radius: 8px;
    background-color: #f9f9f9;
}

.signature-canvas {
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    cursor: crosshair;
    max-width: 100%;
}

.signature-controls {
    margin-top: 10px;
}

.table-responsive {
    margin-top: 15px;
}

.btn-xs {
    padding: 2px 6px;
    font-size: 11px;
    margin-right: 5px;
}

/* Document Viewer Modal Styles */
#documentViewerModal .modal-dialog {
    margin: 30px auto;
}

#documentViewerContent {
    background-color: #f5f5f5;
    position: relative;
}

#documentViewerContent iframe {
    width: 100%;
    height: 100%;
    border: none;
}

#documentViewerContent img {
    max-width: 100%;
    max-height: 600px;
    object-fit: contain;
}

.loader {
    text-align: center;
    color: #666;
}

.loader i {
    color: #3c8dbc;
    margin-bottom: 15px;
}

.document-error {
    padding: 40px;
    text-align: center;
    color: #d9534f;
}

.document-error i {
    font-size: 48px;
    margin-bottom: 20px;
}
</style>
@endsection