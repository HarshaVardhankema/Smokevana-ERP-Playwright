<?php

namespace Modules\Subscription\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Contact;
use Modules\Subscription\Entities\SubscriptionPlan;
use Modules\Subscription\Entities\CustomerSubscription;
use Modules\Subscription\Entities\SubscriptionInvoice;
use Modules\Subscription\Entities\SubscriptionTransaction;

class SubscriptionService
{
    /**
     * Check if customer has active Prime subscription
     */
    public static function hasActivePrimeSubscription($contactId)
    {
        return CustomerSubscription::where('contact_id', $contactId)
            ->active()
            ->whereHas('plan', function ($q) {
                $q->where('is_prime', true);
            })
            ->exists();
    }

    /**
     * Get customer's active subscription
     */
    public static function getActiveSubscription($contactId)
    {
        return CustomerSubscription::where('contact_id', $contactId)
            ->active()
            ->with('plan')
            ->first();
    }

    /**
     * Get Prime discount for customer
     */
    public static function getPrimeDiscount($contactId)
    {
        $subscription = self::getActiveSubscription($contactId);
        
        if (!$subscription || !$subscription->hasPrimeBenefits()) {
            return 0;
        }

        return $subscription->getPrimeDiscount();
    }

    /**
     * Calculate Prime discount amount for an order
     */
    public static function calculatePrimeDiscountAmount($contactId, $orderTotal)
    {
        $discountPercentage = self::getPrimeDiscount($contactId);
        
        if ($discountPercentage <= 0) {
            return 0;
        }

        return round($orderTotal * ($discountPercentage / 100), 2);
    }

    /**
     * Get reward points multiplier for customer
     */
    public static function getRewardPointsMultiplier($contactId)
    {
        $subscription = self::getActiveSubscription($contactId);
        
        if (!$subscription || !$subscription->hasPrimeBenefits()) {
            return 1;
        }

        return $subscription->getRewardPointsMultiplier();
    }

    /**
     * Check if customer has fast delivery benefit
     */
    public static function hasFastDelivery($contactId)
    {
        $subscription = self::getActiveSubscription($contactId);
        
        return $subscription && $subscription->hasFastDelivery();
    }

    /**
     * Check if customer can access Prime-only products
     */
    public static function canAccessPrimeProducts($contactId)
    {
        $subscription = self::getActiveSubscription($contactId);
        
        return $subscription && $subscription->canAccessPrimeProducts();
    }

    /**
     * Check if customer has BNPL benefit
     */
    public static function hasBNPL($contactId)
    {
        $subscription = self::getActiveSubscription($contactId);
        
        return $subscription && $subscription->hasBNPL();
    }

    /**
     * Get customer's BNPL limit
     */
    public static function getBNPLLimit($contactId)
    {
        $subscription = self::getActiveSubscription($contactId);
        
        if (!$subscription || !$subscription->hasBNPL()) {
            return 0;
        }

        return $subscription->getBNPLLimit();
    }

    /**
     * Track savings from Prime discount
     */
    public static function trackPrimeSavings($contactId, $discountAmount)
    {
        $subscription = self::getActiveSubscription($contactId);
        
        if ($subscription && $discountAmount > 0) {
            $subscription->total_savings += $discountAmount;
            $subscription->save();
        }
    }

    /**
     * Track reward points earned
     */
    public static function trackRewardPointsEarned($contactId, $basePoints)
    {
        $subscription = self::getActiveSubscription($contactId);
        
        if ($subscription) {
            $multiplier = $subscription->getRewardPointsMultiplier();
            $bonusPoints = ($multiplier - 1) * $basePoints;
            
            if ($bonusPoints > 0) {
                $subscription->reward_points_earned += $bonusPoints;
                $subscription->save();
            }
        }
    }

    /**
     * Process subscription renewals
     */
    public static function processRenewals()
    {
        $dueSubscriptions = CustomerSubscription::dueForRenewal()
            ->with('plan')
            ->get();

        $processed = 0;
        $failed = 0;

        foreach ($dueSubscriptions as $subscription) {
            try {
                // Check if payment gateway is set up
                if ($subscription->gateway_subscription_id) {
                    // Payment will be handled by gateway webhook
                    continue;
                }

                // For manual payment subscriptions, create invoice
                $invoice = SubscriptionInvoice::create([
                    'business_id' => $subscription->business_id,
                    'subscription_id' => $subscription->id,
                    'contact_id' => $subscription->contact_id,
                    'plan_id' => $subscription->plan_id,
                    'type' => 'renewal',
                    'billing_period_start' => $subscription->current_period_end,
                    'billing_period_end' => $subscription->current_period_end->addDays($subscription->plan->billing_cycle_days),
                    'subtotal' => $subscription->plan->price,
                    'total' => $subscription->plan->price,
                    'currency' => $subscription->plan->currency,
                    'amount_due' => $subscription->plan->price,
                    'status' => 'pending',
                    'due_date' => $subscription->current_period_end,
                    'line_items' => [[
                        'description' => $subscription->plan->name . ' Renewal',
                        'quantity' => 1,
                        'unit_price' => $subscription->plan->price,
                        'total' => $subscription->plan->price,
                    ]],
                ]);

                $subscription->logEvent('renewal_invoice_created', 'Renewal invoice created: ' . $invoice->invoice_no);
                $processed++;

            } catch (\Exception $e) {
                Log::error('Subscription renewal failed', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        return [
            'processed' => $processed,
            'failed' => $failed,
        ];
    }

    /**
     * Process expired subscriptions
     */
    public static function processExpiredSubscriptions()
    {
        $expired = CustomerSubscription::where('status', 'active')
            ->where('expires_at', '<', now())
            ->where('auto_renew', false)
            ->get();

        foreach ($expired as $subscription) {
            $subscription->status = 'expired';
            $subscription->save();
            $subscription->logEvent('subscription_expired', 'Subscription expired');

            // Reset customer group if needed
            if ($subscription->plan && $subscription->plan->customer_group_id) {
                Contact::where('id', $subscription->contact_id)
                    ->where('customer_group_id', $subscription->plan->customer_group_id)
                    ->update(['customer_group_id' => null]);
            }
        }

        return $expired->count();
    }

    /**
     * Process grace period ended subscriptions
     */
    public static function processGracePeriodEnded()
    {
        $ended = CustomerSubscription::where('status', 'past_due')
            ->where('grace_period_ends_at', '<', now())
            ->get();

        foreach ($ended as $subscription) {
            $subscription->status = 'suspended';
            $subscription->save();
            $subscription->logEvent('subscription_suspended', 'Subscription suspended - grace period ended');
        }

        return $ended->count();
    }

    /**
     * Get subscription statistics for a business
     */
    public static function getStatistics($businessId)
    {
        $stats = [
            'total' => CustomerSubscription::where('business_id', $businessId)->count(),
            'active' => CustomerSubscription::where('business_id', $businessId)->active()->count(),
            'trial' => CustomerSubscription::where('business_id', $businessId)->where('status', 'trial')->count(),
            'cancelled' => CustomerSubscription::where('business_id', $businessId)->where('status', 'cancelled')->count(),
            'expired' => CustomerSubscription::where('business_id', $businessId)->where('status', 'expired')->count(),
            'past_due' => CustomerSubscription::where('business_id', $businessId)->where('status', 'past_due')->count(),
        ];

        // MRR calculation
        $stats['mrr'] = CustomerSubscription::where('customer_subscriptions.business_id', $businessId)
            ->active()
            ->join('subscription_plans', 'customer_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->selectRaw('SUM(
                CASE 
                    WHEN subscription_plans.billing_cycle = "monthly" THEN subscription_plans.price
                    WHEN subscription_plans.billing_cycle = "quarterly" THEN subscription_plans.price / 3
                    WHEN subscription_plans.billing_cycle = "semi_annual" THEN subscription_plans.price / 6
                    WHEN subscription_plans.billing_cycle = "annual" THEN subscription_plans.price / 12
                    ELSE 0
                END
            ) as mrr')
            ->value('mrr') ?? 0;

        // Total revenue
        $stats['total_revenue'] = SubscriptionTransaction::where('business_id', $businessId)
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->sum('amount');

        return $stats;
    }
}
