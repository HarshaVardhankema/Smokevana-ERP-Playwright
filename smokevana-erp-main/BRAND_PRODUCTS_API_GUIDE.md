# Multi-Brand Products API Guide

## Overview
This guide explains how to create an API endpoint to filter products by multiple brands (e.g., moonbuzz, biohydroxy, curevana).

## Current Implementation

### Existing Route
```
GET /api/brand-product/{slug}
```
- `slug` can be a single brand slug or comma-separated (e.g., `moonbuzz,biohydroxy,curevana`)
- Example: `/api/brand-product/moonbuzz` or `/api/brand-product/moonbuzz,biohydroxy`

### How It Works
1. Products have a `brand` relationship
2. Brands have a `slug` field (e.g., 'moonbuzz', 'biohydroxy', 'curevana')
3. The `brandProducts` method in `CatalogController` filters products by brand slug(s)

## Creating Your New API Endpoint

### Option 1: Query Parameter Based (Recommended)

**Route:** `GET /api/products/by-brands`

**Query Parameters:**
- `brands` - Comma-separated brand slugs (e.g., `?brands=moonbuzz,biohydroxy,curevana`)
- `page` - Page number (default: 1)
- `perPage` - Items per page (default: 16)
- `sort` - Sort order: `latest`, `low-to-high`, `high-to-low`, `top-selling` (default: `latest`)
- `category` - Optional category slug filter
- `byState` - Optional state filter

**Example Request:**
```
GET /api/products/by-brands?brands=moonbuzz,biohydroxy,curevana&page=1&perPage=20&sort=latest
```

### Implementation Steps

#### Step 1: Add Route in `routes/api.php`

```php
Route::get('/products/by-brands', [CatalogController::class, 'getProductsByBrands']);
```

#### Step 2: Create Method in `app/Http/Controllers/ECOM/CatalogController.php`

```php
/**
 * Get products filtered by multiple brands
 * 
 * GET /api/products/by-brands?brands=moonbuzz,biohydroxy,curevana&page=1&perPage=20
 * 
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function getProductsByBrands(Request $request)
{
    try {
        // Get query parameters
        $brandSlugs = $request->query('brands', '');
        $page = $request->query('page', 1);
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'latest');
        $categorySlugs = $request->query('category', false);
        $byState = $request->query('byState', false);

        // Validate brands parameter
        if (empty($brandSlugs)) {
            return response()->json([
                'status' => false,
                'message' => 'Brands parameter is required. Use comma-separated brand slugs (e.g., ?brands=moonbuzz,biohydroxy,curevana)'
            ], 400);
        }

        // Parse brand slugs (comma-separated)
        $slugArray = array_filter(array_map('trim', explode(',', $brandSlugs)));
        
        if (empty($slugArray)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid brands parameter. Please provide valid brand slugs.'
            ], 400);
        }

        // Parse category slugs if provided
        if ($categorySlugs) {
            $categorySlugs = array_filter(array_map('trim', explode(',', $categorySlugs)));
        }

        // Parse state filter if provided
        if ($byState) {
            $byState = is_array($byState) ? $byState : explode(',', $byState);
            $byState = array_filter(array_map('trim', $byState));
        }

        // Check authentication
        $authData = $this->authCheck($request);
        $locationId = config('services.b2b.location_id');

        if ($authData['status']) {
            // Authenticated user - use customer group pricing
            $contact = $authData['user'];
            $business_id = $contact->business_id ?? 1;
            $cg = $this->contactUtil->getCustomerGroup($business_id, $contact->id);
            $priceGroupId = $cg->price_group_id ?? null;

            $products = Product::with('webcategories', 'brand', 'product_locations', 'product_states')
                ->whereHas('product_locations', function($q) use ($locationId) {
                    $q->where('product_locations.location_id', $locationId);
                })
                ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                    $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                        ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                })
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->whereHas('brand', function ($query) use ($slugArray) {
                    $query->whereIn('slug', $slugArray);
                })
                ->when(!empty($categorySlugs), function($query) use ($categorySlugs) {
                    $query->whereHas('webcategories', function($q) use ($categorySlugs) {
                        $q->whereIn('slug', $categorySlugs)
                          ->where('category_type', 'product');
                    });
                })
                ->when(!empty($byState), function($query) use ($byState) {
                    foreach ($byState as $state) {
                        $query->where(function ($q) use ($state) {
                            $q->where('products.state_check', 'all')
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
                ')
                ->groupBy('products.id')
                ->havingRaw('ad_price > 0');

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

            $products = $products->paginate($perPage, ['*'], 'page', $page);

        } else {
            // Non-authenticated user - use public visibility
            $products = Product::with('webcategories', 'brand', 'product_locations', 'product_states')
                ->whereHas('product_locations', function($q) use ($locationId) {
                    $q->where('product_locations.location_id', $locationId);
                })
                ->where('productVisibility', 'public')
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->whereHas('brand', function ($query) use ($slugArray) {
                    $query->whereIn('slug', $slugArray)
                          ->where('visibility', 'public');
                })
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
                ->when(!empty($byState), function($query) use ($byState) {
                    foreach ($byState as $state) {
                        $query->where(function ($q) use ($state) {
                            $q->where('products.state_check', 'all')
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
                ->where('vld.location_id', $locationId)
                ->select('products.*', 'price_variations.sell_price_inc_tax as ad_price')
                ->groupBy('products.id');

            // Apply sorting
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

        // Format response
        return response()->json([
            'status' => true,
            'message' => 'Products retrieved successfully',
            'data' => [
                'current_page' => $products->currentPage(),
                'data' => ProductResource::collection($products->getCollection()),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'per_page' => $products->perPage(),
                'to' => $products->lastItem(),
                'brands_filtered' => $slugArray, // Show which brands were filtered
            ]
        ]);

    } catch (\Throwable $th) {
        \Log::error('Get products by brands failed', [
            'error' => $th->getMessage(),
            'file' => $th->getFile(),
            'line' => $th->getLine()
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Failed to retrieve products',
            'error' => $th->getMessage()
        ], 500);
    }
}
```

### Option 2: Using Existing Route (Simpler)

You can also use the existing route with comma-separated slugs:

```
GET /api/brand-product/moonbuzz,biohydroxy,curevana?page=1&perPage=20&sort=latest
```

This already works with the existing `brandProducts` method!

## Testing Your API

### Example cURL Requests

**Single Brand:**
```bash
curl "https://smokevanaerp.phantasm-agents.ai/api/products/by-brands?brands=moonbuzz&page=1&perPage=20"
```

**Multiple Brands:**
```bash
curl "https://smokevanaerp.phantasm-agents.ai/api/products/by-brands?brands=moonbuzz,biohydroxy,curevana&page=1&perPage=20&sort=latest"
```

**With Category Filter:**
```bash
curl "https://smokevanaerp.phantasm-agents.ai/api/products/by-brands?brands=moonbuzz,biohydroxy&category=vapes&page=1"
```

**With State Filter:**
```bash
curl "https://smokevanaerp.phantasm-agents.ai/api/products/by-brands?brands=moonbuzz&byState=CA,NY&page=1"
```

## Response Format

```json
{
  "status": true,
  "message": "Products retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 123,
        "name": "Product Name",
        "slug": "product-slug",
        "brand": {
          "id": 1,
          "name": "Moonbuzz",
          "slug": "moonbuzz"
        },
        "price": 29.99,
        // ... other product fields
      }
    ],
    "last_page": 5,
    "total": 100,
    "from": 1,
    "per_page": 20,
    "to": 20,
    "brands_filtered": ["moonbuzz", "biohydroxy", "curevana"]
  }
}
```

## Important Notes

1. **Brand Slugs**: Make sure the brand slugs match exactly (case-sensitive)
   - Check existing brands: `GET /api/brand-list`
   - Common slugs: `moonbuzz`, `biohydroxy`, `curevana`

2. **Authentication**: 
   - Authenticated users see customer group pricing
   - Non-authenticated users see public products only

3. **Location**: Products are filtered by location_id from config

4. **Product Visibility**:
   - Authenticated: All products they have access to
   - Non-authenticated: Only `public` visibility products

5. **Resource Class**: Uses `ProductResource` for consistent formatting

## Next Steps

1. Add the route to `routes/api.php`
2. Add the method to `CatalogController.php`
3. Test with your brand slugs
4. Adjust filters/sorting as needed

## Troubleshooting

- **No products returned**: Check if brand slugs exist in database
- **Wrong prices**: Verify customer group pricing setup
- **Missing products**: Check product visibility and location settings
