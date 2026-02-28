<?php

namespace App\Http\Controllers\B2CECOM;

use App\Cart;
use App\CartItem;
use App\GuestCartItem;
use App\Contact;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ECOM\PaymentOrderController;
use App\Http\Controllers\TransactionPaymentController;
use App\Jobs\WooCommerceWebhookSaleOrder;
use App\LocationTaxCharge;
use App\Models\CustomDiscount;
use App\Models\ElevenLabsSessionModel;
use App\PaymentBuffer;
use App\Product;
use App\Services\CustomDiscountRuleService;
use App\Utils\TransactionUtil;
use App\Utils\ModuleUtil;
use App\Http\Controllers\B2CECOM\B2CPaymentOrderHelper;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Woocommerce\Exceptions\WooCommerceError;
use App\Jobs\SendNotificationJob;

class B2CPaymentOrderController extends Controller
{
    private $paymentOrderController;

    public function __construct()
    {
        $this->paymentOrderController = new PaymentOrderController();
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

    /**
     * @description: B2C Sales Order Creation by API Request
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processOrder(Request $request)
    {
        // dd($request->all());
        $currentTime = now();

        // Check if this is a guest order or customer order
        $isGuestOrder = $request->attributes->get('is_guest_request', false);
        if ($isGuestOrder) {
            return $this->processGuestOrder($request, $currentTime);
        } else {
            return $this->processCustomerOrder($request, $currentTime);
        }
    }

    /**
     * Process order for authenticated customers
     */
    private function processCustomerOrder(Request $request, $currentTime)
    {
        $cid = $request->query('cid');
        if ($cid) {
            if (
                !auth()->user() ||
                (!auth()->user()->can('sell.update') &&
                !auth()->user()->can('direct_sell.access') &&
                !auth()->user()->can('so.update') &&
                !auth()->user()->can('edit_pos_payment'))
            ) {
                abort(403, 'Unauthorized action.');
            }

            $userId = $cid;
            $contact = Contact::find($userId);
            if (!$contact) {
                return response()->json(['status' => false, 'message' => 'Customer not found.']);
            }
        } else {
            if($request->query('elevenlabs_conversation_id')){
                $token_exists = ElevenLabsSessionModel::where('conversation_id', $request->query('elevenlabs_conversation_id'))->first();
                if($token_exists){
                    $request->headers->set('Authorization', 'Bearer ' . $token_exists->token);
                }
            }
            $authData = $this->authCheck($request);
            if (!$authData['status']) {
                return response()->json(['status' => false, 'message' => 'Unauthorized user.']);
            }
            $contact = $authData['user'];
            $userId = $contact->id;
        }

        // B2C customers: get price group if exists, otherwise use null
        $priceTier = $contact->price_tier;
        $priceGroupId = key($priceTier);
        // For B2C customers without a group, priceGroupId is 0, set it to null
        $priceGroupId = ($priceGroupId === 0) ? null : $priceGroupId;
        
        // B2C specific settings
        $business_id = 1;
        $location_id = $request->query('location_id')??2;
        $shipping_charges = 15.00;
        $shippingAddressLine = $contact->shipping_address;
        $final_total = 0;
        $payedAmount = 0;
        $selling_price_group_id = $priceGroupId;
        
        $validate = Validator::make($request->all(), [
            'paymentType' => 'required|string',
            'shippingType' => 'required|string',
            'nonce' => 'nullable|string',
            'is_order' => 'nullable|boolean'
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
        
        $paytype = $request->input('paymentType');
        $shippingType = $request->input('shippingType');
        $nonce = $request->input('nonce') ?? null;
        $isOrder = $request->input('is_order', true); // Default to true for order creation
        
        // Store is_order in request attributes so discount service can access it
        $request->attributes->set('is_order', $isOrder);
        
        $card = ($paytype == 'card') ? 1 : 0;

        $appliedDiscountBucket = [];
        DB::beginTransaction();
        
        try {
            // BUG FIX #4: Lock cart to prevent race conditions
            $cart = Cart::where('user_id', $userId)->lockForUpdate()->first();
            
            if (!$cart) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Cart not found. Please add items to your cart and complete the checkout steps (address & freeze) before placing your order.',
                    'reason' => 'cart_address_not_found',
                ]);
            }
            
            if (!$cart->isFreeze) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Stock got updated for few items during checkout. Please try again.','reason' => 'stock_not_reserved']);
            }
            
            $newValue = (new TransactionUtil())->getInvoiceNumber($business_id, null, $location_id, null, 'sales_order');
            $transaction = DB::table('transactions')->insertGetId([
                'business_id' => $business_id ?? 1,
                'location_id' => $location_id ?? 2,
                'contact_id' => $userId,
                'type' => 'sales_order',
                "status" => "ordered",
                'payment_status' => $card ? "paid" : "partial",
                "customer_group_id" => $priceGroupId,
                "invoice_no" => $newValue,
                "total_before_tax" => '',
                "discount_type" => null,
                'transaction_date' => now(),
                'final_total' => $final_total,
                'shipping_address' => $cart->shipping_address1 . ' ' . $cart->shipping_address2 . ' ' . $cart->shipping_city . ' ' . $cart->shipping_state . ' ' . $cart->shipping_zip . ' ' . $cart->shipping_country ?? $shippingAddressLine,
                'is_direct_sale' => 1,
                'selling_price_group_id' => $selling_price_group_id,
                'recur_interval' => 1.000,
                'recur_interval_type' => 'days',
                'recur_repetitions' => 0,
                "shipping_charges" => 0.00,
                'additional_notes' => 'B2C Web Order',
                'created_by' => "1",
                'created_at' => now(),
                'updated_at' => now(),
                'shipping_first_name' => $cart->shipping_first_name ?? null,
                'shipping_last_name' => $cart->shipping_last_name ?? null,
                'shipping_company' => $cart->shipping_company ?? null,
                'shipping_address1' => $cart->shipping_address1 ?? null,
                'shipping_address2' => $cart->shipping_address2 ?? null,
                'shipping_city' => $cart->shipping_city ?? null,
                'shipping_state' => $cart->shipping_state ?? null,
                'shipping_zip' => $cart->shipping_zip ?? null,
                'shipping_country' => $cart->shipping_country ?? 'US',
                'billing_first_name' => $cart->billing_first_name ?? null,
                'billing_last_name' => $cart->billing_last_name ?? null,
                'billing_company' => $cart->billing_company ?? null,
                'billing_address1' => $cart->billing_address1 ?? null,
                'billing_address2' => $cart->billing_address2 ?? null,
                'billing_city' => $cart->billing_city ?? null,
                'billing_state' => $cart->billing_state ?? null,
                'billing_zip' => $cart->billing_zip ?? null,
                'billing_country' => $cart->billing_country ?? 'US',
                'billing_phone' => $cart->billing_phone ?? null,
                'billing_email' => $cart->billing_email ?? null,
                
            ]);

            // Use the same cart processing logic as B2B but with B2C pricing
            $cartUtil = new \App\Http\Controllers\B2CECOM\UnifiedCartController();
            $cartItemGet = $cartUtil->getCartItems($userId);
            if($cartItemGet['status'] == false){
                return response()->json(['status' => false, 'message' => $cartItemGet['message']]);
            }
            $cartItems = $cartItemGet['data'];
            $productIds = $cartItems->pluck('product_id');
            
            // For B2C, we need to get products with B2C pricing (sell_price_inc_tax)
            $products = B2CPaymentOrderHelper::getB2CProductsWithRelations($productIds, $userId, $priceGroupId, $location_id);
            
            // Get discounts service 
            $discountService = new CustomDiscountRuleService();
            $discounts = $discountService->getActiveDiscounts($contact, $contact->location_id, $contact->brand_id);
            $appliedDiscounts = $cart->applied_discounts ?? [];

            // Pre-fetch location tax charges
            $userState = $cart->shipping_state ?? $contact->shipping_state;
            $taxCharges = $cartUtil->getTaxCharges($userState);

            // Process cart items with B2C pricing
            $sellLinesData = B2CPaymentOrderHelper::processCartItemsForB2C($cartItems, $products, $transaction, $userId, $currentTime, $cartUtil, $discountService, $discounts, $appliedDiscounts, $taxCharges, $userState, $appliedDiscountBucket, $contact->location_id, $contact->brand_id);

            // Insert sell lines
            DB::table('transaction_sell_lines')->insert($sellLinesData);

            // Increment top_selling count for each product in the order
            $productIds = collect($sellLinesData)->pluck('product_id')->unique();
            foreach ($productIds as $productId) {
                DB::table('products')
                    ->where('id', $productId)
                    ->increment('top_selling');
            }

            // Calculate final total with shipping (now returns array with shipping details)
            $totalsData = B2CPaymentOrderHelper::calculateFinalTotal($cartItems, $products, $cartUtil, $discountService, $discounts, $appliedDiscounts, $taxCharges, $userState, $shipping_charges, $shippingType, $userId, $contact->location_id, $contact->brand_id);
            
            $final_total = $totalsData['final_total'];
            $actualShippingCharges = $totalsData['shipping_charges'];
            $freeShippingDiscount = $totalsData['free_shipping_discount'];
            $cartDiscountAmount = $totalsData['cart_discount_amount'];

            // Track free shipping discount in applied discounts
            if ($freeShippingDiscount > -1) {
                $appliedDiscountBucket[] = "Free Shipping Discount: $" . number_format($freeShippingDiscount, 2) . " off (Final Shipping: $" . number_format($actualShippingCharges, 2) . ")";
            }

            // Track cart-level discount
            if ($cartDiscountAmount > 0) {
                $appliedDiscountBucket[] = "Cart Discount: $" . number_format($cartDiscountAmount, 2) . " off";
            }

            // Process payment
            if ($card) {
                $payedAmount = $final_total;
                $paymentResult = B2CPaymentOrderHelper::processPayment($nonce, $payedAmount, $newValue, $userId, $cart, $transaction, $this->paymentOrderController);
                if (!$paymentResult['status']) {
                    // BUG FIX #3: On payment failure, rollback transaction but keep cart frozen
                    // The UnfreezeCart job will automatically unfreeze and restore stock after timeout
                    DB::rollBack();
                    return response()->json($paymentResult);
                }
            } else {
                $payedAmount = 0;
            }

            // Insert Payment
            if ($payedAmount > 0) {
                B2CPaymentOrderHelper::insertPayment($transaction, $business_id, $payedAmount, $card, $newValue, $final_total);
            }

            // Update transaction
            DB::table('transactions')->where('id', $transaction)->update([
                'final_total' => $final_total,
                'discount_type' => $totalsData['discount_type']??null,
                'shipping_charges' => $actualShippingCharges,
                'total_before_tax' => B2CPaymentOrderHelper::calculateTotalBeforeTax($sellLinesData),
                'discount_amount' => $cartDiscountAmount,
                'additional_notes' => 'B2C Web Order <br> Applied Discount: '. implode('<br>', $appliedDiscountBucket),
                'updated_at' => now(),
            ]);

            
            // Mark referral coupons as used
            if (!empty($appliedDiscounts)) {
                foreach ($appliedDiscounts as $couponCode) {
                    // Check if this is a referral coupon
                    $referralCoupon = \App\Models\EcomReferalProgram::where('coupon_code', $couponCode)
                        ->where('customer_id', $userId)
                        ->where('is_used', 0)
                        ->first();
                    
                    if ($referralCoupon) {
                        $referralCoupon->is_used = 1;
                        $referralCoupon->used_at = now();
                        $referralCoupon->save();
                        
                        \Log::info('[OrderReferralCoupon] Marked referral coupon as used', [
                            'coupon_code' => $couponCode,
                            'customer_id' => $userId,
                            'transaction_id' => $transaction
                        ]);
                    }
                }
            }
            
            // Clean up cart
            $cart->delete();
            CartItem::where('user_id', $userId)->delete();
            
            DB::commit();

            $data = Transaction::with([
                'payment_lines'=> function($query){
                    $query->select('id','amount', 'paid_on','payment_ref_no','transaction_no','method');
                },
                'sell_lines' => function($query){
                    $query->select('id','transaction_id','product_id','variation_id','ordered_quantity','verified_qty','picked_quantity','unit_price','unit_price_before_discount','unit_price_inc_tax','item_tax','line_discount_type','line_discount_amount');
                },
            ])->where('contact_id', $userId)
            //   ->where('business_id', $business_id)
              ->where('id', $transaction)
              ->select('id','business_id','type','status','final_total','invoice_no','transaction_date','payment_status','location_id','billing_first_name','billing_last_name','billing_company','billing_address1','billing_address2','billing_city','billing_state','billing_country','billing_zip','billing_phone','billing_email','shipping_first_name','shipping_last_name','shipping_company','shipping_address1','shipping_address2','shipping_city','shipping_state','shipping_country','shipping_zip','discount_type','discount_amount','shipping_charges');
            
            //   mail send to customer based on billing info 
            
            $transaction_data = Transaction::find($transaction)->first();
            $custom_data = (object) [
                'contact_id' => $userId,
                'transaction' => $transaction_data,
                'brand_id' => $contact->brand_id,
                'is_b2c' => true,
                'email' => $cart->billing_email??$cart->shipping_email,
            ];
            SendNotificationJob::dispatch(true, 1, 'new_sale', $contact, $custom_data, $transaction_data);

            return response()->json([
                'status' => true,
                'message' => 'Order created successfully.',
                'SO' => $newValue,
                'id' => $transaction,
                'data' => $data
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get tax charges for a given state
     */
    private function getTaxCharges($userState)
    {
         // Try to get by brand_id first
         $brandId = request()->input('brand_id');
         if ($brandId) {
             $brandTaxes = LocationTaxCharge::where('brand_id', $brandId)
                 ->where('state_code', $userState)
                 ->get();
             if ($brandTaxes->isNotEmpty()) {
                 return $brandTaxes;
             }
         }
 
         // If not found, try by location_id
         $locationId = request()->route('location_id');
         if ($locationId) {
             $locationTaxes = LocationTaxCharge::where('location_id', $locationId)
                 ->where('state_code', $userState)
                 ->get();
             if ($locationTaxes->isNotEmpty()) {
                 return $locationTaxes;
             }
         }
 
         return LocationTaxCharge::where('state_code', $userState)->get();
     
    }

    /**
     * Process order for guest users
     */
    private function processGuestOrder(Request $request, $currentTime)
    {
        $validate = Validator::make($request->all(), [
            'paymentType' => 'required|string',
            'shippingType' => 'required|string',
            'nonce' => 'nullable|string',
            'is_order' => 'nullable|boolean'
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

        $paytype = $request->input('paymentType');
        $shippingType = $request->input('shippingType');
        $nonce = $request->input('nonce') ?? null;
        $isOrder = $request->input('is_order', true); // Default to true for order creation
        
        // Store is_order in request attributes so discount service can access it
        $request->attributes->set('is_order', $isOrder);
        
        $card = ($paytype == 'card') ? 1 : 0;

        // Get guest session from middleware
        $guestSession = $request->attributes->get('current_guest_session');
        $location = $request->attributes->get('current_location');
        $brand = $request->attributes->get('current_brand');

        if (!$guestSession) {
            return response()->json(['status' => false, 'message' => 'Guest session not found.']);
        }

        // B2C specific settings
        $business_id = 1;
        $location_id = $location->id;
        $shipping_charges = 15.00;

        // BUG FIX #4: Start transaction early and lock cart items to prevent race conditions
        DB::beginTransaction();
        
        try {
            // BUG FIX #2 & #4: Lock guest cart items and check if cart is frozen
            $cartItems = GuestCartItem::where('guest_session_id', $guestSession->id)
                ->lockForUpdate()
                ->get();
                
            if ($cartItems->isEmpty()) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Cart is empty.']);
            }

            // BUG FIX #2: Check if guest cart is frozen (stock reserved)
            $isFreeze = $guestSession->isFreeze ?? false;
            if (!$isFreeze) {
                DB::rollBack();
                return response()->json([
                    'status' => false, 
                    'message' => 'Cart is not frozen. Please complete checkout process first.',
                    'reason' => 'stock_not_reserved'
                ]);
            }

        $productIds = $cartItems->pluck('product_id');
        $products = B2CPaymentOrderHelper::getB2CProductsWithRelations($productIds->toArray(), null, null, $location_id);
        
        // Get tax charges - use guest's shipping state or default to IL
        $userState = $guestSession->shipping_state ?? 'IL';
        $taxCharges = $this->getTaxCharges($userState);

        // Get discounts service for guest (using location and brand)
        $discountService = new CustomDiscountRuleService();
        $discounts = $discountService->getActiveDiscounts(null, $location_id, $brand->id);
        
        // Get applied discounts from guest session
        $appliedDiscounts = $guestSession->applied_discounts ?? [];
        
        // Get address information from guest session
        $billingInfo = [
            'first_name' => $guestSession->billing_first_name ?? 'Guest',
            'last_name' => $guestSession->billing_last_name ?? 'User',
            'company' => $guestSession->billing_company ?? null,
            'address1' => $guestSession->billing_address1 ?? null,
            'address2' => $guestSession->billing_address2 ?? null,
            'city' => $guestSession->billing_city ?? null,
            'state' => $guestSession->billing_state ?? null,
            'zip' => $guestSession->billing_zip ?? null,
            'country' => $guestSession->billing_country ?? 'US',
            'phone' => $guestSession->billing_phone ?? null,
            'email' => $guestSession->billing_email ?? null
        ];

        $shippingInfo = [
            'shipping_first_name' => $guestSession->shipping_first_name ?? $billingInfo['first_name'],
            'shipping_last_name' => $guestSession->shipping_last_name ?? $billingInfo['last_name'],
            'shipping_company' => $guestSession->shipping_company ?? $billingInfo['company'],
            'shipping_address1' => $guestSession->shipping_address1 ?? $billingInfo['address1'],
            'shipping_address2' => $guestSession->shipping_address2 ?? $billingInfo['address2'],
            'shipping_city' => $guestSession->shipping_city ?? $billingInfo['city'],
            'shipping_state' => $guestSession->shipping_state ?? $billingInfo['state'],
            'shipping_zip' => $guestSession->shipping_zip ?? $billingInfo['zip'],
            'shipping_country' => $guestSession->shipping_country ?? $billingInfo['country'],
            'shipping_phone' => $guestSession->shipping_phone ?? $billingInfo['phone'],
            'shipping_email' => $guestSession->shipping_email ?? $billingInfo['email']
        ];

            // Check if billing email matches a guest customer
            $guest = null;
            if (!empty($billingInfo['email'])) {
                $guest = Contact::where('type', 'customer')
                    ->where('is_guest', true)
                    ->where('email', $billingInfo['email'])
                    ->where('brand_id', $brand->id ?? null)
                    ->first();
            }

            // If no matching guest customer found, create a new one
            if (!$guest) {
                $guest = Contact::create([
                    'business_id' => $business_id ?? 1,
                    'location_id' => $location_id ?? 2,
                    'brand_id' => $brand->id ?? null,
                    'type' => 'customer',
                    'is_guest' => true,
                    'name' => trim(($billingInfo['first_name'] ?? '') . ' ' . ($billingInfo['last_name'] ?? '')),
                    'first_name' => $billingInfo['first_name'] ?? null,
                    'last_name' => $billingInfo['last_name'] ?? null,
                    'email' => $billingInfo['email'] ?? null,
                    'mobile' => $billingInfo['phone'] ?? null,
                    'address_line_1' => $billingInfo['address1'] ?? null,
                    'address_line_2' => $billingInfo['address2'] ?? null,
                    'city' => $billingInfo['city'] ?? null,
                    'state' => $billingInfo['state'] ?? null,
                    'zip_code' => $billingInfo['zip'] ?? null,
                    'country' => $billingInfo['country'] ?? 'US',
                    'shipping_first_name' => $shippingInfo['shipping_first_name'] ?? null,
                    'shipping_last_name' => $shippingInfo['shipping_last_name'] ?? null,
                    'shipping_company' => $shippingInfo['shipping_company'] ?? null,
                    'shipping_address1' => $shippingInfo['shipping_address1'] ?? null,
                    'shipping_address2' => $shippingInfo['shipping_address2'] ?? null,
                    'shipping_city' => $shippingInfo['shipping_city'] ?? null,
                    'shipping_state' => $shippingInfo['shipping_state'] ?? null,
                    'shipping_zip' => $shippingInfo['shipping_zip'] ?? null,
                    'shipping_country' => $shippingInfo['shipping_country'] ?? 'US',
                    'shipping_phone' => $shippingInfo['shipping_phone'] ?? null,
                    'shipping_email' => $shippingInfo['shipping_email'] ?? null,
                    'created_by' => 1,
                    'isApproved' => 1,
                    'contact_status' => 'active',
                ]);
            }

            $randomString = time() . Str::random(40);
            
            $newValue = (new TransactionUtil())->getInvoiceNumber($business_id, null, $location_id, null, 'sales_order');
            $transaction = DB::table('transactions')->insertGetId([
                'business_id' => $business_id ?? 1,
                'location_id' => $location_id ?? 2,
                'contact_id' => $guest->id, // Guest order
                'type' => 'sales_order',
                "status" => "ordered",
                'payment_status' => $card ? "paid" : "partial",
                "customer_group_id" => null, // Guest order
                "invoice_no" => $newValue,
                "total_before_tax" => '',
                "discount_type" => null,
                'transaction_date' => now(),
                'final_total' => 0,
                'shipping_address' => ($shippingInfo['shipping_address1'] ?? '') . ' ' . ($shippingInfo['shipping_address2'] ?? '') . ' ' . ($shippingInfo['shipping_city'] ?? '') . ' ' . ($shippingInfo['shipping_state'] ?? '') . ' ' . ($shippingInfo['shipping_zip'] ?? '') . ' ' . ($shippingInfo['shipping_country'] ?? ''),
                'is_direct_sale' => 1,
                'selling_price_group_id' => null, // Guest order
                'recur_interval' => 1.000,
                'recur_interval_type' => 'days',
                'recur_repetitions' => 0,
                "shipping_charges" => 0.00,
                'additional_notes' => 'B2C Guest Order',
                'created_by' => "1",
                'created_at' => now(),
                'updated_at' => now(),
                'shipping_first_name' => $shippingInfo['shipping_first_name'] ?? null,
                'shipping_last_name' => $shippingInfo['shipping_last_name'] ?? null,
                'shipping_company' => $shippingInfo['shipping_company'] ?? null,
                'shipping_address1' => $shippingInfo['shipping_address1'] ?? null,
                'shipping_address2' => $shippingInfo['shipping_address2'] ?? null,
                'shipping_city' => $shippingInfo['shipping_city'] ?? null,
                'shipping_state' => $shippingInfo['shipping_state'] ?? null,
                'shipping_zip' => $shippingInfo['shipping_zip'] ?? null,
                'shipping_country' => $shippingInfo['shipping_country'] ?? 'US',
                'billing_first_name' => $billingInfo['first_name'] ?? null,
                'billing_last_name' => $billingInfo['last_name'] ?? null,
                'billing_company' => $billingInfo['company'] ?? null,
                'billing_address1' => $billingInfo['address1'] ?? null,
                'billing_address2' => $billingInfo['address2'] ?? null,
                'billing_city' => $billingInfo['city'] ?? null,
                'billing_state' => $billingInfo['state'] ?? null,
                'billing_zip' => $billingInfo['zip'] ?? null,
                'billing_country' => $billingInfo['country'] ?? 'US',
                'billing_phone' => $billingInfo['phone'] ?? null,
                'billing_email' => $billingInfo['email'] ?? null,
                'unique_public_url' => $randomString
            ]);

            // Create cartUtil instance for tax calculations
            $cartUtil = new \App\Http\Controllers\B2CECOM\UnifiedCartController();

            // Process guest cart items with B2C pricing, discounts, and tax calculations
            $appliedDiscountBucket = [];
            $sellLinesData = B2CPaymentOrderHelper::processGuestCartItemsForB2C($cartItems, $products, $transaction, $location_id, $appliedDiscounts, $discounts, $discountService, $taxCharges, $userState, $cartUtil, $appliedDiscountBucket);

            // Insert sell lines
            DB::table('transaction_sell_lines')->insert($sellLinesData);

            // Increment top_selling count for each product in the order
            $productIds = collect($sellLinesData)->pluck('product_id')->unique();
            foreach ($productIds as $productId) {
                DB::table('products')
                    ->where('id', $productId)
                    ->increment('top_selling');
            }

            // Calculate final total with shipping using enhanced calculations (now returns array with shipping details)
            $totalsData = B2CPaymentOrderHelper::calculateEnhancedGuestFinalTotal($cartItems, $products, $discountService, $discounts, $appliedDiscounts, $taxCharges, $userState, $shipping_charges, $shippingType, $cartUtil);
            
            $final_total = $totalsData['final_total'];
            $actualShippingCharges = $totalsData['shipping_charges'];
            $freeShippingDiscount = $totalsData['free_shipping_discount'];
            $cartDiscountAmount = $totalsData['cart_discount_amount'];

            // Track free shipping discount in applied discounts
            if ($freeShippingDiscount > -1) {
                $appliedDiscountBucket[] = "Free Shipping Discount: $" . number_format($freeShippingDiscount, 2) . " off (Final Shipping: $" . number_format($actualShippingCharges, 2) . ")";
            }

            // Track cart-level discount
            if ($cartDiscountAmount > 0) {
                $appliedDiscountBucket[] = "Cart Discount: $" . number_format($cartDiscountAmount, 2) . " off";
            }

            // Process payment
            if ($card) {
                $payedAmount = $final_total;
                $paymentResult = B2CPaymentOrderHelper::processGuestPayment($nonce, $payedAmount, $newValue, $billingInfo, $shippingInfo, $transaction, $this->paymentOrderController);
                if (!$paymentResult['status']) {
                    // BUG FIX #3: On payment failure, rollback transaction but keep cart frozen
                    // The UnfreezeCart job will automatically unfreeze and restore stock after timeout
                    DB::rollBack();
                    return response()->json($paymentResult);
                }
            } else {
                $payedAmount = 0;
            }

            // Insert Payment
            if ($payedAmount > 0) {
                B2CPaymentOrderHelper::insertPayment($transaction, $business_id, $payedAmount, $card, $newValue, $final_total);
            }

            // Update transaction
            DB::table('transactions')->where('id', $transaction)->update([
                'final_total' => $final_total,
                "discount_type" => $totalsData['discount_type']??null,
                'shipping_charges' => $actualShippingCharges,
                'total_before_tax' => B2CPaymentOrderHelper::calculateGuestTotalBeforeTax($cartItems, $products),
                'discount_amount' => $cartDiscountAmount,
                'additional_notes' => 'B2C Guest Order <br> Applied Discount: '. implode('<br>', $appliedDiscountBucket),
                'updated_at' => now(),
            ]);

            // Clean up guest cart
            GuestCartItem::where('guest_session_id', $guestSession->id)->delete();
            
            DB::commit();
            
            $data = Transaction::with([
                'payment_lines'=> function($query){
                    $query->select('id','amount', 'paid_on','payment_ref_no','transaction_no','method');
                },
                'sell_lines' => function($query){
                    $query->select('id','transaction_id','product_id','variation_id','ordered_quantity','verified_qty','picked_quantity','unit_price','unit_price_before_discount','unit_price_inc_tax','item_tax','line_discount_type','line_discount_amount');
                },
                'sell_lines.product' => function ($query) {
                    $query->select('id', 'name', 'slug','image'); 
                },
                'sell_lines.variations' => function ($query) {
                    $query->select('id','product_id','name','sub_sku', 'var_barcode_no'); 
                }
            ])->where('contact_id', $guest->id)
            //   ->where('business_id', $business_id)
              ->where('id', $transaction)
              ->select('id','business_id','type','status','final_total','invoice_no','transaction_date','payment_status','location_id','billing_first_name','billing_last_name','billing_company','billing_address1','billing_address2','billing_city','billing_state','billing_country','billing_zip','billing_phone','billing_email','shipping_first_name','shipping_last_name','shipping_company','shipping_address1','shipping_address2','shipping_city','shipping_state','shipping_country','shipping_zip','discount_type','discount_amount','shipping_charges');
            

            //   mail send to customer based on billing info append the unique public url also here $randomString
            // $mailData = [
            //     'billing_info' => $billingInfo,
            //     'shipping_info' => $shippingInfo,
            //     'unique_public_url' => $randomString
            // ];
            $transaction_data = Transaction::find($transaction)->first();
            
            // Get guest email from billing or shipping info
            $guest_email = $billingInfo['email'] ?? $shippingInfo['shipping_email'] ?? null;
            
            // Create a contact object for the guest with the email
            // Use stdClass to ensure proper serialization in the job queue
            $guest_contact = new \stdClass();
            $guest_contact->id = $guest->id;
            $guest_contact->email = $guest_email;
            $guest_contact->mobile = $billingInfo['phone'] ?? $shippingInfo['shipping_phone'] ?? null;
            $guest_contact->name = ($billingInfo['first_name'] ?? '') . ' ' . ($billingInfo['last_name'] ?? '');
            $guest_contact->is_b2c = true;
            $guest_contact->brand_id = $brand->id;
            $guest_contact->business_id = $business_id;
            $guest_contact->type = 'customer';
            
            // Prepare custom_data with guest information
            // Note: transaction is passed separately, not in custom_data to avoid serialization issues
            $custom_data = [
                'contact_id' => $guest->id,
                'transaction_id' => $transaction,
                'brand_id' => $brand->id,
                'is_b2c' => true,
                'email' => $guest_email,
            ];
            
            // Dispatch notification job with proper parameters
            // Parameters: is_custom, business_id, notification_type, user, contact, transaction, custom_data
            SendNotificationJob::dispatch(true, 1, 'new_sale', $guest_contact, $guest_contact, $transaction_data, $custom_data);

            return response()->json([
                'status' => true,
                'message' => 'Order created successfully.',
                'SO' => $newValue,
                'id' => $transaction,
                'uuid'=>$randomString,
                'data' => $data
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ]);
        }
    }
}
