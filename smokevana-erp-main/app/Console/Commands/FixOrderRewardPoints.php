<?php

namespace App\Console\Commands;

use App\Business;
use App\Transaction;
use Illuminate\Console\Command;

/**
 * One-time fix: set rp_redeemed and rp_redeemed_amount on a sales order
 * so GET /api/customer/my-order/{id} shows the correct reward_points.
 *
 * Usage: php artisan order:fix-reward-points {order_id} {points_used}
 * Example: php artisan order:fix-reward-points 794 1
 */
class FixOrderRewardPoints extends Command
{
    protected $signature = 'order:fix-reward-points {order_id : Transaction (order) ID} {points_used : Number of reward points used}';

    protected $description = 'Set reward points used on a sales order so the customer order API shows correct reward_points';

    public function handle()
    {
        $orderId = (int) $this->argument('order_id');
        $pointsUsed = (int) $this->argument('points_used');

        if ($orderId <= 0 || $pointsUsed <= 0) {
            $this->error('order_id and points_used must be positive integers.');
            return 1;
        }

        $transaction = Transaction::find($orderId);
        if (! $transaction) {
            $this->error("Order {$orderId} not found.");
            return 1;
        }

        if (! in_array($transaction->type, ['sales_order', 'sell'], true)) {
            $this->error("Order {$orderId} is not a sales order or sell (type: {$transaction->type}).");
            return 1;
        }

        $business = Business::find($transaction->business_id);
        $amountPerPoint = $business ? (float) ($business->redeem_amount_per_unit_rp ?? 0.01) : 0.01;
        $dollarsUsed = round($pointsUsed * $amountPerPoint, 2);

        $transaction->rp_redeemed = $pointsUsed;
        $transaction->rp_redeemed_amount = $dollarsUsed;
        $transaction->save();

        $this->info("Updated order {$orderId}: rp_redeemed={$pointsUsed}, rp_redeemed_amount={$dollarsUsed}.");
        $this->info('GET /api/customer/my-order/' . $orderId . ' will now show reward_points.points_used=' . $pointsUsed . '.');

        return 0;
    }
}
