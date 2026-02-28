<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Woocommerce\Utils\WoocommerceUtil;

class ProcessWooCommerceVariationUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $business_id;
    protected $woo_product_data;
    protected $update_type;

    /**
     * Create a new job instance.
     *
     * @param int $business_id
     * @param array $woo_product_data
     * @param string $update_type
     * @return void
     */
    public function __construct($business_id, $woo_product_data, $update_type = 'variation_only')
    {
        $this->business_id = $business_id;
        $this->woo_product_data = $woo_product_data;
        $this->update_type = $update_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info('Processing WooCommerce variation update job', [
                'business_id' => $this->business_id,
                'product_id' => $this->woo_product_data['id'] ?? 'unknown',
                'update_type' => $this->update_type
            ]);

            $woocommerceUtil = app(WoocommerceUtil::class);
            $result = $woocommerceUtil->processWooCommerceVariationDataUpdate(
                $this->business_id,
                $this->woo_product_data,
                $this->update_type
            );

            if ($result['success']) {
                Log::info('WooCommerce variation update job completed successfully', [
                    'business_id' => $this->business_id,
                    'product_id' => $this->woo_product_data['id'] ?? 'unknown'
                ]);
            } else {
                Log::error('WooCommerce variation update job failed', [
                    'business_id' => $this->business_id,
                    'product_id' => $this->woo_product_data['id'] ?? 'unknown',
                    'error' => $result['message'] ?? 'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error in WooCommerce variation update job', [
                'business_id' => $this->business_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 