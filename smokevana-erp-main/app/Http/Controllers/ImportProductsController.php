<?php

namespace App\Http\Controllers;

use App\Brands;
use App\BusinessLocation;
use App\Category;
use App\Exports\ProductImageMappingExport;
use App\Media;
use App\Product;
use App\TaxRate;
use App\Unit;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Variation;
use App\VariationValueTemplate;
use App\Warranty;
use DB;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImportProductsController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $moduleUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
    }

    /**
     * Display import product screen.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded = extension_loaded('zip') ? true : false;

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = ['success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import',
            ];

            return view('import_products.index')
                ->with('notification', $output);
        } else {
            return view('import_products.index');
        }
    }

    /**
     * Imports the uploaded file to database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $notAllowed = $this->productUtil->notAllowedInDemo();
            if (! empty($notAllowed)) {
                return $notAllowed;
            }

            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            if ($request->hasFile('products_csv')) {
                $file = $request->file('products_csv');

                $parsed_array = Excel::toArray([], $file);

                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');
                $default_profit_percent = $request->session()->get('business.default_profit_percent');

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                $total_rows = count($imported_data);

                //Check if subscribed or not, then check for products quota
                if (! $this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse();
                } elseif (! $this->moduleUtil->isQuotaAvailable('products', $business_id, $total_rows)) {
                    return $this->moduleUtil->quotaExpiredResponse('products', $business_id, action([\App\Http\Controllers\ImportProductsController::class, 'index']));
                }

                $business_locations = BusinessLocation::where('business_id', $business_id)->get();
                
                // Get user's permitted locations to auto-assign product location
                $user = auth()->user();
                $permitted_locations = $user->permitted_locations($business_id);
                $auto_assign_location_id = null;
                
                // If user has exactly one permitted location, auto-assign it
                if (is_array($permitted_locations) && count($permitted_locations) === 1) {
                    $auto_assign_location_id = $permitted_locations[0];
                }
                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {

                    //Check if any column is missing (minimum 38 columns)
                    if (count($value) < 38) {
                        $is_valid = false;
                        $error_msg = 'Some of the columns are missing. Minimum 38 columns required. Please, use latest CSV file template.';
                        break;
                    }

                    $row_no = $key + 1;
                    $product_array = [];
                    $product_array['business_id'] = $business_id;
                    $product_array['created_by'] = $user_id;

                    //Column 1: Product Name (REQUIRED)
                    $product_name = trim($value[0]);
                    if (! empty($product_name)) {
                        $product_array['name'] = $product_name;
                    } else {
                        $is_valid = false;
                        $error_msg = "Product name is required in row no. $row_no";
                        break;
                    }

                    //Column 2: Brand (optional)
                    $brand_name = trim($value[1]);
                    if (! empty($brand_name)) {
                        $brand = Brands::firstOrCreate(
                            ['business_id' => $business_id, 'name' => $brand_name],
                            ['created_by' => $user_id, 'location_id' => $auto_assign_location_id]
                        );
                        if (!empty($auto_assign_location_id) && empty($brand->location_id)) {
                            $brand->location_id = $auto_assign_location_id;
                            $brand->save();
                        }
                        $product_array['brand_id'] = $brand->id;
                    }

                    //Column 3: Unit (REQUIRED)
                    $unit_name = trim($value[2]);
                    if (! empty($unit_name)) {
                        $unit = Unit::where('business_id', $business_id)
                                    ->where(function ($query) use ($unit_name) {
                                        $query->where('short_name', $unit_name)
                                              ->orWhere('actual_name', $unit_name);
                                    })->first();
                        if (! empty($unit)) {
                            $product_array['unit_id'] = $unit->id;
                        } else {
                            $is_valid = false;
                            $error_msg = "Unit with name $unit_name not found in row no. $row_no. You can add unit from Products > Units";
                            break;
                        }
                    } else {
                        $is_valid = false;
                        $error_msg = "UNIT is required in row no. $row_no";
                        break;
                    }

                    //Column 4: Category (optional)
                    $category_name = trim($value[3]);
                    if (! empty($category_name)) {
                        // Find or create category (search without location_id to find existing)
                        $category = Category::where('business_id', $business_id)
                            ->where('name', $category_name)
                            ->where('category_type', 'product')
                            ->where('parent_id', 0)
                            ->first();
                        
                        if (!$category) {
                            // Create new category with location_id if available
                            $category = Category::create([
                                'business_id' => $business_id,
                                'name' => $category_name,
                                'category_type' => 'product',
                                'parent_id' => 0,
                                'created_by' => $user_id,
                                'location_id' => $auto_assign_location_id
                            ]);
                        } elseif (!empty($auto_assign_location_id) && empty($category->location_id)) {
                            // Update existing category with location_id if it doesn't have one
                            $category->location_id = $auto_assign_location_id;
                            $category->save();
                        }
                        
                        $product_array['category_id'] = $category->id;
                    }

                    //Column 5: Sub-Category (optional)
                    $sub_category_name = trim($value[4]);
                    if (! empty($sub_category_name) && !empty($category)) {
                        // Find or create sub-category (search without location_id to find existing)
                        $sub_category = Category::where('business_id', $business_id)
                            ->where('name', $sub_category_name)
                            ->where('category_type', 'product')
                            ->where('parent_id', $category->id)
                            ->first();
                        
                        if (!$sub_category) {
                            // Create new sub-category with location_id if available
                            $sub_category = Category::create([
                                'business_id' => $business_id,
                                'name' => $sub_category_name,
                                'category_type' => 'product',
                                'parent_id' => $category->id,
                                'created_by' => $user_id,
                                'location_id' => $auto_assign_location_id
                            ]);
                        } elseif (!empty($auto_assign_location_id) && empty($sub_category->location_id)) {
                            // Update existing sub-category with location_id if it doesn't have one
                            $sub_category->location_id = $auto_assign_location_id;
                            $sub_category->save();
                        }
                        
                        $product_array['sub_category_id'] = $sub_category->id;
                    }

                    //Column 6: SKU (optional - auto-generated if empty)
                    $sku = trim($value[5]);
                    if (! empty($sku)) {
                        $product_array['sku'] = $sku;
                        //Check if product with same SKU already exist
                        $is_exist = Product::where('sku', $product_array['sku'])
                                        ->where('business_id', $business_id)
                                        ->exists();
                        if ($is_exist) {
                            $is_valid = false;
                            $error_msg = "$sku SKU already exist in row no. $row_no";
                            break;
                        }
                    } else {
                        $product_array['sku'] = ' ';
                    }

                    //Column 7: Barcode Type (optional, default: C128)
                    $barcode_type = strtoupper(trim($value[6]));
                    if (empty($barcode_type)) {
                        $product_array['barcode_type'] = 'C128';
                    } elseif (array_key_exists($barcode_type, $this->barcode_types)) {
                        $product_array['barcode_type'] = $barcode_type;
                    } else {
                        $is_valid = false;
                        $error_msg = "$barcode_type barcode type is not valid in row no. $row_no. Please, check for allowed barcode types in the instructions";
                        break;
                    }

                    //Column 8: Applicable Tax (optional)
                    $tax_name = trim($value[7]);
                    $tax_amount = 0;
                    if (! empty($tax_name)) {
                        $tax = TaxRate::where('business_id', $business_id)
                                        ->where('name', $tax_name)
                                        ->first();
                        if (! empty($tax)) {
                            $product_array['tax'] = $tax->id;
                            $tax_amount = $tax->amount;
                        } else {
                            $is_valid = false;
                            $error_msg = "Tax with name $tax_name in row no. $row_no not found. You can add tax from Settings > Tax Rates";
                            break;
                        }
                    }

                    //Column 9: Selling Price Tax Type (REQUIRED)
                    $tax_type = strtolower(trim($value[8]));
                    if (in_array($tax_type, ['inclusive', 'exclusive'])) {
                        $product_array['tax_type'] = $tax_type;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for Selling Price Tax Type in row no. $row_no. Must be 'inclusive' or 'exclusive'";
                        break;
                    }

                    //Column 10: Product Type (REQUIRED)
                    $product_type = strtolower(trim($value[9]));
                    if (in_array($product_type, ['single', 'variable', 'modifier', 'combo'])) {
                        $product_array['type'] = $product_type;
                        // Skip combo and modifier for now as they need special handling
                        if (in_array($product_type, ['combo', 'modifier'])) {
                            continue;
                        }
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for PRODUCT TYPE in row no. $row_no. Allowed: single, variable, modifier, combo";
                        break;
                    }

                    //Column 18: Enable IMEI or Serial Number (optional, default: 0)
                    $enable_sr_no = trim($value[17]);
                    if (in_array($enable_sr_no, [0, 1])) {
                        $product_array['enable_sr_no'] = $enable_sr_no;
                    } elseif (empty($enable_sr_no)) {
                        $product_array['enable_sr_no'] = 0;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for ENABLE IMEI OR SERIAL NUMBER in row no. $row_no";
                        break;
                    }

                    //Column 19: Weight (optional)
                    if (isset($value[18])) {
                        $product_array['weight'] = trim($value[18]);
                    } else {
                        $product_array['weight'] = '';
                    }

                    //Column 20: Image (optional)
                    $image_name = trim($value[19]);
                    if (! empty($image_name)) {
                        if (filter_var($image_name, FILTER_VALIDATE_URL)) {
                            $source_image = file_get_contents($image_name);
                            $path = parse_url($image_name, PHP_URL_PATH);
                            $new_name = time().'_'.basename($path);
                            $dest_img = public_path().'/uploads/'.config('constants.product_img_path').'/'.$new_name;
                            file_put_contents($dest_img, $source_image);
                            $product_array['image'] = $new_name;
                        } else {
                            $product_array['image'] = $image_name;
                        }
                    } else {
                        $product_array['image'] = '';
                    }

                    //Column 21: Product Description (optional)
                    $product_array['product_description'] = isset($value[20]) ? $value[20] : null;

                    //Columns 22-25: Custom fields (1-4)
                    for ($i = 1; $i <= 4; $i++) {
                        $col_index = 20 + $i;
                        if (isset($value[$col_index])) {
                            $product_array["product_custom_field{$i}"] = trim($value[$col_index]);
                        } else {
                            $product_array["product_custom_field{$i}"] = '';
                        }
                    }

                    //Column 26: Not For Selling (optional, default: 0)
                    $product_array['not_for_selling'] = ! empty($value[25]) && $value[25] == 1 ? 1 : 0;

                    //Column 28: Is Inactive (optional, default: 0)
                    $product_array['is_inactive'] = !empty($value[27]) && $value[27] == 1 ? 1 : 0;

                    //Column 29: Warranty (optional)
                    if (isset($value[28]) && !empty(trim($value[28]))) {
                        $warranty_identifier = trim($value[28]);
                        $warranty = Warranty::where('business_id', $business_id)
                            ->where(function($q) use ($warranty_identifier) {
                                $q->where('id', $warranty_identifier)
                                  ->orWhere('name', $warranty_identifier);
                            })->first();
                        if ($warranty) {
                            $product_array['warranty_id'] = $warranty->id;
                        }
                    }

                    //Column 30: Secondary Unit (optional)
                    if (isset($value[29]) && !empty(trim($value[29]))) {
                        $secondary_unit_name = trim($value[29]);
                        $secondary_unit = Unit::where('business_id', $business_id)
                            ->where(function ($query) use ($secondary_unit_name) {
                                $query->where('short_name', $secondary_unit_name)
                                      ->orWhere('actual_name', $secondary_unit_name);
                            })->first();
                        if ($secondary_unit) {
                            $product_array['secondary_unit_id'] = $secondary_unit->id;
                        }
                    }

                    //Column 31: Preparation Time (optional)
                    if (isset($value[30]) && trim($value[30]) !== '') {
                        $product_array['preparation_time_in_minutes'] = (int)trim($value[30]);
                    }

                    //Column 32: ML (optional)
                    if (isset($value[31]) && trim($value[31]) !== '') {
                        $product_array['ml'] = (int)trim($value[31]);
                    } else {
                        $product_array['ml'] = 0;
                    }

                    //Column 33: CT (optional)
                    if (isset($value[32]) && trim($value[32]) !== '') {
                        $product_array['ct'] = (int)trim($value[32]);
                    } else {
                        $product_array['ct'] = 0;
                    }

                    //Column 34: Product Visibility (optional, default: public)
                    if (isset($value[33]) && !empty(trim($value[33]))) {
                        $visibility = strtolower(trim($value[33]));
                        if (in_array($visibility, ['public', 'private', 'hidden'])) {
                            $product_array['productVisibility'] = $visibility;
                        } else {
                            $product_array['productVisibility'] = 'public';
                        }
                    } else {
                        $product_array['productVisibility'] = 'public';
                    }

                    //Column 35: Max Sale Limit (optional)
                    if (isset($value[34]) && trim($value[34]) !== '') {
                        $product_array['maxSaleLimit'] = (int)trim($value[34]);
                    }

                    //Column 36: Enable Selling (optional, default: 1 - forced to 1 for all products)
                    $product_array['enable_selling'] = 1;

                    //Column 37: Top Selling (optional, default: 0)
                    $product_array['top_selling'] = !empty($value[36]) && $value[36] == 1 ? 1 : 0;

                    //Column 38: Slug (optional)
                    if (isset($value[37]) && !empty(trim($value[37]))) {
                        $product_array['slug'] = trim($value[37]);
                    }

                    // Set enable_stock to 1 (stock management enabled) for all products
                    $product_array['enable_stock'] = 1;

                    if ($product_array['type'] == 'single') {
                        //Calculate profit margin
                        $profit_margin = trim($value[15]);
                        if (empty($profit_margin)) {
                            $profit_margin = $default_profit_percent;
                        }
                        $product_array['variation']['profit_percent'] = $profit_margin;

                        //Calculate purchase price
                        $dpp_inc_tax = trim($value[13]);
                        $dpp_exc_tax = trim($value[14]);
                        if ($dpp_inc_tax == '' && $dpp_exc_tax == '') {
                            $is_valid = false;
                            $error_msg = "PURCHASE PRICE is required in row no. $row_no";
                            break;
                        } else {
                            $dpp_inc_tax = ($dpp_inc_tax != '') ? $dpp_inc_tax : 0;
                            $dpp_exc_tax = ($dpp_exc_tax != '') ? $dpp_exc_tax : 0;
                        }

                        //Calculate Selling price
                        $selling_price = ! empty(trim($value[16])) ? trim($value[16]) : 0;

                        //Calculate product prices
                        $product_prices = $this->calculateVariationPrices($dpp_exc_tax, $dpp_inc_tax, $selling_price, $tax_amount, $tax_type, $profit_margin);

                        //Assign Values
                        $product_array['variation']['dpp_inc_tax'] = $product_prices['dpp_inc_tax'];
                        $product_array['variation']['dpp_exc_tax'] = $product_prices['dpp_exc_tax'];
                        $product_array['variation']['dsp_inc_tax'] = $product_prices['dsp_inc_tax'];
                        $product_array['variation']['dsp_exc_tax'] = $product_prices['dsp_exc_tax'];

                    } elseif ($product_array['type'] == 'variable') {
                        $variation_name = trim($value[10]);
                        if (empty($variation_name)) {
                            $is_valid = false;
                            $error_msg = "VARIATION NAME is required in row no. $row_no";
                            break;
                        }
                        $variation_values_string = trim($value[11]);
                        if (empty($variation_values_string)) {
                            $is_valid = false;
                            $error_msg = "VARIATION VALUES are required in row no. $row_no";
                            break;
                        }

                        $variation_sku_string = trim($value[12]);

                        $dpp_inc_tax_string = trim($value[13]);
                        $dpp_exc_tax_string = trim($value[14]);
                        $selling_price_string = trim($value[16]);
                        $profit_margin_string = trim($value[15]);

                        if (empty($dpp_inc_tax_string) && empty($dpp_exc_tax_string)) {
                            $is_valid = false;
                            $error_msg = "PURCHASE PRICE is required in row no. $row_no";
                            break;
                        }

                        //Variation values
                        $variation_values = array_map('trim', explode('|', $variation_values_string));

                        $variation_skus = [];
                        if (! empty($variation_sku_string)) {
                            $variation_skus = array_map('trim', explode('|', $variation_sku_string));
                        }

                        //Map Purchase price with variation values
                        // If single price provided (no pipe separator), apply to all variations
                        $dpp_inc_tax = [];
                        if (! empty($dpp_inc_tax_string)) {
                            // Check if it contains pipe separator (multiple prices)
                            if (strpos($dpp_inc_tax_string, '|') !== false) {
                                $dpp_inc_tax = array_map('trim', explode('|', $dpp_inc_tax_string));
                            } else {
                                // Single price - apply to all variations
                                $single_price = trim($dpp_inc_tax_string);
                                foreach ($variation_values as $k => $v) {
                                    $dpp_inc_tax[$k] = $single_price;
                                }
                            }
                        } else {
                            foreach ($variation_values as $k => $v) {
                                $dpp_inc_tax[$k] = 0;
                            }
                        }

                        $dpp_exc_tax = [];
                        if (! empty($dpp_exc_tax_string)) {
                            // Check if it contains pipe separator (multiple prices)
                            if (strpos($dpp_exc_tax_string, '|') !== false) {
                                $dpp_exc_tax = array_map('trim', explode('|', $dpp_exc_tax_string));
                            } else {
                                // Single price - apply to all variations
                                $single_price = trim($dpp_exc_tax_string);
                                foreach ($variation_values as $k => $v) {
                                    $dpp_exc_tax[$k] = $single_price;
                                }
                            }
                        } else {
                            foreach ($variation_values as $k => $v) {
                                $dpp_exc_tax[$k] = 0;
                            }
                        }

                        //Map Selling price with variation values
                        // If single price provided (no pipe separator), apply to all variations
                        $selling_price = [];
                        if (! empty($selling_price_string)) {
                            // Check if it contains pipe separator (multiple prices)
                            if (strpos($selling_price_string, '|') !== false) {
                                $selling_price = array_map('trim', explode('|', $selling_price_string));
                            } else {
                                // Single price - apply to all variations
                                $single_price = trim($selling_price_string);
                                foreach ($variation_values as $k => $v) {
                                    $selling_price[$k] = $single_price;
                                }
                            }
                        } else {
                            foreach ($variation_values as $k => $v) {
                                $selling_price[$k] = 0;
                            }
                        }

                        //Map profit margin with variation values
                        // If single margin provided (no pipe separator), apply to all variations
                        $profit_margin = [];
                        if (! empty($profit_margin_string)) {
                            // Check if it contains pipe separator (multiple margins)
                            if (strpos($profit_margin_string, '|') !== false) {
                                $profit_margin = array_map('trim', explode('|', $profit_margin_string));
                            } else {
                                // Single margin - apply to all variations
                                $single_margin = trim($profit_margin_string);
                                foreach ($variation_values as $k => $v) {
                                    $profit_margin[$k] = $single_margin;
                                }
                            }
                        } else {
                            foreach ($variation_values as $k => $v) {
                                $profit_margin[$k] = $default_profit_percent;
                            }
                        }

                        // Normalize all price arrays to match variation_values length
                        // If array is shorter, pad with first value; if longer, truncate to match
                        $variation_count = count($variation_values);
                        
                        // Normalize dpp_inc_tax
                        if (count($dpp_inc_tax) < $variation_count) {
                            $first_value = !empty($dpp_inc_tax) ? $dpp_inc_tax[0] : 0;
                            while (count($dpp_inc_tax) < $variation_count) {
                                $dpp_inc_tax[] = $first_value;
                            }
                        } elseif (count($dpp_inc_tax) > $variation_count) {
                            $dpp_inc_tax = array_slice($dpp_inc_tax, 0, $variation_count);
                        }

                        // Normalize dpp_exc_tax
                        if (count($dpp_exc_tax) < $variation_count) {
                            $first_value = !empty($dpp_exc_tax) ? $dpp_exc_tax[0] : 0;
                            while (count($dpp_exc_tax) < $variation_count) {
                                $dpp_exc_tax[] = $first_value;
                            }
                        } elseif (count($dpp_exc_tax) > $variation_count) {
                            $dpp_exc_tax = array_slice($dpp_exc_tax, 0, $variation_count);
                        }

                        // Normalize selling_price
                        if (count($selling_price) < $variation_count) {
                            $first_value = !empty($selling_price) ? $selling_price[0] : 0;
                            while (count($selling_price) < $variation_count) {
                                $selling_price[] = $first_value;
                            }
                        } elseif (count($selling_price) > $variation_count) {
                            $selling_price = array_slice($selling_price, 0, $variation_count);
                        }

                        // Normalize profit_margin
                        if (count($profit_margin) < $variation_count) {
                            $first_value = !empty($profit_margin) ? $profit_margin[0] : $default_profit_percent;
                            while (count($profit_margin) < $variation_count) {
                                $profit_margin[] = $first_value;
                            }
                        } elseif (count($profit_margin) > $variation_count) {
                            $profit_margin = array_slice($profit_margin, 0, $variation_count);
                        }

                        // Normalize variation_skus
                        if (! empty($variation_skus)) {
                            if (count($variation_skus) < $variation_count) {
                                $first_value = !empty($variation_skus) ? $variation_skus[0] : '';
                                while (count($variation_skus) < $variation_count) {
                                    $variation_skus[] = $first_value;
                                }
                            } elseif (count($variation_skus) > $variation_count) {
                                $variation_skus = array_slice($variation_skus, 0, $variation_count);
                            }
                        }
                        $product_array['variation']['name'] = $variation_name;

                        //Check if variation exists or create new
                        $variation = $this->productUtil->createOrNewVariation($business_id, $variation_name);
                        $product_array['variation']['variation_template_id'] = $variation->id;

                        foreach ($variation_values as $k => $v) {
                            $variation_prices = $this->calculateVariationPrices($dpp_exc_tax[$k], $dpp_inc_tax[$k], $selling_price[$k], $tax_amount, $tax_type, $profit_margin[$k]);

                            //get variation value
                            $variation_value = $variation->values->filter(function ($item) use ($v) {
                                return strtolower($item->name) == strtolower($v);
                            })->first();

                            if (empty($variation_value)) {
                                $variation_value = VariationValueTemplate::create([
                                    'name' => $v,
                                    'variation_template_id' => $variation->id,
                                ]);
                            }

                            //Assign Values
                            $product_array['variation']['variations'][] = [
                                'value' => $v,
                                'variation_value_id' => $variation_value->id,
                                'default_purchase_price' => $variation_prices['dpp_exc_tax'],
                                'dpp_inc_tax' => $variation_prices['dpp_inc_tax'],
                                'profit_percent' => $this->productUtil->num_f($profit_margin[$k]),
                                'default_sell_price' => $variation_prices['dsp_exc_tax'],
                                'sell_price_inc_tax' => $variation_prices['dsp_inc_tax'],
                                'sub_sku' => ! empty($variation_skus[$k]) ? $variation_skus[$k] : '',
                            ];
                        }
                    }
                    //Assign to formated array
                    $formated_data[] = $product_array;
                }

                if (! $is_valid) {
                    throw new \Exception($error_msg);
                }

                if (! empty($formated_data)) {
                    foreach ($formated_data as $index => $product_data) {
                        $variation_data = $product_data['variation'];
                        unset($product_data['variation']);

                        //Create new product
                        $product = Product::create($product_data);
                        
                        // Auto-generate slug if not provided
                        if (empty($product->slug)) {
                            $product->slug = $this->slugMaker($product->name, $product->id);
                            $product->save();
                        }
                        
                        //If auto generate sku generate new sku
                        if ($product->sku == ' ') {
                            // Reload product with brand relationship
                            $product->load('brand');
                            // Check if product has a brand
                            if (!empty($product->brand_id) && $product->brand) {
                                //Brand (MoonBuzz or Yo Shop)+Product name first 4 (Top Mint Flavour Small Size Biscuit) + product id
                                // expected SKU: MOONTMFS4401 (first 4 from brand exclude space + first letter from 4 words of product name)
                                // expected SKU: YOSHTMFS4402
                                
                                $brandName = $product->brand->name;
                                $productName = $product->name;
                                
                                // Get first 4 characters from brand name (excluding spaces), uppercase
                                $brandNameWithoutSpaces = str_replace(' ', '', $brandName);
                                $brandPrefix = strtoupper(substr($brandNameWithoutSpaces, 0, 4));
                                
                                // Get first letter from first 4 words of product name, uppercase
                                $productWords = explode(' ', trim($productName));
                                $productPrefix = '';
                                $wordCount = min(4, count($productWords));
                                for($i = 0; $i < $wordCount; $i++){
                                    if (!empty($productWords[$i])) {
                                        $productPrefix .= strtoupper(substr($productWords[$i], 0, 1));
                                    }
                                }
                                
                                // Combine: Brand prefix + Product prefix + Product ID
                                $productId = $product->id;
                                $sku = $brandPrefix . $productPrefix . $productId;
                                $product->sku = $sku;
                                $product->save();
                            } else {
                                // Fallback to original SKU generation if no brand
                                $sku = $this->productUtil->generateProductSku($product->id);
                                $product->sku = $sku;
                                $product->save();
                            }
                        }

                        //Product locations (Column 27)
                        // Force all products to location_id 1
                        $product->product_locations()->sync([1]);

                        //Create single product variation
                        if ($product->type == 'single') {
                            $this->productUtil->createSingleProductVariation(
                                $product,
                                $product->sku,
                                $variation_data['dpp_exc_tax'],
                                $variation_data['dpp_inc_tax'],
                                $variation_data['profit_percent'],
                                $variation_data['dsp_exc_tax'],
                                $variation_data['dsp_inc_tax']
                            );
                        } elseif ($product->type == 'variable') {
                            //Create variable product variations
                            $this->productUtil->createVariableProductVariations(
                                $product,
                                [$variation_data],
                                "with_out_variation",
                                $business_id
                            );
                        }

                        // Assign webcategories if category exists
                        if (!empty($product->category_id)) {
                            $category = Category::find($product->category_id);
                            if ($category) {
                                $product->webcategories()->syncWithoutDetaching([$category->id]);
                            }
                        }
                        if (!empty($product->sub_category_id)) {
                            $sub_category = Category::find($product->sub_category_id);
                            if ($sub_category) {
                                $product->webcategories()->syncWithoutDetaching([$sub_category->id]);
                            }
                        }
                    }
                }
            }

            $output = ['success' => 1,
                'msg' => __('product.file_imported_successfully'),
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];

            return redirect('import-products')->with('notification', $output);
        }

        return redirect('import-products')->with('status', $output);
    }

    /**
     * Download CSV template with proper headers
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        $file_path = public_path('files/import_products_csv_template1.csv');
        
        if (file_exists($file_path)) {
            return response()->download($file_path, 'import_products_csv_template1.csv', [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="import_products_csv_template1.csv"',
            ]);
        }
        
        abort(404, 'Template file not found');
    }

    /**
     * Generate a unique slug for a product
     *
     * @param  string  $baseName
     * @param  int|null  $productId
     * @return string
     */
    private function slugMaker($baseName, $productId = null)
    {
        $baseName = Str::slug($baseName);
        $counter = 0;
        $newSlug = $baseName;
        while (Product::where('slug', $newSlug)
            ->when($productId, function ($query, $productId) {
                $query->where('id', '!=', $productId);
            })
            ->exists()
        ) {
            $counter++;
            $newSlug = $baseName . '-' . $counter;
        }
        return $newSlug;
    }

    private function calculateVariationPrices($dpp_exc_tax, $dpp_inc_tax, $selling_price, $tax_amount, $tax_type, $margin)
    {
        //Calculate purchase prices
        if ($dpp_inc_tax == 0) {
            $dpp_inc_tax = $this->productUtil->calc_percentage(
                $dpp_exc_tax,
                $tax_amount,
                $dpp_exc_tax
            );
        }

        if ($dpp_exc_tax == 0) {
            $dpp_exc_tax = $this->productUtil->calc_percentage_base($dpp_inc_tax, $tax_amount);
        }

        if ($selling_price != 0) {
            if ($tax_type == 'inclusive') {
                $dsp_inc_tax = $selling_price;
                $dsp_exc_tax = $this->productUtil->calc_percentage_base(
                    $dsp_inc_tax,
                    $tax_amount
                );
            } elseif ($tax_type == 'exclusive') {
                $dsp_exc_tax = $selling_price;
                $dsp_inc_tax = $this->productUtil->calc_percentage(
                    $selling_price,
                    $tax_amount,
                    $selling_price
                );
            }
        } else {
            $dsp_exc_tax = $this->productUtil->calc_percentage(
                $dpp_exc_tax,
                $margin,
                $dpp_exc_tax
            );
            $dsp_inc_tax = $this->productUtil->calc_percentage(
                $dsp_exc_tax,
                $tax_amount,
                $dsp_exc_tax
            );
        }

        return [
            'dpp_exc_tax' => $this->productUtil->num_f($dpp_exc_tax),
            'dpp_inc_tax' => $this->productUtil->num_f($dpp_inc_tax),
            'dsp_exc_tax' => $this->productUtil->num_f($dsp_exc_tax),
            'dsp_inc_tax' => $this->productUtil->num_f($dsp_inc_tax),
        ];
    }

    /**
     * Export all products and variations with image information
     *
     * @return \Illuminate\Http\Response
     */
    public function exportImages()
    {
        if (! auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        $filename = 'products_variations_images_' . date('Y-m-d_His') . '.xlsx';
        
        return Excel::download(new ProductImageMappingExport, $filename);
    }

    /**
     * Import products with SKU regeneration for existing products
     * If product with same name exists: remove old SKU and generate new auto-generated SKU
     * If product is new: follow normal import flow to create new product
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeWithSkuRegeneration(Request $request)
    {
        if (! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $notAllowed = $this->productUtil->notAllowedInDemo();
            if (! empty($notAllowed)) {
                return $notAllowed;
            }

            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            if ($request->hasFile('products_csv')) {
                $file = $request->file('products_csv');

                $parsed_array = Excel::toArray([], $file);

                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');
                $default_profit_percent = $request->session()->get('business.default_profit_percent');

                $formated_data = [];
                $is_valid = true;
                $error_msg = '';
                $total_rows = count($imported_data);

                //Check if subscribed or not, then check for products quota
                if (! $this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse();
                } elseif (! $this->moduleUtil->isQuotaAvailable('products', $business_id, $total_rows)) {
                    return $this->moduleUtil->quotaExpiredResponse('products', $business_id, action([\App\Http\Controllers\ImportProductsController::class, 'index']));
                }

                $business_locations = BusinessLocation::where('business_id', $business_id)->get();
                
                // Get user's permitted locations to auto-assign product location
                $user = auth()->user();
                $permitted_locations = $user->permitted_locations($business_id);
                $auto_assign_location_id = null;
                
                // If user has exactly one permitted location, auto-assign it
                if (is_array($permitted_locations) && count($permitted_locations) === 1) {
                    $auto_assign_location_id = $permitted_locations[0];
                }
                
                DB::beginTransaction();
                
                foreach ($imported_data as $key => $value) {
                    //Check if any column is missing (minimum 38 columns)
                    if (count($value) < 38) {
                        $is_valid = false;
                        $error_msg = 'Some of the columns are missing. Minimum 38 columns required. Please, use latest CSV file template.';
                        break;
                    }

                    $row_no = $key + 1;
                    $product_array = [];
                    $product_array['business_id'] = $business_id;
                    $product_array['created_by'] = $user_id;

                    //Column 1: Product Name (REQUIRED)
                    $product_name = trim($value[0]);
                    if (! empty($product_name)) {
                        $product_array['name'] = $product_name;
                    } else {
                        $is_valid = false;
                        $error_msg = "Product name is required in row no. $row_no";
                        break;
                    }

                    // Check if product with same name exists
                    $existing_product = Product::where('business_id', $business_id)
                        ->where('name', $product_name)
                        ->first();

                    if ($existing_product) {
                        // Product exists: Remove old SKU and generate new auto-generated SKU
                        $existing_product->sku = ' '; // Set to space to trigger auto-generation
                        
                        // Check if brand needs to be added/updated from CSV
                        $brand_name = trim($value[1]);
                        if (!empty($brand_name)) {
                            // Load brand relationship if not already loaded
                            if (!$existing_product->relationLoaded('brand')) {
                                $existing_product->load('brand');
                            }
                            
                            // Get current brand name for comparison
                            $current_brand_name = $existing_product->brand ? $existing_product->brand->name : null;
                            
                            // If product doesn't have a brand, or brand name is different, update it
                            if (empty($existing_product->brand_id) || 
                                ($current_brand_name && strtolower(trim($current_brand_name)) != strtolower(trim($brand_name)))) {
                                $brand = Brands::firstOrCreate(
                                    ['business_id' => $business_id, 'name' => $brand_name],
                                    ['created_by' => $user_id, 'location_id' => $auto_assign_location_id]
                                );
                                if (!empty($auto_assign_location_id) && empty($brand->location_id)) {
                                    $brand->location_id = $auto_assign_location_id;
                                    $brand->save();
                                }
                                // Use update method to ensure brand_id is saved
                                $existing_product->update(['brand_id' => $brand->id]);
                            }
                        }
                        
                        // Refresh product to ensure all changes are saved and relationships are fresh
                        $existing_product->refresh();
                        $existing_product->load('brand');
                        
                        // Generate new SKU using same logic as import
                        if (!empty($existing_product->brand_id) && $existing_product->brand) {
                            $brandName = $existing_product->brand->name;
                            $productName = $existing_product->name;
                            
                            // Get first 4 characters from brand name (excluding spaces), uppercase
                            $brandNameWithoutSpaces = str_replace(' ', '', $brandName);
                            $brandPrefix = strtoupper(substr($brandNameWithoutSpaces, 0, 4));
                            
                            // Get first letter from first 4 words of product name, uppercase
                            $productWords = explode(' ', trim($productName));
                            $productPrefix = '';
                            $wordCount = min(4, count($productWords));
                            for($i = 0; $i < $wordCount; $i++){
                                if (!empty($productWords[$i])) {
                                    $productPrefix .= strtoupper(substr($productWords[$i], 0, 1));
                                }
                            }
                            
                            // Combine: Brand prefix + Product prefix + Product ID
                            $productId = $existing_product->id;
                            $new_sku = $brandPrefix . $productPrefix . $productId;
                            $existing_product->sku = $new_sku;
                            $existing_product->save();
                        } else {
                            // Fallback to original SKU generation if no brand
                            $new_sku = $this->productUtil->generateProductSku($existing_product->id);
                            $existing_product->sku = $new_sku;
                            $existing_product->save();
                        }
                        
                        // Final refresh to ensure all changes (brand, SKU) are persisted
                        $existing_product->refresh();
                        
                        // Skip to next row - product already exists and SKU regenerated
                        continue;
                    }

                    // Product doesn't exist - follow normal import flow
                    // Process all columns same as normal import
                    //Column 2: Brand (optional)
                    $brand_name = trim($value[1]);
                    if (! empty($brand_name)) {
                        $brand = Brands::firstOrCreate(
                            ['business_id' => $business_id, 'name' => $brand_name],
                            ['created_by' => $user_id, 'location_id' => $auto_assign_location_id]
                        );
                        if (!empty($auto_assign_location_id) && empty($brand->location_id)) {
                            $brand->location_id = $auto_assign_location_id;
                            $brand->save();
                        }
                        $product_array['brand_id'] = $brand->id;
                    }

                    //Column 3: Unit (REQUIRED)
                    $unit_name = trim($value[2]);
                    if (! empty($unit_name)) {
                        $unit = Unit::where('business_id', $business_id)
                                    ->where(function ($query) use ($unit_name) {
                                        $query->where('short_name', $unit_name)
                                              ->orWhere('actual_name', $unit_name);
                                    })->first();
                        if (! empty($unit)) {
                            $product_array['unit_id'] = $unit->id;
                        } else {
                            $is_valid = false;
                            $error_msg = "Unit with name $unit_name not found in row no. $row_no. You can add unit from Products > Units";
                            break;
                        }
                    } else {
                        $is_valid = false;
                        $error_msg = "UNIT is required in row no. $row_no";
                        break;
                    }

                    //Column 4: Category (optional)
                    $category_name = trim($value[3]);
                    if (! empty($category_name)) {
                        $category = Category::where('business_id', $business_id)
                            ->where('name', $category_name)
                            ->where('category_type', 'product')
                            ->where('parent_id', 0)
                            ->first();
                        
                        if (!$category) {
                            $category = Category::create([
                                'business_id' => $business_id,
                                'name' => $category_name,
                                'category_type' => 'product',
                                'parent_id' => 0,
                                'created_by' => $user_id,
                                'location_id' => $auto_assign_location_id
                            ]);
                        } elseif (!empty($auto_assign_location_id) && empty($category->location_id)) {
                            $category->location_id = $auto_assign_location_id;
                            $category->save();
                        }
                        
                        $product_array['category_id'] = $category->id;
                    }

                    //Column 5: Sub-Category (optional)
                    $sub_category_name = trim($value[4]);
                    if (! empty($sub_category_name) && !empty($category)) {
                        $sub_category = Category::where('business_id', $business_id)
                            ->where('name', $sub_category_name)
                            ->where('category_type', 'product')
                            ->where('parent_id', $category->id)
                            ->first();
                        
                        if (!$sub_category) {
                            $sub_category = Category::create([
                                'business_id' => $business_id,
                                'name' => $sub_category_name,
                                'category_type' => 'product',
                                'parent_id' => $category->id,
                                'created_by' => $user_id,
                                'location_id' => $auto_assign_location_id
                            ]);
                        } elseif (!empty($auto_assign_location_id) && empty($sub_category->location_id)) {
                            $sub_category->location_id = $auto_assign_location_id;
                            $sub_category->save();
                        }
                        
                        $product_array['sub_category_id'] = $sub_category->id;
                    }

                    //Column 6: SKU (optional - auto-generated if empty)
                    $sku = trim($value[5]);
                    if (! empty($sku)) {
                        $product_array['sku'] = $sku;
                        //Check if product with same SKU already exist
                        $is_exist = Product::where('sku', $product_array['sku'])
                                        ->where('business_id', $business_id)
                                        ->exists();
                        if ($is_exist) {
                            $is_valid = false;
                            $error_msg = "$sku SKU already exist in row no. $row_no";
                            break;
                        }
                    } else {
                        $product_array['sku'] = ' ';
                    }

                    // Continue processing remaining columns using the same logic as store() method
                    // For brevity, I'll reuse the same processing logic
                    // You can copy the rest of the columns processing from the store() method
                    
                    //Column 7: Barcode Type
                    $barcode_type = strtoupper(trim($value[6]));
                    if (empty($barcode_type)) {
                        $product_array['barcode_type'] = 'C128';
                    } elseif (array_key_exists($barcode_type, $this->barcode_types)) {
                        $product_array['barcode_type'] = $barcode_type;
                    } else {
                        $is_valid = false;
                        $error_msg = "$barcode_type barcode type is not valid in row no. $row_no";
                        break;
                    }

                    //Column 8: Applicable Tax
                    $tax_name = trim($value[7]);
                    $tax_amount = 0;
                    if (! empty($tax_name)) {
                        $tax = TaxRate::where('business_id', $business_id)
                                        ->where('name', $tax_name)
                                        ->first();
                        if (! empty($tax)) {
                            $product_array['tax'] = $tax->id;
                            $tax_amount = $tax->amount;
                        } else {
                            $is_valid = false;
                            $error_msg = "Tax with name $tax_name in row no. $row_no not found";
                            break;
                        }
                    }

                    //Column 9: Selling Price Tax Type
                    $tax_type = strtolower(trim($value[8]));
                    if (in_array($tax_type, ['inclusive', 'exclusive'])) {
                        $product_array['tax_type'] = $tax_type;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for Selling Price Tax Type in row no. $row_no";
                        break;
                    }

                    //Column 10: Product Type
                    $product_type = strtolower(trim($value[9]));
                    if (in_array($product_type, ['single', 'variable', 'modifier', 'combo'])) {
                        $product_array['type'] = $product_type;
                        if (in_array($product_type, ['combo', 'modifier'])) {
                            continue;
                        }
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for PRODUCT TYPE in row no. $row_no";
                        break;
                    }

                    // Process remaining columns (11-38) - same as store() method
                    //Column 18: Enable IMEI or Serial Number
                    $product_array['enable_sr_no'] = isset($value[17]) && in_array(trim($value[17]), [0, 1]) ? trim($value[17]) : 0;
                    
                    //Column 19: Weight
                    if (isset($value[18])) {
                        $product_array['weight'] = trim($value[18]);
                    } else {
                        $product_array['weight'] = '';
                    }
                    
                    //Column 20: Image
                    $image_name = trim($value[19]);
                    if (! empty($image_name)) {
                        if (filter_var($image_name, FILTER_VALIDATE_URL)) {
                            $source_image = file_get_contents($image_name);
                            $path = parse_url($image_name, PHP_URL_PATH);
                            $new_name = time().'_'.basename($path);
                            $dest_img = public_path().'/uploads/'.config('constants.product_img_path').'/'.$new_name;
                            file_put_contents($dest_img, $source_image);
                            $product_array['image'] = $new_name;
                        } else {
                            $product_array['image'] = $image_name;
                        }
                    } else {
                        $product_array['image'] = '';
                    }
                    
                    //Column 21: Product Description
                    $product_array['product_description'] = isset($value[20]) ? $value[20] : null;
                    
                    //Columns 22-25: Custom fields (1-4)
                    for ($i = 1; $i <= 4; $i++) {
                        $col_index = 20 + $i;
                        if (isset($value[$col_index])) {
                            $product_array["product_custom_field{$i}"] = trim($value[$col_index]);
                        } else {
                            $product_array["product_custom_field{$i}"] = '';
                        }
                    }
                    
                    //Column 26: Not For Selling
                    $product_array['not_for_selling'] = ! empty($value[25]) && $value[25] == 1 ? 1 : 0;
                    
                    //Column 27: Product Locations
                    $product_array['product_locations'] = isset($value[26]) ? trim($value[26]) : '';
                    
                    //Column 28: Is Inactive
                    $product_array['is_inactive'] = !empty($value[27]) && $value[27] == 1 ? 1 : 0;
                    
                    //Column 29: Warranty
                    if (isset($value[28]) && !empty(trim($value[28]))) {
                        $warranty_identifier = trim($value[28]);
                        $warranty = Warranty::where('business_id', $business_id)
                            ->where(function($q) use ($warranty_identifier) {
                                $q->where('id', $warranty_identifier)
                                  ->orWhere('name', $warranty_identifier);
                            })->first();
                        if ($warranty) {
                            $product_array['warranty_id'] = $warranty->id;
                        }
                    }
                    
                    //Column 30: Secondary Unit
                    if (isset($value[29]) && !empty(trim($value[29]))) {
                        $secondary_unit_name = trim($value[29]);
                        $secondary_unit = Unit::where('business_id', $business_id)
                            ->where(function ($query) use ($secondary_unit_name) {
                                $query->where('short_name', $secondary_unit_name)
                                      ->orWhere('actual_name', $secondary_unit_name);
                            })->first();
                        if ($secondary_unit) {
                            $product_array['secondary_unit_id'] = $secondary_unit->id;
                        }
                    }
                    
                    //Column 31: Preparation Time
                    if (isset($value[30]) && trim($value[30]) !== '') {
                        $product_array['preparation_time_in_minutes'] = (int)trim($value[30]);
                    }
                    
                    //Column 32: ML
                    if (isset($value[31]) && trim($value[31]) !== '') {
                        $product_array['ml'] = (int)trim($value[31]);
                    } else {
                        $product_array['ml'] = 0;
                    }
                    
                    //Column 33: CT
                    if (isset($value[32]) && trim($value[32]) !== '') {
                        $product_array['ct'] = (int)trim($value[32]);
                    } else {
                        $product_array['ct'] = 0;
                    }
                    
                    //Column 34: Product Visibility
                    if (isset($value[33]) && !empty(trim($value[33]))) {
                        $visibility = strtolower(trim($value[33]));
                        if (in_array($visibility, ['public', 'private', 'hidden'])) {
                            $product_array['productVisibility'] = $visibility;
                        } else {
                            $product_array['productVisibility'] = 'public';
                        }
                    } else {
                        $product_array['productVisibility'] = 'public';
                    }
                    
                    //Column 35: Max Sale Limit
                    if (isset($value[34]) && trim($value[34]) !== '') {
                        $product_array['maxSaleLimit'] = (int)trim($value[34]);
                    }
                    
                    //Column 36: Enable Selling
                    $product_array['enable_selling'] = 1;
                    
                    //Column 37: Top Selling
                    $product_array['top_selling'] = !empty($value[36]) && $value[36] == 1 ? 1 : 0;
                    
                    //Column 38: Slug
                    if (isset($value[37]) && !empty(trim($value[37]))) {
                        $product_array['slug'] = trim($value[37]);
                    }
                    
                    // Set enable_stock to 1
                    $product_array['enable_stock'] = 1;

                    // Process variation data
                    try {
                        $variation_data = $this->processVariationData($value, $product_array, $tax_amount, $default_profit_percent, $business_id, $user_id);
                    } catch (\Exception $e) {
                        $is_valid = false;
                        $error_msg = $e->getMessage() . " in row no. $row_no";
                        break;
                    }
                    
                    $product_array['variation'] = $variation_data;
                    $formated_data[] = $product_array;
                }

                if (! $is_valid) {
                    throw new \Exception($error_msg);
                }

                // Create products
                if (! empty($formated_data)) {
                    foreach ($formated_data as $index => $product_data) {
                        $variation_data = $product_data['variation'];
                        unset($product_data['variation']);

                        //Create new product
                        $product = Product::create($product_data);
                        
                        // Auto-generate slug if not provided
                        if (empty($product->slug)) {
                            $product->slug = $this->slugMaker($product->name, $product->id);
                            $product->save();
                        }
                        
                        //If auto generate sku generate new sku
                        if ($product->sku == ' ') {
                            $product->load('brand');
                            if (!empty($product->brand_id) && $product->brand) {
                                $brandName = $product->brand->name;
                                $productName = $product->name;
                                
                                $brandNameWithoutSpaces = str_replace(' ', '', $brandName);
                                $brandPrefix = strtoupper(substr($brandNameWithoutSpaces, 0, 4));
                                
                                $productWords = explode(' ', trim($productName));
                                $productPrefix = '';
                                $wordCount = min(4, count($productWords));
                                for($i = 0; $i < $wordCount; $i++){
                                    if (!empty($productWords[$i])) {
                                        $productPrefix .= strtoupper(substr($productWords[$i], 0, 1));
                                    }
                                }
                                
                                $productId = $product->id;
                                $sku = $brandPrefix . $productPrefix . $productId;
                                $product->sku = $sku;
                                $product->save();
                            } else {
                                $sku = $this->productUtil->generateProductSku($product->id);
                                $product->sku = $sku;
                                $product->save();
                            }
                        }

                        //Product locations
                        $product->product_locations()->sync([1]);

                        //Create single product variation
                        if ($product->type == 'single') {
                            $this->productUtil->createSingleProductVariation(
                                $product,
                                $product->sku,
                                $variation_data['dpp_exc_tax'],
                                $variation_data['dpp_inc_tax'],
                                $variation_data['profit_percent'],
                                $variation_data['dsp_exc_tax'],
                                $variation_data['dsp_inc_tax']
                            );
                        } elseif ($product->type == 'variable') {
                            // For variable products, variation_data contains the variation structure
                            $this->productUtil->createVariableProductVariations(
                                $product,
                                [$variation_data],
                                "with_out_variation",
                                $business_id
                            );
                        }

                        // Assign webcategories
                        if (!empty($product->category_id)) {
                            $category = Category::find($product->category_id);
                            if ($category) {
                                $product->webcategories()->syncWithoutDetaching([$category->id]);
                            }
                        }
                        if (!empty($product->sub_category_id)) {
                            $sub_category = Category::find($product->sub_category_id);
                            if ($sub_category) {
                                $product->webcategories()->syncWithoutDetaching([$sub_category->id]);
                            }
                        }
                    }
                }

                $output = ['success' => 1,
                    'msg' => __('product.file_imported_successfully'),
                ];

                DB::commit();
            } else {
                $output = ['success' => 0,
                    'msg' => __('product.file_not_found'),
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];
        }

        if ($request->ajax()) {
            return response()->json($output);
        }

        return redirect('import-products')->with('notification', $output);
    }

    /**
     * Helper method to process variation data (extracted from store method logic)
     */
    private function processVariationData($value, $product_array, $tax_amount, $default_profit_percent, $business_id, $user_id)
    {
        $tax_type = $product_array['tax_type'];
        
        if ($product_array['type'] == 'single') {
            //Calculate profit margin
            $profit_margin = trim($value[15]);
            if (empty($profit_margin)) {
                $profit_margin = $default_profit_percent;
            }
            
            //Calculate purchase price
            $dpp_inc_tax = trim($value[13]);
            $dpp_exc_tax = trim($value[14]);
            if ($dpp_inc_tax == '' && $dpp_exc_tax == '') {
                throw new \Exception("PURCHASE PRICE is required");
            } else {
                $dpp_inc_tax = ($dpp_inc_tax != '') ? $dpp_inc_tax : 0;
                $dpp_exc_tax = ($dpp_exc_tax != '') ? $dpp_exc_tax : 0;
            }
            
            //Calculate Selling price
            $selling_price = ! empty(trim($value[16])) ? trim($value[16]) : 0;
            
            //Calculate product prices
            $product_prices = $this->calculateVariationPrices($dpp_exc_tax, $dpp_inc_tax, $selling_price, $tax_amount, $tax_type, $profit_margin);
            
            return [
                'dpp_inc_tax' => $product_prices['dpp_inc_tax'],
                'dpp_exc_tax' => $product_prices['dpp_exc_tax'],
                'dsp_inc_tax' => $product_prices['dsp_inc_tax'],
                'dsp_exc_tax' => $product_prices['dsp_exc_tax'],
                'profit_percent' => $profit_margin,
            ];
        } elseif ($product_array['type'] == 'variable') {
            $variation_name = trim($value[10]);
            if (empty($variation_name)) {
                throw new \Exception("VARIATION NAME is required");
            }
            
            $variation_values_string = trim($value[11]);
            if (empty($variation_values_string)) {
                throw new \Exception("VARIATION VALUES are required");
            }
            
            $variation_sku_string = trim($value[12]);
            $dpp_inc_tax_string = trim($value[13]);
            $dpp_exc_tax_string = trim($value[14]);
            $selling_price_string = trim($value[16]);
            $profit_margin_string = trim($value[15]);
            
            if (empty($dpp_inc_tax_string) && empty($dpp_exc_tax_string)) {
                throw new \Exception("PURCHASE PRICE is required");
            }
            
            //Variation values
            $variation_values = array_map('trim', explode('|', $variation_values_string));
            
            $variation_skus = [];
            if (! empty($variation_sku_string)) {
                $variation_skus = array_map('trim', explode('|', $variation_sku_string));
            }
            
            //Map Purchase price with variation values
            $dpp_inc_tax = [];
            if (! empty($dpp_inc_tax_string)) {
                if (strpos($dpp_inc_tax_string, '|') !== false) {
                    $dpp_inc_tax = array_map('trim', explode('|', $dpp_inc_tax_string));
                } else {
                    $single_price = trim($dpp_inc_tax_string);
                    foreach ($variation_values as $k => $v) {
                        $dpp_inc_tax[$k] = $single_price;
                    }
                }
            } else {
                foreach ($variation_values as $k => $v) {
                    $dpp_inc_tax[$k] = 0;
                }
            }
            
            $dpp_exc_tax = [];
            if (! empty($dpp_exc_tax_string)) {
                if (strpos($dpp_exc_tax_string, '|') !== false) {
                    $dpp_exc_tax = array_map('trim', explode('|', $dpp_exc_tax_string));
                } else {
                    $single_price = trim($dpp_exc_tax_string);
                    foreach ($variation_values as $k => $v) {
                        $dpp_exc_tax[$k] = $single_price;
                    }
                }
            } else {
                foreach ($variation_values as $k => $v) {
                    $dpp_exc_tax[$k] = 0;
                }
            }
            
            //Map Selling price with variation values
            $selling_price = [];
            if (! empty($selling_price_string)) {
                if (strpos($selling_price_string, '|') !== false) {
                    $selling_price = array_map('trim', explode('|', $selling_price_string));
                } else {
                    $single_price = trim($selling_price_string);
                    foreach ($variation_values as $k => $v) {
                        $selling_price[$k] = $single_price;
                    }
                }
            } else {
                foreach ($variation_values as $k => $v) {
                    $selling_price[$k] = 0;
                }
            }
            
            //Map profit margin with variation values
            $profit_margin = [];
            if (! empty($profit_margin_string)) {
                if (strpos($profit_margin_string, '|') !== false) {
                    $profit_margin = array_map('trim', explode('|', $profit_margin_string));
                } else {
                    $single_margin = trim($profit_margin_string);
                    foreach ($variation_values as $k => $v) {
                        $profit_margin[$k] = $single_margin;
                    }
                }
            } else {
                foreach ($variation_values as $k => $v) {
                    $profit_margin[$k] = $default_profit_percent;
                }
            }
            
            // Normalize all arrays to match variation_values length
            $variation_count = count($variation_values);
            
            foreach (['dpp_inc_tax', 'dpp_exc_tax', 'selling_price', 'profit_margin'] as $arr_name) {
                $arr = $$arr_name;
                if (count($arr) < $variation_count) {
                    $first_value = !empty($arr) ? $arr[0] : ($arr_name == 'profit_margin' ? $default_profit_percent : 0);
                    while (count($arr) < $variation_count) {
                        $arr[] = $first_value;
                    }
                } elseif (count($arr) > $variation_count) {
                    $arr = array_slice($arr, 0, $variation_count);
                }
                $$arr_name = $arr;
            }
            
            // Normalize variation_skus
            if (! empty($variation_skus)) {
                if (count($variation_skus) < $variation_count) {
                    $first_value = !empty($variation_skus) ? $variation_skus[0] : '';
                    while (count($variation_skus) < $variation_count) {
                        $variation_skus[] = $first_value;
                    }
                } elseif (count($variation_skus) > $variation_count) {
                    $variation_skus = array_slice($variation_skus, 0, $variation_count);
                }
            }
            
            //Check if variation exists or create new
            $variation = $this->productUtil->createOrNewVariation($business_id, $variation_name);
            
            $variation_data = [
                'name' => $variation_name,
                'variation_template_id' => $variation->id,
                'variations' => []
            ];
            
            foreach ($variation_values as $k => $v) {
                $variation_prices = $this->calculateVariationPrices($dpp_exc_tax[$k], $dpp_inc_tax[$k], $selling_price[$k], $tax_amount, $tax_type, $profit_margin[$k]);
                
                //get variation value
                $variation_value = $variation->values->filter(function ($item) use ($v) {
                    return strtolower($item->name) == strtolower($v);
                })->first();
                
                if (empty($variation_value)) {
                    $variation_value = VariationValueTemplate::create([
                        'name' => $v,
                        'variation_template_id' => $variation->id,
                    ]);
                }
                
                //Assign Values
                $variation_data['variations'][] = [
                    'value' => $v,
                    'variation_value_id' => $variation_value->id,
                    'default_purchase_price' => $variation_prices['dpp_exc_tax'],
                    'dpp_inc_tax' => $variation_prices['dpp_inc_tax'],
                    'profit_percent' => $this->productUtil->num_f($profit_margin[$k]),
                    'default_sell_price' => $variation_prices['dsp_exc_tax'],
                    'sell_price_inc_tax' => $variation_prices['dsp_inc_tax'],
                    'sub_sku' => ! empty($variation_skus[$k]) ? $variation_skus[$k] : '',
                ];
            }
            
            return $variation_data;
        }
        
        return [];
    }

    /**
     * Import image mapping CSV to update product/variation images
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importImageMapping(Request $request)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $notAllowed = $this->productUtil->notAllowedInDemo();
            if (! empty($notAllowed)) {
                return $notAllowed;
            }

            if ($request->hasFile('image_mapping_csv')) {
                $file = $request->file('image_mapping_csv');
                $parsed_array = Excel::toArray([], $file);

                // Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');

                $success_count = 0;
                $error_count = 0;
                $errors = [];

                DB::beginTransaction();

                foreach ($imported_data as $key => $value) {
                    $row_no = $key + 2; // +2 because we removed header and array is 0-indexed

                    // Check if row has minimum required columns (SKU, Type, Name, Image File Name)
                    if (count($value) < 4) {
                        $error_count++;
                        $errors[] = "Row $row_no: Insufficient columns. Required: SKU, Type, Name, Image File Name";
                        continue;
                    }

                    $sku = trim($value[0]); // Column 0: SKU
                    // Column 1: Type (not used in import, but present in export)
                    // Column 2: Name (not used in import, but present in export)
                    $image_file_name = isset($value[3]) ? trim($value[3]) : ''; // Column 3: Image File Name

                    if (empty($sku)) {
                        $error_count++;
                        $errors[] = "Row $row_no: SKU is required";
                        continue;
                    }

                    // Try to find product by SKU
                    $product = Product::where('business_id', $business_id)
                        ->where('sku', $sku)
                        ->first();

                    if ($product) {
                        // Update product image
                        // If image_file_name is empty, leave empty (will use img/default.png as default)
                        // If image_file_name has value, update with that value
                        if (empty($image_file_name)) {
                            $product->image = ''; // Leave empty to use default img/default.png
                        } else {
                            $product->image = $image_file_name; // Update with new image file name
                        }
                        $product->save();
                        $success_count++;
                        continue;
                    }

                    // Try to find variation by sub_sku
                    $variation = Variation::whereHas('product', function($query) use ($business_id) {
                        $query->where('business_id', $business_id);
                    })
                    ->where('sub_sku', $sku)
                    ->first();

                    if ($variation) {
                        // Get existing media records before deleting
                        $existing_media = $variation->media()->get();
                        
                        // Delete existing media files from filesystem
                        foreach ($existing_media as $media) {
                            $media_file_path = public_path('uploads/media/' . $media->file_name);
                            if (file_exists($media_file_path)) {
                                @unlink($media_file_path);
                            }
                        }
                        
                        // Delete existing media from database
                        $variation->media()->delete();
                        
                        // If image_file_name is empty, leave database empty (don't create media record)
                        // If image_file_name has value, use that file name as-is (no formatting)
                        if (!empty($image_file_name)) {
                            $final_image_name = $image_file_name;
                            $media_path = public_path('uploads/media/');
                            $source_file = null;
                            
                            // Check if file exists in /uploads/media/ directory (where variation images are stored)
                            $media_file_path = $media_path . $final_image_name;
                            if (file_exists($media_file_path)) {
                                $source_file = $media_file_path;
                            } else {
                                // Check if file exists in /uploads/img/ directory (where product images are stored)
                                $img_path = public_path('uploads/img/' . $final_image_name);
                                if (file_exists($img_path)) {
                                    $source_file = $img_path;
                                }
                            }
                            
                            // If file exists in either location, ensure it's in /uploads/media/ with same name
                            if ($source_file) {
                                // Ensure media directory exists
                                if (!is_dir($media_path)) {
                                    mkdir($media_path, 0755, true);
                                }
                                
                                // Copy file to media directory with same name (if different from source)
                                $destination = $media_path . $final_image_name;
                                if ($source_file !== $destination) {
                                    copy($source_file, $destination);
                                }
                            }
                            
                            // Store file name as-is in database (no timestamp or formatting)
                            $media_obj = new Media([
                                'file_name' => $final_image_name,
                                'business_id' => $business_id,
                                'uploaded_by' => $user_id,
                            ]);
                            $variation->media()->save($media_obj);
                        }
                        // If image_file_name is empty, we've already deleted the media and files, so database is empty
                        $success_count++;
                        continue;
                    }

                    // If neither product nor variation found
                    $error_count++;
                    $errors[] = "Row $row_no: SKU '$sku' not found";
                }

                DB::commit();

                $output = [
                    'success' => 1,
                    'msg' => "Image mapping imported successfully. Updated: $success_count, Errors: $error_count",
                ];

                if ($error_count > 0 && count($errors) <= 20) {
                    $output['msg'] .= '<br><br>Errors:<br>' . implode('<br>', array_slice($errors, 0, 20));
                    if (count($errors) > 20) {
                        $output['msg'] .= '<br>... and ' . (count($errors) - 20) . ' more errors';
                    }
                }

            } else {
                $output = [
                    'success' => 0,
                    'msg' => 'Please upload a CSV file',
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage(),
            ];
        }

        return redirect('import-products')->with('notification', $output);
    }
}

