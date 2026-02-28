<?php

namespace Modules\Woocommerce\Utils;

use App\Business;
use App\Category;
use App\Contact;
use App\Exceptions\PurchaseSellMismatch;
use App\Models\ProductGalleryImage;
use App\Product;
use App\ProductVariation;
use App\Brand;
// use App\WebCategory;
use App\TaxRate;
use App\Transaction;
use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Unit;
use App\Utils\ModuleUtil;
use App\Variation;
use App\VariationLocationDetails;
use App\VariationTemplate;
use Automattic\WooCommerce\Client;
// use DB;
use Illuminate\Support\FacadesLog;
use Modules\Woocommerce\Entities\WoocommerceSyncLog;
use Modules\Woocommerce\Exceptions\WooCommerceError;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\VariationGroupPrice;
use App\VariationValueTemplate;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WoocommerceUtil extends Util
{
    /**
     * All Utils instance.
     */
    protected $transactionUtil;

    protected $productUtil;

    /**
     * Constructor
     *
     * @param  ProductUtil  $product
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Get WooCommerce API settings
     *
     * @param  int  $business_id
     * @return object
     */
    public function get_api_settings($business_id)
    {
        $business = Business::find($business_id);
        $woocommerce_api_settings = json_decode($business->woocommerce_api_settings);

        return $woocommerce_api_settings;
    }

    /**
     * Add order to skipped orders
     *
     * @param  object  $business
     * @param  int  $order_id
     * @return void
     */
    private function add_to_skipped_orders($business, $order_id)
    {
        $business = ! is_object($business) ? Business::find($business) : $business;
        $skipped_orders = ! empty($business->woocommerce_skipped_orders) ? json_decode($business->woocommerce_skipped_orders, true) : [];
        if (! in_array($order_id, $skipped_orders)) {
            $skipped_orders[] = $order_id;
        }

        $business->woocommerce_skipped_orders = json_encode($skipped_orders);
        $business->save();
    }

    /**
     * Remove order from skipped orders
     *
     * @param  object  $business
     * @param  int  $order_id
     * @return void
     */
    private function remove_from_skipped_orders($business, $order_id)
    {
        $business = ! is_object($business) ? Business::find($business) : $business;
        $skipped_orders = ! empty($business->woocommerce_skipped_orders) ? json_decode($business->woocommerce_skipped_orders, true) : [];

        $skipped_orders = empty($skipped_orders) ? [] : $skipped_orders;

        if (in_array($order_id, $skipped_orders)) {
            $skipped_orders = array_diff($skipped_orders, [$order_id]);
        }

        $business->woocommerce_skipped_orders = json_encode($skipped_orders);
        $business->save();
    }

    /**
     * Creates Automattic\WooCommerce\Client object
     *
     * @param  int  $business_id
     * @return object
     */
    public function woo_client($business_id)
    {
        $woocommerce_api_settings = $this->get_api_settings($business_id);
        if (empty($woocommerce_api_settings)) {
            throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
        }

        $woocommerce = new Client(
            $woocommerce_api_settings->woocommerce_app_url,
            $woocommerce_api_settings->woocommerce_consumer_key,
            $woocommerce_api_settings->woocommerce_consumer_secret,
            [
                'wp_api' => true,
                'version' => 'wc/v2',
                'timeout' => 10000,
                'verify_ssl' => false,
            ]
        );

        return $woocommerce;
    }

    /**
     * Synchronizes pos categories with Woocommerce categories
     *
     * @param  int  $business_id
     * @param  array  $data
     * @param  string  $type
     * @param  array  $new_categories
     * @return void
     */
    public function syncCat($business_id, $data, $type, $new_categories = [])
    {

        //woocommerce api client object
        $woocommerce = $this->woo_client($business_id);
        $count = 0;
        foreach (array_chunk($data, 99) as $chunked_array) {
            $sync_data = [];
            $sync_data[$type] = $chunked_array;
            //Batch update categories

            $response = $woocommerce->post('products/categories/batch', $sync_data);

            //update woocommerce_cat_id
            if (! empty($response->create)) {
                foreach ($response->create as $key => $value) {
                    $new_category = $new_categories[$count];
                    if ($value->id != 0) {
                        $new_category->woocommerce_cat_id = $value->id;
                    } else {
                        if (! empty($value->error->data->resource_id)) {
                            $new_category->woocommerce_cat_id = $value->error->data->resource_id;
                        }
                    }
                    $new_category->save();
                    $count++;
                }
            }
        }
    }

    /**
     * Synchronizes pos categories with Woocommerce categories
     *
     * @param  int  $business_id
     * @return void
     */
    public function syncCategories($business_id, $user_id)
    {
        $last_synced = $this->getLastSync($business_id, 'categories', false);

        //Update parent categories
        $query = Category::where('business_id', $business_id)
            ->where('category_type', 'product')
            ->where('parent_id', 0);

        //Limit query to last sync
        if (! empty($last_synced)) {
            $query->where('updated_at', '>', $last_synced);
        }

        $categories = $query->get();

        $category_data = [];
        $new_categories = [];
        $created_data = [];
        $updated_data = [];
        foreach ($categories as $category) {
            if (empty($category->woocommerce_cat_id)) {
                $category_data['create'][] = [
                    'name' => $category->name,
                ];
                $new_categories[] = $category;
                $created_data[] = $category->name;
            } else {
                $category_data['update'][] = [
                    'id' => $category->woocommerce_cat_id,
                    'name' => $category->name,
                ];
                $updated_data[] = $category->name;
            }
        }

        if (! empty($category_data['create'])) {
            $this->syncCat($business_id, $category_data['create'], 'create', $new_categories);
        }
        if (! empty($category_data['update'])) {
            $this->syncCat($business_id, $category_data['update'], 'update', $new_categories);
        }

        //Sync child categories
        $query2 = Category::where('business_id', $business_id)
            ->where('category_type', 'product')
            ->where('parent_id', '!=', 0);
        //Limit query to last sync
        if (! empty($last_synced)) {
            $query2->where('updated_at', '>', $last_synced);
        }

        $child_categories = $query2->get();

        $cat_id_woocommerce_id = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->where('category_type', 'product')
            ->pluck('woocommerce_cat_id', 'id')
            ->toArray();

        $category_data = [];
        $new_categories = [];
        foreach ($child_categories as $category) {
            if (empty($cat_id_woocommerce_id[$category->parent_id])) {
                continue;
            }

            if (empty($category->woocommerce_cat_id)) {
                $category_data['create'][] = [
                    'name' => $category->name,
                    'parent' => $cat_id_woocommerce_id[$category->parent_id],
                ];
                $new_categories[] = $category;
                $created_data[] = $category->name;
            } else {
                $category_data['update'][] = [
                    'id' => $category->woocommerce_cat_id,
                    'name' => $category->name,
                    'parent' => $cat_id_woocommerce_id[$category->parent_id],
                ];
                $updated_data[] = $category->name;
            }
        }

        if (! empty($category_data['create'])) {
            $this->syncCat($business_id, $category_data['create'], 'create', $new_categories);
        }
        if (! empty($category_data['update'])) {
            $this->syncCat($business_id, $category_data['update'], 'update', $new_categories);
        }

        //Create log
        if (! empty($created_data)) {
            $this->createSyncLog($business_id, $user_id, 'categories', 'created', $created_data);
        }
        if (! empty($updated_data)) {
            $this->createSyncLog($business_id, $user_id, 'categories', 'updated', $updated_data);
        }
        if (empty($created_data) && empty($updated_data)) {
            $this->createSyncLog($business_id, $user_id, 'categories');
        }
    }

    /**
     * Sync Categories from Woocommerce to ERP
     *
     * @param  int  $business_id
     * @param  int  $user_id
     * @return void
     */
    public function syncCategoriesFromWoocommerce($business_id, $user_id, $offset = 0, $limit = 500)
    {
        try {
            $woocommerce_api_settings = $this->get_api_settings($business_id);

            if (empty($woocommerce_api_settings->woocommerce_app_url)) {
                throw new \Exception('WooCommerce API settings not configured');
            }

            // Calculate page number based on offset and limit
            $page = floor($offset / $limit) + 1;

            // Get categories using the custom WordPress plugin endpoint with chunking
            $params = [
                'page' => $page,
                'per_page' => $limit,
                'include_empty' => 'true'
            ];

            $woo_categories_response = $this->getCategoriesFromWordPressPlugin($business_id, $params);
            $categories = $woo_categories_response['data'] ?? [];
            $pagination = $woo_categories_response['pagination'] ?? [];

            // Debug logging
            Log::info('WooCommerce Category Sync Debug - ERP received response', [
                'page' => $page,
                'per_page' => $limit,
                'categories_count' => count($categories),
                'pagination' => $pagination
            ]);

            $created_categories = [];
            $updated_categories = [];
            $skipped_categories = [];

            // First pass: Create all categories without parent relationships
            foreach ($categories as $woo_category) {
                try {
                    $category_data = [
                        'name' => $woo_category['name'],
                        'description' => $woo_category['description'] ?? '',
                        'business_id' => $business_id,
                        'woocommerce_cat_id' => $woo_category['id'],
                        'category_type' => 'product',
                        'created_by' => $user_id,
                        'visibility' => $woo_category['visibility']??'public',
                    ];

                    // Check if category already exists by WooCommerce ID
                    $existing_category = Category::where('business_id', $business_id)
                        ->where('woocommerce_cat_id', $woo_category['id'])
                        ->first();

                    if (!$existing_category) {
                        // Check if category exists by name
                        $existing_category = Category::where('business_id', $business_id)
                            ->where('name', $woo_category['name'])
                            ->first();

                        if ($existing_category) {
                            // Update existing category with WooCommerce ID
                            $existing_category->update([
                                'woocommerce_cat_id' => $woo_category['id'],
                                'description' => $woo_category['description'] ?? $existing_category->description
                            ]);
                            $updated_categories[] = $woo_category['name'];
                        } else {
                            // Create new category
                            $category = Category::create($category_data);
                            $created_categories[] = $woo_category['name'];
                        }
                    } else {
                        // Update existing category
                        $existing_category->update([
                            'name' => $woo_category['name'],
                            'description' => $woo_category['description'] ?? $existing_category->description
                        ]);
                        $updated_categories[] = $woo_category['name'];
                    }
                } catch (\Exception $e) {
                    Log::error('Error processing WooCommerce category: ' . $woo_category['name'] . ' - ' . $e->getMessage());
                    $skipped_categories[] = $woo_category['name'];
                }
            }

            // Second pass: Set up parent-child relationships
            foreach ($categories as $woo_category) {
                if (!empty($woo_category['parent_id']) && $woo_category['parent_id'] > 0) {
                    try {
                        // Find the child category
                        $child_category = Category::where('business_id', $business_id)
                            ->where('woocommerce_cat_id', $woo_category['id'])
                            ->first();

                        // Find the parent category
                        $parent_category = Category::where('business_id', $business_id)
                            ->where('woocommerce_cat_id', $woo_category['parent_id'])
                            ->first();

                        if ($child_category && $parent_category) {
                            $child_category->update(['parent_id' => $parent_category->id]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error setting parent-child relationship for category: ' . $woo_category['name'] . ' - ' . $e->getMessage());
                    }
                }
            }

            // Sync categories to webcategories (many-to-many)
            $this->syncCategoriesToWebCategories($business_id);

            $result = [
                'success' => true,
                'message' => 'Categories synced successfully',
                'total_categories' => count($categories),
                'created_categories' => $created_categories,
                'updated_categories' => $updated_categories,
                'skipped_categories' => $skipped_categories,
                'has_more' => $pagination['has_more'] ?? false,
                'next_offset' => $offset + $limit,
                'current_offset' => $offset,
                'limit' => $limit
            ];

            Log::info('WooCommerce to ERP Category Sync completed', $result);
            return $result;
        } catch (\Exception $e) {
            Log::error('Error in syncCategoriesFromWoocommerce: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sync Brands from Woocommerce to ERP
     *
     * @param  int  $business_id
     * @param  int  $user_id
     * @return void
     */
    public function syncBrandsFromWoocommerce($business_id, $user_id)
    {
        try {
            $woocommerce_api_settings = $this->get_api_settings($business_id);
            if (empty($woocommerce_api_settings->woocommerce_app_url) || empty($woocommerce_api_settings->woocommerce_consumer_key) || empty($woocommerce_api_settings->woocommerce_consumer_secret)) {
                throw new \Exception('WooCommerce API settings not configured');
            }

            // Get brands from WordPress plugin endpoint
            $api_url = $woocommerce_api_settings->woocommerce_app_url . '/wp-json/phantasm-erp/v1/brands';
            $consumer_key = $woocommerce_api_settings->woocommerce_consumer_key;
            $consumer_secret = $woocommerce_api_settings->woocommerce_consumer_secret;

            $response = Http::withHeaders([
                // 'Authorization' => 'Basic ' . base64_encode($consumer_key . ':' . $consumer_secret),
                'Content-Type' => 'application/json',
            ])->get($api_url);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch brands from WordPress plugin: ' . $response->body());
            }

            $response_data = $response->json();

            if (!isset($response_data['success']) || !$response_data['success']) {
                throw new \Exception('Invalid response from WordPress plugin: ' . json_encode($response_data));
            }

            $brands_data = $response_data['data'] ?? [];
            $created_brands = [];
            $updated_brands = [];
            $skipped_brands = [];

            foreach ($brands_data as $brand_data) {
                try {
                    // Skip empty brands
                    if (empty($brand_data['name']) || $brand_data['id'] == 0) {
                        continue;
                    }

                    // Check if brand already exists by WooCommerce ID
                    $existing_brand = \App\Brands::where('business_id', $business_id)
                        ->where('slug', $brand_data['slug'])
                        ->first();

                    if (!$existing_brand) {
                        // Check if brand exists by name
                        $existing_brand = \App\Brands::where('business_id', $business_id)
                            ->where('name', $brand_data['name'])
                            ->first();

                        if ($existing_brand) {
                            // Update existing brand with WooCommerce ID
                            // check logo is null ?
                            $logo = $existing_brand->logo ?? null;
                            if ($logo == null) {
                                // check thumbnail_id is not null ?
                                if ($brand_data['thumbnail_id'] != null) {
                                    $logo = $this->downloadAndStoreBrandImage($existing_brand, ['id' => $brand_data['thumbnail_id']]);
                                }
                            }
                            $existing_brand->update([
                                'slug' => $brand_data['slug'] ?? $existing_brand->slug,
                                'visiblity' => $brand_data['visiblity'] ?? $existing_brand->visiblity,
                                'logo' => $logo
                            ]);
                            $updated_brands[] = $brand_data['name'];
                        } else {
                            // Create new brand
                            $logo = null;
                            if ($brand_data['thumbnail_id'] != null) {
                                $logo = $this->downloadAndStoreBrandImage($existing_brand, ['id' => $brand_data['thumbnail_id']]);
                            }
                            $brand = \App\Brands::create([
                                'name' => $brand_data['name'],
                                'slug' => $brand_data['slug'] ?? 'NA',
                                'visiblity' => $brand_data['visiblity'] ?? 'public',
                                'business_id' => $business_id,
                                'created_by' => $user_id,
                                'logo' => $logo
                            ]);
                            $created_brands[] = $brand_data['name'];
                        }
                    } else {
                        // Update existing brand
                        $logo = $existing_brand->logo ?? null;
                        if ($logo == null) {
                            if ($brand_data['thumbnail_id'] != null) {
                                Log::info('Downloading brand image for brand: ' . $brand_data['name'] . ' - ' . $brand_data['thumbnail_id']);
                                $logo = $this->downloadAndStoreBrandImage($existing_brand, ['id' => $brand_data['thumbnail_id']]);
                            }
                        }
                        $existing_brand->update([
                            'name' => $brand_data['name'],
                            'slug' => $brand_data['slug'] ?? $existing_brand->slug,
                            'visiblity' => $brand_data['visiblity'] ?? $existing_brand->visiblity
                        ]);
                        $updated_brands[] = $brand_data['name'];
                    }
                } catch (\Exception $e) {
                    Log::error('Error processing WooCommerce brand: ' . $brand_data['name'] . ' - ' . $e->getMessage());
                    $skipped_brands[] = $brand_data['name'];
                }
            }

            $result = [
                'success' => true,
                'message' => 'Brands synced successfully',
                'total_brands' => count($brands_data),
                'created_brands' => $created_brands,
                'updated_brands' => $updated_brands,
                'skipped_brands' => $skipped_brands
            ];

            Log::info('WooCommerce to ERP Brand Sync completed', $result);
            return $result;
        } catch (\Exception $e) {
            Log::error('Error in syncBrandsFromWoocommerce: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sync categories to webcategories (many-to-many relationship)
     *
     * @param int $business_id
     * @return void
     */
    private function syncCategoriesToWebCategories($business_id)
    {
        try {
            // $categories = Category::where('business_id', $business_id)->get();

            // foreach ($categories as $category) {
            //     // Check if webcategory exists
            //     $webcategory = WebCategory::where('name', $category->name)
            //         ->where('business_id', $business_id)
            //         ->first();

            //     if (!$webcategory) {
            //         $webcategory = WebCategory::create([
            //             'name' => $category->name,
            //             'description' => $category->description,
            //             'business_id' => $business_id,
            //             'created_by' => $category->created_by
            //         ]);
            //     }

            //     // Sync to webcategories table (many-to-many)
            //     $category->webcategories()->syncWithoutDetaching([$webcategory->id]);
            // }
        } catch (\Exception $e) {
            Log::error('Error syncing categories to webcategories: ' . $e->getMessage());
        }
    }

    /**
     * Download and store brand image
     *
     * @param Brand $brand
     * @param array $image_data
     * @return void
     */
    private function downloadAndStoreBrandImage($brand, $image_data)
    {
        try {
            if (empty($image_data['src'])) {
                return;
            }

            $image_url = $image_data['src'];
            $image_content = Http::get($image_url)->body();

            if (empty($image_content)) {
                return;
            }

            $filename = 'brand_' . $brand->id . '_' . time() . '.jpg';
            $upload_path = 'uploads/brands/';

            if (!file_exists(public_path($upload_path))) {
                mkdir(public_path($upload_path), 0755, true);
            }

            $file_path = public_path($upload_path . $filename);
            file_put_contents($file_path, $image_content);

            $brand->update(['image' => $upload_path . $filename]);
        } catch (\Exception $e) {
            Log::error('Error downloading brand image for brand: ' . $brand->name . ' - ' . $e->getMessage());
        }
    }

    /**
     * Synchronizes pos products with Woocommerce products
     *
     * @param  int  $business_id
     * @param  int  $user_id
     * @param  string  $sync_type
     * @param  int  $limit
     * @param  int  $page
     * @return array
     */
    public function syncProducts($business_id, $user_id, $sync_type, $limit = 100, $page = 0)
    {
        //$limit is zero for console command
        if ($page == 0 || $limit == 0) {
            //Sync Categories
            $this->syncCategories($business_id, $user_id);

            //Sync variation attributes
            $this->syncVariationAttributes($business_id);

            if ($limit > 0) {
                request()->session()->forget('last_product_synced');
            }
        }

        $last_synced = ! empty(session('last_product_synced')) ? session('last_product_synced') : $this->getLastSync($business_id, 'all_products', false);
        //store last_synced if page is 0
        if ($page == 0) {
            session(['last_product_synced' => $last_synced]);
        }

        $woocommerce_api_settings = $this->get_api_settings($business_id);
        $created_data = [];
        $updated_data = [];

        $business_location_id = $woocommerce_api_settings->location_id;
        $offset = $page * $limit;
        $query = Product::where('business_id', $business_id)
            ->whereIn('type', ['single', 'variable'])
            ->where('woocommerce_disable_sync', 0)
            ->with([
                'variations',
                'category',
                'sub_category',
                'variations.variation_location_details',
                'variations.product_variation',
                'variations.product_variation.variation_template',
            ]);

        if ($limit > 0) {
            $query->limit($limit)
                ->offset($offset);
        }

        if ($sync_type == 'new') {
            $query->whereNull('woocommerce_product_id');
        }

        //Select products only from selected location
        if (! empty($business_location_id)) {
            $query->ForLocation($business_location_id);
        }

        $all_products = $query->get();

        Log::info('Products to sync', [
            'total_products' => count($all_products),
            'sync_type' => $sync_type,
            'business_location_id' => $business_location_id,
            'products' => $all_products->pluck('sku')->toArray()
        ]);

        $product_data = [];
        $new_products = [];
        $updated_products = [];

        if (count($all_products) == 0) {
            request()->session()->forget('last_product_synced');
        }

        // Get all existing WooCommerce products
        $woocommerce = $this->woo_client($business_id);
        $existing_woo_products = [];
        try {
            $woo_products = $woocommerce->get('products', ['per_page' => 100]);
            foreach ($woo_products as $woo_product) {
                $existing_woo_products[$woo_product->id] = true;
            }
            Log::info('Existing WooCommerce products', [
                'count' => count($existing_woo_products),
                'ids' => array_keys($existing_woo_products)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching WooCommerce products: ' . $e->getMessage());
        }

        foreach ($all_products as $product) {
            Log::info('Processing product', [
                'sku' => $product->sku,
                'type' => $product->type,
                'woocommerce_product_id' => $product->woocommerce_product_id,
                'variations_count' => $product->variations->count()
            ]);

            //Skip product if last updated is less than last sync
            $last_updated = $product->updated_at;
            //check last stock updated
            $last_stock_updated = $this->getLastStockUpdated($business_location_id, $product->id);

            if (! empty($last_stock_updated)) {
                $last_updated = strtotime($last_stock_updated) > strtotime($last_updated) ?
                    $last_stock_updated : $last_updated;
            }
            if (! empty($product->woocommerce_product_id) && ! empty($last_synced) && strtotime($last_updated) < strtotime($last_synced)) {
                Log::info('Skipping product - not updated since last sync', [
                    'sku' => $product->sku,
                    'last_updated' => $last_updated,
                    'last_synced' => $last_synced
                ]);
                continue;
            }

            // Check if product exists in WooCommerce
            if (!empty($product->woocommerce_product_id) && !isset($existing_woo_products[$product->woocommerce_product_id])) {
                // Product was deleted from WooCommerce, reset the ID to recreate it
                $product->woocommerce_product_id = null;
                $product->save();
                Log::info('Product deleted from WooCommerce, will be recreated', [
                    'product_id' => $product->id,
                    'sku' => $product->sku
                ]);
            }

            //Set common data
            $array = [
                'type' => $product->type == 'single' ? 'simple' : 'variable',
                'sku' => $product->sku,
            ];

            $manage_stock = false;
            if ($product->enable_stock == 1 && $product->type == 'single') {
                $manage_stock = true;
            }

            //Get details from first variation for single product only
            $first_variation = $product->variations->first();
            if (empty($first_variation)) {
                Log::warning('Skipping product - no variations found', [
                    'sku' => $product->sku,
                    'type' => $product->type
                ]);
                continue;
            }
            $price = $woocommerce_api_settings->product_tax_type == 'exc' ? $first_variation->default_sell_price : $first_variation->sell_price_inc_tax;

            if (! empty($woocommerce_api_settings->default_selling_price_group)) {
                $group_prices = $this->productUtil->getVariationGroupPrice($first_variation->id, $woocommerce_api_settings->default_selling_price_group, $product->tax_id);

                $price = $woocommerce_api_settings->product_tax_type == 'exc' ? $group_prices['price_exc_tax'] : $group_prices['price_inc_tax'];
            }

            //Set product stock
            $qty_available = 0;
            if ($product->enable_stock == 1) {
                $variation_location_details = $first_variation->variation_location_details;
                foreach ($variation_location_details as $vld) {
                    if ($vld->location_id == $business_location_id) {
                        $qty_available = $vld->in_stock_qty;
                    }
                }
            }

            //Set product category
            $product_cat = [];
            if (! empty($product->category)) {
                $product_cat[] = ['id' => $product->category->woocommerce_cat_id];
            }
            if (! empty($product->sub_category)) {
                $product_cat[] = ['id' => $product->sub_category->woocommerce_cat_id];
            }

            //set attributes for variable products
            if ($product->type == 'variable') {
                $variation_attr_data = [];

                foreach ($product->variations as $variation) {
                    if (! empty($variation->product_variation->variation_template->woocommerce_attr_id)) {
                        $woocommerce_attr_id = $variation->product_variation->variation_template->woocommerce_attr_id;
                        $variation_attr_data[$woocommerce_attr_id][] = $variation->name;
                    }
                }

                foreach ($variation_attr_data as $key => $value) {
                    $array['attributes'][] = [
                        'id' => $key,
                        'variation' => true,
                        'visible' => true,
                        'options' => $value,
                    ];
                }
            }

            $sync_description_as = ! empty($woocommerce_api_settings->sync_description_as) ? $woocommerce_api_settings->sync_description_as : 'long';

            if (empty($product->woocommerce_product_id)) {
                Log::info('Preparing to create product in WooCommerce', [
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'type' => $product->type
                ]);

                $array['tax_class'] = ! empty($woocommerce_api_settings->default_tax_class) ?
                    $woocommerce_api_settings->default_tax_class : 'standard';

                //assign category
                if (in_array('category', $woocommerce_api_settings->product_fields_for_create)) {
                    if (! empty($product_cat)) {
                        $array['categories'] = $product_cat;
                    }
                }

                if (in_array('weight', $woocommerce_api_settings->product_fields_for_create)) {
                    $array['weight'] = $this->formatDecimalPoint($product->weight);
                }

                //sync product description
                if (in_array('description', $woocommerce_api_settings->product_fields_for_create)) {
                    if ($sync_description_as == 'long') {
                        $array['description'] = $product->product_description;
                    } elseif ($sync_description_as == 'short') {
                        $array['short_description'] = $product->product_description;
                    } else {
                        $array['description'] = $product->product_description;
                        $array['short_description'] = $product->product_description;
                    }
                }

                //Set product image url
                //If media id is set use media id else use image src
                if (! empty($product->image) && in_array('image', $woocommerce_api_settings->product_fields_for_create)) {
                    if ($this->isValidImage($product->image_path)) {
                        $array['images'] = ! empty($product->woocommerce_media_id) ? [['id' => $product->woocommerce_media_id]] : [['src' => $product->image_url]];
                    }
                }

                //assign quantity and price if single product
                if ($product->type == 'single') {
                    $array['manage_stock'] = $manage_stock;
                    if (in_array('quantity', $woocommerce_api_settings->product_fields_for_create)) {
                        $array['stock_quantity'] = $this->formatDecimalPoint($qty_available, 'quantity');
                    } else {
                        //set manage stock and in_stock if quantity disabled
                        if (isset($woocommerce_api_settings->manage_stock_for_create)) {
                            if ($woocommerce_api_settings->manage_stock_for_create == 'true') {
                                $array['manage_stock'] = true;
                            } elseif ($woocommerce_api_settings->manage_stock_for_create == 'false') {
                                $array['manage_stock'] = false;
                            } else {
                                unset($array['manage_stock']);
                            }
                        }
                        if (isset($woocommerce_api_settings->in_stock_for_create)) {
                            if ($woocommerce_api_settings->in_stock_for_create == 'true') {
                                $array['in_stock'] = true;
                            } elseif ($woocommerce_api_settings->in_stock_for_create == 'false') {
                                $array['in_stock'] = false;
                            }
                        }
                    }

                    $array['regular_price'] = $this->formatDecimalPoint($price);
                }

                //assign name
                $array['name'] = $product->name;

                $product_data['create'][] = $array;
                $new_products[] = $product;

                $created_data[] = $product->sku;

                Log::info('Product added to create batch', [
                    'sku' => $product->sku,
                    'data' => $array
                ]);
            } else {
                $array['id'] = $product->woocommerce_product_id;
                //assign category
                if (in_array('category', $woocommerce_api_settings->product_fields_for_update)) {
                    if (! empty($product_cat)) {
                        $array['categories'] = $product_cat;
                    }
                }

                if (in_array('weight', $woocommerce_api_settings->product_fields_for_update)) {
                    $array['weight'] = $this->formatDecimalPoint($product->weight);
                }

                //sync product description
                if (in_array('description', $woocommerce_api_settings->product_fields_for_update)) {
                    if ($sync_description_as == 'long') {
                        $array['description'] = $product->product_description;
                    } elseif ($sync_description_as == 'short') {
                        $array['short_description'] = $product->product_description;
                    } else {
                        $array['description'] = $product->product_description;
                        $array['short_description'] = $product->product_description;
                    }
                }

                //If media id is set use media id else use image src
                if (! empty($product->image) && in_array('image', $woocommerce_api_settings->product_fields_for_update)) {
                    if ($this->isValidImage($product->image_path)) {
                        $array['images'] = ! empty($product->woocommerce_media_id) ? [['id' => $product->woocommerce_media_id]] : [['src' => $product->image_url]];
                    }
                }

                if ($product->type == 'single') {
                    //assign quantity
                    $array['manage_stock'] = $manage_stock;
                    if (in_array('quantity', $woocommerce_api_settings->product_fields_for_update)) {
                        $array['stock_quantity'] = $this->formatDecimalPoint($qty_available, 'quantity');
                    } else {
                        //set manage stock and in_stock if quantity disabled
                        if (isset($woocommerce_api_settings->manage_stock_for_update)) {
                            if ($woocommerce_api_settings->manage_stock_for_update == 'true') {
                                $array['manage_stock'] = true;
                            } elseif ($woocommerce_api_settings->manage_stock_for_update == 'false') {
                                $array['manage_stock'] = false;
                            } else {
                                unset($array['manage_stock']);
                            }
                        }
                        if (isset($woocommerce_api_settings->in_stock_for_update)) {
                            if ($woocommerce_api_settings->in_stock_for_update == 'true') {
                                $array['in_stock'] = true;
                            } elseif ($woocommerce_api_settings->in_stock_for_update == 'false') {
                                $array['in_stock'] = false;
                            }
                        }
                    }
                    //assign price
                    if (in_array('price', $woocommerce_api_settings->product_fields_for_update)) {
                        $array['regular_price'] = $this->formatDecimalPoint($price);
                    }
                }

                //assign name
                if (in_array('name', $woocommerce_api_settings->product_fields_for_update)) {
                    $array['name'] = $product->name;
                }

                $product_data['update'][] = $array;
                $updated_data[] = $product->sku;
                $updated_products[] = $product;
            }
        }

        $create_response = [];
        $update_response = [];

        if (! empty($product_data['create'])) {
            $create_response = $this->syncProd($business_id, $product_data['create'], 'create', $new_products);
        }
        if (! empty($product_data['update'])) {
            $update_response = $this->syncProd($business_id, $product_data['update'], 'update', $updated_products);
        }
        $new_woocommerce_product_ids = array_merge($create_response, $update_response);

        //Create log
        if (! empty($created_data)) {
            if ($sync_type == 'new') {
                $this->createSyncLog($business_id, $user_id, 'new_products', 'created', $created_data);
            } else {
                $this->createSyncLog($business_id, $user_id, 'all_products', 'created', $created_data);
            }
        }
        if (! empty($updated_data)) {
            $this->createSyncLog($business_id, $user_id, 'all_products', 'updated', $updated_data);
        }

        //Sync variable product variations
        $this->syncProductVariations($business_id, $sync_type, $new_woocommerce_product_ids);

        if (empty($created_data) && empty($updated_data)) {
            if ($sync_type == 'new') {
                $this->createSyncLog($business_id, $user_id, 'new_products');
            } else {
                $this->createSyncLog($business_id, $user_id, 'all_products');
            }
        }

        return $all_products;
    }

    /**
     * Synchronizes pos products with Woocommerce products
     *
     * @param  int  $business_id
     * @param  array  $data
     * @param  string  $type
     * @param  array  $new_products
     * @return array
     */
    public function syncProd($business_id, $data, $type, $new_products)
    {
        //woocommerce api client object
        $woocommerce = $this->woo_client($business_id);

        $new_woocommerce_product_ids = [];
        $count = 0;
        foreach (array_chunk($data, 99) as $chunked_array) {
            $sync_data = [];
            $sync_data[$type] = $chunked_array;
            $response = $woocommerce->post('products/batch', $sync_data);
            if (! empty($response->create)) {
                foreach ($response->create as $key => $value) {
                    $new_product = $new_products[$count];
                    if ($value->id != 0) {
                        $new_product->woocommerce_product_id = $value->id;
                        //Sync woocommerce media id
                        $new_product->woocommerce_media_id = ! empty($value->images[0]->id) ? $value->images[0]->id : null;
                    } else {
                        if (! empty($value->error->data->resource_id)) {
                            $new_product->woocommerce_product_id = $value->error->data->resource_id;
                        }
                    }
                    $new_product->save();

                    $new_woocommerce_product_ids[] = $new_product->woocommerce_product_id;
                    $count++;
                }
            }

            if (! empty($response->update)) {
                foreach ($response->update as $key => $value) {
                    $updated_product = $new_products[$count];
                    if ($value->id != 0) {
                        //Sync woocommerce media id
                        $updated_product->woocommerce_media_id = ! empty($value->images[0]->id) ? $value->images[0]->id : null;
                        $updated_product->save();
                    }
                    $new_woocommerce_product_ids[] = $updated_product->woocommerce_product_id;
                    $count++;
                }
            }
        }

        return $new_woocommerce_product_ids;
    }

    /**
     * Synchronizes pos variation templates with Woocommerce product attributes
     *
     * @param  int  $business_id
     * @return void
     */
    public function syncVariationAttributes($business_id)
    {
        $woocommerce = $this->woo_client($business_id);
        $query = VariationTemplate::where('business_id', $business_id);

        $attributes = $query->get();
        $data = [];
        $new_attrs = [];
        foreach ($attributes as $attr) {
            if (empty($attr->woocommerce_attr_id)) {
                $data['create'][] = ['name' => $attr->name];
                $new_attrs[] = $attr;
            } else {
                $data['update'][] = [
                    'name' => $attr->name,
                    'id' => $attr->woocommerce_attr_id,
                ];
            }
        }

        if (! empty($data)) {
            $response = $woocommerce->post('products/attributes/batch', $data);

            //update woocommerce_attr_id
            if (! empty($response->create)) {
                foreach ($response->create as $key => $value) {
                    $new_attr = $new_attrs[$key];
                    if ($value->id != 0) {
                        $new_attr->woocommerce_attr_id = $value->id;
                    } else {
                        $all_attrs = $woocommerce->get('products/attributes');
                        foreach ($all_attrs as $attr) {
                            if (strtolower($attr->name) == strtolower($new_attr->name)) {
                                $new_attr->woocommerce_attr_id = $attr->id;
                            }
                        }
                    }
                    $new_attr->save();
                }
            }
        }
    }

    /**
     * Synchronizes pos products variations with Woocommerce product variations
     *
     * @param  int  $business_id
     * @param  string  $sync_type
     * @param  array  $new_woocommerce_product_ids (woocommerce product id of newly created products to sync)
     * @return void
     */
    public function syncProductVariations($business_id, $sync_type = 'all', $new_woocommerce_product_ids = [])
    {
        //woocommerce api client object
        $woocommerce = $this->woo_client($business_id);
        $woocommerce_api_settings = $this->get_api_settings($business_id);

        $query = Product::where('business_id', $business_id)
            ->where('type', 'variable')
            ->where('woocommerce_disable_sync', 0)
            ->with([
                'variations',
                'variations.variation_location_details',
                'variations.product_variation',
                'variations.product_variation.variation_template',
            ]);

        $query->whereIn('woocommerce_product_id', $new_woocommerce_product_ids);

        $variable_products = $query->get();
        $business_location_id = $woocommerce_api_settings->location_id;
        foreach ($variable_products as $product) {

            //Skip product if last updated is less than last sync
            $last_updated = $product->updated_at;

            $last_stock_updated = $this->getLastStockUpdated($business_location_id, $product->id);

            if (! empty($last_stock_updated)) {
                $last_updated = strtotime($last_stock_updated) > strtotime($last_updated) ?
                    $last_stock_updated : $last_updated;
            }
            if (! empty($last_synced) && strtotime($last_updated) < strtotime($last_synced)) {
                continue;
            }

            $variations = $product->variations;

            $variation_data = [];
            $new_variations = [];
            $updated_variations = [];
            foreach ($variations as $variation) {
                $variation_arr = [
                    'sku' => $variation->sub_sku,
                ];

                $manage_stock = false;
                if ($product->enable_stock == 1) {
                    $manage_stock = true;
                }

                if (! empty($variation->product_variation->variation_template->woocommerce_attr_id)) {
                    $variation_arr['attributes'][] = [
                        'id' => $variation->product_variation->variation_template->woocommerce_attr_id,
                        'option' => $variation->name,
                    ];
                }

                $price = $woocommerce_api_settings->product_tax_type == 'exc' ? $variation->default_sell_price : $variation->sell_price_inc_tax;

                if (! empty($woocommerce_api_settings->default_selling_price_group)) {
                    $group_prices = $this->productUtil->getVariationGroupPrice($variation->id, $woocommerce_api_settings->default_selling_price_group, $product->tax_id);

                    $price = $woocommerce_api_settings->product_tax_type == 'exc' ? $group_prices['price_exc_tax'] : $group_prices['price_inc_tax'];
                }

                //Set product stock
                $qty_available = 0;
                if ($product->enable_stock == 1) {
                    $variation_location_details = $variation->variation_location_details;
                    foreach ($variation_location_details as $vld) {
                        if ($vld->location_id == $business_location_id) {
                            $qty_available = $vld->in_stock_qty;
                        }
                    }
                }

                if (empty($variation->woocommerce_variation_id)) {
                    $variation_arr['manage_stock'] = $manage_stock;
                    if (in_array('quantity', $woocommerce_api_settings->product_fields_for_create)) {
                        $variation_arr['stock_quantity'] = $this->formatDecimalPoint($qty_available, 'quantity');
                    } else {
                        //set manage stock and in_stock if quantity disabled
                        if (isset($woocommerce_api_settings->manage_stock_for_create)) {
                            if ($woocommerce_api_settings->manage_stock_for_create == 'true') {
                                $variation_arr['manage_stock'] = true;
                            } elseif ($woocommerce_api_settings->manage_stock_for_create == 'false') {
                                $variation_arr['manage_stock'] = false;
                            } else {
                                unset($variation_arr['manage_stock']);
                            }
                        }
                        if (isset($woocommerce_api_settings->in_stock_for_create)) {
                            if ($woocommerce_api_settings->in_stock_for_create == 'true') {
                                $variation_arr['in_stock'] = true;
                            } elseif ($woocommerce_api_settings->in_stock_for_create == 'false') {
                                $variation_arr['in_stock'] = false;
                            }
                        }
                    }

                    //Set variation images
                    //If media id is set use media id else use image src
                    if (! empty($variation->media) && count($variation->media) > 0 && in_array('image', $woocommerce_api_settings->product_fields_for_create)) {
                        $url = $variation->media->first()->display_url;
                        $path = $variation->media->first()->display_path;
                        $woocommerce_media_id = $variation->media->first()->woocommerce_media_id;
                        if ($this->isValidImage($path)) {
                            $variation_arr['image'] = ! empty($woocommerce_media_id) ? ['id' => $woocommerce_media_id] : ['src' => $url];
                        }
                    }

                    $variation_arr['regular_price'] = $this->formatDecimalPoint($price);
                    $new_variations[] = $variation;

                    $variation_data['create'][] = $variation_arr;
                } else {
                    $variation_arr['id'] = $variation->woocommerce_variation_id;
                    $variation_arr['manage_stock'] = $manage_stock;
                    if (in_array('quantity', $woocommerce_api_settings->product_fields_for_update)) {
                        $variation_arr['stock_quantity'] = $this->formatDecimalPoint($qty_available, 'quantity');
                    } else {
                        //set manage stock and in_stock if quantity disabled
                        if (isset($woocommerce_api_settings->manage_stock_for_update)) {
                            if ($woocommerce_api_settings->manage_stock_for_update == 'true') {
                                $variation_arr['manage_stock'] = true;
                            } elseif ($woocommerce_api_settings->manage_stock_for_update == 'false') {
                                $variation_arr['manage_stock'] = false;
                            } else {
                                unset($variation_arr['manage_stock']);
                            }
                        }
                        if (isset($woocommerce_api_settings->in_stock_for_update)) {
                            if ($woocommerce_api_settings->in_stock_for_update == 'true') {
                                $variation_arr['in_stock'] = true;
                            } elseif ($woocommerce_api_settings->in_stock_for_update == 'false') {
                                $variation_arr['in_stock'] = false;
                            }
                        }
                    }

                    //Set variation images
                    //If media id is set use media id else use image src
                    if (! empty($variation->media) && count($variation->media) > 0 && in_array('image', $woocommerce_api_settings->product_fields_for_update)) {
                        $url = $variation->media->first()->display_url;
                        $path = $variation->media->first()->display_path;
                        $woocommerce_media_id = $variation->media->first()->woocommerce_media_id;
                        if ($this->isValidImage($path)) {
                            $variation_arr['image'] = ! empty($woocommerce_media_id) ? ['id' => $woocommerce_media_id] : ['src' => $url];
                        }
                    }

                    //assign price
                    if (in_array('price', $woocommerce_api_settings->product_fields_for_update)) {
                        $variation_arr['regular_price'] = $this->formatDecimalPoint($price);
                    }

                    $variation_data['update'][] = $variation_arr;
                    $updated_variations[] = $variation;
                }
            }

            if (! empty($variation_data)) {
                $response = $woocommerce->post('products/' . $product->woocommerce_product_id . '/variations/batch', $variation_data);

                //update woocommerce_variation_id
                if (! empty($response->create)) {
                    foreach ($response->create as $key => $value) {
                        $new_variation = $new_variations[$key];
                        if ($value->id != 0) {
                            $new_variation->woocommerce_variation_id = $value->id;
                            $media = $new_variation->media->first();
                            if (! empty($media)) {
                                $media->woocommerce_media_id = ! empty($value->image->id) ? $value->image->id : null;
                                $media->save();
                            }
                        } else {
                            if (! empty($value->error->data->resource_id)) {
                                $new_variation->woocommerce_variation_id = $value->error->data->resource_id;
                            }
                        }
                        $new_variation->save();
                    }
                }

                //Update media id if changed from woocommerce site
                if (! empty($response->update)) {
                    foreach ($response->update as $key => $value) {
                        $updated_variation = $updated_variations[$key];
                        if ($value->id != 0) {
                            $media = $updated_variation->media->first();
                            if (! empty($media)) {
                                $media->woocommerce_media_id = ! empty($value->image->id) ? $value->image->id : null;
                                $media->save();
                            }
                        }
                    }
                }
            }
        }
    }

    // ----------------------------- End of Product Variations Synchronization -----------------------------





    // ----------------------------- Start of Sales Order From WooCommerce to ERP -----------------------------
    /**
     * Synchronizes Woocommers Orders with POS sales
     *
     * @param  int  $business_id
     * @param  int  $user_id
     * @return void
     */
    public function syncOrders($business_id, $user_id)
    {
        $last_synced = $this->getLastSync($business_id, 'orders', false);
        $orders = $this->getAllResponse($business_id, 'orders');

        Log::info('Starting WooCommerce order sync', [
            'business_id' => $business_id,
            'total_orders' => count($orders),
            'last_synced' => $last_synced
        ]);

        $woocommerce_sells = Transaction::where('business_id', $business_id)
            ->whereNotNull('woocommerce_order_id')
            ->with('sell_lines', 'sell_lines.product', 'payment_lines')
            ->get();

        $new_orders = [];
        $updated_orders = [];

        $woocommerce_api_settings = $this->get_api_settings($business_id);
        $business = Business::find($business_id);

        $skipped_orders = ! empty($business->woocommerce_skipped_orders) ? json_decode($business->woocommerce_skipped_orders, true) : [];

        $business_data = [
            'id' => $business_id,
            'accounting_method' => $business->accounting_method,
            'location_id' => $woocommerce_api_settings->location_id,
            'pos_settings' => json_decode($business->pos_settings, true),
            'business' => $business,
        ];

        $created_data = [];
        $updated_data = [];
        $create_error_data = [];
        $update_error_data = [];

        foreach ($orders as $order) {
            Log::info('Processing WooCommerce order', [
                'order_id' => $order->id,
                'order_number' => $order->number,
                'status' => $order->status,
                'date_modified' => $order->date_modified
            ]);

            //Search if order already exists
            $sell = $woocommerce_sells->filter(function ($item) use ($order) {
                return $item->woocommerce_order_id == $order->id;
            })->first();

            // Only skip if:
            // 1. Order is auto-draft
            // 2. Order exists in ERP and was modified before last sync
            // 3. Order is in skipped orders list
            if (
                in_array($order->status, ['auto-draft']) ||
                (!empty($sell) && !empty($last_synced) && strtotime($order->date_modified) <= strtotime($last_synced)) ||
                in_array($order->id, $skipped_orders)
            ) {
                Log::info('Skipping order', [
                    'order_id' => $order->id,
                    'reason' => in_array($order->status, ['auto-draft']) ? 'auto-draft' : (!empty($sell) ? 'already synced' : 'in skipped orders'),
                    'last_synced' => $last_synced,
                    'date_modified' => $order->date_modified
                ]);
                continue;
            }

            try {
                if (empty($sell)) {
                    Log::info('Creating new sale from order', ['order_id' => $order->id]);
                    $created = $this->createNewSaleFromOrder($business_id, $user_id, $order, $business_data);
                    if ($created === true) {
                        $created_data[] = $order->id;
                        Log::info('Successfully created sale from order', ['order_id' => $order->id]);
                    } else {
                        $create_error_data[] = [
                            'id' => $order->id,
                            'error' => $created
                        ];
                        Log::error('Failed to create sale from order', [
                            'order_id' => $order->id,
                            'error' => $created
                        ]);
                    }
                } else {
                    Log::info('Updating existing sale from order', ['order_id' => $order->id]);
                    $updated = $this->updateSaleFromOrder($business_id, $user_id, $order, $sell, $business_data);
                    if ($updated === true) {
                        $updated_data[] = $order->id;
                        Log::info('Successfully updated sale from order', ['order_id' => $order->id]);
                    } else {
                        $update_error_data[] = [
                            'id' => $order->id,
                            'error' => $updated
                        ];
                        Log::error('Failed to update sale from order', [
                            'order_id' => $order->id,
                            'error' => $updated
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error syncing WooCommerce order: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'business_id' => $business_id,
                    'trace' => $e->getTraceAsString()
                ]);
                continue;
            }
        }

        //Create log
        if (! empty($created_data)) {
            $this->createSyncLog($business_id, $user_id, 'orders', 'created', $created_data, $create_error_data);
        }
        if (! empty($updated_data)) {
            $this->createSyncLog($business_id, $user_id, 'orders', 'updated', $updated_data, $update_error_data);
        }

        if (empty($created_data) && empty($updated_data)) {
            $error_data = array_merge($create_error_data, $update_error_data);
            $this->createSyncLog($business_id, $user_id, 'orders', null, [], $error_data);
        }

        Log::info('Completed WooCommerce order sync', [
            'business_id' => $business_id,
            'created' => count($created_data),
            'updated' => count($updated_data),
            'errors' => count($create_error_data) + count($update_error_data)
        ]);
    }

    /**
     * Creates new sales in POS from woocommerce order list
     *
     * @param  int  $business_id
     * @param  int  $user_id
     * @param  object  $order
     * @param  array  $business_data
     */
    public function createNewSaleFromOrder($business_id, $user_id, $order, $business_data)
    {
        $input = $this->formatOrderToSale($business_id, $user_id, $order);

        if (! empty($input['has_error'])) {
            return $input['has_error'];
        }

        $invoice_total = [
            'total_before_tax' => $order->total ?? 0,
            'tax' => 0,
        ];

        DB::beginTransaction();

        // Set type as sales_order
        $input['type'] = 'sales_order';
        $input['status'] = 'ordered'; // Ensure status is set
        $input['invoice_no'] = $order->number ?? $order->id ?? 'WC-' . time();
        $transaction = $this->transactionUtil->createSellTransaction($business_id, $input, $invoice_total, $user_id, false);
        $transaction->woocommerce_order_id = $order->id ?? null;
        $transaction->save();

        //Create sell lines
        $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id'], false, null, ['woocommerce_line_items_id' => 'line_item_id'], false);

        // Collect all variation IDs
        $variation_ids = collect($input['products'])->pluck('variation_id')->unique()->toArray();

        // Fetch all variations and their location details in a single query
        $variations = Variation::whereIn('id', $variation_ids)
            ->with(['variation_location_details' => function ($query) use ($input) {
                $query->where('location_id', $input['location_id']);
            }])
            ->get()
            ->keyBy('id');

        // Update stock quantities
        foreach ($input['products'] as $product) {
            $variation = $variations->get($product['variation_id']);
            if ($variation) {
                $variation_location_details = $variation->variation_location_details->first();
                if ($variation_location_details) {
                    if ($variation_location_details->in_stock_qty > 0) {
                        $variation_location_details->in_stock_qty -= $product['quantity'];
                        $variation_location_details->save();
                    } else {
                        Log::warning('Not enough stock for product', [
                            'product_id' => $variation->product_id,
                            'variation_id' => $variation->id,
                            'location_id' => $input['location_id'],
                            'quantity' => $product['quantity']
                        ]);
                    }
                }
            }
        }

        // Skip payment lines creation for specific payment methods that should remain as "due"
        $skip_payment_methods = ['managemore_onaccount'];
        $should_create_payment_lines = true;
        
        if (isset($order->payment_method) && in_array($order->payment_method, $skip_payment_methods)) {
            $should_create_payment_lines = false;
            Log::info('Skipping payment lines creation for payment method: ' . $order->payment_method);
        }
        
        if ($should_create_payment_lines) {
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $input['payment'], $business_id, $user_id, false);
        }

        // For sales orders, we don't decrease stock immediately
        if ($input['status'] == 'final') {
            //Update payment status based on WooCommerce order status and payment method
            $skip_payment_methods = ['managemore_onaccount'];
            
            if (isset($order->payment_method) && in_array($order->payment_method, $skip_payment_methods)) {
                // For specific payment methods, always keep as "due"
                $transaction->payment_status = 'due';
                Log::info('Setting payment status to "due" for payment method: ' . $order->payment_method);
            } elseif (($order->status ?? '') === 'completed' || ($order->status ?? '') === 'processing') {
                $transaction->payment_status = 'paid';
            } else {
                $transaction->payment_status = 'due';
            }
            $transaction->save();
        }

        if (isset($business_data['business'])) {
            $this->remove_from_skipped_orders($business_data['business'], $order->id ?? null);
        }

        DB::commit();

        // Dispatch SplitOrderJob to handle dropship order splitting
        // This will create child orders for different vendor types (WooCommerce, ERP Dropship, In-House)
        try {
            if (class_exists(\App\Jobs\SplitOrderJob::class)) {
                \App\Jobs\SplitOrderJob::dispatch($transaction->id);
                Log::info('SplitOrderJob dispatched for order', ['transaction_id' => $transaction->id]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to dispatch SplitOrderJob', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }

        return true;
    }

    /**
     * Formats Woocommerce order response to pos sale request
     *
     * @param  int  $business_id
     * @param  int  $user_id
     * @param  object  $order
     * @param  object  $sell = null
     */
    public function formatOrderToSale($business_id, $user_id, $order, $sell = null)
    {
        try {
            $woocommerce_api_settings = $this->get_api_settings($business_id);
            //Create sell line data
            $product_lines = [];

            //For updating sell lines
            $sell_lines = [];
            if (! empty($sell)) {
                $sell_lines = $sell->sell_lines;
            }

            // Log::info('Order line items', ['line_items' => $order->line_items]);

            foreach ($order->line_items as $product_line) {
                // Log::info('product_line',['product_line'=>$product_line]);
                $product = Product::where('business_id', $business_id)
                    ->where('woocommerce_product_id', $product_line->product_id)
                    ->with(['variations'])
                    ->first();
                if (empty($product)) {
                    $product = Product::where('business_id', $business_id)
                    ->where('sku', $product_line->product_sku)
                    ->with(['variations'])
                    ->first();
                }
                // if still missing then hit the api to get the product from woocommerce
                if (empty($product)) {
                    $woocommerce_api_settings = $this->get_api_settings($business_id);
                    if (empty($woocommerce_api_settings->woocommerce_app_url)) {
                        throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
                    }
                    $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
                    $endpoint = $base_url . '/wp-json/phantasm-erp/v1/products/parent/'.$product_line->product_id;

                    try {
                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            // 'Authorization' => 'Basic ' . base64_encode($woocommerce_api_settings->woocommerce_consumer_key . ':' . $woocommerce_api_settings->woocommerce_consumer_secret)
                        ])
                            ->timeout(300)
                            ->get($endpoint);
                        $woo_product = $response->json();
                        if (!empty($woo_product) && isset($woo_product['data'])) {
                            $this->processWooCommerceProduct($business_id, $woo_product['data'], $woocommerce_api_settings);
                        
                            // last attempt to find the product
                            $product = Product::where('business_id', $business_id)
                            ->where('woocommerce_product_id', $woo_product['data']['id'])
                            ->orWhere('sku', $woo_product['data']['sku']?? $woo_product['data']['_sku'])
                            ->with(['variations'])
                            ->first();
                        }
                    } catch (\Exception $e) {
                        Log::error('Error fetching product from WooCommerce while processing the order '.$order->id.': ' . $e->getMessage());
                    }
              
                    // HTTP request to erp connector and get product data by Parent product id and then sync it first woocommerce and then again try to find with SKU
                }

                // Log::info(' product ',['product'=>$product]);
                $unit_price = $product_line->total / $product_line->quantity;
                $line_tax = ! empty($product_line->total_tax) ? $product_line->total_tax : 0;
                $unit_line_tax = $line_tax / $product_line->quantity;
                $unit_price_inc_tax = $unit_price + $unit_line_tax;
                if (! empty($product)) {
                    // Log::info(' product in if');
                    //Set sale line variation;If single product then first variation
                    //else search for woocommerce_variation_id in all the variations
                    if ($product->type == 'single') {
                        $variation = $product->variations->first();
                        
                    } else {
                        foreach ($product->variations as $v) {
                            if ($v->woocommerce_variation_id == $product_line->variation_id) {
                                $variation = $v;
                            }
                        }
                    
                    }

                    // Log::info(' variation ',['variation'=>$variation]);

                    if (empty($variation)) {
                        return [
                            'has_error' => [
                                'error_type' => 'order_product_not_found',
                                'order_number' => $order->number,
                                'product' => $product_line->name . ' SKU:' . $product_line->sku,
                            ],
                        ];
                        exit;
                    }

                    //Check if line tax exists append to sale line data
                    $tax_id = null;
                    if (! empty($product_line->taxes)) {
                        foreach ($product_line->taxes as $tax) {
                            $pos_tax = TaxRate::where('business_id', $business_id)
                                ->where('woocommerce_tax_rate_id', $tax->id)
                                ->first();

                            if (! empty($pos_tax)) {
                                $tax_id = $pos_tax->id;
                                break;
                            }
                        }
                    }

                    $product_data = [
                        'product_id' => $product->id,
                        'unit_price' => $unit_price,
                        'unit_price_inc_tax' => $unit_price_inc_tax,
                        'variation_id' => $variation->id,
                        'quantity' => $product_line->quantity,
                        'ordered_quantity' => $product_line->quantity,
                        'enable_stock' => 1, // $product->enable_stock,
                        'item_tax' => $line_tax,
                        'tax_id' => $tax_id,
                        'line_item_id' => $product_line->id ??null,
                    ];
                    
                    // Log::info('product_data',['product_data'=>$product_data]);
                    //append transaction_sell_lines_id if update
                    if (! empty($sell_lines)) {
                        foreach ($sell_lines as $sell_line) {
                            if (
                                $sell_line->woocommerce_line_items_id ==
                                $product_line->id
                            ) {
                                $product_data['transaction_sell_lines_id'] = $sell_line->id;
                            }
                        }
                    }

                    $product_lines[] = $product_data;
                }
            }
            // Log::info('product_lines',['product_lines'=>$product_lines]);
            //Get or create customer
            $customer = Contact::where('business_id', $business_id)
                ->where('email', $order->billing->email ?? '')
                ->first();

            if (empty($customer)) {
                // $customer = new Contact();
                // $customer->business_id = $business_id;
                // $customer->woocommerce_customer_id = $order->customer_id;
                // $customer->type = 'customer';
                // $customer->name = $order->billing->first_name.' '.$order->billing->last_name;
                // $customer->email = $order->billing->email;
                // $customer->mobile = $order->billing->phone;
                // $customer->created_by = $user_id;
                // $customer->save();
            }

            $sell_status = $this->woocommerceOrderStatusToPosSellStatus($order->status, $business_id);
            $shipping_status = $this->woocommerceOrderStatusToPosShippingStatus($order->status, $business_id);
            $shipping_address = [];
            if (! empty($order->shipping->first_name)) {
                $shipping_address[] = $order->shipping->first_name . ' ' . $order->shipping->last_name;
            }
            if (! empty($order->shipping->company)) {
                $shipping_address[] = $order->shipping->company;
            }
            if (! empty($order->shipping->address_1)) {
                $shipping_address[] = $order->shipping->address_1;
            }
            if (! empty($order->shipping->address_2)) {
                $shipping_address[] = $order->shipping->address_2;
            }
            if (! empty($order->shipping->city)) {
                $shipping_address[] = $order->shipping->city;
            }
            if (! empty($order->shipping->state)) {
                $shipping_address[] = $order->shipping->state;
            }
            if (! empty($order->shipping->country)) {
                $shipping_address[] = $order->shipping->country;
            }
            if (! empty($order->shipping->postcode)) {
                $shipping_address[] = $order->shipping->postcode;
            }
            $addresses['shipping_address'] = [
                'shipping_name' => $order->shipping->first_name . ' ' . $order->shipping->last_name,
                'company' => $order->shipping->company,
                'shipping_address_line_1' => $order->shipping->address_1,
                'shipping_address_line_2' => $order->shipping->address_2,
                'shipping_city' => $order->shipping->city,
                'shipping_state' => $order->shipping->state,
                'shipping_country' => $order->shipping->country,
                'shipping_zip_code' => $order->shipping->postcode,
            ];

            $shipping_lines_array = [];
            if (! empty($order->shipping_lines)) {
                foreach ($order->shipping_lines as $shipping_lines) {
                    $shipping_lines_array[] = $shipping_lines->method_title;
                }
            }
            //  Log::info('$order->payment_method ',['method'=>$order ??'']);
            // Log::info('$order->payment_method ',['method'=>$order->payment_method ??'']);
            $new_sell_data = [
                'business_id' => $business_id,
                'location_id' => $woocommerce_api_settings->location_id ?? 1,
                'contact_id' => $customer->id,
                'discount_type' => 'percentage',
                'discount_amount' => $order->discount_total ?? 0,
                // 'shipping_charges' => $order->shipping_total,
                'final_total' => $order->total ?? 0,
                'created_by' => $user_id,
                'status' => 'ordered',
                'is_quotation' => 0,
                'sub_status' => null,
                'payment_status' => ($order->payment_method ?? '') == 'card' ? 'paid' : 'due',
                'additional_notes' => '',
                'transaction_date' => $order->created_at ?? $order->date_created ?? now(),
                'customer_group_id' => $customer->customer_group_id ?? null,
                'tax_rate_id' => null,
                'sale_note' => null,
                'commission_agent' => null,
                'invoice_no' => $order->number ?? $order->id ?? 'WC-' . time(),
                'order_addresses' => json_encode($addresses),
                'shipping_charges' => ! empty($order->shipping_total) ? $order->shipping_total : 0,
                'shipping_details' => ! empty($shipping_lines_array) ? implode(', ', $shipping_lines_array) : '',
                'shipping_status' => null,
                'shipping_address' => implode(', ', $shipping_address),
                'type' => 'sales_order',
                'recur_interval' => 1.000,
                'recur_interval_type' => 'days',
                'recur_repetitions' => 0,
            ];

            
            // Only create payment lines for non-skipped payment methods
            $skip_payment_methods = ['managemore_onaccount'];
            $should_create_payment_lines = true;
            
            if (isset($order->payment_method) && in_array($order->payment_method, $skip_payment_methods)) {
                $should_create_payment_lines = false;
                Log::info('Skipping payment lines creation for payment method: ' . $order->payment_method);
            }
            
            if ($should_create_payment_lines) {
                $payment = [
                    'amount' => $order->total ?? 0,
                    'method' => 'cash',
                    'card_transaction_number' => '',
                    'card_number' => '',
                    'card_type' => '',
                    'card_holder_name' => '',
                    'card_month' => '',
                    'card_security' => '',
                    'cheque_number' => '',
                    'bank_account_number' => '',
                    'note' => $order->payment_method_title ?? 'WooCommerce Order',
                    'paid_on' => $order->date_paid ?? $order->created_at ?? now(),
                ];

                if (! empty($sell) && count($sell->payment_lines) > 0) {
                    $payment['payment_id'] = $sell->payment_lines->first()->id;
                }

                $new_sell_data['payment'] = [$payment];
            } else {
                // Set empty payment array for skipped payment methods
                $new_sell_data['payment'] = [];
            }

            $new_sell_data['products'] = $product_lines;

            return $new_sell_data;
        } catch (\Exception $e) {
            Log::error('--------------------------------');
            Log::error('Error formatting order to sale', ['error' => $e->getMessage()]);
            Log::info('--------------------------------');
            Log::info('Order Data', ['order' => $order]);
            Log::error('--------------------------------');
            // throw $e;
        }
        return false;
    }



    /**
     * Updates existing sale
     *
     * @param  int  $business_id
     * @param  int  $user_id
     * @param  object  $order
     * @param  object  $sell
     * @param  array  $business_data
     */
    public function updateSaleFromOrder($business_id, $user_id, $order, $sell, $business_data)
    {
        $input = $this->formatOrderToSale($business_id, $user_id, $order, $sell);

        if (! empty($input['has_error'])) {
            return $input['has_error'];
        }

        $invoice_total = [
            'total_before_tax' => $order->total,
            'tax' => 0,
        ];

        $status_before = $sell->status;

        DB::beginTransaction();
        $transaction = $this->transactionUtil->updateSellTransaction($sell, $business_id, $input, $invoice_total, $user_id, false, false);

        //Update Sell lines
        $deleted_lines = $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id'], true, $status_before, [], false);

        $this->transactionUtil->createOrUpdatePaymentLines($transaction, $input['payment'], null, null, false);

        //Update payment status
        $transaction->payment_status = 'paid';
        $transaction->save();

        //Update product stock
        $this->productUtil->adjustProductStockForInvoice($status_before, $transaction, $input, false);

        try {
            $this->transactionUtil->adjustMappingPurchaseSell($status_before, $transaction, $business_data, $deleted_lines);
        } catch (PurchaseSellMismatch $e) {
            DB::rollBack();

            return [
                'error_type' => 'order_insuficient_product_qty',
                'order_number' => $order->number,
                'msg' => $e->getMessage(),
            ];
        }

        DB::commit();

        return true;
    }

    /**
     * Creates sync log in the database
     *
     * @param  int  $business_id
     * @param  int  $user_id
     * @param  string  $type
     * @param  array  $errors = null
     */
    public function createSyncLog($business_id, $user_id, $type, $operation = null, $data = [], $errors = null)
    {
        WoocommerceSyncLog::create([
            'business_id' => $business_id,
            'sync_type' => $type,
            'created_by' => $user_id,
            'operation_type' => $operation,
            'data' => ! empty($data) ? json_encode($data) : null,
            'details' => ! empty($errors) ? json_encode($errors) : null,
        ]);
    }

    /**
     * Retrives last synced date from the database
     *
     * @param  int  $business_id
     * @param  string  $type
     * @param  bool  $for_humans = true
     */
    public function getLastSync($business_id, $type, $for_humans = true)
    {
        $last_sync = WoocommerceSyncLog::where('business_id', $business_id)
            ->where('sync_type', $type)
            ->max('created_at');

        //If last reset present make last sync to null
        $last_reset = WoocommerceSyncLog::where('business_id', $business_id)
            ->where('sync_type', $type)
            ->where('operation_type', 'reset')
            ->max('created_at');
        if (! empty($last_reset) && ! empty($last_sync) && $last_reset >= $last_sync) {
            $last_sync = null;
        }

        if (! empty($last_sync) && $for_humans) {
            $last_sync = \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $last_sync)->diffForHumans();
        }

        return $last_sync;
    }

    public function woocommerceOrderStatusToPosSellStatus($status, $business_id)
    {
        $default_status_array = [
            'pending' => 'draft',
            'processing' => 'final',
            'on-hold' => 'draft',
            'completed' => 'final',
            'cancelled' => 'draft',
            'refunded' => 'draft',
            'failed' => 'draft',
            'shipped' => 'final',
        ];

        $api_settings = $this->get_api_settings($business_id);

        $status_settings = $api_settings->order_statuses ?? null;

        $sale_status = ! empty($status_settings) ? $status_settings->$status : null;
        $sale_status = empty($sale_status) && array_key_exists($status, $default_status_array) ? $default_status_array[$status] : $sale_status;
        $sale_status = empty($sale_status) ? 'final' : $sale_status;

        return $sale_status;
    }

    public function woocommerceOrderStatusToPosShippingStatus($status, $business_id)
    {
        $api_settings = $this->get_api_settings($business_id);

        $status_settings = $api_settings->shipping_statuses ?? null;

        $shipping_status = ! empty($status_settings) ? $status_settings->$status : null;

        return $shipping_status;
    }
    // ----------------------------- End of Sales Order From WooCommerce to ERP -----------------------------





    // ----------------------------- Start of Sales Order Creation at WooCommerce -----------------------------
    /**
     * Creates order in WooCommerce from ERP transaction
     *
     * @param  int  $business_id
     * @param  object  $transaction
     * @return array
     */
    public function createOrderInWooCommerce($business_id, $transaction)
    {
        // --- Pouse for staging ---
        return true;
        try {
            $order_data = $this->formatTransactionToWooCommerceOrder($business_id, $transaction);
            $woocommerce_api_settings = $this->get_api_settings($business_id);
            if (empty($woocommerce_api_settings->woocommerce_app_url)) {
                throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
            }
            $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
            $endpoint = $base_url . '/wp-json/phantasm-erp/v1/create-order';

            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    // 'Authorization' => 'Basic ' . base64_encode($woocommerce_api_settings->woocommerce_consumer_key . ':' . $woocommerce_api_settings->woocommerce_consumer_secret)
                ])
                    ->timeout(300)
                    ->post($endpoint, $order_data);
                $response = $response->json();
                Log::info('Response from WooCommerce', [
                    'response' => $response
                ]);
                if ($response['success']) {
                    $transaction->woocommerce_order_id = $response['order_id'];
                    $transaction->save();
                    return [
                        'success' => true,
                        'woocommerce_order_id' => $response['order_id']
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $response['message'] ?? 'No message',
                        'error' => $response['error'] ?? null
                    ];
                }
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'No order ID returned from WooCommerce',
                    'error' => $e->getMessage()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Updates order in WooCommerce from ERP transaction
     *
     * @param  int  $business_id
     * @param  object  $transaction
     * @return array
     */
    public function updateOrderInWooCommerce($business_id, $transaction)
    {
        // --- Pouse for staging ---
        return true;
        try {
            $order_data = $this->formatTransactionToWooCommerceOrder($business_id, $transaction);

            $woocommerce_api_settings = $this->get_api_settings($business_id);
            if (empty($woocommerce_api_settings->woocommerce_app_url)) {
                throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
            }
            $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
            $endpoint = $base_url . '/wp-json/phantasm-erp/v1/update-order';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                // 'Authorization' => 'Basic ' . base64_encode($woocommerce_api_settings->woocommerce_consumer_key . ':' . $woocommerce_api_settings->woocommerce_consumer_secret)
            ])
                ->timeout(300)
                ->post($endpoint, $order_data);
            $response = $response->json();
            Log::info('WooCommerce response', [
                'response' => $response
            ]);
            if ($response['success']) {
                return [
                    'success' => true,
                    'woocommerce_order_id' => $response['order_id']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $response['message'] ?? 'No message',
                    'error' => $response['error'] ?? null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Formats ERP transaction to WooCommerce order format
     *
     * @param  int  $business_id
     * @param  object  $transaction
     * @return array
     */
    private function formatTransactionToWooCommerceOrder($business_id, $transaction)
    {
        // Get customer data
        $customer = $transaction->contact;

        // Format line items
        $line_items = [];
        foreach ($transaction->sell_lines as $sell_line) {
            $product = $sell_line->product;
            $variation = $sell_line->variations;

            // Skip if product doesn't have WooCommerce ID
            if (empty($product->woocommerce_product_id)) {
                Log::warning('Product does not have WooCommerce ID, skipping', [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'transaction_id' => $transaction->id
                ]);
                continue;
            }

            // Skip if product is null
            if (!$product) {
                Log::warning('Product not found for sell line, skipping', [
                    'sell_line_id' => $sell_line->id,
                    'transaction_id' => $transaction->id
                ]);
                continue;
            }

            $line_item = [
                'product_id' => $product->woocommerce_product_id,
                'quantity' => (int) $sell_line->quantity,
                'total' => $sell_line->unit_price * $sell_line->quantity,
                'subtotal' => $sell_line->unit_price * $sell_line->quantity,
                'name' => $product->name
            ];

            // Add variation ID if it's a variable product
            if ($product->type == 'variable' && !empty($variation->woocommerce_variation_id)) {
                $line_item['variation_id'] = $variation->woocommerce_variation_id;
            }

            // Add tax if present
            if (!empty($sell_line->item_tax) && $sell_line->item_tax > 0) {
                $line_item['total_tax'] = $sell_line->item_tax * $sell_line->quantity;
                $line_item['subtotal_tax'] = $sell_line->item_tax * $sell_line->quantity;
            }

            $line_items[] = $line_item;
        }

        // Format billing address
        $billing_address = [
            'first_name' => $customer->first_name ?? $customer->name,
            'last_name' => $customer->last_name ?? '',
            'company' => $customer->supplier_business_name ?? '',
            'address_1' => $customer->address_line_1 ?? '',
            'address_2' => $customer->address_line_2 ?? '',
            'city' => $customer->city ?? '',
            'state' => $customer->state ?? '',
            'postcode' => $customer->zip_code ?? '',
            'country' => $customer->country ?? '',
            'email' => $customer->email ?? '',
            'phone' => $customer->mobile ?? ''
        ];

        // Format shipping address (same as billing for now)
        $shipping_address = $billing_address;

        // Determine order status

        $status = $this->erpStatusToWooCommerceStatus($transaction->status, $transaction->payment_status);

        // Calculate totals
        $total = $transaction->final_total;
        $subtotal = $transaction->total_before_tax;
        $total_tax = $transaction->tax_amount;
        $discount_total = $transaction->discount_amount ?? 0;

        // Try to create or find customer in WooCommerce
        $customer_id = $this->createOrFindWooCommerceCustomer($business_id, $customer);

        $order_data = [
            'erp_transaction_id' => $transaction->id,
            'status' => $status,
            'currency' => 'USD', // Default currency, can be made configurable
            'prices_include_tax' => false,
            'customer_id' => $customer_id ?? 0, // Use created customer or guest
            'billing' => $billing_address,
            'shipping' => $shipping_address,
            'line_items' => $line_items,
            'total' => $total,
            'subtotal' => $subtotal,
            'total_tax' => $total_tax,
            'discount_total' => $discount_total,
            'payment_method' => 'managemore_onaccount',
            'payment_method_title' => '(*** PLEASE DONT USE THIS PAYMENT METHOD UNTIL WE ASK YOU TO DO IT. YOUR ORDER WILL AUTOMATICALLY GET CANCELLED.)',
            'set_paid' => true, //$transaction->payment_status == 'paid',
            'customer_note' => $transaction->additional_notes ?? '',
            'meta_data' => [
                [
                    'key' => '_erp_transaction_id',
                    'value' => $transaction->id
                ],
                [
                    'key' => '_erp_invoice_no',
                    'value' => $transaction->invoice_no
                ]
            ]
        ];

        // Add payment lines if present
        if ($transaction->payment_lines && count($transaction->payment_lines) > 0) {
            $payment_lines = [];
            foreach ($transaction->payment_lines as $payment_line) {
                $payment_lines[] = [
                    'method_id' => 'card', //$this->erpPaymentMethodToWooCommerce($payment_line->method),
                    'method_title' => 'Credit-Debit Card' ?? $payment_line->method,
                    'amount' => $payment_line->amount
                ];
                $order_data['payment_method'] = 'card';
                $order_data['payment_method_title'] = 'Credit-Debit Card';
            }
            $order_data['payment_lines'] = $payment_lines;
        }

        // Add shipping information if available
        if (!empty($transaction->shipping_address)) {
            $order_data['shipping_lines'] = [
                [
                    'method_id' => 'flat_rate',
                    'method_title' => 'Flat Rate',
                    'total' => $transaction->shipping_charges ?? 0
                ]
            ];
        }

        return $order_data;
    }

    /**
     * Converts ERP status to WooCommerce status , (this function not take care of payment status , if need please write your)
     * @example ordered -> processing
     * @example cancelled -> cancelled
     * @example completed -> completed
     * @example pending -> processing
     * @example processing -> processing
     * @example completed -> completed
     * @example cancelled -> cancelled
     * @param  string  $erp_status
     * @param  string  $payment_status
     * @return string
     */
    private function erpStatusToWooCommerceStatus($erp_status, $payment_status)
    {
        switch ($erp_status) {
            case 'ordered':
                return 'processing';
            case 'cancelled':
                return 'cancelled';
            case 'completed':
                return 'completed';
            default:
                return 'processing';
        }
    }

    /**
     * Converts ERP payment method to WooCommerce payment method
     *
     * @param  string  $erp_method
     * @return string
     */
    private function erpPaymentMethodToWooCommerce($erp_method)
    {
        $method_map = [
            'cash' => 'cod',
            'card' => 'stripe',
            'bank_transfer' => 'bacs',
            'cheque' => 'cheque',
            'other' => 'bacs'
        ];

        return $method_map[$erp_method] ?? 'bacs';
    }

    /**
     * Creates or finds customer in WooCommerce
     *
     * @param  int  $business_id
     * @param  object  $contact
     * @return int|null
     */
    private function createOrFindWooCommerceCustomer($business_id, $contact)
    {
        try {
            // $woocommerce = $this->woo_client($business_id);
            $woocommerce_api_settings = $this->get_api_settings($business_id);
            if (empty($woocommerce_api_settings->woocommerce_app_url)) {
                throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
            }
            $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
            $endpoint = $base_url . '/wp-json/phantasm-erp/v1/get-customer-id';

            // need to fix the auth issue in future
            if (!empty($contact->email)) {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    // 'Authorization' => 'Basic ' . base64_encode($woocommerce_api_settings->woocommerce_consumer_key . ':' . $woocommerce_api_settings->woocommerce_consumer_secret)
                ])
                    ->timeout(300)
                    ->post($endpoint, ['user_email' => $contact->email]);
                Log::info('WooCommerce response', [
                    'response' => $response
                ]);
                $response = $response->json();
                if ($response['success']) {
                    return $response['woocommerce_customer_id'];
                }
            }


            // Create new customer
            $customer_data = [
                'email' => $contact->email ?? 'guest@example.com',
                'first_name' => $contact->first_name ?? $contact->name,
                'last_name' => $contact->last_name ?? '',
                'billing' => [
                    'first_name' => $contact->first_name ?? $contact->name,
                    'last_name' => $contact->last_name ?? '',
                    'company' => $contact->supplier_business_name ?? '',
                    'address_1' => $contact->address_line_1 ?? '',
                    'address_2' => $contact->address_line_2 ?? '',
                    'city' => $contact->city ?? '',
                    'state' => $contact->state ?? '',
                    'postcode' => $contact->zip_code ?? '',
                    'country' => $contact->country ?? '',
                    'email' => $contact->email ?? '',
                    'phone' => $contact->mobile ?? ''
                ],
                'shipping' => [
                    'first_name' => $contact->first_name ?? $contact->name,
                    'last_name' => $contact->last_name ?? '',
                    'company' => $contact->supplier_business_name ?? '',
                    'address_1' => $contact->address_line_1 ?? '',
                    'address_2' => $contact->address_line_2 ?? '',
                    'city' => $contact->city ?? '',
                    'state' => $contact->state ?? '',
                    'postcode' => $contact->zip_code ?? '',
                    'country' => $contact->country ?? ''
                ]
            ];

            $woocommerce = $this->woo_client($business_id);
            $response = $woocommerce->post('customers', $customer_data);

            if (!empty($response->id)) {
                Log::info('Created new WooCommerce customer', [
                    'contact_id' => $contact->id,
                    'woocommerce_customer_id' => $response->id
                ]);
                return $response->id;
            }
        } catch (\Exception $e) {
            Log::error('Error creating/finding WooCommerce customer', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }
    // ----------------------------- End of Sales Order Creation at WooCommerce -----------------------------


    /**
     * Splits response to list of 100 and merges all
     *
     * @param  int  $business_id
     * @param  string  $endpoint
     * @param  array  $params = []
     * @return array
     */
    public function getAllResponse($business_id, $endpoint, $params = [])
    {

        //woocommerce api client object
        $woocommerce = $this->woo_client($business_id);

        $page = 1;
        $list = [];
        $all_list = [];
        $params['per_page'] = 100;

        do {
            $params['page'] = $page;
            try {
                $list = $woocommerce->get($endpoint, $params);
            } catch (\Exception $e) {
                return [];
            }
            $all_list = array_merge($all_list, $list);
            $page++;
        } while (count($list) > 0);

        return $all_list;
    }

    /**
     * Retrives all tax rates from woocommerce api
     *
     * @param  int  $business_id
     * @param  object  $tax_rates
     */
    public function getTaxRates($business_id)
    {
        $tax_rates = $this->getAllResponse($business_id, 'taxes');

        return $tax_rates;
    }

    public function getLastStockUpdated($location_id, $product_id)
    {
        $last_updated = VariationLocationDetails::where('location_id', $location_id)
            ->where('product_id', $product_id)
            ->max('updated_at');

        return $last_updated;
    }

    private function formatDecimalPoint($number, $type = 'currency')
    {
        $precision = 4;
        $currency_precision = session('business.currency_precision', 2);
        $quantity_precision = session('business.quantity_precision', 2);

        if ($type == 'currency' && ! empty($currency_precision)) {
            $precision = $currency_precision;
        }
        if ($type == 'quantity' && ! empty($quantity_precision)) {
            $precision = $quantity_precision;
        }

        return number_format((float) $number, $precision, '.', '');
    }

    public function isValidImage($path)
    {
        $valid_extenstions = ['jpg', 'jpeg', 'png', 'gif'];

        return ! empty($path) && file_exists($path) && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $valid_extenstions);
    }



    // ----------------------------- Start of Product Quantities Synchronization -----------------------------
    /**
     * Synchronizes only product quantities with Woocommerce
     *
     * @param  int  $business_id
     * @param  int  $user_id
     * @return void
     */
    public function syncProductQuantities($business_id, $user_id)
    {
        $woocommerce_api_settings = $this->get_api_settings($business_id);
        $business_location_id = $woocommerce_api_settings->location_id;

        $query = Product::where('business_id', $business_id)
            ->whereIn('type', ['single', 'variable'])
            ->where('woocommerce_disable_sync', 0)
            ->whereNotNull('woocommerce_product_id')
            ->with(['variations' => function ($q) {
                $q->whereNotNull('woocommerce_variation_id');
            }, 'variations.variation_location_details']);

        if (!empty($business_location_id)) {
            $query->ForLocation($business_location_id);
        }

        $products = $query->get();

        $updated_data = [];
        $updates = [];
        $chunk_size = 400;

        foreach ($products as $product) {
            if ($product->type == 'single') {
                // For single products
                $variation = $product->variations->first();
                if ($variation) {
                    $qty_available = 0;
                    if ($product->enable_stock == 1) {
                        $location_detail = $variation->variation_location_details
                            ->where('location_id', $business_location_id)
                            ->first();
                        if ($location_detail) {
                            $qty_available = $location_detail->in_stock_qty;
                        }
                    }
                    $updates[] = [
                        'sku' => $product->sku,
                        'quantity' => $qty_available,
                        'manage_stock' => $product->enable_stock == 1
                    ];
                    $updated_data[] = $product->sku;
                }
            } else {
                // For variable products
                foreach ($product->variations as $variation) {
                    if (!empty($variation->woocommerce_variation_id)) {
                        $qty_available = 0;
                        if ($product->enable_stock == 1) {
                            $location_detail = $variation->variation_location_details
                                ->where('location_id', $business_location_id)
                                ->first();
                            if ($location_detail) {
                                $qty_available = $location_detail->in_stock_qty;
                            }
                        }

                        $updates[] = [
                            'sku' => $variation->sub_sku,
                            'quantity' => $qty_available,
                            'manage_stock' => $product->enable_stock == 1
                        ];
                        $updated_data[] = $variation->sub_sku;
                    }
                }
            }
        }

        // Process updates in chunks
        $chunks = array_chunk($updates, $chunk_size);

        if (!empty($woocommerce_api_settings->woocommerce_app_url)) {
            $webhook_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/') . '/wp-json/phantasm-erp/v1/sync';
        }
        foreach ($chunks as $chunk) {
            try {
                $payload = json_encode(['updates' => $chunk]);

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    // 'Authorization' => 'Basic ' . base64_encode($woocommerce_api_settings->woocommerce_consumer_key . ':' . $woocommerce_api_settings->woocommerce_consumer_secret),
                ])
                    ->post($webhook_url, ['updates' => $chunk]);

                if (!$response->successful()) {
                    Log::error('Failed to sync stock quantities: ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('Error syncing stock quantities: ' . $e->getMessage());
            }
        }

        // Create sync log
        if (!empty($updated_data)) {
            $this->createSyncLog($business_id, $user_id, 'product_quantities', 'updated', $updated_data);
        } else {
            $this->createSyncLog($business_id, $user_id, 'product_quantities');
        }
    }
    // ----------------------------- End of Product Quantities Synchronization -----------------------------

    // ----------------------------- Start of Customers Synchronization -----------------------------
    /**
     * Sync customers from ERP to WooCommerce
     * it will create or update the customer in WooCommerce
     * @param  int  $business_id
     * @param  int  $user_id
     * @return void
     */
    public function syncCustomers($business_id, $user_id)
    {
        $woocommerce = $this->woo_client($business_id);

        // Get all customers from POS
        $customers = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->where('is_default', 0)
            ->get();

        $created_data = [];
        $updated_data = [];

        foreach ($customers as $customer) {
            $customer_data = [
                'email' => $customer->email,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'username' => $customer->customer_u_name ?? $customer->contact_id,
                'billing' => [
                    'first_name' => $customer->first_name ?? '',
                    'last_name' => $customer->last_name ?? '',
                    'email' => $customer->email ?? '',
                    'phone' => $customer->mobile ?? '',
                    'address_1' => $customer->address_line_1 ?? '',
                    'address_2' => $customer->address_line_2 ?? '',
                    'city' => $customer->city ?? '',
                    'state' => $customer->state ?? '',
                    'postcode' => $customer->zip_code ?? '',
                    'country' => $customer->country ?? ''
                ],
                'shipping' => [
                    'first_name' => $customer->shipping_first_name ?? '',
                    'last_name' => $customer->shipping_last_name ?? '',
                    'company' => $customer->shipping_company ?? '',
                    'address_1' => $customer->shipping_address1 ?? '',
                    'address_2' => $customer->shipping_address2 ?? '',
                    'city' => $customer->shipping_city ?? '',
                    'state' => $customer->shipping_state ?? '',
                    'postcode' => $customer->shipping_zip ?? '',
                    'country' => $customer->shipping_country ?? ''
                ]
            ];

            try {
                if (empty($customer->woocommerce_customer_id)) {
                    // Create new customer in WooCommerce
                    $response = $woocommerce->post('customers', $customer_data);
                    if (!empty($response->id)) {
                        $customer->woocommerce_customer_id = $response->id;
                        $customer->save();
                        $created_data[] = $customer->email;
                    }
                } else {
                    // Update existing customer in WooCommerce
                    $response = $woocommerce->put('customers/' . $customer->woocommerce_customer_id, $customer_data);
                    $updated_data[] = $customer->email;
                }
            } catch (\Exception $e) {
                Log::error('Error syncing customer: ' . $e->getMessage(), [
                    'customer_id' => $customer->id,
                    'email' => $customer->email
                ]);
            }
        }

        // Create sync log
        if (!empty($created_data)) {
            $this->createSyncLog($business_id, $user_id, 'customers', 'created', $created_data);
        }
        if (!empty($updated_data)) {
            $this->createSyncLog($business_id, $user_id, 'customers', 'updated', $updated_data);
        }
        if (empty($created_data) && empty($updated_data)) {
            $this->createSyncLog($business_id, $user_id, 'customers');
        }
    }

    /** 
     * Sync customers from WooCommerce to ERP
     * it will create or update the customer in ERP
     * @param  int  $business_id
     * @param  int  $user_id
     * @param  string  $sync_type
     * @param  int  $limit
     * @param  int  $offset
     * @param  bool  $is_chunked_sync
     * @return array
     */
    public function syncCustomersFromWooToErp($business_id, $user_id, $sync_type = 'all', $limit = 100, $offset = 0, $is_chunked_sync = false)
    {
        $woocommerce_api_settings = $this->get_api_settings($business_id);

        $created_customers = [];
        $updated_customers = [];
        $skipped_customers = [];
        $total_customers = 0;
        $current_offset = $offset;
        $has_more = true;
        $chunk_count = 0;

        // Get last sync time for incremental sync
        $last_synced = $this->getLastSync($business_id, 'woo_to_erp_customers', false);

        Log::info('Starting WooCommerce to ERP customer sync using WordPress plugin', [
            'business_id' => $business_id,
            'offset' => $offset,
            'limit' => $limit,
            'sync_type' => $sync_type
        ]);

        try {
            // If this is a chunked sync, only process one chunk
            if ($is_chunked_sync) {
                $chunk_count = 1;

                // Prepare parameters for WordPress plugin API
                $params = [
                    'page' => ($current_offset / $limit) + 1,
                    'per_page' => $limit,
                    'include_meta' => 'true'
                ];

                // Add date filter for incremental sync
                if ($sync_type == 'updated' && !empty($last_synced)) {
                    $params['modified_after'] = $last_synced;
                }

                // Use WordPress plugin endpoint for optimized data retrieval
                $woo_customers = $this->getCustomersFromWordPressPlugin($business_id, $params);

                if (empty($woo_customers) || !isset($woo_customers['data'])) {
                    Log::info('No more customers to sync', [
                        'business_id' => $business_id,
                        'chunk_count' => $chunk_count
                    ]);
                    return [
                        'total_customers' => 0,
                        'created_customers' => [],
                        'updated_customers' => [],
                        'skipped_customers' => [],
                        'has_more' => false,
                        'next_offset' => $current_offset,
                        'total_chunks' => 0
                    ];
                }

                $customers_data = $woo_customers['data'];
                $chunk_total = count($customers_data);
                $total_customers = $chunk_total;
                $has_more = isset($woo_customers['pagination']['has_more']) ? $woo_customers['pagination']['has_more'] : false;

                Log::info('Processing customer chunk', [
                    'business_id' => $business_id,
                    'chunk_count' => $chunk_count,
                    'chunk_total' => $chunk_total,
                    'current_offset' => $current_offset,
                    'pagination' => $woo_customers['pagination'] ?? []
                ]);

                foreach ($customers_data as $customer) {
                    try {
                        $result = $this->processWooCommerceCustomer($business_id, $customer, $woocommerce_api_settings);

                        if ($result['action'] == 'created') {
                            $created_customers[] = $result['customer_name'];
                            Log::info('Customer created successfully', [
                                'customer_name' => $result['customer_name'],
                                'customer_id' => $result['customer_id'],
                                'chunk' => $chunk_count
                            ]);
                        } elseif ($result['action'] == 'updated') {
                            $updated_customers[] = $result['customer_name'];
                            Log::info('Customer updated successfully', [
                                'customer_name' => $result['customer_name'],
                                'customer_id' => $result['customer_id'],
                                'chunk' => $chunk_count
                            ]);
                        } else {
                            $skipped_customers[] = $result['customer_name'];
                            Log::info('Customer skipped', [
                                'customer_name' => $result['customer_name'],
                                'chunk' => $chunk_count
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error processing WooCommerce customer', [
                            'woo_customer_id' => $customer['ID'] ?? 'N/A',
                            'email' => $customer['user_email'] ?? 'N/A',
                            'username' => $customer['user_login'] ?? 'N/A',
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'chunk' => $chunk_count
                        ]);
                        $skipped_customers[] = $customer['user_login'] ?? 'Unknown Customer';
                    }
                }

                // Move to next chunk
                $current_offset += $limit;

                return [
                    'total_customers' => $total_customers,
                    'created_customers' => $created_customers,
                    'updated_customers' => $updated_customers,
                    'skipped_customers' => $skipped_customers,
                    'has_more' => $has_more,
                    'next_offset' => $current_offset,
                    'total_chunks' => $chunk_count
                ];
            } else {
                // Process all chunks in a loop (for complete sync)
                while ($has_more) {
                    $chunk_count++;

                    // Prepare parameters for WordPress plugin API
                    $params = [
                        'page' => ($current_offset / $limit) + 1,
                        'per_page' => $limit,
                        'include_meta' => 'true'
                    ];

                    // Add date filter for incremental sync
                    if ($sync_type == 'updated' && !empty($last_synced)) {
                        $params['modified_after'] = $last_synced;
                    }

                    // Use WordPress plugin endpoint for optimized data retrieval
                    $woo_customers = $this->getCustomersFromWordPressPlugin($business_id, $params);

                    if (empty($woo_customers) || !isset($woo_customers['data'])) {
                        Log::info('No more customers to sync', [
                            'business_id' => $business_id,
                            'chunk_count' => $chunk_count
                        ]);
                        break;
                    }

                    $customers_data = $woo_customers['data'];
                    $chunk_total = count($customers_data);
                    $total_customers += $chunk_total;
                    $has_more = isset($woo_customers['pagination']['has_more']) ? $woo_customers['pagination']['has_more'] : false;

                    Log::info('Processing customer chunk', [
                        'business_id' => $business_id,
                        'chunk_count' => $chunk_count,
                        'chunk_total' => $chunk_total,
                        'current_offset' => $current_offset,
                        'pagination' => $woo_customers['pagination'] ?? []
                    ]);

                    foreach ($customers_data as $customer) {
                        try {
                            $result = $this->processWooCommerceCustomer($business_id, $customer, $woocommerce_api_settings);

                            if ($result['action'] == 'created') {
                                $created_customers[] = $result['customer_name'];
                                Log::info('Customer created successfully', [
                                    'customer_name' => $result['customer_name'],
                                    'customer_id' => $result['customer_id'],
                                    'chunk' => $chunk_count
                                ]);
                            } elseif ($result['action'] == 'updated') {
                                $updated_customers[] = $result['customer_name'];
                                Log::info('Customer updated successfully', [
                                    'customer_name' => $result['customer_name'],
                                    'customer_id' => $result['customer_id'],
                                    'chunk' => $chunk_count
                                ]);
                            } else {
                                $skipped_customers[] = $result['customer_name'];
                                Log::info('Customer skipped', [
                                    'customer_name' => $result['customer_name'],
                                    'chunk' => $chunk_count
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('Error processing WooCommerce customer', [
                                'woo_customer_id' => $customer['ID'] ?? 'N/A',
                                'email' => $customer['user_email'] ?? 'N/A',
                                'username' => $customer['user_login'] ?? 'N/A',
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                                'chunk' => $chunk_count
                            ]);
                            $skipped_customers[] = $customer['user_login'] ?? 'Unknown Customer';
                        }
                    }

                    // Move to next chunk
                    $current_offset += $limit;

                    // Add a small delay to prevent overwhelming the server
                    if ($has_more) {
                        usleep(100000); // 0.1 second delay
                    }
                }
            }

            // Create sync log
            $sync_data = array_merge($created_customers, $updated_customers);
            if (!empty($sync_data)) {
                $this->createSyncLog($business_id, $user_id, 'woo_to_erp_customers', 'synced', $sync_data);
            }

            Log::info('WooCommerce to ERP customer sync completed', [
                'business_id' => $business_id,
                'total_chunks' => $chunk_count,
                'total_customers' => $total_customers,
                'created' => count($created_customers),
                'updated' => count($updated_customers),
                'skipped' => count($skipped_customers)
            ]);
        } catch (\Exception $e) {
            Log::error('Error in WooCommerce to ERP customer sync', [
                'business_id' => $business_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        return [
            'total_customers' => $total_customers,
            'created_customers' => $created_customers,
            'updated_customers' => $updated_customers,
            'skipped_customers' => $skipped_customers,
            'has_more' => false, // All chunks processed
            'next_offset' => $current_offset,
            'total_chunks' => $chunk_count
        ];
    }

    /**
     * Get customers from WordPress plugin endpoint
     *
     * @param  int  $business_id
     * @param  array  $params
     * @return array
     */
    private function getCustomersFromWordPressPlugin($business_id, $params)
    {
        $woocommerce_api_settings = $this->get_api_settings($business_id);

        if (empty($woocommerce_api_settings->woocommerce_app_url)) {
            throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
        }

        $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
        $endpoint = $base_url . '/wp-json/phantasm-erp/v1/customers';

        // Build query string
        $query_string = http_build_query($params);
        $url = $endpoint . '?' . $query_string;

        try {
            $response = Http::withHeaders([
                // 'Authorization' => 'Basic ' . base64_encode($woocommerce_api_settings->woocommerce_consumer_key . ':' . $woocommerce_api_settings->woocommerce_consumer_secret),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
                ->timeout(300) // 5 minutes timeout for large datasets
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Failed to get customers from WordPress plugin', [
                    'url' => $url,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                throw new WooCommerceError('Failed to get customers from WordPress plugin: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error getting customers from WordPress plugin', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Process WooCommerce customer data and create/update in ERP
     *
     * @param  int  $business_id
     * @param  array  $customer
     * @param  object  $woocommerce_api_settings
     * @return array
     */
    private function processWooCommerceCustomer($business_id, $customer, $woocommerce_api_settings)
    {
        // Format customer data similar to existing job
        $syncCustomer = [];
        $syncCustomer['username'] = $customer['user_login'] ?? '';
        $syncCustomer['customer_email'] = $customer['user_email'] ?? '';
        $syncCustomer['password'] = $customer['user_pass'] ?? '';
        $syncCustomer['customer_id'] = $customer['ID'] ?? '';
        $syncCustomer['user_registered'] = $customer['user_registered'] ?? '';
        $ref_count = (new Util())->setAndGetReferenceCount('contacts', 1);
        $syncCustomer['contact_id'] = (new Util())->generateReferenceNumber('contacts', $ref_count, 1);
        $customerData = $customer['meta'] ?? [];

        foreach ($customerData as $meta) {
            $metaKey = $meta['meta_key'] ?? '';
            $metaValue = $meta['meta_value'] ?? '';

            switch ($metaKey) {
                case 'first_name':
                    $syncCustomer['firstName'] = $metaValue;
                    break;
                case 'last_name':
                    $syncCustomer['lastName'] = $metaValue;
                    break;
                case 'billing_address_1':
                    $syncCustomer['billingAddress'] = $metaValue;
                    break;
                case 'billing_address_2':
                    $syncCustomer['billingAddress2'] = $metaValue;
                    break;
                case 'billing_city':
                    $syncCustomer['billingCity'] = $metaValue;
                    break;
                case 'billing_postcode':
                    $syncCustomer['billingPostcode'] = $metaValue;
                    break;
                case 'billing_phone':
                    $syncCustomer['billingPhone'] = $metaValue;
                    break;
                case 'billing_email':
                    $syncCustomer['billingEmail'] = $metaValue;
                    break;
                case 'billing_country':
                    $syncCustomer['billingCountry'] = $metaValue;
                    break;
                case 'billing_state':
                    $syncCustomer['billingState'] = $metaValue;
                    break;
                case 'billing_first_name':
                    $syncCustomer['billingFirstName'] = $metaValue;
                    break;
                case 'billing_last_name':
                    $syncCustomer['billingLastName'] = $metaValue;
                    break;
                case 'billing_company':
                    $syncCustomer['billingCompany'] = $metaValue;
                    break;
                case 'shipping_address_1':
                    $syncCustomer['shippingAddress1'] = $metaValue;
                    break;
                case 'shipping_address_2':
                    $syncCustomer['shippingAddress2'] = $metaValue;
                    break;
                case 'shipping_city':
                    $syncCustomer['shippingCity'] = $metaValue;
                    break;
                case 'shipping_postcode':
                    $syncCustomer['shippingPostcode'] = $metaValue;
                    break;
                case 'shipping_country':
                    $syncCustomer['shippingCountry'] = $metaValue;
                    break;
                case 'shipping_state':
                    $syncCustomer['shippingState'] = $metaValue;
                    break;
                case 'shipping_first_name':
                    $syncCustomer['shippingFirstName'] = $metaValue;
                    break;
                case 'shipping_last_name':
                    $syncCustomer['shippingLastName'] = $metaValue;
                    break;
                case 'shipping_company':
                    $syncCustomer['shippingCompany'] = $metaValue;
                    break;
                case 'orders':
                    $syncCustomer['orders'] = $metaValue;
                    break;
                case 'wp_capabilities':
                    $user_capabilities = unserialize($metaValue) ?? [];
                    if (isset($user_capabilities['wholesale_customer'])) {
                        $syncCustomer['priceGroupID'] = 1;
                    } elseif (isset($user_capabilities['mm_price_2'])) {
                        $syncCustomer['priceGroupID'] = 2;
                    } elseif (isset($user_capabilities['mm_price_3'])) {
                        $syncCustomer['priceGroupID'] = 3;
                    } elseif (isset($user_capabilities['mm_price_4'])) {
                        $syncCustomer['priceGroupID'] = 4;
                    } else {
                        $syncCustomer['priceGroupID'] = null;
                    }
                    break;
                case 'ur_user_status':
                    if ($metaValue == '0') {
                        $syncCustomer['isApproved'] = null;
                    } elseif ($metaValue == '2') {
                        $syncCustomer['isApproved'] = false;
                    } else {
                        $syncCustomer['isApproved'] = true;
                    }
                    break;
            }
        }

        // Skip unapproved customers
        if (isset($syncCustomer['isApproved']) && $syncCustomer['isApproved'] === false) {
            return [
                'action' => 'skipped',
                'customer_name' => $syncCustomer['username'] ?? 'Unknown Customer',
                'customer_id' => $syncCustomer['customer_id'] ?? null
            ];
        }

        // Prepare ERP data
        $erpData = [
            "business_id" => $business_id,
            "type" => "customer",
            "contact_type" => "business",
            "supplier_business_name" => $syncCustomer['billingCompany'] ?? null,
            "name" => ($syncCustomer['firstName'] ?? '') . ' ' . ($syncCustomer['lastName'] ?? ''),
            "prefix" => "Mr",
            "first_name" => $syncCustomer['firstName'] ?? '',
            "middle_name" => null,
            "last_name" => $syncCustomer['lastName'] ?? '',
            "email" => $syncCustomer['customer_email'] ?? '',
            "contact_id" => $syncCustomer['contact_id'] ?? '',
            "contact_status" => isset($syncCustomer['isApproved']) && $syncCustomer['isApproved'] ? "active" : "inactive",
            "tax_number" => null,
            "city" => $syncCustomer['billingCity'] ?? '',
            "state" => $syncCustomer['billingState'] ?? '',
            "country" => $syncCustomer['billingCountry'] ?? '',
            "address_line_1" => $syncCustomer['billingAddress'] ?? '',
            "address_line_2" => $syncCustomer['billingAddress2'] ?? '',
            "zip_code" => $syncCustomer['billingPostcode'] ?? '',
            "dob" => null,
            "mobile" => $syncCustomer['billingPhone'] ?? '',
            "landline" => null,
            "alternate_number" => null,
            "pay_term_number" => null,
            "pay_term_type" => null,
            "credit_limit" => null,
            "created_by" => "1",
            "total_rp" => "0",
            "total_rp_used" => "0",
            "total_rp_expired" => "0",
            "is_default" => "0",
            "shipping_address" => ($syncCustomer['shippingAddress1'] ?? '') . ' ' . ($syncCustomer['shippingAddress2'] ?? '') . ' ' . ($syncCustomer['shippingCity'] ?? '') . ' ' . ($syncCustomer['shippingState'] ?? '') . ' ' . ($syncCustomer['shippingPostcode'] ?? '') . ' ' . ($syncCustomer['shippingCountry'] ?? ''),
            "shipping_custom_field_details" => null,
            "is_export" => "0",
            "position" => null,
            "customer_group_id" => $syncCustomer['priceGroupID'] ?? null,
            "custom_field1" => null,
            "deleted_at" => null,
            "created_at" => $syncCustomer['user_registered'] ?? now(),
            "updated_at" => $syncCustomer['user_registered'] ?? now(),
            "password" => $syncCustomer['password'] ?? '',
            "isApproved" => isset($syncCustomer['isApproved']) && $syncCustomer['isApproved'] ? $syncCustomer['isApproved'] : null,
            "remember_token" => null,
            "role" => null,
            "fcmToken" => null,
            "usermeta" => null,
            "customer_u_name" => $syncCustomer['username'] ?? '',
            "shipping_first_name" => $syncCustomer['shippingFirstName'] ?? $syncCustomer['firstName'] ?? '',
            "shipping_last_name" => $syncCustomer['shippingLastName'] ?? $syncCustomer['lastName'] ?? '',
            "shipping_company" => $syncCustomer['shippingCompany'] ?? $syncCustomer['billingCompany'] ?? '',
            "shipping_address1" => $syncCustomer['shippingAddress1'] ?? $syncCustomer['billingAddress'] ?? '',
            "shipping_address2" => $syncCustomer['shippingAddress2'] ?? $syncCustomer['billingAddress2'] ?? '',
            "shipping_city" => $syncCustomer['shippingCity'] ?? $syncCustomer['billingCity'] ?? '',
            "shipping_state" => $syncCustomer['shippingState'] ?? $syncCustomer['billingState'] ?? '',
            "shipping_zip" => $syncCustomer['shippingPostcode'] ?? $syncCustomer['billingPostcode'] ?? '',
            "shipping_country" => $syncCustomer['shippingCountry'] ?? $syncCustomer['billingCountry'] ?? ''
        ];

        // Check if customer exists
        $existing_customer = Contact::where('email', $syncCustomer['customer_email'])->first();

        if ($existing_customer) {
            $existing_customer->update($erpData);
            return [
                'action' => 'updated',
                'customer_name' => $syncCustomer['username'] ?? 'Unknown Customer',
                'customer_id' => $existing_customer->id
            ];
        } else {
            $new_customer = Contact::create($erpData);
            return [
                'action' => 'created',
                'customer_name' => $syncCustomer['username'] ?? 'Unknown Customer',
                'customer_id' => $new_customer->id
            ];
        }
    }

    // ----------------------------- End of Customers Synchronization -----------------------------


    // ----------------------------- Start of Sync Logic  From WooCommerce to ERP -----------------------------
    /**
     * Synchronizes products from WooCommerce to ERP
     * Handles 20,000+ products with multiple variations efficiently
     * Uses custom WordPress plugin endpoints for optimized data retrieval
     *
     * @param  int  $business_id
     * @param  int  $user_id
     * @param  string  $sync_type
     * @param  int  $limit
     * @param  int  $offset
     * @return array
     */
    public function syncProductsFromWooToErp($business_id, $user_id, $sync_type = 'all', $limit = 100, $offset = 0)
    {
        $woocommerce_api_settings = $this->get_api_settings($business_id);

        $created_products = [];
        $updated_products = [];
        $skipped_products = [];
        $total_products = 0;

        // Get last sync time for incremental sync
        $last_synced = $this->getLastSync($business_id, 'woo_to_erp_products', false);

        // Prepare parameters for WordPress plugin API
        $params = [
            'page' => ($offset / $limit) + 1,
            'per_page' => $limit,
            'include_variations' => 'true',
            'include_meta' => 'true'
        ];

        // Add date filter for incremental sync
        if ($sync_type == 'updated' && !empty($last_synced)) {
            $params['modified_after'] = $last_synced;
        }

        try {
            // Use WordPress plugin endpoint for optimized data retrieval
            $woo_products = $this->getProductsFromWordPressPlugin($business_id, $params);

            if (empty($woo_products) || !isset($woo_products['data'])) {
                return [
                    'total_products' => 0,
                    'created_products' => [],
                    'updated_products' => [],
                    'skipped_products' => [],
                    'has_more' => false,
                    'next_offset' => $offset
                ];
            }

            $products_data = $woo_products['data'];
            $total_products = count($products_data);
            $has_more = isset($woo_products['pagination']['has_more']) ? $woo_products['pagination']['has_more'] : false;

            // Get sync summary for debugging
            $sync_summary = $this->getSyncSummary($business_id, $products_data);

            Log::info('Starting WooCommerce to ERP sync using WordPress plugin', [
                'business_id' => $business_id,
                'total_products' => $total_products,
                'offset' => $offset,
                'limit' => $limit,
                'sync_type' => $sync_type,
                'pagination' => $woo_products['pagination'] ?? [],
                'sync_summary' => $sync_summary
            ]);

            foreach ($products_data as $woo_product) {
                try {
                    $result = $this->processWooCommerceProduct($business_id, $woo_product, $woocommerce_api_settings);

                    if ($result['action'] == 'created') {
                        $created_products[] = $result['product_name'];
                        Log::info('Product created successfully', [
                            'product_name' => $result['product_name'],
                            'product_id' => $result['product_id']
                        ]);
                    } elseif ($result['action'] == 'updated') {
                        $updated_products[] = $result['product_name'];
                        Log::info('Product updated successfully', [
                            'product_name' => $result['product_name'],
                            'product_id' => $result['product_id']
                        ]);
                    } else {
                        $skipped_products[] = $result['product_name'];
                        Log::info('Product skipped', [
                            'product_name' => $result['product_name']
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error processing WooCommerce product', [
                        'woo_product_id' => $woo_product['id'] ?? 'N/A',
                        'sku' => $woo_product['sku'] ?? 'N/A',
                        'name' => $woo_product['name'] ?? 'N/A',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $skipped_products[] = $woo_product['name'] ?? 'Unknown Product';
                }
            }

            // Create sync log
            $sync_data = array_merge($created_products, $updated_products);
            if (!empty($sync_data)) {
                $this->createSyncLog($business_id, $user_id, 'woo_to_erp_products', 'synced', $sync_data);
            }

            Log::info('WooCommerce to ERP sync completed', [
                'business_id' => $business_id,
                'created' => count($created_products),
                'updated' => count($updated_products),
                'skipped' => count($skipped_products)
            ]);
        } catch (\Exception $e) {
            Log::error('Error in WooCommerce to ERP sync', [
                'business_id' => $business_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        return [
            'total_products' => $total_products,
            'created_products' => $created_products,
            'updated_products' => $updated_products,
            'skipped_products' => $skipped_products,
            'has_more' => $has_more,
            'next_offset' => $offset + $limit
        ];
    }

    /**
     * Get products from WordPress plugin endpoint
     *
     * @param  int  $business_id
     * @param  array  $params
     * @return array
     */
    private function getProductsFromWordPressPlugin($business_id, $params)
    {
        $woocommerce_api_settings = $this->get_api_settings($business_id);

        if (empty($woocommerce_api_settings->woocommerce_app_url)) {
            throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
        }

        $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
        $endpoint = $base_url . '/wp-json/phantasm-erp/v1/products';

        // Build query string
        $query_string = http_build_query($params);
        $url = $endpoint . '?' . $query_string;

        try {
            $response = Http::withHeaders([
                // 'Authorization' => 'Basic ' . base64_encode($woocommerce_api_settings->woocommerce_consumer_key . ':' . $woocommerce_api_settings->woocommerce_consumer_secret),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
                ->timeout(300) // 5 minutes timeout for large datasets
                ->get($url);

            if (!$response->successful()) {
                Log::error('WordPress plugin API request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new WooCommerceError('Failed to fetch products from WordPress plugin: ' . $response->status());
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                throw new WooCommerceError('WordPress plugin returned error: ' . ($data['message'] ?? 'Unknown error'));
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Error fetching products from WordPress plugin', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Process individual WooCommerce product and sync to ERP
     * Used by webhook and sync products (product_start)
     * This function is used into bulk sync and individual sync webhook 
     * @param  int  $business_id
     * @param  array  $woo_product
     * @param  object  $woocommerce_api_settings
     * @return array
     */
    public function processWooCommerceProduct($business_id, $woo_product, $woocommerce_api_settings)
    {
        // Log::info('Processing WooCommerce product', ['woo_product' => $woo_product]);

        if ($woo_product['status'] != 'publish') {
            return [
                'action' => 'not_published',
                'product_name' => 'name',
                'product_id' => 'id'
            ];
        }

        $existing_product = Product::where('business_id', $business_id)
            ->where('woocommerce_product_id', $woo_product['id'])
            ->first();
        if (!$existing_product && !empty($woo_product['sku'])) {
            $existing_product = Product::where('business_id', $business_id)
                ->where('sku', $woo_product['sku'])
                ->first();
        }


        if ($existing_product) {
            // If product exists but doesn't have WooCommerce ID set, update it
            if (empty($existing_product->woocommerce_product_id)) {
                $existing_product->woocommerce_product_id = $woo_product['id'];
                $existing_product->save();
            }
            return $this->updateExistingProduct($business_id, $existing_product, $woo_product, $woocommerce_api_settings);
        } else {
            return $this->createNewProduct($business_id, $woo_product, $woocommerce_api_settings);
        }
    }

    /**
     * Create new product in ERP from WooCommerce product
     *
     * @param  int  $business_id
     * @param  array  $woo_product
     * @param  object  $woocommerce_api_settings
     * @return array
     */
    private function createNewProduct($business_id, $woo_product, $woocommerce_api_settings)
    {

        // Check if we should use enhanced sync style
        $use_enhanced_sync = config('constants.sync_style') === 'managemore';

        if ($use_enhanced_sync) {
            return $this->createNewProductEnhanced($business_id, $woo_product, $woocommerce_api_settings);
        }

        // Get default unit if not specified
        $default_unit = Unit::where('business_id', $business_id)->first();
        if (!$default_unit) {
            $default_unit = Unit::first();
        }

        // Prepare product data
        $product_data = [
            'name' => $woo_product['name'],
            'business_id' => $business_id,
            'type' => $woo_product['type'] == 'simple' ? 'single' : 'variable',
            'unit_id' => $default_unit->id,
            'tax_type' => 'exclusive',
            'enable_stock' => 1 , // $woo_product['manage_stock'] ?? 0,
            'alert_quantity' => 0,
            'sku' => $woo_product['_sku'] ?? $this->generateUniqueSku($business_id, $woo_product['name']),
            'slug' => $woo_product['slug'] ?? null,
            'barcode_type' => 'C128',
            'created_by' => auth()->id() ?? 1,
            'woocommerce_product_id' => $woo_product['id'],
            'product_description' => $woo_product['description'] ?? '',
            'weight' => $woo_product['weight'] ?? null,
            'enable_selling' => 1,
            'productVisibility' => 'public'
        ];

        // // Handle categories
        // if (!empty($woo_product['categories'])) {
        //     $category = $this->findOrCreateCategory($business_id, $woo_product['categories'][0]);
        //     if ($category) {
        //         $product_data['category_id'] = $category['id'];
        //     }
        // }

        // Create the product
        $product = Product::create($product_data);

        // Handle variations for variable products
        if ($woo_product['type'] == 'variable' && !empty($woo_product['variations'])) {
            $this->createProductVariations($business_id, $product, $woo_product);
        } else {
            // Create single variation for simple products
            $this->createSingleVariation($business_id, $product, $woo_product);
        }

        return [
            'action' => 'created',
            'product_name' => $product->name,
            'product_id' => $product->id
        ];
    }

    /**
     * Create new product in ERP with enhanced sync logic (managemore style)
     * (new_product_enhanced)
     * @param  int  $business_id
     * @param  array  $woo_product
     * @param  object  $woocommerce_api_settings
     * @return array
     */
    private function createNewProductEnhanced($business_id, $woo_product, $woocommerce_api_settings)
    {
        // Extract meta data for enhanced sync
        $meta_data = $woo_product['meta_data'] ?? [];
        $meta_map = [];
        foreach ($meta_data as $meta) {
            $meta_map[$meta['key']] = $meta['value'];
        }

        // Get default unit
        $default_unit = Unit::where('business_id', $business_id)->first();
        if (!$default_unit) {
            $default_unit = Unit::first();
        }

        // Prepare enhanced product data
        $product_data = [
            'name' => $woo_product['name'],
            'business_id' => $business_id,
            'type' => empty($woo_product['variations']) ? 'single' : 'variable',
            'unit_id' => $default_unit->id,
            'secondary_unit_id' => null,
            'sub_unit_ids' => null,
            'brand_id' => null,
            'category_id' => null,
            'sub_category_id' => null,
            'tax' => null,
            'tax_type' => 'exclusive',
            'enable_stock' => '1',
            'alert_quantity' => null,
            'sku' => $woo_product['_sku'] ?? $woo_product['sku'] ?? 'NA',
            'slug' => $woo_product['slug'] ?? null,
            'barcode_type' => 'C128',
            'expiry_period' => null,
            'expiry_period_type' => null,
            'enable_sr_no' => '0',
            'weight' => $woo_product['weight'] ?? null,
            'image' => null,
            'product_description' => $woo_product['description'] ?? '',
            'created_by' => auth()->id() ?? 1,
            'preparation_time_in_minutes' => null,
            'warranty_id' => null,
            'is_inactive' => '0',
            'not_for_selling' => '0',
            'created_at' => now(),
            'updated_at' => now(),
            'ml' => $meta_map['mm_product_basis_1'] ?? null,
            'ct' => $meta_map['mm_product_basis_2'] ?? null,
            'productVisibility' => 'public',
            'locationTaxType' => '[null]',
            'maxSaleLimit' => ($meta_map['max_quantity'] ?? null) > 0 ? $meta_map['max_quantity'] : null,
            'enable_selling' => '1',
            'custom_sub_categories' => '[]',
            'top_selling' => $meta_map['total_sales'] ?? 0,
            'woocommerce_product_id' => $woo_product['id']
        ];

        // Create the product
        $product = Product::create($product_data);
        $product->refresh();
        $pid = $product->id;
        $sku = $woo_product['_sku'] ?? $woo_product['sku'] ?? 'NA';
        if ($sku == 'NA') {
            $sku = (new ProductUtil())->generateProductSku($pid, $business_id);
            $product->sku = $sku;
            $product->save();
        }

        // Handle product image
        if (!empty($woo_product['images'])) {
            $thumbnail_url = $woo_product['images'][0]['src'] ?? null;
            if ($thumbnail_url) {
                $image_path = (new SupportUtil())->downloadAndStoreImage($thumbnail_url, $pid, false);
                $product->update(['image' => $image_path]);
            }
        }

        // Set product location
        $this->setProductLocation($pid, $business_id);

        // Handle categories
        if (!empty($woo_product['categories'])) {
            $this->handleProductCategories($product, $woo_product['categories'], $business_id);
        }

        // Handle brand
        if (!empty($woo_product['brand'])) {
            // Log::info('handleProductBrand', ['woo_product' => $woo_product['brand']]);
            $this->handleProductBrand($business_id, $woo_product['brand'], $product);
        }

        // Handle variations
        if (!empty($woo_product['variations'])) {
            $this->createEnhancedProductVariations($business_id, $product, $woo_product);
        } else {
            $this->createEnhancedSingleVariation($business_id, $product, $woo_product, $meta_map);
        }

        return [
            'action' => 'created',
            'product_name' => $product->name,
            'product_id' => $product->id
        ];
    }

    /**
     * Update existing product in ERP from WooCommerce product
     *
     * @param  int  $business_id
     * @param  Product  $existing_product
     * @param  array  $woo_product
     * @param  object  $woocommerce_api_settings
     * @return array
     */
    private function updateExistingProduct($business_id, $existing_product, $woo_product, $woocommerce_api_settings)
    {
        if (!empty($woo_product['categories'])) {
            $this->handleProductCategories($existing_product, $woo_product['categories'], $business_id);
        }

        if (!empty($woo_product['brand'])) {
            $this->handleProductBrand($business_id, $woo_product['brand'], $existing_product);
        }

        $use_enhanced_sync = config('constants.sync_style') === 'managemore';

        if ($use_enhanced_sync) {
            return $this->updateExistingProductEnhanced($business_id, $existing_product, $woo_product, $woocommerce_api_settings);
        }

        $existing_product->name = $woo_product['name'];
        $existing_product->slug = $woo_product['slug'] ?? $existing_product->slug;
        $existing_product->product_description = $woo_product['description'] ?? $existing_product->product_description;
        $existing_product->weight = $woo_product['weight'] ?? $existing_product->weight;
        $existing_product->enable_stock = 1; // $woo_product['manage_stock'] ?? $existing_product->enable_stock;

        // Handle categories
        if (!empty($woo_product['categories'])) {
            $category = $this->findOrCreateCategory($business_id, $woo_product['categories'][0], $existing_product);
            if ($category) {
                $existing_product->category_id = $category['id'];
            }
        }

        $existing_product->save();

        // Update variations
        if ($woo_product['type'] == 'variable' && !empty($woo_product['variations'])) {
            $this->updateProductVariations($business_id, $existing_product, $woo_product);
        } else {
            $this->updateSingleVariation($business_id, $existing_product, $woo_product);
        }

        return [
            'action' => 'updated',
            'product_name' => $existing_product->name,
            'product_id' => $existing_product->id
        ];
    }

    /**
     * Update existing product in ERP with enhanced sync logic (managemore style)
     *
     * @param  int  $business_id
     * @param  Product  $existing_product
     * @param  array  $woo_product
     * @param  object  $woocommerce_api_settings
     * @return array
     */
    private function updateExistingProductEnhanced($business_id, $existing_product, $woo_product, $woocommerce_api_settings)
    {

        // Extract meta data for enhanced sync
        $meta_data = $woo_product['meta_data'] ?? [];
        $meta_map = [];
        foreach ($meta_data as $meta) {
            $meta_map[$meta['key']] = $meta['value'];
        }

        // Update enhanced product data
        $update_data = [
            'type' => $woo_product['type'] == 'simple' ? 'single' : 'variable',
            'sku' => $woo_product['_sku'] ?? $existing_product->sku,
            'slug' => $woo_product['slug'] ?? $existing_product->slug,
            'name' => $woo_product['name'],
            'product_description' => $woo_product['description'] ?? $existing_product->product_description,
            'weight' => $woo_product['weight'] ?? $existing_product->weight,
            'enable_stock' => 1, // $woo_product['manage_stock'] ?? $existing_product->enable_stock,
            'ml' => $meta_map['mm_product_basis_1'] ?? $existing_product->ml,
            'ct' => $meta_map['mm_product_basis_2'] ?? $existing_product->ct,
            'maxSaleLimit' => ($meta_map['max_quantity'] ?? 0) > 0 ? $meta_map['max_quantity'] : $existing_product->maxSaleLimit,
            'top_selling' => $meta_map['total_sales'] ?? $existing_product->top_selling ?? 0,
            'updated_at' => now()
        ];

        // Handle categories with enhanced logic
        // if (!empty($woo_product['categories'])) {
        //     $this->handleProductCategories($existing_product, $woo_product['categories'], $business_id);
        // }

        // Check if maxSaleLimit changed and trigger cart adjustment
        $oldMaxSaleLimit = $existing_product->maxSaleLimit;
        $newMaxSaleLimit = ($meta_map['max_quantity'] ?? 0) > 0 ? $meta_map['max_quantity'] : $existing_product->maxSaleLimit;

        // Update the product
        $existing_product->update($update_data);

        // If maxSaleLimit changed, trigger cart adjustment
        if ($oldMaxSaleLimit != $newMaxSaleLimit && !empty($newMaxSaleLimit)) {
            $productController = new \App\Http\Controllers\ProductController(new ProductUtil(), new ModuleUtil());
            $productController->updateCartItemsForNewLimit(null, $existing_product->id, $newMaxSaleLimit);
        }

        // Handle product image
        if (!empty($woo_product['images'])) {
            $thumbnail_url = $woo_product['images'][0]['src'] ?? null;
            if ($thumbnail_url) {
                $image_path = (new SupportUtil())->downloadAndStoreImage($thumbnail_url, $existing_product->id, false, $woo_product['sku']);
                if ($image_path) {
                    $existing_product->update(['image' => $image_path]);
                }
            }
            if (is_array($woo_product['images'])) {
                ProductGalleryImage::where('product_id', $existing_product->id)->delete();
                foreach ($woo_product['images'] as $index => $image) {
                    if ($index === 0) {
                        continue;
                    }

                    $gallery_url = $image['src'] ?? null;
                    if ($gallery_url) {
                        $image_path = (new SupportUtil())->downloadAndStoreGalleryImage($gallery_url, $existing_product->id, false, $woo_product['sku']);
                        if ($image_path) {
                            ProductGalleryImage::create([
                                'product_id' => $existing_product->id,
                                'image_path' => $image_path,
                            ]);
                        }
                    }
                }
            }
        }

        // Update variations
        if (!empty($woo_product['variations'])) {
            $this->updateEnhancedProductVariations($business_id, $existing_product, $woo_product);
        } else {
            $this->updateEnhancedSingleVariation($business_id, $existing_product, $woo_product, $meta_map);
        }

        // Handle tax type
        $this->setProductTaxType($existing_product->id, $meta_map['mm_indirect_tax_type'] ?? null);

        return [
            'action' => 'updated',
            'product_name' => $existing_product->name,
            'product_id' => $existing_product->id
        ];
    }

    /**
     * Create variations for variable products
     *
     * @param  int  $business_id
     * @param  Product  $product
     * @param  array  $woo_product
     * @return void
     */
    private function createProductVariations($business_id, $product, $woo_product)
    {
        // Log::info('createProductVariations', ['woo_product' => $woo_product]);
        // Create product variation template
        $variation_template = VariationTemplate::where('business_id', $business_id)
            ->where('name', 'Size')
            ->first();

        if (!$variation_template) {
            $variation_template = VariationTemplate::create([
                'name' => 'Size',
                'business_id' => $business_id
            ]);
        }

        // Create product variation
        $product_variation = ProductVariation::create([
            'product_id' => $product->id,
            'name' => 'Size',
            'variation_template_id' => $variation_template->id
        ]);

        // Process each variation
        foreach ($woo_product['variations'] as $woo_variation) {
            $this->createVariation($business_id, $product, $product_variation, $woo_variation);
        }
    }

    /**
     * Create single variation for simple products
     *
     * @param  int  $business_id
     * @param  Product  $product
     * @param  array  $woo_product
     * @return void
     */
    private function createSingleVariation($business_id, $product, $woo_product)
    {
        // Create dummy product variation
        $product_variation = ProductVariation::create([
            'product_id' => $product->id,
            'name' => 'DUMMY',
            'is_dummy' => 1
        ]);

        // Create variation
        $variation_data = [
            'product_id' => $product->id,
            'product_variation_id' => $product_variation->id,
            'name' => 'DUMMY',
            'sub_sku' => $woo_product['_sku'] ?? $product->sku,
            'default_purchase_price' => $woo_product['regular_price'] ?? 0,
            'dpp_inc_tax' => $woo_product['regular_price'] ?? 0,
            'profit_percent' => 0,
            'default_sell_price' => $woo_product['price'] ?? $woo_product['regular_price'] ?? 0,
            'sell_price_inc_tax' => $woo_product['price'] ?? $woo_product['regular_price'] ?? 0,
            'woocommerce_variation_id' => $woo_product['id']
        ];

        Variation::create($variation_data);
    }

    /**
     * Create individual variation
     *
     * @param  int  $business_id
     * @param  Product  $product
     * @param  ProductVariation  $product_variation
     * @param  array  $woo_variation
     * @return void
     */
    private function createVariation($business_id, $product, $product_variation, $woo_variation)
    {
        // Get variation attributes
        $variation_name = 'Default';
        if (!empty($woo_variation['attributes'])) {
            foreach ($woo_variation['attributes'] as $attr_name => $attr_value) {
                if (in_array(strtolower($attr_name), ['size', 'color', 'style'])) {
                    $variation_name = $attr_value;
                    break;
                }
            }
        }

        $variation_data = [
            'product_id' => $product->id,
            'product_variation_id' => $product_variation->id,
            'name' => $variation_name,
            'sub_sku' => $woo_variation['sku'] ?? $product->sku . '-' . $variation_name,
            'default_purchase_price' => $woo_variation['regular_price'] ?? 0,
            'dpp_inc_tax' => $woo_variation['regular_price'] ?? 0,
            'profit_percent' => 0,
            'default_sell_price' => $woo_variation['price'] ?? $woo_variation['regular_price'] ?? 0,
            'sell_price_inc_tax' => $woo_variation['price'] ?? $woo_variation['regular_price'] ?? 0,
            'woocommerce_variation_id' => $woo_variation['id']
        ];

        Variation::create($variation_data);
    }

    /**
     * Update product variations
     *
     * @param  int  $business_id
     * @param  Product  $product
     * @param  array  $woo_product
     * @return void
     */
    private function updateProductVariations($business_id, $product, $woo_product)
    {
        // This is a simplified update - in a full implementation you'd want to handle
        // adding/removing variations based on what exists in WooCommerce
        foreach ($woo_product['variations'] as $woo_variation) {
            $existing_variation = Variation::where('product_id', $product->id)
                ->where('woocommerce_variation_id', $woo_variation['id'])
                ->first();

            if ($existing_variation) {
                $existing_variation->default_sell_price = $woo_variation['price'] ?? $woo_variation['regular_price'] ?? 0;
                $existing_variation->sell_price_inc_tax = $woo_variation['price'] ?? $woo_variation['regular_price'] ?? 0;
                $existing_variation->save();
            }
        }
    }

    /**
     * Update single variation
     *
     * @param  int  $business_id
     * @param  Product  $product
     * @param  array  $woo_product
     * @return void
     */
    private function updateSingleVariation($business_id, $product, $woo_product)
    {
        $variation = $product->variations()->first();
        if ($variation) {
            $variation->default_sell_price = $woo_product['price'] ?? $woo_product['regular_price'] ?? 0;
            $variation->sell_price_inc_tax = $woo_product['price'] ?? $woo_product['regular_price'] ?? 0;
            $variation->save();
        }
    }

    /**
     * Update Product Category and attache and detach from product 
     *
     * @param  int  $business_id
     * @param  array  $woo_category
     * @return array|null
     */
    private function findOrCreateCategory($business_id, $woo_category, $existing_product)
    {
        $category = Category::where('business_id', $business_id)
            ->where('woocommerce_cat_id', $woo_category['id'])
            ->first();

        if (!$category) {
            $category = Category::where('business_id', $business_id)
                ->where('name', $woo_category['name'])
                ->first();
        }
        $logo = null;
        if (!$category) {
            // if have thumbnail id
            if ($woo_category['thumbnail_id']) {
                $thumbnail_url = $woo_category['thumbnail_id'];
                $logo = (new SupportUtil())->downloadAndStoreImage($thumbnail_url, $existing_product->id, false, $existing_product->sku);
            }
            $category = Category::create([
                'name' => $woo_category['name'],
                'business_id' => $business_id,
                'category_type' => 'product',
                'logo' => $logo,
                'woocommerce_cat_id' => $woo_category['id'],
                'created_by' => auth()->id() ?? 1,
                'visibility' => $woo_category['visibility']??'public',
            ]);
        } else if ($category) {
            // if have thumbnail id 
            if ($woo_category['thumbnail_id']) {
                $thumbnail_url = $woo_category['thumbnail_id'];
                $logo = (new SupportUtil())->downloadAndStoreImage($thumbnail_url, $existing_product->id, false, $existing_product->sku);
                $category->update(['logo' => $logo]);
            }
            $category->update([
                'name' => $woo_category['name'],
                'slug' => $woo_category['slug'],
                'visibility' => $woo_category['visibility']??'public',
                'woocommerce_cat_id' => $woo_category['id'],
                'logo' => $logo,
                'updated_at' => now(),
            ]);
        }

        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug
        ];
    }

    /**
     * Generate unique SKU
     *
     * @param  int  $business_id
     * @param  string  $product_name
     * @return string
     */
    private function generateUniqueSku($business_id, $product_name)
    {
        $base_sku = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $product_name));
        $base_sku = substr($base_sku, 0, 10);
        $sku = $base_sku;
        $counter = 1;

        while (Product::where('business_id', $business_id)->where('sku', $sku)->exists()) {
            $sku = $base_sku . $counter;
            $counter++;
        }

        return $sku;
    }


    /**
     * Set product location
     *
     * @param int $product_id
     * @param int $business_id
     * @return void
     */
    private function setProductLocation($product_id, $business_id)
    {
        // Get default location for business
        $default_location = DB::table('business_locations')
            ->where('business_id', $business_id)
            ->first();

        if ($default_location) {
            $exists = DB::table('product_locations')
                ->where('product_id', $product_id)
                ->where('location_id', $default_location->id)
                ->exists();

            if (!$exists) {
                DB::table('product_locations')->insert([
                    'product_id' => $product_id,
                    'location_id' => $default_location->id,
                ]);
            }
        }
    }

    /**
     * Handle product categories with enhanced logic (WooCommerce-centric, using woocommerce_cat_id)
     *
     * @param Product $product
     * @param array $categories
     * @param int $business_id
     * @return void
     */
    private function handleProductCategories($product, $categories, $business_id)
    {
        $category_ids = [];
        $parent_category_id = null;
        $sub_categories = [];

        foreach ($categories as $category_data) {
            $logo = null;
            if (!empty($category_data['thumbnail_id'])) {
                $already_have_logo = Category::where('woocommerce_cat_id', $category_data['id'])->first();
                if ($already_have_logo && $already_have_logo->logo != null) {
                    $logo = $already_have_logo->logo;
                } else {
                    $thumbnail_url = $category_data['thumbnail_id'];
                    try {
                        $woocommerce_api_settings = $this->get_api_settings($business_id);

                        if (empty($woocommerce_api_settings->woocommerce_app_url)) {
                            throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
                        }

                        $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
                        $endpoint = $base_url . '/wp-json/phantasm-erp/v1/get-image-url/' . $thumbnail_url;
                        $response = Http::get($endpoint);
                        if ($response->successful()) {
                            $image_url = $response->json()['image_url'];
                            $logo = (new SupportUtil())->downloadAndStoreImage($image_url, $product->id, false, $product->sku);
                        } else {
                            $logo = null;
                        }
                    } catch (\Exception $e) {
                        Log::error('Error in handleProductBrand: ' . $e->getMessage() . ' at line ' . $e->getLine());
                        $logo = null;
                    }
                }
            }
            $category = Category::updateOrCreate(
                [
                    'name' => $category_data['name'],
                ],
                [
                    'woocommerce_cat_id' => $category_data['id'],
                    'business_id' => $business_id,
                    'created_by' => auth()->id() ?? 1,
                    'slug' => $category_data['slug'],
                    'parent_id' => 0,
                    'visibility' => $category_data['visibility'] ?? $category_data['visiblity'] ?? 'public',
                    'category_type' => 'product',
                    'logo' => $logo,
                ]
            );
            $category_ids[] = $category->id;
            if (($category_data['parent_id'] ?? 0) == 0) {
                $parent_category_id = $category->id;
            } else {
                $sub_categories[] = $category;
            }
        }

        // in all sub categories, pass the parent id , if parent id is not set
        foreach ($sub_categories as $sub_category) {
            if ($sub_category->parent_id == 0) {
                $sub_category->update(['parent_id' => $parent_category_id]);
            }
        }
        // Set main category (parent)
        if ($parent_category_id) {
            $product->update(['category_id' => $parent_category_id]);
        }

        // Set sub category (lowest id among subcategories)
        if (!empty($sub_categories)) {
            $lowest_sub = collect($sub_categories)->sortBy('id')->first();
            $product->update(['sub_category_id' => $lowest_sub->id]);
        }

        // Sync webcategories (add new, remove missing)
        if (method_exists($product, 'webcategories')) {
            $product->webcategories()->sync($category_ids);
        }

        // Save all category IDs as custom_sub_categories (JSON array)
        $product->update(['custom_sub_categories' => json_encode($category_ids)]);
    }

    /**
     * Handle product brand, Please Maintain Brand Unique Name
     *
     * @param int $business_id
     * @param array|null $woo_brand
     * @param Product|null $existing_product
     * @return Brand|null
     */
    private function handleProductBrand($business_id, $woo_brand, $existing_product = null)
    {
        try {
            if (empty($woo_brand) || !is_array($woo_brand)) {
                return null;
            }
            $final_brand = null;
            foreach ($woo_brand as $brand_data) {
                if (empty($brand_data['name'])) {
                    continue;
                }
                $brand = \App\Brands::where('business_id', $business_id)
                    ->where('name', $brand_data['name'])
                    ->first();

                $logo = null;
                if (!$brand) {
                    // if have thumbnail id
                    if (!empty($brand_data['thumbnail_id'])) {
                        $thumbnail_url = $brand_data['thumbnail_id'];
                        try {
                            $woocommerce_api_settings = $this->get_api_settings($business_id);

                            if (empty($woocommerce_api_settings->woocommerce_app_url)) {
                                throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
                            }

                            $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
                            $endpoint = $base_url . '/wp-json/phantasm-erp/v1/get-image-url/' . $thumbnail_url;
                            $response = Http::get($endpoint);
                            if ($response->successful()) {
                                $image_url = $response->json()['image_url'];
                                $logo = (new SupportUtil())->downloadAndStoreImage($image_url, $existing_product->id, false, $existing_product->sku);
                            } else {
                                $logo = null;
                            }
                        } catch (\Exception $e) {
                            Log::error('Error in handleProductBrand: ' . $e->getMessage() . ' at line ' . $e->getLine());
                            $logo = null;
                        }
                    }
                    $brand = \App\Brands::updateOrCreate([
                        'slug' => $brand_data['slug'] ?? 'NA',
                    ], [
                        'name' => $brand_data['name'],
                        // 'name' => $brand_data['name'],
                        'visiblity' => $brand_data['visiblity'] ?? null,
                        'business_id' => $business_id,
                        'logo' => $logo,
                        'created_by' => auth()->id() ?? 1
                    ]);
                } else {
                    if ($brand->logo != null) {
                        $logo = $brand->logo;
                    } else {
                        // Update logo if thumbnail_id is present
                        if (!empty($brand_data['thumbnail_id'])) {
                            $thumbnail_url = $brand_data['thumbnail_id'];
                            try {
                                $woocommerce_api_settings = $this->get_api_settings($business_id);

                                if (empty($woocommerce_api_settings->woocommerce_app_url)) {
                                    throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
                                }

                                $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
                                $endpoint = $base_url . '/wp-json/phantasm-erp/v1/get-image-url/' . $thumbnail_url;
                                $response = Http::get($endpoint);
                                if ($response->successful()) {
                                    $image_url = $response->json()['image_url'];
                                    $logo = (new SupportUtil())->downloadAndStoreImage($image_url, $existing_product->id, false, $existing_product->sku);
                                } else {
                                    $logo = null;
                                }
                            } catch (\Exception $e) {
                                Log::error('Error in handleProductBrand: ' . $e->getMessage() . ' at line ' . $e->getLine());
                                $logo = null;
                            }
                        }
                        $brand->updateOrCreate([
                            'slug' => $brand_data['slug'] ?? 'NA',
                        ], [
                            'name' => $brand_data['name'],
                            'visiblity' => $brand_data['visiblity'] ?? null,
                            'logo' => $logo != null ? $logo : $brand->logo,
                            'updated_at' => now(),
                        ]);
                    }
                }
                $final_brand = $brand;
            }
            // Add brand to product
            if ($existing_product && $final_brand) {
                $brand_id = $final_brand->id ?? null;
                $existing_product->brand_id = $brand_id;
                $existing_product->save();
            }
            return $final_brand;
        } catch (\Exception $e) {
            Log::error('Error in handleProductBrand: ' . $e->getMessage() . ' at line ' . $e->getLine() . ' in file ' . $e->getFile());
            return null;
        }
    }

    /**
     * Create enhanced product variations with dynamic pricing
     *
     * @param int $business_id
     * @param Product $product
     * @param array $woo_product
     * @return void
     */
    private function createEnhancedProductVariations($business_id, $product, $woo_product)
    {
        $pid = $product->id;

        $counter = 1;
        foreach ($woo_product['variations'] as $variation) {
            $clean_attribute_name_combined = '';
            foreach ($variation['attributes'] as $attribute_name => $attribute_value) {
                $clean_attribute_name = str_replace('attribute_', '', $attribute_name);
                $clean_attribute_name = ucwords(str_replace('-', ' ', $clean_attribute_name));
                $clean_attribute_name_combined .= $clean_attribute_name . ' ';
            }
            // Flavor + Size + Color
            $variation_template = VariationTemplate::updateOrCreate(
                ['name' => $clean_attribute_name_combined], // Flavor 
                ['business_id' => $business_id]
            );
            $productVariation = ProductVariation::updateOrCreate(
                ['product_id' => $pid],
                [
                    'product_id' => $pid,
                    'variation_template_id' => $variation_template->id, // 2
                    'name' => $clean_attribute_name, // Flavor 
                    'is_dummy' => 0,
                ]
            );

            $this->createEnhancedVariation($business_id, $product, $productVariation, $variation, $variation_template, $counter);
            $counter++;
        }
    }

    /**
     * Create enhanced single variation for simple products
     *
     * @param int $business_id
     * @param Product $product
     * @param array $woo_product
     * @param array $meta_map
     * @return void
     */
    private function createEnhancedSingleVariation($business_id, $product, $woo_product, $meta_map)
    {
        $pid = $product->id;

        // Create dummy product variation
        $productVariation = ProductVariation::updateOrCreate(
            ['product_id' => $pid],
            [
                'product_id' => $pid,
                'variation_template_id' => null,
                'name' => 'DUMMY',
                'is_dummy' => 1,
            ]
        );

        // Extract pricing data from meta
        $silverPrice = $meta_map['wholesale_customer_wholesale_price'] ?? null;
        $goldPrice = $meta_map['mm_price_2_wholesale_price'] ?? null;
        $platinumPrice = $meta_map['mm_price_3_wholesale_price'] ?? null;
        $diamondPrice = $meta_map['mm_price_4_wholesale_price'] ?? null;
        $lowestPrice = $meta_map['mm_product_lowest_price'] ?? $woo_product['price'] ?? 0;
        $costPrice = $meta_map['mm_product_cost'] ?? 0;
        $qty = $meta_map['stock_quantity'] ?? $meta_map['_stock'] ?? $meta_map['stock'] ?? $woo_product['stock'] ??  0;
        $manage_stock = $meta_map['manage_stock'] ?? $meta_map['_manage_stock'] ?? $woo_product['manage_stock'] ?? false;
        $instock = $meta_map['_stock_status'] ?? 'instock';
        $var_sku = $meta_map['_sku'] ?? $woo_product['sku'];
        $var_barcode_no = $meta_map['mm_product_upc'] ?? null;
        $max_quantity_var = $meta_map['max_quantity_var'] ?? null;

        // Check if var_maxSaleLimit changed for existing variation
        $existingVariation = Variation::where('sub_sku', $var_sku)->first();
        $oldVarMaxSaleLimit = $existingVariation ? $existingVariation->var_maxSaleLimit : null;

        // Create variation
        $erpVariation = Variation::updateOrCreate(
            ['sub_sku' => $var_sku],
            [
                'name' => 'DUMMY',
                'product_id' => $pid,
                'product_variation_id' => $productVariation->id,
                'variation_value_id' => null,
                'default_purchase_price' => $costPrice,
                'dpp_inc_tax' => $costPrice,
                'profit_percent' => '0.0000',
                'default_sell_price' => $lowestPrice,
                'sell_price_inc_tax' => $lowestPrice,
                'var_barcode_no' => $var_barcode_no,
                'var_maxSaleLimit' => $max_quantity_var
            ]
        );

        // If var_maxSaleLimit changed, trigger cart adjustment
        if ($oldVarMaxSaleLimit != $max_quantity_var && !empty($max_quantity_var)) {
            $productController = new \App\Http\Controllers\ProductController(new ProductUtil(), new ModuleUtil());
            $productController->updateCartItemsForNewLimit(null, $pid, $max_quantity_var);
        }

        // Create dynamic price groups
        $this->createDynamicPriceGroups($erpVariation->id, [
            'lowest' => $lowestPrice,    // Lowest price (highest priority)
            'silver' => $silverPrice,    // Silver price group
            'gold' => $goldPrice,        // Gold price group
            'platinum' => $platinumPrice, // Platinum price group
            'diamond' => $diamondPrice   // Diamond price group
        ], $business_id);

        // Handle stock
        if ($instock == 'instock' && $manage_stock && $qty) {
            $this->setVariationStock($erpVariation->id, $pid, $qty, $business_id);
        }

        // Handle tax type
        $this->setProductTaxType($pid, $meta_map['mm_indirect_tax_type'] ?? null);
    }

    /**
     * Create enhanced variation with dynamic pricing
     *
     * @param int $business_id
     * @param Product $product
     * @param ProductVariation $productVariation
     * @param array $variation
     * @return void
     */
    private function createEnhancedVariation($business_id, $product, $productVariation, $variation, $variationTemplate, $counter)
    {
        $pid = $product->id;

        // Extract meta data
        // $meta_map = [];
        // foreach ($variation['meta_data'] ?? [] as $meta) {
        //     $meta_map[$meta['key']] = $meta['value'];
        // }

        $silverPrice = $variation['wholesale_customer_wholesale_price'] ?? $variation['regular_price'] ?? $variation['price'] ?? null;
        $goldPrice = $variation['mm_price_2_wholesale_price'] ?? null;
        $platinumPrice = $variation['mm_price_3_wholesale_price'] ?? null;
        $diamondPrice = $variation['mm_price_4_wholesale_price'] ?? null;
        $lowestPrice = $variation['mm_product_lowest_price'] ?? $variation['price'] ?? 0;
        $costPrice = $variation['mm_product_cost'] ?? null;
        $qty = $variation['_stock'] ?? 0;
        $manage_stock = $variation['_manage_stock'] ?? false;
        $instock = $variation['_stock_status'] ?? 'instock';
        $var_sku = $variation['_sku'] ?? $variation['sku'];
        $var_barcode_no = $variation['mm_product_upc'] ?? null;
        $max_quantity_var = $variation['max_quantity_var'] ?? null;
        $variationName = '';

        // Handle attributes
        if (!empty($variation['attributes'])) {
            foreach ($variation['attributes'] as $attr_name => $attr_value) {
                // if (in_array(strtolower($attr_name), ['size', 'color', 'style', 'flavor'])) {
                $variationName .= $attr_value . ' ';
                // break;
                // }
            }
        }


        $variationTemplateValue = VariationValueTemplate::updateOrCreate(
            ['name' => trim($variationName)], // Peach Ice
            ['variation_template_id' => $variationTemplate->id] // Flavor
        );

        $baseSku = $variation['_sku'] ?? $variation['sku'] ?? null;
        if (empty($baseSku)) {
            $baseSku = $product['sku'] . str_pad($counter, 2, '0', STR_PAD_LEFT);
        }

        $erpSku = $baseSku;
        $suffix = 1;
        while (
            Variation::where('sub_sku', $erpSku)
            ->where('woocommerce_variation_id', '!=', $variation['id'])
            ->exists()
        ) {
            $erpSku = $baseSku . $suffix;
            $suffix++;
        }
        if (!$variation['id']) {
            return;
        }

        $erpVariation = Variation::updateOrCreate(
            ['woocommerce_variation_id' => $variation['id']],
            [
                'sub_sku' => $erpSku,
                'name' => $variationName,
                'product_id' => $pid,
                'product_variation_id' => $productVariation->id,
                'variation_value_id' => $variationTemplateValue->id,
                'default_purchase_price' => $costPrice,
                'dpp_inc_tax' => $costPrice,
                'profit_percent' => '0.0000',
                'default_sell_price' => $silverPrice,
                'sell_price_inc_tax' => $silverPrice,
                'var_barcode_no' => $var_barcode_no,
                'var_maxSaleLimit' => $max_quantity_var
            ]
        );

        // Handle variation image
        if (!empty($variation['image'])) {
            $thumbnail_url = $variation['image']['src'] ?? null;
            if ($thumbnail_url) {
                (new SupportUtil())->downloadAndStoreImage($thumbnail_url, $erpVariation->id, true);
                // $this->downloadAndStoreImage($thumbnail_url, $erpVariation->id, true);
            }
        }

        // Create dynamic price groups
        $this->createDynamicPriceGroups($erpVariation->id, [
            'silver' => $silverPrice,
            'gold' => $goldPrice,
            'platinum' => $platinumPrice,
            'diamond' => $diamondPrice,
            'lowest' => $lowestPrice
        ], $business_id);

        // Handle stock
        if ($instock == 'instock' && $manage_stock && $qty) {
            $this->setVariationStock($erpVariation->id, $pid, $qty, $business_id);
        }
    }

    /**
     * Create dynamic price groups mapping for variations
     *
     * @param int $variation_id
     * @param array $prices
     * @param int $business_id
     * @return void
     */
    private function createDynamicPriceGroups($variation_id, $prices, $business_id)
    {
        // Get all available price groups for this business
        $availablePriceGroups = DB::table('selling_price_groups')
            ->where('business_id', $business_id)
            ->orderBy('id')
            ->get();

        if ($availablePriceGroups->isEmpty()) {
            Log::warning('No price groups found for business', [
                'business_id' => $business_id,
                'variation_id' => $variation_id
            ]);
            return;
        }

        // Map WooCommerce prices to ERP price groups dynamically
        $priceMapping = $this->mapWooCommercePricesToERPPriceGroups($prices, $availablePriceGroups);

        // Create price group records for each mapped price
        foreach ($priceMapping as $priceGroupId => $priceData) {
            if (!empty($priceData['price']) && $priceData['price'] > 0) {
                try {
                    VariationGroupPrice::updateOrCreate(
                        [
                            'variation_id' => $variation_id,
                            'price_group_id' => $priceGroupId
                        ],
                        [
                            'price_group_id' => $priceGroupId,
                            'price_inc_tax' => $priceData['price'],
                            'price_type' => 'fixed',
                            'variation_id' => $variation_id,
                        ]
                    );

                    // Log::info('Price group created/updated', [
                    //     'variation_id' => $variation_id,
                    //     'price_group_id' => $priceGroupId,
                    //     'price' => $priceData['price'],
                    //     'price_type' => $priceData['price_type'],
                    //     'price_group_name' => $priceData['price_group_name']
                    // ]);
                } catch (\Exception $e) {
                    Log::error('Error creating price group', [
                        'variation_id' => $variation_id,
                        'price_group_id' => $priceGroupId,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    /**
     * Map WooCommerce prices to ERP price groups dynamically
     *
     * @param array $wooPrices
     * @param \Illuminate\Support\Collection $availablePriceGroups
     * @return array
     */
    private function mapWooCommercePricesToERPPriceGroups($wooPrices, $availablePriceGroups)
    {
        $priceMapping = [];

        // Filter out empty or zero prices
        $validPrices = [];
        foreach ($wooPrices as $priceType => $price) {
            if (!empty($price) && $price > 0) {
                $validPrices[$priceType] = $price;
            }
        }

        if (empty($validPrices)) {
            Log::warning('No valid prices found for mapping', [
                'woo_prices' => $wooPrices
            ]);
            return $priceMapping;
        }

        // Get configured price group mappings from settings
        try {
            $business_id = request()->session()->get('business.id') ?? 1;
        } catch (\Throwable $th) {
            $business_id = 1;
        }
        $business = \App\Business::find($business_id);
        $configuredMappings = [];

        if (!empty($business->woocommerce_api_settings)) {
            $settings = json_decode($business->woocommerce_api_settings, true);
            $configuredMappings = !empty($settings['price_group_mappings']) ? $settings['price_group_mappings'] : [];
        }

        // Map prices to configured price group IDs
        foreach ($validPrices as $priceType => $price) {
            // Check if this price type is configured and enabled
            if (
                isset($configuredMappings[$priceType]) &&
                !empty($configuredMappings[$priceType]['enabled']) &&
                !empty($configuredMappings[$priceType]['erp_price_group_id'])
            ) {

                $priceGroupId = $configuredMappings[$priceType]['erp_price_group_id'];

                // Check if this price group exists in available price groups
                $priceGroup = $availablePriceGroups->firstWhere('id', $priceGroupId);

                if ($priceGroup) {
                    $priceMapping[$priceGroupId] = [
                        'price' => $price,
                        'price_type' => $priceType,
                        'price_group_name' => $priceGroup->name,
                        'assigned_id' => $priceGroupId,
                        'configured' => true
                    ];

                    // Log::info('Price mapped to configured group', [
                    //     'price_type' => $priceType,
                    //     'price' => $price,
                    //     'price_group_id' => $priceGroupId,
                    //     'price_group_name' => $priceGroup->name
                    // ]);
                } else {
                    Log::warning('Configured price group not found in available groups', [
                        'price_type' => $priceType,
                        'price' => $price,
                        'configured_group_id' => $priceGroupId,
                        'available_group_ids' => $availablePriceGroups->pluck('id')->toArray()
                    ]);
                }
            } else {
                Log::info('Price type not configured or disabled', [
                    'price_type' => $priceType,
                    'price' => $price,
                    'configured' => isset($configuredMappings[$priceType]),
                    'enabled' => isset($configuredMappings[$priceType]) ? $configuredMappings[$priceType]['enabled'] : false
                ]);
            }
        }

        // Log::info('Price mapping completed', [
        //     'mapped_prices' => count($priceMapping),
        //     'total_prices' => count($validPrices),
        //     'configured_mappings' => count($configuredMappings),
        //     'available_groups' => count($availablePriceGroups),
        //     'mapping' => $priceMapping
        // ]);

        return $priceMapping;
    }

    /**
     * Set variation stock
     *
     * @param int $variation_id
     * @param int $product_id
     * @param int $qty
     * @param int $business_id
     * @return void
     */
    private function setVariationStock($variation_id, $product_id, $qty, $business_id, $product_variation_id = null)
    {
        // Get default location
        $default_location = DB::table('business_locations')
            ->where('business_id', $business_id)
            ->first();

        if ($default_location) {
            VariationLocationDetails::updateOrCreate(
                ['variation_id' => $variation_id],
                [
                    'product_id' => $product_id,
                    'product_variation_id' => $product_variation_id ?? '',
                    'variation_id' => $variation_id,
                    'location_id' => $default_location->id,
                    'qty_available' => $qty,
                    'in_stock_qty' => $qty,
                ]
            );
        }
    }

    /**
     * Set product tax type based on meta data // Todo: future updates (dynamic tax rates)
     * @version 1.0.0
     * @author Utkarsh Shukla 
     * @param int $product_id
     * @param string $tax_type
     * @return void
     */
    private function setProductTaxType($product_id, $tax_type)
    {
        $locationTaxType = [];

        // Map tax types to location IDs
        switch ($tax_type) {
            case '14346':
                $locationTaxType = [1];
                break;
            case '14347':
                $locationTaxType = [6];
                break;
            case '14344':
                $locationTaxType = [2];
                break;
            case '14343':
                $locationTaxType = [5];
                break;
            case '14345':
                $locationTaxType = [3];
                break;
        }

        if (!empty($locationTaxType)) {
            $locationTaxTypeString = '[' . implode(',', array_map(function ($item) {
                return '"' . $item . '"';
            }, $locationTaxType)) . ']';

            DB::table('products')
                ->where('id', $product_id)
                ->update(['locationTaxType' => $locationTaxTypeString]);
        }
    }

    /**
     * Update enhanced product variations
     *
     * @param int $business_id
     * @param Product $product
     * @param array $woo_product
     * @return void
     */
    private function updateEnhancedProductVariations($business_id, $product, $woo_product)
    {
        $pid = $product->id;
        $counter = 1;
        foreach ($woo_product['variations'] as $variation) {
            $clean_attribute_name_combined = '';
            foreach ($variation['attributes'] as $attribute_name => $attribute_value) {
                $clean_attribute_name = str_replace('attribute_', '', $attribute_name);
                $clean_attribute_name = ucwords(str_replace('-', ' ', $clean_attribute_name));
                $clean_attribute_name_combined .= $clean_attribute_name . ' ';
            }
            $variation_template = VariationTemplate::updateOrCreate(
                ['name' => $clean_attribute_name_combined],
                ['business_id' => $business_id]
            );
            $productVariation = ProductVariation::updateOrCreate(
                ['product_id' => $pid],
                [
                    'product_id' => $pid,
                    'variation_template_id' => $variation_template->id,
                    'name' => $clean_attribute_name_combined,
                    'is_dummy' => 0,
                ]
            );
            $this->updateEnhancedVariation($business_id, $product, $productVariation, $variation, $variation_template, $counter);
            $counter++;
        }
    }

    /**
     * Update enhanced single variation
     *
     * @param int $business_id
     * @param Product $product
     * @param array $woo_product
     * @param array $meta_map
     * @return void
     */
    private function updateEnhancedSingleVariation($business_id, $product, $woo_product, $meta_map)
    {
        $pid = $product->id;

        // Get or create product variation
        $productVariation = ProductVariation::where('product_id', $pid)->first();
        if (!$productVariation) {
            $productVariation = ProductVariation::create([
                'product_id' => $pid,
                'variation_template_id' => null,
                'name' => 'DUMMY',
                'is_dummy' => 1,
            ]);
        }

        // Extract pricing data from meta
        $silverPrice = $meta_map['wholesale_customer_wholesale_price'] ?? null;
        $goldPrice = $meta_map['mm_price_2_wholesale_price'] ?? null;
        $platinumPrice = $meta_map['mm_price_3_wholesale_price'] ?? null;
        $diamondPrice = $meta_map['mm_price_4_wholesale_price'] ?? null;
        $lowestPrice = $meta_map['mm_product_lowest_price'] ?? $woo_product['price'] ?? 0;
        $costPrice = $meta_map['mm_product_cost'] ?? 0;
        $qty = $meta_map['stock_quantity'] ?? $meta_map['_stock'] ?? $meta_map['stock'] ?? $woo_product['stock'] ??  0;
        $manage_stock = $meta_map['manage_stock'] ?? $meta_map['_manage_stock'] ?? $woo_product['manage_stock'] ?? false;
        $instock = $meta_map['_stock_status'] ?? 'instock';
        $var_sku = $meta_map['_sku'] ?? $woo_product['sku'];
        $var_barcode_no = $meta_map['mm_product_upc'] ?? null;
        $max_quantity_var = $meta_map['max_quantity_var'] ?? null;

        // Update or create variation
        $erpVariation = Variation::updateOrCreate(
            ['sub_sku' => $var_sku],
            [
                'name' => 'DUMMY',
                'product_id' => $pid,
                'product_variation_id' => $productVariation->id,
                'variation_value_id' => null,
                'default_purchase_price' => $costPrice,
                'dpp_inc_tax' => $costPrice,
                'profit_percent' => '0.0000',
                'default_sell_price' => $lowestPrice,
                'sell_price_inc_tax' => $lowestPrice,
                'var_barcode_no' => $var_barcode_no,
                'var_maxSaleLimit' => $max_quantity_var
            ]
        );

        // Update dynamic price groups
        $this->createDynamicPriceGroups($erpVariation->id, [
            'silver' => $silverPrice,
            'gold' => $goldPrice,
            'platinum' => $platinumPrice,
            'diamond' => $diamondPrice,
            'lowest' => $lowestPrice
        ], $business_id);

        $erp_product = Product::where('business_id', $business_id)
            ->where('woocommerce_product_id', $woo_product['id'])
            ->first();

        $variation = Variation::where('product_id', $erp_product->id)->first();
        if ($variation) {
            $this->updateVariationStockFromWooCommerceData($variation, $woo_product, $business_id);
        }

        // Update stock
        // if ($instock == 'instock' && $manage_stock && $qty) {
        //     $this->setVariationStock($erpVariation->id, $pid, $qty, $business_id);
        // }
    }

    /**
     * Update enhanced variation
     *
     * @param int $business_id
     * @param Product $product
     * @param ProductVariation $productVariation
     * @param array $variation
     * @return void
     */
    private function updateEnhancedVariation($business_id, $product, $productVariation, $variation, $variationTemplate, $counter)
    {
        $pid = $product->id;

        // Extract meta data
        // $meta_map = [];
        // foreach ($variation['meta_data'] ?? [] as $meta) {
        //     $meta_map[$meta['key']] = $meta['value'];
        // }

        $silverPrice = $variation['wholesale_customer_wholesale_price'] ?? null;
        $goldPrice = $variation['mm_price_2_wholesale_price'] ?? null;
        $platinumPrice = $variation['mm_price_3_wholesale_price'] ?? null;
        $diamondPrice = $variation['mm_price_4_wholesale_price'] ?? null;

        $lowestPrice = $variation['mm_product_lowest_price'] ?? $variation['price'] ?? 0;
        $costPrice = $variation['mm_product_cost'] ?? 0;
        $qty = $variation['stock_quantity'] ?? $variation['_stock'] ?? 0;
        $manage_stock = $variation['manage_stock'] ?? $variation['_manage_stock'] ?? false;
        $instock = $variation['_stock_status'] ?? 'instock';
        $var_barcode_no = $variation['mm_product_upc'] ?? null;
        $max_quantity_var = $variation['max_quantity_var'] ?? null;
        // Log::info('prices', [$silverPrice, $goldPrice, $platinumPrice, $diamondPrice, $lowestPrice, $costPrice, $qty, $manage_stock, $instock, $var_barcode_no, $max_quantity_var]);


        // Build variation name
        $variationName = '';
        if (!empty($variation['attributes'])) {
            foreach ($variation['attributes'] as $attr_name => $attr_value) {
                $variationName .= $attr_value . ' ';
            }
        }

        $variationTemplateValue = VariationValueTemplate::updateOrCreate(
            ['name' => trim($variationName)],
            ['variation_template_id' => $variationTemplate->id]
        );
        $baseSku = $variation['_sku'] ?? $variation['sku'] ?? null;
        if (empty($baseSku)) {
            $baseSku = $product['sku'] . str_pad($counter, 2, '0', STR_PAD_LEFT);
        }

        $erpSku = $baseSku;
        $suffix = 1;
        while (
            Variation::where('sub_sku', $erpSku)
            ->where('woocommerce_variation_id', '!=', $variation['id'])
            ->exists()
        ) {
            $erpSku = $baseSku . $suffix;
            $suffix++;
        }
        if (!$variation['id']) {
            return;
        }
        $erpVariation = Variation::updateOrCreate(
            ['woocommerce_variation_id' => $variation['id']],
            [
                'sku' => $erpSku,
                'sub_sku' => $erpSku,
                'name' => trim($variationName),
                'product_id' => $pid,
                'product_variation_id' => $productVariation->id,
                'variation_value_id' => $variationTemplateValue->id,
                'default_purchase_price' => $costPrice,
                'dpp_inc_tax' => $costPrice,
                'profit_percent' => '0.0000',
                'default_sell_price' => $lowestPrice,
                'sell_price_inc_tax' => $lowestPrice,
                'var_barcode_no' => $var_barcode_no,
                'var_maxSaleLimit' => $max_quantity_var
            ]
        );
        if (!empty($variation['image'])) {
            $thumbnail_url = $variation['image']['src'] ?? null;
            if ($thumbnail_url) {
                (new SupportUtil())->downloadAndStoreImage($thumbnail_url, $erpVariation->id, true);
            }
        }

        $this->createDynamicPriceGroups($erpVariation->id, [
            'silver' => $silverPrice, // should get mapped to erp id 1
            'gold' => $goldPrice, // should get mapped to erp id 2
            'platinum' => $platinumPrice, // should get mapped to erp id 4
            'diamond' => $diamondPrice, // should get mapped to erp id 5
            'lowest' => $lowestPrice // should get mapped to erp id 3
        ], $business_id);

        if ($instock == 'instock' && $manage_stock && $qty) {
            $this->setVariationStock($erpVariation->id, $pid, $qty, $business_id, $productVariation->id);
        }
    }


    /**
     * Check for potential duplicate products in ERP
     *
     * @param int $business_id
     * @param array $woo_product
     * @return array
     */
    private function checkForDuplicateProducts($business_id, $woo_product)
    {
        $duplicates = [];

        // Check by name
        $by_name = Product::where('business_id', $business_id)
            ->where('name', $woo_product['name'])
            ->get();
        if ($by_name->count() > 0) {
            $duplicates['by_name'] = $by_name->pluck('id', 'woocommerce_product_id')->toArray();
        }

        // Check by SKU
        if (!empty($woo_product['sku'])) {
            $by_sku = Product::where('business_id', $business_id)
                ->where('sku', $woo_product['sku'])
                ->get();
            if ($by_sku->count() > 0) {
                $duplicates['by_sku'] = $by_sku->pluck('id', 'woocommerce_product_id')->toArray();
            }
        }

        return $duplicates;
    }

    /**
     * Get sync summary for debugging
     *
     * @param int $business_id
     * @param array $woo_products
     * @return array
     */
    private function getSyncSummary($business_id, $woo_products)
    {
        $summary = [
            'total_woo_products' => count($woo_products),
            'existing_in_erp' => 0,
            'new_to_erp' => 0,
            'products_with_woo_id' => 0,
            'products_without_woo_id' => 0,
            'duplicate_names' => 0,
            'duplicate_skus' => 0
        ];

        foreach ($woo_products as $woo_product) {
            // Check if exists in ERP
            $exists = Product::where('business_id', $business_id)
                ->where('woocommerce_product_id', $woo_product['id'])
                ->exists();

            if ($exists) {
                $summary['existing_in_erp']++;
            } else {
                $summary['new_to_erp']++;
            }

            // Check for products with WooCommerce ID
            $with_woo_id = Product::where('business_id', $business_id)
                ->where('woocommerce_product_id', $woo_product['id'])
                ->count();
            $summary['products_with_woo_id'] += $with_woo_id;

            // Check for products without WooCommerce ID
            $without_woo_id = Product::where('business_id', $business_id)
                ->whereNull('woocommerce_product_id')
                ->where('name', $woo_product['name'])
                ->count();
            $summary['products_without_woo_id'] += $without_woo_id;

            // Check for duplicates
            $duplicates = $this->checkForDuplicateProducts($business_id, $woo_product);
            if (isset($duplicates['by_name'])) {
                $summary['duplicate_names']++;
            }
            if (isset($duplicates['by_sku'])) {
                $summary['duplicate_skus']++;
            }
        }

        return $summary;
    }

    /**
     * Synchronizes product quantities from WooCommerce to ERP using super-fast endpoint
     *
     * @param  int  $business_id
     * @param  int  $user_id
     * @param  int  $chunk_size
     * @param  int  $offset
     * @return array
     */
    public function syncProductQuantitiesFromWooToErp($business_id, $user_id, $chunk_size = 50, $offset = 0)
    {
        $woocommerce_api_settings = $this->get_api_settings($business_id);

        if (empty($woocommerce_api_settings->woocommerce_app_url)) {
            throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
        }

        // Use super-fast quantities endpoint
        $quantities_data = $this->getQuantitiesFromWordPressPlugin($business_id, [
            'per_page' => $chunk_size,
            'offset' => $offset
        ]);

        if (empty($quantities_data) || !isset($quantities_data['data'])) {
            return [
                'success' => true,
                'updated' => 0,
                'skipped' => 0,
                'total_processed' => 0,
                'has_more' => false,
                'next_offset' => $offset
            ];
        }

        $woo_products = $quantities_data['data'];
        $pagination = $quantities_data['pagination'] ?? [];
        $performance = $quantities_data['performance'] ?? [];

        $updated_count = 0;
        $skipped_count = 0;
        $total_products = count($woo_products);
        $has_more = isset($pagination['has_more']) ? $pagination['has_more'] : false;

        Log::info('Starting super-fast quantity sync from WooCommerce to ERP', [
            'business_id' => $business_id,
            'chunk_size' => $chunk_size,
            'offset' => $offset,
            'total_products_in_chunk' => $total_products,
            'has_more' => $has_more,
            'performance' => $performance
        ]);

        foreach ($woo_products as $woo_product) {
            try {
                // Find corresponding ERP product
                $erp_product = Product::where('business_id', $business_id)
                    ->where('woocommerce_product_id', $woo_product['id'])
                    ->first();

                if (!$erp_product) {
                    $skipped_count++;
                    continue;
                }

                // Handle simple products
                if (empty($woo_product['variations'])) {
                    $variation = Variation::where('product_id', $erp_product->id)->first();
                    if ($variation) {
                        $this->updateVariationStockFromWooCommerceData($variation, $woo_product, $business_id);
                        $updated_count++;
                    }
                }
                // Handle variable products
                elseif (!empty($woo_product['variations'])) {
                    foreach ($woo_product['variations'] as $woo_variation) {
                        $erp_variation = Variation::where('product_id', $erp_product->id)
                            ->where('woocommerce_variation_id', $woo_variation['id'])
                            ->first();

                        if ($erp_variation) {
                            $this->updateVariationStockFromWooCommerceData($erp_variation, $woo_variation, $business_id);
                            $updated_count++;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error syncing product quantity from WooCommerce', [
                    'woo_product_id' => $woo_product['id'] ?? 'N/A',
                    'sku' => $woo_product['sku'] ?? 'N/A',
                    'error' => $e->getMessage()
                ]);
                $skipped_count++;
            }
        }

        // Create sync log for this chunk
        $this->createSyncLog($business_id, $user_id, 'product_quantities_from_woo', 'synced', [
            'updated' => $updated_count,
            'skipped' => $skipped_count,
            'chunk_size' => $chunk_size,
            'offset' => $offset,
            'has_more' => $has_more,
            'performance' => $performance
        ]);

        Log::info('Super-fast quantity sync completed', [
            'business_id' => $business_id,
            'updated' => $updated_count,
            'skipped' => $skipped_count,
            'offset' => $offset,
            'has_more' => $has_more,
            'performance' => $performance
        ]);

        return [
            'success' => true,
            'updated' => $updated_count,
            'skipped' => $skipped_count,
            'total_processed' => $total_products,
            'has_more' => $has_more,
            'next_offset' => $offset + $chunk_size
        ];
    }

    /**
     * Get quantities data from WordPress plugin super-fast endpoint
     *
     * @param  int  $business_id
     * @param  array  $params
     * @return array
     */
    private function getQuantitiesFromWordPressPlugin($business_id, $params)
    {
        $woocommerce_api_settings = $this->get_api_settings($business_id);

        if (empty($woocommerce_api_settings->woocommerce_app_url)) {
            throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
        }

        $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
        $endpoint = $base_url . '/wp-json/phantasm-erp/v1/quantities';

        // Build query string
        $query_string = http_build_query($params);
        $url = $endpoint . '?' . $query_string;

        try {
            $response = Http::withHeaders([
                // 'Authorization' => 'Basic ' . base64_encode($woocommerce_api_settings->woocommerce_consumer_key . ':' . $woocommerce_api_settings->woocommerce_consumer_secret),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
                ->timeout(300) // 5 minutes timeout for large datasets
                ->get($url);

            if (!$response->successful()) {
                Log::error('WordPress plugin quantities API request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new WooCommerceError('Failed to fetch quantities from WordPress plugin: ' . $response->status());
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                throw new WooCommerceError('WordPress plugin returned error: ' . ($data['message'] ?? 'Unknown error'));
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Error fetching quantities from WordPress plugin', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update variation stock from WooCommerce data (optimized for super-fast endpoint)
     *
     * @param  Variation  $variation
     * @param  array  $woo_data
     * @param  int  $business_id
     * @return void
     */
    private function updateVariationStockFromWooCommerceData($variation, $woo_data, $business_id)
    {
        // Get stock quantity from optimized data structure
        $stock_quantity = $woo_data['stock_quantity'] ?? 0;
        $manage_stock = $woo_data['manage_stock'] ?? false;
        $stock_status = $woo_data['stock_status'] ?? 'instock';

        if ($stock_quantity == 0 || $manage_stock == false) {
            Log::info('stock_quantity is 0 or manage_stock is false', [$woo_data]);
        }
        // Get default location
        $default_location = DB::table('business_locations')
            ->where('business_id', $business_id)
            ->first();

        if ($default_location && $stock_quantity >= 0) {
            // Update variation location details
            VariationLocationDetails::updateOrCreate(
                ['variation_id' => $variation->id],
                [
                    'product_id' => $variation->product_id,
                    'product_variation_id' => $variation->product_variation_id,
                    'variation_id' => $variation->id,
                    'location_id' => $default_location->id,
                    'qty_available' => $stock_quantity,
                    'in_stock_qty' => $stock_quantity,
                ]
            );
        }
    }


    /**
     * Test connection to WooCommerce
     *
     * @param string $woocommerce_app_url
     * @param string $woocommerce_consumer_key
     * @param string $woocommerce_consumer_secret
     * @param int $location_id
     * @param int $enable_auto_sync
     */
    public function testConnection($business_id, $woocommerce_app_url, $woocommerce_consumer_key, $woocommerce_consumer_secret, $location_id, $enable_auto_sync)
    {
        $woocommerce_api_settings = $this->get_api_settings($business_id);

        if (empty($woocommerce_api_settings->woocommerce_app_url)) {
            throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
        }
        // woocommerce rest api test connection 
        $woocommerce = $this->woo_client($business_id);
        try {
            $response1 = $woocommerce->get('products');
            Log::info('WooCommerce products API response', ['response' => $response1]);
            $response1_success = true; // no exception = success
        } catch (\Exception $e) {
            Log::error('Error connecting to WooCommerce', ['error' => $e->getMessage()]);
            $response1 = null;
            $response1_success = false;
        }

        // also test connection of erp connector plugin
        $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
        $endpoint = $base_url . '/wp-json/phantasm-erp/v1/products';

        try {
            $response2 = Http::withHeaders([
                'Content-Type' => 'application/json',
                // 'Authorization' => 'Basic ' . base64_encode($woocommerce_consumer_key . ':' . $woocommerce_consumer_secret)
            ])->get($endpoint);
            $response2_success = true;
        } catch (\Exception $e) {
            $response2 = null;
            $response2_success = false;
        }

        if ($response1_success && $response2_success) {
            return [
                'status' => 2,
                'message' => 'Rest API and ERP connector plugin connection is successful'
            ];
        } elseif ($response1_success && !$response2_success) {
            return [
                'status' => 1,
                'message' => 'Rest API connection is successful but ERP connector plugin connection is failed'
            ];
        } elseif (!$response1_success && $response2_success) {
            return [
                'status' => 1,
                'message' => 'Rest API connection is failed but ERP connector plugin connection is successful'
            ];
        } else {
            return [
                'status' => 0,
                'message' => 'Rest API and ERP connector plugin connection is failed'
            ];
        }
    }

    // ----------------------------- SPECIFIC PRODUCT UPDATE PROCESSORS -----------------------------

    /**
     * Process stock-only updates from WooCommerce
     * 
     * @param int $business_id
     * @param array $data
     * @param object $woocommerce_api_settings
     * @return void
     */
    public function processWooCommerceProductStockUpdate($business_id, $data, $woocommerce_api_settings)
    {
        try {
            Log::info('Processing stock-only update from WooCommerce', [
                'business_id' => $business_id,
                'product_id' => $data['id'] ?? 'unknown',
                'update_type' => $data['update_type'] ?? 'unknown'
            ]);

            $woo_product_id = $data['id'] ?? null;
            if (!$woo_product_id) {
                Log::error('No WooCommerce product ID provided for stock update');
                return;
            }

            // Find existing product by WooCommerce ID
            $existing_product = Product::where('business_id', $business_id)
                ->where('woocommerce_product_id', $woo_product_id)
                ->first();

            if (!$existing_product) {
                Log::warning('Product not found in ERP for stock update', ['woo_product_id' => $woo_product_id]);
                return;
            }

            // Update stock quantities for all variations
            if (isset($data['variations']) && is_array($data['variations'])) {
                foreach ($data['variations'] as $variation_data) {
                    $this->updateVariationStockFromWooCommerceData(
                        $existing_product->variations->first(),
                        $variation_data,
                        $business_id
                    );
                }
            } else {
                // Single product stock update
                $stock_quantity = $data['stock_quantity'] ?? 0;
                $manage_stock = $data['manage_stock'] ?? false;
                $stock_status = $data['stock_status'] ?? 'instock';

                if ($existing_product->variations->count() > 0) {
                    // Update first variation stock
                    $variation = $existing_product->variations->first();
                    $this->updateVariationStockFromWooCommerceData(
                        $variation,
                        [
                            'stock_quantity' => $stock_quantity,
                            'manage_stock' => $manage_stock,
                            'stock_status' => $stock_status
                        ],
                        $business_id
                    );
                }
            }

            Log::info('Stock update completed successfully', [
                'business_id' => $business_id,
                'product_id' => $existing_product->id,
                'woo_product_id' => $woo_product_id
            ]);
        } catch (Exception $e) {
            Log::error('Error processing stock update from WooCommerce', [
                'business_id' => $business_id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    /**
     * Process price-only updates from WooCommerce
     * 
     * @param int $business_id
     * @param array $data
     * @param object $woocommerce_api_settings
     * @return void
     */
    public function processWooCommerceProductPriceUpdate($business_id, $data, $woocommerce_api_settings)
    {
        try {
            Log::info('Processing price-only update from WooCommerce', [
                'business_id' => $business_id,
                'product_id' => $data['id'] ?? 'unknown',
                'update_type' => $data['update_type'] ?? 'unknown',
                'price_type' => $data['price_type'] ?? 'regular'
            ]);

            $woo_product_id = $data['id'] ?? null;
            if (!$woo_product_id) {
                Log::error('No WooCommerce product ID provided for price update');
                return;
            }

            // Find existing product by WooCommerce ID
            $existing_product = Product::where('business_id', $business_id)
                ->where('woocommerce_product_id', $woo_product_id)
                ->first();

            if (!$existing_product) {
                Log::warning('Product not found in ERP for price update', ['woo_product_id' => $woo_product_id]);
                return;
            }

            // Update prices for all variations
            if (isset($data['variations']) && is_array($data['variations'])) {
                foreach ($data['variations'] as $variation_data) {
                    $this->updateVariationPricesFromWooCommerceData(
                        $existing_product->variations->first(),
                        $variation_data,
                        $business_id
                    );
                }
            } else {
                // Single product price update
                $regular_price = $data['regular_price'] ?? null;
                $sale_price = $data['sale_price'] ?? null;

                if ($existing_product->variations->count() > 0) {
                    // Update first variation prices
                    $variation = $existing_product->variations->first();
                    $this->updateVariationPricesFromWooCommerceData(
                        $variation,
                        [
                            'regular_price' => $regular_price,
                            'sale_price' => $sale_price
                        ],
                        $business_id
                    );
                }
            }

            Log::info('Price update completed successfully', [
                'business_id' => $business_id,
                'product_id' => $existing_product->id,
                'woo_product_id' => $woo_product_id
            ]);
        } catch (Exception $e) {
            Log::error('Error processing price update from WooCommerce', [
                'business_id' => $business_id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    /**
     * Process status-only updates from WooCommerce
     * 
     * @param int $business_id
     * @param array $data
     * @param object $woocommerce_api_settings
     * @return void
     */
    public function processWooCommerceProductStatusUpdate($business_id, $data, $woocommerce_api_settings)
    {
        try {
            Log::info('Processing status-only update from WooCommerce', [
                'business_id' => $business_id,
                'product_id' => $data['id'] ?? 'unknown',
                'update_type' => $data['update_type'] ?? 'unknown',
                'status' => $data['status'] ?? 'unknown'
            ]);

            $woo_product_id = $data['id'] ?? null;
            if (!$woo_product_id) {
                Log::error('No WooCommerce product ID provided for status update');
                return;
            }

            // Find existing product by WooCommerce ID
            $existing_product = Product::where('business_id', $business_id)
                ->where('woocommerce_product_id', $woo_product_id)
                ->first();

            if (!$existing_product) {
                Log::warning('Product not found in ERP for status update', ['woo_product_id' => $woo_product_id]);
                return;
            }

            // Update product status
            $status = $data['status'] ?? 'publish';
            $catalog_visibility = $data['catalog_visibility'] ?? 'visible';

            // Map WooCommerce status to ERP status
            $erp_status = $this->mapWooCommerceStatusToERPStatus($status, $catalog_visibility);

            $existing_product->status = $erp_status;
            $existing_product->save();

            Log::info('Status update completed successfully', [
                'business_id' => $business_id,
                'product_id' => $existing_product->id,
                'woo_product_id' => $woo_product_id,
                'woo_status' => $status,
                'erp_status' => $erp_status
            ]);
        } catch (Exception $e) {
            Log::error('Error processing status update from WooCommerce', [
                'business_id' => $business_id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    /**
     * Update variation prices from WooCommerce data
     * 
     * @param object $variation
     * @param array $woo_data
     * @param int $business_id
     * @return void
     */
    private function updateVariationPricesFromWooCommerceData($variation, $woo_data, $business_id)
    {
        $regular_price = $woo_data['regular_price'] ?? null;
        $sale_price = $woo_data['sale_price'] ?? null;

        if ($regular_price !== null) {
            $variation->default_sell_price = $this->formatDecimalPoint($regular_price, 'currency');
            $variation->sell_price_inc_tax = $this->formatDecimalPoint($regular_price, 'currency');
        }

        if ($sale_price !== null) {
            $variation->sell_price_inc_tax = $this->formatDecimalPoint($sale_price, 'currency');
        }

        $variation->save();

        Log::info('Variation prices updated', [
            'variation_id' => $variation->id,
            'regular_price' => $regular_price,
            'sale_price' => $sale_price,
            'final_price' => $variation->sell_price_inc_tax
        ]);
    }

    /**
     * Map WooCommerce status to ERP status
     * 
     * @param string $woo_status
     * @param string $catalog_visibility
     * @return string
     */
    private function mapWooCommerceStatusToERPStatus($woo_status, $catalog_visibility)
    {
        // Map WooCommerce status to ERP status
        switch ($woo_status) {
            case 'publish':
                return $catalog_visibility === 'hidden' ? 'inactive' : 'active';
            case 'draft':
                return 'inactive';
            case 'private':
                return 'inactive';
            default:
                return 'active';
        }
    }

    // ----------------------------- ERP TO WOOCOMMERCE SPECIFIC UPDATES -----------------------------
    /**
     * Process variation data update from WooCommerce to ERP
     * 
     * @param int $business_id
     * @param array $woo_product_data
     * @param string $update_type
     * @return array
     */
    public function processWooCommerceVariationDataUpdate($business_id, $woo_product_data, $update_type = 'variation_data_only')
    {
        try {
            Log::info('Processing variation data update from WooCommerce', [
                'business_id' => $business_id,
                'product_id' => $woo_product_data['id'] ?? 'unknown',
                'update_type' => $update_type
            ]);

            $woo_product_id = $woo_product_data['id'] ?? null;
            if (!$woo_product_id) {
                Log::error('No WooCommerce product ID provided for variation data update');
                return [
                    'success' => false,
                    'message' => 'No WooCommerce product ID provided'
                ];
            }

            // Find existing product by WooCommerce ID
            $existing_product = Product::where('business_id', $business_id)
                ->where('woocommerce_product_id', $woo_product_id)
                ->first();

            if (!$existing_product) {
                Log::warning('Product not found in ERP for variation data update', ['woo_product_id' => $woo_product_id]);
                return [
                    'success' => false,
                    'message' => 'Product not found in ERP'
                ];
            }

            // Process variations data
            if (isset($woo_product_data['variations']) && is_array($woo_product_data['variations'])) {
                foreach ($woo_product_data['variations'] as $variation_data) {
                    $this->updateVariationDataFromWooCommerce($existing_product, $variation_data, $business_id);
                }
            }

            Log::info('Variation data update completed successfully', [
                'business_id' => $business_id,
                'product_id' => $existing_product->id,
                'woo_product_id' => $woo_product_id
            ]);

            return [
                'success' => true,
                'message' => 'Variation data updated successfully'
            ];
        } catch (Exception $e) {
            Log::error('Error processing variation data update from WooCommerce', [
                'business_id' => $business_id,
                'error' => $e->getMessage(),
                'data' => $woo_product_data
            ]);

            return [
                'success' => false,
                'message' => 'Error processing variation data update: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update variation data from WooCommerce
     * 
     * @param object $product
     * @param array $variation_data
     * @param int $business_id
     * @return void
     */
    private function updateVariationDataFromWooCommerce($product, $variation_data, $business_id)
    {
        $woo_variation_id = $variation_data['id'] ?? null;

        if (!$woo_variation_id) {
            Log::warning('No WooCommerce variation ID provided');
            return;
        }

        // Find existing variation by WooCommerce ID
        $existing_variation = $product->variations()
            ->where('woocommerce_variation_id', $woo_variation_id)
            ->first();

        if (!$existing_variation) {
            Log::warning('Variation not found in ERP', [
                'woo_variation_id' => $woo_variation_id,
                'product_id' => $product->id
            ]);
            return;
        }

        // Update variation data
        if (isset($variation_data['regular_price'])) {
            $existing_variation->default_sell_price = $this->formatDecimalPoint($variation_data['regular_price'], 'currency');
            $existing_variation->sell_price_inc_tax = $this->formatDecimalPoint($variation_data['regular_price'], 'currency');
        }

        if (isset($variation_data['sale_price'])) {
            $existing_variation->sell_price_inc_tax = $this->formatDecimalPoint($variation_data['sale_price'], 'currency');
        }

        if (isset($variation_data['stock_quantity'])) {
            $stock_quantity = $variation_data['stock_quantity'];
            $this->updateVariationStockFromWooCommerceData(
                $existing_variation,
                [
                    'stock_quantity' => $stock_quantity,
                    'manage_stock' => $variation_data['manage_stock'] ?? true,
                    'stock_status' => $variation_data['stock_status'] ?? 'instock'
                ],
                $business_id
            );
        }

        $existing_variation->save();

        Log::info('Variation data updated', [
            'variation_id' => $existing_variation->id,
            'woo_variation_id' => $woo_variation_id,
            'product_id' => $product->id
        ]);
    }

    /**
     * Get categories from WordPress plugin with pagination support
     *
     * @param  int  $business_id
     * @param  array  $params
     * @return array
     */
    private function getCategoriesFromWordPressPlugin($business_id, $params)
    {
        $woocommerce_api_settings = $this->get_api_settings($business_id);

        if (empty($woocommerce_api_settings->woocommerce_app_url)) {
            throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
        }

        $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
        $endpoint = $base_url . '/wp-json/phantasm-erp/v1/sync-categories';

        // Build query string
        $query_string = http_build_query($params);
        $url = $endpoint . '?' . $query_string;

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
                ->timeout(300) // 5 minutes timeout for large datasets
                ->get($url);

            if (!$response->successful()) {
                Log::error('WordPress plugin API request failed for categories', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new WooCommerceError('Failed to fetch categories from WordPress plugin: ' . $response->status());
            }

            $data = $response->json();

            // Debug logging
            Log::info('WordPress plugin response for categories', [
                'url' => $url,
                'response_data' => $data
            ]);

            if (!isset($data['success']) || !$data['success']) {
                throw new WooCommerceError('WordPress plugin returned error: ' . ($data['message'] ?? 'Unknown error'));
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Error fetching categories from WordPress plugin', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Sync a single product from ERP to WooCommerce
     *
     * @param  int  $business_id
     * @param  Product  $product
     * @return array
     */
    public function syncProductToWooCommerce($business_id, Product $product)
    {
        if ($product->woocommerce_product_id) {
            return $this->updateProductInWooCommerce($business_id, $product);
        } else {
            return $this->createProductInWooCommerce($business_id, $product);
        }
    }

    /**
     * Create product in WooCommerce via Phantasm ERP plugin
     *
     * @param  int  $business_id
     * @param  Product  $product
     * @return array
     */
    public function createProductInWooCommerce($business_id, Product $product)
    {
        try {
            $woocommerce_api_settings = $this->get_api_settings($business_id);
            
            if (empty($woocommerce_api_settings->woocommerce_app_url)) {
                throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
            }

            if (empty($woocommerce_api_settings->woocommerce_consumer_key) || 
                empty($woocommerce_api_settings->woocommerce_consumer_secret)) {
                throw new WooCommerceError('WooCommerce API credentials not configured');
            }

            $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
            $endpoint = $base_url . '/wp-json/phantasm-erp/v1/add-item';

            // Format product data for WooCommerce
            $product_data = $this->formatProductForWooCommerce($product, $woocommerce_api_settings);
            $product_data['erp_product_id'] = $product->id;

            Log::info('Sending product to WooCommerce via Phantasm ERP plugin', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'endpoint' => $endpoint
            ]);

            $response = \Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->timeout(300)
                ->post($endpoint, $product_data);

            $responseData = $response->json();

            Log::info('WooCommerce product creation response', [
                'status' => $response->status(),
                'response' => $responseData
            ]);

            $woo_product_id = $responseData['product_id'] ?? $responseData['id'] ?? null;
            $is_success = ($responseData['ok'] ?? 0) == 1 || ($responseData['success'] ?? false);
            
            if ($response->successful() && $is_success && $woo_product_id) {
                $product->woocommerce_product_id = $woo_product_id;
                $product->woocommerce_disable_sync = false;
                $product->save();

                return [
                    'success' => true,
                    'woocommerce_product_id' => $woo_product_id,
                    'message' => $responseData['message'] ?? 'Product created successfully in WooCommerce'
                ];
            } else {
                $error_message = $responseData['message'] ?? $responseData['error'] ?? 'Failed to create product in WooCommerce';
                return [
                    'success' => false,
                    'error' => $error_message,
                    'message' => $error_message
                ];
            }
        } catch (\Exception $e) {
            Log::error('Failed to create product in WooCommerce', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update product in WooCommerce via Phantasm ERP plugin
     *
     * @param  int  $business_id
     * @param  Product  $product
     * @return array
     */
    public function updateProductInWooCommerce($business_id, Product $product)
    {
        try {
            if (empty($product->woocommerce_product_id)) {
                return $this->createProductInWooCommerce($business_id, $product);
            }

            $woocommerce_api_settings = $this->get_api_settings($business_id);
            
            if (empty($woocommerce_api_settings->woocommerce_app_url)) {
                throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
            }

            $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
            $endpoint = $base_url . '/wp-json/phantasm-erp/v1/update-product';

            // Format product data for WooCommerce
            $product_data = $this->formatProductForWooCommerce($product, $woocommerce_api_settings);
            $product_data['woocommerce_product_id'] = $product->woocommerce_product_id;
            $product_data['erp_product_id'] = $product->id;

            Log::info('Updating product in WooCommerce via Phantasm ERP plugin', [
                'product_id' => $product->id,
                'woocommerce_product_id' => $product->woocommerce_product_id
            ]);

            $response = \Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->timeout(300)
                ->post($endpoint, $product_data);

            $responseData = $response->json();

            Log::info('WooCommerce product update response', [
                'status' => $response->status(),
                'response' => $responseData
            ]);

            $woo_product_id = $responseData['product_id'] ?? $responseData['id'] ?? $product->woocommerce_product_id;
            $is_success = ($responseData['ok'] ?? 0) == 1 || ($responseData['success'] ?? false);
            
            if ($response->successful() && $is_success && $woo_product_id) {
                return [
                    'success' => true,
                    'woocommerce_product_id' => $woo_product_id,
                    'message' => $responseData['message'] ?? 'Product updated successfully in WooCommerce'
                ];
            } else {
                $error_message = $responseData['message'] ?? $responseData['error'] ?? 'Failed to update product in WooCommerce';
                return [
                    'success' => false,
                    'error' => $error_message,
                    'message' => $error_message
                ];
            }
        } catch (\Exception $e) {
            Log::error('Failed to update product in WooCommerce', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format ERP product for WooCommerce API
     *
     * @param  Product  $product
     * @param  object  $woocommerce_api_settings
     * @return array
     */
    protected function formatProductForWooCommerce(Product $product, $woocommerce_api_settings)
    {
        // Get variations with stock
        $variationsWithStock = \DB::table('variations')
            ->join('product_variations', 'variations.product_variation_id', '=', 'product_variations.id')
            ->leftJoin('variation_location_details', 'variation_location_details.variation_id', '=', 'variations.id')
            ->where('variations.product_id', $product->id)
            ->select(
                'variations.*',
                'product_variations.name as attribute_name',
                'product_variations.is_dummy',
                \DB::raw('COALESCE(SUM(variation_location_details.qty_available), 0) as total_stock')
            )
            ->groupBy('variations.id')
            ->get();

        $totalStock = $variationsWithStock->sum('total_stock');
        $firstVariation = $variationsWithStock->first();
        
        $hasRealVariations = $variationsWithStock->where('is_dummy', 0)->count() > 0;
        $isVariable = $product->type === 'variable' && $hasRealVariations;
        
        // Base product data
        $data = [
            'name' => $product->name,
            'sku' => $product->sku ?? 'ERP-' . $product->id,
            'description' => $product->product_description ?? '',
            'short_description' => substr(strip_tags($product->product_description ?? ''), 0, 200),
            'type' => $isVariable ? 'variable' : 'simple',
            'status' => $product->is_inactive ? 'draft' : 'publish',
        ];
        
        if (!$isVariable) {
            $data['regular_price'] = $firstVariation ? (string) ($firstVariation->sell_price_inc_tax ?? 0) : '0';
            $data['manage_stock'] = $product->enable_stock ? true : false;
            $data['stock_quantity'] = $product->enable_stock ? (int) $totalStock : null;
            $data['stock_status'] = $totalStock > 0 ? 'instock' : 'outofstock';
        } else {
            $data['manage_stock'] = false;
            $data['stock_status'] = $totalStock > 0 ? 'instock' : 'outofstock';
        }

        // Add category if exists and has WooCommerce ID
        if ($product->category_id && $product->category && $product->category->woocommerce_cat_id) {
            $data['categories'] = [
                ['id' => (int) $product->category->woocommerce_cat_id]
            ];
        }

        // Add image if exists
        if (!empty($product->image)) {
            $imagePath = $product->image;
            if (!filter_var($imagePath, FILTER_VALIDATE_URL)) {
                $data['images'] = [['src' => asset('uploads/img/' . $imagePath)]];
            } else {
                $data['images'] = [['src' => $imagePath]];
            }
        }

        // Add meta data
        $data['meta_data'] = [
            ['key' => '_erp_product_id', 'value' => $product->id],
        ];

        // Add vendor information for dropshipping
        $vendor = $product->vendors()->first();
        if ($vendor) {
            $pivotData = $product->vendors()->where('wp_vendors.id', $vendor->id)->first()?->pivot;
            
            $data['meta_data'][] = ['key' => '_dropship_vendor_id', 'value' => $vendor->id];
            $data['meta_data'][] = ['key' => '_dropship_vendor_name', 'value' => $vendor->name];
            $data['meta_data'][] = ['key' => '_is_dropshipped', 'value' => 'yes'];
            
            if ($pivotData && $pivotData->vendor_cost_price) {
                $data['meta_data'][] = ['key' => '_vendor_cost_price', 'value' => $pivotData->vendor_cost_price];
            }
            
            if ($pivotData && $pivotData->dropship_selling_price && !$isVariable) {
                $data['regular_price'] = (string) $pivotData->dropship_selling_price;
            }
        } else {
            $data['meta_data'][] = ['key' => '_is_dropshipped', 'value' => $product->isDropshipped() ? 'yes' : 'no'];
        }

        return $data;
    }

    /**
     * Bulk sync products to WooCommerce
     *
     * @param  int  $business_id
     * @param  array  $product_ids
     * @return array
     */
    public function bulkSyncProductsToWooCommerce($business_id, array $product_ids)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'synced_products' => []
        ];

        foreach ($product_ids as $product_id) {
            $product = Product::with(['variations', 'category', 'brand', 'vendors'])->find($product_id);
            
            if (!$product) {
                $results['failed']++;
                $results['errors'][] = "Product ID {$product_id} not found";
                continue;
            }

            $result = $this->syncProductToWooCommerce($business_id, $product);
            
            if ($result['success']) {
                $results['success']++;
                $results['synced_products'][] = [
                    'product_id' => $product_id,
                    'woocommerce_id' => $result['woocommerce_product_id']
                ];
            } else {
                $results['failed']++;
                $results['errors'][] = "Product {$product->name}: " . ($result['message'] ?? $result['error'] ?? 'Unknown error');
            }
        }

        return $results;
    }

    /**
     * Sync product stock to WooCommerce
     *
     * @param  int  $business_id
     * @param  Product  $product
     * @return array
     */
    public function syncProductStockToWooCommerce($business_id, Product $product)
    {
        if (empty($product->woocommerce_product_id)) {
            return ['success' => false, 'error' => 'Product not synced to WooCommerce'];
        }

        try {
            $woocommerce_api_settings = $this->get_api_settings($business_id);
            
            if (empty($woocommerce_api_settings->woocommerce_app_url)) {
                throw new WooCommerceError(__('woocommerce::lang.unable_to_connect'));
            }

            $base_url = rtrim($woocommerce_api_settings->woocommerce_app_url, '/');
            $endpoint = $base_url . '/wp-json/phantasm-erp/v1/update-product-stock';

            $variation = $product->variations->first();
            
            $stock_data = [
                'woocommerce_product_id' => $product->woocommerce_product_id,
                'erp_product_id' => $product->id,
                'manage_stock' => $product->enable_stock,
                'stock_quantity' => $variation ? (int) ($variation->qty_available ?? 0) : 0,
                'stock_status' => ($variation && $variation->qty_available > 0) ? 'instock' : 'outofstock'
            ];

            $response = \Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->timeout(60)
                ->post($endpoint, $stock_data);

            $responseData = $response->json();

            if (isset($responseData['success']) && $responseData['success']) {
                return [
                    'success' => true,
                    'message' => 'Stock synced successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $responseData['message'] ?? 'Failed to sync stock'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync product stock to WooCommerce', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
