<?php

namespace App\Http\Controllers\ECOM;

use App\Business;
use App\Cart;
use App\CartItem;
use App\Contact;
use App\GiftCard;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ECOM\CartController;
use App\Http\Controllers\ECOM\B2BPaymentOrderHelper;
use App\Http\Controllers\TransactionPaymentController;
use App\Jobs\WooCommerceWebhookSaleOrder;
use App\Transaction;
use App\Jobs\SendNotificationJob;
use App\LocationTaxCharge;
use App\Models\CustomDiscount;
use App\Models\ElevenLabsSessionModel;
use App\PaymentBuffer;
use App\Product;
use App\Services\CustomDiscountRuleService;
use App\Utils\TransactionUtil;
use App\Utils\NotificationUtil;
use App\TransactionPayment;
use App\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Woocommerce\Exceptions\WooCommerceError;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class PaymentOrderController extends Controller
{
    /**
     * @description: NMI Payment Gateway
     */
    private $security_key;
    public $cartDiscountApplicable;
    public $freeShippingApplicable;

    public function __construct()
    {
        $this->security_key = config('services.nmi.security');
        $this->cartDiscountApplicable = true; // Track if cart-level discounts can be applied
        $this->freeShippingApplicable = true; // Track if free shipping can be applied
    }
    /**
     * @version "nmi/nmi": "API Based Payment Gateway"
     * @description: NMI Payment Gateway Request
     */
    public function doSale($amount, $payment_token, $billing, $shipping, $orderid = null)
    {
        $requestOptions = [
            'type' => 'sale',
            'amount' => $amount,
            'payment_token' => $payment_token
        ];
        
        // Add order ID if provided
        if ($orderid) {
            $requestOptions['orderid'] = $orderid;
        }
        
        $requestOptions = array_merge($requestOptions, $billing, $shipping);

        return $requestOptions;
    }

    public function _doRequest($postData)
    {
        $hostName = "secure.nmi.com";
        $path = "/api/transact.php";
        $client = new Client();

        $postData['security_key'] = config('services.nmi.security');
        $postUrl = "https://{$hostName}{$path}";

        // Log NMI Request Data (mask security key for security)
        $logData = $postData;
        if (isset($logData['security_key'])) {
            $logData['security_key'] = '***MASKED***';
        }
        if (isset($logData['payment_token'])) {
            $logData['payment_token'] = substr($logData['payment_token'], 0, 10) . '...***MASKED***';
        }
        Log::info('NMI Payment Request', [
            'url' => $postUrl,
            'request_data' => $logData
        ]);

        try {
            $response = $client->post($postUrl, [
                'form_params' => $postData,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]);

            parse_str($response->getBody(), $responseArray);

            $parsedResponseCode = (int)$responseArray['response_code'];
            $status = in_array($parsedResponseCode, [100, 200]);

            $paydata = [
                'status' => $status,
                'date' => $response->getHeaderLine('Date'),
                'responsetext' => $responseArray['responsetext'],
                'authcode' => $responseArray['authcode'] ?? '',
                'transactionid' => $responseArray['transactionid'] ?? 'failed',
                'avsresponse' => $responseArray['avsresponse'] ?? 'N',
                'cvvresponse' => $responseArray['cvvresponse'] ?? 'N',
                'description' => $response->getBody()->getContents(),
                'response_code' => $parsedResponseCode,
                'type' => $responseArray['type'] ?? ''
            ];

            // Log NMI Response Data
            Log::info('NMI Payment Response', [
                'status' => $status,
                'response_code' => $parsedResponseCode,
                'transaction_id' => $paydata['transactionid'],
                'response_text' => $paydata['responsetext'],
                'auth_code' => $paydata['authcode'],
                'avs_response' => $paydata['avsresponse'],
                'cvv_response' => $paydata['cvvresponse'],
                'full_response' => $responseArray
            ]);

            return $paydata;
        } catch (Exception $e) {
            // Log NMI Error
            Log::error('NMI Payment Error', [
                'error_message' => $e->getMessage(),
                'request_data' => $logData
            ]);
            throw new Exception("Error: " . $e->getMessage());
        }
    }


    /**
     * @version "authorizenet/authorizenet": "2.0.3"
     * @description: Authorize Payment Gateway
     */
    public function payByAuthorize($total, $paymentToken, $newValue, $useID, $userMail, $billing = [])
    {
        try {
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName(config('services.authorizenet.login_id'));
            $merchantAuthentication->setTransactionKey(config('services.authorizenet.transaction_key'));

            $refId = 'ref' . time();

            $opaqueData = new AnetAPI\OpaqueDataType();
            $opaqueData->setDataDescriptor("COMMON.ACCEPT.INAPP.PAYMENT");
            $opaqueData->setDataValue($paymentToken);

            $paymentOne = new AnetAPI\PaymentType();
            $paymentOne->setOpaqueData($opaqueData);

            $order = new AnetAPI\OrderType();
            $order->setInvoiceNumber($newValue . '-' . time());
            $order->setDescription("Splash Payment Order");

            $customerAddress = new AnetAPI\CustomerAddressType();
            $customerAddress->setFirstName($billing['first_name'] ?? 'John');
            $customerAddress->setLastName($billing['last_name'] ?? 'Doe');
            $customerAddress->setCompany($billing['company'] ?? 'Company');
            $customerAddress->setAddress($billing['address1'] ?? '123 Main St');
            $customerAddress->setCity($billing['city'] ?? 'City');
            $customerAddress->setState($billing['state'] ?? 'State');
            $customerAddress->setZip($billing['zip'] ?? '12345');
            $customerAddress->setCountry("USA");

            $customerData = new AnetAPI\CustomerDataType();
            $customerData->setType("individual");
            $customerData->setId($useID);
            $customerData->setEmail($billing['email'] ?? $userMail);

            $duplicateWindowSetting = new AnetAPI\SettingType();
            $duplicateWindowSetting->setSettingName("duplicateWindow");
            $duplicateWindowSetting->setSettingValue("120");

            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount((float)$total);
            $transactionRequestType->setPayment($paymentOne);
            $transactionRequestType->setOrder($order);
            $transactionRequestType->setBillTo($customerAddress);
            $transactionRequestType->setCustomer($customerData);
            $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);

            $request = new AnetAPI\CreateTransactionRequest();
            $request->setMerchantAuthentication($merchantAuthentication);
            $request->setRefId($refId);
            $request->setTransactionRequest($transactionRequestType);

            $controller = new AnetController\CreateTransactionController($request);
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

            if ($response != null) {
                if ($response->getMessages()->getResultCode() == "Ok") {
                    $tresponse = $response->getTransactionResponse();

                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        Log::info('Authorize.net Payment Success', [
                            'transaction_id' => $tresponse->getTransId(),
                            'response_code' => $tresponse->getResponseCode(),
                            'auth_code' => $tresponse->getAuthCode(),
                            'description' => $tresponse->getMessages()[0]->getDescription()
                        ]);

                        return [
                            'status' => true,
                            'message' => 'Payment successful!',
                            'transaction_id' => $tresponse->getTransId(),
                            'auth_code' => $tresponse->getAuthCode()
                        ];
                    } else {
                        $errorMessage = "Transaction Failed";
                        if ($tresponse != null && $tresponse->getErrors() != null) {
                            $errorMessage .= ": " . $tresponse->getErrors()[0]->getErrorText();
                            Log::error('Authorize.net Payment Error', [
                                'error_code' => $tresponse->getErrors()[0]->getErrorCode(),
                                'error_message' => $tresponse->getErrors()[0]->getErrorText()
                            ]);
                        }
                        return [
                            'status' => false,
                            'message' => $errorMessage
                        ];
                    }
                } else {
                    $tresponse = $response->getTransactionResponse();
                    $errorMessage = "Transaction Failed";

                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        $errorMessage .= ": " . $tresponse->getErrors()[0]->getErrorText();
                        Log::error('Authorize.net Payment Error', [
                            'error_code' => $tresponse->getErrors()[0]->getErrorCode(),
                            'error_message' => $tresponse->getErrors()[0]->getErrorText()
                        ]);
                    } else {
                        $errorMessage .= ": " . $response->getMessages()->getMessage()[0]->getText();
                        Log::error('Authorize.net Payment Error', [
                            'error_code' => $response->getMessages()->getMessage()[0]->getCode(),
                            'error_message' => $response->getMessages()->getMessage()[0]->getText()
                        ]);
                    }

                    return [
                        'status' => false,
                        'message' => $errorMessage
                    ];
                }
            }

            Log::error('Authorize.net Payment Error: No response returned');
            return [
                'status' => false,
                'message' => "Payment failed: No response from payment gateway"
            ];
        } catch (\Throwable $th) {
            Log::error('Authorize.net Payment Exception', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return [
                'status' => false,
                'message' => $th->getMessage() . ' On Authorize Payment'
            ];
        }
    }

    /**
     * @version "square/square": "^39.1"
     * @description: Square Payment Gateway 
     */
    // private function payBySquare($amount, $payment_token, $billing, $shipping){
    //     //square payment 
    //     $client = SquareClientBuilder::init()
    //         ->bearerAuthCredentials(
    //             BearerAuthCredentialsBuilder::init(config('services.square.token'))
    //         )
    //         ->environment(config('services.square.mode') == 'production' ? Environment::PRODUCTION : Environment::SANDBOX)
    //         ->build();

    //     $amountMoney = new Money();
    //     $amountMoney->setAmount($amount * 100); // Amount in cents
    //     $amountMoney->setCurrency('USD');

    //     $billingAddress = new Address();
    //     $billingAddress->setAddressLine1(trim($billing['address1']));
    //     $billingAddress->setAddressLine2(isset($billing['address2']) ? trim($billing['address2']) : null);
    //     $billingAddress->setLocality(trim($billing['city']));
    //     $billingAddress->setAdministrativeDistrictLevel1(trim($billing['state']));
    //     $billingAddress->setPostalCode(trim($billing['zip']));
    //     $billingAddress->setCountry('US');

    //     $paymentBody = new CreatePaymentRequest(
    //         $payment_token,
    //         uniqid()
    //     );
    //     $paymentBody->setAmountMoney($amountMoney);
    //     $paymentBody->setAutocomplete(true);
    //     $paymentBody->setLocationId(config('services.square.locationID'));
    //     $paymentBody->setBillingAddress($billingAddress);
    //     $paymentBody->setNote('Express Payment Note');

    //     try {
    //         $apiResponse = $client->getPaymentsApi()->createPayment($paymentBody);

    //         if ($apiResponse->isSuccess()) {
    //             $result = $apiResponse->getResult();
    //             $payment = $result->getPayment();

    //             // Extracting required fields
    //             $data = [
    //                 'status' => true,
    //                 'message' => 'Payment successful!',
    //                 'payment_id' => $payment->getId(),
    //                 'approved_money' => $payment->getApprovedMoney()->getAmount() / 100,
    //                 'payment_status' => $payment->getStatus(),
    //                 'card_details' => $payment->getCardDetails(),
    //                 'card_details_status' => $payment->getCardDetails()->getStatus(),
    //                 'card' => $payment->getCardDetails()->getCard(),
    //                 'card_brand' => $payment->getCardDetails()->getCard()->getCardBrand(),
    //                 'card_type' => $payment->getCardDetails()->getCard()->getCardType(),
    //             ];
    //             return $data;
    //         } else {
    //             $errors = $apiResponse->getErrors();
    //             Log::error('Payment Failed. API Errors: ' . print_r($errors, true));
    //             return [
    //                 'status' => false,
    //                 'message' => 'Payment Failed! Please wait for a minute and try again.',
    //                 'error_details' => $errors
    //             ];
    //         }
    //     } catch (ApiException $e) {
    //         Log::error('API Exception occurred: ' . $e->getMessage());
    //         return [
    //             'status' => false,
    //             'message' => 'An unexpected error occurred. Please try again later.',
    //             'error_details' => $e->getMessage()
    //         ];
    //     } catch (\Throwable $th) {
    //         Log::error('Unexpected Error: ' . $th->getMessage());
    //         return [
    //             'status' => false,
    //             'message' => 'An unexpected error occurred. Please try again later.',
    //             'error_details' => $th->getMessage()
    //         ];
    //     }
    // }

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
     * @description: B2B Sales Order Creation by API Request (ECOM)
     * @version 1.0.0
     * @author [Utkarsh Shukla]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processOrder(Request $request)
    {
        $currentTime = now();
        $cid = $request->query('cid');
        if ($cid) {
            if (
                !auth()->user()->can('sell.update') &&
                !auth()->user()->can('direct_sell.access') &&
                !auth()->user()->can('so.update') &&
                !auth()->user()->can('edit_pos_payment')
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
                    // validate based on conversation id 
                }
            }
            $authData = $this->authCheck($request);
            if (!$authData['status']) {
                return response()->json(['status' => false, 'message' => 'Unauthorized user.']);
            }
            $contact = $authData['user'];
            $userId = $contact->id;
        }
        $priceTier = $contact->price_tier;
        $priceGroupId = key($priceTier);
        //custom defined
        $business_id = 1;
        $location_id =$contact->location_id;
        // Use shipping_charge from payload when provided, else default
        $payloadShippingCharge = $request->input('shipping_charge') ?? $request->input('shipping_charges');
        $shipping_charges = (is_numeric($payloadShippingCharge) && (float) $payloadShippingCharge >= 0)
            ? (float) $payloadShippingCharge
            : 15.00;
        $shippingAddressLine = $contact->shipping_address;

        $final_total = 0;
        $payedAmount = 0;
        $selling_price_group_id = $priceGroupId;
        $validate = Validator::make($request->all(), [
            'paymentType' => 'required|string',
            'shippingType' => 'required|string',
            'nonce' => 'nullable|string',
            'is_order' => 'nullable|boolean',
            // Optional: shipping rate object selected from /cart/shipping-rates
            'shipping_rate' => 'nullable|array',
            // Optional: shipping charge amount (e.g. from FLATRATE or selected rate)
            'shipping_charge' => 'nullable|numeric|min:0',
            'shipping_charges' => 'nullable|numeric|min:0',
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
        
        $isPayWallet = $request->input('isPayWallet') ?? false;

        if ($paytype == 'card') {
            $card = 1;
        } else if ($paytype == 'onaccount') {
            $card = 0;
        }

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

            // Gift order: from request or cart. When is_gift=true, auto-hide invoice/packing slip for recipient.
            $isGift = $request->has('is_gift') ? $request->boolean('is_gift') : (bool) ($cart->is_gift ?? false);
            $hidePricesForRecipient = $request->has('hide_prices_for_recipient')
                ? $request->boolean('hide_prices_for_recipient')
                : ($isGift ? (bool) ($cart->hide_prices_for_recipient ?? true) : (bool) ($cart->hide_prices_for_recipient ?? false));
            if ($isGift && !$request->has('hide_prices_for_recipient')) {
                $hidePricesForRecipient = true; // auto-hide invoice when gift order
            }

            $newValue = (new TransactionUtil())->getInvoiceNumber($business_id, null, $location_id, null, 'sales_order');
            $transaction = DB::table('transactions')->insertGetId([
                'business_id' => $business_id ?? 1,
                'location_id' => $location_id ?? 1,
                'contact_id' => $userId,
                'type' => 'sales_order',
                "status" => "ordered",
                'payment_status' => $card ? "paid" : "partial",
                "customer_group_id" => $priceGroupId,
                "invoice_no" => $newValue,
                "total_before_tax" => '',
                "discount_type" => null, //currently discount not counted 
                'transaction_date' => now(), // payment response
                'final_total' => $final_total,
                'shipping_address' => $cart->shipping_address1 . ' ' . $cart->shipping_address2 . ' ' . $cart->shipping_city . ' ' . $cart->shipping_state . ' ' . $cart->shipping_zip . ' ' . $cart->shipping_country ?? $shippingAddressLine,

                'is_direct_sale' => 1,
                'selling_price_group_id' => $selling_price_group_id,
                'recur_interval' => 1.000,
                'recur_interval_type' => 'days',
                'recur_repetitions' => 0,

                "shipping_charges" => 0.00,
                'additional_notes' => 'Web Order',
                'created_by' => "1", //admin
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
                'billing_country' => $cart->billing_country ?? null,
                'billing_zip' => $cart->billing_zip ?? null,
                'billing_phone' => $cart->billing_phone ?? null,
                'billing_email' => $cart->billing_email ?? null,
                'is_gift' => $isGift,
                'hide_prices_for_recipient' => $hidePricesForRecipient,
            ]);

            //sales item 
            $cartUtil = new CartController();
            $cartItemGet = $cartUtil->getCartItems($userId);
            if($cartItemGet['status'] == false){
                return response()->json(['status' => false, 'message' => $cartItemGet['message']]);
            }
            $cartItems = $cartItemGet['data'];
            $productIds = $cartItems->pluck('product_id');
            
            //fetch all cart item at once 
            $products = $cartUtil->getProductsWithRelations($productIds, $userId, $priceGroupId, false);

            // If cart has at least one tobacco product, enforce Tobacco License before proceeding
            $hasTobaccoItem = $products->contains(function ($product) {
                return !empty($product->is_tobacco_product);
            });
            if ($hasTobaccoItem) {
                $hasTobaccoLicense = $contact->documentsAndnote()
                    ->where('heading', 'Tobacco-License')
                    ->exists();

                if (! $hasTobaccoLicense) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'code' => 'tobacco_license_required',
                        'message' => 'Tobacco License is required to purchase tobacco products. Please upload it in your account and try again.',
                    ], 422);
                }
            }
            
            // Get discounts service 
            $discountService = new CustomDiscountRuleService();
            $discounts = $discountService->getActiveDiscounts($contact);
            $appliedDiscountsRaw = $cart->applied_discounts ?? [];
            $appliedDiscounts = is_array($appliedDiscountsRaw) ? $appliedDiscountsRaw : [];

            // Pre-fetch location tax charges
            $userState = $cart->shipping_state ?? $contact->shipping_state;
            $taxCharges =$cartUtil->getTaxCharges($userState);

            //transaction sale lines
            $count = 0;
            $subtotal = 0;
            $total_before_tax = 0;
            $cart_final_total = 0;
            $cartDiscountApplicable = true;
            $isFreeShippingApplicable = true;

            // Check order limits and update session table
            foreach ($cartItems as $cartItem) {
                $product = $products->where('id', $cartItem->product_id)->first();
                if ($product) {
                    $variation = $product->variations->where('id', $cartItem->variation_id)->first();
                    
                    // order limit session table update 
                    $maxSaleLimit = $variation->var_maxSaleLimit ?? $product->maxSaleLimit ?? false;
                    $activeSessionId = null;
                    $productSession = null;
                    if($maxSaleLimit){

                        // check limit before placing the order 
                        $allowedItemQty = $cartUtil->allowedItemQty($product->id, $variation->id, $userId, $cartItem->qty, $currentTime, $maxSaleLimit);
                        if($allowedItemQty['status'] == false || $allowedItemQty['can_add'] == false ){
                            // Get product and variation names for specific error message
                            $productName = $product->name ?? 'Unknown Product';
                            $variationName = $variation->name ?? 'Default';
                            $variationDisplayName = $variationName === 'DUMMY' ? '' : ' (' . $variationName . ')';
                            
                            if($allowedItemQty['allowed_qty'] == 0){
                                // product variant reached the limit 
                                return response()->json([
                                    'status' => false, 
                                    'message' => "You have reached the maximum order limit for {$productName}{$variationDisplayName}. Please reduce your quantity or try again later, if you think this is an error, please contact support.",
                                    
                                ]);
                            }
                            if($allowedItemQty['allowed_qty'] < $cartItem->qty){
                                // product variant reached the limit 
                                return response()->json([
                                    'status' => false, 
                                    'message' => "You have reached the maximum order limit for {$productName}{$variationDisplayName}. Maximum allowed quantity: {$allowedItemQty['allowed_qty']}, Please reduce your quantity or try again later, if you think this is an error, please contact support.",
                                    
                                ]);
                            }
                        }




                        // First check for variant-specific limits, then fallback to product-level limits
                        $productSessions = DB::table('product_order_limits')
                        ->where(function($query) use ($product, $variation) {
                            $query->where('variant_id', $variation->id)
                                  ->orWhere(function($q) use ($product) {
                                      $q->where('product_id', $product->id)
                                        ->whereNull('variant_id');
                                  });
                        })
                        ->where('is_active', 1)
                        ->get();
                        if($productSessions){
                            foreach($productSessions as $productSession){
                                $startTime = Carbon::parse($productSession->start_date ?? '2000-01-01 00:00:00');
                                $endTime = Carbon::parse($productSession->end_date ?? '2099-12-31 23:59:59');
                                if($currentTime->between($startTime, $endTime) && $productSession->is_active){
                                    $activeSessionId = $productSession->id;
                                    $productSession = $productSession;
                                    break;
                                }
                            }
                        }
                        if($activeSessionId){
                            $productLimitSession = DB::table('product_order_limit_consumers')
                                ->where('session_id', $activeSessionId)
                                ->where('consumer_id', $userId)
                                ->first();
                            if($productLimitSession){
                                $previousOrderCount = $productLimitSession->order_count ?? 0;
                                $previousLimitCount = $productLimitSession->qty_count ?? 0;
                                $newLimitCount = $previousLimitCount + $cartItem->qty;
                                
                                $maxQtyLimit = $maxSaleLimit; // per order qty limit
                                $maxOrderLimit = $productSession->order_limit ?? null;
                                
                                // Calculate how many "full qty blocks" were consumed before and after
                                $previousBlocks = intdiv($previousLimitCount, $maxQtyLimit);
                                $newBlocks = intdiv($newLimitCount, $maxQtyLimit);

                                // Calculate how many additional order counts to increment
                                $additionalOrders = $newBlocks - $previousBlocks;
                                $newOrderCount = $previousOrderCount + $additionalOrders;

                                DB::table('product_order_limit_consumers')
                                    ->where('id', $productLimitSession->id)
                                    ->update([
                                        'order_count' => $newOrderCount,
                                        'qty_count' => $newLimitCount,
                                        'updated_at' => now(),
                                    ]);
                            } else {
                                $orderCount = intdiv($cartItem->qty, $maxSaleLimit); // initial order count based on qty
                                $limitCount = $cartItem->qty;
                                DB::table('product_order_limit_consumers')->insert([
                                    'variant_id' => $variation->id,
                                    'product_id' => $product->id,
                                    'session_id' => $activeSessionId,
                                    'consumer_id' => $userId,
                                    'order_count' => $orderCount,
                                    'qty_count' => $limitCount,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }
                }
            }

            // Use B2BPaymentOrderHelper to process cart items WITH FREE ITEMS SUPPORT
            $sellLinesData = B2BPaymentOrderHelper::processCartItemsForB2B(
                $cartItems,
                $products,
                $transaction,
                $userId,
                $currentTime,
                $cartUtil,
                $discountService,
                $discounts,
                $appliedDiscounts,
                $taxCharges,
                $userState,
                $appliedDiscountBucket
            );

            // Attach gift cards selected in cart as separate sell lines (no stock movement)
            // For each gift card purchased, create a NEW GiftCard with unique code for the customer
            $giftCardsTotal = 0.0;
            $giftCardsRaw = $cart->gift_cards_to_purchase ?? [];
            $giftCardsToPurchase = is_array($giftCardsRaw) ? $giftCardsRaw : [];
            $giftCardLines = []; // Separate array for gift cards
            $createdGiftCards = []; // New gift cards with codes to email customer after invoice
            if (!empty($giftCardsToPurchase)) {
                foreach ($giftCardsToPurchase as $gcData) {
                    try {
                        $templateGiftCard = GiftCard::find($gcData['id'] ?? null);
                        if (!$templateGiftCard || $templateGiftCard->status !== 'active') {
                            continue;
                        }

                        $amount = (float) ($gcData['initial_amount'] ?? $templateGiftCard->initial_amount ?? 0);
                        if ($amount <= 0) {
                            continue;
                        }

                        $giftCardsTotal += $amount;

                        // Create NEW gift card with unique code for this purchase (Amazon-style)
                        $newGiftCard = GiftCard::create([
                            'code' => GiftCard::generateUniqueCode(),
                            'initial_amount' => $amount,
                            'balance' => $amount,
                            'currency' => $templateGiftCard->currency ?? 'USD',
                            'purchaser_contact_id' => $userId,
                            'type' => $templateGiftCard->type ?? 'egift',
                            'recipient_name' => $gcData['recipient_name'] ?? $contact->name ?? null,
                            'recipient_email' => $gcData['recipient_email'] ?? $contact->email ?? null,
                            'message' => $gcData['message'] ?? $templateGiftCard->message ?? null,
                            'status' => 'active',
                            'purchased_at' => now(),
                            'expires_at' => $templateGiftCard->expires_at ?? null,
                        ]);
                        $createdGiftCards[] = $newGiftCard;

                        // Store gift card lines separately (product_id/variation_id are null, which violates FK constraint)
                        $giftCardLines[] = [
                            'transaction_id' => $transaction,
                            'product_id' => null,
                            'variation_id' => null,
                            'quantity' => 1,
                            'ordered_quantity' => 1,
                            'unit_price' => $amount,
                            'unit_price_before_discount' => $amount,
                            'unit_price_inc_tax' => $amount,
                            'item_tax' => 0,
                            'line_discount_type' => 'fixed',
                            'line_discount_amount' => 0,
                            'is_free' => false,
                            'sell_line_note' => 'Gift Card - ' . ucfirst($templateGiftCard->type ?? 'egift'),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } catch (\Throwable $e) {
                        Log::warning('Failed to attach gift card line to sales order', [
                            'cart_id' => $cart->id ?? null,
                            'gift_card_id' => $gcData['id'] ?? null,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // Calculate totals (include gift cards in calculation)
            $allSellLinesForTotals = array_merge($sellLinesData, $giftCardLines);
            $total_before_tax = B2BPaymentOrderHelper::calculateTotalBeforeTax($allSellLinesForTotals);
            $cart_final_total = 0;
            foreach ($allSellLinesForTotals as $sellLine) {
                if (!isset($sellLine['is_free']) || !$sellLine['is_free']) {
                    $cart_final_total += ($sellLine['unit_price_inc_tax'] ?? $sellLine['unit_price']) * $sellLine['quantity'];
                }
            }
            $count = count($allSellLinesForTotals);

            // Apply cart-level and shipping discounts using helper
            $totalsData = B2BPaymentOrderHelper::calculateFinalTotal(
                $cartItems,
                $products,
                $cartUtil,
                $discountService,
                $discounts,
                $appliedDiscounts,
                $taxCharges,
                $userState,
                $shipping_charges,
                $shippingType,
                $userId
            );

            // Extract discount and shipping data
            $cartDiscountAmount = $totalsData['cart_discount_amount'];
            $shipping_charges = $totalsData['shipping_charges'];
            $freeShippingDiscountAmount = $totalsData['free_shipping_discount'];
            
            // Log applied discounts for cart and shipping
            foreach ($discounts as $discount) {
                if ($discount->discountType === 'cartAdjustment' && 
                    $discountService->isDiscountApplicable($discount, $cartItems, $products, 0, $appliedDiscounts)) {
                    $appliedDiscountBucket[] = $discount->couponName . '( id: ' . $discount->id . ' type: ' . $discount->discountType . ')';
                    break; // Only one cart discount applied
                }
            }
            
            if ($freeShippingDiscountAmount > -1) {
                foreach ($discounts as $discount) {
                    if ($discount->discountType === 'freeShipping' && 
                        $discountService->isDiscountApplicable($discount, $cartItems, $products, 0, $appliedDiscounts)) {
                        $appliedDiscountBucket[] = $discount->couponName . '( id: ' . $discount->id . ' type: ' . $discount->discountType . ')';
                        break;
                    }
                }
            }

            // Insert regular sell lines (products)
            if (!empty($sellLinesData)) {
                DB::table('transaction_sell_lines')->insert($sellLinesData);
            }

            // Insert gift card lines separately (product_id/variation_id are null, requires FK check disabled)
            if (!empty($giftCardLines)) {
                // Temporarily disable foreign key checks for gift card insertion
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                try {
                    DB::table('transaction_sell_lines')->insert($giftCardLines);
                } finally {
                    // Re-enable foreign key checks
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                }
            }

            // Increment top_selling count for each product in the order (exclude gift cards)
            $productIds = collect($sellLinesData)->pluck('product_id')->filter()->unique();
            foreach ($productIds as $productId) {
                DB::table('products')
                    ->where('id', $productId)
                    ->increment('top_selling');
            }

            // Update shipping info if not pickup
            if ($shippingType !== "PICKUP") {
                $shippingInfo = [
                    'shipping_first_name' => $cart->shipping_first_name,
                    'shipping_last_name' => $cart->shipping_last_name,
                    'shipping_company' => $cart->shipping_company,
                    'shipping_address1' => $cart->shipping_address1,
                    'shipping_address2' => $cart->shipping_address2,
                    'shipping_city' => $cart->shipping_city,
                    'shipping_state' => $cart->shipping_state,
                    'shipping_zip' => $cart->shipping_zip,
                    'shipping_country' => $cart->shipping_country,
                    'shipping_email' => $cart->shipping_email
                ];
                DB::table('transactions')->where('id', $transaction)->update([
                    'shipment' => $shippingInfo,
                ]);
            }

            // Calculate final total with shipping (using helper's calculation) + gift cards
            $final_total = $totalsData['final_total'] + ($giftCardsTotal ?? 0);

            // Reward points: optional partial redemption (e.g. use $20 when balance is $50)
            $rp_redeemed = 0;
            $rp_redeemed_amount = 0.0;
            $pointsToRedeem = $request->input('reward_points_to_redeem')
            ?? $request->input('rewardPointsToRedeem')
            ?? $request->input('reward_points')
            ?? $request->input('rewardPoints')
            ?? $request->input('points_to_redeem')
            ?? $request->input('pointsToRedeem');
            // $pointsToRedeem = $request->input('reward_points_to_redeem') ?? $request->input('rewardPointsToRedeem');
            // If request did not send points, use cart's saved value (customer may have applied in cart but client didn't send in place-order body)
            if (($pointsToRedeem === null || $pointsToRedeem === '' || !is_numeric($pointsToRedeem) || (int) $pointsToRedeem <= 0) && $cart && (int) ($cart->reward_points_to_redeem ?? 0) > 0) {
                $pointsToRedeem = (int) $cart->reward_points_to_redeem;
            }
            if (is_numeric($pointsToRedeem) && (int) $pointsToRedeem > 0) {
                $business = Business::find($business_id);
                if ($business && (int) $business->enable_rp === 1) {
                    $transactionUtil = app(TransactionUtil::class);
                    $redeemDetails = $transactionUtil->getRewardRedeemDetails($business_id, $userId);
                    $rewardPointsAvailable = (int) ($redeemDetails['points'] ?? 0);
                    $pointsToRedeem = (int) $pointsToRedeem;

                    if ($pointsToRedeem > $rewardPointsAvailable) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => 'You cannot redeem more reward points than your available balance.',
                            'code' => 'reward_points_exceed_balance',
                            'data' => [
                                'requested_points' => $pointsToRedeem,
                                'available_points' => $rewardPointsAvailable,
                            ],
                        ], 422);
                    }

                    $amountPerPoint = (float) ($business->redeem_amount_per_unit_rp ?? 0.01);
                    $rewardDiscount = round($pointsToRedeem * $amountPerPoint, 2);

                    if ($rewardDiscount > $final_total) {
                        $rewardDiscount = $final_total;
                        $pointsToRedeem = $amountPerPoint > 0 ? (int) round($rewardDiscount / $amountPerPoint) : 0;
                    }

                    $minOrderForRedeem = (float) ($business->min_order_total_for_redeem ?? 0);
                    if ($final_total >= $minOrderForRedeem && $rewardDiscount > 0) {
                        $rp_redeemed = $pointsToRedeem;
                        $rp_redeemed_amount = $rewardDiscount;
                        $final_total = max(0, $final_total - $rewardDiscount);
                    }
                }
            }

            // Process applied gift cards (redeemed balance applied to payment)
            $gift_card_amount = 0.0;
            $appliedGiftCards = [];
            if ($cart) {
                // Get applied gift cards from cart (stored as JSON array of IDs or as gift_card_amount)
                $appliedGiftCardIds = $cart->applied_gift_cards ?? [];
                
                // Handle JSON string if needed
                if (is_string($appliedGiftCardIds)) {
                    $appliedGiftCardIds = json_decode($appliedGiftCardIds, true) ?? [];
                }
                
                if (!empty($appliedGiftCardIds) && is_array($appliedGiftCardIds)) {
                    $giftCards = \App\GiftCard::whereIn('id', $appliedGiftCardIds)
                        ->where('status', 'active')
                        ->where('balance', '>', 0)
                        ->get();
                    
                    foreach ($giftCards as $giftCard) {
                        // Check if expired
                        if ($giftCard->expires_at && $giftCard->expires_at->isPast()) {
                            continue; // Skip expired cards
                        }
                        
                        $cardBalance = (float) $giftCard->balance;
                        $amountToApply = min($cardBalance, $final_total); // Don't apply more than the order total
                        
                        if ($amountToApply > 0) {
                            $gift_card_amount += $amountToApply;
                            $appliedGiftCards[] = [
                                'id' => $giftCard->id,
                                'code' => $giftCard->code,
                                'balance' => $cardBalance,
                                'amount_applied' => $amountToApply,
                            ];
                            
                            // Deduct balance from gift card
                            $newBalance = max(0, $cardBalance - $amountToApply);
                            \App\GiftCard::where('id', $giftCard->id)->update([
                                'balance' => $newBalance,
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
                
                // Fallback: check gift_card_amount directly from cart if no IDs found
                if ($gift_card_amount == 0 && isset($cart->gift_card_amount) && $cart->gift_card_amount > 0) {
                    $gift_card_amount = min((float) $cart->gift_card_amount, $final_total);
                }
            }
            
            // Deduct gift card amount from final total (after reward points)
            if ($gift_card_amount > 0) {
                $final_total = max(0, $final_total - $gift_card_amount);
            }

            if ($isPayWallet) {
                $walletBalance = Contact::where('id', $userId)->first()->balance;
                if ($walletBalance >= $final_total) {
                    $payedAmount = $final_total;
                    $walletBalance -= $final_total;
                    Contact::where('id', $userId)->update([
                        'balance' => $walletBalance,
                    ]);
                    $paymentData = [
                        'transaction_id' => $transaction,
                        'business_id' => $business_id,
                        'amount' => $final_total,
                        'method' => 'wallet',
                        'card_type' => 'wallet',
                        'payment_ref_no' => $newValue,
                        'transaction_no' => null,
                        'paid_on' => now(),
                        'payment_for' => $userId,
                        'gateway' => '',
                        'note' => $final_total ." was final total, Wallet Balance: " . $walletBalance,
                        'created_by' => 1, //admin
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    DB::table('transaction_payments')->insert($paymentData);
                } else {
                    if ($card) {
                        $payedAmount = $final_total-$walletBalance;
                        $billingInfo = [
                            'first_name' => $cart->billing_first_name,
                            'last_name' => $cart->billing_last_name,
                            'company' => $cart->billing_company,
                            'address1' => $cart->billing_address1,
                            'address2' => $cart->billing_address2,
                            'city' => $cart->billing_city,
                            'state' => $cart->billing_state,
                            'zip' => $cart->billing_zip,
                            'country' => $cart->billing_country,
                            'phone' => $cart->billing_phone,
                            'email' => $cart->billing_email
                        ];
                        $shippingInfo = [
                            'shipping_first_name' => $cart->shipping_first_name,
                            'shipping_last_name' => $cart->shipping_last_name,
                            'shipping_company' => $cart->shipping_company,
                            'shipping_address1' => $cart->shipping_address1,
                            'shipping_address2' => $cart->shipping_address2,
                            'shipping_city' => $cart->shipping_city,
                            'shipping_state' => $cart->shipping_state,
                            'shipping_zip' => $cart->shipping_zip,
                            'shipping_country' => $cart->shipping_country,
                            'shipping_email' => $cart->shipping_email
                        ];
                        $gatewayTransactionNo = null;
                        $gatewayText = null;
                        //payment 
                        if ($nonce) {
                            if (config('app.gatewayType') == 'authorizenet') {
                                // Authorize Payment
                                $payByAuthorize =
                                    $this->payByAuthorize(
                                        $payedAmount,
                                        $nonce,
                                        $transaction,
                                        $userId,
                                        $cart->billing_email,
                                        $billingInfo
                                    );
        
                                if ($payByAuthorize['status'] === true) {
                                    Log::info('Authorize Payment Success');
                                    Log::info($payedAmount . ' was paid' . $newValue . 'for ' . $userId . '( ' . $cart->billing_email . ' )' . ' on ' . now());
                                    $gatewayTransactionNo = $payByAuthorize['transaction_id'];
                                    $gatewayText = $payByAuthorize['description'];
                                } else {
                                    return response()->json([
                                        'status' => false,
                                        'message' => $payByAuthorize['message'],
                                    ]);
                                }
                            } else {
                                // NMI Payment
                                $saleData = $this->doSale($payedAmount, $nonce, $billingInfo, $shippingInfo);
                                $paymentResult = $this->_doRequest($saleData);
                                if ($paymentResult['status'] === false || $paymentResult['responsetext'] !== 'Approved') {
                                    return response()->json([
                                        'status' => false,
                                        'message' => $paymentResult['responsetext'],
                                    ]);
                                }
        
                                $gatewayTransactionNo = $paymentResult['transactionid'];
                                $gatewayText = $paymentResult['responsetext'];
                            }
                            // Insert Payment
                            $paymentData = [
                                'transaction_id' => $transaction,
                                'business_id' => $business_id,
                                'amount' => $final_total,
                                'method' => 'wallet + custom_pay_1',
                                'card_type' => 'credit',
                                'payment_ref_no' => $newValue,
                                'transaction_no' => $gatewayTransactionNo,
                                'paid_on' => now(),
                                'payment_for' => $userId,
                                'gateway' => 'NMI',
                                'note' => $final_total ." was final total, Gateway Said: " . $gatewayText ?? '' . " Wallet Balance: " . $walletBalance,
                                'created_by' => 1, //admin
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                            DB::table('transaction_payments')->insert($paymentData);
                        } else {
                            return response()->json([
                                'status' => false,
                                'message' => 'Token is Missing',
                            ]);
                        }
                    } else {
                        $paymentData = [
                            'transaction_id' => $transaction,
                            'business_id' => $business_id,
                            'amount' => $walletBalance,
                            'method' => 'wallet + onaccount',
                            'card_type' => 'credit',
                            'payment_ref_no' => $newValue,
                            'transaction_no' => null,
                            'paid_on' => now(),
                            'payment_for' => $userId,
                            'gateway' => '',
                            'note' => $walletBalance ." was paid from wallet,",
                            'created_by' => 1, //admin
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        DB::table('transaction_payments')->insert($paymentData);

                    }
                }
            }else{
                if ($card) {
                    $payedAmount = $final_total;
                    $billingInfo = [
                        'first_name' => $cart->billing_first_name,
                        'last_name' => $cart->billing_last_name,
                        'company' => $cart->billing_company,
                        'address1' => $cart->billing_address1,
                        'address2' => $cart->billing_address2,
                        'city' => $cart->billing_city,
                        'state' => $cart->billing_state,
                        'zip' => $cart->billing_zip,
                        'country' => $cart->billing_country,
                        'phone' => $cart->billing_phone,
                        'email' => $cart->billing_email
                    ];
                    $shippingInfo = [
                        'shipping_first_name' => $cart->shipping_first_name,
                        'shipping_last_name' => $cart->shipping_last_name,
                        'shipping_company' => $cart->shipping_company,
                        'shipping_address1' => $cart->shipping_address1,
                        'shipping_address2' => $cart->shipping_address2,
                        'shipping_city' => $cart->shipping_city,
                        'shipping_state' => $cart->shipping_state,
                        'shipping_zip' => $cart->shipping_zip,
                        'shipping_country' => $cart->shipping_country,
                        'shipping_email' => $cart->shipping_email
                    ];
                    $gatewayTransactionNo = null;
                    $gatewayText = null;
                    //payment 
                    if ($nonce) {
                        if (config('app.gatewayType') == 'authorizenet') {
                            // Authorize Payment
                            $payByAuthorize =
                                $this->payByAuthorize(
                                    $payedAmount,
                                    $nonce,
                                    $transaction,
                                    $userId,
                                    $cart->billing_email,
                                    $billingInfo
                                );

                            if ($payByAuthorize['status'] === true) {
                                Log::info('Authorize Payment Success');
                                Log::info($payedAmount . ' was paid' . $newValue . 'for ' . $userId . '( ' . $cart->billing_email . ' )' . ' on ' . now());
                                $gatewayTransactionNo = $payByAuthorize['transaction_id'];
                                $gatewayText = $payByAuthorize['description'];
                            } else {
                                return response()->json([
                                    'status' => false,
                                    'message' => $payByAuthorize['message'],
                                ]);
                            }
                        } else {
                            // NMI Payment
                            $saleData = $this->doSale($payedAmount, $nonce, $billingInfo, $shippingInfo);
                            $paymentResult = $this->_doRequest($saleData);
                            if ($paymentResult['status'] === false || $paymentResult['responsetext'] !== 'Approved') {
                                return response()->json([
                                    'status' => false,
                                    'message' => $paymentResult['responsetext'],
                                ]);
                            }

                            $gatewayTransactionNo = $paymentResult['transactionid'];
                            $gatewayText = $paymentResult['responsetext'];
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Token is Missing',
                        ]);
                    }
                } else {
                    $payedAmount = 0;
                }
                if ($payedAmount > 0) {
                    $paymentData = [
                        'transaction_id' => $transaction,
                        'business_id' => $business_id,
                        'amount' => $payedAmount,
                        'method' => $card ? "custom_pay_1" : 'cash',
                        'card_type' => 'credit',
                        'payment_ref_no' => $newValue,
                        'transaction_no' => $gatewayTransactionNo,
                        'paid_on' => now(),
                        'payment_for' => $userId,
                        'gateway' => 'NMI',
                        'note' => $final_total ." was final total" . ' Gateway Said:' . $gatewayText ?? '',
                        'created_by' => 1, //admin
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    DB::table('transaction_payments')->insert($paymentData);
                }
            }

            DB::table('transactions')->where('id', $transaction)->update([
                'final_total' => $final_total,
                'shipping_charges' => $shipping_charges,
                'total_before_tax' => $total_before_tax,
                'discount_type' => $cartDiscountAmount > 0 ? 'fixed' : null,
                'discount_amount' => $cartDiscountAmount,
                'rp_redeemed' => $rp_redeemed,
                'rp_redeemed_amount' => $rp_redeemed_amount,
                'gift_card_amount' => $gift_card_amount,
                'additional_notes' => 'B2B Web Order <br> Applied Discount: '. implode('<br>', $appliedDiscountBucket),
                'is_gift' => $isGift,
                'hide_prices_for_recipient' => $hidePricesForRecipient,
                'updated_at' => now(),
            ]);
            // Fetch shipping rates for response when using shipping (not PICKUP)
            $shippingRates = [];
            $selectedShippingRate = null;
            if ($shippingType !== 'PICKUP') {
                // Use rate from request if provided; otherwise build from FLATRATE/shipping_charges
                if ($request->has('shipping_rate') && is_array($request->input('shipping_rate'))) {
                    $selectedShippingRate = $request->input('shipping_rate');
                } elseif ($shippingType === 'FLATRATE') {
                    $selectedShippingRate = [
                        'service_code' => 'FLATRATE',
                        'shipping_amount' => ['amount' => (float) $shipping_charges, 'currency' => 'USD'],
                    ];
                }
                try {
                    $cartController = app(\App\Http\Controllers\ECOM\CartController::class);
                    $ratesResponse = $cartController->getShippingRates($request);
                    $ratesData = $ratesResponse->getData(true);
                    if (!empty($ratesData['status']) && !empty($ratesData['rates'])) {
                        $shippingRates = $ratesData['rates'];
                        // If no selected rate yet, try to match by amount
                        if ($selectedShippingRate === null && !empty($shippingRates)) {
                            foreach ($shippingRates as $r) {
                                $amt = $r['shipping_amount']['amount'] ?? $r['shipment_cost']['amount'] ?? $r['amount'] ?? null;
                                if ($amt !== null && abs((float) $amt - (float) $shipping_charges) < 0.01) {
                                    $selectedShippingRate = $r;
                                    break;
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // Don't fail order if rate fetch fails; rates remain empty
                }
                if ($selectedShippingRate === null) {
                    $selectedShippingRate = [
                        'service_code' => $shippingType,
                        'shipping_amount' => ['amount' => (float) $shipping_charges, 'currency' => 'USD'],
                    ];
                }
            }

            $cart->delete();
            CartItem::where('user_id', $userId)->delete();
            DB::commit();

            // Deduct redeemed reward points from customer balance
            if ($rp_redeemed > 0) {
                app(TransactionUtil::class)->updateCustomerRewardPoints($userId, 0, 0, $rp_redeemed, 0);
            }

            // Send notification to customer when order is placed
            try {
                $orderTransaction = Transaction::find($transaction);
                if ($orderTransaction && $contact) {
                    Log::info('Attempting to send order placed notification', [
                        'transaction_id' => $transaction,
                        'contact_id' => $userId,
                        'business_id' => $business_id
                    ]);
                    
                    // Dispatch notification job using new_sale notification type
                    SendNotificationJob::dispatch(
                        false, // is_custom = false (using regular notification)
                        $business_id,
                        'new_sale', // Using new_sale notification type for order confirmation
                        null, // user
                        $contact,
                        $orderTransaction // transaction
                    );
                    
                    // Send gift card code(s) to customer via email after invoice
                    if (!empty($createdGiftCards) && ($contact->email ?? null)) {
                        foreach ($createdGiftCards as $giftCard) {
                            try {
                                $customData = (object) [
                                    'name' => $contact->name ?? $contact->supplier_business_name ?? 'Customer',
                                    'brand_id' => $contact->brand_id ?? null,
                                    'gift_card_code' => $giftCard->code,
                                    'gift_card_amount' => (float) $giftCard->initial_amount,
                                    'gift_card_balance' => (float) $giftCard->balance,
                                    'gift_card_currency' => $giftCard->currency ?? 'USD',
                                    'gift_card_expires_at' => $giftCard->expires_at ? $giftCard->expires_at->toDateString() : null,
                                    'gift_card_message' => $giftCard->message,
                                ];
                                SendNotificationJob::dispatch(
                                    true, // is_custom
                                    $business_id,
                                    'gift_card_code',
                                    $customData,
                                    $contact
                                );
                                Log::info('Gift card code notification queued', [
                                    'gift_card_id' => $giftCard->id,
                                    'contact_id' => $userId,
                                ]);
                            } catch (\Throwable $e) {
                                Log::error('Failed to queue gift card code notification', [
                                    'gift_card_id' => $giftCard->id ?? null,
                                    'contact_id' => $userId,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }
                    }
                    
                    // Send Firebase push notification
                    $notificationUtil = new NotificationUtil();
                    $notificationUtil->sendPushNotification(
                        'Order Placed Successfully',
                        'Thank you for placing your order! Your order #' . $newValue . ' has been received and is being processed.',
                        $userId,
                        [
                            'order_id' => $transaction,
                            'invoice_no' => $newValue,
                            'final_total' => $final_total,
                            'type' => 'order_placed'
                        ],
                        'non_urgent'
                    );
                    
                    Log::info('Order placed notification queued successfully', [
                        'transaction_id' => $transaction,
                        'contact_id' => $userId,
                        'business_id' => $business_id
                    ]);
                } else {
                    Log::warning('Transaction or contact not found for order placed notification', [
                        'transaction_id' => $transaction,
                        'contact_id' => $userId
                    ]);
                }
            } catch (\Exception $notificationError) {
                Log::error('Failed to queue order placed notification', [
                    'transaction_id' => $transaction,
                    'contact_id' => $userId,
                    'error' => $notificationError->getMessage(),
                    'trace' => $notificationError->getTraceAsString()
                ]);
                // Don't fail the whole operation if notification fails
            }
            
            //send webhook to woo commerce connector from erp side 
            // --- Pouse for staging ---
            // WooCommerceWebhookSaleOrder::dispatch($transaction);
            $response = [
                'status' => true,
                'message' => 'Order created successfully.',
                'SO' => $newValue,
                'id' => $transaction,
                'created_at' => now(),
                'final_total' => $final_total,
                'shipping' => [
                    'type' => $shippingType,
                    'charges' => $shipping_charges,
                    'rate' => $selectedShippingRate,
                ],
                'reward_points' => [
                    'redeemed_points' => $rp_redeemed,
                    'redeemed_amount' => $rp_redeemed_amount,
                ],
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ]);
        }
    }

    public function paymentSellRequest(Request $request)
    {
        $nounce = $request->input('nounce');
        $token = $request->input('token');
        $userId = $request->input('customer_id');
        // $business_id = 1;
        // $authData = $this->authCheck($request);
        // if (!$authData['status']) {
        //     return response()->json(['status' => false, 'message' => 'Unknown user']);
        // }
        $paymentBuffer = PaymentBuffer::where('token', $token)->where('customer_id', $userId)->first();
        if (!$paymentBuffer) {
            return response()->json(['status' => false, 'message' => 'Invalid payment request']);
        }
        $amount = $paymentBuffer->amount;
        $payload = $paymentBuffer->payload;

        $contact = Contact::find($userId);
        $billingInfo = [
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name ?? '',
            'company' => $contact->company ?? '',
            'address1' => $contact->address1,
            'address2' => $contact->address2 ?? '',
            'city' => $contact->city,
            'state' => $contact->state,
            'zip' => $contact->zip,
            'country' => $contact->country,
            'phone' => $contact->phone ?? '',
            'email' => $contact->email ?? ''
        ];
        $shippingInfo = [
            'shipping_first_name' => $contact->shipping_first_name ?? '',
            'shipping_last_name' => $contact->shipping_last_name ?? '',
            'shipping_company' => $contact->shipping_company ?? '',
            'shipping_address1' => $contact->shipping_address1 ?? '',
            'shipping_address2' => $contact->shipping_address2 ?? '',
            'shipping_city' => $contact->shipping_city ?? '',
            'shipping_state' => $contact->shipping_state ?? '',
            'shipping_zip' => $contact->shipping_zip ?? '',
            'shipping_country' => $contact->shipping_country ?? '',
            'shipping_email' => $contact->shipping_email ?? ''
        ];
        $makePayment = $this->doSale($amount, $nounce, $billingInfo, $shippingInfo);
        $paymentResult = $this->_doRequest($makePayment);
        // if ($paymentResult['status'] === false || $paymentResult['responsetext'] !== 'Approved') {
        //     return response()->json([
        //         'status' => false,
        //         'message' => $paymentResult['responsetext'],
        //     ]);
        // }
        $request = new Request();
        $request->merge($payload);
        $request->merge($paymentBuffer->toArray());
        // dd($request);
        $initiatePayment = (new TransactionPaymentController(new TransactionUtil(), new ModuleUtil()))->postPayContactDue($request, true);
        $paymentBuffer->delete();

        return response()->json([
            'status' => true,
            'message' => 'Payment successful',
            'data' => $initiatePayment
        ]);
    }

    public function getPayment(Request $request)
    {
        $token = $request->query('token');
        $customerId = $request->query('id');
        $userId = $customerId;
        $business_id = 1;
        $location_id = 1;
        $payable = PaymentBuffer::where('customer_id', $userId)->where('business_id', $business_id)->sum('amount');
        return view('payment.index', compact('token', 'customerId', 'payable'));
    }
    public function paymentOfSalesOrder(Request $request)
    {
        try {
            // Get authenticated user
            $user = Auth::guard('api')->user();
            if (!$user) {
                Log::error('User not authenticated');
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'order_ids' => 'required|array',
                'order_ids.*' => 'required|integer',
                'nounce' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $nonce = $request->input('nounce');
            $arrayOfOrderIds = $request->input('order_ids');
            $business_id = 1;
            
            // Get customer
            $customer = Contact::find($user->id);
            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            // Validate orders belong to customer and get transactions
            $unique_order_ids = array_unique($arrayOfOrderIds);
            $transactions = Transaction::whereIn('id', $unique_order_ids)
                ->where('contact_id', $customer->id)
                ->where('business_id', $business_id)
                ->whereIn('type', ['sell', 'sell_return', 'sales_order'])
                ->where('status', '!=', 'void')
                ->get();

            if ($transactions->count() !== count($unique_order_ids)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Some orders do not belong to this customer or are invalid'
                ], 400);
            }

            // Calculate due amounts for each order and total payable amount
            $transactionUtil = new TransactionUtil();
            $total_payment_needed = 0;
            $transaction_amounts = [];
            $skipped_orders = [];

            foreach ($transactions as $transaction) {
                $total_paid = $transactionUtil->getTotalPaid($transaction->id) ?? 0;
                $due_amount = $transaction->final_total - $total_paid;

                if ($due_amount <= 0) {
                    $skipped_orders[] = [
                        'order_id' => $transaction->id,
                        'invoice_no' => $transaction->invoice_no,
                        'message' => 'Order is already fully paid',
                        'due_amount' => $due_amount
                    ];
                    continue;
                }

                $transaction_amounts[$transaction->id] = $due_amount;
                $total_payment_needed += $due_amount;
            }

            if (empty($transaction_amounts)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No payment is due for the selected orders',
                    'skipped_orders' => $skipped_orders
                ], 400);
            }

            // Get billing and shipping addresses
            $billingAddress = CustomerAddress::where('contact_id', $customer->id)
                ->where('address_type', 'billing')
                ->orderBy('created_at', 'desc')
                ->first();

            $shippingAddress = CustomerAddress::where('contact_id', $customer->id)
                ->where('address_type', 'shipping')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$billingAddress) {
                $billingAddress = (object)[
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'company' => $customer->supplier_business_name,
                    'address_line_1' => $customer->address_line_1,
                    'address_line_2' => $customer->address_line_2,
                    'city' => $customer->city,
                    'state' => $customer->state,
                    'zip_code' => $customer->zip_code,
                    'country' => $customer->country ?? 'US',
                ];
            }

            if (!$shippingAddress) {
                $shippingAddress = (object)[
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'company' => $customer->supplier_business_name,
                    'address_line_1' => $customer->shipping_address1 ?? $customer->address_line_1,
                    'address_line_2' => $customer->shipping_address2 ?? $customer->address_line_2,
                    'city' => $customer->shipping_city ?? $customer->city,
                    'state' => $customer->shipping_state ?? $customer->state,
                    'zip_code' => $customer->shipping_zip ?? $customer->zip_code,
                    'country' => $customer->shipping_country ?? $customer->country ?? 'US',
                ];
            }

            // Prepare billing and shipping info for payment gateway
            $billingInfo = [
                'first_name' => $billingAddress->first_name ?? $customer->first_name,
                'last_name' => $billingAddress->last_name ?? $customer->last_name,
                'company' => $billingAddress->company ?? $customer->supplier_business_name,
                'address1' => $billingAddress->address_line_1 ?? $customer->address_line_1,
                'address2' => $billingAddress->address_line_2 ?? $customer->address_line_2,
                'city' => $billingAddress->city ?? $customer->city,
                'state' => $billingAddress->state ?? $customer->state,
                'zip' => $billingAddress->zip_code ?? $customer->zip_code,
                'country' => $billingAddress->country ?? $customer->country ?? 'US',
                'email' => $customer->email,
            ];

            $shippingInfo = [
                'shipping_firstname' => $shippingAddress->first_name ?? $customer->first_name,
                'shipping_lastname' => $shippingAddress->last_name ?? $customer->last_name,
                'shipping_company' => $shippingAddress->company ?? $customer->supplier_business_name,
                'shipping_address1' => $shippingAddress->address_line_1 ?? $customer->shipping_address1 ?? $customer->address_line_1,
                'shipping_address2' => $shippingAddress->address_line_2 ?? $customer->shipping_address2 ?? $customer->address_line_2,
                'shipping_city' => $shippingAddress->city ?? $customer->shipping_city ?? $customer->city,
                'shipping_state' => $shippingAddress->state ?? $customer->shipping_state ?? $customer->state,
                'shipping_zip' => $shippingAddress->zip_code ?? $customer->shipping_zip ?? $customer->zip_code,
                'shipping_country' => $shippingAddress->country ?? $customer->shipping_country ?? $customer->country ?? 'US',
            ];
            $gatewayTransactionNo = null;
            $gatewayText = null;
            // // based on payment gateway, make payment 
            // Process payment through gateway if nonce is provided
            if ($nonce) {
                if (config('app.gatewayType') == 'authorizenet') {
                    // Authorize Payment
                    $payByAuthorize = $this->payByAuthorize(
                        $total_payment_needed,
                        $nonce,
                        implode(',', $unique_order_ids), // transaction reference
                        $customer->id,
                        $customer->email,
                        $billingInfo
                    );

                    if ($payByAuthorize['status'] === true) {
                        Log::info('Authorize Payment Success');
                        Log::info($total_payment_needed . ' was paid for orders: ' . implode(',', $unique_order_ids) . ' by customer ' . $customer->id . ' (' . $customer->email . ') on ' . now());
                        $gatewayTransactionNo = $payByAuthorize['transaction_id'];
                        $gatewayText = $payByAuthorize['description'];
                    } else {
                        // BUG FIX #3: Rollback transaction on payment failure
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => $payByAuthorize['message'] ?? 'Payment gateway error',
                        ], 400);
                    }
                } else {
                    // NMI Payment
                    $saleData = $this->doSale($total_payment_needed, $nonce, $billingInfo, $shippingInfo);
                    $paymentResult = $this->_doRequest($saleData);
                    
                    if ($paymentResult['status'] === false || $paymentResult['responsetext'] !== 'Approved') {
                        // BUG FIX #3: Rollback transaction on payment failure
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => $paymentResult['responsetext'] ?? 'Payment gateway error',
                        ], 400);
                    }

                    $gatewayTransactionNo = $paymentResult['transactionid'];
                    $gatewayText = $paymentResult['responsetext'];
                }
            } else {
                //dd($nonce, $total_payment_needed, $billingInfo, $shippingInfo);
                return response()->json([
                    'status' => false,
                    'message' => 'Payment token (nounce) is required',
                ], 422);
            }

            $transactions_map = $transactions->keyBy('id');

            //payment for each order 
            foreach ($transaction_amounts as $transaction_id => $amount) {
                DB::beginTransaction();
                
                try {
                    $transaction = $transactions_map[$transaction_id];
                    
                    if ($transaction->payment_status != 'paid') {
                        
                        $paymentData = [
                            'transaction_id' => $transaction,
                            'business_id' => $business_id,
                            'amount' => $amount,
                            'method' => 'wallet',
                            'card_type' => 'wallet',
                            'payment_ref_no' => $transaction->invoice_no,
                            'transaction_no' => $gatewayTransactionNo ?? null,
                            'paid_on' => now(),
                            'payment_for' => $customer->id,
                            'gateway' => 'NMI',
                            'note' => $amount  ." was paid for order " . $transaction->invoice_no,
                            'created_by' => 1, //admin
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $tp = TransactionPayment::create($paymentData);

                
                        // Update payment status 
                        $payment_status = $transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                        $transaction->payment_status = $payment_status;
                        $transaction->save();

                        $payment_details[] = [
                            'transaction_id' => $transaction->id,
                            'invoice_no' => $transaction->invoice_no,
                            'amount' => $amount,
                            'payment_id' => $tp->id,
                            'payment_ref_no' => $tp->payment_ref_no,
                        ];
     
                        Log::info('Payment added successfully for order ' . $transaction->invoice_no);
                        DB::commit();
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
                    
                    return response()->json([
                        'status' => false,
                        'message' => 'Payment failed for order ' . $transaction->invoice_no . ': ' . $e->getMessage(),
                    ], 500);
                }
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Payment successful',
                'payment_transaction_id' => $gatewayTransactionNo ?? '',
            ]);

        } catch (\Exception $e) {
            Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Payment failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
