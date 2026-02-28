<div class="row text-center">
	@php
        $filename = session('business.logo');
		// $URL= config('app.');
		$fullpath = "/uploads/business_logos/$filename";
	@endphp
    @if ($fullpath != null)
        <div class="col-xs-12 tw-p-1">
            <img src="{{$fullpath}}" alt="{{session('business.name')}}" style="max-width: 200px; max-height: 80px;">
        </div>
    @else
        <h1 class="text-center page-header">{{ config('app.name', 'ultimatePOS') }}</h1>
    @endif
</div>
