<?php

namespace App\Http\Controllers\ECOM;

use App\Brands;
use App\Contact;
use App\Cart;
use App\CartItem;
use App\GuestCartItem;
use App\GuestSession;
use App\Product;
use App\Http\Resources\ProductResource;
use App\ContactUs;
use App\CustomerAddress;
use App\CustomerGroup;
use App\Models\DeliveryPreference;
use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJob;
use App\Mail\ContactUsMail;
use App\Media;
use App\Models\ElevenLabsSessionModel;
use App\Models\WpVendor;
use App\NewsLetterSubscriber;
use App\PasswordResetOtp;
use App\Utils\ContactUtil;
use App\Utils\NotificationUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use MikeMcLin\WpPassword\Facades\WpPassword;

class CustomerAuthController extends Controller
{
    protected $contactUtil;
    protected $notificationUtil;

    public function __construct(ContactUtil $contactUtil ,NotificationUtil $notificationUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->notificationUtil = $notificationUtil;
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

    
    public function login(Request $request)
    {
        try {
            // Accept email or phone: support both 'email' and 'phone' keys for backward compatibility
            $login = $request->input('phone') ?? $request->input('email');
            $credentials = [
                'login' => $login,
                'password' => $request->input('password'),
            ];
            if (empty($credentials['login']) || empty($credentials['password'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email or phone and password are required',
                ], 422);
            }
            $locationId = $request->route('location_id');
            // Build query with location and brand constraints if provided
            $query = Contact::where(function($query) use ($credentials) {
                $query->where('type', 'customer')
                      ->orWhere('type', 'both')
                      ->whereNull('brand_id')
                      ->where('location_id',1);
            })
            ->where(function($query) use ($credentials) {
                $login = $credentials['login'];
                $query->where('email', $login)
                      ->orWhere('customer_u_name', $login)
                      ->orWhere('contact_id', $login)
                      ->orWhere('mobile', $login);
            });
            
            // Add location constraint for B2C routes
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
            
            $contact = $query->first();
            if(!$contact||$contact->brand_id){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials',
                ]);
            }
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
                    $group_name = $customer_group->name??'';
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
                    'is_tax_exempt'=>$contact->is_tax_exempt ?? false,
                ];

                // If a B2B guest_session is provided, merge its cart into this user's cart
                if ($request->filled('guest_session')) {
                    try {
                        $guestToken = $request->get('guest_session');
                        $locationId = config('services.b2b.location_id', 1);

                        $guestSession = GuestSession::where('uuid', $guestToken)
                            ->where('location_id', $locationId)
                            ->where('expires_at', '>', now())
                            ->first();

                        if ($guestSession) {
                            $guestCartItems = GuestCartItem::where('guest_session_id', $guestSession->id)->get();

                            foreach ($guestCartItems as $guestItem) {
                                $existing = CartItem::where('user_id', $contact->id)
                                    ->where('product_id', $guestItem->product_id)
                                    ->where('variation_id', $guestItem->variation_id)
                                    ->first();

                                if ($existing) {
                                    $existing->update(['qty' => $existing->qty + $guestItem->qty]);
                                } else {
                                    CartItem::create([
                                        'user_id' => $contact->id,
                                        'product_id' => $guestItem->product_id,
                                        'variation_id' => $guestItem->variation_id,
                                        'qty' => $guestItem->qty,
                                        'item_type' => $guestItem->item_type,
                                        'discount_id' => $guestItem->discount_id,
                                        'lable' => $guestItem->lable,
                                    ]);
                                }
                            }

                            // Clean up guest cart (keep session to allow further guest use if needed)
                            GuestCartItem::where('guest_session_id', $guestSession->id)->delete();
                        }
                    } catch (\Throwable $mergeException) {
                        \Log::warning('Failed to merge B2B guest cart on login', [
                            'guest_session' => $request->get('guest_session'),
                            'user_id' => $contact->id ?? null,
                            'error' => $mergeException->getMessage(),
                        ]);
                    }
                }

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
    public function refreshToken(Request $request)
    {
        try {
            // Get brand-specific refresh token cookie name
            $brandName = $request->route('brand_name');
            $refreshTokenCookieName = $brandName ? "refresh_token_{$brandName}" : 'refresh_token';
            $refreshToken = $request->cookie($refreshTokenCookieName);
            
            if (!$refreshToken) {
                return response()->json([
                    'status' => false,
                    'message' => 'Refresh token not found',
                ], 401);
            }

            // Find the refresh token in the database
            $token = DB::table('oauth_refresh_tokens')
                ->where('id', $refreshToken)
                ->where('revoked', false)
                ->where('expires_at', '>', now())
                ->first();

            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired refresh token',
                ], 401);
            }

            // Get the access token
            $accessToken = DB::table('oauth_access_tokens')
                ->where('id', $token->access_token_id)
                ->where('revoked', false)
                ->first();

            if (!$accessToken) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid access token',
                ], 401);
            }

            // Get the contact
            $contact = Contact::find($accessToken->user_id);
            
            if (!$contact) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 401);
            }

            // Revoke the old tokens
            DB::table('oauth_access_tokens')
                ->where('id', $accessToken->id)
                ->update(['revoked' => true]);
            
            DB::table('oauth_refresh_tokens')
                ->where('id', $refreshToken)
                ->update(['revoked' => true]);

            // Create new access token
            $tokenResult = $contact->createToken('Customer Access Token');
            $newAccessToken = $tokenResult->accessToken;
            
            // Generate new refresh token
            $newRefreshToken = Str::random(60);
            
            // Store new refresh token in database
            DB::table('oauth_refresh_tokens')->insert([
                'id' => $newRefreshToken,
                'access_token_id' => $tokenResult->token->id,
                'revoked' => false,
                'expires_at' => now()->addDays(3),
            ]);

            return response()->json([
                'status' => true,
                'access_token' => $newAccessToken,
                'message' => 'Token refreshed successfully'
            ])
            ->withCookie(cookie(
                $refreshTokenCookieName,
                $newRefreshToken,
                4320, // 3 days in minutes
                '/',
                null,
                false,
                false,
                false,
                'None'
            ));

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Token refresh failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = auth('api')->user();  // Get the authenticated user

            if ($user) {
                // Revoke the current user's token
                $user->tokens->each(function ($token) {
                    $token->delete(); // Or revoke the token
                });

                // Get brand-specific refresh token cookie name for logout
                $brandName = $request->route('brand_name');
                $refreshTokenCookieName = $brandName ? "refresh_token_{$brandName}" : 'refresh_token';
                
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully logged out',
                ])
                ->withCookie(cookie(
                    $refreshTokenCookieName,
                    '',
                    -1, // Expire immediately
                    '/',
                    null,
                    false,
                    false,
                    false,
                    'None'
                ));
            }

            return response()->json([
                'status' => false,
                'message' => 'No authenticated user found',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'logout function failed',
                'error' => $th->getMessage()
            ]);
        }
    }
    public function register(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'prefix' => 'nullable|string|max:10',
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'supplier_business_name' => 'nullable|string|max:100',
            'contact_type' => 'nullable|in:individual,business',
            'tax_number' => 'nullable|string|max:20',
            'pay_term_number' => 'nullable|integer',
            'pay_term_type' => 'nullable|string|in:days,months',
            'mobile' => 'required|string|min:10|max:10',
            'landline' => 'nullable|string|max:15',
            'alternate_number' => 'nullable|string|max:15',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
            'contact_id' => 'nullable|string|max:50',
            // 'email' => 'nullable|email|max:100',
            'email' => ['nullable','email','max:100', Rule::unique('contacts', 'email')->whereNull('deleted_at')],
            'shipping_address' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:100',
            'dob' => 'nullable|date',
            'shipping_custom_field_details' => 'nullable|string|max:255',
            'password' => 'required|confirmed|string|min:8',
            'FEIN-License' => 'required|file|mimes:pdf,jpg,png,jpeg|max:10240',  // max 10MB=10240
            'Tobacco-License' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:10240',
            'State-Tax-Business-License' => 'required|file|mimes:pdf,jpg,png,jpeg|max:10240',
            'Government-Issued-ID' => 'required|file|mimes:pdf,jpg,png,jpeg|max:10240',
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
        try {
            DB::beginTransaction();


            // // Create the customer
            // $customer = Contact::create([
            //     'prefix' => $request->input('prefix'),
            //     'business_id' => 1,
            //     'created_by' => 1,
            //     'first_name' => $request->input('first_name'),
            //     'middle_name' => $request->input('middle_name'),
            //     'last_name' => $request->input('last_name'),
            //     'supplier_business_name' => $request->input('supplier_business_name'),
            //     'contact_type' => $request->input('contact_type', 'customer'),
            //     'tax_number' => $request->input('tax_number'),
            //     'pay_term_number' => $request->input('pay_term_number'),
            //     'pay_term_type' => $request->input('pay_term_type'),
            //     'mobile' => $request->input('mobile'),
            //     'landline' => $request->input('landline'),
            //     'alternate_number' => $request->input('alternate_number'),
            //     'city' => $request->input('city'),
            //     'state' => $request->input('state'),
            //     'address_line_1' => $request->input('address_line_1'),
            //     'address_line_2' => $request->input('address_line_2'),
            //     'zip_code' => $request->input('zip_code'),
            //     'contact_id' => $request->input('contact_id'),
            //     'email' => $request->input('email'),
            //     'shipping_address' => $request->input('shipping_address'),
            //     'position' => $request->input('position'),
            //     'dob' => $request->input('dob')?? now(),
            //     'shipping_custom_field_details' => $request->input('shipping_custom_field_details'),
            //     'type' => 'customer',
            //     'name' => trim(implode(' ', array_filter([
            //         $request->input('prefix'),
            //         $request->input('first_name'),
            //         $request->input('middle_name'),
            //         $request->input('last_name')
            //     ]))),
            //     'country' => 'US',
            //     'password' => Hash::make($request->input('password')),
            //     'isApproved' => null,
            // ]);
            $input = $request->all();
            $locationId = $request->route('location_id')??config('services.b2b.location_id');
            
            // Get business_id from location
            $location = $request->get('current_location');
            $businessId = $location ? $location->business_id : 1;
            
            $input['business_id'] = $businessId;
            $input['created_by'] = 1; // Fixed created_by
            
            // Handle customer_group_id for B2C customers
            // Only set if explicitly provided in request, otherwise leave as null for B2C
            if ($request->has('customer_group_id') && !empty($request->input('customer_group_id'))) {
                $input['customer_group_id'] = $request->input('customer_group_id')??1;
            } else {
                // For B2C customers, customer_group_id can be null
                $input['customer_group_id'] = 1;
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
            // $input['password'] = Hash::make($request->input('password'));
            $input['password'] = Hash::make($request->input('password'));
            $input['name'] = trim(implode(' ', array_filter([
                $request->input('prefix'),
                $request->input('first_name'),
                $request->input('middle_name'),
                $request->input('last_name')
            ])));

            $input['shipping_first_name'] = $request->input('first_name');
            $input['shipping_last_name'] = $request->input('last_name');
            $input['shipping_company'] = $request->input('supplier_business_name');
            $input['shipping_address1'] = $request->input('address_line_1');
            $input['shipping_address2'] = $request->input('address_line_2');
            $input['shipping_city'] = $request->input('city');
            $input['shipping_state'] = $request->input('state');
            $input['shipping_zip'] = $request->input('zip_code');
            $input['shipping_country'] = $request->input('country')??'US';
            $input['shipping_address'] = $input['shipping_address1'] . ' ' .
            ($input['shipping_address2'] ? $input['shipping_address2'] . ' ' : '') .
            $input['shipping_city'] . ' ' .
            $input['shipping_state'] . ' ' .
            $input['shipping_zip'] . ' ' .
            $input['shipping_country'];

            // Call the ContactUtil function
            $contactResponse = $this->contactUtil->createNewContact($input);

            if (!$contactResponse['success']) {
                throw new \Exception("Failed to create contact");
            }

            $customer = $contactResponse['data'];
            $fileInputs = [
                'FEIN-License',
                'Tobacco-License',
                'State-Tax-Business-License',
                'Government-Issued-ID'
            ];

            foreach ($fileInputs as $inputName) {
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    if ($file->isValid()) {
                        $timestamp = time();
                        $randomNumber = rand(1000000000, 9999999999);
                        $extension = $file->getClientOriginalExtension();
                        $originalFileName = $file->getClientOriginalName();
                        $fileName = "{$timestamp}_{$randomNumber}_{$originalFileName}";

                        $destinationPath = public_path('uploads/media');
                        if (!File::exists($destinationPath)) {
                            File::makeDirectory($destinationPath, 0775, true);
                        }

                        // Move file to the destination folder
                        $file->move($destinationPath, $fileName);

                        // Create input for the document
                        $input = [
                            'heading' => $inputName,
                            'is_private' => true,
                            'business_id' => 1,
                            'created_by' => 1,
                        ];

                        // Attach the file to the contact
                        $model = Contact::findOrFail($customer->id);
                        $model_note = $model->documentsAndnote()->create($input);
                        Media::attachMediaToModel($model_note, $input['business_id'], $fileName, null, null, $customer->id);
                    }
                }
            }
            DB::commit();
            // Mail::send('emails.registration-confirmation', [
            //     'first_name' => $customer->first_name,
            //     'last_name' => $customer->last_name
            // ], function ($message) use ($customer) {
            //     $message->to($customer->email); // Send the email to the customer's email
            //     $message->subject('Welcome to Our Service!');
            // });

            $contact=(object)[
                'email'=>$request->email,
                'mobile'=>$request->phone
            ];
            $user=(object)[
                'name'=>$customer->first_name.$customer->last_name,
                'mobile'=>$request->phone,
                'email'=>$request->email,      
            ];
            // $whatsapp_link = $this->notificationUtil->autoSendNotificationCustom(1, 'registration_confirmation', $user, $contact);
            SendNotificationJob::dispatch(true, 1 , 'registration_confirmation', $user, $contact);
            
            return response()->json(['status' => true,'message' => 'Customer registered successfully', 'userID' => '1049AD_' . $customer->id], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false,'message' => $th->getMessage() . ' at ' . $th->getLine(),], 500);
        }
    }

    /**
     * Vendor/Supplier Registration API
     * Similar to customer registration but creates a supplier contact
     */
    public function vendorRegister(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'prefix' => 'nullable|string|max:10',
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'supplier_business_name' => 'required|string|max:100',
            'contact_type' => 'nullable|in:individual,business',
            'tax_number' => 'nullable|string|max:20',
            'pay_term_number' => 'nullable|integer',
            'pay_term_type' => 'nullable|string|in:days,months',
            'mobile' => 'required|string|min:10|max:10',
            'landline' => 'nullable|string|max:15',
            'alternate_number' => 'nullable|string|max:15',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
            'contact_id' => 'nullable|string|max:50',
            'email' => ['nullable','email','max:100', Rule::unique('contacts', 'email')->where('type', 'supplier')->whereNull('deleted_at')],
            'shipping_address' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:100',
            'dob' => 'nullable|date',
            'shipping_custom_field_details' => 'nullable|string|max:255',
            'password' => 'required|confirmed|string|min:8',
            'is_tax_exempt' => 'nullable|boolean',
            'FEIN-License' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'Tobacco-License' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'State-Tax-Business-License' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'Government-Issued-ID' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            // Dropshipping vendor fields
            'vendor_type' => 'nullable|in:normal,dropshipping',
            'commission_type' => 'nullable|in:percentage,fixed',
            'commission_value' => 'nullable|numeric|min:0',
            'default_markup_percentage' => 'nullable|numeric|min:0|max:100',
            'margin_percentage' => 'nullable|numeric|min:0|max:100',
            'dropship_payment_terms' => 'nullable|in:immediate,weekly,biweekly,monthly',
            'dropship_payment_method' => 'nullable|string|max:100',
            'lead_time_days' => 'nullable|integer|min:0',
            'min_order_qty' => 'nullable|integer|min:0',
            'auto_forward_orders' => 'nullable',
            'dropship_notes' => 'nullable|string|max:1000',
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

        try {
            DB::beginTransaction();

            $input = $request->all();
            $locationId = $request->route('location_id') ?? config('services.b2b.location_id');
            
            // Get business_id from location
            $location = $request->get('current_location');
            $businessId = $location ? $location->business_id : 1;
            
            $input['business_id'] = $businessId;
            $input['created_by'] = 1;
            
            // Set type to supplier for vendor registration
            $input['type'] = 'supplier';
            $input['country'] = 'US';
            $input['isApproved'] = null;
            
            // Set location_id
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
            
            $input['password'] = Hash::make($request->input('password'));
            $input['name'] = trim(implode(' ', array_filter([
                $request->input('prefix'),
                $request->input('first_name'),
                $request->input('middle_name'),
                $request->input('last_name')
            ])));

            $input['shipping_first_name'] = $request->input('first_name');
            $input['shipping_last_name'] = $request->input('last_name');
            $input['shipping_company'] = $request->input('supplier_business_name');
            $input['shipping_address1'] = $request->input('address_line_1');
            $input['shipping_address2'] = $request->input('address_line_2');
            $input['shipping_city'] = $request->input('city');
            $input['shipping_state'] = $request->input('state');
            $input['shipping_zip'] = $request->input('zip_code');
            $input['shipping_country'] = $request->input('country') ?? 'US';
            $input['shipping_address'] = $input['shipping_address1'] . ' ' .
                ($input['shipping_address2'] ? $input['shipping_address2'] . ' ' : '') .
                $input['shipping_city'] . ' ' .
                $input['shipping_state'] . ' ' .
                $input['shipping_zip'] . ' ' .
                $input['shipping_country'];

            // Handle tax exemption - set to 0 if not provided
            $input['is_tax_exempt'] = $request->has('is_tax_exempt') && $request->input('is_tax_exempt') ? 1 : 0;

            // Handle dropshipping vendor fields
            $input['vendor_type'] = $request->input('vendor_type', 'normal');
            $input['commission_type'] = $request->input('commission_type');
            $input['dropship_payment_terms'] = $request->input('dropship_payment_terms');
            $input['dropship_payment_method'] = $request->input('dropship_payment_method');
            $input['lead_time_days'] = $request->input('lead_time_days');
            $input['min_order_qty'] = $request->input('min_order_qty');
            $input['dropship_notes'] = $request->input('dropship_notes');
            
            // Handle numeric fields - convert string to decimal if provided
            $input['commission_value'] = $request->filled('commission_value') ? (float) $request->input('commission_value') : null;
            $input['default_markup_percentage'] = $request->filled('default_markup_percentage') ? (float) $request->input('default_markup_percentage') : null;
            $input['margin_percentage'] = $request->filled('margin_percentage') ? (float) $request->input('margin_percentage') : null;
            
            // Handle auto_forward_orders - accept string "true"/"false" or boolean
            if ($request->has('auto_forward_orders')) {
                $autoForward = $request->input('auto_forward_orders');
                if (is_string($autoForward)) {
                    $input['auto_forward_orders'] = in_array(strtolower($autoForward), ['true', '1', 'yes', 'on']) ? 1 : 0;
                } else {
                    $input['auto_forward_orders'] = $autoForward ? 1 : 0;
                }
            } else {
                $input['auto_forward_orders'] = 0;
            }

            // Call the ContactUtil function
            $contactResponse = $this->contactUtil->createNewContact($input);

            if (!$contactResponse['success']) {
                throw new \Exception("Failed to create vendor contact");
            }

            $vendor = $contactResponse['data'];
            $fileInputs = [
                'FEIN-License',
                'Tobacco-License',
                'State-Tax-Business-License',
                'Government-Issued-ID'
            ];

            foreach ($fileInputs as $inputName) {
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    if ($file->isValid()) {
                        $timestamp = time();
                        $randomNumber = rand(1000000000, 9999999999);
                        $extension = $file->getClientOriginalExtension();
                        $originalFileName = $file->getClientOriginalName();
                        $fileName = "{$timestamp}_{$randomNumber}_{$originalFileName}";

                        $destinationPath = public_path('uploads/media');
                        if (!File::exists($destinationPath)) {
                            File::makeDirectory($destinationPath, 0775, true);
                        }

                        // Move file to the destination folder
                        $file->move($destinationPath, $fileName);

                        // Create input for the document
                        $docInput = [
                            'heading' => $inputName,
                            'is_private' => true,
                            'business_id' => $businessId,
                            'created_by' => 1,
                        ];

                        // Attach the file to the contact
                        $model = Contact::findOrFail($vendor->id);
                        $model_note = $model->documentsAndnote()->create($docInput);
                        Media::attachMediaToModel($model_note, $docInput['business_id'], $fileName, null, null, $vendor->id);
                    }
                }
            }
            
            // Create WpVendor record if vendor_type is 'dropshipping'
            if ($input['vendor_type'] === 'dropshipping') {
                $formattedAddress = trim(collect([
                    $request->input('address_line_1'),
                    $request->input('address_line_2'),
                    $request->input('city'),
                    $request->input('state'),
                    $request->input('zip_code')
                ])->filter()->implode(', '));
                
                $wpVendorData = [
                    'name' => $vendor->supplier_business_name ?: $vendor->name,
                    'company_name' => $vendor->supplier_business_name,
                    'email' => $vendor->email,
                    'phone' => $vendor->mobile,
                    'address' => $formattedAddress,
                    'contact_id' => $vendor->id,
                    'business_id' => $businessId,
                    'vendor_type' => WpVendor::TYPE_ERP_DROPSHIP,
                    'status' => $vendor->isApproved ? WpVendor::STATUS_ACTIVE : WpVendor::STATUS_PENDING,
                    'commission_type' => $input['commission_type'] ?? WpVendor::COMMISSION_PERCENTAGE,
                    'commission_value' => $input['commission_value'] ?? 0,
                    'default_markup_percentage' => $input['default_markup_percentage'] ?? 0,
                    'margin_percentage' => $input['margin_percentage'] ?? 0,
                    'payment_terms' => $input['dropship_payment_terms'] ?? 'monthly',
                    'payment_method' => $input['dropship_payment_method'] ?? null,
                    'notes' => $input['dropship_notes'] ?? null,
                ];
                
                // Create or update WpVendor
                $wpVendor = WpVendor::updateOrCreate(
                    ['contact_id' => $vendor->id, 'business_id' => $businessId],
                    $wpVendorData
                );
            }
            
            DB::commit();

            $contact = (object)[
                'email' => $request->email,
                'mobile' => $request->mobile
            ];
            $user = (object)[
                'name' => $vendor->first_name . ' ' . $vendor->last_name,
                'mobile' => $request->mobile,
                'email' => $request->email,
            ];
            
            SendNotificationJob::dispatch(true, $businessId, 'registration_confirmation', $user, $contact);
            
            return response()->json([
                'status' => true,
                'message' => 'Vendor registered successfully',
                'userID' => '1049AD_' . $vendor->id,
                'dropship_vendor_id' => isset($wpVendor) ? $wpVendor->id : null
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage() . ' at ' . $th->getLine()
            ], 500);
        }
    }

    public function myAccount(Request $request)
    {
        try {
            
            $contact = Auth::guard('api')->user();
            if (!$contact) {
                return response()->json([
                    'status' => false,
                    'message' => 'No authenticated user found',
                ], 401);
            }
            $profileName = '';
            if ($contact->name) {
                $profileName = $contact->name;
            } else if ($contact->supplier_business_name) {
                $profileName = $contact->supplier_business_name;
            }
            
            // Get customer group name
            $groupName = $contact->customerGroup->name ?? '';

            // Build billing & shipping fields from the Contact so that
            // any changes done via update-address / saved-address APIs
            // are reflected in the my-account response.
            $billing_first_name = $contact->first_name;
            $billing_last_name = $contact->last_name;
            $billing_company = $contact->supplier_business_name;
            $billing_address1 = $contact->address_line_1;
            $billing_address2 = $contact->address_line_2;
            $billing_city = $contact->city;
            $billing_state = $contact->state;
            $billing_zip = $contact->zip_code;
            $billing_country = $contact->country;

            $shipping_first_name = $contact->shipping_first_name ?: $billing_first_name;
            $shipping_last_name = $contact->shipping_last_name ?: $billing_last_name;
            $shipping_company = $contact->shipping_company ?: $billing_company;
            $shipping_address1 = $contact->shipping_address1;
            $shipping_address2 = $contact->shipping_address2;
            $shipping_city = $contact->shipping_city ?: $billing_city;
            $shipping_state = $contact->shipping_state;
            $shipping_zip = $contact->shipping_zip;
            $shipping_country = $contact->shipping_country;
            $shipping_address_line = $contact->shipping_address;

            // Tobacco-License status for profile / account-info page
            $latestTobaccoDoc = $contact->documentsAndnote()
                ->where('heading', 'Tobacco-License')
                ->latest()
                ->first();

            $tobaccoLicenseStatus = 'missing';
            $tobaccoLicenseApproved = false;
            $tobaccoLicenseUploadedAt = null;
            $tobaccoLicenseApprovedAt = null;

            if ($latestTobaccoDoc) {
                $tobaccoLicenseApproved = (bool)($latestTobaccoDoc->is_approved ?? false);
                $tobaccoLicenseStatus = $tobaccoLicenseApproved ? 'approved' : 'pending';
                $tobaccoLicenseUploadedAt = optional($latestTobaccoDoc->created_at)->toDateTimeString();
                $tobaccoLicenseApprovedAt = optional($latestTobaccoDoc->approved_at)->toDateTimeString();
            }
            
            return response()->json([
                'status' => true,
                'message' => 'User information retrieved successfully',
                'data' => [
                    'id' => $contact->id,
                    'profileName' => $profileName,
                    "prifix"=>$contact->prefix??'',
                    'first_name' => $contact->first_name,
                    'last_name' => $contact->last_name,
                    'middle_name' => $contact->middle_name,
                    'mobile' => $contact->mobile,
                    'type' => $contact->type,
                    'email' => $contact->email,
                    'role' => $contact->role,
                    'priceType' => $contact->price_tier??'',
                    'contact_id'=>$contact->contact_id??'',
                    'contact_type'=>$contact->contact_type??'',
                    'is_approved'=>$contact->isApproved??'',
                    'is_active'=>$contact->isActive??'',
                    'balance'=>$contact->balance??'',
                    'credit_application_status'=>$contact->credit_application_status??'',
                    'business_name'=>$contact->supplier_business_name??'',
                    'group_name'=>$groupName,
                    'is_tax_exempt'=>$contact->is_tax_exempt ?? false,
                    // Expose latest billing / shipping details
                    'billing_first_name' => $billing_first_name,
                    'billing_last_name' => $billing_last_name,
                    'billing_company' => $billing_company,
                    'billing_address1' => $billing_address1,
                    'billing_address2' => $billing_address2,
                    'billing_city' => $billing_city,
                    'billing_state' => $billing_state,
                    'billing_zip' => $billing_zip,
                    'billing_country' => $billing_country,
                    'shipping_first_name' => $shipping_first_name,
                    'shipping_last_name' => $shipping_last_name,
                    'shipping_company' => $shipping_company,
                    'shipping_address1' => $shipping_address1,
                    'shipping_address2' => $shipping_address2,
                    'shipping_city' => $shipping_city,
                    'shipping_state' => $shipping_state,
                    'shipping_zip' => $shipping_zip,
                    'shipping_country' => $shipping_country,
                    'shipping_address_line' => $shipping_address_line,
                    'tobacco_license' => [
                        'status' => $tobaccoLicenseStatus, // missing | pending | approved
                        'is_approved' => $tobaccoLicenseApproved,
                        'uploaded_at' => $tobaccoLicenseUploadedAt,
                        'approved_at' => $tobaccoLicenseApprovedAt,
                    ],
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'myaccount function failed', 'error' => $th->getMessage() . ' on ' . $th->getFile()]);
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

        $platform = strtolower(trim($request->header('X-Platform', '')));
        // Default to web when header is missing/empty.
        $isWeb = $platform === '' || $platform === 'web';

        if ($isWeb) {
            // Web: link-only email (token in URL, no OTP)
            PasswordResetOtp::where('email', $user->email)->delete();

            $token = Str::random(60);
            $user->remember_token = $token;
            $user->save();

            $contact = (object)[
                'id' => $user->id,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'is_b2c' => $user->brand_id ? true : false,
                'brand_id' => $user->brand_id ?? null,
            ];
            SendNotificationJob::dispatch(true, 1, 'forget_password_web', $user, $contact, null, []);
        } else {
            // Mobile or omitted: OTP flow
            PasswordResetOtp::where('email', $user->email)->delete();

            $otp = (string) random_int(100000, 999999);
            PasswordResetOtp::create([
                'email' => $user->email,
                'contact_id' => $user->id,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(15),
            ]);

            $contact = (object)[
                'id' => $user->id,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'is_b2c' => $user->brand_id ? true : false,
                'brand_id' => $user->brand_id ?? null,
            ];
            SendNotificationJob::dispatch(true, 1, 'forget_password', $user, $contact, null, ['otp' => $otp]);
        }

        return response()->json(['status' => true, 'message' => 'We have emailed your password reset link!']);
    }

    /**
     * Verify OTP from forgot-password email and issue reset token for set-password page (B2B).
     */
    public function verifyResetOtp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $this->formatValidationErrors($validate),
            ]);
        }

        $otpRecord = PasswordResetOtp::where('email', $request->input('email'))
            ->where('otp', $request->input('otp'))
            ->valid()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        $otpRecord->delete();
        $resetToken = Str::random(60);
        $contact = Contact::find($otpRecord->contact_id);
        if ($contact) {
            $contact->update(['remember_token' => $resetToken]);
        }

        $email = $request->input('email');
        $frontUrl = rtrim(config('app.front-url', 'http://localhost'), '/');
        $redirectUrl = $frontUrl . '/set-password?token=' . urlencode($resetToken) . '&email=' . urlencode($email);

        return response()->json([
            'status' => true,
            'message' => 'OTP verified. You can now reset your password.',
            'reset_token' => $resetToken,
            'email' => $email,
            'redirect_url' => $redirectUrl,
        ]);
    }

    /**
     * Reset password with OTP in one step (forgot-password flow).
     * User lands from email link, enters OTP + new password. Wrong OTP → error; correct OTP → password updated.
     */
    public function resetPasswordWithOtp(Request $request)
    {
        // Ensure JSON body is merged when needed
        if (! $request->has('email') && $request->getContent()) {
            $json = json_decode($request->getContent(), true);
            if (is_array($json)) {
                $request->merge($json);
            }
        }

        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:6|confirmed',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $this->formatValidationErrors($validate),
            ], 422);
        }

        $otpRecord = PasswordResetOtp::where('email', $request->input('email'))
            ->where('otp', $request->input('otp'))
            ->valid()
            ->first();

        if (! $otpRecord) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired OTP. You cannot change your password.',
            ], 422);
        }

        $contact = Contact::find($otpRecord->contact_id);
        if (! $contact) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired OTP. You cannot change your password.',
            ], 422);
        }

        $contact->update([
            'password' => Hash::make($request->input('password')),
            'remember_token' => null,
        ]);
        $otpRecord->delete();

        $contactInfo = (object)[
            'id' => $contact->id,
            'email' => $contact->email,
            'mobile' => $contact->name,
            'is_b2c' => $contact->brand_id ? true : false,
            'brand_id' => $contact->brand_id ?? null,
        ];
        SendNotificationJob::dispatch(true, 1, 'password_reset_success', $contact, $contactInfo);

        return response()->json([
            'status' => true,
            'message' => 'Your password has been reset successfully.',
        ]);
    }

    // public function reset(Request $request)
    // {
    //     try {
    //         $contact = Auth::guard('api')->user();
    //         $validate = Validator::make($request->all(), [
    //             'old_password' => 'required',
    //             'password' => 'required|min:6|confirmed',
    //         ]);
    //         if ($validate->fails()) {
    //             $errors = $validate->errors()->toArray();
    //             $formattedErrors = [];
    //             foreach ($errors as $key => $errorMessages) {
    //                 $formattedErrors[] = [
    //                     'field' => $key,
    //                     'messages' => $errorMessages
    //                 ];
    //             }
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => $formattedErrors
    //             ]);
    //         }
    //         if (!Hash::check($request->input('old_password'), $contact->password)) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'The old password is incorrect.',
    //             ]);
    //         }
    //         $contact = Contact::find($contact->id);
    //         $contact->password = Hash::make($request->input('password'));
    //         $contact->save();
    //         return response()->json(['status' => true, 'message' => 'Your password has been reset!']);
    //     } catch (\Throwable $th) {
    //         $validate = Validator::make($request->all(), [
    //             'email' => 'required|email',
    //             'token' => 'required',
    //             'password' => 'required|min:6|confirmed',
    //         ]);
    //         if ($validate->fails()) {
    //             $errors = $validate->errors()->toArray();
    //             $formattedErrors = [];
    //             foreach ($errors as $key => $errorMessages) {
    //                 $formattedErrors[] = [
    //                     'field' => $key,
    //                     'messages' => $errorMessages
    //                 ];
    //             }
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => $formattedErrors
    //             ]);
    //         }

    //         $resetRecord = Contact::where('email', $request->input('email'))->first();
    //         if (!$resetRecord || $request->input('token') !== $resetRecord->remember_token) {
    //             return response()->json([
    //                 'status' => false, 
    //                 'message' => 'This password reset token is invalid.',
    //             ]);
    //         }
    //         $resetRecord->update([
    //             'password' => Hash::make($request->input('password')),
    //             'remember_token' => null
    //         ]);
    //         // Mail::send('emails.password-reset-success', ['email' => $resetRecord->email,'Username'=> $resetRecord->name], function ($message) use ($resetRecord) {
    //         //     $message->to($resetRecord->email);
    //         //     $message->subject('Your Password Has Been Reset');
    //         // });

    //         $contact=(object)[
    //             'email'=>$resetRecord->email,
    //             'mobile'=>$resetRecord->name
    //         ];

    //         // $whatsapp_link = $this->notificationUtil->autoSendNotificationCustom(1, 'password_reset_success', $resetRecord, $contact);
    //         SendNotificationJob::dispatch(true, 1 , 'password_reset_success', $resetRecord, $contact);

    //         return response()->json(['status' => true, 'message' => 'Your password has been reset!']);
    //     }
    // }


    public function reset(Request $request)
    {
        // Ensure JSON body is merged into request when input is empty (e.g. Content-Type not applied or body not parsed)
        if (! $request->has('email') && $request->getContent()) {
            $json = json_decode($request->getContent(), true);
            if (is_array($json)) {
                $request->merge($json);
            }
        }

        // Forgot-password flow (email + token + password): handle without touching Passport so set-password works even when no Bearer token
        if ($request->filled('email') && $request->filled('token') && $request->filled('password')) {
            $validate = Validator::make($request->all(), [
                'email' => 'required|email',
                'token' => 'required',
                'password' => 'required|min:6|confirmed',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $this->formatValidationErrors($validate),
                ]);
            }

            $locationId = $request->route('location_id');
            $query = Contact::where('email', $request->input('email'));
            if ($locationId) {
                $query->where('location_id', $locationId);
            }
            $brandName = $request->route('brand_name');
            if ($brandName && $request->has('current_brand')) {
                $brand = $request->get('current_brand');
                if ($brand) {
                    $query->where('brand_id', $brand->id);
                }
            }
            $resetRecord = $query->first();

            if (!$resetRecord || $request->input('token') !== $resetRecord->remember_token) {
                return response()->json([
                    'status' => false,
                    'message' => 'This password reset token is invalid.',
                ]);
            }

            $resetRecord->update([
                'password' => Hash::make($request->input('password')),
                'remember_token' => null
            ]);

            $contactInfo = (object)[
                'id' => $resetRecord->id,
                'email' => $resetRecord->email,
                'mobile' => $resetRecord->name,
                'is_b2c' => $resetRecord->brand_id ? true : false,
                'brand_id' => $resetRecord->brand_id,
            ];
            SendNotificationJob::dispatch(true, 1, 'password_reset_success', $resetRecord, $contactInfo);

            return response()->json(['status' => true, 'message' => 'Your password has been reset!']);
        }

        // Logged-in user changing password (old_password + password)
        $contact = Auth::guard('api')->user();
        if ($contact && $request->has('old_password')) {
            $validate = Validator::make($request->all(), [
                'old_password' => 'required',
                'password' => 'required|min:6|confirmed',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $this->formatValidationErrors($validate),
                ]);
            }

            if (!Hash::check($request->input('old_password'), $contact->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'The old password is incorrect.',
                ]);
            }

            $contact->password = Hash::make($request->input('password'));
            $contact->save();

            return response()->json(['status' => true, 'message' => 'Your password has been reset!']);
        }

        return response()->json([
            'status' => false,
            'message' => 'Email, token and password are required for password reset.',
        ], 422);
    }

    private function formatValidationErrors($validator)
    {
        $errors = $validator->errors()->toArray();
        $formattedErrors = [];

        foreach ($errors as $key => $messages) {
            $formattedErrors[] = [
                'field' => $key,
                'messages' => $messages
            ];
        }

        return $formattedErrors;
    }

    public function contactus(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:10',
            'message' => 'required|string|max:5000',
            'meta' => ['nullable', function ($attribute, $value, $fail) use ($request) {
                if (!is_string($value) || empty($value)) {
                    return;
                }
                $decoded = json_decode($value, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $fail("The {$attribute} field must be a valid JSON.");
                    return;
                }
                $blacklist = ['<script', '</script', 'onerror', 'onload', 'javascript:', 'vbscript:', 'expression(', 'eval('];
                $foundMalicious = false;
        
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
                'message' => 'You have reached the request limit. Please try again after a minute.'
            ],);
        }
// dd();
        $reference_no = 'CU' . strtoupper(uniqid());
        
        // Get location_id from request or set default
        $location_id = $request->input('location_id', 1);
        
        // Set brand_id based on location_id condition
        $brand_id = null;
        if ($location_id != 1) {
            $brand_id = $request->input('brand_id');
        }
        
        $contact = ContactUs::create([
            'reference_no' => $reference_no,
            'fname' =>  $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'meta' => json_encode([
               
            ]),
            'status' => 'Pending',
            'staff_id' => null,
            'location_id' => $location_id,
            'brand_id' => $brand_id,
        ]);
        // Mail::send('emails.contact-us-success', [
        //     'name' => $request->full_name,
        //     'subject' => $request->subject,
        //     'message' => $request->message,
        //     'referenceNo'=>$reference_no       
        // ], function ($message) use ($request) {
        //     $message->to($request->email);
        //     $message->subject('We have received your message');
        //     // $message->refrenceNo($reference_no)
        // });

        $contact=(object)[
            'email'=>$request->email,
            'mobile'=>$request->phone
        ];
        $user=(object)[
            'name'=>$request->full_name,
            'ref_no'=>$reference_no,
            'mobile'=>$request->phone,
            'email'=>$request->email,      
        ];
        // $whatsapp_link = $this->notificationUtil->autoSendNotificationCustom(1, 'contact_us_success', $user, $contact);
        SendNotificationJob::dispatch(true, 1 , 'contact_us_success', $user, $contact);


        // Mail::to(env('ADMIN_EMAIL', 'utkarsh@phantasm.co.in'))->send(new ContactUsMail($request->all()));
        RateLimiter::hit($key, 60);
        return response()->json([
            'status' => true,
            'message' => 'Your message sent, We will get back to you shortly.'
        ]);
    }
    public function subscribe(Request $request){
        $validate = Validator::make($request->all(), [
            'email' =>'required|email|max:255',
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
        $key ='subscribe_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 1)) {
            return response()->json([
                'status' => false,
                'message' => 'You have reached the request limit. Please try again after a minute.'
            ],);
        }
        // check if email is already subscribed
        $subscriber = NewsLetterSubscriber::where('email',$request->input('email'))->first();
        if($subscriber){
            return response()->json([
                'status' => false,
                'message' => 'You are already subscribed to our newsletter.'
            ]);
        } 
       
        $location_id = $request->input('location_id', 1);
        
        // Set brand_id based on location_id condition
        $brand_id = null;
        if ($location_id != 1) {
            $brand_id = $request->input('brand_id');
        }
        
        NewsLetterSubscriber::create([
            'email' => $request->input('email'),
            'location_id' => $location_id,
            'brand_id' => $brand_id,
        ]);
        // Mail::send('emails.subscribe-newsletter', ['email' => $request->input('email')], function ($message) use ($request) {
        //     $message->to($request->input('email')); // Send the email to the user's email address
        //     $message->subject('Thank You for Subscribing!');
        // });

        $contact=(object)[
            'email'=>$request->input('email'),
            'mobile'=>""
        ];
        $user=null;

        $email = urlencode($request->input('email'));
        // $unsubscribeUrl = url('/api/customer/unsubscribe?email=' . $email);
        $default_url = config('app.front-url');
        $app_url = $default_url;
        $brand_url = null;
        if ($brand_id) {
            $brand = Brands::where('id', $brand_id)->first();
            if ($brand && $brand->brand_url) {
                $brand_url = $brand->brand_url; 
                $app_url = $brand_url;
            }
        }
        $unsubscribeUrl = $app_url . '/unsubscribe?email=' . $email;

        $contact_name = $request->input('email');
        $contact = Contact::where('email', $request->input('email'))->first();
        if ($contact && $contact->name) {
            $contact_name = $contact->name;
        }
        
        $custom_data = (object)[
            'email' => $request->input('email'),
            'name' => $contact_name,
            'unsubscribe_url' => $unsubscribeUrl
        ];
        Log::info("custom_data: ".json_encode($custom_data));
        SendNotificationJob::dispatch(true, 1 , 'subscribe_newsletter', $user, $contact, null, $custom_data);

        // Mail::to(env('ADMIN_EMAIL', 'utkarsh@phantasm.co.in'))->send(new ContactUsMail($request->all()));
        RateLimiter::hit($key, 60);
        return response()->json([
            'status' => true,
            'message' => 'Thanks for subscribing to our newsletter'
        ]);
    }
    public function unsubscribe(Request $request){
        try {
            // Handle both GET and POST requests
            $email = $request->input('email') ?? $request->query('email');
            
            $validate = Validator::make(['email' => $email], [
                'email' =>'required|email|max:255',
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
                    'message' => 'Validation failed',
                    'errors' => $formattedErrors
                ], 422);
            }
            
            $subscriber = NewsLetterSubscriber::where('email', $email)->first();
            
            if (!$subscriber) {
                //dd($subscriber,$email);
                if ($request->isMethod('get')) {
                    return response()->view('emails.unsubscribe-failed', ['message' => 'You are not subscribed to our newsletter'], 404);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You are not subscribed to our newsletter'
                ], 404);
            }
            
            // Check if already unsubscribed (if is_subscribed field exists and is false)
            if (isset($subscriber->is_subscribed) && !$subscriber->is_subscribed) {
                if ($request->isMethod('get')) {
                    return response()->view('emails.unsubscribe-success', ['message' => 'You are already unsubscribed from our newsletter']);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You are already unsubscribed from our newsletter'
                ], 200);
            }
            
            $subscriber->delete();
            
            // If GET request, return HTML page, otherwise JSON
            if ($request->isMethod('get')) {
                return response()->view('emails.unsubscribe-success', ['message' => 'You have been unsubscribed from our newsletter']);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'You have been unsubscribed from our newsletter'
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Unsubscribe newsletter error: ' . $e->getMessage(), [
                'email' => $email ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to unsubscribe. Please try again later.'
            ], 500);
        }
    }

    /**
     * Upload or update Tobacco-License document from customer profile (/profile/account-info)
     */
    public function updateTobaccoLicense(Request $request)
    {
        try {
            $authData = $this->authCheck($request);
            if (!$authData['status']) {
                return response()->json([
                    'status' => false,
                    'message' => 'No authenticated user found',
                ], 401);
            }

            $contact = $authData['user'];

            $validator = Validator::make($request->all(), [
                'Tobacco-License' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240', // max 10MB
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                $formattedErrors = [];
                foreach ($errors as $key => $errorMessages) {
                    $formattedErrors[] = [
                        'field' => $key,
                        'messages' => $errorMessages,
                    ];
                }

                return response()->json([
                    'status' => false,
                    'message' => $formattedErrors,
                ], 422);
            }

            if (!$request->hasFile('Tobacco-License')) {
                return response()->json([
                    'status' => false,
                    'message' => 'No Tobacco-License file uploaded',
                ], 400);
            }

            DB::beginTransaction();

            $file = $request->file('Tobacco-License');
            if (!$file->isValid()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Uploaded file is not valid',
                ], 400);
            }

            $timestamp = time();
            $randomNumber = rand(1000000000, 9999999999);
            $originalFileName = $file->getClientOriginalName();
            $fileName = "{$timestamp}_{$randomNumber}_{$originalFileName}";

            $destinationPath = public_path('uploads/media');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0775, true);
            }

            // Move file to the destination folder
            $file->move($destinationPath, $fileName);

            // Create document record for Tobacco-License
            $documentInput = [
                'heading' => 'Tobacco-License',
                'is_private' => true,
                'business_id' => $contact->business_id ?? 1,
                'created_by' => $contact->created_by ?? 1,
                // Approval fields default to null; ERP staff will approve via DocumentAndNoteController
                'is_approved' => null,
                'approved_by' => null,
                'approved_at' => null,
            ];

            $model = Contact::findOrFail($contact->id);
            $model_note = $model->documentsAndnote()->create($documentInput);

            Media::attachMediaToModel(
                $model_note,
                $documentInput['business_id'],
                $fileName,
                null,
                null,
                $contact->id
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Tobacco license uploaded successfully. Awaiting for approval.',
                'data' => [
                    'document_id' => $model_note->id,
                    'status' => 'pending',
                ],
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to upload Tobacco license',
                'error' => $th->getMessage(),
            ], 500);
        }
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
                'company' => 'required|string|max:100',

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

            // Update billing address if selected
            if ($request->type === 'billing') {
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
                }
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
            } else {
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
    
    public function updateCustomer(Request $request)
    {
        $user =  Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'No authenticated user found',
                ], 401);
            }
        $contact = $user;
        if(($contact->isApproved == null) || ($contact->isApproved == false) || ($contact->isApproved == 0)){
            return response()->json([
                'status'  => false,
                'message' => 'Customer is not approved',
            ], 403);
        }

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
            // Get business_id from user's location
            $businessId = $user->business_id ?? $user->location->business_id ?? null;
            
            if (!$businessId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unable to determine business ID for this user',
                ], 400);
            }
            // dd($input);
            $contactResponse = $this->contactUtil->updateContact($input, $contact->id,  $businessId);

            if (!$contactResponse['success']) {
                throw new \Exception("Failed to update contact");
            }

            $customer = $contactResponse['data'];

            DB::commit();
            if($is_email_change){
                $user = (object)[
                    'name' => $customer->name,
                    'mobile' => $customer->mobile,
                    'email' => $customer->email,
                    'remember_token' => $token,
                    'brand_id' => $customer->brand_id,
                    'contact_id' => $customer->id,
                    'ref_no' => $customer->contact_id,
                ];
                SendNotificationJob::dispatch(true, 1, 'email_confirmation', $user, $customer);
            }
            return response()->json(['status' => true, 'message' => 'Customer data updated successfully', 'customer' => $customer , 'is_email_change' => $is_email_change], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $th->getMessage() . ' at ' . $th->getLine(),], 500);
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

        // Ensure company is loaded - fetch from DB if model attribute is null (column may exist but not in model cache)
        if (Schema::hasColumn('multiple_address_customer', 'company')) {
            $companyValues = DB::table('multiple_address_customer')
                ->where('contact_id', $user->id)
                ->pluck('company', 'id');
        } else {
            $companyValues = collect();
        }

        $addresses = $addresses->map(function ($addr) use ($companyValues) {
            $company = $addr->company ?? $companyValues->get($addr->id);
            $addr->company = $company;
            $addr->company_name = $company;
            $addr->address_company = $company;
            return $addr;
        });
        $defaultAddressShipping =[
           'first_name' => $user->shipping_first_name,
            'last_name' => $user->shipping_last_name,
            'company' => $user->shipping_company,
            'address_company' => $user->shipping_company,
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
            'address_company' => $user->company,
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
            'address_company' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
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
            $companyValue = $request->input('company') ?? $request->input('company_name') ?? $request->input('address_company');

            
            if($request->address_type == 'both') {
                // Create both billing and shipping addresses
                $billingAddress = CustomerAddress::create([
                    'contact_id' => $user->id,
                    'address_label' => $request->address_label,
                    'address_type' => 'billing',
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'company' => $companyValue,
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
                    'company' => $companyValue,
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
                    'company' => $companyValue,
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

    public function getSavedAddress($id)
    {
        $user =  Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No authenticated user found',
            ], 401);
        }

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

        // Fetch company from DB directly (ensures Edit modal shows saved value when reopening)
        if (Schema::hasColumn('multiple_address_customer', 'company')) {
            $company = DB::table('multiple_address_customer')->where('id', $address->id)->value('company');
            $address->company = $company;
        }
        $address->company_name = $address->company;
        $address->address_company = $address->company;

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

        $id = $request->route('id');
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
            'address_company' => 'nullable|string|max:255', // alias for company
            'company_name' => 'nullable|string|max:255', // alias for company (common frontend field)
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
            $data = $request->only([
                'address_label',
                'address_type',
                'first_name',
                'last_name',
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'zip_code',
                'country'
            ]);
            // Company: accept company, company_name, or address_company - ensure it persists to DB
            $companyValue = $request->input('company') ?? $request->input('company_name') ?? $request->input('address_company');
            $companyToSave = ($companyValue !== null && trim((string) $companyValue) !== '') ? trim((string) $companyValue) : null;
            $data['company'] = $companyToSave;

            $address->update($data);

            // Force company update via query builder to ensure persistence
            if (Schema::hasColumn('multiple_address_customer', 'company')) {
                DB::table('multiple_address_customer')->where('id', $address->id)->update(['company' => $companyToSave]);
            }

            $fresh = $address->fresh();
            // Expose company from saved value - use $companyToSave so response reflects what we persisted
            $fresh->company = $companyToSave ?? $fresh->company;
            $fresh->company_name = $fresh->company;
            $fresh->address_company = $fresh->company;

            return response()->json([
                'status' => true,
                'message' => 'Address updated successfully',
                'data' => $fresh,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update address',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function deleteSavedAddress($id)
    {
        $user =  Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No authenticated user found',
            ], 401);
        }

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
        $id = $request->route('id');
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

    public function emailConfirmation(Request $request)
    {
        $token = $request->input('token');
        $email = $request->input('email');
        $contact = Contact::where('email', $email)->where('remember_token', $token)->first();
        if (!$contact) {
            return response()->json([
                'status' => false,
                'message' => 'Contact not found',
            ], 404);
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
         $refreshTokenCookieName ='refresh_token';
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

    /**
     * Get delivery preferences for an address
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDeliveryPreferences(Request $request)
    {
        try {
            $contact = Auth::guard('api')->user();
            if (!$contact) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Get address_id from query parameter or request body
            $addressId = $request->input('address_id') ?? $request->query('address_id');
            
            /**
             * Support special tokens for default addresses:
             * - default-shipping  => first shipping address for this contact
             * - default-billing   => first billing address for this contact
             * - default           => first address (any type) for this contact
             */
            if (is_string($addressId) && !empty($addressId)) {
                $normalized = strtolower($addressId);
                $addressQuery = null;

                if ($normalized === 'default-shipping' || $normalized === 'default_shipping') {
                    $addressQuery = CustomerAddress::where('contact_id', $contact->id)
                        ->where('address_type', 'shipping');
                } elseif ($normalized === 'default-billing' || $normalized === 'default_billing') {
                    $addressQuery = CustomerAddress::where('contact_id', $contact->id)
                        ->where('address_type', 'billing');
                } elseif ($normalized === 'default') {
                    $addressQuery = CustomerAddress::where('contact_id', $contact->id);
                }

                if ($addressQuery) {
                    $defaultAddress = $addressQuery->orderBy('id', 'asc')->first();
                    if (!$defaultAddress) {
                        return response()->json([
                            'status' => false,
                            'message' => 'No matching default address found for this customer.',
                        ], 404);
                    }
                    $addressId = $defaultAddress->id;
                }
            }

            /**
             * If address_id is still not set (no param or not a special token),
             * fall back to the customer's default address (first saved address).
             */
            if (!$addressId) {
                $defaultAddress = CustomerAddress::where('contact_id', $contact->id)
                    ->orderBy('id', 'asc')
                    ->first();

                if (!$defaultAddress) {
                return response()->json([
                    'status' => false,
                        'message' => 'address_id is required. No default address found for this customer.',
                ], 400);
                }

                $addressId = $defaultAddress->id;
            }

            // Verify address belongs to the customer
            $address = CustomerAddress::where('id', $addressId)
                ->where('contact_id', $contact->id)
                ->first();

            if (!$address) {
                // Get user's address IDs for helpful error message
                $userAddressIds = CustomerAddress::where('contact_id', $contact->id)
                    ->pluck('id')
                    ->toArray();
                
                return response()->json([
                    'status' => false,
                    'message' => 'Address not found or does not belong to you',
                    'hint' => 'Use GET /api/customer/saved-addresses to get your valid address IDs',
                    'your_address_ids' => $userAddressIds
                ], 404);
            }

            // Get or create delivery preferences
            $preferences = DeliveryPreference::firstOrCreate(
                [
                    'address_id' => $addressId,
                    'contact_id' => $contact->id,
                ],
                [
                    'delivery_times' => null,
                    'preferred_day_1' => null,
                    'preferred_day_2' => null,
                    'make_default_delivery_option' => false,
                    'drop_off_location' => 'No Preference',
                    'security_code' => null,
                    'call_box_name_or_number' => null,
                    'additional_info' => null,
                    'observed_holidays' => [],
                    'pallet_preference' => null,
                ]
            );

            // Extract general delivery hours from delivery_times
            // Use the model's accessor which handles the array cast automatically
            $deliveryTimes = $preferences->delivery_times;
            
            // Handle null or empty delivery_times
            if ($deliveryTimes === null || (is_string($deliveryTimes) && trim($deliveryTimes) === '')) {
                $deliveryTimes = [];
            }
            
            // If Laravel's array cast returned null, try getting raw and decoding
            if ($deliveryTimes === null) {
                $raw = $preferences->getRawOriginal('delivery_times');
                if ($raw !== null && $raw !== 'null') {
                    $decoded = json_decode($raw, true);
                    $deliveryTimes = is_array($decoded) ? $decoded : [];
                } else {
                    $deliveryTimes = [];
                }
            }
            
            // Ensure it's an array
            if (!is_array($deliveryTimes)) {
                $deliveryTimes = [];
            }
            
            // Extract general from delivery_times
            $general = null;
            if (!empty($deliveryTimes) && isset($deliveryTimes['general'])) {
                if (is_array($deliveryTimes['general'])) {
                    $general = $deliveryTimes['general'];
                } elseif (is_object($deliveryTimes['general'])) {
                    // Handle stdClass objects (from JSON decode)
                    $general = (array) $deliveryTimes['general'];
                } else {
                    // Handle scalar values (shouldn't happen normally)
                    $general = ['value' => $deliveryTimes['general']];
                }
                // Remove general from delivery_times to avoid duplication
                unset($deliveryTimes['general']);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'address_id' => $address->id,
                    'address' => [
                        'name' => trim(($address->first_name ?? '') . ' ' . ($address->last_name ?? '')),
                        'address_line_1' => $address->address_line_1,
                        'address_line_2' => $address->address_line_2,
                        'city' => $address->city,
                        'state' => $address->state,
                        'zip_code' => $address->zip_code,
                        'country' => $address->country,
                        'full_address' => $address->full_address,
                    ],
                    'delivery_preferences' => [
                        'general' => $general,
                        'delivery_times' => $deliveryTimes,
                        'preferred_day_1' => $preferences->preferred_day_1,
                        'preferred_day_2' => $preferences->preferred_day_2,
                        'make_default_delivery_option' => $preferences->make_default_delivery_option,
                        'drop_off_location' => $preferences->drop_off_location,
                        'security_code' => $preferences->security_code,
                        'call_box_name_or_number' => $preferences->call_box_name_or_number,
                        'additional_info' => $preferences->additional_info,
                        'observed_holidays' => $preferences->observed_holidays ?? [],
                        'custom_holidays' => $preferences->custom_holidays ?? [],
                        'pallet_preference' => $preferences->pallet_preference,
                    ]
                ]
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching delivery preferences',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update delivery preferences for an address
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
      /**
     * Update delivery preferences for an address
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDeliveryPreferences(Request $request)
    {
        try {
            $contact = Auth::guard('api')->user();
            if (!$contact) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Resolve address_id (allow default tokens or missing -> use first/default address)
            $addressId = $request->input('address_id') ?? $request->query('address_id') ?? $request->json('address_id');
            if (!$addressId || in_array($addressId, ['default', 'default-shipping', 'default-billing'])) {
                $query = CustomerAddress::where('contact_id', $contact->id);
                if ($addressId === 'default-shipping') {
                    $query->where('address_type', 'shipping');
                } elseif ($addressId === 'default-billing') {
                    $query->where('address_type', 'billing');
                }
                $defaultAddress = $query->orderBy('id', 'asc')->first();
                if (!$defaultAddress) {
                    return response()->json([
                        'status' => false,
                        'message' => 'address_id is required. No default address found for this customer.'
                    ], 400);
                }
                $addressId = $defaultAddress->id;
            }

            $address = CustomerAddress::where('id', $addressId)
                ->where('contact_id', $contact->id)
                ->first();

            if (!$address) {
                return response()->json([
                    'status' => false,
                    'message' => 'The address does not exist or does not belong to you.'
                ], 404);
            }

            // Validate request (address_id now optional because we resolved it)
            $validated = $request->validate([
                'address_id' => 'nullable|integer',
                // Delivery Times - JSON object with days as keys (time format validated manually)
                'delivery_times' => 'nullable|array',
                // General delivery hours (Monday-Sunday default) - can be sent as separate object
                'general' => 'nullable|array',
                // General delivery hours (Monday-Sunday default)
                'general_start_at' => 'nullable|string',
                'general_stop_at' => 'nullable|string',
                
                // Smokevana Day preferences
                'preferred_day_1' => 'nullable|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday,None',
                'preferred_day_2' => 'nullable|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday,None',
                'make_default_delivery_option' => 'nullable|boolean',
                
                // Delivery Instructions
                'drop_off_location' => 'nullable|string|in:Front Desk,Loading Dock,Mail Room,In-Suite Reception,Front Door,No Preference',
                'security_code' => 'nullable|string|max:50',
                'call_box_name_or_number' => 'nullable|string|max:100',
                'additional_info' => 'nullable|string|max:1000',
                
                // Observed Holidays (predefined)
                'observed_holidays' => 'nullable|array',
                'observed_holidays.*' => 'string|in:New Year\'s Day,Martin Luther King Jr. Day,George Washington\'s Birthday,Memorial Day,Juneteenth Independence Day,Independence Day,Labor Day,Columbus Day,Veterans Day,Thanksgiving Day,Christmas Day',
                
                // Custom Holidays (date ranges, up to 7 days each)
                'custom_holidays' => 'nullable|array',
                'custom_holidays.*.start_date' => 'required_with:custom_holidays.*|date|date_format:Y-m-d',
                'custom_holidays.*.end_date' => 'required_with:custom_holidays.*|date|date_format:Y-m-d|after_or_equal:custom_holidays.*.start_date',
                'custom_holidays.*.name' => 'nullable|string|max:255',
                
                // Pallet Preference
                'pallet_preference' => 'nullable|array',
            ]);

            // Manually validate delivery_times time formats if provided
            if ($request->has('delivery_times') && is_array($request->input('delivery_times'))) {
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                $timePattern = '/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/';
                
                foreach ($days as $day) {
                    if (isset($validated['delivery_times'][$day])) {
                        $dayData = $validated['delivery_times'][$day];
                        if (isset($dayData['start']) && !preg_match($timePattern, $dayData['start'])) {
                            return response()->json([
                                'status' => false,
                                'message' => "Invalid time format for {$day}.start. Use HH:MM format (e.g., 09:00)"
                            ], 422);
                        }
                        if (isset($dayData['stop']) && !preg_match($timePattern, $dayData['stop'])) {
                            return response()->json([
                                'status' => false,
                                'message' => "Invalid time format for {$day}.stop. Use HH:MM format (e.g., 17:00)"
                            ], 422);
                        }
                    }
                }
            }

            // Prepare delivery_times structure
            $deliveryTimes = $validated['delivery_times'] ?? [];
            
            // Handle general/default delivery hours (Monday-Sunday)
            // Check if 'general' is sent as a separate top-level field (as shown in the request JSON)
            $general = null;
            
            // First check if general is in validated data
            if (isset($validated['general']) && is_array($validated['general'])) {
                $general = $validated['general'];
            } else {
                // Check request directly (supports both JSON and form data)
                $general = $request->input('general') ?? $request->json('general');
            }
            
            // If not found as object, check for individual general fields
            if (!$general) {
                // Check multiple possible field names the frontend might use
                $generalStart = $validated['general_start_at'] ?? null;
                $generalStop = $validated['general_stop_at'] ?? null;
                
                // If not in validated, check request directly (supports both JSON and form data)
                if (!$generalStart) {
                    $generalStart = $request->input('general_start_at') ?? 
                                   $request->json('general_start_at') ?? 
                                   $request->input('delivery_hours_start') ??
                                   $request->json('delivery_hours_start') ??
                                   $request->input('default_start_at') ??
                                   $request->json('default_start_at');
                }
                if (!$generalStop) {
                    $generalStop = $request->input('general_stop_at') ?? 
                                  $request->json('general_stop_at') ?? 
                                  $request->input('delivery_hours_stop') ??
                                  $request->json('delivery_hours_stop') ??
                                  $request->input('default_stop_at') ??
                                  $request->json('default_stop_at');
                }
                
                // If we have start/stop values, create general object
                if ($generalStart || $generalStop) {
                    $general = [
                        'start_at' => $generalStart,
                        'stop_at' => $generalStop,
                    ];
                }
            }
            
            // Merge general into delivery_times if it exists
            if ($general && is_array($general)) {
                // If general already exists in delivery_times, merge it
                if (isset($deliveryTimes['general']) && is_array($deliveryTimes['general'])) {
                    $deliveryTimes['general'] = array_merge($deliveryTimes['general'], $general);
                } else {
                    // Add general to delivery_times
                    $deliveryTimes['general'] = $general;
                }
            }

            // Update or create delivery preferences
            $preferences = DeliveryPreference::updateOrCreate(
                [
                    'address_id' => $addressId,
                    'contact_id' => $contact->id,
                ],
                [
                    'delivery_times' => !empty($deliveryTimes) ? $deliveryTimes : null,
                    'preferred_day_1' => $validated['preferred_day_1'] ?? null,
                    'preferred_day_2' => $validated['preferred_day_2'] ?? null,
                    'make_default_delivery_option' => $validated['make_default_delivery_option'] ?? false,
                    'drop_off_location' => $validated['drop_off_location'] ?? 'No Preference',
                    'security_code' => $validated['security_code'] ?? null,
                    'call_box_name_or_number' => $validated['call_box_name_or_number'] ?? null,
                    'additional_info' => $validated['additional_info'] ?? null,
                    'observed_holidays' => $validated['observed_holidays'] ?? [],
                    'custom_holidays' => $this->processCustomHolidays($validated['custom_holidays'] ?? []),
                    'pallet_preference' => $validated['pallet_preference'] ?? null,
                ]
            );

            // Extract general delivery hours from delivery_times for response
            $responseDeliveryTimes = $preferences->delivery_times;
            
            // Handle null or empty delivery_times
            if ($responseDeliveryTimes === null || (is_string($responseDeliveryTimes) && trim($responseDeliveryTimes) === '')) {
                $responseDeliveryTimes = [];
            }
            
            // If Laravel's array cast returned null, try getting raw and decoding
            if ($responseDeliveryTimes === null) {
                $raw = $preferences->getRawOriginal('delivery_times');
                if ($raw !== null && $raw !== 'null') {
                    $decoded = json_decode($raw, true);
                    $responseDeliveryTimes = is_array($decoded) ? $decoded : [];
                } else {
                    $responseDeliveryTimes = [];
                }
            }
            
            // Ensure it's an array
            if (!is_array($responseDeliveryTimes)) {
                $responseDeliveryTimes = [];
            }
            
            // Extract general from delivery_times
            $general = null;
            if (!empty($responseDeliveryTimes) && isset($responseDeliveryTimes['general'])) {
                if (is_array($responseDeliveryTimes['general'])) {
                    $general = $responseDeliveryTimes['general'];
                } elseif (is_object($responseDeliveryTimes['general'])) {
                    // Handle stdClass objects (from JSON decode)
                    $general = (array) $responseDeliveryTimes['general'];
                } else {
                    // Handle scalar values (shouldn't happen normally)
                    $general = ['value' => $responseDeliveryTimes['general']];
                }
                // Remove general from delivery_times to avoid duplication
                unset($responseDeliveryTimes['general']);
            }

            return response()->json([
                'status' => true,
                'message' => 'Delivery preferences updated successfully',
                'data' => [
                    'address_id' => $address->id,
                    'address' => [
                        'name' => trim(($address->first_name ?? '') . ' ' . ($address->last_name ?? '')),
                        'address_line_1' => $address->address_line_1,
                        'address_line_2' => $address->address_line_2,
                        'city' => $address->city,
                        'state' => $address->state,
                        'zip_code' => $address->zip_code,
                        'country' => $address->country,
                        'full_address' => $address->full_address,
                    ],
                    'delivery_preferences' => [
                        'general' => $general,
                        'delivery_times' => $responseDeliveryTimes,
                        'preferred_day_1' => $preferences->preferred_day_1,
                        'preferred_day_2' => $preferences->preferred_day_2,
                        'make_default_delivery_option' => $preferences->make_default_delivery_option,
                        'drop_off_location' => $preferences->drop_off_location,
                        'security_code' => $preferences->security_code,
                        'call_box_name_or_number' => $preferences->call_box_name_or_number,
                        'additional_info' => $preferences->additional_info,
                        'observed_holidays' => $preferences->observed_holidays ?? [],
                        'custom_holidays' => $preferences->custom_holidays ?? [],
                        'pallet_preference' => $preferences->pallet_preference,
                    ]
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error updating delivery preferences',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    
    /**
     * Add idea list. Accepts form data (x-www-form-urlencoded, multipart/form-data) or JSON.
     * Fields: idea_title, idea_description, idea_products_list.
     */
    public function addIdeaList(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No authenticated user found',
            ], 401);
        }

        $input = $request->only('idea_title', 'idea_description', 'idea_products_list');
        $validate = Validator::make($input, [
            'idea_title' => 'required|string|max:255',
            'idea_description' => 'required|string',
            'idea_products_list' => 'required|string',
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

        $contact = Contact::find($user->id);
        if (!$contact) {
            return response()->json([
                'status' => false,
                'message' => 'Contact not found',
            ], 404);
        }

        try {
            $contact->idea_title = $input['idea_title'];
            $contact->idea_description = $input['idea_description'];
            $contact->idea_products_list = $input['idea_products_list'];
            $contact->save();

            return response()->json(['status' => true, 'message' => 'Idea list added successfully'], 201);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage() . ' at ' . $th->getLine()], 500);
        }
    }

    public function getIdeaList(Request $request)
    {
        $user =  Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No authenticated user found',
            ], 401);
        }

        $contact = Contact::where('id', $user->id)->select('id','idea_title', 'idea_description', 'idea_products_list')->first();
        if (!$contact) {
            return response()->json([
                'status' => false,
                'message' => 'Contact not found',
            ], 404);
        }

        // Parse the idea_products_list and search for products
        $products = [];
        if ($contact->idea_products_list) {
            // Handle JSON array or comma-separated string
            $productList = [];
            
            if (is_string($contact->idea_products_list)) {
                // Try to decode as JSON array first
                $decoded = json_decode($contact->idea_products_list, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $productList = $decoded;
                } else {
                    // Fall back to comma-separated string
                    $productList = array_filter(array_map('trim', explode(',', $contact->idea_products_list)));
                }
            } elseif (is_array($contact->idea_products_list)) {
                $productList = $contact->idea_products_list;
            }
            
            if (!empty($productList)) {
                // Separate numeric IDs from names
                $numericIds = array_filter($productList, function($item) {
                    return is_numeric($item);
                });
                
                $names = array_filter($productList, function($item) {
                    return !is_numeric($item);
                });
                
                $query = Product::query();
                
                // Search by numeric IDs if available
                if (!empty($numericIds)) {
                    $query->whereIn('id', $numericIds);
                }
                
                // If names exist, use orWhere to include them
                if (!empty($names)) {
                    $query->orWhereIn('name', $names);
                }
                
                $products = ProductResource::collection($query->get());
            }
        }

        return response()->json([
            'status' => true,
            'data' => [
                'idea_title' => $contact->idea_title,
                'idea_description' => $contact->idea_description,
                'idea_products_list' => $contact->idea_products_list,
                'products' => $products,
            ],
        ], 200);
    }
  /**
     * Store delivery preferences for an address (POST)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeDeliveryPreferences(Request $request)
    {
        try {
            $contact = Auth::guard('api')->user();
            if (!$contact) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Get address_id from request (supports both JSON and form data)
            $addressId = $request->input('address_id') ?? $request->json('address_id');
            
            /**
             * If address_id is not explicitly provided, fall back to the
             * customer's "default" address. For now we treat the first
             * saved address for the contact as the default.
             */
            if (!$addressId) {
                $defaultAddress = CustomerAddress::where('contact_id', $contact->id)
                    ->orderBy('id', 'asc')
                    ->first();

                if (!$defaultAddress) {
                return response()->json([
                    'status' => false,
                        'message' => 'No saved address found for this customer. Please provide address_id in the request body.',
                    'received_data' => $request->all(),
                    'method' => $request->method()
                ], 400);
                }

                $addressId = $defaultAddress->id;
                // Merge back into the request so validation sees it as present
                $request->merge(['address_id' => $addressId]);
            }

            // Validate request
            $validated = $request->validate([
                'address_id' => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) use ($contact) {
                        $address = CustomerAddress::where('id', $value)
                            ->where('contact_id', $contact->id)
                            ->first();
                        if (!$address) {
                            $fail('The address does not exist or does not belong to you.');
                        }
                    },
                ],
                // Delivery Times - JSON object with days as keys (time format validated manually)
                'delivery_times' => 'nullable|array',
                // General delivery hours (Monday-Sunday default) - can be sent as separate object
                'general' => 'nullable|array',
                // Alternative field names for general hours
                'general_start_at' => 'nullable|string',
                'general_stop_at' => 'nullable|string',
                
                // Smokevana Day preferences
                'preferred_day_1' => 'nullable|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday,None',
                'preferred_day_2' => 'nullable|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday,None',
                'make_default_delivery_option' => 'nullable|boolean',
                
                // Delivery Instructions
                'drop_off_location' => 'nullable|string|in:Front Desk,Loading Dock,Mail Room,In-Suite Reception,Front Door,No Preference',
                'security_code' => 'nullable|string|max:50',
                'call_box_name_or_number' => 'nullable|string|max:100',
                'additional_info' => 'nullable|string|max:1000',
                
                // Observed Holidays (predefined)
                'observed_holidays' => 'nullable|array',
                'observed_holidays.*' => 'string|in:New Year\'s Day,Martin Luther King Jr. Day,George Washington\'s Birthday,Memorial Day,Juneteenth Independence Day,Independence Day,Labor Day,Columbus Day,Veterans Day,Thanksgiving Day,Christmas Day',
                
                // Custom Holidays (date ranges, up to 7 days each)
                'custom_holidays' => 'nullable|array',
                'custom_holidays.*.start_date' => 'required_with:custom_holidays.*|date|date_format:Y-m-d',
                'custom_holidays.*.end_date' => 'required_with:custom_holidays.*|date|date_format:Y-m-d|after_or_equal:custom_holidays.*.start_date',
                'custom_holidays.*.name' => 'nullable|string|max:255',
                
                // Pallet Preference
                'pallet_preference' => 'nullable|array',
            ]);

            // Get address after validation (guaranteed to exist & belong to contact)
            $addressId = $validated['address_id'];
            $address = CustomerAddress::where('id', $addressId)
                ->where('contact_id', $contact->id)
                ->first();

            // Prepare delivery_times structure
            $deliveryTimes = $validated['delivery_times'] ?? [];
            
            // Handle general/default delivery hours (Monday-Sunday)
            // Check if 'general' is sent as a separate top-level field (as shown in the request JSON)
            $general = null;
            
            // First check if general is in validated data
            if (isset($validated['general']) && is_array($validated['general'])) {
                $general = $validated['general'];
            } else {
                // Check request directly (supports both JSON and form data)
                $general = $request->input('general') ?? $request->json('general');
            }
            
            // If not found as object, check for individual general fields
            if (!$general) {
                // Check multiple possible field names the frontend might use
                $generalStart = $validated['general_start_at'] ?? null;
                $generalStop = $validated['general_stop_at'] ?? null;
                
                // If not in validated, check request directly (supports both JSON and form data)
                if (!$generalStart) {
                    $generalStart = $request->input('general_start_at') ?? 
                                   $request->json('general_start_at') ?? 
                                   $request->input('delivery_hours_start') ??
                                   $request->json('delivery_hours_start') ??
                                   $request->input('default_start_at') ??
                                   $request->json('default_start_at');
                }
                if (!$generalStop) {
                    $generalStop = $request->input('general_stop_at') ?? 
                                  $request->json('general_stop_at') ?? 
                                  $request->input('delivery_hours_stop') ??
                                  $request->json('delivery_hours_stop') ??
                                  $request->input('default_stop_at') ??
                                  $request->json('default_stop_at');
                }
                
                // If we have start/stop values, create general object
                if ($generalStart || $generalStop) {
                    $general = [
                        'start_at' => $generalStart,
                        'stop_at' => $generalStop,
                    ];
                }
            }
            
            // Merge general into delivery_times if it exists
            if ($general && is_array($general)) {
                // If general already exists in delivery_times, merge it
                if (isset($deliveryTimes['general']) && is_array($deliveryTimes['general'])) {
                    $deliveryTimes['general'] = array_merge($deliveryTimes['general'], $general);
                } else {
                    // Add general to delivery_times
                    $deliveryTimes['general'] = $general;
                }
            }

            // Update or create delivery preferences
            $preferences = DeliveryPreference::updateOrCreate(
                [
                    'address_id' => $addressId,
                    'contact_id' => $contact->id,
                ],
                [
                    'delivery_times' => !empty($deliveryTimes) ? $deliveryTimes : null,
                    'preferred_day_1' => $validated['preferred_day_1'] ?? null,
                    'preferred_day_2' => $validated['preferred_day_2'] ?? null,
                    'make_default_delivery_option' => $validated['make_default_delivery_option'] ?? false,
                    'drop_off_location' => $validated['drop_off_location'] ?? 'No Preference',
                    'security_code' => $validated['security_code'] ?? null,
                    'call_box_name_or_number' => $validated['call_box_name_or_number'] ?? null,
                    'additional_info' => $validated['additional_info'] ?? null,
                    'observed_holidays' => $validated['observed_holidays'] ?? [],
                    'custom_holidays' => $this->processCustomHolidays($validated['custom_holidays'] ?? []),
                    'pallet_preference' => $validated['pallet_preference'] ?? null,
                ]
            );

            // Normalize delivery_times and extract general for the response
            $responseDeliveryTimes = $preferences->delivery_times;
            if ($responseDeliveryTimes === null || (is_string($responseDeliveryTimes) && trim($responseDeliveryTimes) === '')) {
                $responseDeliveryTimes = [];
            }
            if ($responseDeliveryTimes === null) {
                $raw = $preferences->getRawOriginal('delivery_times');
                if ($raw !== null && $raw !== 'null') {
                    $decoded = json_decode($raw, true);
                    $responseDeliveryTimes = is_array($decoded) ? $decoded : [];
                } else {
                    $responseDeliveryTimes = [];
                }
            }
            if (!is_array($responseDeliveryTimes)) {
                $responseDeliveryTimes = [];
            }

            $responseGeneral = null;
            if (!empty($responseDeliveryTimes) && isset($responseDeliveryTimes['general'])) {
                if (is_array($responseDeliveryTimes['general'])) {
                    $responseGeneral = $responseDeliveryTimes['general'];
                } elseif (is_object($responseDeliveryTimes['general'])) {
                    $responseGeneral = (array) $responseDeliveryTimes['general'];
                } else {
                    $responseGeneral = ['value' => $responseDeliveryTimes['general']];
                }
                unset($responseDeliveryTimes['general']);
            }

            return response()->json([
                'status' => true,
                'message' => 'Delivery preferences saved successfully',
                'data' => [
                    'address_id' => $address->id,
                    'address' => [
                        'name' => trim(($address->first_name ?? '') . ' ' . ($address->last_name ?? '')),
                        'address_line_1' => $address->address_line_1,
                        'address_line_2' => $address->address_line_2,
                        'city' => $address->city,
                        'state' => $address->state,
                        'zip_code' => $address->zip_code,
                        'country' => $address->country,
                        'full_address' => $address->full_address,
                    ],
                    'delivery_preferences' => [
                        'general' => $responseGeneral,
                        'delivery_times' => $responseDeliveryTimes,
                        'preferred_day_1' => $preferences->preferred_day_1,
                        'preferred_day_2' => $preferences->preferred_day_2,
                        'make_default_delivery_option' => $preferences->make_default_delivery_option,
                        'drop_off_location' => $preferences->drop_off_location,
                        'security_code' => $preferences->security_code,
                        'call_box_name_or_number' => $preferences->call_box_name_or_number,
                        'additional_info' => $preferences->additional_info,
                        'observed_holidays' => $preferences->observed_holidays,
                        'custom_holidays' => $preferences->custom_holidays,
                        'pallet_preference' => $preferences->pallet_preference,
                    ]
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error saving delivery preferences',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    private function processCustomHolidays($customHolidays)
    {
        if (empty($customHolidays) || !is_array($customHolidays)) {
            return [];
        }

        $processed = [];
        foreach ($customHolidays as $holiday) {
            if (!isset($holiday['start_date']) || !isset($holiday['end_date'])) {
                continue; // Skip invalid entries
            }

            $startDate = \Carbon\Carbon::parse($holiday['start_date']);
            $endDate = \Carbon\Carbon::parse($holiday['end_date']);

            // Ensure end_date is not more than 7 days from start_date
            if ($endDate->diffInDays($startDate) > 7) {
                $endDate = $startDate->copy()->addDays(7);
            }

            $processed[] = [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'name' => $holiday['name'] ?? null,
            ];
        }

        return $processed;
    }
}
