<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ECOM\CartController;
use App\Http\Controllers\ECOM\OrdersController;
use App\Http\Controllers\ECOM\CatalogController;
use App\Http\Controllers\B2CECOM\GuestController;
use App\Http\Controllers\B2CECOM\B2cOrdersController;
use App\Http\Controllers\ECOM\WishlistsController;
use App\Http\Controllers\ECOM\CustomerReviewController;
use App\Http\Controllers\Staff\StaffAuthController;
use App\Http\Controllers\OrderfulfillmentController;
use App\Http\Controllers\B2CECOM\GuestCartController;
use App\Http\Controllers\ECOM\CustomerAuthController;
use App\Http\Controllers\ECOM\PaymentOrderController;
use App\Http\Controllers\B2CECOM\B2cCatalogController;
use App\Http\Controllers\B2CECOM\UnifiedCartController;
use App\Http\Controllers\MerchantApplicationController;
use App\Http\Controllers\B2CECOM\B2cCustomerAuthController;
use App\Http\Controllers\B2CECOM\B2CPaymentOrderController;
use App\Http\Controllers\CreditLineController;
use App\Http\Controllers\Staff\CommissionAgentController;
use App\Http\Controllers\TransactionPaymentController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ECOM\MultichannelController;
use App\Http\Controllers\ECOM\B2bGuestCartController;
use App\Http\Controllers\ECOM\B2bGuestController;
use App\Http\Controllers\CustomerPaymentMethodController;
use App\Http\Controllers\ECOM\ReferralProgramController;
use App\Http\Controllers\ECOM\RewardPointsController;
use App\Http\Controllers\ECOM\GiftCardController;
use App\Http\Controllers\API\FirebaseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// B2B ECOM Routes
Route::post('img',[BusinessController::class,'storeImage']);
Route::middleware(['throttle:throttle5pm'])->group(function () {
    Route::post('/customer/login', [CustomerAuthController::class, 'login']);
    Route::post('/customer/register', [CustomerAuthController::class,'register']);
    Route::post('/vendor/register', [CustomerAuthController::class,'vendorRegister']);
    Route::post('/customer/forgot-password', [CustomerAuthController::class,'sendResetLinkEmail']);
    Route::post('/customer/verify-reset-otp', [CustomerAuthController::class,'verifyResetOtp']);
    Route::post('/customer/set-password', [CustomerAuthController::class,'reset']);
    Route::post('/customer/reset-password-with-otp', [CustomerAuthController::class,'resetPasswordWithOtp']);
    Route::post('/customer/email-confirmation', [CustomerAuthController::class, 'emailConfirmation']);
    Route::post('/customer/contact-us', [CustomerAuthController::class,'contactus']);
    Route::post('/customer/subscribe', [CustomerAuthController::class,'subscribe']);
    Route::post('/customer/unsubscribe', [CustomerAuthController::class,'unsubscribe']);
    Route::get('/customer/unsubscribe', [CustomerAuthController::class,'unsubscribe']);
    Route::post('/customer/refresh-token', [CustomerAuthController::class,'refreshToken']);
    Route::post('/customer/add-idea-list', [CustomerAuthController::class, 'addIdeaList']);
    Route::get('/customer/get-idea-list',[CustomerAuthController::class,'getIdeaList']);

   
});
// Wishlist: allow either API customer or guest_session (no location/brand in path)
Route::middleware(['ecom.auth.or.guest'])->group(function () {
    Route::resource('wishlist', WishlistsController::class)->names('b2b.wishlist');
});

Route::middleware(['ecom.customer.validate'])->group(function () {

    // customer notification api channel 
    Route::get('/customer/notification', [CustomerAuthController::class, 'notification']);

    // customer profile api
    Route::get('/customer/logout', [CustomerAuthController::class, 'logout']);
    
    // Sales Order API - Cancel
    Route::delete('/sells/voidSell/{id}',[App\Http\Controllers\SellPosController::class,'customervoidSell'])->name('api.sells.voidSell');
    
    Route::post('/sells/cancel/{id}', [App\Http\Controllers\SellController::class, 'cancelSalesOrder'])->name('api.sells.cancel');
    Route::get('/customer/my-account', [CustomerAuthController::class, 'myAccount']);
    Route::get('/customer/my-orders', [OrdersController::class, 'mySaleOrders']);
    Route::get('/customer/orders', [OrdersController::class, 'getOrderResponse']); // Standardized order response API
    Route::get('/customer/orders/download-logs', [OrdersController::class, 'getDownloadLogs']); // Get download logs for orders
    Route::get('/customer/my-invoices', [OrdersController::class, 'mySaleInvoices']);
    Route::get('/customer/my-order/{orderId}', [OrdersController::class, 'getOrderDetails']);
    Route::get('/customer/my-order-print/{orderId}', [OrdersController::class, 'printInvoice']);
    Route::get('/customer/my-order/{orderId}/packing-slip-pdf', [OrdersController::class, 'packingSlipPdf']);
    // Route::get('/customer/my-order/{orderId}/tracking', [OrdersController::class, 'getOrderTracking']);
    Route::post('/customer/my-order/{orderId}/mark-received', [OrdersController::class, 'markAsReceived']);
    Route::get('/customer/buy-again', [OrdersController::class, 'getAllBuyAgain']);
    Route::post('/customer/buy-again', [OrdersController::class, 'buyAgainFromBody']);
    Route::get('/customer/my-order/{orderId}/buy-again', [OrdersController::class, 'getBuyAgain']);
    Route::post('/customer/my-order/{orderId}/buy-again', [OrdersController::class, 'buyAgain']);
    
    Route::post('/customer/return-invoice', [App\Http\Controllers\SellReturnController::class, 'storeEcomB2B']);

    Route::post('/customer/update-address', [CustomerAuthController::class,'updateAddress']);

    Route::get('/customer/saved-addresses', [CustomerAuthController::class, 'savedAddresses']);
    Route::post('/customer/create-saved-address', [CustomerAuthController::class, 'createSavedAddress']);
    Route::get('/customer/get-saved-address/{id}', [CustomerAuthController::class, 'getSavedAddress']);
    Route::put('/customer/update-saved-address/{id}', [CustomerAuthController::class, 'updateSavedAddress']);
    
    // Tobacco license upload from profile (/profile/account-info)
    Route::post('/customer/tobacco-license', [CustomerAuthController::class, 'updateTobaccoLicense']);

    // Delivery Preferences
    Route::get('/customer/address/delivery-preferences', [CustomerAuthController::class, 'getDeliveryPreferences']);
    Route::post('/customer/address/delivery-preferences', [CustomerAuthController::class, 'storeDeliveryPreferences']);
    Route::put('/customer/address/delivery-preferences', [CustomerAuthController::class, 'updateDeliveryPreferences']);
    Route::delete('/customer/delete-saved-address/{id}', [CustomerAuthController::class, 'deleteSavedAddress']);
    Route::post('/customer/set-default-address/{id}', [CustomerAuthController::class, 'setDefaultAddress']);

    Route::put('/customer/update', [CustomerAuthController::class, 'updateCustomer']);

    // Customer payment methods (credit/debit cards)
    Route::get('/customer/payment-methods', [CustomerPaymentMethodController::class, 'index']);
    Route::post('/customer/payment-methods', [CustomerPaymentMethodController::class, 'store']);

    Route::post('/credit-applications', [CreditLineController::class, 'storeCreditApplication']);
    Route::post('/credit-application', [CreditLineController::class, 'storeComprehensiveCreditApplication']);
    Route::get('/credit-applications', [CreditLineController::class, 'getcustomerCreditApplications']);

    //cart api 
    Route::get('/cart', [CartController::class, 'getCart']);
    Route::put('/cart/gift-options', [CartController::class, 'updateGiftOptions']);
    Route::post('/cart', [CartController::class, 'bulkCartAddOrUpdate']);
    Route::delete('/cart', [CartController::class, 'deleteItem']);
    Route::delete('/cart/empty',[CartController::class,'emptyCartItems']);
    Route::post('/cart/reduce',[CartController::class,'reduceQty']);
    Route::get('/cart/download-pdf', [CartController::class, 'downloadCartPdf']);
    Route::delete('/cart/restricted/{itemId?}', [CartController::class, 'removeFromCustomerCartByState']);

    //address freeze
    Route::get('/checkout-address',[CartController::class,'getAddress']);
    Route::post('/checkout-address',[CartController::class,'address']);
    // Route::post('/checkout-update-address',[CartController::class,'updateAddress']);

    //apply discount
    Route::post('/cart/apply-discount',[CartController::class,'applyDiscount'])->middleware('throttle:throttle5pm');
    Route::post('/cart/remove-discount',[CartController::class,'removeDiscount']);
    Route::post('/cart/apply-gift-card',[CartController::class,'applyGiftCard'])->middleware('throttle:throttle5pm');
    Route::post('/cart/remove-gift-card',[CartController::class,'removeGiftCard']);
    Route::delete('/cart/gift-card/{id}',[CartController::class,'deleteGiftCardFromCart']);
    Route::get('/get-pay-contact-due', [TransactionPaymentController::class, 'getPayContactDueB2B']);
    Route::post('/pay-contact-due', [TransactionPaymentController::class, 'payContactDueB2B']);
    Route::post('/process-order-payment', [TransactionPaymentController::class, 'processOrderPayment']);

    // live shipping rates (B2B cart -> ShipStation)
    // POST: used when frontend wants to send an explicit payload
    // GET:  used by checkout page to simply read current cart options
    Route::match(['get', 'post'], '/cart/shipping-rates', [CartController::class, 'getShippingRates']);

    // Referral Program APIs
    Route::get('/referral/settings', [ReferralProgramController::class, 'getReferralProgramSettings']);
    Route::post('/referral/generate-code', [ReferralProgramController::class, 'generateReferralCode']);
    Route::get('/referral/my-code', [ReferralProgramController::class, 'getMyReferralCode']);
    Route::post('/referral/apply-code', [ReferralProgramController::class, 'applyReferralCode']);
    Route::get('/referral/stats', [ReferralProgramController::class, 'getMyReferralStats']);
    Route::post('/referral/validate-code', [ReferralProgramController::class, 'validateReferralCode']);

    // Reward points APIs (authenticated customer)
    Route::get('/customer/reward-points', [RewardPointsController::class, 'index']);
    Route::get('/customer/reward-points/rules', [RewardPointsController::class, 'rules']);
    Route::get('/customer/reward-points/history', [RewardPointsController::class, 'history']);
    Route::get('/customer/reward-points/redeem-preview', [RewardPointsController::class, 'redeemPreview']);
    Route::post('/customer/reward-points/apply', [RewardPointsController::class, 'apply']);

    // Gift Card APIs (Amazon-style flow: purchase -> redeem to wallet)
    Route::get('/gift-cards', [GiftCardController::class, 'index']);
    Route::get('/gift-cards/redeemed', [GiftCardController::class, 'redeemed']);
    Route::get('/gift-cards/{id}', [GiftCardController::class, 'show']);
    Route::post('/gift-cards/purchase', [GiftCardController::class, 'purchase']);
    Route::post('/gift-cards/redeem', [GiftCardController::class, 'redeem']);

    // Customer discount APIs
    Route::get('/customer/discounts', [App\Http\Controllers\ECOM\CustomerDiscountController::class, 'getRunningDiscounts']);

    // Search history – get list, edit one, delete one, delete all (all require auth)
    Route::get('/customer/search-history', [CatalogController::class, 'getSearchHistory']);
    Route::put('/customer/search-history/{id}', [CatalogController::class, 'updateSearchHistoryItem']);
    Route::delete('/customer/search-history/{id}', [CatalogController::class, 'deleteSearchHistoryItem']);
    Route::delete('/search/history', [CatalogController::class, 'deleteAllSearchHistory']);
    Route::delete('/search/history/{id}', [CatalogController::class, 'deleteSearchHistoryItem']);

    // Customer Review APIs
    Route::get('/customer/reviews', [CustomerReviewController::class, 'getCustomerReviews']);
    Route::post('/customer/reviews', [CustomerReviewController::class, 'store']);
    Route::get('/customer/reviews/{id}', [CustomerReviewController::class, 'show']);
    Route::put('/customer/reviews/{id}', [CustomerReviewController::class, 'update']);
    Route::delete('/customer/reviews/{id}', [CustomerReviewController::class, 'destroy']);
    Route::post('/customer/reviews/{id}/like', [CustomerReviewController::class, 'toggleLike']);

    // Customer Complaint APIs
    Route::get('/customer/complaints', [ComplaintController::class, 'apiIndex']);
    Route::post('/customer/complaints', [ComplaintController::class, 'apiStore']);
    Route::get('/customer/complaints/{id}', [ComplaintController::class, 'apiShow']);
    Route::put('/customer/complaints/{id}', [ComplaintController::class, 'apiUpdate']);

    // Customer Business Identification APIs
    Route::get('/customer/business-identifications', [App\Http\Controllers\BusinessIdentificationController::class, 'apiIndex']);
    Route::post('/customer/business-identifications', [App\Http\Controllers\BusinessIdentificationController::class, 'apiStore']);
    Route::get('/customer/business-identifications/{id}', [App\Http\Controllers\BusinessIdentificationController::class, 'apiShow']);
    Route::put('/customer/business-identifications/{id}', [App\Http\Controllers\BusinessIdentificationController::class, 'apiUpdate']);
        
});
// this is out of the ecom customer validate middleware bcz of double token of payments (middleware buggy issue fixes)
Route::post('process-order',[PaymentOrderController::class,'processOrder']);
Route::post('/payment-of-sales-order', [PaymentOrderController::class, 'paymentOfSalesOrder']);

// merchant application api
Route::post('/merchant-application', [MerchantApplicationController::class, 'merchantApplicationApi']);

// B2B guest session & guest cart management (no auth required, but rate-limited)
Route::middleware(['throttle:throttle5pm'])->group(function () {
    // Guest session lifecycle
    Route::post('/guest/session', [B2bGuestController::class, 'createSession']);
    Route::get('/guest/session/validate', [B2bGuestController::class, 'validateSession']);
    Route::post('/guest/session/extend', [B2bGuestController::class, 'extendSession']);
    Route::post('/guest/convert-to-user', [B2bGuestController::class, 'convertToUser']);

});
 // Guest cart (requires guest_session via strict middleware)
// NOTE: B2B guest carts now rely on the same /cart endpoints after login via guest_session merge,
// so separate /guest/cart routes are no longer defined here.
// ecom api
Route::get('/category-product/{slugs}',[CatalogController::class,'multiCategory']);
Route::get('/options', [OptionController::class, 'getOptionsForFrontend']);
Route::get('/multi-channel', [MultichannelController::class, 'apiIndex']);
Route::put('/multi-channel/{id}', [MultichannelController::class, 'apiUpdate']);
Route::get('/coa', [\App\Http\Controllers\CoaController::class, 'apiIndex']);
Route::get('/coa/{id}', [\App\Http\Controllers\CoaController::class, 'apiShow']);
Route::get('/multi-category/{slugs}',[CatalogController::class,'multiCategory']);
Route::get('/shop',[CatalogController::class,'shopProducts']);
Route::get('/brand-product/{slug}',[CatalogController::class,'brandProducts']);
Route::get('/products/one-per-brand',[CatalogController::class,'productsOnePerBrand']);
Route::get('/search',[CatalogController::class,'searchProducts']);
Route::get('/all-product',[CatalogController::class,'allProducts']);
Route::get('/brand-category-menu',[CatalogController::class,'sideMenu']);
Route::get('/brand-category-menu-cat2brand',[CatalogController::class,'sideMenucat2brand']);
Route::get('/brand-list',[CatalogController::class,'brandList']);
Route::get('/preferred-brands', [CatalogController::class, 'preferredBrands']);
Route::get('/preferred-brands/by-policy-type', [CatalogController::class, 'getAllBrandsByPolicyType']);
Route::post('/preferred-brands', [CatalogController::class, 'storePreferredBrands'])->middleware('ecom.customer.validate');
Route::delete('/preferred-brands/{brandId}', [CatalogController::class, 'deletePreferredBrand'])->middleware('ecom.customer.validate');
// Admin endpoints for status management
Route::get('/preferred-brands/admin/all', [CatalogController::class, 'getAllPreferredBrands'])->middleware('ecom.customer.validate');
Route::get('/restricted-brands/admin/all', [CatalogController::class, 'getAllRestrictedBrands'])->middleware('ecom.customer.validate');
Route::put('/preferred-brands/admin/{id}/status', [CatalogController::class, 'updatePreferredBrandStatus'])->middleware('ecom.customer.validate');
Route::get('/preferred-categories', [CatalogController::class, 'preferredCategories']);
Route::get('/preferred-categories/by-policy-type', [CatalogController::class, 'getAllCategoriesByPolicyType']);
Route::post('/preferred-categories', [CatalogController::class, 'storePreferredCategories'])->middleware('ecom.customer.validate');
Route::delete('/preferred-categories/{categoryId}', [CatalogController::class, 'deletePreferredCategory'])->middleware('ecom.customer.validate');
// Admin endpoints for category status management
Route::get('/preferred-categories/admin/all', [CatalogController::class, 'getAllPreferredCategories'])->middleware('ecom.customer.validate');
Route::get('/restricted-categories/admin/all', [CatalogController::class, 'getAllRestrictedCategories'])->middleware('ecom.customer.validate');
Route::put('/preferred-categories/admin/{id}/status', [CatalogController::class, 'updatePreferredCategoryStatus'])->middleware('ecom.customer.validate');
Route::get('/product/{slug}',[CatalogController::class,'singleProduct']);
Route::get('/gift-cards', [CatalogController::class, 'getGiftCards']);
Route::post('/gift-cards/purchase', [CartController::class, 'purchaseGiftCard'])->middleware('ecom.customer.validate');
Route::get('/product-list/{slug}',[CatalogController::class,'productList']);    

Route::get('/product/{productId}/reviews', [CustomerReviewController::class, 'getProductReviews']);
Route::post('/product/{productId}/reviews', [CustomerReviewController::class, 'createProductReview']);
Route::post('/reviews/{id}/like', [CustomerReviewController::class, 'toggleLike']);

Route::get('/discounts', [App\Http\Controllers\ECOM\CustomerDiscountController::class, 'getPublicDiscounts']);
Route::get('/discounts/active', [App\Http\Controllers\ECOM\CustomerDiscountController::class, 'getActiveDiscountsB2b']);

Route::post('/test-email', [BusinessController::class, 'testEmailConfiguration']);
//temp route
// Route::get('sync-cat',[CatalogController::class,'syncCategories']);
Route::get('sync-user',[CatalogController::class,'syncUser']);
// Route::get('sync/{id}',[CatalogController::class,'storeProduct']);





//staff APIs
// For Staff Authentication Routes
Route::prefix('staff')->group(function () {
    //public
    Route::post('/login', [StaffAuthController::class, 'login']);
  
    //private
    Route::middleware(['user.access'])->group(function () {
        Route::get('/profile', [StaffAuthController::class, 'profile']);
        Route::post('/logging-active/{status}', [StaffAuthController::class, 'loggingActive']);
        Route::post('/currunt-status', [StaffAuthController::class, 'curruntStatus']);
        Route::post('/update-fcm-token', [StaffAuthController::class, 'updateFcmToken']);
        
        // Shift Management
        Route::post('/start-shift', [StaffAuthController::class, 'startShift']);
        Route::post('/end-shift', [StaffAuthController::class, 'endShift']);
        
        // Dashboard APIs
        Route::get('/dashboard/today-activities', [StaffAuthController::class, 'getTodayActivities']);
        Route::get('/dashboard/stats', [StaffAuthController::class, 'getDashboardStats']);
        Route::get('/picking-orders', [OrderfulfillmentController::class, 'pickerManOrder']);
        Route::get('/picking-order/{id}', [SellController::class, 'manualPick']);
        Route::post('/picking-order/item', [SellController::class, 'manualPickStore']);        
        Route::post('/picking-order/revert', [OrderfulfillmentController::class, 'revert']);     
        Route::post('/picking-order/reset', [OrderfulfillmentController::class, 'reset']);   

        Route::post('/picking-order/status', [OrderfulfillmentController::class, 'updatePickingStatus']);
        Route::post('/picking-order/verify', [OrderfulfillmentController::class, 'verifyPicking']); // self start verify
        Route::post('/picking-order/start-time', [OrderfulfillmentController::class, 'storeStartTime']); // self start 
        Route::post('/picking-order/end-time', [OrderfulfillmentController::class, 'storeEndTime']); // self start

        // Order tracking status (for staff/admin to update)
        Route::post('/order/{orderId}/tracking', [OrdersController::class, 'updateOrderTracking']);

        Route::get('product-lookup',[CatalogController::class,'productLookup']);

        // Preferred brands (create/update/delete) — staff only
        Route::post('preferred-brands', [CatalogController::class, 'storePreferredBrands']);
        Route::delete('preferred-brands/{brandId}', [CatalogController::class, 'deletePreferredBrand']);
        // Preferred brands admin (status management) — staff only
        Route::get('preferred-brands/admin/all', [CatalogController::class, 'getAllPreferredBrands']);
        Route::get('restricted-brands/admin/all', [CatalogController::class, 'getAllRestrictedBrands']);
        Route::put('preferred-brands/admin/{id}/status', [CatalogController::class, 'updatePreferredBrandStatus']);
        // Preferred categories (create/update/delete) — staff only
        Route::post('preferred-categories', [CatalogController::class, 'storePreferredCategories']);
        Route::delete('preferred-categories/{categoryId}', [CatalogController::class, 'deletePreferredCategory']);
        // Preferred categories admin (status management) — staff only
        Route::get('preferred-categories/admin/all', [CatalogController::class, 'getAllPreferredCategories']);
        Route::get('restricted-categories/admin/all', [CatalogController::class, 'getAllRestrictedCategories']);
        Route::put('preferred-categories/admin/{id}/status', [CatalogController::class, 'updatePreferredCategoryStatus']);

        //Staff Commission Agent APIs
        Route::post('/create-customer', [CommissionAgentController::class, 'createCustomer']);
        Route::get('/get-customers', [CommissionAgentController::class,'getCustomer']);
        Route::get('/view-customer/{id}', [CommissionAgentController::class,'getCustomerById']);
        Route::get('/get-customer-due/{id}', [CommissionAgentController::class,'getCustomerDue']);

          //product create update and delete routes
        Route::post('/product/create', [ProductController::class, 'apiStore']);
        Route::get('/product/{id}', [ProductController::class, 'apiShow']);
        Route::post('/product/update/{id}', [ProductController::class, 'apiUpdate']);


        Route::get('/get-sell-orders', [CommissionAgentController::class,'getSellOrder']);
        Route::get('/view-sell-order/{id}', [CommissionAgentController::class,'getSellOrderById']);
        Route::post('/create-sell', [CommissionAgentController::class,'createSell']);
        Route::get('/store-leads', [CommissionAgentController::class,'storeLeads']);
        Route::post('/create-leads', [CommissionAgentController::class,'createLeads']);
        Route::get('/store-lead/{id}', [CommissionAgentController::class,'storeLeadById']);
        // Ticket routes
        Route::get('/tickets/{lead_id}', [CommissionAgentController::class,'getTickets']);
        Route::post('/create-ticket/{lead_id}', [CommissionAgentController::class,'createTicket']);
        Route::get('/get-ticket/{id}', [CommissionAgentController::class,'getTicketById']);
        Route::get('/get-ticket-activities/{id}', [CommissionAgentController::class,'getTicketActivities']);
        Route::post('/add-message-to-ticket/{id}', [CommissionAgentController::class,'addMessageToTicket']);
        Route::post('/update-ticket-status/{id}', [CommissionAgentController::class,'updateTicketStatus']);
        
        // Admin-only ticket routes
        Route::get('/admin/tickets/all', [CommissionAgentController::class,'getAllTickets']);
        Route::post('/admin/close-ticket/{id}', [CommissionAgentController::class,'closeTicket']);

        Route::get('/get-sell-invoice', [CommissionAgentController::class,'getSellsInvoice']);
        Route::get('/view-sell-invoice/{id}', [CommissionAgentController::class,'getSellsInvoiceById']);

        // product routes
        Route::get('/get-products', [CommissionAgentController::class, 'getProducts']);
        Route::get('/get_product_row/{variation_id}/{location_id}', [CommissionAgentController::class, 'getProductRow']);
        Route::get('/list-tax-rates', [CommissionAgentController::class, 'listTaxRates']);

        Route::prefix('leads')->group(function () {
            Route::get('/', [\App\Http\Controllers\API\LeadApiController::class, 'index']); // Get all leads with filters
            Route::post('/', [\App\Http\Controllers\API\LeadApiController::class, 'store']); // Create new lead
            Route::get('/{id}', [\App\Http\Controllers\API\LeadApiController::class, 'show']); // Get single lead
            Route::put('/{id}', [\App\Http\Controllers\API\LeadApiController::class, 'update']); // Update lead
            Route::delete('/{id}', [\App\Http\Controllers\API\LeadApiController::class, 'destroy']); // Delete lead
            Route::get('/{id}/visit-history', [\App\Http\Controllers\API\LeadApiController::class, 'visitHistory']); // Get visit history
        });

        // All Visits API - Get recent visits with lead data
        Route::get('/visits/all', [\App\Http\Controllers\API\LeadApiController::class, 'getAllRecentVisits']); // Get all recent visits

        // Sales Rep Statistics
        Route::get('/sales-rep-activities', [\App\Http\Controllers\API\LeadApiController::class, 'salesRepActivities']); // Get total activities by sales rep

        // Test Geocoding Endpoint (for testing purposes)
        Route::post('/test-geocode', [\App\Http\Controllers\API\LeadApiController::class, 'testGeocode']);
        
        // Get coordinates from Google Places place_id
        Route::post('/geocode-place-id', [\App\Http\Controllers\API\LeadApiController::class, 'geocodeFromPlaceId']);

        // Map & Location APIs
        Route::prefix('map')->group(function () {
            // Nearby Leads Search
            Route::get('/nearby-leads', [\App\Http\Controllers\API\MapApiController::class, 'getNearbyLeads']); // Find nearby leads within radius
            
            // Quick Add Nearby Lead (discovered in field)
            Route::post('/add-nearby-lead', [\App\Http\Controllers\API\MapApiController::class, 'addNearbyLead']); // Quick add discovered lead
            
            // Visit Tracking
            Route::get('/visits', [\App\Http\Controllers\API\MapApiController::class, 'getVisits']); // Get visit history
            Route::post('/create-visit', [\App\Http\Controllers\API\MapApiController::class, 'createVisit']); // Start a visit
            Route::post('/complete-visit/{id}', [\App\Http\Controllers\API\MapApiController::class, 'completeVisit']); // Complete visit with proof
            
            // Location Tracking
            Route::post('/update-location', [\App\Http\Controllers\API\MapApiController::class, 'updateCurrentLocation']); // Update sales rep GPS location
            Route::get('/sales-rep-locations', [\App\Http\Controllers\API\MapApiController::class, 'getSalesRepLocations']); // Get all sales rep locations (Admin)
        });    
        // lock logic
        Route::get('/session-lock/{modelType}/{modelId}', [OrderfulfillmentController::class, 'checkModalAccess']);
        Route::get('/session-ping/{modelType}/{modelId}', [OrderfulfillmentController::class, 'pingModal']);
        Route::post('/session-release/{modelType}/{modelId}', [OrderfulfillmentController::class, 'releaseModal']);
        // Route::post('/verifying-order/{id}', [OrderfulfillmentController::class, 'listOrderDetails']);
        // Route::get('/dashboard', [StaffAuthController::class, 'dashboard']);
        Route::get('/logout', [StaffAuthController::class, 'logout']);
    });
});


// B2C ECOM Routes with Location-based Structure
// Format: /api/{location_id}/{brand_name}/...

// Public B2C routes (no authentication required)
Route::prefix('{location_id}/{brand_name}')->middleware(['ecom.location.validate'])->group(function () {
    
    // Public catalog routes
    Route::get('/shop', [B2cCatalogController::class, 'shopProducts']);
    Route::get('/all-product', [B2cCatalogController::class, 'allProducts']);
    Route::get('/search', [B2cCatalogController::class, 'searchProducts']);
    Route::get('/product/{slug}', [B2cCatalogController::class, 'singleProduct']);
    Route::get('/product-list/{slug}', [B2cCatalogController::class, 'productList']);
    Route::get('/category-product/{slugs}', [B2cCatalogController::class, 'multiCategory']);
    Route::get('/multi-category/{slugs}', [B2cCatalogController::class, 'multiCategory']);
    Route::get('/brand-product/{slug}', [B2cCatalogController::class, 'brandProducts']);
    Route::get('/brand-category-menu', [B2cCatalogController::class, 'sideMenu']);
    Route::get('/brand-list', [B2cCatalogController::class, 'brandList']);
    Route::get('/category-list', [B2cCatalogController::class,'allCategories']);
    Route::get('/customer/my-order-print/{orderId}', [OrdersController::class, 'printInvoice']);
    // Public discount API
    Route::get('/discounts', [App\Http\Controllers\ECOM\CustomerDiscountController::class, 'getPublicDiscounts']);
    
    // Customer authentication routes (public)
    Route::middleware(['throttle:throttle5pm'])->group(function () {
        Route::post('/customer/login', [B2cCustomerAuthController::class, 'login']);
        Route::post('/customer/register', [B2cCustomerAuthController::class, 'register']);
        Route::post('/customer/email-confirmation', [B2cCustomerAuthController::class, 'emailConfirmation']);
        Route::post('/customer/forgot-password', [B2cCustomerAuthController::class, 'sendResetLinkEmail']);
        Route::post('/customer/set-password', [CustomerAuthController::class, 'reset']);
        Route::post('/customer/reset-password-with-otp', [CustomerAuthController::class, 'resetPasswordWithOtp']);
        Route::post('/customer/contact-us', [B2cCustomerAuthController::class, 'contactus']);
        Route::post('/customer/subscribe', [B2cCustomerAuthController::class, 'subscribe']);
        Route::post('/customer/refresh-token', [CustomerAuthController::class, 'refreshToken']);
    });
    
    // Process order (outside customer validation due to payment middleware issues)
    Route::post('/process-order', [PaymentOrderController::class, 'processOrder']);
    
    Route::get('/customer/my-order/{orderId}', [B2cOrdersController::class, 'getOrderDetails']);
});

// Authenticated B2C routes (Location + Brand-based customer authentication)
// These routes provide complete location and brand isolation
Route::prefix('{location_id}/{brand_name}')->middleware(['ecom.unified.auth'])->group(function () {
    
    // Customer profile and account management
    Route::get('/customer/logout', [CustomerAuthController::class, 'logout']);
    Route::get('/customer/my-account', [CustomerAuthController::class, 'myAccount']);
    Route::get('/customer/my-orders', [B2cOrdersController::class, 'mySaleOrders']);
    Route::get('/customer/my-invoices', [B2cOrdersController::class, 'mySaleInvoices']);
    Route::get('/customer/my-order-print/{orderId}', [B2cOrdersController::class, 'printInvoice']);
    Route::post('/customer/return-invoice', [App\Http\Controllers\SellReturnController::class, 'storeEcom']);
    Route::post('/customer/update-address', [B2cCustomerAuthController::class, 'updateAddress']); 
    Route::get('/customer/notification', [CustomerAuthController::class, 'notification']);
    Route::get('/customer/view', [B2cCustomerAuthController::class, 'viewCustomer']);
    Route::put('/customer/update', [B2cCustomerAuthController::class, 'updateCustomer']);
    
    Route::get('/customer/saved-addresses', [B2cCustomerAuthController::class, 'savedAddresses']);
    Route::post('/customer/create-saved-address', [B2cCustomerAuthController::class, 'createSavedAddress']);
    Route::get('/customer/get-saved-address/{address_id}', [B2cCustomerAuthController::class, 'getSavedAddress']);
    Route::put('/customer/update-saved-address/{address_id}', [B2cCustomerAuthController::class, 'updateSavedAddress']);
    Route::delete('/customer/delete-saved-address/{address_id}', [B2cCustomerAuthController::class, 'deleteSavedAddress']);
    Route::post('/customer/set-default-address/{address_id}', [B2cCustomerAuthController::class, 'setDefaultAddress']);
     
    // Cart management (unified for customers and guests)
    Route::get('/cart', [UnifiedCartController::class, 'getCart']);
    Route::post('/cart', [UnifiedCartController::class, 'addToCart']);
    Route::post('/cart/reduce', [UnifiedCartController::class, 'reduceQty']);
    Route::put('/cart/{itemId}', [UnifiedCartController::class, 'updateCartItem']);
    Route::delete('/cart/{itemId}', [UnifiedCartController::class, 'removeFromCart']);
    Route::delete('/cart/restricted', [UnifiedCartController::class, 'removeFromCustomerCartByState']);
    Route::delete('/cart', [UnifiedCartController::class, 'clearCart']);
    
    // Address management
    Route::get('/checkout-address', [UnifiedCartController::class, 'getAddress']);
    Route::post('/checkout-address', [UnifiedCartController::class, 'address']);
    
    // Discount management
    Route::post('/cart/apply-discount',[UnifiedCartController::class,'applyDiscount'])->middleware('throttle:throttle5pm');
    Route::post('/cart/remove-discount',[UnifiedCartController::class,'removeDiscount']);
    
    // Customer discounts
    Route::get('/customer/discounts', [App\Http\Controllers\ECOM\CustomerDiscountController::class, 'getRunningDiscounts']);
    
    // B2C Payment processing
    Route::post('/process-order', [B2CPaymentOrderController::class, 'processOrder']);
    
    // Wishlist management
    Route::resource('wishlist', WishlistsController::class)->names('b2c.wishlist');
});

// Guest session management routes (separate from cart operations)
Route::prefix('{location_id}/{brand_name}')->middleware(['ecom.guest.validate'])->group(function () {
    // Guest session management
    Route::post('/guest/session', [GuestController::class, 'createSession']);
    Route::get('/guest/session/validate', [GuestController::class, 'validateSession']);
    Route::post('/guest/session/extend', [GuestController::class, 'extendSession']);
    Route::post('/guest/convert-to-user', [GuestController::class, 'convertToUser']);
});




// UNIFIED B2C API Routes moved to routes/b2c-unified.php
// Registered in RouteServiceProvider at root level: /b2c-api/{location_id}/...








// Stock Alert API Routes
Route::post('/stock-alerts/request', [\App\Http\Controllers\StockAlertController::class, 'requestAlert']);
Route::get('/stock-alerts/unsubscribe', [\App\Http\Controllers\StockAlertController::class, 'unsubscribe']);

Route::get('/export-product/{productId}', [ProductController::class, 'exportProductByHttp']);
Route::get('/import-product/{productId}', [ProductController::class, 'storeFromDifferentProjectByHttp']);

// Firebase Push Notification API Routes
Route::prefix('firebase')->group(function () {
    Route::post('/store-push-notification-token', [FirebaseController::class, 'storePushNotificationToken'])->middleware('auth:api');
    Route::post('/send-test-notification', [FirebaseController::class, 'sendTestNotification'])->middleware('auth:api');
    Route::post('/test-simple', [FirebaseController::class, 'sendTestNotification']); // Simple test without auth
});

Route::middleware('auth:api')->prefix('notifications')->group(function () {
    Route::get('/all-notifications', [FirebaseController::class, 'getAllNotifications']);
    Route::patch('/mark-read/{notificationId}', [FirebaseController::class, 'markAsRead']);
    Route::delete('/delete-notification/{notificationId}', [FirebaseController::class, 'deleteNotification']);
    
    // Sent Messages Routes
    Route::get('/messages', [FirebaseController::class, 'getMessages']);
    Route::get('/messages/unread', [FirebaseController::class, 'getUnreadMessages']);
    Route::get('/messages/read', [FirebaseController::class, 'getReadMessages']);
    Route::get('/messages/urgent', [FirebaseController::class, 'getUrgentMessages']);
    Route::get('/messages/non-urgent', [FirebaseController::class, 'getNonUrgentMessages']);
    Route::get('/messages/deleted', [FirebaseController::class, 'getDeletedMessages']);
    Route::patch('/messages/{messageId}/read', [FirebaseController::class, 'markMessageAsRead']);
    Route::patch('/messages/{messageId}/unread', [FirebaseController::class, 'markMessageAsUnread']);
    Route::delete('/messages/{messageId}', [FirebaseController::class, 'softDeleteMessage']);
    Route::patch('/messages/{messageId}/restore', [FirebaseController::class, 'restoreMessage']);
});

Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found.',
        'status' => false,
    ]);
});