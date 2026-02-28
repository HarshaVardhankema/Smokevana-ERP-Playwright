<?php

namespace App\Http\Middleware;

use Menu;
use Closure;
use App\Utils\ModuleUtil;
use App\Http\Controllers\WishlistsController;
use Modules\CustomDashboard\Entities\CustomDashboard;
use App\BusinessLocation;
use Illuminate\Support\Facades\DB;

class AdminSidebarMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->ajax()) {
            return $next($request);
        }

        Menu::create('admin-sidebar-menu', function ($menu) {
            $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];

            $common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];
            $pos_settings = !empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];

            $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
            //Home
            //     $menu->url(action([\App\Http\Controllers\HomeController::class, 'index']), __('home.home'), ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            //     <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            //     <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            //     <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
            //     <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
            //     <path d="M10 12h4v4h-4z"></path>
            //   </svg>', 'active' => request()->segment(1) == 'home'])->order(5);



            $menu->url(action([\App\Http\Controllers\HomeController::class, 'index']), __('home.home'), [
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
            <path d="M10 12h4v4h-4z" />
          </svg>',
                'active' => request()->segment(1) == 'home'
            ])->order(5);

            // Maps
            // $menu->url(
            //     action([\App\Http\Controllers\MapController::class, 'index']), 
            //     'Maps', 
            //     [
            //         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            //             <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            //             <path d="M18 6l0 .01" />
            //             <path d="M18 13l-3.5 -5a4 4 0 1 1 7 0l-3.5 5" />
            //             <path d="M10.5 4.75l-1.5 -.75l-6 3l0 13l6 -3l6 3l6 -3l0 -2" />
            //             <path d="M9 4l0 13" />
            //             <path d="M15 15l0 5" />
            //         </svg>',
            //         'active' => request()->segment(1) == 'maps' || request()->segment(1) == 'map'
            //     ]
            // )->order(7);

            $vendorProfile = \App\Models\WpVendor::where('user_id', auth()->id())->first();
            if ($vendorProfile && auth()->user()->can('dropship.vendor_access')) {
                $menu->dropdown(
                    'My Vendor Portal',
                    function ($sub) {
                        // Dashboard - always show if they have vendor access
                        $sub->url(
                            route('vendor.dashboard'),
                            'Dashboard',
                            ['icon' => '', 'active' => request()->routeIs('vendor.dashboard*')]
                        );

                        // Orders - check for view/manage orders permission
                        if (auth()->user()->can('dropship.view_orders') || auth()->user()->can('dropship.manage_orders')) {
                            $sub->url(
                                route('vendor.orders'),
                                'My Orders',
                                ['icon' => '', 'active' => request()->routeIs('vendor.orders*')]
                            );
                        }

                        // Products - check for view/manage products permission
                        if (auth()->user()->can('dropship.view_products') || auth()->user()->can('dropship.manage_products')) {
                            $sub->url(
                                route('vendor.products'),
                                'My Products',
                                ['icon' => '', 'active' => request()->routeIs('vendor.products')]
                            );
                        }

                        // Earnings - check for view earnings permission
                        if (auth()->user()->can('dropship.view_earnings')) {
                            $sub->url(
                                route('vendor.earnings'),
                                'My Earnings',
                                ['icon' => '', 'active' => request()->routeIs('vendor.earnings')]
                            );
                        }
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M3 21l18 0"></path>
                    <path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4"></path>
                    <path d="M5 21l0 -10.15"></path>
                    <path d="M19 21l0 -10.15"></path>
                    <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4"></path>
                  </svg>'
                    ]
                )->order(6);
            }


            //User management dropdown
            if (auth()->user()->can('user.view') || auth()->user()->can('user.create') || auth()->user()->can('roles.view')) {
                $menu->dropdown(
                    __('user.user_management'),
                    function ($sub) {
                        if (auth()->user()->can('user.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ManageUserController::class, 'index']),
                                __('user.users'),
                                ['icon' => '', 'active' => request()->segment(1) == 'users']
                            );
                        }
                        if (auth()->user()->can('roles.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\RoleController::class, 'index']),
                                __('user.roles'),
                                ['icon' => '', 'active' => request()->segment(1) == 'roles']
                            );
                        }
                        if (auth()->user()->can('user.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\SalesCommissionAgentController::class, 'index']),
                                __('lang_v1.sales_commission_agents'),
                                ['icon' => '', 'active' => request()->segment(1) == 'sales-commission-agents']
                            );
                        }
                        // if (auth()->user()->can('lead.view')) {
                        //     $sub->url(
                        //         action([\App\Http\Controllers\LeadController::class, 'index']),
                        //         __('lang_v1.leads'),
                        //         ['icon' => '', 'active' => request()->segment(1) == 'leads']
                        //     );
                        // }
                        // if (auth()->user()->can('user.view')) {
                        //     $sub->url(
                        //         action([\App\Http\Controllers\TicketController::class, 'index']),
                        //         'Tickets',
                        //         ['icon' => '', 'active' => request()->segment(1) == 'tickets']
                        //     );
                        // }
                        // if (auth()->user()->can('user.view')) {
                        //     $sub->url(
                        //         route('visit-history.index'),
                        //         'Visit History',
                        //         ['icon' => '', 'active' => request()->segment(1) == 'visit-history']
                        //     );
                        // }
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                  </svg>',
                    ]
                )->order(10);
            }

            //Contacts dropdown
            if (auth()->user()->can('supplier.view') || auth()->user()->can('customer.view') || auth()->user()->can('supplier.view_own') || auth()->user()->can('customer.view_own')) {
                $menu->dropdown(
                    'Customer Care',
                    function ($sub) use ($enabled_modules, $common_settings) {

                        if (auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own')) {
                            $sub->url(
                                action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer']),
                                __('report.customer'),
                                ['icon' => '', 'active' => request()->input('type') == 'customer']
                            );
                        }
                        if (auth()->user()->can('view_customer_group')) {
                            $sub->url(
                                action([\App\Http\Controllers\CustomerGroupController::class, 'index']),
                                __('lang_v1.customer_groups'),
                                ['icon' => '', 'active' => request()->segment(1) == 'customer-group']
                            );
                        }

                        if (auth()->user()->can('supplier.create') || auth()->user()->can('customer.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\ContactController::class, 'getImportContacts']),
                                __('lang_v1.import_contacts'),
                                ['icon' => '', 'active' => request()->segment(1) == 'contacts' && request()->segment(2) == 'import']
                            );
                        }
                        // Credit Limit Management - Only for B2B login
                        if (auth()->user()->can('customer.view')) {
                            // Check if user has access to any B2B location
                            $user = auth()->user();
                            $business_id = session('business.id');
                            $permitted_locations = $user->permitted_locations($business_id);

                            $has_b2b_access = false;
                            if ($permitted_locations == 'all') {
                                // User has access to all locations, check if any location is B2B
                                $has_b2b_access = BusinessLocation::where('business_id', $business_id)
                                    ->where('is_b2c', 0)
                                    ->exists();
                            } elseif (is_array($permitted_locations) && !empty($permitted_locations)) {
                                // User has specific location permissions, check if any is B2B
                                $has_b2b_access = BusinessLocation::whereIn('id', $permitted_locations)
                                    ->where('is_b2c', 0)
                                    ->exists();
                            }

                            if ($has_b2b_access && in_array('credit_line', $enabled_modules)) {
                                $sub->url(
                                    action([\App\Http\Controllers\CreditLineController::class, 'index']),
                                    'Credit Line Approval form',
                                    ['icon' => '', 'active' => request()->segment(1) == 'credit-lines']
                                );
                            }
                        }

                        if (in_array('complaints', $enabled_modules) && (auth()->user()->can('complaint.view') || auth()->user()->can('complaint.create'))) {
                            $sub->url(
                                action([\App\Http\Controllers\ComplaintController::class, 'index']),
                                'Complaints',
                                ['icon' => '', 'active' => request()->segment(1) == 'complaints']
                            );
                        }

                        // if (!empty(env('GOOGLE_MAP_API_KEY'))) {
                        // $sub->url(
                        //     action([\App\Http\Controllers\ContactController::class, 'contactMap']),
                        //     __('lang_v1.map'),
                        //     ['icon' => 'fa fas fa-map-marker-alt', 'active' => request()->segment(1) == 'contacts' && request()->segment(2) == 'map']
                        // );
                        // }
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M20 6v12a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2z"></path>
                    <path d="M10 16h6"></path>
                    <path d="M13 11m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                    <path d="M4 8h3"></path>
                    <path d="M4 12h3"></path>
                    <path d="M4 16h3"></path>
                  </svg>',
                        'id' => 'tour_step4'
                    ]
                )->order(15);
            }

            //Products dropdown
            if (
                auth()->user()->can('product.view') || auth()->user()->can('product.create') ||
                auth()->user()->can('brand.view') || auth()->user()->can('unit.view') ||
                auth()->user()->can('category.view') || auth()->user()->can('brand.create') ||
                auth()->user()->can('unit.create') || auth()->user()->can('category.create')
            ) {
                $menu->dropdown(
                    __('sale.products'),
                    function ($sub) {
                        if (auth()->user()->can('product.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ProductController::class, 'index']),
                                __('lang_v1.list_products'),
                                ['icon' => '', 'active' => request()->segment(1) == 'products' && request()->segment(2) == '']
                            );
                        }

                        if (auth()->user()->can('product.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\ProductController::class, 'create']),
                                __('product.add_product'),
                                ['icon' => '', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'create']
                            );
                        }
                        // if (auth()->user()->can('product.create')) { // erp disable
                        //     $sub->url(
                        //         action([\App\Http\Controllers\SellingPriceGroupController::class, 'updateProductPrice']),
                        //         __('lang_v1.update_product_price'),
                        //         ['icon' => '', 'active' => request()->segment(1) == 'update-product-price']
                        //     );
                        // }
                        if (auth()->user()->can('product.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\LabelsController::class, 'show']),
                                __('barcode.print_labels'),
                                ['icon' => '', 'active' => request()->segment(1) == 'labels' && request()->segment(2) == 'show']
                            );
                        }
                        if (auth()->user()->can('product.create')) {
                            // $sub->url(
                            //     action([\App\Http\Controllers\VariationTemplateController::class, 'index']),
                            //     __('product.variations'),
                            //     ['icon' => '', 'active' => request()->segment(1) == 'variation-templates']
                            // );
                            $sub->url(
                                action([\App\Http\Controllers\ImportProductsController::class, 'index']),
                                __('product.import_products'),
                                ['icon' => '', 'active' => request()->segment(1) == 'import-products']
                            );
                        }
                        if (auth()->user()->can('product.update')) {
                            $sub->url(
                                action([\App\Http\Controllers\ProductController::class, 'editSellingPrice']),
                                __('Edit Selling Price'),
                                ['icon' => '', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'edit-selling-price']
                            );
                        }
                        if (auth()->user()->can('product.update')) {
                            $sub->url(
                                action([\App\Http\Controllers\ProductController::class, 'addStock']),
                                __('Manage Stock'),
                                ['icon' => '', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'add-stock']
                            );
                        }
                        if (auth()->user()->can('product.opening_stock')) {
                            // $sub->url(
                            //     action([\App\Http\Controllers\ImportOpeningStockController::class, 'index']),
                            //     __('lang_v1.import_opening_stock'),
                            //     ['icon' => '', 'active' => request()->segment(1) == 'import-opening-stock']
                            // );
                        }
                        // if (auth()->user()->can('product.create')) {
                        //     $sub->url(
                        //         action([\App\Http\Controllers\SellingPriceGroupController::class, 'index']),
                        //         __('lang_v1.selling_price_group'),
                        //         ['icon' => '', 'active' => request()->segment(1) == 'selling-price-group']
                        //     );
                        // }
                        // if (auth()->user()->can('unit.view') || auth()->user()->can('unit.create')) {
                        //     $sub->url(
                        //         action([\App\Http\Controllers\UnitController::class, 'index']),
                        //         __('unit.units'),
                        //         ['icon' => '', 'active' => request()->segment(1) == 'units']
                        //     );
                        // }
                        if (auth()->user()->can('product.update')) {
                            $sub->url(
                                action([\App\Http\Controllers\ProductController::class, 'getAllProductOrderLimit']),
                                __('Sale Limit Control'),
                                ['icon' => '', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'sale-limit-control']
                            );
                        }
                        if (auth()->user()->can('category.view') || auth()->user()->can('category.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=product',
                                __('category.categories'),
                                ['icon' => '', 'active' => request()->segment(1) == 'taxonomies' && request()->get('type') == 'product']
                            );
                        }
                        if (auth()->user()->can('brand.view') || auth()->user()->can('brand.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\BrandController::class, 'index']),
                                __('brand.brands'),
                                ['icon' => '', 'active' => request()->segment(1) == 'brands']
                            );
                        }

                        // $sub->url(
                        //     action([\App\Http\Controllers\WarrantyController::class, 'index']),
                        //     __('lang_v1.warranties'),
                        //     ['icon' => '', 'active' => request()->segment(1) == 'warranties']
                        // );
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5"></path>
                    <path d="M12 12l8 -4.5"></path>
                    <path d="M8.2 9.8l7.6 -4.6"></path>
                    <path d="M12 12v9"></path>
                    <path d="M12 12l-8 -4.5"></path>
                  </svg>',
                        'id' => 'tour_step5'
                    ]
                )->order(20);
            }

            //Purchase dropdown
            if (in_array('purchases', $enabled_modules) && (auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create') || auth()->user()->can('purchase.update'))) {
                $menu->dropdown(
                    'Vendor Care',
                    function ($sub) use ($common_settings) {
                        if (auth()->user()->can('supplier.view') || auth()->user()->can('supplier.view_own')) {
                            $sub->url(
                                action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => 'supplier']),
                                "Vendor",
                                ['icon' => '', 'active' => request()->input('type') == 'supplier']
                            );
                        }
                        if (!empty($common_settings['enable_purchase_requisition']) && (auth()->user()->can('purchase_requisition.view_all') || auth()->user()->can('purchase_requisition.view_own'))) {
                            $sub->url(
                                action([\App\Http\Controllers\PurchaseRequisitionController::class, 'index']),
                                __('lang_v1.purchase_requisition'),
                                ['icon' => '', 'active' => request()->segment(1) == 'purchase-requisition']
                            );
                        }

                        if (!empty($common_settings['enable_purchase_order']) && (auth()->user()->can('purchase_order.view_all') || auth()->user()->can('purchase_order.view_own'))) {
                            $sub->url(
                                action([\App\Http\Controllers\PurchaseOrderController::class, 'index']),
                                __('lang_v1.purchase_order'),
                                ['icon' => '', 'active' => request()->segment(1) == 'purchase-order']
                            );
                        }
                        if (auth()->user()->can('purchase.view') || auth()->user()->can('view_own_purchase')) {
                            $sub->url(
                                action([\App\Http\Controllers\PurchaseController::class, 'index']),
                                __('purchase.list_purchase'),
                                ['icon' => '', 'active' => request()->segment(1) == 'purchases' && request()->segment(2) == null]
                            );
                        }
                        if (auth()->user()->can('purchase.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\PurchaseController::class, 'create']),
                                __('purchase.add_purchase'),
                                ['icon' => '', 'active' => request()->segment(1) == 'purchases' && request()->segment(2) == 'create']
                            );
                        }
                        if (auth()->user()->can('purchase.update')) {
                            $sub->url(
                                action([\App\Http\Controllers\PurchaseReturnController::class, 'index']),
                                __('lang_v1.list_purchase_return'),
                                ['icon' => '', 'active' => request()->segment(1) == 'purchase-return']
                            );
                        }

                        // Vendor Product Requests
                        if (auth()->user()->can('dropship.manage_vendors') && \Route::has('dropship.product-requests.index')) {
                            $business_id = session('business.id');
                            $pendingProductRequestsCount = DB::table('vendor_product_requests')
                                ->count();
                            session(['dropship.pending_product_requests_count' => $pendingProductRequestsCount]);
                            $productRequestsLabel = 'Product Requests';
                            $sub->url(
                                route('dropship.product-requests.index'),
                                $productRequestsLabel,
                                ['icon' => '', 'active' => request()->routeIs('dropship.product-requests.*')]
                            );
                        }
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 3v12"></path>
                    <path d="M16 11l-4 4l-4 -4"></path>
                    <path d="M3 12a9 9 0 0 0 18 0"></path>
                  </svg>',
                        'id' => 'tour_step6'
                    ]
                )->order(20);
            }
            //Sell dropdown
            if ($is_admin || auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'access_sell_return', 'direct_sell.view', 'direct_sell.update', 'access_own_sell_return'])) {
                $menu->dropdown(
                    __('sale.sale'),
                    function ($sub) use ($enabled_modules, $is_admin, $pos_settings) {
                        if (!empty($pos_settings['enable_sales_order']) && ($is_admin || auth()->user()->hasAnyPermission(['so.view_own', 'so.view_all', 'so.create']))) {
                            $sub->url(
                                '/order-fulfillment',
                                'Manage Order',
                                ['icon' => '', 'active' => request()->segment(1) == 'order-fulfillment']
                            );
                        }

                        // WooCommerce Order
                        if (in_array('woocommerce', $enabled_modules) && !empty($pos_settings['enable_sales_order']) && ($is_admin || auth()->user()->hasAnyPermission(['so.view_own', 'so.view_all', 'so.create']))) {
                            $sub->url(
                                action([\App\Http\Controllers\OrderfulfillmentController::class, 'woocommerceOrders']),
                                'WooCommerce Orders',
                                ['icon' => '', 'active' => request()->segment(1) == 'woocommerce-orders']
                            );
                        }

                        if (!empty($pos_settings['enable_sales_order']) && ($is_admin || auth()->user()->hasAnyPermission(['so.view_own', 'so.view_all', 'so.create']))) {
                            $sub->url(
                                // '/order-fulfillment',
                                action([\App\Http\Controllers\SalesOrderController::class, 'index']),
                                // __('lang_v1.sales_order'),
                                'Sales Order (SO)',
                                // 'Manage Order',
                                [
                                    'icon' => '',
                                    'active' => (request()->segment(1) == 'sales-order' || (request()->segment(1) == 'sells' && request()->segment(2) == 'create' &&
                                        request()->get('sale_type') == 'sales_order'))
                                ]
                            );
                        }

                        if ($is_admin || auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
                            $sub->url(
                                action([\App\Http\Controllers\SellController::class, 'index']),
                                // __('lang_v1.all_sales'),
                                'Sales Invoice (SI)',
                                ['icon' => '', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == null]
                            );
                        }
                        if (in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
                            $sub->url(
                                action([\App\Http\Controllers\SellController::class, 'create']),
                                // __('sale.add_sale'),
                                "Add Sale Invoice (SI)",
                                [
                                    'icon' => '',
                                    'active' => request()->segment(1) == 'sells' && request()->segment(2) == 'create' &&
                                        empty(request()->get('status')) &&
                                        empty(request()->get('sale_type'))
                                ]
                            );
                        }
                        // if (auth()->user()->can('sell.create')) {
                        //     if (in_array('pos_sale', $enabled_modules)) {
                        //         if (auth()->user()->can('sell.view')) {
                        //             $sub->url(
                        //                 action([\App\Http\Controllers\SellPosController::class, 'index']),
                        //                 __('sale.list_pos'),
                        //                 ['icon' => '', 'active' => request()->segment(1) == 'pos' && request()->segment(2) == null]
                        //             );
                        //         }
    
                        //         $sub->url(
                        //             action([\App\Http\Controllers\SellPosController::class, 'create']),
                        //             __('sale.pos_sale'),
                        //             ['icon' => '', 'active' => request()->segment(1) == 'pos' && request()->segment(2) == 'create']
                        //         );
                        //     }
                        // }
    
                        // if (in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
                        //     $sub->url(
                        //         action([\App\Http\Controllers\SellController::class, 'create'], ['status' => 'draft']),
                        //         __('lang_v1.add_draft'),
                        //         ['icon' => '', 'active' => request()->get('status') == 'draft']
                        //     );
                        // }
                        // if (in_array('add_sale', $enabled_modules) && ($is_admin || auth()->user()->hasAnyPermission(['draft.view_all', 'draft.view_own']))) {
                        //     $sub->url(
                        //         action([\App\Http\Controllers\SellController::class, 'getDrafts']),
                        //         __('lang_v1.list_drafts'),
                        //         ['icon' => '', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == 'drafts']
                        //     );
                        // }
                        // if (in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
                        //     $sub->url(
                        //         action([\App\Http\Controllers\SellController::class, 'create'], ['status' => 'quotation']),
                        //         __('lang_v1.add_quotation'),
                        //         ['icon' => '', 'active' => request()->get('status') == 'quotation']
                        //     );
                        // }
                        // if (in_array('add_sale', $enabled_modules) && ($is_admin || auth()->user()->hasAnyPermission(['quotation.view_all', 'quotation.view_own']))) {
                        //     $sub->url(
                        //         action([\App\Http\Controllers\SellController::class, 'getQuotations']),
                        //         __('lang_v1.list_quotations'),
                        //         ['icon' => '', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == 'quotations']
                        //     );
                        // }
    
                        if (auth()->user()->can('access_sell_return') || auth()->user()->can('access_own_sell_return')) {
                            $sub->url(
                                action([\App\Http\Controllers\SellReturnController::class, 'index']),
                                __('lang_v1.list_sell_return') . '(CN)',
                                ['icon' => '', 'active' => request()->segment(1) == 'sell-return' && request()->segment(2) == null]
                            );
                        }
                        if (auth()->user()->can('access_sell_return') || auth()->user()->can('access_own_sell_return')) {
                            $sub->url(
                                action([\App\Http\Controllers\SellReturnController::class, 'indexEcom']),
                                'Sell Return Ecom (EC)',
                                ['icon' => '', 'active' => request()->segment(1) == 'sell-return-ecom' && request()->segment(2) == null]
                            );
                        }

                        // if ($is_admin || auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
                        //     $sub->url(
                        //         action([\App\Http\Controllers\SellController::class, 'shipments']),
                        //         __('lang_v1.shipments') ,
                        //         ['icon' => '', 'active' => request()->segment(1) == 'shipments']
                        //     );
                        // }
                        // if ($is_admin || auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
                        //     try {
                        //         $shipping_stations_url = url('/shipping-stations');
                        //     } catch (\Exception $e) {
                        //         $shipping_stations_url = '#';
                        //     }
                        //     $sub->url(
                        //         $shipping_stations_url,
                        //         __('Shipping Stations') ,
                        //         ['icon' => '', 'active' => request()->segment(1) == 'shipping-stations']
                        //     );
                        // }
    
                        // if (auth()->user()->can('discount.access')) {
                        //     $sub->url(
                        //         action([\App\Http\Controllers\DiscountController::class, 'index']),
                        //         __('lang_v1.discounts'),
                        //         ['icon' => '', 'active' => request()->segment(1) == 'discount']
                        //     );
                        // }
                        if (in_array('subscription', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
                            $sub->url(
                                action([\App\Http\Controllers\SellPosController::class, 'listSubscriptions']),
                                __('lang_v1.subscriptions'),
                                ['icon' => '', 'active' => request()->segment(1) == 'subscriptions']
                            );
                        }

                        // if (auth()->user()->can('sell.create')) {
                        //     $sub->url(
                        //         action([\App\Http\Controllers\ImportSalesController::class, 'index']),
                        //         __('lang_v1.import_sales'),
                        //         ['icon' => '', 'active' => request()->segment(1) == 'import-sales']
                        //     );
                        // }
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 15v-12"></path>
                    <path d="M16 7l-4 -4l-4 4"></path>
                    <path d="M3 12a9 9 0 0 0 18 0"></path>
                  </svg>',
                        'id' => 'tour_step7'
                    ]
                )->order(12);
            }

            // Prime Subscriptions (Module: Subscription)
            // Shows only when prime_subscription module is enabled and user has permission.
            if (in_array('prime_subscription', $enabled_modules) && auth()->user()->can('subscription.view')) {
                $menu->dropdown(
                    'Prime Subscriptions',
                    function ($sub) {
                        $sub->url(
                            url('subscription'),
                            'Dashboard',
                            ['icon' => '', 'active' => request()->segment(1) == 'subscription' && request()->segment(2) == null]
                        );
                        $sub->url(
                            url('subscription/plans'),
                            'Subscription Plans',
                            ['icon' => '', 'active' => request()->segment(1) == 'subscription' && request()->segment(2) == 'plans']
                        );
                        $sub->url(
                            url('subscription/subscriptions/create'),
                            'Add Subscription',
                            ['icon' => '', 'active' => request()->segment(1) == 'subscription' && request()->segment(2) == 'subscriptions' && request()->segment(3) == 'create']
                        );
                        $sub->url(
                            url('subscription/prime-products'),
                            'Prime Products',
                            ['icon' => '', 'active' => request()->segment(1) == 'subscription' && request()->segment(2) == 'prime-products']
                        );
                        $sub->url(
                            url('subscription/invoices'),
                            'Invoices',
                            ['icon' => '', 'active' => request()->segment(1) == 'subscription' && request()->segment(2) == 'invoices']
                        );
                        $sub->url(
                            url('subscription/reports'),
                            'Reports',
                            ['icon' => '', 'active' => request()->segment(1) == 'subscription' && request()->segment(2) == 'reports']
                        );
                        $sub->url(
                            url('subscription/settings'),
                            'Settings',
                            ['icon' => '', 'active' => request()->segment(1) == 'subscription' && request()->segment(2) == 'settings']
                        );
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 2l3 7h7l-5.5 4.2l2.1 7.8l-6.6-4.6l-6.6 4.6l2.1-7.8l-5.5-4.2h7z"></path>
                  </svg>',
                        'active' => request()->segment(1) == 'subscription'
                    ]
                )->order(31);
            }
            // Dropshipping – visible only to Admin (not B2B or B2C)
            if ($is_admin) {
                $menu->dropdown(
                    'Dropshipping',
                    function ($sub) use ($is_admin, $enabled_modules) {
                        // Dashboard
                        $sub->url(
                            route('dropship.dashboard'),
                            'Dashboard',
                            ['icon' => '', 'active' => request()->routeIs('dropship.dashboard')]
                        );

                        // Dropship Orders
                        $sub->url(
                            route('dropship.orders.index'),
                            'Dropship Orders',
                            ['icon' => '', 'active' => request()->routeIs('dropship.orders.*')]
                        );

                        // Vendor Management
                        $sub->url(
                            route('dropship.vendors.index'),
                            'Manage Vendors',
                            ['icon' => '', 'active' => request()->routeIs('dropship.vendors.*')]
                        );

                        // Add New Vendor
                        $sub->url(
                            route('dropship.vendors.create'),
                            'Add Vendor',
                            ['icon' => '', 'active' => request()->routeIs('dropship.vendors.create')]
                        );

                        // Sync Products to WooCommerce - Only show when woocommerce module is enabled
                        if (in_array('woocommerce', $enabled_modules)) {
                            $sub->url(
                                '/woocommerce/sync-to-woocommerce',
                                'Sync to WooCommerce',
                                ['icon' => '', 'active' => request()->is('woocommerce/sync-to-woocommerce')]
                            );
                        }
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M3 21v-4a4 4 0 0 1 4 -4h4"></path>
                    <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                    <path d="M5 11v-6a2 2 0 0 1 2 -2h7l5 5v12a2 2 0 0 1 -2 2h-5"></path>
                    <path d="M17 17l-4 4"></path>
                    <path d="M13 17h4v4"></path>
                  </svg>'
                    ]
                )->order(31);
            }


            //Stock transfer dropdown
            if (in_array('stock_transfers', $enabled_modules) && (auth()->user()->can('stock.view') || auth()->user()->can('stock.create') || auth()->user()->can('view_own_stock'))) {
                $menu->dropdown(
                    __('lang_v1.stock_transfers'),
                    function ($sub) {
                        if (auth()->user()->can('stock.view') || auth()->user()->can('view_own_stock')) {
                            $sub->url(
                                route('stock-transfers.index'),
                                __('lang_v1.list_stock_transfers'),
                                ['icon' => '', 'active' => request()->routeIs('stock-transfers.index')]
                            );
                        }
                        if (auth()->user()->can('stock.create')) {
                            $sub->url(
                                route('stock-transfers.create'),
                                __('lang_v1.add_stock_transfer'),
                                ['icon' => '', 'active' => request()->routeIs('stock-transfers.create')]
                            );
                        }
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                    <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                    <path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"></path>
                    <path d="M3 9l4 0"></path>
                  </svg>'
                    ]
                )->order(35);
            }

            //stock adjustment dropdown
            if (in_array('stock_adjustment', $enabled_modules) && (auth()->user()->can('stock.view') || auth()->user()->can('stock.create') || auth()->user()->can('view_own_stock'))) {
                $menu->dropdown(
                    __('stock_adjustment.stock_adjustment'),
                    function ($sub) {
                        if (auth()->user()->can('stock.view') || auth()->user()->can('view_own_stock')) {
                            $sub->url(
                                action([\App\Http\Controllers\StockAdjustmentController::class, 'index']),
                                __('stock_adjustment.list'),
                                ['icon' => '', 'active' => request()->segment(1) == 'stock-adjustments' && request()->segment(2) == null]
                            );
                        }
                        if (auth()->user()->can('stock.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\StockAdjustmentController::class, 'create']),
                                __('stock_adjustment.add'),
                                ['icon' => '', 'active' => request()->segment(1) == 'stock-adjustments' && request()->segment(2) == 'create']
                            );
                        }
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0"></path>
                    <path d="M4 6v6a8 3 0 0 0 16 0v-6"></path>
                    <path d="M4 12v6a8 3 0 0 0 16 0v-6"></path>
                  </svg>'
                    ]
                )->order(40);
            }

            //Expense dropdown
            if (in_array('expenses', $enabled_modules) && (auth()->user()->can('all_expense.access') || auth()->user()->can('view_own_expense'))) {
                $menu->dropdown(
                    __('expense.expenses'),
                    function ($sub) {
                        $sub->url(
                            action([\App\Http\Controllers\ExpenseController::class, 'index']),
                            __('lang_v1.list_expenses'),
                            ['icon' => '', 'active' => request()->segment(1) == 'expenses' && request()->segment(2) == null]
                        );

                        if (auth()->user()->can('expense.add')) {
                            $sub->url(
                                action([\App\Http\Controllers\ExpenseController::class, 'create']),
                                __('expense.add_expense'),
                                ['icon' => '', 'active' => request()->segment(1) == 'expenses' && request()->segment(2) == 'create']
                            );
                        }

                        if (auth()->user()->can('expense.add') || auth()->user()->can('expense.edit')) {
                            $sub->url(
                                action([\App\Http\Controllers\ExpenseCategoryController::class, 'index']),
                                __('expense.expense_categories'),
                                ['icon' => '', 'active' => request()->segment(1) == 'expense-categories']
                            );
                        }
                    },
                    [
                        'icon' => ' <svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2"></path>
                    <path d="M14.8 8a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1"></path>
                    <path d="M12 6v10"></path>
                  </svg>'
                    ]
                )->order(45);
            }

            // Gift Cards (show for business admin)
            // if ($is_admin) {
            //     $menu->url(
            //         action([\App\Http\Controllers\GiftCardAdminController::class, 'index']),
            //         __('Gift Cards'),
            //         [
            //             'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            //         <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            //         <path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z"></path>
            //         <path d="M7 9l0 .01"></path>
            //         <path d="M17 9l0 .01"></path>
            //         <path d="M10 12h4"></path>
            //         <path d="M10 15h4"></path>
            //       </svg>',
            //             'active' => request()->segment(1) == 'gift-cards'
            //         ]
            //     )->order(46);
            // }

            //Accounts dropdown
            if (auth()->user()->can('account.access') && in_array('account', $enabled_modules)) {
                $menu->dropdown(
                    __('lang_v1.payment_accounts'),
                    function ($sub) {
                        $sub->url(
                            action([\App\Http\Controllers\AccountController::class, 'index']),
                            __('account.list_accounts'),
                            ['icon' => '', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'account']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\AccountReportsController::class, 'balanceSheet']),
                            __('account.balance_sheet'),
                            ['icon' => '', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'balance-sheet']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\AccountReportsController::class, 'trialBalance']),
                            __('account.trial_balance'),
                            ['icon' => '', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'trial-balance']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\AccountController::class, 'cashFlow']),
                            __('lang_v1.cash_flow'),
                            ['icon' => '', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'cash-flow']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\AccountReportsController::class, 'paymentAccountReport']),
                            __('account.payment_account_report'),
                            ['icon' => '', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'payment-account-report']
                        );
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z"></path>
                    <path d="M3 10l18 0"></path>
                    <path d="M7 15l.01 0"></path>
                    <path d="M11 15l2 0"></path>
                  </svg>'
                    ]
                )->order(50);
            }

            // Bookkeeping dropdown
            if (auth()->user()->can('all_expense.access')) {
                $menu->dropdown(
                    'Bookkeeping',
                    function ($sub) {
                        $sub->url(
                            action([\App\Http\Controllers\BookkeepingController::class, 'dashboard']),
                            'Dashboard',
                            ['icon' => '', 'active' => request()->segment(1) == 'bookkeeping' && request()->segment(2) == null]
                        );
                        $sub->url(
                            action([\App\Http\Controllers\BookkeepingController::class, 'chartOfAccounts']),
                            'Chart of Accounts',
                            ['icon' => '', 'active' => request()->segment(1) == 'bookkeeping' && request()->segment(2) == 'chart-of-accounts']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\BookkeepingController::class, 'journalEntries']),
                            'Journal Entries',
                            ['icon' => '', 'active' => request()->segment(1) == 'bookkeeping' && request()->segment(2) == 'journal-entries']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\BookkeepingController::class, 'bankDeposits']),
                            'Bank Deposits',
                            ['icon' => '', 'active' => request()->segment(1) == 'bookkeeping' && request()->segment(2) == 'bank-deposits']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\BookkeepingController::class, 'liabilities']),
                            'Liabilities',
                            ['icon' => '', 'active' => request()->segment(1) == 'bookkeeping' && request()->segment(2) == 'liabilities']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\BookkeepingController::class, 'partnerTransactions']),
                            'Partner Transactions',
                            ['icon' => '', 'active' => request()->segment(1) == 'bookkeeping' && request()->segment(2) == 'partner-transactions']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\BookkeepingController::class, 'inventoryValuation']),
                            'Inventory Valuation',
                            ['icon' => '', 'active' => request()->segment(1) == 'bookkeeping' && request()->segment(2) == 'inventory-valuation']
                        );
                        $sub->url(
                            url('/bookkeeping/pl-transactions'),
                            'P&L Transactions',
                            ['icon' => '', 'active' => request()->segment(1) == 'bookkeeping' && request()->segment(2) == 'pl-transactions']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\BookkeepingController::class, 'trialBalance']),
                            'Trial Balance',
                            ['icon' => '', 'active' => request()->segment(1) == 'bookkeeping' && request()->segment(2) == 'reports' && request()->segment(3) == 'trial-balance']
                        );
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M4 19l16 0"></path>
                    <path d="M4 15l4 -6l4 2l4 -5l4 4"></path>
                  </svg>'
                    ]
                )->order(52);
            }

            //Reports dropdown
            if (
                auth()->user()->can('purchase_n_sell_report.view') || auth()->user()->can('contacts_report.view')
                || auth()->user()->can('stock_report.view') || auth()->user()->can('tax_report.view')
                || auth()->user()->can('trending_product_report.view') || auth()->user()->can('sales_representative.view') || auth()->user()->can('register_report.view')
                || auth()->user()->can('expense_report.view')
            ) {
                $menu->dropdown(
                    __('report.reports'),
                    function ($sub) use ($enabled_modules, $is_admin) {
                        if (auth()->user()->can('profit_loss_report.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getProfitLoss']),
                                __('report.profit_loss'),
                                ['icon' => '', 'active' => request()->segment(2) == 'profit-loss']
                            );
                        }
                        if (config('constants.show_report_606') == true) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'purchaseReport']),
                                'Report 606 (' . __('lang_v1.purchase') . ')',
                                ['icon' => '', 'active' => request()->segment(2) == 'purchase-report']
                            );
                        }
                        if (config('constants.show_report_607') == true) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'saleReport']),
                                'Report 607 (' . __('business.sale') . ')',
                                ['icon' => '', 'active' => request()->segment(2) == 'sale-report']
                            );
                        }
                        if ((in_array('purchases', $enabled_modules) || in_array('add_sale', $enabled_modules) || in_array('pos_sale', $enabled_modules)) && auth()->user()->can('purchase_n_sell_report.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getPurchaseSell']),
                                __('report.purchase_sell_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'purchase-sell']
                            );
                        }

                        if (auth()->user()->can('tax_report.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getTaxReport']),
                                __('report.tax_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'tax-report']
                            );
                        }
                        if (auth()->user()->can('contacts_report.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getCustomerSuppliers']),
                                __('report.contacts'),
                                ['icon' => '', 'active' => request()->segment(2) == 'customer-supplier']
                            );
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getCustomerGroup']),
                                __('lang_v1.customer_groups_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'customer-group']
                            );
                        }
                        if (auth()->user()->can('stock_report.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getStockReport']),
                                __('report.stock_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'stock-report']
                            );
                            if (session('business.enable_product_expiry') == 1) {
                                $sub->url(
                                    action([\App\Http\Controllers\ReportController::class, 'getStockExpiryReport']),
                                    __('report.stock_expiry_report'),
                                    ['icon' => '', 'active' => request()->segment(2) == 'stock-expiry']
                                );
                            }
                            if (session('business.enable_lot_number') == 1) {
                                $sub->url(
                                    action([\App\Http\Controllers\ReportController::class, 'getLotReport']),
                                    __('lang_v1.lot_report'),
                                    ['icon' => '', 'active' => request()->segment(2) == 'lot-report']
                                );
                            }

                            if (in_array('stock_adjustment', $enabled_modules)) {
                                $sub->url(
                                    action([\App\Http\Controllers\ReportController::class, 'getStockAdjustmentReport']),
                                    __('report.stock_adjustment_report'),
                                    ['icon' => '', 'active' => request()->segment(2) == 'stock-adjustment-report']
                                );
                            }

                        }

                        if (auth()->user()->can('trending_product_report.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getTrendingProducts']),
                                __('report.trending_products'),
                                ['icon' => '', 'active' => request()->segment(2) == 'trending-products']
                            );
                        }

                        if (auth()->user()->can('purchase_n_sell_report.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'itemsReport']),
                                __('lang_v1.items_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'items-report']
                            );

                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getproductPurchaseReport']),
                                __('lang_v1.product_purchase_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'product-purchase-report']
                            );

                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getproductSellReport']),
                                __('lang_v1.product_sell_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'product-sell-report']
                            );

                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'purchasePaymentReport']),
                                __('lang_v1.purchase_payment_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'purchase-payment-report']
                            );

                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'sellPaymentReport']),
                                __('lang_v1.sell_payment_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'sell-payment-report']
                            );
                        }
                        if (in_array('expenses', $enabled_modules) && auth()->user()->can('expense_report.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getExpenseReport']),
                                __('report.expense_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'expense-report']
                            );
                        }
                        if (auth()->user()->can('register_report.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getRegisterReport']),
                                __('report.register_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'register-report']
                            );
                        }
                        if (auth()->user()->can('sales_representative.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getSalesRepresentativeReport']),
                                __('report.sales_representative'),
                                ['icon' => '', 'active' => request()->segment(2) == 'sales-representative-report']
                            );
                        }
                        if (auth()->user()->can('purchase_n_sell_report.view') && in_array('tables', $enabled_modules)) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getTableReport']),
                                __('restaurant.table_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'table-report']
                            );
                        }

                        if (auth()->user()->can('tax_report.view') && !empty(config('constants.enable_gst_report_india'))) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'gstSalesReport']),
                                __('lang_v1.gst_sales_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'gst-sales-report']
                            );

                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'gstPurchaseReport']),
                                __('lang_v1.gst_purchase_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'gst-purchase-report']
                            );
                        }

                        if (auth()->user()->can('sales_representative.view') && in_array('service_staff', $enabled_modules)) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'getServiceStaffReport']),
                                __('restaurant.service_staff_report'),
                                ['icon' => '', 'active' => request()->segment(2) == 'service-staff-report']
                            );
                        }

                        if ($is_admin) {
                            $sub->url(
                                action([\App\Http\Controllers\ReportController::class, 'activityLog']),
                                __('lang_v1.activity_log'),
                                ['icon' => '', 'active' => request()->segment(2) == 'activity-log']
                            );
                        }
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697"></path>
                    <path d="M18 14v4h4"></path>
                    <path d="M18 11v-4a2 2 0 0 0 -2 -2h-2"></path>
                    <path d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>
                    <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                    <path d="M8 11h4"></path>
                    <path d="M8 15h3"></path>
                  </svg>',
                        'id' => 'tour_step8'
                    ]
                )->order(55);
            }

            // //Backup menu
            // if (auth()->user()->can('backup')) {
            //     $menu->url(action([\App\Http\Controllers\BackUpController::class, 'index']), __('lang_v1.backup'), ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            //     <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            //     <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            //     <path d="M12 18.004h-5.343c-2.572 -.004 -4.657 -2.011 -4.657 -4.487c0 -2.475 2.085 -4.482 4.657 -4.482c.393 -1.762 1.794 -3.2 3.675 -3.773c1.88 -.572 3.956 -.193 5.444 1c1.488 1.19 2.162 3.007 1.77 4.769h.99c1.38 0 2.57 .811 3.128 1.986"></path>
            //     <path d="M19 22v-6"></path>
            //     <path d="M22 19l-3 -3l-3 3"></path>
            //   </svg>', 'active' => request()->segment(1) == 'backup'])->order(60);
            // }

            // //Modules menu
            // if (auth()->user()->can('manage_modules')) {
            //     $menu->url(action([\App\Http\Controllers\Install\ModulesController::class, 'index']), __('lang_v1.modules'), ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            //   <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            //   <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            //   <path d="M12 4l-8 4l8 4l8 -4l-8 -4"></path>
            //   <path d="M4 12l8 4l8 -4"></path>
            //   <path d="M4 16l8 4l8 -4"></path>
            // </svg>', 'active' => request()->segment(1) == 'manage-modules'])->order(60);
            // }

            //Booking menu
            if (in_array('booking', $enabled_modules) && (auth()->user()->can('crud_all_bookings') || auth()->user()->can('crud_own_bookings'))) {
                $menu->url(action([\App\Http\Controllers\Restaurant\BookingController::class, 'index']), __('restaurant.bookings'), ['icon' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.5 21h-5.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v6" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M15 19l2 2l4 -4" /></svg>', 'active' => request()->segment(1) == 'bookings'])->order(65);
            }

            //Kitchen menu
            if (in_array('kitchen', $enabled_modules)) {
                $menu->url(action([\App\Http\Controllers\Restaurant\KitchenController::class, 'index']), __('restaurant.kitchen'), ['icon' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-flame"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12c2 -2.96 0 -7 -1 -8c0 3.038 -1.773 4.741 -3 6c-1.226 1.26 -2 3.24 -2 5a6 6 0 1 0 12 0c0 -1.532 -1.056 -3.94 -2 -5c-1.786 3 -2.791 3 -4 2z" /></svg>', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'kitchen'])->order(70);
            }

            //Service Staff menu
            if (in_array('service_staff', $enabled_modules)) {
                $menu->url(action([\App\Http\Controllers\Restaurant\OrderController::class, 'index']), __('restaurant.orders'), ['icon' => 'fa fas fa-list-alt', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'orders'])->order(75);
            }

            //Notification template menu
            if (auth()->user()->can('send_notifications')) {
                $menu->url(action([\App\Http\Controllers\NotificationTemplateController::class, 'index']), __('lang_v1.notification_templates'), [
                    'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"></path>
                    <path d="M3 7l9 6l9 -6"></path>
                  </svg>',
                    'active' => request()->segment(1) == 'notification-templates'
                ])->order(80);
            }

            //Settings Dropdown
            if (
                auth()->user()->can('business_settings.access') ||
                auth()->user()->can('barcode_settings.access') ||
                auth()->user()->can('invoice_settings.access') ||
                auth()->user()->can('tax_rate.view') ||
                auth()->user()->can('tax_rate.create') ||
                auth()->user()->can('access_package_subscriptions')
            ) {
                $menu->dropdown(
                    __('business.settings'),
                    function ($sub) use ($enabled_modules, $pos_settings) {
                        if (auth()->user()->can('business_settings.access')) {
                            $sub->url(
                                action([\App\Http\Controllers\BusinessController::class, 'getBusinessSettings']),
                                __('business.business_settings'),
                                ['icon' => '', 'active' => request()->segment(1) == 'business', 'id' => 'tour_step2']
                            );
                            $sub->url(
                                action([\App\Http\Controllers\BusinessLocationController::class, 'index']),
                                __('business.business_locations'),
                                ['icon' => '', 'active' => request()->segment(1) == 'business-location']
                            );
                        }
                        if (auth()->user()->can('invoice_settings.access')) {
                            $sub->url(
                                action([\App\Http\Controllers\InvoiceSchemeController::class, 'index']),
                                __('invoice.invoice_settings'),
                                ['icon' => '', 'active' => in_array(request()->segment(1), ['invoice-schemes', 'invoice-layouts'])]
                            );
                        }
                        if (auth()->user()->can('barcode_settings.access')) {
                            $sub->url(
                                action([\App\Http\Controllers\BarcodeController::class, 'index']),
                                __('barcode.barcode_settings'),
                                ['icon' => '', 'active' => request()->segment(1) == 'barcodes']
                            );
                        }
                        if (auth()->user()->can('access_printers')) {
                            $sub->url(
                                action([\App\Http\Controllers\PrinterController::class, 'index']),
                                __('printer.receipt_printers'),
                                ['icon' => '', 'active' => request()->segment(1) == 'printers']
                            );
                        }

                        if (auth()->user()->can('tax_rate.view') || auth()->user()->can('tax_rate.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\TaxRateController::class, 'index']),
                                __('tax_rate.tax_rates'),
                                ['icon' => '', 'active' => request()->segment(1) == 'tax-rates']
                            );
                        }

                        if (in_array('tables', $enabled_modules) && auth()->user()->can('access_tables')) {
                            $sub->url(
                                action([\App\Http\Controllers\Restaurant\TableController::class, 'index']),
                                __('restaurant.tables'),
                                ['icon' => '', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'tables']
                            );
                        }

                        if (in_array('modifiers', $enabled_modules) && (auth()->user()->can('product.view') || auth()->user()->can('product.create'))) {
                            $sub->url(
                                action([\App\Http\Controllers\Restaurant\ModifierSetsController::class, 'index']),
                                __('restaurant.modifiers'),
                                ['icon' => '', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'modifiers']
                            );
                        }
                        if (!empty($pos_settings['is_shipping'])) {
                            $sub->url(
                                action([\App\Http\Controllers\ShipStationController::class, 'index']),
                                __('ShipStation'),
                                ['icon' => '', 'active' => request()->segment(1) == 'shipstation']
                            );
                        }
                        $sub->url(
                            action([\App\Http\Controllers\MerchantApplicationController::class, 'index']),
                            __('Merchant Applications'),
                            ['icon' => '', 'active' => request()->segment(1) == 'merchant-applications']
                        );

                        if (in_array('types_of_service', $enabled_modules) && auth()->user()->can('access_types_of_service')) {
                            $sub->url(
                                action([\App\Http\Controllers\TypesOfServiceController::class, 'index']),
                                __('lang_v1.types_of_service'),
                                ['icon' => '', 'active' => request()->segment(1) == 'types-of-service']
                            );
                        }

                        // if (in_array('types_of_service', $enabled_modules) && auth()->user()->can('access_types_of_service')) {
                        $sub->url(
                            action([\App\Http\Controllers\CustomDiscountController::class, 'index']),
                            __('Discount'),
                            ['icon' => '', 'active' => request()->segment(1) == 'custom-discount']
                        );
                        // }
    
                        if (auth()->user()->can('admin')) {
                            $sub->url(
                                action([\App\Http\Controllers\OptionController::class, 'index']),
                                'Options',
                                ['icon' => '', 'active' => request()->segment(1) == 'options']
                            );
                        }
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"></path>
                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                  </svg>',
                        'id' => 'tour_step3'
                    ]
                )->order(85);
            }
            //Ecom Dropdown
            if (
                $is_admin ||
                auth()->user()->can('business_settings.access') ||
                auth()->user()->can('ecom_newsletter') ||
                auth()->user()->can('ecom_contact_us')
            ) {
                $menu->dropdown(
                    'Ecom',
                    function ($sub) use ($enabled_modules, $is_admin) {
                        if (auth()->user()->can('ecom_contact_us')) {
                            $sub->url(
                                action([\App\Http\Controllers\ContactUsController::class, 'index']),
                                'Contact Us',
                                ['icon' => '', 'active' => request()->segment(1) == 'contact-us', 'id' => 'tour_step2']
                            );
                        }
                        if (auth()->user()->can('ecom_newsletter')) {
                            $sub->url(
                                action([\App\Http\Controllers\ContactUsController::class, 'getNewsLetter']),
                                'News Letter',
                                ['icon' => '', 'active' => request()->segment(1) == 'newsletter']
                            );
                        }

                        if (auth()->user()->can('ecom_wishlist')) {
                            $sub->url(
                                action([\App\Http\Controllers\ECOM\WishlistsController::class, 'wishlist']),
                                'Wishlist',
                                ['icon' => '', 'active' => request()->segment(1) == 'wishlist']
                            );
                        }
                        if (auth()->user()->can('ecom_multichannel')) {
                            $sub->url(
                                action([\App\Http\Controllers\ECOM\MultichannelController::class, 'multichannel']),
                                'Multi Channel',
                                ['icon' => '', 'active' => request()->segment(1) == 'multi-channel']
                            );
                        }

                        // COA menu (Admin only)
                        if ($is_admin) {
                            $sub->url(
                                action([\App\Http\Controllers\CoaController::class, 'index']),
                                'List of created COAs',
                                ['icon' => '', 'active' => request()->segment(1) == 'coa' && request()->segment(2) != 'create']
                            );
                            $sub->url(
                                action([\App\Http\Controllers\CoaController::class, 'create']),
                                'Create COA',
                                ['icon' => '', 'active' => request()->segment(1) == 'coa' && request()->segment(2) == 'create']
                            );
                        }


                        // }
    
                    },
                    [
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
                                <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m7.5-6.923c-.67.204-1.335.82-1.887 1.855A8 8 0 0 0 5.145 4H7.5zM4.09 4a9.3 9.3 0 0 1 .64-1.539 7 7 0 0 1 .597-.933A7.03 7.03 0 0 0 2.255 4zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a7 7 0 0 0-.656 2.5zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5zM8.5 5v2.5h2.99a12.5 12.5 0 0 0-.337-2.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5zM5.145 12q.208.58.468 1.068c.552 1.035 1.218 1.65 1.887 1.855V12zm.182 2.472a7 7 0 0 1-.597-.933A9.3 9.3 0 0 1 4.09 12H2.255a7 7 0 0 0 3.072 2.472M3.82 11a13.7 13.7 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5zm6.853 3.472A7 7 0 0 0 13.745 12H11.91a9.3 9.3 0 0 1-.64 1.539 7 7 0 0 1-.597.933M8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855q.26-.487.468-1.068zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.7 13.7 0 0 1-.312 2.5m2.802-3.5a7 7 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7 7 0 0 0-3.072-2.472c.218.284.418.598.597.933M10.855 4a8 8 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4z"/>
                                </svg>',
                        'id' => 'tour_ste4'
                    ]
                )->order(85);

            }

            // New Screens Dropdown
            if ($is_admin) {
                $menu->dropdown(
                    'New Screens',
                    function ($sub) {
                        $sub->url(
                            url('new-screens/dashboard'),
                            'Dashboard',
                            ['icon' => '', 'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'dashboard']
                        );
                        $sub->url(
                            url('new-screens/platform-revenue'),
                            'Platform Revenue',
                            ['icon' => '', 'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'platform-revenue']
                        );
                        $sub->url(
                            url('new-screens/buyer'),
                            'Buyer',
                            ['icon' => '', 'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'buyer']
                        );
                        $sub->url(
                            url('new-screens/dispute-and-claims'),
                            'Dispute & Claims',
                            [
                                'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 3m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0"/>
                                <path d="M12 11v10"/>
                                <path d="M9 15l3 -3l3 3"/>
                                <path d="M3 21h18"/>
                            </svg>',
                                'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'dispute-and-claims'
                            ]
                        );
                        $sub->url(
                            url('new-screens/dispute-and-claims'),
                            'Dispute & Claims',
                            [
                                'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 3m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0"/>
                                <path d="M12 11v10"/>
                                <path d="M9 15l3 -3l3 3"/>
                                <path d="M3 21h18"/>
                            </svg>',
                                'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'dispute-and-claims'
                            ]
                        );
                        $sub->url(
                            url('new-screens/geofence-rule-engine'),
                            'Geofence Rule Engine',
                            ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l0 .01" /><path d="M18 13l-3.5 -5a4 4 0 1 1 7 0l-3.5 5" /><path d="M10.5 4.75l-1.5 -.75l-6 3l0 13l6 -3l6 3l6 -3l0 -2" /><path d="M9 4l0 13" /><path d="M15 15l0 5" /></svg>', 'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'geofence-rule-engine']
                        );
                        $sub->url(
                            url('new-screens/fbs-warehouse'),
                            'FBS Warehouse',
                            ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0"/><path d="M5 21v-16l14 0v16"/><path d="M9 7h6"/><path d="M9 11h6"/><path d="M9 15h4"/></svg>', 'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'fbs-warehouse']
                        );
                        $sub->url(
                            url('new-screens/orders'),
                            'Orders',
                            ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h2"/><path d="M15 2v4"/><path d="M9 2v4"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>', 'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'orders']
                        );
                        $sub->url(
                            url('new-screens/products'),
                            'Products',
                            ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 3m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"/><path d="M17 17v2a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h2"/></svg>', 'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'products']
                        );
                        $sub->url(
                            url('new-screens/compliance-center'),
                            'Compliance Center',
                            ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12l2 2l4 -4"/><path d="M21 12c-1 0 -2.5 -.5 -3 -2c-.5 -1.5 -1 -3 -1 -4c0 -2 -1 -3 -3 -3s-3 1 -3 3c0 1 -0.5 2.5 -1 4c-0.5 1.5 -2 2 -3 2"/><path d="M3 12c1 0 2.5 0.5 3 2c0.5 1.5 1 3 1 4c0 2 1 3 3 3s3 -1 3 -3c0 -1 0.5 -2.5 1 -4c0.5 -1.5 2 -2 3 -2"/></svg>', 'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'compliance-center']
                        );

                        $sub->url(

                            url('new-screens/fee-configuration'),

                            'Fee Configuration',

                            ['icon' => '', 'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'fee-configuration']

                        );

                        $sub->url(

                            url('new-screens/sellers'),

                            'Sellers',

                            ['icon' => '', 'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'sellers']

                        );

                        $sub->url(

                            url('new-screens/ad-platform-overview'),

                            'Ad Platform Overview',

                            ['icon' => '', 'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'ad-platform-overview']

                        );

                        $sub->url(

                            url('new-screens/platform-settings'),

                            'Platform Settings',

                            ['icon' => '', 'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'platform-settings']

                        );

                        $sub->url(

                            url('new-screens/financial-report'),

                            'Financial Report',

                            ['icon' => '', 'active' => request()->segment(1) == 'new-screens' && request()->segment(2) == 'financial-report']

                        );
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <rect x="3" y="4" width="18" height="12" rx="1" />
                            <line x1="7" y1="20" x2="17" y2="20" />
                            <line x1="9" y1="16" x2="9" y2="20" />
                            <line x1="15" y1="16" x2="15" y2="20" />
                        </svg>',
                        'id' => 'tour_new_screens'
                    ]
                )->order(86);
            }

            // Picker Menu 
            if (auth()->user()->can('pickerman.view') && !auth()->user()->hasRole('Admin#' . session('business.id'))) {
                $menu->dropdown(
                    'Order Picking',
                    function ($sub) {
                        if (auth()->user()->can('pickerman.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\OrderfulfillmentController::class, 'picker']),
                                'Order List',
                                ['icon' => '', 'active' => request()->segment(1) == 'order-fulfillment-picker']
                            );
                        }
                    },
                    [
                        'icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                            <path d="M12 12l8 -4.5" />
                            <path d="M12 12l0 9" />
                            <path d="M12 12l-8 -4.5" />
                            <path d="M16 5.25l-8 4.5" />
                          </svg>',
                        'id' => 'tour_ste10'
                    ]
                )->order(90);
            }


        });

        //Add menus from modules
        $moduleUtil = new ModuleUtil;
        $moduleUtil->getModuleData('modifyAdminMenu');

        return $next($request);
    }
}
