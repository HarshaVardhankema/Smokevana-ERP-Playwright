{{-- @extends('layouts.app')
@section('title', __('lang_v1.add_opening_stock'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.add_opening_stock')</h1>
</section>

<!-- Main content  -->
<section class="content">
	{!! Form::open(['url' => action([\App\Http\Controllers\OpeningStockController::class, 'save']), 'method' => 'post', 'id' => 'add_opening_stock_form' ]) !!}
	{!! Form::hidden('product_id', $product->id); !!}
	@include('opening_stock.form-part')
	<div class="row">
		<div class="col-sm-12 text-center">
			<button type="submit" class="btn btn-primary btn-big">@lang('messages.save')</button>
		</div>
	</div>

	{!! Form::close() !!}
</section>
@stop
@section('javascript')
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$(document).ready( function(){
			$('.os_date').datetimepicker({
		        format: moment_date_format + ' ' + moment_time_format,
		        ignoreReadonly: true,
		        widgetPositioning: {
		            horizontal: 'right',
		            vertical: 'bottom'
		        }
		    });
		});
	</script>
@endsection --}}

<style>
	body {
	  background-color: #f8f9fa;
	  height: 100vh;
	  display: flex;
	  align-items: center;
	  justify-content: center;
	}
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
