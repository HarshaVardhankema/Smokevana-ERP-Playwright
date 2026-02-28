<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            <h4>@lang('woocommerce::lang.order_created')</h4>
        </div>
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('woocommerce_wh_oc_secret',  __('woocommerce::lang.webhook_secret') . ':') !!}
            	{!! Form::text('woocommerce_wh_oc_secret', !empty($business->woocommerce_wh_oc_secret) ? $business->woocommerce_wh_oc_secret : null, ['class' => 'form-control','placeholder' => __('woocommerce::lang.webhook_secret')]) !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('woocommerce::lang.webhook_delivery_url'):</strong>
                <p>{{action([\Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'orderCreated'], ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('woocommerce::lang.order_updated')</h4>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('woocommerce_wh_ou_secret',  __('woocommerce::lang.webhook_secret') . ':') !!}
                {!! Form::text('woocommerce_wh_ou_secret', !empty($business->woocommerce_wh_oc_secret) ? $business->woocommerce_wh_ou_secret : null, ['class' => 'form-control','placeholder' => __('woocommerce::lang.webhook_secret')]) !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('woocommerce::lang.webhook_delivery_url'):</strong>
                <p>{{action([\Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'orderUpdated'], ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('woocommerce::lang.order_deleted')</h4>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('woocommerce_wh_od_secret',  __('woocommerce::lang.webhook_secret') . ':') !!}
                {!! Form::text('woocommerce_wh_od_secret', !empty($business->woocommerce_wh_oc_secret) ? $business->woocommerce_wh_od_secret : null, ['class' => 'form-control','placeholder' => __('woocommerce::lang.webhook_secret')]) !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('woocommerce::lang.webhook_delivery_url'):</strong>
                <p>{{action([\Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'orderDeleted'], ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('woocommerce::lang.order_restored')</h4>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('woocommerce_wh_or_secret',  __('woocommerce::lang.webhook_secret') . ':') !!}
                {!! Form::text('woocommerce_wh_or_secret', !empty($business->woocommerce_wh_oc_secret) ? $business->woocommerce_wh_or_secret : null, ['class' => 'form-control','placeholder' => __('woocommerce::lang.webhook_secret')]) !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('woocommerce::lang.webhook_delivery_url'):</strong>
                <p>{{action([\Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'orderRestored'], ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>🚀 WooCommerce to ERP Webhook (New)</h4>
            <p class="text-muted">This endpoint receives webhooks from WooCommerce for real-time synchronization</p>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('woocommerce_wh_general_secret',  __('woocommerce::lang.webhook_secret') . ':') !!}
                {!! Form::text('woocommerce_wh_general_secret', !empty($business->woocommerce_wh_general_secret) ? $business->woocommerce_wh_general_secret : null, ['class' => 'form-control','placeholder' => __('woocommerce::lang.webhook_secret')]) !!}
                <p class="help-block">Secret key for authenticating webhooks from WooCommerce plugin</p>
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('woocommerce::lang.webhook_delivery_url'):</strong>
                <p>{{action([\Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'receiveWebhook'], ['business_id' => session()->get('business.id')])}}</p>
                <p class="help-block">
                    <strong>Supported Events:</strong><br>
                    • Product Created/Updated<br>
                    • Order Created<br>
                    • Order Status Changed
                </p>
            </div>
        </div>

    </div>
</div>