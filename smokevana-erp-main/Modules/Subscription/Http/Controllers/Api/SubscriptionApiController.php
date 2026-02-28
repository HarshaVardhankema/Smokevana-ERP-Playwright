<?php

namespace Modules\Subscription\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\Entities\SubscriptionPlan;
use Modules\Subscription\Entities\CustomerSubscription;
use Modules\Subscription\Entities\SubscriptionInvoice;
use Modules\Subscription\Entities\SubscriptionTransaction;
use Modules\Subscription\Entities\SubscriptionDiscount;
use Modules\Subscription\Entities\PrimeProduct;

class SubscriptionApiController extends Controller
{
    /**
     * Get business_id from authenticated user's contact record or request
     */
    protected function getBusinessId(Request $request)
    {
        // First, try to get from authenticated user (contact)
        try{
            $contact = Auth::guard('api')->user();
        if ($contact) {
            return $contact->business_id;
        } else {
            return null;
        }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            return null;
        }
    }

    /**
     * Get available subscription plans
     */
    public function getPlans(Request $request)
    {
        $business_id = $this->getBusinessId($request);

        if (!$business_id) {
            return response()->json(['error' => 'Business ID required'], 400);
        }

        // Check if user is authenticated and get their subscription
        $userSubscription = null;
        $isSubscribed = false;
        $currentPlanId = null;
        $pendingInvoice = null;
        $contact = null;
        $customerGroup = null;
        
        try {
            // Use Auth::guard('api')->user() instead of $request->user() 
            // because this is a public route without auth middleware
            // This will attempt to authenticate if token is provided, but won't fail if not
            $contact = Auth::guard('api')->user();
            if ($contact) {
                // Load customer group relationship if not already loaded
                if (!$contact->relationLoaded('customerGroup')) {
                    $contact->load('customerGroup');
                }
                
                // Get customer's current group
                if ($contact->customerGroup) {
                    $customerGroup = [
                        'id' => $contact->customerGroup->id,
                        'name' => $contact->customerGroup->name,
                    ];
                }
                
                // Get subscription - include active, trial, pending, and paused (so paused still shows as current plan with correct status)
                $userSubscription = CustomerSubscription::where('contact_id', $contact->id)
                    ->whereIn('status', [
                        CustomerSubscription::STATUS_ACTIVE,
                        CustomerSubscription::STATUS_TRIAL,
                        CustomerSubscription::STATUS_PENDING,
                        CustomerSubscription::STATUS_PAUSED,
                    ])
                    ->with('plan')
                    ->orderBy('created_at', 'desc') // Get the most recent subscription
                    ->first();
                
                if ($userSubscription) {
                    $isSubscribed = true;
                    $currentPlanId = $userSubscription->plan_id;
                    
                    // Get pending invoice if subscription is pending
                    if ($userSubscription->status === CustomerSubscription::STATUS_PENDING) {
                        $pendingInvoice = SubscriptionInvoice::where('subscription_id', $userSubscription->id)
                            ->where('status', 'pending')
                            ->first();
                    }
                }
            }
        } catch (\Exception $e) {
            // User not authenticated or error - continue without subscription info
            Log::debug('Subscription API: Error getting user subscription', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        $plans = SubscriptionPlan::where('business_id', $business_id)
            ->active()
            ->public()
            ->with(['sellingPriceGroup', 'customerGroup'])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($plan) use ($currentPlanId, $customerGroup, $isSubscribed, $userSubscription) {
                // Determine if this is the current plan for the user.
                // Mark as current ONLY when the user is actually subscribed and this plan matches their subscription.
                $isCurrentPlan = false;
                if ($isSubscribed && $currentPlanId && $plan->id === $currentPlanId) {
                    $isCurrentPlan = true;
                }
                
                // Convert benefits from associative array to array format
                $benefitsArray = [];
                $allBenefits = $plan->getAllBenefits();
                if (is_array($allBenefits) && !empty($allBenefits)) {
                    foreach ($allBenefits as $key => $value) {
                        $benefitsArray[] = [
                            'key' => $key,
                            'value' => $value
                        ];
                    }
                }
                
                $data = [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'description' => $plan->description,
                    'price' => $plan->price,
                    'currency' => $plan->currency,
                    'billing_type' => $plan->billing_type,
                    'billing_cycle' => $plan->billing_cycle,
                    'has_trial' => $plan->has_trial,
                    'trial_days' => $plan->trial_days,
                    'is_prime' => $plan->is_prime,
                    'is_featured' => $plan->is_featured,
                    'badge_text' => $plan->badge_text,
                    'badge_color' => $plan->badge_color,
                    'features' => $plan->features,
                    'benefits' => $benefitsArray,
                    'price_group' => $plan->sellingPriceGroup ? [
                        'id' => $plan->sellingPriceGroup->id,
                        'name' => $plan->sellingPriceGroup->name,
                        'description' => $plan->sellingPriceGroup->description,
                    ] : null,
                    'customer_group' => $plan->customerGroup ? [
                        'id' => $plan->customerGroup->id,
                        'name' => $plan->customerGroup->name,
                    ] : null,
                ];

                // Only include is_current_plan when the user has at least one subscription we care about.
                if ($isSubscribed) {
                    $data['is_current_plan'] = $isCurrentPlan;

                    // When this is the user's current plan, also surface the current
                    // subscription status (e.g. active, trial, paused) alongside the plan.
                    if ($isCurrentPlan && $userSubscription) {
                        $data['subscription_status'] = $userSubscription->status;
                    }
                }

                return $data;
            });

        $response = [
            'success' => true,
            'data' => $plans
        ];

        // Add user subscription information if available
        if ($isSubscribed && $userSubscription) {
            // Determine whether UI should show pause/resume actions
            $canPause = $userSubscription->status === CustomerSubscription::STATUS_ACTIVE;
            $canResume = $userSubscription->status === CustomerSubscription::STATUS_PAUSED;

            $subscriptionData = [
                'is_subscribed' => true,
                'subscription_id' => $userSubscription->id,
                'subscription_no' => $userSubscription->subscription_no,
                'status' => $userSubscription->status,
                'show_active_status' => in_array($userSubscription->status, [
                    CustomerSubscription::STATUS_ACTIVE,
                    CustomerSubscription::STATUS_TRIAL,
                ], true),
                // Frontend can use these to decide whether to show Pause / Resume buttons
                'can_pause' => $canPause,
                'can_resume' => $canResume,
                'plan' => [
                    'id' => $userSubscription->plan->id,
                    'name' => $userSubscription->plan->name,
                    'slug' => $userSubscription->plan->slug,
                    'is_prime' => $userSubscription->plan->is_prime,
                    'customer_group' => $userSubscription->plan->customerGroup ? [
                        'id' => $userSubscription->plan->customerGroup->id,
                        'name' => $userSubscription->plan->customerGroup->name,
                    ] : null,
                ],
                'subscribed_at' => $userSubscription->subscribed_at,
                'expires_at' => $userSubscription->expires_at,
                'days_remaining' => $userSubscription->days_remaining,
                'auto_renew' => $userSubscription->auto_renew,
            ];
            
            // Add customer's current group for comparison
            if ($customerGroup) {
                $subscriptionData['current_customer_group'] = $customerGroup;
                
                // Check if there's a mismatch between customer group and plan's customer group
                $planGroupId = $userSubscription->plan->customer_group_id;
                if ($planGroupId && $customerGroup['id'] != $planGroupId) {
                    $subscriptionData['group_mismatch'] = true;
                    $subscriptionData['group_mismatch_message'] = 'Customer is in "' . $customerGroup['name'] . '" group but subscription plan is associated with a different group.';
                }
            }
            
            // Add invoice information if subscription is pending
            if ($pendingInvoice) {
                $subscriptionData['pending_invoice'] = [
                    'invoice_id' => $pendingInvoice->id,
                    'invoice_no' => $pendingInvoice->invoice_no,
                    'amount_due' => $pendingInvoice->amount_due,
                    'currency' => $pendingInvoice->currency,
                    'status' => $pendingInvoice->status,
                ];
            }
            
            $response['user_subscription'] = $subscriptionData;
        } else {
            $response['user_subscription'] = [
                'is_subscribed' => false,
                'plan' => null,
            ];
            
            // Still include customer group info even if not subscribed
            if ($customerGroup) {
                $response['user_subscription']['current_customer_group'] = $customerGroup;
            }
        }

        return response()->json($response);
    }

    /**
     * Get plan details
     */
    public function getPlan(Request $request, $id)
    {
        $business_id = $this->getBusinessId($request);

        $plan = SubscriptionPlan::where('business_id', $business_id)
            ->where('id', $id)
            ->active()
            ->with('sellingPriceGroup')
            ->first();

        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'description' => $plan->description,
                'price' => $plan->price,
                'setup_fee' => $plan->setup_fee,
                'currency' => $plan->currency,
                'billing_type' => $plan->billing_type,
                'billing_cycle' => $plan->billing_cycle,
                'billing_cycle_days' => $plan->billing_cycle_days,
                'has_trial' => $plan->has_trial,
                'trial_days' => $plan->trial_days,
                'is_prime' => $plan->is_prime,
                'discount_percentage' => $plan->discount_percentage,
                'reward_points_multiplier' => $plan->reward_points_multiplier,
                'fast_delivery_enabled' => $plan->fast_delivery_enabled,
                'prime_products_access' => $plan->prime_products_access,
                'bnpl_enabled' => $plan->bnpl_enabled,
                'bnpl_limit' => $plan->bnpl_limit,
                'is_featured' => $plan->is_featured,
                'features' => $plan->features,
                'benefits' => array_values($plan->getAllBenefits()), // Convert object to array
                'price_group' => $plan->sellingPriceGroup ? [
                    'id' => $plan->sellingPriceGroup->id,
                    'name' => $plan->sellingPriceGroup->name,
                    'description' => $plan->sellingPriceGroup->description,
                ] : null,
            ]
        ]);
    }

    /**
     * Get customer's subscription status
     */
    public function getCustomerSubscription(Request $request)
    {
        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Treat active, trial, and paused subscriptions as \"current\" for this endpoint.
        // Paused subscriptions should still show full details to the customer.
        $subscription = CustomerSubscription::where('contact_id', $contact->id)
            ->whereIn('status', [
                CustomerSubscription::STATUS_ACTIVE,
                CustomerSubscription::STATUS_TRIAL,
                CustomerSubscription::STATUS_PAUSED,
            ])
            ->with('plan')
            ->first();

        if (!$subscription || $subscription->isExpired()) {
            return response()->json([
                'success' => true,
                'has_subscription' => false,
                'data' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'has_subscription' => true,
            'data' => [
                'subscription_no' => $subscription->subscription_no,
                'status' => $subscription->status,
                'plan' => [
                    'id' => $subscription->plan->id,
                    'name' => $subscription->plan->name,
                    'is_prime' => $subscription->plan->is_prime,
                ],
                'subscribed_at' => $subscription->subscribed_at,
                'expires_at' => $subscription->expires_at,
                'days_remaining' => $subscription->days_remaining,
                'auto_renew' => $subscription->auto_renew,
                'benefits' => array_values($subscription->applied_benefits ?? $subscription->plan->getAllBenefits()), // Convert object to array
                'prime_discount' => $subscription->getPrimeDiscount(),
                'reward_multiplier' => $subscription->getRewardPointsMultiplier(),
                'has_fast_delivery' => $subscription->hasFastDelivery(),
                'can_access_prime_products' => $subscription->canAccessPrimeProducts(),
                'has_bnpl' => $subscription->hasBNPL(),
                'bnpl_limit' => $subscription->getBNPLLimit(),
            ]
        ]);
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'discount_code' => 'nullable|string',
        ]);

        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if customer already has active subscription for this plan
        $existing = CustomerSubscription::where('contact_id', $contact->id)
            ->where('plan_id', $plan->id)
            ->active()
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'error' => 'You already have an active subscription for this plan.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Apply discount if provided
            $discount = null;
            $discountAmount = 0;
            if ($request->discount_code) {
                $discount = SubscriptionDiscount::findByCode($request->discount_code, $plan->business_id);
                if ($discount && $discount->canBeUsedByCustomer($contact->id) && $discount->appliesToPlan($plan->id)) {
                    $discountAmount = $discount->calculateDiscount($plan->price);
                }
            }

            $totalAmount = $plan->price + $plan->setup_fee - $discountAmount;

            // Create subscription
            $subscription = CustomerSubscription::create([
                'business_id' => $plan->business_id,
                'contact_id' => $contact->id,
                'plan_id' => $plan->id,
                'status' => 'pending',
                'auto_renew' => $request->auto_renew ?? true,
                'source' => 'ecommerce_portal',
                'metadata' => $discount ? ['discount_code' => $discount->code, 'discount_amount' => $discountAmount] : null,
            ]);

            // Create invoice
            $invoice = SubscriptionInvoice::create([
                'business_id' => $plan->business_id,
                'subscription_id' => $subscription->id,
                'contact_id' => $contact->id,
                'plan_id' => $plan->id,
                'type' => 'subscription',
                'subtotal' => $plan->price + $plan->setup_fee,
                'discount_amount' => $discountAmount,
                'discount_code' => $discount ? $discount->code : null,
                'total' => $totalAmount,
                'currency' => $plan->currency,
                'amount_due' => $totalAmount,
                'status' => 'pending',
                'due_date' => now()->addDays(7),
                'line_items' => [
                    [
                        'description' => $plan->name . ' Subscription',
                        'quantity' => 1,
                        'unit_price' => $plan->price,
                        'total' => $plan->price,
                    ],
                    $plan->setup_fee > 0 ? [
                        'description' => 'Setup Fee',
                        'quantity' => 1,
                        'unit_price' => $plan->setup_fee,
                        'total' => $plan->setup_fee,
                    ] : null,
                ],
            ]);

            // Increment discount usage
            if ($discount) {
                $discount->incrementUsage();
            }

            // Log event
            $subscription->logEvent('subscription_created', 'Subscription created via e-commerce portal');

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'subscription_id' => $subscription->id,
                    'subscription_no' => $subscription->subscription_no,
                    'invoice_id' => $invoice->id,
                    'invoice_no' => $invoice->invoice_no,
                    'amount_due' => $invoice->amount_due,
                    'currency' => $invoice->currency,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Subscription failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = CustomerSubscription::where('contact_id', $contact->id)
            ->active()
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'error' => 'No active subscription found.'
            ], 404);
        }

        try {
            $subscription->cancel(
                $request->reason,
                $request->cancel_immediately ? 'immediate' : 'end_of_period'
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully.',
                'effective_date' => $request->cancel_immediately ? now() : $subscription->expires_at,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Cancellation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check prime eligibility for a customer
     */
    public function checkPrimeEligibility(Request $request)
    {
        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = CustomerSubscription::where('contact_id', $contact->id)
            ->active()
            ->with('plan')
            ->first();

        $isPrime = $subscription && $subscription->hasPrimeBenefits();

        return response()->json([
            'success' => true,
            'is_prime' => $isPrime,
            'benefits' => $isPrime ? [
                'discount_percentage' => $subscription->getPrimeDiscount(),
                'reward_multiplier' => $subscription->getRewardPointsMultiplier(),
                'fast_delivery' => $subscription->hasFastDelivery(),
                'prime_products_access' => $subscription->canAccessPrimeProducts(),
                'bnpl_enabled' => $subscription->hasBNPL(),
                'bnpl_limit' => $subscription->getBNPLLimit(),
            ] : null,
        ]);
    }

    /**
     * Get prime-only products
     */
    public function getPrimeProducts(Request $request)
    {
        $business_id = $this->getBusinessId($request);
        $contact = $request->user();

        // Get customer's subscription to determine plan
        $planId = null;
        if ($contact) {
            $subscription = CustomerSubscription::where('contact_id', $contact->id)
                ->active()
                ->first();
            if ($subscription) {
                $planId = $subscription->plan_id;
            }
        }

        $primeProducts = PrimeProduct::where('business_id', $business_id)
            ->active()
            ->valid()
            ->when($planId, function ($q) use ($planId) {
                $q->forPlan($planId);
            })
            ->with('product:id,name,sku,image')
            ->get()
            ->map(function ($pp) {
                return [
                    'product_id' => $pp->product_id,
                    'product' => $pp->product ? [
                        'id' => $pp->product->id,
                        'name' => $pp->product->name,
                        'sku' => $pp->product->sku,
                        'image' => $pp->product->image,
                    ] : null,
                    'access_type' => $pp->access_type,
                    'additional_discount' => $pp->additional_discount,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $primeProducts
        ]);
    }

    /**
     * Validate discount code
     */
    public function validateDiscountCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $business_id = $this->getBusinessId($request);
        $contact = $request->user();

        $discount = SubscriptionDiscount::findByCode($request->code, $business_id);

        if (!$discount) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'error' => 'Invalid or expired discount code.'
            ]);
        }

        if (!$discount->appliesToPlan($request->plan_id)) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'error' => 'This discount code does not apply to the selected plan.'
            ]);
        }

        if ($contact && !$discount->canBeUsedByCustomer($contact->id)) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'error' => 'You have already used this discount code.'
            ]);
        }

        $plan = SubscriptionPlan::find($request->plan_id);
        $discountAmount = $discount->calculateDiscount($plan->price);

        return response()->json([
            'success' => true,
            'valid' => true,
            'discount' => [
                'code' => $discount->code,
                'type' => $discount->type,
                'value' => $discount->value,
                'formatted_value' => $discount->formatted_value,
                'discount_amount' => $discountAmount,
                'duration' => $discount->duration,
            ]
        ]);
    }

    /**
     * Get customer's subscription history
     */
    public function getHistory(Request $request)
    {
        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscriptions = CustomerSubscription::where('contact_id', $contact->id)
            ->with('plan:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sub) {
                return [
                    'subscription_no' => $sub->subscription_no,
                    'plan_name' => $sub->plan ? $sub->plan->name : 'N/A',
                    'status' => $sub->status,
                    'subscribed_at' => $sub->subscribed_at,
                    'expires_at' => $sub->expires_at,
                    'cancelled_at' => $sub->cancelled_at,
                    'amount_paid' => $sub->amount_paid,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $subscriptions
        ]);
    }

    /**
     * Get customer's invoices
     */
    public function getInvoices(Request $request)
    {
        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $invoices = SubscriptionInvoice::where('contact_id', $contact->id)
            ->with('plan:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($inv) {
                return [
                    'invoice_no' => $inv->invoice_no,
                    'plan_name' => $inv->plan ? $inv->plan->name : 'N/A',
                    'total' => $inv->total,
                    'amount_paid' => $inv->amount_paid,
                    'status' => $inv->status,
                    'due_date' => $inv->due_date,
                    'paid_at' => $inv->paid_at,
                    'created_at' => $inv->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $invoices
        ]);
    }

    /**
     * Confirm payment for a subscription (bypass payment gateway for testing)
     * In production, this would be called by payment gateway webhook
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:customer_subscriptions,id',
            'payment_method' => 'nullable|string',
            'transaction_id' => 'nullable|string',
        ]);

        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = CustomerSubscription::where('contact_id', $contact->id)
            ->where('id', $request->subscription_id)
            ->with('plan')
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'error' => 'Subscription not found.'
            ], 404);
        }

        if ($subscription->status === CustomerSubscription::STATUS_ACTIVE) {
            return response()->json([
                'success' => false,
                'error' => 'Subscription is already active.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Record the payment transaction
            $transaction = SubscriptionTransaction::create([
                'business_id' => $subscription->business_id,
                'subscription_id' => $subscription->id,
                'contact_id' => $contact->id,
                'type' => 'payment',
                'amount' => $subscription->plan->price,
                'currency' => $subscription->plan->currency ?? 'USD',
                'status' => 'completed',
                'payment_method' => $request->payment_method ?? 'manual',
                'gateway_transaction_id' => $request->transaction_id ?? 'TXN_' . time(),
                'description' => 'Subscription payment for ' . $subscription->plan->name,
                'processed_at' => now(),
            ]);

            // Update subscription amount_paid
            $subscription->increment('amount_paid', $subscription->plan->price);

            // Activate the subscription
            $subscription->activate();

            // Update invoice if exists
            $invoice = SubscriptionInvoice::where('subscription_id', $subscription->id)
                ->where('status', 'pending')
                ->first();

            if ($invoice) {
                $invoice->update([
                    'status' => 'paid',
                    'amount_paid' => $invoice->total,
                    'amount_due' => 0,
                    'paid_at' => now(),
                ]);
            }

            $subscription->logEvent('payment_confirmed', 'Payment confirmed. Transaction ID: ' . $transaction->gateway_transaction_id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed and subscription activated.',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'subscription_code' => $subscription->subscription_code,
                    'status' => $subscription->status,
                    'transaction_id' => $transaction->gateway_transaction_id,
                    'activated_at' => $subscription->subscribed_at,
                    'expires_at' => $subscription->expires_at,
                    'customer_group' => $subscription->contact->customerGroup->name ?? null,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Payment confirmation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change subscription plan (upgrade/downgrade)
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'new_plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = CustomerSubscription::where('contact_id', $contact->id)
            ->active()
            ->with('plan')
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'error' => 'No active subscription found.'
            ], 404);
        }

        $newPlan = SubscriptionPlan::where('id', $request->new_plan_id)
            ->where('business_id', $subscription->business_id)
            ->active()
            ->first();

        if (!$newPlan) {
            return response()->json([
                'success' => false,
                'error' => 'New plan not found or not available.'
            ], 404);
        }

        if ($newPlan->id === $subscription->plan_id) {
            return response()->json([
                'success' => false,
                'error' => 'You are already subscribed to this plan.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $oldPlan = $subscription->plan;
            $subscription->changePlan($newPlan->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plan changed successfully.',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'old_plan' => $oldPlan->name,
                    'new_plan' => $newPlan->name,
                    'new_price' => $newPlan->price,
                    'new_expires_at' => $subscription->expires_at,
                    'customer_group' => $subscription->contact->fresh()->customerGroup->name ?? null,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Plan change failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pause subscription
     */
    public function pause(Request $request)
    {
        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = CustomerSubscription::where('contact_id', $contact->id)
            ->where('status', CustomerSubscription::STATUS_ACTIVE)
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'error' => 'No active subscription found to pause.'
            ], 404);
        }

        try {
            $subscription->pause();

            return response()->json([
                'success' => true,
                'message' => 'Subscription paused successfully.',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'status' => $subscription->status,
                    'paused_at' => $subscription->paused_at,
                    'customer_group' => $subscription->contact->fresh()->customerGroup->name ?? null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Pause failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resume subscription
     */
    public function resume(Request $request)
    {
        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = CustomerSubscription::where('contact_id', $contact->id)
            ->where('status', CustomerSubscription::STATUS_PAUSED)
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'error' => 'No paused subscription found to resume.'
            ], 404);
        }

        try {
            $subscription->resume();

            return response()->json([
                'success' => true,
                'message' => 'Subscription resumed successfully.',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'status' => $subscription->status,
                    'resumed_at' => $subscription->resumed_at,
                    'customer_group' => $subscription->contact->fresh()->customerGroup->name ?? null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Resume failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle auto-renewal
     */
    public function toggleAutoRenew(Request $request)
    {
        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = CustomerSubscription::where('contact_id', $contact->id)
            ->whereIn('status', [CustomerSubscription::STATUS_ACTIVE, CustomerSubscription::STATUS_PAUSED])
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'error' => 'No subscription found.'
            ], 404);
        }

        $subscription->auto_renew = !$subscription->auto_renew;
        $subscription->save();

        $subscription->logEvent(
            'auto_renew_toggled',
            'Auto-renewal ' . ($subscription->auto_renew ? 'enabled' : 'disabled')
        );

        return response()->json([
            'success' => true,
            'message' => 'Auto-renewal ' . ($subscription->auto_renew ? 'enabled' : 'disabled'),
            'auto_renew' => $subscription->auto_renew,
        ]);
    }

    /**
     * Get subscription by contact ID (for internal ERP use)
     */
    public function getByContactId(Request $request, $contactId)
    {
        $business_id = $this->getBusinessId($request);

        if (!$business_id) {
            return response()->json(['error' => 'Business ID required'], 400);
        }

        $subscription = CustomerSubscription::where('business_id', $business_id)
            ->where('contact_id', $contactId)
            ->active()
            ->with(['plan', 'contact.customerGroup'])
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => true,
                'has_subscription' => false,
                'data' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'has_subscription' => true,
            'data' => [
                'id' => $subscription->id,
                'subscription_code' => $subscription->subscription_code,
                'status' => $subscription->status,
                'plan' => [
                    'id' => $subscription->plan->id,
                    'name' => $subscription->plan->name,
                    'price' => $subscription->plan->price,
                    'is_prime' => $subscription->plan->is_prime,
                    'customer_group_id' => $subscription->plan->customer_group_id,
                ],
                'contact' => [
                    'id' => $subscription->contact->id,
                    'name' => $subscription->contact->name,
                    'customer_group' => $subscription->contact->customerGroup->name ?? null,
                ],
                'subscribed_at' => $subscription->subscribed_at,
                'expires_at' => $subscription->expires_at,
                'days_remaining' => $subscription->days_remaining,
                'auto_renew' => $subscription->auto_renew,
                'is_prime' => $subscription->hasPrimeBenefits(),
                'prime_discount' => $subscription->getPrimeDiscount(),
            ]
        ]);
    }

    /**
     * Force expire subscription (for testing)
     */
    public function forceExpire(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:customer_subscriptions,id',
        ]);

        $business_id = $this->getBusinessId($request);

        $subscription = CustomerSubscription::where('business_id', $business_id)
            ->where('id', $request->subscription_id)
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'error' => 'Subscription not found.'
            ], 404);
        }

        try {
            // Force set expiry date to past
            $subscription->expires_at = now()->subDay();
            $subscription->current_period_end = now()->subDay();
            $subscription->save();

            // Expire the subscription
            $subscription->expire();

            return response()->json([
                'success' => true,
                'message' => 'Subscription force-expired successfully.',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'status' => $subscription->status,
                    'customer_group' => $subscription->contact->fresh()->customerGroup->name ?? null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Force expire failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process subscription payment via NMI
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processNMIPayment(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:customer_subscriptions,id',
            'payment_token' => 'required|string',
            'billing' => 'nullable|array',
            'shipping' => 'nullable|array',
        ]);

        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = CustomerSubscription::where('contact_id', $contact->id)
            ->where('id', $request->subscription_id)
            ->with(['plan', 'contact'])
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'error' => 'Subscription not found.'
            ], 404);
        }

        // Get the invoice or calculate amount
        $invoice = SubscriptionInvoice::where('subscription_id', $subscription->id)
            ->where('status', 'pending')
            ->first();

        $amount = $invoice ? $invoice->amount_due : $subscription->plan->price;

        try {
            DB::beginTransaction();

            // Initialize NMI Service with business_id for settings lookup
            $nmiService = new \Modules\Subscription\Services\NMISubscriptionService($subscription->business_id);

            // Prepare billing data
            $billingData = $request->billing ?? [];
            if (empty($billingData) && $contact) {
                $billingData = [
                    'first_name' => $contact->first_name ?? $contact->name,
                    'last_name' => $contact->last_name ?? '',
                    'email' => $contact->email ?? '',
                    'phone' => $contact->mobile ?? '',
                    'company' => $contact->supplier_business_name ?? '',
                ];
                
                // Get default address if available
                if ($contact->customerAddress) {
                    $address = $contact->customerAddress->where('is_default', 1)->first();
                    if ($address) {
                        $billingData['address1'] = $address->street ?? '';
                        $billingData['city'] = $address->city ?? '';
                        $billingData['state'] = $address->state ?? '';
                        $billingData['zip'] = $address->zip ?? '';
                        $billingData['country'] = $address->country ?? 'US';
                    }
                }
            }

            // Process payment through NMI
            $paymentResult = $nmiService->processSubscriptionPayment([
                'amount' => $amount,
                'payment_token' => $request->payment_token,
                'billing' => $billingData,
                'shipping' => $request->shipping ?? $billingData,
                'order_id' => 'SUB_' . $subscription->subscription_no,
                'description' => 'Subscription Payment - ' . $subscription->plan->name,
                'create_recurring' => $subscription->auto_renew,
                'billing_cycle' => $subscription->plan->billing_cycle,
            ]);

            if (!$paymentResult['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => $paymentResult['message'] ?? 'Payment failed',
                    'details' => $paymentResult['data'] ?? null
                ], 400);
            }

            // Record the payment transaction
            $transaction = SubscriptionTransaction::create([
                'business_id' => $subscription->business_id,
                'subscription_id' => $subscription->id,
                'contact_id' => $contact->id,
                'type' => 'payment',
                'amount' => $amount,
                'currency' => $subscription->plan->currency ?? 'USD',
                'status' => 'completed',
                'payment_method' => 'nmi',
                'gateway_transaction_id' => $paymentResult['transaction_id'],
                'gateway_subscription_id' => $paymentResult['subscription_id'],
                'description' => 'NMI Payment for ' . $subscription->plan->name,
                'metadata' => [
                    'nmi_response' => $paymentResult['data'],
                    'payment_token' => substr($request->payment_token, 0, 10) . '...',
                ],
                'processed_at' => now(),
            ]);

            // Update subscription with NMI subscription ID if created
            if ($paymentResult['subscription_id']) {
                $subscription->update([
                    'gateway_subscription_id' => $paymentResult['subscription_id'],
                    'payment_gateway' => 'nmi',
                ]);
            }

            // Update subscription amount_paid
            $subscription->increment('amount_paid', $amount);

            // Activate the subscription
            if ($subscription->status !== CustomerSubscription::STATUS_ACTIVE) {
                $subscription->activate();
            }

            // Update invoice if exists
            if ($invoice) {
                $invoice->update([
                    'status' => 'paid',
                    'amount_paid' => $invoice->total,
                    'amount_due' => 0,
                    'paid_at' => now(),
                    'payment_method' => 'nmi',
                    'payment_reference' => $paymentResult['transaction_id'],
                ]);
            }

            $subscription->logEvent('nmi_payment_processed', 'NMI Payment processed. Transaction ID: ' . $paymentResult['transaction_id']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully.',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'transaction_id' => $paymentResult['transaction_id'],
                    'nmi_subscription_id' => $paymentResult['subscription_id'],
                    'amount' => $amount,
                    'status' => $subscription->fresh()->status,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('NMI Subscription Payment Error', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Subscribe with immediate NMI payment
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribeWithPayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'payment_token' => 'required|string',
            'discount_code' => 'nullable|string',
            'billing' => 'nullable|array',
            'auto_renew' => 'nullable|boolean',
        ]);

        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if customer already has active subscription for this plan
        $existing = CustomerSubscription::where('contact_id', $contact->id)
            ->where('plan_id', $plan->id)
            ->active()
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'error' => 'You already have an active subscription for this plan.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Apply discount if provided
            $discount = null;
            $discountAmount = 0;
            if ($request->discount_code) {
                $discount = SubscriptionDiscount::findByCode($request->discount_code, $plan->business_id);
                if ($discount && $discount->canBeUsedByCustomer($contact->id) && $discount->appliesToPlan($plan->id)) {
                    $discountAmount = $discount->calculateDiscount($plan->price);
                }
            }

            $totalAmount = $plan->price + $plan->setup_fee - $discountAmount;

            // Initialize NMI Service with business_id for settings lookup
            $nmiService = new \Modules\Subscription\Services\NMISubscriptionService($plan->business_id);

            // Prepare billing data
            $billingData = $request->billing ?? [];
            if (empty($billingData)) {
                $billingData = [
                    'first_name' => $contact->first_name ?? $contact->name,
                    'last_name' => $contact->last_name ?? '',
                    'email' => $contact->email ?? '',
                    'phone' => $contact->mobile ?? '',
                    'company' => $contact->supplier_business_name ?? '',
                ];
            }

            // Process payment through NMI first
            $paymentResult = $nmiService->processSubscriptionPayment([
                'amount' => $totalAmount,
                'payment_token' => $request->payment_token,
                'billing' => $billingData,
                'order_id' => 'SUB_' . time() . '_' . $contact->id,
                'description' => 'Subscription - ' . $plan->name,
                'create_recurring' => $request->auto_renew ?? true,
                'billing_cycle' => $plan->billing_cycle,
            ]);

            if (!$paymentResult['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => $paymentResult['message'] ?? 'Payment failed',
                    'details' => $paymentResult['data'] ?? null
                ], 400);
            }

            // Create subscription (already paid)
            $subscription = CustomerSubscription::create([
                'business_id' => $plan->business_id,
                'contact_id' => $contact->id,
                'plan_id' => $plan->id,
                'status' => 'pending', // Will be activated below
                'auto_renew' => $request->auto_renew ?? true,
                'source' => 'ecommerce_portal',
                'payment_gateway' => 'nmi',
                'gateway_subscription_id' => $paymentResult['subscription_id'],
                'amount_paid' => $totalAmount,
                'metadata' => [
                    'discount_code' => $discount ? $discount->code : null,
                    'discount_amount' => $discountAmount,
                    'nmi_transaction_id' => $paymentResult['transaction_id'],
                ],
            ]);

            // Create invoice (already paid)
            $invoice = SubscriptionInvoice::create([
                'business_id' => $plan->business_id,
                'subscription_id' => $subscription->id,
                'contact_id' => $contact->id,
                'plan_id' => $plan->id,
                'type' => 'subscription',
                'subtotal' => $plan->price + $plan->setup_fee,
                'discount_amount' => $discountAmount,
                'discount_code' => $discount ? $discount->code : null,
                'total' => $totalAmount,
                'currency' => $plan->currency,
                'amount_paid' => $totalAmount,
                'amount_due' => 0,
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => 'nmi',
                'payment_reference' => $paymentResult['transaction_id'],
                'line_items' => array_filter([
                    [
                        'description' => $plan->name . ' Subscription',
                        'quantity' => 1,
                        'unit_price' => $plan->price,
                        'total' => $plan->price,
                    ],
                    $plan->setup_fee > 0 ? [
                        'description' => 'Setup Fee',
                        'quantity' => 1,
                        'unit_price' => $plan->setup_fee,
                        'total' => $plan->setup_fee,
                    ] : null,
                ]),
            ]);

            // Record transaction
            SubscriptionTransaction::create([
                'business_id' => $plan->business_id,
                'subscription_id' => $subscription->id,
                'contact_id' => $contact->id,
                'type' => 'payment',
                'amount' => $totalAmount,
                'currency' => $plan->currency ?? 'USD',
                'status' => 'completed',
                'payment_method' => 'nmi',
                'gateway_transaction_id' => $paymentResult['transaction_id'],
                'gateway_subscription_id' => $paymentResult['subscription_id'],
                'description' => 'Initial subscription payment via NMI',
                'metadata' => [
                    'nmi_response' => $paymentResult['data'],
                ],
                'processed_at' => now(),
            ]);

            // Increment discount usage
            if ($discount) {
                $discount->incrementUsage();
            }

            // Activate the subscription
            $subscription->activate();

            // Log event
            $subscription->logEvent('subscription_created_with_payment', 'Subscription created and paid via NMI. Transaction ID: ' . $paymentResult['transaction_id']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subscription created and payment processed successfully.',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'subscription_no' => $subscription->subscription_no,
                    'invoice_id' => $invoice->id,
                    'invoice_no' => $invoice->invoice_no,
                    'transaction_id' => $paymentResult['transaction_id'],
                    'nmi_subscription_id' => $paymentResult['subscription_id'],
                    'amount_paid' => $totalAmount,
                    'status' => 'active',
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscribe with Payment Error', [
                'plan_id' => $request->plan_id,
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Subscription failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel NMI recurring subscription
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelNMISubscription(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:customer_subscriptions,id',
        ]);

        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = CustomerSubscription::where('contact_id', $contact->id)
            ->where('id', $request->subscription_id)
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'error' => 'Subscription not found.'
            ], 404);
        }

        try {
            // Cancel on NMI if there's a gateway subscription ID
            if ($subscription->gateway_subscription_id) {
                $nmiService = new \Modules\Subscription\Services\NMISubscriptionService($subscription->business_id);
                $cancelResult = $nmiService->cancelSubscription($subscription->gateway_subscription_id);
                
                Log::info('NMI Subscription Cancellation', [
                    'subscription_id' => $subscription->id,
                    'nmi_subscription_id' => $subscription->gateway_subscription_id,
                    'result' => $cancelResult
                ]);
            }

            // Cancel in our system
            $subscription->cancel('Customer requested cancellation');

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully.',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'status' => 'cancelled',
                    'benefits_until' => $subscription->expires_at?->format('Y-m-d'),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Cancel NMI Subscription Error', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Cancellation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process refund for subscription payment
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refundPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'nullable|string',
        ]);

        $contact = $request->user();

        if (!$contact) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Find the transaction
        $transaction = SubscriptionTransaction::where('gateway_transaction_id', $request->transaction_id)
            ->where('contact_id', $contact->id)
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'error' => 'Transaction not found.'
            ], 404);
        }

        try {
            $nmiService = new \Modules\Subscription\Services\NMISubscriptionService($transaction->business_id);
            
            $refundResult = $nmiService->refundTransaction(
                $request->transaction_id,
                $request->amount
            );

            if (!$refundResult['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $refundResult['responsetext'] ?? 'Refund failed',
                ], 400);
            }

            // Record refund transaction
            $refundTransaction = SubscriptionTransaction::create([
                'business_id' => $transaction->business_id,
                'subscription_id' => $transaction->subscription_id,
                'contact_id' => $contact->id,
                'type' => 'refund',
                'amount' => $request->amount ?? $transaction->amount,
                'currency' => $transaction->currency,
                'status' => 'completed',
                'payment_method' => 'nmi',
                'gateway_transaction_id' => $refundResult['transactionid'],
                'description' => 'Refund: ' . ($request->reason ?? 'Customer refund'),
                'metadata' => [
                    'original_transaction_id' => $request->transaction_id,
                    'nmi_response' => $refundResult,
                ],
                'processed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully.',
                'data' => [
                    'refund_transaction_id' => $refundResult['transactionid'],
                    'amount_refunded' => $request->amount ?? $transaction->amount,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Refund Payment Error', [
                'transaction_id' => $request->transaction_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Refund failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
