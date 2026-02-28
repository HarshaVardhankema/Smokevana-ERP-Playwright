<?php

namespace App\Http\Controllers\ECOM;

use App\Cart;
use App\CartItem;
use App\Contact;
use App\GuestCartItem;
use App\Business;
use App\Http\Controllers\Controller;
use App\Jobs\UnfreezeCart;
use App\LocationTaxCharge;
use App\Product;
use App\Variation;
use App\VariationGroupPrice;
use App\VariationLocationDetails;
use App\CustomDiscount;
use App\CustomerAddress;
use App\Services\CustomDiscountRuleService;
use App\Services\GeoRestrictionService;
use App\Utils\TransactionUtil;
use App\ShipStation;
use App\Http\Controllers\ShipStationController;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public $cartDiscountApplicable;
    public $freeShippingApplicable;

    public function __construct()
    {
        $this->cartDiscountApplicable = true; // Track if cart-level discounts can be applied
        $this->freeShippingApplicable = true; // Track if free shipping can be applied
    }
    private function authCheck($request)
    {
        $contact = Auth::guard('api')->user();
        if ($contact) {
            return [
                'status' => true,
                'user' => $contact
            ];
        } else {
            return [
                'status' => false,
                'message' => 'User not authenticated',
            ];
        }
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
            ->where(function($query) use ($productId, $variantId) {
                $query->where('variant_id', $variantId)
                      ->orWhere(function($q) use ($productId) {
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
                        // AI generated code 
                        // $remainingOrders = max(0, $maxLimit - $orderCount);
                        
                        // // Calculate remaining quantity based on maxSaleLimit per order
                        // $maxQtyPerOrder = $maxSaleLimit ?? 1;
                        // $totalAllowedQty = $remainingOrders * $maxQtyPerOrder;
                        // $remainingQty = max(0, $totalAllowedQty - $qtyCount);
                        
                        // $willExceedOrders = $qtyGoingToAdd > $remainingQty ? $qtyGoingToAdd - $remainingQty : 0;

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
     * Get products with all necessary relationships
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param array $productIds
     * @param int $userId
     * @param int $priceGroupId
     * @return array
     */
    /**
     * Whether the customer uses default sell price (no specific price group).
     * @param int|string|null $priceGroupId
     * @return bool
     */
    private function useDefaultSellPrice($priceGroupId)
    {
        return $priceGroupId === 0 || $priceGroupId === null || $priceGroupId === '';
    }

    public function getProductsWithRelations($productIds, $userId, $priceGroupId, $isKeyByID = false)
    {
        $useDefaultPrice = $this->useDefaultSellPrice($priceGroupId);
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
            'variations' => function ($query) use ($priceGroupId, $useDefaultPrice) {
                $query->select([
                    'variations.id',
                    'variations.name',
                    'variations.product_id',
                    'variations.var_barcode_no',
                    'variations.var_maxSaleLimit',
                    'variations.product_variation_id',
                    'variations.default_purchase_price',
                    'variations.sell_price_inc_tax',
                    DB::raw($useDefaultPrice
                        ? 'COALESCE(variation_group_prices.price_inc_tax, variations.sell_price_inc_tax) as ad_price'
                        : 'variation_group_prices.price_inc_tax as ad_price'
                    ),
                    'variation_location_details.in_stock_qty as qty',

                ])
                    ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                        $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                            ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                    })
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
     * @return array
     */
    public function getTaxCharges($userState)
    {
        $webLocationId = config('services.b2b.location_id') ?? 1;
        return LocationTaxCharge::where('state_code', $userState)->where('web_location_id', $webLocationId)->whereNull('brand_id')->get();
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
        // Handle gift cards: use product price_range or first variation selling price
        if ($product->is_gift_card) {
            $price = (float) ($product->price_range ?? 0);
            if ($price <= 0 && $product->relationLoaded('variations') && $product->variations->isNotEmpty()) {
                $price = (float) ($product->variations->first()->ad_price ?? 0);
            }
            return $price;
        }
        
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
     * @param array $discounts
     * @param array $cartItems
     * @param array $products
     * @param object $discountService
     * @return array
     */
    public function validateAppliedDiscount($appliedDiscounts, $discounts, $cartItems, $products, $discountService)
    {
        $validAppliedDiscounts = [];
        foreach ($appliedDiscounts as $appliedDiscount) {
            $isValid = false;
            foreach ($discounts as $discount) {
                if ($discount->couponCode === $appliedDiscount) {
                    // For cart-level discounts (cartAdjustment, freeShipping), check against entire cart
                    if ($discount->discountType === 'cartAdjustment' || $discount->discountType === 'freeShipping') {
                        // Pass collections for cart-level discounts
                        if ($discountService->isDiscountApplicable($discount, $cartItems, $products, 0, [$appliedDiscount])) {
                            $isValid = true;
                            break;
                        }
                    } else {
                        // For product-level discounts, check individual products
                        foreach ($cartItems as $cartItem) {
                            $product = $products->where('id', $cartItem->product_id)->first();
                            if ($product) {
                                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                                if ($discountService->isDiscountApplicable($discount, $product, $variation, $cartItem->qty, [$appliedDiscount])) {
                                    $isValid = true;
                                    break 2;
                                }
                            }
                        }
                    }
                    break; // Found the discount, no need to check others
                }
            }
            if ($isValid) {
                $validAppliedDiscounts[] = $appliedDiscount;
            }
        }
        return $validAppliedDiscounts;
    }

    /** 
     * Calculate high priority discount (product adjustment and bxgy)
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param array $appliedDiscounts
     * @param array $discounts
     * @param array $cartItems
     * @param array $products
     * @param object $discountService
     * @return array
     */
    public function calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, $cartItems, $discountService)
    {
        // high priority discount (product adjustment and bxgy)
        $eligibleDiscounts = collect($discounts)
            ->filter(function ($discount) use ($discountService, $product, $variation, $cartItems, $appliedDiscounts) {
                return in_array($discount->discountType, ['productAdjustment', 'buyXgetY']) &&
                    $discountService->isDiscountApplicable($discount, $product, $variation, $cartItems->qty, $appliedDiscounts);
            })
            ->sortByDesc('setPriority')
            ->values();

        // Pick the discount with the highest priority (highest setPriority number)
        $appliedDiscount = $eligibleDiscounts->first();

        return $appliedDiscount;
    }
    /**
     * Apply tax calculations after discount applied
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param object $product
     * @param object $variation
     * @param float $discountedPrice
     * @param array $taxCharges
     */
    public function applyTaxCalculations($product, $variation, $discountedPrice, $taxCharges, $userState)
    {
        $productLocationID = $product->locationTaxType[0];
        $default_purchase_price = $variation?->default_purchase_price;
        $charges = $taxCharges->where('location_id', $productLocationID)
            ->where('state_code', $userState)
            ->first();

        if ($charges) {
            $taxType = $charges->tax_type;
            $value = $charges->value;

            switch ($taxType) {
                case 'UNIT_BASIS_ML':
                    // Treat as a per-unit flat charge; front-end already encodes any ML basis into $value.
                    $discountedPrice += $value;
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
                    // Treat as a per-unit flat charge; front-end already encodes any unit-count basis into $value.
                    $discountedPrice += $value;
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
        $isGiftCard = !empty($product->is_gift_card);
        $productPrice = $isGiftCard
            ? (float) ($product->price_range ?? $product->variations->first()?->ad_price ?? 0)
            : ($variation?->ad_price ?? 0);
        $stock = $isGiftCard
            ? (float) ($product->gift_card_stock ?? 0)
            : ($variation?->qty ?? 0);
        $sku = $isGiftCard ? ($product->sku ?? null) : ($variation?->sub_sku ?? null);

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
            'product_price' => $productPrice,
            'discounted_price' => $discountedPrice,
            'discounts' => $itemDiscounts,
            'product_image' => $variationImage,
            'variation_name' => $variation?->name == 'DUMMY' ? '' : ($variation?->name ?? null),
            'stock' => $stock,
            'sku' => $sku,
            'itemBarcode' => $variation?->var_barcode_no ?? null,
            'purchaseLimit' => $variation?->var_maxSaleLimit ?? $product->maxSaleLimit ?? null,
            'stock_status' => $stock > 0 ? 'instock' : 'outofstock',
            'qty' => $cartItem->qty,
            'is_gift_card' => $isGiftCard,
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
        // If request is coming through with a validated guest_session (set by middleware),
        // return the guest cart, same as /api/guest/cart, without requiring customer auth.
        if ($guestSession = $request->get('current_guest_session')) {
            try {
                $locationId = config('services.b2b.location_id', 1);
                $cartItems = GuestCartItem::with([
                    'product',
                    'variation.media',
                    'variation.variation_location_details' => fn ($q) => $q->where('location_id', $locationId),
                ])
                    ->where('guest_session_id', $guestSession->id)
                    ->get();

                $cartData = [];
                $subtotal = 0;
                $subtotal_inc_tax = 0;

                foreach ($cartItems as $item) {
                    $product = $item->product;
                    $variation = $item->variation;
                    if (!$product) {
                        continue;
                    }
                    $unitPrice = $variation && $variation->sell_price_inc_tax !== null
                        ? (float) $variation->sell_price_inc_tax
                        : 0;
                    $lineTotal = $unitPrice * $item->qty;
                    $subtotal += $lineTotal;
                    $subtotal_inc_tax += $lineTotal;

                    $variationImage = $this->getVariationImage($variation, $product);
                    $stockQty = $variation && $variation->relationLoaded('variation_location_details') && $variation->variation_location_details->isNotEmpty()
                        ? (float) $variation->variation_location_details->first()->in_stock_qty
                        : ($variation->qty ?? 0);

                    $cartData[] = [
                        'key' => $item->id,
                        'id' => $item->id,
                        'product_id' => $product->id,
                        'variation_id' => $variation?->id ?? null,
                        'ml' => $product->ml ?? null,
                        'ct' => $product->ct ?? null,
                        'locationTaxType' => $product->locationTaxType ?? null,
                        'maxSaleLimit' => $product->maxSaleLimit ?? null,
                        'product_name' => $product->name,
                        'product_slug' => $product->slug ?? null,
                        'product_price' => $unitPrice,
                        'discounted_price' => $unitPrice,
                        'discounts' => [],
                        'product_image' => $variationImage,
                        'variation_name' => ($variation && $variation->name !== 'DUMMY') ? $variation->name : '',
                        'stock' => $stockQty,
                        'sku' => $variation?->sub_sku ?? null,
                        'itemBarcode' => $variation?->var_barcode_no ?? null,
                        'purchaseLimit' => $variation?->var_maxSaleLimit ?? null,
                        'stock_status' => $stockQty > 0 ? 'instock' : 'outofstock',
                        'qty' => $item->qty,
                        'item_type' => $item->item_type ?? 'line_item',
                        'discount_id' => $item->discount_id,
                        'lable' => $item->lable ?? 'Item',
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ];
                }

                $itemCount = count($cartData);
                $totalQty = $cartItems->sum('qty');

                return response()->json([
                    'status' => true,
                    'message' => 'Cart Items',
                    'data' => $cartData,
                    'itemCount' => $itemCount,
                    'total_items' => $itemCount,
                    'total_qty' => $totalQty,
                    'subtotal' => round($subtotal, 2),
                    'subtotal_inc_tax' => round($subtotal_inc_tax, 2),
                    'total_tax_on_cart' => 0,
                    'cartDiscountDetails' => [],
                    'cartDiscountAmount' => 0,
                    'freeShippingDiscountDetails' => [],
                    'freeShippingDiscountAmount' => -1,
                    'applied_discounts' => [],
                    'gift_options' => [
                        'is_gift' => false,
                        'hide_prices_for_recipient' => false,
                    ],
                ]);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to get guest cart items',
                    'error' => $th->getMessage(),
                ], 500);
            }
        }

        $cid = $request->query('cid');
        $total_tax_on_cart = 0;

        if ($cid) {
            if (
                !auth()->user()->can('sell.update') &&
                !auth()->user()->can('direct_sell.access') &&
                !auth()->user()->can('so.update') &&
                !auth()->user()->can('edit_pos_payment')
            ) {
                abort(403, 'Unauthorized action.');
            }

            $userId = $cid;
            $contact = Contact::find($userId);
            if (!$contact) {
                return response()->json(['status' => false, 'message' => 'Customer not found.']);
            }
        } else {
            $authData = $this->authCheck($request);
            if (!$authData['status']) {
                return response()->json(['status' => false, 'message' => 'No API token provided.']);
            }

            $contact = $authData['user'];
            $userId = $contact->id;
        }

        try {
            $priceTier = $contact->price_tier;
            $priceGroupId = key($priceTier);

            // Get cart (checkout data) - needed for applied gift cards even when empty
            $cart = Cart::where('user_id', $userId)->first();
            
            // Get cart items
            $cartItemGet = $this->getCartItems($userId);
            if ($cartItemGet['status'] == false) {
                // Cart is empty - still return applied gift cards in data array if any
                [$appliedGiftCards, $giftCardAmount] = $this->resolveAppliedGiftCardsFromCart($cart);
                $cartData = [];
                foreach ($appliedGiftCards as $gcData) {
                    $cartData[] = [
                        'id' => 'applied_gift_card_' . $gcData['id'],
                        'key' => 'applied_gift_card_' . $gcData['id'],
                        'product_id' => null,
                        'variation_id' => null,
                        'product_name' => 'Gift Card Applied',
                        'variation_name' => $gcData['code'],
                        'product_slug' => null,
                        'product_price' => $gcData['amount_applied'], // Positive value for display
                        'discounted_price' => $gcData['amount_applied'],
                        'unit_price' => $gcData['amount_applied'],
                        'qty' => 1,
                        'stock' => null,
                        'stock_status' => null,
                        'product_image' => null,
                        'discounts' => [],
                        'item_type' => 'applied_gift_card',
                        'gift_card_id' => $gcData['id'],
                        'gift_card_code' => $gcData['code'],
                        'gift_card_balance' => $gcData['balance'],
                        'gift_card_amount_applied' => $gcData['amount_applied'],
                        'currency' => $gcData['currency'],
                    ];
                }
                return response()->json([
                    'status' => true,
                    'message' => $cartItemGet['message'],
                    'data' => $cartData,
                    'itemCount' => count($cartData),
                    'subtotal' => $giftCardAmount, // Include gift card in subtotal
                    'subtotal_inc_tax' => $giftCardAmount, // Include gift card in subtotal inc tax
                    'total_tax_on_cart' => 0,
                    'cartDiscountDetails' => [],
                    'cartDiscountAmount' => 0,
                    'freeShippingDiscountDetails' => [],
                    'freeShippingDiscountAmount' => -1,
                    'gift_card_amount' => $giftCardAmount,
                    'final_total' => max(0, 0 - $giftCardAmount), // Final total after gift card deduction
                    'reward_points' => ['balance' => 0, 'available' => 0, 'used' => 0, 'discount_amount' => 0, 'balance_after' => 0],
                    'final_total' => max(0, -$giftCardAmount),
                    'applied_discounts' => [],
                    'gift_options' => [
                        'is_gift' => (bool) ($cart?->is_gift ?? false),
                        'hide_prices_for_recipient' => (bool) ($cart?->hide_prices_for_recipient ?? false),
                    ],
                ]);
            }
            $cartItems = $cartItemGet['data'];
            $productIds = $cartItems->pluck('product_id');
            // Allow state/shipping_state query param to override for tax calculation (e.g. ?state=FL or ?shipping_state=FL)
            $stateFromQuery = $request->query('state') ?? $request->query('shipping_state');
            $userState = $stateFromQuery ?? $cart->shipping_state ?? $contact->shipping_state;
            if ($stateFromQuery !== null && (string) $stateFromQuery !== '' && $cart && ($cart->shipping_state ?? '') !== (string) $stateFromQuery) {
                $cart->shipping_state = $stateFromQuery;
                $cart->save();
            }
            $taxCharges = $this->getTaxCharges($userState);

            // Get discounts service 
            $discountService = new CustomDiscountRuleService();
            $discounts = $discountService->getActiveDiscounts($contact);
            $appliedDiscounts = $cart->applied_discounts ?? [];

            // Get products with relation 
            $products = $this->getProductsWithRelations($productIds, $userId, $priceGroupId);

            // Validate and clean up applied discounts
            $validAppliedDiscounts = $this->validateAppliedDiscount($appliedDiscounts, $discounts, $cartItems, $products, $discountService);
            if ($validAppliedDiscounts !== $appliedDiscounts) {
                $cart->update(['applied_discounts' => $validAppliedDiscounts]);
                $appliedDiscounts = $validAppliedDiscounts;
            }

            $cartData = [];
            $count = 0;
            $cart_total_before_tax = 0;
            $cart_final_total = 0;
            $temp =0;

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
                $temp=0;
                $product = $products->where('id', $cartItem->product_id)->first();
                if ($product) {
                    // Handle gift cards (variation_id = null) vs regular products
                    if ($product->is_gift_card) {
                        $variation = null; // Gift cards don't have variations
                    } else {
                        $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                    }
                    // Calculate unit price or price recall value 
                    $unitPrice = $this->calculateUnitPrice($variation, $product, $userId);

                    // Calculate base price before tax
                    // $cart_total_before_tax += $unitPrice * $cartItem->qty;

                    // Apply discount (product adjustment and bxgy) on cart items (Priority: 1)
                    $appliedDiscount = null;
                    $appliedDiscount = $this->calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, $cartItem, $discountService);

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

                            $itemDiscounts[] = [
                                'name' => $appliedDiscount->couponName,
                                'code' => $appliedDiscount->couponCode,
                                'type' => $appliedDiscount->discountType,
                                'value' => $appliedDiscount->discountValue,
                                'original_price' => $unitPrice,
                                'discounted_price' => $discountedPrice,
                                'discount' => $appliedDiscount->discount=='percentageDiscount'?'percentage':'fixed',
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
                    $total_tax_on_cart += ($discountedPrice * $cartItem->qty)-$temp;

                    // Get variation image
                    $variationImage = $this->getVariationImage($variation, $product);

                    // Build cart data
                    $cartData[] = $this->buildCartItemData($cartItem, $product, $variation, $discountedPrice, $itemDiscounts, $variationImage);

                    $count++;
                }
            }

            // ✅ ENHANCED: Process free items from buy X get Y discounts with B2C advanced logic
            // Group cart items by product to check total quantity across all variations
            $productQuantities = [];
            foreach ($cartItems as $cartItem) {
                if (!isset($productQuantities[$cartItem->product_id])) {
                    $productQuantities[$cartItem->product_id] = 0;
                }
                $productQuantities[$cartItem->product_id] += $cartItem->qty;
            }
            
            $freeItemsToAdd = [];
            $processedDiscounts = []; // Track which discounts we've already processed to prevent duplicates
            
            foreach ($cartItems as $cartItem) {
                $product = $products->where('id', $cartItem->product_id)->first();
                if (!$product) continue;

                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                if (!$variation) continue;

                // Calculate high priority discount (product adjustment and bxgy)
                $appliedDiscount = null;
                $appliedDiscount = $this->calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, $cartItem, $discountService);

                if ($appliedDiscount && $appliedDiscount->discountType === 'buyXgetY') {
                    $cartDiscountApplicable = false;
                    
                    // Create unique key for this discount + product combo to avoid duplicates
                    $discountKey = $appliedDiscount->id . '_' . $product->id;
                    if (isset($processedDiscounts[$discountKey])) {
                        continue; // Already processed this discount for this product
                    }
                    
                    $details = json_decode($appliedDiscount->custom_meta, true);
                    $buyQuantity = $details['buy_quantity'] ?? null;
                    $getYProductDetails = $details['get_y_products'] ?? [];
                    $isRecursive = $details['is_recursive'] ?? false;

                    // Use TOTAL product quantity across all variations (B2C logic)
                    $totalProductQty = $productQuantities[$cartItem->product_id];

                    if (!$buyQuantity || empty($getYProductDetails) || $totalProductQty < $buyQuantity) {
                        continue;
                    }
                    
                    // Mark this discount as processed
                    $processedDiscounts[$discountKey] = true;
                    
                    $timesToApply = $isRecursive ? floor($totalProductQty / $buyQuantity) : 1;
                    foreach ($getYProductDetails as $freebie) {
                        $freeItemsToAdd[] = [
                            'product_id' => $freebie['product_id'],
                            'variation_id' => $freebie['variation_id'],
                            'quantity' => $timesToApply * $freebie['quantity'],
                            'discount' => $appliedDiscount,
                        ];
                    }
                }
            }
            // ✅ ENHANCED: Append free items to cart list with B2C multi-variation distribution logic
            // Track free items allocated per variation to properly manage stock when multiple BOGO offers apply
            $freeItemQuantities = [];
            
            if (!empty($freeItemsToAdd)) {
                $freeProductIds = collect($freeItemsToAdd)->pluck('product_id')->unique()->toArray();
                // Get free products with relation
                $freeProducts = $this->getProductsWithRelations($freeProductIds, $userId, $priceGroupId, true);

                foreach ($freeItemsToAdd as $item) {
                    $product = $freeProducts->get($item['product_id'])??null;
                    if (!$product) continue;

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
                    
                    // Distribute free items across available variations (B2C advanced logic)
                    foreach ($variationsToCheck as $variation) {
                        if ($remainingQty <= 0) break;
                        
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
                        
                        // Track allocated free items for this variation
                        if (!isset($freeItemQuantities[$variation->id])) {
                            $freeItemQuantities[$variation->id] = 0;
                        }
                        $freeItemQuantities[$variation->id] += $qtyToGive;

                        $variationImage = $this->getVariationImage($variation, $product);
                        $discount = $item['discount'];
                        $cartDiscountApplicable = false;
                        $cartData[] = [
                            'key'              => 'free_' . $item['product_id'] . '_' . $variation->id . '_' . ($discount->id??''),
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
                                'name'            => $discount->couponName??null,
                                'code'            => $discount->couponCode??null,
                                'type'            => $discount->discountType??null,
                                'value'           => 'Free',
                                'original_price'  => $variation->ad_price ?? 0,
                                'discounted_price' => 0,
                                'discount_lable'  => $discount->discount_lable??null,
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


            // Apply cart-level discounts (Priority: 2) - ✅ FIXED: Only highest priority discount
            $cartDiscountAmount = 0;
            $cartDiscountDetails = [];
            $isFreeShippingApplicable = true;
            if ($cartDiscountApplicable) {
                // Find the highest priority applicable cart discount
                $selectedCartDiscount = null;
                foreach ($discounts as $discount) {
                    // Note: passing the cart item in the product variable
                    // Note: passing $products in the variation variable
                    if (
                        $discount->discountType === 'cartAdjustment' &&
                        $discountService->isDiscountApplicable($discount, $cartItems, $products, 0, $appliedDiscounts)
                    ) {
                        // Since discounts are already sorted by setPriority DESC, take the first match
                        $selectedCartDiscount = $discount;
                        break; // ✅ FIXED: Only apply the highest priority discount
                    }
                }
                
                // Apply only the selected highest priority discount
                if ($selectedCartDiscount) {
                    $discountAmount = $discountService->calculateCartDiscount($cart_final_total, $selectedCartDiscount, $cartItems, $products);
                    $isFreeShippingApplicable = false;

                    if ($discountAmount > 0) {
                        $cartDiscountAmount = $discountAmount;
                        $cartDiscountDetails[] = [
                            'label' => $selectedCartDiscount->discount_lable??null,
                            'name' => $selectedCartDiscount->couponName,
                            'code' => $selectedCartDiscount->couponCode,
                            'type' => $selectedCartDiscount->discountType,
                            'discount' => $selectedCartDiscount->discount=="fixedDiscount"?"fixed":"percentage",
                            'value' => $selectedCartDiscount->discountValue,
                            'discount_amount' => $discountAmount,
                            'min_order_value' => $selectedCartDiscount->minOrderValue ?? 0,
                            'max_discount_amount' => $selectedCartDiscount->maxDiscountAmount ?? null,
                            'discount_lable' => $selectedCartDiscount->discount_lable
                        ];
                    }
                }
                $cart_final_total = max(0, $cart_final_total - $cartDiscountAmount);
            }

            // Apply free shipping discount (Priority: 3) - ✅ FIXED: Only highest priority discount
            $freeShippingDiscountAmount = -1; // case of no amount applied
            $freeShippingDiscountDetails = [];
            // if user will pickup then no need to apply free shipping discount
            if (request()->has('shippingType') && request()->input('shippingType') == 'PICKUP') {
                $isFreeShippingApplicable = false;
            }
            // apply free shipping discount if no coupon is applied then it will be applied
            if ($cartDiscountApplicable && $isFreeShippingApplicable) {
                // Find the highest priority applicable free shipping discount
                $selectedFreeShippingDiscount = null;
                foreach ($discounts as $discount) {
                    if ($discount->discountType === 'freeShipping' && $discountService->isDiscountApplicable($discount, $cartItems, $products, 0, $appliedDiscounts)) {
                        // Since discounts are already sorted by setPriority DESC, take the first match
                        $selectedFreeShippingDiscount = $discount;
                        break; // ✅ FIXED: Only apply the highest priority discount
                    }
                }
                
                // Apply only the selected highest priority free shipping discount
                if ($selectedFreeShippingDiscount) {
                    $freeShippingDiscountAmount = $discountService->freeShippingDiscount($cart_final_total, $selectedFreeShippingDiscount, $cartItems, $products, 15.00);
                    if ($freeShippingDiscountAmount > -1) {
                        $freeShippingDiscountDetails[] = [
                            'label' => $selectedFreeShippingDiscount->discount_lable??null,
                            'name' => $selectedFreeShippingDiscount->couponName,
                            'code' => $selectedFreeShippingDiscount->couponCode,
                            'type' => $selectedFreeShippingDiscount->discountType,
                            'value' => $selectedFreeShippingDiscount->discountValue,
                            'discount_amount' => $freeShippingDiscountAmount,
                        ];
                    }
                }
            }

            // Gift Cards to Purchase - Add as purchasable items (before discounts)
            $giftCardsToPurchase = $cart->gift_cards_to_purchase ?? [];
            $giftCardsTotal = 0.0;
            $giftCardsData = [];
            
            try {
                if (!empty($giftCardsToPurchase)) {
                    foreach ($giftCardsToPurchase as $gcData) {
                        $giftCard = \App\GiftCard::find($gcData['id'] ?? null);
                        if ($giftCard && $giftCard->status === 'active') {
                            $giftCardAmount = (float) ($gcData['initial_amount'] ?? $giftCard->initial_amount);
                            $giftCardsTotal += $giftCardAmount;
                            
                            $giftCardsData[] = [
                                'id' => $giftCard->id,
                                'type' => 'gift_card',
                                'name' => 'Gift Card',
                                'initial_amount' => $giftCardAmount,
                                'currency' => $giftCard->currency ?? 'USD',
                                'image' => $giftCard->image ? asset('uploads/img/' . $giftCard->image) : null,
                                'type_label' => ucfirst($giftCard->type ?? 'egift'),
                                'qty' => 1,
                                'unit_price' => $giftCardAmount,
                                'total_price' => $giftCardAmount,
                            ];
                            
                            // Add to cart data as an item
                            $cartData[] = [
                                'id' => 'gift_card_' . $giftCard->id,
                                'product_id' => null,
                                'variation_id' => null,
                                'product_name' => 'Gift Card - ' . ucfirst($giftCard->type ?? 'egift'),
                                'variation_name' => null,
                                'qty' => 1,
                                'unit_price' => $giftCardAmount,
                                'total_price' => $giftCardAmount,
                                'image' => $giftCard->image ? asset('uploads/img/' . $giftCard->image) : null,
                                'item_type' => 'gift_card',
                                'gift_card_id' => $giftCard->id,
                                'gift_card_type' => $giftCard->type ?? 'egift',
                                'currency' => $giftCard->currency ?? 'USD',
                            ];
                            $count++;
                        }
                    }
                    
                    // Add gift cards total to cart totals (before discounts)
                    $cart_total_before_tax += $giftCardsTotal;
                    $cart_final_total += $giftCardsTotal;
                }
            } catch (\Throwable $e) {
                Log::debug('Gift cards to purchase failed in CartController@getCart', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Reward points preview (cart-level only, no persistence)
            $rewardPointsBalance = 0;      // total points the customer has
            $rewardPointsAvailable = 0;    // max redeemable this order (capped by business max_redeem_point)
            $rewardPointsUsed = 0;
            $rewardPointsDiscount = 0.0;
            $rewardPointsBalanceAfter = null;

            try {
                $business_id = $contact->business_id ?? null;
                if ($business_id) {
                    $business = Business::find($business_id);
                    if ($business && (int) $business->enable_rp === 1) {
                        /** @var \App\Utils\TransactionUtil $transactionUtil */
                        $transactionUtil = app(TransactionUtil::class);
                        $rewardPointsBalance = (int) ($contact->total_rp ?? 0);
                        $redeemDetails = $transactionUtil->getRewardRedeemDetails($business_id, $userId);
                        $rewardPointsAvailable = (int) ($redeemDetails['points'] ?? 0);

                        $useRewardPoints = filter_var($request->query('use_reward_points', false), FILTER_VALIDATE_BOOLEAN);
                        $pointsToRedeem = $request->query('reward_points_to_redeem');

                        // Use stored cart value when not passed in query (so cart total decreases without re-passing params)
                        if (($pointsToRedeem === null || $pointsToRedeem === '') && $cart && (int) ($cart->reward_points_to_redeem ?? 0) > 0) {
                            $pointsToRedeem = (int) $cart->reward_points_to_redeem;
                            $useRewardPoints = true;
                        }

                        // If use_reward_points=true, default to all available points (up to business limits)
                        if ($useRewardPoints && $rewardPointsAvailable > 0 && empty($pointsToRedeem)) {
                            $pointsToRedeem = $rewardPointsAvailable;
                        }

                        // Backend validation: customer cannot redeem more points than available
                        if (is_numeric($pointsToRedeem) && (int) $pointsToRedeem > 0 && $rewardPointsAvailable > 0) {
                            $pointsToRedeem = (int) $pointsToRedeem;

                            if ($pointsToRedeem > $rewardPointsAvailable) {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'You cannot redeem more reward points than your available balance.',
                                    'code' => 'reward_points_exceed_balance',
                                    'data' => [
                                        'requested_points' => $pointsToRedeem,
                                        'available_points' => $rewardPointsAvailable,
                                    ],
                                ], 422);
                            }

                            $amountPerPoint = (float) ($business->redeem_amount_per_unit_rp ?? 0.01);
                            $rewardPointsDiscount = round($pointsToRedeem * $amountPerPoint, 2);

                            // Don't allow discount to exceed cart total
                            if ($rewardPointsDiscount > $cart_final_total) {
                                $rewardPointsDiscount = $cart_final_total;
                                $pointsToRedeem = $amountPerPoint > 0
                                    ? (int) round($rewardPointsDiscount / $amountPerPoint)
                                    : 0;
                            }

                            // Respect minimum order total for redemption
                            $minOrderForRedeem = (float) ($business->min_order_total_for_redeem ?? 0);
                            if ($cart_final_total >= $minOrderForRedeem && $rewardPointsDiscount > 0) {
                                $rewardPointsUsed = $pointsToRedeem;
                                $rewardPointsBalanceAfter = $rewardPointsAvailable - $rewardPointsUsed;
                                $cart_final_total = max(0, $cart_final_total - $rewardPointsDiscount);
                            } else {
                                $rewardPointsDiscount = 0.0;
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Silently ignore reward point preview issues in cart
                Log::debug('Reward points preview failed in CartController@getCart', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Applied gift cards (redeemed balance applied to payment)
            $appliedGiftCardsData = [];
            $totalGiftCardAmount = 0.0;
            
            try {
                if ($cart) {
                    // Get applied gift cards from cart (stored as JSON array of IDs or as gift_card_amount)
                    $appliedGiftCardIds = $cart->applied_gift_cards ?? [];
                    
                    // Handle both array and JSON string formats
                    if (is_string($appliedGiftCardIds)) {
                        $appliedGiftCardIds = json_decode($appliedGiftCardIds, true) ?? [];
                    }
                    
                    if (!empty($appliedGiftCardIds) && is_array($appliedGiftCardIds)) {
                        foreach ($appliedGiftCardIds as $cardId) {
                            $giftCard = \App\GiftCard::find($cardId);
                            if ($giftCard && $giftCard->status === 'active' && $giftCard->balance > 0) {
                                // Check if expired
                                if ($giftCard->expires_at && $giftCard->expires_at->isPast()) {
                                    continue; // Skip expired cards
                                }
                                
                                $cardBalance = (float) $giftCard->balance;
                                $totalGiftCardAmount += $cardBalance;
                                
                                $appliedGiftCardsData[] = [
                                    'id' => $giftCard->id,
                                    'code' => $giftCard->code,
                                    'balance' => $cardBalance,
                                    'amount_applied' => $cardBalance,
                                    'currency' => $giftCard->currency ?? 'USD',
                                ];
                            }
                        }
                    }
                    
                    // Also check if gift_card_amount is stored directly (fallback)
                    if ($totalGiftCardAmount == 0 && isset($cart->gift_card_amount) && $cart->gift_card_amount > 0) {
                        $totalGiftCardAmount = (float) $cart->gift_card_amount;
                    }
                    
                    // Subtract gift card amount from final total (after all discounts and reward points)
                    if ($totalGiftCardAmount > 0) {
                        $cart_final_total = max(0, $cart_final_total - $totalGiftCardAmount);
                    }
                    
                    // Add applied gift cards to cart data array (as items)
                    foreach ($appliedGiftCardsData as $gcData) {
                        $cartData[] = [
                            'id' => 'applied_gift_card_' . $gcData['id'],
                            'key' => 'applied_gift_card_' . $gcData['id'],
                            'product_id' => null,
                            'variation_id' => null,
                            'product_name' => 'Gift Card Applied',
                            'variation_name' => $gcData['code'],
                            'product_slug' => null,
                            'product_price' => $gcData['amount_applied'], // Positive value for display
                            'discounted_price' => $gcData['amount_applied'],
                            'unit_price' => $gcData['amount_applied'],
                            'qty' => 1,
                            'stock' => null,
                            'stock_status' => null,
                            'product_image' => null,
                            'discounts' => [],
                            'item_type' => 'applied_gift_card',
                            'gift_card_id' => $gcData['id'],
                            'gift_card_code' => $gcData['code'],
                            'gift_card_balance' => $gcData['balance'],
                            'gift_card_amount_applied' => $gcData['amount_applied'],
                            'currency' => $gcData['currency'],
                        ];
                        $count++;
                    }
                }
            } catch (\Throwable $e) {
                // Silently ignore gift card issues in cart
                Log::debug('Applied gift cards failed in CartController@getCart', [
                    'error' => $e->getMessage(),
                ]);
            }

            // return cart data (with reward points preview totals)
            // Calculate subtotal including gift cards (since gift cards are shown as items in cart)
            $subtotal_with_gift_cards = $cart_total_before_tax + $totalGiftCardAmount;
            // Subtotal inc tax should include tax and gift cards (before gift card deduction)
            // $cart_final_total already has discounts/reward points applied, so we need to calculate from base
            $subtotal_inc_tax_with_gift_cards = $cart_total_before_tax + ($total_tax_on_cart ?? 0) + $totalGiftCardAmount;
            
            return response()->json([
                'status' => true,
                'message' => 'Cart Items',
                'data' => $cartData,
                'itemCount' => $count,
                'subtotal' => $subtotal_with_gift_cards, // Include gift card in subtotal
                'subtotal_inc_tax' => $subtotal_inc_tax_with_gift_cards, // Include gift card in subtotal inc tax
                'total_tax_on_cart' => $total_tax_on_cart ?? 0,
                // cart discounts
                'cartDiscountDetails' => $cartDiscountDetails,
                'cartDiscountAmount' => $cartDiscountAmount, // if 0 then no discount applied
                // free shipping discount
                'freeShippingDiscountDetails' => $freeShippingDiscountDetails,
                'freeShippingDiscountAmount' => $freeShippingDiscountAmount, // if -1 then no discount applied
                // reward points preview (balance = total points; available = max redeemable this order per business rules)
                'reward_points' => [
                    'balance' => $rewardPointsBalance,
                    'available' => $rewardPointsAvailable,
                    'used' => $rewardPointsUsed,
                    'discount_amount' => $rewardPointsDiscount,
                    'balance_after' => $rewardPointsBalanceAfter,
                ],
                'gift_card_amount' => $totalGiftCardAmount,
                'final_total' => $cart_final_total,
                // applied discounts for bxgy and productAdjustment
                'applied_discounts' => $appliedDiscounts, // customer filled
                // Gift order: show on cart page; used when placing order (invoice auto-hidden when is_gift)
                'gift_options' => [
                    'is_gift' => (bool) ($cart->is_gift ?? false),
                    'hide_prices_for_recipient' => (bool) ($cart->hide_prices_for_recipient ?? false),
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Cart access failed', 'data' => $th->getMessage() . ' in line ' . $th->getLine() . ' at file ' . $th->getFile()]);
        }
    }

    /**
     * PUT /api/cart/gift-options
     * Set gift order options for the cart (displayed on cart page; used when placing order).
     * When is_gift is true, process-order will auto-hide prices on packing slip for recipient unless overridden.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGiftOptions(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'Unauthorized.'], 401);
        }
        $contact = $authData['user'];
        $userId = $contact->id;

        $isGift = $request->boolean('is_gift', false);
        $hidePrices = $request->boolean('hide_prices_for_recipient', false);

        $cart = $this->getOrCreateCart($contact);
        $cart->update([
            'is_gift' => $isGift,
            'hide_prices_for_recipient' => $hidePrices,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Gift options updated.',
            'gift_options' => [
                'is_gift' => (bool) $cart->is_gift,
                'hide_prices_for_recipient' => (bool) $cart->hide_prices_for_recipient,
            ],
        ]);
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

        // Check authentication
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'Unauthorized user.']);
        }

        $contact = $authData['user'];
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

        // Get price tier for B2B customer
        $priceTier = $contact->price_tier;
        $priceGroupId = key($priceTier);
        
        $productIds = $cartItems->pluck('product_id');
        
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
        $locationId = $contact->location_id ?? null;
        $brandId = $contact->brand_id ?? null;
        
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
     * Remove discount from the cart (customer filled) 
     * this function removes the applied discount from cart
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeDiscount(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'Unauthorized user.']);
        }

        $contact = $authData['user'];
        $userId = $contact->id;

        // Get or create cart
        $cart = $this->getOrCreateCart($contact);

        // Clear applied discounts
        $cart->update(['applied_discounts' => []]);

        return response()->json(['status' => true, 'message' => 'Discount removed successfully.']);
    }

    /**
     * Apply gift card to cart
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyGiftCard(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized user.'
            ], 401);
        }

        $contact = $authData['user'];

        $validator = Validator::make($request->all(), [
            'gift_card_id' => 'required|integer|exists:gift_cards,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $giftCardId = $request->input('gift_card_id');
            $giftCard = \App\GiftCard::find($giftCardId);

            if (!$giftCard) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Gift card not found.'
                ], 404);
            }

            // Validate gift card status
            if ($giftCard->status !== 'active') {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'This gift card is not active.'
                ], 400);
            }

            if ($giftCard->balance <= 0) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'This gift card has no balance.'
                ], 400);
            }

            if ($giftCard->expires_at && $giftCard->expires_at->isPast()) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'This gift card has expired.'
                ], 400);
            }

            // Get or create cart
            $cart = $this->getOrCreateCart($contact);

            // Check if gift card is already applied to cart
            $appliedGiftCards = $cart->applied_gift_cards ?? [];
            if (in_array($giftCardId, $appliedGiftCards)) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'This gift card is already applied to your cart.'
                ], 400);
            }

            // Add gift card to applied list
            $appliedGiftCards[] = $giftCardId;
            
            // Calculate total gift card balance applied
            $totalGiftCardAmount = 0;
            $appliedGiftCardDetails = [];
            
            foreach ($appliedGiftCards as $cardId) {
                $card = \App\GiftCard::find($cardId);
                if ($card && $card->status === 'active' && $card->balance > 0) {
                    $totalGiftCardAmount += $card->balance;
                    $appliedGiftCardDetails[] = [
                        'id' => $card->id,
                        'code' => $card->code,
                        'balance' => (float) $card->balance,
                        'amount_applied' => (float) $card->balance
                    ];
                }
            }

            // Update cart with applied gift cards
            $cart->update([
                'applied_gift_cards' => $appliedGiftCards,
                'gift_card_amount' => $totalGiftCardAmount
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Gift card applied successfully.',
                'data' => [
                    'applied_gift_cards' => $appliedGiftCardDetails,
                    'total_gift_card_amount' => $totalGiftCardAmount,
                    'cart_total' => (float) $cart->total_amount,
                    'remaining_balance' => (float) ($cart->total_amount - $totalGiftCardAmount)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error applying gift card: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to apply gift card.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Purchase gift card directly (not add to cart)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function purchaseGiftCard(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized user.'
            ], 401);
        }

        $contact = $authData['user'];

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $productId = $request->input('product_id');
            $quantity = $request->input('quantity');

            $giftCard = Product::where('id', $productId)
                ->where('is_gift_card', 1)
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->first();

            if (!$giftCard) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Gift card not found or inactive.'
                ], 404);
            }

            // Check stock availability
            if ($giftCard->gift_card_stock < $quantity) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient gift card stock.',
                    'available_stock' => $giftCard->gift_card_stock
                ], 400);
            }

            // Calculate total price
            $totalPrice = $giftCard->gift_card_value * $quantity;

            // Create order or process payment (simplified for now)
            // In real implementation, this would create a proper order
            
            // Generate gift card codes for each purchased card
            $generatedCodes = [];
            for ($i = 0; $i < $quantity; $i++) {
                $code = 'GIFT' . strtoupper(uniqid());
                $generatedCodes[] = $code;
                
                // Store individual gift card in database
                \App\GiftCard::create([
                    'code' => $code,
                    'balance' => $giftCard->gift_card_value,
                    'status' => 'active',
                    'expires_at' => $giftCard->gift_card_expiry_days ? 
                        now()->addDays($giftCard->gift_card_expiry_days) : null,
                    'customer_id' => $contact->id,
                    'product_id' => $productId,
                    'created_at' => now(),
                ]);
            }

            // Update gift card stock
            $giftCard->decrement('gift_card_stock', $quantity);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Gift card purchased successfully.',
                'data' => [
                    'product_id' => $productId,
                    'product_name' => $giftCard->name,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                    'gift_card_codes' => $generatedCodes,
                    'currency' => 'USD',
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error purchasing gift card: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to purchase gift card.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function activeDiscount(Request $request){
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized user.'
            ]);
        }

        $customer = $authData['user'];

        $discounts = CustomDiscount::where('isDisabled', false)
            ->where(function($query) {
                $query->whereNull('applyDate')
                      ->orWhere('applyDate', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('endDate')
                      ->orWhere('endDate', '>=', now());
            })
            ->select('id','couponName','discountType','discountValue','discount_lable','minOrderValue','maxDiscountAmount')
            
            ->get()
            ->values();

        return response()->json([
            'status' => true,
            'message' => 'Active discounts retrieved successfully.',
            'data' => $discounts
        ]);
    }


    /**
     * Add or update cart items
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkCartAddOrUpdate(Request $request)
    {
        // If request has a validated guest_session (set by middleware),
        // treat this as a guest cart add/update operation instead of requiring customer auth.
        if ($guestSession = $request->get('current_guest_session')) {
            try {
                // Support both single-item payload (product_id, qty, ...) and
                // the standard items[] array payload used for authenticated carts.
                $itemsPayload = $request->input('items');

                if ($itemsPayload && is_array($itemsPayload)) {
                    $validator = Validator::make($request->all(), [
                        'items' => 'required|array|min:1',
                        'items.*.product_id' => 'required|integer|exists:products,id',
                        'items.*.variation_id' => 'nullable|integer|exists:variations,id',
                        'items.*.qty' => 'required|integer|min:1',
                    ]);

                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Validation failed',
                            'errors' => $validator->errors(),
                        ], 422);
                    }

                    $results = [];
                    foreach ($itemsPayload as $item) {
                        $productId = $item['product_id'];
                        $variationId = $item['variation_id'] ?? null;
                        $qty = $item['qty'];
                        $itemType = $item['item_type'] ?? 'line_item';
                        $discountId = $item['discount_id'] ?? null;
                        $label = $item['lable'] ?? 'Item';

                        // Check product active
                        $product = Product::where('id', $item['product_id'])
                            ->where('is_inactive', 0)
                            ->first();

                        if (!$product) {
                            $results[] = [
                                'product_id' => $productId,
                                'variation_id' => $variationId,
                                'status' => false,
                                'message' => 'Product not found or inactive.',
                            ];
                            continue;
                        }

                        // Check if this is a gift card and handle accordingly
                        if ($product->is_gift_card) {
                            // Gift cards can now be added to cart like regular products
                            // Use gift_card_stock for stock validation
                            $availableStock = $product->gift_card_stock ?? 0;
                            if ($availableStock < $qty) {
                                $results[] = [
                                    'product_id' => $productId,
                                    'variation_id' => $variationId,
                                    'qty' => $qty,
                                    'status' => false,
                                    'message' => 'Insufficient gift card stock. Available: ' . $availableStock,
                                    'is_gift_card' => true,
                                ];
                                continue;
                            }
                            
                            // Gift cards use variation_id = null since they don't have variations
                            $variationId = null;
                        }

                        // Validate variation if provided
                        if ($variationId) {
                            $variation = Variation::where('id', $variationId)
                                ->where('product_id', $productId)
                                ->first();

                            if (!$variation) {
                                $results[] = [
                                    'product_id' => $productId,
                                    'variation_id' => $variationId,
                                    'status' => false,
                                    'message' => 'Invalid variation for this product.',
                                ];
                                continue;
                            }
                        }

                        // Upsert guest cart item (same logic as B2bGuestCartController@addToCart)
                        $existingItem = GuestCartItem::where('guest_session_id', $guestSession->id)
                            ->where('product_id', $productId)
                            ->where('variation_id', $variationId)
                            ->first();

                        if ($existingItem) {
                            $existingItem->update([
                                'qty' => $existingItem->qty + $qty,
                                'item_type' => $itemType,
                                'discount_id' => $discountId,
                                'lable' => $label,
                            ]);
                            $cartItem = $existingItem;
                        } else {
                            $cartItem = GuestCartItem::create([
                                'guest_session_id' => $guestSession->id,
                                'product_id' => $productId,
                                'variation_id' => $variationId,
                                'qty' => $qty,
                                'item_type' => $itemType,
                                'discount_id' => $discountId,
                                'lable' => $label,
                            ]);
                        }

                        $results[] = [
                            'product_id' => $cartItem->product_id,
                            'variation_id' => $cartItem->variation_id,
                            'qty' => $cartItem->qty,
                            'item_type' => $cartItem->item_type,
                            'discount_id' => $cartItem->discount_id,
                            'lable' => $cartItem->lable,
                            'status' => true,
                        ];
                    }

                    return response()->json([
                        'status' => true,
                        'message' => 'Guest cart updated successfully',
                        'items' => $results,
                    ], 200);
                }

                // Single-item guest payload (product_id, variation_id, qty, ...)
                $validator = Validator::make($request->all(), [
                    'product_id' => 'required|integer|exists:products,id',
                    'variation_id' => 'nullable|integer|exists:variations,id',
                    'qty' => 'required|integer|min:1',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 422);
                }

                $productId = $request->input('product_id');
                $variationId = $request->input('variation_id');
                $qty = $request->input('qty');
                $itemType = $request->input('item_type', 'line_item');
                $discountId = $request->input('discount_id');
                $label = $request->input('lable', 'Item');

                // Check product active
                $product = Product::where('id', $productId)
                    ->where('is_inactive', 0)
                    ->first();

                if (!$product) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Product not found or inactive.',
                    ], 404);
                }

                // Validate variation if provided
                if ($variationId) {
                    $variation = Variation::where('id', $variationId)
                        ->where('product_id', $productId)
                        ->first();

                    if (!$variation) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid variation for this product.',
                        ], 404);
                    }
                }

                $existingItem = GuestCartItem::where('guest_session_id', $guestSession->id)
                    ->where('product_id', $productId)
                    ->where('variation_id', $variationId)
                    ->first();

                if ($existingItem) {
                    $existingItem->update([
                        'qty' => $existingItem->qty + $qty,
                        'item_type' => $itemType,
                        'discount_id' => $discountId,
                        'lable' => $label,
                    ]);
                    $cartItem = $existingItem;
                } else {
                    $cartItem = GuestCartItem::create([
                        'guest_session_id' => $guestSession->id,
                        'product_id' => $productId,
                        'variation_id' => $variationId,
                        'qty' => $qty,
                        'item_type' => $itemType,
                        'discount_id' => $discountId,
                        'lable' => $label,
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Item added to guest cart successfully',
                    'data' => [
                        'id' => $cartItem->id,
                        'product_id' => $cartItem->product_id,
                        'variation_id' => $cartItem->variation_id,
                        'qty' => $cartItem->qty,
                        'item_type' => $cartItem->item_type,
                        'discount_id' => $cartItem->discount_id,
                        'lable' => $cartItem->lable,
                    ],
                ], 201);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to update guest cart',
                    'error' => $th->getMessage(),
                ], 500);
            }
        }

        $currentTime = now(); // client time
        $cid = $request->query('cid');

        if ($cid) {
            if (
                !auth()->user()->can('sell.update') &&
                !auth()->user()->can('direct_sell.access') &&
                !auth()->user()->can('so.update') &&
                !auth()->user()->can('edit_pos_payment')
            ) {
                abort(403, 'Unauthorized action.');
            }

            $userId = $cid;
            $contact = Contact::find($userId);
            if (!$contact) {
                return response()->json(['status' => false, 'message' => 'Customer not found.']);
            }
        } else {
            $authData = $this->authCheck($request);
            if (!$authData['status']) {
                return response()->json(['status' => false, 'message' => 'No API token provided.']);
            }

            $contact = $authData['user'];
            $userId = $contact->id;
        }

        // Validation
        $validate = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.variation_id' => 'nullable|integer', // Remove exists:variations for gift cards
            'items.*.qty' => 'required|integer|min:1',
        ]);

        if ($validate->fails()) {
            $formattedErrors = [];
            foreach ($validate->errors()->toArray() as $key => $errorMessages) {
                $formattedErrors[] = ['field' => $key, 'messages' => $errorMessages];
            }
            return response()->json(['status' => false, 'message' => $formattedErrors]);
        }

        $priceTier = $contact->price_tier;
        $priceGroupId = key($priceTier);
        $items = $request->input('items');
        $checkout = Cart::where('user_id', $userId)->first();
        $isFreeze = $checkout ? $checkout->isFreeze : false;
        $ms = [];

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                // Resolve product first to distinguish gift card vs regular
                $product = Product::where('id', $item['product_id'])
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->first();

                if (!$product) {
                    $ms[] = "Product not found";
                    continue;
                }

                $isGiftCard = !empty($product->is_gift_card);
                if ($isGiftCard) {
                    // Gift cards: no variation; always use variation_id null in cart
                    $variation = null;
                    $effectiveVariationId = null;
                } else {
                    // Regular products: variation_id required and must exist (flow unchanged)
                    if (empty($item['variation_id'])) {
                        $ms[] = "Variation is required for product: {$product->name}.";
                        continue;
                    }
                    $variation = DB::table('variations')
                        ->join('variation_location_details', 'variations.id', '=', 'variation_location_details.variation_id')
                        ->where('variations.id', $item['variation_id'])
                        ->where('variations.product_id', $item['product_id'])
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
                        $ms[] = "Invalid variation for product: {$product->name}.";
                        continue;
                    }
                    $effectiveVariationId = $variation->id;
                }

                $variationName = $isGiftCard ? '' : ($variation->name === 'DUMMY' ? ' ' : $variation->name);
                $maxSaleLimit = $isGiftCard ? ($product->maxSaleLimit ?? false) : ($variation->var_maxSaleLimit ?? $product->maxSaleLimit ?? false);
                $cartItem = CartItem::where([
                    'user_id' => $userId,
                    'product_id' => $item['product_id'],
                    'variation_id' => $effectiveVariationId,
                ])
                    ->lockForUpdate()
                    ->first();

                $existingQty = $cartItem ? $cartItem->qty : 0;
                $newQty = $existingQty + $item['qty'];

                // session limit check (customer are limited for few)
                if ($maxSaleLimit) {
                    $can_add = $this->allowedItemQty($item['product_id'], $effectiveVariationId, $userId, $item['qty'], $currentTime, $maxSaleLimit);
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
                // Only check stock if enable_stock is true
                if ($product->enable_stock == 1) {
                    $availableStock = $product->is_gift_card ? $product->gift_card_stock : ($variation->qty ?? 0);
                    if ($availableStock + $existingQty < $newQty && $isFreeze) {
                        $ms[] = "{$product->name} {$variationName} has insufficient stock.";
                        continue;
                    }

                    if ($newQty > $availableStock && !$isFreeze) {
                        $newQty = $availableStock;
                        $ms[] = "{$product->name} {$variationName} quantity adjusted to available stock ({$availableStock}).";
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
                    $cartItemData = CartItem::create([
                        'user_id' => $userId,
                        'product_id' => $item['product_id'],
                        'variation_id' => $effectiveVariationId,
                        'qty' => $newQty,
                        'item_type' => 'line_item',
                        'discount_id' => null,
                        'lable' => 'Item',
                    ]);
                    $ms[] = "{$product->name} {$variationName} added with {$item['qty']} items.";
                    $cart_item_id = $cartItemData->id;
                }

                // Only manage stock if enable_stock is true
                if ($isFreeze && $qtyDiff > 0 && $product->enable_stock == 1) {
                    try {
                        if ($product->is_gift_card) {
                            // Gift cards use gift_card_stock field
                            $updated = DB::table('products')
                                ->where('id', $product->id)
                                ->decrement('gift_card_stock', $qtyDiff);
                        } else {
                            // Regular products use variation_location_details
                            $updated = DB::table('variation_location_details')
                                ->where('variation_id', $variation->id)
                                ->decrement('in_stock_qty', $qtyDiff);
                        }
                    } catch (\Throwable $th) {
                        if ($product->is_gift_card) {
                            $updated = DB::table('products')
                                ->where('id', $product->id)
                                ->update(['gift_card_stock' => 0]);
                        } else {
                            $updated = DB::table('variation_location_details')
                                ->where('variation_id', $variation->id)
                                ->update(['in_stock_qty' => 0]);
                        }
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
                'item_id' => $cart_item_id??null
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
        $cid = $request->query('cid');
        if ($cid) {
            if (
                !auth()->user()->can('sell.update') &&
                !auth()->user()->can('direct_sell.access') &&
                !auth()->user()->can('so.update') &&
                !auth()->user()->can('edit_pos_payment')
            ) {
                abort(403, 'Unauthorized action.');
            }

            $userId = $cid;
            $contact = Contact::find($userId);
            if (!$contact) {
                return response()->json(['status' => false, 'message' => 'Customer not found.']);
            }
        } else {
            $authData = $this->authCheck($request);
            if (!$authData['status']) {
                return response()->json(['status' => false, 'message' => 'Unauthorized user.']);
            }
            $contact = $authData['user'];
            $userId = $contact->id;
        }

        $validate = Validator::make($request->all(), [
            'item_id' => 'required|integer|exists:cart_items,id',
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
        $itemId = $request->input('item_id');
        $qtyToReduce = $request->input('qty');

        $checkout = Cart::where('user_id', $userId)->first();
        $isFreeze = $checkout ? $checkout->isFreeze : false;

        DB::beginTransaction();
        try {
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
            // Only manage stock if enable_stock is true
            if ($isFreeze && $cartItem->variation_id) {
                $variation = Variation::find($cartItem->variation_id);
                if ($variation && $variation->product && $variation->product->enable_stock == 1) {
                    $updated = DB::table('variation_location_details')
                        ->where('variation_id', $cartItem->variation_id)
                        ->increment('in_stock_qty', $qtyToReduce);
                }
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Quantity successfully reduced.', 'new_qty' => $cartItem->qty]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Failed to reduce quantity.', 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * Delete a cart item
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteItem(Request $request)
    {
        $guestSession = $request->get('current_guest_session') ?: $request->attributes->get('current_guest_session');

        if ($guestSession) {
            $validate = Validator::make($request->all(), [
                'item_id' => 'required|integer',
            ]);
            if ($validate->fails()) {
                return response()->json(['status' => false, 'message' => $validate->errors()->toArray()]);
            }
            $itemId = (int) $request->input('item_id');
            $guestItem = GuestCartItem::where('id', $itemId)
                ->where('guest_session_id', $guestSession->id)
                ->first();
            if (!$guestItem) {
                return response()->json(['status' => false, 'message' => 'Item not found in cart.']);
            }
            $guestItem->delete();
            return response()->json(['status' => true, 'message' => 'Item successfully removed from cart.']);
        }

        $cid = $request->query('cid');

        if ($cid) {
            if (
                !auth()->user()->can('sell.update') &&
                !auth()->user()->can('direct_sell.access') &&
                !auth()->user()->can('so.update') &&
                !auth()->user()->can('edit_pos_payment')
            ) {
                abort(403, 'Unauthorized action.');
            }

            $userId = $cid;
            $contact = Contact::find($userId);
            if (!$contact) {
                return response()->json(['status' => false, 'message' => 'Customer not found.']);
            }
        } else {
            $authData = $this->authCheck($request);
            if (!$authData['status']) {
                return response()->json(['status' => false, 'message' => 'Unauthorized user.']);
            }

            $contact = $authData['user'];
            $userId = $contact->id;
        }

        $validate = Validator::make($request->all(), [
            'item_id' => 'required|integer|exists:cart_items,id',
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
            return response()->json(['status' => false, 'message' => $formattedErrors]);
        }

        $itemId = $request->input('item_id');

        $checkout = Cart::where('user_id', $userId)->first();
        $isFreeze = $checkout ? $checkout->isFreeze : false;

        DB::beginTransaction();
        try {
            $cartItem = CartItem::where('id', $itemId)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if (!$cartItem) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Item not found in cart.']);
            }

            // Only manage stock if enable_stock is true
            if ($isFreeze && $cartItem->variation_id) {
                $variation = Variation::find($cartItem->variation_id);
                if ($variation && $variation->product && $variation->product->enable_stock == 1) {
                    DB::table('variation_location_details')
                        ->where('variation_id', $cartItem->variation_id)
                        ->lockForUpdate()
                        ->increment('in_stock_qty', $cartItem->qty);
                }
            }

            $cartItem->delete();

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Item successfully removed from cart.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Failed to delete item.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Empty the cart
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function emptyCartItems(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'Unauthorized user.'], 401);
        }

        $contact = $authData['user'];
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

            // Clear applied gift cards from cart
            if ($checkout) {
                $checkout->update([
                    'applied_gift_cards' => null,
                    'gift_card_amount' => 0,
                    'gift_card_code' => null, // Clear legacy field too
                ]);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Cart successfully emptied.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Failed to empty the cart.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get address from cart
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAddress(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'Unknown user']);
        }
        $contact = $authData['user'];
        $userId = $contact->id;
        $addresses = CustomerAddress::where('contact_id', $userId)->get();
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            $response = [
                'billing_first_name' => $cart->billing_first_name,
                'billing_last_name' => $cart->billing_last_name,
                'billing_company' => $cart->billing_company,
                'billing_address1' => $cart->billing_address1,
                'billing_address2' => $cart->billing_address2,
                'billing_city' => $cart->billing_city,
                'billing_state' => $cart->billing_state,
                'billing_zip' => $cart->billing_zip,
                'billing_country' => $cart->billing_country,
                'billing_phone' => $cart->billing_phone,
                'billing_email' => $cart->billing_email,
                'shipping_first_name' => $cart->shipping_first_name,
                'shipping_last_name' => $cart->shipping_last_name,
                'shipping_company' => $cart->shipping_company,
                'shipping_address1' => $cart->shipping_address1,
                'shipping_address2' => $cart->shipping_address2,
                'shipping_city' => $cart->shipping_city,
                'shipping_state' => $cart->shipping_state,
                'shipping_zip' => $cart->shipping_zip,
                'shipping_country' => $cart->shipping_country
            ];
            foreach ($response as $key => $value) {
                if (empty($value)) {
                    $response[$key] = $contact->$key ?? null;  //
                }
            }
        } else {
            $response = [
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
                'shipping_first_name' => $contact->shipping_first_name ?? null,
                'shipping_last_name' => $contact->shipping_last_name ?? null,
                'shipping_company' => $contact->shipping_company ?? null,
                'shipping_address1' => $contact->shipping_address1 ?? null,
                'shipping_address2' => $contact->shipping_address2 ?? null,
                'shipping_city' => $contact->shipping_city ?? null,
                'shipping_state' => $contact->shipping_state ?? null,
                'shipping_zip' => $contact->shipping_zip ?? null,
                'shipping_country' => $contact->shipping_country ?? null,
                'shipping_address_line' => $contact->shipping_address
            ];
        }
        return response()->json(['status' => true, 'data' => $response, 'saved_addresses' => $addresses]);
    }

    /**
     * Get live shipping rates for the authenticated customer's cart using ShipStation.
     *
     * Frontend: call this on the cart page after address is known to show
     * real-time shipping options (1‑day, 2‑day, etc.) instead of a flat rate.
     *
     * Request (JSON):
     *  - warehouse_id (optional): ShipStation warehouse to use. If omitted,
     *    the highest priority usable ShipStation record is used.
     *  - ship_to (optional): override shipping address structure:
     *      {
     *          "country_code": "US",
     *          "postal_code": "33801",
     *          "city_locality": "Lakeland",
     *          "state_province": "FL"
     *      }
     *
     * Response (JSON):
     *  - status: true/false
     *  - message: optional error message
     *  - rates: array of normalized rate objects when successful
     */
    public function getShippingRates(Request $request)
    {
        // Ensure customer is authenticated via API guard
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'Unknown user'], 401);
        }

        $contact = $authData['user'];
        $userId = $contact->id;

        // Load cart & items
        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart) {
            return response()->json(['status' => false, 'message' => 'Cart not found'], 404);
        }

        $cartItems = CartItem::where('user_id', $userId)->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Cart is empty'], 400);
        }

        // Determine ship-to address: request override > cart shipping > contact shipping
        $shipTo = $request->input('ship_to', []);
        $to_country_code = $shipTo['country_code']
            ?? $cart->shipping_country
            ?? $contact->shipping_country
            ?? 'US';
        $to_postal_code = $shipTo['postal_code']
            ?? $cart->shipping_zip
            ?? $contact->shipping_zip;
        $to_city_locality = $shipTo['city_locality']
            ?? $cart->shipping_city
            ?? $contact->shipping_city;
        $to_state_province = $shipTo['state_province']
            ?? $cart->shipping_state
            ?? $contact->shipping_state;

        if (empty($to_postal_code) || empty($to_city_locality) || empty($to_state_province)) {
            return response()->json([
                'status' => false,
                'message' => 'Incomplete shipping address for rate estimation',
            ], 422);
        }

        // Resolve ShipStation warehouse
        $warehouseId = $request->input('warehouse_id');
        if ($warehouseId) {
            $warehouse = ShipStation::find($warehouseId);
        } else {
            $warehouse = ShipStation::where('usable', 1)
                ->orderBy('priority', 'desc')
                ->first();
        }

        if (!$warehouse) {
            return response()->json([
                'status' => false,
                'message' => 'No ShipStation warehouse configured',
            ], 500);
        }

        // Build weight and dimensions from cart items.
        // Weight is totalled from product.weight (stored in lbs, converted to grams for ShipStation).
        // Dimensions are derived from product length/width/height in inches when available,
        // otherwise we fall back to a sensible default carton size.
        $products = Product::whereIn('id', $cartItems->pluck('product_id')->unique())->get()->keyBy('id');

        $totalWeightGrams = 0.0;
        $maxLength = 0.0;
        $maxWidth = 0.0;
        $sumHeights = 0.0;

        // Conversion factor: 1 pound (lb) = 453.592 grams
        $lbsToGrams = 453.592;

        foreach ($cartItems as $item) {
            $product = $products->get($item->product_id);
            if (!$product) {
                continue;
            }

            // Product weight is stored in lbs (admin input), convert to grams for ShipStation
            $productWeightLbs = (float) ($product->weight ?? 0);
            if ($productWeightLbs <= 0) {
                // still allow dimensions below to be considered
            } else {
                $qty = (int) ($item->qty ?? 1);
                // Convert lbs to grams: weight_lbs * conversion_factor * quantity
                $weightInGrams = $productWeightLbs * $lbsToGrams * max($qty, 1);
                $totalWeightGrams += $weightInGrams;
                
                // Debug logging for weight conversion verification
                \Log::debug('Product weight conversion', [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'weight_lbs' => $productWeightLbs,
                    'quantity' => $qty,
                    'weight_grams' => $weightInGrams,
                    'total_weight_grams' => $totalWeightGrams,
                ]);
            }

            // Dimensions: treat product dimensions as per‑unit inches.
            // For a single master package we approximate:
            //  - length = max of all item lengths
            //  - width  = max of all item widths
            //  - height = sum of (item height * qty) to simulate stacking
            $length = (float) ($product->length ?? 0);
            $width = (float) ($product->width ?? 0);
            $height = (float) ($product->height ?? 0);

            if ($length > 0) {
                $maxLength = max($maxLength, $length);
            }
            if ($width > 0) {
                $maxWidth = max($maxWidth, $width);
            }
            if ($height > 0) {
                $qty = (int) ($item->qty ?? 1);
                $sumHeights += ($height * max($qty, 1));
            }
        }

        if ($totalWeightGrams <= 0) {
            // Fallback minimal weight so ShipStation accepts the request
            $totalWeightGrams = 100; // 100g
        }

        $weight = [
            'value' => $totalWeightGrams,
            'unit' => 'gram',
        ];

        // Simple default / derived package dimensions in inches.
        // Use derived dimensions if we have any, otherwise fall back.
        $defaultLength = 10;
        $defaultWidth = 8;
        $defaultHeight = 4;

        $packageLength = $maxLength > 0 ? $maxLength : $defaultLength;
        $packageWidth = $maxWidth > 0 ? $maxWidth : $defaultWidth;
        $packageHeight = $sumHeights > 0 ? $sumHeights : $defaultHeight;

        $packages = [[
            'package_type' => 'package',
            'dimensions' => [
                'unit' => 'inch',
                'length' => $packageLength,
                'width' => $packageWidth,
                'height' => $packageHeight,
            ],
            'insuranceProviderId' => 0,
            'insuranceProvider' => 'None',
            'insured_value' => [
                'currency' => 'usd',
                'value' => 0.00,
            ],
            'label_messages' => [
                'reference1' => 'Cart rate estimate',
            ],
            'content_description' => 'Cart items',
        ]];

        // Build payload compatible with ShipStationController::getEstRate
        $payload = [
            'warehouse_id' => (string) $warehouse->id,
            'from_country_code' => $warehouse->country_code ?? 'US',
            'from_postal_code' => $warehouse->postal_code,
            'from_city_locality' => $warehouse->city_locality,
            'from_state_province' => $warehouse->state_province,
            'to_country_code' => $to_country_code,
            'to_postal_code' => $to_postal_code,
            'to_city_locality' => $to_city_locality,
            'to_state_province' => $to_state_province,
            'weight' => $weight,
            'packages' => $packages,
        ];

        // Delegate to existing ShipStation rate estimation logic
        /** @var ShipStationController $shipstationController */
        $shipstationController = app(ShipStationController::class);
        $rateRequest = new Request($payload);
        $rateResponse = $shipstationController->getEstRate($rateRequest);

        // getEstRate already returns a JsonResponse with status + data
        // We normalize that to a simpler contract for the cart frontend.
        $decoded = $rateResponse->getData(true);
        if (empty($decoded['status']) || !empty($decoded['message']) && empty($decoded['data'])) {
            return response()->json([
                'status' => false,
                'message' => $decoded['message'] ?? 'Unable to fetch shipping rates',
                'details' => $decoded['details'] ?? null,
            ], 422);
        }

        $rawRates = $decoded['data'] ?? [];

        // If only 1 rate with all nulls, it likely means getEstRate hit an error
        // for the first carrier and returned early. Check if rawRates is actually
        // an error response wrapped in data.
        // Also: if the raw rate has no service_code, it might be a partial/error entry
        // from ShipStation — filter those out.
        $rawRates = array_filter($rawRates, function ($rate) {
            // Keep only rates that have at least a service_code or shipping_amount
            return !empty($rate['service_code'])
                || !empty($rate['shipping_amount'])
                || !empty($rate['shipment_cost'])
                || !empty($rate['shippingAmount']);
        });
        $rawRates = array_values($rawRates); // re-index

        // Normalize ShipStation rate objects into a frontend-friendly format
        $normalizedRates = [];
        foreach ($rawRates as $rate) {
            // ShipEngine/ShipStation rate payloads commonly use either:
            //  - shipment_cost: { amount, currency }
            //  - shipping_amount: { amount, currency }
            //  - shippingAmount: { amount, currency }  (camelCase variant)
            $shipmentCostAmount = null;
            $shipmentCurrency = null;

            if (isset($rate['shipment_cost']) && is_array($rate['shipment_cost'])) {
                $shipmentCostAmount = $rate['shipment_cost']['amount'] ?? null;
                $shipmentCurrency = $rate['shipment_cost']['currency'] ?? null;
            } elseif (isset($rate['shipping_amount']) && is_array($rate['shipping_amount'])) {
                $shipmentCostAmount = $rate['shipping_amount']['amount'] ?? null;
                $shipmentCurrency = $rate['shipping_amount']['currency'] ?? null;
            } elseif (isset($rate['shippingAmount']) && is_array($rate['shippingAmount'])) {
                $shipmentCostAmount = $rate['shippingAmount']['amount'] ?? null;
                $shipmentCurrency = $rate['shippingAmount']['currency'] ?? null;
            }

            // "other charges" may be represented as other_amount / otherAmount
            $otherChargesAmount = 0;
            if (isset($rate['other_charges'])) {
                $otherChargesAmount = $rate['other_charges'];
            } elseif (isset($rate['other_amount']) && is_array($rate['other_amount'])) {
                $otherChargesAmount = $rate['other_amount']['amount'] ?? 0;
            } elseif (isset($rate['otherAmount']) && is_array($rate['otherAmount'])) {
                $otherChargesAmount = $rate['otherAmount']['amount'] ?? 0;
            }

            $normalizedRates[] = [
                'carrier_id' => $rate['carrier_id'] ?? null,
                'carrier_code' => $rate['carrier_code'] ?? null,
                'carrier_name' => $rate['carrier_friendly_name'] ?? $rate['carrierCode'] ?? null,
                'service_code' => $rate['service_code'] ?? null,
                'service_name' => $rate['service_type'] ?? $rate['serviceCode'] ?? null,
                'shipment_cost' => $shipmentCostAmount,
                'other_charges' => $otherChargesAmount,
                'currency' => $rate['currency'] ?? $shipmentCurrency ?? 'USD',
                'delivery_days' => $rate['delivery_days'] ?? null,
                'guaranteed_delivery_date' => $rate['guaranteed_delivery_date'] ?? null,
                'package_code' => $rate['package_code'] ?? $rate['packageType'] ?? null,
            ];
        }

        // Sort by shipment cost ascending as a sensible default
        usort($normalizedRates, function ($a, $b) {
            return ($a['shipment_cost'] ?? PHP_INT_MAX) <=> ($b['shipment_cost'] ?? PHP_INT_MAX);
        });

        // Collect weight conversion details for debugging
        $weightDebug = [];
        foreach ($cartItems as $item) {
            $product = $products->get($item->product_id);
            if ($product && $product->weight) {
                $productWeightLbs = (float) $product->weight;
                $qty = (int) ($item->qty ?? 1);
                $weightDebug[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'weight_lbs' => $productWeightLbs,
                    'quantity' => $qty,
                    'weight_grams' => $productWeightLbs * $lbsToGrams * max($qty, 1),
                ];
            }
        }

        return response()->json([
            'status' => true,
            'rates' => $normalizedRates,
            'meta' => [
                'weight' => $weight,
                'packages' => $packages,
            ],
            // DEBUG: remove after confirming live works
            '_debug' => [
                'raw_rate_count' => count($decoded['data'] ?? []),
                'filtered_rate_count' => count($rawRates),
                'first_raw_rate' => ($decoded['data'] ?? [])[0] ?? null,
                'decoded_status' => $decoded['status'] ?? null,
                'decoded_message' => $decoded['message'] ?? null,
                'weight_conversion' => [
                    'total_weight_grams' => $totalWeightGrams,
                    'conversion_factor_lbs_to_grams' => $lbsToGrams,
                    'products' => $weightDebug,
                ],
            ],
        ]); 
        
       
    }

    /**
     * Update address in cart
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function address(Request $request)
    {
        $cid = $request->query('cid');
        if ($cid) {
            if (
                !auth()->user()->can('sell.update') &&
                !auth()->user()->can('direct_sell.access') &&
                !auth()->user()->can('so.update') &&
                !auth()->user()->can('edit_pos_payment')
            ) {
                abort(403, 'Unauthorized action.');
            }

            $userId = $cid;
            $contact = Contact::find($userId);
            if (!$contact) {
                return response()->json(['status' => false, 'message' => 'Customer not found.']);
            }
        } else {
            $authData = $this->authCheck($request);
            if (!$authData['status']) {
                return response()->json(['status' => false, 'message' => 'Unauthorized user.']);
            }
            $contact = $authData['user'];
            $userId = $contact->id;
        }

        // State Restriction check
        $restrictedProducts = $this->checkProductStateRestrictions($userId, $request->shipping_state);
        
        if (!empty($restrictedProducts)) {
            return response()->json([
                'status' => false,
                'message' => 'Some products in your cart cannot be shipped to your location',
                'restricted_products' => $restrictedProducts,
                'reason' => 'geo_restriction'
            ], 422);
        }

        
        $priceTier = $contact->price_tier;
        $priceGroupId = key($priceTier);
        $location_id = $contact->location_id; // Get customer's location
        
        $validate = Validator::make($request->all(), [
            // 'user_id' => 'required|integer',
            'billing_first_name' => 'required|string|max:255',
            'billing_last_name' => 'nullable|string|max:255',
            'billing_company' => 'nullable|string|max:255',
            'billing_address1' => 'required|string|max:255',
            'billing_address2' => 'nullable|string|max:255',
            'billing_city' => 'required|string|max:255',
            'billing_state' => 'required|string|max:255',
            'billing_zip' => 'required|string|max:255',
            'billing_country' => 'nullable|string|max:255',
            'billing_phone' => 'nullable|string|min:10|max:10',
            'billing_email' => 'nullable|email|max:255',
            'shipping_first_name' => 'required|string|max:255',
            'shipping_last_name' => 'nullable|string|max:255',
            'shipping_company' => 'nullable|string|max:255',
            'shipping_address1' => 'required|string|max:255',
            'shipping_address2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:255',
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
        $check = Cart::where('user_id', $userId)->firstOrFail();
        if (!$check->isFreeze) {
            $response = $this->reserveCartItem($userId, $priceGroupId, $check, $location_id);
            return response()->json(['status' => true, 'message' => 'Address Selected Successfully, Stock reserved', 'data' => $response]);
        }
        return response()->json(['status' => true, 'message' => 'Address Selected Successfully', 'data' => 'cart already reserved'], 201);
    }
    /**
     * Reserve cart item (when customer select address) x item stock will be reserved for 5 minutes to that customer 
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param int $userId
     * @param int $priceGroupId
     * @param object $check
     * @param int $location_id - Customer's location ID for stock reservation
     * @return array
     */
    private function reserveCartItem($userId, $priceGroupId, $check, $location_id)
    {
        $cartItems = CartItem::where('user_id', $userId)->get();
        $ms = [];
        $flag = true;
        foreach ($cartItems as $cartItem) {
            $product = Product::with(['variations' => function ($query) use ($location_id) {
                $query->select([
                    'variations.id', // Specify table for the `id` column
                    'variations.var_maxSaleLimit',
                    'variations.product_id',
                    'variation_location_details.in_stock_qty as qty',
                    'variation_location_details.location_id'
                ])
                    ->leftJoin('variation_location_details', function ($join) use ($location_id) {
                        $join->on('variations.id', '=', 'variation_location_details.variation_id')
                             ->where('variation_location_details.location_id', '=', $location_id);
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
            if (!$variation && !$product->is_gift_card) {
                $ms[] = "Unknown Item";
                $cartItem->delete();
                continue; // not add to cart
            }

            $variationName = $product->is_gift_card ? '' : ($variation->name == 'DUMMY' ? ' ' : $variation->name);
            $maxSaleLimit = $product->is_gift_card ? ($product->maxSaleLimit ?? false) : ($variation->var_maxSaleLimit ?? $product->maxSaleLimit ?? false);
            if ($maxSaleLimit && $cartItem['qty'] > $maxSaleLimit) {
                $ms[] = "{$variationName} Cannot purchase more than {$maxSaleLimit} units.";
                $cartItem['qty'] = $maxSaleLimit;
                $cartItem->save();
            }
            // Only check stock if enable_stock == 1
            if ($product->enable_stock == 1) {
                $stockQty = $product->is_gift_card ? $product->gift_card_stock : ($variation->qty ?? 0);
                if ($stockQty < $cartItem['qty']) {
                    if ($stockQty <= 0) {
                        $ms[] = "{$product->name} {$variationName} have insufficient stock";
                        $cartItem->delete();
                    } else {
                        $ms[] = "{$product->name} {$variationName} has insufficient stock. Available: {$stockQty}";
                        $cartItem['qty'] = $stockQty;
                        $cartItem->save();
                    }
                    continue;
                }
            }

            // Only manage stock if enable_stock is true
            if ($product->enable_stock == 1) {
                try {
                    if ($product->is_gift_card) {
                        // Gift cards use gift_card_stock field
                        $updated = DB::table('products')
                            ->where('id', $product->id)
                            ->decrement('gift_card_stock', $cartItem['qty']);
                    } else {
                        // Regular products use variation_location_details
                        $updated = DB::table('variation_location_details')
                            ->where('variation_id', $variation->id)
                            ->where('location_id', $location_id)
                            ->decrement('in_stock_qty', $cartItem['qty']);
                    }
                    
                    if (!$updated) {
                        Log::error("Stock reservation failed for " . ($product->is_gift_card ? "gift card {$product->id}" : "variation {$variation->id}") . " at location {$location_id}");
                        $ms[] = "{$product->name} {$variationName}: Stock reservation failed";
                    }
                } catch (\Exception $e) {
                    Log::error("Stock reservation exception: " . $e->getMessage());
                    if ($product->is_gift_card) {
                        DB::table('products')
                            ->where('id', $product->id)
                            ->update(['gift_card_stock' => 0]);
                    } else {
                        DB::table('variation_location_details')
                            ->where('variation_id', $variation->id)
                            ->where('location_id', $location_id)
                            ->update(['in_stock_qty' => 0]);
                    }
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

    /**
     * Check product state restrictions for cart items
     * 
     * @param int $userId
     * @param string $shippingState
     * @return array
     */
    private function checkProductStateRestrictions($userId, $shippingState)
    {
        $cartItems = CartItem::where('user_id', $userId)->get();
        $restrictedProducts = [];

        foreach ($cartItems as $cartItem) {
            $productId = $cartItem->product_id;
            $variationId = $cartItem->variation_id;

            $product = Product::with('product_states')->find($productId);
            
            if (!$product) {
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
            // If state_check is 'all', no restriction applies

            if ($isRestricted) {
                $variation = $variationId ? Variation::find($variationId) : null;

                $restrictedProducts[] = [
                    'product_id' => $product->id,
                    'variation_id' => $variation ? $variation->id : null,
                    'product_name' => $product->name,
                    'product_image' => $product->image_url,
                    'variation_name' => $variation ? $variation->name : null,
                    'quantity' => $cartItem->qty,
                    'reason' => 'This product is unavailable for shipping to ' . $shippingState
                ];
            }
        }

        return $restrictedProducts;
    }


    /**
     * Check geo restrictions for cart items
     * 
     * @param int $userId
     * @param array $location
     * @return array
     */
    private function checkGeoRestrictions($userId, $location)
    {
        $geoService = app(GeoRestrictionService::class);
        $cartItems = CartItem::where('user_id', $userId)->get();
        $restrictedProducts = [];

        foreach ($cartItems as $cartItem) {
            $productId = $cartItem->product_id;
            $variationId = $cartItem->variation_id;

            // Check variation-specific restrictions first
            $isRestricted = false;
            if ($variationId) {
                $isRestricted = $geoService->isVariationRestricted($variationId, $location);
            }
            
            // If variation is not restricted, check product-level restrictions
            if (!$isRestricted) {
                $isRestricted = $geoService->isProductRestricted($productId, $location);
            }

            if ($isRestricted) {
                $product = Product::find($productId);
                $variation = $variationId ? Variation::find($variationId) : null;

                if ($product) {
                    $restrictedProducts[] = [
                        'product_id' => $product->id,
                        'variation_id' => $variation ? $variation->id : null,
                        'product_name' => $product->name,
                        'product_image' => $product->image_url,
                        'variation_name' => $variation ? $variation->name : null,
                        'quantity' => $cartItem->qty,
                        'reason' => 'This product is unavailable for shipping to your location'
                    ];
                }
            }
        }

        return $restrictedProducts;
    }
    public function removeFromCustomerCartByState(Request $request, $itemId = null)
    {
        $isGuestRequest = $request->attributes->get('is_guest_request', false);

        // Get itemId from route or request
        if (!$itemId) {
            $itemId = $request->input('item_id') ?? $request->route('itemId');
        }

        try {
            if ($isGuestRequest) {
                return response()->json([
                    'status' => false,
                    'message' => 'This endpoint is only available for authenticated customers.'
                ], 403);
            } else {
                return $this->removeFromCustomerCartByStateHandler($request, $itemId);
            }
        } catch (\Exception $e) {
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
            
            if (!$contact) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }
            
            $userId = $contact->id;
            $shippingState = $request->query('shipping_state') ?? $request->input('shipping_state') ?? $contact->shipping_state;
            //dd($shippingState);
            $checkout = Cart::where('user_id', $userId)->first();
            //dd($checkout);
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
            if ($isFreeze && $checkout && !empty($idsToDelete)) {
                // Refresh cart to get latest state
                $checkout->refresh();
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
       /**
     * Download cart as PDF
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function downloadCartPdf(Request $request)
    {
        try {
            $authData = $this->authCheck($request);
            if (!$authData['status']) {
                return response()->json(['status' => false, 'message' => 'No API token provided.'], 401);
            }

            $contact = $authData['user'];
            $userId = $contact->id;

            // Get cart data (reuse getCart logic)
            $priceTier = $contact->price_tier;
            $priceGroupId = key($priceTier);

            // Get cart items
            $cartItemGet = $this->getCartItems($userId);
            if ($cartItemGet['status'] == false) {
                return response()->json(['status' => false, 'message' => $cartItemGet['message']], 400);
            }
            $cartItems = $cartItemGet['data'];
            $productIds = $cartItems->pluck('product_id');
            
            // Get cart (checkout data). If missing, create a default cart for this user.
            $cart = Cart::where('user_id', $userId)->first();
            if (!$cart) {
                $cart = $this->getOrCreateCart($contact);
            }
            $userState = $cart->shipping_state ?? $contact->shipping_state;
            $taxCharges = $this->getTaxCharges($userState);

            // Get discounts service 
            $discountService = new CustomDiscountRuleService();
            $discounts = $discountService->getActiveDiscounts($contact);
            $appliedDiscounts = $cart->applied_discounts ?? [];

            // Get products with relation 
            $products = $this->getProductsWithRelations($productIds, $userId, $priceGroupId);

            // Validate applied discounts
            $validAppliedDiscounts = $this->validateAppliedDiscount($appliedDiscounts, $discounts, $cartItems, $products, $discountService);
            if ($validAppliedDiscounts !== $appliedDiscounts) {
                $cart->update(['applied_discounts' => $validAppliedDiscounts]);
                $appliedDiscounts = $validAppliedDiscounts;
            }

            $cartData = [];
            $cart_total_before_tax = 0;
            $cart_final_total = 0;
            $total_tax_on_cart = 0;

            // Build cart data (simplified for PDF)
            foreach ($cartItems as $cartItem) {
                $product = $products->where('id', $cartItem->product_id)->first();
                if ($product) {
                    $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                    $unitPrice = $this->calculateUnitPrice($variation, $product, $userId);
                    
                    // Apply discount if any
                    $discountedPrice = $unitPrice;
                    $appliedDiscount = $this->calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, $cartItem, $discountService);
                    if ($appliedDiscount && $appliedDiscount->discountType === 'productAdjustment') {
                        $discountedPrice = $discountService->calculateDiscountedPrice($unitPrice, $appliedDiscount);
                    }
                    
                    $cart_total_before_tax += $discountedPrice * $cartItem->qty;
                    $temp = $discountedPrice * $cartItem->qty;
                    
                    // Apply tax
                    $discountedPrice = $this->applyTaxCalculations($product, $variation, $discountedPrice, $taxCharges, $userState);
                    $cart_final_total += $discountedPrice * $cartItem->qty;
                    $total_tax_on_cart += ($discountedPrice * $cartItem->qty) - $temp;

                    // Get product image - use absolute file path for mpdf
                    $productImage = null;
                    try {
                        if (!empty($product->image)) {
                            $imagePath = public_path('uploads/img/' . $product->image);
                            if (file_exists($imagePath)) {
                                $productImage = $imagePath;
                            }
                        }
                        if (!$productImage) {
                            $defaultPath = public_path('img/default.png');
                            if (file_exists($defaultPath)) {
                                $productImage = $defaultPath;
                            }
                        }
                    } catch (\Exception $e) {
                        // Log error but continue without image
                        Log::error('Error loading product image for PDF: ' . $e->getMessage());
                    }
                    
                    $cartData[] = [
                        'product_name' => $product->name,
                        'variation_name' => ($variation && $variation->name != 'DUMMY') ? $variation->name : null,
                        'sku' => $variation->sub_sku ?? $product->sku ?? null,
                        'qty' => $cartItem->qty,
                        'unit_price' => $discountedPrice,
                        'total_price' => $discountedPrice * $cartItem->qty,
                        'product_image' => $productImage,
                    ];
                }
            }

            // Calculate cart discounts
            $cartDiscountAmount = 0;
            $cartDiscountDetails = [];
            // Simplified discount calculation for PDF
            if (!empty($appliedDiscounts)) {
                // You can add cart discount calculation here if needed
            }
            $cart_final_total = max(0, $cart_final_total - $cartDiscountAmount);

            // Get business logo - use absolute file path for mpdf
            $businessLogo = null;
            try {
                if (!empty($contact->business_id)) {
                    $business = Business::select('logo')->find($contact->business_id);
                    if ($business && !empty($business->logo)) {
                        $logoPath = public_path('uploads/business_logos/' . $business->logo);
                        if (file_exists($logoPath)) {
                            $businessLogo = $logoPath;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't break the PDF generation
                Log::error('Error loading business logo for PDF: ' . $e->getMessage());
            }

            // Prepare data for PDF
            $pdfData = [
                'contact' => $contact,
                'cart_items' => $cartData,
                'item_count' => count($cartData),
                'subtotal' => $cart_total_before_tax,
                'subtotal_inc_tax' => $cart_final_total,
                'total_tax' => $total_tax_on_cart,
                'cart_discount_amount' => $cartDiscountAmount,
                'cart_discount_details' => $cartDiscountDetails,
                'date' => now()->format('F d, Y'),
                'business_logo' => $businessLogo,
            ];

            // Generate PDF
            $body = view('cart.pdf', $pdfData)->render();

            $mpdf = new \Mpdf\Mpdf([
                'tempDir' => public_path('uploads/temp'),
                'mode' => 'utf-8',
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
                'autoVietnamese' => true,
                'autoArabic' => true,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_left' => 10,
                'margin_right' => 10,
                'format' => 'A4',
            ]);

            $mpdf->useSubstitutions = true;
            $mpdf->SetTitle('Cart-' . $contact->id . '-' . date('Y-m-d') . '.pdf');
            $mpdf->WriteHTML($body);
            
            // Get PDF as string instead of directly outputting
            $pdfContent = $mpdf->Output('', 'S');
            $filename = 'Cart-' . $contact->id . '-' . date('Y-m-d') . '.pdf';
            
            // Return proper HTTP response with CORS headers
            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, Accept-Language, Origin, Referer')
                ->header('Access-Control-Expose-Headers', 'Content-Disposition');

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error generating cart PDF',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Resolve applied gift cards from cart (supports both applied_gift_cards array and legacy gift_card_code)
     * @param Cart|null $cart
     * @return array [appliedGiftCards array, totalAmount float]
     */
    private function resolveAppliedGiftCardsFromCart($cart)
    {
        $appliedGiftCards = [];
        $giftCardAmount = 0.0;
        if (!$cart) {
            return [$appliedGiftCards, $giftCardAmount];
        }
        // Primary: applied_gift_cards (array of IDs) - used by applyGiftCard
        $ids = is_array($cart->applied_gift_cards ?? null) ? $cart->applied_gift_cards : [];
        if (is_string($cart->applied_gift_cards ?? null)) {
            $ids = json_decode($cart->applied_gift_cards, true) ?? [];
        }
        if (!empty($ids) && is_array($ids)) {
            $cards = \App\GiftCard::whereIn('id', $ids)
                ->where('status', 'active')
                ->where('balance', '>', 0)
                ->get();
            foreach ($cards as $giftCard) {
                // Check if expired
                if ($giftCard->expires_at && $giftCard->expires_at->isPast()) {
                    continue; // Skip expired cards
                }
                $appliedGiftCards[] = [
                    'id' => $giftCard->id,
                    'code' => $giftCard->code,
                    'balance' => (float) $giftCard->balance,
                    'amount_applied' => (float) $giftCard->balance,
                    'currency' => $giftCard->currency ?? 'USD',
                ];
                $giftCardAmount += (float) $giftCard->balance;
            }
            return [$appliedGiftCards, $giftCardAmount];
        }
        // Fallback: gift_card_code (legacy single card)
        if ($cart->gift_card_code) {
            $giftCard = \App\GiftCard::where('code', $cart->gift_card_code)->first();
            if ($giftCard && $giftCard->status === 'active' && $giftCard->balance > 0) {
                if (!$giftCard->expires_at || !$giftCard->expires_at->isPast()) {
                    $appliedGiftCards[] = [
                        'id' => $giftCard->id,
                        'code' => $giftCard->code,
                        'balance' => (float) $giftCard->balance,
                        'amount_applied' => (float) $giftCard->balance,
                        'currency' => $giftCard->currency ?? 'USD',
                    ];
                    $giftCardAmount = (float) $giftCard->balance;
                }
            }
        }
        return [$appliedGiftCards, $giftCardAmount];
    }
}
