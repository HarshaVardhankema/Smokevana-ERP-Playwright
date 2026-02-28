<?php

namespace App\Http\Controllers;

use App\Brands;
use App\Category;
use App\Contact;
use App\CustomerGroup;
use App\Models\CustomDiscount;
use App\Product;
use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use App\BusinessLocation;

class CustomDiscountController extends Controller
{
    public function getLocationForContact($request)
    {
        $user = auth()->user();
        $business_id = $request->session()->get('user.business_id');
        
        // Check if user is super admin or has access to all locations
        $is_super_admin = $user->can('access_all_locations') || $user->can('admin');
        
        if ($is_super_admin) {
            // Super admin can choose location from request
            $location_id = $request->input('location_id');
            if (!empty($location_id)) {
                return $location_id;
            }
            // If no location selected by super admin, return null to show all contacts
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
     * Return discount list by search term 
     */
    public function searchDiscountList(Request $request)
    {
        $searchTerm = $request->query('s', null);
        $discounts = CustomDiscount::where('couponName', 'LIKE', '%'.$searchTerm.'%')
            ->orWhere('couponCode', 'LIKE', '%'.$searchTerm.'%')
            ->orWhere('discount_lable', 'LIKE', '%'.$searchTerm.'%')
            ->select('id', 'couponName')
            ->get();
        return response()->json(['status' => true, 'result' => $discounts]);
    }
    /**
     * Summary of searchFilter
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function searchFilter(Request $request)
    {
        $searchTerm = $request->query('s', null);
        $searchIn = $request->query('type', 'product');
        $location_id = $this->getLocationForContact($request);
        $business_id = request()->session()->get('user.business_id');
        $searchWords = preg_split('/\s+/', $searchTerm);
        $regexPattern = implode('.*', array_map(function ($word) {
            return "(?=.*" . preg_quote($word) . ")";
        }, $searchWords));
        
        // Check if location_id is valid (not 'all', not null, and is a single value)
        $shouldFilterByLocation = !empty($location_id) && $location_id !== 'all' && is_numeric($location_id);
        
        if ($searchIn == 'product') {
            $result = Product::where(function ($query) use ($regexPattern) {
                    $query->where('name', 'REGEXP', $regexPattern)
                        ->orWhere('sku', 'REGEXP', $regexPattern);
                })
                ->when($shouldFilterByLocation, function ($query) use ($location_id) {
                    $query->whereHas('product_locations', function ($q) use ($location_id) {
                        $q->where('product_locations.location_id', $location_id);
                    });
                })
                ->get();
        } else if ($searchIn == 'product_variations') { // Product Name
            // Product Name + Variation Name
            $q = Product::leftJoin(
                'variations',
                'products.id',
                '=',
                'variations.product_id'
            )
                ->where(function ($query) use ($regexPattern) {
                    $query->where('products.name', 'REGEXP', $regexPattern)
                        ->orWhere('variations.name', 'REGEXP', $regexPattern)
                        ->orWhere('variations.sub_sku', 'REGEXP', $regexPattern);
                })
                // ->active()
                ->where('business_id', $business_id)
                ->whereNull('variations.deleted_at')
                ->when($shouldFilterByLocation, function ($query) use ($location_id) {
                    $query->join('product_locations as pl', 'pl.product_id', '=', 'products.id')
                        ->where('pl.location_id', $location_id);
                })
                ->select(
                    'products.id as product_id',
                    'products.name',
                    'products.type',
                    // 'products.sku as sku',
                    'variations.id as variation_id',
                    'variations.name as variation',
                    'variations.sub_sku as sub_sku'
                )
                ->groupBy('variation_id');
            $products = $q->get();
            $products_array = [];
            foreach ($products as $product) {
                $products_array[$product->product_id]['name'] = $product->name;
                $products_array[$product->product_id]['sku'] = $product->sub_sku;
                $products_array[$product->product_id]['type'] = $product->type;
                $products_array[$product->product_id]['variations'][]
                    = [
                        'variation_id' => $product->variation_id,
                        'variation_name' => $product->variation,
                        'sub_sku' => $product->sub_sku,
                    ];
            }
            $result = [];
            $i = 1;
            $no_of_records = $products->count();
            if (! empty($products_array)) {
                foreach ($products_array as $key => $value) {
                    if ($no_of_records > 1 && $value['type'] != 'single') {
                        $result[] = [
                            'id' => $i,
                            'product_name' => $value['sku'] . ' - ' . $value['name'],
                            'variation_id' => null,
                            'product_id' => $key,
                        ];
                    }
                    $name = $value['name'];
                    foreach ($value['variations'] as $variation) {
                        $text = $name;
                        if ($value['type'] == 'variable') {
                            $text = $text . ' (' . $variation['variation_name'] . ')';
                        }
                        $i++;
                        $result[] = [
                            'id' => $i,
                            'product_name' => $variation['sub_sku'] . ' - ' . $text,
                            'product_id' => $key,
                            'variation_id' => $variation['variation_id'],
                            'variation_name' =>  $variation['variation_name'],
                        ];
                    }
                    $i++;
                }
            }
        } elseif ($searchIn == 'category') {
            $result = Category::where('category_type', 'product')
                ->where('name', 'REGEXP', $regexPattern)
                ->when($shouldFilterByLocation, function ($query) use ($location_id) {
                    $query->where('location_id', $location_id);
                })
                ->get();
        } else if ($searchIn == 'brand') {
            $result = Brands::where('name', 'REGEXP', $regexPattern)
                ->when($shouldFilterByLocation, function ($query) use ($location_id) {
                    $query->where('location_id', $location_id);
                })
                ->get();
        } else if ($searchIn == 'customer') {
            $result = Contact::where('type', 'customer')
                ->where(function ($query) use ($regexPattern) {
                    $query->where('name', 'REGEXP', $regexPattern)
                        ->orWhere('email', 'REGEXP', $regexPattern)
                        ->orWhere('contact_id', 'REGEXP', $regexPattern);
                })
                ->when($shouldFilterByLocation, function ($query) use ($location_id) {
                    return $query->where('location_id', $location_id);
                })
                ->get();
        } else if ($searchIn == 'customers_group') {
            $result = CustomerGroup::where('name', 'REGEXP', $regexPattern)->get();
        }
        return response()->json(['status' => true, 'result' => $result]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response | mixed
     */
    public function index()
    {
        $location_id = null;
        if(!auth()->user()->can('access_all_locations') || !auth()->user()->can('admin')){
            $location_id = $this->getLocationForContact(request());
        }
        $is_b2c = false;
        if(!auth()->user()->can('access_all_locations') || !auth()->user()->can('admin')){
            $is_b2c = BusinessLocation::where('id', $location_id)->value('is_b2c');
        }
        $is_super_admin = false;
        if(auth()->user()->can('access_all_locations') || auth()->user()->can('admin')){
            $is_super_admin = true;
        }
        if (request()->ajax()) {
            $discounts = CustomDiscount::with('location')
                ->select([
                    'id', 'couponName', 'discountType', 'filter', 'location_id', 
                    'brand_id', 'applyDate', 'endDate', 'isDisabled', 'useLimit', 
                    'setPriority', 'updated_at','discount_lable'
                ])
                ->orderBy('updated_at', 'desc')
                ->where(function($query) use ($location_id){
                    if($location_id){
                        $query->where('location_id', $location_id);
                    }
                });
            return \Yajra\DataTables\Facades\DataTables::of($discounts)
                ->addColumn('offerName', function ($row) {
                    return $row->couponName;
                })
                ->addColumn('offerLable', function ($row) {
                    return $row->discount_lable;
                })
                ->addColumn('type', function ($row) {
                    $typeLabels = [
                        'productAdjustment' => 'Product Adjustment',
                        'cartAdjustment' => 'Cart Adjustment',
                        'freeShipping' => 'Free Shipping',
                        'buyXgetX' => 'Buy X Get X',
                        'buyXgetY' => 'Buy X Get Y',
                        'bulk' => 'Bulk',
                        'bundle' => 'Bundle',
                    ];
                    return $typeLabels[$row->discountType] ?? ucwords(preg_replace('/([a-z])([A-Z])/', '$1 $2', $row->discountType));
                })
                ->addColumn('applicability', function ($row) {
                    return $this->getApplicabilityText($row->filter);
                })
                ->addColumn('location', function ($row) {
                    return $row->location->name ?? 'All Locations';
                })
                ->addColumn('brand', function ($row) {
                    // Use the optimized brand() method from the model
                    return $row->brand();
                })
                ->addColumn('validity', function ($row) {
                    $start = $row->applyDate ? \Carbon\Carbon::parse($row->applyDate)->format('Y-m-d H:i:s') : '';
                    $end = $row->endDate ? \Carbon\Carbon::parse($row->endDate)->format('Y-m-d H:i:s') : '';
                    return $start . ' - ' . $end;
                })
                ->addColumn('status', function ($row) {
                    $statusChangeUrl = action([\App\Http\Controllers\CustomDiscountController::class, 'statusChange'], [$row->id]);
                    $statusText = $row->isDisabled ? 'Inactive' : 'Active';
                    $btnClass = $row->isDisabled
                        ? 'background: #dc3545; color: #fff;' // Red for Inactive
                        : 'background: #28a745; color: #fff;'; // Green for Active
                    return '<button class="btn btn-xs change-status-btn" data-url="' . $statusChangeUrl . '" style="' . $btnClass . '">' . $statusText . '</button>';
                })
                ->addColumn('used', function ($row) {
                    return $row->useLimit;
                })
                ->addColumn('priority', function ($row) {
                    $priorityLabel = 'Low';
                    $btnClass = 'background: #6c757d; color: #fff;'; // Default gray
                    if ($row->setPriority >= 8) {
                        $priorityLabel = 'High';
                        $btnClass = 'background: #dc3545; color: #fff;'; // Red
                    } elseif ($row->setPriority >= 4) {
                        $priorityLabel = 'Medium';
                        $btnClass = 'background: #fd7e14; color: #fff;'; // Orange
                    }

                    $btn = '<button class="btn btn-xs change-priority-btn" 
                                data-id="' . $row->id . '" 
                                data-priority="' . $row->setPriority . '"
                                style="min-width:60px; ' . $btnClass . '">' .
                       $row->setPriority .
                        '</button>';
                    return $btn;
                })
                ->addColumn('updated_at', function ($row) {
                    return [
                        'display' => $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '',
                        'timestamp' => $row->updated_at ? $row->updated_at->timestamp : 0
                    ];
                })
                ->addColumn('action', function ($row) {
                    $viewUrl = action([\App\Http\Controllers\CustomDiscountController::class, 'show'], [$row->id]);
                    $editUrl = action([\App\Http\Controllers\CustomDiscountController::class, 'edit'], [$row->id]);
                    $deleteUrl = action([\App\Http\Controllers\CustomDiscountController::class, 'destroy'], [$row->id]);
                    $duplicateUrl = action([\App\Http\Controllers\CustomDiscountController::class, 'duplicate'], [$row->id]);
                    $html = '<div style="display: flex; align-items: center; gap: 8px; justify-content:space-around; width:137px; height:54px">'
                        . '<button class="btn-modal" data-href="' . $viewUrl . '" data-container=".custom_discount_modal" title="View">'
                        . '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="23" viewBox="0 0 24 23" fill="none">'
                        . '<path fill-rule="evenodd" clip-rule="evenodd" d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z" fill="#276536"/>'
                        . '</svg></button>'
                        . '<a href="#" class="duplicate-discount-btn" data-duplicate-url="' . $duplicateUrl . '" title="Duplicate">'
                        . '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">'
                        . '<rect x="7" y="7" width="9" height="9" rx="2" stroke="#094C89" stroke-width="2"/>'
                        . '<rect x="3" y="3" width="9" height="9" rx="2" stroke="#094C89" stroke-width="2"/>'
                        . '</svg></a>'
                        . '<a href="' . $editUrl . '" title="Edit">'
                        . '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">'
                        . '<path d="M2.5 14.5V17.5H5.5L14.873 8.127C15.033 7.967 15.033 7.7 14.873 7.54L12.46 5.127C12.3 4.967 12.033 4.967 11.873 5.127L2.5 14.5ZM17.5 6.127C17.76 5.867 17.76 5.433 17.5 5.173L14.827 2.5C14.567 2.24 14.133 2.24 13.873 2.5L12.127 4.247L15.753 7.873L17.5 6.127Z" fill="#FE8833"/>'
                        . '</svg></a>'
                        . '<a href="#" class="delete-discount-btn" data-delete-url="' . $deleteUrl . '" title="Delete">'
                        . '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">'
                        . '<path d="M6 7V17C6 17.5523 6.44772 18 7 18H13C13.5523 18 14 17.5523 14 17V7M4 7H16M9 10V14M11 10V14M8 7V5C8 4.44772 8.44772 4 9 4H11C11.5523 4 12 4.44772 12 5V7" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'
                        . '</svg></a>'
                        . '</div>';
                    return $html;
                })
                ->rawColumns(['action', 'status', 'priority'])
                ->make(true);
        }
        return view('custom_discounts.index', compact('is_b2c', 'is_super_admin'));
    }

    // Helper function to decode filter
    private function getApplicabilityText($filter)
    {
        if (!$filter || $filter === 'null') return 'All Products';
        $filterArr = json_decode($filter, true);
        if (!is_array($filterArr)) return 'All Products';

        $parts = [];
        foreach ($filterArr as $key => $value) {
            if (is_array($value) && isset($value['ids']) && is_array($value['ids'])) {
                $ids = $value['ids'];
                $text = count($ids) > 1 ? ($ids[0] . ', ...') : (isset($ids[0]) ? $ids[0] : '');
                $parts[] = ucfirst($key) . ': ' . $text;
            } elseif (is_array($value) && isset($value['values']) && is_array($value['values'])) {
                $vals = $value['values'];
                $text = count($vals) > 1 ? ($vals[0] . ', ...') : (isset($vals[0]) ? $vals[0] : '');
                $parts[] = ucfirst($key) . ': ' . $text;
            } elseif (is_string($value)) {
                $parts[] = ucfirst($key) . ': ' . $value;
            }
        }
        return count($parts) ? implode(' | ', $parts) : 'All Products';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response | mixed
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        $business_locations = [];
        if (auth()->user()->can('access_all_locations') || auth()->user()->can('admin')) {
            $business_locations = BusinessLocation::forDropdown($business_id);
        }
        
        $brands=[];
        $is_b2c = false;
        if(!auth()->user()->can('access_all_locations') || !auth()->user()->can('admin')){
            $location_id = $this->getLocationForContact(request());
            $brands=Brands::forDropdown($business_id, false, false, $location_id);
            $is_b2c = BusinessLocation::where('id', $location_id)->value('is_b2c');
        }
        $brands = ['all' => 'All Brands'] + (is_array($brands) ? $brands : $brands->toArray());
        return view('custom_discounts.create', compact('business_locations', 'brands', 'is_b2c'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response | mixed
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'couponName' => 'required|string|max:255',
            'couponCode' => 'nullable|string|max:50|unique:custom_discounts',
            'applyDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:applyDate',
            'discountType' => 'required|string|in:productAdjustment,cartAdjustment,freeShipping,buyXgetX,buyXgetY,bulk,bundle',
            'discountValue' => 'required|numeric|min:0',
            'minBuyQty' => 'nullable|integer|min:1',
            'maxBuyQty' => 'nullable|integer|min:1|gte:minBuyQty',
            'freeQty' => 'nullable|integer|min:1',
            'useLimit' => 'nullable|integer|min:1',
            'setPriority' => 'nullable|integer|min:0',
            'isDisabled' => 'boolean',
            'isPrimary' => 'boolean',
            'isLifeCycleCoupon' => 'boolean',
            'couponLife' => 'nullable|integer|min:1',
            'filter' => 'nullable|array',
            'discount' => 'nullable|string',
            'custom_meta' => 'nullable|array',
            'rulesOnCart' => 'nullable|array',
            'rulesOnPurchaseHistory' => 'nullable|array',
            'rulesOnShipping' => 'nullable|array',
            'rulesOnCustomer' => 'nullable|array',
            'brand_id' => 'nullable|array',
            'location_id' => 'nullable|string',
            'discount_lable' => 'nullable|string',
            'is_referal_program_discount' => 'nullable|boolean',

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 422);
        }

        $data = $request->all();
        // dd($data);
        // Convert array fields to JSON
        $arrayFields = [
            'filter',
            'discount',
            'custom_meta',
            'rulesOnCart',
            'rulesOnPurchaseHistory',
            'rulesOnShipping',
            'rulesOnCustomer',
            'brand_id',
        ];

        foreach ($arrayFields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = json_encode($data[$field]);
            }
        }
        if(empty(request()->location_id)){
            $data['location_id'] = $this->getLocationForContact(request());
        }
        $is_b2c = BusinessLocation::where('id', $data['location_id'])->value('is_b2c');
        if(!$is_b2c){
            $data['brand_id'] = null;
        }

        $last_high_priority_discount = CustomDiscount::orderBy('setPriority', 'desc')->first();
        $last_high_priority_discount_priority = $last_high_priority_discount ? $last_high_priority_discount->setPriority : 0;
        $data['setPriority'] = $last_high_priority_discount_priority + 1;
        $discount = CustomDiscount::create($data);
        // dd($discount);
        return redirect()->route('custom-discounts.index')->with('success', 'Discount created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CustomDiscount  $customDiscount
     * @return \Illuminate\Http\Response | mixed
     */
    public function show($id)
    {
        $custom_discount = CustomDiscount::find($id);

        // Decode filter and rulesOnCustomer
        $filter = $custom_discount->filter ? json_decode($custom_discount->filter, true) : [];
        $rulesOnCustomer = $custom_discount->rulesOnCustomer ? json_decode($custom_discount->rulesOnCustomer, true) : [];
        $customMeta = $custom_discount->custom_meta ? json_decode($custom_discount->custom_meta, true) : [];

        // Prepare arrays to hold selected objects
        $selectedCategories = [];
        $selectedBrands = [];
        $selectedProducts = [];
        $notCategories = [];
        $notBrands = [];
        $notProducts = [];
        $selectedCustomers = [];
        $selectedCustomerGroups = [];
        $selectedGetYProducts = [];
        $selectedLocation = [];
        $selectedBrandids = [];

        // Categories
        if (!empty($filter['categories']['ids'])) {
            $selectedCategories = Category::whereIn('id', $filter['categories']['ids'])->pluck('name', 'id')->toArray();
        }
        // Brands
        if (!empty($filter['brand']['ids'])) {
            $selectedBrands = Brands::whereIn('id', $filter['brand']['ids'])->pluck('name', 'id')->toArray();
        }
        // Products
        if (!empty($filter['product_ids']['ids'])) {
            $selectedProducts = Product::whereIn('id', $filter['product_ids']['ids'])->pluck('name', 'id')->toArray();
        }
        // Categories
        if (!empty($filter['not_categories']['ids'])) {
            $notCategories = Category::whereIn('id', $filter['not_categories']['ids'])->pluck('name', 'id')->toArray();
        }
        // Brands
        if (!empty($filter['not_brand']['ids'])) {
            $notBrands = Brands::whereIn('id', $filter['not_brand']['ids'])->pluck('name', 'id')->toArray();
        }
        // Products
        if (!empty($filter['not_product_ids']['ids'])) {
            $notProducts = Product::whereIn('id', $filter['not_product_ids']['ids'])->pluck('name', 'id')->toArray();
        }
        // Customers
        if (!empty($rulesOnCustomer['applyOn']) && $rulesOnCustomer['applyOn'] == 'customer-list' && !empty($rulesOnCustomer['values'])) {
            $selectedCustomers = Contact::whereIn('id', $rulesOnCustomer['values'])->pluck('name', 'id')->toArray();
        }
        // Customer Groups
        if (!empty($rulesOnCustomer['applyOn']) && $rulesOnCustomer['applyOn'] == 'customer-group' && !empty($rulesOnCustomer['values'])) {
            $selectedCustomerGroups = CustomerGroup::whereIn('id', $rulesOnCustomer['values'])->pluck('name', 'id')->toArray();
        }

        // Location
        if (!empty($custom_discount->location_id)) {
            $selectedLocation = json_decode($custom_discount->location_id, true);
        }
        // Brandsids - use optimized method from model
        $selectedBrandids = $custom_discount->getBrandIds();


        // Get Y Products (for buyXgetY, etc.)
        if (!empty($customMeta['get_y_products'])) {
            foreach ($customMeta['get_y_products'] as $item) {
                if (!empty($item['variation_id'])) {
                    $variation = Variation::with('product')->find($item['variation_id']);
                    if ($variation) {
                        $selectedGetYProducts[] = [
                            'id' => $variation->product_id . '-' . $variation->id,
                            'text' => ($variation->product->name ?? '') . ' (' . $variation->name . ')',
                            'quantity' => $item['quantity'],
                            'is_variation' => true,
                        ];
                    }
                } elseif (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $selectedGetYProducts[] = [
                            'id' => $product->id,
                            'text' => $product->name,
                            'quantity' => $item['quantity'],
                            'is_variation' => false,
                        ];
                    }
                }
            }
        }
        $business_id = request()->session()->get('user.business_id');
        $business_locations = [];
        $is_super_admin = false;

        if (auth()->user()->can('access_all_locations') || auth()->user()->can('admin')) {
            $is_super_admin = true;
        }

        $business_locations = BusinessLocation::forDropdown($business_id , false, false, false, false);
        $business_locations = ['all' => 'All Locations'] + (is_array($business_locations) ? $business_locations : $business_locations->toArray());

        $brands=[];
        if($selectedLocation != 'all'){
            $location_id = $selectedLocation;
            $brands=Brands::forDropdown($business_id, false, false, $location_id);
        }
        $brands = ['all' => 'All Brands'] + (is_array($brands) ? $brands : $brands->toArray());
        $is_b2c = BusinessLocation::where('id', $selectedLocation)->value('is_b2c');

        return view('custom_discounts.show', compact(
            'custom_discount',
            'selectedCategories',
            'selectedBrands',
            'selectedProducts',
            'notCategories',
            'notBrands',
            'notProducts',
            'selectedGetYProducts',
            'selectedCustomers',
            'selectedCustomerGroups',
            'business_locations',
            'brands',
            'is_super_admin',
            'selectedLocation',
            'selectedBrandids',
            'is_b2c'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomDiscount  $customDiscount
     * @return \Illuminate\Http\Response | mixed
     */
    public function edit($id)
    {
        $custom_discount = CustomDiscount::find($id);

        // Decode filter and rulesOnCustomer
        $filter = $custom_discount->filter ? json_decode($custom_discount->filter, true) : [];
        $rulesOnCustomer = $custom_discount->rulesOnCustomer ? json_decode($custom_discount->rulesOnCustomer, true) : [];
        $customMeta = $custom_discount->custom_meta ? json_decode($custom_discount->custom_meta, true) : [];

        // Prepare arrays to hold selected objects
        $selectedCategories = [];
        $selectedBrands = [];
        $selectedProducts = [];
        $notCategories = [];
        $notBrands = [];
        $notProducts = [];
        $selectedCustomers = [];
        $selectedCustomerGroups = [];
        $selectedGetYProducts = [];
        $selectedLocation = null;
        $selectedBrandids = [];
        // Categories
        if (!empty($filter['categories']['ids'])) {
            $selectedCategories = Category::whereIn('id', $filter['categories']['ids'])->pluck('name', 'id')->toArray();
        }
        // Brands
        if (!empty($filter['brand']['ids'])) {
            $selectedBrands = Brands::whereIn('id', $filter['brand']['ids'])->pluck('name', 'id')->toArray();
        }
        // Products
        if (!empty($filter['product_ids']['ids'])) {
            $selectedProducts = Product::whereIn('id', $filter['product_ids']['ids'])->pluck('name', 'id')->toArray();
        }
        // Categories
        if (!empty($filter['not_categories']['ids'])) {
            $notCategories = Category::whereIn('id', $filter['not_categories']['ids'])->pluck('name', 'id')->toArray();
        }
        // Brands
        if (!empty($filter['not_brand']['ids'])) {
            $notBrands = Brands::whereIn('id', $filter['not_brand']['ids'])->pluck('name', 'id')->toArray();
        }
        // Products
        if (!empty($filter['not_product_ids']['ids'])) {
            $notProducts = Product::whereIn('id', $filter['not_product_ids']['ids'])->pluck('name', 'id')->toArray();
        }
        // Customers
        if (!empty($rulesOnCustomer['applyOn']) && $rulesOnCustomer['applyOn'] == 'customer-list' && !empty($rulesOnCustomer['values'])) {
            $selectedCustomers = Contact::whereIn('id', $rulesOnCustomer['values'])->pluck('name', 'id')->toArray();
        }
        // Customer Groups
        if (!empty($rulesOnCustomer['applyOn']) && $rulesOnCustomer['applyOn'] == 'customer-group' && !empty($rulesOnCustomer['values'])) {
            $selectedCustomerGroups = CustomerGroup::whereIn('id', $rulesOnCustomer['values'])->pluck('name', 'id')->toArray();
        }
        // Location
        if (!empty($custom_discount->location_id)) {
            $selectedLocation = $custom_discount->location_id;
        }
        // Brandsids - use optimized method from model
        $selectedBrandids = $custom_discount->getBrandIds();
        // Get Y Products (for buyXgetY, etc.)
        if (!empty($customMeta['get_y_products'])) {
            foreach ($customMeta['get_y_products'] as $item) {
                if (!empty($item['variation_id'])) {
                    $variation = Variation::with('product')->find($item['variation_id']);
                    if ($variation) {
                        $selectedGetYProducts[] = [
                            'id' => $variation->product_id . '-' . $variation->id,
                            'text' => ($variation->product->name ?? '') . ' (' . $variation->name . ')',
                            'quantity' => $item['quantity'],
                            'is_variation' => true,
                        ];
                    }
                } elseif (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $selectedGetYProducts[] = [
                            'id' => $product->id,
                            'text' => $product->name,
                            'quantity' => $item['quantity'],
                            'is_variation' => false,
                        ];
                    }
                }
            }
        }
        $business_id = request()->session()->get('user.business_id');
        $business_locations = [];
            if (auth()->user()->can('access_all_locations') || auth()->user()->can('admin')) {
                $business_locations = BusinessLocation::forDropdown($business_id);
            }
            $brands=[];
            $is_b2c = false;
            if(!auth()->user()->can('access_all_locations') || !auth()->user()->can('admin')){
                $location_id = $this->getLocationForContact(request());
                $brands=Brands::forDropdown($business_id, false, false, $location_id);
                $is_b2c = BusinessLocation::where('id', $selectedLocation)->value('is_b2c');
            }else{
                $is_b2c = BusinessLocation::where('id', $selectedLocation)->value('is_b2c');
                $location_id = $selectedLocation;
                $brands=Brands::forDropdown($business_id, false, false, $location_id);
            }
        $brands = ['all' => 'All Brands'] + (is_array($brands) ? $brands : $brands->toArray());

        return view('custom_discounts.edit', compact(
            'custom_discount',
            'selectedCategories',
            'selectedBrands',
            'selectedProducts',
            'notCategories',
            'notBrands',
            'notProducts',
            'selectedGetYProducts',
            'selectedCustomers',
            'selectedCustomerGroups',
            'business_locations',
            'brands',
            'selectedLocation',
            'selectedBrandids',
            'is_b2c'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomDiscount  $customDiscount
     * @return \Illuminate\Http\Response | mixed
     */
    public function update(Request $request, CustomDiscount $customDiscount)
    {
        $validator = Validator::make($request->all(), [
            'couponName' => 'required|string|max:255',
            'couponCode' => 'nullable|string|max:50|unique:custom_discounts,couponCode,' . $customDiscount->id,
            'applyDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:applyDate',
            'discountType' => 'required|string|in:productAdjustment,cartAdjustment,freeShipping,buyXgetX,buyXgetY,bulk,bundle',
            'discountValue' => 'required|numeric|min:0',
            'minBuyQty' => 'nullable|integer|min:1',
            'maxBuyQty' => 'nullable|integer|min:1|gte:minBuyQty',
            'freeQty' => 'nullable|integer|min:1',
            'useLimit' => 'nullable|integer|min:1',
            'setPriority' => 'nullable|integer|min:0',
            'isDisabled' => 'boolean',
            'isPrimary' => 'boolean',
            'isLifeCycleCoupon' => 'boolean',
            'couponLife' => 'nullable|integer|min:1',
            'filter' => 'nullable|array',
            'discount' => 'nullable|string',
            'custom_meta' => 'nullable|array',
            'rulesOnCart' => 'nullable|array',
            'rulesOnPurchaseHistory' => 'nullable|array',
            'rulesOnShipping' => 'nullable|array',
            'rulesOnCustomer' => 'nullable|array',
            'brand_id' => 'nullable|array',
            'location_id' => 'nullable|string',
            'discount_lable' => 'nullable|string',
            'is_referal_program_discount' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $data = $request->all();
        if (isset($data['setPriority']) && $data['setPriority'] == null) {
            $last_high_priority_discount = CustomDiscount::orderBy('setPriority', 'desc')->first();
            $last_high_priority_discount_priority = $last_high_priority_discount ? $last_high_priority_discount->setPriority : 0;
            $data['setPriority'] = $last_high_priority_discount_priority + 1;
        }
        $arrayFields = [
            'filter',
            'discount',
            'custom_meta',
            'rulesOnCart',
            'rulesOnPurchaseHistory',
            'rulesOnShipping',
            'rulesOnCustomer',
            'brand_id',
        ];

        foreach ($arrayFields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = json_encode($data[$field]);
            }
        }
        if(empty($data['location_id'])){
            $data['location_id'] = $this->getLocationForContact(request());
        }
        $is_b2c = BusinessLocation::where('id', $data['location_id'])->value('is_b2c');
        if(!$is_b2c){
            $data['brand_id'] = null;
        }
        $customDiscount->update($data);

        return response()->json(['status' => true, 'message' => 'Discount updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomDiscount  $customDiscount
     * @return \Illuminate\Http\Response | mixed
     */
    public function destroy($id)
    {
        // Find the CustomDiscount by ID
        $customDiscount = CustomDiscount::find($id);

        // If not found, redirect back with an error
        if (!$customDiscount) {
            return response()->json(['status' => false, 'message' => 'Discount not found.']);
        }

        // Delete the record
        $customDiscount->delete();

        // Redirect with success message
        return response()->json(['status' => true, 'message' => 'Discount deleted successfully.']);
    }
    public function duplicate($id)
    {
        // Find the CustomDiscount by ID
        $customDiscount = CustomDiscount::find($id);

        if (!$customDiscount) {
            return response()->json(['status' => false, 'message' => 'Discount not found.']);
        }

        $data = $customDiscount->toArray();
        unset($data['id']);
        unset($data['created_at']);
        unset($data['updated_at']);
        $data['isDisabled'] = 1;
        $data['couponName'] = $customDiscount->couponName . ' (Copied)';
        if (!empty($data['couponCode'])) {
            $data['couponCode'] = $data['couponCode'] . '_' . strtoupper(Str::random(4));
        }
        $newDiscount = CustomDiscount::create($data);

        return response()->json(['status' => true, 'message' => 'Discount duplicated successfully.', 'new_id' => $newDiscount->id]);
    }
    public function statusChange($id)
    {
        $customDiscount = CustomDiscount::find($id);
        $customDiscount->isDisabled = !$customDiscount->isDisabled;
        $customDiscount->save();
        return response()->json(['status' => true, 'message' => 'Discount status changed successfully.']);
    }
    public function priorityChange($id, $priority)
    {
        $customDiscount = CustomDiscount::find($id);
        $customDiscount->setPriority = $priority;
        $customDiscount->save();
        return response()->json(['status' => true, 'message' => 'Discount priority changed successfully.']);
    }
}
