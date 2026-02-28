@extends('layouts.app')

@section('title', 'Merchant Application Details')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Merchant Application Details</h3>
                    <div class="float-right">
                        <a href="{{ route('merchant-applications.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Status Section -->
                    <div class="section mb-4">
                        <h4>Application Status</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Status:</strong> 
                                    <span class="badge badge-{{ $application->status === 'approved' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </p>
                                <p><strong>Submitted Date:</strong> {{ $application->created_at->format('Y-m-d H:i:s') }}</p>
                                @if($application->updated_at)
                                <p><strong>Last Updated:</strong> {{ $application->updated_at->format('Y-m-d H:i:s') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Business Information Section -->
                    <div class="section mb-4">
                        <h4>Business Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Legal Business Name:</strong> {{ $application->legal_business_name }}</p>
                                <p><strong>DBA Name:</strong> {{ $application->dba_name ?? 'N/A' }}</p>
                                <p><strong>Business Type:</strong> {{ $application->business_type }}</p>
                                <p><strong>Federal Tax ID:</strong> {{ $application->federal_tax_id }}</p>
                                <p><strong>Business Age:</strong> {{ $application->business_age }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Business Phone:</strong> {{ $application->business_phone }}</p>
                                <p><strong>Website:</strong> {{ $application->website ?? 'N/A' }}</p>
                                <p><strong>Legal Address:</strong><br>
                                    {{ $application->legal_address }}<br>
                                    {{ $application->legal_city }}, {{ $application->legal_state }} {{ $application->legal_zip }}
                                </p>
                                @if($application->dba_name)
                                <p><strong>DBA Address:</strong><br>
                                    {{ $application->dba_address }}<br>
                                    {{ $application->dba_city }}, {{ $application->dba_state }} {{ $application->dba_zip }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Owner Information Section -->
                    <div class="section mb-4">
                        <h4>Owner Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Legal Name:</strong> {{ $application->owner_legal_name }}</p>
                                <p><strong>Ownership Percentage:</strong> {{ $application->ownership_percentage }}%</p>
                                <p><strong>Job Title:</strong> {{ $application->job_title }}</p>
                                <p><strong>Date of Birth:</strong> {{ $application->date_of_birth->format('Y-m-d') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email:</strong> {{ $application->owner_email }}</p>
                                <p><strong>Phone:</strong> {{ $application->owner_phone }}</p>
                                <p><strong>Home Address:</strong><br>
                                    {{ $application->owner_address }}<br>
                                    {{ $application->owner_city }}, {{ $application->owner_state }} {{ $application->owner_zip }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Previous Processing Section -->
                    @if($application->has_previous_processing)
                    <div class="section mb-4">
                        <h4>Previous Processing Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Processing Duration:</strong> {{ $application->processing_duration }}</p>
                                <p><strong>Previous Processor:</strong> {{ $application->previous_processor }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Average Ticket Amount:</strong> ${{ number_format($application->average_ticket_amount, 2) }}</p>
                                <p><strong>Monthly Volume:</strong> ${{ number_format($application->monthly_volume, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Additional Owners Section -->
                    @if($application->additional_owners)
                    <div class="section mb-4">
                        <h4>Additional Owners</h4>
                        @foreach($application->additional_owners as $index => $owner)
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5>Owner {{ $index + 1 }}</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> {{ $owner['name'] }}</p>
                                        <p><strong>Ownership Percentage:</strong> {{ $owner['percentage'] }}%</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Date of Birth:</strong> {{ $owner['dob'] }}</p>
                                        <p><strong>SSN:</strong> {{ $owner['ssn'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Documents Section -->
                    <div class="section mb-4">
                        <h4>Submitted Documents</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Voided Check</h5>
                                        <a href="{{ Storage::url($application->voided_check_path) }}" target="_blank" class="btn btn-primary">View Document</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Driver's License/ID</h5>
                                        <a href="{{ Storage::url($application->driver_license_path) }}" target="_blank" class="btn btn-primary">View Document</a>
                                    </div>
                                </div>
                            </div>
                            @if($application->processing_statements_path)
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Processing Statements</h5>
                                        <a href="{{ Storage::url($application->processing_statements_path) }}" target="_blank" class="btn btn-primary">View Document</a>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Admin Notes Section -->
                    @if($application->admin_notes || $application->admin_response)
                    <div class="section mb-4">
                        <h4>Admin Notes</h4>
                        @if($application->admin_notes)
                        <div class="card mb-3">
                            <div class="card-header">Review Notes</div>
                            <div class="card-body">
                                {{ $application->admin_notes }}
                            </div>
                        </div>
                        @endif
                        @if($application->admin_response)
                        <div class="card">
                            <div class="card-header">Response to Applicant</div>
                            <div class="card-body">
                                {{ $application->admin_response }}
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    @if($application->status === 'pending')
                    <div class="text-center">
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#approveModal">
                            Approve Application
                        </button>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                            Reject Application
                        </button>
                    </div>

                    <!-- Approve Modal -->
                    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('merchant-applications.update', $application->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    
                                    <div class="modal-header">
                                        <h5 class="modal-title">Approve Application</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="admin_notes">Review Notes *</label>
                                            <textarea class="form-control" name="admin_notes" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="admin_response">Response to Applicant *</label>
                                            <textarea class="form-control" name="admin_response" required></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Approve Application</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('merchant-applications.update', $application->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reject Application</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="admin_notes">Review Notes *</label>
                                            <textarea class="form-control" name="admin_notes" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="admin_response">Response to Applicant *</label>
                                            <textarea class="form-control" name="admin_response" required></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-danger">Reject Application</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Handle form submissions
    $('form').submit(function(e) {
        e.preventDefault();
        
        const form = $(this);
        const url = form.attr('action');
        const formData = form.serialize();
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Application updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating application: ' + response.message);
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMessage = 'Please fix the following errors:\n\n';
                
                for (const field in errors) {
                    errorMessage += `${field}: ${errors[field].join(', ')}\n`;
                }
                
                alert(errorMessage);
            }
        });
    });
});
</script>
@endpush

@endsection 