<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('woocommerce_app_url',  __('woocommerce::lang.woocommerce_app_url') . ':') !!}
            	{!! Form::text('woocommerce_app_url', $default_settings['woocommerce_app_url'], ['class' => 'form-control','placeholder' => __('woocommerce::lang.woocommerce_app_url')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('woocommerce_consumer_key',  __('woocommerce::lang.woocommerce_consumer_key') . ':') !!}
                {!! Form::text('woocommerce_consumer_key', $default_settings['woocommerce_consumer_key'], ['class' => 'form-control','placeholder' => __('woocommerce::lang.woocommerce_consumer_key')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('woocommerce_consumer_secret', __('woocommerce::lang.woocommerce_consumer_secret') . ':') !!}
                <input type="password" name="woocommerce_consumer_secret" value="{{$default_settings['woocommerce_consumer_secret']}}" id="woocommerce_consumer_secret" class="form-control">
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('location_id',  __('business.business_locations') . ':') !!} @show_tooltip(__('woocommerce::lang.location_dropdown_help'))
                {!! Form::select('location_id', $locations, $default_settings['location_id'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="checkbox">
                <label>
                    <br/>
                    {!! Form::checkbox('enable_auto_sync', 1, !empty($default_settings['enable_auto_sync']), ['class' => 'input-icheck'] ); !!} @lang('woocommerce::lang.enable_auto_sync')
                </label>
                @show_tooltip(__('woocommerce::lang.auto_sync_tooltip'))
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <button class="btn btn-primary" id="test-connection-button">Test Connection</button>
                <div id="test-connection-result"></div>
            </div>
        </div>
    </div>
</div>

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#test-connection-button').click(function(event) {
                event.preventDefault();
                console.log('testConnection');
                var woocommerce_app_url = document.getElementById('woocommerce_app_url').value;
                var woocommerce_consumer_key = document.getElementById('woocommerce_consumer_key').value;
                var woocommerce_consumer_secret = document.getElementById('woocommerce_consumer_secret').value;
                var location_id = document.getElementById('location_id').value;
                // var enable_auto_sync = document.getElementById('enable_auto_sync').checked;

                var data = {

                    woocommerce_app_url: woocommerce_app_url,
                    woocommerce_consumer_key: woocommerce_consumer_key,
                    woocommerce_consumer_secret: woocommerce_consumer_secret,
                    location_id: location_id,
                    enable_auto_sync: true
                };

                $.ajax({
                    url: '{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'testConnection']) }}',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        // $('#test-connection-result').html(response);
                        if(response.status == 2){
                            toastr.success(response.message);
                        }else if(response.status == 1){
                            toastr.warning(response.message);
                        }else{
                            toastr.error(response.message);
                        }
                    }
                });
            });

        });
    </script>
@endsection