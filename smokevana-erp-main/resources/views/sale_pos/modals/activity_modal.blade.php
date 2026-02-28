<div class="modal-dialog no-print  modal-lg" id='metrix_modal' role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">Activity</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <strong>{{ __('lang_v1.activities') }}:</strong><br>
                    @includeIf('activity_log.activities', ['activity_type' => 'sell'])
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print"
            id='close_button'
                data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>
