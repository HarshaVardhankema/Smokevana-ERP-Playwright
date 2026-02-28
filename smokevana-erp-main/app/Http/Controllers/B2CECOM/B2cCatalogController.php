<?php

namespace App\Http\Controllers\B2CECOM;

use App\Brands;
use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CateLogResource;
use App\Http\Resources\ProductResource;
use App\Product;
use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class B2cCatalogController extends Controller
{
    /**
     * B2C shop page api 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shopProducts(Request $request)
    {
        $searchTerm = $request->input('s', '');
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'popularity');
        $page = $request->query('page', 1);
        $locationId = $request->route('location_id');
        $brandName = $request->route('brand_name');
        $categorIds = $request->query('categoryIds',false);
        $byState = $request->query('byState'); // Get state filter parameter
        
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
        $products = Product::with('webcategories', 'brand')
            
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
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

        // Always restrict to selected brand, if a brand name and location are present in the URL
        if (!empty($brandName) && !empty($locationId)) {
            $products = $products->whereHas('brand', function ($query) use ($brandName, $locationId) {
                // Restrict by slug and location_id.
                $query->where('slug', $brandName)
                    ->where('location_id', $locationId);
            });
        }

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
        // return response()->json([ 'status' => true, 'message' => 'Shop products successful', 'data' => $products]);
    }
    /**
     * Summary of singleProduct
     * @param \Illuminate\Http\Request $request
     * @param mixed $slug
     * @return \Illuminate\Http\JsonResponse
     */

    public function singleProduct(Request $request, $slug){
        $slug = $request->route('slug');
        $product = Product::with([
            'webcategories',
            'brand',
            'product_variations',
            'product_gallery_images' => function ($query) {
                $query->select('id', 'product_id', 'image_path');
            },
            'variations' => function ($query) {
                $query->select([
                    'variations.id',
                    'variations.name',
                    'variations.product_id',
                    'variations.var_barcode_no',
                    'variations.var_maxSaleLimit',
                    'variations.product_variation_id',
                    'variations.sell_price_inc_tax as b2c_price',
                    'variation_location_details.in_stock_qty as qty',
                ])

                    ->leftJoin('variation_location_details', function ($join) {
                        $join->on('variations.id', '=', 'variation_location_details.variation_id');
                    })
                    ->leftJoin('products', function ($join) {
                        $join->on('variations.product_id', '=', 'products.id');
                    })
                    ->where(function($query) {
                        $query->where('variation_location_details.in_stock_qty', '>', 0)
                            ->orWhere('products.enable_stock', '=', 0);
                    });
            },
            'variations.media' => function ($query) {
                $query->select('id', 'file_name', 'model_id');
            }
        ])
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->where('slug', $slug)
            ->first();



            // dd($product);
        
            $categoryIds = isset($product->webcategories) ?
            $product->webcategories->where('category_type', 'product')
            // ->where('parent_id', '!=', 0)
            ->pluck('id')
            ->toArray() :
            [1];
            
        $relatedProducts = Product::with('webcategories', 'brand', 'product_locations')
            ->wherehas('product_locations', function ($q) {
                $q->where('product_locations.location_id',request()->route('location_id'));
            })
            ->where('productVisibility', 'public')
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->where('id', '!=', $product->id) 
            ->where('brand_id', $product->brand_id) 
            ->whereHas('webcategories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->addSelect([
                'b2c_price' => Variation::select('sell_price_inc_tax')
                    ->whereColumn('product_id', 'products.id')
                    ->where('sell_price_inc_tax', '>', 0)
                    ->orderBy('sell_price_inc_tax')
                    ->whereNotNull('sell_price_inc_tax')
                    ->limit(1)
            ])
            ->limit(20)
            ->get();
            // dd($relatedProducts); 

            if($relatedProducts->count() < 20){
                $remainingCount = 20 - $relatedProducts->count();
                $otherProducts = Product::with('webcategories', 'brand', 'product_locations')
                ->wherehas('product_locations', function ($q) {
                    $q->where('product_locations.location_id', request()->route('location_id'));
                })
                ->where('productVisibility', 'public')
                ->where('enable_selling', 1)
                ->where('is_inactive', 0)
                ->where('id', '!=', $product->id) 
                ->where('brand_id', $product->brand_id) 
                ->addSelect([
                    'b2c_price' => Variation::select('sell_price_inc_tax')
                        ->whereColumn('product_id', 'products.id')
                        ->where('sell_price_inc_tax', '>', 0)
                        ->orderBy('sell_price_inc_tax')
                        ->whereNotNull('sell_price_inc_tax')
                        ->limit(1)
                ])
                ->limit($remainingCount)
                ->get();
                $relatedProducts = $relatedProducts->concat($otherProducts)->unique('id');
            }

        if ($product) {
            return response()->json([
                'status' => true,
                'data' => new ProductResource($product),
                // 'recommendedProducts' => CateLogResource::collection($recommendedProducts),
                'relatedProducts' => CateLogResource::collection($relatedProducts)
            ]);
        }
        return response()->json(['status' => false, 'message' => 'Product Not Found', 'error' => 'Check Url']);
    }

    /**
     * Summary of searchProducts
     * @param \Illuminate\Http\Request $request
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
        $locationId = $request->route('location_id');
        $brandName = $request->route('brand_name');

        $productsQuery = Product::with('webcategories', 'brand')
            ->whereHas('brand', function ($query) use ($brandName, $locationId) {
                $query->where('slug', $brandName)
                    ->where('location_id', $locationId);
            })
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
            ->addSelect([
                'b2c_price' => Variation::select('sell_price_inc_tax')
                    ->whereColumn('product_id', 'products.id')
                    ->where('sell_price_inc_tax', '>', 0)
                    ->orderBy('sell_price_inc_tax')
                    ->whereNotNull('sell_price_inc_tax')
                    ->limit(1)
            ]);
        
        if (!empty($searchTerm)) {
            $searchWords = preg_split('/\s+/', $searchTerm);
            $regexPattern = implode('.*', array_map(function ($word) {
                return "(?=.*" . preg_quote($word) . ")";
            }, $searchWords));
            $productsQuery->where(function ($query) use ($regexPattern) {
                $query->where('products.name', 'REGEXP', $regexPattern)
                    ->orWhere('products.sku', 'REGEXP', $regexPattern)
                    ->orWhereHas('webcategories', function ($q) use ($regexPattern) {
                        $q->where('name', 'REGEXP', $regexPattern);
                    });
            });
        }

        if ($minPrice !== null) {
            $productsQuery->havingRaw('b2c_price >= ?', [(float)$minPrice]);
        }
        if ($maxPrice !== null) {
            $productsQuery->havingRaw('b2c_price <= ?', [(float)$maxPrice]);
        }

        // sorting 
        switch ($sortBy) {
            case 'low-to-high':
                $productsQuery->orderBy('b2c_price', 'asc');
                break;
            case 'high-to-low':
                $productsQuery->orderBy('b2c_price', 'desc');
                break;
            case 'top-selling':
                $productsQuery->orderBy('top_selling', 'desc');
                break;
            case 'latest':
            default:
                $productsQuery->orderBy('products.created_at', 'desc');
                break;
        }
        //final pagination 
        $products = $productsQuery->paginate($perPage, ['*'], 'page', $page);

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
    }
    /**
     * Summary of multiCategory
     * @param \Illuminate\Http\Request $request
     * @param mixed $slugs
     * @return \Illuminate\Http\JsonResponse
     */
    public function multiCategory(Request $request, $slugs)
    {
        $slugs = $request->route('slugs');
        $location_id = $request->route('location_id');
        $brand_name = $request->route('brand_name');
        $slugArray = explode(',', $slugs);
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'popularity');
        $page = $request->query('page', 1);




        $products = Product::with('webcategories', 'brand', 'product_locations')
            ->whereHas('brand', function ($query) use ($brand_name, $location_id) {
                $query->where('slug', $brand_name)
                    ->where('location_id', $location_id);
            })
            ->whereHas('webcategories', function ($query) use ($slugArray) {
                $query->whereIn('slug', $slugArray)
                      ->where('category_type', 'product');
            })
            ->whereHas('product_locations', function($q) use ($location_id) {
                $q->where('product_locations.location_id', $location_id);
            })
            ->where('enable_selling', 1)
            ->where('is_inactive', 0)
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
                $products = $products->orderBy('b2c_price', 'desc');
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
                // 'first_page_url' => $products->url(1),
                'from' => $products->firstItem(),
                // 'last_page_url' => $products->url($products->lastPage()),
                // 'next_page_url' => $products->nextPageUrl(),
                'per_page' => $products->perPage(),
                // 'prev_page_url' => $products->previousPageUrl(),
                'to' => $products->lastItem(),
            ]
        ]);
    }


    public function allCategories(Request $request)
    {
        $brandName = $request->route('brand_name');
        $locationId = $request->route('location_id');
        $brand = $request->get('current_brand');
        
        // Use the new many-to-many relationship for B2C
        $categories = Category::with('brandCategories')
            ->whereHas('brandCategories', function ($query) use ($brand, $locationId) {
                if ($locationId) {
                    $query->where('brand_id', $brand->id)
                        ->where('location_id', $locationId);
                }
            })
            ->select('categories.*')
            ->get()->makeHidden('brandCategories');
            
        return response()->json([
            'status' => true,
            'data' => $categories
        ]);
    }
    public function sideMenu(Request $request)
    {
        $locationId = $request->route('location_id');
        $brandName = $request->route('brand_name');

        // Validate required parameters
        if (!$locationId || !$brandName) {
            return response()->json(['status' => false, 'message' => 'Location ID and Brand Name are required']);
        }

        try {
            // First, find the brand by slug and location_id
            $brand = \App\Brands::where('slug', $brandName)
                ->where('location_id', $locationId)
                ->where('visibility', 'public')
                ->first();

            if (!$brand) {
                return response()->json(['status' => false, 'message' => 'Brand not found']);
            }

            // Get the category that this brand belongs to and its children
            $categories = Category::with('brandCategories')->wherehas('brandCategories',function($query) use ($brandName){
                 {
                    $query->where('slug',$brandName)
                    ;
                }
            } )
            ->get()->makeHidden('brandCategories');
            
            
           
            

            // Get other brands for the same location (excluding current brand)
            

            return response()->json([
                'status' => true, 
                'categories' => $categories, 
                
                'current_brand' => $brand       
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Side menu error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Error while fetching Side menu: ' . $e->getMessage()]);
        }
    }


}
