<?php

namespace App\Http\Controllers\UnifiedB2c;

use App\Brands;
use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CateLogResource;
use App\Http\Resources\ProductResource;
use App\Product;
use App\Variation;
use Illuminate\Http\Request;

class B2cCatalogController extends Controller
{
    /**
     * B2C shop page api location wise
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shopProducts(Request $request)
    {
        //die('in unified b2c catalog controller');
        $searchTerm = $request->input('s', '');
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'popularity');
        $page = $request->query('page', 1);
        $locationId = $request->route('location_id');
        $categorIds = $request->query('categoryIds', false);
        $byState = $request->query('byState'); // Get state filter parameter

        if ($categorIds) {
            $categorIds = explode(',', $categorIds);
        }
        $regexPattern = null;
        if (!empty($searchTerm)) {
            $searchWords = preg_split('/\s+/', $searchTerm);
            $regexPattern = implode('.*', array_map(function ($word) {
                return "(?=.*" . preg_quote($word) . ")";
            }, $searchWords));
        }
        $products = Product::with('webcategories', 'brand')

            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->whereHas('product_locations', function ($query) use ($locationId) {
                $query->where('product_locations.location_id', $locationId);
            })
            ->addSelect([
                'b2c_price' => Variation::select('sell_price_inc_tax')
                    ->whereColumn('product_id', 'products.id')
                    ->where('sell_price_inc_tax', '>', 0)
                    ->orderBy('sell_price_inc_tax')
                    ->whereNotNull('sell_price_inc_tax')
                    ->limit(1)
            ])
            ->when(is_array($categorIds), function ($query) use ($categorIds) {
                $query->whereHas('webcategories', function ($query) use ($categorIds) {
                    $query->whereIn('categories.id', $categorIds);
                });
            });

         
        // Filter by state if byState parameter is provided
        if (!empty($byState)) {
            $products = $products->where(function ($query) use ($byState) {
                // Products available in all states
                $query->where('state_check', 'all')
                    // OR products that are specifically allowed in this state
                    ->orWhere(function ($query) use ($byState) {
                        $query->where('state_check', 'in')
                            ->whereHas('product_states', function ($query) use ($byState) {
                                $query->where('state', strtoupper($byState));
                            });
                    })
                    // OR products that are NOT restricted in this state
                    ->orWhere(function ($query) use ($byState) {
                        $query->where('state_check', 'not_in')
                            ->whereDoesntHave('product_states', function ($query) use ($byState) {
                                $query->where('state', strtoupper($byState));
                            });
                    });
            });
        }

        // If there is a search term, apply filter (REGEXP) but *not* on brands (since brand filter already restricted above)
        if ($regexPattern) {
            $products = $products->where(function ($query) use ($regexPattern) {
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
                $products = $products->orderBy('b2c_price', 'asc');
                break;
            case 'high-to-low':
                $products = $products->orderBy('b2c_price', 'desc');
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
            ]
        ]);
    }
    /**
     * Get side menu for location-wise categories
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sideMenu(Request $request)
    {
        $locationId = $request->route('location_id');
        
        try {
            // Get categories for the location that have children
            $categories = Category::getCategoriesHierarchy()
                ->where('location_id', $locationId)
                ->where('visibility', 'public')
                ->whereHas('children')
                ->get();

            // Get brands for the location
            $brands = Brands::where('location_id', $locationId)
                ->where('visibility', 'public')
                ->whereNotNull('slug')
                ->get();

            return response()->json([
                'status' => true, 
                'categories' => $categories, 
                'brands' => $brands
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false, 
                'message' => 'Error while fetching Side menu', 
                'error' => $th->getMessage()
            ]);
        }
    }
 /**
     * Get products for multiple categories (location-wise)
     * @param Request $request
     * @param string $slugs
     * @return \Illuminate\Http\JsonResponse
     */
    public function multiCategory(Request $request, $slugs)
    {
        $slugArray = explode(',', $slugs); 
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'popularity');
        $page = $request->query('page', 1);
        $locationId = $request->route('location_id');
        $byState = $request->query('byState');
    
        try {
            $products = Product::with('webcategories', 'brand', 'product_locations', 'product_states')
                ->whereHas('product_locations', function ($query) use ($locationId) {
                    $query->where('product_locations.location_id', $locationId);
                })
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->whereHas('webcategories', function ($query) use ($slugArray) {
                    $query->whereIn('slug', $slugArray)
                        ->where('category_type', 'product')
                        ->where('visibility', 'public');
                })
                ->addSelect([
                    'b2c_price' => Variation::select('sell_price_inc_tax')
                        ->whereColumn('product_id', 'products.id')
                        ->where('sell_price_inc_tax', '>', 0)
                        ->orderBy('sell_price_inc_tax')
                        ->whereNotNull('sell_price_inc_tax')
                        ->limit(1)
                ])
                ->when(!empty($byState), function($query) use ($byState) {
                    $query->where(function ($q) use ($byState) {
                        $q->where('state_check', 'all')
                            ->orWhere(function ($q) use ($byState) {
                                $q->where('state_check', 'in')
                                    ->whereHas('product_states', function ($q) use ($byState) {
                                        $q->where('state', strtoupper($byState));
                                    });
                            })
                            ->orWhere(function ($q) use ($byState) {
                                $q->where('state_check', 'not_in')
                                    ->whereDoesntHave('product_states', function ($q) use ($byState) {
                                        $q->where('state', strtoupper($byState));
                                    });
                            });
                    });
                });

            // Sorting logic
            switch ($sortBy) {
                case 'low-to-high':
                    $products = $products->orderBy('b2c_price', 'asc');
                    break;
                case 'high-to-low':
                    $products = $products->orderBy('b2c_price', 'desc');
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
            return response()->json([
                'status' => false, 
                'message' => 'categoryProducts function failed', 
                'error' => $th->getMessage()
            ]);
        }
    }
/**
     * Get products for a brand (location-wise)
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function brandProducts(Request $request, $slug)
    {
        // Get slug from route parameter (function parameter might be wrong due to route binding)
        $brandSlug = $request->route('slug') ?? $slug;
        $slugArray = explode(',', $brandSlug);
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'popularity');
        $page = $request->query('page', 1);
        $locationId = $request->route('location_id');
        
        // Ensure locationId is an integer
        if (is_string($locationId)) {
            $locationId = (int) $locationId;
        }
    
        // Use brandSlug instead of slug for the query
        $categorySlugs = $request->query('category', false);
        $byState = $request->query('byState', false);

        if($categorySlugs){
            $categorySlugs = explode(',', $categorySlugs);
        }

        try {
            $products = Product::with('webcategories', 'brand', 'product_locations', 'product_states')
                ->whereHas('product_locations', function ($query) use ($locationId) {
                    $query->where('product_locations.location_id', $locationId);
                })
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->whereHas('brand', function ($query) use ($slugArray) {
                    $query->whereIn('brands.slug', $slugArray)
                        ->where('brands.visibility', 'public');
                })
                ->when(!empty($categorySlugs), function($query) use ($categorySlugs) {
                    $query->whereHas('webcategories', function($q) use ($categorySlugs) {
                        $q->whereIn('slug', $categorySlugs)
                            ->where('category_type', 'product')
                            ->where('visibility', 'public');
                    });
                })
                ->when(!empty($byState), function($query) use ($byState) {
                    $query->where(function ($q) use ($byState) {
                        $q->where('state_check', 'all')
                            ->orWhere(function ($q) use ($byState) {
                                $q->where('state_check', 'in')
                                    ->whereHas('product_states', function ($q) use ($byState) {
                                        $q->where('state', strtoupper($byState));
                                    });
                            })
                            ->orWhere(function ($q) use ($byState) {
                                $q->where('state_check', 'not_in')
                                    ->whereDoesntHave('product_states', function ($q) use ($byState) {
                                        $q->where('state', strtoupper($byState));
                                    });
                            });
                    });
                })
                ->addSelect([
                    'b2c_price' => Variation::select('sell_price_inc_tax')
                        ->whereColumn('product_id', 'products.id')
                        ->where('sell_price_inc_tax', '>', 0)
                        ->orderBy('sell_price_inc_tax')
                        ->whereNotNull('sell_price_inc_tax')
                        ->limit(1)
                ]);

            // Sorting logic
            switch ($sortBy) {
                case 'low-to-high':
                    $products = $products->orderByRaw('b2c_price ASC NULLS LAST');
                    break;
                case 'high-to-low':
                    $products = $products->orderByRaw('b2c_price DESC NULLS LAST');
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
            
            return response()->json([
                'status' => true,
                'data' => [
                    'current_page' => $products->currentPage(),
                    'data' => ProductResource::collection($products->getCollection()),
                    'last_page' => $products->lastPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'per_page' => $products->perPage(),
                    'to' => $products->lastItem(),
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false, 
                'message' => 'Brand function failed', 
                'error' => $th->getMessage()
            ]);
        }
    }
 /**
     * Search products (location-wise)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchProducts(Request $request)
    {
        $searchTerm = $request->input('s', '');
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'default');
        $page = $request->query('page', 1);
        $locationId = $request->route('location_id');
        $byState = $request->query('byState', false);

        try {
            $regexPattern = null;
            if (!empty($searchTerm)) {
                $searchWords = preg_split('/\s+/', $searchTerm);
                $regexPattern = implode('.*', array_map(function ($word) {
                    return "(?=.*" . preg_quote($word) . ")";
                }, $searchWords));
            }

            $products = Product::with('webcategories', 'brand', 'product_locations', 'product_states')
                ->whereHas('product_locations', function ($query) use ($locationId) {
                    $query->where('product_locations.location_id', $locationId);
                })
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->when($regexPattern, function($query) use ($regexPattern) {
                    $query->where(function ($q) use ($regexPattern) {
                        $q->where('products.name', 'REGEXP', $regexPattern)
                            ->orWhere('products.sku', 'REGEXP', $regexPattern)
                            ->orWhereHas('webcategories', function ($subQuery) use ($regexPattern) {
                                $subQuery->where('name', 'REGEXP', $regexPattern);
                            });
                    });
                })
                ->when(!empty($byState), function($query) use ($byState) {
                    $query->where(function ($q) use ($byState) {
                        $q->where('state_check', 'all')
                            ->orWhere(function ($q) use ($byState) {
                                $q->where('state_check', 'in')
                                    ->whereHas('product_states', function ($q) use ($byState) {
                                        $q->where('state', strtoupper($byState));
                                    });
                            })
                            ->orWhere(function ($q) use ($byState) {
                                $q->where('state_check', 'not_in')
                                    ->whereDoesntHave('product_states', function ($q) use ($byState) {
                                        $q->where('state', strtoupper($byState));
                                    });
                            });
                    });
                })
                ->addSelect([
                    'b2c_price' => Variation::select('sell_price_inc_tax')
                        ->whereColumn('product_id', 'products.id')
                        ->where('sell_price_inc_tax', '>', 0)
                        ->orderBy('sell_price_inc_tax')
                        ->whereNotNull('sell_price_inc_tax')
                        ->limit(1)
                ]);

            // Sorting logic
            switch ($sortBy) {
                case 'low-to-high':
                    $products = $products->orderBy('b2c_price', 'asc');
                    break;
                case 'high-to-low':
                    $products = $products->orderBy('b2c_price', 'desc');
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
            return response()->json([
                'status' => false, 
                'message' => 'Search function failed', 
                'error' => $th->getMessage()
            ]);
        }
    }
 /**
     * Get all products (location-wise)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allProducts(Request $request)
    {
        $searchTerm = $request->input('s', '');
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'default');
        $page = $request->query('page', 1);
        $locationId = $request->route('location_id');
        $byState = $request->query('byState', false);

        try {
            $regexPattern = null;
            if (!empty($searchTerm)) {
                $searchWords = preg_split('/\s+/', $searchTerm);
                $regexPattern = implode('.*', array_map(function ($word) {
                    return "(?=.*" . preg_quote($word) . ")";
                }, $searchWords));
            }

            $products = Product::with('webcategories', 'brand', 'product_locations', 'product_states')
                ->whereHas('product_locations', function ($query) use ($locationId) {
                    $query->where('product_locations.location_id', $locationId);
                })
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->when($regexPattern, function($query) use ($regexPattern) {
                    $query->where(function ($q) use ($regexPattern) {
                        $q->where('products.name', 'REGEXP', $regexPattern)
                            ->orWhere('products.sku', 'REGEXP', $regexPattern)
                            ->orWhereHas('webcategories', function ($subQuery) use ($regexPattern) {
                                $subQuery->where('name', 'REGEXP', $regexPattern);
                            });
                    });
                })
                ->when(!empty($byState), function($query) use ($byState) {
                    $query->where(function ($q) use ($byState) {
                        $q->where('state_check', 'all')
                            ->orWhere(function ($q) use ($byState) {
                                $q->where('state_check', 'in')
                                    ->whereHas('product_states', function ($q) use ($byState) {
                                        $q->where('state', strtoupper($byState));
                                    });
                            })
                            ->orWhere(function ($q) use ($byState) {
                                $q->where('state_check', 'not_in')
                                    ->whereDoesntHave('product_states', function ($q) use ($byState) {
                                        $q->where('state', strtoupper($byState));
                                    });
                            });
                    });
                })
                ->addSelect([
                    'b2c_price' => Variation::select('sell_price_inc_tax')
                        ->whereColumn('product_id', 'products.id')
                        ->where('sell_price_inc_tax', '>', 0)
                        ->orderBy('sell_price_inc_tax')
                        ->whereNotNull('sell_price_inc_tax')
                        ->limit(1)
                ]);

            // Sorting logic
            switch ($sortBy) {
                case 'low-to-high':
                    $products = $products->orderBy('b2c_price', 'asc');
                    break;
                case 'high-to-low':
                    $products = $products->orderBy('b2c_price', 'desc');
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
            return response()->json([
                'status' => false, 
                'message' => 'All products function failed', 
                'error' => $th->getMessage()
            ]);
        }
    }
 /**
     * Get single product details (location-wise)
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function singleProduct(Request $request, $slug)
    {
        try {
            $locationId = $request->route('location_id');
            
            // Get slug from route parameter (function parameter might be wrong due to route binding)
            $productSlug = $request->route('slug') ?? $slug;
            
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
                    $query->with(['contact' => function($q) {
                        $q->select('id', 'name', 'first_name', 'last_name', 'email');
                    }])
                    ->select('id', 'product_id', 'contact_id', 'description', 'rating', 'likes', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->limit(50);
                },
                'variations' => function ($query) use ($locationId) {
                    $query->select([
                        'variations.id',
                        'variations.name',
                        'variations.product_id',
                        'variations.var_barcode_no',
                        'variations.var_maxSaleLimit',
                        'variations.product_variation_id',
                        'variations.sell_price_inc_tax as ad_price',
                        'variation_location_details.in_stock_qty as qty',
                    ])
                        ->leftJoin('variation_location_details', function ($join) use ($locationId) {
                            $join->on('variations.id', '=', 'variation_location_details.variation_id')
                                ->where('variation_location_details.location_id', $locationId);
                        })
                        ->leftJoin('products', function ($join) {
                            $join->on('variations.product_id', '=', 'products.id');
                        })
                        ->where('variations.sell_price_inc_tax', '>', 0)
                        ->whereNotNull('variations.sell_price_inc_tax')
                        ->where(function($query) {
                            $query->where('variation_location_details.in_stock_qty', '>', 0)
                                ->orWhere('products.enable_stock', '=', 0);
                        });
                },
                'variations.media' => function($query) {
                    $query->select('id', 'file_name', 'model_id');
                }
            ])
            ->whereHas('product_locations', function ($query) use ($locationId) {
                $query->where('product_locations.location_id', $locationId);
            })
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->where('products.slug', $productSlug)
            ->first();

            if (!$product) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Product Not Found', 
                    'error' => 'Check Url'
                ]);
            }

            // Get recommended products (same brand, same location)
            $recommendedProducts = Product::with('webcategories', 'brand', 'product_locations', 'product_states')
                ->whereHas('product_locations', function ($query) use ($locationId) {
                    $query->where('product_locations.location_id', $locationId);
                })
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->where('products.slug', '!=', $productSlug)
                ->when($product->brand, function($query) use ($product) {
                    return $query->whereHas('brand', function($q) use ($product) {
                        $q->where('id', $product->brand->id);
                    });
                })
                ->addSelect([
                    'b2c_price' => Variation::select('sell_price_inc_tax')
                        ->whereColumn('product_id', 'products.id')
                        ->where('sell_price_inc_tax', '>', 0)
                        ->orderBy('sell_price_inc_tax')
                        ->whereNotNull('sell_price_inc_tax')
                        ->limit(1)
                ])
                ->limit(10)
                ->get();

            // Get related products (same category, same location)
            $productCategoryIds = $product->webcategories 
                ? $product->webcategories->where('category_type', 'product')->where('parent_id', '!=', 0)->pluck('id')->toArray() 
                : [1];

            $relatedProducts = Product::with('webcategories', 'brand', 'product_locations', 'product_states')
                ->whereHas('product_locations', function ($query) use ($locationId) {
                    $query->where('product_locations.location_id', $locationId);
                })
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->where('products.slug', '!=', $productSlug)
                ->when($product->brand, function($query) use ($product) {
                    return $query->whereDoesntHave('brand', function($q) use ($product) {
                        $q->where('id', $product->brand->id);
                    });
                })
                ->whereHas('webcategories', function($query) use ($productCategoryIds) {
                    $query->whereIn('categories.id', $productCategoryIds);
                })
                ->addSelect([
                    'b2c_price' => Variation::select('sell_price_inc_tax')
                        ->whereColumn('product_id', 'products.id')
                        ->where('sell_price_inc_tax', '>', 0)
                        ->orderBy('sell_price_inc_tax')
                        ->whereNotNull('sell_price_inc_tax')
                        ->limit(1)
                ])
                ->limit(50)
                ->get();

            return response()->json([
                'status' => true, 
                'data' => new ProductResource($product), 
                'recommendedProducts' => CateLogResource::collection($recommendedProducts), 
                'relatedProducts' => CateLogResource::collection($relatedProducts)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false, 
                'message' => 'Single Product function failed', 
                'error' => $th->getMessage() . ' on ' . $th->getFile() . ' on line ' . $th->getLine()
            ]);
        }
    }

    /**
     * Get product list (location-wise)
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function productList(Request $request, $slug)
    {
        // Get slug from route parameter (function parameter might be wrong due to route binding)
        $productSlug = $request->route('slug') ?? $slug;
        $slugArray = explode(',', $productSlug);
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'popularity');
        $page = $request->query('page', 1);
        $locationId = $request->route('location_id');
        $byState = $request->query('byState', false);

        try {
            $products = Product::with('webcategories', 'brand', 'product_locations', 'product_states')
                ->whereHas('product_locations', function ($query) use ($locationId) {
                    $query->where('product_locations.location_id', $locationId);
                })
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->whereIn('products.slug', $slugArray)
                ->when(!empty($byState), function($query) use ($byState) {
                    $query->where(function ($q) use ($byState) {
                        $q->where('state_check', 'all')
                            ->orWhere(function ($q) use ($byState) {
                                $q->where('state_check', 'in')
                                    ->whereHas('product_states', function ($q) use ($byState) {
                                        $q->where('state', strtoupper($byState));
                                    });
                            })
                            ->orWhere(function ($q) use ($byState) {
                                $q->where('state_check', 'not_in')
                                    ->whereDoesntHave('product_states', function ($q) use ($byState) {
                                        $q->where('state', strtoupper($byState));
                                    });
                            });
                    });
                })
                ->addSelect([
                    'b2c_price' => Variation::select('sell_price_inc_tax')
                        ->whereColumn('product_id', 'products.id')
                        ->where('sell_price_inc_tax', '>', 0)
                        ->orderBy('sell_price_inc_tax')
                        ->whereNotNull('sell_price_inc_tax')
                        ->limit(1)
                ]);

            // Sorting logic
            switch ($sortBy) {
                case 'low-to-high':
                    $products = $products->orderBy('b2c_price', 'asc');
                    break;
                case 'high-to-low':
                    $products = $products->orderBy('b2c_price', 'desc');
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
            return response()->json([
                'status' => false, 
                'message' => 'Product list function failed', 
                'error' => $th->getMessage()
            ]);
        }
    }
 /**
     * Get brand list (location-wise)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function brandList(Request $request)
    {
        $locationId = $request->route('location_id');
        
        try {
            $brands = Brands::where('location_id', $locationId)
                ->where('visibility', 'public')
                ->whereNotNull('slug')
                ->get();

            return response()->json([
                'status' => true,
                'brands' => $brands
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false, 
                'message' => 'Error while fetching Brands', 
                'error' => $th->getMessage()
            ]);
        }
    }

    /**
     * Get all categories (location-wise)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allCategories(Request $request)
    {
        $locationId = $request->route('location_id');
        
        try {
            $categories = Category::where('location_id', $locationId)
                ->where('visibility', 'public')
                ->where('category_type', 'product')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $categories
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false, 
                'message' => 'Error while fetching Categories', 
                'error' => $th->getMessage()
            ]);
        }
    }
}
