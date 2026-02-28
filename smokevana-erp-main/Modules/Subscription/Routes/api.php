<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Subscription API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your subscription module.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group and prefixed with "api/subscription".
|
*/

// Public routes (no auth required)
Route::prefix('v1')->group(function () {
    // Get available plans (public)
    Route::get('/plans', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'getPlans']);
    Route::get('/plans/{id}', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'getPlan']);
    
    // Validate discount code (public)
    Route::post('/validate-discount', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'validateDiscountCode']);
    
    // Prime products (public)
    Route::get('/prime-products', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'getPrimeProducts']);
});

// Protected routes (auth required)
Route::prefix('v1')->middleware(['auth:api'])->group(function () {
    // Customer subscription
    Route::get('/my-subscription', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'getCustomerSubscription']);
    Route::post('/subscribe', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'subscribe']);
    Route::post('/cancel', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'cancel']);
    
    // Payment & Lifecycle
    Route::post('/confirm-payment', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'confirmPayment']);
    Route::post('/change-plan', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'changePlan']);
    Route::post('/pause', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'pause']);
    Route::post('/resume', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'resume']);
    Route::post('/toggle-auto-renew', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'toggleAutoRenew']);
    
    // NMI Payment Processing
    Route::post('/subscribe-with-payment', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'subscribeWithPayment']);
    Route::post('/process-nmi-payment', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'processNMIPayment']);
    Route::post('/cancel-nmi-subscription', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'cancelNMISubscription']);
    Route::post('/refund', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'refundPayment']);
    
    // Prime eligibility check
    Route::get('/prime-eligibility', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'checkPrimeEligibility']);
    
    // Customer history
    Route::get('/history', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'getHistory']);
    Route::get('/invoices', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'getInvoices']);
});

// B2B / ERP Integration routes (API key auth)
Route::prefix('b2b')->group(function () {
    // Get subscription by contact ID (for B2B e-commerce integration)
    Route::get('/subscription/{contactId}', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'getByContactId']);
    
    // Testing endpoints (should be disabled in production)
    Route::post('/force-expire', [Modules\Subscription\Http\Controllers\Api\SubscriptionApiController::class, 'forceExpire']);
});

// Webhook routes (no auth, signature verification)
Route::prefix('webhooks')->group(function () {
    Route::post('/stripe', [Modules\Subscription\Http\Controllers\Api\WebhookController::class, 'handleStripe']);
    Route::post('/paypal', [Modules\Subscription\Http\Controllers\Api\WebhookController::class, 'handlePaypal']);
    Route::post('/nmi', [Modules\Subscription\Http\Controllers\Api\NMIWebhookController::class, 'handle']);
});

// Internal API routes (for ERP sync)
Route::prefix('internal')->middleware(['auth:api'])->group(function () {
    Route::get('/check-prime/{contact_id}', function ($contact_id) {
        $isPrime = \Modules\Subscription\Services\SubscriptionService::hasActivePrimeSubscription($contact_id);
        $discount = \Modules\Subscription\Services\SubscriptionService::getPrimeDiscount($contact_id);
        $multiplier = \Modules\Subscription\Services\SubscriptionService::getRewardPointsMultiplier($contact_id);
        
        return response()->json([
            'is_prime' => $isPrime,
            'discount_percentage' => $discount,
            'reward_multiplier' => $multiplier,
            'has_fast_delivery' => \Modules\Subscription\Services\SubscriptionService::hasFastDelivery($contact_id),
            'can_access_prime_products' => \Modules\Subscription\Services\SubscriptionService::canAccessPrimeProducts($contact_id),
            'has_bnpl' => \Modules\Subscription\Services\SubscriptionService::hasBNPL($contact_id),
            'bnpl_limit' => \Modules\Subscription\Services\SubscriptionService::getBNPLLimit($contact_id),
        ]);
    });
    
    Route::post('/track-savings/{contact_id}', function (\Illuminate\Http\Request $request, $contact_id) {
        \Modules\Subscription\Services\SubscriptionService::trackPrimeSavings($contact_id, $request->amount);
        return response()->json(['success' => true]);
    });
    
    Route::post('/track-points/{contact_id}', function (\Illuminate\Http\Request $request, $contact_id) {
        \Modules\Subscription\Services\SubscriptionService::trackRewardPointsEarned($contact_id, $request->base_points);
        return response()->json(['success' => true]);
    });
});
