<?php

use Illuminate\Support\Facades\Route;

Route::post(
    '/webhook/order-created/{business_id}',
    [Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'orderCreated']
);
Route::post(
    '/webhook/order-updated/{business_id}',
    [Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'orderUpdated']
);
Route::post(
    '/webhook/order-deleted/{business_id}',
    [Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'orderDeleted']
);
Route::post(
    '/webhook/order-restored/{business_id}',
    [Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'orderRestored']
);

// General webhook endpoint for WooCommerce to ERP communication
Route::post(
    '/webhook/woocommerce/{business_id}',
    [Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'receiveWebhook']
);

Route::middleware('web', 'SetSessionData', 'auth', 'language', 'timezone', 'AdminSidebarMenu')->prefix('woocommerce')->group(function () {
    Route::get('/install', [Modules\Woocommerce\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\Woocommerce\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/update', [Modules\Woocommerce\Http\Controllers\InstallController::class, 'update']);
    Route::get('/install/uninstall', [Modules\Woocommerce\Http\Controllers\InstallController::class, 'uninstall']);

    Route::get('/', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'index']);
    Route::get('/api-settings', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'apiSettings']);
    Route::post('/update-api-settings', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'updateSettings']);
    Route::get('/sync-categories', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncCategories']);
    
    Route::get('/sync-products', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncProducts']);
    Route::get('/sync-product-from-woo-to-erp', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncProductFromWooToErp']);
    Route::get('/sync-product-quantities', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncProductQuantities']);
    Route::get('/sync-product-quantities-from-woo-to-erp', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncProductQuantitiesFromWooToErp']);
    
    Route::get('/sync-customers', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncCustomers']);
    
    Route::get('/sync-customers-from-woo-to-erp', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncCustomersFromWooToErp']);
    
    Route::get('/sync-log', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'getSyncLog']);
    
    Route::get('/sync-orders', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncOrders']);
    Route::get('/test-erp-to-woo-order-sync', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'testErpToWooOrderSync']);
    
    Route::post('/map-taxrates', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'mapTaxRates']);
    Route::get('/view-sync-log', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'viewSyncLog']);
    Route::get('/get-log-details/{id}', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'getLogDetails']);
    Route::get('/reset-categories', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'resetCategories']);
    Route::get('/reset-products', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'resetProducts']);

    Route::post('/test-connection', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'testConnection']);
    
    // WooCommerce to ERP job-based update routes
    Route::post('/process-stock-update', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'processWooCommerceStockUpdate']);
    Route::post('/process-price-update', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'processWooCommercePriceUpdate']);
    Route::post('/process-variation-update', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'processWooCommerceVariationUpdate']);
    Route::post('/process-variation-data-update', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'updateWooCommerceVariationsData']);
    
    // Category and Brand sync from WooCommerce to ERP
    Route::post('/sync-categories-from-woo', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncCategoriesFromWooToErp']);
    Route::post('/sync-brands-from-woo', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncBrandsFromWooToErp']);
    
    // Sync Products to WooCommerce page
    Route::get('/sync-to-woocommerce', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncToWooCommercePage'])->name('woocommerce.sync-to-woo-page');
    Route::post('/sync-product-to-woo/{product_id}', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncProductToWooCommerce'])->name('woocommerce.sync-product-to-woo');
    Route::post('/bulk-sync-products-to-woo', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'bulkSyncProductsToWooCommerce'])->name('woocommerce.bulk-sync-to-woo');
    Route::post('/sync-product-stock-to-woo/{product_id}', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncProductStockToWooCommerce'])->name('woocommerce.sync-stock-to-woo');
    Route::get('/products-for-woo-sync', [Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'getProductsForWooSync'])->name('woocommerce.products-for-woo-sync');
});
