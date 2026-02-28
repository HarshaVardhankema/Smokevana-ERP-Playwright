<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnifiedB2c\B2cCatalogController as UnifiedB2cB2cCatalogController;

/*
|--------------------------------------------------------------------------
| Unified B2C API Routes (Location-wise, No Brand Filtration)
|--------------------------------------------------------------------------
|
| These routes are for unified B2C APIs that work location-wise without
| brand filtration. They are registered at root level: http://127.0.0.1:8000/b2c-api/2/shop
|
*/

// UNIFIED B2C API Routes (No Brand Wise) // only location wise 
Route::prefix('/{location_id}')->middleware(['ecom.b2cunified.location.validate'])->group(function () {
    // Public catalog routes
    Route::get('/shop', [UnifiedB2cB2cCatalogController::class, 'shopProducts']);
    Route::get('/brand-category-menu', [UnifiedB2cB2cCatalogController::class, 'sideMenu']);
    Route::get('/category-list', [UnifiedB2cB2cCatalogController::class,'allCategories']);
    Route::get('/multi-category/{slugs}', [UnifiedB2cB2cCatalogController::class, 'multiCategory']);
    Route::get('/category-product/{slugs}', [UnifiedB2cB2cCatalogController::class, 'multiCategory']);
    Route::get('/brand-product/{slug}', [UnifiedB2cB2cCatalogController::class, 'brandProducts']);
    Route::get('/brand-list', [UnifiedB2cB2cCatalogController::class, 'brandList']);
    Route::get('/all-product', [UnifiedB2cB2cCatalogController::class, 'allProducts']);
    Route::get('/search', [UnifiedB2cB2cCatalogController::class, 'searchProducts']);
    Route::get('/product/{slug}', [UnifiedB2cB2cCatalogController::class, 'singleProduct']);
    Route::get('/product-list/{slug}', [UnifiedB2cB2cCatalogController::class, 'productList']);


});

// Authenticated unified B2C routes (if needed in future)
// Route::prefix('b2c-api/{location_id}')->middleware(['ecom.unified.guest.validate'])->group(function () {
//     // Guest session management for unified routes
//     // Route::post('/guest/session', [GuestController::class, 'createSession']);
// });

