<?php

namespace App\Http\Controllers\B2CECOM;

use App\Cart;
use App\CartItem;
use App\GuestCartItem;
use App\Contact;
use App\Http\Controllers\Controller;
use App\Jobs\UnfreezeCart;
use App\LocationTaxCharge;
use App\Product;
use App\Variation;
use App\VariationGroupPrice;
use App\VariationLocationDetails;
use App\CustomDiscount;
use App\GuestSession;
use App\Services\CustomDiscountRuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\EcomReferalProgram;

class UnifiedCartController extends Controller
{
    public $cartDiscountApplicable;
    public $freeShippingApplicable;

    public function __construct()
    {
        $this->cartDiscountApplicable = true; // Track if cart-level discounts can be applied
        $this->freeShippingApplicable = true; // Track if free shipping can be applied
    }

    // helper function start
    /**
     * Check product order limit 
     * product_order_limit_consumers table 
     * product_order_limits table
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param int $productId
     * @param int $variantId
     * @param int $contactId
     * @return array
     */
    public function allowedItemQty($productId = null, $variantId = null, $contactId = null, $qtyGoingToAdd = 1, $currentTime = null, $maxSaleLimit = null)
    {
        $currentTime = $currentTime instanceof Carbon
            ? $currentTime
            : Carbon::parse($currentTime);
        // First check for variant-specific limits, then fallback to product-level limits
        $productSessions = DB::table('product_order_limits')
            ->where(function ($query) use ($productId, $variantId) {
                $query->where('variant_id', $variantId)
                    ->orWhere(function ($q) use ($productId) {
                        $q->where('product_id', $productId)
                            ->whereNull('variant_id');
                    });
            })
            ->where('is_active', 1)
            ->get();

        if ($productSessions) {
            foreach ($productSessions as $productSession) {
                $startTime = Carbon::parse($productSession->start_date ?? '2000-01-01 00:00:00');
                $endTime = Carbon::parse($productSession->end_date ?? '2099-12-31 23:59:59');

                if ($currentTime->between($startTime, $endTime)) {
                    $sessionLimitId = $productSession->id;
                    $maxLimit = $productSession->order_limit;

                    // when no limit set 
                    if ($maxLimit === 0 && ($maxSaleLimit == 0 || $maxSaleLimit == null)) {
                        return ['status' => true, 'can_add' => true, 'allowed_qty' => $qtyGoingToAdd];
                    }

                    // when limit set 
                    // do check if consumer already have order in this session 
                    $limitRecord = DB::table('product_order_limit_consumers')
                        ->where('session_id', $sessionLimitId)
                        ->where('consumer_id', $contactId)
                        ->first();

                    $orderCount = $limitRecord->order_count ?? 0;
                    $qtyCount = $limitRecord->qty_count ?? 0;

                    $willExceedQty = null;
                    $willExceedOrders = null;
                    if ($maxSaleLimit > 0 && $qtyGoingToAdd >= $maxSaleLimit) {
                        $willExceedQty = $qtyGoingToAdd - $maxSaleLimit;
                        $qtyGoingToAdd = $maxSaleLimit;
                    };

                    if ($maxLimit > 0) {
                        // Human generated correct code 
                        $thershold  = $maxLimit * $maxSaleLimit;
                        $remainingQty = $thershold - $qtyCount;
                        $willExceedOrders = $remainingQty < $qtyGoingToAdd ? $qtyGoingToAdd - $remainingQty : 0;
                        $qtyGoingToAdd = min($qtyGoingToAdd, $remainingQty);
                    }

                    $remainingOrders = $maxLimit > 0 ? max(0, $maxLimit - $orderCount) : 1;
                    $remainingQty = $qtyGoingToAdd;

                    // Do Log
                    if ($willExceedQty || $willExceedOrders) {
                        // Get existing meta data
                        $existingMeta = !empty($limitRecord->meta) ? json_decode($limitRecord->meta, true) : [];

                        // Prepare new log entry
                        $newLogEntry = [
                            'timestamp' => $currentTime,
                            'product_id' => $productId ?? null,
                            'variant_id' => $variantId ?? null,
                            'contact_id' => $contactId ?? null,
                            'qty_going_to_add' => $qtyGoingToAdd ?? null,
                            'will_exceed_qty' => $willExceedQty ?? null,
                            'will_exceed_orders' => $willExceedOrders ?? null,
                            'remaining_qty' => $remainingQty ?? null,
                            'remaining_orders' => $remainingOrders ?? null,
                        ];

                        // Merge with existing logs
                        if (!isset($existingMeta['log_history'])) {
                            $existingMeta['log_history'] = [];
                        }

                        $existingMeta['log_history'][] = $newLogEntry;
                        $existingMeta['blocked_at'] = $currentTime;
                        $existingMeta['blocked_attempts'] = ($existingMeta['blocked_attempts'] ?? 0) + 1;

                        // Update the meta column with merged data using DB::table()->update()
                        DB::table('product_order_limit_consumers')
                            ->where('id', $limitRecord->id)
                            ->update([
                                'meta' => json_encode($existingMeta),
                                'blocked_attempts' => $existingMeta['blocked_attempts'],
                                'blocked_at' => $currentTime,
                                'updated_at' => now()
                            ]);
                    }

                    if ($qtyGoingToAdd <= 0) {
                        return [
                            'status' => false,
                            'can_add' => false,
                            'allowed_qty' => 0,
                            'remaining_orders' => 0,
                        ];
                    }

                    // Valid
                    return [
                        'status' => true,
                        'can_add' => false,
                        'allowed_qty' => $remainingQty,
                        'remaining_orders' => $remainingOrders,
                    ];
                }
            }
            // mean previous did order 
            return [
                'status' => true,
                'can_add' => true, // qty going to add 
                'allowed_qty' => $maxSaleLimit,
                'remaining_orders' => 0,
            ];
        } else {
            // can add upto max sale limit 
            return [
                'status' => true,
                'can_add' => true, // qty going to add
                'allowed_qty' => $maxSaleLimit,
                'remaining_orders' => 0,
            ];
        }
    }

    /**
     * Get or create cart for user
     */
    public function getOrCreateCart($contact)
    {
        $userId = $contact->id;
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $userId,
                'isFreeze' => false,
                'billing_first_name' => $contact->first_name,
                'billing_last_name' => $contact->last_name,
                'billing_company' => $contact->supplier_business_name,
                'billing_address1' => $contact->address_line_1,
                'billing_address2' => $contact->address_line_2,
                'billing_city' => $contact->city,
                'billing_state' => $contact->state,
                'billing_zip' => $contact->zip_code,
                'billing_country' => $contact->country,
                'billing_phone' => $contact->mobile,
                'billing_email' => $contact->email,
                'shipping_first_name' => $contact->shipping_first_name,
                'shipping_last_name' => $contact->shipping_last_name,
                'shipping_company' => $contact->shipping_company,
                'shipping_address1' => $contact->shipping_address1,
                'shipping_address2' => $contact->shipping_address2,
                'shipping_city' => $contact->shipping_city,
                'shipping_state' => $contact->shipping_state,
                'shipping_zip' => $contact->shipping_zip,
                'shipping_country' => $contact->shipping_country,
            ]);
        }

        return $cart;
    }

    /**
     * Get cart items with validation
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param int $userId
     * @return array
     */
    public function getCartItems($userId)
    {
        $cartItems = CartItem::where('user_id', $userId)->get();

        if (empty($cartItems)) {
            return ['status' => false, 'message' => 'Cart is empty.'];
        }

        return ['status' => true, 'data' => $cartItems];
    }

    /**
     * Get products with all necessary relationships (B2C version - uses sell_price_inc_tax)
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param array $productIds
     * @param int $userId
     * @param int $priceGroupId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProductsWithRelations($productIds, $userId, $priceGroupId, $isKeyByID = false)
    {
        $products = Product::with([
            'webcategories',
            'brand',
            'customer_price_recalls' => function ($query) use ($userId) {
                $query->where('contact_id', $userId)
                    ->where('is_active', 1)
                    ->where('is_deleted', 0)
                    ->with(['updatedBy' => function ($q) {
                        $q->select('id', 'first_name', 'last_name');
                    }]);
            },
            'variations' => function ($query) {
                $query->select([
                    'variations.id',
                    'variations.name',
                    'variations.product_id',
                    'variations.var_barcode_no',
                    'variations.var_maxSaleLimit',
                    'variations.product_variation_id',
                    'variations.sell_price_inc_tax as ad_price', // B2C uses sell_price_inc_tax
                    'variation_location_details.in_stock_qty as qty',
                    'variations.default_purchase_price',
                ])
                    ->leftJoin('variation_location_details', function ($join) {
                        $join->on('variations.id', '=', 'variation_location_details.variation_id');
                    });
            },
            'variations.media' => function ($query) {
                $query->select('id', 'file_name', 'model_id');
            }
        ])
            ->whereIn('id', $productIds)
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->get();

        if ($isKeyByID) {
            return $products->keyBy('id');
        }

        return $products;
    }

    /**
     * Get tax charges for user state
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param string $userState
     * @return \Illuminate\Support\Collection
     */
     public function getTaxCharges($userState)
     {
         $brandId = request()->input('brand_id');
         $locationId = request()->route('location_id');
 
         if ($brandId) {
             $brandTaxes = LocationTaxCharge::where('brand_id', $brandId)
                 ->where('state_code', $userState)
                 ->get();
             if ($brandTaxes->isNotEmpty()) {
                 return $brandTaxes;
             }
         }
         $locationId = request()->route('location_id');
         if ($locationId) {
             $locationTaxes = LocationTaxCharge::where('web_location_id', $locationId)->whereNull('brand_id')
                 ->where('state_code', $userState)
                 ->get();
             if ($locationTaxes->isNotEmpty()) {
                 return $locationTaxes;
             }
         }
         // If no brand_id or location_id, return empty collection (no generic taxes)
        return collect([]);    
    }


    /**
     * Calculate unit price with price recall
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param object $variation
     * @param object $product
     * @param int $userId
     * @return float
     */
    public function calculateUnitPrice($variation, $product, $userId)
    {
        $unitPrice = $variation?->ad_price;

        // Apply price recall if exists
        $priceRecall = $product->customer_price_recalls
            ->where('variation_id', $variation?->id)
            ->first();

        if ($priceRecall) {
            $unitPrice = $priceRecall->new_price;
        }

        return $unitPrice;
    }

    /**
     * Validate and clean up applied discounts
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param array $appliedDiscounts
     * @param \Illuminate\Database\Eloquent\Collection $discounts
     * @param array $cartItems
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @param object $discountService
     * @return array
     */
    public function validateAppliedDiscount($appliedDiscounts, $discounts, $cartItems, $products, $discountService, $userId = null, $locationId = null, $brandId = null)
    {
        $validAppliedDiscounts = [];
        foreach ($appliedDiscounts as $appliedDiscount) {
            $isValid = false;
            
            // First check if this is a referral coupon
            $referalRecord = \App\Models\EcomReferalProgram::where('coupon_code', $appliedDiscount)->first();
            
            if ($referalRecord) {
                // This is a referral coupon - use validateCoupon to check it
                $cartTotal = 0;
                foreach ($cartItems as $cartItem) {
                    $product = $products->where('id', $cartItem->product_id)->first();
                    if ($product) {
                        $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                        if ($variation) {
                            $unitPrice = $variation->ad_price ?? $variation->default_sell_price ?? 0;
                            $cartTotal += $unitPrice * $cartItem->qty;
                        }
                    }
                }
                
                $validation = $discountService->validateCoupon(
                    $appliedDiscount,
                    $cartItems,
                    $products,
                    $cartTotal,
                    $userId,
                    $locationId,
                    $brandId
                );
                
                if ($validation['status']) {
                    $isValid = true;
                }
            } else {
                // Regular discount - check in the discounts collection
                foreach ($discounts as $discount) {
                    if ($discount->couponCode === $appliedDiscount) {
                        // Check based on discount type
                        if ($discount->discountType === 'cartAdjustment' || $discount->discountType === 'freeShipping') {
                            // For cart-level discounts, check against entire cart
                            if ($discountService->isDiscountApplicable($discount, $cartItems, $products, 0, [$appliedDiscount])) {
                                $isValid = true;
                            }
                        } else {
                            // For product-level discounts, check if any product in cart is eligible
                            foreach ($cartItems as $cartItem) {
                                $product = $products->where('id', $cartItem->product_id)->first();
                                if ($product) {
                                    $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                                    if ($discountService->isDiscountApplicable($discount, $product, $variation, $cartItem->qty, [$appliedDiscount])) {
                                        $isValid = true;
                                        break;
                                    }
                                }
                            }
                        }
                        break; // Found the discount, no need to check others
                    }
                }
            }
            
            if ($isValid) {
                $validAppliedDiscounts[] = $appliedDiscount;
            }
        }
        return $validAppliedDiscounts;
    }

    /**
     * Get discount object from coupon code (including referral coupons)
     * @param string $couponCode
     * @param \Illuminate\Database\Eloquent\Collection $discounts
     * @param int|null $userId
     * @param int|null $locationId
     * @param int|null $brandId
     * @return object|null
     */
    private function getDiscountFromCouponCode($couponCode, $discounts, $userId = null, $locationId = null, $brandId = null)
    {
        // First check if it's a referral coupon
        $referalRecord = \App\Models\EcomReferalProgram::where('coupon_code', $couponCode)->first();
        
        if ($referalRecord) {
            // Fetch the referral discount template
            $discount = \App\Models\CustomDiscount::where('id', $referalRecord->discount_id)
                ->where('isDisabled', 0)
                ->where('is_referal_program_discount', true)
                ->where(function($query) {
                    $query->whereNull('applyDate')
                          ->orWhere('applyDate', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('endDate')
                          ->orWhere('endDate', '>=', now());
                })
                ->when($locationId !== null && $locationId !== 'all', function($q) use ($locationId) {
                    $q->where('location_id', $locationId);
                })
                ->when($brandId !== null && $brandId !== 'all', function($q) use ($brandId) {
                    $q->where(function ($q2) use ($brandId) {
                        $q2->whereRaw('JSON_CONTAINS(brand_id, ?)', [json_encode((string) $brandId)])
                            ->orWhereRaw('JSON_CONTAINS(brand_id, ?)', [json_encode('all')]);
                    });
                })
                ->first();
            
            // Set the actual referral coupon code on the discount object
            // This ensures isDiscountApplicable checks will pass
            if ($discount) {
                \Log::info('[GetDiscountFromCouponCode] Setting referral coupon code', [
                    'original_code' => $discount->couponCode,
                    'new_code' => $couponCode,
                    'discount_id' => $discount->id
                ]);
                $discount->couponCode = $couponCode;
                \Log::info('[GetDiscountFromCouponCode] After setting', [
                    'discount_coupon_code' => $discount->couponCode
                ]);
            }
            
            return $discount;
        }
        
        // Regular discount - find in the discounts collection
        return $discounts->firstWhere('couponCode', $couponCode);
    }

    /** 
     * Calculate high priority discount (product adjustment and bxgy)
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param array $appliedDiscounts
     * @param \Illuminate\Database\Eloquent\Collection $discounts
     * @param array $cartItems
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @param object $discountService
     * @return array
     */
    public function calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, $cartItems, $discountService, $userId = null, $locationId = null, $brandId = null)
    {
        // high priority discount (product adjustment and bxgy)
        // Handle both single cart item object and array of cart items
        $quantity = 0;
        if (is_array($cartItems) || $cartItems instanceof \Illuminate\Support\Collection) {
            $quantity = $cartItems[0]->qty ?? 0;
        } elseif (is_object($cartItems) && isset($cartItems->qty)) {
            $quantity = $cartItems->qty;
        }
        
        // Build collection of all applicable discounts including referral coupons
        $allDiscounts = collect($discounts);
        
        // Add/update referral discounts from applied discounts
        foreach ($appliedDiscounts as $couponCode) {
            $discount = $this->getDiscountFromCouponCode($couponCode, $discounts, $userId, $locationId, $brandId);
            if ($discount) {
                // Check if this discount is already in the collection
                $existingIndex = $allDiscounts->search(function($item) use ($discount) {
                    return $item->id === $discount->id;
                });
                
                if ($existingIndex !== false) {
                    // Replace the existing discount with our updated one (with correct couponCode)
                    $allDiscounts->put($existingIndex, $discount);
                    \Log::info('[CalculateHighPriorityDiscount] Replaced existing discount with updated couponCode', [
                        'discount_id' => $discount->id,
                        'coupon_code' => $discount->couponCode
                    ]);
                } else {
                    // Add new discount
                    $allDiscounts->push($discount);
                    \Log::info('[CalculateHighPriorityDiscount] Added new referral discount', [
                        'discount_id' => $discount->id,
                        'coupon_code' => $discount->couponCode
                    ]);
                }
            }
        }
        
        $eligibleDiscounts = $allDiscounts
            ->filter(function ($discount) use ($discountService, $product, $variation, $quantity, $appliedDiscounts) {
                \Log::info('[CalculateHighPriorityDiscount] Evaluating discount', [
                    'discount_id' => $discount->id,
                    'discount_type' => $discount->discountType,
                    'discount_coupon_code' => $discount->couponCode,
                    'is_referal' => $discount->is_referal_program_discount ?? false
                ]);
                
                $isApplicable = in_array($discount->discountType, ['productAdjustment', 'buyXgetY']) &&
                    $discountService->isDiscountApplicable($discount, $product, $variation, $quantity, $appliedDiscounts);
                
                \Log::info('[CalculateHighPriorityDiscount] Discount applicable?', [
                    'discount_id' => $discount->id,
                    'is_applicable' => $isApplicable
                ]);
                
                return $isApplicable;
            })
            ->sortByDesc('setPriority')
            ->values();

        // Pick the discount with the highest priority (highest setPriority number)
        $appliedDiscount = $eligibleDiscounts->first();
        
        \Log::info('[CalculateHighPriorityDiscount] Selected discount', [
            'selected_discount_id' => $appliedDiscount?->id ?? null,
            'selected_coupon_code' => $appliedDiscount?->couponCode ?? null
        ]);

        return $appliedDiscount;
    }

    /**
     * Apply tax calculations after discount applied
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param object $product
     * @param object $variation
     * @param float $discountedPrice
     * @param object $taxCharges
     */

     /**

  * Apply tax calculations after discount applied
 *
 * @version 1.0.0
 * @author Utkarsh Shukla
 * @param object $product
 * @param object $variation
 * @param float $discountedPrice
 * @param object $taxCharges
 * @param string $userState
 */
public function applyTaxCalculations($product, $variation, $discountedPrice, $taxCharges, $userState)
{
    // Handle empty or null taxCharges collection
    if (!$taxCharges || $taxCharges->isEmpty()) {
        return $discountedPrice; // No tax to apply
    }
    $productLocationID = $product->locationTaxType[0] ?? null;
    // If product doesn't have locationTaxType set, return without tax
    if (!$productLocationID) {
        return $discountedPrice;
    } 
    $default_purchase_price = $variation?->default_purchase_price;

    $charges = $taxCharges
        ->where('location_id', $productLocationID)
        ->where('state_code', $userState)
        ->first();
    // Apply tax if charges found
    if ($charges) {
        $taxType = $charges->tax_type;
        $value = $charges->value;

        switch ($taxType) {
            case 'UNIT_BASIS_ML':
                if (!empty($product->ml) && $product->ml > 0) {
                    $discountedPrice += $product->ml * $value;
                }
                break;

            case 'FLAT_RATE':
                $discountedPrice += $value;
                break;

            case 'PERCENTAGE_ON_SALE':
                $discountedPrice += ($value / 100) * $discountedPrice;
                break;

            case 'PERCENTAGE_ON_COST':
                $discountedPrice += ($value / 100) * $default_purchase_price; // trump tax
                break;

            case 'UNIT_COUNT':
                if (!empty($product->ct) && $product->ct > 0) {
                    $discountedPrice += $product->ct * $value;
                }
                break;
        }
    }

    return $discountedPrice;
}


    /**
     * Get variation image
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param object $variation
     * @param object $product
     * @return string
     */
    public function getVariationImage($variation, $product)
    {
        try {
            $variationImage = $variation?->media[0]?->display_url ?? null;
        } catch (\Exception $e) {
            $variationImage = null;
        }

        return $variationImage ?? $product->image_url;
    }

    /**
     * Build cart item data
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param object $cartItem
     * @param object $product
     * @param object $variation
     * @param float $discountedPrice
     */
    public function buildCartItemData($cartItem, $product, $variation, $discountedPrice, $itemDiscounts, $variationImage)
    {
        return [
            'key' => $cartItem->id,
            'product_id' => $product->id,
            'variation_id' => $variation?->id ?? null,
            'ml' => $product->ml ?? null,
            'ct' => $product->ct ?? null,
            'locationTaxType' => $product->locationTaxType ?? null,
            'maxSaleLimit' => $product->maxSaleLimit ?? null,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'product_price' => $variation?->ad_price ?? 0,
            'discounted_price' => $discountedPrice,
            'discounts' => $itemDiscounts,
            'product_image' => $variationImage,
            'variation_name' => $variation?->name == 'DUMMY' ? '' : $variation?->name ?? null,
            'stock' => $variation?->qty ?? 0,
            'sku' => $variation?->sub_sku ?? null,
            'itemBarcode' => $variation?->var_barcode_no ?? null,
            'purchaseLimit' => $variation?->var_maxSaleLimit ?? null,
            'stock_status' => ($variation?->qty ?? 0) > 0 ? 'instock' : 'outofstock',
            'qty' => $cartItem->qty,
        ];
    }

    // helper function end

    /**
     * Get cart data with discount validation
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return object
     */
    public function getCart(Request $request)
    {
        $isGuestRequest = $request->attributes->get('is_guest_request', false);

        if ($isGuestRequest) {
            return $this->getGuestCart($request);
        } else {
            return $this->getCustomerCart($request);
        }
    }

    /**
     * Get cart for authenticated customers - follows B2B structure exactly
     */
    private function getCustomerCart(Request $request)
    {
        $contact = Auth::guard('api')->user();
        $this->setReferralCouponsForRequest($contact);
        $userId = $contact->id;
        $total_tax_on_cart = 0;

        try {
            // B2C customers: get price group if exists, otherwise use null
            $priceTier = $contact->price_tier;
            $priceGroupId = key($priceTier);
            // For B2C customers without a group, priceGroupId is 0, set it to null
            $priceGroupId = ($priceGroupId === 0) ? null : $priceGroupId;

            // Get cart items
            $cartItemGet = $this->getCartItems($userId);
            if ($cartItemGet['status'] == false) {
                return response()->json(['status' => false, 'message' => $cartItemGet['message']]);
            }
            $cartItems = $cartItemGet['data'];
            $productIds = $cartItems->pluck('product_id');

            // Get cart (checkout data)
            $cart = Cart::where('user_id', $userId)->first();
            $userState = $request->query('shipping_state') ?? $cart->shipping_state ?? $contact->shipping_state;
            $taxCharges = $this->getTaxCharges($userState);

            // Get discounts service 
            $discountService = new CustomDiscountRuleService();
            // fetch customers referal code if any applicable and put in request()
            
            $locationId = $contact->location_id;
            $brandId = $contact->brand_id;
            $discounts = $discountService->getActiveDiscounts($contact, $locationId, $brandId);
            $appliedDiscounts = $cart->applied_discounts ?? [];

            // Get products with relation 
            $products = $this->getProductsWithRelations($productIds, $userId, $priceGroupId);

            // Validate and clean up applied discounts
            $validAppliedDiscounts = $this->validateAppliedDiscount($appliedDiscounts, $discounts, $cartItems, $products, $discountService, $userId, $locationId, $brandId);
            if ($validAppliedDiscounts !== $appliedDiscounts) {
                $cart->update(['applied_discounts' => $validAppliedDiscounts]);
                $appliedDiscounts = $validAppliedDiscounts;
            }

            // Use the same complex cart building logic as B2B
            return $this->buildComplexCartResponse($cartItems, $products, $contact, $appliedDiscounts, $discounts, $discountService, $taxCharges, $userState);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Cart access failed', 'data' => $th->getMessage() . ' in line ' . $th->getLine() . ' at file ' . $th->getFile()]);
        }
    }

    /**
     * Get cart for guest users - with complex discount and tax calculations
     */
    private function getGuestCart(Request $request)
    {
        $guestSession = $request->attributes->get('current_guest_session');
        $location = $request->attributes->get('current_location');
        $brand = $request->attributes->get('current_brand');

        // Get guest cart items
        $cartItems = GuestCartItem::where('guest_session_id', $guestSession->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty.'
            ]);
        }

        $productIds = $cartItems->pluck('product_id');

        // Get products with relations (using location for guest)
        $products = $this->getGuestProductsWithRelations($productIds->toArray(), $location->id, $brand->id);

        // Get tax charges - use guest's shipping state or default to IL
        $userState =$request->query('shipping_state') ?? $guestSession->shipping_state ?? 'IL';
        $taxCharges = $this->getTaxCharges($userState);

        // Get discounts service for guest (using location and brand)
        $discountService = new CustomDiscountRuleService();
        $discounts = $discountService->getActiveDiscounts(null, $location->id, $brand->id);
        
        // Get applied discounts from guest session
        $appliedDiscounts = $guestSession->applied_discounts ?? [];

        // Validate and clean up applied discounts
        $validAppliedDiscounts = $this->validateAppliedDiscount($appliedDiscounts, $discounts, $cartItems, $products, $discountService, null, $location->id, $brand->id);
        if ($validAppliedDiscounts !== $appliedDiscounts) {
            $guestSession->update(['applied_discounts' => $validAppliedDiscounts]);
            $appliedDiscounts = $validAppliedDiscounts;
        }

        // Use the same complex cart building logic as customer cart
        return $this->buildComplexGuestCartResponse($cartItems, $products, $guestSession, $appliedDiscounts, $discounts, $discountService, $taxCharges, $userState);
    }

    /**
     * Get products with all necessary relationships for guest users (B2C version - uses sell_price_inc_tax)
     * @param array $productIds
     * @param int $locationId
     * @param int $brandId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getGuestProductsWithRelations($productIds, $locationId, $brandId,$isKeyByID = false)
    {
        $products = Product::with([
            'webcategories',
            'brand',
            'variations' => function ($query) use ($locationId) {
                $query->select([
                    'variations.id',
                    'variations.name',
                    'variations.product_id',
                    'variations.var_barcode_no',
                    'variations.var_maxSaleLimit',
                    'variations.product_variation_id',
                    'variations.sell_price_inc_tax as ad_price', // B2C uses sell_price_inc_tax
                    'variation_location_details.in_stock_qty as qty',
                    'variations.default_purchase_price',
                ])
                    ->leftJoin('variation_location_details', function ($join) use ($locationId) {
                        $join->on('variations.id', '=', 'variation_location_details.variation_id')
                            ->where('variation_location_details.location_id', '=', $locationId);
                    });
            },
            'variations.media' => function ($query) {
                $query->select('id', 'file_name', 'model_id');
            }
        ])
            ->whereIn('id', $productIds)
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->where('brand_id', $brandId)
            ->get();
        if ($isKeyByID) {
            return $products->keyBy('id');
        }    

        return $products;
    }

    private function buildGuestCartResponse($cartItems, $products, $guestSession)
    {
        $cartData = [];
        $count = 0;
        $cart_total_before_tax = 0;
        $cart_final_total = 0;

        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                $unitPrice = $variation?->ad_price ?? 0;

                $cart_total_before_tax += $unitPrice * $cartItem->qty;
                $cart_final_total += $unitPrice * $cartItem->qty;

                // Get variation image
                $variationImage = $this->getVariationImage($variation, $product);

                // Build cart data
                $cartData[] = [
                    'key' => $cartItem->id,
                    'product_id' => $product->id,
                    'variation_id' => $variation?->id ?? null,
                    'ml' => $product->ml ?? null,
                    'ct' => $product->ct ?? null,
                    'locationTaxType' => $product->locationTaxType ?? null,
                    'maxSaleLimit' => $product->maxSaleLimit ?? null,
                    'product_name' => $product->name,
                    'product_slug' => $product->slug,
                    'product_price' => $unitPrice,
                    'discounted_price' => $unitPrice,
                    'discounts' => [],
                    'product_image' => $variationImage,
                    'variation_name' => $variation?->name == 'DUMMY' ? '' : $variation?->name ?? null,
                    'stock' => $variation?->qty ?? 0,
                    'sku' => $variation?->sub_sku ?? null,
                    'itemBarcode' => $variation?->var_barcode_no ?? null,
                    'purchaseLimit' => $variation?->var_maxSaleLimit ?? null,
                    'stock_status' => ($variation?->qty ?? 0) > 0 ? 'instock' : 'outofstock',
                    'qty' => $cartItem->qty,
                ];

                $count++;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Cart Items',
            'data' => $cartData,
            'itemCount' => $count,
            'subtotal' => $cart_total_before_tax,
            'subtotal_inc_tax' => $cart_final_total,
            'total_tax_on_cart' => 0,
            'cartDiscountDetails' => [],
            'cartDiscountAmount' => 0,
            'freeShippingDiscountDetails' => [],
            'freeShippingDiscountAmount' => -1,
            'applied_discounts' => []
        ]);
    }

    /**
     * Build complex guest cart response with discount and tax calculations
     */
    private function buildComplexGuestCartResponse($cartItems, $products, $guestSession, $appliedDiscounts, $discounts, $discountService, $taxCharges, $userState)
    {
        $locationId = $guestSession->location_id;
        $brandId = $guestSession->brand_id;
        $total_tax_on_cart = 0;
        $cartData = [];
        $count = 0;
        $cart_total_before_tax = 0;
        $cart_final_total = 0;
        $temp = 0;
        $cartDiscountApplicable = true;
        
        // Track purchased quantities per variation for BxGY stock adjustment
        $purchasedVariationQuantities = [];
        foreach ($cartItems as $cartItem) {
            // Track purchased quantities
            if (!isset($purchasedVariationQuantities[$cartItem->variation_id])) {
                $purchasedVariationQuantities[$cartItem->variation_id] = 0;
            }
            $purchasedVariationQuantities[$cartItem->variation_id] += $cartItem->qty;
        }
        
        foreach ($cartItems as $cartItem) {
            $temp = 0;
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                if ($variation) {
                    $unitPrice = $variation->ad_price ?? 0;
                }
                $appliedDiscount = null;
                $appliedDiscount = $this->calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, [$cartItem], $discountService, null, $locationId, $brandId);
                if ($appliedDiscount) {
                    if ($appliedDiscount->discountType === 'productAdjustment') {
                        $productAdjustmentExists = true;
                    } elseif ($appliedDiscount->discountType === 'buyXgetY') {
                        $isBxGyApplicable = true;
                        $cartDiscountApplicable = false;
                    }
                }
                $discountedPrice = $unitPrice;
                $itemDiscounts = [];
                if ($appliedDiscount) {
                    if ($appliedDiscount->discountType === 'productAdjustment') {
                        $discountedPrice = $discountService->calculateDiscountedPrice($unitPrice, $appliedDiscount);
                        $cartDiscountApplicable = false;
                        $itemDiscounts[] = [
                            'name' => $appliedDiscount->couponName,
                            'code' => $appliedDiscount->couponCode,
                            'type' => $appliedDiscount->discountType,
                            'value' => $appliedDiscount->discountValue,
                            'original_price' => $unitPrice,
                            'discounted_price' => $discountedPrice,
                            'discount_lable' => $appliedDiscount->discount_lable
                        ];
                    } elseif ($appliedDiscount->discountType === 'buyXgetY') {
                        $isBxGyApplicable = true;
                        $cartDiscountApplicable = false;
                    }
                }
                $cart_total_before_tax += $discountedPrice * $cartItem->qty;
                $temp = $discountedPrice * $cartItem->qty;
                $discountedPrice = $this->applyTaxCalculations($product, $variation, $discountedPrice, $taxCharges, $userState);
                $cart_final_total += $discountedPrice * $cartItem->qty;
                $total_tax_on_cart += ($discountedPrice * $cartItem->qty) - $temp;
                $variationImage = $this->getVariationImage($variation, $product);
                $cartData[] = $this->buildCartItemData($cartItem, $product, $variation, $discountedPrice, $itemDiscounts, $variationImage);
                $count++;
            }
        }
        
        // Track purchased quantities per variation from cart items for stock calculation
        $purchasedVariationQuantities = [];
        foreach ($cartItems as $cartItem) {
            if (!isset($purchasedVariationQuantities[$cartItem->variation_id])) {
                $purchasedVariationQuantities[$cartItem->variation_id] = 0;
            }
            $purchasedVariationQuantities[$cartItem->variation_id] += $cartItem->qty;
        }
        
        $freeItemsToAdd = [];
        
        // Group matching items by discount to count total quantity
        $discountGroups = [];
        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if (!$product) continue;
            $variation = $product->variations->where('id', $cartItem->variation_id)->first();
            if (!$variation) continue;
            $appliedDiscount = null;
            $appliedDiscount = $this->calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, [$cartItem], $discountService, null, $locationId, $brandId);
            if ($appliedDiscount && $appliedDiscount->discountType === 'buyXgetY') {
                // Safety check: Skip if coupon required but not applied
                if (!empty($appliedDiscount->couponCode) && !in_array($appliedDiscount->couponCode, $appliedDiscounts)) {
                    continue;
                }
                
                $discountId = $appliedDiscount->id;
                if (!isset($discountGroups[$discountId])) {
                    $discountGroups[$discountId] = [
                        'discount' => $appliedDiscount,
                        'total_qty' => 0,
                        'items' => []
                    ];
                }
                $discountGroups[$discountId]['total_qty'] += $cartItem->qty;
                $discountGroups[$discountId]['items'][] = $cartItem;
            }
        }

        // Process each discount group
        foreach ($discountGroups as $discountGroup) {
            $appliedDiscount = $discountGroup['discount'];
            $totalQty = $discountGroup['total_qty'];
            
            $cartDiscountApplicable = false;
            $details = json_decode($appliedDiscount->custom_meta, true);
            $buyQuantity = $details['buy_quantity'] ?? null;
            $getYProductDetails = $details['get_y_products'] ?? [];
            $isRecursive = $details['is_recursive'] ?? false;

            // Check if TOTAL quantity across all matching items meets requirement
            if (!$buyQuantity || empty($getYProductDetails) || $totalQty < $buyQuantity) {
                continue;
            }
            
            $timesToApply = $isRecursive ? floor($totalQty / $buyQuantity) : 1;
            foreach ($getYProductDetails as $freebie) {
                $freeItemsToAdd[] = [
                    'product_id' => $freebie['product_id'],
                    'variation_id' => $freebie['variation_id'],
                    'quantity' => $timesToApply * $freebie['quantity'],
                    'discount' => $appliedDiscount,
                ];
            }
        }
        
    
        // Track free items allocated per variation to properly manage stock when multiple BOGO offers apply
        $freeItemQuantities = [];
        
        if (!empty($freeItemsToAdd)) {
            $freeProductIds = collect($freeItemsToAdd)->pluck('product_id')->unique()->toArray();
            $freeProducts = $this->getGuestProductsWithRelations($freeProductIds, $locationId, $brandId, true);
            
            foreach ($freeItemsToAdd as $item) {
                $product = $freeProducts->get($item['product_id']) ?? null;
                if (!$product) continue;

                $discount = $item['discount'];
                $cartDiscountApplicable = false;
                $requiredQty = $item['quantity'];
                $remainingQty = $requiredQty;
                
                // Check if discount specifies a specific variation or just a product
                $isSpecificVariation = !empty($item['variation_id']);
                
                // Prepare variations sorted by priority
                $variationsToCheck = collect();
                
                if ($isSpecificVariation) {
                    // Case 1: Specific variation specified - ONLY use that variation, no fallback
                    $specifiedVariation = $product->variations->firstWhere('id', $item['variation_id']);
                    if ($specifiedVariation) {
                        $variationsToCheck->push($specifiedVariation);
                    }
                } else {
                    // Case 2: Product-level discount - use multi-variation allocation
                    // Pick highest stock variation first, then others
                    $variationsToCheck = $product->variations->sortByDesc('qty');
                }
                
                // Distribute free items across available variations
                foreach ($variationsToCheck as $variation) {
                    if ($remainingQty <= 0) break;
                    
                    // If stock is not enabled for this product, give full quantity without checking stock
                    if ($product->enable_stock == 0) {
                        $qtyToGive = $remainingQty;
                        $remainingQty = 0;
                    } else {
                        // Calculate available stock considering:
                        // 1. Already purchased quantities from cart items
                        // 2. Already allocated free items from previous BOGO offers
                        $currentStock = $variation->qty ?? 0;
                        $purchasedQty = $purchasedVariationQuantities[$variation->id] ?? 0;
                        $allocatedFreeQty = $freeItemQuantities[$variation->id] ?? 0;
                        $availableStock = $currentStock - $purchasedQty - $allocatedFreeQty;
                        
                        if ($availableStock <= 0) continue;
                        
                        $qtyToGive = min($remainingQty, $availableStock);
                        $remainingQty -= $qtyToGive;
                    }
                    
                    // Track allocated free items for this variation
                    if (!isset($freeItemQuantities[$variation->id])) {
                        $freeItemQuantities[$variation->id] = 0;
                    }
                    $freeItemQuantities[$variation->id] += $qtyToGive;
                    
                    $variationImage = $this->getVariationImage($variation, $product);
                    
                    $cartData[] = [
                        'key'              => 'free_' . $item['product_id'] . '_' . $variation->id . '_' . ($discount->id ?? ''),
                        'product_id'       => $product->id,
                        'variation_id'     => $variation->id,
                        'ml'               => $product->ml,
                        'ct'               => $product->ct,
                        'locationTaxType'  => $product->locationTaxType,
                        'maxSaleLimit'     => $product->maxSaleLimit,
                        'product_name'     => $product->name,
                        'product_slug'     => $product->slug,
                        'product_price'    => $variation->ad_price ?? 0,
                        'discounted_price' => 0,
                        'discounts'        => [[
                            'name'            => $discount->couponName ?? null,
                            'code'            => $discount->couponCode ?? null,
                            'type'            => $discount->discountType ?? null,
                            'value'           => 'Free',
                            'original_price'  => $variation->ad_price ?? 0,
                            'discounted_price' => 0,
                            'discount_lable'  => $discount->discount_lable ?? null,
                        ]],
                        'product_image'    => $variationImage,
                        'variation_name'   => $variation->name,
                        'stock'            => $availableStock,
                        'sku'              => $variation->sub_sku,
                        'itemBarcode'      => $variation->var_barcode_no,
                        'purchaseLimit'    => $variation->var_maxSaleLimit,
                        'stock_status'     => $availableStock > 0 ? 'instock' : 'outofstock',
                        'qty'              => $qtyToGive,
                        'is_free_item'     => true,
                    ];
                    $count++;
                }
            }
        }
        
        $cartDiscountAmount = 0;
        $cartDiscountDetails = [];
        $isFreeShippingApplicable = true;
        if ($cartDiscountApplicable) {
            // Build collection including referral discounts from applied coupons
            $allDiscounts = collect($discounts);
            foreach ($appliedDiscounts as $couponCode) {
                $discount = $this->getDiscountFromCouponCode($couponCode, $discounts, $userId, $locationId, $brandId);
                if ($discount && !$allDiscounts->contains('id', $discount->id)) {
                    $allDiscounts->push($discount);
                }
            }
            
            // Find the highest priority applicable cart discount
            $selectedCartDiscount = null;
            foreach ($allDiscounts as $discount) {
                if ($discount->discountType === 'cartAdjustment' && $discountService->isDiscountApplicable($discount, $cartItems, $products, 0, $appliedDiscounts)) {
                    // Since discounts are already sorted by setPriority DESC, take the first match
                    $selectedCartDiscount = $discount;
                    break; // Only apply the highest priority discount
                }
            }
            
            // Apply only the selected highest priority discount
            if ($selectedCartDiscount) {
                $discountAmount = $discountService->calculateCartDiscount($cart_final_total, $selectedCartDiscount, $cartItems, $products);
                $isFreeShippingApplicable = false;
                if ($discountAmount > 0) {
                    $cartDiscountAmount = $discountAmount;
                    $discountDetail = [
                        'name' => $selectedCartDiscount->couponName,
                        'code' => $selectedCartDiscount->couponCode,
                        'type' => $selectedCartDiscount->discount,
                        'value' => $selectedCartDiscount->discountValue,
                        'discount_amount' => $discountAmount,
                    ];
                    if (!empty($selectedCartDiscount->discount_lable)) {
                        $discountDetail['discount_lable'] = $selectedCartDiscount->discount_lable;
                    }
                    $cartDiscountDetails[] = $discountDetail;
                }
            }
            $cart_final_total = max(0, $cart_final_total - $cartDiscountAmount);
        }
        $freeShippingDiscountAmount = -1;
        $freeShippingDiscountDetails = [];
        if (request()->has('shippingType') && request()->input('shippingType') == 'PICKUP') {
            $isFreeShippingApplicable = false;
        }
        if ($cartDiscountApplicable && $isFreeShippingApplicable) {
            // Build collection including referral discounts from applied coupons
            $allDiscounts = collect($discounts);
            foreach ($appliedDiscounts as $couponCode) {
                $discount = $this->getDiscountFromCouponCode($couponCode, $discounts, $userId, $locationId, $brandId);
                if ($discount && !$allDiscounts->contains('id', $discount->id)) {
                    $allDiscounts->push($discount);
                }
            }
            
            // Find the highest priority applicable free shipping discount
            $selectedFreeShippingDiscount = null;
            foreach ($allDiscounts as $discount) {
                if ($discount->discountType === 'freeShipping' && $discountService->isDiscountApplicable($discount, $cartItems, $products, 0, $appliedDiscounts)) {
                    // Since discounts are already sorted by setPriority DESC, take the first match
                    $selectedFreeShippingDiscount = $discount;
                    break; // Only apply the highest priority discount
                }
            }
            
            // Apply only the selected highest priority free shipping discount
            if ($selectedFreeShippingDiscount) {
                $freeShippingDiscountAmount = $discountService->freeShippingDiscount($cart_final_total, $selectedFreeShippingDiscount, $cartItems, $products);
                if ($freeShippingDiscountAmount > -1) {
                    $freeShippingDiscountDetails[] = [
                        'name' => $selectedFreeShippingDiscount->couponName,
                        'code' => $selectedFreeShippingDiscount->couponCode,
                        'type' => $selectedFreeShippingDiscount->discountType,
                        'value' => $selectedFreeShippingDiscount->discountValue,
                        'discount_amount' => $freeShippingDiscountAmount,
                    ];
                }
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'Cart Items',
            'data' => $cartData,
            'itemCount' => $count,
            'subtotal' => $cart_total_before_tax,
            'subtotal_inc_tax' => $cart_final_total,
            'total_tax_on_cart' => $total_tax_on_cart ?? 0,
            'cartDiscountDetails' => $cartDiscountDetails,
            'cartDiscountAmount' => $cartDiscountAmount,
            'freeShippingDiscountDetails' => $freeShippingDiscountDetails,
            'freeShippingDiscountAmount' => $freeShippingDiscountAmount,
            'applied_discounts' => $appliedDiscounts
        ]);
    }


    /**
     * Build complex cart response with discounts and tax calculations
     * @param array $cartItems
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @param object $contact
     * @param array $appliedDiscounts
     * @param \Illuminate\Database\Eloquent\Collection $discounts
     * @param object $discountService
     * @param array $taxCharges
     * @param string $userState
     * @return \Illuminate\Http\JsonResponse
     */
    private function buildComplexCartResponse($cartItems, $products, $contact, $appliedDiscounts, $discounts, $discountService, $taxCharges, $userState)
    {
        $userId = $contact->id ?? null;
        $locationId = $contact->location_id ?? null;
        $brandId = $contact->brand_id ?? null;
        $cartData = [];
        $count = 0;
        $cart_total_before_tax = 0;
        $cart_final_total = 0;
        $temp = 0;
        $total_tax_on_cart = 0;

        $cartDiscountApplicable = true;
        
        // Track purchased quantities per variation for BxGY stock adjustment
        $purchasedVariationQuantities = [];
        foreach ($cartItems as $cartItem) {
            // Track purchased quantities
            if (!isset($purchasedVariationQuantities[$cartItem->variation_id])) {
                $purchasedVariationQuantities[$cartItem->variation_id] = 0;
            }
            $purchasedVariationQuantities[$cartItem->variation_id] += $cartItem->qty;
        }
        
        foreach ($cartItems as $cartItem) {
            $temp = 0;
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                // Calculate unit price or price recall value 
                $unitPrice = $this->calculateUnitPrice($variation, $product, $contact->id);

                // Apply discount (product adjustment and bxgy) on cart items (Priority: 1)
                $appliedDiscount = null;
                $appliedDiscount = $this->calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, [$cartItem], $discountService, $userId, $locationId, $brandId);

                if ($appliedDiscount) {
                    if ($appliedDiscount->discountType === 'productAdjustment') {
                        $productAdjustmentExists = true;
                    } elseif ($appliedDiscount->discountType === 'buyXgetY') {
                        $isBxGyApplicable = true;
                        $cartDiscountApplicable = false;
                    }
                }

                $discountedPrice = $unitPrice;
                $itemDiscounts = [];

                // Apply the discount that was found (either productAdjustment or buyXgetY, but not both)
                if ($appliedDiscount) {
                    if ($appliedDiscount->discountType === 'productAdjustment') {
                        $discountedPrice = $discountService->calculateDiscountedPrice($unitPrice, $appliedDiscount);
                        $cartDiscountApplicable = false;

                        \Log::info('[CartDiscount] Applying product adjustment', [
                            'product_id' => $product->id,
                            'variation_id' => $variation->id,
                            'coupon_code' => $appliedDiscount->couponCode,
                            'discount_type' => $appliedDiscount->discount,
                            'discount_value' => $appliedDiscount->discountValue,
                            'original_price' => $unitPrice,
                            'discounted_price' => $discountedPrice
                        ]);

                        $itemDiscounts[] = [
                            'name' => $appliedDiscount->couponName,
                            'code' => $appliedDiscount->couponCode,
                            'type' => $appliedDiscount->discountType,
                            'value' => $appliedDiscount->discountValue,
                            'original_price' => $unitPrice,
                            'discounted_price' => $discountedPrice,
                            'discount_lable' => $appliedDiscount->discount_lable
                        ];
                    } elseif ($appliedDiscount->discountType === 'buyXgetY') {
                        // For buyXgetY, keep original price but mark as applicable for free items
                        $isBxGyApplicable = true;
                        $cartDiscountApplicable = false;
                    }
                }
                $cart_total_before_tax += $discountedPrice * $cartItem->qty;
                $temp = $discountedPrice * $cartItem->qty;
                // Apply tax calculations after discount applied
                $discountedPrice = $this->applyTaxCalculations($product, $variation, $discountedPrice, $taxCharges, $userState);

                // Calculate cart final total
                $cart_final_total += $discountedPrice * $cartItem->qty;

                // only tax amount 
                $total_tax_on_cart += ($discountedPrice * $cartItem->qty) - $temp;

                // Get variation image
                $variationImage = $this->getVariationImage($variation, $product);

                // Build cart data
                $cartData[] = $this->buildCartItemData($cartItem, $product, $variation, $discountedPrice, $itemDiscounts, $variationImage);

                $count++;
            }
        }

        // after cart items are processed, process free items from buy X get Y discounts
        $freeItemsToAdd = [];
        
        // Group matching items by discount to count total quantity
        $discountGroups = [];
        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if (!$product) continue;

            $variation = $product->variations->where('id', $cartItem->variation_id)->first();
            if (!$variation) continue;

            // Calculate high priority discount (product adjustment and bxgy)
            $appliedDiscount = $this->calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, [$cartItem], $discountService, null, $locationId, $brandId);

            if ($appliedDiscount && $appliedDiscount->discountType === 'buyXgetY') {
                // Safety check: Skip if coupon required but not applied
                if (!empty($appliedDiscount->couponCode) && !in_array($appliedDiscount->couponCode, $appliedDiscounts)) {
                    continue;
                }
                
                $discountId = $appliedDiscount->id;
                if (!isset($discountGroups[$discountId])) {
                    $discountGroups[$discountId] = [
                        'discount' => $appliedDiscount,
                        'total_qty' => 0,
                        'items' => []
                    ];
                }
                $discountGroups[$discountId]['total_qty'] += $cartItem->qty;
                $discountGroups[$discountId]['items'][] = $cartItem;
            }
        }

        // Process each discount group
        foreach ($discountGroups as $discountGroup) {
            $appliedDiscount = $discountGroup['discount'];
            $totalQty = $discountGroup['total_qty'];
            
            $cartDiscountApplicable = false;
            $details = json_decode($appliedDiscount->custom_meta, true);
            $buyQuantity = $details['buy_quantity'] ?? null;
            $getYProductDetails = $details['get_y_products'] ?? [];
            $isRecursive = $details['is_recursive'] ?? false;

            // Check if TOTAL quantity across all matching items meets requirement
            if (!$buyQuantity || empty($getYProductDetails) || $totalQty < $buyQuantity) {
                continue;
            }
            
            $timesToApply = $isRecursive ? floor($totalQty / $buyQuantity) : 1;
            foreach ($getYProductDetails as $freebie) {
                $freeItemsToAdd[] = [
                    'product_id' => $freebie['product_id'],
                    'variation_id' => $freebie['variation_id'],
                    'quantity' => $timesToApply * $freebie['quantity'],
                    'discount' => $appliedDiscount,
                ];
            }
        }
        // append free items to cart list (bxgy)
        if (!empty($freeItemsToAdd)) {
            $freeProductIds = collect($freeItemsToAdd)->pluck('product_id')->unique()->toArray();
            // Get free products with relation
            // For B2C customers without a group, use null instead of 0
            $freePriceGroupId = key($contact->price_tier);
            $freePriceGroupId = ($freePriceGroupId === 0) ? null : $freePriceGroupId;
            $freeProducts = $this->getProductsWithRelations($freeProductIds, $contact->id, $freePriceGroupId, true);

            foreach ($freeItemsToAdd as $item) {
                $product = $freeProducts->get($item['product_id']) ?? null;
                if (!$product) continue;

                $discount = $item['discount'];
                $cartDiscountApplicable = false;
                $requiredQty = $item['quantity'];
                $remainingQty = $requiredQty;
                
                // Check if discount specifies a specific variation or just a product
                $isSpecificVariation = !empty($item['variation_id']);
                
                // Prepare variations sorted by priority
                $variationsToCheck = collect();
                
                if ($isSpecificVariation) {
                    // Case 1: Specific variation specified - ONLY use that variation, no fallback
                    $specifiedVariation = $product->variations->firstWhere('id', $item['variation_id']);
                    if ($specifiedVariation) {
                        $variationsToCheck->push($specifiedVariation);
                    }
                } else {
                    // Case 2: Product-level discount - use multi-variation allocation
                    // Pick highest stock variation first, then others
                    $variationsToCheck = $product->variations->sortByDesc('qty');
                }
                
                // Distribute free items across available variations
                foreach ($variationsToCheck as $variation) {
                    if ($remainingQty <= 0) break;
                    
                    // If stock is not enabled for this product, give full quantity without checking stock
                    if ($product->enable_stock == 0) {
                        $qtyToGive = $remainingQty;
                        $remainingQty = 0;
                    } else {
                        // Calculate available stock considering already purchased quantities in this transaction
                        $currentStock = $variation->qty ?? 0;
                        $purchasedQty = $purchasedVariationQuantities[$variation->id] ?? 0;
                        $availableStock = $currentStock - $purchasedQty;
                        
                        if ($availableStock <= 0) continue;
                        
                        $qtyToGive = min($remainingQty, $availableStock);
                        $remainingQty -= $qtyToGive;
                    }
                    
                    $variationImage = $this->getVariationImage($variation, $product);
                    
                    $cartData[] = [
                        'key'              => 'free_' . $item['product_id'] . '_' . $variation->id . '_' . ($discount->id ?? ''),
                        'product_id'       => $product->id,
                        'variation_id'     => $variation->id,
                        'ml'               => $product->ml,
                        'ct'               => $product->ct,
                        'locationTaxType'  => $product->locationTaxType,
                        'maxSaleLimit'     => $product->maxSaleLimit,
                        'product_name'     => $product->name,
                        'product_slug'     => $product->slug,
                        'product_price'    => $variation->ad_price ?? 0,
                        'discounted_price' => 0,
                        'discounts'        => [[
                            'name'            => $discount->couponName ?? null,
                            'code'            => $discount->couponCode ?? null,
                            'type'            => $discount->discountType ?? null,
                            'value'           => 'Free',
                            'original_price'  => $variation->ad_price ?? 0,
                            'discounted_price' => 0,
                            'discount_lable'  => $discount->discount_lable ?? null,
                        ]],
                        'product_image'    => $variationImage,
                        'variation_name'   => $variation->name,
                        'stock'            => $availableStock,
                        'sku'              => $variation->sub_sku,
                        'itemBarcode'      => $variation->var_barcode_no,
                        'purchaseLimit'    => $variation->var_maxSaleLimit,
                        'stock_status'     => $availableStock > 0 ? 'instock' : 'outofstock',
                        'qty'              => $qtyToGive,
                        'is_free_item'     => true,
                    ];
                    $count++;
                }
            }
        }

        // Apply cart-level discounts (Priority: 2) - Only highest priority discount
        $cartDiscountAmount = 0;
        $cartDiscountDetails = [];
        $isFreeShippingApplicable = true;
        if ($cartDiscountApplicable) {
            // Build collection including referral discounts from applied coupons
            $allDiscounts = collect($discounts);
            foreach ($appliedDiscounts as $couponCode) {
                $discount = $this->getDiscountFromCouponCode($couponCode, $discounts, null, $locationId, $brandId);
                if ($discount && !$allDiscounts->contains('id', $discount->id)) {
                    $allDiscounts->push($discount);
                }
            }
            
            // Find the highest priority applicable cart discount
            $selectedCartDiscount = null;
            foreach ($allDiscounts as $discount) {
                // Note: passing the cart item in the product variable
                // Note: passing $products in the variation variable
                if (
                    $discount->discountType === 'cartAdjustment' &&
                    $discountService->isDiscountApplicable($discount, $cartItems, $products, 0, $appliedDiscounts)
                ) {
                    // Since discounts are already sorted by setPriority DESC, take the first match
                    $selectedCartDiscount = $discount;
                    break; // Only apply the highest priority discount
                }
            }
            
            // Apply only the selected highest priority discount
            if ($selectedCartDiscount) {
                $discountAmount = $discountService->calculateCartDiscount($cart_final_total, $selectedCartDiscount, $cartItems, $products);
                $isFreeShippingApplicable = false;

                if ($discountAmount > 0) {
                    $cartDiscountAmount = $discountAmount;
                    $cartDiscountDetails[] = [
                        'name' => $selectedCartDiscount->couponName,
                        'code' => $selectedCartDiscount->couponCode,
                        'type' => $selectedCartDiscount->discountType,
                        'value' => $selectedCartDiscount->discountValue,
                        'discount_amount' => $discountAmount,
                        'discount_lable' => $selectedCartDiscount->discount_lable
                    ];
                }
            }
            $cart_final_total = max(0, $cart_final_total - $cartDiscountAmount);
        }

        // Apply free shipping discount (Priority: 3) - Only highest priority discount
        $freeShippingDiscountAmount = -1; // case of no amount applied
        $freeShippingDiscountDetails = [];
        // if user will pickup then no need to apply free shipping discount
        if (request()->has('shippingType') && request()->input('shippingType') == 'PICKUP') {
            $isFreeShippingApplicable = false;
        }
        // apply free shipping discount if no coupon is applied then it will be applied
        if ($cartDiscountApplicable && $isFreeShippingApplicable) {
            // Build collection including referral discounts from applied coupons
            $allDiscounts = collect($discounts);
            foreach ($appliedDiscounts as $couponCode) {
                $discount = $this->getDiscountFromCouponCode($couponCode, $discounts, null, $locationId, $brandId);
                if ($discount && !$allDiscounts->contains('id', $discount->id)) {
                    $allDiscounts->push($discount);
                }
            }
            
            // Find the highest priority applicable free shipping discount
            $selectedFreeShippingDiscount = null;
            // Note: passing the cart item in the product variable
            // Note: passing $products in the variation variable
            foreach ($allDiscounts as $discount) {
                if ($discount->discountType === 'freeShipping' && $discountService->isDiscountApplicable($discount, $cartItems, $products, 0, $appliedDiscounts)) {
                    // Since discounts are already sorted by setPriority DESC, take the first match
                    $selectedFreeShippingDiscount = $discount;
                    break; // Only apply the highest priority discount
                }
            }
            
            // Apply only the selected highest priority free shipping discount
            if ($selectedFreeShippingDiscount) {
                $freeShippingDiscountAmount = $discountService->freeShippingDiscount($cart_final_total, $selectedFreeShippingDiscount, $cartItems, $products);
                if ($freeShippingDiscountAmount > -1) {
                    $freeShippingDiscountDetails[] = [
                        'name' => $selectedFreeShippingDiscount->couponName,
                        'code' => $selectedFreeShippingDiscount->couponCode,
                        'type' => $selectedFreeShippingDiscount->discountType,
                        'value' => $selectedFreeShippingDiscount->discountValue,
                        'discount_amount' => $freeShippingDiscountAmount,
                    ];
                }
            }
        }

        // return cart data
        return response()->json([
            'status' => true,
            'message' => 'Cart Items',
            'data' => $cartData,
            'itemCount' => $count,
            'subtotal' => $cart_total_before_tax,
            'subtotal_inc_tax' => $cart_final_total,
            'total_tax_on_cart' => $total_tax_on_cart ?? 0,
            // cart discounts
            'cartDiscountDetails' => $cartDiscountDetails,
            'cartDiscountAmount' => $cartDiscountAmount, // if 0 then no discount applied
            // free shipping discount
            'freeShippingDiscountDetails' => $freeShippingDiscountDetails,
            'freeShippingDiscountAmount' => $freeShippingDiscountAmount, // if -1 then no discount applied
            // applied discounts for bxgy and productAdjustment
            'applied_discounts' => $appliedDiscounts // customer filled 
        ]);
    }





    /**
     * Add item to cart for both customers and guests
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCart(Request $request)
    {
        $isGuestRequest = $request->attributes->get('is_guest_request', false);

        // Validation for bulk items (same for both customers and guests)
        $validate = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.variation_id' => 'nullable|integer|exists:variations,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        if ($validate->fails()) {
            $formattedErrors = [];
            foreach ($validate->errors()->toArray() as $key => $errorMessages) {
                $formattedErrors[] = ['field' => $key, 'messages' => $errorMessages];
            }
            return response()->json(['status' => false, 'message' => $formattedErrors]);
        }

        try {
            if ($isGuestRequest) {
                return $this->addToGuestCart($request);
            } else {
                return $this->addToCustomerCart($request);
            }
        } catch (\Exception $e) {
            Log::error('Add to cart error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error adding item to cart.'
            ], 500);
        }
    }

    /**
     * Add or update multiple items to customer cart (bulk)
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function addToCustomerCart(Request $request)
    {
        $contact = Auth::guard('api')->user();
        $userId = $contact->id;
        $currentTime = now();
        $ms = [];
        $cart_item_id = null;

        // Validation
        $validate = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.variation_id' => 'nullable|integer|exists:variations,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        if ($validate->fails()) {
            $formattedErrors = [];
            foreach ($validate->errors()->toArray() as $key => $errorMessages) {
                $formattedErrors[] = ['field' => $key, 'messages' => $errorMessages];
            }
            return response()->json(['status' => false, 'message' => $formattedErrors]);
        }

        $items = $request->input('items');
        $checkout = Cart::where('user_id', $userId)->first();
        $isFreeze = $checkout ? $checkout->isFreeze : false;

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $variation = DB::table('variations')
                    ->join('variation_location_details', 'variations.id', '=', 'variation_location_details.variation_id')
                    ->where('variations.id', $item['variation_id'])
                    ->select([
                        'variations.id',
                        'variations.name',
                        'variations.var_maxSaleLimit',
                        'variations.product_id',
                        'variation_location_details.in_stock_qty as qty'
                    ])
                    ->lockForUpdate()
                    ->first();

                if (!$variation) {
                    $ms[] = "Unknown Item";
                    continue;
                }

                $product = Product::where('id', $item['product_id'])
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->first();

                if (!$product) {
                    $ms[] = "Product not found";
                    continue;
                }

                $variationName = $variation->name === 'DUMMY' ? ' ' : $variation->name;
                $maxSaleLimit = $variation->var_maxSaleLimit ?? $product->maxSaleLimit ?? false;
                $cartItem = CartItem::where([
                    'user_id' => $userId,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                ])
                    ->lockForUpdate()
                    ->first();

                $existingQty = $cartItem ? $cartItem->qty : 0;
                $newQty = $existingQty + $item['qty'];

                // session limit check (customer are limited for few)
                if ($maxSaleLimit) {
                    $can_add = $this->allowedItemQty($item['product_id'], $item['variation_id'], $userId, $item['qty'], $currentTime, $maxSaleLimit);
                    if (!$can_add['status']) {
                        $ms[] = "{$product->name} {$variationName} has order limit reached.";
                        continue;
                    } else if (!$can_add['can_add'] && $can_add['status']) {
                        $newQty = $can_add['allowed_qty'];
                        // Add specific message about limit reached
                        if ($can_add['allowed_qty'] == 0) {
                            $ms[] = "Order limit reached for {$product->name} {$variationName}. You cannot add more of this item.";
                        } else {
                            $ms[] = "Order limit reached for {$product->name} {$variationName}. Quantity adjusted to {$can_add['allowed_qty']}.";
                        }
                    }
                }

                if ($maxSaleLimit && $newQty > $maxSaleLimit) {
                    $newQty = $maxSaleLimit;
                }
                if ($variation->qty + $existingQty < $newQty && $isFreeze) {
                    $ms[] = "{$product->name} {$variationName} has insufficient stock.";
                    continue;
                }

                if ($newQty > $variation->qty && !$isFreeze) {
                    $newQty = $variation->qty;
                    $ms[] = "{$product->name} {$variationName} quantity adjusted to available stock ({$variation->qty}).";
                }
                $qtyDiff = $newQty - $existingQty;
                $cart_item_id = null;
                if ($cartItem) {
                    $cartItem->qty = $newQty;
                    $cartItem->save();
                    $ms[] = "{$product->name} {$variationName} quantity updated to {$cartItem->qty}.";
                    $cart_item_id = $cartItem->id;
                } else {
                    $cartItemData = CartItem::create([
                        'user_id' => $userId,
                        'product_id' => $item['product_id'],
                        'variation_id' => $item['variation_id'] ?? null,
                        'qty' => $newQty,
                        'item_type' => 'line_item',
                        'discount_id' => null,
                        'lable' => 'Item',
                    ]);
                    $ms[] = "{$product->name} {$variationName} added with {$item['qty']} items.";
                    $cart_item_id = $cartItemData->id;
                }

                if ($isFreeze && $qtyDiff > 0) {
                    try {
                        $updated = DB::table('variation_location_details')
                            ->where('variation_id', $variation->id)
                            ->decrement('in_stock_qty', $qtyDiff);
                    } catch (\Throwable $th) {
                        $updated = DB::table('variation_location_details')
                            ->where('variation_id', $variation->id)
                            ->update(['in_stock_qty' => 0]);
                    }

                    if (!$updated) {
                        $ms[] = "Insufficient stock for item {$product->name} {$variationName}.";
                        continue;
                    }
                }
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Cart updated successfully.',
                'ms' => $ms,
                'item_id' => $cart_item_id ?? null
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Cart update failed.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Add or update multiple items to guest cart (bulk)
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function addToGuestCart(Request $request)
    {
        $guestSession = $request->attributes->get('current_guest_session');
        $location = $request->attributes->get('current_location');
        $brand = $request->attributes->get('current_brand');
        $currentTime = now();
        $ms = [];
        $cart_item_id = null;

        // Validation - same as customer cart for bulk items
        $validate = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.variation_id' => 'nullable|integer|exists:variations,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        if ($validate->fails()) {
            $formattedErrors = [];
            foreach ($validate->errors()->toArray() as $key => $errorMessages) {
                $formattedErrors[] = ['field' => $key, 'messages' => $errorMessages];
            }
            return response()->json(['status' => false, 'message' => $formattedErrors]);
        }

        $items = $request->input('items');

        // Check if guest cart is frozen (similar to customer cart)
        $isFreeze = $guestSession->isFreeze ?? false;

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                // First try to get variation with location filter
                $variation = DB::table('variations')
                    ->join('variation_location_details', 'variations.id', '=', 'variation_location_details.variation_id')
                    ->where('variations.id', $item['variation_id'])
                    ->where('variation_location_details.location_id', $location->id)
                    ->select([
                        'variations.id',
                        'variations.name',
                        'variations.var_maxSaleLimit',
                        'variations.product_id',
                        'variation_location_details.in_stock_qty as qty'
                    ])
                    ->lockForUpdate()
                    ->first();

                // If not found with location filter, try without location filter (fallback)
                if (!$variation) {
                    $variation = DB::table('variations')
                        ->join('variation_location_details', 'variations.id', '=', 'variation_location_details.variation_id')
                        ->where('variations.id', $item['variation_id'])
                        ->select([
                            'variations.id',
                            'variations.name',
                            'variations.var_maxSaleLimit',
                            'variations.product_id',
                            'variation_location_details.in_stock_qty as qty'
                        ])
                        ->lockForUpdate()
                        ->first();
                }

                if (!$variation) {
                    $ms[] = "Unknown Item";
                    continue;
                }

                $product = Product::where('id', $item['product_id'])
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->first();

                if (!$product) {
                    $ms[] = "Product not found";
                    continue;
                }

                $variationName = $variation->name === 'DUMMY' ? ' ' : $variation->name;
                $maxSaleLimit = $variation->var_maxSaleLimit ?? $product->maxSaleLimit ?? false;

                $cartItem = GuestCartItem::where([
                    'guest_session_id' => $guestSession->id,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                ])
                    ->lockForUpdate()
                    ->first();

                $existingQty = $cartItem ? $cartItem->qty : 0;
                $newQty = $existingQty + $item['qty'];

                // For guests, we'll use a simplified limit check (no complex allowedItemQty logic)
                if ($maxSaleLimit && $newQty > $maxSaleLimit) {
                    $newQty = $maxSaleLimit;
                    $ms[] = "{$product->name} {$variationName} quantity adjusted to maximum allowed limit ({$maxSaleLimit}).";
                }

                // Stock check with freeze functionality - only check if enable_stock is true
                if ($product->enable_stock == 1) {
                    if ($variation->qty + $existingQty < $newQty && $isFreeze) {
                        $ms[] = "{$product->name} {$variationName} has insufficient stock.";
                        continue;
                    }

                    if ($newQty > $variation->qty && !$isFreeze) {
                        $newQty = $variation->qty;
                        $ms[] = "{$product->name} {$variationName} quantity adjusted to available stock ({$variation->qty}).";
                    }
                }

                $qtyDiff = $newQty - $existingQty;
                $cart_item_id = null;

                if ($cartItem) {
                    $cartItem->qty = $newQty;
                    $cartItem->save();
                    $ms[] = "{$product->name} {$variationName} quantity updated to {$cartItem->qty}.";
                    $cart_item_id = $cartItem->id;
                } else {
                    $cartItemData = GuestCartItem::create([
                        'guest_session_id' => $guestSession->id,
                        'product_id' => $item['product_id'],
                        'variation_id' => $item['variation_id'] ?? null,
                        'qty' => $newQty,
                        'item_type' => 'line_item',
                        'discount_id' => null,
                        'lable' => 'Item',
                    ]);
                    $ms[] = "{$product->name} {$variationName} added with {$item['qty']} items.";
                    $cart_item_id = $cartItemData->id;
                }

                // Handle stock freezing/reservation for guests (similar to customer cart)
                // Only manage stock if enable_stock is true
                if ($isFreeze && $qtyDiff > 0 && $product->enable_stock == 1) {
                    try {
                        $updated = DB::table('variation_location_details')
                            ->where('variation_id', $variation->id)
                            ->where('location_id', $location->id)
                            ->decrement('in_stock_qty', $qtyDiff);
                    } catch (\Throwable $th) {
                        $updated = DB::table('variation_location_details')
                            ->where('variation_id', $variation->id)
                            ->where('location_id', $location->id)
                            ->update(['in_stock_qty' => 0]);
                    }

                    if (!$updated) {
                        $ms[] = "Insufficient stock for item {$product->name} {$variationName}.";
                        continue;
                    }
                }
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Cart updated successfully.',
                'ms' => $ms,
                'item_id' => $cart_item_id ?? null
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Cart update failed.', 'error' => $e->getMessage()], 500);
        }
    }







    /**
     * When customer want to decrease use this api only 
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reduceQty(Request $request)
    {
        $isGuestRequest = $request->attributes->get('is_guest_request', false)
            || $request->attributes->get('current_guest_session') !== null;

        $validate = Validator::make($request->all(), [
            'item_id' => 'required|integer|exists:' . ($isGuestRequest ? 'guest_cart_items' : 'cart_items') . ',id',
            'qty' => 'required|integer|min:1',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->toArray();
            $formattedErrors = [];
            foreach ($errors as $key => $errorMessages) {
                $formattedErrors[] = [
                    'field' => $key,
                    'messages' => $errorMessages,
                ];
            }
            return response()->json(['status' => false, 'message' => $formattedErrors]);
        }

        DB::beginTransaction();
        try {
            if ($isGuestRequest) {
                return $this->reduceGuestQty($request);
            } else {
                return $this->reduceCustomerQty($request);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Failed to reduce quantity.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Reduce quantity for customer cart
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function reduceCustomerQty(Request $request)
    {
        $contact = Auth::guard('api')->user();
        $userId = $contact->id;
        $itemId = $request->input('item_id');
        $qtyToReduce = $request->input('qty');

        $checkout = Cart::where('user_id', $userId)->first();
        $isFreeze = $checkout ? $checkout->isFreeze : false;

        // Lock the cart item to prevent race condition
        $cartItem = CartItem::where('id', $itemId)
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->first();

        if (!$cartItem) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Cart item not found.']);
        }

        if ($cartItem->qty <= 1) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Quantity cannot be less than 1.']);
        }

        $newQty = $cartItem->qty - $qtyToReduce;
        if ($newQty < 1) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Quantity cannot be less than 1.']);
        }

        $cartItem->qty = $newQty;
        $cartItem->save();

        // Restore stock if cart is frozen - only if enable_stock is true
        if ($isFreeze && $cartItem->variation_id) {
            $variation = Variation::with('product')->find($cartItem->variation_id);
            if ($variation && $variation->product && $variation->product->enable_stock == 1) {
                $updated = DB::table('variation_location_details')
                    ->where('variation_id', $cartItem->variation_id)
                    ->increment('in_stock_qty', $qtyToReduce);
            }
        }

        DB::commit();
        return response()->json(['status' => true, 'message' => 'Quantity successfully reduced.', 'new_qty' => $cartItem->qty]);
    }

    /**
     * Reduce quantity for guest cart
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function reduceGuestQty(Request $request)
    {
        $guestSession = $request->attributes->get('current_guest_session');
        $itemId = $request->input('item_id');
        $qtyToReduce = $request->input('qty');

        // Lock the cart item to prevent race condition
        $cartItem = GuestCartItem::where('id', $itemId)
            ->where('guest_session_id', $guestSession->id)
            ->lockForUpdate()
            ->first();

        if (!$cartItem) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Cart item not found.']);
        }

        $newQty = $cartItem->qty - $qtyToReduce;

        // If new qty would be 0 or less, remove the item from cart instead of error
        if ($newQty < 1) {
            $qtyToRestore = $cartItem->qty;
            $isFreeze = $guestSession->isFreeze ?? false;
            if ($isFreeze && $cartItem->variation_id) {
                $variation = Variation::with('product')->find($cartItem->variation_id);
                if ($variation && $variation->product && $variation->product->enable_stock == 1) {
                    DB::table('variation_location_details')
                        ->where('variation_id', $cartItem->variation_id)
                        ->increment('in_stock_qty', $qtyToRestore);
                }
            }
            $cartItem->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Item removed from cart.', 'new_qty' => 0, 'removed' => true]);
        }

        $cartItem->qty = $newQty;
        $cartItem->save();

        // Restore stock if cart is frozen - only if enable_stock is true
        $isFreeze = $guestSession->isFreeze ?? false;
        if ($isFreeze && $cartItem->variation_id) {
            $variation = Variation::with('product')->find($cartItem->variation_id);
            if ($variation && $variation->product && $variation->product->enable_stock == 1) {
                $updated = DB::table('variation_location_details')
                    ->where('variation_id', $cartItem->variation_id)
                    ->increment('in_stock_qty', $qtyToReduce);
            }
        }

        DB::commit();
        return response()->json(['status' => true, 'message' => 'Quantity successfully reduced.', 'new_qty' => $cartItem->qty]);
    }










    /**
     * Remove item from cart for both customers and guests
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromCart(Request $request, $itemId)
    {
        $isGuestRequest = $request->attributes->get('is_guest_request', false)
            || $request->attributes->get('current_guest_session') !== null;

        $itemId = $request->route('itemId');
        if (!$itemId) {
            return response()->json(['status' => false, 'message' => 'Item ID is required.']);
        }

        DB::beginTransaction();
        try {
            if ($isGuestRequest) {
                return $this->removeFromGuestCart($request, $itemId);
            } else {
                return $this->removeFromCustomerCart($request, $itemId);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Remove from cart error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove item from cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from customer cart
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @param int $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    private function removeFromCustomerCart(Request $request, $itemId)
    {
        $contact = Auth::guard('api')->user();
        $userId = $contact->id;
        $checkout = Cart::where('user_id', $userId)->first();
        $isFreeze = $checkout ? $checkout->isFreeze : false;

        // Lock the cart item to prevent race condition
        $cartItem = CartItem::where('id', $itemId)
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->first();

        if (!$cartItem) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Cart item not found.']);
        }

        // If cart is frozen and item has variation, restore stock - only if enable_stock is true
        if ($isFreeze && $cartItem->variation_id) {
            $variation = Variation::find($cartItem->variation_id);
            if ($variation && $variation->product && $variation->product->enable_stock == 1) {
                $updated = DB::table('variation_location_details')
                    ->where('variation_id', $cartItem->variation_id)
                    ->increment('in_stock_qty', $cartItem->qty);
            }
        }

        $cartItem->delete();
        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Item removed from cart successfully.'
        ]);
    }

    /**
     * Remove item from guest cart
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @param int $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    private function removeFromGuestCart(Request $request, $itemId)
    {
        $guestSession = $request->attributes->get('current_guest_session');
        $isFreeze = $guestSession->isFreeze ?? false;

        // Lock the cart item to prevent race condition
        $cartItem = GuestCartItem::where('id', $itemId)
            ->where('guest_session_id', $guestSession->id)
            ->lockForUpdate()
            ->first();

        if (!$cartItem) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Cart item not found.']);
        }

        // If cart is frozen and item has variation, restore stock - only if enable_stock is true
        if ($isFreeze && $cartItem->variation_id) {
            $variation = Variation::with('product')->find($cartItem->variation_id);
            if ($variation && $variation->product && $variation->product->enable_stock == 1) {
                $updated = DB::table('variation_location_details')
                    ->where('variation_id', $cartItem->variation_id)
                    ->increment('in_stock_qty', $cartItem->qty);
            }
        }

        $cartItem->delete();
        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Item removed from cart successfully.'
        ]);
    }








    /**
     * Clear entire cart for both customers and guests
     */
    public function clearCart(Request $request)
    {
        $isGuestRequest = $request->attributes->get('is_guest_request', false)
            || $request->attributes->get('current_guest_session') !== null;

        try {
            if ($isGuestRequest) {
                return $this->clearGuestCart($request);
            } else {
                return $this->clearCustomerCart($request);
            }
        } catch (\Exception $e) {
            Log::error('Clear cart error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error clearing cart.'
            ], 500);
        }
    }

    /**
     * Clear customer cart
     */
    private function clearCustomerCart(Request $request)
    {
        $contact = Auth::guard('api')->user();
        $userId = $contact->id;

        $checkout = Cart::where('user_id', $userId)->first();
        $isFreeze = $checkout ? $checkout->isFreeze : false;

        DB::beginTransaction();
        try {
            $cartItems = CartItem::where('user_id', $userId)
                ->lockForUpdate()
                ->get();

            foreach ($cartItems as $item) {
                // Only manage stock if enable_stock is true
                if ($isFreeze && $item->variation_id) {
                    $variation = Variation::find($item->variation_id);
                    if ($variation && $variation->product && $variation->product->enable_stock == 1) {
                        DB::table('variation_location_details')
                            ->where('variation_id', $item->variation_id)
                            ->lockForUpdate()
                            ->increment('in_stock_qty', $item->qty);
                    }
                }
            }

            CartItem::where('user_id', $userId)->delete();
            Cart::where('user_id', $userId)->delete();

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Cart cleared successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to clear the cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear guest cart
     */
    private function clearGuestCart(Request $request)
    {
        $guestSession = $request->attributes->get('current_guest_session');

        // Check if guest cart is frozen (if you have such a flag for guest carts)
        $isFreeze = false;
        if (property_exists($guestSession, 'isFreeze')) {
            $isFreeze = $guestSession->isFreeze;
        } elseif (isset($guestSession->isFreeze)) {
            $isFreeze = $guestSession->isFreeze;
        }

        DB::beginTransaction();
        try {
            $cartItems = GuestCartItem::where('guest_session_id', $guestSession->id)
                ->lockForUpdate()
                ->get();

            foreach ($cartItems as $item) {
                // Only manage stock if enable_stock is true
                if ($isFreeze && $item->variation_id) {
                    $variation = Variation::find($item->variation_id);
                    if ($variation && $variation->product && $variation->product->enable_stock == 1) {
                        DB::table('variation_location_details')
                            ->where('variation_id', $item->variation_id)
                            ->lockForUpdate()
                            ->increment('in_stock_qty', $item->qty);
                    }
                }
            }

            GuestCartItem::where('guest_session_id', $guestSession->id)->delete();

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Cart cleared successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to clear the cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    /**
     * Apply discount to the cart (customer filled) 
     * this function just add discount , cart api will handle rest validation
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @input applied_discounts: "10OFF"
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */         

     
    public function applyDiscount(Request $request)
    {
        request()->attributes->set('customer_referal_coupons', []);
        $guestSession = $request->attributes->get('current_guest_session');
        $locationId = $request->route('location_id');
        $brandName  = $request->route('brand_name');
        $brandId = $request->get('current_brand')->id;
        
        // Validate input
        $validate = Validator::make($request->all(), [
            'applied_discounts' => 'required|string|max:255'
        ]);
        
        if ($validate->fails()) {
            $firstErrorMessage = $validate->errors()->first();
            return response()->json([
                'status' => false,
                'message' => $firstErrorMessage
            ], 422);
        }

        // Handle guest users
        if ($guestSession) {
            return $this->applyDiscountForGuest($request);
        }

        // Handle authenticated users
        return $this->applyDiscountForCustomer($request);
    }

    /**
     * Apply discount for authenticated customers
     */
    private function applyDiscountForCustomer(Request $request)
    {
        $locationId = $request->route('location_id');
        $brandName  = $request->route('brand_name');
        $brandId = $request->get('current_brand')->id;
        $contact = Auth::guard('api')->user();
        if (!$contact) {
            return response()->json(['status' => false, 'message' => 'Unauthorized user.']);
        }

        $this->setReferralCouponsForRequest($contact);

        $userId = $contact->id;
        $coupon_code = $request->input('applied_discounts');

        // Get or create cart
        $cart = $this->getOrCreateCart($contact);

        // Get cart items
        $cartItemGet = $this->getCartItems($userId);
        if ($cartItemGet['status'] == false) {
            return response()->json(['status' => false, 'message' => $cartItemGet['message']]);
        }
        $cartItems = $cartItemGet['data'];
        
        if ($cartItems->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Your cart is empty.']);
        }

        $productIds = $cartItems->pluck('product_id');
        // B2C customers: get price group if exists, otherwise use null
        $priceTier = $contact->price_tier;
        $priceGroupId = key($priceTier);
        // For B2C customers without a group, priceGroupId is 0, set it to null
        $priceGroupId = ($priceGroupId === 0) ? null : $priceGroupId;
        
        // Get products with relations
        $products = $this->getProductsWithRelations($productIds, $userId, $priceGroupId);

        // Calculate cart total (basic calculation without discounts)
        $cartTotal = 0;
        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                if ($variation) {
                    $unitPrice = $this->calculateUnitPrice($variation, $product, $userId);
                    $cartTotal += $unitPrice * $cartItem->qty;
                }
            }
        }

        // Validate coupon using the discount service
        $discountService = new CustomDiscountRuleService();
        $validation = $discountService->validateCoupon(
            $coupon_code,
            $cartItems,
            $products,
            $cartTotal,
            $userId,
            $locationId,
            $brandId
        );

        if (!$validation['status']) {
            return response()->json([
                'status' => false,
                'message' => $validation['message']
            ]);
        }

        // Get applied discounts from request
        $appliedDiscounts = [];
        if ($request->input('applied_discounts')) {
            $appliedDiscounts[] = $request->input('applied_discounts');
        }

        if (empty($appliedDiscounts)) {
            return response()->json(['status' => false, 'message' => 'No discount applied.']);
        }

        // Update cart with applied discounts
        $cart->update(['applied_discounts' => $appliedDiscounts]);

        return response()->json(['status' => true, 'message' => 'Coupon applied successfully.']);
    }

    /**
     * Apply discount for guest users
     */
    private function applyDiscountForGuest(Request $request)
    {
        $guestSession = $request->attributes->get('current_guest_session');
        
        $location = $request->attributes->get('current_location');
        $brand = $request->attributes->get('current_brand');
        
        if (!$guestSession) {
            return response()->json(['status' => false, 'message' => 'Guest session ID required.']);
        }

        $coupon_code = $request->input('applied_discounts');

        // Get guest cart items
        $cartItems = GuestCartItem::where('guest_session_id', $guestSession->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Your cart is empty.']);
        }

        $productIds = $cartItems->pluck('product_id');
        
        // Get products with relations (use a dummy price group for guest)
        $products = $this->getProductsWithRelations($productIds, null, null);

        // Calculate cart total (basic calculation without discounts)
        $cartTotal = 0;
        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                if ($variation) {
                    $unitPrice = $variation->ad_price ?? $variation->default_sell_price ?? 0;
                    $cartTotal += $unitPrice * $cartItem->qty;
                }
            }
        }

        // Validate coupon using the discount service
        $discountService = new CustomDiscountRuleService();
        $locationId = $location ? $location->id : null;
        $brandId = $brand ? $brand->id : null;
        
        $validation = $discountService->validateCoupon(
            $coupon_code,
            $cartItems,
            $products,
            $cartTotal,
            null,
            $locationId,
            $brandId
        );

        if (!$validation['status']) {
            return response()->json([
                'status' => false,
                'message' => $validation['message']
            ]);
        }

        // Get applied discounts from request
        $appliedDiscounts = [];
        if ($request->input('applied_discounts')) {
            $appliedDiscounts[] = $request->input('applied_discounts');
        }

        if (empty($appliedDiscounts)) {
            return response()->json(['status' => false, 'message' => 'No discount applied.']);
        }

        // Update guest session with applied discounts
        $guestSession->update(['applied_discounts' => $appliedDiscounts]);

        return response()->json(['status' => true, 'message' => 'Discount applied successfully.']);
    }

    /**
     * Remove discount from the cart
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @input applied_discounts: "10OFF" (optional - if not provided, removes all discounts)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeDiscount(Request $request)
    {
        $guestSession = $request->attributes->get('current_guest_session');
        
        // Handle guest users
        if ($guestSession) {
            return $this->removeDiscountForGuest($request);
        }

        // Handle authenticated users
        return $this->removeDiscountForCustomer($request);
    }

    /**
     * Remove discount for authenticated customers
     */
    private function removeDiscountForCustomer(Request $request)
    {
        $contact = Auth::guard('api')->user();
        if (!$contact) {
            return response()->json(['status' => false, 'message' => 'Unauthorized user.']);
        }

        $userId = $contact->id;
        $discountCode = $request->input('applied_discounts');

        // Get cart
        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart) {
            return response()->json(['status' => false, 'message' => 'Cart not found.']);
        }

        $appliedDiscounts = $cart->applied_discounts ?? [];

        if (empty($appliedDiscounts)) {
            return response()->json(['status' => false, 'message' => 'No discounts applied to cart.']);
        }

        // If applied_discounts is provided, remove only that discount
        if ($discountCode) {
            $key = array_search($discountCode, $appliedDiscounts);
            if ($key !== false) {
                unset($appliedDiscounts[$key]);
                $appliedDiscounts = array_values($appliedDiscounts); // Re-index array
                $cart->update(['applied_discounts' => $appliedDiscounts]);
                return response()->json(['status' => true, 'message' => 'Discount removed successfully.']);
            } else {
                return response()->json(['status' => false, 'message' => 'Discount code not found in cart.']);
            }
        }

        // If no applied_discounts provided, remove all discounts
        $cart->update(['applied_discounts' => []]);
        return response()->json(['status' => true, 'message' => 'All discounts removed successfully.']);
    }

    /**
     * Remove discount for guest users
     */
    private function removeDiscountForGuest(Request $request)
    {
        $guestSession = $request->attributes->get('current_guest_session');
        
        if (!$guestSession) {
            return response()->json(['status' => false, 'message' => 'Guest session not found.']);
        }

        $discountCode = $request->input('applied_discounts');
        $appliedDiscounts = $guestSession->applied_discounts ?? [];

        if (empty($appliedDiscounts)) {
            return response()->json(['status' => false, 'message' => 'No discounts applied to cart.']);
        }

        // If applied_discounts is provided, remove only that discount
        if ($discountCode) {
            $key = array_search($discountCode, $appliedDiscounts);
            if ($key !== false) {
                unset($appliedDiscounts[$key]);
                $appliedDiscounts = array_values($appliedDiscounts); // Re-index array
                $guestSession->update(['applied_discounts' => $appliedDiscounts]);
                return response()->json(['status' => true, 'message' => 'Discount removed successfully.']);
            } else {
                return response()->json(['status' => false, 'message' => 'Discount code not found in cart.']);
            }
        }

        // If no applied_discounts provided, remove all discounts
        $guestSession->update(['applied_discounts' => []]);
        return response()->json(['status' => true, 'message' => 'All discounts removed successfully.']);
    }

    private function setReferralCouponsForRequest($contact = null): void
    {
        if (!$contact) {
            request()->attributes->set('customer_referal_coupons', []);
            return;
        }

        $coupons = EcomReferalProgram::where('customer_id', $contact->id)
            ->where('is_used', false)
            ->pluck('coupon_code')
            ->toArray();

        request()->attributes->set('customer_referal_coupons', $coupons);
    }

    /**
     * Get active discounts for authenticated customers
     */
    // public function activeDiscount(Request $request)
    // {
    //     $isGuestRequest = $request->attributes->get('is_guest_request', false);
    // }











    /**
     * Reserve cart item (when customer select address) x item stock will be reserved for 5 minutes to that customer 
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param int $userId
     * @param object $check
     * @param boolean $isGuest
     * @return array
     */
    private function reserveCartItem($userId, $check, $isGuestRequest)
    {
        if($isGuestRequest){
            $cartItems = GuestCartItem::where('guest_session_id',$userId)->get();
        } else {
            $cartItems = CartItem::where('user_id', $userId)->get();
        }

        $ms = [];
        foreach ($cartItems as $cartItem) {
            $product = Product::with(['variations' => function ($query) {
                $query->select([
                    'variations.id', // Specify table for the `id` column
                    'variations.var_maxSaleLimit',
                    'variations.product_id',
                    'variation_location_details.in_stock_qty as qty',
                ])
                    ->leftJoin('variation_location_details', function ($join) {
                        $join->on('variations.id', '=', 'variation_location_details.variation_id');
                    });
            }])
                ->where('products.id', $cartItem['product_id']) // Specify the table for `id`
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->first();
            if (!$product) {
                $ms[] = "Product not found or unavailable";
                $cartItem->delete(); // Remove invalid cart item
                continue; // Skip this iteration
            }
            $variation = $product->variations->where('id', $cartItem['variation_id'])->first();
            if (!$variation) {
                $ms[] = "Unknown Item";
                $cartItem->delete();
                continue; // not add to cart
            }

            $variationName = $variation->name == 'DUMMY' ? ' ' : $variation->name;
            $maxSaleLimit = $variation->var_maxSaleLimit ?? $product->maxSaleLimit ?? false;
            if ($maxSaleLimit && $cartItem['qty'] > $maxSaleLimit) {
                $ms[] = "{$variationName} Cannot purchase more than {$maxSaleLimit} units.";
                $cartItem['qty'] = $maxSaleLimit;
                $cartItem->save();
            }
            // Only check stock if enable_stock == 1
            if ($product->enable_stock == 1 && $variation->qty < $cartItem['qty']) {
                if ($variation->qty <= 0) {
                    $ms[] = "{$product->name} {$variationName} have insufficient stock";
                    $cartItem->delete();
                    continue; // not add to cart
                } else {
                    $cartItem['qty'] = $variation->qty;
                    $ms[] = "{$product->name} {$variationName} added with {$variation->qty}";
                    $cartItem->qty = $variation->qty;
                    $cartItem->save();
                }
            }

            // Only manage stock if enable_stock is true
            if ($product->enable_stock == 1) {
                try {
                    DB::table('variation_location_details')
                        ->where('variation_id', $variation->id)
                        ->decrement('in_stock_qty', $cartItem['qty']);
                } catch (\Exception $e) {
                    DB::table('variation_location_details')
                        ->where('variation_id', $variation->id)
                        ->update(['in_stock_qty' => 0]);
                }
            }
        }
        $check->update([
            'isFreeze' => 1,
        ]);
        $response = [
            'status' => true,
            'message' => 'Cart items frozen',
        ];
        UnfreezeCart::dispatch($userId)->delay(now()->addMinutes(5));
        if (!empty($ms)) {
            $response['adjusted_items'] = $ms;
            $response['must_read_info'] = 'Few items got adjusted during checkout, so your cart has been updated if you want you can check your cart again';
        }
        return $response;
    }
    public function address(Request $request)
    {

        $isGuestRequest = $request->attributes->get('is_guest_request', false);

        $validate = Validator::make($request->all(), [
            // 'user_id' => 'required|integer',
            'billing_first_name' => 'required|string|max:255',
            'billing_last_name' => 'nullable|string|max:255',
            'billing_company' => 'nullable|string|max:255',
            'billing_address1' => 'required|string|max:255',
            'billing_address2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'required|string|max:255',
            'billing_zip' => 'required|string|max:255',
            'billing_country' => 'nullable|string|max:255',
            'billing_phone' => 'required|string|min:10|max:10',
            'billing_email' => 'required|email|max:255',
            'shipping_first_name' => 'required|string|max:255',
            'shipping_last_name' => 'nullable|string|max:255',
            'shipping_company' => 'nullable|string|max:255',
            'shipping_address1' => 'required|string|max:255',
            'shipping_address2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_state' => 'required|string|max:255',
            'shipping_zip' => 'required|string|max:255',
            'shipping_country' => 'nullable|string|max:255'
        ]);
        if ($validate->fails()) {
            $errors = $validate->errors()->toArray();
            $formattedErrors = [];
            foreach ($errors as $key => $errorMessages) {
                $formattedErrors[] = [
                    'field' => $key,
                    'messages' => $errorMessages
                ];
            }
            return response()->json([
                'status' => false,
                'message' => $formattedErrors
            ]);
        }
        $brand = $request->attributes->get('current_brand');
        if ($isGuestRequest) {
            $guestSession = $request->attributes->get('current_guest_session');
            $contact = Contact::where('type', 'customer')
                    ->where(function($q) {
                        $q->where('is_guest', false)
                          ->orWhereNull('is_guest');
                    })
                    ->where('email', $request->billing_email)
                    ->where('brand_id', $brand->id)
                    ->first();
            if($contact){
                return response()->json([
                    'status' => false,
                    'message' => 'You are already a registered customer. Please log in to place your order.'
                ]);
            }
            $guest = GuestSession::updateOrCreate(
                [
                    'id' => $guestSession->id
                ],
                [
                    'billing_first_name' => $request->billing_first_name,
                    'billing_last_name' => $request->billing_last_name,
                    'billing_company' => $request->billing_company,
                    'billing_address1' => $request->billing_address1,
                    'billing_address2' => $request->billing_address2,
                    'billing_city' => $request->billing_city,
                    'billing_state' => $request->billing_state,
                    'billing_zip' => $request->billing_zip,
                    'billing_country' => $request->billing_country ?? 'US',
                    'billing_phone' => $request->billing_phone,
                    'billing_email' => $request->billing_email,
                    'shipping_first_name' => $request->shipping_first_name,
                    'shipping_last_name' => $request->shipping_last_name,
                    'shipping_company' => $request->shipping_company,
                    'shipping_address1' => $request->shipping_address1,
                    'shipping_address2' => $request->shipping_address2,
                    'shipping_city' => $request->shipping_city,
                    'shipping_state' => $request->shipping_state,
                    'shipping_zip' => $request->shipping_zip,
                    'shipping_country' => $request->shipping_country ?? 'US',
                ]
            );

        } else {
            $contact = Auth::guard('api')->user();
            $userId = $contact->id;
            
            $customer = Contact::find($userId);
            // if customer address is not set then set it
            if($customer->address_line_1 == null){
                $customer->address_line_1 = $request->billing_address1;
                $customer->address_line_2 = $request->billing_address2;
                $customer->city = $request->billing_city;
                $customer->state = $request->billing_state;
                $customer->zip_code = $request->billing_zip;
                $customer->country = $request->billing_country ?? 'US';
            }
            if($customer->shipping_address1 == null){
                $customer->shipping_address1 = $request->shipping_address1;
                $customer->shipping_address2 = $request->shipping_address2;
                $contact->shipping_city = $request->shipping_city;
                $customer->shipping_state = $request->shipping_state;
                $customer->shipping_zip = $request->shipping_zip;
                $customer->shipping_country = $request->shipping_country ?? 'US';
            }
            $customer->save();
            
            $checkout = Cart::updateOrCreate(

                ['user_id' => $userId],
                [
                    'billing_first_name' => $request->billing_first_name,
                    'billing_last_name' => $request->billing_last_name,
                    'billing_company' => $request->billing_company,
                    'billing_address1' => $request->billing_address1,
                    'billing_address2' => $request->billing_address2,
                    'billing_city' => $request->billing_city,
                    'billing_state' => $request->billing_state,
                    'billing_zip' => $request->billing_zip,
                    'billing_country' => $request->billing_country ?? 'US',
                    'billing_phone' => $request->billing_phone,
                    'billing_email' => $request->billing_email,
                    'shipping_first_name' => $request->shipping_first_name,
                    'shipping_last_name' => $request->shipping_last_name,
                    'shipping_company' => $request->shipping_company,
                    'shipping_address1' => $request->shipping_address1,
                    'shipping_address2' => $request->shipping_address2,
                    'shipping_city' => $request->shipping_city,
                    'shipping_state' => $request->shipping_state,
                    'shipping_zip' => $request->shipping_zip,
                    'shipping_country' => $request->shipping_country ?? 'US',
                ]
            );
        }
        $userId = $isGuestRequest ? $guestSession->id : $userId;

        if($isGuestRequest) {
            $guestSession = $request->attributes->get('current_guest_session');
            $check = GuestSession::where('id', $guestSession->id)->firstOrFail();
        }else {
            $check = Cart::where('user_id', $userId)->firstOrFail();
        }
        
        if (!$check->isFreeze) {
            $response = $this->reserveCartItem($userId, $check, $isGuestRequest);
            return response()->json(['status' => true, 'message' => 'Address Selected Successfully, Stock reserved', 'data' => $response]);
        }
        return response()->json(['status' => true, 'message' => 'Address Selected Successfully', 'data' => 'cart already reserved'], 201);
    }

    //getAddress
    public function getAddress(Request $request)
    {
        $isGuestRequest = $request->attributes->get('is_guest_request', false);
        if ($isGuestRequest) {
            $guestSession = $request->attributes->get('current_guest_session');

            if (!$guestSession) {
                return response()->json([
                    'status' => false,
                    'message' => 'Guest session not found.'
                ], 400);
            }
            // Return guest address
            $response = [
                'billing_address' =>[
                    'billing_first_name' => $guestSession->billing_first_name ?? null,
                    'billing_last_name' => $guestSession->billing_last_name ?? null,
                    'billing_company' => $guestSession->billing_company ?? null,
                    'billing_address1' => $guestSession->billing_address1 ?? null,
                    'billing_address2' => $guestSession->billing_address2 ?? null,
                    'billing_city' => $guestSession->billing_city ?? null,
                    'billing_state' => $guestSession->billing_state ?? null,
                    'billing_zip' => $guestSession->billing_zip ?? null,
                    'billing_country' => $guestSession->billing_country ?? null,
                    'billing_phone' => $guestSession->billing_phone ?? null,
                    'billing_email' => $guestSession->billing_email ?? null,
                ],
                'shipping_address' =>[
                    'shipping_first_name' => $guestSession->shipping_first_name ?? null,
                    'shipping_last_name' => $guestSession->shipping_last_name ?? null,
                    'shipping_company' => $guestSession->shipping_company ?? null,
                    'shipping_address1' => $guestSession->shipping_address1 ?? null,
                    'shipping_address2' => $guestSession->shipping_address2 ?? null,
                    'shipping_city' => $guestSession->shipping_city ?? null,
                    'shipping_state' => $guestSession->shipping_state ?? null,
                    'shipping_zip' => $guestSession->shipping_zip ?? null,
                    'shipping_country' => $guestSession->shipping_country ?? null,
                    'shipping_phone' => $guestSession->billing_phone ?? null,
                    'shipping_email' => $guestSession->billing_email ?? null
                ],
            ];
        } else {

            $contact = Auth::guard('api')->user();
            $userId = $contact->id;
            $cart = Cart::where('user_id', $userId)->first();

            if ($cart) {
                $response = [
                    'billing_address' =>[
                        'billing_first_name' => $cart->billing_first_name??null,
                        'billing_last_name' => $cart->billing_last_name??null,
                        'billing_company' => $cart->billing_company??null,
                        'billing_address1' => $cart->billing_address1??null,
                        'billing_address2' => $cart->billing_address2??null,
                        'billing_city' => $cart->billing_city??null,
                        'billing_state' => $cart->billing_state??null,
                        'billing_zip' => $cart->billing_zip??null,
                        'billing_country' => $cart->billing_country??null,
                        'billing_phone' => $cart->billing_phone??null,
                        'billing_email' => $cart->billing_email??null,
                    ],
                    'shipping_address' =>[
                        'shipping_first_name' => $cart->shipping_first_name??null,
                        'shipping_last_name' => $cart->shipping_last_name??null,
                        'shipping_company' => $cart->shipping_company??null,
                        'shipping_address1' => $cart->shipping_address1??null,
                        'shipping_address2' => $cart->shipping_address2??null,
                        'shipping_city' => $cart->shipping_city??null,
                        'shipping_state' => $cart->shipping_state??null,
                        'shipping_zip' => $cart->shipping_zip??null,
                        'shipping_country' => $cart->shipping_country??null,
                        'shipping_phone' => $cart->billing_phone??null,
                        'shipping_email' => $cart->billing_email??null,
                    ]
                ];
                
            } else {
                $response = [
                    'billing_address' =>[
                        'billing_first_name' => $contact->first_name ?? null,
                        'billing_last_name' => $contact->last_name ?? null,
                        'billing_company' => $contact->supplier_business_name ?? null,
                        'billing_address1' => $contact->address_line_1 ?? null,
                        'billing_address2' => $contact->address_line_2 ?? null,
                        'billing_city' => $contact->city ?? null,
                        'billing_state' => $contact->state ?? null,
                        'billing_zip' => $contact->zip_code ?? null,
                        'billing_country' => $contact->country ?? null,
                        'billing_phone' => $contact->mobile ?? null,
                        'billing_email' => $contact->email ?? null,
                    ],
                    'shipping_address' =>[
                        'shipping_first_name' => $contact->shipping_first_name ?? null,
                        'shipping_last_name' => $contact->shipping_last_name ?? null,
                        'shipping_company' => $contact->shipping_company ?? null,
                        'shipping_address1' => $contact->shipping_address1 ?? null,
                        'shipping_address2' => $contact->shipping_address2 ?? null,
                        'shipping_city' => $contact->shipping_city ?? null,
                        'shipping_state' => $contact->shipping_state ?? null,
                        'shipping_zip' => $contact->shipping_zip ?? null,
                        'shipping_country' => $contact->shipping_country ?? null,
                        'shipping_address_line' => $contact->shipping_address ?? null,
                        'shipping_phone' => $contact->mobile ?? null,
                        'shipping_email' => $contact->email ?? null,
                    ]
                ];
            }
        }
        return response()->json(['status' => true, 'data' => $response]);
    }
    public function removeFromCustomerCartByState(Request $request, $itemId = null)
    {
        $isGuestRequest = $request->attributes->get('is_guest_request', false);

        // Get itemId from route or request
        if (!$itemId) {
            $itemId = $request->input('item_id') ?? $request->route('itemId');
        }

        DB::beginTransaction();
        try {
            if ($isGuestRequest) {
                // For guest users, you might want to create a similar method or handle differently
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'This endpoint is only available for authenticated customers.'
                ], 403);
            } else {
                return $this->removeFromCustomerCartByStateHandler($request, $itemId);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Remove from cart by state error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove items from cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle removal of cart items by state restrictions for customer
     * @param Request $request
     * @param int|null $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    private function removeFromCustomerCartByStateHandler(Request $request, $itemId = null)
    {
        DB::beginTransaction();
        try {
            $contact = Auth::guard('api')->user();
            $userId = $contact->id;
            $shippingState = $request->query('shipping_state') ?? $request->input('shipping_state') ?? $contact->shipping_state ?? 'IL';
            
            $checkout = Cart::where('user_id', $userId)->first();
            $isFreeze = $checkout ? $checkout->isFreeze : false;
            
            // Get ids from request body or query parameter
            $ids = [];
            if ($request->has('ids')) {
                $ids = $request->input('ids');
                if (is_string($ids)) {
                    $ids = explode(',', $ids);
                }
                if (!is_array($ids)) {
                    $ids = [];
                }
            } elseif ($request->has('keys')) {
                $ids = $request->input('keys');
                if (is_string($ids)) {
                    $ids = explode(',', $ids);
                }
                if (!is_array($ids)) {
                    $ids = [];
                }
            }
            
            // Combine itemId with ids array for checking, or get all cart items if no IDs specified
            $allIds = [];
            if ($itemId) {
                $allIds = array_merge([$itemId], $ids ?? []);
            } else {
                $allIds = $ids ?? [];
            }
            $allIds = array_filter($allIds); // Remove empty values
            $allIds = array_map('intval', $allIds); // Convert to integers
            
            // If no specific IDs provided, get all cart items for the user
            if (empty($allIds)) {
                // Get all cart items for the user
                $cartItems = CartItem::where('user_id', $userId)
                    ->lockForUpdate()
                    ->get();
            } else {
                // Get specific cart items by IDs
                $cartItems = CartItem::whereIn('id', $allIds)
                    ->where('user_id', $userId)
                    ->lockForUpdate()
                    ->get();
            }

            if ($cartItems->isEmpty()) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Cart item not found.']);
            }

            // Check state restrictions and collect IDs to delete
            $idsToDelete = [];
            $itemsToRestoreStock = [];
            
            foreach ($cartItems as $cartItem) {
                $product = Product::with('product_states')->find($cartItem->product_id);
                
                if (!$product) {
                    // If product doesn't exist, mark for deletion
                    $idsToDelete[] = $cartItem->id;
                    if ($isFreeze && $cartItem->variation_id) {
                        $itemsToRestoreStock[] = $cartItem;
                    }
                    continue;
                }

                $isRestricted = false;

                // Check if product has state restrictions
                if ($product->state_check == 'in') {
                    // Only these states are allowed
                    $allowedStates = $product->product_states->pluck('state')->toArray();
                    if (!in_array($shippingState, $allowedStates)) {
                        $isRestricted = true;
                    }
                } elseif ($product->state_check == 'not_in') {
                    // These states are excluded
                    $excludedStates = $product->product_states->pluck('state')->toArray();
                    if (in_array($shippingState, $excludedStates)) {
                        $isRestricted = true;
                    }
                }

                // If state doesn't have this product (restricted), mark for deletion
                if ($isRestricted) {
                    $idsToDelete[] = $cartItem->id;
                    if ($isFreeze && $cartItem->variation_id) {
                        $itemsToRestoreStock[] = $cartItem;
                    }
                }
            }

            // Restore stock for frozen cart items that will be deleted - only if enable_stock is true
            if ($isFreeze && !empty($itemsToRestoreStock)) {
                foreach ($itemsToRestoreStock as $item) {
                    $variation = Variation::find($item->variation_id);
                    if ($variation && $variation->product && $variation->product->enable_stock == 1) {
                        DB::table('variation_location_details')
                            ->where('variation_id', $item->variation_id)
                            ->increment('in_stock_qty', $item->qty);
                    }
                }
            }

            // Delete cart items that are restricted for the state
            if (!empty($idsToDelete)) {
                CartItem::whereIn('id', $idsToDelete)
                    ->where('user_id', $userId)
                    ->delete();
            }

            // Check if cart should be unfrozen after removing items
            if ($isFreeze && $checkout) {
                $remainingCartItems = CartItem::where('user_id', $userId)->count();
                // If no items left in cart, unfreeze it
                if ($remainingCartItems == 0) {
                    $checkout->update(['isFreeze' => false]);
                }
            }

            DB::commit();

            $message = !empty($idsToDelete) 
                ? 'Items that do not belong to the state have been removed from cart successfully.'
                : 'No items were removed. All items are valid for the current state.';

            return response()->json([
                'status' => true,
                'message' => $message,
                'deleted_ids' => $idsToDelete,
                'total_checked' => count($allIds) > 0 ? count($allIds) : $cartItems->count(),
                'deleted_count' => count($idsToDelete),
                'shipping_state' => $shippingState
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Remove from cart by state handler error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove items from cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
