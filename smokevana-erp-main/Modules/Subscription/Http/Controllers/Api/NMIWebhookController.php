<?php

namespace Modules\Subscription\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Modules\Subscription\Entities\CustomerSubscription;
use Modules\Subscription\Entities\SubscriptionTransaction;
use Modules\Subscription\Entities\SubscriptionInvoice;

/**
 * NMI Webhook Controller
 * 
 * Handles webhook notifications from NMI for subscription events
 * 
 * @see https://support.nmi.com/hc/en-gb/articles/14525725002385-API-Recurring-Payments-and-Subscriptions
 */
class NMIWebhookController extends Controller
{
    /**
     * Handle NMI webhook events
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        // Log the incoming webhook
        Log::info('NMI Webhook Received', [
            'headers' => $request->headers->all(),
            'payload' => $request->all()
        ]);

        // Verify webhook signature if configured
        if (!$this->verifySignature($request)) {
            Log::warning('NMI Webhook - Invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $eventType = $request->input('event_type') ?? $request->input('action') ?? 'unknown';
        $subscriptionId = $request->input('subscription_id');
        $transactionId = $request->input('transactionid') ?? $request->input('transaction_id');

        try {
            switch ($eventType) {
                case 'recurring.subscription.created':
                    return $this->handleSubscriptionCreated($request);

                case 'recurring.subscription.updated':
                    return $this->handleSubscriptionUpdated($request);

                case 'recurring.subscription.deleted':
                case 'recurring.subscription.cancelled':
                    return $this->handleSubscriptionCancelled($request);

                case 'recurring.payment.success':
                case 'transaction.sale.success':
                    return $this->handlePaymentSuccess($request);

                case 'recurring.payment.failure':
                case 'transaction.sale.failure':
                    return $this->handlePaymentFailure($request);

                case 'recurring.payment.refund':
                case 'transaction.refund.success':
                    return $this->handleRefund($request);

                default:
                    Log::info('NMI Webhook - Unhandled event type', ['event_type' => $eventType]);
                    return response()->json(['status' => 'ignored', 'event_type' => $eventType]);
            }
        } catch (\Exception $e) {
            Log::error('NMI Webhook Error', [
                'event_type' => $eventType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle subscription created event
     */
    protected function handleSubscriptionCreated(Request $request): \Illuminate\Http\JsonResponse
    {
        $subscriptionId = $request->input('subscription_id');
        
        Log::info('NMI Webhook - Subscription Created', ['subscription_id' => $subscriptionId]);

        // Find our subscription by gateway ID
        $subscription = CustomerSubscription::where('gateway_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $subscription->logEvent('nmi_subscription_created', 'NMI subscription created. ID: ' . $subscriptionId);
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle subscription updated event
     */
    protected function handleSubscriptionUpdated(Request $request): \Illuminate\Http\JsonResponse
    {
        $subscriptionId = $request->input('subscription_id');
        
        Log::info('NMI Webhook - Subscription Updated', ['subscription_id' => $subscriptionId]);

        $subscription = CustomerSubscription::where('gateway_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $subscription->logEvent('nmi_subscription_updated', 'NMI subscription updated via webhook');
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle subscription cancelled event
     */
    protected function handleSubscriptionCancelled(Request $request): \Illuminate\Http\JsonResponse
    {
        $subscriptionId = $request->input('subscription_id');
        
        Log::info('NMI Webhook - Subscription Cancelled', ['subscription_id' => $subscriptionId]);

        $subscription = CustomerSubscription::where('gateway_subscription_id', $subscriptionId)->first();

        if ($subscription && $subscription->status !== CustomerSubscription::STATUS_CANCELLED) {
            $subscription->cancel('Cancelled via NMI webhook');
            $subscription->logEvent('nmi_subscription_cancelled', 'Subscription cancelled via NMI webhook');
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle successful payment event
     */
    protected function handlePaymentSuccess(Request $request): \Illuminate\Http\JsonResponse
    {
        $subscriptionId = $request->input('subscription_id');
        $transactionId = $request->input('transactionid') ?? $request->input('transaction_id');
        $amount = $request->input('amount');
        
        Log::info('NMI Webhook - Payment Success', [
            'subscription_id' => $subscriptionId,
            'transaction_id' => $transactionId,
            'amount' => $amount
        ]);

        $subscription = CustomerSubscription::where('gateway_subscription_id', $subscriptionId)->first();

        if (!$subscription) {
            Log::warning('NMI Webhook - Subscription not found', ['subscription_id' => $subscriptionId]);
            return response()->json(['status' => 'subscription_not_found']);
        }

        try {
            DB::beginTransaction();

            // Check if transaction already recorded (idempotency)
            $existingTransaction = SubscriptionTransaction::where('gateway_transaction_id', $transactionId)->first();

            if (!$existingTransaction) {
                // Record the transaction
                SubscriptionTransaction::create([
                    'business_id' => $subscription->business_id,
                    'subscription_id' => $subscription->id,
                    'contact_id' => $subscription->contact_id,
                    'type' => 'payment',
                    'amount' => $amount,
                    'currency' => $subscription->plan->currency ?? 'USD',
                    'status' => 'completed',
                    'payment_method' => 'nmi',
                    'gateway_transaction_id' => $transactionId,
                    'gateway_subscription_id' => $subscriptionId,
                    'description' => 'Recurring payment via NMI',
                    'metadata' => [
                        'webhook_data' => $request->all(),
                    ],
                    'processed_at' => now(),
                ]);

                // Update subscription
                $subscription->increment('amount_paid', $amount);
                $subscription->touch();

                // Create renewal invoice
                $invoice = SubscriptionInvoice::create([
                    'business_id' => $subscription->business_id,
                    'subscription_id' => $subscription->id,
                    'contact_id' => $subscription->contact_id,
                    'plan_id' => $subscription->plan_id,
                    'type' => 'renewal',
                    'subtotal' => $amount,
                    'total' => $amount,
                    'currency' => $subscription->plan->currency ?? 'USD',
                    'amount_paid' => $amount,
                    'amount_due' => 0,
                    'status' => 'paid',
                    'paid_at' => now(),
                    'payment_method' => 'nmi',
                    'payment_reference' => $transactionId,
                    'line_items' => [
                        [
                            'description' => $subscription->plan->name . ' - Renewal',
                            'quantity' => 1,
                            'unit_price' => $amount,
                            'total' => $amount,
                        ]
                    ],
                ]);

                // Extend subscription if needed
                if ($subscription->expires_at && $subscription->expires_at->isPast()) {
                    $subscription->renew();
                }

                $subscription->logEvent('nmi_payment_success', 'Recurring payment successful. Transaction: ' . $transactionId);
            }

            DB::commit();

            return response()->json(['status' => 'processed']);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Handle failed payment event
     */
    protected function handlePaymentFailure(Request $request): \Illuminate\Http\JsonResponse
    {
        $subscriptionId = $request->input('subscription_id');
        $transactionId = $request->input('transactionid') ?? $request->input('transaction_id');
        $responseText = $request->input('responsetext') ?? $request->input('response_text');
        
        Log::warning('NMI Webhook - Payment Failure', [
            'subscription_id' => $subscriptionId,
            'transaction_id' => $transactionId,
            'response' => $responseText
        ]);

        $subscription = CustomerSubscription::where('gateway_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            // Record failed transaction
            SubscriptionTransaction::create([
                'business_id' => $subscription->business_id,
                'subscription_id' => $subscription->id,
                'contact_id' => $subscription->contact_id,
                'type' => 'payment',
                'amount' => $request->input('amount') ?? 0,
                'currency' => $subscription->plan->currency ?? 'USD',
                'status' => 'failed',
                'payment_method' => 'nmi',
                'gateway_transaction_id' => $transactionId,
                'gateway_subscription_id' => $subscriptionId,
                'description' => 'Payment failed: ' . ($responseText ?? 'Unknown error'),
                'metadata' => [
                    'webhook_data' => $request->all(),
                    'failure_reason' => $responseText,
                ],
                'processed_at' => now(),
            ]);

            // Check if we should suspend the subscription after multiple failures
            $failedCount = SubscriptionTransaction::where('subscription_id', $subscription->id)
                ->where('status', 'failed')
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            if ($failedCount >= 3) {
                // Too many failed payments, consider suspending
                $subscription->update(['status' => 'past_due']);
                $subscription->logEvent('subscription_past_due', 'Subscription marked as past due after multiple payment failures');
            }

            $subscription->logEvent('nmi_payment_failed', 'Recurring payment failed: ' . ($responseText ?? 'Unknown'));
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle refund event
     */
    protected function handleRefund(Request $request): \Illuminate\Http\JsonResponse
    {
        $subscriptionId = $request->input('subscription_id');
        $transactionId = $request->input('transactionid') ?? $request->input('transaction_id');
        $amount = $request->input('amount');
        
        Log::info('NMI Webhook - Refund', [
            'subscription_id' => $subscriptionId,
            'transaction_id' => $transactionId,
            'amount' => $amount
        ]);

        $subscription = CustomerSubscription::where('gateway_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            // Check if refund already recorded
            $existingRefund = SubscriptionTransaction::where('gateway_transaction_id', $transactionId)
                ->where('type', 'refund')
                ->first();

            if (!$existingRefund) {
                SubscriptionTransaction::create([
                    'business_id' => $subscription->business_id,
                    'subscription_id' => $subscription->id,
                    'contact_id' => $subscription->contact_id,
                    'type' => 'refund',
                    'amount' => $amount,
                    'currency' => $subscription->plan->currency ?? 'USD',
                    'status' => 'completed',
                    'payment_method' => 'nmi',
                    'gateway_transaction_id' => $transactionId,
                    'gateway_subscription_id' => $subscriptionId,
                    'description' => 'Refund processed via NMI',
                    'metadata' => [
                        'webhook_data' => $request->all(),
                    ],
                    'processed_at' => now(),
                ]);

                $subscription->logEvent('nmi_refund_processed', 'Refund processed. Amount: ' . $amount);
            }
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Verify webhook signature
     */
    protected function verifySignature(Request $request): bool
    {
        // NMI webhook signature verification
        // This depends on how NMI is configured to send webhooks
        // Common approaches:
        // 1. Shared secret in header
        // 2. HMAC signature
        // 3. IP whitelist
        
        $webhookSecret = config('services.nmi.webhook_secret');
        
        // If no secret configured, skip verification (not recommended for production)
        if (empty($webhookSecret)) {
            return true;
        }

        // Check for signature header
        $signature = $request->header('X-NMI-Signature') ?? $request->header('X-Webhook-Signature');
        
        if (!$signature) {
            // Fallback: check if request comes from NMI IP
            $nmiIps = config('services.nmi.webhook_ips', []);
            if (!empty($nmiIps)) {
                return in_array($request->ip(), $nmiIps);
            }
            return true; // Skip verification if not configured
        }

        // Verify HMAC signature
        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        return hash_equals($expectedSignature, $signature);
    }
}
