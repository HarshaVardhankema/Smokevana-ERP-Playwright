<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title " id='modalTitle'>Change Picking Status</h4>
        </div>
        <div class="modal-body">
            <form action="{{ action([\App\Http\Controllers\OrderfulfillmentController::class, 'changePickingStatusStore'],['id' => $id]) }}" method="post" id="change-picking-status-form">
                @csrf
                <input type="hidden" name="id" id="modal_selected_order" value="{{ $id }}">
                <div class="form-group">
                    <label for="status">Select Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="picking">Picking</option>
                        <option value="picked">Picked</option>
                        <option value="verifying">Verifying</option>
                        <option value="verified">Verified</option>
                    </select>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#change-picking-status-form').submit(function(e) {
            e.preventDefault();
            var status = $('#status').val();
            var id = $('#modal_selected_order').val();
            $.ajax({
                url: '/change-picking-status',
                type: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function(response) {
                    toastr.success(response.message);
                    $('#change-picking-status-form-modal').modal('hide');
                },
                error: function(xhr, status, error) {
                    toastr.error('Failed to update picking status');
                }
            });
        });
    });
</script>


