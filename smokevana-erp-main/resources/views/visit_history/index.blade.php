@extends('layouts.app')
@section('title', 'Visit History')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Visit History
        <small>Sales Rep Visit Tracking</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

    <!-- Filters -->
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-warning', 'title' => 'Filters'])
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Sales Rep:</label>
                            {!! Form::select('sales_rep_filter', $salesReps, null, ['class' => 'form-control select2', 'id' => 'sales_rep_filter', 'placeholder' => 'All Sales Reps', 'style' => 'width: 100%;']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status:</label>
                            <select class="form-control" id="status_filter">
                                <option value="">All Status</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date From:</label>
                            <input type="date" class="form-control" id="date_from_filter">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date To:</label>
                            <input type="date" class="form-control" id="date_to_filter">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary" id="apply_filters">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                        <button type="button" class="btn btn-default" id="clear_filters">
                            <i class="fa fa-times"></i> Clear Filters
                        </button>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    <!-- Visit History Table -->
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-success', 'title' => 'Visit History'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="visit_history_table">
                        <thead>
                            <tr>
                                <th>Visit Ref</th>
                                <th>Lead Info</th>
                                <th>Sales Rep</th>
                                <th>Location</th>
                                <th>Visit Time</th>
                                <th>Duration</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2();

        // Visit History DataTable
        var visit_history_table = $('#visit_history_table').DataTable({
            processing: true,
            serverSide: true,
            scrollY: "60vh",
            scrollX: true,
            scrollCollapse: false,
            ajax: {
                url: "{{ route('visit-history.index') }}",
                data: function(d) {
                    d.sales_rep_id = $('#sales_rep_filter').val();
                    d.status = $('#status_filter').val();
                    d.date_from = $('#date_from_filter').val();
                    d.date_to = $('#date_to_filter').val();
                }
            },
            columnDefs: [
                {
                    targets: [1, 2, 3, 4, 5, 6, 7],
                    orderable: false,
                    searchable: false,
                },
                {
                    targets: 8,
                    orderable: false,
                    searchable: false,
                }
            ],
            columns: [
                { data: 'visit_reference', name: 'visit_reference' },
                { data: 'lead_info', name: 'lead_info' },
                { data: 'sales_rep_name', name: 'sales_rep_name' },
                { data: 'location', name: 'location' },
                { data: 'visit_time', name: 'visit_time' },
                { data: 'duration', name: 'duration' },
                { data: 'visit_type', name: 'visit_type' },
                { data: 'status_badge', name: 'status' },
                { data: 'action', name: 'action' }
            ],
            order: [[0, 'desc']],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#visit_history_table'));
            }
        });

        // Apply filters
        $('#apply_filters').click(function() {
            visit_history_table.ajax.reload();
        });

        // Clear filters
        $('#clear_filters').click(function() {
            $('#sales_rep_filter').val('').trigger('change');
            $('#status_filter').val('');
            $('#date_from_filter').val('');
            $('#date_to_filter').val('');
            visit_history_table.ajax.reload();
        });

        // View visit details
        $(document).on('click', '.view-details', function() {
            const visitId = $(this).data('id');
            
            $.ajax({
                url: "{{ route('visit-history.show', '') }}/" + visitId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const visit = response.data;
                        const proofs = visit.proofs || {};
                        
                        // Build photos gallery HTML
                        let photosHtml = '';
                        if (proofs.has_photo_proof && proofs.photo_proof_paths && proofs.photo_proof_paths.length > 0) {
                            photosHtml = '<div class="row"><div class="col-md-12"><h4>Visit Photos</h4><div class="row">';
                            proofs.photo_proof_paths.forEach(function(photoPath) {
                                photosHtml += `
                                    <div class="col-md-3 col-sm-4 col-xs-6" style="margin-bottom: 15px;">
                                        <a href="/uploads/visits/${photoPath}" target="_blank" class="thumbnail">
                                            <img src="/uploads/visits/${photoPath}" alt="Visit Photo" style="width: 100%; height: 150px; object-fit: cover;">
                                        </a>
                                    </div>
                                `;
                            });
                            photosHtml += '</div></div></div>';
                        }
                        
                        // Build location proof HTML
                        let locationProofHtml = '';
                        if (proofs.has_location_proof && proofs.location_proof_path) {
                            locationProofHtml = `
                                <div class="col-md-6">
                                    <h4>Location Proof</h4>
                                    <a href="/uploads/visits/${proofs.location_proof_path}" target="_blank" class="thumbnail">
                                        <img src="/uploads/visits/${proofs.location_proof_path}" alt="Location Proof" style="width: 100%; max-height: 200px; object-fit: contain;">
                                    </a>
                                </div>
                            `;
                        }
                        
                        // Build signature proof HTML
                        let signatureProofHtml = '';
                        if (proofs.has_signature_proof && proofs.signature_proof_path) {
                            signatureProofHtml = `
                                <div class="col-md-6">
                                    <h4>Signature Proof</h4>
                                    <a href="/uploads/visits/${proofs.signature_proof_path}" target="_blank" class="thumbnail">
                                        <img src="/uploads/visits/${proofs.signature_proof_path}" alt="Signature" style="width: 100%; max-height: 200px; object-fit: contain;">
                                    </a>
                                </div>
                            `;
                        }
                        
                        // Build video proof HTML
                        let videoProofHtml = '';
                        if (proofs.has_video_proof && proofs.video_proof_path) {
                            videoProofHtml = `
                                <div class="col-md-12">
                                    <h4>Video Proof</h4>
                                    <video controls style="width: 100%; max-height: 400px;">
                                        <source src="/uploads/visits/${proofs.video_proof_path}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <p><a href="/uploads/visits/${proofs.video_proof_path}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-download"></i> Download Video</a></p>
                                </div>
                            `;
                        }
                        
                        let detailsHtml = `
                            <div class="modal fade" id="visit_details_modal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" style="width: 90%; max-width: 1200px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Visit Details - ${visit.visit_reference || 'N/A'}</h4>
                                        </div>
                                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h4>Lead Information</h4>
                                                    <p><strong>Reference:</strong> ${visit.lead.reference_no}</p>
                                                    <p><strong>Store Name:</strong> ${visit.lead.store_name}</p>
                                                    <p><strong>Contact:</strong> ${visit.lead.contact_name}</p>
                                                    <p><strong>Phone:</strong> ${visit.lead.contact_phone}</p>
                                                    <p><strong>Address:</strong> ${visit.lead.address || 'N/A'}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h4>Visit Information</h4>
                                                    <p><strong>Sales Rep:</strong> ${visit.sales_rep.name}</p>
                                                    <p><strong>Email:</strong> ${visit.sales_rep.email}</p>
                                                    <p><strong>Start Time:</strong> ${visit.start_time || 'N/A'}</p>
                                                    <p><strong>End Time:</strong> ${visit.checkout_time || 'N/A'}</p>
                                                    <p><strong>Duration:</strong> ${visit.duration || 'N/A'}</p>
                                                    <p><strong>Type:</strong> ${visit.visit_type || 'N/A'}</p>
                                                    <p><strong>Status:</strong> ${visit.status}</p>
                                                </div>
                                            </div>
                                            ${visit.remarks ? `<div class="row"><div class="col-md-12"><h4>Remarks</h4><p>${visit.remarks}</p></div></div>` : ''}
                                            <hr>
                                            ${photosHtml}
                                            ${locationProofHtml || signatureProofHtml ? '<div class="row">' + locationProofHtml + signatureProofHtml + '</div>' : ''}
                                            ${videoProofHtml ? '<div class="row">' + videoProofHtml + '</div>' : ''}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Remove existing modal if any
                        $('#visit_details_modal').remove();
                        
                        // Add and show modal
                        $('body').append(detailsHtml);
                        $('#visit_details_modal').modal('show');
                    }
                },
                error: function() {
                    toastr.error('Failed to load visit details');
                }
            });
        });
    });
</script>
@endsection

