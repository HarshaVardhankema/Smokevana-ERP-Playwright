<?php

use App\Http\Controllers\VendorPortal\VendorAuthController;
use App\Http\Controllers\VendorPortal\VendorPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Vendor Portal Routes
|--------------------------------------------------------------------------
|
| Routes for the Vendor Portal - A separate interface for vendors to
| manage their products, fulfill orders, track earnings, etc.
|
| All routes here are prefixed with 'vendor-portal' or accessed via
| the /vendorlogin entry point.
|
*/

// Guest routes (for vendor login)
Route::group(['middleware' => ['web']], function () {
    // Redirect /vendor-portal to site root for guests, or dashboard if logged in
    Route::get('/vendor-portal', function () {
        if (session('vendor_portal.vendor_id')) {
            return redirect()->route('vendor.dashboard');
        }
        return redirect('/');
    })->name('vendor.portal.root');
    
    // Vendor Login Page (separate URL)
    Route::get('/vendorlogin', [VendorAuthController::class, 'showLoginForm'])
        ->name('vendor.login');
    
    // Alternative vendor-portal login URL
    Route::get('/vendor-portal/login', [VendorAuthController::class, 'showLoginForm'])
        ->name('vendor.portal.login');
    
    // Vendor Login Submit
    Route::post('/vendorlogin', [VendorAuthController::class, 'login'])
        ->name('vendor.login.submit');
    
    Route::post('/vendor-portal/login', [VendorAuthController::class, 'login']);
    
    // Vendor Logout
    Route::post('/vendor-portal/logout', [VendorAuthController::class, 'logout'])
        ->name('vendor.logout');
});

// Protected Vendor Portal Routes
Route::group([
    'prefix' => 'vendor-portal',
    'middleware' => ['web', 'vendor.auth']
], function () {
    
    // Dashboard
    Route::get('/', [VendorPortalController::class, 'dashboard'])
        ->name('vendor.dashboard');
    
    Route::get('/dashboard', [VendorPortalController::class, 'dashboard'])
        ->name('vendor.dashboard.index');
    
    // Orders
    Route::get('/orders', [VendorPortalController::class, 'orders'])
        ->name('vendor.orders');
    
    Route::get('/orders/{id}', [VendorPortalController::class, 'showOrder'])
        ->name('vendor.orders.show');
    
    Route::post('/orders/{id}/accept', [VendorPortalController::class, 'acceptOrder'])
        ->name('vendor.orders.accept');
    
    Route::post('/orders/{id}/processing', [VendorPortalController::class, 'markProcessing'])
        ->name('vendor.orders.processing');
    
    Route::post('/orders/{id}/ship', [VendorPortalController::class, 'shipOrder'])
        ->name('vendor.orders.ship');
    
    Route::post('/orders/{id}/complete', [VendorPortalController::class, 'completeOrder'])
        ->name('vendor.orders.complete');
    
    Route::get('/orders/{id}/packing-slip', [VendorPortalController::class, 'packingSlip'])
        ->name('vendor.orders.packing-slip');
    
    // Products
    Route::get('/products', [VendorPortalController::class, 'products'])
        ->name('vendor.products');
    
    Route::post('/products/{id}/stock', [VendorPortalController::class, 'updateStock'])
        ->name('vendor.products.stock');
    
    // Bulk stock update (product-level)
    Route::post('/products/bulk-stock', [VendorPortalController::class, 'bulkUpdateStock'])
        ->name('vendor.products.bulk-stock');
    
    // Variation-level updates (for variant products)
    Route::post('/variations/{id}/update', [VendorPortalController::class, 'updateVariation'])
        ->name('vendor.variations.update');
    
    Route::post('/variations/bulk-update', [VendorPortalController::class, 'bulkUpdateVariations'])
        ->name('vendor.variations.bulk-update');
    
    // Earnings
    Route::get('/earnings', [VendorPortalController::class, 'earnings'])
        ->name('vendor.earnings');
    
    // Product Requests
    Route::get('/product-requests', [VendorPortalController::class, 'productRequests'])
        ->name('vendor.product-requests');
    
    Route::get('/product-requests/create', [VendorPortalController::class, 'createProductRequest'])
        ->name('vendor.product-requests.create');
    
    Route::get('/product-requests/catalog', [VendorPortalController::class, 'getProductCatalog'])
        ->name('vendor.product-requests.catalog');
    
    Route::post('/product-requests/submit-existing', [VendorPortalController::class, 'submitExistingProductRequest'])
        ->name('vendor.product-requests.submit-existing');
    
    Route::post('/product-requests/submit-new', [VendorPortalController::class, 'submitNewProductRequest'])
        ->name('vendor.product-requests.submit-new');
    
    Route::get('/product-requests/{id}/view', [VendorPortalController::class, 'showProductRequest'])
        ->name('vendor.product-requests.show');
    
    Route::put('/product-requests/{id}', [VendorPortalController::class, 'updateProductRequest'])
        ->name('vendor.product-requests.update');
    
    Route::delete('/product-requests/{id}', [VendorPortalController::class, 'deleteProductRequest'])
        ->name('vendor.product-requests.delete');
    
    // Purchase Orders
    Route::get('/purchase-orders', [VendorPortalController::class, 'purchaseOrders'])
        ->name('vendor.purchase-orders');
    
    Route::get('/purchase-orders/create', [VendorPortalController::class, 'createVendorPurchaseOrder'])
        ->name('vendor.purchase-orders.create');
    
    Route::get('/purchase-orders/inventory-products', [VendorPortalController::class, 'getVendorInventoryProducts'])
        ->name('vendor.purchase-orders.inventory-products');
    
    Route::post('/purchase-orders/store', [VendorPortalController::class, 'storeVendorPurchaseOrder'])
        ->name('vendor.purchase-orders.store');
    
    Route::get('/purchase-orders/{id}', [VendorPortalController::class, 'showPurchaseOrder'])
        ->name('vendor.purchase-orders.show');
    
    // Purchase Receipts
    Route::get('/purchase-receipts', [VendorPortalController::class, 'purchaseReceipts'])
        ->name('vendor.purchase-receipts');
    
    Route::get('/purchase-receipts/create', [VendorPortalController::class, 'createPurchaseReceipt'])
        ->name('vendor.purchase-receipts.create');
    
    Route::get('/purchase-receipts/inventory-products', [VendorPortalController::class, 'getVendorInventoryProducts'])
        ->name('vendor.purchase-receipts.inventory-products');
    
    Route::post('/purchase-receipts/store', [VendorPortalController::class, 'storePurchaseReceipt'])
        ->name('vendor.purchase-receipts.store');
    
    Route::get('/purchase-receipts/{id}', [VendorPortalController::class, 'showPurchaseReceipt'])
        ->name('vendor.purchase-receipts.show');
    
    // Profile
    Route::get('/profile', [VendorPortalController::class, 'profile'])
        ->name('vendor.profile');
    
    Route::post('/profile', [VendorPortalController::class, 'updateProfile'])
        ->name('vendor.profile.update');
    
    Route::post('/profile/password', [VendorPortalController::class, 'changePassword'])
        ->name('vendor.profile.password');
});
