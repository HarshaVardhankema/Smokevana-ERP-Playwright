<?php

namespace App\Http\Controllers\ECOM;

use services;
use App\Media;
use App\Brands;
use App\Product;
use App\Category;
use App\Contact;
use App\CustomerGroup;
use App\Variation;
use App\ProductLocation;
use App\Jobs\SyncProduct;
use App\ProductVariation;
use App\Jobs\SyncCustomer;
use App\VariationTemplate;
use App\VariationGroupPrice;
use Illuminate\Http\Request;
use App\Jobs\SyncProductMeta;
use App\VariationValueTemplate;
use App\VariationLocationDetails;
use App\SearchHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\CateLogResource;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\Models\PreferredBrand;
use App\Models\PreferredCategory;

class CatalogController extends Controller
{
    protected $contactUtil;
    protected $productUtil;

    public function __construct(ContactUtil $contactUtil, ProductUtil $productUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Whether the customer uses default sell price (no specific price group).
     * @param int|string|null $priceGroupId
     * @return bool
     */
    private function useDefaultSellPrice($priceGroupId)
    {
        return $priceGroupId === 0 || $priceGroupId === null || $priceGroupId === '';
    }

    /**
     * Get side menu for authenticated and non-authenticated users (parent categories only if has children)
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sideMenu(Request $request)
    {
        $authData = $this->authCheck($request);
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $byState = $request->query('byState', false);

        // Fetch categories based on authentication
        if ($authData['status'] == true) {
            $categories = Category::getCategoriesHierarchy()
                ->where('location_id',config('services.b2b.location_id'))
                ->whereHas('children') // Only categories that have children
                ->get();

            $brands = Category::with('brands')

                ->where('parent_id', 0)
                ->where('location_id',config('services.b2b.location_id'))
                ->whereHas('brands') // Only include categories with non-empty brands array
                ->get();

            return response()->json(['status' => true, 'categories' => $categories, 'brands' => $brands]);
        } else if ($authData['status'] == false) {
            // Categories visible to public that have children
            $categories = Category::getCategoriesHierarchy()
                ->where('location_id',config('services.b2b.location_id'))
                ->where('visibility', 'public')
                ->where('location_id',1)
                ->whereHas('children') // Only categories that have children
                ->get();

            $brands = Category::with('brands')
                ->where('location_id',config('services.b2b.location_id'))
                ->where('visibility', 'public')
                ->where('parent_id', 0)
                ->whereHas('brands') // Only include categories with non-empty brands array
                ->get();

            return response()->json(['status' => true, 'categories' => $categories, 'brands' => $brands]);
        }

        return response()->json(['status' => false, 'message' => 'Error while fetching Side menu']);
    }

    /**
     * Get gift cards for catalog
     * Shows gift cards separately from regular products
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGiftCards(Request $request)
    {
        $authData = $this->authCheck($request);
        $locationId = config('services.b2b.location_id');

        if ($authData['status'] == true) {
            // Authenticated user - show all gift cards
            $giftCards = Product::where('is_gift_card', 1)
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->wherehas('product_locations',function($q) use ($locationId) {
                    $q->where('product_locations.location_id', $locationId);
                })
                ->with('webcategories', 'brand','product_locations','product_states')
                ->with(['variations' => function($query) use ($authData) {
                    $priceTier = $authData['user']->price_tier;
                    $priceGroupId = key($priceTier);
                    $query->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                        $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                            ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                    });
                }])
                ->get();
        } else {
            // Non-authenticated user - show public gift cards only
            $giftCards = Product::where('is_gift_card', 1)
                ->where('productVisibility', 'public')
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->wherehas('product_locations',function($q) use ($locationId) {
                    $q->where('product_locations.location_id', $locationId);
                })
                ->with('webcategories', 'brand','product_locations','product_states')
                ->with(['variations' => function($query) use ($locationId) {
                    $query->leftJoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')
                        ->where('vld.location_id', $locationId);
                }])
                ->get();
        }

        // Format gift cards for response
        $formattedGiftCards = $giftCards->map(function($giftCard) use ($authData) {
            // Get price from variations or use gift_card_value or price_range
            $price = $giftCard->gift_card_value ?? $giftCard->price_range ?? 0;
            if ($giftCard->variations && $giftCard->variations->isNotEmpty()) {
                $variation = $giftCard->variations->first();
                if ($authData['status'] == true) {
                    // Authenticated user - use default_purchase_price or ad_price
                    $price = $variation->default_purchase_price ?? $variation->ad_price ?? $price;
                } else {
                    // Non-authenticated user - use default_sell_price or sell_price_inc_tax
                    $price = $variation->default_sell_price ?? $variation->sell_price_inc_tax ?? $price;
                }
            }

            return [
                'id' => $giftCard->id,
                'name' => $giftCard->name,
                'sku' => $giftCard->sku,
                'slug' => $giftCard->slug,
                'description' => $giftCard->description ?? '',
                'short_description' => $giftCard->short_description ?? '',
                'image_url' => $giftCard->image_url ?? '',
                'price' => (float) $price,
                'currency' => 'USD',
                'gift_card_expiry_days' => $giftCard->gift_card_expiry_days ?? null,
                'gift_card_stock' => (float) $giftCard->gift_card_stock ?? 0,
                'allow_partial_redemption' => $giftCard->allow_partial_redemption ?? true,
                'is_gift_card' => true,
                'variations' => $giftCard->variations->map(function($variation) use ($authData) {
                    $price = 0;
                    if ($authData['status'] == true) {
                        $price = $variation->default_purchase_price ?? $variation->ad_price ?? 0;
                    } else {
                        $price = $variation->default_sell_price ?? $variation->sell_price_inc_tax ?? 0;
                    }
                    return [
                        'id' => $variation->id,
                        'name' => $variation->name,
                        'sku' => $variation->sub_sku,
                        'price' => (float) $price,
                        'stock' => $variation->qty ?? 0,
                    ];
                }),
                // Don't include qty for gift cards - they can be added to cart like regular products
                'show_qty_selector' => true,
                'purchase_only' => false,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Gift cards retrieved successfully',
            'data' => $formattedGiftCards,
            'total' => $formattedGiftCards->count(),
        ]);
    }

    /**
     * Build full image URL for category/brand logo or banner (filename stored in DB).
     */
    private function categoryBrandImageUrl(?string $filename): ?string
    {
        if (empty($filename)) {
            return null;
        }
        return asset('uploads/img/' . rawurlencode($filename));
    }

    /**
     * Append image_url fields to category_brands and brand list for API response.
     */
    private function appendCategoryBrandImageUrls($categoryBrands, $brandList)
    {
        $categoryBrands->each(function ($category) {
            $category->logo_url = $this->categoryBrandImageUrl($category->logo ?? null);
            $category->banner_url = $this->categoryBrandImageUrl($category->banner ?? null);
            $category->category_banner_url = $this->categoryBrandImageUrl($category->category_banner ?? null);
            if ($category->relationLoaded('brands') && $category->brands) {
                $category->brands->each(function ($brand) {
                    $brand->logo_url = $this->categoryBrandImageUrl($brand->logo ?? null);
                    $brand->banner_url = $this->categoryBrandImageUrl($brand->banner ?? null);
                });
            }
        });
        $brandList->each(function ($brand) {
            $brand->logo_url = $this->categoryBrandImageUrl($brand->logo ?? null);
            $brand->banner_url = $this->categoryBrandImageUrl($brand->banner ?? null);
        });
        return [$categoryBrands, $brandList];
    }

    public function sideMenucat2brand(Request $request)
    {
        $authData = $this->authCheck($request);
        $byState = $request->query('byState', false);
        $perPage = $request->query('perPage', 15);
        $sortBy = $request->query('sort', 'popularity');
        $page = $request->query('page', 1);
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        
        // Parse query parameters for category and brand filtering
        $catSlugs = $request->query(key: 'cat') ? explode(',', $request->query('cat')) : [];
        $brandSlugs = $request->query('brand') ? explode(',', $request->query('brand')) : [];

        // Check if this is a gift card category request (support both 'gift-cards' and 'gift-code')
        $isGiftCardCategory = false;
        foreach ($catSlugs as $slug) {
            if (in_array($slug, ['gift-cards', 'gift-code'])) {
                $isGiftCardCategory = true;
                break;
            }
        }

        // If this is a gift card category request, return gift cards directly
        if ($isGiftCardCategory) {
            return $this->getGiftCards($request);
        }

        // search query
        $searchTerm = $request->query('s', '');

        // regex pattern
        $regexPattern = null;
        if (!empty($searchTerm)) {
            $searchWords = preg_split('/\s+/', $searchTerm);
            $regexPattern = implode('.*', array_map(function ($word) {
                return "(?=.*" . preg_quote($word) . ")";
            }, $searchWords));
        }
        try {
        // Fetch categories based on authentication
        if ($authData['status'] == true) {
            // Get all categories for the location, with their associated brands
            // Brands are filtered by location_id and visibility = public
            $locationId = config('services.b2b.location_id');
            $brands = Category::with(['brandCategories' => function($query) use ($locationId) {
                    $query->where('brands.location_id', $locationId)
                          ->where('brands.visibility', 'public');
                }])
                ->where('location_id', $locationId)
                ->get()
                ->map(function($category) {
                    $category->setRelation('brands', $category->brandCategories);
                    $category->unsetRelation('brandCategories');
                    return $category;
                });
                $brand = Brands::where('location_id',config('services.b2b.location_id'))->get();

                // Get products for authenticated users
                $products = Product::with('webcategories', 'brand','product_locations','product_states')
                    ->wherehas('product_locations',function($q) {
                        $q->where('product_locations.location_id',config('services.b2b.location_id'));
                    })
                    ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                    ->leftJoin('variation_group_prices', function ($join) use ($authData) {
                        $priceTier = $authData['user']->price_tier;
                        $priceGroupId = key($priceTier);
                        $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                            ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                    })
                    ->leftJoin('wishlists', function ($join) use ($authData) {
                        $join->on('products.id', '=', 'wishlists.product_id')
                            ->where('wishlists.user_id', '=', $authData['user']->id);
                    })
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->where(function($query) {
                        $query->where('is_gift_card', 0) // Hide gift cards from regular product listings
                              ->orWhereNull('is_gift_card'); // Also include products where field is null
                    })
                    ->forCatalogListing($catSlugs ?? null)
                    ->when(!empty($catSlugs), function($query) use ($catSlugs) {
                        $query->whereHas('webcategories', function ($q) use ($catSlugs) {
                            $q->whereIn('slug', $catSlugs)
                                ->where('category_type', 'product');
                        });
                    })
                    ->when(!empty($brandSlugs), function($query) use ($brandSlugs) {
                        $query->whereHas('brand', function($q) use ($brandSlugs) {
                            $q->whereIn('slug', $brandSlugs);
                        });
                    })
                    ->when($byState, function($query) use ($byState) {
                        $query->whereHas('product_states', function($q) use ($byState) {
                            $q->where('product_state.state', '=', $byState);
                        });
                    })
                    ->when($regexPattern, function($query) use ($regexPattern) {
                        $query->where(function ($query) use ($regexPattern) {
                            $query->where('products.name', 'REGEXP', $regexPattern)
                                ->orWhere('products.sku', 'REGEXP', $regexPattern)
                                ->orWhereHas('webcategories', function ($subQuery) use ($regexPattern) {
                                    $subQuery->where('name', 'REGEXP', $regexPattern);
                                });
                        });
                    })
                    ->selectRaw('products.*, MIN(variation_group_prices.price_inc_tax) as ad_price, wishlists.id as wishlist_id')
                    ->groupBy('products.id', 'wishlists.id')
                    ->havingRaw('ad_price > 0');

                // Apply price range filter for authenticated users
                if ($minPrice !== null && $maxPrice !== null) {
                    $products = $products->havingRaw('ad_price >= ? AND ad_price <= ?', [$minPrice, $maxPrice]);
                } elseif ($minPrice !== null) {
                    $products = $products->havingRaw('ad_price >= ?', [$minPrice]);
                } elseif ($maxPrice !== null) {
                    $products = $products->havingRaw('ad_price <= ?', [$maxPrice]);
                }

                //sorting 
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

                $products = $products->paginate($perPage, ['*'], 'page', $page);

                // Apply customer group percentage to prices
                $contact = $authData['user'];
                $business_id = $contact->business_id ?? 1;
                $cg = $this->contactUtil->getCustomerGroup($business_id, $contact->id);
                
                // Percentage is applied in CateLogResource, no need to apply here

                // Use the current page's products as a simple related-products pool
                $productCollection = $products->getCollection();
                $relatedProducts = $productCollection->take(20);

                $this->appendCategoryBrandImageUrls($brands, $brand);

                return response()->json([
                    'status' => true,
                    'category_brands' => $brands,
                    'brand' => $brand,
                    'products' => [
                        'current_page' => $products->currentPage(),
                        'data' => CateLogResource::collection($productCollection),
                        'last_page' => $products->lastPage(),
                        'total' => $products->total(),
                        'from' => $products->firstItem(),
                        'per_page' => $products->perPage(),
                        'to' => $products->lastItem(),
                    ],
                    'relatedProducts' => CateLogResource::collection($relatedProducts),
                ]);
        } else if ($authData['status'] == false) {
            // Get all public categories for the location, with their associated brands
            // Brands are filtered by location_id and visibility = public
            $locationId = config('services.b2b.location_id');
            $brands = Category::with(['brandCategories' => function($query) use ($locationId) {
                    $query->where('brands.location_id', $locationId)
                          ->where('brands.visibility', 'public');
                }])
                ->where('location_id', $locationId)
                ->where('visibility', 'public')
                ->get()
                ->map(function($category) {
                    $category->setRelation('brands', $category->brandCategories);
                    $category->unsetRelation('brandCategories');
                    return $category;
                });
                $brand = Brands::where('location_id',config('services.b2b.location_id'))->get();

                // Get products for non-authenticated users
                $products = Product::with('webcategories', 'brand','product_locations','product_states')
                    ->wherehas('product_locations',function($q) {
                        $q->where('product_locations.location_id',config('services.b2b.location_id'));
                    })
                    ->where('productVisibility', 'public')
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->where(function($query) {
                        $query->where('is_gift_card', 0) // Hide gift cards from regular product listings
                              ->orWhereNull('is_gift_card'); // Also include products where field is null
                    })
                    ->forCatalogListing($catSlugs ?? null)
                    ->when(!empty($catSlugs), function($query) use ($catSlugs) {
                        $query->whereHas('webcategories', function ($q) use ($catSlugs) {
                            $q->whereIn('slug', $catSlugs)
                                ->where('category_type', 'product')
                                ->where('visibility', 'public');
                        });
                    })
                    ->when(!empty($brandSlugs), function($query) use ($brandSlugs) {
                        $query->whereHas('brand', function($q) use ($brandSlugs) {
                            $q->whereIn('slug', $brandSlugs);
                        });
                    })
                    ->when($byState, function($query) use ($byState) {
                        $query->whereHas('product_states', function($q) use ($byState) {
                            $q->where('product_state.state', '=', $byState);
                        });
                    })
                    ->whereDoesntHave('webcategories', function ($query) {
                        $query->where('visibility', 'protected');
                    })
                    ->leftJoin('variations as price_variations', 'products.id', '=', 'price_variations.product_id')
                    ->leftJoin('variation_location_details as vld', 'price_variations.id', '=', 'vld.variation_id')
                    ->where('vld.location_id', config('services.b2b.location_id'));

                // Frontend uses ad_price for Prime price (61), pricing for web price (66). Assign accordingly.
                $publicPriceGroupId = config('services.b2b.public_price_group_id');
                $primePriceGroupId = config('services.b2b.prime_price_group_id');

                if (!empty($publicPriceGroupId)) {
                    $products = $products->leftJoin('variation_group_prices as vgp_web', function ($join) use ($publicPriceGroupId) {
                        $join->on('price_variations.id', '=', 'vgp_web.variation_id')
                            ->where('vgp_web.price_group_id', '=', $publicPriceGroupId);
                    });
                }
                if (!empty($primePriceGroupId)) {
                    $products = $products->leftJoin('variation_group_prices as vgp_prime', function ($join) use ($primePriceGroupId) {
                        $join->on('price_variations.id', '=', 'vgp_prime.variation_id')
                            ->where('vgp_prime.price_group_id', '=', $primePriceGroupId);
                    });
                }

                if (!empty($publicPriceGroupId) || !empty($primePriceGroupId)) {
                    $webPriceExpr = !empty($publicPriceGroupId)
                        ? 'COALESCE(MIN(vgp_web.price_inc_tax), MIN(price_variations.sell_price_inc_tax))'
                        : 'MIN(price_variations.sell_price_inc_tax)';
                    $primePriceExpr = !empty($primePriceGroupId)
                        ? 'COALESCE(MIN(vgp_prime.price_inc_tax), MIN(price_variations.sell_price_inc_tax))'
                        : 'MIN(price_variations.sell_price_inc_tax)';
                    // ad_price = Prime (61), prime_price = web (66) - frontend displays them vice versa
                    $products = $products->selectRaw("products.*, {$primePriceExpr} as ad_price, NULL as wishlist_id, {$webPriceExpr} as prime_price");
                } else {
                    $products = $products->selectRaw('products.*, MIN(price_variations.sell_price_inc_tax) as ad_price, NULL as wishlist_id, MIN(price_variations.sell_price_inc_tax) as prime_price');
                }
                $products = $products->groupBy('products.id')
                    ->havingRaw('ad_price > 0');

                // Apply price range filter for non-authenticated users (based on min price across variations)
                if ($minPrice !== null || $maxPrice !== null) {
                    if ($minPrice !== null && $maxPrice !== null) {
                        $products = $products->havingRaw('ad_price >= ? AND ad_price <= ?', [$minPrice, $maxPrice]);
                    } elseif ($minPrice !== null) {
                        $products = $products->havingRaw('ad_price >= ?', [$minPrice]);
                    } elseif ($maxPrice !== null) {
                        $products = $products->havingRaw('ad_price <= ?', [$maxPrice]);
                    }
                }

                //sorting (ad_price is MIN so ordering by it is correct)
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

                $products = $products->paginate($perPage, ['*'], 'page', $page);

                $productCollection = $products->getCollection();
                $relatedProducts = $productCollection->take(20);

                $this->appendCategoryBrandImageUrls($brands, $brand);

                return response()->json([
                    'status' => true,
                    'category_brands' => $brands,
                    'brand' => $brand,
                    'products' => [
                        'current_page' => $products->currentPage(),
                        'data' => CateLogResource::collection($productCollection),
                        'last_page' => $products->lastPage(),
                        'total' => $products->total(),
                        'from' => $products->firstItem(),
                        'per_page' => $products->perPage(),
                        'to' => $products->lastItem(),
                    ],
                    'relatedProducts' => CateLogResource::collection($relatedProducts),
                ]);
        }

        return response()->json(['status' => false, 'message' => 'Error while fetching Side menu']);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Side menu function failed', 'error' => $th->getMessage() . ' on ' . $th->getFile()]);
        }
    }

    /**
     * Get brand list for authenticated and non-authenticated users
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function brandList(Request $request)
    {
        $authData = $this->authCheck($request);
        if ($authData['status'] == true) {
           

            $brands = Brands::whereNotNull('slug')->where('location_id',config('services.b2b.location_id'))->get();

            return response()->json(['status' => true,'brands' => $brands]);
        } else if ($authData['status'] == false) {
            $brands = Brands::where('visibility','public')->whereNotNull('slug')->where('location_id',config('services.b2b.location_id'))->get();
            return response()->json(['status' => true, 'brands' => $brands]);
        }

        return response()->json(['status' => false, 'message' => 'Error while fetching Brands']);
    }

    /**
     * Get all brands for B2B location with their policy status (preferred/restricted/blocked).
     * Returns all brands; includes policy_type if brand has a policy set.
     * Can filter by policy_type query param.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function preferredBrands(Request $request)
    {
        $locationId = config('services.b2b.location_id');
        if (!$locationId) {
            return response()->json(['status' => true, 'preferred_brands' => []]);
        }

        try {
            // Get all brands for B2B location (same logic as brandList)
            $authData = $this->authCheck($request);
            $brandsQuery = Brands::where(function($q) use ($locationId) {
                $q->where('location_id', $locationId)->orWhereNull('location_id');
            })->whereNotNull('slug');

            // Apply visibility filter for non-authenticated users
            if ($authData['status'] == false) {
                $brandsQuery->where('visibility', 'public');
            }

            // Optional filter by policy_type - only show brands with that policy_type
            $policyTypeFilter = $request->query('policy_type');
            if ($policyTypeFilter && in_array($policyTypeFilter, ['preferred', 'restricted', 'blocked'])) {
                // Only show brands that have this policy_type set
                $brandsQuery->whereHas('preferredBrands', function($q) use ($locationId, $policyTypeFilter) {
                    $q->where('location_id', $locationId)
                      ->where('status', 'Active')
                      ->where('policy_type', $policyTypeFilter);
                });
            }

            $allBrands = $brandsQuery->orderBy('name')->get();

            // Get preferred_brands data for these brands
            try {
                $preferredBrandsData = PreferredBrand::where('location_id', $locationId)
                    ->where('status', 'Active')
                    ->whereIn('brand_id', $allBrands->pluck('id'))
                    ->get()
                    ->keyBy('brand_id');
            } catch (QueryException $e) {
                // preferred_brands table may not exist
                $preferredBrandsData = collect();
            }

            $brands = $allBrands->map(function ($brand) use ($preferredBrandsData) {
                $pb = $preferredBrandsData->get($brand->id);
                $logoUrl = $this->categoryBrandImageUrl($brand->logo ?? null);
                $bannerUrl = $this->categoryBrandImageUrl($brand->banner ?? null);
                
                return [
                    'id' => $pb->id ?? null, // preferred_brands row id (null if no policy set)
                    'brand_id' => $brand->id,
                    'name' => $brand->name,
                    'slug' => $brand->slug,
                    'description' => $brand->description,
                    'logo' => $brand->logo,
                    'banner' => $brand->banner,
                    'logo_url' => $logoUrl,
                    'banner_url' => $bannerUrl,
                    'sort_order' => $pb->sort_order ?? null,
                    'policy_type' => $pb->policy_type ?? null, // null if no policy set
                    'status' => $pb->status ?? null,
                    'contact_id' => $pb->contact_id ?? null,
                ];
            })->values();

        } catch (QueryException $e) {
            // Table may not exist yet (migration not run); return empty list
            if (in_array($e->getCode(), ['42S02', 1146], true) || str_contains($e->getMessage(), "doesn't exist")) {
                return response()->json(['status' => true, 'preferred_brands' => []]);
            }
            throw $e;
        }

        return response()->json([
            'status' => true,
            'preferred_brands' => $brands,
        ]);
    }

    /**
     * Get all brands grouped by policy_type (preferred, blocked, restricted).
     * Returns all brands organized by their policy status.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllBrandsByPolicyType(Request $request)
    {
        $locationId = config('services.b2b.location_id');
        if (!$locationId) {
            return response()->json([
                'status' => true,
                'preferred' => [],
                'blocked' => [],
                'restricted' => [],
            ]);
        }

        try {
            // Get all brands for B2B location
            $authData = $this->authCheck($request);
            $brandsQuery = Brands::where(function($q) use ($locationId) {
                $q->where('location_id', $locationId)->orWhereNull('location_id');
            })->whereNotNull('slug');

            // Apply visibility filter for non-authenticated users
            if ($authData['status'] == false) {
                $brandsQuery->where('visibility', 'public');
            }

            $allBrands = $brandsQuery->orderBy('name')->get();

            // Get preferred_brands data for these brands
            try {
                $preferredBrandsData = PreferredBrand::where('location_id', $locationId)
                    ->where('status', 'Active')
                    ->whereIn('brand_id', $allBrands->pluck('id'))
                    ->get()
                    ->keyBy('brand_id');
            } catch (QueryException $e) {
                // preferred_brands table may not exist
                $preferredBrandsData = collect();
            }

            // Initialize groups
            $preferred = [];
            $blocked = [];
            $restricted = [];

            foreach ($allBrands as $brand) {
                $pb = $preferredBrandsData->get($brand->id);
                
                // Only include brands that have a policy set
                if (!$pb || !$pb->policy_type) {
                    continue;
                }
                
                $logoUrl = $this->categoryBrandImageUrl($brand->logo ?? null);
                $bannerUrl = $this->categoryBrandImageUrl($brand->banner ?? null);
                
                $brandData = [
                    'id' => $pb->id, // preferred_brands row id
                    'brand_id' => $brand->id,
                    'name' => $brand->name,
                    'slug' => $brand->slug,
                    'description' => $brand->description,
                    'logo' => $brand->logo,
                    'banner' => $brand->banner,
                    'logo_url' => $logoUrl,
                    'banner_url' => $bannerUrl,
                    'sort_order' => $pb->sort_order,
                    'policy_type' => $pb->policy_type,
                    'status' => $pb->status,
                    'contact_id' => $pb->contact_id,
                ];

                // Group by policy_type
                switch ($pb->policy_type) {
                    case 'preferred':
                        $preferred[] = $brandData;
                        break;
                    case 'blocked':
                        $blocked[] = $brandData;
                        break;
                    case 'restricted':
                        $restricted[] = $brandData;
                        break;
                }
            }

            // Sort preferred, blocked, and restricted by sort_order
            usort($preferred, function($a, $b) {
                $sortA = $a['sort_order'] ?? 999999;
                $sortB = $b['sort_order'] ?? 999999;
                return $sortA <=> $sortB;
            });

            usort($blocked, function($a, $b) {
                $sortA = $a['sort_order'] ?? 999999;
                $sortB = $b['sort_order'] ?? 999999;
                return $sortA <=> $sortB;
            });

            usort($restricted, function($a, $b) {
                $sortA = $a['sort_order'] ?? 999999;
                $sortB = $b['sort_order'] ?? 999999;
                return $sortA <=> $sortB;
            });

        } catch (QueryException $e) {
            // Table may not exist yet (migration not run); return empty groups
            if (in_array($e->getCode(), ['42S02', 1146], true) || str_contains($e->getMessage(), "doesn't exist")) {
                return response()->json([
                    'status' => true,
                    'preferred' => [],
                    'blocked' => [],
                    'restricted' => [],
                ]);
            }
            throw $e;
        }

        return response()->json([
            'status' => true,
            'preferred' => $preferred,
            'blocked' => $blocked,
            'restricted' => $restricted,
        ]);
    }

    /**
     * Create or update preferred brands (replace list for B2B location).
     * Accepts one of:
     *   - brand_ids: [1, 2, 3]
     *   - brand_slugs: ["slug-a", "slug-b"]
     *   - brand_names: ["Brand A", "Brand B"]
     * Order in the array = sort_order (first = 0).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storePreferredBrands(Request $request)
    {
        $request->validate([
            'brand_ids' => 'nullable|array',
            'brand_ids.*' => 'integer|exists:brands,id',
            'brand_slugs' => 'nullable|array',
            'brand_slugs.*' => 'string',
            'brand_names' => 'nullable|array',
            'brand_names.*' => 'string',
            'status' => 'nullable|in:Draft,Pending Approval,Active,Rejected,Expired,Suspended',
            'policy_type' => 'nullable|in:preferred,blocked,restricted',
            'contact_id' => 'nullable|integer|exists:contacts,id',
        ]);

        $locationId = config('services.b2b.location_id');
        if (!$locationId) {
            return response()->json(['status' => false, 'message' => 'B2B location not configured.'], 400);
        }

        $brandIds = $this->resolvePreferredBrandIds($request, $locationId);
        if ($brandIds === null) {
            return response()->json([
                'status' => false,
                'message' => 'Provide exactly one of: brand_ids, brand_slugs, or brand_names (non-empty array).',
            ], 422);
        }

        if (empty($brandIds)) {
            return response()->json([
                'status' => false,
                'message' => 'No valid brands found for the given ids, slugs, or names.',
            ], 422);
        }

        $status = $request->input('status', 'Draft'); // Default to Draft
        $policyType = $request->input('policy_type', 'preferred'); // Default to preferred
        
        // Get contact_id from authenticated user if not provided
        $contactId = $request->input('contact_id');
        if (!$contactId) {
            $user = Auth::guard('api')->user();
            if ($user) {
                $contactId = $user->id; // Assuming user is a Contact
            }
        }

        // Update existing or create new preferred brands
        foreach ($brandIds as $sortOrder => $brandId) {
            PreferredBrand::updateOrCreate(
                [
                    'location_id' => $locationId,
                    'brand_id' => $brandId,
                ],
                [
                    'sort_order' => $sortOrder,
                    'status' => $status,
                    'policy_type' => $policyType,
                    'contact_id' => $contactId,
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Preferred brands updated.',
        ]);
    }

    /**
     * Resolve request input to an ordered list of brand IDs for the given location.
     * Returns null if none of brand_ids, brand_slugs, brand_names provided; otherwise ordered ids.
     *
     * @param Request $request
     * @param int $locationId
     * @return array<int>|null
     */
    private function resolvePreferredBrandIds(Request $request, int $locationId): ?array
    {
        if ($request->filled('brand_ids')) {
            $ids = $request->brand_ids;
            // Keep only brands that belong to this location (or null location)
            $existing = Brands::where('location_id', $locationId)
                ->orWhereNull('location_id')
                ->whereIn('id', $ids)
                ->pluck('id')
                ->flip()
                ->all();
            return array_values(array_filter($ids, function ($id) use ($existing) {
                return isset($existing[$id]);
            }));
        }

        if ($request->filled('brand_slugs')) {
            $slugs = array_values(array_filter(array_map('trim', $request->brand_slugs)));
            if (empty($slugs)) {
                return [];
            }
            $brands = Brands::where('location_id', $locationId)
                ->orWhereNull('location_id')
                ->whereIn('slug', $slugs)
                ->get()
                ->keyBy('slug');
            $ordered = [];
            foreach ($slugs as $slug) {
                if (isset($brands[$slug])) {
                    $ordered[] = $brands[$slug]->id;
                }
            }
            return $ordered;
        }

        if ($request->filled('brand_names')) {
            $names = array_values(array_filter(array_map('trim', $request->brand_names)));
            if (empty($names)) {
                return [];
            }
            $brands = Brands::where('location_id', $locationId)
                ->orWhereNull('location_id')
                ->whereIn('name', $names)
                ->get()
                ->keyBy('name');
            $ordered = [];
            foreach ($names as $name) {
                if (isset($brands[$name])) {
                    $ordered[] = $brands[$name]->id;
                }
            }
            return $ordered;
        }

        return null;
    }

    /**
     * Remove one brand from preferred list (staff auth).
     *
     * @param int $brandId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePreferredBrand($brandId)
    {
        $locationId = config('services.b2b.location_id');
        if (!$locationId) {
            return response()->json(['status' => false, 'message' => 'B2B location not configured.'], 400);
        }

        $deleted = PreferredBrand::where('location_id', $locationId)
            ->where('brand_id', $brandId)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => $deleted ? 'Brand removed from preferred list.' : 'Brand was not in preferred list.',
        ]);
    }

    /**
     * Get all preferred brands with all statuses (admin only - for approval workflow).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPreferredBrands(Request $request)
    {
        $locationId = config('services.b2b.location_id');
        if (!$locationId) {
            return response()->json(['status' => true, 'preferred_brands' => []]);
        }

        try {
            $query = PreferredBrand::where('location_id', $locationId);
            // Optional filter by policy_type (e.g. ?policy_type=restricted)
            $policyType = $request->query('policy_type');
            if ($policyType && in_array($policyType, ['preferred', 'restricted', 'blocked'])) {
                $query->where('policy_type', $policyType);
            }
            $preferred = $query->orderBy('status')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->with('brand')
                ->get();
        } catch (QueryException $e) {
            if (in_array($e->getCode(), ['42S02', 1146], true) || str_contains($e->getMessage(), "doesn't exist")) {
                return response()->json(['status' => true, 'preferred_brands' => []]);
            }
            throw $e;
        }

        $brands = $preferred->map(function ($pb) {
            $brand = $pb->brand;
            if (!$brand) {
                return null;
            }
            $brand->logo_url = $this->categoryBrandImageUrl($brand->logo ?? null);
            $brand->banner_url = $this->categoryBrandImageUrl($brand->banner ?? null);
            return [
                'id' => $pb->id,
                'brand_id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'description' => $brand->description,
                'logo' => $brand->logo,
                'banner' => $brand->banner,
                'logo_url' => $brand->logo_url,
                'banner_url' => $brand->banner_url,
                'sort_order' => $pb->sort_order,
                'status' => $pb->status ?? 'Draft',
                'policy_type' => $pb->policy_type ?? 'preferred',
                'contact_id' => $pb->contact_id,
            ];
        })->filter()->values();

        return response()->json([
            'status' => true,
            'preferred_brands' => $brands,
        ]);
    }

    /**
     * Get all restricted brands (admin) - same as preferred-brands/admin/all filtered by policy_type=restricted.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRestrictedBrands(Request $request)
    {
        $request->merge(['policy_type' => 'restricted']);
        return $this->getAllPreferredBrands($request);
    }

    /**
     * Update status of a preferred brand (admin approval workflow).
     * {id} can be either preferred_brands.id (row id) or brand_id.
     *
     * @param Request $request
     * @param int $id Preferred brand row id OR brand_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePreferredBrandStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'nullable|in:Draft,Pending Approval,Active,Rejected,Expired,Suspended',
            'policy_type' => 'nullable|in:preferred,blocked,restricted',
        ]);

        $locationId = config('services.b2b.location_id');
        if (!$locationId) {
            return response()->json(['status' => false, 'message' => 'B2B location not configured.'], 400);
        }

        // Try by preferred_brands.id first, then by brand_id
        $preferredBrand = PreferredBrand::where('location_id', $locationId)
            ->where('id', $id)
            ->first();

        if (!$preferredBrand) {
            $preferredBrand = PreferredBrand::where('location_id', $locationId)
                ->where('brand_id', $id)
                ->first();
        }

        if (!$preferredBrand) {
            return response()->json([
                'status' => false,
                'message' => 'Preferred brand not found. Use GET /api/preferred-brands/admin/all to list entries (id or brand_id).',
            ], 404);
        }

        if ($request->has('status')) {
            $preferredBrand->status = $request->status;
        }
        if ($request->has('policy_type')) {
            $preferredBrand->policy_type = $request->policy_type;
        }
        $preferredBrand->save();

        return response()->json([
            'status' => true,
            'message' => 'Preferred brand updated.',
            'preferred_brand' => [
                'id' => $preferredBrand->id,
                'brand_id' => $preferredBrand->brand_id,
                'status' => $preferredBrand->status,
                'policy_type' => $preferredBrand->policy_type ?? 'preferred',
                'contact_id' => $preferredBrand->contact_id,
            ],
        ]);
    }

    /**
     * Get preferred categories for the frontend (Amazon-style "Prefer categories").
     * These categories can be shown first in search results / "Preferred by your Organization".
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function preferredCategories(Request $request)
    {
        $locationId = config('services.b2b.location_id');
        if (!$locationId) {
            return response()->json(['status' => true, 'preferred_categories' => []]);
        }

        try {
            // Get all categories for B2B location
            $authData = $this->authCheck($request);
            $categoriesQuery = Category::where(function($q) use ($locationId) {
                $q->where('location_id', $locationId)->orWhereNull('location_id');
            });

            // Apply visibility filter for non-authenticated users
            if ($authData['status'] == false) {
                $categoriesQuery->where('visibility', 'public');
            }

            // Optional filter by policy_type - only show categories with that policy_type
            $policyTypeFilter = $request->query('policy_type');
            if ($policyTypeFilter && in_array($policyTypeFilter, ['preferred', 'restricted', 'blocked'])) {
                // Only show categories that have this policy_type set (using subquery instead of relationship)
                $categoriesQuery->whereIn('id', function($query) use ($locationId, $policyTypeFilter) {
                    $query->select('category_id')
                        ->from('preferred_categories')
                        ->where('location_id', $locationId)
                        ->where('status', 'Active')
                        ->where('policy_type', $policyTypeFilter);
                });
            }

            $allCategories = $categoriesQuery->orderBy('name')->get();

            // Get preferred_categories data for these categories
            try {
                $preferredCategoriesData = PreferredCategory::where('location_id', $locationId)
                    ->where('status', 'Active')
                    ->whereIn('category_id', $allCategories->pluck('id'))
                    ->get()
                    ->keyBy('category_id');
            } catch (QueryException $e) {
                // preferred_categories table may not exist
                $preferredCategoriesData = collect();
            }

            $categories = $allCategories->map(function ($category) use ($preferredCategoriesData) {
                $pc = $preferredCategoriesData->get($category->id);
                $logoUrl = $this->categoryBrandImageUrl($category->logo ?? null);
                $bannerUrl = $this->categoryBrandImageUrl($category->banner ?? null);
                $categoryBannerUrl = $this->categoryBrandImageUrl($category->category_banner ?? null);
                
                return [
                    'id' => $pc->id ?? null, // preferred_categories row id (null if no policy set)
                    'category_id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug ?? null,
                    'parent_id' => $category->parent_id,
                    'description' => $category->body ?? null,
                    'logo' => $category->logo,
                    'banner' => $category->banner,
                    'category_banner' => $category->category_banner ?? null,
                    'logo_url' => $logoUrl,
                    'banner_url' => $bannerUrl,
                    'category_banner_url' => $categoryBannerUrl,
                    'sort_order' => $pc->sort_order ?? null,
                    'policy_type' => $pc->policy_type ?? null, // null if no policy set
                    'status' => $pc->status ?? null,
                    'contact_id' => $pc->contact_id ?? null,
                ];
            })->values();

        } catch (QueryException $e) {
            // Table may not exist yet (migration not run); return empty list
            if (in_array($e->getCode(), ['42S02', 1146], true) || str_contains($e->getMessage(), "doesn't exist")) {
                return response()->json(['status' => true, 'preferred_categories' => []]);
            }
            throw $e;
        }

        return response()->json([
            'status' => true,
            'preferred_categories' => $categories,
        ]);
    }

    /**
     * Get all categories grouped by policy_type (preferred, blocked, restricted).
     * Returns all categories organized by their policy status.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCategoriesByPolicyType(Request $request)
    {
        $locationId = config('services.b2b.location_id');
        if (!$locationId) {
            return response()->json([
                'status' => true,
                'preferred' => [],
                'blocked' => [],
                'restricted' => [],
            ]);
        }

        try {
            // Get all categories for B2B location
            $authData = $this->authCheck($request);
            $categoriesQuery = Category::where(function($q) use ($locationId) {
                $q->where('location_id', $locationId)->orWhereNull('location_id');
            });

            // Apply visibility filter for non-authenticated users
            if ($authData['status'] == false) {
                $categoriesQuery->where('visibility', 'public');
            }

            $allCategories = $categoriesQuery->orderBy('name')->get();

            // Get preferred_categories data for these categories
            try {
                $preferredCategoriesData = PreferredCategory::where('location_id', $locationId)
                    ->where('status', 'Active')
                    ->whereIn('category_id', $allCategories->pluck('id'))
                    ->get()
                    ->keyBy('category_id');
            } catch (QueryException $e) {
                // preferred_categories table may not exist
                $preferredCategoriesData = collect();
            }

            // Initialize groups
            $preferred = [];
            $blocked = [];
            $restricted = [];

            foreach ($allCategories as $category) {
                $pc = $preferredCategoriesData->get($category->id);
                
                // Only include categories that have a policy set
                if (!$pc || !$pc->policy_type) {
                    continue;
                }
                
                $logoUrl = $this->categoryBrandImageUrl($category->logo ?? null);
                $bannerUrl = $this->categoryBrandImageUrl($category->banner ?? null);
                $categoryBannerUrl = $this->categoryBrandImageUrl($category->category_banner ?? null);
                
                $categoryData = [
                    'id' => $pc->id, // preferred_categories row id
                    'category_id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug ?? null,
                    'parent_id' => $category->parent_id,
                    'description' => $category->body ?? null,
                    'logo' => $category->logo,
                    'banner' => $category->banner,
                    'category_banner' => $category->category_banner ?? null,
                    'logo_url' => $logoUrl,
                    'banner_url' => $bannerUrl,
                    'category_banner_url' => $categoryBannerUrl,
                    'sort_order' => $pc->sort_order,
                    'policy_type' => $pc->policy_type,
                    'status' => $pc->status,
                    'contact_id' => $pc->contact_id,
                ];

                // Group by policy_type
                switch ($pc->policy_type) {
                    case 'preferred':
                        $preferred[] = $categoryData;
                        break;
                    case 'blocked':
                        $blocked[] = $categoryData;
                        break;
                    case 'restricted':
                        $restricted[] = $categoryData;
                        break;
                }
            }

            // Sort preferred, blocked, and restricted by sort_order
            usort($preferred, function($a, $b) {
                $sortA = $a['sort_order'] ?? 999999;
                $sortB = $b['sort_order'] ?? 999999;
                return $sortA <=> $sortB;
            });

            usort($blocked, function($a, $b) {
                $sortA = $a['sort_order'] ?? 999999;
                $sortB = $b['sort_order'] ?? 999999;
                return $sortA <=> $sortB;
            });

            usort($restricted, function($a, $b) {
                $sortA = $a['sort_order'] ?? 999999;
                $sortB = $b['sort_order'] ?? 999999;
                return $sortA <=> $sortB;
            });

        } catch (QueryException $e) {
            // Table may not exist yet (migration not run); return empty groups
            if (in_array($e->getCode(), ['42S02', 1146], true) || str_contains($e->getMessage(), "doesn't exist")) {
                return response()->json([
                    'status' => true,
                    'preferred' => [],
                    'blocked' => [],
                    'restricted' => [],
                ]);
            }
            throw $e;
        }

        return response()->json([
            'status' => true,
            'preferred' => $preferred,
            'blocked' => $blocked,
            'restricted' => $restricted,
        ]);
    }

    /**
     * Create or update preferred categories (replace list for B2B location).
     * Accepts one of:
     *   - category_ids: [1, 2, 3]
     *   - category_slugs: ["slug-a", "slug-b"]
     *   - category_names: ["Category A", "Category B"]
     * Order in the array = sort_order (first = 0).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storePreferredCategories(Request $request)
    {
        $request->validate([
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'category_slugs' => 'nullable|array',
            'category_slugs.*' => 'string',
            'category_names' => 'nullable|array',
            'category_names.*' => 'string',
            'status' => 'nullable|in:Draft,Pending Approval,Active,Rejected,Expired,Suspended',
            'policy_type' => 'nullable|in:preferred,blocked,restricted',
            'contact_id' => 'nullable|integer|exists:contacts,id',
        ]);

        $locationId = config('services.b2b.location_id');
        if (!$locationId) {
            return response()->json(['status' => false, 'message' => 'B2B location not configured.'], 400);
        }

        $categoryIds = $this->resolvePreferredCategoryIds($request, $locationId);
        if ($categoryIds === null) {
            return response()->json([
                'status' => false,
                'message' => 'Provide exactly one of: category_ids, category_slugs, or category_names (non-empty array).',
            ], 422);
        }

        if (empty($categoryIds)) {
            return response()->json([
                'status' => false,
                'message' => 'No valid categories found for the given ids, slugs, or names.',
            ], 422);
        }

        $status = $request->input('status', 'Draft'); // Default to Draft
        $policyType = $request->input('policy_type', 'preferred'); // Default to preferred
        
        // Get contact_id from authenticated user if not provided
        $contactId = $request->input('contact_id');
        if (!$contactId) {
            $user = Auth::guard('api')->user();
            if ($user) {
                $contactId = $user->id; // Assuming user is a Contact
            }
        }

        // Update existing or create new preferred categories
        foreach ($categoryIds as $sortOrder => $categoryId) {
            PreferredCategory::updateOrCreate(
                [
                    'location_id' => $locationId,
                    'category_id' => $categoryId,
                ],
                [
                    'sort_order' => $sortOrder,
                    'status' => $status,
                    'policy_type' => $policyType,
                    'contact_id' => $contactId,
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Preferred categories updated.',
        ]);
    }

    /**
     * Resolve request input to an ordered list of category IDs for the given location.
     *
     * @param Request $request
     * @param int $locationId
     * @return array<int>|null
     */
    private function resolvePreferredCategoryIds(Request $request, int $locationId): ?array
    {
        if ($request->filled('category_ids')) {
            $ids = $request->category_ids;
            $existing = Category::where('location_id', $locationId)
                ->orWhereNull('location_id')
                ->whereIn('id', $ids)
                ->pluck('id')
                ->flip()
                ->all();
            return array_values(array_filter($ids, function ($id) use ($existing) {
                return isset($existing[$id]);
            }));
        }

        if ($request->filled('category_slugs')) {
            $slugs = array_values(array_filter(array_map('trim', $request->category_slugs)));
            if (empty($slugs)) {
                return [];
            }
            $categories = Category::where('location_id', $locationId)
                ->orWhereNull('location_id')
                ->whereIn('slug', $slugs)
                ->get()
                ->keyBy('slug');
            $ordered = [];
            foreach ($slugs as $slug) {
                if (isset($categories[$slug])) {
                    $ordered[] = $categories[$slug]->id;
                }
            }
            return $ordered;
        }

        if ($request->filled('category_names')) {
            $names = array_values(array_filter(array_map('trim', $request->category_names)));
            if (empty($names)) {
                return [];
            }
            $categories = Category::where('location_id', $locationId)
                ->orWhereNull('location_id')
                ->whereIn('name', $names)
                ->get()
                ->keyBy('name');
            $ordered = [];
            foreach ($names as $name) {
                if (isset($categories[$name])) {
                    $ordered[] = $categories[$name]->id;
                }
            }
            return $ordered;
        }

        return null;
    }

    /**
     * Remove one category from preferred list.
     *
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePreferredCategory($categoryId)
    {
        $locationId = config('services.b2b.location_id');
        if (!$locationId) {
            return response()->json(['status' => false, 'message' => 'B2B location not configured.'], 400);
        }

        $deleted = PreferredCategory::where('location_id', $locationId)
            ->where('category_id', $categoryId)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => $deleted ? 'Category removed from preferred list.' : 'Category was not in preferred list.',
        ]);
    }

    /**
     * Get all preferred categories with all statuses (admin only - for approval workflow).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPreferredCategories(Request $request)
    {
        $locationId = config('services.b2b.location_id');
        if (!$locationId) {
            return response()->json(['status' => true, 'preferred_categories' => []]);
        }

        try {
            $query = PreferredCategory::where('location_id', $locationId);
            // Optional filter by policy_type (e.g. ?policy_type=restricted)
            $policyType = $request->query('policy_type');
            if ($policyType && in_array($policyType, ['preferred', 'restricted', 'blocked'])) {
                $query->where('policy_type', $policyType);
            }
            $preferred = $query->orderBy('status')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->with('category')
                ->get();
        } catch (QueryException $e) {
            if (in_array($e->getCode(), ['42S02', 1146], true) || str_contains($e->getMessage(), "doesn't exist")) {
                return response()->json(['status' => true, 'preferred_categories' => []]);
            }
            throw $e;
        }

        $categories = $preferred->map(function ($pc) {
            $category = $pc->category;
            if (!$category) {
                return null;
            }
            $logoUrl = $this->categoryBrandImageUrl($category->logo ?? null);
            $bannerUrl = $this->categoryBrandImageUrl($category->banner ?? null);
            $categoryBannerUrl = $this->categoryBrandImageUrl($category->category_banner ?? null);
            return [
                'id' => $pc->id,
                'category_id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug ?? null,
                'parent_id' => $category->parent_id,
                'description' => $category->body ?? null,
                'logo' => $category->logo,
                'banner' => $category->banner,
                'category_banner' => $category->category_banner ?? null,
                'logo_url' => $logoUrl,
                'banner_url' => $bannerUrl,
                'category_banner_url' => $categoryBannerUrl,
                'sort_order' => $pc->sort_order,
                'status' => $pc->status ?? 'Draft',
                'policy_type' => $pc->policy_type ?? 'preferred',
                'contact_id' => $pc->contact_id,
            ];
        })->filter()->values();

        return response()->json([
            'status' => true,
            'preferred_categories' => $categories,
        ]);
    }

    /**
     * Get all restricted categories (admin) - same as preferred-categories/admin/all filtered by policy_type=restricted.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRestrictedCategories(Request $request)
    {
        $request->merge(['policy_type' => 'restricted']);
        return $this->getAllPreferredCategories($request);
    }

    /**
     * Update status of a preferred category (admin approval workflow).
     * {id} can be either preferred_categories.id (row id) or category_id.
     *
     * @param Request $request
     * @param int $id Preferred category row id OR category_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePreferredCategoryStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'nullable|in:Draft,Pending Approval,Active,Rejected,Expired,Suspended',
            'policy_type' => 'nullable|in:preferred,blocked,restricted',
        ]);

        $locationId = config('services.b2b.location_id');
        if (!$locationId) {
            return response()->json(['status' => false, 'message' => 'B2B location not configured.'], 400);
        }

        // Try by preferred_categories.id first, then by category_id
        $preferredCategory = PreferredCategory::where('location_id', $locationId)
            ->where('id', $id)
            ->first();

        if (!$preferredCategory) {
            $preferredCategory = PreferredCategory::where('location_id', $locationId)
                ->where('category_id', $id)
                ->first();
        }

        if (!$preferredCategory) {
            return response()->json([
                'status' => false,
                'message' => 'Preferred category not found. Use GET /api/preferred-categories/admin/all to list entries (id or category_id).',
            ], 404);
        }

        if ($request->has('status')) {
            $preferredCategory->status = $request->status;
        }
        if ($request->has('policy_type')) {
            $preferredCategory->policy_type = $request->policy_type;
        }
        $preferredCategory->save();

        return response()->json([
            'status' => true,
            'message' => 'Preferred category updated.',
            'preferred_category' => [
                'id' => $preferredCategory->id,
                'category_id' => $preferredCategory->category_id,
                'status' => $preferredCategory->status,
                'policy_type' => $preferredCategory->policy_type ?? 'preferred',
                'contact_id' => $preferredCategory->contact_id,
            ],
        ]);
    }

    /**
     * Check if user is authenticated
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return array
     */
    private function authCheck($request)
    {
        try {
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
        } catch (\League\OAuth2\Server\Exception\OAuthServerException | \Lcobucci\JWT\Validation\RequiredConstraintsViolated $e) {
            return [
                'status' => false,
                'message' => 'Invalid or expired token',
            ];
        }
    }

    /**
     * Get products for multiple categories
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @param string $slugs
     * @return \Illuminate\Http\JsonResponse
     */
    public function multiCategory(Request $request, $slugs){
        $slugArray = explode(',', $slugs); 
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'popularity');
        $page = $request->query('page', 1);
        
        // Check if this is a gift card category request
        $isGiftCardCategory = in_array('gift-cards', $slugArray);
    
        try {
            // Auth check
            $authData = $this->authCheck($request);
            
            if ($isGiftCardCategory) {
                // Return only gift cards for gift card category
                return $this->getGiftCards($request);
            }
            
            if ($authData['status'] == true) {
                $contact = $authData['user'];
                $priceTier = $contact->price_tier;
                $priceGroupId = key($priceTier);  // Group ID for pricing
    
                $products = Product::with('webcategories', 'brand','product_locations','product_states')
                ->wherehas('product_locations',function($q) {
                    $q->where('product_locations.location_id',config('services.b2b.location_id'));
                })
                    ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                    ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                        $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                            ->where('variation_group_prices.price_group_id', '=', $priceGroupId); // Get price based on tier
                    })
                    ->leftJoin('wishlists', function ($join) use ($contact) {
                        $join->on('products.id', '=', 'wishlists.product_id')
                            ->where('wishlists.user_id', '=', $contact->id);
                    })
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->forCatalogListing($slugArray)
                    ->whereHas('webcategories', function ($query) use ($slugArray) {
                        $query->whereIn('slug', $slugArray)
                                ->where('category_type', 'product');
                    })
                    ->selectRaw('
                            products.*, 
                            MIN(variation_group_prices.price_inc_tax) as ad_price,
                            wishlists.id as wishlist_id
                        ')  
                    ->groupBy('products.id', 'wishlists.id')
                    ->havingRaw('ad_price > 0'); // Group by product ID to ensure unique products

                // Sorting logic
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
    
                // Apply pagination
                $products = $products->paginate($perPage, ['*'], 'page', $page);
            } else {
                // Query for non-authenticated users (public visibility)
                $products = Product::with('webcategories', 'brand','product_locations','product_states')
                ->wherehas('product_locations',function($q) {
                    $q->where('product_locations.location_id',config('services.b2b.location_id'));
                })
                    ->where('productVisibility', 'public')
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->forCatalogListing($slugArray)
                    ->whereHas('webcategories', function ($query) use ($slugArray) {
                        $query->whereIn('slug', $slugArray) // Filter by multiple slugs
                                ->where('category_type', 'product')
                                ->where('visibility', 'public');
                    })
                    ->whereDoesntHave('webcategories', function ($query) {
                        $query->where('visibility', 'protected');
                    })
                    ->leftJoin('variations as price_variations', 'products.id', '=', 'price_variations.product_id')
                    ->leftJoin('variation_location_details as vld', 'price_variations.id', '=', 'vld.variation_id')
                    ->where('vld.location_id', config('services.b2b.location_id'))
                    ->select('products.*', 'price_variations.sell_price_inc_tax as ad_price'); //sell price for non-auth users

                // Sorting logic
                switch ($sortBy) {
                    case 'low-to-high':
                        $products = $products->orderBy('price_variations.sell_price_inc_tax', 'asc');
                        break;
                    case 'high-to-low':
                        $products = $products->orderBy('price_variations.sell_price_inc_tax', 'desc');
                        break;
                    case 'top-selling':
                        $products = $products->orderBy('top_selling', 'desc');
                        break;
                    case 'latest':
                    default:
                        $products = $products->orderBy('products.created_at', 'desc');
                        break;
                }
    
                // Apply pagination
                $products = $products->paginate($perPage, ['*'], 'page', $page);
            }
            return response()->json([
                'status' => true,
                'data' => [
                    'current_page' => $products->currentPage(),
                    'data' => CateLogResource::collection($products->getCollection()),
                    'last_page' => $products->lastPage(),
                    'total' => $products->total(),
                    // 'first_page_url' => $products->url(1),
                    'from' => $products->firstItem(),
                    // 'last_page_url' => $products->url($products->lastPage()),
                    // 'next_page_url' => $products->nextPageUrl(),
                    'per_page' => $products->perPage(),
                    // 'prev_page_url' => $products->previousPageUrl(),
                    'to' => $products->lastItem(),
                ]
            ]);
            // Return the response with the paginated products
            // return response()->json(['status' => true, 'data' => $products]);
    
        } catch (\Throwable $th) {
            // Catch any errors and return a response
            return response()->json(['status' => false, 'message' => 'categoryProducts function failed', 'error' => $th->getMessage() . ' on ' . $th->getFile()]);
        }
    }

    /**
     * Get products for a brand
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function brandProducts(Request $request, $slug)
    {
        $slugArray = explode(',', $slug);
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'popularity');
        $page = $request->query('page', 1);
        $categorySlugs = $request->query('category',false);
        $byState = $request->query('byState',false);

        if($categorySlugs){
            $categorySlugs = explode(',', $categorySlugs);
        }
        if($byState){
            $byState = explode(',', $byState);
        }
        try {
            //auth check 
            $authData = $this->authCheck($request);
            if ($authData['status'] == true) {
                $contact = $authData['user'];
                $priceTier = $contact->price_tier;
                $priceGroupId =  key($priceTier);  //group id 

                //query 
                $products = Product::with('webcategories', 'brand','product_locations','product_states')
                ->wherehas('product_locations',function($q) {
                    $q->where('product_locations.location_id',config('services.b2b.location_id'));
                })
                    ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                    ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                        $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                            ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                    })
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->forCatalogListing(null)
                    ->whereHas('brand', function ($query) use ($slugArray) {
                        $query->whereIn('slug', $slugArray);
                    })
                    ->when(!empty($categorySlugs), function($query) use ($categorySlugs) {
                        $query->whereHas('webcategories', function($q) use ($categorySlugs) {
                            $q->whereIn('slug', $categorySlugs);
                        });
                    })
                    ->when(!empty($byState), function($query) use ($byState) {
                        foreach ($byState as $state) {
                            $query->where(function ($q) use ($state) {
                                // Products available in all states
                                $q->where('products.state_check', 'all')
                                    // OR products that are specifically allowed in this state
                                    ->orWhere(function ($q) use ($state) {
                                        $q->where('products.state_check', 'in')
                                            ->whereHas('product_states', function ($q) use ($state) {
                                                $q->where('state', strtoupper($state));
                                            });
                                    });
                            });
                        }
                    })
                    ->selectRaw('
                    products.*, 
                    MIN(variation_group_prices.price_inc_tax) as ad_price
                ') // Use MIN to get the lowest ad_price
                    ->groupBy('products.id')
                    ->havingRaw('ad_price > 0');

                //sorting 
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
                //final pagination 
                $products = $products->paginate($perPage, ['*'], 'page', $page);
            } else if ($authData['status'] == false) {
                $products = Product::with('webcategories', 'brand','product_locations','product_states')
                ->wherehas('product_locations',function($q) {
                    $q->where('product_locations.location_id',config('services.b2b.location_id'));
                })
                    ->where('productVisibility', 'public')
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->forCatalogListing(null)
                    ->when(!empty($categorySlugs), function($query) use ($categorySlugs) {
                        $query->whereHas('webcategories', function ($q) use ($categorySlugs) {
                            $q->whereIn('slug', $categorySlugs)
                                ->where('category_type', 'product')
                                ->where('visibility', 'public');
                        });
                    })
                    ->whereDoesntHave('webcategories', function ($query) {
                        $query->where('visibility', 'protected');
                    })
                    ->where('is_inactive', 0)
                    ->whereHas('brand', function ($query) use ($slugArray   ) {
                        $query->whereIn('slug', $slugArray)
                            ->where('visibility', 'public');
                    })
                    ->when(!empty($byState), function($query) use ($byState) {
                        foreach ($byState as $state) {
                            $query->where(function ($q) use ($state) {
                                // Products available in all states
                                $q->where('products.state_check', 'all')
                                    // OR products that are specifically allowed in this state
                                    ->orWhere(function ($q) use ($state) {
                                        $q->where('products.state_check', 'in')
                                            ->whereHas('product_states', function ($q) use ($state) {
                                                $q->where('state', strtoupper($state));
                                            });
                                    });
                            });
                        }
                    })
                    ->leftJoin('variations as price_variations', 'products.id', '=', 'price_variations.product_id')
                    ->leftJoin('variation_location_details as vld', 'price_variations.id', '=', 'vld.variation_id')
                    ->where('vld.location_id', config('services.b2b.location_id'))
                    ->select('products.*', 'price_variations.sell_price_inc_tax as ad_price'); //sell price for non-auth users
                //sorting 
                switch ($sortBy) {
                    case 'low-to-high':
                        $products = $products->orderBy('price_variations.sell_price_inc_tax', 'asc');
                        break;
                    case 'high-to-low':
                        $products = $products->orderBy('price_variations.sell_price_inc_tax', 'desc');
                        break;
                    case 'top-selling':
                        $products = $products->orderBy('top_selling', 'desc');
                        break;
                    case 'latest':
                    default:
                        $products = $products->orderBy('products.created_at', 'desc');
                        break;
                }
                $products = $products->paginate($perPage, ['*'], 'page', $page);
            }
            return response()->json([
                'status' => true,
                'data' => [
                    'current_page' => $products->currentPage(),
                    'data' => ProductResource::collection($products->getCollection()),
                    'last_page' => $products->lastPage(),
                    'total' => $products->total(),
                    // 'first_page_url' => $products->url(1),
                    'from' => $products->firstItem(),
                    // 'last_page_url' => $products->url($products->lastPage()),
                    // 'next_page_url' => $products->nextPageUrl(),
                    'per_page' => $products->perPage(),
                    // 'prev_page_url' => $products->previousPageUrl(),
                    'to' => $products->lastItem(),
                ]
            ]);
            // return response()->json(['status' => true, 'data' => $products]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Brand function failed', 'error' => $th->getMessage() . ' on ' . $th->getFile()]);
        }
    }

    /**
     * Get products with at least one product per brand.
     * Returns one representative product per brand (same visibility/location rules as catalog).
     *
     * GET /api/products/one-per-brand
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productsOnePerBrand(Request $request)
    {
        try {
            $authData = $this->authCheck($request);
            $sortBy = $request->query('sort', 'latest');

            if ($authData['status'] == true) {
                $contact = $authData['user'];
                $priceTier = $contact->price_tier;
                $priceGroupId = key($priceTier);

                $baseQuery = Product::query()
                    ->whereHas('product_locations', function ($q) {
                        $q->where('product_locations.location_id', config('services.b2b.location_id'));
                    })
                    ->whereNotNull('products.brand_id')
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->forCatalogListing(null);

                $representativeIds = (clone $baseQuery)
                    ->select(DB::raw('MIN(products.id) as id'))
                    ->groupBy('products.brand_id')
                    ->pluck('id')
                    ->toArray();

                if (empty($representativeIds)) {
                    return response()->json([
                        'status' => true,
                        'data' => [
                            'data' => [],
                            'total' => 0,
                        ]
                    ]);
                }

                $products = Product::with('webcategories', 'brand', 'product_locations', 'product_states')
                    ->whereHas('product_locations', function ($q) {
                        $q->where('product_locations.location_id', config('services.b2b.location_id'));
                    })
                    ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                    ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                        $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                            ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                    })
                    ->leftJoin('wishlists', function ($join) use ($contact) {
                        $join->on('products.id', '=', 'wishlists.product_id')
                            ->where('wishlists.user_id', '=', $contact->id);
                    })
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->forCatalogListing(null)
                    ->whereIn('products.id', $representativeIds)
                    ->selectRaw('
                        products.*,
                        MIN(variation_group_prices.price_inc_tax) as ad_price,
                        wishlists.id as wishlist_id
                    ')
                    ->groupBy('products.id', 'wishlists.id')
                    ->havingRaw('MIN(variation_group_prices.price_inc_tax) > 0');
            } else {
                $baseQuery = Product::query()
                    ->whereHas('product_locations', function ($q) {
                        $q->where('product_locations.location_id', config('services.b2b.location_id'));
                    })
                    ->where('productVisibility', 'public')
                    ->whereNotNull('products.brand_id')
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->forCatalogListing(null)
                    ->whereDoesntHave('webcategories', function ($query) {
                        $query->where('visibility', 'protected');
                    })
                    ->whereHas('brand', function ($query) {
                        $query->where('visibility', 'public');
                    });

                $representativeIds = (clone $baseQuery)
                    ->select(DB::raw('MIN(products.id) as id'))
                    ->groupBy('products.brand_id')
                    ->pluck('id')
                    ->toArray();

                if (empty($representativeIds)) {
                    return response()->json([
                        'status' => true,
                        'data' => [
                            'data' => [],
                            'total' => 0,
                        ]
                    ]);
                }

                $products = Product::with('webcategories', 'brand', 'product_locations', 'product_states')
                    ->whereHas('product_locations', function ($q) {
                        $q->where('product_locations.location_id', config('services.b2b.location_id'));
                    })
                    ->where('productVisibility', 'public')
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->forCatalogListing(null)
                    ->whereDoesntHave('webcategories', function ($query) {
                        $query->where('visibility', 'protected');
                    })
                    ->whereHas('brand', function ($query) {
                        $query->where('visibility', 'public');
                    })
                    ->leftJoin('variations as price_variations', 'products.id', '=', 'price_variations.product_id')
                    ->leftJoin('variation_location_details as vld', 'price_variations.id', '=', 'vld.variation_id')
                    ->where('vld.location_id', config('services.b2b.location_id'))
                    ->whereIn('products.id', $representativeIds)
                    ->select('products.*', 'price_variations.sell_price_inc_tax as ad_price');
            }

            switch ($sortBy) {
                case 'low-to-high':
                    $products = $products->orderBy('ad_price', 'asc');
                    break;
                case 'high-to-low':
                    $products = $products->orderBy('ad_price', 'desc');
                    break;
                case 'top-selling':
                    $products = $products->orderBy('products.top_selling', 'desc');
                    break;
                case 'latest':
                default:
                    $products = $products->orderBy('products.created_at', 'desc');
                    break;
            }

            $products = $products->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'data' => ProductResource::collection($products),
                    'total' => $products->count(),
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Products one-per-brand failed',
                'error' => $th->getMessage() . ' on ' . $th->getFile()
            ], 500);
        }
    }

    /**
     * Get products for a shop
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shopProducts(Request $request)
    {
        $searchTerm = $request->input('s', '');
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'popularity');
        $page = $request->query('page', 1);

        // Support both B2C-style route params and B2B-style query params
        // - B2C: /b2c-api/{location_id}/{brand_name}/shop
        // - B2B: /api/shop?brand_name=zevenir
        $locationId = $request->route('location_id') ?? config('services.b2b.location_id');
        $brandName = $request->route('brand_name') ?? $request->query('brand_name');
        $categorIds = $request->query('categoryIds',false);
        if($categorIds){
            $categorIds = explode(',', $categorIds);
        }
        $regexPattern = null;
        if (!empty($searchTerm)) {
            $searchWords = preg_split('/\s+/', $searchTerm);
            $regexPattern = implode('.*', array_map(function ($word) {
                return "(?=.*" . preg_quote($word) . ")";
            }, $searchWords));
        }
        // Get authenticated user (B2C customer) and wishlist info
        $authGuard = \Illuminate\Support\Facades\Auth::guard('api');
        $userId = $authGuard->id();
        $contact = $authGuard->user();
        $priceGroupId = null;
        $useDefaultPrice = true;
        if ($contact && is_array($contact->price_tier) && !empty($contact->price_tier)) {
            $priceGroupId = key($contact->price_tier);
            $useDefaultPrice = $this->useDefaultSellPrice($priceGroupId);
        }

        // Base query for B2B shop products
        $productsQuery = Product::with('webcategories', 'brand')
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->forCatalogListing(null, is_array($categorIds) ? $categorIds : null)
            ->addSelect([
                'b2c_price' => Variation::select('sell_price_inc_tax')
                    ->whereColumn('product_id', 'products.id')
                    ->where('sell_price_inc_tax', '>', 0)
                    ->orderBy('sell_price_inc_tax')
                    ->whereNotNull('sell_price_inc_tax')
                    ->limit(1)
            ])
            // Visibility rule: hide products that don't have a real price
            // - For customers with a selling price group: require a positive group price
            // - Otherwise (guest/default): require a positive base variation price
            ->when($contact && ! $useDefaultPrice && $priceGroupId !== null, function ($query) use ($priceGroupId) {
                $query->whereExists(function ($q) use ($priceGroupId) {
                    $q->select(DB::raw(1))
                        ->from('variations')
                        ->join('variation_group_prices', 'variations.id', '=', 'variation_group_prices.variation_id')
                        ->whereColumn('variations.product_id', 'products.id')
                        ->where('variation_group_prices.price_group_id', $priceGroupId)
                        ->whereNotNull('variation_group_prices.price_inc_tax')
                        ->where('variation_group_prices.price_inc_tax', '>', 0);
                });
            }, function ($query) {
                // Default path (no tier / default price): require positive base price
                $query->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('variations')
                        ->whereColumn('variations.product_id', 'products.id')
                        ->whereNotNull('variations.sell_price_inc_tax')
                        ->where('variations.sell_price_inc_tax', '>', 0);
                });
            })
            ->when($userId, function ($query) use ($userId) {
                // Add wishlist_id for the authenticated user
                $query->addSelect([
                    'wishlist_id' => \App\Wishlist::select('id')
                        ->whereColumn('product_id', 'products.id')
                        ->where('user_id', $userId)
                        ->limit(1)
                ]);
            })
            ->when(is_array($categorIds), function ($query) use ($categorIds) {
                $query->whereHas('webcategories', function ($query) use ($categorIds) {
                    $query->whereIn('categories.id', $categorIds);
                });
            });
        // Always restrict to selected brand, if a brand name is present (route or query)
        if (!empty($brandName)) {
            $productsQuery = $productsQuery->whereHas('brand', function ($query) use ($brandName, $locationId) {
                // Restrict by exact slug match; optionally scope by location_id when available
                $query->where('slug', $brandName);

                if (!empty($locationId)) {
                    $query->where('location_id', $locationId);
                }
            });
        }

        // If there is a search term, apply filter (REGEXP) but *not* on brands (since brand filter already restricted above)
        if ($regexPattern) {
            $productsQuery = $productsQuery->where(function ($query) use ($regexPattern) {
                $query->where('products.name', 'REGEXP', $regexPattern)
                    // ->orWhere('products.product_description', 'REGEXP', $regexPattern)
                    // ->orWhere('products.barcode_no', 'REGEXP', $regexPattern)
                    ->orWhere('products.sku', 'REGEXP', $regexPattern)
                    ->orWhereHas('webcategories', function ($query) use ($regexPattern) {
                       $query->where('name', 'REGEXP', $regexPattern);
                        // ->orWhere('slug', 'REGEXP', $regexPattern);
                    });
            });
        }
        //sorting 
        switch ($sortBy) {
            case 'low-to-high':
                $productsQuery = $productsQuery->orderBy('b2c_price', 'asc');
                break;
            case 'high-to-low':
                $productsQuery = $productsQuery->orderBy('b2c_price', 'desc');
                break;
            case 'top-selling':
                $productsQuery = $productsQuery->orderBy('top_selling', 'desc');
                break;
            case 'latest':
            default:
                $productsQuery = $productsQuery->orderBy('products.created_at', 'desc');
                break;
        }
        //final pagination 
        $products = $productsQuery->paginate($perPage, ['*'], 'page', $page);

        // As a final safety filter, hide any products whose effective shop price resolves to 0 or null.
        // Frontend reads `b2c_price`; if it's empty it shows 0.00, so we strip those items here.
        $filtered = $products->getCollection()->filter(function ($product) {
            $price = $product->b2c_price ?? 0;
            return (float) $price > 0;
        });
        $products->setCollection($filtered->values());

        // Related products for B2B shop (used for \"You may also like\" on listing pages)
        // Reuse the same base query so related items follow the same brand/category/price rules.
        $relatedProductsCollection = (clone $productsQuery)
            ->reorder() // clear any previous ordering
            ->orderBy('top_selling', 'desc')
            ->limit(50)
            ->get();

        // Ensure related items also have a valid b2c_price
        $relatedProducts = $relatedProductsCollection->filter(function ($product) {
            $price = $product->b2c_price ?? 0;
            return (float) $price > 0;
        })->values()->take(20);

        return response()->json([
            'status' => true,
            'data' => [
                'data' => CateLogResource::collection($products->getCollection()),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'per_page' => $products->perPage(),
                'to' => $products->lastItem(),
            ],
            'relatedProducts' => CateLogResource::collection($relatedProducts),

        ]);
        // return response()->json([ 'status' => true, 'message' => 'Shop products successful', 'data' => $products]);
    }
    /**
     * Get default products for authenticated and non-authenticated users
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param array $authData
     * @param int $limit
     * @return \Illuminate\Http\JsonResponse
     */
    private function getDefaultProducts($authData, $limit = 10)
{
    if ($authData['status']) {
        $contact = $authData['user'];
        $priceTier = $contact->price_tier;
        $priceGroupId = key($priceTier);

        return Product::with('webcategories', 'brand','product_locations','product_states')
        ->wherehas('product_locations',function($q) {
            $q->where('product_locations.location_id',config('services.b2b.location_id'));
        })
            ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
            ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                    ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
            })
            ->selectRaw('products.*, MIN(variation_group_prices.price_inc_tax) as ad_price')
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->inRandomOrder()
            ->groupBy('products.id')
            ->havingRaw('ad_price > 0')
            ->limit($limit)
             ->paginate(10, ['*'], 'page', 1);
    } else {
        return Product::with('webcategories', 'brand')
            ->where('productVisibility', 'public')
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->leftJoin('variations as price_variations', 'products.id', '=', 'price_variations.product_id')
            ->leftJoin('variation_location_details as vld', 'price_variations.id', '=', 'vld.variation_id')
            ->where('vld.location_id', config('services.b2b.location_id'))
            ->select('products.*', 'price_variations.sell_price_inc_tax as ad_price')
            ->inRandomOrder()
            ->limit($limit)
            ->paginate(10, ['*'], 'page', 1);
    }
    
    }

    /**
     * Search products for authenticated and non-authenticated users
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchProducts(Request $request)
    {
        $searchTerm = $request->input('s', '');
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'default');
        $page = $request->query('page', 1);
        $minPrice = $request->query('min');
        $maxPrice = $request->query('max');
        $byState = $request->query('byState', false);
        $includeRecent = $request->query('include_recent', false);
        $recentSearches = [];


        try {
            //auth check 
            $authData = $this->authCheck($request);
            if ($authData['status'] == true) {
                $contact = $authData['user'];
                $priceTier = $contact->price_tier;
                $priceGroupId =  key($priceTier);  //group id 

                // Log search term to history for authenticated users
                if (!empty($searchTerm)) {
                    try {
                        $history = SearchHistory::firstOrNew([
                            'user_id' => $contact->id,
                            'term' => $searchTerm,
                        ]);
                        $history->count = ($history->count ?? 0) + 1;
                        $history->save();
                    } catch (\Throwable $e) {
                        Log::warning('Failed to store search history', [
                            'user_id' => $contact->id,
                            'term' => $searchTerm,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Fetch recent searches if requested
                if ($includeRecent) {
                    $recentSearches = SearchHistory::where('user_id', $contact->id)
                        ->orderByDesc('updated_at')
                        ->limit(10)
                        ->get(['id', 'term', 'count', 'updated_at']);
                }

                //query 
                if (!empty($searchTerm)) {
                    $searchWords = preg_split('/\s+/', $searchTerm);
                    $regexPattern = implode('.*', array_map(function ($word) {
                        return "(?=.*" . preg_quote($word) . ")";
                    }, $searchWords));
                    $products = Product::with('webcategories', 'brand','product_locations','product_states')
                    ->wherehas('product_locations',function($q) {
                        $q->where('product_locations.location_id',config('services.b2b.location_id'));
                    })
                        ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                        ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                            $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                                ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                        })
                        ->where('enable_selling', 1)
                        ->where('is_inactive', 0)
                        ->selectRaw('
                        products.*, 
                        MIN(variation_group_prices.price_inc_tax) as ad_price
                    ')
                        ->where(function ($query) use ($regexPattern) {
                            $query->where('products.name', 'REGEXP', $regexPattern)
                                // ->orWhere('products.product_description', 'REGEXP', $regexPattern)
                                // ->orWhere('products.barcode_no', 'REGEXP', $regexPattern)
                                ->where(function ($query) use ($regexPattern) {
                                    $query->where('products.name', 'REGEXP', $regexPattern)
                                        // ->orWhere('products.product_description', 'REGEXP', $regexPattern)
                                        // ->orWhere('products.barcode_no', 'REGEXP', $regexPattern)
                                        ->orWhere('products.sku', 'REGEXP', $regexPattern)
                                        ->orWhereHas('webcategories', function ($subQuery) use ($regexPattern) {
                                            $subQuery->where('name', 'REGEXP', $regexPattern)
                                                ->where('visibility', 'public');
                                        })
                                        ->orWhereHas('brand', function ($subQuery) use ($regexPattern) {
                                            $subQuery->where('name', 'REGEXP', $regexPattern)
                                                ->where('visibility', 'public');
                                        });
                                });
        
                        })
                        ->orWhereHas('webcategories', function ($query) use ($regexPattern) {
                            $query->where('name', 'REGEXP', $regexPattern);
                            // ->orWhere('slug', 'REGEXP', $regexPattern);
                        })
                        ->orWhereHas('brand', function ($query) use ($regexPattern) {
                            $query->where('name', 'REGEXP', $regexPattern);
                            // ->orWhere('slug', 'REGEXP', $regexPattern);
                        })
                        
                        ->groupBy('products.id')
                        
                    ->havingRaw('ad_price > 0')
                    
                    ;
                    if ($minPrice !== null) {
                            $products = $products->havingRaw('ad_price >= ?', [(float)$minPrice]);
                        }
                    if ($maxPrice !== null) {
                            $products = $products->havingRaw('ad_price <= ?', [(float)$maxPrice]);
                        }
                    //sorting 
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
                    //final pagination 
                    $products = $products->paginate($perPage, ['*'], 'page', $page);
                }


            } else if ($authData['status'] == false) {
                if (!empty($searchTerm)) {
                    $searchWords = preg_split('/\s+/', $searchTerm);
                    $regexPattern = implode('.*', array_map(function ($word) {
                        return "(?=.*" . preg_quote($word) . ")";
                    }, $searchWords));
                    $products = Product::with('webcategories', 'brand','product_locations','product_states')
                    ->when($byState, function($query) use ($byState) {
                        $query->whereHas('product_states', function($q) use ($byState) {
                            $q->where('state', '=', $byState);
                        });
                    })
                    ->wherehas('product_locations',function($q) {
                        $q->where('product_locations.location_id',config('services.b2b.location_id'));
                    })
                    
                        ->where('productVisibility', 'public')
                        ->where('enable_selling', 1)
                        ->where('is_inactive', 0)
                        ->leftJoin('variations as price_variations', 'products.id', '=', 'price_variations.product_id')
                        ->leftJoin('variation_location_details as vld', 'price_variations.id', '=', 'vld.variation_id')
                        ->where('vld.location_id', config('services.b2b.location_id'))
                        ->select('products.*', 'price_variations.sell_price_inc_tax as ad_price')   //sell price for non-auth users
                        ->where(function ($query) use ($regexPattern) {
                            $query->where('products.name', 'REGEXP', $regexPattern)
                                // ->orWhere('products.product_description', 'REGEXP', $regexPattern)
                                // ->orWhere('products.barcode_no', 'REGEXP', $regexPattern)
                                ->orWhere('products.sku', 'REGEXP', $regexPattern)
                                ->orWhereHas('webcategories', function ($subQuery) use ($regexPattern) {
                                    $subQuery->where('name', 'REGEXP', $regexPattern)
                                        ->where('visibility', 'public');
                                })
                                ->orWhereHas('brand', function ($subQuery) use ($regexPattern) {
                                    $subQuery->where('name', 'REGEXP', $regexPattern)
                                        ->where('visibility', 'public');
                                });

                        })
                        ->orWhereHas('webcategories', function ($query) use ($regexPattern) {
                            $query->where('name', 'REGEXP', $regexPattern)
                                ->where('visibility', 'public')
                                // ->orWhere('slug', 'REGEXP', $regexPattern)
                            ;
                        })->when($byState, function($query) use ($byState) {
                            $query->whereHas('product_states', function($q) use ($byState) {
                                $q->where('state', '=', $byState);
                            });
                        });
                        // ->whereDoesntHave('webcategories', function ($query) {
                        //     $query->where('visibility', 'protected');
                        // })
                        // ->orWhereHas('brand', function ($query) use ($regexPattern) {
                        //     $query->where('name', 'REGEXP', $regexPattern)->where('visibility', 'public')
                        //         // ->orWhere('slug', 'REGEXP', $regexPattern)
                        //     ;
                        // });
                    //sorting 
                    switch ($sortBy) {
                        case 'top-selling':
                            $products = $products->orderBy('top_selling', 'desc');
                            break;
                        case 'latest':
                        default:
                            $products = $products->orderBy('products.created_at', 'desc');
                            break;
                    }
                    //final pagination 
                    $products = $products->paginate($perPage, ['*'], 'page', $page);
                }
                  

            }
            if (empty($products)){
                                 
                $products = $this->getDefaultProducts($authData, 10);
            }
            return response()->json([
                'status' => true,
                'data' => [
                    'current_page' => $products->currentPage(),
                    'data' => CateLogResource::collection($products->getCollection()),
                    'last_page' => $products->lastPage(),
                    'total' => $products->total(),
                    // 'first_page_url' => $products->url(1),
                    'from' => $products->firstItem(),
                    // 'last_page_url' => $products->url($products->lastPage()),
                    // 'next_page_url' => $products->nextPageUrl(),
                    'per_page' => $products->perPage(),
                    // 'prev_page_url' => $products->previousPageUrl(),
                    'to' => $products->lastItem(),
                    'recent_searches' => $recentSearches,
                ],
            ]);
            // return response()->json(['status' => true, 'data' => $products]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Search function failed', 'error' => $th->getMessage() . ' on ' . $th->getFile() .'at' .$th->getLine()]);
        }
    }

    /**
     * Delete a single search history item (authenticated users only).
     * DELETE /api/search/history/{id}
     */
    public function deleteSearchHistoryItem(Request $request, $id)
    {
        $authData = $this->authCheck($request);
        if ($authData['status'] !== true) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $deleted = SearchHistory::where('user_id', $authData['user']->id)
            ->where('id', $id)
            ->delete();

        if (!$deleted) {
            return response()->json(['status' => false, 'message' => 'Search history item not found or already deleted'], 404);
        }

        return response()->json(['status' => true, 'message' => 'Search history item deleted']);
    }

    /**
     * Delete all search history for the current user (authenticated users only).
     * DELETE /api/search/history
     */
    public function deleteAllSearchHistory(Request $request)
    {
        $authData = $this->authCheck($request);
        if ($authData['status'] !== true) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $deleted = SearchHistory::where('user_id', $authData['user']->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'All search history deleted',
            'deleted_count' => $deleted,
        ]);
    }

    /**
     * Get first matching product for a search term for the given contact (for search history preview).
     * Returns product_name, product_image, price, sku or null if no product with valid price.
     *
     * @param \App\Contact $contact
     * @param string $term
     * @return array|null
     */
    private function getSearchHistoryProductPreview(Contact $contact, $term)
    {
        $term = trim((string) $term);
        if ($term === '') {
            return null;
        }
        $priceTier = $contact->price_tier;
        $priceGroupId = is_array($priceTier) && !empty($priceTier) ? key($priceTier) : null;
        $useDefaultPrice = $this->useDefaultSellPrice($priceGroupId);

        $searchWords = preg_split('/\s+/', $term);
        $regexPattern = implode('.*', array_map(function ($word) {
            return '(?=.*' . preg_quote($word) . ')';
        }, $searchWords));

        $baseQuery = Product::query()
            ->whereHas('product_locations', function ($q) {
                $q->where('product_locations.location_id', config('services.b2b.location_id'));
            })
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->where(function ($query) use ($regexPattern) {
                $query->where('products.name', 'REGEXP', $regexPattern)
                    ->orWhere('products.sku', 'REGEXP', $regexPattern);
            });

        if ($useDefaultPrice) {
            $product = $baseQuery
                ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                ->selectRaw('products.id, products.name, products.sku, products.slug, products.image, MIN(variations.sell_price_inc_tax) as ad_price')
                ->groupBy('products.id', 'products.name', 'products.sku', 'products.slug', 'products.image')
                ->havingRaw('ad_price > 0')
                ->first();
        } else {
            $product = $baseQuery
                ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                    $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                        ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                })
                ->selectRaw('products.id, products.name, products.sku, products.slug, products.image, MIN(variation_group_prices.price_inc_tax) as ad_price')
                ->groupBy('products.id', 'products.name', 'products.sku', 'products.slug', 'products.image')
                ->havingRaw('ad_price > 0')
                ->first();
        }

        if (!$product) {
            return null;
        }

        $imageUrl = !empty($product->image)
            ? asset('/uploads/img/' . rawurlencode($product->image))
            : asset('/img/default.png');

        $sku = $product->sku;
        if (($sku === null || $sku === '') && $product->id) {
            $firstVariation = Variation::where('product_id', $product->id)->value('sub_sku');
            $sku = $firstVariation ?? '';
        }

        return [
            'product_name' => $product->name,
            'product_image' => $imageUrl,
            'price' => (float) $product->ad_price,
            'sku' => (string) $sku,
            'slug' => $product->slug ?? null,
        ];
    }

    /**
     * Get search history for the current customer (paginated).
     * GET /api/customer/search-history
     * Each item includes product_name, product_image, price, sku from the first matching product for that term.
     */
    public function getSearchHistory(Request $request)
    {
        $authData = $this->authCheck($request);
        if ($authData['status'] !== true) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $perPage = (int) $request->query('limit', $request->query('perPage', 10));
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $history = SearchHistory::where('user_id', $authData['user']->id)
            ->orderByDesc('updated_at')
            ->paginate($perPage);

        $contact = $authData['user'];
        $dataItems = [];
        foreach ($history->items() as $item) {
            $row = is_array($item) ? $item : $item->toArray();
            $preview = $this->getSearchHistoryProductPreview($contact, $row['term'] ?? '');
            $row['product_name'] = $preview ? $preview['product_name'] : null;
            $row['product_image'] = $preview ? $preview['product_image'] : null;
            $row['price'] = $preview ? $preview['price'] : null;
            $row['sku'] = $preview ? $preview['sku'] : null;
            $row['slug'] = $preview ? ($preview['slug'] ?? null) : null;
            $dataItems[] = $row;
        }

        return response()->json([
            'status' => true,
            'data' => [
                'current_page' => $history->currentPage(),
                'data' => $dataItems,
                'last_page' => $history->lastPage(),
                'total' => $history->total(),
                'from' => $history->firstItem(),
                'per_page' => $history->perPage(),
                'to' => $history->lastItem(),
            ],
        ]);
    }

    /**
     * Update a single search history entry (e.g. rename term or adjust count).
     * PUT /api/customer/search-history/{id}
     */
    public function updateSearchHistoryItem(Request $request, $id)
    {
        $authData = $this->authCheck($request);
        if ($authData['status'] !== true) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $history = SearchHistory::where('user_id', $authData['user']->id)
            ->where('id', $id)
            ->first();

        if (!$history) {
            return response()->json([
                'status' => false,
                'message' => 'Search history item not found.',
            ], 404);
        }

        $data = $request->validate([
            'term' => 'sometimes|string|max:255',
            'count' => 'sometimes|integer|min:0',
        ]);

        $history->fill($data);
        $history->save();

        return response()->json([
            'status' => true,
            'message' => 'Search history item updated.',
            'data' => $history,
        ]);
    }

    /**
     * Get all products for authenticated and non-authenticated users
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allProducts(Request $request)
    {
        $searchTerm = $request->input('s', '');
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'default');
        $page = $request->query('page', 1);

        try {
            //auth check 
            $authData = $this->authCheck($request);
            if ($authData['status'] == true) {
                $contact = $authData['user'];
                $priceTier = $contact->price_tier;
                $priceGroupId =  key($priceTier);  //group id 

                //query 
                if (!empty($searchTerm)) {
                    $searchWords = preg_split('/\s+/', $searchTerm);
                    $regexPattern = implode('.*', array_map(function ($word) {
                        return "(?=.*" . preg_quote($word) . ")";
                    }, $searchWords));
                    $products = Product::with('webcategories', 'brand','product_locations','product_states')
                    ->wherehas('product_locations',function($q) {
                        $q->where('product_locations.location_id',config('services.b2b.location_id'));
                    })
                        ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                        ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                            $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                                ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                        })
                        ->where('enable_selling', 1)
                        ->where('is_inactive', 0)
                        ->selectRaw('
                        products.*, 
                        MIN(variation_group_prices.price_inc_tax) as ad_price
                    ')
                        ->where(function ($query) use ($regexPattern) {
                            $query->where('products.name', 'REGEXP', $regexPattern)
                                // ->orWhere('products.product_description', 'REGEXP', $regexPattern)
                                // ->orWhere('products.barcode_no', 'REGEXP', $regexPattern)
                                ->orWhere('products.sku', 'REGEXP', $regexPattern)
                                ->orWhereHas('webcategories', function ($subQuery) use ($regexPattern) {
                                    $subQuery->where('name', 'REGEXP', $regexPattern)
                                        ->where('visibility', 'public');
                                })
                                ->orWhereHas('brand', function ($subQuery) use ($regexPattern) {
                                    $subQuery->where('name', 'REGEXP', $regexPattern)
                                        ->where('visibility', 'public');
                                });
                        })
                        ->orWhereHas('webcategories', function ($query) use ($regexPattern) {
                            $query->where('name', 'REGEXP', $regexPattern)
                                // ->orWhere('slug', 'REGEXP', $regexPattern)
                            ;
                        })
                        ->orWhereHas('brand', function ($query) use ($regexPattern) {
                            $query->where('name', 'REGEXP', $regexPattern)
                                // ->orWhere('slug', 'REGEXP', $regexPattern)
                            ;
                        })
                        ->groupBy('products.id')
                        
                    ->havingRaw('ad_price > 0'); // Group by product ID to remove duplicates

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
                    $products = $products->paginate($perPage, ['*'], 'page', $page);
                } else {
                    $products = Product::with('webcategories', 'brand','product_locations','product_states')
                    ->wherehas('product_locations',function($q) {
                        $q->where('product_locations.location_id',config('services.b2b.location_id'));
                    })
                        ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                        ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                            $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                                ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                        })
                        ->where('enable_selling', 1)
                        ->where('is_inactive', 0)
                        ->select('products.*', 'variation_group_prices.price_inc_tax as ad_price')   //ad price null
                        ->orderBy('products.created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
                }
            } else if ($authData['status'] == false) {
                if (!empty($searchTerm)) {
                    $searchWords = preg_split('/\s+/', $searchTerm);
                    $regexPattern = implode('.*', array_map(function ($word) {
                        return "(?=.*" . preg_quote($word) . ")";
                    }, $searchWords));
                    $products = Product::with('webcategories', 'brand','product_locations','product_states')
                    ->wherehas('product_locations',function($q) {
                        $q->where('product_locations.location_id',config('services.b2b.location_id'));
                    })
                        ->where('productVisibility', 'public')
                        ->where('enable_selling', 1)
                        ->where('is_inactive', 0)
                        ->leftJoin('variations as price_variations', 'products.id', '=', 'price_variations.product_id')
                        ->leftJoin('variation_location_details as vld', 'price_variations.id', '=', 'vld.variation_id')
                        ->where('vld.location_id', config('services.b2b.location_id'))
                        ->select('products.*', 'price_variations.sell_price_inc_tax as ad_price')   //sell price for non-auth users
                        ->where(function ($query) use ($regexPattern) {
                            $query->where('products.name', 'REGEXP', $regexPattern)
                                        // ->orWhere('products.product_description', 'REGEXP', $regexPattern)
                                        // ->orWhere('products.barcode_no', 'REGEXP', $regexPattern)
                                        ->orWhere('products.sku', 'REGEXP', $regexPattern)
                                        ->orWhereHas('webcategories', function ($subQuery) use ($regexPattern) {
                                            $subQuery->where('name', 'REGEXP', $regexPattern)
                                                ->where('visibility', 'public');
                                        })
                                        ->orWhereHas('brand', function ($subQuery) use ($regexPattern) {
                                            $subQuery->where('name', 'REGEXP', $regexPattern)
                                                ->where('visibility', 'public');
                                        });
                        })
                        ->whereDoesntHave('webcategories', function ($query) {
                            $query->where('visibility', 'protected');
                        });
                    //sorting 
                    switch ($sortBy) {
                        case 'top-selling':
                            $products = $products->orderBy('top_selling', 'desc');
                            break;
                        case 'latest':
                        default:
                            $products = $products->orderBy('products.created_at', 'desc');
                            break;
                    }
                    $products = $products->paginate($perPage, ['*'], 'page', $page);
                } else {
                        $products = Product::with('webcategories', 'brand','product_locations','product_states')
                    ->wherehas('product_locations',function($q) {
                        $q->where('product_locations.location_id',config('services.b2b.location_id'));
                    })
                        ->where('productVisibility', 'public')
                        ->whereHas('webcategories', function ($query) {
                            $query
                                ->where('visibility', 'public')
                            ;
                        })
                        ->whereDoesntHave('webcategories', function ($query) {
                            $query->where('visibility', 'protected');
                        })
                        ->where('enable_selling', 1)
                        ->where('is_inactive', 0)
                        ->leftJoin('variations as price_variations', 'products.id', '=', 'price_variations.product_id')
                        ->leftJoin('variation_location_details as vld', 'price_variations.id', '=', 'vld.variation_id')
                        ->where('vld.location_id', config('services.b2b.location_id'))
                        ->select('products.*', 'price_variations.sell_price_inc_tax as ad_price')   //sell price for non-auth users
                        ->orderBy('products.created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
                }
            }
            return response()->json([
                'status' => true,
                'data' => [
                    'current_page' => $products->currentPage(),
                    'data' => ProductResource::collection($products->getCollection()),
                    'last_page' => $products->lastPage(),
                    'total' => $products->total(),
                    // 'first_page_url' => $products->url(1),
                    'from' => $products->firstItem(),
                    // 'last_page_url' => $products->url($products->lastPage()),
                    // 'next_page_url' => $products->nextPageUrl(),
                    'per_page' => $products->perPage(),
                    // 'prev_page_url' => $products->previousPageUrl(),
                    'to' => $products->lastItem(),
                ]
            ]);
            // return response()->json(['status' => true, 'data' => $products]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Search function failed', 'error' => $th->getMessage() . ' on ' . $th->getFile()]);
        }
    }

    /**
     * Get single product for authenticated and non-authenticated users
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function singleProduct(Request $request, $slug)
    {
        try {
            //auth check 
            $authData = $this->authCheck($request);
            if ($authData['status'] == true) {
                $contact = $authData['user'];
                $priceTier = $contact->price_tier;
                $priceGroupId =  key($priceTier);  //group id 
                $product = Product::with([
                    'webcategories',
                    'brand',
                    'category',
                    'product_variations',
                    'product_locations',
                    'product_states',
                    'product_gallery_images' => function($query) {
                        $query->select('id', 'product_id', 'image_path');
                    },
                    'customer_reviews' => function($query) {
                        $query->where('is_active', 1)
                            ->where('is_deleted', 0)
                            ->with(['contact' => function($q) {
                                $q->select('id', 'name', 'first_name', 'last_name', 'email');
                            }])
                            ->select('id', 'product_id', 'contact_id', 'title', 'description', 'rating', 'likes', 'created_at')
                            ->orderBy('created_at', 'desc')
                            ->limit(50);
                    },
                    'variations' => function ($query) use ($priceGroupId) {
                        $query->select([
                            'variations.id',
                            'variations.name',
                            'variations.product_id',
                            'variations.var_barcode_no',
                            'variations.var_maxSaleLimit',
                            'variations.product_variation_id',
                            'variation_group_prices.price_inc_tax as ad_price',
                            DB::raw('COALESCE(variation_location_details.in_stock_qty, 0) as qty'),
                        ])
                            ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                                $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                                    ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                            })
                            ->leftJoin('variation_location_details', function ($join) {
                                $join->on('variations.id', '=', 'variation_location_details.variation_id')
                                     ->where('variation_location_details.location_id', '=', config('services.b2b.location_id', 1));
                            })
                            ->leftJoin('products', function ($join) {
                                $join->on('variations.product_id', '=', 'products.id');
                            })
                            ->whereNotNull('variation_group_prices.price_inc_tax') 
                            ->where(function($query) {
                                $query->where('variation_location_details.qty_available', '>', 0)
                                    ->orWhere('products.enable_stock', '=', 0);
                            });
                    },
                    'variations.media' => function($query) {
                        $query->select('id', 'file_name', 'model_id');
                    }
                ])
                ->wherehas('product_locations',function($q) {
                    $q->where('product_locations.location_id',config('services.b2b.location_id'));
                })
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->where('slug', $slug)
                    ->first();

                    $recommendedProducts = Product::with('webcategories', 'brand','product_locations','product_states')
                    ->wherehas('product_locations',function($q) {
                        $q->where('product_locations.location_id',config('services.b2b.location_id'));
                    })
                    ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                    ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                        $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                            ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                    })
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->selectRaw('
                    products.*, 
                    MIN(variation_group_prices.price_inc_tax) as ad_price
                ')
                ->wherehas('product_locations',function($q) {
                    $q->where('product_locations.location_id',config('services.b2b.location_id'));
                })
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->where('slug', '!=', $slug)
                    
                    ->whereHas('brand', function($query) use ($product) {
                        if ($product && $product->brand) {
                            $query->where('id', $product->brand->id);
                        } else {
                            // If no brand, filter by category instead
                            $categoryId = 1; // Default category ID
                            if ($product && $product->webcategories) {
                                $category = $product->webcategories
                                    ->where('category_type', 'product')
                                    ->where('parent_id', '!=', 0)
                                    ->first();
                                if ($category) {
                                    $categoryId = $category->id;
                                }
                            }
                            $query->where('category', $categoryId);
                        }
                    })
                    ->limit(10)
                    ->groupBy('products.id')
                    ->havingRaw('ad_price > 0')
                    ->get();
                    // Get product category IDs
                    $productCategoryIds = isset($product->webcategories)? $product->webcategories->where('category_type', 'product')->where('parent_id','!=', 0)->pluck('id')->toArray():[1];
                    if ($recommendedProducts->count() < 30) {
                        $remainingCount =50; // 30 - $recommendedProducts->count();
                        $sameCategoryProducts = Product::with('webcategories', 'brand','product_locations','product_states')
                        ->wherehas('product_locations',function($q) {
                            $q->where('product_locations.location_id',config('services.b2b.location_id'));
                        })
                        ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                        ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                            $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                                ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                        })
                        ->where('enable_selling', 1)
                        ->where('is_inactive', 0)
                        ->selectRaw('
                        products.*, 
                        MIN(variation_group_prices.price_inc_tax) as ad_price
                    ')
                    ->wherehas('product_locations',function($q) {
                        $q->where('product_locations.location_id',config('services.b2b.location_id'));
                    })
                            ->where('enable_selling', 1)
                            ->where('is_inactive', 0)
                            ->where('slug', '!=', $slug)
                            ->whereDoesntHave('brand', function($query) use ($product){
                                if (isset($product->brand) && isset($product->brand->id)) {
                                    $query->where('id', $product->brand->id);
                                }
                            })
                            ->whereHas('webcategories', function($query) use ($productCategoryIds) {
                                $query->whereIn('categories.id', $productCategoryIds);
                            })
                            ->limit($remainingCount)
                            ->groupBy('products.id')
                            ->havingRaw('ad_price > 0')
                            ->get();

                        $relatedProducts = $sameCategoryProducts;
                    }
                    
                    if ($relatedProducts->count() < 50) {
                        $remainingCount = 50 - $relatedProducts->count();
                        $otherProducts = Product::with('webcategories', 'brand','product_locations','product_states')
                        ->wherehas('product_locations',function($q) {
                            $q->where('product_locations.location_id',config('services.b2b.location_id'));
                        })
                        ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                        ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                            $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                                ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                        })
                        ->where('enable_selling', 1)
                        ->where('is_inactive', 0)
                        ->selectRaw('
                        products.*, 
                        MIN(variation_group_prices.price_inc_tax) as ad_price
                    ')
                    ->wherehas('product_locations',function($q) {
                        $q->where('product_locations.location_id',config('services.b2b.location_id'));
                    })
                            ->where('enable_selling', 1)
                            ->where('is_inactive', 0)
                            ->where('slug', '!=', $slug)
                            ->whereDoesntHave('brand', function($query) use ($product){
                                if (isset($product->brand) && isset($product->brand->id)) {
                                    $query->where('id', $product->brand->id);
                                }
                            })
                            ->whereDoesntHave('webcategories', function($query) use ($productCategoryIds) {
                                $query->whereIn('categories.id', $productCategoryIds);
                            })
                            ->limit($remainingCount)
                            ->groupBy('products.id')
                            ->havingRaw('ad_price > 0')
                            ->get();

                        $relatedProducts = $relatedProducts->concat($otherProducts)->unique('id');
                    }

                    // Apply customer group percentage to prices
                    $business_id = $contact->business_id ?? 1;
                    $cg = $this->contactUtil->getCustomerGroup($business_id, $contact->id);
                    
                    // Percentage is applied in ProductResource, no need to apply here

                    if($product){
                        return response()->json([
                            'status' => true, 
                            'data' =>new ProductResource( $product), 
                            'recommendedProducts' => CateLogResource::collection($recommendedProducts), 
                            'relatedProducts' => CateLogResource::collection($relatedProducts)
                        ]);
                    }
                return response()->json(['status' => false, 'message' => 'Product Not Found', 'error' => 'Check Url']);
            } else if ($authData['status'] == false) {
                // For non-authenticated B2B users: use Prime price group when request is from Prime storefront, else public
                $publicPriceGroupId = config('services.b2b.public_price_group_id');
                $primePriceGroupId = config('services.b2b.prime_price_group_id');
                $primeStorefrontHost = config('services.b2b.prime_storefront_host', 'prime.smokevana.com');
                $origin = $request->header('Origin', '');
                $referer = $request->header('Referer', '');
                $isFromPrimeStorefront = (!empty($primeStorefrontHost) && (stripos($origin, $primeStorefrontHost) !== false || stripos($referer, $primeStorefrontHost) !== false));
                $guestPriceGroupId = ($isFromPrimeStorefront && !empty($primePriceGroupId)) ? $primePriceGroupId : $publicPriceGroupId;

                $product = Product::with([
                    'webcategories',
                    'brand',
                    'category',
                    'product_variations',
                    'product_states',
                    'product_locations',
                    'product_gallery_images' => function($query) {
                        $query->select('id', 'product_id', 'image_path');
                    },
                    'customer_reviews' => function($query) {
                        $query->where('is_active', 1)
                            ->where('is_deleted', 0)
                            ->with(['contact' => function($q) {
                                $q->select('id', 'name', 'first_name', 'last_name', 'email');
                            }])
                            ->select('id', 'product_id', 'contact_id', 'title', 'description', 'rating', 'likes', 'created_at')
                            ->orderBy('created_at', 'desc')
                            ->limit(50);
                    },
                    // Load variation-level web prices for guests (Prime group when from Prime storefront)
                    'variations' => function ($query) use ($guestPriceGroupId) {
                        $locationId = config('services.b2b.location_id', 1);
                        $query->select([
                                'variations.id',
                                'variations.name',
                                'variations.product_id',
                                'variations.var_barcode_no',
                                'variations.var_maxSaleLimit',
                                'variations.product_variation_id',
                                'variations.sell_price_inc_tax',
                                DB::raw(!empty($guestPriceGroupId) 
                                    ? 'COALESCE(variation_group_prices.price_inc_tax, variations.sell_price_inc_tax) as ad_price'
                                    : 'variations.sell_price_inc_tax as ad_price'
                                ),
                                DB::raw('COALESCE(variation_location_details.in_stock_qty, 0) as qty'),
                            ])
                            ->when(!empty($guestPriceGroupId), function ($q) use ($guestPriceGroupId) {
                                $q->leftJoin('variation_group_prices', function ($join) use ($guestPriceGroupId) {
                                    $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                                         ->where('variation_group_prices.price_group_id', '=', $guestPriceGroupId);
                                });
                            })
                            ->leftJoin('variation_location_details', function ($join) use ($locationId) {
                                $join->on('variations.id', '=', 'variation_location_details.variation_id')
                                     ->where('variation_location_details.location_id', '=', $locationId);
                            })
                            ->leftJoin('products', function ($join) {
                                $join->on('variations.product_id', '=', 'products.id');
                            })
                            ->where(function($query) {
                                $query->where('variation_location_details.qty_available', '>', 0)
                                      ->orWhere('products.enable_stock', '=', 0);
                            });
                    },
                    'variations.media' => function($query) {
                        $query->select('id', 'file_name', 'model_id');
                    }
                ])
                ->wherehas('product_locations',function($q) {
                    $q->where('product_locations.location_id',config('services.b2b.location_id'));
                })
                    ->where('productVisibility', 'public')
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->whereHas('webcategories', function ($query) {
                        $query->where('visibility', 'public');
                    })
                    ->whereDoesntHave('webcategories', function ($query) {
                        $query->where('visibility', 'protected');
                    })
                    ->where('slug', $slug)
                    ->first();

                // Derive top-level ad_price and prime_price from variation web prices (Prime group when from Prime storefront)
                if ($product && $product->relationLoaded('variations')) {
                    $minAdPrice = $product->variations->whereNotNull('ad_price')->min('ad_price');
                    if (!empty($minAdPrice)) {
                        $product->ad_price = $minAdPrice;
                        // For guest: prime_price for pricing/ad_prices (customer group prime price)
                        $product->prime_price = $minAdPrice;
                    }
                }
                    
                    // Build recommended products query with price calculation (Prime group when from Prime storefront)
                    $recommendedProductsQuery = Product::with('webcategories', 'brand','product_locations','product_states')
                    ->wherehas('product_locations',function($q) {
                        $q->where('product_locations.location_id',config('services.b2b.location_id'));
                    })
                    ->leftJoin('variations', 'products.id', '=', 'variations.product_id');
                    
                    if (!empty($guestPriceGroupId)) {
                        $recommendedProductsQuery->leftJoin('variation_group_prices', function ($join) use ($guestPriceGroupId) {
                            $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                                 ->where('variation_group_prices.price_group_id', '=', $guestPriceGroupId);
                        })
                        ->selectRaw('products.*, MIN(variation_group_prices.price_inc_tax) as ad_price')
                        ->groupBy('products.id')
                        ->havingRaw('ad_price > 0');
                    } else {
                        $recommendedProductsQuery->selectRaw('products.*, MIN(variations.sell_price_inc_tax) as ad_price')
                        ->groupBy('products.id')
                        ->havingRaw('ad_price > 0');
                    }
                    
                    $recommendedProducts = $recommendedProductsQuery
                    ->where('products.productVisibility', 'public')
                    ->where('products.enable_selling', 1)
                    ->where('products.is_inactive', 0)
                    ->where('products.slug', '!=', $slug)
                    ->when($product && $product->brand, function($query) use ($product) {
                        return $query->whereHas('brand', function($q) use ($product) {
                            $q->where('id', $product->brand->id);
                        });
                    })
                    ->limit(10)
                    ->get();

                    $categoryIds = isset($product->webcategories) ? 
                        $product->webcategories->where('category_type', 'product')
                            ->where('parent_id','!=', 0)
                            ->pluck('id')
                            ->toArray() : 
                        [1];

                    // Build related products query with price calculation (Prime group when from Prime storefront)
                    $relatedProductsQuery = Product::with('webcategories', 'brand','product_locations','product_states')
                    ->wherehas('product_locations',function($q) {
                        $q->where('product_locations.location_id',config('services.b2b.location_id'));
                    })
                    ->leftJoin('variations', 'products.id', '=', 'variations.product_id');
                    
                    if (!empty($guestPriceGroupId)) {
                        $relatedProductsQuery->leftJoin('variation_group_prices', function ($join) use ($guestPriceGroupId) {
                            $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                                 ->where('variation_group_prices.price_group_id', '=', $guestPriceGroupId);
                        })
                        ->selectRaw('products.*, MIN(variation_group_prices.price_inc_tax) as ad_price')
                        ->groupBy('products.id')
                        ->havingRaw('ad_price > 0');
                    } else {
                        $relatedProductsQuery->selectRaw('products.*, MIN(variations.sell_price_inc_tax) as ad_price')
                        ->groupBy('products.id')
                        ->havingRaw('ad_price > 0');
                    }
                    
                    $relatedProducts = $relatedProductsQuery
                    ->where('products.productVisibility', 'public')
                    ->where('products.enable_selling', 1)
                    ->where('products.is_inactive', 0)
                    ->where('products.slug', '!=', $slug)
                    ->when($product && $product->brand, function($query) use ($product) {
                        return $query->whereDoesntHave('brand', function($q) use ($product) {
                            $q->where('id', $product->brand->id);
                        });
                    })
                    ->whereHas('webcategories', function($query) use ($categoryIds) {
                        $query->whereIn('categories.id', $categoryIds);
                    })
                    ->limit(50)
                    ->get();
                if($product){

                    return response()->json(['status' => true, 'data' => new ProductResource($product), 'recommendedProducts' => CateLogResource::collection($recommendedProducts), 'relatedProducts' => CateLogResource::collection($relatedProducts)]);
                }
                return response()->json(['status' => false, 'message' => 'Product Not Found', 'error' => 'Check Url']);
            }
            return response()->json(['status' => false, 'message' => 'Product Not Found', 'error' => 'Check Url']);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Singe Product function failed', 'error' => $th->getMessage() . ' on ' . $th->getFile() . ' on line ' . $th->getLine()]);
        }
    }

    /**
     * Get product list for authenticated and non-authenticated users
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function productList(Request $request, $slug){
        $slugArray = explode(',', $slug);
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'popularity');
        $page = $request->query('page', 1);
        try {
            //auth check 
            $authData = $this->authCheck($request);
            if ($authData['status'] == true) {
                $contact = $authData['user'];
                $priceTier = $contact->price_tier;
                $priceGroupId =  key($priceTier);  //group id 

                //query 
                $products = Product::with('webcategories', 'brand','product_locations','product_states')
                ->wherehas('product_locations',function($q) {
                    $q->where('product_locations.location_id',config('services.b2b.location_id'));
                })
                    ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                    ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {  // For authenticated user
                        $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                            ->where('variation_group_prices.price_group_id', '=', $priceGroupId); // Get price based on tier
                    })
                    ->where('products.enable_selling', 1)
                    ->where('products.is_inactive', 0)
                    ->forCatalogListing(null)
                    ->whereIn('products.id', $slugArray)
                    ->selectRaw('
                            products.*, 
                            MIN(variation_group_prices.price_inc_tax) as ad_price
                        ') // ad_price
                    ->groupBy('products.id')
                    ->havingRaw('ad_price > 0'); // Group by product ID to ensure unique products

                //sorting 
                switch ($sortBy) {
                    case 'low-to-high':
                        $products = $products->orderBy('ad_price', 'asc');
                        break;
                    case 'high-to-low':
                        $products = $products->orderBy('ad_price', 'desc');
                        break;
                    case 'top-selling':
                        $products = $products->orderBy('products.top_selling', 'desc');
                        break;
                    case 'latest':
                    default:
                        $products = $products->orderBy('products.created_at', 'desc');
                        break;
                }
                //final pagination 
                $products = $products->paginate($perPage, ['*'], 'page', $page);
            } else if ($authData['status'] == false) {
                $products = Product::with('webcategories', 'brand','product_locations','product_states')
                ->wherehas('product_locations',function($q) {
                    $q->where('product_locations.location_id',config('services.b2b.location_id'));
                })
                    ->where('productVisibility', 'public')
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->forCatalogListing(null)
                    ->whereIn('id', $slugArray)
                    // ->whereHas('webcategories', function ($query) use ($slug) {
                    //     $query
                    //     // ->where('slug', $slug)
                    //         ->where('category_type', 'product')
                    //         ->where('visibility', 'public');
                    // })
                    ->whereDoesntHave('webcategories', function ($query) {
                        $query->where('visibility', 'protected');
                    })
                    ->leftJoin('variations as price_variations', 'products.id', '=', 'price_variations.product_id')
                    ->leftJoin('variation_location_details as vld', 'price_variations.id', '=', 'vld.variation_id')
                    ->where('vld.location_id', config('services.b2b.location_id'))
                    ->select('products.*', 'price_variations.sell_price_inc_tax as ad_price'); //sell price for non-auth users
                //sorting 
                switch ($sortBy) {
                    case 'low-to-high':
                        $products = $products->orderBy('price_variations.sell_price_inc_tax', 'asc');
                        break;
                    case 'high-to-low':
                        $products = $products->orderBy('price_variations.sell_price_inc_tax', 'desc');
                        break;
                    case 'top-selling':
                        $products = $products->orderBy('top_selling', 'desc');
                        break;
                    case 'latest':
                    default:
                        $products = $products->orderBy('created_at', 'desc');
                        break;
                }
                $products = $products->paginate($perPage, ['*'], 'page', $page);
            }
            return response()->json([
                'status' => true,
                'data' => [
                    'current_page' => $products->currentPage(),
                    'data' => CateLogResource::collection($products->getCollection()),
                    'last_page' => $products->lastPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'per_page' => $products->perPage(),
                    'to' => $products->lastItem(),
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'categoryProducts function failed', 'error' => $th->getMessage() . ' on ' . $th->getFile()]);
        }
    }


    /**
     * Summary of productLookup for staff
     * @param \Illuminate\Http\Request $request
     * @query s search query
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function productLookup(Request $request){
        $search = $request->query('s');
        try {
            $variation = Variation::with(['product', 'product.webcategories', 'product.brand', 'variation_location_details','group_prices','group_prices.groupInfo','media','product.product_gallery_images','product.product_locations'])
            ->wherehas('product.product_locations',function($q) {
                $q->where('product_locations.location_id',config('services.b2b.location_id'));
            })
                        ->where('var_barcode_no', $search)
                        ->first();
                        
        if (!$variation) {
            $variation = Variation::with(['product', 'product.webcategories', 'product.brand', 'variation_location_details','group_prices','group_prices.groupInfo','media','product.product_gallery_images'])
                ->where('sub_sku', $search)
                ->first();
        }
        if($variation){
            return response()->json(['status' => true, 'data' => $variation]);
        }
            return response()->json(['status' => false, 'message' => 'Product Not Found', 'error' => 'Check Url']);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'productLookup function failed', 'error' => $th->getMessage() . ' on ' . $th->getFile() . ' on line ' . $th->getLine()],500);
        }
    }



    


    //temp function 
    protected $productSlug;
    protected $nextProductSlug;
    private function downloadAndStoreLogo(string $url, $brandLogo = false, $pid = null, $v = false): string
    {
        if ($brandLogo) {
            $apiUrl = 'http://127.0.0.1:8080/api/thumbnail/' . $url;
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
    // public function storeProduct(Request $request, $id)
    // {
    //     $pid = (int) $id;
    //     SyncProduct::dispatch($pid);
    // }
    public function syncUser(){
        SyncCustomer::dispatch();
        return response()->json(['status' => true, 'message' => 'Syncing User']);
    }
    public function syncBrandCategories()
    {
        try {
            $brands = Brands::all();
            $totalBrands = $brands->count();
            $processedBrands = 0;

            foreach ($brands as $brand) {
                // Get the first active product for this brand
                $product = Product::where('brand_id', $brand->id)
                    ->where('enable_selling', 1)
                    ->whereNotNull('category_id')
                    ->where('is_inactive', 0)
                    ->first();

                if ($product) {
                    $categoryId = $product->category_id;
                    $brand->category = $categoryId;
                    $brand->save();
                } else {
                    // If no active product found, set category to null
                    $brand->category = null;
                    $brand->save();
                }

                $processedBrands++;

                // Log progress
                if ($processedBrands % 100 == 0) {
                    Log::info("Processed $processedBrands out of $totalBrands brands");
                }
            }

            Log::info('Brand categories synced successfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand categories synced successfully'
            ]);
            
        } catch (\Throwable $th) {
            Log::error('Failed to sync brand categories: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to sync brand categories',
                'error' => $th->getMessage()
            ], 500);
        }
    }

}
