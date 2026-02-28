<div class="modal-dialog modal-xl no-print" role="document">
  <div class="modal-content no-print">
    @include('purchase.partials.show_details')
  </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		var element = $('div.modal-xl');
		__currency_convert_recursively(element);
	});
</script>