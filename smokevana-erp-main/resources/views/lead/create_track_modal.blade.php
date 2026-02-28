<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open([
            'url' => action([\App\Http\Controllers\LeadController::class, 'createTrack']),
            'method' => 'post',
            'id' => 'track_add_form',
            'files' => true,
        ]) !!}

        <div class="modal-header">
            <h4 class="modal-title">Create Track Entry</h4>
            <div class="tw-flex tw-justify-end tw-gap-2" style="margin-top: -30px">
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print">Save</button>
                <button type="button" class="tw-dw-btn tw-dw-btn-danger tw-text-white no-print" data-dismiss="modal"
                    id='close_button'>Close</button>
            </div>
        </div>
        <div class="modal-body">
            <!-- Basic Information -->
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-primary">Visit Information</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('sales_rep_id', 'Sales Rep:*') !!}
                        {!! Form::select('sales_rep_id', $salesReps->pluck('name', 'id'), null, [
                            'class' => 'form-control select2',
                            'required',
                            'placeholder' => 'Select Sales Rep',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('lead_id', 'Lead:*') !!}
                        {!! Form::select('lead_id', $leads->pluck('store_name', 'id'), null, [
                            'class' => 'form-control select2',
                            'required',
                            'placeholder' => 'Select Lead',
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('start_time', 'Start Time:*') !!}
                        <input type="datetime-local" name="start_time" id="start_time" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('duration', 'Duration (minutes):') !!}
                        {!! Form::number('duration', null, [
                            'class' => 'form-control',
                            'min' => '1',
                            'placeholder' => 'Duration in minutes',
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('status', 'Status:*') !!}
                        {!! Form::select(
                            'status',
                            [
                                '' => 'Select Status',
                                'scheduled' => 'Scheduled',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'rescheduled' => 'Rescheduled',
                                'pending' => 'Pending',
                            ],
                            '',
                            ['class' => 'form-control', 'required', 'id' => 'status'],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('visit_type', 'Visit Type:') !!}
                        {!! Form::select(
                            'visit_type',
                            [
                                '' => 'Select Visit Type',
                                'initial' => 'Initial Visit',
                                'follow_up' => 'Follow Up',
                                'demo' => 'Demo',
                                'closing' => 'Closing',
                                'meeting' => 'Meeting',
                                'support' => 'Support',
                            ],
                            '',
                            ['class' => 'form-control', 'id' => 'visit_type'],
                        ) !!}
                    </div>
                </div>
            </div>

            <!-- Proof of Visit Section -->
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-primary">Proof of Visit</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('location_proof_file', 'Location Proof (GPS/Address):') !!}
                        {!! Form::file('location_proof_file', ['class' => 'form-control', 'accept' => 'image/*,.pdf']) !!}
                        <small class="help-block">Upload GPS screenshot or address proof</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('photo_proof_file[]', 'Photo Proof:') !!}
                        {!! Form::file('photo_proof_file[]', ['class' => 'form-control', 'multiple', 'accept' => 'image/*']) !!}
                        <small class="help-block">Upload photos of the visit (multiple files allowed)</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('signature_proof_file', 'Signature Proof:') !!}
                        {!! Form::file('signature_proof_file', ['class' => 'form-control', 'accept' => 'image/*,.pdf']) !!}
                        <small class="help-block">Upload signature or agreement proof</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('video_proof_file', 'Video Proof:') !!}
                        {!! Form::file('video_proof_file', ['class' => 'form-control', 'accept' => 'video/*']) !!}
                        <small class="help-block">Upload video recording of the visit</small>
                    </div>
                </div>
            </div>

            <!-- Remarks -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('remarks', 'Remarks:') !!}
                        {!! Form::textarea('remarks', null, [
                            'class' => 'form-control',
                            'rows' => 3,
                            'placeholder' => 'Enter any additional remarks about the visit',
                        ]) !!}
                    </div>
                </div>
            </div>

        </div>
        {!! Form::close() !!}
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Set default datetime to current time
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('start_time').value = now.toISOString().slice(0, 16);

        // Initialize select2
        $('.select2').select2({
            dropdownParent: $('.modal')
        });

        // Form submission
        $('#track_add_form').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = new FormData(this);

            // Debug: Log form data
            console.log('Form data being sent:');
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            $.ajax({
                method: "POST",
                url: form.attr('action'),
                dataType: "json",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#track_add_form button[type="submit"]').prop('disabled', true).html(
                        '<i class="fa fa-spinner fa-spin"></i> Saving...');
                },
                success: function(result) {
                    console.log('Server response:', result);
                    if (result.success == true) {
                        $('div.track_modal').modal('hide');
                        toastr.success(result.msg);
                        if (typeof visits_table !== 'undefined') {
                            visits_table.ajax.reload();
                        }
                        location.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr) {
                    console.log('Error response:', xhr);
                    var errorMsg = 'An error occurred while saving the track entry.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join(
                            '<br>');
                    }
                    toastr.error(errorMsg);
                },
                complete: function() {
                    $('#track_add_form button[type="submit"]').prop('disabled', false).html(
                        'Save');
                }
            });
        });
    });
</script>
