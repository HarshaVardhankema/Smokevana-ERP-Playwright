<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\App\Http\Controllers\TicketController::class, 'store']), 'method' => 'post', 'id' => 'ticket_add_form', 'files' => true ]) !!}
        
        <div class="modal-header">
            <h4 class="modal-title">Add Ticket</h4>
            <div class="tw-flex tw-justify-end tw-gap-2" style="margin-top: -30px">
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print">Save</button>
                <button type="button" class="tw-dw-btn tw-dw-btn-danger tw-text-white no-print" data-dismiss="modal"
                    id='close_button'>Close</button>
            </div>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('lead_id', 'Lead:*') !!}
                        {!! Form::select('lead_id', $leads, null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Please Select', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('user_id', 'Assign To:*') !!}
                        {!! Form::select('user_id', $users, null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Please Select', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
            </div>

            @if(isset($hasIssueType) && $hasIssueType || isset($hasIssuePriority) && $hasIssuePriority)
            <div class="row">
                @if(isset($hasIssueType) && $hasIssueType)
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('issue_type', 'Issue Type:*') !!}
                        {!! Form::select('issue_type', [
                            'technical' => 'Technical Issue',
                            'billing' => 'Billing Issue',
                            'product' => 'Product Issue',
                            'service' => 'Service Request',
                            'complaint' => 'Complaint',
                            'inquiry' => 'General Inquiry',
                            'other' => 'Other'
                        ], null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Please Select', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                @endif
                @if(isset($hasIssuePriority) && $hasIssuePriority)
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('issue_priority', 'Issue Priority:*') !!}
                        {!! Form::select('issue_priority', [
                            'low' => 'Low',
                            'medium' => 'Medium',
                            'high' => 'High',
                            'urgent' => 'Urgent'
                        ], 'medium', ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                @endif
            </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('status', 'Status:*') !!}
                        {!! Form::select('status', [
                            'open' => 'Open',
                            'in_progress' => 'In Progress',
                            'pending' => 'Pending',
                            'resolved' => 'Resolved'
                        ], 'open', ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                @if(isset($hasInitialImage) && $hasInitialImage)
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('initial_image', 'Initial Image:') !!}
                        {!! Form::file('initial_image', ['class' => 'form-control', 'accept' => 'image/*']); !!}
                        <small class="text-muted">Upload an image related to the issue (optional)</small>
                    </div>
                </div>
                @endif
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('ticket_description', 'Ticket Description:*') !!}
                        {!! Form::textarea('ticket_description', null, ['class' => 'form-control', 'required', 'placeholder' => 'Ticket Description', 'rows' => 4]); !!}
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
        
        $('#ticket_add_form').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = new FormData(this);

            $.ajax({
                method: "POST",
                url: form.attr('action'),
                dataType: "json",
                data: formData,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success == true) {
                        $('div.ticket_modal').modal('hide');
                        toastr.success(result.msg);
                        $('#tickets_table').DataTable().ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error('Error creating ticket. Please try again.');
                    }
                }
            });
        });
    });
</script>

