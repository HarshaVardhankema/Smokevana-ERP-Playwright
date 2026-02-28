<?php

namespace App\Http\Controllers\ECOM;

use App\Http\Controllers\Controller;
use App\Models\CustomDiscount;
use App\Product;
use App\Category;
use App\Brands;
use App\BusinessLocation;
use App\Models\Contact;
use App\Services\CustomDiscountRuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CateLogResource;
use Illuminate\Support\Facades\Log;
class CustomerDiscountController extends Controller
{
    protected $discountService;

    public function __construct(CustomDiscountRuleService $discountService)
    {
        $this->discountService = $discountService;
    }

    /**
     * Get all running discounts for customer
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRunningDiscounts(Request $request)
    {
        try {
            // Get authenticated customer
            $customer = Auth::guard('api')->user();
            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not authenticated'
                ], 401);
            }
            $priceGroupId = 1;
            if($customer->price_tier){
                $priceTier = $customer->price_tier;
                $priceGroupId = key($priceTier); 
            }
            $locationId = $request->route('location_id',null);
            if($locationId){
                BusinessLocation::where('id',$locationId)->first();
            }
            $brandId = $request->get('brand_id',null);
            // Get active discounts
            $discounts = $this->discountService->getActiveDiscounts($customer, $locationId, $brandId);
            $id = $request->query('id',false);
            if($id){
                $discounts = $discounts->where('id',$id);
            }
            $discountList = [];
            foreach ($discounts as $discount) {
                $discountData = [
                    'id' => $discount->id,
                    'name' => $discount->couponName,
                    'logo' => $discount->logo,
                    'discount_type' => $discount->discountType,
                    'discount_value' => $discount->discountValue,
                    'discount_method' => $discount->discount,
                    'min_buy_qty' => $discount->minBuyQty,
                    'max_buy_qty' => $discount->maxBuyQty,
                    'use_limit' => $discount->useLimit,
                    'priority' => $discount->setPriority,
                    'apply_date' => $discount->applyDate,
                    'end_date' => $discount->endDate,
                    'is_primary' => $discount->isPrimary,
                    'filter_summary' => $this->getFilterSummary($discount->filter, $priceGroupId??1),
                    'applicability' => $this->getApplicabilityText($discount->filter)
                ];

                // Add specific details based on discount type
                if ($discount->discountType === 'buyXgetY') {
                    $customMeta = json_decode($discount->custom_meta, true);
                    $discountData['buy_x_get_y_details'] = [
                        'buy_quantity' => $customMeta['buy_quantity'] ?? null,
                        'is_recursive' => $customMeta['is_recursive'] ?? false,
                        'free_products' => $customMeta['get_y_products'] ?? []
                    ];
                }

                if ($discount->discountType === 'cartAdjustment') {
                    $rulesOnCart = json_decode($discount->rulesOnCart, true);
                    $discountData['cart_rules'] = [
                        'min_order_value' => $rulesOnCart['minOrderValue'] ?? null,
                        'max_discount_amount' => $rulesOnCart['maxDiscountAmount'] ?? null
                    ];
                }

                if ($discount->discountType === 'freeShipping') {
                    $rulesOnCart = json_decode($discount->rulesOnCart, true);
                    $discountData['shipping_rules'] = [
                        'min_order_value' => $rulesOnCart['minOrderValue'] ?? null,
                        'max_discount_amount' => $rulesOnCart['maxDiscountAmount'] ?? null
                    ];
                }

                $discountList[] = $discountData;
            }

            return response()->json([
                'status' => true,
                'message' => 'Running discounts retrieved successfully',
                'data' => $discountList,
                'total_count' => count($discountList)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve discounts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all active discounts for B2B (public endpoint).
     * GET /api/discounts/active
     * Returns discounts with Status "Active" (isDisabled = false), matching the Web Discount Rules UI.
     * Optional: ?valid_only=1 to restrict to discounts whose apply/end date range includes today.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveDiscountsB2b(Request $request)
    {
        try {
            $query = CustomDiscount::active()->orderBy('setPriority', 'desc');

            // Optionally restrict to currently valid by date (applyDate <= now, endDate >= now)
            if ($request->boolean('valid_only')) {
                $query->valid();
            }

            $discounts = $query->get();

            $discountList = [];
            foreach ($discounts as $discount) {
                $discountData = [
                    'id' => $discount->id,
                    'name' => $discount->couponName,
                    'logo' => $discount->logo,
                    'discount_type' => $discount->discountType,
                    'discount_value' => $discount->discountValue,
                    'discount_method' => $discount->discount,
                    'min_buy_qty' => $discount->minBuyQty,
                    'max_buy_qty' => $discount->maxBuyQty,
                    'use_limit' => $discount->useLimit,
                    'priority' => $discount->setPriority,
                    'apply_date' => $discount->applyDate,
                    'end_date' => $discount->endDate,
                    'is_primary' => $discount->isPrimary,
                    'filter_summary' => $this->getFilterSummary($discount->filter),
                    'applicability' => $this->getApplicabilityText($discount->filter),
                ];

                if ($discount->discountType === 'buyXgetY') {
                    $customMeta = json_decode($discount->custom_meta, true);
                    $discountData['buy_x_get_y_details'] = [
                        'buy_quantity' => $customMeta['buy_quantity'] ?? null,
                        'is_recursive' => $customMeta['is_recursive'] ?? false,
                        'free_products' => $customMeta['get_y_products'] ?? [],
                    ];
                }

                if ($discount->discountType === 'cartAdjustment') {
                    $rulesOnCart = json_decode($discount->rulesOnCart, true);
                    $discountData['cart_rules'] = [
                        'min_order_value' => $rulesOnCart['minOrderValue'] ?? null,
                        'max_discount_amount' => $rulesOnCart['maxDiscountAmount'] ?? null,
                    ];
                }

                if ($discount->discountType === 'freeShipping') {
                    $rulesOnCart = json_decode($discount->rulesOnCart, true);
                    $discountData['shipping_rules'] = [
                        'min_order_value' => $rulesOnCart['minOrderValue'] ?? null,
                        'max_discount_amount' => $rulesOnCart['maxDiscountAmount'] ?? null,
                    ];
                }

                $discountList[] = $discountData;
            }

            return response()->json([
                'status' => true,
                'message' => 'All active B2B discounts retrieved successfully',
                'data' => $discountList,
                'total_count' => count($discountList),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve active discounts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all running discounts publicly (for promotional purposes)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPublicDiscounts(Request $request)
    {
        try {
            // Get active discounts without customer context
            $discounts = CustomDiscount::active()
                ->valid()
                ->orderBy('setPriority', 'desc')
                ->get();

            $discountList = [];
            if(empty($discounts)){
                return response()->json([
                    'status' => true,
                    'message' => 'No discounts found',
                    'data' => []
                ]);
            }
            foreach ($discounts as $discount) {
                $discountData = [
                    'id' => $discount->id,
                    'name' => $discount->couponName,
                    'logo' => $discount->logo,
                    'discount_type' => $discount->discountType,
                    'discount_value' => $discount->discountValue,
                    'discount_method' => $discount->discount,
                    'min_buy_qty' => $discount->minBuyQty,
                    'max_buy_qty' => $discount->maxBuyQty,
                    'use_limit' => $discount->useLimit,
                    'priority' => $discount->setPriority,
                    'apply_date' => $discount->applyDate,
                    'end_date' => $discount->endDate,
                    'is_primary' => $discount->isPrimary,
                    'filter_summary' => $this->getFilterSummary($discount->filter),
                    'applicability' => $this->getApplicabilityText($discount->filter)
                ];

                // Add specific details based on discount type
                if ($discount->discountType === 'buyXgetY') {
                    $customMeta = json_decode($discount->custom_meta, true);
                    $discountData['buy_x_get_y_details'] = [
                        'buy_quantity' => $customMeta['buy_quantity'] ?? null,
                        'is_recursive' => $customMeta['is_recursive'] ?? false,
                        'free_products' => $customMeta['get_y_products'] ?? []
                    ];
                }

                if ($discount->discountType === 'cartAdjustment') {
                    $rulesOnCart = json_decode($discount->rulesOnCart, true);
                    $discountData['cart_rules'] = [
                        'min_order_value' => $rulesOnCart['minOrderValue'] ?? null,
                        'max_discount_amount' => $rulesOnCart['maxDiscountAmount'] ?? null
                    ];
                }

                if ($discount->discountType === 'freeShipping') {
                    $rulesOnCart = json_decode($discount->rulesOnCart, true);
                    $discountData['shipping_rules'] = [
                        'min_order_value' => $rulesOnCart['minOrderValue'] ?? null,
                        'max_discount_amount' => $rulesOnCart['maxDiscountAmount'] ?? null
                    ];
                }

                $discountList[] = $discountData;
            }

            return response()->json([
                'status' => true,
                'message' => 'Running discounts retrieved successfully',
                'data' => $discountList,
                'total_count' => count($discountList)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve discounts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single discount details with filter criteria
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDiscountDetails(Request $request, $id)
    {
        try {
            // Get authenticated customer
            $customer = Auth::guard('api')->user();
            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not authenticated'
                ], 401);
            }
            $priceGroupId = null;
            if($customer->price_tier){
                $priceTier = $customer->price_tier;
                $priceGroupId = key($priceTier); 
            }
            
            // Get the specific discount
            $discount = CustomDiscount::active()
                ->valid()
                ->where('id', $id)
                ->first();
            if (!$discount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Discount not found or not active'
                ], 404);
            }
            // Check if discount is applicable for this customer
            // if (!$this->discountService->isDiscountApplicable($discount, null, null, 0, [])) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Discount has been closed or expired'
            //     ], 403);
            // }

            $filter = json_decode($discount->filter, true);
            if(empty($filter)){
                return response()->json([
                    'status' => false,
                    'message' => 'Filter not found'
                ],200);
            }
            $discountData = [
                'id' => $discount->id,
                'name' => $discount->couponName,
                'logo' => $discount->logo,
                'discount_type' => $discount->discountType ?? null,
                'discount_value' => $discount->discountValue ?? null,
                'discount_method' => $discount->discount ?? null,
                'min_buy_qty' => $discount->minBuyQty ?? null,
                'max_buy_qty' => $discount->maxBuyQty ?? null,
                'use_limit' => $discount->useLimit ?? null,
                'priority' => $discount->setPriority ?? null,
                'apply_date' => $discount->applyDate ?? null,
                'end_date' => $discount->endDate ?? null,
                'is_primary' => $discount->isPrimary ?? null,
                'filter_summary' => $this->getFilterSummary($filter, $priceGroupId) ?? null,
                'applicability' => $this->getApplicabilityText($filter) ?? null,
                'filter_details' => $this->getDetailedFilterInfo($filter) ?? null
            ];
            // Add specific details based on discount type
            if ($discount->discountType === 'buyXgetY') {
                $customMeta = json_decode($discount->custom_meta, true);
                $discountData['buy_x_get_y_details'] = [
                    'buy_quantity' => $customMeta['buy_quantity'] ?? null,
                    'is_recursive' => $customMeta['is_recursive'] ?? false,
                    'free_products' => $this->getFreeProductsDetails($customMeta['get_y_products'] ?? [])
                ];
            }
            if ($discount->discountType === 'cartAdjustment') {
                $rulesOnCart = json_decode($discount->rulesOnCart, true);
                $discountData['cart_rules'] = [
                    'min_order_value' => $rulesOnCart['minOrderValue'] ?? null,
                    'max_discount_amount' => $rulesOnCart['maxDiscountAmount'] ?? null
                ];
            }
            if ($discount->discountType === 'freeShipping') {
                $rulesOnCart = json_decode($discount->rulesOnCart, true);
                $discountData['shipping_rules'] = [
                    'min_order_value' => $rulesOnCart['minOrderValue'] ?? null,
                    'max_discount_amount' => $rulesOnCart['maxDiscountAmount'] ?? null
                ];
            }
            return response()->json([
                'status' => true,
                'message' => 'Discount details retrieved successfully',
                'data' => $discountData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve discount details and error is '.$e->getMessage() .' and line is '.$e->getLine(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filter summary text
     * 
     * @param array $filter
     * @return string
     */
    private function getFilterSummary($filter, $priceGroupId = null, $sortBy = 'latest', $perPage = 16, $page = 1, $customer = null)
    {
        $categoryIds = [];
        $brandIds = [];
        $productIds = [];
        $variationIds = [];

        if (is_string($filter)) {
            $filter = json_decode($filter, true);
        }

        if (isset($filter['categories'])) {
            $categoryIds = $filter['categories']['ids'] ?? [];
        }

        if (isset($filter['brands'])) {
            $brandIds = $filter['brands']['ids'] ?? [];
        }

        if (isset($filter['product_ids'])) {
            $productIds = $filter['product_ids']['ids'] ?? [];
        }

        if (isset($filter['variation_ids'])) {
            $variationIds = $filter['variation_ids']['ids'] ?? [];
        }

        $products = Product::with('webcategories', 'brand')
            ->where(function ($query) use ($categoryIds, $brandIds, $productIds, $variationIds) {
                $hasAnyFilter = !empty($categoryIds) || !empty($brandIds) || !empty($productIds) || !empty($variationIds);

                if ($hasAnyFilter) {
                    $query->where(function ($q) use ($categoryIds, $brandIds, $productIds, $variationIds) {
                        if (!empty($productIds)) {
                            $q->orWhereIn('products.id', $productIds);
                        }
                        if (!empty($categoryIds)) {
                            $q->orWhereHas('webcategories', function ($q2) use ($categoryIds) {
                                $q2->whereIn('categories.id', $categoryIds)
                                    ->where('category_type', 'product');
                            });
                        }
                        if (!empty($brandIds)) {
                            $q->orWhereHas('brand', function ($q2) use ($brandIds) {
                                $q2->whereIn('brands.id', $brandIds);
                            });
                        }
                        if (!empty($variationIds)) {
                            $q->orWhereHas('variations', function ($q2) use ($variationIds) {
                                $q2->whereIn('variations.id', $variationIds);
                            });
                        }
                    });
                }
            })
            ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
            ->where('products.enable_selling', 1)
            ->where('products.is_inactive', 0);

        if ($priceGroupId) {
            $products->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                    ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
            });
        }

        // Select + group
        if ($priceGroupId) {
            $products->selectRaw('products.*, COALESCE(MIN(variation_group_prices.price_inc_tax), variations.sell_price_inc_tax) as ad_price')
                ->groupBy('products.id')
                ->havingRaw('ad_price IS NOT NULL');
        } else {
            $products->selectRaw('products.*, variations.sell_price_inc_tax as ad_price')
                ->groupBy('products.id')
                ->havingRaw('ad_price IS NOT NULL');
        }

        // Apply sorting
        switch ($sortBy) {
            case 'low-to-high':
                $products = $products->orderBy('ad_price', 'asc');
                break;
            case 'high-to-low':
                $products = $products->orderBy('ad_price', 'desc');
                break;
            case 'top-selling':
                $products = $products->orderBy('top_selling', 'desc');
                break;
            case 'latest':
            default:
                $products = $products->orderBy('products.created_at', 'desc');
                break;
        }

        // Paginate results
        $products = $products->paginate($perPage, ['products.*'], 'page', $page);

        return [
            'current_page' => $products->currentPage(),
            'data' => CateLogResource::collection($products->getCollection()),
            'last_page' => $products->lastPage(),
            'total' => $products->total(),
            'from' => $products->firstItem(),
        ];
    }


    /**
     * Get applicability text
     * 
     * @param array $filter
     * @return string
     */
    private function getApplicabilityText($filter)
    {
        if (!$filter) {
            return 'Applicable on all products';
        }

        $applicability = [];

        if (isset($filter['categories'])) {
            $operation = $filter['categories']['opration'] ?? 'in';
            $categoryIds = $filter['categories']['ids'] ?? [];
            $categories = Category::whereIn('id', $categoryIds)->pluck('name')->toArray();
            
            if ($operation === 'in') {
                $applicability[] = 'Categories: ' . implode(', ', $categories);
            } else {
                $applicability[] = 'All categories except: ' . implode(', ', $categories);
            }
        }

        if (isset($filter['brands'])) {
            $operation = $filter['brands']['opration'] ?? 'in';
            $brandIds = $filter['brands']['ids'] ?? [];
            $brands = Brands::whereIn('id', $brandIds)->pluck('name')->toArray();
            
            if ($operation === 'in') {
                $applicability[] = 'Brands: ' . implode(', ', $brands);
            } else {
                $applicability[] = 'All brands except: ' . implode(', ', $brands);
            }
        }

        if (isset($filter['product_ids'])) {
            $operation = $filter['product_ids']['opration'] ?? 'in';
            $productIds = $filter['product_ids']['ids'] ?? [];
            $products = Product::whereIn('id', $productIds)->pluck('name')->toArray();
            
            if ($operation === 'in') {
                $applicability[] = 'Products: ' . implode(', ', $products);
            } else {
                $applicability[] = 'All products except: ' . implode(', ', $products);
            }
        }

        return count($applicability) ? implode(' | ', $applicability) : 'Applicable on all products';
    }

    /**
     * Get detailed filter information
     * 
     * @param array $filter
     * @return array
     */
    private function getDetailedFilterInfo($filter)
    {
        if (!$filter) {
            return [
                'type' => 'all_products',
                'description' => 'Applicable on all products'
            ];
        }

        $filterInfo = [];

        if (isset($filter['categories'])) {
            $operation = $filter['categories']['opration'] ?? 'in';
            $categoryIds = $filter['categories']['ids'] ?? [];
            $categories = Category::whereIn('id', $categoryIds)
                ->select('id', 'name','slug')
                ->get()
                ->toArray();

            $filterInfo['categories'] = [
                'operation' => $operation,
                'description' => $operation === 'in' ? 'Included categories' : 'Excluded categories',
                'items' => $categories
            ];
        }

        if (isset($filter['brands'])) {
            $operation = $filter['brands']['opration'] ?? 'in';
            $brandIds = $filter['brands']['ids'] ?? [];
            $brands = Brands::whereIn('id', $brandIds)
                ->select('id', 'name','slug')
                ->get()
                ->toArray();

            $filterInfo['brands'] = [
                'operation' => $operation,
                'description' => $operation === 'in' ? 'Included brands' : 'Excluded brands',
                'items' => $brands
            ];
        }

        if (isset($filter['product_ids'])) {
            $operation = $filter['product_ids']['opration'] ?? 'in';
            $productIds = $filter['product_ids']['ids'] ?? [];
            $products = Product::whereIn('id', $productIds)
                ->select('id', 'name', 'sku','slug')
                ->get()
                ->toArray();

            $filterInfo['products'] = [
                'operation' => $operation,
                'description' => $operation === 'in' ? 'Included products' : 'Excluded products',
                'items' => $products
            ];
        }

        if (isset($filter['variation_ids'])) {
            $operation = $filter['variation_ids']['opration'] ?? 'in';
            $variationIds = $filter['variation_ids']['ids'] ?? [];
            
            $filterInfo['variations'] = [
                'operation' => $operation,
                'description' => $operation === 'in' ? 'Included variations' : 'Excluded variations',
                'count' => count($variationIds),
                'variation_ids' => $variationIds
            ];
        }

        return $filterInfo;
    }

    /**
     * Get free products details for buyXgetY discounts
     * 
     * @param array|null $freeProducts
     * @return array
     */
    private function getFreeProductsDetails($freeProducts)
    {
        $details = [];

        // Ensure input is an array
        if (!is_array($freeProducts) || empty($freeProducts)) {
            return $details;
        }

        foreach ($freeProducts as $freeProduct) {
            if (!isset($freeProduct['product_id'])) {
                continue;
            }

            $product = Product::find($freeProduct['product_id']);

            if ($product) {
                $details[] = [
                    'product_id' => $freeProduct['product_id'],
                    'variation_id' => $freeProduct['variation_id'] ?? null,
                    'quantity' => $freeProduct['quantity'] ?? 1,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'slug' => $product->slug,
                    'product_image' => $product->image_url,
                ];
            }
        }

        return $details;
    }

} 