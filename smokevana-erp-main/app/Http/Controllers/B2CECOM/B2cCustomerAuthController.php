<?php

namespace App\Http\Controllers\B2CECOM;

use App\Business;
use App\Cart; 
use App\Http\Controllers\Controller;
use App\Models\ElevenLabsSessionModel;
use Illuminate\Http\Request;
use App\Contact;
use App\ContactUs;
use App\CustomerAddress;
use App\Transaction;
use App\Jobs\SendNotificationJob;
use App\NewsLetterSubscriber;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Utils\ContactUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\CustomDiscount;
use App\Models\EcomReferalProgram;
use App\GuestSession;
use App\GuestCartItem;
use App\CartItem;
use App\Product;
use MikeMcLin\WpPassword\Facades\WpPassword;

class B2cCustomerAuthController extends Controller
{
    protected $contactUtil;

    public function __construct(ContactUtil $contactUtil)
    {
        $this->contactUtil = $contactUtil;
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
    public function register(Request $request)
    {
        $input = $request->all();
        $locationId = $request->route('location_id');
        $brandName = $request->route('brand_name');
        $brand = $request->get('current_brand');
        if (!$locationId || !$brandName) {
            return response()->json([
                'status' => false,
                'message' => 'Location and brand are required',
            ], 400);
        }
         // Get brand_id for validation
         $brandId = null;
         if ($request->has('current_brand')) {
             $brand = $request->get('current_brand');
             if ($brand) {
                 $brandId = $brand->id;
             }
         }
         
         // Build email validation rule
         $emailRules = ['nullable', 'email', 'max:100'];
         if ($request->filled('email') && $locationId) {
             $uniqueRule = Rule::unique('contacts', 'email')
                 ->where('location_id', $locationId)
                 ->whereNull('deleted_at');
             
             // Add brand_id condition if available
             if ($brandId) {
                 $uniqueRule->where('brand_id', $brandId);
             }
             
             $emailRules[] = $uniqueRule;
         }
         
        $validate = Validator::make($request->all(), [
            'prefix' => 'nullable|string|max:10',
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            //'email' => ['nullable','email','max:100', Rule::unique('contacts', 'email')->where('location_id', $request->route('location_id'))->where('brand_id', $request->route('brand_id'))->whereNull('deleted_at')],
            'mobile' => 'nullable|string|min:10|max:10',
            'email' => $emailRules,
            'password' => 'required|confirmed|string|min:8',
            'referal_code' => 'nullable|string|max:50',
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
            ], 422);
        }
        try {
            DB::beginTransaction();
            $input = $request->only('first_name', 'middle_name', 'last_name', 'prefix', 'mobile', 'email' ,'password');
            $input['password'] = Hash::make($request->input('password'));
            // Get business_id from location
            $location = $request->get('current_location');
            $businessId = $location ? $location->business_id : 1;

            $input['business_id'] = $businessId;
            $input['created_by'] = 1; // Fixed created_by
            
            // Handle customer_group_id for B2C customers
            // Only set if explicitly provided in request, otherwise leave as null for B2C
            if ($request->has('customer_group_id') && !empty($request->input('customer_group_id'))) {
                $input['customer_group_id'] = $request->input('customer_group_id');
            } else {
                // For B2C customers, customer_group_id can be null
                $input['customer_group_id'] = null;
            }
            
            $input['type'] = 'customer';
            $input['country'] = 'US';
            $input['isApproved'] = null;

            // Set location_id for B2C registration
            if ($locationId) {
                $input['location_id'] = $locationId;
            }
            // Set brand_id for brand-specific registration
            $brandName = $request->route('brand_name');
            if ($brandName && $request->has('current_brand')) {
                $brand = $request->get('current_brand');
                if ($brand) {
                    $input['brand_id'] = $brand->id;
                }
            }
            $input['name'] = trim(implode(' ', array_filter([
                $request->input('prefix'),
                $request->input('first_name'),
                $request->input('middle_name'),
                $request->input('last_name')
            ])));
            $token =Str::random(40);
            $input['remember_token'] = $token;
            if($request->input('referal_code')){
                $input['sender_refralcode'] = $request->input('referal_code');
            }


            // Prepare and store unique referral code for customer
            $brandPrefix = '';
            if ($brand && !empty($brand->name)) {
                $normalized = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($brand->name));
                $brandPrefix = substr($normalized, 0, 2);
            }
            if (strlen($brandPrefix) < 2) {
                $brandPrefix = 'RF';
            }
            do {
                $referalCode = $brandPrefix . strtoupper(Str::random(8));
            } while (Contact::where('referal_code', $referalCode)->exists());

            $input['referal_code'] = $referalCode;

            // Call the ContactUtil function
            $contactResponse = $this->contactUtil->createNewContact($input);

            
            if (!$contactResponse['success']) {
                throw new \Exception("Failed to create contact");
            }

            $customer = $contactResponse['data'];


            $business = Business::find($businessId);
            DB::commit();

            $contact = (object)[
                'email' => $request->email,
                'mobile' => $request->mobile,
                'remember_token' => $token,
                'brand_id' => $brand->id,
            ];
            $user = (object)[
                'name' => $customer->first_name . $customer->last_name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'remember_token' => $token,
                'brand_id' => $brand->id,
                'contact_id' => $customer->id,
                'ref_no' => $customer->contact_id,
                'is_b2c' => true,
            ];
            SendNotificationJob::dispatch(true, 1, 'registration_email_validation', $user, $contact);

            return response()->json(['status' => true, 'message' => 'Email Confirmation Link Sent to your email'], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $th->getMessage() . ' at ' . $th->getLine(),], 500);
        }
    }
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $locationId = $request->route('location_id');
            $brand = $request->get('current_brand');
            
            // Build query with location and brand constraints if provided
            $query = Contact::where(function($query) use ($credentials) {
                $query->where('type', 'customer');
            })
            ->where(function($query) use ($credentials) {
                $query->where('email', $credentials['email'])
                      ->orWhere('customer_u_name', $credentials['email']) 
                      ->orWhere('contact_id', $credentials['email']);
            })->where('brand_id', $brand->id);
            
            $contact = $query->first();
            // customer password is hashed with wp_password or md5 hash 
            if ($contact && (Hash::check($credentials['password'], $contact->password) || WpPassword::check($credentials['password'], $contact->password))) {
                if ($contact->isApproved == false) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Not Approved!',
                    ]);
                }
                if($contact->contact_status =='inactive'){
                    return response()->json([
                        'status' => false,
                        'message' => 'This account is deactivated',
                    ]);
                }
                
                // Create access token and refresh token
                $tokenResult = $contact->createToken('Customer Access Token');
                $accessToken = $tokenResult->accessToken;
                
                // Generate refresh token manually
                $refreshToken = Str::random(60);
                
                // Store refresh token in database
                DB::table('oauth_refresh_tokens')->insert([
                    'id' => $refreshToken,
                    'access_token_id' => $tokenResult->token->id,
                    'revoked' => false,
                    'expires_at' => now()->addDays(3),
                ]);
                
                $profileName = '';
                if ($contact->name) {
                    $profileName = $contact->name;
                } else if ($contact->supplier_business_name) {
                    $profileName = $contact->supplier_business_name;
                }
                $group_name = '';
                if($contact->customer_group_id != null){
                    $customer_group = CustomerGroup::find($contact->customer_group_id);
                    $group_name = $customer_group->name;
                }

                // store token in database for ai agents action 
                if($request->query('elevenlabs_conversation_id')){
                    ElevenLabsSessionModel::updateOrCreate([
                        'user_id' => $contact->id,
                    ], [
                        'conversation_id' => $request->query('elevenlabs_conversation_id'),
                        'token' => $accessToken,
                    ]);
                }

                // Migrate guest cart to customer cart if guest_session parameter exists
                $guestSessionUuid = $request->query('guest_session');
                if ($guestSessionUuid) {
                    $this->migrateGuestCartToCustomer($guestSessionUuid, $contact->id, $locationId, $brand->id);
                }

                $data = [
                    'id' => $contact->id,
                    'profileName' => $profileName,
                    'type' => $contact->type,
                    'email' => $contact->email,
                    'role' => $contact->role,
                    'priceType' => $contact->price_tier??'',
                    'group_name' => $group_name??'',
                    'contact_id'=>$contact->contact_id??'',
                    'contact_type'=>$contact->contact_type??'',
                    'is_approved'=>$contact->isApproved??'',
                    'is_active'=>$contact->isActive??'',
                    'balance'=>$contact->balance??'',
                ];

                // Create brand-specific refresh token cookie name
                $brandName = $request->route('brand_name');
                $refreshTokenCookieName = $brandName ? "refresh_token_{$brandName}" : 'refresh_token';
                
                return response()->json(['status' => true, 'access_token' => $accessToken, 'data' => $data])
                ->withCookie(cookie(
                    $refreshTokenCookieName,
                    $refreshToken,
                    4320, // 3 days in minutes
                    '/',
                    null, //'aderp.phantasm.digital', // backend is adfe.phantasm.digital , frontend is aderp.phantasm.digital 
                    false, // Secure (true for HTTPS)
                    false, // HttpOnly
                    false, // Raw
                    'None', //'None' // SameSite (None for cross-origin requests)
                    ));
            }
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'login function failed', 'error' => $th->getMessage()]);
        }
    }

    /**
     * Migrate guest cart items to customer cart
     * 
     * @param string $guestSessionUuid
     * @param int $customerId
     * @param int $locationId
     * @param int $brandId
     * @return void
     */
    private function migrateGuestCartToCustomer($guestSessionUuid, $customerId, $locationId, $brandId)
    {
        try {
            // Find the guest session
            $guestSession = GuestSession::where('uuid', $guestSessionUuid)
                ->where('location_id', $locationId)
                ->where('brand_id', $brandId)
                ->first();

            if (!$guestSession) {
                return; // Guest session not found, silently skip
            }

            // Get all guest cart items
            $guestCartItems = GuestCartItem::where('guest_session_id', $guestSession->id)->get();

            if ($guestCartItems->isEmpty()) {
                // No items to migrate, delete guest session and return
                $guestSession->delete();
                return;
            }

            // Get customer cart to check if frozen
            $checkout = Cart::where('user_id', $customerId)->first();
            $isFreeze = $checkout ? $checkout->isFreeze : false;
            $currentTime = now();

            DB::beginTransaction();
            try {
                foreach ($guestCartItems as $guestItem) {
                    // Get variation details
                    $variation = DB::table('variations')
                        ->join('variation_location_details', 'variations.id', '=', 'variation_location_details.variation_id')
                        ->where('variations.id', $guestItem->variation_id)
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
                        continue; // Skip invalid variation
                    }

                    // Get product
                    $product = Product::where('id', $guestItem->product_id)
                        ->where('enable_selling', 1)
                        ->where('is_inactive', 0)
                        ->first();

                    if (!$product) {
                        continue; // Skip invalid product
                    }
                    $maxSaleLimit = $variation->var_maxSaleLimit ?? $product->maxSaleLimit ?? false;

                    // Check if item already exists in customer cart
                    $cartItem = CartItem::where([
                        'user_id' => $customerId,
                        'product_id' => $guestItem->product_id,
                        'variation_id' => $guestItem->variation_id,
                    ])
                        ->lockForUpdate()
                        ->first();

                    $existingQty = $cartItem ? $cartItem->qty : 0;
                    $newQty = $existingQty + $guestItem->qty;

                    if ($maxSaleLimit && $newQty > $maxSaleLimit) {
                        $newQty = $maxSaleLimit;
                    }

                    if ($product->enable_stock == 1) {
                        if ($variation->qty + $existingQty < $newQty && $isFreeze) {
                            continue;
                        }

                        if ($newQty > $variation->qty && !$isFreeze) {
                            $newQty = $variation->qty;
                        }
                    }

                    $qtyDiff = $newQty - $existingQty;
                    if ($cartItem) {
                        $cartItem->qty = $newQty;
                        $cartItem->save();
                    } else {
                        CartItem::create([
                            'user_id' => $customerId,
                            'product_id' => $guestItem->product_id,
                            'variation_id' => $guestItem->variation_id ?? null,
                            'qty' => $newQty,
                            'item_type' => $guestItem->item_type ?? 'line_item',
                            'discount_id' => $guestItem->discount_id ?? null,
                            'lable' => $guestItem->lable ?? 'Item',
                        ]);
                    }
                    if ($isFreeze && $qtyDiff > 0 && $product->enable_stock == 1) {
                        try {
                            DB::table('variation_location_details')
                                ->where('variation_id', $variation->id)
                                ->decrement('in_stock_qty', $qtyDiff);
                        } catch (\Throwable $th) {
                            DB::table('variation_location_details')
                                ->where('variation_id', $variation->id)
                                ->update(['in_stock_qty' => 0]);
                        }
                    }
                }
                GuestCartItem::where('guest_session_id', $guestSession->id)->delete();
                $guestSession->delete();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Failed to migrate guest cart: ' . $e->getMessage());
            }
        } catch (\Throwable $th) {
            \Log::error('Failed to migrate guest cart: ' . $th->getMessage());
        }
    }

    public function viewCustomer(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'No API token provided.']);
        }
    
        $locationId = $request->route('location_id');
        $brandName  = $request->route('brand_name');
        $brandId = $request->get('current_brand')->id;
    
        if (!$locationId || !$brandName) {
            return response()->json([
                'status'  => false,
                'message' => 'Location, Brand name are required',
            ], 400);
        }
    
        $contact = $authData['user'];
    
        if ($contact->location_id != $locationId) {
            return response()->json([
                'status'  => false,
                'message' => 'Access denied. You do not have permission to access this location.',
            ], 403);
        }
        if($contact->brand_id != $brandId){
            return response()->json([
                'status'  => false,
                'message' => 'Access denied. You do not have permission to access this brand.',
            ], 403);
        }
    
        $contactData = $contact->where('id', $contact->id)->select('id', 'contact_id', 'balance', 'name','prefix', 'first_name', 'middle_name', 'last_name', 'email', 'mobile')->first();

                $business = Business::find($contact->business_id);
        if ($business->enable_referal_program) {
            $contactData['referal_code'] = $contact->referal_code;

            $contactData['beneficiaryCustomersByMe'] = EcomReferalProgram::with([
                'beneficiaryCustomer:id,first_name,last_name,email,mobile',
                'discount:id,couponName,couponCode,discount,discountValue,discountType,applyDate,endDate'
            ])
                ->where('referred_by_customer_id', $contact->id)
                ->select('id', 'coupon_code', 'customer_id', 'referred_by_customer_id', 'discount_id', 'is_used', 'used_at', 'created_at')
                ->get();

            $contactData['meReferredByCustomers'] = EcomReferalProgram::with([
                'beneficiaryCustomer:id,first_name,last_name,email,mobile',
                'referredByCustomer:id,first_name,last_name,email,mobile',
                'discount:id,couponName,couponCode,discount,discountValue,discountType,applyDate,endDate'
            ])
                ->where('customer_id', $contact->id)
                ->select('id', 'coupon_code', 'customer_id', 'referred_by_customer_id', 'discount_id', 'is_used', 'used_at', 'created_at')
                ->get();
        }
    
        $totalOrders = Transaction::where('contact_id', $contact->id)
            ->where('type', 'sales_order')
            ->count();
    
        $contactData->total_Orders = $totalOrders;
    
        return response()->json([
            'status'   => true,
            'message'  => 'Customer data fetched successfully',
            'customer' => $contactData,
        ], 200);
    }

    public function updateCustomer(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'No API token provided.']);
        }
        $locationId = $request->route('location_id');
        $brandName  = $request->route('brand_name');
        $brandId = $request->get('current_brand')->id;
        
        if (!$locationId || !$brandName) {
            return response()->json([
                'status'  => false,
                'message' => 'Location, Brand name are required',
            ], 400);
        }
    
        $contact = $authData['user'];
        if(($contact->isApproved == null) || ($contact->isApproved == false) || ($contact->isApproved == 0)){
            return response()->json([
                'status'  => false,
                'message' => 'Customer is not approved',
            ], 403);
        }
    
        if ($contact->location_id != $locationId) {
            return response()->json([
                'status'  => false,
                'message' => 'Access denied. You do not have permission to access this location.',
            ], 403);
        }
        if($contact->brand_id != $brandId){
            return response()->json([
                'status'  => false,
                'message' => 'Access denied. You do not have permission to access this brand.',
            ], 403);
        }
        // dd($contact);

        $validate = Validator::make($request->all(), [
            'prefix' => 'nullable|string|max:10',
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'mobile' => 'required|string|min:10|max:10',
            'email' => 'required|email|max:100',
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
        $is_email_change = false;
        if($request->email != $contact->email){
            $token =Str::random(40);
            $is_email_change = true;
        }
        try {
            DB::beginTransaction();
            $input = $request->only('first_name', 'middle_name', 'last_name', 'prefix', 'mobile', 'email');
            $input['name'] = trim(implode(' ', array_filter([
                $request->input('prefix'),
                $request->input('first_name'),
                $request->input('middle_name'),
                $request->input('last_name')
            ])));
            if($is_email_change){
                $input['remember_token'] = $token;
                $input['isApproved'] = null;
            }
            $businessId = $request->get('current_location')->business_id;
            // dd($input);
            $contactResponse = $this->contactUtil->updateContact($input, $contact->id,  $businessId);

            if (!$contactResponse['success']) {
                throw new \Exception("Failed to update contact");
            }

            $customer = $contactResponse['data'];
            $msg = 'Customer data updated successfully';
            DB::commit();
            if($is_email_change){
                $user = (object)[
                    'name' => $customer->first_name . $customer->last_name,
                    'mobile' => $request->mobile,
                    'email' => $request->email,
                    'remember_token' => $token,
                    'brand_id' => $brandId,
                    'contact_id' => $customer->id,
                    'ref_no' => $customer->contact_id,
                    'is_b2c' => true,
                ];
                SendNotificationJob::dispatch(true, 1, 'email_confirmation', $user, $customer);
                $msg = 'You updated your email address. Please verify your email address to continue.';
            }
            return response()->json(['status' => true, 'message' => $msg, 'customer' => $customer , 'is_email_change' => $is_email_change], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $th->getMessage() . ' at ' . $th->getLine(),], 500);
        }
    }
    public function emailConfirmation(Request $request)
    {
        $token = $request->input('token');
        $email = $request->input('email');
        $brandId = $request->get('current_brand')->id;
        $contact = Contact::where('email', $email)->where('remember_token', $token)->first();
        if (!$contact) {
            return response()->json([
                'status' => false,
                'message' => 'Contact not found',
            ], 404);
        }
        if($contact->brand_id != $brandId){
            return response()->json([
                'status'  => false,
                'message' => 'Access denied. You do not have permission to access this brand.',
            ], 403);
        }
        if($contact->sender_refralcode){
            $this->handleReferalProgramRewards(
                $business,
                $contact,
                $brand,
                $contact->sender_refralcode
            );
            $contact->sender_refralcode = null;
        }
        $contact->isApproved = true;
        $contact->remember_token = null;
        $contact->save();

        try {
        $tokenResult = $contact->createToken('Customer Access Token');
        $accessToken = $tokenResult->accessToken;
        
        $refreshToken = Str::random(60);
        
        DB::table('oauth_refresh_tokens')->insert([
            'id' => $refreshToken,
            'access_token_id' => $tokenResult->token->id,
            'revoked' => false,
            'expires_at' => now()->addDays(3),
        ]);

        if($request->query('elevenlabs_conversation_id')){
            ElevenLabsSessionModel::updateOrCreate([
                'user_id' => $contact->id,
            ], [
                'conversation_id' => $request->query('elevenlabs_conversation_id'),
                'token' => $accessToken,
            ]);
        }
        $data = [
            'id' => $contact->id,
            'profileName' => $contact->name,
            'type' => $contact->type,
            'email' => $contact->email,
            'role' => $contact->role,
            'priceType' => $contact->price_tier??'',
            'group_name' => $contact->group_name??'',
            'contact_id'=>$contact->contact_id??'',
            'contact_type'=>$contact->contact_type??'',
            'is_approved'=>$contact->isApproved??'',
            'is_active'=>$contact->isActive??'',
            'balance'=>$contact->balance??'',
        ];

         $brandName = $request->route('brand_name');
         $refreshTokenCookieName = $brandName ? "refresh_token_{$brandName}" : 'refresh_token';
         
         return response()->json(['status' => true, 'access_token' => $accessToken, 'data' => $data])
         ->withCookie(cookie(
             $refreshTokenCookieName,
             $refreshToken,
             4320,
             '/',
             null,
             false,
             false,
             false,
             'None',
             ));    
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage() . ' at ' . $th->getLine(),], 500);
        }
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validate = Validator::make($request->all(), ['email' => 'required|email']);
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
        $locationId = $request->route('location_id');
        
        // Build query with location and brand constraints if provided
        $query = Contact::where('email', $request->input('email'));
        if ($locationId) {
            $query->where('location_id', $locationId);
        }
        
        // Add brand constraint for brand-specific routes
        $brandName = $request->route('brand_name');
        if ($brandName && $request->has('current_brand')) {
            $brand = $request->get('current_brand');
            if ($brand) {
                $query->where('brand_id', $brand->id);
            }
        }
        
        $user = $query->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'We cannot find a user with that email address.']);
        }
        $token = Str::random(60);
        $user->remember_token = $token;
        $user->update();
        // Mail::send('emails.forgot-password', ['token' => $token, 'email' => $user->email,'Username'=> $user->name], function ($message) use ($user) {
        //     $message->to($user->email);
        //     $message->subject('Password Reset Link');
        // });
        // dd($user);
        $contact=(object)[
            'id' => $user->id,
            'email'=>$user->email,
            'mobile'=>$user->mobile,
            'is_b2c' =>true,
            'brand_id' => $user->brand_id,
        ];
        // $whatsapp_link = $this->notificationUtil->autoSendNotificationCustom(1, 'forget_password', $user, $contact);
        SendNotificationJob::dispatch(true, 1 , 'forget_password', $user, $contact);

        return response()->json(['status' => true, 'message' => 'We have emailed your password reset link!']);
    }

     /**

     * Summary of contactus

     * @param \Illuminate\Http\Request $request

     * @param mixed $slug

     * @return \Illuminate\Http\JsonResponse

     */

     public function contactus(Request $request)

     {
 
         //return response()->json($request->all());
 
         $validate = Validator::make($request->all(), [
             'full_name' => 'required|string|max:255',
             'email' => 'required|email|max:255',
             'subject' => 'required|string|max:255',
             'message' => 'required|string|max:5000',
             'phone' => 'required|string|min:10|max:10',
             'meta' => ['nullable', function ($attribute, $value, $fail) use ($request) {
                 if (!is_string($value) || empty($value)) {  //checks not a string or empty string
                     return;
                 }
                 $decoded = json_decode($value, true);
                 if (json_last_error() != JSON_ERROR_NONE) {
                     $fail("The {$attribute} field must be a valid JSON");
                     return;
                 }
                 $blacklist = ['<script', '</script', 'onerror', 'onload', 'javascript:', 'vbscript:', 'expression(', 'eval('];
                 $foundMalicious = false; //Start by assuming input is safe flip true if somthing bad found 
                 //Loops through all values in $decoded, even if deeply nested
                 array_walk_recursive($decoded, function (&$item) use ($blacklist, &$foundMalicious) {
                     if (!is_string($item)) return;
                     $decodedItem = html_entity_decode($item, ENT_QUOTES, 'UTF-8'); 
                     foreach ($blacklist as $badWord) { 
                         if (stripos($decodedItem, $badWord) !== false) {
                              $foundMalicious = true; 
                         } 
                     } 
                     $item = htmlspecialchars(strip_tags($decodedItem), ENT_QUOTES, 'UTF-8'); 
                 });
                  if ($foundMalicious) { 
                     $fail("The {$attribute} field contains invalid or unsafe content."); 
                     return; 
                 } 
                 $sanitizedMeta = json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); 
                 $request->merge([$attribute => $sanitizedMeta]); 
             }] 
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
 
         $key = 'contactus_' . $request->ip(); 
         if (RateLimiter::tooManyAttempts($key, 1)) {
              return response()->json([
                  'status' => false,
                  'message' => 'you have reached the request limit. Please try again after a minute.'
              ],);
          } 
         //dd($key);
          //for generate unique ref num 
         //Makes a readable reference like: CU650F4BC9AB12 
         $reference_no = 'CU' . strtoupper(uniqid());
          // Use location and brand from URL/route (which website the form was submitted from)
         $location = $request->get('current_location');
         $brand = $request->get('current_brand');
         $location_id = $request->route('location_id') ?? ($location ? $location->id : null) ?? $request->input('location_id');
         $brand_id = $request->get('brand_id') ?? ($brand ? $brand->id : null) ?? $request->input('brand_id');
         $contact = ContactUs::create([
             'brand_id' => $brand_id,
             'reference_no' => $reference_no,
             'location_id' => $location_id,
             'fname' =>  $request->full_name, 
             'email' => $request->email, 
             'phone' => $request->phone, 
             'subject' => $request->subject, 
             'message' => $request->message, 
             'meta' => json_encode([]), 
             'status' => 'Pending', 
             'staff_id' => null,
          ]);
          //dd($request->brand_id); 
          //return response()->json($contact);
          $contact = (object)[
              'email' => $request->email,
              'mobile' => $request->phone,
              'brand_id' => $request->brand_id
          ];
 
         $user = (object)[ 
            'name' => $request->full_name,
            'ref_no' => $reference_no,
            'mobile' => $request->phone, 
            'email' => $request->email,
            'brand_id' => $request->brand_id,
            'is_b2c' => true, 
         ]; 
         SendNotificationJob::dispatch(true, 1, 'contact_us_success', $user, $contact); 
         RateLimiter::hit($key, 60);
          return response()->json([ 
             'status' => true,
             'message' => 'Your message has been sent successfully. We’ll get back to you shortly.'
            ]);
      }
 
     /**
       * Summary of subscribe
       * @param \Illuminate\Http\Request $request
       * @param mixed $slug
       * @return \Illuminate\Http\JsonResponse 
      */
      public function subscribe(Request $request)
     {
         $validate = Validator::make($request->all(), [
             'email' => 'required|email|max:255',
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
         $key = 'subscribe_' . $request->ip();
         if (RateLimiter::tooManyAttempts($key, 1)) {
             return response()->json([
                 'status' => false,
                 'message' => 'You have reached the request limit. Please try again after a minute.'
             ],);
         }
         // check if email is already subscribed
         $subscriber = NewsLetterSubscriber::where('email', $request->input('email'))->where('location_id', $request->get('current_location')->id)->where('brand_id', $request->get('current_brand')->id)->first();
         if ($subscriber) {
             return response()->json([
                 'status' => false,
                 'message' => 'You are already subscribed to our newsletter.'
             ]);
         }
         NewsLetterSubscriber::create([
             'email' => $request->input('email'),
             'location_id' => $request->location_id,
             'brand_id' => $request->brand_id
         ]);
         $contact = (object)[
             'email' => $request->input('email'),
             'mobile' => ""
         ];
         $user = (object)[
            'email' => $request->input('email'),
            'mobile' => "",
            'brand_id' => $request->brand_id,
            'is_b2c' => true,
        ];
         SendNotificationJob::dispatch(true, 1, 'subscribe_newsletter', $user, $contact);
         RateLimiter::hit($key, 60);
         return response()->json([
             'status' => true,
             'message' => 'Thanks for subscribing to our newsletter'
         ]);
     }
     public function updateAddress(Request $request)
     {
         try {
             $user =  Auth::guard('api')->user();
 
             if (!$user) {
                 return response()->json([
                     'status' => false,
                     'message' => 'No authenticated user found',
                 ], 401);
             }
 
             // Validate address details
             $validate = Validator::make($request->all(), [
                 'type' => 'required|in:billing,shipping,both',
                 // Added validation for type
                 'first_name' => 'required|string|max:255',
                 'last_name' => 'nullable|string|max:255',
                 'address_line_1' => 'required|string|max:255',
                 'address_line_2' => 'nullable|string|max:255',
                 'city' => 'required|string|max:100',
                 'state' => 'required|string|max:2',
                 'zip_code' => 'required|string|max:10',
                 'country' => 'required|string|max:100',
                 'company' => 'nullable|string|max:100',
 
                 'shipping_custom_field_details' => 'nullable|string|max:255',
                 // Shipping fields
 
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
 
             $contact = Contact::find($user->id);
             $cart = Cart::where('user_id', $user->id)->first();
 
 
             if (!$contact) {
                 return response()->json([
                     'status' => false,
                     'message' => 'Contact not found',
                 ], 404);
             }
             $updateData = [];
 
             if ($request->type === 'billing') {
                 $contact->address_line_1 = $request->input('address_line_1');
                 $contact->address_line_2 = $request->input('address_line_2');
                 $contact->city = $request->input('city');
                 $contact->state = $request->input('state');
                 $contact->zip_code = $request->input('zip_code');
                 $contact->country = $request->input('country');
 
                 $contact->first_name = $request->input('first_name');
                 $contact->last_name = $request->input('last_name');
                 $contact->name = $request->input('first_name') . " " . $request->input('last_name');
                 $contact->supplier_business_name = $request->input('company');
                 if ($cart) {
                     $cart->billing_first_name = $request->input('first_name');
                     $cart->billing_last_name = $request->input('last_name');
                     $cart->billing_company = $request->input('company');
                     $cart->billing_address1 = $request->input('address_line_1');
                     $cart->billing_address2 = $request->input('address_line_2');
                     $cart->billing_city = $request->input('city');
                     $cart->billing_state = $request->input('state');
                     $cart->billing_zip = $request->input('zip_code');
                     $cart->billing_country = $request->input('country');
                     $cart->save();
                 } //update shipping address
             } else if ($request->input('type') === 'shipping') {
                 $contact->shipping_address1 = $request->input('address_line_1');
                 $contact->shipping_address2 = $request->input('address_line_2');
                 $contact->shipping_city = $request->input('city');
                 $contact->shipping_state = $request->input('state');
                 $contact->shipping_zip = $request->input('zip_code');
                 $contact->shipping_country = $request->input('country');
                 $contact->shipping_first_name = $request->input('first_name');
                 $contact->shipping_last_name = $request->input('last_name');
                 $contact->shipping_company = $request->input('company');
 
                 $contact->shipping_address = $request->input('address_line_1') . ' ' .
                     ($request->input('address_line_1') ? $request->input('address_line_2') . ' ' : '') .
                     $request->input('address_line_2') . ' ' .
                     $request->input('city') . ' ' .
                     $request->input('state') . ' ' .
                     $request->input('zip_code') . ' ' .
                     $request->input('country');
 
                 if ($cart) {
                     $cart->shipping_first_name = $request->input('first_name');
                     $cart->shipping_last_name = $request->input('last_name');
                     $cart->shipping_company = $request->input('company');
                     $cart->shipping_address1 = $request->input('address_line_1');
                     $cart->shipping_address2 = $request->input('address_line_2');
                     $cart->shipping_city = $request->input('city');
                     $cart->shipping_state = $request->input('state');
                     $cart->shipping_zip = $request->input('zip_code');
                     $cart->shipping_country = $request->input('country');
                     $cart->save();
                 }
             }else {
                $contact->address_line_1 = $request->input('address_line_1');
                $contact->address_line_2 = $request->input('address_line_2');
                $contact->city = $request->input('city');
                $contact->state = $request->input('state');
                $contact->zip_code = $request->input('zip_code');
                $contact->country = $request->input('country');
                $contact->first_name = $request->input('first_name');
                $contact->last_name = $request->input('last_name');
                $contact->name=$request->input('first_name')." " .$request->input('last_name');
                $contact->supplier_business_name = $request->input('company');
                $contact->shipping_address = $request->input('address_line_1') . ' ' .
                    ($request->input('address_line_1') ? $request->input('address_line_2') . ' ' : '') .
                    $request->input('address_line_2') . ' ' .
                    $request->input('state') . ' ' .
                    $request->input('zip_code') . ' ' .
                    $request->input('country');
                $contact->shipping_address1 = $request->input('address_line_1');
                $contact->shipping_address2 = $request->input('address_line_2');
                $contact->shipping_city = $request->input('city');
                $contact->shipping_state = $request->input('state');
                $contact->shipping_zip = $request->input('zip_code');
                $contact->shipping_country = $request->input('country');
                $contact->shipping_first_name = $request->input('first_name');
                $contact->shipping_last_name = $request->input('last_name');
                $contact->shipping_company = $request->input('company');

                if ($cart) {
                    $cart->billing_first_name = $request->input('first_name');
                    $cart->billing_last_name = $request->input('last_name');
                    $cart->billing_company = $request->input('company');
                    $cart->billing_address1 = $request->input('address_line_1');
                    $cart->billing_address2 = $request->input('address_line_2');
                    $cart->billing_city = $request->input('city');
                    $cart->billing_state = $request->input('state');
                    $cart->billing_zip = $request->input('zip_code');
                    $cart->billing_country = $request->input('country');

                    $cart->shipping_first_name = $request->input('first_name');
                    $cart->shipping_last_name = $request->input('last_name');
                    $cart->shipping_company = $request->input('company');
                    $cart->shipping_address1 = $request->input('address_line_1');
                    $cart->shipping_address2 = $request->input('address_line_2');
                    $cart->shipping_city = $request->input('city');
                    $cart->shipping_state = $request->input('state');
                    $cart->shipping_zip = $request->input('zip_code');
                    $cart->shipping_country = $request->input('country');

                    $cart->save();
                }
            }
             $contact->update($updateData);
 
             return response()->json([
                 'status' => true,
                 'message' => 'Address updated successfully',
                 'data' => $contact,
             ], 200);
         } catch (\Throwable $th) {
             return response()->json([
                 'status' => false,
                 'message' => 'Failed to update address',
                 'error' => $th->getMessage(),
             ], 500);
         }
     }

     public function savedAddresses(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'No API token provided.']);
        }
        $user = $authData['user'];
        $addresses = CustomerAddress::where('contact_id', $user->id)->get();
        $defaultAddressShipping =[
           'first_name' => $user->shipping_first_name,
            'last_name' => $user->shipping_last_name,
            'company' => $user->shipping_company,
            'address_line_1' => $user->shipping_address1,
            'address_line_2' => $user->shipping_address2,
            'city' => $user->city,
            'state' => $user->shipping_state,
            'zip_code' => $user->shipping_zip,
            'country' => $user->shipping_country,
        ];
        $defaultAddressBilling =[
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'company' => $user->company,
            'address_line_1' => $user->address_line_1,
            'address_line_2' => $user->address_line_2,
            'city' => $user->city,
            'state' => $user->state,
            'zip_code' => $user->zip_code,
            'country' => $user->country,
        ];
        $defaultAddress = [
            'shipping' => $defaultAddressShipping,
            'billing' => $defaultAddressBilling,
        ];
        return response()->json([
            'status' => true,
            'message' => 'Saved addresses retrieved successfully',
            'data' => $addresses,
            'default_address' => $defaultAddress,
        ], 200);
    }

    public function createSavedAddress(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'No API token provided.']);
        }
        $user = $authData['user'];

        $validate = Validator::make($request->all(), [
            'address_label' => 'nullable|string|max:255',
            'address_type' => 'nullable|string|in:billing,shipping,both',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:2',
            'zip_code' => 'required|string|max:10',
            'country' => 'required|string|max:100',
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
            ], 422);
        }

        try {
            $addresses = [];
            
            if($request->address_type == 'both') {
                // Create both billing and shipping addresses
                $billingAddress = CustomerAddress::create([
                    'contact_id' => $user->id,
                    'address_label' => $request->address_label,
                    'address_type' => 'billing',
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'company' => $request->company,
                    'address_line_1' => $request->address_line_1,
                    'address_line_2' => $request->address_line_2,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip_code' => $request->zip_code,
                    'country' => $request->country,
                ]);
                
                $shippingAddress = CustomerAddress::create([
                    'contact_id' => $user->id,
                    'address_label' => $request->address_label,
                    'address_type' => 'shipping',
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'company' => $request->company,
                    'address_line_1' => $request->address_line_1,
                    'address_line_2' => $request->address_line_2,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip_code' => $request->zip_code,
                    'country' => $request->country,
                ]);
                
                $addresses = [$billingAddress, $shippingAddress];
                
                return response()->json([
                    'status' => true,
                    'message' => 'Addresses created successfully',
                    'data' => $addresses,
                ], 201);
            } else {
                // Create single address (billing or shipping)
                $address = CustomerAddress::create([
                    'contact_id' => $user->id,
                    'address_label' => $request->address_label,
                    'address_type' => $request->address_type,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'company' => $request->company,
                    'address_line_1' => $request->address_line_1,
                    'address_line_2' => $request->address_line_2,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip_code' => $request->zip_code,
                    'country' => $request->country,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Address created successfully',
                    'data' => $address,
                ], 201);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create address',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function getSavedAddress(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'No API token provided.']);
        }
        $user = $authData['user'];
        $id = $request->route('address_id');
        $address = CustomerAddress::find($id);
        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found',
            ], 404);
        }

        if ($address->contact_id != $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'status' => true,
            'message' => 'Address retrieved successfully',
            'data' => $address,
        ], 200);
    }

    public function updateSavedAddress(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'No API token provided.']);
        }
        $user = $authData['user'];

        $id = $request->route('address_id');
        $address = CustomerAddress::find($id);
        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found',
            ], 404);
        }

        if ($address->contact_id != $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validate = Validator::make($request->all(), [
            'address_label' => 'nullable|string|max:255',
            'address_type' => 'nullable|string|in:billing,shipping,both',
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line_1' => 'sometimes|required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'sometimes|required|string|max:100',
            'state' => 'sometimes|required|string|max:2',
            'zip_code' => 'sometimes|required|string|max:10',
            'country' => 'sometimes|required|string|max:100',
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
            ], 422);
        }

        try {
            $address->update($request->only([
                'address_label',
                'address_type',
                'first_name',
                'last_name',
                'company',
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'zip_code',
                'country'
            ]));

            return response()->json([
                'status' => true,
                'message' => 'Address updated successfully',
                'data' => $address->fresh(),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update address',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function deleteSavedAddress(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'No API token provided.']);
        }
        $user = $authData['user'];
        $id = $request->route('address_id');


        $address = CustomerAddress::find($id);
        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found',
            ], 404);
        }

        if ($address->contact_id != $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $address->delete();
            return response()->json([
                'status' => true,
                'message' => 'Address deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete address',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function setDefaultAddress(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json(['status' => false, 'message' => 'No API token provided.']);
        }
        $user = $authData['user'];
        $id = $request->route('address_id');


        $address = CustomerAddress::find($id);
        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found',
            ], 404);
        }

        // Verify the address belongs to the authenticated user
        if ($address->contact_id != $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $changeType = $request->change_type;
        if (!in_array($changeType, ['billing', 'shipping'])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid change type. Must be "billing" or "shipping"',
            ], 422);
        }

        try {
            $contact = Contact::find($user->id);
            if (!$contact) {
                return response()->json([
                    'status' => false,
                    'message' => 'Contact not found',
                ], 404);
            }

            DB::beginTransaction();

            if ($changeType == 'billing') {
                // First, save current billing address from Contact to CustomerAddress (if it exists)
                if ($contact->address_line_1) {
                    // Check if this address already exists in CustomerAddress to avoid duplicates
                    $existingAddress = CustomerAddress::where('contact_id', $user->id)
                        ->where('address_type', 'billing')
                        ->where('address_line_1', $contact->address_line_1)
                        ->where('city', $contact->city)
                        ->where('state', $contact->state)
                        ->where('zip_code', $contact->zip_code)
                        ->first();

                    if (!$existingAddress) {
                        CustomerAddress::create([
                            'contact_id' => $user->id,
                            'address_label' => 'Previous Billing Address',
                            'address_type' => 'billing',
                            'first_name' => $contact->first_name,
                            'last_name' => $contact->last_name,
                            'company' => $contact->supplier_business_name,
                            'address_line_1' => $contact->address_line_1,
                            'address_line_2' => $contact->address_line_2,
                            'city' => $contact->city,
                            'state' => $contact->state,
                            'zip_code' => $contact->zip_code,
                            'country' => $contact->country,
                        ]);
                    }
                }

                // Then, update billing address in Contact table from selected CustomerAddress
                $contact->name = $address->first_name . " " . $address->last_name;
                $contact->first_name = $address->first_name;
                $contact->last_name = $address->last_name;
                $contact->supplier_business_name = $address->company;
                $contact->address_line_1 = $address->address_line_1;
                $contact->address_line_2 = $address->address_line_2;
                $contact->city = $address->city;
                $contact->state = $address->state;
                $contact->zip_code = $address->zip_code;
                $contact->country = $address->country;

            } else if ($changeType == 'shipping') {
                // First, save current shipping address from Contact to CustomerAddress (if it exists)
                if ($contact->shipping_address1) {
                    // Check if this address already exists in CustomerAddress to avoid duplicates
                    $existingAddress = CustomerAddress::where('contact_id', $user->id)
                        ->where('address_type', 'shipping')
                        ->where('address_line_1', $contact->shipping_address1)
                        ->where('city', $contact->shipping_city)
                        ->where('state', $contact->shipping_state)
                        ->where('zip_code', $contact->shipping_zip)
                        ->first();

                    if (!$existingAddress) {
                        CustomerAddress::create([
                            'contact_id' => $user->id,
                            'address_label' => 'Previous Shipping Address',
                            'address_type' => 'shipping',
                            'first_name' => $contact->shipping_first_name,
                            'last_name' => $contact->shipping_last_name,
                            'company' => $contact->shipping_company,
                            'address_line_1' => $contact->shipping_address1,
                            'address_line_2' => $contact->shipping_address2,
                            'city' => $contact->shipping_city,
                            'state' => $contact->shipping_state,
                            'zip_code' => $contact->shipping_zip,
                            'country' => $contact->shipping_country,
                        ]);
                    }
                }

                // Then, update shipping address in Contact table from selected CustomerAddress
                $contact->shipping_first_name = $address->first_name;
                $contact->shipping_last_name = $address->last_name;
                $contact->shipping_company = $address->company;
                $contact->shipping_address1 = $address->address_line_1;
                $contact->shipping_address2 = $address->address_line_2;
                $contact->shipping_city = $address->city;
                $contact->shipping_state = $address->state;
                $contact->shipping_zip = $address->zip_code;
                $contact->shipping_country = $address->country;
                
                // Build the combined shipping address string
                $contact->shipping_address = $address->address_line_1 . ' ' .
                    ($address->address_line_2 ? $address->address_line_2 . ' ' : '') .
                    $address->city . ' ' .
                    $address->state . ' ' .
                    $address->zip_code . ' ' .
                    $address->country;
            }

            $contact->save();

            // Update cart table with the new address
            $cart = Cart::where('user_id', $user->id)->first();
            if ($cart) {
                if ($changeType == 'billing') {
                    $cart->billing_first_name = $address->first_name;
                    $cart->billing_last_name = $address->last_name;
                    $cart->billing_company = $address->company;
                    $cart->billing_address1 = $address->address_line_1;
                    $cart->billing_address2 = $address->address_line_2;
                    $cart->billing_city = $address->city;
                    $cart->billing_state = $address->state;
                    $cart->billing_zip = $address->zip_code;
                    $cart->billing_country = $address->country;
                    $cart->billing_phone = $address->phone;
                    $cart->billing_email = $user->email;
                } else if ($changeType == 'shipping') {
                    $cart->shipping_first_name = $address->first_name;
                    $cart->shipping_last_name = $address->last_name;
                    $cart->shipping_company = $address->company;
                    $cart->shipping_address1 = $address->address_line_1;
                    $cart->shipping_address2 = $address->address_line_2;
                    $cart->shipping_city = $address->city;
                    $cart->shipping_state = $address->state;
                    $cart->shipping_zip = $address->zip_code;
                    $cart->shipping_country = $address->country;
                }
                $cart->save();
            }
            $address->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Address set as default successfully',
                'data' => $contact,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to set address as default',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle rewarding coupons for referral program events on registration.
     *
     * @param  \App\Business|null  $business
     * @param  \App\Contact        $newCustomer
     * @param  mixed               $brand
     * @param  string|null         $usedReferalCode
     * @return void
     */
    private function handleReferalProgramRewards(?Business $business, $newCustomer, $brand, ?string $usedReferalCode): void
    {
        if (!$business || !$business->enable_referal_program || !$business->referal_available_for_b2c) {
            return;
        }

        if (!empty($business->referal_brand_list)) {
            $brandIds = array_filter(array_map('trim', explode(',', $business->referal_brand_list)));
            if (!$brand || !in_array((string) $brand->id, $brandIds, true)) {
                return;
            }
        }

        if (empty($usedReferalCode)) {
            return;
        }

        $referrer = Contact::where('referal_code', $usedReferalCode)->first();
        if (!$referrer || $referrer->id === $newCustomer->id) {
            return;
        }

        $templateId = $business->referal_program_custom_discount_id;
        if (empty($templateId)) {
            return;
        }

        $discountTemplate = CustomDiscount::find($templateId);
        if (!$discountTemplate) {
            return;
        }

        $this->createReferalCouponFromTemplate($discountTemplate, $referrer->id, $newCustomer->id);

        if ($business->referal_sent_to_both_sides) {
            $this->createReferalCouponFromTemplate($discountTemplate, $newCustomer->id, $referrer->id);
        }
    }

    /**
     * Clone the base discount template and record the coupon issuance.
     *
     * @param  \App\Models\CustomDiscount  $template
     * @param  int                         $beneficiaryCustomerId
     * @param  int|null                    $relatedCustomerId
     * @return void
     */
    private function createReferalCouponFromTemplate(CustomDiscount $template, int $beneficiaryCustomerId, ?int $relatedCustomerId = null): void
    {
        $couponCode = $this->generateUniqueCouponCode($template->couponCode);

        EcomReferalProgram::create([
            'coupon_code' => $couponCode,
            'discount_id' => $template->id,
            'customer_id' => $beneficiaryCustomerId,
            'referred_by_customer_id' => $relatedCustomerId,
            'mail_sent_to_customer' => false,
            'mail_sent_to_referred_by_customer' => false,
        ]);
        $contact = Contact::find($beneficiaryCustomerId);
        
        if($contact){
            $custom_data = (object)[
                'cupon_code' => $couponCode,
                'discount_id' => $template->id,
                'contact_id' => $beneficiaryCustomerId,
                'email' => $contact->email,
                'name' => $contact->name,
                'is_b2c' => true,
                'brand_id' => $contact->brand_id,
            ];
            // mail job 
            SendNotificationJob::dispatch(true, 1, 'refral_notification', null, $contact, null, $custom_data);
        }
    }

    /**
     * Generate a unique coupon code using an optional base string.
     *
     * @param  string|null  $baseCode
     * @return string
     */
    private function generateUniqueCouponCode(?string $baseCode = null): string
    {
        $prefix = $baseCode ? preg_replace('/[^A-Za-z0-9]/', '', strtoupper($baseCode)) : 'REF';
        $prefix = substr($prefix, 0, 6) ?: 'REF';

        do {
            $code = $prefix . strtoupper(Str::random(6));
        } while (
            CustomDiscount::where('couponCode', $code)->exists() ||
            EcomReferalProgram::where('coupon_code', $code)->exists()
        );

        return $code;
    }
}
