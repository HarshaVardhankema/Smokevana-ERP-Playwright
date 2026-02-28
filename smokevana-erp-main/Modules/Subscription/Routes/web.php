<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Subscription Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your subscription module.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

Route::middleware(['web', 'SetSessionData', 'auth', 'language', 'timezone', 'AdminSidebarMenu'])
    ->prefix('subscription')
    ->name('subscription.')
    ->group(function () {
        
        // Dashboard
        Route::get('/', [Modules\Subscription\Http\Controllers\SubscriptionController::class, 'index'])
            ->name('subscriptions.index');
        
        // Subscriptions
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/data', [Modules\Subscription\Http\Controllers\SubscriptionController::class, 'getSubscriptions'])
                ->name('data');
            Route::get('/create', [Modules\Subscription\Http\Controllers\SubscriptionController::class, 'create'])
                ->name('create');
            Route::post('/', [Modules\Subscription\Http\Controllers\SubscriptionController::class, 'store'])
                ->name('store');
            Route::get('/{id}', [Modules\Subscription\Http\Controllers\SubscriptionController::class, 'show'])
                ->name('show');
            Route::get('/{id}/edit', [Modules\Subscription\Http\Controllers\SubscriptionController::class, 'edit'])
                ->name('edit');
            Route::put('/{id}', [Modules\Subscription\Http\Controllers\SubscriptionController::class, 'update'])
                ->name('update');
            Route::post('/{id}/cancel', [Modules\Subscription\Http\Controllers\SubscriptionController::class, 'cancel'])
                ->name('cancel');
            Route::post('/{id}/pause', [Modules\Subscription\Http\Controllers\SubscriptionController::class, 'pause'])
                ->name('pause');
            Route::post('/{id}/resume', [Modules\Subscription\Http\Controllers\SubscriptionController::class, 'resume'])
                ->name('resume');
            Route::post('/{id}/renew', [Modules\Subscription\Http\Controllers\SubscriptionController::class, 'renew'])
                ->name('renew');
            Route::post('/{id}/sync-group', [Modules\Subscription\Http\Controllers\SubscriptionController::class, 'syncCustomerGroup'])
                ->name('sync-group');
        });
        
        // Plans
        Route::prefix('plans')->name('plans.')->group(function () {
            Route::get('/', [Modules\Subscription\Http\Controllers\SubscriptionPlanController::class, 'index'])
                ->name('index');
            Route::get('/data', [Modules\Subscription\Http\Controllers\SubscriptionPlanController::class, 'getPlans'])
                ->name('data');
            Route::get('/create', [Modules\Subscription\Http\Controllers\SubscriptionPlanController::class, 'create'])
                ->name('create');
            Route::post('/', [Modules\Subscription\Http\Controllers\SubscriptionPlanController::class, 'store'])
                ->name('store');
            Route::get('/{id}', [Modules\Subscription\Http\Controllers\SubscriptionPlanController::class, 'show'])
                ->name('show');
            Route::get('/{id}/edit', [Modules\Subscription\Http\Controllers\SubscriptionPlanController::class, 'edit'])
                ->name('edit');
            Route::put('/{id}', [Modules\Subscription\Http\Controllers\SubscriptionPlanController::class, 'update'])
                ->name('update');
            Route::delete('/{id}', [Modules\Subscription\Http\Controllers\SubscriptionPlanController::class, 'destroy'])
                ->name('destroy');
            Route::post('/{id}/toggle-status', [Modules\Subscription\Http\Controllers\SubscriptionPlanController::class, 'toggleStatus'])
                ->name('toggle-status');
        });
        
        // Prime Products
        Route::prefix('prime-products')->name('prime-products.')->group(function () {
            Route::get('/', [Modules\Subscription\Http\Controllers\PrimeProductController::class, 'index'])
                ->name('index');
            Route::get('/data', [Modules\Subscription\Http\Controllers\PrimeProductController::class, 'getPrimeProducts'])
                ->name('data');
            Route::get('/search-products', [Modules\Subscription\Http\Controllers\PrimeProductController::class, 'searchProducts'])
                ->name('search-products');
            Route::post('/', [Modules\Subscription\Http\Controllers\PrimeProductController::class, 'store'])
                ->name('store');
            Route::get('/{id}/edit', [Modules\Subscription\Http\Controllers\PrimeProductController::class, 'edit'])
                ->name('edit');
            Route::put('/{id}', [Modules\Subscription\Http\Controllers\PrimeProductController::class, 'update'])
                ->name('update');
            Route::delete('/{id}', [Modules\Subscription\Http\Controllers\PrimeProductController::class, 'destroy'])
                ->name('destroy');
            Route::post('/bulk-add', [Modules\Subscription\Http\Controllers\PrimeProductController::class, 'bulkAdd'])
                ->name('bulk-add');
        });
        
        // Invoices
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [Modules\Subscription\Http\Controllers\SubscriptionInvoiceController::class, 'index'])
                ->name('index');
            Route::get('/data', [Modules\Subscription\Http\Controllers\SubscriptionInvoiceController::class, 'getInvoices'])
                ->name('data');
            Route::get('/{id}', [Modules\Subscription\Http\Controllers\SubscriptionInvoiceController::class, 'show'])
                ->name('show');
            Route::get('/{id}/print', [Modules\Subscription\Http\Controllers\SubscriptionInvoiceController::class, 'print'])
                ->name('print');
            Route::post('/{id}/record-payment', [Modules\Subscription\Http\Controllers\SubscriptionInvoiceController::class, 'recordPayment'])
                ->name('record-payment');
            Route::get('/create-for-subscription/{subscription_id}', [Modules\Subscription\Http\Controllers\SubscriptionInvoiceController::class, 'createForSubscription'])
                ->name('create-for-subscription');
            Route::post('/{id}/send', [Modules\Subscription\Http\Controllers\SubscriptionInvoiceController::class, 'send'])
                ->name('send');
            Route::post('/{id}/cancel', [Modules\Subscription\Http\Controllers\SubscriptionInvoiceController::class, 'cancel'])
                ->name('cancel');
        });
        
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [Modules\Subscription\Http\Controllers\SubscriptionReportController::class, 'index'])
                ->name('index');
            Route::get('/revenue', [Modules\Subscription\Http\Controllers\SubscriptionReportController::class, 'revenueReport'])
                ->name('revenue');
            Route::get('/subscriptions', [Modules\Subscription\Http\Controllers\SubscriptionReportController::class, 'subscriptionReport'])
                ->name('subscriptions');
            Route::get('/churn', [Modules\Subscription\Http\Controllers\SubscriptionReportController::class, 'churnAnalysis'])
                ->name('churn');
            Route::get('/mrr', [Modules\Subscription\Http\Controllers\SubscriptionReportController::class, 'mrrReport'])
                ->name('mrr');
            Route::get('/export/{type}', [Modules\Subscription\Http\Controllers\SubscriptionReportController::class, 'export'])
                ->name('export');
        });
        
        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [Modules\Subscription\Http\Controllers\SubscriptionSettingsController::class, 'index'])
                ->name('index');
            Route::post('/', [Modules\Subscription\Http\Controllers\SubscriptionSettingsController::class, 'update'])
                ->name('update');
        });
    });
