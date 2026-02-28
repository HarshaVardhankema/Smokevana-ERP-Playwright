<?php

namespace Modules\Woocommerce\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Media;
use App\Product;
use App\SellingPriceGroup;
use App\System;
use App\TaxRate;
use App\Utils\ModuleUtil;
use App\Variation;
use App\VariationTemplate;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Woocommerce\Entities\WoocommerceSyncLog;
use Modules\Woocommerce\Utils\WoocommerceUtil;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use App\VariationLocationDetails;
use App\Transaction;

class WoocommerceController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $woocommerceUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  WoocommerceUtil  $woocommerceUtil
     * @return void
     */
    public function __construct(WoocommerceUtil $woocommerceUtil, ModuleUtil $moduleUtil)
    {
        $this->woocommerceUtil = $woocommerceUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        try {
            $business_id = request()->session()->get('business.id');

            if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
                abort(403, 'Unauthorized action.');
            }

            $tax_rates = TaxRate::where('business_id', $business_id)
                            ->get();

            $woocommerce_tax_rates = ['' => __('messages.please_select')];

            $woocommerce_api_settings = $this->woocommerceUtil->get_api_settings($business_id);

            $alerts = [];

            $not_synced_cat_count = Category::where('business_id', $business_id)
                                        ->whereNull('woocommerce_cat_id')
                                        ->where('category_type', 'product')
                                        ->count();

            if (! empty($not_synced_cat_count)) {
                $alerts['not_synced_cat'] = $not_synced_cat_count == 1 ? __('woocommerce::lang.one_cat_not_synced_alert') : __('woocommerce::lang.cat_not_sync_alert', ['count' => $not_synced_cat_count]);
            }

            $cat_last_sync = $this->woocommerceUtil->getLastSync($business_id, 'categories', false);
            if (! empty($cat_last_sync)) {
                $updated_cat_count = Category::where('business_id', $business_id)
                                        ->whereNotNull('woocommerce_cat_id')
                                        ->where('updated_at', '>', $cat_last_sync)
                                        ->count();
            }

            if (! empty($updated_cat_count)) {
                $alerts['updated_cat'] = $updated_cat_count == 1 ? __('woocommerce::lang.one_cat_updated_alert') : __('woocommerce::lang.cat_updated_alert', ['count' => $updated_cat_count]);
            }

            $products_last_synced = $this->woocommerceUtil->getLastSync($business_id, 'all_products', false);
            $query = Product::where('business_id', $business_id)
                                        ->whereIn('type', ['single', 'variable'])
                                        ->join('variations as v', 'v.product_id', '=', 'products.id')
                                        ->whereNull('woocommerce_product_id')
                                        ->where('woocommerce_disable_sync', 0)
                                        ->whereNull('v.deleted_at')
                                        ->groupBy('products.id');

            if (! empty($woocommerce_api_settings->location_id)) {
                $query->ForLocation($woocommerce_api_settings->location_id);
            }
            $not_synced_product_count = $query->get()->count();

            if (! empty($not_synced_product_count)) {
                $alerts['not_synced_product'] = $not_synced_product_count == 1 ? __('woocommerce::lang.one_product_not_sync_alert') : __('woocommerce::lang.product_not_sync_alert', ['count' => $not_synced_product_count]);
            }
            if (! empty($products_last_synced)) {
                $updated_product_count = Product::where('business_id', $business_id)
                                        ->whereNotNull('woocommerce_product_id')
                                        ->where('woocommerce_disable_sync', 0)
                                        ->whereIn('type', ['single', 'variable'])
                                        ->where('updated_at', '>', $products_last_synced)
                                        ->count();
            }

            if (! empty($updated_product_count)) {
                $alerts['not_updated_product'] = $updated_product_count == 1 ? __('woocommerce::lang.one_product_updated_alert') : __('woocommerce::lang.product_updated_alert', ['count' => $updated_product_count]);
            }

            $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
            if (empty($notAllowed)) {
                $response = $this->woocommerceUtil->getTaxRates($business_id);
                if (! empty($response)) {
                    foreach ($response as $r) {
                        $woocommerce_tax_rates[$r->id] = $r->name;
                    }
                }
            }
        } catch (\Exception $e) {
            $alerts['connection_failed'] = 'Unable to connect with WooCommerce, Check API settings';
        }

        return view('woocommerce::woocommerce.index')
                ->with(compact('tax_rates', 'woocommerce_tax_rates', 'alerts'));
    }

    /**
     * Displays form to update woocommerce api settings.
     *
     * @return Response
     */
    public function apiSettings()
    {
        $business_id = request()->session()->get('business.id');

        if (! (auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.access_woocommerce_api_settings')))) {
            abort(403, 'Unauthorized action.');
        }

        $default_settings = [
            'woocommerce_app_url' => '',
            'woocommerce_consumer_key' => '',
            'woocommerce_consumer_secret' => '',
            'location_id' => null,
            'default_tax_class' => '',
            'product_tax_type' => 'inc',
            'default_selling_price_group' => '',
            'product_fields_for_create' => ['category', 'quantity'],
            'product_fields_for_update' => ['name', 'price', 'category', 'quantity'],
        ];

        $price_groups = SellingPriceGroup::where('business_id', $business_id)
                        ->pluck('name', 'id')->prepend(__('lang_v1.default'), '');

        $business = Business::find($business_id);

        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            $business = null;
        }

        if (! empty($business->woocommerce_api_settings)) {
            $default_settings = json_decode($business->woocommerce_api_settings, true);
            if (empty($default_settings['product_fields_for_create'])) {
                $default_settings['product_fields_for_create'] = [];
            }

            if (empty($default_settings['product_fields_for_update'])) {
                $default_settings['product_fields_for_update'] = [];
            }
        }

        $locations = BusinessLocation::forDropdown($business_id);
        $module_version = System::getProperty('woocommerce_version');

        $cron_job_command = $this->moduleUtil->getCronJobCommand();

        $shipping_statuses = $this->moduleUtil->shipping_statuses();

        return view('woocommerce::woocommerce.api_settings')
                ->with(compact('default_settings', 'locations', 'price_groups', 'module_version', 'cron_job_command', 'business', 'shipping_statuses'));
    }

    /**
     * Updates woocommerce api settings.
     *
     * @return Response
     */
    public function updateSettings(Request $request)
    {
        $business_id = request()->session()->get('business.id');

        if (! (auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.access_woocommerce_api_settings')))) {
            abort(403, 'Unauthorized action.');
        }

        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $input = $request->except('_token');

            $input['product_fields_for_create'] = ! empty($input['product_fields_for_create']) ? $input['product_fields_for_create'] : [];
            $input['product_fields_for_update'] = ! empty($input['product_fields_for_update']) ? $input['product_fields_for_update'] : [];
            $input['order_statuses'] = ! empty($input['order_statuses']) ? $input['order_statuses'] : [];
            $input['shipping_statuses'] = ! empty($input['shipping_statuses']) ? $input['shipping_statuses'] : [];
            $input['price_group_mappings'] = ! empty($input['price_group_mappings']) ? $input['price_group_mappings'] : [];

            $business = Business::find($business_id);
            $business->woocommerce_api_settings = json_encode($input);
            $business->woocommerce_wh_oc_secret = $input['woocommerce_wh_oc_secret'];
            $business->woocommerce_wh_ou_secret = $input['woocommerce_wh_ou_secret'];
            $business->woocommerce_wh_od_secret = $input['woocommerce_wh_od_secret'];
            $business->woocommerce_wh_or_secret = $input['woocommerce_wh_or_secret'];
            $business->woocommerce_wh_general_secret = $input['woocommerce_wh_general_secret'] ?? null;
            $business->save();

            $output = ['success' => 1,
                'msg' => trans('lang_v1.updated_succesfully'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Synchronizes pos categories with Woocommerce categories
     *
     * @return Response
     */
    public function syncCategories()
    {
        $business_id = request()->session()->get('business.id');

        if (! (auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.syc_categories')))) {
            abort(403, 'Unauthorized action.');
        }

        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            DB::beginTransaction();
            $user_id = request()->session()->get('user.id');

            $this->woocommerceUtil->syncCategories($business_id, $user_id);

            DB::commit();

            $output = ['success' => 1,
                'msg' => __('woocommerce::lang.synced_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,
                    'msg' => $e->getMessage(),
                ];
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
        }

        return $output;
    }

    /**
     * Synchronizes pos products with Woocommerce products
     *
     * @return Response
     */
    public function syncProducts()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (! (auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.sync_products')))) {
            abort(403, 'Unauthorized action.');
        }

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        try {
            $user_id = request()->session()->get('user.id');
            $sync_type = request()->input('type');

            DB::beginTransaction();

            $offset = request()->input('offset');
            $limit = 100;
            $all_products = $this->woocommerceUtil->syncProducts($business_id, $user_id, $sync_type, $limit, $offset);
            $total_products = count($all_products);

            DB::commit();
            $msg = $total_products > 0 ? __('woocommerce::lang.n_products_synced_successfully', ['count' => $total_products]) : __('woocommerce::lang.synced_successfully');
            $output = ['success' => 1,
                'msg' => $msg,
                'total_products' => $total_products,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,
                    'msg' => $e->getMessage(),
                ];
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
        }

        return $output;
    }
    /**
     * Synchronizes WooCommerce Products with ERP products
     * Handles 20,000+ products with multiple variations efficiently
     *
     * @return Response
     */
    public function syncProductFromWooToErp()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        try {
            $user_id = request()->session()->get('user.id');
            $offset = request()->input('offset', 0);
            $limit = request()->input('limit', 100);
            $sync_type = request()->input('sync_type', 'all'); // all, new, updated

            DB::beginTransaction();
            $sync_result = $this->woocommerceUtil->syncProductsFromWooToErp($business_id, $user_id, $sync_type, $limit, $offset);
            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('woocommerce::lang.n_products_synced_successfully', ['count' => $sync_result['total_products']]),
                'total_products' => $sync_result['total_products'],
                'created_products' => $sync_result['created_products'],
                'updated_products' => $sync_result['updated_products'],
                'skipped_products' => $sync_result['skipped_products'],
                'has_more' => $sync_result['has_more'],
                'next_offset' => $sync_result['next_offset']
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,
                    'msg' => $e->getMessage(),
                ];
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
        }

        return $output;
    }
    /**
     * Synchronizes Woocommers Orders with POS sales
     *
     * @return Response
     */
    public function syncOrders()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (! (auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.sync_orders')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            $user_id = request()->session()->get('user.id');

            $this->woocommerceUtil->syncOrders($business_id, $user_id);

            DB::commit();

            $output = ['success' => 1,
                'msg' => __('woocommerce::lang.synced_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,
                    'msg' => $e->getMessage(),
                ];
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
        }

        return $output;
    }

    /**
     * Retrives sync log
     *
     * @return Response
     */
    public function getSyncLog()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $last_sync = [
                'categories' => $this->woocommerceUtil->getLastSync($business_id, 'categories'),
                'new_products' => $this->woocommerceUtil->getLastSync($business_id, 'new_products'),
                'all_products' => $this->woocommerceUtil->getLastSync($business_id, 'all_products'),
                'orders' => $this->woocommerceUtil->getLastSync($business_id, 'orders'),

            ];

            return $last_sync;
        }
    }

    /**
     * Maps POS tax_rates with Woocommerce tax rates.
     *
     * @return Response
     */
    public function mapTaxRates(Request $request)
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (! (auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.map_tax_rates')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->except('_token');
            foreach ($input['taxes'] as $key => $value) {
                $value = ! empty($value) ? $value : null;
                TaxRate::where('business_id', $business_id)
                        ->where('id', $key)
                        ->update(['woocommerce_tax_rate_id' => $value]);
            }

            $output = ['success' => 1,
                'msg' => __('lang_v1.updated_succesfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function viewSyncLog()
    {
        $business_id = request()->session()->get('business.id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $logs = WoocommerceSyncLog::where('woocommerce_sync_logs.business_id', $business_id)
                    ->leftjoin('users as U', 'U.id', '=', 'woocommerce_sync_logs.created_by')
                    ->select([
                        'woocommerce_sync_logs.created_at',
                        'sync_type', 'operation_type',
                        DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"),
                        'woocommerce_sync_logs.data',
                        'woocommerce_sync_logs.details as log_details',
                        'woocommerce_sync_logs.id as DT_RowId',
                    ]);
            $sync_type = [];
            if (auth()->user()->can('woocommerce.syc_categories')) {
                $sync_type[] = 'categories';
            }
            if (auth()->user()->can('woocommerce.sync_products')) {
                $sync_type[] = 'all_products';
                $sync_type[] = 'new_products';
                $sync_type[] = 'product_quantities';
                $sync_type[] = 'customers';
            }

            if (auth()->user()->can('woocommerce.sync_orders')) {
                $sync_type[] = 'orders';
            }
            if (! auth()->user()->can('superadmin')) {
                $logs->whereIn('sync_type', $sync_type);
            }

            return Datatables::of($logs)
                ->editColumn('created_at', function ($row) {
                    $created_at = $this->woocommerceUtil->format_date($row->created_at, true);
                    $for_humans = \Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at)->diffForHumans();

                    return $created_at.'<br><small>'.$for_humans.'</small>';
                })
                ->editColumn('sync_type', function ($row) {
                    $array = [
                        'categories' => __('category.categories'),
                        'all_products' => __('sale.products'),
                        'new_products' => __('sale.products'),
                        'product_quantities' => __('sale.quantities'),
                        'customers' => __('Customers'),
                        'orders' => __('woocommerce::lang.orders'),
                    ];

                    return $array[$row->sync_type];
                })
                ->editColumn('operation_type', function ($row) {
                    $array = [
                        'created' => __('woocommerce::lang.created'),
                        'updated' => __('woocommerce::lang.updated'),
                        'reset' => __('woocommerce::lang.reset'),
                        'deleted' => __('lang_v1.deleted'),
                        'restored' => __('woocommerce::lang.order_restored'),
                    ];

                    return array_key_exists($row->operation_type, $array) ? $array[$row->operation_type] : '';
                })
                ->editColumn('data', function ($row) {
                    if (! empty($row->data)) {
                        $data = json_decode($row->data, true);

                        return implode(', ', $data).'<br><small>'.count($data).' '.__('woocommerce::lang.records').'</small>';
                    } else {
                        return '';
                    }
                })
                ->editColumn('log_details', function ($row) {
                    $details = '';
                    if (! empty($row->log_details)) {
                        $details = $row->log_details;
                    }

                    return $details;
                })
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['created_at', 'data'])
                ->make(true);
        }

        return view('woocommerce::woocommerce.sync_log');
    }

    /**
     * Retrives details of a sync log.
     *
     * @param  int  $id
     * @return Response
     */
    public function getLogDetails($id)
    {
        $business_id = request()->session()->get('business.id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $log = WoocommerceSyncLog::where('business_id', $business_id)
                                            ->find($id);
            $log_details = json_decode($log->details);

            return view('woocommerce::woocommerce.partials.log_details')
                    ->with(compact('log_details'));
        }
    }

    /**
     * Resets synced categories
     *
     * @return json
     */
    public function resetCategories()
    {
        $business_id = request()->session()->get('business.id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                Category::where('business_id', $business_id)
                        ->update(['woocommerce_cat_id' => null]);
                $user_id = request()->session()->get('user.id');
                $this->woocommerceUtil->createSyncLog($business_id, $user_id, 'categories', 'reset', null);

                $output = ['success' => 1,
                    'msg' => __('woocommerce::lang.cat_reset_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Resets synced products
     *
     * @return json
     */
    public function resetProducts()
    {
        $business_id = request()->session()->get('business.id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                //Update products table
                Product::where('business_id', $business_id)
                        ->update(['woocommerce_product_id' => null, 'woocommerce_media_id' => null]);

                $product_ids = Product::where('business_id', $business_id)
                                    ->pluck('id');

                $product_ids = ! empty($product_ids) ? $product_ids : [];
                //Update variations table
                Variation::whereIn('product_id', $product_ids)
                        ->update([
                            'woocommerce_variation_id' => null,
                        ]);

                //Update variation templates
                VariationTemplate::where('business_id', $business_id)
                                ->update([
                                    'woocommerce_attr_id' => null,
                                ]);

                Media::where('business_id', $business_id)
                        ->update(['woocommerce_media_id' => null]);

                $user_id = request()->session()->get('user.id');
                $this->woocommerceUtil->createSyncLog($business_id, $user_id, 'all_products', 'reset', null);

                $output = ['success' => 1,
                    'msg' => __('woocommerce::lang.prod_reset_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => 0,
                    'msg' => 'File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage(),
                ];
            }

            return $output;
        }
    }

    /**
     * Synchronizes only product quantities with Woocommerce
     *
     * @return Response
     */
    public function syncProductQuantities()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.sync_products')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            $user_id = request()->session()->get('user.id');

            $this->woocommerceUtil->syncProductQuantities($business_id, $user_id);

            DB::commit();

            $output = ['success' => 1,
                'msg' => __('woocommerce::lang.synced_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,
                    'msg' => $e->getMessage(),
                ];
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
        }

        return $output;
    }

    /**
     * Synchronizes product quantities from WooCommerce to ERP using super-fast endpoint
     *
     * @return Response
     */
    public function syncProductQuantitiesFromWooToErp()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.sync_products')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user_id = request()->session()->get('user.id');
            $chunk_size = request()->get('chunk_size', 100); // Increased default chunk size for super-fast sync
            $offset = request()->get('offset', 0);
            $modified_after = request()->get('modified_after'); // Optional filter for recent changes

            \Log::info('Starting super-fast quantity sync from WooCommerce to ERP', [
                'business_id' => $business_id,
                'chunk_size' => $chunk_size,
                'offset' => $offset,
                'modified_after' => $modified_after
            ]);

            $result = $this->woocommerceUtil->syncProductQuantitiesFromWooToErp($business_id, $user_id, $chunk_size, $offset);

            // Add performance metrics to response
            $result['sync_type'] = 'super_fast';
            $result['endpoint'] = 'quantities';

            $output = [
                'success' => 1,
                'msg' => __('woocommerce::lang.quantities_synced_from_woo_successfully'),
                'data' => $result
            ];
        } catch (\Exception $e) {
            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,
                    'msg' => $e->getMessage(),
                    'sync_type' => 'super_fast'
                ];
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                    'sync_type' => 'super_fast'
                ];
            }
        }

        return $output;
    }

    /**
     * Synchronizes customers with Woocommerce
     *
     * @return Response
     */
    public function syncCustomers()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.sync_products')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            $user_id = request()->session()->get('user.id');

            $this->woocommerceUtil->syncCustomers($business_id, $user_id);

            DB::commit();

            $output = ['success' => 1,
                'msg' => __('woocommerce::lang.synced_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,
                    'msg' => $e->getMessage(),
                ];
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
        }

        return $output;
    }

    /**
     * Synchronizes customers from WooCommerce to ERP
     *
     * @return Response
     */
    public function syncCustomersFromWooToErp()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.sync_products')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            $user_id = request()->session()->get('user.id');

            // Check if this is a chunked sync request
            $offset = request()->get('offset', 0);
            $limit = request()->get('limit', 100);
            $is_chunked = request()->get('chunked', false);

            $result = $this->woocommerceUtil->syncCustomersFromWooToErp($business_id, $user_id, 'all', $limit, $offset, $is_chunked);

            $msg = __('woocommerce::lang.synced_successfully');
            if ($result['total_customers'] > 0) {
                $msg = 'Successfully synced ' . $result['total_customers'] . ' customers';
                if ($result['total_chunks'] > 1) {
                    $msg .= ' in ' . $result['total_chunks'] . ' chunks';
                }
            }

            $output = ['success' => 1,
                'msg' => $msg,
                'data' => $result
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,
                    'msg' => $e->getMessage(),
                ];
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
        }

        return $output;
    }

    /**
     * Test ERP to WooCommerce order sync
     *
     * @return Response
     */
    public function testErpToWooOrderSync()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            // Get a sample transaction for testing
            $transaction = Transaction::where('business_id', $business_id)
                ->where('type', 'sales_order')
                ->where('status', 'ordered')
                ->with(['sell_lines.product', 'sell_lines.variations', 'contact', 'payment_lines'])
                ->where('id', 136)
                ->first();

            if (!$transaction) {
                return [
                    'success' => 0,
                    'msg' => 'No suitable transaction found for testing. Please create a sale transaction first.'
                ];
            }

            DB::beginTransaction();
            
            // Test the sync
            $result = $this->woocommerceUtil->updateOrderInWooCommerce($business_id, $transaction);
            
            DB::commit();

            if ($result['success']) {
                $output = [
                    'success' => 1,
                    'msg' => 'Order successfully synced to WooCommerce! WooCommerce Order ID: ' . $result['woocommerce_order_id'],
                    'transaction_id' => $transaction->id,
                    'woocommerce_order_id' => $result['woocommerce_order_id']
                ];
            } else {
                $output = [
                    'success' => 0,
                    'msg' => 'Failed to sync order to WooCommerce: ' . ($result['message']?? $result['error'] ?? 'Unknown error'),
                    'transaction_id' => $transaction->id
                ];
            }

        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = [
                    'success' => 0,
                    'msg' => $e->getMessage(),
                ];
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = [
                    'success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
        }

        return $output;
    }

    public function testConnection(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        $woocommerce_app_url = $request->woocommerce_app_url;
        $woocommerce_consumer_key = $request->woocommerce_consumer_key;
        $woocommerce_consumer_secret = $request->woocommerce_consumer_secret;
        $location_id = $request->location_id;
        $enable_auto_sync = $request->enable_auto_sync;

        $testResponse = $this->woocommerceUtil->testConnection($business_id, $woocommerce_app_url, $woocommerce_consumer_key, $woocommerce_consumer_secret, $location_id, $enable_auto_sync);

        return response()->json($testResponse);
    }

    // ----------------------------- SPECIFIC PRODUCT UPDATE METHODS -----------------------------

    /**
     * Process stock update from WooCommerce to ERP (Job-based)
     *
     * @param Request $request
     * @return Response
     */
    public function processWooCommerceStockUpdate(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');

            if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
                abort(403, 'Unauthorized action.');
            }

            $woo_product_data = $request->input('product_data');
            $update_type = $request->input('update_type', 'stock_only');

            if (!$woo_product_data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product data is required'
                ]);
            }

            // Dispatch job for processing
            \App\Jobs\ProcessWooCommerceStockUpdate::dispatch($business_id, $woo_product_data, $update_type);

            return response()->json([
                'success' => true,
                'message' => 'Stock update job dispatched successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing stock update from WooCommerce', [
                'error' => $e->getMessage(),
                'business_id' => $business_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing stock update: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process price update from WooCommerce to ERP (Job-based)
     *
     * @param Request $request
     * @return Response
     */
    public function processWooCommercePriceUpdate(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');

            if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
                abort(403, 'Unauthorized action.');
            }

            $woo_product_data = $request->input('product_data');
            $update_type = $request->input('update_type', 'price_only');

            if (!$woo_product_data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product data is required'
                ]);
            }

            // Dispatch job for processing
            \App\Jobs\ProcessWooCommercePriceUpdate::dispatch($business_id, $woo_product_data, $update_type);

            return response()->json([
                'success' => true,
                'message' => 'Price update job dispatched successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing price update from WooCommerce', [
                'error' => $e->getMessage(),
                'business_id' => $business_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing price update: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process variation update from WooCommerce to ERP (Job-based)
     *
     * @param Request $request
     * @return Response
     */
    public function processWooCommerceVariationUpdate(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');

            if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
                abort(403, 'Unauthorized action.');
            }

            $woo_product_data = $request->input('product_data');
            $update_type = $request->input('update_type', 'variation_only');

            if (!$woo_product_data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product data is required'
                ]);
            }

            // Dispatch job for processing
            \App\Jobs\ProcessWooCommerceVariationUpdate::dispatch($business_id, $woo_product_data, $update_type);

            return response()->json([
                'success' => true,
                'message' => 'Variation update job dispatched successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing variation update from WooCommerce', [
                'error' => $e->getMessage(),
                'business_id' => $business_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing variation update: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process variations data from WooCommerce to ERP (Job-based)
     */
    public function updateWooCommerceVariationsData(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');

            if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
                abort(403, 'Unauthorized action.');
            }

            $woo_product_data = $request->input('product_data');
            $update_type = $request->input('update_type', 'variation_data_only');

            if (!$woo_product_data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product data is required'
                ]);
            }

            // Dispatch job for processing
            \App\Jobs\ProcessWooCommerceVariationDataUpdate::dispatch($business_id, $woo_product_data, $update_type);

            return response()->json([
                'success' => true,
                'message' => 'Variation data update job dispatched successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing variation data update from WooCommerce', [
                'error' => $e->getMessage(),
                'business_id' => $business_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing variation data update: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Sync categories from WooCommerce to ERP (chunked)
     */
    public function syncCategoriesFromWooToErp(Request $request)
    {
        if (!auth()->user()->can('woocommerce.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            
            // Get chunking parameters
            $offset = $request->input('offset', 0);
            $limit = $request->input('limit', 500);

            $result = $this->woocommerceUtil->syncCategoriesFromWoocommerce($business_id, $user_id, $offset, $limit);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error in syncCategoriesFromWooToErp: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing categories: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Sync brands from WooCommerce to ERP
     */
    public function syncBrandsFromWooToErp()
    {
        if (!auth()->user()->can('woocommerce.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $result = $this->woocommerceUtil->syncBrandsFromWoocommerce($business_id, $user_id);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error in syncBrandsFromWooToErp: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing brands: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the ERP to WooCommerce sync page
     */
    public function syncToWooCommercePage()
    {
        if (!auth()->user()->can('woocommerce.view')) {
            abort(403, 'Unauthorized action.');
        }

        return view('woocommerce::woocommerce.sync_products_to_woo');
    }

    /**
     * Sync a single product from ERP to WooCommerce
     */
    public function syncProductToWooCommerce(Request $request, $product_id)
    {
        if (!auth()->user()->can('woocommerce.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            
            $product = Product::with(['variations', 'category', 'brand', 'vendors'])
                ->where('business_id', $business_id)
                ->findOrFail($product_id);

            $result = $this->woocommerceUtil->syncProductToWooCommerce($business_id, $product);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product synced to WooCommerce successfully',
                    'woocommerce_product_id' => $result['woocommerce_product_id']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? $result['error'] ?? 'Sync failed'
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('Error syncing product to WooCommerce', [
                'product_id' => $product_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk sync products from ERP to WooCommerce
     */
    public function bulkSyncProductsToWooCommerce(Request $request)
    {
        if (!auth()->user()->can('woocommerce.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $product_ids = $request->input('product_ids', []);

            if (empty($product_ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No products selected'
                ], 422);
            }

            $result = $this->woocommerceUtil->bulkSyncProductsToWooCommerce($business_id, $product_ids);

            return response()->json([
                'success' => true,
                'message' => "Synced {$result['success']} products. Failed: {$result['failed']}",
                'details' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error in bulk sync products to WooCommerce', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync product stock from ERP to WooCommerce
     */
    public function syncProductStockToWooCommerce(Request $request, $product_id)
    {
        if (!auth()->user()->can('woocommerce.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            
            $product = Product::with('variations')
                ->where('business_id', $business_id)
                ->findOrFail($product_id);

            $result = $this->woocommerceUtil->syncProductStockToWooCommerce($business_id, $product);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error syncing product stock to WooCommerce', [
                'product_id' => $product_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products eligible for WooCommerce sync
     */
    public function getProductsForWooSync(Request $request)
    {
        if (!auth()->user()->can('woocommerce.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $query = Product::where('business_id', $business_id)
            ->whereIn('type', ['single', 'variable'])
            ->with(['variations', 'category', 'vendors']);

        // Filter by sync status
        $sync_status = $request->input('sync_status');
        if ($sync_status === 'not_synced') {
            $query->whereNull('woocommerce_product_id');
        } elseif ($sync_status === 'synced') {
            $query->whereNotNull('woocommerce_product_id');
        }

        // Filter by product source
        $source_type = $request->input('source_type');
        if ($source_type) {
            $query->where('product_source_type', $source_type);
        }

        return DataTables::of($query)
            ->addColumn('sync_status', function ($row) {
                if ($row->woocommerce_product_id) {
                    return '<span class="badge bg-success">Synced</span> <small class="text-muted">#' . $row->woocommerce_product_id . '</small>';
                }
                return '<span class="badge bg-warning">Not Synced</span>';
            })
            ->addColumn('product_type', function ($row) {
                if ($row->product_source_type === 'dropshipped') {
                    return '<span class="badge bg-purple">Dropshipped</span>';
                }
                return '<span class="badge bg-info">In-House</span>';
            })
            ->addColumn('vendor', function ($row) {
                $vendor = $row->vendors->first();
                return $vendor ? $vendor->name : '-';
            })
            ->addColumn('price', function ($row) {
                $variation = $row->variations->first();
                return $variation ? number_format($variation->sell_price_inc_tax ?? 0, 2) : '0.00';
            })
            ->addColumn('action', function ($row) {
                $html = '<div class="btn-group">';
                
                if ($row->woocommerce_product_id) {
                    $html .= '<button class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-primary sync-product" data-id="' . $row->id . '" title="Re-sync to WooCommerce"><i class="fas fa-sync"></i></button> ';
                    $html .= '<button class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-info sync-stock" data-id="' . $row->id . '" title="Sync Stock Only"><i class="fas fa-boxes"></i></button>';
                } else {
                    $html .= '<button class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-success sync-product" data-id="' . $row->id . '" title="Sync to WooCommerce"><i class="fas fa-cloud-upload-alt"></i></button>';
                }
                
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['sync_status', 'product_type', 'action'])
            ->make(true);
    }
}
