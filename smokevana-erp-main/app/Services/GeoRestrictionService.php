<?php

namespace App\Services;

use App\GeoRestriction;
use Illuminate\Support\Facades\DB;

class GeoRestrictionService
{
    /**
     * Check if a product is restricted for a given location
     *
     * @param  int  $productId
     * @param  array  $location
     * @return bool
     */
    public function isProductRestricted($productId, $location)
    {
        // Check product-specific restrictions
        $productRestrictions = GeoRestriction::active()
            ->where('scope', 'product')
            ->whereJsonContains('target_entities', $productId)
            ->get();

        foreach ($productRestrictions as $restriction) {
            if ($this->isLocationRestrictedByRule($restriction, $location)) {
                return true;
            }
        }

        // Check category restrictions
        $categoryIds = $this->getProductCategories($productId);
        if (count($categoryIds) > 0) {
            $categoryRestrictions = GeoRestriction::active()
                ->where('scope', 'category')
                ->where(function($query) use ($categoryIds) {
                    foreach ($categoryIds as $categoryId) {
                        $query->orWhereJsonContains('target_entities', $categoryId);
                    }
                })
                ->get();

            foreach ($categoryRestrictions as $restriction) {
                if ($this->isLocationRestrictedByRule($restriction, $location)) {
                    return true;
                }
            }
        }

        // Check brand restrictions
        $brandId = $this->getProductBrand($productId);
        if ($brandId) {
            $brandRestrictions = GeoRestriction::active()
                ->where('scope', 'brand')
                ->whereJsonContains('target_entities', $brandId)
                ->get();

            foreach ($brandRestrictions as $restriction) {
                if ($this->isLocationRestrictedByRule($restriction, $location)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if a variation is restricted for a given location
     *
     * @param  int  $variationId
     * @param  array  $location
     * @return bool
     */
    public function isVariationRestricted($variationId, $location)
    {
        // Check variation-specific restrictions
        $variationRestrictions = GeoRestriction::active()
            ->where('scope', 'variation')
            ->whereJsonContains('target_entities', $variationId)
            ->get();

        foreach ($variationRestrictions as $restriction) {
            if ($this->isLocationRestrictedByRule($restriction, $location)) {
                return true;
            }
        }

        // Also check product-level restrictions for this variation's product
        $variation = DB::table('variations')->where('id', $variationId)->first();
        if ($variation && $variation->product_id) {
            return $this->isProductRestricted($variation->product_id, $location);
        }

        return false;
    }

    /**
     * Check if a location is restricted by a specific rule
     *
     * @param  GeoRestriction  $restriction
     * @param  array  $location
     * @return bool
     */
    protected function isLocationRestrictedByRule($restriction, $location)
    {
        $locationMatches = [];
        $locationRules = [];

        // Find all matching locations and their rules
        foreach ($restriction->locations as $restrictedLocation) {
            if ($this->matchesLocation($location, $restrictedLocation)) {
                $locationMatches[] = $restrictedLocation;
                // Use the location's rule_type, defaulting to 'restrict' if not specified
                $locationRules[] = $restrictedLocation['rule_type'] ?? 'restrict';
            }
        }

        // If no locations match:
        if (empty($locationMatches)) {
            // If any location in the rule is 'allow', treat as allow-list: restrict all others
            $hasAllow = collect($restriction->locations)->contains(function($loc) {
                return ($loc['rule_type'] ?? 'restrict') === 'allow';
            });
            if ($hasAllow) {
                return true; // Not in allow-list, so restrict
            }
            // Otherwise, not restricted (no restrict/disallow rule matches)
            return false;
        }

        // If any location has a 'restrict' rule, the product is restricted
        if (in_array('restrict', $locationRules)) {
            return true;
        }

        // If all matching locations have 'allow' rules, the product is not restricted
        if (count(array_unique($locationRules)) === 1 && $locationRules[0] === 'allow') {
            return false;
        }

        // Default to restricted if there's any ambiguity
        return true;
    }

    /**
     * Check if a location matches a restricted location
     *
     * @param  array  $location
     * @param  array  $restrictedLocation
     * @return bool
     */
    protected function matchesLocation($location, $restrictedLocation)
    {
        $type = $restrictedLocation['type'] ?? null;
        $value = $restrictedLocation['value'] ?? null;

        if (!$type || !$value) {
            return false;
        }

        if ($type === 'state') {
            return isset($location['state']) && 
                   strtolower($location['state']) === strtolower($value);
        }

        if ($type === 'city') {
            return isset($location['city']) && 
                   strtolower($location['city']) === strtolower($value);
        }

        if ($type === 'zip') {
            return isset($location['zip']) && 
                   $location['zip'] === $value;
        }

        return false;
    }

    /**
     * Get all restricted products for a given location
     *
     * @param  array  $location
     * @return array
     */
    public function getRestrictedProducts($location)
    {
        $restrictedProductIds = collect();

        // Get all active restrictions
        $restrictions = GeoRestriction::active()->get();

        foreach ($restrictions as $restriction) {
            if ($this->isLocationRestrictedByRule($restriction, $location)) {
                switch ($restriction->scope) {
                    case 'product':
                        $restrictedProductIds = $restrictedProductIds->merge($restriction->target_entities);
                        break;

                    case 'variation':
                        // Get product IDs for these variations
                        $productIds = DB::table('variations')
                            ->whereIn('id', $restriction->target_entities)
                            ->pluck('product_id')
                            ->toArray();
                        $restrictedProductIds = $restrictedProductIds->merge($productIds);
                        break;

                    case 'category':
                        $categoryIds = $restriction->target_entities;
                        $productIds = $this->getProductsInCategories($categoryIds);
                        $restrictedProductIds = $restrictedProductIds->merge($productIds);
                        break;

                    case 'brand':
                        $brandIds = $restriction->target_entities;
                        $productIds = $this->getProductsInBrands($brandIds);
                        $restrictedProductIds = $restrictedProductIds->merge($productIds);
                        break;
                }
            }
        }

        return $restrictedProductIds->unique()->values()->all();
    }

    /**
     * Get product categories (including main and sub-category)
     *
     * @param  int  $productId
     * @return array
     */
    protected function getProductCategories($productId)
    {
        $categoryIds = [];
        
        $product = DB::table('products')->where('id', $productId)->first();
        
        if ($product) {
            // Add main category
            if ($product->category_id) {
                $categoryIds[] = $product->category_id;
            }
            
            // Add sub-category
            if ($product->sub_category_id) {
                $categoryIds[] = $product->sub_category_id;
            }

            // Also check many-to-many relationship via webcategories_product
            $webCategories = DB::table('webcategories_product')
                ->where('product_id', $productId)
                ->pluck('category_id')
                ->toArray();
            
            $categoryIds = array_merge($categoryIds, $webCategories);
        }

        return array_unique($categoryIds);
    }

    /**
     * Get product brand
     *
     * @param  int  $productId
     * @return int|null
     */
    protected function getProductBrand($productId)
    {
        return DB::table('products')
            ->where('id', $productId)
            ->value('brand_id');
    }

    /**
     * Get products in categories
     *
     * @param  array  $categoryIds
     * @return array
     */
    protected function getProductsInCategories($categoryIds)
    {
        $productIds = [];

        // Get products from direct category/sub_category relationship
        $directProducts = DB::table('products')
            ->where(function($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds)
                      ->orWhereIn('sub_category_id', $categoryIds);
            })
            ->where('is_inactive', 0)
            ->pluck('id')
            ->toArray();

        // Get products from many-to-many relationship
        $webProducts = DB::table('webcategories_product')
            ->whereIn('category_id', $categoryIds)
            ->pluck('product_id')
            ->toArray();

        $productIds = array_merge($directProducts, $webProducts);

        return array_unique($productIds);
    }

    /**
     * Get products in brands
     *
     * @param  array  $brandIds
     * @return array
     */
    protected function getProductsInBrands($brandIds)
    {
        return DB::table('products')
            ->whereIn('brand_id', $brandIds)
            ->where('is_inactive', 0)
            ->pluck('id')
            ->toArray();
    }
}

