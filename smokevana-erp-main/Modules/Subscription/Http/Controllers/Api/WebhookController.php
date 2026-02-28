<?php

namespace Modules\Subscription\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\Entities\CustomerSubscription;
use Modules\Subscription\Entities\SubscriptionInvoice;
use Modules\Subscription\Entities\SubscriptionTransaction;
use Modules\Subscription\Entities\SubscriptionWebhook;

class WebhookController extends Controller
{
    /**
     * Handle Stripe webhook
     */
    public function handleStripe(Request $request)
    {
        $payload = $request->all();
        $signature = $request->header('Stripe-Signature');

        // Record webhook for idempotency and debugging
        $webhook = SubscriptionWebhook::record(
            'stripe',
            $payload['type'] ?? 'unknown',
            $payload,
            $request->headers->all()
        );

        // Check idempotency
        if (SubscriptionWebhook::isAlreadyProcessed($payload['id'] ?? null)) {
            $webhook->markAsSkipped('Already processed');
            return response()->json(['status' => 'skipped']);
        }

        try {
            $webhook->markAsProcessing();

            switch ($payload['type'] ?? '') {
                case 'invoice.paid':
                    $this->handleStripePaidInvoice($payload, $webhook);
                    break;

                case 'invoice.payment_failed':
                    $this->handleStripePaymentFailed($payload, $webhook);
                    break;

                case 'customer.subscription.updated':
                    $this->handleStripeSubscriptionUpdated($payload, $webhook);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleStripeSubscriptionDeleted($payload, $webhook);
                    break;

                default:
                    $webhook->markAsSkipped('Unhandled event type: ' . ($payload['type'] ?? 'unknown'));
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage(), [
                'webhook_id' => $webhook->id,
                'payload' => $payload,
            ]);
            $webhook->markAsFailed($e->getMessage(), $e->getTraceAsString());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle Stripe paid invoice
     */
    private function handleStripePaidInvoice($payload, $webhook)
    {
        $invoice = $payload['data']['object'] ?? [];
        $subscriptionId = $invoice['subscription'] ?? null;

        if (!$subscriptionId) {
            $webhook->markAsSkipped('No subscription ID in payload');
            return;
        }

        $subscription = CustomerSubscription::where('gateway_subscription_id', $subscriptionId)->first();

        if (!$subscription) {
            $webhook->markAsSkipped('Subscription not found: ' . $subscriptionId);
            return;
        }

        DB::transaction(function () use ($invoice, $subscription, $webhook) {
            // Create transaction
            $transaction = SubscriptionTransaction::create([
                'business_id' => $subscription->business_id,
                'subscription_id' => $subscription->id,
                'contact_id' => $subscription->contact_id,
                'type' => 'payment',
                'status' => 'completed',
                'amount' => ($invoice['amount_paid'] ?? 0) / 100, // Stripe amounts are in cents
                'currency' => strtoupper($invoice['currency'] ?? 'USD'),
                'payment_gateway' => 'stripe',
                'gateway_transaction_id' => $invoice['id'],
                'gateway_response' => $invoice,
            ]);

            // Update subscription
            $subscription->amount_paid += $transaction->amount;
            $subscription->billing_attempts = 0;
            $subscription->last_billing_attempt = now();

            if ($subscription->status === 'pending' || $subscription->status === 'past_due') {
                $subscription->activate();
            }

            if ($subscription->status === 'active') {
                $subscription->renew();
            }

            $subscription->save();

            // Update invoice if exists
            $subscriptionInvoice = SubscriptionInvoice::where('subscription_id', $subscription->id)
                ->where('status', 'pending')
                ->first();

            if ($subscriptionInvoice) {
                $subscriptionInvoice->recordPayment(
                    $transaction->amount,
                    'card',
                    $transaction->transaction_no
                );
            }

            // Log event
            $subscription->logEvent('payment_received', 'Payment received via Stripe: ' . $transaction->amount);

            $webhook->markAsProcessed($subscription->id, $transaction->id);
        });
    }

    /**
     * Handle Stripe payment failed
     */
    private function handleStripePaymentFailed($payload, $webhook)
    {
        $invoice = $payload['data']['object'] ?? [];
        $subscriptionId = $invoice['subscription'] ?? null;

        if (!$subscriptionId) {
            $webhook->markAsSkipped('No subscription ID in payload');
            return;
        }

        $subscription = CustomerSubscription::where('gateway_subscription_id', $subscriptionId)->first();

        if (!$subscription) {
            $webhook->markAsSkipped('Subscription not found: ' . $subscriptionId);
            return;
        }

        DB::transaction(function () use ($invoice, $subscription, $webhook) {
            // Create failed transaction record
            $transaction = SubscriptionTransaction::create([
                'business_id' => $subscription->business_id,
                'subscription_id' => $subscription->id,
                'contact_id' => $subscription->contact_id,
                'type' => 'payment',
                'status' => 'failed',
                'amount' => ($invoice['amount_due'] ?? 0) / 100,
                'currency' => strtoupper($invoice['currency'] ?? 'USD'),
                'payment_gateway' => 'stripe',
                'gateway_transaction_id' => $invoice['id'],
                'gateway_response' => $invoice,
                'failure_reason' => $invoice['last_finalization_error']['message'] ?? 'Payment failed',
            ]);

            // Update subscription status
            $subscription->billing_attempts++;
            $subscription->last_billing_attempt = now();

            // Mark as past due after first failure
            if ($subscription->status === 'active') {
                $subscription->markAsPastDue();
            }

            $subscription->save();

            // Log event
            $subscription->logEvent('payment_failed', 'Payment failed via Stripe');

            $webhook->markAsProcessed($subscription->id, $transaction->id);
        });
    }

    /**
     * Handle Stripe subscription updated
     */
    private function handleStripeSubscriptionUpdated($payload, $webhook)
    {
        $stripeSubscription = $payload['data']['object'] ?? [];
        $subscriptionId = $stripeSubscription['id'] ?? null;

        $subscription = CustomerSubscription::where('gateway_subscription_id', $subscriptionId)->first();

        if (!$subscription) {
            $webhook->markAsSkipped('Subscription not found: ' . $subscriptionId);
            return;
        }

        // Update subscription dates based on Stripe data
        if (isset($stripeSubscription['current_period_start'])) {
            $subscription->current_period_start = \Carbon\Carbon::createFromTimestamp($stripeSubscription['current_period_start']);
        }

        if (isset($stripeSubscription['current_period_end'])) {
            $subscription->current_period_end = \Carbon\Carbon::createFromTimestamp($stripeSubscription['current_period_end']);
            $subscription->expires_at = $subscription->current_period_end;
        }

        $subscription->save();
        $subscription->logEvent('subscription_updated', 'Subscription updated via Stripe webhook');

        $webhook->markAsProcessed($subscription->id);
    }

    /**
     * Handle Stripe subscription deleted
     */
    private function handleStripeSubscriptionDeleted($payload, $webhook)
    {
        $stripeSubscription = $payload['data']['object'] ?? [];
        $subscriptionId = $stripeSubscription['id'] ?? null;

        $subscription = CustomerSubscription::where('gateway_subscription_id', $subscriptionId)->first();

        if (!$subscription) {
            $webhook->markAsSkipped('Subscription not found: ' . $subscriptionId);
            return;
        }

        $subscription->cancel('Cancelled via Stripe', 'immediate');

        $webhook->markAsProcessed($subscription->id);
    }

    /**
     * Handle PayPal webhook
     */
    public function handlePaypal(Request $request)
    {
        $payload = $request->all();

        // Record webhook
        $webhook = SubscriptionWebhook::record(
            'paypal',
            $payload['event_type'] ?? 'unknown',
            $payload,
            $request->headers->all()
        );

        // Check idempotency
        if (SubscriptionWebhook::isAlreadyProcessed($payload['id'] ?? null)) {
            $webhook->markAsSkipped('Already processed');
            return response()->json(['status' => 'skipped']);
        }

        try {
            $webhook->markAsProcessing();

            switch ($payload['event_type'] ?? '') {
                case 'BILLING.SUBSCRIPTION.ACTIVATED':
                    $this->handlePaypalSubscriptionActivated($payload, $webhook);
                    break;

                case 'PAYMENT.SALE.COMPLETED':
                    $this->handlePaypalPaymentCompleted($payload, $webhook);
                    break;

                case 'BILLING.SUBSCRIPTION.CANCELLED':
                    $this->handlePaypalSubscriptionCancelled($payload, $webhook);
                    break;

                default:
                    $webhook->markAsSkipped('Unhandled event type: ' . ($payload['event_type'] ?? 'unknown'));
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('PayPal webhook error: ' . $e->getMessage(), [
                'webhook_id' => $webhook->id,
                'payload' => $payload,
            ]);
            $webhook->markAsFailed($e->getMessage(), $e->getTraceAsString());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle PayPal subscription activated
     */
    private function handlePaypalSubscriptionActivated($payload, $webhook)
    {
        $resource = $payload['resource'] ?? [];
        $subscriptionId = $resource['id'] ?? null;

        $subscription = CustomerSubscription::where('gateway_subscription_id', $subscriptionId)->first();

        if (!$subscription) {
            $webhook->markAsSkipped('Subscription not found: ' . $subscriptionId);
            return;
        }

        $subscription->activate();
        $subscription->logEvent('subscription_activated', 'Subscription activated via PayPal');

        $webhook->markAsProcessed($subscription->id);
    }

    /**
     * Handle PayPal payment completed
     */
    private function handlePaypalPaymentCompleted($payload, $webhook)
    {
        $resource = $payload['resource'] ?? [];
        $billingAgreementId = $resource['billing_agreement_id'] ?? null;

        $subscription = CustomerSubscription::where('gateway_subscription_id', $billingAgreementId)->first();

        if (!$subscription) {
            $webhook->markAsSkipped('Subscription not found: ' . $billingAgreementId);
            return;
        }

        DB::transaction(function () use ($resource, $subscription, $webhook) {
            $transaction = SubscriptionTransaction::create([
                'business_id' => $subscription->business_id,
                'subscription_id' => $subscription->id,
                'contact_id' => $subscription->contact_id,
                'type' => 'payment',
                'status' => 'completed',
                'amount' => $resource['amount']['total'] ?? 0,
                'currency' => strtoupper($resource['amount']['currency'] ?? 'USD'),
                'payment_gateway' => 'paypal',
                'gateway_transaction_id' => $resource['id'],
                'gateway_response' => $resource,
            ]);

            $subscription->amount_paid += $transaction->amount;
            $subscription->save();

            $subscription->logEvent('payment_received', 'Payment received via PayPal: ' . $transaction->amount);

            $webhook->markAsProcessed($subscription->id, $transaction->id);
        });
    }

    /**
     * Handle PayPal subscription cancelled
     */
    private function handlePaypalSubscriptionCancelled($payload, $webhook)
    {
        $resource = $payload['resource'] ?? [];
        $subscriptionId = $resource['id'] ?? null;

        $subscription = CustomerSubscription::where('gateway_subscription_id', $subscriptionId)->first();

        if (!$subscription) {
            $webhook->markAsSkipped('Subscription not found: ' . $subscriptionId);
            return;
        }

        $subscription->cancel('Cancelled via PayPal', 'immediate');

        $webhook->markAsProcessed($subscription->id);
    }

    /**
     * Retry failed webhooks
     */
    public function retryFailed(Request $request)
    {
        if (!auth()->user() || !auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $webhookId = $request->webhook_id;
        $webhook = SubscriptionWebhook::find($webhookId);

        if (!$webhook || !$webhook->canBeRetried()) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook cannot be retried.'
            ], 400);
        }

        $webhook->retry();

        // Re-dispatch for processing
        // You could dispatch a job here to re-process the webhook

        return response()->json([
            'success' => true,
            'message' => 'Webhook queued for retry.'
        ]);
    }
}
