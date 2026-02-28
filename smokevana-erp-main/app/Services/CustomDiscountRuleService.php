<?php

namespace App\Services;

use App\Brands;
use App\Models\CustomDiscount;
use App\Models\EcomReferalProgram;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomDiscountRuleService
{
    /**
     * Get all active discounts
     * @param mixed $contact
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveDiscounts($contact, $locationId = null, $brandId = null)
    {
        $discounts = CustomDiscount::active()
            ->valid()
            ->when(
                $locationId !== 'all' && $locationId !== null &&
                    $brandId !== null && $brandId !== 'all',
                function ($q) use ($locationId, $brandId) {
                    $q->where('location_id', $locationId)
                        ->where(function ($q2) use ($brandId) {
                            $q2->whereRaw('JSON_CONTAINS(brand_id, ?)', [json_encode((string) $brandId)])
                                ->orWhereRaw('JSON_CONTAINS(brand_id, ?)', [json_encode('all')]);
                        });
                }
            )
            ->orderBy('setPriority', 'desc')
            ->get();

        return $discounts;
    }

    /**
     * Check if the discount is applicable for the quantity
     * @param mixed $discount
     * @param mixed $quantity
     * @return bool
     */
    private function checkDiscountQuantity($discount, $quantity){
        if ($discount->minBuyQty && $quantity < $discount->minBuyQty) {
            return false;
        }
        if ($discount->maxBuyQty && $quantity > $discount->maxBuyQty) {
            return false;
        }
        return true;
    }

    /**
     * Check if the discount is applicable for the coupon code
     * @param mixed $discount
     * @param mixed $appliedDiscounts
     * @return bool
     */
    private function checkCouponCode($discount, $appliedDiscounts = [])
    {
        // If discount has no coupon code, it's automatically applicable
        if (empty($discount->couponCode)) {
            return true;
        }

        // If discount has a coupon code, check if it's in the applied discounts
        if (!empty($appliedDiscounts) && is_array($appliedDiscounts)) {
            foreach ($appliedDiscounts as $appliedDiscount) {
                if (isset($appliedDiscount) && $appliedDiscount === $discount->couponCode) {
                    return true;
                }
            }
        }

        // Allow referral coupons sourced from request context
        $allowedReferalCoupons = request()->attributes->get('customer_referal_coupons', []);
        if (!empty($allowedReferalCoupons) && in_array($discount->couponCode, $allowedReferalCoupons, true)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the discount is applicable for the category, brand, product, variation
     * @param mixed $discount
     * @param mixed $product
     * @param mixed $variation
     * @param mixed $quantity
     * @return bool
     */
    private function checkFilters($discount, $product, $variation, $quantity){
        $filter = $discount->filter ?? [];
        if(empty($filter) || $filter == null || $filter == '' || $filter == 'null'){
            if($this->checkDiscountQuantity($discount, $quantity)){
                return true;
            }
            return false;
        }
        $filter = json_decode($filter, true);
        
        // Check if quantity requirement is met first
        if(!$this->checkDiscountQuantity($discount, $quantity)){
            return false;
        }

        // Flags to track which filter types exist and match
        $hasProductLevelFilters = false;
        $productLevelMatches = false;
        $hasVariationFilters = false;
        $variationMatches = false;

        // Check category filters
        if(isset($filter['categories']) || isset($filter['not_categories'])){
            $hasProductLevelFilters = true;
            $productCategoryIds = $product->webcategories->pluck('id')->toArray() ?? $product->categories->pluck('id')->toArray();
            
            if(isset($filter['categories'])){
                if($filter['categories']['opration'] == 'in'){
                    if(array_intersect($productCategoryIds, $filter['categories']['ids'])){
                        $productLevelMatches = true;
                    }
                }else{
                    if(!array_intersect($productCategoryIds, $filter['categories']['ids'])){
                        $productLevelMatches = true;
                    }
                }
            }
            
            if(isset($filter['not_categories'])){
                if($filter['not_categories']['opration'] == 'in'){
                    if(array_intersect($productCategoryIds, $filter['not_categories']['ids'])){
                        $productLevelMatches = true;
                    }
                }else{
                    if(!array_intersect($productCategoryIds, $filter['not_categories']['ids'])){
                        $productLevelMatches = true;
                    }
                }
            }
        }

        // Check brand filters
        if(isset($filter['brand']) || isset($filter['not_brand'])){
            $hasProductLevelFilters = true;
            
            if(isset($filter['brand'])){
                if($filter['brand']['opration'] == 'in'){
                    if(isset($product->brand->id) && in_array($product->brand->id, $filter['brand']['ids'])){
                        $productLevelMatches = true;
                    }
                }else{
                    if(isset($product->brand->id) && !in_array($product->brand->id, $filter['brand']['ids'])){
                        $productLevelMatches = true;
                    }
                }
            }
            
            if(isset($filter['not_brand'])){
                if($filter['not_brand']['opration'] == 'in'){
                    if(isset($product->brand->id) && in_array($product->brand->id, $filter['not_brand']['ids'])){
                        $productLevelMatches = true;
                    }
                }else{
                    if(isset($product->brand->id) && !in_array($product->brand->id, $filter['not_brand']['ids'])){
                        $productLevelMatches = true;
                    }
                }
            }
        }

        // Check product filters
        if(isset($filter['product_ids']) || isset($filter['not_product_ids'])){
            $hasProductLevelFilters = true;
            
            if(isset($filter['product_ids'])){
                if($filter['product_ids']['opration'] == 'in'){
                    if(in_array($product->id, $filter['product_ids']['ids'])){
                        $productLevelMatches = true;
                    }
                }else{
                    if(!in_array($product->id, $filter['product_ids']['ids'])){
                        $productLevelMatches = true;
                    }
                }
            }
            
            if(isset($filter['not_product_ids'])){
                if($filter['not_product_ids']['opration'] == 'in'){
                    if(in_array($product->id, $filter['not_product_ids']['ids'])){
                        $productLevelMatches = true;
                    }
                }else{
                    if(!in_array($product->id, $filter['not_product_ids']['ids'])){
                        $productLevelMatches = true;
                    }
                }
            }
        }

        // Check variation filters (more specific than product filters)
        if(isset($filter['variation_ids']) || isset($filter['not_variation_ids'])){
            $hasVariationFilters = true;
            
            if(isset($filter['variation_ids'])){
                if($filter['variation_ids']['opration'] == 'in'){
                    if(in_array($variation->id, $filter['variation_ids']['ids'])){
                        $variationMatches = true;
                    }
                }else{
                    if(!in_array($variation->id, $filter['variation_ids']['ids'])){
                        $variationMatches = true;
                    }
                }
            }
            
            if(isset($filter['not_variation_ids'])){
                if($filter['not_variation_ids']['opration'] == 'in'){
                    if(in_array($variation->id, $filter['not_variation_ids']['ids'])){
                        $variationMatches = true;
                    }
                }else{
                    if(!in_array($variation->id, $filter['not_variation_ids']['ids'])){
                        $variationMatches = true;
                    }
                }
            }
        }

        // Apply logic:
        // 1. If variation filters exist, they take priority (must match)
        // 2. If only product-level filters exist, product match is sufficient
        // 3. If both exist, BOTH must match (AND logic)
        
        if($hasVariationFilters && $hasProductLevelFilters){
            // Both types exist: both must match
            return $variationMatches && $productLevelMatches;
        } elseif($hasVariationFilters){
            // Only variation filters: variation must match
            return $variationMatches;
        } elseif($hasProductLevelFilters){
            // Only product-level filters: product must match (applies to all variations)
            return $productLevelMatches;
        }

        // No filters matched
        return false;
    }

    /**
     * Check if the discount is applicable for the cart items
     * @param mixed $discount
     * @param mixed $cartItem
     * @param mixed $productsInfoOfCartItems
     * @return bool
     */
    private function checkCartAdjustment($discount, $cartItem = [], $productsInfoOfCartItems = []){
        $filters = !empty($discount->filter) ? json_decode($discount->filter, true) : [];
        // filter by category
        if(empty($filters) || $filters == null || $filters == '' || $filters == 'null'){
            return true;
        }
        if(isset($filters['not_categories'])){
             // Get all category IDs from cart items' products
             $cartProductCategoryIds = [];
             foreach($productsInfoOfCartItems as $product){
                 if(!$product || !is_object($product)) continue; // Skip invalid products
                 if(isset($product->webcategories) && !empty($product->webcategories)){
                     foreach($product->webcategories as $category){
                         $cartProductCategoryIds[] = $category->id;
                     }
                 }
             }
             if($filters['not_categories']['opration'] == 'in'){
                 // Check if any category from filter exists in cart products
                 if(array_intersect($cartProductCategoryIds, $filters['not_categories']['ids'])){
                     return true;
                 }
             } else {
                 if(!array_intersect($cartProductCategoryIds, $filters['not_categories']['ids'])){
                     return true;
                 }
             }
        }
        if(isset($filters['categories'])){
             // Get all category IDs from cart items' products
             $cartProductCategoryIds = [];
             foreach($productsInfoOfCartItems as $product){
                 if(!$product || !is_object($product)) continue; // Skip invalid products
                 if(isset($product->webcategories) && !empty($product->webcategories)){
                     foreach($product->webcategories as $category){
                         $cartProductCategoryIds[] = $category->id;
                     }
                 }
             }
            if($filters['categories']['opration'] == 'in'){
                // Check if any category from filter exists in cart products
                if(array_intersect($cartProductCategoryIds, $filters['categories']['ids'])){
                    return true;
                }
            } else {
                if(!array_intersect($cartProductCategoryIds, $filters['categories']['ids'])){
                    return true;
                }
            }
        }
        // filter by brand
        if(isset($filters['not_brand'])){
            $cartProductBrandIds = [];
            foreach($productsInfoOfCartItems as $product){
                if(!$product || !is_object($product)) continue; // Skip invalid products
                if(isset($product->brand) && !empty($product->brand)){
                    $cartProductBrandIds[] = $product->brand->id;
                }
            }
            if($filters['not_brand']['opration'] == 'in'){
                if(array_intersect($cartProductBrandIds, $filters['not_brand']['ids'])){
                    return true;
                }
            } else {
                if(!array_intersect($cartProductBrandIds, $filters['not_brand']['ids'])){
                    return true;
                }
            }
        }
        if(isset($filters['brand'])){
            $cartProductBrandIds = [];
            foreach($productsInfoOfCartItems as $product){
                if(!$product || !is_object($product)) continue; // Skip invalid products
                if(isset($product->brand) && !empty($product->brand)){
                    $cartProductBrandIds[] = $product->brand->id;
                }
            }
            if($filters['brand']['opration'] == 'in'){
                if(array_intersect($cartProductBrandIds, $filters['brand']['ids'])){
                    return true;
                }
            } else {
                if(!array_intersect($cartProductBrandIds, $filters['brand']['ids'])){
                    return true;
                }
            }
        }
        // filter by product
        if(isset($filters['not_product_ids'])){
            $cartProductIds = [];
            foreach($productsInfoOfCartItems as $product){
                if(!$product || !is_object($product)) continue; // Skip invalid products
                $cartProductIds[] = $product->id;
            }
            if($filters['not_product_ids']['opration'] == 'in'){
                if(array_intersect($cartProductIds, $filters['not_product_ids']['ids'])){
                    return true;
                }
            } else {
                if(!array_intersect($cartProductIds, $filters['not_product_ids']['ids'])){
                    return true;
                }
            }
        }
        if(isset($filters['product_ids'])){
            $cartProductIds = [];
            foreach($productsInfoOfCartItems as $product){
                if(!$product || !is_object($product)) continue; // Skip invalid products
                $cartProductIds[] = $product->id;
            }
            if($filters['product_ids']['opration'] == 'in'){
                if(array_intersect($cartProductIds, $filters['product_ids']['ids'])){
                    return true;
                }
            } else {
                if(!array_intersect($cartProductIds, $filters['product_ids']['ids'])){
                    return true;
                }
            }
        }
        // filter by variation
        if(isset($filters['not_variation_ids'])){
            $cartProductVariationIds = [];
            foreach($productsInfoOfCartItems as $product){
                if(!$product || !is_object($product)) continue; // Skip invalid products
                $cartProductVariationIds[] = $product->variations->pluck('id')->toArray();
            }
            if($filters['not_variation_ids']['opration'] == 'in'){
                if(array_intersect($cartProductVariationIds, $filters['not_variation_ids']['ids'])){
                    return true;
                }
            } else {
                if(!array_intersect($cartProductVariationIds, $filters['not_variation_ids']['ids'])){
                    return true;
                }
            }
        }
        if(isset($filters['variation_ids'])){
            $cartProductVariationIds = [];
            foreach($productsInfoOfCartItems as $product){
                if(!$product || !is_object($product)) continue; // Skip invalid products
                $cartProductVariationIds[] = $product->variations->pluck('id')->toArray();
            }
            if($filters['variation_ids']['opration'] == 'in'){
                if(array_intersect($cartProductVariationIds, $filters['variation_ids']['ids'])){
                    return true;
                }
            } else {
                if(!array_intersect($cartProductVariationIds, $filters['variation_ids']['ids'])){
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * Check if the discount is applicable for the customer
     * @param mixed $discount
     * @param mixed $customer
     * @return bool
     */
    private function checkCustomerRules($discount, $customer){
        $rulesOnCustomer = $discount->rulesOnCustomer ?? null;
        if(!$rulesOnCustomer){
            return true;
        }

        if(is_string($rulesOnCustomer)){
            $rulesOnCustomer = json_decode($rulesOnCustomer, true);
        }

        // Check if customer is eligible based on group or list
        $isEligible = false;
        if($rulesOnCustomer['applyOn'] == 'customer-group'){
            $isEligible = is_array($rulesOnCustomer['values']) && in_array($customer->customer_group_id, $rulesOnCustomer['values']);
        } else if($rulesOnCustomer['applyOn'] == 'customer-list'){
            $isEligible = in_array($customer->id, $rulesOnCustomer['values']);
        } else if($rulesOnCustomer['applyOn'] == 'all'){
            $isEligible = true;
        }

        if(!$isEligible){
            return false;
        }
        // mean no restriction on customer
        if($rulesOnCustomer['applyOn'] == 'all' && $rulesOnCustomer['on-first-order'] == false && $rulesOnCustomer['on-last-order-value'] == false){
            return true;
        }

        // Check first order restriction - now works correctly because transaction is created AFTER discount validation
        if($rulesOnCustomer['on-first-order']){
            // Get is_order parameter from request attributes (set by order controllers)
            $isOrder = request()->attributes->get('is_order', false);
            
            // Count previous transactions
            $previousOrders = DB::select('SELECT COUNT(*) as count FROM `transactions` WHERE `type` = "sales_order" AND `contact_id` = ? AND `status` NOT IN (?,?)', [$customer->id, 'cancelled','void']);
            $transactionCount = $previousOrders[0]->count;
            
            // If is_order is true, check if transaction count > 1 (not first order)
            // If is_order is false, check if transaction count > 0 (has previous orders)
            if($isOrder){
                if($transactionCount > 1){
                    return false;
                }
            } else {
                if($transactionCount > 0){
                    return false;
                }
            }
        }

        // Check minimum order value restriction
        if($rulesOnCustomer['on-last-order-value']){
            $lastOrderValue = DB::select('SELECT SUM(`final_total`) AS `total` FROM `transactions` WHERE `type` = "sales_order" AND `contact_id` = ? AND `status` NOT IN (?)', [$customer->id, 'cancelled']);
            if($lastOrderValue[0]->total < $rulesOnCustomer['last-order-value']){
                return false;
            }
        }

        return true;
    }
    /**
     * Check if the discount is applicable for the product, variation, quantity
     * @param mixed $discount
     * @param mixed $product
     * @param mixed $variation
     * @param mixed $quantity
     * @param mixed $appliedDiscounts
     * @return bool
     */
    public function isDiscountApplicable($discount, $product, $variation, $quantity, $appliedDiscounts = [])
    {
        // First check if coupon code is required and valid || check in referal program customer have using request() 
        if (!$this->checkCouponCode($discount, $appliedDiscounts)) {
            return false;
        }
        // check on customer 
        $rulesOnCustomer = $discount->rulesOnCustomer ?? null;
        if($rulesOnCustomer){
            if(is_string($rulesOnCustomer)){
                $rulesOnCustomer = json_decode($rulesOnCustomer, true);
            }
            if($rulesOnCustomer['applyOn'] == 'customer-group' || $rulesOnCustomer['applyOn'] == 'customer-list' || $rulesOnCustomer['applyOn'] == 'all'){
                if(request()->attributes->get('is_guest_request', false)){
                    if(request()->attributes->get('is_guest_request', false) && $rulesOnCustomer['on-first-order'] == true || request()->attributes->get('is_guest_request', false) && $rulesOnCustomer['on-last-order-value'] == true){
                       return false;
                    }
                    $discountType = $discount->discountType;
                    if ($discountType === 'productAdjustment') {
                        if($this->checkFilters($discount, $product, $variation, $quantity)){
                            return true;
                        }
                    }
                    if($discountType == 'cartAdjustment'){
                        //passing the cart item in the product variable
                        if($this->checkCartAdjustment($discount, $product, $variation)){
                            return true;
                        }
                    }

                    if($discountType == 'freeShipping'){
                        //passing the cart item in the product variable
                        if($this->checkCartAdjustment($discount, $product, $variation)){
                            return true;
                        }
                    }
                    
                    if($discountType == 'buyXgetX'){
                        if($this->checkFilters($discount, $product, $variation, $quantity)){
                            return true;
                        }
                    }
                    if($discountType == 'buyXgetY'){
                        if($this->checkFilters($discount, $product, $variation, $quantity)){
                            return true;
                        }
                    }
                } else {
                    // Check customer rules for authenticated users
                    $customer = Auth::guard('api')->user();
                    if($customer && !$this->checkCustomerRules($discount, $customer)){
                        return false;
                    }
                }
            }
        }

        $discountType = $discount->discountType;
        if ($discountType === 'productAdjustment') {
            if($this->checkFilters($discount, $product, $variation, $quantity)){
                return true;
            }
        }
        if($discountType == 'cartAdjustment'){
            //passing the cart item in the product variable
            if($this->checkCartAdjustment($discount, $product, $variation)){
                return true;
            }
        }

        if($discountType == 'freeShipping'){
            //passing the cart item in the product variable
            if($this->checkCartAdjustment($discount, $product, $variation)){
                return true;
            }
        }
        
        if($discountType == 'buyXgetX'){
            if($this->checkFilters($discount, $product, $variation, $quantity)){
                return true;
            }
        }
        if($discountType == 'buyXgetY'){
            if($this->checkFilters($discount, $product, $variation, $quantity)){
                return true;
            }
        }
        return false;
    }

    /**
     * Calculate the discounted price
     * @param mixed $price
     * @param mixed $discount
     * @return float
     */
    public function calculateDiscountedPrice($price, $discount)
    {
        $discountType = $discount->discount;
        switch ($discountType) {
            case 'percentageDiscount':
                return round($price * (1 - ($discount->discountValue / 100)), 2);
            case 'fixedDiscount':
                return max(0, $price - $discount->discountValue);
            case 'fixedPricePerItem':
                return $discount->discountValue;
            case 'free':
                return 0;
            default:
                return $price;
        }
    }

    /**
     * Calculate the total value of cart items that match the discount filter
     * @param mixed $cartItems
     * @param mixed $products
     * @param mixed $discount
     * @param float $cartTotal
     * @return float
     */
    private function calculateFilteredCartTotal($cartItems, $products, $discount, $cartTotal)
    {
        $filters = !empty($discount->filter) ? json_decode($discount->filter, true) : [];
        
        // If no filter is set, return the full cart total
        if(empty($filters) || $filters == null || $filters == '' || $filters == 'null'){
            return $cartTotal;
        }

        $filteredTotal = 0;

        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if (!$product) continue;

            $variation = $product->variations->where('id', $cartItem->variation_id)->first();
            if (!$variation) continue;

            $itemMatches = false;

            // Check category filter (operation 'in' means item must be in these categories)
            if(isset($filters['categories']) && $filters['categories']['opration'] == 'in'){
                $productCategoryIds = $product->webcategories->pluck('id')->toArray() ?? $product->categories->pluck('id')->toArray() ?? [];
                if(array_intersect($productCategoryIds, $filters['categories']['ids'])){
                    $itemMatches = true;
                }
            }

            // Check brand filter (operation 'in' means item must be in these brands)
            if(isset($filters['brand']) && $filters['brand']['opration'] == 'in'){
                if(isset($product->brand) && !empty($product->brand) && in_array($product->brand->id, $filters['brand']['ids'])){
                    $itemMatches = true;
                }
            }

            // Check product filter (operation 'in' means item must be in these products)
            if(isset($filters['product_ids']) && $filters['product_ids']['opration'] == 'in'){
                if(in_array($product->id, $filters['product_ids']['ids'])){
                    $itemMatches = true;
                }
            }

            // Check variation filter (operation 'in' means item must be in these variations)
            if(isset($filters['variation_ids']) && $filters['variation_ids']['opration'] == 'in'){
                if(in_array($variation->id, $filters['variation_ids']['ids'])){
                    $itemMatches = true;
                }
            }

            // If item matches any of the filters, add its value to filtered total
            // Note: If multiple filters are present, item matches if it matches ANY of them (OR logic)
            if($itemMatches){
                // Calculate item price (use ad_price or similar pricing field)
                $itemPrice = $variation->ad_price ?? 0;
                $filteredTotal += $itemPrice * $cartItem->qty;
            }
        }

        return $filteredTotal;
    }

    /**
     * Calculate the cart discount
     * @param mixed $cartTotal
     * @param mixed $discount
     * @param mixed $cartItems (optional) - required when discount has filters
     * @param mixed $products (optional) - required when discount has filters
     * @return float
     */
    public function calculateCartDiscount($cartTotal, $discount, $cartItems = null, $products = null)
    {
        if ($discount->discountType !== 'cartAdjustment') {
            return 0;
        }
        // Check minimum order value
        $rulesOnCart = $discount->rulesOnCart ?? [];
        
        // Check if rulesOnCart is already an array or needs to be decoded
        if (is_string($rulesOnCart)) {
            $rulesOnCart = json_decode($rulesOnCart, true);
        }
        
        // If discount has filters, check minimum order value against filtered items only
        $totalToCheck = $cartTotal;
        if (!empty($discount->filter) && $cartItems !== null && $products !== null) {
            $totalToCheck = $this->calculateFilteredCartTotal($cartItems, $products, $discount, $cartTotal);
        }
        
        // Only check minimum order value if it's actually set
        if (isset($rulesOnCart['minOrderValue']) && $totalToCheck < $rulesOnCart['minOrderValue']) {
            return 0;
        }

        // Calculate percentage discount or fixed amount (still based on full cart total)
        $discountAmount = 0;
        if($discount->discount == 'percentageDiscount'){
            $discountAmount = ($cartTotal * $discount->discountValue) / 100;
        } else if($discount->discount == 'fixedDiscount'){
            $discountAmount = $discount->discountValue;
        }

        // Apply maximum discount cap
        $maxDiscountAmount = $rulesOnCart['maxDiscountAmount'] ?? null;
        if ($maxDiscountAmount && $discountAmount > $maxDiscountAmount) {
            $discountAmount = $maxDiscountAmount;
        }

        return round($discountAmount, 2);
    }
    /**
     * Calculate the free shipping discount
     * @param mixed $cartTotal
     * @param mixed $discount
     * @param mixed $cartItems (optional) - required when discount has filters
     * @param mixed $products (optional) - required when discount has filters
     * @param float $baseShippingCharge (optional) - base shipping charge, defaults to 15
     * @return float - Returns the discount amount to be subtracted from shipping (15 for 100% free, -1 if not applicable)
     */
    public function freeShippingDiscount($cartTotal, $discount, $cartItems = null, $products = null, $baseShippingCharge = 15.00)
    {
        if ($discount->discountType !== 'freeShipping') {
            return -1;
        }
        // check if the cart total is greater than the minimum order value
        $rulesOnCart = $discount->rulesOnCart ?? [];
        
        // Check if rulesOnCart is already an array or needs to be decoded
        if (is_string($rulesOnCart)) {
            $rulesOnCart = json_decode($rulesOnCart, true);
        }
        
        // If discount has filters, check minimum order value against filtered items only
        $totalToCheck = $cartTotal;
        if (!empty($discount->filter) && $cartItems !== null && $products !== null) {
            $totalToCheck = $this->calculateFilteredCartTotal($cartItems, $products, $discount, $cartTotal);
        }
        
        // Only check minimum order value if it's actually set
        if (isset($rulesOnCart['minOrderValue']) && $totalToCheck < $rulesOnCart['minOrderValue']) {
            return -1;
        }
        $discountAmount = -1;
        // if($discount->discount == 'percentageDiscount'){
        //     // discount on base shipping charge
        //     $discountAmount = ($baseShippingCharge * $discount->discountValue) / 100;
        // } else if($discount->discount == 'fixedDiscount'){
        //     // fixed discount amount 
        //     $discountAmount = $discount->discountValue;
        // } else if($discount->discount == 'free'){
        //     // 100% discount on shipping - return full shipping amount
        //     $discountAmount = $baseShippingCharge;
        // }
        $discountAmount = $baseShippingCharge;
        // max discount amount
        $maxDiscountAmount = $rulesOnCart['maxDiscountAmount'] ?? null;
        if ($maxDiscountAmount && $discountAmount > $maxDiscountAmount) {
            $discountAmount = $maxDiscountAmount;
        }
        // return the discount value
        return round($discountAmount, 2);
    }

    /**
     * Validate coupon code against cart items and products
     * Validates based on discount type specific rules
     * @param string $couponCode
     * @param mixed $cartItems
     * @param mixed $products
     * @param float $cartTotal
     * @param int|null $userId
     * @return array ['status' => bool, 'message' => string, 'discount' => object|null]
     */
    public function validateCoupon($couponCode, $cartItems, $products, $cartTotal, $userId = null, $locationId = null, $brandId = null)
    {
        // First, check if this is a referral coupon
        $referalRecord = EcomReferalProgram::where('coupon_code', $couponCode)->first();
        $discount = null;
        $isReferralCoupon = false;

        // If it's a referral coupon, validate referral-specific rules
        if ($referalRecord) {
            $isReferralCoupon = true;
            // Referral coupons require login
            if (!$userId) {
                return [
                    'status' => false,
                    'message' => 'Please log in to use referral coupons.',
                    'discount' => null
                ];
            }

            // Verify customer ownership
            if ((int) $referalRecord->customer_id !== (int) $userId) {
                return [
                    'status' => false,
                    'message' => 'This referral coupon is invalid.',
                    'discount' => null
                ];
            }

            // Check if already used
            if ($referalRecord->is_used) {
                return [
                    'status' => false,
                    'message' => 'Referral coupon has already been used.',
                    'discount' => null
                ];
            }
            // Fetch the referral discount template
            $discount = CustomDiscount::where('id', $referalRecord->discount_id)
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
            if (!$discount) {
                return [
                    'status' => false,
                    'message' => 'Referral discount is no longer valid or available for this location/brand.',
                    'discount' => null
                ];
            }
            // Set the actual referral coupon code on the discount template
            // This ensures checkCouponCode validation will pass in isDiscountApplicable
            $discount->couponCode = $couponCode;
            
           
        } else {
            // Not a referral coupon, check regular discounts
            $discount = CustomDiscount::whereRaw('BINARY couponCode = ?', [$couponCode])
                ->where('isDisabled', 0)
                ->where('is_referal_program_discount', false)
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

            if (!$discount) {
                return [
                    'status' => false,
                    'message' => 'Invalid or expired coupon code.',
                    'discount' => null
                ];
            }
        }

        // Validate customer eligibility
        $customer = null;
        if ($userId) {
            $customer = \App\Contact::find($userId);
        }

        // Check if discount has customer rules
        if (!empty($discount->rulesOnCustomer)) {
            $rulesOnCustomer = $discount->rulesOnCustomer;
            if (is_string($rulesOnCustomer)) {
                $rulesOnCustomer = json_decode($rulesOnCustomer, true);
            }
            if(request()->attributes->get('is_guest_request', false) && $rulesOnCustomer['on-first-order'] == true || request()->attributes->get('is_guest_request', false) && $rulesOnCustomer['on-last-order-value'] == true){
                return [
                    'status' => false,
                    'message' => 'This coupon is only available for customers.',
                    'discount' => null
                ];
            }

            // If rules are not set to 'all', customer must be logged in
            if (isset($rulesOnCustomer['applyOn']) && $rulesOnCustomer['applyOn'] !== 'all' && !$customer) {
                return [
                    'status' => false,
                    'message' => 'This coupon is only available for registered customers. Please log in to use this coupon.',
                    'discount' => null
                ];
            }

            // If customer is logged in or rules apply to 'all', validate customer rules
            if ($customer && !$this->checkCustomerRules($discount, $customer)) {
                return [
                    'status' => false,
                    'message' => 'This coupon is not available for your account.',
                    'discount' => null
                ];
            }
        }

        // Validate based on discount type
        $validationResult = null;
        switch ($discount->discountType) {
            case 'productAdjustment':
                $validationResult = $this->validateProductAdjustmentCoupon($discount, $cartItems, $products);
                break;
            
            case 'cartAdjustment':
                $validationResult = $this->validateCartAdjustmentCoupon($discount, $cartItems, $products, $cartTotal);
                break;
            
            case 'freeShipping':
                $validationResult = $this->validateFreeShippingCoupon($discount, $cartItems, $products, $cartTotal);
                break;
            
            case 'buyXgetY':
                $validationResult = $this->validateBXGYCoupon($discount, $cartItems, $products);
                break;
            
            case 'buyXgetX':
                $validationResult = $this->validateBXGXCoupon($discount, $cartItems, $products);
                break;
            
            default:
                $validationResult = [
                    'status' => false,
                    'message' => 'Invalid discount type.',
                    'discount' => null
                ];
        }

        // If validation passed and it's a referral coupon, attach the referral record to the result
        if ($validationResult['status'] && $isReferralCoupon && $referalRecord) {
            $validationResult['referral_record'] = $referalRecord;
        }
        return $validationResult;
    }


    /**
     * Validate Product Adjustment coupon
     * Validates min/max quantity requirements
     */
    private function validateProductAdjustmentCoupon($discount, $cartItems, $products)
    {
        // Get items that match the discount filters
        $matchingItems = [];
        $totalMatchingQty = 0;

        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if (!$product) continue;

            $variation = $product->variations->where('id', $cartItem->variation_id)->first();
            if (!$variation) continue;

            // Check if this item matches the discount filters
            if ($this->checkFilters($discount, $product, $variation, $cartItem->qty)) {
                $matchingItems[] = $cartItem;
                $totalMatchingQty += $cartItem->qty;
            }
        }

        if (empty($matchingItems)) {
            return [
                'status' => false,
                'message' => 'No eligible products in cart for this coupon.',
                'discount' => null
            ];
        }

        // Check minimum quantity
        if ($discount->minBuyQty && $totalMatchingQty < $discount->minBuyQty) {
            return [
                'status' => false,
                'message' => "Minimum quantity of {$discount->minBuyQty} required for eligible products. You have {$totalMatchingQty}.",
                'discount' => null
            ];
        }

        // Check maximum quantity
        if ($discount->maxBuyQty && $totalMatchingQty > $discount->maxBuyQty) {
            return [
                'status' => false,
                'message' => "Maximum quantity of {$discount->maxBuyQty} allowed for eligible products. You have {$totalMatchingQty}.",
                'discount' => null
            ];
        }

        return [
            'status' => true,
            'message' => 'Coupon validated successfully.',
            'discount' => $discount
        ];
    }

    /**
     * Validate Cart Adjustment coupon
     * Validates minimum order value
     */
    private function validateCartAdjustmentCoupon($discount, $cartItems, $products, $cartTotal)
    {
        $rulesOnCart = $discount->rulesOnCart ?? [];
        
        if (is_string($rulesOnCart)) {
            $rulesOnCart = json_decode($rulesOnCart, true);
        }

        // Calculate the total to check (filtered or full cart)
        $totalToCheck = $cartTotal;
        if (!empty($discount->filter) && $cartItems !== null && $products !== null) {
            $totalToCheck = $this->calculateFilteredCartTotal($cartItems, $products, $discount, $cartTotal);
            
            if ($totalToCheck == 0) {
                return [
                    'status' => false,
                    'message' => 'No eligible products in cart for this coupon.',
                    'discount' => null
                ];
            }
        }

        // Check minimum order value
        $minOrderValue = $rulesOnCart['minOrderValue'] ?? 0;
        if ($minOrderValue > 0 && $totalToCheck < $minOrderValue) {
            return [
                'status' => false,
                'message' => "Minimum order value of $" . number_format($minOrderValue, 2) . " required. Your cart total is $" . number_format($totalToCheck, 2) . ".",
                'discount' => null
            ];
        }

        return [
            'status' => true,
            'message' => 'Coupon validated successfully.',
            'discount' => $discount
        ];
    }

    /**
     * Validate Free Shipping coupon
     * Validates minimum order value
     */
    private function validateFreeShippingCoupon($discount, $cartItems, $products, $cartTotal)
    {
        $rulesOnCart = $discount->rulesOnCart ?? [];
        
        if (is_string($rulesOnCart)) {
            $rulesOnCart = json_decode($rulesOnCart, true);
        }

        // Calculate the total to check (filtered or full cart)
        $totalToCheck = $cartTotal;
        if (!empty($discount->filter) && $cartItems !== null && $products !== null) {
            $totalToCheck = $this->calculateFilteredCartTotal($cartItems, $products, $discount, $cartTotal);
            
            if ($totalToCheck == 0) {
                return [
                    'status' => false,
                    'message' => 'No eligible products in cart for this coupon.',
                    'discount' => null
                ];
            }
        }

        // Check minimum order value
        $minOrderValue = $rulesOnCart['minOrderValue'] ?? 0;
        if ($minOrderValue > 0 && $totalToCheck < $minOrderValue) {
            return [
                'status' => false,
                'message' => "Minimum order value of $" . number_format($minOrderValue, 2) . " required for free shipping. Your cart total is $" . number_format($totalToCheck, 2) . ".",
                'discount' => null
            ];
        }

        return [
            'status' => true,
            'message' => 'Coupon validated successfully.',
            'discount' => $discount
        ];
    }

    /**
     * Validate Buy X Get Y coupon
     * Validates minimum quantity requirements using custom_meta buy_quantity
     * Counts total quantity across all matching items (including variants)
     */
    private function validateBXGYCoupon($discount, $cartItems, $products)
    {
        // Get buy_quantity from custom_meta (BXGY uses this, not minBuyQty)
        $customMeta = $discount->custom_meta;
        if (is_string($customMeta)) {
            $customMeta = json_decode($customMeta, true);
        }
        
        $buyQuantity = $customMeta['buy_quantity'] ?? 1;

        // Count TOTAL quantity across all matching items (including different variants)
        $totalMatchingQty = 0;
        $matchingItems = [];

        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if (!$product) continue;

            $variation = $product->variations->where('id', $cartItem->variation_id)->first();
            if (!$variation) continue;

            // Check if this item matches the discount filters
            if ($this->checkFiltersWithoutQty($discount, $product, $variation)) {
                $matchingItems[] = $cartItem;
                $totalMatchingQty += $cartItem->qty;
            }
        }

        if (empty($matchingItems)) {
            return [
                'status' => false,
                'message' => 'No eligible products in cart for this Buy X Get Y offer.',
                'discount' => null
            ];
        }

        // Check if total quantity meets the requirement
        if ($totalMatchingQty < $buyQuantity) {
            return [
                'status' => false,
                'message' => "Buy {$buyQuantity} of any eligible item to qualify for this offer. Your highest quantity is {$totalMatchingQty}.",
                'discount' => null
            ];
        }

        return [
            'status' => true,
            'message' => 'Coupon validated successfully.',
            'discount' => $discount
        ];
    }

    /**
     * Validate Buy X Get X coupon
     * Validates minimum quantity requirements
     * Counts total quantity across all matching items (including variants)
     */
    private function validateBXGXCoupon($discount, $cartItems, $products)
    {
        $minBuyQty = $discount->minBuyQty ?? 1;

        // Count TOTAL quantity across all matching items (including different variants)
        $totalMatchingQty = 0;
        $matchingItems = [];

        foreach ($cartItems as $cartItem) {
            $product = $products->where('id', $cartItem->product_id)->first();
            if (!$product) continue;

            $variation = $product->variations->where('id', $cartItem->variation_id)->first();
            if (!$variation) continue;

            // Check if this item matches the discount filters
            if ($this->checkFiltersWithoutQty($discount, $product, $variation)) {
                $matchingItems[] = $cartItem;
                $totalMatchingQty += $cartItem->qty;
            }
        }

        if (empty($matchingItems)) {
            return [
                'status' => false,
                'message' => 'No eligible products in cart for this Buy X Get X offer.',
                'discount' => null
            ];
        }

        // Check if total quantity meets the requirement
        if ($totalMatchingQty < $minBuyQty) {
            return [
                'status' => false,
                'message' => "Buy {$minBuyQty} of any eligible item to qualify for this offer. You have {$totalMatchingQty}.",
                'discount' => null
            ];
        }

        return [
            'status' => true,
            'message' => 'Coupon validated successfully.',
            'discount' => $discount
        ];
    }

    /**
     * Check filters without quantity validation
     * Used for BXGY/BXGX where we need to count total qty across all matching items
     * @param mixed $discount
     * @param mixed $product
     * @param mixed $variation
     * @return bool
     */
    private function checkFiltersWithoutQty($discount, $product, $variation)
    {
        // Check if product is valid
        if(!$product || !is_object($product)){
            return false;
        }
        
        $filter = $discount->filter ?? [];
        if(empty($filter) || $filter == null || $filter == '' || $filter == 'null'){
            return true;
        }
        
        $filter = json_decode($filter, true);
        
        // filter by category
        if(isset($filter['categories'])){
            $productCategoryIds = $product->webcategories ? $product->webcategories->pluck('id')->toArray() : ($product->categories ? $product->categories->pluck('id')->toArray() : []);
            if($filter['categories']['opration'] == 'in'){
                if(array_intersect($productCategoryIds, $filter['categories']['ids'])){
                    return true;
                }
            }else{
                if(!array_intersect($productCategoryIds, $filter['categories']['ids'])){
                    return true;
                }
            }
        }

        // filter by brand
        if(isset($filter['brand'])){
            if($filter['brand']['opration'] == 'in'){
                if($product->brand && in_array($product->brand->id, $filter['brand']['ids'])){
                    return true;
                }
            }else{
                if($product->brand && !in_array($product->brand->id, $filter['brand']['ids'])){
                    return true;
                }
            }
        }

        // filter by product
        if(isset($filter['product_ids'])){
            if($filter['product_ids']['opration'] == 'in'){
                if(in_array($product->id, $filter['product_ids']['ids'])){
                    return true;
                }
            }else{
                if(!in_array($product->id, $filter['product_ids']['ids'])){
                    return true;
                }
            }
        }

        // filter by variation
        if(isset($filter['variation_ids'])){
            if($filter['variation_ids']['opration'] == 'in'){
                if($variation && in_array($variation->id, $filter['variation_ids']['ids'])){
                    return true;
                }
            }else{
                if($variation && !in_array($variation->id, $filter['variation_ids']['ids'])){
                    return true;
                }
            }
        }
        
        return false;
    }
}