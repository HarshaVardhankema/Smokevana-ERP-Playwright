<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\App\Http\Controllers\TicketController::class, 'update'], [$ticket->id]), 'method' => 'PUT', 'id' => 'ticket_edit_form', 'files' => true]) !!}

        <div class="modal-header">
            <h4 class="modal-title">Edit Ticket</h4>
            <div class="tw-flex tw-justify-end tw-gap-2" style="margin-top: -30px">
                <button type="submit"
                    class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print">Update</button>
                <button type="button" class="tw-dw-btn tw-dw-btn-danger tw-text-white no-print" data-dismiss="modal"
                    id='close_button'>Close</button>
            </div>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('lead_id', 'Lead:*') !!}
                        {!! Form::select('lead_id', $leads, $ticket->lead_id, ['class' => 'form-control select2', 'required', 'placeholder' => 'Please Select', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('user_id', 'Assign To:*') !!}
                        {!! Form::select('user_id', $users, $ticket->user_id, ['class' => 'form-control select2', 'required', 'placeholder' => 'Please Select', 'style' => 'width: 100%;']); !!}
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
                        ], $ticket->issue_type ?? null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Please Select', 'style' => 'width: 100%;']); !!}
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
                        ], $ticket->issue_priority ?? 'medium', ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
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
                            'resolved' => 'Resolved',
                            'closed' => 'Closed'
                        ], $ticket->status, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                @if(isset($hasInitialImage) && $hasInitialImage)
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('initial_image', 'Initial Image:') !!}
                        {!! Form::file('initial_image', ['class' => 'form-control', 'accept' => 'image/*']); !!}
                        @if(isset($ticket->initial_image) && $ticket->initial_image)
                            <div class="mt-2">
                                <small class="text-muted">Current: </small>
                                <a href="{{ url('uploads/tickets/' . $ticket->initial_image) }}" target="_blank">View Image</a>
                            </div>
                        @endif
                        <small class="text-muted">Upload a new image to replace the current one (optional)</small>
                    </div>
                </div>
                @endif
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('ticket_description', 'Ticket Description:*') !!}
                        {!! Form::textarea('ticket_description', $ticket->ticket_description, ['class' => 'form-control', 'required', 'placeholder' => 'Ticket Description', 'rows' => 4]); !!}
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.select2').select2();
        
        $('#ticket_edit_form').submit(function (e) {
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
                success: function (result) {
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
                        toastr.error('Error updating ticket. Please try again.');
                    }
                }
            });
        });
    });
</script>

