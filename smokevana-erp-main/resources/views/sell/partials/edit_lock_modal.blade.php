<div class="modal-dialog" role="document">
    <form method="POST" action="{{ route('release.modal.web', [$modelType,$modelId]) }}" id="edit_session_lock">
        
    @csrf
    <div class="modal-content">
        <div class="modal-header" style="background: #37475A; color: #ffffff;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title tw-text-xl tw-font-bold">Invoice Locked</h4>
        </div>
        <div class="modal-body tw-text-center">
            <p class="tw-text-6xl tw-text-yellow-500">⚠️</p> <!-- Bigger Warning Emoji -->
            <h4 style="font-size: 18px; font-weight: 600; margin-top: 10px;">
                {{$user->first_name}} {{$user->last_name}}
                <span title="{{$user->contact_number}}"style="cursor: pointer; margin-left: 5px;">
                    <i style="font-style: normal; border: 1px solid black; border-radius: 50%; padding: 0 5px;">ℹ</i>
                </span>
                is already editing this Transaction Do you want to take over?
            </h4>

            <div id="password_section" class="tw-hidden tw-mt-4">
                <input type="hidden" name="order_id" value="{{ $modelId }}" />
                <input type="password" class="form-control input-sm tw-mt-2 tw-text-lg tw-p-2" name="password"
                    id="password" placeholder="Enter your password" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="confirm_takeover"
                class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-text-lg">Yes</button>
            <button type="submit" id="submit_form"
                class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-text-lg tw-hidden">Take Over</button>
            <button type="button" id="cancel_btn_modal_warning"
                class="tw-dw-btn tw-dw-btn-neutral tw-text-white tw-text-lg" class="close" data-dismiss="modal"
                aria-label="Close">Cancel</button>
        </div>
    </div>
    {{-- <div class="hidden">{{ $is_invoice }}</div> --}}
    </form>

</div>

<script>
    $(document).ready(function() {
        $('#confirm_takeover').on('click', function() {
            $('#password_section').removeClass('tw-hidden').addClass('tw-block');
            $('#confirm_takeover').addClass('tw-hidden');
            $('#submit_form').removeClass('tw-hidden').addClass('tw-block');
        });

        $('#edit_session_lock').on('submit', function(e) {
            e.preventDefault();

            $('#submit_form').prop('disabled', true);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    
                    toastr.success('You can now edit the invoice.', '✅ Success', {
                        timeOut: 3000
                    });
                        $('#cancel_btn_modal_warning').trigger('click');
                        $('.discount input').prop('disabled', false);
                        $('.discount select').prop('disabled', false);
                        $('.unit_price input').prop('disabled', false);
                        $('.quantity input').prop('disabled', false);
                        $('#openLock').text('🔓')
                        $('#openLock').addClass('hide');
                        $('#openLock').prop('disabled', true);
                        $('#search_foot').removeClass('hide');
                        $('.handle_lock').removeClass('hide');
                        $('#save_button_invoice').removeClass('hide');
                        $('#save_button_purchase').removeClass('hide');
                },
                error: function(xhr) {
                    toastr.error('You are not authorized.', '❌ Error', {
                        timeOut: 3000
                    });
                    $('#submit_form').prop('disabled', false);
                }
            });
        });

        // $('#cancel_btn').on('click', function() {
        //     if (isInvoice == 'no') {
        //         window.location.href = '/sells';
        //         $('.modal').modal('hide');
        //     }
        // });
    });
</script>
