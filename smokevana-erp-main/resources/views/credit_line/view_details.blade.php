@extends('layouts.app')

@section('title', 'Credit Line Details')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header tw-flex tw-items-center tw-justify-between">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Credit Line Details</h1>
    <div class="tw-flex tw-gap-3">
        <a href="{{ route('credit-lines.index') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
    </div>
</section>

<!-- Main content -->
<section class="content">
    
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
                        <td>
                            @php
                                $status = $contact->credit_application_status ?? 'pending';
                                $badgeColor = $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning');
                            @endphp
                            <span class="label label-{{ $badgeColor }}">{{ ucfirst($status) }}</span>
                        </td>
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
                        <td><strong>Approved Credit Limit:</strong></td>
                        <td>
                            @if($contact->credit_application_status === 'approved' && $contact->credit_limit > 0)
                                <span class="label label-success display_currency">{{ number_format($contact->credit_limit, 2) }}</span>
                            @else
                                <span class="label label-default">Not Set</span>
                            @endif
                        </td>
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

    @component('components.widget', ['class' => 'box-info', 'title' => 'Authorized Signatory Information'])
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <tr>
                        <td><strong>Authorized Signatory Name:</strong></td>
                        <td>{{ $contact->authorized_signatory_name ?? 'Not Provided' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Authorized Signatory Email:</strong></td>
                        <td>{{ $contact->authorized_signatory_email ?? 'Not Provided' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Authorized Signatory Phone:</strong></td>
                        <td>{{ $contact->authorized_signatory_phone ?? 'Not Provided' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    @endcomponent

    @if(isset($allApplications) && $allApplications->count() > 1)
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Credit Application History'])
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> This customer has <strong>{{ $allApplications->count() }}</strong> credit application(s) on record.
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allApplications as $index => $app)
                            <tr class="{{ $app->id == $creditApplication->id ? 'info' : '' }}">
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
                                        <span class="label label-info">Current View</span>
                                    @endif
                                </td>
                                <td>
                                    @if($app->credit_application_status === 'approved')
                                        <span class="display_currency text-success"><strong>{{ number_format($contact->credit_limit ?? 0, 2) }}</strong></span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($app->id != $creditApplication->id)
                                        <a href="{{ route('credit-lines.view', $app->id) }}" class="btn btn-xs btn-primary">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    @else
                                        <span class="text-muted">Currently Viewing</span>
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

    @if($contact->supporting_documents_paths && is_array($contact->supporting_documents_paths))
    @component('components.widget', ['class' => 'box-success', 'title' => 'Supporting Documents'])
        <div class="row">
            <div class="col-md-12">
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
                                        $filePath = $doc['file_path'] ?? null;
                                    @endphp
                                    @if(isset($doc['file_path']) && !empty($doc['file_path']))
                                    <button type="button" class="btn btn-xs btn-info view-document" 
                                        data-file-path="{{ asset($filePath) }}" 
                                        data-file-name="{{ $doc['original_name'] ?? 'Document' }}"
                                        data-file-type="{{ strtolower($fileExtension) }}">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                    <a href="{{ asset($filePath) }}" download class="btn btn-xs btn-success">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                    @else
                                    <span class="text-muted">File not available</span>
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

    @php
        // Handle digital signatures data properly
        $digitalSignatures = $contact->digital_signatures_paths;
        
        // If it's a string (JSON), decode it
        if (is_string($digitalSignatures)) {
            $digitalSignatures = json_decode($digitalSignatures, true);
        }
        
        // Check if we have valid data
        $hasSignatureData = !is_null($digitalSignatures) && !empty($digitalSignatures) && is_array($digitalSignatures);
    @endphp

    @if($hasSignatureData)
    @component('components.widget', ['class' => 'box-warning', 'title' => 'Digital Signatures'])
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>File Type</th>
                                <th>File Size</th>
                                <th>Uploaded Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($digitalSignatures as $signature)
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
    @endcomponent
    @else
    @if(config('app.debug'))
    <div class="alert alert-warning">
        <strong>No Digital Signatures Found</strong><br>
        Raw Data: {{ $contact->getRawOriginal('digital_signatures_paths') ?? 'NULL' }}<br>
        Contact Model Data: {{ json_encode($contact->digital_signatures_paths) }}<br>
        Has Data: {{ $hasSignatureData ? 'Yes' : 'No' }}
    </div>
    @endif
    @endif

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

@section('css')
<style>
/* Credit Application History Table */
.table tbody tr.info {
    background-color: #d9edf7 !important;
    font-weight: 500;
}

.table tbody tr.info td {
    border-color: #bce8f1 !important;
}

.signature-display {
    padding: 20px;
    border: 2px dashed #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
}

.signature-image {
    max-width: 400px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.table-responsive {
    margin-top: 15px;
}

.btn-xs {
    padding: 2px 6px;
    font-size: 11px;
    margin-right: 5px;
}

.label {
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 3px;
}

.label-success {
    background-color: #5cb85c;
    color: white;
}

.label-danger {
    background-color: #d9534f;
    color: white;
}

.label-warning {
    background-color: #f0ad4e;
    color: white;
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
});
</script>
@endsection
