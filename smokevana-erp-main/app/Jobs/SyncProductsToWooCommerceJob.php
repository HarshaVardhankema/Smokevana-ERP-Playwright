<?php

namespace App\Jobs;

use App\Models\WoocommerceSyncHistory;
use App\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Woocommerce\Utils\WoocommerceUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;

class SyncProductsToWooCommerceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $syncHistoryId;
    public $businessId;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600; // 1 hour

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($syncHistoryId, $businessId)
    {
        $this->syncHistoryId = $syncHistoryId;
        $this->businessId = $businessId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $syncHistory = WoocommerceSyncHistory::find($this->syncHistoryId);
        
        if (!$syncHistory) {
            Log::error('Sync history not found', ['id' => $this->syncHistoryId]);
            return;
        }

        try {
            // Get products that need syncing
            $products = Product::where('business_id', $this->businessId)
                ->where(function ($q) {
                    $q->whereNull('woocommerce_disable_sync')
                        ->orWhere('woocommerce_disable_sync', 0);
                })
                ->get();

            $totalProducts = $products->count();
            
            if ($totalProducts === 0) {
                $syncHistory->markCompleted(0, 0, 0, ['message' => 'No products to sync']);
                return;
            }

            // Update total items
            $syncHistory->update(['total_items' => $totalProducts]);

            $woocommerce = new WoocommerceUtil(new TransactionUtil(), new ProductUtil());
            
            $synced = 0;
            $failed = 0;
            $skipped = 0;
            $details = ['synced' => [], 'failed' => [], 'skipped' => []];

            foreach ($products as $product) {
                try {
                    // Check if product has WooCommerce ID
                    if (!empty($product->woocommerce_product_id)) {
                        // Update existing product
                        $result = $woocommerce->updateProductInWooCommerce($this->businessId, $product);
                    } else {
                        // Create new product
                        $result = $woocommerce->createProductInWooCommerce($this->businessId, $product);
                    }

                    if (isset($result['success']) && $result['success']) {
                        $synced++;
                        $details['synced'][] = [
                            'id' => $product->id,
                            'sku' => $product->sku,
                            'name' => $product->name,
                            'wc_id' => $result['woocommerce_product_id'] ?? $product->woocommerce_product_id,
                        ];
                    } else {
                        $failed++;
                        $details['failed'][] = [
                            'id' => $product->id,
                            'sku' => $product->sku,
                            'name' => $product->name,
                            'error' => $result['message'] ?? $result['error'] ?? 'Unknown error',
                        ];
                    }
                } catch (\Exception $e) {
                    $failed++;
                    $details['failed'][] = [
                        'id' => $product->id,
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'error' => $e->getMessage(),
                    ];
                    Log::warning("Product sync failed: {$product->id}", ['error' => $e->getMessage()]);
                }

                // Update progress every 10 products
                if (($synced + $failed + $skipped) % 10 === 0) {
                    $syncHistory->updateProgress($synced, $failed);
                }
            }

            // Limit details to prevent huge JSON
            if (count($details['synced']) > 100) {
                $details['synced'] = array_slice($details['synced'], 0, 100);
                $details['synced_truncated'] = true;
            }
            if (count($details['failed']) > 100) {
                $details['failed'] = array_slice($details['failed'], 0, 100);
                $details['failed_truncated'] = true;
            }

            // Mark sync as completed
            $syncHistory->markCompleted($synced, $failed, $skipped, $details);

            Log::info("WooCommerce product sync completed for business {$this->businessId}", [
                'synced' => $synced,
                'failed' => $failed,
                'skipped' => $skipped,
            ]);

        } catch (\Exception $e) {
            $syncHistory->markFailed($e->getMessage());
            Log::error("WooCommerce product sync failed for business {$this->businessId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        $syncHistory = WoocommerceSyncHistory::find($this->syncHistoryId);
        
        if ($syncHistory) {
            $syncHistory->markFailed('Job failed: ' . $exception->getMessage());
        }

        Log::error('SyncProductsToWooCommerceJob failed', [
            'sync_id' => $this->syncHistoryId,
            'business_id' => $this->businessId,
            'error' => $exception->getMessage()
        ]);
    }
}
