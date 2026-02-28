<?php

use App\Http\Controllers\Install;
use App\Http\Controllers\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
// use App\Http\Controllers\Auth;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BackUpController;
use App\Http\Controllers\LabelsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SellPosController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\GroupTaxController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TaxonomyController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\ECOM\CartController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\DropshipDashboardController;
use App\Http\Controllers\DropshipOrderController;
use App\Http\Controllers\DropshipVendorController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SellReturnController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\ImportSalesController;
use App\Http\Controllers\ShipStationController;
use App\Http\Controllers\ShippingStationController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OpeningStockController;
use App\Http\Controllers\CustomerGroupController;
use App\Http\Controllers\InvoiceLayoutController;
use App\Http\Controllers\InvoiceSchemeController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\AccountReportsController;
use App\Http\Controllers\CustomDiscountController;
use App\Http\Controllers\ECOM\WishlistsController;
use App\Http\Controllers\ImportProductsController;
use App\Http\Controllers\LedgerDiscountController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\TypesOfServiceController;
use App\Http\Controllers\DocumentAndNoteController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\LocationTaxTypeController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\BusinessLocationController;
use App\Http\Controllers\LocationSettingsController;
use App\Http\Controllers\OrderfulfillmentController;
use App\Http\Controllers\ECOM\MultichannelController;
use App\Http\Controllers\ECOM\PaymentOrderController;
use App\Http\Controllers\SellingPriceGroupController;
use App\Http\Controllers\VariationTemplateController;
use App\Http\Controllers\ImportOpeningStockController;
use App\Http\Controllers\TransactionPaymentController;
use App\Http\Controllers\MerchantApplicationController;
use App\Http\Controllers\PurchaseRequisitionController;
use App\Http\Controllers\NotificationTemplateController;
use App\Http\Controllers\SalesCommissionAgentController;
use App\Http\Controllers\DashboardConfiguratorController;
use App\Http\Controllers\CombinedPurchaseReturnController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CreditLineController;
use App\Http\Controllers\CustomerAddressController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ReleaseNotesController;
use App\Http\Controllers\GiftCardAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

include_once 'install_r.php';

// Hidden unlock endpoint for User-Agent override (handled by middleware).
Route::post('/ua/unlock', function (Request $request) {
    return response()->noContent();
})->name('ua.unlock');

Route::middleware(['setData'])->group(function () {
    // Root URL redirects based on user agent and authentication status
    Route::get('/', function () {
        $allowedAgent = 'Trivida-Labs-Corporation';


        // Check if user agent is Trivida-Labs-Corporation
        if (request()->header('User-Agent') === $allowedAgent) {
            // Redirect to /home if authenticated, otherwise /login
            if (auth()->check()) {
                return redirect('/home');
            }
            return redirect('/login');
        }

        // Default web entry (non-vendor)
        return redirect('/login');
    });

    Auth::routes();

    Route::get('/business/register', [BusinessController::class, 'getRegister'])->name('business.getRegister');
    Route::post('/business/register', [BusinessController::class, 'postRegister'])->name('business.postRegister');
    Route::post('/business/register/check-username', [BusinessController::class, 'postCheckUsername'])->name('business.postCheckUsername');
    Route::post('/business/register/check-email', [BusinessController::class, 'postCheckEmail'])->name('business.postCheckEmail');
    Route::post('/business/save-css-settings', [BusinessController::class, 'saveCssSettings']);
    Route::get('/invoice/{token}', [SellPosController::class, 'showInvoice'])
        ->name('show_invoice');
    Route::get('/quote/{token}', [SellPosController::class, 'showInvoice'])
        ->name('show_quote');

    Route::get('/pay/{token}', [SellPosController::class, 'invoicePayment'])
        ->name('invoice_payment');
    Route::post('/confirm-payment/{id}', [SellPosController::class, 'confirmPayment'])
        ->name('confirm_payment');
});
// step 1 : send notification 
Route::get('/some-xyz-notification', [OrderfulfillmentController::class, 'someXyzNotification']);
Route::get('/mark-as-readed/{id}', [OrderfulfillmentController::class, 'markAsReaded']);
//Routes for authenticated users only
Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu', 'CheckUserLogin'])->group(function () {
    Route::get('pos/payment/{id}', [SellPosController::class, 'edit'])->name('edit-pos-payment');
    Route::get('service-staff-availability', [SellPosController::class, 'showServiceStaffAvailibility']);
    Route::get('pause-resume-service-staff-timer/{user_id}', [SellPosController::class, 'pauseResumeServiceStaffTimer']);
    Route::get('mark-as-available/{user_id}', [SellPosController::class, 'markAsAvailable']);

    Route::resource('purchase-requisition', PurchaseRequisitionController::class)->except(['edit', 'update']);
    Route::post('/get-requisition-products', [PurchaseRequisitionController::class, 'getRequisitionProducts'])->name('get-requisition-products');
    Route::get('get-purchase-requisitions/{location_id}', [PurchaseRequisitionController::class, 'getPurchaseRequisitions']);
    Route::get('get-purchase-requisition-lines/{purchase_requisition_id}', [PurchaseRequisitionController::class, 'getPurchaseRequisitionLines']);

    Route::get('/sign-in-as-user/{id}', [ManageUserController::class, 'signInAsUser'])->name('sign-in-as-user');

    // New Screens
    Route::get('/new-screens/dashboard', function () {
        return view('new-screens.dashboard');
    })->name('new-screens.dashboard');

    Route::get('/new-screens/platform-revenue', function () {
        return view('new-screens.platform-revenue');
    })->name('new-screens.platform-revenue');

    Route::get('/new-screens/buyer', function () {
        return view('new-screens.buyer');
    })->name('new-screens.buyer');

    Route::get('/new-screens/dispute-and-claims', function () {
        return view('new-screens.dispute-and-claims');
    })->name('new-screens.dispute-and-claims');

    Route::get('/new-screens/geofence-rule-engine', function () {
        return view('new-screens.geofence-rule-engine');
    })->name('new-screens.geofence-rule-engine');

    Route::get('/new-screens/fbs-warehouse', function () {
        return view('new-screens.fbs-warehouse');
    })->name('new-screens.fbs-warehouse');

    Route::get('/new-screens/orders', function () {
        return view('new-screens.orders');
    })->name('new-screens.orders');

    Route::get('/new-screens/products', function () {
        return view('new-screens.products');
    })->name('new-screens.products');

    Route::get('/new-screens/compliance-center', function () {
        return view('new-screens.compliance-center');
    })->name('new-screens.compliance-center');
    Route::get('/new-screens/fee-configuration', function () {

        return view('new-screens.fee-configuration');

    })->name('new-screens.fee-configuration');



    Route::get('/new-screens/sellers', function () {

        return view('new-screens.sellers');

    })->name('new-screens.sellers');



    Route::get('/new-screens/seller-detail', function () {

        return view('new-screens.seller-detail');

    })->name('new-screens.seller-detail');



    Route::get('/new-screens/ad-platform-overview', function () {

        return view('new-screens.ad-platform-overview');

    })->name('new-screens.ad-platform-overview');

    Route::get('/new-screens/financial-report', function () {

        return view('new-screens.financial-report');

    })->name('new-screens.financial-report');

    Route::get('/new-screens/pending-applications', function () {
        return view('new-screens.pending-applications');
    })->name('new-screens.pending-applications');


    Route::get('/new-screens/platform-settings', function () {

        return view('new-screens.platform-settings');

    })->name('new-screens.platform-settings');

    Route::get('/navigation_page', [HomeController::class, 'navigationPage']);
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/home/get-totals', [HomeController::class, 'getTotals']);
    Route::get('/home/product-stock-alert', [HomeController::class, 'getProductStockAlert']);
    Route::get('/home/purchase-payment-dues', [HomeController::class, 'getPurchasePaymentDues']);
    Route::get('/home/sales-payment-dues', [HomeController::class, 'getSalesPaymentDues']);
    Route::get('/home/search', [HomeController::class, 'search']);
    Route::post('/attach-medias-to-model', [HomeController::class, 'attachMediasToGivenModel'])->name('attach.medias.to.model');
    Route::get('/calendar', [HomeController::class, 'getCalendar'])->name('calendar');

    Route::post('/test-email', [BusinessController::class, 'testEmailConfiguration']);
    Route::post('/test-sms', [BusinessController::class, 'testSmsConfiguration']);
    Route::get('/business/settings', [BusinessController::class, 'getBusinessSettings'])->name('business.getBusinessSettings');
    Route::post('/business/update', [BusinessController::class, 'postBusinessSettings'])->name('business.postBusinessSettings');
    Route::get('/user/profile', [UserController::class, 'getProfile'])->name('user.getProfile');
    Route::post('/user/update', [UserController::class, 'updateProfile'])->name('user.updateProfile');
    Route::post('/user/profile/update-field', [UserController::class, 'updateProfileField'])->name('user.updateProfileField');
    Route::post('/user/update-password', [UserController::class, 'updatePassword'])->name('user.updatePassword');

    // Brand config routes - must come before resource route
    Route::get('brands/{id}/config', [BrandController::class, 'config'])->name('brands.config');
    Route::post('brands/{id}/config', [BrandController::class, 'saveConfig'])->name('brands.saveConfig');
    Route::post('brands/{id}/test-email', [BrandController::class, 'testEmailConfig'])->name('brands.testEmail');

    Route::resource('brands', BrandController::class);

    // Specific routes must come BEFORE resource route to avoid route conflicts
    Route::get('shipping-stations/quick-add-station-name', [ShippingStationController::class, 'quickAddStationName'])->name('shipping-stations.quick-add-name');
    Route::post('shipping-stations/quick-add-station-name', [ShippingStationController::class, 'storeQuickAddStationName'])->name('shipping-stations.store-quick-add-name');
    Route::post('shipping-stations/save-to-transaction', [ShippingStationController::class, 'saveStationToTransaction'])->name('shipping-stations.save-to-transaction');
    Route::resource('shipping-stations', ShippingStationController::class);

    // Route::resource('payment-account', \App\Http\Controllers\PaymentAccountController::class); // Controller not found

    Route::resource('tax-rates', TaxRateController::class);
    Route::get('list-tax-rates', [TaxRateController::class, 'charges']);

    Route::resource('locationtaxtype', LocationTaxTypeController::class);

    Route::resource('units', UnitController::class);

    Route::resource('options', \App\Http\Controllers\OptionController::class);

    Route::resource('ledger-discount', LedgerDiscountController::class)->only('edit', 'destroy', 'store', 'update');

    Route::post('check-mobile', [ContactController::class, 'checkMobile']);
    Route::get('/get-contact-due/{contact_id}', [ContactController::class, 'getContactDue']);
    Route::get('/contacts/payments/{contact_id}', [ContactController::class, 'getContactPayments']);
    Route::get('/contacts/map', [ContactController::class, 'contactMap']);
    Route::get('/contacts/update-status/{id}', [ContactController::class, 'updateStatus']);
    Route::post('/contacts/bulk-update-status', [ContactController::class, 'bulkUpdateStatus']);
    Route::post('/contacts/bulk/delete', [ContactController::class, 'massDestroy']);
    Route::get('/contacts/approve/{id}', [ContactController::class, 'approve']);
    Route::get('/contacts/not-approve/{id}', [ContactController::class, 'notApprove']);

    Route::get('/contacts/stock-report/{supplier_id}', [ContactController::class, 'getSupplierStockReport']);
    Route::get('/contacts/ledger', [ContactController::class, 'getLedger']);
    Route::post('/contacts/send-ledger', [ContactController::class, 'sendLedger']);
    Route::get('/contacts/import', [ContactController::class, 'getImportContacts'])->name('contacts.import');
    Route::post('/contacts/import', [ContactController::class, 'postImportContacts']);
    Route::post('/contacts/export', [ContactController::class, 'exportContacts']);
    Route::get('/contacts/customers-for-merge', [ContactController::class, 'getCustomersForMerge']);
    Route::get('/contacts/merge-preview', [ContactController::class, 'getMergePreview']);
    Route::post('/contacts/merge', [ContactController::class, 'mergeCustomerAccounts']);
    Route::post('/contacts/check-contacts-id', [ContactController::class, 'checkContactId']);
    Route::post('/contacts/check-contacts-username', [ContactController::class, 'checkContactUserName']);
    Route::get('/contacts/customers', [ContactController::class, 'getCustomers']);
    Route::get('/contacts/invoice-customers', [ContactController::class, 'getInoiceCustomers']);
    Route::get('/contacts/customer-cart/{contact_id}', [ContactController::class, 'getCustomerCart']);
    Route::get('/contacts/sync-customer-cart/{contact_id}', [ContactController::class, 'syncCustomerCart']);
    Route::post('/contacts/customer-cart/cart', [CartController::class, 'bulkCartAddOrUpdate']);
    Route::post('/contacts/customer-cart/cart/delete', [CartController::class, 'deleteItem']);
    Route::post('/contacts/customer-cart/cart/reduce', [CartController::class, 'reduceQty']);
    Route::post('/customer-cart/checkout-address', [CartController::class, 'address']);
    Route::get('/contacts/customer-prices/{contact_id}', [ContactController::class, 'getCustomerPrices']);
    Route::post('/contacts/customer-cart/update-recall-price', [ContactController::class, 'updateRecallPrice']);
    Route::get('/contacts/customer-counts', [ContactController::class, 'getCustomerCounts']);
    Route::post('/customer-cart-process-order', [PaymentOrderController::class, 'processOrder']);
    Route::resource('contacts', ContactController::class);
    Route::resource('customer-addresses', CustomerAddressController::class);

    // Lead routes
    Route::resource('leads', LeadController::class);
    Route::get('visit-tracking', [LeadController::class, 'visitTracking'])->name('leads.visit-tracking');
    Route::get('leads/visit-details/{id}', [LeadController::class, 'visitDetails'])->name('leads.visit-details');
    Route::post('leads/store-track', [LeadController::class, 'storeTrack'])->name('leads.store-track');

    // Ticket routes
    Route::resource('tickets', TicketController::class);
    Route::post('/tickets/{id}/add-message', [TicketController::class, 'addMessage'])->name('tickets.add-message');
    Route::post('/tickets/{id}/update-status', [TicketController::class, 'updateStatus'])->name('tickets.update-status');

    // Visit History routes
    Route::get('visit-history', [\App\Http\Controllers\VisitHistoryController::class, 'index'])->name('visit-history.index');
    Route::get('visit-history/{id}', [\App\Http\Controllers\VisitHistoryController::class, 'show'])->name('visit-history.show');

    // Credit Line routes
    Route::resource('credit-lines', CreditLineController::class);
    Route::get('/credit-lines/{id}/view', [CreditLineController::class, 'view'])->name('credit-lines.view');
    Route::get('/credit-lines/{id}/approve', [CreditLineController::class, 'approve'])->name('credit-lines.approve');
    Route::post('/credit-lines/{id}/approve', [CreditLineController::class, 'processApproval'])->name('credit-lines.process-approval');
    Route::get('/credit-lines/{id}/reject', [CreditLineController::class, 'reject'])->name('credit-lines.reject');

    // Complaint routes
    Route::get('complaints/contact/{contact_id}/transactions', [ComplaintController::class, 'getContactTransactions'])->name('complaints.contact.transactions');
    Route::get('complaints/transaction/{transaction_id}/variations', [ComplaintController::class, 'getTransactionVariations'])->name('complaints.transaction.variations');
    Route::resource('complaints', ComplaintController::class);

    // Business Identification routes
    Route::resource('business-identifications', App\Http\Controllers\BusinessIdentificationController::class);

    Route::get('taxonomies-ajax-index-page', [TaxonomyController::class, 'getTaxonomyIndexPage']);
    Route::get('get-categories-for-location/{location_id}', [TaxonomyController::class, 'getCategoriesForLocation']);
    Route::get('get-brands-for-location/{location_id}', [TaxonomyController::class, 'getBrandsForLocation']);
    Route::resource('taxonomies', TaxonomyController::class);

    Route::resource('variation-templates', VariationTemplateController::class);


    //Sale limit control of product 
    Route::get('/products/sale-limit-control', [ProductController::class, 'getAllProductOrderLimit']);
    Route::get('/products/sale-limit-control/get-all', [ProductController::class, 'getAllProductOrderLimit']);
    Route::post('/products/sale-limit-control/create', [ProductController::class, 'createProductOrderLimitRule']);
    Route::post('/products/sale-limit-control/update', [ProductController::class, 'updateProductOrderLimitRule']);
    Route::post('/products/sale-limit-control/delete', [ProductController::class, 'deleteProductOrderLimitRule']);
    Route::post('/products/sale-limit-control/get', [ProductController::class, 'getProductOrderLimitRule']);
    Route::post('/products/sale-limit-control/update-sale-limit', [ProductController::class, 'updateSaleLimit']);
    Route::post('/products/sale-limit-control/get-product-details', [ProductController::class, 'getProductDetailsForRule']);
    Route::post('/products/sale-limit-control/get-consumer-details', [ProductController::class, 'getConsumerDetails']);
    Route::post('/products/sale-limit-control/get-consumer-logs', [ProductController::class, 'getConsumerLogs']);
    Route::post('/products/sale-limit-control/update-variant-purchase-limit', [ProductController::class, 'updateVariantPurchaseLimit']);

    Route::get('/products/get-variations/{productId}', [ProductController::class, 'getVariations']);

    Route::get('/products/get-product-order-limit-rules', [ProductController::class, 'getProductOrderLimitRules']);

    Route::get('/products/download-excel', [ProductController::class, 'downloadExcel']);
    Route::get('/products/manage-stock', [ProductController::class, 'addStock']);


    Route::get('/products/stock-history/{id}', [ProductController::class, 'productStockHistory']);
    Route::get('/delete-media/{media_id}', [ProductController::class, 'deleteMedia']);
    Route::post('/products/mass-deactivate', [ProductController::class, 'massDeactivate']);
    Route::get('/products/activate/{id}', [ProductController::class, 'activate']);
    Route::post('/products/discontinue/{id}', [ProductController::class, 'discontinue']);
    Route::post('/products/mass-discontinue', [ProductController::class, 'massDiscontinue']);
    Route::post('/products/mass-activate', [ProductController::class, 'massActivate']);
    Route::post('/products/deactivate/{id}', [ProductController::class, 'deactivate']);
    Route::get('/products/view-product-group-price/{id}', [ProductController::class, 'viewGroupPrice']);
    Route::get('/products/add-selling-prices/{id}', [ProductController::class, 'addSellingPrices']);
    Route::post('/products/save-selling-prices', [ProductController::class, 'saveSellingPrices']);
    Route::get('/products/edit-selling-price', [ProductController::class, 'editSellingPrice']);
    Route::get('/products/get-filter-options', [ProductController::class, 'getFilterOptions']);
    Route::get('/products/get-filtered-products', [ProductController::class, 'getFilteredProducts']);
    Route::post('/products/update-selling-price', [ProductController::class, 'updateSellingPrice']);
    Route::post('/products/update-bulk-selling-price', [ProductController::class, 'updateBulkSellingPrice']);
    Route::get('/products/get-variation-details', [ProductController::class, 'getVariationDetails']);
    Route::post('/products/upload-variation-image-instantly', [ProductController::class, 'uploadVariationImageInstantly']);
    Route::post('/products/create-variation-instantly', [ProductController::class, 'createVariationInstantly']);
    Route::post('/products/mass-delete', [ProductController::class, 'massDestroy']);
    Route::get('/products/view/{id}', [ProductController::class, 'view']);
    Route::get('/products/list', [ProductController::class, 'getProducts']);
    Route::get('/products/list-no-variation', [ProductController::class, 'getProductsWithoutVariations']);
    Route::post('/products/bulk-edit', [ProductController::class, 'bulkEdit']);
    Route::post('/products/bulk-update', [ProductController::class, 'bulkUpdate']);
    Route::post('/products/bulk-update-location', [ProductController::class, 'updateProductLocation']);
    Route::get('/products/get-product-to-edit/{product_id}', [ProductController::class, 'getProductToEdit']);

    Route::post('/products/get_sub_categories', [ProductController::class, 'getSubCategories']);
    Route::get('/products/get_sub_units', [ProductController::class, 'getSubUnits']);
    Route::post('/products/product_form_part', [ProductController::class, 'getProductVariationFormPart']);
    Route::post('/products/get_product_variation_row', [ProductController::class, 'getProductVariationRow']);
    Route::post('/products/get_variation_template', [ProductController::class, 'getVariationTemplate']);

    Route::post('/products/get_variation_value_row_by_id', [ProductController::class, 'getVariationValueRowById']);

    Route::get('/products/get_variation_value_row', [ProductController::class, 'getVariationValueRow']);
    Route::post('/products/check_product_sku', [ProductController::class, 'checkProductSku']);
    Route::post('/products/check_product_barcode_no', [ProductController::class, 'checkProductBarcodeNO']);
    Route::post('/products/validate_variation_skus', [ProductController::class, 'validateVaritionSkus']); //validates multiple skus at once
    Route::post('/products/validate_variation_barcodes', [ProductController::class, 'validateVaritionBarcodes']); //validates multiple skus at once
    Route::get('/products/quick_add', [ProductController::class, 'quickAdd']);
    Route::post('/products/save_quick_product', [ProductController::class, 'saveQuickProduct']);
    Route::get('/products/get-combo-product-entry-row', [ProductController::class, 'getComboProductEntryRow']);
    Route::post('/products/toggle-woocommerce-sync', [ProductController::class, 'toggleWooCommerceSync']);
    Route::get('/products/child/{id}', [ProductController::class, 'showChildProduct']);
    Route::delete('/products/delete-media/{id}', [ProductController::class, 'deleteProductImage']);
    Route::post('/products/remove-gallery-image', [ProductController::class, 'removeGalleryImage']);

    // Dropshipping routes
    Route::prefix('dropship')->name('dropship.')->group(function () {
        // Dashboard
        Route::get('/', [DropshipDashboardController::class, 'index'])->name('dashboard');

        // Orders
        Route::get('/orders', [DropshipOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/data', [DropshipOrderController::class, 'data'])->name('orders.data');
        Route::get('/orders/{id}', [DropshipOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/sync-woo', [DropshipOrderController::class, 'syncOrderToWoo'])->name('orders.sync-woo');
        Route::post('/orders/bulk-sync-woo', [DropshipOrderController::class, 'bulkSyncOrdersToWoo'])->name('orders.bulk-sync-woo');
        Route::post('/orders/{id}/retry-sync', [DropshipOrderController::class, 'retrySync'])->name('orders.retry-sync');
        Route::post('/orders/{id}/add-tracking', [DropshipOrderController::class, 'addTracking'])->name('orders.add-tracking');
        Route::post('/orders/{id}/update-status', [DropshipOrderController::class, 'updateStatus'])->name('orders.update-status');

        // Sync endpoints and history (aliases for both dot and hyphen names used in views)
        Route::post('/sync-products', [DropshipOrderController::class, 'syncProducts'])->name('sync-products');
        Route::post('/sync/products', [DropshipOrderController::class, 'syncProducts'])->name('sync.products');
        Route::get('/sync', [DropshipOrderController::class, 'syncHistory'])->name('sync.history');
        Route::get('/sync/data', [DropshipOrderController::class, 'syncHistoryData'])->name('sync.history.data');
        Route::get('/sync/{id}/details', [DropshipOrderController::class, 'syncDetails'])->name('sync.details');
        Route::get('/sync/{id}/status', [DropshipOrderController::class, 'syncStatus'])->name('sync.status');

        // Vendors
        Route::resource('vendors', DropshipVendorController::class);
        Route::get('/vendors/{id}/products', [DropshipVendorController::class, 'products'])->name('vendors.products');
        Route::post('/vendors/{id}/products', [DropshipVendorController::class, 'addProductMapping'])->name('vendors.add-product');
        Route::put('/vendors/{vendor_id}/products/{product_id}', [DropshipVendorController::class, 'updateProductMapping'])->name('vendors.update-product');
        Route::delete('/vendors/{vendor_id}/products/{product_id}', [DropshipVendorController::class, 'removeProductMapping'])->name('vendors.remove-product');
        Route::get('/vendors/{id}/orders', [DropshipVendorController::class, 'orders'])->name('vendors.orders');

        // Vendor Product Requests (Admin)
        Route::get('/product-requests', [DropshipVendorController::class, 'productRequests'])->name('product-requests.index');
        Route::get('/product-requests/data', [DropshipVendorController::class, 'productRequestsData'])->name('product-requests.data');
        Route::get('/product-requests/{id}', [DropshipVendorController::class, 'showProductRequest'])->name('product-requests.show');
        Route::put('/product-requests/{id}/approve', [DropshipVendorController::class, 'approveProductRequest'])->name('product-requests.approve');
        Route::put('/product-requests/{id}/create-approve', [DropshipVendorController::class, 'createAndApproveProductRequest'])->name('product-requests.create-approve');
        Route::put('/product-requests/{id}/reject', [DropshipVendorController::class, 'rejectProductRequest'])->name('product-requests.reject');
    });

    Route::resource('products', ProductController::class);
    Route::get('/products/get-combo-product-entry-row', [ProductController::class, 'getComboProductEntryRow']);
    Route::get('/toggle-subscription/{id}', 'SellPosController@toggleRecurringInvoices');
    Route::post('/sells/pos/get-types-of-service-details', 'SellPosController@getTypesOfServiceDetails');
    Route::get('/sells/subscriptions', 'SellPosController@listSubscriptions');
    Route::get('/sells/duplicate/{id}', 'SellController@duplicateSell');
    Route::get('/sells/drafts', 'SellController@getDrafts');
    Route::get('/sells/convert-to-draft/{id}', 'SellPosController@convertToInvoice');
    Route::get('/sells/convert-to-proforma/{id}', 'SellPosController@convertToProforma');
    Route::get('/sells/quotations', 'SellController@getQuotations');
    Route::get('/sells/draft-dt', 'SellController@getDraftDatables');
    Route::resource('sells', 'SellController')->except(['show']);
    Route::get('/sells/copy-quotation/{id}', [SellPosController::class, 'copyQuotation']);

    Route::post('/import-purchase-products', [PurchaseController::class, 'importPurchaseProducts']);
    Route::post('/purchases/update-status', [PurchaseController::class, 'updateStatus']);
    Route::get('/purchases/get_products', [PurchaseController::class, 'getProducts']);

    // Route::get('/purchases/get_products_metrix', [PurchaseController::class, 'getProductsMetrix']);

    Route::get('/purchases/get_suppliers', [PurchaseController::class, 'getSuppliers']);
    Route::get('/purchases/get_suppliers_auto', [PurchaseController::class, 'getSuppliersAuto']);
    Route::post('/purchases/get_purchase_entry_row', [PurchaseController::class, 'getPurchaseEntryRow']);
    Route::post('/purchases/get_purchase_entry_row/popup', [PurchaseController::class, 'getPurchaseEntryRowPopup']);
    Route::get('/purchases/get_purchase_entry_row/metrix/{variation_ids}/{location_id}', [PurchaseController::class, 'getPurchaseEntryRowMatrix']);
    Route::post('/sell/get_modal_entry_row', [SellController::class, 'getModalEntryRow']);
    Route::post('/purchases/check_ref_number', [PurchaseController::class, 'checkRefNumber']);
    Route::post('/purchases/update-ref-no/{id}', [PurchaseController::class, 'updateRefNo']);
    Route::delete('/purchases/voidPurchase/{id}', [PurchaseController::class, 'voidPurchase']);
    Route::post('/purchases/import-csv', [PurchaseController::class, 'importFromCsv'])->name('purchases.import-csv');
    Route::get('/purchases/download-csv-template', [PurchaseController::class, 'downloadCsvTemplate'])->name('purchases.download-csv-template');
    Route::resource('purchases', PurchaseController::class)->except(['show']);

    Route::get('/toggle-subscription/{id}', [SellPosController::class, 'toggleRecurringInvoices']);
    Route::post('/sells/pos/get-types-of-service-details', [SellPosController::class, 'getTypesOfServiceDetails']);
    Route::get('/sells/subscriptions', [SellPosController::class, 'listSubscriptions']);
    Route::get('/sells/duplicate/{id}', [SellController::class, 'duplicateSell']);
    Route::get('/sells/drafts', [SellController::class, 'getDrafts']);
    Route::get('/sells/convert-to-draft/{id}', [SellPosController::class, 'convertToInvoice']);
    Route::get('/sells/convert-to-proforma/{id}', [SellPosController::class, 'convertToProforma']);
    Route::get('/sells/quotations', [SellController::class, 'getQuotations']);
    Route::get('/sells/draft-dt', [SellController::class, 'getDraftDatables']);
    Route::delete('/sells/voidSell/{id}', [SellPosController::class, 'voidSell']);
    Route::get('/sells/get-order-count', [SellController::class, 'getOrderCount']);
    Route::get('/sells/daily-order-reminder', [SellController::class, 'getDailyOrderReminder']);
    Route::get('/sells/get-order-stats', [SellController::class, 'getOrderStats']);
    Route::resource('sells', SellController::class)->except(['show']);

    Route::get('/import-sales', [ImportSalesController::class, 'index']);
    Route::post('/import-sales/preview', [ImportSalesController::class, 'preview']);
    Route::post('/import-sales', [ImportSalesController::class, 'import']);
    Route::get('/revert-sale-import/{batch}', [ImportSalesController::class, 'revertSaleImport']);

    Route::get('/sells/pos/getmatrixproduct/{id}/{priceGroupId}', [ProductController::class, 'getMatrixData']);

    Route::get('/sells/pos/edit_price_product_modal/{id}/{priceGroupId}', [ProductController::class, 'getEditPriceProductModal']);
    Route::post('/sells/pos/update_price_popup', [ProductController::class, 'updatePricePopUP']);


    Route::get('/sells/pos/activity_modal/{id}', [SellPosController::class, 'openActivityModal']);
    Route::get('/sells/pos/sell_note_modal/{id}', [SellController::class, 'openSellNoteModal']);
    Route::post('/sells/update-note/{id}', [SellController::class, 'updateSellNote']);
    Route::get('/sells/pos/edit_shipping_address/{id}', [SellController::class, 'openEditShippingAddressModal']);
    Route::post('/sells/pos/update_shipping_address_transaction', [SellController::class, 'updateShippingAddressTransaction']);

    Route::get('/sells/pos/history_modal', [SellPosController::class, 'openHistoryModal']);
    Route::get('/sells/pos/history_modal_ajax/{id}/{range}', [SellPosController::class, 'openHistoryModalAjax']);
    Route::post('/update-sell-line', [SellController::class, 'updateSellsLine']);


    Route::get('/sells/pos/get_product_row/{variation_id}/{location_id}', [SellPosController::class, 'getProductRow']);
    Route::get('/sells/pos/get_matrix_row/{variation_ids}/{location_id}', [SellPosController::class, 'getMatrixRow']);
    Route::get('/sells/pos/get_erp_new_product_row', [SellPosController::class, 'getErpNewProductRow']);

    Route::post('/sells/pos/get_payment_row', [SellPosController::class, 'getPaymentRow']);
    Route::post('/sells/pos/get-reward-details', [SellPosController::class, 'getRewardDetails']);
    Route::get('/sells/pos/get-recent-transactions', [SellPosController::class, 'getRecentTransactions']);
    Route::get('/sells/pos/get-product-suggestion', [SellPosController::class, 'getProductSuggestion']);
    Route::get('/sells/pos/get-featured-products/{location_id}', [SellPosController::class, 'getFeaturedProducts']);
    Route::get('/reset-mapping', [SellController::class, 'resetMapping']);
    Route::get('/sells/price-recall/get_product_row/{variation_id}/{contact_id}', [ContactController::class, 'getProductData']);
    Route::post('/delete-recall-price', [ContactController::class, 'deleteRecallPrice']);
    Route::resource('pos', SellPosController::class);

    //order fulfillment
    Route::resource('order-fulfillment', OrderfulfillmentController::class);
    // erp picker man 
    Route::get('/order-fulfillment-history', [OrderfulfillmentController::class, 'history']);
    Route::get('/order-fulfillment-history-data', [OrderfulfillmentController::class, 'historyData']);
    Route::get('/order-fulfillment-picker', [OrderfulfillmentController::class, 'picker']);
    Route::post('/logging-active/{status}', [OrderfulfillmentController::class, 'loggingActive']);
    Route::get('/picker-man-order', [OrderfulfillmentController::class, 'pickerManOrder']);
    Route::post('/pick/start-time', [OrderfulfillmentController::class, 'storeStartTime'])->name('pick.startTime');
    Route::post('/pick/end-time', [OrderfulfillmentController::class, 'storeEndTime'])->name('pick.endTime');
    Route::post('/update-priorities', [OrderfulfillmentController::class, 'updatePriorities']);

    // Preprocessing Orders - Dropshipping workflow (must be before general order tabs)
    Route::get('/order-fulfillment-preprocessing', [OrderfulfillmentController::class, 'preprocessingOrder']);

    // WooCommerce Orders
    Route::get('/woocommerce-orders', [OrderfulfillmentController::class, 'woocommerceOrders'])->name('woocommerce.orders');

    // erp order tabs 
    Route::get('/processing-order', [OrderfulfillmentController::class, 'processingOrder']);
    Route::get('/picking-order', [OrderfulfillmentController::class, 'pickingOrder']);
    Route::get('/picked-order', [OrderfulfillmentController::class, 'pickedOrder']);
    Route::get('/cancel-order', [OrderfulfillmentController::class, 'cancelOrder']);
    Route::get('/complete-order', [OrderfulfillmentController::class, 'completeOrder']);
    Route::get('/order-counts', [OrderfulfillmentController::class, 'getOrderCountsAjax']);
    // erp manage order tab apis 
    Route::get('/packed-order', [OrderfulfillmentController::class, 'packedOrderToInoice']);
    Route::post('/process-To-pending', [OrderfulfillmentController::class, 'processTopending']);
    Route::post('/packing-to-process', [OrderfulfillmentController::class, 'packingToprocess']);
    Route::post('/process-to-packing', [OrderfulfillmentController::class, 'processtopacking']);
    Route::post('/cancel-to-pending', [OrderfulfillmentController::class, 'orderTopacking']);
    Route::post('/packing-to-ordered', [OrderfulfillmentController::class, 'packedOrderToInoice']);
    Route::get('/held', [OrderfulfillmentController::class, 'held']);
    Route::post('/held', [OrderfulfillmentController::class, 'held1']);

    // pack stage 

    Route::get('/change-picking-status/{id}', [OrderfulfillmentController::class, 'changePickingStatus']);
    Route::post('/change-picking-status', [OrderfulfillmentController::class, 'changePickingStatusStore']);
    Route::post('/get-est-rate', [ShipStationController::class, 'getEstRate']);

    // shipstation 
    Route::post('shipstation/services', [ShipStationController::class, 'storeServices']);
    Route::delete('shipstations/verify/{id}', [ShipStationController::class, 'verifyShipStation']);
    Route::delete('shipstations/delete/{id}', [ShipStationController::class, 'delete']);
    Route::get('shipstations/shipStationServices/{id}', [ShipStationController::class, 'shipStationServices']);
    Route::resource('shipstation', ShipStationController::class);

    // payment gateway station 
    Route::resource('merchant-applications', MerchantApplicationController::class);
    // Route::get('credit-application-form', [CatalogController::class, 'creditApplicationForm']);



    //need to add requested item to so
    Route::post('/apply-order-operation', [OrderfulfillmentController::class, 'applyOrderOperation']);
    Route::get('/get-active-picker', [OrderfulfillmentController::class, 'getActivePicker']);
    Route::post('/mark-as-picked', [OrderfulfillmentController::class, 'markAsPicked']);
    Route::post('/bypass-to-shipping', [OrderfulfillmentController::class, 'bypassToShipping']);
    Route::get('/bypass-order-modal/{id}', [OrderfulfillmentController::class, 'getBypassModal']);
    Route::post('/bypass-order-partial', [OrderfulfillmentController::class, 'bypassOrderPartial']);
    Route::get('/sells-picking/{id}', [SellController::class, 'manualPick']);
    Route::get('/sells-picking-popup/{id}', [SellController::class, 'manualPickPopup']);
    Route::get('/sell-pick-verify-data/{id}', [SellController::class, 'sellPickVerifyData']);
    Route::get('/cancel-so/{id}', [SellController::class, 'cancelSO']);
    Route::post('/cancel-so', [SellController::class, 'cancelSOFromPending']);
    Route::get('/sells-packing/{id}', [SellController::class, 'manualPack']);
    Route::get('/sell-local-pickup/{id}', [SellController::class, 'markLocalPickup'])->name('sell-local-pickup');
    Route::post('/sells-picking', [SellController::class, 'manualPickStore'])->name('pick.store');
    Route::post('/sells-picking/revert', [OrderfulfillmentController::class, 'revert'])->name('pick.revert');
    Route::post('/sells-packing', [SellController::class, 'manualPackStore']);
    Route::get('/sells-invoice-create/{id}', [SellController::class, 'saleInvoiceCreate'])->name('pick.sellInvoice');
    Route::post('/sells-invoice-store', [SellController::class, 'saleInvoiceStore']);
    Route::post('/void-shipment/{id}', [ShipStationController::class, 'voidShipment']); //void invoice
    Route::post('list-of-shipment', [ShipStationController::class, 'listOfShipment']); //
    // old lock logic 
    Route::get('/sells-lock/{id}', [OrderfulfillmentController::class, 'lockSale']);
    Route::get('/sells-unlock/{id}', [OrderfulfillmentController::class, 'unLock']);
    Route::post('/sells-takeover/{id}', [OrderfulfillmentController::class, 'takeOver']);
    Route::post('/sells/cancel-sales-order/{id}', [SellController::class, 'cancelSalesOrder'])->name('sells.cancelSalesOrder');


    // new lock logic 
    Route::get('/session-unlock-model/{modalType}/{id}', [OrderfulfillmentController::class, 'unLockModel']); // modal popup
    Route::get('/session-lock/{modelType}/{modelId}', [OrderfulfillmentController::class, 'checkModalAccess']);
    Route::get('/session-ping/{modelType}/{modelId}', [OrderfulfillmentController::class, 'pingModal']);
    Route::post('/session-release/{modelType}/{modelId}', [OrderfulfillmentController::class, 'releaseModal'])->name('release.modal.web');

    Route::get('/contact/redirect-sale/{id}/{type}', [SellController::class, 'createSaleRedirect']);

    Route::resource('contact-us', ContactUsController::class);
    Route::post('/contact-us/send-mail', [ContactUsController::class, 'sendMail']);
    // Route::get('/send-mail-popup/{id}', [ContactUsController::class, 'mailPopup']);
    Route::get('/newsletter', [ContactUsController::class, 'getNewsLetter'])->name('newsletter');
    Route::delete('/newsletter/{id}', [ContactUsController::class, 'deleteNewsLetter']);
    Route::get('/wishlist', [WishlistsController::class, 'wishlist']);
    Route::get('/multi-channel', [MultichannelController::class, 'multichannel']);
    Route::get('/multi-channel/create', [MultichannelController::class, 'create']);
    Route::post('/multi-channel', [MultichannelController::class, 'store']);
    Route::get('/multi-channel/{id}/edit', [MultichannelController::class, 'edit']);

    // COA (Certificate of Analysis) - Admin only (checked inside controller)
    Route::get('/coa', [\App\Http\Controllers\CoaController::class, 'index'])->name('coa.index');
    Route::get('/coa/create', [\App\Http\Controllers\CoaController::class, 'create'])->name('coa.create');
    Route::get('/coa/search-categories', [\App\Http\Controllers\CoaController::class, 'searchCategories'])->name('coa.searchCategories');
    Route::post('/coa', [\App\Http\Controllers\CoaController::class, 'store'])->name('coa.store');
    Route::get('/coa/{id}', [\App\Http\Controllers\CoaController::class, 'show'])->name('coa.show');
    Route::delete('/coa/{id}', [\App\Http\Controllers\CoaController::class, 'destroy'])->name('coa.destroy');




    Route::resource('roles', RoleController::class);

    Route::post('/users/update-location', [ManageUserController::class, 'updateLocation']);
    Route::resource('users', ManageUserController::class);

    Route::resource('group-taxes', GroupTaxController::class);

    Route::get('/barcodes/set_default/{id}', [BarcodeController::class, 'setDefault']);
    Route::resource('barcodes', BarcodeController::class);

    //Invoice schemes..
    Route::get('/invoice-schemes/set_default/{id}', [InvoiceSchemeController::class, 'setDefault']);
    Route::resource('invoice-schemes', InvoiceSchemeController::class);

    //Print Labels
    Route::get('/labels/show', [LabelsController::class, 'show']);
    Route::get('/labels/add-product-row', [LabelsController::class, 'addProductRow']);
    Route::get('/labels/preview', [LabelsController::class, 'preview']);

    //Reports...
    Route::get('/reports/gst-purchase-report', [ReportController::class, 'gstPurchaseReport']);
    Route::get('/reports/gst-sales-report', [ReportController::class, 'gstSalesReport']);
    Route::get('/reports/get-stock-by-sell-price', [ReportController::class, 'getStockBySellingPrice']);
    Route::get('/reports/purchase-report', [ReportController::class, 'purchaseReport']);
    Route::get('/reports/sale-report', [ReportController::class, 'saleReport']);
    Route::get('/reports/service-staff-report', [ReportController::class, 'getServiceStaffReport']);
    Route::get('/reports/service-staff-line-orders', [ReportController::class, 'serviceStaffLineOrders']);
    Route::get('/reports/table-report', [ReportController::class, 'getTableReport']);
    Route::get('/reports/profit-loss', [ReportController::class, 'getProfitLoss']);
    Route::get('/reports/profit-loss-chart-data', [ReportController::class, 'getProfitLossChartData']);
    Route::get('/reports/get-opening-stock', [ReportController::class, 'getOpeningStock']);
    Route::get('/reports/purchase-sell', [ReportController::class, 'getPurchaseSell']);
    Route::get('/reports/purchase-sell-chart-data', [ReportController::class, 'getPurchaseSellChartData']);
    Route::get('/reports/customer-supplier', [ReportController::class, 'getCustomerSuppliers']);
    Route::get('/reports/customer-supplier-chart-data', [ReportController::class, 'getCustomerSupplierChartData']);
    Route::get('/reports/stock-report', [ReportController::class, 'getStockReport']);
    Route::get('/reports/stock-report-chart-data', [ReportController::class, 'getStockReportChartData']);
    Route::get('/reports/stock-details', [ReportController::class, 'getStockDetails']);
    Route::get('/reports/tax-report', [ReportController::class, 'getTaxReport']);
    Route::get('/reports/tax-report-chart-data', [ReportController::class, 'getTaxReportChartData']);
    Route::get('/reports/tax-details', [ReportController::class, 'getTaxDetails']);
    Route::get('/reports/trending-products', [ReportController::class, 'getTrendingProducts']);
    Route::get('/reports/expense-report', [ReportController::class, 'getExpenseReport']);
    Route::get('/reports/stock-adjustment-report', [ReportController::class, 'getStockAdjustmentReport']);
    Route::get('/reports/stock-adjustment-chart-data', [ReportController::class, 'getStockAdjustmentChartData']);
    Route::get('/reports/register-report', [ReportController::class, 'getRegisterReport']);
    Route::get('/reports/register-report-chart-data', [ReportController::class, 'getRegisterReportChartData']);
    Route::get('/reports/sales-representative-report', [ReportController::class, 'getSalesRepresentativeReport']);
    Route::get('/reports/sales-representative-chart-data', [ReportController::class, 'getSalesRepresentativeChartData']);
    Route::get('/reports/sales-representative-total-expense', [ReportController::class, 'getSalesRepresentativeTotalExpense']);
    Route::get('/reports/sales-representative-total-sell', [ReportController::class, 'getSalesRepresentativeTotalSell']);
    Route::get('/reports/sales-representative-total-commission', [ReportController::class, 'getSalesRepresentativeTotalCommission']);
    Route::get('/reports/stock-expiry', [ReportController::class, 'getStockExpiryReport']);
    Route::get('/reports/stock-expiry-edit-modal/{purchase_line_id}', [ReportController::class, 'getStockExpiryReportEditModal']);
    Route::post('/reports/stock-expiry-update', [ReportController::class, 'updateStockExpiryReport'])->name('updateStockExpiryReport');
    Route::get('/reports/customer-group', [ReportController::class, 'getCustomerGroup']);
    Route::get('/reports/customer-group-chart-data', [ReportController::class, 'getCustomerGroupChartData']);
    Route::get('/reports/product-purchase-report', [ReportController::class, 'getproductPurchaseReport']);
    Route::get('/reports/product-purchase-report-chart-data', [ReportController::class, 'getProductPurchaseReportChartData']);
    Route::get('/reports/product-purchase-report-pie-chart-data', [ReportController::class, 'getProductPurchaseReportPieChartData']);
    Route::get('/reports/product-sell-grouped-by', [ReportController::class, 'productSellReportBy']);
    Route::get('/reports/product-sell-report', [ReportController::class, 'getproductSellReport']);
    Route::get('/reports/product-sell-report-chart-data', [ReportController::class, 'getProductSellReportChartData']);
    Route::get('/reports/product-sell-report-grouped-chart-data', [ReportController::class, 'getProductSellReportGroupedChartData']);
    Route::get('/reports/product-sell-report-with-purchase', [ReportController::class, 'getproductSellReportWithPurchase']);
    Route::get('/reports/product-sell-grouped-report', [ReportController::class, 'getproductSellGroupedReport']);
    Route::get('/reports/lot-report', [ReportController::class, 'getLotReport']);
    Route::get('/reports/purchase-payment-report', [ReportController::class, 'purchasePaymentReport']);
    Route::get('/reports/purchase-payment-report-chart-data', [ReportController::class, 'getPurchasePaymentReportChartData']);
    Route::get('/reports/sell-payment-report', [ReportController::class, 'sellPaymentReport']);
    Route::get('/reports/sell-payment-report-chart-data', [ReportController::class, 'getSellPaymentReportChartData']);
    Route::get('/reports/product-stock-details', [ReportController::class, 'productStockDetails']);
    Route::get('/reports/adjust-product-stock', [ReportController::class, 'adjustProductStock']);
    Route::get('/reports/get-profit/{by?}', [ReportController::class, 'getProfit']);
    Route::get('/reports/items-report', [ReportController::class, 'itemsReport']);
    Route::get('/reports/items-report-chart-data', [ReportController::class, 'getItemsReportChartData']);
    Route::get('/reports/get-stock-value', [ReportController::class, 'getStockValue']);

    Route::get('business-location/activate-deactivate/{location_id}', [BusinessLocationController::class, 'activateDeactivateLocation']);

    //Business Location Settings...
    Route::prefix('business-location/{location_id}')->name('location.')->group(function () {
        Route::get('settings', [LocationSettingsController::class, 'index'])->name('settings');
        Route::post('settings', [LocationSettingsController::class, 'updateSettings'])->name('settings_update');
    });

    //Business Locations...
    Route::post('business-location/check-location-id', [BusinessLocationController::class, 'checkLocationId']);
    Route::resource('business-location', BusinessLocationController::class);

    //Invoice layouts..
    Route::resource('invoice-layouts', InvoiceLayoutController::class);

    Route::post('get-expense-sub-categories', [ExpenseCategoryController::class, 'getSubCategories']);

    //Expense Categories...
    Route::resource('expense-categories', ExpenseCategoryController::class);

    //Expenses...
    Route::resource('expenses', ExpenseController::class);

    Route::prefix('bookkeeping')->name('bookkeeping.')->group(function () {
        // Dashboard
        Route::get('/', [\App\Http\Controllers\BookkeepingController::class, 'dashboard'])->name('dashboard');

        // Chart of Accounts
        Route::get('/chart-of-accounts', [\App\Http\Controllers\BookkeepingController::class, 'chartOfAccounts'])->name('accounts.index');
        Route::get('/accounts/create', [\App\Http\Controllers\BookkeepingController::class, 'createAccount'])->name('accounts.create');
        Route::post('/accounts', [\App\Http\Controllers\BookkeepingController::class, 'storeAccount'])->name('accounts.store');
        Route::get('/accounts/{id}/edit', [\App\Http\Controllers\BookkeepingController::class, 'editAccount'])->name('accounts.edit');
        Route::put('/accounts/{id}', [\App\Http\Controllers\BookkeepingController::class, 'updateAccount'])->name('accounts.update');
        Route::delete('/accounts/{id}', [\App\Http\Controllers\BookkeepingController::class, 'destroyAccount'])->name('accounts.destroy');
        Route::get('/accounts/{id}/ledger', [\App\Http\Controllers\BookkeepingController::class, 'accountLedger'])->name('accounts.ledger');
        Route::get('/detail-types/{accountType}', [\App\Http\Controllers\BookkeepingController::class, 'getDetailTypes'])->name('detail-types');

        // Journal Entries
        Route::get('/journal-entries', [\App\Http\Controllers\BookkeepingController::class, 'journalEntries'])->name('journal.index');
        Route::get('/journal-entries/create', [\App\Http\Controllers\BookkeepingController::class, 'createJournalEntry'])->name('journal.create');
        Route::post('/journal-entries', [\App\Http\Controllers\BookkeepingController::class, 'storeJournalEntry'])->name('journal.store');
        Route::get('/journal-entries/{id}', [\App\Http\Controllers\BookkeepingController::class, 'showJournalEntry'])->name('journal.show');
        Route::get('/journal-entries/{id}/edit', [\App\Http\Controllers\BookkeepingController::class, 'editJournalEntry'])->name('journal.edit');
        Route::put('/journal-entries/{id}', [\App\Http\Controllers\BookkeepingController::class, 'updateJournalEntry'])->name('journal.update');
        Route::post('/journal-entries/{id}/post', [\App\Http\Controllers\BookkeepingController::class, 'postJournalEntry'])->name('journal.post');
        Route::post('/journal-entries/{id}/void', [\App\Http\Controllers\BookkeepingController::class, 'voidJournalEntry'])->name('journal.void');
        Route::post('/journal-entries/{id}/reverse', [\App\Http\Controllers\BookkeepingController::class, 'reverseJournalEntry'])->name('journal.reverse');
        Route::post('/journal-entries/{id}/duplicate', [\App\Http\Controllers\BookkeepingController::class, 'duplicateJournalEntry'])->name('journal.duplicate');

        // Bank Deposits
        Route::get('/bank-deposits', [\App\Http\Controllers\BookkeepingController::class, 'bankDeposits'])->name('deposits.index');
        Route::get('/bank-deposits/create', [\App\Http\Controllers\BookkeepingController::class, 'createBankDeposit'])->name('deposits.create');
        Route::post('/bank-deposits', [\App\Http\Controllers\BookkeepingController::class, 'storeBankDeposit'])->name('deposits.store');
        Route::get('/bank-deposits/{id}', [\App\Http\Controllers\BookkeepingController::class, 'showBankDeposit'])->name('deposits.show');
        Route::get('/bank-deposits/{id}/edit', [\App\Http\Controllers\BookkeepingController::class, 'editBankDeposit'])->name('deposits.edit');
        Route::post('/bank-deposits/{id}/process', [\App\Http\Controllers\BookkeepingController::class, 'processBankDeposit'])->name('deposits.process');
        Route::post('/bank-deposits/{id}/void', [\App\Http\Controllers\BookkeepingController::class, 'voidBankDeposit'])->name('deposits.void');

        // Liabilities
        Route::get('/liabilities', [\App\Http\Controllers\BookkeepingController::class, 'liabilities'])->name('liabilities.index');
        Route::get('/liabilities/create', [\App\Http\Controllers\BookkeepingController::class, 'createLiability'])->name('liabilities.create');
        Route::post('/liabilities', [\App\Http\Controllers\BookkeepingController::class, 'storeLiability'])->name('liabilities.store');
        Route::get('/liabilities/{id}', [\App\Http\Controllers\BookkeepingController::class, 'showLiability'])->name('liabilities.show');
        Route::get('/liabilities/{id}/edit', [\App\Http\Controllers\BookkeepingController::class, 'editLiability'])->name('liabilities.edit');
        Route::put('/liabilities/{id}', [\App\Http\Controllers\BookkeepingController::class, 'updateLiability'])->name('liabilities.update');
        Route::get('/liabilities/{id}/payment', [\App\Http\Controllers\BookkeepingController::class, 'createLiabilityPayment'])->name('liabilities.payment.create');
        Route::post('/liabilities/{id}/payment', [\App\Http\Controllers\BookkeepingController::class, 'storeLiabilityPayment'])->name('liabilities.payment.store');

        // Reports
        Route::get('/reports/trial-balance', [\App\Http\Controllers\BookkeepingController::class, 'trialBalance'])->name('reports.trial-balance');
        Route::get('/reports/balance-sheet', [\App\Http\Controllers\BookkeepingController::class, 'balanceSheet'])->name('reports.balance-sheet');
        Route::get('/reports/income-statement', [\App\Http\Controllers\BookkeepingController::class, 'incomeStatement'])->name('reports.income-statement');

        // Partner Transactions (Business Partner Assets, Loans, Advances)
        Route::get('/partner-transactions', [\App\Http\Controllers\BookkeepingController::class, 'partnerTransactions'])->name('partner.index');
        Route::get('/partner-transactions/create', [\App\Http\Controllers\BookkeepingController::class, 'createPartnerTransaction'])->name('partner.create');
        Route::post('/partner-transactions', [\App\Http\Controllers\BookkeepingController::class, 'storePartnerTransaction'])->name('partner.store');
        Route::get('/partner-transactions/{id}', [\App\Http\Controllers\BookkeepingController::class, 'showPartnerTransaction'])->name('partner.show');
        Route::get('/partner-transactions/{id}/edit', [\App\Http\Controllers\BookkeepingController::class, 'editPartnerTransaction'])->name('partner.edit');
        Route::put('/partner-transactions/{id}', [\App\Http\Controllers\BookkeepingController::class, 'updatePartnerTransaction'])->name('partner.update');
        Route::delete('/partner-transactions/{id}', [\App\Http\Controllers\BookkeepingController::class, 'destroyPartnerTransaction'])->name('partner.destroy');

        // Inventory Valuation
        Route::get('/inventory-valuation', [\App\Http\Controllers\BookkeepingController::class, 'inventoryValuation'])->name('inventory.index');
        Route::post('/inventory-valuation/calculate', [\App\Http\Controllers\BookkeepingController::class, 'calculateInventoryValuation'])->name('inventory.calculate');
        Route::get('/inventory-valuation/history', [\App\Http\Controllers\BookkeepingController::class, 'inventoryValuationHistory'])->name('inventory.history');

        // P&L Transactions (Manual Income & Expense Entries)
        Route::get('/pl-transactions', [\App\Http\Controllers\BookkeepingController::class, 'plTransactions'])->name('pl.index');
        Route::get('/pl-transactions/income/create', [\App\Http\Controllers\BookkeepingController::class, 'createIncome'])->name('pl.income.create');
        Route::post('/pl-transactions/income', [\App\Http\Controllers\BookkeepingController::class, 'storeIncome'])->name('pl.income.store');
        Route::get('/pl-transactions/expense/create', [\App\Http\Controllers\BookkeepingController::class, 'createExpense'])->name('pl.expense.create');
        Route::post('/pl-transactions/expense', [\App\Http\Controllers\BookkeepingController::class, 'storeExpense'])->name('pl.expense.store');
        Route::get('/pl-transactions/{id}', [\App\Http\Controllers\BookkeepingController::class, 'showPLTransaction'])->name('pl.show');
        Route::post('/pl-transactions/{id}/void', [\App\Http\Controllers\BookkeepingController::class, 'voidPLTransaction'])->name('pl.void');
        Route::get('/pl-transactions/summary', [\App\Http\Controllers\BookkeepingController::class, 'plSummary'])->name('pl.summary');

        // Accounts Receivable (Customer Balances - Current Asset)
        Route::get('/accounts-receivable', [\App\Http\Controllers\BookkeepingController::class, 'accountsReceivable'])->name('accounts-receivable.index');

        // Credit Notes for Customers
        Route::get('/credit-notes', [\App\Http\Controllers\BookkeepingController::class, 'creditNotes'])->name('credit-notes.index');
        Route::get('/credit-notes/create', [\App\Http\Controllers\BookkeepingController::class, 'createCreditNote'])->name('credit-notes.create');
        Route::post('/credit-notes', [\App\Http\Controllers\BookkeepingController::class, 'storeCreditNote'])->name('credit-notes.store');
        Route::get('/credit-notes/{id}', [\App\Http\Controllers\BookkeepingController::class, 'showCreditNote'])->name('credit-notes.show');
        Route::post('/credit-notes/{id}/approve', [\App\Http\Controllers\BookkeepingController::class, 'approveCreditNote'])->name('credit-notes.approve');
        Route::post('/credit-notes/{id}/apply', [\App\Http\Controllers\BookkeepingController::class, 'applyCreditNote'])->name('credit-notes.apply');
        Route::post('/credit-notes/{id}/void', [\App\Http\Controllers\BookkeepingController::class, 'voidCreditNote'])->name('credit-notes.void');
        Route::get('/customer-invoices/{contact_id}', [\App\Http\Controllers\BookkeepingController::class, 'getCustomerInvoices'])->name('customer-invoices');

        // Accounts Payable (Vendor Balances - Current Liability)
        Route::get('/accounts-payable', [\App\Http\Controllers\BookkeepingController::class, 'accountsPayable'])->name('accounts-payable.index');
        Route::post('/accounts-payable/sync', [\App\Http\Controllers\BookkeepingController::class, 'syncAPJournalEntries'])->name('accounts-payable.sync');

        // Debit Notes for Vendors (Accounts Payable Adjustments)
        Route::get('/debit-notes', [\App\Http\Controllers\BookkeepingController::class, 'debitNotes'])->name('debit-notes.index');
        Route::get('/debit-notes/data', [\App\Http\Controllers\BookkeepingController::class, 'debitNotesData'])->name('debit-notes.data');
        Route::get('/debit-notes/create', [\App\Http\Controllers\BookkeepingController::class, 'createDebitNote'])->name('debit-notes.create');
        Route::post('/debit-notes', [\App\Http\Controllers\BookkeepingController::class, 'storeDebitNote'])->name('debit-notes.store');
        Route::get('/debit-notes/{id}', [\App\Http\Controllers\BookkeepingController::class, 'showDebitNote'])->name('debit-notes.show');
        Route::get('/debit-notes/{id}/edit', [\App\Http\Controllers\BookkeepingController::class, 'editDebitNote'])->name('debit-notes.edit');
        Route::put('/debit-notes/{id}', [\App\Http\Controllers\BookkeepingController::class, 'updateDebitNote'])->name('debit-notes.update');
        Route::delete('/debit-notes/{id}', [\App\Http\Controllers\BookkeepingController::class, 'destroyDebitNote'])->name('debit-notes.destroy');
        Route::post('/debit-notes/{id}/submit', [\App\Http\Controllers\BookkeepingController::class, 'submitDebitNote'])->name('debit-notes.submit');
        Route::post('/debit-notes/{id}/approve', [\App\Http\Controllers\BookkeepingController::class, 'approveDebitNote'])->name('debit-notes.approve');
        Route::post('/debit-notes/{id}/send', [\App\Http\Controllers\BookkeepingController::class, 'sendDebitNote'])->name('debit-notes.send');
        Route::post('/debit-notes/{id}/settle', [\App\Http\Controllers\BookkeepingController::class, 'settleDebitNote'])->name('debit-notes.settle');
        Route::post('/debit-notes/{id}/void', [\App\Http\Controllers\BookkeepingController::class, 'voidDebitNote'])->name('debit-notes.void');
        Route::get('/debit-notes/{id}/pdf', [\App\Http\Controllers\BookkeepingController::class, 'debitNotePdf'])->name('debit-notes.pdf');
        Route::get('/debit-notes/{id}/print', [\App\Http\Controllers\BookkeepingController::class, 'printDebitNote'])->name('debit-notes.print');
        Route::get('/vendor-purchases/{vendorId}', [\App\Http\Controllers\BookkeepingController::class, 'getVendorPurchasesAjax'])->name('vendor-purchases');
        Route::get('/purchase-lines/{purchaseId}', [\App\Http\Controllers\BookkeepingController::class, 'getPurchaseLines'])->name('purchase-lines');
    });


    //Transaction payments...
    Route::get('/get-contact-payment-group/{contact_id}', [TransactionPaymentController::class, 'getContactPaymentGroup'])->name('get-contact-payment-group');
    Route::get('/payment-group/{id}', [TransactionPaymentController::class, 'getPaymentGroup']);
    Route::put('/payment-group/{id}', [TransactionPaymentController::class, 'updatePaymentGroup']);
    // Route::get('/payments/opening-balance/{contact_id}', 'TransactionPaymentController@getOpeningBalancePayments');
    Route::get('/payments/show-child-payments/{payment_id}', [TransactionPaymentController::class, 'showChildPayments']);
    Route::get('/payments/view-payment/{payment_id}', [TransactionPaymentController::class, 'viewPayment']);
    Route::get('/payments/add_payment/{transaction_id}', [TransactionPaymentController::class, 'addPayment']);
    Route::get('/payments/pay-contact-due/{contact_id}', [TransactionPaymentController::class, 'getPayContactDue']);
    Route::post('/payments/pay-contact-due', [TransactionPaymentController::class, 'postPayContactDue']);
    Route::resource('payments', TransactionPaymentController::class);
    //Printers...
    Route::resource('printers', PrinterController::class);

    Route::get('/stock-adjustments/remove-expired-stock/{purchase_line_id}', [StockAdjustmentController::class, 'removeExpiredStock']);
    Route::post('/stock-adjustments/get_product_row', [StockAdjustmentController::class, 'getProductRow']);
    Route::resource('stock-adjustments', StockAdjustmentController::class);

    Route::get('/cash-register/register-details', [CashRegisterController::class, 'getRegisterDetails']);
    Route::get('/cash-register/close-register/{id?}', [CashRegisterController::class, 'getCloseRegister']);
    Route::post('/cash-register/close-register', [CashRegisterController::class, 'postCloseRegister']);
    Route::resource('cash-register', CashRegisterController::class);

    //Import products
    Route::post('/import-products/store-with-sku-regeneration', [ImportProductsController::class, 'storeWithSkuRegeneration']);
    Route::get('/import-products', [ImportProductsController::class, 'index']);
    Route::post('/import-products/store', [ImportProductsController::class, 'store']);
    Route::get('/import-products/download-template', [ImportProductsController::class, 'downloadTemplate']);
    Route::get('/import-products/export-images', [ImportProductsController::class, 'exportImages']);
    Route::post('/import-products/import-image-mapping', [ImportProductsController::class, 'importImageMapping']);

    //Sales Commission Agent
    Route::post('sales-commission-agents/{id}/update-commission-settings', [SalesCommissionAgentController::class, 'updateCommissionSettings']);
    Route::post('sales-commission-agents/{id}/update-bonus-settings', [SalesCommissionAgentController::class, 'updateBonusSettings']);
    Route::post('sales-commission-agents/{id}/process-payout', [SalesCommissionAgentController::class, 'processPayout']);
    Route::resource('sales-commission-agents', SalesCommissionAgentController::class);

    //Stock Transfer
    Route::get('stock-transfers/print/{id}', [StockTransferController::class, 'printInvoice']);
    Route::post('stock-transfers/update-status/{id}', [StockTransferController::class, 'updateStatus']);
    Route::resource('stock-transfers', StockTransferController::class);

    Route::get('/opening-stock/add/{product_id}', [OpeningStockController::class, 'add']);
    Route::post('/opening-stock/save', [OpeningStockController::class, 'save']);

    //Customer Groups
    Route::resource('customer-group', CustomerGroupController::class);

    //Import opening stock
    Route::get('/import-opening-stock', [ImportOpeningStockController::class, 'index']);
    Route::post('/import-opening-stock/store', [ImportOpeningStockController::class, 'store']);

    //Sell return
    Route::get('validate-invoice-to-return/{invoice_no}', [SellReturnController::class, 'validateInvoiceToReturn']);
    // service staff replacement
    Route::get('validate-invoice-to-service-staff-replacement/{invoice_no}', [SellPosController::class, 'validateInvoiceToServiceStaffReplacement']);
    Route::put('change-service-staff/{id}', [SellPosController::class, 'change_service_staff'])->name('change_service_staff');

    Route::get('sell-return/get-product-row', [SellReturnController::class, 'getProductRow']);
    Route::get('/sell-return/print/{id}', [SellReturnController::class, 'printInvoice']);
    Route::get('/sell-return/add/{id}', [SellReturnController::class, 'add']);
    Route::get('/sell-return/get-sell-data/{id}', [SellReturnController::class, 'getSellData']);
    Route::resource('sell-return', SellReturnController::class);

    Route::get('sell-return-ecom', [SellReturnController::class, 'indexEcom'])->name('sell-return-ecom');
    Route::get('sell-return-ecom-pending', [SellReturnController::class, 'indexEcomPending'])->name('sell-return-ecom-pending');
    Route::get('sell-return-ecom-approved', [SellReturnController::class, 'indexEcomApproved'])->name('sell-return-ecom-approved');
    Route::get('sell-return-ecom-in-transit', [SellReturnController::class, 'indexEcomInTransit'])->name('sell-return-ecom-in-transit');
    Route::get('sell-return-ecom-varified', [SellReturnController::class, 'indexEcomVarified'])->name('sell-return-ecom-varified');
    Route::get('sell-return-ecom-completed', [SellReturnController::class, 'indexEcomCompleted'])->name('sell-return-ecom-completed');
    Route::get('sell-return-ecom/{id}', [SellReturnController::class, 'showEcom'])->name('sell-return-ecom.show');
    Route::post('sell-return-ecom/{id}', [SellReturnController::class, 'varifyEcom'])->name('sell-return-ecom.store');
    Route::get('sell-return-ecom/manual-pickup/{id}', [SellReturnController::class, 'manualPickup'])->name('sell-return-ecom.manual-pick');
    Route::post('sells-invoice-return-store', [SellReturnController::class, 'sellsInvoiceReturnStore'])->name('sells-invoice-return-store');
    Route::post('sell-return-ecom-create-sell-return/{id}', [SellReturnController::class, 'storeSellReturn'])->name('sell-return-ecom.create-sell-return');

    //Backup
    Route::get('backup/download/{file_name}', [BackUpController::class, 'download']);
    Route::get('backup/{id}/delete', [BackUpController::class, 'delete'])->name('delete_backup');
    Route::resource('backup', BackUpController::class)->only('index', 'create', 'store');

    Route::get('selling-price-group/activate-deactivate/{id}', [SellingPriceGroupController::class, 'activateDeactivate']);
    Route::get('update-product-price', [SellingPriceGroupController::class, 'updateProductPrice'])->name('update-product-price');
    Route::get('export-product-price', [SellingPriceGroupController::class, 'export']);
    Route::post('import-product-price', [SellingPriceGroupController::class, 'import']);

    Route::resource('selling-price-group', SellingPriceGroupController::class);

    Route::resource('notification-templates', NotificationTemplateController::class)->only(['index', 'store']);
    Route::get('notification/get-template/{transaction_id}/{template_for}', [NotificationController::class, 'getTemplate'])->name("notification.template");
    Route::post('notification/get-template-payment', [NotificationController::class, 'getTemplatePayment']);

    Route::post('notification/send', [NotificationController::class, 'send']);
    Route::post('shipment/notification/send/{id}', [OrderfulfillmentController::class, 'sendShipmentNotification']);

    Route::post('/purchase-return/update', [CombinedPurchaseReturnController::class, 'update']);
    Route::get('/purchase-return/edit/{id}', [CombinedPurchaseReturnController::class, 'edit']);
    Route::post('/purchase-return/save', [CombinedPurchaseReturnController::class, 'save']);
    Route::post('/purchase-return/get_product_row', [CombinedPurchaseReturnController::class, 'getProductRow']);
    Route::get('/purchase-return/create', [CombinedPurchaseReturnController::class, 'create']);
    Route::get('/purchase-return/add/{id}', [PurchaseReturnController::class, 'add']);
    Route::resource('/purchase-return', PurchaseReturnController::class)->except('create');

    Route::get('/discount/activate/{id}', [DiscountController::class, 'activate']);
    Route::post('/discount/mass-deactivate', [DiscountController::class, 'massDeactivate']);
    Route::resource('discount', DiscountController::class);

    Route::prefix('account')->group(function () {
        Route::resource('/account', AccountController::class);
        Route::get('/fund-transfer/{id}', [AccountController::class, 'getFundTransfer']);
        Route::post('/fund-transfer', [AccountController::class, 'postFundTransfer']);
        Route::get('/deposit/{id}', [AccountController::class, 'getDeposit']);
        Route::post('/deposit', [AccountController::class, 'postDeposit']);
        Route::get('/close/{id}', [AccountController::class, 'close']);
        Route::get('/activate/{id}', [AccountController::class, 'activate']);
        Route::get('/delete-account-transaction/{id}', [AccountController::class, 'destroyAccountTransaction']);
        Route::get('/edit-account-transaction/{id}', [AccountController::class, 'editAccountTransaction']);
        Route::post('/update-account-transaction/{id}', [AccountController::class, 'updateAccountTransaction']);
        Route::get('/get-account-balance/{id}', [AccountController::class, 'getAccountBalance']);
        Route::get('/balance-sheet', [AccountReportsController::class, 'balanceSheet']);
        Route::get('/trial-balance', [AccountReportsController::class, 'trialBalance']);
        Route::get('/payment-account-report', [AccountReportsController::class, 'paymentAccountReport']);
        Route::get('/link-account/{id}', [AccountReportsController::class, 'getLinkAccount']);
        Route::post('/link-account', [AccountReportsController::class, 'postLinkAccount']);
        Route::get('/cash-flow', [AccountController::class, 'cashFlow']);
    });

    Route::resource('account-types', AccountTypeController::class);

    //Restaurant module
    Route::prefix('modules')->group(function () {
        Route::resource('tables', Restaurant\TableController::class);
        Route::resource('modifiers', Restaurant\ModifierSetsController::class);

        //Map modifier to products
        Route::get('/product-modifiers/{id}/edit', [Restaurant\ProductModifierSetController::class, 'edit']);
        Route::post('/product-modifiers/{id}/update', [Restaurant\ProductModifierSetController::class, 'update']);
        Route::get('/product-modifiers/product-row/{product_id}', [Restaurant\ProductModifierSetController::class, 'product_row']);

        Route::get('/add-selected-modifiers', [Restaurant\ProductModifierSetController::class, 'add_selected_modifiers']);

        Route::get('/kitchen', [Restaurant\KitchenController::class, 'index']);
        Route::get('/kitchen/mark-as-cooked/{id}', [Restaurant\KitchenController::class, 'markAsCooked']);
        Route::post('/refresh-orders-list', [Restaurant\KitchenController::class, 'refreshOrdersList']);
        Route::post('/refresh-line-orders-list', [Restaurant\KitchenController::class, 'refreshLineOrdersList']);

        Route::get('/orders', [Restaurant\OrderController::class, 'index']);
        Route::get('/orders/mark-as-served/{id}', [Restaurant\OrderController::class, 'markAsServed']);
        Route::get('/data/get-pos-details', [Restaurant\DataController::class, 'getPosDetails']);
        Route::get('/data/check-staff-pin', [Restaurant\DataController::class, 'checkStaffPin']);
        Route::get('/orders/mark-line-order-as-served/{id}', [Restaurant\OrderController::class, 'markLineOrderAsServed']);
        Route::get('/print-line-order', [Restaurant\OrderController::class, 'printLineOrder']);
    });

    Route::get('bookings/get-todays-bookings', [Restaurant\BookingController::class, 'getTodaysBookings']);
    Route::resource('bookings', Restaurant\BookingController::class);

    Route::resource('types-of-service', TypesOfServiceController::class);

    // ECOM Discounts Management
    Route::get('multi-select/search', [CustomDiscountController::class, 'searchFilter']);
    Route::post('apply-coupon', [CustomDiscountController::class, 'applyCoupon'])->name('apply-coupon');
    Route::get('custom-discounts/search-discount-list', [CustomDiscountController::class, 'searchDiscountList'])->name('custom-discounts.search-discount-list');
    Route::resource('custom-discounts', CustomDiscountController::class);
    Route::post('custom-discounts/duplicate/{id}', [CustomDiscountController::class, 'duplicate'])->name('custom-discounts.duplicate');
    Route::post('custom-discounts/status-change/{id}', [CustomDiscountController::class, 'statusChange'])->name('custom-discounts.status-change');
    Route::post('custom-discounts/priority-change/{id}/{priority}', [CustomDiscountController::class, 'priorityChange'])->name('custom-discounts.priority-change');


    Route::get('sells/edit-shipping/{id}', [SellController::class, 'editShipping']);
    Route::put('sells/update-shipping/{id}', [SellController::class, 'updateShipping']);
    Route::get('shipments', [SellController::class, 'shipments']);
    Route::get('shipment/{id}', [SellController::class, 'shipmentDetails']);



    Route::post('upload-module', [Install\ModulesController::class, 'uploadModule']);
    Route::delete('manage-modules/destroy/{module_name}', [Install\ModulesController::class, 'destroy']);
    Route::resource('manage-modules', Install\ModulesController::class)
        ->only(['index', 'update']);
    Route::get('regenerate', [Install\ModulesController::class, 'regenerate']);

    Route::resource('warranties', WarrantyController::class);

    // Map & Location Web Routes
    Route::prefix('maps')->group(function () {
        Route::get('/', [MapController::class, 'index'])->name('maps.index');
        Route::get('/sales-rep', [MapController::class, 'salesRepMap'])->name('maps.sales-rep');

        // Map Data Endpoints (matching API structure but for web)
        Route::prefix('api')->group(function () {
            // GET endpoints for data retrieval
            Route::get('/leads', [MapController::class, 'getLeadsData']);
            Route::get('/visits', [MapController::class, 'getVisitsData']);
            Route::get('/nearby-leads', [MapController::class, 'getNearbyLeadsData']);
            Route::get('/sales-rep-locations', [MapController::class, 'getSalesRepLocationsData']);

            // POST endpoints for creating/updating data
            Route::post('/visits', [MapController::class, 'createVisit']);
            Route::post('/visits/{id}/complete', [MapController::class, 'completeVisit']);
            Route::post('/update-location', [MapController::class, 'updateCurrentLocation']);

            // Backward compatibility aliases
            Route::post('/create-visit', [MapController::class, 'createVisit']);
            Route::post('/complete-visit/{id}', [MapController::class, 'completeVisit']);
        });
    });

    Route::resource('dashboard-configurator', DashboardConfiguratorController::class)
        ->only(['edit', 'update']);

    Route::get('view-media/{model_id}', [SellController::class, 'viewMedia']);

    //common controller for document & note
    Route::get('get-document-note-page', [DocumentAndNoteController::class, 'getDocAndNoteIndexPage']);
    Route::post('post-document-upload', [DocumentAndNoteController::class, 'postMedia']);
    Route::resource('note-documents', DocumentAndNoteController::class);
    Route::resource('purchase-order', PurchaseOrderController::class);
    Route::get('get-purchase-orders/{contact_id}', [PurchaseOrderController::class, 'getPurchaseOrders']);
    Route::get('get-purchase-order-lines/{purchase_order_id}', [PurchaseController::class, 'getPurchaseOrderLines']);
    Route::get('edit-purchase-orders/{id}/status', [PurchaseOrderController::class, 'getEditPurchaseOrderStatus']);
    Route::put('update-purchase-orders/{id}/status', [PurchaseOrderController::class, 'postEditPurchaseOrderStatus']);
    Route::post('update-purchase-orders-modal/{id}', [PurchaseOrderController::class, 'updatePoModal']);
    Route::resource('sales-order', SalesOrderController::class)->only(['index', 'create']);
    Route::get('get-sales-orders/{customer_id}', [SalesOrderController::class, 'getSalesOrders']);
    Route::get('get-sales-order-lines', [SellPosController::class, 'getSalesOrderLines']);
    Route::get('edit-sales-orders/{id}/status', [SalesOrderController::class, 'getEditSalesOrderStatus']);
    Route::put('update-sales-orders/{id}/status', [SalesOrderController::class, 'postEditSalesOrderStatus']);
    Route::get('reports/activity-log', [ReportController::class, 'activityLog']);
    Route::get('user-location/{latlng}', [HomeController::class, 'getUserLocation']);

    // Release Notes
    Route::get('release-notes', [ReleaseNotesController::class, 'index'])->name('release-notes.index');
});

// Route::middleware(['EcomApi'])->prefix('api/ecom')->group(function () {
//     Route::get('products/{id?}', [ProductController::class, 'getProductsApi']);
//     Route::get('categories', [CategoryController::class, 'getCategoriesApi']);
//     Route::get('brands', [BrandController::class, 'getBrandsApi']);
//     Route::post('customers', [ContactController::class, 'postCustomersApi']);
//     Route::get('settings', [BusinessController::class, 'getEcomSettings']);
//     Route::get('variations', [ProductController::class, 'getVariationsApi']);
//     Route::post('orders', [SellPosController::class, 'placeOrdersApi']);
// });

//common route
Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logouts');
    // Custom Discount Routes
});

Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone'])->group(function () {
    Route::get('/load-more-notifications', [HomeController::class, 'loadMoreNotifications']);
    Route::get('/get-total-unread', [HomeController::class, 'getTotalUnreadNotifications']);
    Route::get('/purchases/print/{id}', [PurchaseController::class, 'printInvoice']);
    Route::get('/purchases/{id}', [PurchaseController::class, 'show']);
    Route::get('/download-purchase-order/{id}/pdf', [PurchaseOrderController::class, 'downloadPdf'])->name('purchaseOrder.downloadPdf');
    Route::get('/download-shipment-label/{id}/pdf', [SellController::class, 'downloadLabelPdf'])->name('shipmentLable.downloadPdf');
    Route::get('/sells/{id}', [SellController::class, 'show']);
    Route::get('/sells/{transaction_id}/print', [SellPosController::class, 'printInvoice'])->name('sell.printInvoice');
    Route::get('/download-sells/{transaction_id}/pdf', [SellPosController::class, 'downloadPdf'])->name('sell.downloadPdf');
    Route::get('/download-quotation/{id}/pdf', [SellPosController::class, 'downloadQuotationPdf'])
        ->name('quotation.downloadPdf');
    Route::get('/download-packing-list/{id}/pdf', [SellPosController::class, 'downloadPackingListPdf'])
        ->name('packing.downloadPdf');
    Route::get('/sells/invoice-url/{id}', [SellPosController::class, 'showInvoiceUrl']);
    Route::get('/show-notification/{id}', [HomeController::class, 'showNotification']);
});

//payment buffer
Route::get('/payments/payable-requested-amount', [PaymentOrderController::class, 'payableRequestedAmount']);
Route::get('/info', [HomeController::class, 'infoPage']);
Route::post('/payment-sell-request', [PaymentOrderController::class, 'paymentSellRequest']);
Route::get('/payment-request', [PaymentOrderController::class, 'getPayment']);

// Simple test route
Route::get('/websocket-simple-test', function () {
    return response()->json(['message' => 'Simple test route working!']);
});

// WebSocket Test Routes
Route::group(['prefix' => 'websocket', 'middleware' => ['web', 'auth']], function () {
    Route::get('/test', [App\Http\Controllers\WebSocketTestController::class, 'showTestPage'])->name('websocket.test');
    Route::post('/test', [App\Http\Controllers\WebSocketTestController::class, 'testWebSocket'])->name('websocket.test.send');
    Route::get('/status', [App\Http\Controllers\WebSocketTestController::class, 'getStatus'])->name('websocket.status');
});

// Laravel WebSockets Dashboard Routes
Route::group(['prefix' => 'laravel-websockets', 'middleware' => ['web', 'auth']], function () {
    Route::get('/', function () {
        return view('websockets.dashboard');
    })->name('websockets.dashboard');
});

// WebSocket Statistics API
Route::group(['prefix' => 'laravel-websockets', 'middleware' => ['web']], function () {
    Route::get('/api/{appId}/statistics', function () {
        return response()->json([
            'statistics' => []
        ]);
    });
});

