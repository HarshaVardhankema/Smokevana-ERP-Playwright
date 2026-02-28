<?php

namespace App\Jobs;

use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Woocommerce\Utils\WoocommerceUtil;

class WooCommerceWebhookPipeline implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    protected $business_id;
    protected $data;
    protected $woocommerce_api_settings;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($business_id, $data, $woocommerce_api_settings)
    {
        $this->business_id = $business_id;
        $this->data = $data;
        $this->woocommerce_api_settings = $woocommerce_api_settings;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $task = (new WoocommerceUtil(new TransactionUtil(), new ProductUtil()))->processWooCommerceProduct($this->business_id, $this->data, $this->woocommerce_api_settings);
        Log::info($task);
        Log::info('WooCommerceWebhookPipeline completed');
    }
}
