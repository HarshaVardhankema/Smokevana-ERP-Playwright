<div class="modal-dialog no-print  modal-md" id='metrix_modal' role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">Sales Notes</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="tw-flex tw-justify-between tw-items-center tw-mb-2">
                        <strong>{{ __('sale.sell_note') }}:</strong>
                        <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary tw-text-white" id="edit_note_btn">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                    </div>
                    <div id="note_display" class="well well-sm no-shadow bg-gray" style="min-height: 100px; white-space: pre-wrap; word-wrap: break-word;">
                        @if ($sell->additional_notes)
                            {{ $sell->additional_notes }}
                        @else
                            --
                        @endif
                    </div>
                    <div id="note_edit" style="display: none;">
                        <textarea id="note_textarea" class="form-control" rows="5" style="resize: vertical;">{{ $sell->additional_notes ?? '' }}</textarea>
                    </div>
                </div>
                {{-- <div class="col-sm-6">
                    <strong>{{ __('sale.staff_note') }}:</strong><br>
                    <p class="well well-sm no-shadow bg-gray">
                        @if ($sell->staff_note)
                            {!! nl2br($sell->staff_note) !!}
                        @else
                            --
                        @endif
                    </p>
                </div> --}}
            </div>
        </div>
        <div class="modal-footer">
            <div id="edit_actions" style="display: none;">
                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print" id="apply_note_btn">
                    <i class="fa fa-check"></i> Apply
                </button>
                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" id="cancel_edit_btn">
                    Cancel
                </button>
            </div>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print"
            id='close_button'
                data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var transactionId = {{ $sell->id }};
    var originalNote = $('#note_textarea').val();
    
    // Edit button click
    $('#edit_note_btn').on('click', function() {
        $('#note_display').hide();
        $('#note_edit').show();
        $('#edit_actions').show();
        $('#close_button').hide();
        $('#edit_note_btn').hide();
        $('#note_textarea').focus();
    });
    
    // Cancel button click
    $('#cancel_edit_btn').on('click', function() {
        $('#note_textarea').val(originalNote);
        $('#note_display').show();
        $('#note_edit').hide();
        $('#edit_actions').hide();
        $('#close_button').show();
        $('#edit_note_btn').show();
    });
    
    // Apply button click
    $('#apply_note_btn').on('click', function() {
        var newNote = $('#note_textarea').val();
        var btn = $(this);
        var originalHtml = btn.html();
        
        // Disable button and show loading
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: '/sells/update-note/' + transactionId,
            method: 'POST',
            data: {
                additional_notes: newNote,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Update display - preserve line breaks
                    var displayText = newNote || '--';
                    $('#note_display').text(displayText);
                    originalNote = newNote;
                    
                    // Hide edit mode
                    $('#note_display').show();
                    $('#note_edit').hide();
                    $('#edit_actions').hide();
                    $('#close_button').show();
                    $('#edit_note_btn').show();
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.msg || 'Note updated successfully');
                    } else {
                        alert(response.msg || 'Note updated successfully');
                    }
                    
                    // Reload the main view if needed
                    if (typeof sell_table !== 'undefined') {
                        sell_table.ajax.reload(null, false);
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.msg || 'Failed to update note');
                    } else {
                        alert(response.msg || 'Failed to update note');
                    }
                }
            },
            error: function(xhr) {
                var errorMsg = 'Failed to update note';
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = xhr.responseJSON.msg;
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                } else {
                    alert(errorMsg);
                }
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
});
</script>
