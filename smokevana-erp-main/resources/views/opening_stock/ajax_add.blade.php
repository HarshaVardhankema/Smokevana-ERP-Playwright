<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
	{{-- {!! Form::open(['url' => action([\App\Http\Controllers\OpeningStockController::class, 'save']), 'method' => 'post', 'id' => 'add_opening_stock_form' ]) !!}
	{!! Form::hidden('product_id', $product->id); !!}
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">@lang('lang_v1.add_opening_stock')</h4>
	    </div>
	    <div class="modal-body">
			@include('opening_stock.form-part')
		</div>
		<div class="modal-footer">
			<button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="add_opening_stock_btn">@lang('messages.save')</button>
		    <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
		 </div>
	 {!! Form::close() !!} --}}
	 <style>
	 		.error-container {
		  text-align: center;
		  padding: 40px;
		  background: white;
		  border: 1px solid #dee2e6;
		  border-radius: 8px;
		  box-shadow: 0 0 10px rgba(0,0,0,0.05);
		}
		.error-code {
		  font-size: 72px;
		  font-weight: bold;
		  color: #dc3545;
		}
	  </style>
	  <div class="error-container">
		<div class="error-code">Error</div>
		<h2 class="mt-3">Stock Modification Not Allowed</h2>
		<p class="text-muted">Increasing or decreasing stock directly is restricted by the system policy.</p>
		<a href="/products" class="btn btn-primary mt-3">Return to Dashboard</a>
	  </div>
	</div>
</div>
