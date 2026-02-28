<?php

namespace App\Http\Controllers;

use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Exports\ProductsExport;
use App\Media;
use App\Product;
use App\ProductLocation;
use App\ProductVariation;
use App\PurchaseLine;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Unit;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Variation;
use App\VariationGroupPrice;
use App\VariationLocationDetails;
use App\VariationTemplate;
use App\VariationValueTemplate;
use App\Warranty;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Events\ProductsCreatedOrModified;
use App\LocationTaxType;
use App\Models\ProductGalleryImage;
use App\Models\ProductOrderLimit;
use App\TransactionSellLine;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('product.view') && ! auth()->user()->can('product.create')) { //      
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');

        if (request()->ajax()) {
            $location_id = request()->get('location_id', null);
            $permitted_locations = auth()->user()->permitted_locations();

        // Optimize price group subquery - pre-calculate and cache
        $priceGroups = DB::table('variation_group_prices as vgp')
        ->join('selling_price_groups as spg', 'vgp.price_group_id', '=', 'spg.id')
        ->select(
            'vgp.variation_id',
            DB::raw("MAX(CASE WHEN spg.name = 'SilverSellingPrice' THEN vgp.price_inc_tax ELSE NULL END) AS silver_price"),
            DB::raw("MAX(CASE WHEN spg.name = 'GoldSellingPrice' THEN vgp.price_inc_tax ELSE NULL END) AS gold_price"),
            DB::raw("MAX(CASE WHEN spg.name = 'PlatinumSellingPrice' THEN vgp.price_inc_tax ELSE NULL END) AS platinum_price")
        )
        ->groupBy('vgp.variation_id');
             // Build optimized base query with indexable columns
             $query = Product::select('products.id')
             ->with(['media' => function ($q) {
                 $q->select('id', 'model_id', 'model_type', 'file_name');
             }])
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->join('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('categories as c1', 'products.category_id', '=', 'c1.id')
                ->leftJoin('categories as c2', 'products.sub_category_id', '=', 'c2.id')
                ->leftJoin('tax_rates', 'products.tax', '=', 'tax_rates.id')
                ->join('variations as v', function ($join) {
                    $join->on('v.product_id', '=', 'products.id')
                        ->whereNull('v.deleted_at');
                    // Filter out discontinued variations if column exists
                    if (Schema::hasColumn('variations', 'is_discontinued')) {
                        $join->where(function($q) {
                            $q->where('v.is_discontinued', '!=', 1)
                              ->orWhereNull('v.is_discontinued');
                        });
                    }
                })
                ->leftJoin('variation_location_details as vld', function ($join) use ($permitted_locations, $location_id) {
                    $join->on('vld.variation_id', '=', 'v.id');
                    if ($permitted_locations != 'all') {
                        $join->whereIn('vld.location_id', $permitted_locations);
                    }
                    if (!empty($location_id) && $location_id != 'none') {
                        $join->where('vld.location_id', '=', $location_id);
                    }
                })
                ->leftJoinSub($priceGroups, 'pg', function ($join) {
                    $join->on('pg.variation_id', '=', 'v.id');
                })
                ->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier')
                ->whereNull('products.discontinue');

           

            // Apply active/inactive filters early
            $active_state = request()->get('active_state', null);
            if ($active_state == 'active') {
                $query->where('products.is_inactive', 0);
            }
            if ($active_state == 'inactive') {
                $query->where('products.is_inactive', 1);
            }

            // Apply not_for_selling filter early
            $not_for_selling = request()->get('not_for_selling', null);
            if ($not_for_selling == 'true') {
                $query->where('products.not_for_selling', 1);
            }

            // Apply additional filters early to reduce dataset size
            $type = request()->get('type', null);
            if (!empty($type)) {
                $query->where('products.type', $type);
            }

            $category_id = request()->get('category_id', null);
            if (!empty($category_id)) {
                $query->where('products.category_id', $category_id);
            }

            $brand_id = request()->get('brand_id', null);
            if (!empty($brand_id)) {
                $query->where('products.brand_id', $brand_id);
            }

            $unit_id = request()->get('unit_id', null);
            if (!empty($unit_id)) {
                $query->where('products.unit_id', $unit_id);
            }

            $tax_id = request()->get('tax_id', null);
            if (!empty($tax_id)) {
                $query->where('products.tax', $tax_id);
            }

            // Apply location filters
            if (!empty($location_id) && $location_id != 'none') {
                if ($permitted_locations == 'all' || in_array($location_id, $permitted_locations)) {
                    $query->whereHas('product_locations', function ($query) use ($location_id) {
                        $query->where('product_locations.location_id', '=', $location_id);
                    });
                }
            } elseif ($location_id == 'none') {
                $query->doesntHave('product_locations');
            } else {
                if ($permitted_locations != 'all') {
                    $query->whereHas('product_locations', function ($query) use ($permitted_locations) {
                        $query->whereIn('product_locations.location_id', $permitted_locations);
                    });
                }
            }

            if ($is_woocommerce) {
                $woocommerce_enabled = request()->get('woocommerce_enabled', 0);
                if ($woocommerce_enabled == 1) {
                    $query->where('products.woocommerce_disable_sync', 0);
                }
            }

            if (!empty(request()->get('repair_model_id'))) {
                $query->where('products.repair_model_id', request()->get('repair_model_id'));
            }

            // Use subquery for filtered product IDs instead of loading all IDs into memory (fixes slow datatable load)
            $productIdsSubquery = (clone $query)->distinct()->select('products.id');

            // Now get the actual data with the filtered product IDs via subquery (DB optimizes; DataTables pagination applies here)
            $products = Product::with(['product_locations', 'vendors'])
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->join('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('categories as c1', 'products.category_id', '=', 'c1.id')
                ->leftJoin('categories as c2', 'products.sub_category_id', '=', 'c2.id')
                ->leftJoin('tax_rates', 'products.tax', '=', 'tax_rates.id')
                ->whereIn('products.id', $productIdsSubquery)
                ->select(
                    'products.id',
                    'products.name as product',
                    'products.type',
                    'c1.name as category',
                    'c2.name as sub_category',
                    'units.actual_name as unit',
                    'brands.name as brand',
                    'tax_rates.name as tax',
                    'products.sku',
                    'products.image',
                    'products.enable_stock',
                    'products.is_inactive',
                    'products.not_for_selling',
                    'products.alert_quantity',
                    'products.created_at',
                    'products.product_source_type'
                );

            if ($is_woocommerce) {
                $products->addSelect('woocommerce_disable_sync');
            }

            // Subquery for filtered product IDs (same as main list, avoids loading all IDs into memory)
            $filteredProductIdsSubquery = function () use ($query) {
                return (clone $query)->distinct()->select('products.id');
            };

            // Get variation data separately and join later for better performance
            $variationData = DB::table('variations as v')
                ->select(
                    'v.product_id',
                    DB::raw('MAX(v.sell_price_inc_tax) as max_price'),
                    DB::raw('MIN(v.sell_price_inc_tax) as min_price'),
                    DB::raw('MAX(v.dpp_inc_tax) as max_purchase_price'),
                    DB::raw('MIN(v.dpp_inc_tax) as min_purchase_price')
                )
                ->whereIn('v.product_id', $filteredProductIdsSubquery())
                ->whereNull('v.deleted_at');

            if (Schema::hasColumn('variations', 'is_discontinued')) {
                $variationData->where(function ($q) {
                    $q->where('v.is_discontinued', '!=', 1)->orWhereNull('v.is_discontinued');
                });
            }

            $variationData = $variationData->groupBy('v.product_id');

            // Get stock data separately
            $stockData = DB::table('variation_location_details as vld')
                ->join('variations as v', 'vld.variation_id', '=', 'v.id')
                ->select(
                    'v.product_id',
                    DB::raw('SUM(vld.qty_available) as current_stock')
                )
                ->whereIn('v.product_id', $filteredProductIdsSubquery())
                ->whereNull('v.deleted_at');

            if (Schema::hasColumn('variations', 'is_discontinued')) {
                $stockData->where(function ($q) {
                    $q->where('v.is_discontinued', '!=', 1)->orWhereNull('v.is_discontinued');
                });
            }

            $stockData = $stockData->when($location_id && $location_id != 'none', function ($query) use ($location_id) {
                    return $query->where('vld.location_id', $location_id);
                })
                ->when($permitted_locations != 'all', function ($query) use ($permitted_locations) {
                    return $query->whereIn('vld.location_id', $permitted_locations);
                })
                ->groupBy('v.product_id');

            // Get price group data
            $priceGroupData = DB::table('variations as v')
                ->leftJoinSub($priceGroups, 'pg', function ($join) {
                    $join->on('pg.variation_id', '=', 'v.id');
                })
                ->select(
                    'v.product_id',
                    DB::raw('MAX(pg.silver_price) as silver_price'),
                    DB::raw('MAX(pg.gold_price) as gold_price'),
                    DB::raw('MAX(pg.platinum_price) as platinum_price')
                )
                ->whereIn('v.product_id', $filteredProductIdsSubquery())
                ->whereNull('v.deleted_at');

            if (Schema::hasColumn('variations', 'is_discontinued')) {
                $priceGroupData->where(function ($q) {
                    $q->where('v.is_discontinued', '!=', 1)->orWhereNull('v.is_discontinued');
                });
            }

            $priceGroupData = $priceGroupData->groupBy('v.product_id');

            // Join all the data
            $products->leftJoinSub($variationData, 'vd', function ($join) {
                $join->on('products.id', '=', 'vd.product_id');
            });

            $products->leftJoinSub($stockData, 'sd', function ($join) {
                $join->on('products.id', '=', 'sd.product_id');
            });

            $products->leftJoinSub($priceGroupData, 'pgd', function ($join) {
                $join->on('products.id', '=', 'pgd.product_id');
            });

            // Add joined columns to select
            $products->addSelect([
                'sd.current_stock',
                'vd.max_price',
                'vd.min_price',
                'vd.max_purchase_price',
                'vd.min_purchase_price',
                'pgd.silver_price',
                'pgd.gold_price',
                'pgd.platinum_price'
            ]);

            return Datatables::of($products)
                ->addColumn(
                    'product_locations',
                    function ($row) {
                        return $row->product_locations->implode('name', ', ');
                    }
                )
                ->editColumn('created_at', function ($product) {
                    return Carbon::parse($product->created_at)->format('Y-m-d H:i:s');
                })
                ->editColumn('category', '{{$category}} @if(!empty($sub_category))<br/> -- {{$sub_category}}@endif')
                ->addColumn(
                    'action',
                    function ($row) use ($selling_price_group_count) {
                        $html =
                            '<div class="btn-group dropdown scroll-safe-dropdown"><button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max dropdown-toggle" data-toggle="dropdown" aria-expanded="false">' . __('messages.actions') . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu"><li><a href="' . action([\App\Http\Controllers\LabelsController::class, 'show']) . '?product_id=' . $row->id . '" class="barcode-labels-product" data-toggle="tooltip" title="' . __('lang_v1.label_help') . '"><i class="fa fa-barcode"></i> ' . __('barcode.labels') . '</a></li>';

                        if (auth()->user()->can('product.view')) {
                            $html .=
                                '<li><a href="' . action([\App\Http\Controllers\ProductController::class, 'view'], [$row->id]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __('messages.view') . '</a></li>';
                            $html .=
                                '<li><a href="#" class="view-variants" data-product-id="' . $row->id . '"><i class="fa fa-sitemap"></i> View Variants</a></li>';
                        }

                        if (auth()->user()->can('product.update')) {
                            $html .=
                                '<li><a href="' . action([\App\Http\Controllers\ProductController::class, 'edit'], [$row->id]) . '" class="edit-product"><i class="glyphicon glyphicon-edit"></i> ' . __('messages.edit') . '</a></li>';
                        }

                        if (auth()->user()->can('product.delete')) {
                            $html .=
                                '<li><a href="' . action([\App\Http\Controllers\ProductController::class, 'destroy'], [$row->id]) . '" class="delete-product"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</a></li>';
                        }

                        if ($row->is_inactive == 1) {
                            $html .=
                                '<li><a href="' . action([\App\Http\Controllers\ProductController::class, 'activate'], [$row->id]) . '" class="activate-product"><i class="fas fa-check-circle"></i> ' . __('lang_v1.reactivate') . '</a></li>';
                        }else{
                            $html .=
                            '<li><a href="' . action([\App\Http\Controllers\ProductController::class, 'deactivate'], [$row->id]) . '" class="deactivate-product"><i class="fas fa-times-circle"></i> Deactivate</a></li>';
                        }
                        $html .='<li><a href="' . action([\App\Http\Controllers\ProductController::class, 'discontinue'], [$row->id]) . '" class="discontinue-product"><i class="fas fa-times-circle"></i> Discontinue</a></li>';
                    

                        $html .= '<li class="divider"></li>';

                        if (auth()->user()->can('product.view')) {
                            $html .=
                                '<li><a href="' . action([\App\Http\Controllers\ProductController::class, 'productStockHistory'], [$row->id]) . '" class="view-stock-history"><i class="fas fa-history"></i> ' . __('lang_v1.product_stock_history') . '</a></li>';
                        }

                        // if (auth()->user()->can('product.create')) {
                        //     if ($selling_price_group_count > 0) {
                        //         $html .=
                        //             '<li><a href="' . action([\App\Http\Controllers\ProductController::class, 'addSellingPrices'], [$row->id]) . '"><i class="fas fa-money-bill-alt"></i> ' . __('lang_v1.add_selling_price_group_prices') . '</a></li>';
                        //     }

                        //     $html .=
                        //         '<li><a href="' . action([\App\Http\Controllers\ProductController::class, 'create'], ['d' => $row->id]) . '"><i class="fa fa-copy"></i> ' . __('lang_v1.duplicate_product') . '</a></li>';
                        // }

                        if (! empty($row->media->first())) {
                            $html .=
                                '<li><a href="' . $row->media->first()->display_url . '" class="download-brochure" download="' . $row->media->first()->display_name . '"><i class="fas fa-download"></i> ' . __('lang_v1.product_brochure') . '</a></li>';
                        }

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->editColumn('product', function ($row) use ($is_woocommerce) {
                    $product = $row->is_inactive == 1 ? $row->product . ' <span class="label bg-gray">' . __('lang_v1.inactive') . '</span>' : $row->product;

                    $product = $row->not_for_selling == 1 ? $product . ' <span class="label bg-gray">' . __('lang_v1.not_for_selling') .
                        '</span>' : $product;

                    // if ($is_woocommerce && isset($row->woocommerce_disable_sync) && ! $row->woocommerce_disable_sync) {
                    //     $product = $product . '<br><i class="fab fa-wordpress"></i>';
                    // }

                    return $product;
                })
                ->editColumn('image', function ($row) {
                    return '<div style="display: flex;"><img src="' . $row->image_url . '" alt="Product image" class="product-thumbnail-small"></div>';
                })
                ->editColumn('type', '@lang("lang_v1." . $type)')
                ->addColumn('mass_delete', function ($row) {
                    return  '<input type="checkbox" class="row-select" value="' . $row->id . '">';
                })
                ->editColumn('current_stock', function ($row) {
                    if ($row->enable_stock) {
                        $stock = $this->productUtil->num_f($row->current_stock, false, null, true);
                        return $stock . ' ';
                    } else {
                        return '--';
                    }
                })
                ->addColumn(
                    'purchase_price',
                    '<div style="white-space: nowrap;">@format_currency($min_purchase_price) @if($max_purchase_price != $min_purchase_price && $type == "variable") -  @format_currency($max_purchase_price)@endif </div>'
                )
                ->addColumn(
                    'selling_price',
                    '<div style="white-space: nowrap;">@format_currency($min_price) @if($max_price != $min_price && $type == "variable") -  @format_currency($max_price)@endif </div>'
                )
                ->editColumn('silver_price', function ($row) {
                    return '$' . number_format((float)$row->silver_price, 2, '.', '');
                })
                ->addColumn('fulfillment_type', function ($row) {
                    $sourceType = $row->product_source_type ?? 'in_house';
                    if ($sourceType === 'dropshipped') {
                        // Check vendor type
                        $vendor = $row->vendors->first();
                        if ($vendor) {
                            if ($vendor->vendor_type === 'woocommerce') {
                                return '<span class="label bg-blue"><i class="fas fa-globe"></i> Dropship - WooCommerce</span><br><small>' . e($vendor->name) . '</small>';
                            } else {
                                return '<span class="label bg-purple"><i class="fas fa-user-tie"></i> Dropship - ERP</span><br><small>' . e($vendor->name) . '</small>';
                            }
                        }
                        return '<span class="label bg-orange"><i class="fas fa-truck"></i> Dropship</span>';
                    }
                    return '<span class="label bg-green"><i class="fas fa-warehouse"></i> In-House</span>';
                })
                ->filterColumn('products.sku', function ($query, $keyword) {
                    $query->whereHas('variations', function ($q) use ($keyword) {
                        $q->where('sub_sku', 'like', "%{$keyword}%")
                            ->orWhere('var_barcode_no', 'like', "%{$keyword}%");
                    })
                        ->orWhere('products.sku', 'like', "%{$keyword}%");
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('product.view')) {
                            return action([\App\Http\Controllers\ProductController::class, 'view'], [$row->id]);
                        } else {
                            return '';
                        }
                    },
                ])
                ->rawColumns(['action', 'image', 'mass_delete', 'product', 'selling_price', 'purchase_price', 'category', 'current_stock', 'fulfillment_type'])
                ->make(true);
        }

        $rack_enabled = (request()->session()->get('business.enable_racks') || request()->session()->get('business.enable_row') || request()->session()->get('business.enable_position'));
        $location_id = $this->getLocationId(request());
        $categories = Category::forDropdown($business_id, 'product', $location_id);

        $brands = Brands::forDropdown($business_id, false, false, $location_id);

        $units = Unit::forDropdown($business_id);

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, false);
        $taxes = $tax_dropdown['tax_rates'];

        $business_locations = BusinessLocation::forDropdown($business_id);
        $business_locations->prepend(__('lang_v1.none'), 'none');

        if ($this->moduleUtil->isModuleInstalled('Manufacturing') && (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module'))) {
            $show_manufacturing_data = true;
        } else {
            $show_manufacturing_data = false;
        }

        //list product screen filter from module
        $pos_module_data = $this->moduleUtil->getModuleData('get_filters_for_list_product_screen');

        $is_admin = $this->productUtil->is_admin(auth()->user());

        return view('product.index')
            ->with(compact(
                'rack_enabled',
                'categories',
                'brands',
                'units',
                'taxes',
                'business_locations',
                'show_manufacturing_data',
                'pos_module_data',
                'is_woocommerce',
                'is_admin'
            ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not, then check for products quota
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        } elseif (! $this->moduleUtil->isQuotaAvailable('products', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('products', $business_id, action([\App\Http\Controllers\ProductController::class, 'index']));
        }

        $location_id = $this->getLocationId(request());
        $categories = Category::forDropdown($business_id, 'product', $location_id);

        $brands = Brands::forDropdown($business_id, false, false, $location_id);
        $units = Unit::forDropdown($business_id, true);

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        $taxes = $tax_dropdown['tax_rates'];
        $tax_attributes = $tax_dropdown['attributes'];

        $barcode_types = $this->barcode_types;
        $barcode_default = $this->productUtil->barcode_default();

        $default_profit_percent = request()->session()->get('business.default_profit_percent');

        //Get all business locations
        $business_locations = BusinessLocation::forDropdown($business_id);

        //Duplicate product
        $duplicate_product = null;
        $rack_details = null;

        $sub_categories = [];
        if (! empty(request()->input('d'))) {
            $duplicate_product = Product::where('business_id', $business_id)->find(request()->input('d'));
            $duplicate_product->name .= ' (copy)';

            if (! empty($duplicate_product->category_id)) {
                $sub_categories = Category::where('business_id', $business_id)
                    ->where('parent_id', $duplicate_product->category_id)
                    ->pluck('name', 'id')
                    ->toArray();
            }

            //Rack details
            if (! empty($duplicate_product->id)) {
                $rack_details = $this->productUtil->getRackDetails($business_id, $duplicate_product->id);
            }
        }

        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);

        $module_form_parts = $this->moduleUtil->getModuleData('product_form_part');
        $product_types = $this->product_types();

        $common_settings = session()->get('business.common_settings');
        $warranties = Warranty::forDropdown($business_id); // Fetch tax types and prepare them as an associative array (id => name)

        //custom
        $taxTypes = LocationTaxType::pluck('name', 'id')->toArray();
        $visbility = new Util();
        $productVisibility = $visbility->productVisibility();
        $catListQuery = Category::where('business_id', $business_id)
            ->where('category_type', 'product');
        if ($location_id) {
            $location = BusinessLocation::find($location_id);
            if ($location) {
                $is_b2c = $location->is_b2c ?? false;
                $matching_location_ids = BusinessLocation::where('business_id', $business_id)
                    ->where('is_b2c', $is_b2c)
                    ->pluck('id')
                    ->toArray();
                if (!empty($matching_location_ids)) {
                    $catListQuery->whereIn('location_id', $matching_location_ids);
                } else {
                    $catListQuery->whereRaw('1 = 0');
                }
            } else {
                $catListQuery->where('location_id', $location_id);
            }
        }
        
        $catList = $catListQuery->pluck('name', 'id')->toArray();
        //product screen view from module
        $pos_module_data = $this->moduleUtil->getModuleData('get_product_screen_top_view');
        
        // Check B2B access for selling price group button
        $has_b2b_access = $this->hasB2BAccess();

        return view('product.create')
            ->with(compact('categories', 'taxTypes', 'catList', 'productVisibility', 'brands', 'units', 'taxes', 'barcode_types', 'default_profit_percent', 'tax_attributes', 'barcode_default', 'business_locations', 'duplicate_product', 'sub_categories', 'rack_details', 'selling_price_group_count', 'module_form_parts', 'product_types', 'common_settings', 'warranties', 'pos_module_data', 'has_b2b_access'));
    }

    private function product_types()
    {
        //Product types also includes modifier.
        return [
            'single' => __('lang_v1.single'),
            'variable' => __('lang_v1.variable'),
            'combo' => __('lang_v1.combo'),
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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

    private function uploadGalleryImages($file , $prefix = null)
    {
        $uploaded_file_name = null;
        // Check if file is valid and is an image
        if ($file && $file->isValid()) {
            if (strpos($file->getMimeType(), 'image/') === false) {
                throw new \Exception('Invalid image file');
            }

            if ($file->getSize() <= config('constants.document_size_limit')) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $basename = pathinfo($originalName, PATHINFO_FILENAME);
                $basename = Str::limit($basename, 100, ''); 
                $new_file_name = time().'_'.Str::slug($basename).'.'.$extension;
                if($prefix != null){
                    $new_file_name = $prefix.'_'.$new_file_name;
                }
                $destination_path = public_path('uploads/img/gallery');
                if (!file_exists($destination_path)) {
                    mkdir($destination_path, 0755, true);
                }
                if ($file->move($destination_path, $new_file_name)) {
                    $uploaded_file_name = 'uploads/img/gallery/' . $new_file_name;
                }
            }
        }

        return $uploaded_file_name;
    }

    /**
     * Sync product variations to vendor for Vendor Portal
     * This ensures variants appear in the Vendor Portal, not just the parent product
     */
    private function syncVariationsToVendor($product, $vendorId, $pivotData = [])
    {
        try {
            // Check if table exists
            if (!\Schema::hasTable('variation_vendor_pivot')) {
                \Log::info('variation_vendor_pivot table does not exist, skipping variant sync');
                return;
            }

            $vendor = \App\Models\WpVendor::find($vendorId);
            if (!$vendor) {
                \Log::warning('Vendor not found for variation sync', ['vendor_id' => $vendorId]);
                return;
            }

            $defaultMarkup = $vendor->default_markup_percentage ?? ($pivotData['vendor_markup_percentage'] ?? 0);
            $variations = $product->variations()->get();
            
            foreach ($variations as $variation) {
                \App\Models\VariationVendor::updateOrCreate(
                    [
                        'variation_id' => $variation->id,
                        'wp_vendor_id' => $vendorId,
                    ],
                    [
                        'product_id' => $product->id,
                        'markup_percentage' => $defaultMarkup,
                        'vendor_cost_price' => $pivotData['vendor_cost_price'] ?? null,
                        'vendor_stock_qty' => 0,
                        'status' => $pivotData['status'] ?? 'active',
                        'lead_time_days' => $pivotData['lead_time_days'] ?? 0,
                        'min_order_qty' => 1,
                    ]
                );
            }
            
            \Log::info('Product variations synced to vendor', [
                'product_id' => $product->id,
                'vendor_id' => $vendorId,
                'variations_synced' => $variations->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to sync variations to vendor', [
                'product_id' => $product->id,
                'vendor_id' => $vendorId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
            if (! auth()->user()->can('product.create')) { 

                abort(403, 'Unauthorized action.');
            }
            // dd($request->all());
            if ($request->input('type') == 'variable' && empty($request->input('product_variation'))) {
                $output = [
                    'success' => 0,
                    'msg' => 'Please add at least one variation',
                ];
                if ($request->ajax()) {
                    return response()->json($output);
                }
                return redirect()->back()->with('status', $output);
            }else if($request->input('type') == 'variable' && !empty($request->input('product_variation'))){
              $product_variation = $request->input('product_variation');
              foreach($product_variation as $variation){
                if(empty($variation['variations'])){
                    $output = [
                        'success' => 0,
                        'msg' => 'Please add variations for all variations',
                    ];
                    if ($request->ajax()) {
                        return response()->json($output);
                    }
                    return redirect()->back()->with('status', $output);
                }
              }
            }
            // dd($request->all());

        try {
            $business_id = $request->session()->get('user.business_id');
            $form_fields = [
                'name',
                'slug',
                'brand_id',
                'unit_id',
                'category_id',
                'tax',
                'type',
                'barcode_type',
                'sku',
                'alert_quantity',
                'tax_type',
                'weight',
                'length',
                'width',
                'height',
                'product_description',
                'product_warranty',
                'sub_unit_ids',
                'preparation_time_in_minutes',
                'product_custom_field1',
                'product_custom_field2',
                'product_custom_field3',
                'product_custom_field4',
                'product_custom_field5',
                'product_custom_field6',
                'product_custom_field7',
                'product_custom_field8',
                'product_custom_field9',
                'product_custom_field10',
                'product_custom_field11',
                'product_custom_field12',
                'product_custom_field13',
                'product_custom_field14',
                'product_custom_field15',
                'product_custom_field16',
                'product_custom_field17',
                'product_custom_field18',
                'product_custom_field19',
                'product_custom_field20',
                'productVisibility',
                'ml',
                'ct',
                'locationTaxType',
                'maxSaleLimit',
                'barcode_no',
                'enable_selling',
                'is_tobacco_product',
                'is_gift_card',
                'gift_card_expires_at',
                'gift_card_stock',
                'custom_sub_categories'
            ];

            $module_form_fields = $this->moduleUtil->getModuleFormField('product_form_fields');
            if (! empty($module_form_fields)) {
                $form_fields = array_merge($form_fields, $module_form_fields);
            }
            // Ensure locationTaxType is an array
            $product_details['locationTaxType'] = $request->input('locationTaxType', []);
            $product_details['custom_sub_categories'] = $request->input('custom_sub_categories', []);
            // if ($request->has('locationTaxType')) {
            //     $product_details['locationTaxType'] = $request->input('locationTaxType');
            // }
            $product_details = $request->only($form_fields);
            $product_details['business_id'] = $business_id;
            $product_details['created_by'] = $request->session()->get('user.id');
            $product_details['slug'] = $this->slugMaker($request->input('name'));
            
            // Ensure weight and dimensions are always included and normalized (empty strings to null)
            // Handle weight exactly like length, width, height - always get from request, even if 0
            // Note: 0 is a valid value and should be saved, only null/empty string should be converted to null
            // Override any values from $request->only() to ensure we always have the latest values
            $weightInput = $request->input('weight');
            if ($weightInput === '' || $weightInput === null) {
                $product_details['weight'] = null;
            } else {
                $product_details['weight'] = $weightInput; // This includes 0 as a valid value
            }
            
            $lengthInput = $request->input('length');
            if ($lengthInput === '' || $lengthInput === null) {
                $product_details['length'] = null;
            } else {
                $product_details['length'] = $lengthInput;
            }
            
            $widthInput = $request->input('width');
            if ($widthInput === '' || $widthInput === null) {
                $product_details['width'] = null;
            } else {
                $product_details['width'] = $widthInput;
            }
            
            $heightInput = $request->input('height');
            if ($heightInput === '' || $heightInput === null) {
                $product_details['height'] = null;
            } else {
                $product_details['height'] = $heightInput;
            }

            $product_details['enable_stock'] = (! empty($request->input('enable_stock')) && $request->input('enable_stock') == 1) ? 1 : 0;
            $product_details['not_for_selling'] = (! empty($request->input('not_for_selling')) && $request->input('not_for_selling') == 1) ? 1 : 0;
            $product_details['is_gift_card'] = $request->boolean('is_gift_card');
            if ($product_details['is_gift_card']) {
                $product_details['gift_card_expires_at'] = $request->input('gift_card_expires_at') ?: null;
                $product_details['gift_card_stock'] = $request->input('gift_card_stock') !== null && $request->input('gift_card_stock') !== '' ? $this->productUtil->num_uf($request->input('gift_card_stock')) : null;
            } else {
                $product_details['gift_card_expires_at'] = null;
                $product_details['gift_card_stock'] = null;
            }

            if (! empty($request->input('sub_category_id'))) {
                $product_details['sub_category_id'] = $request->input('sub_category_id');
            }
            if (! empty($request->input('secondary_unit_id'))) {
                $product_details['secondary_unit_id'] = $request->input('secondary_unit_id');
            }

            if (empty($product_details['sku'])) {
                $product_details['sku'] = ' ';
            }
            // if (empty($product_details['barcode_no'])) {
            //     $product_details['barcode_no'] = ' ';
            // }

            if (! empty($product_details['alert_quantity'])) {
                $product_details['alert_quantity'] = $this->productUtil->num_uf($product_details['alert_quantity']);
            }

            $expiry_enabled = $request->session()->get('business.enable_product_expiry');
            if (! empty($request->input('expiry_period_type')) && ! empty($request->input('expiry_period')) && ! empty($expiry_enabled) && ($product_details['enable_stock'] == 1)) {
                $product_details['expiry_period_type'] = $request->input('expiry_period_type');
                $product_details['expiry_period'] = $this->productUtil->num_uf($request->input('expiry_period'));
            }

            if (! empty($request->input('enable_sr_no')) && $request->input('enable_sr_no') == 1) {
                $product_details['enable_sr_no'] = 1;
            }

            //upload document
            $product_details['image'] = $this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'), 'image', $product_details['sku'] ?? null);
            $common_settings = session()->get('business.common_settings');
            $product_details['warranty_id'] = ! empty($request->input('warranty_id')) ? $request->input('warranty_id') : null;

            DB::beginTransaction();

            $product = Product::create($product_details);
            if (!empty($product_details['custom_sub_categories'])) {
                $product_details['custom_sub_categories'] = array_filter($product_details['custom_sub_categories'], function ($value) {
                    return !is_null($value) && $value !== '';
                });
                if (!empty($product_details['custom_sub_categories'])) {
                    // Log::info($request->input('custom_sub_categories'));
                    $product->webcategories()->attach($product_details['custom_sub_categories']);
                }
            }
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $image) {
                    $path = $this->uploadGalleryImages($image, $product->sku);
                    if ($path) {
                        ProductGalleryImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                        ]);
                    }
                }
            }
            event(new ProductsCreatedOrModified($product_details, 'added'));

            if (empty(trim($request->input('sku')))) {
                //Brand+Product name first digits + product id
                $brandName = $product->brand ? $product->brand->name : '';
                $productName = $product->name;
                $skuFirstDigits = ' ';
                // Safely get first 3 characters of brand name
                if (!empty($brandName)) {
                    for($i = 0; $i < 3 && $i < strlen($brandName); $i++){
                        $skuFirstDigits .= $brandName[$i];
                    }
                }
                // Safely get first 3 characters of product name
                for($i = 0; $i < 3 && $i < strlen($productName); $i++){
                    $skuFirstDigits .= $productName[$i];
                }

                $productId = $product->id;
                $sku = $this->productUtil->generateProductSku($product->id);
                $product->sku = $sku;
                $product->save();
            }
            // if (empty(trim($request->input('barcode_no')))) {
            //     $barcode_no = $this->productUtil->generateProductSku($product->id);
            //     $product->barcode_no = $barcode_no;
            //     $product->save();
            // }
            //Add product locations
            $product_locations = $request->input('product_locations');
            if (! empty($product_locations)) {
                $product->product_locations()->sync($product_locations);
            }

            if ($product->type == 'single') {
                $this->productUtil->createSingleProductVariation($product->id, $product->sku, $request->input('single_dpp'), $request->input('single_dpp_inc_tax'), $request->input('profit_percent'), $request->input('single_dsp'), $request->input('single_dsp_inc_tax'), null, $request->input('barcode_no'));
            } elseif ($product->type == 'variable') {
                if (! empty($request->input('product_variation'))) {
                    $input_variations = $request->input('product_variation');

                    $this->productUtil->createVariableProductVariations($product->id, $input_variations, $request->input('sku_type'));
                }
            } elseif ($product->type == 'combo') {

                //Create combo_variations array by combining variation_id and quantity.
                $combo_variations = [];
                if (! empty($request->input('composition_variation_id'))) {
                    $composition_variation_id = $request->input('composition_variation_id');
                    $quantity = $request->input('quantity');
                    $unit = $request->input('unit');

                    foreach ($composition_variation_id as $key => $value) {
                        $combo_variations[] = [
                            'variation_id' => $value,
                            'quantity' => $this->productUtil->num_uf($quantity[$key]),
                            'unit_id' => $unit[$key],
                        ];
                    }
                }

                $this->productUtil->createSingleProductVariation($product->id, $product->sku, $request->input('item_level_purchase_price_total'), $request->input('purchase_price_inc_tax'), $request->input('profit_percent'), $request->input('selling_price'), $request->input('selling_price_inc_tax'), $combo_variations);
            }

            //Add product racks details.
            $product_racks = $request->get('product_racks', null);
            if (! empty($product_racks)) {
                $this->productUtil->addRackDetails($business_id, $product->id, $product_racks);
            }

            //Set Module fields
            if (! empty($request->input('has_module_data'))) {
                $this->moduleUtil->getModuleData('after_product_saved', ['product' => $product, 'request' => $request]);
            }

            Media::uploadMedia($product->business_id, $product, $request, 'product_brochure', true);

            // Handle state restrictions
            $this->saveProductStates($product->id, $request);

            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('product.product_added_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];

            return redirect('products')->with('status', $output);
        }

        if ($request->input('submit_type') == 'submit_n_add_opening_stock') {
            return redirect()->action(
                [\App\Http\Controllers\OpeningStockController::class, 'add'],
                ['product_id' => $product->id]
            );
        } elseif ($request->input('submit_type') == 'submit_n_add_selling_prices') {
            return redirect()->action(
                [\App\Http\Controllers\ProductController::class, 'addSellingPrices'],
                [$product->id]
            );
        } elseif ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action(
                [\App\Http\Controllers\ProductController::class, 'create']
            )->with('status', $output);
        }

        return redirect('products')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $details = $this->productUtil->getRackDetails($business_id, $id, true);

        return view('product.show')->with(compact('details'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $location_id = $this->getLocationId(request());
        $categories = Category::forDropdown($business_id, 'product', $location_id);
        $brands = Brands::forDropdown($business_id, false, false, $location_id);

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        $taxes = $tax_dropdown['tax_rates'];
        $tax_attributes = $tax_dropdown['attributes'];

        $barcode_types = $this->barcode_types;

        $product = Product::where('business_id', $business_id)
            ->with(['product_locations', 'webcategories', 'vendors'])
            ->where('id', $id)
            ->firstOrFail();

        //Sub-category
        $sub_categories = [];
        $sub_categories = Category::where('business_id', $business_id)
            ->where('parent_id', $product->category_id)
            ->pluck('name', 'id')
            ->toArray();
        $sub_categories = ['' => 'None'] + $sub_categories;

        $default_profit_percent = request()->session()->get('business.default_profit_percent');

        //Get units.
        $units = Unit::forDropdown($business_id, true);
        $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit_id, true);

        //Get all business locations
        $business_locations = BusinessLocation::forDropdown($business_id);
        //Rack details
        $rack_details = $this->productUtil->getRackDetails($business_id, $id);

        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);

        $module_form_parts = $this->moduleUtil->getModuleData('product_form_part');
        $product_types = $this->product_types();
        $common_settings = session()->get('business.common_settings');
        $warranties = Warranty::forDropdown($business_id);

        //product screen view from module
        $pos_module_data = $this->moduleUtil->getModuleData('get_product_screen_top_view');

        $alert_quantity = ! is_null($product->alert_quantity) ? $this->productUtil->num_f($product->alert_quantity, false, null, true) : null;

        //custom
        $taxTypes = LocationTaxType::pluck('name', 'id')->toArray();
        $visbility = new Util();
        $productVisibility = $visbility->productVisibility();
        
        // Filter web categories based on B2B/B2C
        $catListQuery = Category::where('business_id', $business_id)
            ->where('category_type', 'product');
        $edit_location_id = $this->getLocationId(request());
        if ($edit_location_id) {
            $location = BusinessLocation::find($edit_location_id);
            if ($location) {
                $is_b2c = $location->is_b2c ?? false;
                $matching_location_ids = BusinessLocation::where('business_id', $business_id)
                    ->where('is_b2c', $is_b2c)
                    ->pluck('id')
                    ->toArray();
                if (!empty($matching_location_ids)) {
                    $catListQuery->whereIn('location_id', $matching_location_ids);
                } else {
                    $catListQuery->whereRaw('1 = 0');
                }
            } else {
                $catListQuery->where('location_id', $edit_location_id);
            }
        }
        
        $catList = $catListQuery->pluck('name', 'id')->toArray();


        return view('product.edit')
            ->with(compact('categories', 'taxTypes', 'catList', 'productVisibility', 'brands', 'units', 'sub_units', 'taxes', 'tax_attributes', 'barcode_types', 'product', 'sub_categories', 'default_profit_percent', 'business_locations', 'rack_details', 'selling_price_group_count', 'module_form_parts', 'product_types', 'common_settings', 'warranties', 'pos_module_data', 'alert_quantity'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response | mixed
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $product_details = $request->only([
                'name',
                'slug',
                'brand_id',
                'unit_id',
                'category_id',
                'tax',
                'barcode_type',
                'sku',
                'alert_quantity',
                'tax_type',
                'weight',
                'product_description',
                'product_warranty',
                'sub_unit_ids',
                'preparation_time_in_minutes',
                'product_custom_field1',
                'product_custom_field2',
                'product_custom_field3',
                'product_custom_field4',
                'product_custom_field5',
                'product_custom_field6',
                'product_custom_field7',
                'product_custom_field8',
                'product_custom_field9',
                'product_custom_field10',
                'product_custom_field11',
                'product_custom_field12',
                'product_custom_field13',
                'product_custom_field14',
                'product_custom_field15',
                'product_custom_field16',
                'product_custom_field17',
                'product_custom_field18',
                'product_custom_field19',
                'product_custom_field20',
                'productVisibility',
                'ml',
                'ct',
                'locationTaxType',
                'maxSaleLimit',
                'barcode_no',
                'enable_selling',
                'is_tobacco_product',
                'is_gift_card',
                'gift_card_expires_at',
                'gift_card_stock',
                'custom_sub_categories'
            ]);

            DB::beginTransaction();

            $product = Product::where('business_id', $business_id)
                ->where('id', $id)
                ->with(['product_variations'])
                ->first();

            $module_form_fields = $this->moduleUtil->getModuleFormField('product_form_fields');
            if (! empty($module_form_fields)) {
                foreach ($module_form_fields as $column) {
                    $product->$column = $request->input($column);
                }
            }
            $product_details['custom_sub_categories'] = $request->input('custom_sub_categories', []);
            $product->name = $product_details['name'];

            $product->slug  = $this->slugMaker($product_details['name'], $id);
            $product->brand_id = $product_details['brand_id'];
            $product->unit_id = $product_details['unit_id'];
            $product->category_id = $product_details['category_id'];
            $product->barcode_type = $product_details['barcode_type'];
            $product->productVisibility = $product_details['productVisibility'];
            $product->locationTaxType = $product_details['locationTaxType'];
            $product->ml = $product_details['ml'];
            $product->ct = $product_details['ct'];
            $product->tax = $product_details['tax'];
            $product->sku = $product_details['sku'];
            if ($product->maxSaleLimit != $product_details['maxSaleLimit']) {
                $this->updateCartItemsForNewLimit($product->id, $product->id, $product_details['maxSaleLimit']);
            }
            $product->maxSaleLimit = $product_details['maxSaleLimit'];
            // $product->barcode_no = $product_details['barcode_no'];
            $product->enable_selling = $product_details['enable_selling'] ?? false;
            $product->is_tobacco_product = $product_details['is_tobacco_product'] ?? false;
            $product->is_gift_card = $product_details['is_gift_card'] ?? false;
            if ($product->is_gift_card) {
                $product->gift_card_expires_at = $product_details['gift_card_expires_at'] ?? null;
                $product->gift_card_stock = isset($product_details['gift_card_stock']) && $product_details['gift_card_stock'] !== '' ? $this->productUtil->num_uf($product_details['gift_card_stock']) : null;
            } else {
                $product->gift_card_expires_at = null;
                $product->gift_card_stock = null;
            }
            $product->custom_sub_categories = $product_details['custom_sub_categories'];
            $product->alert_quantity = ! empty($product_details['alert_quantity']) ? $this->productUtil->num_uf($product_details['alert_quantity']) : $product_details['alert_quantity'];
            $product->tax_type = $product_details['tax_type'] ?? $product->tax_type;

            // Weight & dimensions: always update if present in request (even if 0 or empty)
            // Get values directly from request - handle weight exactly like length, width, height
            // Note: 0 is a valid value and should be saved, only null/empty string should be converted to null
            $weightValue = $request->input('weight');
            if ($weightValue === '' || $weightValue === null) {
                $product->weight = null;
            } else {
                $product->weight = $weightValue; // This includes 0 as a valid value
            }
            
            $lengthValue = $request->input('length');
            if ($lengthValue === '' || $lengthValue === null) {
                $product->length = null;
            } else {
                $product->length = $lengthValue;
            }
            
            $widthValue = $request->input('width');
            if ($widthValue === '' || $widthValue === null) {
                $product->width = null;
            } else {
                $product->width = $widthValue;
            }
            
            $heightValue = $request->input('height');
            if ($heightValue === '' || $heightValue === null) {
                $product->height = null;
            } else {
                $product->height = $heightValue;
            }
            $product->product_custom_field1 = $product_details['product_custom_field1'] ?? '';
            $product->product_custom_field2 = $product_details['product_custom_field2'] ?? '';
            $product->product_custom_field3 = $product_details['product_custom_field3'] ?? '';
            $product->product_custom_field4 = $product_details['product_custom_field4'] ?? '';
            $product->product_custom_field5 = $product_details['product_custom_field5'] ?? '';
            $product->product_custom_field6 = $product_details['product_custom_field6'] ?? '';
            $product->product_custom_field7 = $product_details['product_custom_field7'] ?? '';
            $product->product_custom_field8 = $product_details['product_custom_field8'] ?? '';
            $product->product_custom_field9 = $product_details['product_custom_field9'] ?? '';
            $product->product_custom_field10 = $product_details['product_custom_field10'] ?? '';
            $product->product_custom_field11 = $product_details['product_custom_field11'] ?? '';
            $product->product_custom_field12 = $product_details['product_custom_field12'] ?? '';
            $product->product_custom_field13 = $product_details['product_custom_field13'] ?? '';
            $product->product_custom_field14 = $product_details['product_custom_field14'] ?? '';
            $product->product_custom_field15 = $product_details['product_custom_field15'] ?? '';
            $product->product_custom_field16 = $product_details['product_custom_field16'] ?? '';
            $product->product_custom_field17 = $product_details['product_custom_field17'] ?? '';
            $product->product_custom_field18 = $product_details['product_custom_field18'] ?? '';
            $product->product_custom_field19 = $product_details['product_custom_field19'] ?? '';
            $product->product_custom_field20 = $product_details['product_custom_field20'] ?? '';

            $product->product_description = $product_details['product_description'];
            $product->product_warranty = $product_details['product_warranty'] ?? null;
            $product->sub_unit_ids = ! empty($product_details['sub_unit_ids']) ? $product_details['sub_unit_ids'] : null;
            $product->preparation_time_in_minutes = $product_details['preparation_time_in_minutes'];
            $product->warranty_id = ! empty($request->input('warranty_id')) ? $request->input('warranty_id') : null;
            $product->secondary_unit_id = ! empty($request->input('secondary_unit_id')) ? $request->input('secondary_unit_id') : null;

            if (! empty($request->input('enable_stock')) && $request->input('enable_stock') == 1) {
                $product->enable_stock = 1;
            } else {
                $product->enable_stock = 0;
            }

            $product->not_for_selling = (! empty($request->input('not_for_selling')) && $request->input('not_for_selling') == 1) ? 1 : 0;

            if (! empty($request->input('sub_category_id'))) {
                $product->sub_category_id = $request->input('sub_category_id');
            } else {
                $product->sub_category_id = null;
            }

            $expiry_enabled = $request->session()->get('business.enable_product_expiry');
            if (! empty($expiry_enabled)) {
                if (! empty($request->input('expiry_period_type')) && ! empty($request->input('expiry_period')) && ($product->enable_stock == 1)) {
                    $product->expiry_period_type = $request->input('expiry_period_type');
                    $product->expiry_period = $this->productUtil->num_uf($request->input('expiry_period'));
                } else {
                    $product->expiry_period_type = null;
                    $product->expiry_period = null;
                }
            }

            if (! empty($request->input('enable_sr_no')) && $request->input('enable_sr_no') == 1) {
                $product->enable_sr_no = 1;
            } else {
                $product->enable_sr_no = 0;
            }

            //upload document
            $file_name = $this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'), 'image', $product->sku);
            if (! empty($file_name)) {

                //If previous image found then remove
                if (! empty($product->image_path) && file_exists($product->image_path)) {
                    unlink($product->image_path);
                }

                $product->image = $file_name;
                //If product image is updated update woocommerce media id
                if (! empty($product->woocommerce_media_id)) {
                    $product->woocommerce_media_id = null;
                }
            }

            // Handle Dropship Settings
            if ($request->has('product_source_type')) {
                $product->product_source_type = $request->input('product_source_type');
            }

            $product->save();
            $product->touch();

            // Handle Dropship Vendor Mapping
            if ($request->input('product_source_type') === 'dropshipped' && $request->filled('dropship_vendor_id')) {
                $vendorId = $request->input('dropship_vendor_id');
                
                // Prepare pivot data
                $pivotData = [
                    'vendor_cost_price' => $request->input('vendor_cost_price'),
                    'vendor_markup_percentage' => $request->input('vendor_markup_percentage'),
                    'dropship_selling_price' => $request->input('dropship_selling_price'),
                    'vendor_sku' => $request->input('vendor_sku'),
                    'lead_time_days' => $request->input('lead_time_days', 0),
                    'is_primary_vendor' => true,
                    'status' => 'active',
                ];
                
                // Sync the vendor relationship (detach old, attach new with pivot data)
                $product->vendors()->sync([$vendorId => $pivotData]);
                
                // Also sync variation-level mappings for Vendor Portal
                $this->syncVariationsToVendor($product, $vendorId, $pivotData);
                
                \Log::info('Dropship vendor mapping updated with variations', [
                    'product_id' => $product->id,
                    'vendor_id' => $vendorId,
                    'variations_count' => $product->variations()->count(),
                    'pivot_data' => $pivotData
                ]);
            } elseif ($request->input('product_source_type') === 'in_house') {
                // If switching back to in-house, remove vendor mappings
                $product->vendors()->detach();
                
                // Also remove variation-level mappings
                try {
                    \App\Models\VariationVendor::where('product_id', $product->id)->delete();
                } catch (\Exception $e) {
                    // Table might not exist
                }
                
                \Log::info('Product changed to in-house, vendor mappings removed', ['product_id' => $product->id]);
            }

            // Handle gallery images
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $image) {
                    $path = $this->uploadGalleryImages($image, $product->sku);
                    if ($path) {
                        ProductGalleryImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                        ]);
                    }
                }
            }

            event(new ProductsCreatedOrModified($product, 'updated'));

            //Add product locations
            $product_locations = ! empty($request->input('product_locations')) ?
                $request->input('product_locations') : [];

            $permitted_locations = auth()->user()->permitted_locations();
            //If not assigned location exists don't remove it
            if ($permitted_locations != 'all') {
                $existing_product_locations = $product->product_locations()->pluck('id');

                foreach ($existing_product_locations as $pl) {
                    if (! in_array($pl, $permitted_locations)) {
                        $product_locations[] = $pl;
                    }
                }
            }

            $product->product_locations()->sync($product_locations);
            $product_details['custom_sub_categories'] = array_filter($product_details['custom_sub_categories'], function ($value) {
                return !is_null($value) && $value !== '';
            });
            if (!empty($product_details['custom_sub_categories'])) {
                $product->webcategories()->sync($product_details['custom_sub_categories']);
            }
            if ($product->type == 'single') {
                $single_data = $request->only(['single_variation_id', 'single_dpp', 'single_dpp_inc_tax', 'single_dsp_inc_tax', 'profit_percent', 'single_dsp','var_barcode_no']);
                $variation = Variation::find($single_data['single_variation_id']);

                $variation->sub_sku = $product->sku;
                $variation->default_purchase_price = $this->productUtil->num_uf($single_data['single_dpp']);
                $variation->var_barcode_no = $single_data['var_barcode_no'];
                $variation->dpp_inc_tax = $this->productUtil->num_uf($single_data['single_dpp_inc_tax']);
                $variation->profit_percent = $this->productUtil->num_uf($single_data['profit_percent']);
                $variation->default_sell_price = $this->productUtil->num_uf($single_data['single_dsp']);
                $variation->sell_price_inc_tax = $this->productUtil->num_uf($single_data['single_dsp_inc_tax']);
                $variation->save();

                Media::uploadMedia($product->business_id, $variation, $request, 'variation_images');
            } elseif ($product->type == 'variable') {
                //Update existing variations
                $input_variations_edit = $request->get('product_variation_edit');
                if (! empty($input_variations_edit)) {
                    $this->productUtil->updateVariableProductVariations($product->id, $input_variations_edit, $request->input('sku_type'));
                }

                //Add new variations created.
                $input_variations = $request->input('product_variation');
                if (! empty($input_variations)) {
                    $this->productUtil->createVariableProductVariations($product->id, $input_variations, $request->input('sku_type'));
                }
            } elseif ($product->type == 'combo') {

                //Create combo_variations array by combining variation_id and quantity.
                $combo_variations = [];
                if (! empty($request->input('composition_variation_id'))) {
                    $composition_variation_id = $request->input('composition_variation_id');
                    $quantity = $request->input('quantity');
                    $unit = $request->input('unit');

                    foreach ($composition_variation_id as $key => $value) {
                        $combo_variations[] = [
                            'variation_id' => $value,
                            'quantity' => $quantity[$key],
                            'unit_id' => $unit[$key],
                        ];
                    }
                }

                $variation = Variation::find($request->input('combo_variation_id'));
                $variation->sub_sku = $product->sku;
                $variation->default_purchase_price = $this->productUtil->num_uf($request->input('item_level_purchase_price_total'));
                $variation->dpp_inc_tax = $this->productUtil->num_uf($request->input('purchase_price_inc_tax'));
                $variation->profit_percent = $this->productUtil->num_uf($request->input('profit_percent'));
                $variation->default_sell_price = $this->productUtil->num_uf($request->input('selling_price'));
                $variation->sell_price_inc_tax = $this->productUtil->num_uf($request->input('selling_price_inc_tax'));
                $variation->combo_variations = $combo_variations;
                $variation->save();
            }

            //Add product racks details.
            $product_racks = $request->get('product_racks', null);
            if (! empty($product_racks)) {
                $this->productUtil->addRackDetails($business_id, $product->id, $product_racks);
            }

            $product_racks_update = $request->get('product_racks_update', null);
            if (! empty($product_racks_update)) {
                $this->productUtil->updateRackDetails($business_id, $product->id, $product_racks_update);
            }

            //Set Module fields
            if (! empty($request->input('has_module_data'))) {
                $this->moduleUtil->getModuleData('after_product_saved', ['product' => $product, 'request' => $request]);
            }

            Media::uploadMedia($product->business_id, $product, $request, 'product_brochure', true);

            // Handle state restrictions
            $this->saveProductStates($product->id, $request);

            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('product.product_updated_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage(),
            ];
        }

        if ($request->input('submit_type') == 'update_n_edit_opening_stock') {
            return redirect()->action(
                [\App\Http\Controllers\OpeningStockController::class, 'add'],
                ['product_id' => $product->id]
            );
        } elseif ($request->input('submit_type') == 'submit_n_add_selling_prices') {
            return redirect()->action(
                [\App\Http\Controllers\ProductController::class, 'addSellingPrices'],
                [$product->id]
            );
        } elseif ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action(
                [\App\Http\Controllers\ProductController::class, 'create']
            )->with('status', $output);
        }

        return redirect('products')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('product.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $can_be_deleted = true;
                $error_msg = '';

                //Check if any purchase or transfer exists
                $count = PurchaseLine::join(
                    'transactions as T',
                    'purchase_lines.transaction_id',
                    '=',
                    'T.id'
                )
                    ->whereIn('T.type', ['purchase'])
                    ->where('T.business_id', $business_id)
                    ->where('purchase_lines.product_id', $id)
                    ->count();
                if ($count > 0) {
                    $can_be_deleted = false;
                    $error_msg = __('lang_v1.purchase_already_exist');
                } else {
                    //Check if any opening stock sold
                    $count = PurchaseLine::join(
                        'transactions as T',
                        'purchase_lines.transaction_id',
                        '=',
                        'T.id'
                    )
                        ->where('T.type', 'opening_stock')
                        ->where('T.business_id', $business_id)
                        ->where('purchase_lines.product_id', $id)
                        ->where('purchase_lines.quantity_sold', '>', 0)
                        ->count();
                    if ($count > 0) {
                        $can_be_deleted = false;
                        $error_msg = __('lang_v1.opening_stock_sold');
                    } else {
                        //Check if any stock is adjusted
                        $count = PurchaseLine::join(
                            'transactions as T',
                            'purchase_lines.transaction_id',
                            '=',
                            'T.id'
                        )
                            ->where('T.business_id', $business_id)
                            ->where('purchase_lines.product_id', $id)
                            ->where('purchase_lines.quantity_adjusted', '>', 0)
                            ->count();
                        if ($count > 0) {
                            $can_be_deleted = false;
                            $error_msg = __('lang_v1.stock_adjusted');
                        }
                    }
                }

                $product = Product::where('id', $id)
                    ->where('business_id', $business_id)
                    ->with('variations')
                    ->first();

                // check for enable stock = 0 product
                if ($product->enable_stock == 0) {
                    $t_count = TransactionSellLine::join(
                        'transactions as T',
                        'transaction_sell_lines.transaction_id',
                        '=',
                        'T.id'
                    )
                        ->where('T.business_id', $business_id)
                        ->where('transaction_sell_lines.product_id', $id)
                        ->count();

                    if ($t_count > 0) {
                        $can_be_deleted = false;
                        $error_msg = "can't delete product exit in sell";
                    }
                }

                //Check if product is added as an ingredient of any recipe
                if ($this->moduleUtil->isModuleInstalled('Manufacturing')) {
                    $variation_ids = $product->variations->pluck('id');

                    $exists_as_ingredient = \Modules\Manufacturing\Entities\MfgRecipeIngredient::whereIn('variation_id', $variation_ids)
                        ->exists();
                    if ($exists_as_ingredient) {
                        $can_be_deleted = false;
                        $error_msg = __('manufacturing::lang.added_as_ingredient');
                    }
                }

                if ($can_be_deleted) {
                    if (! empty($product)) {
                        DB::beginTransaction();
                        //Delete variation location details
                        VariationLocationDetails::where('product_id', $id)
                            ->delete();
                        $product->delete();
                        event(new ProductsCreatedOrModified($product, 'deleted'));
                        DB::commit();
                    }

                    $output = [
                        'success' => true,
                        'msg' => __('lang_v1.product_delete_success'),
                    ];
                } else {
                    $output = [
                        'success' => false,
                        'msg' => $error_msg,
                    ];
                }
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Get subcategories list for a category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSubCategories(Request $request)
    {
        if (! empty($request->input('cat_id'))) {
            $category_id = $request->input('cat_id');
            $business_id = $request->session()->get('user.business_id');
            $sub_categories = Category::where('business_id', $business_id)
                ->where('parent_id', $category_id)
                ->select(['name', 'id'])
                ->get();
            $html = '<option value="">None</option>';
            if (! empty($sub_categories)) {
                foreach ($sub_categories as $sub_category) {
                    $html .= '<option value="' . $sub_category->id . '">' . $sub_category->name . '</option>';
                }
            }
            echo $html;
            exit;
        }
    }

    /**
     * Get product form parts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProductVariationFormPart(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findorfail($business_id);
        $profit_percent = $business->default_profit_percent;

        $action = $request->input('action');
        if ($request->input('action') == 'add') {
            if ($request->input('type') == 'single') {
                return view('product.partials.single_product_form_part')
                    ->with(['profit_percent' => $profit_percent]);
            } elseif ($request->input('type') == 'variable') {
                $variation_templates = VariationTemplate::where('business_id', $business_id)->pluck('name', 'id')->toArray();
                $variation_templates = ['' => __('messages.please_select')] + $variation_templates;

                return view('product.partials.variable_product_form_part')
                    ->with(compact('variation_templates', 'profit_percent', 'action'));
            } elseif ($request->input('type') == 'combo') {
                return view('product.partials.combo_product_form_part')
                    ->with(compact('profit_percent', 'action'));
            }
        } elseif ($request->input('action') == 'edit' || $request->input('action') == 'duplicate') {
            $product_id = $request->input('product_id');
            $action = $request->input('action');
            if ($request->input('type') == 'single') {
                $product_deatails = ProductVariation::where('product_id', $product_id)
                    ->with(['variations', 'variations.media'])
                    ->first();

                return view('product.partials.edit_single_product_form_part')
                    ->with(compact('product_deatails', 'action'));
            } elseif ($request->input('type') == 'variable') {
                $product_variations = ProductVariation::where('product_id', $product_id)
                    ->with(['variations', 'variations.media', 'variations.group_prices'])
                    ->get();
                
                // Filter out discontinued variations in PHP (handles case where column might not exist)
                $product_variations->each(function($pv) {
                    $pv->setRelation('variations', $pv->variations->filter(function($v) {
                        // Check if is_discontinued exists and is not 1
                        if (property_exists($v, 'is_discontinued') || isset($v->is_discontinued)) {
                            return $v->is_discontinued != 1;
                        }
                        // If column doesn't exist, include all variations
                        return true;
                    }));
                });

                // For edit: pass allowed group prices so Vip/Diamond etc. columns display
                $allowed_group_prices = [];
                if ($action == 'edit' || $action == 'duplicate') {
                    $business_id = $request->session()->get('user.business_id');
                    $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');
                    foreach ($price_groups as $key => $value) {
                        if (auth()->user()->can('selling_price_group.' . $key)) {
                            $allowed_group_prices[$key] = $value;
                        }
                    }
                }

                return view('product.partials.variable_product_form_part')
                    ->with(compact('product_variations', 'profit_percent', 'action', 'allowed_group_prices'));
            } elseif ($request->input('type') == 'combo') {
                $product_deatails = ProductVariation::where('product_id', $product_id)
                    ->with(['variations', 'variations.media'])
                    ->first();
                $combo_variations = $this->productUtil->__getComboProductDetails($product_deatails['variations'][0]->combo_variations, $business_id);

                $variation_id = $product_deatails['variations'][0]->id;
                $profit_percent = $product_deatails['variations'][0]->profit_percent;

                return view('product.partials.combo_product_form_part')
                    ->with(compact('combo_variations', 'profit_percent', 'action', 'variation_id'));
            }
        }
    }

    /**
     * Get product form parts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getVariationValueRow(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findorfail($business_id);
        $profit_percent = $business->default_profit_percent;

        $variation_index = $request->input('variation_row_index');
        $value_index = $request->input('value_index') + 1;

        $row_type = $request->input('row_type', 'add');

        return view('product.partials.variation_value_row')
            ->with(compact('profit_percent', 'variation_index', 'value_index', 'row_type'));
    }

    /**
     * Get product form parts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProductVariationRow(Request $request)
    {
        $row_index = $request->input('row_index', 0);
        $action = $request->input('action');
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findorfail($business_id);
        $profit_percent = $business->default_profit_percent;

        $variation_templates = VariationTemplate::where('business_id', $business_id)
            ->pluck('name', 'id')->toArray();
        $variation_templates = ['' => __('messages.please_select')] + $variation_templates;

        $row_index = $request->input('row_index', 0);
        $action = $request->input('action');

        return view('product.partials.product_variation_row')
            ->with(compact('variation_templates', 'row_index', 'action', 'profit_percent'));
    }

    
    /**
     * Get variation template
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getVariationTemplate(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);
        $profit_percent = $business->default_profit_percent;

        $template = VariationTemplate::where('id', $request->input('template_id'))
            ->with(['values'])
            ->firstOrFail();

        $values = $template->values->map(function ($v) {
            return [
                'id' => $v->id,
                'text' => $v->name,
            ];
        });

        return [
            'status' => 'success',
            'values' => $values,
            'profit_percent' => $profit_percent,
        ];
    }
    /**
     * Get variation value row by id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getVariationValueRowById(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);
        $profit_percent = $business->default_profit_percent;

        $variation_id = $request->input('variation_id');
        $value_id = $request->input('value_id');
        $row_index = $request->input('row_index');
        $value_index = $request->input('value_index');

        $variation_value = VariationValueTemplate::findOrFail($value_id);

        return response()->json([
            'status' => 'success',
            'html' => view('product.partials.variation_value_row', [
                'variation_index' => $row_index,
                'value_index' => $value_index,
                'variation_name' => $variation_value->name,
                'variation_value_id' => $variation_value->id,
                'profit_percent' => $profit_percent,
            ])->render()
        ]);
    }
    /**
     * Return the view for combo product row
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getComboProductEntryRow(Request $request)
    {
        if (request()->ajax()) {
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $business_id = $request->session()->get('user.business_id');

            if (! empty($product_id)) {
                $product = Product::where('id', $product_id)
                    ->with(['unit'])
                    ->first();

                $query = Variation::where('product_id', $product_id)
                    ->with(['product_variation']);

                if ($variation_id !== '0') {
                    $query->where('id', $variation_id);
                }
                $variations = $query->get();

                $sub_units = $this->productUtil->getSubUnits($business_id, $product['unit']->id);

                return view('product.partials.combo_product_entry_row')
                    ->with(compact('product', 'variations', 'sub_units'));
            }
        }
    }
  /**
     * Upload variation image instantly (for edit page only)
     * Creates variation if it doesn't exist, then uploads the image
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadVariationImageInstantly(Request $request)
    {
        if (! auth()->user()->can('product.update')) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized action'], 403);
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id'); // null for new variations
            $product_variation_id = $request->input('product_variation_id'); // ProductVariation ID

            if (empty($product_id)) {
                return response()->json(['success' => false, 'msg' => 'Product ID is required']);
            }

            // Verify product belongs to business
            $product = Product::where('business_id', $business_id)->find($product_id);
            if (!$product) {
                return response()->json(['success' => false, 'msg' => 'Product not found']);
            }

            DB::beginTransaction();

            $variation = null;

            // If variation_id exists, use existing variation
            if (!empty($variation_id)) {
                $variation = Variation::where('product_id', $product_id)
                    ->where('id', $variation_id)
                    ->first();
                
                if (!$variation) {
                    return response()->json(['success' => false, 'msg' => 'Variation not found']);
                }
            } else {
                // Create new variation
                // Get variation data from request
                $variation_data = [
                    'name' => $request->input('variation_name', 'New Variation'),
                    'product_id' => $product_id,
                    'sub_sku' => $request->input('sub_sku', ''),
                    'var_barcode_no' => $request->input('var_barcode_no', null),
                    'var_maxSaleLimit' => $request->input('var_maxSaleLimit', null),
                    'default_purchase_price' => $this->productUtil->num_uf($request->input('default_purchase_price', 0)),
                    'dpp_inc_tax' => $this->productUtil->num_uf($request->input('dpp_inc_tax', 0)),
                    'profit_percent' => $this->productUtil->num_uf($request->input('profit_percent', 0)),
                    'default_sell_price' => $this->productUtil->num_uf($request->input('default_sell_price', 0)),
                    'sell_price_inc_tax' => $this->productUtil->num_uf($request->input('sell_price_inc_tax', 0)),
                ];

                // Get or create ProductVariation
                if (!empty($product_variation_id)) {
                    $product_variation = ProductVariation::where('product_id', $product_id)
                        ->where('id', $product_variation_id)
                        ->first();
                } else {
                    // Create new ProductVariation if needed
                    $variation_template_id = $request->input('variation_template_id');
                    $variation_name = $request->input('product_variation_name', 'Default');
                    
                    $product_variation = ProductVariation::create([
                        'product_id' => $product_id,
                        'name' => $variation_name,
                        'variation_template_id' => $variation_template_id,
                    ]);
                }

                if (!$product_variation) {
                    return response()->json(['success' => false, 'msg' => 'Product variation not found']);
                }

                // Create the variation
                $variation = $product_variation->variations()->create($variation_data);
            }

            // Upload the image
            if ($request->hasFile('image')) {
                // Handle single file upload
                $file = $request->file('image');
                
                // Upload the file
                $uploaded_file = Media::uploadFile($file);
                
                if (!empty($uploaded_file)) {
                    // Attach media to variation
                    Media::attachMediaToModel($variation, $business_id, [$uploaded_file], $request);
                }
            }

            // Reload variation with media
            $variation->refresh();
            $variation->load('media');

            // Get uploaded image URLs - return all images so frontend can check for duplicates
            $image_urls = [];
            if ($variation->media && $variation->media->isNotEmpty()) {
                foreach ($variation->media as $media) {
                    $image_urls[] = [
                        'id' => $media->id,
                        'url' => $media->display_url ?? asset('uploads/media/' . $media->file_name),
                        'thumbnail' => $media->thumbnail() ? (string)$media->thumbnail() : null
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Image uploaded successfully',
                'variation_id' => $variation->id,
                'product_variation_id' => $variation->product_variation_id,
                'images' => $image_urls
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create variation instantly when a new variation row is added (for edit page only)
     * Creates variation with default/minimal data so it can be used for image uploads
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createVariationInstantly(Request $request)
    {
        if (! auth()->user()->can('product.update')) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized action'], 403);
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $product_id = $request->input('product_id');
            $product_variation_id = $request->input('product_variation_id'); // ProductVariation ID

            if (empty($product_id)) {
                return response()->json(['success' => false, 'msg' => 'Product ID is required']);
            }

            // Verify product belongs to business
            $product = Product::where('business_id', $business_id)->find($product_id);
            if (!$product) {
                return response()->json(['success' => false, 'msg' => 'Product not found']);
            }

            DB::beginTransaction();

            // Get SKU type (default to 'with_out_variation' if not provided)
            $sku_type = $request->input('sku_type', 'with_out_variation');
            
            // Get variation count for SKU generation
            $variation_count = Variation::withTrashed()
                ->where('product_id', $product_id)
                ->count() + 1;

            // Generate sub_sku if not provided
            $sub_sku = $request->input('sub_sku', '');
            if (empty($sub_sku)) {
                $variation_name = $request->input('variation_name', 'New Variation');
                $sub_sku = $this->productUtil->generateSubSku(
                    $product->sku,
                    $variation_count,
                    $product->barcode_type,
                    $variation_name,
                    $sku_type
                );
            }

            // Get variation data from request (use defaults if not provided)
            $variation_data = [
                'name' => $request->input('variation_name', 'New Variation'),
                'product_id' => $product_id,
                'sub_sku' => $sub_sku,
                'var_barcode_no' => $request->input('var_barcode_no', null),
                'var_maxSaleLimit' => $request->input('var_maxSaleLimit', null),
                'default_purchase_price' => $this->productUtil->num_uf($request->input('default_purchase_price', 0)),
                'dpp_inc_tax' => $this->productUtil->num_uf($request->input('dpp_inc_tax', 0)),
                'profit_percent' => $this->productUtil->num_uf($request->input('profit_percent', 0)),
                'default_sell_price' => $this->productUtil->num_uf($request->input('default_sell_price', 0)),
                'sell_price_inc_tax' => $this->productUtil->num_uf($request->input('sell_price_inc_tax', 0)),
            ];

            // Get or create ProductVariation
            if (!empty($product_variation_id)) {
                $product_variation = ProductVariation::where('product_id', $product_id)
                    ->where('id', $product_variation_id)
                    ->first();
            } else {
                // Create new ProductVariation if needed
                $variation_template_id = $request->input('variation_template_id');
                $variation_name = $request->input('product_variation_name', 'Default');
                
                $product_variation = ProductVariation::create([
                    'product_id' => $product_id,
                    'name' => $variation_name,
                    'variation_template_id' => $variation_template_id,
                ]);
            }

            if (!$product_variation) {
                DB::rollBack();
                return response()->json(['success' => false, 'msg' => 'Product variation not found']);
            }

            // Create the variation
            $variation = $product_variation->variations()->create($variation_data);

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Variation created successfully',
                'variation_id' => $variation->id,
                'product_variation_id' => $variation->product_variation_id,
                'sub_sku' => $variation->sub_sku
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error creating variation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieves products list.
     *
     * @param  string  $q
     * @param  bool  $check_qty
     * @return JSON
     */
    public function getProducts()
    {
        if (request()->ajax()) {
            $search_term = request()->input('term', '');
            $location_id = request()->input('location_id', null);
            $check_qty = request()->input('check_qty', false);
            $price_group_id = request()->input('price_group', null);
            $business_id = request()->session()->get('user.business_id');
            $not_for_selling = request()->get('not_for_selling', null);
            $price_group_id = request()->input('price_group', '');
            $product_types = request()->get('product_types', []);

            $search_fields = request()->get('search_fields', ['name', 'sku', 'var_barcode_no']);

            $is_metrix = filter_var(request()->get('is_metrix', false), FILTER_VALIDATE_BOOLEAN);
            if ($is_metrix) {
                $result = $this->productUtil->filtermetrix($business_id, $search_term, $location_id, $not_for_selling, $price_group_id, $product_types, $search_fields, $check_qty);
            } else {
                if (in_array('sku', $search_fields)) {
                    $search_fields[] = 'sub_sku';
                }
                $result = $this->productUtil->filterProduct($business_id, $search_term, $location_id, $not_for_selling, $price_group_id, $product_types, $search_fields, $check_qty);
            }

            return json_encode($result);
        }
    }

    /**
     * Get matrix data
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @param int $priceGroupId
     * @return \Illuminate\Http\Response
     */
    public function getMatrixData(Request $request, $id, $priceGroupId)
    {
        $business_id = $request->session()->get('user.business_id');
        $is_purchase = false;
        if ($request->input('is_purchase')) {
            $is_purchase = true;
        } else {
            $is_purchase = false;
        }

        $product = Product::with([
            'brand',
            'unit',
            'category',
            'sub_category',
            'product_tax',
            'media',
            'product_variations',
            'variations' => function ($query) use ($priceGroupId) {
                $query->select([
                    'variations.id',
                    'variations.name',
                    'variations.product_id',
                    'variations.var_barcode_no',
                    'variations.sub_sku',
                    'variations.var_maxSaleLimit',
                    'variations.product_variation_id',
                    'variation_location_details.qty_available as in_hand_qty',
                    'variation_location_details.in_stock_qty as qty'
                ])
                    ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                        $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                            ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                    })
                    ->leftJoin('variation_location_details', function ($join) {
                        $join->on('variations.id', '=', 'variation_location_details.variation_id');
                    })
                    ->addSelect([
                        \DB::raw('COALESCE(variation_group_prices.price_inc_tax, 0) as ad_price')
                    ]);
            }
        ])
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->where('id', $id)
            ->first();

        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found.']);
        }

        $html = view('sell.partials.variable-modal')->with(compact('product', 'priceGroupId', 'is_purchase'))->render();

        return response()->json([
            'status' => true,
            'html' => $html
        ]);
    }
    /**
     * Get edit price product modal
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @param int $priceGroupId
     * @return \Illuminate\Http\Response
     */
    public function getEditPriceProductModal(Request $request, $id, $priceGroupId)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Load product with variations and existing group prices
        $product = Product::where('business_id', $business_id)
            ->with([
                'brand',
                'unit',
                'category',
                'sub_category',
                'webcategories',
                'product_tax',
                'variations',
                'variations.product_variation',
                'variations.group_prices',
                'product_locations',
                'warranty',
                'media',
            ])
            ->findOrFail($id);

        // Enabled modules and price groups
        $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];
        $price_groups = collect([]);
        if (in_array('group_pricing', $enabled_modules)) {
            $price_groups = SellingPriceGroup::where('business_id', $business_id)
                ->active()
                ->get();
        }

        // Build variation->price_group mapping (for group prices)
        $variation_prices = [];
        foreach ($product->variations as $variation) {
            foreach ($variation->group_prices as $group_price) {
                $variation_prices[$variation->id][$group_price->price_group_id] = [
                    'price' => $group_price->price_inc_tax,
                    'price_type' => $group_price->price_type,
                ];
            }
        }

        // Price group percentage settings from business common settings
        $common_settings = session()->get('business.common_settings', []);
        $price_group_percentage = !empty($common_settings['price_group_percentage'])
            ? $common_settings['price_group_percentage']
            : [];

        return view('sale_pos.modals.edit_price_product_modal')
            ->with(compact('product', 'variation_prices', 'price_groups', 'price_group_percentage'));
    }


    /**
     * Retrieves products list without variation list
     *
     * @param  string  $q
     * @param  bool  $check_qty
     * @return JSON
     */
    public function getProductsWithoutVariations()
    {
        if (request()->ajax()) {
            $term = request()->input('term', '');
            //$location_id = request()->input('location_id', '');

            //$check_qty = request()->input('check_qty', false);

            $business_id = request()->session()->get('user.business_id');

            $products = Product::join('variations', 'products.id', '=', 'variations.product_id')
                ->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier');

            //Include search
            if (! empty($term)) {
                $products->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term . '%');
                    $query->orWhere('sku', 'like', '%' . $term . '%');
                    $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                });
            }

            //Include check for quantity
            // if($check_qty){
            //     $products->where('VLD.qty_available', '>', 0);
            // }

            $products = $products->groupBy('products.id')
                ->select(
                    'products.id as product_id',
                    'products.name',
                    'products.type',
                    'products.enable_stock',
                    'products.sku',
                    'products.id as id',
                    DB::raw('CONCAT(products.name, " - ", products.sku) as text')
                )
                ->orderBy('products.name')
                ->get();

            return json_encode($products);
        }
    }

    /**
     * Checks if product sku already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkProductBarcodeNO(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $sku = $request->input('barcode_no');
        $product_id = $request->input('product_id');
        // $query = Product::where('business_id', $business_id)
        //     ->where('barcode_no', $sku);
        // if (! empty($product_id)) {
        //     $query->where('id', '!=', $product_id);
        // }
        $count = 0;
        if ($count == 0) {
            $query2 = Variation::where('var_barcode_no', $sku)
                ->join('products', 'variations.product_id', '=', 'products.id')
                ->where('business_id', $business_id);

            if (! empty($product_id)) {
                $query2->where('product_id', '!=', $product_id);
            }

            if (! empty($request->input('variation_id'))) {
                $query2->where('variations.id', '!=', $request->input('variation_id'));
            }
            $count = $query2->count();
        }
        if ($count == 0) {
            echo 'true';
            exit;
        } else {
            echo 'false';
            exit;
        }
    }
    /**
     * Check if product sku already exists
     * @param \Illuminate\Http\Request $request
     * @return never
     */
    public function checkProductSku(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $sku = $request->input('sku');
        $product_id = $request->input('product_id');

        //check in products table
        $query = Product::where('business_id', $business_id)
            ->where('sku', $sku);
        if (! empty($product_id)) {
            $query->where('id', '!=', $product_id);
        }
        $count = $query->count();

        //check in variation table if $count = 0
        if ($count == 0) {
            $query2 = Variation::where('sub_sku', $sku)
                ->join('products', 'variations.product_id', '=', 'products.id')
                ->where('business_id', $business_id);

            if (! empty($product_id)) {
                $query2->where('product_id', '!=', $product_id);
            }

            if (! empty($request->input('variation_id'))) {
                $query2->where('variations.id', '!=', $request->input('variation_id'));
            }
            $count = $query2->count();
        }
        if ($count == 0) {
            echo 'true';
            exit;
        } else {
            echo 'false';
            exit;
        }
    }

    /**
     * Validates multiple variation skus
     */
    public function validateVaritionSkus(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $all_skus = $request->input('skus');

        $skus = collect($all_skus)->pluck('sku')->filter()->unique()->values();

        // Check for duplicate in products.sku
        $product = Product::where('business_id', $business_id)
            ->whereIn('sku', $skus)
            ->select('sku')
            ->first();

        if (!empty($product)) {
            return ['success' => 0, 'sku' => $product->sku];
        }

        // Prepare variation_id map for exclusion
        $variationIdMap = collect($all_skus)->mapWithKeys(function ($item) {
            return [$item['sku'] => $item['variation_id'] ?? null];
        });

        // Query all existing variation SKUs in one go
        $existing = Variation::whereIn('sub_sku', $skus)
            ->join('products', 'variations.product_id', '=', 'products.id')
            ->where('products.business_id', $business_id)
            ->select('variations.id', 'sub_sku')
            ->get();

        foreach ($existing as $variation) {
            $incomingId = $variationIdMap[$variation->sub_sku] ?? null;
            if ((string) $variation->id !== (string) $incomingId) {
                return ['success' => 0, 'sku' => $variation->sub_sku];
            }
        }

        return ['success' => 1];
    }

    /**
     * Validate variation barcodes
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function validateVaritionBarcodes(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $all_skus = $request->input('barcodes');
        $business_id = $request->session()->get('user.business_id');
        if (!empty($all_skus)) {
            // Filter out empty barcode values and validate each barcode
            $barcodes = [];
            foreach ($all_skus as $sku) {
                if (!empty($sku['barcode_no'])) {
                    // Validate barcode format
                    if (!preg_match('/^[0-9]+$/', $sku['barcode_no'])) {
                        return [
                            'success' => 0,
                            'sku' => $sku['barcode_no'],
                            'message' => 'Invalid barcode format - must contain only numbers'
                        ];
                    }
                    $barcodes[] = $sku['barcode_no'];
                }
            }

            if (!empty($barcodes)) {
                // Check for duplicate barcodes within the input
                $barcode_counts = array_count_values($barcodes);
                foreach ($barcode_counts as $barcode => $count) {
                    if ($count > 1) {
                        return [
                            'success' => 0,
                            'sku' => $barcode,
                            'message' => "Duplicate barcode found: $barcode appears $count times"
                        ];
                    }
                }
            }
        }

        $skus = collect($all_skus)->pluck('barcode_no')->toArray();
        $variation_ids = collect($all_skus)->pluck('variation_id', 'barcode_no')->toArray();

        $query = Variation::whereIn('var_barcode_no', $skus)
            ->join('products', 'variations.product_id', '=', 'products.id')
            ->where('business_id', $business_id);

        $variations = $query->get(['variations.id', 'var_barcode_no']);

        foreach ($variations as $variation) {
            $incoming_variation_id = $variation_ids[$variation->var_barcode_no] ?? null;
            if ((string)$variation->id !== (string)$incoming_variation_id) {
                return ['success' => 0, 'sku' => $variation->var_barcode_no];
            }
        }

        return ['success' => 1];
    }

    /**
     * Loads quick add product modal.
     *
     * @return \Illuminate\Http\Response
     */
    public function quickAdd()
    {
        if (! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $product_name = ! empty(request()->input('product_name')) ? request()->input('product_name') : '';

        $product_for = ! empty(request()->input('product_for')) ? request()->input('product_for') : null;

        $business_id = request()->session()->get('user.business_id');
        $location_id = $this->getLocationId(request());
        $categories = Category::forDropdown($business_id, 'product',$location_id);
        $brands = Brands::forDropdown($business_id, false, false, $location_id);
        $units = Unit::forDropdown($business_id, true);

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        $taxes = $tax_dropdown['tax_rates'];
        $tax_attributes = $tax_dropdown['attributes'];

        $barcode_types = $this->barcode_types;

        $default_profit_percent = Business::where('id', $business_id)->value('default_profit_percent');

        $locations = BusinessLocation::forDropdown($business_id);

        $enable_expiry = request()->session()->get('business.enable_product_expiry');
        $enable_lot = request()->session()->get('business.enable_lot_number');

        $module_form_parts = $this->moduleUtil->getModuleData('product_form_part');

        //Get all business locations
        $business_locations = BusinessLocation::forDropdown($business_id);

        $common_settings = session()->get('business.common_settings');
        $warranties = Warranty::forDropdown($business_id);

        return view('product.partials.quick_add_product')
            ->with(compact('categories', 'brands', 'units', 'taxes', 'barcode_types', 'default_profit_percent', 'tax_attributes', 'product_name', 'locations', 'product_for', 'enable_expiry', 'enable_lot', 'module_form_parts', 'business_locations', 'common_settings', 'warranties'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveQuickProduct(Request $request)
    {
        if (! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $form_fields = [
                'name',
                'brand_id',
                'unit_id',
                'category_id',
                'tax',
                'barcode_type',
                'tax_type',
                'sku',
                'alert_quantity',
                'type',
                'sub_unit_ids',
                'sub_category_id',
                'weight',
                'length',
                'width',
                'height',
                'product_description',
                'product_warranty',
                'product_custom_field1',
                'product_custom_field2',
                'product_custom_field3',
                'product_custom_field4',
                'product_custom_field5',
                'product_custom_field6',
                'product_custom_field7',
                'product_custom_field8',
                'product_custom_field9',
                'product_custom_field10',
                'product_custom_field11',
                'product_custom_field12',
                'product_custom_field13',
                'product_custom_field14',
                'product_custom_field15',
                'product_custom_field16',
                'product_custom_field17',
                'product_custom_field18',
                'product_custom_field19',
                'product_custom_field20'
            ];

            $module_form_fields = $this->moduleUtil->getModuleData('product_form_fields');
            if (! empty($module_form_fields)) {
                foreach ($module_form_fields as $key => $value) {
                    if (! empty($value) && is_array($value)) {
                        $form_fields = array_merge($form_fields, $value);
                    }
                }
            }
            $product_details = $request->only($form_fields);

            $product_details['type'] = empty($product_details['type']) ? 'single' : $product_details['type'];
            $product_details['business_id'] = $business_id;
            $product_details['created_by'] = $request->session()->get('user.id');
            if (! empty($request->input('enable_stock')) && $request->input('enable_stock') == 1) {
                $product_details['enable_stock'] = 1;
                //TODO: Save total qty
                //$product_details['total_qty_available'] = 0;
            }
            if (! empty($request->input('not_for_selling')) && $request->input('not_for_selling') == 1) {
                $product_details['not_for_selling'] = 1;
            }
            if (empty($product_details['sku'])) {
                $product_details['sku'] = ' ';
            }

            if (! empty($product_details['alert_quantity'])) {
                $product_details['alert_quantity'] = $this->productUtil->num_uf($product_details['alert_quantity']);
            }

            $expiry_enabled = $request->session()->get('business.enable_product_expiry');
            if (! empty($request->input('expiry_period_type')) && ! empty($request->input('expiry_period')) && ! empty($expiry_enabled)) {
                $product_details['expiry_period_type'] = $request->input('expiry_period_type');
                $product_details['expiry_period'] = $this->productUtil->num_uf($request->input('expiry_period'));
            }

            if (! empty($request->input('enable_sr_no')) && $request->input('enable_sr_no') == 1) {
                $product_details['enable_sr_no'] = 1;
            }

            $product_details['warranty_id'] = ! empty($request->input('warranty_id')) ? $request->input('warranty_id') : null;

            DB::beginTransaction();

            $product = Product::create($product_details);
            event(new ProductsCreatedOrModified($product_details, 'added'));

            if (empty(trim($request->input('sku')))) {
                $sku = $this->productUtil->generateProductSku($product->id);
                $product->sku = $sku;
                $product->save();
            }

            $this->productUtil->createSingleProductVariation(
                $product->id,
                $product->sku,
                $request->input('single_dpp'),
                $request->input('single_dpp_inc_tax'),
                $request->input('profit_percent'),
                $request->input('single_dsp'),
                $request->input('single_dsp_inc_tax')
            );

            if ($product->enable_stock == 1 && ! empty($request->input('opening_stock'))) {
                $user_id = $request->session()->get('user.id');

                $transaction_date = $request->session()->get('financial_year.start');
                $transaction_date = \Carbon::createFromFormat('Y-m-d', $transaction_date)->toDateTimeString();

                $this->productUtil->addSingleProductOpeningStock($business_id, $product, $request->input('opening_stock'), $transaction_date, $user_id);
            }

            //Add product locations
            $product_locations = $request->input('product_locations');
            if (! empty($product_locations)) {
                $product->product_locations()->sync($product_locations);
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('product.product_added_success'),
                'product' => $product,
                'variation' => $product->variations->first(),
                'locations' => $product_locations,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        if (! auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $product = Product::where('business_id', $business_id)
                ->with(['brand', 'unit', 'category', 'sub_category', 'webcategories', 'product_tax', 'variations', 'variations.product_variation', 'variations.group_prices', 'variations.media', 'product_locations', 'warranty', 'media', 'vendors'])
                ->findOrFail($id);

            $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');

            $allowed_group_prices = [];
            foreach ($price_groups as $key => $value) {
                if (auth()->user()->can('selling_price_group.' . $key)) {
                    $allowed_group_prices[$key] = $value;
                }
            }

            $group_price_details = [];

            foreach ($product->variations as $variation) {
                foreach ($variation->group_prices as $group_price) {
                    $group_price_details[$variation->id][$group_price->price_group_id] = ['price' => $group_price->price_inc_tax, 'price_type' => $group_price->price_type, 'calculated_price' => $group_price->calculated_price];
                }
            }

            $rack_details = $this->productUtil->getRackDetails($business_id, $id, true);

            $combo_variations = [];
            if ($product->type == 'combo') {
                $combo_variations = $this->productUtil->__getComboProductDetails($product['variations'][0]->combo_variations, $business_id);
            }

            return view('product.view-modal')->with(compact(
                'product',
                'rack_details',
                'allowed_group_prices',
                'group_price_details',
                'combo_variations'
            ));
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
        }
    }

    /**
     * Mass deletes products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {
        if (! auth()->user()->can('product.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $purchase_exist = false;

            if (! empty($request->input('selected_rows'))) {
                $business_id = $request->session()->get('user.business_id');

                $selected_rows = explode(',', $request->input('selected_rows'));

                $products = Product::where('business_id', $business_id)
                    ->whereIn('id', $selected_rows)
                    ->with(['purchase_lines', 'variations'])
                    ->get();
                $deletable_products = [];

                $is_mfg_installed = $this->moduleUtil->isModuleInstalled('Manufacturing');

                DB::beginTransaction();

                foreach ($products as $product) {
                    $can_be_deleted = true;
                    //Check if product is added as an ingredient of any recipe
                    if ($is_mfg_installed) {
                        $variation_ids = $product->variations->pluck('id');

                        $exists_as_ingredient = \Modules\Manufacturing\Entities\MfgRecipeIngredient::whereIn('variation_id', $variation_ids)
                            ->exists();
                        $can_be_deleted = ! $exists_as_ingredient;
                    }

                    //Delete if no purchase found
                    if (empty($product->purchase_lines->toArray()) && $can_be_deleted) {
                        //Delete variation location details
                        VariationLocationDetails::where('product_id', $product->id)
                            ->delete();
                        $product->delete();
                        event(new ProductsCreatedOrModified($product, 'Deleted'));
                    } else {
                        $purchase_exist = true;
                    }
                }

                DB::commit();
            }

            if (! $purchase_exist) {
                $output = [
                    'success' => 1,
                    'msg' => __('lang_v1.deleted_success'),
                ];
            } else {
                $output = [
                    'success' => 0,
                    'msg' => __('lang_v1.products_could_not_be_deleted'),
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Shows form to add selling price group prices for a product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addSellingPrices($id)
    {
        if (! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $product = Product::where('business_id', $business_id)
            ->with(['variations', 'variations.group_prices', 'variations.product_variation'])
            ->findOrFail($id);

        $price_groups = SellingPriceGroup::where('business_id', $business_id)
            ->active()
            ->get();
        $variation_prices = [];
        foreach ($product->variations as $variation) {
            foreach ($variation->group_prices as $group_price) {
                $variation_prices[$variation->id][$group_price->price_group_id] = ['price' => $group_price->price_inc_tax, 'price_type' => $group_price->price_type];
            }
        }

        return view('product.add-selling-prices')->with(compact('product', 'price_groups', 'variation_prices'));
    }

    /**
     * Saves selling price group prices for a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSellingPrices(Request $request)
    {
        if (! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $product = Product::where('business_id', $business_id)
                ->with(['variations'])
                ->findOrFail($request->input('product_id'));
            DB::beginTransaction();
            foreach ($product->variations as $variation) {
                $variation_group_prices = [];
                foreach ($request->input('group_prices') as $key => $value) {
                    if (isset($value[$variation->id])) {
                        $variation_group_price =
                            VariationGroupPrice::where('variation_id', $variation->id)
                            ->where('price_group_id', $key)
                            ->first();
                        if (empty($variation_group_price)) {
                            $variation_group_price = new VariationGroupPrice([
                                'variation_id' => $variation->id,
                                'price_group_id' => $key,
                            ]);
                        }

                        $variation_group_price->price_inc_tax = $this->productUtil->num_uf($value[$variation->id]['price']);
                        $variation_group_price->price_type = $value[$variation->id]['price_type'];
                        $variation_group_prices[] = $variation_group_price;
                    }
                }

                if (! empty($variation_group_prices)) {
                    $variation->group_prices()->saveMany($variation_group_prices);
                }
            }
            //Update product updated_at timestamp
            $product->touch();

            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('lang_v1.updated_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        if ($request->input('submit_type') == 'submit_n_add_opening_stock') {
            return redirect()->action(
                [\App\Http\Controllers\OpeningStockController::class, 'add'],
                ['product_id' => $product->id]
            );
        } elseif ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action(
                [\App\Http\Controllers\ProductController::class, 'create']
            )->with('status', $output);
        }

        return redirect('products')->with('status', $output);
    }

    public function viewGroupPrice($id)
    {
        if (! auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $product = Product::where('business_id', $business_id)
            ->where('id', $id)
            ->with(['variations', 'variations.product_variation', 'variations.group_prices'])
            ->first();

        $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');

        $allowed_group_prices = [];
        foreach ($price_groups as $key => $value) {
            if (auth()->user()->can('selling_price_group.' . $key)) {
                $allowed_group_prices[$key] = $value;
            }
        }

        $group_price_details = [];

        foreach ($product->variations as $variation) {
            foreach ($variation->group_prices as $group_price) {
                $group_price_details[$variation->id][$group_price->price_group_id] = $group_price->price_inc_tax;
            }
        }

        return view('product.view-product-group-prices')->with(compact('product', 'allowed_group_prices', 'group_price_details'));
    }

    /**
     * Mass deactivates products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDeactivate(Request $request)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            if (! empty($request->input('selected_products'))) {
                $business_id = $request->session()->get('user.business_id');

                $selected_products = explode(',', $request->input('selected_products'));

                DB::beginTransaction();

                $products = Product::where('business_id', $business_id)
                    ->whereIn('id', $selected_products)
                    ->update(['is_inactive' => 1]);

                DB::commit();
            }

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.products_deactivated_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
    public function massDiscontinue(Request $request)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            if (! empty($request->input('selected_products'))) {
                $business_id = $request->session()->get('user.business_id');

                $selected_products = explode(',', $request->input('selected_products'));

                DB::beginTransaction();

                $products = Product::where('business_id', $business_id)
                    ->whereIn('id', $selected_products)
                    ->update(['discontinue' => 1,'is_inactive' => 1]);

                DB::commit();
            }

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.products_deactivated_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
    public function massActivate(Request $request){
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            if (! empty($request->input('selected_products'))) {
                $business_id = $request->session()->get('user.business_id');

                $selected_products = explode(',', $request->input('selected_products'));

                DB::beginTransaction();

                $products = Product::where('business_id', $business_id)
                    ->whereIn('id', $selected_products)
                    ->update(['is_inactive' => 0,'discontinue' => null]);

                DB::commit();
            }

            $output = [
                'success' => 1,
                'msg' => "Products activated successfully",
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    public function deactivate($id){
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $product = Product::where('id', $id)
                    ->where('business_id', $business_id)
                    ->update(['is_inactive' => 1]);

                $output = [
                    'success' => true,
                    'msg' => "Product discontinued successfully",
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }
    public function discontinue($id){
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $product = Product::where('id', $id)
                    ->where('business_id', $business_id)
                    ->update(['is_inactive' => 1,'discontinue' => 1]);

                $output = [
                    'success' => true,
                    'msg' => "Product discontinued successfully",
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }
    /**
     * Activates the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function activate($id)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $product = Product::where('id', $id)
                    ->where('business_id', $business_id)
                    ->update(['is_inactive' => 0,'discontinue' => null]);

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.updated_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Deletes a media file from storage and database.
     *
     * @param  int  $media_id
     * @return json
     */
    public function deleteMedia($media_id)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                Media::deleteMedia($business_id, $media_id);

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.file_deleted_successfully'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    public function getProductsApi($id = null)
    {
        try {
            $api_token = request()->header('API-TOKEN');
            $filter_string = request()->header('FILTERS');
            $order_by = request()->header('ORDER-BY');

            parse_str($filter_string, $filters);

            $api_settings = $this->moduleUtil->getApiSettings($api_token);

            $limit = ! empty(request()->input('limit')) ? request()->input('limit') : 10;

            $location_id = $api_settings->location_id;

            $query = Product::where('business_id', $api_settings->business_id)
                ->active()
                ->with([
                    'brand',
                    'unit',
                    'category',
                    'sub_category',
                    'product_variations',
                    'product_variations.variations',
                    'product_variations.variations.media',
                    'product_variations.variations.variation_location_details' => function ($q) use ($location_id) {
                        $q->where('location_id', $location_id);
                    },
                ]);

            if (! empty($filters['categories'])) {
                $query->whereIn('category_id', $filters['categories']);
            }

            if (! empty($filters['brands'])) {
                $query->whereIn('brand_id', $filters['brands']);
            }

            if (! empty($filters['category'])) {
                $query->where('category_id', $filters['category']);
            }

            if (! empty($filters['sub_category'])) {
                $query->where('sub_category_id', $filters['sub_category']);
            }

            if ($order_by == 'name') {
                $query->orderBy('name', 'asc');
            } elseif ($order_by == 'date') {
                $query->orderBy('created_at', 'desc');
            }

            if (empty($id)) {
                $products = $query->paginate($limit);
            } else {
                $products = $query->find($id);
            }
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            return $this->respondWentWrong($e);
        }

        return $this->respond($products);
    }

    public function getVariationsApi()
    {
        try {
            $api_token = request()->header('API-TOKEN');
            $variations_string = request()->header('VARIATIONS');

            if (is_numeric($variations_string)) {
                $variation_ids = intval($variations_string);
            } else {
                parse_str($variations_string, $variation_ids);
            }

            $api_settings = $this->moduleUtil->getApiSettings($api_token);
            $location_id = $api_settings->location_id;
            $business_id = $api_settings->business_id;

            $query = Variation::with([
                'product_variation',
                'product' => function ($q) use ($business_id) {
                    $q->where('business_id', $business_id);
                },
                'product.unit',
                'variation_location_details' => function ($q) use ($location_id) {
                    $q->where('location_id', $location_id);
                },
            ]);

            $variations = is_array($variation_ids) ? $query->whereIn('id', $variation_ids)->get() : $query->where('id', $variation_ids)->first();
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            return $this->respondWentWrong($e);
        }

        return $this->respond($variations);
    }

    /**
     * Shows form to edit multiple products at once.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkEdit(Request $request)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        $selected_products_string = $request->input('selected_products');
        if (! empty($selected_products_string)) {
            $selected_products = explode(',', $selected_products_string);
            $business_id = $request->session()->get('user.business_id');

            $products = Product::where('business_id', $business_id)
                ->whereIn('id', $selected_products)
                ->with(['variations', 'variations.product_variation', 'variations.group_prices', 'product_locations'])
                ->get();

            $all_categories = Category::catAndSubCategories($business_id);

            $categories = [];
            $sub_categories = [];
            foreach ($all_categories as $category) {
                $categories[$category['id']] = $category['name'];

                if (! empty($category['sub_categories'])) {
                    foreach ($category['sub_categories'] as $sub_category) {
                        $sub_categories[$category['id']][$sub_category['id']] = $sub_category['name'];
                    }
                }
            }

            $location_id = $this->getLocationId(request());
            $brands = Brands::forDropdown($business_id, false, false, $location_id);

            $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
            $taxes = $tax_dropdown['tax_rates'];
            $tax_attributes = $tax_dropdown['attributes'];

            $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');
            $business_locations = BusinessLocation::forDropdown($business_id);

            return view('product.bulk-edit')->with(compact(
                'products',
                'categories',
                'brands',
                'taxes',
                'tax_attributes',
                'sub_categories',
                'price_groups',
                'business_locations'
            ));
        }
    }

    /**
     * Updates multiple products at once.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkUpdate(Request $request)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $products = $request->input('products');
            $business_id = $request->session()->get('user.business_id');

            DB::beginTransaction();
            foreach ($products as $id => $product_data) {
                $update_data = [
                    'category_id' => $product_data['category_id'],
                    'sub_category_id' => $product_data['sub_category_id'],
                    'brand_id' => $product_data['brand_id'],
                    'tax' => $product_data['tax'],
                ];

                //Update product
                $product = Product::where('business_id', $business_id)
                    ->findOrFail($id);

                $product->update($update_data);

                //Add product locations
                $product_locations = ! empty($product_data['product_locations']) ?
                    $product_data['product_locations'] : [];
                $product->product_locations()->sync($product_locations);

                $variations_data = [];

                //Format variations data
                foreach ($product_data['variations'] as $key => $value) {
                    $variation = Variation::where('product_id', $product->id)->findOrFail($key);
                    $variation->default_purchase_price = $this->productUtil->num_uf($value['default_purchase_price']);
                    $variation->dpp_inc_tax = $this->productUtil->num_uf($value['dpp_inc_tax']);
                    $variation->profit_percent = $this->productUtil->num_uf($value['profit_percent']);
                    $variation->default_sell_price = $this->productUtil->num_uf($value['default_sell_price']);
                    $variation->sell_price_inc_tax = $this->productUtil->num_uf($value['sell_price_inc_tax']);
                    $variations_data[] = $variation;

                    //Update price groups
                    if (! empty($value['group_prices']) && is_array($value['group_prices'])) {
                        foreach ($value['group_prices'] as $k => $v) {
                            // Handle both simple value format and array format
                            $price_value = is_array($v) ? (isset($v['price']) ? $v['price'] : (isset($v['price_inc_tax']) ? $v['price_inc_tax'] : null)) : $v;
                            
                            // Skip if price is empty or null
                            if ($price_value === '' || $price_value === null) {
                                // Delete existing group price if explicitly empty
                                VariationGroupPrice::where('variation_id', $variation->id)
                                    ->where('price_group_id', $k)
                                    ->delete();
                                continue;
                            }
                            
                            $price = $this->productUtil->num_uf($price_value);
                            
                            // Only save if price is greater than 0
                            if ($price > 0) {
                                $price_type = (is_array($v) && isset($v['price_type'])) ? $v['price_type'] : 'fixed';
                                
                                VariationGroupPrice::updateOrCreate(
                                    ['price_group_id' => $k, 'variation_id' => $variation->id],
                                    [
                                        'price_inc_tax' => $price,
                                        'price_type' => $price_type
                                    ]
                                );
                            } else {
                                // Delete if price is 0 or negative
                                VariationGroupPrice::where('variation_id', $variation->id)
                                    ->where('price_group_id', $k)
                                    ->delete();
                            }
                        }
                    }
                }
                $product->variations()->saveMany($variations_data);
            }
            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.updated_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect('products')->with('status', $output);
    }

    /**
     * Adds product row to edit in bulk edit product form
     *
     * @param  int  $product_id
     * @return \Illuminate\Http\Response
     */
    public function getProductToEdit($product_id)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        $product = Product::where('business_id', $business_id)
            ->with(['variations', 'variations.product_variation', 'variations.group_prices'])
            ->findOrFail($product_id);
        $all_categories = Category::catAndSubCategories($business_id);

        $categories = [];
        $sub_categories = [];
        foreach ($all_categories as $category) {
            $categories[$category['id']] = $category['name'];

            if (! empty($category['sub_categories'])) {
                foreach ($category['sub_categories'] as $sub_category) {
                    $sub_categories[$category['id']][$sub_category['id']] = $sub_category['name'];
                }
            }
        }

        $location_id = $this->getLocationId(request());
        $brands = Brands::forDropdown($business_id, false, false, $location_id);

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        $taxes = $tax_dropdown['tax_rates'];
        $tax_attributes = $tax_dropdown['attributes'];

        $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('product.partials.bulk_edit_product_row')->with(compact(
            'product',
            'categories',
            'brands',
            'taxes',
            'tax_attributes',
            'sub_categories',
            'price_groups',
            'business_locations'
        ));
    }

    /**
     * Gets the sub units for the given unit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $unit_id
     * @return \Illuminate\Http\Response
     */
    public function getSubUnits(Request $request)
    {
        if (! empty($request->input('unit_id'))) {
            $unit_id = $request->input('unit_id');
            $business_id = $request->session()->get('user.business_id');
            $sub_units = $this->productUtil->getSubUnits($business_id, $unit_id, true);

            //$html = '<option value="">' . __('lang_v1.all') . '</option>';
            $html = '';
            if (! empty($sub_units)) {
                foreach ($sub_units as $id => $sub_unit) {
                    $html .= '<option value="' . $id . '">' . $sub_unit['name'] . '</option>';
                }
            }

            return $html;
        }
    }

    public function updateProductLocation(Request $request)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $selected_products = $request->input('products');
            $update_type = $request->input('update_type');
            $location_ids = $request->input('product_location');

            $business_id = $request->session()->get('user.business_id');

            $product_ids = explode(',', $selected_products);

            $products = Product::where('business_id', $business_id)
                ->whereIn('id', $product_ids)
                ->with(['product_locations'])
                ->get();
            DB::beginTransaction();
            foreach ($products as $product) {
                $product_locations = $product->product_locations->pluck('id')->toArray();

                if ($update_type == 'add') {
                    $product_locations = array_unique(array_merge($location_ids, $product_locations));
                    $product->product_locations()->sync($product_locations);
                } elseif ($update_type == 'remove') {
                    foreach ($product_locations as $key => $value) {
                        if (in_array($value, $location_ids)) {
                            unset($product_locations[$key]);
                        }
                    }
                    $product->product_locations()->sync($product_locations);
                }
            }
            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('lang_v1.updated_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    public function productStockHistory($id)
    {
        if (! auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {

            //for ajax call $id is variation id else it is product id
            $stock_details = $this->productUtil->getVariationStockDetails($business_id, $id, request()->input('location_id'));
            $stock_history = $this->productUtil->getVariationStockHistory($business_id, $id, request()->input('location_id'));

            //if mismach found update stock in variation location details
            if (isset($stock_history[0]) && (float) $stock_details['current_stock'] != (float) $stock_history[0]['stock']) {
                VariationLocationDetails::where(
                    'variation_id',
                    $id
                )
                    ->where('location_id', request()->input('location_id'))
                    ->update(['qty_available' => $stock_history[0]['stock']]);
                $stock_details['current_stock'] = $stock_history[0]['stock'];
            }

            return view('product.stock_history_details')
                ->with(compact('stock_details', 'stock_history'));
        }

        $product = Product::where('business_id', $business_id)
            ->with(['variations', 'variations.product_variation'])
            ->findOrFail($id);

        //Get all business locations
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('product.stock_history')
            ->with(compact('product', 'business_locations'));
    }

    /**
     * Toggle WooComerce sync
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toggleWooCommerceSync(Request $request)
    {
        try {
            $selected_products = $request->input('woocommerce_products_sync');
            $woocommerce_disable_sync = $request->input('woocommerce_disable_sync');

            $business_id = $request->session()->get('user.business_id');
            $product_ids = explode(',', $selected_products);

            DB::beginTransaction();
            if ($this->moduleUtil->isModuleInstalled('Woocommerce')) {
                Product::where('business_id', $business_id)
                    ->whereIn('id', $product_ids)
                    ->update(['woocommerce_disable_sync' => $woocommerce_disable_sync]);
            }
            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Function to download all products in xlsx format
     */
    public function downloadExcel()
    {
        $is_admin = $this->productUtil->is_admin(auth()->user());
        if (! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $filename = 'products-export-' . \Carbon::now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new ProductsExport, $filename);
    }


    public function updatePricePopUP(Request $request)
    {
        //     if (! auth()->user()->can('product.update')) {
        //     abort(403, 'Unauthorized action.');
        // }
        $business_id = $request->session()->get('user.business_id');
        $validate = Validator::make($request->all(), [
            'prices' => 'required|array',
            'product_id' => 'required|exists:products,id',
        ]);
        // return $request->all();
        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => $validate->errors()]);
        }
        $product_id = $request->input('product_id');
        $product = Product::where('business_id', $business_id)->findOrFail($product_id);
        $data = $request->input('prices');
        foreach ($data as $variation_id => $sell_price_inc_tax) {
            if ($sell_price_inc_tax) {
                $variation = Variation::where('id', $variation_id)
                    ->whereHas('product', function($query) use ($business_id) {
                        $query->where('business_id', $business_id);
                    })
                    ->first();
                if ($variation) {
                    $sell_price_inc_tax = $this->productUtil->num_uf($sell_price_inc_tax);
                    
                    // Update sell_price_inc_tax
                    $variation->sell_price_inc_tax = $sell_price_inc_tax;
                    $variation->default_sell_price = $sell_price_inc_tax;
                    $variation->save();
                }
            }
        }
        return response()->json(['status' => true, 'message' => 'Prices updated successfully']);
    }
    public function showChildProduct($id)
    {
        $business_id = request()->session()->get('user.business_id');

        // Get all variations for this product
        $variation = Variation::with([
                'media' => function ($q) {
                    $q->select('id', 'file_name', 'model_id', 'model_type');
                },
                'product' => function ($row) {
                    $row->select('id', 'name', 'image');
                },
                'product_variation',
                'variation_location_details',
                'group_prices',
            ])
            ->where('product_id', $id)
            ->whereNull('deleted_at');

        // Filter out discontinued variations if column exists
        if (Schema::hasColumn('variations', 'is_discontinued')) {
            $variation->where(function ($q) {
                $q->where('is_discontinued', '!=', 1)
                    ->orWhereNull('is_discontinued');
            });
        }

        $variation = $variation->get();

        // Get enabled modules and price groups for the view
        $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];
        $price_groups = collect([]);
        if (in_array('group_pricing', $enabled_modules)) {
            $price_groups = SellingPriceGroup::where('business_id', $business_id)
                ->active()
                ->get();
        }

        return view('product.partials.show_child_product')
            ->with(compact('variation', 'enabled_modules', 'price_groups'));
    }

    public function deleteProductImage($id)
    {
        try {
            $product = Product::findOrFail($id);
            $media = Media::where('model_id', $id);
            $media->delete();
            $product->image = null;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => __('Product Image Deleted Successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong')
            ]);
        }
    }
    public function removeGalleryImage(Request $request)
    {
        $deleted_image = ProductGalleryImage::where('id', $request->image_id)->delete();
        if ($deleted_image) {
            return response()->json(['status' => true, 'message' => 'Gallery images updated successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong']);
        }
    }

    // ------------------------------- Session based product order limit -------------------------------

    /**
     * Create product order limit
     */
    public function createProductOrderLimitRule(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'product_id' => 'nullable|exists:products,id',
            'variant_id' => 'nullable|exists:variations,id',
            'order_limit' => 'nullable|integer',
            'sale_limit' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => $validate->errors()]);
        }

        $data = $request->all();

        // Clean up the data - remove form fields that shouldn't be saved to database
        unset($data['search_product']);
        unset($data['search_fields']);
        unset($data['_token']);

        // Convert empty strings to null for optional fields
        $data['product_id'] = !empty($data['product_id']) ? $data['product_id'] : null;
        $data['variant_id'] = !empty($data['variant_id']) ? $data['variant_id'] : null;
        $data['order_limit'] = !empty($data['order_limit']) ? $data['order_limit'] : null;
        $data['start_date'] = !empty($data['start_date']) ? $data['start_date'] : null;
        $data['end_date'] = !empty($data['end_date']) ? $data['end_date'] : null;
        $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : true;

        if ($data['sale_limit'] != null) {
            if ($data['variant_id'] != null) {
                Variation::where('id',$data['variant_id'])->update(['var_maxSaleLimit' => $data['sale_limit']]);
            } elseif ($data['product_id'] != null) {
                Product::where('id',$data['product_id'])->update(['maxSaleLimit' => $data['sale_limit']]);
            }
        }
        
        unset($data['sale_limit']);


        // Check for date conflicts with existing rules
        $conflictQuery = ProductOrderLimit::where(function ($query) use ($data) {
            if (!empty($data['variant_id'])) {
                $query->where('variant_id', $data['variant_id']);
            } else {
                $query->where('product_id', $data['product_id'])
                    ->whereNull('variant_id');
            }
        });

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $conflictQuery->where(function ($q) use ($data) {
                $q->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                    ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                    ->orWhere(function ($subQ) use ($data) {
                        $subQ->where('start_date', '<=', $data['start_date'])
                            ->where('end_date', '>=', $data['end_date']);
                    });
            });
        }

        $conflicts = $conflictQuery->get();

        if ($conflicts->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Date range conflicts with existing rules for this product/variant'
            ]);
        }

        $data['created_by'] = auth()->id();
        $product_order_limit = ProductOrderLimit::create($data);

        // Update cart items with the new limit
        $affectedUsers = $this->updateCartItemsForNewLimit($product_order_limit);

        $message = 'Product order limit created successfully';
        if ($affectedUsers > 0) {
            $message .= ". Cart quantities adjusted for {$affectedUsers} user(s).";
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $product_order_limit,
            'affected_users' => $affectedUsers
        ]);
    }

    /**
     * Update product order limit
     */
    public function updateProductOrderLimitRule(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required|exists:product_order_limits,id',
            'product_id' => 'nullable|exists:products,id',
            'variant_id' => 'nullable|exists:variations,id',
            'order_limit' => 'nullable|integer',
            'sale_limit' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => $validate->errors()]);
        }

        $data = $request->all();

        // Clean up the data - remove form fields that shouldn't be saved to database
        unset($data['search_product']);
        unset($data['search_fields']);
        unset($data['_token']);

        // Convert empty strings to null for optional fields
        $data['product_id'] = !empty($data['product_id']) ? $data['product_id'] : null;
        $data['variant_id'] = !empty($data['variant_id']) ? $data['variant_id'] : null;
        $data['order_limit'] = !empty($data['order_limit']) ? $data['order_limit'] : null;
        $data['sale_limit'] = !empty($data['sale_limit']) ? $data['sale_limit'] : null;
        $data['start_date'] = !empty($data['start_date']) ? $data['start_date'] : null;
        $data['end_date'] = !empty($data['end_date']) ? $data['end_date'] : null;
        $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : true;

        if ($data['sale_limit'] != null) {
            if ($data['variant_id'] != null) {
                Variation::where('id',$data['variant_id'])->update(['var_maxSaleLimit' => $data['sale_limit']]);
            } elseif ($data['product_id'] != null) {
                Product::where('id',$data['product_id'])->update(['maxSaleLimit' => $data['sale_limit']]);
            }
        }
        
        unset($data['sale_limit']);

        $product_order_limit = ProductOrderLimit::findOrFail($request->id);

        // Check for date conflicts with other rules (excluding current rule)
        $conflictQuery = ProductOrderLimit::where('id', '!=', $request->id)
            ->where(function ($query) use ($data) {
                if (!empty($data['variant_id'])) {
                    $query->where('variant_id', $data['variant_id']);
                } else {
                    $query->where('product_id', $data['product_id'])
                        ->whereNull('variant_id');
                }
            });

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $conflictQuery->where(function ($q) use ($data) {
                $q->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                    ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                    ->orWhere(function ($subQ) use ($data) {
                        $subQ->where('start_date', '<=', $data['start_date'])
                            ->where('end_date', '>=', $data['end_date']);
                    });
            });
        }

        $conflicts = $conflictQuery->get();

        if ($conflicts->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Date range conflicts with existing rules for this product/variant'
            ]);
        }

        $data['updated_by'] = auth()->id();
        $product_order_limit->update($data);

        // Update cart items with the updated limit
        $affectedUsers = $this->updateCartItemsForNewLimit($product_order_limit);

        $message = 'Product order limit updated successfully';
        if ($affectedUsers > 0) {
            $message .= ". Cart quantities adjusted for {$affectedUsers} user(s).";
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $product_order_limit,
            'affected_users' => $affectedUsers
        ]);
    }

    /**
     * Deactivate product order limit
     */
    public function deleteProductOrderLimitRule(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required|exists:product_order_limits,id',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => $validate->errors()]);
        }

        $product_order_limit = ProductOrderLimit::findOrFail($request->id);
        $product_order_limit->deleted_by = auth()->id();
        // $product_order_limit->save();
        $product_order_limit->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product order limit deleted successfully'
        ]);
    }

    /**
     * Get product order limit by ID
     */
    public function getProductOrderLimitRule(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required|exists:product_order_limits,id',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => $validate->errors()]);
        }

        $product_order_limit = ProductOrderLimit::with(['product', 'variation'])->findOrFail($request->id);
        
        // Get the sale limit from product or variation
        $sale_limit = null;
        if ($product_order_limit->variant_id) {
            // If it's a variant, get the variant's sale limit
            $variation = $product_order_limit->variation;
            if ($variation) {
                $sale_limit = $variation->var_maxSaleLimit;
            }
        } else {
            // If it's a product, get the product's sale limit
            $product = $product_order_limit->product;
            if ($product) {
                $sale_limit = $product->maxSaleLimit;
            }
        }
        
        // Add sale limit to the response
        $product_order_limit->sale_limit = $sale_limit;

        return response()->json([
            'status' => true,
            'data' => $product_order_limit
        ]);
    }

    /**
     * Get all product order limit
     * Support Filter like Active, Inactive, All , Search , Pagination , Sorting , Yajra Datatable
     */
    public function getAllProductOrderLimit(Request $request)
{
    $query = Product::with(['variations', 'product_order_limits'])
        ->where(function($query) {
            $query->where('maxSaleLimit', '>', 0)
                ->orWhereHas('variations', function($q) {
                    $q->where('var_maxSaleLimit', '>', 0);
                });
        });

    // Filter by status
    if ($request->filled('status') && $request->status !== 'all') {
        $status = $request->status === 'active';
        $query->whereHas('product_order_limits', function($q) use ($status) {
            $q->where('is_active', $status);
        });
    }

    // Filter by product
    if ($request->filled('product_id')) {
        $query->where('id', $request->product_id);
    }

    // Search
   if ($request->filled('search.value')) {
    $search = $request->input('search.value');
    $query->where(function ($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere(function ($sub) use ($search) {
              $sub->whereHas('variations', function ($v) use ($search) {
                  $v->where('name', 'like', "%{$search}%");
              });
          });
    });
    }

    // Sort
    $query->orderBy($request->get('sort_by', 'name'), $request->get('sort_order', 'asc'));

    if ($request->ajax()) {
        return DataTables::eloquent($query)
            ->addColumn('product_id', fn($row) => $row->id) // Add product ID for all rows
            ->addColumn('rule_id', function($row) {
                $rule = optional($row->product_order_limits->where('is_active', true)->first());
                return $rule->id ?? null; // Add rule ID (null if no rule)
            })
            ->addColumn('product_name', fn($row) => $row->name ?? 'N/A')
            ->addColumn('variant_name', function($row) {
                $rule = optional($row->product_order_limits->where('is_active', true)->first());
                if ($rule->variant_id) {
                    $variant = $row->variations->where('id', $rule->variant_id)->first();
                    return $variant ? $variant->name : 'N/A';
                }
                return 'No Variant';
            })
            ->addColumn('sale_limit', fn($row) => $row->maxSaleLimit ?? 'No Parent Limit')
            ->addColumn('order_limit', function ($row) {
                $rule = optional($row->product_order_limits->where('is_active', true)->first());
                return $rule->order_limit ?? 'No Limit';
            })
            ->addColumn('start_date', function ($row) {
                $rule = optional($row->product_order_limits->where('is_active', true)->first());
                return $rule->start_date ? date('Y-m-d', strtotime($rule->start_date)) : 'N/A';
            })
            ->addColumn('end_date', function ($row) {
                $rule = optional($row->product_order_limits->where('is_active', true)->first());
                return $rule->end_date ? date('Y-m-d', strtotime($rule->end_date)) : 'N/A';
            })
            
            ->addColumn('status', function ($row) {
                $rule = optional($row->product_order_limits->where('is_active', true)->first());
                if ($rule->id) {
                    $statusClass = $rule->is_active ? 'success' : 'danger';
                    $statusText  = $rule->is_active ? 'Active' : 'Inactive';
                    return "<span class='label label-{$statusClass}'>{$statusText}</span>";
                }
                return "<span class='label label-warning'>No Rule</span>";
            })
            ->addColumn('created_at', function ($row) {
                $rule = optional($row->product_order_limits->where('is_active', true)->first());
                return $rule->created_at ? $rule->created_at->format('d-m-Y H:i') : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $rule = optional($row->product_order_limits->where('is_active', true)->first());
                if ($rule->id) {
                    return 
                           '<button type="button" class="btn btn-warning-gradient edit-sale-limit shadow-sm" data-product-id="' . $row->id . '" data-variant-id="' . ($rule->variant_id ?? '') . '" data-current-limit="' . ($rule->variant_id ? ($row->variations->where('id', $rule->variant_id)->first()->var_maxSaleLimit ?? 'N/A') : ($row->maxSaleLimit ?? 'N/A')) . '"><svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button> ' .
                           '<button type="button" class="btn btn-success-gradient add-rule-for-product shadow-sm" data-product-id="' . $row->id . '" data-variant-id="' . ($rule->variant_id ?? '') . '"><svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>';
                }
                return 
                       '<button type="button" class="btn btn-success-gradient add-rule-for-product shadow-sm" data-product-id="' . $row->id . '" title="Add New Rule"><svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>';
            }
            )
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    // For initial page load
    $perPage = $request->get('per_page', 15);
    $products = $query->paginate($perPage);

    $productsForDropdown = Product::select('id', 'name')
        ->where(function($query) {
            $query->where('maxSaleLimit', '>', 0)
                ->orWhereHas('variations', function($q) {
                    $q->where('var_maxSaleLimit', '>', 0);
                });
        })
        ->orderBy('name')
        ->get();

    return view('product.sale_limit_control.index', compact('products', 'productsForDropdown'));
}


    /**
     * Get consumer details for a specific rule OR product info if no rule exists
     */
    public function getConsumerDetails(Request $request)
    {
        // Check if this is a product info request (no rule exists)
        if ($request->has('product_id') && !$request->has('rule_id')) {
            $product = Product::findOrFail($request->product_id);
            
            // Get variant information if any, excluding DUMMY variants
            $variants = $product->variations()
                ->select('id', 'name', 'var_maxSaleLimit')
                ->where('name', 'not like', '%DUMMY%')
                ->get();
            
            return response()->json([
                'status' => true,
                'has_rules' => false,
                'data' => [
                    'product_name' => $product->name,
                    'variant_name' => 'No Variant',
                    'product_sale_limit' => $product->maxSaleLimit,
                    'variants' => $variants->map(function($variant) use ($product) {
                        return [
                            'id' => $variant->id,
                            'name' => $variant->name,
                            'sale_limit' => $variant->var_maxSaleLimit,
                            'edit_button' => '<button type="button" class="btn btn-warning btn-xs edit-variant-purchase-limit" data-product-id="' . $product->id . '" data-variant-id="' . $variant->id . '" data-variant-name="' . $variant->name . '" data-current-limit="' . ($variant->var_maxSaleLimit ?? 'No Limit') . '"><i class="fa fa-edit"></i> Edit</button>'
                        ];
                    })
                ]
            ]);
        }
        
        // Original logic for products with rules
        $request->validate([
            'rule_id' => 'required|integer|exists:product_order_limits,id'
        ]);

        $rule = ProductOrderLimit::with(['product', 'variation'])->find($request->rule_id);

        if (!$rule) {
            return response()->json([
                'status' => false,
                'message' => 'Rule not found'
            ]);
        }

        // Get all variants for this product with their purchase limits, excluding DUMMY variants
        $variants = $rule->product->variations()
            ->select('id', 'name', 'var_maxSaleLimit')
            ->where('name', 'not like', '%DUMMY%')
            ->get();

        // Get all rules for the same product and sale limit
        $allRules = ProductOrderLimit::where('product_id', $rule->product_id)
            ->when($rule->variant_id, function($query) use ($rule) {
                $query->where('variant_id', $rule->variant_id);
            })
            ->when(!$rule->variant_id, function($query) {
                $query->whereNull('variant_id');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get consumers for this specific rule
        $consumers = DB::table('product_order_limit_consumers as c')
            ->leftJoin('contacts as ct', 'c.consumer_id', '=', 'ct.id')
            ->leftJoin('products as p', function($join) use ($rule) {
                $join->on('p.id', '=', DB::raw($rule->product_id));
            })
            ->where('c.session_id', $rule->id)
            ->select([
                'c.id',
                'c.consumer_id',
                'c.order_count',
                'c.qty_count',
                'c.blocked_attempts',
                'c.blocked_at',
                'ct.name as contact_name',
                'ct.email as contact_email',
                'ct.mobile as contact_mobile',
                'p.maxSaleLimit as product_sale_limit'
            ])
            ->get();

        return response()->json([
            'status' => true,
            'has_rules' => true,
            'data' => [
                'product_name' => $rule->product ? $rule->product->name : 'N/A',
                'variant_name' => $rule->variation ? $rule->variation->name : 'N/A',
                'order_limit' => $rule->order_limit ?? 'No Limit',
                'product_sale_limit' => $rule->product ? $rule->product->maxSaleLimit : 'N/A',
                'status_html' => '<span class="label label-' . ($rule->is_active ? 'success' : 'danger') . '">' . ($rule->is_active ? 'Active' : 'Inactive') . '</span>',
                'start_date' => $rule->start_date ? \Carbon\Carbon::parse($rule->start_date)->format('Y-m-d H:i') : 'N/A',
                'end_date' => $rule->end_date ? \Carbon\Carbon::parse($rule->end_date)->format('Y-m-d H:i') : 'N/A',
                'created_at' => $rule->created_at->format('Y-m-d H:i'),
                'variants' => $variants->map(function($variant) use ($rule) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'sale_limit' => $variant->var_maxSaleLimit,
                        'edit_button' => '<button type="button" class="btn btn-warning btn-xs edit-variant-purchase-limit" data-product-id="' . $rule->product_id . '" data-variant-id="' . $variant->id . '" data-variant-name="' . $variant->name . '" data-current-limit="' . ($variant->var_maxSaleLimit ?? 'No Limit') . '"><i class="fa fa-edit"></i> Edit</button>'
                    ];
                }),
                'consumers' => $consumers,
                'all_rules' => $allRules->map(function($r) {
                    return [
                        'id' => $r->id,
                        'order_limit' => $r->order_limit ?? 'No Limit',
                        'start_date' => $r->start_date ? \Carbon\Carbon::parse($r->start_date)->format('Y-m-d H:i') : 'N/A',
                        'end_date' => $r->end_date ? \Carbon\Carbon::parse($r->end_date)->format('Y-m-d H:i') : 'N/A',
                        'is_active' => $r->is_active,
                        'created_at' => $r->created_at->format('Y-m-d H:i'),
                        'status_html' => '<span class="label label-' . ($r->is_active ? 'success' : 'danger') . '">' . ($r->is_active ? 'Active' : 'Inactive') . '</span>'
                    ];
                })
            ]
        ]);
    }



    /**
     * Get detailed logs for a specific consumer
     */
    public function getConsumerLogs(Request $request)
    {
        $request->validate([
            'consumer_id' => 'required|integer|exists:product_order_limit_consumers,id'
        ]);

        $consumer = DB::table('product_order_limit_consumers')
            ->where('id', $request->consumer_id)
            ->first();

        if (!$consumer) {
            return response()->json([
                'status' => false,
                'message' => 'Consumer record not found'
            ]);
        }

        $logs = [];
        if (!empty($consumer->meta)) {
            $meta = json_decode($consumer->meta, true);
            if (isset($meta['log_history']) && is_array($meta['log_history'])) {
                $logs = $meta['log_history'];
            }
        }

        return response()->json([
            'status' => true,
            'data' => [
                'logs' => $logs
            ]
        ]);
    }

    /**
     * Get variations for a product
     */
    public function getVariations($productId)
    {
        $variations = Variation::where('product_id', $productId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($variations);
    }

    /**
     * Update cart items when only sale limit changes (not order limit rules)
     */
    public function updateCartItemsForSaleLimitChange($productId, $variantId = null, $newSaleLimit)
    {
        try {
            // Get all cart items that match this product/variant
            $cartItemsQuery = DB::table('cart_items')
                ->join('variations', 'cart_items.variation_id', '=', 'variations.id')
                ->where('variations.product_id', $productId);

            if ($variantId) {
                $cartItemsQuery->where('variations.id', $variantId);
            }

            $cartItems = $cartItemsQuery->get();
            $affectedUsers = 0;
            $affectedUserIds = [];

            foreach ($cartItems as $cartItem) {
                // If cart quantity exceeds new sale limit, reduce it
                if ($cartItem->quantity > $newSaleLimit) {
                    $newQuantity = $newSaleLimit;

                    // Update cart item quantity
                    DB::table('cart_items')
                        ->where('id', $cartItem->id)
                        ->update([
                            'quantity' => $newQuantity,
                            'updated_at' => now()
                        ]);

                    if (!in_array($cartItem->user_id, $affectedUserIds)) {
                        $affectedUsers++;
                        $affectedUserIds[] = $cartItem->user_id;
                    }
                }
            }

            return $affectedUsers;
        } catch (\Throwable $th) {
            \Log::error('Error updating cart items for sale limit change: ' . $th->getMessage());
            return 0;
        }
    }

    /**
     * Update cart items when a new order limit is created or updated
     */
    public function updateCartItemsForNewLimit($productOrderLimit = null, $productId = null, $maxSaleLimit = null)
    {
        try {
            // Get all cart items that match this product/variant
            $cartItemsQuery = DB::table('cart_items')
                ->join('variations', 'cart_items.variation_id', '=', 'variations.id')
                ->where('variations.product_id', $productId ?? $productOrderLimit->product_id);

            if ($productId) {
                $cartItemsQuery->where('variations.product_id', $productId);
            }

            if ($maxSaleLimit) {
                $cartItemsQuery->where('variations.var_maxSaleLimit', $maxSaleLimit ?? $productOrderLimit->order_limit);
            }

            $cartItems = $cartItemsQuery->get();
            $affectedUsers = 0;
            $affectedUserIds = [];

            foreach ($cartItems as $cartItem) {
                $wasAdjusted = $this->adjustCartItemQuantity($cartItem, $productOrderLimit);
                if ($wasAdjusted && !in_array($cartItem->user_id, $affectedUserIds)) {
                    $affectedUsers++;
                    $affectedUserIds[] = $cartItem->user_id;
                }
            }

            return $affectedUsers;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Adjust cart item quantity based on new order limit
     */
    private function adjustCartItemQuantity($cartItem, $productOrderLimit)
    {
        // Get the product and variation details
        $product = Product::find($productOrderLimit->product_id);
        $variation = Variation::find($cartItem->variation_id);

        if (!$product || !$variation) {
            return false;
        }

        // Get max sale limit (product level or variant level)
        $maxSaleLimit = $variation->var_maxSaleLimit ?? $product->maxSaleLimit ?? null;

        if (!$maxSaleLimit) {
            return false; // No max sale limit set, no adjustment needed
        }

        // Check if the limit rule is currently active
        $currentTime = now();
        $startTime = $productOrderLimit->start_date ? Carbon::parse($productOrderLimit->start_date) : Carbon::parse('2000-01-01');
        $endTime = $productOrderLimit->end_date ? Carbon::parse($productOrderLimit->end_date) : Carbon::parse('2099-12-31');

        if (!$currentTime->between($startTime, $endTime) || !$productOrderLimit->is_active) {
            return false; // Rule is not active, no adjustment needed
        }

        // Calculate allowed quantity based on order limit
        $orderLimit = $productOrderLimit->order_limit;
        if (!$orderLimit) {
            return false; // No order limit set
        }

        // Get user's current consumption for this product/variant
        $userConsumption = DB::table('product_order_limit_consumers')
            ->where('session_id', $productOrderLimit->id)
            ->where('consumer_id', $cartItem->user_id)
            ->first();

        $currentOrderCount = $userConsumption->order_count ?? 0;
        $currentQtyCount = $userConsumption->qty_count ?? 0;

        // Calculate remaining orders and allowed quantity
        $remainingOrders = max(0, $orderLimit - $currentOrderCount);
        $totalAllowedQty = $remainingOrders * $maxSaleLimit;
        $remainingQty = max(0, $totalAllowedQty - $currentQtyCount);

        // If cart quantity exceeds remaining allowed quantity, reduce it
        if ($cartItem->quantity > $remainingQty) {
            $newQuantity = max(0, $remainingQty);

            // Update cart item quantity
            DB::table('cart_items')
                ->where('id', $cartItem->id)
                ->update([
                    'quantity' => $newQuantity,
                    'updated_at' => now()
                ]);

            // Log the adjustment
            $this->logCartAdjustment($cartItem, $newQuantity, $productOrderLimit);

            return true; // Cart item was adjusted
        }

        return false; // No adjustment needed
    }

    /**
     * Log cart adjustment for audit trail
     */
    private function logCartAdjustment($cartItem, $newQuantity, $productOrderLimit)
    {
        $adjustmentData = [
            'cart_item_id' => $cartItem->id,
            'user_id' => $cartItem->user_id,
            'product_id' => $productOrderLimit->product_id,
            'variant_id' => $productOrderLimit->variant_id,
            'old_quantity' => $cartItem->quantity,
            'new_quantity' => $newQuantity,
            'limit_rule_id' => $productOrderLimit->id,
            'adjusted_at' => now()->toISOString(),
            'reason' => 'Order limit rule applied'
        ];

        // Store in meta field or create a separate log table
        DB::table('product_order_limit_consumers')
            ->where('session_id', $productOrderLimit->id)
            ->where('consumer_id', $cartItem->user_id)
            ->update([
                'meta' => json_encode($adjustmentData)
            ]);

        // Send notification to user about cart adjustment
        // $this->sendCartAdjustmentNotification($cartItem, $newQuantity, $productOrderLimit);
    }

    /**
     * Send notification to user about cart adjustment
     */
    private function sendCartAdjustmentNotification($cartItem, $newQuantity, $productOrderLimit)
    {
        $product = Product::find($productOrderLimit->product_id);
        $variation = Variation::find($cartItem->variation_id);

        $productName = $variation ? $variation->name : ($product ? $product->name : 'Product');

        $message = "Your cart quantity for '{$productName}' has been adjusted from {$cartItem->quantity} to {$newQuantity} due to a new order limit rule.";

        // You can implement your notification system here
        // For example, using Laravel's notification system or broadcasting

        // Store notification in database
        DB::table('notifications')->insert([
            'user_id' => $cartItem->user_id,
            'type' => 'cart_adjustment',
            'title' => 'Cart Quantity Adjusted',
            'message' => $message,
            'data' => json_encode([
                'cart_item_id' => $cartItem->id,
                'product_id' => $productOrderLimit->product_id,
                'variant_id' => $productOrderLimit->variant_id,
                'old_quantity' => $cartItem->quantity,
                'new_quantity' => $newQuantity,
                'limit_rule_id' => $productOrderLimit->id
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Update only the sale limit for a product or variant
     */
    public function updateSaleLimit(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:variations,id',
            'sale_limit' => 'required|integer|min:1',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => $validate->errors()]);
        }

        try {
            DB::beginTransaction();

            if ($request->filled('variant_id')) {
                // Update variant sale limit
                $variation = Variation::findOrFail($request->variant_id);
                $variation->var_maxSaleLimit = $request->sale_limit;
                $variation->save();
                
                $message = "Sale limit updated for variant '{$variation->name}' to {$request->sale_limit}";
            } else {
                // Update product sale limit
                $product = Product::findOrFail($request->product_id);
                $product->maxSaleLimit = $request->sale_limit;
                $product->save();
                
                $message = "Sale limit updated successfully";
            }

            // Update cart items with the new limit - only adjust quantities, not order limits
            $affectedUsers = $this->updateCartItemsForSaleLimitChange($request->product_id, $request->variant_id, $request->sale_limit);

            if ($affectedUsers > 0) {
                $message .= ". Cart quantities adjusted for {$affectedUsers} user(s).";
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $message,
                'affected_users' => $affectedUsers
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while updating sale limit'
            ]);
        }
    }

    /**
     * Update variant purchase limit
     */
    public function updateVariantPurchaseLimit(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:variations,id',
            'purchase_limit' => 'required|integer|min:1',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => $validate->errors()]);
        }

        try {
            DB::beginTransaction();

            // Update variant purchase limit
            $variation = Variation::findOrFail($request->variant_id);
            $variation->var_maxSaleLimit = $request->purchase_limit;
            $variation->save();
            
            $message = "Purchase limit updated for variant '{$variation->name}' to {$request->purchase_limit}";

            // Update cart items with the new limit - only adjust quantities, not order limits
            $affectedUsers = $this->updateCartItemsForSaleLimitChange($request->product_id, $request->variant_id, $request->purchase_limit);

            if ($affectedUsers > 0) {
                $message .= ". Cart quantities adjusted for {$affectedUsers} user(s).";
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $message,
                'affected_users' => $affectedUsers
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while updating variant purchase limit'
            ]);
        }
    }

    /**
     * Get product details for adding rules
     */
    public function getProductDetailsForRule(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:variations,id',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => $validate->errors()]);
        }

        $product = Product::findOrFail($request->product_id);
        $variant = null;
        $sale_limit = null;

        if ($request->filled('variant_id')) {
            $variant = Variation::find($request->variant_id);
            if ($variant) {
                $sale_limit = $variant->var_maxSaleLimit;
            }
        } else {
            $sale_limit = $product->maxSaleLimit;
        }

        return response()->json([
            'status' => true,
            'data' => [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'variant_id' => $variant ? $variant->id : null,
                'variant_name' => $variant ? $variant->name : null,
                'sale_limit' => $sale_limit
            ]
        ]);
    }
    public function getLocationId($request)
    {
        $user = auth()->user();
        $business_id = $request->session()->get('user.business_id');
        
        // Check if user is super admin or has access to all locations
        $is_super_admin = $user->can('access_all_locations') || $user->can('admin');
        
        if ($is_super_admin) {
            return null;
        }
        
        // For regular users, auto-detect location
        $permitted_locations = $user->permitted_locations($business_id);
        
        if ($permitted_locations == 'all') {
            // User has access to all locations, get first available location
            $default_location = BusinessLocation::where('business_id', $business_id)
                ->where('is_active', 1)
                ->first();
            return $default_location ? $default_location->id : null;
        } elseif (is_array($permitted_locations) && !empty($permitted_locations)) {
            // User has specific location permissions, use the first one
            return $permitted_locations[0];
        }
        
        return null;
    }

    /**
     * Save product state restrictions
     *
     * @param int $productId
     * @param Request $request
     * @return void
     */
    private function saveProductStates($productId, Request $request)
    {
        // Get the state_check value
        $stateCheck = $request->input('state_check', 'all');
        
        // Update the product's state_check column
        Product::where('id', $productId)->update(['state_check' => $stateCheck]);

        // Delete existing state restrictions for this product
        \App\ProductState::where('product_id', $productId)->delete();

        // If state_check is 'in' or 'not_in', save the selected states
        if (in_array($stateCheck, ['in', 'not_in']) && $request->has('states')) {
            $states = $request->input('states', []);
            
            foreach ($states as $state) {
                if (!empty($state)) {
                    \App\ProductState::create([
                        'product_id' => $productId,
                        'state' => $state
                    ]);
                }
            }
        }
    }




       /**
     * Summary of exportProduct
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportProductByHttp($id){
        $productId = $id;
        $product = Product::with(
            'webcategories', 
            'brand',
            'product_locations',
            'category',
            'variations',
            'variations.group_prices',
            'variations.group_prices.groupInfo',
            'variations.media',
            'product_gallery_images',
            'variations.variation_location_details',
            'variations.group_prices',
            'variations.group_prices.groupInfo',
        )
        ->wherehas('product_locations',function($q) {
            $q->where('product_locations.location_id',config('services.b2b.location_id'));
        })
        ->where('id',$productId)->first();
        if($product){
            return response()->json(['status' => true, 'data' => $product]);
        }
        return response()->json(['status' => false, 'message' => 'Product not found', 'error' => 'Product not found']);
    }
    public function storeFromDifferentProjectByHttp(Request $request , $productId)
    {
        try {
            Log::info('storeFromDifferentProjectByHttp', ['productId' => $productId]);
            $response = Http::get('https://erp.gohunterdistro.com/api/export-product/'.$productId, $request->all());
            $responseData = $response->json();
            Log::info('response', ['response' => $responseData]);
            
            // Extract the 'data' part from the response
            if (isset($responseData['data']) && !empty($responseData['data'])) {
                $productData = $responseData['data'];
                $result = $this->importProductFromJson($productData);
                return $result;
            } else {
                return [
                    'success' => 0,
                    'msg' => 'No product data found in response',
                ];
            }
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            return [
                'success' => 0,
                'msg' => 'Error importing product: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Import product from JSON response (from another project)
     * 
     * This function maps product data from a JSON response and imports it into the system.
     * It handles:
     * - Creating/updating brands and categories
     * - Creating products with all fields
     * - Handling variable product variations with group prices
     * - Downloading and storing images (product, variation, gallery)
     * 
     * Usage example:
     * $jsonResponse = '{"status": true, "data": {...}}';
     * $productData = json_decode($jsonResponse, true)['data'];
     * $controller = new ProductController($productUtil, $moduleUtil);
     * $result = $controller->importProductFromJson($productData);
     * 
     * @param array $productData - The product data from JSON response (the 'data' part)
     * @return array ['success' => 1|0, 'msg' => string, 'product' => Product|null]
     */
    public function importProductFromJson($productData)
    {
        try {
            // Hardcoded values as per requirements
            $business_id = 1;
            $location_id = 1;
            $user_id = 1;

            DB::beginTransaction();

            // Step 1: Create or update Brand
            $brand_id = null;
            if (!empty($productData['brand']) && !empty($productData['brand']['name'])) {
                $brand = Brands::updateOrCreate(
                    [
                        'business_id' => $business_id,
                        'location_id' => $location_id,
                        'name' => $productData['brand']['name']
                    ],
                    [
                        'description' => $productData['brand']['description'] ?? null,
                        'slug' => $productData['brand']['slug'] ?? $this->slugMakerForBrand($productData['brand']['name'], $location_id),
                        'visibility' => $productData['brand']['visibility'] ?? 'public',
                        'created_by' => $user_id,
                    ]
                );
                $brand_id = $brand->id;

                // Download brand logo if exists (if it's a URL)
                if (!empty($productData['brand']['logo'])) {
                    $logo_value = $productData['brand']['logo'];
                    // If it's a URL, download it; otherwise use the filename as-is
                    if (filter_var($logo_value, FILTER_VALIDATE_URL)) {
                        $logo_filename = $this->downloadAndStoreImageFromUrl($logo_value, $brand->id, false, $brand->slug ?? 'brand');
                        if ($logo_filename) {
                            $brand->logo = $logo_filename;
                            $brand->save();
                        }
                    } else {
                        // It's already a filename, use it directly
                        $brand->logo = $logo_value;
                        $brand->save();
                    }
                }
            }

            // Step 2: Create or update Category
            $category_id = null;
            $custom_sub_categories = [];
            
            if (!empty($productData['category']) && !empty($productData['category']['name'])) {
                $category = Category::updateOrCreate(
                    [
                        'business_id' => $business_id,
                        'location_id' => $location_id,
                        'name' => $productData['category']['name']
                    ],
                    [
                        'slug' => $productData['category']['slug'] ?? $this->slugMakerForCategory($productData['category']['name']),
                        'parent_id' => $productData['category']['parent_id'] ?? 0,
                        'visibility' => $productData['category']['visibility'] ?? 'public',
                        'category_type' => 'product',
                        'description' => $productData['category']['description'] ?? null,
                        'created_by' => $user_id,
                    ]
                );
                $category_id = $category->id;
                $custom_sub_categories[] = $category->id;
            }

            // Handle webcategories
            if (!empty($productData['webcategories']) && is_array($productData['webcategories'])) {
                foreach ($productData['webcategories'] as $webcat) {
                    if (empty($webcat['name'])) {
                        continue; // Skip if name is missing
                    }
                    $webcategory = Category::updateOrCreate(
                        [
                            'business_id' => $business_id,
                            'location_id' => $location_id,
                            'name' => $webcat['name']
                        ],
                        [
                            'slug' => $webcat['slug'] ?? $this->slugMakerForCategory($webcat['name']),
                            'parent_id' => $webcat['parent_id'] ?? 0,
                            'visibility' => $webcat['visibility'] ?? 'public',
                            'category_type' => 'product',
                            'description' => $webcat['description'] ?? null,
                            'created_by' => $user_id,
                        ]
                    );
                    if (!in_array($webcategory->id, $custom_sub_categories)) {
                        $custom_sub_categories[] = $webcategory->id;
                    }
                }
            }

            // Step 3: Prepare product data
            $form_fields = [
                'name', 'slug', 'brand_id', 'unit_id', 'category_id', 'tax', 'type',
                'barcode_type', 'sku', 'alert_quantity', 'tax_type', 'weight',
                'length', 'width', 'height',
                'product_description', 'sub_unit_ids', 'preparation_time_in_minutes',
                'product_custom_field1', 'product_custom_field2', 'product_custom_field3',
                'product_custom_field4', 'product_custom_field5', 'product_custom_field6',
                'product_custom_field7', 'product_custom_field8', 'product_custom_field9',
                'product_custom_field10', 'product_custom_field11', 'product_custom_field12',
                'product_custom_field13', 'product_custom_field14', 'product_custom_field15',
                'product_custom_field16', 'product_custom_field17', 'product_custom_field18',
                'product_custom_field19', 'product_custom_field20', 'productVisibility',
                'ml', 'ct', 'locationTaxType', 'maxSaleLimit', 'barcode_no', 'enable_selling'
            ];

            // Validate required fields
            if (empty($productData) || !is_array($productData)) {
                throw new \Exception('Invalid product data: data must be an array');
            }
            
            if (empty($productData['name'])) {
                throw new \Exception('Product name is required. Received data: ' . json_encode(array_keys($productData)));
            }

            $product_details = [];
            foreach ($form_fields as $field) {
                if (isset($productData[$field])) {
                    $product_details[$field] = $productData[$field];
                }
            }

            // Ensure name is set
            if (!isset($product_details['name'])) {
                $product_details['name'] = $productData['name'];
            }

            // Set required fields
            $product_details['business_id'] = $business_id;
            $product_details['created_by'] = $user_id;
            $product_details['brand_id'] = $brand_id;
            $product_details['category_id'] = $category_id;
            $product_details['slug'] = $productData['slug'] ?? $this->slugMaker($product_details['name']);
            $product_details['enable_stock'] = $productData['enable_stock'] ?? 0;
            $product_details['not_for_selling'] = $productData['not_for_selling'] ?? 0;
            $product_details['is_inactive'] = $productData['is_inactive'] ?? 0;
            $product_details['type'] = $productData['type'] ?? 'single';
            $product_details['sku'] = $productData['sku'] ?? ' ';
            $product_details['locationTaxType'] = $productData['locationTaxType'] ?? [];
            $product_details['custom_sub_categories'] = $custom_sub_categories;

            // Handle numeric fields
            if (!empty($product_details['alert_quantity'])) {
                $product_details['alert_quantity'] = $this->productUtil->num_uf($product_details['alert_quantity']);
            }

            // Download product image if exists
            if (!empty($productData['image_url'])) {
                $image_filename = $this->downloadAndStoreImageFromUrl($productData['image_url'], null, false, $product_details['sku']);
                if ($image_filename) {
                    $product_details['image'] = $image_filename;
                }
            } elseif (!empty($productData['image'])) {
                $product_details['image'] = $productData['image'];
            }

            // Step 4: Create product
            $product = Product::create($product_details);

            // Attach webcategories
            if (!empty($custom_sub_categories)) {
                $product->webcategories()->sync($custom_sub_categories);
            }

            // Add product locations
            $product->product_locations()->sync([$location_id]);

            // Step 5: Handle variations
            if ($product->type == 'variable' && !empty($productData['variations'])) {
                $this->importVariableProductVariations($product, $productData['variations'], $business_id, $location_id);
            } elseif ($product->type == 'single' && !empty($productData['variations'][0])) {
                $variation_data = $productData['variations'][0];
                $this->productUtil->createSingleProductVariation(
                    $product->id,
                    $product->sku,
                    $variation_data['default_purchase_price'] ?? 0,
                    $variation_data['dpp_inc_tax'] ?? 0,
                    $variation_data['profit_percent'] ?? 0,
                    $variation_data['default_sell_price'] ?? 0,
                    $variation_data['sell_price_inc_tax'] ?? 0,
                    null,
                    $variation_data['var_barcode_no'] ?? null
                );

                // Handle group prices for single product variation
                if (!empty($variation_data['group_prices']) && is_array($variation_data['group_prices'])) {
                    $variation_model = Variation::where('product_id', $product->id)
                        ->where('sub_sku', $variation_data['sub_sku'] ?? $product->sku)
                        ->first();

                    if ($variation_model) {
                        foreach ($variation_data['group_prices'] as $group_price) {
                            if (!empty($group_price['group_info']) && !empty($group_price['price_inc_tax'])) {
                                $group_info = $group_price['group_info'];
                                // Get or create the price group
                                $price_group_id = $this->getOrCreatePriceGroup(
                                    $business_id,
                                    $group_info['name'] ?? 'Unknown',
                                    $group_info['description'] ?? null,
                                    $group_info['id'] ?? null,
                                    $group_info['is_active'] ?? 1
                                );

                                if ($price_group_id) {
                                    VariationGroupPrice::updateOrCreate(
                                        [
                                            'variation_id' => $variation_model->id,
                                            'price_group_id' => $price_group_id
                                        ],
                                        [
                                            'price_inc_tax' => $this->productUtil->num_uf($group_price['price_inc_tax']),
                                            'price_type' => $group_price['price_type'] ?? 'fixed',
                                        ]
                                    );
                                }
                            }
                        }

                        // Download variation images if exists
                        if (!empty($variation_data['media']) && is_array($variation_data['media'])) {
                            foreach ($variation_data['media'] as $media) {
                                if (!empty($media['display_url'])) {
                                    $this->downloadAndStoreImageFromUrl($media['display_url'], $variation_model->id, true, $variation_data['sub_sku'] ?? $product->sku);
                                }
                            }
                        }
                    }
                }
            }

            // Step 6: Handle gallery images
            if (!empty($productData['product_gallery_images']) && is_array($productData['product_gallery_images'])) {
                foreach ($productData['product_gallery_images'] as $gallery_image) {
                    if (!empty($gallery_image['image_url'])) {
                        $gallery_path = $this->downloadAndStoreGalleryImageFromUrl($gallery_image['image_url'], $product->id, $product->sku);
                        if ($gallery_path) {
                            \App\Models\ProductGalleryImage::create([
                                'product_id' => $product->id,
                                'image_path' => $gallery_path,
                            ]);
                        }
                    }
                }
            }

            event(new ProductsCreatedOrModified($product_details, 'added'));

            DB::commit();

            return [
                'success' => 1,
                'msg' => 'Product imported successfully',
                'product' => $product,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            return [
                'success' => 0,
                'msg' => 'Error importing product: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Import variable product variations with group prices
     */
    private function importVariableProductVariations($product, $variations, $business_id, $location_id)
    {
        // Group variations by product_variation_id
        $grouped_variations = [];
        foreach ($variations as $variation) {
            $pv_id = $variation['product_variation_id'] ?? 'default';
            if (!isset($grouped_variations[$pv_id])) {
                $grouped_variations[$pv_id] = [
                    'name' => 'Size', // Default variation name, adjust as needed
                    'variations' => []
                ];
            }
            $grouped_variations[$pv_id]['variations'][] = $variation;
        }

        // Convert to the format expected by createVariableProductVariations
        $input_variations = [];
            foreach ($grouped_variations as $pv_id => $group) {
            $variation_values = [];
            foreach ($group['variations'] as $var) {
                // Extract variation value from name (e.g., "Product Name (50g)" -> "50g")
                $variation_name = $var['name'] ?? 'DUMMY';
                $value = $variation_name;
                if (preg_match('/\(([^)]+)\)/', $variation_name, $matches)) {
                    $value = $matches[1];
                }

                $variation_values[] = [
                    'value' => $value,
                    'variation_value_id' => $var['variation_value_id'] ?? null,
                    'sub_sku' => $var['sub_sku'] ?? null,
                    'var_barcode_no' => $var['var_barcode_no'] ?? null,
                    'var_maxSaleLimit' => $var['var_maxSaleLimit'] ?? null,
                    'default_purchase_price' => $var['default_purchase_price'] ?? 0,
                    'dpp_inc_tax' => $var['dpp_inc_tax'] ?? 0,
                    'profit_percent' => $var['profit_percent'] ?? 0,
                    'default_sell_price' => $var['default_sell_price'] ?? 0,
                    'sell_price_inc_tax' => $var['sell_price_inc_tax'] ?? 0,
                ];
            }

            $input_variations[] = [
                'name' => $group['name'],
                'variations' => $variation_values
            ];
        }

        // Create variations
        $this->productUtil->createVariableProductVariations($product->id, $input_variations, 'sku', $business_id);

        // Handle group prices for each variation
        foreach ($variations as $variation) {
            if (!empty($variation['group_prices']) && is_array($variation['group_prices'])) {
                $variation_model = Variation::where('product_id', $product->id)
                    ->where('sub_sku', $variation['sub_sku'])
                    ->first();

                if ($variation_model) {
                    foreach ($variation['group_prices'] as $group_price) {
                        if (!empty($group_price['group_info']) && !empty($group_price['price_inc_tax'])) {
                            $group_info = $group_price['group_info'];
                            // Get or create the price group
                            $price_group_id = $this->getOrCreatePriceGroup(
                                $business_id,
                                $group_info['name'] ?? 'Unknown',
                                $group_info['description'] ?? null,
                                $group_info['id'] ?? null,
                                $group_info['is_active'] ?? 1
                            );

                            if ($price_group_id) {
                                VariationGroupPrice::updateOrCreate(
                                    [
                                        'variation_id' => $variation_model->id,
                                        'price_group_id' => $price_group_id
                                    ],
                                    [
                                        'price_inc_tax' => $this->productUtil->num_uf($group_price['price_inc_tax']),
                                        'price_type' => $group_price['price_type'] ?? 'fixed',
                                    ]
                                );
                            }
                        }
                    }

                    // Download variation images if exists
                    if (!empty($variation['media']) && is_array($variation['media'])) {
                        foreach ($variation['media'] as $media) {
                            if (!empty($media['display_url'])) {
                                $this->downloadAndStoreImageFromUrl($media['display_url'], $variation_model->id, true, $variation['sub_sku']);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Download and store image from URL
     */
    private function downloadAndStoreImageFromUrl($image_url, $model_id = null, $is_variation = false, $prefix = '')
    {
        try {
            $response = Http::timeout(30)->get($image_url);
            if ($response->successful()) {
                $image_data = $response->body();
                $year = date('Y');
                $month = date('m');
                $day = date('d');
                $url_parts = parse_url($image_url);
                $extension = 'jpg';
                if (isset($url_parts['path'])) {
                    $path_parts = pathinfo($url_parts['path']);
                    if (isset($path_parts['extension'])) {
                        $extension = strtolower($path_parts['extension']);
                    }
                }
                $base_name = basename($image_url, '.' . $extension);
                $base_name = Str::limit(Str::slug($base_name), 50, '');
                $filename = ($prefix ? $prefix . '_' : '') . time() . '_' . $base_name . '.' . $extension;
                $mediaURL = ($prefix ? $prefix . '_' : '') . time() . '_' . $base_name . '.' . $extension;
                $storagePath = public_path('uploads/img');
                $fullPath = $storagePath . '/' . $filename;
                
                if (!is_dir($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }
                
                file_put_contents($fullPath, $image_data);

                if ($model_id) {
                    Media::updateOrCreate(
                        ['model_id' => $model_id, 'model_type' => $is_variation ? "App\\Variation" : "App\\Product"],
                        [
                            'business_id' => 1,
                            'file_name' => $mediaURL,
                            'uploaded_by' => 1,
                        ]
                    );
                }
                
                return $filename;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to download image: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Download and store gallery image from URL
     */
    private function downloadAndStoreGalleryImageFromUrl($image_url, $product_id, $sku = '')
    {
        try {
            $response = Http::timeout(30)->get($image_url);
            if ($response->successful()) {
                $image_data = $response->body();
                $year = date('Y');
                $month = date('m');
                $day = date('d');
                $url_parts = parse_url($image_url);
                $extension = 'jpg';
                if (isset($url_parts['path'])) {
                    $path_parts = pathinfo($url_parts['path']);
                    if (isset($path_parts['extension'])) {
                        $extension = strtolower($path_parts['extension']);
                    }
                }
                $base_name = basename($image_url, '.' . $extension);
                $base_name = Str::limit(Str::slug($base_name), 50, '');
                $filename = ($sku ? $sku . '_' : '') . time() . '_' . $base_name . '.' . $extension;
                $storagePath = public_path('uploads/img/gallery');
                $fullPath = $storagePath . '/' . $filename;

                if (!is_dir($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }

                if (!file_exists($fullPath)) {
                    file_put_contents($fullPath, $image_data);
                }

                return 'uploads/img/gallery/' . $filename;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to download gallery image: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Get or create a selling price group
     * 
     * @param int $business_id
     * @param string $name
     * @param string|null $description
     * @param int|null $old_id - The ID from the source system (for reference, not used for matching)
     * @param int $is_active
     * @return int|null
     */
    private function getOrCreatePriceGroup($business_id, $name, $description = null, $old_id = null, $is_active = 1)
    {
        try {
            // First, try to find by name and business_id
            $price_group = SellingPriceGroup::where('business_id', $business_id)
                ->where('name', $name)
                ->first();

            if ($price_group) {
                return $price_group->id;
            }

            // If not found, create a new one
            $price_group = SellingPriceGroup::create([
                'business_id' => $business_id,
                'name' => $name,
                'description' => $description,
                'is_active' => $is_active,
            ]);

            // Create permission for the new price group
            if (class_exists(\Spatie\Permission\Models\Permission::class)) {
                try {
                    \Spatie\Permission\Models\Permission::create([
                        'name' => 'selling_price_group.' . $price_group->id
                    ]);
                } catch (\Exception $e) {
                    // Permission might already exist, ignore
                    \Log::warning('Could not create permission for price group: ' . $e->getMessage());
                }
            }

            return $price_group->id;
        } catch (\Exception $e) {
            \Log::error('Error creating price group: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Slug maker for brand
     */
    private function slugMakerForBrand($name, $location_id, $brand_id = null)
    {
        $baseName = Str::slug($name);
        $counter = 0;
        $newSlug = $baseName;
        while (Brands::where('slug', $newSlug)
            ->where('location_id', $location_id)
            ->when($brand_id, function ($query, $brand_id) {
                $query->where('id', '!=', $brand_id);
            })
            ->exists()
        ) {
            $counter++;
            $newSlug = $baseName . '-' . $counter;
        }
        return $newSlug;
    }

    /**
     * Slug maker for category
     */
    private function slugMakerForCategory($name, $category_id = null)
    {
        $baseName = Str::slug($name);
        $counter = 0;
        $newSlug = $baseName;
        while (Category::where('slug', $newSlug)
            ->when($category_id, function ($query, $category_id) {
                $query->where('id', '!=', $category_id);
            })
            ->exists()
        ) {
            $counter++;
            $newSlug = $baseName . '-' . $counter;
        }
        return $newSlug;
    }
    /**
     * Shows form to edit selling price for a product.
     *
     * @return \Illuminate\Http\Response
     */
    public function editSellingPrice()
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $product_id = request()->input('product_id');

        // Check if group_pricing module is enabled
        $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];
        $is_group_pricing_enabled = in_array('group_pricing', $enabled_modules);

        // Get enabled selling price groups only if module is enabled
        $price_groups = collect([]);
        if ($is_group_pricing_enabled) {
            $price_groups = SellingPriceGroup::where('business_id', $business_id)
                ->active()
                ->get();
        }

        $product = null;
        if ($product_id) {
            $product = Product::where('business_id', $business_id)
                ->with(['variations', 'variations.product_variation'])
                ->find($product_id);
        }

        // Get user's permitted locations
        $permitted_locations = auth()->user()->permitted_locations();
        $has_multiple_locations = false;
        $business_locations = collect([]);
        
        if ($permitted_locations == 'all') {
            $business_locations = BusinessLocation::forDropdown($business_id, false, false, false, false);
            $has_multiple_locations = $business_locations->count() > 1;
        } else {
            if (is_array($permitted_locations) && count($permitted_locations) > 1) {
                $has_multiple_locations = true;
                $business_locations = BusinessLocation::where('business_id', $business_id)
                    ->whereIn('id', $permitted_locations)
                    ->active()
                    ->pluck('name', 'id');
            }
        }

        // Get initial filter options (without location filter for now)
        $location_id = null;
        $categories = Category::forDropdown($business_id, 'product', $location_id);
        $brands = Brands::forDropdown($business_id, false, false, $location_id);

        return view('product.edit-selling-price')->with(compact(
            'product', 
            'price_groups', 
            'is_group_pricing_enabled',
            'business_locations',
            'has_multiple_locations',
            'categories',
            'brands'
        ));
    }

    /**
     * Get filter options (brands and categories) based on location
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilterOptions()
    {
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $location_id = request()->input('location_id');

            // Get brands filtered by location
            $brands = Brands::forDropdown($business_id, false, false, $location_id);

            // Get categories filtered by location
            $categories = Category::forDropdown($business_id, 'product', $location_id);

            return response()->json([
                'success' => true,
                'brands' => $brands,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ], 500);
        }
    }

    /**
     * Get filtered products/variations based on filters
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilteredProducts()
    {
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $location_id = request()->input('location_id');
            $product_type = request()->input('product_type');
            $brand_id = request()->input('brand_id');
            $category_id = request()->input('category_id');
            $page = request()->input('page', 1);
            $per_page = request()->input('per_page', 15);

            // Get user's permitted locations
            $permitted_locations = auth()->user()->permitted_locations();

            // Build query for variations
            $query = Variation::join('products as p', 'p.id', '=', 'variations.product_id')
                ->join('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
                ->leftJoin('variation_location_details as vld', function($join) use ($location_id) {
                    $join->on('variations.id', '=', 'vld.variation_id');
                    if ($location_id) {
                        $join->where('vld.location_id', '=', $location_id);
                    }
                })
                ->where('p.business_id', $business_id)
                ->where('p.type', '!=', 'modifier')
                ->whereNull('variations.deleted_at');

            // Apply product type filter
            if (!empty($product_type)) {
                $query->where('p.type', $product_type);
            } else {
                $query->whereIn('p.type', ['single', 'variable']);
            }

            // Apply brand filter
            if (!empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            // Apply category filter
            if (!empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }

            // Apply location filter for permitted locations
            if ($permitted_locations != 'all') {
                if ($location_id && in_array($location_id, $permitted_locations)) {
                    // Location is permitted, continue
                } else {
                    // Filter by permitted locations
                    $query->where(function($q) use ($permitted_locations, $location_id) {
                        if ($location_id) {
                            $q->where('vld.location_id', $location_id)
                              ->whereIn('vld.location_id', $permitted_locations);
                        } else {
                            $q->whereIn('vld.location_id', $permitted_locations)
                              ->orWhereNull('vld.location_id');
                        }
                    });
                }
            } elseif ($location_id) {
                // User has access to all locations, but filter by selected location
                $query->where(function($q) use ($location_id) {
                    $q->where('vld.location_id', $location_id)
                      ->orWhereNull('vld.location_id');
                });
            }

            // Get total count before pagination
            $total_count = $query->select('variations.id')->distinct()->count('variations.id');

            if ($total_count == 0) {
                return response()->json([
                    'success' => true,
                    'variations' => [],
                    'price_groups' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'per_page' => $per_page,
                        'total' => 0,
                        'total_pages' => 0
                    ]
                ]);
            }

            // Get variation IDs with pagination
            $variation_ids = $query->select('variations.id')
                ->distinct()
                ->orderBy('variations.id', 'desc')
                ->skip(($page - 1) * $per_page)
                ->take($per_page)
                ->pluck('id')
                ->toArray();

            // Get full Variation models with relationships
            $variations = Variation::whereIn('variations.id', $variation_ids)
                ->with(['media', 'product'])
                ->orderBy('id', 'desc')
                ->get();

            // Get price groups if module is enabled
            $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];
            $is_group_pricing_enabled = in_array('group_pricing', $enabled_modules);
            
            $price_groups = collect([]);
            if ($is_group_pricing_enabled) {
                $price_groups = SellingPriceGroup::where('business_id', $business_id)
                    ->active()
                    ->get();
            }

            // Get group prices for each variation
            $group_prices_data = [];
            
            if ($is_group_pricing_enabled && count($variation_ids) > 0) {
                $group_prices = VariationGroupPrice::whereIn('variation_id', $variation_ids)
                    ->get()
                    ->groupBy('variation_id');
                
                foreach ($group_prices as $var_id => $prices) {
                    foreach ($prices as $price) {
                        $group_prices_data[$var_id][$price->price_group_id] = [
                            'price' => $price->price_inc_tax,
                            'price_type' => $price->price_type
                        ];
                    }
                }
            }

            // Format variations data
            $formatted_variations = [];
            foreach ($variations as $variation) {
                // Get variation image using the image_url accessor
                $image_url = $variation->image_url;

                $variation_data = [
                    'id' => $variation->id,
                    'product_name' => $variation->product ? $variation->product->name : 'N/A',
                    'variation_name' => $variation->name ?: 'Default',
                    'sub_sku' => $variation->sub_sku,
                    'purchase_price' => $variation->default_purchase_price ?: 0,
                    'selling_price' => $variation->sell_price_inc_tax ?: 0,
                    'image_url' => $image_url,
                    'group_prices' => $group_prices_data[$variation->id] ?? []
                ];

                $formatted_variations[] = $variation_data;
            }

            // Format price groups for response
            $formatted_price_groups = [];
            foreach ($price_groups as $pg) {
                $formatted_price_groups[] = [
                    'id' => $pg->id,
                    'name' => $pg->name
                ];
            }

            return response()->json([
                'success' => true,
                'variations' => $formatted_variations,
                'price_groups' => $formatted_price_groups,
                'pagination' => [
                    'current_page' => (int) $page,
                    'per_page' => (int) $per_page,
                    'total' => $total_count,
                    'total_pages' => ceil($total_count / $per_page)
                ]
            ]);
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update bulk selling prices for variations
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBulkSellingPrice(Request $request)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $variations_data = $request->input('variations', []);

            if (empty($variations_data) || !is_array($variations_data)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No variations data provided'
                ], 400);
            }

            // Check if group_pricing module is enabled
            $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];
            $is_group_pricing_enabled = in_array('group_pricing', $enabled_modules);

            DB::beginTransaction();

            $updated_count = 0;
            $errors = [];

            foreach ($variations_data as $variation_id => $variation_data) {
                try {
                    // Get variation
                    $variation = Variation::where('id', $variation_id)
                        ->whereHas('product', function($q) use ($business_id) {
                            $q->where('business_id', $business_id);
                        })
                        ->first();

                    if (!$variation) {
                        $errors[] = "Variation ID {$variation_id} not found or doesn't belong to your business";
                        continue;
                    }

                    $product = $variation->product;
                    if (!$product) {
                        $errors[] = "Product not found for variation ID {$variation_id}";
                        continue;
                    }

                    // Get tax rate
                    $tax_rate = 0;
                    if ($product->tax) {
                        $tax = TaxRate::find($product->tax);
                        if ($tax) {
                            $tax_rate = $tax->amount;
                        }
                    }

                    $updated = false;

                    // Update purchase price if provided
                    if (isset($variation_data['purchase_price']) && $variation_data['purchase_price'] !== '') {
                        $purchase_price = $this->productUtil->num_uf($variation_data['purchase_price']);
                        $variation->default_purchase_price = $purchase_price;
                        $variation->dpp_inc_tax = $this->productUtil->calc_percentage($purchase_price, $tax_rate, $purchase_price);
                        $updated = true;
                    }

                    // Update selling price if provided
                    if (isset($variation_data['selling_price']) && $variation_data['selling_price'] !== '') {
                        $sell_price_inc_tax = $this->productUtil->num_uf($variation_data['selling_price']);
                        $variation->sell_price_inc_tax = $sell_price_inc_tax;
                        $variation->default_sell_price = $this->productUtil->calc_percentage_base($sell_price_inc_tax, $tax_rate);
                        
                        // Recalculate profit percent
                        if ($variation->default_purchase_price > 0) {
                            $variation->profit_percent = $this->productUtil->get_percent(
                                $variation->default_purchase_price,
                                $variation->default_sell_price
                            );
                        }
                        $updated = true;
                    }

                    // Save variation if updated
                    if ($updated) {
                        $variation->save();
                        $updated_count++;
                    }

                    // Update group prices if enabled and provided
                    if ($is_group_pricing_enabled && isset($variation_data['group_prices']) && is_array($variation_data['group_prices'])) {
                        foreach ($variation_data['group_prices'] as $price_group_id => $group_price_data) {
                            // Check if price is provided (allow 0 as valid value to clear prices)
                            if (!isset($group_price_data['price'])) {
                                continue;
                            }

                            // Get price value - handle empty string, null, or actual values
                            $price_value = $group_price_data['price'];
                            
                            // Skip if price is empty string (but allow 0)
                            if ($price_value === '' || $price_value === null) {
                                // If price is explicitly empty, delete the group price record
                                VariationGroupPrice::where('variation_id', $variation_id)
                                    ->where('price_group_id', $price_group_id)
                                    ->delete();
                                continue;
                            }

                            // Convert price to numeric (handles strings like "10.50")
                            $price = $this->productUtil->num_uf($price_value);
                            
                            // Only save if price is greater than 0
                            if ($price <= 0) {
                                // Delete existing group price if price is 0 or negative
                                VariationGroupPrice::where('variation_id', $variation_id)
                                    ->where('price_group_id', $price_group_id)
                                    ->delete();
                                continue;
                            }

                            $price_type = isset($group_price_data['price_type']) ? $group_price_data['price_type'] : 'fixed';

                            // Check if price group exists and belongs to business
                            $price_group = SellingPriceGroup::where('id', $price_group_id)
                                ->where('business_id', $business_id)
                                ->active()
                                ->first();

                            if (!$price_group) {
                                $errors[] = "Price group ID {$price_group_id} not found for variation ID {$variation_id}";
                                continue;
                            }

                            // Update or create group price
                            VariationGroupPrice::updateOrCreate(
                                [
                                    'variation_id' => $variation_id,
                                    'price_group_id' => $price_group_id
                                ],
                                [
                                    'price_inc_tax' => $price,
                                    'price_type' => $price_type
                                ]
                            );
                            
                            $updated = true; // Mark as updated since group prices were changed
                        }
                    }

                } catch (\Exception $e) {
                    \Log::error('Error updating variation ' . $variation_id . ': ' . $e->getMessage());
                    $errors[] = "Error updating variation ID {$variation_id}: " . $e->getMessage();
                }
            }

            if ($updated_count > 0) {
                DB::commit();
                
                $msg = "Successfully updated prices for {$updated_count} variation(s)";
                if (!empty($errors)) {
                    $msg .= ". Some errors occurred: " . implode(', ', array_slice($errors, 0, 5));
                }

                return response()->json([
                    'success' => true,
                    'msg' => $msg,
                    'updated_count' => $updated_count,
                    'errors' => $errors
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'msg' => 'No variations were updated. ' . (!empty($errors) ? implode(', ', array_slice($errors, 0, 5)) : 'Please check your input.')
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Shows form to add stock for products.
     *
     * @return \Illuminate\Http\Response
     */
    public function addStock()
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get user's permitted locations
        $permitted_locations = auth()->user()->permitted_locations();
        $has_multiple_locations = false;
        $business_locations = collect([]);
        
        if ($permitted_locations == 'all') {
            $business_locations = BusinessLocation::forDropdown($business_id, false, false, false, false);
            $has_multiple_locations = $business_locations->count() > 1;
        } else {
            if (is_array($permitted_locations) && count($permitted_locations) > 1) {
                $has_multiple_locations = true;
                $business_locations = BusinessLocation::where('business_id', $business_id)
                    ->whereIn('id', $permitted_locations)
                    ->active()
                    ->pluck('name', 'id');
            }
        }

        // Get initial filter options (without location filter for now)
        $location_id = null;
        $categories = Category::forDropdown($business_id, 'product', $location_id);
        $brands = Brands::forDropdown($business_id, false, false, $location_id);

        return view('product.add-stock')->with(compact(
            'business_locations',
            'has_multiple_locations',
            'categories',
            'brands'
        ));
    }

    /**
     * Get filtered products for stock management
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilteredProductsForStock(Request $request)
    {
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $location_id = $request->input('location_id');
            $product_type = $request->input('product_type');
            $brand_id = $request->input('brand_id');
            $category_id = $request->input('category_id');
            $zero_negative_stock = $request->input('zero_negative_stock', 0);

            // Get user's permitted locations
            $permitted_locations = auth()->user()->permitted_locations();
            
            // Validate location
            if ($location_id) {
                if ($permitted_locations != 'all' && !in_array($location_id, $permitted_locations)) {
                    return response()->json([
                        'success' => false,
                        'msg' => 'You do not have permission to access this location'
                    ], 403);
                }
            } else {
                // If no location selected and user has multiple locations, return error
                if ($permitted_locations == 'all') {
                    $location_count = BusinessLocation::where('business_id', $business_id)->active()->count();
                    if ($location_count > 1) {
                        return response()->json([
                            'success' => false,
                            'msg' => 'Please select a location first'
                        ], 400);
                    } else {
                        // Get the only location
                        $location = BusinessLocation::where('business_id', $business_id)->active()->first();
                        $location_id = $location ? $location->id : null;
                    }
                } else {
                    if (count($permitted_locations) > 1) {
                        return response()->json([
                            'success' => false,
                            'msg' => 'Please select a location first'
                        ], 400);
                    } else {
                        $location_id = $permitted_locations[0] ?? null;
                    }
                }
            }

            // Build query for variations
            $query = Variation::join('products as p', 'variations.product_id', '=', 'p.id')
                ->leftJoin('variation_location_details as vld', function($join) use ($location_id) {
                    $join->on('variations.id', '=', 'vld.variation_id')
                         ->where('vld.location_id', '=', $location_id);
                })
                ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
                ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
                ->where('p.business_id', $business_id)
                ->whereNull('p.discontinue')
                ->where('p.is_inactive', 0)
                ->where('p.enable_stock', 1) // Only products with stock enabled
                ->whereNull('variations.deleted_at');

            // Filter products by location - only show products available at the selected location
            if ($location_id) {
                $query->join('product_locations as pl', 'pl.product_id', '=', 'p.id')
                      ->where('pl.location_id', $location_id);
            }

            // Apply filters
            if ($product_type) {
                $query->where('p.type', $product_type);
            }

            if ($brand_id) {
                $query->where('p.brand_id', $brand_id);
            }

            if ($category_id) {
                $query->where('p.category_id', $category_id);
            }

            // Filter by zero/negative stock
            if ($zero_negative_stock) {
                $query->where(function($q) {
                    $q->whereNull('vld.qty_available')
                      ->orWhere('vld.qty_available', '<=', 0);
                });
            }

            // Get variations
            $variations = $query->select(
                'variations.id',
                'variations.product_id',
                'variations.name as variation_name',
                'variations.sub_sku',
                'p.name as product_name',
                'p.type as product_type',
                DB::raw('COALESCE(vld.qty_available, 0) as current_stock')
            )->get();

            // Format variations with image URLs
            $formatted_variations = [];
            foreach ($variations as $variation) {
                // Get variation image
                $image_url = $variation->image_url;

                $variation_data = [
                    'id' => $variation->id,
                    'product_name' => $variation->product_name,
                    'variation_name' => $variation->variation_name ?: 'Default',
                    'sub_sku' => $variation->sub_sku,
                    'current_stock' => $variation->current_stock ?? 0,
                    'image_url' => $image_url
                ];

                $formatted_variations[] = $variation_data;
            }

            return response()->json([
                'success' => true,
                'variations' => $formatted_variations
            ]);
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update bulk stock for variations
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBulkStock(Request $request)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $variations_data = $request->input('variations', []);
            $location_id = $request->input('location_id');

            if (empty($variations_data) || !is_array($variations_data)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No variations data provided'
                ], 400);
            }

            // Get user's permitted locations
            $permitted_locations = auth()->user()->permitted_locations();
            
            // Validate and set location_id if not provided
            if ($location_id) {
                if ($permitted_locations != 'all' && !in_array($location_id, $permitted_locations)) {
                    return response()->json([
                        'success' => false,
                        'msg' => 'You do not have permission to access this location'
                    ], 403);
                }
            } else {
                // Auto-select location if only one available
                if ($permitted_locations == 'all') {
                    $location_count = BusinessLocation::where('business_id', $business_id)->active()->count();
                    if ($location_count == 1) {
                        $location = BusinessLocation::where('business_id', $business_id)->active()->first();
                        $location_id = $location ? $location->id : null;
                    }
                } else {
                    if (count($permitted_locations) == 1) {
                        $location_id = $permitted_locations[0] ?? null;
                    }
                }
            }

            if (!$location_id) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Please select a location first'
                ], 400);
            }

            DB::beginTransaction();

            $updated_count = 0;
            $errors = [];

            foreach ($variations_data as $variation_id => $variation_data) {
                try {
                    // Get variation - ensure it's available at the selected location
                    $variation = Variation::where('id', $variation_id)
                        ->whereHas('product', function($q) use ($business_id, $location_id) {
                            $q->where('business_id', $business_id)
                              ->whereNull('discontinue')
                              ->where('is_inactive', 0)
                              ->where('enable_stock', 1)
                              ->whereHas('product_locations', function($q2) use ($location_id) {
                                  $q2->where('product_locations.location_id', $location_id);
                              });
                        })
                        ->first();

                    if (!$variation) {
                        $errors[] = "Variation ID {$variation_id} not found or not available at the selected location";
                        continue;
                    }

                    $product = $variation->product;
                    if (!$product) {
                        $errors[] = "Product not found for variation ID {$variation_id}";
                        continue;
                    }

                    // Check if stock is enabled
                    if ($product->enable_stock != 1) {
                        $errors[] = "Product '{$product->name}' does not have stock enabled";
                        continue;
                    }

                    // Get stock quantity to add
                    if (!isset($variation_data['stock_quantity']) || $variation_data['stock_quantity'] === '') {
                        continue; // Skip if no quantity provided
                    }

                    $stock_quantity = $this->productUtil->num_uf($variation_data['stock_quantity']);
                    
                    if ($stock_quantity < 0) {
                        $errors[] = "Invalid stock quantity for variation ID {$variation_id}";
                        continue;
                    }

                    // Set stock to the selected location only
                    try {
                        // Get current stock for this location
                        $current_stock = $this->productUtil->getCurrentStock($variation_id, $location_id);
                        
                        // Set stock using productUtil (updateProductQuantity calculates difference: new - old)
                        $this->productUtil->updateProductQuantity(
                            $location_id,
                            $product->id,
                            $variation_id,
                            $stock_quantity, // New quantity to set
                            $current_stock  // Old quantity for difference calculation
                        );
                        $updated_count++;
                    } catch (\Exception $e) {
                        \Log::error('Error setting stock for variation ' . $variation_id . ' at location ' . $location_id . ': ' . $e->getMessage());
                        $errors[] = "Error setting stock for variation ID {$variation_id}: " . $e->getMessage();
                    }

                } catch (\Exception $e) {
                    \Log::error('Error updating stock for variation ' . $variation_id . ': ' . $e->getMessage());
                    $errors[] = "Error updating stock for variation ID {$variation_id}: " . $e->getMessage();
                }
            }

            if ($updated_count > 0) {
                DB::commit();
                
                $msg = "Successfully set stock for {$updated_count} variation(s)";
                if (!empty($errors)) {
                    $msg .= ". Some errors occurred: " . implode(', ', array_slice($errors, 0, 5));
                }

                return response()->json([
                    'success' => true,
                    'msg' => $msg,
                    'updated_count' => $updated_count,
                    'errors' => $errors
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'msg' => 'No stock was added. ' . (!empty($errors) ? implode(', ', array_slice($errors, 0, 5)) : 'Please check your input.')
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export stock template CSV/Excel
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportStockTemplate(Request $request)
    {
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $location_id = $request->input('location_id');

            // Get variations with current stock
            $query = Variation::join('products as p', 'variations.product_id', '=', 'p.id')
                ->leftJoin('variation_location_details as vld', function($join) use ($location_id) {
                    $join->on('variations.id', '=', 'vld.variation_id');
                    if ($location_id) {
                        $join->where('vld.location_id', '=', $location_id);
                    }
                })
                ->where('p.business_id', $business_id)
                ->where('p.enable_stock', 1)
                ->whereNull('variations.deleted_at')
                ->whereNull('p.discontinue')
                ->where('p.is_inactive', 0);

            // Filter products by location if location_id is provided
            if ($location_id) {
                $query->join('product_locations as pl', 'pl.product_id', '=', 'p.id')
                      ->where('pl.location_id', $location_id);
            }

            $query->select(
                    'variations.id',
                    'variations.sub_sku',
                    'p.name as product_name',
                    'variations.name as variation_name',
                    DB::raw('COALESCE(vld.qty_available, 0) as current_stock')
                )
                ->orderBy('p.name')
                ->orderBy('variations.name');

            $variations = $query->get();

            // Create CSV content
            $filename = 'stock_template_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($variations) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8 to help Excel recognize the file correctly
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Add headers
                fputcsv($file, ['SKU', 'Product Name', 'Variation Name', 'Current Stock', 'Stock Quantity']);
                
                // Add data
                foreach ($variations as $variation) {
                    // Format SKU using Excel formula format to preserve leading zeros
                    // Excel will treat ="SKU" as text and preserve leading zeros
                    $sku = '="' . str_replace('"', '""', $variation->sub_sku) . '"';
                    
                    fputcsv($file, [
                        $sku,
                        $variation->product_name,
                        $variation->variation_name ?: 'Default',
                        $variation->current_stock,
                        '' // Empty column for user to fill
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import stock from CSV/Excel file
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importStock(Request $request)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            // Location is optional now - we use product locations instead

            // Validate file
            $request->validate([
                'file' => 'required|mimes:csv,txt,xlsx,xls|max:10240'
            ]);

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            DB::beginTransaction();

            $imported_count = 0;
            $errors = [];

            if (in_array($extension, ['csv', 'txt'])) {
                // Handle CSV
                $data = array_map('str_getcsv', file($file->getRealPath()));
                $header = array_shift($data); // Remove header row

                foreach ($data as $row) {
                    if (count($row) < 5) continue; // Skip invalid rows

                    // Extract SKU - handle Excel formula format (="SKU") or plain SKU
                    $sku_raw = trim($row[0]);
                    // Remove Excel formula format if present: ="SKU" -> SKU
                    if (preg_match('/^="(.+)"$/', $sku_raw, $matches)) {
                        $sku = $matches[1];
                    } else {
                        $sku = $sku_raw;
                    }
                    // Also handle if it starts with single quote (Excel text indicator)
                    $sku = ltrim($sku, "'");
                    $stock_quantity_str = trim($row[4]);

                    if (empty($sku) || empty($stock_quantity_str)) continue;

                    // Find variation by SKU
                    $variation = Variation::where('sub_sku', $sku)
                        ->whereHas('product', function($q) use ($business_id) {
                            $q->where('business_id', $business_id)
                              ->where('enable_stock', 1)
                              ->whereNull('discontinue')
                              ->where('is_inactive', 0);
                        })
                        ->first();

                    if (!$variation) {
                        $errors[] = "Variation with SKU '{$sku}' not found";
                        continue;
                    }

                    $stock_quantity = $this->productUtil->num_uf($stock_quantity_str);
                    if ($stock_quantity < 0) {
                        $errors[] = "Invalid stock quantity for SKU '{$sku}'";
                        continue;
                    }

                    $product = $variation->product;
                    
                    // Get product locations
                    $product_locations = ProductLocation::where('product_id', $product->id)
                        ->pluck('location_id')
                        ->toArray();

                    // If product has no locations assigned, get all business locations or user's permitted locations
                    if (empty($product_locations)) {
                        $permitted_locations = auth()->user()->permitted_locations();
                        if ($permitted_locations == 'all') {
                            $product_locations = BusinessLocation::where('business_id', $business_id)
                                ->active()
                                ->pluck('id')
                                ->toArray();
                        } else {
                            $product_locations = $permitted_locations;
                        }
                    }

                    // Filter locations based on user permissions
                    $permitted_locations = auth()->user()->permitted_locations();
                    if ($permitted_locations != 'all') {
                        $product_locations = array_intersect($product_locations, $permitted_locations);
                    }

                    if (empty($product_locations)) {
                        $errors[] = "Product with SKU '{$sku}' has no accessible locations";
                        continue;
                    }

                    // Set stock for all product locations
                    $locations_updated = 0;
                    foreach ($product_locations as $prod_location_id) {
                        try {
                            // Get current stock for this location
                            $current_stock = $this->productUtil->getCurrentStock($variation->id, $prod_location_id);
                            
                            // Set stock using productUtil (updateProductQuantity calculates difference: new - old)
                            $this->productUtil->updateProductQuantity(
                                $prod_location_id,
                                $variation->product_id,
                                $variation->id,
                                $stock_quantity, // New quantity to set
                                $current_stock  // Old quantity for difference calculation
                            );
                            $locations_updated++;
                        } catch (\Exception $e) {
                            \Log::error('Error importing stock for SKU ' . $sku . ' at location ' . $prod_location_id . ': ' . $e->getMessage());
                            $errors[] = "Error setting stock for SKU '{$sku}' at location {$prod_location_id}: " . $e->getMessage();
                        }
                    }

                    if ($locations_updated > 0) {
                        $imported_count++;
                    }
                }
            } else {
                // Handle Excel
                $data = Excel::toArray([], $file);
                if (empty($data) || empty($data[0])) {
                    throw new \Exception('Empty file or invalid format');
                }

                $rows = $data[0];
                $header = array_shift($rows); // Remove header row

                foreach ($rows as $row) {
                    if (count($row) < 5) continue; // Skip invalid rows

                    // Extract SKU - handle Excel formula format (="SKU") or plain SKU
                    $sku_raw = trim($row[0] ?? '');
                    // Remove Excel formula format if present: ="SKU" -> SKU
                    if (preg_match('/^="(.+)"$/', $sku_raw, $matches)) {
                        $sku = $matches[1];
                    } else {
                        $sku = $sku_raw;
                    }
                    // Also handle if it starts with single quote (Excel text indicator)
                    $sku = ltrim($sku, "'");
                    $stock_quantity_str = trim($row[4] ?? '');

                    if (empty($sku) || empty($stock_quantity_str)) continue;

                    // Find variation by SKU
                    $variation = Variation::where('sub_sku', $sku)
                        ->whereHas('product', function($q) use ($business_id) {
                            $q->where('business_id', $business_id)
                              ->where('enable_stock', 1)
                              ->whereNull('discontinue')
                              ->whereNull('deleted_at')
                              ->where('is_inactive', 0);
                        })
                        ->first();

                    if (!$variation) {
                        $errors[] = "Variation with SKU '{$sku}' not found";
                        continue;
                    }

                    $stock_quantity = $this->productUtil->num_uf($stock_quantity_str);
                    if ($stock_quantity < 0) {
                        $errors[] = "Invalid stock quantity for SKU '{$sku}'";
                        continue;
                    }

                    $product = $variation->product;
                    
                    // Get product locations
                    $product_locations = ProductLocation::where('product_id', $product->id)
                        ->pluck('location_id')
                        ->toArray();

                    // If product has no locations assigned, get all business locations or user's permitted locations
                    if (empty($product_locations)) {
                        $permitted_locations = auth()->user()->permitted_locations();
                        if ($permitted_locations == 'all') {
                            $product_locations = BusinessLocation::where('business_id', $business_id)
                                ->active()
                                ->pluck('id')
                                ->toArray();
                        } else {
                            $product_locations = $permitted_locations;
                        }
                    }

                    // Filter locations based on user permissions
                    $permitted_locations = auth()->user()->permitted_locations();
                    if ($permitted_locations != 'all') {
                        $product_locations = array_intersect($product_locations, $permitted_locations);
                    }

                    if (empty($product_locations)) {
                        $errors[] = "Product with SKU '{$sku}' has no accessible locations";
                        continue;
                    }

                    // Set stock for all product locations
                    $locations_updated = 0;
                    foreach ($product_locations as $prod_location_id) {
                        try {
                            // Get current stock for this location
                            $current_stock = $this->productUtil->getCurrentStock($variation->id, $prod_location_id);
                            
                            // Set stock using productUtil (updateProductQuantity calculates difference: new - old)
                            $this->productUtil->updateProductQuantity(
                                $prod_location_id,
                                $variation->product_id,
                                $variation->id,
                                $stock_quantity, // New quantity to set
                                $current_stock  // Old quantity for difference calculation
                            );
                            $locations_updated++;
                        } catch (\Exception $e) {
                            \Log::error('Error importing stock for SKU ' . $sku . ' at location ' . $prod_location_id . ': ' . $e->getMessage());
                            $errors[] = "Error setting stock for SKU '{$sku}' at location {$prod_location_id}: " . $e->getMessage();
                        }
                    }

                    if ($locations_updated > 0) {
                        $imported_count++;
                    }
                }
            }

            if ($imported_count > 0) {
                DB::commit();
                
                $msg = "Successfully set stock for {$imported_count} variation(s)";
                if (!empty($errors)) {
                    $msg .= ". Some errors occurred: " . implode(', ', array_slice($errors, 0, 5));
                }

                return response()->json([
                    'success' => true,
                    'msg' => $msg,
                    'imported_count' => $imported_count,
                    'errors' => $errors
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'msg' => 'No stock was imported. ' . (!empty($errors) ? implode(', ', array_slice($errors, 0, 5)) : 'Please check your file format.')
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user has access to B2B locations
     *
     * @return bool
     */
    private function hasB2BAccess()
    {
        $user = auth()->user();
        $business_id = session('business.id');
        
        if (!$business_id) {
            return false;
        }
        
        $permitted_locations = $user->permitted_locations($business_id);
        
        if ($permitted_locations == 'all') {
            // User has access to all locations, check if any location is B2B
            return BusinessLocation::where('business_id', $business_id)
                ->where('is_b2c', 0)
                ->exists();
        } elseif (is_array($permitted_locations) && !empty($permitted_locations)) {
            // User has specific location permissions, check if any is B2B
            return BusinessLocation::whereIn('id', $permitted_locations)
                ->where('is_b2c', 0)
                ->exists();
        }
        
        return false;
    }
}