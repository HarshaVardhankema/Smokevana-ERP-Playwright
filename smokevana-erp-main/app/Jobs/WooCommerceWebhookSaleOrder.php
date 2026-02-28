<?php

namespace App\Jobs;

use App\Transaction;
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

class WooCommerceWebhookSaleOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $transaction = Transaction::with([
                'sell_lines.product', 
                'sell_lines.variations', 
                'contact', 
                'payment_lines'
            ])->find($this->order);

            if (!$transaction) {
                Log::error('Transaction not found for WooCommerce sync', ['transaction_id' => $this->order]);
                return;
            }

            $woocommerce = new WoocommerceUtil(new TransactionUtil(), new ProductUtil());
            
            // Check if order already exists in WooCommerce
            if (!empty($transaction->woocommerce_order_id)) {
                $result = $woocommerce->updateOrderInWooCommerce($transaction->business_id, $transaction);
            } else {
                $result = $woocommerce->createOrderInWooCommerce($transaction->business_id, $transaction);
            }

            if ($result['success']) {
                Log::info('Order synced successfully', [
                    'transaction_id' => $transaction->id,
                    'woocommerce_order_id' => $result['woocommerce_order_id']
                ]);
            } else {
                Log::error('Order sync failed', [
                    'transaction_id' => $transaction->id,
                    'error' => $result['message']??$result['error']??'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error in WooCommerceWebhookSaleOrder job', [
                'transaction_id' => $this->order,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
