<div class="modal-dialog modal-xl enable-session-lock" data-modal-id="{{ $purchase->id }}" 
  data-is-lock-modal="{{ $isLockModal }}" role="document">
<div class="hide modal_id" id={{ $purchase->id }} transaction_type="Purchase"></div>
  <div class="modal-content">
    @include('purchase.partials.show_details')
    {{-- <div class="modal-footer">
      <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print" aria-label="Print" 
      onclick="$(this).closest('div.modal-content').printThis();"><i class="fa fa-print"></i> @lang( 'messages.print' )
      </button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div> --}}
  </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		var element = $('div.modal-xl');
		__currency_convert_recursively(element);
	});
</script>