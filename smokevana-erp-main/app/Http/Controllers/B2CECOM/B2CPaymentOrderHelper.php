<?php

namespace App\Http\Controllers\B2CECOM;

use App\Cart;
use App\CartItem;
use App\GuestCartItem;
use App\Contact;
use App\Http\Controllers\ECOM\CartController;
use App\Services\CustomDiscountRuleService;
use App\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class B2CPaymentOrderHelper
{
    /**
     * Get products with B2C pricing (sell_price_inc_tax instead of price_group_prices)
     */
    public static function getB2CProductsWithRelations($productIds, $userId, $priceGroupId, $locationId, $isKeyByID = false)
    {
        $products = Product::with([
            'webcategories',
            'brand',
            'customer_price_recalls' => function ($query) use ($userId) {
                if ($userId) {
                    $query->where('contact_id', $userId)
                        ->where('is_active', 1)
                        ->where('is_deleted', 0)
                        ->with(['updatedBy' => function ($q) {
                            $q->select('id', 'first_name', 'last_name');
                        }]);
                }
            },
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
            ->get();

        if ($isKeyByID) {
            return $products->keyBy('id');
        }

        return $products;
    }

    /**
     * Process cart items for B2C customer orders - NOW WITH FREE ITEMS SUPPORT
     */
    public static function processCartItemsForB2C($cartItems, $products, $transaction, $userId, $currentTime, $cartUtil, $discountService, $discounts, $appliedDiscounts, $taxCharges, $userState, &$appliedDiscountBucket, $locationId = null, $brandId = null)
    {
        $sellLinesData = [];
        $cartDiscountApplicable = true;
        $isFreeShippingApplicable = true;

        // Track purchased quantities per variation for BxGY stock adjustment
        $purchasedVariationQuantities = [];

        // Process regular cart items
        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                
                // Track purchased quantities
                if (!isset($purchasedVariationQuantities[$cartItem->variation_id])) {
                    $purchasedVariationQuantities[$cartItem->variation_id] = 0;
                }
                $purchasedVariationQuantities[$cartItem->variation_id] += $cartItem->qty;
                
                // Calculate unit price or price recall value 
                $unitPrice = $cartUtil->calculateUnitPrice($variation, $product, $userId);
                
                // Apply discount (product adjustment and bxgy) on cart items (Priority: 1)
                $appliedDiscount = $cartUtil->calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, $cartItem, $discountService, $userId, $locationId, $brandId);

                if ($appliedDiscount) {
                    if ($appliedDiscount->discountType === 'productAdjustment') {
                        $cartDiscountApplicable = false;
                    } elseif ($appliedDiscount->discountType === 'buyXgetY') {
                        $isFreeShippingApplicable = true;
                        $cartDiscountApplicable = false;
                    }
                    $appliedDiscountBucket[] = $appliedDiscount->couponName . '( id: ' . $appliedDiscount->id . ' type: ' . $appliedDiscount->discountType . ')';
                }

                $discountedPrice = $unitPrice;

                // Apply the discount that was found
                if ($appliedDiscount && $appliedDiscount->discountType === 'productAdjustment') {
                    $discountedPrice = $discountService->calculateDiscountedPrice($unitPrice, $appliedDiscount);
                }

                // Apply tax calculations after discount applied
                $discountedPriceIncTax = $cartUtil->applyTaxCalculations($product, $variation, $discountedPrice, $taxCharges, $userState);
                $itemTax = $discountedPriceIncTax - $discountedPrice;

                $sellLinesData[] = [
                    'transaction_id' => $transaction,
                    'product_id' => $product->id,
                    'variation_id' => $variation?->id,
                    'quantity' => $cartItem->qty,
                    'ordered_quantity' => $cartItem->qty,
                    'unit_price' => $discountedPrice,
                    'unit_price_before_discount' => $unitPrice ?? null,
                    'unit_price_inc_tax' => $discountedPriceIncTax ?? null,
                    'item_tax' => $itemTax,
                    'line_discount_type' => 'fixed',
                    'line_discount_amount' => $unitPrice - $discountedPrice,
                    'is_free'=>false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // ✅ NEW: Process free items from buy X get Y discounts
        // Group cart items by product to check total quantity across all variations
        $productQuantities = [];
        foreach ($cartItems as $cartItem) {
            if (!isset($productQuantities[$cartItem->product_id])) {
                $productQuantities[$cartItem->product_id] = 0;
            }
            $productQuantities[$cartItem->product_id] += $cartItem->qty;
        }
        
        $freeItemsToAdd = [];
        $processedDiscounts = []; // Track which discounts we've already processed
        
        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if (!$product) continue;

            $variation = $product->variations->where('id', $cartItem->variation_id)->first();
            if (!$variation) continue;

            $appliedDiscount = $cartUtil->calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, $cartItem, $discountService, $userId, $locationId, $brandId);

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
                
                // Use TOTAL product quantity across all variations
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

        // ✅ NEW: Add free items to sell lines
        // Track free items allocated per variation to properly manage stock when multiple BOGO offers apply
        $freeItemQuantities = [];
        
        if (!empty($freeItemsToAdd)) {
            $freeProductIds = collect($freeItemsToAdd)->pluck('product_id')->unique()->toArray();
            $freeProducts = self::getB2CProductsWithRelations(
                $freeProductIds, 
                $userId, 
                null, 
                request()->query('location_id') ?? 2, 
                true
            );

            foreach ($freeItemsToAdd as $item) {
                $product = $freeProducts->get($item['product_id']) ?? null;
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
                
                // Distribute free items across available variations
                foreach ($variationsToCheck as $variation) {
                    if ($remainingQty <= 0) break;
                    
                    // If stock is not enabled for this product, give full quantity without checking stock
                    if ($product->enable_stock == 0) {
                        $qtyToGive = $remainingQty;
                        $remainingQty = 0;
                    } else {
                        // Calculate available stock considering:
                        // 1. Already purchased quantities in this transaction
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

                    $appliedDiscountBucket[] = $item['discount']->couponName . '( id: ' . $item['discount']->id . ' type: buyXgetY - FREE ITEM)';

                    $sellLinesData[] = [
                        'transaction_id' => $transaction,
                        'product_id' => $product->id,
                        'variation_id' => $variation->id,
                        'quantity' => $qtyToGive,
                        'ordered_quantity' => $qtyToGive,
                        'unit_price' => 0, // Free item
                        'unit_price_before_discount' => $variation->ad_price ?? 0,
                        'unit_price_inc_tax' => 0, // Free item
                        'item_tax' => 0,
                        'line_discount_type' => 'fixed',
                        'is_free'=>true,
                        'line_discount_amount' => $variation->ad_price ?? 0, // Full discount
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        return $sellLinesData;
    }

    /**
     * Process cart items for B2C guest orders with discount and tax calculations - NOW WITH FREE ITEMS
     * Now returns array with sell lines data and applied discount bucket
     */
    public static function processGuestCartItemsForB2C($cartItems, $products, $transaction, $locationId, $appliedDiscounts = [], $discounts = [], $discountService = null, $taxCharges = [], $userState = 'IL', $cartUtil = null, &$appliedDiscountBucket = [])
    {
        $sellLinesData = [];
        $cartDiscountApplicable = true;

        // Track purchased quantities per variation for BxGY stock adjustment
        $purchasedVariationQuantities = [];

        // Process regular cart items
        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                
                if ($variation) {
                    // Track purchased quantities
                    if (!isset($purchasedVariationQuantities[$cartItem->variation_id])) {
                        $purchasedVariationQuantities[$cartItem->variation_id] = 0;
                    }
                    $purchasedVariationQuantities[$cartItem->variation_id] += $cartItem->qty;
                    
                    // Calculate unit price (for guests, use sell_price_inc_tax directly)
                    $unitPrice = $variation->ad_price ?? 0;
                    
                    // Apply discount (product adjustment and bxgy) on cart items (Priority: 1)
                    $appliedDiscount = null;
                    if ($discountService && !empty($discounts)) {
                        $appliedDiscount = self::calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, $cartItem, $discountService);
                        
                        if ($appliedDiscount) {
                            $appliedDiscountBucket[] = $appliedDiscount->couponName . '( id: ' . $appliedDiscount->id . ' type: ' . $appliedDiscount->discountType . ')';
                            if ($appliedDiscount->discountType === 'buyXgetY') {
                                $cartDiscountApplicable = false;
                            }
                        }
                    }

                    $discountedPrice = $unitPrice;

                    // Apply the discount that was found
                    if ($appliedDiscount && $appliedDiscount->discountType === 'productAdjustment') {
                        $discountedPrice = $discountService->calculateDiscountedPrice($unitPrice, $appliedDiscount);
                        $cartDiscountApplicable = false;
                    }

                    // Apply tax calculations after discount applied
                    $discountedPriceIncTax = $cartUtil ? $cartUtil->applyTaxCalculations($product, $variation, $discountedPrice, $taxCharges, $userState) : $discountedPrice;
                    $itemTax = $discountedPriceIncTax - $discountedPrice;

                    $sellLinesData[] = [
                        'transaction_id' => $transaction,
                        'product_id' => $product->id,
                        'variation_id' => $variation?->id,
                        'quantity' => $cartItem->qty,
                        'ordered_quantity' => $cartItem->qty,
                        'unit_price' => $discountedPrice,
                        'unit_price_before_discount' => $unitPrice,
                        'unit_price_inc_tax' => $discountedPriceIncTax,
                        'item_tax' => $itemTax,
                        'line_discount_type' => 'fixed',
                        'line_discount_amount' => $unitPrice - $discountedPrice,
                        'is_free'=>false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // ✅ NEW: Process free items from buy X get Y discounts for GUESTS
        // Group cart items by product to check total quantity across all variations
        $productQuantities = [];
        foreach ($cartItems as $cartItem) {
            if (!isset($productQuantities[$cartItem->product_id])) {
                $productQuantities[$cartItem->product_id] = 0;
            }
            $productQuantities[$cartItem->product_id] += $cartItem->qty;
        }
        
        $freeItemsToAdd = [];
        $processedDiscounts = []; // Track which discounts we've already processed
        
        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if (!$product) continue;

            $variation = $product->variations->where('id', $cartItem->variation_id)->first();
            if (!$variation) continue;

            $appliedDiscount = self::calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, $cartItem, $discountService);

            if ($appliedDiscount && $appliedDiscount->discountType === 'buyXgetY') {
                // Create unique key for this discount + product combo to avoid duplicates
                $discountKey = $appliedDiscount->id . '_' . $product->id;
                if (isset($processedDiscounts[$discountKey])) {
                    continue; // Already processed this discount for this product
                }
                
                $details = json_decode($appliedDiscount->custom_meta, true);
                $buyQuantity = $details['buy_quantity'] ?? null;
                $getYProductDetails = $details['get_y_products'] ?? [];
                $isRecursive = $details['is_recursive'] ?? false;
                
                // Use TOTAL product quantity across all variations
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

        // ✅ NEW: Add free items to sell lines for GUESTS
        // Track free items allocated per variation to properly manage stock when multiple BOGO offers apply
        $freeItemQuantities = [];
        
        if (!empty($freeItemsToAdd)) {
            $freeProductIds = collect($freeItemsToAdd)->pluck('product_id')->unique()->toArray();
            $freeProducts = self::getB2CProductsWithRelations($freeProductIds, null, null, $locationId, true);

            foreach ($freeItemsToAdd as $item) {
                $product = $freeProducts->get($item['product_id']) ?? null;
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
                
                // Distribute free items across available variations
                foreach ($variationsToCheck as $variation) {
                    if ($remainingQty <= 0) break;
                    
                    // If stock is not enabled for this product, give full quantity without checking stock
                    if ($product->enable_stock == 0) {
                        $qtyToGive = $remainingQty;
                        $remainingQty = 0;
                    } else {
                        // Calculate available stock considering:
                        // 1. Already purchased quantities in this transaction
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

                    $appliedDiscountBucket[] = $item['discount']->couponName . '( id: ' . $item['discount']->id . ' type: buyXgetY - FREE ITEM)';

                    $sellLinesData[] = [
                        'transaction_id' => $transaction,
                        'product_id' => $product->id,
                        'variation_id' => $variation->id,
                        'quantity' => $qtyToGive,
                        'ordered_quantity' => $qtyToGive,
                        'unit_price' => 0,
                        'unit_price_before_discount' => $variation->ad_price ?? 0,
                        'unit_price_inc_tax' => 0,
                        'item_tax' => 0,
                        'line_discount_type' => 'fixed',
                        'is_free'=>true,
                        'line_discount_amount' => $variation->ad_price ?? 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        return $sellLinesData;
    }

    /**
     * Calculate final total for customer orders
     * Returns array with total and shipping details matching Cart API logic
     */
    public static function calculateFinalTotal($cartItems, $products, $cartUtil, $discountService, $discounts, $appliedDiscounts, $taxCharges, $userState, $shipping_charges, $shippingType, $userId = null, $locationId = null, $brandId = null)
    {
        $cart_final_total = 0;
        $cartDiscountApplicable = true;
        $isFreeShippingApplicable = true;

        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                $unitPrice = $cartUtil->calculateUnitPrice($variation, $product, $cartItem->user_id);
                
                // Apply discount
                $appliedDiscount = $cartUtil->calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, $cartItem, $discountService, $userId, $locationId, $brandId);
                $discountedPrice = $unitPrice;
                
                if ($appliedDiscount && $appliedDiscount->discountType === 'productAdjustment') {
                    $discountedPrice = $discountService->calculateDiscountedPrice($unitPrice, $appliedDiscount);
                    $cartDiscountApplicable = false;
                } elseif ($appliedDiscount && $appliedDiscount->discountType === 'buyXgetY') {
                    $cartDiscountApplicable = false;
                }

                // Apply tax calculations
                $discountedPrice = $cartUtil->applyTaxCalculations($product, $variation, $discountedPrice, $taxCharges, $userState);
                $cart_final_total += $discountedPrice * $cartItem->qty;
            }
        }

        // Apply cart-level discounts (matches Cart API Priority: 2) - Only highest priority discount
        $cartDiscountAmount = 0;
        if ($cartDiscountApplicable) {
            // Build collection including referral discounts from applied coupons
            $allDiscounts = collect($discounts);
            foreach ($appliedDiscounts as $couponCode) {
                $discount = $cartUtil->getDiscountFromCouponCode($couponCode, $discounts, $userId, $locationId, $brandId);
                if ($discount) {
                    $existingIndex = $allDiscounts->search(function($item) use ($discount) {
                        return $item->id === $discount->id;
                    });
                    if ($existingIndex !== false) {
                        $allDiscounts->put($existingIndex, $discount);
                    } else {
                        $allDiscounts->push($discount);
                    }
                }
            }
            
            // Find the highest priority applicable cart discount
            $selectedCartDiscount = null;
            foreach ($allDiscounts as $discount) {
                if ($discount->discountType === 'cartAdjustment' && 
                    $discountService->isDiscountApplicable($discount, $cartItems, $products, 0, $appliedDiscounts)) {
                    // Since discounts are already sorted by setPriority DESC, take the first match
                    $selectedCartDiscount = $discount;
                    break; // Only apply the highest priority discount
                }
            }
            
            // Apply only the selected highest priority discount
            if ($selectedCartDiscount) {
                $discountAmount = $discountService->calculateCartDiscount($cart_final_total, $selectedCartDiscount, $cartItems, $products);
                if ($discountAmount > 0) {
                    $cartDiscountAmount = $discountAmount;
                    $isFreeShippingApplicable = false; // Cart discount prevents free shipping
                }
            }
            $cart_final_total = max(0, $cart_final_total - $cartDiscountAmount);
        }

        // Apply free shipping discount (matches Cart API Priority: 3)
        $freeShippingDiscountAmount = -1; // -1 means no discount applied
        $actualShippingCharges = $shipping_charges;
        
        // If user will pickup then no need to apply free shipping discount
        if ($shippingType === "PICKUP") {
            $actualShippingCharges = 0.00;
            $isFreeShippingApplicable = false;
        }
        
        // Apply free shipping discount if no cart coupon is applied
        if ($cartDiscountApplicable && $isFreeShippingApplicable) {
            // Build collection including referral discounts from applied coupons
            $allDiscounts = collect($discounts);
            foreach ($appliedDiscounts as $couponCode) {
                $discount = $cartUtil->getDiscountFromCouponCode($couponCode, $discounts, $userId, $locationId, $brandId);
                if ($discount) {
                    $existingIndex = $allDiscounts->search(function($item) use ($discount) {
                        return $item->id === $discount->id;
                    });
                    if ($existingIndex !== false) {
                        $allDiscounts->put($existingIndex, $discount);
                    } else {
                        $allDiscounts->push($discount);
                    }
                }
            }
            
            $freeShippingCount = 0;
            foreach ($allDiscounts as $discount) {
                if ($discount->discountType === 'freeShipping') {
                    $freeShippingCount++;
                    
                    $isApplicable = $discountService->isDiscountApplicable($discount, $cartItems, $products, 0, $appliedDiscounts);                    
                    if ($isApplicable) {
                        // Pass the actual shipping charge to the discount service
                        $freeShippingDiscountAmount = $discountService->freeShippingDiscount($cart_final_total, $discount, $cartItems, $products, $shipping_charges);                        
                        if ($freeShippingDiscountAmount > -1) {
                            // Apply the discount to shipping charges
                            $actualShippingCharges = max(0, $shipping_charges - $freeShippingDiscountAmount);
                            break; // Apply first matching free shipping discount
                        }
                    }
                }
            }
        }

        return [
            'final_total' => $cart_final_total + $actualShippingCharges,
            'subtotal' => $cart_final_total,
            "discount_type" =>'fixed',
            'discount_amount' => $cartDiscountAmount,
            'shipping_charges' => $actualShippingCharges,
            'free_shipping_discount' => $freeShippingDiscountAmount,
            'cart_discount_amount' => $cartDiscountAmount
        ];
    }

    /**
     * Calculate final total for guest orders
     */
    public static function calculateGuestFinalTotal($cartItems, $products, $shipping_charges, $shippingType)
    {
        $total = 0;

        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                $unitPrice = $variation?->ad_price ?? 0;
                $total += $unitPrice * $cartItem->qty;
            }
        }

        if ($shippingType === "PICKUP") {
            $shipping_charges = 0.00;
        }

        return $total + $shipping_charges;
    }

    /**
     * Calculate enhanced final total for guest orders with discount and tax calculations
     * Returns array with total and shipping details matching Cart API logic
     */
    public static function calculateEnhancedGuestFinalTotal($cartItems, $products, $discountService, $discounts, $appliedDiscounts, $taxCharges, $userState, $shipping_charges, $shippingType, $cartUtil)
    {
        $cart_final_total = 0;
        $cartDiscountApplicable = true;
        $isFreeShippingApplicable = true;

        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                $unitPrice = $variation?->ad_price ?? 0;
                
                // Apply discount
                $appliedDiscount = self::calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, $cartItem, $discountService);
                $discountedPrice = $unitPrice;
                
                if ($appliedDiscount && $appliedDiscount->discountType === 'productAdjustment') {
                    $discountedPrice = $discountService->calculateDiscountedPrice($unitPrice, $appliedDiscount);
                    $cartDiscountApplicable = false;
                } elseif ($appliedDiscount && $appliedDiscount->discountType === 'buyXgetY') {
                    $cartDiscountApplicable = false;
                }

                // Apply tax calculations
                $discountedPrice = $cartUtil->applyTaxCalculations($product, $variation, $discountedPrice, $taxCharges, $userState);
                $cart_final_total += $discountedPrice * $cartItem->qty;
            }
        }

        // Apply cart-level discounts (matches Cart API Priority: 2) - Only highest priority discount
        $cartDiscountAmount = 0;
        if ($cartDiscountApplicable) {
            // Find the highest priority applicable cart discount
            $selectedCartDiscount = null;
            foreach ($discounts as $discount) {
                if ($discount->discountType === 'cartAdjustment' && 
                    $discountService->isDiscountApplicable($discount, $cartItems, $products, 0, $appliedDiscounts)) {
                    // Since discounts are already sorted by setPriority DESC, take the first match
                    $selectedCartDiscount = $discount;
                    break; // Only apply the highest priority discount
                }
            }
            
            // Apply only the selected highest priority discount
            if ($selectedCartDiscount) {
                $discountAmount = $discountService->calculateCartDiscount($cart_final_total, $selectedCartDiscount, $cartItems, $products);
                if ($discountAmount > 0) {
                    $cartDiscountAmount = $discountAmount;
                    $isFreeShippingApplicable = false; // Cart discount prevents free shipping
                }
            }
            $cart_final_total = max(0, $cart_final_total - $cartDiscountAmount);
        }

        // Apply free shipping discount (matches Cart API Priority: 3)
        $freeShippingDiscountAmount = -1; // -1 means no discount applied
        $actualShippingCharges = $shipping_charges;
        
        // If user will pickup then no need to apply free shipping discount
        if ($shippingType === "PICKUP") {
            $actualShippingCharges = 0.00;
            $isFreeShippingApplicable = false;
        }
        
        // Apply free shipping discount if no cart coupon is applied
        if ($cartDiscountApplicable && $isFreeShippingApplicable) {
            $freeShippingCount = 0;
            foreach ($discounts as $discount) {
                if ($discount->discountType === 'freeShipping') {
                    $freeShippingCount++;
                    $isApplicable = $discountService->isDiscountApplicable($discount, $cartItems, $products, 0, $appliedDiscounts);                    
                    if ($isApplicable) {
                        // Pass the actual shipping charge to the discount service
                        $freeShippingDiscountAmount = $discountService->freeShippingDiscount($cart_final_total, $discount, $cartItems, $products, $shipping_charges);
                        if ($freeShippingDiscountAmount > -1) {
                            // Apply the discount to shipping charges
                            $actualShippingCharges = max(0, $shipping_charges - $freeShippingDiscountAmount);
                            break; // Apply first matching free shipping discount
                        }
                    }
                }
            }
        }

        return [
            'final_total' => $cart_final_total + $actualShippingCharges,
            'subtotal' => $cart_final_total,
            "discount_type" => 'fixed',
            'discount_amount' => $cartDiscountAmount,
            'shipping_charges' => $actualShippingCharges,
            'free_shipping_discount' => $freeShippingDiscountAmount,
            'cart_discount_amount' => $cartDiscountAmount
        ];
    }

    /**
     * Calculate total before tax for customer orders
     */
    public static function calculateTotalBeforeTax($sellLinesData)
    {
        $total = 0;
        foreach ($sellLinesData as $sellLine) {
            $total += $sellLine['unit_price'] * $sellLine['quantity'];
        }
        return $total;
    }

    /**
     * Calculate total before tax for guest orders
     */
    public static function calculateGuestTotalBeforeTax($cartItems, $products)
    {
        $total = 0;
        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if ($product) {
                $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                $unitPrice = $variation?->ad_price ?? 0;
                $total += $unitPrice * $cartItem->qty;
            }
        }
        return $total;
    }

    /**
     * Process payment for customer orders using existing PaymentOrderController
     */
    public static function processPayment($nonce, $payedAmount, $newValue, $userId, $cart, $transaction, $paymentOrderController)
    {
        if (!$nonce) {
            return ['status' => false, 'message' => 'Token is Missing'];
        }

        $billingInfo = [
            'first_name' => $cart->billing_first_name,
            'last_name' => $cart->billing_last_name,
            'company' => $cart->billing_company,
            'address1' => $cart->billing_address1,
            'address2' => $cart->billing_address2,
            'city' => $cart->billing_city,
            'state' => $cart->billing_state,
            'zip' => $cart->billing_zip,
            'country' => $cart->billing_country,
            'phone' => $cart->billing_phone,
            'email' => $cart->billing_email
        ];

        $shippingInfo = [
            'shipping_first_name' => $cart->shipping_first_name,
            'shipping_last_name' => $cart->shipping_last_name,
            'shipping_company' => $cart->shipping_company,
            'shipping_address1' => $cart->shipping_address1,
            'shipping_address2' => $cart->shipping_address2,
            'shipping_city' => $cart->shipping_city,
            'shipping_state' => $cart->shipping_state,
            'shipping_zip' => $cart->shipping_zip,
            'shipping_country' => $cart->shipping_country,
            'shipping_email' => $cart->shipping_email
        ];

        if (config('app.gatewayType') == 'authorizenet') {
            $payByAuthorize = $paymentOrderController->payByAuthorize($payedAmount, $nonce, $newValue, $userId, $cart->billing_email, $billingInfo);
            if ($payByAuthorize['status'] === true) {
                Log::info('Authorize B2C Payment Success');
                return ['status' => true, 'transaction_id' => $payByAuthorize['transaction_id']];
            } else {
                return ['status' => false, 'message' => $payByAuthorize['message']];
            }
        } else {
            // Log payment initiation
            Log::info('B2C NMI Payment Initiated (Customer)', [
                'amount' => $payedAmount,
                'invoice' => $newValue,
                'user_id' => $userId,
                'billing_info' => $billingInfo,
                'shipping_info' => $shippingInfo
            ]);

            $saleData = $paymentOrderController->doSale($payedAmount, $nonce, $billingInfo, $shippingInfo, $newValue);
            $paymentResult = $paymentOrderController->_doRequest($saleData);
            
            // Log payment result
            Log::info('B2C NMI Payment Result (Customer)', [
                'status' => $paymentResult['status'],
                'response_text' => $paymentResult['responsetext'],
                'transaction_id' => $paymentResult['transactionid'] ?? null,
                'invoice' => $newValue
            ]);

            if ($paymentResult['status'] === false || $paymentResult['responsetext'] !== 'Approved') {
                return ['status' => false, 'message' => $paymentResult['responsetext']];
            }
            return ['status' => true, 'transaction_id' => $paymentResult['transactionid']];
        }
    }

    /**
     * Process payment for guest orders using existing PaymentOrderController
     */
    public static function processGuestPayment($nonce, $payedAmount, $newValue, $billingInfo, $shippingInfo, $transaction, $paymentOrderController)
    {
        if (!$nonce) {
            return ['status' => false, 'message' => 'Token is Missing'];
        }

        if (config('app.gatewayType') == 'authorizenet') {
            $payByAuthorize = $paymentOrderController->payByAuthorize($payedAmount, $nonce, $newValue, 'guest', $billingInfo['email'], $billingInfo);
            if ($payByAuthorize['status'] === true) {
                Log::info('Authorize B2C Guest Payment Success');
                return ['status' => true, 'transaction_id' => $payByAuthorize['transaction_id']];
            } else {
                return ['status' => false, 'message' => $payByAuthorize['message']];
            }
        } else {
            // Log guest payment initiation
            Log::info('B2C NMI Payment Initiated (Guest)', [
                'amount' => $payedAmount,
                'invoice' => $newValue,
                'billing_info' => $billingInfo,
                'shipping_info' => $shippingInfo
            ]);

            $saleData = $paymentOrderController->doSale($payedAmount, $nonce, $billingInfo, $shippingInfo, $newValue);
            $paymentResult = $paymentOrderController->_doRequest($saleData);
            
            // Log guest payment result
            Log::info('B2C NMI Payment Result (Guest)', [
                'status' => $paymentResult['status'],
                'response_text' => $paymentResult['responsetext'],
                'transaction_id' => $paymentResult['transactionid'] ?? null,
                'invoice' => $newValue
            ]);

            if ($paymentResult['status'] === false || $paymentResult['responsetext'] !== 'Approved') {
                return ['status' => false, 'message' => $paymentResult['responsetext']];
            }
            return ['status' => true, 'transaction_id' => $paymentResult['transactionid']];
        }
    }

    /**
     * Insert payment record
     */
    public static function insertPayment($transaction, $business_id, $payedAmount, $card, $newValue, $final_total)
    {
        $paymentData = [
            'transaction_id' => $transaction,
            'business_id' => $business_id,
            'amount' => $payedAmount,
            'method' => $card ? "custom_pay_1" : 'cash',
            'card_type' => 'credit',
            'payment_ref_no' => $newValue,
            'transaction_no' => 'B2C_' . time(),
            'paid_on' => now(),
            'payment_for' => $transaction,
            'gateway' => config('app.gatewayType') == 'authorizenet' ? 'AUTHORIZENET' : 'NMI',
            'note' => "B2C Order: {$final_total} was final total",
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('transaction_payments')->insert($paymentData);
    }

    /**
     * Calculate high priority discount for cart items (matches CartController logic)
     */
    public static function calculateHighPriorityDiscount($appliedDiscounts, $discounts, $product, $variation, $cartItem, $discountService)
    {
        // High priority discount (product adjustment and bxgy)
        $eligibleDiscounts = collect($discounts)
            ->filter(function ($discount) use ($discountService, $product, $variation, $cartItem, $appliedDiscounts) {
                return in_array($discount->discountType, ['productAdjustment', 'buyXgetY']) &&
                    $discountService->isDiscountApplicable($discount, $product, $variation, $cartItem->qty, $appliedDiscounts);
            })
            ->sortByDesc('setPriority')
            ->values();

        // Pick the discount with the highest priority (highest setPriority number)
        return $eligibleDiscounts->first();
    }

}