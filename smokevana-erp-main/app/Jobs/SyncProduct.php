<?php

namespace App\Jobs;

use App\Brands;
use App\Category;
use App\Media;
use App\Product;
use App\ProductVariation;
use App\SyncRecord;
use App\Variation;
use App\VariationGroupPrice;
use App\VariationLocationDetails;
use App\VariationTemplate;
use App\VariationValueTemplate;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SyncProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $productSlug;
    protected $nextProductSlug;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($productSlug, $nextProductSlug = null)
    {
        $this->productSlug = $productSlug;
    }
    private function downloadAndStoreLogo(string $url, $brandLogo = false, $pid = null, $v = false): string
    {
        Log::info("Downloading $url for $brandLogo erp $pid with $v");
        if ($brandLogo) {
            $apiUrl = 'https://ad2.phantasm.solutions/api/thumbnail/' . $url;
            $response = Http::get($apiUrl);
            $url = $response->body();
            // Log::info('image url brand ' . $url);
        }
        $year = date('Y');
        $month = date('m');
        $date = date('d');
        $pathInfo = pathinfo($url);
        $fileExtension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '.jpg';
        $filename = $year . '-' . $month . '-' . $date . '-' . basename($url, $fileExtension) . $fileExtension;
        $mediaURL = $year . '_' . $month . '_' . $date . '_' . basename($url, $fileExtension) . $fileExtension;
        $storagePath = public_path('uploads/img');
        $fullPath = $storagePath . '/' . $filename;

        // If the file already exists, return the filename
        if ($pid != null) {
            Media::updateOrCreate(['model_id' => $pid], [
                'business_id' => 1,
                'file_name' => $mediaURL,
                'uploaded_by' => 1,
                'model_type' => $v == true ? "App\Variation" : "App\Product"
            ]);
        }
        if (file_exists($fullPath)) {
            return $filename;
        }
            $fileContents = file_get_contents($url);
        
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
        file_put_contents($fullPath, $fileContents);

        return $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            try {
                Log::info('Executing: '.$this->productSlug);
                $apiUrl = 'https://ad2.phantasm.solutions/api/sync-product/'.$this->productSlug;
                // $apiUrl = 'http://127.0.0.1:8080/api/sync-product/modus-snow-balls-thc-a-thc-p-thc-a-diamonds-1-ounce-flower-bag';
                // $apiUrl = 'http://127.0.0.1:8080/api/sync-product/'.$this->productSlug;
                // Log::info($apiUrl);
                $response = Http::get($apiUrl);
                $data = $response->json();
                $nextProduct = $data['nextProduct'];
                $data = $data['product'];
                DB::beginTransaction();
                if ($data['type'] == "product") {
                    $product = Product::updateOrCreate(
                        ['slug' => $data['slug']],
                        [
                            "name" => $data['name'],
                            "business_id" => 1,
                            "type" => empty($data['variations']) ? "single" : "variable",
                            "unit_id" => 1,
                            "secondary_unit_id" => null,
                            "sub_unit_ids" => null,
                            "brand_id" => null,
                            "category_id" => null,
                            "sub_category_id" => null,
                            "tax" => null,
                            "tax_type" => "exclusive",
                            "enable_stock" => "1",
                            "alert_quantity" => null,
                            "sku" =>  $data['sku'],
                            "barcode_type" => "C128",
                            "expiry_period" => null,
                            "expiry_period_type" => null,
                            "enable_sr_no" => "0",
                            "weight" => null,
                            "image" => null,
                            "product_description" => $data['description'],
                            "created_by" => 1,
                            "preparation_time_in_minutes" => null,
                            "warranty_id" => null,
                            "is_inactive" => "0",
                            "not_for_selling" => "0",
                            "created_at" => Carbon::now(),
                            "updated_at" => Carbon::now(),
                            "ml" => $data['ml'],
                            "ct" => $data['ct'],
                            "productVisibility" => "public",
                            "locationTaxType" => "[null]",
                            "maxSaleLimit" => $data['max_quantity'] > 0 ? $data['max_quantity'] : null,
                            // "barcode_no" => $data['mm_product_upc'],
                            "enable_selling" => "1",
                            "custom_sub_categories" => "[]",
                            "top_selling" => $data['total_sales'] ?? 0,
                        ]
                    );
                    $pid = $product->id;
    
                    DB::table('products')->where('id', $product->id)->update([
                        'image' => $this->downloadAndStoreLogo($data['thumbnail_url'], false, $pid, false)
                    ]);
                    
                    Log::info('Product qyery done');
                    $isSetLocation = DB::table('product_locations')
                        ->where('product_id', $pid)
                        ->where('location_id', 1)
                        ->exists();
    
                    if (!$isSetLocation) {
                        DB::table('product_locations')->insertOrIgnore([
                            'product_id' => $pid,
                            'location_id' => 1,
                        ]);
                    }
    
    
                    $productVariation = ProductVariation::updateOrCreate(['product_id' => $pid], [
                        'product_id' => $pid,
                        'variation_template_id' => isset($data['variations']) && !empty($data['variations']) ? 2 : null,
                        'name' => isset($data['variations']) && !empty($data['variations']) ? "Flavor" : "DUMMY",
                        'is_dummy' => isset($data['variations']) && !empty($data['variations']) ? 0 : 1,
                    ]);
                    if (!empty($data['brands'])) {
                        foreach ($data['brands'] as $brand) {
                            $brand = Brands::updateOrCreate(
                                [
                                    'name' => $brand["name"],
                                    'slug' => $brand["slug"],
                                ],
                                [
                                    'business_id' => 1,
                                    'created_by' => 1,
                                    'deleted_at' => null,
                                    'slug' => $brand["slug"],
                                    'visibility' => "public",
                                    ]
                                );
                                Log::info('Brand qyery done');
                        }
                    }
    
                    if (!empty($data['categories'])) {
                        $count = 0;
                        $custom_sub_categories = [];
                        foreach ($data['categories'] as $category) {
                            $category = Category::firstOrCreate(
                                ['slug' => $category["slug"]],
                                [
                                    'business_id' => 1,
                                    'created_by' => 1,
                                    'deleted_at' => null,
                                    'short_code' => null,
                                    'parent_id' => 0, //need to fix this logic 
                                    'name' => $category["name"],
                                    'visibility' => $category["visibility"],
                                    'category_type' => 'product',
                                    'logo' => null
                                ]
                            );
                            $custom_sub_categories[] = $category->id;
                            $product->webcategories()->attach($category->id);
                            if ($count == 0) {
                                DB::table('products')->where('id', $product->id)->update([
                                    'category_id' => $category->id,
                                ]);
                            } else if ($count == 1) {
                                DB::table('products')->where('id', $product->id)->update([
                                    'sub_category_id' => $category->id,
                                ]);
                            }
                            $count++;
                        }
                        $product = DB::table('products')->where('id', $product->id)->update([
                            'custom_sub_categories' => $custom_sub_categories,
                        ]);
                    }
                    //varient 
                    if (!empty($data['variations'])) {
                        $sliverPrice = null;
                        $goldPrice = null;
                        $platinumPrice = null;
                        $diamondPrice =  null;
                        $lowestPrice = null;
                        $stockStatus = null;
                        $qty = null;
                        $manage_stock = false;
                        $instock = false;
                        $max_quantity_var = null;
                        $mlVal = null;
                        $ctVal = null;
                        $mm_indirect_tax_type = null;
                        $thumbnailID= null;
                        $storeThumb = false;
                        $vid=null;
                        foreach ($data['variations'] as $variation) {
                            foreach ($variation['meta'] as $meta) {
                                //price 
                                if ($meta['meta_key'] == 'wholesale_customer_wholesale_price') { //silver
                                    $sliverPrice = $meta['meta_value'];
                                }
                                if ($meta['meta_key'] == 'mm_price_2_wholesale_price') { //gold
                                    $goldPrice = $meta['meta_value'];
                                }
                                if ($meta['meta_key'] == 'mm_price_3_wholesale_price') { //platinum
                                    $platinumPrice = $meta['meta_value'];
                                }
                                if ($meta['meta_key'] == 'mm_price_4_wholesale_price') { //diamond
                                    $diamondPrice = $meta['meta_value'];
                                }
                                if ($meta['meta_key'] == '_regular_price') {
                                    $var_default_price = $meta['meta_value'];
                                }
                                if ($meta['meta_key'] == 'mm_product_cost') { //cost
                                    $costPrice = $meta['meta_value'];
                                }
                                if ($meta['meta_key'] == 'mm_product_lowest_price') { //cost
                                    $lowestPrice = $meta['meta_value'];
                                }
    
                                //stock
                                if ($meta['meta_key'] == '_stock') { //qty
                                    $qty = $meta['meta_value'];
                                }
                                if ($meta['meta_key'] == '_stock_status') { //instock
                                    $instock = true;
                                }
                                if ($meta['meta_key'] == '_manage_stock') { //manage stock
                                    $manage_stock = true;
                                }
                                //ml and ct
                                if ($meta['meta_key'] == 'mm_product_basis_1') {
                                    $mlVal = $meta['meta_value'];
                                }
                                if ($meta['meta_key'] == 'mm_product_basis_2') {
                                    $ctVal = $meta['meta_value'];
                                }
                                if ($meta['meta_key'] == 'mm_indirect_tax_type') {
                                    $mm_indirect_tax_type = $meta['meta_value'];
                                }
                                //meta
                                if ($meta['meta_key'] == '_sku') {
                                    $var_sku = $meta['meta_value'];
                                }
                                if ($meta['meta_key'] == 'mm_product_upc') { //barcode
                                    $var_barcode_no = $meta['meta_value'];
                                }
                                if ($meta['meta_key'] == 'max_quantity_var') {
                                    $max_quantity_var = $meta['meta_value'];
                                }

                                // thubmnail
                                if ($meta['meta_key'] == '_thumbnail_id'){
                                    $thumbnailID = $meta['meta_value'];
                                    if($thumbnailID !='0' || $thumbnailID !=''){
                                        $storeThumb= true;
                                        
                                    }
                                }
                                if (strpos($meta['meta_key'], 'attribute_') === 0) {
                                    $attributeName = substr($meta['meta_key'], strlen('attribute_')); // Will give 'size' from 'attribute_size'
                                    $variationName = $meta['meta_value'];
                                    $variationTemplate = VariationTemplate::updateOrCreate(
                                        ['name' => ucfirst($attributeName)],
                                        ['business_id' => 1]
                                    );
                                    $variationTemplateValue = VariationValueTemplate::updateOrCreate(
                                        ['name' => $variationName],
                                        ['variation_template_id' => $variationTemplate->id]
                                    );
                                }
                            }
    
                            $erpVariation = Variation::updateOrCreate( // peach ice + 10mg 
                                ['sub_sku' => $var_sku],
                                [
                                    "name" => $variationName,
                                    "product_id" => $pid,
                                    "product_variation_id" => $productVariation->id, // product_variation table 
                                    "variation_value_id" => $variationTemplateValue->id,  // 
                                    "default_purchase_price" => $costPrice,
                                    "dpp_inc_tax" =>  $costPrice,
                                    "profit_percent" => "0.0000",
                                    "default_sell_price" =>  $lowestPrice,
                                    "sell_price_inc_tax" => $lowestPrice,
                                    "var_barcode_no" => $var_barcode_no,
                                    "var_maxSaleLimit" => $max_quantity_var
                                ]
                            );
                            
                            if($thumbnailID !="0"){
                                try {
                                    //code...
                                    $this->downloadAndStoreLogo($thumbnailID, true, $erpVariation->id,true);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                }
                            }
                            //pricing 
    
                            $priceGroupData = [];
                            if ($sliverPrice > 0) {
    
                                $priceGroupData[] = [
                                    "price_group_id" => 1, //silver
                                    "price_inc_tax" => $sliverPrice,
                                    "price_type" => "fixed"
                                ];
                            }
                            if ($goldPrice > 0) {
                                $priceGroupData[] = [
                                    "price_group_id" => 2, //gold
                                    "price_inc_tax" => $goldPrice,
                                    "price_type" => "fixed"
                                ];
                            }
                            if ($platinumPrice > 0) {
                                $priceGroupData[] = [
                                    "price_group_id" => 3, //platinum
                                    "price_inc_tax" => $platinumPrice,
                                    "price_type" => "fixed"
                                ];
                            }
                            if ($lowestPrice > 0) {
                                $priceGroupData[] = [
                                    "price_group_id" => 4, //lowest price
                                    "price_inc_tax" => $lowestPrice,
                                    "price_type" => "fixed"
                                ];
                            }
                            if ($diamondPrice > 0) {
                                $priceGroupData[] = [
                                    "price_group_id" => 5, //diamond
                                    "price_inc_tax" => $diamondPrice,
                                    "price_type" => "fixed"
                                ];
                            }
    
                            foreach ($priceGroupData as $priceData) {
                                VariationGroupPrice::updateOrCreate(
                                    ['variation_id' => $erpVariation->id, 'price_group_id' => $priceData['price_group_id']],
                                    $priceData
                                );
                            }
    
                            //stock 
                            if ($instock == 'instock' && $manage_stock && $qty) {
                                VariationLocationDetails::updateOrCreate(
                                    ['variation_id' => $erpVariation->id],
                                    [
                                        'product_id' => $pid,
                                        'product_variation_id' => '',
                                        'variation_id' => $erpVariation->id,
                                        'location_id' => 1,
                                        'qty_available' => $qty,
                                    ]
                                );
                            }
                        }
                        //ml and ct
                        $isSetcharges = DB::table('products')
                            ->where('id', $pid)
                            ->where(function ($query) {
                                $query->whereNotNull('ml')
                                    ->orWhereNotNull('ct');
                            })
                            ->exists();
                        if (!$isSetcharges) {
                            $locationTaxType = [];
                            if ($mm_indirect_tax_type == "14346") {
                                $locationTaxType = [1];
                            }
                            if ($mm_indirect_tax_type == "14347") {
                                $locationTaxType = [6];
                            }
                            if ($mm_indirect_tax_type == "14344") {
                                $locationTaxType = [2];
                            }
                            if ($mm_indirect_tax_type == "14343") {
                                $locationTaxType = [5];
                            }
                            if ($mm_indirect_tax_type == "14345") {
                                $locationTaxType = [3];
                            }
                            $locationTaxTypeString = '[' . implode(',', array_map(function($item) {
                                return '"' . $item . '"'; 
                            }, $locationTaxType)) . ']';
                            DB::table('products')->insertOrIgnore([
                                'id' => $pid,
                                'ml' =>  $mlVal,
                                'ct' => $ctVal,
                                'locationTaxType' =>$locationTaxTypeString
                            ]);
                        }
                    }
    
                    if (empty($data['variations'])) {
                        $sliverPrice = null;
                        $goldPrice = null;
                        $platinumPrice = null;
                        $diamondPrice =  null;
                        $lowestPrice = null;
                        $stockStatus = null;
                        $qty = null;
                        $manage_stock = false;
                        $instock = false;
                        $max_quantity_var = null;
                        $mlVal = null;
                        $ctVal = null;
                        $mm_indirect_tax_type = null;
                        foreach ($data['meta'] as $meta) {
                            //price 
    
                            if ($meta['key'] == 'wholesale_customer_wholesale_price') { //silver
                                $sliverPrice = $meta['value'];
                            }
                            if ($meta['key'] == 'mm_price_2_wholesale_price') { //gold
                                $goldPrice = $meta['value'];
                            }
                            if ($meta['key'] == 'mm_price_3_wholesale_price') { //platinum
                                $platinumPrice = $meta['value'];
                            }
                            if ($meta['key'] == 'mm_price_4_wholesale_price') { //diamond
                                $diamondPrice = $meta['value'];
                            }
                            if ($meta['key'] == '_regular_price') { //regular price ?
                                $var_default_price = $meta['value'];
                            }
                            if ($meta['key'] == 'mm_product_cost') { //cost ?
                                $costPrice = $meta['value'];
                            }
                            if ($meta['key'] == 'mm_product_lowest_price') { //cost
                                $lowestPrice = $meta['value'];
                            }
    
                            //stock
                            if ($meta['key'] == '_stock') { //qty
                                $qty = $meta['value'];
                            }
                            if ($meta['key'] == '_stock_status') { //instock
                                $instock = true;
                            }
                            if ($meta['key'] == '_manage_stock') { //manage stock
                                $manage_stock = true;
                            }
    
                            //ml and ct
                            if ($meta['key'] == 'mm_product_basis_1') {
                                $mlVal = $meta['value'];
                            }
                            if ($meta['key'] == 'mm_product_basis_2') {
                                $ctVal = $meta['value'];
                            }
                            if ($meta['key'] == 'mm_indirect_tax_type') {
                                $mm_indirect_tax_type = $meta['value'];
                            }
    
                            //meta
                            if ($meta['key'] == '_sku') {
                                $var_sku = $meta['value'];
                            }
                            if ($meta['key'] == 'mm_product_upc') { //barcode
                                $var_barcode_no = $meta['value'];
                            }
                            if ($meta['key'] == 'max_quantity_var') {
                                $max_quantity_var = $meta['value'];
                            }
                            // if (strpos($meta['key'], 'attribute_') === 0) {
                            //     $attributeName = substr($meta['key'], strlen('attribute_')); // Will give 'size' from 'attribute_size'
                            //     $variationName = $meta['value'];
                            //     $variationTemplate = VariationTemplate::updateOrCreate(
                            //         ['name' => ucfirst($attributeName)],
                            //         ['business_id' => 1]
                            //     );
                            //     $variationTemplateValue = VariationValueTemplate::updateOrCreate(
                            //         ['name' => $variationName],
                            //         ['variation_template_id' => $variationTemplate->id]
                            //     );
                            // }
                        }
                        $erpVariation = Variation::updateOrCreate(
                            ['sub_sku' => $var_sku],
                            [
                                "name" => "DUMMY",
                                "product_id" => $pid,
                                "product_variation_id" => $productVariation->id,
                                "variation_value_id" => null,
                                "default_purchase_price" => $costPrice,
                                "dpp_inc_tax" =>  $costPrice,
                                "profit_percent" => "0.0000",
                                "default_sell_price" =>  $lowestPrice,
                                "sell_price_inc_tax" => $lowestPrice,
                                "var_barcode_no" => $var_barcode_no,
                                "var_maxSaleLimit" => $max_quantity_var
                            ]
                        );
                        //pricing 
                        $priceGroupData = [];
                        if ($sliverPrice > 0) {
    
                            $priceGroupData[] = [
                                "price_group_id" => 1, //silver
                                "price_inc_tax" => $sliverPrice,
                                "price_type" => "fixed",
                                'variation_id' => $erpVariation->id,
                            ];
                        }
                        if ($goldPrice > 0) {
                            $priceGroupData[] = [
                                "price_group_id" => 2, //gold
                                "price_inc_tax" => $goldPrice,
                                "price_type" => "fixed",
                                'variation_id' => $erpVariation->id,
                            ];
                        }
                        if ($platinumPrice > 0) {
                            $priceGroupData[] = [
                                "price_group_id" => 3, //platinum
                                "price_inc_tax" => $platinumPrice,
                                "price_type" => "fixed",
                                'variation_id' => $erpVariation->id,
                            ];
                        }
                        if ($lowestPrice > 0) {
                            $priceGroupData[] = [
                                "price_group_id" => 4, //lowest price
                                "price_inc_tax" => $lowestPrice,
                                "price_type" => "fixed",
                                'variation_id' => $erpVariation->id,
                            ];
                        }
                        if ($diamondPrice > 0) {
                            $priceGroupData[] = [
                                "price_group_id" => 5, //diamond
                                "price_inc_tax" => $diamondPrice,
                                "price_type" => "fixed",
                                'variation_id' => $erpVariation->id,
                            ];
                        }
                        foreach ($priceGroupData as $priceData) {
                            VariationGroupPrice::updateOrCreate(
                                ['variation_id' => $erpVariation->id, 'price_group_id' => $priceData['price_group_id']],
                                $priceData
                            );
                        }
                        //stock 
                        if ($instock == 'instock' && $manage_stock && $qty) {
                            VariationLocationDetails::updateOrCreate(
                                ['variation_id' => $erpVariation->id],
                                [
                                    'product_id' => $pid,
                                    'product_variation_id' => '',
                                    'variation_id' => $erpVariation->id,
                                    'location_id' => 1,
                                    'qty_available' => $qty,
                                ]
                            );
                        }
                        //ml and ct
                        $isSetcharges = DB::table('products')
                            ->where('id', $pid)
                            ->where(function ($query) {
                                $query->whereNotNull('ml')
                                    ->orWhereNotNull('ct');
                            })
                            ->exists();
    
    
                        if (!$isSetcharges) {
                            $locationTaxType = [];
                            if ($mm_indirect_tax_type == "14346") {
                                $locationTaxType = [1];
                            }
                            if ($mm_indirect_tax_type == "14347") {
                                $locationTaxType = [6];
                            }
                            if ($mm_indirect_tax_type == "14344") {
                                $locationTaxType = [2];
                            }
                            if ($mm_indirect_tax_type == "14343") {
                                $locationTaxType = [5];
                            }
                            if ($mm_indirect_tax_type == "14345") {
                                $locationTaxType = [3];
                            }
                            $locationTaxTypeString = '[' . implode(',', array_map(function($item) {
                                return '' . $item . ''; //
                            }, $locationTaxType)) . ']';
                            
                            DB::table('products')->insertOrIgnore([
                                'id' => $pid,
                                'ml' =>  $mlVal,
                                'ct' => $ctVal,
                                "locationTaxType" => $locationTaxTypeString
                            ]);
                        }
                    }
                }
                // Log::info('Commited: '.$this->productSlug);
                DB::commit();
                
                if (isset($nextProduct)) {
                    $next = (int) $nextProduct;
                    Log::info("Dispatched: ".$next);
                    SyncProduct::dispatch($next)->delay(now()); 
                }
                // SyncRecord::updateOrCreate(['synced_id'=>$this->productSlug],[
                //     'next_id'=>$next
                // ]);
            } catch (\Throwable $th) {
                Log::info('Rolling back: '.$this->productSlug);
                DB::rollback();
                Log::error($th->getMessage() . ' ' . $th->getLine() . ' ' . $th->getFile());
                return response()->json();
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage() . ' ' . $th->getLine() . ' ' . $th->getFile());
        }
    }
    
}
