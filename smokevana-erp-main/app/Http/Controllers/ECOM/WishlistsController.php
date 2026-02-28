<?php

namespace App\Http\Controllers\ECOM;

use contact;
use App\Product;
use App\Wishlist;
use App\GuestWishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\CateLogResource;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class WishlistsController extends Controller
{
    /**
     * Resolve auth: either API user or validated guest session (current_guest_session from EcomUnifiedAuth).
     */
    private function authCheck($request)
    {
        $contact = Auth::guard('api')->user();
        if ($contact) {
            return [
                'status' => true,
                'is_guest' => false,
                'user' => $contact,
            ];
        }
        $guestSession = $request->attributes->get('current_guest_session');
        if ($guestSession) {
            return [
                'status' => true,
                'is_guest' => true,
                'guest' => $guestSession,
            ];
        }
        return [
            'status' => false,
            'message' => 'User not authenticated',
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        
        $perPage = $request->query('perPage', 16);
        $sortBy = $request->query('sort', 'popularity');
        $page = $request->query('page', 1);
        $auth = $this->authCheck($request);
        if (!$auth['status']) {
            return response()->json(['status' => false, 'message' => $auth['message']]);
        }

        try {
            $isGuest = !empty($auth['is_guest']);
            $productIds = [];

            if ($isGuest) {
                $guestSession = $auth['guest'];
                $productIds = GuestWishlist::where('guest_session_id', $guestSession->id)->pluck('product_id')->toArray();
            } else {
                $productIds = Wishlist::where('user_id', $auth['user']->id)->pluck('product_id')->toArray();
            }

            if (empty($productIds)) {
                $products = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, $page);
                return response()->json([
                    'status' => true,
                    'data' => [
                        'current_page' => 1,
                        'data' => [],
                        'last_page' => 1,
                        'total' => 0,
                        'from' => null,
                        'per_page' => (int) $perPage,
                        'to' => null,
                    ]
                ]);
            }

            if ($isGuest) {
                $products = Product::with('webcategories', 'brand')
                    ->leftJoin('guest_wishlists', function ($join) use ($auth) {
                        $join->on('products.id', '=', 'guest_wishlists.product_id')
                            ->where('guest_wishlists.guest_session_id', '=', $auth['guest']->id);
                    })
                    ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->whereIn('products.id', $productIds)
                    ->selectRaw('products.*, MIN(variations.sell_price_inc_tax) as ad_price, MAX(guest_wishlists.id) as wishlist_id')
                    ->groupBy('products.id');
            } else {
                $contact = $auth['user'];
                $priceTier = $contact->price_tier;
                $priceGroupId = key($priceTier);
                $products = Product::with('webcategories', 'brand')
                    ->leftJoin('wishlists', function ($join) use ($auth) {
                        $join->on('products.id', '=', 'wishlists.product_id')
                            ->where('wishlists.user_id', '=', $auth['user']->id);
                    })
                    ->leftJoin('variations', 'products.id', '=', 'variations.product_id')
                    ->leftJoin('variation_group_prices', function ($join) use ($priceGroupId) {
                        $join->on('variations.id', '=', 'variation_group_prices.variation_id')
                            ->where('variation_group_prices.price_group_id', '=', $priceGroupId);
                    })
                    ->where('enable_selling', 1)
                    ->where('is_inactive', 0)
                    ->whereIn('products.id', $productIds)
                    ->selectRaw('products.*, (variation_group_prices.price_inc_tax) as ad_price, wishlists.id as wishlist_id')
                    ->groupBy('products.id');
            }

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
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function wishlist(){
       

        $business_id = request()->session()->get('user.business_id');
        if (request()-> ajax()){
            $data = Wishlist::with('customer','product')->get();
            return DataTables::of($data)
            ->addColumn('name',function($row){
                return $row->customer->name??$row->customer->supplier_business_name ?? '';
            })
            ->addColumn('email',function($row){
                return $row->customer->email??'';
            })
            
            ->addColumn('product_name',function($row){
                return $row->product->name;
            })
            
            ->addColumn('product_image',function($row){
                return $row->product->image_url;
            })
            ->removeColumn(['customer','product'])
            // ->rowColumn([])
            ->make(true);
        }
        return view('contact_us.wishlist');
        

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return response()->json('unkown route');
    }
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


     public function store(Request $request)
     {
        $auth = $this->authCheck($request);
        if (!$auth['status']) {
            return response()->json(['status' => false, 'message' => $auth['message']], 401);
        }

        $businessId = $request->input('business_id');
        if ($businessId === null || $businessId === '') {
            $location = $request->attributes->get('current_location');
            if ($location) {
                $businessId = $location->business_id ?? null;
                $request->merge(['business_id' => $businessId]);
            }
        }

        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'business_id' => 'required|integer|exists:business,id',
        ]);
    
        try {
            $isGuest = !empty($auth['is_guest']);

            if ($isGuest) {
                $guestSession = $auth['guest'];
                $wishlistItem = GuestWishlist::where('guest_session_id', $guestSession->id)
                    ->where('product_id', $request->product_id)
                    ->where('business_id', $request->business_id)
                    ->first();

                if ($wishlistItem) {
                    $wishlistItem->delete();
                    $message = 'Item removed from wishlist';
                } else {
                    GuestWishlist::create([
                        'guest_session_id' => $guestSession->id,
                        'product_id' => $request->product_id,
                        'business_id' => $request->business_id,
                    ]);
                    $message = 'Item added to wishlist';
                }
            } else {
                $userId = $auth['user']->id;
                $wishlistItem = Wishlist::where('user_id', $userId)
                    ->where('product_id', $request->product_id)
                    ->where('business_id', $request->business_id)
                    ->first();

                if ($wishlistItem) {
                    $wishlistItem->delete();
                    $message = 'Item removed from wishlist';
                } else {
                    Wishlist::create([
                        'user_id' => $userId,
                        'product_id' => $request->product_id,
                        'business_id' => $request->business_id,
                    ]);
                    $message = 'Item added to wishlist';
                }
            }
    
            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Wishlist update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Wishlist  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function show()
    {

        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Wishlist  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function edit(Wishlist $wishlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Wishlist  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wishlist $wishlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Wishlist  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id = null)
    {
        $auth = $this->authCheck($request);
        if (!$auth['status']) {
            return response()->json(['status' => false, 'message' => $auth['message']], 401);
        }

        try {
            $isGuest = !empty($auth['is_guest']);

            if ($isGuest) {
                $deleted = GuestWishlist::where('guest_session_id', $auth['guest']->id)->delete();
            } else {
                $deleted = Wishlist::where('user_id', $auth['user']->id)->delete();
            }

            if ($deleted) {
                return response()->json([
                    'status' => true,
                    'message' => 'Wishlist cleared successfully',
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Wishlist was already empty',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete wishlist item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    


}